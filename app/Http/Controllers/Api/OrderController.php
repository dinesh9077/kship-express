<?php
	
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\{DB, Auth, File, Storage, Validator, Http, Log};
	use App\Models\{
		Order, OrderItem, Vendor, VendorAddress, OrderActivity, OrderStatus, Billing, Packaging, 
		WeightFreeze, Customer, User, CustomerAddress, PincodeService, ShippingCompany, 
		CourierWarehouse, ProductCategory, CodVoucher, CourierCommission
	};
	use App\Exports\PendingStarOrderExport;
	use Maatwebsite\Excel\Facades\Excel;
	use PDF;  
	use App\Services\ShipMozo;
	use App\Services\DelhiveryService;
	use App\Services\DelhiveryB2CService;
	use App\Imports\BulkOrder;
	use App\Traits\ApiResponse;   
	use Helper; 
	use DNS1D; 
	use App\Exports\CodRemittanceExport;
	use PhpOffice\PhpSpreadsheet\Shared\Date;
	
	class OrderController extends Controller
	{
		use ApiResponse; 
		
		protected $delhiveryService;
		protected $shipMozo;
		protected $delhiveryB2CService;
		public function __construct()
		{   
			$this->shipMozo = new ShipMozo();  
			$this->delhiveryService = new DelhiveryService();  
			$this->delhiveryB2CService = new DelhiveryB2CService();  
		}
		 
		public function index(Request $request)
		{   
			$start = $request->post("offset");
			$limit = $request->post("limit"); 
			 
			$search = $request->post('search');
			$status = strtolower($request->post('status'));
			$weightOrder = $request->get('weight_order', 1); 
			$user = Auth::user(); 
			$role = $user->role;
			$id = $user->id;
			
			// Build the query for orders
			$query = Order::with([
				'customer:id,first_name,last_name,mobile,email',
				'customerAddress:id,address',
				'warehouse:id,warehouse_name,contact_name,contact_number,company_name',
				'user:id,name,company_name,email,mobile',
				'orderItems:id,order_id,product_category,product_name,sku_number,hsn_number,amount,quantity',
			])
			->select('id', 'order_prefix', 'user_id', 'courier_id', 'courier_logo','customer_id', 'delivery_date', 'customer_address_id', 'shipping_company_id', 'warehouse_id', 'status_courier', 'order_type', 'created_at', 'weight_order', 'cod_amount', 'awb_number', 'invoice_amount', 'length', 'width', 'height', 'weight', 'reason_cancel', 'courier_name')
			->when($role === "user", fn($q) => $q->where('orders.user_id', $id));
			
			if($status === "in transit")
			{
				$query->whereNotIn('status_courier', ['new', 'manifested', 'delivered', 'cancelled']);
			}
			else
			{
				$query->when($status !== "all", fn($q) => $q->where('orders.status_courier', $status));
			}
			
			if($status === "all")
			{
				if ($request->courier_name)
				{
					$query->where('orders.courier_name', $request->courier_name);
				}
				
				if($request->status_courier)
				{
					$query->where('orders.status_courier', $request->status_courier);
				}
				if($request->order_type)
				{
					$query->where('orders.order_type', $request->order_type);
				}
				if($request->user_id)
				{
					$query->where('orders.user_id', $request->user_id);
				}
				if($request->from_date && $request->to_date)
				{
					$query->whereBetween('orders.created_at', [$request->from_date, $request->to_date]);
				}
			}
			
			if($weightOrder)
			{
				$query->where('orders.weight_order', $weightOrder);
			}
			   
			// Apply search filter
			if (!empty($search)) { 
				$query->where(function ($q) use ($search) {
					// Search in the main `orders` table
					$q->where('orders.id', 'LIKE', "%$search%")
					->orWhere('orders.order_prefix', 'LIKE', "%$search%")
					->orWhere('orders.created_at', 'LIKE', "%$search%")
					->orWhere('orders.awb_number', 'LIKE', "%$search%")
					->orWhere('orders.courier_name', 'LIKE', "%$search%")
					->orWhere('orders.status_courier', 'LIKE', "%$search%")
					->orWhere('orders.lr_no', 'LIKE', "%$search%"); 
					
					// Search in `customer` relationship
					$q->orWhereHas('customer', function ($q) use ($search) {
						$q->where('first_name', 'LIKE', "%$search%")
						->orWhere('last_name', 'LIKE', "%$search%")
						->orWhere('mobile', 'LIKE', "%$search%")
						->orWhere('email', 'LIKE', "%$search%");
					});
					
					// Search in `user` relationship
					$q->orWhereHas('user', function ($q) use ($search) {
						$q->where('name', 'LIKE', "%$search%")
						->orWhere('email', 'LIKE', "%$search%")
						->orWhere('company_name', 'LIKE', "%$search%")
						->orWhere('mobile', 'LIKE', "%$search%");
					});
					
					// Search in `warehouse` relationship
					$q->orWhereHas('warehouse', function ($q) use ($search) {
						$q->where('warehouse_name', 'LIKE', "%$search%")
						->orWhere('company_name', 'LIKE', "%$search%")
						->orWhere('contact_name', 'LIKE', "%$search%")
						->orWhere('contact_number', 'LIKE', "%$search%");
					});
					
					// Search in `orderItems` relationship
					$q->orWhereHas('orderItems', function ($q) use ($search) {
						$q->where('product_discription', 'LIKE', "%$search%")
						->orWhere('amount', 'LIKE', "%$search%")
						->orWhere('quantity', 'LIKE', "%$search%");
					});
				}); 
			} 
			
			$orders = $query->offset($start)
			->limit($limit)
			->latest()
			->get();
			
			return $this->successResponse($orders, 'orders fetched successfully.');
		}
		 
		public function filterList(Request $request)
		{ 
			$weightOrder = $request->get('weight_order', 1);

			// Fetch sellers (users with orders for given weight_order)
			$sellers = User::select('users.id', 'users.name')
				->whereHas('orders', function ($q) use ($weightOrder) {
					$q->where('weight_order', $weightOrder);
				})
				->orderBy('users.name')
				->distinct()
				->get();

			// Fetch distinct statuses for given weight_order
			$statuses = Order::where('weight_order', $weightOrder)
				->distinct()
				->pluck('status_courier');

			return $this->successResponse([
				'users'    => $sellers,
				'statuses' => $statuses,
			], 'Filter fetched successfully.');
		}
		  
		public function orderStore(Request $request)
		{    
			DB::beginTransaction(); 
			try { 
				$user_id = Auth::id();  
				
				$exclude = ['_token', 'product_name', 'product_category', 'sku_number', 'hsn_number', 'amount', 'quantity', 'order_image', 'invoice_document', 'first_name', 'last_name', 'email', 'gst_number', 'mobile', 'address', 'country', 'state', 'city', 'zip_code']; 
				if ($request->weight_order == 2) { 
					$exclude = array_merge($exclude, ['weight', 'length', 'width', 'height']);
				} 
				$data = $request->except($exclude); 
				
				// Check if order prefix already exists 
				/* if (Order::where('order_prefix', $request->order_prefix)->exists()) {
					return $this->errorResponse('The order number already exists.'); 
				} */
				
				// // Create Customer
				// $customer = Customer::create([
				// 	'user_id'    => $user_id,
				// 	'first_name' => $request->first_name,
				// 	'last_name'  => $request->last_name,
				// 	'email'      => $request->email,
				// 	'gst_number' => $request->gst_number ?? null,
				// 	'mobile'     => $request->mobile,
				// 	'status'     => 1,
				// ]);
				
				// // Create Customer Address if available
				// $customerAddress = null;
				// if ($request->filled('address')) {
				// 	$customerAddress = CustomerAddress::create([
				// 		'customer_id' => $customer->id,
				// 		'address'     => $request->address,
				// 		'country'     => $request->country,
				// 		'state'       => $request->state,
				// 		'city'        => $request->city,
				// 		'zip_code'    => $request->zip_code,
				// 		'status'      => 1,
				// 	]);
				// }
					
				$data['user_id'] = $user_id;
				$data['order_prefix'] = Order::generateOrderNumber($user_id);
				$data['status_courier'] = 'New'; 
				$data['weight'] = $request->total_weight;
				$data['is_fragile_item'] = $request->is_fragile_item ?? 0;
				$data['total_amount'] = $request->invoice_amount;
				$data['status'] = 1;
				$data['created_at'] = now();
				$data['updated_at'] = now();
			   
				$order = Order::create($data); 
				
				$imagePaths = []; 
				if ($request->hasFile('order_image')) {
					foreach ($request->file('order_image') as $image) 
					{ 
						$filename = time() . '_' . $image->getClientOriginalName(); 
						$path = $image->storeAs('public/orders/' . $order->id, $filename);  
						$imagePaths[] = $filename; // Store file path
					}
				} 
				
				$invoiceDocPaths = []; 
				if ($request->hasFile('invoice_document')) {
					foreach ($request->file('invoice_document') as $image) 
					{ 
						$filename = time() . '_' . $image->getClientOriginalName(); 
						$path = $image->storeAs('public/orders/' . $order->id, $filename);  
						$invoiceDocPaths[] = $filename; // Store file path
					}
				} 
				$order->update(['order_image' => $imagePaths, 'invoice_document' => $invoiceDocPaths]);
				
				if($request->weight_order == 2)
				{ 
					$this->b2bStore($order, $request);
				}
				else
				{
					$this->b2cStore($order, $request);
				}  
				
				// Insert order status
				OrderStatus::insert([
					'order_id' => $order->id, 
					'order_status' => 'New', 
					'created_at' => now(), 
					'updated_at' => now()
				]);
				
				Helper::orderActivity($order->id, 'Order created.');
				
				DB::commit(); 
				return $this->successResponse($order, 'The order has been successfully added.');  
			} 
			catch (\Exception $e) {
				DB::rollback();
				return $this->errorResponse('The failed to create order.please try again.'); 
			}
		}
		
		private function b2cStore($order, $request)
		{
			$orderItems = []; 
			if (!empty($request->product_category)) 
			{
				foreach ($request->product_category as $key => $productCategory) { 
					$amount = $request->amount[$key] ?? 0;
					$quantity = $request->quantity[$key] ?? 1;
					  
					$orderItems[] = [
						'order_id' => $order->id, 
						'product_category' => $productCategory,
						'product_name' => $request->product_name[$key] ?? null,
						'sku_number' => $request->sku_number[$key] ?? null,
						'hsn_number' => $request->hsn_number[$key] ?? null,
						'amount' => $amount, 
						'ewaybillno' => null, 
						'quantity' => $quantity,
						'created_at' => now(),
						'updated_at' => now() 
					]; 
				} 
				OrderItem::insert($orderItems);  
			}  
		}
		
		private function b2bStore($order, $request)
		{
			$orderItems = [];
			$height = $width = $length = $weight = 0;
			
			if (!empty($request->product_category)) 
			{
				foreach ($request->product_category as $key => $productCategory) { 
					$amount = $request->amount[$key] ?? 0;
					$quantity = $request->quantity[$key] ?? 1;
					  
					$orderItems[] = [
						'order_id' => $order->id, 
						'product_category' => $productCategory,
						'product_name' => $request->product_name[$key] ?? null,
						'sku_number' => $request->sku_number[$key] ?? null,
						'hsn_number' => $request->hsn_number[$key] ?? null,
						'amount' => $amount, 
						'ewaybillno' => null, 
						'quantity' => $quantity,
						'created_at' => now(),
						'updated_at' => now(),
						'dimensions' => json_encode([
							'no_of_box' => $request->no_of_box[$key] ?? 0,
							'weight' => $request->weight[$key] ?? 0,
							'length' => $request->length[$key] ?? 0,
							'width' => $request->width[$key] ?? 0,
							'height' => $request->height[$key] ?? 0,
						]),
					];
					
					// Accumulate totals
					$height += $request->height[$key] ?? 0;
					$width  += $request->width[$key] ?? 0;
					$length += $request->length[$key] ?? 0; 
				}
				
				OrderItem::insert($orderItems);  
			}
			  
			$order->update([ 
				'length' => $length,
				'width'  => $width,
				'height' => $height
			]);	
			return;
		}
		   
		public function orderEdit($id)
		{  
			$order = Order::with('orderItems')->find($id);
			return $this->successResponse($order, 'order fetch successfully.'); 
		}
		
		public function orderUpdate(Request $request, $id)
		{   
			DB::beginTransaction(); 
			try { 
				$user_id = Auth::id();  
				$exclude = ['_token', 'product_name', 'product_category', 'sku_number', 'hsn_number', 'amount', 'quantity', 'order_image', 'invoice_document', 'first_name', 'last_name', 'email', 'gst_number', 'mobile', 'address', 'country', 'state', 'city', 'zip_code']; 
				if ($request->weight_order == 2) { 
					$exclude = array_merge($exclude, ['weight', 'length', 'width', 'height']);
				} 
				$data = $request->except($exclude); 
				
				$order = Order::findOrFail($id); 
				
				// $order->customer()->update([
				// 	'user_id'    => $user_id,
				// 	'first_name' => $request->first_name,
				// 	'last_name'  => $request->last_name,
				// 	'email'      => $request->email,
				// 	'gst_number' => $request->gst_number ?? null,
				// 	'mobile'     => $request->mobile,
				// 	'status'     => 1,
				// 	'updated_at' => now(), // ✅ fixed
				// ]);

				// if (!empty($request->address)) {
				// 	$order->customerAddress()->update([
				// 		'customer_id' => $order->customer_id,
				// 		'address'     => $request->address,
				// 		'country'     => $request->country,
				// 		'state'       => $request->state,
				// 		'city'        => $request->city,
				// 		'zip_code'    => $request->zip_code,
				// 		'status'      => 1,
				// 		'updated_at'  => now(), // ✅ fixed
				// 	]);
				// }
	 
				$data['user_id'] = $user_id;
				$data['order_prefix'] = Order::generateOrderNumber($user_id);
				$data['is_fragile_item'] = $request->is_fragile_item ?? 0;
				$data['weight'] = $request->total_weight; 
				$data['total_amount'] = $request->invoice_amount; 
				$data['status'] = 1;
				$data['created_at'] = now();
				$data['updated_at'] = now();
				  
				$imagePaths = $order->order_image ?? [];   
				if ($request->hasFile('order_image')) {
					foreach ($request->file('order_image') as $image) 
					{ 
						$filename = time() . '_' . $image->getClientOriginalName(); 
						$path = $image->storeAs('public/orders/' . $order->id, $filename);  
						$imagePaths[] = $filename; // Store file path
					}
				}
				
				$invoiceDocPaths = $order->invoice_document ?? [];   
				if ($request->hasFile('invoice_document')) {
					foreach ($request->file('invoice_document') as $image) 
					{ 
						$filename = time() . '_' . $image->getClientOriginalName(); 
						$path = $image->storeAs('public/orders/' . $order->id, $filename);  
						$invoiceDocPaths[] = $filename; // Store file path
					}
				}
				$data['order_image'] = $imagePaths;
				$data['invoice_document'] = $invoiceDocPaths;
				$order->update($data);
				 
				if($request->weight_order == 2)
				{ 
					$this->b2bUpdate($order, $request);
				}
				else
				{
					$this->b2cUpdate($order, $request);
				}
				 
				Helper::orderActivity($order->id, 'Order updated.');
				
				DB::commit(); 
				return $this->successResponse($order, 'The order have been updated successfully.');  
			} 
			catch (\Exception $e) {
				DB::rollback(); 
				return $this->errorResponse('The failed to update order.please try again.');  
			}
		}
		
		private function b2cUpdate($order, $request)
		{ 
			$orderItems = []; 
			
			if (!empty($request->product_category)) 
			{
				OrderItem::where('order_id', $order->id)->whereNotIn('id', $request->id ?? [])->delete();
				foreach ($request->product_category as $key => $productCategory)
				{  
					$amount = $request->amount[$key] ?? 0;
					$quantity = $request->quantity[$key] ?? 1;
					 
					$orderItemData = [
						'order_id' => $order->id, 
						'product_category' => $productCategory,
						'product_name' => $request->product_name[$key],
						'sku_number' => $request->sku_number[$key],
						'hsn_number' => $request->hsn_number[$key],
						'amount' => $amount, 
						'ewaybillno' => null, 
						'quantity' => $quantity,
						'updated_at' => now() 
					];
					 
					// Check if order_item_id exists in the request
					if (!empty($request->id[$key])) 
					{
						$orderItem = OrderItem::find($request->id[$key]);
						if ($orderItem) { 
							$orderItem->update($orderItemData); // Update existing item
						}
					} else {
						$orderItemData['created_at'] = now();
						$orderItems[] = $orderItemData; // Add new item for bulk insert
					}
				} 
				
				// Bulk insert for new items if any
				if (!empty($orderItems)) {
					OrderItem::insert($orderItems);
				} 
			}
			return;
		}
		
		private function b2bUpdate($order, $request)
		{
			$totalAmount = 0;
			$orderItems = [];
			$height = $width = $length = $weight = 0;
			
			if (!empty($request->product_category)) 
			{
				OrderItem::where('order_id', $order->id)->whereNotIn('id', $request->id ?? [])->delete();
				foreach ($request->product_category as $key => $productCategory)
				{  
					$amount = $request->amount[$key] ?? 0;
					$quantity = $request->quantity[$key] ?? 1;
					 
					$orderItemData = [
						'order_id' => $order->id, 
						'product_category' => $productCategory,
						'product_name' => $request->product_name[$key],
						'sku_number' => $request->sku_number[$key],
						'hsn_number' => $request->hsn_number[$key],
						'amount' => $amount, 
						'ewaybillno' => null, 
						'quantity' => $quantity,
						'updated_at' => now(),
						'dimensions' => json_encode([
							'no_of_box' => $request->no_of_box[$key] ?? 0,
							'weight' => $request->weight[$key] ?? 0,
							'length' => $request->length[$key] ?? 0,
							'width' => $request->width[$key] ?? 0,
							'height' => $request->height[$key] ?? 0,
						]),
					];
					
					// Accumulate totals
					$height += $request->height[$key] ?? 0;
					$width  += $request->width[$key] ?? 0;
					$length += $request->length[$key] ?? 0; 
					
					// Check if order_item_id exists in the request
					if (!empty($request->id[$key])) {
						$orderItem = OrderItem::find($request->id[$key]);
						if ($orderItem) {
							$orderItemData['dimensions'] = json_decode($orderItemData['dimensions'], true);
							$orderItem->update($orderItemData); // Update existing item
						}
						} else {
						$orderItemData['created_at'] = now();
						$orderItems[] = $orderItemData; // Add new item for bulk insert
					}
				}
				 
				$order->update([ 
					'length' => $length,
					'width'  => $width,
					'height' => $height
				]);
				
				// Bulk insert for new items if any
				if (!empty($orderItems)) {
					OrderItem::insert($orderItems);
				} 
			}
			return;
		}
		
		public function orderDetails($id)
		{ 
			$order = Order::with('customer', 'shippingCompany', 'customerAddress', 'warehouse', 'orderItems')->find($id);  
			$orderActivities = OrderActivity::where('order_id', $id)->latest()->get();
			
			$trackingHistories = []; 
			if(!empty($order->awb_number))
			{  
				$shippingCompany = $order->shippingCompany ?? null;
				
				if(($shippingCompany && $shippingCompany->id == 1))
				{ 
					$trackingResponse = $this->shipMozo->trackOrder($order->awb_number, $shippingCompany);  
					if (!($trackingResponse['success'] ?? false)) {
						$errorMsg = $trackingResponse['response']['errors'][0]['message'] ?? ($trackingResponse['response']['error'] ?? 'An error occurred.');
						return back()->with('error', $errorMsg); 
					}
					
					if ((isset($trackingResponse['response']['result']) && $trackingResponse['response']['result']) == 0)
					{ 
						return back()->with('error', $trackingResponse['response']['message'] ?? 'An error occurred.'); 
					}
					
					$responseData = $trackingResponse['response']['data']['scan_detail'] ?? [];
					$trackingHistories = $responseData; 
				} 
			}
			
			return $this->successResponse(compact('order', 'orderActivities', 'trackingHistories'), 'success'); 
		}
		
		public function orderCancel($id)
		{
			DB::beginTransaction();
			
			try {
				// Update Order Status
				$order = Order::findOrFail($id);
				$order->update([
					'status_courier' => 'cancelled',
					'reason_cancel' => 'The client has cancelled this order before it was shipped.',
					'order_cancel_date' => now(),
				]);
				
				
				// Insert Order Status Record
				OrderStatus::create([
					'order_id' => $id,
					'order_status' => 'cancelled',
					'created_at' => now(),
					'updated_at' => now(),
				]);
				
				// Log Order Activity
				Helper::orderActivity($id, 'Order cancelled.');
				
				DB::commit(); // Commit Transaction
				
				return $this->successResponse($order, 'The order has been successfully cancelled.');  
			} catch (\Exception $e) {
				DB::rollback();
				return $this->errorResponse('Failed to cancel the order'); 
			}
		}
		
		public function orderCancelApi($orderId)
		{
 			DB::beginTransaction();
			
			try 
			{
				$order = Order::with(['user'])->findOrFail($orderId);
				$user = $order->user;
			 
				$shippingCompany = ShippingCompany::where('id', $order->shipping_company_id)
				->where('status', 1)
				->first();
				
				if(!$shippingCompany)
				{
					return $this->errorResponse('Shipment not found.');  
				}
				
				$orderCancelledData = []; 
				if ($shippingCompany->id == 1) {  
					$response = $this->shipMozo->cancelShipment($order, $shippingCompany);  
					if (!($response['success'] ?? false)) {
						$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response']['message'] ?? 'An error occurred.');
						return $this->errorResponse($errorMsg);  
					}
					
					if ((isset($response['response']['result']) && $response['response']['result'] == 0))
					{  
						return $this->errorResponse($response['response']['message'] ?? 'An error occurred.');  
					} 
					
					$orderCancelledData = [
						'status_courier' => 'cancelled',
						'order_cancel_date' => now(),
						'reason_cancel' => 'cancelled shipment by user',
					]; 
				} 
				
				if (!$orderCancelledData) {
					return $this->errorResponse('Something went wrong while processing the cancellation.');   
				}
				
				$order->update($orderCancelledData);
				
				if ($user->role == "user") {
					$walletRefund = $order->shipping_charge ?? 0;
					$user->increment('wallet_amount', $walletRefund);
					
					Billing::create([
						'user_id' => $order->user_id,
						'billing_type' => "Order",
						'billing_type_id' => $order->id,
						'transaction_type' => 'credit',
						'amount' => $walletRefund,
						'note' => 'Order canceled with AWB number: ' . $order->awb_number,
						'created_at' => now(),
						'updated_at' => now(),
					]);
				}
				
				OrderStatus::create([
					'order_id' => $order->id,
					'order_status' => 'cancelled',
					'created_at' => now(),
					'updated_at' => now(),
				]);
				
				Helper::orderActivity($order->id, 'Order canceled with AWB number: ' . $order->awb_number);
				
				DB::commit(); 
				return $this->successResponse($order, 'The order has been successfully cancelled.');  
			} catch (\Exception $e) {
				DB::rollback();
				return $this->errorResponse('Failed to cancel the order'); 
			}
		} 
		public function orderShipCharge($orderId)
		{
			$user = Auth::user();
			$role = $user->role; 
			
			if($role != "admin" && $user->kyc_status == 0)
			{ 
				return $this->errorResponse('Your order cannot be placed until your KYC is approved.');
			}  
			 
			$order = Order::with(['warehouse', 'user', 'customerAddress', 'orderItems'])->find($orderId);
			if (!$order) {
				return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
			}
			
			$user = $order->user ?? null;
			if (!$user) {
				return response()->json(['status' => 'error', 'message' => 'User order not found'], 404);
			}

			$shippingCompanies = ShippingCompany::whereStatus(1)->get();
			$couriers = [];
			
			$volumetricWt = $order->length * $order->width * $order->height / 5000; 
			$weight = $order->weight; 
			
			foreach ($shippingCompanies as $shippingCompany)
			{
				if ($shippingCompany->id == 1)
				{ 
					$requestData = new Request([
						'pickup_code'   => optional($order->warehouse)->zip_code,
						'delivery_code' => optional($order->customerAddress)->zip_code,
					]);
  
					$pincodeServiceData = $this->shipMozo->pincodeService($requestData->all(), $shippingCompany);
 
					if (
						!($pincodeServiceData['success'] ?? false) || 
						(isset($pincodeServiceData['response']['result']) && $pincodeServiceData['response']['result'] == 0) || 
						(isset($pincodeServiceData['response']['data']['serviceable']) && !$pincodeServiceData['response']['data'])
					) {
						continue;
					}
					 
					$response = $this->shipMozo->orderRateCaculator($order, $shippingCompany); 
					
					if (!($response['success'] ?? false) || 
					(isset($response['response']['result']) && $response['response']['result'] == 0)) {
						continue;
					}
					
					$responseDetails = $response['response']['data'] ?? []; 
					if (!$responseDetails) {
						continue;
					}
					
					$courierCommissions = CourierCommission::with(['userCommissions' => function($q) use ($user){
						$q->where('user_id', $user->id);
					}])
					->where('shipping_company', $shippingCompany->id)
					->get()
					->keyBy('courier_id');
					
					foreach($responseDetails as  $responseData)
					{
						$courierLogo = $this->courierImage($responseData['image'], $responseData['id']);
						$totalCharges = $responseData['total_charges'];  
						$beforeTax = $responseData['before_tax_total_charges'];  
						$gst = $responseData['gst'];  
						
						$courierCommission = $courierCommissions->has($responseData['id']) ? $courierCommissions->get($responseData['id']) : null;
						$commissionAmount = 0;
						if($courierCommission && $user->role == "user")
						{
							$userCommission = $courierCommission->userCommissions->first() ?? null;
							$commissionType = $userCommission->type ?? $courierCommission->type ?? 'fix';
							$commissionValue = $userCommission->value ?? $courierCommission->value ?? 0;
							
							if ($commissionType === "fix") {
								$commissionAmount = $commissionValue; // flat fee
							} else {
								$commissionAmount = ($totalCharges * $commissionValue) / 100; // percentage
							}
							
							$totalCharges += $commissionAmount;
							$beforeTax += $commissionAmount;
						}
						
						$couriers[] = [  
							'order_id' => $order->id, 
							'shipping_charge' => $beforeTax, 
							'tax' => $gst, 
							'shipping_company_id' => $shippingCompany->id,
							'courier_id' 	=> $responseData['id'],
							'shipping_company_name' => $responseData['name'],
							'shipping_company_logo' => $courierLogo, 
							'courier_name' => $responseData['name'], 
							'total_charges' => $totalCharges,
							'estimated_delivery' => $responseData['estimated_delivery'] ?? 'N/A', 
							'chargeable_weight' => $responseData['minimum_chargeable_weight'] ?? 0,
							'applicable_weight' =>  max($volumetricWt, $weight) ?? 0,
							'percentage_amount' => $commissionAmount,
							'responseData' => $responseData
						];
					}
				} 
			}
			 
			$couriers = $couriers ? collect($couriers)->sortBy('total_charges') : collect();
			 
			return $this->successResponse([
				'order' => $order,  
				'couriers' => collect($couriers)->values(),
				'total_courier' => count($couriers)
			], 'success');   
		}

		public function courierImage($imageUrl, $courierId)
		{
			// Build file info
			$extension = 'png';
			$filename = "{$courierId}.{$extension}";
			$localPath = "courier-logo/{$filename}";

			// ✅ Check if file already exists
			if (Storage::disk('public')->exists($localPath)) {
				// Return existing local file URL
				return asset("storage/{$localPath}");
			}

			// Fetch image from URL
			$response = Http::withOptions(['verify' => false])->get($imageUrl);

			if (!$response->successful()) {
				// If failed, return original image URL
				return $imageUrl;
			}

			// Save file locally
			Storage::disk('public')->put($localPath, $response->body());

			// Return local URL
			return asset("storage/{$localPath}");
		}

		public function orderShipNow(Request $request)
		{   
			DB::beginTransaction();
			try {
				$requestData = $request->all();  
				$shippingCompany = ShippingCompany::findOrFail($requestData['shipping_company_id']);
			 
				if (!$shippingCompany) {
					return $this->errorResponse('Invalid Shipping Company');   
				}
				
				$order = Order::with(['warehouse', 'customer', 'customerAddress', 'orderItems', 'user'])->findOrFail($requestData['order_id']);
				$user = $order->user;
				
				if($user->role != "admin" && $user->kyc_status == 0)
				{ 
					return $this->errorResponse('Your order cannot be placed until your KYC is approved.');
				} 

				$walletAmount = $user->wallet_amount;
					
				if ($user->role == "user") {
					if ($walletAmount < 100) {
						return $this->errorResponse('Minimum 100 Wallet Balance required');   
					}
					
					if ($requestData['total_charges'] > $walletAmount) {
						return $this->errorResponse('Insufficient wallet balance');  
					}
				}
				$courierWarehouse = $order->warehouse ?? null;	
				if(!$courierWarehouse)
				{
					return $this->errorResponse('Pickup warehouse address empty.');   
				}
				
				$courierLogo = $requestData['shipping_company_logo'] ?? null;
				if ($shippingCompany->id == 1) 
				{    
					$existStatus = $courierWarehouse->created ?? [];
					 
					if ($existStatus && isset($existStatus['shipMozo']) && $existStatus['shipMozo'] == 0) 
					{ 	 
						$data = $this->shipMozo->createWarehouse($courierWarehouse, $shippingCompany);  
						if ($data['success'] || (isset($data['response']['result']) && $data['response']['result'] == 1)){
							$existingCreated = $courierWarehouse->created ?? [];  
							if (is_string($existingCreated)) {
								$existingCreated = json_decode($existingCreated, true) ?? [];
							} 
							$existingCreated['ship_mozo'] = 1;  
							
							$courierWarehouse->created = $existingCreated;
							$courierWarehouse->shipping_id   = $data['response']['data']['warehouse_id'];
							$courierWarehouse->api_response  = $data['response'];
							$courierWarehouse->save(); 
						} 
					}
					 
					$response = $this->shipMozo->pushOrder($order, $requestData, $shippingCompany);
					 
					if (!($response['success'] ?? false)) {
						$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response']['error'] ?? 'An error occurred.');
						return $this->errorResponse($errorMsg);   
					}
					
					if ((isset($response['response']['result']) && $response['response']['result'] == 0))
					{
						return $this->errorResponse($response['response']['data']['error'] ?? $response['response']['message'] ?? 'An error occurred.');
					} 
						
					$orderId = $response['response']['data']['order_id'] ?? '';
					if (!$orderId) {
						$errorMsg = 'Somthing went wrong.';
						return $this->errorResponse($errorMsg);  
					}
					
					$courierResponse = $this->shipMozo->assignCourier($orderId, $requestData['courier_id'] ?? null, $shippingCompany);  
					if (!($courierResponse['success'] ?? false)) {
						$errorMsg = $courierResponse['response']['errors'][0]['message'] ?? ($courierResponse['response']['error'] ?? 'An error occurred.');
						return $this->errorResponse($errorMsg);   
					}
					
					if ((isset($courierResponse['response']['result']) && $courierResponse['response']['result'] == 0))
					{
						return $this->errorResponse($courierResponse['response']['data']['error'] ?? $courierResponse['response']['message'] ?? 'An error occurred.');					 
					}   
					$awbNumber = $courierResponse['response']['data']['awb_number'] ?? '';
					
					$shipment_id = $courierResponse['response']['data']['order_id'] ?? '';
					$lr_no = $courierResponse['response']['data']['order_id'] ?? '' ?? null; 
					$awb_number = $awbNumber ?? null; 
					$courier_id = $requestData['courier_id'] ?? null; 
					$statusCourier = 'manifested';
					$apiResponse = $courierResponse;
					if (empty($courierLogo)) {
						$courierLogo = "{$courier_id}.png";
					} 
				}
				
				// Prepare update data
				$updateData = [
					'status_courier' => $statusCourier,
					'tax' => 0,
					'tax_percentage' => 0,
					'shipping_charge' => $requestData['total_charges'] ?? 0,
					'shipping_company_id' => $shippingCompany->id,
					'shipment_id' => $shipment_id,
					'awb_number' => $awb_number,
					'courier_name' => $requestData['courier_name'],
					'courier_id' => $courier_id,
					'courier_logo' => $courierLogo,
					'label' => null,
					'lr_no' => $lr_no,
					'api_response' => $apiResponse,
					'applicable_weight' => $requestData['applicable_weight'],
					'cod_charges' => 0,
					'rto_charge' => 0,
					'percentage_amount' => $requestData['percentage_amount'] ?? 0
				];
				
				$order->update($updateData);
				
				if ($user->role === "user") {
					$user->decrement('wallet_amount', $requestData['total_charges']);
				}
				
				Billing::create([
					'user_id' => $user->id,
					'billing_type' => "Order",
					'billing_type_id' => $order->id,
					'transaction_type' => 'debit',
					'amount' => $requestData['total_charges'],
					'note' => "Order is shipped with AWB: {$awb_number}",
					'created_at' => now(),
					'updated_at' => now(),
				]);
				
				OrderStatus::create([
					'order_id' => $order->id,
					'order_status' => 'pending pickup',
					'created_at' => now(),
					'updated_at' => now(),
				]);
				
				Helper::orderActivity($order->id, "Order is shipped with AWB: {$awb_number}");
				
				// Send Email Notification Instead of SMS
				/* $customer = order->customer;
					if($customer->email)
					{
					Mail::to($customer->email)->send(new \App\Mail\OrderShipped($order, $awb_number));
				} */
				
				DB::commit(); 
				  
				return $this->successResponse($order, "Your package has been assigned to {$order->courier_name} and The AWB number is {$awb_number}");
				
			} 
			catch (\Exception $e) 
			{
				DB::rollBack();  
				return $this->errorResponse('Something went wrong!');
			}
		}
		
		public function orderTrackingHistory($orderId)
		{ 
			$order = Order::with('shippingCompany')->find($orderId);
			$shippingCompany = $order->shippingCompany ?? [];
			
			if(!$shippingCompany) 
			{
				return $this->errorResponse('Invalid courier!'); 
			} 
			
			$trackingHistories = []; 
			if(!empty($order->awb_number))
			{  
				if($shippingCompany->id == 1)
				{ 
					$trackingResponse = $this->shipMozo->trackOrder($order->awb_number, $shippingCompany);   
					if (!($trackingResponse['success'] ?? false)) {
						$errorMsg = $trackingResponse['response']['errors'][0]['message'] ?? ($trackingResponse['response']['error'] ?? 'An error occurred.');
						return $this->errorResponse($errorMsg); 
					}
					
					if ((isset($trackingResponse['response']['result']) && $trackingResponse['response']['result']) == 0)
					{ 
						return $this->errorResponse($trackingResponse['response']['message'] ?? 'An error occurred.');  
					}
					
					$responseData = $trackingResponse['response']['data']['scan_detail'] ?? [];
					$trackingHistories = $responseData; 
				}  
			}
			
			if(!$trackingHistories)
			{
				return $this->errorResponse('The order tracking data not found.');  
			}
			return $this->successResponse(compact('order', 'trackingHistories', 'shippingCompany'), 'success');  
		}  
		
		public function orderLableDownload($orderId)
		{  
			$order = Order::with(['shippingCompany:id,logo', 'customer:id,first_name,last_name,mobile', 'customerAddress', 'warehouse', 'orderItems'])->find($orderId);
			
			$shipping = $order->shippingCompany ?? null;
			$customer = $order->customer ?? null;
			$customerAddr = $order->customerAddress ?? null;
			$products =  $order->orderItems ?? null; 
			$hideLabel =  $order->warehouse ? $order->warehouse->label_options : []; 
			 
			$barcodePng = DNS1D::getBarcodePNG($order->awb_number, 'C128', 2.5, 60); 
			$orderIdBarcodePng = DNS1D::getBarcodePNG($order->shipment_id ?? $order->order_prefix, 'C128', 2.5, 60);
			
			$htmlView = view('order.single_label', compact('shipping', 'order', 'customer', 'customerAddr', 'products', 'barcodePng', 'hideLabel', 'orderIdBarcodePng'))->render();  
			 
			$pdf = PDF::loadHtml($htmlView);  
			return $pdf->download('order_label_' . $orderId . '.pdf'); 
		}
		  
        public function alllabeldownload(Request $request)
        {  
			try {
				$orderIds = $request->input('order_ids'); 
			 
				if (empty($orderIds) || !is_array($orderIds)) { 
					return $this->errorResponse('order ids required'); 
				}
				
				// Eager load related models in one go
				$orders = Order::with([
					'shippingCompany', 'customer', 'customerAddress',
					'orderItems', 'warehouse', 'user'
				])
				->whereIn('id', $orderIds)->get();
				 
				if ($orders->isEmpty()) {
					return $this->errorResponse('No valid orders found');  
				}
				 
				$htmlSections = []; 
				foreach ($orders as $order)
				{ 
					$shipping       = $order->shippingCompany;
					$customer       = $order->customer;
					$customerAddr   = $order->customerAddress;
					$products       = $order->orderItems;  
					 
					$hideLabel =  $order->warehouse ? $order->warehouse->label_options : []; 
					 
					$barcodePng = DNS1D::getBarcodePNG($order->awb_number, 'C128', 2.5, 60);
					$orderIdBarcodePng = DNS1D::getBarcodePNG($order->shipment_id ?? $order->order_prefix, 'C128', 2.5, 60);
					
					// Generate HTML for label
					$html = view('order.bulk-label', compact(
					'shipping', 'order', 'customer', 'products',
					'customerAddr', 'barcodePng', 'hideLabel', 'orderIdBarcodePng'
					))->render();
					
					// Optional: remove excessive whitespace (disable if layout breaks)
					$htmlSections[] = trim(preg_replace('/\s+/', ' ', $html));  
				}
				
				if (empty($htmlSections)) {
					return $this->errorResponse('All labels are empty');   
				}
				
				$mergedHtml = implode('', $htmlSections); 
				$pdf = PDF::loadHtml($mergedHtml);
				return $pdf->download('labels.pdf');  
			} 
			catch (\Exception $e) 
			{
				return $this->errorResponse('PDF generation failed');  
			}
		}   
		
		public function orderBulkStore(Request $request)
		{  
			$user = Auth::user();
			DB::beginTransaction(); // Begin Transaction

			try {  
				// Import the Excel file
				$bulkOrder = new BulkOrder();
				Excel::import($bulkOrder, $request->file('bulk_excel'));

				// Check if rows exist after removing the header
				if (empty($bulkOrder->rows)) {
					return $this->errorResponse('No valid data found in the Excel file.'); 
				}
			 
				foreach ($bulkOrder->rows as $row) 
				{ 
					$dateValue = $row[0] ?? null;
					$orderDate = null;

					if ($dateValue) {
						if (is_numeric($dateValue)) {
							// Excel serial number → Carbon date
							$orderDate = Date::excelToDateTimeObject($dateValue)->format('Y-m-d');
						} else {
							// If already string (like 2025-09-24)
							$orderDate = \Carbon\Carbon::parse($dateValue)->format('Y-m-d');
						}
					}
					  
					if($request->type_of_package == 1)
					{
						$this->b2cBulkStore($request, $user, $row, $orderDate);
					}
					else
					{
						$this->b2bBulkStore($request, $user, $row, $orderDate);
					}  
				}

				DB::commit(); 
				return $this->successResponse([], 'The order has been successfully added.');  
			} 
			catch (\Exception $e) {
				DB::rollback(); 
				return $this->errorResponse('Excel upload failed. Ensure the file is valid and contains the required data.'); 
			}
		}
		
		private function b2cBulkStore($request, $user, $row, $orderDate)
		{
			// Create Customer
			$customer = Customer::create([
				'user_id'    => $user->id,
				'first_name' => $row[4] ?? '',
				'last_name'  => $row[5] ?? '',
				'email'      => $row[6] ?? null,
				'gst_number' => $row[7] ?? null,
				'mobile'     => $row[8] ?? null,
				'status'     => 1,
			]);
			
			// Create Customer Address if available
			$customerAddress = null;
			if ($row[9] ?? '') {
				$customerAddress = CustomerAddress::create([
					'customer_id' => $customer->id,
					'address'     => $row[9],
					'zip_code'    =>$row[10],
					'city'        => $row[11],
					'state'       => $row[12],
					'country'     => $row[13],
					'status'      => 1,
				]);
			}
					
			// Assuming the column order matches the Excel data structure
			$data = [
				'order_prefix' => Order::generateOrderNumber($user->id),
				'order_date' => $orderDate,
				'shipping_mode' => $row[1] ? strtolower($row[1]) : 'surface', 
				'order_type' => $row[2] ?? 'cod', 
				'cod_amount' => $row[3] ?? 0,
				'warehouse_id' => $request->warehouse_id,
				'customer_id' => $customer->id ?? null,
				'customer_address_id' => $customerAddress->id ?? null,
				'invoice_no' => $row[20] ?? null,
				'invoice_amount' => $row[21] ?? 0,
				'ewaybillno' => $row[22] ?? 0,
				'dimension_type' => 'cm',
				'total_amount' => $row[21] ?? 0,  
				'user_id' => $user->id,
				'status_courier' => 'New',
				'weight_order' => 1, 
				'weight' => $row[23] ?? 0,
				'length' => $row[24] ?? 0,
				'Width' => $row[25] ?? 0,
				'height' => $row[26] ?? 0,
				'status' => 1,
				'created_at' => now(),
				'updated_at' => now()
			]; 
	 
			$order = Order::create($data);

			// Extract and explode values
			$productCategory = $row[14] ? explode(',', $row[14]) : null;
			$productName = $row[15] ? explode(',', $row[15]) : null;
			$productSku = $row[16] ? explode(',', $row[16]) : null;
			$productHsn = $row[17] ? explode(',', $row[17]) : null;
			$productAmount = $row[18] ? explode(',', $row[18]) : null;
			$productQuantity = $row[19] ? explode(',', $row[19]) : null;
		 

			$orderItems = [];
			$totalAmount = 0; // Initialize total amount

			if ($productCategory) {
				foreach ($productCategory as $key => $productCat) { 
					$orderItems[] = [
						'order_id' => $order->id, 
						'product_category' => $productCat,
						'product_name' => $productName[$key] ?? 0,
						'sku_number' => $productSku[$key] ?? 0,
						'hsn_number' => $productHsn[$key] ?? 0,
						'amount' => $productAmount[$key] ?? 0, 
						'ewaybillno' => null, 
						'quantity' => $productQuantity[$key] ?? 0, 
						'created_at' => now(),
						'updated_at' => now() 
					];
				} 
			}

			// Bulk Insert Order Items
			OrderItem::insert($orderItems); 

			// Insert Order Status
			OrderStatus::insert([
				'order_id' => $order->id, 
				'order_status' => 'New', 
				'created_at' => now(), 
				'updated_at' => now()
			]);

			Helper::orderActivity($order->id, 'Order created.');
		}
		
		private function b2bBulkStore($request, $user, $row, $orderDate)
		{
			// Create Customer
			$customer = Customer::create([
				'user_id'    => $user->id,
				'first_name' => $row[4] ?? '',
				'last_name'  => $row[5] ?? '',
				'email'      => $row[6] ?? null,
				'gst_number' => $row[7] ?? null,
				'mobile'     => $row[8] ?? null,
				'status'     => 1,
			]);
			
			// Create Customer Address if available
			$customerAddress = null;
			if ($row[9] ?? '') {
				$customerAddress = CustomerAddress::create([
					'customer_id' => $customer->id,
					'address'     => $row[9],
					'zip_code'    =>$row[10],
					'city'        => $row[11],
					'state'       => $row[12],
					'country'     => $row[13],
					'status'      => 1,
				]);
			}
					
			// Assuming the column order matches the Excel data structure
			$data = [
				'order_prefix' => Order::generateOrderNumber($user->id),
				'order_date' => $orderDate,
				'shipping_mode' => $row[1] ? strtolower($row[1]) : 'surface', 
				'order_type' => $row[2] ?? 'cod', 
				'cod_amount' => $row[3] ?? 0,
				'warehouse_id' => $request->warehouse_id,
				'customer_id' => $customer->id ?? null,
				'customer_address_id' => $customerAddress->id ?? null,
				'invoice_no' => $row[20] ?? null,
				'invoice_amount' => $row[21] ?? 0,
				'ewaybillno' => $row[22] ?? 0,
				'dimension_type' => 'cm',
				'total_amount' => $row[21] ?? 0,  
				'user_id' => $user->id,
				'status_courier' => 'New', 
				'weight_order' => 2, 
				'status' => 1,
				'created_at' => now(),
				'updated_at' => now()
			]; 
	 
			$order = Order::create($data);

			// Extract and explode values
			$productCategory = $row[14] ? explode(',', $row[14]) : null;
			$productName = $row[15] ? explode(',', $row[15]) : null;
			$productSku = $row[16] ? explode(',', $row[16]) : null;
			$productHsn = $row[17] ? explode(',', $row[17]) : null;
			$productAmount = $row[18] ? explode(',', $row[18]) : null;
			$productQuantity = $row[19] ? explode(',', $row[19]) : null;
			
			$noOfBox = $row[23] ? explode(',', $row[23]) : null;
			$weightBox = $row[24] ? explode(',', $row[24]) : null;
			$lengthBox = $row[25] ? explode(',', $row[25]) : null;
			$widthBox = $row[26] ? explode(',', $row[26]) : null;
			$heightBox = $row[27] ? explode(',', $row[27]) : null;
			$weight = $row[28] ?? 0;
		  
			$orderItems = [];
			$height = $width = $length = 0;
			
			if (!empty($productCategory)) 
			{
				foreach ($productCategory as $key => $productCat) { 
					$amount = $request->amount[$key] ?? 0;
					$quantity = $request->quantity[$key] ?? 1;
					  
					$orderItems[] = [
						'order_id' => $order->id, 
						'product_category' => $productCat,
						'product_name' => $productName[$key] ?? null,
						'sku_number' => $productSku[$key] ?? null,
						'hsn_number' => $productHsn[$key] ?? null,
						'amount' => $productAmount[$key] ?? 0, 
						'ewaybillno' => null,
						'quantity' => $productQuantity[$key] ?? 0,  
						'created_at' => now(),
						'updated_at' => now(),
						'dimensions' => json_encode([
							'no_of_box' => $noOfBox[$key] ?? 0,
							'weight' => $weightBox[$key] ?? 0,
							'length' => $lengthBox[$key] ?? 0,
							'width' => $widthBox[$key] ?? 0,
							'height' => $heightBox[$key] ?? 0,
						]),
					];
					
					// Accumulate totals
					$length += $lengthBox[$key] ?? 0; 
					$width  += $widthBox[$key] ?? 0;
					$height += $heightBox[$key] ?? 0;
				}
				
				OrderItem::insert($orderItems);  
			}
			  
			$order->update([ 
				'weight' => $weight,
				'length' => $length,
				'width'  => $width,
				'height' => $height
			]);	
		}
		
		public function codRemittance(Request $request)
		{ 
			$start = $request->post("offset", 0);
			$limit = $request->post("limit", 10); 
			$search = $request->input('search'); 

			$user = Auth::user();
			$role = $user->role;
			$userId = $user->id;

			// Base query
			$baseQuery = Order::with(['user']) 
				->where('orders.status_courier', 'delivered')
				->where('orders.is_remmitance', '0')
				->where('orders.order_type', 'cod');

			// Filters
			if ($role !== "admin") {
				$baseQuery->where('orders.user_id', $userId);
			}

			if ($request->filled('fromdate') && $request->filled('todate')) {
				$baseQuery->whereBetween('orders.delivery_date', [$request->fromdate, $request->todate]);
			} elseif ($request->filled('fromdate')) {
				$baseQuery->whereDate('orders.delivery_date', '>=', $request->fromdate);
			} elseif ($request->filled('todate')) {
				$baseQuery->whereDate('orders.delivery_date', '<=', $request->todate);
			}

			if ($request->filled('shipping_company_id')) {
				$baseQuery->where('orders.shipping_company_id', $request->shipping_company_id);
			}

			// Search filter
			if (!empty($search)) {
				$baseQuery->where(function ($query) use ($search) {
					$query->where('orders.created_at', 'like', "%{$search}%")
						->orWhere('orders.awb_number', 'like', "%{$search}%")
						->orWhere('orders.courier_name', 'like', "%{$search}%")
						->orWhere('orders.status_courier', 'like', "%{$search}%")
						->orWhere('orders.id', 'like', "%{$search}%") 
						->orWhere('orders.order_prefix', 'like', "%{$search}%")
						->orWhereHas('user', function($q) use ($search){
							$q->where('name', 'like', "%{$search}%")
							  ->orWhere('email', 'like', "%{$search}%")
							  ->orWhere('mobile', 'like', "%{$search}%")
							  ->orWhere('company_name', 'like', "%{$search}%");
						});
				});
			}
    
			$orders = $baseQuery
			->orderByDesc('id')
			->offset($start)
			->limit($limit)
			->get();
			
			return $this->successResponse($orders, 'list fetched successfully.');
		}
		
		public function downloadRemittanceExcel($id)
		{ 
			try
			{
				$codVoucher = CodVoucher::with(['codVoucherOrders'])->findOrFail($id); 
				return Excel::download(new CodRemittanceExport($codVoucher), $codVoucher->voucher_no.'.xlsx');
			}
			catch(\Exception $e)
			{
				return $this->errorResponse('failed to download excel.');
			}
		}
		
		public function codPayout(Request $request)
		{   
			$start = $request->get("offset");
			$length = $request->get("limit");
			$searchValue = $request->input("search");
			
			$user = auth()->user();
			$role = $user->role;
			$userId = $user->id;
			
			$query = CodVoucher::with(['user', 'codVoucherOrders']);
			
			// Filters
			if ($role !== "admin") { 
				$query->where('user_id', $userId);
			}
			
			if ($request->filled('fromdate') && $request->filled('todate')) {
				$query->whereBetween('voucher_date', [$request->fromdate, $request->todate]);
				} elseif ($request->filled('fromdate')) {
				$query->whereDate('voucher_date', '>=', $request->fromdate);
				} elseif ($request->filled('todate')) {
				$query->whereDate('voucher_date', '<=', $request->todate);
			}
			
			if ($request->filled('user_id')) { 
				$query->where('user_id', $request->user_id);
			}
			
			if ($request->filled('voucher_status')) {
				$query->where('voucher_status', $request->voucher_status);
			}
			
			if (!empty($searchValue)) {
				$query->where(function ($q) use ($searchValue) {
					$q->where('voucher_no', 'like', "%$searchValue%")
					->orWhereHas('user', function ($uq) use ($searchValue) {
						$uq->where('name', 'like', "%$searchValue%")
						->orWhere('email', 'like', "%$searchValue%")
						->orWhere('mobile', 'like', "%$searchValue%")
						->orWhere('company_name', 'like', "%$searchValue%");
					});
				});
			}
			 
			
			$vouchers = $query->offset($start)
			->limit($length)
			->orderBy('id', 'desc')
			->get();
			
			return $this->successResponse($vouchers, 'list fetched successfully.'); 
		}
		
		public function storePayout(Request $request)
        { 
            try { 
                DB::beginTransaction();
				
				$data = $request->except('id', '_token');
				$data['voucher_status'] = 1;
				
				$codPayout = CodVoucher::findOrFail($request->id);
				$codPayout->update($data);
                DB::commit();
				return $this->successResponse($codPayout, 'Cod Payout successfully.'); 
			} 
			catch (\Exception $e)
			{
				DB::rollBack(); 
				return $this->errorResponse('An error occurred. Please try again.'); 
			}
		}
		public function searchByAwb(Request $request)
		{
			$user = Auth::user(); 
			$userId = $user->id;	

			try {
				$query = $request->get('query');
				if (empty($query)) { 
					return $this->errorResponse('Required Param fired is empprt.');
				}

				$orders = Order::where(function ($q) use ($query) {
					$q->where('order_prefix', 'LIKE', "%{$query}%")
						->orWhere('awb_number', 'LIKE', "%{$query}%");
				})
				->where('user_id', $userId)
				->with(['customer:id,first_name,last_name'])
				->select([
					'id',
					'order_prefix',
					'awb_number',
					'status_courier',
					'customer_id',
					'created_at',
					'weight_order'
				])
				->orderBy('created_at', 'desc')
				->limit(10)
				->get()
				->map(function ($order) {
					return [
						'id' => $order->id,
						'order_prefix' => $order->order_prefix,
						'awb_number' => $order->awb_number,
						'status_courier' => $order->status_courier,
						'customer_name' => $order->customer
							? $order->customer->first_name . ' ' . $order->customer->last_name
							: 'N/A',
						'created_at' => $order->created_at->format('Y-m-d H:i:s'),
						'weight_order' => $order->weight_order
					];
				}); 
			 
				return $this->successResponse($orders, 'order fetched successfully.'); 
			} catch (\Exception $e) { 
				return $this->errorResponse('An error occurred while searching orders'); 
			}
		}
	}
