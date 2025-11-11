@extends('layouts.backend.app')
@section('title', config('setting.company_name') . ' - Kyc Request Verified')
@section('header_title', 'Kyc Request Verified')
@section('content')
    <style>
        button.btn-light-2 {
            padding: 7px 12px;
            font-size: 14px;
            border: none;
            background: linear-gradient(180deg, #ffc8c8 0%, #ff6e6e45 100%);
            border-radius: 5px;
        }

        .header-11 {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sel-main {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-11 {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .header-11 h5 {
            font-size: 16px;
            color: #111827;
        }

        .header-11 .form-select {
            font-size: 14px;
            padding: 4px 8px;
            height: 32px;
        }

        .reason-badge {
            background-color: #fde8e8;
            border: 1px solid #f5c2c7;
            border-radius: 4px;
            color: #842029;
            font-size: 13px;
            line-height: 1.4;
        }

    </style>
    <div class="content-page">
        <div class="container-fluid">
            <div class="main-order-page-1 mb-3">
                <div class="main-order-001">
                    <div class="content">

                        <div class="container-fluid">
                            <div class="">
                                <div class="header-11 border rounded p-3 mb-3 bg-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 w-100">

                                        {{-- LEFT: Heading + Reason --}}
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <h5 class="m-0 fw-semibold text-uppercase text-dark">PANCARD KYC REQUEST</h5>

                                            {{-- Show reason beside heading (if rejected) --}}
                                            @if ($userkyc->pancard_status == 2 && !empty($userkyc->pan_reason))
                                                <span class="reason-badge px-2 py-1 ml-2 ">
                                                    <strong>Reason:</strong> {{ $userkyc->pan_reason }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- RIGHT: Status Dropdown --}}
                                        <div class="ms-auto">
                                            <select class="form-select form-select-sm kyc-status-select select2"
                                                    data-id="{{ $userkyc->id }}"
                                                    data-field="pancard_status"
                                                    style="min-width: 160px;">
                                                <option value="0" {{ $userkyc->pancard_status == 0 ? 'selected' : '' }}>Pending</option>
                                                <option value="1" {{ $userkyc->pancard_status == 1 ? 'selected' : '' }}>Approved</option>
                                                <option value="2" {{ $userkyc->pancard_status == 2 ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
 
                                <div class="main-rowx-1 mt-3">
                                    <div class="main-row main-data-teble-1">
                                        <div class="row"> {{-- make row flex aligned --}}
                                            <div class="col-lg-4 col-md-6">
                                                <div class="form-group my-2">
                                                    <label for="username"> Pancard Number <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" autocomplete="off" name="pancard" id="pancard"
                                                        placeholder="Pancard Number" value="{{ $userkyc->pancard }}"
                                                        class="form-control" style="height: 48px;"
                                                        {{ $userkyc->pancard_status ? 'readonly' : 'required' }}>
                                                </div>
                                            </div>
                                            @if ($userkyc->pan_full_name)
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="form-group my-2">
                                                        <label for="username"> Pancard Holder Name <span
                                                                class="text-danger">*</span> </label>
                                                        <input type="text" autocomplete="off"
                                                            value="{{ $userkyc->pan_full_name }}" class="form-control"
                                                            style="height: 48px;" readonly>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($userkyc->pancard_category)
												<div class="col-lg-4 col-md-6">
													<div class="form-group my-2">
														<label for="username"> Pancard Category <span class="text-danger">*</span> </label>
														<input type="text" autocomplete="off" 
															value="{{ $userkyc->pancard_category }}"
															class="form-control"
															style="height: 48px;"
															readonly>
													</div>
												</div>
											@endif
                                            
                                            @if (!$userkyc->pancard_status)
                                                <div class="col-lg-4 col-md-6 d-flex justify-content-start my-2">
                                                    <button type="submit" class="btn btn-primary btn-main-1"
                                                        style="height: 48px;">
                                                        Submit
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="main-order-page-1 mb-3">
                <div class="main-order-001">
                    <div class="content">
                        <div class="container-fluid">
                            <div class="header-11 border rounded p-3 mb-3 bg-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 w-100">

                                    {{-- LEFT: Heading + Reason --}}
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <h5 class="m-0 fw-semibold text-uppercase text-dark">AADHAR KYC REQUEST</h5>

                                        {{-- Show reason beside heading (if rejected) --}}
                                        @if ($userkyc->aadhar_status == 2 && !empty($userkyc->aadhar_reason))
                                            <span class="reason-badge px-2 py-1 ml-2 ">
                                                <strong>Reason:</strong> {{ $userkyc->aadhar_reason }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- RIGHT: Status Dropdown --}}
                                    <div class="ms-auto">
                                        <select class="form-select form-select-sm kyc-status-select select2"
                                                data-id="{{ $userkyc->id }}"
                                                data-field="aadhar_status"
                                                style="min-width: 160px;">
                                            <option value="0" {{ $userkyc->aadhar_status == 0 ? 'selected' : '' }}>Pending</option>
                                            <option value="1" {{ $userkyc->aadhar_status == 1 ? 'selected' : '' }}>Approved</option>
                                            <option value="2" {{ $userkyc->aadhar_status == 2 ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

							<div class="row align-items-end">
								<div class="col-lg-4 col-md-6">
									<div class="form-group my-2">
										<label>Aadhar Number <span class="text-danger">*</span></label>
										<input type="text" autocomplete="off" name="aadhar" id="aadhar"
											value="{{ $userkyc->aadhar }}"
											maxlength="12"
											class="form-control"
											placeholder="Enter Aadhar Number"
											style="height: 48px;"
											{{ $userkyc->aadhar_status ? 'readonly' : 'required' }}>
									</div>
								</div>
                                 
								@if($userkyc->aadhar_full_name)
									<div class="col-lg-4 col-md-6">
										<div class="form-group my-2">
											<label>Aadhar Holder Name</label>
											<input type="text" class="form-control" value="{{ $userkyc->aadhar_full_name }}" readonly style="height: 48px;">
										</div>
									</div>
								@endif
                                @if($userkyc->aadhar_address)
									<div class="col-lg-4 col-md-6">
										<div class="form-group my-2">
											<label>Address</label>
											<input type="text" class="form-control" value="{{ $userkyc->aadhar_address }}" readonly style="height: 48px;">
										</div>
									</div>
								@endif
								
								@if($userkyc->aadhar_zip)
									<div class="col-lg-4 col-md-6">
										<div class="form-group my-2">
											<label>Zip Code</label>
											<input type="text" class="form-control" value="{{ $userkyc->aadhar_zip }}" readonly style="height: 48px;">
										</div>
									</div>
								@endif

								@if($userkyc->aadhar_dob)
									<div class="col-lg-4 col-md-6">
										<div class="form-group my-2">
											<label>Date Of Birth</label>
											<input type="text" class="form-control" value="{{ $userkyc->aadhar_dob }}" readonly style="height: 48px;">
										</div>
									</div>
								@endif
 
								@if($userkyc->aadhar_gender)
									<div class="col-lg-4 col-md-6">
										<div class="form-group my-2">
											<label>Gender</label>
											<input type="text" class="form-control" value="{{ $userkyc->aadhar_gender }}" readonly style="height: 48px;">
										</div>
									</div>
								@endif

								@if($userkyc->aadhar_front)
									<div class="col-lg-4 col-md-6">
										<div class="form-group my-2"> 
											<img src="{{ asset($userkyc->aadhar_front) }}" alt="Aadhar Front" style="max-width: 100%; height: auto; border: 1px solid #ccc; padding: 5px; border-radius: 5px;">
										</div>
									</div>
								@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
<script>
    var userKycId = @json($userkyc->id);
// Save old value (so if modal closed, dropdown resets)
$(document).on('focus', '.kyc-status-select', function () {
    $(this).data('old', $(this).val());
});

// Handle dropdown change
$(document).on('change', '.kyc-status-select', function () {
    const el    = $(this);
    const id    = el.data('id');
    const field = el.data('field');
    const status= el.val();

    // When rejected (status = 2) → show modal
    if (status == 2) {
        $('#cancelKycId').val(id);
        $('#cancelField').val(field);
        $('#cancelStatus').val(status);
        $('#cancelReasonText').val('');
        $('#cancelReasonModal').data('relatedSelect', el);
        $('#cancelReasonModal').modal('show');
        return; // stop direct update
    }

    // Otherwise update immediately
    updateKycStatus(el, id, field, status);
});

// If modal closed without submitting → revert dropdown
$('#cancelReasonModal').on('hidden.bs.modal', function () {
    const el = $(this).data('relatedSelect');
    if (el && el.length) {
        el.val(el.data('old')).trigger('change.select2');
    }
});

// Handle modal form submit
$('#cancelReasonForm').on('submit', function (e) {
    e.preventDefault();
    const id = $('#cancelKycId').val();
    const field = $('#cancelField').val();
    const status = $('#cancelStatus').val();
    const reason = $('#cancelReasonText').val().trim();

    if (!reason) {
        toastrMsg('error', 'Please enter a rejection reason.');
        return;
    }

    const el = $('#cancelReasonModal').data('relatedSelect');
    $('#cancelReasonModal').modal('hide');

    updateKycStatus(el, id, field, status, reason);
});

// Shared AJAX function
function updateKycStatus(el, id, field, status, reason = '') 
{
    // alert(id)
    // alert(field)
    // alert(status)
    // alert(reason)
    // return false;
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
        success: function (res) {
            toastrMsg('success', res.message || 'Status updated');
            window.location.href = "{{ url('users/kyc/verified') }}/"+userKycId
        },
        error: function (xhr) {
            toastrMsg('error', xhr.responseJSON?.message || 'Update failed');
            el.val(el.data('old')).trigger('change.select2');
        },
        complete: function () {
            el.prop('disabled', false);
        }
    });
}

</script>
@endpush
