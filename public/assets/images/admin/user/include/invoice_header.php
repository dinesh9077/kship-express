<div class="row mb-10">
    <div class="col-md-4 col-xs-12">
		<h2 class="pull-left" style="font-size: 26px"><strong><i class="flaticon-approve-invoice"></i>&nbsp;<?php if(isset($page_title) && $page_title == 'Edit Invoice'){echo trans('edit');}else{echo trans('create-new');} ?> <?php echo trans('invoice') ?></strong></h2>
	</div>
    <div class="col-md-8 col-xs-12">
        <div class="add-btn inv-top-btn mb-10">
			
			<?php if (isset($page_title) && $page_title == 'Edit Invoice'): ?>
			<a href="<?php echo $_SERVER['HTTP_REFERER'] ?>" class="btn btn-default btn-rounded ml-10"><i class="fa fa-long-arrow-left"></i> Back</a>
            <?php endif ?>
			
			<button type="submit" class="btn btn-info btn-rounded save_invoice_btn ml-5"><?php if(isset($page_title) && $page_title == 'Edit Invoice'){echo trans('update');}else{echo trans('save-and-continue');} ?></button>
            
			<button id="edit_invoice" type="button" class="btn waves-effect waves-light btn-rounded btn-outline-info mr-10 edit_invoice_btn" style="display: none;"><?php echo trans('edit') ?></button>
			<?php if($this->business->invoice_preview == 1): ?>
            <button type="submit" class="btn waves-effect waves-light btn-outline-info btn-rounde mr-10 preview_invoice_btn"><?php echo trans('preview') ?></button>
			<?php endif; ?>
		</div>
        <input type="hidden" class="set_value" name="check_value">
	</div>
</div>