<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\User;
	use App\Models\UserWallet;
	use App\Models\Billing;
	use Auth,File,DB,Helper;
	use Razorpay\Api\Api;
	use Illuminate\Support\Facades\Http;
	use App\Services\MasterService;
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
			$search_arr = $request->get('search');

			$transaction_type = $request->transaction_type;
			$status = $request->status;

			$columnIndex = $columnIndex_arr[0]['column'] ?? 0;
			$order = $columnName_arr[$columnIndex]['data'] ?? 'user_wallets.id';
			$dir = $order_arr[0]['dir'] ?? 'desc';

			$userId = Auth::id();

			// Base query
			$query = UserWallet::with('user:id,name')
				->where('user_wallets.user_id', $userId);

			if (!empty($transaction_type)) {
				$query->where('user_wallets.transaction_type', $transaction_type);
			}

			if (!empty($status)) {
				$query->where('user_wallets.transaction_status', $status);
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
					->orWhere('user_wallets.transaction_type', 'LIKE', "%{$searchValue}%");
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
				$receipt = '';
				if ($value->payment_receipt) {
					foreach (explode(',', $value->payment_receipt) as $image) {
						$receipt .= '<a href="'.url('storage/receipt',$image).'" target="_blank">
										<img src="'.url('storage/receipt',$image).'" style="height:50px">
									 </a>';
					}
				} 

				$data[] = [
					'id' => $i++,
					'name' => $value->user->name ?? '',
					'transaction_type' => $value->transaction_type,
					'amount' => config('setting.currency').$value->amount,
					'payment_receipt' => $receipt,
					'order_id' => $value->order_id,
					'txn_number' => $value->txn_number,
					'note' => $value->note,
					'status' => $value->transaction_status == "Paid" ? '<span class="badge badge-success">Paid</span>' : '<span class="badge badge-warning">Pending</span>',
					'created_at' => $value->created_at->format('d M Y'),
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
			return view('recharge.recharg_history');
		}
		
		public function rechargeListAjaxAdmin(Request $request)
		{
			$draw = $request->get('draw');
			$start = $request->get("start");
			$limit = $request->get("length");

			$columnIndex = $request->input('order.0.column', 0);
			$order = $request->input("columns.$columnIndex.data", 'user_wallets.id');
			$dir = $request->input('order.0.dir', 'desc');

			$role = Auth::user()->role;
			$userId = Auth::id();

			// Base query with user relation
			$query = UserWallet::with('user:id,name');

			if ($role != 'admin') {
				$query->where('user_wallets.user_id', $userId);
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
					->orWhere('user_wallets.transaction_type', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.txn_number', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.order_id', 'LIKE', "%{$searchValue}%");
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
				// Payment receipts
				$receipt = '';
				if ($value->payment_receipt) {
					foreach (explode(',', $value->payment_receipt) as $image) {
						$receipt .= '<a href="'.url('storage/receipt', $image).'" target="_blank">
										<img src="'.url('storage/receipt', $image).'" style="height:50px">
									 </a>';
					}
				}
 
				$array = [
					'id' => $i++,
					'name' => $value->user->name ?? '', 
					'amount' => config('setting.currency') . $value->amount,
					'order_id' => $value->order_id,
					'txn_number' => $value->txn_number,
					'status' => $value->transaction_status == "Paid" ? '<span class="badge badge-success">Paid</span>' : '<span class="badge badge-warning">Pending</span>',
					'created_at' => $value->created_at->format('d M Y')
				];
				$array['action'] = "";
				if($value->transaction_status != "Paid")
				{	
					$array['action'] = '<button type="button" data-id="'.$value->id.'" data-reject_note="'.($value->reject_note ?? '').'" data-status="'.$value->status.'" class="btn-main-1" onclick="approvedRequest(this);">Approve Request</button>';
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
							'note'             => 'Recharge Wallet amount approved by admin.',
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
						'note' => 'Recharge Wallet amount online.',
					]);

					// Update UserWallet record if needed
					$userWallet->update([
						'transaction_status' => 'Paid',
						'txn_number' => $request->txn_id ?? null,
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