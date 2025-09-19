<style>
	/* Hide prev/next buttons and month name*/
	/* .datepicker-days>table>thead>tr>th.prev,
    .datepicker-days>table>thead>tr>th.datepicker-switch,
    .datepicker-days>table>thead>tr>th.next {
      display: none;
    } */

    /* Hide days of previous month */
	/* td.old.day{
      visibility: hidden;
    } */

    /* Hide days of next month */
	/* td.new.day{
      display: none;
    } */
  </style>
  <div class="content-wrapper">

  	<!-- Main content -->
  	<section class="content">
  		<div class="col-md-12 m-auto box add_area" style="display: <?php if ($page_title == "Edit") { echo "block"; } else { echo "none"; } ?>">
  		<div class="d-flex align-items-center justify-content-between bg-light1 f-no">
  			<?php if (isset($page_title) && $page_title == "Edit") : ?>
  				<h3 class="box-title" style="font-size: 24px"><i class="flaticon-contract"></i>&nbsp;Edit New Loan</h3>
  			<?php else : ?>
  				<h3 class="box-title" style="font-size: 24px"><i class="flaticon-contract"></i>&nbsp;Add New Loan</h3>
  			<?php endif; ?>

  			<div class="box-tools pull-right">
  				<?php if (isset($page_title) && $page_title == "Edit") : ?>
  					<a href="<?php echo base_url('admin/banking/loan_system') ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
  				<?php else : ?>
  					<a href="#" class="pull-right btn btn-info rounded btn-sm cancel_btn"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
  				<?php endif; ?>
  			</div>
  		</div>

  		<form method="post" enctype="multipart/form-data" id="loan_form" class="row validate-form mt-20 p-30" action="<?php echo base_url('admin/banking/add_loan_sys') ?>" role="form" novalidate>

  			<div class="col-lg-6 form-group">
  				<label>Loan Taken Bank Name<span class="text-danger">*</span></label>
  				<input type="text" class="form-control" required name="loan_taken_bank" value="<?php echo html_escape($loan_system[0]['loan_taken_bank']); ?>" required>
  			</div>
  			<div class="col-lg-6 form-group">
  				<label class="col-sm-12 control-label p-0" for="example-input-normal">Select Bank To Transfer Loan<span class="text-danger">*</span></label>
  				<select class="form-control single_select" style="width:100%" name="bank_id" required>
  					<option value=""><?php echo trans('select') ?></option>
  					<?php foreach ($bankings as $banking) : ?>
						<?php if ($banking->status == 1) { ?>
						<option value="<?php echo html_escape($banking->id); ?>" <?php echo ($loan_system[0]['bank_id'] == $banking->id) ? 'selected' : ''; ?>>
							<?php if ($banking->account_type == "Bank") { ?>
								<i class="fa fa-bank"></i><?php echo html_escape($banking->account_type) . '-' . html_escape($banking->bank_name) . ' (' . $banking->account_number . ')'; ?>
							<?php } else { ?>
								<i class="fa fa-money"></i><?php echo html_escape($banking->account_type) . '- (' . $banking->account_name . ')'; ?>
							<?php } ?>
						</option>
						<?php } ?>
						<!-- <?php if ($banking->account_type == "Bank") : ?>
  							<optgroup label="Bank">
  								<option value="<?php echo html_escape($banking->id); ?>" <?php echo ($loan_system[0]['bank_id'] == $banking->id) ? 'selected' : ''; ?>>
  									<?php if ($banking->account_type == "Bank") { ?>
  										<i class="fa fa-bank"></i><?php echo html_escape($banking->account_type) . '-' . html_escape($banking->bank_name) . ' (' . $banking->account_number . ')'; ?>
  									<?php } else { ?>
  										<i class="fa fa-money"></i><?php echo html_escape($banking->account_type) . '-' . html_escape($banking->bank_name) . ' (' . $banking->account_number . ')'; ?>
  									<?php } ?>
  								</option>
  							</optgroup>
  						<?php endif; ?>
  						<?php if ($banking->account_type == "Cash In Hand") : ?>
  							<optgroup label="Cash In Hand">
  								<option value="<?php echo html_escape($banking->id); ?>" <?php echo ($loan_system[0]['bank_id'] == $banking->id) ? 'selected' : ''; ?>>
  									<?php if ($banking->account_type == "Bank") { ?>
  										<i class="fa fa-bank"></i><?php echo html_escape($banking->account_type) . '-' . html_escape($banking->bank_name) . ' (' . $banking->account_number . ')'; ?>
  									<?php } else { ?>
  										<i class="fa fa-money"></i><?php echo html_escape($banking->account_type) . '-' . html_escape($banking->bank_name) . ' (' . $banking->account_number . ')'; ?>
  									<?php } ?>
  								</option>
  							</optgroup>
  						<?php endif; ?> -->
  					<?php endforeach ?>
  				</select>
  			</div>
  			<div class="col-lg-6 form-group">
  				<label>Loan Type<span class="text-danger">*</span></label>
  				<select class="form-control single_select" style="width:100%" name="loan_type" id="loan_type" required>
  					<option value="">Select Loan Type</option>
  					<option value="Home Loan" <?php echo ($loan_system[0]['loan_type'] == 'Home Loan') ? 'selected' : ''; ?>>Home Loan</option>
  					<option value="Personal Loan" <?php echo ($loan_system[0]['loan_type'] == 'Personal Loan') ? 'selected' : ''; ?>>Personal Loan</option>
  					<option value="Car Loan" <?php echo ($loan_system[0]['loan_type'] == 'Car Loan') ? 'selected' : ''; ?>>Car Loan</option>
  					<option value="Business Loan" <?php echo ($loan_system[0]['loan_type'] == 'Business Loan') ? 'selected' : ''; ?>>Business Loan</option>
  					<option value="Gold Loan" <?php echo ($loan_system[0]['loan_type'] == 'Home Loan') ? 'selected' : ''; ?>>Gold Loan</option>
  					<option value="Rental Deposit Loan" <?php echo ($loan_system[0]['loan_type'] == 'Rental Deposit Loan') ? 'selected' : ''; ?>>Rental Deposit Loan</option>
  					<option value="Vehicle Loan" <?php echo ($loan_system[0]['loan_type'] == 'Vehicle Loan') ? 'selected' : ''; ?>>Vehicle Loan</option>
  					<option value="Education Loan" <?php echo ($loan_system[0]['loan_type'] == 'Education Loan') ? 'selected' : ''; ?>>Education Loan</option>
  				</select>
  			</div>
  			<div class="col-lg-6 form-group">
  				<label>Intrest Type<span class="text-danger">*</span></label>
  				<select class="form-control single_select" style="width:100%" name="intrest_type" id="intrest_type" required>
  					<option value="">Select Intrest Type</option>
  					<option value="1" <?php echo ($loan_system[0]['intrest_type'] == 1) ? 'selected' : ''; ?>>Fixed/Flat-Rate Intrest</option>
  					<option value="2" <?php echo ($loan_system[0]['intrest_type'] == 2) ? 'selected' : ''; ?>>Reducing-Balance Intrest</option>
  				</select>
  			</div>
  			<?php if (isset($page_title) && $page_title == "Edit") : ?>
  				<div class="col-lg-6 form-group">
  					<label>Loan Amount<span class="text-danger">*</span></label>
  					<input type="text" class="form-control" name="loan_amount" id="loan_amount" value="<?php echo html_escape($loan_system[0]['loan_amount']); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
  				</div>
  			<?php else : ?>
  				<div class="col-lg-6 form-group">
  					<label>Loan Amount<span class="text-danger">*</span></label>
  					<input type="text" class="form-control" name="loan_amount" id="loan_amount" value="<?php echo html_escape($loan_system[0]['loan_amount']); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
  				</div>
  			<?php endif ?>
  			<div class="col-lg-6 form-group">
  				<label>Rate of Interest <span class="text-danger">*</span></label>
  				<input type="text" class="form-control" name="roi" id="roi" value="<?php echo html_escape($loan_system[0]['roi']); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
  			</div>

  			<div class="col-lg-6 form-group">
  				<label>EMI Tenure (In Month) <span class="text-danger">*</span></label>
  				<input type="text" class="form-control" name="emi_tenure" id="emi_tenure" value="<?php echo html_escape($loan_system[0]['emi_tenure']); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
  			</div>

  			<div class="col-lg-6 form-group">
  				<label>EMI Amount <span class="text-danger">*</span></label>
  				<input type="text" class="form-control" name="emi_amount" id="emi_amount" value="<?php echo html_escape($loan_system[0]['emi_amount']); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
  			</div>

  			<div class="col-lg-6 form-group">
  				<label>Processing Fee</label>
  				<input type="text" class="form-control" id="process_fee" name="process_fee" value="<?php echo html_escape($loan_system[0]['process_fee']); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
				<span class="error_process_fee extra_amount"></span>
  			</div>

  			<div class="col-lg-6 form-group">
  				<label>File Charge</label>
  				<input type="text" class="form-control" id="file_charge" name="file_charge" value="<?php echo html_escape($loan_system[0]['file_charge']); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
				<span class="error_file_charge extra_amount"></span>
  			</div>

  			<div class="col-lg-6 form-group">
  				<label>Any Other Charge</label>
  				<input type="text" class="form-control" id="any_other_charge" name="any_other_charge" value="<?php echo html_escape($loan_system[0]['any_other_charge']); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
				<span class="error_any_other_charge extra_amount"></span>
  			</div>
  			<div class="col-lg-6 form-group">
  				<label>Total Amount To Transfer Bank <span class="text-danger">*</span> </label>
  				<input type="text" class="form-control" id="transfer_bank_amt" name="transfer_bank_amt" value="<?php echo html_escape($loan_system[0]['transfer_bank_amt']); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
  			</div>

  			<div class="col-lg-6 form-group">
  				<label for="inputEmail3" class="col-sm-12 control-label p-0">Loan Issue Date<span class="text-danger">*</span></label>
  				<div class="input-group">
  					<input type="text" class="form-control edit_emi_date" required placeholder="yyyy/mm/dd" name="loan_issue_date" value="<?php echo date('Y-m-d') ?>">
  					<div class="input-group-append">
  						<span class="input-group-text">
  							<i class="fa fa-calender"></i>
  						</span>
  					</div>
  				</div>
  			</div>

  			<div class="col-lg-6 form-group">
  				<label>EMI Date <span class="text-danger">*</span></label>
  				<input type="text" class="form-control edit_emi_date" name="emi_date" value="<?php echo html_escape($loan_system[0]['emi_date']); ?>" required>
  			</div>

  			<div class="col-lg-6 form-group">
  				<?php if (!empty($loan_system[0]['file'])) : ?>
  					<p><label class="label label-info"><?php echo $loan_system[0]['file'] ?></label></p>
  				<?php endif ?>
  				<label>Upload Any Other Document</label>
  				<input class="form-control" type="file" name="files[]" multiple>
  			</div>

  			<input type="hidden" name="id" value="<?php echo html_escape($loan_system['0']['id']); ?>">
  			<!-- csrf token -->
  			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

  			<div class="col-sm-12">
  				<?php if (isset($page_title) && $page_title == "Edit") : ?>
  					<button type="submit" class="loan_btn btn btn-info btn-rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
  				<?php else : ?>
  					<button type="submit" class="loan_btn btn btn-info btn-rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
  				<?php endif; ?>
  			</div>

  		</form>
  	</div>

  	<?php if (isset($page_title) && $page_title != "Edit") : ?>

  		<div class="list_area">
  			<div class="card-box">
  				<div class="d-flex align-items-center justify-content-between bg-light1 f-no">
  				<?php if (isset($page_title) && $page_title == "Edit") : ?>
  					<h3 class="box-title" style="font-size: 24px">Edit Income </h3>
  					<a href="<?php echo base_url('admin/banking/loan_system') ?>" class="pull-right btn btn-primary rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
  				<?php else : ?>
  					<div class="d-flex justify-content-between align-items-center bg-light1 loan_re">
  						<h3 class="box-title" style="font-size: 24px"><i class="flaticon-contract"></i>&nbsp;Add New Loan </h3>
  						<div class="add-btn add_new">
  							<a href="#" class="btn btn-info btn-sm rounded add_btn mb-5"><i class="fa fa-plus"></i> Add New Loan</a>
  						</div>
  					</div>
  				<?php endif; ?>
  				</div>

  				<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-10 p-0 over_scroll">

  					<table class="table table-hover cushover <?php if (count($loan_stystems) > 10) { echo "datatable"; } ?>" id="dg_table">
  					<thead>
  						<tr>
  							<th>#</th>
  							<th>Loan Taken Bank</th>
  							<th>Transfer Bank</th>
  							<th class="text-right">Loan Amount</th>
  							<th class="text-right">EMI Amount</th>
  							<th class="text-right">Paid Amount</th>
  							<th class="text-right">Remaining Amount</th>
  							<th>EMI Tenure</th>
  							<th>EMI Date</th>
  							<th><?php echo trans('action') ?></th>
  						</tr>
  					</thead>
  					<tbody>
  						<?php
  						$i = 1;
  						foreach ($loan_stystems as $loan) {
  							$expense = $this->db->where('loan_id', $loan->id)->where('type_transaction', 'Loan')->get('expenses')->result();
  							$paid_amt = count($expense) * $loan->emi_amount;

  							$pending_amt = $this->db->where('loan_id', $loan->id)->where('status', 1)->order_by('id', 'desc')->get('loan_details')->row();

  							if (!empty($pending_amt)) {
  								$remain_amt = $pending_amt->balance;
  							} else {
  								$remain_amt = $loan->loan_amount + $loan_total->loan;
  							}
									// $remain_amt = $loan->emi_tenure * $loan->emi_amount - $paid_amt;
  							?>
  							<tr id="row_<?php echo html_escape($loan->id); ?>">
  								<td><?php echo $i; ?></td>
  								<td><?php echo html_escape($loan->loan_taken_bank); ?></td>
  								<td><?php echo html_escape($loan->bank_name); ?></td>
  								<td class="text-right"><?php echo $this->business->currency_symbol . '' . decimal_format($loan->loan_amount, 2); ?></td>
  								<td class="text-right"><?php echo $this->business->currency_symbol . '' . decimal_format($loan->emi_amount, 2); ?></td>
  								<td class="text-right"><?php echo $this->business->currency_symbol . '' . decimal_format($paid_amt, 2); ?></td>
  								<td class="text-right"><?php echo $this->business->currency_symbol . '' . decimal_format($remain_amt, 2); ?></td>
  								<td><?php echo count($expense); ?> / <?php echo html_escape($loan->emi_tenure); ?></td>
  								<td><?php echo my_date_show($loan->emi_date); ?></td>

  								<td class="actions" width="15%">
  									<?php if (count($expense) == 0) : ?>
  										<a href="<?php echo base_url('admin/banking/edit_loan/' . html_escape($loan->id)); ?>" class="on-default edit-row" data-placement="top" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a> &nbsp;
  									<?php endif ?>
  									<a data-val="loan_system" href="<?php echo base_url('admin/banking/view_loan/' . html_escape($loan->id)); ?>" class="on-default" data-placement="top" data-toggle="tooltip" title="View"><i class="fa fa-eye"></i></a> &nbsp;
									  
  									<?php if (count($expense) == 0) : ?>
										  <a data-val="loan_system" data-id="<?php echo html_escape($loan->id); ?>" href="<?php echo base_url('admin/banking/delete_loan/' . html_escape($loan->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a> &nbsp;
  									<?php endif ?>
  									<?php if (!empty($loan->file)) { ?>
  										<a href="<?php echo base_url(html_escape($loan->file)); ?>" class="on-default edit-row" data-toggle="tooltip" data-placement="top" title="Download file" download><i class="fa fa-download"></i></a>
  									<?php } ?>
  								</td>
  							</tr>

  							<?php $i++;
  						} ?>
  					</tbody>
  				</table>
  			</div>
  		</div>

  	</div>
  <?php endif; ?>

</section>
</div>

<?php foreach ($loan_stystems as $loan) : ?>
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Add EMI</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form method="post" enctype="multipart/form-data" class="row validate-form " action="<?php echo base_url('admin/banking/add_loan_emi') ?>" role="form" novalidate>
						<div class="col-lg-12 form-group">
							<label>Select Bank To Transfer Loan<span class="text-danger">*</span></label>
							<select class="form-control single_select" style="width:100%" name="bank_id" required>
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
							<label>EMI Amount<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="emi_amount" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" readonly value="<?php echo $loan->emi_amount; ?>" required>
						</div>

						<div class="col-lg-12 form-group">
							<label>EMI Date <span class="text-danger">*</span></label>
							<input type="text" class="form-control edit_emi_date" name="emi_date" value="" required>
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
<?php endforeach ?>
