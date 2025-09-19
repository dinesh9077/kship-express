<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\DelhiveryService;
use App\Services\DelhiveryB2CService;
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
	 
	protected $delhiveryService; 
	public function __construct(DelhiveryService $delhiveryService, DelhiveryB2CService $delhiveryB2CService)
	{
		parent::__construct();
		$this->delhiveryService = $delhiveryService;
		$this->delhiveryB2CService = $delhiveryB2CService;
	}
		
    public function handle()
    {
        $orders = Order::with('shippingCompany')
            ->whereNotIn('status_courier', ['delivered', 'new', 'cancelled'])
            ->whereNotNull('awb_number')
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

        if ($shippingCompany->id === 2 && $order->lr_no) {
            $trackingResponse = $this->delhiveryService->trackOrderByLrNo($order->lr_no, $shippingCompany);
            
            return $trackingResponse['response']['data']['status'] ?? $order->status_courier;
        }
		
        if ($shippingCompany->id === 3 && $order->awb_number) {
            $trackingResponse = $this->delhiveryB2CService->trackOrderByAwbNumber($order->awb_number, $shippingCompany);
			 
            if ((isset($trackingResponse['response']['ShipmentData']) && !empty($trackingResponse['response']['ShipmentData'])))
			{
				return $trackingResponse['response']['ShipmentData'][0]['Shipment']['Status']['Status'] ?? $order->status_courier;
			}
        }
        
        return $order->status_courier;
    }
}
