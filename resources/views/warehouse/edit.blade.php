@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Edit Warehouse')
@section('header_title','Edit Warehouse')
@section('content') 
<style>
	button.btn-light-2 {
    padding: 7px 12px;
    font-size: 14px;
    border: none;
    background: linear-gradient(180deg, #ffc8c8 0%, #ff6e6e45 100%);
    border-radius: 5px;
	}
</style>
<div class="content-page">
    <div class="content">
        <form id="warehouseForm" method="post" class="customer_form" action="{{ route('warehouse.update', ['id' => $courierWarehouse->id]) }}" enctype="multipart/form-data"> 
            <div class="container-fluid">
                <div class="main-rowx-1 mt-3">
                    <div class="main-row main-data-teble-1"> 
						<div class="main-rowx-1">
                            <div class="main-order-001">
                                <div class="address_block">
                                    <div class="main-heading-1 pt-lg-0 pb-lg-2 px-lg-0">
                                       <h4 class="mb-0"> 
											<small class="text-muted d-block">
												Note: If Delhivery API is activated, the address will be sent to the warehouse API to add it to the Delhivery portal.
											</small>
										</h4> 
        							</div> 
									<div class="row align-items-end">
										<div class="col-xl-3 col-md-4 col-sm-6">
											<div class="from-group my-2">
												<label for="username"> Company Name </label>
												<input type="text" autocomplete="off" name="company_name" id="company_name" maxlength="18" placeholder="Company Name" value="{{ $courierWarehouse->company_name }}" required> 
											</div>
										</div> 
										<div class="col-xl-3 col-md-4 col-sm-6">
											<div class="from-group my-2">
												<label for="username"> Contact Name </label>
												<input type="text" autocomplete="off" name="contact_name" id="contact_name" placeholder="Contact Name"value="{{ $courierWarehouse->contact_name }}" required> 
											</div>
										</div> 
										<div class="col-xl-3 col-md-4 col-sm-6">
											<div class="from-group my-2">
												<label for="first-name"> Contact Number </label>
												<input type="text" autocomplete="off" name="contact_number" id="contact_number" placeholder="Contact Number"  maxlength="10" pattern="\d{10}" title="Please enter exactly 10 digits" value="{{ $courierWarehouse->contact_number }}" required> 
											</div>
										</div> 
									 
										<div class="col-xl-3 col-md-4 col-sm-6">
											<div class="from-group my-2">
												<label for="username"> Warehouse Name </label>
												<input class="default" type="text" data-id="0" autocomplete="off" maxlength="18" name="warehouse_name" id="warehouse_name" placeholder="Warehouse Name" value="{{ $courierWarehouse->warehouse_name }}" required readonly> 
											</div>
										</div>
										<div class="col-xl-12 col-md-4 col-sm-6">
											<div class="from-group my-2">
												<label for="username"> Address </label>
												<textarea class="default" type="text" data-id="0" autocomplete="off" name="address" id="address" placeholder="Address" required>{{ $courierWarehouse->address }}</textarea> 
											</div>
										</div> 
										<div class="col-xl-3 col-md-4 col-sm-6">
											<div class="from-group my-2">
												<label for="first-name"> Zip code </label>
												<input class="default" type="text" data-id="0" autocomplete="off" name="zip_code" id="zip_code" placeholder="Zip code" value="{{ $courierWarehouse->zip_code }}" onkeyup="autoFetchCountry(this)" required> 
											</div>
										</div>
										<div class="col-xl-3 col-md-4 col-sm-6">
											<div class="from-group my-2">
												<label for="username"> Country </label>
												<input class="default" type="text" data-id="0" autocomplete="off" name="country" id="country" placeholder="Country" value="{{ $courierWarehouse->country }}" required> 
											</div>
										</div>
										<div class="col-xl-3 col-md-4 col-sm-6">
											<div class="from-group my-2">
												<label for="username"> State </label>
												<input class="default" type="text" data-id="0" autocomplete="off" name="state" id="state" placeholder="State" value="{{ $courierWarehouse->state }}" required> 
											</div>
										</div>
										
										<div class="col-xl-3 col-md-4 col-sm-6">
											<div class="from-group my-2">
												<label for="username"> City </label>
												<input class="default" type="text" data-id="0" autocomplete="off" name="city" id="city" placeholder="City" value="{{ $courierWarehouse->city }}" required> 
											</div>
										</div> 
										  
										<div class="col-xl-3 col-md-4 col-sm-6">
											<div class="from-group my-2">
												<label for="first-name"> Status </label>
												<select autocomplete="off" name="warehouse_status" id="warehouse_status" > 
													<option value="1" {{ $courierWarehouse->warehouse_status == 1 ? 'selected' : '' }}> Active </option>
													<option value="0" {{ $courierWarehouse->warehouse_status == 0 ? 'selected' : '' }}> In-Active </option>
												</select>
											</div>
										</div> 
        							</div>
        						</div> 
    						</div>
						</div> 
					</div>
				</div> 
                <div class="text-align-center mb-4">
                    <button class="btn-main-1" type="submit" > Submit </button>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection
@push('js')
<script> 
	$(document).ready(function ()
	{
		const $warehouseForm = $("#warehouseForm");
		  
		// Enable button initially
		$warehouseForm.find('input[type="submit"]').prop("disabled", false);
 
		$warehouseForm.on("submit", function (event) {
			event.preventDefault();
			let $submitButton = $warehouseForm.find('button[type="submit"]');
			$submitButton.prop("disabled", true); // Disable button to prevent multiple clicks
 
			// Create FormData object
			let formData = new FormData(this);
			formData.append('_token', "{{ csrf_token() }}");

			// AJAX Request
			$.ajax({
				url: $warehouseForm.attr("action"),
				type: $warehouseForm.attr("method"),
				data: formData,
				cache: false,
				processData: false,
				contentType: false, 
				dataType: 'Json',
				success: function (res)
				{
					$submitButton.prop("disabled", false); // Re-enable button
					if (res.status === "success") 
					{
						toastrMsg(res.status,res.msg);
						$warehouseForm[0].reset(); 
						setTimeout(() => {
							window.location.href = "{{ route('warehouse.index') }}";
						}, 1000); // Redirect after 1 second
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
@endpush