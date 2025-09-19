<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\User;
	use App\Models\UserWallet;
	use App\Models\Billing;
	use Auth,File,DB,Helper;
	use Illuminate\Support\Facades\Http;
	use Razorpay\Api\Api;
	
	class RechargeController extends Controller
	{   
		public function rechargeList()
		{
			$paymentStatuses = UserWallet::distinct()->pluck('transaction_status'); 
			return view('recharge.index', compact('paymentStatuses'));
		}
		
		public function rechargeListAjax(Request $request)
		{
			$draw = $request->get('draw');
			$start = $request->get("start");
			$limit = $request->get("length"); // Rows per page
			$columnIndex = $request->input('order.0.column'); // Column index
			$orderColumn = $request->input("columns.$columnIndex.data", 'id'); // Column name
			$orderDir = $request->input('order.0.dir', 'asc'); // Sorting direction
			$searchValue = $request->input('search'); // Search input

			$userId = Auth::id();
			$transactionType = $request->transaction_type;
			$transaction_status = $request->transaction_status;

			// Query with necessary joins
			$query = UserWallet::select('user_wallets.*', 'u.name')
				->join('users as u', 'u.id', '=', 'user_wallets.user_id')
				->where('user_wallets.user_id', $userId);

			if (!empty($transactionType)) {
				$query->where('user_wallets.transaction_type', $transactionType);
			}

			if ($transaction_status) {
				$query->where('user_wallets.transaction_status', $transaction_status);
			}

			// Apply search filter
			if (!empty($searchValue)) {
				$query->where(function ($q) use ($searchValue) {
					$q->where('u.name', 'LIKE', "%{$searchValue}%")
					  ->orWhere('user_wallets.amount', 'LIKE', "%{$searchValue}%")
					  ->orWhere('user_wallets.created_at', 'LIKE', "%{$searchValue}%")
					  ->orWhere('user_wallets.transaction_type', 'LIKE', "%{$searchValue}%");
				});
			}

			$totalData = $query->count();
			$filteredData = clone $query; // Clone query for filtering count

			// Apply sorting and pagination
			$values = $query->orderBy("user_wallets.$orderColumn", $orderDir)
				->offset($start)
				->limit($limit)
				->get();

			$data = [];
			$i = $start + 1;

			foreach ($values as $value) {
				$receipt = '';
				if (!empty($value->payment_receipt)) {
					$images = explode(',', $value->payment_receipt);
					foreach ($images as $image) {
						$url = url("storage/receipt/$image");
						$receipt .= '<a href="' . $url . '" target="_blank"><img src="' . $url . '" style="height:50px"></a>';
					}
				}

				$statusBadge = match ($value->transaction_status) {
					'Pending' => '<span class="badge badge-warning">Pending</span>',
					'Success' => '<span class="badge badge-success">Paid</span>',
					default => '<span class="badge badge-danger">' . $value->transaction_status . '</span>',
				};

				$data[] = [
					'id' => $i++,
					'name' => $value->name,
					'transaction_type' => $value->transaction_type,
					'amount' => config('setting.currency') . $value->amount,
					'payment_receipt' => $receipt,
					'note' => $value->note,
					'status' => $statusBadge,
					'created_at' => date('d M Y', strtotime($value->created_at)),
				];
			}

			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $totalData,
				"iTotalDisplayRecords" => $filteredData->count(),
				"aaData" => $data
			]);
		}
 
		
		public function rechargeListAdmin()
		{
			return view('recharge.recharge-history');
		}
		
		public function rechargeListAjaxAdmin(Request $request)
		{
			$draw = $request->get('draw');
			$start = $request->get('start');
			$limit = $request->get('length');
			$search = $request->input('search');
			
			$columnIndex = $request->get('order')[0]['column'];
			$order = $request->get('columns')[$columnIndex]['data'];
			$dir = $request->get('order')[0]['dir'];

			$user = Auth::user();
			
			$query = UserWallet::with('user:id,name,staff_id')
				->whereHas('user')
				->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
				->when($user->role === 'staff', fn($q) => $q->whereHas('user', fn($q) => $q->where('staff_id', $user->id)));
			
			$totalData = $query->count();

			if (!empty($search)) {
				$query->where(function ($q) use ($search) {
					$q->whereHas('user', fn($q) => $q->where('name', 'LIKE', "%{$search}%"))
					  ->orWhere('amount', 'LIKE', "%{$search}%")
					  ->orWhere('created_at', 'LIKE', "%{$search}%")
					  ->orWhere('transaction_type', 'LIKE', "%{$search}%")
					  ->orWhere('transaction_status', 'LIKE', "%{$search}%");
				});
			}
			
			$totalFiltered = $query->count();

			$values = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();
			
			$data = [];
			foreach ($values as $index => $value) {
				$receipt = collect(explode(',', $value->payment_receipt))->map(fn($image) => '<a href="' . url('storage/receipt', $image) . '" target="_blank"><img src="' . url('storage/receipt', $image) . '" style="height:50px"></a>')->implode(' ');

				$status = match (strtolower($value->transaction_status)) {
					'pending' => '<span class="badge badge-warning">Pending</span>',
					'success' => '<span class="badge badge-success">Success</span>',
					default => '<span class="badge badge-warning">' . $value->transaction_status . '</span>'
				};
				
				$data[] = [
					'id' => $start + $index + 1,
					'name' => $value->user->name ?? 'N/A',
					'transaction_type' => $value->transaction_type,
					'amount' => config('setting.currency') . $value->amount,
					'payment_receipt' => $receipt,
					'note' => $value->note,
					'status' => $status . '<p>' . $value->reject_note . '</p>',
					'created_at' => $value->created_at->format('d M Y'),
					'action' => '<button type="button" data-id="' . $value->id . '" data-reject_note="' . $value->reject_note . '" data-status="' . $value->status . '" class="btn-main-1" onclick="approvedRequest(this);">Approve Request</button>'
				];
			}

			return response()->json([
				'draw' => intval($draw),
				'iTotalRecords' => $totalData,
				'iTotalDisplayRecords' => $totalFiltered,
				'aaData' => $data
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
	        if($request->amount < 200)
			{
				return back()->with('error','The amount should be place 200 or greater than 200.');  
			}  
			$user_id = Auth::user()->id;
			$amount = $_POST['amount'];
			if($request->transaction_type == "Online")
			{
				$working_key='633F33CE6FBB1940116F503F0A5342E8';//Shared by CCAVENUES
				$access_code='AVXD94KG18CN96DXNC';//Shared by CCAVENUES
				$merchant_data='';
				$data = $request->except('_token','tid','merchant_id','order_id','currency','redirect_url','cancel_url','language','merchant_param5');
				
				$data['user_id'] = $user_id;
    			$data['status'] = 1;
    			$data['amount'] = $amount;
    			$data['order_id']=$_POST['order_id'];
    			$data['transaction_type'] = 'Online';
    			$data['created_at'] = date('Y-m-d H:i:s');
    			$data['updated_at'] = date('Y-m-d H:i:s');
    			$id = 	UserWallet::insertGetId($data); 
                $_POST['merchant_param2'] = $id;
    			
				foreach ($_POST as $key => $value){
					$merchant_data.=$key.'='.$value.'&';
				}
				 
				
				$encrypted_data= $this->encrypt($merchant_data,$working_key); // Method for encrypting the data.
				
				echo '<form method="post" name="redirect" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction">  
				<input type=hidden name=encRequest value='.$encrypted_data.'>
				<input type=hidden name=access_code value='.$access_code.'>  
				</form><script language="javascript">document.redirect.submit();</script>'; 
			}
			else
			{
				
				
				$data = $request->except('_token','tid','merchant_id','order_id','currency','redirect_url','cancel_url','language','merchant_param5','payment_receipt');
				
				$data['user_id'] = $user_id;
				$data['status'] = 0; 
				$data['created_at'] = date('Y-m-d H:i:s');
				$data['updated_at'] = date('Y-m-d H:i:s');
				
				$path_avatar = "storage/app/public/receipt";
				
				if(!File::isDirectory($path_avatar)){
					File::makeDirectory($path_avatar, 0777, true, true);
				}
				
				if(!empty($request->file('payment_receipt')))
				{
					$images = $request->file('payment_receipt');
					$string = "";
					foreach($images as $key => $image)
					{ 
						$getAvatar = time().rand(111111,999999) . '.' . $image->getClientOriginalExtension();
						$filename = $path_avatar."/".$getAvatar;
						$image->move($path_avatar, $getAvatar);  
						$string .= $getAvatar.',';
					}
					$data['payment_receipt'] = rtrim($string,',');
				} 
				
				try
				{
					UserWallet::insert($data); 
					return back()->with('success','The recharge amount request has been sent to admin.'); 
				}
				catch(\Exception $e)
				{  
					return back()->with('error',$e->getMessage()); 
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
				$users = User::whereId($user_id)->first();
				$name =  $users->name;
				$mobile = $users->mobile;
				$status = "Recharged";
				$url = "http://text.instavaluesms.in/V2/http-api.php?apikey=0puoldcpT5mRfdIe&senderid=STARSU&number=$mobile&message=Dear%20$name%20Your%20wallet%20has%20been%20$status%20with%20Rs%20$amount%20kindly%20check%20your%20panel%20for%20any%20assistance%20contact%20your%20Key%20manager/%20Raise%20ticket%20or%20write%20us%20on%20info@starexpressin.com&format=json";
				$response = Http::get($url); 
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
		
		/* public function rechargeWalletRazorpay(Request $request)
		{ 
        	$api = new Api('rzp_live_xr7VFPDqDbnJs6', 'QTceUGz3L2Gcqg1HxgIm4W4n');
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
			$data['payable_response'] = $payable_response;
			$data['created_at'] = now();
			$data['updated_at'] = now();
			
			$wallet = UserWallet::create($data);

        	if($order_status==="authorized" || $order_status==="captured")
        	{       	    	
				UserWallet::whereId($wallet->id)->update(['txn_number'=>$txn_number,'payable_response'=>$payable_response,'transaction_status'=>$order_status]);
				
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
        	   	UserWallet::whereId($wallet->id)->update(['payable_response'=>$payable_response,'transaction_status'=>$order_status]); 
				return response()->json(['status' => 'error', 'message' => 'Security Error. Illegal access detected']);
			} 
		} */
		
		public function rechargeWalletRazorpay(Request $request)
		{
			try {
				//$api = new Api('rzp_live_xr7VFPDqDbnJs6', 'QTceUGz3L2Gcqg1HxgIm4W4n');
				$api = new Api('rzp_live_kmwVBY7h35GqKE', 'g5wbHVGHWsIXGFrS0BmT21CD');
				$payment = $api->payment->fetch($request->txn_number);
				$order_status = $payment->status;

				$user = User::findOrFail($request->user_id);
				
				// Create wallet transaction
				$wallet = UserWallet::create([
					'user_id'           => $user->id,
					'status'            => 1,
					'txn_number'        => $request->txn_number,
					'amount'            => $request->amount,
					'transaction_type'  => 'Online',
					'payable_response'  => $request->payable_response,
					'created_at'        => now(),
					'updated_at'        => now(),
				]);

				// Update wallet transaction if payment is successful
				if (in_array($order_status, ['authorized', 'captured'])) {
					$wallet->update([
						'txn_number'         => $request->txn_number,
						'payable_response'   => $request->payable_response,
						'transaction_status' => $order_status
					]);

					// Update user's wallet balance
					$user->increment('wallet_amount', $request->amount);

					// Log transaction in billing
					Billing::create([
						'user_id'         => $user->id,
						'billing_type'    => "Recharge Wallet",
						'billing_type_id' => $wallet->id,
						'transaction_type'=> 'credit',
						'amount'          => $request->amount,
						'note'            => 'Recharge Wallet amount online.',
						'created_at'      => now(),
						'updated_at'      => now(),
					]);

					return response()->json([
						'status'  => 'success',
						'message' => 'The transaction has been charged and your transaction is successful'
					]);
				} else {
					// Handle failed transactions
					$wallet->update([
						'payable_response'   => $request->payable_response,
						'transaction_status' => $order_status
					]);

					return response()->json([
						'status'  => 'error',
						'message' => 'Security Error. Illegal access detected'
					]);
				}
			} catch (\Exception $e) {
				return response()->json([
					'status'  => 'error',
					'message' => 'Something went wrong: ' . $e->getMessage()
				]);
			}
		}

	}
