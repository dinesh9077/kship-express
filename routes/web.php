<?php
	
	use Illuminate\Support\Facades\Route; 
	/*
		|--------------------------------------------------------------------------
		| Web Routes
		|--------------------------------------------------------------------------
		|
		| Here is where you can register web routes for your application. These
		| routes are loaded by the RouteServiceProvider within a group which
		| contains the "web" middleware group. Now create something great!
		|
	*/
	
	Route::get('/', function () { 
		return redirect()->route('login');
	});
	
	Route::get('test/{id}', [App\Http\Controllers\ApiController::class, 'test']); 
	Route::get('order_track/{id}', [App\Http\Controllers\ApiController::class, 'order_track']);
	
	Auth::routes();
	
	Route::get('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'forgotPassword'])->name('forgot-password'); 
	Route::post('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'forgotPasswordSend'])->name('forgot-password.post'); 
	
	Route::post('post-login', [App\Http\Controllers\Auth\LoginController::class, 'postLogin'])->name('login.post');
	
	Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'postRegister'])->name('register.store'); 
	Route::post('verfiy-otp', [App\Http\Controllers\Auth\RegisterController::class, 'verifyOTP'])->name('register.verify-otp'); 
	Route::post('resend-otp', [App\Http\Controllers\Auth\RegisterController::class, 'resend'])->name('register.resend-otp'); 
	
	Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home'); 
	Route::get('/notification', [App\Http\Controllers\HomeController::class, 'notification'])->name('notification');
	Route::get('/notification/clear-all', [App\Http\Controllers\HomeController::class, 'notificationClearAll'])->name('notification.clear-all');
	Route::get('/check-amount', [App\Http\Controllers\HomeController::class,'checkAmount'])->name('check-amount');
	Route::get('/lowbalance', [App\Http\Controllers\HomeController::class,'lowbalance'])->name('lowbalance');
	Route::get('/kycpending', [App\Http\Controllers\HomeController::class,'kycpending'])->name('kycpending');
	
	
	//Profile 
	Route::get('/profile', [App\Http\Controllers\SettingController::class, 'profile'])->name('profile');  
	Route::post('/update', [App\Http\Controllers\SettingController::class, 'updateProfile'])->name('profile.update');  
	 
	
	//Change Password
	Route::group(['prefix'=>'change-password'], function(){ 
		Route::get('/', [App\Http\Controllers\SettingController::class, 'changePassword'])->name('change-password');  
		Route::post('/update', [App\Http\Controllers\SettingController::class, 'updatePassword'])->name('update.change-password');  
	});
	
	//Setting
	Route::group(['prefix'=>'setting'], function(){ 
		Route::get('/general', [App\Http\Controllers\SettingController::class, 'index'])->name('general-setting')->middleware('permission:general_setting.view');  
		Route::post('/general', [App\Http\Controllers\SettingController::class, 'updateGeneral']);  
		Route::get('/lable-preferance', [App\Http\Controllers\SettingController::class, 'lablePreferance'])->name('lable.preferance');  
		Route::post('/update-preferance', [App\Http\Controllers\SettingController::class, 'updateLablePref']);  
	});
	
	//Customer
	Route::group(['prefix'=>'customer'], function(){ 
		Route::get('/', [App\Http\Controllers\CustomerController::class, 'index'])->name('customer')->middleware('permission:client.view');  
		Route::post('/ajax', [App\Http\Controllers\CustomerController::class, 'ajaxCustomer'])->name('customer.ajax');  
		Route::get('/add', [App\Http\Controllers\CustomerController::class, 'addCustomer'])->name('customer.add')->middleware('permission:client.add');  
		Route::post('/store', [App\Http\Controllers\CustomerController::class, 'storeCustomer'])->name('customer.store');  
		Route::get('/delete/{id}', [App\Http\Controllers\CustomerController::class, 'deleteCustomer']);  
		Route::get('/edit/{id}', [App\Http\Controllers\CustomerController::class, 'editCustomer'])->middleware('permission:client.edit');  
		Route::post('/update/{id}', [App\Http\Controllers\CustomerController::class, 'updateCustomer'])->name('customer.update');  
	});
	
	//Vendor
	Route::group(['prefix'=>'vendor'], function(){ 
		Route::get('/', [App\Http\Controllers\VendorController::class, 'index'])->name('vendor');  
		Route::post('/ajax', [App\Http\Controllers\VendorController::class, 'ajaxVendor'])->name('vendor.ajax');  
		Route::get('/add', [App\Http\Controllers\VendorController::class, 'addVendor'])->name('vendor.add');  
		Route::post('/store', [App\Http\Controllers\VendorController::class, 'storeVendor'])->name('vendor.store');  
		Route::get('/delete/{id}', [App\Http\Controllers\VendorController::class, 'deleteVendor']);  
		Route::get('/edit/{id}', [App\Http\Controllers\VendorController::class, 'editVendor']);  
		Route::post('/update/{id}', [App\Http\Controllers\VendorController::class, 'updateVendor'])->name('vendor.update');  
	});
	
	//Warehouse
	Route::group(['prefix'=>'warehouse'], function()
	{ 
		Route::get('/', [App\Http\Controllers\WarehouseController::class, 'index'])->name('warehouse.index')->middleware('permission:warehouse.view');  
		Route::post('/ajax', [App\Http\Controllers\WarehouseController::class, 'ajaxWarehouse'])->name('warehouse.ajax');  
		Route::get('/add', [App\Http\Controllers\WarehouseController::class, 'createWarehouse'])->name('warehouse.add')->middleware('permission:warehouse.add'); 
		Route::post('/store', [App\Http\Controllers\WarehouseController::class, 'storeWarehouse'])->name('warehouse.store');  
		Route::get('/edit/{id}', [App\Http\Controllers\WarehouseController::class, 'editWarehouse'])->name('warehouse.edit')->middleware('permission:warehouse.edit');
		Route::post('/update/{id}', [App\Http\Controllers\WarehouseController::class, 'updateWarehouse'])->name('warehouse.update');  
		Route::get('/delete/{id}', [App\Http\Controllers\WarehouseController::class, 'deleteWarehouse'])->name('warehouse.delete');  
	});
	
	//Pikup Request
	Route::group(['prefix'=>'pickup-request'], function()
	{ 
		Route::get('/', [App\Http\Controllers\WarehouseController::class, 'pickupRequestList'])->name('pickup.request.index')->middleware('permission:pickup_request.view');
		Route::post('/ajax', [App\Http\Controllers\WarehouseController::class, 'pickupRequestAjax'])->name('pickup.request.ajax');  
		Route::post('/create', [App\Http\Controllers\WarehouseController::class, 'createPickupRequest'])->name('pickup.request.create');    
		Route::get('/cancel/{id}', [App\Http\Controllers\WarehouseController::class, 'cancelPickupRequest'])->name('pickup.request.cancel');  
	});
	
	//User
	Route::group(['prefix'=>'users'], function(){ 
		Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('users')->middleware('permission:clients.view');  
		Route::post('/ajax', [App\Http\Controllers\UserController::class, 'ajaxUser'])->name('users.ajax');  
		Route::get('/create', [App\Http\Controllers\UserController::class, 'createUser'])->name('users.create')->middleware('permission:clients.add');  
		Route::post('/store', [App\Http\Controllers\UserController::class, 'storeUser'])->name('users.store');  
		Route::get('/edit/{id}', [App\Http\Controllers\UserController::class, 'editUser'])->middleware('permission:clients.edit');
		Route::post('/update/{id}', [App\Http\Controllers\UserController::class, 'updateUser'])->name('users.update');  
		Route::get('/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteUser']);  
		
		Route::post('/recharge/offline', [App\Http\Controllers\UserController::class, 'rechargeOffline']);    
		
		//KYC USER
		Route::get('/kyc/edit', [App\Http\Controllers\UserController::class, 'kycUser'])->name('users.kyc.edit');  
		Route::post('/kyc/pancard/update', [App\Http\Controllers\UserController::class, 'kycUserPancardUpdate'])->name('users.pancard.update');
		Route::post('/kyc/aadhar/update', [App\Http\Controllers\UserController::class, 'kycUserAadharUpdate'])->name('users.aadhar.update');  
		Route::post('/kyc/gst/update', [App\Http\Controllers\UserController::class, 'kycUserGSTUpdate'])->name('users.gst.update');
		Route::post('/kyc/bank/update', [App\Http\Controllers\UserController::class, 'kycUserBankUpdate'])->name('users.bank.update');  
		
		//KYC ADMIN
		Route::get('/kyc/request', [App\Http\Controllers\UserController::class, 'kycUserRequest'])->name('users.kyc.request')->middleware('permission:client_kyc_request.view'); 
		Route::post('/kyc/request-ajax', [App\Http\Controllers\UserController::class, 'kycUserRequestAjax'])->name('users.kyc-request.ajax');  
		Route::get('/kyc/verified/{id}', [App\Http\Controllers\UserController::class, 'kycUserRequestVerified'])->middleware('permission:client_kyc_request.edit');   
		Route::post('/kyc/verified', [App\Http\Controllers\UserController::class, 'kycUserVerified'])->name('users.kyc.verified');  
		Route::post('/kyc/rejected', [App\Http\Controllers\UserController::class, 'kycUserRejected'])->name('users.kyc.rejected');  
	});
	 
	//New User 
	Route::get('/new/user', [App\Http\Controllers\UserController::class, 'newuser'])->name('newuser');  
	Route::post('/new/ajaxnewUser', [App\Http\Controllers\UserController::class, 'ajaxnewUser'])->name('newuser.ajaxnewUser');  
	Route::post('/new/role', [App\Http\Controllers\UserController::class, 'storeRoleUser'])->name('newuser.storeRoleUser');
	 
	//staffs
    Route::get('/staff', [App\Http\Controllers\StaffController::class, 'staff'])->name('staff');
    Route::post('/staff/ajax', [App\Http\Controllers\StaffController::class, 'staffAjax'])->name('staff.ajax');
    Route::get('/staff/create', [App\Http\Controllers\StaffController::class, 'staffCreate'])->name('staff.create');
    Route::post('/staff/store', [App\Http\Controllers\StaffController::class, 'staffStore'])->name('staff.store');
    Route::get('/staff/edit/{id}', [App\Http\Controllers\StaffController::class, 'staffEdit'])->name('staff.edit');
    Route::post('/staff/update/{id}', [App\Http\Controllers\StaffController::class, 'staffUpdate'])->name('staff.update');
    Route::get('/staff/delete/{id}', [App\Http\Controllers\StaffController::class, 'staffDelete'])->name('staff.delete');
    Route::get('/staff/permission/{id}', [App\Http\Controllers\StaffController::class, 'staffPermission'])->name('staff.permission');
    Route::post('/staff/permission/{id}', [App\Http\Controllers\StaffController::class, 'staffPermissionUpdate'])->name('staff.permission-update');
	
	//Roles
    Route::get('/roles', [App\Http\Controllers\StaffController::class, 'roles'])->name('roles');
    Route::post('/roles/ajax', [App\Http\Controllers\StaffController::class, 'rolesAjax'])->name('roles.ajax');
    Route::get('/roles/create', [App\Http\Controllers\StaffController::class, 'rolesCreate'])->name('roles.create');
    Route::post('/roles/store', [App\Http\Controllers\StaffController::class, 'rolesStore'])->name('roles.store');
    Route::get('/roles/edit/{id}', [App\Http\Controllers\StaffController::class, 'rolesEdit'])->name('roles.edit');
    Route::post('/roles/update/{id}', [App\Http\Controllers\StaffController::class, 'rolesUpdate'])->name('roles.update');
    Route::get('/roles/delete/{id}', [App\Http\Controllers\StaffController::class, 'rolesDelete'])->name('roles.delete');
    Route::get('roles/groups/{id}', [App\Http\Controllers\StaffController::class, 'rolesGroups']);
	
    //Permission 
    Route::get('/permission', [App\Http\Controllers\UserController::class, 'permission'])->name('users.permission');  
	Route::post('/permission/ajax', [App\Http\Controllers\UserController::class, 'ajaxUserPermission'])->name('users.permission.ajax');
	Route::post('/permission/store', [App\Http\Controllers\UserController::class, 'storeUserpermission'])->name('users.permission.store');  
	Route::get('/permission/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteUserpermission']);  
	 
	//Shippping Company
	Route::group(['prefix'=>'shipping/company'], function()
	{ 
		Route::get('/', [App\Http\Controllers\ShippingController::class, 'index'])->name('shipping.company')->middleware('permission:shipping_company.view');    
		Route::post('/store', [App\Http\Controllers\ShippingController::class, 'storeShipping'])->name('shipping.company.store');   
		Route::post('/update', [App\Http\Controllers\ShippingController::class, 'updateShipping'])->name('shipping.company.update');
	});
	
	//Shippping Packaging
	Route::group(['prefix'=>'shipment/packaging'], function()
	{ 
		Route::get('/', [App\Http\Controllers\ShippingController::class, 'shipmentPackaging'])->name('shipment.packaging');    
		Route::post('/ajax', [App\Http\Controllers\ShippingController::class, 'shipmentPackagingAjax'])->name('shipping.packaging.ajax'); 
		Route::get('/add', [App\Http\Controllers\ShippingController::class, 'shipmentPackagingAdd'])->name('shipment.packaging.add');    
		Route::post('/store', [App\Http\Controllers\ShippingController::class, 'shipmentPackagingStore'])->name('shipment.packaging.store');    
		Route::get('/edit/{id}', [App\Http\Controllers\ShippingController::class, 'shipmentPackagingEdit']);    
		Route::post('/update', [App\Http\Controllers\ShippingController::class, 'shipmentPackagingUpdate'])->name('shipment.packaging.update');    
		Route::get('/delete/{id}', [App\Http\Controllers\ShippingController::class, 'shipmentPackagingDelete']);    
		
	});
	
	//Shippping Rate
	Route::get('rate/calculator', [App\Http\Controllers\ShippingController::class, 'rateCalculator'])->name('rate.calculator')
	->middleware('permission:rate_calculator.view');    
	Route::post('rate/calculator/show', [App\Http\Controllers\ShippingController::class, 'rateCalculatorShow'])->name('rate.calculator.create');   
	Route::post('rate/freight-breakup', [App\Http\Controllers\ShippingController::class, 'rateFreightBreakup'])->name('rate.freight-breakup');   
	Route::get('rate/pincode/serviciability/{pincode}', [App\Http\Controllers\ShippingController::class, 'ratePincodeServiciable'])->name('rate.pincode.serviceable');
	
	//Product Category
	Route::group(['prefix'=>'product-category'], function()
	{ 
		Route::get('/', [App\Http\Controllers\ShippingController::class, 'productCategory'])->name('product-category');    
		Route::post('/store', [App\Http\Controllers\ShippingController::class, 'storeProductCategory'])->name('product-category.store');   
		Route::post('/update', [App\Http\Controllers\ShippingController::class, 'updateProductCategory'])->name('product-category.update');
		Route::get('/delete/{id}', [App\Http\Controllers\ShippingController::class, 'deleteProductCategory']);  
	});	
	
	Route::group(['prefix'=>'ndr'], function()
	{ 
		Route::get('/', [App\Http\Controllers\NDRController::class, 'index'])->name('ndr');    
		Route::post('/ndrajax', [App\Http\Controllers\NDRController::class, 'ndrAjax'])->name('ndr.ajax');  
		Route::post('/raiserequest', [App\Http\Controllers\NDRController::class, 'raiserequest'])->name('ndr.raiserequest'); 
	});	
	 
	//Order 
	Route::group(['prefix'=>'order'], function()
	{ 
		Route::get('/', [App\Http\Controllers\OrderController::class, 'index'])->name('order')->middleware('permission:order.view');   
		Route::post('/ajax', [App\Http\Controllers\OrderController::class, 'orderAjax'])->name('order.ajax');   
		 
		Route::get('/details/{id}', [App\Http\Controllers\OrderController::class,'orderDetails']);  
		
		Route::get('/create', [App\Http\Controllers\OrderController::class,'orderCreate'])->name('order.create')->middleware('permission:order.add'); 
		Route::post('/store', [App\Http\Controllers\OrderController::class, 'orderStore'])->name('order.store'); 
		Route::get('/edit/{id}', [App\Http\Controllers\OrderController::class,'orderEdit'])->middleware('permission:order.edit');   
		Route::post('/update/{id}', [App\Http\Controllers\OrderController::class,'orderUpdate'])->name('order.update'); 
		Route::get('/delete/{id}', [App\Http\Controllers\OrderController::class,'orderDelete']); 
		Route::get('/clone/{id}', [App\Http\Controllers\OrderController::class,'orderClone']);   
		Route::get('/cancel/{id}', [App\Http\Controllers\OrderController::class,'orderCancel']);
		
		Route::get('/bulk-create', [App\Http\Controllers\OrderController::class,'orderBulkCreate'])->name('order.bulk-create')
		->middleware('permission:bulk_order.add');
		Route::post('/bulk-store', [App\Http\Controllers\OrderController::class, 'orderBulkStore'])->name('order.bulk-store');
		
		Route::get('/shipment/charge/{orderId}', [App\Http\Controllers\OrderController::class, 'orderShipCharge']);   
		Route::post('/ship/now', [App\Http\Controllers\OrderController::class, 'orderShipNow'])->name('order.ship.now');
		 
		Route::get('/cancel-api/{id}', [App\Http\Controllers\OrderController::class,'orderCancelApi']); 
		Route::get('/tracking-history/{id}', [App\Http\Controllers\OrderController::class,'orderTrackingHistory']);  
		
		Route::get('/shipping-lable/{orderId}', [App\Http\Controllers\OrderController::class, 'orderShippingLableDownload'])->name('order.shipping-download');
		Route::get('/waybill-copy/{orderId}', [App\Http\Controllers\OrderController::class, 'orderWayBillCopy']); 
		
		Route::get('/download-label/{id}', [App\Http\Controllers\OrderController::class, 'orderLableDownload'])->name('order.downloadLabel');
		Route::get('/download/all-lable', [App\Http\Controllers\OrderController::class, 'alllabeldownload'])->name('order.download-all-lable'); 
		     
		Route::post('/warehouse/add', [App\Http\Controllers\OrderController::class, 'orderwarehouseAdd'])->name('order.warehouse.address');  
		
		// Warehouse Pickup
		Route::get('/warehouse/list', [App\Http\Controllers\OrderController::class, 'orderWarehouseList'])->name('order.warehouse.list');  
		Route::get('/warehouse/create', [App\Http\Controllers\OrderController::class, 'orderWarehouseCreate'])->name('order.warehouse.create');  
		   
		//Customer
		Route::get('/customer/list', [App\Http\Controllers\OrderController::class, 'orderCustomerList'])->name('order.customer.list');  
		Route::get('/customer/address-list/{customerId}', [App\Http\Controllers\OrderController::class, 'orderCustomerAddressList'])->name('order.customer.address-list'); 
		Route::get('/customer/create', [App\Http\Controllers\OrderController::class, 'orderCustomerCreate'])->name('order.customer.create'); 
		Route::get('/customer-address/create/{customerId}', [App\Http\Controllers\OrderController::class, 'orderCustomerAddressCreate'])->name('order.customer-address.create'); 
		Route::post('/customer-address/store', [App\Http\Controllers\OrderController::class, 'orderCustomerAddressStore'])->name('order.customer-address.store');  
	});	
	
	// Remmitance Reports
	Route::group(['prefix'=> 'order/remmitance'], function () {
		
		Route::get('/', [App\Http\Controllers\OrderController::class, 'orderRemmitance'])->name('order.remmitance')->middleware('permission:remittance.view'); 
		Route::post('/ajax', [App\Http\Controllers\OrderController::class, 'orderRemmitanceAjax'])->name('order.remmitance.ajax');  
		Route::post('/store',[App\Http\Controllers\OrderController::class, 'orderRemmitanceStore'])->name('order.remmitance.store');
		
	});
	
	Route::group(['prefix'=> 'order/codvoucher'], function () {
		
		Route::get('/', [App\Http\Controllers\OrderController::class, 'codVoucher'])->name('order.codvoucher')->middleware('permission:cod_voucher.view'); 
		Route::post('/ajax', [App\Http\Controllers\OrderController::class, 'codVoucherAjax'])->name('order.codvoucher.ajax');  
		Route::post('/generatevouchers',[App\Http\Controllers\OrderController::class, 'generatevouchers'])->name('generatevouchers');
		Route::get('/{voucher_no}',[App\Http\Controllers\OrderController::class, 'viewvoucher'])->name('viewvoucher');
		
	});
	Route::group(['prefix'=> 'order/codpayout'], function () {
	    Route::get('/',[App\Http\Controllers\OrderController::class, 'codPayout'])->name('order.codpayout')
		->middleware('permission:cod_payout.view');
		Route::post('/codPayoutajax',[App\Http\Controllers\OrderController::class, 'codPayoutajax'])->name('codPayoutajax');
		Route::post('/codRemmitance',[App\Http\Controllers\OrderController::class, 'codRemittance'])->name('codRemmitance');
		
	});
	    
	// Daily Reports
	Route::get('/report/order', [App\Http\Controllers\ReportController::class, 'index'])->name('report.order')->middleware('permission:order_report.view'); 
	Route::post('/report/order/ajax', [App\Http\Controllers\ReportController::class, 'reportOrderAjax'])->name('report.order.ajax');
	Route::get('/report/export-orders', [App\Http\Controllers\ReportController::class, 'reportOrderExport'])->name('report.order.export');
	
	Route::get('/report/income', [App\Http\Controllers\ReportController::class, 'incomeReport'])->name('report.income')->middleware('permission:income_report.view'); 
	Route::post('/report/income/ajax', [App\Http\Controllers\ReportController::class, 'incomeReportAjax'])->name('report.income.ajax');
	
	Route::get('/report/payment', [App\Http\Controllers\ReportController::class, 'paymentReport'])->name('report.payment')->middleware('permission:payment_report.view'); 
	Route::post('/report/payment/ajax', [App\Http\Controllers\ReportController::class, 'paymentReportAjax'])->name('report.payment.ajax');
	 
	Route::get('/passbook/', [App\Http\Controllers\ReportController::class, 'passbookReport'])->name('report.passbook')->middleware('permission:passbook_report.view'); 
	Route::post('/passbook/ajax', [App\Http\Controllers\ReportController::class, 'passbookReportAjax'])->name('report.passbook.ajax'); 
	
	Route::get('/daily_report', [App\Http\Controllers\ReportController::class, 'reports'])->name('daily_report'); 
	Route::get('/daily_recharge', [App\Http\Controllers\ReportController::class, 'daily_recharge'])->name('daily_recharge');
	Route::post('/daily_recharge/RechargeAjaxData/', [App\Http\Controllers\ReportController::class, 'RechargeAjaxData'])->name('daily_recharge.RechargeAjaxData'); 
	  
	Route::get('/invoice_report', [App\Http\Controllers\ReportController::class, 'invoice_report'])->name('invoice_report'); 
	Route::post('/invoice_report/invoicetAjax', [App\Http\Controllers\ReportController::class, 'invoicetAjax'])->name('invoicetAjax');
	 
	//Recharge Amount 
	Route::group(['prefix'=>'recharge'], function()
	{  
		Route::get('/list', [App\Http\Controllers\RechargeController::class, 'rechargeList'])->name('recharge.list');
		Route::post('/ajax', [App\Http\Controllers\RechargeController::class, 'rechargeListAjax'])->name('recharge.list.ajax');
		
		Route::post('/wallet/store', [App\Http\Controllers\RechargeController::class, 'rechargeWalletStore'])->name('recharge.wallet.amount'); 
		Route::post('/wallet/response', [App\Http\Controllers\RechargeController::class, 'rechargeWalletResponse'])->name('recharge.wallet.response');
		
		Route::post('/razorpay/wallet', [App\Http\Controllers\RechargeController::class, 'rechargeWalletRazorpay'])->name('recharge.razorpay.wallet');
		 
		Route::get('/list/history', [App\Http\Controllers\RechargeController::class, 'rechargeListAdmin'])->name('recharge.list.history');
		Route::post('/ajax/history', [App\Http\Controllers\RechargeController::class, 'rechargeListAjaxAdmin'])->name('recharge.list.ajax.history');
		
		Route::post('/wallet/action', [App\Http\Controllers\RechargeController::class, 'rechargeWalletAction'])->name('recharge.wallet.action');
	});	
	
	//Ticket
	Route::group(['prefix'=>'ticket'], function()
	{     
		Route::get('/list', [App\Http\Controllers\TicketController::class, 'index'])->name('ticket');
		Route::post('/ajax', [App\Http\Controllers\TicketController::class, 'ticketListAjax'])->name('ticket.list.ajax'); 
		Route::get('/add', [App\Http\Controllers\TicketController::class, 'ticketAdd'])->name('ticket.add');	 
		Route::post('/store', [App\Http\Controllers\TicketController::class, 'ticketStore'])->name('ticket.store');	 
		Route::get('/delete/{id}', [App\Http\Controllers\TicketController::class, 'ticketDelete']);	 
		
		Route::get('/all/list', [App\Http\Controllers\TicketController::class, 'ticketList'])->name('ticket.admin')->middleware('permission:ticket_request.view');
		Route::post('/all/ajax', [App\Http\Controllers\TicketController::class, 'ticketAllListAjax'])->name('ticket.admin.ajax');
		Route::get('/revert_ticket/{id}', [App\Http\Controllers\TicketController::class, 'revert_ticket'])->name('ticket.admin.revert_ticket')->middleware('permission:ticket_request.edit');	
		Route::post('/update/{id}', [App\Http\Controllers\TicketController::class, 'ticketUpdate'])->name('ticket.admin.update_revert');		
		Route::get('/close/{id}', [App\Http\Controllers\TicketController::class, 'ticketClose']);	 
	});	
	
	//Weight Management
	Route::group(['prefix'=>'weight'], function()
	{     
		//ADMIN
		Route::get('/descripencies/all', [App\Http\Controllers\WeightController::class, 'descrepenciesAll'])->name('weight.admin.descripencies')->middleware('permission:weight_descripencies.view');
		Route::post('/raise/excess-weight', [App\Http\Controllers\WeightController::class, 'raiseExcessWeight'])->name('weight.raise.excess-weight');  
		Route::get('/descripencies/history/{id}', [App\Http\Controllers\WeightController::class, 'weightHoistory']);
		Route::get('/descripencies/view_image/{id}', [App\Http\Controllers\WeightController::class, 'weightview_image']);
		Route::get('/descripencies/accepted/{id}', [App\Http\Controllers\WeightController::class, 'weightAccepted']);
		Route::get('/descripencies/reject/{id}', [App\Http\Controllers\WeightController::class, 'weightRejected']);
		Route::post('/descripencies/bycourier', [App\Http\Controllers\WeightController::class, 'weightbycourier']);
		Route::get('/descripencies/auto-accepted', [App\Http\Controllers\WeightController::class, 'autoAccepted'])->name('weight.auto-accepted'); 
		Route::post('/rejetform/store', [App\Http\Controllers\WeightController::class, 'rejectFormStore'])->name('weight.rejetform.store');  
		
		//User 
		Route::get('/descripencies/list', [App\Http\Controllers\WeightController::class, 'index'])->name('weight.descripencies');
		
		Route::get('/freeze/list', [App\Http\Controllers\WeightController::class, 'freezeList'])->name('weight.freeze');
		Route::get('/freeze/add/{id}', [App\Http\Controllers\WeightController::class, 'freezeAdd']);
		Route::post('/freeze/store', [App\Http\Controllers\WeightController::class, 'freezeStore'])->name('weight.freeze.store');
		
		Route::get('/freeze/all', [App\Http\Controllers\WeightController::class, 'freezeAll'])->name('weight.admin.freeze');
		
	});	
	
	//Excel upload Pincode 
	Route::group(['prefix'=>'service'], function()
	{     
		Route::get('/list', [App\Http\Controllers\PincodeController::class, 'index'])->name('service.index');
		Route::post('list/ajax', [App\Http\Controllers\PincodeController::class, 'listAjax'])->name('service.list.ajax');
		Route::post('/import', [App\Http\Controllers\PincodeController::class, 'import'])->name('service.import');
		Route::post('/update/charge', [App\Http\Controllers\PincodeController::class, 'updateCharge'])->name('service.update.charge');
		
	});			