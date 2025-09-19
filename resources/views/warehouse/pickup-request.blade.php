@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Pickup Request')
@section('header_title', 'Pickup Request')
@section('content') 
<div class="content-page">
    <div class="content"> 
        <div class="container-fluid">
            <div class="main-order-page-1">
				<div class="main-order-001">
					<div class="main-create-order">
						<div class="main-disolay-felx" style="margin-top: 0 !important;">
							@if(config('permission.pickup_request.add'))	
								<div class="main-btn0main-1">
									<a href="{{ route('pickup.request.create') }}" onclick="createPickupRequest(event)"> <button class="btn-main-1"> Create Pickup </button> </a>
								</div>
							@endif
						</div>
						
						<div class="main-data-teble-1 table-responsive">
							<table id="pickurequest-datatable" class="" style="width:100%">
								<thead>
									<tr>
										<th> # </th>
										<th> Shipping Company</th>
										<th> Request Id</th>
										<th> Warehouse Location</th>
										<th> Expected Package Count </th> 
										<th> Pickup Date And Time</th>
										<th> Shipper's Contact No </th> 
										<th> Status </th> 
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
<div class="modal fade bd-example-modal-lg" id="pickupRequestModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> 
     <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="pickupRequestForm" action="{{ route('pickup.request.create') }}" method="post">
                @csrf 
                <div class="modal-body">
                    <div class="form-group">
                        <label>Shipping Company</label>
                        <select name="shipping_company_id" class="form-control select2" required>
							<option value="">Select Shipping Company</option>
							@foreach($shippingCompanies as $shippingCompany)
								<option value="{{ $shippingCompany->id }}">{{ $shippingCompany->name }}</option>
							@endforeach
						</select>
                    </div>
                    <div class="form-group">
                        <label>Warehouse Location</label>
                        <select name="warehouse_id" class="form-control select2" required>
							<option value="">Select Warehouse Name</option>
							@foreach($warehouses as $warehouse)
								<option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }} ({{ $warehouse->address }})</option>
							@endforeach
						</select>
                    </div>
                    <div class="form-group">
                        <label>Expected Package</label>
                        <input type="text" name="expected_package_count" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" id="expected_package_count" class="form-control"  required>
                    </div>
                    <div class="form-group">
                        <label>Pickup Date</label>
                        <input type="text" name="pickup_date" id="pickup_date" class="form-control datepicker" required>
                    </div>
                    <div class="form-group">
                        <label>Pickup Start Time</label>
                        <input type="time" name="pickup_start_time" id="pickup_start_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Pickup End Time</label>
                        <input type="time" name="pickup_end_time" id="pickup_end_time" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('js') 
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 
<script>
	function createPickupRequest(event) {
		event.preventDefault();  
		$('#pickupRequestForm').trigger('reset'); 
		$('#pickupRequestForm select').val('').trigger('change'); 
		$('#pickupRequestModal').modal('show');
	}
 
	var dataTable = $('#pickurequest-datatable').DataTable({
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
		order: [[0, 'desc']],
		bAutoWidth: false,			 
		"ajax": {
			"url": "{{ route('pickup.request.ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{ csrf_token() }}";
				d.search   = $('input[type="search"]').val();   
			}
		},
		"columns": [
			{ "data": "id" }, 
			{ "data": "shipping_company_id" }, 
			{ "data": "pickup_id" }, 
			{ "data": "warehouse_id" }, 
			{ "data": "expected_package_count" },
			{ "data": "pickup_date" }, 
			{ "data": "shipper_contact" }, 
			{ "data": "status" }, 
			{ "data": "created_at" },   
			{ "data": "action" }
		]
	}); 
	
	function cancelPickupRequest(obj, event)
	{
		event.preventDefault();
		Swal.fire({
			title:"Are you sure you want to cancel this pickup request?",
			text:"You can't undo this action.",
			type:"warning",
			showCancelButton:!0,
			confirmButtonColor:"#31ce77",
			cancelButtonColor:"#f34943",
			confirmButtonText:"Yes, Cancel!"
			}).then(function (t) {
			if(t.value)
			{
				location.href = obj;
			}
		})
	}
</script>
@endpush