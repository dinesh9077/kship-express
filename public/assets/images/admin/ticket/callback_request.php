<div class="content-wrapper">

	<!-- Main content -->
	<section class="content">

		<div class="list_area">

			
			<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0">
				<div class="card-box">
					<div class="bg-light1">
				<h3 class="box-title">CallBack Request</h3>
			</div>
					<table class="table table-hover cushover <?php if (count($callback_requests) > 10) {
																	echo "datatable";
																} ?>" id="dg_table">
						<thead>
							<tr>
								<th>#</th>
								<th><?php echo trans('name') ?></th>
								<th><?php echo trans('phone') ?></th>
								<th><?php echo trans('email') ?></th>
								<th>Content</th>
								<th>DateTime</th>
								<th><?php echo trans('status') ?></th>
								<th><?php echo trans('action') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1;
							foreach ($callback_requests as $row) : ?>
								<tr id="row_<?php echo html_escape($row->id); ?>">

									<td><?php echo $i; ?></td>

									<td><?php echo html_escape($row->name); ?></td>
									<td><?php echo html_escape($row->contact_no); ?></td>
									<td><?php echo html_escape($row->email); ?></td>
									<td><?php echo html_escape($row->desc); ?></td>
									<td><?php echo html_escape($row->call_time); ?></td>
									<?php if (is_admin()) {  ?>
										<td><span><input type="checkbox" data-id="<?php echo $row->id; ?>" id="close_request" <?php if ($row->status == 1) {
																																	echo 'checked';
																																} ?> data-toggle="toggle" data-onstyle="info" data-width="100"></span></td>
									<?php } else { ?>
										<?php if ($row->status == "1") { ?>
											<td><span class="badge badge-success">Active</span></td>
										<?php } else { ?>
											<td><span class="badge badge-danger">Close</span></td>
									<?php }
									} ?>
									<td class="actions" width="10%">

										<a data-val="Callback" data-id="<?php echo html_escape($row->id); ?>" href="<?php echo base_url('admin/ticket/delete_request/' . html_escape($row->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a>

									</td>
								</tr>

							<?php $i++;
							endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>
</div>