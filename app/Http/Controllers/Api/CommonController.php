<?php
	
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use App\Models\User;
	use App\Models\UserWallet;
	use App\Models\ShippingCompany;
	use App\Models\Order;  
	use Illuminate\Support\Facades\Hash;
	use Carbon\Carbon; 
	use App\Traits\ApiResponse;  
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Support\Facades\Http; 
	use Illuminate\Support\Str;
	use App\Services\ShipMozo;
	use File;
	
	class CommonController extends Controller
	{
		use ApiResponse; 
		 
		protected $shipMozo; 
		public function __construct()
		{
			$this->middleware('auth');
			$this->shipMozo = new ShipMozo();   
		} 
		
		public function dashboard(Request $request)
		{ 
			$user = $request->user();  
			 
			$isAdmin = $user->role === 'admin';

			// Common query filter for Orders
			$orderFilter = function ($query) use ($isAdmin, $user) {
				if (!$isAdmin) {
					$query->where('user_id', $user->id);
				}
			};

			$today = Carbon::today();
			$yesterday = Carbon::yesterday();

			// Base Order query with filter
			$baseOrderQuery = Order::query()->when(!$isAdmin, $orderFilter);

			// ✅ Get all counts in one query with conditional sums
			$orderStats = $baseOrderQuery->selectRaw("
				SUM(CASE WHEN DATE(created_at) = ? AND weight_order = 1 THEN 1 ELSE 0 END) as todaysLightWeightOrder,
				SUM(CASE WHEN weight_order = 1 THEN 1 ELSE 0 END) as totalLightWeightShipment,
				SUM(CASE WHEN status_courier = 'cancelled' AND weight_order = 1 THEN 1 ELSE 0 END) as cancelledOrder,
				SUM(CASE WHEN status_courier = 'manifested' AND weight_order = 1 THEN 1 ELSE 0 END) as manifested,
				SUM(CASE WHEN status_courier = 'delivered' AND weight_order = 1 THEN 1 ELSE 0 END) as delivered,
				SUM(CASE WHEN status_courier = 'out for delivery' AND weight_order = 1 THEN 1 ELSE 0 END) as outForDelivery,
				SUM(CASE WHEN status_courier LIKE '%rto%' AND weight_order = 1 THEN 1 ELSE 0 END) as rto,
				SUM(CASE WHEN status_courier NOT IN ('new','manifested','delivered','cancelled') AND weight_order = 1 THEN 1 ELSE 0 END) as inTransit,
				SUM(invoice_amount) as totalInvoiceAmount,
				SUM(CASE WHEN DATE(created_at) = ? THEN invoice_amount ELSE 0 END) as tadaysInvoiceAmount,
				SUM(CASE WHEN order_type = 'cod' THEN cod_amount ELSE 0 END) as totalCodAmount,
				SUM(CASE WHEN order_type = 'cod' AND DATE(created_at) = ? THEN cod_amount ELSE 0 END) as tadaysCodAmount
			", [$today, $yesterday, $yesterday])->first();

			// ✅ Today recharge (filtered by user if not admin)
			$todayRecharge = UserWallet::when(!$isAdmin, function ($q) use ($user) {
					$q->where('user_id', $user->id);
				})
				->whereDate('created_at', $today)
				->whereRaw('LOWER(transaction_status) = ?', ['success'])
				->sum('amount');

			// ✅ Overall Wallet balance
			$overallWalletAmount = User::whereNotNull('wallet_amount')
				->whereNull('deleted_at')
				->when(!$isAdmin, fn($q) => $q->where('id', $user->id))
				->sum('wallet_amount');

			$data = [ 
				'todayRecharge'         	=> $todayRecharge,
				'overallWalletAmount'   	=> $overallWalletAmount,
				'todaysLightWeightOrder'	=> $orderStats->todaysLightWeightOrder,
				'totalLightWeightShipment'  => $orderStats->totalLightWeightShipment,
				'cancelledOrder'            => $orderStats->cancelledOrder,
				'manifested'           		=> $orderStats->manifested,
				'delivered'             	=> $orderStats->delivered,
				'outForDelivery'        	=> $orderStats->outForDelivery,
				'rto'                   	=> $orderStats->rto,
				'inTransit'             	=> $orderStats->inTransit,
				'totalInvoiceAmount'    	=> $orderStats->totalInvoiceAmount,
				'tadaysInvoiceAmount'   	=> $orderStats->tadaysInvoiceAmount,
				'totalCodAmount'        	=> $orderStats->totalCodAmount,
				'tadaysCodAmount'       	=> $orderStats->tadaysCodAmount,
			];
			
			return $this->successResponse($data, 'detail fetched successfully.');
		} 
		
		public function rateCalculator(Request $request)
		{
			$user = Auth::user();
			$role = $user->role;
			$charge = $user->charge;
			$charge_type = $user->charge_type;
			
			$shippingCompanies = ShippingCompany::whereStatus(1)->get();
			$couriers = [];
			 
			foreach($shippingCompanies as $shippingCompany)
			{ 
				if ($shippingCompany->id == 1) { 
					
					$pincodeServiceData = $this->shipMozo->pincodeService($request, $shippingCompany);
					 
					if (!($pincodeServiceData['success'] ?? false) || 
					(isset($pincodeServiceData['response']['result']) && $pincodeServiceData['response']['result'] == 0)) {
						continue;
					}
					
					$response = $this->shipMozo->rateCaculator($request->all(), $shippingCompany); 
					if (!($response['success'] ?? false) || 
						(isset($response['response']['result']) && $response['response']['result'] == 0)) {
						continue;
					}
					
					$responseDetails = $response['response']['data'] ?? []; 
					if (!$responseDetails) {
						continue;
					}
					
					foreach($responseDetails as  $responseData){ 
						$totalCharges = $responseData['total_charges'];  
						$beforeTax = $responseData['before_tax_total_charges'];  
						$gst = $responseData['gst'];  

						$couriers[] = [ 
							'shipping_charge' => $beforeTax, 
							'tax' => $gst, 
							'shipping_company_id' => $shippingCompany->id,
							'courier_id' 	=> $responseData['id'],
							'shipping_company_name' => $responseData['name'],
							'shipping_company_logo' => $responseData['image'], 
							'courier_name' => $responseData['name'], 
							'total_charges' => $totalCharges,
							'estimated_delivery' => $responseData['estimated_delivery'] ?? 'N/A', 
							'chargeable_weight' => $responseData['minimum_chargeable_weight'] ?? 0,
							'applicable_weight' => $request->weight ?? 0,
							'percentage_amount' => 0,
							'responseData' => $responseData
						];
					}
				} 
			} 
			
			return $this->successResponse($couriers, 'detail fetched successfully.');
		}

		public function updateProfile(Request $request)
		{
			try {
				DB::beginTransaction(); // Start transaction

				$user_id = Auth::id();
				$data = $request->except('_token', 'profile_image');

				if ($request->hasFile('profile_image')) {
					$photo_image = $request->file('profile_image');
					$getAvatar = time() . rand(111111, 999999) . '.' . $photo_image->getClientOriginalExtension();
					$path_avatar = storage_path("app/public/profile");

					// Ensure directory exists
					if (!File::exists($path_avatar)) {
						File::makeDirectory($path_avatar, 0777, true, true);
					}

					// Move file
					$photo_image->move($path_avatar, $getAvatar);
					$data['profile_image'] = $getAvatar;
				}

				// Update user profile
				User::whereId($user_id)->update($data);

				DB::commit(); // Commit transaction
				return $this->successResponse($data, 'Your profile has been updated successfully.');	 

			} catch (\Exception $e) {
				DB::rollBack();
				return $this->errorResponse('An error occurred while updating your profile. Please try again.'); 
			}
		}

		public function updatePassword(Request $request)
		{
			// Validate request input 
			DB::beginTransaction();
			try {
				// Get the authenticated user
				$user = Auth::user();

				// Check if old password is correct
				if (!Hash::check($request->old_password, $user->password)) {
					return $this->errorResponse('The old password is incorrect.'); 
				}

				// Update user password
				$user->update([
					'xpass' => $request->new_password,
					'password' => Hash::make($request->new_password),
				]);

				DB::commit();
				return $this->successResponse($user,'Your password has been changed successfully.');
		  
			} catch (\Exception $e) {
				DB::rollBack();
				return $this->errorResponse('An error occurred while updating your password. Please try again.'); 
			}
		}
	}
