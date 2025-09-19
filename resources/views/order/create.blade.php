@extends('layouts.backend.app')
@section('title', config('setting.company_name') . ' - Create Shipments')
@section('header_title', 'Create Shipments')
@section('content')
<style>
    button.d-002 {
        padding: 6px 15px;
        border-radius: 4px 4px 4px 4px !important;
        font-size: 20px;
        font-weight: 700;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px !important;
        font-size: 14px !important;
        padding: 7px 15px;
    }

    @media(max-width: 1600px) {
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1 !important;
            padding: 11px 15px !important;
            font-size: 12px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 35px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px !important;
            font-size: 12px !important;
            padding: 1px 10px !important;
        }
    }
	#select2-customer_address_id-container { 
		max-width: 76.5ch; /* Limit text to 100 characters */
	}

</style>
<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
            <form id="orderForm" method="post" action="{{ route('order.store') }}" enctype="multipart/form-data">
                <div class="main-create-order mt-3"> 
                    <div class="main-rowx-1">
                        <div class="main-order-001">
                            <div class="row">
                                <div class="col-lg-3 col-sm-6">
                                    <div class="from-group my-2">
                                        <label for="order-id"> Order ID </label>
                                        <input type="text" placeholder="Order Id" readonly name="order_prefix" value="{{ \App\Models\Order::generateOrderNumber($user->id) }}">
                                    </div>
                                </div> 
                                <div class="col-lg-3 col-sm-6 ">
                                    <div class="from-group my-2">
                                        <label for="packaging-type"> Shipping Mode </label>
                                        <select name="shipping_mode" id="shipping_mode" required>
                                            <option value=""> Select Shipping Mode </option>
                                            <option value="Surface"> By Surface </option>
                                            <option value="Air"> By Air </option>
                                        </select>
                                    </div>
                                </div>
								<input type="hidden" name="weight_order" value="{{ request('weight_order') }}">
                                <div class="col-lg-3 col-sm-6 ">
                                    <div class="from-group my-2">
                                        <label for="packaging-type"> Date </label>
                                        <input type="text" name="order_date" class="datepicker" autocomplete="off" placeholder="Date" value="{{ date('Y-m-d') }}" id="order_date" required>
                                    </div>
                                </div>
								
								<div class="col-lg-3 col-sm-6 ">
                                    <div class="from-group my-2">
                                        <label for="order-type"> Freight (paid/to pay) </label>
                                        <select name="freight_mode" id="freight_mode" required>
                                            <option value="FOP">Paid</option>
                                            <option value="FOD">To Pay </option>
                                        </select>
                                    </div>
                                </div>
                                @if(request('weight_order') == 2)
                                    <div class="col-lg-3 col-sm-6 mt-2">
                                        <div class="from-group my-2">
                                            <label for="insurance-type"> Insurance Type </label>
                                            <select name="insurance_type" id="insurance_type" required>
                                                <option value="1">Owner Risk</option>
                                                <option value="2">Carrier Risk </option>
                                            </select>
                                        </div>
                                    </div>
                                @endif
								<div class="col-lg-3 col-sm-6 mt-2">
                                    <div class="from-group my-2">
                                        <label for="order-type"> Order Type </label>
                                        <select name="order_type" id="order_type" required>
                                            <option value="cod">Cash on Delivery</option>
                                            <option value="prepaid">Prepaid</option>
                                        </select>
                                    </div>
                                </div>
								<div class="col-lg-3 col-sm-6 mt-2">
                                    <div class="from-group my-2">
                                        <label for="order-id"> Amount To Collect</label>
                                        <input type="text" placeholder="Amount To Collect" id="cod_amount" name="cod_amount" value="" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
                                    </div>
                                </div> 
								 
								<div class="col-lg-4 col-sm-6 mt-2 ml-3">
									<div class="form-group my-2">
										<label class="form-label d-block">.</label>
										<div class="d-flex align-items-center">
											<input type="checkbox" id="is_fragile_item" name="is_fragile_item" value="1" class="form-check-input me-2 mt-0">
											<label for="is_fragile_item" class="form-check-label mb-0">My Package Contains Fragile Items</label>
										</div>
									</div>
								</div> 
                            </div>
                        </div>
                    </div>

					<div class="main-rowx-1">
                        <div class="main-order-001">
							<div class="row">
								<div class="col-lg-12"> 
									<div class="main-vender">
										<h5> Pickup Location  </h5>
									</div>
									<div class="row">
										<div class="from-group col-6"> 
											<div class="main-rox-input">
												<select name="warehouse_id" class="control-form select2" id="warehouse_id" style="border-radius: 5px 0px 0px 5px" onchange="warehousePickup(this)" required>
													<option value="">Select Pickup Location</option> 
												</select>
												<button type="button" class="btn btn-primary btn-main-1 d-001" onclick="createWarehousePickup(this, event)"> + </button>
											</div>
											<br>
											<div id="warehouse_lable">  
											</div> 
										</div> 
									</div> 
								</div>
							</div>
                        </div>
                    </div>
					
                    <div class="main-rowx-1">
                        <div class="main-order-001">
							<div class="row">
								<div class="col-lg-12">
									<div class="main-vender">
										<h5> Recipeint/Customer Information </h5>
									</div>
									<div class="row">
										<div class="from-group col-6"> 
											<label for="order-id"> Recipeint/Customer </label>
											<div class="main-rox-input">
												<select name="customer_id" class="control-form select2" id="customer_id" style="border-radius: 5px 0px 0px 5px" onchange="customerAddresList(this)" required>
													<option value="">Select Recipeint/Customer</option>
												</select>
												<button type="button" class="btn btn-primary btn-main-1 d-001" onclick="createCustomer(this, event)"> + </button>
											</div>
										</div> 
										<div class="from-group col-6">
											<label for="order-id"> Customer Address </label>
											<div class="main-rox-input">
												<select name="customer_address_id" class="control-form select2" id="customer_address_id" style="border-radius: 5px 0px 0px 5px" required>
													<option>Select Customer Address</option>
												</select>
												<button type="button" class="btn-main-1 d-001" disabled data-toggle="tooltip" data-placement="right" title="Kindly select a customer before adding an address." onclick="createCustomerAddress(this, event)"> + </button>
											</div>
											<div id="warehouse_lable"></div> 
										</div>
									</div>
								</div>
							</div>
						</div>
                    </div>

                    <div class="main-rowx-1">
                        <div class="main-order-001">
                            <div class="main-vender">
                                <h5> Product Information </h5>
                            </div>
                            <div class="add_product_more">
                                <div class="row align-items-end removeProductRows"> 
                                    <div class="col-lg-4 col-sm-6 col-md-6">
                                        <div class="from-group my-2">
                                            <label for="packaging-type"> Product Description </label>
                                            <input type="text" placeholder="Product Description" name="product_discription[]" id="product_discription" required>
                                        </div>
                                    </div> 
                                    <div class="col-lg-3 col-md-6">
                                        <div class="from-group my-2">
                                            <label for="packaging-type"> Total Amount </label>
                                            <input type="text" class="totalAmount" placeholder="Total Amount" value="" name="amount[]" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
                                        </div>
                                    </div> 

                                    <div class="col-lg-3 col-md-6">
                                        <div class="from-group my-2">
                                            <label for="packaging-type"> No of Box </label>
                                            <input type="number" name="quantity[]" class="noofbox" id="quantity" value="" placeholder="Quantity" required oninput="allowOnlyNumbers(this)">
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-6">
                                        <div class="from-group my-2">
                                            <label for="packaging-type"> </label>
                                            <button type="button" class="btn btn-primary btn-main-1 d-002" id="add_more_product"> + </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<div class="row align-items-end mt-2"> 
								<div class="col-lg-4 col-sm-6 col-md-6">
									<div class="from-group my-2">
										<label for="packaging-type"> Invoice No </label>
										<input type="text" placeholder="Invoice No" name="invoice_no" id="invoice_no" required>
									</div>
								</div> 
								<div class="col-lg-3 col-md-6">
									<div class="from-group my-2">
										<label for="packaging-type"> Invoice Amount </label>
										<input type="text" placeholder="Invoice Amount" value="" id="invoice_amount" name="invoice_amount" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
									</div>
								</div>
								<div class="col-lg-3 col-md-6">
									<div class="from-group my-2">
										<label for="packaging-type"> E-Waybill No. </label>
										<input type="text" id="ewaybillno" placeholder="E-Waybill No." value="" name="ewaybillno">
									</div>
								</div> 
							</div>
                        </div>
                    </div>
  
                    <div class="main-rowx-1">
                        <div class="main-order-001">
                            <div class="main-vender">
                                <h5> Package Details </h5>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 col-sm-6 col-md-6">
                                    <div class="from-group my-2">
                                        <label for="packaging-type"> Dimensions </label>
                                        <select class="form-control select2" id="dimension_type" name="dimension_type">
                                            <option value="cm">Centimeter</option>
                                            <option value="ft">Feet</option>
                                            <option value="inch">Inch</option> 
                                        </select>
                                    </div>
                                </div> 
                            </div>
                            <p class="my-2"></p>
                            <div id="dimenstionDetails">
								<div class="row">
									<div class="col-lg-3 col-sm-6 col-md-6">
										<div class="from-group my-2">
											<label for="packaging-type"> Weight (KG) </label>
											<input type="text" name="weight[]" id="weight" placeholder="Weight (KG)" value="" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
										</div>
										<label id="volumatric_weight" style="font-weight: 900;"></label>
									</div>

									<div class="col-lg-3 col-sm-6 col-md-6">
										<div class="from-group my-2">
											<label for="packaging-type"> Length </label>
											<input type="text" name="length[]" id="length" placeholder="Length" value="" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
										</div>
									</div>

									<div class="col-lg-3 col-sm-6 col-md-6">
										<div class="from-group my-2">
											<label for="packaging-type"> Width </label>
											<input type="text" name="width[]" id="width" placeholder="Width" value="" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
										</div>
									</div>

									<div class="col-lg-3 col-sm-6 col-md-6">
										<div class="from-group my-2">
											<label for="packaging-type"> Height </label>
											<input type="text" name="height[]" id="height" placeholder="Height" value="" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
										</div>
									</div>
								</div>
                            </div>
                        </div> 
                    </div> 
					
					<div class="main-rowx-1">
                        <div class="main-order-001">
                            <div class="main-vender">
                                <h5> Upload Documents </h5>
                            </div>
                            <div class="row"> 
                                <div class="col-lg-6 col-sm-6 col-md-6">
                                    <div class="from-group my-2">
                                        <label for="packaging-type"> Invoice Documents </label>
                                        <input type="file" class="form-control" id="invoice_document" name="invoice_document[]" multiple {{ request('weight_order') == 2 ? 'required' : '' }} > 
                                    </div>
                                </div> 
								<div class="col-lg-6 col-sm-6 col-md-6">
                                    <div class="from-group my-2">
                                        <label for="packaging-type">Other Documents </label>
                                        <input type="file" class="form-control" id="order_image" name="order_image[]" multiple > 
                                    </div>
                                </div> 
                            </div> 
                        </div>
                        <button type="submit" class="btn btn-primary btn-main-1 float-right mt-3">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> 
  
@endsection
@push('js')
<script> 
	var $orderForm = $('#orderForm');
	var weightOrder = @json(request('weight_order')) || '';
	
	$('#orderForm #order_type').on('input', function () {
		const orderType = $(this).val().toLowerCase(); // Ensure case consistency
	 
		$orderForm.find('#cod_amount').prop('readonly', false);

		if (orderType === "prepaid") {
			$orderForm.find('#cod_amount').val(0).prop('readonly', true);
		}
	});
 
	// Warehouse pickup location
	warehousePickupLocation()
	function warehousePickupLocation()
	{
		$.get("{{ route('order.warehouse.list') }}", function(res)
		{ 
			$orderForm.find('#warehouse_id').html(res.output); 
		});
	}
	
	function warehousePickup(obj) {
		const $obj = $(obj);
		let warehouse = $obj.find(':selected').attr('data-warehouse');

		if (!warehouse) {
			$('#warehouse_lable').html(''); // Clear if no warehouse is selected
			return;
		}

		let data;
		try {
			data = JSON.parse(warehouse);
		} catch (error) {
			console.error('Invalid JSON in data-warehouse:', warehouse);
			return;
		}

		let addressLabel = `
			<label>Pickup Details:</label> 
			<p><b>Address:</b> ${data.address}, ${data.city}, ${data.state}, ${data.country}, ${data.zip_code}</p>
		`;

		$('#warehouse_lable').html(addressLabel);
	}
	
	function createWarehousePickup(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get("{{ route('order.warehouse.create') }}", function(res)
			{ 
				$('body').find('#modal-view-render').html(res.view);
				$('#createWarehousePickupModal').modal('show');  
			});
		} 
	}
    
	// Customer/Destination Details
    customerDetailsList() 
    function customerDetailsList() 
	{ 
		$orderForm.find('#customer_id').prop('disabled', true);
		
		let $customerAddressElement = $orderForm.find('#customer_address_id'); 
		let $addAddressButton = $customerAddressElement.siblings('button');
		$addAddressButton.prop('disabled', true)
			.attr('data-original-title', 'Kindly select a customer before adding an address.') 
			.tooltip();  
		$customerAddressElement.html('<option>Select Customer Address</option>').prop('disabled', true);
		
        $.get("{{ route('order.customer.list') }}", function(res) {
            $orderForm.find('#customer_id').html(res.output);
			$orderForm.find('#customer_id').prop('disabled', false);
        }, 'Json');
    }
	
	function customerAddresList(obj, isCustomerAddressId = null)
	{
		const customerId = $orderForm.find('#customer_id').val();  
		let $customerAddressElement = $orderForm.find('#customer_address_id');
		let $addAddressButton = $customerAddressElement.siblings('button');

		if (!customerId) { 
			$addAddressButton.prop('disabled', true)
				.attr('data-original-title', 'Kindly select a customer before adding an address.') 
				.tooltip(); 
				   
			$customerAddressElement.html('<option>Select Customer Address</option>')
				.prop('disabled', true);
			return; 
		}
 
		$addAddressButton.prop('disabled', false).attr('data-original-title', '');
 
		$customerAddressElement.prop('disabled', true);
 
		$.ajax({
			url: `{{ url('order/customer/address-list') }}/${customerId}`,
			type: "GET",
			dataType: "json",
			success: function (res) {
				$customerAddressElement.html(res.output).prop('disabled', false);
				if(isCustomerAddressId)
				{
					setTimeout(function(){ 
						$('#orderForm #customer_address_id').val(isCustomerAddressId).trigger('change');
					}, 1000);
				}
			},
			error: function () {
				console.error("Error fetching customer addresses.");
				$customerAddressElement.html('<option>Error loading addresses</option>').prop('disabled', false);
			}
		});
	}
	
	function createCustomer(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get("{{ route('order.customer.create') }}", function(res)
			{ 
				$('body').find('#modal-view-render').html(res.view);
				$('#createCustomerModal').modal('show');  
			});
		} 
	}
	
	function createCustomerAddress(obj, event)
	{
		event.preventDefault();
		const customerId = $orderForm.find('#customer_id').val();  
		if (!customerId) {
			toastrMsg('warning', 'recipient/customer selection required.');
			return;
		}
		
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get(`{{ url('order/customer-address/create') }}/${customerId}`, function(res)
			{ 
				$('body').find('#modal-view-render').html(res.view);
				$('#createCustomerAddressModal').modal('show');  
			});
		} 
	}
	 
    var i = 1; 
    $(`#add_more_product`).click(function()
	{ 
        var html = `<div class="row align-items-end mt-2 removeProductRows"> 
						<div class="col-lg-4 col-sm-6 col-md-6">
							<div class="from-group my-2">
								<label for="packaging-type"> Product Description </label>
								<input type="text" placeholder="Product Description" name="product_discription[]" id="product_discription" required>
							</div>
						</div> 
						<div class="col-lg-3 col-md-6">
							<div class="from-group my-2">
								<label for="packaging-type"> Total Amount </label>
								<input type="text" class="totalAmount" placeholder="Total Amount" value="" name="amount[]" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
							</div>
						</div>  
						<div class="col-lg-3 col-md-6">
							<div class="from-group my-2">
								<label for="packaging-type"> No of Box </label>
								<input type="number" name="quantity[]" class="noofbox" id="quantity" value="" placeholder="Quantity" oninput="allowOnlyNumbers(this)" required>
							</div>
						</div>
						<div class="col-lg-1 col-md-6">
							<div class="from-group my-2">
								<label for="packaging-type"> </label>
								<button type="button" class="btn btn-danger d-002" data-row-id="${i}" onclick="removeProductRow(this)"> <i class="mdi mdi-trash-can"></i> </button> 
							</div>
						</div>
					</div>`;

        $orderForm.find(`.add_product_more`).append(html);
		noOfBoxDimenstion(i);
        i++;
    });

    function removeProductRow(obj) 
	{ 
		const rowId = $(obj).attr('data-row-id');
        $(obj).closest('.removeProductRows').remove(); 
		$orderForm.find(`#removeDimension${rowId}`).remove();
		ewayBillRequired()
    }
	  
	var ewaybillReqAmount = 50000; // Convert to a number 
	
	function ewayBillRequired()
	{
		let totalAmount = 0;

        $('.totalAmount').each(function () {
            let value = parseFloat($(this).val()) || 0; // Get value, default to 0 if empty/invalid
            totalAmount += value;
        });

		let ewaybillInput = $orderForm.find('#ewaybillno'); // Use class instead of ID for closest
		$orderForm.find('#invoice_amount').val(totalAmount.toFixed(2));
		if (totalAmount >= ewaybillReqAmount) {
			ewaybillInput.prop('required', true);
		} else {
			ewaybillInput.prop('required', false);
		}
	}
	
	$(document).on('input', '.totalAmount', function()
	{ 
		ewayBillRequired()
	});
 
	function noOfBoxDimenstion(rowId)
	{ 
		let html = `<div class="row" id="removeDimension${rowId}">
			<div class="col-lg-3 col-sm-6 col-md-6">
				<div class="from-group my-2">
					<label for="packaging-type"> Weight (KG) </label>
					<input type="text" name="weight[]" oninput="allowOnlyNumbers(this)" id="weight" placeholder="Weight (KG)" value="" required>
				</div>
				<label id="volumatric_weight" style="font-weight: 900;"></label>
			</div>

			<div class="col-lg-3 col-sm-6 col-md-6">
				<div class="from-group my-2">
					<label for="packaging-type"> Length </label>
					<input type="text" name="length[]" id="length" placeholder="Length" value="" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
				</div>
			</div>

			<div class="col-lg-3 col-sm-6 col-md-6">
				<div class="from-group my-2">
					<label for="packaging-type"> Width </label>
					<input type="text" name="width[]" id="width" placeholder="Width" value="" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
				</div>
			</div>

			<div class="col-lg-3 col-sm-6 col-md-6">
				<div class="from-group my-2">
					<label for="packaging-type"> Height </label>
					<input type="text" name="height[]" id="height" placeholder="Height" value="" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
				</div>
			</div>
		</div>`;
 
		$orderForm.find(`#dimenstionDetails`).append(html);
	}
	  
    $orderForm.submit(function (event) {
		event.preventDefault();

		// Disable submit button to prevent multiple submissions
		$orderForm.find(':submit').prop('disabled', true);

		// Create FormData object
		var formData = new FormData(this);
		formData.append('_token', "{{ csrf_token() }}"); // Use meta tag for CSRF token

		$.ajax({
			type: $orderForm.attr('method'),
			url: $orderForm.attr('action'),
			data: formData,
			cache: false,
			processData: false,
			contentType: false,
			dataType: 'json', // Use lowercase "json"
			success: function (res) {
				$orderForm.find(':submit').prop('disabled', false); // Re-enable submit button

				toastrMsg(res.status, res.msg); // Show notification

				if (res.status === "success") {
					setTimeout(function () {
						window.location.href = "{{ route('order') }}?weight_order=" + weightOrder;
					}, 1000);
				}
			},
			error: function (xhr) {
				$orderForm.find(':submit').prop('disabled', false); // Re-enable button on failure
				console.error("AJAX error:", xhr.responseText);
				toastrMsg("error", "Something went wrong. Please try again.");
			}
		});
	});
	  
	function allowOnlyNumbers(input) {
		input.value = input.value.replace(/\D/g, '');
	}

</script>
@endpush