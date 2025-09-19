<style>
	.query.bg-white {
		background: #fff !important;
	}

	.suppo_re h2,
	h4 {
		display: inline-block;
	}

	.my-cutn-12 {
		border-radius: 20px;
		border: 1px solid rgba(0, 0, 0, 0.20) !important;
		background: #FFF !important;
	}

	.custm-1 {
		background-color: #059b0e1c;
		padding: 6px 20px !important;
		border-radius: 6px;
		color: #059b0e;
		border: 1px solid;
	}

	.pull-right.full-v1 {
		color: #7334D9;
		background: #000;
		padding: 5px 20px;
		border-radius: 6px;
		border-radius: 7px;
		border: 1px solid #E3D2FF;
		background: #EDE1FF;
	}
</style>


<div class="content-wrapper">

	<!-- Main content -->
	<section class="content">
		<div class="">

			<div class="bg-light" style="padding: 0px 20px;">

				<div class="suppo_re d-flex justify-content-between align-items-center ">
					<div>
						<h2 class="box-title mr-5" style="font-size: 26px;"><i class="fa fa-ticket"></i>&nbsp;Support Ticket</h2>
						<!-- <?php if (count($active_ticket) > 0) { ?>
							<h4 class="mb-0">Open Query(<?php echo count($active_ticket); ?>)</h4>
						<?php } ?> -->
					</div>
					<div>
						<?php
						if (!is_admin()) {
						?>
							<a href="javscript:void(0);" id="new_queryOpen" class="btn btn-info waves-effect pull-right">+ Raise New Query</a>
						<?php
						} else {
						?>
							<a href="<?php echo base_url('admin/ticket/admin_ticket') ?>" class="btn btn-info waves-effect pull-right" style="width: fit-content"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;Back</a>
						<?php
						}
						?>
					</div>
				</div>
			</div>
			<?php
			if (count($active_ticket) > 0) {
				foreach ($active_ticket as $key => $val) {
					$cat = $this->db->where('id', $val->cat_id)->get('ticket_category')->row();
			?>

					<div class="query bg-white my-cutn-12" style="margin-top: 20px">
						<?php
						if (is_admin()) {
						?>
							<div class="form-group pull-right">
								<select class="form-control" name="query_status" id="query_status">
									<option value="1-<?php echo $val->id; ?>" <?php echo ($val->status == 1) ? "selected" : ""; ?>>Active</option>
									<option value="0-<?php echo $val->id; ?>" <?php echo ($val->status == 0) ? "selected" : ""; ?>>Closed</option>
								</select>
							</div>

						<?php } else { ?>
							<span class="badge badge-success custm-1 pull-right">ACTIVE</span>
						<?php } ?>

						<a href="<?php echo base_url('admin/ticket/ticket_info/' . md5($val->id)) ?>">
							<h5 style="color: #7334D9;"><?php echo $cat->cat_name; ?> </h5>
						</a>
						<?php
						if (!empty($val->subcat_id)) {
							$subcat = $this->db->where('id', $val->subcat_id)->get('ticket_subcategory')->row();
							echo '<h6>' . $subcat->subcat_name . '</h6>';
						}
						?>
						<p style="color: #4d6575">Ticket #<?php echo $val->ticket_no; ?> - Raised <?php echo get_time_ago($val->created_at) ?></p>
						<?php
						$query_msg = substr($val->query_msg, 0, 172);
						$dot = '';
						if (strlen($query_msg) == '172') {
							$dot = '...';
						}
						echo '<p style="color: black">' . $query_msg . $dot . '</p>';
						?>

						<hr>
						<p style="color: #4d6575"><i class="fa fa-clock-o"></i>&nbsp;Response expected before <strong><?php echo date('d M Y', strtotime($val->response_date)) ?></strong>
							<a href="<?php echo base_url('admin/ticket/ticket_info/' . md5($val->id)) ?>" class="pull-right full-v1">View</a>
						</p>
						<!--<p ></p>-->
					</div>
			<?php
				}
			}
			?>
			<?php
			if (count($close_ticket) > 0) {
				foreach (array_slice($close_ticket, 0, 3) as $key => $row) {
					$cat = $this->db->where('id', $row->cat_id)->get('ticket_category')->row();
					if ($key == 0) {
			?>

						<div class="row align-items-center" style="margin-top: 2%">
							<div class="col-12">
								<h4>Closed Query (<?php echo count($close_ticket); ?>) <a href="<?php echo base_url('admin/ticket/view_close_history'); ?>" class="pull-right">View All</a></h4>
							</div>
						</div>
					<?php } ?>
					<a href="<?php echo base_url('admin/ticket/ticket_info/' . md5($row->id)); ?>">
						<div class="query-closed" style="margin-top: 2%">
							<span class="badge badge-danger pull-right" style="color: #ffffff;">CLOSED</span>
							<h5><?php echo $cat->cat_name; ?> </h5>
							<?php
							if (!empty($row->subcat_id)) {
								$subcat = $this->db->where('id', $row->subcat_id)->get('ticket_subcategory')->row();
								echo '<h6>' . $subcat->subcat_name . '</h6>';
							}
							?>
							<p style="color: #4d6575">Ticket #<?php echo $row->ticket_no; ?> - Raised <?php echo get_time_ago($row->created_at) ?> </p>
							<?php
							$query_msg = substr($row->query_msg, 0, 172);
							$dot = '';
							if (strlen($query_msg) == '172') {
								$dot = '...';
							}
							echo '<p>' . $query_msg . $dot . '</p>';
							?>
						</div>
					</a>
			<?php
				}
			}
			?>
		</div>
</div>
</section>
</div>