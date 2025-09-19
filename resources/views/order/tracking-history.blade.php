@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Order Tracking History')
@section('content') 
 
<div class="content-page">
    <div class="content"> 
        <div class="container-fluid">
            <section class="main-order-sec">
                <div class="custom-container">
                    <div class="main-cls-1">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="main-head-text">
                                    <h4> Order Information </h4> 
                                    <ul class="list-ordr-detal">
                                        <li> <i class="mdi mdi-briefcase"></i> Order Number : <span> {{ $order->order_prefix }} </span> </li>
                                        <li> <i class="mdi mdi-cube-send"></i> Courier : <span> {{ $order->courier_name }} </span> </li>
                                        <li style="border: none;"> <i class="mdi mdi-cube-send"></i> Awb No : <span> {{ $order->awb_number }} </span> </li> 
										@if($order->shipping_company_id == 2 && $order->lr_no)
											<li style="border: none;"> <i class="mdi mdi-cube-send"></i> LR No : <span> {{ $order->lr_no }} </span> </li>
										@endif
                                    </ul>
                                </div> 
                            </div>

                            <div class="col-lg-8">
                                <div class="main-head-text custm-pad ">
                                    <h4> Tracking History </h4> 
                                    <div class="main-steper-11"> 
                                        <div class="stepper d-flex flex-column mt-4 ml-2"> 
											 @foreach($trackingHistories as $key => $trackingHistory)
												<div class="d-flex mb-1">
													<div class="d-flex flex-column pr-4 align-items-center">
														<div class="rounded-circle bg-primary text-white mb-1 main-12-10"><img src="{{asset('assets/images/order-1/fast-delivery.png')}}"></div>
														<div class="line h-100"></div>
													</div> 
													@if($shippingCompany->id == 2)
														 <div class="main-flex-raet">
																 <div class="main-cotn-nt">
																<h5 class="text-dark"> Activity : {{ $trackingHistory['status'] }} </h5>
																<p class="lead-custm"> {{ $trackingHistory['location'] }} </p>
																<h6 class="lead-custm"> {{ $trackingHistory['scan_remark'] }} </h6>
															</div>

															<div class="main-cotn-nt">
																<h6 class="lead-custm"> {{ date('M d, Y  h:i a', strtotime($trackingHistory['scan_timestamp'])) }} </h6>
															</div>
														</div>
													@endif  
													
													@if($shippingCompany->id == 3)
														 <div class="main-flex-raet">
																 <div class="main-cotn-nt">
																<h5 class="text-dark"> Activity : {{ $trackingHistory['ScanDetail']['Scan'] }} </h5>
																<p class="lead-custm"> {{ $trackingHistory['ScanDetail']['ScannedLocation'] }} </p>
																<h6 class="lead-custm"> {{ $trackingHistory['ScanDetail']['Instructions'] }} </h6>
															</div>

															<div class="main-cotn-nt">
																<h6 class="lead-custm"> {{ date('M d, Y  h:i a', strtotime($trackingHistory['ScanDetail']['ScanDateTime'])) }} </h6>
															</div>
														</div>
													@endif  
												</div>
											 @endforeach   
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection