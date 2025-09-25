<!DOCTYPE html>
<html lang="en"> 
	<head>
		<meta charset="utf-8" />
		<title>@yield('title')</title>
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
		</style>
		<script> 
            let modalOpen = false;
            function closemodal()
            {
                setTimeout(function()
                {
                    modalOpen = false;
				},1000)
			}
		</script>
	</head> 
	<body> 
		<!-- Begin page -->
		<div id="wrapper"> 
			@include('layouts.backend.partial.header') 
			@include('layouts.backend.partial.leftside-bar') 
			<div class="modal fade" id="rechargeWalletModal" tabindex="-1" role="dialog" aria-labelledby="rechargeWalletLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg model-width-1">
					<div class="modal-content">
						
						{{-- Header --}}
						<div class="modal-header head-00re pb-0 border-0">
							<h5 class="modal-title" id="rechargeWalletLabel">Recharge Your Wallet</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>

						{{-- Form --}}
						<form method="POST" action="{{ route('recharge.wallet.amount') }}" enctype="multipart/form-data" id="paymentForm">
							@csrf

							<div class="modal-body pt-0">
								
								{{-- Current Wallet --}}
								<div class="main-01">
									<h5>
										Current Wallet Amount: 
										<span>{{ config('setting.currency') }}{{ Helper::decimal_number(Auth::user()->wallet_amount) }}</span>
									</h5>
								</div>

								<div class="man-01rech">
									{{-- Transaction Type --}}
									<h5>Transaction Type</h5>
									<div class="form-group rech-re-form">
										<select name="transaction_type" id="transaction_type" class="form-control" required>
											<option value="Online">Online</option>
										</select>
									</div>

									{{-- Amount Input --}}
									<h5>Enter Amount</h5>
									<div class="form-group rech-re-form position-relative">
										<span class="position-absolute custom-rupee-position">{{ config('setting.currency') }}</span>
										<input type="number" name="amount" id="recharge_amount" value="200" min="200" placeholder="200" class="form-control" required>
										<small class="form-text text-muted">Min value: {{ config('setting.currency') }}200</small>
									</div>

									{{-- Quick Amount Buttons --}}
									<h6>Or Select From Below</h6>
									<div class="main-21-33">
										@foreach([200, 500, 1000, 2500, 5000, 10000] as $preset)
											<button type="button" 
													class="re-btn {{ $preset == 200 ? 'active' : '' }}" 
													onclick="setRechargeAmount(this, {{ $preset }})">
												{{ config('setting.currency') }}{{ $preset }}
											</button>
										@endforeach
									</div>

									{{-- Offline Params --}}
									<div class="offline_param d-none">
										<h5>Payment Receipt</h5>
										<div class="form-group rech-re-form">
											<input type="file" name="payment_receipt[]" id="payment_receipt" multiple class="form-control-file">
										</div>

										<h5>Note</h5>
										<div class="form-group rech-re-form">
											<textarea name="note" id="note" class="form-control"></textarea>
										</div>
									</div>
								</div>

								<input type="hidden" id="user_id" value="{{ Auth::id() }}">

								{{-- Amount Summary --}}
								<div class="class-main-count">
									<div class="main-justify-space">
										<h5>Recharge Amount</h5>
										<h5>{{ config('setting.currency') }}<span class="payableamount">200</span></h5>
									</div>

									<div class="main-justify-space">
										<h5>Payable Amount</h5>
										<h5>{{ config('setting.currency') }}<span class="payableamount">200</span></h5>
									</div>
								</div>

							</div>

							{{-- Footer --}}
							<div class="modal-footer justify-content-center">
								<button type="submit" class="btn btn-primary btn-main-1" id="payButton">
									Continue to Payment
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
 
			
			@yield('content') 
			
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
			<!-- end Footer -->
			
		</div>
		
		<!-- ============================================================== -->
		<!-- End Page content -->
		<!-- ============================================================== -->
		
	</div>
	<!-- END wrapper -->
	
	<!-- Right Sidebar -->
	<div class="right-bar">
		<div class="rightbar-title">
			<a href="javascript:void(0);" class="right-bar-toggle float-right">
				<i class="mdi mdi-close"></i>
			</a>
			<h5 class="m-0 text-white">Settings</h5>
		</div>
		<div class="slimscroll-menu">
			<hr class="mt-0">
			<h5 class="pl-3">Basic Settings</h5>
			<hr class="mb-0" />
			 
			<div class="p-3">
				<div class="custom-control custom-checkbox mb-2">
					<input type="checkbox" class="custom-control-input" id="customCheck1" checked>
					<label class="custom-control-label" for="customCheck1">Notifications</label>
				</div>
				<div class="custom-control custom-checkbox mb-2">
					<input type="checkbox" class="custom-control-input" id="customCheck2" checked>
					<label class="custom-control-label" for="customCheck2">API Access</label>
				</div>
				<div class="custom-control custom-checkbox mb-2">
					<input type="checkbox" class="custom-control-input" id="customCheck3">
					<label class="custom-control-label" for="customCheck3">Auto Updates</label>
				</div>
				<div class="custom-control custom-checkbox mb-2">
					<input type="checkbox" class="custom-control-input" id="customCheck4" checked>
					<label class="custom-control-label" for="customCheck4">Online Status</label>
				</div>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="customCheck5">
					<label class="custom-control-label" for="customCheck5">Auto Payout</label>
				</div>
			</div>
			
			<!-- Messages -->
			<hr class="mt-0" />
			<h5 class="pl-3 pr-3">Messages <span class="float-right badge badge-pill badge-danger">24</span></h5>
			<hr class="mb-0" />
			<div class="p-3">
				<div class="inbox-widget">
					<div class="inbox-item">
						<div class="inbox-item-img"><img src="{{asset('assets/images/users/avatar-1.jpg')}}" class="rounded-circle" alt=""></div>
						<p class="inbox-item-author"><a href="javascript: void(0);">Chadengle</a></p>
						<p class="inbox-item-text">Hey! there I'm available...</p>
						<p class="inbox-item-date">13:40 PM</p>
					</div>
					<div class="inbox-item">
						<div class="inbox-item-img"><img src="{{asset('assets/images/users/avatar-2.jpg')}}" class="rounded-circle" alt=""></div>
						<p class="inbox-item-author"><a href="javascript: void(0);">Tomaslau</a></p>
						<p class="inbox-item-text">I've finished it! See you so...</p>
						<p class="inbox-item-date">13:34 PM</p>
					</div>
					<div class="inbox-item">
						<div class="inbox-item-img"><img src="{{asset('assets/images/users/avatar-3.jpg')}}" class="rounded-circle" alt=""></div>
						<p class="inbox-item-author"><a href="javascript: void(0);">Stillnotdavid</a></p>
						<p class="inbox-item-text">This theme is awesome!</p>
						<p class="inbox-item-date">13:17 PM</p>
					</div>
					
					<div class="inbox-item">
						<div class="inbox-item-img"><img src="{{asset('assets/images/users/avatar-4.jpg')}}" class="rounded-circle" alt=""></div>
						<p class="inbox-item-author"><a href="javascript: void(0);">Kurafire</a></p>
						<p class="inbox-item-text">Nice to meet you</p>
						<p class="inbox-item-date">12:20 PM</p>
						
					</div>
					<div class="inbox-item">
						<div class="inbox-item-img"><img src="{{asset('assets/images/users/avatar-5.jpg')}}" class="rounded-circle" alt=""></div>
						<p class="inbox-item-author"><a href="javascript: void(0);">Shahedk</a></p>
						<p class="inbox-item-text">Hey! there I'm available...</p>
						<p class="inbox-item-date">10:15 AM</p>
						
					</div>
				</div> <!-- end inbox-widget -->
			</div> <!-- end .p-3-->
			
		</div> <!-- end slimscroll-menu-->
	</div> 
	<div class="rightbar-overlay"></div>
	<div id="modal-view-render"></div>
 
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
	<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
	<script>
		$(document).ready(function()
		{
			$('.select2').each(function () {
				$(this).select2({
					dropdownParent: $(this).parent(),
					width:"100%" 
				});
			});
			
			$(".datepicker").datepicker({
				changeMonth: true,
				changeYear: true,
				showAnim: 'clip',
				minDate: "-70Y", 
				maxDate: "+15Y",
				yearRange: "1900:3010", 
				dateFormat: 'yy-mm-dd'
			}); 
			
			$('ul li a').click(function() {
				$('li a').removeClass("active");
				$(this).addClass("active");
			});
			
 			//setInterval(checkPopup, 1800000);
 			//setInterval(lowbalancePopup, 60000);
			//setInterval(kycpendingPopup, 100000);
		});
		
		@if(Session::has('success'))  
		$.toast({
			heading: "Success!",
			text: "{{ session('success') }}",
			position: "top-right",
			loaderBg: "#5ba035",
			icon: "success",
			hideAfter: 3e3,
			stack: 1
		})
		@endif
		
		@if(Session::has('error'))  
		$.toast({
			heading: "Error!",
			text: "{{ session('error') }}",
			position: "top-right",
			loaderBg: "#bf441d",
			icon: "error",
			hideAfter: 3e3,
			stack: 1
		})
		@endif
		
		@if(Session::has('info'))  
		$.toast({ 
			heading: "Info!",
			text: "{{ session('info') }}",
			position: "top-right",
			loaderBg: "#3b98b5",
			icon: "info",
			hideAfter: 3e3,
			stack: 1
		})
		@endif
		
		@if(Session::has('warning'))  
		$.toast({ 
			heading: "Warning!",
			text: "{{ session('warning') }}",
			position: "top-right",
			loaderBg: "#da8609",
			icon: "warning",
			hideAfter: 3e3,
			stack: 1
		})
		@endif 
		
		function toastrMsg(type, msg) {
			if (type == "success") {
				$.toast({
					heading: "Success!",
					text: msg,
					position: "top-right",
					loaderBg: "#5ba035",
					icon: "success",
					hideAfter: 3e3,
					stack: 1
				})
			}
			if (type == "error") {
				$.toast({
					heading: "Error!",
					text: msg,
					position: "top-right",
					loaderBg: "#bf441d",
					icon: "error",
					hideAfter: 3e3,
					stack: 1
				})
			}
			if (type == "info") {
				$.toast({
					heading: "Info!",
					text: msg,
					position: "top-right",
					loaderBg: "#3b98b5",
					icon: "info",
					hideAfter: 3e3,
					stack: 1
				})
			}
			if (type == "warning") {
				$.toast({
					heading: "Warning!",
					text: msg,
					position: "top-right",
					loaderBg: "#da8609",
					icon: "warning",
					hideAfter: 3e3,
					stack: 1
				})
			}
		}
		
		function run_waitMe(el, num, effect){
			text = 'Please wait...';
			fontSize = '';
			switch (num) {
				case 1:
				maxSize = '';
				textPos = 'vertical';
				break;
				case 2:
				text = '';
				maxSize = 30;
				textPos = 'vertical';
				break;
				case 3:
				maxSize = 30;
				textPos = 'horizontal';
				fontSize = '18px';
				break;
			}
			el.waitMe({
				effect: effect,
				text: text,
				bg: 'rgba(255,255,255,0.7)',
				color: '#000',
				maxSize: maxSize,
				waitTime: -1,
				source: 'img.svg',
				textPos: textPos,
				fontSize: fontSize,
				onClose: function(el) {}
			});
		}
		
		function deleteRecord(obj,event)
		{
			event.preventDefault();
			Swal.fire({
				title:"Are you sure you want to delete?",
				text:"You won't be able to revert this!",
				type:"warning",
				showCancelButton:!0,
				confirmButtonColor:"#31ce77",
				cancelButtonColor:"#f34943",
				confirmButtonText:"Yes, delete it!"
				}).then(function (t) {
				if(t.value)
				{
					location.href = obj;
				}
			})
		}
		
		$(document).ready(function() {
			$("ul#tabs li").click(function(e) {
				if (!$(this).hasClass("active")) {
					var tabNum = $(this).index();
					var nthChild = tabNum + 1;
					$("ul#tabs li.active").removeClass("active");
					$(this).addClass("active");
					$("ul#tab li.active").removeClass("active");
					$("ul#tab li:nth-child(" + nthChild + ")").addClass("active");
				}
			});
		});
		
		// Accordion Action
		const accordionItem = document.querySelectorAll(".accordion-item");
		
		accordionItem.forEach((el) =>
		el.addEventListener("click", () => {
			if (el.classList.contains("active")) {
				el.classList.remove("active");
				} else {
				accordionItem.forEach((el2) => el2.classList.remove("active"));
				el.classList.add("active");
			}
		})
		);
		
		function openTab(evt, tabName) {
			var i, tabcontent, tablinks;
			tabcontent = document.getElementsByClassName("tabcontent");
			for (i = 0; i < tabcontent.length; i++) {
				tabcontent[i].style.display = "none";
			}
			tablinks = document.getElementsByClassName("tablinks");
			for (i = 0; i < tablinks.length; i++) {
				tablinks[i].className = tablinks[i].className.replace(" active", "");
			}
			document.getElementById(tabName).style.display = "block";
			evt.currentTarget.className += " active";
		}
		
		function setRechargeAmount(obj,amount)
		{
			$('button.re-btn').removeClass('active');
			$('#recharge_amount').val(amount); 
			$('.payableamount').text(amount);
			$(obj).addClass('active');
		}
		
		$('#recharge_amount').on('keyup',function(){
		    var recharge_amt = $(this).val();
		    setRechargeAmount(this,recharge_amt);
		})
		 
		window.onload = function() {
			var d = new Date().getTime();
			document.getElementById("tid").value = d;
		};
		 
		notifyMsg();
		function notifyMsg()
		{
			$.get("{{ route('notification') }}", function(res){
				$('.notifycount').html(res.count)
				$('.notifymsg').html(res.view)
			},'Json');
		}
		
		autoAccepted();
		function autoAccepted()
		{
			$.get("{{route('weight.auto-accepted')}}", function(res){ 
			},'Json');
		}
		
		function checkPopup() {
			$.get("{{ route('check-amount') }}", function(res) {
				if (res.showPopup) {
					var msg = res.user.join(" and ") + " have Low Wallet Balance."; // Concatenating users' names
					$.toast({
						heading: "Wallet!",
						text: msg,
						loaderBg: "#bf441d",
						icon: "error",
						hideAfter: 3000, // Changed the time format to milliseconds
						stack: 1
					});
				}
			}, 'json');
		}
		
		function lowbalancePopup() {
			$.get("{{ route('lowbalance') }}", function(res) {
			    console.log(res);
				if (res.showPopup) {
					var msg = res.msg; // Concatenating users' names
					$.toast({
						heading: "Wallet!",
						text: msg,
						loaderBg: "#bf441d",
						icon: "error",
						hideAfter: 3000, // Changed the time format to milliseconds
						stack: 1
					});
				}
			}, 'json');
		}

		function kycpendingPopup() {
			$.get("{{ route('kycpending') }}", function(res) {
				if (res.showPopup) {
					var msg = res.msg; // Concatenating users' names
					$.toast({
						heading: "Wallet!",
						text: msg,
						loaderBg: "#bf441d",
						icon: "error",
						hideAfter: 3000, // Changed the time format to milliseconds
						stack: 1
					});
				}
			}, 'json');
		}
		
		$(document).ready(function() {
			$('#payButton').click(function(e) {
				e.preventDefault();
				var amount = $('#recharge_amount').val(); 
				var transaction_type = $('#transaction_type').val();
				var options = {
					"key": "rzp_test_kmwVBY7h35GqKE", 
					"amount": (amount * 100), 
					"currency": "INR",
					"name": "Xpressfly",
					"description": "Wallet Recharge",
					"image": "{{ asset('assets/images/dashbord/logo.svg') }}",
					"handler": function (response)
					{  
						var payable_response = response;
						var dataURL = "{{ route('recharge.wallet.razorpay') }}";
						var user_id = $('#user_id').val();
						$.ajax({
							url: dataURL,
							data: {
								"_token": "{{ csrf_token() }}",
								'user_id': user_id,
								'txn_number' : response.razorpay_payment_id,
								'amount': amount,
								'transaction_type': transaction_type,
								'payable_response': payable_response,
							},
							type: 'post',
							dataType: 'json',
							success: function (data) {
								toastrMsg(data.status, data.message);	
								setTimeout(function() {
										window.location.reload();
									}, 2000);
							},
							error: function (xhr, status, error) {
								console.error(xhr.responseText);
								toastrMsg('error', 'An error occurred while processing your request.');
								
							}
						});
					},
					"prefill": {
						"name": "{{ Auth::user()->name }}",
						"email": "{{ Auth::user()->email }}"
					},                
					"theme": {
						"color": "#528FF0"
					}
				};
				var rzp1 = new Razorpay(options);
				rzp1.open();
			});
		});
		
		function redirectSimpleUrl(url) {
			window.location.href = url;
		}
	</script>
	@stack('js')
</body> 
</html>													