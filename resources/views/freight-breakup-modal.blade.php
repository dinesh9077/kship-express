<div class="modal fade" id="freightBreakupModal" tabindex="-1" aria-labelledby="freightModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header  text-white">
				<h5 class="modal-title" id="freightModalLabel">FREIGHT BREAKUP</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="mdi mdi-close"></i></button>
			</div>
			<div class="modal-body">
				@if($responseData['shipping_company_id'] == 1)
					<div class="p-3 border rounded d-flex align-items-center justify-content-between">
						<div>
							<p class="mb-1"><strong>Courier:</strong> {{ $responseData['shipping_company_name'] ?? '' }}</p>
							<p class="mb-1"><strong>Estimated Delivery:</strong> {{ $responseData['estimated_delivery'] ?? '' }}</p>
							<p class="mb-1"><strong>Chargeable Weight:</strong> {{ $responseData['chargeable_weight'] ?? 0 }}</p>
							<p class="mb-1"><strong>Shipping Charges:</strong> ₹ {{ $responseData['shipping_charge'] ?? 0 }}</p>
							<p class="mb-1"><strong>GST at {{ $responseData['responseData']['gst_percentage'] ?? 0 }}%:</strong> ₹ {{ $responseData['tax'] ?? 0 }}</p>
							<hr class="my-2">
							<h5 class="text-danger mb-0"><strong>Total:</strong> ₹ {{ $responseData['total_charges'] ?? 0 }}</h5>
						</div>
						<div>
							@if(!empty($responseData['shipping_company_logo']))
								<img src="{{ $responseData['shipping_company_logo'] }}" alt="Courier Logo" style="height: 120px; width: 120px; object-fit: contain;">
							@endif
						</div>
					</div>  
				@elseif($responseData['shipping_company_id'] == 2)
					<div class="p-3 border rounded">
						<p><strong>Total freight on Pickup:</strong> ₹ {{ $responseData['total_charges'] }}</p>
						<p><strong>Charged weight:</strong> {{ $responseData['responseData']['charged_wt'] }} kg(s)</p>
						<p><strong>Minimum Charged Weight:</strong> {{ $responseData['responseData']['min_charged_wt'] }} kg/LR</p>  
						<hr> 
						<h5><strong>Forward Freight</strong></h5>
						<p><strong>Base Freight Charge:</strong> ₹ {{ ($responseData['freight_charges'] ?? 0) + ($responseData['percentage_amount'] ?? 0) }}</p>
						<p><strong>Fuel Surcharge:</strong> ₹ {{ $responseData['responseData']['price_breakup']['fuel_surcharge'] ?? 0 }}</p>
						<p><strong>Insurance ROV:</strong> ₹ {{ $responseData['responseData']['price_breakup']['insurance_rov'] ?? 0 }}</p>
						<p><strong>ODA Charge:</strong> ₹ {{ array_sum($responseData['responseData']['price_breakup']['oda'] ?? []) }}</p>
						<p><strong>Handling Charges (Box, COD & POD):</strong> ₹ {{ $responseData['responseData']['price_breakup']['other_handling_charges'] ?? 0 }}</p>
						<p><strong>Pre-tax Freight Charges:</strong> ₹ {{ ($responseData['responseData']['price_breakup']['pre_tax_freight_charges'] ?? 0) + ($responseData['percentage_amount'] ?? 0) }}</p>
						<p><strong>GST at {{ $responseData['responseData']['price_breakup']['gst_percent'] ?? 0 }}%:</strong> ₹ {{ $responseData['responseData']['price_breakup']['gst'] ?? 0 }}</p> 
						<hr> 
						<h5 class="text-danger"><strong>Total:</strong> ₹ {{ $responseData['total_charges'] }}</h5>  
					</div>
				@elseif($responseData['shipping_company_id'] == 3) 
					<div class="p-3 border rounded">
						<p><strong>Total freight on Pickup:</strong> ₹ {{ $responseData['total_charges'] }}</p>
						<p><strong>Charged weight:</strong> {{ $responseData['chargeable_weight'] }} kg(s)</p> 
						<hr> 
						<h5><strong>Forward Freight</strong></h5>
						<p><strong>Base Freight Charge:</strong> ₹ {{ ($responseData['freight_charges'] ?? 0) + ($responseData['percentage_amount'] ?? 0) }}</p>
						 
						<p><strong>Handling Charges (COD):</strong> ₹ {{ $responseData['cod_charges'] ?? 0 }}</p>
						<p><strong>Pre-tax Freight Charges:</strong> ₹ {{ ($responseData['freight_charges'] ?? 0) + ($responseData['percentage_amount'] ?? 0)  + ($responseData['cod_charges'] ?? 0) }}</p>
						<p><strong>Tax: </strong> ₹ {{ $responseData['responseData']['tax_data'] ? array_sum($responseData['responseData']['tax_data']) : 0 }}</p> 
						<hr> 
						<h5 class="text-danger"><strong>Total:</strong> ₹ {{ $responseData['total_charges'] }}</h5> 
					</div>
				@endif
			</div>
		</div>
	</div>
</div>