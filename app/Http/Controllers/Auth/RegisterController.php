<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Mail\OtpMail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    } 
	
    public function postRegister(Request $request)
	{  
		// Generate OTP
		$generatedOTP = rand(100000, 999999); // 6-digit OTP
		  	
		try { 
			 
			if(User::where("email", $request->email)->exists())
			{
				return response()->json(['status' => 'error', 'msg' => 'The email has already exists.']);
			}
			
			if(User::where("mobile", $request->mobile)->exists())
			{
				return response()->json(['status' => 'error', 'msg' => 'The mobile has already exists.']);
			}
		
			if(!isset($request->is_verify_otp) && $request->is_verify_otp == 0)
			{ 
				Mail::to($request->email)->send(new OtpMail($request->name, $generatedOTP));
				return response()->json(['status' => 'mail', 'msg' => 'The mail send you have proveided email', 'otp' => $generatedOTP]);
			}
		
			DB::beginTransaction();

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
				'otp' => $generatedOTP,
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
 
			DB::commit();
  
			return response()->json(['status' => 'success', 'msg' => 'The registration has been successfully created.']);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json(['status' => 'error', 'msg' => 'Failed to send OTP. Please try again.'. $e->getMessage()]); 
		}
	}
	
	public function verifyOTP(Request $request) 
	{  
        $otp = $request->input('otp'); 
        $generatedOtp = $request->input('generatedOtp');  
		
		if($generatedOtp != $otp)
		{ 
			return response()->json(['status' => 'error', 'msg' => 'Invalid OTP, Please Try again.']); 
		}
		return response()->json(['status' => 'success', 'msg' => 'OTP verified successfully']);  
    }
	
    public function resend(Request $request)
    {  
		$generatedOTP = rand(100000, 999999); // 6-digit OTP
		try
		{ 
			Mail::to($request->email)->send(new OtpMail($request->name, $generatedOTP));
			return response()->json(['status' => 'mail', 'msg' => 'The mail send you have proveided email', 'otp' => $generatedOTP]);
		} catch (\Exception $e) { 
			return response()->json(['status' => 'error', 'msg' => 'Failed to send OTP. Please try again.'. $e->getMessage()]); 
		}
    } 
}
