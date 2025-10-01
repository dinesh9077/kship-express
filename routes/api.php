<?php
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Route; 
	use App\Http\Controllers\Api\AuthController;
	use App\Http\Controllers\Api\CommonController;
	use App\Http\Controllers\Api\WarehouseController;
	use App\Http\Controllers\Api\OrderController;
	use App\Http\Controllers\Api\UserKycController;
	use App\Http\Controllers\Api\WeightController;
	use App\Http\Controllers\Api\TicketController;
	use App\Http\Controllers\Api\ReportController; 
	
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

		Route::post('profile-update', [CommonController::class, 'updateProfile']);
		Route::post('change-password', [CommonController::class, 'updatePassword']);
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
			Route::post('/cancel/{id}', [OrderController::class,'orderCancel']);
			Route::post('/shipment-cancel/{id}', [OrderController::class,'orderCancelApi']); 
			 
			Route::post('/shipment/charge/{orderId}', [OrderController::class, 'orderShipCharge']);   
			Route::post('/ship/now', [OrderController::class, 'orderShipNow']);
			 
			Route::post('/tracking-history/{id}', [OrderController::class,'orderTrackingHistory']);  
			  
			Route::get('/download-label/{id}', [OrderController::class, 'orderLableDownload']);
			Route::post('/download/all-lable', [OrderController::class, 'alllabeldownload']);   
			
			Route::post('/bulk-store', [OrderController::class, 'orderBulkStore']);
		});	
		
		Route::group(['prefix'=>'user-kyc'], function()
		{
			Route::post('/pancard/update', [UserKycController::class, 'kycUserPancardUpdate']);
			Route::post('/aadhar/update', [UserKycController::class, 'kycUserAadharUpdate']);  
			Route::post('/gst/update', [UserKycController::class, 'kycUserGSTUpdate']);
			Route::post('/bank/update', [UserKycController::class, 'kycUserBankUpdate']);  
		});
		
		// Remmitance Reports
		Route::group(['prefix'=> 'cod-remmitance'], function () { 
			Route::post('/list', [OrderController::class, 'codRemittance']);  
			Route::get('/download-excel/{id}', [OrderController::class, 'downloadRemittanceExcel']); 
		}); 
		
		Route::group(['prefix'=> 'cod-payout'], function () {
			Route::post('/list',[OrderController::class, 'codPayout']); 
			Route::post('/store',[OrderController::class, 'storePayout']); 
		});
		
		//Weight Management
		Route::group(['prefix'=>'weight'], function()
		{  
			Route::post('/descripencies/list', [WeightController::class, 'index']);
			Route::get('/descripencies/remark/{id}', [WeightController::class, 'weightRemark']);
			Route::post('/descripencies/remark/store', [WeightController::class, 'remarkStore']);  
			Route::post('/descripencies/accepted/{id}', [WeightController::class, 'weightAccepted']); 
		});  
		
		//Ticket
		Route::group(['prefix'=>'ticket'], function()
		{
			Route::post('/list', [TicketController::class, 'index']); 
			Route::post('/store', [TicketController::class, 'ticketStore']);
			Route::get('/view-ticket/{id}', [TicketController::class, 'ticketView']);
			Route::post('/remark/store', [TicketController::class, 'remarkStore']); 
		});
		
		Route::prefix('report')->name('report.')->group(function ()
		{   
			Route::post('/order/list', [ReportController::class, 'reportOrderList']);
			Route::post('/export-orders', [ReportController::class, 'reportOrderExport']);
 
			Route::post('/passbook/list', [ReportController::class, 'passbookReportList']); 
			 
			Route::post('billing-invoice/list', [ReportController::class, 'billingInvoiceList']);
			Route::get('billing-invoice/pdf/{id}', [ReportController::class, 'billingInvoicePdf']);
			Route::get('billing-invoice/excel/{id}', [ReportController::class, 'billingInvoiceExcel']);
			 
			Route::post('/recharge/list', [ReportController::class, 'rechargeList']);
		});
	});	