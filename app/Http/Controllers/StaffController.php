<?php
	
	namespace App\Http\Controllers;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\Role;
	use App\Models\RoleGroup;
	use App\Models\Permission;
	use App\Models\RolePermission;
	use App\Models\User; 
	use Validator, DB, Auth, ImageManager, Hash, Helper;
	use App\Services\MasterService;
	use Carbon\Carbon;
	class StaffController extends Controller
	{ 
		protected $masterService;
		public function __construct()
		{
			$this->masterService = new MasterService();
		}
		
		public function roles()
		{
			if (auth()->user()->role === 'user') {
				abort(403, 'Permission denied');
			} 
			return view('roles.index');
		}
		
		public function rolesAjax(Request $request)
		{
			if ($request->ajax())
			{
				$columns = ['id', 'name', 'status', 'created_at', 'action'];
				
				$search = $request->input('search.value');
				$start = $request->input('start');
				$limit = $request->input('length');
				
				// Base query
				$query = Role::query();
				
				// Apply search filter if present
				if (!empty($search)) {
					$query->where(function ($q) use ($search) {
						$q->where('name', 'LIKE', "%{$search}%")
						->orWhere('created_at', 'LIKE', "%{$search}%");
					}); 
				}
				
				$totalData = $query->count();
				$totalFiltered = $totalData;
				
				// Get data with limit and offset for pagination
				$values = $query->offset($start)->limit($limit)
				->orderBy($columns[$request->input('order.0.column')], $request->input('order.0.dir'))
				->get();
				
				// Format response
				$data = [];
				$i = $start + 1;
				foreach ($values as $key => $value) {
					$statusClass = $value->status == 1 ? 'success' : 'danger';
					$statusText = $value->status == 1 ? 'Active' : 'In-Active';

					$appendData = [
						'id' => $i, // Use $key instead of manually tracking $i
						'name' => $value->name,
						'status' => "<span class=\"badge bg-{$statusClass}\">{$statusText}</span>",
						'created_at' => $value->created_at->format('Y-m-d H:i:s'),
						'action' => '',
					];

					if (!in_array($value->name, ['admin', 'user'])) {
						$actions = [];
						if (config('permission.roles.edit')) { 
							$actions[] = "<a href=\"" . route('roles.edit', ['id' => $value->id]) . "\" onclick=\"editRoles(this, event)\" class=\"btn btn-icon waves-effect waves-light action-icon mr-1\"><i class='mdi mdi-pencil'></i></a>";
						}
						if (config('permission.roles.delete')) { 
						$actions[] = "<a href=\"" . route('roles.delete', ['id' => $value->id]) . "\" onclick=\"deleteRecord(this, event)\" class=\"btn btn-icon waves-effect waves-light action-icon\"> <i class='mdi mdi-trash-can-outline'></i></a>";
						}
						$appendData['action'] = implode(' ', $actions);
					}

					$data[] = $appendData;
					$i++;
				}
 
				return response()->json([
				'draw' => intval($request->input('draw')),
				'recordsTotal' => $totalData,
				'recordsFiltered' => $totalFiltered,
				'data' => $data,
				]);
			}
		}
		
		public function rolesCreate()
		{
			$permissions = Permission::where('status', 1) 
			->orderBy('position', 'asc')
			->get(); 
			
			$view = view('roles.create', compact('permissions'))->render();
			return response()->json(['status'=>'success','view'=>$view]); 
		} 
		
		public function rolesStore(Request $request)
		{  
			$validator = Validator::make($request->all(), [
				'name' => [
					'required', 
					'string', 
					'unique:roles,name' 	
					],
				'status' => [
					'required', 
					'integer', 	
					'in:0,1'  	
				]
			]);
			
			
			if ($validator->fails()) { 
				return response()->json(['status'=>'validation', 'errors'=>$validator->errors()]); 
			}
			
			try
			{
				DB::beginTransaction();
				
				if(!$request->permission)
				{  
					return response()->json(['status'=>'error', 'msg'=> 'You have not select any checkbox.']); 
				}
				$admin = auth()->user();
				
				$currentTime = now(); 
				$data = $request->except('_token', 'permission'); 
				$data['user_id'] = $admin->id; 
				$data['created_at'] = $currentTime;
				$data['updated_at'] = $currentTime;
				
				$role = Role::create($data);
				
				$permissions = $request->permission;
				
				// Prepare data for bulk insert
				$data = [];
				foreach ($permissions as $key => $permission) {
					$data[] = [
					'role_id' => $role->id,
					'name' => $key,
					'value' => $permission,
					'created_at' => $currentTime,
					'updated_at' => $currentTime,
					];
				}
				
				// Chunk size for batch insertion (adjust as needed)
				$chunkSize = 200;
				$dataChunks = array_chunk($data, $chunkSize); 
				foreach ($dataChunks as $chunk) 
				{
					// Bulk insert using insert method
					RoleGroup::insert($chunk);
				}
				
				DB::commit();
				 
				return response()->json(['status'=>'success', 'msg'=> 'The role has been created successfully.']); 
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return response()->json(['status'=>'error', 'msg'=> 'Failed to update settings. ' . $e->getMessage()]); 
			}
		}
		
		public function rolesEdit($roleId)
		{
			$role = Role::find($roleId);
			if(!$role)
			{ 
				return response()->json(['status'=>'error', 'msg'=> 'Role not found.']);  
			}
			
			$permissions = Permission::where('status', 1) 
			->orderBy('position', 'asc')
			->get(); 
			
			$roleper = RoleGroup::where('role_id', $roleId)
			->pluck('name')
			->toArray();
			
			$view = view('roles.edit', compact('permissions', 'role', 'roleper'))->render(); 
			return response()->json(['status'=>'success', 'view' => $view]); 
		} 
		
		public function rolesUpdate(Request $request, $id)
		{
			// Validate input
			$validator = Validator::make($request->all(), [
				'name' => [
					'required', 
					'string', 
					'unique:roles,name,' . $id
				],
				'status' => [
					'required', 
					'integer',
					'in:0,1'
				]
			]);
			
			// Return validation errors if validation fails
			if ($validator->fails()) {
				return response()->json(['status'=>'validation', 'errors'=>$validator->errors()]); 
			}
			
			try {
				DB::beginTransaction();
				
				// Find the role by ID
				$role = Role::find($id);
				if (!$role) { 
					return response()->json(['status'=>'error', 'msg'=> 'Role not found.']); 
				}
				
				// Ensure at least one permission is provided
				if (!$request->permission) { 
					return response()->json(['status'=>'error', 'msg'=> 'You have not selected any permissions.']);  
				}
				
				// Get the current authenticated admin
				$admin = auth()->user();
				
				// Prepare data for updating the role
				$currentTime = now();
				$data = $request->except('_token', 'permission', 'old_name'); 
				$data['user_id'] = $admin->id;
				$data['updated_at'] = $currentTime;
				
				// Update the role
				$role->update($data);
				
				User::whereRole($request->old_name)->update(['role' => $request->name]);
				
				// Fetch users and permissions
				$users = User::whereRole($request->name)->get();
				$permissions = $request->permission;
				$permissionNames = array_keys($permissions);
				
				// Delete old role permissions not in current permissions
				RolePermission::whereIn('user_id', $users->pluck('id'))
				->whereNotIn('name', $permissionNames)
				->delete();
				
				// Delete old role groups
				RoleGroup::whereRole_id($id)->delete();
				
				// Bulk insert role groups and role permissions
				$roleGroups = [];
				$rolePermissions = [];
				
				foreach ($permissions as $key => $permission) {
					$roleGroups[] = [
					'role_id' => $id,
					'name' => $key,
					'value' => $permission,
					'created_at' => $currentTime,
					'updated_at' => $currentTime
					];
					
					foreach ($users as $user) {
						// Check if role permission exists for user
						$exists = RolePermission::where('user_id', $user->id)
						->where('name', $key)
						->exists();
						
						if (!$exists) {
							$rolePermissions[] = [
							'user_id' => $user->id,
							'name' => $key,
							'value' => $permission,
							'created_at' =>$currentTime,
							'updated_at' => $currentTime
							];
						}
					}
				}
				
				// Chunk size for bulk insert operations (adjust as needed)
				$chunkSize = 100; // Example chunk size
				
				// Insert role groups in chunks
				foreach (array_chunk($roleGroups, $chunkSize) as $chunk) {
					RoleGroup::insert($chunk);
				}
				
				// Insert role permissions in chunks
				foreach (array_chunk($rolePermissions, $chunkSize) as $chunk) {
					RolePermission::insert($chunk);
				}
				
				// Commit the transaction
				DB::commit();
				
				// Return success response 
				return response()->json(['status'=>'success', 'msg'=> 'The role has been updated successfully.']); 
				
				} catch (\Throwable $e) {
				DB::rollBack(); 
				return response()->json(['status'=>'error', 'msg'=> 'Failed to update the role. ' . $e->getMessage()]); 
			}
		}
		 
		public function rolesDelete($id)
		{   
			try {
				DB::beginTransaction();
				
				$role = Role::find($id); 
				if (!$role) {  
					return redirect()->back()->with('error', 'The role was not found.');
				}  

				// Ensure roleGroups() relationship exists in Role model
				if ($role->roleGroups()->count()) {
					$role->roleGroups()->delete();  
				}

				$role->delete();
				
				DB::commit(); 
				return redirect()->back()->with('success', 'Role and its associated role groups deleted successfully.');  
			} catch (\Exception $e) {
				DB::rollBack();
				return redirect()->back()->with('error', 'Failed to delete the role. ' . $e->getMessage()); 
			}
		}
 
		public function staff()
		{
			if (auth()->user()->role === 'user') {
				abort(403, 'Permission denied');
			} 
			return view('staff.index');
		}
		
		public function staffAjax(Request $request)
		{
			if ($request->ajax())
			{
				$columns = ['id', 'company_name', 'name', 'mobile', 'email', 'role', 'status', 'created_at', 'action'];
				
				$search = $request->input('search.value');
				$start = $request->input('start');
				$limit = $request->input('length');
				
				// Base query
				$query = User::query()->whereNotIn('role', ['user']);
				
				// Apply search filter if present
				if (!empty($search)) {
					$query->where(function ($q) use ($search) {
						$q->where('name', 'LIKE', "%{$search}%")
						->orwhere('company_name', 'LIKE', "%{$search}%")
						->orwhere('email', 'LIKE', "%{$search}%")
						->orwhere('mobile', 'LIKE', "%{$search}%") 
						->orwhere('role', 'LIKE', "%{$search}%")
						->orWhere('created_at', 'LIKE', "%{$search}%");
					}); 
				}
				
				$totalData = $query->count();
				$totalFiltered = $totalData;
				
				// Get data with limit and offset for pagination
				$values = $query->offset($start)->limit($limit)
				->orderBy($columns[$request->input('order.0.column')], $request->input('order.0.dir'))
				->get();
				
				// Format response
				$data = [];
				$i = $start + 1;
				foreach ($values as $key => $value)
				{  
					// Build the data row
					$appendData = [
						'id' => $i,  
						'company_name' => $value->company_name, 
						'name' => $value->name, 
						'mobile' => $value->mobile, 
						'email' => $value->email, 
						'role' => $value->role, 
						'status' => '<span class="badge bg-' . ($value->status == 1 ? 'success' : 'danger') . '">' . ($value->status == 1 ? 'Active' : 'In-Active') . '</span>',
						'created_at' => $value->created_at->format('Y-m-d H:i:s'),
						'action' => '', // Initialize action
					];

					// Check role and permissions
					if (!in_array($value->role, ['admin', 'user'])) {
						$actions = [];
						if (config('permission.staff.edit')) {  
							$actions[] = '<a href="' . route('staff.edit', ['id' => $value->id]) . '" onclick="editStaff(this, event)"><i class="mdi mdi-pencil class-pencil"></i></a>';
						}
						// Delete permission
						if (config('permission.staff.delete')) {  
							$actions[] = '<a href="' . route('staff.delete', ['id' => $value->id]) . '" onclick="deleteRecord(this, event)" ><i class="mdi mdi-trash-can-outline class-delete"></i></a>';
						}
						if (config('permission.staff.edit')) {  
							$actions[] = '<a href="' . route('staff.permission', ['id' => $value->id]) . '" onclick="editPermission(this, event)" ><i class="mdi mdi-lock class-pencil"></i></a>';
						}
						  
						// Combine actions into the action column
						$appendData['action'] = implode(' ', $actions);
					}

					// Append data row to output
					$data[] = $appendData;
					$i++;
				}

				
				return response()->json([
				'draw' => intval($request->input('draw')),
				'recordsTotal' => $totalData,
				'recordsFiltered' => $totalFiltered,
				'data' => $data,
				]);
			}
		}
		
		public function staffCreate()
		{ 
			$roles = $this->masterService->getRoles(1);
			$view = view('staff.create', compact('roles'))->render();
			return response()->json([
				'status' => 'success',
				'view' => $view
			]);
		} 
		
		public function rolesGroups($roleId)
		{
			// Fetch permissions, ordered by heading_position and position, and group them by heading
			$permissions = Permission::where('status', 1) 
				->orderBy('position', 'asc')
				->get();

			// Fetch role permissions and map to names
			$roleper = RoleGroup::where('role_id', $roleId)
				->pluck('name')
				->toArray();

			// Render the view and return the JSON response
			$view = view('roles.role-group', compact('permissions', 'roleper'))->render();

			return response()->json([
				'status' => 'success',
				'view' => $view
			]);
		}
		
		public function staffStore(Request $request)
		{  
			$validator = Validator::make($request->all(), [ 
				'name' => 'required|string', 
				'email' => 'required|email|unique:users,email',
				'password' => 'required|string',
				'mobile' => 'required|string|unique:users,mobile',  
				'role' => 'required|string',  
				'status' => 'required|in:1,0',  
			]);
 
			if ($validator->fails()) { 
				return response()->json([
					'status' => 'validation',
					'errors' => $validator->errors()
				]);
			}
			
			try
			{
				DB::beginTransaction();
				 
				$admin = auth()->user();
				
				$currentTimestamp = Carbon::now();
			
				$data = $request->except('_token', 'permission', 'password');
				$data['password'] = Hash::make($request->password);
				$data['xpass'] = $request->password;
				$data['email'] = strtolower($request->email);
				$data['staff_id'] = $admin->id;  
				$data['created_at'] = $currentTimestamp;
				$data['updated_at'] = $currentTimestamp;  
				
				$staff = User::create($data);
				
				if ($request->role != "admin")
				{
					$permissions = $request->permission;
					
					// Prepare data for bulk insert
					$permissionData = [];
					foreach ($permissions as $key => $permission) {
						$permissionData[] = [
						'user_id' => $staff->id,
						'name' => $key,
						'value' => $permission,
						'created_at' => $currentTimestamp,
						'updated_at' => $currentTimestamp,
						];
					}
					
					// Use chunking for bulk insert to improve performance
					$chunks = array_chunk($permissionData, 200); // Chunk size can be adjusted based on your needs
					
					foreach ($chunks as $chunk) 
					{
						RolePermission::insert($chunk);
					}  
				}
				
				DB::commit();
				return response()->json([
					'status' => 'success',
					'msg' => 'The staff has been created successfully.'
				]);
				return $this->successResponse();
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return response()->json([
					'status' => 'error',
					'msg' => 'Failed to update settings. ' . $e->getMessage()
				]); 
			}
		}
		
		public function staffEdit($staffId)
		{
			$staff = User::find($staffId);
			if(!$staff)
			{ 
				return response()->json([
					'status' => 'error',
					'msg' => 'Staff not found.'
				]); 
			} 
			
			$view = view('staff.edit', compact('staff'))->render();
			return response()->json([
				'status' => 'success',
				'view' => $view
			]);  
		} 
		
		public function staffUpdate(Request $request, $staffId)
		{ 
			$validator = Validator::make($request->all(), [ 
				'name' => 'required|string', 
				'email' => 'required|email|unique:users,email,' . $staffId, 
				'password' => 'nullable|string',
				'mobile' => 'required|string|unique:users,mobile,' . $staffId,
				'status' => 'required|in:1,0',  
			]);

			if ($validator->fails()) { 
				return response()->json([
					'status' => 'validation',
					'errors' => $validator->errors()
				]);  
			}

			try {
				DB::beginTransaction();

				// Fetch the staff member to update
				$staff = User::findOrFail($staffId);

				$admin = auth()->user();
				$currentTimestamp = Carbon::now();

				// Update only the fields provided in the request
				$data = $request->except('_token', 'password');
				if ($request->filled('password')) {
					$data['password'] = Hash::make($request->password); // Hash the new password if provided
					$data['xps'] = base64_encode($request->password);  // Base64 encode the password
				}
				$data['email'] = strtolower($request->email);  
				$data['staff_id'] = $admin->id; 
				$data['updated_at'] = $currentTimestamp; // Update the timestamp

				// Update the staff details
				$staff->update($data);

				DB::commit();
				return response()->json([
					'status' => 'success',
					'msg' => 'The staff details have been updated successfully.'
				]);   
			} catch (\Throwable $e) {
				DB::rollBack();
				return response()->json([
					'status' => 'error',
					'msg' => 'Failed to update staff details. ' . $e->getMessage()
				]);   
			}
		}
		
		public function staffPermission($staffId)
		{
			$staff = User::find($staffId);
			if(!$staff)
			{ 
				return response()->json([
					'status' => 'error',
					'msg' => 'Staff not found.'
				]);   
			} 
			
			$roles = $this->masterService->getRoles(1);
			
			// Fetch permissions in a single query and organize them by heading
			$permissions = Permission::whereStatus(1) 
				->orderBy('position')
				->get();

			// Retrieve role permissions for the user
			$roleper = RolePermission::whereUser_id($staffId)
				->pluck('name')
				->toArray();
				
			$view = view('staff.permission', compact('staff', 'roleper', 'permissions', 'roles'))->render();
			return response()->json([
				'status' => 'success',
				'view' => $view
			]);   
		} 
		
		public function staffPermissionUpdate(Request $request, $staffId)
		{  
			$validator = Validator::make($request->all(), [    
				'role' => 'required|string',  
			]);
			 
			if ($validator->fails()) { 
				return response()->json([
					'status' => 'validation',
					'errors' => $validator->errors()
				]);  
			}
			
			try
			{  
				DB::beginTransaction(); 
				
				$currentTime = now();
				if ($request->role != "admin")
				{
					$permissions = $request->permission;
					
					// Delete permissions not included in the current request 
					RolePermission::where('user_id', $staffId)->delete();
					
					// Prepare permissionData for bulk insert
					$permissionData = [];
					foreach ($permissions as $key => $permission) {
						$permissionData[] = [
							'user_id' => $staffId,
							'name' => $key,
							'value' => $permission,
							'created_at' => $currentTime,
							'updated_at' => $currentTime,
						];
					}
					
					// Use chunking for bulk insert to improve performance
					$chunks = array_chunk($permissionData, 200); // Chunk size can be adjusted based on your needs
					
					foreach ($chunks as $chunk) 
					{
						RolePermission::insert($chunk);
					}  
				}
				else if ($request->role != "admin")
				{
					MstRolePermission::where('user_id', $staffId)->delete();
				}
				
				User::whereId($staffId)->update($request->only('role'));
			 
				DB::commit();
				return response()->json([
					'status' => 'success',
					'msg' => 'The staff permission been updated successfully.'
				]);  
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return response()->json([
					'status' => 'error',
					'msg' => 'Failed to update staff details. ' . $e->getMessage()
				]);   
			}  
		}
		
		public function staffDelete($staffId)
		{
			try
			{
				DB::beginTransaction();
  
				RolePermission::whereUser_id($staffId)->delete(); 
				User::find($staffId)->delete();
 
				DB::commit();
				
				return redirect()->back()->with('success', 'The staff has been successfully deleted.');   
			}
			catch (\Throwable $e)
			{
				DB::rollBack(); 
				return redirect()->back()->with('error', 'Failed to update staff details. ' . $e->getMessage());   
			}
		}
	}
