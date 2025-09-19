<ul class="nav nav-tabs admin mb-4">
    <h2>Settings</h2>
    <a href="<?php echo base_url('admin/business') ?>" class="btn btn-default btn-sm mb-10" style="display: <?php if(isset($page_title) && $page_title == 'Personal Information' || $page_title == 'Change Password'){echo "inline-block";}else{echo "none";} ?>"><i class="fa fa-angle-left"></i> Back</a>
    <?php if (auth('role') == 'user' || auth('role') == 'subadmin'): ?>
	    <li><a class="<?php if(isset($page_title) && $page_title == 'Business' || $page == 'Business'){echo "active";} ?>" href="<?php echo base_url('admin/business') ?>"><i class="fa fa-briefcase"></i>&nbsp; <span class=""><?php echo trans('business') ?></span></a></li>
	<?php endif; ?>
	
    <li style="display: <?php if(isset($page_title) && $page_title == 'Personal Information' || $page_title == 'Change Password'){echo "none";} ?>"><a class="<?php if(isset($page_title) && $page_title == 'General Setting'){echo "active";} ?>" href="<?php echo base_url('admin/business/general_setting') ?>"><i class="fa fa-cog"></i>&nbsp;  <span class=""><?php echo trans('general-settings') ?></span> 	</a></li>
    
	<li><a class="<?php if(isset($page_title) && $page_title == 'Personal Information'){echo "active";} ?>" href="<?php echo base_url('admin/profile') ?>"><i class="fa fa-user"></i>&nbsp;  <span class="">Manage Profile</span> 	</a></li>
	
	<li style="display: <?php if(isset($page_title) && $page_title == 'Personal Information' || $page_title == 'Change Password'){echo "block";} ?>"><a class="<?php if(isset($page_title) && $page_title == 'Change Password'){echo "active";} ?>" href="<?php echo base_url('admin/profile/change_password') ?>"><i class="fa fa-lock"></i>&nbsp;  <span class=""><?php echo trans('change-password') ?></span></a></li>
	 
	<?php if (auth('role') == 'user' || auth('role') == 'subadmin'): ?>
	<!--<li><a class="<?php if(isset($page_title) && $page_title == 'Business' || $page == 'Business'){echo "active";} ?>" href="<?php echo base_url('admin/business') ?>"><i class="fa fa-briefcase"></i>&nbsp; <span class=""><?php echo trans('business') ?></span></a></li>-->
	
	<li style="display: <?php if(isset($page_title) && $page_title == 'Personal Information' || $page_title == 'Change Password'){echo "none";} ?>"><a class="<?php if(isset($page_title) && $page_title == 'Invoice Customization'){echo "active";} ?>" href="<?php echo base_url('admin/business/invoice_customize') ?>"><i class="fa fa-paint-brush"></i>&nbsp;  <span class=""><?php echo trans('invoice-customization') ?></span></a></li>
	
	<li style="display: <?php if(isset($page_title) && $page_title == 'Personal Information' || $page_title == 'Change Password'){echo "none";} ?>"><a class="<?php if(isset($page_title) && $page_title == 'Role Permissions'){echo "active";} ?>" href="<?php echo base_url('admin/role_management/permissions') ?>"><i class="fa fa-check-circle"></i>&nbsp; <span class=""><?php echo trans('role-permissions') ?></span></a></li>
	
	<li style="display: <?php if(isset($page_title) && $page_title == 'Personal Information' || $page_title == 'Change Password' || $page_title == 'Edit'){echo "none";} ?>"><a class="<?php if(isset($page_title) && $page_title == 'Role Management'){echo "active";} ?>" href="<?php echo base_url('admin/role_management') ?>"><i class="fa fa-users"></i>&nbsp; <span class=""><?php echo trans('role-management') ?></span></a></li>
	
	<li style="display: <?php if(isset($page_title) && $page_title == 'Personal Information' || $page_title == 'Change Password'){echo "none";} ?>"><a class="<?php if(isset($page_title) && $page_title == 'Subscription'){echo "active";} ?>" href="<?php echo base_url('admin/subscription') ?>"><i class="fa fa-rocket"></i>&nbsp; <span class=""><?php echo trans('subscription') ?></span></a></li>
	
	<li style="display: <?php if(isset($page_title) && $page_title == 'Personal Information' || $page_title == 'Change Password'){echo "none";} ?>"><a class="<?php if(isset($page_title) && $page_title == 'Country'){echo "active";} ?>" href="<?php echo base_url('admin/country') ?>"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp; <span class=""><?php echo trans('country') ?></span></a></li>
	
	<li style="display: <?php if(isset($page_title) && $page_title == 'Personal Information' || $page_title == 'Change Password'){echo "none";} ?>"><a class="<?php if(isset($page_title) && $page_title == 'Tax'){echo "active";} ?>" href="<?php echo base_url('admin/tax') ?>"><i class="fa fa-percent" aria-hidden="true"></i>&nbsp; <span class=""><?php echo trans('tax') ?></span></a></li>
	
	<li style="display: <?php if(isset($page_title) && $page_title == 'Personal Information' || $page_title == 'Change Password'){echo "none";} ?>"><a class="<?php if(isset($page_title) && $page_title == 'Unit'){echo "active";} ?>" href="<?php echo base_url('admin/unit') ?>"><i class="fa fa-balance-scale" aria-hidden="true"></i>&nbsp; <span class="">Unit</span></a></li>
	
	<?php if (auth('role') == 'user' || auth('role') == 'subadmin'): ?>
    	<?php if (check_package_limit('invoice-payments') == -1): ?>
    	<li style="display: <?php if(isset($page_title) && $page_title == 'Personal Information' || $page_title == 'Change Password'){echo "none";} ?>"><a class="<?php if(isset($page_title) && $page_title == 'Payment Settings'){echo "active";} ?>" href="<?php echo base_url('admin/payment/user') ?>"><i class="fa fa-exchange" aria-hidden="true"></i>&nbsp; <span class=""><?php echo trans('payment-settings') ?></span></a></li>
    	<?php endif; ?>
	<?php endif; ?>
	
	<?php endif; ?>
	
</ul>