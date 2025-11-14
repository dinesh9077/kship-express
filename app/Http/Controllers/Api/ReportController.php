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
	use Carbon\Carbon;

	class ReportController extends Controller
	{ 
		use ApiResponse; 
		 
    	public function reportOrderList(Request $request)
		{ 
			$start = $request->post("offset");
			$limit = $request->post("limit");
 
			$search = $request->input('search') ?? '';
			 
			$currentDate = now()->toDateString(); 
			$user = Auth::user();
			$role = $user->role;
			$userId = $user->id;

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
			} else {
				$query->whereDate('order_date', $currentDate);
			}

			// Apply search filter
			if ($search) {
				$query->where(function ($q) use ($search) {
					$q->where('created_at', 'LIKE', "%{$search}%")
						->orWhereHas('customer', function ($q) use ($search) {
							$q->where('first_name', 'LIKE', "%{$search}%")
								->orwhere('last_name', 'LIKE', "%{$search}%")
								->orwhere('mobile', 'LIKE', "%{$search}%");
						})
						->orWhereHas('user', function ($q) use ($search) {
							$q->where('name', 'LIKE', "%{$search}%")
								->orwhere('email', 'LIKE', "%{$search}%")
								->orwhere('company_name', 'LIKE', "%{$search}%")
								->orwhere('mobile', 'LIKE', "%{$search}%");
						})
						->orWhereHas('warehouse', function ($q) use ($search) {
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

			$couriers = Order::select('courier_name')
				->distinct()
				->orderBy('courier_name');
			if ($user->role == 'user') {
				$couriers = $couriers->where('user_id', $user->id);
			}
			$couriers = $couriers->pluck('courier_name', 'courier_name')
			->filter()
			->toArray();

			$data = [
				'couriers' => $couriers,
				'orders' => $orders
			];
			return $this->successResponse($data, 'list fetched successfully.');
		}
		
		function reportOrderExport(Request $request)
		{
			return Excel::download(new OrdersExport($request), 'orders.xlsx');
		}
		     
		public function passbookReportList(Request $request)
		{
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
 
				// compute the index in runningBalancesDesc
				$balanceForRow = isset($runningBalancesDesc[$key]) ? $runningBalancesDesc[$key] : $openingBalance;
				$order = $value->billing_type === 'Order' ? ($value->order ?? null) : null;
				$courierLogo = $value->billing_type === 'Order' ? ($order->courier_id ? url("storage/courier-logo/{$order->courier_id}.png") : null) : null;
				$data[] = [
					'srno' => $key + 1,
					'created_at' => date('Y M d | h:i A', strtotime($value->created_at)),
					'name' => $value->user->name,
					'billing_type' => $value->billing_type, 
					'billing_type_id' => $value->billing_type_id, 
					'order_id' => $value->billing_type === 'Order' ? $value->order->id : null, 
					'order_prefix' => $value->billing_type === 'Order' ? $value->order->order_prefix : null,
					'courier_name' => $value->billing_type === 'Order' ? $value->order->courier_name : null,
					'courier_logo' => $courierLogo,
					'debit' => ($value->transaction_type == 'debit') ? $value->amount : "-",
					'credit' => ($value->transaction_type == 'credit') ? $value->amount : "-",
					'balance' => number_format($balanceForRow, 2),
					'note' => $value->note, 
				];
			}

			if ($openingBalance == 0) {
				$openingBalance = Billing::where('user_id', $request->user_id)->orderBy('id')->first()->amount ?? 0;
			}
			$responseData = [ 
				"opening_balance" => number_format($openingBalance, 2),
				"closing_balance" => number_format($closingBalance, 2),
				'data' => $data,
			];
			return $this->successResponse($responseData, 'list fetched successfully.');
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
 
			$searchValue = $request->get('search');
 
			$userId = Auth::id();  
			$query = UserWallet::with('user:id,name')
				->where('user_wallets.user_id', $userId);

			if ($request->filled('payment_mode')) {
				$query->where('user_wallets.transaction_type', $request->payment_mode);
			}

			if ($request->filled('status')) {
				$query->where('user_wallets.transaction_status', $request->status);
			}

			if ($request->filled('fromdate') && $request->filled('todate')) {
				$query->whereBetween('user_wallets.created_at', [
					Carbon::parse($request->fromdate)->startOfDay(),
					Carbon::parse($request->todate)->endOfDay()
				]);
			}

			// Search filter 
			if (!empty($searchValue)) {
				$query->where(function ($q) use ($searchValue) {
					$q->whereHas('user', function ($q2) use ($searchValue) {
						$q2->where('name', 'LIKE', "%{$searchValue}%");
					})
					->orWhere('user_wallets.amount', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.created_at', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.order_id', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.txn_number', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.transaction_type', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.amount_type', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.pg_name', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.transaction_status', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.utr_no', 'LIKE', "%{$searchValue}%");
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
