<!DOCTYPE html>
<html lang="en">
	
	<head>
		<meta charset="utf-8" />
		<title>{{config('setting.company_name')}} :: Register</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta content="Star Express" name="description" /> 
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<!-- App favicon -->
		<link rel="shortcut icon" href="{{asset('storage/settings/'.config('setting.fevicon_icon'))}}">
		 
		<!-- App css -->
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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
		
	</head>
	
	<body class="p-0">
	    
	    <style>
	        .main-text-input-filld-up{ 
				z-index: 999;
				position: relative;
	        }
			
			.main-login-21 .from-group {
				margin: 15px 0px !important;
			}
	           
            .otp_btn{
				background-color: #030326  !important;
				border-color: #030326 !important;
            }
            .otp_btn:hover{
				background-color: #030326  !important;
				border-color: #030326 !important;
            }
            
            @media(max-width: 1600px){
				.from-group-up input, .from-group-up select, .from-group-up textarea{
					font-size: 12px !important;
				}
				.from-group-up label{
					font-size: 13px !important;
					margin-bottom: 5px;
				}
				.main-logo-121 h5{
					font-size: 16px;
				}
				.form-control {
					font-size: 12px !important;
					padding: 9px !important;
				} 
            }
            @media(max-width: 991px)
			{
				.main-text-input-filld-up{
					width: 100%;
				}
            }
            @media(max-width: 576px){
				section.main-logout-login{
					height: auto !important;
					padding: 20px 0;
				}
            }
		</style>
		<section class="main-logout-login">
			<div class="container">
				<div class="main-text-input-filld-up wrapper1">
					<div class="main-logo-121">
						<img src="{{ asset('storage/settings/'.config('setting.login_logo')) }}" style="height: 90px">
						
						<h5 style="color: #000;"> Register now! </h5>
					</div>
					
					<form method="post" id="signupForm" action="#">
						@csrf 
						<div class="main-login-21">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<div class="from-group">
										<label for="packaging-type"> Company Name <span class="text-danger"> * </span> </label>
										<input type="text" name="company_name" id="company_name" placeholder="Company Name" required>
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6">
									<div class="from-group">
										<label for="packaging-type"> Full Name <span class="text-danger"> * </span></label>
										<input type="text" id="name" name="name" placeholder="Full Name" required>
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6">
									<div class="from-group">
										<label for="packaging-type"> Mobile <span class="text-danger"> * </span></label>
										<input type="text" name="mobile" placeholder="Mobile" id="mobile" maxlength="10" pattern="\d{10}" title="Please enter exactly 10 digits" required="">
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6">
									<div class="from-group">
										<label for="packaging-type"> Email <span class="text-danger"> * </span></label>
										<input type="email" name="email" id="email" placeholder="Email" required autocomplete="off">
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6">
									<div class="from-group">
										<label for="packaging-type"> Password <span class="text-danger"> * </span></label>
										<input type="password" placeholder="Password" id="password" name="password" autocomplete="off">
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6">
									<div class="from-group">
										<label for="packaging-type"> Gender <span class="text-danger"> * </span></label>
										<select name="gender" id="genderDropdown" class="form-control" required>
											<option value=""> Select Gender</option>
											<option value="Male"> Male</option>
											<option value="Female"> Female</option>
											<option value="Other "> Other</option>
										</select>
									</div>
								</div>
								
								<div class="col-lg-12">
									<div class="from-group">
										<label for="packaging-type"> Address <span class="text-danger"> * </span></label>
										<textarea name="address" id="address"></textarea>
									</div>
								</div>
								
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="packaging-type"> ZipCode <span class="text-danger"> * </span></label>
										<input type="text" id="zip_code" name="zip_code"  placeholder="ZipCode">
									</div>
								</div>
								
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="packaging-type"> City <span class="text-danger"> * </span></label>
										<input type="text" id="city" name="city" placeholder="city">
									</div>
								</div>
								
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="packaging-type"> State <span class="text-danger"> * </span></label>
										<input type="text" id="state" name="state" placeholder="State">
									</div>
								</div>
								
								<div class="col-lg-3 col-sm-6">
									<div class="from-group">
										<label for="packaging-type"> Country <span class="text-danger"> * </span></label>
										<input type="text" id="country" name="country" placeholder="Country">
									</div>
								</div> 
							</div>
						</div>
						<div class="main-login-btn mt-2">
							<a href="javascript:;"> <button type="submit" id="sign_up" class="btn-main-1"> Sign Up </button> </a>
							<button type="resend" id="resend_btn" class="btn btn-primary otp_btn d-none">Resend OTP</button>
						</div>
					</form> 
					<p class="sin-up"> Do you already have an account? <a href="{{ route('login') }}"> Sign in </a> </p>
				</div>
				
				<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="otpModalLabel">Verify OTP</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form id="otpForm" action="{{ route('register.verify-otp') }}" method="post"> 
									@csrf
									<div class="mb-3">
										<label for="otpInput" class="form-label">Enter OTP</label>
										<input type="text" class="form-control" id="otpInput" name="otp" oninput="this.value = this.value.replace(/\D/g, '')" maxlength="6" required>
									</div> 
									<button type="submit" class="btn btn-primary">Verify OTP</button>
									<button type="button" id="resendOtp"  class="btn btn-primary">Resend OTP</button>
								</form>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</section>
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
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
		
        <script>
			$(document).ready(function()
			{
				const $signupForm = $('#signupForm');
				const $otpForm = $('#otpForm');
				const $resendOtp = $('#resendOtp');
				
				const otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
				
				$signupForm.submit(function (event) {
					event.preventDefault();

					// Disable submit button to prevent multiple submissions
					$signupForm.find(':submit').prop('disabled', true);

					// Create FormData object
					var formData = new FormData(this);
					formData.append('_token', "{{ csrf_token() }}"); // Use meta tag for CSRF token

					$.ajax({
						type: $signupForm.attr('method'),
						url: $signupForm.attr('action'),
						data: formData,
						cache: false,
						processData: false,
						contentType: false,
						dataType: 'json', // Use lowercase "json"
						success: function (res) {
							$signupForm.find(':submit').prop('disabled', false); 
							if(res.status == "success")
							{
								toastrMsg(res.status, res.msg); 
								setTimeout(function(){
									window.location.href = "{{ route('login') }}";
								}, 1000)
							}
							else if(res.status == "mail")
							{
								localStorage.setItem('generatedOtp', res.otp); 
								otpModal.show();
							}
							else 
							{
								toastrMsg(res.status, res.msg); 
							}
						},
						error: function (xhr) {
							$signupForm.find(':submit').prop('disabled', false); // Re-enable button on failure
							console.error("AJAX error:", xhr.responseText);
							toastrMsg("error", "Something went wrong. Please try again.");
						}
					});
				});
				
				$otpForm.submit(function (event)
				{
					event.preventDefault();

					// Disable submit button to prevent multiple submissions
					$otpForm.find(':submit').prop('disabled', true);
					const generatedOtp = localStorage.getItem('generatedOtp');
					 
					if(!generatedOtp)
					{
						toastrMsg("error", "Something went wrong. Please try again.");
						return;
					}
					
					// Create FormData object
					var formData = new FormData(this);
					formData.append('_token', "{{ csrf_token() }}"); // Use meta tag for CSRF token
					formData.append('generatedOtp', generatedOtp); // Use meta tag for CSRF token
					
					$.ajax({
						type: $otpForm.attr('method'),
						url: $otpForm.attr('action'),
						data: formData,
						cache: false,
						processData: false,
						contentType: false,
						dataType: 'json',  
						success: function (res) {
							$otpForm.find(':submit').prop('disabled', false); 
							$signupForm.find('#is_verify_otp').remove();
							if(res.status == "success")
							{ 
								$signupForm.append('<input type="hidden" id="is_verify_otp" name="is_verify_otp" value="1">'); 
								toastrMsg(res.status, res.msg); 
								setTimeout(function(){
									otpModal.hide(); 
									$signupForm.submit();
								}, 1000)
							} 
							else 
							{
								toastrMsg(res.status, res.msg); 
							}
						},
						error: function (xhr) {
							$otpForm.find(':submit').prop('disabled', false); // Re-enable button on failure
							console.error("AJAX error:", xhr.responseText);
							toastrMsg("error", "Something went wrong. Please try again.");
						}
					});
				}); 
				
				$resendOtp.click(function (event) {
					event.preventDefault();
					$resendOtp.prop('disabled', true);

					const name = $signupForm.find('#name').val() || ''; 
					const email = $signupForm.find('#email').val() || ''; 

					// Correctly initialize FormData
					var formData = new FormData();
					formData.append('_token', "{{ csrf_token() }}"); // Get CSRF token from meta tag
					formData.append('name', name);
					formData.append('email', email);

					$.ajax({
						type: 'post',
						url: "{{ route('register.resend-otp') }}",
						data: formData,
						cache: false,
						processData: false, // Necessary for FormData
						contentType: false, // Necessary for FormData
						dataType: 'json',  
						success: function (res) {
							$resendOtp.prop('disabled', false);

							if (res.status === "mail") {
								localStorage.setItem('generatedOtp', res.otp); 
								toastrMsg("success", res.msg); 
							} else {
								toastrMsg("error", res.msg); 
							}
						},
						error: function (xhr) {
							$otpForm.find(':submit').prop('disabled', false); // Re-enable button on failure
							console.error("AJAX error:", xhr.responseText);
							toastrMsg("error", "Something went wrong. Please try again.");
						}
					});
				});
  
			});
			 
			function toastrMsg(type,msg)
			{
				if(type == "success")
				{
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
				if(type == "error")
				{
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
			}  
			
			/* // Disable right-click on the entire document
			document.addEventListener('contextmenu', function(event) {
				event.preventDefault();  // Prevent right-click context menu
				return false; // Ensure no other context menu can be triggered
			});
			 
			// Prevent mouse button event that could trigger context menu (for right-click prevention)
			document.addEventListener('mousedown', function(event) {
				// Check if it's a right-click (button 2 is the right-click mouse button)
				if (event.button === 2) {
					event.preventDefault(); // Prevent right-click
				}
			});

			// Optionally, disable drag event, which could reveal the page source or cause issues
			document.addEventListener('dragstart', function(event) {
				event.preventDefault();  // Prevent dragging
			});

			// Prevent Developer Tools Access by monitoring focus events (URL bar & Dev Tools)
			let devToolsOpen = false;
			Object.defineProperty(document, 'hidden', {
				get: function() {
					devToolsOpen = true;
					return false;
				}
			});

			// Detect if the developer tools are open and prevent right-click while in this state
			setInterval(function() {
				if (devToolsOpen) {
					// Disable right-click on the page while developer tools are open
					document.body.style.pointerEvents = 'none';  
				} else {
					document.body.style.pointerEvents = 'auto';  // Allow interaction once developer tools are closed
				}
			}, 1000);

			document.onkeydown = function(event) {
				// Preventing common developer tools and inspect element shortcuts
				if (
					(event.key === 'F12') || // F12 - Developer Tools
					(event.ctrlKey && event.shiftKey && event.key === 'I' || event.ctrlKey && event.shiftKey && event.key === 'i') || // Ctrl + Shift + I - Developer Tools
					(event.ctrlKey && event.shiftKey && event.key === 'J' || event.ctrlKey && event.shiftKey && event.key === 'j') || // Ctrl + Shift + J - Console
					(event.ctrlKey && event.shiftKey && event.key === 'Z' || event.ctrlKey && event.shiftKey && event.key === 'z') || // Ctrl + Shift + Z - Console
					(event.ctrlKey && event.shiftKey && event.key === 'K' || event.ctrlKey && event.shiftKey && event.key === 'k') || // Ctrl + Shift + K - Console
					(event.ctrlKey && event.shiftKey && event.key === 'E' || event.ctrlKey && event.shiftKey && event.key === 'e') || // Ctrl + Shift + E - Console
					(event.shiftKey && event.key === 'F7') || 
					(event.shiftKey && event.key === 'F5') || 
					(event.shiftKey && event.key === 'F9') || 
					(event.shiftKey && event.key === 'F12') || 
					(event.shiftKey && event.key === 'F2') || 
					(event.ctrlKey && event.key === 'U' || event.ctrlKey && event.key === 'u') || // Ctrl + U - View Source
					(event.ctrlKey && event.key === 'C' || event.ctrlKey && event.key === 'c') || // Ctrl + C - In some browsers, used for copying the inspected code
					(event.ctrlKey && event.key === 'S' || event.ctrlKey && event.key === 's') || // Ctrl + S - Save Page, can be used for code inspection
					(event.ctrlKey && event.key === 'P' || event.ctrlKey && event.key === 'p') || // Ctrl + P - Print Page, also can open Developer Tools
					(event.key === 'F11') || // F11 - Full Screen, can sometimes be used to enter DevTools in some browsers
					(event.altKey && event.key === 'F12') // Alt + F12 - Developer Tools in some browsers
				) {
					event.preventDefault();  
				}
		 
				if (event.button === 2) {
					event.preventDefault();  
				}  
			}; */
		</script>
	</body>  
</html>