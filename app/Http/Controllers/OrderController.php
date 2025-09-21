<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\{DB, Auth, File, Storage, Validator, Http, Log};
	use App\Models\{
		Order, OrderItem, Vendor, VendorAddress, OrderActivity, OrderStatus, Billing, Packaging, 
		WeightFreeze, Customer, User, CustomerAddress, PincodeService, ShippingCompany, 
		CourierWarehouse, ProductCategory
	};
	use App\Exports\PendingStarOrderExport;
	use Maatwebsite\Excel\Facades\Excel;
	use PDF;  
	use App\Services\DelhiveryService;
	use App\Services\DelhiveryB2CService;
	use App\Imports\BulkOrder;
	use Helper; 
	
	class OrderController extends Controller
	{
		protected $delhiveryService;
		protected $delhiveryB2CService;
		public function __construct()
		{  
			$this->middleware('auth')->except(['orderLableGenerate', 'orderTrackingHistoryGlobal']);  
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
			
			$role = Auth::user()->role;
			$id = Auth::user()->id;
			
			// Build the query for orders
			$query = Order::with([
				'customer:id,first_name,last_name,mobile,email',
				'customerAddress:id,address',
				'warehouse:id,warehouse_name,contact_name,contact_number,company_name',
				'user:id,name,company_name,email,mobile',
				'orderItems:id,order_id,product_discription,amount,quantity',
			])
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
			
			foreach ($orders as $j => $order)
			{  
				$totalOrderTypeAmount =  $order->order_type == "cod" ? $order->cod_amount : $order->invoice_amount;
				$totalOrderTypeLabel =  $order->order_type == "cod" ? 'Cod Amount' : 'Invoice Amount';
				$data[] = [
					//'id' => $status == "new" ? $j + 1 : "<input type='checkbox' class='order-checkbox' value=".$order->id.">",
					'id' => $i,
					'order_id' => '#' . $order->id . ($role == "admin" ? '<br><p>' . ($order->user->name ?? 'N.A') . '</p>' : ''), 
					'seller_details' => "<div class='main-cont1-2'>
					<p>" . optional($order->warehouse)->warehouse_name . " (" . optional($order->warehouse)->company_name . ")</p>
					<p>" . optional($order->warehouse)->contact_name . "</p>
					<p>" . optional($order->warehouse)->contact_number . "</p>
					<p>" . optional($order->warehouse)->address . "</p>
					</div>", 
					'customer_details' => "<div class='main-cont1-2'>
					<p>" . optional($order->customer)->first_name . " " . optional($order->customer)->last_name . "</p>
					<p>" . optional($order->customer)->mobile . "</p>
					<span style='padding-left:0'> 
					<a href='javascript:;'> 
					<div class='tooltip' data-toggle='tooltip' data-placement='top' title='" . optional($order->customerAddress)->address . "'> 
					View Address 
					</div>
					</a> 
					</span> 
					</div>",
					'shipment_details' => $this->orderShipmentDetailHtml($order, $status, $weightOrder),  
					'total_amount' => "<div class='main-cont1-2'>
					<p class='" . strtolower($order->order_type) . "'>{$order->order_type}</p> 
					<p>{$totalOrderTypeLabel} : {$totalOrderTypeAmount}</p>
					</div>",
					'status_courier' => $this->statusCourieHtml($order),
					'action' => $this->orderAction($order, $status, $weightOrder)
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
			$productDetails = $order->orderItems
			->map(fn($item) => "<p>{$item->product_description} Amount: {$item->amount} No Of Box: {$item->quantity}</p>")
			->implode(' | ');
			
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
			<a href='javascript:;'> 
			<div class='tooltip' data-toggle='tooltip' data-placement='top' title='" . strip_tags($productDetails) . "'> 
			View Products 
			</div>
			</a> 
			</span>
			</div>";
			
			return $output;
		}
		
		function orderAction($order, $status, $weightOrder)
		{ 
			$output = "<div class='main-btn-1'>";
			if(in_array($status, ["new", "all"]) && config('permission.order.add'))
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
			<i class='fas fa-ellipsis-h'></i> 
			</button>
			<div class='dropdown-menu'>"; 
			$output .="<a class='dropdown-item' href='" . url("order/details/{$order->id}") . "?weight_order=".$weightOrder."&status=".ucwords($status)."'>View Order</a>
			<hr class='m-0' />"; 
			if(config('permission.order.add'))
			{
				$output .="<a class='dropdown-item' href='" . url("order/clone/{$order->id}") . "?weight_order=".$weightOrder."'>Clone Order</a>
				<hr class='m-0' />"; 
			}
			if($status == "new")
			{
				if(config('permission.order.edit'))
				{
					$output .="<a class='dropdown-item' href='" . url("order/edit/{$order->id}") . "?weight_order=".$weightOrder."'>Edit Order</a>
					<hr class='m-0' />";
				}
				if(config('permission.order.delete'))
				{
					$output .="<a class='dropdown-item' href='" . url("order/delete/{$order->id}") . "?weight_order=".$weightOrder."' onclick='deleteOrder(this, event)'>Delete Order</a>
					<hr class='m-0' />"; 
					$output .="<a class='dropdown-item' href='" . url("order/cancel/{$order->id}") . "?weight_order=".$weightOrder."' style='color: red;' onclick='cancelNewOrder(this, event)'> 
					Cancel Order 
					</a>"; 
				}
				
			}
			
			if(in_array($status, ["manifested", "in transit", "all"]) && !empty($order->shipping_company_id) && !empty($order->awb_number))
			{   
				if(!in_array(strtolower($order->status_courier), ["cancelled", "new"]))
				{
					$output .="<a class='dropdown-item' href='" . url("order/tracking-history/{$order->id}") . "?weight_order=".$weightOrder."'> Tracking Order </a><hr class='m-0' />"; 
					if($order->shipping_company_id == 2)
					{
						$output .="<a class='dropdown-item' href='" . url("order/waybill-copy/{$order->id}") . "?weight_order=".$weightOrder."' target='_blank'> Waybill Copy</a><hr class='m-0' />"; 
						
						$output .="<a class='dropdown-item' href='" . url("order/shipping-lable/{$order->id}") . "?weight_order=".$weightOrder."' target='_blank'> Shipping Label </a><hr class='m-0' />"; 
					}
					$output .="<a class='dropdown-item' href='" . url("order/label/{$order->id}") . "?weight_order=".$weightOrder."' target='_blank'> Order Label </a><hr class='m-0' />"; 
					
					if(in_array(strtolower($order->status_courier), ["manifested", "in transit", "pending", "open", "scheduled"]))
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
			return view('order.create', compact('user'));
		}
		  
		public function orderStore(Request $request)
		{     
			$user_id = Auth::id();  
			$data = $request->except('_token', 'product_name', 'product_category', 'sku_number', 'hsn_number', 'amount', 'quantity', 'weight', 'length', 'width', 'height', 'order_image', 'invoice_document'); 
			$data['user_id'] = $user_id;
			$data['status_courier'] = 'new';
			$data['weight'] = $request->total_weight;
			$data['is_fragile_item'] = $request->is_fragile_item ?? 0;
			$data['total_amount'] = $request->invoice_amount;
			$data['status'] = 1;
			$data['created_at'] = now();
			$data['updated_at'] = now();
			
			// Check if order prefix already exists
			if (Order::where('order_prefix', $request->order_prefix)->exists()) {
				return response()->json(['status' => 'error', 'msg' => 'The order number already exists.']);
			}
			
			DB::beginTransaction();
			
			try {   
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
							'updated_at' => now(),
							'dimensions' => json_encode([
								'weight' => $request->weight[$key] ?? 0,
								'length' => $request->length[$key] ?? 0,
								'width' => $request->width[$key] ?? 0,
								'height' => $request->height[$key] ?? 0,
							]),
						];
					}
					
					OrderItem::insert($orderItems);  
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
				 
				foreach ($bulkOrder->rows as $row) {
					// Assuming the column order matches the Excel data structure
					$data = [
						'order_prefix' => Order::generateOrderNumber($user->id),
						'shipping_mode' => $row[0] ? strtolower($row[0]) : null,
						'order_date' => now()->toDateString(),
						'freight_mode' => $row[1] ?? null, 
						'order_type' => $row[9] ?? 'prepaid', 
						'cod_amount' => $row[10] ?? 0,
						'warehouse_id' => $request->warehouse_id,
						'customer_id' => $request->customer_id,
						'customer_address_id' => $request->customer_address_id,
						'invoice_no' => $row[11] ?? null,
						'invoice_amount' => $row[12] ?? 0,
						'ewaybillno' => $row[13] ?? 0,
						'dimension_type' => 'cm',
						'total_amount' => 0,  
						'user_id' => $user->id,
						'status_courier' => 'new',
						'status' => 1,
						'created_at' => now(),
						'updated_at' => now()
					]; 
			
					// Insert Order and get ID 
					$order = Order::create($data);

					// Handle invoice documents
					$invoiceDocPaths = []; 
					if ($request->hasFile('invoice_document')) {
						foreach ($request->file('invoice_document') as $image) { 
							$filename = time() . '_' . $image->getClientOriginalName(); 
							$path = $image->storeAs('public/orders/' . $order->id, $filename);  
							$invoiceDocPaths[] = $filename;
						}
					} 
					$order->update(['invoice_document' => $invoiceDocPaths]);

					// Extract and explode values
					$productDiscriptions = $row[2] ? explode(',', $row[2]) : null;
					$productAmount = $row[3] ? explode(',', $row[3]) : null;
					$boxCounts = $row[4] ? explode(',', $row[4]) : null;
					$boxLength = $row[5] ? explode(',', $row[5]) : null;
					$boxWidth = $row[6] ? explode(',', $row[6]) : null;
					$boxHeight = $row[7] ? explode(',', $row[7]) : null;
					$boxWeight = $row[8] ? explode(',', $row[8]) : null;

					$orderItems = [];
					$totalAmount = 0; // Initialize total amount

					if ($productDiscriptions) {
						foreach ($productDiscriptions as $key => $productDiscription) {
							$amount = $productAmount[$key] ?? 0;
							$totalAmount += $amount; // Calculate total amount

							$orderItems[] = [
								'order_id' => $order->id, 
								'product_discription' => $productDiscription,
								'amount' => $amount, 
								'ewaybillno' => null, 
								'quantity' => $boxCounts[$key] ?? 0, 
								'created_at' => now(),
								'updated_at' => now(),
								'dimensions' => json_encode([
									'weight' => $boxWeight[$key] ?? 0,
									'length' => $boxLength[$key] ?? 0,
									'width' => $boxWidth[$key] ?? 0,
									'height' => $boxHeight[$key] ?? 0,
								]),
							];
						} 
					}

					// Bulk Insert Order Items
					OrderItem::insert($orderItems); 

					// Update Order Total Amount
					$order->update(['total_amount' => $totalAmount]);

					// Insert Order Status
					OrderStatus::insert([
						'order_id' => $order->id, 
						'order_status' => 'New', 
						'created_at' => now(), 
						'updated_at' => now()
					]);

					Helper::orderActivity($order->id, 'Order created.');
				}

				DB::commit(); // Commit Transaction
				return response()->json(['status' => 'success', 'msg' => 'The order has been successfully added.']);
			} 
			catch (\Exception $e) {
				DB::rollback(); // Rollback in case of failure
				return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
			}
		}
 
 
		public function orderEdit($id)
		{ 
			$user = Auth::user();
			$order = Order::with( 'orderItems')->find($id);  
			return view('order.edit', compact('order', 'user'));
		}
		
		public function orderUpdate(Request $request, $id)
		{  
			$user_id = Auth::id();  
			$data = $request->except('_token', 'product_name', 'product_category', 'sku_number', 'hsn_number', 'amount', 'quantity', 'weight', 'length', 'width', 'height', 'order_image', 'invoice_document'); 
			$data['user_id'] = $user_id;
			$data['is_fragile_item'] = $request->is_fragile_item ?? 0;
			$data['weight'] = $request->total_weight; 
			$data['total_amount'] = $request->invoice_amount;
			$data['status_courier'] = 'New';
			$data['status'] = 1;
			$data['created_at'] = now();
			$data['updated_at'] = now();
			
			DB::beginTransaction(); // Begin Transaction
			
			try {  
				// Insert Order and get ID
				$order = Order::findOrFail($id);
				
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
				
				$totalAmount = 0;
				$orderItems = [];
				
				if (!empty($request->product_category)) 
				{
					OrderItem::where('order_id', $id)->whereNotIn('id', $request->id ?? [])->delete();
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
								'weight' => $request->weight[$key] ?? 0,
								'length' => $request->length[$key] ?? 0,
								'width' => $request->width[$key] ?? 0,
								'height' => $request->height[$key] ?? 0,
							]),
						];
						
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
					
					// Bulk insert for new items if any
					if (!empty($orderItems)) {
						OrderItem::insert($orderItems);
					} 
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
		
		public function orderClone($id)
		{
		    $user = Auth::user();
			$order = Order::with( 'orderItems')->find($id);  
			return view('order.clone', compact('order', 'user'));
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
		
		public function orderLableDownload($orderId)
		{ 
			error_reporting(0);
			$order = Order::with(['shippingCompany:id,logo', 'customer:id,first_name,last_name,mobile', 'customerAddress', 'warehouse', 'orderItems'])->find($orderId);
			 
			if ($order && $order->shippingCompany && $order->shippingCompany->id == 2) 
			{  
				$docWaybill = $order->api_response['response']['data']['doc_waybill'] ?? '';  
				 
				// Sample data
				$awbNumbers = $order->api_response['response']['data']['waybills'] ?? [];  
				$items = $order->orderItems->map(function ($item) {
					return [
						'id' => $item->id,
						'product_discription' => $item->product_discription, // Assuming correct column name
						'quantity' => $item->quantity
					];
				})->toArray();
 
				$wayBills = [];
				$awbIndex = 0; // Track AWB assignment

				foreach ($items as $item) {
					for ($i = 0; $i < $item['quantity']; $i++) {
						$wayBills[] = [
							'id' => $item['id'],
							'product_discription' => $item['product_discription'],
							'awb_number' => $awbNumbers[$awbIndex] ?? 'No AWB' // Assign AWB dynamically
						];
						$awbIndex++; // Move to the next AWB
					}
				} 
				return view('order.label.delhivery_b2b', compact('order', 'wayBills', 'docWaybill'));
				$pdf = PDF::loadView('order.label.delhivery_b2b', compact('order', 'wayBills', 'docWaybill')); 
				return $pdf->download('order_label_' . $orderId . '.pdf');
			}
			
			if ($order && $order->shippingCompany && $order->shippingCompany->id == 3) 
			{    
				$product_name = ["Order Id-{$order->order_prefix}"]; 
				foreach ($order->orderItems ?? [] as $orderItems) {
					$product_name[] = "Product discription - {$orderItems->product_discription}";
				} 
				$productNamesString = implode(', ', array_unique($product_name));
			
				return view('order.label.delhivery_b2c', compact('order', 'productNamesString')); 
			}
			
			return back()->with('error', 'Something went wrong');
		}
		
        public function ecomtrackorder($id)
        {
            $order =  DB::table('orders')->where('id',$id)->first();
            $shippingcomp = ShippingCompany::whereId($order->shipping_company_id)->whereStatus(1)->first();
            
			
            $curl = curl_init();
			
            curl_setopt_array($curl, [
			CURLOPT_URL => 'https://plapi.ecomexpress.in/track_me/api/mawbd/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => [
			'username' => $shippingcomp->email,
			'password' => $shippingcomp->password,
			'awb' => $order->awb_number,
			],
            ]);
			
            $response = curl_exec($curl);
            curl_close($curl);
			
            // Convert XML to JSON
            function xmlToJson($xmlString) {
                $xml = simplexml_load_string($xmlString);
                $json = json_encode($xml);
                return $json;
			}
            
            // Convert the response from XML to JSON
            $jsonResponse = xmlToJson($response);
            $data = json_decode($jsonResponse, true);
			
            
            return view('tracking', ['data' => $data]);
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
		
    	public function invoice_generate($order_id)
    	{
    	    $order = DB::table('orders')->where('id',$order_id)->first();
            $existingInvoice = DB::table('invoices')->where('order_id', $order_id)->first();
            if (!$existingInvoice) 
            {
                $vouchers_no = DB::table('invoices')->orderBy('id', 'desc')->pluck('inv_code')->first();
    			if ($vouchers_no) {
    				$vouchers_no = explode('-', $vouchers_no);
    				
    			    
    				$v_number = $vouchers_no[3] + 1;
    				} else {
    				$v_number = 1;
				}
    			$v_no = '#INV-' . date('m-Y') . '-' . sprintf('%04d', $v_number);
                $invoiceData = [
				'inv_code'          => $v_no, // Assuming order ID is unique
				'user_id'           => $order->user_id,
				'order_id'          => $order->id,
				'vendor_id'         => $order->vendor_id,
				'vendor_address_id' => $order->vendor_address_id,
				'total_amount'      => $order->total_amount,
				'date'              => date('Y-m-d H:i:s'), // Assuming the current timestamp
				'status'            => '1', // Assuming '1' means the invoice is active
				'created_at'        => now(),
				'updated_at'        => now(),
                ];
				
                DB::table('invoices')->insert($invoiceData);
                
                DB::table('orders')->where('id', $order->id)->update(['is_inovice' => 1]);
                return back()->with('success','The order has been successfully generate invoice.');
			}
            return back()->with('error','This order already have invoice.');
		}
		
	    public function multi_invoice_generate(Request $request)
    	{
			// echo "<pre>"; print_r($request->order_Ids); echo "</pre>"; die;
			$validator = Validator::make($request->all(), [
			'order_Ids' => 'required|array',
			'order_Ids.*' => 'required|exists:orders,id',
            ]);
			
            // If validation fails, return error response
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
			}
			
            $o_id =	 $request->order_Ids ;
            foreach ($o_id as $order_id)
            {
        	    $order = DB::table('orders')->where('id',$order_id)->first();
				if (!$order) {
					// If order does not exist, return error response
					return response()->json(['error' => 'Order with ID ' . $orderId . ' not found.'], 404);
				}
				
                $existingInvoice = DB::table('invoices')->where('order_id', $order_id)->first();
                if (!$existingInvoice) 
                {
                    $vouchers_no = DB::table('invoices')->orderBy('id', 'desc')->pluck('inv_code')->first();
        			if ($vouchers_no) {
        				$vouchers_no = explode('-', $vouchers_no);
        				
        			    
        				$v_number = $vouchers_no[3] + 1;
        				} else {
        				$v_number = 1;
					}
        			$v_no = '#INV-' . date('m-Y') . '-' . sprintf('%04d', $v_number);
                    $invoiceData = [
					'inv_code'          => $v_no, // Assuming order ID is unique
					'user_id'           => $order->user_id,
					'order_id'          => $order->id,
					'vendor_id'         => $order->vendor_id,
					'vendor_address_id' => $order->vendor_address_id,
					'total_amount'      => $order->total_amount,
					'date'              => date('Y-m-d H:i:s'), // Assuming the current timestamp
					'status'            => '1', // Assuming '1' means the invoice is active
					'created_at'        => now(),
					'updated_at'        => now(),
                    ];
					
                    DB::table('invoices')->insert($invoiceData);
                    
                    DB::table('orders')->where('id', $order->id)->update(['is_inovice' => '1']);
					
				}
			}
			return response()->json(['message' => 'Invoices generated successfully.'], 200); 
		}
    	 
    	public function invoice_view($order_id)
    	{
    	    $invoice_data = DB::table('invoices')
			->where('invoices.order_id', $order_id)
			->join('orders', 'orders.id', '=', 'invoices.order_id')
			->join('users', 'users.id', '=', 'invoices.user_id')
			->leftjoin('company_details', 'company_details.user_id', '=', 'invoices.user_id')
			->join('user_kycs', 'user_kycs.user_id', '=', 'invoices.user_id')
			->select(
			'invoices.*',
			'company_details.company_name as user_company_name',
			'users.name AS user_name',
			'users.state as billing_state',
			DB::raw("CONCAT(company_details.address, ' ', company_details.city, ' ', company_details.state, '-' ,company_details.zipcode ) AS company_address"),
			DB::raw("CONCAT('+91-',users.mobile) AS user_mobile"),
			'users.email as user_email',
			'user_kycs.pancard as user_pancard',
			'user_kycs.gst as user_gst',
			DB::raw("CONCAT('(AWB No : ',orders.awb_number,' , Order Date: ',orders.order_date, ')') AS order_data"),
			'orders.shipping_charge as order_total_amount',
			'orders.tax as order_total_tax',
			DB::raw('(orders.shipping_charge - orders.tax) as order_total_amount_without_tax')
			)
			->first(); 
    	    return view('order.invoice',compact('invoice_data'));
		}
		
		public function multi_invoice_download(Request $request)
		{
		    $validator = Validator::make($request->all(), [
			'order_Ids' => 'required|array',
			'order_Ids.*' => 'required|exists:orders,id',
            ]);
			
            // If validation fails, return error response
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
			}
            $invoice_datas = [];
            $o_id =	 $request->order_Ids ;
            foreach ($o_id as $order_id)
            {
				$invoiceDataArray = DB::table('invoices')
				->where('invoices.order_id', $order_id)
				->join('orders', 'orders.id', '=', 'invoices.order_id')
				->join('users', 'users.id', '=', 'invoices.user_id')
				->leftjoin('company_details', 'company_details.user_id', '=', 'invoices.user_id')
				->join('user_kycs', 'user_kycs.user_id', '=', 'invoices.user_id')
				->select(
				'invoices.*',
				'company_details.company_name as user_company_name',
				'users.name AS user_name',
				'users.state as billing_state',
				DB::raw("CONCAT(company_details.address, ' ', company_details.city, ' ', company_details.state, '-' ,company_details.zipcode ) AS company_address"),
				DB::raw("CONCAT('+91-',users.mobile) AS user_mobile"),
				'users.email as user_email',
				'user_kycs.pancard as user_pancard',
				'user_kycs.gst as user_gst',
				DB::raw("CONCAT('(AWB No : ',orders.awb_number,' , Order Date: ',orders.order_date, ')') AS order_data"),
				'orders.shipping_charge as order_total_amount',
				'orders.tax as order_total_tax',
				DB::raw('(orders.shipping_charge - orders.tax) as order_total_amount_without_tax')
				)
				->first();
                $invoice_datas[] = $invoiceDataArray;
			} 
            $view = view('order.invoice', compact('invoice_datas'))->render(); 
		}
		
        public function alllabeldownload(Request $request)
        {
            $orderIds = $request->input('order_Ids');
            $pdfPaths = [];
			
            foreach ($orderIds as $orderId) 
            {
                $order = Order::find($orderId);
                if (!$order) {
                    continue; // Skip if order not found
				}
                
                $shipping = ShippingCompany::find($order->shipping_company_id);
                $customer = Customer::find($order->customer_id);
                $customerAddr = CustomerAddress::find($order->customer_address_id);
                $vendor = Vendor::find($order->vendor_id);
                $vendorAddress = VendorAddress::find($order->vendor_address_id);
                
                $view = view('order.print-label', compact('shipping', 'order', 'customer', 'customerAddr', 'vendor', 'vendorAddress'))->render();
                
                // Create a unique filename for each PDF
                $filename = "order_{$orderId}_label.pdf";
                $pdfPath = storage_path("app/public/{$filename}");
                
                // Generate PDF and save it
                $pdf = PDF::loadHTML($view);
                $pdf->save($pdfPath);
                
                $pdfPaths[] = $pdfPath;
			}
            
            if (empty($pdfPaths)) {
                return response()->json(['error' => 'No valid orders found'], 400);
			}
            
            // Create a ZIP file containing all PDFs
            $zipPath = storage_path('app/public/labels.zip');
            $zip = new \ZipArchive();
            $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            
            foreach ($pdfPaths as $pdfPath) {
                $zip->addFile($pdfPath, basename($pdfPath));
			}
            
            $zip->close();
            
            // Remove individual PDFs after adding them to ZIP
            foreach ($pdfPaths as $pdfPath) {
                unlink($pdfPath);
			}
            
            return response()->json(['url' => url('storage/labels.zip')]);
		} 
		
        public function showLabel($order_id)
        {
            $order = Order::findOrFail($order_id);
            $shipping = ShippingCompany::find($order->shipping_company_id);
            $customer = Customer::find($order->customer_id);
            $customerAddr = CustomerAddress::find($order->customer_address_id);
            $vendor = Vendor::find($order->vendor_id);
            $vendorAddress = VendorAddress::find($order->vendor_address_id);
			
            // Pass the data to the view
            return view('order.print-label', [
			'shipping' => $shipping,
			'order' => $order,
			'customer' => $customer,
			'customer_address' => $customerAddr,
			'vendor' => $vendor,
			'vendor_address' => $vendorAddress,
            ]);
		}
		
		public function orderLableGenerate($id)
		{
			$order = Order::whereId($id)->first();
			
			if($order->shipping_company_id == 3)
			{
				$shipping = ShippingCompany::whereId($order->shipping_company_id)->first();
				$customer = Customer::where('id',$order->customer_id)->first();
				$customerAddr = CustomerAddress::where('id',$order->customer_address_id)->first();
				
				$vendor = Vendor::where('id',$order->vendor_id)->first();
				$vendorAddress = VendorAddress::where('id',$order->vendor_address_id)->first();
				return view('order.print-label', compact('shipping','order','customer','customerAddr','vendor','vendorAddress'));
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
				->firstOrFail();
				
				if(!$shippingCompany)
				{
					return back()->with('error', 'Something went wrong.');
				}
				
				$orderCancelledData = []; 
				if ($shippingCompany->id == 2) {  
					$response = $this->delhiveryService->cancelShipmentByLrNo($order->lr_no, $shippingCompany);
					
					if (!($response['success'] ?? false)) {
						$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response']['error']['message'] ?? 'An error occurred.');
						return back()->with('error', $errorMsg);
					}
					
					if ((isset($response['response']['success']) && !$response['response']['success']))
					{ 
						return back()->with('error', $response['response']['error']['message'] ?? 'An error occurred.');
					} 
					
					$orderCancelledData = [
						'status_courier' => 'cancelled',
						'order_cancel_date' => now(),
						'reason_cancel' => $response['response']['data'] ?? '',
					]; 
				}
				
				if ($shippingCompany->id == 3) {  
					$response = $this->delhiveryB2CService->cancelShipmentByAwbNumber($order->awb_number, $shippingCompany);
					 
					if (!($response['success'] ?? false)) {
						$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response']['error']['message'] ?? 'An error occurred.');
						return back()->with('error', $errorMsg);
					}
					
					if ((isset($response['response']['success']) && !$response['response']['success']))
					{ 
						return back()->with('error', $response['response']['error']['message'] ?? 'An error occurred.');
					} 
					
					$orderCancelledData = [
						'status_courier' => 'cancelled',
						'order_cancel_date' => now(),
						'reason_cancel' => "For AWB number {$order->awb_number}, Shipment has been cancelled.",
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
				if($shippingCompany->id == 2 && $order->lr_no)
				{ 
					$trackingResponse = $this->delhiveryService->trackOrderByLrNo($order->lr_no, $shippingCompany);  
					
					if (!($trackingResponse['success'] ?? false)) {
						$errorMsg = $trackingResponse['response']['errors'][0]['message'] ?? ($trackingResponse['response']['error']['message'] ?? 'An error occurred.');
						return back()->with('error', $errorMsg); 
					}
					
					if ((isset($trackingResponse['response']['success']) && !$trackingResponse['response']['success']))
					{ 
						return back()->with('error', $trackingResponse['response']['error']['message'] ?? 'An error occurred.'); 
					}
					
					$responseData = $trackingResponse['response']['data']['wbns'] ?? [];
					$trackingHistories = array_reverse($responseData); 
				} 
				
				if($shippingCompany->id == 3)
				{ 
					$trackingResponse = $this->delhiveryB2CService->trackOrderByAwbNumber($order->awb_number, $shippingCompany);  
					
					if (!($trackingResponse['success'] ?? false)) {
						$errorMsg = $trackingResponse['response']['errors'][0]['message'] ?? ($trackingResponse['response']['error']['message'] ?? 'An error occurred.');
						return back()->with('error', $errorMsg); 
					}
					
					if ((isset($trackingResponse['response']['success']) && !$trackingResponse['response']['success']))
					{ 
						return back()->with('error', $trackingResponse['response']['Error'] ?? 'An error occurred.'); 
					}
					
					$responseData = $trackingResponse['response']['ShipmentData'][0]['Shipment']['Scans'] ?? [];
					$trackingHistories = array_reverse($responseData); 
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
				
				if(($shippingCompany && $shippingCompany->id == 2) && $order->lr_no)
				{ 
					$trackingResponse = $this->delhiveryService->trackOrderByLrNo($order->lr_no, $shippingCompany);  
					
					if ((isset($trackingResponse['response']['success']) && $trackingResponse['response']['success']))
					{ 
						$responseData = $trackingResponse['response']['data']['wbns'] ?? [];
						$trackingHistories = array_reverse($responseData); 
					} 
				}
				if(($shippingCompany && $shippingCompany->id == 3))
				{ 
					$trackingResponse = $this->delhiveryB2CService->trackOrderByAwbNumber($order->awb_number, $shippingCompany);  
				 
					if ((isset($trackingResponse['response']['ShipmentData']) && !empty($trackingResponse['response']['ShipmentData'])))
					{ 
						$responseData = $trackingResponse['response']['ShipmentData'][0]['Shipment']['Scans'] ?? [];
						$trackingHistories = array_reverse($responseData); 
					} 
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
			
			$totalWeightInKg = $order->orderItems->sum(fn($item) => $item->dimensions['weight'] ?? 0);
			
			$shippingCompanies = ShippingCompany::whereStatus(1)->get();
			$couriers = [];
			
			foreach ($shippingCompanies as $shippingCompany)
			{
				if ($order->weight_order == 2 && $shippingCompany->id == 2)
				{ 
					$pincodeServiceData = $this->delhiveryService->pincodeService($order->customerAddress->zip_code ?? '', $shippingCompany);
					
					if (!($pincodeServiceData['success'] ?? false) || 
					(isset($pincodeServiceData['response']['success']) && !$pincodeServiceData['response']['success'])) {
						continue;
					}
					
					$response = $this->delhiveryService->freightEstimate($order, $shippingCompany);
					
					if (!($response['success'] ?? false) || 
					(isset($response['response']['success']) && !$response['response']['success'])) {
						continue;
					}
					
					$responseData = $response['response']['data'] ?? [];
					if (!$responseData) {
						continue;
					}
					
					$totalCharges = $responseData['total'];
					$percentageAmount = ($role === "user" && $charge > 0)
					? ($charge_type == 1 ? $charge : ($totalCharges * $charge) / 100) 
					: 0;
					$totalCharges += $percentageAmount;
					
					$tax = ($totalCharges * $shippingCompany->tax) / 100;
					$roundedTotalCharges = round($totalCharges + $tax);
					$roundOffAmount = $roundedTotalCharges - ($totalCharges + $tax);
					
					$couriers[] = [
						'direct_rate' => $responseData['price_breakup']['base_freight_charge'] ?? 0,
						'order_id' => $orderId,
						'tax' => $tax,
						'round_off' => $roundOffAmount,
						'shipping_company_id' => $shippingCompany->id,
						'shipping_company_name' => $shippingCompany->name,
						'shipping_company_logo' => asset('storage/shipping-logo/' . $shippingCompany->logo),
						'courier_id' => '',
						'courier_name' => "{$shippingCompany->name} {$order->shipping_mode}",
						'freight_charges' => $responseData['price_breakup']['base_freight_charge'] ?? 0,
						'cod_charges' => $responseData['price_breakup']['meta_charges']['cod'] ?? 0,
						'total_charges' => $roundedTotalCharges,
						'rto_charge' => $responseData['price_breakup']['insurance_rov'] ?? 0,
						'min_weight' => $responseData['min_charged_wt'] ?? 0,
						'chargeable_weight' => ($responseData['charged_wt'] ?? 0),
						'applicable_weight' => $totalWeightInKg ?? 0,
						'percentage_amount' => $percentageAmount,
						'responseData' => $responseData
					];
				}
				else if ($order->weight_order == 1 && $shippingCompany->id == 3) 
				{ 
					$pincodeServiceData = $this->delhiveryB2CService->pincodeService($order->customerAddress->zip_code ?? '', $shippingCompany);
					 
					if (!($pincodeServiceData['success'] ?? false) || 
					(isset($pincodeServiceData['response']['success']) && !$pincodeServiceData['response']['success'])) {
						continue;
					}
					
					$response = $this->delhiveryB2CService->freightEstimate($order, $shippingCompany);
					 
					if (!($response['success'] ?? false) || 
					(isset($response['response']['success']) && !$response['response']['success'])) {
						continue;
					}
					
					$responseData = $response['response'] ?? [];
					if (!$responseData) {
						continue;
					}
					
					foreach($responseData as $responseValue)
					{ 
						$taxData = $responseValue['tax_data'] ? array_sum($responseValue['tax_data']) : 0;
						$totalAmount = $responseValue['charge_DL'] ?? 0;
						$chargeDph = $responseValue['charge_DPH'] ?? 0;
						$chargeCod = $responseValue['charge_COD'] ?? 0;
						$chargeRto = $responseValue['charge_RTO'] ?? 0;
						
						$totalCharges = $taxData + $totalAmount + $chargeDph + $chargeCod;
						
						$percentageAmount = ($role === "user" && $charge > 0) 
						? ($charge_type == 1 ? $charge : ($totalCharges * $charge) / 100) 
						: 0;
						$totalCharges += $percentageAmount;
						
						$tax = ($totalCharges * $shippingCompany->tax) / 100;
						$roundedTotalCharges = round($totalCharges + $tax);
						$roundOffAmount = $roundedTotalCharges - ($totalCharges + $tax);
						
						$couriers[] = [
							'direct_rate' => $totalAmount ?? 0,
							'order_id' => $orderId,
							'tax' => $tax,
							'round_off' => $roundOffAmount,
							'shipping_company_id' => $shippingCompany->id,
							'shipping_company_name' => $shippingCompany->name,
							'shipping_company_logo' => asset('storage/shipping-logo/' . $shippingCompany->logo),
							'courier_id' => '',
							'courier_name' => "{$shippingCompany->name} {$order->shipping_mode}",
							'freight_charges' => ($totalAmount + $chargeDph),
							'cod_charges' => $chargeCod,
							'total_charges' => $roundedTotalCharges,
							'rto_charge' => $chargeRto,
							'min_weight' => ($responseValue['charged_weight'] / 1000),
							'chargeable_weight' => ($responseValue['charged_weight'] / 1000),
							'applicable_weight' => $totalWeightInKg ?? 0,
							'percentage_amount' => $percentageAmount,
							'responseData' => $responseValue
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
		
		public function orderShipNow(Request $request)
		{
			DB::beginTransaction(); // Start Transaction
			try {
				$requestData = collect(json_decode($request->data, true) ?? []);
				$shippingCompany = ShippingCompany::find($requestData['shipping_company_id']);
				
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
					return response()->json(['status' => 'error', 'msg' => 'Something went wrong.']);
				}
				
				if ($shippingCompany->id == 2) 
				{    
					if($courierWarehouse->delhivery_status == 0)
					{
						$data = $this->delhiveryService->createWarehouse($courierWarehouse, $shippingCompany); 
						if (!empty($data['success']) && !empty($data['response']['success'])) {   
							$courierWarehouse->update(['shipping_id' => $shippingCompany->id, 'delhivery_status' => 1, 'api_response' => $data['response']]); 
						}
					}
					$response = $this->delhiveryService->manifest($order, $requestData, $shippingCompany);
					
					if (!($response['success'] ?? false)) {
						$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response']['error']['message'] ?? 'An error occurred.');
						return response()->json(['status' => 'error', 'msg' => $errorMsg]);
					}
					
					if ((isset($response['response']['success']) && !$response['response']['success']))
					{
						return response()->json(['status' => 'error', 'msg' => $response['response']['error']['message'] ?? 'An error occurred.']);
					}
					
					$jobId = $response['response']['job_id'] ?? '';
					
					$menifestStatusResponse = $this->delhiveryService->manifestStatus($jobId, $shippingCompany);  
					
					if (!($menifestStatusResponse['success'] ?? false)) {
						$errorMsg = $menifestStatusResponse['response']['errors'][0]['message'] ?? ($menifestStatusResponse['response']['error']['message'] ?? 'An error occurred.');
						return response()->json(['status' => 'error', 'msg' => $errorMsg]);
					}
					
					if ((isset($menifestStatusResponse['response']['success']) && !$menifestStatusResponse['response']['success']))
					{
						return response()->json(['status' => 'error', 'msg' => $menifestStatusResponse['response']['error']['message'] ?? 'An error occurred.']);
					}
					
					if($menifestStatusResponse['response']['data']['status'] == "DataError")
					{
						return response()->json(['status' => 'error', 'msg' => $menifestStatusResponse['response']['data']['reason'] ?? 'An error occurred.']);
					}
					
					// Extract response data
					$shipment_id = $jobId;
					$lr_no = $menifestStatusResponse['response']['data']['lrnum'] ?? null; 
					$awb_number = $menifestStatusResponse['response']['data']['master_waybill'] ?? null; 
					$courier_id = $menifestStatusResponse['response']['request_id'] ?? null; 
					$statusCourier = 'manifested';
					$apiResponse = $menifestStatusResponse;   
				}
				
				if ($shippingCompany->id == 3) 
				{    
					if($courierWarehouse->delhivery_status1 == 0)
					{
						$data = $this->delhiveryB2CService->createWarehouse($courierWarehouse, $shippingCompany);
						 
						if (!empty($data['success']) && !empty($data['response']['success'])) {   
							$courierWarehouse->update(['shipping_id' => $shippingCompany->id, 'delhivery_status1' => 1, 'api_response1' => $data['response']]); 
						}
					}
					
					$response = $this->delhiveryB2CService->manifest($order, $requestData, $shippingCompany);
					 
					if (!($response['success'] ?? false)) {
						$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response']['error']['message'] ?? 'An error occurred.');
						return response()->json(['status' => 'error', 'msg' => $errorMsg]);
					}
					
					if ((isset($response['response']['success']) && !$response['response']['success']))
					{
						$errmsg = $response['response']['rmk'];
						if(count($response['response']['packages']) > 0)
						{
							$errmsg = $response['response']['packages'][0]['remarks'][0];
						} 
						return response()->json(['status' => 'error', 'msg' => $errmsg ?? 'An error occurred.']);
					}
					 
					// Extract response data
					$shipment_id = $response['response']['upload_wbn'];
					$lr_no = $order->order_prefix; 
					$awb_number = $response['response']['packages'][0]['waybill'] ?? null; 
					$courier_id = $response['response']['packages'][0]['refnum'] ?? null; 
					$statusCourier = 'manifested';
					$apiResponse = $response['response'] ?? null;   
				}
				
				// Prepare update data
				$updateData = [
					'status_courier' => $statusCourier,
					'tax' => $requestData['tax'] ?? 0,
					'tax_percentage' => $shippingCompany->tax,
					'shipping_charge' => $requestData['total_charges'] ?? 0,
					'shipping_company_id' => $shippingCompany->id,
					'shipment_id' => $shipment_id,
					'awb_number' => $awb_number,
					'courier_name' => $requestData['courier_name'],
					'courier_id' => $courier_id,
					'label' => null,
					'lr_no' => $lr_no,
					'api_response' => $apiResponse,
					'applicable_weight' => $requestData['chargeable_weight'],
					'cod_charges' => $requestData['cod_charges'],
					'rto_charge' => $requestData['rto_charge'],
					'percentage_amount' => $requestData['percentage_amount']
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
				Your package has been assigned to <b>{$shippingCompany->name}</b>."; 
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
				
				} catch (\Exception $e) {
				DB::rollBack(); // Rollback Transaction on Error
				Log::error("Order Shipping Error: " . $e->getMessage());
				return response()->json(['status' => 'error', 'msg' => 'Something went wrong!'. $e->getMessage()]);
			}
		}
		
		public function getGenerateAWB($shipping_id)
		{
			$number = 'STAR-'.rand(1111111,9999999);
			if(Order::where('shipping_company_id',$shipping_id)->where('awb_number',$number)->exists())
			{
				$this->getGenerateAWB($shipping_id);	
			}
			return $number;
		}
		
		public function ecomlabeldownload($awb, $shippingcomp)
		{
    		$url = 'https://shipment.ecomexpress.in/services/expp/shipping_label';
			
    		
    		// Making the HTTP POST request
    		$response = Http::attach('username', $shippingcomp->email)
			->attach('password', $shippingcomp->password)
			->attach('awb', $awb)
			->post($url);
            
            if ($response->successful()) 
            {
    			
    			
    			$pdfContent = $response->body();
				
                // Define the path where the PDF will be stored
                $pdfName = 'shipping_label_' . $awb . '.pdf';
                $filePath = storage_path('app/public/' . $pdfName);
                
                // Save the PDF content to the file
                file_put_contents($filePath, $pdfContent);
				
                // Generate a URL for the stored PDF
                $responseData = asset('storage/' . $pdfName);
				//return $pdfUrl;
				
				} else {
    		    $responseData = [];
			}
    		return $responseData;
		}
		
		public function ecomRequestData($request, $shippingcomp)
    	{
			
    		$order = Order::whereId($request['order_id'])->first();
			
    		$url = $shippingcomp->url . "apiv2/fetch_awb/";
			
    		if($order->order_type == "cod")
    		{
    			$type = "COD";
			}else
    		{
    			$type = "PPD";
			}
    		
    		// Making the HTTP POST request
    		$response = Http::attach('username', $shippingcomp->email)
			->attach('password', $shippingcomp->password)
			->attach('count', 1)
			->attach('type', $type)
			->post($url);
            
            if ($response->successful()) {
    			$responseData = $response->json();
				
    			$awb_no =  $responseData['awb'][0];
			} 
			// 		else {
			// 		    $responseDatajson = 'Something went wrong to fetch awb number';
			// 		    return $responseDatajson;
			// 		}
    		
            $orderitems = DB::table('order_items')
			->select('product_name as name', 'quantity', 'amount as price', 'product_discription as description')
			->where('order_id', $order->id)
			->get()
			->toArray();
			
            $vendoraddr = VendorAddress::whereId($order->vendor_address_id)->first();
            $customeraddr = CustomerAddress::whereId($order->customer_address_id)->first();
            $vendor = Vendor::whereId($order->vendor_id)->first();
            $customer = Customer::whereId($order->customer_id)->first();
			
            $order_amount = $order->total_amount;
            $collectable_amount = 0;
            if ($order->order_type == "cod") {
                $order_amount = $order->total_amount;
                $collectable_amount = $order->total_amount;
			}
            
            $weight = $order->weight;
            $volumatric_weight = $order->length * $order->width * $order->height / 5000;
            if ($order->weight > $volumatric_weight) {
                $applicable_weight = $weight;
				} else {
                $applicable_weight = $volumatric_weight;
			}
			
            // Preparing JSON Input data
            $json_input = [
			[
			'AWB_NUMBER' => $awb_no, // Example value, replace with dynamic data if needed
			'ORDER_NUMBER' => $order->order_prefix,
			'ITEM_DESCRIPTION' => $orderitems[0]->description, // Example value, replace with dynamic data if needed
			'PIECES' => $orderitems[0]->quantity,
			'DECLARED_VALUE' => $orderitems[0]->price,
			'ACTUAL_WEIGHT' => $applicable_weight,
			'VOLUMETRIC_WEIGHT' => $volumatric_weight,
			'LENGTH' => $order->length,
			'BREADTH' => $order->width,
			'HEIGHT' => $order->height,
			'DG_SHIPMENT' => false,
			'ADDITIONAL_INFORMATION' => [
			'essentialProduct' => 'N',
			'OTP_REQUIRED_FOR_DELIVERY' => 'N',
			'DELIVERY_TYPE' => 'EXPRESS',
			'SELLER_TIN' => '',
			'INVOICE_NUMBER' => $order->order_prefix,
			'INVOICE_DATE' => null,
			'ESUGAM_NUMBER' => '',
			'ITEM_CATEGORY' => '',
			'PACKING_TYPE' => '',
			'PICKUP_TYPE' => '',
			'RETURN_TYPE' => '',
			'CONSIGNEE_ADDRESS_TYPE' => '',
			'PICKUP_LOCATION_CODE' => '',
			'SELLER_GSTIN' => '',
			'GST_HSN' => '4309197354',
			'GST_ERN' => '',
			'GST_TAX_NAME' => 'HR IGST',
			'GST_TAX_BASE' => 10,
			'DISCOUNT' => 10,
			'GST_TAX_RATE_CGSTN' => 10,
			'GST_TAX_RATE_SGSTN' => 10,
			'GST_TAX_RATE_IGSTN' => 10,
			'GST_TAX_TOTAL' => 10,
			'GST_TAX_CGSTN' => 10,
			'GST_TAX_SGSTN' => 10,
			'GST_TAX_IGSTN' => 10
			],
			'PRODUCT' => $type, // Example value, replace with dynamic data if needed
			'COLLECTABLE_VALUE' => $collectable_amount,
			'CONSIGNEE' => $customer->first_name . ' ' . $customer->last_name,
			'CONSIGNEE_ADDRESS1' => $customeraddr->address,
			'CONSIGNEE_ADDRESS2' => $customeraddr->address2,
			'CONSIGNEE_ADDRESS3' => '',
			'DESTINATION_CITY' => $customeraddr->city,
			'PINCODE' => $customeraddr->zip_code,
			'STATE' => $customeraddr->state,
			'MOBILE' => $customer->mobile,
			'TELEPHONE' => $customer->mobile,
			'PICKUP_NAME' => $vendor->name,
			'PICKUP_ADDRESS_LINE1' => $vendoraddr->address,
			'PICKUP_ADDRESS_LINE2' => '',
			'PICKUP_PINCODE' => $vendoraddr->zip_code,
			'PICKUP_PHONE' => $vendor->phone,
			'PICKUP_MOBILE' => $vendor->mobile,
			'RETURN_NAME' => $vendor->name,
			'RETURN_ADDRESS_LINE1' => $vendoraddr->address,
			'RETURN_ADDRESS_LINE2' => '',
			'RETURN_PINCODE' => $vendoraddr->zip_code,
			'RETURN_PHONE' => $vendor->phone,
			'RETURN_MOBILE' => $vendor->mobile,
			]
            ];
            // echo "<pre>";
            // print_r($json_input);
            // echo "</pre>";
            // die;
            $url = $shippingcomp->url . "apiv2/manifest_awb/";
			
            // Making the HTTP POST request
            $response = Http::attach('username', $shippingcomp->email)
			->attach('password', $shippingcomp->password)
			->attach('json_input', json_encode($json_input))
			->post($url);
			
            if ($response->successful()) 
            {
                
                $responseData = $response->json();
                
				} else {
                $responseData =  [];
			}
            
            return $responseData;
		}
		
		public function marutiRequestData($request,$shippingcomp)
		{ 
			
			$order = Order::whereId($request['order_id'])->first();
			$orderitems_data = OrderItem::select('id','product_name as name','quantity','amount as price','product_discription as sku')->whereOrder_id($request['order_id'])->get()->toArray();
			
			$lineItems = array();
    		foreach ($orderitems_data as $key => $orderitems)
    		{
				$sku = "PA".$orderitems['id'].$orderitems['name'];
				$orderitems['price'] = number_format($orderitems['price'], 0, '.', '');
				
    			if($orderitems['quantity'] > 1)
    			{
    				$pr = $orderitems['quantity'] * $orderitems['price'];
				}else
    			{
    				$pr =  $orderitems['price'];
				}
				$lineItems = [
				'name' => $orderitems['name'],
				'price' => (int)$pr, // Convert to integer if needed
				'weight' => 120,
				'quantity' => $orderitems['quantity'],
				'sku' => $sku,
				'unitPrice' => (int)$orderitems['price'] // Convert to integer if needed
                ];
			}
			$vendoraddr = VendorAddress::whereId($order->vendor_address_id)->first();
			$customeraddr = CustomerAddress::whereId($order->customer_address_id)->first();
			$vendor = Vendor::whereId($order->vendor_id)->first();
			$customer = Customer::whereId($order->customer_id)->first();
			$order_amount = $order->total_amount;
			$collectable_amount = 0; 
			
			$weight = $order->weight * 1000;
			if($order->order_type == "cod")
			{
				$order_amount = $order->total_amount;
				$collectable_amount = $order->total_amount;
			}
			
			if($order->shipping_mode == 'Surface')
			{
			    $shipping_mode = 'SURFACE';
			}else
			{
			    $shipping_mode = 'AIR';
			}
			
			if($order->order_type == "cod")
			{
			    $paymentType = "COD";
			}else
			{
			    $paymentType = "ONLINE";
			}
			$amount_order = number_format($order_amount, 0, '.', '');
			
			$dataArray = [
			'orderId' => $order->order_prefix,
			'orderSubtype' => 'FORWARD',
			'orderCreatedAt' => date('Y-m-d H:i:s'),
			'currency' => 'INR',
			'amount' => (int)$amount_order,
			'weight' => $weight,
 			'lineItems' => [$lineItems],
			'paymentType' => $paymentType,
			'paymentStatus'=> 'PENDING',
			'subTotal' => (int)$amount_order,
			'remarks' => 'handle with care',
			'shippingAddress' => [
			'name' => $customer->first_name . ' ' . $customer->last_name,
			'email' => $customer->email,
			'phone' => $customer->mobile,
			'address1' => $customeraddr->address,
			'address_2' => '',
			'city' => $customeraddr->city,
			'state' => $customeraddr->state,
			'country' => $customeraddr->country,
			'zip' => $customeraddr->zip_code,
			
			],
			'billingAddress' => [
			'name' => $customer->first_name . ' ' . $customer->last_name,
			'email' => $customer->email,
			'phone' => $customer->mobile,
			'address1' => $customeraddr->address,
			'address_2' => '',
			'city' => $customeraddr->city,
			'state' => $customeraddr->state,
			'country' => $customeraddr->country,
			'zip' => $customeraddr->zip_code,
			
			],
			'pickupAddress' => [
			'name' => $vendor->first_name . ' ' . $vendor->last_name,
			'email' => $vendor->email,
			'phone' => $vendor->mobile,
			'address1' => $vendoraddr->address,
			'address_2' => '',
			'city' => $vendoraddr->city,
			'state' => $vendoraddr->state,
			'country' => $vendoraddr->country,
			'zip' => $vendoraddr->zip_code,
			
			],
			'returnAddress' => [
			'name' => $vendor->first_name . ' ' . $vendor->last_name,
			'email' => $vendor->email,
			'phone' => $vendor->mobile,
			'address1' => $vendoraddr->address,
			'address_2' => '',
			'city' => $vendoraddr->city,
			'state' => $vendoraddr->state,
			'country' => $vendoraddr->country,
			'zip' => $vendoraddr->zip_code,
			
			],
			'gst' => 0,
			'deliveryPromise' =>$shipping_mode,
			'discountUnit'=> 'RUPEES',
			'discount'=> 00,
			'length'=> $order->length,
			'height'=> $order->width,
			'width'=> $order->height,
			];
			
			$token = $shippingcomp->api_key;
			$url = $shippingcomp->url.'fulfillment/public/seller/order/ecomm/push-order';
			
			//  		echo "<pre>"; print_r($dataArray); echo "</pre>"; die;
			$response = Helper::callCurlApi($url,$dataArray,$token);
			
			return $response;
		}
		
		public function downloadLabelShipway($id,$shipping_company_id,$carrier_id)
		{
		    
			$order_details = Order::where('id',$id)->first();
			
			$shippingcomp = ShippingCompany::whereId($shipping_company_id['id'])->whereStatus(1)->first();
			
			//  $shippingcomp = ShippingCompany::whereId('id',5)->whereStatus(1)->first();
			$orderitems = OrderItem::select('product_name as name', 'quantity', 'amount as price', 'product_discription as sku')->whereOrder_id($order_details['id'])->get()->toArray();
			
			$vendoraddr = VendorAddress::whereId($order_details->vendor_address_id)->first();
			$customeraddr = CustomerAddress::whereId($order_details->customer_address_id)->first();
			$vendor = Vendor::whereId($order_details->vendor_id)->first();
			$customer = Customer::whereId($order_details->customer_id)->first();
			$curierware = CourierWarehouse::where('vendor_address_id', $order_details->vendor_address_id)->where('shipping_id', $shippingcomp->id)->first();
			
			$warehouse_id = $curierware->warehouse_name;
			$return_warehouse_id = $curierware->warehouse_name;
			$order_amount = $order_details->total_amount;
			$collectable_amount = 0;
			if ($order_details->order_type == "cod") {
				$order_amount = $order_details->total_amount;
				$collectable_amount = $order_details->total_amount;
			}
			
			$data = [];
			foreach($orderitems as $product_list)
			{
				
				$data['product'] = $product_list["name"];
				$data['price'] = $product_list["price"];
				$data['product_code'] = $product_list["sku"];
				$data['amount'] = $product_list["quantity"];
				$data['discount'] = 0;
			}
			
			if ($order_details->order_type == "cod") {
				$paymentType = "C";
				}elseif ($order_details->order_type == "prepaid") {
				$paymentType = "P";
			}
			$valueInGrams = $order_details->weight * 1000;
			$dataArray = [
			'order_id' 			=> $order_details->order_prefix,
			'carrier_id'       => '',
			'warehouse_id'      => $warehouse_id,
			'return_warehouse_id'      => $warehouse_id,
			'ewaybill' 			=> '',
			'products' 			=> [$data],
			'discount' 			=> 0,
			'shipping' 			=> '',
			'order_total' 			=> $order_amount,
			'gift_card_amt' 		=> 0,
			'taxes' 				=> '',
			'email'				=> $customer->email,
			'payment_type' 		=> $paymentType,
			'billing_address'		=> $customeraddr->address,
			'billing_address2' 		=> '',
			'billing_city' 		=> $customeraddr->city,
			'billing_state' 		=> $customeraddr->state,
			'billing_country' 		=> $customeraddr->country,
			'billing_firstname' 	=> $customer->first_name,
			'billing_lastname' 		=> $customer->last_name,
			'billing_phone' 		=> $customer->mobile,
			'billing_zipcode' 		=> $customeraddr->zip_code,
			'shipping_address' 		=> $customeraddr->address,
			'shipping_address2' 	=> '',
			'shipping_city' 		=> $customeraddr->city,
			'shipping_state' 		=> $customeraddr->state,
			'shipping_country' 		=> $customeraddr->country,
			'shipping_firstname' 	=> $customer->first_name,
			'shipping_lastname' 	=> $customer->last_name,
			'shipping_phone' 		=> $customer->mobile,
			'shipping_zipcode' 		=> $customeraddr->zip_code,
			"order_weight"			=> $valueInGrams,
			"box_length"			=> $order_details->length,
			"box_breadth"			=> $order_details->width,
			"box_height"			=> $order_details->height,
			"order_date"			=> date($order_details->created_at),
			];
			
			$url = $shippingcomp->url."api/v2orders";	
			$username = $shippingcomp->email;
			$password = $shippingcomp->password;
			//			echo "<pre>"; print_r($dataArray); echo "</pre>"; die;
			$response = Http::withBasicAuth($username, $password)->post($url, $dataArray);
			
			
			if ($response->successful()) 
    		{			
    			$responseData = $response->json();
				
			}
    		else
    		{
				
    			$responseData = [];
			}
    		return $responseData;
		}
		
	    public function shipwayRequestData($request,$shippingcomp)
	    {
			
			
			$order = Order::whereId($request['order_id'])->first();
			$orderitems = OrderItem::select('product_name as name', 'quantity', 'amount as price', 'product_discription as sku')->whereOrder_id($request['order_id'])->get()->toArray();
			
			$vendoraddr = VendorAddress::whereId($order->vendor_address_id)->first();
			$customeraddr = CustomerAddress::whereId($order->customer_address_id)->first();
			$vendor = Vendor::whereId($order->vendor_id)->first();
			$customer = Customer::whereId($order->customer_id)->first();
			$order_amount = $order->total_amount;
			$collectable_amount = 0;
			if ($order->order_type == "cod") {
				$order_amount = $order->total_amount;
				$collectable_amount = $order->total_amount;
			}
			
			$data = [];
			foreach($orderitems as $product_list)
			{
				
				// 			$data['product'] = $product_list["name"];
				// 			$data['price'] = $product_list["price"];
				// 			$data['product_code'] = $product_list["sku"];
				// 			$data['amount'] = $product_list["price"];
				// 			$data['discount'] = 0;
				$data = [
				'product' => $product_list["name"],
				'price' => $product_list["price"], // Convert to integer if needed
				'product_code' => $product_list["sku"],
				'amount' => $product_list["quantity"],
				'discount' => 0,
                ];
			}
			
			if ($order->order_type == "cod") {
				$paymentType = "C";
				}elseif ($order->order_type == "prepaid") {
				$paymentType = "P";
			}
			
			$valueInGrams =  $valueInGrams * 1000;
			
			$dataArray = [
			'order_id' 			=> $order->order_prefix,
			'ewaybill' 			=> '',
			'products' 			=> [$data],
			'discount' 			=> 0,
			'shipping' 			=> '',
			'order_total' 			=> $order_amount,
			'gift_card_amt' 		=> 0,
			'taxes' 				=> '',
			'email'				=> $customer->email,
			'payment_type' 		=> $paymentType,
			'billing_address'		=> $customeraddr->address,
			'billing_address2' 		=> '',
			'billing_city' 		=> $customeraddr->city,
			'billing_state' 		=> $customeraddr->state,
			'billing_country' 		=> $customeraddr->country,
			'billing_firstname' 	=> $customer->first_name,
			'billing_lastname' 		=> $customer->last_name,
			'billing_phone' 		=> $customer->mobile,
			'billing_zipcode' 		=> $customeraddr->zip_code,
			'shipping_address' 		=> $customeraddr->address,
			'shipping_address2' 	=> '',
			'shipping_city' 		=> $customeraddr->city,
			'shipping_state' 		=> $customeraddr->state,
			'shipping_country' 		=> $customeraddr->country,
			'shipping_firstname' 	=> $customer->first_name,
			'shipping_lastname' 	=> $customer->last_name,
			'shipping_phone' 		=> $customer->mobile,
			'shipping_zipcode' 		=> $customeraddr->zip_code,
			"order_weight"			=> $valueInGrams,
			"box_length"			=> $order->length,
			"box_breadth"			=> $order->width,
			"box_height"			=> $order->height,
			"order_date"			=> date($order->created_at),
			];
			
			$url = $shippingcomp->url."api/v2orders";	
			$username = $shippingcomp->email;
			$password = $shippingcomp->password;
			
			$response = Http::withBasicAuth($username, $password)->post($url, $dataArray);
			if ($response->successful()) 
			{			
				$responseData = $response->json();
			}
			else
			{
				$responseData = [];
			}
			return $responseData;
		}
		
		public function delhiveryRequestData($request)
		{
			
			$shippingcomp = ShippingCompany::whereId($request['shipping_company_id'])->whereStatus(1)->first();	
			
			$url = $shippingcomp->url;
			$token = $shippingcomp->api_key;
			
			$order = Order::whereId($request['order_id'])->first();
			$user = User::whereId($order->user_id)->first();
			$user_id = $user->id;
			$role = $user->role; 
			
			
			$orderitems = OrderItem::select('product_name as name','quantity as qty','amount as price','product_discription as sku')->where('order_id',$request['order_id'])->get()->toArray();
			
			$vendoraddr = VendorAddress::whereId($order->vendor_address_id)->first();
			$customeraddr = CustomerAddress::whereId($order->customer_address_id)->first();
			$vendor = Vendor::whereId($order->vendor_id)->first();
			$customer = Customer::whereId($order->customer_id)->first();
			$order_amount = $order->total_amount;
			
			// 		$cutomer_address = $customeraddr->address.' '.$customeraddr->city.' '.$customeraddr->state.' '.$customeraddr->zip_code.','.$customeraddr->country;
			$cutomer_address = preg_replace('/\s+/', ' ', $customeraddr->address . ' ' . $customeraddr->city . ' ' . $customeraddr->state . ' ' . $customeraddr->zip_code . ', ' . $customeraddr->country);
			
			
			// 			$pickup_address = $vendoraddr->address.' '.$vendoraddr->city.' '.$vendoraddr->state.' '.$vendoraddr->zip_code.','.$vendoraddr->country;
	        $pickup_address = preg_replace('/\s+/', ' ', $vendoraddr->address.' '.$vendoraddr->city.' '.$vendoraddr->state.' '.$vendoraddr->zip_code.','.$vendoraddr->country);
			
            $specialChars = ['&', '\\', '%', '#', ';', ',,', '"'];
			$pickup_address = str_replace($specialChars, '', $pickup_address);
			$cutomer_address = str_replace($specialChars, '', $cutomer_address);
			
			$pickup_address = preg_replace('/\s+/', ' ', $pickup_address);
            $customer_address = preg_replace('/\s+/', ' ', $customer_address);
			
			foreach($orderitems as  $product_list)
			{
				$product_name[] = "Order Id-".$order->order_prefix;
				$product_name[] = "Product - ". $product_list["name"];
			} 
			
			$productNamesString = implode(', ', array_unique($product_name));
			
			$data = 'format=json&data={
			"shipments": [
			{
			"add": "'.$cutomer_address.'",
			"address_type": "",
			"phone": "'.$customer->mobile.'",
			"payment_mode": "'.$order->order_type.'",
			"name": "'.$customer->first_name.' '.$customer->last_name.'",
			"pin": "'.$customeraddr->zip_code.'",
			"order": "'.$order->order_prefix.'",
			"consignee_gst_amount": "",
			"integrated_gst_amount": "",
			"ewbn": "",
			"consignee_gst_tin": "",
			"seller_gst_tin": "",
			"client_gst_tin": "",
			"hsn_code": "",
			"gst_cess_amount": "",
			"shipping_mode": "'.$order->shipping_mode.'",
			"client": "37b434-STAREXPRESS-do",
			"tax_value": "",
			"seller_tin": "",
			"seller_gst_amount": "",
			"seller_inv": "",
			"city": "",
			"commodity_value": "",
			"weight": "'.$request['chargeable_weight'].'",
			"return_state": "",
			"document_number": "",
			"od_distance": "",
			"sales_tax_form_ack_no": "",
			"document_type": "",
			"seller_cst": "",
			"seller_name": "",
			"fragile_shipment": "",
			"return_city": "",
			"return_phone": "",
			"qc": {
			"item": [
			{
			"images": "",
			"color": "",
			"reason": "",
			"descr": "",
			"ean": "",
			"imei": "",
			"brand": "",
			"pcat": "",
			"si": "",
			"item_quantity": ""
			}
			]
			},
			"shipment_height": "'.$order->height.'",
			"shipment_width": "'.$order->width.'",
			"shipment_length": "'.$order->length.'",
			"category_of_goods": "",
			"cod_amount": "'.$order_amount.'",
			"return_country": "",
			"document_date": "",
			"taxable_amount": "",
			"products_desc": "'.$productNamesString.'",
			"state": "",
			"dangerous_good": "",
			"waybill": "",
			"consignee_tin": "",
			"order_date": "'.date('Y-m-d').'",
			"return_add": "'.$pickup_address.'",
			"total_amount": "'.$order_amount.'",
			"seller_add": "'.$pickup_address.'",
			"country": "'.$vendoraddr->country.'",
			"return_pin": "",
			"extra_parameters": {
			"return_reason": ""
			},
			"return_name": "",
			"supply_sub_type": "",
			"plastic_packaging": "false",
			"quantity": ""
			}
			],
			"pickup_location": {
			"name": "'.$request['warehouse_name'].'",
			"city": "'.$vendoraddr->city.'",
			"pin": "'.$vendoraddr->zip_code.'",
			"country": "'.$vendoraddr->country.'",
			"phone": "'.$vendor->mobile.'",
			"add": "'.$pickup_address.'"
			}
			}';
			
			
			//	 echo "<pre>"; print_r($data); echo "</pre>"; die;
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => $url.'api/cmu/create.json',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>$data,
			CURLOPT_HTTPHEADER => array(
			'Authorization: Token '.$token.'',
			'Content-Type: application/json' 
			),
			));
			
			$response = curl_exec($curl); 
			
			return json_decode($response,true);
		}
		
		public function orderDownloadManifest(Request $request)
		{
			$awb_number = explode(',',$request->ids);
			
			$order = Order::whereAwb_number($awb_number[0])->first();
			
			$shippingcomp = ShippingCompany::whereId($order->shipping_company_id)->whereStatus(1)->first();
			if($shippingcomp->id == 1)
			{ 
				$token = Helper::xpressBeesToken($shippingcomp->id);
				
				$url = $shippingcomp->url.'api/shipments2/manifest/';
				
				$dataArray = [
				'awbs'=> $awb_number
				];
				
				$response = Helper::callCurlApi($url,$dataArray,$token); 
				
				if($response['status'] == false)
				{
					return back()->with('error',$response['message']);
				} 
				return response()->json(['status'=>'success','msg'=>'Manifest generated successfully.','url'=>$response['data']]); 
			}    
		}
		
		public function orderPickupAll(Request $request)
		{
			$date = $request->date;
			$time = $request->time;
			$order_id = explode(',',$request->order_id);
			$orders = Order::whereIn('id',$order_id)->get();
			$data = [];
			foreach($orders as $order)
			{
				$courierware = CourierWarehouse::where('vendor_address_id',$order->vendor_address_id)->where('shipping_id',$order->shipping_company_id)->first();
				$data[$order->shipping_company_id.'-'.$courierware->warehouse_name][] = [
				'order_id'=>$order->id
				];
			}
			$i = 0;
			foreach($data as $key => $row)
			{   
				$explode = explode('-',$key);
				if($explode[0] == 2)
				{  
					$countpackage = count($row);
					$shippingcomp = ShippingCompany::whereId($explode[0])->whereStatus(1)->first();
					
					$token = Helper::xpressBeesToken($shippingcomp->id); 
					$url = $shippingcomp->url.'fm/request/new/'; 
					
					$dataArray = [
					'pickup_time'=> $time,
					'pickup_date'=> $date,
					'pickup_location'=> $explode[1],
					'expected_package_count'=> $countpackage,
					];
					
					$response = Helper::postCurl($url,json_encode($dataArray),$token);
					
					if(isset($response['error']))
					{
						$i += count($row);
						$msg = $i.' order not pickup '.$response['error']['message'];
						return response()->json(['status'=>'error','msg'=>$msg]);
					}
					
					if(isset($response['pickup_id']))
					{
						for($i = 0; $i< $countpackage;$i++)
						{
							Order::where('id',$row[$i]['order_id'])->update(['pickup_location_name'=>$response['pickup_location_name'],'pickup_time'=>$response['pickup_time'],'pickup_id'=>$response['pickup_id'],'pickup_date'=>$response['pickup_date'],'expected_package_count'=>$response['expected_package_count']]);
						}
						
						return response()->json(['status'=>'success','msg'=>'The pickup request has been generated.']); 
					}
					else
					{  
						$msg = "Something went wrong.";
						if(isset($response['pickup_time']))
						{
							$msg = $response['pickup_time'];
						}
						if(isset($response['pickup_date']))
						{
							$msg = $response['pickup_date'];
						}
						if(isset($response['pickup_location']))
						{
							$msg = $response['pickup_location'];
						}
						if(isset($response['error']))
						{
							$msg = $response['error']['message'];
						}
						
						return response()->json(['status'=>'error','msg'=>$msg]);
					}
				} 
			} 
		}
		
		public function orderSchedulePickup(Request $request)
		{
			$order_id = $request->order_id;
			$shipping_id = $request->shipping_id;
			$date = $request->date;
			$time = $request->time;
			
			$order = Order::where('id',$order_id)->first();
			$shippingcomp = ShippingCompany::whereId($shipping_id)->whereStatus(1)->first();
			
			if($shippingcomp->id == 2)
			{ 
				$token = Helper::xpressBeesToken($shippingcomp->id); 
				$url = $shippingcomp->url.'fm/request/new/'; 
				
				$courierware = CourierWarehouse::where('vendor_address_id',$order->vendor_address_id)->where('shipping_id',$shipping_id)->first();
				
				$dataArray = [
				'pickup_time'=> $time,
				'pickup_date'=> $date,
				'pickup_location'=> $courierware->warehouse_name,
				'expected_package_count'=> 1,
				];
				
				$response = Helper::postCurl($url,json_encode($dataArray),$token);
				
				// echo "<pre>"; print_r($response); echo "</pre>"; die;
				if(isset($response['error']))
				{
					// 	$msg = $response['error']['message'];
					$msg = $response['error'];
					return response()->json(['status'=>'error','msg'=>$msg]);
				}
				
				if(isset($response['pickup_id']))
				{
					Order::where('id',$order_id)->update(['pickup_location_name'=>$response['pickup_location_name'],'pickup_time'=>$response['pickup_time'],'pickup_id'=>$response['pickup_id'],'pickup_date'=>$response['pickup_date'],'expected_package_count'=>$response['expected_package_count']]);
					return response()->json(['status'=>'success','msg'=>'The pickup request has been generated.']); 
				}
				else
				{ 
					$msg = "Something went wrong.";
					if(isset($response['pickup_time']))
					{
						$msg = $response['pickup_time'];
					}
					if(isset($response['pickup_date']))
					{
						$msg = $response['pickup_date'];
					}
					if(isset($response['pickup_location']))
					{
						$msg = $response['pickup_location'];
					}
					if(isset($response['error']))
					{
						$msg = $response['error']['message'];
					}
					return response()->json(['status'=>'error', 'msg'=>$msg]);
				}
			}
		}
		
		// Warehouse pickup 
		public function orderWarehouseList()
		{
			$user = Auth::user();
			$pickupWarehouses = CourierWarehouse::where('user_id', $user->id)
			->where('warehouse_status', 1)
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
			$customers = Customer::where('user_id', $user->id)
			->where('status', 1)
			->get();
			
			$options = ['<option value="">Select Recipeint/Customer</option>'];
			
			foreach ($customers as $customer) { 
				$options[] = '<option value="' . $customer->id . '">'
				. $customer->first_name.' '.$customer->last_name . ' - (' . $customer->mobile . ')</option>';
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
		
		public function generatePendingExcel()
		{ 
			error_reporting(0);
			return Excel::download(new PendingStarOrderExport,'star-express-order.xlsx');
		}
		
		
		public function orderTrackingHistoryGlobal($id)
		{
			error_reporting(0);
			$order = Order::whereId($id)->first();
			$trackingHistories = [];
			if(!empty($order->awb_number))
			{ 
				$shippingcomp = ShippingCompany::whereId($order->shipping_company_id)->whereStatus(1)->first();
				if($shippingcomp->id == 1)
				{ 
					$token = Helper::xpressBeesToken($shippingcomp->id);
					
					$url = $shippingcomp->url.'api/shipments2/track/'.$order->awb_number;
					
					$response = Helper::callCurlGetApi($url,$token); 
					$response = json_decode($response,true);
					if($response['status'] == true)
					{
						$trackingHistories = $response['data']['history'];
					}
				} 
				
				if($shippingcomp->id == 2)
				{ 
					$token = Helper::xpressBeesToken($shippingcomp->id);
					
					$url = $shippingcomp->url.'api/v1/packages/json?waybill='.$order->awb_number.'&token='.$token.'';
					$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array(
					'Cookie: sessionid=bg71z30eiahrpwfasedox06td1arwx4p'
					),
					));
					
					$response = curl_exec($curl);
					
					curl_close($curl);
					$response = json_decode($response,true);
					
					if(isset($response['Success']))
					{
						if(empty($response['Success']))
						{
							return back()->with('error',$response['Error']);
						}
					}
					
					$reversedArray = $response['ShipmentData'][0]['Shipment']['Scans']; 
					$trackingHistories = array_reverse($reversedArray); 
				} 
				
				if($shippingcomp->id == 3)
				{ 
					
					$curl = curl_init();
					
					curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://shipway.in/api/getOrderShipmentDetails',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{
					"username":"starexpress",
					"password":"dafd4ff7d6456bf1d5a25ef2ca69f696",
					"order_id": "'.$order->awb_number.'"
					}',
					));
					
					$response = curl_exec($curl);
					
					curl_close($curl); 
					$response = json_decode($response,true);
					if($response['status'] == "Success")
					{
						$trackingHistories = $response['response']['scan']; 
					}  
				} 
			}
			
			return view('order.tracking-order',compact('order','trackingHistories','shippingcomp'));
		}
		
		public function orderRemmitance()
		{ 
		    $shippingCompany = ShippingCompany::where('status', 1)->get();  
		    return view('remmitance.index', compact('shippingCompany'));
		}
		
		public function orderRemmitanceAjax(Request $request)
		{
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); // Rows display per page
			
			$columnIndex_arr = $request->post('order');
			$columnName_arr = $request->post('columns');
			$order_arr = $request->post('order');
			
			if($order = 'action')  
			{
				$order = 'id';
			}
			
			// Get user role and id
			$role = Auth::user()->role;
			$id = Auth::user()->id;
			
			// Create the initial query builder with necessary joins
			$query = Order::with(['user',])
			->where('orders.status_courier', 'delivered')
			->where('orders.order_type', 'cod')
			->where('orders.is_remmitance', '0');
			
			// Apply user-based filtering (admin check)
			if ($role != "admin") {
				$query->where('orders.user_id', $id);
			} 
			
			// Filter by date range if provided
			if ($request->from_date && $request->to_date) {
				$query->whereBetween('orders.delivery_date', [$request->from_date, $request->to_date]);
				} elseif ($request->from_date) {
				$query->whereDate('orders.delivery_date', '>=', $request->from_date);
				} elseif ($request->to_date) {
				$query->whereDate('orders.delivery_date', '<=', $request->to_date);
			}
			
			// Apply additional filter for shipping company
			if ($request->shipping_company_id) {
				$query->where('orders.shipping_company_id', $request->shipping_company_id);
			}
			
			// Apply search filter if available
			if (!empty($request->input('search'))) {
				$search = $request->input('search');
				$query->where(function ($query) use ($search) {
					return $query->where('orders.created_at', 'LIKE', '%' . $search . '%')
					->orWhereHas('user', function($q) use ($search){
						$q->where('name', 'LIKE', '%' . $search . '%')
						->orWhere('email', 'LIKE', '%' . $search . '%')
						->orWhere('company_name', 'LIKE', '%' . $search . '%')
						->orWhere('mobile', 'LIKE', '%' . $search . '%');
					})  
					->orWhere('orders.awb_number', 'LIKE', "%{$search}%")
					->orWhere('orders.courier_name', 'LIKE', "%{$search}%")
					->orWhere('orders.status_courier', 'LIKE', "%{$search}%")
					->orWhere('orders.total_amount', 'LIKE', "%{$search}%")
					->orWhere('orders.id', 'LIKE', "%{$search}%")
					->orWhere('orders.order_prefix', 'LIKE', "%{$search}%");
				});
			}
			
			// Calculate total amount (total sum of the orders)
			$totalAmountQuery = clone $query;
			//$totalAmount = $totalAmountQuery->select(DB::raw('SUM(orders.total_amount) AS total_Amt'))->first();
			
			// Apply pagination and ordering
			$values = $query
			->offset($start)
			->limit($limit)
			->orderByDesc('id')
			->get();
			
			// Calculate the total filtered records
			$totalFiltered = $query->count();
			
			// Prepare the data for response
			$data = [];
			foreach ($values as $value) 
			{ 
				$status_courier = '<p class="prepaid">' . $value->status_courier . '</p>'; 
				$data[] = [
				'id' => '<input type="checkbox" class="order-checkbox" data-order_id="' . $value->id . '">',
				'order_id' => '#'.$value->id,
				'seller_details' => '<div class="main-cont1-2"><p>' . ( $value->user->name ?? '') . ' (' . ( $value->user->company_name ?? '') . ')</p><p>' . ( $value->user->email ?? '') . '</p><p>' . ( $value->user->mobile ?? '') . '</p></div>',
				'amount' => $value->total_amount,
				'delivery_date' => $value->delivery_date,
				'shipment_details' => '<p>' . $value->courier_name . '</p><p>AWB No: <b>' . $value->awb_number . '</b></p>',
				'status_courier' => $status_courier
				];
			}
			
			// Return response in JSON format
			return response()->json([
			'draw' => intval($draw),
			'iTotalRecords' => $totalFiltered,
			'iTotalDisplayRecords' => $totalFiltered,
			'aaData' => $data,
			'totAmt' => $totalAmount->total_Amt ?? 0,
			]);
		}
		
		public function orderRemmitanceStore(Request $request)
		{  
			DB::beginTransaction();
			try {  
				// Convert orders_id string to an array and filter empty values
				$orderIds = array_filter(explode(',', $request->input('orders_id')));
				
				// If no valid order IDs, return with error
				if (empty($orderIds)) {
					return redirect()->back()->with(['success' => false, 'message' => 'No valid orders selected']);
				}
				
				Order::query()
				->whereIn('id', $orderIds)
				->update([
				'remittance_reference_id' => $request->input('remittance_reference_id'),
				'remittance_amount' => $request->input('remittance_amount'),
				'remittance_date' => $request->input('remittance_date'),
				'is_remmitance' => $request->input('is_remmitance') 
				]);
				
				DB::commit(); 
				return redirect()->back()->with(['success' => true, 'message' => 'Orders remmittance created successfully']);
				
				} catch (\Exception $e) {
				DB::rollback();
				return redirect()->back()->with(['success' => false, 'message' => 'Something went wrong. Please try again.']);
			}
		} 
		
		public function codVoucher()
		{
		    return view('remmitance.codvoucher');
		}
		
		public function codVoucherAjax(Request $request)
		{
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); // Rows display per page
			
			// Get sorting and searching parameters
			$columnIndex_arr = $request->post('order');
			$columnName_arr = $request->post('columns');
			$order_arr = $request->post('order');
			$search_arr = $request->post('search');
			$status = $request->post('status');
			
			$columnIndex = $columnIndex_arr[0]['column']; // Column index
			$order = $columnName_arr[$columnIndex]['data']; // Column name
			$dir = $order_arr[0]['dir']; // asc or desc
			
			// Fix column name for 'action' field
			if ($order == 'action') {
				$order = 'id';
			}
			
			$role = Auth::user()->role;
			$id = Auth::user()->id;
			
			// Create the initial query with necessary joins and base conditions
			$query = DB::table('cod_vouchers')
			->join('users as u', 'u.id', '=', 'cod_vouchers.user_id')
			->leftJoin('orders as o', 'o.id', '=', 'cod_vouchers.order_id')
			->select(
			'cod_vouchers.user_id',
			'u.name as user_name',
			'u.company_name as seller_company',
			'u.email as user_email',
			'u.mobile as user_mobile',
			DB::raw('GROUP_CONCAT(cod_vouchers.order_id) as order_ids'),
			DB::raw('GROUP_CONCAT(o.order_prefix) as order_prefix_list'),
			DB::raw('GROUP_CONCAT(cod_vouchers.shipping_company_id) as shipping_company_ids'),
			DB::raw('GROUP_CONCAT(cod_vouchers.voucher_date) as voucher_date'),
			DB::raw('GROUP_CONCAT(cod_vouchers.amount) as amounts'),
			DB::raw('SUM(cod_vouchers.amount) as total_amount'),
			'cod_vouchers.voucher_no'
			);
			
			// Admin check for user-based filtering
			if ($role != "admin") {
				$query->where('cod_vouchers.user_id', $id);
			}
			
			// Apply search filters
			if (!empty($request->input('search'))) {
				$search = $request->input('search');
				$query->where(function ($query) use ($search) {
					return $query
					->where('u.name', 'LIKE', '%' . $search . '%')
					->orWhere('u.mobile', 'LIKE', '%' . $search . '%')
					->orWhere('u.email', 'LIKE', '%' . $search . '%')
					->orWhere('u.company_name', 'LIKE', '%' . $search . '%');
				});
			}
			
			// Apply groupBy and pagination logic
			$query->groupBy('cod_vouchers.user_id', 'u.name', 'u.company_name', 'u.email', 'u.mobile', 'cod_vouchers.voucher_no')
			->orderBy('cod_vouchers.' . $order, $dir)
			->offset($start)
			->limit($limit);
			
			// Get the results
			$values = $query->get();
			
			// Count total records and filtered records
			$totalData = DB::table('cod_vouchers')
			->join('users as u', 'u.id', '=', 'cod_vouchers.user_id')
			->leftJoin('orders as o', 'o.id', '=', 'cod_vouchers.order_id')
			->count();
			
			$totalFiltered = $values->count();
			
			// Prepare the data for response
			$data = [];
			if ($values->isNotEmpty()) {
				$i = $start + 1;
				foreach ($values as $value) {
					// Process shipping companies
					$shipping = explode(',', $value->shipping_company_ids);
					$ship_comp = array_map(function ($shipping_comp) {
						return DB::table('shipping_companies')->where('status', '1')->pluck('name')->first();
					}, $shipping);
					
					$mainData = [
					'id' => $i,
					'seller_details' => '<div class="main-cont1-2"><p>' . $value->user_name . ' (' . $value->seller_company . ') </p><p>' . $value->user_email . ' </p><p>' . $value->user_mobile . ' </p></div>',
					'order_details' => '<div class="main-cont1-1"><div class="checkbox checkbox-purple">' . $value->order_ids . '</div>',
					'amount' => $value->amounts,
					'total_amount' => $value->total_amount,
					'action' => '<div class="main-btn-1"><div class="mian-btn"><div class="btn-group"><a href="' . route("viewvoucher", $value->voucher_no) . '"><button class="btn btn-primary" type="button">View</button></a></div></div></div>'
					];
					
					$data[] = $mainData;
					$i++;
				}
			}
			
			// Return the final response as JSON
			return response()->json([
			"draw" => intval($draw),
			"iTotalRecords" => $totalData,
			"iTotalDisplayRecords" => $totalFiltered,
			"aaData" => $data
			]);
		}
		
		
		public function generateVouchers(Request $request)
        {
            $date = $request->date;
            $today = date('Y-m-d');
			
            try {
                DB::beginTransaction();
				
                $orders = DB::table('orders')
				->whereDate('order_date', '<', $date)
				->where('status_courier', '=', 'Delivered')
				->where('is_remmitance', '=', '1')
				->where('is_payout', '=', '0')
				->where('is_voucher', '=', '0')
				->get();
				$groupedOrders = $orders->groupBy('user_id');
				
				foreach ($groupedOrders as $userId => $userOrders) {
				    $lastVoucher = DB::table('cod_vouchers')->orderBy('id', 'desc')->first();
				    $voucherNumber = $lastVoucher ? (explode('-', $lastVoucher->voucher_no)[1] + 1) : 1;
				    $voucherNo = "VN-" . sprintf('%04d', $voucherNumber);
					
				    foreach ($userOrders as $order) {
						$voucherData = DB::table('cod_vouchers')->where('order_id', $order->id)->first();
						
						if (!isset($voucherData)) {
							$voucherData = [
							'voucher_no' => $voucherNo,
							'order_id' => $order->id,
							'user_id' => $order->user_id,
							'shipping_company_id' => $order->shipping_company_id,
							'amount' => $order->total_amount,
							'voucher_status' => '0',
							'voucher_date' => $today,
							];
							
							DB::table('cod_vouchers')->insert($voucherData);
							
							Order::where('id', $order->id)->update([
							'voucher_no' => $voucherNo,
							'is_voucher' => '1',
							]);
						}
					}
				} 
                DB::commit();
				
                return redirect()->back()->with(['success' => true, 'message' => 'Voucher generated successfully']);
				} catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with(['error' => true, 'message' => $e->getMessage()]);
			}
		}
		
		public function viewvoucher($voucher_no)
        {
			$data['voucher_lists']= DB::table('cod_vouchers')
			->select('cod_vouchers.*', 'shipping_companies.name as shipping_companies_name', 'orders.awb_number','order_items.product_name', 'order_items.product_discription', 'orders.order_date')
			->join('orders', 'cod_vouchers.order_id', '=', 'orders.id')
			->join('shipping_companies', 'shipping_companies.id', '=', 'cod_vouchers.shipping_company_id')
			->join('order_items', 'cod_vouchers.order_id', '=', 'order_items.order_id')
			->where('cod_vouchers.voucher_no', '=', $voucher_no)
			->get();
			
            $data['voucher_data'] =DB::table('cod_vouchers')
			->join('users as u', 'u.id', '=', 'cod_vouchers.user_id')
			->where('cod_vouchers.voucher_no', $voucher_no)
			->select('cod_vouchers.user_id', 'u.name as user_name', 'u.company_name as seller_company', 'u.address as user_address','u.email as user_email', 'u.mobile as user_mobile',
			DB::raw('GROUP_CONCAT(cod_vouchers.order_id) as order_id'),
			DB::raw('GROUP_CONCAT(cod_vouchers.shipping_company_id) as shipping_company_id'),
			DB::raw('GROUP_CONCAT(DISTINCT amount) AS amount'), 'cod_vouchers.voucher_no', 'cod_vouchers.voucher_date',
			DB::raw('SUM(cod_vouchers.amount) as total_amount'))
			->groupBy('cod_vouchers.user_id', 'cod_vouchers.voucher_no', 'cod_vouchers.voucher_date', 'u.address','u.name', 'u.company_name', 'u.email', 'u.mobile','cod_vouchers.voucher_no')->first(); 
			// ->groupBy('cod_vouchers.user_id','cod_vouchers.voucher_no')->get();
            if ($data['voucher_lists']->isEmpty()) {
                
                return redirect()->back()->with(['error' => true, 'message' => 'Voucher not found']);
			}
			
            return view('remmitance.voucher', compact('data'));
		}
		
		public function codPayout()
		{
			$vendors = DB::table('cod_vouchers')
			->join('users', 'users.id', '=', 'cod_vouchers.user_id')
			->select('users.id', 'users.name')
			->groupBy('users.id', 'users.name')
			->get();
		    return view('remmitance.codpayout', compact('vendors'));
		}
		
		public function codPayoutajax(Request $request)
		{
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); // Rows display per page
			
			$columnIndex_arr = $request->post('order');
			$columnName_arr = $request->post('columns');
			$order_arr = $request->post('order');
			$search_arr = $request->post('search');
			$status = $request->post('status');
			
			$columnIndex = $columnIndex_arr[0]['column']; // Column index
			$order = $columnName_arr[$columnIndex]['data']; // Column name
			$dir = $order_arr[0]['dir']; // asc or desc
			
			if($order = 'action')  
			{
			    $order = 'id';
			}
			
			$role = Auth::user()->role;
			$id = Auth::user()->id;
			
			$query = DB::table('cod_vouchers')
			->join('users as u', 'u.id', '=', 'cod_vouchers.user_id')
			->leftJoin('orders as o', 'o.id', '=', 'cod_vouchers.order_id')
			->select('cod_vouchers.user_id', 'cod_vouchers.voucher_status','cod_vouchers.reference_no','cod_vouchers.payout_date', 'u.name as user_name', 'u.company_name as seller_company', 'u.email as user_email', 'u.mobile as user_mobile',
			DB::raw('GROUP_CONCAT(cod_vouchers.order_id) as order_ids'),
			DB::raw('GROUP_CONCAT(o.order_prefix) as order_prefix_list'),
			DB::raw('GROUP_CONCAT(cod_vouchers.shipping_company_id) as shipping_company_ids'), 
			DB::raw('SUM(cod_vouchers.amount) as total_amount'));
			if($role != "admin") 
			{
				$query->where('cod_vouchers.user_id',$id);
			}
			$query->groupBy('cod_vouchers.user_id');
			
			$totalData = $query->count();
			
			$totalFiltered = DB::table('cod_vouchers')
			->join('users as u', 'u.id', '=', 'cod_vouchers.user_id')
			->leftJoin('orders as o', 'o.id', '=', 'cod_vouchers.order_id')
			->select('cod_vouchers.user_id', 'cod_vouchers.voucher_status','cod_vouchers.reference_no','cod_vouchers.payout_date','u.name as user_name', 'u.company_name as seller_company', 'u.email as user_email', 'u.mobile as user_mobile',
			DB::raw('GROUP_CONCAT(cod_vouchers.order_id) as order_ids'),
			DB::raw('GROUP_CONCAT(o.order_prefix) as order_prefix_list'),
			DB::raw('GROUP_CONCAT(cod_vouchers.shipping_company_id) as shipping_company_ids'),
			// DB::raw('GROUP_CONCAT(cod_vouchers.voucher_date) as order_dates'),
			DB::raw('SUM(cod_vouchers.amount) as total_amount'));
			if($role != "admin") 
			{
				$totalFiltered->where('cod_vouchers.user_id',$id);
			}
			$totalFiltered->groupBy('cod_vouchers.user_id');
			
			
			
            $values = DB::table('cod_vouchers')
			->join('users as u', 'u.id', '=', 'cod_vouchers.user_id')
			->leftJoin('orders as o', 'o.id', '=', 'cod_vouchers.order_id')
			->select('cod_vouchers.user_id', 'cod_vouchers.voucher_status','cod_vouchers.reference_no','cod_vouchers.payout_date','u.name as user_name', 'u.company_name as seller_company', 'u.email as user_email', 'u.mobile as user_mobile',
			DB::raw('GROUP_CONCAT(cod_vouchers.order_id) as order_ids'),
			DB::raw('GROUP_CONCAT(o.order_prefix) as order_prefix_list'),
			DB::raw('GROUP_CONCAT(cod_vouchers.shipping_company_id) as shipping_company_ids'),
			DB::raw('GROUP_CONCAT(cod_vouchers.id) as voucher_ids'),
			DB::raw('GROUP_CONCAT(cod_vouchers.voucher_date) as voucher_date'),
			DB::raw('GROUP_CONCAT(cod_vouchers.amount) as total_amount'),
			DB::raw('SUM(cod_vouchers.amount) as voucher_total_amount'),
			'cod_vouchers.voucher_no');
			if($role != "admin") 
			{
				$values->where('cod_vouchers.user_id',$id);
			}
			//   echo $request->input('voucher_status'); die;
			if($request->input('voucher_status') != '')
			{
			    
				
				$status = $request->input('voucher_status');
				
				$values = $values->where('cod_vouchers.voucher_status',$status);
			}
			if(!empty($request->input('user_id')))
			{
				$voucher_user_id = $request->input('user_id');
				$values = $values->where('cod_vouchers.user_id',$voucher_user_id);
			}
			if ($request->fromdate != '' && $request->todate != '') {
                $values->whereBetween('cod_vouchers.voucher_date', [$request->fromdate, $request->todate]);
				} elseif ($request->fromdate) {
                $values->whereDate('cod_vouchers.voucher_date', '>=', $request->fromdate);
				} elseif ($request->todate) {
                $values->whereDate('cod_vouchers.voucher_date', '<=', $request->todate);
			}
			if(!empty($request->input('search')))
			{ 
				$search = $request->input('search');
				$values = $values->where(function ($query) use ($search) 
				{
					return $query->where('u.name', 'LIKE', '%' . $search . '%')->orWhere('u.mobile', 'LIKE', '%' . $search . '%')->orWhere('u.email', 'LIKE', '%' . $search . '%')->orWhere('u.company_name', 'LIKE', '%' . $search . '%');
				});
				
				$totalFiltered = $totalFiltered->where(function ($query) use ($search) {
					return $query->where('u.name', 'LIKE', '%' . $search . '%')->orWhere('u.mobile', 'LIKE', '%' . $search . '%')->orWhere('u.email', 'LIKE', '%' . $search . '%')->orWhere('u.company_name', 'LIKE', '%' . $search . '%');
				});  
			}
			$clonedQuery = clone $values;
			
            $resultsCloned = $clonedQuery
            ->select(DB::raw('SUM(cod_vouchers.amount) AS total_Amt'))
            ->first();
			$values->orderBy('cod_vouchers.' . $order, $dir)
			->offset($start)
			->limit($limit)
			->groupBy('cod_vouchers.user_id','cod_vouchers.voucher_status','cod_vouchers.reference_no','cod_vouchers.payout_date', 'u.name', 'u.company_name', 'u.email', 'u.mobile','cod_vouchers.voucher_no');
			$values = $values->get(); 
			$totalFiltered = $totalFiltered->count();
			
			$data = array();
			if(!empty($values))
			{
				$i = $start + 1; 
				foreach ($values as $value)
				{    
					$shipping = explode(',',$value->shipping_company_ids);
					$ship_comp =array();
					foreach($shipping as $shipping_comp)
					{
					    $shippinng = DB::table('shipping_companies')->where('status','1')->pluck('name')->first();
					    $ship_comp[] = $shippinng;
					}
					// 	echo "<pre>"; print_r($value); echo "</pre>"; die;
					if($value->voucher_status === '0')
					{
						$voucher_status = '<span class="badge badge-warning">Unpaid</span>';
					}else
					{
						$voucher_status = '<span class="badge badge-success">Paid</span>';
					}
					$mainData['id'] = $i;
					$mainData['order_prefix_list'] = $value->order_prefix_list;
					$mainData['seller_details'] = ' <div class="main-cont1-2"><p> '.$value->user_name.' ('.$value->seller_company .') </p><p> '.$value->user_email.'  </p><p> '.$value->user_mobile.' </p></div>';
					$mainData['order_details'] = '<div class="main-cont1-1"><div class="checkbox checkbox-purple">'.$value->order_ids.'</div> '; 
					$mainData['status'] = $voucher_status;
					$mainData['amount'] = $value->voucher_total_amount;
					$mainData['action'] = "";   
					if($value->voucher_status == 0 && config('permission.cod_payout.add'))
					{
						$mainData['action'] .= '<div class="main-btn-1"> 
						<div class="mian-btn"> 
						<div class="btn-group">
						<button class="btn btn-primary pay_now" data-voucher_no="'. $value->voucher_ids .'" data-order-id="'.$value->order_ids.'" data-user-id="'.$value->user_id.'" data-shipping_company_ids="'.$value->shipping_company_ids.'" data-amount="'.$value->total_amount.'" type="button"> Pay Now</button> 
						
						</div>
						</div>
						</div>';
					}else
				    {
				        $mainData['action'] .= ' <div class="main-cont1-2"><b>Ref. No. :</b><p>'.$value->reference_no.'</p> <b>Payout Date :</b>'.$value->payout_date.'';
					}
					$data[] = $mainData;
					$i++;
				}
			}
			
			$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalData,
			"iTotalDisplayRecords" => $totalFiltered,
			"aaData" => $data,
			"totAmt"=>$resultsCloned->total_Amt,
				); 
				
				echo json_encode($response);
				exit;
		}
		
        public function codRemittance(Request $request)
        {
            
            try {
                $orderIds = explode(",", $request->order_id);
                $voucherIds = explode(",", $request->voucher_no);
				
                $latestInvoice = DB::table('order_remittances')->orderBy('id', 'desc')->pluck('inv_no')->first();
                $inv_number = $latestInvoice ? explode('-', $latestInvoice)[1] + 1 : 1;
                $inv_no = "INVCODP-" . sprintf('%04d', $inv_number);
				
                $orderRemittanceData = [
				'inv_no' => $inv_no,
				'order_id' => $request->order_id,
				'user_id' => $request->user_ids,
				'shipping_company_id' => $request->shipping_company_id,
				'reference_no' => $request->remittance_reference_id,
				'amount' => $request->amount,
				'date' => $request->remittance_date,
                ];
				
                DB::beginTransaction();
				
                DB::table('order_remittances')->insert($orderRemittanceData);
				
                $voucherUpdateData = [
				'voucher_status' => '1',
				'reference_no' => $request->remittance_reference_id,
				'payout_date' => $request->remittance_date
                ];
				
                foreach ($voucherIds as $voucher_id) {
                    DB::table('cod_vouchers')
					->where('id', $voucher_id)
					->update($voucherUpdateData);
				}
				
                foreach ($orderIds as $order_id) {
                    DB::table('orders')->where('id', $order_id)->update(['is_payout' => '1']);
				}
				
                DB::commit();
				
                return redirect()->back()->with('success', 'Cod Payout successfully.');
				} catch (\Exception $e) {
                DB::rollBack();
                \Log::error($e->getMessage());
                return redirect()->back()->with('error', 'An error occurred. Please try again.');
			}
		}
		
		public function pickup()
		{			
			$status = (isset($_GET['status']))?$_GET['status']:'New';
			return view('pickup.index',compact('status'));
		}
		
		public function pickupAjax(Request $request)
		{
		    
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); // Rows display per page
			
			$columnIndex_arr = $request->post('order');
			$columnName_arr = $request->post('columns');
			$order_arr = $request->post('order');
			$search_arr = $request->post('search');
			
			// $status = $request->post('status');
			$status = "pending pickup";
			
			
			$columnIndex = $columnIndex_arr[0]['column']; // Column index
			$order = $columnName_arr[$columnIndex]['data']; // Column name
			$dir = $order_arr[0]['dir']; // asc or desc
			
			if($order = 'action')  
			{
				$order = 'id';
			}
			
			$role = Auth::user()->role;
			$id = Auth::user()->id;
			
			$query = Order::where('orders.id','!=','');
			// 			$query->where('orders.shipping_company_id','=',3);
			$query->where('orders.status_courier', 'LIKE', "%$status%");
			$query->join('customers as c','c.id','=','orders.customer_id');
			$query->join('vendors as v','v.id','=','orders.vendor_id');
			$query->join('users as u','u.id','=','orders.user_id');
			// echo "<pre>"; print_r($id); echo "</pre>"; die;
			if($role != "admin") 
			{
				$query->where('orders.user_id',$id);
			}
			if($role == "staff") 
			{
				$query->where('u.staff_id',$id);
			}
			// 			if($status != "All") 
			// 			{
			// 				$query->where('orders.status_courier', 'LIKE', "%$status%");
			// 			}
			$totalData = $query->get()->count();
			// echo "<pre>";	 print_r($totalData); echo "</pre>"; die;
			$totalFiltered = Order::where('orders.id','!=','');
			// 			$totalFiltered->where('orders.shipping_company_id','=',3);
			$totalFiltered->where('orders.status_courier', 'LIKE', "%$status%");
			$totalFiltered->join('customers as c','c.id','=','orders.customer_id');
			$totalFiltered->join('vendors as v','v.id','=','orders.vendor_id');
			$totalFiltered->join('users as u','u.id','=','orders.user_id');
			
			if($role != "admin") 
			{
				$totalFiltered->where('orders.user_id',$id);
			}
			if($role == "staff") 
			{
				$totalFiltered->where('u.staff_id',$id);
			}
			// 			if($status != "All") 
			// 			{
			// 				$totalFiltered->where('status_courier', 'LIKE', "%$status%");;
			// 			}
			
			$values = Order::select('orders.*','c.first_name as c_first_name','c.mobile as c_mobile','c.email as c_email','c.last_name as c_last_name','v.first_name as v_first_name','v.last_name as v_last_name','v.mobile as v_mobile','v.company_name as v_company_name','u.name as user_name','u.company_name as seller_company','u.email as user_email','u.mobile as user_mobile');
			// 			$values->where('orders.shipping_company_id','=',3);
			$values->where('orders.status_courier', 'LIKE', "%$status%");
			$values->join('customers as c','c.id','=','orders.customer_id');
			$values->join('vendors as v','v.id','=','orders.vendor_id');
			$values->join('users as u','u.id','=','orders.user_id');
			
			if($request->fromdate)
			{
				$values->whereDate('orders.pickup_date','like',$request->fromdate);
			}
			if($request->todate)
			{
				$values->whereDate('orders.pickup_date','like',$request->todate);
			}
			if($role != "admin") 
			{
				$values->where('orders.user_id',$id);
			}
			if($role == "staff") 
			{
				$values->where('u.staff_id',$id);
			}
			// 			if($status != "All") 
			// 			{
			// 				$values->where('status_courier', 'LIKE', "%$status%");
			// 			}
			$values->offset($start)->limit($limit)->orderBy('orders'.'.'.$order,$dir);
			
			if(!empty($request->input('search')))
			{ 
				$search = $request->input('search');
				$values = $values->where(function ($query) use ($search) 
				{
					return $query->where('c.first_name', 'LIKE', '%' . $search . '%')->orWhere('c.last_name', 'LIKE', '%' . $search . '%')->orWhere('c.email', 'LIKE', '%' . $search . '%')->orWhere('c.mobile', 'LIKE', '%' . $search . '%')->orWhere('u.name', 'LIKE', '%' . $search . '%')->orWhere('u.email', 'LIKE', '%' . $search . '%')->orWhere('u.mobile', 'LIKE', '%' . $search . '%')->orWhere('u.company_name', 'LIKE', '%' . $search . '%')->orWhere('orders.created_at', 'LIKE',"%{$search}%")->orWhere('orders.awb_number', 'LIKE',"%{$search}%")->orWhere('orders.courier_name', 'LIKE',"%{$search}%")->orWhere('orders.status_courier', 'LIKE',"%{$search}%")->orWhere('orders.id', 'LIKE',"%{$search}%")->orWhere('orders.order_prefix', 'LIKE',"%{$search}%");
				});
				
				$totalFiltered = $totalFiltered->where(function ($query) use ($search) {
					return $query->where('c.first_name', 'LIKE', '%' . $search . '%')->orWhere('c.last_name', 'LIKE', '%' . $search . '%')->orWhere('c.email', 'LIKE', '%' . $search . '%')->orWhere('c.mobile', 'LIKE', '%' . $search . '%')->orWhere('u.name', 'LIKE', '%' . $search . '%')->orWhere('u.email', 'LIKE', '%' . $search . '%')->orWhere('u.mobile', 'LIKE', '%' . $search . '%')->orWhere('u.company_name', 'LIKE', '%' . $search . '%')->orWhere('orders.created_at', 'LIKE',"%{$search}%")->orWhere('orders.awb_number', 'LIKE',"%{$search}%")->orWhere('orders.courier_name', 'LIKE',"%{$search}%")->orWhere('orders.status_courier', 'LIKE',"%{$search}%")->orWhere('orders.id', 'LIKE',"%{$search}%")->orWhere('orders.order_prefix', 'LIKE',"%{$search}%");
				});  
			}
			
			$values = $values->get(); 
			$totalFiltered = $totalFiltered->count();
			
			$data = array();
			if(!empty($values))
			{
				$i = $start + 1; 
				foreach ($values as $value)
				{    
					
					$orderitems = OrderItem::whereOrder_id($value->id)->get();
					$vendoradd = VendorAddress::whereId($value->vendor_address_id)->first();
					$product_details = "";
					foreach($orderitems as $key => $orderitem)
					{
						$product_details .= '<p>'.$orderitem->product_name.'  Amount :'.$orderitem->amount.'  QTY : '.$orderitem->quantity.'</p>'; 
						if(count($orderitems) - 1 != $key)
						{
							$product_details .= ' | ';
						}
					}
					$volumetric_wt = $value->length * $value->width * $value->height / 5000;
					
					$mainData['id'] = $i;
					$mainData['seller_details'] = ' <div class="main-cont1-2"><p> '.$value->user_name.' ('.$value->seller_company .') </p><p> '.$value->user_email.'  </p><p> '.$value->user_mobile.' </p></div>';
					$mainData['order_details'] = '<div class="main-cont1-1"><div class="checkbox checkbox-purple"><a href="'.url('order/details/'.$value->id).'"> #'.$value->order_prefix.' </a> </div><p style="padding-left:0"> '.date('Y M d | h:i A',strtotime($value->created_at)).'</p>  <span  style="padding-left:0"> <a href="javascript:;" ><div class="tooltip" data-toggle="tooltip" data-placement="top" title="'.strip_tags($product_details).'"> View Products</div></a> </span></div>'; 
					
					$mainData['customer_details'] = ' <div class="main-cont1-2"><p> '.$value->c_first_name.' '.$value->c_last_name .' </p><p> '.$value->c_email.'  </p><p> '.$value->c_mobile.' </p></div>';
					
					$mainData['package_details'] = '<div class="main-cont1-2"><p> Dead wt. : '.$value->weight.' kg </p><p> '.$value->length.' x '.$value->width.' x '.$value->height.' (cm) </p><p> Volumetric wt.: '.$volumetric_wt.' Kg </p></div>';
					
					$mainData['total_amount'] =  ' <div class="main-cont1-2"> <p> '.$value->total_amount.' </p><p class="'.strtolower($value->order_type).'"> '.$value->order_type.' </p></div>';
					
					$mainData['pickup_address'] =  '<div class="tooltip"> '.strtoupper($value->v_company_name).'<span class="tooltiptext"> <b> '.$value->v_first_name.' '.$value->v_last_name.' </b><br><b>Address </b>: '.$vendoradd->address.' '.$vendoradd->city.' <br> '.$vendoradd->state.'-'.$vendoradd->zip_code.'<br>'.$value->v_mobile.'</span></div>'; 
					
					$mainData['status_courier'] = '<p class="prepaid"> '.$value->status_courier.' </p>'; 
					if($value->pickup_date == '')
					{
						$pickup_date = "Not Found";
					}else
					{
						$pickup_date = $value->pickup_date;
					}
					if($value->pickup_id == '' )
					{
					    $pickup_id = "Not Found";
					}else
					{
					    $pickup_id = $value->pickup_id;
					}
					if($value->pickup_time == '')
					{
						$pickup_time = "Not Found";
					}else
					{
						$pickup_time = $value->pickup_time;
					}
					$mainData['pickup_details'] = ' <div class="main-cont1-2"><p> '.$pickup_id.'  </p><p> '.$pickup_date	.'  </p><p> '.$pickup_time.' </p></div>';
					
					$data[] = $mainData;
					$i++;
				}
			}
			
			$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalData,
			"iTotalDisplayRecords" => $totalFiltered,
			"aaData" => $data
			); 
			
			echo json_encode($response);
			exit;
		}
	}
