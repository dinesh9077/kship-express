<style>
    .dropdown-item {
		  padding: 10px 15px !important;
	} 
	.search-container {
		position: relative;
	}
	.search-results-dropdown {
    position: absolute;
    top: 100%; /* appear below the input */
    left: 0;
    width: 100%;
    max-height: 300px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    z-index: 9999; /* ensures it’s above header and other elements */
}

.custom-search-input {
    width: 100%;
    padding: 10px 40px 10px 43px;
    border-radius: 6px;
    outline: none;
}

.search-result-item {
	padding: 10px 15px;
	border-bottom: 1px solid #eee;
	background-color: #fff; /* ✅ ensure each item is solid */
	cursor: pointer;
	transition: background-color 0.2s ease;
}

.search-result-item:hover {
	background-color: #f2f2f2;
}

.search-result-item .order-id strong {
	color: #007bff;
}

.search-result-item .awb-number {
	color: #666;
	font-size: 13px;
	margin-left: 5px;
}

.search-result-item .order-details {
	display: flex;
	gap: 10px;
	align-items: center;
	font-size: 13px;
	color: #444;
	margin-top: 4px;
}

.no-results {
	padding: 10px 15px;
	color: #888;
	text-align: center;
	background: #fff;
}

.custom-search-wrapper .search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #777;
  font-size: 18px;
  pointer-events: none; /* so click passes to input */
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
		<li style="position: relative;">
<div class="custom-search-wrapper">
  <input type="text" 
    class="custom-search-input" 
    id="orderSearch"
    placeholder="Search Order By AWB Number & Order Id"
    autocomplete="off"> 
  <i class="fa fa-search search-icon"></i> <!-- Font Awesome icon -->
</div>
    <div id="searchResults" class="search-results-dropdown"></div>
</li>
 
		
		@if(Auth::user()->role != "admin" && Auth::user()->role != "staff")
			<li class="head-li moin-bord" data-toggle="modal" data-target="#rechargeWalletModal" >
				<div class="balcnce-wa">
					<img src="{{asset('assets/images/dashbord/wallet-1.png')}}" style="    width: auto;padding: 13px 12px;background: black;border-radius: 10px 0px 0px 10px;">
					<h6 style="margin-right : 10px;"> {{ config('setting.currency') }} {{ Helper::decimal_number($authUser->wallet_amount) }} </h6>
				</div>
			</li> 
		@endif
 
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





