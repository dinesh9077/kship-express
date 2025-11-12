<?php
	
	namespace App\Http\Controllers;
	
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
	use Helper; 
	use DNS1D;
	use App\Exports\CodRemittanceExport;
	use PhpOffice\PhpSpreadsheet\Shared\Date;
	
	class OrderController extends Controller
	{
		protected $delhiveryService;
		protected $delhiveryB2CService;
		protected $shipMozo;
		public function __construct()
		{  
			$this->middleware('auth')->except(['orderLableGenerate']);  
			$this->shipMozo = new ShipMozo();  
			$this->delhiveryService = new DelhiveryService();  
			$this->delhiveryB2CService = new DelhiveryB2CService();  
		}
		
		public function index()
		{   
			$status = $_GET['status'] ?? 'New';  
			$authUser = Auth::user();
			
			$sellers = Order::join('users', 'users.id', '=', 'orders.user_id')
			->select('users.id', 'users.name')
			->groupBy('users.id', 'users.name')
			->orderBy('users.name', 'asc')
			->get();
			 
			return view('order.index',compact('status', 'sellers', 'authUser'));
		}
		
		public function orderAjax(Request $request)
		{  
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); // Rows per page
			
			$columnIndex_arr = $request->post('order');
			$columnName_arr = $request->post('columns');
			$order_arr = $request->post('order');
			$search_arr = $request->post('search');
			$status = strtolower($request->post('status'));
			$weightOrder = strtolower($request->post('weightOrder'));
			
			$columnIndex = $columnIndex_arr[0]['column']; // Column index
			$order = $columnName_arr[$columnIndex]['data']; // Column name
			$dir = $order_arr[0]['dir']; // Sorting direction
			
			// Ensure 'action' column is ordered by 'id'
			if ($order == 'action') {
				$order = 'id';
			}
			
			$user = Auth::user();
			$role = $user->role;
			$id = $user->id;
			
			// Build the query for orders
			$query = Order::with([
				'customer:id,first_name,last_name,mobile,email',
				'customerAddress:id,address',
				'warehouse:id,warehouse_name,contact_name,contact_number,company_name',
				'user:id,name,company_name,email,mobile',
				'orderItems:id,order_id,product_category,product_name,sku_number,hsn_number,amount,quantity,dimensions',
			])
			->select('id', 'order_prefix', 'user_id', 'customer_id', 'customer_address_id', 'shipping_company_id', 'warehouse_id', 'status_courier', 'order_type', 'created_at', 'weight_order', 'cod_amount', 'awb_number', 'invoice_amount', 'length', 'width', 'height', 'weight', 'reason_cancel', 'courier_name')
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
			$totalData = $query->count();
			$totalFiltered = $totalData;
			
			// Apply search filter
			if (!empty($search_arr)) {
				$search = $search_arr;
				
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
				
				$totalFiltered = $query->count();
			}
			// Fetch paginated and sorted data
			$orders = $query->offset($start)
			->limit($limit)
			->orderBy('orders.' . $order, $dir)
			->get();
			
			// Prepare data response
			$data = [];
			$i = $start + 1;
			
			foreach ($orders as $j => $order) {    
				$isCOD   = strtolower($order->order_type) === "cod";
				$amount  = $isCOD ? $order->cod_amount : $order->invoice_amount;
				$label   = $isCOD ? 'COD Amount' : 'Invoice Amount';

				$warehouse = optional($order->warehouse);
				$customer  = optional($order->customer);
				$address   = optional($order->customerAddress)->address;

				$data[] = [
					'id' => $status == "new" || $status == "all" ? $i : "<input type='checkbox' class='order-checkbox' value={$order->id}>",
					//'id' => $i,

					// Order + Admin info
					'order_id' => '#' . $order->order_prefix
						. ($role === "admin" ? "<br><p>" . e($order->user->name ?? 'N.A') . "</p>" : ''),

					// Seller details
					'seller_details' => "
						<div class='main-cont1-2'>
							<p>{$warehouse->warehouse_name} ({$warehouse->company_name})</p>
							<p>{$warehouse->contact_name}</p>
							<p>{$warehouse->contact_number}</p>
							<p>{$warehouse->address}</p>
						</div>",

					// Customer details + Tooltip
					'customer_details' => "
						<div class='main-cont1-2'> 
							<p>" . optional($order->customer)->first_name . " " . optional($order->customer)->last_name . "</p> 
							<p>" . optional($order->customer)->mobile . "</p> 
							 <div class='tooltip'>View Address<span class='tooltiptext'><b>" . optional($order->customer)->first_name . " " . optional($order->customer)->last_name . 
							"</b><br><b>Address</b>: " . optional($order->customerAddress)->address . "</span></div>
						</div>
					",
					  
					// Shipment details
					'shipment_details' => $this->orderShipmentDetailHtml($order, $status, $weightOrder),

					// Package details
					'package_details' => $this->orderPackageDetailHtml($order),

					// Order type + Amount
					'total_amount' => "
						<div class='main-cont1-2'>
							<p class='" . strtolower($order->order_type) . "'>{$order->order_type}</p>
							<p>{$label}: {$amount}</p>
						</div>",

					// Courier + Action
					'status_courier' => $this->statusCourieHtml($order),
					'action'         => $this->orderAction($order, $status, $weightOrder),
				];

				$i++;
			}

			
			return response()->json([
			"draw" => intval($draw),
			"iTotalRecords" => $totalData,
			"iTotalDisplayRecords" => $totalFiltered,
			"aaData" => $data
			]);
		}
		
		public function statusCourieHtml($order)
		{
			$statusColor = strtolower($order->status_courier) == "cancelled" ? 'cod' : 'prepaid';
			$output = "<p class='{$statusColor}'>{$order->status_courier}</p>";
			if(strtolower($order->status_courier) == "cancelled")
			{
				$output .= "<p style='padding-left:0'>" . $order->reason_cancel ?? 'The client has cancelled this order before it was shipped.' . "</p>";
			}
			$output .= "<p style='padding-left:0'>" . date('Y M d | h:i A', strtotime($order->created_at)) . "</p>";
			return $output;
		}
		
		function orderShipmentDetailHtml($order, $status, $weightOrder)
		{
			// Prepare order items JSON
			$items = $order->orderItems->map(fn($item) => [
				'product_category' => $item->product_category,
				'product_name' => $item->product_name,
				'sku_number' => $item->sku_number,
				'hsn_number' => $item->hsn_number,
				'amount' => $item->amount,
				'quantity' => $item->quantity,
			]);

			$jsonItems = htmlspecialchars($items->toJson(), ENT_QUOTES, 'UTF-8'); // safely encode JSON
			
			$output = '';
			
			$output .= "<div class='main-cont1-1'>
			<div class='checkbox checkbox-purple'>
			Order Prefix/LR No: <a href='" . url("order/details/{$order->id}") . "?weight_order=".$weightOrder."&status=".ucwords($status)."'>#{$order->order_prefix}</a> 
			</div>
			<div class='checkbox checkbox-purple'>
			Courier: {$order->courier_name} 
			</div>"; 
			if($status != "new")
			{
				$output .= "<div class='checkbox checkbox-purple'>
				AWB Number: <a href='" . url("order/details/{$order->id}") . "?weight_order=".$weightOrder."&status=".ucwords($status)."'>#{$order->awb_number}</a>
				</div>"; 
			} 
			$output .= "<span style='padding-left:0'> 
					<a href='javascript:;' class='show-details-btn' data-order='{$jsonItems}' style=' color: #1A4BEC ;'  >
						View Products
					</a>
			</span>
			</div>";
			
			return $output;
		}
		
		function orderPackageDetailHtml($order)
		{
			if (!$order) {
				return '';
			} 
			$length = (float) $order->length;
			$width  = (float) $order->width;
			$height = (float) $order->height;
			$weight = (float) $order->weight;

			// Avoid division by zero → standard divisor 5000
			$volumetricWt = ($length * $width * $height) / 5000;
			
			if($order->weight_order == 2)
			{ 
				$totalBox = $order->orderItems->sum(function ($item) {
					return $item->dimensions['no_of_box'] ?? 0;
				});
				
				$totalVolumetric = 0; 
				foreach ($order->orderItems as $box) {
					$volume = $box->dimensions['length'] * $box->dimensions['width'] * $box->dimensions['height'];
					$volumetricWeight = ($volume / 5000) *  ($box->dimensions['no_of_box'] ?? 0);
					$totalVolumetric += $volumetricWeight;
				}

				return "
					<div class='main-cont1-2'>
						<p>{$totalBox} Boxe(s) - (B2B)</p>
						<p>Dead wt.: " . number_format($weight, 2) . " Kg</p>
						<p>Volumetric wt.: " . number_format($totalVolumetric, 2) . " Kg</p>
					</div>
				";
			}
			else
			{
				return "
					<div class='main-cont1-2'>
						<p>Dead wt.: " . number_format($weight, 2) . " Kg</p>
						<p>{$length} x {$width} x {$height} (cm)</p>
						<p>Volumetric wt.: " . number_format($volumetricWt, 2) . " Kg</p>
					</div>
				";
			}
		}
 
		function orderAction($order, $status, $weightOrder)
		{ 
			$output = "<div class='main-btn-1'>"; 
			if($order->status_courier === "New")
			{
				$output .= "<a href='javascript:;'> 
				<button type='button' class='customization_popup_trigger btn-light-1' data-weight-order=".$weightOrder." onclick='shipNow(this, event)' data-id='{$order->id}'> 
				Ship Now 
				</button> 
				</a>";
			}
			$output .= "<div class='mian-btn'>
			<div class='btn-group'>
			<button class='dropbtn' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'> 
			<i class='fas fa-ellipsis-v'></i> 
			</button>
			<div class='dropdown-menu'>"; 
			$output .="<a class='dropdown-item' href='" . url("order/details/{$order->id}") . "?weight_order=".$weightOrder."&status=".ucwords($status)."'>View Order</a>
			<hr class='m-0' />"; 
			if(config('permission.order.add'))
			{
				$output .="<a class='dropdown-item' href='" . url("order/clone/{$order->id}") . "?weight_order=".$weightOrder."'>Clone Order</a>
				<hr class='m-0' />"; 
			}
			
			if($order->status_courier === "New")
			{
				if(config('permission.order.edit'))
				{
					$output .="<a class='dropdown-item' href='" . url("order/edit/{$order->id}") . "?weight_order=".$weightOrder."'>Edit Order</a>
					<hr class='m-0' />";
				}
				
				if(config('permission.order.delete'))
				{
					/* $output .="<a class='dropdown-item' href='" . url("order/delete/{$order->id}") . "?weight_order=".$weightOrder."' onclick='deleteOrder(this, event)'>Delete Order</a>
					<hr class='m-0' />"; */ 
					$output .="<a class='dropdown-item' href='" . url("order/cancel/{$order->id}") . "?weight_order=".$weightOrder."' style='color: red;' onclick='cancelNewOrder(this, event)'> 
					Cancel Order 
					</a>"; 
				}
				
			}
			
			if(in_array($status, ["manifested", "in transit", "delivered", "all"]) && !empty($order->shipping_company_id) && !empty($order->awb_number))
			{   
				if(!in_array(strtolower($order->status_courier), ["cancelled", "new"]))
				{
					$output .="<a class='dropdown-item' href='" . url("order/tracking-history/{$order->id}") . "?weight_order=".$weightOrder."'> Tracking Order </a><hr class='m-0' />"; 
					if($order->shipping_company_id == 2)
					{
						$output .="<a class='dropdown-item' href='" . url("order/waybill-copy/{$order->id}") . "?weight_order=".$weightOrder."' target='_blank'> Waybill Copy</a><hr class='m-0' />"; 
						
						$output .="<a class='dropdown-item' href='" . url("order/shipping-lable/{$order->id}") . "?weight_order=".$weightOrder."' target='_blank'> Shipping Label </a><hr class='m-0' />"; 
					}
					$output .="<a class='dropdown-item' href='" . url("order/download-label/{$order->id}") . "?weight_order=".$weightOrder."' target='_blank'> Order Label </a><hr class='m-0' />"; 
					
					if(in_array(strtolower($order->status_courier), ["manifested", "in transit", "Pickup Pending", "open", "scheduled"]))
					{
						if(config('permission.order.delete'))
						{
							$output .="<a class='dropdown-item' href='" . url("order/cancel-api/{$order->id}") . "?weight_order=".$weightOrder."' style='color: red;' onclick='cancelOrderApi(this, event)'> 
							Cancel Order 
							</a>";  
						}
					}
				}
			}
			
			$output .="</div>
			</div>
			</div>
			</div>";
			return $output;
		} 
		
		public function orderCreate()
		{
			$user = Auth::user();  
			$weightOrder = request('weight_order', 1);
			return view($weightOrder == 1 ? 'order.create-b2c' : 'order.create-b2b', compact('user'));
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
				if (Order::where('order_prefix', $request->order_prefix)->exists()) {
					return response()->json(['status' => 'error', 'msg' => 'The order number already exists.']);
				}
				
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
				$data['status_courier'] = 'New';
				// $data['customer_id'] = $customer->id;
				// $data['customer_address_id'] = $customerAddress->id ?? null;
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
				
				DB::commit(); // Commit Transaction
				return response()->json(['status' => 'success', 'msg' => 'The order has been successfully added.']);
			} 
			catch (\Exception $e) {
				DB::rollback(); // Rollback in case of failure
				return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
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
			$user = Auth::user();
			$order = Order::with( 'orderItems')->find($id);  
			$weightOrder = request('weight_order', 1);
			return view($weightOrder == 1 ? 'order.edit-b2c' : 'order.edit-b2b', compact('user', 'order', 'weightOrder')); 
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
				
				DB::commit(); // Commit Transaction
				return response()->json(['status' => 'success', 'msg' => 'The order have been updated successfully.']);
			} 
			catch (\Exception $e) {
				DB::rollback(); // Rollback in case of failure
				return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
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
		
		public function orderClone($id)
		{
		    $user = Auth::user();
			$order = Order::with( 'orderItems')->find($id);  
			$weightOrder = request('weight_order', 1);
			return view($weightOrder == 1 ? 'order.clone-b2c' : 'order.clone-b2b', compact('user', 'order', 'weightOrder'));  
		}
		
		public function orderDelete($id)
		{ 
			DB::beginTransaction();
			try
			{ 
				$order = Order::find($id); 
				$order->orderItems()->delete();
				
				$documents = [
					'order_image',
					'invoice_document'
				];

				foreach ($documents as $document) {
					$imagePaths = $order->$document ?? [];

					if (!empty($imagePaths) && is_array($imagePaths)) {
						foreach ($imagePaths as $image) {
							$filePath = "public/orders/{$order->id}/{$image}";

							if (Storage::exists($filePath)) {
								Storage::delete($filePath);
							}
						}
					}
				}
 
				// Remove the entire order directory if it's empty
				$orderDirectory = 'public/orders/' . $order->id;
				if (Storage::exists($orderDirectory) && count(Storage::files($orderDirectory)) === 0) {
					Storage::deleteDirectory($orderDirectory);
				}
				
				$order->delete();
				
				DB::commit();
				return redirect()->back()->with('success','The order has been successfully deleted.'); 
			}
			catch(\Exception $e)
			{ 
				return redirect()->back()->with('error','Something went wrong.'); 
			}  	
			
		}
		
		public function orderShippingLableDownload($orderId)
        {
            $order = Order::with('shippingCompany')->find($orderId);
			if(!$order->shippingCompany && !empty($order->awb_number))
			{
				return back()->with('error', 'Something went wrong');
			}
			
			if ($order->shippingCompany->id == 2) {  
				$response = $this->delhiveryService->shippingLableByLrNo($order->lr_no, $order->shippingCompany);
				
				if (!($response['success'] ?? false)) {
					$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response']['error']['message'] ?? 'An error occurred.');
					return back()->with('error', $errorMsg);
				}
				
				if ((isset($response['response']['success']) && !$response['response']['success']))
				{ 
					return back()->with('error', $response['response']['error']['message'] ?? 'An error occurred.');
				} 
				if(!$response['response']['data'])
				{
					return back()->with('error', 'Something went wrong');
				} 
				$data = $response['response']['data'] ?? [];
				
				// Fetch each label image and convert to Base64
				$labels = [];
				foreach ($data as $url) {
					$response = $this->delhiveryService->getLabelImage($url);
					if(isset($response['response']['success']) && $response['response']['success'])
					{
						$labels[] = $response['response']['data'] ?? '';
					}
				}
				return view('order.shipping-label', compact('labels', 'order'));
			}  
			return back()->with('error', 'Something went wrong');
		}
		
		public function orderWayBillCopy($orderId)
		{
		    $order = Order::with('shippingCompany')->find($orderId);
			if(!$order->shippingCompany && !empty($order->awb_number))
			{
				return back()->with('error', 'Something went wrong');
			}
			if ($order->shippingCompany->id == 2) {  
				$response = $this->delhiveryService->waybillCopyByLrNo($order->lr_no, $order->shippingCompany);
				
				if (!($response['success'] ?? false)) {
					$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response']['error']['message'] ?? 'An error occurred.');
					return back()->with('error', $errorMsg);
				}
				
				if ((isset($response['response']['success']) && !$response['response']['success']))
				{ 
					return back()->with('error', $response['response']['error']['message'] ?? 'An error occurred.');
				} 
				if(!$response['response'])
				{
					return back()->with('error', 'Something went wrong');
				}
				return \Response::make($response['response'], 200, [
				'Content-Type' => 'application/pdf',
				'Content-Disposition' => 'inline; filename="LR_Copy_'.$order->lr_no.'.pdf"'
				]);
			}
			return back()->with('error', 'Something went wrong'); 
		}
		
		public function downloadShippingLabel($id)
        {
            $order = Order::find($id);
            $labelUrl = $order->label;
            
            // Download the file from the URL
            $response = Http::get($labelUrl);
            
            // Define the path where you want to save the file
            $filePath = 'public/labels/' . $id . '.pdf';
            
            // Save the file to the server
            Storage::put($filePath, $response->body());
            
            // Generate a URL to access the saved file
            $fileUrl = url(Storage::url($filePath));
            
            // Return the full URL for downloading the file
            return $fileUrl;
		}

		public function orderLableDownload($orderId)
		{
			error_reporting(0);

			// Load order with related data
			$order = Order::with([
				'shippingCompany:id,logo',
				'customer:id,first_name,last_name,mobile',
				'customerAddress',
				'warehouse',
				'orderItems'
			])->findOrFail($orderId);

			// Related data
			$shipping = $order->shippingCompany ?? null;
			$customer = $order->customer ?? null;
			$customerAddr = $order->customerAddress ?? null;
			$products = $order->orderItems ?? null;
			$hideLabel = $order->warehouse ? $order->warehouse->label_options : [];

			// Generate barcodes
			$barcodePng = DNS1D::getBarcodePNG($order->awb_number, 'C128', 2.5, 60);
			$orderIdBarcodePng = DNS1D::getBarcodePNG($order->shipment_id ?? $order->order_prefix, 'C128', 2.5, 60);

			// Render Blade HTML
			$htmlView = view('order.single_label', compact(
				'shipping',
				'order',
				'customer',
				'customerAddr',
				'products',
				'barcodePng',
				'hideLabel',
				'orderIdBarcodePng'
			))->render();

			// ✅ Create PDF — set to A4 portrait (or landscape if preferred)
			$pdf = PDF::loadHtml($htmlView)
				->setPaper('A4', 'portrait') // or 'landscape'
				->setOption('isHtml5ParserEnabled', true)
				->setOption('isRemoteEnabled', true);

			// ✅ Return A4 label PDF
			return $pdf->stream('order_label_' . $orderId . '.pdf');
		}


		public function alllabeldownload(Request $request)
        {
			error_reporting(0);
			try {
				$orderIds = $request->input('order_ids'); 
				if (empty($orderIds) || !is_array($orderIds)) {
					return back()->with('error', 'No orders selected');	 
				}
				
				// Eager load related models in one go
				$orders = Order::with([
					'shippingCompany', 'customer', 'customerAddress',
					'orderItems', 'warehouse', 'user'
				])
				->whereIn('id', $orderIds)->get();
				 
				if ($orders->isEmpty()) {
					return back()->with('error', 'No valid orders found');	 
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
					return back()->with('error', 'All labels are empty'); 
				}
				
				$mergedHtml = implode('', $htmlSections); 
				$pdf = PDF::loadHtml($mergedHtml);
				return $pdf->download('labels.pdf');  
			} 
			catch (\Exception $e) 
			{
				return back()->with('error', 'PDF generation failed: ' . $e->getMessage());  
			}
		}  
		
		public function orderCancel($id)
		{
			DB::beginTransaction(); // Begin Transaction
			
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
				
				return back()->with('success', 'The order has been successfully cancelled.');
				} catch (\Exception $e) {
				DB::rollback(); // Rollback Transaction on Failure
				return back()->with('error', 'Failed to cancel the order: ' . $e->getMessage());
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
					return back()->with('error', 'Shipment not found.');	 
				}
				
				$orderCancelledData = []; 
				if ($shippingCompany->id == 1) {  
					$response = $this->shipMozo->cancelShipment($order, $shippingCompany);  
					if (!($response['success'] ?? false)) {
						$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response']['message'] ?? 'An error occurred.');
						return back()->with('error', $errorMsg);
					}
					
					if ((isset($response['response']['result']) && $response['response']['result'] == 0))
					{ 
						return back()->with('error', $response['response']['message'] ?? 'An error occurred.');
					} 
					
					$orderCancelledData = [
						'status_courier' => 'cancelled',
						'order_cancel_date' => now(),
						'reason_cancel' => 'cancelled shipment by user',
					]; 
				} 
				
				if (!$orderCancelledData) {
					throw new \Exception('Something went wrong while processing the cancellation.');
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
				return back()->with('success', 'The order has been successfully cancelled.');
				
				} catch (\Exception $e) {
				DB::rollBack(); 
				return back()->with('error', $e->getMessage());
			}
		}
		
		public function orderTrackingHistory($orderId)
		{ 
			$order = Order::with('shippingCompany')->find($orderId);
			$shippingCompany = $order->shippingCompany ?? [];
			
			if(!$shippingCompany) 
			{
				return back()->with('error', 'Something went wrong.');
			} 
			
			$trackingHistories = []; 
			if(!empty($order->awb_number))
			{  
				if($shippingCompany->id == 1)
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
		
			if(!$trackingHistories)
			{
				return back()->with('error', 'The order tracking data not found.');
			}
			
			return view('order.tracking-history', compact('order', 'trackingHistories', 'shippingCompany'));
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
			
			return view('order.details', compact('order', 'orderActivities', 'trackingHistories'));
		}
		 
		public function orderShipCharge($orderId)
		{
			$user = Auth::user();
			$role = $user->role;
			$charge = $user->charge;
			$charge_type = $user->charge_type;
			
			$order = Order::with(['warehouse', 'customerAddress', 'orderItems'])->find($orderId);
			if (!$order) {
				return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
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
							'percentage_amount' => 0,
							'responseData' => $responseData
						];
					}
				} 
			}
			 
			$couriers = $couriers ? collect($couriers)->sortBy('total_charges') : collect();
			 
			$view = view('order.shipment_charges', [
				'order' => $order,  
				'couriers' => $couriers,
				'total_courier' => count($couriers)
			])->render();
			
			return response()->json(['status' => 'success', 'view' => $view]);
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
			DB::beginTransaction(); // Start Transaction
			try {
				$requestData = collect(json_decode($request->data, true) ?? []); 
				
				$shippingCompany = ShippingCompany::findOrFail($requestData['shipping_company_id']);
			 
				if (!$shippingCompany) {
					return response()->json(['status' => 'error', 'msg' => 'Invalid Shipping Company']);
				}
				
				$order = Order::with(['warehouse', 'customer', 'customerAddress', 'orderItems', 'user'])->findOrFail($requestData['order_id']);
				$user = $order->user;
				
				$walletAmount = $user->wallet_amount;
					
				if ($user->role == "user") {
					if ($walletAmount < 100) {
						return response()->json(['status' => 'error', 'wallet' => 1, 'msg' => 'Minimum 100 Wallet Balance required']);
					}
					
					if ($requestData['total_charges'] > $walletAmount) {
						return response()->json(['status' => 'error', 'msg' => 'Insufficient wallet balance']);
					}
				}
				$courierWarehouse = $order->warehouse ?? null;	
				if(!$courierWarehouse)
				{
					return response()->json(['status' => 'error', 'msg' => 'Pickup warehouse address empty.']);
				}
				
				$courierLogo = $requestData['shipping_company_logo'] ?? null;
				if ($shippingCompany->id == 1) 
				{    
					$existStatus = $courierWarehouse->created ?? [];

					if (
						(isset($existStatus['shipMozo']) && $existStatus['shipMozo'] == 0)
						|| empty($existStatus->shipping_id)
					) { 	 
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
						return response()->json(['status' => 'error', 'msg' => $errorMsg]);
					}
					
					if ((isset($response['response']['result']) && $response['response']['result'] == 0))
					{
					return response()->json(['status' => 'error', 'msg' => $$response['response']['data']['error'] ?? $response['response']['message'] ?? 'An error occurred.']);	 
					} 
					
					$orderId = $response['response']['data']['order_id'] ?? '';
					if (!$orderId) {
						$errorMsg = 'Somthing went wrong.';
						return response()->json(['status' => 'error', 'msg' => $errorMsg]);
					}
					
					$courierResponse = $this->shipMozo->assignCourier($orderId, $requestData['courier_id'] ?? null, $shippingCompany);  
					if (!($courierResponse['success'] ?? false)) {
						$errorMsg = $courierResponse['response']['errors'][0]['message'] ?? ($courierResponse['response']['error'] ?? 'An error occurred.');
						return response()->json(['status' => 'error', 'msg' => $errorMsg]);
					}
					
					if ((isset($courierResponse['response']['result']) && $courierResponse['response']['result'] == 0))
					{
						return response()->json(['status' => 'error', 'msg' => $courierResponse['response']['data']['error'] ?? $courierResponse['response']['message'] ?? 'An error occurred.']);	 
					}   
					$awbNumber = $courierResponse['response']['data']['awb_number'] ?? '';
					
					$shipment_id = $courierResponse['response']['data']['order_id'] ?? '';
					$lr_no = $courierResponse['response']['data']['order_id'] ?? '' ?? null; 
					$awb_number = $awbNumber ?? null; 
					$courier_id = $requestData['courier_id'] ?? null; 
					$statusCourier = 'manifested';
					$apiResponse = $courierResponse;   
					if(empty($courierLogo))
					{
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
					'awb_number' => $awb_number ?? null,
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
				
				DB::commit(); // Commit Transaction
				
				$warehouse = $order->warehouse ?? null;  
				if ($warehouse) {
					$pickupAddress = "<img src='" . asset('assets/images/order-1/location.png') . "' style='margin-right: 10px;'>
					<h5><b>Pick Up Address <br></b>{$warehouse->company_name}, {$warehouse->address}, 
					{$warehouse->city}, {$warehouse->state}, {$warehouse->country}, {$warehouse->zip_code}</h5>";
					} else {
					$pickupAddress = "<h5><b>Pick Up Address Not Available</b></h5>";
				}
				
				$msg = "<h5> <img src='" . asset('assets/images/order-1/green-teu.png') . "' style='margin-right: 5px;'>
				Your package has been assigned to <b>{$order->courier_name}</b>."; 
				if(!empty($awb_number))
				{
					$msg .= "The AWB number is <span>{$awb_number}</span></h5>";
				}
				 
				return response()->json([
					'status' => 'success',
					'msg' => $msg,
					'pickup_address' => $pickupAddress,
					'order_id' => $order->id,
					'shipping_id' => $shippingCompany->id
				]);
				
			} 
			catch (\Exception $e) 
			{
				DB::rollBack();  
				return response()->json(['status' => 'error', 'msg' => 'Something went wrong!'. $e->getMessage()]);
			}
		}
		    
		// Warehouse pickup 
		public function orderWarehouseList()
		{
			$user = Auth::user();
			$query = CourierWarehouse::query();
			if($user->role === "user")
			{
				$query->where('user_id', $user->id);
			}
			$pickupWarehouses = $query->where('warehouse_status', 1)
			->get();
			
			$options = ['<option value="">Select Pickup Location</option>'];
			
			foreach ($pickupWarehouses as $pickupWarehouse) {
				$jsonWarehouse = htmlspecialchars(json_encode($pickupWarehouse), ENT_QUOTES, 'UTF-8');
				
				$options[] = '<option value="' . $pickupWarehouse->id . '" data-warehouse=\'' . $jsonWarehouse . '\'>'
				. $pickupWarehouse->warehouse_name . ' - (' . $pickupWarehouse->company_name . ')</option>';
			}
			
			return response()->json([
			'status' => 'success',
			'output' => implode('', $options)
			]);
		}
		
		public function orderWarehouseCreate()
		{
			$view = view('order.modals.warehouse-create')->render();
			return response()->json(['status'=> 'success', 'view'=> $view]);
		}
		
		// Recipeint/Customer
		public function orderCustomerList()
		{
			$user = Auth::user();
			$customer = Customer::query();
			if ($user->role === "user") {
				$customer->where('user_id', $user->id);
			}
			$customers = $customer->where('status', 1)->get();

			$options = ['<option value="">Select Recipeint/Customer</option>'];

			foreach ($customers as $customer) {
				$options[] = '<option value="' . $customer->id . '">'
					. $customer->first_name . ' ' . $customer->last_name . ' - (' . $customer->mobile . ')</option>';
			}

			return response()->json([
				'status' => 'success',
				'output' => implode('', $options)
			]);
		}
		
		public function orderCustomerAddressList($customerId)
		{
			$user = Auth::user();
			$customerAddresses = CustomerAddress::where('customer_id', $customerId)->get();
			
			$options = ['<option value="">Select Customer Address</option>'];
			
			foreach ($customerAddresses as $customerAddress) { 
				$options[] = '<option value="' . $customerAddress->id . '">'
				. $customerAddress->address.' '.$customerAddress->city . ' '.$customerAddress->states . ' '.$customerAddress->country . ', ' . $customerAddress->zip_code . '</option>';
			}
			
			return response()->json([
			'status' => 'success',
			'output' => implode('', $options)
			]);
		}
		
		public function orderCustomerCreate()
		{
			$view = view('order.modals.customer-create')->render();
			return response()->json(['status'=> 'success', 'view'=> $view]);
		}
		
		public function orderCustomerAddressCreate($customerId)
		{ 
			$view = view('order.modals.customer-address-create', compact('customerId'))->render();
			return response()->json(['status'=> 'success', 'view'=> $view]);
		}
		
		public function orderCustomerAddressStore(Request $request)
		{  
			try {
				DB::beginTransaction(); 
				$customer = Customer::findOrFail($request->customer_id);  
				
				if ($request->has('address') && count($request->address) > 0) {
					$addresses = [];
					foreach ($request->address as $key => $addr) {
						$addresses[] = [
						'customer_id' => $customer->id,
						'address' => $addr,
						'country' => $request->country[$key],
						'state' => $request->state[$key],
						'city' => $request->city[$key],
						'zip_code' => $request->zip_code[$key],
						'status' => 1,
						'created_at' => now(),
						'updated_at' => now(),
						];
					}
					CustomerAddress::insert($addresses);
				}
				$customerAddressId = optional($customer->latestCustomerAddress)->id ?? ''; 
				DB::commit();
				return response()->json(['status' => 'success', 'msg' => 'The customer address has been successfully added.', 'customer_id' => $customer->id, 'customer_address_id' => $customerAddressId]);
				} catch (\Exception $e) {
				DB::rollBack();
				return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
			}
		} 
		
		//Bulk Order
		public function orderBulkCreate()
		{
			$user = Auth::user();  
			return view('order.bulk-create', compact('user'));
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
					return response()->json(['status' => 'error', 'msg' => 'No valid data found in the Excel file.']);
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

				DB::commit(); // Commit Transaction
				return response()->json(['status' => 'success', 'msg' => 'The order has been successfully added.', 'type_of_package' => $request->type_of_package]);
			} 
			catch (\Exception $e) {
				DB::rollback(); // Rollback in case of failure
				return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
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
		
		public function codRemittance()
		{   
		    //$shippingCompanies = ShippingCompany::whereStatus(1)->get();  
		    return view('remmitance.index');
		}
		
		public function remmitanceAjax(Request $request)
		{ 
			$draw = $request->post('draw');
			$start = $request->post("start", 0);
			$limit = $request->post("length", 10);

			// Handle ordering safely
			$order = $request->post('order', []);
			$columns = $request->post('columns', []);

			$orderColumnIndex = $order[0]['column'] ?? 0;
			$orderColumnName = $columns[$orderColumnIndex]['data'] ?? 'id';
			$orderDir = $order[0]['dir'] ?? 'asc';

			$orderColumnName = $orderColumnName === 'action' ? 'id' : $orderColumnName;

			$search = $request->input('search'); // DataTables sends `search[value]`
			$status = $request->post('status');

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

			// Count for DataTables
			$totalData = $baseQuery->count();

			// Get total amount
			$resultsCloned = (clone $baseQuery)->select(DB::raw('SUM(orders.total_amount) as total_Amt'))->first();

			// Pagination + Sorting
			$orders = $baseQuery
				->orderBy($orderColumnName, $orderDir) // 👈 Apply ordering
				->offset($start)
				->limit($limit)
				->get();

			// Prepare output
			$data = [];
			$i = $start + 1;
			foreach ($orders as $order) {
				$userName = $order->user->name ?? 'N/A';
				$sellerCompany = $order->user->company_name ?? 'N/A';
				$userEmail = $order->user->email ?? 'N/A';
				$userMobile = $order->user->mobile ?? 'N/A';

				$data[] = [
					'id' =>  $i,
					'order_id' => $order->id.' <br>#'.$order->order_prefix,
					'seller_details' => "<div class='main-cont1-2'><p>{$userName} ({$sellerCompany})</p><p>{$userEmail}</p><p>{$userMobile}</p></div>",
					'amount' => $order->cod_amount,
					'delivery_date' => $order->delivery_date,
					'shipment_details' => "<p>{$order->courier_name}</p><p>AWB No: <b>{$order->awb_number}</b></p>",
					'status_courier' => "<p class='prepaid'>{$order->status_courier}</p>",
				];
				$i++;
			}

			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $totalData,
				"iTotalDisplayRecords" => $totalData,
				"aaData" => $data,
				"totAmt" => $resultsCloned->total_Amt ?? 0,
			]);
		}

		
		public function downloadRemittanceExcel($id)
		{ 
			$codVoucher = CodVoucher::with(['codVoucherOrders'])->find($id); 
			return Excel::download(new CodRemittanceExport($codVoucher), $codVoucher->voucher_no.'.xlsx');
		}
		
		public function codPayout()
		{ 
			$users = CodVoucher::select(DB::raw('MAX(id) as id'), 'user_id')
			->groupBy('user_id')
			->with('user')
			->get();
		 
		    return view('remmitance.cod-payout', compact('users'));
		}
		
		public function codPayoutAjax(Request $request)
		{
			$draw = $request->get('draw');
			$start = $request->get("start");
			$length = $request->get("length");
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
			
			$total = $query->count();
			
			$vouchers = $query->offset($start)
			->limit($length)
			->orderBy('id', 'desc')
			->get();
			
			$data = [];
			$i = $start + 1;
			foreach ($vouchers as $voucher) {
				$userName = $voucher->user->name ?? 'N/A';
				$sellerCompany = $voucher->user->company_name ?? 'N/A';
				$userEmail = $voucher->user->email ?? 'N/A';
				$userMobile = $voucher->user->mobile ?? 'N/A';
				
				$action = '<div class="main-btn-1">
				<div class="mian-btn">
				<div class="btn-group">';
				if($voucher->voucher_status == 0 && auth()->user()->role == "admin" && config('permission.cod_payout.add'))
				{
					$action .='<button class="btn btn-primary" data-payout-id="'.$voucher->id.'" onclick="payNow(this)" type="button"> Pay Now</button>'; 
				}
				$url = route('remmitance.download.excel', $voucher->id);
				$action .='<a class="btn download-btn" href="'.$url.'">Download</a>';
				$action .='</div>
				</div>
				</div>';
				
				$data[] = [
					'id' => $i,
					'seller_details' => "<div class='main-cont1-2'><p>{$userName} ({$sellerCompany})</p><p>{$userEmail}</p><p>{$userMobile}</p></div>",
					'voucher_date' => $voucher->voucher_date,
					'voucher_no' => $voucher->voucher_no,
					'amount' => $voucher->amount ?? '',
					'status' => $voucher->voucher_status == 0 ? '<span class="badge badge-danger">UnPaid</span>' : '<span class="badge badge-success">Paid</span>', 
					'remarks' => $voucher->remarks ?? 'N/A',
					'reference_no' => $voucher->reference_no ?? 'N/A',
					'payout_date' => $voucher->payout_date ?? 'N/A',
					'action' => $action,
				];
				$i++;
			}
			
			return response()->json([
			"draw" => intval($draw),
			"recordsTotal" => $total,
			"recordsFiltered" => $total,
			"data" => $data,
			]);
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
				
                return redirect()->back()->with('success', 'Cod Payout successfully.');
			} catch (\Exception $e)
			{
				DB::rollBack(); 
				return redirect()->back()->with('error', 'An error occurred. Please try again.');
			}
		}
 		public function searchByAwb(Request $request)
		{
			try {
				$query = $request->get('query');
				if (empty($query)) {
					return response()->json([
						'results' => []
					]);
				}

				$orders = Order::where(function ($q) use ($query) {
					$q->where('order_prefix', 'LIKE', "%{$query}%")
						->orWhere('awb_number', 'LIKE', "%{$query}%");
				})
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

				return response()->json([
					'results' => $orders
				]);

			} catch (\Exception $e) {
				\Log::error('Order search error: ' . $e->getMessage());
				return response()->json([
					'error' => 'An error occurred while searching orders',
					'results' => []
				], 500);
			}
		}
	}
