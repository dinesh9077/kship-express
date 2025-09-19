@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - General Setting')
@section('header_title','General Setting')
@section('content')

<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
			<div class="main-order-page-1">
				<div class="main-order-001">
					<form class="generalForm" action="" method="post" enctype="multipart/form-data">
						@csrf
						<div class="main-rowx-1">
							<div class="main-row main-data-teble-1">
								<div class="row">
									<div class="col-lg-6 col-md-6">
										<div class="from-group my-2">
											<label for="first-name"> Company Name </label>
											<input type="text" name="company_name" value="{{config('setting.company_name')}}" placeholder="Company Name">
										</div>
									</div>
									<div class="col-lg-6 col-md-6">
										<div class="from-group my-2">
											<label for="first-name"> Company Contact Number </label>
											<input type="text" name="company_contact" value="{{config('setting.company_contact')}}" placeholder="Company Contact Number">
										</div>
									</div>
									<div class="col-lg-6 col-md-6">
										<div class="from-group my-2">
											<label for="first-name"> Company Email </label>
											<input type="email" name="company_email" value="{{config('setting.company_email')}}" placeholder="Company Email">
										</div>
									</div>
									<div class="col-lg-6 col-md-6">
										<div class="from-group my-2">
											<label for="username"> Login Logo </label>
											<input class="form-control" type="file" name="login_logo" id="login_logo">
											@if(!empty(config('setting.login_logo')))
											<br>
											<img src="{{asset('storage/settings/'.config('setting.login_logo'))}}" style="width:15%;padding: 10px 0 0 0;">
											@endif
										</div>
									</div>
									
									<div class="col-lg-6 col-md-6">
										<div class="from-group my-2">
											<label for="password"> Header Logo </label>
											<input class="form-control" type="file"  name="header_logo" id="header_logo">
											@if(!empty(config('setting.header_logo')))
											<br>
											<img src="{{asset('storage/settings/'.config('setting.header_logo'))}}" style="width:15%;padding: 10px 0 0 0;">
											@endif
										</div>
									</div>
									
									<div class="col-lg-6 col-md-6">
										<div class="from-group my-2">
											<label for="first-name"> Fevicon Icon </label>
											<input class="form-control gt-txt" type="file"  name="fevicon_icon" id="fevicon_icon">
											@if(!empty(config('setting.fevicon_icon')))
											<br>
											<img src="{{asset('storage/settings/'.config('setting.fevicon_icon'))}}" style="width:15%;padding: 10px 0 0 0;">
											@endif
										</div>
									</div> 
									
									<div class="col-lg-6 col-md-6">
										<div class="from-group my-2">
											<label for="first-name"> Bussiness Currency </label>
											<select class="form-control" id="currency" name="currency"> 
												<option value="₹">INDIA - INR - ₹</option>
											</select>
										</div>
									</div> 
								</div>  
							</div>
						</div> 
						@if(config('permission.general_setting.edit'))
							<div class="text-align-center">
								<button class="btn-main-1"> Submit </button>
							</div>
						@endif
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection