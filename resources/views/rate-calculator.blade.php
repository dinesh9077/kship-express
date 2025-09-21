@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Shipping Rate Calculator')
@section('header_title','Shipping Rate Calculator')
@section('content')

<div class="content-page">
	<div class="content">
		
		@if($user->kyc_status != 1 && $user->role === 'user')
			<div class="container-fluid">
				<h2 style="font-weight: 800; text-align: center; color: red;">
					Your KYC verification is incomplete. Please complete it to proceed.
				</h2>
			</div>
		@elseif($user->charge == '0.00' && $user->role === 'user')
			<div class="container-fluid">
				<h2 style="font-weight: 800; text-align: center; color: red;">
					Please contact the admin to set up your rate calculator.
				</h2>
			</div>
		@else  
			<div class="container-fluid">
				<div class="main-create-order mt-3"> 
					<div class="main-order-001">
						<div class="row">
							<div class="col-xl-12 col-sm-12">
								<form id="rateForm" method="post" action="{{ route('rate.calculator.create') }}">
									@csrf
									<div class="main-data-teble-1">
										<div class="row" style="row-gap: 15px;">
											<div class="col-lg-3 col-sm-6">
												<div class="from-group">
													<label for="pickup-pincode"> Pickup Pincode </label>
													<input type="text" name="pickup_code" id="pickup_code" maxlength="6" placeholder="Enter 6 digit pickup area pincode" required>
													<p id="pickup_code_lable" style="display:none;"><i class="mdi mdi-map-marker"></i> Surat</p>
												</div>
											</div>
											
											<div class="col-lg-3 col-sm-6">
												<div class="from-group">
													<label for="pickup-pincode"> Delivery Pincode </label>
													<input type="text" name="delivery_code" id="delivery_code" maxlength="6" placeholder="Enter 6 digit Delivery area pincode" required>
													<p id="delivery_code_lable" style="display:none;"><i class="mdi mdi-map-marker"></i> Surat</p>
												</div>
											</div>
											<div class="col-lg-3 col-sm-6">
												<div class="from-group"> 
													<label for="pickup-pincode">Payment Type</label>
													<select name="payment_type" id="payment_type" class="form-control" required>
														<option value="COD" selected>Cash on Delivery</option>
														<option value="PREPAID">Prepaid</option>
													</select>
												</div>
											</div>

											<div class="col-lg-3 col-sm-6 ">
												<div class="from-group">
													<label for="pickup-pincode"> COD Amount </label>
													<input type="text" name="cod_amount" id="cod_amount" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" placeholder="Amount to be collected"> 
												</div>
											</div>
										</div>
										
										<div class="row mt-2" style="row-gap: 15px;">	
											<div class="col-lg-3 col-sm-6">
												<div class="from-group"> 
													<label for="pickup-pincode">ROV Type</label>
													<select name="rov_type" id="rov_type" class="form-control" >
														<option value="">Select ROV Type</option>
														<option value="ROV_OWNER">ROV OWNER</option>
														<option value="ROV_CARRIER">ROV CARRIER</option>
													</select>
												</div>
											</div>
											<div class="col-lg-3 col-sm-6 ">
												<div class="from-group">
													<label for="pickup-pincode"> Invoice Amount </label>
													<input type="text" name="inv_amount" id="inv_amount" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" placeholder="Invoice Amount"> 
												</div>
											</div>
											<div class="col-lg-3 col-sm-6">
												<div class="from-group">
													<label for="pickup-pincode"> Total Weight In KG </label>
													<input type="text" name="weight" id="weight" placeholder="Total Weight In KG" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required> 
												</div>
											</div>		
											<div class="col-lg-3 col-sm-6" style="display:none;">
												<div class="from-group">
													<label for="packaging-type"> Dimensions </label>
													<select class="form-control" id="dimension_type" name="dimension_type">
														<option value="cm">Centimeter</option> 
													</select>
												</div>
											</div>
										</div> 
										<div class="addMoreDimension">	
											<div class="row mt-2 removeDimenionRow" style="row-gap: 15px;">	
												<div class="col-lg-3 col-sm-6">
													<div class="from-group">
														<label for="pickup-pincode">Qty (No of box) </label>
														<input type="text" name="qty[]" id="qty" oninput="allowOnlyNumbers(this)" placeholder="Qty (No of box)" value="0"> 
													</div>
												</div>
												<div class="col-lg-3 col-sm-6">
													<div class="from-group">
														<label for="pickup-pincode">Length </label>
														<input type="text" name="length[]" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" id="length" placeholder="L" value="0.00"> 
													</div>
												</div>
												
												<div class="col-lg-3 col-sm-6">
													<div class="from-group">
														<label for="pickup-pincode"> Height </label>
														<input type="text" name="height[]" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" id="height" placeholder="0.00" value="0.00"> 
													</div>
												</div>
												<div class="col-lg-2 col-sm-6">
													<div class="from-group">
														<label for="pickup-pincode"> Width </label>
														<input type="text" name="width[]" id="width" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" placeholder="0.00" value="0.00"> 
													</div>
												</div>
												<div class="col-lg-1 col-sm-6">
													<div class="from-group my-2">
														<label for="packaging-type" class="text-white">. </label>
														<button type="button" class="btn btn-primary btn-main-1 d-002" onclick="addMoreDimension(this, event)"> + </button>
													</div>
												</div>
											</div>  
										</div>  
										@if(config('permission.rate_calculator.add'))
										<div class="de-flex-clas mt-4">
											<button type="submit" class="btn-main-1"> Calculate </button>
											<button type="button" class="btn-main-1" onClick="window.location.reload();"> Reset </button>
										</div>
										@endif
									</div>
								</form>
							</div> 
						</div>
					</div>  
				</div>
				<div class="main-create-order mt-3 shipping_cost" style="display:none;"></div> 
			</div>
		</div>
	@endif
</div>
@endsection
@push('js')
<script>
	let $rateForm = $('#rateForm');
	
	$(document).ready(function () {
		let debounceTimer;
		
		function fetchPincodeDetails(pincode, type) 
		{
			$(`#${type}_code_lable`).hide();;
			if (pincode.length === 6) {  
				$.ajax({
					url: `{{ url('rate/pincode/serviciability') }}/${pincode}`,
					method: "GET",
					dataType: "json",
					success: function (response) {
						if (response.status === "success") 
						{
							if(response.msg)
							{
								$(`#${type}_code_lable`).html(`<i class="mdi mdi-map-marker"></i> ${response.msg}`).show();
							}
							} else {
							$(`#${type}_code_lable`).text(response.msg).show();;
						}
					}
				});
			}  
		}
		
		function debounceFetch(type) {
			clearTimeout(debounceTimer);
			debounceTimer = setTimeout(function () {
				let pincode = $(`#${type}_code`).val();
				fetchPincodeDetails(pincode, type);
			}, 500); 
		}
		
		/* $("#pickup_code").on("input", function () {
			debounceFetch("pickup");
		});
		
		$("#delivery_code").on("input", function () {
			debounceFetch("delivery");
		}); */
	});
	
	
	$('#rateForm select[name="payment_type"]').on('change', function () 
	{
		const orderType = $(this).val().toLowerCase(); // Get selected radio button value
		 
		const $rateForm = $('#rateForm'); // Define $rateForm
		const $codAmount = $rateForm.find('#cod_amount');
		
		$codAmount.prop('readonly', false); // Enable input by default
		
		if (orderType === "prepaid") {
			$codAmount.val(0).prop('readonly', true); // Disable and set value to 0
		}
	});
	
	function addMoreDimension(obj, event)
	{
		let html = `<div class="row mt-2 removeDimenionRow" style="row-gap: 15px;">	
		<div class="col-lg-3 col-sm-6">
		<div class="from-group">
		<label for="pickup-pincode">Qty (No of box) </label>
		<input type="text" name="qty[]" id="qty" oninput="allowOnlyNumbers(this)" placeholder="Qty (No of box)" value="0"> 
		</div>
		</div>
		<div class="col-lg-3 col-sm-6">
		<div class="from-group">
		<label for="pickup-pincode">Length </label>
		<input type="text" name="length[]" id="length" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" placeholder="L" value="0.00"> 
		</div>
		</div>
		
		<div class="col-lg-3 col-sm-6">
		<div class="from-group">
		<label for="pickup-pincode"> Height </label>
		<input type="text" name="height[]" id="height" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" placeholder="0.00" value="0.00"> 
		</div>
		</div>
		<div class="col-lg-2 col-sm-6">
		<div class="from-group">
		<label for="pickup-pincode"> Width </label>
		<input type="text" name="width[]" id="width" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" placeholder="0.00" value="0.00"> 
		</div>
		</div>
		<div class="col-lg-1 col-sm-6">
		<div class="from-group my-2">
		<label for="packaging-type" class="text-white">. </label>
		<button type="button" class="btn btn-danger btn-main-1 d-002"  onclick="removeDimenionRow(this)"> <i class="mdi mdi-trash-can"></i> </button> 
		</div>
		</div>
		</div>`;
		
		$rateForm.find('.addMoreDimension').append(html); 
	}
	
    function removeDimenionRow(obj) 
	{ 
		const rowId = $(obj).attr('data-row-id');
        $(obj).closest('.removeDimenionRow').remove();  
	}
	
	$rateForm.submit(function(event) 
	{
		event.preventDefault();
		$rateForm.find(':submit').prop('disabled', true);
		run_waitMe($('body'), 1, 'win8')
		var formData = new FormData(this);
		formData.append('_token', '{{csrf_token()}}');
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
				$('body').waitMe('hide');
				$rateForm.find(':submit').prop('disabled', false); 
				$('.shipping_cost').html(res.view).show();
			}
		});
	});
	
	function allowOnlyNumbers(input) {
		input.value = input.value.replace(/\D/g, '');
	}
</script>
@endpush