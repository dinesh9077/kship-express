 
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
	
	Route::get('courier-commission', [App\Http\Controllers\CourierCommissionController::class, 'index'])->name('courier.commission')->middleware('permission:general_setting.view');
	Route::post('courier-commission', [App\Http\Controllers\CourierCommissionController::class, 'update'])->name('courier.commission.store');
	Route::get('users/commission/{userId}', [App\Http\Controllers\CourierCommissionController::class, 'userCommission']);
	Route::post('user-courier-commission', [App\Http\Controllers\CourierCommissionController::class, 'userCommissionUpdate'])->name('users.courier.commission.store');
	
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
		Route::post('/kyc/aadhar/send-otp', [App\Http\Controllers\UserController::class, 'kycUserAadharOtp'])->name('users.aadhar.sendOtp');
		Route::post('/kyc/aadhar/update', [App\Http\Controllers\UserController::class, 'kycUserAadharUpdate'])->name('users.aadhar.update');  
		Route::post('/kyc/gst/update', [App\Http\Controllers\UserController::class, 'kycUserGSTUpdate'])->name('users.gst.update');
		Route::post('/kyc/bank/update', [App\Http\Controllers\UserController::class, 'kycUserBankUpdate'])->name('users.bank.update');  
		
		//KYC ADMIN
		Route::get('/kyc/request', [App\Http\Controllers\UserController::class, 'kycUserRequest'])->name('users.kyc.request')->middleware('permission:client_kyc_request.view'); 
		Route::post('/kyc/request-ajax', [App\Http\Controllers\UserController::class, 'kycUserRequestAjax'])->name('users.kyc-request.ajax');
		Route::post('/kyc/update-status', [App\Http\Controllers\UserController::class, 'kycUserUpdateStatus'])->name('users.kyc.update-status');
		Route::get('/kyc/verified/{id}', [App\Http\Controllers\UserController::class, 'kycUserRequestVerified'])->middleware('permission:client_kyc_request.edit');   
		Route::post('/kyc/verified', [App\Http\Controllers\UserController::class, 'kycUserVerified'])->name('users.kyc.verified');  
		Route::post('/kyc/rejected', [App\Http\Controllers\UserController::class, 'kycUserRejected'])->name('users.kyc.rejected');  
	});

	//Recharge Amount
	Route::group(['prefix' => 'recharge'], function () {
		Route::post('/wallet/store', [App\Http\Controllers\RechargeController::class, 'rechargeWalletStore'])->name('recharge.wallet.amount');
		Route::post('/wallet/update', [App\Http\Controllers\RechargeController::class, 'rechargeWalletRazorpay'])->name('recharge.wallet.razorpay');

		Route::get('/list', [App\Http\Controllers\RechargeController::class, 'rechargeList'])->name('recharge.list');
		Route::post('/ajax', [App\Http\Controllers\RechargeController::class, 'rechargeListAjax'])->name('recharge.list.ajax');

		Route::get('/list/history', [App\Http\Controllers\RechargeController::class, 'rechargeListAdmin'])->name('recharge.list.history');
		Route::post('/ajax/history', [App\Http\Controllers\RechargeController::class, 'rechargeListAjaxAdmin'])->name('recharge.list.ajax.history');

		Route::post('/wallet/action', [App\Http\Controllers\RechargeController::class, 'rechargeWalletAction'])->name('recharge.wallet.action');
	});
	  
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
		Route::get('/order', [App\Http\Controllers\NDRController::class, 'index'])->name('ndr.order');    
		Route::post('/order/ajax', [App\Http\Controllers\NDRController::class, 'ndrAjax'])->name('ndr.order.ajax');  
		Route::post('/order/raise', [App\Http\Controllers\NDRController::class, 'ndrRaise'])->name('ndr.order.raise'); 
	});	
	 
	//Order 
	Route::group(['prefix'=>'order'], function()
	{ 
		Route::get('/', [App\Http\Controllers\OrderController::class, 'index'])->name('order')->middleware('permission:order.view');   
		Route::post('/ajax', [App\Http\Controllers\OrderController::class, 'orderAjax'])->name('order.ajax');   
		
		 
		Route::get('/search', [App\Http\Controllers\OrderController::class, 'searchByAwb'])->name('order.search');

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
	Route::group(['prefix'=> 'cod-remmitance'], function () { 
		Route::get('/', [App\Http\Controllers\OrderController::class, 'codRemittance'])->name('cod-remmitance')->middleware('permission:remittance.view'); 
		Route::post('/ajax', [App\Http\Controllers\OrderController::class, 'remmitanceAjax'])->name('remmitance.ajax');
		Route::get('/download-excel/{id}', [App\Http\Controllers\OrderController::class, 'downloadRemittanceExcel'])->name('remmitance.download.excel'); 
	}); 
	
	Route::group(['prefix'=> 'cod-payout'], function () {
	    Route::get('/',[App\Http\Controllers\OrderController::class, 'codPayout'])->name('cod-payout')->middleware('permission:cod_payout.view');
		Route::post('/ajax',[App\Http\Controllers\OrderController::class, 'codPayoutAjax'])->name('cod-payout-ajax');
		Route::post('/store',[App\Http\Controllers\OrderController::class, 'storePayout'])->name('cod-payout-store'); 
	});
	  
	// Daily Reports
	Route::get('/report/order', [App\Http\Controllers\ReportController::class, 'index'])->name('report.order')->middleware('permission:order_report.view'); 
	Route::post('/report/order/ajax', [App\Http\Controllers\ReportController::class, 'reportOrderAjax'])->name('report.order.ajax');
	Route::get('/report/export-orders', [App\Http\Controllers\ReportController::class, 'reportOrderExport'])->name('report.order.export');
	
	Route::get('/passbook/', [App\Http\Controllers\ReportController::class, 'passbookReport'])->name('report.passbook')->middleware('permission:passbook_report.view'); 
	Route::post('/passbook/ajax', [App\Http\Controllers\ReportController::class, 'passbookReportAjax'])->name('report.passbook.ajax'); 
	 
	Route::get('/report/shipping-charge', [App\Http\Controllers\ReportController::class, 'shippingCharge'])->name('report.shipping-charge')->middleware('permission:shipping_charge.view'); 
	Route::post('/report/shipping-charge/ajax', [App\Http\Controllers\ReportController::class, 'shippingChargeAjax'])->name('report.shipping-charge.ajax');
	 
	Route::get('report/billing-invoice', [App\Http\Controllers\ReportController::class, 'billingInvoice'])->name('report.billing-invoice');
	Route::post('report/billing-invoice/ajax', [App\Http\Controllers\ReportController::class, 'billingInvoiceAjax'])->name('report.billing-invoice.ajax');
	Route::get('report/billing-invoice/pdf/{id}', [App\Http\Controllers\ReportController::class, 'billingInvoicePdf'])->name('report.billing-invoice.pdf');
	Route::get('report/billing-invoice/excel/{id}', [App\Http\Controllers\ReportController::class, 'billingInvoiceExcel'])->name('report.billing-invoice.excel');
	
	 
	//Ticket
	Route::group(['prefix'=>'ticket'], function()
	{
		Route::get('/list', [App\Http\Controllers\TicketController::class, 'index'])->name('ticket');
		Route::post('/ajax', [App\Http\Controllers\TicketController::class, 'ticketListAjax'])->name('ticket.list.ajax');
		Route::get('/add', [App\Http\Controllers\TicketController::class, 'ticketAdd'])->name('ticket.add');
		Route::post('/store', [App\Http\Controllers\TicketController::class, 'ticketStore'])->name('ticket.store');
		Route::get('/view/{id}', [App\Http\Controllers\TicketController::class, 'ticketView']);
		Route::post('/remark/store', [App\Http\Controllers\TicketController::class, 'remarkStore'])->name('ticket.remark.store');
		
		Route::get('/all/list', [App\Http\Controllers\TicketController::class, 'ticketList'])->name('ticket.admin')->middleware('permission:ticket_request.view');
		Route::post('/all/ajax', [App\Http\Controllers\TicketController::class, 'ticketAllListAjax'])->name('ticket.admin.ajax')->middleware('permission:ticket_request.view');
		Route::get('/revert_ticket/{id}', [App\Http\Controllers\TicketController::class, 'revert_ticket'])->name('ticket.admin.revert_ticket')->middleware('role');
		Route::post('/update/{id}', [App\Http\Controllers\TicketController::class, 'ticketupdate'])->name('ticket.admin.update_revert')->middleware('permission:ticket_request.update');
		Route::get('/close/{id}', [App\Http\Controllers\TicketController::class, 'ticketClose'])->middleware('permission:ticket_request.update');
		Route::get('/delete/{id}', [App\Http\Controllers\TicketController::class, 'ticketDelete'])->middleware('permission:ticket_request.delete');
	});
	 	 
	//Weight Management
	Route::group(['prefix'=>'weight'], function()
	{
		//ADMIN
		Route::get('/descripencies/all', [App\Http\Controllers\WeightController::class, 'descrepenciesAll'])->name('weight.admin.descripencies')->middleware('permission:weight_descripencies.view');
		Route::post('/raise/excess-weight', [App\Http\Controllers\WeightController::class, 'raiseExcessWeight'])->name('weight.raise.excess-weight');
		Route::get('/descripencies/history/{id}', [App\Http\Controllers\WeightController::class, 'weightHoistory']);
		Route::get('/descripencies/view_image/{id}', [App\Http\Controllers\WeightController::class, 'weightview_image']); 
		
		Route::post('/descripencies/bycourier', [App\Http\Controllers\WeightController::class, 'weightbycourier']);
		Route::get('/descripencies/auto-accepted', [App\Http\Controllers\WeightController::class, 'autoAccepted'])->name('weight.auto-accepted');
		
		//User
		Route::get('/descripencies/list', [App\Http\Controllers\WeightController::class, 'index'])->name('weight.descripencies');
		Route::get('/descripencies/accepted/{id}', [App\Http\Controllers\WeightController::class, 'weightAccepted']);
		Route::get('/descripencies/remark/{id}', [App\Http\Controllers\WeightController::class, 'weightRemark']);
		Route::post('/descripencies/remark/store', [App\Http\Controllers\WeightController::class, 'remarkStore'])->name('weight.remark.store');  
	});

	// App Banner CRUD
	Route::prefix('app-banner')->name('app.banner.')->middleware('permission:general_setting.view')->group(function () {
		Route::get('/', [App\Http\Controllers\AppBannerController::class, 'index'])->name('index');
		Route::get('/create', [App\Http\Controllers\AppBannerController::class, 'create'])->name('create');
		Route::post('/store', [App\Http\Controllers\AppBannerController::class, 'store'])->name('store');
		Route::get('/edit/{id}', [App\Http\Controllers\AppBannerController::class, 'edit'])->name('edit');
		Route::post('/update/{id}', [App\Http\Controllers\AppBannerController::class, 'update'])->name('update');
		Route::delete('/delete/{id}', [App\Http\Controllers\AppBannerController::class, 'destroy'])->name('delete');
	});