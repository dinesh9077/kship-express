@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Shipping Charge')
@section('header_title','Shipping Charge')
@section('content') 
<div class="content-page">
    <div class="content">
		
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="main-order-page-1">                    
               <div class="main-order-001">  
                    <div class="main-create-order">
                        <div class="main-disolay-felx" style="margin-top: 0 !important;">
                            <div class="main-btn0main-1">
                                <a href="javascript:;"  data-toggle="modal" data-target=".excelUpload"> <button class="btn-main-1"> Import Excel </button> </a>
        					</div>
        				</div>
        				
                        <div class="main-data-teble-1 table-responsive">
                            <table id="service-datatable" class="" style="width:100%">
                                <thead>
                                    <tr>
                                        <th> SR.No </th> 
                                        <th> Origin Pincode</th>
                                        <th> Origin City </th>
                                        <th> Origin State </th>
                                        <th> Origin Center </th> 
                                        <th> Origin Serviciable</th>
                                        <th> Destination Pincode</th>
                                        <th> Destination City</th>
                                        <th> Destination State</th>
                                        <th> Shipping Charge</th>
                                        <th> Status</th>
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

<div class="modal fade excelUpload" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> Upload Excel </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<form method="post" id="exceluploadForm" action="{{route('service.import')}}" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
                    <div class="from-group my-2">
                        <label for="order-id"> Upload Excel File </label>
                        <input type="file" class="form-control" name="excel_file" required>
					</div>  
				</div>
				<div class="modal-footer"> 
					<button type="submit" class="btn btn-primary btn-main-1"> Save </button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade updateCharge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> Update Shipping Charge</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<form method="post" id="updateChargeForm" action="{{route('service.update.charge')}}" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
                    <div class="from-group my-2">
                        <label for="order-id"> Shipping Charge </label>
                        <input type="text" class="form-control" name="shipping_charge" id="shipping_charge" required>
					</div>  
					<input type="hidden" name="id" id="update_id">
					<div class="from-group my-2">
                        <label for="order-id"> Status </label>
                        <select name="status" id="status" class="form-control">
							<option value="1">Active <option>
							<option value="0">In-active <option>
						</select> 
					</div>  
				</div>
				<div class="modal-footer"> 
					<button type="submit" class="btn btn-primary btn-main-1"> Save </button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
@push('js') 
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 
<script>
	
	var dataTable = $('#service-datatable').DataTable({
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
			"url": "{{ route('service.list.ajax') }}",
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
		{ "data": "origin_pincode" }, 
		{ "data": "origin_city" }, 
		{ "data": "origin_state" }, 
		{ "data": "origin_center" },  
		{ "data": "origin_serviceable" }, 
		{ "data": "des_pincode" },   
		{ "data": "des_city" },   
		{ "data": "des_state" },   
		{ "data": "shipping_charge" },   
		{ "data": "status" },   
		{ "data": "created_at" },   
		{ "data": "action" }
		]
	}); 
	 
	$('#exceluploadForm').submit(function(event) 
	{
		event.preventDefault();   
		run_waitMe($('body'), 1, 'win8')
		var formData = new FormData(this); 
		formData.append('_token','{{csrf_token()}}'); 
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
				$('body').waitMe('hide');
				if(res.status == "error")
				{
					toastrMsg(res.status,res.msg);
				}
				else
				{ 
					$('.excelUpload').modal('show');
					dataTable.draw();
					toastrMsg(res.status,res.msg);
				}
			} 
		});
	});
	
	$('#updateChargeForm').submit(function(event) 
	{
		event.preventDefault();   
		run_waitMe($('body'), 1, 'win8')
		var formData = new FormData(this); 
		formData.append('_token','{{csrf_token()}}'); 
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
				$('body').waitMe('hide');
				if(res.status == "error")
				{
					toastrMsg(res.status,res.msg);
				}
				else
				{ 
					$('.updateCharge').modal('hide');
					dataTable.draw();
					toastrMsg(res.status,res.msg);
				}
			} 
		});
	});
	
	function editPrice(obj,event)
	{
		event.preventDefault();
		var id = $(obj).attr('data-id');
		var shipping_charge = $(obj).attr('data-shipping_charge');
		var status = $(obj).attr('data-status');
		
		$('#update_id').val(id);
		$('#shipping_charge').val(shipping_charge);
		$('#status').val(status);
		$('.updateCharge').modal('show');
	}
	
	  
</script>
@endpush