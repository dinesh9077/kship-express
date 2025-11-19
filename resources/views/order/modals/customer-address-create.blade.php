<div class="modal fade" id="createCustomerAddressModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> Add Recipient/Customer Address</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="customerAddressForm" method="post" action="{{ route('order.customer-address.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body"> 
					<input type="hidden" value="{{ $customerId }}" name="customer_id">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12">
							<div class="from-group my-2">
								<label for="username"> Address <span class="text-danger">*</span></label>
								<textarea class="default" type="text" data-id="0" autocomplete="off" name="address[]" id="address" placeholder="Address" required></textarea>
							</div>
						</div>
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="first-name"> Zip code <span class="text-danger">*</span></label>
								<input class="default" type="text" data-id="0" autocomplete="off" name="zip_code[]" id="zip_code" placeholder="Zip code" onkeyup="autoFetchCountry(this)" required> 
							</div>
						</div>  
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="username"> City <span class="text-danger">*</span></label>
								<input class="default" type="text" data-id="0" autocomplete="off" name="city[]" id="city" placeholder="City" required> 
							</div>
						</div>  
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="username"> State <span class="text-danger">*</span></label>
								<input class="default" type="text" data-id="0" autocomplete="off" name="state[]" id="state" placeholder="State" required> 
							</div>
						</div>
						<div class="col-xl-6 col-md-4 col-sm-12">
							<div class="from-group my-2">
								<label for="username"> Country <span class="text-danger">*</span></label>
								<input class="default" type="text" data-id="0" autocomplete="off" name="country[]" id="country" placeholder="Country" required> 
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
			const $customerAddressForm = $("#customerAddressForm");
			  
			// Enable button initially
			$customerAddressForm.find('input[type="submit"]').prop("disabled", false);
	 
			$customerAddressForm.on("submit", function (event) {
				event.preventDefault();
				let $submitButton = $customerAddressForm.find('button[type="submit"]');
				$submitButton.prop("disabled", true); // Disable button to prevent multiple clicks
	 
				// Create FormData object
				let formData = new FormData(this);
				formData.append('_token', "{{ csrf_token() }}");

				// AJAX Request
				$.ajax({
					url: $customerAddressForm.attr("action"),
					type: $customerAddressForm.attr("method"),
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
							$customerAddressForm[0].reset();   
							setTimeout(function(){  
								customerAddresList(this, res.customer_address_id);
							}, 1000);
							$('#createCustomerAddressModal').modal('hide');
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
		
		let zipTimeoutAddress;
	
		function autoFetchCountry(obj)
		{
			clearTimeout(zipTimeoutAddress); // Clear previous timeout
			
			const zip_code = $(obj).val().trim();  
			
			if (/^\d{6}$/.test(zip_code)) { // Validate: Exactly 6 digits
				zipTimeoutAddress = setTimeout(() => {
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