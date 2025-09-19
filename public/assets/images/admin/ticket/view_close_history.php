<div class="content-wrapper">
	
	<!-- Main content -->
	<section class="content"> 
	    <div class="container">
	        <h2 class="box-title" style="font-size: 26px;"><i class="fa fa-ticket"></i>&nbsp;All Close Ticket History</h2>
	        <div class="box box-body" style="padding: 25px">
				
				<?php 
					if(is_admin()){ 
					?>  
				    <a href="<?php echo base_url('admin/ticket/admin_ticket')?>" class="btn btn-info waves-effect" style="width: fit-content"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;Back</a>
					<?php 
						}else{
					?> 
					<a href="<?php echo base_url('admin/ticket/view_all_ticket')?>" class="btn btn-info waves-effect" style="width: fit-content"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;Back</a>
					<?php 
					}
				?> 
				 
				<?php 
					if(count($close_ticket) > 0)
					{
						foreach($close_ticket as $key => $row)
						{
							$cat = $this->db->where('id',$row->cat_id)->get('ticket_category')->row();
						?> 
						<a href="<?php base_url('admin/ticket/ticket_info'); ?>">
							<div class="query-closed" style="margin-top: 2%">
								<span class="badge badge-danger pull-right" style="color: #ffffff;">CLOSED</span>
								<h5><?php echo $cat->cat_name; ?> </h5>
								<?php  
									if(!empty($row->subcat_id))
									{
										$subcat = $this->db->where('id',$row->subcat_id)->get('ticket_subcategory')->row(); 
										echo '<h6>'.$subcat->subcat_name.'</h6>';
									} 
								?> 
								<p style="color: #4d6575">Ticket #<?php echo $row->ticket_no; ?> - Raised <?php echo get_time_ago($row->created_at) ?> </p>
								<?php 
									$query_msg = substr($row->query_msg,0,172);
									$dot = '';
									if(strlen($query_msg) == '172')
									{
										$dot = '...';
									}
									echo '<p>'.$query_msg.$dot.'</p>';
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
