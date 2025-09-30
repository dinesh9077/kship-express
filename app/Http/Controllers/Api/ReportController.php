<?php
	
	namespace App\Http\Controllers\Api;
	
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use App\Models\Order;
	use App\Models\OrderItem; 
	use App\Models\Billing; 
	use App\Models\Invoice;
	use App\Models\UserWallet;
	use App\Models\User;   
	use App\Exports\PendingStarOrderExport;
	use DB,Auth,File,Helper;
	use Illuminate\Support\Facades\Http;
	use App\Exports\OrdersExport;
	use Maatwebsite\Excel\Facades\Excel;
	use Barryvdh\DomPDF\Facade\Pdf;
	use Illuminate\Support\Facades\Storage; 
	use App\Exports\BillingInvoiceExport; 
	use App\Traits\ApiResponse;   

	class ReportController extends Controller
	{ 
		use ApiResponse; 
		 
    	public function reportOrderList(Request $request)
		{ 
			$start = $request->post("offset");
			$limit = $request->post("limit");
 
			$search = $request->input('search') ?? '';
			 
			$currentDate = now()->toDateString(); 
			$role = Auth::user()->role;
			$userId = Auth::id();

			// Base query with necessary joins
			$query = Order::with(['user:id,name,email,mobile,company_name', 'warehouse', 'customer', 'customerAddress', 'orderItems']);

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
 
			// Apply ordering, pagination, and fetch results
			$orders = $query->orderByDesc('id')
							->offset($start)
							->limit($limit)
							->get();

			return $this->successResponse($orders, 'list fetched successfully.');
		}
		
		function reportOrderExport(Request $request)
		{
			return Excel::download(new OrdersExport($request), 'orders.xlsx');
		}
		     
		public function passbookReportList(Request $request)
		{  
			$role = Auth::user()->role;
			$id = Auth::user()->id;

			$query = Billing::with(['user'])
				->when(in_array($role, ['user']), fn($q) => $q->where('billings.user_id', $id))
				->when(!in_array($role, ['user']) && $request->user_id, fn($q) => $q->where('billings.user_id', $request->user_id))
				->when($request->fromdate && $request->todate, fn($q) => $q->whereBetween('billings.created_at', [$request->fromdate, $request->todate]))
				->when($request->fromdate && !$request->todate, fn($q) => $q->whereDate('billings.created_at', $request->fromdate))
				->when($request->todate && !$request->fromdate, fn($q) => $q->whereDate('billings.created_at', $request->todate));

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

			foreach ($paginated as $key => $value) 
			{  
				$transactionTypeHtml = ($value->transaction_type == 'debit')
					? 'Debit'
					: 'Credit';
				
				$displayCurrentBalance = $currentBalance;
				
				$data[] = [
					'srno' => $key + 1,
					'name' => $value->user->name,
					'billing_detail' => $value->billing_type_id,
					'transaction_type' => $transactionTypeHtml,
					'debit' => ($value->transaction_type == 'debit') ? $value->amount : "-",
					'credit' => ($value->transaction_type == 'credit') ? $value->amount : "-",
					'balance' => number_format($displayCurrentBalance, 2),
					'note' => $value->note,
					'created_at' => date('Y M d | h:i A', strtotime($value->created_at)),
				]; 
				$currentBalance -= ($value->transaction_type == 'debit') ? -$value->amount : $value->amount;
			}
			return $this->successResponse($data, 'list fetched successfully.');
		}   
		 
		public function billingInvoiceList(Request $request)
		{ 
			$start = $request->post("offset");
			$limit = $request->post("limit"); 
			$search = $request->post("search"); 
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

			$invoices = $query
				->orderByDesc('id')
				->offset($start)
				->limit($limit)
				->get();
			return $this->successResponse($invoices, 'list fetched successfully.');
		}
		
		public function billingInvoicePdf($invoiceId)
		{
			try{
			
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
			catch(\Exception $e)
			{
				return $this->errorResponse('failed to download pdf generation.');
			}
		}
		
		 
		public function billingInvoiceExcel($invoiceId)
		{
			try{
				$invoice = Invoice::with(['orders.user'])->findOrFail($invoiceId);
				// Replace slashes to make a valid filename
				$sanitizedNumber = str_replace('/', '-', $invoice->invoice_number);
				$filename = 'invoice_' . $sanitizedNumber . '.xlsx';
		
				return Excel::download(new BillingInvoiceExport($invoice), $filename);
			}
			catch(\Exception $e)
			{
				return $this->errorResponse('failed to download excel generation.');
			}
		}
		
		public function rechargeList(Request $request)
		{ 
			$start = $request->get("offset");
			$limit = $request->get("limit"); // Rows per page
 
			$search = $request->get('search');

			$transaction_type = $request->transaction_type;
			$status = $request->status;
 
			$userId = Auth::id();  
			$query = UserWallet::with('user:id,name')
				->where('user_wallets.user_id', $userId);

			if (!empty($transaction_type)) {
				$query->where('user_wallets.transaction_type', $transaction_type);
			}

			if (!empty($status)) {
				$query->where('user_wallets.status', $status);
			}
  
			// Search filter 
			if (!empty($search)) {
				$query->where(function ($q) use ($search) {
					$q->whereHas('user', function ($q2) use ($search) {
						$q2->where('name', 'LIKE', "%{$search}%");
					})
					->orWhere('user_wallets.amount', 'LIKE', "%{$search}%")
					->orWhere('user_wallets.created_at', 'LIKE', "%{$search}%")
					->orWhere('user_wallets.transaction_type', 'LIKE', "%{$search}%");
				});
			} 
			
			// Fetch paginated data
			$values = $query->offset($start)
			->limit($limit)
			->orderByDesc('id')
			->get();
 
			return $this->successResponse($values, 'list fetch successfully.');
		}
	}
