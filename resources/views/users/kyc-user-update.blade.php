@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - KYC REQUEST')
@section('header_title','Kyc Request')
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
</style>
<div class="content-page">
	<div class="container-fluid">
		<div class="main-create-order mt-3">
			<div class="main-rowx-1">
				<div class="main-order-001">
					<div class="content">
						<form method="post" class="customer_form" action="{{ route('users.pancard.update') }}" enctype="multipart/form-data">
							@csrf
							<div class="">
								<div class="header-11">
									<div class="sel-main">
										<h4 class="p-0 m-0">PANCARD KYC REQUEST </h4>
										@if($userkyc->pancard_status == 0)
											<span class="badge badge-warning">Pending</span>
										@elseif($userkyc->pancard_status == 1)
											<span class="badge badge-success">Approved</span>
										@else
											<span class="badge badge-danger">Rejected</span>
										@endif
									</div>

								</div> 
								<div class="main-rowx-1 mt-3">
									<div class="main-row main-data-teble-1">
										<div class="row"> {{-- make row flex aligned --}}
											<div class="col-lg-4 col-md-6">
												<div class="form-group my-2">
													<label for="username"> Pancard Number <span class="text-danger">*</span> </label>
													<input type="text" autocomplete="off" name="pancard" id="pancard"
														placeholder="Pancard Number"
														value="{{ $userkyc->pancard }}"
														class="form-control"
														style="height: 48px;"
														{{ $userkyc->pancard_status ? 'readonly' : 'required' }}>
												</div>
											</div>
											@if($userkyc->pan_full_name)
												<div class="col-lg-4 col-md-6">
													<div class="form-group my-2">
														<label for="username"> Pancard Holder Name <span class="text-danger">*</span> </label>
														<input type="text" autocomplete="off" 
															value="{{ $userkyc->pan_full_name }}"
															class="form-control"
															style="height: 48px;"
															readonly>
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
											@if(!$userkyc->pancard_status)
												<div class="col-lg-4 col-md-6 d-flex justify-content-start my-4">
													<button type="submit" class="btn btn-primary btn-main-1"  style="height: 48px;">
														Submit
													</button>
												</div>
											@endif
										</div>
									</div>
								</div> 
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<div class="main-rowx-1">
				<div class="main-order-001">
					<div class="content">
						<form id="aadharForm" method="post" class="customer_form" action="{{ route('users.aadhar.update') }}" enctype="multipart/form-data">
							@csrf

							<div class="header-11">
								<div class="sel-main">
									<h4 class="p-0 m-0">AADHAR KYC REQUEST</h4>
									@if($userkyc->aadhar_status == 0)
										<span class="badge badge-warning">Pending</span>
									@elseif($userkyc->aadhar_status == 1)
										<span class="badge badge-success">Approved</span>
									@else
										<span class="badge badge-danger">Rejected</span>
									@endif
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

								@if(!$userkyc->aadhar_status)
									<div class="col-lg-4 col-md-6 my-2 d-flex align-items-end">
										<button type="button" id="sendOtpBtn" class="btn btn-primary btn-main-1" style="height: 48px;">
											Send OTP
										</button>
									</div>
								@endif
							</div>

							{{-- Hidden OTP Section (will show after OTP sent) --}}
							<div id="otpSection" class="row mt-3 d-none">
								<div class="col-lg-4 col-md-6">
									<div class="form-group">
										<label>Enter OTP <span class="text-danger">*</span></label>
										<input type="text" name="otp" id="otp" class="form-control" maxlength="6" placeholder="Enter OTP" required>
									</div>
								</div>

								<input type="hidden" name="request_id" id="request_id">
								<div class="col-lg-4 col-md-6 my-2 d-flex align-items-end">
									<button type="submit" class="btn btn-primary btn-main-1" style="height: 48px;">
										Verify OTP
									</button>
								</div>	 
							</div>
						</form>
					</div>
				</div>
			</div>
 
		</div>
	</div>
</div>
@endsection
@push('js')
<script>
	$(document).ready(function() {
		$('#sendOtpBtn').on('click', function(e) {
			e.preventDefault();

			let aadhar = $('#aadhar').val().trim();
			if (aadhar.length !== 12) {
				toastrMsg("warning", 'Please enter a valid 12-digit Aadhar number.'); 
				return;
			}

			// Disable button to prevent multiple clicks
			$('#sendOtpBtn').prop('disabled', true).text('Sending...');

			$.ajax({
				url: "{{ route('users.aadhar.sendOtp') }}", // 👈 your route for OTP
				type: "POST",
				data: {
					_token: "{{ csrf_token() }}",
					aadhar: aadhar
				},
				success: function(res) {
					if (res.status === 'success') {
						toastrMsg(res.status, res.msg);
						$('#sendOtpBtn').prop('disabled', false).text('Send OTP').hide();
						$('#otpSection').removeClass('d-none');
						$('#request_id').val(res.request_id);
					} else {
						toastrMsg(res.status, res.msg || 'Failed to send OTP.'); 
					}
				},
				error: function(xhr) {
					toastrMsg("error", 'Something went wrong while sending OTP.');  
				},
				complete: function() {
					$('#sendOtpBtn').prop('disabled', false).text('Send OTP');
				}
			});
		});
	});
</script>
@endpush
