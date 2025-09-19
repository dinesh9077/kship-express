@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Create User')
@section('header_title','Create User')
@section('content')
<style>
	button.btn-light-2 {
		padding: 7px 12px;
		font-size: 14px;
		border: none;
		background: linear-gradient(180deg, #ffc8c8 0%, #ff6e6e45 100%);
		border-radius: 5px;
	}

	.main-row.main-data-teble-1 .row {
		row-gap: 15px;
	}

	.main-heading-1.pt-lg-0.pb-lg-2.px-lg-0 h4 {
		color: #000;
		font-size: 20px;
	}
</style>
<div class="content-page">
	<div class="content">
		<form id="userForm" method="post" class="customer_form" action="{{route('users.store')}}" enctype="multipart/form-data">
			@csrf
			<div class="container-fluid">
				<div class="main-rowx-1 mt-3">
					<div class="main-order-001">
						<div class="main-row main-data-teble-1">
							<div class="row">
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="username"> Company Name </label>
										<input type="text" autocomplete="off" name="company_name" id="company_name" placeholder="Company Name" required>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="username"> Full Name </label>
										<input type="text" autocomplete="off" name="name" id="name" placeholder="Full Name" required>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Mobile </label>
										<input type="text" autocomplete="off" name="mobile" id="mobile" placeholder="Mobile" maxlength="10" pattern="\d{10}" title="Please enter exactly 10 digits" required>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Email </label>
										<input type="email" autocomplete="off" name="email" id="email" placeholder="Email" required>
									</div>
								</div>

								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Password </label>
										<input type="password" autocomplete="off" name="password" id="password" placeholder="Password" required>
									</div>
								</div>
								<!--<div class="col-lg-3 col-sm-6">-->
								<!--                         <div class="from-group">-->
								<!--                             <label for="first-name"> Role </label>-->
								<!--                             <select autocomplete="off" name="status" id="status" > -->
								<!--                                 <option value="user"> User </option>-->
								<!--                                 <option value="admin"> Admin </option>-->
								<!--		</select>-->
								<!--	</div>-->
								<!--</div>-->
								
								<div class="col-lg-3 col-sm-6">
                                <div class="from-group my-2">
                                    <label for="first-name"> Staff List </label>
                                    <select autocomplete="off" name="staff_id" id="staff_id" > 
                                        @foreach($user_list as $user)
                                        <option value="{{$user->id}}"> {{$user->name}} </option>
                                        @endforeach
									</select>
								</div>
							</div>

								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Gender </label>
										<select autocomplete="off" name="gender" id="gender" Required>
											<option value=""> Select Gender </option>
											<option value="Male"> Male </option>
											<option value="Female"> Female</option>
											<option value="Other"> Other</option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Shipping Charge Type </label>
										<select autocomplete="off" name="charge_type" id="status">
											<option value="1"> Fixed </option>
											<option value="2"> Percentage </option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Shipping Charge </label>
										<input type="text" autocomplete="off" name="charge" id="charge" placeholder="Shipping Charge" value="0">
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Status </label>
										<select autocomplete="off" name="status" id="status">
											<option value="1"> Active </option>
											<option value="0"> In-Active </option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Is KYC </label>
										<select autocomplete="off" name="kyc_status" id="kyc_status">
											<option value="0"> No </option>
											<option value="1"> Yes </option>
										</select>
									</div>
								</div>

							</div>
							<hr />
							<div class="address_block">
								<div class="main-heading-1 pt-lg-0 pb-lg-2 px-lg-0">
									<h4 class="mb-0"> Address </h4>
								</div>
								<div class="append_address">

									<div class="row align-items-end">
										<div class="col-lg-12 col-sm-6">
											<div class="from-group">
												<label for="username"> Address </label>
												<input class="default" type="text" data-id="0" autocomplete="off" name="address" id="address" placeholder="Address" required>
											</div>
										</div>

										<div class="col-lg-3 col-sm-6">
											<div class="from-group">
												<label for="username"> Country </label>
												<input class="default" type="text" data-id="0" autocomplete="off" name="country" id="country" placeholder="Country" required>
											</div>
										</div>
										<div class="col-lg-3 col-sm-6">
											<div class="from-group">
												<label for="username"> State </label>
												<input class="default" type="text" data-id="0" autocomplete="off" name="state" id="state" placeholder="State" required>
											</div>
										</div>

										<div class="col-lg-3 col-sm-6">
											<div class="from-group">
												<label for="username"> City </label>
												<input class="default" type="text" data-id="0" autocomplete="off" name="city" id="state" placeholder="City" required>
											</div>
										</div>

										<div class="col-lg-3 col-sm-6">
											<div class="from-group">
												<label for="first-name"> Zip code </label>
												<input class="default" type="text" data-id="0" autocomplete="off" name="zip_code" id="zip_code" placeholder="Zip code" required>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- <div class="text-align-left">
                            <button class="btn-light-1" id="add_new_address" type="button" > + Add another Address </button>
						</div>-->
						</div>
					</div>
				</div>

				<div class="text-align-center mb-4">
					<button class="btn-main-1" id="customer_submit"> Submit </button>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection
@push('js')
<script>
	$(document).ready(function() {
		var i = 1;
		$('#add_new_address').click(function() {
			var html = '';
			html += '<div class="row align-items-end remove_address_' + i + '"><div class="col-lg-4 col-md-6"><div class="from-group"><label for="username"> Address </label><input class="default" type="text" data-id="0" autocomplete="off" name="address[]" id="address" placeholder="Address" required></div></div><div class="col-lg-4 col-md-6"><div class="from-group"><label for="username"> Country </label><input class="default" type="text" data-id="0" autocomplete="off" name="country[]" id="country" placeholder="Country" required></div></div><div class="col-lg-4 col-md-6"><div class="from-group"><label for="username"> State </label><input class="default" type="text" data-id="0" autocomplete="off" name="state[]" id="state" placeholder="State" required></div></div><div class="col-lg-4 col-md-6"><div class="from-group"><label for="username"> City </label><input class="default" type="text" data-id="0" autocomplete="off" name="city[]" id="state" placeholder="State" required> </div></div><div class="col-lg- col-md-6"><div class="from-group"><label for="first-name"> Zip code </label><input class="default" type="text" data-id="0" autocomplete="off" name="zip_code[]" id="zip_code" placeholder="Zip code" required></div></div><div class="col-lg- col-md-6 "><div class="from-group"><button type="button" class="btn-light-2" onclick="removeAddress(' + i + ')">❌ Delete </button></div></div> </div>';
			$('.append_address').append(html);
			i++;
		})
	})

	function removeAddress(id) {
		$('.remove_address_' + id).remove();
	}

	$('#userForm').submit(function(event) {
		event.preventDefault();
		$(this).find('button').prop('disabled', true);
		var formData = new FormData(this);
		$.ajax({
			async: true,
			type: $(this).attr('method'),
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			processData: false,
			contentType: false,
			dataType: 'Json',
			success: function(res) {
				$('button').prop('disabled', false);
				if (res.status == "error") {
					toastrMsg(res.status, res.msg);
				} else {
					toastrMsg(res.status, res.msg);
					window.location.href = "{{route('users')}}";
				}
			}
		});
	});
</script>
@endpush