<div class="content-wrapper">
	
	<!-- Main content -->
	<section class="content">  
		
        <div class="list_area container">
			
			<h3 class="box-title">Visitors</h3>  
			<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0">
				<table class="table table-hover cushover dg_table <?php //if(count($visitors) > 10){echo "datatable";} ?>" id="">
					<thead>
						<tr>
							<th>#</th>
							<th>Ip Address</th>
							<th>State</th>
							<th>Country</th>
							<th>Lat Long</th>
							<th>Source</th>
							<th>Total Pageview</th>
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
	$(document).ready(function() 
	{
		$('.dg_table').DataTable({
			'processing': true, 
			'serverSide': true,
			'serverMethod': 'post',
			'ajax': {
				'url':"<?php echo base_url().'admin/users/get_visitor_count'; ?>"
			}, 
			"order": [[ 0, "desc" ]],
			'columns': [
			{ data: 'id' },
			{ data: 'ip_address' },
			{ data: 'state' },
			{ data: 'country' },
			{ data: 'lat_long' },
			{ data: 'source' },
			{ data: 'pagetotal'},
			{ data: 'action' }
			]
		});
		
	});
</script>
