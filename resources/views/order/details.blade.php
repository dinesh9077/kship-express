@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Order Details')
@section('header_title','Order Details')
@section('content') 

<style>
    .mani-00-tetxt p{
        font-size: 14px;
    }
    
    @media(max-width: 1600px){
        .mani-00-tetxt p {
          font-size: 12px;
        }
    }
</style>

<div class="content-page">
    <div class="content">
		
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="main-frist-heder121 mt-2">
				
                <div class="row">
                    <div class="col-lg-8">
                        <div class="main-le-ri-text">
                            <div class="left-order-text">
                                <div class="mnain-frisnol-det m-0">
									@php($class = ($order->status_courier == "CANCELED") ? 'background: #80000024;color: #800000;' : 'background: #00800024;color: #008000;') 
                                    <a href="{{ route('order') }}?weight_order={{ request('weight_order')}}&status={{ request('status')}}"><i class="mdi mdi-chevron-left"></i> <b> #{{ $order->order_prefix }}</b> </a> 
									<span style="{{ $class }}"> {{ $order->status_courier }} </span>
								</div>
							</div> 
							<div class="right-order-text"> 
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-8">
						<div class="main-ord-1-text">
							<div class="main-hed-order-details">
								<div class="main-headet-det">
									<img src="{{asset('assets/images/new1.png')}}">
									<h5> Order Details </h5>
								</div>
								
								<div class="row" style="padding-bottom: 10px; border-bottom: 1px solid #8888882e;">
									<div class="col-lg-3 col-md-6 ship-col">
										<div class="mani-00-tetxt">
											<p> Order created on channel </p>
											<h5> {{ date('Y M d h:i A',strtotime($order->order_date)) }} </h5>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-6 ship-col">
										<div class="mani-00-tetxt">
											<p> Payment </p>
											<div class="main-cont1-2" style="display: flex; column-gap: 10px;">
												<p style="color: #000;">{{ $order->order_type == "cod" ? $order->cod_amount : $order->invoice_amount }} </p>
												<p class="{{ strtolower($order->order_type) }}" > {{ $order->order_type }} </p>
											</div>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-6 ship-col">
										<div class="mani-00-tetxt">
											<p> Pickup Details </p> 
											<h5> {{ $order->warehouse->contact_name ?? 'N/A' }} </h5>
											<h5> {{ $order->warehouse->contact_number ?? 'N/A' }} </h5> 
										</div>
									</div>
									
									<div class="col-lg-3 col-md-6 ship-col">
										<div class="mani-00-tetxt">
											<p> Pickup Address</p>
											<h5> {{ $order->warehouse->address ?? 'N/A' }} {{ $order->warehouse->city ?? '' }} {{ $order->warehouse->state ?? '' }} {{ $order->warehouse->country ?? '' }}, {{ $order->warehouse->zip_code ?? '' }} </h5>
										</div>
									</div>
									<div class="col-lg-3 col-md-6 ship-col">
										<div class="mani-00-tetxt">
											<p> Invoice Number</p>
											<h5> {{ $order->invoice_no ?? '' }} </h5>
										</div>
									</div>
									<div class="col-lg-3 col-md-6 ship-col">
										<div class="mani-00-tetxt">
											<p> Invoice Amount</p>
											<h5> {{ $order->invoice_amount ?? '' }} </h5>
										</div>
									</div>
									<div class="col-lg-3 col-md-6 ship-col">
										<div class="mani-00-tetxt">
											<p> E-waybill No.</p>
											<h5> {{ $order->ewaybillno ?? '' }} </h5>
										</div>
									</div>
									
								</div>
							</div>
						</div>
						<div class="main-ord-1-text">
							<div class="main-hed-order-details">
								<div class="main-headet-det">
									<img src="{{asset('assets/images/new3.png')}}">
									<h5> Customer Details </h5>
								</div>
								
								<div class="row" style="padding-bottom: 10px;">
									<div class="col-lg-3 col-md-6 ship-col">
										<div class="mani-00-tetxt">
											<p> Name </p>
											<h5> {{ $order->customer->first_name ?? '' }} {{ $order->customer->last_name ?? '' }} </h5>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-6 ship-col">
										<div class="mani-00-tetxt">
											<p> Contact No. </p>
											<h5> {{ $order->customer->mobile ?? '' }} </h5>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-6 ship-col">
										<div class="mani-00-tetxt">
											<p> Email </p>
											<h5> {{ $order->customer->email ?? '' }} </h5>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-6 ship-col">
										<div class="mani-00-tetxt">
											<p> Customer Address </p>
											<h5> {{ $order->customerAddress->address ?? 'N/A' }} {{ $order->customerAddress->city ?? '' }} {{ $order->customerAddress->state ?? '' }} {{ $order->customerAddress->country ?? '' }}, {{ $order->customerAddress->zip_code ?? '' }} </h5>
										</div>
									</div>
								</div>
							</div>
						</div>
						 
						<div class="main-ord-1-text">
							<div class="main-hed-order-details">
								<div class="main-headet-det">
									<img src="{{asset('assets/images/new4.png')}}">
									<h5> Product Details </h5>
								</div>
								
								<div class="table-responsive">
									
									<table class="table mb-0">
										<thead class="thead-light">
											<tr> 
												<th> Description </th> 
												<th> Qty (No Of Box)</th> 
												<th> Amount </th> 
											</tr>
										</thead>
										@if($order->orderItems->isNotEmpty())
											<tbody>
												@foreach($order->orderItems as $orderItem)
												<tr> 
													<td> {{ $orderItem->product_discription }} </td>   
													<td> {{ $orderItem->quantity }}  </td>
													<td> {{ $orderItem->amount }} </td>   
												</tr>
												@endforeach 
												<tr>
													<td> </td>  
													<td> <b> Order Total </b> </td>
													<td> <b> {{ $order->total_amount }} </b> </td>
												</tr>
											</tbody>
										@endif
									</table>
								</div>
							</div>
						</div>
						
						<div class="main-ord-1-text">
							<div class="main-hed-order-details">
								<div class="main-headet-det">
									<img src="{{asset('assets/images/new2.png')}}">
									<h5> Package Details </h5>
								</div>
								@if($order->orderItems->isNotEmpty())
									@foreach($order->orderItems as $orderItem)
										<div class="row" style="padding-bottom: 10px;">
											<div class="col-lg-3 col-md-6 ship-col">
												<div class="mani-00-tetxt">
													<p> Weight (in Kg) </p>
													<h5> {{ $orderItem->dimensions['weight'] ?? 0 }} </h5>
												</div>
											</div> 
											<div class="col-lg-3 col-md-6 ship-col">
												<div class="mani-00-tetxt">
													<p> Length (in cm) </p>
													<h5> {{ $orderItem->dimensions['length'] ?? 0 }} </h5>
												</div>
											</div>  
											<div class="col-lg-3 col-md-6 ship-col">
												<div class="mani-00-tetxt">
													<p> Height (in cm) </p>
													<h5> {{ $orderItem->dimensions['height'] ?? 0 }} </h5>
												</div>
											</div> 
											<div class="col-lg-3 col-md-6 ship-col">
												<div class="mani-00-tetxt">
													<p> Width (in cm) </p>
													<h5> {{ $orderItem->dimensions['width'] ?? 0 }} </h5>
												</div>
											</div>  
										</div>
									@endforeach
								@endif
							</div>
						</div>
						<div class="main-ord-1-text">
							<div class="main-hed-order-details">
								<div class="main-headet-det">
									<img src="{{asset('assets/images/new2.png')}}">
									<h5> Invoice Document </h5>
								</div>
								@if(!empty($order->invoice_document))
									<div class="row mt-2">
										@foreach($order->invoice_document as $index => $invoice_Image)
											<div class="col-lg-2 col-sm-2 col-md-2">
												<div class="from-group my-2">
													<label for="packaging-type"> Invoice {{ ($index + 1) }}</label>
													<a href="{{ url('storage/orders/'.$order->id, $invoice_Image) }}" target="_blank"><img src="{{ url('storage/orders/'.$order->id, $invoice_Image) }}" height=80> </a>
												</div>
											</div>
										@endforeach
									</div> 
								@endif
							</div>
						</div>
						@if(!empty($order->order_image))
							<div class="main-ord-1-text">
								<div class="main-hed-order-details">
									<div class="main-headet-det">
										<img src="{{asset('assets/images/new2.png')}}">
										<h5> Other Document </h5>
									</div> 
									<div class="row mt-2">
										@foreach($order->order_image as $index => $order_image)
											<div class="col-lg-2 col-sm-2 col-md-2">
												<div class="from-group my-2">
													<label for="packaging-type"> Document {{ ($index + 1) }}</label>
													<a href="{{ url('storage/orders/'.$order->id, $order_image) }}" target="_blank"><img src="{{ url('storage/orders/'.$order->id, $order_image) }}" height=80> </a>
												</div>
											</div>
										@endforeach
									</div>  
								</div>
							</div>
						@endif
					</div>
					
					 <div class="col-lg-4">
                        <div class="main-tba-show-order-1">
                            <div class="tabs effect-3">
                                <!-- tab-title -->
                                <input type="radio" id="tab-1" name="tab-effect-3" checked="checked">
                                <span> Activity Log </span>

                                <input type="radio" id="tab-2" name="tab-effect-3">
                                <span>Tracking Info</span>

                                <div class="line ease"></div>

                                <!-- tab-content -->
                                <div class="tab-content">
                                    <section id="tab-item-1">
                                        <div class="main-content-001">
											@foreach($orderActivities as $key => $row)
												<div class="step {{ $key == 0 ? 'completed' : '' }}  {{ $key == 1 ? 'active' : '' }}">
													<div class="v-stepper">
														<div class="circle"></div>
														<div class="line"></div>
													</div> 
													<div class="content">
														<h4> {{ $row->activity_name }} </h4>
														<h6> {{ date('d M Y, H:i:s', strtotime($row->created_at)) }} </h6>
													</div>
												</div>
											@endforeach 
                                        </div>
                                    </section>
                                    <section id="tab-item-2">
										@if(!empty($order->awb_number) && !empty($trackingHistories))
											<div class="main-content-001">
												@foreach($trackingHistories as $key => $trackingHistory) 
													@if($order->shipping_company_id == 2)
														<div class="step {{ $key == 0 ? 'completed' : '' }}  {{ $key == 1 ? 'active' : '' }}">
															<div class="v-stepper">
																<div class="circle"></div>
																<div class="line"></div>
															</div> 
															<div class="content">
																<h4> {{ $trackingHistory['status'] ?? '' }} </h4>
																<h4> {{ $trackingHistory['scan_remark'] ?? '' }} </h4>
																<h6> {{ date('d M Y, h:i a', strtotime($trackingHistory['scan_timestamp'])) }} </h6>
															</div>
														</div>
													@endif 
													@if($order->shipping_company_id == 3)
														<div class="step {{ $key == 0 ? 'completed' : '' }}  {{ $key == 1 ? 'active' : '' }}">
															<div class="v-stepper">
																<div class="circle"></div>
																<div class="line"></div>
															</div> 
															<div class="content">
																<h4> {{ $trackingHistory['ScanDetail']['Scan'] ?? '' }} </h4>
																<h4> {{ $trackingHistory['ScanDetail']['Instructions'] ?? '' }} </h4>
																<h6> {{ date('d M Y, h:i a', strtotime($trackingHistory['ScanDetail']['ScanDateTime'])) }} </h6>
															</div>
														</div>
													@endif 
												@endforeach 
											</div>
										@else	
											<p> No Data Available </p>
										@endif
                                    </section>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection