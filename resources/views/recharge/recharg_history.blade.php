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
 
<div class="modal fade apprevod_request" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg model-width-1">
		<div class="modal-content">
			<div class="modal-header head-00re pb-3" style="border-bottom: 1px solid #dcdcdc;">
				<h5 class="modal-title" id="exampleModalLabel"> Action Recharge Request </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" action="{{route('recharge.wallet.action')}}" enctype="multipart/form-data">
				@csrf
				<div class="modal-body"> 
					<div class="">
						<h5> Status </h5>
						<div class="from-group rech-re-form"> 
							<select name="status" id="status" required>  
								<option value="Pending">Pending</option>
								<option value="Paid">Paid</option>
							</select>
						</div> 
					</div> 
					<input type="hidden" name="id" id="id">
					<div class="rejected_param" style="display:none;"> 
						<h5> Note </h5>
						<div class="from-group rech-re-form"> 
							<textarea name="reject_note" id="reject_note"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="justify-content: center; padding-top : 0px; border-top : 0px;"> 
					<button type="submit" class="btn new-submit-popup-btn"> Submit </button>
				</div>
			</form>
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
	var dataTable = $('#recharge-datatable').DataTable({
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
		order: [[0, 'desc'] ],
		bAutoWidth: false,			 
		"ajax":{
			"url": "{{ route('recharge.list.ajax.history') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{csrf_token()}}";
				d.search   = $('input[type="search"]').val();
				d.status   = $('select[name="status"]').val();  
			}
		},
		"columns": [
		{ "data": "id" }, 
		{ "data": "name" },   
		{ "data": "amount" }, 
		{ "data": "order_id" }, 
		{ "data": "txn_number" }, 
		{ "data": "status" }, 
		{ "data": "created_at" },
		{ "data": "action" }
		]
	}); 
	 
	function approvedRequest(obj)
	{ 
		var id = $(obj).attr('data-id');
		var status = $(obj).attr('data-status'); 
		var reject_note = $(obj).attr('data-reject_note'); 
		$('.rejected_param').hide();
		$('#reject_note').attr('required',false)
		if(status == 2)
		{
			$('.rejected_param').show();
			$('#reject_note').val(reject_note);
			$('#reject_note').attr('required',true)
		}
		$('#status').val(status)
		$('#id').val(id)
		$('.apprevod_request').modal('show');
	}
	
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
	
	
	$('#status').change(function(){
		var status = $(this).val();
		$('.rejected_param').hide();
		$('#reject_note').attr('required',false)
		if(status == 2)
		{
			$('.rejected_param').show(); 
			$('#reject_note').attr('required',true)
		}
	})
</script>
@endpush