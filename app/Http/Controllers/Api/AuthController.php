<?php
	
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use App\Models\User;
	use App\Models\Notification;
	use App\Models\UserOtp;
	use Illuminate\Support\Facades\Hash;
	use Carbon\Carbon; 
	use App\Traits\ApiResponse; 
	use Illuminate\Support\Facades\Mail;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Support\Facades\Http;
	use App\Mail\OtpMail;
	use App\Mail\ForgotPasswordMail;
	use Illuminate\Support\Str;
	
	class AuthController extends Controller
	{
		use ApiResponse;
		 
		public function register(Request $request)
		{ 
			try { 	
				$validator = Validator::make($request->all(), [
					'company_name' => 'required|string|max:255',
					'name'         => 'required|string|max:255',
					'mobile'       => 'required|digits_between:8,15|unique:users,mobile',
					'email'        => 'required|email|max:255|unique:users,email',
					'password'     => 'required|string|min:8',
					'gender'       => 'required|in:Male,Female,Other',
					'address'      => 'required|string|max:500',
					'zip_code'     => 'required|digits_between:4,10',
					'city'         => 'required|string|max:255',
					'state'        => 'required|string|max:255',
					'country'      => 'required|string|max:255',
				]);
				
				if ($validator->fails()) { 
					return $this->validateResponse('Validation failed', 422, $validator->errors());
				}
				 
				if(!isset($request->is_verify_otp) && $request->is_verify_otp == 0)
				{  
					$otp = rand(100000, 999999); 
					UserOtp::updateOrCreate(
						['email' => $request->email],
						[
							'otp' => $otp,
							'expires_at' => now()->addMinutes(5)
						]
					);
 
					Mail::to($request->email)->send(new OtpMail($request->name, $otp)); 
					return $this->successResponse(['type' => 'mail'], 'The mail send you have proveided email.');  
				}
				
				// Create the user
				$user = User::create([
					'company_name' => $request->company_name,
					'name' => $request->name,
					'email' => $request->email,
					'mobile' => $request->mobile,
					'xpass' => $request->password,
					'password' => Hash::make($request->password),
					'status' => 1,
					'charge_type' => 1,
					'charge' => 0,
					'otp' => null,
					'address' => $request->address,
					'state' => $request->state,
					'city' => $request->city,
					'country' => $request->country,
					'zip_code' => $request->zip_code,
					'gender' => $request->gender,
					'created_at' => now(),
					'updated_at' => now(),
				]);
				
				Notification::create([
					'task_id' => $user->id,
					'type' => 'New User',
					'role' => 'admin',
					'text' => $request->name . ' is a new user who has successfully registered.',
					'created_at' => now(),
					'updated_at' => now()
				]);
				
				Notification::create([
					'user_id' => $user->id,
					'task_id' => $user->id,
					'type' => 'New User',
					'role' => 'user',
					'text' => 'Your registration is now complete. Thank you.',
					'created_at' => now(),
					'updated_at' => now()
				]);
				 
				$tokenResult = $user->createToken('Personal Access Token');
				$token = $tokenResult->accessToken; 
				$user->token = $token;
				$user->type = 'register';
				
				DB::commit();
				
				return $this->successResponse($user, 'The registration has been successfully created.'); 
			} 
			catch (\Exception $e) 
			{
				DB::rollBack();
				return $this->errorResponse('Failed to send OTP. Please try again.', 500); 
			}
		}
		
		public function resendOtp(Request $request)
		{
			$validator = Validator::make($request->all(), [
				'name' => 'required|string|max:255',
				'email' => 'required|email|max:255',
			]);

			if ($validator->fails()) {
				return $this->validateResponse('Validation failed', 422, $validator->errors());
			}

			try {
				$otp = rand(100000, 999999);

				UserOtp::updateOrCreate(
					['email' => $request->email],
					[
						'otp' => $otp,
						'expires_at' => now()->addMinutes(10)
					]
				); 
				
				Mail::to($request->email)->send(new OtpMail($request->name, $otp)); 
				return $this->successResponse([], 'New OTP sent successfully.');
			} catch (\Exception $e) {
				return $this->errorResponse('Failed to resend OTP. Please try again.', 500);
			}
		}
  
		public function verifyOtp(Request $request)
		{  
			$validator = Validator::make($request->all(), [
				'email' => 'required|email|max:255',
				'otp'   => 'required|digits:6',
			]);

			if ($validator->fails()) {
				return $this->validateResponse('Validation failed', 422, $validator->errors());
			}

			$userOtp = UserOtp::where('email', $request->email)->first();

			if (!$userOtp || $userOtp->otp != $request->otp) {
				return $this->errorResponse('Invalid OTP.', 400);
			}

			if ($userOtp->expires_at < now()) {
				return $this->errorResponse('OTP expired.', 400);
			}
			return $this->successResponse([], 'OTP has been successfully verified.'); 
		}
		
		public function forgotPassword(Request $request)
		{  
			$validator = Validator::make($request->all(), [
				'email' => 'required|email|max:255' 
			]);

			if ($validator->fails()) {
				return $this->validateResponse('Validation failed', 422, $validator->errors());
			}
			
			$user = User::where('email', $request->email)->first();
			if(!$user)
			{
				return $this->errorResponse('user not found.', 400); 
			} 
			
			DB::beginTransaction(); 
			try { 
				$newPassword = Str::random(8);  
				$data = [
					'name' => $user->name,
					'email' => $user->email,
					'password' => $newPassword,
				];
			
				$user->xpass = $newPassword;
				$user->password = Hash::make($newPassword);
				$user->save();
 
				Mail::to($user->email)->send(new ForgotPasswordMail($data)); 
				DB::commit();
				return $this->successResponse([], 'A reset password email has been sent to your email address.'); 
			} catch (\Exception $e) { 
				DB::rollBack();
				return $this->errorResponse('Failed to send reset password email. Please try again later.', 500);	 
			}
		} 
		 
		public function login(Request $request)
		{
			$credentials = $request->only('email', 'password');
			$user = User::where('email', $request->email)->first();
			if($user->role != "user")
			{
				return $this->errorResponse('Invalid credentials', 401);
			}
			if (!Auth::attempt($credentials)) {
				return $this->errorResponse('Invalid credentials', 401);
			}
			
			$user = Auth::user();
			$user->tokens()->update(['revoked' => true]);
			
			$tokenResult = $user->createToken('Personal Access Token');
			$token = $tokenResult->accessToken;
			$user->token = $token;
			return $this->successResponse($user, 'User registered successfully');
		}

		public function user(Request $request)
		{
			$user = $request->user();

			$user->profile_image = $user->profile_image
				? url('storage/profile/' . $user->profile_image)
				: asset('assets/images/profile-logo.png');

			return $this->successResponse($user, 'User detail fetched.');
		}
 
		public function logout(Request $request)
		{
			$user = $request->user();
			 
			$token = $user->token();
			if ($token) { 
				$token->revoke();
				 
				DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $token->id)
                ->update(['revoked' => true]);
			}
			return $this->successResponse([], 'Successfully logged out.'); 
		}
	}
