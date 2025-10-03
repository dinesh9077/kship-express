@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Dashboard')
@section('header_title','Dashboard')
@section('content')

<style>
.new-tbs-white td{
    background-color: white !important;
    border-radius: 10px;
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
                                                            <div style="display: flex;     justify-content: space-between;">
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
                                                            <div style="display: flex;     justify-content: space-between;">
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
                                                            <div style="display: flex;     justify-content: space-between;">
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
                                                        <div  style="display: flex;     justify-content: space-between;">
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
                                                           <div style="display: flex;     justify-content: space-between;">
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
                                                            <div style="display: flex;     justify-content: space-between;">
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

    <div style="margin: 10px;">


    <div style="display: flex; justify-content : space-between; align-items: center; padding : 2px 20px; background : #5640B0; border-radius: 10px;">
        <h6 style="font-size: 18px; color : white; font-weight : 500; ">Recent Orders</h6>
        <a href="#" style="font-size: 16px; color : white; font-weight : 300;text-decoration: underline !important; ">View Details</a>
    </div>

    <table id="neworder_datatable" style="width:100%" class="dataTable no-footer"
							aria-describedby="neworder_datatable_info">
							<thead>
								<tr>
									<th class="sorting_disabled sorting_desc" rowspan="1" colspan="1"
										aria-label="Sr.No">Sr.No</th>
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
										colspan="1" aria-label="Status: activate to sort column ascending">Status</th>
									<th class="sorting" tabindex="0" aria-controls="neworder_datatable" rowspan="1"
										colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
								</tr>
							</thead>
							<tbody>
								<tr class="odd new-tbs-white">
									<td class="sorting_1">1</td>
									<td>#53<br>
										<p>Kship Express</p>
									</td>
									<td>
										<div class="main-cont1-2">
											<p>Softieons (Softieons)</p>
											<p>Amit sharma</p>
											<p>8754875487</p>
											<p></p>
										</div>
									</td>
									
									

                                    	<td>
										<div class="main-cont1-2">
											<p>Demo Demo</p>
											<p>9016105349</p>
											<div class="tooltip">View Address<span class="tooltiptext"><b>Demo
														Demo</b><br><b>Address</b>: Surat</span></div>
										</div>
									</td>

                                    <td>
										<div class="main-cont1-2">
											<p class="cod">cod</p>
											<p>COD Amount: 12.00</p>
										</div>
									</td>



									<td>
										<div class="main-cont1-1">
											<div class="checkbox checkbox-purple">
												Order Prefix/LR No: <a
													href="http://127.0.0.1:8000/order/details/53?weight_order=1&amp;status=New">#146</a>
											</div>
											<div class="checkbox checkbox-purple">
												Courier:
											</div><span style="padding-left:0">
												<a href="javascript:;" class="show-details-btn"
													data-order="[{&quot;product_category&quot;:&quot;12&quot;,&quot;product_name&quot;:&quot;12&quot;,&quot;sku_number&quot;:&quot;12&quot;,&quot;hsn_number&quot;:&quot;1&quot;,&quot;amount&quot;:&quot;12.00&quot;,&quot;quantity&quot;:12}]"
													style=" color: #1A4BEC ;     font-size: 15px;">
													View Products
												</a>
											</span>
										</div>
									</td>
								
									<td>
										<p class="prepaid">New</p>
										<p style="padding-left:0">2025 Sep 30 | 02:20 PM</p>
									</td>
									<td>
										<div class="main-btn-1"><a href="javascript:;">
												<button type="button" class="customization_popup_trigger btn-light-1"
													data-weight-order="1" onclick="shipNow(this, event)" data-id="53">
													Ship Now
												</button>
											</a>
											<div class="mian-btn">
												<div class="btn-group">
													<button class="dropbtn" type="button" data-toggle="dropdown"
														aria-haspopup="true" aria-expanded="false">
														<i class="fas fa-ellipsis-v"></i>
													</button>
													<div class="dropdown-menu"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/details/53?weight_order=1&amp;status=New">View
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/clone/53?weight_order=1">Clone
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/edit/53?weight_order=1">Edit
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/cancel/53?weight_order=1"
															style="color: red;" onclick="cancelNewOrder(this, event)">
															Cancel Order
														</a>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr class="even new-tbs-white">
									<td class="sorting_1">2</td>
									<td>#52<br>
										<p>Dinesh Patil</p>
									</td>
                                    	<td>
										<div class="main-cont1-2">
											<p>Softieons (Softieons)</p>
											<p>Amit sharma</p>
											<p>8754875487</p>
											<p></p>
										</div>
									</td>
									
									
                                    
								
									<td>
										<div class="main-cont1-1">
											<div class="checkbox checkbox-purple">
												Order Prefix/LR No: <a
													href="http://127.0.0.1:8000/order/details/52?weight_order=1&amp;status=New">#7192</a>
											</div>
											<div class="checkbox checkbox-purple">
												Courier:
											</div><span style="padding-left:0">
												<a href="javascript:;" class="show-details-btn"
													data-order="[{&quot;product_category&quot;:&quot;No&quot;,&quot;product_name&quot;:&quot;No&quot;,&quot;sku_number&quot;:&quot;550080&quot;,&quot;hsn_number&quot;:&quot;01058818&quot;,&quot;amount&quot;:&quot;2500.00&quot;,&quot;quantity&quot;:5}]"
													style=" color: #1A4BEC ;     font-size: 15px;">
													View Products
												</a>
											</span>
										</div>
									</td>

                                    
                                    <td>
										<div class="main-cont1-2">
											<p class="cod">cod</p>
											<p>COD Amount: 12.00</p>
										</div>
									</td>

                                    

                                    	<td>
										<div class="main-cont1-2">
											<p>Demo Demo</p>
											<p>9016105349</p>
											<div class="tooltip">View Address<span class="tooltiptext"><b>Demo
														Demo</b><br><b>Address</b>: Surat</span></div>
										</div>
									</td>
									
									<td>
										<p class="prepaid">New</p>
										<p style="padding-left:0">2025 Sep 28 | 12:12 PM</p>
									</td>
									<td>
										<div class="main-btn-1"><a href="javascript:;">
												<button type="button" class="customization_popup_trigger btn-light-1"
													data-weight-order="1" onclick="shipNow(this, event)" data-id="52">
													Ship Now
												</button>
											</a>
											<div class="mian-btn">
												<div class="btn-group">
													<button class="dropbtn" type="button" data-toggle="dropdown"
														aria-haspopup="true" aria-expanded="false">
														<i class="fas fa-ellipsis-v"></i>
													</button>
													<div class="dropdown-menu"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/details/52?weight_order=1&amp;status=New">View
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/clone/52?weight_order=1">Clone
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/edit/52?weight_order=1">Edit
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/cancel/52?weight_order=1"
															style="color: red;" onclick="cancelNewOrder(this, event)">
															Cancel Order
														</a>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr class="odd new-tbs-white">
									<td class="sorting_1">3</td>
									<td>#29<br>
										<p>Kship Express</p>
									</td>
									<td>
										<div class="main-cont1-2">
											<p>Softieons (Softieons)</p>
											<p>Amit sharma</p>
											<p>8754875487</p>
											<p></p>
										</div>
									</td>
									<td>
										<div class="main-cont1-2">
											<p>John Doe</p>
											<p>9876543210</p>
											<div class="tooltip">View Address<span class="tooltiptext"><b>John
														Doe</b><br><b>Address</b>: 221B Baker Street</span></div>
										</div>
									</td>
									<td>
										<div class="main-cont1-2">
											<p class="cod">cod</p>
											<p>COD Amount: 45451.00</p>
										</div>
									</td>
									<td>
										<div class="main-cont1-1">
											<div class="checkbox checkbox-purple">
												Order Prefix/LR No: <a
													href="http://127.0.0.1:8000/order/details/29?weight_order=1&amp;status=New">#121</a>
											</div>
											<div class="checkbox checkbox-purple">
												Courier:
											</div><span style="padding-left:0">
												<a href="javascript:;" class="show-details-btn"
													data-order="[{&quot;product_category&quot;:&quot;asd&quot;,&quot;product_name&quot;:&quot;saree&quot;,&quot;sku_number&quot;:&quot;5465&quot;,&quot;hsn_number&quot;:&quot;54564&quot;,&quot;amount&quot;:&quot;451.00&quot;,&quot;quantity&quot;:1},{&quot;product_category&quot;:&quot;shoe&quot;,&quot;product_name&quot;:&quot;addidas sports&quot;,&quot;sku_number&quot;:&quot;7487&quot;,&quot;hsn_number&quot;:&quot;54564&quot;,&quot;amount&quot;:&quot;200.00&quot;,&quot;quantity&quot;:1}]"
													style=" color: #1A4BEC ;     font-size: 15px;">
													View Products
												</a>
											</span>
										</div>
									</td>
								
									<td>
										<p class="prepaid">New</p>
										<p style="padding-left:0">2025 Sep 25 | 03:03 PM</p>
									</td>
									<td>
										<div class="main-btn-1"><a href="javascript:;">
												<button type="button" class="customization_popup_trigger btn-light-1"
													data-weight-order="1" onclick="shipNow(this, event)" data-id="29">
													Ship Now
												</button>
											</a>
											<div class="mian-btn">
												<div class="btn-group">
													<button class="dropbtn" type="button" data-toggle="dropdown"
														aria-haspopup="true" aria-expanded="false">
														<i class="fas fa-ellipsis-v"></i>
													</button>
													<div class="dropdown-menu"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/details/29?weight_order=1&amp;status=New">View
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/clone/29?weight_order=1">Clone
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/edit/29?weight_order=1">Edit
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/cancel/29?weight_order=1"
															style="color: red;" onclick="cancelNewOrder(this, event)">
															Cancel Order
														</a>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr class="even new-tbs-white">
									<td class="sorting_1">4</td>
									<td>#28<br>
										<p>Kship Express</p>
									</td>
									<td>
										<div class="main-cont1-2">
											<p>Softieons (Softieons)</p>
											<p>Amit sharma</p>
											<p>8754875487</p>
											<p></p>
										</div>
									</td>
									<td>
										<div class="main-cont1-2">
											<p>John Doe</p>
											<p>9876543210</p>
											<div class="tooltip">View Address<span class="tooltiptext"><b>John
														Doe</b><br><b>Address</b>: 221B Baker Street</span></div>
										</div>
									</td>
									<td>
										<div class="main-cont1-2">
											<p class="cod">cod</p>
											<p>COD Amount: 45451.00</p>
										</div>
									</td>
									<td>
										<div class="main-cont1-1">
											<div class="checkbox checkbox-purple">
												Order Prefix/LR No: <a
													href="http://127.0.0.1:8000/order/details/28?weight_order=1&amp;status=New">#120</a>
											</div>
											<div class="checkbox checkbox-purple">
												Courier:
											</div><span style="padding-left:0">
												<a href="javascript:;" class="show-details-btn"
													data-order="[{&quot;product_category&quot;:&quot;asd&quot;,&quot;product_name&quot;:&quot;saree&quot;,&quot;sku_number&quot;:&quot;5465&quot;,&quot;hsn_number&quot;:&quot;54564&quot;,&quot;amount&quot;:&quot;451.00&quot;,&quot;quantity&quot;:1},{&quot;product_category&quot;:&quot;shoe&quot;,&quot;product_name&quot;:&quot;addidas sports&quot;,&quot;sku_number&quot;:&quot;7487&quot;,&quot;hsn_number&quot;:&quot;54564&quot;,&quot;amount&quot;:&quot;200.00&quot;,&quot;quantity&quot;:1}]"
													style=" color: #1A4BEC ;     font-size: 15px;">
													View Products
												</a>
											</span>
										</div>
									</td>
								
									<td>
										<p class="prepaid">New</p>
										<p style="padding-left:0">2025 Sep 25 | 03:03 PM</p>
									</td>
									<td>
										<div class="main-btn-1"><a href="javascript:;">
												<button type="button" class="customization_popup_trigger btn-light-1"
													data-weight-order="1" onclick="shipNow(this, event)" data-id="28">
													Ship Now
												</button>
											</a>
											<div class="mian-btn">
												<div class="btn-group">
													<button class="dropbtn" type="button" data-toggle="dropdown"
														aria-haspopup="true" aria-expanded="false">
														<i class="fas fa-ellipsis-v"></i>
													</button>
													<div class="dropdown-menu"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/details/28?weight_order=1&amp;status=New">View
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/clone/28?weight_order=1">Clone
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/edit/28?weight_order=1">Edit
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/cancel/28?weight_order=1"
															style="color: red;" onclick="cancelNewOrder(this, event)">
															Cancel Order
														</a>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr class="odd new-tbs-white">
									<td class="sorting_1">5</td>
									<td>#25<br>
										<p>Kship Express</p>
									</td>
									<td>
										<div class="main-cont1-2">
											<p>Softieons (Softieons)</p>
											<p>Amit sharma</p>
											<p>8754875487</p>
											<p></p>
										</div>
									</td>
									<td>
										<div class="main-cont1-2">
											<p>John Doe</p>
											<p>9876543210</p>
											<div class="tooltip">View Address<span class="tooltiptext"><b>John
														Doe</b><br><b>Address</b>: 221B Baker Street</span></div>
										</div>
									</td>
									<td>
										<div class="main-cont1-2">
											<p class="cod">cod</p>
											<p>COD Amount: 45451.00</p>
										</div>
									</td>
									<td>
										<div class="main-cont1-1">
											<div class="checkbox checkbox-purple">
												Order Prefix/LR No: <a
													href="http://127.0.0.1:8000/order/details/25?weight_order=1&amp;status=New">#220</a>
											</div>
											<div class="checkbox checkbox-purple">
												Courier:
											</div><span style="padding-left:0">
												<a href="javascript:;" class="show-details-btn"
													data-order="[{&quot;product_category&quot;:&quot;asd&quot;,&quot;product_name&quot;:&quot;saree&quot;,&quot;sku_number&quot;:&quot;5465&quot;,&quot;hsn_number&quot;:&quot;54564&quot;,&quot;amount&quot;:&quot;451.00&quot;,&quot;quantity&quot;:1},{&quot;product_category&quot;:&quot;shoe&quot;,&quot;product_name&quot;:&quot;addidas sports&quot;,&quot;sku_number&quot;:&quot;7487&quot;,&quot;hsn_number&quot;:&quot;54564&quot;,&quot;amount&quot;:&quot;200.00&quot;,&quot;quantity&quot;:1}]"
													style=" color: #1A4BEC ;     font-size: 15px;">
													View Products
												</a>
											</span>
										</div>
									</td>
								
									<td>
										<p class="prepaid">New</p>
										<p style="padding-left:0">2025 Sep 25 | 02:32 PM</p>
									</td>
									<td>
										<div class="main-btn-1"><a href="javascript:;">
												<button type="button" class="customization_popup_trigger btn-light-1"
													data-weight-order="1" onclick="shipNow(this, event)" data-id="25">
													Ship Now
												</button>
											</a>
											<div class="mian-btn">
												<div class="btn-group">
													<button class="dropbtn" type="button" data-toggle="dropdown"
														aria-haspopup="true" aria-expanded="false">
														<i class="fas fa-ellipsis-v"></i>
													</button>
													<div class="dropdown-menu"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/details/25?weight_order=1&amp;status=New">View
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/clone/25?weight_order=1">Clone
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/edit/25?weight_order=1">Edit
															Order</a>
														<hr class="m-0"><a class="dropdown-item"
															href="http://127.0.0.1:8000/order/cancel/25?weight_order=1"
															style="color: red;" onclick="cancelNewOrder(this, event)">
															Cancel Order
														</a>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
							</tbody>
		</table>

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