@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Recharge History')
@section('header_title','Recharge History')
@section('content') 
<div class="content-page">
    <div class="content"> 
        <div class="container-fluid">
            <div class="main-order-page-1">                    
                <div class="main-order-001">  
                    <div class="main-create-order"> 
                        <div class="main-data-teble-1 table-responsive">
                            <table id="recharge-datatable" class="" style="width:100%">
                                <thead>
                                    <tr>
                                        <th> SR.No </th>
                                        <th> User Name</th> 
                                        <th> Transaction Type </th>
                                        <th> Amount </th>
                                        <th> Reciept </th>
                                        <th> Note </th>
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
			<div class="modal-header head-00re pb-0" style="border: none;">
				<h5 class="modal-title" id="exampleModalLabel"> Action Recharge Request </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" action="{{ route('recharge.wallet.action') }}" enctype="multipart/form-data">
				@csrf
				<div class="modal-body"> 
					<div class="">
						<h5> Transaction Type </h5>
						<div class="from-group rech-re-form"> 
							<select name="status" id="status" required> 
								<option value="0">Pending</option>
								<option value="1">Approved</option>
								<option value="2">Rejected</option>
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
				<div class="modal-footer" style="justify-content: center;"> 
					<button type="submit" class="btn btn-primary btn-main-1"> Submit </button>
				</div>
			</form>
		</div>
	</div>
</div>	
@endsection
@push('js') 
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 
<script> 
	var dataTable = $('#recharge-datatable').DataTable({
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
		{ "data": "transaction_type" },  
		{ "data": "amount" }, 
		{ "data": "payment_receipt" }, 
		{ "data": "note" }, 
		{ "data": "status" }, 
		{ "data": "created_at" },
		{ "data": "action" }
		]
	}); 
	 
	dataTable.columns(8).visible(false);
	dataTable.columns(4).visible(false); 
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