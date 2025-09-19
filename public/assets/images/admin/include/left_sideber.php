<?php if (isset($page_title) && $page_title != 'Online Payment') : ?>

	<aside class="main-sidebar">
		<section class="sidebar mt-10">
			<!-- <img src="<?php echo base_url($settings->logo) ?>" alt="logo"> -->
			<ul class="sidebar-menu" data-widget="tree">

				<?php if ($this->admin_model->get_user_info() == TRUE) {
					$uval = 'd-block';
				} else {
					$uval = 'd-none';
				} ?>

				<?php if (is_admin()) : ?>
					<li class="<?php if (isset($page_title) && $page_title == "Dashboard") {
						echo "active";
					} ?>">
					<a href="<?php echo base_url('admin/dashboard') ?>">
						<i class="flaticon-home-2"></i> <span><?php echo trans('dashboard') ?></span>
					</a>
				</li>

				<li class="treeview <?php if (isset($main_page) && $main_page == "Settings") {
					echo "active";
				} ?>">
				<a href="#"><i class="flaticon-settings-1"></i>
					<span><?php echo trans('settings') ?></span>
					<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
				</a>

				<ul class="treeview-menu">
					<li class="<?php if (isset($page_title) && $page_title == "Settings") {
						echo "active";
					} ?>">
					<a href="<?php echo base_url('admin/settings') ?>">
						<i class="flaticon-layout"></i> <span><?php echo trans('website-settings') ?></span>
					</a>
				</li>

				<li class="<?php if (isset($page_title) && $page_title == "Appearance") {
					echo "active";
				} ?>">
				<a href="<?php echo base_url('admin/settings/appearance') ?>">
					<i class="flaticon-ui"></i> <span><?php echo trans('appearance') ?></span>
				</a>
			</li>

			<li class="<?php if (isset($page_title) && $page_title == "Preferences") {
				echo "active";
			} ?>">
			<a href="<?php echo base_url('admin/settings/preferences') ?>">
				<i class="flaticon-intelligent"></i> <span><?php echo trans('preferences') ?></span>
			</a>
		</li>

		<li class="<?php if (isset($page_title) && $page_title == "Payment Settings") {
			echo "active";
		} ?> <?= $uval; ?>">
		<a href="<?php echo base_url('admin/payment') ?>">
			<i class="flaticon-save-money"></i> <span><?php echo trans('payment-settings') ?></span>
		</a>
	</li>

	<li class="<?php if (isset($page_title) && $page_title == "License") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/settings/license') ?>">
		<i class="fa fa-key mr-0"></i> <span class="ml--3"><?php echo trans('license') ?></span>
	</a>
</li>

<li class="<?php if (isset($page_title) && $page_title == "Discounts") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/discount') ?>">
	<i class="flaticon-tax"></i> <span><?php echo trans('discount') ?></span>
</a>
</li>

<li class="<?php if (isset($page_title) && $page_title == "Categories") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/business/categories') ?>">
	<i class="flaticon-menu-1"></i> <span><span><?php echo trans('business') . ' ' . trans('categories') ?></span>
</a>
</li>
</ul>
</li>

<li class="<?php if (isset($page_title) && $page_title == "Language") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/language') ?>" class="waves-effect"><i class="flaticon-accept"></i> <span><?php echo trans('testimonials') ?> <?php echo trans('language') ?> </span> </a>
</li>

<li class="treeview <?php if (isset($main_page) && $main_page == "User Management") {
	echo "active";
} ?>">
<a href="#"><i class="flaticon-group"></i>
	<span>User Management</span>
	<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
</a>

<ul class="treeview-menu">
	<li class="<?php if (isset($page_title) && $page_title == "Users") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/users') ?>">
		<i class="flaticon-group"></i> <span><?php echo trans('users') ?></span>
	</a>
</li>
<li class="<?php if (isset($page_title) && $page_title == "Landing Users") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/users/landing') ?>">
	<i class="flaticon-group"></i> <span>Landing <?php echo trans('users') ?></span>
</a>
</li>
<li class="<?php if (isset($page_title) && $page_title == "Visitor") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/users/visitor') ?>">
	<i class="flaticon-group"></i> <span>Vistors</span>
</a>
</li>
<li class="<?php if (isset($page_title) && $page_title == "Payment History") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/users/payment_history') ?>">
	<i class="flaticon-group"></i> <span>Payment History</span>
</a>
</li>
<li class="<?php if (isset($page_title) && $page_title == "Login History") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/users/login_history') ?>">
	<i class="flaticon-group"></i> <span>Login History</span>
</a>
</li>
</ul>
</li>
<li class="<?php if (isset($page_title) && $page_title == "Package") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/package') ?>">
	<i class="flaticon-box-1"></i> <span><?php echo trans('pricing-package') ?></span>
</a>
</li>

<li class="<?php if (isset($page_title) && $page_title == "Feature") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/feature') ?>">
	<i class="flaticon-feature"></i> <span><?php echo trans('features') ?></span>
</a>
</li>

<li class="<?php if (isset($page_title) && $page_title == "Pages") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/pages') ?>">
	<i class="flaticon-document"></i> <span><?php echo trans('pages') ?></span>
</a>
</li>

<li class="<?php if (isset($page_title) && $page_title == "Faqs") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/faq') ?>">
	<i class="flaticon-info"></i> <span><?php echo trans('faqs') ?></span>
</a>
</li>

<li class="<?php if (isset($page_title) && $page_title == "Testimonial") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/testimonial') ?>">
	<i class="flaticon-rating"></i> <span><?php echo trans('testimonial') ?></span>
</a>
</li>

<li class="<?php if (isset($page_title) && $page_title == "Contact") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/contact') ?>">
	<i class="flaticon-hired"></i> <span><?php echo trans('contact') ?></span>
</a>
</li>

<li class="treeview <?php if (isset($page) && $page == "Blog") {
	echo "active";
} ?>">
<a href="#"><i class="flaticon-blogging-1"></i>
	<span><?php echo trans('blog') ?></span>
	<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
</a>
<ul class="treeview-menu">
	<li class="<?php if (isset($page_title) && $page_title == "Blog Category") {
		echo "active";
	} ?>"><a href="<?php echo base_url('admin/blog_category') ?>"><?php echo trans('add-category') ?> </a></li>
	<li class="<?php if (isset($page_title) && $page_title == "Blog Posts") {
		echo "active";
	} ?>"><a href="<?php echo base_url('admin/blog') ?>"><?php echo trans('blog-posts') ?></a></li>
</ul>
</li>
<li class="treeview <?php if (isset($page) && $page == "Support Ticket") {
	echo "active";
} ?>"> <a href="#"><i class="flaticon-feature"></i> <span>Customer Support</span>
	<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
</a>
<ul class="treeview-menu">

	<li class="<?php if (isset($page_title) && $page_title == "All Ticket") {
		echo "active";
	} ?>"><a href="<?php echo base_url('admin/ticket/admin_ticket') ?>">Ticket</a></li>

	<li class="<?php if (isset($page_title) && $page_title == "Category") {
		echo "active";
	} ?>"><a href="<?php echo base_url('admin/ticket/category') ?>"><?php echo trans('category') ?> </a></li>

	<li class="<?php if (isset($page_title) && $page_title == "Sub Category") {
		echo "active";
	} ?>"><a href="<?php echo base_url('admin/ticket/sub_category') ?>">Sub Category</a></li>

</ul>
</li>
<li class="<?php if (isset($page_title) && $page_title == "Send Notification") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/contact/notification') ?>">
	<i class="flaticon-hired"></i> <span>Send Notification</span>
</a>
</li>
<?php else : ?>

	<li class="<?php if (isset($page_title) && $page_title == "User Dashboard") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/dashboard/business') ?>">
		<img class="left-icon" src="<?php echo base_url('assets/admin/dashboardi.png') ?>"> <span><?php echo trans('dashboard') ?></span>
	</a>
</li>

<?php if (check_payment_status() == TRUE || settings()->enable_paypal == 0 || user()->user_type == 'trial') : ?>
<li class="treeview <?php if (isset($page) && $page == "Banking") {
	echo "active";
} ?>">
<a href="#">
	<svg style="margin-right: 8px;" height="20" viewBox="0 0 24 26" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M0 14H2.66667V26H0V14ZM21.3333 8.66663H24V26H21.3333V8.66663ZM10.6667 0.666626H13.3333V26H10.6667V0.666626Z"/>
	</svg>
	<span>Banking</span>
	<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
</a>
<ul class="treeview-menu">
	<li class="<?php if (isset($page_title) && $page_title == "Bank List") {
		echo "active";
	} ?>"><a href="<?php echo base_url('admin/banking') ?>"><i class="flaticon-bank" aria-hidden="true"></i>&nbsp;Bank/Cash</a></li>
	<li class="<?php if (isset($page_title) && $page_title == "Loan System") {
		echo "active";
	} ?>"><a href="<?php echo base_url('admin/banking/loan_system') ?>"><i class="flaticon-bank" aria-hidden="true"></i>&nbsp;Loan System</a></li>
	<li class="<?php if (isset($page_title) && $page_title == "Bank Transfer") {
		echo "active";
	} ?>"><a href="<?php echo base_url('admin/banking/transfer') ?>"><i class="flaticon-credit-card" aria-hidden="true"></i>&nbsp;Bank To Bank Transfer</a></li>
	<li class="<?php if (isset($page_title) && $page_title == "All Transaction") {
		echo "active";
	} ?>"><a href="<?php echo 
	base_url('admin/banking/transaction_list') ?>"><i class="flaticon-credit-card" aria-hidden="true"></i>&nbsp;All Transaction List</a></li>
</ul>
</li>


<li class="treeview <?php if (isset($main_page) && $main_page == "Sales") {
	echo "active";
} ?>">

<a href="#"><svg style="margin-right: 11px;" height="20" viewBox="0 0 24 29" xmlns="http://www.w3.org/2000/svg">
	<path d="M21.3333 7.00004H18.6667C18.6667 3.26671 15.7333 0.333374 12 0.333374C8.26667 0.333374 5.33333 3.26671 5.33333 7.00004H2.66667C1.2 7.00004 0 8.20004 0 9.66671V25.6667C0 27.1334 1.2 28.3334 2.66667 28.3334H21.3333C22.8 28.3334 24 27.1334 24 25.6667V9.66671C24 8.20004 22.8 7.00004 21.3333 7.00004ZM12 3.00004C14.2667 3.00004 16 4.73337 16 7.00004H8C8 4.73337 9.73333 3.00004 12 3.00004ZM21.3333 25.6667H2.66667V9.66671H21.3333V25.6667ZM12 15C9.73333 15 8 13.2667 8 11H5.33333C5.33333 14.7334 8.26667 17.6667 12 17.6667C15.7333 17.6667 18.6667 14.7334 18.6667 11H16C16 13.2667 14.2667 15 12 15Z"/>
</svg>

<span><?php echo trans('sales') ?></span>
<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
</a>

<ul class="treeview-menu">

	<?php if (check_permissions(auth('role'), 'customers') == TRUE) : ?>
		<li class="<?php if (isset($page_title) && $page_title == "Customers") {
			echo "active";
		} ?>">
		<a href="<?php echo base_url('admin/customer') ?>">
			<i class="flaticon-network"></i> <span><?php echo trans('customers') ?></span>
		</a>
	</li>
<?php endif; ?>

<?php if (check_permissions(auth('role'), 'products') == TRUE) : ?>
	<li class="<?php if (isset($page_title) && $page_title == "Products") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/product/all/sell') ?>">
		<i class="flaticon-box-1"></i> <span>Stocks & Services</span>
	</a>
</li>
<?php endif; ?>

<?php
if (check_permissions(auth('role'), 'estimates') == TRUE) :
	if ($this->business->estimate_quatation == 1) :
		?>
		<li class="<?php if (isset($page_title) && $page_title == "Estimate") {
			echo "active";
		} ?>">
		<a href="<?php echo base_url('admin/estimate') ?>">
			<i class="flaticon-contract"></i> <span><?php echo trans('estimates') ?></span>
		</a>
	</li>
	<?php
endif;
endif;
?>

<?php if (check_permissions(auth('role'), 'income') == TRUE) : ?>
	<li class="<?php if (isset($page_title) && $page_title == "Income") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/income') ?>">
		<i class="flaticon-contract"></i> <span>Income</span>
	</a>
</li>
<?php endif; ?>
<?php if ($this->business->delivery_challan == 1) : ?>
	<li class="<?php if (isset($page_title) && $page_title == "Delivery") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/delivery') ?>">
		<i class="flaticon-approve-invoice"></i> <span>Delivery Challan</span>
	</a>
</li>
<?php endif; ?>
<?php if (check_permissions(auth('role'), 'invoices') == TRUE) : ?>
	<?php if ($this->business->sale_invoice == 1) : ?>
		<li class="<?php if (isset($page_title) && $page_title == "Invoices") {
			echo "active";
		} ?>">
		<a href="<?php echo base_url('admin/invoice/type/1') ?>">
			<i class="flaticon-approve-invoice"></i> <span><?php echo trans('invoices') ?></span>
		</a>
	</li>
<?php endif; ?>
<li class="<?php if (isset($page_title) && $page_title == "Create Recurring Invoice") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/invoice/create/1') ?>">
	<i class="flaticon-iterative"></i> <span><?php echo trans('recurring-invoice') ?> </span>
</a>
</li>
<li class="<?php if (isset($page_title) && $page_title == "Sale Return List") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/invoice/sale_return_list') ?>">
	<i class="flaticon-approve-invoice"></i> <span>Sale Return / Cr Note</span>
</a>
</li>
<?php endif; ?>

</ul>
</li>

<li class="treeview <?php if (isset($main_page) && $main_page == "Purchases") {
	echo "active";
} ?>">

<a 	href="#"><svg style="margin-right: 8px;" height="20" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
	<path d="M4 4H6.66667L7.2 6.66667M9.33333 17.3333H22.6667L28 6.66667H7.2M9.33333 17.3333L7.2 6.66667M9.33333 17.3333L6.27614 20.3905C5.43619 21.2305 6.03108 22.6667 7.21895 22.6667H22.6667M22.6667 22.6667C21.1939 22.6667 20 23.8606 20 25.3333C20 26.8061 21.1939 28 22.6667 28C24.1394 28 25.3333 26.8061 25.3333 25.3333C25.3333 23.8606 24.1394 22.6667 22.6667 22.6667ZM12 25.3333C12 26.8061 10.8061 28 9.33333 28C7.86057 28 6.66667 26.8061 6.66667 25.3333C6.66667 23.8606 7.86057 22.6667 9.33333 22.6667C10.8061 22.6667 12 23.8606 12 25.3333Z" stroke="#252525" fill="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

<span><?php echo trans('purchases') ?></span>
<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
</a>
<ul class="treeview-menu">
	<li class="<?php if (isset($page_title) && $page_title == "Vendor") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/vendor') ?>">
		<i class="flaticon-group"></i> <span><?php echo trans('vendors') ?></span>
	</a>
</li>

<?php if (check_permissions(auth('role'), 'products') == TRUE) : ?>
	<li class="<?php if (isset($page_title) && $page_title == "Products") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/product/all/buy') ?>">
		<i class="flaticon-box-1"></i> <span>Stocks & Services</span>
	</a>
</li>
<?php endif; ?>

<?php if (check_permissions(auth('role'), 'expenses') == TRUE) : ?>
	<li class="<?php if (isset($page_title) && $page_title == "Expense") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/expense') ?>">
		<i class="flaticon-bill"></i> <span><?php echo trans('expense') ?></span>
	</a>
</li>
<?php endif; ?>

<?php if (check_permissions(auth('role'), 'bills') == TRUE) : ?>
	<?php if ($this->business->purchase_bill == 1) : ?>
		<li class="<?php if (isset($page_title) && $page_title == "Bills") {
			echo "active";
		} ?>">
		<a href="<?php echo base_url('admin/bills') ?>">
			<i class="flaticon-credit-card"></i> <span><?php echo trans('bills') ?></span>
		</a>
	</li>
<?php endif; ?>
<?php endif; ?>

<?php if (check_permissions(auth('role'), 'bills') == TRUE) : ?>
	<li class="<?php if (isset($page_title) && $page_title == "Return Bills") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/bills/return_bills_list') ?>">
		<i class="flaticon-credit-card"></i> <span>Bills Return / Dr Note</span>
	</a>
</li>
<?php endif; ?>

</ul>
</li>

<?php if (auth('role') == 'user' || auth('role') == 'subadmin') : ?>
<li class="<?php if (isset($page_title) && $page_title == "Category") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/category') ?>">
	<svg style="margin-right: 7px;" height="20" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M4 9.33335V22.6667C4 24.1394 5.19391 25.3334 6.66667 25.3334H25.3333C26.8061 25.3334 28 24.1394 28 22.6667V12C28 10.5273 26.8061 9.33335 25.3333 9.33335H17.3333L14.6667 6.66669H6.66667C5.19391 6.66669 4 7.86059 4 9.33335Z" stroke="#252525" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="#fff"/>
	</svg>
	<span><?php echo trans('categories') ?></span>
</a>
</li>
<?php endif; ?>
<?php if (check_permissions(auth('role'), 'reports') == TRUE) : ?>
	<li class="<?php if (isset($page_title) && $page_title == "Reports" || $page_title == "Profit & Loss" || $page_title == "Sales Tax" || $page_title == "Customers" || $page_title == "Vendors" || $page_title == "Reports") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/reports/allreports') ?>">
		<i class="icon-pie-chart"></i> <span><?php echo trans('reports') ?></span>
	</a>
</li>
<?php endif ?>


<?php endif ?>



<?php endif; ?>


<?php if (is_admin()) : ?>
	<li class="<?php if (isset($page_title) && $page_title == "Change Password") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('change_password') ?>">
		<i class="flaticon-lock-1"></i> <span><?php echo trans('change-password') ?></span>
	</a>
</li>
<?php endif; ?>

<?php if (is_admin()) : ?>
	<li class="">
		<a href="<?php echo base_url('auth/logout') ?>">
			<i class="flaticon-exit"></i> <span>Logout</span>
		</a>
	</li>
<?php endif; ?>

<?php if (is_admin()) : ?>
	<?php if (file_exists(APPPATH . 'controllers/addons/Razorpay.php')) : ?>
		<li class="treeview <?php if (isset($main_page) && $main_page == "Addons") {
			echo "active";
		} ?> d-none">
		<a href="#" class=""><i class="flaticon-favorites"></i>
			<span><?php echo trans('addons') ?></span>
			<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
		</a>
		<ul class="treeview-menu">
			<li class="<?php if (isset($page_title) && $page_title == "Razorpay") {
				echo "active";
			} ?>"><a href="<?php echo base_url('addons/razorpay') ?>">Razorpay </a></li>
		</ul>
	</li>
<?php endif ?>
<?php endif; ?>



<?php if (check_payment_status() == TRUE || settings()->enable_paypal == 0 || user()->user_type == 'trial') : ?>

<?php if (auth('role') == 'user' || auth('role') == 'subadmin') : ?>
<hr style="border: 1px solid #d4dde3; width: 85%">
<li class="treeview <?php if (isset($page) && $page == "Tickets") {
	echo "active";
} ?>">
<a href="#"><svg style="margin-right: 7px;" height="20" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
	<path d="M27 16C27 22.0751 22.0751 27 16 27V29C23.1797 29 29 23.1797 29 16H27ZM16 27C9.92487 27 5 22.0751 5 16H3C3 23.1797 8.8203 29 16 29V27ZM5 16C5 9.92487 9.92487 5 16 5V3C8.8203 3 3 8.8203 3 16H5ZM16 5C22.0751 5 27 9.92487 27 16H29C29 8.8203 23.1797 3 16 3V5ZM20.3333 16C20.3333 18.3932 18.3932 20.3333 16 20.3333V22.3333C19.4978 22.3333 22.3333 19.4978 22.3333 16H20.3333ZM16 20.3333C13.6068 20.3333 11.6667 18.3932 11.6667 16H9.66667C9.66667 19.4978 12.5022 22.3333 16 22.3333V20.3333ZM11.6667 16C11.6667 13.6068 13.6068 11.6667 16 11.6667V9.66667C12.5022 9.66667 9.66667 12.5022 9.66667 16H11.6667ZM16 11.6667C18.3932 11.6667 20.3333 13.6068 20.3333 16H22.3333C22.3333 12.5022 19.4978 9.66667 16 9.66667V11.6667ZM23.7782 6.80761L19.0641 11.5217L20.4783 12.9359L25.1924 8.22183L23.7782 6.80761ZM19.0641 20.4783L23.7782 25.1924L25.1924 23.7782L20.4783 19.0641L19.0641 20.4783ZM12.9359 11.5217L8.22183 6.80761L6.80761 8.22183L11.5217 12.9359L12.9359 11.5217ZM11.5217 19.0641L6.80761 23.7782L8.22183 25.1924L12.9359 20.4783L11.5217 19.0641Z" fill="#252525"/>
</svg>

<span>Customer Support</span>
<span class="pull-right-container"><i class="fa fa-angle-right pull-right d-none"></i></span>
</a>
<ul class="treeview-menu">

	<li class="<?php if (isset($page_title) && $page_title == "All Tickets") {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/ticket/view_all_ticket') ?>">
		<span><i class="fa fa-ticket" style="font-weight: 400"></i>Ticket</span>
	</a>
</li>
<li class="<?php if (isset($page_title) && $page_title == "Callback Request") {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/ticket/callback_request') ?>">
	<span><i class="fa fa-rocket" style="font-weight: 400"></i>Callback Request</span>
</a>
</li>

</ul>
</li>

<li class="setting_web">
	<a href="<?php echo base_url('admin/business') ?>" style="font-size: 16px">
		<img class="left-icon" src="<?php echo base_url('assets/admin/setting.png') ?>"> <span><?php echo trans('settings') ?></span>
	</a>
</li>

<li class="treeview setting_mobile<?php if (isset($page_title) && $page_title == "Profile") {
	echo "active";
} ?>">

<a href="#">
	<span><?php echo trans('settings') ?></span>
	<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
</a>
<ul class="treeview-menu">

	<li class="<?php if (isset($page_title) && $page_title == 'Personal Information') {
		echo "active";
	} ?>">

	<a href="<?php echo base_url('admin/profile') ?>">
		<span><i class="fa fa-cog" style="font-weight: 400"></i><?php echo trans('general-settings') ?></span>
	</a>
</li>

<li class="<?php if (isset($page_title) && $page_title == 'Change Password') {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/profile/change_password') ?>">
	<span class=""><i class="fa fa-lock" style="font-weight: 400"></i><?php echo trans('change-password') ?></span>
</a>
</li>

<?php if (auth('role') == 'user' || auth('role') == 'subadmin') : ?>
<li class="<?php if (isset($page_title) && $page_title == 'Business' || $page == 'Business') {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/business') ?>">
	<span class=""><i class="fa fa-briefcase" style="font-weight: 400"></i><?php echo trans('business') ?></span>
</a>
</li>

<li class="<?php if (isset($page_title) && $page_title == 'Invoice Customization') {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/business/invoice_customize') ?>">
	<span class=""><i class="fa fa-paint-brush" style="font-weight: 400"></i><?php echo trans('invoice-customization') ?></span>
</a>
</li>

<li class="<?php if (isset($page_title) && $page_title == 'Role Permissions') {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/role_management/permissions') ?>">
	<span class=""><i class="fa fa-check-circle" style="font-weight: 400"></i><?php echo trans('role-permissions') ?></span>
</a>
</li>

<li class="<?php if (isset($page_title) && $page_title == 'Role Management') {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/role_management') ?>">
	<span><i class="fa fa-users" style="font-weight: 400"></i><?php echo trans('role-management') ?></span>
</a>
</li>
<li class="<?php if (isset($page_title) && $page_title == 'Subscription') {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/subscription') ?>">
	<span><i class="fa fa-users" style="font-weight: 400"></i><?php echo trans('subscription') ?></span>
</a>
</li>
<li class="<?php if (isset($page_title) && $page_title == 'Country') {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/country') ?>">
	<span><i class="fa fa-flag" aria-hidden="true" style="font-weight: 400"></i><?php echo trans('country') ?></span>
</a>
</li>
<li class="<?php if (isset($page_title) && $page_title == 'Tax') {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/tax') ?>">
	<span><i class="fa fa-percent" aria-hidden="true" style="font-weight: 400"></i><?php echo trans('tax') ?></span>
</a>
</li>
<li class="<?php if (isset($page_title) && $page_title == 'Unit') {
	echo "active";
} ?>">
<a href="<?php echo base_url('admin/unit') ?>">
	<span><i class="fa fa-balance-scale" aria-hidden="true" style="font-weight: 400"></i>Unit</span>
</a>
</li>

<?php if (auth('role') == 'user' || auth('role') == 'subadmin') : ?>
<?php if (check_package_limit('invoice-payments') == -1) : ?>
	<li class="<?php if (isset($page_title) && $page_title == 'Payment Settings') {
		echo "active";
	} ?>">
	<a href="<?php echo base_url('admin/payment/user') ?>">
		<span><i class="fa fa-exchange" aria-hidden="true" style="font-weight: 400"></i><?php echo trans('payment-settings') ?></span>
	</a>
</li>
<?php endif ?>
<?php endif ?>
<?php endif ?>
</ul>
</li>

<li class="">
	<a href="<?php echo base_url('auth/logout') ?>" style="font-size: 16px">
		<img class="left-icon" src="<?php echo base_url('assets/admin/out.png') ?>"> <span><?php echo trans('logout') ?></span>
	</a>
</li>
<?php endif ?>

<?php endif; ?>


</ul>

<?php if (is_admin()) : ?>
	&nbsp;&nbsp;<a href="javascript:void(0);" class="btn btn-info upgrade_version" style="color: white !important">
		<i class="fa fa-info-circle"></i> <span><?php echo trans('version') ?> <?php echo html_escape(settings()->version) ?></span>
	</a>
<?php else : ?>
	&nbsp;&nbsp;<a href="<?php echo base_url('admin/subscription') ?>" class="btn btn-info" style="color: white !important;display: none;">
		<i class="fa fa-rocket"></i> <span><?php echo trans('upgrade') ?></span>
	</a>
<?php endif; ?>



<a href="<?php echo base_url('admin/ticket/callback_request') ?>">				
	<div class="upgrad_sec text-center vert-move">
		<img src="<?php echo base_url('assets/admin/Group.png') ?>">
		<h5>Upgrade to PRO</h5>
		<p>Give your money
		awesome space in Accountieons</p>
		<button>Upgrade to Premium</button>
	</div>
</a>

</section>
</aside>


<?php endif ?>