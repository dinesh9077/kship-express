<div class="content-wrapper">
	
	<!-- Main content -->
	<section class="content">  
		
        <div class="list_area container">
			<div class="row align-items-center">
			    <div class="col-md-6">
			        <h3 class="box-title">Login History</h3>          
			    </div>
			    <div class="col-md-6">
			        <div class="add-btn">
					<input type="radio">
                        <a href="javascript:void(0);" class="btn btn-info rounded btn-sm" data-id="today" id="today">Today</a>
                        <a href="javascript:void(0);" class="btn btn-default rounded btn-sm" data-id="last_week" id="last_week">Last Week</a>
                        <a href="javascript:void(0);" class="btn btn-default rounded btn-sm" data-id="last_month" id="last_month">Last Month</a>
                    </div>
			    </div>
			</div>
			<input type="hidden" name="filter_by" id="filter_by" value="today">
			<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0">
				<table class="table table-hover cushover dg_table">
					<thead>
						<tr>
							<th>#</th>
							<th>User Name</th>
							<th>Email</th>
							<th>Ip Address</th>
							<th>Attempt</th>
							<th>Login At</th>
							<th ><?php echo trans('action'); ?></th>
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
		var dataTable = $('.dg_table').DataTable({
			'processing': true, 
			'serverSide': true,
			'serverMethod': 'post',
			'ajax': {
				'url':"<?php echo base_url().'admin/users/get_login_history'; ?>", 
                "data": function (d) {  
					d.filter_by = $('#filter_by').val(); 
				} 
			},  
			'columns': [
			{ data: 'id' },
			{ data: 'name' },
			{ data: 'email' },
			{ data: 'ip_address' },
			{ data: 'attempt' },
			{ data: 'created_at' }, 
			{ data: 'action' } 
			]
		});
		
		$('#today').click(function (event){
			filter_by = $(this).attr('data-id');
			$('#filter_by').val(filter_by);
			$(this).removeClass('btn btn-default');
			$(this).addClass('btn btn-info');
			$('#last_week').removeClass('btn btn-info');
			$('#last_month').removeClass('btn btn-info');
			$('#last_week').addClass('btn btn-default');
			$('#last_month').addClass('btn btn-default');
			dataTable.draw();
			event.preventDefault();
		});
		$('#last_week').click(function (event){
			filter_by = $(this).attr('data-id');
			$('#filter_by').val(filter_by);
			$(this).removeClass('btn btn-default');
			$(this).addClass('btn btn-info');
			$('#today').removeClass('btn btn-info');
			$('#last_month').removeClass('btn btn-info');
			$('#today').addClass('btn btn-default');
			$('#last_month').addClass('btn btn-default');
			dataTable.draw();
			event.preventDefault();
		});
		$('#last_month').click(function (event){
			filter_by = $(this).attr('data-id'); 
			$('#filter_by').val(filter_by);
			$(this).removeClass('btn btn-default');
			$(this).addClass('btn btn-info');
			$('#last_week').removeClass('btn btn-info');
			$('#today').removeClass('btn btn-info');
			$('#last_week').addClass('btn btn-default');
			$('#today').addClass('btn btn-default');
			dataTable.draw();
			event.preventDefault();
		});
		
	});
</script>
