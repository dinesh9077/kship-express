<style>
	.avatar-uploadb {
		position: relative;
		max-width: 300px;
		margin: 0px !important;
	}

	.avatar-delete a {
		position: absolute;
		right: 12px;
		z-index: 1;
		top: 40%;
		width: 35px;
		height: 35px;
		margin-bottom: 0;
		border-radius: 100%;
		background: #FFFFFF;
		border: 1px solid #f1f1f1;
		box-shadow: 0px 2px 10px 0px rgb(0 0 0 / 12%);
		cursor: pointer;
		padding: 1%;
		color: red;
		transition: 0.3s ease;
	}

	.avatar-delete a:hover {
		background: red;
		color: white;
	}

	input.color-picker {
		background: transparent;
		border: none;
	}

	.panel {
		padding: 0 !important;
		background-color: white;
		max-height: none !important;
		overflow: hidden;
		transition: max-height 0.2s ease-out;
	}

	/* ghanshyam Css 31-08-2023 */
	.invoice-img img {
		height: 250px !important;
	}
</style>

<link href="<?php echo base_url() ?>assets/admin/css/dropzone.css" rel="stylesheet" />
<link href="<?php echo base_url() ?>assets/admin/css/cropper.css" rel="stylesheet" />
<div class="content-wrapper">
	<section class="content">
		<div class="card-box">
			<form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/business/invoice_customize') ?>" role="form" class="form-horizontal">

				<!--<div class="nav-tabs-custom">-->

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

						<div class="box-header">
							<h3 class="box-title"><?php echo trans('invoice-customization') ?></h3>
						</div>

						<div class="box-body p-10">

							<div class="row" style="width: 100%; margin: 0">
								<div class="col-md-12 p-5" style="width: 100%; margin: 0;">
									<p class="m-0"><?php echo trans('choose-invoice-templates') ?></p>
								</div>
							</div>
							<div class="row mb-0 inv-custom">

								<?php
								if (user()->user_type == 'trial') {
									$limits = 1;
									$limit = 10;
								} else {
									$limits = check_package_limit('invoice_template');

									if ($limits == -2) {
										$limit = 10;
									} elseif ($limits == -1) {
										$limit = 1;
									} else {
										$limit = 10;
									}
								}

								if ($limits == 1) {
									$hide = [1];
								} else if ($limits == 2) {
									$hide = [1, 2];
								} else if ($limits == 3) {
									$hide = [1, 2, 3];
								} else if ($limits == 4) {
									$hide = [1, 2, 3, 4];
								} else if ($limits == 5) {
									$hide = [1, 2, 3, 4, 5];
								} else if ($limits == 6) {
									$hide = [1, 2, 3, 4, 5, 6];
								} else if ($limits == 7) {
									$hide = [1, 2, 3, 4, 5, 6, 7];
								} else if ($limits == 8) {
									$hide = [1, 2, 3, 4, 5, 6, 7, 8];
								} else if ($limits == 9) {
									$hide = [1, 2, 3, 4, 5, 6, 7, 8, 9];
								} else if ($limits == 10) {
									$hide = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
								} else {
									$hide = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
								}


								?>

								<?php for ($i = 1; $i <= $limit; $i++) {

									// if($i!=2)
									// {
									//     $ii = 1;
									//     if($i==3) {$ii=2;}
									//     if($i==4) {$ii=3;}

								?>
									<div class="col-xl-3 col-md-4 col-sm-4 text-center p-5 col-12">
										<div class="invoice-layout <?php if ($i == $this->business->template_style) {
																		echo "active";
																	} ?>">
											<div class="invoice-img">
												<img src="<?php echo base_url() ?>assets/admin/layouts/invoice<?php echo $i ?>.png">
												<!--<div class="iconv"><a data-toggle="modal" href="#templateModal_<?php echo $ii ?>"><i class="icon-eye"></i></a></div>-->
												<div class="iconv"><a data-toggle="modal" href="#templateModal_<?php echo $i ?>"><i class="icon-eye"></i></a></div>
											</div>

											<div class="radio radio-info radio-inline mt-10">
												<input type="radio" id="inlineRadio<?php echo $i ?>" <?php if ($i == $this->business->template_style) {
																											echo "checked";
																										} ?> value="<?php echo $i ?>" name="template_style" <?php echo (!in_array($i, $hide)) ? "disabled" : ""; ?>>
												<label class="<?php if ($i == $this->business->template_style) {
																	echo "text-primary";
																} ?>" for="inlineRadio<?php echo $i ?>"> <?php echo trans('template') ?> <?php echo $i ?> </label>
												<label><?php echo (!in_array($i, $hide)) ? "Uprgade Your Package" : "Included in Your Package"; ?></label>
											</div>
										</div>
									</div>
								<?php  }
								//}
								?>
							</div>

							<!--<div class="row pl-30">
                            
                            <div class="col-5 p-10">
                                <div class="form-group p-0">
                                    <p><?php //echo trans('accent-color') 
										?></p>
                                    <input type="text" name="color" class="colorpicker-default form-control colorpicker-element" value="<?php //echo html_escape($this->business->color) 
																																		?>">
								</div>
							</div>
                            <div class="col-5 text-left">
                                <p></p><br>
                                <a class="colors-trigger colorpicker-advanced colorpicker-element mt-10" style="background-color: <?php //echo html_escape($this->business->color) 
																																	?>" href="#"></a>
							</div>
						</div>-->

							<div class="row pl-30">

								<div class="col-5 p-10">
									<div class="form-group p-0">
										<p><?php echo trans('accent-color') ?></p>
										<input type="color" name="color" class="color-picker" value="<?php echo html_escape($this->business->color) ?>">
									</div>
								</div>
								<div class="col-5 text-left">

								</div>
							</div>

							<!--<div class="row pl-30 mb-20">
                            <div class="col-md-12 pl-10">
							<div class="form-group">
							<input type="checkbox" id="md_checkbox_1" class="filled-in chk-col-blue" value="1" name="enable_qrcode" <?php //if($this->business->enable_qrcode == 1){echo "checked";} 
																																	?>>
							<label for="md_checkbox_1"> <? php // echo trans('enable-invoice-qr-code') 
														?></label>
							<p><? php // echo trans('enable-qr-help') 
								?></p>
							</div>
                            </div>
						</div>-->
							<div class="row pl-30">
								<div class="col-md-12 pl-10">
									<div class="form-group">
										<label for="md_checkbox_1">Payment QR-Code</label>
										<p>Upload your UPI QR code</p>
										<div class="avatar-uploadb text-center">
											<div class="avatar-edit">
												<input type='file' name="photo1" id="upload_image" accept=".png, .jpg, .jpeg" />
												<label for="upload_image"></label>
											</div>
											<div class="avatar-delete" style="display: <?php echo ($this->business->pay_qrcode != "") ? "block" : "none"; ?>">
												<a href="#" id="remove_qr"><i class="fa fa-trash-o"></i></a>
											</div>
											<div class="avatar-preview">
												<?php if ($this->business->pay_qrcode != "") { ?>
													<div id="imagePreview" style="background-image: url(<?php echo base_url($this->business->pay_qrcode); ?>);">
													</div>
												<?php } else { ?>
													<div id="imagePreview">
														<p class="upload-text"><i class="fa fa-plus"></i> <br> Upload QR-Code</p>
													</div>
												<?php } ?>

											</div>
										</div>
									</div>
								</div>
							</div>
							<input type="hidden" name="pay_qrcode" id="pay_qrcode" value="<?php echo $this->business->pay_qrcode; ?>">
							<br>
							<!-- <div class="row pl-30">
                            <div class="col-md-12 pl-10">
                                <div class="form-group">
                                    <input type="checkbox" id="md_checkbox_2" class="filled-in chk-col-blue" value="1" name="enable_stock" <?php if ($this->business->enable_stock == 1) {
																																				echo "checked";
																																			} ?>>
                                    <label for="md_checkbox_2"> <?php echo trans('enable-stock') ?></label>
                                    <p><?php echo trans('enable-sotck-help') ?></p>
								</div>
							</div>
						</div>-->

							<div class="row pl-30 mt-50">
								<div class="col-md-12 pl-10">
									<p class="m-0"><?php echo trans('set-default-footer-note') ?></p>
								</div>
								<div class="col-md-11 p-10">
									<!--<div class="form-group p-0">-->
									<textarea class="form-control" rows="4" name="footer_note" placeholder="<?php echo trans('invoice-footer-placeholder') ?>"><?php echo $this->business->footer_note ?></textarea>
									<!--</div>-->
								</div>
							</div>


						</div>

						<div class="box-footer">
							<!-- csrf token -->
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							<button type="submit" class="btn btn-info waves-effect rounded w-md waves-light"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
						</div>

					</div>
				</div>
				<!--</div>-->
			</form>
		</div>
	</section>
</div>

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalLabel">Crop Image Before Upload</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="img-container">
					<div class="row">
						<div class="col-md-8">
							<img src="" id="sample_image" />
						</div>
						<div class="col-md-4">
							<div class="preview"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="crop">Crop</button>
			</div>
		</div>
	</div>
</div>
<?php
for ($a = 1; $a <= $limit; $a++) {
	// 	if($a!=2)
	// 	{
	// 		$ii = 1;
	// 		if($a==3) {$ii=2;}
	// 		if($a==4) {$ii=3;} 
?>

	<div id="templateModal_<?php echo html_escape($a) ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="vcenter" aria-hidden="true">
		<div class="modal-dialog modal-dialog-zoom modal-lg">
			<div class="modal-content modal-md" style="margin-top: 10%">
				<div class="modal-header">
					<h4 class="modal-title" id="vcenter">Invoice template - <?php echo html_escape($a) ?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body">
					<center>
						<img src="<?php echo base_url() ?>assets/admin/layouts/invoice<?php echo $a ?>.png">
					</center>
				</div>
			</div>
		</div>
	</div>
<?php
}
// } 
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

<script src="<?php echo base_url() ?>assets/admin/js/dropzone.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/cropper.js"></script>
<script>
	$(document).ready(function() {

		var $modal = $('#modal');
		var image = document.getElementById('sample_image');
		var cropper;

		$('#upload_image').change(function(event) {
			var files = event.target.files;
			var done = function(url) {
				image.src = url;
				$modal.modal('show');
			};

			if (files && files.length > 0) {
				reader = new FileReader();
				reader.onload = function(event) {
					done(reader.result);
				};
				reader.readAsDataURL(files[0]);
			}
		});

		$modal.on('shown.bs.modal', function() {
			cropper = new Cropper(image, {
				aspectRatio: 1,
				viewMode: 3,
				preview: '.preview'
			});
		}).on('hidden.bs.modal', function() {
			cropper.destroy();
			cropper = null;
		});

		$("#crop").click(function() {
			var base_url = '<?php echo base_url(); ?>';
			canvas = cropper.getCroppedCanvas({
				width: 400,
				height: 400,
			});

			canvas.toBlob(function(blob) {

				var reader = new FileReader();
				reader.readAsDataURL(blob);
				reader.onloadend = function() {
					var base64data = reader.result;

					$.ajax({
						url: "<?php echo base_url() . 'admin/business/upload_qrcode'; ?>",
						method: "POST",
						data: {
							image: base64data,
							'csrf_test_name': csrf_token
						},
						success: function(data) {
							$modal.modal('hide');
							$('.upload-text').remove();
							$('#imagePreview').css('background-image', 'url(' + base_url + '' + data + ')');
							$('#pay_qrcode').val(data);
							$('.avatar-delete').show();
						}
					});
				}
			});
		});
		$('#remove_qr').click(function() {
			$('.avatar-delete').hide();
			$('#imagePreview').css('background-image', 'none');
			$('#pay_qrcode').val('');
		});
	});
</script>