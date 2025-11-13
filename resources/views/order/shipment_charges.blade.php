<div class="row" style="height: 100%;background: white;overflow-y: scroll;">
	<div class="col-lg-3">
		<div class="bg-010 bg-0021">
			<div class="main-heading-1	new-shipping-padding-div">
				<h4> Order Details </h4>
				
				<div class="main-text1-pickup-from mt-4">
					<p> Pickup From </p>
					<h5> {{ $order->warehouse->zip_code ?? 'N/A' }}, {{ $order->warehouse->state ?? 'N/A' }} </h5>
				</div>
				
				<div class="main-text1-pickup-from">
					<p> Deliver To </p>
					<h5> {{ $order->customerAddress->zip_code ?? 'N/A' }}, {{ $order->customerAddress->state ?? 'N/A' }} </h5>
				</div>
				
				<div class="main-text1-pickup-from">
					<p> Invoice Amount </p>
					<h5> ₹{{ $order->invoice_amount ?? 0 }} </h5>
				</div>
				
				<div class="main-text1-pickup-from">
					<p> Payment Mode </p>
					<h5> {{ $order->order_type }} </h5>
				</div>
				
				<div class="main-text1-pickup-from">
					<p> Applicable Weight (in Kg) </p>
					@php  
						$volumetricWt = $order->length * $order->width * $order->height / 5000; 
						$weight = $order->weight; 
					@endphp
					<h5>{{ max($volumetricWt, $weight) ?? 0 }} Kg </h5>
				</div> 
			</div>
		</div>
	</div>
	
	<div class="col-lg-9">
		<div class="bg-01-1 bg-0021 new-shipping-padding-div1">
			<div class="main-heading-1" style="padding-left: 0;">
				<h4 style="margin-bottom: 0;"> Select Courier Partner </h4>
				<p style="text-align: left;  margin-top: 10px; margin-bottom: 0;">{{ $total_courier }} Couriers Found</p>
			</div> 
			<div class="main-data-teble-1 main-12112 table-responsive">
				<table id="example" class="example-new-ids-shipping" style="margin: auto;width: 97%;">
					<thead>
						<tr style="border-bottom: none !important;">
							<th class="new-details-table;" style="text-align:left;"> Logo </th> 
							<th class="new-details-table;" style="text-align:left;"> Courier </th>
							<th class="new-details-table;" style="text-align:left;"> Estimated Delivery </th>
							<th class="new-details-table;" style="text-align:left;"> Chargeable Weight </th>
							<th class="new-details-table;" style="text-align:left;"> Charges </th>
							<th class="new-details-table;" > Action </th>
						</tr>
					</thead>
					<tbody>
						@foreach($couriers as $key => $courier)
							<tr  style="border-bottom: none !important;" class="">
								<td style="text-align: start;">
									<div class="main-img-and-product" style="padding : 20px 0px  20px 15px;">
										<div class="pro-img-11">
											<img src="{{$courier['shipping_company_logo']}}" style="width: 80px; height: 70px;">
										</div> 
									</div>
								</td> 
								<td style="text-align: start;"> {{ $courier['courier_name'] }} </td>
								<td style="text-align: start;"> {{ $courier['estimated_delivery'] ?? 'N/A'}} </td>
								<td style="text-align: start;"> {{ $courier['chargeable_weight'] }} </td>
								<td style="text-align: start;"> ₹{{ $courier['total_charges'] }} </td>
								<td style="text-align: start;"> 
									<button type="button" 
										class="new-btn-ship-now" 
										data-courier='@json(\Illuminate\Support\Arr::except($courier, ["responseData"]))' 
										onclick="shipNowOrder(this, event)">
										Ship Now
									</button>

									<button type="button" class="new-btn-ship-now" data-freight-charge='@json($courier)' onclick="viewFreightBreakup(this, event)">View Freight Breakup</button> 
								</td> 
							</tr>
						@endforeach 
					</tbody>
				</table>
			</div>
		</div>
	</div> 
	<script>
		function viewFreightBreakup(obj, event)
		{
			event.preventDefault();
			const $obj = $(obj);
			let courierDetail = $obj.attr('data-freight-charge');
			let data;
			try {
				data = JSON.parse(courierDetail);
			} catch (error) { 
				toastrMsg('error', 'Something went wrong.');
				return;
			}
			if (!modalOpen)
			{
				modalOpen = true;
				closemodal(); 
				run_waitMe($('body'), 1, 'win8');
    			$.post("{{ route('rate.freight-breakup') }}",
    			{
    				_token: "{{csrf_token()}}",
    				data: JSON.stringify(data)
				},
    			function(res)
    			{
					$('body').waitMe('hide');
					if(res.status == "success")
					{
						$('body').find('#modal-view-render').html(res.view);
						$('#freightBreakupModal').modal('show');  
					}
					else 
					{
						toastrMsg(res.status, res.msg);
					}
				},'Json');  
			} 
		}
	</script>
</div>  
