<style>
	/* ghanshyam Css 31-08-2023	 */
	[type="checkbox"].filled-in:not(:checked)+label::after {
		height: 25px;
		width: 25px;
		background-color: transparent;
		border: 2px solid #844fdd;
		top: 0;
		z-index: 0;
		border-radius: 100%;
	}

	[type="checkbox"].filled-in:checked.chk-col-blue+label::after {
		border: 4px solid #d2b9ff;
		background: linear-gradient(90.29deg, #9E6AF6 0.25%, #6A34C3 100.78%);
		border-radius: 100%;
	}

	[type="checkbox"].filled-in:checked+label::after {
		top: 0;
		width: 25px;
		height: 25px;
		border: 2px solid #398bf7;
		background-color: #398bf7;
		z-index: 0;
	}

	[type="checkbox"].filled-in:checked+label::before {
		top: 6px;
		left: 6px;
		width: 5px;
		height: 9px;
		border-top: 1px solid transparent;
		border-left: 1px solid transparent;
	}

	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		background-color: #723ccb;
		border: none;
		color: #fff;
		font-weight: 400;
	}


	.select2-container--default .select2-selection--single .select2-selection__rendered,
	input#amount_decimal {
		color: #7843d1;
	}
</style>

<div class="content-wrapper">
	<section class="content">
		<div class="card-box">
			<form id="general_setting_form" method="post" enctype="multipart/form-data" action="" role="form" class="form-horizontal">
				<!--<div class="nav-tabs-custom">-->
				<div class="row m-5 mt-20">
					<div class="col-xl-3 col-lg-12">
						<div class="nav-tabs-custom profile_menu_web">
							<?php include "include/profile_menu.php"; ?>
						</div>
						<div class="nav-tabs-custom profile_menu_mobile">
							<?php include "include/profile_menu_1.php"; ?>
						</div>
					</div>
					<div class="col-xl-9">
						<div class="box-header">
							<h3 class="box-title">General Setting</h3>
						</div>
						<div class="box-body p-10">
							<div class="row pl-30">
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<label for="business_currency"> Business Currency</label>
										<select class="form-control single_select" id="business_currency" name="business_currency">
											<?php
											foreach ($currencies as $cur) :
												if (!empty($cur->currency_symbol)) :
											?>
													<option value="<?php echo $cur->id ?>" <?php echo ($this->business->country_id == $cur->id) ? 'selected' : ''; ?>><?php echo $cur->name ?> - <?php echo $cur->currency_symbol ?></option>
											<?php
												endif;
											endforeach;
											?>
										</select>
									</div>
								</div>
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<label for="amount_decimal"> Amount Up To Decimal</label>
										<input type="number" class="form-control" name="amount_decimal" id="amount_decimal" placeholder="eg. 0.00" value="<?php echo $business->amount_decimal ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
									</div>
								</div>
							</div>
							<div class="row pl-30">

								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="estimate_quatation" class="filled-in chk-col-blue" value="1" name="estimate_quatation" <?php echo ($business->estimate_quatation == 1) ? 'checked' : '' ?>>
										<label for="estimate_quatation"> Enable Estimate / Quatation</label>
										<p></p>
									</div>
								</div>
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="delivery_challan" class="filled-in chk-col-blue" value="1" name="delivery_challan" <?php echo ($business->delivery_challan == 1) ? 'checked' : '' ?>>
										<label for="delivery_challan"> Enable Delivery Challan</label>
										<p></p>
									</div>
								</div>
							</div>
							<div class="row pl-30">
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="sale_invoice" class="filled-in chk-col-blue" value="1" name="sale_invoice" <?php echo ($business->sale_invoice == 1) ? 'checked' : '' ?>>
										<label for="sale_invoice"> Enable Sale Invoice</label>
										<p></p>
									</div>
								</div>
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="purchase_bill" class="filled-in chk-col-blue" value="1" name="purchase_bill" <?php echo ($business->purchase_bill == 1) ? 'checked' : '' ?>>
										<label for="purchase_bill">Enable Purchase BIll </label>
										<p></p>
									</div>
								</div>
							</div>

						</div>

						<div class="box-header">
							<h3 class="box-title">Invoice / Item Setting</h3>
						</div>

						<div class="box-body p-10">
							<div class="row pl-30">
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="gst_invoice" class="filled-in chk-col-blue" value="1" name="gst_invoice" <?php echo ($business->gst_invoice == 1) ? 'checked' : '' ?>>
										<label for="gst_invoice"> Enable GST No. On Invoice</label>
										<p></p>
									</div>
								</div>
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="invoice_number" class="filled-in chk-col-blue" value="1" name="invoice_number" <?php echo ($business->invoice_number == 1) ? 'checked' : '' ?>>
										<label for="invoice_number">Enable Invoice/Bill No</label>
										<p></p>
									</div>
								</div>
							</div>

							<div class="row pl-30">
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="invoice_tax" class="filled-in chk-col-blue" value="1" name="invoice_tax" <?php echo ($business->invoice_tax == 1) ? 'checked' : '' ?>>
										<label for="invoice_tax">Enable Invoice Tax </label>
										<p></p>
									</div>
								</div>
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="invoice_discount" class="filled-in chk-col-blue" value="1" name="invoice_discount" <?php echo ($business->invoice_discount == 1) ? 'checked' : '' ?>>
										<label for="invoice_discount">
											Enable Invoice Discount
										</label>
										<p></p>
									</div>
								</div>
							</div>
							<div class="row pl-30">
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="hsn_sac" class="filled-in chk-col-blue" value="1" name="hsn_sac" <?php echo ($business->hsn_sac == 1) ? 'checked' : '' ?>>
										<label for="hsn_sac">Enable HSN/SAC Code</label>
										<p></p>
									</div>
								</div>
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="enable_serial_no" class="filled-in chk-col-blue" value="1" name="enable_serial_no" <?php echo ($business->enable_serial_no == 1) ? 'checked' : '' ?>>
										<label for="enable_serial_no">Enable Serial No / IMEI No. etc</label>
										<p></p>
									</div>
								</div>
							</div>
							<div class="row pl-30">
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="invoice_preview" class="filled-in chk-col-blue" value="1" name="invoice_preview" <?php echo ($business->invoice_preview == 1) ? 'checked' : '' ?>>
										<label for="invoice_preview">
											Do Not Show Invoice Preview
										</label>
										<p></p>
									</div>
								</div>
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="enable_stock" class="filled-in chk-col-blue" value="1" name="enable_stock" <?php echo ($business->enable_stock == 1) ? 'checked' : '' ?> onchange="quanstock(this)">
										<label for="enable_stock">
											Enable Stock Quantity
										</label>
										<p></p>
									</div>
								</div>
							</div>
							<div class="row pl-30">
								<!-- <div class="col-md-4 pl-10">
										<div class="form-group">
											<input type="checkbox" id="time_on_invoice" class="filled-in chk-col-blue" value="1" name="time_on_invoice" <?php echo ($business->time_on_invoice == 1) ? 'checked' : '' ?>>
											<label for="time_on_invoice">
												Add Time On Invoice
											</label>
											<p></p>
										</div>
									</div> -->
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<input type="checkbox" id="po_details" class="filled-in chk-col-blue" value="1" name="po_details" <?php echo ($business->po_details == 1) ? 'checked' : '' ?>>
										<label for="po_details">
											Customers P.O. Details on Transactions
										</label>
										<p></p>
									</div>
								</div>
								<div class="col-md-4 pl-10 hide_quan">
									<div class="form-group">
										<label for="quantity_limit"> Quantity Limit</label>
										<input type="number" class="form-control" name="quantity_limit" id="quantity_limit" value="<?php echo $business->quantity_limit ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
										<input type="hidden" class="form-control" name="time_on_invoice" id="time_on_invoice" value="0">
									</div>
								</div>
							</div>
							<div class="row pl-30">
							</div>
						</div>
						<div class="box-header">
							<h3 class="box-title">Invoice Number Prefixes</h3>
						</div>
						<div class="box-body p-10">
							<div class="row pl-30">
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<label>Estimate Prefix</label>
										<input type="text" id="estimate_invoice_prefix" class="form-control filled-in chk-col-blue" name="estimate_invoice_prefix" value="<?php echo $business->estimate_invoice_prefix; ?>">
									</div>
								</div>
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<label>Delivery Prefix</label>
										<input type="text" id="delivery_invoice_prefix" class="form-control filled-in chk-col-blue" name="delivery_invoice_prefix" value="<?php echo $business->delivery_invoice_prefix; ?>">
									</div>
								</div>
							</div>
							<div class="row pl-30">
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<label>Sales Prefix</label>
										<input type="text" id="sale_invoice_prefix" class="form-control filled-in chk-col-blue" name="sale_invoice_prefix" value="<?php echo $business->sale_invoice_prefix; ?>">
									</div>
								</div>
								<div class="col-md-4 pl-10">
									<div class="form-group">
										<label>Purchase Prefix</label>
										<input type="text" id="purchase_invoice_prefix" class="form-control filled-in chk-col-blue" name="purchase_invoice_prefix" value="<?php echo $business->purchase_invoice_prefix; ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="box-header">
							<h3 class="box-title">MultiCurrency Setting <span class="text-danger font-size-12">(INR,AED,USD ID DEFAULT SELECTED CURRENCY)</span></h3>
						</div>
						<div class="box-body p-10">
							<div class="row pl-30">
								<div class="col-md-6 pl-10">
									<select class="form-select single_select" style="width:100%" id="country" name="country[]" required multiple>
										<?php
										foreach ($country as $key => $value) {
											$ids = ($value['bussiness_user_id'] != '') ? $value['bussiness_user_id'] : '';
											$checked = (strpos($ids, $bussiness_user_id) || $value['id'] == $this->business->country_id || $value['id'] == '2' || $value['id'] == '79' || $value['id'] == '178') ? 'selected' : ''; ?>
											<option value="<?php echo $value['id']; ?>" <?php echo $checked; ?>><?php echo $value['currency_code'] . '-' . $value['name']; ?></option>
										<?php } ?>

									</select>
								</div>
							</div>
						</div>

						<div class="box-footer">
							<!-- csrf token -->
							<button type="button" class="btn btn-info waves-effect rounded w-md waves-light" id="update_setting"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
						</div>

					</div>
				</div>
				<!--</div>-->
			</form>
		</div>
	</section>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		$('#amount_decimal').keyup(function() {
			var decimal = $(this).val();
			if (decimal < 1 || decimal > 5) {
				$.toast({
					heading: "",
					text: "Values more than 5 or less than 1 is not allowed",
					position: 'top-right',
					loaderBg: '#fff',
					icon: 'error',
					hideAfter: 3000
				});
			}
		})

		$("#amount_decimal").keyup(function(event) {
			if (event.keyCode === 13) {
				$("#update_setting").click();
			}
		});
		$('#update_setting').click(function() {
			$("#update_setting").html(`Saving...`);
			$("#update_setting").prop('disabled', true);
			var formdata = new FormData($('#general_setting_form')[0]);
			$.ajax({
				type: "POST",
				url: "<?php echo base_url('admin/business/update_general_setting') ?>",
				data: formdata,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function(data, textStatus, jqXHR) {
					$.toast({
						heading: "Success",
						text: data.msg,
						position: 'top-right',
						loaderBg: '#fff',
						icon: 'success',
						hideAfter: 3000
					});
					setTimeout(function() {
						location.reload();
					}, 1000);
					$("#update_setting").html(`<i class="fa fa-check"></i> <?php echo trans('save-changes') ?>`);
					$("#update_setting").prop('disabled', false);
				}
			});
		})
	})
	quanstock("#enable_stock");

	function quanstock(evt) {
		if ($(evt).is(':checked')) {
			$('.hide_quan').show();
			$('#quantity_limit').val('10');
		} else {
			$('#quantity_limit').val('10');
			$('.hide_quan').hide();
		}
	}
</script>