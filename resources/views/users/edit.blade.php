@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Edit Franchise Partner')
@section('header_title','Edit Franchise Partner')
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
		<form id="userForm" method="post" class="customer_form" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
			@csrf
			<div class="container-fluid">
				<div class="main-rowx-1 mt-3">
					<div class="main-order-001">
						<div class="main-row main-data-teble-1">
							<div class="row">
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="username"> Company Name </label>
										<input type="text" autocomplete="off" name="company_name" id="company_name" placeholder="Company Name" value="{{ $user->company_name }}" required>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="username"> Full Name </label>
										<input type="text" autocomplete="off" name="name" id="name" placeholder="Full Name" value="{{ $user->name }}" required>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Mobile </label>
										<input type="text" autocomplete="off" name="mobile" id="mobile" placeholder="Mobile" maxlength="10" pattern="\d{10}" title="Please enter exactly 10 digits" oninput="allowOnlyNumbers(this)" value="{{ $user->mobile }}" required>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Email </label>
										<input type="email" autocomplete="off" name="email" id="email" placeholder="Email" value="{{ $user->email }}" required>
									</div>
								</div>
								
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Password </label>
										<input type="text" autocomplete="off" name="password" id="password" placeholder="Password" >
									</div>
								</div>   
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Gender </label>
										<select autocomplete="off" name="gender" id="gender" Required>
											<option value=""> Select Gender </option>
											<option value="Male" {{ $user->gender == 'Male' ? 'selected' : ''  }}> Male </option>
											<option value="Female" {{ $user->gender == 'Female' ? 'selected' : ''  }} > Female</option>
											<option value="Other" {{ $user->gender == 'Other' ? 'selected' : ''  }}> Other</option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Shipping Charge Type </label>
										<select autocomplete="off" name="charge_type" id="status">
											<option value="1" {{ $user->charge_type == 1 ? 'selected' : ''  }}> Fixed </option>
											<option value="2" {{ $user->charge_type == 2 ? 'selected' : ''  }}> Percentage </option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Shipping Charge </label>
										<input type="text" autocomplete="off" name="charge" id="charge" placeholder="Shipping Charge" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));"  value="{{ $user->charge }}">
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Status </label>
										<select autocomplete="off" name="status" id="status">
											<option value="1" {{ $user->status == 1 ? 'selected' : ''  }}> Active </option>
											<option value="0" {{ $user->status == 0 ? 'selected' : ''  }}> In-Active </option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="first-name"> Is KYC </label>
										<select autocomplete="off" name="kyc_status" id="kyc_status">
											<option value="0" {{ $user->kyc_status == 0 ? 'selected' : ''  }}> No </option>
											<option value="1" {{ $user->kyc_status == 1 ? 'selected' : ''  }}> Yes </option>
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
												<textarea class="default" autocomplete="off" name="address" id="address" placeholder="Address" required>{{ $user->address }}</textarea>
											</div>
										</div>
										<div class="col-lg-3 col-sm-6">
											<div class="from-group">
												<label for="first-name"> Zip code </label>
												<input class="default" type="text" autocomplete="off" name="zip_code" id="zip_code" placeholder="Zip code" onKeyup="autoFetchCountry(this)"  value="{{ $user->zip_code }}" required>
											</div>
										</div>
										<div class="col-lg-3 col-sm-6">
											<div class="from-group">
												<label for="username"> Country </label>
												<input class="default" type="text" autocomplete="off" name="country" id="country" placeholder="Country" value="{{ $user->country }}" required>
											</div>
										</div>
										<div class="col-lg-3 col-sm-6">
											<div class="from-group">
												<label for="username"> State </label>
												<input class="default" type="text"autocomplete="off" name="state" id="state" placeholder="State"  value="{{ $user->state }}" required>
											</div>
										</div>
										
										<div class="col-lg-3 col-sm-6">
											<div class="from-group">
												<label for="username"> City </label>
												<input class="default" type="text" autocomplete="off" name="city" id="city" placeholder="City"  value="{{ $user->state }}" required>
											</div>
										</div>
										 
									</div>
								</div>
							</div> 
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
	function allowOnlyNumbers(input) {
		input.value = input.value.replace(/\D/g, '');
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
			success: function(res) 
			{
				$('button').prop('disabled', false);
				if (res.status == "error") {
					toastrMsg(res.status, res.msg);
					} else {
					toastrMsg(res.status, res.msg);
					window.location.href = "{{ route('users') }}";
				}
			}
		});
	});
	
	let zipTimeout;
	
	function autoFetchCountry(obj)
	{
		obj.value = obj.value.replace(/\D/g, '');
		
		clearTimeout(zipTimeout); // Clear previous timeout
		
		const zip_code = $(obj).val().trim();  
		
		if (/^\d{6}$/.test(zip_code)) { // Validate: Exactly 6 digits
			zipTimeout = setTimeout(() => {
				$.ajax({
					type: 'GET',
					url: `https://api.postalpincode.in/pincode/${zip_code}`, // Using Template Literal
					success: function (response) {
						if (response[0]?.Status === "Success" && response[0].PostOffice?.length) {
							const { District: city, State: state, Country: country } = response[0].PostOffice[0];
							
							$(`#city`).val(city);
							$(`#state`).val(state);
							$(`#country`).val(country);
							} else {
							console.warn("Invalid or missing pincode data.");
						}
					},
					error: function (xhr, status, error) {
						console.error("API Error:", error);
					}
				});
			}, 500); // Debounce API request by 500ms
		}
	}
</script>
@endpush