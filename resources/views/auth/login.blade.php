<!DOCTYPE html>
<html lang="en">
	
	<head>
		<meta charset="utf-8" />
		<title>{{config('setting.company_name')}} :: Login</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta content="{{ config('setting.company_name') }}" name="description" /> 
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<!-- App favicon -->
		<link rel="shortcut icon" href="{{asset('storage/settings/'.config('setting.fevicon_icon'))}}">
		
		<!-- App css -->
		<link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" >
		
	</head>
	
	<body class="p-0">
		<section class="main-logout-login">
			<div class="container">
				<div class="row">
					<div class="col-7 main-text-input-filld wrapper1" >
						
						<div class="main-logo-121">
							<img src="{{asset('storage/settings/'.config('setting.login_logo'))}}" style="height: 90px"> 
							<h5>{{ __('Login') }} Using {{config('setting.company_name')}}</h5>
						</div>
						<form method="POST" action="{{ route('login.post') }}">
							@csrf 
							<div class="main-login-21">
								<div class="from-group">
									<label for="packaging-type"> {{ __('E-Mail Address') }}</label>
									<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter {{ __('E-Mail Address') }}" autofocus>
									@error('email')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
								
								<div class="from-group">
									<label for="packaging-type"> {{ __('Password') }} </label>
									<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required  placeholder="Enter {{ __('Password') }}" autocomplete="current-password">
									
									@error('password')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror 
								</div> 
							</div>
							
							<p style="color:#fff; line-height: 0;float: right;"><a href="{{ route('forgot-password') }} " style="color: #838383; font-weight : 500;">Forgot Password</a></p>

							
							<div class="main-login-btn">
								<a href="javascript:;"> <button type="submit" class="btn-main-1">  {{ __('Login') }} </button> </a>
							</div>
						</form>
						<p class="sin-up"> Don't Have Account <a href="{{ route('register') }}" style="color: #5640B0;"> Sign Up </a> </p>
			
					</div>
					<div class="col-5 blue-ng-login-page">
						<img src="{{asset('assets/images/dashbord/login-images.png')}}" alt="">
					</div>
				</div>
			</div>
		</section>
	</body>
	
</html>