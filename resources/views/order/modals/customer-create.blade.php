<div class="modal fade" id="createCustomerModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> Add Recipient/Customer </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="customerForm" method="post" action="{{ route('customer.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="username"> First Name </label>
								<input type="text" autocomplete="off" name="first_name" id="first_name" placeholder="First Name" required> 
							</div>
						</div> 
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="first-name"> Last Name </label>
								<input type="text" autocomplete="off" name="last_name" id="last_name" placeholder="Last Name" required> 
							</div>
						</div>
						
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="first-name"> Email </label>
								<input type="email" autocomplete="off" name="email" id="email" placeholder="Email">
							</div>
						</div>
						
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="first-name"> Mobile </label>
								<input type="text" autocomplete="off" name="mobile" id="mobile" placeholder="Mobile"  maxlength="10" pattern="\d{10}" title="Please enter exactly 10 digits" required> 
							</div>
						</div> 
						
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="first-name"> GST Number </label>
								<input type="text" name="gst_number"  id="gst_number"  placeholder="Enter GST Number" pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}$" title="Enter a valid 15-character GST Number (e.g., 22AAAAA1234A1Z5)"> 
							</div>
						</div> 
						
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="first-name"> Aadhar Front Image </label>
								<input type="file" name="aadhar_front" id="aadhar_front" accept="image/png, image/jpeg, image/jpg, image/webp" > 
							</div> 
						</div> 
						
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="first-name"> Aadhar Back Image </label>
								<input type="file" name="aadhar_back" id="aadhar_back" accept="image/png, image/jpeg, image/jpg, image/webp" > 
							</div>
						</div> 
						
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="first-name"> Pancard Image </label>
								<input type="file" name="pancard" id="pancard" accept="image/png, image/jpeg, image/jpg, image/webp" > 
							</div>
						</div> 
						 
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="first-name"> Status </label>
								<select autocomplete="off" name="status" id="status" > 
									<option value="1"> Active </option>
									<option value="0"> In-Active </option>
								</select>
							</div>
						</div>
                    </div> 
                    <hr /> 
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12">
							<div class="from-group my-2">
								<label for="username"> Address </label>
								<textarea class="default" data-id="0" autocomplete="off" name="address[]" id="address" placeholder="Address" required></textarea> 
							</div>
						</div>
						 
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="first-name"> Zip code </label>
								<input class="default" type="text" data-id="0" autocomplete="off" name="zip_code[]" id="zip_code" placeholder="Zip code" onkeyup="autoFetchCountry(this)" required> 
							</div>
						</div>  
						
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="username"> Country </label>
								<input class="default" type="text" data-id="0" autocomplete="off" name="country[]" id="country" placeholder="Country" required> 
							</div>
						</div>
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="username"> State </label>
								<input class="default" type="text" data-id="0" autocomplete="off" name="state[]" id="state" placeholder="State" required> 
							</div>
						</div>
						
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="username"> City </label>
								<input class="default" type="text" data-id="0" autocomplete="off" name="city[]" id="city" placeholder="City" required> 
							</div>
						</div> 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-main-1">Submit</button>
                </div>
            </form>
        </div>
    </div>
	<script>
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
							customerDetailsList() 
							setTimeout(function(){ 
								$('#orderForm #customer_id').val(res.customer_id).trigger('change');
								customerAddresList(this, res.customer_address_id);
							}, 1000);
							$('#createCustomerModal').modal('hide');
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
</div>