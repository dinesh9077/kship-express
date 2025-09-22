<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Shipping Label</title>
	</head>
	<body>
		<table style="width: 100%; margin: auto; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px; border: 1px solid black;">
			<tr>
				<td colspan="3" style="padding: 20px 10px; width: 80%; vertical-align: middle; font-size: 25px; font-weight: bold; border: 1px solid black;">
					{{ ucwords($order->warehouse->company_name ?? '') }}
				</td>
				<td colspan="2" style="padding: 0; text-align: center ; width: 20%; center; border: 1px solid black; padding: 0px 20px;">
					<div style="height: 30px; width: 100%;"> 
						@if($shipping->id == 1)
							@php 
								$logoUrl = $order->courier_logo;

								// Try to check if the signed URL is still accessible
								if (!empty($logoUrl)) {
									try {
										$response = Illuminate\Support\Facades\Http::withoutVerifying()->get($logoUrl);

										if ($response->status() !== 200) {
											$logoUrl = asset('storage/shipping-logo/' . ($shipping->logo ?? 'default.png'));
										}
									} catch (\Throwable $e) {
										// If any error, fallback
										$logoUrl = asset('storage/shipping-logo/' . ($shipping->logo ?? 'default.png'));
									}
								} else {
									$logoUrl = asset('storage/shipping-logo/' . ($shipping->logo ?? 'default.png'));
								}

								$order->final_logo = $logoUrl;

							@endphp	
							<img src="{{ $logoUrl }}" alt="LOGO" style="height: 100%; width: auto; display: block; margin-left: auto; margin-right: auto;">	
						@else
							<img src="{{ asset('storage/shipping-logo/'.$shipping->logo) }}" alt="LOGO" style="height: 100%; width: auto; display: block; margin-left: auto; margin-right: auto;">	 
						@endif
					</div> 
					<p style="font-weight: bold;margin: 0;">{{ $order->shipping_mode }} </p> 
				</td>
			</tr>
			
			<tr>
				<td colspan="3" style="padding: 20px 0px; text-align: center; border: 1px solid black;">
					<div style="height: 60px; width: 100%;">
						@if(!empty($order->awb_number))  
							<img src="data:image/png;base64,{{ $barcodePng }}" alt="Barcode" style="display: block; margin: 0 auto;" />
							
						@endif
					</div>
					<p style="font-size: 18px;margin:0">{{ $order->awb_number }}</p>
				</td>
				<td colspan="2" style="font-size: 25px; text-align: center; font-weight: bold; border: 1px solid black;">
					@if($order->order_type == "cod")
						(COD) <br> Rs. {{ $order->cod_amount }}
					@else
						Prepaid
					@endif
				</td>
			</tr>
			<?php
				$pickupAddress = '';
                if ($order->warehouse) {
                    $pickupAddress = trim(
					$order->warehouse->address . ' ' .
					$order->warehouse->city . ' ' .
					$order->warehouse->state . ' ' .
					$order->warehouse->zip_code . ', ' .
					$order->warehouse->country
                    );
				}
				
				$customerAddress = $customerAddr->address.' '.$customerAddr->city.' '.$customerAddr->state.' '.$customerAddr->zip_code.','.$customerAddr->country;
			?>
			<tr>
				<td colspan="3" style="padding: 5px; font-size: 14px; border: 1px solid black; vertical-align: top;">
					<p style="margin-top: 5px;"> <span style="font-weight: bold;">Deliver To :</span> {{ $customer->first_name.' '.$customer->last_name }}</p>
					@if(empty($hideLabel['hide_contact']))
						<p style="margin-top: 5px;"><span style="font-weight: bold;">Contact :</span> {{ $customer->mobile }}</p>
					@endif
					<p style="margin-top: 5px;"><span style="font-weight: bold;">Address :</span> {{ $customerAddress }}</p>
					<p style="margin-top: 5px;"><span style="font-weight: bold;">Pin :</span> {{ $customerAddr->zip_code }}</p>
				</td>
				
				<td colspan="2" style="padding: 5px; font-size: 14px; border: 1px solid black; vertical-align: top;">
					<p style="margin-top: 7px;"><span style="font-weight: bold;">Order Id :</span> {{ $order->order_prefix }}</p>
					@if($order->invoice_no)
						<p style="margin-top: 7px;"><span style="font-weight: bold;">Ref./Invoice# :</span> {{ $order->invoice_no }}</p>
					@endif
					<p style="margin-top: 7px;"><span style="font-weight: bold;">Date :</span> {{ $order->order_date }}</p>
					<p style="margin-top: 7px;"><span style="font-weight: bold;">Invoice Value :</span> Rs. {{ $order->total_amount }}</p>
					@if(empty($hideLabel['hide_weight']))
						<p style="margin-top: 7px;">
							<span style="font-weight: bold;">Weights :</span> {{ $order->weight }}
						</p> 
					@endif
					@if($order->shopify_order_id)
						<p style="margin-top: 7px;">
							<span style="font-weight: bold;">Shopify OrderId :</span> {{ $order->shopify_order_id }}
						</p> 
					@endif
				</td>
			</tr>
			@if(empty($hideLabel['hide_product']))
				<tr>
					<th style="padding: 5px; border: 1px solid black; vertical-align: top;">Product category</th>
					<th style="padding: 5px; border: 1px solid black; vertical-align: top;">Product Name</th>
					<th style="padding: 5px; border: 1px solid black; vertical-align: top;">Sku</th>
					<th style="padding: 5px; width: 70px; border: 1px solid black; vertical-align: top;">Qty</th>
					<th style="padding: 5px; width: 100px; border: 1px solid black; vertical-align: top;">Price</th>
				</tr>
				@php 
					$totalQuntity = 0;
					$totalAmount = $order->total_amount;
				@endphp
				@foreach($products as $product)
					@php 
						$totalQuntity += $product->quantity; 
					@endphp
					<tr>
						<td style="padding: 5px; border: 1px solid black; vertical-align: top;">{{ $product->product_category }}</td>
						<td style="padding: 5px; border: 1px solid black; vertical-align: top;">{{ $product->product_name }}</td>
						<td style="padding: 5px; border: 1px solid black; vertical-align: top;">{{ $product->sku_number }}</td>
						<td style="padding: 5px; border: 1px solid black; vertical-align: top;">{{ $product->quantity }}</td>
						<td style="padding: 5px; border: 1px solid black; vertical-align: top;">{{ $product->amount }}</td>
					</tr>
				@endforeach
				
				<tr>
					<td colspan="3" style="text-align: right; font-weight: bold; padding: 5px; border: 1px solid black; vertical-align: top;">Total</td>
					<td style="padding: 5px; border: 1px solid black; vertical-align: top;">{{ $totalQuntity }}</td>
					<td style="padding: 5px; border: 1px solid black; vertical-align: top;">Rs. {{ $totalAmount }}</td>
				</tr>
			@endif
			@if($shipping->id == 1)
				<tr>
					<td colspan="5" style="padding: 20px 0px; text-align: center; border: 1px solid black;">
						<div style="height: 60px; width: 100%;">
							@if(!empty($order->shipment_id))  
								<img src="data:image/png;base64,{{ $orderIdBarcodePng }}" alt="Barcode" style="display: block; margin: 0 auto;" /> 
							@endif
						</div>
						<p style="font-size: 18px;margin:0">{{ $order->shipment_id }}</p> 
					</td> 
				</tr>
			@endif
			@if(empty($hideLabel['hide_address']) || empty($hideLabel['hide_mobile']))
				<tr>
					<td colspan="5" style="padding: 5px; border: 1px solid black; vertical-align: top;">
						<p style="font-weight: bold; ">If not delivered, Return to:</p>
						@if(empty($hideLabel['hide_address']))
							<p>{{ $pickupAddress }}</p>
						@endif
						<p>Contact Name: <span>{{ $order->warehouse->contact_name ?? '' }}</span></p>
						@if(empty($hideLabel['hide_mobile']))
							<p>Phone: {{ $order->warehouse->contact_number ?? '' }}</p>
						@endif
					</td>
				</tr>
			@endif	
		</table>
	</body>
</html>