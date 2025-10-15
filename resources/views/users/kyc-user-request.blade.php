@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Client Kyc Request')
@section('header_title','Client Kyc Request')
@section('content') 
  
<div class="content-page">
	<div class="content">
		<!-- Start Content-->
		<div class="container-fluid">
		    <div class="main-order-page-1">                    
                <div class="main-order-001">  
        			<div class="main-filter-weight">
        				<div class="row row-re">
        					<div class="col-lg-2">
        						<div class="main-selet-11">
        							<select name="kyc_status" id="kyc_status">
        								<option value=""> All Status </option>
        								<option value="0"> Pending </option>
        								<option value="1"> Approved </option>
        								<option value="2"> Rejected </option>
        							</select>
        						</div>
        					</div>
        					<div class="col-lg-2 col-sm-6">
        						<div class="main-selet-11">
        							<button class="btn-main-1  search-btn-remi">Search</button>
        						</div>
        					</div>
        				</div>
        			</div>
        			<div class="main-create-order">
        				<div class="main-disolay-felx">
        					<div class="main-btn0main-1"></div>
        				</div>
        				<div class="main-data-teble-1 table-responsive">
        					<table id="kycRequestDatatable" class="" style="width:100%">
        						<thead>
        							<tr>
        								<th> SR.No </th>
        								<th> Name </th>
        								<th> Email </th>
        								<th> Pancard Status </th>
        								<th> Aadhar Status </th> 
        								<th> Created At </th>
        								<th> Action </th>
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
	
	var dataTable = $('#kycRequestDatatable').DataTable({
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
			"url": "{{ route('users.kyc-request.ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{csrf_token()}}";
				d.search   = $('input[type="search"]').val();
				d.kyc_status   = $('#kyc_status').val();
			}
		},
		"columns": [
		{ "data": "id" }, 
		{ "data": "name" }, 
		{ "data": "email" }, 
		{ "data": "pancard_status" }, 
		{ "data": "aadhar_status" },   
		{ "data": "created_at" },   
		{ "data": "action" }
		]
	}); 
	
	$('.search_kyc').click(function (){
		dataTable.draw();	
	})
 
	$('#pancardForm').submit(function(event) {
		event.preventDefault();  
		$(this).find('button').prop('disabled',true); 
		var formData = new FormData(this); 
		$.ajax({
			async: true,
			type: $(this).attr('method'),
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			processData: false,
			contentType: false, 
			dataType: 'Json', 
			success: function (res) 
			{
				$('button').prop('disabled',false);  
				if(res.status == "error")
				{
					toastrMsg(res.status,res.msg);
				}
				else
				{ 
					toastrMsg(res.status,res.msg); 
					window.location.href = "{{route('users')}}";
				}
			} 
		});
	});
</script>
@endpush