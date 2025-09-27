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
			<div class="modal fade" id="rechargeWalletModal" tabindex="-1" role="dialog" aria-labelledby="rechargeWalletLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
				<div class="modal-dialog modal-lg model-width-1">
					<div class="modal-content">
						
						{{-- Header --}}
						<div class="modal-header head-00re pb-0 border-0">
							<h5 class="modal-title" id="rechargeWalletLabel">Recharge Your Wallet</h5>
							<!--<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>-->
						</div>

						<form id="paymentForm">
							<div class="modal-body pt-0"> 
								{{-- Current Wallet --}}
								<div class="main-01">
									<h5>
										Current Wallet Amount: 
										<span>{{ config('setting.currency') }}{{ Helper::decimal_number($authUser->wallet_amount) }}</span>
									</h5>
								</div>

								<div class="man-01rech"> 
									<input type="hidden" id="transaction_type" name="transaction_type" value="Online">
									<input type="hidden" id="user_id" name="user_id" value="{{ $authUser->id }}">
								
									{{-- Amount Input --}}
									<h5>Enter Amount</h5>
									<div class="form-group rech-re-form position-relative">
										<span class="position-absolute custom-rupee-position">{{ config('setting.currency') }}</span>
										<input type="number" name="amount" id="recharge_amount"  value="200"  placeholder="200" class="form-control" required>
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
								</div> 
							</div> 

							{{-- QR Code Container --}}
							<div id="qrcode" style="display:flex; justify-content:center; margin:20px 0;"></div>

							{{-- Status Message --}}
							<div id="statusMessage" class="status-message" style="text-align:center; margin-bottom:10px; display:none;"></div>

							{{-- Footer --}}
							<div class="modal-footer justify-content-center">
								<button type="button" class="btn btn-primary btn-main-1" id="butnwallethide" onclick="initiatePayment()">
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
		</div> 
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
	<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/qr-code-styling@1.6.0/lib/qr-code-styling.js"></script>
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
		
		function redirectSimpleUrl(url) {
			window.location.href = url;
		}
		 
		let currentOrderId = null;
		let checkInterval = null;

		function showMessage(message, type) {
			const msgEl = document.getElementById('statusMessage');
			msgEl.textContent = message;
			msgEl.className = `status-message status-${type}`;
			msgEl.style.display = 'block';
		}

		function setRechargeAmount(button, amount) {
			document.getElementById('recharge_amount').value = amount;
			document.querySelectorAll('.re-btn').forEach(btn => btn.classList.remove('active'));
			button.classList.add('active');
		}

		async function createOrder(amount) {
			const response = await fetch("{{ route('recharge.wallet.amount') }}", {  // Laravel route to store pending order
				method: 'POST',
				headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
				body: JSON.stringify({
					amount: amount,
					user_id: "{{ $authUser->id }}",
					transaction_type: 'Online'
				})
			});
			const data = await response.json(); 
			if (data.status == "error") throw new Error(data.msg);
			return data.order;
		}

		async function checkPaymentStatus() {
		  if (!currentOrderId) return;

		  try {
			const response = await fetch('https://api.recharge.kashishindiapvtltd.com/payments/check-payment-gateway', {
				  method: 'POST',
				  headers: { 'Content-Type': 'application/json' },
				  body: JSON.stringify({
						id: currentOrderId,
						secret_key: "kashishindiapvtltdgatewaywithrznew",
						user: "8c816dcb-766b-4c7c-bbc3-831b494966fe"
				  })
			});

			const data = await response.json();
			console.log("Payment check:", data);

			if (data?.data?.paymentStatus === "captured")
			{
				clearInterval(checkInterval);
				
				const response = await fetch("{{ route('recharge.wallet.razorpay') }}", {  // Laravel route to store pending order
					method: 'POST',
					headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
					body: JSON.stringify({
						order_id: currentOrderId, 
						txn_id: data.data.paymentDetails.id ?? '', 
					})
				});
				const res = await response.json();   
				showMessage("✅ Payment Successful!", "success",data.data.paymentDetails.id); 
				setInterval(function() {
					location.reload();
				}, 3000);
				
			} else {
			  showMessage("Checking... Status: " + (data.data?.status || "Pending"), "info");
			}
		  } catch (err) {
			showMessage("Error checking status: " + err.message, "error");
		  }
		}
		 
		async function initiatePayment() {
			const amount = parseFloat(document.getElementById('recharge_amount').value);
		    if (!amount || amount < 200) {
				showMessage("Enter a valid amount (min ₹200)", "error");
				return;
			}
			
			try {
				showMessage("Creating order...", "info");
				const orderData = await createOrder(amount);
				currentOrderId = orderData.order_id;
				
				// Display QR Code
				const qrCode = new QRCodeStyling({
					width: 250,
					height: 250,
					data: orderData.qrTextContent,
					dotsOptions: { color: "#000" }
				});
				document.getElementById("qrcode").innerHTML = "";
				qrCode.append(document.getElementById("qrcode"));

				showMessage("Scan QR to Pay via UPI", "info");
				$('#butnwallethide').hide();
				// Start checking payment status every 3 sec
				if (checkInterval) clearInterval(checkInterval);
				checkInterval = setInterval(checkPaymentStatus, 3000);

			} catch (err) {
				showMessage("Error: " + err.message, "error");
			}
		} 
	</script>
	@stack('js')
</body> 
</html>													