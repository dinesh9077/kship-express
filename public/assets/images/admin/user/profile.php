<style>
	.avatar-upload {
		margin: 0 !important;
	}

	.main-align-cls {
		text-align: right;
	}

	.form-control {
		color: #7334d9 !important;
		background: linear-gradient(90deg, #9E6AF6 0.25%, #6A34C3 100.78%);
		background-clip: border-box;
		background-clip: text;
		-webkit-background-clip: text;
	}

	.select2-container--default .select2-selection--single .select2-selection__rendered {
		color: #6b35c4;
		line-height: 50px;
		font-weight: 600;
	}
</style>

<div class="content-wrapper">
	<section class="content">

		<div class="card-box">
			<?php if (auth('role') == 'user') {
				$action = 'update';
			} else {
				$action = 'update_role';
			} ?>
			<div class="row m-5 mt-20">
				<div class="col-xl-3 col-lg-12">
					<div class="nav-tabs-custom profile_menu_web">
						<?php include "include/profile_menu.php"; ?>
					</div>
					<div class="nav-tabs-custom profile_menu_mobile">
						<?php include "include/profile_menu_1.php"; ?>
					</div>
				</div>
				<div class="col-xl-9">
					<div class="box1">
						<form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/profile/' . $action) ?>" role="form" class="form-horizontal">
							<!--<div class="nav-tabs-custom">-->

							<div class="box-header">
								<h3 class="box-title"><?php echo trans('personal-information') ?></h3>
							</div>

							<div class="box-body p-10">

								<div class="form-group">
									<div class="avatar-upload">
										<div class="avatar-edit">
											<input type='file' name="photo" id="imageUpload" accept=".png, .jpg, .jpeg" />
											<label for="imageUpload"></label>
										</div>
										<div class="avatar-preview">
											<div id="imagePreview" style="background-image: url('<?php echo base_url($user->thumb); ?>');">
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-xl-3 col-lg-6">
										<div class="form-group m-t-20">
											<label class="control-label" for="example-input-normal"><?php echo trans('name') ?> <span class="text-danger">*</span></label>
											<div class="">
												<input type="text" name="name" value="<?php echo html_escape($user->name); ?>" class="form-control" required>
											</div>
										</div>
									</div>

									<div class="col-xl-3 col-lg-6">
										<div class="form-group m-t-20">
											<label class="control-label" for="example-input-normal"><?php echo trans('email') ?> <span class="text-danger">*</span></label>
											<div class="">
												<input type="text" name="email" value="<?php echo html_escape($user->email); ?>" class="form-control" required>
											</div>
										</div>
									</div>

									<div class="col-xl-3 col-lg-6">
										<div class="form-group m-t-20">
											<label class="control-label" for="example-input-normal"><?php echo trans('phone') ?> <span class="text-danger">*</span></label>
											<div class="">
												<input type="text" name="phone" value="<?php echo html_escape($user->phone); ?>" class="form-control" required>
											</div>
										</div>
									</div>

									<div class="col-xl-3 col-lg-6">
										<div class="form-group p-0">
											<label class="control-label" for="example-input-normal"><?php echo trans('country') ?> <span class="text-danger">*</span></label>
											<div class="">
												<select class="form-control single_select" name="country" required>
													<option value="0"><?php echo trans('select') ?></option>
													<?php foreach ($countries as $country) : ?>
														<option value="<?php echo html_escape($country->id); ?>" <?php echo ($user->country == $country->id) ? 'selected' : ''; ?>>
															<?php echo html_escape($country->name); ?>
														</option>
													<?php endforeach ?>
												</select>
											</div>
										</div>
									</div>

									<div class="col-xl-3 col-lg-6">
										<div class="form-group">
											<label class="control-label" for="example-input-normal"><?php echo trans('city') ?> <span class="text-danger">*</span></label>
											<div class="">
												<input type="text" name="city" class="form-control" value="<?php echo html_escape($user->city); ?>" required>
											</div>
										</div>
									</div>

									<div class="col-xl-3 col-lg-6">
										<?php if (auth('role') == 'user') : ?>
											<div class="form-group">
												<label class="control-label" for="example-input-normal"><?php echo trans('state') ?> <span class="text-danger">*</span></label>
												<div class="">
													<input type="text" name="state" class="form-control" value="<?php echo html_escape($user->state); ?>" required>
												</div>
											</div>
									</div>

									<div class="col-xl-3 col-lg-6">
										<div class="form-group">
											<label class="control-label" for="example-input-normal"><?php echo trans('postcode') ?></label>
											<div class="">
												<input type="text" name="postcode" class="form-control" value="<?php echo html_escape($user->postcode); ?>">
											</div>
										</div>
									<?php endif ?>

									</div>
								</div>

								<div class="form-group">
									<label class="control-label p-0" for="example-input-normal"><?php echo trans('address') ?></label>
									<div class="p-0">
										<input type="text" name="address" class="form-control" value="<?php echo html_escape($user->address); ?>" style="background: linear-gradient(90deg, rgba(158, 106, 246, 0.10) 0.25%, rgba(106, 52, 195, 0.10) 100.78%), #FFF;">
									</div>
								</div>
							</div>

							<div class="box-footer main-align-cls">
								<!-- csrf token -->
								<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
								<button type="submit" class="btn btn-info waves-effect rounded w-md waves-light"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
							</div>



							<!--</div>-->
						</form>
					</div>


				</div>
			</div>
		</div>
	</section>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script>
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