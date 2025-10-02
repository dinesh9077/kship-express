<style>
	.table>tbody>tr>td,
	.table>tbody>tr>th,
	.table>tfoot>tr>td,
	.table>tfoot>tr>th,
	.table>thead>tr>td,
	.table>thead>tr>th {
		border-top: 0px solid #f4f4f4;
	}

	.shadow-lg {
		box-shadow: 0 8px 32px rgb(77 101 117 / 35%) !important;
	}

	@media only screen and (max-width: 600px) {
		.save_invoice_btn {
			width: 100%;
			margin-bottom: 7px;
			margin-left: 0 !important;
		}

		.preview_invoice_btn {
			width: 100%;
		}
	}

	.btn {
		padding: 13px 20px;
	}

	textarea.form-control {
		background: #F1F4F8;
		border: none !important;
	}

	.select2-container--default .select2-selection--single {
		background-color: #F1F4F8;
		border: 1px solid #b2c2cd;
		border-radius: 4px;
		margin-bottom: 14px;
	}

	.add-new-item {
		border-radius: 50px !important;
		padding: 13px !important;
		background: linear-gradient(89.98deg, #9E6AF6 0.02%, #6A34C3 99.98%);
		color: #fff;
		border: none !important;
	}

	.form-control {
		height: 45px;
		border-radius: 0;
		/* border-color: #b2c2cd; */
		border: 1.5px solid #F1F4F8 !important;
		margin-bottom: 5px;
		border-radius: 9px;
		box-shadow: 0 0px 2px rgba(0, 0, 0, 0.06);
		transition: none;
		background: #F1F4F8;
		margin-bottom: 11px;
	}

	.form-control {
		height: 45px;
	}

	.panel.panel-default.inv {
		border: none;
		border-radius: 10px;
		margin-bottom: 20px;
		padding: 10px;
	}

	.input-group .form-control {
		position: relative;
		z-index: 2;
		-ms-flex: 1 1 auto;
		flex: 1 1 auto;
		width: 1%;
		margin-bottom: 0;
		font-weight: 500;
		color: #181818;
		border-color: #fff !important;
		border-radius: 10px !important;
		padding: 11px !important;
		height: auto;
		margin-bottom: 6px;
	}

	select.form-control {
		height: 45px !important;
		margin: 0;
		border-radius: 10px;
		border-color: #fff0 !important;
		background: #f1f4f8;
	}

	a.btn.btn-default.add_item_btn {
		width: 100%;
		padding: 15px !important;
		background: #fff;
		font-size: 16px;
		color: #000000;
		font-weight: bold;
		border: none !important;
		font-size: 16px !important;
	}

	.box-footer {
		border-top: none;
		padding: 0.8rem 2.25rem;
		background-color: #fff;
		border-radius: 0 0 10px 10px;
	}

	.fa-list:before {
		content: "\f03a";
		top: 6px;
		position: relative;
	}

	.delete {
		display: block;
		color: #ffffff;
		background: #fc4b6c;
		/* position: absolute; */
		border: none;
		font-weight: bold;
		padding: 2px 9px;
		/* top: -8px; */
		/* right: -9px; */
		font-family: Verdana;
		font-size: 19px;
		border-radius: 50px;
	}
</style>
<div class="content-wrapper">
	<?php $settings = get_settings(); ?>
	<!-- Main content -->
	<section class="content">
		<div class="">
			<div class="col-md-12 m-auto">
				<?php if (empty($this->business->logo)) : ?>
					<?php include 'include/setup_alert.php'; ?>
				<?php endif ?>
				<div class="row mb-10">
					<div class="col-md-12">
						<h2>
							<strong style="text-transform:capitalize"><i class="flaticon-approve-invoice"></i>&nbsp;<?php if (isset($page_title) && $page_title == 'Edit Estimate') {
																														echo "Edit";
																													} else {
																														echo trans('create-new-estimate');
																													} ?></strong>
							<?php if (isset($page_title) && $page_title == 'Edit Estimate') : ?>
								<a href="<?php echo base_url('admin/estimate/details/' . md5($invoice[0]['id'])) ?>" class="btn btn-default btn-rounded pull-right"><i class="fa fa-long-arrow-left"></i> <?php echo trans('back') ?></a>
							<?php else : ?>
								<a href="<?php echo base_url('admin/estimate') ?>" class="btn btn-default btn-rounded pull-right"> <?php echo trans('all-estimates') ?></a>
							<?php endif ?>
						</h2>
					</div>
				</div>
				<form id="estimate_form" method="post" enctype="multipart/form-data" class="validate-form leave_con" action="<?php echo base_url('admin/estimate/add') ?>" role="form" novalidate>
					<!-- load preview data -->
					<div class="alert alert-danger mb-20 error_area" style="display: none;">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<h4><?php echo trans('invoice-create-error') ?>.</h4>
						<div id="load_error"></div>
					</div>
					<div id="load_data"></div>
					<div class="invoice_area mt-20">
						<!-- invoice header -->
						<div class="row">
							<div class="col-12">
								<div class="panel panel-default inv">
									<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse8" aria-expanded="true" aria-controls="collapse8">
										<div class="panel-heading inv" role="tab" id="heading8">
											<h4 class="panel-title inv">
												<span class="style_border"><?php echo trans('invoice-heading-title') ?></span>
												<i class="fa fa-angle-down pull-right"></i>
											</h4>
										</div>
									</a>
									<div id="collapse8" class="panel-collapse data_collaps_border collapse show" role="tabpanel" aria-labelledby="heading8" style="">
										<div class="panel-body inv">
											<div class="row">
												<div class="col-md-6">
													<?php if (empty($this->business->logo)) : ?>
														<span class="alterlogo"><i class="flaticon-close"></i></span>
													<?php else : ?>
														<img width="130px" src="<?php echo base_url($this->business->logo) ?>" alt="Logo">
													<?php endif ?>
												</div>
												<div class="col-md-6">
													<?php if (isset($page_title) && $page_title == 'Edit Estimate') : ?>
														<input type="text" id="example-input-large" name="title" class="form-control form-control-lg text-right" value="<?php echo html_escape($invoice[0]['title']) ?>">
													<?php else : ?>
														<input type="text" class="form-control text-right" name="title" placeholder="<?php echo trans('estimate-title') ?>" value="Estimate">
													<?php endif ?>
													<input type="text" id="example-input-large" name="summary" class="form-control form-control-md text-right" placeholder="<?php echo trans('summery-placeholder') ?>" value="<?php echo html_escape($invoice[0]['summary']) ?>"> <br>
													<p class="mb-0 text-right"><strong><?php echo html_escape($this->business->name) ?></strong></p>
													<p class="text-right"><?php echo html_escape($this->business->country) ?></p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- invoice body -->
						<div class="box ">
							<div class="box-body ">
								<div class="">
									<div class="row mb-20">
										<div class="col-xs-12 col-md-12">
											<div class="row inv-info">
												<div class="col-lg-6 text-left">
													<h5><?php echo trans('estimate-to') ?></h5>
													<div id="load_customers">
														<?php include 'include/invoice_load_customers.php'; ?>
													</div>
													<a data-toggle="modal" href="#customerModal" title="Add a row" class="add-new-item btn btn-block btn-default btn-sm p-10"><i class="icon-plus"></i> <?php echo trans('add-a-customer') ?></a>
													<div class="mt-20" id="load_info"></div>
												</div>
												<div class="col-lg-6 text-right">
													<?php if ($this->business->invoice_number == 1) : ?>
														<div class="form-group row">
															<label for="inputEmail3" class="col-md-8 text-right control-label col-form-label re_label">
																<?php echo trans('estimate-number') ?> - <?php if (!empty($this->business->estimate_invoice_prefix)) {
																												echo $this->business->estimate_invoice_prefix;
																											} ?>
															</label>
															<div class="col-md-4">
																<?php if (isset($page_title) && $page_title == 'Edit Estimate') : ?>
																	<input type="text" class="form-control" name="number" value="<?php echo html_escape($invoice[0]['number']) ?>" placeholder="<?php echo trans('estimate-number') ?>">
																<?php else : ?>
																	<input type="text" class="form-control" name="number" value="<?php echo get_auto_invoice_number(2, 0) ?>">
																<?php endif ?>
															</div>
														</div>
													<?php endif; ?>
													<?php if ($this->business->po_details == 1) : ?>
														<div class="form-group row">
															<label for="inputEmail3" class="col-md-8 text-right control-label col-form-label re_label"><?php echo trans('p.o.s.o.-number') ?></label>
															<div class="col-md-4">
																<input type="text" value="<?php echo html_escape($invoice[0]['poso_number']) ?>" class="form-control" name="poso_number">
															</div>
														</div>
													<?php endif; ?>
													<div class="form-group row">
														<label for="inputEmail3" class="col-md-8 text-right control-label col-form-label re_label"><?php echo trans('estimate-date') ?></label>
														<div class="col-md-4">
															<div class="input-group">
																<?php if (isset($page_title) && $page_title == 'Edit Estimate') : ?>
																	<input type="text" class="form-control datepicker" name="date" value="<?php echo $invoice[0]['date'] ?>">
																<?php else : ?>
																	<input type="text" class="form-control datepicker" placeholder="yyyy/mm/dd" name="date" value="<?php echo date('Y-m-d') ?>">
																<?php endif ?>
																<div class="input-group-append">
																	<span class="input-group-text">
																		<i class="fa fa-calender"></i>
																	</span>
																</div>
															</div>
														</div>
													</div>
													<div class="form-group row d-none">
														<label for="inputEmail3" class="col-md-8 text-right control-label col-form-label re_label">Time</label>
														<div class="col-md-4">
															<div class="input-group">
																<input type="text" class="form-control" placeholder="h:i A" name="time" value="<?php echo (isset($page_title) && $page_title == 'Edit Estimate' && isset($invoice[0]['time']) && !empty($invoice[0]['time'])) ? $invoice[0]['time'] : date('h:i A') ?>" readonly>
																<div class="input-group-append">
																	<span class="input-group-text">
																		<i class="fa fa-calender"></i>
																	</span>
																</div>
															</div>
														</div>
													</div>
													<?php if ($this->business->time_on_invoice == 1) : ?>
														<div class="form-group row d-none">
															<label for="inputEmail3" class="col-md-8 text-right control-label col-form-label re_label">Time</label>
															<div class="col-md-4">
																<div class="input-group">
																	<?php if (isset($page_title) && $page_title == 'Edit Bill') : ?>
																		<input type="text" class="form-control" name="time" value="<?php echo $invoice[0]['time'] ?>">
																	<?php else : ?>
																		<input type="text" class="form-control" placeholder="h:i A" name="time" value="<?php echo date('h:i A') ?>">
																	<?php endif ?>
																	<div class="input-group-append">
																		<span class="input-group-text">
																			<i class="fa fa-calender"></i>
																		</span>
																	</div>
																</div>
															</div>
														</div>
													<?php endif ?>
													<div class="form-group row mt-10">
														<label for="inputEmail3" class="col-md-8 text-right control-label col-form-label re_label"><?php echo trans('expires-on') ?></label>
														<div class="col-md-4">
															<div class="input-group">

																<?php if (isset($page_title) && $page_title == 'Edit Estimate') : ?>
																	<input type="text" class="form-control datepicker" name="expire_on" value="<?php echo $invoice[0]['expire_on'] ?>">
																<?php else : ?>
																	<input type="text" class="form-control datepicker" placeholder="yyyy/mm/dd" name="expire_on" value="<?php echo date('Y-m-d') ?>">
																<?php endif ?>
																<div class="input-group-append">
																	<span class="input-group-text">
																		<i class="fa fa-calender"></i>
																	</span>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12 p-0">
											<div class="table-responsive">
												<table class="table m-0">
													<thead>
														<tr class="item-row">
															<th colspan="3"><?php echo trans('item') ?></th>
															<th>Item Description</th>
															<th>Price Per Unit</th>
															<th><?php echo trans('quantity') ?> / Unit</th>
															<!-- <th>Unit</th> -->
															<th class="text-right"><?php echo trans('total') ?></th>
															<th></th>
														</tr>
													</thead>
													<tbody>
														<input type="hidden" id="total_item" value="<?php echo (isset($page_title) && $page_title == 'Edit Estimate') ? count(helper_get_invoice_items($invoice[0]['id'])) + 1 : 1; ?>">
													<tbody id="invoice_row">
														<?php if (isset($page_title) && $page_title == 'Edit Estimate') { ?>
															<?php $items = helper_get_invoice_items($invoice[0]['id']) ?>
															<?php
															$j = 1;
															foreach ($items as $key => $product) {
																$i = $key + 2
															?>
																<tr class="row_id_<?php echo $i; ?>">
																	<td colspan="3" style="width: 20%;">
																		<input type="text" class="form-control item" placeholder="Item" name="items[]" value="<?php echo html_escape($product->item_name) ?>" id="item_<?php echo $i; ?>">
																	</td>
																	<input type="hidden" class="form-control ws-180" name="serial_no[]" value="<?php echo html_escape($product->serial_no) ?>" id="serial_no_<?php echo $i; ?>">
																	<td style="width: 30%;vertical-align: top !important;" rowspan="3">
																		<?php if ($this->business->enable_serial_no == 1) : ?>
																			<div class="d-flex">
																				<i class="fa fa-list form-control item ws-180 w-40 mr-10" style="cursor:pointer;" aria-hidden="true" onclick="openSerialModal(<?php echo ($product->item) ? $product->item : 0 ?>,<?php echo $i; ?>)"></i>
																				<input class="form-control" readonly type="text" id="text_serial_no_<?php echo $i; ?>" value="<?php echo html_escape($product->serial_no) ?>" placeholder="Serial No.">
																			</div>
																		<?php endif; ?>
																		<?php if ($this->business->hsnsac == "1" || $this->business->hsnsac == 1) : ?>
																			<input class="form-control" placeholder="HSN / SAC Code" type="text" name="hsn_sac[]" value="<?php echo html_escape($product->hsn_code) ?>" id="hsn_sac_<?php echo $i; ?>">
																		<?php endif; ?>
																		<textarea name="details[]" class="form-control ac-textarea" rows="4" placeholder="Enter item description"><?php echo html_escape($product->details) ?></textarea>
																	</td>
																	<td style="width: 20%;">
																		<input class="form-control invo_price text-right" placeholder="Price" type="text" name="price[]" value="<?php echo (isset($product->price)) ? $product->price : 0; ?>" id="price_<?php echo $i; ?>">
																	</td>
																	<td width="20%">
																		<div class="d-flex">
																			<input class="form-control invo_qty mr-2" placeholder="Qnty." type="text" name="quantity[]" value="<?php echo html_escape($product->qty) ?>" id="qty_<?php echo $i; ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" max="<?php echo $this->business->quantity_limit; ?>" min="0">
																				<small class="text-danger error_qty_<?php echo $i; ?>"></small>
																		<!-- </td>
																		<td width="10%"> -->
																			<select class="form-control single_select" name="unit[]" id="unit_<?php echo $i; ?>">
																				<?php foreach ($units as $unit) { ?>
																					<option value="<?php echo $unit->unit; ?>" <?php echo (!empty($product->unit) && $unit->unit == $product->unit) ? 'selected' : ""; ?>><?php echo $unit->unit; ?></option>
																				<?php } ?>
																			</select>
																		</div>
																		<small class="text-danger error_qty_<?php echo $i; ?>"></small>
																	</td>
																	<td class="text-right" width="15%">
																		<span class="currency_wrapper"></span>
																		<span class="total" id="price_text_<?php echo $i; ?>"><?php echo html_escape($product->total) ?></span>
																	</td>
																	<td class="text-right">
																		<div class="delete-btn">
																			<button type="button" class="delete remove_row" href="javascript:;" id="<?php echo $i; ?>" style="cursor:pointer" title="Remove row"><i class="fa fa-trash" aria-hidden="true"></i></button>
																			<input class="total" type="hidden" name="total_price[]" value="<?php echo html_escape($product->total) ?>" id="total_price_<?php echo $i; ?>">
																			<input type="hidden" name="product_ids[]" value="<?php echo ($product->item) ? $product->item : 0 ?>">
																			<input type="hidden" name="tax_key[]" value="<?php echo html_escape($i) ?>">
																		</div>
																	</td>
																</tr>
																<tr class="row_id_<?php echo $i; ?>">
																	<td colspan="2"></td>
																	<td></td>
																	<td class="text-right"><strong style="display:<?php echo ($this->business->invoice_discount == 1) ? 'block' : 'none'; ?>">Discount</strong></td>
																	<td colspan="1">
																		<input type="text" id="discount_<?php echo $i; ?>" name="discount[]" class="form-control invo_discount" value="<?php echo $product->discount; ?>" style="display:<?php echo ($this->business->invoice_discount == 1) ? 'block' : 'none'; ?>">
																	</td>
																	<?php $discount_text = $product->total * $product->discount / 100; ?>
																	<td class="text-right"> <span id="discount_text_<?php echo $i; ?>" style="display:<?php echo ($this->business->invoice_discount == 1) ? 'block' : 'none'; ?>"><?php echo $discount_text; ?></span></td>
																	<td class="text-right">
																	</td>
																</tr>
																<?php
																if (!empty($product->taxs)) {
																	$taxs = explode(',', $product->taxs);
																	$taxvals = explode(',', $product->taxvals);
																	foreach ($taxs as $keys => $tax) {
																		$j++;

																?>
																		<tr class="row_id_<?php echo $i; ?>" id="gst_row_<?php echo $j; ?>">
																			<td></td>
																			<td></td>
																			<td class="text-right" colspan="3"><strong>Tax</strong></td>
																			<td colspan="1"><select class="form-control single_select changesel" name="tax[<?php echo $i; ?>][]" id="tax_<?php echo $i; ?>_<?php echo $j; ?>" style="text-transform: capitalize">
																					<option value="" selected>Select Tax</option><?php foreach ($gsts as $key => $gst) {
																																		$tax_name = $gst->name . ' ' . $gst->rate . '%'; ?><option value="<?php echo $gst->name; ?> <?php echo $gst->rate; ?>%" data-rate="<?php echo $gst->rate; ?>" data-name="<?php echo $gst->name; ?>" <?php echo ($tax_name == $tax) ? 'selected' : ''; ?>><?php echo $gst->name; ?> <?php echo $gst->rate; ?>%</option><?php } ?>
																				</select></td>
																			<td class="text-right" id="tax_text_<?php echo $i; ?>_<?php echo $j; ?>">0.00</td>
																			<td class="text-right">
																				<div class="delete-btn"><input type="hidden" name="taxval[<?php echo $i; ?>][]" id="tax_val_<?php echo $i; ?>_<?php echo $j; ?>" value="<?php echo $taxvals[$keys]; ?>"><a class="delete remove_gst_row" id="<?php echo $j; ?>" href="javascript:;" title="Remove row"><i class="fa fa-trash" aria-hidden="true"></i></a></div>
																			</td>
																		</tr>




																<?php }
																}  ?>

																<tr class="row_id_<?php echo $i; ?>" id="gst_row_1" style="border-bottom: 1px solid #b2c2cd8c">
																	<td></td>
																	<td></td>
																	<td class="text-right" colspan="3"><strong style="display:<?php echo ($this->business->invoice_tax == 1) ? 'block' : 'none'; ?>">Tax</strong></td>
																	<td colspan="1">
																		<?php if ($this->business->invoice_tax == 1) : ?> <select class="form-control single_select selectgst" id="tax_<?php echo $i; ?>_1" data-id="<?php echo $i; ?>" style="text-transform: capitalize">
																				<option value="" selected>Select Tax</option>
																				<?php foreach ($gsts as $key => $gst) { ?>
																					<option value="<?php echo $gst->name; ?> <?php echo $gst->rate; ?>%" data-id="<?php echo $gst->name; ?> <?php echo $gst->rate; ?>%" data-rate="<?php echo $gst->rate; ?>" data-name="<?php echo $gst->name; ?>">
																						<?php echo $gst->name; ?> <?php echo $gst->rate; ?>%
																					</option>
																				<?php } ?>
																			</select>
																		<?php endif; ?>
																	<td class="text-right" style="display:<?php echo ($this->business->invoice_tax == 1) ? 'block' : 'none'; ?>">
																		—
																	</td>
																</tr>




															<?php
															}
															?>
															<input type="hidden" value="<?php echo ($j) ? $j : 1; ?>" id="total_gst_row">

														<?php
														}
														?>

													</tbody>
													<tr style="border-bottom: 1px solid #c5c5c5;">
														<td colspan="9" class="p-0">
															<a href="#" class="btn btn-default add_item_btn text-left"><i class="icon-plus"></i> <?php echo trans('add-an-item') ?></a>
														</td>
													</tr>

													<tr id="products_list_inv" style="display: none;">
														<td colspan="9" class="p-0">
															<div class="inv-product br-10 animate-ttb">
																<div class="form-group has-search">
																	<span class="icon-magnifier form-control-feedback"></span>
																	<input type="text" class="form-control search_product" placeholder="Type product" data-id="">
																</div>

																<div class="loaderp text-center p-10"></div>

																<!-- load ajax data -->
																<a href="#" class="cancel-inv">&times;</a>
																<div id="load_product" class="pro-scroll">
																	<?php include 'include/invoice_product_list.php'; ?>
																</div>

																<div class="col-md-12 p-0">
																	<a id="addRow" href="#" class="add-new-item btn btn-block btn-info p-10"><i class="icon-plus"></i> <?php echo trans('add-new-item') ?></a>
																</div>
															</div>
														</td>
													</tr>
													</tbody>
												</table>
											</div>
											<div class="table-responsive">
												<table class="table">
													<tr>
														<td colspan="2"></td>
														<td colspan="2"></td>
														<td></td>
														<td colspan="2" class="text-right"><strong><?php echo trans('sub-total') ?></strong></td>
														<td class="text-right">
															<span class="currency_wrapper"></span><span id="subtotal">0.00</span>
															<input type="hidden" class="subtotal" name="subtotal" value="">
														</td>
														<td>
														</td>
													</tr>

													<tr>
														<td colspan="2"></td>
														<td colspan="2"></td>
														<td></td>
														<td colspan="2" class="text-right"><strong style="display:<?php echo ($this->business->invoice_discount == 1) ? 'block' : 'none'; ?>">Discount</strong></td>
														<td class="text-right">
															<span class="currency_wrapper" style="display:<?php echo ($this->business->invoice_discount == 1) ? 'block' : 'none'; ?>"></span><span id="total_discount" style="display:<?php echo ($this->business->invoice_discount == 1) ? 'block' : 'none'; ?>">0.00</span>
															<input type="hidden" name="total_discount" class="total_discount" value="0">
														</td>
														<td> </td>
													</tr>
													<tbody id="gst_total_tax">
													</tbody>
													<input type="hidden" class="total_tax" id="total_tax" value="<?php if (isset($total_tax)) {
																														echo $total_tax->total;
																													} ?>">
													<tr>
														<td colspan="3"></td>
														<td></td>
														<td></td>
														<td></td>
														<td class="text-right">
															<strong><?php echo trans('grand-total') ?></strong>
														</td>
														<td class="text-right" style="font-weight: bold">
															<span class="currency_wrapper"></span><span id="grandTotal">0</span>
															<input type="hidden" class="grandtotal" name="grand_total" value="">
															<input type="hidden" class="convert_total" name="convert_total" value="0">
														</td>
													</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
									<div class="box-footer text-right">
										<input type="hidden" class="currency_code" name="currency_code" value="<?php echo $this->business->currency_code; ?>">
										<input type="hidden" class="c_currency_symbol" name="c_currency_symbol" value="<?php echo $this->business->currency_symbol; ?>">
										<input type="hidden" class="c_rate" name="c_rate">
										<strong><span class="conversion_currency"> </span></strong>
									</div>
								</div>
							</div>

						</div>

						<!-- invoice footer -->
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default inv">
									<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="true" aria-controls="collapse2">
										<div class="panel-heading inv" role="tab" id="heading8">
											<h4 class="panel-title inv">
												<span class="style_border"><?php echo trans('footer') ?></span>
												<i class="fa fa-angle-down pull-right fa-1x"></i>
											</h4>
										</div>
									</a>
									<div id="collapse2" class="panel-collapse data_collaps_border collapse show" role="tabpanel" aria-labelledby="heading2" aria-expanded="false">
										<div class="panel-body inv">
											<div class="row">
												<div class="col-md-12">
													<?php if (isset($page_title) && $page_title == 'Edit Invoice') : ?>
														<textarea class="form-control" rows="4" name="footer_note" placeholder="Enter a footer for this invoice (eg. Tax info, Thank you note, etc.)"><?php echo $invoice[0]['footer_note'] ?></textarea>
													<?php else : ?>
														<textarea class="form-control" rows="4" name="footer_note" placeholder="Enter a footer for this invoice (eg. Tax info, Thank you note, etc.)"><?php echo $this->business->footer_note ?></textarea>

													<?php endif ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>

					<!-- csrf token -->
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<input type="hidden" name="id" value="<?php echo html_escape($invoice[0]['id']); ?>">

					<div class="row mb-20">
						<div class="col-md-12 text-right p-20">
							<button type="submit" class="btn btn-info btn-rounded save_estimate_btn"><i class="fa fa-check"></i> <?php if (isset($page_title) && $page_title == 'Edit Estimate') {
																																		echo "Update";
																																	} else {
																																		echo "Save Estimate";
																																	} ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
</div>


<!-- product list modal -->
<div id="productModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="vcenter" aria-hidden="true">
	<div class="modal-dialog modal-dialog-zoom modal-md">
		<form id="product-form" method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/invoice/ajax_add_product') ?>" role="form" novalidate>
			<div class="modal-content modal-md">
				<div class="modal-header">
					<h4 class="modal-title" id="vcenter"><?php echo trans('add-new-product') ?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body">

					<div class="form-group row">
						<label for="inputEmail3" class="col-sm-3 text-right control-label col-form-label"><?php echo trans('product-name') ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="name" required>
						</div>
					</div>

					<div class="form-group row">
						<label for="inputEmail3" class="col-sm-3 text-right control-label col-form-label"><?php echo trans('price') ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="price" required>
						</div>
					</div>

					<div class="form-group row">
						<label for="inputEmail3" class="col-sm-3 text-right control-label col-form-label"><?php echo trans('details') ?></label>
						<div class="col-sm-9">
							<textarea class="form-control" name="details"> </textarea>
						</div>
					</div>

				</div>

				<div class="modal-footer">
					<!-- csrf token -->
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<button type="submit" class="btn btn-info rounded waves-effect pull-right"><?php echo trans('add-product') ?></button>
				</div>
			</div>
		</form>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>


<!-- customer modal -->
<div id="customerModal" class="modal fade" role="dialog" aria-labelledby="vcenter" aria-hidden="true">
	<div class="modal-dialog modal-dialog-zoom modal-md">
		<form id="customer-form" method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/invoice/ajax_add_customer') ?>" role="form" novalidate>
			<div class="modal-content modal-md">
				<div class="modal-header">
					<h4 class="modal-title" id="vcenter"><?php echo trans('add-new-customer') ?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body">
					<ul class="nav nav-tabs" style="border-bottom:0px">
						<li><a class="active" data-toggle="tab" href="#cus_contact">Contact</a></li>
						<li><a data-toggle="tab" href="#cus_address">Billing</a></li>
					</ul>

					<div class="tab-content" style="padding: 2%">
						<div id="cus_contact" class="tab-pane in active">
							<h3>Contact</h3>
							<div class="form-group">
								<label><?php echo trans('customer-name') ?>  <span class="text-danger">*</span></label>
								<input type="text" class="form-control" required name="name">
							</div>
							<div class="form-group">
								<label><?php echo trans('email') ?></label>
								<input type="email" class="form-control" name="email" value="">
							</div>
							<div class="form-group">
								<label><?php echo trans('phone') ?></label>
								<input type="text" class="form-control" name="phone" value="">
							</div>
							<div class="form-group">
								<label><?php echo trans('address') ?> </label>
								<textarea class="form-control" name="address"></textarea>
							</div>
							<a class="btn btn-info waves-effect pull-right" data-toggle="tab" href="#cus_address">Next</a>
						</div>

						<div id="cus_address" class="tab-pane fade">
							<h3>Billing</h3>
							<div class="form-group">
								<label>Shipping contact person name : </label>
								<input type="text" class="form-control" required name="s_name">
							</div>
							<div class="form-group">
								<label>Shipping <?php echo trans('phone') ?> No.</label>
								<input type="text" class="form-control" required name="s_phone">
							</div>
							<div class="form-group">
								<label>Shipping Address</label>
								<textarea class="form-control" name="address1"></textarea>
							</div>
							<!-- <div class="form-group">
								<label><?php echo trans('business') . ' ' . trans('number') ?></label>
								<input type="text" class="form-control" name="cus_number" value="">
							</div> -->
							<div class="form-group">
								<label>Select Tax Type</label>
								<select class="form-control single_select" style="width:100%" name="tax_format" id="tax_format" onchange="changeFunction();">
									<option value="0"><?php echo trans('select') ?></option>
									<option value="GST Number">GST Number</option>
									<option value="Tax Number">Tax Number</option>
									<option value="Vat Number">Vat Number</option>
									<option value="Tax/Vat Number">Tax/Vat Number</option>
								</select>
							</div>
							<div class="form-group" id="vat_code_show" style="display:none;">
								<label id="text_name_change"></label>
								<input type="text" class="form-control" name="vat_code" value="">
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label p-0" for="example-input-normal"><?php echo trans('country') ?> </label>
								<select class="form-control single_select col-sm-12 country" id="countrys" name="country" style="width: 100%">
									<option value=""><?php echo trans('select') ?></option>
									<?php foreach ($countries as $country) : ?>
										<?php if (!empty($country->currency_name)) : ?>
											<option value="<?php echo html_escape($country->id); ?>" data-currencyname="<?php echo html_escape($country->name); ?>" data-currency_code="<?php echo html_escape($country->currency_code); ?>" data-currency_symbol="<?php echo html_escape($country->currency_symbol); ?>"><?php echo html_escape($country->name); ?></option>
										<?php endif ?>
									<?php endforeach ?>
								</select>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label p-0" for="example-input-normal"><?php echo trans('currency') ?> </label>
								<select class="form-control col-sm-12 wd-100" id="currency" name="currency">
									<option value=""><?php echo trans('select') ?></option>
									<?php foreach ($countries as $currency) : ?>
										<option value="<?php echo $currency->currency_code; ?>"><?php echo $currency->currency_code . ' - ' . $currency->currency_name; ?></option>
									<?php endforeach ?>
								</select>
							</div>
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							<button type="submit" class="btn btn-info waves-effect pull-right"><?php echo trans('add-customer') ?></button>
							<input type="reset" value="Reset" class="d-none reset_customer">
						</div>
					</div>

				</div>
			</div>
		</form>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<style>
	.srch-btn {
		border: 1px solid #dbdbdb;
		padding: .5rem .75rem;
		height: 40px;
		margin-bottom: 5px;
		border-left: none;
		cursor: pointer;
		background: #bce2e4;
		border-top-right-radius: 3px;
		border-bottom-right-radius: 3px;
	}
</style>

<div class="modal fade" id="InvserialNo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="tab-content" style="padding: 2%">
			<div class="tab-pane in active">
				<div class="modal-content modal-lg" style="width: 60%;">
					<div class="modal-header">
						<h4 class="modal-title" id="vcenter">Opening Stock</h4>
						<button type="button" class="close close_seroalmodal">×</button>
					</div>
					<div class="modal-body" id="existing_query">
						<div class="row align-items-center">
							<div class="col-12 form-group">
								<label>Add Serial No</label>
								<div class="d-flex align-items-center">
									<input type="text" class="form-control" id="serial_no" placeholder="Serial No">
									<button type="button" class="srch-btn w-25" id="add_serial">Add</button>
								</div>
							</div>
							<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive">
								<table>
									<thead id="InvserialNo_html">
									</thead>
								</table>
							</div>
							<input type="hidden" id="sr_no" value="">
							<div class="col-12" style="margin-top: 10%;"></div>
							<div class="col-12 form-group">
								<button type="button" class="btn btn-primary w-25 pull-right" id="store_serial">Submit</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<?php if (isset($page_title) && $page_title == 'Edit Estimate') { ?>
	<script>
		$(document).ready(function() {
			var j = <?php echo ($j) ? $j : 1; ?>;
			$(document).on('change', ".selectgst", function(event) {
				var id = $(this).attr('data-id');
				j++;
				$('#total_gst_row').val(j);
				var value = $(this).val();
				var name = $(this).find('option:selected').attr('data-id');
				$(this).val('');
				var html = '';
				html += '<tr class="row_id_' + id + '" id="gst_row_' + j + '"><td ></td><td></td><td class="text-right" colspan="3"><strong>Tax</strong></td><td colspan="1"><select class="form-control single_select changesel"  name="tax[' + id + '][]" id="tax_' + id + '_' + j + '" style="text-transform: capitalize"><option value="" selected>Select Tax</option><?php foreach ($gsts as $key => $gst) { ?><option value="<?php echo $gst->name; ?> <?php echo $gst->rate; ?>%" data-rate="<?php echo $gst->rate; ?>"  data-name="<?php echo $gst->name; ?>"><?php echo $gst->name; ?> <?php echo $gst->rate; ?>%</option><?php } ?></select></td><td class="text-right" id="tax_text_' + id + '_' + j + '">0.00</td><td class="text-right"><div class="delete-btn"><input type="hidden" name="taxval[' + id + '][]" id="tax_val_' + id + '_' + j + '" value="0.00"><a class="delete remove_gst_row" id="' + j + '" href="javascript:;" title="Remove row"><i class="fa fa-trash" aria-hidden="true"></i></a></div></td></tr>';
				$(this).closest("tr").before(html);
				$('#tax_' + id + '_' + j + '').val(value);
				$('#select2-tax_' + id + '_' + j + '-container').text(name);
				$('.single_select').select2();
				inv_cal_final_total();
			});

			$(document).on('click', '.remove_gst_row', function() {
				var row_id = $(this).attr("id");
				$('#gst_row_' + row_id).remove();
				inv_cal_final_total();
			});

			$(document).on('change', ".changesel", function(event) {
				inv_cal_final_total();
			});
		});
	</script>
<?php } ?>

<script>
	const arr = [];
	var i = 1000;
	$('#add_serial').click(function() {
		var serial = $('#serial_no').val();
		if (serial == "") {
			$.toast({
				heading: "Enter serial no.",
				text: "",
				position: 'top-right',
				loaderBg: '#fff',
				icon: 'error',
				hideAfter: 1000
			});
			return false;
		}
		var check = arr.includes(serial);
		if (check) {
			$.toast({
				heading: "Duplicate serial no not allowed",
				text: "",
				position: 'top-right',
				loaderBg: '#fff',
				icon: 'error',
				hideAfter: 1000
			});
			return false;
		}
		arr.push(serial);
		var output = '<tr id="close_"><th width="100%" style="text-align:left"><input type="checkbox" name="serial_check[]"  value="' + serial + '" id="serial_' + i + '" checked/><label for="serial_' + i + '">' + serial + '</label></th></tr>';
		$('#InvserialNo_html').prepend(output);
		$('#serial_no').val('');
		i++;
	});

	$('#store_serial').click(function() {
		var sr_no = $('#sr_no').val();
		var j = $(':checkbox[name=serial_check\\[\\]]:checked').length;

		var product_id = $('#product_id').val();
		var sr_qty = $('#sr_qty').val();
		var serqtys = $('#qty_' + sr_no).val();
		if (j > 0) {
			$('#qty_' + sr_no).val(j);
		}
		var serial = jQuery.map($(':checkbox[name=serial_check\\[\\]]:checked'), function(n, i) {
			return n.value;
		}).join(',');

		$('#serial_no_' + sr_no).val(serial);
		$('#text_serial_no_' + sr_no).val(serial);
		$('#InvserialNo').modal('hide');
		$('#InvserialNo_html').html("");
		$('#serial_no').val('');
		inv_cal_final_total();
	})

	$('.close_seroalmodal').click(function() {
		$('#InvserialNo').modal('hide');
		$('#InvserialNo_html').html("");
	})
	$("#serial_no").keyup(function(event) {
		if (event.keyCode === 13) {
			$("#add_serial").click();
		}
	});
</script>
<script>
	function openSerialModal(product_id, id) {
		var serial_no_ = $('#serial_no_' + id).val();
		var base_url = $('#base_url').val();
		var url = base_url + 'admin/estimate/serialnomodal/' + product_id + '/' + id;
		$.post(url, {
				data: 'value',
				'serial_no': serial_no_,
				'csrf_test_name': csrf_token
			}, function(json) {
				if (json.st == 1) {
					$('#InvserialNo_html').append(json.loaded);
					$('#sr_no').val(json.key);
					$('#InvserialNo').modal('show');
				} else {
					$('#InvserialNo_html').append(json.loaded);
					$('#sr_no').val(json.key);
					$('#InvserialNo').modal('show');
				}
			},
			'json');
	}
	changeFunction();

	function changeFunction() {
		var tax_format = $("#tax_format").val();

		if (tax_format == 'GST Number') {
			$("#vat_code_show").show();
			$("#text_name_change").text("GST Number");
		} else if (tax_format == 'Tax Number') {
			$("#vat_code_show").show();
			$("#text_name_change").text("Tax Number");
		} else if (tax_format == 'Vat Number') {
			$("#vat_code_show").show();
			$("#text_name_change").text("Vat Number");
		} else if (tax_format == 'Tax/Vat Number') {
			$("#vat_code_show").show();
			$("#text_name_change").text("Tax/Vat Number");
		} else {
			$("#vat_code_show").hide();
		}
	}
</script>