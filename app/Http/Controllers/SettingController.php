<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use File,Auth,DB,Hash;
	use App\models\Setting;
	use App\models\User;
	class SettingController extends Controller
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
			return view('setting.general');
		}
		
		public function updateGeneral(Request $request)
		{ 
			$data = $request->except('_token','login_logo','header_logo','fevicon_icon'); 
			$path_avatar = "storage/app/public/settings";
			
			// Create directory if it doesn't exist
			if (!File::isDirectory($path_avatar)) {
				File::makeDirectory($path_avatar, 0777, true, true);
			}
			
			// Initialize the transaction
			DB::beginTransaction();

			try {
				// Handle file uploads
				$files = ['login_logo', 'header_logo', 'fevicon_icon'];
				foreach ($files as $file) {
					if (!empty($request->file($file))) {
						$photo_image = $request->file($file);  
						$getAvatar = time().rand(111111,999999) . '.' . $photo_image->getClientOriginalExtension();
						$photo_image->move($path_avatar, $getAvatar);  
						$data[$file] = $getAvatar; // Assign file to the data array
					}
				}

				// Update or insert settings
				foreach ($data as $key => $value) {
					$setting = Setting::where('name', $key)->first();
					if ($setting) {
						// Update existing setting
						$setting->update(['value' => $value, 'updated_at' => now()]);
					} else {
						// Insert new setting
						Setting::create(['name' => $key, 'value' => $value, 'created_at' => now(), 'updated_at' => now()]);
					}
				}

				// Commit the transaction
				DB::commit();

				return redirect()->route('general-setting')->with('success', 'The general setting has been updated successfully.');
			} catch (\Exception $e) {
				// Rollback the transaction in case of an error
				DB::rollBack();

				// Return error response
				return redirect()->route('general-setting')->with('error', 'Failed to update general settings: ' . $e->getMessage());
			}
		}

		
		// Profile	
		public function profile()
		{
		    $user = Auth::user(); 
			return view('setting.profile', compact('user'));
		}
		
		public function updateProfile(Request $request)
		{
			try {
				DB::beginTransaction(); // Start transaction

				$user_id = Auth::id();
				$data = $request->except('_token', 'profile_image');

				if ($request->hasFile('profile_image')) {
					$photo_image = $request->file('profile_image');  
					$getAvatar = time() . rand(111111, 999999) . '.' . $photo_image->getClientOriginalExtension();
					$path_avatar = storage_path("app/public/profile");

					// Ensure directory exists
					if (!File::exists($path_avatar)) {
						File::makeDirectory($path_avatar, 0777, true, true);
					}

					// Move file
					$photo_image->move($path_avatar, $getAvatar);
					$data['profile_image'] = $getAvatar;
				}

				// Update user profile
				User::whereId($user_id)->update($data);

				DB::commit(); // Commit transaction

				return redirect()->route('profile')->with('success', 'Your profile has been updated successfully.');

			} catch (\Exception $e) {
				DB::rollBack(); // Rollback if an error occurs
				return redirect()->route('profile')->with('error', 'An error occurred while updating your profile. Please try again.');
			}
		}
 
		// Change Password	
		public function changePassword()
		{
			return view('setting.change-password');
		}
		
		public function updatePassword(Request $request)
		{
			// Validate request input
			$request->validate([
				'old_password' => ['required'],
				'new_password' => ['required', 'min:8', 'confirmed'], // Laravel 'confirmed' rule removes the need for manual check
			]);
				
			DB::beginTransaction();
			try {
				// Get the authenticated user
				$user = Auth::user();
 
				// Check if old password is correct
				if (!Hash::check($request->old_password, $user->password)) { 
					return back()->with('error', 'The old password is incorrect.');
				}

				// Update user password
				$user->update([
					'xpass' => $request->new_password,
					'password' => Hash::make($request->new_password),
				]); 
				
				DB::commit();  
				// Logout the user after password change
				Auth::logout();

				// Redirect to login page with success message
				return redirect()->route('login')->with('success', 'Your password has been changed successfully. Please log in again.');
			
			} catch (\Exception $e) {
				DB::rollBack();
				return back()->with('error', 'An error occurred while updating your password. Please try again.');
			}
		}
 
		public function lablePreferance()
		{
			return view('setting.label-preferance');
		}
		
		public function updateLablePref(Request $request)
		{ 
			$data = [];
			$data['order_value'] = (isset($request->order_value))?$request->order_value:0;
			$data['shipper_mobile'] = (isset($request->shipper_mobile))?$request->shipper_mobile:0;
			$data['shipper_address'] = (isset($request->shipper_address))?$request->shipper_address:0;
			$data['consignee_contact'] = (isset($request->consignee_contact))?$request->consignee_contact:0;
			foreach($data as $key => $row)
			{	
				$check = Setting::where('name',$key)->first();
				if(!empty($check))
				{
					Setting::where('name',$key)->update(['value'=>$row,'updated_at'=>date('Y-m-d H:i:s')]);  
				}
				else
				{
					if(!empty($row))
					{
						Setting::insert(['name'=>$key,'value'=>$row,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
					}
				}
			}   
			return back()->with('success','The label setting have been updated successfully.');
		}
		
	}
	
