@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Dashboard')
@section('header_title','Dashboard')
@section('content')

<div class="content-page">
    <div class="content"> 
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="main-dashbordr-22">
                <div class="row main-mb-2 align-items-center new-re">
                    <div class="col-lg-6 col-md-6">
                        <div class="main-box-10-1">
                            <img class="main-dash-bord" src="{{asset('assets/images/dashbord/order-dash.png')}}">

                            <div class="main-box-cont-dash">
                                <h5> Today's Recharge </h5>
								<h3> ₹{{ $todayRecharge ?? 0 }} </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="main-box-10-1">
                            <img class="main-dash-bord" src="{{asset('assets/images/dashbord/order-dash.png')}}">

                            <div class="main-box-cont-dash">
								<h5> Total Wallet Amount </h5>
                                <h3> ₹{{ $overallWalletAmount ?? 0 }} </h3> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row main-mb-2 align-items-center">
                    <div class="col-lg-4 col-md-3">
						<a href="{{ url('order') }}?weight_order=1&status=All">
							<div class="main-box-10-1">
								<img class="main-dash-bord" src="{{asset('assets/images/dashbord/order-dash.png')}}">  
								<div class="main-box-cont-dash">
									<h5> Total Order </h5>
									<h3> {{ $totalLightWeightShipment }} </h3>
									<h6> Today's ({{ $todaysLightWeightOrder }}) </h6>
								</div>
							</div>
						</a>
                    </div> 
					 
                    <div class="col-lg-4 col-md-3">
                        <div class="main-box-10-1">
                            <img class="main-dash-bord" src="{{asset('assets/images/dashbord/revenue.png')}}"> 
                            <div class="main-box-cont-dash">
                                <h5> Total Invoice Amount </h5>
                                <h3> ₹{{ $totalInvoiceAmount ?? 0 }} </h3> 
								<h6> Today's ({{ $tadaysInvoiceAmount ?? 0 }}) </h6>
                            </div>
                        </div>
                    </div> 
					
                    <div class="col-lg-4 col-md-3">
                        <div class="main-box-10-1">
                            <img class="main-dash-bord" src="{{asset('assets/images/dashbord/revenue.png')}}"> 
                            <div class="main-box-cont-dash">
                                <h5> Total COD Amount </h5>
                                <h3> ₹{{ $totalCodAmount ?? 0 }}</h3> 
								<h6> Today's ({{ $tadaysCodAmount ?? 0 }}) </h6>
                            </div>
                        </div>
                    </div> 
                
                    <!--<div class="col-lg-3 col-md-3">
                        <div class="main-box-10-1">
                            <img class="main-dash-bord" src="{{asset('assets/images/dashbord/average-shipping.png')}}"> 
                            <div class="main-box-cont-dash">
                                <h5> Average Shipping Time</h5>
                                <h3> {{ $averageShippingTime ?? 0 }} </h3> 
                            </div>
                        </div>
                    </div>  -->
                </div>
				<div class="row main-mb-2 align-items-center">
					<div class="col-lg-12 col-md-12">
                        <div class="main-roow-1">
                            <h5 style="text-transform: uppercase;color:black;">Shipments Details </h5>
                            <div class="row "> 
                                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-4"> 
									<a href="{{ url('order') }}?weight_order=1&status=Manifested">
										<div class="main012">
											<h5> {{ $manifested }} </h5>
											<h4> Manifested </h4>
										</div> 
									</a>
                                </div>

                                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-4">
									<a href="{{ url('order') }}?weight_order=1&status=In Transit">
										<div class="main012">
											<h5> {{ $inTransit }} </h5>
											<h4> In-Transit </h4>
										</div>
									</a>
                                </div>
 

                                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-4">
									<a href="{{ url('order') }}?weight_order=1&status=All&order_status=cancelled">
										<div class="main012">
											<h5> {{ $cancelledOrder }} </h5>
											<h4> Cancelled </h4>
										</div>
									</a>
                                </div>
 
                                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-4">
                                    <div class="main012">
                                        <h5> {{ $rto }}  </h5>
                                        <h4> RTO </h4>
                                    </div>
                                </div> 
                                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-4">
                                    <div class="main012">
                                        <h5> {{ $outForDelivery }}  </h5>
                                        <h4> Out For Delivery  </h4>
                                    </div>
                                </div> 
								<div class="col-xl-2 col-lg-4 col-md-4 col-sm-4">
									<a href="{{ url('order') }}?weight_order=1&status=All&order_status=delivered">
										<div class="main012">
											<h5> {{ $delivered }}  </h5>
											<h4> Delivered </h4>
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
@endsection 
@push('js') 
@endpush