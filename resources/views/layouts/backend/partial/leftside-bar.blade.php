<style>
    .head-li.mail.mob-money .btn.btn-primary.btn-closee-1 {
        width: 100%;
        background: #4351f3;
    }
    
    .head-li.mail.mob-money {
    	margin: 15px 0 !important;
    	display: none;
    }
    
    @media(max-width: 767px){
        .head-li.mail.mob-money {
        	display: block;
        }
    }
</style>
<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu wrapper1">
	
	<div class="slimscroll-menu">
		<div class="logo-box">
			<a href="{{url('')}}" class="logo text-center">
				<div class="user1">
					<img src="{{asset('storage/settings/'.config('setting.header_logo'))}}" alt="" style="height:70px;">
				</div> 
			</a>
		</div>
		<!--- Sidemenu -->
		<div id="sidebar-menu">
			
			<ul class="metismenu p-0" id="side-menu"> 
        		<!-- <li class="head-sub">
					<h6>Menu</h6>
				</li> -->
				<!-- <li class="head-sub mb-2">
					<div class="kship-left-header-search">
						<i class="mdi mdi-magnify"></i>
						<input type="text" placeholder="Search">
					</div>
				</li> -->
				@if(Auth::user()->role == "user")
					@include('layouts.backend.partial.user-sidebar')
				@else
					<li>
						<a href="{{url('home')}}" class="{{ request()->is('home*') ? 'active' : '' }}">
							<i  class="mdi mdi-view-dashboard-outline"></i>
							<span> Dashboard </span>
						</a>
					</li>
					@if(config('permission.order.view'))
						<!--<li>
							<a href="{{ route('order') }}?weight_order=1" class="{{ request()->is(['order', 'order/create', 'order/edit*', 'order/details*', 'order/clone*', 'order/label*', 'order/tracking-history*', 'order/shipping-label*']) ? 'active' : '' }}">
								<i class="mdi mdi-package-variant-closed"></i>
								<span> Order </span>
							</a>
						</li>-->
						
						<li class="{{ request()->is('order*') && request('weight_order') ? 'mm-active' : '' }}">
							<a href="javascript: void(0);" class="{{ request()->is('order*') && request('weight_order') ? 'active' : '' }}">
								<i class="mdi mdi-package-variant-closed"></i>
								<span> Order </span>
								<span class="menu-arrow"></span>
							</a>
							<ul class="nav-second-level {{ request()->is('order*') && request('weight_order') ? 'mm-collapse mm-show' : '' }}" aria-expanded="false">  
								<li class="{{ request()->is('order*') && request('weight_order') == 1 ? 'mm-active' : '' }}">
									<a class="{{ request()->is('order*') && request('weight_order') == 1 ? 'active' : '' }}" 
									   href="{{ route('order') }}?weight_order=1"> 
									   B2C Order 
									</a>
								</li> 
								<li class="{{ request()->is('order*') && request('weight_order') == 2 ? 'mm-active' : '' }}">
									<a class="{{ request()->is('order*') && request('weight_order') == 2 ? 'active' : '' }}" 
									   href="{{ route('order') }}?weight_order=2"> 
									   B2B Order 
									</a>
								</li>  
							</ul>
						</li> 
					@endif
					@if(config('permission.bulk_order.view'))
						<li>
							<a href="{{ route('order.bulk-create') }}" class="{{ request()->is('order/bulk-create') ? 'active' : '' }}">
								<i class="mdi mdi-cart-plus"></i>
								<span> Bulk Order </span>
							</a>
						</li>
					@endif
					@if(config('permission.rate_calculator.view'))
						<li>
							<a href="{{ route('rate.calculator') }}"  class="{{ request()->is('calculator*') ? 'active' : '' }}">
								<i class="mdi mdi-calculator"></i>
								<span> Rate Calculator </span>
							</a>
						</li>
					@endif
					{{--@if(config('permission.ndr.view'))
						<li>
							<a href="{{ route('ndr.order') }}" >
								<i class="mdi mdi-alert-circle-outline"></i>
								<span> NDR Order</span>
							</a>
						</li>
					@endif--}}
					@if(config('permission.client.view'))
						<li>
							<a href="{{route('customer')}}" class="{{ request()->is('customer*') ? 'active' : '' }}">
								<i class="mdi mdi-account"></i>
								<span> Recipeint/Customer </span>
							</a>
						</li>
					@endif
					@if(config('permission.warehouse.view'))
						<li>
							<a href="{{ route('warehouse.index') }}" class="{{ request()->is('warehouse*') ? 'active' : '' }}">
								<i class="mdi mdi-home-variant"></i>
								<span> Add Warehouse </span>
							</a>
						</li>
					@endif
					{{-- @if(config('permission.pickup_request.view'))
						<li>
							<a href="{{ route('pickup.request.index') }}" class="{{ request()->is('pickup*') ? 'active' : '' }}">
								<i class="mdi mdi-truck"></i>
								<span> Pickup Request </span>
							</a>
						</li> 
					@endif --}}
					
					@if(config('permission.remittance.view') || config('permission.cod_payout.view'))
						<li>
							<a href="javascript: void(0);">
								<i class="mdi mdi-cash-multiple"></i>
								<span> Remittance Management </span>
								<span class="menu-arrow"></span>
							</a>
							<ul class="nav-second-level" aria-expanded="false">
								@if(config('permission.remittance.view'))
									<li><a href="{{ route('cod-remmitance') }}"> Cod Remittance </a></li>  
								@endif 
								@if(config('permission.cod_payout.view'))
									<li><a href="{{ route('cod-payout') }}"> COD Payout </a></li> 
								@endif
							</ul>
						</li>
					@endif	
					
					@if(config('permission.weight_descripencies.view'))
						<li class="{{ request()->is('weight/descripencies*') ? 'mm-active' : '' }}">
							<a href="{{ route('weight.admin.descripencies') }}" class="{{ request()->is('weight/descripencies*') ? 'active' : '' }}">
								<i class="mdi mdi-scale-balance"></i>
								<span> Weight Discrepancies </span>
							</a>
						</li> 
					@endif		
					  
					@if(config('permission.clients.view') || config('permission.client_kyc_request.view'))
						<li class="{{ request()->is('users*') ? 'mm-active' : '' }}">
							<a href="javascript:void(0);" class="{{ request()->is('users*') ? 'active' : '' }}">
								<i class="mdi mdi-account-multiple"></i>
								<span> Clients </span>
								<span class="menu-arrow"></span>
							</a>
							<ul class="nav-second-level {{ request()->is('users*') ? 'mm-collapse mm-show' : '' }}" aria-expanded="false">  
								@if(config('permission.clients.view'))
									<li class="{{ (request()->is('users') || request()->is('users/edit*')) && request('kyc_status') == '0' ? 'mm-active' : '' }}">
										<a href="{{ route('users') }}?kyc_status=0" class="{{ (request()->is('users') || request()->is('users/edit*')) && request('kyc_status') == '0' ? 'active' : '' }}">
											Pending Clients
										</a>
									</li>    
									<li class="{{ (request()->is('users') || request()->is('users/edit*')) && request('kyc_status') == '1' ? 'mm-active' : '' }}">
										<a href="{{ route('users') }}?kyc_status=1" class="{{ (request()->is('users') || request()->is('users/edit*')) && request('kyc_status') == '1' ? 'active' : '' }}">
											Approve Clients
										</a>
									</li>   
								@endif 
								@if(config('permission.client_kyc_request.view'))
									<li class="{{ request()->is('users/kyc*') ? 'mm-active' : '' }}">
										<a href="{{ route('users.kyc.request') }}" class="{{ request()->is('users/kyc*') ? 'active' : '' }}">
											KYC Request
										</a>
									</li>  
								@endif 
							</ul>
						</li>
					@endif 
					 
					@if(config('permission.roles.view') || config('permission.staff.view'))    
						<li>
							<a href="javascript: void(0);">
								<i class="mdi mdi-account-tie"></i>
								<span> Staff Management </span>
								<span class="menu-arrow"></span>
							</a>
							<ul class="nav-second-level" aria-expanded="false"> 
								@if(config('permission.roles.view')) 
									<li><a href="{{ route('roles') }}"> Roles </a></li>   
								@endif	
								@if(config('permission.staff.view')) 
									<li><a href="{{ route('staff') }}">Staff </a></li> 
								@endif	
							</ul>
						</li>
					@endif	
					
					@if(config('permission.ticket_request.view'))
						<li class="{{ request()->is('ticket*') ? 'mm-active' : '' }}">
							<a href="{{ route('ticket.admin') }}" class="{{ request()->is('ticket*') ? 'active' : '' }}">
								<i class="mdi mdi-ticket-confirmation"></i>
								<span> Ticket Request</span>
							</a>
						</li>
					@endif	
					   
					@if(config('permission.order_report.view') || config('permission.recharge_history.view') || config('permission.income_report.view') || config('permission.payment_report.view') || config('permission.passbook_report.view'))  
						<li> 
							<a href="javascript: void(0);">
								<i class="mdi mdi-file-chart"></i>
								<span> Report </span>
								<span class="menu-arrow"></span>
							</a>
							<ul class="nav-second-level" aria-expanded="false">
								@if(config('permission.order_report.view'))
								<li>
									<a href="{{ route('report.order') }}" class="{{ request()->is('report.order') ? 'active' : '' }}"> 
										<span>Order Report</span>
									</a>
								</li>
								@endif
								@if(config('permission.passbook_report.view'))
								<li>
									<a href="{{route('report.passbook')}}" class="{{ request()->is('passbook*') ? 'active' : '' }}"> 
										<span> Passbook </span>
									</a>
								</li> 
								@endif 
								@if(config('permission.recharge_history.view'))
								<li>
									<a href="{{route('recharge.list.history')}}" class="{{ request()->is('recharge.list.history') ? 'active' : '' }}"> 
										<span> Recharge History </span>
									</a>
								</li>
								@endif
								@if(config('permission.shipping_charge.view'))
									<li>
										<a href="{{ route('report.shipping-charge') }}" class="{{ request()->is('report.shipping-charge') ? 'active' : '' }}"> 
											<span> Shipping Charge Report</span>
										</a>
									</li>
								@endif 
								@if(config('permission.billing_invoice.view'))
								<li>
									<a href="{{ route('report.billing-invoice') }}" class="{{ request()->is('report.billing-invoice') ? 'active' : '' }}"> 
										<span> Billing Invoice Report</span>
									</a>
								</li>
								@endif
							</ul>
						</li>
					@endif
					 
					{{-- <li>
						<a href="{{route('users.permission')}}" class="{{ request()->is('users.permission*') ? 'active' : '' }}">
							<i class="fe-box"></i>
							<span> Staff Permission </span>
						</a>
					</li>
					 
					  
					<li class="head-sub toper-line">
						<h6>Tools</h6>
					</li>
					<li>
						<a href="javascript: void(0);">
							<i class="fe-briefcase"></i>
							<span> Setup & Tools </span>
							<span class="menu-arrow"></span>
						</a>
						<ul class="nav-second-level" aria-expanded="false">
							<li><a href="{{ route('rate.calculator') }}"> Rate Calculator </a></li>  
							<li><a href="{{ route('product-category') }}"> Product Category </a></li>
							<li><a href="{{ route('shipment.packaging') }}"> Packaging </a></li>
						</ul>
					</li> --}} 
					
					@if(config('permission.general_setting.view') || config('permission.shipping_company.view')) 
						<li>
							<a href="javascript: void(0);">
								<i class="mdi mdi-cogs"></i>
								<span> Setting </span>
								<span class="menu-arrow"></span>
							</a>
							<ul class="nav-second-level" aria-expanded="false">
								@if(config('permission.general_setting.view'))	
									<li><a href="{{route('general-setting')}}"> General Setting </a></li> 
								@endif		
								@if(config('permission.shipping_company.view')) 
									<li><a href="{{route('shipping.company')}}"> Shipping Companies </a></li> 
								@endif   
								@if(config('permission.general_setting.view'))
									<li><a href="{{route('courier.commission')}}"> Courier Commission </a></li>
								@endif
								{{--<li><a href="{{route('lable.preferance')}}"> Label Preferance </a></li>--}} 
							</ul>
						</li> 
					@endif		 
				@endif		 
				<li>
					<a href="{{ route('logout') }}" onclick="event.preventDefault();
					document.getElementById('logout-form').submit();">
						<i class="mdi mdi-logout"></i>
						<span> Logout </span>
					</a>
					<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
						@csrf
					</form>
				</li> 
			</ul>
		</div>
		<!-- End Sidebar -->
		
		<div class="clearfix"></div>
		
	</div>
	<!-- Sidebar -left -->
	
</div>
<!-- Left Sidebar End -->