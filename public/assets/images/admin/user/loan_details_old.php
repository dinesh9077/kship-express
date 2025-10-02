<style>
	@media only screen and (max-width: 320px) {
		.toggle.btn.btn-info {
			width: 60px !important;
			height: 15px !important;
		}

		.toggle.btn.btn-light.off {
			width: 60px !important;
			height: 15px !important;
		}
	}

	label.btn.btn-info.toggle-on {
		color: #fff;
		/* padding: 3% 0%; */
		text-align: left;
		font-size: 12px;
	}

	label.btn.btn-light.toggle-off {
		padding: 1% 4%;
		text-align: right;
		font-size: 12px;
		justify-content: flex-end;
	}

	.toggle-handle {
		background: #F1F1F1 !important;
		width: 50px !important;
	}
</style>
<?php
	$pr = $int = 0;
	foreach ($loan_details as $loans) {
		$pr += $loans->principal_amt;
		$int += $loans->intrest_amt;
	} 
?>
<div class="content-wrapper">

	<!-- Main content -->
	<section class="content">

		<div class="col-md-10 m-auto box add_area mt-50" style="display: <?php if ($page_title == "Edit") { echo "block"; } else { echo "none"; } ?>">
			<div class="box-header with-border">
				<?php if (isset($page_title) && $page_title == "Edit") : ?>
					<h3 class="box-title" style="font-size: 24px"><i class="flaticon-contract"></i>&nbsp;Edit New EMI</h3>
				<?php else : ?>
					<h3 class="box-title" style="font-size: 24px"><i class="flaticon-contract"></i>&nbsp;Add New EMI</h3>
				<?php endif; ?>

				<div class="box-tools pull-right">
					<?php if (isset($page_title) && $page_title == "Edit") : ?>
						<a href="<?php echo base_url('admin/banking/view_loan/' . $loan_id) ?>" class="btn btn-default rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
					<?php else : ?>
						<a href="<?php echo base_url('admin/banking/view_loan/' . $loan_id) ?>" class="btn btn-default btn-sm rounded"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
					<?php endif; ?>
				</div>
			</div>

			<form method="post" enctype="multipart/form-data" class="row validate-form mt-20 p-30" action="<?php echo base_url('admin/banking/add_loan_emi') ?>" role="form" novalidate>

				<div class="col-lg-6 form-group">
					<label class="col-sm-12 control-label p-0" for="example-input-normal">Select Bank To Transfer Loan <span class="text-danger">*</span></label>
					<select class="form-control" name="bank_id" required>
						<option value=""><?php echo trans('select') ?></option>
						<?php foreach ($bankings as $banking) : ?>
							<?php if ($banking->account_type != "Credit Card") : ?>
								<option value="<?php echo html_escape($banking->id); ?>" <?php echo ($loan_system[0]['bank_id'] == $banking->id) ? 'selected' : ''; ?>>
									<?php if ($banking->account_type == "Bank") { ?>
										<i class="fa fa-bank"></i><?php echo html_escape($banking->account_type) . '-' . html_escape($banking->bank_name); ?>
									<?php } else { ?>
										<i class="fa fa-money"></i><?php echo html_escape($banking->account_type) . '-' . html_escape($banking->bank_name); ?>
									<?php } ?>
								</option>
							<?php endif; ?>
						<?php endforeach ?>
					</select>
				</div>
				<div class="col-lg-6 form-group">
					<label>EMI Amount <span class="text-danger">*</span></label>
					<input type="text" class="form-control" name="emi_amount" value="<?php echo html_escape($loan_system[0]['emi_amount']); ?>" readonly oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
				</div>

				<div class="col-lg-6 form-group">
					<label>EMI Date  <span class="text-danger">*</span></label>
					<input type="text" class="form-control datepicker" name="emi_date" value="<?php echo html_escape($loan_system[0]['emi_date']); ?>" required>
				</div>

				<div class="col-lg-6 form-group">
					<label>Notes</label>
					<input type="text" class="form-control" name="notes" value="<?php echo html_escape($loan_system[0]['notes']); ?>">
				</div>

				<input type="hidden" name="loan_id" value="<?php echo html_escape($loan_id); ?>">
				<input type="hidden" name="id" value="<?php echo html_escape($loan_system['0']['id']); ?>">
				<!-- csrf token -->
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">


				<div class="col-sm-12">
					<?php if (isset($page_title) && $page_title == "Edit") : ?>
						<button type="submit" class="btn btn-info btn-rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
					<?php else : ?>
						<button type="submit" class="btn btn-info btn-rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
					<?php endif; ?>
				</div>

			</form>
		</div>
		<?php if (isset($page_title) && $page_title != "Edit") : ?>
			<div class="list_area container">

				<?php if (isset($page_title) && $page_title == "Edit") : ?>
					<h3 class="box-title" style="font-size: 24px">Edit EMI <a href="<?php echo base_url('admin/banking/view_loan/' . $loan_id) ?>" class="pull-right btn btn-primary rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a></h3>
				<?php else : ?>
					<h3 class="box-title" style="font-size: 24px"><i class="flaticon-contract"></i>&nbsp;Add New EMI </h3>
					<div class="add-btn">
						<!--<a href="#" class="btn btn-info btn-sm rounded add_btn mb-5"><i class="fa fa-plus"></i> Add New EMI</a>-->
						<a href="<?php echo base_url('admin/banking/loan_system') ?>" class="btn btn-default rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
					</div>
				<?php endif; ?>

				<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-10">
					<div class="row report_info mb-2">
						<div class="col-md-6">
							<div><span class="font-weight-800">Total Amount :</span> <?php echo decimal_format($pr + $int, 2); ?></div>
							<div><span class="font-weight-800">Total Pending Amount : </span><?php if (!empty($pending_amt)) { echo $pending_amt->balance; } else { echo $pr + $int; } ?> </div>
						</div>
					</div>
					<table class="table table-bordered dshadow table-hover  <?php if (count($loan_details) > 10) {
																				echo "dt_btn1";
																			} ?>">
						<thead>
							<tr>
								<th>#</th>
								<th>EMI Date</th>
								<th>EMI Amount</th>
								<th>Principal Amount</th>
								<th>Intrest Amount</th>
								<th>Balance</th>
								<th>Status</th>
								<th class="noExport"><?php echo trans('action') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$i = 1;
							$pr = $int = 0;
							foreach ($loan_details as $loan) { ?>
								<tr id="row_<?php echo html_escape($loan->id); ?>">
									<td><?php echo $i; ?></td>
									<td><?php echo my_date_show($loan->emi_date); ?></td>
									<td><?php echo $this->business->currency_symbol . '' . decimal_format($loan->emi_amount, 2); ?></td>
									<td><?php echo $this->business->currency_symbol . '' . decimal_format($loan->principal_amt, 2); ?></td>
									<td><?php echo $this->business->currency_symbol . '' . decimal_format($loan->intrest_amt, 2); ?></td>
									<td><?php echo $this->business->currency_symbol . '' . decimal_format($loan->balance, 2); ?></td>
									<td>
										<?php if ($loan->status == 0) : ?>
											<span class="badge badge-warning">UnPaid</span>
										<?php else : ?>
											<span class="badge badge-success">Paid</span>
										<?php endif ?>
									</td>
									<td>
										<div class="toggle">
											<input type="checkbox" name="paid_status" value="1" data-toggle="toggle" data-onstyle="info" data-width="100" class="paid_or_unpaid" <?php if ($loan->status == 1) { echo 'checked'; } ?> data-id="<?php echo $loan->id; ?>" data-loan_id="<?php echo $loan->loan_id; ?>" data-emi_amount="<?php echo $loan->emi_amount; ?>" data-emi_date="<?php echo date('Y-m-d', strtotime($loan->emi_date)); ?>" <?php if ($loan->status == 1) { echo 'disabled'; } ?>>
										</div>
									</td>
								</tr>

							<?php 
								$pr += $loan->principal_amt;
								$int += $loan->intrest_amt;
								$i++;
							} ?>
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th><strong>Total: <?php echo decimal_format($pr, 2); ?></strong></th>
								<th><strong>Total: <?php echo decimal_format($int, 2); ?></strong></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>

			</div>
		<?php endif; ?>

	</section>
</div>

<div class="modal fade" id="statsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add EMI Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" enctype="multipart/form-data" class="row validate-form " action="<?php echo base_url('admin/banking/add_loan_emi') ?>" role="form" novalidate>
					<div class="col-lg-12 form-group">
						<label>Select Bank To Transfer Loan <span class="text-danger">*</span></label>
						<select class="form-control" name="bank_id" required>
							<option value=""><?php echo trans('select') ?></option>
							<?php foreach ($bankings as $banking) : ?>
								<?php if ($banking->account_type != "Credit Card") : ?>
									<option value="<?php echo html_escape($banking->id); ?>" <?php echo ($loan_system[0]['bank_id'] == $banking->id) ? 'selected' : ''; ?>>
										<?php if ($banking->account_type == "Bank") { ?>
											<i class="fa fa-bank"></i><?php echo html_escape($banking->account_type) . '-' . html_escape($banking->bank_name); ?>
										<?php } else { ?>
											<i class="fa fa-money"></i><?php echo html_escape($banking->account_type) . '-' . html_escape($banking->bank_name); ?>
										<?php } ?>
									</option>
								<?php endif; ?>
							<?php endforeach ?>
						</select>
					</div>

					<div class="col-lg-12 form-group">
						<label>EMI Amount <span class="text-danger">*</span></label>
						<input type="text" class="form-control" name="emi_amount" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" readonly value="<?php echo $loan->emi_amount; ?>" required>
					</div>

					<div class="col-lg-12 form-group">
						<label>EMI Date  <span class="text-danger">*</span></label>
						<input type="text" class="form-control datepicker" name="emi_date" value="" required>
					</div>

					<div class="col-lg-12 form-group">
						<label>Notes</label>
						<input type="text" class="form-control" name="notes" value="">
					</div>

					<input type="hidden" name="loan_id" value="<?php echo $loan->id; ?>">
					<input type="hidden" name="id" value="">
					<!-- csrf token -->
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">


					<div class="col-sm-12">
						<?php if (isset($page_title) && $page_title == "Edit") : ?>
							<button type="submit" class="btn btn-info btn-rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
						<?php else : ?>
							<button type="submit" class="btn btn-info btn-rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
						<?php endif; ?>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		$('.paid_or_unpaid').change(function() {
			if ($(this).prop("checked") == true) {
				var id = $(this).attr('data-id');
				var loan_id = $(this).attr('data-loan_id');
				var emi_amount = $(this).attr('data-emi_amount');
				var emi_date = $(this).attr('data-emi_date');
				$('input[name="id"]').val(id);
				$('input[name="loan_id"]').val(loan_id);
				$('input[name="emi_amount"]').val(emi_amount);
				$('input[name="emi_date"]').val(emi_date);
				$('#statsModal').modal('show');
			}
		});
	});
</script>