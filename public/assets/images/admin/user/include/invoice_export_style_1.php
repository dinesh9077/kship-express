<?php include 'invoice_val.php'; ?>
<div style="height: 5px"></div>

<div class="card-body p-0" style="margin: 12px; border: 1px solid <?php echo html_escape($color) ?>; margin-top: 12px; ">
	<div class="row p-5 align-items-start" style="justify-content: space-between; margin: 0; align-items:start">
		<div class="col-6" style="width: 35%; float: left;">
			<?php if ($this->business->gst_invoice == 1) : ?>
				<?php if (count($biz_vat_code) > 0) :
					foreach ($tax_format as $key => $taxlable) {
				?>
						<p style="color: black; font-weight: bold; font-size:12px; font-family: opensans"><?php echo $taxlable; ?>: <?php echo html_escape($biz_vat_code[$key]) ?></p>
				<?php
					}
				endif ?>
			<?php endif ?>
		</div>
		<div class="col-6" style="width: 40%; float: right;">
			<p style="font-weight: bold; text-align: right; font-family: opensans; font-size:12px">Original Copy</p>
		</div>
		<div class="col-12">
			<p class="mb-0" style="text-transform: uppercase; font-weight: bold; text-decoration: underline; text-align:center; font-size: 12px"><?php echo html_escape($title) ?></p>
		</div>
	</div>
	<div class="row p-15" style="justify-content: <?php if (empty($logo)) : ?> center; <?php else : ?>space-between <?php endif ?>; margin: 0;  align-items: start;">
		<div class="col-3" style="width: 35%; float: left; display: <?php if (empty($logo)) : ?> none; <?php else : ?>block <?php endif ?>; ">
			<?php if (empty($logo)) : ?>
				<p><span class="alterlogo"><?php echo $business_name ?></span></p>
			<?php else : ?>
				<img width="90%" src="<?php echo base_url($logo) ?>" alt="Logo" style="max-width: 200px; max-height: 200px;">
			<?php endif ?>
		</div>
		<div class="col-5 text-right" style="width: 40%; float: right; margin-top: -10px; text-align: <?php if (empty($logo)) : ?> center; <?php else : ?>right <?php endif ?>">
			<p class="mb-0" style="text-transform: uppercase; font-family: opensans; font-weight: bold; font-size:16px"><?php echo $business_name ?></p>

			<p class="mb-0" style="text-transform: uppercase; font-weight: bold; font-family: opensans; font-size:12px"><?php echo $business_address ?></p>
			<p class="mb-0" style="text-transform: uppercase; font-weight: bold; font-family: opensans; font-size:12px"><?php echo html_escape($country) ?></p>
			<?php if (!empty($biz_number)) : ?>
				<p class="mb-0" style="text-transform: uppercase; font-weight: bold; font-family: opensans; font-size:12px"><?php echo trans('contact') . ' ' . trans('no') ?>: <?php echo html_escape($biz_number) ?></p>
			<?php endif ?>

			<?php if (!empty($website_url)) : ?>
				<p class="mb-0" style="font-weight: bold; font-family: opensans; font-size:12px"><?php echo html_escape($website_url) ?></p>
			<?php endif ?>
		</div>
	</div>

	<div class="table-responsive">
		<table class="table m-0" style="margin: 0">
			<tr class="pre_head_tr inv">
				<td width="50%" style="border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-left: 0; vertical-align: top !important">
					<p style="margin: 0; font-family: opensans;">
						<span style="font-weight: bold; font-size: 12px"><?php if (isset($page) && $page == 'Invoice') {
																				echo trans('invoice-number');
																			} else if ($page == 'Estimate') {
																				echo trans('estimate-number');
																			} else if ($page == 'Delivery') {
																				echo 'Challan Number';
																			} else {
																				echo trans('bill-number');
																			} ?>:</span>
						<span style="font-size: 12px"><?php echo html_escape($number) ?></span>
					</p>
					<p style="margin: 0; font-family: opensans;">
						<span style="font-weight: bold; font-size: 12px">
							<?php if (isset($page) && $page == 'Invoice') {
								echo trans('invoice-date');
							} else if (isset($page) && $page == 'Delivery') {
								echo 'Delivery Date';
							} else {
								echo trans('date');
							} ?>:
						</span>
						<span style="font-size: 12px"><?php echo my_date_show($date) ?></span>
					</p>
					<p style="margin: 0; font-family: opensans;">
						<span style="font-weight: bold; font-size: 12px">Place of Supply:</span>
						<span></span>
					</p>

				</td>
				<td width="50%" style="border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0; vertical-align: top !important; font-family: opensans;">
					<?php if (isset($page) && $page == 'Invoice') : ?>

						<p style="font-size: 12px"><span style="font-weight: bold; font-family: opensans; font-size: 12px"><?php echo trans('due-date') ?> </span>: <?php echo my_date_show($payment_due) ?> (<?php if ($due_limit == 1) : ?><?php echo trans('on-receipt') ?><?php else : ?><?php echo trans('within') ?> <?php echo html_escape($due_limit) ?> <?php echo trans('days') ?><?php endif ?>)</p>

					<?php else : ?>
						<?php if ($invoice->expire_on != '0000-00-00') :
							if (isset($page) && $page != 'Delivery') : ?>

								<p style="font-family: opensans;"><span style="font-weight: bold"><?php echo trans('expires-on') ?> </span>: <?php echo my_date_show($invoice->expire_on) ?></p>

							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>

				</td>
			</tr>
		</table>
	</div>

	<div class="table-responsive">
		<table class="table m-0 table-bordered" style="border-bottom: 1px solid <?php echo html_escape($color) ?>; margin: 0 ">
			<tr class="pre_head_tr inv">
				<td width="50%" style="border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-left: 0; border-top: 0; vertical-align: top !important">
					<p style="margin: 0; font-weight: bold; font-style: italic; font-size: 12px; font-family: opensans;">
						<?php if (isset($page) && $page == 'Bill') {
							echo trans('purchase-from');
						} else {
							echo trans('bill-to');
						} ?>:
					</p>
					<?php if (empty($customer_id)) : ?>
						<p class="mb-1"><?php echo trans('empty-customer') ?></p>

					<?php else : ?>

						<?php if (isset($page) && $page == 'Bill') : ?>
							<?php if (!empty(helper_get_vendor($customer_id))) : ?>
								<p style="text-transform: uppercase; font-size: 12px; font-family: opensans;"><strong><?php echo helper_get_vendor($customer_id)->name ?></strong></p>
								<p style="text-transform: uppercase; font-family: opensans; font-size: 12px"><?php echo helper_get_vendor($customer_id)->address ?></p>
								<p style="font-family: opensans; font-size: 12px"><?php echo trans('contact') . ' ' . trans('no') ?>: <?php echo helper_get_vendor($customer_id)->phone ?></p>
								<p style="font-family: opensans; font-size: 12px"><?php echo helper_get_vendor($customer_id)->email ?></p>
							<?php endif ?>
						<?php else : ?>

							<?php if (!empty(helper_get_customer($customer_id))) : ?>
								<p style="font-family: opensans; text-transform: uppercase; font-weight: bold; font-size: 12px"><?php echo helper_get_customer($customer_id)->name ?></p>
								<?php if (!empty($cus_vat_code)) : ?>
									<p><span style="font-weight: bold; font-size: 12px"><?php echo $cus_tax_format; ?></span>: <span style="text-transform: uppercase; font-size:12px"><?php echo html_escape($cus_vat_code) ?></span></p>
								<?php endif ?>
								<p style="font-family: opensans; text-transform: uppercase; font-weight: bold; font-size: 12px"><?php echo helper_get_customer($customer_id)->address ?>, <?php echo helper_get_customer($customer_id)->country ?></p>
								<p style="font-family: opensans; font-size: 12px"><span style="font-weight: bold; font-size: 12px"><?php echo trans('contact') . ' ' . trans('no') ?></span>: <?php echo helper_get_customer($customer_id)->phone ?></p>
								<p style="font-family: opensans; font-size: 12px"><span style="font-weight: bold; font-size: 12px"><?php echo trans('email') . ' ' . trans('id') ?></span>: <?php echo helper_get_customer($customer_id)->email ?></p>
							<?php endif ?>
						<?php endif ?>

					<?php endif ?>

				</td>
				<td width="50%" style="font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0; border-top: 0; vertical-align: top !important">
					<p style="font-family: opensans; margin: 0; font-weight: bold; font-style: italic; font-size:12px">Shipped to:</p>
					<?php if (empty($customer_id)) : ?>
						<p style="font-size: 12px"><?php echo trans('empty-customer') ?></p>

					<?php else : ?>

						<?php if (isset($page) && $page == 'Bill') : ?>
							<?php if (!empty(helper_get_vendor($customer_id))) : ?>
								<p style="font-family: opensans; text-transform: uppercase; font-size:12px"><strong><?php echo helper_get_vendor($customer_id)->name ?></strong></p>
								<p style="font-family: opensans; text-transform: uppercase; font-size:12px"><?php echo helper_get_vendor($customer_id)->address ?></p>
								<p style="font-family: opensans; font-size: 12px"><?php echo trans('contact') . ' ' . trans('no') ?>: <?php echo helper_get_vendor($customer_id)->phone ?></p>
								<p style="font-family: opensans; font-size: 12px"><?php echo helper_get_vendor($customer_id)->email ?></p>
							<?php endif ?>
						<?php else : ?>

							<?php if (!empty(helper_get_customer($customer_id))) : ?>
								<p class="mt-0 mb-0" style="font-family: opensans; text-transform: uppercase; font-weight: bold; font-size:12px"><?php echo helper_get_customer($customer_id)->name ?></p>
								<?php if ($this->business->gst_invoice == 1) : ?>
									<?php if (!empty($cus_vat_code)) : ?>
										<p class="mt-0 mb-0"><span style="font-family: opensans; font-weight: bold; font-size:12px"><?php echo $cus_tax_format; ?></span>: <span style="text-transform: uppercase; font-size: 12px"><?php echo html_escape($cus_vat_code) ?></span></p>
									<?php endif ?>
								<?php endif ?>
								<p style="font-family: opensans; font-size: 12px; text-transform: uppercase; font-weight: bold"><?php echo helper_get_customer($customer_id)->address ?>, <?php echo helper_get_customer($customer_id)->country ?></p>
								<p style="font-family: opensans; font-size: 12px"><span style="font-weight: bold; font-size: 12px"><?php echo trans('contact') . ' ' . trans('no') ?></span>: <?php echo helper_get_customer($customer_id)->phone ?></p>
								<p style="font-family: opensans; font-size: 12px"><span style="font-weight: bold; font-size: 12px"><?php echo trans('email') . ' ' . trans('id') ?></span>: <?php echo helper_get_customer($customer_id)->email ?></p>
							<?php endif ?>
						<?php endif ?>

					<?php endif ?>

				</td>
			</tr>
		</table>
	</div>

	<br>

	<div class="row p-0 table_area m-0" style="width: 100%; margin: 0">
		<div class="col-12 table-responsive p-0" style="padding: 0">
			<table class="table m-0" style="border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-left: 0; border-right: 0; margin: 0">
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

						<th colspan="<?php echo $cols; ?>" style="font-family: opensans; font-weight: bold; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-left: 0 font-size: 12px; color:black">
							<?php echo trans('items') ?>
						</th>
						<?php if ($this->business->hsn_sac == 1) : ?>
							<th style="font-family: opensans; font-weight: bold; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; color:black">
								HSN/SAC Code
							</th>
						<?php endif; ?>
						<th style="font-family: opensans; font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; color:black">
							<?php echo trans('rate') ?>
						</th>
						<th style="font-family: opensans; font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; color:black">
							<?php echo trans('quantity') ?>
						</th>
						<th style="font-family: opensans; font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; color:black">
							Unit
						</th>
						<?php if ($this->business->invoice_discount == 1) : ?>
							<th style="font-family: opensans; font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; color:black">
								Discount
							</th>
						<?php endif; ?>
						<?php if ($this->business->invoice_tax == 1) : ?>
							<th style="font-family: opensans; font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; color:black">
								GST
							</th>
						<?php endif; ?>
						<th style="font-family: opensans; font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; color:black; border-right: 0">
							<?php echo trans('amount') ?>
						</th>
					</tr>
				</thead>

				<tbody>

					<?php
					$discount_percent = 0;
					if (isset($page_title) && $page_title == 'Invoice Preview') : ?>

						<?php
						if (!empty($this->session->userdata('item'))) {
							$total_items = count($this->session->userdata('item'));
						} else {
							$total_items = 0;
						}
						?>

						<?php if (empty($total_items)) : ?>
							<tr>
								<td colspan="8" style="font-family: opensans; font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0">
									<?php echo trans('empty-items') ?>
								</td>
							</tr>
						<?php else : ?>

							<?php
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
									<td style="border-top: 0; border-left: 0; border: 1px solid <?php echo html_escape($color) ?>;" colspan="<?php echo $cols; ?>">
										<p class="m-0" style="font-family: opensans; font-weight: bold; font-size: 12px"><?php $product_id = $this->session->userdata('item')[$i] ?></p>

										<?php
										if (is_numeric($product_id)) {
											echo '<p class="m-0" style="font-weight: bold; font-size: 12px">' . helper_get_product($product_id)->name . '</p> <p style="font-size: 12px>' . nl2br(helper_get_product($product_id)->details) . '</p>';
										} else {
											echo html_escape($product_id);
										}
										?>

									</td>
									<?php if ($this->business->hsn_sac == 1) : ?>
										<td style="font-family: opensans; border-top: 0;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px"><?php echo $this->session->userdata('hsn_sac')[$i] ?></td>
									<?php endif; ?>

									<td style="font-family: opensans; border-top: 0; text-align: right;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px">
										<?php if (!empty($currency_symbol)) {
											echo html_escape($currency_symbol);
										} ?><?php echo $this->session->userdata('price')[$i] ?>
									</td>
									<td style="font-family: opensans; border-top: 0; text-align: center;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px">
										<?php echo $this->session->userdata('quantity')[$i] ?>
									</td>
									<td style="font-family: opensans; border-top: 0; text-align: center;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px">
										<?php if (!empty($this->session->userdata('unit')[$i])) {
											echo $this->session->userdata('unit')[$i];
										} else {
											echo "";
										} ?>
									</td>
									<?php if ($this->business->invoice_discount == 1) : ?>
										<td style="font-family: opensans; border-top: 0; text-align: center;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px">
											<?php
											if (!empty($this->session->userdata('discount')[$i])) {
												echo html_escape($this->session->userdata('discount')[$i]) . '%';
											} else {
												echo "-";
											}
											?>
										</td>
									<?php endif; ?>

									<?php if ($this->business->invoice_tax == 1) : ?>
										<td style="font-family: opensans; border-top: 0; text-align: center;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px">
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
									<td style="font-family: opensans; border-top: 0; text-align: right; padding-right:20px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0; font-size: 12px">
										<?php if (!empty($currency_symbol)) {
											echo html_escape($currency_symbol);
										} ?><?php echo decimal_format($this->session->userdata('total_price')[$i], 2) ?>
									</td>
								</tr>
							<?php } ?>

						<?php endif ?>

					<?php else : ?>
						<?php $items = helper_get_invoice_items($invoice->id) ?>
						<?php if (empty($items)) : ?>
							<tr>
								<td colspan="8" style="font-family: opensans; font-weight: bold; text-align: center; padding: 5px 10px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0">
									<?php echo trans('empty-items') ?>
								</td>
							</tr>
						<?php else : ?>

							<?php
							foreach ($items as $item) :
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
									<td style="font-family: opensans; border-top: 0;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse;  border-left: 0" colspan="<?php echo $cols; ?>">
										<p style="font-family: opensans; margin-bottom: 0px; font-weight: bold; font-size: 12px"><?php echo html_escape($item->item_name) ?></p>
										<?php if ($this->business->enable_serial_no == 1) : ?>
											<p class="m-0" style="font-family: opensans; font-size: 12px"><?php echo html_escape($item->serial_no) ?></p>
										<?php endif; ?>
										<p class="m-0" style="font-family: opensans; font-size: 12px"><?php echo nl2br($item->details) ?></p>
									</td>
									<?php if ($this->business->hsn_sac == 1) : ?>
										<td style="font-family: opensans; border-top: 0; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px;">
											<p class="m-0"><?php echo $item->hsn_sac ?></p>
										</td>
									<?php endif; ?>
									<td style="font-family: opensans; border-top: 0; text-align: right; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px;">
										<p class="m-0"><?php if (!empty($currency_symbol)) {
															echo html_escape($currency_symbol);
														} ?><?php echo number_format($item->price, 2) ?></p>
									</td>
									<td style="font-family: opensans; border-top: 0; text-align: center; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px;">
										<p class="m-0"><?php echo html_escape($item->qty) ?></p>
									</td>
									<td style="font-family: opensans; border-top: 0; text-align: center; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px;">
										<p class="m-0">
											<?php
											if (!empty($item->unit)) {
												echo html_escape($item->unit);
											} else {
												echo "-";
											}
											?>
										</p>
									</td>
									<?php if ($this->business->invoice_discount == 1) : ?>
										<td style="font-family: opensans; border-top: 0; text-align: center; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px;">
											<?php if (!empty($item->discount)) {
												echo html_escape($item->discount) . '%';
											} else {
												echo "-";
											} ?>
										</td>
									<?php endif; ?>
									<?php if ($this->business->invoice_tax == 1) : ?>
										<td style="font-family: opensans; border-top: 0; text-align: center; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px;">
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
									<td style="font-family: opensans; border-top: 0; text-align: right; padding-right:20px;  border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; border-right: 0">
										<p class="m-0"><?php if (!empty($currency_symbol)) {
															echo html_escape($currency_symbol);
														} ?><?php echo decimal_format($item->total, 2) ?></p>
									</td>
								</tr>
							<?php
								$discount_percent += $item->discount;
							endforeach
							?>
						<?php endif ?>
					<?php endif ?>

					<?php if ($discount > 0 && !empty($discount)) : ?>
						<?php if ($this->business->invoice_discount == 1) : ?>
							<tr class="inv">
								<td style="font-family: opensans; border-top: 0; text-align: center; border: 0"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td class="text-right border-0" style="padding: 5px 12px; font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; font-weight: bold">Total :</td>
								<td class="font-family: opensans; border-0" style="padding: 5px 20px; text-align: right; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0">
									<span>
										<?php
										if (!empty($currency_symbol)) {
											echo html_escape($currency_symbol);
										}
										if (isset($page_title) && $page_title == 'Invoice Preview') {
											echo decimal_format($this->session->userdata('sub_total'), 2);
										} else {
											echo decimal_format($sub_total, 2);
										}
										?>
									</span>
								</td>
							</tr>
							<tr class="inv">
								<td style="font-family: opensans; border-top: 0; text-align: center; border: 0"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td class="text-right border-0" style="font-family: opensans; border-top: 0; padding: 5px 12px; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-weight: bold; font-size: 12px;">
									<?php echo trans('discount') ?> <?php echo html_escape($discount_percent) ?>% :
								</td>
								<td class="border-0" style="font-family: opensans; border-top: 0; padding: 5px 20px; text-align: right; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0; font-size: 12px"><span><?php if (!empty($currency_symbol)) {
																																																																	echo html_escape($currency_symbol);
																																																																}
																																																																if (isset($page_title) && $page_title == 'Invoice Preview') {
																																																																	echo decimal_format($this->session->userdata('total_discount'), 2);
																																																																} else {
																																																																	echo decimal_format($discount, 2);
																																																																} ?></span></td>
							</tr>
							<tr class="inv">
								<td style="font-family: opensans; border-top: 0; text-align: center; border: 0"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
								<td style="font-family: opensans; padding: 5px 12px; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; text-align: right;  font-size: 12px; font-weight: bold"><strong><?php echo trans('sub-total') ?>:</strong></td>
								<td style="font-family: opensans; padding: 5px 20px; text-align: right; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; text-align: right; border-right: 0;  font-size: 12px">
									<span>
										<?php if (!empty($currency_symbol)) {
											echo html_escape($currency_symbol);
										}
										if (isset($page_title) && $page_title == 'Invoice Preview') :  echo decimal_format($this->session->userdata('sub_total') - $this->session->userdata('total_discount'), 2);
										else : ?>
										<?php echo decimal_format($sub_total - $discount, 2);
										endif; ?>
									</span>
								</td>
							</tr>
						<?php endif; ?>
					<?php else : ?>
						<tr class="inv">
							<td style="font-family: opensans; border-top: 0; text-align: center; border: 0"></td>
							<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
							<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
							<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
							<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
							<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
							<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
							<td class="text-right" style="padding: 5px 12px; font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; text-align: right; font-weight: bold"><strong><?php echo trans('sub-total') ?> : </strong></td>
							<td style="padding: 5px 20px; text-align: right; font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; border-right: 0"><span><?php if (!empty($currency_symbol)) {
																																																								echo html_escape($currency_symbol);
																																																							}
																																																							if (isset($page_title) && $page_title == 'Invoice Preview') {
																																																								echo decimal_format($this->session->userdata('sub_total'), 2);
																																																							} else {
																																																								echo decimal_format($sub_total, 2);
																																																							} ?></span></td>
						</tr>
					<?php endif ?>


					<?php if ($this->business->invoice_tax == 1) : ?>
						<?php if (isset($page_title) && $page_title != 'Invoice Preview') : ?>

							<?php if (!empty($taxes)) : ?>
								<?php foreach ($taxes as $tax) : ?>
									<?php if ($tax != 0) : ?>
										<tr class="inv">
											<td style="border-top: 0; text-align: center; border: 0"></td>
											<td style="border-top: 0; text-align: center;"></td>
											<td style="border-top: 0; text-align: center;"></td>
											<td style="border-top: 0; text-align: center;"></td>
											<td style="border-top: 0; text-align: center;"></td>
											<td style="border-top: 0; text-align: center;"></td>
											<td style="border-top: 0; text-align: center;"></td>
											<td class="text-right" style="padding: 5px 12px; font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; text-align: right; font-weight: bold">
												<strong><?php echo $tax->tax_type; ?> :</strong>
											</td>
											<td style="padding: 5px 20px; text-align: right; font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; border-right: 0">
												<span><?php if (!empty($currency_symbol)) {
															echo html_escape($currency_symbol);
														} ?><?php echo decimal_format($tax->tax_value); ?></span>
											</td>
										</tr>
									<?php endif ?>
								<?php endforeach ?>
							<?php endif ?>
						<?php else : ?>


							<?php
							$taxes = ($this->session->userdata('tax_value')) ? $this->session->userdata('tax_value') : '';
							if (!empty($taxes)) :
							?>
								<?php foreach ($taxes as $key => $tax) : ?>
									<?php if ($tax != 0) : ?>
										<tr class="inv">
											<td style="border-top: 0; text-align: center; border: 0"></td>
											<td style="border-top: 0; text-align: center;"></td>
											<td style="border-top: 0; text-align: center;"></td>
											<td style="border-top: 0; text-align: center;"></td>
											<td style="border-top: 0; text-align: center;"></td>
											<td style="border-top: 0; text-align: center;"></td>
											<td style="border-top: 0; text-align: center;"></td>
											<td class="text-right" style="padding: 5px 12px; font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; text-align: right; font-weight: bold">
												<strong>
													<?php echo $key; ?>:
												</strong>
											</td>
											<td style="padding: 5px 20px; text-align: right; font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; border-right: 0">
												<span><?php if (!empty($currency_symbol)) {
															echo html_escape($currency_symbol);
														} ?><?php echo decimal_format($tax); ?></span>
											</td>
										</tr>
									<?php endif ?>
								<?php endforeach ?>
							<?php endif ?>


						<?php endif ?>
					<?php endif ?>



					<tr class="inv">
						<td style="font-family: opensans; border-top: 0; text-align: center; border: 0"></td>
						<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
						<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
						<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
						<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
						<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
						<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
						<td class="text-right" style="padding: 5px 12px; font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; font-weight: bold"><strong><?php echo trans('grand-total') ?>:</strong></td>
						<td style="padding: 5px 20px; text-align: right; font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0; font-size: 12px"><span><?php if (!empty($currency_symbol)) {
																																																							echo html_escape($currency_symbol);
																																																						} ?><?php echo number_format($grand_total, 2) ?></span></td>
					</tr>


					<?php foreach (get_invoice_payments($invoice->id) as $payment) : ?>
						<tr class="inv text-dark">
							<td style="font-family: opensans; border-top: 0; text-align: center; border: 0"></td>
							<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
							<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
							<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
							<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
							<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
							<td style="font-family: opensans; border-top: 0; text-align: center;"></td>
							<td class="text-right" style="padding: 5px 12px; font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-weight: bold ">
								<p style="font-family: opensans; font-weight: bold; font-size: 12px; color: black"><?php echo trans('payment-on') ?> <?php echo my_date_show($payment->payment_date) ?> <?php echo trans('using') ?> <?php echo get_using_methods($payment->payment_method) ?>:</p>
							</td>
							<td style="padding: 5px 20px; text-align: right; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0;">
								<p style="font-size: 12px"><?php if (!empty($currency_symbol)) {
																echo html_escape($currency_symbol);
															} ?><?php echo decimal_format($payment->amount / $c_rate, 2) ?></p>
							</td>
						</tr>
					<?php endforeach ?>

					<tr class="inv">
						<td style="font-family: opensans; border-top: 0; text-align: center; border: 0"></td>
						<td style="font-family: opensans; border-top: 0; text-align: center; border: 0"></td>
						<td style="font-family: opensans; border-top: 0; text-align: center; border: 0"></td>
						<td style="font-family: opensans; border-top: 0; text-align: center; border: 0"></td>
						<td style="font-family: opensans; border-top: 0; text-align: center; border: 0"></td>
						<td style="font-family: opensans; border-top: 0; text-align: center; border: 0"></td>
						<td style="font-family: opensans; border-top: 0; text-align: center; border-bottom: 0"></td>
						<td class="text-right" style="padding: 5px 12px; font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-size: 12px; font-weight: bold"><strong><?php echo trans('amount-due') ?>:</strong></td>
						<td style="padding: 5px 20px; text-align: right; font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0; font-size: 12px">
							<p style="font-family: opensans; font-size: 12px">
							<strong>
									<?php
									if ($status == 2) {
										if (!empty($currency_symbol)) {
											echo html_escape($currency_symbol);
										}
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
							</p>
						</td>
					</tr>

				</tbody>
			</table>

			<table class="table" style="margin: 0">
				<tbody>
					<tr>
						<td style="font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; font-weight: bold; border-right: 0; border-left: 0; border-bottom: 0; font-size:12px">
							<?php echo trans('amount-due') ?>:
							<?php if ($status == 2) : ?>
								<?php if (!empty($currency_symbol)) {
									echo html_escape($currency_symbol);
								} ?>0.00
							<?php else : ?>
								<?php if (isset($page_title) && $page_title == 'Invoice Preview') : ?>
									<?php echo convertNumberToWordsForIndia($convert_total / $c_rate) ?>
								<?php else : ?>
									<?php
									$amotdue = $convert_total - get_total_invoice_payments($invoice->id, $invoice->parent_id);
									echo convertNumberToWordsForIndia($amotdue / $c_rate) ?>
								<?php endif ?>
							<?php endif ?>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="table" style="margin: 0">
				<tbody>
					<tr>
						<td style="font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; vertical-align: top !important; border-left: 0" width="80%">
							<p style="font-family: opensans; text-decoration: underline; font-weight: bold">Bank Details: </p>
							<?php
							$activeBanking = $this->db->where('user_id', $this->session->userdata('id'))->where('bank_print_invoice', 1)->get('banking')->row();
							if (!empty($activeBanking)) :
							?>
								<p style="font-family: opensans; font-size: 12px"><span style="font-weight: bold; font-size: 12px">Account Holder Name:</span> <?php echo html_escape($activeBanking->account_name); ?> </p>
								<p style="font-family: opensans; font-size: 12px"><span style="font-weight: bold; font-size: 12px">Bank name: </span><?php echo html_escape($activeBanking->bank_name); ?></p>
								<p style="font-family: opensans; font-size: 12px"><span style="font-weight: bold; font-size: 12px">Account Number: </span><?php echo html_escape($activeBanking->account_number); ?></p>
								<p style="font-family: opensans; font-size: 12px"><span style="font-weight: bold; font-size: 12px">IFSC: </span><?php echo html_escape($activeBanking->ifsc); ?></p>
								<?php if ($key == $length) : ?>
									<hr style="border-top: 1px solid <?php echo html_escape($color) ?>; ">
								<?php endif; ?>

							<?php endif; ?>
						</td>
						<td style="font-family: opensans; border: 1px solid <?php echo html_escape($color) ?>; border-collapse: collapse; border-right: 0; " width="20%">
							<?php if (!empty($pay_qrcode)) : ?>
								<p style="font-weight: bold; font-size: 12px">Scan QR-code to Pay</p>
								<p style="text-align: center"><img src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code" style="width: 130px"></p>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="table-responsive">
				<table class="table m-0 table-bordered" style="margin: 0">
					<tr class="pre_head_tr inv">
						<td width="50%" style="border: 1px solid <?php echo html_escape($color) ?>; border-right: 0; border-top: 0; border-left: 0; border-top: 0; vertical-align: top !important;">
							<p style="font-family: opensans; font-weight: bold; text-decoration: underline">Notes / Terms: </p>
							<p class="text-centers" style="font-family: opensans; font-size: 12px"><?php echo $footer_note ?></p>
						</td>
						<td width="50%" style="vertical-align: top !important; border: 1px solid <?php echo html_escape($color) ?>; border-right: 0; border-top: 0; ">
							<p style="font-family: opensans; font-weight: bold; font-size: 12px; width: 100%">Receiver's Signature: _________________________</p>
							<hr>
							<div style="text-align: right">
								<p style="font-family: opensans; text-align: right; font-weight: bold; font-size: 12px; width: 100%;">For <?php echo $business_name ?></p>
								<br>
								<br>
								<p style="font-family: opensans; text-align: right; font-weight: bold; font-size: 12px; width: 100%;">Authorised Signatory</p>
							</div>
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