<style>
    .dropdown-item {
		  padding: 10px 15px !important;
	}
    
</style>
<!-- Topbar Start -->
<div class="navbar-custom">
	
	<div class="main-mob-menu">
		 <button class="button-menu-mobile waves-effect waves-light">
			<i class="fe-menu" style="color: #000 !important; "></i>
		</button> 
	</div>
	
	<div class="page_head">
	    <h2>@yield('header_title')</h2>
	</div>
	
	<ul class="list-unstyled topnav-menu float-right mb-0">
		@if(Auth::user()->role != "admin" && Auth::user()->role != "staff")
			<li class="head-li moin-bord" data-toggle="modal" data-target="#rechargeWalletModal" >
				<div class="balcnce-wa">
					<img src="{{asset('assets/images/dashbord/wallet-1.png')}}" style="    width: auto;padding: 17px 15px;background: black;border-radius: 10px 0px 0px 10px;">
					<h6 style="margin-right : 10px;"> {{ config('setting.currency') }} {{ Helper::decimal_number(Auth::user()->wallet_amount) }} </h6>
					<!-- <i class="fe-refresh-ccw" style="color: #000000ff;"></i> -->
				</div>
			</li> 
		@endif
		<li class="dropdown notification-list mo-none" >
			<a class="nav-link dropdown-toggle  waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
				<img src="{{asset('assets/images/dashbord/notification-1.png')}}">
				<span class="badge badge-danger rounded-circle noti-icon-badge notifycount" ></span>
			</a>
			<div class="dropdown-menu dropdown-menu-right dropdown-lg">
				
				<!-- item-->
				<div class="dropdown-item noti-title">
					<h5 class="m-0">
						<span class="float-right">
							<a href="{{ route('notification.clear-all') }}" class="text-dark">
								<small>Clear All</small>
							</a>
						</span>Notification
					</h5>
				</div> 
				<div class="slimscroll noti-scroll notifymsg">  
				</div>  
				<!-- All-->
				<a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item notify-all"> 
				</a> 
			</div>
		</li>
		
		<li class="dropdown notification-list mo-none-991" >
			<a class="nav-link waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
				<div class="balcnce-wa new-q-action-bg">
					<img src="{{asset('assets/images/dashbord/electricity.png')}}">
					<h6  style=" color: white;"> Quick Actions </h6>
				</div>
			</a>	

				<div class="dropdown-menu dropdown-menu-right dropdown-lg" style="width: 500px; max-width: 500px; border-radius : 20px; right: 0 !important;">
				<div class="main-row-12">
					<div class="main-anitio1 bg-bo-2" onclick="redirectSimpleUrl('{{ url('order/create') }}?weight_order=1')" style="cursor:pointer;">
						<a href="javascript:;"><img src="{{asset('assets/images/order-1/tt-1.png')}}"></a>
						<h5> Add an Order </h5>
					</div> 
				  
					<div class="main-anitio1 bg-bo-3" onclick="redirectSimpleUrl('{{ url('rate/calculator') }}')" style="cursor:pointer;">
						<a href="javascript:;"><img src="{{asset('assets/images/order-1/tt-2.png')}}"></a>
						<h5> Rate Calculator </h5>
					</div>
					@if(Auth::user()->role == "user")
						<div class="main-anitio1 bg-bo-1" onclick="redirectSimpleUrl('{{ url('ticket/add') }}')" style="cursor:pointer;">
							<a href="javascript:;"><img src="{{asset('assets/images/order-1/tt-3.png')}}"></a>
							<h5> Create a Ticket </h5>
						</div>
					@endif
					<div class="main-anitio1 bg-bo-4" onclick="redirectSimpleUrl('{{ url('order') }}?weight_order=1&status=All')" style="cursor:pointer;">
						<img src="{{ asset('assets/images/order-1/tt-4.png') }}" alt="Track Shipments">
						<h5>Track Shipments</h5>
					</div>
				</div>
				<div class="main-do-it">
					<button class="simple-021-btn">Close Action Descriptions</button>
				</div>
			</div>
		</li>
		</li>

		
		
		
		<li class="dropdown notification-list mo-none">
			<a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"> 
				@if(auth()->user()->profile_image)
					<img src="{{url('storage/profile/',Auth::user()->profile_image)}}">  
				@else 
					<img src="{{asset('assets/images/profile-logo.png')}}">  
				@endif  
				<h6 style="color:#000;">{{ auth()->user()->name }}</h6>
			</a>
			<div class="dropdown-menu dropdown-menu-right profile-dropdown ">
				<!-- item-->
				<a href="{{route('profile')}}" class="dropdown-item notify-item">
					<i class="fe-user"></i>
					<span> Profile </span>
				</a>
				
				<div class="dropdown-divider"></div>
				
				<!-- item-->
				<a href="{{route('change-password')}}" class="dropdown-item notify-item">
					<i class="fe-lock"></i>
					<span> Change Password </span>
				</a>
				
				<div class="dropdown-divider"></div>
				
				<!-- item-->
				<a href="{{ route('logout') }}" class="dropdown-item notify-item" onclick="event.preventDefault();
				document.getElementById('logout-form').submit();">
					<i class="fe-log-out"></i>
					<span>{{ __('Logout') }}</span>
				</a>
				<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
					@csrf
				</form> 
			</div>
		</li>
	</ul> 
</div>





