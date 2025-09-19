@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Kyc Request Verified')
@section('header_title','Kyc Request Verified')
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
		<div class="main-order-page-1 mb-3">
			<div class="main-order-001">
				<div class="content">
					 
					<div class="container-fluid">
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
							@if($userkyc->pancard_status == 2)
								<p class="text-danger">Reject reason: {{ $userkyc->pancard_text }}</p>
							@endif
						</div>
						<div class="main-rowx-1 mt-3">
							<div class="main-row main-data-teble-1">
								<div class="row">
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2">
											<label for="username"> Pancard Number </label>
											<input type="text" autocomplete="off" name="pancard" id="pancard" placeholder="Pancard Number" value="{{ $userkyc->pancard }}" required>
										</div>
									</div>
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2 pan">
											<label for="username"> Pancard Image</label>
											<input type="file" name="pancard_image" id="pancard_image"> 
											@if(!empty($userkyc->pancard_image))
												@php
													$extension = pathinfo($userkyc->pancard_image, PATHINFO_EXTENSION);
													$imageSrc = ($extension === 'pdf') ? url('storage/kyc/pdf_image.png') : url('storage/kyc/'.$userkyc->user_id.'/'.$userkyc->pancard_image);
												@endphp 
												<a href="{{ $imageSrc }}" download>
													<img src="{{ $imageSrc }}" alt="PAN Card Image">
												</a>
											@endif
										</div>
									</div>
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2">
											<label for="username"> Pancard Verification </label>
											<select class="form-control" onchange="verifiedKyc(this, 'pancard')"> 
												<option value="">Select Pancard Verification</option>
												<option value="0" {{ $userkyc->pancard_status == 0 ? 'selected' : '' }}>Pending</option>
												<option value="1" {{ $userkyc->pancard_status == 1 ? 'selected' : '' }}>Approved</option>
												<option value="2" {{ $userkyc->pancard_status == 2 ? 'selected' : '' }}>Rejected</option>
											</select>
										</div>
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
						<div class="header-11">
							<div class="sel-main">
								<h4 class="p-0 m-0">AADHAR KYC REQUEST </h4>
								@if($userkyc->aadhar_status == 0)
									<span class="badge badge-warning">Pending</span>
								@elseif($userkyc->aadhar_status == 1)
									<span class="badge badge-success">Approved</span>
								@else
									<span class="badge badge-danger">Rejected</span>
								@endif
							</div>
							@if($userkyc->aadhar_status == 2)
								<p class="text-danger">Reject reason: {{ $userkyc->aadhar_text }} </p>
							@endif
						</div>
						<div class="main-rowx-1 mt-3">
							<div class="main-row main-data-teble-1">
								<div class="row">
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2">
											<label for="username"> Aadhar Number </label>
											<input type="text" autocomplete="off" name="aadhar" id="aadhar" placeholder="Aadhar Number" required value="{{$userkyc->aadhar}}" maxlength="12">
										</div>
									</div>
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2 pan">
											<label for="username"> Aadhar Front Image</label>
											<input type="file" name="aadhar_front" id="aadhar_front" > 
											@if(!empty($userkyc->aadhar_front))
												@php
													$extension = pathinfo($userkyc->aadhar_front, PATHINFO_EXTENSION);
													$imageSrc = ($extension === 'pdf') ? url('storage/kyc/pdf_image.png') : url('storage/kyc/'.$userkyc->user_id.'/'.$userkyc->aadhar_front);
												@endphp 
												<a href="{{ $imageSrc }}" download>
													<img src="{{ $imageSrc }}" alt="Adhar Card Image">
												</a>
											@endif
										</div>
									</div>
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2 pan">
											<label for="username"> Aadhar Back Image</label>
											<input type="file" name="aadhar_back" id="aadhar_back" > 
											@if(!empty($userkyc->aadhar_back))
												@php
													$extension = pathinfo($userkyc->aadhar_back, PATHINFO_EXTENSION);
													$imageSrc = ($extension === 'pdf') ? url('storage/kyc/pdf_image.png') : url('storage/kyc/'.$userkyc->user_id.'/'.$userkyc->aadhar_back);
												@endphp
												
												<a href="{{ $imageSrc }}" download>
													<img src="{{ $imageSrc }}" alt="Adhar Card Image">
												</a>
											@endif
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2">
											<label for="username"> Aadhar Verification </label>
											<select class="form-control" onchange="verifiedKyc(this,'aadhar')">
												<option value="">Select Aadhar Verification</option>
												<option value="0" {{ $userkyc->aadhar_status == 0 ? 'selected' : '' }}>Pending</option>
												<option value="1" {{ $userkyc->aadhar_status == 1 ? 'selected' : '' }}>Approved</option>
												<option value="2" {{ $userkyc->aadhar_status == 2 ? 'selected' : '' }}>Rejected</option>
											</select>
										</div>
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
						<div class="header-11">
							<div class="sel-main">
								<h4 class="p-0 m-0">GST KYC REQUEST </h4>
									@if($userkyc->gst_status == 0)
								<span class="badge badge-warning">Pending</span>
								@elseif($userkyc->gst_status == 1)
									<span class="badge badge-success">Approved</span>
								@else
									<span class="badge badge-danger">Rejected</span>
								@endif
							</div>
							
						</div>
						@if($userkyc->gst_status == 2)
							<p class="text-danger">Reject reason: {{$userkyc->gst_text}}</span>
						@endif
						<div class="main-rowx-1 mt-3">
							<div class="main-row main-data-teble-1">
								<div class="row">
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2">
											<label for="username"> GST Number </label>
											<input type="text" autocomplete="off" name="gst" id="gst" placeholder="GST Number" required value="{{ $userkyc->gst }}">
										</div>
									</div>
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2">
											<label for="username"> GST Image</label>
											<input type="file" name="gst_image" id="gst_image">
											
											@if(!empty($userkyc->gst_image))
												@php
													$extension = pathinfo($userkyc->gst_image, PATHINFO_EXTENSION);
													$imageSrc = ($extension === 'pdf') ? url('storage/kyc/pdf_image.png') : url('storage/kyc/'.$userkyc->user_id.'/'.$userkyc->gst_image);
												@endphp
											
												<a href="{{ $imageSrc }}"  download>
													<img src="{{ $imageSrc }}" alt="GST Image" style="height: 150px;width: 300px;margin-top: 10px;">
												</a>
											@endif
										</div>
									</div>
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2">
											<label for="username"> GST Verification </label>
											<select class="form-control" onchange="verifiedKyc(this, 'gst')">
												<option value="">Select GST Verification</option>
												<option value="0" {{ $userkyc->gst_status == 0 ? 'selected' : '' }}>Pending</option>
												<option value="1" {{ $userkyc->gst_status == 1 ? 'selected' : '' }}>Approved</option>
												<option value="2" {{ $userkyc->gst_status == 2 ? 'selected' : '' }}>Rejected</option>
											</select>
										</div>
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
						<div class="header-11">
							<div class="sel-main">
								<h4 class="p-0 m-0">BANK KYC REQUEST </h4>
								@if($userkyc->bank_status == 0)
									<span class="badge badge-warning">Pending</span>
								@elseif($userkyc->bank_status == 1)
									<span class="badge badge-success">Approved</span>
								@else
									<span class="badge badge-danger">Rejected</span>
								@endif
							</div>
							@if($userkyc->bank_status == 2)
								<p class="text-danger">Reject reason: {{ $userkyc->bank_text }}</p>
							@endif
						</div>
						<div class="main-rowx-1 mt-3">
							<div class="main-row main-data-teble-1">
								<div class="row">
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2 pan">
											<label for="username"> Bank Passbook</label>
											<input type="file" name="bank_passbook" id="bank_passbook" required>
											
											@if(!empty($userkyc->bank_passbook))
												@php
													$extension = pathinfo($userkyc->bank_passbook, PATHINFO_EXTENSION);
													$imageSrc = ($extension === 'pdf') ? url('storage/kyc/pdf_image.png') : url('storage/kyc/'.$userkyc->user_id.'/'.$userkyc->bank_passbook);
												@endphp
												
												<a href="{{ $imageSrc }}" style="height: 150px;width: 300px;margin-top: 10px;" download>
													<img src="{{ $imageSrc }}" alt="Bank Passbook Image">
												</a>
											@endif
										</div>
									</div>
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2">
											<label for="username"> Bank Name </label>
											<input type="text" autocomplete="off" name="bank_name" id="aadhar" placeholder="Bank Name" required value="{{ $userkyc->bank_name }}">
										</div>
									</div>
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2">
											<label for="username"> Account Holder Name </label>
											<input type="text" autocomplete="off" name="account_holder_name" id="account_holder_name" placeholder="Account Holder Name" required value="{{ $userkyc->account_holder_name }}">
										</div>
									</div>
									
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2">
											<label for="username"> Account Number</label>
											<input type="text" autocomplete="off" name="account_number" id="account_number" placeholder="Account Number" required value="{{ $userkyc->account_number }}">
										</div>
									</div>
									
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2">
											<label for="username"> IFSC Code</label>
											<input type="text" autocomplete="off" name="ifsc_code" id="ifsc_code" placeholder="IFSC Code" required value="{{ $userkyc->ifsc_code }}">
										</div>
									</div>
									<div class="col-lg-3 col-sm-6">
										<div class="from-group my-2">
											<label for="username"> Bank Verification </label>
											<select class="form-control" onchange="verifiedKyc(this, 'bank')">
												<option value="">Select Bank Verification</option>
												<option value="0" {{ $userkyc->bank_status == 0 ? 'selected' : '' }}>Pending</option>
												<option value="1" {{ $userkyc->bank_status == 1 ? 'selected' : '' }}>Approved</option>
												<option value="2" {{ $userkyc->bank_status == 2 ? 'selected' : '' }}>Rejected</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div> 
					</div> 
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="reject_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"> Rejection </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<form id="rejectionForm" method="post" action="{{ route('users.kyc.rejected') }} ">
				@csrf
				<div class="modal-body">
					<div class="from-group my-2">
						<label for="order-id">Reason To Reject </label>
						<textarea class="form-control" name="text" placeholder="Reason To Reject" required></textarea>
					</div>
					<input type="hidden" id="id" name="id">
					<input type="hidden" id="type" name="type">
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
<script>
	function verifiedKyc(obj, type) {
		var status = $(obj).val(); 
		var id = @json($userkyc->id);
		var text = $(obj).find("option:selected").text(); // Get selected text

		// Common SweetAlert settings
		const swalSettings = {
			title: "Are you sure you want " + text + " " + type + "?",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#31ce77",
			cancelButtonColor: "#f34943"
		};

		if (status == 2) {
			// Show reject modal for status 2 (rejected)
			Swal.fire(swalSettings).then(function(t1) {
				if (t1.value) {
					$('#rejectionForm #id').val(id);
					$('#rejectionForm #type').val(type);
					$('#reject_modal').modal('show');
				}
			});
			return false;
		}

		// Confirmation for approved or pending statuses
		Swal.fire({...swalSettings, confirmButtonText: text}).then(function(t) {
			if (t.value) {
				$.post("{{ route('users.kyc.verified') }}", {
					_token: "{{csrf_token()}}",
					id: id,
					status: status,
					type: type
				}, function(res) {
					toastrMsg(res.status, res.msg);
				}, 'json');
			}
		});
	}

</script>
@endpush