<div class="accordion">
    <h2>Settings <span class="buss-arrow pull-right" style="margin: 0px"><i class="icon-arrow-right"></i></span></h2>
</div>
<ul class="nav nav-tabs admin mb-4 panel">
    
    <li><a class="<?php if(isset($page_title) && $page_title == 'General Setting'){echo "active";} ?>" href="<?php echo base_url('admin/business/general_setting') ?>"><i class="fa fa-cog"></i>&nbsp;  <span class=""><?php echo trans('general-settings') ?></span> 	</a></li>
	
	<li><a class="<?php if(isset($page_title) && $page_title == 'Personal Information'){echo "active";} ?>" href="<?php echo base_url('admin/profile') ?>"><i class="fa fa-user"></i>&nbsp;  <span class="">Manage Profile</span> 	</a></li>
	
	<!--<li><a class="<?php if(isset($page_title) && $page_title == 'Change Password'){echo "active";} ?>" href="<?php echo base_url('admin/profile/change_password') ?>"><i class="fa fa-lock"></i>&nbsp;  <span class=""><?php echo trans('change-password') ?></span></a></li>-->
	
	<?php if (auth('role') == 'user' || auth('role') == 'subadmin'): ?>
	<li><a class="<?php if(isset($page_title) && $page_title == 'Business' || $page == 'Business'){echo "active";} ?>" href="<?php echo base_url('admin/business') ?>"><i class="fa fa-briefcase"></i>&nbsp; <span class=""><?php echo trans('business') ?></span></a></li>
	
	<li><a class="<?php if(isset($page_title) && $page_title == 'Invoice Customization'){echo "active";} ?>" href="<?php echo base_url('admin/business/invoice_customize') ?>"><i class="fa fa-paint-brush"></i>&nbsp;  <span class=""><?php echo trans('invoice-customization') ?></span></a></li>
	
	<li><a class="<?php if(isset($page_title) && $page_title == 'Role Permissions'){echo "active";} ?>" href="<?php echo base_url('admin/role_management/permissions') ?>"><i class="fa fa-check-circle"></i>&nbsp; <span class=""><?php echo trans('role-permissions') ?></span></a></li>
	
	<li><a class="<?php if(isset($page_title) && $page_title == 'Role Management'){echo "active";} ?>" href="<?php echo base_url('admin/role_management') ?>"><i class="fa fa-users"></i>&nbsp; <span class=""><?php echo trans('role-management') ?></span></a></li>
	
	
	<li><a class="<?php if(isset($page_title) && $page_title == 'Subscription'){echo "active";} ?>" href="<?php echo base_url('admin/subscription') ?>"><i class="fa fa-rocket"></i>&nbsp; <span class=""><?php echo trans('subscription') ?></span></a></li>
	
	
	<li><a class="<?php if(isset($page_title) && $page_title == 'Country'){echo "active";} ?>" href="<?php echo base_url('admin/country') ?>"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp; <span class=""><?php echo trans('country') ?></span></a></li>
	
	<li><a class="<?php if(isset($page_title) && $page_title == 'Tax'){echo "active";} ?>" href="<?php echo base_url('admin/tax') ?>"><i class="fa fa-percent" aria-hidden="true"></i>&nbsp; <span class=""><?php echo trans('tax') ?></span></a></li>
	
	<li><a class="<?php if(isset($page_title) && $page_title == 'Unit'){echo "active";} ?>" href="<?php echo base_url('admin/unit') ?>"><i class="fa fa-balance-scale" aria-hidden="true"></i>&nbsp; <span class="">Unit</span></a></li> 
	
	<?php if (auth('role') == 'user' || auth('role') == 'subadmin'): ?>
	<?php if (check_package_limit('invoice-payments') == -1): ?>
	<li><a class="<?php if(isset($page_title) && $page_title == 'Payment Settings'){echo "active";} ?>" href="<?php echo base_url('admin/payment/user') ?>"><i class="fa fa-exchange" aria-hidden="true"></i>&nbsp; <span class=""><?php echo trans('payment-settings') ?></span></a></li>
	<?php endif; ?>
	<?php endif; ?>
	
	<?php endif; ?>
	
</ul>


<style>
	.accordion {
	background-color: #ffe3d285;
    color: #444;
    cursor: pointer;
    padding: 0 15px;
    width: 100%;
    border: none;
    text-align: left;
    outline: none;
    font-size: 15px;
    transition: 0.4s;
    border-radius: 25px;
	}
	
	.active, .accordion:hover {
	background-color: #136acd33;
	}
	
	.accordion .h2:after {
	content: '\002B';
	color: #777;
	font-weight: bold;
	float: right;
	margin-left: 5px;
	}
	
	.active .h2:after {
	content: "\2212";
	}
	
	.panel {
	padding: 0 18px;
	background-color: white;
	max-height: 0;
	overflow: hidden;
	transition: max-height 0.2s ease-out;
	}
</style>


<script>
	var acc = document.getElementsByClassName("accordion");
	var i;
	
	for (i = 0; i < acc.length; i++) {
		acc[i].addEventListener("click", function() {
			this.classList.toggle("active");
			var panel = this.nextElementSibling;
			if (panel.style.maxHeight) {
				panel.style.maxHeight = null;
				} else {
				panel.style.maxHeight = panel.scrollHeight + "px";
			} 
		});
	}
</script>


