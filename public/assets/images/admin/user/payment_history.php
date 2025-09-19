<div class="content-wrapper">

	<!-- Main content -->
	<section class="content">

		<div class="list_area container">

			<h3 class="box-title">Payment History</h3>
			<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0">
				<table class="table table-hover cushover dg_table">
					<thead>
						<tr>
							<th>#</th>
							<th>User Name</th>
							<th>Plan</th>
							<th>Billing Frequency</th>
							<th>Amount</th>
							<th>Create Date</th>
							<th>Expire On</th>
							<th>Status</th>
							<th><?php echo trans('action') ?></th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</section>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		$('.dg_table').DataTable({
			'processing': true,
			'serverSide': true,
			'serverMethod': 'post',
			'ajax': {
				'url': "<?php echo base_url() . 'admin/users/get_payment_history'; ?>"
			},
			"order": [
				[0, "desc"]
			],
			'columns': [{
					data: 'id'
				},
				{
					data: 'username'
				},
				{
					data: 'plan'
				},
				{
					data: 'billing_type'
				},
				{
					data: 'amount'
				},
				{
					data: 'created_at'
				},
				{
					data: 'expire_on'
				},
				{
					data: 'status'
				},
				{
					data: 'action'
				}
			]
		});

	});
</script>