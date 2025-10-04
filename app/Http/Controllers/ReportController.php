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
			$users = User::where('role', 'user')->where('kyc_status','!=', 0)->orderBy('name')->get();
			$status = $request->query('status', 'New');
			$couriers = Order::select('courier_name')
			->distinct()
			->orderBy('courier_name')
			->pluck('courier_name', 'courier_name')
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
						<a href='javascript:;' class='show-details-btn' data-order='{$jsonItems}' style='border-bottom: 1px solid #1A4BEC ; color: #1A4BEC;'    >
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
					  ->orWhere('order_date', 'LIKE', "%{$search}%")
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
				$total_charge = $shipping_charges;
				 
				// Prepare data row
				$data[] = [
					'id' => $i,
					'order_date' => $order->order_date,
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
			$users = User::where('role', 'user')->where('kyc_status','!=', 0)->orderBy('name')->get();
			return view('report.passbook', compact('users'));
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

			$query = Billing::with(['user'])
			->when(
				in_array($role, ['user']),
				fn($q) =>
				$q->where('billings.user_id', $id)
			)
			->when(
				!in_array($role, ['user']) && $request->user_id,
				fn($q) =>
				$q->where('billings.user_id', $request->user_id)
			)
			// ✅ From + To date (inclusive, full-day range)
			->when($request->fromdate && $request->todate, function ($q) use ($request) {
				$from = Carbon::parse($request->fromdate)->startOfDay();
				$to = Carbon::parse($request->todate)->endOfDay();
				return $q->whereBetween('billings.created_at', [$from, $to]);
			})
			// ✅ Only From date (single-day filter)
			->when($request->fromdate && !$request->todate, function ($q) use ($request) {
				$from = Carbon::parse($request->fromdate)->startOfDay();
				$to = Carbon::parse($request->fromdate)->endOfDay();
				return $q->whereBetween('billings.created_at', [$from, $to]);
			})
			// ✅ Only To date (everything up to that day inclusive)
			->when($request->todate && !$request->fromdate, function ($q) use ($request) {
				$to = Carbon::parse($request->todate)->endOfDay();
				return $q->where('billings.created_at', '<=', $to);
			});

			// Get all records for accurate reverse balance calculation
			$allRecords = $query->orderBy("billings.id", 'asc')->get();

			// Calculate final balance
			$totalBalance = 0;
			foreach ($allRecords as $record) {
				$totalBalance += ($record->transaction_type == 'debit') ? -$record->amount : $record->amount;
			}

			// Reverse order for pagination display
			$reversed = $allRecords->sortByDesc('id')->values();
			$paginated = $reversed/* ->slice($start, $limit) */;

			$data = [];
			$currentBalance = $totalBalance;

			foreach ($paginated as $key => $value) {
				  
				$billingTypeHtml = $value->billing_type === 'Order'
					? '<div class="main-cont1-1">
							<div class="checkbox checkbox-purple">Order Id: 
								<a href="'.url('order/details/'.$value->billing_type_id).'" target="_blank"> #'.$value->billing_type_id.' </a>
							</div> 
					   </div>'
					: "<div class='main-cont1-2'><p>{$value->billing_type}</p></div>";

				$transactionTypeHtml = ($value->transaction_type == 'debit')
					? '<span class="badge badge-danger" disabled>Debit</span>'
					: '<span class="badge badge-success" disabled>Credit</span>';
				
				$displayCurrentBalance = $currentBalance;
				
				$data[] = [
					'id' => $start + $key + 1,
					'name' => "<div class='main-cont1-2'><p>{$value->user->name}</p></div>",
					'billing_type' => $billingTypeHtml,
					'transaction_type' => "<div class='main-cont1-2'>{$transactionTypeHtml}</div>",
					'debit' => ($value->transaction_type == 'debit') ? $value->amount : "-",
					'credit' => ($value->transaction_type == 'credit') ? $value->amount : "-",
					'balance' => number_format($displayCurrentBalance, 2),
					'note' => "<div class='main-cont1-2'><p>{$value->note}</p></div>",
					'created_at' => "<div class='main-cont1-2'><p>".date('Y M d | h:i A', strtotime($value->created_at))."</p></div>",
				];

				$currentBalance -= ($value->transaction_type == 'debit') ? -$value->amount : $value->amount;
			}

			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $allRecords->count(),
				"iTotalDisplayRecords" => $allRecords->count(),
				"aaData" => $data
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
