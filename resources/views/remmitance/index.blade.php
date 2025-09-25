@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Remmitance')
@section('header_title','Remmitance')
@section('content')
<style>
    .tooltip .tooltiptext {
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
            <div class="main-order-page-1">
                <div class="main-order-001">
                    <div class="main-filter-weight">
                        <form method="get" action="">
                            <div class="inner-page-heading">
                                <div class="row row-re w-100">
                                    <div class="col-lg-2 col-sm-6">
                                        <div class="main-selet-11">
                                            <input type="text" class="form-control datepicker" name="fromdate" <?php echo (isset($_GET['fromdate'])) ? $_GET['fromdate'] : ''; ?> id="fromdate" placeholder="From Date">
										</div>
									</div>
                                    <div class="col-lg-2 col-sm-6">
                                        <div class="main-selet-11">
                                            <input type="text" class="form-control datepicker" name="todate" <?php echo (isset($_GET['todate'])) ? $_GET['todate'] : ''; ?> id="todate" placeholder="To Date">
										</div>
									</div>
                                    @if(session('success'))
										<div class="alert alert-success">
											{{ session('message') }}
										</div>
                                    @endif 
                                    <div class="col-lg-2 col-sm-6">
                                        <div class="main-selet-11">
                                            <button type="button" class="btn-main-1 search_data">Search</button>
										</div>
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
                                                    <th> # </th>
                                                    <th> Order Id </th>
                                                    <th> Seller Details </th>
                                                    <th> Delivery Date </th>
                                                    <th> Amount </th>
                                                    <th> Shipping Details </th>
                                                    <th> Status </th> 
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
@endsection
@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script> 
    var dataTable = $('#remmitance_datatable').DataTable({
        processing: true,
        "language": {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
		},
        serverSide: true,
        bLengthChange: true,
        searching: true,
        bFilter: true,
        bInfo: true,
        iDisplayLength: 25,
        order: false,
        bAutoWidth: false,
        "ajax": {
            "url": "{{ route('remmitance.ajax') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) {
                d._token = "{{csrf_token()}}";
                d.search = $('input[type="search"]').val();
                d.shipping_company_id = $('#shipping_company_id').val();
                d.fromdate = $('#fromdate').val();
                d.todate = $('#todate').val();
			}
		},
        "columns": [{
			"data": "id"
		},
		{
			"data": "order_id"
		},
		{
			"data": "seller_details"
		},
		{
			"data": "delivery_date"
		},
		{
			"data": "amount"
		},
		{
			"data": "shipment_details"
		},
		{
			"data": "status_courier"
		}, 
        ],
        "drawCallback": function(settings) {
            $('#totAmt').text(settings.json.totAmt);
		}
	});
	
    $('.search_data').click(function() {
        $('.remittance_data').show();
        dataTable.draw();
	});
	  
</script>
@endpush