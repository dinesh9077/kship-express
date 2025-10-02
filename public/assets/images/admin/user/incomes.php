<style type="text/css">
	.verify_mail {
		background: #9e9e9e00;
		border-left: 4px solid #7b46d400;
		border: none !important;
	}

	@media only screen and (max-width: 600px) {
		.mb {
			margin-bottom: 8px !important;
		}
	}
</style>

<div class="content-wrapper">

	<!-- Main content -->
	<section class="content">

		<div class="col-md-12 m-auto box add_area mt-50" style="display: <?php if ($page_title == "Edit") {
																				echo "block";
																			} else {
																				echo "none";
																			} ?>">
			<div class="box-header bg-light1 d-flex with-border justify-content-between align-items-center f-no">
				<?php if (isset($page_title) && $page_title == "Edit") : ?>
					<h3 class="box-title" style="font-size: 24px"><i class="flaticon-contract"></i>&nbsp;Edit Income </h3>
				<?php else : ?>
					<h3 class="box-title" style="font-size: 24px"><i class="flaticon-contract"></i>&nbsp;Add New Income</h3>
				<?php endif; ?>
				<div class="box-tools pull-right">
					<?php if (isset($page_title) && $page_title == "Edit") : ?>
						<a href="<?php echo base_url('admin/income') ?>" class="btn btn-info btn-rounded pull-right"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
					<?php else : ?>
						<a href="#" class="btn btn-info btn-rounded pull-right cancel_btn"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
					<?php endif; ?>
				</div>
			</div>
			<form method="post" enctype="multipart/form-data" class="row validate-form mt-20 p-30" action="<?php echo base_url('admin/income/add') ?>" role="form" novalidate>

				<input type="hidden" class="form-control" required name="type_transaction" value="Income">
				<div class="col-lg-6 form-group">
					<label class="col-sm-12 control-label p-0" for="example-input-normal">Select Bank</label>
					<select class="form-control single_select" style="width:100%" name="bank_id" required>
						<option value=""><?php echo trans('select') ?></option>
  
						<?php foreach ($bankings as $banking): ?>
							<?php if ($banking->status == 1) { ?>
								<?php if ($banking->account_type != "Cash In Hand" && $banking->account_type != "Credit Card"): ?>
									 
									<option value="<?php echo html_escape($banking->id); ?>" <?php echo ($expense[0]['bank_id'] == $banking->id) ? 'selected' : ''; ?>>
										<?php echo html_escape($banking->account_type) . ' - ' . html_escape($banking->bank_name) . ' (' . $banking->account_number . ')'; ?>
									</option>
								 
								<?php endif; ?>
								<?php if ($banking->account_type == "Cash In Hand"): ?>
									 
									<option value="<?php echo html_escape($banking->id); ?>" <?php echo ($expense[0]['bank_id'] == $banking->id) ? 'selected' : ''; ?>>
										<?php echo html_escape($banking->account_type) . '- (' . $banking->account_name . ')'; ?>
									</option>
									 
								<?php endif; ?>
								<?php if ($banking->account_type == "Credit Card"): ?> 
									<option value="<?php echo html_escape($banking->id); ?>" <?php echo ($expense[0]['bank_id'] == $banking->id) ? 'selected' : ''; ?>>
										<?php echo html_escape($banking->account_type) . ' - ' . html_escape($banking->bank_name) . ' (' . $banking->account_number . ')'; ?>
									</option> 
								<?php endif; ?>
							<?php } ?>
						<?php endforeach ?>
					</select>
				</div>

				<div class="col-lg-6 form-group">
					<label>Income Amount  <span class="text-danger">*</span></label>
					<input type="number" class="form-control" required name="amount" value="<?php echo html_escape($expense[0]['amount']); ?>">
				</div>
				<div class="col-lg-6 form-group">
					<label class="col-sm-12 control-label p-0" for="example-input-normal">Income Category <span class="text-danger">*</span></label>
					<select class="form-control single_select" style="width:100%" name="category" id="income_category" required>
						<option value=""><?php echo trans('select') ?></option>
						<?php foreach ($income_category as $category) : ?>
							<option value="<?php echo html_escape($category->id); ?>" <?php echo ($expense[0]['category'] == $category->id) ? 'selected' : ''; ?>>
								<?php echo html_escape($category->name); ?>
							</option>
						<?php endforeach ?>
						<option value="add_income_category">+Add Income Category</option>
					</select>
				</div>

				<div class="col-lg-6 form-group">
					<label for="inputEmail3" class="col-sm-12 control-label p-0"><?php echo trans('date') ?>  <span class="text-danger">*</span></label>
					<div class="input-group">
						<input type="text" class="form-control datepicker" required placeholder="yyyy/mm/dd" name="date" value="<?php echo date('Y-m-d') ?>">
						<div class="input-group-append">
							<span class="input-group-text">
								<i class="fa fa-calender"></i>
							</span>
						</div>
					</div>
				</div>

				<div class="col-lg-6 form-group">
					<label><?php echo trans('notes') ?></label>
					<textarea class="form-control" name="notes"><?php echo html_escape($expense[0]['notes']); ?></textarea>
				</div>

				<div class="col-lg-6 form-group">
					<?php if (!empty($expense[0]['file'])) : ?>
						<p><label class="label label-info"><?php echo $expense[0]['file'] ?></label></p>
					<?php endif ?>
					<label><?php echo trans('upload') ?></label>
					<input class="form-control" type="file" name="file">
				</div>


				<input type="hidden" name="id" value="<?php echo html_escape($expense['0']['id']); ?>">
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
			<div class="list_area">
				<div class="verify_mail">
					<div class="box-body mb-5">
						<h3>
							<i class="fa fa-info-circle"></i> Now you can track your loan from banking menu with loan system module.
						</h3>
					</div>
				</div>



				<form method="GET" class="sort_invoice_form" action="<?php echo base_url('admin/income') ?>">
					<div class="row p-15 mt-20 mb-20" style="padding-right: 0 !important;">
						<div class="col-lg-12 p-0 ">
							<p class="mb-5"><a href="<?php echo base_url('admin/income') ?>" class="view_link border-btn">Clear Filter</a></p>
						</div>
						<div class="col-xl-3 mt-5 pl-0">
							<select class="form-control sort" name="category">
								<option value=""><?php echo trans('select') ?></option>
								<?php foreach ($income_category as $category) : ?>
									<option value="<?php echo html_escape($category->id); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $category->id) ? 'selected' : ''; ?>>
										<?php echo html_escape($category->name); ?>
									</option>
								<?php endforeach ?>
							</select>
						</div>
						<div class="col-xl-3 mt-5 pl-0">
							<div class="input-group">
								<input type="text" class="inv-dpick form-control datepicker" placeholder="<?php echo trans('from') ?>" name="start_date" value="<?php if (isset($_GET['start_date'])) {
																																									echo $_GET['start_date'];
																																								} ?>" autocomplete="off">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
						</div>
						<div class="col-xl-3 mt-5 pl-0">
							<div class="input-group">
								<input type="text" class="inv-dpick form-control datepicker" placeholder="<?php echo trans('to') ?>" name="end_date" value="<?php if (isset($_GET['end_date'])) {
																																								echo $_GET['end_date'];
																																							} ?>" autocomplete="off">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
						</div>
						<div class="col-xl-1 mt-5 pl-0">
							<button type="submit" class="btn btn-info btn-report btn-block custom_search"><i class="flaticon-magnifying-glass"></i></button>
						</div>
					</div>
				</form>

				<div class="col-xl-12 scroll table-responsive mt-10 p-0">
					<div class="card-box">
						<?php if (isset($page_title) && $page_title == "Edit") : ?>
							<h3 class="box-title" style="font-size: 24px">Edit Income <a href="<?php echo base_url('admin/expense') ?>" class="pull-right btn btn-primary rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a></h3>
						<?php else : ?>

							<div class="income_re d-flex bg-light1 justify-content-between align-items-center mt-4">
								<h3 class="box-title" style="font-size: 24px"><i class="flaticon-contract"></i>&nbsp;<?php echo trans('income') ?></h3>
								<div class="add-btn add_re">
									<a href="#" class="btn btn-info btn-sm rounded mb add_btn mb-5"><i class="fa fa-plus"></i> Add New Income</a>
									<a href="<?php echo base_url('admin/category') ?>" class="btn btn-default btn-sm rounded mr-10 mb-5"><i class="fa fa-folder-open"></i> <?php echo trans('add-new-category') ?></a>
								</div>
							</div>
						<?php endif; ?>
						<table class="table table-hover cushover <?php if (count($incomes) > 10) {
																		echo "datatable";
																	} ?>" id="dg_table">
							<thead>
								<tr>
									<th>#</th>
									<th><?php echo trans('date') ?></th>
									<th>Bank Name</th>
									<th><?php echo trans('amount') ?></th>
									<th><?php echo trans('category') ?></th>
									<th><?php echo trans('notes') ?></th> 
									<th><?php echo trans('action') ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$i = 1;
								foreach ($incomes as $expense) :
									if ($expense->category != 171 && $expense->category != 126) {
								?>
										<tr id="row_<?php echo html_escape($expense->id); ?>">
											<td><?php echo $i; ?></td>
											<td><?php echo my_date_show($expense->date); ?></td>
											<td>
												<?php 
													if($expense->account_type == "Cash In Hand")
													{ 
														echo html_escape($expense->account_type) . '- (' . $expense->account_name . ')';
													}
													else
													{ 
														echo html_escape($expense->account_type) . ' - ' . html_escape($expense->bank_name) . ' (' . $expense->account_number . ')';
													}
												?>
											</td>
											<td><?php echo $this->business->currency_symbol . '' . decimal_format($expense->net_amount, 2); ?></td>
											<td><?php echo html_escape($expense->category_name); ?></td>
											<td><?php echo html_escape($expense->notes); ?></td>

											<!-- <td><?php if (!empty($expense->file)) { ?><label class="label label-default"><?php echo html_escape($expense->file); ?></label><?php } ?></td> -->

											<td class="actions" width="15%">
												<a href="<?php echo base_url('admin/income/edit/' . html_escape($expense->id)); ?>" class="on-default edit-row" data-toggle="tooltip" data-placement="top" title="<?php echo trans('edit') ?>"><i class="fa fa-pencil"></i></a> &nbsp;

												<a data-val="expense" data-id="<?php echo html_escape($expense->id); ?>" href="<?php echo base_url('admin/income/delete/' . html_escape($expense->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a> &nbsp;
												<?php if (!empty($expense->file)) { ?>
													<a href="<?php echo base_url('admin/income/download/' . html_escape($expense->id)); ?>" class="on-default edit-row" data-placement="top" title="Download file"><i class="fa fa-download"></i></a>
												<?php } ?>
											</td>
										</tr>

								<?php $i++;
									}
								endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>

			</div>
		<?php endif; ?>

	</section>
</div>

<!-- product list modal -->
<div id="IncomeCatModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="vcenter" aria-hidden="true">
	<div class="modal-dialog modal-dialog-zoom modal-md">
		<form id="category-form" method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/Income/ajax_add_category') ?>" role="form" novalidate>
			<div class="modal-content modal-md">
				<div class="modal-header">
					<h4 class="modal-title" id="vcenter">Add Income Category</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body">
					<div class="form-group row">
						<label for="inputEmail3" class="col-sm-3 text-right control-label col-form-label">Category Name</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="name" id="name" required>
						</div>
					</div>
				</div>
				<input type="hidden" class="form-control" name="type" id="type" value="1" required>
				<div class="modal-footer">
					<!-- csrf token -->
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<button type="submit" class="btn btn-info rounded waves-effect pull-right submit_btn">Submit</button>
				</div>
			</div>
		</form>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		$('#income_category').change(function() {
			if ($(this).val() == "add_income_category") {
				$('#IncomeCatModal').modal('show');
			}
		})

	});

	$('#category-form').on('submit', (function(e) {
		e.preventDefault();
		var formData = new FormData(this);
		$('.submit_btn').prop('disabled', true);
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('admin/Income/ajax_add_category') ?>',
			data: formData,
			dataType: 'json',
			cache: false,
			contentType: false,
			processData: false,
			success: function(data) {
				$('.submit_btn').prop('disabled', false);
				$('#income_category').html(data.output);
				$('#IncomeCatModal').modal('hide');
			},
			error: function(data) {
				console.log("error");
			}
		});
	}));
</script>