<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Order;
	use App\Models\OrderItem; 
	use App\Models\Billing; 
	use App\Models\Invoice;
	use App\Models\User;   
	use App\Exports\PendingStarOrderExport;
	use DB,Auth,File,Helper;
	use Illuminate\Support\Facades\Http;
	use App\Exports\OrdersExport;
	use Maatwebsite\Excel\Facades\Excel;
	use Barryvdh\DomPDF\Facade\Pdf;
	use Illuminate\Support\Facades\Storage; 
	use App\Exports\BillingInvoiceExport;
	use Carbon\Carbon;
	class ReportController extends Controller
	{ 
		public function __construct()
		{
			$this->middleware('auth');
		}
		
		public function index(Request $request)
		{ 
			$user = Auth::user();	
			$users = User::where('role', 'user')->where('kyc_status','!=', 0)->orderBy('name')->get();
			$status = $request->query('status', 'New');
			$couriers = Order::select('courier_name')
			->distinct()
			->orderBy('courier_name');
			if($user->role == 'user'){
				$couriers = $couriers->where('user_id', $user->id);
			}
			$couriers = $couriers->pluck('courier_name', 'courier_name')
			->filter()
			->toArray(); 
			return view('report.order', compact('status', 'users', 'couriers'));
		}
 
    	public function reportOrderAjax(Request $request)
		{
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); // Rows per page

			$columnIndex = $request->post('order')[0]['column'] ?? 0;
			$orderColumn = $request->post('columns')[$columnIndex]['data'] ?? 'id';
			$dir = $request->post('order')[0]['dir'] ?? 'asc';
			$search = $request->input('search') ?? $request->input('search.value') ?? '';
			
			// Fix incorrect assignment (`=` instead of `==`)
			if ($orderColumn == 'action') {
				$orderColumn = 'id';
			}

			$currentDate = now()->toDateString(); 
			$role = Auth::user()->role;
			$userId = Auth::id();

			// Base query with necessary joins
			$query = Order::with(['user', 'warehouse', 'customer', 'customerAddress', 'orderItems']);

			// Apply filters
			if (in_array($role, ["user"])) {
				$query->where('user_id', $userId);
			}
  
			if ($request->filled('user_id')) {
				$query->where('user_id', $request->user_id);
			}
  
			if ($request->filled('courier_name')) {
				$query->where('courier_name', $request->courier_name);
			}

			if ($request->filled('fromdate') && $request->filled('todate')) {
				$query->whereBetween('order_date', [$request->fromdate, $request->todate]);
			} elseif ($request->filled('fromdate')) {
				$query->whereDate('order_date', $request->fromdate);
			} elseif ($request->filled('todate')) {
				$query->whereDate('order_date', $request->todate);
			}else {
				$query->whereDate('order_date', $currentDate);
			}

			// Apply search filter
			if ($search) { 
				$query->where(function ($q) use ($search) {
					$q->where('created_at', 'LIKE', "%{$search}%")
					  ->orWhereHas('customer', function($q) use ($search){
						  $q->where('first_name', 'LIKE', "%{$search}%")
						  ->orwhere('last_name', 'LIKE', "%{$search}%") 
						  ->orwhere('mobile', 'LIKE', "%{$search}%");
						})
					  ->orWhereHas('user', function($q) use ($search){
						  $q->where('name', 'LIKE', "%{$search}%")
						  ->orwhere('email', 'LIKE', "%{$search}%")
						  ->orwhere('company_name', 'LIKE', "%{$search}%")
						  ->orwhere('mobile', 'LIKE', "%{$search}%");
						})
					  ->orWhereHas('warehouse', function($q) use ($search){
						  $q->where('contact_name', 'LIKE', "%{$search}%")
						  ->orwhere('contact_number', 'LIKE', "%{$search}%")
						  ->orwhere('warehouse_name', 'LIKE', "%{$search}%");
						})  
					  ->orWhere('awb_number', 'LIKE', "%{$search}%")
					  ->orWhere('courier_name', 'LIKE', "%{$search}%")
					  ->orWhere('status_courier', 'LIKE', "%{$search}%")
					  ->orWhere('id', 'LIKE', "%{$search}%")
					  ->orWhere('order_prefix', 'LIKE', "%{$search}%");
				});
			}

			// Get total count before applying pagination
			$totalData = $query->count();

			// Apply ordering, pagination, and fetch results
			$orders = $query->orderBy('orders.' . $orderColumn, $dir)
							->offset($start)
							->limit($limit)
							->get();

			// Format data
			$data = [];
			$i = $start + 1;
			foreach ($orders as $order)
			{ 
				$totalOrderTypeAmount =  $order->order_type == "cod" ? $order->cod_amount : $order->invoice_amount;
				$totalOrderTypeLabel =  $order->order_type == "cod" ? 'Cod Amount' : 'Invoice Amount';
				
				$customeraddr = $order->customerAddress;   
				$data[] = [
					'id' => $i++,
					'seller_details' => "<div class='main-cont1-2'><p>" . 
					(isset($order->user) ? "{$order->user->name} ({$order->user->company_name})" : "N/A") . 
					"</p><p>" . ($order->user->email ?? "N/A") . "</p><p>" . ($order->user->mobile ?? "N/A") . "</p></div>",
					
					'order_details' => $this->orderShipmentDetailHtml($order),
					
					'customer_details' => "<div class='main-cont1-2'>
						<p>" . (isset($order->customer) ? "{$order->customer->first_name} {$order->customer->last_name}" : "N/A") . "</p>
						<p>" . ($order->customer->email ?? "N/A") . "</p>
						<p>" . ($order->customer->mobile ?? "N/A") . "</p> 
					</div>",
					
					'customer_address' => "<p style='word-wrap:break-word; white-space:normal;'>" . ($customeraddr->address ?? "N/A") . "</p>",
					'customer_city' => $customeraddr->city,
					'customer_state' => $customeraddr->state,
					'customer_country' => $customeraddr->country,
					'customer_pincode' => $customeraddr->zip_code,
					
		
					'package_details' => $this->orderPackageDetailHtml($order),
					
					'total_amount' => "<div class='main-cont1-2'>
					<p class='" . strtolower($order->order_type) . "'>{$order->order_type}</p> 
					<p>{$totalOrderTypeLabel} : {$totalOrderTypeAmount}</p>
					</div>",
					
					'pickup_address' => "<div class='tooltip'>" . 
					(isset($order->warehouse) ? strtoupper($order->warehouse->warehouse_name) : "N/A") . 
					"<span class='tooltiptext'><b>" . ($order->warehouse->contact_name ?? "N/A") . 
					"</b><br><b>Address</b>: " . ($order->warehouse->address ?? "N/A") . " " . ($order->warehouse->city ?? "") . 
					"<br>" . ($order->warehouse->state ?? "") . "-" . ($order->warehouse->zip_code ?? "") . 
					"<br>" . ($order->warehouse->contact_number ?? "") . "</span></div>",
		
					'status_courier' => "<p class='prepaid'>{$order->status_courier}</p>",
					'created_at' => $order->order_date, 
				];
			}

			// Response
			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $totalData,
				"iTotalDisplayRecords" => count($data),
				"aaData" => $data
			]);
		}
		
		function reportOrderExport(Request $request)
		{
			return Excel::download(new OrdersExport($request), 'orders.xlsx');
		}
		
		function orderShipmentDetailHtml($order)
		{
			// Prepare order items JSON
			$items = $order->orderItems->map(fn($item) => [
				'product_category' => $item->product_category,
				'product_name' => $item->product_name,
				'sku_number' => $item->sku_number,
				'hsn_number' => $item->hsn_number,
				'amount' => $item->amount,
				'quantity' => $item->quantity,
			]);

			$jsonItems = htmlspecialchars($items->toJson(), ENT_QUOTES, 'UTF-8'); // safely encode JSON

			$html = "<div class='main-cont1-1'>";

			$html .= "<div class='checkbox checkbox-purple'>
						Order Prefix/LR No: 
						<a href='" . url("order/details/{$order->id}") . "'>#{$order->order_prefix}</a>
					  </div>";

			$html .= "<div class='checkbox checkbox-purple'>
						Courier: " . ($order->courier_name ?? 'N/A') . "
					  </div>";

			if ($order->awb_number) {
				$html .= "<div class='checkbox checkbox-purple'>
							AWB Number: 
							<a href='" . url("order/details/{$order->id}") . "'>#{$order->awb_number}</a>
						  </div>";
			}

			// View Products button with JSON
			$html .= "<span style='padding-left:0'>
						<a href='javascript:;' class='show-details-btn' data-order='{$jsonItems}'     >
							View Products
						</a>
					  </span>";

			$html .= "</div>";

			return $html;
		}
 
		public static function orderPackageDetailHtml($order)
		{
			$orderItems = $order->orderItems;
			
			if ($order->weight_order == 2) {
				$productDetails = $orderItems->map(function ($item) {
					return "
						<p class='text-white'>
							No Of Box: ".($item->dimensions['no_of_box'] ?? '')."<br>
							Weight Per Box: ".($item->dimensions['weight'] ?? '')."<br>
							Length: ".($item->dimensions['length'] ?? '')."<br>
							Width: ".($item->dimensions['width'] ?? '')."<br>
							Height: ".($item->dimensions['height'] ?? '')."
						</p>
					";
				})->implode('<hr>');
			} else {
				$productDetails = "
					<p class='text-white'>
						Weight In Kg: ".($order->weight ?? '')."<br>
						Length: ".($order->length ?? '')."<br>
						Width: ".($order->width ?? '')."<br>
						Height: ".($order->height ?? '')."
					</p>
				";
			}
			
			$quantitiy = $orderItems->sum('quantity');
			$totalWeightInKg = $order->weight ?? 0;
			
			$output = ''; 
			$output .= "<div class='main-cont1-1'>
				<div class='checkbox checkbox-purple'>
					Quantitiy: {$quantitiy}
				</div> 
				<div class='checkbox checkbox-purple'>
					Weight In Kg: {$totalWeightInKg}
				</div>"; 
				 
				$output .= "<div class='tooltip'>View Package Details<span class='tooltiptext'><b>" . $productDetails. "</span></div>  
			</div>";
			
			return $output;
		}
		 
		public function shippingCharge()
		{
			if (auth()->user()->role === 'user') {
				abort(403, 'Permission denied');
			} 
			$users = User::where('role', 'user')->where('kyc_status','!=', 0)->orderBy('name')->get(); 
			return view('report.shipping-charge', compact('users'));
		}

		public function shippingChargeAjax(Request $request)
		{
			$draw = (int) $request->post('draw', 1);
			$start = (int) $request->post('start', 0);
			$limit = (int) $request->post('length', 25);

			$orderArr = $request->post('order', []);
			$columnsArr = $request->post('columns', []);

			$columnIndex = $orderArr[0]['column'] ?? 0;
			$orderKey = $columnsArr[$columnIndex]['data'] ?? 'id';
			$dir = ($orderArr[0]['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

			// Map DataTables columns -> real DB columns or expressions
			$orderMap = [
				'id' => 'orders.id',
				'order_date' => 'orders.order_date',
				'seller_details' => 'users.name',           // join needed via with() or join()
				'order_details' => 'orders.order_no',
				'shippings' => 'orders.courier_name',
				'shipping_charges' => DB::raw('(COALESCE(orders.shipping_charge,0) - COALESCE(orders.percentage_amount,0))'),
				'profit' => 'orders.percentage_amount',
			];
			$orderByCol = $orderMap[$orderKey] ?? 'orders.id';

			$role = Auth::user()->role;
			$authUserId = Auth::id();

			// ---- Base query (authorization scope only) ----
			$base = Order::query()
				->with(['orderItems', 'user', 'shippingCompany'])
				->whereNotNull('orders.shipping_company_id');

			// If role needs scoping
			if (in_array($role, ['user'])) {
				$base->where('orders.user_id', $authUserId);
			}

			// For recordsTotal (unfiltered except auth scope)
			$recordsTotal = (clone $base)->count();

			// ---- Apply filters (UI filters) ----
			$filtered = (clone $base);

			if ($request->filled('user_id')) {
				$filtered->where('orders.user_id', $request->user_id);
			}

			if ($request->filled('shipping_company_id')) {
				$filtered->where('orders.shipping_company_id', $request->shipping_company_id);
			}

			if ($request->filled('fromdate') && $request->filled('todate')) {
				$filtered->whereBetween('orders.order_date', [$request->fromdate, $request->todate]);
			} elseif ($request->filled('fromdate')) {
				$filtered->whereDate('orders.order_date', $request->fromdate);
			} elseif ($request->filled('todate')) {
				$filtered->whereDate('orders.order_date', $request->todate);
			}

			// Search (simple)
			if ($request->filled('search')) {
				$search = $request->input('search');
				$filtered->where(function ($q) use ($search) {
					$q->where('orders.created_at', 'like', "%{$search}%")
						->orWhere('orders.awb_number', 'like', "%{$search}%")
						->orWhere('orders.order_date', 'like', "%{$search}%")
						->orWhere('orders.courier_name', 'like', "%{$search}%")
						->orWhere('orders.status_courier', 'like', "%{$search}%")
						->orWhere('orders.id', 'like', "%{$search}%")
						->orWhere('orders.order_prefix', 'like', "%{$search}%")
						->orWhereHas('user', function ($u) use ($search) {
							$u->where('name', 'like', "%{$search}%")
								->orWhere('email', 'like', "%{$search}%")
								->orWhere('company_name', 'like', "%{$search}%")
								->orWhere('mobile', 'like', "%{$search}%");
						});
				});
			}

			// ---- Counts after filters ----
			$recordsFiltered = (clone $filtered)->count();

			// ---- Totals on filtered set ----
			$totals = (clone $filtered)
				->selectRaw('
					SUM(COALESCE(orders.shipping_charge,0) - COALESCE(orders.percentage_amount,0)) as total_shipping,
					SUM(COALESCE(orders.percentage_amount,0)) as total_profit
				')
				->first();

			$totalShipping = (float) ($totals->total_shipping ?? 0);
			$totalProfit = (float) ($totals->total_profit ?? 0);

			// ---- Fetch page data ----
			$rows = (clone $filtered)
				->orderBy($orderByCol, $dir)
				->offset($start)
				->limit($limit)
				->get();

			// ---- Build DataTables rows ----
			$data = [];
			$i = $start + 1;
			foreach ($rows as $order) {
				// row-level computed values
				$shipping_charges = (float) ($order->shipping_charge ?? 0) - (float) ($order->percentage_amount ?? 0);
				$profit = (float) ($order->percentage_amount ?? 0);

				$data[] = [
					'id' => $i,
					'order_date' => $order->order_date,
					'seller_details' => "<div class='main-cont1-2'><p>" .
					(isset($order->user) ? e($order->user->name) . ' (' . e($order->user->company_name) . ')' : 'N/A') .
						"</p><p>" . e($order->user->email ?? 'N/A') . "</p><p>" . e($order->user->mobile ?? 'N/A') . "</p></div>",
					'order_details' => $this->orderShipmentDetailHtml($order),
					'shippings' => "<div class='main-cont1-2'><p>" . e($order->courier_name) . "</p></div>",
					'shipping_charges' => "<div class='main-cont1-2'><p>" . number_format($shipping_charges, 2) . "</p></div>",
					// 'commision_charge' => "<div class='main-cont1-2'><p>" . number_format($order->percentage_amount, 2) . "</p></div>",
					'profit' => "<div class='main-cont1-2'><p>" . number_format($profit, 2) . "</p></div>",
				];
				$i++;
			}

			return response()->json([
				'draw' => $draw,
				'recordsTotal' => $recordsTotal,
				'recordsFiltered' => $recordsFiltered,
				'data' => $data,             // modern key
				'totals' => [
						'total_shipping' => $totalShipping, // numeric
						'total_profit' => $totalProfit,   // numeric
					],
			]);
		} 

		public function passbookUser()
		{ 
			$users = User::where('role', 'user')->where('kyc_status','!=', 0)->orderBy('name')->get();
			return view('report.passbook-user', compact('users'));
		}

		public function passbookUserAjax(Request $request)
		{
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length");

			$columnIndex = $request->post('order')[0]['column'];
			$orderColumn = $request->post('columns')[$columnIndex]['data'];
			$orderDir = $request->post('order')[0]['dir'];

			$orderColumn = ($orderColumn == 'action') ? 'created_at' : $orderColumn;

			$query = User::where('role', 'user')
				->where('kyc_status', '!=', 0)
				->when($request->user_id, function ($q) use ($request) {
					return $q->where('id', $request->user_id);
				});

			if ($request->filled('search')) {
				$search = $request->input('search');
				$query->where(function ($q) use ($search) {
					$q->where('name', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%");
				});
			}

			// Get all filtered records
			$allRecords = $query->orderBy("users.id", 'asc')->get();

			// Calculate total wallet amount
			$totalWallet = $allRecords->sum('wallet_amount');

			$data = [];
			foreach ($allRecords as $key => $value) {
			// Create URL for passbook
				$viewUrl = url('passbook', $value->id);
				$data[] = [
					'id' => $start + $key + 1,
					'name' => $value->name,
					'email' => $value->email,
					'balance' => number_format($value->wallet_amount, 2),
					'action' => '<a href="' . $viewUrl . '" class="btn btn-sm btn-success">Passbook</a>'
				];
			}

			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $allRecords->count(),
				"iTotalDisplayRecords" => $allRecords->count(),
				"aaData" => $data,
				"total_wallet_amount" => number_format($totalWallet, 2) // ← Added here
			]);
		}


		public function passbookReport($userId)
		{ 
			$user = User::find($userId); 
			$defaultFrom = now()->copy()->startOfMonth()->toDateString(); 
			$defaultTo = now()->copy()->endOfMonth()->toDateString();  
			return view('report.passbook', compact('user', 'defaultFrom', 'defaultTo'));
		}
		public function passbookReportAjax(Request $request)
		{
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length");

			$columnIndex = $request->post('order')[0]['column'];
			$orderColumn = $request->post('columns')[$columnIndex]['data'];
			$orderDir = $request->post('order')[0]['dir'];

			$orderColumn = ($orderColumn == 'action') ? 'created_at' : $orderColumn;

			$role = Auth::user()->role;
			$id = Auth::user()->id;

			// Base query for filtered records (applies user_id and date range if provided)
			$query = Billing::with(['order'])
				->when($request->user_id, function ($q) use ($request) {
					return $q->where('user_id', $request->user_id);
				})
				->when($request->fromdate && $request->todate, function ($q) use ($request) {
					$from = Carbon::parse($request->fromdate)->startOfDay();
					$to = Carbon::parse($request->todate)->endOfDay();
					return $q->whereBetween('billings.created_at', [$from, $to]);
				})
				// If only fromdate provided - filter that single day
				->when($request->fromdate && !$request->todate, function ($q) use ($request) {
					$from = Carbon::parse($request->fromdate)->startOfDay();
					$to = Carbon::parse($request->fromdate)->endOfDay();
					return $q->whereBetween('billings.created_at', [$from, $to]);
				})
				// If only todate provided - everything up to that day inclusive
				->when($request->todate && !$request->fromdate, function ($q) use ($request) {
					$to = Carbon::parse($request->todate)->endOfDay();
					return $q->where('billings.created_at', '<=', $to);
				});

			// --- Opening balance: sum of all transactions BEFORE fromdate (if provided)
			$openingBalance = 0.00;
			if ($request->fromdate) {
				$before = Carbon::parse($request->fromdate)->startOfDay();

				$openingQuery = Billing::query()
					->when($request->user_id, function ($q) use ($request) {
						return $q->where('user_id', $request->user_id);
					})
					->where('billings.created_at', '<', $before);

				// Sum credits as +amount, debits as -amount
				$openingBalance = $openingQuery->get()->reduce(function ($carry, $item) {
					return $carry + (($item->transaction_type === 'credit') ? $item->amount : -$item->amount);
				}, 0.00);
			} else {
				// If no fromdate provided, opening balance is zero (or you can compute all prior if desired)
				$openingBalance = 0.00;
			}

			// Get filtered records in ASC order (oldest → newest) to compute running balances cleanly
			$filteredAsc = $query->orderBy("billings.created_at", 'asc')->get();

			// Build running balances array
			$running = $openingBalance;
			$runningBalances = []; // same index as $filteredAsc
			foreach ($filteredAsc as $rec) {
				$running += ($rec->transaction_type === 'credit') ? $rec->amount : -$rec->amount;
				$runningBalances[] = $running;
			}

			// Closing balance = running after last filtered record (or opening if none)
			$closingBalance = $running;

			// For display you previously reversed the records (most recent first)
			$filteredDesc = $filteredAsc->sortByDesc('id')->values();

			// Reverse running balances array to align with $filteredDesc
			$runningBalancesDesc = array_reverse($runningBalances);

			// Prepare data rows (matching $filteredDesc order)
			$data = [];
			foreach ($filteredDesc as $key => $value) {

				$billingTypeHtml = $value->billing_type === 'Order'

					? '<div class="main-cont1-1">
						<div class="checkbox checkbox-purple">Order Id: 
							<a href="' . url('order/details/' . $value->billing_type_id) . '" target="_blank"> #' . $value->billing_type_id . ' </a> 
						</div> 
						<div class="checkbox checkbox-purple">Courier Name: 
							<a href="' . url('order/details/' . $value->billing_type_id) . '" target="_blank"> #' . ($value->order->courier_name ?? 'N/A') . ' </a> 
						</div>  
						<div class="checkbox checkbox-purple">Awb Number: 
							<a href="' . url('order/details/' . $value->billing_type_id) . '" target="_blank"> #' . ($value->order->awb_number ?? 'N/A') . ' </a> 
						</div> 
					</div>'
					: "<div class='main-cont1-2'><p>{$value->billing_type}</p></div>";

				// compute the index in runningBalancesDesc
				$balanceForRow = isset($runningBalancesDesc[$key]) ? $runningBalancesDesc[$key] : $openingBalance;

				$data[] = [
					'id' => $start + $key + 1,
					'billing_type' => $billingTypeHtml,
					'debit' => ($value->transaction_type == 'debit')
						? '<span class="text-danger">' . number_format($value->amount, 2) . '</span>'
						: '-',
					'credit' => ($value->transaction_type == 'credit')
						? '<span class="text-success">' . number_format($value->amount, 2) . '</span>'
						: '-',
					'balance' => number_format($balanceForRow, 2),
					'note' => "<div class='main-cont1-2'><p>{$value->note}</p></div>",
					'created_at' => "<div class='main-cont1-2'><p>" . date('Y M d | h:i A', strtotime($value->created_at)) . "</p></div>",
				];
			}

			if($openingBalance <= 0)
			{
				$openingBalance = Billing::where('user_id', $request->user_id)->orderBy('id')->first()->amount ?? 0;
			}
			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $filteredAsc->count(),
				"iTotalDisplayRecords" => $filteredAsc->count(),
				"aaData" => $data,
				// New fields:
				"opening_balance" => number_format($openingBalance, 2),
				"closing_balance" => number_format($closingBalance, 2),
			]);
		} 

		public function billingInvoice()
		{
			$users = User::where('role', 'user')->orderBy('name', 'asc')->get(); 
			return view('report.invoice', compact('users'));
		} 
		
		public function billingInvoiceAjax(Request $request)
		{
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length");

			$columnIndex_arr = $request->post('order');
			$columnName_arr = $request->post('columns');
			$order_arr = $request->post('order');
			$search = $request->post('search') ?? $request->post('search.value') ?? '';

			$columnIndex = $columnIndex_arr[0]['column'];
			$order = $columnName_arr[$columnIndex]['data'] ?? 'created_at';
			$dir = $order_arr[0]['dir'] ?? 'desc';

			if ($order == 'action') {
				$order = 'created_at';
			}

			$user = auth()->user();

			$query = Invoice::with('user');

			if ($user->role === 'user') {
				$query->where('user_id', $user->id);
			}
			 
			if ($request->filled('user_id')) {
				$query->where('user_id', $request->user_id);
			}
  
			if ($request->filled('year') && $request->filled('month') && is_numeric($request->year) && is_numeric($request->month)) {
				$query->whereYear('invoice_date', $request->year)
					  ->whereMonth('invoice_date', $request->month);
			}

			$totalData = $query->count();

			// Apply search filter
			if (!empty($search)) { 
				$query->where(function ($q) use ($search) {
					$q->whereHas('user', function ($sub) use ($search) {
						$sub->where('name', 'LIKE', "%{$search}%")
							->orWhere('email', 'LIKE', "%{$search}%")
							->orWhere('company_name', 'LIKE', "%{$search}%");
					})
					->orWhere('invoice_number', 'LIKE', "%{$search}%")
					->orWhere('invoice_period', 'LIKE', "%{$search}%")
					->orWhere('invoice_date', 'LIKE', "%{$search}%")
					->orWhere('total_amount', 'LIKE', "%{$search}%");
				});
			}

			$totalFiltered = $query->count();

			$invoices = $query
				->orderBy($order, $dir)
				->offset($start)
				->limit($limit)
				->get();

			$data = [];
			$index = $start + 1;

			foreach ($invoices as $invoice) {
				$data[] = [
					'id' => $index++,
					'invoice_state' => $invoice->invoice_state,
					'invoice_number' => $invoice->invoice_number,
					'invoice_period' => $invoice->invoice_period,
					'invoice_date' => $invoice->invoice_date,
					'total_amount' => number_format($invoice->total_amount, 2),
					'user_name' => trim(($invoice->user->name ?? '') . ' ' . ($invoice->user->company_name ?? '')),
					'action' => '
						<a href="' . route('report.billing-invoice.pdf', $invoice->id) . '" class="btn btn-sm btn-danger" target="_blank">PDF</a>
						<a href="' . route('report.billing-invoice.excel', $invoice->id) . '" class="btn btn-sm btn-success" target="_blank">Excel</a>'
				];
			}

			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $totalData,
				"iTotalDisplayRecords" => $totalFiltered,
				"aaData" => $data,
			]);
		}
		
		public function billingInvoicePdf($invoiceId)
		{
			$invoiceDetail = Invoice::with('user')->findOrFail($invoiceId);
			$user = $invoiceDetail->user;

			$invoice = [
				'number' => $invoiceDetail->invoice_number,
				'date' => \Carbon\Carbon::parse($invoiceDetail->invoice_date)->format('d M Y'),
				'period' => \Carbon\Carbon::parse($invoiceDetail->month_start)->format('d') . ' to ' . \Carbon\Carbon::parse($invoiceDetail->month_end)->format('d M Y'),
				'from' => [
					'name' => 'KSHIP EXPRESS',
					'address' => 'RING ROAD SURAT GUJARAT 395003',
					'gst' => '24AFKFS090154',
				],
				'to' => [
					'name' => $user->company_name ?? '',
					'address' => trim(($user->address ?? '') . ', ' . ($user->city ?? '') . ', ' . ($user->state ?? '') . ', ' . ($user->country ?? '')),
					'mobile' => $user->mobile ?? '',
				],
				'charges' => [
					'shipping' => round($invoiceDetail->base_amount, 2),
					'cgst' => round($invoiceDetail->cgst_amount, 2),
					'sgst' => round($invoiceDetail->sgst_amount, 2),
					'igst' => round($invoiceDetail->igst_amount, 2),
				],
			];

			$total = $invoiceDetail->total_amount;
			$pdf = Pdf::loadView('report.pdf-invoice', compact('invoice', 'total'))->setPaper('a4', 'portrait');
			$filename = 'invoice_' . $invoiceDetail->invoice_number . '.pdf';

			return $pdf->download($filename); 
		}
		
		 
		public function billingInvoiceExcel($invoiceId)
		{
			$invoice = Invoice::with(['orders.user'])->findOrFail($invoiceId);
			// Replace slashes to make a valid filename
			$sanitizedNumber = str_replace('/', '-', $invoice->invoice_number);
			$filename = 'invoice_' . $sanitizedNumber . '.xlsx';
	
			return Excel::download(new BillingInvoiceExport($invoice), $filename);
		}
	}
