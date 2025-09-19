<?php 
	namespace App\Services;

	use App\Models\ShippingCompany;
	use App\Models\CourierWarehouse;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Http;
	use Auth;
	use Illuminate\Support\Facades\Storage;
	class DelhiveryB2CService
	{    
		public function createWarehouse($courierWarehouse, $delhivery)
		{
			$pickup_address = implode(' ', [
				trim($courierWarehouse->address),
				trim($courierWarehouse->city),
				trim($courierWarehouse->state),
				trim($courierWarehouse->zip_code)
			]) . ', ' . trim($courierWarehouse->country);
			  
			$requestBody = [
				"phone" => trim($courierWarehouse->contact_number),
				"city" => trim($courierWarehouse->city),
				"name" => trim($courierWarehouse->warehouse_name),
				"pin" => trim($courierWarehouse->zip_code),
				"address" => $pickup_address,
				"country" => trim($courierWarehouse->country),
				"email" => "",
				"registered_name" => "",
				"return_address" => $pickup_address,
				"return_pin" => trim($courierWarehouse->zip_code),
				"return_city" => trim($courierWarehouse->city),
				"return_state" => trim($courierWarehouse->state),
				"return_country" => trim($courierWarehouse->country)
			]; 
			 
			$baseUrl = rtrim($delhivery->url ?? '', '/');   
			
            $response = Http::withHeaders([ 
				'Authorization' => 'Token '.$delhivery->api_key,
				'Content-Type' => 'application/json', 
			])
			->withOptions([
				'verify' => false,
			])
			->post($baseUrl.'/api/backend/clientwarehouse/create/', $requestBody);
			 
			// Handle the response
			$responseBody = $response->body();

			// Convert XML to PHP Array
			$xml = simplexml_load_string($responseBody, "SimpleXMLElement", LIBXML_NOCDATA);
			$json = json_encode($xml);
			$dataArray = json_decode($json, true);
			
			if ($response->successful()) {
				return [
					'success' => true,
					'request' => $requestBody,
					'response' => $dataArray,
				];
			}
 
			return [
				'success' => false,
				'request' => $requestBody,
				'response' => $dataArray,
			];  
		}
		
		public function updateWarehouse($courierWarehouse, $delhivery)
		{
			$pickup_address = implode(' ', [
				trim($courierWarehouse->address),
				trim($courierWarehouse->city),
				trim($courierWarehouse->state),
				trim($courierWarehouse->zip_code)
			]) . ', ' . trim($courierWarehouse->country);
			 
			$requestBody = [ 
				"name" => trim($courierWarehouse->warehouse_name),
				"registered_name" => '',
				"address" => $pickup_address
			];
			
			$baseUrl = rtrim($delhivery->url ?? '', '/');  
            $response = Http::withHeaders([ 
				'Authorization' => 'Token '.$delhivery->api_key,
				'Content-Type' => 'application/json',
			])
			->withOptions([
				'verify' => false,
			])
			->post($baseUrl.'/api/backend/clientwarehouse/edit/', $requestBody);
		  
			$responseBody = $response->body();

			// Convert XML to PHP Array
			$xml = simplexml_load_string($responseBody, "SimpleXMLElement", LIBXML_NOCDATA);
			$json = json_encode($xml);
			$dataArray = json_decode($json, true);
			
			if ($response->successful()) {
				return [
					'success' => true,
					'request' => $requestBody,
					'response' => $dataArray,
				];
			}

			// If the response was unsuccessful, return an error response
			return [
				'success' => false,
				'request' => $requestBody,
				'response' => $dataArray,
			];  
		}
		
		public function pincodeService($pinCode, $delhivery)
		{  
			$baseUrl = rtrim($delhivery->url ?? '', '/');  
			 
            $response = Http::withHeaders([  
				'Content-Type' => 'application/json',
			])
			->withOptions([
				'verify' => false,
			])
			->get("{$baseUrl}/c/api/pin-codes/json/?token={$delhivery->api_key}&filter_codes={$pinCode}");
		  
			// Handle the response 
			if ($response->successful()) {
				return [
					'success' => $response->json()['delivery_codes'] ? true : false,  
					'response' => $response->json(),
				];
			}

			// If the response was unsuccessful, return an error response
			return [
				'success' => false,  
				'response' => json_decode($response->body(), true),
			];  
		}
		
		public function freightEstimate($order, $delhivery)
		{  	
			$totalWeightInKg = $order->orderItems->sum(fn($item) => $item->dimensions['weight'] ?? 0);
			$totalInvoiceAmount = $order->orderItems->sum(fn($item) => $item->amount ?? 0);
			$sourcePin = $order->warehouse->zip_code ?? '';
			$consigneePin = $order->customerAddress->zip_code ?? '';
			
			$queryParam = [
				'md' => $order->shipping_mode == 'Surface' ? 'S' : 'E',
				'ss' => 'Delivered',
				'd_pin' => $sourcePin,
				'o_pin' => $consigneePin,
				'cgm' => ($totalWeightInKg ?? 0) * 1000,
				'pt' => $order->order_type == 'cod' ? 'COD' : 'Pre-paid',
				'cod' => $order->order_type == 'cod' ? $order->cod_amount : 0,
			];
			 
			$url = rtrim($delhivery->url, '/') . "/api/kinko/v1/invoice/charges/.json?" . http_build_query($queryParam);
  
            $response = Http::withHeaders([ 
				'Authorization' => 'Token '.$delhivery->api_key,
				'Content-Type' => 'application/json',
			])
			->withOptions([
				'verify' => false,
			])
			->get($url);
		  
			// Handle the response
			if ($response->successful()) {
				return [
					'success' => true, 
					'request' => $queryParam, 
					'response' => $response->json(),
				];
			}

			// If the response was unsuccessful, return an error response
			return [
				'success' => false, 
				'request' => $queryParam, 
				'response' => json_decode($response->body(), true),
			];  
		}
		
		public function rateCaculator($requestData, $delhivery)
		{  	    
			$queryParam = [
				'md' => 'S',
				'ss' => 'Delivered',
				'd_pin' => $requestData['pickup_code'] ?? '',
				'o_pin' => $requestData['delivery_code'] ?? '',
				'cgm' => ($requestData['weight'] * 1000) ?? 0,
				'pt' => $requestData['payment_type'] == 'cod' ? 'COD' : 'Pre-paid',
				'cod' => $requestData['payment_type'] == 'cod' ? $requestData['cod_amount'] : 0,
			];
			 
			$url = rtrim($delhivery->url, '/') . "/api/kinko/v1/invoice/charges/.json?" . http_build_query($queryParam);
  
            $response = Http::withHeaders([ 
				'Authorization' => 'Token '.$delhivery->api_key,
				'Content-Type' => 'application/json',
			])
			->withOptions([
				'verify' => false,
			])
			->get($url);
		  
			// Handle the response
			if ($response->successful()) {
				return [
					'success' => true, 
					'request' => $queryParam, 
					'response' => $response->json(),
				];
			}

			// If the response was unsuccessful, return an error response
			return [
				'success' => false, 
				'request' => $queryParam, 
				'response' => json_decode($response->body(), true),
			];   
		}
		
		public function manifest($order, $requestData, $delhivery)
        {  	   
        	$user = Auth::user(); 
        	$totalWeightInGm = $order->orderItems->sum(fn($item) => $item->dimensions['weight'] ?? 0);
        	
        	$consigneeName = implode(' ', [
        		$order->customer->first_name ?? '',
        		$order->customer->last_name ?? ''
        	]);
			
			$customerAddress = $order->customerAddress ?? null; 
			$customer_address = $customerAddress 
			? implode(' ', array_filter([
				trim($customerAddress->address ?? ''),
				trim($customerAddress->city ?? ''),
				trim($customerAddress->state ?? ''),
				trim($customerAddress->zip_code ?? '')
			])) . (isset($customerAddress->country) ? ', ' . trim($customerAddress->country) : '')
			: '';

			$warehouse = $order->warehouse ?? null; 
			$pickup_address = $warehouse 
			? implode(' ', array_filter([
				trim($warehouse->address ?? ''),
				trim($warehouse->city ?? ''),
				trim($warehouse->state ?? ''),
				trim($warehouse->zip_code ?? '')
			])) . (isset($warehouse->country) ? ', ' . trim($warehouse->country) : '')
			: '';
			
			$product_name = ["Order Id-{$order->order_prefix}"]; 
			foreach ($order->orderItems ?? [] as $orderItems) {
				$product_name[] = "Product discription - {$orderItems->product_discription}";
			} 
			$productNamesString = implode(', ', array_unique($product_name));
  
			$data = [
				"shipments" => [
					[
						"add" => $customer_address,
						"address_type" => "",
						"phone" => $order->customer->mobile ?? '',
						"payment_mode" => $order->order_type,
						"name" => $consigneeName,
						"pin" => $order->customerAddress->zip_code ?? '',
						"order" => $order->order_prefix,
						"consignee_gst_amount" => "",
						"integrated_gst_amount" => "",
						"ewbn" => "",
						"consignee_gst_tin" => "",
						"seller_gst_tin" => "",
						"client_gst_tin" => "",
						"hsn_code" => "",
						"gst_cess_amount" => "",
						"shipping_mode" => $order->shipping_mode,
						"client" => "XPRESSFLY",
						"tax_value" => "",
						"seller_tin" => "",
						"seller_gst_amount" => "",
						"seller_inv" => "",
						"city" => "",
						"commodity_value" => "",
						"weight" => ($totalWeightInGm * 1000),
						"return_state" => "",
						"document_number" => "",
						"od_distance" => "",
						"sales_tax_form_ack_no" => "",
						"document_type" => "",
						"seller_cst" => "",
						"seller_name" => "",
						"fragile_shipment" => "",
						"return_city" => "",
						"return_phone" => "",
						"qc" => [
							"item" => [
								[
									"images" => "",
									"color" => "",
									"reason" => "",
									"descr" => "",
									"ean" => "",
									"imei" => "",
									"brand" => "",
									"pcat" => "",
									"si" => "",
									"item_quantity" => ""
								]
							]
						],
						"shipment_height" => '',
						"shipment_width" => '',
						"shipment_length" => '',
						"category_of_goods" => "",
						"cod_amount" => $order->cod_amount,
						"return_country" => "",
						"document_date" => "",
						"taxable_amount" => "",
						"products_desc" => $productNamesString,
						"state" => "",
						"dangerous_good" => "",
						"waybill" => "",
						"consignee_tin" => "",
						"order_date" => now()->toDateString(),
						"return_add" => $pickup_address,
						"total_amount" => $order->total_amount,
						"seller_add" => $pickup_address,
						"country" => $warehouse->country ?? '',
						"return_pin" => "",
						"extra_parameters" => [
							"return_reason" => ""
						],
						"return_name" => "",
						"supply_sub_type" => "",
						"plastic_packaging" => "false",
						"quantity" => ""
					]
				],
				"pickup_location" => [
					"name" => $warehouse->warehouse_name ?? '',
					"city" => $warehouse->city ?? '',
					"pin" => $warehouse->zip_code ?? '',
					"country" => $warehouse->country ?? '',
					"phone" => $warehouse->contact_number ?? '',
					"add" => $pickup_address
				]
			];
			
			$requestBody = "format=json&data=".json_encode($data); 
			 
			$baseUrl = rtrim($delhivery->url ?? '', '/');

			$response = Http::withHeaders([
				'Authorization' => 'Token ' . $delhivery->api_key,
				'Content-Type' => 'application/json' // Ensure JSON format
			])->withOptions([
				'verify' => false
			])->withBody($requestBody, 'application/json') // Send raw JSON
			  ->post($baseUrl . '/api/cmu/create.json');
			 
        	// Handle the response
        	if ($response->successful()) {
        		return [
        			'success' => true, 
        			'request' => $requestBody, 
        			'response' => $response->json(),
        		];
        	} 
        	
        	// If the response was unsuccessful, return an error response
        	return [
        		'success' => false, 
        		'request' => $requestBody, 
        		'response' => json_decode($response->body(), true),
        	];  
        }
		
		public function createPickupRequest($requestData, $delhivery)
        {  	 
			$courierWarehouse = CourierWarehouse::find($requestData['warehouse_id']);
			$startTime = $requestData['pickup_start_time'] ? date('H:i:s', strtotime($requestData['pickup_start_time'])) : '';
			$endTime = $requestData['pickup_end_time'] ? date('H:i:s', strtotime($requestData['pickup_end_time'])) : '';
        	$requestBody = [
        		'pickup_location' => $courierWarehouse->warehouse_name,
        		'pickup_date' => $requestData['pickup_date'] ?? '',
        		'pickup_time' => (string)$startTime, 
        		'expected_package_count' => (int)$requestData['expected_package_count'] ?? 0,
        	];
			 
        	// API Base URL
        	$baseUrl = rtrim($delhivery->url ?? '', '/');  
        
			// Start the HTTP request
        	$request = Http::withHeaders([ 
        		'Authorization' => 'Token ' . $delhivery->api_key,
        		'Content-Type' => 'application/json',
        	])->withOptions([
        		'verify' => false,
        	]); 
        	// Send the request
        	$response = $request->post($baseUrl . '/fm/request/new/', $requestBody);
        
        	// Handle the response
        	if ($response->successful()) {
        		return [
        			'success' => true, 
        			'request' => $requestBody, 
        			'response' => $response->json(),
        		];
        	} 
        	
        	// If the response was unsuccessful, return an error response
        	return [
        		'success' => false, 
        		'request' => $requestBody, 
        		'response' => json_decode($response->body(), true),
        	];  
        }
		 
		public function cancelShipmentByAwbNumber($awbNumber, $delhivery)
		{   
			$baseUrl = rtrim($delhivery->url ?? '', '/');
			
			$requestBody = [
				'waybill' => $awbNumber,
				'cancellation' => "true"
			];
			
			// Make API request
			$response = Http::withHeaders([
				'Authorization' => 'Token ' . $delhivery->api_key,
				'Accept' => 'application/json',
			])->withOptions([
				'verify' => false, 
			])->post($baseUrl."/api/p/edit", $requestBody);
			
			 
			// Check if request was successful
			if ($response->successful()) {
				return [
					'success' => true,
					'response' => $response->json(),
				];
			}

			return [
				'success' => false,
				'response' => json_decode($response->body(), true),
			];
 
		}
		
		public function trackOrderByAwbNumber($awbNumber, $delhivery)
		{  
			try {
				$baseUrl = rtrim($delhivery->url ?? '', '/');

				// Make API request
				$response = Http::withHeaders([ 
					'Accept' => 'application/json',
				])->withOptions([
					'verify' => false, 
				])->get($baseUrl."/api/v1/packages/json", ['waybill' => $awbNumber, 'token' => $delhivery->api_key]);

				// Check if request was successful
				if ($response->successful()) {
					return [
						'success' => true,
						'response' => $response->json(),
					];
				}
 
				return [
					'success' => false,
					'response' => json_decode($response->body(), true),
				];

			} catch (\Exception $e) {
				  
				return [
					'success' => false,
					'response' => ['error' => 'An unexpected error occurred.'],
				];
			}
		} 
	}
