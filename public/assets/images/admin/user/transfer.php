<style>
	.bank-category {
	font-weight: bold;
	}
</style>
<div class="content-wrapper">
	
	<!-- Main content -->
	<section class="content">
		
		<div class="col-md-10 m-auto box add_area" style="display: <?php if ($page_title == "Edit") {
			echo "block";
			} else {
			echo "none";
		} ?>">
		<div class="box-header with-border">
			<?php if (isset($page_title) && $page_title == "Edit") : ?>
			<h3 class="box-title">Edit Transfer Detail</h3>
			<?php else : ?>
			<h3 class="box-title"><i class="fa fa-exchange" aria-hidden="true"></i>&nbsp;Add New Transfer </h3>
			<?php endif; ?>
			
			<div class="box-tools pull-right">
				<?php if (isset($page_title) && $page_title == "Edit") : ?>
				<a href="<?php echo base_url('admin/banking/transfer') ?>" class="pull-right rounded btn btn-default btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
				<?php else : ?>
				<a href="#" class="text-right rounded btn btn-default btn-sm cancel_btn"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
				<?php endif; ?>
			</div>
		</div>
		
		<div class="box-body">
			<form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form row" action="<?php echo base_url('admin/banking/addTransfer') ?>" role="form" novalidate>
				<div class="col-lg-6 form-group">
					<label class="col-sm-4 control-label p-0" for="example-input-normal">From  <span class="text-danger">*</span></label>
					<select class="form-control single_select" style="width:100%" name="from_bank_id" id="from_bank_id" required onchange="from_bank(this);">
						<option value=""><?php echo trans('select') ?></option>
						<?php foreach ($new_bankdetails as $type => $banks) { ?>
							<optgroup label="<?php echo ucfirst(str_replace('_', ' ', $type)); ?>">
								<?php foreach ($banks as $banking) { ?>
									<option value="<?php echo html_escape($banking->id); ?>" <?php echo ($transfer->from_bank_id == $banking->id) ? 'selected' : ''; ?>>
										<?php echo html_escape($banking->account_type) . ' - ' . html_escape($banking->bank_name) . ' (' . $banking->account_number . ')'; ?>
									</option>
								<?php } ?>
							</optgroup>
						<?php } ?>
					</select>
					<span id="show_from_bank"></span>
				</div>
				<div class="col-lg-6 form-group">
					<label class="col-sm-4 control-label p-0" for="example-input-normal">To  <span class="text-danger">*</span></label>
					<select class="form-control single_select" style="width:100%" name="to_bank_id" id="to_bank_id" required onchange="to_bank(this);">
						<option value=""><?php echo trans('select') ?></option>
						<?php foreach ($new_bankdetails as $type => $banks) { ?>
							<optgroup label="<?php echo ucfirst(str_replace('_', ' ', $type)); ?>">
								<?php foreach ($banks as $banking) { ?>
									<option value="<?php echo html_escape($banking->id); ?>" <?php echo ($transfer->to_bank_id == $banking->id) ? 'selected' : ''; ?>>
										<?php echo html_escape($banking->account_type) . ' - ' . html_escape($banking->bank_name) . ' (' . $banking->account_number . ')'; ?>
									</option>
								<?php } ?>
							</optgroup>
						<?php } ?>
					</select>
					<span id="show_to_bank"></span>
				</div>
				<div class="col-lg-6 form-group">
					<label>Transaction Amount</label>
					<input type="text" class="form-control" name="transaction_amount" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" value="<?php echo $transfer->transaction_amount; ?>" required>
				</div>
				<div class="col-lg-6 form-group">
					<label>Note</label>
					<textarea type="text" class="form-control" name="note"><?php echo $transfer->note; ?></textarea>
				</div>
				
				<input type="hidden" name="id" value="<?php echo $transfer->id; ?>">
				<!-- csrf token -->
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
				
				<hr>
				
				<div class="col-sm-12">
					<?php if (isset($page_title) && $page_title == "Edit") : ?>
					<button type="submit" class="btn btn-info rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
					<?php else : ?>
					<button type="submit" class="btn btn-info rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
					<?php endif; ?>
				</div>
				
			</form>
		</div>
		
		<div class="box-footer">
			
		</div>
		</div>
		
		
		<?php if (isset($page_title) && $page_title != "Edit") : ?>
		
		<div class="list_area container">
			
			<?php if (isset($page_title) && $page_title == "Edit") : ?>
			<h3 class="box-title">Edit Transfer Detail <a href="<?php echo base_url('admin/banking/transfer') ?>" class="pull-right btn btn-primary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a></h3>
			<?php else : ?>
			
			<div class="row justify-content-between align-items-center">
				<h3 class="box-title"><i class="fa fa-exchange" aria-hidden="true"></i>&nbsp;All Transfer List </h3>
				<div class="add-btn"><a href="#" class="btn btn-info rounded btn-sm add_btn"><i class="fa fa-plus"></i> Add Transfer </a></div>
			</div>
			<?php endif; ?>
			
			<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0">
				<table class="table table-hover cushover <?php if (count($customers) > 10) {
					echo "datatable";
				} ?>">
				<thead>
					<tr>
						<th>#</th>
						<th>From</th>
						<th>To</th>
						<th class="text-center">Amount</th>
						<th>Note</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1;
					foreach ($bank_transfer as $bank_transferd) : ?>
					<tr id="row_<?php echo html_escape($bank_transferd->id); ?>">
						<td><?php echo $i; ?></td>
						<td style="text-transform: capitalize"><?php echo html_escape($bank_transferd->from_bank_name); ?></td>
						<td><?php echo html_escape($bank_transferd->to_bank_name); ?></td>
						<td class="text-center"><?php echo $this->business->currency_symbol . '' . decimal_format($bank_transferd->transaction_amount, 2); ?></td>
						<td><?php echo html_escape($bank_transferd->note); ?></td>
						
						<td class="actions" width="12%">
							<a href="<?php echo base_url('admin/banking/transferEdit/' . html_escape($bank_transferd->id)); ?>" class="on-default remove-row" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-pencil"></i></a>
							&nbsp;
							<a data-val="<?php echo trans('customer') ?>" data-id="<?php echo html_escape($bank_transferd->id); ?>" href="<?php echo base_url('admin/banking/transferDelete/' . html_escape($bank_transferd->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a> &nbsp;
						</td>
					</tr>
					<?php $i++;
					endforeach; ?>
				</tbody>
				</table>
			</div>
			
		</div>
		<?php endif; ?>
		
	</section>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
	function from_bank(obj) {
		var bank_id = $(obj).val();
		$.post("<?php echo base_url('admin/banking/get_total_amount_by_bank_id'); ?>", {
			bank_id: bank_id
			}, function(data, status) {
			if (data > 0) {
				$('#show_from_bank').html('Total Balance: ' + data);
				$('#show_from_bank').css('color', 'green');
				} else {
				$('#show_from_bank').html('Total Balance: ' + data);
				$('#show_from_bank').css('color', 'red');
			}
		});
	}
	
	function to_bank(obj) {
		var bank_id = $(obj).val();
		$.post("<?php echo base_url('admin/banking/get_total_amount_by_bank_id'); ?>", {
			bank_id: bank_id
			}, function(data, status) {
			if (data > 0) {
				$('#show_to_bank').html('Total Balance: ' + data);
				$('#show_to_bank').css('color', 'green');
				} else {
				$('#show_to_bank').html('Total Balance: ' + data);
				$('#show_to_bank').css('color', 'red');
			}
		});
	}
</script>