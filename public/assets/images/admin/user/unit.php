<div class="content-wrapper">

	<!-- Main content -->
	<section class="content">
		<div class="card-box">
			
			<div class="row">

				<div class="col-xl-3 col-lg-12">
					<div class="nav-tabs-custom profile_menu_web">
						<?php include "include/profile_menu.php"; ?>
					</div>
					<div class="nav-tabs-custom profile_menu_mobile">
						<?php include "include/profile_menu_1.php"; ?>
					</div>
				</div>

				<div class="col-xl-9">
					<div class="row">
						<?php if (isset($page_title) && $page_title != "Edit") : ?>
							<div class="col-lg-6 col-md-12">
								<div class="box1">
									<div class="box-header with-border">
										<?php if (isset($page_title) && $page_title == "Edit Type") : ?>
											<h3 class="box-title">Edit Units</h3>
										<?php else : ?>
											<h3 class="box-title">Add Units</h3>
										<?php endif; ?>

										<div class="box-tools pull-right">
											<?php if (isset($page_title) && $page_title == "Edit Type") : ?>
												<a href="<?php echo base_url('admin/unit') ?>" class="pull-right rounded btn btn-default btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
											<?php endif; ?>
										</div>
									</div>

									<div class="box-body">
										<form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/unit/add_unit') ?>" role="form" novalidate>

											<div class="form-group">
												<label>Unit <?php echo trans('name') ?> <span class="text-danger">*</span></label>
												<input type="text" class="form-control" required name="unit" value="<?php echo html_escape($unit[0]['unit']); ?>">
											</div>


											<input type="hidden" name="id" value="<?php echo html_escape($unit['0']['id']); ?>">

											<!-- csrf token -->
											<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

											<div class="row m-t-30">
												<div class="col-sm-12">
													<?php if (isset($page_title) && $page_title == "Edit") : ?>
														<button type="submit" class="btn btn-info rounded pull-left"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
													<?php else : ?>
														<button type="submit" class="btn btn-info rounded pull-left"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
													<?php endif; ?>
												</div>
											</div>

										</form>

										<br>

										<?php if (isset($page_title) && $page_title != "Edit Type") : ?>
											<div class="table-responsive">
												<table class="table <?php if (count($units) > 10) : echo 'datatable';
												endif ?> table-hover">
												<thead>
													<tr>
														<th>#</th>
														<th>Unit</th>
														<th><?php echo trans('action') ?></th>
													</tr>
												</thead>
												<tbody>
													<?php $i = 1;
													foreach ($units as $unit) : ?>
														<tr id="row_<?php echo html_escape($unit->id); ?>">

															<td><?php echo $i; ?></td>
															<td><?php echo html_escape($unit->unit); ?></td>

															<td class="actions" width="25%">
																<?php if ($unit->user_id != 0) : ?>
																	<a href="<?php echo base_url('admin/unit/edit_unit/' . html_escape($unit->id)); ?>" class="on-default edit-row" data-placement="top" title="Edit"><i class="fa fa-pencil"></i></a> &nbsp;

																	<a data-val="<?php echo trans('tax') ?>" data-id="<?php echo html_escape($unit->id); ?>" href="<?php echo base_url('admin/unit/delete_unit/' . html_escape($unit->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a>
																<?php endif ?>
															</td>
														</tr>

														<?php $i++;
													endforeach; ?>
												</tbody>
											</table>
										</div>
									<?php endif; ?>

								</div>
							</div>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</div>
</section>
</div>