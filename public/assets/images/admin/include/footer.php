<style>
	img.msg_btn.circle {
		border: none !important;
		position: fixed;
		padding: 8px;
		right: 20px !important;
		height: 44px !important;
		width: 44px !important;
		border-radius: 50% !important;
		border: none;
		bottom: 20px !important;
		z-index: 999;
		background: #001B66;
		left: auto;
		box-shadow: 0 0 0 rgba(204, 169, 44, 0.4);
		animation: pulse-blue2 2s linear infinite;
	}

	.avenue-messenger {
		opacity: 1;
		-webkit-border-radius: 20px;
		-moz-border-radius: 20px;
		border-radius: 7px;
		max-height: 460px !important;
		min-height: 220px !important;
		width: 320px;
		background: white;
		position: fixed;
		right: 2%;
		bottom: 10%;
		margin: auto;
		z-index: 10;
		box-shadow: 2px 7px 12px rgb(22 20 19 / 30%);
		-webkit-transition: 0.3s all ease-out 0.1s, transform 0.2s ease-in;
	}

	.query {
		padding: 2%;
		border: 1px solid #EBEFF4;
		border-radius: 4px;
		background: #00afa51a;
	}

	.query-closed {
		padding: 2%;
		border: 1px solid #9f9e9e;
		border-radius: 4px;
		background: #8787871a;
	}

	.query_category {
		padding: 3%;
		background: #EBEFF4;
		overflow-y: auto;
		min-height: auto;
		max-height: 400px;
	}

	.query_category::-webkit-scrollbar {
		width: 6px;
	}

	/* Track */
	.query_category::-webkit-scrollbar-track {
		background: #00000014;
	}

	/* Handle */
	.query_category::-webkit-scrollbar-thumb {
		background: #8888887a;
		border-radius: 25px;
	}

	/* Handle on hover */
	.query_category::-webkit-scrollbar-thumb:hover {
		background: #555;
	}

	.file-edit input {
		display: none;
	}


	@media only screen and (max-device-width: 667px),
	screen and (max-width: 450px) {
		.avenue-messenger {
			width: 95% !important;
			left: 0;
			right: 0;
		}
	}
	}

	.imageThumb {
		max-height: 75px;
		border: 2px solid;
		padding: 1px;
		cursor: pointer;
	}

	.pip {
		background: #f4f4f4;
		display: inline-block;
		padding: 0px 12px 0 0;
		margin-right: 20px;
		margin-top: 15px;
	}

	.remove {
		font-size: 20px;
		color: #cd1313;
		text-align: center;
		cursor: pointer;
		transition: 0.3s ease;
	}

	.remove:hover {
		color: #cd1313;
	}

	form#support_ticketform {
		min-height: auto;
		max-height: 300px;
		overflow-y: auto;
		overflow-x: hidden;
	}

	form#support_ticketform::-webkit-scrollbar {
		width: 6px;
	}

	/* Track */
	form#support_ticketform::-webkit-scrollbar-track {
		background: #00000014;
	}

	/* Handle */
	form#support_ticketform::-webkit-scrollbar-thumb {
		background: #8888887a;
		border-radius: 25px;
	}

	/* Handle on hover */
	form#support_ticketform::-webkit-scrollbar-thumb:hover {
		background: #555;
	}

	.close-btn {
		font-size: 24px;
		transition: 0.3s ease;
		cursor: pointer;
		line-height: 1;
	}

	.close-btn:hover {
		color: red;
	}

	@media only screen and (max-width: 767px) {
		img.msg_btn.circle {
			display: none;
		}
	}
</style>
<footer class="main-footer">
	<div class="pull-right d-none d-sm-inline-block">
		<?php if (!is_admin() && auth('role') != 'viewer'): ?>
			<?php if (check_payment_status() == TRUE || settings()->enable_paypal == 0 || user()->user_type == 'trial'): ?>
				<div id="floating-container" style="display:none">
					<div class="circle1 circle-blue1"></div>
					<div class="floating-menus" style="display:none;">
						<?php if (check_permissions(auth('role'), 'invoices') == TRUE): ?>
							<div>
								<a href="<?php echo base_url('admin/invoice/create') ?>"> <?php echo trans('create-new-invoice') ?>
									<i class="fa fa-file-text-o"></i></a>
							</div>
						<?php endif ?>

						<?php if (check_permissions(auth('role'), 'estimates') == TRUE): ?>
							<div>
								<a href="<?php echo base_url('admin/estimate/create') ?>"> <?php echo trans('create-new-estimate') ?>
									<i class="fa fa-file-text"></i></a>
							</div>
						<?php endif ?>

						<?php if (check_permissions(auth('role'), 'bills') == TRUE): ?>
							<div>
								<a href="<?php echo base_url('admin/bills/create') ?>"><?php echo trans('create-new-bill') ?>
									<i class="fa fa-file-text-o"></i></a>
							</div>
						<?php endif ?>

						<?php if (check_permissions(auth('role'), 'customers') == TRUE): ?>
							<div>
								<a href="<?php echo base_url('admin/customer') ?>"><?php echo trans('add-customer') ?>
									<i class="fa fa-user-o"></i></a>
							</div>
						<?php endif ?>

						<div>
							<a href="<?php echo base_url('admin/vendor') ?>"><?php echo trans('add-vendor') ?>
								<i class="fa ti-user"></i></a>
						</div>
					</div>
					<div class="fab-button">
						<i class="ti-plus" aria-hidden="true"></i>
					</div>
				</div>
			<?php endif ?>
		<?php endif ?>
	</div>
	<?php if (!is_admin() && auth('role') != 'viewer'): ?>
		<div class="pull-right" id="msg_open" style="cursor:pointer;">
			<img class="msg_btn circle pulse" src="<?php echo base_url('assets/admin/support1.png') ?>" alt="Jesse Tino">
		</div>
	<?php endif ?>
	<div class="avenue-messenger animate-btmup" id="msg" style="display: none;">
		<div class="box-body"
			style="padding: 12px 25px; border-top-left-radius: inherit; border-top-right-radius: inherit; background: #136acd; color: white">
			<div class="row justify-content-between align-items-center">
				<div class="col-10">
					<h4 style="margin-bottom: 0px; color: white">Customer Support</h4>
				</div>
				<div class="col-1 close-btn pull-right"
					onclick="return document.getElementById('msg').style.display='none';">x</div>
			</div>
		</div>
		<a href="javascript:void(0);" id="QueryModalOpen">
			<div class="box-body" style="padding: 25px; border-bottom: 1px solid #0000001f">
				<div class="row justify-content-between">
					<div class="col">
						<h5><img src="<?php echo base_url('assets/admin/query.png') ?>">&nbsp; Have a Query!</h5>
						<p style="color: #4d6575">Facing any issues with our product or looking for something
							specific?</p>
					</div>
					<div class="pull-right">
						<i class="fa fa-angle-right pull-right"></i>
					</div>
				</div>
			</div>
		</a>
		<a href="javascript:void(0);" id="CallreqModalOpen">
			<div class="box-body" style="padding: 25px; border-bottom: 1px solid #0000001f">
				<div class="row justify-content-between">
					<div class="col">
						<h5><img src="<?php echo base_url('assets/admin/call-back.png') ?>">&nbsp;Request a Call
							Back!</h5>
						<p style="color: #4d6575">We will call you back within 24 hrs.</p>
					</div>
					<div class="pull-right">
						<i class="fa fa-angle-right pull-right"></i>
					</div>
				</div>
			</div>
		</a>
	</div>
</footer>


<div class="modal fade" id="QueryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
	aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-zoom modal-md">
		<form id="customer-form" method="post" enctype="multipart/form-data" class="validate-form"
			action="<?php echo base_url('admin/invoice/ajax_add_customer') ?>" role="form" novalidate>
			<div class="tab-content" style="padding: 2%">
				<div class="tab-pane in active">
					<div class="modal-content modal-md">
						<div class="modal-header">
							<h4 class="modal-title" id="vcenter">Existing Queries</h4>
							<button type="button" class="close" data-dismiss="modal"
								aria-hidden="true">×</button>
						</div>
						<div class="modal-body" id="existing_query">

						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="new_query" class="modal fade" role="dialog" aria-labelledby="vcenter" aria-hidden="true" data-backdrop="static"
	data-keyboard="false">
	<div class="modal-dialog modal-dialog-zoom modal-md">
		<div class="modal-content modal-md">
			<div class="modal-header">
				<h4 class="modal-title" id="vcenter">Raise New Query</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<!--<a href="javascript:void(0);" id="back_to_existquery" class="btn btn-info waves-effect"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;Back</a>-->
				<br>
				<br>
				<div class="row align-items-center">
					<div class="col-12">
						<h4>Select a Topic for your query</h4>
					</div>
				</div>
				<br>
				<div class="query_category ticket_category">

				</div>
			</div>
		</div>
	</div>
</div>

<div id="new_sub_query" class="modal fade" role="dialog" aria-labelledby="vcenter" aria-hidden="true"
	data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-zoom modal-md">
		<div class="modal-content modal-md">
			<div class="modal-header">
				<h4 class="modal-title" id="vcenter">Raise New Query</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">

				<a href="javascript:void(0);" id="back_to_newquery" class="btn btn-info waves-effect"><i
						class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;Back</a>
				<br>
				<br>
				<div class="row align-items-center">
					<div class="col-12">
						<h4>Select a Topic for your query</h4>
					</div>
				</div>
				<br>
				<div class="query_category ticket_subcategory" style="padding: 0">

				</div>
			</div>
		</div>
	</div>
</div>


<div id="new_query_form" class="modal fade" role="dialog" aria-labelledby="vcenter" aria-hidden="true"
	data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-zoom modal-md">
		<div class="modal-content modal-md">
			<div class="modal-header">
				<h4 class="modal-title" id="vcenter">Raise New Query</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<a href="javascript:void(0);" id="back_to_newquery_form" class="btn btn-info waves-effect"><i
						class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;Back</a>
				<a href="javascript:void(0);" id="back_to_newquery1" class="btn btn-info waves-effect"
					style="display:none;"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;Back</a>

				<br>
				<br>
				<div class="box box-body" style="padding: 25px;">
					<div class="row align-items-start justify-content-between">
						<div class="col-lg-7">
							<h5 id="cat_name"></h5>
							<p id="subcat_name"></p>
						</div>
						<div class="col-lg-5">
							<a href="javascript:void(0);" id="back_to_newquery_form1"
								class="btn btn-info waves-effect pull-right">Change Topic</a>
							<a href="javascript:void(0);" style="display:none;" id="back_to_newquery2"
								class="btn btn-info waves-effect pull-right">Change Topic</a>
						</div>
					</div>
				</div>
				<h4 class="modal-title" style="margin-bottom: 20px">How can we help?</h4>
				<form id="support_ticketform" class="validate-form" method="post" enctype="multipart/form-data"
					role="form" novalidate>
					<input type="hidden" name="cat_id" id="cat_id" value="">
					<input type="hidden" name="subcat_id" id="subcat_id" value="">
					<div class="form-group">
						<textarea id="summernote" class="form-control" name="query_msg" aria-invalid="false"
							placeholder="Write a brief description of our query" style="height: 110px;"
							required></textarea>
					</div>
					<div class="form-group">
						<div class="form-control">
							<div class="file-edit">
								<input type="file" name="photo1" id="fileUpload" accept=".png, .jpg, .jpeg"
									multiple>
								<label for="fileUpload" style="width: 100%" class="text-center"><i
										class="fa fa-cloud-upload"></i>&nbsp;Click here to upload</label>
							</div>
						</div>
					</div>
					<div class="file-upload-preview row box-body">

					</div>
					<div class="text-right">
						<a href="javascript:void(0);" id="storeadd" class="btn btn-info waves-effect">Submit</a>
						<a href="javascript:void(0);" id="showstoreadd" style="display:none;"
							class="btn btn-info waves-effect">
							<div class="spinner-sm" style="margin: 0px;"></div> Loading...
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div id="call_back_form" class="modal fade" role="dialog" aria-labelledby="vcenter" aria-hidden="true"
	data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-zoom modal-md">
		<div class="modal-content modal-md">
			<div class="modal-header">
				<h4 class="modal-title" id="vcenter">Request a Call Back</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<form id="callback_from" action="<?php echo base_url() . 'admin/ticket/submit_request'; ?>"
					method="post">
					<div class="form-group validate">
						<label>Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" required="" name="name" value=""
							aria-invalid="false">
					</div>
					<div class="form-group validate">
						<label>Contact No. <span class="text-danger">*</span></label>
						<input type="number" class="form-control" required="" name="contact_no" value=""
							aria-invalid="false">
					</div>
					<div class="form-group validate">
						<label>Email Id <span class="text-danger">*</span></label>
						<input type="email" class="form-control" required="" name="email" value=""
							aria-invalid="false">
					</div>
					<div class="form-group validate">
						<label>Your Query</label>
						<textarea class="form-control" name="query_mss" aria-invalid="false"
							placeholder="Write a brief description of our query"
							style="height: 110px;"></textarea>
					</div>
					<div class="form-group">
						<label>Select call-back time</label>
						<div class="row align-items-center">
							<div class="col-lg-6">
								<input type="datetime-local" class="form-control" name="time" value="" id="time"
									autocomplete="off">
							</div>
							<div class="col-lg-1">
								OR
							</div>
							<div class="col-lg-5">
								<input type="checkbox" id="any_time" class="filled-in chk-col-blue view_only_1"
									value="any time" name="any_time" aria-invalid="false">
								<label for="any_time"> Any time</label>
							</div>
						</div>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-info waves-effect" value="Submit">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="versionModal" class="modal fade" role="dialog" aria-labelledby="vcenter" aria-hidden="true"
	data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-zoom modal-md">
		<div class="modal-content modal-md">
			<div class="modal-header">
				<h4 class="modal-title" id="vcenter">Upgrade Version</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<form id="version_from" action="<?php echo base_url() . 'admin/users/update_version'; ?>"
					method="post">
					<div class="form-group validate">
						<label>Version</label>
						<input type="text" class="form-control" name="version"
							value="<?php echo html_escape(settings()->version) ?>" aria-invalid="false">
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-info waves-effect" value="Submit">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php include 'js_msg_list.php'; ?>

<div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->


<?php 
$success = ''; 
$error = ''; 
if(isset($_SESSION['error'])){
	$success = $this->session->flashdata('msg'); 
	$error = $this->session->flashdata('error'); 
	unset($_SESSION['error']);
	unset($_SESSION['msg']);
	unset($_SESSION['gst_error']);
	unset($_SESSION['post_data']);
}
?>
<input type="hidden" id="success" value="<?php echo html_escape($success); ?>">
<input type="hidden" id="error" value="<?php echo html_escape($error); ?>">
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="browser" value="<?php echo $this->agent->browser(); ?>">

<!-- jQuery 3 -->
<?php if (strlen(settings()->ind_code) == 40): ?>
	<script src="<?php echo base_url() ?>assets/admin/js/jquery3.min.js"></script>
<?php endif ?>
<!-- popper -->
<script src="<?php echo base_url() ?>assets/admin/js/popper.min.js"></script>
<!-- Bootstrap -->
<script src="<?php echo base_url() ?>assets/admin/js/bootstrap.min.js"></script>
<!-- Custom js -->
<script
	src="<?php echo base_url() ?>assets/admin/js/admin.js?var=<?php echo settings()->version ?>&time=<?= time(); ?>"></script>
<script src="<?php echo base_url() ?>assets/admin/js/invoice.js"></script>

<script src="<?php echo base_url() ?>assets/admin/js/pdfmake.min.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/html2canvas.min.js"></script>

<script src="<?php echo base_url() ?>assets/admin/js/toast.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/bootstrap-tagsinput.min.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/sweet-alert.min.js"></script>
<!-- Datatables-->
<script src="<?php echo base_url() ?>assets/admin/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/dataTables.bootstrap.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/validation.js"></script>

<script src="<?php echo base_url() ?>assets/admin/js/jquery.slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/plugins/ckeditor/ckeditor.js"></script>

<script src="<?php echo base_url() ?>assets/admin/js/fastclick.js"></script>

<script src="<?php echo base_url() ?>assets/admin/js/template.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/bootstrap-datepicker.min.js"></script>

<script src="<?php echo base_url() ?>assets/admin/js/demo.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/select2.min.js"></script>
<!--<script src="<?php echo base_url() ?>assets/admin/js/jquery.invoice.js"></script>-->

<script src="<?php echo base_url() ?>assets/admin/js/wow.min.js"></script>

<?php if (isset($main_page) && $main_page == 'Report'): ?>
	<!-- datatable export buttons -->
	<script src="<?php echo base_url() ?>assets/admin/js/export_buttons/buttons.min.js"> </script>
	<script src="<?php echo base_url() ?>assets/admin/js/export_buttons/buttons.flash.min.js"> </script>
	<script src="<?php echo base_url() ?>assets/admin/js/export_buttons/jszip.min.js"> </script>
	<script src="<?php echo base_url() ?>assets/admin/js/export_buttons/pdfmake.min.js"> </script>
	<script src="<?php echo base_url() ?>assets/admin/js/export_buttons/vfs_fonts.js"> </script>
	<script src="<?php echo base_url() ?>assets/admin/js/export_buttons/buttons.html5.min.js"> </script>
	<script src="<?php echo base_url() ?>assets/admin/js/export_buttons/buttons.print.min.js"> </script>
<?php endif ?>

<script src="<?php echo base_url() ?>assets/admin/js/bootstrap4-toggle.min.js"> </script>
<script src="<?php echo base_url() ?>assets/admin/js/summernote.js"> </script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>

<?php $this->load->view('include/stripe-js'); ?>


<!-- datatable export buttons -->
<script type="text/javascript">
	$(document).ready(function () {
		$(function () {
			$(".ac-textarea").on("keyup input", function () {
				$(this).css('height', 'auto').css('height', this.scrollHeight +
					(this.offsetHeight - this.clientHeight));
			});
		});

		$("#summernote").summernote({
			height: 100,
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'italic', 'underline']],
				['fontname', ['fontname']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ol', 'ul', 'paragraph']],
				['table', ['table']],
				['insert', ['link']]
			]
		});

		$('.dt_btn').DataTable({
			dom: 'Bfrtip',
			buttons: [
				'copy', 'csv', 'excel', 'pdf', 'print'
			]
		});
		$('.dt_btn1').DataTable({
			dom: 'Bfrtip',
			buttons: [{
				extend: 'pdf',
				title: 'Accountieons Loan Statistic',
				exportOptions: {
					columns: "thead th:not(.noExport)"
				}
			}, {
				extend: 'excel',
				title: 'Accountieons Loan Statistic',
				exportOptions: {
					columns: "thead th:not(.noExport)"
				}
			}, {
				extend: 'csv',
				title: 'Accountieons Loan Statistic',
				exportOptions: {
					columns: "thead th:not(.noExport)"
				}
			}, {
				extend: 'print',
				title: 'Accountieons Loan Statistic',
				exportOptions: {
					columns: "thead th:not(.noExport)"
				}
			}
			]
		});
	});
</script>

<?php if (isset($page_title) && $page_title == 'Invoice Customization'): ?>
	<script>
		$(document).ready(function () {
			$("#summernote").show();
		});
	</script>
<?php endif ?>

<?php if (isset($page_title) && $page_title == 'User Dashboard'): ?>
	<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
	<script>
		$(document).ready(function () {
			$(".sortables").sortable({
				placeholder: "ui-state-highlight"
			});
			$(".sortables").disableSelection();

			$("#sortables").sortable({
				placeholder: "ui-state-highlight"
			});
			$("#sortables").disableSelection();
		});
	</script>
<?php endif ?>

<script src="<?php echo base_url() ?>assets/admin/js/printThis.js"></script>
<!-- Color Picker Plugin JavaScript -->
<script
	src="<?php echo base_url() ?>assets/admin/plugins/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>


<!-- bt-switch -->
<script src="<?php echo base_url() ?>assets/admin/js/bootstrap-switch.min.js"></script>
<script type="text/javascript">
	$(".bt-switch input[type='checkbox'], .bt-switch input[type='radio']").bootstrapSwitch();
	var radioswitch = function () {
		var bt = function () {
			$(".radio-switch").on("switch-change", function () {
				$(".radio-switch").bootstrapSwitch("toggleRadioState")
			}), $(".radio-switch").on("switch-change", function () {
				$(".radio-switch").bootstrapSwitch("toggleRadioStateAllowUncheck")
			}), $(".radio-switch").on("switch-change", function () {
				$(".radio-switch").bootstrapSwitch("toggleRadioStateAllowUncheck", !1)
			})
		};
		return {
			init: function () {
				bt()
			}
		}
	}();
	$(document).ready(function () {
		radioswitch.init()
	});


</script>


<!-- Style switcher -->
<!-- <script src="<?php echo base_url() ?>assets/admin/js/jQuery.style.switcher.js"></script> -->

<script type="text/javascript">
	<?php if (isset($success) && !empty($success)): ?>
		$(document).ready(function () {
			var msg = $('#success').val();
			var msg_success = $('.msg_success').val();

			$.toast({
				heading: msg_success,
				text: msg,
				position: 'top-right',
				loaderBg: '#fff',
				icon: 'success',
				hideAfter: 8000
			});

		});
	<?php endif; ?>
	<?php if (isset($error) && !empty($error)): ?>
		$(document).ready(function () {
			var msg = $('#error').val();
			var msg_error = $('.msg_error').val();

			$.toast({
				heading: msg_error,
				text: msg,
				position: 'top-right',
				loaderBg: '#fff',
				icon: 'error',
				hideAfter: 8000
			});

		});
	<?php endif; ?>
</script>

<script>
	! function (window, document, $) {
		"use strict";
		$("input,select,textarea").not("[type=submit]").jqBootstrapValidation();
	}(window, document, jQuery);

	$(document).ready(function () {
		$('.datatable').dataTable();
		$('.multiple_select').select2();
		$('.single_select').select2();
	});
</script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript">
	jQuery('.datepicker').datepicker({
		dateFormat: 'yy-mm-dd'
	});

	//colorpicker start
	$('.colorpicker-default').colorpicker({
		format: 'hex'
	});
	$('.colorpicker-rgba').colorpicker();

</script>


<script type="text/javascript">
	$(function () {
		$(".edit_emi_date").datepicker(
			{
				changeMonth: false,
				changeYear: false,
				stepMonths: false,
				dateFormat: 'yy-mm-dd'
			});

		$(".invoice_due_date").datepicker(
			{
				dateFormat: 'yy-mm-dd'
			});
	});
</script>

<!-- <script>
	CKEDITOR.replace('ckEditor', {
	language: 'en',
	filebrowserImageUploadUrl: "<?php //echo base_url(); ?>admin/post/upload_ckimage_post?key=kgh764hdj990sghsg46r"
	});
</script> -->

<?php if (isset($page_sub) && $page_sub == 'Edit'): ?>
	<script type="text/javascript">
		$(document).ready(function () {
			var Id = $('#customer').val();
			var base_url = $('#base_url').val();
			if (Id != '') {
				var url = base_url + 'admin/customer/load_customer_info/' + Id;
				$.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function (json) {
					if (json.st == 1) {
						$('#load_info').html(json.value);
						$('.currency_wrapper').html($('.c_currency_symbol').val());
						$('.currency_name').html(json.currency_name);
						$('.currency_code').val(($('.currency_code').val() == '') ? json.code : $('.currency_code').val());
						$.each($("#country_change option"), function () {
							if ($('.currency_code').val() == $(this).val()) {
								$(this).attr('selected', true);
								$('#select2-country_change-container').html($(this).text());
							}
						});
					}
				}, 'json');
			} else {
				$('.currency_wrapper').html('');
				// $('#load_info').html('Select a customer');
			}
		});
	</script>
<?php endif ?>

<?php if (isset($page_title) && $page_title == 'Loan Details'): ?>
	<script type="text/javascript">
		$(document).ready(function () {
			$('.toggle-on').text('Paid');
			$('.toggle-off').text('UnPaid');
		});
	</script>
<?php endif ?>

<?php if (isset($page_sub) && $page_sub == 'Edit Bill'): ?>
	<script type="text/javascript">
		$(document).ready(function () {
			var Id = $('#vendors').val();
			var base_url = $('#base_url').val();
			if (Id != '') {
				var url = base_url + 'admin/vendor/load_customer_info/' + Id;
				$.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function (json) {
					if (json.st == 1) {
						$('#load_info').html(json.value);
						$('.currency_wrapper').html(json.currency);
						$('.currency_name').html(json.currency_name);
						$('.currency_code').val(json.code);
					}
				}, 'json');
			} else {
				$('.currency_wrapper').html('');
				$('#load_info').html('Select a vendor');
			}
		});

	</script>
<?php endif ?>


<?php if (isset($page) && $page == 'Invoice' || isset($page) && $page == 'Create' || isset($page) && $page == 'Bill'): ?>
	<script type="text/javascript">
		$('.card-box').on("click", function () {
			var base_url = $('#base_url').val();
			var total = $('.grandtotal').val();
			var code = $('.currency_code').val();
			var url = base_url + 'admin/invoice/convert_currency/' + total + '/' + code;
			$.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function (json) {

				if (json.st == 1) {
					$('.conversion_currency').html(json.result);
					$('.convert_total').val(json.convert_total);
					$('.c_rate').val(json.rate);
				}
			}, 'json');
		});
	</script>
<?php endif ?>

<script>
	$(document).ready(function () {
		$(".next_bill").click(function () {
			$('.cus_contact').removeClass('active');
			$('.cus_address').addClass('active');
			$('#cus_contact').removeClass('in active');
			$('#cus_address').addClass('in active');

		});

		$(".prev_bill").click(function () {
			$('.cus_address').removeClass('active');
			$('.cus_contact').addClass('active');
			$('#cus_address').removeClass('in active');
			$('#cus_contact').addClass('in active');
		});

		$("#main_bal").keyup(function () {
			var main_bal = $('#span_main').html();
			var card_upd_bal = $('#span_current').html();
			var newcard_limit = $(this).val();
			if (newcard_limit != "" && newcard_limit > 0) {
				var carlb = parseFloat(main_bal) - parseFloat(card_upd_bal);
				var cardup = parseFloat(newcard_limit) - carlb;
				$('#card_upd_bal').val(cardup.toFixed(2));
			}

		});
	});

	function update_cardcurrent(obj) {
		$('#card_ope_bal').val(obj);
	}

</script>

<script>

	$(document).ready(function () {
		$("#msg_open").click(function () {
			$("#msg").toggle();
		});

		$("#QueryModalOpen").click(function (e) {
			e.preventDefault();
			$('#msg').hide();
			$.get("<?php echo base_url() . 'admin/ticket/get_existing_query'; ?>", function (data) {
				$('#existing_query').html(data);
				$('#QueryModal').modal('show');
			});
		});

		$(document).on('click', "#new_queryOpen", function (e) {
			e.preventDefault();
			$.get("<?php echo base_url() . 'admin/ticket/getAllcategory'; ?>", function (data) {
				$('.ticket_category').html(data);
				$('#QueryModal').modal('hide');
				$('#new_query').modal('show');
			});
		});

		$("#back_to_existquery").click(function () {
			$('#new_query').modal('hide');
			$('#QueryModal').modal('show');
		});

		$(document).on('click', '.new_sub_queryOpen', function () {
			var cat_id = $(this).attr('data-id');
			var cat_name = $(this).find('h5').text();

			$.post("<?php echo base_url() . 'admin/ticket/getAllsubcategory'; ?>", { cat_id: cat_id, 'csrf_test_name': csrf_token }, function (json) {
				if (json != "") {
					$('.ticket_subcategory').html(json);
					$('#new_query').modal('hide');
					$('#new_sub_query').modal('show');
				}
				else {
					$('#cat_id').val(cat_id);
					$('#cat_name').text(cat_name);
					$('#subcat_name').text('');
					$('#subcat_id').val('');
					$('#new_query').modal('hide');
					$('#new_sub_query').modal('hide');
					$('#new_query_form').modal('show');
					$('#back_to_newquery_form').hide();
					$('#back_to_newquery1').show();
					$('#back_to_newquery_form1').hide();
					$('#back_to_newquery2').show();
				}
			});
		});

		$("#back_to_newquery").click(function () {
			$('#new_sub_query').modal('hide');
			$('#new_query').modal('show');
		});

		$("#back_to_newquery1").click(function () {
			$('#new_query_form').modal('hide');
			$('#new_query').modal('show');
			$('#back_to_newquery_form').show();
			$('#back_to_newquery1').hide();
		});
		$("#back_to_newquery2").click(function () {
			$('#new_query_form').modal('hide');
			$('#new_query').modal('show');
			$('#back_to_newquery_form1').show();
			$('#back_to_newquery2').hide();
		});

		$(document).on('click', '.new_query_formOpen', function () {
			var both_id = $(this).attr('data-id');
			$.post("<?php echo base_url() . 'admin/ticket/getcatandsub'; ?>", { both_id: both_id, 'csrf_test_name': csrf_token }, function (json) {
				$('#cat_name').text(json.data.cat_name);
				$('#subcat_name').text(json.data.subcat_name);
				$('#cat_id').val(json.data.cat_id);
				$('#subcat_id').val(json.data.id);
				$('#new_sub_query').modal('hide');
				$('#new_query_form').modal('show');
			}, 'json');
		});

		$("#back_to_newquery_form").click(function () {
			$('#new_query_form').modal('hide');
			$('#new_sub_query').modal('show');
		});
		$("#back_to_newquery_form1").click(function () {
			$('#new_query_form').modal('hide');
			$('#new_sub_query').modal('show');
		});

		$("#CallreqModalOpen").click(function () {
			$('#msg').hide();
			$('#call_back_form').modal('show');
		});

	});

	var selectedImages = [];
	$("#fileUpload").on("change", function (e) {
		var files = Array.from(e.target.files)
		filesLength = files.length;
		for (var i = 0; i < filesLength; i++) {
			var f = files[i];
			selectedImages.push(files[i]);
			var fileReader = new FileReader();
			fileReader.onload = (function (e) {
				var file = e.target;
				$(".file-upload-preview").append("<span class=\"pip\">" +
					"<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/ width='75' height='75'>" +
					"<span class=\"remove\"  onclick='remove_image(" + i + ")'> <i class=\"fa fa-trash\"></i> </span>" +
					"</span>");

				$(".remove").click(function () {
					$(this).parent(".pip").remove();
				});
			});

			fileReader.readAsDataURL(f);
		}
	});

	function remove_image(a) {
		selectedImages.splice(0, 1);
	}

	$('#storeadd').on('click', function (e) {
		e.preventDefault();
		var msg_success = $('.msg_success').val();
		$(this).hide();
		$('#showstoreadd').show();
		$('#storeadd').prop('disabled', true);
		var formData = new FormData($('#support_ticketform')[0]);
		for (var a = 0; a < selectedImages.length; a++) {
			formData.append("file_image[]", selectedImages[a]);
		}
		formData.append("csrf_test_name", csrf_token);

		$.ajax({
			url: "<?php echo base_url() . 'admin/ticket/submit_ticket'; ?>",
			type: "POST",
			dataType: "JSON",
			data: formData,
			contentType: false,
			processData: false,
			beforeSend: function () {
			},
			success: function (data) {
				$('#new_query_form').modal('hide');
				$.toast({
					heading: msg_success,
					text: data.msg,
					position: 'top-right',
					loaderBg: '#fff',
					icon: 'success',
					hideAfter: 5000
				});

			}
		});
	});

	$(document).on('click', '.addnewpostajax', function () {
		var summernote = $("#summernote").val();
		if (summernote == '') {
			alert("Please Enter The Message.");
			return false;
		}

		var formData = new FormData($('#sendform')[0]);

		formData.append("csrf_test_name", csrf_token);
		$.ajax({
			type: "post",
			url: "<?php echo base_url('admin/ticket/send_reply'); ?>",
			data: formData,
			contentType: false,
			processData: false,
			success: function (data) {
				location.reload(true);
			}
		});
	});

	$(document).on('change', '#query_status', function () {
		var id = $(this).val();
		$.ajax({
			type: "post",
			url: "<?php echo base_url('admin/ticket/update_status'); ?>",
			data: { id: id, 'csrf_test_name': csrf_token },
			success: function (data) {
				$.toast({
					heading: msg_success,
					text: data,
					position: 'top-right',
					loaderBg: '#fff',
					icon: 'success',
					hideAfter: 5000
				});
			}
		});
	});

	$(document).on('change', '#any_time', function () {
		if ($(this).prop("checked") == true) {
			$('#time').val('');
		}
	});

	$('#callback_from').ajaxForm({
		success: function (data) {
			var msg_success = $('.msg_success').val();
			$('#call_back_form').modal('hide');
			$.toast({
				heading: msg_success,
				text: data,
				position: 'top-right',
				loaderBg: '#fff',
				icon: 'success',
				hideAfter: 5000
			});
		}
	});

	$(document).on('change', '#close_request', function () {
		var msg_success = $('.msg_success').val();
		var id = $(this).attr('data-id');
		if ($(this).prop("checked") == true) {
			var status = 1;
		}
		else {
			var status = 0;
		}

		$.ajax({
			type: "post",
			url: "<?php echo base_url('admin/ticket/update_request_status'); ?>",
			data: { id: id, status: status, 'csrf_test_name': csrf_token },
			success: function (data) {
				$.toast({
					heading: msg_success,
					text: data,
					position: 'top-right',
					loaderBg: '#fff',
					icon: 'success',
					hideAfter: 5000
				});
			}
		});
	});

	$(document).on('change', '#dark_mode', function () {
		if ($(this).prop("checked") == true) {
			var status = 1;
		}
		else {
			var status = 2;
		}
		$.ajax({
			type: "post",
			url: "<?php echo base_url('admin/dashboard/update_darkmode'); ?>",
			data: { status: status, 'csrf_test_name': csrf_token },
			success: function (data) {
				location.reload();
			}
		});
	});

	$(document).on('click', '.upgrade_version', function () {
		$('#versionModal').modal('show');
	});

	$('#version_from').ajaxForm({
		success: function (data) {
			var msg_success = $('.msg_success').val();
			$('#versionModal').modal('hide');
			$.toast({
				heading: msg_success,
				text: data,
				position: 'top-right',
				loaderBg: '#fff',
				icon: 'success',
				hideAfter: 5000
			});
		}
	});

	$(document).ready(function () {
		$('#loan_amount,#roi,#emi_tenure').keyup(function () {
			loan_emi()
		});
		$('#intrest_type').change(function () {
			loan_emi()
		});
	});

	function loan_emi() {
		var p = $('#loan_amount').val();
		var roi = $('#roi').val();
		var n = $('#emi_tenure').val();
		var type = $('#intrest_type').val();

		if (p != '' && roi != '' && n != '' && type != '') {
			var r = roi / (12 * 100);
			if (type == 1) {
				// flat or fixed intrest 
				var per_annum = n / 12;
				var interest = (p * roi) / 100;
				var interest = interest * per_annum;

				var total_p = parseFloat(p) + parseFloat(interest);

				var total = total_p / n;
				$('#emi_amount').val(total.toFixed(2));
			}
			else {
				// Reducing fixed intrest 
				var emi = p * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
				$('#emi_amount').val(emi.toFixed(2));
			}

		}
	}
</script>
</body>

</html>