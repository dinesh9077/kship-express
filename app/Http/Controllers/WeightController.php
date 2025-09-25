<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Order;
	use App\Models\User;
	use App\Models\weightDescrepencyHistory;
	use App\Models\ExcessWeight;
	use App\Models\weightDescrepency;
	use App\Models\WeightFreeze;
	use App\Models\Notification;
	use App\Models\Billing;
	use App\Models\WeightDiscrepancyRemark;
	use File,Auth,DB,Helper;
	class WeightController extends Controller
	{
		public function __construct()
		{
			$this->middleware('auth');
		}
		  
		public function descrepenciesAll(Request $request)
		{    
			if (auth()->user()->role === 'user') {
				abort(403, 'Permission denied');
			} 
			// Fetch users efficiently
			$users = User::where('status', 1)->select('id', 'name')->get();
            $taskId = request('task_id', '');
			
			// Query builder with necessary columns
			$order = Order::with('user:id,name', 'orderItems', 'excessWeight', 'weightDescripencies')  
			->whereNotNull('orders.awb_number')
			->when($request->user_id, fn($query) => $query->where('orders.user_id', $request->user_id))
			->when($request->weight_status, fn($query) => $query->where('orders.weight_status', $request->weight_status))
			->when($request->weight_update_date, fn($query) => $query->whereDate('orders.weight_update_date', $request->weight_update_date))
			->when($request->search, function ($query, $search) {
				return $query->where(function ($q) use ($search) {
					$q->where('orders.awb_number', 'LIKE', "%{$search}%")
					  ->orWhere('orders.id', 'LIKE', "%{$search}%")
					  ->orWhere('orders.order_prefix', 'LIKE', "%{$search}%");
				});
			});
			
			if($taskId)
			{
			    $order->where('id', $taskId);
			}
			
			if($request->search || $request->user_id || $request->weight_status || $request->weight_update_date)
			{
				$order->where('is_raise_weight', 0);
			}
			else
			{
				$order->where('is_raise_weight', 1);
			}
			$orders = $order->orderByDesc('orders.id')->paginate(10);  
			return view('weight.descrepensies.all_descrepencies', compact('users', 'orders'));
		}
  
		public function raiseExcessWeight(Request $request)
		{
			try {
				DB::beginTransaction(); // Start transaction
				$orderId = $request->order_id;
				
				// Prepare data
				$data = $request->except('_token', 'product_images');
				$timestamp = now(); // Use Laravel's now() for cleaner code
				$data['created_at'] = $timestamp;
				$data['updated_at'] = $timestamp;
				$data['status'] = 'New';
				
				$imagePaths = []; 
				if ($request->hasFile('product_images')) {
					foreach ($request->file('product_images') as $image) {
						// Generate a unique filename
						$filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

						// Store the file in the "weight_descrepency" folder within "storage/app/public"
						$path = $image->storeAs('weight_descrepency/'.$orderId, $filename, 'public');

						// Save the file path in an array
						$imagePaths[] = $path;
					}
				} 
				$data['product_images'] = $imagePaths;
		
				// Insert Excess Weight Data
				ExcessWeight::create($data);

				// Update Order Status
		    	$order = Order::find($orderId);

				$order->update([
					'weight_status' => 'New',
					'is_raise_weight' => 1,
					'weight_update_date' => $timestamp
				]);

				// Insert into weight descrepency history
				weightDescrepencyHistory::create([
					'order_id' => $request->order_id,
					'action_by' => config('setting.company_name'),
					'remarks' => 'Immediately Upload',
					'status_descrepency' => 'New',
					'created_at' => $timestamp,
					'updated_at' => $timestamp
				]);
                
                $now = now();
				
				Notification::insert([
                    'user_id'    => $order->user_id, // recipient user ID
                    'task_id'    => $orderId, // or related shipment/ticket ID
                    'type'       => 'Weight Discrepancy',
                    'role'       => 'user',
                    'text'       => '⚖️ A weight discrepancy has been detected in your shipment (AWB/Order No: ' . $order->awb_number . '). Please review the updated shipping charges.',
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
 
				DB::commit(); // Commit transaction if all operations succeed

				return redirect('weight/descripencies/all')->with('success', 'The raise excess weight has been added successfully.');

			} catch (\Exception $e) {
				DB::rollBack(); // Rollback on failure
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
			try {
				DB::beginTransaction(); // Start transaction

				// Fetch Order & Excess Weight
				$order = Order::with('user')->findOrFail($id);
				$excess_weight = ExcessWeight::where('order_id', $id)->firstOrFail();
				$excess_charge = $excess_weight->excess_charge;

				// Deduct from User Wallet
				$user = User::findOrFail($order->user_id);
				if ($user->wallet_amount < $excess_charge) {
					return response()->json(['status' => 'error', 'msg' => 'Insufficient wallet balance'], 400);
				}
				$user->decrement('wallet_amount', $excess_charge);

				// Insert Billing Record
				Billing::insert([
					'user_id'         => $order->user_id,
					'billing_type'    => "Order",
					'billing_type_id' => $order->id,
					'transaction_type'=> 'debit',
					'amount'          => $excess_charge,
					'note'            => 'Excess weight applied for AWB: ' . $order->awb_number,
					'created_at'      => now(),
					'updated_at'      => now(),
				]);

				// Insert into Weight Discrepancy History
				weightDescrepencyHistory::create([
					'order_id'            => $id,
					'action_by'           => 'Dispute accepted in favour of '.request('by'),
					'remarks'             => '',
					'status_descrepency'  => 'Accepted',
					'created_at'          => now(),
					'updated_at'          => now(),
				]);
				
				$now = now();
                
                if(auth()->user()->role == "user")
                {
                    Notification::insert([
    					'task_id'    => $order->id,
    					'type'       => 'Weight Discrepancy',
    					'role'       => 'admin',
    				    'text'       => '✅ ' . $order->user->name . ' has accepted the weight discrepancy for  Awb No: ' . $order->awb_number . '.',
    					'created_at' => $now,
    					'updated_at' => $now
    				]);
                }
                else
                {
                    Notification::insert([
                        'user_id'    => $order->user_id,
                        'task_id'    => $order->id,
                        'type'       => 'Weight Discrepancy',
                        'role'       => 'user',
                        'text'       => '📦 Your shipment weight discrepancy (AWB/Order No: ' . $order->awb_number . ') has been approved by admin. Updated charges will apply.',
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);

                }
				// Update Order Status
				$order->update([
					'weight_status'    => 'Accepted', 
					'weight_update_date'    => now(), 
				]);

				DB::commit(); // Commit transaction

				return response()->json(['status' => 'success', 'msg' => 'Weight discrepancy has been accepted']);
			} catch (\Exception $e) {
				DB::rollBack(); // Rollback on failure
				return response()->json(['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()], 500);
			}
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
			}
			else
			{ 
				weightDescrepencyHistory::insert(['order_id'=>$id,'action_by'=>'Courier','remarks'=>'Reject, courier charged correctly as per seller content & qty.','status_descrepency'=>$status,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
			}
			Order::whereId($id)->update(['weight_status'=>$status,'weight_update_date'=>date('Y-m-d H:i:s')]); 
			return response()->json(['status'=>'success','msg'=>'weight descrepency has been accepted']); 
		}
		
		public function weightRemark($id)
		{
			$order = Order::find($id);
			$remarks = WeightDiscrepancyRemark::where('order_id', $id)->orderBy('id')->get(); 
			$role = auth()->user()->role;
			return view('weight.descrepensies.remark-form', compact('id', 'order', 'remarks', 'role'));
		}
		
		public function remarkStore(Request $request)
		{   
			$request->validate([
				'order_id' => 'required|integer',
				'remark' => 'required|string',
				'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
			]);
			
			DB::beginTransaction();
			try {
				$orderId = $request->order_id;
				$order = Order::find($orderId);
				$imagePaths = []; 
				if ($request->hasFile('images')) {
					foreach ($request->file('images') as $image) {
						 
						// Generate a unique filename
						$filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

						// Store the file in the "weight_descrepency" folder within "storage/app/public"
						$path = $image->storeAs('remark/'.$orderId, $filename, 'public');

						// Save the file path in an array
						$imagePaths[] = $path;
					}
				} 
				
				$remark = WeightDiscrepancyRemark::create([
					'order_id' => $orderId,
					'role' =>  auth()->user()->role,
					'user_id' => auth()->id(),
					'remark' => $request->remark,
					'images' => $imagePaths,
				]);
				
				$now= now();
				
				if(auth()->user()->role == "user")
				{
    				Notification::insert([
    					'task_id'    => $order->id,
    					'type'       => 'Weight Discrepancy',
    					'role'       => 'admin',
    					'text'       => $order->user->name . ' has created a new weight reamrk (Awb No: ' . $order->awb_number . ').',
    					'created_at' => $now,
    					'updated_at' => $now
    				]); 
				}
				else { 
    
    				Notification::insert([
    					'user_id'    => $order->user_id,
    					'task_id'    => $order->id,
    					'type'       => 'Weight Discrepancy',
    					'role'       => 'user',
    					'text'       => auth()->user()->name . ' has created a new weight reamrk (Awb No: ' . $order->awb_number . ').',
    					'created_at' => $now,
    					'updated_at' => $now
    				]);
    				
				}
				
				$order->update(['weight_status' => 'remark', 'weight_update_date' => now()]);
				DB::commit(); 
				return redirect()->back()->with('success', 'Remark submitted successfully.');
			} catch (\Exception $e) {
				DB::rollback();
				return redirect()->back()->with('error', 'Failed to submit remark.'); 
			}
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
				 
					$fourDaysafetr = strtotime(date('Y-m-d', strtotime('7 days', strtotime($excess_weight->created_at))));
					
					// Get the current date
					$currentDate = strtotime(date('Y-m-d'));
					 
					// Compare the current date with 7 days before the target date
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
					}
				}
			}
			return response()->json(['status'=>'success','msg'=>'weight descrepency has been accepted']); 
		}
		
		public function index(Request $request)
		{   
		    $userId = $request->user_id ??auth()->user()->id; 
			$users = User::where('status', 1)->get();
			 
			// Use eager loading to optimize queries and avoid redundant joins
			$orders = Order::with(['user:id,name', 'excessWeight', 'orderItems'])
				->select('orders.*')
				->whereNotNull('orders.awb_number')
				->when($userId, fn($query) => $query->where('orders.user_id', $userId))
				->when($request->weight_status, fn($query) => $query->where('orders.weight_status', $request->weight_status))
				->when($request->weight_update_date, fn($query) => $query->whereDate('orders.weight_update_date', $request->weight_update_date))
				->when($request->search, function ($query, $search) {
					$query->where(function ($q) use ($search) {
						$q->where('orders.awb_number', 'LIKE', "%{$search}%")
						  ->orWhere('orders.id', 'LIKE', "%{$search}%")
						  ->orWhere('orders.order_prefix', 'LIKE', "%{$search}%");
					});
				}) 
				->whereHas('excessWeight')
				->whereNotIn('orders.weight_status', ['Accepted', 'Auto Accepted'])
				->orderByDesc('orders.id')
				->paginate(10);
			 
			return view('weight.descrepensies.index', compact('users', 'orders'));
		} 
	}
