<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\User;
	use App\Models\UserWallet;
	use App\Models\Billing;
	use Auth, Hash,DB,Helper;
	use Razorpay\Api\Api;
	use Illuminate\Support\Facades\Http;
	use App\Services\MasterService;
	use Carbon\Carbon;
	class RechargeController extends Controller
	{   
		public function rechargeList()
		{
			return view('recharge.index');
		}
		
		public function rechargeListAjax(Request $request)
		{
			$draw = $request->get('draw');
			$start = $request->get("start");
			$limit = $request->get("length"); // Rows per page

			$columnIndex_arr = $request->get('order');
			$columnName_arr = $request->get('columns');
			$order_arr = $request->get('order'); 
 

			$columnIndex = $columnIndex_arr[0]['column'] ?? 0;
			$order = $columnName_arr[$columnIndex]['data'] ?? 'user_wallets.id';
			$dir = $order_arr[0]['dir'] ?? 'desc';

			$userId = Auth::id();

			// Base query
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

			// Clone query for total count
			$totalData = (clone $query)->count();

			// Search filter
			$searchValue = $request->input('search') ?? $request->input('search.value') ?? '';
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

			$totalFiltered = $query->count();

			// Fetch paginated data
			$values = $query->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			$data = [];
			$i = $start + 1;
			foreach ($values as $value) {
				// $receipt = '';
				// if ($value->payment_receipt) {
				// 	foreach (explode(',', $value->payment_receipt) as $image) {
				// 		$receipt .= '<a href="'.url('storage/receipt',$image).'" target="_blank">
				// 						<img src="'.url('storage/receipt',$image).'" style="height:50px">
				// 					 </a>';
				// 	}
				// } 

				$data[] = [
					'id' => $i++,
					'created_at' => $value->created_at->format('d M Y'),
					'amount_type' => $value->amount_type ? ucwords($value->amount_type) : 'N/A',
					'amount' => $value->amount,
					'order_id' => $value->order_id ?? 'N/A',
					'txn_number' => $value->txn_number ?? 'N/A',
					'utr_no' => $value->utr_no ?? 'N/A',
					'payment_mode' => $value->transaction_type,
					'pg_name' => $value->pg_name ?? 'N/A',  
					'transaction_status' => $value->transaction_status == "Paid"
					? '<span class="badge badge-success">Paid</span>'
					: ($value->transaction_status == "Rejected"
						? '<span class="badge badge-danger">Rejected</span><br><small>Reject Note: ' . e($value->reject_note) . '</small>'
						: '<span class="badge badge-warning">Pending</span>'
					),
				];
			}

			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $totalData,
				"iTotalDisplayRecords" => $totalFiltered,
				"aaData" => $data
			]);
		}
 
		public function rechargeListAdmin()
		{
			$users = User::where('role', 'user')->where('kyc_status', '!=', 0)->orderBy('name')->get();
			return view('recharge.recharg_history', compact('users'));
		}
		
		public function rechargeListAjaxAdmin(Request $request)
		{
			$draw = $request->get('draw');
			$start = $request->get("start");
			$limit = $request->get("length");

			$columnIndex = $request->input('order.0.column', 0);
			$order = $request->input("columns.$columnIndex.data", 'user_wallets.id');
			$dir = $request->input('order.0.dir', 'desc');
  
			// Base query with user relation
			$query = UserWallet::with('user:id,name');
 
			if ($request->filled('user_id')) {
				$query->where('user_wallets.user_id', $request->user_id);
			}

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

			// Clone for total count
			$totalData = (clone $query)->count();

			// Search filter
			$searchValue = $request->input('search') ?? $request->input('search.value') ?? '';
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
					->orWhere('user_wallets.utr_no', 'LIKE', "%{$searchValue}%")
					->orwhereHas('user', function ($q) use ($searchValue) {
						$q->where('name', 'LIKE', "%{$searchValue}%");
					});
				});
			}

			$totalFiltered = $query->count();

			// Fetch paginated data
			$values = $query->offset($start)
				->limit($limit)
				->orderBy('user_wallets.' . $order, $dir)
				->get();

			$data = [];
			$i = $start + 1;

			foreach ($values as $value) { 
				$array = [
					'id' => $i++,
					'username' => $value->user->name ?? 'N/A',
					'created_at' => $value->created_at->format('d M Y'),
					'amount_type' => $value->amount_type ? ucwords($value->amount_type) : 'N/A',
					'amount' => $value->amount,
					'order_id' => $value->order_id ?? 'N/A',
					'txn_number' => $value->txn_number ?? 'N/A',
					'utr_no' => $value->utr_no ?? 'N/A',
					'payment_mode' => $value->transaction_type,
					'pg_name' => $value->pg_name ?? 'N/A',
					'transaction_status' =>
					$value->transaction_status == "Paid"
					? '<span class="badge badge-success">Paid</span>'
					: ($value->transaction_status == "Rejected"
						? '<span class="badge badge-danger">Rejected</span><br><small>Reject Note: ' . e($value->reject_note) . '</small>'
						: '<span class="badge badge-warning">Pending</span>'
					),
				];


				$array['action'] = "";
				if($value->transaction_status == "Pending")
				{	
					$array['action'] = '<button type="button" data-id="'.$value->id.'" data-reject_note="'.($value->reject_note ?? '').'" data-status="'.$value->transaction_status.'" class="new-submit-thmebtn" onclick="approvedRequest(this);">Approve Request</button>';
				} 
				
				$data[] = $array;
			}

			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $totalData,
				"iTotalDisplayRecords" => $totalFiltered,
				"aaData" => $data
			]);
		}
 
		public function rechargeWalletAction(Request $request)
		{
			$validated = $request->validate([ 
				'status' => "required|in:Pending,Paid,Rejected",
				'passkey' => "required"
			]);

			$user = Auth::user();
			// Hash::check returns true when plain text matches hashed password
			if (!Hash::check($request->input('passkey'), $user->password)) { 
				return back()->with('error', 'Passkey does not match.');
			}

			$userWallet = UserWallet::with('user')->findOrFail($request->id); 
			try {
				DB::transaction(function () use ($request, $userWallet) {
					if ($request->status === "Pending") {
						$userWallet->update(['transaction_status' => 'Pending']);
					}

					if ($request->status === "Paid") {
						// Credit to wallet
						$userWallet->user->increment('wallet_amount', $userWallet->amount);

						// Add billing entry
						Billing::create([
							'user_id'          => $userWallet->user_id,
							'billing_type'     => "Recharge Wallet",
							'billing_type_id'  => $userWallet->id,
							'transaction_type' => 'credit',
							'amount'           => $userWallet->amount,
							'note' => 'The amount has been credited and approved by the admin.' 
						]);

						$userWallet->update(['transaction_status' => 'Paid']);
					}

					if ($request->status === "Rejected") {
						$userWallet->update([
							'transaction_status'       => 'Rejected',
							'reject_note'  => $request->reject_note,
						]);
					}
				});

				return back()->with('success', "The payment status has been updated successfully.");

			} catch (\Exception $e) {
				return back()->with('error', "Error: " . $e->getMessage());
			}
		}
		  
		public function rechargeWalletStore(Request $request, MasterService $masterService)
		{  
			try{
				$userId = $request->user_id;
				$amount = $request->amount; 
				$response = $masterService->rechargeOrderCreate($amount);
				 
				if (!($response['success'] ?? false)) {
					$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response']['error'] ?? 'An error occurred.');
					return response()->json(['status' => 'error', 'msg' => $errorMsg]);
				}
				
				if ((isset($response['response']['message']) && $response['response']['message'] === "Failed"))
				{
					return response()->json(['status' => 'error', 'msg' => $response['response']['err'] ?? 'An error occurred.']);
				} 
			 
				if (empty($response['response']['data']))
				{
					return response()->json(['status' => 'error', 'msg' => 'something went wrong.']);
				} 
				
				$responseData = $response['response']['data'];
				  
				// Prepare data for database
				$data = $request->except('_token', 'payment_receipt');
				$data['user_id'] = $userId;
				$data['status'] = 1;
				$data['amount'] = $amount;
				$data['amount_type'] = "credit";
				$data['transaction_type'] = "QR Code (Intent)";
				$data['pg_name'] = "Razorpay";
				$data['order_id'] = $responseData['order_id'] ?? null; 
				$data['payable_response'] = $responseData ?? null; 
				$data['created_at'] = now();
				$data['updated_at'] = now();
				
				$wallet = UserWallet::create($data);

				// Return Razorpay order details to the frontend
				return response()->json([
					'status' => 'success', 
					'msg' => 'success',
					'order' => $responseData
				]);
			}
			catch(\Exception $e)
			{
				return response()->json(['status' => 'error', 'msg' => $e->getMessage()]); 
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
						'note' => "Payment received through Razorpay with UTR number: {$request->utr_no}."
					]);

					// Update UserWallet record if needed
					$userWallet->update([
						'transaction_status' => 'Paid',
						'txn_number' => $request->txn_id ?? null,
						'utr_no' => $request->utr_no ?? null,
					]);
				});

				return response()->json([
					'status' => 'success',
					'message' => 'The transaction has been charged and your wallet updated successfully.'
				]);

			} catch (\Exception $e) {
				return response()->json([
					'status' => 'error',
					'message' => $e->getMessage()
				], 500);
			}
		}
		 
	} 