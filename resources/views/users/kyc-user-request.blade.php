@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Client Kyc Request')
@section('header_title','Client Kyc Request')
@section('content') 
  <style>
	/* Custom wrap class */
table.dataTable td.wrap-text {
  white-space: normal !important;
  word-break: break-word;
  word-wrap: break-word;
  max-width: 320px;
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
        				<div class="main-data-table-1 table-responsive">
							<table id="kycRequestDatatable" class="table table-striped" style="width:100%">
								<thead>
									<tr>
										<th>Sr. No</th>
										<th>User Name</th>
										<th>User Email</th>
										<th>PAN Number</th>
										<th>PAN Holder Name</th>
										<th>PAN Category</th>
										<th>Aadhaar Profile</th>
										<th>Aadhaar Holder Name</th>
										<th>Aadhaar Address</th>
										<th>Aadhaar Zip Code</th>
										<th>Aadhaar Date of Birth</th>
										<th>Aadhaar Gender</th>
										<th>PAN Status</th>
										<th>Aadhaar Status</th>
										<th>Created At</th>
										<th>Action</th>
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

<!-- Cancel Reason Modal -->
<div class="modal fade" id="cancelReasonModal" tabindex="-1" aria-labelledby="cancelReasonModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cancelReasonModalLabel">Cancellation Reason</h5>
       		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button>
      </div>
	  
      <div class="modal-body">
        <form id="cancelReasonForm">
          <input type="hidden" name="kyc_id" id="cancelKycId">
          <input type="hidden" name="field" id="cancelField">
          <input type="hidden" name="status" id="cancelStatus" value="2">
          <div class="mb-3">
            <label for="cancelReasonText" class="form-label fw-semibold">Please enter reason for cancellation</label>
            <textarea class="form-control" id="cancelReasonText" rows="3" placeholder="Enter reason..." required></textarea>
          </div>
          <div class="text-end">
            <button type="submit" class="btn new-submit-popup-btn">Submit</button>
          </div>
        </form>
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
			{ "data": "user_name" },
			{ "data": "user_email" },
			{ "data": "pan_number" },
			{ "data": "pan_holder_name" },
			{ "data": "pan_category" },
			{ "data": "aadhaar_profile" },
			{ "data": "aadhaar_holder_name" },
			{ "data": "aadhaar_address" , className:'wrap-text'},
			{ "data": "aadhaar_zip_code" },
			{ "data": "aadhaar_dob" },
			{ "data": "aadhaar_gender" },
			{ "data": "pancard_status" },
			{ "data": "aadhar_status" },
			{ "data": "created_at" },
			{ "data": "action" }
		] 
	}); 

	dataTable.on('draw', function() {
		$('.kyc-status-select').select2({
			minimumResultsForSearch: Infinity, // hide search box
			width: 'style'
		});
	});
	
	$('.search_kyc').click(function (){
		dataTable.draw();	
	})
 
	$(document).on('change', '.kyc-status-select', function() {
		const el    = $(this);
		const id    = el.data('id');
		const field = el.data('field');
		const status= el.val();

		// 🔹 Case 1: Status == 2 → show reason modal
		if (status == 2) {
			// Store context in modal inputs
			$('#cancelKycId').val(id);
			$('#cancelField').val(field);
			$('#cancelStatus').val(status);
			$('#cancelReasonText').val(''); // reset
			$('#cancelReasonModal').modal('show');

			// Reset dropdown to previous value until confirm
			el.val(el.data('old') ?? '').trigger('change.select2');
			return; // stop normal flow
		}

		// 🔹 Case 2: Normal update
		updateKycStatus(el, id, field, status);
	});

	// 🔹 Save previous value for rollback if cancel modal opens
	$(document).on('focus', '.kyc-status-select', function() {
		$(this).data('old', $(this).val());
	});
 

	// 🔹 Form submit handler
	$('#cancelReasonForm').on('submit', function(e) {
		e.preventDefault();

		const id      = $('#cancelKycId').val();
		const field   = $('#cancelField').val();
		const status  = $('#cancelStatus').val();
		const reason  = $('#cancelReasonText').val();

		if (!reason.trim()) {
			toastrMsg('error', 'Please enter a cancellation reason.');
			return;
		}

		// Hide modal
		$('#cancelReasonModal').modal('hide');

		// Find the matching dropdown in table
		const el = $(`.kyc-status-select[data-id="${id}"][data-field="${field}"]`);

		// Trigger update with reason included
		updateKycStatus(el, id, field, status, reason);
	});

	// 🔹 Shared function for AJAX update
	function updateKycStatus(el, id, field, status, reason = '') {
		el.prop('disabled', true);

		$.ajax({
			url: "{{ route('users.kyc.update-status') }}",
			method: "POST",
			data: {
				id,
				field,
				status,
				reason,
				_token: "{{ csrf_token() }}"
			},
			success: function(res) {
				toastrMsg('success', res.message || 'Status updated');
				const row = el.closest('tr');
				dataTable.row(row).invalidate().draw(false);
			},
			error: function(xhr) {
				toastrMsg('error', xhr.responseJSON?.message || 'Failed to update.');
				dataTable.ajax.reload(null, false);
			},
			complete: function() {
				el.prop('disabled', false);
			}
		});
	}
</script>
@endpush