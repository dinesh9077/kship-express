<div class="modal fade" id="freightBreakupModal" tabindex="-1" aria-labelledby="freightModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header  text-white">
				<h5 class="modal-title" id="freightModalLabel">FREIGHT BREAKUP</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="mdi mdi-close"></i></button>
			</div>
			<div class="modal-body">
				@if($responseData['shipping_company_id'] == 2)
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

						<p class="text-muted mt-3">
							<small>* These Freight Charges are for estimation only. The actual charges are subject to change in lieu of other factors / special pricing.</small>
						</p>
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

						<p class="text-muted mt-3">
							<small>* These Freight Charges are for estimation only. The actual charges are subject to change in lieu of other factors / special pricing.</small>
						</p>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>