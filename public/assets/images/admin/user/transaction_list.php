<style type="text/css">
	input.inv-dpick.form-control.datepicker {
		height: 45px;
		padding-left: 10px;
		border-radius: 16px !important;
		border-right: none !important;
		background: #F1F4F8;
		border-color: #F1F4F8 !important;
	}

	.input-group .input-group-addon {
		border-radius: 0;
		border-color: #F1F4F8;
		background-color: #F1F4F8;
		border-radius: 0px 10px 10px 0px;
	}

	.spce-bt p {
		margin-bottom: 5px !important;
	}

	.select2-container--default .select2-selection--single {
		background-color: #F1F4F8;
		border: 1px solid #F1F4F8 !important;
		border-radius: 10px;
	}

	.form-control {
		height: 35px;
	}

	select.form-control {
		height: 35px !important;
		margin: 0px 6px;
	}
</style>


<div class="content-wrapper">
	<!-- Main content -->
	<section class="content">

		<div class="list_area">
			<div class="row">
				<div class="col-md-12">

					<form method="GET" class="sort_report_form validate-form" action="<?php echo base_url('admin/banking/generate') ?>">
						<div class="card-box mb-10 pl-15">
							<div class="bg-light1">
								<h2 style="font-size: 26px">
									<i class="flaticon-bar-chart"></i> <?php echo trans('reports') ?>
									<span class="pull-right"></span>
								</h2>
							</div>
							<div class="row">
								<div class="col-xl-12 text-right mb-10 mt-10">
									<button type="submit" class="btn btn-info btn-report mt-0"><i class="fa fa-search"></i> <?php echo trans('show-report') ?></button>
									<a href="<?php echo base_url('admin/banking/transaction_list') ?>" class="btn btn-default reset-report reset_re"><i class="flaticon-reload"></i> <?php echo trans('reset-filter') ?></a>
								</div>
							</div>
							<div class="row m-0">
								<div class="col-xl-12">
									<div class="row spce-bt align-items-end">
										<!-- <div class="col-xl-4 mt-10">
											<p class="m-0">Date Range</p>
										</div> -->
										<div class="col-xl-3 mt-10">
											<p class="m-0">Date Range</p>
											<div class="input-group">
												<input type="text" class="inv-dpick form-control datepicker" placeholder="From" name="start_date" value="<?php if (isset($_GET['start_date'])) {
																																								echo $_GET['start_date'];
																																							} ?>" autocomplete="off">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											</div>
										</div>
										<div class="col-xl-3 mt-10">
											<div class="input-group">
												<input type="text" class="inv-dpick form-control datepicker" placeholder="To" name="end_date" value="<?php if (isset($_GET['end_date'])) {
																																							echo $_GET['end_date'];
																																						} ?>" autocomplete="off">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											</div>
										</div>
										<div class="col-xl-4 mt-10">
											<p class="m-0">Report Types</p>
											<select class="form-control single_select report_types" name="report_types" style="width: 100%;">
												<option value=""><?php echo trans('report-types') ?></option>
												<option <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 1) ? 'selected' : ''; ?> value="1">Sale</option>
												<option <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 2) ? 'selected' : ''; ?> value="2"><?php echo trans('purchase') ?></option>
												<option <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 3) ? 'selected' : ''; ?> value="3"><?php echo trans('expenses') ?></option>
												<option <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 4) ? 'selected' : ''; ?> value="4">Income</option>
												<option <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 5) ? 'selected' : ''; ?> value="5">Loan</option>
											</select>
										</div>
										<div class="col-xl-4 mt-10 expense_items" style="display: <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 3 || $_GET['report_types'] == 2 || $_GET['report_types'] == 4) ? 'block' : 'none'; ?>;">
											<select class="form-control single_select" name="vendor" style="width: 100%;">
												<option value="0"><?php echo trans('vendors') ?></option>
												<?php foreach ($vendors as $vendor) : ?>
													<option value="<?php echo html_escape($vendor->id) ?>" <?php echo (isset($_GET['vendor']) && $_GET['vendor'] == $vendor->id) ? 'selected' : ''; ?>><?php echo html_escape($vendor->name) ?></option>
												<?php endforeach ?>
											</select>
										</div>

										<div class="col-xl-4 mt-10 income_items" style="display: <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 1) ? 'block' : 'none'; ?>;">
											<select class="form-control single_select" name="customer" style="width: 100%;">
												<option value="0"><?php echo trans('all-customers') ?></option>
												<?php foreach ($customers as $customer) : ?>
													<option value="<?php echo html_escape($customer->id) ?>" <?php echo (isset($_GET['customer']) && $_GET['customer'] == $customer->id) ? 'selected' : ''; ?>><?php echo html_escape($customer->name) ?></option>
												<?php endforeach ?>
											</select>
										</div>
										<div class="col-xl-3 mt-10">
											<p class="m-0">Select Bank</p>
											<select class="form-control single_select" name="bank_id" required>
												<option value="">Select Bank</option>
												<?php foreach ($new_bankdetails as $type => $banks) : ?>
													<optgroup label="<?php echo ucfirst(str_replace('_', ' ', $type)); ?>">
														<?php foreach ($banks as $bank) : ?>
															<option value="<?php echo html_escape($bank->id); ?>" <?php if (isset($_GET['bank_id']) && $_GET['bank_id'] == $bank->id) { echo  'selected'; }; ?>>
																<?php echo html_escape($bank->account_type) . ' - ' . html_escape($bank->bank_name) . ' (' . $bank->account_number . ')'; ?>
															</option>
														<?php endforeach ?>
													</optgroup>
												<?php endforeach ?>
											</select>
										</div>
									</div>
									<div class="row align-items-center">
										<!-- <div class="col-xl-4 mt-10">
											<p class="m-0">Report Types</p>
										</div> -->
										<!-- <div class="col-xl-4 mt-10">
											<p class="m-0">Report Types</p>
											<select class="form-control single_select report_types" name="report_types" style="width: 100%;">
												<option value=""><?php echo trans('report-types') ?></option>
												<option <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 1) ? 'selected' : ''; ?> value="1">Sale</option>
												<option <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 2) ? 'selected' : ''; ?> value="2"><?php echo trans('purchase') ?></option>
												<option <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 3) ? 'selected' : ''; ?> value="3"><?php echo trans('expenses') ?></option>
												<option <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 4) ? 'selected' : ''; ?> value="4">Income</option>
												<option <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 5) ? 'selected' : ''; ?> value="5">Loan</option>
											</select>
										</div>
										<div class="col-xl-4 mt-10 expense_items" style="display: <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 3 || $_GET['report_types'] == 2 || $_GET['report_types'] == 4) ? 'block' : 'none'; ?>;">
											<select class="form-control single_select" name="vendor" style="width: 100%;">
												<option value="0"><?php echo trans('vendors') ?></option>
												<?php foreach ($vendors as $vendor) : ?>
													<option value="<?php echo html_escape($vendor->id) ?>" <?php echo (isset($_GET['vendor']) && $_GET['vendor'] == $vendor->id) ? 'selected' : ''; ?>><?php echo html_escape($vendor->name) ?></option>
												<?php endforeach ?>
											</select>
										</div>

										<div class="col-xl-4 mt-10 income_items" style="display: <?php echo (isset($_GET['report_types']) && $_GET['report_types'] == 1) ? 'block' : 'none'; ?>;">
											<select class="form-control single_select" name="customer" style="width: 100%;">
												<option value="0"><?php echo trans('all-customers') ?></option>
												<?php foreach ($customers as $customer) : ?>
													<option value="<?php echo html_escape($customer->id) ?>" <?php echo (isset($_GET['customer']) && $_GET['customer'] == $customer->id) ? 'selected' : ''; ?>><?php echo html_escape($customer->name) ?></option>
												<?php endforeach ?>
											</select>
										</div> -->
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="profit-and-loss-report mt-50">
				<div class="profit-and-loss-single">
					<p class="mb-0"><?php echo trans('income') ?></p>
					<h1 class="fs-40 text-dark"><?php echo $this->business->currency_symbol . ' ' . decimal_format($profit['income'], 2); ?></h1>
				</div>
				<div class="seperater-minus profit-and-loss-seperater">-</div>

				<div class="profit-and-loss-single">
					<p class="mb-0"><?php echo trans('expenses') ?></p>
					<h1 class="fs-40 text-dark"><?php echo $this->business->currency_symbol . ' ' . decimal_format($profit['expense'], 2); ?></h1>
				</div>
				<div class="seperater-minus profit-and-loss-seperater">=</div>

				<div class="profit-and-loss-single">
					<p class="mb-0"><?php echo trans('net-profit') ?></p>
					<?php if ($profit['profitloss'] < 0) : ?>
						<h1 class="fs-40 text-danger"><?php echo '- ' . $this->business->currency_symbol . ' ' . decimal_format(abs($profit['profitloss'])); ?></h1>
					<?php else : ?>
						<h1 class="fs-40 text-success"><?php echo $this->business->currency_symbol . ' ' . decimal_format($profit['profitloss'], 2); ?></h1>
					<?php endif; ?>
				</div>
			</div>
			<!--<h3 class="box-title" style="font-size: 26px">All Transaction List</h3>-->
			<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0" style="overflow: auto;">
				<div class="card-box">
					<table class="table table-hover cushover datatable" id="dg_table">
						<thead>
							<tr>
								<th>#</th>
								<th><?php echo trans('date') ?></th>
								<th>Bank Name</th>
								<th>Transaction Type</th>
								<th><?php echo trans('client') ?></th>
								<th><?php echo trans('category') ?></th>
								<th><?php echo trans('notes') ?></th>
								<th>Debit</th>
								<th>Credit</th>
								<th>Balance</th>
								<!--<th><?php echo trans('action') ?></th>-->
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 1;
							$bank_id = (isset($_GET['bank_id']) && !empty($_GET['bank_id'])) ? $_GET['bank_id'] : $banking[0]->id;
							$opening_bal = $this->db->where('bank_id', $bank_id)->where('type_transaction', 'Opening Balance')->get('expenses')->row()->net_amount;
							$price_sum = 0;
							foreach ($bank_transactions as $expense) :
							?>
								<tr id="row_<?php echo html_escape($expense->id); ?>">

									<td><?php echo $i; ?></td>
									<td><?php echo my_date_show($expense->date); ?></td>
									<td><?php echo html_escape($expense->bank_name); ?></td>
									<td><?php echo html_escape($expense->type_transaction); ?></td>
									<td style="text-transform: capitalize"><?php echo (!empty($expense->vendor_name)) ? $expense->vendor_name : $expense->customer_name; ?></td>
									<td><?php echo html_escape($expense->category_name); ?></td>
									<td><?php echo html_escape($expense->notes); ?></td>
									<?php if ($expense->type_transaction == "Purchase" || $expense->type_transaction == "Loan" || $expense->type_transaction == "Expense") { ?>
										<td><span class="badge badge-danger"><?php echo decimal_format($expense->net_amount); ?></span></td>
									<?php } else {  ?>
										<td> - </td>
									<?php } ?>

									<?php if ($expense->type_transaction == "Sale" || $expense->type_transaction == "Income" || $expense->type_transaction == "Opening Balance") { ?>
										<td><span class="badge badge-success"><?php echo decimal_format($expense->net_amount); ?></span></td>
									<?php } else {  ?>
										<td> - </td>
									<?php } ?>


									<?php
									if ($expense->type_transaction == "Sale" || $expense->type_transaction == "Income") {
										$balance = $opening_bal + $expense->net_amount;
										$opening_bal = $balance;

									?>

										<td><?php echo $this->business->currency_symbol . '' . decimal_format($balance); ?></td>

									<?php
									} else if ($expense->type_transaction == "Purchase" || $expense->type_transaction == "Loan" || $expense->type_transaction == "Expense") {
										$balance = $opening_bal - $expense->net_amount;
										$opening_bal = $balance;
									?>

										<td><?php echo $this->business->currency_symbol . '' . decimal_format($balance); ?></td>

									<?php } else if ($expense->type_transaction == "Opening Balance") {  ?>
										<td><?php echo $this->business->currency_symbol . '' . decimal_format($expense->net_amount); ?></td>
									<?php } ?>

									<!-- <td class="actions" width="15%">
								<?php if ($expense->type_transaction == "Sale" || $expense->type_transaction == "Purchase" || $expense->type_transaction == "Loan") {
								?>
								<a data-val="expense" data-id="<?php echo html_escape($expense->id); ?>" href="<?php echo base_url('admin/banking/deleteTransactionPayment/' . html_escape($expense->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a> 
								<?php } else { ?>
								
								<?php if ($expense->type_transaction != "Opening Balance") {
								?>
									<a href="<?php echo base_url('admin/banking/editTransaction/' . html_escape($expense->id)); ?>" class="on-default edit-row" data-placement="top" title="Edit"><i class="fa fa-pencil"></i></a> &nbsp; 
									<?php if ($expense->file != "") {
									?>
										<a href="<?php echo base_url('admin/expense/download/' . html_escape($expense->id)); ?>" class="on-default edit-row" data-placement="top" title="Download file"><i class="fa fa-download"></i></a> 
									<?php } ?>
								<?php } ?>
								
                                <a data-val="expense" data-id="<?php echo html_escape($expense->id); ?>" href="<?php echo base_url('admin/banking/deleteTransaction/' . html_escape($expense->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a> &nbsp; 
                                
								<?php } ?>
							</td>-->
								</tr>

							<?php $price_sum += $expense->net_amount;
								$i++;
							endforeach; ?>
						</tbody>
						<?php if (isset($_GET['report_types']) && !empty($_GET['report_types'])) { ?>
							<tfoot>
								<tr>
									<th colspan="10" style="text-align:right"><strong>Total : <?php echo decimal_format($price_sum, 2); ?></strong></th>
									<th></th>
								</tr>
							</tfoot>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
	</section>
</div>