<div class="row" style="height: 100%;background: #F8F8F8;overflow-y: scroll;">
	<div class="col-lg-3">
		<div class="bg-010 bg-0021">
			<div class="main-heading-1">
				<h4> Order Details </h4>
				
				<div class="main-text1-pickup-from">
					<p> Pickup From </p>
					<h5> {{ $order->warehouse->zip_code ?? 'N/A' }}, {{ $order->warehouse->state ?? 'N/A' }} </h5>
				</div>
				
				<div class="main-text1-pickup-from">
					<p> Deliver To </p>
					<h5> {{ $order->customerAddress->zip_code ?? 'N/A' }}, {{ $order->customerAddress->state ?? 'N/A' }} </h5>
				</div>
				
				<div class="main-text1-pickup-from">
					<p> Invoice Amount </p>
					<h6> ₹{{ $order->total_amount ?? 0 }} </h6>
				</div>
				
				<div class="main-text1-pickup-from">
					<p> Payment Mode </p>
					<h6> {{ $order->order_type }} </h6>
				</div>
				
				<div class="main-text1-pickup-from">
					<p> Applicable Weight (in Kg) </p>
					<h6>{{ $order->orderItems->sum(fn($item) => $item->dimensions['weight'] ?? 0) }} Kg </h6>
				</div>
				
			</div>
		</div>
	</div>
	
	<div class="col-lg-9">
		<div class="bg-01-1 bg-0021">
			<div class="main-heading-1" style="padding-left: 0;">
				<h4 style="margin-bottom: 0;"> Select Courier Partner </h4>
				<p style="text-align: left; margin-bottom: 0;">{{ $total_courier }} Couriers Found</p>
			</div> 
			<div class="main-data-teble-1 main-12112 table-responsive">
				<table id="example" class="" style="width:100%">
					<thead>
						<tr>
							<th style="text-align:left"> Courier Partner </th> 
							<th> Chargeable Weight </th>
							<th> Charges </th>
							<th> Action </th>
						</tr>
					</thead>
					<tbody>
						@foreach($couriers as $key => $courier)
							<tr>
								<td>
									<div class="main-img-and-product">
										<div class="pro-img-11">
											<img src="{{$courier['shipping_company_logo']}}" style="width: 50px; height: 50px;">
										</div>
										<div class="main-content-1-pro">
											<h5> {{ $courier['courier_name'] }} </h5>
											<h6>  Min-weight: {{ $courier['min_weight'] }} kg </h6> 
										</div>
									</div>
								</td> 
								<td> {{ $courier['chargeable_weight'] }} Kg </td>
								<td> ₹{{ $courier['total_charges'] }} </td>
								<td>
									<button type="button" class="btn btn-primary" data-freight-charge='@json($courier)' onclick="viewFreightBreakup(this, event)">View Freight Breakup</button> 
									<button type="button" class="btn btn-primary" data-courier='@json($courier)' onclick="shipNowOrder(this, event)"> Ship Now </button> 
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
