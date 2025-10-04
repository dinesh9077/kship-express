@extends('layouts.backend.app')
@section('title', config('setting.company_name') . ' - Dashboard')
@section('header_title', 'Dashboard')
@section('content')

    <style>
        .new-tbs-white td {
            background-color: white !important;
            border-radius: 10px;
        }

        /* Container */
        .welcome-card {
            width: 100%;
            height: 220px;
            overflow: hidden;
            position: relative;
            border-radius: 15px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Slider */
        .welcome-slider {
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform 0.8s ease-in-out;
        }

        /* Single Slide */
        .welcome-slide {
            min-width: 100%;
            height: 100%;
        }

        /* Image Style */
        .welcome-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>


    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="main-dashbordr-22">
                    <div class="row">
                        <div class="col-xl-8"> 
                            <div class="row new-re">
                                <div class="col-lg-8 col-md-12">
                                    <div class="welcome-card">
                                        <div class="welcome-slider" id="slider">
                                            @forelse($banners as $banner)
                                                <div class="welcome-slide">
                                                    <img src="{{ asset('storage/'.$banner->banner_image) }}"
                                                        alt="{{ $banner->title }}">
                                                </div>
                                            @empty
                                                <div class="welcome-slide">
                                                    <img src="{{ asset('assets/images/dashbord/dashboard-images.png') }}"
                                                        alt="Default Banner">
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-12 new-margin-dashes">
                                    <div class="help-card">
                                        <img src="{{ asset('assets/images/dashbord/help-card-img.png') }}" alt=""
                                            style="width: fit-content;">
                                        <h6 class="help-card-title"> Need Help?</h6>
                                        <p class="help-card-des">We are here to solve your doubts. Reach out to us on
                                            1234567890</p>

                                    </div>
                                </div>
                            </div>
                            <div class="row main-mb-2  new-re">
                                <div class="col-xl-4 col-md-4 col-sm-12">
                                    <div class="row">
                                        <div class="col-12 mt-3">
                                            <div class="main-box-10-1 border-c-1">
                                                <div class="main-box-cont-dash">
                                                    <h5> Total Wallet Amount </h5>
                                                    <h3> ₹{{ $overallWalletAmount ?? 0 }} </h3>
                                                </div>
                                                <img class="chart-1" src="{{ asset('assets/images/dashbord/chat-1.png') }}">
                                            </div>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <a href="{{ url('order') }}?weight_order=1&status=All">
                                                <div class="main-box-10-1 border-c-2">
                                                    <div class="main-box-cont-dash">
                                                        <h5> Total Order </h5>
                                                        <h3> {{ $totalLightWeightShipment }} </h3>
                                                    </div>
                                                    <div class="main-box-cont-dash mt-3">
                                                        <h5> Today's Order</h5>
                                                        <h3> {{ $todaysLightWeightOrder }}</h3>
                                                    </div>
                                                </div>
                                                <img class="chart-2" src="{{ asset('assets/images/dashbord/chat-2.png') }}">

                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-12 mt-3">
                                    <div class="main-box-10-1 border-c-3">
                                        <div class="main-box-cont-dash">
                                            <h5> Total Invoice Amount </h5>
                                            <h3> ₹{{ $totalInvoiceAmount ?? 0 }} </h3>

                                        </div>
                                        <div class="mt-5" style="width: 100%;">
                                            <img src="{{ asset('assets/images/dashbord/chat-3.png') }}" alt=""
                                                class="chat-3-img">
                                        </div>
                                        <div class="main-box-cont-dash new-amount-div mt-3 ">
                                            <h5> Today's Invoice Amount </h5>
                                            <h3>
                                                {{ $tadaysInvoiceAmount ?? 0 }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-12">
                                    <div class="row">
                                        <div class="col-12 mt-3">
                                            <div class="main-box-10-1 border-c-4">
                                                <!-- <img class="main-dash-bord" src="{{ asset('assets/images/dashbord/order-dash.png') }}"> -->

                                                <div class="main-box-cont-dash">
                                                    <h5> Today's Recharge </h5>
                                                    <h3> ₹{{ $todayRecharge ?? 0 }} </h3>
                                                </div>
                                                <img class="chart-2"
                                                    src="{{ asset('assets/images/dashbord/chat-4.png') }}">

                                            </div>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <div class="main-box-10-1 border-c-5">
                                                <!-- <img class="main-dash-bord" src="{{ asset('assets/images/dashbord/revenue.png') }}"> -->
                                                <div class="main-box-cont-dash">
                                                    <h5> Total COD Amount </h5>
                                                    <h3> ₹{{ $totalCodAmount ?? 0 }}</h3>

                                                </div>
                                                <div class="main-box-cont-dash mt-3">
                                                    <h5> Today's COD Amount </h5>
                                                    <h3>{{ $tadaysCodAmount ?? 0 }}</h3>
                                                </div>
                                                <img class="chart-2"
                                                    src="{{ asset('assets/images/dashbord/chat-5.png') }}">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-xl-4">



                            <div class="row main-mb-2 align-items-center">
                                <div class="col-lg-12 col-md-12">
                                    <div class="main-roow-1">
                                        <div class="row">
                                            <div class="col-12 new-border-design-single">
                                                <div class="row">
                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12  border-1-top">
                                                        <a href="{{ url('order') }}?weight_order=1&status=Manifested">
                                                            <div class="main012">
                                                                <img src="{{ asset('assets/images/dashbord/rr-1.png') }}">
                                                                <h5> {{ $manifested }} </h5>
                                                                <div
                                                                    style="display: flex;     justify-content: space-between;">
                                                                    <h4> Manifested / Pending Pickup </h4>
                                                                    <img src="{{ asset('assets/images/dashbord/arro.png') }}"
                                                                        style="object-fit: none; width: fit-content;">
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 ">
                                                        <a
                                                            href="{{ url('order') }}?weight_order=1&status=All&order_status=cancelled">
                                                            <div class="main012">
                                                                <img src="{{ asset('assets/images/dashbord/rr-2.png') }}">
                                                                <h5> {{ $cancelledOrder }} </h5>
                                                                <div
                                                                    style="display: flex;     justify-content: space-between;">
                                                                    <h4> Cancelled </h4>
                                                                    <img src="{{ asset('assets/images/dashbord/arro.png') }}"
                                                                        style="object-fit: none; width: fit-content;">
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-12 new-border-design-single">
                                                <div class="row">
                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 border-1-center">
                                                        <a href="{{ url('order') }}?weight_order=1&status=In Transit">
                                                            <div class="main012">
                                                                <img src="{{ asset('assets/images/dashbord/rr-4.png') }}">
                                                                <h5> {{ $inTransit }} </h5>
                                                                <div
                                                                    style="display: flex;     justify-content: space-between;">
                                                                    <h4> In-Transit </h4>
                                                                    <img src="{{ asset('assets/images/dashbord/arro.png') }}"
                                                                        style="object-fit: none; width: fit-content;">

                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                                        <div class="main012">
                                                            <img src="{{ asset('assets/images/dashbord/rr-5.png') }}">
                                                            <h5> {{ $rto }} </h5>
                                                            <div
                                                                style="display: flex;     justify-content: space-between;">
                                                                <h4> RTO </h4>
                                                                <img src="{{ asset('assets/images/dashbord/arro.png') }}"
                                                                    style="object-fit: none; width: fit-content;">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 border-1-bottom">
                                                        <div class="main012">
                                                            <img src="{{ asset('assets/images/dashbord/rr-3.png') }}">
                                                            <h5> {{ $outForDelivery }} </h5>
                                                            <div
                                                                style="display: flex;     justify-content: space-between;">
                                                                <h4> Out For Delivery </h4>
                                                                <img src="{{ asset('assets/images/dashbord/arro.png') }}"
                                                                    style="object-fit: none; width: fit-content;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                                        <a
                                                            href="{{ url('order') }}?weight_order=1&status=All&order_status=delivered">
                                                            <div class="main012">
                                                                <img src="{{ asset('assets/images/dashbord/rr-6.png') }}">
                                                                <h5> {{ $delivered }} </h5>
                                                                <div
                                                                    style="display: flex;     justify-content: space-between;">
                                                                    <h4> Delivered </h4>
                                                                    <img src="{{ asset('assets/images/dashbord/arro.png') }}"
                                                                        style="object-fit: none; width: fit-content;">

                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>






                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin: 10px;"> 
            <div
                style="display: flex; justify-content : space-between; align-items: center; padding : 2px 20px; background : #5640B0; border-radius: 10px;">
                <h6 style="font-size: 18px; color : white; font-weight : 500; ">Recent Orders</h6>
                <a href="{{ url('order') }}?weight_order=1&status=All"
                    style="font-size: 16px; color : white; font-weight : 300;text-decoration: underline !important; ">View
                    Details</a>
            </div>

            <table id="neworder_datatable" style="width:100%" class="dataTable no-footer"
                aria-describedby="neworder_datatable_info">
                <thead>
                    <tr>
                        <th class="sorting_disabled sorting_desc" rowspan="1" colspan="1" aria-label="Sr.No">Sr.No
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="neworder_datatable" rowspan="1"
                            colspan="1" aria-label="Order Id: activate to sort column ascending">Order Id
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="neworder_datatable" rowspan="1"
                            colspan="1" aria-label="Seller Details: activate to sort column ascending">
                            Seller Details</th>
                        <th class="sorting" tabindex="0" aria-controls="neworder_datatable" rowspan="1"
                            colspan="1" aria-label="Customer Details: activate to sort column ascending">
                            Customer Details</th>
                        <th class="sorting" tabindex="0" aria-controls="neworder_datatable" rowspan="1"
                            colspan="1" aria-label="Total Amount: activate to sort column ascending">Total
                            Amount</th>
                        <th class="sorting" tabindex="0" aria-controls="neworder_datatable" rowspan="1"
                            colspan="1" aria-label="Shipping Details: activate to sort column ascending">
                            Shipping Details</th>
                             
                        <th class="sorting" tabindex="0" aria-controls="neworder_datatable" rowspan="1"
                            colspan="1" aria-label="Shipping Details: activate to sort column ascending">
                            Package Details</th>
                        <th class="sorting" tabindex="0" aria-controls="neworder_datatable" rowspan="1"
                            colspan="1" aria-label="Status: activate to sort column ascending">Status</th>
                        <th class="sorting" tabindex="0" aria-controls="neworder_datatable" rowspan="1"
                            colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
                    </tr>
                </thead>
                <tbody>
					@foreach($recentOrders as $order)
						@php
							$isCOD   = strtolower($order->order_type) === "cod";
							$amount  = $isCOD ? $order->cod_amount : $order->invoice_amount;
							$label   = $isCOD ? 'COD Amount' : 'Invoice Amount';

							$warehouse = optional($order->warehouse);
							$customer  = optional($order->customer);
							$address   = optional($order->customerAddress)->address;
						@endphp
						<tr class="odd new-tbs-white">
							<td class="sorting_1">1</td>
							<td>#{{ $order->id }}<br>
								@if($user->role === "admin")
									<p>{{ $order->user->name ?? 'N.A' }}</p>
								@endif
							</td>
							<td>
								<div class='main-cont1-2'>
									<p>{{ $warehouse->warehouse_name }} ({{ $warehouse->company_name }})</p>
									<p>{{ $warehouse->contact_name }}</p>
									<p>{{ $warehouse->contact_number }}</p>
									<p>{{ $warehouse->address }}</p>
								</div>
							</td> 
							<td>
								<div class='main-cont1-2'> 
									<p>{{ optional($order->customer)->first_name }} {{ optional($order->customer)->last_name }}</p> 
									<p>{{ optional($order->customer)->mobile }}</p> 
									<div class='tooltip'>View Address<span class='tooltiptext'><b>{{ optional($order->customer)->first_name }} {{ optional($order->customer)->last_name }}</b><br><b>Address</b>: {{ optional($order->customerAddress)->address }}</span></div>
								</div>
							</td> 
							<td>
								<div class='main-cont1-2'>
									<p class="{{ strtolower($order->order_type) }}">{{ $order->order_type }}</p>
									<p>{{ $label }}: {{ $amount }}</p>
								</div>
							</td> 
							<td>
								<div class='main-cont1-1'>
									<div class='checkbox checkbox-purple'>
										Order Prefix/LR No: <a href="{{ url('order/details/'.$order->id) }}?weight_order={{ $order->weight_order }}&status=New">#{{ $order->order_prefix }}</a>
									</div>
									<div class='checkbox checkbox-purple'>
										Courier: {{ $order->courier_name }}
									</div> 
									<span style='padding-left:0'>
										<a href='javascript:;' class='show-details-btn' 
											data-order='{{ $order->orderItems->map(fn($item) => [
												"product_category" => $item->product_category,
												"product_name" => $item->product_name,
												"sku_number" => $item->sku_number,
												"hsn_number" => $item->hsn_number,
												"amount" => $item->amount,
												"quantity" => $item->quantity,
											])->toJson() }}'
											style='color: #1A4BEC; font-size: 15px;'>
											View Products
										</a>
									</span>
								</div>
							</td> 
							<td>
								@php
									$length = (float) $order->length;
									$width  = (float) $order->width;
									$height = (float) $order->height;
									$weight = (float) $order->weight;
									
									// Avoid division by zero → standard divisor 5000
									$volumetricWt = ($length * $width * $height) / 5000;
									
									if($order->weight_order == 2) {
										$totalBox = $order->orderItems->sum(function ($item) {
											return $item->dimensions['no_of_box'] ?? 0;
										});
										
										$totalVolumetric = 0; 
										foreach ($order->orderItems as $box) {
											$volume = $box->dimensions['length'] * $box->dimensions['width'] * $box->dimensions['height'];
											$volumetricWeight = ($volume / 5000) * ($box->dimensions['no_of_box'] ?? 0);
											$totalVolumetric += $volumetricWeight;
										}
									}
								@endphp
								
								<div class="main-cont1-2">
									@if($order->weight_order == 2)
										<p>{{ $totalBox }} Boxe(s) - (B2B)</p>
										<p>Dead wt.: {{ number_format($weight, 2) }} Kg</p>
										<p>Volumetric wt.: {{ number_format($totalVolumetric, 2) }} Kg</p>
									@else
										<p>Dead wt.: {{ number_format($weight, 2) }} Kg</p>
										<p>{{ $length }} x {{ $width }} x {{ $height }} (cm)</p>
										<p>Volumetric wt.: {{ number_format($volumetricWt, 2) }} Kg</p>
									@endif
								</div>
							</td>
							<td>
								@php
									$statusColor = strtolower($order->status_courier) == "cancelled" ? 'cod' : 'prepaid';
								@endphp
								<p class="{{ $statusColor }}">{{ $order->status_courier }}</p>
								@if(strtolower($order->status_courier) == "cancelled")
									<p style="padding-left:0">{{ $order->reason_cancel ?? 'The client has cancelled this order before it was shipped.' }}</p>
								@endif
								<p style="padding-left:0">{{ date('Y M d | h:i A', strtotime($order->created_at)) }}</p>
							</td>
							<td>
								<div class="main-btn-1">
									@if($order->status_courier === "New")
										<a href="javascript:;">
											<button type="button" 
												class="customization_popup_trigger btn-light-1" 
												data-weight-order="{{ $order->weight_order }}" 
												onclick="shipNow(this, event)" 
												data-id="{{ $order->id }}">
												Ship Now
											</button>
										</a>
									@endif
									
									<div class="mian-btn">
										<div class="btn-group">
											<button class="dropbtn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="fas fa-ellipsis-v"></i>
											</button>
											<div class="dropdown-menu">
												<a class="dropdown-item" href="{{ url('order/details/'.$order->id) }}?weight_order={{ $order->weight_order }}&status=New">
													View Order
												</a>
												<hr class="m-0">
												
												@if(config('permission.order.add'))
													<a class="dropdown-item" href="{{ url('order/clone/'.$order->id) }}?weight_order={{ $order->weight_order }}">
														Clone Order
													</a>
													<hr class="m-0">
												@endif
												
												@if($order->status_courier === "New")
													@if(config('permission.order.edit'))
														<a class="dropdown-item" href="{{ url('order/edit/'.$order->id) }}?weight_order={{ $order->weight_order }}">
															Edit Order
														</a>
														<hr class="m-0">
													@endif
													
													@if(config('permission.order.delete'))
														<a class="dropdown-item" 
															href="{{ url('order/cancel/'.$order->id) }}?weight_order={{ $order->weight_order }}" 
															style="color: red;" 
															onclick="cancelNewOrder(this, event)">
															Cancel Order
														</a>
													@endif
												@endif 
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr> 
					@endforeach
                </tbody>
            </table> 
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
@endsection
@push('js')
	<script> 
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
			const weightOrder = $(obj).attr('data-weight-order'); 
			run_waitMe($('body'), 1, 'win8');
			
			$.get(`{{ url('order/shipment/charge') }}/${orderId}?weight_order=${weightOrder}`, function(res)
			{
				$('.customization_popup').addClass('is-visible');
				$('.shipmentChargesByAll').html(res.view);
				$('body').waitMe('hide'); 
			}, 'Json'); 
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
		
        let index = 0;
        const slides = document.querySelectorAll(".welcome-slide");
        const total = slides.length;
        const slider = document.getElementById("slider");

        setInterval(() => {
            index = (index + 1) % total;
            slider.style.transform = `translateX(-${index * 100}%)`;
        }, 3000); // 3 second auto change

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
