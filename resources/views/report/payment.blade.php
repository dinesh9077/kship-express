@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Payment Report')
@section('header_title','Payment Report')
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
    .dataTables_length{
	margin-top:5px;
    }
</style>
<div class="content-page">
	<div class="content">
		<!-- Start Content-->
		<div class="container-fluid">
			<div class="ordr-main-001">
				<div class="main-filter-weight">
                    <div class="row row-re paymentSearchForm">  
						@if(Auth::user()->role == "admin")                  
						<div class="col-lg-2 col-sm-6">
							<div class="main-selet-11">
								<select name="user" id="user_id">
									<option value=""> All Users </option> 
									@foreach ($users as $user)
									<option value="{{$user->id}}"> {{$user->name}}  </option>
									@endforeach
								</select>
							</div>
						</div>
						@endif
						<div class="col-lg-2 col-sm-6">
							<div class="main-selet-11">
								<select name="shipping_company_id" id="shipping_company_id">
									<option value=""> All Company </option> 
									@foreach ($shippingcompanies as $shippingcompany)
									<option value="{{$shippingcompany->id}}"> {{$shippingcompany->name}}  </option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-sm-6">
							<div class="main-selet-11">
								<input type="text" class="form-control datepicker " name="fromdate" <?php echo (isset($_GET['fromdate']))?$_GET['fromdate']:''; ?> id="fromdate" placeholder="From Date">
							</div>
						</div>
						<div class="col-lg-2 col-sm-6">
							<div class="main-selet-11">
								<input type="text" class="form-control datepicker" name="todate" <?php echo (isset($_GET['todate']))?$_GET['todate']:''; ?> id="todate" placeholder="To Date">
							</div>
						</div>
						<div class="col-lg-2 col-sm-6">
							<div class="main-selet-11">
								<button class="btn-main-1 search_user">Search</button>
							</div>
						</div>
					</div>
                    
				</div>                    
				<ul id="tab">                             
					<li class="active">
						<div class="main-calander-11"> 
							<div class="main-data-teble-1 table-responsive">
								<table id="payment_datatable" class="" style="width:100%">
									<thead>
										<tr>
											<th> Sr.No</th>
											<th> Seller Details </th>
											<th> Order details </th>
											<th> Shipping details</th>
											<th> Charge </th>
											<th> Shipping charges</th> 
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
@endsection
@push('js')  
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>  
<script> 
	var dataTable = $('#payment_datatable').DataTable({
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
    	lengthMenu: [ [10, 25, 50, 100, 200, 500, 1000000], [10, 25, 50, 100, 200, 500, 'All'] ], // ðŸ”¥ options shown in dropdown
		order: [[0, 'desc'] ],
		bAutoWidth: false,			 
		"ajax":{
            "url": "{{ route('report.payment.ajax') }}",
            "dataType": "json",
            "type": "POST",
            "data": function (d) {
				d._token   = "{{csrf_token()}}";
				d.search   = $('input[type="search"]').val(); 
				d.user_id   = $('.paymentSearchForm #user_id').val(); 
				d.fromdate   = $('.paymentSearchForm #fromdate').val(); 
				d.todate   = $('.paymentSearchForm #todate').val(); 
				d.shipping_company_id  = $('.paymentSearchForm #shipping_company_id').val(); 
			}
		},
		"columns": [
			{ "data": "id" }, 
			{ "data": "seller_details" }, 
			{ "data": "order_details" }, 
			{ "data": "shippings" }, 
			{ "data": "charge" }, 
			{ "data": "shipping_charges"}
		],
		drawCallback: function () {
			$('[data-toggle="tooltip"]').tooltip();
			//$('[data-fancybox="images"]').fancybox({ loop: true });
		}
	}); 
	 
	$('.search_user').click(function (){
		dataTable.draw();	
	}); 
</script>
@endpush