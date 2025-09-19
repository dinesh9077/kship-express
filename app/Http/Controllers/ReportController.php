<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Order;
	use App\Models\OrderItem;
	use App\Models\Vendor;
	use App\Models\VendorAddress;
	use App\Models\OrderActivity;
	use App\Models\OrderStatus;
	use App\Models\Billing;
	use App\Models\Packaging;
	use App\Models\WeightFreeze;
	use App\Models\Customer;
	use App\Models\User;
	use App\Models\CustomerAddress;
	use App\Models\PincodeService;
	use App\Models\ShippingCompany;
	use App\Models\CourierWarehouse;
	use App\Models\ProductCategory;
	use App\Exports\PendingStarOrderExport;
	use DB,Auth,File,Helper;
	use Illuminate\Support\Facades\Http;
	use App\Exports\OrdersExport;
	use Maatwebsite\Excel\Facades\Excel;

	class ReportController extends Controller
	{ 
		public function __construct()
		{
			$this->middleware('auth');
		}
		
		public function index(Request $request)
		{ 
			$users = User::where('role', 'user')->orderBy('name')->get();
			$status = $request->query('status', 'New');

			return view('report.order', compact('status', 'users'));
		}
 
    	public function reportOrderAjax(Request $request)
		{
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); // Rows per page

			$columnIndex = $request->post('order')[0]['column'] ?? 0;
			$orderColumn = $request->post('columns')[$columnIndex]['data'] ?? 'id';
			$dir = $request->post('order')[0]['dir'] ?? 'asc';

			// Fix incorrect assignment (`=` instead of `==`)
			if ($orderColumn == 'action') {
				$orderColumn = 'id';
			}

			$currentDate = date('Y-m-d'); 
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
			if ($request->filled('search')) {
				$search = $request->input('search');
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
				 
				$data[] = [
					'id' => $i++,
					'seller_details' => "<div class='main-cont1-2'><p>" . 
					(isset($order->user) ? "{$order->user->name} ({$order->user->company_name})" : "N/A") . 
					"</p><p>" . ($order->user->email ?? "N/A") . "</p><p>" . ($order->user->mobile ?? "N/A") . "</p></div>",
					
					'order_details' => $this->orderShipmentDetailHtml($order),
					
					'customer_details' => "<div class='main-cont1-2'><p>" . 
					(isset($order->customer) ? "{$order->customer->first_name} {$order->customer->last_name}" : "N/A") . 
					"</p><p>" . ($order->customer->email ?? "N/A") . "</p><p>" . ($order->customer->mobile ?? "N/A") . "</p></div>",
		
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
					'created_at' => $order->created_at->format('Y-m-d h:i A'), 
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
			$productDetails = $order->orderItems
			->map(fn($item) => "<p>{$item->product_description} Amount: {$item->amount} No Of Box: {$item->quantity}</p>")
			->implode(' | ');
			
			$output = '';
			
			$output .= "<div class='main-cont1-1'>
			<div class='checkbox checkbox-purple'>
			Order Prefix/LR No: <a href='" . url("order/details/{$order->id}") . "'>#{$order->order_prefix}</a> 
			</div>
			<div class='checkbox checkbox-purple'>
			Courier: ".($order->courier_name ?? 'N/A')."
			</div>"; 
			if($order->awb_number)
			{
				$output .= "<div class='checkbox checkbox-purple'>
				AWB Number: <a href='" . url("order/details/{$order->id}") . "'>#{$order->awb_number}</a>
				</div>"; 
			} 
			$output .= "<span style='padding-left:0'> 
			<a href='javascript:;'> 
			<div class='tooltip' data-toggle='tooltip' data-placement='top' title='" . strip_tags($productDetails) . "'> 
			View Products 
			</div>
			</a> 
			</span>
			</div>";
			
			return $output;
		}
		
		public static function orderPackageDetailHtml($order)
		{
			$orderItems = $order->orderItems;
			$productDetails = $orderItems->map(fn ($item) => "<p>Weight In Kg: ".($item->dimensions['weight'] ?? '')." Length: ".($item->dimensions['length'] ?? '')." Width: ".($item->dimensions['width'] ?? '')." Height: ".($item->dimensions['height'] ?? '')."</p>")->implode(' | '); 
			$noofBox = $orderItems->sum('quantity');
			$totalWeightInKg = $orderItems->sum('dimensions.weight') ?? 0;
			
			$output = ''; 
			$output .= "<div class='main-cont1-1'>
				<div class='checkbox checkbox-purple'>
					No Of Box: {$noofBox}
				</div> 
				<div class='checkbox checkbox-purple'>
					Weight In Kg: {$totalWeightInKg}
				</div>"; 
				 
				$output .= "<span style='padding-left:0'> 
					<a href='javascript:;'> 
						<div class='tooltip' data-toggle='tooltip' data-placement='top' title='" . strip_tags($productDetails) . "'> 
							View Package Details 
						</div>
					</a> 
				</span>
			</div>";
			
			return $output;
		}
		
		public function incomeReport()
		{ 
			$users = User::where('role', 'user')->orderBy('name')->get(); 
			return view('report.income', compact('users'));
		}
		
		public function incomeReportAjax(Request $request)
		{
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); // Rows per page
			$search = $request->post('search')['value'] ?? null;
			$orderColumnIndex = $request->post('order')[0]['column'] ?? 0;
			$orderDirection = $request->post('order')[0]['dir'] ?? 'asc';
			$columns = $request->post('columns');
			$orderColumn = $columns[$orderColumnIndex]['data'] ?? 'id';

			if ($orderColumn === 'action') {
				$orderColumn = 'id';
			}

			$currentDate = date('Y-m-d');
			$status = $request->post('status');
			$userId = Auth::id();
			$userRole = Auth::user()->role;

			// Optimize with eager loading to reduce queries
			$query = Order::with(['orderItems', 'user'])
				->when(in_array($userRole, ["user"]), fn($q) => $q->where('orders.user_id', $userId)) 
				->when($request->user_id, fn($q) => $q->where('orders.user_id', $request->user_id))
				->when($request->fromdate && $request->todate, fn($q) => $q->whereBetween('orders.order_date', [$request->fromdate, $request->todate]))
				->when($request->fromdate && !$request->todate, fn($q) => $q->whereDate('orders.order_date', $request->fromdate))
				->when($request->todate && !$request->fromdate, fn($q) => $q->whereDate('orders.order_date', $request->todate))
				->where('orders.shipping_company_id', '!=', '');

			// Searching logic
			if ($request->filled('search')) {
				$search = $request->input('search');
				$query->where(function ($q) use ($search) {
					$q->where('created_at', 'LIKE', "%{$search}%") 
					  ->orWhereHas('user', function($q) use ($search){
						  $q->where('name', 'LIKE', "%{$search}%")
						  ->orwhere('email', 'LIKE', "%{$search}%")
						  ->orwhere('company_name', 'LIKE', "%{$search}%")
						  ->orwhere('mobile', 'LIKE', "%{$search}%");
						}) 
					  ->orWhere('awb_number', 'LIKE', "%{$search}%")
					  ->orWhere('courier_name', 'LIKE', "%{$search}%")
					  ->orWhere('status_courier', 'LIKE', "%{$search}%")
					  ->orWhere('id', 'LIKE', "%{$search}%")
					  ->orWhere('order_prefix', 'LIKE', "%{$search}%");
				});
			}

			// Get total records before pagination
			$totalData = $query->count();

			// Apply sorting and pagination
			$orders = $query->orderBy("orders.{$orderColumn}", $orderDirection)
				->offset($start)
				->limit($limit)
				->get();

			// Collect order data
			$data = $orders->map(function ($order, $index) use ($start) {
				$orderItems = $order->orderItems; // Use eager-loaded relation
				$productDetails = $orderItems->map(fn($item) => "<p>{$item->product_name} Amount: {$item->amount} QTY: {$item->quantity}</p>")
					->implode(' | ');

				return [
					'id' => $start + $index + 1,
					'seller_details' => "<div class='main-cont1-2'><p>" . 
					(isset($order->user) ? "{$order->user->name} ({$order->user->company_name})" : "N/A") . 
					"</p><p>" . ($order->user->email ?? "N/A") . "</p><p>" . ($order->user->mobile ?? "N/A") . "</p></div>",
					'order_details' => $this->orderShipmentDetailHtml($order),
					'charge' => "<div class='main-cont1-2'><p>{$order->shipping_charge}</p></div>",
					'income' => "<div class='main-cont1-2'><p>{$order->percentage_amount}</p></div>",
				];
			});

			// Response
			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $totalData,
				"iTotalDisplayRecords" => $totalData,
				"aaData" => $data
			]);
		}  
		
		public function paymentReport()
		{
			$users = User::where('role', 'user')->orderBy('name')->get(); 
			$shippingcompanies = ShippingCompany::whereStatus(1)->get();
			return view('report.payment', compact('users', 'shippingcompanies'));
		}
		  
		public function paymentReportAjax(Request $request)
		{
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); 
			$order_arr = $request->post('order');
			$columnName_arr = $request->post('columns');

			$columnIndex = $order_arr[0]['column'];
			$order = $columnName_arr[$columnIndex]['data'];
			$dir = $order_arr[0]['dir'];
			
			if ($order === 'action') {
				$order = 'id';
			}

			$role = Auth::user()->role;
			$id = Auth::user()->id;
			$status = $request->post('status');
			$currentDate = date('Y-m-d');

			// Build base query
			$query = Order::with(['orderItems', 'user', 'shippingCompany'])  
			->whereNotNull('orders.shipping_company_id');

			// Apply filters
			if (in_array($role, ["user"])) {
				$query->where('orders.user_id', $id);
			}
 
			if ($request->filled('user_id')) {
				$query->where('orders.user_id', $request->user_id);
			}

			if ($request->filled('shipping_company_id')) {
				$query->where('orders.shipping_company_id', $request->shipping_company_id);
			}

			if ($request->filled('fromdate') && $request->filled('todate')) {
				$query->whereBetween('orders.order_date', [$request->fromdate, $request->todate]);
			} elseif ($request->filled('fromdate')) {
				$query->whereDate('orders.order_date', $request->fromdate);
			} elseif ($request->filled('todate')) {
				$query->whereDate('orders.order_date', $request->todate);
			}

			// Search functionality
			if ($request->filled('search')) {
				$search = $request->input('search');
				$query->where(function ($q) use ($search) {
					$q->where('created_at', 'LIKE', "%{$search}%") 
					  ->orWhereHas('user', function($q) use ($search){
						  $q->where('name', 'LIKE', "%{$search}%")
						  ->orwhere('email', 'LIKE', "%{$search}%")
						  ->orwhere('company_name', 'LIKE', "%{$search}%")
						  ->orwhere('mobile', 'LIKE', "%{$search}%");
						}) 
					  ->orWhere('awb_number', 'LIKE', "%{$search}%")
					  ->orWhere('courier_name', 'LIKE', "%{$search}%")
					  ->orWhere('status_courier', 'LIKE', "%{$search}%")
					  ->orWhere('id', 'LIKE', "%{$search}%")
					  ->orWhere('order_prefix', 'LIKE', "%{$search}%");
				});
			}

			// Get total filtered count
			$totalFiltered = $query->count();

			// Fetch paginated results with ordering
			$values = $query->orderBy("orders.$order", $dir)
							->offset($start)
							->limit($limit)
							->get();
 
			$data = [];
			$i = $start + 1;
			foreach ($values as $order) {
				 
				// Calculate total charges
				$shipping_charges = $order->shipping_charge - $order->percentage_amount;
				$total_charge = $shipping_charges - ($order->tax ?? 0);
				 
				// Prepare data row
				$data[] = [
					'id' => $i,
					'seller_details' => "<div class='main-cont1-2'><p>" . 
					(isset($order->user) ? "{$order->user->name} ({$order->user->company_name})" : "N/A") . 
					"</p><p>" . ($order->user->email ?? "N/A") . "</p><p>" . ($order->user->mobile ?? "N/A") . "</p></div>",
					'order_details' => $this->orderShipmentDetailHtml($order),
					'shippings' => "<div class='main-cont1-2'><p>{$order->courier_name}</p></div>",
					'charge' => "<div class='main-cont1-2'><p>" . number_format($order->shipping_charge, 2) . "</p></div>",
					'shipping_charges' => "<div class='main-cont1-2'><p>" . number_format($total_charge, 2) . "</p></div>",
				];

				$i++;
			}

			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $totalFiltered,
				"iTotalDisplayRecords" => $totalFiltered,
				"aaData" => $data
			]);
		}
		
		public function passbookReport()
		{ 
			$users = User::where('role', 'user')->orderBy('name', 'asc')->get(); 
			return view('report.passbook', compact('users'));
		}
		
		public function passbookReportAjax(Request $request)
		{ 
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); // Rows per page

			// Sorting
			$columnIndex = $request->post('order')[0]['column']; // Column index
			$orderColumn = $request->post('columns')[$columnIndex]['data']; // Column name
			$orderDir = $request->post('order')[0]['dir']; // Sorting direction

			// Fix ordering issue
			$orderColumn = ($orderColumn == 'action') ? 'created_at' : $orderColumn;

			$role = Auth::user()->role;
			$id = Auth::user()->id;

			// Main Query
			$query = Billing::select('billings.*', 'users.name', 'users.wallet_amount')
				->join('users', 'users.id', '=', 'billings.user_id')
				->when(in_array($role, ['user']), fn($q) => $q->where('billings.user_id', $id))
				->when(!in_array($role, ['user']) && $request->user_id, fn($q) => $q->where('billings.user_id', $request->user_id))
				->when($request->fromdate && $request->todate, fn($q) => $q->whereBetween('billings.created_at', [$request->fromdate, $request->todate]))
				->when($request->fromdate && !$request->todate, fn($q) => $q->whereDate('billings.created_at', $request->fromdate))
				->when($request->todate && !$request->fromdate, fn($q) => $q->whereDate('billings.created_at', $request->todate));

			// Get total records before filtering
			$totalData = $query->count();

			// Apply search filter
			if (!empty($request->input('search'))) {
				$search = $request->input('search');
				$query->where(function ($q) use ($search) {
					$q->where('users.name', 'LIKE', "%{$search}%")
					  ->orWhere('billings.amount', 'LIKE', "%{$search}%")
					  ->orWhere('billings.billing_type', 'LIKE', "%{$search}%")
					  ->orWhere('billings.transaction_type', 'LIKE', "%{$search}%")
					  ->orWhere('billings.note', 'LIKE', "%{$search}%");
				});
			}

			// Get total filtered records
			$totalFiltered = $totalData;

			// Apply pagination and ordering
			$values = $query->offset($start)
							->limit($limit)
							->orderBy("billings.id", 'asc')
							->get();

			// Process Data for Response
			$data = [];
			$balance = 0;
			foreach ($values as $key => $value)
			{
				$orderitems = OrderItem::where('order_id', $value->billing_type_id)->get();
				$product_details = $orderitems->map(fn($item) => "<p>{$item->product_name} Amount: {$item->amount} QTY: {$item->quantity}</p>")->implode(' | ');

				$billingTypeHtml = $value->billing_type === 'Order'
					? '<div class="main-cont1-1">
							<div class="checkbox checkbox-purple">Order Id: 
								<a href="'.url('order/details/'.$value->billing_type_id).'" target=_blank"> #'.$value->billing_type_id.' </a>
							</div> 
					   </div>'
					: "<div class='main-cont1-2'><p>{$value->billing_type}</p></div>";

				$transactionTypeHtml = ($value->transaction_type == 'debit')
					? '<span class="badge badge-danger" disabled>Debit</span>'
					: '<span class="badge badge-success" disabled>Credit</span>';

				$balance += ($value->transaction_type == 'debit') ? -$value->amount : $value->amount;

				$data[] = [
					'id' => $start + $key + 1,
					'name' => "<div class='main-cont1-2'><p>{$value->name}</p></div>",
					'billing_type' => $billingTypeHtml,
					'transaction_type' => "<div class='main-cont1-2'>{$transactionTypeHtml}</div>",
					'debit' => ($value->transaction_type == 'debit') ? $value->amount : "-",
					'credit' => ($value->transaction_type == 'credit') ? $value->amount : "-",
					'balance' => $balance,
					'note' => "<div class='main-cont1-2'><p>{$value->note}</p></div>",
					'created_at' => "<div class='main-cont1-2'><p>".date('Y M d | h:i A', strtotime($value->created_at))."</p></div>",
				];
			}

			// Response
			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $totalData,
				"iTotalDisplayRecords" => $totalFiltered,
				"aaData" => $data
			]); 
		}

		
		public function invoice_report()
		{
			$users = User::orderBy('name', 'asc')->get();
			$status = (isset($_GET['status']))?$_GET['status']:'New';
			return view('report.invoice',compact('status','users'));
		}
		
		
		public function invoicetAjax(Request $request)
		{
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); // Rows display per page
			
			$columnIndex_arr = $request->post('order');
			$columnName_arr = $request->post('columns');
			$order_arr = $request->post('order');
			$search_arr = $request->post('search');
			
			
			
			$columnIndex = $columnIndex_arr[0]['column']; // Column index
			$order = $columnName_arr[$columnIndex]['data']; // Column name
			$dir = $order_arr[0]['dir']; // asc or desc
			
			if($order = 'action')  
			{
				$order = 'created_at';
			}
			
			$role = Auth::user()->role;
			$id = Auth::user()->id;
			
			$query = DB::table('invoices')->where('invoices.id','!=','');
			$query->select('invoices.*','user_kycs.gst' , 'users.name','users.email','users.company_name','users.mobile','orders.id','orders.awb_number','orders.status_courier as order_status','orders.tax as order_total_tax','users.state as billing_state');
			$query->join('users','users.id','=','invoices.user_id');
			$query->join('user_kycs','user_kycs.user_id','=','invoices.user_id');
			$query->join('orders','orders.id','=','invoices.order_id');
			if($role != "admin") 
			{
				$query->where('invoices.user_id',$id);
			}
			
			$totalData = $query->get()->count();
			// echo "<pre>";	 print_r($totalData); echo "</pre>"; die;
			
			$totalFiltered = DB::table('invoices')->where('invoices.id','!=','');
			$totalFiltered->select('invoices.*','user_kycs.gst' ,'users.name','users.email','users.company_name','users.mobile','orders.id','orders.awb_number','orders.status_courier as order_status','orders.tax as order_total_tax','users.state as billing_state');
			$totalFiltered->join('users','users.id','=','invoices.user_id');
			$totalFiltered->join('user_kycs','user_kycs.user_id','=','invoices.user_id');
			$totalFiltered->join('orders','orders.id','=','invoices.order_id');
			if($role != "admin") 
			{
				$totalFiltered->where('invoices.user_id',$id);
				}else{
				if($request->user_id)
			    {
					$user_id = $request->user_id;
					$totalFiltered->where('invoices.user_id',$user_id);
				}
			}
			
			
			$values = DB::table('invoices')->where('invoices.id','!=','');
			$values->select('invoices.*','user_kycs.gst' , 'users.name','users.email','users.company_name','users.mobile','orders.id','orders.awb_number','orders.status_courier as order_status','orders.tax as order_total_tax','users.state as billing_state');
			$values->join('users','users.id','=','invoices.user_id');
			$values->join('user_kycs','user_kycs.user_id','=','invoices.user_id');
			$values->join('orders','orders.id','=','invoices.order_id');
			if($role != "admin") 
			{
				$values->where('invoices.user_id',$id);
				}else{
			    if($request->user_id)
			    {
					$user_id = $request->user_id;
				    $values->where('invoices.user_id',$user_id); 
				}
				
				
			}
			
			
			if ($request->fromdate && $request->todate) {
				
				$values->whereBetween('invoices.date', [$request->fromdate, $request->todate]);
				} elseif ($request->fromdate) {
				
				$values->whereDate('invoices.date', $request->fromdate);
				} elseif ($request->todate) {
				
				$values->whereDate('invoices.date', $request->todate);
			} 
			
			$values->offset($start)->limit($limit)->orderBy('invoices'.'.'.$order,"DESC");
			
			if(!empty($request->input('search')))
			{ 
				$search = $request->input('search');
				$values = $values->where(function ($query) use ($search) 
				{
					return $query->where('users.name', 'LIKE',"%{$search}%")
					->where('users.email', 'LIKE',"%{$search}%")
					->orWhere('users.mobile', 'LIKE',"%{$search}%")
					->orWhere('orders.awb_number', 'LIKE',"%{$search}%")
					->orWhere('orders.id', 'LIKE',"%{$search}%")
					->orWhere('invoices.total_amount', 'LIKE',"%{$search}%")
					->orWhere('invoices.inv_code', 'LIKE',"%{$search}%")
					->orWhere('invoices.date', 'LIKE',"%{$search}%");
				});
				
				$totalFiltered = $totalFiltered->where(function ($query) use ($search) {
			    	return $query->where('users.name', 'LIKE',"%{$search}%")
					->where('users.email', 'LIKE',"%{$search}%")
					->orWhere('users.mobile', 'LIKE',"%{$search}%")
					->orWhere('orders.awb_number', 'LIKE',"%{$search}%")
					->orWhere('orders.id', 'LIKE',"%{$search}%")
					->orWhere('invoices.total_amount', 'LIKE',"%{$search}%")
					->orWhere('invoices.inv_code', 'LIKE',"%{$search}%")
					->orWhere('invoices.date', 'LIKE',"%{$search}%");
					
				});  
			}
			
			$values = $values->get(); 
			 
			$totalFiltered = $totalFiltered->count();
			
			$data = array();
			if(!empty($values))
			{
				$i = $start + 1; 
				// 			$balance = 0;
				foreach ($values as $value)
				{    	
					
					
					if($value->billing_state == 'Gujarat')
					{
						$mainData['sgst'] = number_format(($value->order_total_tax / 2),2);
						$mainData['cgst'] = number_format(($value->order_total_tax / 2),2);
						$mainData['igst'] = '';
						
					}else
					{
						$mainData['sgst'] = '';
						$mainData['cgst'] = '';
						$mainData['igst'] = number_format(($value->order_total_tax),2);
					}
					$mainData['id'] = $i;
					$mainData['inv_no'] = $value->inv_code;
					$mainData['vendor'] = '<div class="main-cont1-2"><p> '.$value->name.' ('.$value->company_name .') </p><p> '.$value->email.'  </p><p> '.$value->mobile.' </p></div>';
					$mainData['gst'] = '<p> '.$value->gst.'</p>';
					$mainData['order'] = '<div class="main-cont1-2"><p> Order ID : '.$value->order_id.' </p><p> Awb No : '.$value->awb_number.'  </p></div>';
					$mainData['order_status'] = $value->order_status;
					$mainData['date'] = $value->date;
					$mainData['amount'] = $value->total_amount;
					
					
					$mainData['created_at'] = ' <div class="main-cont1-2"><p>'.date('Y M d | h:i A',strtotime($value->created_at)).' </p></div>';
					
					$data[] = $mainData;
					$i++;
				}
			}
			
			$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalData,
			"iTotalDisplayRecords" => $totalFiltered,
			"aaData" => $data
			); 
			
			echo json_encode($response);
			exit;
		}
		 
		public function daily_recharge()
		{
			$users = User::orderBy('name', 'asc')->get();
			
			return view('report.recharge',compact('users'));
		}
		
		public function RechargeAjaxData(Request $request)
		{
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); // Rows display per page
			
			$columnIndex_arr = $request->post('order');
			$columnName_arr = $request->post('columns');
			$order_arr = $request->post('order');
			$search_arr = $request->post('search');
			
			// Assuming 'action' is the default sorting column
			$order = $columnName_arr[$columnIndex_arr[0]['column']]['data']; // Column name
			$dir = $order_arr[0]['dir']; // asc or desc
			
			if($order === 'action')  
			{
				$order = 'id';
			}
			
			$role = Auth::user()->role;
			$id = Auth::user()->id;
			
			$query = DB::table('user_wallets')->where('user_wallets.id','!=','');
			$query->join('users','users.id','=','user_wallets.user_id');
			
			if($role != "admin") 
			{
				$query->where('user_wallets.user_id',$id);
			}
			
			$totalData = $query->get()->count();
			
			$totalFiltered = DB::table('user_wallets')->where('user_wallets.id','!=','');
			
			$totalFiltered->join('users','users.id','=','user_wallets.user_id');
			if($role != "admin") 
			{
				$totalFiltered->where('user_wallets.user_id',$id);
			}
			
			$values = DB::table('user_wallets')
			->select('user_wallets.*','users.name','users.email','users.mobile','users.company_name','users.wallet_amount')
			->join('users','users.id','=','user_wallets.user_id');
			
			if ($request->user_id) {
				$user_id = $request->user_id;
				$values->where('user_wallets.user_id',$user_id);
			}
			
			
			
			if ($request->fromdate && $request->todate) {
				
				$values->whereBetween('user_wallets.created_at', [$request->fromdate, $request->todate]);
				} elseif ($request->fromdate) {
				
				$values->whereDate('user_wallets.created_at', $request->fromdate);
				} elseif ($request->todate) {
				
				$values->whereDate('user_wallets.created_at', $request->todate);
			} 
			
			if ($request->transaction_type && $request->transaction_type != 'All') {
				
				$values->where('user_wallets.transaction_type',$request->transaction_type);
			}
			
			
			if(!empty($request->input('search')))
			{ 
				$search = $request->input('search');
				$values->where(function ($query) use ($search) 
				{
					return $query->where('users.name', 'LIKE',"%{$search}%")
					->orWhere('users.email', 'LIKE',"%{$search}%")
					->orWhere('users.mobile', 'LIKE',"%{$search}%")
					->orWhere('user_wallets.created_at', 'LIKE',"%{$search}%")
					->orWhere('user_wallets.amount', 'LIKE',"%{$search}%");
				});
				
				$totalFiltered = $totalFiltered->where(function ($query) use ($search) {
					return $query->where('users.name', 'LIKE',"%{$search}%")
					->orWhere('users.email', 'LIKE',"%{$search}%")
					->orWhere('users.mobile', 'LIKE',"%{$search}%")
					->orWhere('user_wallets.created_at', 'LIKE',"%{$search}%")
					->orWhere('user_wallets.amount', 'LIKE',"%{$search}%");
				});  
			}
			
			$totalFiltered = $totalFiltered->count();
			
			$clonedQuery = clone $values;
			
			$resultsCloned = $clonedQuery
			->select(DB::raw('IFNULL(SUM(CASE WHEN user_wallets.transaction_type = "Online" THEN user_wallets.amount ELSE 0 END),0) AS online_total'),
            DB::raw('IFNULL(SUM(CASE WHEN user_wallets.transaction_type = "Offline" THEN user_wallets.amount ELSE 0 END),0) AS offline_total'))
			->first();
			
			$values = $values->offset($start)->limit($limit)->orderBy('user_wallets'.'.'.$order,$dir)->get();
			
			
			// $online_total = 
			$data = array();
			if(!empty($values))
			{
				$i = $start + 1; 
				foreach ($values as $value)
				{    
					$formattedDate = date('d M Y', strtotime($value->created_at));
					
					$mainData['id'] = $i;
					$mainData['user_details'] = ' <div class="main-cont1-2"><p> '.$value->name.' ('.$value->company_name .') </p><p> '.$value->email.'  </p><p> '.$value->mobile.' </p></div>';
					
					$mainData['amount'] =  ' <div class="main-cont1-2"> <p> '.number_format($value->amount,2).' </p></div>';
					$mainData['transaction_type'] = ' <div class="main-cont1-2"> <p> '.$value->transaction_type.' </p></div>';
					$mainData['date'] =  ' <div class="main-cont1-2"> <p> '.$formattedDate.' </p></div>';
					$mainData['balance'] =  ' <div class="main-cont1-2"> <p> '.$value->wallet_amount.' </p></div>';
					
					$data[] = $mainData;
					$i++;
				}
			}
			
			$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalData,
			"iTotalDisplayRecords" => $totalFiltered,
			"aaData" => $data,
			"offlineAmt" => $resultsCloned->offline_total,
			"onlineAmt" => $resultsCloned->online_total
			); 
			
			return response()->json($response);
		}
		 
	}
