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
	.page-heading-main {
	display: flex;
	align-items: center;
	justify-content: end;
	margin-bottom: 20px;
	gap: 15px;
	flex-wrap: wrap;
}

.left-head-deta {
	display: flex;
	align-items: end;
	gap: 15px;
}
.custom-entry {
	display: flex;
	align-items: center;
	gap: 8px;
}
.right-head-deta {
	display: flex;
	align-items: center;
	gap: 15px;
}

.table-custom-serch .input-main {
	min-width: 500px;
}
.table-custom-serch .input-main { 
	    border: 1px solid #dcdcdc;
	border-radius: 10px;
	padding: 7px;
	margin-left: 3px;
	font-weight: 400;
	font-size: 14px;
	color: #000;
	background-color: white;
	padding: 10px 20px;
 
}
.custom-entry p {
	margin: 0;
	font-size: 14px;
	color: #0A1629;
	font-weight: 500;
}


.btn-warning {
	border-radius: 10px;
	padding: 10px 20px;
	background-color: #FBA911;
	border-radius: 10px;
}

.btn-blues{
border-radius: 10px;
	padding: 10px 20px;
	background-color: #15A7DD;
	border-radius: 10px;
}

#page_length{
	padding: 5px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 44px !important;
	background-color: #f3f3f3 !important;
	border: none !important;
}
</style>
<div class="content-page">
	<div class="content">
		<!-- Start Content-->
		<div class="container-fluid">
			<div class="main-order-page-1">
			    <div class="main-order-001">
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
								<input type="text" class="form-control new-height-fcs-rmi  datepicker" name="fromdate" value="{{ request('fromdate') }}" id="fromdate" placeholder="From Date">
							</div>
						</div>

						<div class="col-lg-2 col-sm-6">
							<div class="main-selet-11">
								<input type="text" class="form-control new-height-fcs-rmi  datepicker" name="todate" value="{{ request('todate') }}" id="todate" placeholder="To Date">
							</div>
						</div>

						<div class="col-lg-2 col-sm-6">
							<div class="main-selet-11">
								<button type="submit" class="btn-main-1 search_user search-btn-remi">Search</button>
							</div>
						</div>
					</div> 
				</div> 
				<div class="ordr-main-001">
					<div class="page-heading-main justify-content-between align-items-end  mb-0">
						<div class="left-head-deta">
							<div class="custom-entry">
								<p>Show</p>
								<select id="page_length">
									<option value="10">10</option>
									<option value="25" selected>25</option>
									<option value="50">50</option>
									<option value="100">100</option>
									<option value="500">500</option>
									<option value="1000">1000</option>
									<option value="2000">2000</option>
									<option value="200000000">All</option>
								</select>
								<p>entries</p>
							</div>
						</div>
						<div class="right-head-deta"> 
							<a href="javascript:;" class="btn btn-blues" id="pdfExport"> PDF</a>
							<a href="javascript:;" class="btn btn-warning" id="excelExport"> XLXS</a>
						</div>
					</div> 
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
</div>
@endsection
@push('js')  
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>  

<!-- DataTables Buttons Extension -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<!-- JSZip for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- pdfmake for PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Buttons HTML5 export -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<!-- Buttons print option (optional) -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script> 
	var dataTable = $('#wallet_datatable').DataTable({
		processing:true,
		dom: 'Bfrtip', 
		buttons: [
		{
			extend: 'excelHtml5',
			className: 'd-none',
			text: 'excel',
			exportOptions: {  modifier: {  page: 'current' }  }
		},{
			extend: 'pdfHtml5',
			className: 'd-none',
			text: 'excel',
			exportOptions: {  modifier: {  page: 'current' }  }
		}],
		"language": {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
		}, 
		serverSide:true,
		bLengthChange: false,
		searching: false,
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
	
	$("#excelExport").on("click", function() {
		$(".buttons-excel").trigger("click");
	});
	
	$("#pdfExport").on("click", function() {
		$(".buttons-pdf").trigger("click");
	});
	
	$('#page_length').change(function(){
		dataTable.page.len($(this).val()).draw();
	})
	
	$('.search_user').click(function() { 
		dataTable.draw();  
	}); 
	
	var debounceTimer; 
	$('#search_table').keyup(function() {
		clearTimeout(debounceTimer);
		debounceTimer = setTimeout(function() {
			dataTable.draw(); 
		}, 400); // Adjust the debounce delay (in milliseconds) as per your preference
	}); 
	
</script>
@endpush