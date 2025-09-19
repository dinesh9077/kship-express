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
        		<li class="head-sub">
					<h6>Menu</h6>
				</li>
				@if(Auth::user()->role == "user")
					@include('layouts.backend.partial.user-sidebar')
				@else
					<li>
						<a href="{{url('home')}}" class="{{ request()->is('home*') ? 'active' : '' }}">
							<i class="mdi mdi-view-dashboard"></i>
							<span> Dashboard </span>
						</a>
					</li>
					@if(config('permission.order.view'))
						<li>
							<a href="{{ route('order') }}?weight_order=1" class="{{ request()->is(['order', 'order/create', 'order/edit*', 'order/details*', 'order/clone*', 'order/label*', 'order/tracking-history*', 'order/shipping-label*']) ? 'active' : '' }}">
								<i class="mdi mdi-package-variant-closed"></i>
								<span> Order </span>
							</a>
						</li>
						
						<!--<li class="{{ request()->is('order*') && request('weight_order') ? 'mm-active' : '' }}">
							<a href="javascript: void(0);" class="{{ request()->is('order*') && request('weight_order') ? 'active' : '' }}">
								<i class="mdi mdi-package-variant-closed"></i>
								<span> Order </span>
								<span class="menu-arrow"></span>
							</a>
							<ul class="nav-second-level {{ request()->is('order*') && request('weight_order') ? 'mm-collapse mm-show' : '' }}" aria-expanded="false">  
								<li class="{{ request()->is('order*') && request('weight_order') == 1 ? 'mm-active' : '' }}">
									<a class="{{ request()->is('order*') && request('weight_order') == 1 ? 'active' : '' }}" 
									   href="{{ route('order') }}?weight_order=1"> 
									   Lightweight Order 
									</a>
								</li> 
								<li class="{{ request()->is('order*') && request('weight_order') == 2 ? 'mm-active' : '' }}">
									<a class="{{ request()->is('order*') && request('weight_order') == 2 ? 'active' : '' }}" 
									   href="{{ route('order') }}?weight_order=2"> 
									   Heavyweight Order 
									</a>
								</li>  
							</ul>
						</li>--> 

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
					@if(config('permission.client.view'))
						<li>
							<a href="{{route('customer')}}" class="{{ request()->is('customer*') ? 'active' : '' }}">
								<i class="mdi mdi-account"></i>
								<span> Client </span>
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
					@if(config('permission.pickup_request.view'))
						<li>
							<a href="{{ route('pickup.request.index') }}" class="{{ request()->is('pickup*') ? 'active' : '' }}">
								<i class="mdi mdi-truck"></i>
								<span> Pickup Request </span>
							</a>
						</li> 
					@endif
					
					@if(config('permission.remittance.view') || config('permission.cod_voucher.view') || config('permission.cod_payout.view'))
						<li>
							<a href="javascript: void(0);">
								<i class="mdi mdi-cash-multiple"></i>
								<span> Remittance Management </span>
								<span class="menu-arrow"></span>
							</a>
							<ul class="nav-second-level" aria-expanded="false">
								@if(config('permission.remittance.view'))
									<li><a href="{{ route('order.remmitance') }}"> Remittance </a></li>  
								@endif
								@if(config('permission.cod_voucher.view'))
									<li><a href="{{ route('order.codvoucher') }}"> Cod Vochers </a></li>
								@endif
								@if(config('permission.cod_payout.view'))
									<li><a href="{{ route('order.codpayout') }}"> Cod Payout </a></li> 
								@endif
							</ul>
						</li>
					@endif	
					
					@if(config('permission.weight_descripencies.view'))
						<li>
							<a href="{{ route('weight.admin.descripencies') }}" class="{{ request()->is('descripencies*') ? 'active' : '' }}">
								<i class="mdi mdi-scale-balance"></i>
								<span> Weight Discrepancies </span>
							</a>
						</li> 
					@endif		
						
					{{--<li>
						<a href="javascript: void(0);">
							<i class="fe-briefcase"></i>
							<span> Weight Management </span>
							<span class="menu-arrow"></span>
						</a>
						<ul class="nav-second-level" aria-expanded="false">
							@if(Auth::user()->role == "admin")
								<li><a href="{{ route('weight.admin.descripencies') }}"> Weight Discrepancies </a></li>  
								<li><a href="{{ route('weight.admin.freeze')}}"> Weight Freeze </a></li>
							@else
								<li><a href="{{ route('weight.descripencies') }}"> Weight Discrepancies </a></li>  
								<li><a href="{{ route('weight.freeze')}}"> Weight Freeze </a></li>
							@endif
						</ul>
					</li> --}}
					 
					@if(config('permission.franchise_partner.view') || config('permission.franchise_partner_kyc_request.view'))
						<li>
							<a href="javascript: void(0);">
								<i class="mdi mdi-account-multiple"></i>
								<span> Franchise Partner </span>
								<span class="menu-arrow"></span>
							</a>
							<ul class="nav-second-level" aria-expanded="false">  
								@if(config('permission.franchise_partner.view'))
									<li><a href="{{ route('users') }}"> Partners </a></li>   
								@endif 
								@if(config('permission.franchise_partner_kyc_request.view'))
									<li><a href="{{ route('users.kyc.request') }}">KYC Request </a></li>  
								@endif 
							</ul>
						</li>
					@endif 
					
					{{-- <li>
							<a href="{{route('newuser')}}" class="{{ request()->is('newuser*') ? 'active' : '' }}">
								<i class="fe-box"></i>
								<span> New Client </span>
							</a>
					</li> --}}
					 
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
					<li>
						<a href="{{ route('ticket.admin') }}">
							<i class="mdi mdi-ticket-confirmation"></i>
							<span> Ticket Request</span>
						</a>
					</li>
					@endif	
					 
					{{-- <li>
							<a href="{{route('service.index')}}" class="{{ request()->is('service*') ? 'active' : '' }}">
								<i class="fe-box"></i>
								<span> Import Shipping Charges </span>
							</a>
					</li> --}} 
					
				   
					{{--<li >
							<a href="{{route('pickup')}}"  class="{{ request()->is('pickup*') ? 'active' : '' }}">
								<i class="mdi mdi-checkbox-multiple-marked"></i>
								<span>Pickup Report </span>
							</a>
					</li>--}} 
				   
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
								{{--<li>
									<a href="{{route('invoice_report')}}" class="{{ request()->is('invoice_report*') ? 'active' : '' }}"> 
										<span> Invoice </span>
									</a>
								</li>
							 
								<li>
									<a href="{{route('daily_recharge')}}" class="{{ request()->is('daily_recharge*') ? 'active' : '' }}"> 
										<span> Recharge Report </span>
									</a>
								</li>--}}
								@if(config('permission.recharge_history.view'))
								<li>
									<a href="{{route('recharge.list.history')}}" class="{{ request()->is('recharge.list.history') ? 'active' : '' }}"> 
										<span> Recharge History </span>
									</a>
								</li>
								@endif
								@if(config('permission.income_report.view'))
								<li>
									<a href="{{ route('report.income') }}" class="{{ request()->is('report.income') ? 'active' : '' }}"> 
										<span> Income Report</span>
									</a>
								</li>
								@endif
								@if(config('permission.payment_report.view'))
								<li>
									<a href="{{ route('report.payment') }}" class="{{ request()->is('report.payment') ? 'active' : '' }}"> 
										<span> Payments Report</span>
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