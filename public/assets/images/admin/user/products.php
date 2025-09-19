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

	.hsn>tbody>tr>td,
	.hsn>tbody>tr>th,
	.hsn>tfoot>tr>td,
	.hsn>tfoot>tr>th,
	.hsn>thead>tr>td,
	.hsn>thead>tr>th {
		border: 1px solid #f4f4f4;
		word-wrap: anywhere;
	}

	.hsn>thead>tr>th {
		border: 1px solid #f4f4f4 !important;
	}

	.highlight {
		background-color: yellow
	}

	mark,
	.mark {
		color: #131111 !important;
		border-radius: .143rem !important;
	}

	.mark,
	mark {
		padding: .2em !important;
		background-color: #e8ca30 !important;
	}
</style>
<style type="text/css">
	input.inv-dpick.form-control.datepicker {
		height: 45px;
		padding-left: 10px;
		border-radius: 10px 0px 0px 10px !important;
		border-right: none !important;
		background: #fff;
		border-color: #fff !important;
	}

	.input-group .input-group-addon {
		border-radius: 0;
		border-color: #fff;
		background-color: #fff;
		border-radius: 0px 10px 10px 0px;
	}

	.spce-bt p {
		margin-bottom: 5px !important;
	}

	.select2-container--default .select2-selection--single {
		background-color: #fff;
		border: 1px solid #f1f1f1 !important;
		border-radius: 10px;
	}

	.form-control {
		/*		height: 35px;*/
	}

	select.form-control {
		height: 35px !important;
		margin: 0px 6px;
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
		border-radius: 10px;
		padding: 11px 12px !important;
		height: auto;
	}
</style>

<div class="content-wrapper">

	<!-- Main content -->
	<section class="content">

		<div class="col-md-12 m-auto box add_area" style="display: <?php if ($page_title == "Edit") { echo "block"; } else { echo "none"; } ?>">
			<div class="box-header d-flex with-border bg-light1 justify-content-between f-no align-items-center">
				<?php if (isset($page_title) && $page_title == "Edit") : ?>
					<h3 class="box-title"><i class="flaticon-box-1"></i>&nbsp;<?php echo trans('edit-product') ?></h3>
				<?php else : ?>
					<h3 class="box-title"><i class="flaticon-box-1"></i>&nbsp;<?php echo trans('add-product') ?> </h3>
				<?php endif; ?>
				<?php if (isset($page_title) && $page_title == "Edit") : ?>
					<a href="<?php echo base_url('admin/product/all/' . $type) ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
				<?php else : ?>
					<a href="#" class="pull-right btn btn-info rounded btn-sm cancel_btn"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
				<?php endif; ?>
			</div>

			<div class="box-body">
				<form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form row edit_re" action="<?php echo base_url('admin/product/add') ?>" role="form" novalidate>
					<div class="col-lg-6 form-group">
						<label>Types <span class="text-danger">*</span> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="Product Types"></i></label>
						<div class="radio radio-info radio-inline mt-10">
							<input type="radio" id="good" value="1" name="pro_type" <?php if (isset($page_title) && $page_title == "Edit") { if ($product[0]['pro_type'] == 1) { echo "checked"; } } else { echo "checked"; } ?>>
							<label class="text-primary" for="good"> Goods </label>
							<input type="radio" id="service" value="2" name="pro_type" <?php if ($product[0]['pro_type'] == 2) { echo "checked"; } ?>>
							<label class="text-primary" for="service"> Service </label>
						</div>
					</div>
					<?php if ($this->business->hsn_sac == 1) : ?>
						<div class="col-lg-6 form-group" id="good_show">
							<label>HSN Code <span class="text-danger">*</span></label>
							<div class="d-flex align-items-center">
								<input type="number" min="0" class="form-control" name="hsn_code" id="hsn_code" value="<?php echo html_escape($product[0]['hsn_code']); ?>" style="border-top-right-radius: 0; border-bottom-right-radius: 0;" onkeyup="return this.value = this.value.replace(/[^0-9\.]/g,'');">
								<button type="button" class="srch-btn" data-toggle="modal" data-target="#Hsn_code"><i class="fa fa-search" aria-hidden="true"></i></button>
							</div>
						</div>

						<div class="col-lg-6 form-group" style="display:none;" id="service_show">
							<label>SAC Code <span class="text-danger">*</span></label>
							<div class="d-flex align-items-center">
								<input type="number" min="0" class="form-control" name="sac_code" id="sac_code" value="<?php echo html_escape($product[0]['hsn_code']); ?>" style="border-top-right-radius: 0; border-bottom-right-radius: 0;" onkeyup="return this.value = this.value.replace(/[^0-9\.]/g,'');">
								<button type="button" class="srch-btn" data-toggle="modal" data-target="#Sca_code"><i class="fa fa-search" aria-hidden="true"></i></button>
							</div>
						</div>
					<?php endif; ?>
					<div class="col-lg-6 form-group">
						<label><?php echo trans('product-name') ?> <span class="text-danger">*</span></label>
						<input type="text" class="form-control" required name="name" value="<?php echo html_escape($product[0]['name']); ?>">
					</div>

					<div class="col-lg-6 form-group">
						<label><?php echo trans('price') ?> <span class="text-danger">*</span></label>
						<input type="text" class="form-control" required name="price" value="<?php echo html_escape($product[0]['price']); ?>" onkeyup="return this.value = this.value.replace(/[^0-9\.]/g,'');">
					</div>

					<?php
					if ($this->business->enable_stock == 1) :
						if ($this->business->enable_serial_no != 1) : ?>
							<div class="col-lg-6 form-group">
								<label><?php echo trans('stock-quantity') ?> <span class="text-danger">*</span></label>
								<input type="text" class="form-control" required name="quantity" value="<?php echo html_escape($product[0]['quantity']); ?>" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
							</div>
						<?php else : ?>
							<div class="col-lg-6 form-group">
								<label><?php echo trans('stock-quantity') ?> <span class="text-danger">*</span></label>
								<div class="d-flex align-items-center">
									<input type="text" class="form-control" id="serial_quantity" required name="quantity" value="<?php echo html_escape(isset($product[0]['quantity'])) ? $product[0]['quantity'] : 0; ?>" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
									<button type="button" class="srch-btn" style="width: 160px;" id="openserialmodal">Add Serial No</button>
								</div>
								<input type="hidden" value="<?php echo html_escape($product[0]['serial_no']); ?>" name="serial_no" id="serialno_value">
							</div>
						<?php
						endif ?>
					<?php else : ?>
						<input type="hidden" class="form-control" name="quantity" value="<?php echo html_escape($product[0]['quantity']); ?>">
					<?php endif ?>

					<div class="col-lg-6 form-group">
						<label>Unit</label>
						<select class="form-control single_select" name="unit" style="width: 100%;">
							<option value="0"><?php echo trans('select') ?></option>
							<?php foreach ($units as $unit) : ?>
								<option value="<?php echo html_escape($unit->unit); ?>" <?php echo ($product[0]['unit'] == $unit->unit) ? 'selected' : ''; ?>>
									<?php echo html_escape($unit->unit); ?>
								</option>
							<?php endforeach ?>
						</select>
					</div>

					<?php if ($type == 'buy') : ?>
						<input type="hidden" name="is_buy" value="1">
						<input type="hidden" name="is_sell" value="0">
					<?php else : ?>
						<input type="hidden" name="is_sell" value="1">
						<input type="hidden" name="is_buy" value="0">
					<?php endif ; ?>
					<?php
					if ($type == 'buy' && $type == 'sell') : 
						if ($type == 'buy') : ?>
							<div class="col-lg-6 form-group col-md-12 expense_list">
								<label><?php echo trans('expense-category') ?> </label>
								<select class="form-control single_select" name="expense_category" style="width: 100%;" id="expense_category">
									<option value="0"><?php echo trans('select') ?></option>
									<?php foreach ($expense_category as $expense) : ?>
										<option value="<?php echo html_escape($expense->id); ?>" <?php echo ($product[0]['expense_category'] == $expense->id) ? 'selected' : ''; ?>>
											<?php echo html_escape($expense->name); ?>
										</option>
									<?php endforeach ?>
								</select>
							</div>
							<input type="hidden" name="is_buy" value="1">
							<input type="hidden" name="is_sell" value="0">
					<?php else : ?>
							<div class="col-lg-6 form-group col-md-12 income_list">
								<label><?php echo trans('income-category') ?> </label>
								<select class="form-control single_select" name="income_category" id="income_category" style="width: 100%;">
									<option value="0"><?php echo trans('select') ?></option>
									<?php foreach ($income_category as $income) : ?>
										<option value="<?php echo html_escape($income->id); ?>" <?php echo ($product[0]['income_category'] == $income->id) ? 'selected' : ''; ?>>
											<?php echo html_escape($income->name); ?>
										</option>
									<?php endforeach ?>
								</select>
							</div>
							<input type="hidden" name="is_sell" value="1">
							<input type="hidden" name="is_buy" value="0">
					<?php
						endif ;
					endif ;
					?>

					<div class="col-lg-12 form-group m-t-30">
						<input type="checkbox" id="md_checkbox_11" class="filled-in chk-col-blue" value="1" name="is_both" <?php if ($product[0]['is_sell'] == 1 && $product[0]['is_buy'] == 1) {
																																echo "checked";
																															} ?>>
						<label for="md_checkbox_11"> <?php echo trans('product-both') ?></label>
					</div>
					<div class="col-lg-6 form-group">
						<label>Product Image</label>
						<input type="file" class="form-control" name="product_image">
					</div>
					<div class="col-lg-6 form-group">
						<label><?php echo trans('product-details') ?></label>
						<textarea class="form-control" name="details" rows="6"><?php echo html_escape($product[0]['details']); ?></textarea>
					</div>
					<input type="hidden" name="id" value="<?php echo html_escape($product['0']['id']); ?>">
					<input type="hidden" name="type" value="<?php echo html_escape($type); ?>">
					<!-- csrf token -->
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

					<div class="col-sm-12">
						<?php if (isset($page_title) && $page_title == "Edit") : ?>
							<button type="submit" class="btn btn-info rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
						<?php else : ?>
							<button type="submit" class="btn btn-info rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
						<?php endif; ?>
					</div>
				</form>
			</div>
		</div>


		<?php if (isset($page_title) && $page_title != "Edit") : ?>

			<div class="list_area">
				<form method="GET" class="sort_invoice_form" action="<?php echo base_url('admin/product/all/sell') ?>">
					<div class="row p-15 mt-20 mb-20" style="padding-right: 0 !important;">
						<div class="col-lg-12 p-0 mb-2">
							<p class="mb-5"><a href="<?php echo base_url('admin/product/all/sell') ?>" class="view_link bg-border">Clear Filter</a></p>
						</div>
						<div class="col-lg-3 col-xs-12 mt-5 pl-0">
							<label>Product Name</label>
							<select class="form-control single_select sort" name="product_name">
								<option value="">Select Product name</option>
								<?php foreach ($products_list as $list) { ?>
									<?php
									$isSelected = (isset($_GET['product_name']) && $_GET['product_name'] == html_escape($list->name)) ? 'selected' : '';
									?>
									<option value="<?php echo html_escape($list->name); ?>" <?php echo $isSelected; ?>>
										<?php echo html_escape($list->name); ?>
									</option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-3 col-xs-12 mt-5 pl-0">
							<label>Unit</label>
							<select class="form-control single_select sort" name="unit">
								<option value="">-- Select Unites --</option>
								<?php foreach ($units as $unit) : ?>
									<option value="<?php echo html_escape($unit->unit); ?>" <?php echo (isset($_GET['unit']) && $_GET['unit'] == $unit->unit) ? 'selected' : ''; ?>>
										<?php echo html_escape($unit->unit); ?>
									</option>
								<?php endforeach ?>
							</select>
						</div>

						<!-- <div class="col-lg-3 mt-5 pl-0">
							<div class="input-group">
								<input type="text" class="inv-dpick form-control datepicker" placeholder="<?php echo trans('from') ?>" name="start_date" value="<?php if (isset($_GET['start_date'])) {
																																									echo $_GET['start_date'];
																																								} ?>" autocomplete="off">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
						</div>

						<div class="col-lg-3 mt-5 pl-0">
							<div class="input-group">
								<input type="text" class="inv-dpick form-control datepicker" placeholder="<?php echo trans('to') ?>" name="end_date" value="<?php if (isset($_GET['end_date'])) {
																																								echo $_GET['end_date'];
																																							} ?>" autocomplete="off">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
						</div> -->

						<div class="col-lg-2 mt-5 pl-0">
							<label>HSN Code</label>
							<div class="input-group">
								<input type="text" class="form-control" placeholder="Enter HSN Code #" name="hsn_code" value="<?php if (isset($_GET['hsn_code'])) {
																																	echo $_GET['hsn_code'];
																																} ?>" autocomplete="off">
							</div>
						</div>

						<div class="col-lg-1 mt-5 pl-0">
							<button type="submit" class="btn btn-info btn-report btn-block custom_search mt-30"><i class="flaticon-magnifying-glass"></i></button>
						</div>
					</div>
				</form>


				<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0 over_scroll">
					<div class="card-box">
						<?php if (isset($page_title) && $page_title == "Edit") : ?>
							<h3 class="box-title"><?php echo trans('edit-product') ?></h3>
							<div class="add-btn">
								<a href="<?php echo base_url('admin/product') ?>" class="pull-right btn btn-primary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
							</div>
						<?php else : ?>
							<div class="d-flex bg-light1 justify-content-between align-items-center loan_re">
								<h3 class="box-title"> <i class="flaticon-box-1"></i>&nbsp;(<?php if ($type == 'buy') {
																								echo trans('purchases');
																							} else {
																								echo trans('sales');
																							} ?>) Stocks & Services </h3>
								<div class="add-btn add_new">
									<a href="#" class="btn btn-info btn-sm add_btn rounded"><i class="fa fa-plus"></i> <?php echo trans('add-product') ?></a>
								</div>
							</div>
						<?php endif; ?>
						<table class="table table-hover cushover">
							<thead>
								<tr>
									<th>#</th>
									<th><?php echo trans('name') ?></th>
									<th>
										HSN / SAC Code
									</th>
									<th class="text-right"><?php echo trans('price') ?></th>
									<?php if ($this->business->enable_stock == 1) : ?>
										<th class="text-center"><?php echo trans('quantity') ?></th>
									<?php endif; ?>
									<th class="text-center">Unit</th>
									<th><?php echo trans('type') ?></th>
									<!--<th>Image</th>-->
									<th><?php echo trans('action') ?></th>
								</tr>
							</thead>
							<tbody>
								<?php $i = 1;
								foreach ($products as $product) : ?>
									<tr id="row_<?php echo html_escape($product->id); ?>">

										<td><?php echo $i; ?></td>
										<td style="text-transform: capitalize">
											<p><?php echo html_escape($product->name); ?></p>
											<p class="text-muted" style="text-wrap: balance;"><?php echo html_escape($product->details); ?></p>
										</td>
										<td>
										<span class="custom-label-sm label-light-<?php echo ($product->pro_type == 1) ? 'success' : 'info'; ?> mr-2">
											<?php echo ($product->pro_type == 1) ? 'HSN' : 'SAC'; ?>
										</span>
										<span class="font-weight-700">
											<?php echo $product->hsn_code ; ?>
										</span>
										</td>
										<td class="text-right"><?php echo html_escape($this->business->currency_symbol . '' . decimal_format($product->price, 2)); ?></td>

										<?php if ($this->business->enable_stock == 1) : ?>
											<td class="text-center"><?php echo html_escape($product->quantity); ?></td>
										<?php endif; ?>
										<td class="text-center" style="text-transform: capitalize"><?php
																									if (!empty($product->unit)) {
																										echo html_escape($product->unit);
																									} else {
																										echo " ";
																									}
																									?>
										</td>
										<td>
											<?php if ($product->is_buy == 1) : ?>
												<label class="label gren"><?php echo trans('purchases'); ?></label>
											<?php endif ?>

											<?php if ($product->is_sell == 1 || $product->is_buy == 0) : ?>
												<label class="label blue"><?php echo trans('sales'); ?></label>
											<?php endif ?>
										</td>
										<!--<td><?php //if(!empty($product->product_image)) { 
												?><label class="label label-default"><?php //echo html_escape($product->product_image); 
																						?></label><?php //} 
																									?></td>-->
										<td class="actions" width="12%">
											<a href="<?php echo base_url('admin/product/edit/' . html_escape($product->id) . '/' . $type); ?>" class="on-default edit-row" data-placement="top" title="Edit"><i class="fa fa-pencil"></i></a> &nbsp;

											<a data-val="Category" data-id="<?php echo html_escape($product->id); ?>" href="<?php echo base_url('admin/product/delete/' . html_escape($product->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a> &nbsp;
											<?php if (!empty($product->product_image)) { ?>
												<a href="<?php echo base_url('uploads/files/' . html_escape($product->product_image)); ?>" class="on-default edit-row" download data-toggle="tooltip" data-placement="top" title="Download Product Image"><i class="fa fa-download"></i></a>
											<?php } ?>
										</td>
									</tr>
								<?php $i++;
								endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</section>
</div>

<div class="modal fade" id="Hsn_code" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-zoom modal-lg">
		<div class="tab-content" style="padding: 2%">
			<div class="tab-pane in active">
				<div class="modal-content modal-lg">
					<div class="modal-header">
						<h4 class="modal-title" id="vcenter">Find HSN Code</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					</div>
					<div class="modal-body" id="existing_query">
						<div class="row align-items-center">
							<div class="col-12 form-group">
								<label>Search HSN Code for your item</label>
								<input type="text" class="form-control" id="hsn_search" placeholder="Please enter 2 or more characters">
							</div>
							<div class="col-12 text-center" id="hsn_loader" style="display:none">
								<img src="<?php echo base_url(); ?>/assets/Fountain.gif">
							</div>
							<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive" style="display:none" id="hsn_table">
								<table class="table table-hover hsn">
									<thead>
										<tr>
											<th width="30%"><label>HSN Code</label></th>
											<th width="70%">Description</th>
										</tr>
									</thead>
									<tbody id="hsn_body">

									</tbody>
								</table>
							</div>

							<div class="col-12" style="margin-top: 4%;"></div>
						</div>
						<br>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="Sca_code" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-zoom modal-lg">
		<div class="tab-content" style="padding: 2%">
			<div class="tab-pane in active">
				<div class="modal-content modal-lg">
					<div class="modal-header">
						<h4 class="modal-title" id="vcenter">Find SAC Code</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					</div>
					<div class="modal-body" id="existing_query">
						<div class="row align-items-center">
							<div class="col-12 form-group">
								<label>Search SAC Code for your item</label>
								<input type="text" class="form-control" id="sac_search" name="srch_hsn" placeholder="Please enter 2 or more characters">
							</div>
							<div class="col-12 text-center" id="sac_loader" style="display:none">
								<img src="<?php echo base_url(); ?>/assets/Fountain.gif">
							</div>
							<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive" style="display:none" id="sac_table">
								<table class="table table-hover hsn searchtbl">
									<thead>
										<tr>
											<th width="30%"><label>SAC Code</label></th>
											<th width="70%">Description</th>
										</tr>
									</thead>
									<tbody id="sac_body">

									</tbody>
								</table>
							</div>

							<div class="col-12" style="margin-top: 4%;"></div>
						</div>
						<br>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="serialNo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="tab-content" style="padding: 2%">
			<div class="tab-pane in active">
				<div class="modal-content modal-lg" style="width: 60%;">
					<div class="modal-header">
						<h4 class="modal-title" id="vcenter">Opening Stock</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
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
									<thead id="serialNo_html">
										<?php
										if (isset($page_title) && $page_title == "Edit") {
											$serial_no = explode(',', $product[0]['serial_no']);

											if (count($serial_no) > 0) {
												foreach ($serial_no as $key => $row) {
													if (!empty($row)) {
										?>
														<tr id="close_<?php echo $key + 1; ?>">
															<th width="100%" style="text-align:left"><label><?php echo $row; ?></label></th>
															<th width="30%" style="text-align:right"><button type="button" class="close remove_serialno" data-id="<?php echo $row; ?>" onclick="remove_serial(this,<?php echo $key + 1; ?>)">×</button></th>
														</tr>
										<?php
													}
												}
											}
										}
										?>
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
	$(document).ready(function() {
		var protype = $('input[name="pro_type"]:checked').val();
		if (protype == 1) {
			$('#good_show').show();
			$('#service_show').hide();
		} else {
			$('#service_show').show();
			$('#good_show').hide();
		}
	});

	$('#openserialmodal').click(function() {
		$('#serialNo').modal('show');
	});

	const arr = <?php echo (isset($page_title) && $page_title == "Edit") ? json_encode($serial_no) : json_encode([]); ?>;
	var i = <?php echo (isset($page_title) && $page_title == "Edit") ? count($serial_no) + 1 : 1; ?>;

	$('#add_serial').click(function() {
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
		var output = '<tr id="close_' + i + '"><th width="100%" style="text-align:left"><label>' + serial + '</label></th><th width="30%" style="text-align:right"><button type="button" class="close remove_serialno" data-id="' + serial + '" onclick="remove_serial(this,' + i + ')">×</button></th></tr>';
		$('#serialNo_html').append(output);
		$('#serial_no').val('');
		i++;
	});
	$("#serial_no").keyup(function(event) {
		if (event.keyCode === 13) {
			$("#add_serial").click();
		}
	});
	$('#store_serial').click(function() {
		var sr_qty = $('#sr_qty').val();
		var serqtys = $('#serial_quantity').val();
		var sr_no = $('#sr_no').val();

		if (serqtys == 0) {
			$('#serial_quantity').val(sr_qty);
		}
		if (serqtys <= i) {
			$('#serial_quantity').val(sr_qty);
		}

		$('#serialno_value').val(sr_no);
		$('#serialNo').modal('hide');
	})

	function remove_serial(obj, del) {
		var serno = $(obj).attr('data-id');
		$('#close_' + del + '').remove();
		--i;
		const index = arr.indexOf(serno);
		if (index > -1) {
			arr.splice(index, 1);
		}
		$('#sr_no').val(arr);
		$('#sr_qty').val(i - 1);
	}

	$('#good').change(function() {
		$('#good_show').show();
		$('#openserialmodal').show();
		$('#service_show').hide();
		$("#hsn_code").removeAttr('required');
		$("#sac_code").attr('required', '');
	});

	$('#service').change(function() {
		$('#service_show').show();
		$('#openserialmodal').hide();
		$('#good_show').hide();
		$("#sac_code").removeAttr('required');
		$("#hsn_code").attr('required', '');
	});

	$('#hsn_search').keyup(function() {
		var hsn = $(this).val();
		if (hsn.length > 2) {
			$('#hsn_loader').show();
			$.post("<?php echo base_url(); ?>/admin/product/hsn_search", {
				hsn: hsn
			}, function(data) {
				$('#hsn_loader').hide();
				$('#hsn_body').html(data);
				$('#hsn_table').show();
				$('table').find('tr td').each(function() {
					if ($(this).attr('data-search') !== 'false') {
						var text = $(this).text();
						var textL = text.toLowerCase();
						var position = textL.indexOf(hsn.toLowerCase());

						var regex = new RegExp(hsn, 'ig');
						text = text.replace(regex, (match, $1) => {
							// Return the replacement
							return '<mark>' + match + '</mark>';
						});

						$(this).html(text);

						if (position !== -1) {
							setTimeout(function() {
								if ($(this).parent().find('mark').is(':empty')) {
									$('mark').remove();
								}
							}.bind(this), 0);
						} else {
							$(this).text(text);
						}
					}
				});
			});
		} else {
			$('#hsn_loader').hide();
			$('#hsn_table').hide();
		}
	});

	function set_hsn_code(hsncode) {
		$('#hsn_code').val(hsncode)
		$('#Hsn_code').modal('hide');
	}

	$('#sac_search').keyup(function() {
		var sac = $(this).val();
		if (sac.length > 2) {
			$('#sac_loader').show();
			$.post("<?php echo base_url(); ?>/admin/product/sac_search", {
				hsn: sac
			}, function(data) {
				$('#sac_loader').hide();
				$('#sac_body').html(data);
				$('#sac_table').show();
				$('table').find('tr td').each(function() {
					if ($(this).attr('data-search') !== 'false') {
						var text = $(this).text();
						var textL = text.toLowerCase();
						var position = textL.indexOf(sac.toLowerCase());

						var regex = new RegExp(sac, 'ig');
						text = text.replace(regex, (match, $1) => {
							// Return the replacement
							return '<mark>' + match + '</mark>';
						});

						$(this).html(text);

						if (position !== -1) {
							setTimeout(function() {
								if ($(this).parent().find('mark').is(':empty')) {
									$('mark').remove();
								}
							}.bind(this), 0);
						} else {
							$(this).text(text);
						}
					}
				});

			});
		} else {
			$('#sac_loader').hide();
			$('#sac_table').hide();
		}
	});

	function set_sca_code(scacode) {
		$('#sac_code').val(scacode)
		$('#Sca_code').modal('hide');
	}
</script>