@extends('layouts.backend.app')
@section('title', config('setting.company_name') . ' - Create Bulk Order')
@section('header_title', 'Create Bulk Order')
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
            <form id="orderForm" method="post" action="{{ route('order.bulk-store') }}" enctype="multipart/form-data">
                <div class="main-create-order mt-3">   
					<div class="main-rowx-1">
                        <div class="main-order-001">
							<div class="row">
								<div class="col-lg-12"> 
									<div class="main-vender">
										<h5> Pickup Location <span class="text-danger">*</span></h5>
									</div>
									<div class="row">
										<div class="from-group col-6"> 
											<div class="main-rox-input">
												<select name="warehouse_id" class="control-form select2" id="warehouse_id" style="border-radius: 5px 0px 0px 5px" onchange="warehousePickup(this)" required>
													<option value="">Select Pickup Location</option> 
												</select> 
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
                            <div class="main-vender">
                                <h5> Upload Excel <a href="{{ url('assets/b2c_sample.xlsx') }}">(B2C Sample Format)</a> <a href="{{ url('assets/b2b_sample.xlsx') }}">(B2B Sample Format)</a></h5>
                            </div>
                            <div class="row"> 
								 
								<div class="col-lg-6 col-sm-6 col-md-6">
                                    <div class="from-group my-2">
                                        <label for="packaging-type"> Type Of Package <span class="text-danger">*</span></label>
                                        <select name="type_of_package" class="control-form select2" id="type_of_package" style="border-radius: 5px 0px 0px 5px" required>
											<option value="1">B2C</option> 
											<option value="2">B2B</option> 
										</select> 
                                    </div>
                                </div> 
                                <div class="col-lg-6 col-sm-6 col-md-6">
                                    <div class="from-group my-2">
                                        <label for="packaging-type"> Upload Excel <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="bulk_excel" name="bulk_excel" accept=".xlsx,.xls" required> 
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
					const weightOrder = res.type_of_package ?? 1;
					setTimeout(function () {
						window.location.href = `{{ route('order') }}?weight_order=${weightOrder}`; 
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

</script>
@endpush