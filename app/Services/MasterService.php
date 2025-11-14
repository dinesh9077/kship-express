<?php 
	namespace App\Services;
	use App\Models\ShippingCompany;
	use App\Models\Role;
	use Illuminate\Support\Facades\Http;
  
	class MasterService
	{ 
		public function getShippingCompanies($status = null) 
		{
			$query = ShippingCompany::query();
			if($status)
			{
				$query->where('status', $status);
			}
			return $query->get();
		}
		
		public function getShippingCompaniesById($shippingCompanyId = null) 
		{
			$query = ShippingCompany::where('id', $shippingCompanyId)->where('status', 1); 
			return $query->first();
		}
		
		public function getRoles($status = null) 
		{
			$query = Role::query();
			if($status)
			{
				$query->where('status', $status);
			}
			return $query->get();
		}
		
		public function rechargeOrderCreate($amount) 
		{ 
			try { 
				$orderAmount = $amount * 100; // Razorpay usually expects paise
				$orderUrl = "https://api.recharge.kashishindiapvtltd.com/payments/create-order-gateway";
				
				$response = Http::withOptions(['verify' => false])
				->withHeaders([
					'Accept'       => 'application/json',
					'Content-Type' => 'application/json',
				])
				->post($orderUrl, [
					"secret_key" => "kashishindiapvtltdgatewaywithrznew",
					"user"       => "8c816dcb-766b-4c7c-bbc3-831b494966fe",
					"amount"     => $orderAmount,
				]);

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
					'response' => ['error' => 'Unable to connect to order service.'],
					'message'  => $e->getMessage()
				];
			} 
		}
 
		public function getRechargeStatus($orderId)
		{
			try { 
				$orderUrl = "https://api.recharge.kashishindiapvtltd.com/payments/check-payment-gateway";

				$response = Http::withOptions(['verify' => false])
					->withHeaders([
						'Accept' => 'application/json',
						'Content-Type' => 'application/json',
					])
					->post($orderUrl, [
						"secret_key" => "kashishindiapvtltdgatewaywithrznew",
						"user" => "8c816dcb-766b-4c7c-bbc3-831b494966fe",
						"id" => $orderId,
					]);

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

			} catch (\Throwable $e) {
				return [
					'success' => false,
					'response' => ['error' => 'Unable to connect to order service.'],
					'message' => $e->getMessage()
				];
			}
		}

	}