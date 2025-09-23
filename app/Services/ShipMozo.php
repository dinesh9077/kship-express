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
					"pickup_pincode"   => $request['pickup_code'],
					"delivery_pincode" => $request['delivery_code'],
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
					"type_of_package"	=> $requestData['type_of_package'] == 2 ? "B2B" : "SPS",
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
		
		public function orderRateCaculator($order, $shippingCompany)
		{  	  
			try {  
				$dimensions = []; 
				if (!empty($order->orderItems->isNotEmpty()) && $order->weight_order == 2) {
					$conversionFactors = [ 
						"cm"   => 1
					]; 
					
					$factor = $conversionFactors[$order->dimension_type] ?? 1; 
					foreach ($order->orderItems as $index => $orderItem) {
						$dimensions[] = [
							"no_of_box" => $orderItem->quantity ?? 0,
							"length" => ($orderItem->dimensions['length'] ?? 0) * $factor,
							"width"  => ($orderItem->dimensions['width'] ?? 0) * $factor,
							"height" => ($orderItem->dimensions['height'] ?? 0) * $factor,
						];
					}
				}
				else
				{
					$quantity = $order->orderItems->sum(fn($q) => $q->quantity);

					$dimensions[] = [
						"no_of_box" => $quantity ?? 0,
						"length"    => $order->length,
						"width"     => $order->width,
						"height"    => $order->height,
					];

				}
				
				$codAmount = strtolower($order->order_type) === "prepaid" ? '' : ($order->cod_amount ?? ''); 
				$requestBody = [
					"order_id"        	=> "", 
					"pickup_pincode" 	=> optional($order->warehouse)->zip_code ?? '',
					"delivery_pincode" 	=> optional($order->customerAddress)->zip_code ?? '', 
					"payment_type"    	=> strtoupper($order->order_type),
					"shipment_type"  	=> "FORWARD",
					"order_amount"   	=> $order->invoice_amount,
					"type_of_package"	=> $order->weight_order == 2 ? "B2B" : "SPS",
					"rov_type"        	=> $order->insurance_type == 1 ? 'ROV_OWNER' : 'ROV_CARRIER',
					"cod_amount"      	=> $codAmount,
					"weight"          	=> $order->weight * 1000,
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
		
		public function pushOrder($order, $requestData, $shippingCompany)
        {  	   
			try {  
				//$user = Auth::user()->load('userKyc'); 
				//$pancardNumber = $user->userKyc->pancard ?? $user->pancard_number ?? null;
	
				$totalWeightInGm = $requestData['applicable_weight'] * 1000;
				
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
	  
				$productDetail = $order->orderItems->map(function($item) {
					return [
						"name"             => $item->product_name,
						"sku_number"       => $item->sku_number,
						"quantity"         => $item->quantity,
						"discount"         => "",
						"hsn"              => $item->hsn_number,
						"unit_price"       => $item->amount,
						"product_category" => $item->product_category,
					];
				})->toArray(); 
				
				$dimensions = $order->orderItems->map(function($item) {
					return [
						"no_of_box"   => $item->dimensions['no_of_box'] ?? 0,
						"length"      => $item->dimensions['length'] ?? 0,
						"width"       => $item->dimensions['width'] ?? 0,
						"height"      => $item->dimensions['height'] ?? 0,
					];
				})->toArray(); 
 
				$codAmount = strtolower($order->order_type) === "prepaid" ? '' : ($order->cod_amount ?? ''); 
				
				$requestBody = [
					"order_id"                     => $order->order_prefix,
					"order_date"                   => $order->order_date,
					"order_type"                   => "ESSENTIALS",
					"consignee_name"               => $consigneeName,
					"consignee_phone"              => $order->customer->mobile ?? '',
					"consignee_alternate_phone"    => "",
					"consignee_email"              => $order->customer->email ?? '',
					"consignee_address_line_one"   => $customer_address ?? '',
					"consignee_address_line_two"   => "",
					"consignee_pin_code"           => $order->customerAddress->zip_code ?? '',
					"consignee_city"               => $order->customerAddress->city ?? '',
					"consignee_state"              => $order->customerAddress->state ?? '',
					"product_detail"               => $productDetail,
					"payment_type"                 => strtoupper($order->order_type),
					"cod_amount"                   => $codAmount ?? "",
					"shipping_charges"             => "",
					"weight"                       => $totalWeightInGm, 
					"warehouse_id"                 => $order->warehouse->shipping_id ?? '',
					"gst_ewaybill_number"          => $order->ewaybillno,
					"gstin_number"                 => $order->customer->gst_number ?? ''
				]; 
				
				if($order->weight_order == 2)
				{
					$requestBody = array_merge(
						$requestBody,
						[
							"type_of_package" => "B2B",
							"dimensions" => $dimensions
						]
					);
				}
				else
				{
					$requestBody = array_merge(
						$requestBody,
						[
							"length" => $order->length,
							"width"  => $order->width,
							"height" => $order->height,
						]
					);

				}	
				 
				// API Base URL
				$baseUrl = rtrim($shippingCompany->url ?? '', '/');  
				 
				// Start the HTTP request 
				$response = Http::withHeaders(
					$this->httpHeader($shippingCompany)
				)
				->withOptions(['verify' => false])
				->post($baseUrl . '/push-order', $requestBody);
				 
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
			} catch (\Throwable $e) {  
				return [
					'success'  => false,
					'request'  => $requestBody, 
					'response' => ['error' => 'Unable to connect to pincode service.'],
					'message'  => $e->getMessage(),
				];
			} 
        }
		 
		public function assignCourier($orderId, $courierId, $shippingCompany)
		{  
			try{
				// API Base URL
				$baseUrl = rtrim($shippingCompany->url ?? '', '/');  
				 
				$requestBody = [
					'order_id' => $orderId,
					'courier_id' => $courierId
				];
				
				// Start the HTTP request 
				$response = Http::withHeaders(
					$this->httpHeader($shippingCompany)
				)
				->withOptions(['verify' => false])
				->post($baseUrl . '/assign-courier', $requestBody);
				 
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
			} catch (\Throwable $e) {  
				return [
					'success'  => false,
					'request'  => $requestBody, 
					'response' => ['error' => 'Unable to connect to pincode service.'],
					'message'  => $e->getMessage(),
				];
			} 
		}
		  
		public function trackOrder($awbNumber, $shippingCompany)
		{  
			try{
				// API Base URL
				$baseUrl = rtrim($shippingCompany->url ?? '', '/');  
				 
				$requestBody = [
					'awb_number' => $awbNumber
					//'awb_number' => 'SF2079425818SPZ'
				];
				
				// Start the HTTP request 
				$response = Http::withHeaders(
					$this->httpHeader($shippingCompany)
				)
				->withOptions(['verify' => false])
				->get($baseUrl . '/track-order', $requestBody);
				 
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
			} catch (\Throwable $e) {  
				return [
					'success'  => false,
					'request'  => $requestBody, 
					'response' => ['error' => 'Unable to connect to pincode service.'],
					'message'  => $e->getMessage(),
				];
			} 
		}
		
		public function cancelShipment($order, $shippingCompany)
		{  
			
			try{
				// API Base URL
				$baseUrl = rtrim($shippingCompany->url ?? '', '/');  
				 
				$requestBody = [
					'order_id' => $order->shipment_id,
					'awb_number' => $order->awb_number,
				];
				 
				// Start the HTTP request 
				$response = Http::withHeaders(
					$this->httpHeader($shippingCompany)
				)
				->withOptions(['verify' => false])
				->post($baseUrl . '/cancel-order', $requestBody);
				  
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
			} catch (\Throwable $e) {  
				return [
					'success'  => false,
					'request'  => $requestBody, 
					'response' => ['error' => 'Unable to connect api.'],
					'message'  => $e->getMessage(),
				];
			}
		} 
		
		public function getOrderDetails($orderPrefrenceNo, $shippingCompany)
		{  
			try {
			
				$baseUrl = rtrim($shippingCompany->url ?? '', '/');  
				  
				// Start the HTTP request 
				$response = Http::withHeaders(
					$this->httpHeader($shippingCompany)
				)
				->withOptions(['verify' => false])
				->get($baseUrl . '/get-order-detail/'.$orderPrefrenceNo);
				 
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
			} catch (\Throwable $e) {  
				return [
					'success'  => false, 
					'response' => ['error' => 'Unable to connect api.'],
					'message'  => $e->getMessage(),
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
