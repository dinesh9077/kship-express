<?php
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Route; 
	use App\Http\Controllers\Api\AuthController;
	use App\Http\Controllers\Api\CommonController;
	
	/*
		|--------------------------------------------------------------------------
		| API Routes
		|--------------------------------------------------------------------------
		|
		| Here is where you can register API routes for your application. These
		| routes are loaded by the RouteServiceProvider within a group which
		| is assigned the "api" middleware group. Enjoy building your API!
		|
	*/
	
	Route::post('/track-order/{orderId}', 'App\Http\Controllers\ApiController@trackOrder');
	
	Route::post('register', [AuthController::class, 'register']);
	Route::post('resend-otp', [AuthController::class, 'resendOtp']);
	Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
	Route::post('login',    [AuthController::class, 'login']);
	Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
	
	// authenticated routes
	Route::middleware('auth:api')->group(function () 
	{
		Route::post('logout',[AuthController::class, 'logout']); 
		Route::get('user',   [AuthController::class, 'user']);
		
		Route::get('dashboard',   [CommonController::class, 'dashboard']);  
	});	