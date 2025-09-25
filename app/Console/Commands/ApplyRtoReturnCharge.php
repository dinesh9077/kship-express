<?php
	namespace App\Console\Commands;
	
	use Illuminate\Console\Command;
	use App\Models\Order;
	use App\Models\Billing; // Assuming this is your debit log model
	use DB;
	
	class ApplyRtoReturnCharge extends Command
	{
		protected $signature = 'order:apply-rto-charge';
		protected $description = 'Apply RTO return charges on eligible orders';
		
		public function handle()
		{
			$eligibleOrders = Order::with('user')
			->whereIn('status_courier', ['rto', 'rto delivered', 'rto in transit', 'rto lost', 'rto damaged'])
            ->whereNull('rto_charge_applied')
            ->get();
		 
			if ($eligibleOrders->isEmpty()) {
				$this->info("No eligible orders found.");
				return;
			}
			
			DB::beginTransaction();
			try {
				foreach ($eligibleOrders as $order) 
				{ 
					$totalShippingCharge = $order->shipping_charge ?? 0; 
					$codCharge = $order->cod_charges ?? 0;
					
					$rtoCharge = $totalShippingCharge - $codCharge;
					Billing::insert([
						'user_id'         => $order->user_id,
						'billing_type'    => "Order",
						'billing_type_id' => $order->id,
						'transaction_type'=> 'debit',
						'amount'          => $rtoCharge,
						'note'            => "Debit entry for RTO return charges on AWB: {$order->awb_number}",
						'created_at'      => now(),
						'updated_at'      => now()
					]);
					
					$order->user->decrement('wallet_amount', $rtoCharge);
					
					Billing::insert([
						'user_id'         => $order->user_id,
						'billing_type'    => "Order",
						'billing_type_id' => $order->id,
						'transaction_type'=> 'credit',
						'amount'          => $codCharge,
						'note'            => "COD RTO return credited for AWB: {$order->awb_number}",
						'created_at'      => now(),
						'updated_at'      => now()
					]);
					
					$order->user->increment('wallet_amount', $codCharge);
					 
					// Mark charge applied
					$order->rto_charge_applied = true;
					$order->rto_charge = $rtoCharge;
					$order->save();
					
					$this->info("Applied RTO charge of ₹{$rtoCharge} for Order #{$order->id}");
				}
				
				DB::commit();
				} catch (\Exception $e) {
				DB::rollBack();
				$this->error("Error: " . $e->getMessage());
			}
		}
	}
