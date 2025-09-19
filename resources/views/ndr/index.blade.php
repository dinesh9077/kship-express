@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Ndr')
@section('header_title','Ndr')
@section('content') 
<link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" rel="stylesheet">
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
                    <div class="inner-page-heading">
					</div>
                    <div class="ordr-main-001">
                        <ul id="tab">
							<li class="active">
								<div class="main-calander-11"> 
									<div class="main-data-teble-1 table-responsive">
										<table id="neworder_datatable" class="" style="width:100%">
											<thead>
												<tr>
													<th> Sr.No</th>
													<th> Order Id</th>
													<th> Seller Details </th>
													<th> Customer details </th>
													<th> Package Details </th>
													<th> Payment </th>
													<th> Status </th>
													<th> Action </th>
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
<div class="modal fade bd-example-modal-lg main-bg0-021 raisendr" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header pb-0" style="border: none;">
                <h5 class="modal-title pick-up0" id="exampleModalLabel"> Add Warehouse </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="NDRForm" action="{{route('ndr.raiserequest')}}" method="post" enctype="multipart/form-data"> 
				@csrf
				<div class="modal-body"> 
					<input type="hidden" name="order_id" id="ndr_order_id" >
					<input type="hidden" name="shipping_company_id" id="ndr_shipping_company_id" >
					<input type="hidden" name="awb_number" id="ndr_awb_number" >
					<div class="form-group">
                        <select name="action" class="form-control" id="choose">
                            <option value="">Choose Action</option>
                            <option value="re-attempt">Re-Attempt</option>
                            <option value="change_phone">Update Phone Number</option>
                            <option value="change_address">Update Address</option>
                        </select>
                    </div>
                    <div class="form-group" id="date-picker">
                        <label>Re-Attempt Date </label>
                        <input type="date" name="reattemptdate" class="form-control">
                    </div>
					<div class="form-group">
                        <label>Remark</label>
                        <textarea class="form-control" name="remarks" placeholder="Enter Remark"></textarea>
                    </div>
				</div>
				<div class="modal-footer" style="margin: auto;border: none;"> 
					<button type="sumbit" class="btn btn-primary btn-main-1"> Submit </button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
@push('js')  
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 

 <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>
<script>

     $(document).on('click','.ndrModal', function() 
	{  
	    var shipping_id = $(this).data('shipping_id');
	    var order_id = $(this).data('id');
	    var awb_number = $(this).data('awb_number');
	    $('#ndr_shipping_company_id').val(shipping_id);
	    $('#ndr_order_id').val(order_id);
	    $('#ndr_awb_number').val(awb_number);
	    $('.raisendr').modal('show');
	});
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
			"url": "{{ route('ndr.ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{csrf_token()}}";
				d.search   = $('input[type="search"]').val();
				d.status   = "{{$status}}";  
			}
		},
		"columns": [
		{ "data": "id" }, 
		{ "data": "order_id" }, 
		{ "data": "seller_details" }, 
		{ "data": "customer_details" }, 
		{ "data": "package_details" }, 
		{ "data": "total_amount" }, 
		{ "data": "status_courier" },   
		{ "data": "action" }
		]
	}); 
	
	$('#neworder_datatable').on('draw.dt', function() {
		$('[data-toggle="tooltip"]').tooltip({
			position: {
				my: "left bottom", // the "anchor point" in the tooltip element
				at: "left top", // the position of that anchor point relative to selected element
			}
		});
	});
	$('#NDRForm').submit(function(event) {
		event.preventDefault();  
		$(this).find('button').prop('disabled',true); 
		var formData = new FormData(this); 
		formData.append('_token',"{{csrf_token()}}"); 
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
			    console.log('response',res);
			    $('.raisendr').modal('hide');
			    if(res.status != "error")
			    {
			        toastrMsg('success', res.msg);
			    }else
			    {
			        toastrMsg('error',res.msg);
			    }
                // Reload window after 3 seconds
                setTimeout(function() {
                        location.reload();
                    }, 3000);
				$('button').prop('disabled',false);  
				
			},
			error: function(xhr, status, error) {
                    console.error(xhr.responseText);
            }
		});
	});

</script>

@endpush