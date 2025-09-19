@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Report')
@section('header_title','Report')
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
    					<div class="row row-re">  
    						@if(Auth::user()->role == "admin")                  
    						<div class="col-lg-2 col-sm-6">
    							<div class="main-selet-11">
    								<select name="user" id="user_id">
    									<option value="">All Users</option> 
    									@foreach ($users as $user)
    									<option value="{{$user->id}}">{{$user->name}}</option>
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
        							<div class="main-data-teble-1 table-responsive">
        								<table id="neworder_datatable" class="table" style="width:100%">
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
        											<th>Pickup Details</th>
        										</tr>
        									</thead>
        									<tbody>
        										<!-- Data will be populated here -->
        									</tbody>
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
$(document).ready(function() {
     $('ul.tabs-001 li').click(function() {
         var tab_id = $(this).attr('data-tab');
         $('ul.tabs-001 li').removeClass('current');
         $('.tab-content11').removeClass('current');
         $(this).addClass('current');
         $("#" + tab_id).addClass('current');
       })
  })
  
  
  
  var dataTable = $('#neworder_datatable').DataTable({
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
            "url": "{{ route('report.dailyAjax') }}",
            "dataType": "json",
            "type": "POST",
            "data": function (d) {
                 d._token   = "{{csrf_token()}}";
                 d.search   = $('input[type="search"]').val();
                 d.status   = "{{$status}}"; 
                 d.user_id   = $('#user_id').val(); 
                 d.fromdate   = $('#fromdate').val(); 
                 d.todate   = $('#todate').val(); 
            }
       },
       "columns": [
       { "data": "id" }, 
       { "data": "seller_details" }, 
       { "data": "order_details" }, 
       { "data": "customer_details" }, 
       { "data": "package_details" }, 
       { "data": "total_amount" }, 
       { "data": "pickup_address" }, 
       { "data": "status_courier" },   
       { "data": "pickup_details" }
       ]
  }); 
  $('.search_user').click(function (){
			dataTable.draw();	
	});
  $('#neworder_datatable').on('draw.dt', function() {
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