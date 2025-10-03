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
			if (auth()->user()->role === 'user') {
				abort(403, 'Permission denied');
			} 
			
			if(isset($_GET['task_id']))
			{
				Notification::whereId($_GET['notify_id'])->update(['read_at'=>1]);
			} 
			$user_id = (isset($_GET['task_id']))?$_GET['task_id']:''; 
			
			$kycStatus = request('kyc_status', 0);
			return view('users.index', compact('user_id', 'kycStatus'));
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
			$kycStatus = $request->get('kyc_status'); 
			
			$columnIndex = $columnIndex_arr[0]['column']; // Column index
			$order = $columnName_arr[$columnIndex]['data']; // Column name
			$dir = $order_arr[0]['dir']; // asc or desc
			
			$user = Auth::user(); 
			$role = $user->role;
			$id = $user->id;
			
			// Base query for users
			$query = User::with('createdBy:id,name') 
				->where('role', 'user')
				->where('kyc_status', $kycStatus);
			 
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
			if(config('permission.clients.add'))
			{
				$actionButtons .= '<a href="javascript:;" class="btn btn-icon waves-effect waves-light action-icon mr-1" data-toggle="tooltip" title="Recharge mannualy" data-id="'.$value->id.'" data-amount="'.$value->wallet_amount.'" onclick="rechargeUser(this, event)"> <i class="mdi mdi-refresh"></i> </a>';
			}
			if(config('permission.clients.edit'))
			{
				$actionButtons .=  '<a href="'.url('users/edit', $value->id).'?kyc_status='.$value->kyc_status.'" class="btn btn-icon waves-effect waves-light action-icon mr-1" data-toggle="tooltip" title="Update User"> <i class="mdi mdi-pencil"></i> </a>';
			}
			if(config('permission.clients.delete'))
			{
				$actionButtons .= '<a href="'.url('users/delete', $value->id).'" class="btn btn-icon waves-effect waves-light action-icon" data-toggle="tooltip" title="Delete User" onClick="deleteRecord(this,event);"> <i class="mdi mdi-trash-can-outline"></i> </a>'; 
			}
			if(config('permission.clients.edit'))
			{
				$actionButtons .= '<a href="'.url('users/commission', $value->id).'" class="btn btn-icon waves-effect waves-light action-icon" data-toggle="tooltip" title="User Commission"> <i class="mdi mdi-percent"></i> </a>'; 
			}
			return $actionButtons;
		} 
		
		public function userCommission($userId)
		{
			return view('users.commission');
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
			if (auth()->user()->role === 'user') {
				abort(403, 'Permission denied');
			} 
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
			if(config('permission.client_kyc_request.edit'))
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
