<?php
	
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request; 
	use App\Models\User; 
	use App\Models\ShippingCompany;
	use App\Models\PickupRequest;
	use App\Models\CourierWarehouse;
	use DB,Auth,File,Helper; 
	use App\Services\DelhiveryService;
	use App\Services\ShipMozo;
	use App\Services\DelhiveryB2CService;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Validation\Rule;
	use App\Traits\ApiResponse;   
	
	class WarehouseController extends Controller
	{	 
		use ApiResponse; 
		
		public function __construct()
		{ 
			$this->delhiveryService = new DelhiveryService();
			$this->delhiveryB2CService = new DelhiveryB2CService();
			$this->shipMozo = new ShipMozo();
		}
		 
		public function index(Request $request)
		{ 
			$user = Auth::user();
			$role = $user->role;
			$userId = $user->id;
			
			$search = $request->input('search');
			$offset = $request->input('offset');
			$limit = $request->input('limit');
			
			// Base query
			$query = CourierWarehouse::with('user:id,name');

			if ($role === "user") {
				$query->where('user_id', $userId);
			}
 
			if (!empty($search)) { 
				$query->where(function ($q) use ($search) {
					$q->where('company_name', 'LIKE', "%{$search}%")
						->orWhere('contact_name', 'LIKE', "%{$search}%")
						->orWhere('contact_number', 'LIKE', "%{$search}%")
						->orWhere('warehouse_name', 'LIKE', "%{$search}%")
						->orWhere('address', 'LIKE', "%{$search}%")
						->orWhereDate('created_at', 'LIKE', "%{$search}%");
				});
			} 
			 
			$values = $query->orderBy('id')
			->offset($offset)
			->limit($limit)
			->get();
			
			return $this->successResponse($values, 'warehouse fetched successfully.');
		}
		    
		public function storeWarehouse(Request $request)
		{
			$user_id = Auth::id();
			$timestamp = now();

			// Validate input, including warehouse name uniqueness
			$validator = Validator::make($request->all(), [
				'warehouse_name' => 'required|unique:courier_warehouses,warehouse_name',
				'company_name' => 'required|string|max:255',
				'contact_name' => 'required|string|max:255',
				'contact_email' => 'required|email',
				'contact_number' => 'required|digits_between:7,15',
				'address' => 'required|string',
				'country' => 'required|string',
				'state' => 'required|string',
				'city' => 'required|string',
				'zip_code' => 'required|digits:6',
			]);
 
			if ($validator->fails()) {
				return $this->validateResponse('Validation failed', 422, $validator->errors());
			}

			// Prepare vendor data
			$warehouseData = $request->except('_token');
			$warehouseData['user_id'] = $user_id;
			$warehouseData['created_at'] = $timestamp;
			$warehouseData['updated_at'] = $timestamp;

			DB::beginTransaction();
			try {
			
				$courierWarehouse = CourierWarehouse::create($warehouseData);
				$shippingCompanies = ShippingCompany::whereIn('id', [1])->where('status', 1)->get();
				$this->addWareHouseByAPI($courierWarehouse, $shippingCompanies);

				DB::commit();
				return $this->successResponse($courierWarehouse, 'The warehouse has been successfully added.'); 
			} catch (\Exception $e) {
				DB::rollBack();
				return $this->errorResponse([], 'Failed to add warehouse. Please try again.');  
			}
		}

		public function addWareHouseByAPI($courierWarehouse, $shippingCompanies)
		{
			if ($shippingCompanies->isNotEmpty()) {
				foreach ($shippingCompanies as $shippingCompany)
				{
					if($shippingCompany->id == 1)
					{
						$data = $this->shipMozo->createWarehouse($courierWarehouse, $shippingCompany);  
						if (!isset($data['success']) || !$data['success'] || (isset($data['response']['result']) && $data['response']['result'] == 0)){
							continue;
						}

						if ($data['response']['data']) 
						{    
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
				}
			}
		}
		   
		public function updateWarehouse(Request $request, $id)
		{
			$user_id = Auth::id();
			$timestamp = now();

			// Validate input
			$validator = Validator::make($request->all(), [
				'warehouse_name' => 'required|unique:courier_warehouses,warehouse_name,' . $id,
				'company_name' => 'required|string|max:255',
				'contact_name' => 'required|string|max:255',
				'contact_email' => 'required|email',
				'contact_number' => 'required|digits_between:7,15',
				'address' => 'required|string',
				'country' => 'required|string',
				'state' => 'required|string',
				'city' => 'required|string',
				'zip_code' => 'required|digits:6',
			]);

			if ($validator->fails()) {
				return $this->validateResponse('Validation failed', 422, $validator->errors());
			}

			DB::beginTransaction();
			try { 
				$courierWarehouse = CourierWarehouse::findOrFail($id); 
				$courierWarehouse->fill($request->all()); 
				$courierWarehouse->user_id = $user_id;
				$courierWarehouse->updated_at = $timestamp;

				// Check if warehouse name or address has changed
				$shouldUpdateAPI = $courierWarehouse->isDirty(['address', 'contact_number', 'contact_name', 'contact_email', 'company_name']);

				// Save updates
				$courierWarehouse->save();

				// If name or address changed, update via API
				if ($shouldUpdateAPI) {
					$shippingCompanies = ShippingCompany::whereIn('id', [1])->where('status', 1)->get();
					$this->updateWareHouseByAPI($courierWarehouse, $shippingCompanies);
				}

				DB::commit(); 
				return $this->successResponse($courierWarehouse, 'The warehouse has been successfully updated.'); 
			} catch (\Exception $e) {
				DB::rollBack();
				return $this->errorResponse([], 'Failed to update warehouse. Please try again.');   
			}
		}
 
		public function updateWareHouseByAPI($courierWarehouse, $shippingCompanies)
		{ 
			if ($shippingCompanies->isNotEmpty()) 
			{
				foreach ($shippingCompanies as $shippingCompany) 
				{
					if($shippingCompany->id == 1)
					{
						$data = $this->shipMozo->createWarehouse($courierWarehouse, $shippingCompany);  
						if (!isset($data['success']) || !$data['success'] || (isset($data['response']['result']) && $data['response']['result'] == 0)){
							continue;
						}

						if ($data['response']['data']) 
						{    
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
				}
			}
		}
    
		public function pickupRequestList()
		{ 
			$user = Auth::user();
			$warehouses = CourierWarehouse::where('user_id', $user->id)
			->where('warehouse_status', 1)
			->get();
			
			$shippingCompanies = ShippingCompany::whereIn('id', [2, 3])
			->where('status', 1)
			->get(['id', 'name']);
			
			return view('warehouse.pickup-request', compact('warehouses', 'shippingCompanies'));
		}
		
		public function pickupRequestAjax(Request $request)
		{
			$draw = $request->get('draw', 1);
			$start = $request->get("start", 0);
			$limit = $request->get("length", 10);

			$columnIndexArr = $request->get('order', []);
			$columnNameArr = $request->get('columns', []);
			$orderArr = $request->get('order', []);
			$searchArr = $request->get('search', []);

			$columnIndex = $columnIndexArr[0]['column'] ?? 0;
			$order = $columnNameArr[$columnIndex]['data'] ?? 'id';
			$dir = $orderArr[0]['dir'] ?? 'asc';

			if ($order === 'customer_name') {
				$order = 'id';
			}

			$user = Auth::user();
			$role = $user->role;
			$userId = $user->id;

			// Base query with necessary joins
			$query = PickupRequest::query()->with(['warehouse', 'user', 'shippingCompany']);

			if ($role === "user") {
				$query->where('user_id', $userId);
			}

			// Apply search filter
			if (!empty($searchArr)) {
				$search = trim($searchArr);

				$query->where(function ($q) use ($search) {
					$q->where('pickup_id', 'LIKE', "%{$search}%")
						->orWhereHas('warehouse', function ($q) use ($search) {
							$q->where('warehouse_name', 'LIKE', "%{$search}%")
							  ->orWhere('address', 'LIKE', "%{$search}%")
							  ->orWhere('contact_number', 'LIKE', "%{$search}%");
						})
						->orWhereHas('shippingCompany', function ($q) use ($search) {
							$q->where('name', 'LIKE', "%{$search}%");
						})
						->orWhereHas('user', function ($q) use ($search) {
							$q->where('name', 'LIKE', "%{$search}%")
							  ->orWhere('email', 'LIKE', "%{$search}%");
						})
						->orWhereDate('created_at', 'LIKE', "%{$search}%");
				});
			}

			$totalData = PickupRequest::count();
			$totalFiltered = $query->count();

			// Fetch paginated data
			$values = $query->orderBy($order, $dir)
				->offset($start)
				->limit($limit)
				->get();
 
			$data = [];
			$i = $start + 1;

			foreach ($values as $value) {
				$action = '';

				// Check permission before allowing cancel action
				if ($value->status == 1 && $value->shipping_company_id == 2 && config('permission.pickup_request.delete')) {
					$action = '<a href="' . url('pickup-request/cancel', $value->id) . '" 
						class="btn btn-danger btn-sm" data-toggle="tooltip" 
						title="Cancel Pickup Request" onClick="cancelPickupRequest(this, event);">
						Cancel
					</a>';
				}

				$data[] = [
					'id' => $i++,
					'shipping_company_id' => $value->shippingCompany->name ?? '',
					'pickup_id' => $value->pickup_id,
					'warehouse_id' => ($value->warehouse->warehouse_name ?? '') . '<br>' . ($value->warehouse->address ?? ''),
					'expected_package_count' => $value->expected_package_count,
					'pickup_date' => date('d M Y', strtotime($value->pickup_date)) . '<br>' . 
						date('h:i A', strtotime($value->pickup_start_time)) . ' - ' . 
						date('h:i A', strtotime($value->pickup_end_time)),
					'shipper_contact' => $value->warehouse->contact_number ?? '',
					'status' => $value->status == 1
						? '<span class="badge badge-info">Open</span>'
						: '<span class="badge badge-danger">Cancelled</span>', 
					'created_at' => date('d M Y', strtotime($value->created_at)) . 
						($role == "admin" ? '<br><p>' . ($value->user->name ?? 'N.A') . '</p>' : ''), 
					'action' => $action
				];
			}
 
			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $totalData,
				"iTotalDisplayRecords" => $totalFiltered,
				"aaData" => $data,
			]);
		}
		
		public function createPickupRequest(Request $request)
		{
			DB::beginTransaction();
			
			try {
				// Validate if the shipping company exists
				$shippingCompany = ShippingCompany::where('status', 1)->where('id', $request->shipping_company_id)->first();
				
				if (!$shippingCompany) {
					return redirect()->back()->with('error', 'Shipping company not found or inactive.');
				}
				
				if($shippingCompany->id == 2)
				{
					// Call the external service to create a pickup request
					$data = $this->delhiveryService->createPickupRequest($request->all(), $shippingCompany); 
					
					// Handle API response errors
					if (!($data['success'] ?? false)) {
						$errorMsg = $data['response']['errors'][0]['message'] 
									?? ($data['response']['error']['message'] ?? 'An error occurred.');
						return redirect()->back()->with('error', $errorMsg);
					}

					// Ensure the response contains success confirmation
					if (isset($data['response']['success']) && !$data['response']['success']) { 
						return redirect()->back()->with('error', $data['response']['error']['message'] ?? 'An error occurred.');
					}
					
					// Extract response data
					$responseData = $data['response']['data'] ?? null;
					if (!$responseData || empty($responseData['pickup_id'])) {
						return redirect()->back()->with('error', 'Pickup ID not received from the API.');
					}
				}
				
				if($shippingCompany->id == 3)
				{
					// Call the external service to create a pickup request
					$data = $this->delhiveryB2CService->createPickupRequest($request->all(), $shippingCompany); 
					 
					// Handle API response errors
					if (!($data['success'] ?? false)) {
						$errorMsg = ($data['response']['pickup_location'] ?? $data['response']['pickup_date'])
									?? ($data['response']['error']['message'] ?? 'An error occurred.');
						return redirect()->back()->with('error', $errorMsg);
					}
 
					// Extract response data
					$responseData = $data['response'] ?? null; 
					if(isset($responseData['error']))
					{
					       return redirect()->back()->with('error', $responseData['error']['message']);        
					}
					if (!$responseData || empty($responseData['pickup_id'])) {
						return redirect()->back()->with('error', 'Pickup ID not received from the API.');
					}
				}
				// Prepare data for insertion
				$requestedData = $request->except('_token');
				$requestedData['pickup_id'] = $responseData['pickup_id'];
				$requestedData['user_id'] = Auth::id();
				$requestedData['status'] = 1;
				$requestedData['created_at'] = now();
				$requestedData['updated_at'] = now();
				
				// Save to the database
				PickupRequest::create($requestedData);
				
				DB::commit();

				return redirect()->back()->with('success', 'Pickup request created successfully.');
			} catch (\Exception $e) {
				DB::rollback(); 
				return redirect()->back()->with('error', 'An unexpected error occurred. Please try again.');
			}
		} 
		
		public function cancelPickupRequest($pickupId)
		{
			DB::beginTransaction();
			
			try {
				// Validate if the shipping company exists
				$pickupRequest = PickupRequest::find($pickupId);
				if (!$pickupRequest) {
					return redirect()->back()->with('error', 'Pickup Request not found.');
				}
				
				// Validate if the shipping company exists
				$shippingCompany = ShippingCompany::where('status', 1)->where('id', 2)->first();
				if (!$shippingCompany) {
					return redirect()->back()->with('error', 'Shipping company not found or inactive.');
				}
				
				// Call the external service to create a pickup request
				$data = $this->delhiveryService->cancelPickupRequest($pickupRequest->pickup_id, $shippingCompany); 
				
				// Handle API response errors
				if (!($data['success'] ?? false)) {
					$errorMsg = $data['response']['errors'][0]['message'] 
								?? ($data['response']['error']['message'] ?? 'An error occurred.');
					return redirect()->back()->with('error', $errorMsg);
				}

				// Ensure the response contains success confirmation
				if (isset($data['response']['success']) && !$data['response']['success']) { 
					return redirect()->back()->with('error', $data['response']['error']['message'] ?? 'An error occurred.');
				}
				
				// Extract response data
				$responseMsg = $data['response']['data']['message'] ?? 'Cancelled pickup request successfully';
				 
				$pickupRequest->update(['status' => 0, 'updated_at' => now()]);  
				DB::commit();

				return redirect()->back()->with('success', $responseMsg);
			} catch (\Exception $e) {
				DB::rollback(); 
				return redirect()->back()->with('error', 'An unexpected error occurred. Please try again.');
			}
		} 
	}
