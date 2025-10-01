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
								@if($userkyc->pancard_status == 2)
									<span>Reason : {{ $userkyc->pancard_text }}</span>
								@endif
								<div class="main-rowx-1 mt-3">
									<div class="main-row main-data-teble-1">
										<div class="row">
											<div class="col-lg-4 col-md-6">
												<div class="from-group my-2">
													<label for="username"> Pancard Number{{--  <span class="text-danger">*</span> --}}</label>
													<input type="text" autocomplete="off" name="pancard" id="pancard" placeholder="Pancard Number" required value="{{ $userkyc->pancard }}" style="height : 48px;" >
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="from-group my-2">
													<label for="username"> Pancard Image{{--  <span class="text-danger">*</span> --}}</label>
													<input type="file" name="pancard_image" id="pancard_image"  style="height : 48px;" {{ empty($userkyc->pancard_image) ? 'required' : '' }}>
													@if(!empty($userkyc->pancard_image))
														<img src="{{ url('storage/kyc/'.$userkyc->user_id.'/'.$userkyc->pancard_image) }}" style="height: 150px;width: 300px;margin-top: 10px;">
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
								
								@if($userkyc->pancard_status != 1)
									<div class="text-align-end">
										<button class="btn-main-1" id="customer_submit"> Submit </button>
									</div>
								@endif
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<div class="main-rowx-1">
				<div class="main-order-001">
					<div class="content">
						<form method="post" class="customer_form" action="{{ route('users.aadhar.update') }}" enctype="multipart/form-data">
							@csrf
							<div class=""> 
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
								</div>
								@if($userkyc->aadhar_status == 2)
									<span>Reason : {{ $userkyc->aadhar_text }}</span>
								@endif
								<div class="main-rowx-1 mt-3">
									<div class="main-row main-data-teble-1">
										<div class="row">
											<div class="col-lg-4 col-md-6">
												<div class="from-group my-2">
													<label for="username"> Aadhar Number{{--  <span class="text-danger">*</span> --}}</label>
													<input type="text" autocomplete="off" name="aadhar" id="aadhar" placeholder="Aadhar Number" required value="{{ $userkyc->aadhar }}"  maxlength="12" style="height : 48px;">
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="from-group my-2">
													<label for="username"> Aadhar Front Image{{--  <span class="text-danger">*</span> --}}</label>
													<input type="file" style="height: 48px;" name="aadhar_front" id="aadhar_front" {{ empty($userkyc->aadhar_front) ? 'required' : '' }}>
													@if(!empty($userkyc->aadhar_front))
														<img src="{{url('storage/kyc/'.$userkyc->user_id.'/'.$userkyc->aadhar_front)}}" style="height: 150px;width: 300px;margin-top: 10px;">
													@endif
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="from-group my-2">
													<label for="username"> Aadhar Back Image{{--  <span class="text-danger">*</span> --}}</label>
													<input type="file" name="aadhar_back" style="height: 48px;" id="aadhar_back" {{ empty($userkyc->aadhar_back) ? 'required' : '' }}>
													@if(!empty($userkyc->aadhar_back))
														<img src="{{url('storage/kyc/'.$userkyc->user_id.'/'.$userkyc->aadhar_back)}}" style="height: 150px;width: 300px;margin-top: 10px;">
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
								@if($userkyc->aadhar_status != 1)
									<div class="text-align-end">
										<button class="btn-main-1" id="customer_submit"> Submit </button>
									</div>
								@endif
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="main-rowx-1">
				<div class="main-order-001">
					<div class="content">
						<form method="post" class="customer_form" action="{{ route('users.gst.update') }}" enctype="multipart/form-data">
							@csrf
							<div class="">
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
									<span>Reason : {{$userkyc->gst_text}}</span>
								@endif
								<div class="main-rowx-1 mt-3">
									<div class="main-row main-data-teble-1">
										<div class="row">
											<div class="col-lg-4 col-md-6">
												<div class="from-group my-2">
													<label for="username"> GST Number{{--  <span class="text-danger">*</span> --}}</label>
													<input type="text" autocomplete="off" name="gst" id="gst" placeholder="GST Number" required value="{{ $userkyc->gst }}" >
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="from-group my-2">
													<label for="username"> GST Image{{--  <span class="text-danger">*</span> --}}</label>
													<input type="file" name="gst_image" style="height: 48px;" id="gst_image" {{ empty($userkyc->gst_image) ? 'required' : '' }}>
													@if(!empty($userkyc->gst_image))
														<img src="{{url('storage/kyc/'.$userkyc->user_id.'/'.$userkyc->gst_image)}}" style="height: 150px;width: 300px;margin-top: 10px;">
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
								@if($userkyc->gst_status != 1)
									<div class="text-align-end">
										<button class="btn-main-1" id="customer_submit"> Submit </button>
									</div>
								@endif
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="main-rowx-1">
				<div class="main-order-001">
					<div class="content">
						<form method="post" class="customer_form" action="{{ route('users.bank.update') }}" enctype="multipart/form-data">
							@csrf
							<div class="">
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
								</div>
								@if($userkyc->bank_status == 2)
									<span>Reason : {{ $userkyc->bank_text }}</span>
								@endif
								<div class="main-rowx-1 mt-3">
									<div class="main-row main-data-teble-1">
										<div class="row">
											
											<div class="col-lg-4 col-md-6">
												<div class="from-group my-2">
													<label for="username"> Bank Name{{--  <span class="text-danger">*</span> --}}</label>
													<input type="text" autocomplete="off" name="bank_name" id="aadhar" placeholder="Bank Name" required value="{{ $userkyc->bank_name }}">
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="from-group my-2">
													<label for="username"> Account Holder Name{{--  <span class="text-danger">*</span> --}}</label>
													<input type="text" autocomplete="off" name="account_holder_name" id="account_holder_name" placeholder="Account Holder Name" required value="{{ $userkyc->account_holder_name }}">
												</div>
											</div>
  
											<div class="col-lg-4 col-md-6 ">
												<div class="from-group my-2">
													<label for="username"> Account Number {{-- <span class="text-danger">*</span>--}}</label>
													<input type="text" autocomplete="off" name="account_number" id="account_number" placeholder="Account Number" required value="{{ $userkyc->account_number }}">
												</div>
											</div>

											<div class="col-lg-4 col-md-6 mt-2">
												<div class="from-group my-2">
													<label for="username"> IFSC Code {{-- <span class="text-danger">*</span>--}}</label>
													<input type="text" autocomplete="off" name="ifsc_code" id="ifsc_code" placeholder="IFSC Code" required value="{{ $userkyc->ifsc_code }}">
												</div>
											</div>
											<div class="col-lg-4 col-md-6 mt-2">
												<div class="from-group my-2">
													<label for="username"> Bank Passbook {{-- <span class="text-danger">*</span>--}}</label>
													<input type="file" name="bank_passbook" style="height: 48px;" id="bank_passbook" {{ empty($userkyc->bank_passbook) ? 'required' : '' }}>
													@if(!empty($userkyc->bank_passbook))
														<img src="{{url('storage/kyc/'.$userkyc->user_id.'/'.$userkyc->bank_passbook)}}" style="height: 150px;width: 300px;margin-top: 10px;">
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
								@if($userkyc->bank_status != 1)
									<div class="text-align-end">
										<button class="btn-main-1" id="customer_submit"> Submit </button>
									</div>
								@endif
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection 