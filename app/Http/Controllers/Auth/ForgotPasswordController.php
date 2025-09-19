<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Models\User; 
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */ 
    use SendsPasswordResetEmails;
	
    public function forgotPassword()
    {
        return view("auth.forgot");
    }
	
    public function forgotPasswordSend(Request $request)
	{
		// Validate input
		$request->validate([
			'email' => 'required|email|exists:users,email',
		]);

		// Retrieve user
		$user = User::where('email', $request->email)->firstOrFail();

		// Generate new password
		$newPassword = Str::random(8); // Secure random string

		// Prepare email data
		$data = [
			'name' => $user->name,
			'email' => $user->email,
			'password' => $newPassword,
		];

		// Begin transaction
		DB::beginTransaction();

		try {
			// Update user password
			$user->xpass = $newPassword; // If xpass is used for temporary storage
			$user->password = Hash::make($newPassword);
			$user->save();

			// Send reset email
			Mail::to($user->email)->send(new ForgotPasswordMail($data));

			// Commit transaction if mail is sent successfully
			DB::commit();

			return redirect()->route('login')->with('success', 'A reset password email has been sent to your email address.');
		} catch (\Exception $e) {
			// Rollback if an error occurs (ensures password is not updated if email fails)
			DB::rollBack();

			return redirect()->route('forget_password')->with('error', 'Failed to send reset password email. Please try again later.');
		}
	}
}
