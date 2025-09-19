<style>
	.my-cutm-box {
		border-right: 1px solid #DFDFDF;
	}
</style>

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
							<div class="col-lg-5 my-cutm-box">
								<div class="my-box">
									<div class=" d-flex justify-content-between align-items-center f-no">
										<?php if (isset($page_title) && $page_title == "Edit Type") : ?>
											<h3><?php echo trans('edit-gst') ?></h3>
										<?php else : ?>
											<h3>Add Tax Type</h3>
										<?php endif; ?>

										<div class="box-tools pull-right">
											<?php if (isset($page_title) && $page_title == "Edit Type") : ?>
												<a href="<?php echo base_url('admin/tax') ?>" class="pull-right btn btn-info rounded btn-sm add_btn"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
											<?php endif; ?>
										</div>
									</div>

									<div class="box-body">
										<form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/tax/add_type') ?>" role="form" novalidate>

											<div class="form-group">
												<label><?php echo trans('name') ?> <span class="text-danger">*</span></label>
												<input type="text" class="form-control" required name="name" value="<?php echo html_escape($type[0]['name']); ?>">
											</div>


											<input type="hidden" name="id" value="<?php echo html_escape($type['0']['id']); ?>">

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
												<table class="table table-hover">
													<thead>
														<tr>
															<th>#</th>
															<th><?php echo trans('name') ?></th>
															<?php if ($type->user_id != 0) : ?>
																<th><?php echo trans('action') ?></th>
															<?php endif ?>
														</tr>
													</thead>
													<tbody>
														<?php $i = 1;
														foreach ($types as $type) : ?>
															<tr id="row_<?php echo html_escape($type->id); ?>">

																<td><?php echo $i; ?></td>
																<td><?php echo html_escape($type->name); ?></td>

																<?php if ($type->user_id != 0) : ?>
																	<td class="actions" width="25%">
																		<a href="<?php echo base_url('admin/tax/edit_type/' . html_escape($type->id)); ?>" class="on-default edit-row" data-placement="top" title="Edit"><i class="fa fa-pencil"></i></a> &nbsp;

																		<a data-val="<?php echo trans('tax') ?>" data-id="<?php echo html_escape($type->id); ?>" href="<?php echo base_url('admin/tax/delete_type/' . html_escape($type->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a>
																	</td>
																<?php endif ?>
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

						<?php if (isset($page_title) && $page_title != "Edit Type") : ?>
							<div class="col-lg-7">

								<div class="box add_area" style="display: <?php if ($page_title == "Edit") {
																				echo "block";
																			} else {
																				echo "none";
																			} ?>">
									<div class="d-flex justify-content-between align-items-center f-no">
										<?php if (isset($page_title) && $page_title == "Edit") : ?>
											<h3><?php echo trans('edit-tax') ?></h3>
										<?php else : ?>
											<h3><?php echo trans('add-new-tax') ?> </h3>
										<?php endif; ?>

										<div class="box-tools pull-right">
											<?php if (isset($page_title) && $page_title == "Edit") : ?>
												<a href="<?php echo base_url('admin/tax') ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
											<?php else : ?>
												<a href="#" class="pull-right btn btn-info rounded btn-sm cancel_btn"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
											<?php endif; ?>
										</div>
									</div>

									<div class="box-body">
										<form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/tax/add') ?>" role="form" novalidate>

											<div class="form-group">
												<label class="col-sm-2 control-label p-0" for="example-input-normal"><?php echo trans('tax') ?> <span class="text-danger">*</span></label>
												<select class="form-control single_select" style="width: 100%" name="type" required>
													<option value=""><?php echo trans('select') ?></option>
													<?php foreach ($types as $type) : ?>
														<option value="<?php echo html_escape($type->id); ?>" <?php echo ($tax[0]['type'] == $type->id) ? 'selected' : ''; ?>>
															<?php echo html_escape($type->name); ?>
														</option>
													<?php endforeach ?>
												</select>
											</div>

											<div class="form-group">
												<label><?php echo trans('tax-name') ?> <span class="text-danger">*</span></label>
												<input type="text" class="form-control" required name="name" value="<?php echo html_escape($tax[0]['name']); ?>">
											</div>

											<div class="form-group">
												<label><?php echo trans('tax-rate') ?> (%)<span class="text-danger">*</span></label>
												<input type="text" class="form-control" required name="rate" value="<?php echo html_escape($tax[0]['rate']); ?>">
												<p><?php echo trans('tax-rate-info') ?></p>
											</div>

											<div class="form-group">
												<label><?php echo trans('tax-number-id') ?></label>
												<input type="text" class="form-control" name="number" value="<?php echo html_escape($tax[0]['number']); ?>">
											</div>

											<div class="form-group">
												<label><?php echo trans('details') ?></label>
												<textarea class="form-control" name="details" rows="6"><?php echo html_escape($tax[0]['details']); ?></textarea>
											</div>

											<div class="form-group m-t-30">
												<input type="checkbox" id="md_checkbox_1" class="filled-in chk-col-blue" value="1" name="is_invoices" <?php if ($page_title != "Edit") {
																																							echo "checked";
																																						} ?> <?php if ($tax[0]['is_invoices'] == 1) {
																																									echo "checked";
																																								} ?>>
												<label for="md_checkbox_1"> <?php echo trans('show-the-tax-number-on-invoices') ?></label>
											</div>

											<input type="hidden" name="id" value="<?php echo html_escape($tax['0']['id']); ?>">
											<!-- csrf token -->
											<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

											<hr>

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

									</div>
								</div>

								<?php if (isset($page_title) && $page_title != "Edit") : ?>
									<div class="list_area container" style="background: #fff;border-radius: 8px;padding: 12px;">
										<div class="d-flex justify-content-between align-items-center f-no">
											<?php if (isset($page_title) && $page_title == "Edit") : ?>
												<h3><?php echo trans('edit-tax') ?> </h3>
												<a href="<?php echo base_url('admin/portfolio_tax') ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
											<?php else : ?>
												<h3><?php echo trans('all-tax') ?> </h3>
												<a href="#" class="pull-right btn btn-info rounded btn-sm add_btn"><i class="fa fa-plus"></i> <?php echo trans('add-new-tax') ?></a>
											<?php endif; ?>
										</div>

										<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0" style="overflow: auto;">
											<table class="table table-hover  cushover <?php if (count($taxes) > 10) {
																							echo "datatable";
																						} ?>" id="dg_table">
												<thead>
													<tr>
														<th>#</th>
														<th><?php echo trans('type') ?></th>
														<th><?php echo trans('name') ?></th>
														<th><?php echo trans('rate') ?></th>
														<th><?php echo trans('action') ?></th>
													</tr>
												</thead>
												<tbody>
													<?php $i = 1;
													foreach ($taxes as $tax) : ?>
														<tr id="row_<?php echo html_escape($tax->id); ?>">

															<td><?php echo $i; ?></td>
															<td><?php echo html_escape($tax->type_name); ?></td>
															<td><?php echo html_escape($tax->name); ?></td>
															<td><?php echo html_escape(floor($tax->rate * pow(10, 3)) / pow(10, 3)); ?> %</td>
															<td class="actions" width="15%">
																<?php if ($tax->user_id != 0) : ?>
																	<a href="<?php echo base_url('admin/tax/edit/' . html_escape($tax->id)); ?>" class="on-default edit-row" data-placement="top" title="Edit"><i class="fa fa-pencil"></i></a> &nbsp;

																	<a data-val="<?php echo trans('tax') ?>" data-id="<?php echo html_escape($tax->id); ?>" href="<?php echo base_url('admin/tax/delete/' . html_escape($tax->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a>
																<?php endif ?>
															</td>
														</tr>

													<?php $i++;
													endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
								<?php endif; ?>

							</div>
						<?php endif; ?>
					</div>
				</div>







			</div>
		</div>

	</section>
</div>