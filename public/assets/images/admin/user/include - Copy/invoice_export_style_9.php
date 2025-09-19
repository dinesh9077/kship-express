<?php include'invoice_val.php'; ?>
<link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/admin_style.css?var=<?php echo settings()->version ?>&time=<?=time();?>"> 
<div class="card-body p-0">
    <div class="row p-35 align-items-start" style="justify-content: space-between">
        <div class="col-3" style="width: 35%; float: left;">
            
            <?php if (empty($logo)): ?>
			<p><span class="alterlogo"><?php echo $business_name ?></span></p>
            <?php else: ?>
			<img width="90%" src="<?php echo base_url($logo) ?>" alt="Logo" style="max-width: 200px; max-height: 200px;">
            <?php endif ?>
		</div>
        <div class="col-5 text-right" style="width: 40%; float: right; margin-top: -10px">
            <h1 class="mb-0" style="text-transform: uppercase; font-family: helvetica; font-weight: 800"><strong><?php echo html_escape($title) ?></strong></h1>
             
            <?php if($this->business->gst_invoice == 1):?>
			<?php if (count($tax_format) > 0 && !empty($tax_format)):
				foreach($tax_format as $key=> $taxlable){
				?> 
				 <p class="mb-0" style="color: #849194; font-family: helvetica;"><?php echo $taxlable; ?>: <span style="text-transform: uppercase"><?php echo html_escape($biz_vat_code[$key]) ?></span></p>
			<?php 
				}
				endif ?>
            <?php endif ?>
			
            <br>
            <p class="mb-0" style="font-weight: bold; font-size: 12px"><b><strong><?php echo $business_name ?></strong></b></p>
            
            <span class="mb-0 invbiz" style="font-size: 12px"><?php echo $business_address ?></span>
            <p class="mb-0" style="font-size: 12px"><?php echo html_escape($country) ?></p>
            
            <?php if (!empty($biz_number)): ?>
            <p class="mb-0" style="font-size: 12px; font-family: arial; text-transform: uppercase"><?php echo trans('contact').' '.trans('no') ?>: <?php echo html_escape($biz_number) ?></p>
            <?php endif ?>
            
            <?php if (!empty($website_url)): ?>
            <p class="mb-0" style="font-size: 12px; font-family: arial;"><?php echo html_escape($website_url) ?></p>
            <?php endif ?>
			
		</div>
	</div>
	
	<hr class="my-5" style="border-top-color: rgb(97 106 120 / 27%)">
	
    <div class="flex-parent-between bill_area align-items-center" style="width: 100%">
        <div class="col-6 py-4 pl-30 pr-30" style="width: 25%; float: left;">
            
            <?php if (isset($page) && $page == 'Bill'): ?>
			<h5 class="mb-0" style="font-weight: 400; text-transform: uppercase; color: #a7a7a7; font-size: 12px"><?php echo trans('purchase-from') ?></h5>
            <?php else: ?>
			<h5 class="mb-0" style="font-weight: 400; text-transform: uppercase; color: #a7a7a7; font-size: 12px"><?php echo trans('bill-to') ?></h5>
            <?php endif ?>
            
            
			<?php if (empty($customer_id)): ?>
			<p class="mb-0"><?php echo trans('empty-customer') ?></p>
            <?php else: ?>
			<p class="mb-0">
				
				<?php if (isset($page) && $page == 'Bill'): ?>
				<?php if (!empty(helper_get_vendor($customer_id))): ?>
				<p class="mb-0" style="font-weight: bold; font-size: 12px; text-transform: uppercase"><strong><?php echo helper_get_vendor($customer_id)->name ?></strong></p>
				<p class="mt-0 mb-0" style="font-size: 12px; text-transform: uppercase;"><?php echo helper_get_vendor($customer_id)->address ?></p>
				 <p class="mb-0" style="font-size: 12px"><?php echo html_escape($country) ?></p>
				<p class="mt-0 mb-0" style="font-size: 12px"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_vendor($customer_id)->phone ?></p>
				<p class="mt-0 mb-0" style="font-size: 12px"><?php echo helper_get_vendor($customer_id)->email ?></p>
				<?php endif ?>
				<?php else: ?>
				
				<?php if (!empty(helper_get_customer($customer_id))): ?>
				<p class="mt-0 mb-0" style="font-weight: bold; font-size: 12px; text-transform: uppercase"><strong><?php echo helper_get_customer($customer_id)->name ?></strong></p>
				<?php if (!empty($cus_vat_code)): ?>
				<p class="mt-0 mb-0" style="font-size: 12px"><span><?php echo $cus_tax_format; ?>:</span> <span style="text-transform: uppercase"><?php echo html_escape($cus_vat_code) ?></span></p>
				<?php endif ?>
				<p class="mt-0 mb-0" style="font-size: 12px; text-transform: uppercase"><?php echo helper_get_customer($customer_id)->address ?></p>
				 <p class="mb-0" style="font-size: 12px"><?php echo html_escape($country) ?></p>
				<p class="mt-0 mb-0" style="font-size: 12px; text-transform: uppercase"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_customer($customer_id)->phone ?></p>
				<p class="mt-0 mb-0" style="font-size: 12px;"><?php echo trans('email').' '.trans('id') ?>: <?php echo helper_get_customer($customer_id)->email ?></p>
				<?php endif ?>
				<?php endif ?>
				
			</p>
            <?php endif ?>
		</div>
		
        <div class="col-5 py-4 text-right" style="width: 40%; float: right; text-align: right; margin-right: -105px;">
            <table class="tables" style="width: 100%">
                <tr>
                    <td style="text-align: right; font-size: 12px; font-weight: bold; padding-bottom: 5px"><b class="mr-10"><strong><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-number');}else if($page == 'Estimate'){echo trans('estimate-number');}else if($page == 'Delivery'){echo 'Challan Number';}else{echo trans('bill-number');} ?>:</strong></b></td>
                    <td class="text-left" colspan="1" style="padding-left: 12px; font-size: 12px; padding-bottom: 5px"><?php echo html_escape($number) ?></td>
				</tr>
                <tr>
                    <td style="text-align: right; font-size: 12px; font-weight: bold; padding-bottom: 5px"><b class="mr-10"><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-date');}else if(isset($page) && $page == 'Delivery'){echo 'Delivery Date';}else{echo trans('date');} ?>:</b></td>
                    <td class="text-left" colspan="1" style="padding-left: 12px; font-size: 12px; padding-bottom: 5px"><?php echo my_date_show($date) ?></td>
				</tr>
                <?php if (!empty($poso_number)): ?>
				<tr>
					<td style="text-align: right; font-size: 12px; font-weight: bold; padding-bottom: 5px"><b class="mr-10"><?php echo trans('p.o.s.o.-number') ?>:</b></td>
					<td class="text-left" colspan="1" style="padding-left: 12px; font-size: 12px; padding-bottom: 5px"><?php echo html_escape($poso_number) ?></td>
				</tr>
                <?php endif ?>
                <?php if(isset($page) && $page == 'Invoice'):?>
				<tr>
					<td style="text-align: right; font-size: 12px; font-weight: bold; padding-bottom: 5px"><b class="mr-10"><?php echo trans('due-date') ?>:</b></td>
					<td class="text-left" style="padding-left: 12px; font-size: 12px; padding-bottom: 5px">
						<?php echo my_date_show($payment_due) ?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td class="text-left" style="padding-left: 12px; font-size: 12px; padding-bottom: 5px">
						<?php if ($due_limit == 1): ?>
						<p style="font-size: 12px;"><?php echo trans('on-receipt') ?></p>
						<?php else: ?>
						<p style="font-size: 12px;"><?php echo trans('within') ?> <?php echo html_escape($due_limit) ?> <?php echo trans('days') ?></p>
						<?php endif ?>
					</td>
				</tr>
                <?php
					else:
					if(isset($page) && $page != 'Delivery'):
					?>
				<tr>
					<td style="text-align: right; font-size: 12px; font-weight: bold; padding-bottom: 5px"><b class="mr-10"><?php echo trans('expires-on') ?>:</b></td>
					<td class="text-left" style="padding-left: 12px; font-size: 12px; padding-bottom: 5px">
                        <?php echo my_date_show($invoice->expire_on) ?>
					</td>
				</tr>
                <?php endif; ?>
                <?php endif; ?>
                
                <tr>
                    <td class="text-right bg-mlight" style="padding: 2%; font-weight: bold; font-size: 12px"><b class="mr-10"><strong><?php echo trans('amount-due') ?>:</strong></b></td>
                    <td class="bg-mlight" style="padding: 2%; padding-left: 12px; text-align: left; font-weight: bold; font-size: 12px">
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
            <table class="table">
                <thead class="pre_head">
                    <tr class="pre_head_tr inv-pl30" style="background: <?php echo html_escape($color) ?>">
                        <th class="border-0" style="font-weight: bold; padding: 5px 10px"><?php echo trans('items') ?></th>
						<th class="border-0" style="font-weight: bold; padding: 5px 10px">HSN/SAC Code</th>
                        <th class="border-0" style="font-weight: bold; text-align: center; padding: 5px 10px"><?php echo trans('rate') ?></th>
                        <th class="border-0" style="font-weight: bold; text-align: center; padding: 5px 10px"><?php echo trans('quantity') ?></th>
						<th class="border-0" style="font-weight: bold; text-align: center; padding: 5px 10px">Unit</th>
                        <th class="border-0" style="font-weight: bold; text-align: right; padding: 5px 10px"><?php echo trans('amount') ?></th>
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
							<td width="25%" style="border-top: 0; padding: 5px 10px">
								<?php $product_id = $this->session->userdata('item')[$i] ?>
								
								<?php if (is_numeric($product_id)) {
									echo '<p style="font-weight: bold; font-size:12px">'.  helper_get_product($product_id)->name.'</p> <p>'. nl2br(helper_get_product($product_id)->details).'</p>';
                                    } else {
									echo '<p style="font-size:12px">'.html_escape($product_id).'</p>';
								} ?>
							</td>
							<td style="border-top: 0; padding: 5px 10px" ><?php echo $this->session->userdata('hsn_sac')[$i] ?></td>
							<td style="border-top: 0; text-align: center; padding: 5px 10px"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo $this->session->userdata('price')[$i] ?></td>
							<td style="border-top: 0; text-align: center; padding: 5px 10px"><?php echo $this->session->userdata('quantity')[$i] ?></td>
							<td style="border-top: 0; text-align: center; padding: 5px 10px"><?php if(!empty($this->session->userdata('unit')[$i])){ echo $this->session->userdata('unit')[$i]; }else {echo "";} ?></td>
							<td style="border-top: 0; text-align: right; padding: 5px 10px"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($this->session->userdata('total_price')[$i], 2) ?></td>
						</tr>
					<?php } ?>
					<?php endif ?>
					
                    <?php else: ?>
					
					<?php $items = helper_get_invoice_items($invoice->id) ?>
					<?php if (empty($items)): ?>
					<tr>
						<td colspan="5" class="text-center"><?php echo trans('empty-items') ?></td>
					</tr>
					<?php else: ?>
					<?php foreach ($items as $item): ?>
					<tr class="inv-pl30">
						<td width="25%" style="border-top: 0;padding: 5px 10px">
						    <p style="font-weight: bold; font-size: 12px; "><?php echo html_escape($item->item_name) ?></p>
						    <p style="font-size: 12px"><?php echo html_escape($item->serial_no) ?></p>
						    <p style="font-size: 12px"><?php echo nl2br($item->details) ?></p>
						 </td>
						<td style="border-top: 0; padding: 5px 10px; font-size: 12px;"><p><?php echo $item->hsn_sac ?></p></td>
						<td style="border-top: 0; font-size: 12px; text-align: center; padding: 5px 10px"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->price, 2) ?></td>
						<td style="border-top: 0; font-size: 12px; text-align: center; padding: 5px 10px"><?php echo html_escape($item->qty) ?></td>
						<td style="border-top: 0; font-size: 12px; text-align: center; padding: 5px 10px"><?php if(!empty($item->unit)){echo html_escape($item->unit);}else{echo "-";} ?></td>
						<td style="border-top: 0; font-size: 12px; text-align: right; padding: 5px 10px"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->total, 2) ?></td>
					</tr>
					<?php endforeach ?>
					<?php endif ?>
                    <?php endif ?>
					
					<?php if (!empty($discount)): ?>
					 <tr class="inv-pl30" style="border-top: 3px solid  #dfdede">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right" style="font-weight: bold; font-size: 12px; padding: 5px 10px"><strong>Total :</strong></td>
                        <td style="font-size: 12px; padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total, 2) ?></span></td>
					</tr>
					<tr class="inv-pl30">
						<td style="border-top: 0; padding: 5px 10px"></td>
						<td style="border-top: 0; padding: 5px 10px"></td>
						<td style="border-top: 0; padding: 5px 10px"></td>
						<td style="border-top: 0; padding: 5px 10px"></td>
						<td class="text-right" style="border-top: 0; font-weight: bold; font-size: 12px; padding: 5px 10px"><strong><?php echo trans('discount') ?> <?php echo html_escape($discount) ?>% :</strong></td>
						<td style="border-top: 0; font-size: 12px; padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total * ($discount / 100), 2) ?></span></td>
					</tr>
					 <tr class="inv-pl30" style="border-top: 3px solid  #dfdede">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right" style="font-weight: bold; font-size: 12px; padding: 5px 10px"><strong><?php echo trans('sub-total') ?>:</strong></td>
                        <td style="font-size: 12px; padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total-$sub_total * ($discount / 100), 2) ?></span></td>
					</tr>
                    <?php else: ?>
                    <tr class="inv-pl30" style="border-top: 3px solid  #dfdede">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right" style="font-weight: bold; font-size: 12px; padding: 5px 10px"><strong><?php echo trans('sub-total') ?>:</strong></td>
                        <td style="font-size: 12px; padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total, 2) ?></span></td>
					</tr>
					<?php endif ?>
					
                    <?php if (!empty($taxes)): ?>
					<?php foreach ($taxes as $tax): ?>
					<?php if ($tax != 0): ?>
					<tr class="inv-pl30">
						<td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
						<td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
						<td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
						<td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
						<!--<td class="text-right"><strong><?php echo get_tax_id($tax)->type_name.' ('.get_tax_id($tax)->name.'-'.get_tax_id($tax)->number.') '.get_tax_id($tax)->rate.'%' ?></strong></td>-->
						
						<td class="text-right" style="border-top: 0; font-weight: bold; font-size: 12px; padding: 5px 10px">
							<strong>
								<?php echo get_tax_id($tax)->type_name.'  ('.get_tax_id($tax)->rate.'%)' ?>:
							</strong></td>
							 <?php 
    						    if (!empty($discount)){
    						        $gstamount = $sub_total-$sub_total * ($discount / 100);
    						    }else{
    						        $gstamount = $sub_total;
    						    } 
            				?>
							<td style="border-top: 0; font-size: 12px; padding: 5px 20px; text-align: right">
								<span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($gstamount * (get_tax_id($tax)->rate / 100), 2) ?></span>
							</td>
					</tr>
					<?php endif ?>
					<?php endforeach ?>
                    <?php endif ?>
					
                    <tr class="inv-pl30">
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td class="text-right" style="font-weight: bold; font-size: 12px; padding: 5px 10px"><strong><?php echo trans('grand-total') ?>:</strong></td>
                        <td style="font-size: 12px; text-align: right; padding: 5px 20px"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($grand_total, 2) ?></span></td>
					</tr>
					
					
                    <?php foreach (get_invoice_payments($invoice->id) as $payment): ?>
					<tr class="inv-pl30 text-dark">
						<td style="border-top: 0; padding: 5px 10px"></td>
						<td style="border-top: 0; padding: 5px 10px"></td>
						<td style="border-top: 0; padding: 5px 10px"></td>
						<td style="border-top: 0; padding: 5px 10px"></td>
						<td class="text-right" style="font-size: 12px; padding: 5px 10px">
							<span class="fs-13"><strong><?php echo trans('payment-on') ?> <?php echo my_date_show($payment->payment_date) ?> <?php echo trans('using') ?> <?php echo get_using_methods($payment->payment_method) ?>:</strong></span>
						</td>
						<td class="text-right">
							<span class="fs-13" style="font-size: 12px; padding: 5px 10px; text-align: right"><strong><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($payment->amount,2) ?></strong></span>
						</td>
					</tr>
                    <?php endforeach ?>
					
                    <tr class="inv-pl30">
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td class="text-right" style="border-top: 3px solid #dfdede; font-weight: bold; font-size: 12px; padding: 5px 10px"><strong><?php echo trans('amount-due') ?>:</strong></td>
                        <td style="border-top: 3px solid #dfdede; font-size: 12px; padding: 5px 20px; text-align: right">
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
					
				</tbody>
			</table>
            <div class="p-30">
                <p><strong>Notes / Terms</strong></p>
                <p><?php echo $footer_note ?></p>
                 
                 <p style="text-align: right; font-weight: bold; font-size: 14px">for <?php echo $business_name ?></p>
                 <br>
                 <br>
                 <p style="text-align: right; font-weight: bold; font-size: 14px">Authorised Signatory</p>
                <p class="">Receiver's Signature _________________________</p>
			</div>
			
            <?php if (!empty($pay_qrcode)): ?>
			<h6 class="pl-10 ml-30" style="color: black">Scan-QR to Pay</h6>
			<p class="p-10"><img class="qr_code_sm ml-30" src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code" style="background: transparent"></p>
            <?php endif; ?>
		</div>
	</div>

</div>