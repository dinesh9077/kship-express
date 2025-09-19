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
			<div class="ordr-main-001">    
				<div class="main-filter-weight">                    
                    <div class="row row-re passbookSerachForm">
						@if(Auth::user()->role == "admin")
							<div class="col-lg-2 col-sm-6">
								<div class="main-selet-11">
									<select class="select2 form-control" name="user" id="user_id">
										<option value="">Select User</option>
										@foreach ($users as $user)
											<option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
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
								<button type="submit" class="btn-main-1 search_user">Search</button>
							</div>
						</div>
					</div> 
				</div> 
				<div class="main-calander-11 mt-3"> 
					<div class="main-data-teble-1 table-responsive">
						<table id="wallet_datatable" class="" style="width:100%">
							<thead>
								<tr>
									<th> Sr.No</th>
									<th> User Name </th>
									<th> Billing details </th>
									<th> Transaction Details </th>
									<th> Credit </th>
									<th> Debit </th>
									<th> Balance </th> 
									<th> Description </th>
									<th> Date </th>
								</tr>
							</thead> 
						</table>
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
	var dataTable = $('#wallet_datatable').DataTable({
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
            "url": "{{ route('report.passbook.ajax') }}",
            "dataType": "json",
            "type": "POST",
            "data": function (d) {
				d._token   = "{{csrf_token()}}";
				d.search   = $('input[type="search"]').val();
				d.user_id   = $('.passbookSerachForm #user_id').val(); 
				d.fromdate   = $('.passbookSerachForm #fromdate').val(); 
				d.todate   = $('.passbookSerachForm #todate').val(); 
			}
		},
		"columns": [
		{ "data": "id" }, 
		{ "data": "name" }, 
		{ "data": "billing_type" }, 
		{ "data": "transaction_type" }, 
		{ "data": "credit" }, 
		{ "data": "debit" }, 
		{ "data": "balance" },  
		{ "data": "note" }, 
		{ "data": "created_at" }
		]
	}); 
	$('.search_user').click(function (){
		dataTable.draw();	
	}); 
</script>
@endpush