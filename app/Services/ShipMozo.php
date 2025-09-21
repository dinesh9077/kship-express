<?php 
	namespace App\Services;

	use App\Models\ShippingCompany;
	use App\Models\CourierWarehouse;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Http;
	use Auth;
	use Illuminate\Support\Facades\Storage;
	
	class ShipMozo
	{  
		public function httpHeader($shippingCompany)
		{
			return [ 
				'public-key' => $shippingCompany->api_key,
				'private-key' => $shippingCompany->secret_key,
				'Content-Type' => 'application/json',
				'Accept' => 'application/json'
			];
		}
		
		public function createWarehouse($courierWarehouse, $shippingCompany)
		{ 
			try {
				$pickup_address = implode(' ', [
					trim($courierWarehouse->address),
					trim($courierWarehouse->city),
					trim($courierWarehouse->state),
					trim($courierWarehouse->zip_code)
				]) . ', ' . trim($courierWarehouse->country);
				
			  
				$requestBody = [
					"address_title"      => trim($courierWarehouse->warehouse_name),
					"name"               => trim($courierWarehouse->contact_name),
					"phone"              => trim($courierWarehouse->contact_number),
					"alternate_phone"    => "",
					"email"              => trim($courierWarehouse->contact_email),
					"address_line_one"   => $pickup_address,
					"address_line_two"   => "",
					"pin_code"           => trim($courierWarehouse->zip_code)
				]; 
			
				$baseUrl = rtrim($shippingCompany->url ?? '', '/');  
 
				$response = Http::withHeaders(
					$this->httpHeader($shippingCompany)
				)
				->withOptions(['verify' => false])
				->post($baseUrl . '/create-warehouse', $requestBody);

				if ($response->successful()) {
					return [
						'success'  => true, 
						'request'  => $requestBody, 
						'response' => $response->json(),
					];
				}

				return [
					'success'  => false, 
					'request'  => $requestBody,
					'response' => json_decode($response->body(), true),
				];

			} catch (\Throwable $e) { 

				return [
					'success'  => false,
					'request'  => $requestBody,
					'response' => ['error' => 'Unable to connect to pincode service.'],
					'message'  => $e->getMessage(),
				];
			} 
		}
		  
		public function pincodeService($request, $shippingCompany)
		{  
			try {
				$baseUrl = rtrim($shippingCompany->url ?? '', '/');  

				$requestBody = [
					"pickup_pincode"   => $request->pickup_code,
					"delivery_pincode" => $request->delivery_code,
				];

				$response = Http::withHeaders(
					$this->httpHeader($shippingCompany)
				)
				->withOptions(['verify' => false])
				->post($baseUrl . '/pincode-serviceability', $requestBody);

				if ($response->successful()) {
					return [
						'success'  => true, 
						'response' => $response->json(),
					];
				}

				return [
					'success'  => false, 
					'response' => json_decode($response->body(), true),
				];

			} catch (\Throwable $e) { 

				return [
					'success'  => false,
					'response' => ['error' => 'Unable to connect to pincode service.'],
					'message'  => $e->getMessage(),
				];
			}
		}

		public function rateCaculator($requestData, $shippingCompany)
		{  	  
			try {
			
				$dimensions = []; 
				if (!empty($requestData['qty'])) {
					$conversionFactors = [ 
						"cm"   => 1
					]; 
					
					$factor = $conversionFactors[$requestData['dimension_type']] ?? 1; 
					foreach ($requestData['qty'] as $index => $qty) {
						$dimensions[] = [
							"no_of_box" => $qty ?? 0,
							"length" => ($requestData['length'][$index] ?? 0) * $factor,
							"width"  => ($requestData['width'][$index] ?? 0) * $factor,
							"height" => ($requestData['height'][$index] ?? 0) * $factor,
						];
					}
				}
				
				$codAmount = strtolower($requestData['payment_type']) === "prepaid" ? '' : ($requestData['cod_amount'] ?? ''); 
				$requestBody = [
					"order_id"        	=> "", 
					"pickup_pincode" 	=> $requestData['pickup_code'] ?? '',
					"delivery_pincode" 	=> $requestData['delivery_code'] ?? '', 
					"payment_type"    	=> $requestData['payment_type'],
					"shipment_type"  	=> "FORWARD",
					"order_amount"   	=> $requestData['inv_amount'] ?? 0,
					"type_of_package"	=> count($dimensions) > 1 ? "MPS" : "SPS",
					"rov_type"        	=> $requestData['rov_type'] ?? '',
					"cod_amount"      	=> $codAmount,
					"weight"          	=> $requestData['weight'] * 1000,
					"dimensions"        => $dimensions,
				]; 
			
				$baseUrl = rtrim($shippingCompany->url ?? '', '/');  
 
				$response = Http::withHeaders(
					$this->httpHeader($shippingCompany)
				)
				->withOptions(['verify' => false])
				->post($baseUrl . '/rate-calculator', $requestBody);

				if ($response->successful()) {
					return [
						'success'  => true, 
						'request'  => $requestBody, 
						'response' => $response->json(),
					];
				}

				return [
					'success'  => false, 
					'request'  => $requestBody, 
					'response' => json_decode($response->body(), true),
				];

			} catch (\Throwable $e) {  
				return [
					'success'  => false,
					'request'  => $requestBody, 
					'response' => ['error' => 'Unable to connect to pincode service.'],
					'message'  => $e->getMessage(),
				];
			} 
		}
		
		public function freightEstimate($order, $delhivery)
		{  	
			$totalWeightInKg = $order->orderItems->sum(fn($item) => $item->dimensions['weight'] ?? 0);
			$totalInvoiceAmount = $order->orderItems->sum(fn($item) => $item->amount ?? 0);
			$sourcePin = $order->warehouse->zip_code ?? '';
			$consigneePin = $order->customerAddress->zip_code ?? '';
			
			$conversionFactors = [
				"ft"   => 30.48,
				"inch" => 2.54,
				"cm"   => 1 // No conversion needed
			];
			
			// Determine the correct conversion factor
			$factor = $conversionFactors[$order->dimension_type] ?? 1;
				
			$dimensions = $order->orderItems->map(fn($orderItem) => [
				"length_cm" => ($orderItem->dimensions['length'] ?? 0) * $factor,
				"width_cm" => ($orderItem->dimensions['width'] ?? 0) * $factor,
				"height_cm" => ($orderItem->dimensions['height'] ?? 0) * $factor,
				"box_count" => $orderItem->quantity ?? 0,
			])->toArray();
			 
			$requestBody = [
				"dimensions" => $dimensions,
				"weight_g" => $totalWeightInKg > 0 ? ($totalWeightInKg * 1000) : 0,
				"cheque_payment" => false,
				"source_pin" => $sourcePin,
				"consignee_pin" => $consigneePin,
				"payment_mode" => $order->order_type, 
				"inv_amount" => $totalInvoiceAmount,
				"freight_mode" => strtolower($order->freight_mode),
				"rov_insurance" => $order->insurance_type == 1 ? false : true
			];
			if($order->order_type === "cod")
			{
				$requestBody['cod_amount'] = $order->cod_amount;
			}
			$baseUrl = rtrim($delhivery->url ?? '', '/');  
            $response = Http::withHeaders([ 
				'Authorization' => 'Bearer '.$delhivery->api_key,
				'Content-Type' => 'application/json',
			])
			->withOptions([
				'verify' => false,
			])
			->post($baseUrl.'/freight/estimate', $requestBody);
		  
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
		
		
		
		public function manifest($order, $requestData, $delhivery)
        {  	   
        	$user = Auth::user()->load('userKyc'); 
			$pancardNumber = $user->userKyc->pancard ?? $user->pancard_number ?? null;
 
        	$totalWeightInGm = $order->orderItems->sum(fn($item) => $item->dimensions['weight'] ?? 0) * 1000;
        	
        	$consigneeName = implode(' ', [
        		$order->customer->first_name ?? '',
        		$order->customer->last_name ?? ''
        	]);
        
        	$dropOffLocation = [
        		"consignee_name" => $consigneeName,
        		"address" => $order->customerAddress->address ?? '',
        		"city" => $order->customerAddress->city ?? '',
        		"state" => $order->customerAddress->state ?? '',
        		"zip" => $order->customerAddress->zip_code ?? '',
        		"phone" => $order->customer->mobile ?? '',
        		"email" => $order->customer->email ?? '',
        	];
        
        	$shipmentDetails = $order->orderItems->map(fn($item) => [
        		"order_id" => "{$item->id}",
        		"box_count" => $item->quantity,
        		"description" => $item->product_discription,
        		"weight" => ($item->dimensions['weight'] ?? 0) * 1000, 
        		"waybills" => [], 
        		"master" => 0
        	])->toArray();
        
        	$dimensions = $order->orderItems->map(fn($item) => [
        		"box_count" => $item->quantity,
        		"length" => $item->dimensions['length'] ?? 0,
        		"width" => $item->dimensions['width'] ?? 0,
        		"height" => $item->dimensions['height'] ?? 0
        	])->toArray();
        
        	$invoices = [
        		[
        			"inv_num" => $order->invoice_no,
        			"inv_amt" => $order->invoice_amount,
        			"inv_qr_code" => '',
        			"ewaybill" => $order->ewaybillno ?? ''
        		]
        	];
        
        	$billingAddress = [
        		"name" => $user->name,
        		"consignor" => $user->name,
        		"company" => $user->company_name,
        		"address" => $user->address,
        		"city" => $user->city,
        		"state" => $user->state,
        		"pin" => $user->zip_code,
        		"phone" => $user->mobile,
        		"pan_number" => $pancardNumber ?? null
        	];
        
        	$docData = [
        		[
        			"doc_type" => "INVOICE_COPY",
        			"doc_meta" => [
        				"invoice_num" => [$order->invoice_no]
        			]
        		]
        	]; 
        
        	$requestBody = [
        		'lrn' => '',
        		'pickup_location_name' => $order->warehouse->warehouse_name ?? '',
        		'payment_mode' => $order->order_type,
        		'cod_amount' => $order->cod_amount,
        		'weight' => (float) $totalWeightInGm,
        		'dropoff_location' => json_encode($dropOffLocation),  
        		'rov_insurance' => 0,
        		'invoices' => json_encode($invoices),
        		'shipment_details' => json_encode($shipmentDetails), 
        		'dimensions' => json_encode($dimensions),  
        		'doc_data' => json_encode($docData),   
        		'freight_mode' => strtolower($order->freight_mode),
        		'fm_pickup' => 1,
        		'billing_address' => json_encode($billingAddress)
        	];
        
        	// API Base URL
        	$baseUrl = rtrim($delhivery->url ?? '', '/');  
        
			// Start the HTTP request
        	$request = Http::withHeaders([ 
        		'Authorization' => 'Bearer ' . $delhivery->api_key,
        	])->withOptions([
        		'verify' => false,
        	]);
        
        	// Attach Images One by One
        	if (!empty($order->invoice_document)) {  
                $filePath = 'orders/' . $order->id . '/' . $order->invoice_document[0];
            
                // Check if the file exists in storage
                if (!Storage::disk('public')->exists($filePath)) {
                    return [
                        'success' => false,
                        'message' => 'invoice document not found in storage.'
                    ];
                }
            
                // Get the full path of the file
                $fullPath = Storage::disk('public')->path($filePath);
            
                // Attach the file to the request
                $request = $request->attach(
                    'doc_file', file_get_contents($fullPath), basename($fullPath)
                );
            }
 
        
        	// Send the request
        	$response = $request->asMultipart()->post($baseUrl . '/manifest', $requestBody);
        
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
        		'client_warehouse' => $courierWarehouse->warehouse_name,
        		'pickup_date' => $requestData['pickup_date'] ?? '',
        		'start_time' => (string)$startTime,
        		'end_time' => (string)$endTime,
        		'expected_package_count' => (int)$requestData['expected_package_count'] ?? 0,
        	];
			 
        	// API Base URL
        	$baseUrl = rtrim($delhivery->url ?? '', '/');  
        
			// Start the HTTP request
        	$request = Http::withHeaders([ 
        		'Authorization' => 'Bearer ' . $delhivery->api_key,
        	])->withOptions([
        		'verify' => false,
        	]); 
        	// Send the request
        	$response = $request->post($baseUrl . '/pickup_requests', $requestBody);
        
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
		
		public function cancelPickupRequest($pickupId, $delhivery)
        {  	  
			 
        	// API Base URL
        	$baseUrl = rtrim($delhivery->url ?? '', '/');  
        
			// Start the HTTP request
        	$request = Http::withHeaders([ 
        		'Authorization' => 'Bearer ' . $delhivery->api_key,
        	])->withOptions([
        		'verify' => false,
        	]); 
        	// Send the request
        	$response = $request->delete($baseUrl . '/pickup_requests/'.$pickupId);
        
        	// Handle the response
        	if ($response->successful()) {
        		return [
        			'success' => true,  
        			'response' => $response->json(),
        		];
        	} 
        	
        	// If the response was unsuccessful, return an error response
        	return [
        		'success' => false,  
        		'response' => json_decode($response->body(), true),
        	];  
        }
		
		public function manifestStatus($jobId, $delhivery)
		{  
			$baseUrl = rtrim($delhivery->url ?? '', '/');  

			// Make API request
			$response = Http::withHeaders([
				'Authorization' => 'Bearer ' . $delhivery->api_key,
				'Accept' => 'application/json',
			])->withOptions([
				'verify' => false, // Disable SSL verification (only if needed)
			])->get("$baseUrl/manifest", ['job_id' => $jobId]);

			// Check if request was successful
			if ($response->successful()) {
				return [
					'success' => true,
					'response' => $response->json(),
				];
			}

			// Return error response if failed
			return [
				'success' => false,
				'response' => json_decode($response->body(), true),
			];
		}
		 
		public function cancelShipmentByLrNo($lrNo, $delhivery)
		{  
			try {
				$baseUrl = rtrim($delhivery->url ?? '', '/');

				// Make API request
				$response = Http::withHeaders([
					'Authorization' => 'Bearer ' . $delhivery->api_key,
					'Accept' => 'application/json',
				])->withOptions([
					'verify' => false, 
				])->delete("$baseUrl/lrn/cancel/$lrNo");

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
		
		public function trackOrderByLrNo($lrNo, $delhivery)
		{  
			try {
				$baseUrl = rtrim($delhivery->url ?? '', '/');

				// Make API request
				$response = Http::withHeaders([
					'Authorization' => 'Bearer ' . $delhivery->api_key,
					'Accept' => 'application/json',
				])->withOptions([
					'verify' => false, 
				])->get("$baseUrl/lrn/track", ['lrnum' => $lrNo]);

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
		
		public function shippingLableByLrNo($lrNo, $delhivery)
		{  
			try {
				$baseUrl = rtrim($delhivery->url ?? '', '/');

				// Make API request
				$response = Http::withHeaders([
					'Authorization' => 'Bearer ' . $delhivery->api_key,
					'Accept' => 'application/json',
				])->withOptions([
					'verify' => false, 
				])->get("$baseUrl/label/get_urls/std/$lrNo");

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
		
		public function getLabelImage($baseUrl)
		{  
			try {
				  
				// Make API request
				$response = Http::get($baseUrl);

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
		
		public function waybillCopyByLrNo($lrNo, $delhivery)
		{  
			try {
				$baseUrl = rtrim($delhivery->url ?? '', '/');

				// Make API request
				$response = Http::withHeaders([
					'Authorization' => 'Bearer ' . $delhivery->api_key,
					'Accept' => 'application/json',
				])->withOptions([
					'verify' => false, 
				])->get("$baseUrl/lr_copy/print/$lrNo");

				// Check if request was successful
				if ($response->successful()) {
					return [
						'success' => true,
						'response' => $response->body(),
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
