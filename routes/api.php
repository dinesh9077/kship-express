<?php
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Route; 
	use App\Http\Controllers\Api\AuthController;
	use App\Http\Controllers\Api\CommonController;
	use App\Http\Controllers\Api\WarehouseController;
	use App\Http\Controllers\Api\OrderController;
	
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
		Route::post('rate-calculator',   [CommonController::class, 'rateCalculator']);  
		
		Route::group(['prefix'=>'warehouse'], function(){ 
			Route::post('/list', [WarehouseController::class, 'index']);    
			Route::post('/create', [WarehouseController::class, 'storeWarehouse']);  
			Route::post('/update/{id}', [WarehouseController::class, 'updateWarehouse']);  
		});
		  
		Route::group(['prefix'=>'customer'], function(){ 
			Route::post('/list', [WarehouseController::class, 'customerList']);   
			Route::post('/create', [WarehouseController::class, 'storeCustomer']);  
			Route::post('/update/{id}', [WarehouseController::class, 'updateCustomer']);  
			Route::post('/delete/{id}', [WarehouseController::class, 'deleteCustomer']);   
		});
			
		Route::group(['prefix'=>'order'], function()
		{ 
			Route::post('/list', [OrderController::class, 'index']);    
			Route::get('/filter', [OrderController::class, 'filterList']);    
			 
			Route::get('/details/{id}', [OrderController::class,'orderDetails']);  
			
			Route::post('/create', [OrderController::class,'orderStore']);  
			Route::get('/edit/{id}', [OrderController::class,'orderEdit']);   
			Route::post('/update/{id}', [OrderController::class,'orderUpdate']);  
			Route::get('/cancel/{id}', [OrderController::class,'orderCancel']);
			
			Route::get('/bulk-create', [OrderController::class,'orderBulkCreate'])
			->middleware('permission:bulk_order.add');
			Route::post('/bulk-store', [OrderController::class, 'orderBulkStore']);
			
			Route::get('/shipment/charge/{orderId}', [OrderController::class, 'orderShipCharge']);   
			Route::post('/ship/now', [OrderController::class, 'orderShipNow']);
			 
			Route::get('/cancel-api/{id}', [OrderController::class,'orderCancelApi']); 
			Route::get('/tracking-history/{id}', [OrderController::class,'orderTrackingHistory']);  
			
			Route::get('/shipping-lable/{orderId}', [OrderController::class, 'orderShippingLableDownload']);
			Route::get('/waybill-copy/{orderId}', [OrderController::class, 'orderWayBillCopy']); 
			
			Route::get('/download-label/{id}', [OrderController::class, 'orderLableDownload']);
			Route::get('/download/all-lable', [OrderController::class, 'alllabeldownload']);  
			Route::post('/warehouse/add', [OrderController::class, 'orderwarehouseAdd']);  
		});	
	});	