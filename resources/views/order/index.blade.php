@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Order')
@section('header_title','Order')
@section('content') 
<link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" rel="stylesheet">
<style>
	.tooltip .tooltiptext 
	{ 
	text-align: left !important; 
	padding: 5px 0 5px 5px !important;
	}	
	td p {
	margin-bottom: 0;
    }
</style>
<div class="content-page">
    <div class="content"> 
        <div class="container-fluid"> 
            <div class="main-order-page-1">
                <div class="main-order-001"> 
                    <div class="inner-page-heading">
						<ul id="tabs">
							@php
								$statuses = ['New', 'Manifested', 'In Transit', 'Delivered', 'All'];
							@endphp

							@foreach($statuses as $st)
								<li class="{{ $status == $st || ($st == 'New' && $status == '') ? 'active' : '' }}" onclick="redirectUrl('{{ $st }}')">
									{{ ucfirst($st) }}
								</li>
							@endforeach
						</ul>
						@if(config('permission.order.add'))
							<div class="order-111-add">
								@if(!in_array($status, ['All', 'New']))
									<button id="downloadAllLable" class="btn-main-1" style="display:none;">⬇ Bulk Label </button>
								@endif
								<a href="{{ route('order.create') }}?weight_order={{ request('weight_order') }}"> <button class="btn-main-1"> + Add Order </button> </a> 
							</div>
						@endif
					</div> 
					
                    <div class="ordr-main-001">
						<ul id="tab">

							@php
								$tables = [
									'New'        => 'neworder_datatable',
									'Manifested' => 'readytoship_datatable',
									'In Transit' => 'pickup_datatable',
									'Delivered'  => 'pickup_datatable',
									'All'        => 'all_datatable',
								];
							@endphp

							@foreach ($tables as $key => $tableId)
								@if($status === $key || ($key === 'New' && $status == ''))
									{{-- Extra filters for "All" --}}
									@if($key === 'All')
										<div class="row row-re mb-2 searchAllForm">
											<div class="col-lg-2 col-sm-6">
												<div class="main-selet-11">
													<input type="text" class="form-control datepicker" name="from_date"
														id="from_date" placeholder="From Date"
														value="{{ request('from_date', '') }}">
												</div>
											</div>
											<div class="col-lg-2 col-sm-6">
												<div class="main-selet-11">
													<input type="text" class="form-control datepicker" name="to_date"
														id="to_date" placeholder="To Date"
														value="{{ request('to_date', '') }}">
												</div>
											</div>

											@if(Auth::user()->role == "admin")  
												<div class="col-lg-2 col-sm-6">
													<div class="main-selet-11">
														<select name="user_id" id="user_id" class="form-control">
															<option value="">Select User</option> 
															@foreach($sellers as $seller)
																<option value="{{ $seller->id }}" 
																	{{ request('user_id') == $seller->id ? 'selected' : '' }}>
																	{{ $seller->name }}
																</option>
															@endforeach
														</select>
													</div>
												</div>
											@endif

											<div class="col-lg-2 col-sm-6">
												<div class="main-selet-11">
													<select name="order_type" id="order_type" class="form-control">
														<option value="">Select Order Type</option> 
														<option value="prepaid" {{ request('order_type') == 'prepaid' ? 'selected' : '' }}>Prepaid</option>
														<option value="cod" {{ request('order_type') == 'cod' ? 'selected' : '' }}>COD</option>
													</select>
												</div>
											</div> 

											<div class="col-lg-2 col-sm-6">
												<div class="main-selet-11">
													<select name="status_courier" id="status_courier" class="form-control">
														@php
															$statuses = \App\Models\Order::distinct()->pluck('status_courier');
														@endphp
														<option value=""> {{ ucfirst($status) }} </option>
														@foreach($statuses as $st)
															<option value="{{ $st }}" {{ request('status_courier') == $st ? 'selected' : '' }}>
																{{ ucfirst($st) }}
															</option>
														@endforeach
													</select>
												</div>
											</div>

											<div class="col-lg-2 col-sm-6">
												<div class="main-selet-11">
													<button type="button" id="searchAllFilter" class="btn-main-1 search_data search-btn-remi">Search</button>
												</div>
											</div>
										</div>   
									@endif

									<li class="active">
										<div class="main-calander-11"> 
											<div class="main-data-teble-1 table-responsive">
												<table id="{{ $tableId }}" style="width:100%">
													<thead>
														<tr>
															@if(in_array($status, ['All', 'New']))
																<th>Sr.No</th>
															@else
																<th> <input type="checkbox" id="checkAll"> </th>
															@endif 
															<th>Order Id</th>
															<th>Seller Details</th>
															<th>Customer Details</th>
															<th>Total Amount</th>
															<th>Shipping Details</th>
															<th>Package Details</th>
															<th>Status</th>
															<th>Action</th>
														</tr>
													</thead> 
												</table>
											</div>
										</div>
									</li>
								@endif
							@endforeach
						</ul>
					</div> 
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade bd-example-modal-lg main-bg0-021 pickupschedulemodal"  data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header pb-0" style="border: none;">
                <h5 class="modal-title pick-up0" id="exampleModalLabel"> Shipment Details </h5> 
			</div>
			 
			<div class="modal-body not_pickup">
				<div class="main-0091-text show_msg"> </div> 
				<div class="main-0921-order-text show_pickup_msg"> </div>  
			</div>
			<div class="modal-footer" style="margin: auto;border: none;">
				<a href="{{ route('order') }}?status=Manifested" id="doitlater" class="btn btn-primary simple-021-btn" > close </a> 
			</div> 
		</div>
	</div>
</div>
  
<div class="customization_popup" role="alert">
    <div class="customization_popup_container">
        <a href="#0" class="customization_popup_close img-replace">X</a> 
        <div class="main-count-te-order-1 shipmentChargesByAll" style="height: 100%;"> 
		</div>
	</div>
</div> 

<div class="modal fade bd-example-modal-lg main-bg0-021 addwarehousemodal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header pb-0" style="border: none;">
                <h5 class="modal-title pick-up0" id="exampleModalLabel"> Add Warehouse </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="warehouseForm" action="{{route('order.warehouse.address')}}" method="post" enctype="multipart/form-data"> 
				@csrf
				<div class="modal-body"> 
					<input type="hidden" name="address_id" id="address_id" >
					<input type="hidden" name="id" id="vendor_id" >
					<input type="hidden" name="shipping_id" id="ware_shipping_id" >
					<div class="from-group my-2">
						<label for="order-id"> WareHouse Name </label>
						<input type="text" placeholder="Enter WareHouse Name" name="warehouse_name" maxlength="18" required>
						<span style="color:red" id="warehouse_error"></span>
					</div>
				</div>
				<div class="modal-footer" style="margin: auto;border: none;"> 
					<button type="sumbit" class="btn btn-primary btn-main-1"> Submit </button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="hotelInfoModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="infoModalLabel">Product Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div> 
			<div class="modal-body" id="infoModalBody">
				<!-- HTML hotel description appears here -->
			</div>
		</div>
	</div>
</div>

@endsection
@push('js')  
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>

<!-- Load the Roboto font from Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

<script>
	var weightOrder = @json(request('weight_order')) || 1;
	
	$('.customization_popup').on('click', function(event) {
		if ($(event.target).is('.customization_popup_close') || $(event.target).is('.customization_popup')) {
			event.preventDefault();
			$(this).removeClass('is-visible');
		}
	});
	 
	function shipNow(obj, event)
	{
		event.preventDefault();
		
	    const kyc_status = @json($authUser->kyc_status);
	   	const role = @json($authUser->role); 
	    if(role != "admin" && kyc_status == 0)
	    {
		    toastrMsg('error','Your order cannot be placed until your KYC is approved.'); 
			return;
		} 
		
		const orderId = $(obj).attr('data-id'); 
		run_waitMe($('body'), 1, 'win8');
		
		$.get(`{{ url('order/shipment/charge') }}/${orderId}?weight_order=${weightOrder}`, function(res)
		{
			$('.customization_popup').addClass('is-visible');
			$('.shipmentChargesByAll').html(res.view);
			$('body').waitMe('hide'); 
		}, 'Json'); 
	}
	
    $(document).ready(function() 
	{
        $('ul.tabs-001 li').click(function() {
            var tab_id = $(this).attr('data-tab');
            $('ul.tabs-001 li').removeClass('current');
            $('.tab-content11').removeClass('current');
            $(this).addClass('current');
            $("#" + tab_id).addClass('current');
		})
	})
	   
	function redirectUrl(status = null) {
		window.location.href = `?weight_order=${weightOrder}&status=${status ?? ''}`;
	}

	 
	// Order listing
	$(document).ready(function () {
		function initializeDataTable(selector, ajaxUrl, additionalData = {}, columns = []) {
			if ($(selector).length === 0) {
				return null;
			}
			console.log(additionalData)
			return $(selector).DataTable({
				processing: true,
				serverSide: true,
				destroy: true, // Prevent duplicate initialization
				bLengthChange: true,
				searching: true,
				bFilter: true,
				bInfo: true,
				iDisplayLength: 25,
				order: [[0, 'desc']],
				bAutoWidth: false,
				language: {
					loadingRecords: '&nbsp;',
					processing: 'Loading...',
				},
				ajax: {
					url: ajaxUrl,
					type: "POST",
					dataType: "json",
					data: function (d) {
						d._token = "{{ csrf_token() }}";
						d.search = $('input[type="search"]').val();
						Object.assign(d, additionalData);
					}
				},
				columns: columns,
				drawCallback: function () {
					$('[data-toggle="tooltip"]').tooltip();
					$('[data-fancybox="images"]').fancybox({ loop: true });
				}
			});
		}

		// Define table columns
		let OrderColumns = [
			{ data: "id", orderable: false },
			{ data: "order_id" },
			{ data: "seller_details" },
			{ data: "customer_details" },
			{ data: "total_amount" },
			{ data: "shipment_details" },
			{ data: "package_details" },
			{ data: "status_courier" },
			{ data: "action" }
		];
		
		var statusCourier = "{{ request('order_status') ?? '' }}";

		// Initialize DataTables for different statuses
		let newOrderTable = initializeDataTable('#neworder_datatable', "{{ route('order.ajax') }}", { status: "New", weightOrder: weightOrder }, OrderColumns);
		let readyToShipTable = initializeDataTable('#readytoship_datatable', "{{ route('order.ajax') }}", { status: "Manifested", weightOrder: weightOrder}, OrderColumns);
		let pickupTable = initializeDataTable('#pickup_datatable', "{{ route('order.ajax') }}", { status: @json($status), weightOrder: weightOrder}, OrderColumns);
		let allOrdersTable = initializeDataTable('#all_datatable', "{{ route('order.ajax') }}", {
			status: 'All',
			status_courier: statusCourier ?? $('.searchAllForm  #status_courier').val(),
			from_date: $('.searchAllForm  #from_date').val(),
			to_date: $('.searchAllForm  #to_date').val(),
			order_type: $('.searchAllForm  #order_type').val(),
			user_id: $('.searchAllForm #user_id').val(),
			weightOrder: weightOrder
		}, OrderColumns);

		// Redraw DataTable on filter button click
		$('#searchAllFilter').click(function (e) { 
			e.preventDefault(); // Prevent default behavior
				initializeDataTable('#all_datatable', "{{ route('order.ajax') }}", {
				status: 'All',
				status_courier: $('.searchAllForm  #status_courier').val(),
				from_date: $('.searchAllForm  #from_date').val(),
				to_date: $('.searchAllForm  #to_date').val(),
				order_type: $('.searchAllForm  #order_type').val(),
				user_id: $('.searchAllForm #user_id').val(),
			}, OrderColumns)
		});
	});  
	//Order End Listing
	
	function cancelNewOrder(obj, e)
	{
		e.preventDefault();
		Swal.fire({
			title:"Are you sure you want to cancel this order?",
			text:"You can't undo this action.",
			type:"warning",
			showCancelButton:!0,
			confirmButtonColor:"#31ce77",
			cancelButtonColor:"#f34943",
			confirmButtonText:"Yes, Cancel!"
			}).then(function (t) {
			if(t.value)
			{
				location.href = obj;
			}
		})
	}
	
	function deleteOrder(obj,e)
	{
		e.preventDefault();
		Swal.fire({
			title:"Are you sure you want to permanently delete this order?",
			text:"You can't undo this action.",
			type:"warning",
			showCancelButton:!0,
			confirmButtonColor:"#31ce77",
			cancelButtonColor:"#f34943",
			confirmButtonText:"Yes, Delete!"
			}).then(function (t) {
			if(t.value)
			{
				location.href = obj;
			}
		})
	}
	
	function cancelOrderApi(obj,e)
	{
		e.preventDefault();
		Swal.fire({
			title:"Do you want to cancel the Order or Shipment?",
			text:"You can't undo this action.",
			type:"warning",
			showCancelButton:!0,
			confirmButtonColor:"#31ce77",
			cancelButtonColor:"#f34943",
			confirmButtonText:"Yes, Canceled!"
			}).then(function (t) {
			if(t.value)
			{
				location.href = obj;
			}
		})
	} 
	
	function shipNowOrder(obj, event)
	{
		event.preventDefault();
		const $obj = $(obj);
		let courierDetail = $obj.attr('data-courier');
		let data;
		try {
			data = JSON.parse(courierDetail);
		} catch (error) { 
			toastrMsg('error', 'Something went wrong.');
			return;
		}
		  
	 	Swal.fire({
			title: "Do you want to ship order?",
			text: "You can't undo this action.",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#31ce77",
			cancelButtonColor: "#f34943",
			confirmButtonText: "Yes"
		}).then((result) => {
			if(result.value)
			{
				run_waitMe($('body'), 1, 'win8');
    			$.post("{{ route('order.ship.now') }}",
    			{
    				_token: "{{csrf_token()}}",
    				data: JSON.stringify(data)
				},
    			function(res)
    			{
					$('body').waitMe('hide');
    				if(res.status == "warehouse")
    				{ 
						$('#warehouse_error').text(res.msg);
						$('#ware_shipping_id').val(res.shipping_id);
						$('#vendor_id').val(res.id);
						$('#address_id').val(res.address_id);
						$('.addwarehousemodal').modal('show');
					}
					else if(res.status == "error")
    				{
						if (res.wallet === "1") {
							Swal.fire({
								title: res.msg,
								icon: "warning",
								showCancelButton: true,
								confirmButtonColor: "#31ce77",
								cancelButtonColor: "#f34943",
								confirmButtonText: "Recharge"
							}).then((walletResult) => {
								if (walletResult.value) {
									$('#rechargeWalletModal').modal('show');
								}
							});
						} else {
							toastrMsg(res.status, res.msg);
						}
					}
    				else
    				{
    					$('.show_msg').html(res.msg);
						$('#order_id').val(res.order_id);
						$('#shipping_id').val(res.shipping_id);
						$('.show_pickup_msg').html(res.pickup_address);
						$('.pickupschedulemodal').modal('show');  
					}
				},'Json'); 
			}
		})
	}
	
	$('#warehouseForm').submit(function(event) {
		event.preventDefault();  
		$(this).find('button').prop('disabled',true); 
		var formData = new FormData(this); 
		formData.append('_token',"{{csrf_token()}}"); 
		$.ajax({
			async: true,
			type: $(this).attr('method'),
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			processData: false,
			contentType: false, 
			dataType: 'Json', 
			success: function (res) 
			{
				$('button').prop('disabled',false);  
				if(res.status == "error")
				{ 
					$('#warehouse_error').text(res.msg)
				}
				else
				{ 
					toastrMsg(res.status,res.msg); 
					$('.addwarehousemodal').modal('hide');
				}
			} 
		});
	});
	
	$(document).ready(function () {
		$('#downloadAllLable').hide();

		// Master Checkbox Click Event
		$('#checkAll').on('change', function () {
			let isChecked = $(this).prop('checked');
			$(".order-checkbox").prop('checked', isChecked);
			$('#downloadAllLable').toggle(isChecked);
		});

		// Individual Checkbox Click Event
		$(document).on('change', '.order-checkbox', function () {
			let totalCheckboxes = $('.order-checkbox').length;
			let checkedCheckboxes = $('.order-checkbox:checked').length;

			$('#checkAll').prop('checked', totalCheckboxes === checkedCheckboxes);
			$('#downloadAllLable').toggle(checkedCheckboxes > 0);
		});
		
		$('#downloadAllLable').on('click', function () {
			let orderIds = $(".order-checkbox:checked").map(function () {
				return $(this).val();
			}).get();

			if (orderIds.length === 0) {
				toastrMsg('error', 'Choose at least one item to download.');
				return;
			}

			// Send as array: order_ids[]=1&order_ids[]=2
			window.location.href = "{{ route('order.download-all-lable') }}" + "?" + 
				$.param({ order_ids: orderIds });
		});

	});
  
	$('.orderAll').on('click', function(e)
	{
		var studentIdArr = [];
		$(".order-checkbox:checked").each(function() {
			studentIdArr.push($(this).attr('data-order_id'));
		});
		if (studentIdArr.length <= 0) 
		{  
			toastrMsg('error','Choose min one item to remove.');
		} 
		else 
		{
			Swal.fire({
				title:"Are you sure you want to pickup all order?", 
				type:"warning",
				showCancelButton:!0,
				confirmButtonColor:"#31ce77",
				cancelButtonColor:"#f34943",
				confirmButtonText:"Yes, Pickup it!"
				}).then(function (t) {
				if(t.value)
				{  
					var stuId = studentIdArr.join(","); 
					$('#order_id_all').val(stuId);
					$('.pickupscheduleall').modal('show');
				}
			})    
		}
	});
	
	$('#pickupScheduleAllForm').submit(function(event) 
	{
		event.preventDefault(); 
		run_waitMe($('body'), 1, 'win8')
		var formData = new FormData(this); 
		formData.append('_token','{{csrf_token()}}');   
		$.ajax({
			async: true,
			type: $(this).attr('method'),
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			processData: false,
			contentType: false, 
			dataType: 'Json', 
			success: function (res) 
			{ 
				$('body').waitMe('hide');
				if(res.status == "error")
				{ 
					$('#schdule_all_error').text(res.msg)
				}
				else
				{  
					$('.pickupscheduleall').modal('hide');
					toastrMsg(res.status,res.msg);  
					setTimeout(function(){
						window.location.href = "{{route('order')}}?status=Ready To Ship";	
					},1000)
				}
			} 
		});
	});
	
	$('#pickupScheduleForm').submit(function(event) 
	{
		event.preventDefault();  
		//	$(this).find('button').prop('disabled',true); 
		run_waitMe($('body'), 1, 'win8')
		var formData = new FormData(this); 
		formData.append('_token','{{csrf_token()}}');   
		$.ajax({
			async: true,
			type: $(this).attr('method'),
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			processData: false,
			contentType: false, 
			dataType: 'Json', 
			success: function (res) 
			{ 
				$('body').waitMe('hide');
				if(res.status == "error")
				{ 
					$('#schdule_error').text(res.msg)
				}
				else
				{  
					$('.pickupschedulemodal').modal('hide');
					toastrMsg(res.status,res.msg);  
					setTimeout(function(){
						window.location.href = "{{route('order')}}?status=Ready To Ship";	
					},1000)
				}
			} 
		});
	});
	
	function schedulePickup(obj,event)
	{ 
		event.preventDefault();  
		var order_id = $(obj).attr('data-order_id');
		var shipping_id = $(obj).attr('data-shipping_id');
		var address = $(obj).attr('data-address');
		var msg = $(obj).attr('data-msg'); 
		$('.show_msg').html(msg);
		$('#order_id').val(order_id);
		$('#shipping_id').val(shipping_id);
		$('.show_pickup_msg').html(address);
		$('.pickupschedulemodal').modal('show'); 
	}
	
	$(document).on('click', '.show-details-btn', function () {
		var items = $(this).data('order'); // JSON string automatically converted by jQuery
		if (typeof items === 'string') {
			items = JSON.parse(items);
		}

		var totalAmount = 0;
		var html = "<table class='table table-bordered table-sm'><thead><tr><th>Category</th><th>Name</th><th>SKU</th><th>HSN</th><th>Amount</th><th>Qty</th></tr></thead><tbody>";

		items.forEach(function(item) {
			html += "<tr>" +
						"<td>" + item.product_category + "</td>" +
						"<td>" + item.product_name + "</td>" +
						"<td>" + item.sku_number + "</td>" +
						"<td>" + item.hsn_number + "</td>" +
						"<td>" + item.amount + "</td>" +
						"<td>" + item.quantity + "</td>" +
					"</tr>";
			totalAmount += parseFloat(item.amount * item.quantity) || 0;
		});

		html += "</tbody>";
		html += "<tfoot><tr><th colspan='4'>Total</th><th colspan='2'>" + totalAmount.toFixed(2) + "</th></tr></tfoot>";
		html += "</table>";

		$('#infoModalLabel').html("Product Details");
		$('#infoModalBody').html(html);
		$('#infoModal').modal('show');
	});
</script> 
@endpush