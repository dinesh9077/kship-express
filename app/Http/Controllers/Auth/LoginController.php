<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; 
use App\Models\User;
use DB,Auth,File, Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
	 
	public function postLogin(Request $request)
	{
		$request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
		
		$user = User::whereEmail($request->email)->first();
	   // $user->update(['password' => Hash::make('123456789'), 'xpass' => '123456789']);
	   // die;
		if(empty($user))
		{
			return redirect("login")->with('error','These credentials do not match our records.');
		}
		if($user->role != "admin")
		{
			if($user->status == 0)
			{
				return redirect("login")->with('error','These user is not active by admin.');
			}
			/* if($user->kyc_status == 0)
			{
				return redirect("login")->with('error','These user is not appreved kyc by admin.');
			}   */
		}
        $credentials = $request->only('email', 'password'); 
        if (Auth::attempt($credentials)) 
		{ 
            return redirect()->intended('home')->with('success','You have Successfully loggedin.');
        }
  
        return redirect("login")->with('error','These credentials do not match our records.');
	}
	
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
