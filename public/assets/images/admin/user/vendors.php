<style type="text/css">
	/*	.bg-light1 {
    background: #F1F4F8;
    padding: 4px 19px;
    border-radius: 8px;
}*/
</style>


<div class="content-wrapper">

	<!-- Main content -->
	<section class="content">
		<div class="col-md-12 m-auto box add_area" style="display: <?php if (isset($_SESSION['error']) || $page_title == "Edit") {
																		echo "block";
																	} else {
																		echo "none";
																	} ?>">
			<div class="d-flex justify-content-between align-items-center bg-light1 f-no box-header">
				<?php if (isset($page_title) && $page_title == "Edit") : ?>
					<h3 style="font-size: 26px"><i class="flaticon-group"></i>&nbsp;<?php echo trans('edit-vendor') ?></h3>
					<div class="add-btn">
						<a href="<?php echo base_url('admin/vendor') ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
					</div>
				<?php else : ?>
					<h3 style="font-size: 26px"><i class="flaticon-group"></i>&nbsp;<?php echo trans('add-new-vendor') ?></h3>
					<div class="add-btn">
						<a href="#" class="pull-right btn btn-info rounded btn-sm cancel_btn"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
					</div>
				<?php endif; ?>
			</div>

			<form id="cat-form" method="post" enctype="multipart/form-data" class="row validate-form mt-20 p-30" action="<?php echo base_url('admin/vendor/add') ?>" role="form" novalidate>
				<input type="hidden" name="gst_validation" id="gst_validation" value="0">
				<div class="col-lg-6 form-group">
					<label><?php echo trans('vendor-name') ?>  <span class="text-danger">*</span></label>
					<input type="text" class="form-control" required name="name" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['name'] : html_escape($vendor[0]['name']); ?>">
				</div>

				<div class="col-lg-6 form-group">
					<label><?php echo trans('phone') ?></label>
					<input type="text" class="form-control" name="phone" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['phone'] : html_escape($vendor[0]['phone']); ?>">
				</div>

				<div class="col-lg-6 form-group">
					<label><?php echo trans('email') ?></label>
					<input type="text" class="form-control" name="email" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['email'] : html_escape($vendor[0]['email']); ?>">
				</div>

				<div class="col-lg-6 form-group">
					<label><?php echo trans('address') ?></label>
					<textarea class="form-control" name="address"><?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['address'] : html_escape($vendor[0]['address']); ?></textarea>
				</div>

				<div class="col-lg-12">
					<h4><?php echo trans('billing-information') ?></h4>
					<br>
				</div>

				<!--<div class="col-lg-6 form-group" >
					<label><?php echo trans('business') . ' ' . trans('number') ?></label>
					<input type="text" class="form-control" name="cus_number" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['cus_number'] : html_escape($vendor[0]['cus_number']); ?>" >
                    </div>
                -->

				<div class="col-lg-6 form-group">
					<label>Select Tax Type</label>
					<select class="form-control single_select" style="width:100%" name="tax_format" id="tax_format" onchange="changeFunction();">
						<option value="0"><?php echo trans('select') ?></option>
						<option value="GST Number" <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['tax_format'] == 'GST Number') || $vendor[0]['tax_format'] == 'GST Number') ? 'selected' : ''; ?>>GST Number</option>
						<option value="Tax Number" <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['tax_format'] == 'Tax Number') || $vendor[0]['tax_format'] == 'Tax Number') ? 'selected' : ''; ?>>Tax Number</option>
						<option value="Vat Number" <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['tax_format'] == 'Vat Number') || $vendor[0]['tax_format'] == 'Vat Number') ? 'selected' : ''; ?>>Vat Number</option>
						<option value="Tax/Vat Number" <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['tax_format'] == 'Tax/Vat Number') || $vendor[0]['tax_format'] == 'Tax/Vat Number') ? 'selected' : ''; ?>>Tax/Vat Number</option>
					</select>
				</div>

				<div class="col-lg-6 form-group" id="vat_code_show" <?php if ($vendor[0]['tax_format'] != "" || isset($_SESSION['error'])) {
																		echo "style='display:block;'";
																	} else {
																		echo "style='display:none;'";
																	} ?>>
					<label id="text_name_change"><?php echo $vendor[0]['tax_format']; ?></label>
					<input type="text" class="form-control" name="vat_code" id="edit_vat_code" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['vat_code'] : html_escape($vendor[0]['vat_code']); ?>">
					<span id="lblError" class="error"><?php echo (isset($_SESSION['error']) && isset($_SESSION['gst_error'])) ? $_SESSION['gst_error'] : ''; ?></span>
					<span id="lblSError" class="suerror"></span>
				</div>

				<div class="col-lg-6 form-group">
					<label class="col-sm-2 control-label p-0" for="example-input-normal"><?php echo trans('country') ?> </label>
					<select class="form-control single_select col-sm-12 country" id="country" name="country" style="width: 100%" required>
						<option value="0"><?php echo trans('select') ?></option>
						<?php foreach ($countries as $country) : ?>
							<?php if (!empty($country->currency_symbol)) : ?>
                                    <option value="<?php echo html_escape($country->id); ?>" data-currencyname="<?php echo html_escape($country->name); ?>" data-currency_code="<?php echo html_escape($country->currency_code); ?>" data-currency_symbol="<?php echo html_escape($country->currency_symbol); ?>"  <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['country'] == $country->id) || $vendor[0]['country'] == $country->id) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($country->name); ?>
                                    </option>
							<?php endif ?>
						<?php endforeach ?>
					</select>
				</div>
				<div class="col-lg-6 form-group">
					<label class="col-sm-2 control-label p-0" for="example-input-normal"><?php echo trans('currency') ?> </label>
					<select class="form-control col-sm-12 wd-100 single_select" style="width:100%" id="currency" name="currency">
						<option value=""><?php echo trans('select') ?></option>
						<?php foreach ($countries as $currency) :
							if (!empty($currency->currency_symbol)) : ?>
								<option value="<?php echo $currency->currency_code; ?>" <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['country'] == $currency->id) || $vendor[0]['country'] == $currency->id) ? 'selected' : ''; ?>><?php echo $currency->currency_code . ' - ' . $currency->currency_name; ?></option>
						<?php endif;
						endforeach; ?>
					</select>
				</div>
				<input type="hidden" name="id" value="<?php echo html_escape($vendor['0']['id']); ?>">
				<!-- csrf token -->
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<hr>

				<div class="col-sm-12">
					<?php if (isset($page_title) && $page_title == "Edit") : ?>
                            <input type="hidden" id="hid_country" name="country" value="<?php echo html_escape($vendor[0]['country']); ?>">
                            <input type="hidden" id="hid_currency" name="currency" value="<?php echo html_escape($vendor[0]['currency']); ?>">
						<button type="submit" class="btn btn-info btn-rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
					<?php else : ?>
						<button type="submit" class="btn btn-info btn-rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
					<?php endif; ?>
				</div>

			</form>

		</div>


		<?php if (isset($page_title) && $page_title != "Edit") : ?>
			<div class="list_area" style="display: <?php if (isset($_SESSION['error']) || $page_title == "Edit") {
														echo "none";
													} else {
														echo "block";
													} ?>">
				<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0">
					<div class="card-box">
						<div class="bg-light1">
							<?php if (isset($page_title) && $page_title == "Edit") : ?>
								<h3 class="box-title" style="font-size: 26px"><?php echo trans('edit-vendor') ?></h3>
								<div class="add-btn">
									<a href="<?php echo base_url('admin/vendor') ?>" class="btn btn-primary rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
								</div>
							<?php else : ?>
								<div class="d-flex justify-content-between align-items-center loan_re">
									<h3 class="box-title" style="font-size: 26px"><i class="flaticon-group"></i> <?php echo trans('vendors') ?></h3>
									<div class="add-btn add_new">
										<a href="#" class=" btn btn-info btn-sm rounded add_btn"><i class="fa fa-plus"></i> <?php echo trans('add-new-vendor') ?></a>
									</div>
								</div>
							<?php endif; ?>
						</div>
						<table class="table table-hover cushover <?php if (count($vendors) > 10) {
																		echo "datatable";
																	} ?>" id="dg_table">
							<thead>
								<tr>
									<th>#</th>
									<th><?php echo trans('name') ?></th>
									<th><?php echo trans('phone') ?></th>
									<th><?php echo trans('email') ?></th>
									<th><?php echo trans('action') ?></th>
								</tr>
							</thead>
							<tbody>
								<?php $i = 1;
								foreach ($vendors as $vendor) : ?>
									<tr id="row_<?php echo html_escape($vendor->id); ?>">

										<td><?php echo $i; ?></td>
										<td style="text-transform: capitalize"><?php echo html_escape($vendor->name); ?></td>
										<td><?php echo html_escape($vendor->phone); ?></td>
										<td><?php echo html_escape($vendor->email); ?></td>

										<td class="actions" width="15%">
											<a href="<?php echo base_url('admin/vendor/edit/' . html_escape($vendor->id)); ?>" class="on-default edit-row" data-placement="top" title="Edit"><i class="fa fa-pencil"></i></a> &nbsp;

											<a data-val="vendor" data-id="<?php echo html_escape($vendor->id); ?>" href="<?php echo base_url('admin/vendor/delete/' . html_escape($vendor->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a>
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
<style type="text/css">
	body {
		font-family: Arial;
		font-size: 10pt;
	}

	.error {
		color: Red;
	}

	.suerror {
		color: green;
	}

	.gst {
		text-transform: uppercase;
	}
</style>
<script>
	changeFunction();

	function changeFunction() {
		var tax_format = $("#tax_format").val();

		if (tax_format == 'GST Number') {
			$("#vat_code_show").show();
			$("#text_name_change").text("GST Number");

			$("#edit_vat_code").attr("onkeyup", "ValidateGSTNumber()");
		} else if (tax_format == 'Tax Number') {
			$("#vat_code_show").show();
			$("#text_name_change").text("Tax Number");

			$("#edit_vat_code").attr("onkeyup", "");
			$("#gst_validation").val(0);
		} else if (tax_format == 'Vat Number') {
			$("#vat_code_show").show();
			$("#text_name_change").text("Vat Number");

			$("#edit_vat_code").attr("onkeyup", "");
			$("#gst_validation").val(0);
		} else if (tax_format == 'Tax/Vat Number') {
			$("#vat_code_show").show();
			$("#text_name_change").text("Tax/Vat Number");

			$("#edit_vat_code").attr("onkeyup", "");
			$("#gst_validation").val(0);
		} else {
			$("#vat_code_show").hide();

			$("#edit_vat_code").attr("onkeyup", "");
			$("#gst_validation").val(0);
		}
	}
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js"></script>
<script type="text/javascript">
	function ValidateGSTNumber() {
		var gstNumber = document.getElementById("edit_vat_code").value;
		var lblError = document.getElementById("lblError");
		lblError.innerHTML = "";
		lblSError.innerHTML = "";
		var expr = /^([0-9]{2}[a-zA-Z]{4}([a-zA-Z]{1}|[0-9]{1})[0-9]{4}[a-zA-Z]{1}([a-zA-Z]|[0-9]){3}){0,15}$/;

		if (gstNumber) {
			if (!expr.test(gstNumber)) {
				lblError.innerHTML = "Invalid GST Number.";
				$("#gst_validation").val(1);
			} else {
				lblSError.innerHTML = "Valid GST Number.";
				$("#gst_validation").val(2);
			}
		} else {
			lblError.innerHTML = "Please Enter GST Number.";
			$("#gst_validation").val(0);
		}
	}
</script>