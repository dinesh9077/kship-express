<?php
	
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use App\Models\User;
	use App\Models\UserWallet;
	use App\Models\ShippingCompany;
	use App\Models\Notification;
	use App\Models\AppBanner;
	use App\Models\Order;  
	use Illuminate\Support\Facades\Hash;
	use Carbon\Carbon; 
	use App\Traits\ApiResponse;  
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Support\Facades\Http; 
	use Illuminate\Support\Str;
	use App\Services\ShipMozo;
	use App\Services\MasterService;
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

			$recentOrders = Order::with([
				'customer:id,first_name,last_name,mobile,email',
				'customerAddress:id,address',
				'warehouse:id,warehouse_name,contact_name,contact_number,company_name',
				'user:id,name,company_name,email,mobile',
				'orderItems:id,order_id,product_category,product_name,sku_number,hsn_number,amount,quantity,dimensions',
			])
			->select('id', 'order_prefix', 'user_id', 'customer_id', 'customer_address_id', 'shipping_company_id', 'warehouse_id', 'status_courier', 'order_type', 'created_at', 'weight_order', 'cod_amount', 'awb_number', 'invoice_amount', 'length', 'width', 'height', 'weight', 'reason_cancel', 'courier_name')
			->when($user->role === "user", fn($q) => $q->where('orders.user_id', $user->id))
			->where('status_courier',  'New')
			->latest()
			->limit(10)
			->get();

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
				'recentOrders'       		=> $recentOrders,
			];
			
			return $this->successResponse($data, 'detail fetched successfully.');
		}
	public function notification()
	{
		$user = Auth::user();
		$query = Notification::where('read_at', 0)->where('role', $user->role);

		if ($user->role == "user") {
			$query->where('user_id', $user->id);
		}

		// clone query so we don’t run it twice
		$notifications = (clone $query)->latest()->get();
		$count = $query->count(); // runs SELECT COUNT(*)

		$data = [
			'notifications' => $notifications,
			'count' => $count
		];

		return $this->successResponse($data, 'detail fetched successfully.');
	}


	public function notificationClearAll()
		{
			DB::beginTransaction();
			try {
				$user = Auth::user();
				$query = Notification::where('role', $user->role);

				if ($user->role !== "admin") {
					$query->where('user_id', $user->id);
				}

				$query->update(['read_at' => 1]);
				DB::commit();
				return $this->successResponse([], 'Notifications have been successfully cleared.'); 
			} catch (\Exception $e) {
				DB::rollBack();
			return $this->errorResponse( 'Something went wrong.'); 
			}
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

				$user = Auth::user();
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
				$user->update($data); 
				$user->profile_image = $user->profile_image
				? url('storage/profile/' . $user->profile_image)
				: asset('assets/images/profile-logo.png');
 
				DB::commit(); // Commit transaction
				return $this->successResponse($user, 'Your profile has been updated successfully.');	 

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

		public function rechargeWalletStore(Request $request, MasterService $masterService)
		{
			try {
				$userId = $request->user_id;
				$amount = $request->amount;
				if($amount < 10)
				{
					return $this->errorResponse('Enter a valid amount (min ₹200)');
				}
				$response = $masterService->rechargeOrderCreate($amount);
				if (!($response['success'] ?? false)) {
					$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response']['error'] ?? 'An error occurred.');
					return $this->errorResponse($errorMsg);
				}

				if ((isset($response['response']['message']) && $response['response']['message'] === "Failed")) {
					return $this->errorResponse( $response['response']['err'] ?? 'An error occurred.');	 
				}

				if (empty($response['response']['data'])) {
					return $this->errorResponse('something went wrong.'); 
				}

				$responseData = $response['response']['data'];

				// Prepare data for database
				$data = $request->except('_token', 'payment_receipt');
				$data['user_id'] = $userId;
				$data['status'] = 1;
				$data['amount'] = $amount;
				$data['order_id'] = $responseData['order_id'] ?? null;
				$data['payable_response'] = $responseData ?? null;
				$data['created_at'] = now();
				$data['updated_at'] = now();

				$wallet = UserWallet::create($data);

				// Return Razorpay order details to the frontend
				return $this->successResponse($responseData, 'success');
			 
			} catch (\Exception $e) {
				return $this->errorResponse($e->getMessage()); 
			}
		}

		public function rechargeWalletRazorpay(Request $request)
		{
			try {
				$userWallet = UserWallet::with('user')->where('order_id', $request->order_id)->firstOrFail();
				$user = $userWallet->user;

				DB::transaction(function () use ($user, $request, $userWallet) {
					// Update wallet amount
					$user->increment('wallet_amount', $userWallet->amount);

					// Record billing
					Billing::create([
						'user_id' => $user->id,
						'billing_type' => 'Recharge Wallet',
						'billing_type_id' => $userWallet->id,
						'transaction_type' => 'credit',
						'amount' => $userWallet->amount,
						'note' => 'Recharge Wallet amount online.',
					]);

					// Update UserWallet record if needed
					$userWallet->update([
						'transaction_status' => 'Paid',
						'txn_number' => $request->txn_id ?? null,
					]);
				}); 

				return $this->successResponse([], 'The transaction has been charged and your wallet updated successfully.');

			} catch (\Exception $e) {
				return $this->errorResponse($e->getMessage());
			}
		}

		public function banners()
		{
			$banners = AppBanner::where('status', 1)->get();
			foreach($banners as $banner)
			{
				$banner->banner_image = $banner->banner_image
				? url('storage/' . $banner->banner_image)
				: $banner->banner_image;
			}
			return $this->successResponse($banners, 'detail fetched successfully.');
		}
	}
