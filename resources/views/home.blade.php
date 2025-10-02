@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Dashboard')
@section('header_title','Dashboard')
@section('content')

<style>

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
                                    <div>
                                        <img src="{{asset('assets/images/dashbord/dashboard-images.png')}}" alt="">
                                    </div>
                                    <div>
                                        <h5 class="welcome-title">Welcome <span style="color : #316BFF; margin-left : 5px;">KSHIP!</span></h5>
                                        <p class="welcome-des">Hooray!! You are one step closer to dispatching your shipment.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-12 new-margin-dashes">
                                <div class="help-card">
                                    <img src="{{asset('assets/images/dashbord/help-card-img.png')}}" alt="" style="width: fit-content;">
                                    <h6 class="help-card-title"> Need Help?</h6>
                                    <p class="help-card-des">We are here to solve your doubts. Reach out to us on 1234567890</p>

                                </div>
                            </div>
                        </div>
                        <div class="row main-mb-2  new-re">
                            <div class="col-xl-4 col-md-4 col-sm-12">
                                <div class="row">
                                    <div class="col-12 mt-3">
                                        <div class="main-box-10-1 border-c-1">
                                            <!-- <img class="main-dash-bord" src="{{asset('assets/images/dashbord/order-dash.png')}}"> -->

                                            <div class="main-box-cont-dash">
                                                <h5> Total Wallet Amount </h5>
                                                <h3> ₹{{ $overallWalletAmount ?? 0 }} </h3>
                                            </div>
                                            <img class="chart-1" src="{{asset('assets/images/dashbord/chat-1.png')}}">
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
                                            <img class="chart-2" src="{{asset('assets/images/dashbord/chat-2.png')}}">

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
                                        <img src="{{asset('assets/images/dashbord/chat-3.png')}}" alt="" class="chat-3-img">
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
                                            <!-- <img class="main-dash-bord" src="{{asset('assets/images/dashbord/order-dash.png')}}"> -->

                                            <div class="main-box-cont-dash">
                                                <h5> Today's Recharge </h5>
                                                <h3> ₹{{ $todayRecharge ?? 0 }} </h3>
                                            </div>
                                            <img class="chart-2" src="{{asset('assets/images/dashbord/chat-4.png')}}">

                                        </div>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <div class="main-box-10-1 border-c-5">
                                            <!-- <img class="main-dash-bord" src="{{asset('assets/images/dashbord/revenue.png')}}"> -->
                                            <div class="main-box-cont-dash">
                                                <h5> Total COD Amount </h5>
                                                <h3> ₹{{ $totalCodAmount ?? 0 }}</h3>

                                            </div>
                                            <div class="main-box-cont-dash mt-3">
                                                <h5> Today's COD Amount </h5>
                                                <h3>{{ $tadaysCodAmount ?? 0 }}</h3>
                                            </div>
                                            <img class="chart-2" src="{{asset('assets/images/dashbord/chat-5.png')}}">

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
                                                            <img src="{{asset('assets/images/dashbord/rr-1.png')}}">
                                                            <h5> {{ $manifested }} </h5>
                                                            <div style="display: flex;     justify-content: space-between; flex-wrap : wrap;">
                                                                <h4> Manifested / Pending Pickup </h4>
                                                                <img src="{{asset('assets/images/dashbord/arro.png')}}" style="object-fit: none; width: fit-content;">
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 ">
                                                    <a href="{{ url('order') }}?weight_order=1&status=All&order_status=cancelled">
                                                        <div class="main012">
                                                            <img src="{{asset('assets/images/dashbord/rr-2.png')}}">
                                                            <h5> {{ $cancelledOrder }} </h5>
                                                            <div style="display: flex;     justify-content: space-between; flex-wrap : wrap;">
                                                                <h4> Cancelled </h4>
                                                                <img src="{{asset('assets/images/dashbord/arro.png')}}" style="object-fit: none; width: fit-content;">
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
                                                            <img src="{{asset('assets/images/dashbord/rr-4.png')}}">
                                                            <h5> {{ $inTransit }} </h5>
                                                            <div style="display: flex;     justify-content: space-between; flex-wrap : wrap;">
                                                                <h4> In-Transit </h4>
                                                                <img src="{{asset('assets/images/dashbord/arro.png')}}" style="object-fit: none; width: fit-content;">

                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                                    <div class="main012">
                                                        <img src="{{asset('assets/images/dashbord/rr-5.png')}}">
                                                        <h5> {{ $rto }} </h5>
                                                        <div  style="display: flex;     justify-content: space-between; flex-wrap : wrap;">
                                                            <h4> RTO </h4>
                                                                <img src="{{asset('assets/images/dashbord/arro.png')}}" style="object-fit: none; width: fit-content;">
                                                        </div>
                                                    </div>
                                                </div>
                                               
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                 <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 border-1-bottom">
                                                    <div class="main012">
                                                        <img src="{{asset('assets/images/dashbord/rr-3.png')}}">
                                                        <h5> {{ $outForDelivery }} </h5>
                                                           <div style="display: flex;     justify-content: space-between; flex-wrap : wrap;">
                                                               <h4> Out For Delivery </h4>
                                                                <img src="{{asset('assets/images/dashbord/arro.png')}}" style="object-fit: none; width: fit-content;">
                                                           </div>
                                                    </div>
                                                </div>
                                                 <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                                    <a href="{{ url('order') }}?weight_order=1&status=All&order_status=delivered">
                                                        <div class="main012">
                                                            <img src="{{asset('assets/images/dashbord/rr-6.png')}}">
                                                            <h5> {{ $delivered }} </h5>
                                                            <div style="display: flex;     justify-content: space-between; flex-wrap : wrap;">
                                                                <h4> Delivered </h4>
                                                                <img src="{{asset('assets/images/dashbord/arro.png')}}" style="object-fit: none; width: fit-content;">

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

    
</div>



<!-- <div class="content-page">
    <div class="content"> 
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
</div>   -->
@endsection
@push('js')
@endpush