<div class="content-wrapper">

	<!-- Main content -->
	<section class="content">

		<div class="card-box">
			<div class="row bus_area">
				<div class="col-lg-3">
					<div class="nav-tabs-custom profile_menu_web">
						<?php include "include/profile_menu.php"; ?>
					</div>
					<div class="nav-tabs-custom profile_menu_mobile">
						<?php include "include/profile_menu_1.php"; ?>
					</div>
				</div>
				<div class="col-xl-9">
					<div class=" box m-10 add_area" style="display: <?php if ($page_title == "Edit") {
																		echo "block";
																	} else {
																		echo "none";
																	} ?> ">
						<div class="box-header with-border">
							<?php if (isset($page_title) && $page_title == "Edit") : ?>
								<h3>
									<?php echo trans('edit-business') ?>
								</h3>
							<?php else : ?>
								<h3>
									<?php echo trans('add-new-business') ?>
								</h3>
							<?php endif; ?>

							<div class="box-tools pull-right acc_re">
								<?php if (isset($page_title) && $page_title == "Edit") : ?>
									<a href="<?php echo base_url('admin/business') ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-angle-left"></i> Back</a>
								<?php else : ?>
									<a href="#" class="pull-right btn btn-info rounded btn-sm cancel_btn"><i class="fa fa-angle-left"></i>
										<?php echo trans('back') ?>
									</a>
								<?php endif; ?>
							</div>
						</div>

						<div class="box-body">
							<form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form row buss_new" action="<?php echo base_url('admin/business/add') ?>" role="form" novalidate>
								<div class="form-group col-md-12">
									<div class="avatar-uploadb text-center">
										<div class="avatar-edit">
											<input type='file' name="photo1" id="imageUpload" accept=".png, .jpg, .jpeg" />
											<label for="imageUpload"></label>
										</div>
										<div class="avatar-preview">
											<?php if (isset($page_title) && $page_title == "Edit") : ?>
												<div id="imagePreview" style="background-image: url('<?php echo base_url($busines[0]['logo']); ?>');">
												</div>
											<?php else : ?>
												<div id="imagePreview">
													<p class="upload-text"><i class="fa fa-plus"></i> <br>
														<?php echo trans('upload-business-logo') ?>
													</p>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>

								<div class="form-group col-md-12">
									<label>
										<?php echo trans('name') ?> <span class="text-danger">*</span>
									</label>
									<input type="text" class="form-control" required name="name" value="<?php echo html_escape($busines[0]['name']); ?>" required>
								</div>

								<div class="form-group col-md-12">
									<label>
										<?php echo trans('title') ?>
									</label>
									<input type="text" class="form-control" name="title" value="<?php echo html_escape($busines[0]['title']); ?>">
								</div>

								<div class="form-group col-md-12">
									<label>
										<?php echo trans('contact') . ' ' . trans('number') ?> <span class="text-danger">*</span>
									</label>
									<input type="text" class="form-control" name="biz_number" value="<?php echo html_escape($busines[0]['biz_number']); ?>" required>
								</div>
								<div class="form-group col-md-12">
									<label>Website URL</label>
									<input type="text" class="form-control" name="website_url" value="<?php echo html_escape($busines[0]['website_url']); ?>">
								</div>

								<?php
								if (isset($busines[0]['tax_format'])) {
									$taxformats = explode(',', $busines[0]['tax_format']);
									$vatcodes = explode(',', $busines[0]['vat_code']);
									foreach ($taxformats as $key => $taxformat) {
								?>
										<div class="form-group col-md-4 remove_row_<?php echo $key; ?>">
											<label>Select Type</label>
											<select class="form-control single_select tax_format tax_format_<?php echo $key ?>" style="width:100%" data-tax-format-id="<?php echo $key ?>" name="tax_format[]" id="tax_format<?php echo $key; ?>" required>
												<option value="">
													<?php echo trans('select') ?>
												</option>
												<option value="GST Number" <?php echo ($taxformat == 'GST Number') ? 'selected' : ''; ?>>GST Number</option>
												<option value="Tax Number" <?php echo ($taxformat == 'Tax Number') ? 'selected' : ''; ?>>Tax Number</option>
												<option value="Vat Number" <?php echo ($taxformat == 'Vat Number') ? 'selected' : ''; ?>>Vat Number</option>
												<option value="Tax/Vat Number" <?php echo ($taxformat == 'Tax/Vat Number') ? 'selected' : ''; ?>>Tax/Vat Number</option>
												<option value="CIN Number" <?php echo ($taxformat == 'CIN Number') ? 'selected' : ''; ?>>CIN Number</option>
											</select>
											<small class="error_tax_format_<?php echo $key ?>" style="display: none;">this is requeired!!!</small>
										</div>
										<div class="form-group col-md-4 remove_row_<?php echo $key; ?>">
											<label>Number</label>
											<input type="text" class="form-control vat_codes" data-vat-code-id="<?php echo $key ?>" name="vat_code[]" value="<?php echo html_escape($vatcodes[$key]); ?>" required>
											<small class="error_vat_code_id_<?php echo $key ?>" style="display: none;">this is requeired!!!</small>
										</div>
										<?php if ($key == 0) { ?>
											<div class="form-group mt-3 col-md-4">
												<label></label> <br>
												<button type="button" class="btn btn-primary" id="add_more">Add</button>
											</div>
										<?php } else { ?>
											<div class="form-group mt-3 col-md-4 remove_row_<?php echo $key; ?>">
												<label></label> <br>
												<button type="button" class="btn btn-danger" onclick="remove_row(<?php echo $key; ?>)">Remove</button>
											</div>
										<?php } ?>
									<?php } ?>
									<div class="row col-md-12" id="show_type_tax">
									</div>
								<?php } else { ?>
									<div class="form-group col-md-4">
										<label>Select Type</label>
										<select class="form-control single_select tax_format tax_format_0" style="width:100%" data-tax-format-id="0" name="tax_format[]" id="tax_format">
											<option value="">
												<?php echo trans('select') ?>
											</option>
											<option value="GST Number" <?php echo ($busines[0]['tax_format'] == 'GST Number') ? 'selected' : ''; ?>>GST Number</option>
											<option value="Tax Number" <?php echo ($busines[0]['tax_format'] == 'Tax Number') ? 'selected' : ''; ?>>Tax Number</option>
											<option value="Vat Number" <?php echo ($busines[0]['tax_format'] == 'Vat Number') ? 'selected' : ''; ?>>Vat Number</option>
											<option value="Tax/Vat Number" <?php echo ($busines[0]['tax_format'] == 'Tax/Vat Number') ? 'selected' : ''; ?>>Tax/Vat Number</option>
											<option value="CIN Number" <?php echo ($busines[0]['tax_format'] == 'CIN Number') ? 'selected' : ''; ?>>CIN Number</option>
										</select>
										<small class="error_tax_format_0" style="display: none;">this is
											requeired!!!</small>
									</div>
									<div class="form-group col-md-4">
										<label>Number</label>
										<input type="text" class="form-control vat_codes" data-vat-code-id="0" name="vat_code[]" value="<?php echo html_escape($busines[0]['vat_code']); ?>">
										<small class="error_vat_code_id_0" style="display: none;">this is
											requeired!!!</small>
									</div>
									<div class="form-group mt-3 col-md-4">
										<label></label> <br>
										<button type="button" class="btn btn-primary" id="add_more">Add</button>
									</div>
									<div class="row col-md-12" id="show_type_tax">
									</div>
								<?php } ?>
								<div class="form-group col-md-12">
									<label>
										<?php echo trans('phone') ?>,
										<?php echo trans('address') ?>
									</label>
									<textarea id="ckEditor" class="form-control" name="address"><?php echo html_escape($busines[0]['address']); ?></textarea>
								</div>
								<div class="form-group col-md-12">
									<label for="example-input-normal">Business Category <span class="text-danger">*</span></label>
									<select class="selectfield textfield--grey single_select col-sm-12 single_select" name="category" required style="width: 100%">
										<option value="">
											<?php echo trans('select-business-category') ?>
										</option>
										<?php foreach ($categories as $category) : ?>
											<option value="<?php echo html_escape($category->id); ?>" <?php if ($category->id == $busines[0]['category']) {
																											echo "selected";
																										} ?>>
												<?php echo html_escape($category->name); ?>
											</option>
										<?php endforeach ?>
									</select>
								</div>


								<div class="form-group col-md-12">
									<label for="example-input-normal">
										<?php echo trans('country') ?> <span class="text-danger">*</span>
									</label>
									<select class="selectfield textfield--grey single_select col-sm-12 single_select" id="country" name="country" style="width: 100%" required <?php echo (isset($page_title) && $page_title == "Edit") ? "disabled" : '' ?>>
										<option value="">
											<?php echo trans('select-country') ?>
										</option>
										<?php foreach ($countries as $country) : ?>
											<option value="<?php echo html_escape($country->id); ?>" <?php if ($country->id == $busines[0]['country']) {
																											echo "selected";
																										} ?>>
												<?php echo html_escape($country->name); ?>
											</option>
										<?php endforeach ?>
									</select>
								</div>
								<div class="form-group col-md-12" id="currency">
									<?php if (isset($page_title) && $page_title == "Edit") : ?>
										<p>
											<?php echo html_escape($busines[0]['currency_code']) ?> -
											<?php echo html_escape($busines[0]['currency_name']) ?> (
											<?php echo html_escape($busines[0]['currency_symbol']) ?>)
										</p>
									<?php endif; ?>
								</div>
								<p class="info callout callout-default">
									<?php echo trans('currency-notice') ?>
								</p>

								<input type="hidden" name="id" value="<?php echo html_escape($busines['0']['id']); ?>">
								<!-- csrf token -->
								<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

								<div class="row mt-10">
									<div class="col-sm-12">
										<?php if (isset($page_title) && $page_title == "Edit") : ?>
											<input type="hidden" name="country" value="<?php echo html_escape($busines['0']['country']); ?>">
											<button type="submit" class="btn btn-info rounded pull-left"><i class="fa fa-check"></i>
												<?php echo trans('save-changes') ?>
											</button>
										<?php else : ?>
											<button type="submit" class="btn btn-info rounded pull-left"><i class="fa fa-check"></i>
												<?php echo trans('save') ?>
											</button>
										<?php endif; ?>
									</div>
								</div>

							</form>
						</div>
					</div>


					<?php if (isset($page_title) && $page_title != "Edit") : ?>
						<div class="list_area container">

							<?php if (isset($page_title) && $page_title == "Edit") : ?>
								<h3>
									<?php echo trans('edit-business') ?> <a href="<?php echo base_url('admin/business') ?>" class="pull-right btn btn-primary btn-sm"><i class="fa fa-angle-left"></i>
										<?php echo trans('back') ?>
									</a>
								</h3>
							<?php else : ?>
								<h3>
									<?php echo trans('business') ?> <a href="#" class="pull-right btn btn-info rounded btn-sm add_btn"><i class="fa fa-plus"></i>
										<?php echo trans('add-new-business') ?>
									</a>
								</h3>
							<?php endif; ?>

							<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0">
								<table class="table table-hover cushover <?php if (count($business) > 10) {
																				echo "datatable";
																			} ?>" id="dg_table">
									<thead>
										<tr>
											<th>#</th>
											<th>
												<?php echo trans('logo') ?>
											</th>
											<th>
												<?php echo trans('informations') ?>
											</th>
											<th></th>
											<th>
												<?php echo trans('action') ?>
											</th>
										</tr>
									</thead>
									<tbody>
										<?php $i = 1;
										foreach ($business as $busines) : ?>
											<tr id="row_<?php echo html_escape($busines->id); ?>">
												<td>
													<?php echo $i; ?>
												</td>
												<td>
													<?php if (!empty($busines->logo)) : ?>
														<img width="60px" class="img-thumbnails" src="<?php echo base_url($busines->logo); ?>">
													<?php endif ?>
												</td>
												<td>
													<h3 class="mt-0 mb-0" style="text-transform: capitalize">
														<?php echo html_escape($busines->name); ?>
													</h3>
													<p class="mb-0">Category: <strong style="text-transform: capitalize">
															<?php echo html_escape($busines->category_name) ?>
														</strong></p>
													<p class="mb-0">
														<?php echo html_escape($busines->currency_code . ' - ' . $busines->currency_name . ' (' . $busines->currency_symbol . ')'); ?>
													</p>
												</td>
												<td class="text-center">
													<?php if ($busines->is_primary == 1) : ?>
														<a href="#" class="btn btn-default bor-cls" disabled data-toggle="tooltip" data-placement="top" title="This is your default business"><i class="fa fa-check"></i>
															<?php echo trans('active') ?>
														</a>
													<?php else : ?>
														<a data-val="<?php echo html_escape($busines->name); ?>" data-id="<?php echo html_escape($busines->id); ?>" href="<?php echo base_url('admin/business/set_primary/' . md5($busines->id)); ?>" class="btn btn-default primary_item" data-toggle="tooltip" data-placement="top" title="<?php echo trans('default-business') ?>"> <?php echo trans('set-default') ?></a>
													<?php endif ?>
												</td>
												<td class="actions" width="15%">
													<a href="<?php echo base_url('admin/business/edit/' . md5($busines->id)); ?>" class="on-default edit-row" data-placement="top" title="Edit"><i class="fa fa-pencil"></i></a>
													<?php if (count($business) > 1) : ?>
														<?php if ($busines->is_primary != 1) : ?>
															<a href="#deleteModal_<?php echo md5($busines->id); ?>" data-toggle="modal" class="on-default remove-row" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a>
														<?php endif ?>
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
			</div>
		</div>
	</section>
</div>

<?php foreach ($business as $busines) : ?>
	<div id="deleteModal_<?php echo md5($busines->id); ?>" class="modal fade" role="dialog" aria-labelledby="vcenter" aria-hidden="true">
		<div class="modal-dialog modal-dialog-zoom modal-md">
			<form id="customer-form" method="post" enctype="multipart/form-data" class="validate-form" action="" role="form" novalidate>
				<div class="modal-content modal-md">
					<div class="modal-header">
						<h4 class="modal-title text-danger">
							<?php echo trans('delete-confirmation') ?>
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					</div>
					<div class="modal-body">

						<h3>
							<?php echo trans('sure-delete-business') ?> <strong class="text-info">
								<?php echo html_escape($busines->name); ?>
							</strong>
							<?php echo trans('permanently') ?>?
						</h3>
						<p>
							<?php echo trans('affect-business') ?>
						</p>

					</div>

					<div class="modal-footer">
						<!-- csrf token -->
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
						<a class="btn btn-danger waves-effect pull-left" href="<?php echo base_url('admin/business/delete/' . $busines->id); ?>"><i class="fa fa-trash"></i>
							<?php echo trans('delete') ?>
						</a>
					</div>
				</div>
			</form>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
<?php endforeach; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		var i = 25;
	});
	$('body').on('click', '#add_more', function(event) {
		if (select_validation(event)) {
			var html = '<div class="form-group col-md-4 remove_row_' + i + '"><label>Select Type</label><select class="form-control single_select tax_format tax_format_' + i + '" style="width:100%" data-tax-format-id="' + i + '" name="tax_format[]" id="tax_format' + i + '" required><option value="">Select Type</option><option value="GST Number">GST Number</option><option value="Tax Number">Tax Number</option><option value="Vat Number">Vat Number</option><option value="Tax/Vat Number">Tax/Vat Number</option><option value="CIN Number">CIN Number</option></select><small class="error_tax_format_' + i + '" style="display: none;">this is requeired!!!</small></div><div class="form-group col-md-4 remove_row_' + i + '"><label>Number</label><input type="text" class="form-control vat_codes" data-vat-code-id="' + i + '" name="vat_code[]" value=""><small class="error_vat_code_id_' + i + '" style="display: none;">this is requeired!!!</small></div><div class="form-group mt-3 col-md-4 remove_row_' + i + '"><label></label> <br><button type="button" class="btn btn-danger" onclick="remove_row(' + i + ')">Remove</button></div>';
			i++;
			$('#show_type_tax').append(html);
			$('.single_select').select2();
		}
		select_uniqe_formate_tax_validaion();
	});

	function select_uniqe_formate_tax_validaion() {
		$('body').on('change', '.tax_format', function(event) {
			let text_formate = [];
			let current_value = $(this).find('option').filter(':selected').text();
			var selectElements = $('select[name="tax_format[]"]');

			selectElements.each(function() {
				if (jQuery.inArray(current_value, text_formate) >= 0) {
					$("#add_more").prop("disabled", true);
					let tax_id = $(this).attr('data-tax-format-id');
					$(`.error_tax_format_${tax_id}`).html(`Please Provide Select Another Text Format`).addClass('text-danger').removeClass('text-success');
					$(`.error_tax_format_${tax_id}`).fadeIn().delay(3000).fadeOut();
					return false;
				} else {
					$("#add_more").prop("disabled", false);
				}
				text_formate.push($(this).val());
			});
		});
	}

	function remove_row(row) {
		$('.remove_row_' + row + '').remove();
	}


	$('#cat-form').submit(function(event) {
		select_validation(event);
	});

	function select_validation(event) {
		var isValid = true;
		let text_formate = [];
		var selectElements = $('select[name="tax_format[]"]');
		var inputElements = $('input[name="vat_code[]"]');

		selectElements.each(function() {
			if ($(this).val() === '') {
				isValid = false;
				// $(this).addClass('error'); // Add custom error class if needed
				let tax_id = $(this).attr('data-tax-format-id');
				$(`.error_tax_format_${tax_id}`).html(`Please Provide Select Any One`).addClass('text-danger').removeClass('text-success');
				$(`.error_tax_format_${tax_id}`).fadeIn().delay(3000).fadeOut();
			} else {
				$(this).removeClass('error'); // Remove error class if previously added
			}
		});
		inputElements.each(function() {
			if ($(this).val() === '') {
				isValid = false;
				// $(this).addClass('error'); // Add custom error class if needed
				let vat_id = $(this).attr('data-vat-code-id');
				let selected_text = $(`.tax_format_${vat_id}`).find('option').filter(':selected').text();
				$(`.error_vat_code_id_${vat_id}`).html(`Please Provide ${selected_text}`).addClass('text-danger').removeClass('text-success');
				$(`.error_vat_code_id_${vat_id}`).fadeIn().delay(3000).fadeOut();
			} else {
				$(this).removeClass('error'); // Remove error class if previously added
			}
		});

		if (!isValid) {
			event.preventDefault(); // Prevent form submission if validation fails
		}
		return isValid;
	}

	changeFunction();

	function changeFunction() {
		var tax_format = $("#tax_format").val();

		if (tax_format == 'GST Number') {
			$("#vat_code_show").show();
			$("#text_name_change").text("GST Number");
		} else if (tax_format == 'Tax Number') {
			$("#vat_code_show").show();
			$("#text_name_change").text("Tax Number");
		} else if (tax_format == 'Vat Number') {
			$("#vat_code_show").show();
			$("#text_name_change").text("Vat Number");
		} else if (tax_format == 'Tax/Vat Number') {
			$("#vat_code_show").show();
			$("#text_name_change").text("Tax/Vat Number");
		} else {
			$("#vat_code_show").hide();
		}
	}

	$('body').on('keyup', '.vat_codes', function(e) {
		let vat_id = $(this).attr('data-vat-code-id');
		console.log('vat_id', vat_id);
		let vat_value = $(this).val();
		let selected_text = $(`.tax_format_${vat_id}`).find('option').filter(':selected').text();
		if (selected_text == "GST Number") {
			let innerHTML = "Please Enter GST Number.";
			let text_class = "danger";
			let remove_class = "success";
			// console.log('selected_text',selected_text);			
			if (vat_value) {
				var expr = /^([0-9]{2}[a-zA-Z]{4}([a-zA-Z]{1}|[0-9]{1})[0-9]{4}[a-zA-Z]{1}([a-zA-Z]|[0-9]){3}){0,15}$/;
				if (!expr.test(vat_value)) {
					innerHTML = "Invalid GST Number.";
				} else {
					innerHTML = "Valid GST Number.";
					text_class = "success";
					remove_class = "danger";
				}
			}
			$(`.error_vat_code_id_${vat_id}`).html(`${innerHTML}`).addClass(`text-${text_class}`).removeClass(`text-${remove_class}`);
			$(`.error_vat_code_id_${vat_id}`).fadeIn().delay(3000).fadeOut();
		}
	});

	$('INPUT[type="file"]').change(function() {
		//var ext = this.value.match(/\.(.+)$/)[1];

		var ext = this.value.split('.').pop();

		switch (ext) {
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
				$('#uploadButton').attr('disabled', false);
				break;
			default:
				alert('This is not an allowed file type.');
				this.value = '';
		}
	});
</script>