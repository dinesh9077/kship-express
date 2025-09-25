<li>
	<a href="{{ url('home') }}" class="{{ request()->is('home*') ? 'active' : '' }}">
		<i class="mdi mdi-view-dashboard"></i>
		<span> Dashboard </span>
	</a>
</li>

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
<li>
    <a href="{{ route('order.bulk-create') }}" class="{{ request()->is('order/bulk-create') ? 'active' : '' }}">
        <i class="mdi mdi-cart-plus"></i>
        <span> Bulk Order </span>
    </a>
</li>

<li>
	<a href="{{ route('rate.calculator') }}"  class="{{ request()->is('calculator*') ? 'active' : '' }}">
		<i class="mdi mdi-calculator"></i>
		<span> Rate Calculator </span>
	</a>
</li>

<!--<li>
	<a href="{{ route('ndr.order') }}" >
		<i class="mdi mdi-alert-circle-outline"></i>
		<span> NDR Order</span>
	</a>
</li>-->
						
<li>
	<a href="{{route('customer')}}" class="{{ request()->is('customer*') ? 'active' : '' }}">
		<i class="mdi mdi-account"></i>
		<span> Recipeint/Customer </span>
	</a>
</li>

<li>
	<a href="{{ route('warehouse.index') }}" class="{{ request()->is('warehouse*') ? 'active' : '' }}">
		<i class="mdi mdi-home-variant"></i>
		<span> Add Warehouse </span>
	</a>
</li>

{{--<li>
	<a href="{{ route('pickup.request.index') }}" class="{{ request()->is('pickup*') ? 'active' : '' }}">
		<i class="mdi mdi-truck"></i>
		<span> Pickup Request </span>
	</a>
</li>--}} 
  
<li>
	<a href="{{route('users.kyc.edit')}}" class="{{ request()->is('users/kyc/edit') ? 'active' : '' }}">
		<i class="mdi mdi-account-check"></i>
		<span> KYC Update </span>
	</a>
</li>
 
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
<li>
	<a href="{{ route('weight.descripencies') }}" class="{{ request()->is('descripencies*') ? 'active' : '' }}">
		<i class="mdi mdi-scale-balance"></i>
		<span> Weight Discrepancies </span>
	</a>
</li> 

<li class="{{ request()->is('ticket*') ? 'mm-active' : '' }}">
	<a href="{{ route('ticket') }}" class="{{ request()->is('ticket*') ? 'active' : '' }}">
		<i class="mdi mdi-ticket"></i>
		<span> Ticket Request</span>
	</a>
</li> 

<li> 
	<a href="javascript: void(0);">
		<i class="mdi mdi-file-chart"></i>
		<span> Report </span>
		<span class="menu-arrow"></span>
	</a>
	<ul class="nav-second-level" aria-expanded="false"> 
		<li>
			<a href="{{ route('report.order') }}" class="{{ request()->is('report.order') ? 'active' : '' }}"> 
				<span>Order Report</span>
			</a>
		</li>  
		<li>
			<a href="{{route('report.passbook')}}" class="{{ request()->is('passbook*') ? 'active' : '' }}"> 
				<span> Passbook </span>
			</a>
		</li>  
		<li>
			<a href="{{route('recharge.list')}}" class="{{ request()->is('recharge.list') ? 'active' : '' }}"> 
				<span> Recharge History </span>
			</a>
		</li> 
		<li>
			<a href="{{ route('report.billing-invoice') }}" class="{{ request()->is('report.billing-invoice') ? 'active' : '' }}"> 
				<span> Billing Invoice Report</span>
			</a>
		</li> 
	</ul>
 </li>