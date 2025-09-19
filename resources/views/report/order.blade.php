@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Order Report')
@section('header_title','Order Report')
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
			<div class="main-order-page-1">
			    <div class="main-order-001">
    				<div class="main-filter-weight">
    					<div class="row row-re reportOrderForm">  
    						@if(Auth::user()->role != "user")                  
							<div class="col-lg-2 col-sm-6">
								<div class="main-selet-11">
									<select name="user" class="select2" id="user_id">
										<option value="">All Users</option> 
										@foreach ($users as $user)
										<option value="{{ $user->id }}">{{ $user->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
    						@endif
    						<div class="col-lg-2 col-sm-6">
    							<div class="main-selet-11">
    								<input type="text" class="form-control datepicker" name="fromdate" value="{{ request('fromdate') }}" id="fromdate" placeholder="From Date">
								</div>
							</div>
    						<div class="col-lg-2 col-sm-6">
    							<div class="main-selet-11">
    								<input type="text" class="form-control datepicker" name="todate" value="{{ request('todate') }}" id="todate" placeholder="To Date">
								</div>
							</div>
    						<div class="col-lg-2 col-sm-6">
    							<div class="main-selet-11">
    								<button class="btn-main-1 search_user">Search</button>
								</div>
							</div>
						</div>
					</div>
    				<div class="ordr-main-001">
        				<ul id="tab">
        					<li class="active">
        						<div class="main-calander-11">
									<a href="javascript:;" onclick="redirectUrl()" class="btn btn-success">
										Export Orders to Excel
									</a>
        							<div class="main-data-teble-1 table-responsive">
        								<table id="orderreport_datatable" class="table" style="width:100%">
        									<thead>
        										<tr>
        											<th>Sr.No</th>
        											<th>Seller Details</th>
        											<th>Order Details</th>
        											<th>Customer Details</th>
        											<th>Package Details</th>
        											<th>Payment</th>
        											<th>Pickup Address</th>
        											<th>Status</th> 
        											<th>Created At</th> 
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
<!-- DataTables Buttons extension -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<!-- Export functionality -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script> 
	var dataTable = $('#orderreport_datatable').DataTable({
    	processing: true,
    	serverSide: true,
    	searching: true,
    	bLengthChange: true, // ðŸ”¥ enables page length dropdown
    	lengthMenu: [ [10, 25, 50, 100, 200, 500, 1000000], [10, 25, 50, 100, 200, 500, 'All'] ], // ðŸ”¥ options shown in dropdown
    	bFilter: true,
    	bInfo: true,
    	iDisplayLength: 25,
    	order: [[0, 'desc']],
    	bAutoWidth: true,
    	  
    	ajax: {
    		url: "{{ route('report.order.ajax') }}",
    		type: "POST",
    		dataType: "json",
    		data: function (d) {
    			d._token = "{{ csrf_token() }}";
    			d.search = $('input[type="search"]').val();
    			d.user_id = $('.reportOrderForm #user_id').val();
    			d.fromdate = $('.reportOrderForm #fromdate').val();
    			d.todate = $('.reportOrderForm #todate').val();
    		}
    	},
    
    	columns: [
    		{ data: "id" },
    		{ data: "seller_details" },
    		{ data: "order_details" },
    		{ data: "customer_details" },
    		{ data: "package_details" },
    		{ data: "total_amount" },
    		{ data: "pickup_address" },
    		{ data: "status_courier" },
    		{ data: "created_at" },
    	],
    
    	drawCallback: function () {
    		$('[data-toggle="tooltip"]').tooltip();
    	}
    });
    
    $('.search_user').click(function () {
    	dataTable.draw();
    });
	
	
	function redirectUrl() {
		let user_id = $('.reportOrderForm #user_id').val();
		let fromdate = $('.reportOrderForm #fromdate').val();
		let todate = $('.reportOrderForm #todate').val();

		let url = "{{ url('report/export-orders') }}";
		url += `?user_id=${encodeURIComponent(user_id)}&fromdate=${encodeURIComponent(fromdate)}&todate=${encodeURIComponent(todate)}`;

		window.location.href = url;
	}

</script>
@endpush