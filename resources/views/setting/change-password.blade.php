@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Change Password')
@section('header_title','Change Password')
@section('content')

<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="main-profile-1">
				
                <div class="main-roow-1" style="margin-top: 20px;">
                    <div class="main-row">
                        <div class="main-001">
                            <!-- <h5> Change Password </h5> -->
						</div>
						<form method="post" action="{{ route('update.change-password') }}">
							@csrf
							<div class="main-change-pass">
								<div class="row justify-content-center">
									<div class="col-lg-6">
										<div class="form-group my-4">
											<label>Old Password</label>
											<input type="password" name="old_password" placeholder="Old Password" class="form-control @error('old_password') is-invalid @enderror">
											@error('old_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
										</div>

										<div class="form-group my-4">
											<label>New Password</label>
											<input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="New Password">
											@error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
										</div>

										<div class="form-group my-4">
											<label>Confirm Password</label>
											<input type="password" name="new_password_confirmation" class="form-control" placeholder="Confirm Password">
										</div>

										<div class="text-center">
											<button class="btn-main-1">Submit</button>
										</div>
									</div>
								</div>
							</div>
						</form> 
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection