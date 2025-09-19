<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\User;
	use App\Models\Role;
	use App\Models\Permission;
	use App\Models\Billing;
	use App\Models\UserKyc;
	use App\Models\Notification;
	use App\Models\UserWallet;
	use DB,Auth,File,Hash;
	use Illuminate\Support\Facades\Http;
	class UserController extends Controller
	{
		public function __construct()
		{
			$this->middleware('auth');
		}
		
		public function index()
		{  
			if(isset($_GET['task_id']))
			{
				Notification::whereId($_GET['notify_id'])->update(['read_at'=>1]);
			}
			$user_id = (isset($_GET['task_id']))?$_GET['task_id']:''; 
			return view('users.index',compact('user_id'));
		}
		
		public function ajaxUser(Request $request)
		{
			error_reporting(0);
			$draw = $request->get('draw');
			$start = $request->get("start");
			$limit = $request->get("length"); // Rows display per page
			
			$columnIndex_arr = $request->get('order');
			$columnName_arr = $request->get('columns');
			$order_arr = $request->get('order');
			$search_arr = $request->get('search'); 
			
			$columnIndex = $columnIndex_arr[0]['column']; // Column index
			$order = $columnName_arr[$columnIndex]['data']; // Column name
			$dir = $order_arr[0]['dir']; // asc or desc
			
			// Base query for users
			$query = User::with('createdBy:id,name') 
				->where('role', 'user');
				 
			$role = Auth::user()->role;
			$id = Auth::user()->id;
 
			// Count total records for pagination
			$totalData = $query->count();

			// Apply search filter if present
			if ($search = $request->input('search')) {
				$query->where(function ($q) use ($search) {
					$q->where('company_name', 'LIKE', "%{$search}%")
					->orWhere('name', 'LIKE', "%{$search}%")
					->orWhere('mobile', 'LIKE', "%{$search}%")
					->orWhere('email', 'LIKE', "%{$search}%")
					->orWhere('wallet_amount', 'LIKE', "%{$search}%")
					->orWhereHas('createdBy', function($q) use ($search){
						$q->where('name', 'LIKE', "%{$search}%");
					})
					->orWhere('created_at', 'LIKE', "%{$search}%");
				});
			}

			// Get filtered count for pagination
			$totalFiltered = $query->count();

			// Get the final paginated list of users
			$values = $query->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			// Prepare response data
			$data = [];
			foreach ($values as $index => $value) {
				  
				$mainData = [
					'id' => $start + $index + 1,
					'company_name' => $value->company_name,
					'name' => $value->name,
					'mobile' => $value->mobile,
					'email' => $value->email,
					'wallet_amount' => config('setting.currency') . $value->wallet_amount,
					'kyc_status' => $value->kyc_status == 1 ? '<span class="badge badge-success">Approved</span>' : '<span class="badge badge-danger">Pending</span>',
					'status' => $value->status == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">In-Active</span>',
					'staff_member' => $value->createdBy->name ?? 'N/A',
					'created_at' => $value->created_at->format('d M Y'),
					'action' => $this->generateUserActions($value, $role, $id)
				];

				$data[] = $mainData;
			}

			// Return the response in the correct format
			$response = [
				"draw" => intval($draw),
				"iTotalRecords" => $totalData,
				"iTotalDisplayRecords" => $totalFiltered,
				"aaData" => $data
			];

			return response()->json($response);
		}

		private function generateUserActions($value, $role, $id)
		{
			$actionButtons = '';  
			if(config('permission.franchise_partner.add'))
			{
				$actionButtons .= '<a href="javascript:;" class="btn btn-icon waves-effect waves-light action-icon mr-1" data-toggle="tooltip" title="Recharge mannualy" data-id="'.$value->id.'" data-amount="'.$value->wallet_amount.'" onclick="rechargeUser(this, event)"> <i class="mdi mdi-refresh"></i> </a>';
			}
			if(config('permission.franchise_partner.edit'))
			{
				$actionButtons .=  '<a href="'.url('users/edit', $value->id).'" class="btn btn-icon waves-effect waves-light action-icon mr-1" data-toggle="tooltip" title="Update User"> <i class="mdi mdi-pencil"></i> </a>';
			}
			if(config('permission.franchise_partner.delete'))
			{
				$actionButtons .= '<a href="'.url('users/delete', $value->id).'" class="btn btn-icon waves-effect waves-light action-icon" data-toggle="tooltip" title="Delete User" onClick="deleteRecord(this,event);"> <i class="mdi mdi-trash-can-outline"></i> </a>'; 
			}
			return $actionButtons;
		} 
		 
		public function createUser()
		{ 
			return view('users.create');
		}
		
		public function storeUser(Request $request)
		{
			$staffId = Auth::id();
			$data = $request->except('_token', 'password');
			$data['xpass'] = $request->password;
			$data['password'] = Hash::make($request->password);
			$data['role'] = 'user';
			$data['staff_id'] = $staffId;
			
			// Start database transaction
			DB::beginTransaction();
			
			try {
				// Check if mobile or email already exists
				if (User::whereMobile($request->mobile)->exists()) {
					return response()->json(['status' => 'error', 'msg' => 'The mobile number already exists.']);
				}

				if (User::whereEmail($request->email)->exists()) {
					return response()->json(['status' => 'error', 'msg' => 'The email number already exists.']);
				}

				// Insert user data and get the inserted ID
				User::create($data);

				// Commit transaction
				DB::commit();

				return response()->json(['status' => 'success', 'msg' => 'The user has been successfully added.']);
			} catch (\Exception $e) {
				// Rollback transaction if anything fails
				DB::rollBack();
				return response()->json(['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()]);
			}
		}
		
		public function editUser($userId)
		{ 
			$user = User::find($userId);  
			return view('users.edit', compact('user',));
		}
		
		public function updateUser(Request $request, $userId)
		{
			$data = $request->except('_token', 'password');

			// If a password is provided, hash it and store it
			if (!empty($request->password)) {
				$data['xpass'] = $request->password;
				$data['password'] = Hash::make($request->password);
			}
			$data['updated_at'] = now();

			// Start database transaction
			DB::beginTransaction();

			try {
				// Check if mobile or email already exists for other users
				if (User::where('id', '!=', $userId)->whereMobile($request->mobile)->exists()) {
					return response()->json(['status' => 'error', 'msg' => 'The mobile number already exists.']);
				}

				if (User::where('id', '!=', $userId)->whereEmail($request->email)->exists()) {
					return response()->json(['status' => 'error', 'msg' => 'The email number already exists.']);
				}

				// Update user data
				$user = User::find($userId);
				$user->update($data);

				// Commit transaction
				DB::commit();

				return response()->json(['status' => 'success', 'msg' => 'The user has been updated successfully.']);
			} catch (\Exception $e) {
				// Rollback transaction if anything fails
				DB::rollBack();
				return response()->json(['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()]);
			}
		}
		
		public function deleteUser($userId)
		{ 
			DB::beginTransaction(); 
			try { 
			
				User::find($userId)->delete();  
				DB::commit(); 
				
				return redirect()->route('users')->with('success', 'The user has been successfully deleted.');
			} catch (\Exception $e) {
			 
				DB::rollBack();
				return redirect()->route('users')->with('error', 'Something went wrong.');
			}
		}
 
		public function rechargeOffline(Request $request)
		{   
			$user_id = $request->user_id;
			$amount = $request->amount;
			if(!$user_id || !$amount)
			{
				return back()->with('error', 'Something went wrong');
			}
			 
			DB::beginTransaction(); 
			try { 
				$data = $request->except('_token');
				$data['status'] = 1;
				$data['transaction_status'] = 'success';
				$data['created_at'] = now();  
				$data['updated_at'] = now();
				$userWallet = UserWallet::create($data); 
 
				$user = User::find($user_id);
				if ($user) { 
					$user->increment('wallet_amount', $amount);
 
					Billing::create([
						'user_id' => $user_id,
						'billing_type' => "Recharge Wallet",
						'billing_type_id' => $userWallet->id,
						'transaction_type' => 'credit',
						'amount' => $amount,
						'note' => 'Recharge Wallet amount offline by admin.',
						'created_at' => now(),
						'updated_at' => now(),
					]);
				} 
				DB::commit();

				return back()->with('success', 'The recharge amount has been successfully credited.');
			} catch (\Exception $e) { 
				DB::rollBack();  
				return back()->with('error', 'Something went wrong: ' . $e->getMessage());
			}
		}
  
		public function newuser()
		{
			if(isset($_GET['task_id']))
			{
				Notification::whereId($_GET['notify_id'])->update(['read_at'=>1]);
			}
			$user_id = (isset($_GET['task_id']))?$_GET['task_id']:'';
			$users = User::where('kyc_status','!=', 1)->orderBy('name', 'asc')->get();
			$staffs = User::where('role','staff')->orderBy('name', 'asc')->get();
			return view('users.new_user',compact('user_id','users','staffs'));
		}
		
		public function ajaxnewUser(Request $request)
		{
			$draw = $request->get('draw');
			$start = $request->get("start");
			$limit = $request->get("length"); // Rows display per page
			
			$columnIndex_arr = $request->get('order');
			$columnName_arr = $request->get('columns');
			$order_arr = $request->get('order');
			$search_arr = $request->get('search');
			$user_id = $request->post('user_id');
			$role = Auth::user()->role;
			$id = Auth::user()->id;
			
			$columnIndex = $columnIndex_arr[0]['column']; // Column index
			$order = $columnName_arr[$columnIndex]['data']; // Column name
			$dir = $order_arr[0]['dir']; // asc or desc
			if($order = 'customer_name')  
			{
				$order = 'id';
			}
			$query = User::where('id','!=','')->where('kyc_status','!=', 1)->where('role','user');  
			if(!empty($user_id))
			{
				$query->where('id',$user_id);
			}
			if($role == "staff") 
			{
				$query->where('users.staff_id',$id);
			}
			if ($request->staff_id) {
    			$staff_id = $request->staff_id;
    			$query->where('users.staff_id',$staff_id);
			}
    		
    		if ($request->fromdate && $request->todate) {
    			
    			$query->whereBetween('users.created_at', [$request->fromdate, $request->todate]);
				} elseif ($request->fromdate) {
				
    			$query->whereDate('users.created_at', $request->fromdate);
				} elseif ($request->todate) {
				
    			$query->whereDate('users.created_at', $request->todate);
			} 
			
			$totalData = $query->get()->count();
			
			$totalFiltered = User::where('id','!=','')->where('kyc_status','!=', 1)->where('role','user'); 
			if(!empty($user_id))
			{
				$totalFiltered->where('id',$user_id);
			}
			if($role == "staff") 
			{
				$totalFiltered->where('users.staff_id',$id);
			}
			
			if ($request->staff_id) {
    			$staff_id = $request->staff_id;
    			$totalFiltered->where('users.staff_id',$staff_id);
			}
    		
    		if ($request->fromdate && $request->todate) {
    			
    			$totalFiltered->whereBetween('users.created_at', [$request->fromdate, $request->todate]);
				} elseif ($request->fromdate) {
				
    			$totalFiltered->whereDate('users.created_at', $request->fromdate);
				} elseif ($request->todate) {
				
    			$totalFiltered->whereDate('users.created_at', $request->todate);
			} 
    		
    		$users_permission = Permission::where('user_id',$id)->where('slug','2')->first();
			// 		echo "<pre>";
			// 		print_r($users_permission);
			// 		echo "</pre>";
			// 		die;
			$values = User::select('users.*')->where('kyc_status','!=', 1)->where('role','user');
			
			if(!empty($user_id))
			{
				$values->where('id',$user_id);
			}
		    if($role == "staff") 
			{
				$values->where('users.staff_id',$id);
			}
    		if ($request->staff_id) {
    			$staff_id = $request->staff_id;
    			$values->where('users.staff_id',$staff_id);
			}
    		
    		if ($request->fromdate && $request->todate) {
    			
    			$values->whereBetween('users.created_at', [$request->fromdate, $request->todate]);
				} elseif ($request->fromdate) {
				
    			$values->whereDate('users.created_at', $request->fromdate);
				} elseif ($request->todate) {
				
    			$values->whereDate('users.created_at', $request->todate);
			} 
			
			$values->offset($start)->limit($limit)->orderBy('users'.'.'.$order,$dir);
			
			if(!empty($request->input('search')))
			{ 
				$search = $request->input('search');
				$values = $values->where(function ($query) use ($search) 
				{
					return $query->where('company_name', 'LIKE',"%{$search}%")->orWhere('name', 'LIKE', '%' . $search . '%')->orWhere('mobile', 'LIKE',"%{$search}%")->orWhere('email', 'LIKE',"%{$search}%")->orWhere('created_at', 'LIKE',"%{$search}%");
				});
				
				$totalFiltered = $totalFiltered->where(function ($query) use ($search) {
					return $query->where('company_name', 'LIKE',"%{$search}%")->orWhere('name', 'LIKE', '%' . $search . '%')->orWhere('mobile', 'LIKE',"%{$search}%")->orWhere('email', 'LIKE',"%{$search}%")->orWhere('created_at', 'LIKE',"%{$search}%");
				});  
			}
			
			$values = $values->get(); 
			$totalFiltered = $totalFiltered->count();
			
			$data = array();
			if(!empty($values))
			{
				$i = $start + 1; 
				foreach ($values as $value)
				{    
				    if($value->staff_id != '0')
    			    {
    			        $staff = User::where('id',$value->staff_id)->first();
    			        if($staff)
    			        {
    			            $staff_member = $staff->name;
						}else
    			        {
							$staff_member = '--';
						}
					}else
    			    {
						$staff_member = '--';
					}
    			    
					$mainData['id'] = $i; 
					$mainData['company_name'] = $value->company_name;
					$mainData['name'] = $value->name;
					$mainData['mobile'] = $value->mobile; 
					$mainData['email'] = $value->email; 
					$mainData['wallet_amount'] = config('setting.currency').''.$value->wallet_amount;   
					$mainData['kyc_status'] = ($value->kyc_status == 1)?'<span class="badge badge-success">Approved</span>':'<span class="badge badge-danger">Pending</span>'; 
					$mainData['status'] = ($value->status == 1)?'<span class="badge badge-success">Active</span>':'<span class="badge badge-danger">In-Active</span>'; 
					$mainData['staff_member'] = $staff_member;
					$mainData['created_at'] = date('d M Y',strtotime($value->created_at));
					$mainData['action'] = ""; 
					if($role != 'admin')
					{
					    if($users_permission->type == '2')
					    {
						    $mainData['action'] .= '<a href="javascript:;" class="btn btn-icon waves-effect waves-light action-icon mr-1" data-toggle="tooltip" title="Recharge mannualy" data-id="'.$value->id.'" data-amount="'.$value->wallet_amount.'" onclick="rechargeUser(this,event)"> <i class="mdi mdi-refresh"></i> </a>'; 
						}
					} else if($role == 'admin')
					{
					    $mainData['action'] .= '<a href="javascript:;" class="btn btn-icon waves-effect waves-light action-icon mr-1" data-toggle="tooltip" title="Recharge mannualy" data-id="'.$value->id.'" data-amount="'.$value->wallet_amount.'" onclick="rechargeUser(this,event)"> <i class="mdi mdi-refresh"></i> </a>'; 
					} 
					
					if($role != 'admin')
					{
					    if($users_permission->type == '2')
    				    {
    					    $mainData['action'] .= '<a href="'.url('users/edit',$value->id).'" class="btn btn-icon waves-effect waves-light action-icon mr-1" data-toggle="tooltip" title="Update User"> <i class="mdi mdi-pencil"></i> </a>';
						}
					} else if($role == 'admin')
					{
					    $mainData['action'] .= '<a href="'.url('users/edit',$value->id).'" class="btn btn-icon waves-effect waves-light action-icon mr-1" data-toggle="tooltip" title="Update User"> <i class="mdi mdi-pencil"></i> </a>';
					}
					
					
					if($role != 'admin')
					{
					    if($users_permission->type == '2')
    				    {
    					    $mainData['action'] .= '<a href="'.url('users/delete',$value->id).'" class="btn btn-icon waves-effect waves-light action-icon" data-toggle="tooltip"  title="Delete User"  onClick="deleteRecord(this,event);"> <i class="mdi mdi-trash-can-outline"></i> </a>'; 
						}
					} else if($role == 'admin')
					{
					    $mainData['action'] .= '<a href="'.url('users/delete',$value->id).'" class="btn btn-icon waves-effect waves-light action-icon" data-toggle="tooltip"  title="Delete User"  onClick="deleteRecord(this,event);"> <i class="mdi mdi-trash-can-outline"></i> </a>'; 
					}
					
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
		
		public function permission()
		{			
			if(isset($_GET['task_id']))
			{
				Notification::whereId($_GET['notify_id'])->update(['read_at'=>1]);
			}
			$user_id = (isset($_GET['task_id']))?$_GET['task_id']:'';
			$users = User::where('role','staff')->get();
			$roles = Role::get();
			$permission_list = DB::table('permission_model')->get();
			// foreach ($user as $v)
			// {
			// 	echo '<pre>';
			// 	print_r($v);
			// 	echo '</pre>';
			// 	die;
			// }
			
			return view('users.permission_list',compact('permission_list','roles', 'users','user_id'));
		}
		
		public function ajaxUserPermission(Request $request)
		{
			$draw = $request->get('draw');
			$start = $request->get("start");
			$limit = $request->get("length"); // Rows display per page
			
			$columnIndex_arr = $request->get('order');
			$columnName_arr = $request->get('columns');
			$order_arr = $request->get('order');
			$search_arr = $request->get('search');
			$user_id = $request->post('user_id');
			
			$columnIndex = $columnIndex_arr[0]['column']; // Column index
			$order = $columnName_arr[$columnIndex]['data']; // Column name
			$dir = $order_arr[0]['dir']; // asc or desc
			if($order = 'customer_name')  
			{
				$order = 'id';
			}
			$totalFiltered = Permission::select('permissions.user_id', 'users.name', DB::raw('GROUP_CONCAT(permission_model.id ORDER BY permission_model.id ASC) as slugs'))->join('users', 'users.id', '=', 'permissions.user_id')->join('permission_model', 'permission_model.id', '=', 'permissions.slug')->groupBy('permissions.user_id', 'users.name');
			if(!empty($user_id))
			{
				$totalFiltered->where('user_id',$user_id);
				
			}
			
			$values = Permission::select('permissions.user_id', 'users.name', DB::raw('GROUP_CONCAT(permission_model.id ORDER BY permission_model.id ASC) as slugs'))->join('users', 'users.id', '=', 'permissions.user_id')->join('permission_model', 'permission_model.id', '=', 'permissions.slug')->groupBy('permissions.user_id', 'users.name');
			if(!empty($user_id))
			{
				$values->where('user_id',$user_id);
				
			}
			
			
            if(!empty($request->input('search'))) {
                $search = $request->input('search');
                $values->where(function ($query) use ($search) {
                    $query->where('users.name', 'LIKE', '%' . $search . '%')
					->orWhere('permission_model.model', 'LIKE', '%' . $search . '%')
					->orWhere('permissions.created_at', 'LIKE', '%' . $search . '%');
				});
                $totalFiltered->where(function ($query) use ($search) {
                    $query->where('users.name', 'LIKE', '%' . $search . '%')
					->orWhere('permission_model.model', 'LIKE', '%' . $search . '%')
					->orWhere('permissions.created_at', 'LIKE', '%' . $search . '%');
				});
			}
            
			$values->offset($start)->limit($limit)->orderBy('permissions'.'.'.$order,$dir);
			$values = $values->get(); 
			$totalFiltered = $totalFiltered->count();
			
			$data = array();
			if(!empty($values))
			{
				$i = $start + 1; 
				
				foreach ($values as $value)
				{    
					$slg = [];
					$slugs = explode(",", $value->slugs);
					foreach ($slugs as $slug)
					{
						$model = DB::table("permission_model")->where("id", $slug)->first();
						if ($model) {
							$sl = $model->model;
							$slg[] = $sl;
						}
						
					}
					
					$mainData['id'] = $i; 
					$mainData['user_id'] = $value->name;
					$mainData['slug'] = $slg;
					// 	$mainData['created_at'] = date('d M Y',strtotime($value->created_at));
					// $mainData['action'] .= '<a href="'.url('users/edit',$value->id).'" class="btn btn-icon waves-effect waves-light action-icon mr-1" data-toggle="tooltip" title="Update User"> <i class="mdi mdi-pencil"></i> </a>';
					if($value->role != 'admin')
					{
						$mainData['action'] = '<a href="'.url('permission/delete',$value->user_id).'" class="btn btn-icon waves-effect waves-light action-icon" data-toggle="tooltip"  title="Delete User Permission"  onClick="deleteRecord(this,event);"> <i class="mdi mdi-trash-can-outline"></i> </a>'; 
					}
					$data[] = $mainData;
					$i++;
				}
			}
			
			$response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalFiltered,
            "iTotalDisplayRecords" => $totalFiltered,
            "aaData" => $data
			); 
			
			echo json_encode($response);
			exit;
		}
		
        // public function ajaxUserPermission(Request $request)
        // {
        //     $draw = $request->get('draw');
        //     $start = $request->get("start");
        //     $limit = $request->get("length"); // Rows display per page
		
        //     $columnIndex_arr = $request->get('order');
        //     $columnName_arr = $request->get('columns');
        //     $order_arr = $request->get('order');
        //     $search_arr = $request->get('search');
        //     $user_id = $request->post('user_id');
		
        //     $columnIndex = $columnIndex_arr[0]['column']; // Column index
        //     $order = $columnName_arr[$columnIndex]['data']; // Column name
        //     $dir = $order_arr[0]['dir']; // asc or desc
        
        //     // Fix column name for ordering
        //     if ($order == 'customer_name') {
        //         $order = 'user_id';
        //     }
        
        //     $query = DB::table('permissions')
        //         ->join('users', 'users.id', '=', 'permissions.user_id')
        //         ->join('permission_model', 'permission_model.id', '=', 'permissions.slug')
        //         ->select(
        //             'users.id as user_id',
        //             'users.name',
        //             DB::raw('GROUP_CONCAT(permission_model.id ORDER BY permission_model.id ASC) as slugs')
        //         )
        //         ->groupBy('users.id', 'users.name');
        
        //     // Apply user_id filter if provided
        //     if (!empty($user_id)) {
        //         $query->where('users.id', $user_id);
        //     }
        
        //     // Apply search filter if provided
        //     if (!empty($request->input('search')['value'])) {
        //         $search = $request->input('search')['value'];
        //         $query->where(function ($q) use ($search) {
        //             $q->where('users.name', 'LIKE', '%' . $search . '%')
        //                 ->orWhere('permission_model.model', 'LIKE', '%' . $search . '%')
        //                 ->orWhere('permissions.created_at', 'LIKE', '%' . $search . '%');
        //         });
        //     }
        
        //     // Total records with filtering
        //     $totalFiltered = $query->count();
        
        //     // Get filtered results with pagination and ordering
        //     $values = $query->offset($start)
        //         ->limit($limit)
        //         ->orderBy($order, $dir)
        //         ->get();
        
        //     $data = [];
        //     if (!$values->isEmpty()) {
        //         $i = $start + 1;
        
        //         foreach ($values as $value) {
        //             $slg = [];
        //             $slugs = explode(",", $value->slugs); // Changed from $value->slug to $value->slugs
        //             foreach ($slugs as $slug) {
        //                 $model = DB::table("permission_model")->where("id", $slug)->first();
        //                 if ($model) {
        //                     $sl = $model->model;
        //                     $slg[] = $sl;
        //                 }
        //             }
        
        //             $mainData = [
        //                 'id' => $i,
        //                 'user_id' => $value->name,
        //                 'slug' => implode(", ", $slg), // Convert array to string for display
        //                 'created_at' => date('d M Y', strtotime($value->created_at)),
        //                 'action' => $value->role != 'admin' ? 
        //                     '<a href="'.url('permission/delete',$value->user_id).'" class="btn btn-icon waves-effect waves-light action-icon" data-toggle="tooltip"  title="Delete User Permission"  onClick="deleteRecord(this,event);"> <i class="mdi mdi-trash-can-outline"></i> </a>' : 
        //                     '',
        //             ];
		
        //             $data[] = $mainData;
        //             $i++;
        //         }
        //     }
        
        //     $response = [
        //         "draw" => intval($draw),
        //         "iTotalRecords" => $totalFiltered,
        //         "iTotalDisplayRecords" => $totalFiltered,
        //         "aaData" => $data
        //     ];
        
        //     return response()->json($response);
        // }
		
		
		public function storeUserpermission(Request $request)
		{  
			$data['user_id'] = $request->input('user_id');
			$data['type'] = $request->input('permission_type');
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$slug = $request->input('slug');
			
			foreach($slug as $slugs)
			{	
				$data['slug'] = $slugs;
				Permission::insert($data);  		
			}
			
			return redirect()->route('users.permission')->with('success','The user  Permission has been successfully added.');  
		}
		
		public function deleteUserpermission($id)
		{ 
			try
			{ 
				$user = Permission::where('user_id',$id)->delete(); 
				return redirect()->route('users.permission')->with('success','The user permission has been successfully deleted.'); 
			}
			catch(\Exception $e)
			{ 
				return redirect()->route('users')->with('error','Something went wrong.'); 
			}  
		}
		  
		// USER KYC
		public function kycUser()
		{   
			error_reporting(0);
			$user_id = Auth::id();
			$userkyc = UserKyc::where('user_id', $user_id)->first();
			return view('users.kyc-user-update', compact('userkyc'));
		}
		
		public function kycUserPancardUpdate(Request $request)
		{
			return $this->kycUserDocumentUpdate($request, 'pancard_image', 'pancard');
		}
		
		public function kycUserAadharUpdate(Request $request)
		{
			return $this->kycUserDocumentUpdate($request, ['aadhar_front', 'aadhar_back'], 'aadhar');
		}
		
		public function kycUserGSTUpdate(Request $request)
		{
			return $this->kycUserDocumentUpdate($request, 'gst_image', 'gst');
		}
		
		public function kycUserBankUpdate(Request $request)
		{
			return $this->kycUserDocumentUpdate($request, 'bank_passbook', 'bank_passbook');
		}
		
		private function kycUserDocumentUpdate(Request $request, $imageFields, $prefix)
		{
			$user_id = Auth::id();
			$data = $request->except('_token');
			$path_avatar = storage_path("app/public/kyc/{$user_id}");
			
			try {
				if (!File::exists($path_avatar)) {
					File::makeDirectory($path_avatar, 0777, true, true);
				}
				
				$imageFields = (array) $imageFields;
				foreach ($imageFields as $field) {
					if ($request->hasFile($field)) {
						$photo_image = $request->file($field);
						$getAvatar = "{$prefix}_" . time() . rand(111111, 999999) . '.' . $photo_image->getClientOriginalExtension();
						$filename = "$path_avatar/$getAvatar";
						
						if ($photo_image->getSize() > 2 * 1024 * 1024) {
							$sourceImage = imagecreatefromjpeg($photo_image->getRealPath());
							if ($sourceImage) {
								imagejpeg($sourceImage, $filename, 75);
								imagedestroy($sourceImage);
								} else {
								throw new \Exception("Invalid image format. Only JPEG supported for compression.");
							}
							} else {
							$photo_image->move($path_avatar, $getAvatar);
						}
						$data[$field] = $getAvatar;
					}
				}
				
				DB::beginTransaction();
				
				if (UserKyc::where('user_id', $user_id)->exists()) {
					UserKyc::where('user_id', $user_id)->update($data);
					} else {
					$data['user_id'] = $user_id;
					UserKyc::create($data);
				}
				
				DB::commit();
				
				return redirect()->back()->with('success', 'The KYC request has been successfully sent to the admin.');
				} catch (\Exception $e) {
				DB::rollBack();
				return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
			}
		}
		
		// KYC ADMIN
		public function kycUserRequest()
		{  
			return view('users.kyc-user-request');
		}
		
		public function kycUserRequestAjax(Request $request)
		{
			error_reporting(0);
			$draw = $request->get('draw');
			$start = $request->get('start');
			$limit = $request->get('length'); // Rows display per page
			
			$order = $request->get('order');
			$columnNameArr = $request->get('columns');
			$searchValue = $request->get('search'); // Global search value
			
			// Determine the order column
			$columnIndex = $order[0]['column']; // Column index
			$orderBy = $columnNameArr[$columnIndex]['data']; // Column name
			$dir = $order[0]['dir']; // asc or desc

			$role = Auth::user()->role;
			$id = Auth::user()->id;
			
			// Build base query
			$query = UserKyc::join('users as u', 'u.id', '=', 'user_kycs.user_id')
							->select('user_kycs.*', 'u.name', 'u.email', 'u.staff_id', 'u.kyc_status');

			// Filter by role
			if ($role == 'staff') {
				$query->where('u.staff_id', $id);
			}

			// Apply search filter if exists
			if ($searchValue) {
				$query->where(function($query) use ($searchValue) {
					$query->where('u.name', 'LIKE', "%$searchValue%")
					  ->orWhere('u.email', 'LIKE', "%$searchValue%");
				});
			}

			// Apply KYC status filter if selected
			if ($kycStatus = $request->input('kyc_status')) 
			{  
				$query->where(function($query) use ($kycStatus) { 
					  $query->where('user_kycs.pancard_status', $kycStatus)
					  ->orWhere('user_kycs.bank_status', $kycStatus)
					  ->orWhere('user_kycs.aadhar_status', $kycStatus)
					  ->orWhere('user_kycs.gst_status', $kycStatus);
				});
			}

			// Count total filtered data
			$totalFiltered = $query->count();

			// Get paginated values
			$values = $query->offset($start)
							->limit($limit)
							->orderBy('user_kycs.' . $orderBy, $dir)
							->get();

			// Prepare data for response
			$data = [];
			$i = $start + 1; // To display the correct row index
			foreach ($values as $value) {
				$pancardStatus = $this->getStatusBadge($value->pancard_status);
				$aadharStatus = $this->getStatusBadge($value->aadhar_status);
				$gstStatus = $this->getStatusBadge($value->gst_status);
				$bankStatus = $this->getStatusBadge($value->bank_status);

				$mainData = [
					'id' => $i,
					'name' => $value->name,
					'email' => $value->email,
					'pancard_status' => $pancardStatus,
					'aadhar_status' => $aadharStatus,
					'gst_status' => $gstStatus,
					'bank_status' => $bankStatus,
					'created_at' => $value->updated_at->format('d M Y'),
					'action' => $this->getActionButton($value->id, $role, $id)
				];

				$data[] = $mainData;
				$i++;
			}

			// Return response as JSON
			$response = [
				'draw' => intval($draw),
				'iTotalRecords' => $totalFiltered,
				'iTotalDisplayRecords' => $totalFiltered,
				'aaData' => $data
			];

			return response()->json($response);
		}

		// Helper to get the status badge HTML
		private function getStatusBadge($status)
		{
			switch ($status) {
				case 1:
					return '<span class="badge badge-success">Approved</span>';
				case 2:
					return '<span class="badge badge-danger">Rejected</span>';
				default:
					return '<span class="badge badge-warning">Pending</span>';
			}
		}

		// Helper to generate the action button
		private function getActionButton($id, $role, $userId)
		{
			$buttonHtml = ''; 
			if(config('permission.franchise_partner_kyc_request.edit'))
			{
				$buttonHtml .= '<a href="' . url('users/kyc/verified', $id) . '" class="btn btn-icon waves-effect waves-light action-icon mr-1"> <i class="mdi mdi-pencil"></i> </a>';  
			}
			return $buttonHtml;
		}
  
		public function kycUserRequestVerified($userKycId)
		{ 
			$userkyc = UserKyc::find($userKycId);
			return view('users.kyc-user-verify', compact('userkyc'));
		}
		
		public function kycUserVerified(Request $request)
		{ 
			$status = $request->status;    
			$type = $request->type;    
			$id = $request->id;

			try {
				// Start a transaction to ensure data consistency
				DB::beginTransaction();

				// Prepare the update data based on type
				$columnMap = [
					'pancard' => 'pancard_status',
					'aadhar' => 'aadhar_status',
					'gst' => 'gst_status',
					'bank' => 'bank_status'
				];

				// Check if the provided type exists in the map
				if (!isset($columnMap[$type])) {
					return response()->json(['status' => 'error', 'msg' => 'Invalid verification type.']);
				}
 
				$userKyc = UserKyc::find($id);
				$userKyc->update([$columnMap[$type] => $status]);
				
				$this->userVerifiedKyc($userKyc);

				// Commit the transaction after the update is successful
				DB::commit();

				// Return success message based on type
				$verificationMessages = [
					'pancard' => 'The pancard is verified successfully.',
					'aadhar' => 'The aadhar is verified successfully.',
					'gst' => 'The GST is verified successfully.',
					'bank' => 'The bank detail is verified successfully.'
				];

				return response()->json(['status' => 'success', 'msg' => $verificationMessages[$type]]);
			} catch (\Exception $e) { 
				DB::rollBack(); 
				return response()->json(['status' => 'error', 'msg' => 'Failed to verify KYC: ' . $e->getMessage()]);
			}
		}
	 
		public function userVerifiedKyc($userKyc)
		{ 
			$userKyc->user->update(['kyc_status' => 0]);
			if($userKyc->pancard_status == 1 && $userKyc->aadhar_status == 1 && $userKyc->gst_status == 1 && $userKyc->bank_status == 1)
			{
				$userKyc->user->update(['kyc_status' => 1]);
			} 
		}
		
		public function kycUserRejected(Request $request)
		{
			$status = 2;    
			$type = $request->type;    
			$id = $request->id;    
			$text = $request->text;

			// Mapping for columns to be updated based on type
			$columnMap = [
				'pancard' => ['column' => 'pancard_status', 'textColumn' => 'pancard_text', 'message' => 'The pancard is rejected successfully.'],
				'aadhar' => ['column' => 'aadhar_status', 'textColumn' => 'aadhar_text', 'message' => 'The aadhar is rejected successfully.'],
				'gst' => ['column' => 'gst_status', 'textColumn' => 'gst_text', 'message' => 'The gst is rejected successfully.'],
				'bank' => ['column' => 'bank_status', 'textColumn' => 'bank_text', 'message' => 'The bank detail is rejected successfully.']
			];

			try {
				// Start a transaction to ensure data consistency
				DB::beginTransaction();

				// Check if the provided type exists in the map
				if (!isset($columnMap[$type])) {
					return redirect()->back()->with('error', 'Invalid verification type.');
				}

				// Perform the update on the user KYC table
				$updateData = [
					$columnMap[$type]['textColumn'] => $text,
					$columnMap[$type]['column'] => $status
				];
				UserKyc::whereId($id)->update($updateData);

				// Commit the transaction
				DB::commit();

				// Return success message based on the type
				return redirect()->back()->with('success', $columnMap[$type]['message']);
			} catch (\Exception $e) {
				// Rollback the transaction in case of an error
				DB::rollBack(); 
				// Return error response with the exception message
				return redirect()->back()->with('error', 'Failed to reject KYC: ' . $e->getMessage());
			}
		}  
	}
