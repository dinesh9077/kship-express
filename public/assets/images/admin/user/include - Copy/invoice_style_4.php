<?php include'invoice_val.php'; ?>

<style>
    
    .inv_header{
	display: flex;
    }
    
    .inv_header_hr_left{
	border-color: rgb(68, 68, 68); 
	border-width: 2px 0; 
	border-style: solid; 
	padding: 2px 0; 
	flex-grow: 2; 
	margin: 40px 0;
    }
    
    .inv_header_hr_right{
	border-color: rgb(68, 68, 68); 
	border-width: 2px 0; 
	border-style: solid; 
	padding: 2px 0; 
	flex-grow: 2; 
	margin: 40px 0;
    }
    
    .inv_header_title{
	border-width: 2px 0;
	border-style: solid; 
	height: 60px; 
	line-height: 64px; 
	position: relative; 
	padding: 0 10px;
    }
    
    .inv_header_title h4{
	display: inline; 
	border-width: 10px;
	margin: 0; 
	font-size: 30px; 
	text-transform: uppercase;
    }
    
    .inv_header_title:after, .inv_header_title:before{
	content: "";
	position: absolute;
	top: -8px;
	bottom: -8px;
	left: 0;
	right: 0;
	border-width: 2px 0;
	border-style: solid;
    }
    
    .table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th{
	border: 0;
    }
    
    @font-face {
        font-family: "georgia";
        src: url("vendor/mpdf/mpdf/ttfonts/Georgia.ttf") format("truetype");
        font-weight: normal;
        font-display: swap
    }
    .card.inv {
        font-family: 'georgia';
    }
</style>

<div class="card-body p-0">
    <div class="row p-15 mt-20 mb-20 flex-parent-between align-items-start m-0" style="width: 100%">
        <div class="col-4">
            <?php if (empty($logo)): ?>
			<p><span class="alterlogo"><?php echo $business_name ?></span></p>
            <?php else: ?>
			<img width="100%" src="<?php echo base_url($logo) ?>" alt="Logo" style="max-width: 200px">
            <?php endif ?>
		</div>
        <div class="col-5 text-right">
            <!--<p class="font-weight-bold mb-1"></p>-->
            <h4 class="mb-0" style=" color: black"><strong><?php echo html_escape($business_name) ?></strong></h4> 
			<?php if($this->business->gst_invoice == 1):?>
			<?php if (count($tax_format) > 0 && !empty($tax_format)):
				foreach($tax_format as $key=> $taxlable){
				?> 
				 <p class="mb-0" style=" color: black"><?php echo $taxlable; ?>: <span style="text-transform: uppercase"><?php echo html_escape($biz_vat_code[$key]) ?></span></p>
			<?php 
				}
				endif ?>
            <?php endif ?>
			
            <span class="mb-0 invbiz" style=""><?php echo $business_address ?></span>
            <p class="" style=""><?php echo html_escape($country) ?></p>
            
            <?php if (!empty($biz_number)): ?>
            <p class="mb-0" style=""><?php echo trans('contact').' '.trans('number') ?>: <?php echo html_escape($biz_number) ?></p>
            <?php endif ?>
            
            <?php if (!empty($website_url)): ?>
            <p class="mb-0" style=""><?php echo html_escape($website_url) ?></p>
            <?php endif ?>
            
		</div>
	</div>
    
    <div class="inv_header">
        <hr class="inv_header_hr_left">
        
        <div style="display: flex; align-items: center; margin: 0 20px;">
            <svg height="72" viewBox="0 0 28 72" width="28">
			<g fill="none" stroke="#444444" stroke-width="2"><path d="M183 57.038v-42.076c-.33.025-.664.038-1 .038-7.18 0-13-5.82-13-13 0-.336.013-.67.038-1h-154.076c.025.33.038.664.038 1 0 7.18-5.82 13-13 13-.336 0-.67-.013-1-.038v42.076c.33-.025.664-.038 1-.038 7.18 0 13 5.82 13 13 0 .336-.013.67-.038 1h154.076c-.025-.33-.038-.664-.038-1 0-7.18 5.82-13 13-13 .336 0 .67.013 1 .038z"></path><path d="M177 51.503v-31.007c-.33.024-.664.037-1 .037-7.18 0-13-5.626-13-12.567 0-.325.013-.648.038-.967h-142.076c.025.319.038.641.038.967 0 6.94-5.82 12.567-13 12.567-.336 0-.67-.012-1-.037v31.007c.33-.024.664-.037 1-.037 7.18 0 13 5.626 13 12.567 0 .325-.013.648-.038.967h142.076c-.025-.319-.038-.641-.038-.967 0-6.94 5.82-12.567 13-12.567.336 0 .67.012 1 .037z"></path></g></svg>
			
            <div class="inv_header_title">
                <h4 style=" color: black"><?php echo html_escape($title) ?></h4>
			</div>
            
            <svg height="72" viewBox="0 0 28 72" width="28">
			<g fill="none" stroke="#444444" stroke-width="2"><path d="M27 57.038v-42.076c-.33.025-.664.038-1 .038-7.18 0-13-5.82-13-13 0-.336.013-.67.038-1h-154.076c.025.33.038.664.038 1 0 7.18-5.82 13-13 13-.336 0-.67-.013-1-.038v42.076c.33-.025.664-.038 1-.038 7.18 0 13 5.82 13 13 0 .336-.013.67-.038 1h154.076c-.025-.33-.038-.664-.038-1 0-7.18 5.82-13 13-13 .336 0 .67.013 1 .038z"></path><path d="M21 51.503v-31.007c-.33.024-.664.037-1 .037-7.18 0-13-5.626-13-12.567 0-.325.013-.648.038-.967h-142.076c.025.319.038.641.038.967 0 6.94-5.82 12.567-13 12.567-.336 0-.67-.012-1-.037v31.007c.33-.024.664-.037 1-.037 7.18 0 13 5.626 13 12.567 0 .325-.013.648-.038.967h142.076c-.025-.319-.038-.641-.038-.967 0-6.94 5.82-12.567 13-12.567.336 0 .67.012 1 .037z"></path></g></svg>
		</div>
        
        <hr class="inv_header_hr_right">
	</div>

    
	
    <div class="rows bill_area flex-parent-between">
        <div class="col-4">
            <?php if (isset($page) && $page == 'Bill'): ?>
			<h5 class="font-weight-bold" style="text-transform: uppercase"><?php echo trans('purchase-from') ?></h5>
            <?php else: ?>
			<h5 class="font-weight-bold" style="text-transform: uppercase"><?php echo trans('bill-to') ?></h5>
            <?php endif ?>
            
            <?php if (empty($customer_id)): ?>
			<p class="mb-1" style=""><?php echo trans('empty-customer') ?></p>
			
            <?php else: ?>
			<p class="mb-1">
				<?php if (isset($page) && $page == 'Bill'): ?>
				<?php if (!empty(helper_get_vendor($customer_id))): ?>
				<p class="mb-0" style="text-transform: uppercase;"><strong><?php echo helper_get_vendor($customer_id)->name ?></strong></p>
				<p class="mt-0 mb-0" style="text-transform: uppercase;"><?php echo helper_get_vendor($customer_id)->address ?></p>
				<p class="mt-0 mb-0"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_vendor($customer_id)->phone ?></p>
				<p class="mt-0 mb-0"><?php echo helper_get_vendor($customer_id)->email ?></p>
				<?php endif ?>
				<?php else: ?>
				
				<?php if (!empty(helper_get_customer($customer_id))): ?>
				<p class="mb-0" style=""><strong><?php echo helper_get_customer($customer_id)->name ?></strong></p>
				<?php if (!empty($cus_vat_code)): ?>
				<p class="mt-0 mb-0" style=""><?php echo $cus_tax_format; ?>: <span style="text-transform: uppercase"><?php echo html_escape($cus_vat_code) ?></span></p>
				<?php endif ?>
				<p class="mt-0 mb-0" style=""><?php echo helper_get_customer($customer_id)->address ?> </p>
				<p class="mt-0 mb-0" style=""><?php echo helper_get_customer($customer_id)->country ?></p>
				<p class="mt-0 mb-0" style=""><?php echo trans('contact').' '.trans('number') ?>: <?php echo helper_get_customer($customer_id)->phone ?></p>
				<p class="mt-0 mb-0" style=""><?php echo trans('email').' '.trans('id') ?>: <?php echo helper_get_customer($customer_id)->email ?></p>
				<?php if (!empty($cus_number)): ?>
				<p class="mt-0 mb-0" style=""><?php echo trans('business').' '.trans('number') ?>: <?php echo html_escape($cus_number) ?></p>
				<?php endif ?>
				<?php endif ?>
				<?php endif ?>
			</p>
            <?php endif ?>
		</div>
		
        <div class="col-4 text-right">
            <table class="tables" style="float: right; width: 100%">
                <tr>
                    <td><strong class="mr-10" style=""><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-number');}else if($page == 'Estimate'){echo trans('estimate-number');}else if($page == 'Delivery'){echo 'Challan Number';}else{echo trans('bill-number');} ?>:</strong></td>
                    <td class="text-left" colspan="1" style=""><?php echo html_escape($number) ?></td>
				</tr>
                <tr>
                    <td><strong class="mr-10" style=""><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-date');}else if(isset($page) && $page == 'Delivery'){echo 'Delivery Date';}else{echo trans('date');} ?>:</strong></td>
                    <td class="text-left" colspan="1" style=""><?php echo my_date_show($date) ?></td>
				</tr>
                <?php if (!empty($poso_number)): ?>
                <tr>
                    <td><strong class="mr-10" style=""><?php echo trans('p.o.s.o.-number') ?>:</strong></td>
                    <td class="text-left" colspan="1" style=""><?php echo html_escape($poso_number) ?></td>
				</tr>
                <?php endif ?>
                <?php if(isset($page) && $page == 'Invoice'):?>
				<tr>
					<td><strong class="mr-10" style=""><?php echo trans('due-date') ?>:</strong></td>
					<td class="text-left" style="">
						<?php echo my_date_show($payment_due) ?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td class="text-left" style="">
						<?php if ($due_limit == 1): ?>
						<p><?php echo trans('on-receipt') ?></p>
						<?php else: ?>
						<p><?php echo trans('within') ?> <?php echo html_escape($due_limit) ?> <?php echo trans('days') ?></p>
						<?php endif ?>
					</td>
				</tr>
                <?php 
					else:
					if(isset($page) && $page != 'Delivery'):
				?>
				<tr>
					<td><b class="mr-10" style=""><?php echo trans('expires-on') ?>:</b></td>
					<td class="text-left" style="">
                        <?php echo my_date_show($invoice->expire_on) ?>
					</td>
				</tr>
                <?php endif; ?>
                <?php endif; ?>
                <tr>
                    <td class="text-right bg-mlight" style=" padding: 2%; border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b class="mr-10"><strong><?php echo trans('amount-due') ?>:</strong></b></td>
                    <td class="bg-mlight" style=" padding: 2%; text-align: left; border-top-right-radius: 25px; border-bottom-right-radius: 25px;">
                        <span>
                            <?php if ($status == 2): ?>
							<?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?>0.00 
                            <?php else: ?>
							<?php if (isset($page_title) && $page_title == 'Invoice Preview'): ?>
							<?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo $grand_total; ?>
							<?php else: ?>
							<?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id), 2); ?>
							<?php endif ?>
                            <?php endif ?>
						</span>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
    <div class="row p-0 table_area">
        <div class="col-12 table-responsive">
            <table class="table table-bordered m-0">
                <thead class="pre_head_4" style="border-bottom: 1px dotted black">
                    <tr class="inv-pl30">
                        <th class="border-0" style="color: black; font-weight: bold; "><?php echo trans('items') ?></th>
                        <th class="border-0" style="color: black; font-weight: bold; ">HSN/SAC Code</th>
                        <th class="border-0 text-right" style="color: black; font-weight: bold; "><?php echo trans('price') ?></th>
                        <th class="border-0 text-center" style="color: black; font-weight: bold; "><?php echo trans('quantity') ?></th>
						<th class="border-0" style="color: black; font-weight: bold; ">Unit</th>
                        <th class="border-0 text-right" style="color: black; font-weight: bold; "><?php echo trans('amount') ?></th>
					</tr>
				</thead>
                <tbody>
					
                    <?php if (isset($page_title) && $page_title == 'Invoice Preview'): ?>
					<?php if (!empty($this->session->userdata('item'))): ?>
					<?php $total_items = count($this->session->userdata('item')); ?>
					<?php else: ?>
					<?php $total_items = 0; ?>
					<?php endif ?>
					
					<?php if (empty($total_items)): ?>
					<tr>
						<td colspan="5" class="text-center"><?php echo trans('empty-items') ?></td>
					</tr>
					<?php else: ?>
					<?php for ($i=0; $i < $total_items; $i++) { ?>
						<tr class="inv-pl30">
							<td width="25%">
								<?php $product_id = $this->session->userdata('item')[$i] ?>
								
								<?php if (is_numeric($product_id)) {
									echo helper_get_product($product_id)->name.'<br> <small>'. nl2br(helper_get_product($product_id)->details).'</small>';
                                    } else {
									echo html_escape($product_id);
								} ?>
							</td>
							<td style="color: black; font-weight: bold; " ><?php echo $this->session->userdata('hsn_sac')[$i] ?></td>
							<td class="text-right" style="color: black; font-weight: bold; "><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?> <?php echo $this->session->userdata('price')[$i] ?></td>
							<td style="color: black; font-weight: bold; " class="text-center"><?php echo $this->session->userdata('quantity')[$i] ?></td>	
							<td style="color: black; font-weight: bold; "><?php if(!empty($this->session->userdata('unit')[$i])){ echo $this->session->userdata('unit')[$i];}else{echo "-";} ?></td>
							<td style="color: black; font-weight: bold; "><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?> <?php echo number_format($this->session->userdata('total_price')[$i], 2) ?></td>
						</tr>
					<?php } ?>
					<?php endif ?>
                    <?php else: ?>
					<?php $items = helper_get_invoice_items($invoice->id) ?>
					<?php if (empty($items)): ?>
					<tr class="inv-pl30">
						<td colspan="4" class="text-center"><?php echo trans('empty-items') ?></td>
					</tr>
					<?php else: ?>
					<?php foreach ($items as $item): ?>
					<tr class="inv-pl30">
						<td width="25%">
							<h5 style="margin-bottom: 0px;  color: black"><strong><?php echo html_escape($item->item_name) ?></strong></h5>
							<p style=" margin-bottom: 0px;"><?php echo html_escape($item->serial_no) ?></p>
						    <p style=" margin-bottom: 0px;"><?php echo nl2br($item->details) ?></p>
						</td>
						<td style="color: black; "><p class="m-0"><?php echo $item->hsn_sac ?></p></td>
						<td class="text-right" style="color: black; "><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->price,2) ?></td>
						<td style="color: black; " class="text-center"><?php echo html_escape($item->qty) ?></td>
						<td style="color: black; "><?php if(!empty($item->unit)){echo html_escape($item->unit);}else{echo "-";} ?></td>
						<td class="text-right" style="color: black; "><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->total, 2) ?></td>
					</tr>
					<?php endforeach ?>
					<?php endif ?>
                    <?php endif ?>
	
					
				</tbody>
			</table>
			
			<table class="table table-bordered">
                <tbody>
									
					<?php if (!empty($discount)): ?>
					 <tr class="inv-pl30" style="border-top: 1px dotted black">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right" style=" color: black"><strong>Total :</strong></td>
                        <td class="text-right" style=" color: black"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total,2) ?></span></td>
					</tr>
					<tr class="inv-pl30">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-right" style=" color: black"><strong><?php echo trans('discount') ?> ( <?php echo html_escape($discount) ?>% ):</strong></td>
						<td class="text-right" style=" color: black"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total * ($discount / 100), 2) ?></span></td>
					</tr>
					<tr class="inv-pl30" style="border-top: 1px dotted black">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right" style=" color: black"><strong><?php echo trans('sub-total') ?>:</strong></td>
                        <td class="text-right" style=" color: black"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total - $sub_total * ($discount / 100),2) ?></span></td>
					</tr>
                    <?php else: ?>
                    <tr class="inv-pl30" style="border-top: 1px dotted black">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right" style=" color: black"><strong><?php echo trans('sub-total') ?>:</strong></td>
                        <td class="text-right" style=" color: black"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total,2) ?></span></td>
					</tr>
					<?php endif ?>
                    <?php if (!empty($taxes)): ?>
					<?php foreach ($taxes as $tax): ?>
					<?php if ($tax != 0): ?>
					<tr class="inv-pl30">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-right" style=" color: black"><strong><?php echo get_tax_id($tax)->type_name.' ('.get_tax_id($tax)->rate.'%)' ?>:</strong></td>
						<?php 
						    if (!empty($discount)){
						        $gstamount = $sub_total-$sub_total * ($discount / 100);
						    }else{
						        $gstamount = $sub_total;
						    } 
        				?>
						<td class="text-right" style=" color: black"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($gstamount * (get_tax_id($tax)->rate / 100), 2) ?></span></td>
					</tr>
					<?php endif ?>
					<?php endforeach ?>
                    <?php endif ?>
                     
                    <tr class="inv-pl30">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right" style="border-top: 1px solid #b2b2b2;  color: black"><strong><?php echo trans('grand-total') ?>:</strong></td>
                        <td class="text-right" style="border-top: 1px solid #b2b2b2;  color: black"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($grand_total,2) ?></span></td>
					</tr>
					
                    <?php foreach (get_invoice_payments($invoice->id) as $payment): ?>
					<tr class="inv-pl30 text-dark">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-right" width="60%">
							<span class="fs-13"><strong><?php echo trans('payment-on') ?> <?php echo my_date_show($payment->payment_date) ?> <?php echo trans('using') ?> <?php echo get_using_methods($payment->payment_method) ?></strong></span>
						</td>
						<td class="text-right">
							<span class="fs-13"><strong><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($payment->amount,2) ?></strong></span>
						</td>
					</tr>
                    <?php endforeach ?>
                    
                    <tr class="inv-pl30">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
						<td class="text-right" style="border-top: double #b2b2b2;  color: black"><b><strong><?php echo trans('amount-due')?>:</strong></b></td>
						<td class="text-right" style="border-top: double #b2b2b2;  color: black">
							<span>
								<strong>
									<?php if ($status == 2): ?>
									<?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?>0.00 
									<?php else: ?>
									<?php if (isset($page_title) && $page_title == 'Invoice Preview'): ?>
                                    <?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo $grand_total; ?>
									<?php else: ?>
                                    <?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id), 2); ?>
									<?php endif ?>
									<?php endif ?>
								</strong>
							</span>
						</td>
					</tr>
				</tbody>
			</table>
			
			<div class="p-30" style=" color: black">
        		<p><strong>Notes / Terms</strong></p>
        		<p><?php echo $footer_note ?></p>
                 
                 <p style="text-align: right; font-weight: bold; font-size: 14px">for <?php echo $business_name ?></p>
                 <br>
                 <br>
                 <p style="text-align: right; font-weight: bold; font-size: 14px">Authorised Signatory</p>
                <p class="">Receiver's Signature _________________________</p>
        	</div>
			
            <?php if (!empty($qr_code)): ?>
			<p class="p-30"><img class="qr_code_sm" src="<?php echo base_url($qr_code) ?>" alt="QR Code"></p>
            <?php endif; ?>
			<?php if (!empty($pay_qrcode)): ?>
			<h6 class="pl-30" style="color: black">Scan-QR to Pay</h6>
			<p class="pl-30"><img class="qr_code_sm" src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code"></p>
            <?php endif; ?>
		</div>
	</div>

	
	
</div>