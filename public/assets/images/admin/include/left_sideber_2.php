<?php if (isset($page_title) && $page_title != 'Online Payment') : ?>
	<header class="main-header">
		<?php if (is_admin()) : ?>
			<a target="_blank" href="<?php echo base_url() ?>" class="switch_businesss logo text-centers">
				<span class="logo-lg">
					<img width="60px" class="mr-5" src="<?php echo base_url($settings->favicon) ?>" alt="<?php echo html_escape($settings->site_name); ?>"> <span class="ml-20"><?php echo html_escape($settings->site_name); ?></span>
				</span>
			</a>
		<?php else : ?>
			<?php if (!is_admin() && auth('role') != 'viewer') { ?>
				<a href="#" class="switch_business logo text-centers">
					<span class="logo-lg">
						<img width="40px" src="<?php echo (!empty(user()->image)) ? base_url(user()->image) : base_url("assets/images/avatar.png"); ?>" alt="<?php echo html_escape($settings->site_name); ?>" style="width: 45px; height: 45px; border-radius: 50%">
						<span><?php echo html_escape($this->business->name); ?> </span>
					</span>
					<span class="buss-arrow pull-right"><i class="icon-arrow-right"></i></span>
				</a>
			<?php } else { ?>
				<a href="#" class="switch_business logo text-centers">
					<span class="logo-lg">
						<img width="40px" src="<?php echo base_url($settings->favicon) ?>" alt="<?php echo html_escape($settings->site_name); ?>" style="width: 45px; height: 45px; border-radius: 50%">
						<span><?php echo html_escape($this->business->name); ?> </span>
					</span>
					<span class="buss-arrow pull-right"><i class="icon-arrow-right"></i></span>
				</a>
			<?php } ?>
			<div class="business_switch_panel animate-ltr" style="display: none;">
				<div class="buss_switch_panel_header">
					<img width="30px" src="<?php echo base_url($settings->favicon) ?>" alt="<?php echo html_escape($settings->site_name); ?>">
					<span class="acc">Your <?php echo html_escape($settings->site_name); ?> <?php echo trans('accounts') ?></span>
					<span class="business_close pull-<?php echo ($settings->dir == 'rtl') ? 'left' : 'right'; ?>">×</span>
				</div>

				<div class="buss_switch_panel_body">
					<ul class="switcher_business_menu pb-20">
						<?php foreach (get_my_business() as $mybuss) : ?>
							<li class="business_menu_item <?php if ($this->business->uid == $mybuss->uid) {
																echo "default";
															} ?>">
								<a class="business_menu_item_link" href="<?php echo base_url('admin/profile/switch_business/' . $mybuss->uid) ?>">
									<span class="business-menu_item_label">
										<?php echo $mybuss->name ?>
										<?php if ($this->business->uid == $mybuss->uid) : ?>
											<span class="is_default pull-right"><i class="flaticon-checked text-success"></i></span>
										<?php endif ?>
									</span>
								</a>
							</li>
						<?php endforeach ?>
					</ul>

					<div class="seperater"></div>

					<?php if (auth('role') == 'user' || auth('role') == 'subadmin') : ?>
						<a class="new_business_link" href="<?php echo base_url('admin/business') ?>"><i class="icon-briefcase"></i> <span><?php echo trans('manage-business') ?></span></a>

						<a class="new_business_link" href="<?php echo base_url('admin/role_management') ?>"><i class="icon-people"></i> <span><?php echo trans('manage-users') ?></span></a>

						<a class="new_business_link" href="<?php echo base_url('admin/business/invoice_customize') ?>"><i class="fa fa-paint-brush"></i> <span><?php echo trans('invoice-customization') ?></span></a>
					<?php endif; ?>

					<a class="new_business_link" href="<?php echo base_url('admin/profile') ?>"><i class="flaticon-user-1"></i> <span><?php echo trans('manage-profile') ?></span></a>

					<a class="new_business_link" href="<?php echo base_url('auth/logout') ?>"><i class="icon-logout"></i> <span><?php echo trans('sign-out') ?></span></a>
				</div>

				<div class="buss_switch_panel_footer">

				</div>
			</div>
		<?php endif; ?>

		<nav class="navbar navbar-static-top hidden-md">
			<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
				<span class="sr-only">Toggle navigation</span>
			</a>
		</nav>

	</header>

	<aside class="main-sidebar">
		<section class="sidebar mt-10">
			<img src="https://accountieons.com/uploads/medium/acc_medium-400x81.png">
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

					<li class="<?php if (isset($page_title) && $page_title == "Users") {
									echo "active";
								} ?>">
						<a href="<?php echo base_url('admin/users') ?>">
							<i class="flaticon-group"></i> <span><span><?php echo trans('users') ?></span>
						</a>
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
										} ?>">
						<a href="#"><i class="flaticon-feature"></i>
							<span>Customer Support</span>
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

				<?php else : ?>

					<li class="<?php if (isset($page_title) && $page_title == "User Dashboard") {
									echo "active";
								} ?>">
						<a href="<?php echo base_url('admin/dashboard/business') ?>">
							<i class="flaticon-home-1"></i> <span><?php echo trans('dashboard') ?></span>
						</a>
					</li>

					<?php if (check_payment_status() == TRUE || settings()->enable_paypal == 0 || user()->user_type == 'trial') : ?>
						<li class="treeview <?php if (isset($page) && $page == "Banking") {
												echo "active";
											} ?>">
							<a href="#"><i class="flaticon-blogging-1"></i>
								<span>Banking</span>
								<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
							</a>
							<ul class="treeview-menu">
								<li class="<?php if (isset($page_title) && $page_title == "Bank List") {
												echo "active";
											} ?>"><a href="<?php echo base_url('admin/banking') ?>"><i class="flaticon-bank" aria-hidden="true"></i>&nbsp;Bank/Cash</a></li>
								<!--<li class="<?php if (isset($page_title) && $page_title == "Loan System") {
													echo "active";
												} ?>"><a href="<?php echo base_url('admin/banking/loan_system') ?>"><i class="flaticon-bank" aria-hidden="true"></i>&nbsp;Loan System</a></li>-->
								<li class="<?php if (isset($page_title) && $page_title == "Bank Transfer") {
												echo "active";
											} ?>"><a href="<?php echo base_url('admin/banking/transfer') ?>"><i class="flaticon-credit-card" aria-hidden="true"></i>&nbsp;Bank To Bank Transfer</a></li>
								<li class="<?php if (isset($page_title) && $page_title == "All Transaction") {
												echo "active";
											} ?>"><a href="<?php echo base_url('admin/banking/transaction_list') ?>"><i class="flaticon-credit-card" aria-hidden="true"></i>&nbsp;All Transaction List</a></li>
							</ul>
						</li>


						<li class="treeview <?php if (isset($main_page) && $main_page == "Sales") {
												echo "active";
											} ?>">

							<a href="#"><i class="flaticon-business-cards"></i>
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

								<?php if (check_permissions(auth('role'), 'estimates') == TRUE) : ?>
									<li class="<?php if (isset($page_title) && $page_title == "Estimate") {
													echo "active";
												} ?>">
										<a href="<?php echo base_url('admin/estimate') ?>">
											<i class="flaticon-contract"></i> <span><?php echo trans('estimates') ?></span>
										</a>
									</li>
								<?php endif; ?>

								<?php if (check_permissions(auth('role'), 'income') == TRUE) : ?>
									<li class="<?php if (isset($page_title) && $page_title == "Income") {
													echo "active";
												} ?>">
										<a href="<?php echo base_url('admin/income') ?>">
											<i class="flaticon-contract"></i> <span>Income</span>
										</a>
									</li>
								<?php endif; ?>
								<li class="<?php if (isset($page_title) && $page_title == "Delivery") {
												echo "active";
											} ?>">
									<a href="<?php echo base_url('admin/delivery') ?>">
										<i class="flaticon-approve-invoice"></i> <span>Delivery Challan</span>
									</a>
								</li>
								<?php if (check_permissions(auth('role'), 'invoices') == TRUE) : ?>
									<li class="<?php if (isset($page_title) && $page_title == "Invoices") {
													echo "active";
												} ?>">
										<a href="<?php echo base_url('admin/invoice/type/1') ?>">
											<i class="flaticon-approve-invoice"></i> <span><?php echo trans('invoices') ?></span>
										</a>
									</li>

									<li class="<?php if (isset($page_title) && $page_title == "Create Recurring Invoice") {
													echo "active";
												} ?>">
										<a href="<?php echo base_url('admin/invoice/create/1') ?>">
											<i class="flaticon-iterative"></i> <span><?php echo trans('recurring-invoice') ?> </span>
										</a>
									</li>
								<?php endif; ?>

							</ul>
						</li>

						<li class="treeview <?php if (isset($main_page) && $main_page == "Purchases") {
												echo "active";
											} ?>">

							<a href="#"><i class="icon-basket"></i>
								<span><?php echo trans('purchases') ?></span>
								<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
							</a>
							<ul class="treeview-menu">

								<?php if (check_permissions(auth('role'), 'bills') == TRUE) : ?>
									<li class="<?php if (isset($page_title) && $page_title == "Bills") {
													echo "active";
												} ?>">
										<a href="<?php echo base_url('admin/bills') ?>">
											<i class="flaticon-credit-card"></i> <span><?php echo trans('bills') ?></span>
										</a>
									</li>
								<?php endif; ?>

								<li class="<?php if (isset($page_title) && $page_title == "Vendor") {
												echo "active";
											} ?>">
									<a href="<?php echo base_url('admin/vendor') ?>">
										<i class="flaticon-group"></i> <span><?php echo trans('vendors') ?></span>
									</a>
								</li>

								<?php if (check_permissions(auth('role'), 'expenses') == TRUE) : ?>
									<li class="<?php if (isset($page_title) && $page_title == "Expense") {
													echo "active";
												} ?>">
										<a href="<?php echo base_url('admin/expense') ?>">
											<i class="flaticon-bill"></i> <span><?php echo trans('expense') ?></span>
										</a>
									</li>
								<?php endif; ?>

								<?php if (check_permissions(auth('role'), 'products') == TRUE) : ?>
									<li class="<?php if (isset($page_title) && $page_title == "Products") {
													echo "active";
												} ?>">
										<a href="<?php echo base_url('admin/product/all/buy') ?>">
											<i class="flaticon-box-1"></i> <span>Stocks & Services</span>
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
									<i class="flaticon-folder-1"></i> <span><?php echo trans('categories') ?></span>
								</a>
							</li>
						<?php endif; ?>

						<?php if (check_permissions(auth('role'), 'reports') == TRUE) : ?>
							<li class="treeview <?php if (isset($main_page) && $main_page == "Report") {
													echo "active";
												} ?>">

								<a href="#"><i class="icon-pie-chart"></i>
									<span><?php echo trans('reports') ?></span>
									<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
								</a>
								<ul class="treeview-menu">

									<li class="<?php if (isset($page_title) && $page_title == "Profit & Loss") {
													echo "active";
												} ?>">
										<a href="<?php echo base_url('admin/reports/profit_loss?end=' . date('Y-m-d') . '&start=' . date('Y') . '-01-01&report_type=1') ?>">
											<span><i class="fa fa-balance-scale"></i><?php echo trans('profit-loss') ?></span>
										</a>
									</li>

									<li class="<?php if (isset($page_title) && $page_title == "Sales Tax") {
													echo "active";
												} ?>">
										<a href="<?php echo base_url('admin/reports/sales_tax?end=' . date('Y-m-d') . '&start=' . date('Y') . '-01-01&report_type=1') ?>">
											<span><i class="fa fa-bar-chart"></i><?php echo trans('sales-tax-report') ?></span>
										</a>
									</li>

									<li class="<?php if (isset($page_title) && $page_title == "Customers") {
													echo "active";
												} ?>">
										<a href="<?php echo base_url('admin/reports/customers?end=' . date('Y-m-d') . '&start=' . date('Y') . '-01-01&report_type=1') ?>">
											<span><i class="flaticon-contract"></i><?php echo trans('income-by-customer'); ?></span>
										</a>
									</li>

									<li class="<?php if (isset($page_title) && $page_title == "Vendors") {
													echo "active";
												} ?>">
										<a href="<?php echo base_url('admin/reports/vendors?end=' . date('Y-m-d') . '&start=' . date('Y') . '-01-01&report_type=1') ?>">
											<span><i class="flaticon-contract"></i><?php echo trans('purchases-by-Vendor'); ?></span>
										</a>
									</li>

									<li class="<?php if (isset($page_title) && $page_title == "Reports") {
													echo "active";
												} ?>">
										<a href="<?php echo base_url('admin/reports') ?>">
											<span><i class="flaticon-bar-chart"></i><?php echo trans('general') . ' ' . trans('reports') ?></span>
										</a>
									</li>

								</ul>
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
						<hr>
						<li class="treeview <?php if (isset($page) && $page == "Tickets") {
												echo "active";
											} ?>">
							<a href="#"><i class="fa fa-support" style="padding-left: 0px; margin: 0"></i>
								<span>Customer Support</span>
								<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
							</a>
							<ul class="treeview-menu">

								<li class="<?php if (isset($page_title) && $page_title == "All Tickets") {
												echo "active";
											} ?>">
									<a href="<?php echo base_url('admin/ticket/view_all_ticket') ?>">
										<span><i class="fa fa-ticket" style="font-weight:400"></i>Ticket</span>
									</a>
								</li>
								<li class="<?php if (isset($page_title) && $page_title == "Callback Request") {
												echo "active";
											} ?>">
									<a href="<?php echo base_url('admin/ticket/callback_request') ?>">
										<span><i class="fa fa-rocket" style="font-weight:400"></i>Callback Request</span>
									</a>
								</li>

							</ul>
						</li>

						<li class="setting_web <?php if (isset($page_title) && $page_title == "Profile") {
													echo "active";
												} ?>">
							<a href="<?php echo base_url('admin/profile') ?>" style="font-size: 16px">
								<!--<i class="flaticon-settings-1"></i>--> <span><?php echo trans('settings') ?></span>
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
								<!--<i class="flaticon-exit"></i>--> <span><?php echo trans('logout') ?></span>
							</a>
						</li>
					<?php endif ?>

				<?php endif; ?>


			</ul>

			<?php if (is_admin()) : ?>
				<a href="javascript:void(0);" class="btn btn-secondary upgrade_version" style="color: white !important">
					<i class="fa fa-info-circle"></i> <span><?php echo trans('version') ?> <?php echo html_escape(settings()->version) ?></span>
				</a>
			<?php else : ?>
				<a href="<?php echo base_url('admin/subscription') ?>" class="btn btn-info" style="color: white !important">
					<i class="fa fa-rocket"></i> <span><?php echo trans('upgrade') ?></span>
				</a>
			<?php endif; ?>

		</section>
	</aside>

<?php endif ?>