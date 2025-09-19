<div class="content-wrapper">
  <!-- Main content -->
    <section class="content">
      <div class="list_area">

        <form class="user_sort_form" role="search" autocomplete="off" action="<?php echo base_url('admin/users') ?>" method="get">

            <div class="row">
                <div class="col-md-3 col-xs-12 mt-5 pl-15">
                  <h3 class="box-title"><?php echo trans('all-users') ?> </h3>
                </div>
                <div class="col-md-9 col-xs-12 mt-5 pl-15 text-right">
                  <label class="btn btn-info">Total Users: <?php echo $total_user; ?> </label>
                </div>
            </div>
                  
            <div class="row mt-12">  
            <div class="col-md-3 col-xs-12 mt-5"> 
              <div class="input-group"> 
                  <button type="submit" name="export_to_excel"  value="export_to_excel" class="btn btn-info"><i class="fa fa-file"></i> Export To Excel</button>
              </div>
            </div>
            <div class="col-md-2 col-xs-12 mt-5 pl-0">
              <select class="form-control user_sort" name="package">
                  <option value=""><?php echo trans('all-package') ?></option>
                  <?php foreach ($packages as $package): ?>
                    <option value="<?php echo html_escape($package->id) ?>" <?php echo(isset($_GET['package']) && $_GET['package'] == $package->id) ? 'selected' : ''; ?>
                    ><?php echo html_escape($package->name) ?></option>
                  <?php endforeach ?>
              </select>
            </div>
			
			 <div class="col-md-2 col-xs-12 mt-5 pl-0">
			 <div class="input-group"> 
                  <input type="text" class="form-control datepicker" placeholder="From Date" name="from_date" value="<?php if(isset($_GET['from_date'])){echo $_GET['from_date'];} ?>" autocomplete="off">  
              </div>
              </div>
          
		  <div class="col-md-2 col-xs-12 mt-5 pl-0">
              <div class="input-group"> 
                  <input type="text" class="form-control datepicker" placeholder="To Date" name="to_date" value="<?php if(isset($_GET['to_date'])){echo $_GET['to_date'];} ?>" autocomplete="off">  
              </div>
              </div>
            <div class="col-md-3 col-xs-12 mt-5 pl-0"> 
              <div class="input-group"> 
                  <input type="text" class="form-control" placeholder="<?php echo trans('search-by-name-email') ?> ..." name="search" value="<?php if(isset($_GET['search'])){echo $_GET['search'];} ?>" autocomplete="off"> 
                  <button type="submit" name="searchss" class="input-group-addon btn btn-secondary"><i class="fa fa-search"></i></button>
              </div>
            </div>
          </div>
        </form>
        
        <div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0">
          <table class="table table-hover cushover">
            <thead>
              <tr>
                <th>#</th>
                <th><?php echo trans('avatar') ?></th>
                <th><?php echo trans('name') ?></th>
                <th><?php echo trans('email') ?></th>
                <th><?php echo trans('contact') ?></th>
                <th>Country</th>
                <th><?php echo trans('package') ?></th>
                <th><?php echo trans('business') ?></th>
                <th>Business Type</th>
                <th>Invoice</th>
                <th>Estimate</th>
                <th><?php echo trans('payment-status') ?></th>
                <th><?php echo trans('join') ?></th>
                <th><?php echo trans('expire') ?></th>
                <th>From Mobile</th>
                <th><?php echo trans('action') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php $i=1; foreach ($users as $user): ?>
              <tr id="row_<?php echo html_escape($user->id); ?>">

                <td><?php echo $i; ?></td>
                <td>
                  <?php if ($user->thumb == ''): ?>
                    <?php $avatar = 'assets/images/avatar.png'; ?> 
                  <?php else: ?>
                    <?php $avatar = $user->thumb; ?>
                  <?php endif ?>
                  <img width="40px" class="img-circle" src="<?php echo base_url($avatar); ?>">
                </td>
               
                <td style="text-transform: capitalize;"><?php echo html_escape($user->name); ?></td>
                <td><?php echo html_escape($user->email); ?></td>
 
                <td>
                    <?php echo html_escape($user->phone); ?>
                </td> 
				 
				<td>
                  <?php if (empty($user->country)): ?>
                     <?php echo trans('not-found') ?>
                    <?php else: ?>
                      <?php echo $this->db->where('id',$user->country)->get('country')->row()->name; ?>
                    <?php endif; ?>
                </td>
				
				<td>
                    <?php if ($user->user_type != 'trial'): ?>
                      <span class="label label-info">
                          <?php echo get_user_payment_details($user->id)->package_name; ?>
                      </span>
                    <?php else:?>
						<span class="label label-warning">Trial</span>
                    <?php endif  ?>
                </td>
				<td>
                  <?php if (count($user->business) == 0): ?>
                      <p class="mt-10"><?php echo trans('not-found') ?></p>
                    <?php else: ?>
                      <?php foreach ($user->business as $business): ?>
                          <p class="mb-0"><i class="fa fa-long-arrow-right"></i> <?php echo html_escape($business->name) ?></p>
                      <?php endforeach; ?>
                    <?php endif; ?>
                </td>
                <td>
                  <?php if (count($user->business) == 0): ?>
                      <p class="mt-10"><?php echo trans('not-found') ?></p>
                    <?php else: ?>
                      <?php foreach ($user->business as $business): ?>
                          <p class="mb-0"><i class="fa fa-long-arrow-right"></i> <?php echo $this->db->where('id',$business->category)->get('business_category')->row()->name; ?></p>
                      <?php endforeach; ?>
                    <?php endif; ?>
                </td>
				<td>
                  <?php echo $this->db->where('user_id',$user->id)->where('type',1)->get('invoice')->num_rows(); ?> 
                </td>
				<td>
                  <?php echo $this->db->where('user_id',$user->id)->where('type',2)->get('invoice')->num_rows(); ?> 
                </td>
						
                <td>
                  <?php if ($user->user_type == 'trial'): ?>
                    <span class="label label-warning"><i class="flaticon-clock"></i> Pending</span>
                  <?php else: ?>
                    <?php $payment_status = get_user_payment($user->id) ?>
                    <?php $label = ''; ?>
                    <?php if ($payment_status == 'pending'){
                      $label = 'warning';
                      $text = '<i class="flaticon-time-is-money"></i> Pending';
                    }else if($payment_status == 'verified'){ 
                      $label = 'primary';
                      $text = '<i class="flaticon-checked"></i> Verified';
                    }else{ 
                      $label = 'danger';
                      $text = '<i class="flaticon-megaphone"></i> Expired';
                    }?>
                    <span class="label label-<?php echo html_escape($label) ?>">
                        <?php echo $text; ?>
                    </span>
                  <?php endif ?>
                </td>

                <td style="width:100px;">
                  <?php echo get_time_ago($user->created_at) ?>
                </td>
                <td>
                  <?php if ($user->payment_status != 'expire'): ?>
                    <?php if ($user->user_type == 'trial'): ?>
                      <span class="label label-warning"><?php echo date_dif(date('Y-m-d'), $user->trial_expire) ?> <?php echo trans('days-left') ?></span>
                    <?php 
                    else:
                        $expire_on = $this->db->where('user_id',$user->id)->where('status','verified')->get('payment')->row()->expire_on;
                    ?> 
                      <span class="label label-warning"><b><?php echo date_dif(date('Y-m-d'),$expire_on) ?> <?php echo trans('days-left') ?></span>
                    <?php endif ?>
                  <?php else: ?>
                
                    <span class="label label-danger"><b><?php echo get_time_ago($user->expire_on) ?></span>
                  <?php endif ?>
                </td>
            	<td>
                  <?php if($user->from_mobile == 1): ?> 
                    Mobile App
                   <?php else: ?>
                    Browser 
                  <?php endif ?>
                </td>
                <td class="actions">
                  <div class="btn-group">
                      <button type="button" class="btn btn-default rounded btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                         <?php echo trans('action') ?>
                      </button>
                      <div class="dropdown-menu st" x-placement="bottom-start">
                         
                          <?php if ($user->status == 1): ?>
                            <li><a class="dropdown-item" href="<?php echo base_url('admin/users/status_action/2/'.$user->id) ?>"><i class="fa fa-times"></i> <?php echo trans('deactivate') ?></a></li>
                          <?php endif ?>
                          <?php if ($user->status == 2 || $user->status == 0): ?>
                            <li><a class="dropdown-item" href="<?php echo base_url('admin/users/status_action/1/'.$user->id) ?>"><i class="fa fa-check"></i> <?php echo trans('activate') ?></a></li>
                          <?php endif ?>
                  
                          <li><a class="dropdown-item delete_item" data-val="User" data-id="<?php echo html_escape($user->id); ?>" href="<?php echo base_url('admin/users/delete/'.$user->id);?>" class="on-defaults remove-row delete_item"><i class="fa fa-trash-o"></i> <?php echo trans('delete') ?></a></li>
						  
						  <?php if (!empty($user->ip_address)): ?>
                            <li><a class="dropdown-item" href="<?php echo base_url('admin/users/view_visitor/'.md5($user->ip_address)) ?>"><i class="fa fa-file"></i> View pages</a></li>
                          <?php endif ?>
						  
						   <li><a class="dropdown-item" href="<?php echo base_url('admin/users/view_activity/'.md5($user->id)) ?>"><i class="fa fa-user"></i>User Activity</a></li>
                      </div>
                  </div>
                </td>

              </tr>

              <?php $i++; endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="col-md-12 text-center mt-50">
            <?php echo $this->pagination->create_links(); ?>
        </div>
      </div>
    </section>
</div>


<?php foreach ($users as $user): ?>
  <div id="roleModal_<?php echo html_escape($user->id) ?>" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <form method="post" action="<?php echo base_url('admin/users/change_account/'.html_escape($user->id))?>" role="form">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Select account type</h4>
        </div>

        <div class="modal-body">
          <div class="form-group m-t-20">
              <div class="radio radio-info radio-inline">
                  <input <?php if($user->account_type == 'free'){echo "checked";} ?> type="radio" id="inlineRadio3" value="free" name="type">
                  <label for="inlineRadio3"> Free </label>
              </div>
             <div class="radio radio-info radio-inline">
                  <input <?php if($user->account_type == 'pro'){echo "checked";} ?> type="radio" id="inlineRadio4" value="pro" name="type">
                  <label for="inlineRadio4"> Pro </label>
              </div>
          </div>
          <!-- csrf token -->
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-info">Update</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

      </form>

    </div>
  </div>
<?php endforeach ?>