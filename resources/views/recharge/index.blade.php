@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Recharge History')
@section('header_title','Recharge History')
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

@media(max-width : 575px){
	.right-head-deta {
	flex-direction: column;
}
}

.table-custom-serch .input-main {
	min-width: 500px;
}

@media(max-width : 991px){
		.table-custom-serch .input-main {
	min-width: 200px;
	}
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

	
.btn-blues{
border-radius: 10px;
	padding: 10px 20px;
	background-color: #15A7DD;
	border-radius: 10px;
}


.btn-warning {
	border-radius: 10px;
	padding: 10px 20px;
	background-color: #FBA911;
	border-radius: 10px;
}


	.custom-entry p {
	margin: 0;
	font-size: 14px;
	color: #0A1629;
	font-weight: 500;
	}
</style>
<div class="content-page">
	<div class="content">

		<!-- Start Content-->
		<div class="container-fluid">
			<div class="main-order-page-1">
				<div class="main-order-001">
					<div class="main-create-order">
						<div class="mb-3">
							<div class="header-11">
								<div class="row" style="row-gap: 10px;">
									<div class="from-group col-lg-2 col-sm-6">
										<select name="status" id="status">
											<option value="">All</option> 
											<option value="Pending">Pending</option>
											<option value="Paid">Paid</option>
										</select>
									</div>
									<!--<div class="from-group col-lg-2 col-sm-6">
										<select name="transaction_type" id="transaction_type">
											<option value="">All</option>
											<option value="Online">Online</option>
											<option value="Offline">Offline</option>
										</select>
									</div>-->
								</div>
							</div>
							<div class="main-btn0main-1"> </div>
						</div>

						<div class="main-data-teble-1 table-responsive">
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
									<div class="table-custom-serch">
										<input class="input-main" type="search" id="search_table"  placeholder="Search">
									</div> 
									<div>
										<a href="javascript:;" class="btn btn-blues" id="pdfExport"> PDF</a>
										<a href="javascript:;" class="btn btn-warning" id="excelExport"> XLXS</a>
									</div>
								</div>
							</div>	
							<table id="recharge-datatable" class="" style="width:100%">
								<thead>
									<tr>
										<th> SR.No </th>
										<th> User Name</th> 
										<th> Amount </th> 
										<th> Order Id </th>
										<th> Txn No. </th>
										<th> Payment status </th>
										<th> Created At </th>
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
	var dataTable = $('#recharge-datatable').DataTable({
		processing: true,
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
		serverSide: true,
		bLengthChange: false,
		searching: false,
		bFilter: true,
		bInfo: true,
		iDisplayLength: 25,
		order: [
			[0, 'desc']
		],
		bAutoWidth: false,
		"ajax": {
			"url": "{{ route('recharge.list.ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function(d) {
				d._token = "{{csrf_token()}}";
				d.search = $('input[type="search"]').val();
				d.status = $('#status').val();
				d.transaction_type = $('#transaction_type').val();
			}
		},
		"columns": [{
				"data": "id"
			},
			{
				"data": "name"
			}, 
			{
				"data": "amount"
			},
			{
				"data": "order_id"
			},
			{
				"data": "txn_number"
			},
			{
				"data": "status"
			},
			{
				"data": "created_at"
			}
		]
	});

	$('#transaction_type, #status').change(function() {
		dataTable.draw()
	}) 
	 
	 $("#excelExport").on("click", function() {
		$(".buttons-excel").trigger("click");
	});
	
	$("#pdfExport").on("click", function() {
		$(".buttons-pdf").trigger("click");
	});
	
	$('#page_length').change(function(){
		dataTable.page.len($(this).val()).draw();
	})
	
	var debounceTimer; 
	$('#search_table').keyup(function() {
		clearTimeout(debounceTimer);
		debounceTimer = setTimeout(function() {
			dataTable.draw(); 
		}, 400); // Adjust the debounce delay (in milliseconds) as per your preference
	}); 
</script>
@endpush