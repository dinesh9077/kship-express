<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\DelhiveryService;
use App\Services\DelhiveryB2CService;
use App\Services\ShipMozo;
use Illuminate\Support\Facades\Log;
	
class UpdateOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shipment status update command';

    /**
     * Execute the console command.
     *
     * @return int
     */
	 
	protected $shipMozo; 
	public function __construct(ShipMozo $shipMozo)
	{
		parent::__construct();
		$this->shipMozo = $shipMozo; 
	}
		
    public function handle()
    { 
		$orders = Order::with(['shippingCompany'])
		->whereNotIn('status_courier', ['delivered', 'New', 'cancelled', 'rto', 'rto delivered', 'rto in transit', 'rto lost', 'rto damaged'])
		->whereNotNull('awb_number')
		->whereHas('shippingCompany')
		->latest()
		->get(); 
		 
        $today = now()->toDateString();
        foreach ($orders as $order) {
            $newStatus = $this->getUpdatedStatus($order);

            if ($newStatus && $newStatus !== $order->status_courier) {
                $order->update(['status_courier' => strtolower($newStatus)]);
                
                if (strtolower($newStatus) === 'delivered') {
                    $order->update(['delivery_date' => $today]);
                }
            }
        }

        $this->info('Shipment statuses updated successfully.');
    }

    /**
     * Get the updated status for an order.
     *
     * @param Order $order
     * @return string|null
     */
    private function getUpdatedStatus(Order $order)
    {
        $shippingCompany = $order->shippingCompany;
        
        if (!$shippingCompany) {
            return null;
        }

        if ($shippingCompany->id === 1) {
            $trackingResponse = $this->shipMozo->trackOrder($order->awb_number, $shippingCompany); 
			if (!($trackingResponse['success'] ?? false)) {
				$errorMsg = $trackingResponse['response']['errors'][0]['message'] ?? ($trackingResponse['response']['error'] ?? 'An error occurred.');
				return $order->status_courier; 
			}  
			if ((isset($trackingResponse['response']['result']) && $trackingResponse['response']['result']) == 0)
			{ 
				return $order->status_courier; 
			} 
			
			if($trackingResponse['response']['data']['current_status'] == "Pickup Pending")
			{
				return $order->status_courier; 
			}
			
            return $trackingResponse['response']['data']['current_status'] ?? $order->status_courier;
        } 
        return $order->status_courier;
    }
}
