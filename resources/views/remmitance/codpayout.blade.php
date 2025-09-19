@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - COD Payout')
@section('header_title','COD Payout')
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
							<form method="get" action="">
                                <div class="inner-page-heading">
                                    <div class="row row-re w-100">
                                        <div class="col-lg-2 col-sm-6">
											<div class="main-selet-11">
												<input type="text" class="form-control datepicker" name="fromdate" <?php echo (isset($_GET['fromdate']))?$_GET['fromdate']:''; ?> id="fromdate" placeholder="From Date">
											</div>
										</div>
										<div class="col-lg-2 col-sm-6">
											<div class="main-selet-11">
												<input type="text" class="form-control datepicker" name="todate" <?php echo (isset($_GET['todate']))?$_GET['todate']:''; ?> id="todate" placeholder="To Date">
											</div>
										</div>
										@if(Auth::user()->role == "admin")
                                        <div class="col-lg-2 col-sm-6">
											<div class="main-selet-11">
												<select name="vendor_id" id="vendor_id">
													<option value=""> Select User </option> 
													@foreach($vendors as $vendor)
													<option value="{{$vendor->id}}"> {{$vendor->name}}  </option>
													@endforeach
												</select>
											</div>
										</div>
										@endif
										<div class="col-lg-2 col-sm-6">
											<div class="main-selet-11">
												<select name="voucher_status" id="voucher_status">
													<option value=""> Select Status </option> 
													
													<option value="0"> Unpaid  </option>
													<option value="1"> Paid  </option>
												</select>
											</div>
										</div>
										<div class="col-lg-2 col-sm-6">
											<div class="main-selet-11">
												<button type="button" class="btn-main-1 search_data">Search</button>
											</div>
										</div>
										
									</div>
                                    <div class="order-111-add">
                                        <div class="main-selet-11 text-end" style="white-space: nowrap;">
											<b>Total Amount : </b> ₹<span id="totAmt"> </span>
										</div>
									</div>
								</div>
							</form>
						</div>
                        <div class="ordr-main-001">
                            <ul id="tab">                             
								<li class="active">
									<div class="main-calander-11"> 
										<div class="main-data-teble-1 table-responsive">
											<table id="remmitance_datatable" class="" style="width:100%">
												<thead>
													<tr>
														<th><input type="checkbox" id="checkboxesMain"></th>
														<th> Order Prefix </th>
														<th> Seller Details </th>
														<th> Order details </th>
														<th> Status </th>
														<th> Amount</th>
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
<div class="modal fade bd-example-modal-lg" id="cod_payout_form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> Cod Payout </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<form action="{{ route('codRemmitance')}}" method="post">
                @csrf
                <input type="hidden" name="user_ids" id="user_ids">
                <input type="hidden" name="order_id" id="order_id">
                <input type="hidden" name="shipping_company_id" id="shipping_company_id">
                <input type="hidden" name="amount" id="amount">
                <input type="hidden" name="voucher_no" id="voucher_no">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reference Id</label>
                        <input type="text" name="remittance_reference_id" class="form-control">
					</div>
                    <div class="form-group">
                        <label>Remittance Date</label>
                        <input type="date" name="remittance_date" class="form-control">
					</div>
				</div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary"  id="close_btn_" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
@push('js')  
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 
<script>
    setTimeout(function() {
        document.getElementById('alert-success').style.display = 'none';
	}, 2000);
	setTimeout(function() {
        document.getElementById('alert-error').style.display = 'none';
	}, 2000);
</script>
<script>
	$(document).ready(function() {
		$('.remittance_data').hide();
		$('ul.tabs-001 li').click(function() {
			var tab_id = $(this).attr('data-tab');
			$('ul.tabs-001 li').removeClass('current');
			$('.tab-content11').removeClass('current');
			$(this).addClass('current');
			$("#" + tab_id).addClass('current');
		})
	})
	
	
	var dataTable = $('#remmitance_datatable').DataTable({
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
            "url": "{{ route('codPayoutajax') }}",
            "dataType": "json",
            "type": "POST",
            "data": function (d) {
				d._token   = "{{csrf_token()}}";
				d.search   = $('input[type="search"]').val();
                //  d.shipping_company_id   = $('#shipping_company_id').val();
				d.fromdate   = $('#fromdate').val();
				d.todate   = $('#todate').val();
				d.voucher_status= $('#voucher_status').val();
				d.user_id = $('#vendor_id').val();
			}
		},
		"columns": [
		{ "data": "id" }, 
		{ "data": "order_prefix_list" },
		{ "data": "seller_details" }, 
		{ "data": "order_details" }, 
		{ "data": "status" },
		{ "data": "amount" },
		{ "data": "action" },
		],
		"drawCallback": function( settings )
        {
            $('#totAmt').text(settings.json.totAmt);
		}
	});
	
  	$('.search_data').click(function (){
  	    $('.remittance_data').show();
		dataTable.draw();	
	});
	
	
    $(document).ready(function () {
		
        $(document).on('click', '.pay_now', function () {
            var order_id = $(this).data('order-id');
            var user_ids = $(this).data('user-id');
            var shipping_company_id = $(this).data('shipping_company_ids');
            var amount = $(this).data('amount');
            var voucher_no = $(this).data('voucher_no');
            console.log(user_ids);
    		$("#order_id").val(order_id);
    		$("#user_ids").val(user_ids);
    		$("#shipping_company_id").val(shipping_company_id);
    		$("#amount").val(amount);
        	$("#voucher_no").val(voucher_no);
            $('#cod_payout_form').modal('show');
		});
	});
	
	$('#remmitance_datatable').on('draw.dt', function() {
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