<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Order;
	use App\Models\User;
	use App\Models\weightDescrepencyHistory;
	use App\Models\ExcessWeight;
	use App\Models\weightDescrepency;
	use App\Models\WeightFreeze;
	use App\Models\Billing;
	use File,Auth,DB,Helper;
	use Illuminate\Support\Facades\Http;
	class WeightController extends Controller
	{
		public function __construct()
		{
			$this->middleware('auth');
		}
		
		public function descrepenciesAll(Request $request)
		{	
			$users = User::whereStatus(1)->whereRole('user')->get(); 
			$orders = Order::with(['user', 'excessWeight', 'orderItems']) 
			->when($request->user_id, fn($query) => $query->where('user_id', $request->user_id))
			->when($request->weight_status, fn($query) => $query->where('weight_status', $request->weight_status))
			->when($request->weight_update_date, fn($query) => $query->whereDate('weight_update_date', $request->weight_update_date))
			->when($request->search, function ($query) use ($request) {
				$search = $request->search;
				$query->where(function ($q) use ($search) {
					$q->where('awb_number', 'LIKE', "%{$search}%")
					  ->orWhere('id', 'LIKE', "%{$search}%")
					  ->orWhere('order_prefix', 'LIKE', "%{$search}%");
				});
			})
			->whereNotNull('awb_number')
			->orderByDesc('id')
			->paginate(10);
 
			return view('weight.descrepensies.all_descrepencies', compact('users', 'orders'));
		}
		
		public function raiseExcessWeight(Request $request)
		{
			try {
				DB::beginTransaction(); // Start Transaction

				$data = $request->except('_token', 'upload_image'); 
				$timestamp = now(); // Get the current timestamp

				$data['user_id'] = Auth::id();
				$data['created_at'] = $timestamp;
				$data['updated_at'] = $timestamp;
				$data['status'] = 'New Descrepency';

				// Handle file upload
				if ($request->hasFile('upload_image')) {
					$file = $request->file('upload_image');
					$filePath = $file->store('uploads', 'public'); // Store file in 'uploads' directory
					$data['image_path'] = $filePath;
				}

				// Insert data into ExcessWeight table
				ExcessWeight::create($data);

				// Update order status and weight update date
				$order = Order::with('user')->find($request->order_id);
				
				// Decrement the user's wallet_amount by the excess charge
				$order->user->decrement('wallet_amount', $request->excess_charge);
				
            	Billing::insert([
					'user_id'=> $order->user_id,
					'billing_type'=> "Order",
					'billing_type_id'=> $order->id,
					'transaction_type'=> 'debit',
					'amount'=> $request->excess_charge,
					'note'=> 'Excess weight is apply with awb number : '.$order->awb_number,
					'created_at'=> now(),
					'updated_at'=> now(),
				]); 

				// Update the order's weight status and weight update date
				$order->update([
					'weight_status'      => 'Accepted',
					'weight_update_date' => $timestamp,
				]);

				// Insert history record
				/* WeightDescrepencyHistory::create([
					'order_id' => $request->order_id,
					'action_by' => config('setting.company_name'),
					'remarks' => 'Immediately Uploaded',
					'status_descrepency' => 'New Descrepency',
					'created_at' => $timestamp,
					'updated_at' => $timestamp
				]); */

				DB::commit(); // Commit Transaction

				return back()->with('success', 'Excess weight discrepancy has been raised successfully.');

			} catch (\Exception $e) {
				DB::rollBack(); // Rollback transaction in case of error
				return back()->with('error', 'An error occurred while raising excess weight: ' . $e->getMessage());
			}
		}
		
		public function weightHoistory($id)
		{
			$order = Order::whereId($id)->first();
			$weight_history = weightDescrepencyHistory::whereOrder_id($id)->orderBy('id','desc')->get();
			$view = view('weight.descrepensies.view_history',compact('weight_history'))->render();
			return response()->json(['view'=>$view,'header_msg'=>'Weight Discrepancy History for AWB: '.$order->awb_number]); 
		}
		
		public function weightAccepted($id)
		{
			$order = Order::whereId($id)->first();
			$excess_weight = ExcessWeight::whereOrder_id($id)->first();
			$excess_charge = $excess_weight->excess_charge;
			
			$userwalt = User::whereId($order->user_id)->first(); 
			$walletamunt = $userwalt->wallet_amount - $excess_charge;
			User::whereId($order->user_id)->update(['wallet_amount'=>$walletamunt]); 
			
			Billing::insert([
			'user_id'=>$order->user_id,
			'billing_type'=>"Order",
			'billing_type_id'=>$order->id,
			'transaction_type'=>'debit',
			'amount'=>$excess_charge,
			'note'=>'Excess weight is apply with awb number : '.$order->awb_number,
			'created_at'=>date('Y-m-d H:i:s'),
			'updated_at'=>date('Y-m-d H:i:s'),
			]);
			
			weightDescrepencyHistory::insert(['order_id'=>$id,'action_by'=>'Seller','remarks'=>'','status_descrepency'=>'Accepted','created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
			Order::whereId($id)->update(['weight_status'=>'Accepted','weight_update_date'=>date('Y-m-d H:i:s')]); 
			
			$users = User::whereId($order->user_id)->first();
			$name =  $users->name;
			$mobile = $users->mobile;
			$status = "Deducted";
			$amount = $walletamunt;
			$url = "http://text.instavaluesms.in/V2/http-api.php?apikey=0puoldcpT5mRfdIe&senderid=STARSU&number=$mobile&message=Dear%20$name%20Your%20wallet%20has%20been%20$status%20with%20Rs%20$amount%20kindly%20check%20your%20panel%20for%20any%20assistance%20contact%20your%20Key%20manager/%20Raise%20ticket%20or%20write%20us%20on%20info@starexpressin.com&format=json";
			$response = Http::get($url); 
			
			return response()->json(['status'=>'success','msg'=>'weight descrepency has been accepted']); 
		}
		
		public function weightbycourier(Request $request)
		{
			$id = $request->order_id;
			$status = $request->acton;
			
			$order = Order::whereId($id)->first();
			if($status == "Dispute Accepted by Courier")
			{
				
				$excess_weight = ExcessWeight::whereOrder_id($id)->first();
				$excess_charge = $excess_weight->excess_charge;
				
				$userwalt = User::whereId($order->user_id)->first(); 
				$walletamunt = $userwalt->wallet_amount - $excess_charge;
				User::whereId($order->user_id)->update(['wallet_amount'=>$walletamunt]); 
				
				Billing::insert([
				'user_id'=>$order->user_id,
				'billing_type'=>"Order",
				'billing_type_id'=>$order->id,
				'transaction_type'=>'debit',
				'amount'=>$excess_charge,
				'note'=>'Excess weight is apply with awb number : '.$order->awb_number,
				'created_at'=>date('Y-m-d H:i:s'),
				'updated_at'=>date('Y-m-d H:i:s'),
				]);
				
				weightDescrepencyHistory::insert(['order_id'=>$id,'action_by'=>'Courier','remarks'=>'','status_descrepency'=>$status,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
				
				$users = User::whereId($order->user_id)->first();
    			$name =  $users->name;
    			$mobile = $users->mobile;
    			$status = "Deducted";
    			$amount = $walletamunt;
    			$url = "http://text.instavaluesms.in/V2/http-api.php?apikey=0puoldcpT5mRfdIe&senderid=STARSU&number=$mobile&message=Dear%20$name%20Your%20wallet%20has%20been%20$status%20with%20Rs%20$amount%20kindly%20check%20your%20panel%20for%20any%20assistance%20contact%20your%20Key%20manager/%20Raise%20ticket%20or%20write%20us%20on%20info@starexpressin.com&format=json";
    			$response = Http::get($url); 
			}
			else
			{ 
				weightDescrepencyHistory::insert(['order_id'=>$id,'action_by'=>'Courier','remarks'=>'Reject, courier charged correctly as per seller content & qty.','status_descrepency'=>$status,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
			}
			Order::whereId($id)->update(['weight_status'=>$status,'weight_update_date'=>date('Y-m-d H:i:s')]); 
			return response()->json(['status'=>'success','msg'=>'weight descrepency has been accepted']); 
		}
		
		public function weightRejected($id)
		{
			$order = Order::whereId($id)->first();
			return view('weight.descrepensies.reject_form',compact('id','order'));
		}
		
		public function rejectFormStore(Request $request)
		{ 
			$data = $request->except('_token','product_image','length_image','width_image','height_image','weight_image','label_image');
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$path_avatar = "storage/app/public/weight_descrepency/".$request->order_id;
			
			if(!File::isDirectory($path_avatar)){
				File::makeDirectory($path_avatar, 0777, true, true);
			}
			
			if(!empty($request->file('product_image')))
			{
				$images = $request->file('product_image');
				$string = "";
				foreach($images as $key => $image)
				{ 
					$getAvatar = time().rand(111111,999999) . '.' . $image->getClientOriginalExtension();
					$filename = $path_avatar."/".$getAvatar;
					$image->move($path_avatar, $getAvatar);  
					$string .= $getAvatar.',';
				}
				$data['product_image'] = rtrim($string,',');
			} 
			
			if(!empty($request->file('length_image')))
			{
				$photo_image = $request->file('length_image');  
				$getAvatar = time().rand(111111,999999) . '.' . $photo_image->getClientOriginalExtension();
				$filename = $path_avatar."/".$getAvatar;
				$photo_image->move($path_avatar, $getAvatar);  
				$data['length_image'] = $getAvatar;
			}  
			if(!empty($request->file('width_image')))
			{
				$photo_image = $request->file('width_image');  
				$getAvatar = time().rand(111111,999999) . '.' . $photo_image->getClientOriginalExtension();
				$filename = $path_avatar."/".$getAvatar;
				$photo_image->move($path_avatar, $getAvatar);  
				$data['width_image'] = $getAvatar;
			}   
			if(!empty($request->file('height_image')))
			{
				$photo_image = $request->file('height_image');  
				$getAvatar = time().rand(111111,999999) . '.' . $photo_image->getClientOriginalExtension();
				$filename = $path_avatar."/".$getAvatar;
				$photo_image->move($path_avatar, $getAvatar);  
				$data['height_image'] = $getAvatar;
			}  
			if(!empty($request->file('weight_image')))
			{
				$photo_image = $request->file('weight_image');  
				$getAvatar = time().rand(111111,999999) . '.' . $photo_image->getClientOriginalExtension();
				$filename = $path_avatar."/".$getAvatar;
				$photo_image->move($path_avatar, $getAvatar);  
				$data['weight_image'] = $getAvatar;
			}  
			if(!empty($request->file('label_image')))
			{
				$photo_image = $request->file('label_image');  
				$getAvatar = time().rand(111111,999999) . '.' . $photo_image->getClientOriginalExtension();
				$filename = $path_avatar."/".$getAvatar;
				$photo_image->move($path_avatar, $getAvatar);  
				$data['label_image'] = $getAvatar;
			}  
			
			weightDescrepency::insert($data);
			weightDescrepencyHistory::insert(['order_id'=>$request->order_id,'action_by'=>'Seller','remarks'=>'','status_descrepency'=>'Rejected','created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
			Order::whereId($request->order_id)->update(['weight_status'=>'Rejected']);
			return redirect()->route('weight.descripencies')->with('success','weight descrepency has been successfully added.');
		}
		
		public function weightview_image($id)
		{ 
			$weight_descrepencies = weightDescrepency::whereId($id)->orderBy('id','desc')->first();
			$view = view('weight.descrepensies.view_image',compact('weight_descrepencies'))->render();
			return response()->json(['view'=>$view,'header_msg'=>'Weight Discrepancy Image']); 
		}
		
		
		public function autoAccepted()
		{
			
			$excess_weights = ExcessWeight::select('excess_weights.*','o.id as order_id','o.user_id','o.awb_number')->join('orders as o','o.id','=','excess_weights.order_id')
			->where('o.weight_status','New Descrepency')->get();
			
			if(count($excess_weights) > 0)
			{
				foreach($excess_weights  as $excess_weight)
				{
				 
					$fourDaysafetr = strtotime(date('Y-m-d', strtotime('3 days', strtotime($excess_weight->created_at))));
					
					// Get the current date
					$currentDate = strtotime(date('Y-m-d'));
					 
					// Compare the current date with 2 days before the target date
					if ($currentDate == $fourDaysafetr) 
					{ 
						$excess_charge = $excess_weight->excess_charge; 
						$userwalt = User::whereId($excess_weight->user_id)->first(); 
						$walletamunt = $userwalt->wallet_amount - $excess_charge;
						User::whereId($excess_weight->user_id)->update(['wallet_amount'=>$walletamunt]); 
						
						Billing::insert([
						'user_id'=>$excess_weight->user_id,
						'billing_type'=>"Order",
						'billing_type_id'=>$excess_weight->order_id,
						'transaction_type'=>'debit',
						'amount'=>$excess_charge,
						'note'=>'Excess weight is apply with awb number : '.$excess_weight->awb_number,
						'created_at'=>date('Y-m-d H:i:s'),
						'updated_at'=>date('Y-m-d H:i:s'),
						]);
						
						weightDescrepencyHistory::insert(['order_id'=>$excess_weight->order_id,'action_by'=>'Seller','remarks'=>'','status_descrepency'=>'Auto Accepted','created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
						Order::whereId($excess_weight->order_id)->update(['weight_status'=>'Auto Accepted','weight_update_date'=>date('Y-m-d H:i:s')]); 
						
						$users = User::whereId($excess_weight->user_id)->first();
            			$name =  $users->name;
            			$mobile = $users->mobile;
            			$status = "Deducted";
            			$amount = $walletamunt;
            			$url = "http://text.instavaluesms.in/V2/http-api.php?apikey=0puoldcpT5mRfdIe&senderid=STARSU&number=$mobile&message=Dear%20$name%20Your%20wallet%20has%20been%20$status%20with%20Rs%20$amount%20kindly%20check%20your%20panel%20for%20any%20assistance%20contact%20your%20Key%20manager/%20Raise%20ticket%20or%20write%20us%20on%20info@starexpressin.com&format=json";
            			$response = Http::get($url); 
					}
				}
			}
			return response()->json(['status'=>'success','msg'=>'weight descrepency has been accepted']); 
		}
		
		public function index(Request $request)
		{ 
			// Initialize the Order query with necessary relationships and columns
			$ordersQuery = Order::with(['user', 'excessWeight']);  
   
			if ($request->filled('weight_update_date')) {
				$ordersQuery->whereDate('weight_update_date', $request->weight_update_date);
			}

			if ($request->filled('search')) {
				$searchTerm = $request->search;
				$ordersQuery->where(function ($query) use ($searchTerm) {
					$query->where('awb_number', 'LIKE', "%{$searchTerm}%")
						->orWhere('id', 'LIKE', "%{$searchTerm}%")
						->orWhere('order_prefix', 'LIKE', "%{$searchTerm}%");
				});
			}

			// Exclude orders without an AWB number
			$ordersQuery->whereNotNull('awb_number')->where('user_id', Auth::id());
			
			$ordersQuery->whereHas('excessWeight');

			// Paginate the results
			$orders = $ordersQuery->orderByDesc('id')->paginate(10);

			// Return the view with the users and orders data
			return view('weight.descrepensies.index', compact('orders'));
		}

		
		public function freezeList(Request $request)
		{	 
			$weightfreeze = WeightFreeze::select('weight_freezes.*','o.weight_freeze_status','o.packaging_id')
			->join('orders as o','o.id','=','weight_freezes.order_id');
				$role = Auth::user()->role;
			$id = Auth::user()->id;
			if($role != "admin") 
			{
				$weightfreeze->where('o.user_id',$id);
			}
			if($request->search)
			{
				$search = $request->search;
				$weightfreeze = $weightfreeze->where(function ($query) use ($search) 
				{
					return $query->where('o.awb_number', 'LIKE',"%{$search}%")->orWhere('o.id', 'LIKE',"%{$search}%")->orWhere('o.order_prefix', 'LIKE',"%{$search}%");
				});
			}
			$weightfreezes = $weightfreeze->orderBy('weight_freezes.id','desc')->paginate(10);
		
			return view('weight.freeze.index',compact('weightfreezes'));
		}
		
		public function freezeAdd($id)
		{
			$weight_freeze = WeightFreeze::whereId($id)->first();
			$order = Order::whereId($weight_freeze->order_id)->first();  
			return view('weight.freeze.add',compact('order','weight_freeze'));
		}
		
		
		public function freezeStore(Request $request)
		{ 
			if(!Order::whereId($request->order_id)->exists())
			{
				return back()->with('error','Order id not exists');
			}
			$order = Order::whereId($request->order_id)->first();
			$data = $request->except('_token','product_image','length_image','width_image','height_image','weight_image','label_image','id');
			$data['user_id'] = $order->user_id;
			$data['freeze_status'] = 'Freezed';
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$path_avatar = "storage/app/public/weight_freeze/".$request->order_id;
			
			if(!File::isDirectory($path_avatar)){
				File::makeDirectory($path_avatar, 0777, true, true);
			}
			
			if(!empty($request->file('product_image')))
			{
				$images = $request->file('product_image');
				$string = "";
				foreach($images as $key => $image)
				{ 
					$getAvatar = time().rand(111111,999999) . '.' . $image->getClientOriginalExtension();
					$filename = $path_avatar."/".$getAvatar;
					$image->move($path_avatar, $getAvatar);  
					$string .= $getAvatar.',';
				}
				$data['product_image'] = rtrim($string,',');
			} 
			
			if(!empty($request->file('length_image')))
			{
				$photo_image = $request->file('length_image');  
				$getAvatar = time().rand(111111,999999) . '.' . $photo_image->getClientOriginalExtension();
				$filename = $path_avatar."/".$getAvatar;
				$photo_image->move($path_avatar, $getAvatar);  
				$data['length_image'] = $getAvatar;
			}  
			if(!empty($request->file('width_image')))
			{
				$photo_image = $request->file('width_image');  
				$getAvatar = time().rand(111111,999999) . '.' . $photo_image->getClientOriginalExtension();
				$filename = $path_avatar."/".$getAvatar;
				$photo_image->move($path_avatar, $getAvatar);  
				$data['width_image'] = $getAvatar;
			}   
			if(!empty($request->file('height_image')))
			{
				$photo_image = $request->file('height_image');  
				$getAvatar = time().rand(111111,999999) . '.' . $photo_image->getClientOriginalExtension();
				$filename = $path_avatar."/".$getAvatar;
				$photo_image->move($path_avatar, $getAvatar);  
				$data['height_image'] = $getAvatar;
			}  
			if(!empty($request->file('weight_image')))
			{
				$photo_image = $request->file('weight_image');  
				$getAvatar = time().rand(111111,999999) . '.' . $photo_image->getClientOriginalExtension();
				$filename = $path_avatar."/".$getAvatar;
				$photo_image->move($path_avatar, $getAvatar);  
				$data['weight_image'] = $getAvatar;
			}  
			if(!empty($request->file('label_image')))
			{
				$photo_image = $request->file('label_image');  
				$getAvatar = time().rand(111111,999999) . '.' . $photo_image->getClientOriginalExtension();
				$filename = $path_avatar."/".$getAvatar;
				$photo_image->move($path_avatar, $getAvatar);  
				$data['label_image'] = $getAvatar;
			}  
			WeightFreeze::whereId($request->id)->update($data);
			Order::whereId($request->order_id)->update(['weight_freeze_status'=>'Freezed']);
			return redirect()->route('weight.freeze')->with('success','weight freezed has been successfully added.');
		}
		
		public function freezeAll(Request $request)
		{	 
			$users = User::whereStatus(1)->get();
			$weightfreeze = WeightFreeze::select('weight_freezes.*','o.weight_freeze_status','o.packaging_id','u.name')
			->join('orders as o','o.id','=','weight_freezes.order_id')
			->join('users as u','u.id','=','o.user_id');
			if($request->search)
			{
				$search = $request->search;
				$weightfreeze = $weightfreeze->where(function ($query) use ($search) 
				{
					return $query->where('o.awb_number', 'LIKE',"%{$search}%")->orWhere('o.id', 'LIKE',"%{$search}%")->orWhere('o.order_prefix', 'LIKE',"%{$search}%");
				});
			}
			if($request->user_id)
			{	
				$weightfreeze->where('o.user_id',$request->user_id);
			}
			$weightfreeze->where('o.weight_freeze_status','Freezed')->orderBy('weight_freezes.id','desc');
			$weightfreezes = $weightfreeze->paginate(10);
		
			return view('weight.freeze.all',compact('weightfreezes','users'));
		}
	}
