<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Notification;
	use App\Models\Order;
	use App\Models\UserWallet;	
	use App\Models\UserKyc;	
	use App\Models\User;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB; 
	use Illuminate\Support\Facades\Http;
	use Illuminate\Support\Facades\Mail;
	use App\Mail\LowBalanceAlert;
	use Carbon\Carbon;
 
	class HomeController extends Controller
	{
		/**
			* Create a new controller instance.
			*
			* @return void
		*/
		public function __construct()
		{
			$this->middleware('auth');
		}
		
		/**
			* Show the application dashboard.
			*
			* @return \Illuminate\Contracts\Support\Renderable
		*/
		public function index()
		{
			$user = Auth::user(); 
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
			", [$today, $yesterday, $yesterday])
			->first();

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

			// Build the query for orders
			$recentOrders = Order::with([
				'customer:id,first_name,last_name,mobile,email',
				'customerAddress:id,address',
				'warehouse:id,warehouse_name,contact_name,contact_number,company_name',
				'user:id,name,company_name,email,mobile',
				'orderItems:id,order_id,product_category,product_name,sku_number,hsn_number,amount,quantity,dimensions',
			])
			->select('id', 'order_prefix', 'user_id', 'customer_id', 'customer_address_id', 'shipping_company_id', 'warehouse_id', 'status_courier', 'order_type', 'created_at', 'weight_order', 'cod_amount', 'awb_number', 'invoice_amount', 'length', 'width', 'height', 'weight', 'reason_cancel', 'courier_name')
			->when($user->role === "user", fn($q) => $q->where('orders.user_id', $user->id))
			->where('status_courier', 'New')
			->latest()
			->limit(10)
			->get();

			$courierWiseCount = Order::whereNotNull('shipping_company_id')
			->whereNotNull('courier_name')
			->select(
				'courier_name',
				DB::raw('COUNT(*) as total_orders'),
				DB::raw('SUM(CASE WHEN weight_order = 1 THEN 1 ELSE 0 END) as b2c_count'),
				DB::raw('SUM(CASE WHEN weight_order = 2 THEN 1 ELSE 0 END) as b2b_count')
			)
			->groupBy('courier_name')
			->orderByDesc('total_orders')
			->get();
			 
			$banners = DB::table('app_banners')->where('status', 1)->get();
			return view('home', [
				'user'                  => $user,
				'todayRecharge'         => $todayRecharge,
				'overallWalletAmount'   => $overallWalletAmount,
				'todaysLightWeightOrder'=> $orderStats->todaysLightWeightOrder,
				'totalLightWeightShipment'=> $orderStats->totalLightWeightShipment,
				'cancelledOrder'        => $orderStats->cancelledOrder,
				'manifested'            => $orderStats->manifested,
				'delivered'             => $orderStats->delivered,
				'outForDelivery'        => $orderStats->outForDelivery,
				'rto'                   => $orderStats->rto,
				'inTransit'             => $orderStats->inTransit,
				'totalInvoiceAmount'    => $orderStats->totalInvoiceAmount,
				'tadaysInvoiceAmount'   => $orderStats->tadaysInvoiceAmount,
				'totalCodAmount'        => $orderStats->totalCodAmount,
				'tadaysCodAmount'       => $orderStats->tadaysCodAmount,
				'recentOrders'      	=> $recentOrders,
				'banners'      			=> $banners,
				'courierWiseCount'      => $courierWiseCount,
			]);
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
			$view = view('notification', compact('notifications'))->render();

			return response()->json(['view' => $view, 'count' => $count]);
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

				return back()->with('success', 'Notifications have been successfully cleared.');
			} catch (\Exception $e) {
				DB::rollBack(); 
				return back()->with('error', 'Something went wrong.');
			}
		} 	  
		
		public function checkAmount()
		{ 
			$user = auth()->user();

			// Ensure only admins can trigger this check
			if ($user->role !== 'admin') {
				return response()->json(['showPopup' => false]);
			}

			// Fetch users with low wallet balance
			$lowBalanceUsers = UserWallet::select('users.id', 'users.name', 'user_wallets.amount')
				->join('users', 'user_wallets.user_id', '=', 'users.id')
				->where('user_wallets.amount', '<=', 100)
				->get();

			if ($lowBalanceUsers->isEmpty()) {
				return response()->json(['showPopup' => false]);
			}

			$userNames = $lowBalanceUsers->pluck('name')->toArray();
			$notifications = [];

			try {
				DB::beginTransaction(); // Start transaction

				foreach ($lowBalanceUsers as $userNotify) {
					$notifications[] = [
						'user_id'    => $userNotify->id,
						'role'       => "admin",
						'type'       => "Low Balance",
						'text'       => "{$userNotify->name} has a low wallet balance.",
						'created_at' => now(),
						'updated_at' => now(),
					];
				}

				// Bulk insert notifications for efficiency
				Notification::insert($notifications);

				DB::commit(); // Commit transaction

				return response()->json(['showPopup' => true, 'users' => $userNames]);
			} catch (\Exception $e) {
				DB::rollBack(); // Rollback transaction in case of failure
				Log::error('Low balance notification failed: ' . $e->getMessage());
				return response()->json(['showPopup' => false]);
			}
		}
		public function lowbalance()
		{
			$user = auth()->user();

			if ($user->role !== 'user') {
				return response()->json(['showPopup' => false]);
			}

			$lowBalanceUser = User::where('id', $user->id)
				->where('wallet_amount', '<=', 100)
				->first(['id', 'name', 'email', 'mobile', 'sms_date']);

			if (!$lowBalanceUser) {
				return response()->json(['showPopup' => false]);
			}

			$today = now();
			$smsDate = $lowBalanceUser->sms_date ? Carbon::parse($lowBalanceUser->sms_date) : null;

			// Message content
			$message = "Alert! Low wallet balance. Top-up needed for continued service. Thank you. Regards, ".config('setting.company_name');

			try {
				DB::beginTransaction(); // Start transaction

				// Send email & SMS only if it hasn’t been sent today
				if (!$smsDate || !$smsDate->isSameDay($today)) {
					// Send Email Notification
					try {
						Mail::to($lowBalanceUser->email)->send(new LowBalanceAlert($lowBalanceUser, $message));
					} catch (\Exception $e) { 
					}

					// Update SMS date to prevent duplicate messages
					$lowBalanceUser->update(['sms_date' => $today->format('Y-m-d')]);
				}
				
				return response()->json([
					'showPopup' => true,
					'msg' => "You have low balance in your wallet!"
				]);

				DB::commit(); // Commit transaction
			} catch (\Exception $e) {
				DB::rollBack();  
				return response()->json(['showPopup' => false, 'msg' => "You have low balance in your wallet!"]);
			} 
		}
 
		public function kycpending()
		{
			$user = auth()->user();

			if ($user->role !== 'user') {
				return response()->json(['showPopup' => false]);
			}

			$userKyc = UserKyc::where('user_id', $user->id)->first();

			if (!$userKyc) {
				return response()->json(['showPopup' => false]);
			}

			$kycChecks = [
				'pancard_status' => 'PanCard',
				'aadhar_status' => 'Aadhar Card',
				'bank_status' => 'Bank',
			];

			foreach ($kycChecks as $field => $kycType) {
				if ($userKyc->$field == 0) {
					return response()->json([
						'showPopup' => true,
						'msg' => "You need to complete your {$kycType} KYC Request!!!"
					]);
				}
			} 
			return response()->json(['showPopup' => false]);
		} 
	}
