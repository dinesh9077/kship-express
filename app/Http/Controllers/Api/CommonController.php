<?php
	
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use App\Models\User;
	use App\Models\UserWallet;
	use App\Models\Order;  
	use Illuminate\Support\Facades\Hash;
	use Carbon\Carbon; 
	use App\Traits\ApiResponse;  
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Support\Facades\Http; 
	use Illuminate\Support\Str;
	
	class CommonController extends Controller
	{
		use ApiResponse; 
		
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
	}
