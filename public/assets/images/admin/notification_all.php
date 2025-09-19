<div class="content-wrapper">
	
	<!-- Main content -->
	<section class="content container">
		
		<div class="list_area container">
			<h3 class="box-title"><?php echo $page_title; ?> <a href="javascript:void(0);" class="pull-right btn btn-info btn-sm" data-toggle="modal" data-target="#send_notification"><i class="fa fa-plus"></i> Send Notification</a></h3> 
			<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive p-0">
				<table class="table table-hover cushover <?php if(count($notifications) > 10){echo "datatable";} ?>" id="dg_table">
					<thead>
						<tr>
							<th>#</th>
							<th><?php echo trans('title') ?></th>
							<th>Image</th> 
							<th><?php echo trans('date') ?></th>
							<th><?php echo trans('action') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $i=1; foreach ($notifications as $notification): ?>
						<tr id="row_<?php echo html_escape($notification->id); ?>">
							
							<td><?php echo $i; ?></td>
							<td><?php echo html_escape($notification->title); ?></td>
							<td><?php echo html_escape($notification->image); ?></td> 
							<td><span class="label label-default"> <?php echo my_date_show_time($notification->created_at); ?> </span></td> 
							<td class="actions" width="5%">
								<a data-val="notification" data-id="<?php echo html_escape($notification->id); ?>" href="<?php echo base_url('admin/contact/notification_delete/'.html_escape($notification->id));?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a> &nbsp; 
							</td>
						</tr>
						
						<?php $i++; endforeach; ?>
					</tbody>
				</table>
			</div>  
		</div> 
	</section>
</div>
<div id="send_notification" class="modal fade" role="dialog" aria-labelledby="vcenter" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-zoom modal-md">
		<div class="modal-content modal-md">
			<div class="modal-header">
				<h4 class="modal-title" id="vcenter">Send Notification To All User</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<form action="<?php echo base_url().'admin/contact/sendnotification';?>" method="post" enctype="multipart/form-data">
				    <div class="form-group validate">
                        <label>Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" required="" name="title" value="" aria-invalid="false">
					</div> 
					<div class="form-group validate">
                        <label>Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="content" aria-invalid="false" placeholder="Write a brief description of our query" style="height: 110px;"></textarea>
					</div>   
                    <div class="form-group validate">
                        <label>Image </label>
                        <input type="file" class="form-control" name="file" value="" aria-invalid="false">
					</div> 
				    <div class="text-right">
				        <button type="submit" class="btn btn-info waves-effect" value="Submit">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>