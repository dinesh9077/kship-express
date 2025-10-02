<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{config('setting.company_name')}} :: Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ config('setting.company_name') }}" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('storage/settings/'.config('setting.fevicon_icon'))}}">

    <!-- CSS -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #f7f7f7;
        }

        .login-container {
            max-width: 1800px;
            margin: 100px auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        }

        @media(max-width : 1800px) {
            .login-container {
                max-width: 1700px;
            }
        }

        @media(max-width : 1700px) {
            .login-container {
                max-width: 1600px;
                margin: 100px;
            }
        }

        @media(max-width : 1600px) {
            .login-container {
                max-width: 1500px;
            }
        }

        @media(max-width : 1500px) {
            .login-container {
                max-width: 1400px;
            }
        }

        @media(max-width : 1400px) {
            .login-container {
                max-width: 1300px;
            }
        }

        @media(max-width : 1300px) {
            .login-container {
                max-width: 1200px;
            }
        }

        @media(max-width : 1200px) {
            .login-container {
                max-width: 1100px;
            }
        }

        @media(max-width : 1100px) {
            .login-container {
                max-width: 1000px;
            }
        }

        @media(max-width : 1100px) {
            .login-container {
                max-width: 900px;
            }
        }

        @media(max-width : 900px) {
            .login-container {
                max-width: 800px;
            }
        }

        @media(max-width : 800px) {
            .login-container {
                max-width: 700px;
            }
        }

        @media(max-width : 700px) {
            .login-container {
                max-width: 600px;
            }
        }

        @media(max-width : 600px) {
            .login-container {
                max-width: 500px;
                margin: 0px;
            }
        }

        @media(max-width : 500px) {
            .login-container {
                max-width: 400px;
            }
        }

        .login-left {
            padding: 100px 250px;
        }

      


        @media(max-width : 1600px) {
            .login-left {
                padding: 100px 150px;
            }
        }

           @media(max-width : 1100px) {
            .login-left {
                padding: 50px 50px;
            }
        }


        .login-left h5 {
            font-weight: 600;
            margin: 20px 0;
        }

        .form-control {
            border-radius: 25px;
            padding: 12px 20px;
        }

        .btn-main-1 {
            border-radius: 25px;
            background: #5640B0;
            color: #fff;
            font-weight: 600;
            padding: 12px;
            width: 100%;
            border: none;
            transition: 0.3s;
        }

        .btn-main-1:hover {
            background: #432e8f;
        }

        .forgot {
            font-size: 16px;
            font-weight: 500;
            color: #727272;
            display: block;
            text-align: right;
            margin-top: 5px;
        }

        .sin-up {
            font-size: 14px;
            margin-top: 15px;
        }

        .login-right {
            background: #5640B0;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 100px 100px;
            position: relative;
        }

        @media(max-width : 1600px) {
            .login-right {

                padding: 100px 50px;

            }

        }

        .login-right h1 {
            font-size: 26px;
            font-weight: 400;
            margin-top: 20px;
            text-transform: capitalize;
        }

        .login-right p {
            font-size: 16px;
            font-weight: 500;
            max-width: 400px;
            text-align: center;
        }

        .info-box {
            position: absolute;
            padding: 12px 18px;
            border-radius: 10px;
            background: #fff;
            color: #333;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }

        .rate-box {
            top: 50px;
            right: 50px;
            background: #fcdca7;
        }

        .recharge-box {
            bottom: 100px;
            left: 40px;
            background: #b8f3c8;
        }

        .order-box {
            bottom: 40px;
            right: 70px;
            background: #dcd8fc;
        }

        @media(max-width: 992px) {
            .login-container {
                flex-direction: column;
            }

            .login-right {
                min-height: 300px;
            }
        }
    </style>
</head>

<body class="p-0">

    <section class="login-container d-flex">

        <!-- Left Side -->
        <div class="login-left col-lg-7 col-12">
            <div class="text-center mb-4">
                <img src="{{asset('storage/settings/'.config('setting.login_logo'))}}" style="height: 80px">
            </div>
            <h5 class="text-center mt-5">{{ __('Login') }} To {{config('setting.company_name')}}</h5>

            <form method="POST" action="{{ route('login.post') }}" class="mt-5">
                @csrf
                <div class="mb-3">
                    <label for="email"> {{ __('E-Mail Address') }}</label>
                    <input id="email" type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}"
                        required autocomplete="email"
                        placeholder="Enter {{ __('E-Mail Address') }}" autofocus>
                    @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password"> {{ __('Password') }} </label>
                    <input id="password" type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        name="password" required
                        placeholder="Enter {{ __('Password') }}"
                        autocomplete="current-password">
                    @error('password')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <a href="{{ route('forgot-password') }}" class="forgot">Forgot Password?</a>

                <div class="mt-3">
                    <button type="submit" class="btn-main-1" style="border-radius : 50px !important;">{{ __('Login') }}</button>
                </div>
            </form>

            <p class="sin-up text-center">Don't Have Account <a href="{{ route('register') }}" style="color: #5640B0;"> Sign Up </a></p>


            <p class="text-center" style="color : #3f3f3fff; font-weight : 500; margin-top : 96px; font-size : 18px;">Developed By <span style="color: #000000ff;"> Softieons</span> </a></p>
        </div>

        <!-- Right Side -->
        <div class="login-right col-lg-5 col-12" style="position: relative;">
            <img src="{{asset('assets/images/dashbord/login-image1.png')}}" alt="" class="img-fluid mb-3">
            <h1 class="mt-5">{{ config('setting.company_name') }} Solutions Worldwide</h1>
            <p class="mt-5">"Express Delivery," "Standard Delivery," or "Fragile Goods Handling" under "Domestic Shipping"</p>

            <img src="{{asset('assets/images/dashbord/vecotr-login.png')}}" alt="" style="position: absolute; bottom : 0px ;left : 0px;">
            <img src="{{asset('assets/images/dashbord/vecotr-login.png')}}" alt="" style="position: absolute; top : 0px; right : 0px;">

        </div>

    </section>

</body>

</html>