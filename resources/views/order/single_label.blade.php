<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Shipping Label</title>
	</head>
	<style>
		@page{
			margin: 20px !important;
		}
	</style>
	<body>
		<table style="width: 50%; margin: ; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 11px; border: 1px solid black;">
			<tr>
				<td colspan="4" style="padding: 10px 10px; width: 80%; vertical-align: middle; font-size: 25px; font-weight: bold; border: 1px solid black;">
					<img src="{{ url('storage/settings', config('setting.header_logo')) }}" width="150" alt="">
				</td>
				<td colspan="1" style="padding: 0; text-align: center ; width: 20%; center; border: 1px solid black; padding: 0px 20px;">
					<div style="height: 45px; width: 100%;">
						@if($shipping->id == 1)
							@php 
								$logoUrl = $order->courier_logo;

								// Check if the courier_logo exists and belongs to shipping company 1
								if (!empty($logoUrl) && $order->shipping_company_id == 1) {
									$localPath = "courier-logo/{$order->courier_id}.png";

									// Check if local copy exists in storage
									if (\Storage::disk('public')->exists($localPath)) { 
										$logoUrl = asset("storage/{$localPath}");
									}
								}else{
							    	$localPath = "courier-logo/{$order->courier_id}.png";
								    $logoUrl = asset("storage/{$localPath}");
								}

								// Store final logo path for later use if needed
								$order->final_logo = $logoUrl;
							@endphp	

							<img src="{{ $logoUrl }}" alt="LOGO" style="height: 50px	; width: 50px; display: block; margin-left: auto; margin-right: auto;">	
						@else
							<img src="{{ asset('storage/shipping-logo/'.$shipping->logo) }}" alt="LOGO"
						style="height: 100%; width: auto; display: block; margin-left: auto; margin-right: auto;">	
						@endif 
					</div>
					<p style="font-weight: bold;margin: 0;font-size : 14px;">{{ $order->shipping_mode }}</p>
				</td>
			</tr>
			
			<tr>
				<td colspan="4" style="padding: 10px 0; border: 1px solid black;">
					<div style="
						display: table;
						margin: 0 auto;
						text-align: center;
					">
						<div style="
							display: table-row;
							vertical-align: middle;
						">
						@if(!empty($order->awb_number))
							<!-- QR/Barcode (left side) -->
							<div style="display: table-cell; vertical-align: middle; padding-right: 10px;">
							<img src="data:image/png;base64,{{ $barcodePng }}" alt="QR Code"
								style="height: 60px; width: 60px; object-fit: contain;" />
							</div>
						@endif

						<!-- AWB Number (right side) -->
						<div style="display: table-cell; vertical-align: middle;">
							<p style="font-size: 18px; font-weight: 600; margin: 0;">
							{{ $order->awb_number }}
							</p>
						</div>
						</div>
					</div>
				</td>

				<td colspan="1" style="font-size: 15px; text-align: center; font-weight: bold; border: 1px solid black;">
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
				<td colspan="3" style="padding: 5px; font-size: 11px; border: 1px solid black; vertical-align: top;">
					<p style="margin-top: 5px;"> <span style="font-weight: bold;">Deliver To :</span> {{ $customer->first_name.' '.$customer->last_name }}</p>
					@if(empty($hideLabel['hide_contact']))
						<p style="margin-top: 5px;"><span style="font-weight: bold;">Contact :</span> {{ $customer->mobile }}</p>
					@endif
					<p style="margin-top: 5px;"><span style="font-weight: bold;">Address :</span> {{ $customerAddress }}</p>
					<p style="margin-top: 5px;"><span style="font-weight: bold;">Pin :</span> {{ $customerAddr->zip_code }}</p>
				</td>
				
				<td colspan="2" style="padding: 5px; font-size: 11px; border: 1px solid black; vertical-align: top;">
					<p style="margin-top: 7px;"><span style="font-weight: bold;">Order Id :</span> {{ $order->order_prefix }}</p>
					@if($order->invoice_no)
						<p style="margin-top: 7px;"><span style="font-weight: bold;">Ref./Invoice# :</span> {{ $order->invoice_no }}</p>
					@endif
					<p style="margin-top: 7px;"><span style="font-weight: bold;">Date :</span> {{ $order->order_date }}</p>
					<p style="margin-top: 7px;"><span style="font-weight: bold;">Invoice Value :</span> Rs. {{ $order->total_amount }}</p>
					
					@if(empty($hideLabel['hide_weight']))
						<p style="margin-top: 7px;">
							<span style="font-weight: bold;">Weights :</span> {{ $order->weight }} KG
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
					<td colspan="5" style="padding: 10px 0px; text-align: center; border: 1px solid black;">
						<div style="height: 50px; width: 100%;">
							@if(!empty($order->shipment_id))  
								<img src="data:image/png;base64,{{ $orderIdBarcodePng }}" alt="Barcode" style="display: block; width: 90%; margin: 0 auto;" /> 
							@endif
						</div>
						<p style="font-size: 18px;margin:0">{{ $order->shipment_id }}</p> 
					</td> 
				</tr>
			@endif
			
			@if(empty($hideLabel['hide_address']) || empty($hideLabel['hide_mobile']))
				<tr>
					<td colspan="5" style="padding: 0PX 5px; border: 1px solid black; vertical-align: top;">
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