<style>
	.btn.btn-new-bfs {
    padding: 12px 17px;
    border: none;
    background: #000000ff;
    font-family: 'Poppins', sans-serif;
    border-radius: 10px !important;
    font-weight: 500;
    font-size: 12px;
    color: #fff;
	}
</style>

<div class="main-order-001 "> 
	<!-- <div class="row">
		<div class="col-lg-12">
		<div class="main-data-teble-1 table-responsive">
		<table id="example" style="width:100%">
		<thead>
		<tr>
		<th style="width:20%"> Logo </th> 
		<th style="width:20%"> Courier Partner </th> 
		<th style="width:10%"> Weight </th> 
		<th style="width:20%"> Chargeable Weight </th>
		<th style="width:10%"> Charges </th>  
		<th style="width:20%"> Action </th>  
		</tr>
		</thead>
		<tbody>
		@foreach($couriers as $courier)
		<tr>
		<td>
		<div class="main-img-and-product">
		<div class="pro-img-11">
		Logo <img src="{{ $courier['shipping_company_logo'] }}" style="width: 80px; height: 70px;">
		</div> 
		</div>
		</td> 
		<td>Courier Partner  {{ $courier['courier_name'] }} </td>
		<td> Weight {{ $courier['applicable_weight'] }} </td>
		<td>Chargeable Weight {{ $courier['chargeable_weight'] }} </td>
		<td>Charges  ₹{{ $courier['total_charges'] }} </td>  
		<td>
		<button type="button" class="btn btn-primary" data-freight-charge='@json($courier)' onclick="viewFreightBreakup(this, event)">View Freight Breakup</button> 
		</td>  
		</tr>
		@endforeach 
		</tbody>
		</table>
		</div>
		</div>
	</div> -->
	
	<div class="row">
		
		
		@foreach($couriers as $courier)
		<div class="col-lg-3 col-md-4 col-sm-12 mt-2">
			<div style="display: flex;flex-direction: column; height: 100%; align-items: center; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.09);border-radius: 7px;">
				<div>
					<div class="main-img-and-product">
						<div class="pro-img-11" style="width : 100px; height : 100px;     display: flex
						;
						align-items: center;
						">
							<img src="{{ $courier['shipping_company_logo'] }}" style="width : 100%; ">
						</div> 
					</div>
				</div> 
				<div style="font-size: 16px; font-weight : 500; color : black;">{{ $courier['courier_name'] }} </div>
				<div class="mt-1" style="font-size: 14px; color : #626262;">( Weight : {{ $courier['applicable_weight'] }} )</div>
				<div class="mt-1" style="font-size: 14px; color : #626262;">Chargeable Weight : {{ $courier['chargeable_weight'] }} </div>
				<div class="mt-1" style="font-size: 20px; font-weight : 500;  color : #000000ff;"> ₹{{ $courier['total_charges'] }} </div>  
				<div class="mt-2">
					<button type="button" class="btn btn-new-bfs mb-3" data-freight-charge='@json($courier)' onclick="viewFreightBreakup(this, event)">View Freight Breakup</button> 
				</div>  
			</div>
		</div>
		@endforeach 
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