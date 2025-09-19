@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Create Client')
@section('header_title','Create Client')
@section('content') 
<style>
	button.btn-light-2 {
	padding: 7px 12px;
	font-size: 14px;
	border: none;
	background: #f44747;
	border-radius: 5px;
	color: #fff;
}

.main-heading-1 h4 {
	color: #000;
	margin-top: 0 !important;
}
</style>
<div class="content-page">
    <div class="content">
        <form id="customerForm" method="post" class="customer_form" action="{{ route('customer.store') }}" enctype="multipart/form-data"> 
            <div class="container-fluid"> 
                <div class="main-rowx-1 mt-3">
                    <div class="main-row main-data-teble-1">
                        <div class="main-rowx-1">
                            <div class="main-order-001">
								<div class="main-heading-1 pt-lg-0 pb-lg-2 px-lg-0">
									<h4 class="mb-0"> Client Details </h4>
								</div> 
                                <div class="row">
                                    <div class="col-xl-3 col-md-4 col-sm-6">
                                        <div class="from-group my-2">
                                            <label for="username"> First Name </label>
                                            <input type="text" autocomplete="off" name="first_name" id="first_name" placeholder="First Name" required> 
        								</div>
        							</div> 
                                    <div class="col-xl-3 col-md-4 col-sm-6">
                                        <div class="from-group my-2">
                                            <label for="first-name"> Last Name </label>
                                            <input type="text" autocomplete="off" name="last_name" id="last_name" placeholder="Last Name" required> 
        								</div>
        							</div>
        							
                                    <div class="col-xl-3 col-md-4 col-sm-6">
                                        <div class="from-group my-2">
                                            <label for="first-name"> Email </label>
                                            <input type="email" autocomplete="off" name="email" id="email" placeholder="Email">
        								</div>
        							</div>
        							
                                    <div class="col-xl-3 col-md-4 col-sm-6">
                                        <div class="from-group my-2">
                                            <label for="first-name"> Mobile </label>
                                            <input type="text" autocomplete="off" name="mobile" id="mobile" placeholder="Mobile"  maxlength="10" pattern="\d{10}" title="Please enter exactly 10 digits" required> 
        								</div>
        							</div>  
        						</div>
    						</div>
						</div>      
						<div class="main-rowx-1">
                            <div class="main-order-001">
                                <div class="address_block">
                                    <div class="main-heading-1 pt-lg-0 pb-lg-2 px-lg-0">
										<h4 class="mb-0"> Client Address </h4>
									</div> 
        							<div class="append_address"> 
        								<div class="row align-items-end">
        									<div class="col-xl-12 col-md-4 col-sm-6">
        										<div class="from-group my-2">
        											<label for="username"> Address </label>
        											<textarea class="default" data-id="0" autocomplete="off" name="address[]" id="address" placeholder="Address" required></textarea>
        										</div>
        									</div>
        									<div class="col-xl-3 col-md-4 col-sm-6">
        										<div class="from-group my-2">
        											<label for="first-name"> Zip code </label>
        											<input class="default" type="text" data-id="0" autocomplete="off" name="zip_code[]" id="zip_code" placeholder="Zip code" onkeyup="autoFetchCountry(this)" required> 
        										</div>
        									</div>  
        									<div class="col-xl-3 col-md-4 col-sm-6">
        										<div class="from-group my-2">
        											<label for="username"> Country </label>
        											<input class="default" type="text" data-id="0" autocomplete="off" name="country[]" id="country_0" placeholder="Country" required> 
        										</div>
        									</div>
        									<div class="col-xl-3 col-md-4 col-sm-6">
        										<div class="from-group my-2">
        											<label for="username"> State </label>
        											<input class="default" type="text" data-id="0" autocomplete="off" name="state[]" id="state_0" placeholder="State" required> 
        										</div>
        									</div>
        									
        									<div class="col-xl-3 col-md-4 col-sm-6">
        										<div class="from-group my-2">
        											<label for="username"> City </label>
        											<input class="default" type="text" data-id="0" autocomplete="off" name="city[]" id="city_0" placeholder="City" required> 
        										</div>
        									</div> 
        								</div>
        							</div>
        						</div>
								<div class="text-align-left">
									<button class="btn-light-1" id="add_new_address" type="button" > + Add another Address </button>
								</div>
    						</div>              
						</div>              
						
						<div class="main-rowx-1">
                            <div class="main-order-001">
								<div class="main-heading-1 pt-lg-0 pb-lg-2 px-lg-0">
									<h4 class="mb-0"> Client Document </h4>
								</div> 
                                <div class="row"> 
                                    <div class="col-xl-3 col-md-4 col-sm-6">
                                        <div class="from-group my-2">
                                            <label for="first-name"> GST Number </label>
                                            <input type="text" name="gst_number"  id="gst_number"  placeholder="Enter GST Number" pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}$" title="Enter a valid 15-character GST Number (e.g., 22AAAAA1234A1Z5)"> 
        								</div>
        							</div> 
									
									<div class="col-xl-3 col-md-4 col-sm-6">
                                        <div class="from-group my-2">
                                            <label for="first-name"> Aadhar Front Image </label>
                                            <input type="file" name="aadhar_front" id="aadhar_front" accept="image/png, image/jpeg, image/jpg, image/webp" > 
        								</div> 
        							</div> 
									
									<div class="col-xl-3 col-md-4 col-sm-6">
                                        <div class="from-group my-2">
                                            <label for="first-name"> Aadhar Back Image </label>
                                            <input type="file" name="aadhar_back" id="aadhar_back" accept="image/png, image/jpeg, image/jpg, image/webp" > 
        								</div>
        							</div> 
									
									<div class="col-xl-3 col-md-4 col-sm-6">
                                        <div class="from-group my-2">
                                            <label for="first-name"> Pancard Image </label>
                                            <input type="file" name="pancard" id="pancard" accept="image/png, image/jpeg, image/jpg, image/webp" > 
        								</div>
        							</div> 
									 
                                    <div class="col-xl-3 col-md-4 col-sm-6">
                                        <div class="from-group my-2">
                                            <label for="first-name"> Status </label>
                                            <select autocomplete="off" name="status" id="status" > 
                                                <option value="1"> Active </option>
                                                <option value="0"> In-Active </option>
        									</select>
        								</div>
        							</div>
        						</div>
    						</div>
						</div> 
					</div>
				</div>
				
                <div class="text-align-center mb-4">
                    <button class="btn-main-1" type="submit" id="customer_submit"> Submit </button>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection
@push('js')
<script>
	$(document).ready(function () {
		var i = 1;

		$('#add_new_address').click(function () {
			var html = `
				<div class="row align-items-end remove_address_${i}"> 
					<div class="col-xl-12 col-md-4 col-sm-6">
						<div class="from-group my-2">
							<label for="address_${i}"> Address </label>
							<textarea class="default" data-id="${i}" autocomplete="off" name="address[]" id="address_${i}" placeholder="Address" required></textarea>
						</div>
					</div>
					<div class="col-xl-3 col-md-4 col-sm-6">
						<div class="from-group my-2">
							<label for="zip_code_${i}"> Zip Code </label>
							<input class="default" type="text" data-id="${i}" autocomplete="off" name="zip_code[]" id="zip_code_${i}" placeholder="Zip Code" onkeyup="autoFetchCountry(this)" required>
						</div>
					</div>
					<div class="col-xl-3 col-md-4 col-sm-6">
						<div class="from-group my-2">
							<label for="country_${i}"> Country </label>
							<input class="default" type="text" data-id="${i}" autocomplete="off" name="country[]" id="country_${i}" placeholder="Country" required>
						</div>
					</div>
					<div class="col-xl-3 col-md-4 col-sm-6">
						<div class="from-group my-2">
							<label for="state_${i}"> State </label>
							<input class="default" type="text" data-id="${i}" autocomplete="off" name="state[]" id="state_${i}" placeholder="State" required>
						</div>
					</div>
					<div class="col-xl-3 col-md-4 col-sm-6">
						<div class="from-group my-2">
							<label for="city_${i}"> City </label>
							<input class="default" type="text" data-id="${i}" autocomplete="off" name="city[]" id="city_${i}" placeholder="City" required>
						</div>
					</div> 
					<div class="col-lg-3 col-md-6">
						<div class="from-group my-2">
							<button type="button" class="btn-light-2 remove_address_btn" data-id="${i}">
								<i class="mdi mdi-trash-can"></i> Delete
							</button>
						</div>
					</div>
				</div>`;

			$('.append_address').append(html);
			i++;
		});

		// Remove address dynamically
		$(document).on('click', '.remove_address_btn', function () {
			var removeId = $(this).data('id');
			$('.remove_address_' + removeId).remove();
		});
	});
 
	$(document).ready(function ()
	{
		const $customerForm = $("#customerForm");
		  
		// Enable button initially
		$customerForm.find('input[type="submit"]').prop("disabled", false);
 
		$customerForm.on("submit", function (event) {
			event.preventDefault();
			let $submitButton = $customerForm.find('button[type="submit"]');
			$submitButton.prop("disabled", true); // Disable button to prevent multiple clicks
 
			// Create FormData object
			let formData = new FormData(this);
			formData.append('_token', "{{ csrf_token() }}");

			// AJAX Request
			$.ajax({
				url: $customerForm.attr("action"),
				type: $customerForm.attr("method"),
				data: formData,
				cache: false,
				processData: false,
				contentType: false, 
				dataType: 'Json',
				success: function (res)
				{
					$submitButton.prop("disabled", false);  
					if (res.status === "success") 
					{
						toastrMsg(res.status,res.msg);
						$customerForm[0].reset(); 
						setTimeout(() => {
							window.location.href = "{{ route('customer') }}";
						}, 1000); 
					}
					else if(res.status == "info")
					{  
						toastrMsg('info', res.msg);
					}else {
						toastrMsg(res.status,res.msg);
					} 
				},
				error: function (xhr) { 
					toastrMsg("error","Something went wrong. Please try again.");
					$submitButton.prop("disabled", false); // Re-enable button
				}
			});
		});
	});
	
	let zipTimeout;
	
	function autoFetchCountry(obj)
	{
		clearTimeout(zipTimeout); // Clear previous timeout
		
		const zip_code = $(obj).val().trim(); // Trim whitespace
		const index = $(obj).data('id'); // Trim whitespace
		
		if (/^\d{6}$/.test(zip_code)) { // Validate: Exactly 6 digits
			zipTimeout = setTimeout(() => {
				$.ajax({
					type: 'GET',
					url: `https://api.postalpincode.in/pincode/${zip_code}`, // Using Template Literal
					success: function (response) {
						if (response[0]?.Status === "Success" && response[0].PostOffice?.length) {
							const { District: city, State: state, Country: country } = response[0].PostOffice[0];
							
							$(`#city_${index}`).val(city);
							$(`#state_${index}`).val(state);
							$(`#country_${index}`).val(country);
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