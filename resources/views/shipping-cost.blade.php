<div class="main-order-001 "> 
	<div class="row">
		<div class="col-lg-12">
			<div class="main-data-teble-1 table-responsive">
				<table id="example" style="width:100%">
					<thead>
						<tr>
							<th style="text-align:left"> Courier Partner </th> 
							<th> Chargeable Weight </th>
							<th> Charges </th>  
							<th> Action </th>  
						</tr>
					</thead>
					<tbody>
						@foreach($couriers as $courier)
							<tr>
								<td>
									<div class="main-img-and-product">
										<div class="pro-img-11">
											<img src="{{ $courier['shipping_company_logo'] }}" style="width: 50px; height: 50px;">
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