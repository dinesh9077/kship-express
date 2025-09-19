<div class="content-wrapper">
	<?php $settings = get_settings(); ?>
	<!-- Main content -->
	<section class="content">
		<div class="container">
			<div class="col-md-12 m-auto">

				<?php if (empty($this->business->logo)) : ?>
					<?php include 'include/setup_alert.php'; ?>
				<?php endif ?>

				<div class="row mb-10">
					<div class="col-md-12">
						<h2><i class="flaticon-approve-invoice"></i>&nbsp;<?php if (isset($page_title) && $page_title == 'Edit Estimate') {
																				echo "Edit";
																			} else {
																				echo trans('create-new-estimate');
																			} ?>
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

													<p class="mb-0 pull-right"><strong><?php echo html_escape($this->business->name) ?></strong></p><br>
													<p class="pull-right"><?php echo html_escape($this->business->country) ?></p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- invoice body -->
						<div class="box shadow-lg">

							<div class="box-body inv">
								<div class="container">

									<div class="row mb-20">

										<div class="col-xs-12 col-md-12">

											<div class="row inv-info">
												<div class="col-xs-6 col-md-4 text-left">
													<h5><?php echo trans('estimate-to') ?></h5>

													<div id="load_customers">
														<?php include 'include/invoice_load_customers.php'; ?>
													</div>

													<a data-toggle="modal" href="#customerModal" title="Add a row" class="add-new-item btn btn-block btn-default btn-sm p-10"><i class="icon-plus"></i> <?php echo trans('add-a-customer') ?></a>

													<div class="mt-20" id="load_info"></div>
												</div>

												<div class="col-xs-6 col-md-8 text-right">
													<div class="form-group row">
														<label for="inputEmail3" class="col-sm-8 text-right control-label col-form-label"><?php echo trans('estimate-number') ?></label>
														<div class="col-sm-4">
															<?php if (isset($page_title) && $page_title == 'Edit Estimate') : ?>
																<input type="text" class="form-control" name="number" value="<?php echo html_escape($invoice[0]['number']) ?>" placeholder="<?php echo trans('estimate-number') ?>">
															<?php else : ?>
																<input type="text" class="form-control" name="number" value="<?php echo get_auto_invoice_number(2, 0) ?>">
															<?php endif ?>
														</div>
													</div>

													<div class="form-group row">
														<label for="inputEmail3" class="col-sm-8 text-right control-label col-form-label"><?php echo trans('p.o.s.o.-number') ?></label>
														<div class="col-sm-4">
															<input type="text" value="<?php echo html_escape($invoice[0]['poso_number']) ?>" class="form-control" name="poso_number">
														</div>
													</div>

													<div class="form-group row">
														<label for="inputEmail3" class="col-sm-8 text-right control-label col-form-label"><?php echo trans('estimate-date') ?></label>
														<div class="col-sm-4">
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

													<div class="form-group row mt-10">
														<label for="inputEmail3" class="col-sm-8 text-right control-label col-form-label"><?php echo trans('expires-on') ?></label>
														<div class="col-sm-4">
															<div class="input-group">
																<input type="text" class="form-control datepicker" placeholder="yyyy/mm/dd" name="expire_on" value="<?php echo date('Y-m-d') ?>">
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
												<table class="table">
													<thead>
														<tr class="item-row">
															<th><?php echo trans('item') ?></th>
															<th>Serial No.</th>
															<th>HSN/SAC Code</th>
															<th>Item Description</th>
															<th>Price Per Unit</th>
															<th><?php echo trans('quantity') ?></th>
															<th>Unit</th>
															<th><?php echo trans('total') ?></th>
														</tr>
													</thead>
													<tbody>

														<?php if (isset($page_title) && $page_title == 'Edit Estimate') : ?>
															<?php $items = helper_get_invoice_items($invoice[0]['id']) ?>
															<?php foreach ($items as $product) : ?>
																<tr class="item-row">
																	<td>
																		<input type="text" class="form-control item" placeholder="Item" type="text" name="items_val[]" value="<?php echo html_escape($product->item_name) ?>">
																		<input type="hidden" class="form-control item" placeholder="Item" type="text" name="items[]" value="<?php echo html_escape($product->item) ?>">
																	</td>
																	<td width="10%">
																		<i class="fa fa-list form-control item ws-180" style="cursor:pointer;" aria-hidden="true" onclick="openSerialModal(<?php echo $product->item ?>,0)"></i>
																	</td>
																	<input type="hidden" class="form-control ws-180" name="serial_no[]" value="<?php echo html_escape($product->serial_no) ?>" id="serial_no_<?php echo html_escape($product->item) ?>">
																	<td>
																		<input class="form-control" placeholder="HSN / SAC Code" type="text" name="hsn_sac[]" value="<?php echo html_escape($product->hsn_code) ?>">
																	</td>
																	<td>
																		<textarea name="details[]" class="form-control ac-textarea" rows="1" placeholder="Enter item description"><?php echo html_escape($product->details) ?></textarea>
																	</td>
																	<td>
																		<input class="form-control price invo" placeholder="Price" type="text" name="price[]" value="<?php echo html_escape($product->price) ?>">
																	</td>
																	<td>
																		<input class="form-control qty" placeholder="Quantity" type="text" name="quantity[]" value="<?php echo html_escape($product->qty) ?>" id="qty_<?php echo html_escape($product->item) ?>">
																	</td>
																	<td>
																		<input class="form-control" placeholder="Unit" type="text" name="unit[]" value="<?php echo html_escape($product->unit) ?>">
																	</td>
																	<td width="15%">
																		<div class="delete-btn">
																			<span class="currency_wrapper"></span>
																			<span class="total"><?php echo html_escape($product->price) ?></span>
																			<a class="delete" href="javascript:;" title="Remove row"><i class="fa fa-trash" aria-hidden="true"></i></a>
																			<input class="total" type="hidden" name="total_price[]" value="<?php echo html_escape($product->price) ?>">
																			<input type="hidden" name="product_ids[]" value="<?php echo html_escape($product->item) ?>">
																		</div>
																	</td>
																</tr>
															<?php endforeach ?>
														<?php endif ?>


														<thead id="add_item">

														</thead>


														<tr>
															<td colspan="8" class="p-0 text-center">
																<a href="#" class="btn btn-default add_item_btn"><i class="icon-plus"></i> <?php echo trans('add-an-item') ?></a>
															</td>
														</tr>

														<tr id="products_list_inv" style="display: none;">
															<td colspan="8" class="p-0">
																<div class="inv-product br-10 dshadow">
																	<div class="form-group has-search">
																		<span class="icon-magnifier form-control-feedback"></span>
																		<input type="text" class="form-control search_product" placeholder="Type product">
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


														<tr>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td class="text-right"><strong><?php echo trans('sub-total') ?></strong></td>
															<td>
																<span class="currency_wrapper"></span><span id="subtotal">0.00</span>
																<input type="hidden" class="subtotal" name="sub_total" value="">
															</td>
														</tr>
														<tr>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td class="text-right"><strong><?php echo trans('discount') ?></strong></td>
															<td width="15%">
																<div class="input-group">
																	<input type="text" id="discount" name="discount" value="<?php echo html_escape($invoice[0]['discount']); ?>" class="form-control" aria-describedby="basic-addon2">
																	<div class="input-group-append discount">
																		<span class="input-group-text" id="basic-addon1">%</span>
																	</div>
																</div>
															</td>
														</tr>
														<?php foreach ($gsts as $key => $gst) : ?>

															<tr>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td class="text-right"><strong><?php echo $gst->name ?></strong></td>
																<td class="inv-width">
																	<select class="form-control tax_id" data-id="<?php echo $gst->id ?>" id="tax_id_<?php echo $gst->id ?>" name="taxes[]">
																		<option value="0"><?php echo trans('select-tax') ?></option>
																		<?php foreach ($gst->taxes as $tax) : ?>

																			<?php $selected = '';
																			$tax_id = ''; ?>
																			<?php foreach ($asign_taxs as $asign_tax) : ?>
																				<?php if ($asign_tax->tax_id == $tax->id) : ?>
																					<?php $selected = 'selected';
																					$tax_id = $tax->id;

																					break; ?>
																				<?php else : ?>
																					<?php $selected = ''; ?>
																				<?php endif ?>
																			<?php endforeach ?>

																			<option <?php echo $selected; ?> value="<?php echo html_escape($tax->id) ?>"><?php echo html_escape($tax->name) ?> - <?php echo html_escape($tax->rate) ?>%</option>
																		<?php endforeach ?>
																	</select>
																	<input type="hidden" class="tax" id="tax_<?php echo $gst->id ?>" value="<?php echo get_invoice_tax($asign_taxs[$key]->tax_id, $invoice[0]['id']); ?>">
																</td>
															</tr>
														<?php endforeach ?>
														<input type="hidden" class="total_tax" id="total_tax" value="<?php if (isset($total_tax)) {
																															echo $total_tax->total;
																														} ?>">


														<tr>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td class="text-right">
																<strong><?php echo trans('grand-total') ?></strong>
															</td>
															<td>
																<span class="currency_wrapper"></span><span id="grandTotal">0</span>
																<input type="hidden" class="grandtotal" name="grand_total" value="">
																<input type="hidden" class="convert_total" name="convert_total" value="">
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="box-footer text-right">
								<input type="hidden" class="currency_code" name="currency_code" value="">
								<strong style="display:none;"><span class="conversion_currency"> </span></strong>
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
													<textarea class="form-control" rows="4" name="footer_note" placeholder="Enter a footer for this estimate (eg. tatx info, thankyou note, etc.)"><?php echo $invoice[0]['footer_note']; ?></textarea>
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
						<div class="col-md-12 text-center p-20">
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
								<label><?php echo trans('customer-name') ?> <span class="text-danger">*</span></label>
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
							<a class="btn btn-info waves-effect pull-right" data-toggle="tab" href="#cus_address">Next</a>
						</div>

						<div id="cus_address" class="tab-pane fade">
							<h3>Billing</h3>
							<div class="form-group">
								<label><?php echo trans('address') ?> </label>
								<textarea class="form-control" name="address"></textarea>
							</div>
							<div class="form-group">
								<label><?php echo trans('business') . ' ' . trans('number') ?></label>
								<input type="text" class="form-control" name="cus_number" value="">
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label p-0" for="example-input-normal"><?php echo trans('country') ?> </label>
								<select class="form-control single_select col-sm-12" id="country" name="country" style="width: 100%">
									<option value=""><?php echo trans('select') ?></option>
									<?php foreach ($countries as $country) : ?>
										<?php if (!empty($country->currency_name)) : ?>
											<option value="<?php echo html_escape($country->id); ?>">
												<?php echo html_escape($country->name); ?>
											</option>
										<?php endif ?>
									<?php endforeach ?>
								</select>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label p-0" for="example-input-normal"><?php echo trans('currency') ?> </label>
								<select class="form-control col-sm-12 wd-100" id="currency" name="currency" disabled>
									<option value=""><?php echo trans('select') ?></option>
									<?php foreach ($countries as $currency) : ?>>
									<?php echo html_escape($currency->currency_code . ' - ' . $currency->currency_name); ?>
									</option>
								<?php endforeach ?>
								</select>
							</div>
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							<button type="submit" class="btn btn-info waves-effect pull-right"><?php echo trans('add-customer') ?></button>
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
<input type="hidden" id="srNoCount" value="1">
<input type="hidden" id="product_id" value="1">
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
							<input type="hidden" id="sr_qty" value="0">
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
<script>
	const arr = <?php echo (isset($page_title) && $page_title == "Edit") ? json_encode($serial_no) : json_encode([]); ?>;

	$('#add_serial').click(function() {
		var i = $('#srNoCount').val();
		var serial = $('#serial_no').val();
		if (serial == "") {
			alert('Enter serial no.');
			return false;
		}
		var check = arr.includes(serial);
		if (check) {
			alert('Duplicate serial no not allowed');
			return false;
		}

		arr.push(serial);
		$('#sr_qty').val(i);
		$('#sr_no').val(arr);
		$('#srNoCount').val(i)
		var output = '<tr id="close_' + i + '"><th width="100%" style="text-align:left"><input type="checkbox" name="serial_check[]" data-id="' + i + '" value="' + serial + '" id="' + i + '" checked/><label for="' + i + '">' + serial + '</label></th></tr>';
		$('#InvserialNo_html').prepend(output);
		$('#serial_no').val('');
		i++;
	});

	$('#store_serial').click(function() {
		var j = $(':checkbox[name=serial_check\\[\\]]:checked').length;

		var product_id = $('#product_id').val();
		var sr_qty = $('#sr_qty').val();
		var serqtys = $('#qty_' + product_id).val();

		var sr_no = $('#sr_no').val();

		if (serqtys == 0) {
			$('#qty_' + product_id).val(j);
		}
		if (serqtys <= j) {
			$('#qty_' + product_id).val(j);
		}
		var outputs = jQuery.map($(':checkbox[name=serial_check\\[\\]]:checked'), function(n, j) {
			return n.value;
		}).join(',');

		$('#serial_no_' + product_id).val(outputs);
		$('#InvserialNo').modal('hide');
		$('#InvserialNo_html').html("");
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
	function openSerialModal(Id, customerId) {
		var serial_no_ = $('#serial_no_' + Id).val();
		var base_url = $('#base_url').val();
		var url = base_url + 'admin/estimate/serialnomodal/' + Id + '/' + customerId;
		$.post(url, {
				data: 'value',
				'serial_no': serial_no_,
				'csrf_test_name': csrf_token
			}, function(json) {
				if (json.st == 1) {
					$('#product_id').val(json.product_id);
					$('#srNoCount').val(json.key);
					$('#InvserialNo_html').append(json.loaded);
					$('#InvserialNo').modal('show');
				} else {
					$('#InvserialNo_html').append(json.loaded);
					$('#product_id').val(json.product_id);
					$('#InvserialNo').modal('show');
				}
			},
			'json');
	}
</script>