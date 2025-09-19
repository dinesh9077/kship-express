<div class="content-wrapper">
	
	<!-- Main content -->
	<section class="content">  
		 
        <div class="list_area container">
			  
			<h3 class="box-title">Page Views<a href="<?php echo base_url('admin/users/visitor'); ?>" class="btn btn-warning btn-sm pull-right">Back</a></h3>  
			<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0">
				<table class="table table-hover cushover <?php if(count($pageviews) > 10){echo "datatable";} ?>" id="dg_table">
					<thead>
						<tr>
							<th>#</th>
							<th>Ip Address</th>
							<th>Page Link</th>
							<th>Pageview</th>
							<th>Browser Type</th> 
							<th>Date Time</th>
						</tr>
					</thead>
					<tbody>
						<?php $i=1; foreach ($pageviews as $pageview): ?>
						<tr id="row_<?php echo html_escape($pageview->id); ?>">
							
							<td><?php echo $i; ?></td> 
							<td><?php echo html_escape($pageview->ip_address); ?></td>
							<td><?php echo html_escape($pageview->page_link); ?></td>
							<td><?php echo html_escape(round($pageview->total/2)); ?></td>
							<td><?php echo html_escape($pageview->browser_type); ?></td>
							<td><?php echo html_escape($pageview->page_view_datetime); ?></td>
							 
							<!--<td class="actions" width="20%">
								<a href="<?php echo base_url('admin/user/view_visitor/'.html_escape($visitor->ip_address));?>" class="on-default" data-placement="top" title="View Page View"><i class="fa fa-eye"></i></a> &nbsp;
								
								<a data-val="visitor" data-id="<?php echo html_escape($visitor->ip_address); ?>" href="<?php echo base_url('admin/user/delete_visitor/'.html_escape($visitor->ip_address));?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a> -->
							</td>
						</tr>
						
						<?php $i++; endforeach; ?>
					</tbody>
				</table>
			</div>
		</div> 
	</section>
</div>
