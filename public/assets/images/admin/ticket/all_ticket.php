<div class="content-wrapper">
	
	<!-- Main content -->
	<section class="content">  
		 
        <div class="list_area container">
			  
			<h3 class="box-title">All Tickets<a href="#" class="pull-right btn btn-info rounded btn-sm add_btn"><i class="fa fa-plus"></i> <?php echo trans('add-new-category') ?></a></h3>  
			<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0">
				<table class="table table-hover cushover <?php if(count($alltickets) > 10){echo "datatable";} ?>" id="dg_table">
					<thead>
						<tr>
							<th>#</th>
							<th>Image</th>
							<th><?php echo trans('name') ?></th>
							<th><?php echo trans('category') ?></th>
							<th>Total Active Ticket</th>
							<th><?php echo trans('status') ?></th>
							<th><?php echo trans('action') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $i=1; foreach ($alltickets as $allticket): ?>
						<tr id="row_<?php echo html_escape($allticket->id); ?>">
							
							<td><?php echo $i; ?></td>
							<td><img class="mr-5" src="<?php echo (!empty($allticket->image))? base_url($allticket->image):base_url('assets/images/avatar.png');?>" width="60px"></td>
							<td style="text-transform: capitalize"><?php echo html_escape($allticket->name); ?></td>
							<td><?php echo html_escape($allticket->cat_name); ?></td>
							<td><?php echo html_escape($allticket->num_ticket); ?></td>
							<?php if($allticket->status == "1"){ ?>
								<td><span class="badge badge-success">Active</span></td>
								<?php }else{ ?>
								<td><span class="badge badge-danger">In-Active</span></td>
							<?php } ?>
							<td class="actions" width="40%">
								<a href="<?php echo base_url('admin/ticket/view_all_ticket/'.html_escape($allticket->user_id));?>" class="btn btn-primary btn-sm" data-placement="top" title="Active Ticket">Active Ticket</a> &nbsp;
								<a href="<?php echo base_url('admin/ticket/view_close_history/'.html_escape($allticket->user_id));?>" class="btn btn-info btn-sm" data-placement="top" title="Close Ticket">Close Ticket</a> &nbsp;  
								<a href="<?php echo base_url('admin/ticket/all_callback_request/'.html_escape($allticket->user_id));?>" class="btn btn-warning btn-sm" data-placement="top" title="Close Ticket">Callback Request</a>
								<a data-val="Ticket" data-id="<?php echo html_escape($allticket->user_id); ?>" href="<?php echo base_url('admin/ticket/delete_ticket/'.html_escape($allticket->user_id));?>" class="btn btn-danger btn-sm remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete">Delete</a>
							</td>
						</tr>
						
						<?php $i++; endforeach; ?>
					</tbody>
				</table>
			</div>
		</div> 
	</section>
</div>
