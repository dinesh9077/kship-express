 <!DOCTYPE html>
<html lang="en"> 
	<head>
		<meta charset="utf-8" />
		<title>Milega Yaha</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
		<meta content="Coderthemes" name="author" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<!-- App favicon -->
		<link rel="shortcut icon" href="{{asset('storage/settings/'.config('setting.fevicon_icon'))}}">
		
		<!-- App css -->
		<link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" /> 
		
		<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <link href="{{asset('assets/libs/custombox/custombox.min.css')}}" rel="stylesheet" type="text/css" >
        <link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/libs/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">
		
		<link href="{{asset('assets/css/waitMe.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('assets/libs/jquery-toast/jquery.toast.min.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
		<style>
			.select2-container .select2-selection--single {
			height:100% !important;
			}
			.select2-container--default .select2-selection--single .select2-selection__arrow
			{ 
			height:42px !important;
			}
			.select2-container--default .select2-selection--single .select2-selection__rendered 
			{ 
			line-height: 42px !important;
			}
			p.cod {
			text-transform: capitalize;
			color: #8e0000;
			background: #8e000028;
			padding: 2px 7px;
			font-size: 12px;
			width: fit-content;
			border-radius: 3px;
			}
			
			button.re-btn.active {
				color: #fff;
				background: #5c57ffad;
			}
			
			.notification-list .notify-item .notify-details {
				margin-bottom: 5px;
				overflow: hidden;
				margin-left: 45px; 
				text-wrap: wrap;
				color: #414d5f;
				font-weight: 500;
			}
			
			.slimscroll.noti-scroll.notifymsg {
				height: auto !important;
			}
			
			.content-page {
    margin-left: 20px;
    overflow: hidden;
    padding: 0 15px 5px 15px;
    min-height: 80vh;
    margin-top: 20px;
}
		</style>
	</head> 
	<body> 
		<!-- Begin page -->
		<div id="wrapper"> 
<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
            <section class="main-order-sec">
                <div class="custom-container">
                    <div class="main-cls-1">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="main-head-text">
                                    <h4> Order Information </h4>

                                    <ul class="list-ordr-detal">
                                        <li> <i class="mdi mdi-briefcase"></i> Order Number : <span> {{$order->order_prefix}} </span> </li>
                                        <li> <i class="mdi mdi-cube-send"></i> Courier : <span> {{$order->courier_name}} </span> </li>
                                        <li style="border: none;"> <i class="mdi mdi-cube-send"></i> Tracking No : <span> {{$order->awb_number}} </span> </li>
                                    </ul>
                                </div>

                              <!--  <div class="main-head-text custm-pad">
                                    <h5> Shipping Solution by <img src="assets/images/logo1/logo-full-black.png" style="width: auto; height: 25px; padding-left: 5px;"> </h5>
                                </div>-->
                            </div>

                            <div class="col-lg-8">
                                <div class="main-head-text custm-pad ">
                                    <h4> Tracking History </h4>

                                    <div class="main-steper-11"> 
                                        <div class="stepper d-flex flex-column mt-4 ml-2"> 
										 @foreach($trackingHistories as $key => $trackinghist)
                                            <div class="d-flex mb-1">
                                                <div class="d-flex flex-column pr-4 align-items-center">
                                                    <div class="rounded-circle bg-primary text-white mb-1 main-12-10"><img src="{{asset('assets/images/order-1/fast-delivery.png')}}"></div>
                                                    <div class="line h-100"></div>
                                                </div>
												@if($shippingcomp->id == 1)
													<div class="main-flex-raet">
														<div class="main-cotn-nt">
															<h5 class="text-dark"> Activity : {{$trackinghist['message']}} </h5>
															<p class="lead-custm"> {{$trackinghist['location']}} </p>
															<h6 class="lead-custm"> {{$trackinghist['message']}} </h6>
														</div>

														<div class="main-cotn-nt">
															<h6 class="lead-custm"> {{date('M d, Y  h:i:a',strtotime($trackinghist['event_time']))}} </h6>
														</div>
													</div>
												@endif
												@if($shippingcomp->id == 2)
													 <div class="main-flex-raet">
															 <div class="main-cotn-nt">
															<h5 class="text-dark"> Activity : {{$trackinghist['ScanDetail']['Scan']}} </h5>
															<p class="lead-custm"> {{$trackinghist['ScanDetail']['ScannedLocation']}} </p>
															<h6 class="lead-custm"> {{$trackinghist['ScanDetail']['Instructions']}} </h6>
														</div>

														<div class="main-cotn-nt">
															<h6 class="lead-custm"> {{date('M d, Y  h:i:a',strtotime($trackinghist['ScanDetail']['ScanDateTime']))}} </h6>
														</div>
													</div>
												@endif
												@if($shippingcomp->id == 3)
													 <div class="main-flex-raet">
															 <div class="main-cotn-nt">
															<h5 class="text-dark"> Activity : {{$trackinghist['status_detail']}} </h5>
															<p class="lead-custm"> {{$trackinghist['location']}} </p>
															<h6 class="lead-custm"> {{$trackinghist['status_detail']}} </h6>
														</div>

														<div class="main-cotn-nt">
															<h6 class="lead-custm"> {{date('M d, Y  h:i:a',strtotime($trackinghist['time']))}} </h6>
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

	<!-- Footer Start -->
			<footer class="footer">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							{{date('Y')}} &copy; Developed By <a href="https://softieons.com/">Softieons</a>
						</div>
						
					</div>
				</div>
			</footer>  
		</div> 
		
	</div> 
	
	<!-- Right bar overlay-->
	<div class="rightbar-overlay"></div>
	
	<!-- Vendor js -->
	<script src="{{asset('assets/js/vendor.min.js')}}"></script>
	
	<script src="{{asset('assets/libs/chart-js/Chart.bundle.min.js')}}"></script> 
	<script src="{{asset('assets/libs/moment/moment.min.js')}}"></script>
	<script src="{{asset('assets/libs/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
	<script src="{{asset('assets/js/app.min.js')}}"></script>
	<script src="{{asset('assets/libs/select2/select2.min.js')}}"></script>
	<script src="{{asset('assets/libs/jquery-toast/jquery.toast.min.js')}}"></script>
	<script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
	<script src="{{asset('assets/js/waitMe.js')}}"></script> 
	<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
	 
</body> 
</html>				