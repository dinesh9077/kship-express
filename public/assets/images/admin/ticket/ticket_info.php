<div class="content-wrapper">
	
	<!-- Main content -->
	<section class="content"> 
	    <div class="container">
	        <h2 class="box-title" style="font-size: 26px;"><i class="fa fa-ticket"></i>&nbsp;Support Ticket Information</h2>
	        <div class="box box-body" style="padding: 25px">
				<?php 
				if(is_admin()){ 
				?> 
	            <a href="<?php echo base_url('admin/ticket/view_all_ticket/'.$ticket_info->user_id)?>" class="btn btn-info waves-effect" style="width: fit-content"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;View All Tickets</a>
	            <?php 
				}else{
				?> 
				 <a href="<?php echo base_url('admin/ticket/view_all_ticket')?>" class="btn btn-info waves-effect" style="width: fit-content"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;View All Tickets</a>
				<?php 
				}
				?> 
	            <div class="box-body" style="border: 1px solid #d5d5d5; border-radius: 5px; padding: 0;margin-top:2%;">
	                <div class="query" style="border: 0">
	                    <p>
							<?php if($ticket_info->status == 1){ ?>
								<span class="badge badge-success pull-right">ACTIVE</span>
								<?php }else if($ticket_info->status == 0){ ?>
								<span class="badge badge-danger pull-right" style="color: #ffffff;">CLOSED</span>
							<?php } ?>
						</p>
                        <h5><?php echo $ticket_info->cat_name; ?> </h5>
						<?php  
							if(!empty($ticket_info->subcat_id))
							{
								$subcat = $this->db->where('id',$ticket_info->subcat_id)->get('ticket_subcategory')->row(); 
								echo '<h6>'.$subcat->subcat_name.'</h6>';
							} 
						?> 
                        <p style="color: #4d6575">Ticket #<?php echo $ticket_info->ticket_no; ?> - Raised <?php echo get_time_ago($ticket_info->created_at) ?></p>
					</div>
                    <div class="query_msg">
						<div class="box-body" style="padding: 25px">
							<div class="row">
								<div class="">
									<?php 
										if(!empty($ticket_info->image)): 
									?>  
									<img src="<?php echo base_url($ticket_info->image); ?>" style="border-radius: 50%; height: 70px; width: 70px; background: #f0f0f0;">
									<?php 
										else:
									?>
									<img src="<?php echo base_url('assets/images/avatar.png'); ?>" style="border-radius: 50%; height: 70px; width: 70px; background: #f0f0f0;">
									<?php 
										endif
									?> 
								</div>
								<div class="col">
									<h5 style="text-transform: capitalize"><?php echo $ticket_info->name; ?> <span class="pull-right"><?php echo get_time_ago($ticket_info->created_at) ?></span></h5>
									<p><?php echo $ticket_info->query_msg; ?></p>
									<?php if(!empty($ticket_info->images)): ?>
									<div class="row">
										<?php 
											$images = explode(',',rtrim($ticket_info->images, ','));
											foreach($images as $img):
										?>
										<div style="padding:1%">
											<a href="<?php echo base_url('uploads/ticket_image/'.$img); ?>" download ><img src="<?php echo base_url('uploads/ticket_image/'.$img); ?>" style="height: 70px; width: 70px; background: #f0f0f0;"></a>
										</div> 
										<?php endforeach ?>
										
									</div>
									<?php endif ?>
								</div>
							</div>
						</div>
						<?php 
							if(count($allmessages) > 0): 
							foreach($allmessages as $kry=>$allmessage):	
						?> 
						<hr>
						<div class="box-body" style="padding: 25px">
							<div class="row">
								<div class="">
									
									<?php
										if($allmessage->is_user == "admin")
										{  	  
									?> 	
											<img src="<?php echo base_url($settings->favicon) ?>" style="border-radius: 50%; height: 70px; width: 70px; background: #f0f0f0;"> 
										<?php  
										}
										else
										{
											if(!empty($allmessage->image)){ 
											?>  
											<img src="<?php echo base_url($allmessage->image); ?>" style="border-radius: 50%; height: 70px; width: 70px; background: #f0f0f0;">
											<?php 
												}else{
											?>
											<img src="<?php echo base_url('assets/images/avatar.png'); ?>" style="border-radius: 50%; height: 70px; width: 70px; background: #f0f0f0;">
											<?php 
											}
										}
									?> 
						</div>
						<div class="col">
							<h5 style="text-transform: capitalize"><?php echo ($allmessage->is_user == "admin")?$settings->site_name:$allmessage->name; ?> <span class="pull-right"><?php echo get_time_ago($allmessage->created_at) ?></span></h5>
							<p><?php echo $allmessage->reply_msg; ?></p>
							
						</div>
						</div>
						</div>
						<?php endforeach ?>
						<?php endif ?>
					</div>
					<?php if($ticket_info->status == 1) { ?>
						<form id="sendform" method="post">
							<input type="hidden" name="ticket_id" id="ticket_id" value="<?php echo $ticket_info->id; ?>">
							<input type="hidden" name="ticket_no" id="ticket_no" value="<?php echo $ticket_info->ticket_no; ?>">
							<textarea class="form-control" name="reply_msg" id="summernote" required></textarea>
							<button type="button" class="btn btn-info waves-effect pull-right addnewpostajax" style="margin: 1%">Send </button>
						</form>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>
</div>   