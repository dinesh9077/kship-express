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
    </style>
    <div class="content-page">
        <div class="container-fluid">
            <div class="main-order-page-1 mb-3">
                <div class="main-order-001">
                    <div class="content">

                        <div class="container-fluid">
                            <div class="">
                                <div class="header-11">
                                    <div class="sel-main">
                                        <h4 class="p-0 m-0">PANCARD KYC REQUEST </h4>
                                        @if ($userkyc->pancard_status == 0)
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
