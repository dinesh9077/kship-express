<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\User;
	use App\Models\UserWallet;
	use App\Models\Billing;
	use Auth,File,DB,Helper;
	use Razorpay\Api\Api;
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

			if ($status != 5) {
				$query->where('user_wallets.status', $status);
			}

			// Clone query for total count
			$totalData = (clone $query)->count();

			// Search filter
			$searchValue = $request->input('search.value');
			if (!empty($searchValue)) {
				$query->where(function ($q) use ($searchValue) {
					$q->whereHas('user', function ($q2) use ($searchValue) {
						$q2->where('name', 'LIKE', "%{$searchValue}%");
					})
					->orWhere('user_wallets.amount', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.created_at', 'LIKE', "%{$searchValue}%")
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

				$statusHtml = match($value->transaction_status) {
					'Pending' => '<span class="badge badge-warning">Pending</span>',
					'Success' => '<span class="badge badge-success">Paid</span>',
					default   => '<span class="badge badge-danger">'.$value->transaction_status.'</span>',
				};

				$data[] = [
					'id' => $i++,
					'name' => $value->user->name ?? '',
					'transaction_type' => $value->transaction_type,
					'amount' => config('setting.currency').$value->amount,
					'payment_receipt' => $receipt,
					'note' => $value->note,
					'status' => $statusHtml,
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
			$searchValue = $request->input('search.value');
			if (!empty($searchValue)) {
				$query->where(function ($q) use ($searchValue) {
					$q->whereHas('user', function ($q2) use ($searchValue) {
						$q2->where('name', 'LIKE', "%{$searchValue}%");
					})
					->orWhere('user_wallets.amount', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.created_at', 'LIKE', "%{$searchValue}%")
					->orWhere('user_wallets.transaction_type', 'LIKE', "%{$searchValue}%");
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

				// Status badge
				$status = match($value->transaction_status) {
					'Pending' => '<span class="badge badge-warning">Pending</span>',
					'Success' => '<span class="badge badge-success">Paid</span>',
					default => '<span class="badge badge-danger">'.$value->transaction_status.'</span>',
				};
				$status .= '<p>'.($value->reject_note ?? '').'</p>';

				$data[] = [
					'id' => $i++,
					'name' => $value->user->name ?? '',
					'transaction_type' => $value->transaction_type,
					'amount' => config('setting.currency') . $value->amount,
					'payment_receipt' => $receipt,
					'note' => $value->note,
					'status' => $status,
					'created_at' => $value->created_at->format('d M Y'),
					'action' => '<button type="button" data-id="'.$value->id.'" data-reject_note="'.($value->reject_note ?? '').'" data-status="'.$value->status.'" class="btn-main-1" onclick="approvedRequest(this);">Approve Request</button>',
				];
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
			$id = $request->id;
			$status = $request->status;
			$reject_note = $request->reject_note;
			
			$data = [];
			$data['status'] = $status;
			if($status == 0)
			{
				UserWallet::whereId($id)->update($data);
				return back()->with('success','The payment status has been change to pending.'); 
			}
			if($status == 1)
			{  
				$userwallt = UserWallet::whereId($id)->first();
				 
				$w_amount = User::whereId($userwallt->user_id)->first()->wallet_amount;
				
				$wallet_amount = $w_amount + $userwallt->amount;
				User::whereId($userwallt->user_id)->update(['wallet_amount'=>$wallet_amount]);
				
				Billing::insert([
				'user_id'=>$userwallt->user_id,
				'billing_type'=>"Recharge Wallet",
				'billing_type_id'=>$id,
				'transaction_type'=>'credit',
				'amount'=>$userwallt->amount,
				'note'=>'Recharge Wallet amount approve by admin.',
				'created_at'=>date('Y-m-d H:i:s'),
				'updated_at'=>date('Y-m-d H:i:s'),
				]);
				
				UserWallet::whereId($id)->update($data);
				return back()->with('success','The payment status has been change to approved and credit to user wallet successfully.'); 
			}
			if($status == 2)
			{
				$data['reject_note'] = $reject_note;
				UserWallet::whereId($id)->update($data);
				return back()->with('success','The payment status has been change to rejected.'); 
			}
			 
		}
		  
		public function rechargeWalletStore(Request $request)
		{
			error_reporting(0);

			$user_id = Auth::user()->id;
			$amount = $request->amount;

			if ($request->transaction_type == "Online") {
				// Razorpay credentials
				$api_key = 'rzp_test_breqGHECxIJvjz';
				$api_secret = 'mCIqxNi2BUC4UZtY7gifUZUj';
				
				// Initialize Razorpay API
				$api = new Api($api_key, $api_secret);

				// Create order
				$orderData = [
					'receipt'         => rand(11111111, 99999999),
					'amount'          => $amount * 100, // Amount in paise
					'currency'        => 'INR',
					'payment_capture' => 1 // Auto capture
				];
				

				$razorpayOrder = $api->order->create($orderData);
				 
				// Prepare data for database
				$data = $request->except('_token', 'tid', 'merchant_id', 'order_id', 'currency', 'redirect_url', 'cancel_url', 'language', 'merchant_param5');
				$data['user_id'] = $user_id;
				$data['status'] = 1;
				$data['amount'] = $amount;
				$data['order_id'] = $razorpayOrder->id;
				$data['transaction_type'] = 'Online';
				$data['created_at'] = date('Y-m-d H:i:s');
				$data['updated_at'] = date('Y-m-d H:i:s');
				$id = UserWallet::insertGetId($data);

				// Return Razorpay order details to the frontend
				return response()->json([
					'order_id' => $razorpayOrder->id,
					'amount' => $razorpayOrder->amount,
					'currency' => $razorpayOrder->currency,
					'key' => $api_key,
				]);
			} else {
				$data = $request->except('_token', 'tid', 'merchant_id', 'order_id', 'currency', 'redirect_url', 'cancel_url', 'language', 'merchant_param5', 'payment_receipt');
				$data['user_id'] = $user_id;
				$data['status'] = 0;
				$data['created_at'] = date('Y-m-d H:i:s');
				$data['updated_at'] = date('Y-m-d H:i:s');

				$path_avatar = "storage/app/public/receipt";

				if (!File::isDirectory($path_avatar)) {
					File::makeDirectory($path_avatar, 0777, true, true);
				}

				if (!empty($request->file('payment_receipt'))) {
					$images = $request->file('payment_receipt');
					$string = "";
					foreach ($images as $key => $image) {
						$getAvatar = time() . rand(111111, 999999) . '.' . $image->getClientOriginalExtension();
						$filename = $path_avatar . "/" . $getAvatar;
						$image->move($path_avatar, $getAvatar);
						$string .= $getAvatar . ',';
					}
					$data['payment_receipt'] = rtrim($string, ',');
				}

				try {
					UserWallet::insert($data);
					return back()->with('success', 'The recharge amount request has been sent to admin.');
				} catch (\Exception $e) {
					return back()->with('error', $e->getMessage());
				}
			}
		}
		
		public function encrypt($plainText,$key)
		{
			$key = $this->hextobin(md5($key));
			$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
			$openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
			$encryptedText = bin2hex($openMode);
			return $encryptedText;
		}
		
		/*
			* @param1 : Encrypted String
			* @param2 : Working key provided by CCAvenue
			* @return : Plain String
		*/
		public function decrypt($encryptedText,$key)
		{
			$key = $this->hextobin(md5($key));
			$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
			$encryptedText = $this->hextobin($encryptedText);
			$decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
			return $decryptedText;
		}
		
		public function hextobin($hexString) 
		{ 
			$length = strlen($hexString); 
			$binString="";   
			$count=0; 
			while($count<$length) 
			{       
				$subString =substr($hexString,$count,2);           
				$packedString = pack("H*",$subString); 
				if ($count==0)
				{
					$binString=$packedString;
				} 
				
				else 
				{
					$binString.=$packedString;
				} 
				
				$count+=2; 
			} 
			return $binString; 
		}
		
		public function rechargeWalletResponse(Request $request)
		{
			error_reporting(0);
			
        	$workingKey='633F33CE6FBB1940116F503F0A5342E8';		//Working Key should be provided here.
        	$encResponse=$_POST["encResp"];			//This is the response sent by the CCAvenue Server
        	$rcvdString=$this->decrypt($encResponse,$workingKey);		//Crypto Decryption used as per the specified working key.
        	$order_status="";
        	$decryptValues=explode('&', $rcvdString);
        	$dataSize=sizeof($decryptValues);
		
        	for($i = 0; $i < $dataSize; $i++) 
        	{
        		$information=explode('=',$decryptValues[$i]); 
        		if($i==3)
        		{
        		    $order_status=$information[1];
				}
        		if($i==30)
        		{
        		    $user_id = $information[1];
				}
        		if($i==1)
        		{
        		    $txn_number = $information[1];
				}
        		if($i==10)
        		{
        		    $amount = $information[1];
				}
				if($i==27)
        		{
        		    $wallet_id = $information[1];
				}
			}
        	
            Auth::loginUsingId($user_id, true);   
            
        	if($order_status==="Success")
        	{     
    	    	$data['user_id'] = $user_id;
    			$data['status'] = 1;
    			$data['txn_number'] = $txn_number;
    			$data['amount'] = $amount;
    			$data['transaction_type'] = 'Online';
    			$data['payable_response'] = json_encode($decryptValues);
    			$data['created_at'] = date('Y-m-d H:i:s');
    			$data['updated_at'] = date('Y-m-d H:i:s');
				$payable_response = json_encode($decryptValues);
				// $id = UserWallet::insertGetId($data);
				UserWallet::whereId($wallet_id)->update(['txn_number'=>$txn_number,'payable_response'=>$payable_response,'transaction_status'=>$order_status]);
				$w_amount = User::whereId($user_id)->first()->wallet_amount;
				$wallet_amount = $w_amount + $amount;
				User::whereId($user_id)->update(['wallet_amount'=>$wallet_amount]);
				
				Billing::insert([
				'user_id'=>$user_id,
				'billing_type'=>"Recharge Wallet",
				'billing_type_id'=>$wallet_id,
				'transaction_type'=>'credit',
				'amount'=>$amount,
				'note'=>'Recharge Wallet amount online.',
				'created_at'=>date('Y-m-d H:i:s'),
				'updated_at'=>date('Y-m-d H:i:s'),
				]);
        		return redirect()->route('home')->with('success','The transaction has been charged and your transaction is successful');  
			}
        	else if($order_status==="Aborted")
        	{ 
        	    UserWallet::whereId($wallet_id)->update(['payable_response'=>$payable_response,'transaction_status'=>$order_status]);
        		return redirect()->route('home')->with('error','The transaction has been cancel by user'); 
			}
        	else if($order_status==="Failure")
        	{        
        	    UserWallet::whereId($wallet_id)->update(['payable_response'=>$payable_response,'transaction_status'=>$order_status]);
        		return redirect()->route('home')->with('error','The transaction has been declined.'); 
			}
        	else
        	{   
        	   	UserWallet::whereId($wallet_id)->update(['payable_response'=>$payable_response,'transaction_status'=>$order_status]);
        		return redirect()->route('home')->with('error','Security Error. Illegal access detected');  
			} 
		}

		public function rechargeWalletRazorpay(Request $request)
		{
        	$api = new Api('rzp_live_dc6C4IBvihVRTV','V8fCcj5USK2u9521mRZYvrVf');
			$payment = $api->payment->fetch($request->txn_number);
			$order_status = $payment->status;
			$payable_response = $request->payable_response;
            $user_id = $request->user_id;  
			$txn_number = $request->txn_number;
			$amount = $request->amount;
            $data['user_id'] = $user_id;
    			$data['status'] = 1;
    			$data['txn_number'] = $txn_number;
    			$data['amount'] = $amount;
    			$data['transaction_type'] = 'Online';
    			$data['payable_response'] = json_encode($payable_response);
    			$data['created_at'] = date('Y-m-d H:i:s');
    			$data['updated_at'] = date('Y-m-d H:i:s');
				$wallet_id = UserWallet::insertGetId($data);

        	if($order_status==="authorized" || $order_status==="captured")
        	{       	    	
				UserWallet::whereId($wallet_id)->update(['txn_number'=>$txn_number,'payable_response'=>$payable_response,'transaction_status'=>$order_status]);
				$w_amount = User::whereId($user_id)->first()->wallet_amount;
				$wallet_amount = $w_amount + $amount;
				User::whereId($user_id)->update(['wallet_amount'=>$wallet_amount]);
				
				Billing::insert([
				'user_id'=>$user_id,
				'billing_type'=>"Recharge Wallet",
				'billing_type_id'=>$wallet_id,
				'transaction_type'=>'credit',
				'amount'=>$amount,
				'note'=>'Recharge Wallet amount online.',
				'created_at'=>date('Y-m-d H:i:s'),
				'updated_at'=>date('Y-m-d H:i:s'),
				]);
        		
				return response()->json(['status' => 'success', 'message' => 'The transaction has been charged and your transaction is successful']);  
			}
        	else
        	{   
        	   	UserWallet::whereId($wallet_id)->update(['payable_response'=>$payable_response,'transaction_status'=>$order_status]);
        		
				return response()->json(['status' => 'error', 'message' => 'Security Error. Illegal access detected']);
			} 
		}
	}
