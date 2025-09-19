<?php
$sum = 0;
?>
<style>
	ul.nav.nav-tabs>li>a:active,
	ul.nav.nav-tabs>li>a:focus,
	ul.nav.nav-tabs>li>a:hover {
		border-bottom: 4px solid #d4dde3;
		color: #1c252c !important;
		background: transparent !important;
	}

	ul.nav.nav-tabs>li>a.active {
		border-bottom: 4px solid #136acd;
		color: #1c252c;
		background: transparent;
		font-weight: bold;
	}

	ul.nav.nav-tabs>li>a {
		color: #4d6575;
		border-bottom: 4px solid transparent;
		background: #F1F4F8;
		border-radius: 50px;
	}

	.box-body {
		padding: 15px;
		-ms-flex: 1 1 auto;
		flex: 1 1 auto;
	}

	.nav-tabs .nav-link.active,
	.nav-tabs .nav-link.active:focus,
	.nav-tabs .nav-link.active:hover,
	.nav-tabs .nav-item.open .nav-link,
	.nav-tabs .nav-item.open .nav-link:focus,
	.nav-tabs .nav-item.open .nav-link:hover {
		color: #00afa5;
		font-weight: 600;
		border-color: transparent;
		border-bottom-color: #136acd;
		background: transparent;
	}

	/* ghanshyam Css 31-08-2023 */

	.nav-link.active {
		border: none !important;
		background: #fff !important;
		padding: 9px 20px !important;
		border-radius: 9px !important;
	}

	ul.nav.nav-tabs {
		margin: 0 auto;
		margin-top: 0px;
		width: fit-content;
		padding: 3px;
		border-radius: 10px;
		border: 1px solid rgba(37, 37, 37, 0.10);
		background: #F1F5FE;
		position: relative;
		z-index: 111;
	}

	ul.nav.nav-tabs>li>a:active,
	ul.nav.nav-tabs>li>a:focus,
	ul.nav.nav-tabs>li>a:hover {
		border-bottom: 4px solid #d4dde3;
		color: #1c252c !important;
		background: transparent !important;
		border: none;
		cursor: pointer;
	}

	hr.main-line-tab {
		position: relative;
		top: -40px;
		z-index: 11;
	}
</style>

<div class="content-wrapper">
	<section class="content">
		<div class="">
			<div class="d-flex justify-content-between align-items-center f-no bg-light1">
				<h2 style="font-size: 26px"><i class="flaticon-approve-invoice"></i>&nbsp;
					<?php echo trans('invoices') ?>
					<?php if (check_payment_status() == TRUE || settings()->enable_paypal == 0 || user()->user_type == 'trial') : ?>
					<?php endif; ?>
				</h2>
				<a href="<?php echo base_url('admin/invoice/create') ?>" class="btn btn-info btn-rounded pull-right"><i class="fa fa-plus"></i>
					<?php echo trans('create-new-invoices') ?>
				</a>
			</div>
			<div class="row">
				<div class="col-md-12 ">
					<!-- <p class="mb-5"><a href="http://localhost/accountieons/admin/invoice/type/1" class="view_link">Overdue</a></p> -->
					<div class="due_amt mt-4">
						<div class="box-body">
							<!-- <p>Overdue</p> -->
							<p class="due_amt_title">
							<p class="mb-5"><a href="http://localhost/accountieons/admin/invoice/type/1" class="view_link">Overdue</a></p>
							<h3>
								<?php echo $this->business->currency_symbol . ' ' . $total_overdues; ?>
							</h3>
							</p>
						</div>
					</div>
					<?php
					$sum = 0;
					$s = 1;
					foreach ($invoices as $invoice) :
						if ($invoice->status == 1) :
							$sum = $sum + ($invoice->convert_total - get_total_invoice_payments($invoice->id, $invoice->parent_id));
							$s++;
						endif;
					endforeach;
					if (!empty(helper_get_customer($invoice->customer))) : ?>
						<?php
						$currency_symbol = $this->business->currency_symbol;
						?>
						<?php $currency_code = $this->business->currency_code ?>
					<?php endif ?>
					<form method="GET" class="sort_invoice_form" action="<?php echo base_url('admin/invoice/type/3') ?>">
						<div class="row p-15 mt-20 mb-10" style="padding-right: 0 !important;">
							<div class="col-lg-12 p-0 ">
								<p class="mb-5"><a href="<?php echo base_url('admin/invoice/type/1') ?>" class="view_link border-btn	">Clear Filter</a></p>
							</div>
							<div class="col-lg-3 mt-5 pl-0">
								<select class="form-control single_select sort" name="customer" placeholder="Hello">
									<option value="">
										<?php echo trans('all-customers') ?>
									</option>
									<?php foreach ($customers as $customer) : ?>
										<option value="<?php echo html_escape($customer->id) ?>" <?php echo (isset($_GET['customer']) && $_GET['customer'] == $customer->id) ? 'selected' : ''; ?>><?php echo html_escape($customer->name) ?>
										</option>
									<?php endforeach ?>
								</select>
							</div>

							<div class="col-lg-2 col-md-12 mt-5 pl-0">
								<select class="form-control single_select sort" name="status" style="width: 100%;">
									<option value="" <?php echo (isset($_GET['status']) && $_GET['status'] == 0) ? 'selected' : ''; ?>><?php echo trans('all-status') ?></option>
									<option value="2" <?php echo (isset($_GET['status']) && $_GET['status'] == 2) ? 'selected' : ''; ?>><?php echo trans('paid') ?></option>
									<option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == 1) ? 'selected' : ''; ?>><?php echo trans('unpaid') ?></option>
									<option value="4" <?php echo (isset($_GET['status']) && $_GET['status'] == 4) ? 'selected' : ''; ?>><?php echo trans('draft') ?></option>
									<?php if ($invoice->type == 4) { ?>
										<option value="3" <?php echo (isset($_GET['status']) && $_GET['status'] == 3) ? 'selected' : ''; ?>><?php echo trans('sent') ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="col-lg-2 col-md-12 mt-5 pl-0">
								<div class="input-group">
									<input type="text" class="inv-dpick form-control datepicker" placeholder="<?php echo trans('from') ?>" name="start_date" value="<?php if (isset($_GET['start_date'])) {
																																										echo $_GET['start_date'];
																																									} ?>" autocomplete="off">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								</div>
							</div>

							<div class="col-lg-2 col-md-12 mt-5 pl-0">
								<div class="input-group">
									<input type="text" class="inv-dpick form-control datepicker" placeholder="<?php echo trans('to') ?>" name="end_date" value="<?php if (isset($_GET['end_date'])) {
																																									echo $_GET['end_date'];
																																								} ?>" autocomplete="off">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								</div>
							</div>

							<div class="col-lg-2 col-md-12 mt-5 pl-0">
								<div class="input-group">
									<input type="text" class="form-control" placeholder="Enter Invoice #" name="number" value="<?php if (isset($_GET['number'])) {
																																	echo $_GET['number'];
																																} ?>" autocomplete="off">
								</div>
							</div>
							<div class="col-lg-1 mt-5 pl-0">
								<input type="hidden" class="form-control" placeholder="" name="recurring" value="<?php if (isset($_GET['recurring'])) {
																														echo $_GET['recurring'];
																													} ?>" autocomplete="off">
								<button type="submit" class="btn btn-info btn-report btn-block custom_search"><i class="flaticon-magnifying-glass"></i></button>
							</div>
						</div>
					</form>
					<div class="tab-content">
						<!-- All -->
						<div class="tab-pane active" id="messages2" role="tabpanel">
							<div class="table-responsive">
								<div class="card-box mt-30 vh50">
									<!-- <div
										class="d-flex justify-content-between align-items-center f-no bg-light1">
										<h2 style="font-size: 26px"><i
												class="flaticon-approve-invoice"></i>&nbsp;
											<?php echo trans('invoices') ?>
											<?php if (check_payment_status() == TRUE || settings()->enable_paypal == 0 || user()->user_type == 'trial') : ?>
											<?php endif; ?>
										</h2>
										<a href="<?php echo base_url('admin/invoice/create') ?>"
											class="btn btn-info btn-rounded pull-right"><i
												class="fa fa-plus"></i>
											<?php echo trans('create-new-invoices') ?>
										</a>
									</div> -->
									<ul class="nav nav-tabs custab mt-4" role="tablist">
										<li class="nav-item">
											<a class="nav-link <?php if ($status == 3 && empty($_GET['recurring'])) {
																	echo "active";
																} ?>" href="<?php echo base_url('admin/invoice/type/3') ?>" role="tab" aria-selected="false">
												<span class="hidden-xs-downs">
													<?php echo trans('all-invoices') ?>
													<span class="label-count">
														<?php echo helper_count_invoices($istatus = 3, $type = 1) ?>
													</span>
												</span>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link <?php if ($status == 1) {
																	echo "active";
																} ?>" href="<?php echo base_url('admin/invoice/type/1') ?>" role="tab" aria-selected="true">
												<span class="hidden-xs-downs">
													<?php echo trans('unpaid') ?>
													<span class="label-count">
														<?php echo helper_count_invoices($istatus = 1, $type = 1) ?>
													</span>
												</span>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link <?php if ($status == 0) {
																	echo "active";
																} ?>" href="<?php echo base_url('admin/invoice/type/0') ?>" role="tab" aria-selected="false">
												<span class="hidden-xs-downs">
													<?php echo trans('draft') ?>
													<span class="label-count">
														<?php echo helper_count_invoices($istatus = 0, $type = 1) ?>
													</span>
												</span>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link <?php if (isset($_GET['recurring']) && $_GET['recurring'] == 1) {
																	echo "active";
																} ?>" href="<?php echo base_url('admin/invoice/type/3?recurring=1') ?>" role="tab" aria-selected="false">
												<span class="hidden-xs-downs">
													<?php echo trans('recurring-invoice') ?>
													<span class="label-count">
														<?php echo count_recurring_invoices() ?>
													</span>
												</span>
											</a>
										</li>
									</ul>
									<hr class="main-line-tab" />
									<table class="table table-hover cushover">
										<thead>
											<tr class="item-row">
												<th>
													<?php echo trans('status') ?>
												</th>
												<th>
													<?php echo trans('date') ?>
												</th>
												<th>
													<?php echo trans('number') ?>
												</th>
												<th>
													<?php echo trans('customer') ?>
												</th>
												<th class="text-right">
													<?php echo trans('total') ?>
												</th>
												<th class="text-right">
													<?php echo trans('amount-due') ?>
												</th>
												<th class="text-right">
													<?php echo trans('actions') ?>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php if (empty($invoices)) : ?>
												<tr>
													<td colspan="7" class="text-center p-30"><strong>No Data
															Found</strong></td>
												</tr>
											<?php else : ?>
												<?php $total = 0;
												$r = 1;
												foreach ($invoices as $invoice) :
													$currency_symbol = ($invoice->c_currency_symbol != '') ? $invoice->c_currency_symbol : $this->business->currency_symbol;
												?>
													<tr id="row_<?php echo html_escape($invoice->id) ?>">
														<td>
															<?php
															if ($invoice->status == 0) { ?>
																<span data-toggle="tooltip" data-placement="right" title="<?php echo trans('draft-tooltip') ?>" class="custom-label-sm label-light-default"><?php echo trans('draft') ?></span>
																<?php
															} else if ($invoice->status == 2) {
																if ($invoice->type == 4) { ?>
																	<span data-toggle="tooltip" data-placement="right" title="" class="custom-label-sm label-light-warning" style="width: 90px">
																		<?php echo trans('credit-note') ?>
																	</span>
																<?php
																} else { ?>
																	<span data-toggle="tooltip" data-placement="right" title="<?php echo trans('paid-tooltip') ?>" class="custom-label-sm label-light-success"><?php echo trans('paid') ?></span>
																<?php
																}
															} else if ($invoice->status == 1) {
																if (check_paid_status($invoice->id) == 1) { ?>
																	<span data-toggle="tooltip" data-placement="right" title="<?php echo trans('partial-payment') ?>" class="custom-label-sm label-light-info"><?php echo trans('partial') ?></span>
																<?php
																} else { ?>
																	<span data-toggle="tooltip" data-placement="right" title="<?php echo trans('unpaid-tooltip') ?>" class="custom-label-sm label-light-danger"><?php echo trans('unpaid') ?></span>
																<?php
																}
															}
															if ($invoice->recurring == 1) {
																if ($invoice->is_completed == 0) { ?>
																	<span class="custom-label-sm label-light-success mt-5">
																		<?php echo trans('active') ?>
																	</span>
																<?php
																} else if ($invoice->is_completed == 1) { ?>
																	<span data-toggle="tooltip" data-placement="right" title="<?php echo trans('complete-tooltip') ?>" class="custom-label-sm label-light-danger mt-5"><?php echo trans('completed') ?></span>
															<?php }
															} ?>
														</td>
														<td>
															<a href="<?php echo base_url('admin/invoice/details/' . md5($invoice->id)) ?>" class="view_link"><?php echo my_date_show($invoice->date); ?></a>
														</td>
														<td>
															<p class="mb-0">
																<a href="<?php echo base_url('admin/invoice/details/' . md5($invoice->id)) ?>" class="view_link">
																	<?php echo ($invoice->prefix) ? $invoice->prefix . ' - ' : ''; ?> <?php echo html_escape($invoice->number) ?>
																</a>
															</p>
															<?php if ($invoice->recurring == 1) : ?>
																<strong><a href="<?php echo base_url('admin/invoice/details/' . md5($invoice->id)) ?>" class="view_link"><?php echo trans('recurring') ?></a></strong>
															<?php endif ?>
														</td>
														<td style="text-transform: capitalize">
															<a href="<?php echo base_url('admin/invoice/details/' . md5($invoice->id)) ?>" class="view_link">
																<?php echo helper_get_customer($invoice->customer)->name ?>
															</a>
														</td>
														<?php if ($invoice->status == 2) : ?>
															<td class="text-right">
																<span class="total-price">
																	<a href="<?php echo base_url('admin/invoice/details/' . md5($invoice->id)) ?>" class="view_link">
																		<?php if (!empty($currency_symbol)) {
																			echo html_escape($currency_symbol);
																		}
																		echo decimal_format(html_escape($invoice->grand_total), 2); ?>
																	</a>
																</span>
																<br>
																<?php
																if ($currency_symbol != '') {
																	if ($this->business->currency_symbol != $currency_symbol) {
																		$toatl = ($invoice->convert_total != 0) ? $invoice->convert_total : $invoice->grand_total * $invoice->c_rate;
																?>
																		<!-- <span class="conver-total"><?php echo $this->business->currency_symbol . '' . $invoice->convert_total . ' ' . user()->currency_code ?></span> -->
																		<span class="conver-total">
																			<?php echo $this->business->currency_symbol . '' . decimal_format(html_escape($toatl), 2) . ' ' . $this->business->currency_code ?>
																		</span>
																<?php
																	}
																}
																?>
															</td>
															<td class="text-right">
																<span class="total-price">
																	<a href="<?php echo base_url('admin/invoice/details/' . md5($invoice->id)) ?>" class="view_link">
																		<?php if (!empty($currency_symbol)) {
																			echo html_escape($currency_symbol);
																		} ?>0.00
																	</a>
																</span>
																<br>
																<?php
																if ($this->business->currency_symbol != $currency_symbol) { ?>
																	<!-- <span class="conver-total"><?php echo $this->business->currency_symbol . '0.00' . user()->currency_code ?></span> -->
																	<span class="conver-total">
																		<?php echo $this->business->currency_symbol . '0.00' . $this->business->currency_code ?>
																	</span>
																<?php
																}
																?>
															</td>
														<?php else : ?>
															<td class="text-right">
																<span class="total-price">
																	<a href="<?php echo base_url('admin/invoice/details/' . md5($invoice->id)) ?>" class="view_link">
																		<?php if (!empty($currency_symbol)) {
																			echo html_escape($currency_symbol);
																		}
																		echo decimal_format(html_escape($invoice->grand_total), 2) ?>
																	</a>
																</span>
																<br>
																<?php
																if ($currency_symbol != '') {
																	if ($this->business->currency_symbol == $currency_symbol) {
																		$toatl = $invoice->grand_total;
																	} else {
																		$toatl = $invoice->convert_total; ?>
																		<!-- <span class="conver-total"><?php echo $this->business->currency_symbol . '' . $toatl . ' ' . user()->currency_code ?> </span> -->
																		<span class="conver-total">
																			<?php echo $this->business->currency_symbol . '' . decimal_format($toatl, 2) . ' ' . $this->business->currency_code ?>
																		</span>
																<?php
																	}
																}
																?>
															</td>
															<td class="text-danger text-right">
																<span class="total-price">
																	<a href="<?php echo base_url('admin/invoice/details/' . md5($invoice->id)) ?>" class="view_link">
																		<?php
																		if (!empty($currency_symbol)) {
																			echo html_escape($currency_symbol);
																		}
																		if ($invoice->c_rate != 0) {
																			echo decimal_format(html_escape($due_amt = $invoice->grand_total - get_total_invoice_payments($invoice->id, 0)), 2);
																			// echo decimal_format(html_escape($due_amt = $invoice->grand_total - get_total_invoice_payments($invoice->id, 0) / $invoice->c_rate), 2);
																		}
																		?>
																	</a>
																</span>
																<br>
																<?php
																if ($currency_symbol != '' || $this->business->currency_symbol != $currency_symbol) {
																	if (check_paid_status($invoice->id) == 1) {
																		$toatl = ($invoice->grand_total - get_total_invoice_payments($invoice->id, 0)) * $invoice->c_rate;
																	} else {
																		if ($invoice->convert_total == 0.00) {
																			$toatl = $invoice->grand_total;
																		} else {
																			$toatl = $invoice->convert_total;
																		}
																	}
																}
																?>
																<!-- <span class="conver-total"><?php echo $this->business->currency_symbol . '' . $toatl . ' ' . user()->currency_code ?> </span> -->
																<span class="conver-total">
																	<?php echo $this->business->currency_symbol . '' . decimal_format($toatl, 2) . ' ' . $this->business->currency_code ?>
																</span>
															</td>
														<?php endif ?>

														<td class="text-right">
															<?php if ($invoice->status == 2) : ?>
																<?php if ($invoice->type != 4) : ?>
																	<a target="_blank" class="mr-5 hide_viewer view_link" href="<?php echo base_url('admin/invoice/details/' . md5($invoice->id)) ?>"><strong>
																			<?php echo trans('view') ?>
																		</strong></a>
																<?php endif; ?>
															<?php elseif ($invoice->status == 0) : ?>
																<a class="mr-5 approve_item hide_viewer view_link" href="<?php echo base_url('admin/invoice/approve_invoice/' . md5($invoice->id)) ?>"><strong>
																		<?php echo trans('approve') ?>
																	</strong></a>
															<?php else : ?>
																<a class="mr-5 hide_viewer view_link" href="#recordPayment_<?php echo html_escape($invoice->id) ?>" data-toggle="modal"><strong>
																		<?php echo trans('record-a-payment') ?>
																	</strong></a>
															<?php endif ?>

															<div class="btn-group">
																<button type="button" class="btn btn-default rounded btn-sm dropdown-toggle d-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	<?php echo trans('more') ?>
																</button>
																<div class="dropdown-menu st" x-placement="bottom-start">

																	<?php if (auth('role') != 'viewer') : ?>

																		<?php if ($invoice->status != 2) : ?>
																			<a class="dropdown-item" href="<?php echo base_url('admin/invoice/details/' . md5($invoice->id)) ?>"><?php echo trans('view') ?>
																			</a>
																		<?php endif ?>
																		<a class="dropdown-item" data-toggle="modal" href="#sendInvoiceModal_<?php echo html_escape($invoice->id) ?>"><?php echo trans('send') ?></a>
																		<a class="dropdown-item" href="<?php echo base_url('admin/invoice/details/' . md5($invoice->id)) ?>"><?php echo trans('print') ?></a>


																		<?php if ($invoice->type == 4) : ?>
																			<a class="dropdown-item revert_to_invoice" href="<?php echo base_url('admin/invoice/revert_credit_note/' . md5($invoice->id)) ?>"><?php echo trans('revert-invoice') ?></a>
																		<?php endif ?>

																		<?php if ($invoice->recurring == 1 && $invoice->is_completed == 0) : ?>
																			<a class="dropdown-item stop_recurring" href="<?php echo base_url('admin/invoice/stop_recurring/' . $invoice->id) ?>"><?php echo trans('stop-recurring') ?></a>
																		<?php endif ?>

																		<?php if ($invoice->recurring == 0) : ?>
																			<a class="dropdown-item convert_to_recurring" href="<?php echo base_url('admin/invoice/convert_recurring/' . md5($invoice->id)) ?>"><?php echo trans('convert-recurring') ?></a>
																		<?php endif ?>

																		<?php if (settings()->pdf_type == 1) : ?>
																			<a href="#" data-id="invoice_<?php echo rand() ?>" class="dropdown-item btnExport"><?php echo trans('download-pdf') ?> </a>
																		<?php else : ?>
																			<a target="_blank" class="dropdown-item" href="<?php echo base_url('readonly/export_pdf/' . md5($invoice->id)) ?>"><?php echo trans('export-as-pdf') ?></a>
																		<?php endif ?>

																		<!-- <a class="dropdown-item" href="<?php //echo base_url('readonly/export_pdf/'.md5($invoice->id)) 
																											?>"><?php //echo trans('export-as-pdf') 
																												?></a> -->

																		<a target="_blank" class="dropdown-item" href="<?php echo base_url('readonly/invoice/preview/' . md5($invoice->id)) ?>"><?php echo trans('preview-as-a-customer') ?></a>

																		<a target="_blank" class="dropdown-item" href="<?php echo base_url('readonly/invoice/view/' . md5($invoice->id)) ?>"><?php echo trans('share-link') ?></a>

																		<?php if ($this->business->enable_qrcode == 1 && $invoice->qr_code == '') : ?>
																			<a class="dropdown-item" href="<?php echo base_url('admin/business/generate_qucode/' . md5($invoice->id)) ?>"><?php echo trans('generate-qr-code') ?> </a>
																		<?php endif; ?>

																		<div class="dropdown-divider"></div>
																		<a class="dropdown-item" href="<?php echo base_url('admin/invoice/edit/' . md5($invoice->id)) ?>"><?php echo trans('edit') ?> </a>
																		<!-- <?php if ($invoice->type != 4) : ?>
															<?php if ($invoice->edit_show != 1) : ?>
															<a class="dropdown-item" href="<?php echo base_url('admin/invoice/edit/' . md5($invoice->id)) ?>"><?php echo trans('edit') ?> </a>
															<?php endif; ?>
															<?php endif; ?>-->

																		<?php if ($settings->enable_delete_invoice == 1) : ?>
																			<a class="dropdown-item delete_item" data-id="<?php echo html_escape($invoice->id); ?>" href="<?php echo base_url('admin/invoice/delete/' . $invoice->id) ?>"><?php echo trans('delete') ?></a>
																		<?php endif ?>

																	<?php else : ?>
																		<a class="dropdown-item" href="<?php echo base_url('admin/invoice/details/' . md5($invoice->id)) ?>"><?php echo trans('export-as-pdf') ?></a>

																		<a target="_blank" class="dropdown-item" href="<?php echo base_url('readonly/invoice/preview/' . md5($invoice->id)) ?>"><?php echo trans('preview-as-a-customer') ?></a>
																	<?php endif ?>

																</div>
															</div>
														</td>
													</tr>

													<?php $total += $due_amt; ?>
												<?php $r++;
												endforeach ?>
											<?php endif ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="col-md-12 text-center mt-50">
					<?php echo $this->pagination->create_links(); ?>
				</div>
			</div>
		</div>
	</section>
</div>


<?php foreach ($invoices as $invoice) : ?>
	<?php include "include/send_invoice_modal.php"; ?>
<?php endforeach; ?>


<?php foreach ($invoices as $invoice) : ?>
	<div id="recordPayment_<?php echo html_escape($invoice->id) ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="vcenter" aria-hidden="true">
		<div class="modal-dialog modal-dialog-zoom modal-md">
			<form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/invoice/record_payment') ?>" role="form" novalidate>
				<div class="modal-content modal-md">
					<div class="modal-header">
						<h4 class="modal-title" id="vcenter">
							<?php echo trans('record-a-payment-for-this-invoice') ?>
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					</div>
					<div class="modal-body">
						<?php $records = get_customer_advanced_record($invoice->customer) ?>
						<?php if (!empty($records) && $records->customer_id == $invoice->customer) : ?>
							<?php if ($records->amount != 0) : ?>
								<div class="alert alert-info">
									<i class="fa fa-info-circle"></i> <strong>You have reserve amount for this customer:
										<?php echo $records->amount . ' ' . $records->currency; ?>
									</strong>
								</div>
							<?php endif ?>
						<?php endif ?>
						<div class="form-group m-t-30" style="display: none">
							<input type="checkbox" name="is_autoload_amount" value="1" <?php if ($this->business->is_autoload_amount == 1) {
																							echo 'checked';
																						} ?> data-toggle="toggle" data-onstyle="info" data-width="100"></span>
							<label> is autoload advance amount in your next invoice?</label>
						</div>

						<p class="text-muted">
							<?php echo trans('record-payment-info') ?>
						</p><br>
						<div class="form-group row">
							<label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label">
								<?php echo trans('payment-date') ?>
							</label>
							<div class="col-sm-8">
								<div class="input-group">
									<input type="date" class="form-control invoice_due_date" placeholder="yyyy/mm/dd" name="payment_date" value="<?php echo date('Y-m-d') ?>">
									<div class="input-group-append">
										<span class="input-group-text">
											<i class="fa fa-calender"></i>
										</span>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label">
								<?php echo trans('due-date') ?>
							</label>
							<div class="col-sm-8">
								<div class="input-group">
									<input type="date" class="form-control invoice_due_date" placeholder="Enter the due date for partial payment" name="due_date" value="" autocomplete="off">
									<div class="input-group-append">
										<span class="input-group-text">
											<i class="fa fa-calender"></i>
										</span>
									</div>
								</div>
							</div>
						</div>
						<?php
						//echo  sprintf('%0.2f',html_escape($invoice->grand_total = $invoice->grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id)/$invoice->c_rate)); 
						// if ($invoice->c_rate != 0) {
						// 	$due_amt = ($invoice->grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id) / $invoice->c_rate);
						// } else {
						$due_amt = ($invoice->grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id));
						// }
						?>

						<div class="form-group row">
							<label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label">
								<?php echo trans('amount') ?>
							</label>
							<div class="col-sm-8">
								<!-- <input type="number amount" class="form-control" name="amount" value="<?php echo $due_amt; ?>" required max="<?php echo $due_amt; ?>" min="0"> -->
								<input class="form-control amount" placeholder="amount" type="text" name="amount" value="<?php echo $due_amt; ?>" min="0" id="amount_<?php echo html_escape($invoice->id) ?>" max='<?php echo $due_amt; ?>'>
							</div>
						</div>

						<div class="form-group row">
							<label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label">Select
								Bank</label>
							<div class="col-sm-8">
								<select class="form-control bank_id" name="bank_id" required>
									<option value="">
										<?php echo trans('select') ?>
									</option>
									<?php
									foreach ($bankdetails as $banking) :
										if ($banking->account_type != "Credit Card") :
									?>
											<option data-account-no="<?php echo html_escape($banking->account_number); ?>" value="<?php echo html_escape($banking->id); ?>">
												<?php echo html_escape($banking->account_type) . '-' . html_escape($banking->bank_name); ?>
											</option>
										<?php endif ?>
									<?php endforeach ?>
								</select>
								<div class="account_number_show mt-1 ml-10"></div>
							</div>
						</div>

						<div class="form-group row">
							<label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label">
								<?php echo trans('payment-method') ?>
							</label>
							<div class="col-sm-8">
								<select class="form-control" id="tax" name="payment_method" required>
									<option value="">
										<?php echo trans('select-payment-method') ?>
									</option>
									<?php $i = 1;
									foreach (get_payment_methods() as $payment) : ?>
										<option value="<?php echo $i; ?>"><?php echo html_escape($payment); ?></option>
									<?php $i++;
									endforeach ?>
								</select>
							</div>
						</div>

						<div class="form-group row">
							<label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label">
								<?php echo trans('memo-notes') ?>
							</label>
							<div class="col-sm-8">
								<textarea class="form-control" name="note"> </textarea>
							</div>
						</div>

					</div>

					<div class="modal-footer">
						<input type="hidden" name="invoice_id" value="<?php echo html_escape(md5($invoice->id)) ?>">
						<!-- csrf token -->
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
						<button type="submit" class="btn btn-info waves-effect pull-right">
							<?php echo trans('add-payment') ?>
						</button>
					</div>
				</div>
			</form>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
<?php endforeach; ?>