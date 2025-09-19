<div class="content-wrapper">
	<section class="content">
		<div class="card-box">
			<div class="row">
				<div class="col-md-12">
					<div class="d-flex justify-content-between align-items-center">
						<h2 style="font-size: 26px"><i class="fa fa-file"></i> Customer Statement</h2>
						<div class="add-btn mb-25">
							<a href="#" class="btn btn-default btn-rounded print"><i class="fa fa-print"></i>
								<?php echo trans('print') ?>
							</a>
						</div>
					</div>

					<form method="GET" class="sort_report_form validate-form"
						action="<?php echo base_url('admin/reports/customer_statement') ?>">
						<div class="reprt-box mb-10 pl-15">
							<div class="row">
								<div class="col-xl-12 text-right pur_re">
									<button type="submit" class="btn btn-info btn-report"><i
											class="fa fa-search"></i>
										<?php echo trans('show-report') ?>
									</button>
								</div>
							</div>
							<div class="row m-0">
								<div class="col-xl-12 pr-0">
									<div class="row align-items-end">
										<div class="col-xl-3 mt-10 pl-0">
											<p class="m-0">Date Range</p>
											<div class="input-group">
												<input type="text" class="inv-dpick form-control datepicker"
													placeholder="From" name="start" value="<?php if (isset($_GET['start'])) {
														echo $_GET['start'];
													} ?>" autocomplete="off" required>
												<span class="input-group-addon"><i
														class="fa fa-calendar"></i></span>
											</div>
										</div>
										<div class="col-xl-3 mt-10 pl-0">
											<div class="input-group">
												<input type="text" class="inv-dpick form-control datepicker"
													placeholder="To" name="end" value="<?php if (isset($_GET['end'])) {
														echo $_GET['end'];
													} ?>" autocomplete="off" required>
												<span class="input-group-addon"><i
														class="fa fa-calendar"></i></span>
											</div>
										</div>
										<div class="col-xl-3 mt-10 pl-0">
											<p class="m-0">Customer</p>
											<select class="form-control single_select customer_id" required
												name="customer">
												<option value="">Select Customer</option>
												<?php foreach ($customers as $key => $customer) { ?>
													<option value="<?php echo $customer->id; ?>" <?php echo (isset($_GET['customer']) && $_GET['customer'] == $customer->id) ? 'selected' : ''; ?>><?php echo $customer->name; ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="col-xl-3 mt-10 pl-0">
											<p class="m-0">Report Type</p>
											<select class="form-control single_select report_type" required
												name="report_type">
												<option value="">
													<?php echo trans('report-types') ?>
												</option>
												<option <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 1) ? 'selected' : ''; ?>
													value="1">Outstanding Invoices</option>
												<option <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 2) ? 'selected' : ''; ?>
													value="2">Account activity</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>

			<div class="print_area">
				<?php if (isset($_GET['report_type']) && $_GET['report_type'] == 1) { ?>
					<div class="col-md-12 mt-50">
						<div class="table-responsive">
							<table class="table table-hover cushover">
								<thead>
									<tr>
										<th>Invoice #</th>
										<th>Invoice date</th>
										<th>Due date</th>
										<th>Total</th>
										<th>Paid</th>
										<th>Due</th>
									</tr>
								</thead>
								<tbody>
									<?php $amt = 0;
									if (empty($statements)) { ?>
										<tr>
											<td colspan="6" class="text-center p-30"><strong>No Data Found</strong>
											</td>
										</tr>
									<?php 
									} else { ?>
										<?php
										$amt = 0;
										foreach ($statements as $statement) {
											$currency_symbol = $statement->c_currency_symbol;

											$conv_total_amt = $total_amt = $statement->grand_total;
											$conv_paid_amt = $paid_amt = get_total_invoice_payments($statement->id, $statement->parent_id);
											$conv_due_amt = $due_amt = $total_amt - $paid_amt;
											if ($currency_symbol != $this->business->currency_symbol) {
												$conv_total_amt = $total_amt * $statement->c_rate;
												$conv_paid_amt = $paid_amt * $statement->c_rate;
												$conv_due_amt = $due_amt * $statement->c_rate;
											}

											$expenses = $this->db->where('invoice_id', $statement->id)->get('expenses')->result();
											$paidamt = 0;
											foreach ($expenses as $expense) {
												$paidamt += $expense->net_amount;
											}
											?>
											<tr>
												<td>
													<p>
														<strong class="text-primary">Invoice #
															<?php echo ($statement->prefix) ? $statement->prefix . ' - ' : ''; ?>
															<?php echo html_escape($statement->number) ?>
															<small class="text-<?php echo ($statement->status == 0) ? 'danger' : 'black' ?>"><?php echo ($statement->status == 0) ? '(Drafted)' : '' ?></small>
														</strong>
													</p>
												</td>
												<td>
													<?php echo my_date_show($statement->date); ?>
												</td>
												<td>
													<?php echo my_date_show($statement->payment_due); ?>
													<p><strong class="text-danger">Overdue</strong></p>
												</td>
												<td>
													<span class="total-price <?php echo ($statement->status == 0) ? 'text-danger' : '' ?>">
														<?php
														if (!empty($currency_symbol)) {
															echo html_escape($currency_symbol);
														}
														echo decimal_format(html_escape($total_amt), 2); ?>
													</span>
													<br>
													<span class="conver-total">
														<?php echo '(' . $this->business->currency_symbol . '' . number_format($conv_total_amt, 2) . ' ' . user()->currency_code . ')'; ?>
													</span>
												</td>
												<td>
													<span class="total-price <?php echo ($statement->status == 0) ? 'text-danger' : '' ?> <?php echo ($statement->status != 0 && $paid_amt != 0) ? 'text-success' : '' ?>">
														<?php
														if (!empty($currency_symbol)) {
															echo html_escape($currency_symbol);
														}
														echo decimal_format(html_escape($paid_amt), 2); ?>
													</span>
													<?php if($paid_amt != 0) { ?>
													<br>
													<span class="conver-total">
														<?php echo '(' . $this->business->currency_symbol . '' . number_format($conv_paid_amt, 2) . ' ' . user()->currency_code . ')'; ?>
													</span>
													<?php } ?>
												</td>
												<td>
													<?php 
													if($statement->status != 0){ ?>
														<span class="total-price <?php echo ($statement->status == 0) ? 'text-danger' : '' ?>">
															<?php
															if (!empty($currency_symbol)) {
																echo html_escape($currency_symbol);
															}
															echo decimal_format(html_escape($due_amt), 2); ?>
														</span>
														<br>
														<span class="conver-total">
															<?php echo '(' . $this->business->currency_symbol . '' . number_format($conv_due_amt, 2) . ' ' . user()->currency_code . ')'; ?>
														</span>
													<?php 
													} else { 
														echo ' ~ ';
													} ?>
												</td>
											</tr>
											<?php
											// $amt += ($statement->convert_total - $paidamt);
											if($statement->status != 0){
												$amt += $conv_due_amt;
											}
										}
									} ?>
								</tbody>
								<tfoot>
									<tr>

										<td colspan='5' style="text-align:right;">
											<span class="label label-default">Outstanding balance (<?php echo user()->currency_code; ?>)</span>
										</td>
										<td>
											<span class="label label-default">
												<?php echo $this->business->currency_symbol . '' . number_format($amt, 2); ?>
											</span>
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				<?php } elseif (isset($_GET['report_type']) && $_GET['report_type'] == 2) { ?>
					<div class="col-md-12 mt-50">
						<div class="table-responsive">
							<table class="table table-hover cushover">
								<thead>
									<tr>
										<th>Date</th>
										<th>Item</th>
										<th>Amount</th>
										<th>Balance</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$balance = 0;
									if (empty($statements)) {
										?>
										<tr>
											<td colspan="6" class="text-center p-30"><strong>No Data Found</strong>
											</td>
										</tr>
										<?php
									} else {
										$balance = 0;
										foreach ($statements as $statement) {
											$currency_symbol = $statement->c_currency_symbol;

											$conv_total_amt = $total_amt = $statement->grand_total;
											if ($currency_symbol != $this->business->currency_symbol) {
												$conv_total_amt = $total_amt * $statement->c_rate;
											}
											$expensespaids = $this->db->where('invoice_id', $statement->id)->get('expenses')->result();
											if($statement->status != 0){
												$balance += $conv_total_amt;
											}
											?>
											<tr>
												<td>
													<?php echo my_date_show($statement->date); ?>
												</td>
												<td>
													<p>
														<strong class="text-primary">Invoice #
															<?php echo ($statement->prefix) ? $statement->prefix . ' - ' : ''; ?>
															<?php echo html_escape($statement->number) ?>
															<small class="text-<?php echo ($statement->status == 0) ? 'danger' : 'black' ?>"><?php echo ($statement->status == 0) ? '(Drafted)' : '' ?></small>
														</strong>
													</p>
													<?php echo my_date_show($statement->payment_due); ?>
												</td>
												<td>
													<span class="total-price <?php echo ($statement->status == 0) ? 'text-danger' : '' ?>">
														<?php
														if (!empty($currency_symbol)) {
															echo html_escape($currency_symbol);
														}
														echo decimal_format(html_escape($total_amt), 2); ?>
													</span>
													<br>
													<span class="conver-total">
														<?php echo '(' . $this->business->currency_symbol . '' . number_format($conv_total_amt, 2) . ' ' . user()->currency_code . ')'; ?>
													</span>
												</td>
												<td>
													<strong class="text-black">
														<?php echo ($statement->status != 0) ? $this->business->currency_symbol . '' . number_format($balance, 2) : ' ~ '; ?>
													</strong>
												</td>
											</tr>
											<?php
											if (count($expensespaids)) {
												foreach ($expensespaids as $expaid) {
													// $balance -= $expaid->net_amount; 
													$ex_conv_total_amt = $ex_total_amt = $expaid->net_amount;
													if ($currency_symbol != $this->business->currency_symbol) {
														$ex_conv_total_amt = $ex_total_amt * $statement->c_rate;
													}
													$balance -= $ex_conv_total_amt; 
													?>
													<tr>
														<td>
															<?php echo my_date_show($expaid->created_at); ?>
														</td>
														<td>
															<p><strong class="text-primary"> Payment made for Invoice #
																	<?php echo ($statement->prefix) ? $statement->prefix . ' - ' : ''; ?>
																	<?php echo html_escape($statement->number) ?>
																</strong></p>
														</td>
														<td>
															<span class="total-price <?php echo ($ex_total_amt > 0) ? 'text-success' : '' ?>">
																<?php
																if (!empty($currency_symbol)) {
																	echo html_escape($currency_symbol);
																}
																echo decimal_format(html_escape($ex_total_amt), 2); ?>
															</span>
															<br>
															<span class="conver-total">
																<?php echo '(' . $this->business->currency_symbol . '' . number_format($ex_conv_total_amt, 2) . ' ' . user()->currency_code . ')'; ?>
															</span>
														</td>
														<td>
															<strong class="text-danger">
																<?php echo $this->business->currency_symbol . '' .  number_format($balance, 2); ?>
															</strong>
														</td>
													</tr>
													<?php
												}
											}
											?>
											<?php

										}
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan='3' style="text-align:right;"><span
												class="label label-default">Closing balance on
												<?php echo date('M,d Y') ?> (
												<?php echo user()->currency_code; ?>)
											</span></td>
										<td><span class="label label-default">
												<?php echo $this->business->currency_symbol . '' . number_format($balance, 2) ?>
											</span></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</section>
</div>