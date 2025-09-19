@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - COD Voucher')
@section('header_title','COD Voucher')
@section('content') 
<style>
	.tooltip .tooltiptext 
	{ 
	text-align: left !important; 
	padding: 5px 0 5px 5px !important;
	}	
	td p {
	margin-bottom: 0;
    }
</style>
<div class="content-page">
	<div class="content">
		<!-- Start Content-->
		<div class="container-fluid">
			<div class="main-find-weght">
                <div class="main-order-page-1">
					<div class="main-order-001">                    
						<div class="main-filter-weight">
							@if(config('permission.cod_voucher.add'))	
								<form method="post" action="{{route('generatevouchers')}}">
									@csrf
									<div class="row row-re">
										<div class="col-lg-2 col-sm-6">
											<div class="main-selet-11">
												<input type="text" class="form-control datepicker" name="date"  placeholder="Select Date" required>
											</div>
										</div>
										<div class="col-lg-2 col-sm-6">
											<div class="main-selet-11">
												<button type="submit" class="btn-main-1">Generate Voucher</button>
											</div>
										</div>
									</div>
								</form>
							@endif 
						</div>
						<div class="ordr-main-001">
							<ul id="tab">                             
								<li class="active">
									<div class="main-calander-11"> 
										<div class="main-data-teble-1 table-responsive">
											<table id="codvoucher_datatable" class="" style="width:100%">
												<thead>
													<tr>
														<th><input type="checkbox" id="checkboxesMain"></th> 
														<th> Seller Details </th>
														<th> Order details </th> 
														<th> Amount</th>
														<th> Total Amount</th>
														<th> Action</th>
													</tr>
												</thead> 
											</table>
										</div>
									</div>
								</li> 
							</ul>  
						</div>  
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@push('js')  
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 
<script> 
	var dataTable = $('#codvoucher_datatable').DataTable({
		processing:true,
		"language": {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
		},
		serverSide:true,
		bLengthChange: true,
		searching: true,
		bFilter: true,
		bInfo: true,
		iDisplayLength: 25,
		order: [[0, 'desc'] ],
		bAutoWidth: false,			 
		"ajax":{
            "url": "{{ route('order.codvoucher.ajax') }}",
            "dataType": "json",
            "type": "POST",
            "data": function (d) {
				d._token   = "{{csrf_token()}}";
				d.search   = $('input[type="search"]').val();
				
			}
		},
		"columns": [
			{ "data": "id" },  
			{ "data": "seller_details" }, 
			{ "data": "order_details" },   
			{ "data": "amount" },
			{ "data": "total_amount" },
			{ "data": "action" },
		]
	});
	
	
	
    $(document).ready(function () {
		
        $(document).on('click', '.pay_now', function () {
            var order_id = $(this).data('order-id');
            var user_id = $(this).data('user-id');
            var shipping_company_id = $(this).data('shipping_company_ids');
            var amount = $(this).data('amount');
            console.log(order_id);
    		$("#order_id").val(order_id);
    		$("#user_id").val(user_id);
    		$("#shipping_company_id").val(shipping_company_id);
    		$("#amount").val(amount);
        	$("#cod_payout_form").show();
		});
	});
	 
	$('#codvoucher_datatable').on('draw.dt', function() {
		$('[data-toggle="tooltip"]').tooltip({
            position: {
				my: "left bottom", // the "anchor point" in the tooltip element
				at: "left top", // the position of that anchor point relative to selected element
			}
		});
		@if(Auth::user()->role != "admin") 
			dataTable.columns(1).visible(false); 
		@endif 
	});
</script>
@endpush