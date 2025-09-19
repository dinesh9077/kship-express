 
<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        
        <div class="col-md-12 m-auto box add_area">
            <div class="box-header with-border">
               <div class="d-flex align-items-center justify-content-between bg-light1 f-no">
                        <?php if (isset($page_title) && $page_title == "Edit"): ?>
                        <h3 class="box-title"><i class="fa fa-money"></i>&nbsp;Edit Cash Detail</h3>
                        <?php else: ?>
                        <h3 class="box-title"><i class="fa fa-money"></i>&nbsp;Add New Cash</h3>
                        <?php endif; ?>
                        <div class="box-tools pull-right">
                            <?php if (isset($page_title) && $page_title == "Edit"): ?>
                            <a href="<?php echo base_url('admin/banking') ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                            <?php else: ?>
                            <a href="<?php echo base_url('admin/banking') ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                            <?php endif; ?>
						</div>
				</div>
			</div>
            
            <div class="box-body">
                 <?php if (isset($page_title) && $page_title == "Edit"): ?>
                  <form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form row" action="<?php echo base_url('admin/banking/add_cash_data')?>" role="form" novalidate> 
                    <div class="col-lg-6 form-group ">
                        <label  for="example-input-normal">Select Account Type <span class="text-danger">*</span></label>
                        <select class="form-control col-sm-12 wd-100 single_select" style="width:100%" id="account_type" name="account_type" required>
							<option>Cash In Hand</option>
						</select>
					</div>   
					<div class="col-lg-6 form-group">
                        <label id="card_lim">Cash Holder Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="account_name" value="<?php echo html_escape($customer[0]['account_name']); ?>" required>
					</div>
                    <div class="col-lg-6 form-group d-none">
                        <label class="col-sm-2 control-label p-0" for="example-input-normal">Currency <span class="text-danger">*</span></label>
                        <select class="form-control col-sm-12 wd-100 single_select" style="width:100%" id="currency" name="currency" required>
                            <!--<option value=""><?php //echo trans('select') ?></option>-->
                            <option <?php echo ($customer[0]['currency'] == "INR") ? 'selected' : ''; ?>>INR</option>
                            <option <?php echo ($customer[0]['currency'] == "AUD") ? 'selected' : ''; ?>>AUD</option>
                            <option <?php echo ($customer[0]['currency'] == "CNY") ? 'selected' : ''; ?>>CNY</option>
                            <option <?php echo ($customer[0]['currency'] == "EUR") ? 'selected' : ''; ?>>EUR</option>
                            <option <?php echo ($customer[0]['currency'] == "GBP") ? 'selected' : ''; ?>>GBP</option>
                            <option <?php echo ($customer[0]['currency'] == "JPY") ? 'selected' : ''; ?>>JPY</option>
                            <option <?php echo ($customer[0]['currency'] == "USD") ? 'selected' : ''; ?>>USD</option>
                            <option <?php echo ($customer[0]['currency'] == "ZAR") ? 'selected' : ''; ?>>ZAR</option>
						</select>
					</div>
                     
					<div class="col-lg-6 form-group">
                        <label id="card_lim">Cash Balance <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="opening_balance" value="<?php echo html_escape($customer[0]['opening_balance']); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
					</div>	
					  
                    <div class="col-lg-6 form-group">
                        <label>Description </label>
                        <textarea class="form-control" name="description"><?php echo html_escape($customer[0]['description']); ?></textarea>
					</div> 
					<div class="col-lg-6 form-group">
                        <label class="col-sm-2 control-label p-0" for="example-input-normal">Status <span class="text-danger">*</span></label>
                        <select class="form-control col-sm-12 wd-100 single_select" style="width:100%" id="status" name="status" required>
								<option value="1" <?php echo ($customer[0]['status'] == "1") ? 'selected' : ''; ?>>Active</option>
                            <option value="0"<?php echo ($customer[0]['status'] == "0") ? 'selected' : ''; ?>>In-Active</option>
						</select>
					</div>
					<input type="hidden" name="id" value="<?php echo html_escape($customer['0']['id']); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>"> 
                    <hr> 
                    <!--<div class="row m-t-30">-->
					<div class="col-sm-12">
						<?php if (isset($page_title) && $page_title == "Edit"): ?>
						<button type="submit" class="btn btn-info rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
						<?php else: ?>
						<button type="submit" class="btn btn-info rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
						<?php endif; ?>
					</div>
                    <!--</div>--> 
				</form>
				
                  <?php else: ?>
                  
                <form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form row" action="<?php echo base_url('admin/banking/add_cash_data')?>" role="form" novalidate> 
                    <div class="col-lg-6 form-group ">
                        <label  for="example-input-normal">Select Account Type <span class="text-danger">*</span></label>
                        <select class="form-control col-sm-12 wd-100 single_select" style="width:100%" id="account_type" name="account_type" required>
							<option>Cash In Hand</option>
						</select>
					</div> 
				      
					<div class="col-lg-6 form-group">
                        <label id="card_lim">Cash Holder Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="account_name" value="" required>
					</div>	
					 
                    <div class="col-lg-6 form-group d-none">
                        <label class="col-sm-2 control-label p-0" for="example-input-normal">Currency <span class="text-danger">*</span></label>
                        <select class="form-control col-sm-12 wd-100 single_select" style="width:100%" id="currency" name="currency" required> 
                            <option>INR</option>
                            <option>AUD</option>
                            <option>CNY</option>
                            <option>EUR</option>
                            <option>GBP</option>
                            <option>JPY</option>
                            <option>USD</option>
                            <option>ZAR</option>
						</select>
					</div>
                      
					<div class="col-lg-6 form-group">
                        <label id="card_lim">Cash Balance <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="opening_balance" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
					</div>	
					 
                    <div class="col-lg-6 form-group">
                        <label>Description </label>
                        <textarea class="form-control" name="description"></textarea>
					</div> 
					<div class="col-lg-6 form-group">
                        <label class="col-sm-2 control-label p-0" for="example-input-normal">Status <span class="text-danger">*</span></label>
                        <select class="form-control col-sm-12 wd-100 single_select" style="width:100%" id="status" name="status" required>
							<option value="1">Active</option>
                            <option value="0">In-Active</option>
						</select>
					</div>
				
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>"> 
                    <hr> 
                   
					<div class="col-sm-12">
						<?php if (isset($page_title) && $page_title == "Edit"): ?>
						<button type="submit" class="btn btn-info rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
						<?php else: ?>
						<button type="submit" class="btn btn-info rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
						<?php endif; ?>
					</div>
                    <!--</div>--> 
				</form>
				  <?php endif; ?>
			</div>
             
		</div>
         
	</section>
</div>
