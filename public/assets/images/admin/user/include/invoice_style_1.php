<?php 
include 'invoice_val.php';
$invoice_data = $invoice;
// extract($invoice_data);
?>
<div class="card-body p-0" style="margin: 12px; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; ">
	<div class="row p-5 align-items-start" style="justify-content: space-between; margin: 0">
		<div class="col-6">
			<?php
			if ($this->business->gst_invoice == 1) {
				if (count($tax_format) > 0 && !empty($tax_format)) {
					foreach ($tax_format as $key => $tax_label) { ?>
						<p style="color: black; font-weight: bold;"><?= $tax_label ?>: <?= html_escape($biz_vat_code[$key]) ?></p>
			<?php 	}
				}
			} ?>
		</div>
		<div class="col-6">
			<p style="font-weight: bold; text-align: right; font-style: italic">Original Copy</p>
		</div>
		<div class="col-12">
			<p class="mb-0" style="text-transform: uppercase; font-weight: bold; text-decoration: underline; text-align:center">tax Invoice</p>
		</div>
	</div>
	<div class="row p-15 align-items-start" style="justify-content: <?php if (empty($logo)) : ?> center; <?php else : ?>space-between <?php endif ?>; margin: 0">
		<div class="col-3" style="display: <?php if (empty($logo)) : ?> none; <?php else : ?>block <?php endif ?>; ">
			<img width="100%" src="<?php echo base_url($logo) ?>" alt="Logo">
			<?php if (empty($logo)) { ?>
				<!--<p><span class="alterlogo"><?= $business_name ?></span></p>-->
			<?php } else { ?>
				<!--<img width="100%" src="<?= base_url($logo) ?>" alt="Logo">-->
			<?php } ?>

		</div>

		<div class="col-5" style="text-align: <?php if (empty($logo)) : ?> center; <?php else : ?>right <?php endif ?>">
			<p class="mb-0" style="text-transform: uppercase; font-weight: bold; font-size:20px"><?php echo $business_name ?></p>

			<p class="mb-0" style="text-transform: uppercase; font-weight: bold; font-size:14px"><?php echo $business_address ?></p>
			<p class="mb-0" style="text-transform: uppercase; font-weight: bold; font-size:14px"><?php echo html_escape($country) ?></p>
			<?php if (!empty($biz_number)) { ?>
				<p class="mb-0" style="text-transform: uppercase; font-weight: bold; font-size:14px"><?= trans('contact') . ' ' . trans('no') ?>: <?= html_escape($biz_number) ?></p>
			<?php } ?>

			<?php if (!empty($website_url)) { ?>
				<p class="mb-0" style="font-weight: bold; font-size:14px"><?= html_escape($website_url) ?></p>
			<?php } ?>

		</div>
	</div>

	<div class="table-responsive">
		<table class="table m-0 table-bordered" style='border-top: 1px solid <?php echo html_escape($color) ?>; border-bottom: 1px solid <?php echo html_escape($color) ?>'>
			<tr class="pre_head_tr inv">
				<td width="50%" style='border-right: 1px solid <?php echo html_escape($color) ?>'>
					<div style="margin: 0">
						<span style="font-weight: bold">
							<?php
							if (isset($page) && $page == 'Invoice') {
								echo trans('invoice-number');
							} elseif ($page == 'Estimate') {
								echo trans('estimate-number');
							} elseif ($page == 'Delivery') {
								echo 'Challan Number';
							} else {
								echo trans('bill-number');
							}
							?> : </span>
						<span><?= ($prefix) ? $prefix . ' - ' : ''; ?><?= html_escape($number) ?></span>
					</div>
					<?php
					if ($this->business->po_details == 1 && !empty($poso_number)) {
					?>
						<div style="margin: 0">
							<span style="font-weight: bold">PO Number :</span>
							<span><?= $poso_number ?></span>
						</div>
					<?php
					}
					?>
					<div style="margin: 0">
						<span style="font-weight: bold">
							<?php
							if (isset($page) && $page == 'Invoice') {
								echo trans('invoice-date');
							} elseif (isset($page) && $page == 'Delivery') {
								echo 'Delivery Date';
							} else {
								echo trans('date');
							}
							?> : </span>
						<span><?= my_date_show($date) ?></span>
					</div>

					<?php if ($this->business->time_on_invoice == 1) { ?>
						<div style="margin: 0">
							<span style="font-weight: bold">Time :</span>
							<span><?= $time ?></span>
						</div>
					<?php } ?>

				</td>
				<td width="50%">
					<?php if (isset($page) && $page == 'Invoice') { ?>

						<p><span style="font-weight: bold"><?= trans('due-date') ?> </span>: <?= my_date_show($payment_due) ?> (<?php if ($due_limit == 1) : ?><?= trans('on-receipt') ?><?php else : ?><?= trans('within') ?> <?= html_escape($due_limit) ?> <?= trans('days') ?><?php endif ?>)</p>

						<?php } else {
						if ($invoice->expire_on != '0000-00-00' && isset($page) && $page != 'Delivery') { ?>

							<p><span style="font-weight: bold"><?= trans('expires-on') ?> </span>: <?= my_date_show($invoice->expire_on) ?></p>

					<?php }
					} ?>

				</td>

			</tr>
		</table>
	</div>

	<div class="table-responsive">
		<table class="table m-0 table-bordered" style="border-bottom: 1px solid <?php echo html_escape($color) ?>; ">
			<tr class="pre_head_tr inv">
				<td width="50%">
					<div style="margin: 0; font-weight: bold;">
						<?php
						if (isset($page) && $page == 'Bill') {
							echo trans('purchase-from');
						} else {
							echo trans('bill-to');
						}
						?>:
					</div>
					<?php if (empty($customer_id)) { ?>
						<p class="mb-1"><?= trans('empty-customer') ?></p>
						<?php } else {
						if (isset($page) && $page == 'Bill') {
							if (!empty(helper_get_vendor($customer_id))) { ?>
								<p class="mb-0" style="text-transform: uppercase;"><strong><?= helper_get_vendor($customer_id)->name ?></strong></p>
								<p class="mt-0 mb-0" style="text-transform: uppercase;"><?= helper_get_vendor($customer_id)->address ?></p>
								<p class="mt-0 mb-0"><?= trans('contact') . ' ' . trans('no') ?>: <?= helper_get_vendor($customer_id)->phone ?></p>
								<p class="mt-0 mb-0"><?= helper_get_vendor($customer_id)->email ?></p>
							<?php }
						} else {
							if (!empty(helper_get_customer($customer_id))) { ?>
								<p class="mt-0 mb-0" style="text-transform: uppercase; font-weight: bold;"><?= helper_get_customer($customer_id)->name ?></p>
								<?php if (!empty($cus_vat_code)) { ?>
									<p class="mt-0 mb-0"><span style="font-weight: bold;"><?= $cus_tax_format; ?></span>: <span style="text-transform: uppercase;"><?= html_escape($cus_vat_code) ?></span></p>
								<?php } ?>
								<p class="mt-0 mb-0" style="text-transform: uppercase; font-weight: bold;"><?= helper_get_customer($customer_id)->address ?>, <?= helper_get_customer($customer_id)->country ?></p>
								<p class="mt-0 mb-0"><span style="font-weight: bold;"><?= trans('contact') . ' ' . trans('no') ?></span>: <?= helper_get_customer($customer_id)->phone ?></p>
								<p class="mt-0 mb-0"><span style="font-weight: bold;"><?= trans('email') . ' ' . trans('id') ?></span>: <?= helper_get_customer($customer_id)->email ?></p>
					<?php }
						}
					} ?>

				</td>

				<td width="50%" style="border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse; border-right: 0; border-top: 0; vertical-align: top !important">
				<?php if ($page != 'Bill') { ?>
					<p style="margin: 0; font-weight: bold;">Shipped To:</p>
					<?php 
					if (empty($customer_id)) { ?>
						<p class="mb-1"><?= trans('empty-customer') ?></p>
					<?php 
					} else {
						if (isset($page) && $page == 'Bill') {
							if (!empty(helper_get_vendor($customer_id))) { ?>
								<p class="mb-0" style="text-transform: uppercase;"><strong><?= helper_get_vendor($customer_id)->name ?></strong></p>
								<p class="mt-0 mb-0" style="text-transform: uppercase; font-weight: bold;"><?= helper_get_vendor($customer_id)->address1 ?></p>
								<p class="mt-0 mb-0"><?= trans('contact') . ' ' . trans('no') ?>: <?= helper_get_vendor($customer_id)->phone ?></p>
								<p class="mt-0 mb-0"><?= helper_get_vendor($customer_id)->email ?></p>
							<?php }
						} else {
							if (!empty(helper_get_customer($customer_id))) { ?>
								<p class="mt-0 mb-0" style="text-transform: uppercase; font-weight: bold;"><?= helper_get_customer($customer_id)->s_name ?></p>
								<p class="mt-0 mb-0" style="text-transform: uppercase; font-weight: bold;"><?= helper_get_customer($customer_id)->address1 ?></p>
								<p class="mt-0 mb-0"><span style="font-weight: bold;"><?= trans('contact') . ' ' . trans('no') ?></span>: <?= helper_get_customer($customer_id)->s_phone ?></p>
					<?php }
						}
						}
					} ?>

				</td>
			</tr>

		</table>
	</div>
	<br>

	<div class="row p-0 table_area m-0" style="width: 100%">
		<div class="col-12 table-responsive p-0">
			<table class="table m-0" style="border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-left: 0; border-right: 0">
				<thead>
					<tr>
						<?php
						$cols = 2;
						if ($this->business->enable_serial_no == 0) {
							$cols++;
						}
						if ($this->business->invoice_discount == 0) {
							$cols++;
						}
						if ($this->business->invoice_tax == 0) {
							$cols++;
						}
						?>
						<th style="font-weight: bold; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-left: 0" colspan="<?php echo $cols; ?>"><?php echo trans('items') ?></th>
						<?php if ($this->business->hsn_sac == 1) : ?>
							<th style="font-weight: bold; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse;">HSN/SAC Code</th>
						<?php endif; ?>
						<th style="font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse;"><?php echo trans('rate') ?></th>
						<th style="font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse;"><?php echo trans('quantity') ?></th>
						<th style="font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse;">Unit</th>
						<?php if ($this->business->invoice_discount == 1) : ?>
							<th style="font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse;">Discount</th>
						<?php endif; ?>
						<?php if ($this->business->invoice_tax == 1) : ?>
							<th style="font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse;">GST</th>
						<?php endif; ?>
						<th style="font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0"><?php echo trans('amount') ?></th>
					</tr>
				</thead>

				<tbody>
					<?php
					$discount_percent = 0;
					if (isset($page_title) && $page_title == 'Invoice Preview') {
						if (!empty($this->session->userdata('item'))) {
							$total_items = count($this->session->userdata('item'));
						} else {
							$total_items = 0;
						}
						if (empty($total_items)) {
							echo '<tr><td colspan="8" class="text-center">' . trans('empty-items') . '</td></tr>';
						} else {
							for ($i = 0; $i < $total_items; $i++) {
								$cols = 2;
								if ($this->business->enable_serial_no == 0) {
									$cols++;
								}
								if ($this->business->invoice_discount == 0) {
									$cols++;
								}
								if ($this->business->invoice_tax == 0) {
									$cols++;
								} 
								?>
								<tr class="inv">
									<td width="25%" style="border-top: 0;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;  border-left: 0" colspan="<?= $cols ?>">
										<p style="margin-bottom: 0px; font-weight: bold"><?= $this->session->userdata('item')[$i] ?></p>
										<?php if ($this->business->enable_serial_no == 1) : ?>
											<p class="m-0"><?= $this->session->userdata('serial_no')[$i] ?></p>
										<?php endif; ?>
										<p class="m-0"><?= $this->session->userdata('details')[$i] ?></p>
									</td>
									<?php if ($this->business->hsn_sac == 1) : ?>
										<td style="border-top: 0; border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;">
											<p class="m-0"><?= $this->session->userdata('hsn_sac')[$i] ?></p>
										</td>
									<?php endif; ?>
									<td style="border-top: 0; text-align: right;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;">
										<p class="m-0"><?= !empty($currency_symbol) ? html_escape($currency_symbol) : '' ?><?= decimal_format($this->session->userdata('price')[$i], 2) ?></p>
									</td>
									<td style="border-top: 0; text-align: center;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;">
										<p class="m-0"><?= $this->session->userdata('quantity')[$i] ?></p>
									</td>
									<td style="border-top: 0; text-align: center;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;">
										<p class="m-0"><?= !empty($this->session->userdata('unit')[$i]) ? $this->session->userdata('unit')[$i] : '-' ?></p>
									</td>
									<?php if ($this->business->invoice_discount == 1) : ?>
										<td style="border-top: 0; text-align: center;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;"><?= !empty($this->session->userdata('discount')[$i]) ? html_escape($this->session->userdata('discount')[$i]) . '%' : '-' ?></td>
									<?php endif; ?>
									<?php if ($this->business->invoice_tax == 1) : ?>
										<td style="border-top: 0; text-align: center;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;">
											<?php
											$taxs = $this->session->userdata('tax');
											$tax_key = $this->session->userdata('tax_key')[$i];
											if (!empty($taxs)) {
												$taxss = $taxs[$tax_key];
												foreach ($taxss as $tax) {
													echo $tax . '<br>';
												}
											} else {
												echo "-";
											}
											?>
										</td>
									<?php endif; ?>
									<td style="border-top: 0; text-align: right; padding-right:20px;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse; border-right: 0">
										<p class="m-0"><?= !empty($currency_symbol) ? html_escape($currency_symbol) : '' ?><?= decimal_format($this->session->userdata('total_price')[$i], 2) ?></p>
									</td>
								</tr>

							<?php
								$discount_percent += $this->session->userdata('discount')[$i];
							}
						}
					} else {
						$items = helper_get_invoice_items($invoice->id);

						if (empty($items)) {
							echo '<tr><td colspan="8" class="text-center">' . trans('empty-items') . '</td></tr>';
						} else {
							foreach ($items as $item) {
								$cols = 2;
								if ($this->business->enable_serial_no == 0) {
									$cols++;
								}
								if ($this->business->invoice_discount == 0) {
									$cols++;
								}
								if ($this->business->invoice_tax == 0) {
									$cols++;
								}
							?>

								<tr class="inv">
									<td width="25%" style="border-top: 0;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;  border-left: 0" colspan="<?= $cols ?>">
										<p style="margin-bottom: 0px; font-weight: bold"><?= html_escape($item->item_name) ?></p>
										<?php if ($this->business->enable_serial_no == 1) : ?>
											<p class="m-0"><?= html_escape($item->serial_no) ?></p>
										<?php endif; ?>
										<p class="m-0" style="text-wrap: balance;"><?= nl2br($item->details) ?></p>
									</td>
									<?php if ($this->business->hsn_sac == 1) : ?>
										<td style="border-top: 0; border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;">
											<p class="m-0"><?= $item->hsn_sac ?></p>
										</td>
									<?php endif; ?>
									<td style="border-top: 0; text-align: right;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;">
										<p class="m-0"><?= !empty($currency_symbol) ? html_escape($currency_symbol) : '' ?><?= decimal_format($item->price, 2) ?></p>
									</td>
									<td style="border-top: 0; text-align: center;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;">
										<p class="m-0"><?= html_escape($item->qty) ?></p>
									</td>
									<td style="border-top: 0; text-align: center;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;">
										<p class="m-0"><?= !empty($item->unit) ? html_escape($item->unit) : '-' ?></p>
									</td>
									<?php if ($this->business->invoice_discount == 1) : ?>
										<td style="border-top: 0; text-align: center;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;"><?= !empty($item->discount) ? html_escape($item->discount) . '%' : '-' ?></td>
									<?php endif; ?>
									<?php if ($this->business->invoice_tax == 1) : ?>
										<td style="border-top: 0; text-align: center;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;">
											<?php if (!empty($item->taxs)) {
												$taxs = explode(',', $item->taxs);
												foreach ($taxs as $tax) {
													echo $tax . '<br>';
												}
											} else {
												echo "-";
											}
											?>
										</td>
									<?php endif; ?>
									<td style="border-top: 0; text-align: right; padding-right:20px;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse; border-right: 0">
										<p class="m-0"><?= !empty($currency_symbol) ? html_escape($currency_symbol) : '' ?><?= decimal_format($item->total, 2) ?></p>
									</td>
								</tr>

					<?php
								$discount_percent += $item->discount;
							}
						}
					}
					?>


					<?php if ($discount > 0 && !empty($discount)) {
						if ($this->business->invoice_discount == 1) {
					?>
							<tr class="inv">
								<td data-row-id="1" style="border-top: 0; text-align: center; border: 1px solid transparent; border-left: 0" colspan="6"></td>
								<td colspan="2" class="text-right border-0" style="padding: 5px 12px; border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;"><strong>Total :</strong></td>
								<td class="border-0" style="padding: 5px 20px; text-align: right; border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse; border-right: 0">
									<span>
										<?= !empty($currency_symbol) ? html_escape($currency_symbol) : '' ?>
										<?= (isset($page_title) && $page_title == 'Invoice Preview') ? decimal_format($this->session->userdata('sub_total'), 2) : decimal_format($sub_total, 2) ?>
									</span>
								</td>
							</tr>
							<tr class="inv">
								<td data-row-id="2" style="border-top: 0; text-align: center; border: 1px solid transparent; border-left: 0" colspan="6"></td>
								<td colspan="2" class="text-right border-0" style="border-top: 0; padding: 5px 12px; border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;"><strong><?= trans('discount') ?> <?= html_escape($discount_percent) ?>% :</strong></td>
								<td class="border-0" style="border-top: 0; padding: 5px 20px; text-align: right; border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse; border-right: 0">
									<span>
										<?= !empty($currency_symbol) ? html_escape($currency_symbol) : '' ?>
										<?= (isset($page_title) && $page_title == 'Invoice Preview') ? decimal_format($this->session->userdata('total_discount'), 2) : decimal_format($discount, 2) ?>
									</span>
								</td>
							</tr>
							<tr class="inv">
								<td data-row-id="3" style="border-top: 0; text-align: center; border: 1px solid transparent; border-left: 0;border-right: 1px;" colspan="6"></td>
								<td colspan="2" style="padding: 5px 12px; border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse; text-align: right"><strong><?= trans('sub-total') ?>:</strong></td>
								<td style="padding: 5px 20px; text-align: right; border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse; text-align: right; border-right: 0">
									<span>
										<?= !empty($currency_symbol) ? html_escape($currency_symbol) : '' ?>
										<?= (isset($page_title) && $page_title == 'Invoice Preview') ? decimal_format($this->session->userdata('sub_total') - $this->session->userdata('total_discount'), 2) : decimal_format($sub_total - $discount, 2) ?>
									</span>
								</td>
							</tr>
						<?php
						} else {
						?>
							<tr class="inv">
								<td data-row-id="4" style="border-top: 0; text-align: center; border: 1px solid transparent; border-left: 0;border-right: 1px;" colspan="6"></td>
								<td colspan="2" class="text-right" style="padding: 5px 12px; border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;"><strong><?= trans('sub-total') ?> : </strong></td>
								<td style="padding: 5px 20px; text-align: right; border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse; border-right: 0">
									<span>
										<?= !empty($currency_symbol) ? html_escape($currency_symbol) : '' ?>
										<?= (isset($page_title) && $page_title == 'Invoice Preview') ? decimal_format($this->session->userdata('sub_total'), 2) : decimal_format($sub_total, 2) ?>
									</span>
								</td>
							</tr>
					<?php
						}
					} ?>


					<?php if ($this->business->invoice_tax == 1) {
						if (isset($page_title) && $page_title != 'Invoice Preview') {
							if (!empty($taxes)) {
								foreach ($taxes as $tax) {
									if ($tax != 0) {					?>
										<tr class="inv">
											<td data-row-id="5" style="border-top: 0; text-align: center; border: 1px solid transparent; border-left: 0;border-right: 1px;" colspan="6"></td>
											<td colspan="2" class="text-right" style="border-top: 0; padding: 5px 12px;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;"><strong><?= $tax->tax_type ?> :</strong></td>
											<td style="border-top: 0; padding: 5px 20px; text-align: right; border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse; border-right: 0"><span><?= !empty($currency_symbol) ? html_escape($currency_symbol) : '' ?><?= decimal_format($tax->tax_value) ?></span></td>
										</tr>
									<?php
									}
								}
							}
						} else {
							$taxes = $this->session->userdata('tax_value') ? $this->session->userdata('tax_value') : '';
							if (!empty($taxes)) {
								foreach ($taxes as $key => $tax) {
									if ($tax != 0) {
									?>
										<tr class="inv">
										<td data-row-id="6" style="border-top: 0; text-align: center; border: 1px solid transparent; border-left: 0;border-right: 1px;" colspan="6"></td>
											<!-- <td style="border-top: 0; text-align: center;">6</td>
											<td style="border-top: 0; text-align: center;">6</td>
											<td style="border-top: 0; text-align: center;">6</td>
											<td style="border-top: 0; text-align: center;">6</td>
											<td style="border-top: 0; text-align: center;">6</td>
											<td style="border-top: 0; text-align: center;">6</td> -->
											<td colspan="2" class="text-right" style="border-top: 0; padding: 5px 12px;  border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse;"><strong><?= $key ?>:</strong></td>
											<td style="border-top: 0; padding: 5px 20px; text-align: right; border: 1px solid <?= html_escape($color) ?>; border-collapse: collapse; border-right: 0"><span><?= !empty($currency_symbol) ? html_escape($currency_symbol) : '' ?><?= decimal_format($tax) ?></span></td>
										</tr>
					<?php
									}
								}
							}
						}
					} ?>


					<tr class="inv">
						<td data-row-id="7" style="border-top: 0; text-align: center; border: 1px solid; border-left: 0; border-right: 0; border-bottom: 0px solid <?php echo html_escape($color) ?> ;" colspan="6"></td>
						<td colspan="2" class="text-right" style="padding: 5px 12px; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse;"><strong><?php echo trans('grand-total') ?>:</strong></td>
						<td style="padding: 5px 20px; text-align: right; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0"><span><?php if (!empty($currency_symbol)) {
																																													echo html_escape($currency_symbol);
																																												}
																																												if (isset($page_title) && $page_title == 'Invoice Preview') {
																																													echo decimal_format($this->session->userdata('grand_total'), 2);
																																												} else {
																																													echo decimal_format($grand_total, 2);
																																												} ?></span></td>
					</tr>

					<?php foreach (get_invoice_payments($invoice->id) as $payment) : ?>
						<tr class="inv text-dark">
							<td data-row-id="8" style="border-top: 0; text-align: center; border: 0px solid; border-left: 0; border-right: 0; border-bottom: 0px;" colspan="6"></td>
							<td colspan="2" class="text-right" style="padding: 5px 12px; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse;">
								<p class="fs-13 m-0" style="font-weight: bold"><?php echo trans('payment-on') ?> <?php echo my_date_show($payment->payment_date) ?> <?php echo trans('using') ?> <?php echo get_using_methods($payment->payment_method) ?>:</p>
							</td>
							<td style="padding: 5px 20px; text-align: right; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0">
								<p class="fs-13"><?php if (!empty($currency_symbol)) {
														echo html_escape($currency_symbol);
													} ?>
									<?php echo decimal_format($payment->amount / $c_rate, 2) ?></p>
							</td>
						</tr>
					<?php endforeach ?>

					<tr class="inv">
						<td data-row-id="9" style="border-top: 3px solid #dfdede; padding: 5px 12px; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-bottom: 1px solid transparent; border-left: 0;" colspan="6">
							<?php echo $conversion_currency ?>
						</td>
						<td colspan="2" class="text-right" style="border-top: 3px solid #dfdede; padding: 5px 12px; border: 1px solid <?php echo html_escape($color) ?>; border-right: 1px; border-collapse: collapse; border-bottom: 1px solid transparent">
							<strong><?php echo trans('amount-due') ?>:</strong>
						</td>
						<td style="border-top: 3px solid #dfdede; padding: 5px 20px; text-align: right; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0; border-bottom: 1px solid transparent">
							<span>
								<strong>
									<?php
									if ($status == 2) {
										if (!empty($currency_symbol)) {
											echo html_escape($currency_symbol);
										}
										echo " 0.00";
									} else {
										if (isset($page_title) && $page_title == 'Invoice Preview') {
											if (!empty($currency_symbol)) {
												echo html_escape($currency_symbol);
											}
											echo $convert_total . "<br>";
											echo $c_rate . "<br>";
											echo number_format(($convert_total / $c_rate), 2);
											// echo decimal_format(number_format($convert_total / $c_rate), 2);
										} else {
											if (!empty($currency_symbol)) {
												echo html_escape($currency_symbol);
											}
											if ($convert_total != '0.00') {
												$amotdue = $convert_total - get_total_invoice_payments($invoice->id, $invoice->parent_id);
												echo decimal_format($amotdue / $c_rate, 2);
											} else {
												echo decimal_format($grand_total, 2);
											}
											// $amotdue = $convert_total - get_total_invoice_payments($invoice->id, $invoice->parent_id);
											// echo decimal_format($amotdue / $c_rate, 2);
										}
									}
									?>
								</strong>
							</span>
						</td>
					</tr>
				</tbody>
			</table>
			<?php if (isset($currency_symbol) && ($currency_symbol == '₹' && $this->business->currency_symbol == '₹')) : ?>
			<table class="table m-0">
				<tbody>
					<tr>
						<td style="border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-weight: bold; border-right: 0; border-left: 0; border-bottom: 0;text-transform:capitalize;">
							<?php echo trans('amount-due') ?>:
							<?php
							if ($status == 2) {
								if (!empty($currency_symbol)) {
									echo html_escape($currency_symbol);
								}
								echo "0.00";
							} else {
								if (isset($page_title) && $page_title == 'Invoice Preview') {
									echo convertNumberToWordsForIndia($convert_total / $c_rate);
								} else {
									if ($convert_total != '0.00') {
										$amotdue = $convert_total - get_total_invoice_payments($invoice->id, $invoice->parent_id);
										echo convertNumberToWordsForIndia($amotdue / $c_rate);
									} else {
										echo convertNumberToWordsForIndia($grand_total);
									}
								}
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php endif ?>
			<table class="table m-0">
				<tbody>
					<tr>
						<td data-row-id="10" style="border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; vertical-align: top !important; border-left: 0" width="80%">
							<?php
							$activeBanking = $this->db->where('user_id', $this->session->userdata('id'))->where('bank_print_invoice', 1)->get('banking')->row();
							if (!empty($activeBanking)) :
							?>
								<p style="text-decoration: underline; font-weight: bold">Bank Details : </p>

								<p style="text-transform:capitalize"><span style="font-weight: bold;">Account Holder Name :</span> <?php echo html_escape($activeBanking->account_name); ?> </p>
								<p><span style="font-weight: bold">Bank name : </span><?php echo html_escape($activeBanking->bank_name); ?></p>
								<p><span style="font-weight: bold">Account Number : </span><?php echo html_escape($activeBanking->account_number); ?></p>
								<p><span style="font-weight: bold">IFSC : </span><?php echo html_escape($activeBanking->ifsc); ?></p>
							<?php endif; ?>
						</td>
						<td style="border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0; " width="20%">
							<?php if (isset($page_title) && $page_title == 'Invoice Preview') :
								$pay_qrcode = $this->session->userdata('qr_code');
							?>
								<?php if (!empty($pay_qrcode)) : ?>
									<p style="font-weight: bold">Scan QR-code to Pay</p>
									<p style="text-align: center"><img src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code" style="width: 75%; max-width: 150px"></p>
								<?php endif; ?>
							<?php else : ?>
								<?php if (!empty($pay_qrcode)) : ?>
									<p style="font-weight: bold">Scan QR-code to Pay</p>
									<p style="text-align: center"><img src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code" style="width: 75%; max-width: 150px"></p>
								<?php endif; ?>
							<?php endif ?>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="table-responsive">
				<table class="table m-0 table-bordered" style="border-bottom: 1px solid <?php echo html_escape($color) ?>">
					<tr class="pre_head_tr inv">
						<td width="50%" style='border-right: 1px solid <?php echo html_escape($color) ?>; vertical-align: top !important'>
							<p style="font-weight: bold; text-decoration: underline">Notes / Terms : </p>
							<?php if (!empty($footer_note)) : ?>
								<p style="font-weight: bold"><?php echo $footer_note ?></p>
							<?php endif; ?>
						</td>
						<td width="50%" style="vertical-align: top !important">
							<div style="font-weight: bold; font-size: 12px">Receiver's Signature: _________________________</div>
							<hr>
							<br>
							<p style="text-align: right; font-weight: bold; font-size: 14px">For <?php echo $business_name ?></p>
							<p style="text-align: right; font-weight: bold; font-size: 14px">Authorised Signatory</p>
							<br>
							<br>
						</td>
					</tr>
				</table>
			</div>
			<?php if (!empty($qr_code)) : ?>
				<p class="p-10"><img class="qr_code_sm ml-30" src="<?php echo base_url($qr_code) ?>" alt="QR Code"></p>
			<?php endif; ?>
		</div>
	</div>

</div>