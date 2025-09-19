<?php include'invoice_val.php'; ?>

<style>
tbody tr.inv-pl30:nth-child(odd){
    background: #F4F5F5;
}

.inv-pl40 td, .inv-pl40 th {
    padding-left: 30px !important;
}
</style>

<div class="card-body p-0 overhidden">
    
    <div class="flex-parent-between" style="width: 100%; background: <?php echo html_escape($color) ?>;">
        
        <div class="col-6 py-4 pl-30 pr-30" style="width: 45%; float: left; padding: 25px;">
            <h1 class="mb-0 text-uppercase text-white" style="color: white; font-weight: 400"><?php echo html_escape($title) ?></h1>
            <p class="mb-0" style="color: white"><strong><?php echo html_escape($business_name) ?></strong></p>
            <?php if($this->business->gst_invoice == 1):?>
        	    <?php if (!empty($cus_vat_code)): ?>
                    <p class="mb-0 text-white"><?php echo $cus_tax_format; ?>: <?php echo html_escape($cus_vat_code) ?></p>
                <?php endif ?>
            <?php endif ?>
		</div>
		
        <div class="col-5 py-4" style="width: 25%; float: right; text-align: center; padding: 30px; background: <?php echo html_escape($color) ?>0f">
            <p class="text-white mb-0"><?php echo trans('amount-due') ?></p>
            <h1 class="text-white mt-0" style="font-weight: 400">
                <?php if (isset($view_type) && $view_type == 'live'): ?>
				<?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php if ($status == 2){echo "0.00";}else{echo number_format($amount_due,2);} ?>
                <?php else: ?>
				<?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?> 0.00
                <?php endif; ?>
			</h1>
		</div>
	</div>
	
	
    <div class="flex-parent-between bill_area align-items-center" style="width: 100%">
        <div class="col-6 py-4 pl-30 pr-30" style="width: 25%; float: left;">
            
            <?php if (isset($page) && $page == 'Bill'): ?>
			<p class="mb-0" style="font-weight: 400; text-transform: uppercase; color: #a7a7a7; font-size: 12px"><?php echo trans('purchase-from') ?></p>
            <?php else: ?>
			<p class="mb-0" style="font-weight: 400; text-transform: uppercase; color: #a7a7a7; font-size: 12px"><?php echo trans('bill-to') ?></p>
            <?php endif ?>
            
            
			
            <?php if (empty($customer_id)): ?>
			<p class="mb-0"><?php echo trans('empty-customer') ?></p>
            <?php else: ?>
            
				<?php if (isset($page) && $page == 'Bill'): ?>
    				<?php if (!empty(helper_get_vendor($customer_id))): ?>
    				<p class="mb-0" style="font-size: 12px;"><?php echo helper_get_vendor($customer_id)->name ?></p>
    				<p class="mt-0 mb-0" style="font-size: 12px;"><?php echo helper_get_vendor($customer_id)->address ?></p>
    				<p class="mt-0 mb-0" style="font-size: 12px;"><?php echo html_escape($country) ?></p>
    				<p class="mt-0 mb-0" style="font-size: 12px"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_vendor($customer_id)->phone ?></p>
    				<p class="mt-0 mb-0" style="font-size: 12px"><?php echo helper_get_vendor($customer_id)->email ?></p>
    				<?php endif ?>
				<?php else: ?>
				
    				<?php if (!empty(helper_get_customer($customer_id))): ?>
    				<p class="mt-0 mb-0" style="font-weight: bold; font-size: 12px; text-transform: capitalize"><strong><?php echo helper_get_customer($customer_id)->name ?></strong></p>
    				<?php if (!empty($cus_vat_code)): ?>
    				<p class="mt-0 mb-0" style="font-size: 12px"><span><?php echo $cus_tax_format; ?>:</span> <span style="text-transform: uppercase"><?php echo html_escape($cus_vat_code) ?></span></p>
    				<?php endif ?>
    				<p class="mt-0 mb-0" style="font-size: 12px; text-transform: uppercase"><?php echo helper_get_customer($customer_id)->address ?></p>
    				<p class="mt-0 mb-0"><?php echo helper_get_customer($customer_id)->country ?></p>
    				<p class="mt-0 mb-0" style="font-size: 12px; text-transform: uppercase"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_customer($customer_id)->phone ?></p>
    				<p class="mt-0 mb-0" style="font-size: 12px;"><?php echo trans('email').' '.trans('id') ?>: <?php echo helper_get_customer($customer_id)->email ?></p>
    				<?php if (!empty($cus_number)): ?>
    				    <p class="mt-0 mb-0"  style="font-size: 12px;"><?php echo trans('business').' '.trans('number') ?>: <?php echo html_escape($cus_number) ?></p>
    				<?php endif ?>
    				<?php endif ?>
				<?php endif ?>
				
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
            <table class="table mb-0">
                <thead class="pre_head">
                    <tr class="pre_head_tr2 inv-pl30">
                        <th class="border-0" style="padding: 5px 25px; border-top: 0; text-transform: uppercase; font-size: 12px"><?php echo trans('items') ?></th>
						<th class="border-0" style="padding: 5px 25px; border-top: 0; text-transform: uppercase; font-size: 12px">HSN/SAC Code</th>
                        <th class="border-0" style="text-align: center; padding: 5px 10px; border-top: 0; text-transform: uppercase; font-size: 12px"><?php echo trans('rate') ?></th>
                        <th class="border-0" style="text-align: center; padding: 5px 10px; border-top: 0; text-transform: uppercase; font-size: 12px"><?php echo trans('quantity') ?></th>
						<th class="border-0" style="text-align: center; padding: 5px 10px; border-top: 0; text-transform: uppercase; font-size: 12px">Unit</th>
                        <th class="border-0" style="text-align: center; padding: 5px 10px; border-top: 0; text-transform: uppercase; font-size: 12px"><?php echo trans('amount') ?></th>
					</tr>
				</thead>
				
                <tbody style="border-top: 3px solid #949494; border-bottom: 3px solid #949494">
					
                    <?php if (isset($page_title) && $page_title == 'Invoice Preview'): ?>
					<?php if (!empty($this->session->userdata('item'))): ?>
					<?php $total_items = count($this->session->userdata('item')); ?>
					<?php else: ?>
					<?php $total_items = 0; ?>
					<?php endif ?>
					
					<?php if (empty($total_items)): ?>
					<tr>
						<td colspan="6" class="text-center"><?php echo trans('empty-items') ?></td>
					</tr>
					<?php else: ?>
					<?php for ($i=0; $i < $total_items; $i++) { ?>
						<tr class="inv-pl30">
							<td width="25%" style="border-top: 0">
								<?php $product_id = $this->session->userdata('item')[$i] ?>
								
								<?php if (is_numeric($product_id)) {
									echo helper_get_product($product_id)->name.'<br> <small>'. nl2br(helper_get_product($product_id)->details).'</small>';
                                    } else {
									echo html_escape($product_id);
								} ?>
							</td>
							<td style="border-top: 0" ><?php echo $this->session->userdata('hsn_sac')[$i] ?></td>
							<td style="border-top: 0; text-align: center"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo $this->session->userdata('price')[$i] ?></td>
							<td style="border-top: 0; text-align: center"><?php echo $this->session->userdata('quantity')[$i] ?></td>
							<td style="border-top: 0; text-align: center"><?php if(!empty($this->session->userdata('unit')[$i])){ echo $this->session->userdata('unit')[$i];}else{echo "-" ;} ?></td>
							<td style="border-top: 0; text-align: right"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($this->session->userdata('total_price')[$i], 2) ?></td>
						</tr>
					<?php } ?>
					<?php endif ?>
					
                    <?php else: ?>
					
					<?php $items = helper_get_invoice_items($invoice->id) ?>
					<?php if (empty($items)): ?>
					<tr>
						<td colspan="6" class="text-center"><?php echo trans('empty-items') ?></td>
					</tr>
					<?php else: ?>
					<?php $i=0; foreach ($items as $item): ?>
					<tr class="inv-pl30" style=<?php if($i%2==0):?>"background: #F4F5F5;"<?php else:?> "background: white;" <?php endif ?>>
						<td width="25%" style="border-top: 0">
						    <p style="font-size: 12px; font-weight: bold"><?php echo html_escape($item->item_name) ?></p> 
						    <p style="font-size: 12px; font-weight: 400"><?php echo html_escape($item->serial_no) ?></p>
						    <p style="font-size: 12px; font-weight: 400"><?php echo html_escape($item->details) ?></p>
						</td>
						<td style="border-top: 0; font-size: 12px;"><p style="margin: 0"><?php echo html_escape($item->hsn_sac) ?></p></td>
						<td style="border-top: 0; font-size: 12px; text-align: center"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->price, 2) ?></td>
						<td style="border-top: 0; font-size: 12px; text-align: center"><?php echo html_escape($item->qty) ?></td>
						<td style="border-top: 0; font-size: 12px; text-align: center"><?php if(!empty($item->unit)){echo html_escape($item->unit);}else{echo "-";} ?></td>
						<td style="border-top: 0; font-size: 12px; text-align: right; padding: 5px 20px"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->total, 2) ?></td>
					</tr>
					<?php $i++; endforeach ?>
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
					
                   <?php if($this->business->invoice_tax == 1): ?>
					 <?php if (isset($page_title) && $page_title != 'Invoice Preview'): ?>

                        <?php if (!empty($taxes)): ?>
        					<?php foreach ($taxes as $tax): ?>
            					<?php if ($tax != 0): ?>
					<tr class="inv-pl30">
						<td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
						<td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
						<td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
						<td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
						<td class="text-right" style="border-top: 0; font-weight: bold; font-size: 12px; padding: 5px 10px">
							<strong>
								<?php echo $tax->tax_type; ?> :
							</strong>
						</td>
						<td style="border-top: 0; font-size: 12px; padding: 5px 20px; text-align: right">
							<span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo decimal_format($tax->tax_value); ?></span>
						</td>
					</tr>
					 <?php endif ?>
        					<?php endforeach ?>
                        <?php endif ?> 
                    <?php else: ?>
                    <?php 
						$taxes = ($this->session->userdata('tax_value'))?$this->session->userdata('tax_value'):''; 
						if (!empty($taxes)): 
						?>
					<?php foreach ($taxes as $key => $tax): ?>
					<?php if ($tax != 0): ?>
					    <tr class="inv-pl30">
						<td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
						<td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
						<td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
						<td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
						<td class="text-right" style="border-top: 0; font-weight: bold; font-size: 12px; padding: 5px 10px">
							<strong>
								<?php echo $key; ?> :
							</strong>
						</td>
						<td style="border-top: 0; font-size: 12px; padding: 5px 20px; text-align: right">
							<span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo decimal_format($tax); ?></span>
						</td>
					</tr>
					
					<?php endif ?>
					<?php endforeach ?>
                    <?php endif ?> 
					
					
                    <?php endif ?>
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
						<td>
							<span class="fs-13" style="font-size: 12px; padding: 5px 20px; text-align: right"><strong><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($payment->amount,2) ?></strong></span>
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
		</div>
	</div>
    
	<table class="p-30 table m-0">
			    <tbody>
			        <tr>
					    <td style="border-right: 1px solid #bdc1c7; border-collapse: collapse; vertical-align: top !important; border-left: 0" width="80%">
							<?php 
								$activeBanking = $this->db->where('user_id',$this->session->userdata('id'))->where('bank_print_invoice',1)->get('banking')->row();
								if (!empty($activeBanking)): 
							?>
					        <p style="font-family: opensans;text-decoration: underline; font-weight: bold; font-size: 12px">Bank Details : </p>
							
							<p style="font-family: opensans;text-transform:capitalize;  font-size: 12px"><span style="font-weight: bold;">Account Holder Name :</span> <?php echo html_escape($activeBanking->account_name); ?> </p>
							<p style="font-family: opensans;font-size: 12px"><span style="font-weight: bold;">Bank name : </span><?php echo html_escape($activeBanking->bank_name); ?></p>
							<p style="font-family: opensans;font-size: 12px"><span style="font-weight: bold;">Account Number : </span><?php echo html_escape($activeBanking->account_number); ?></p>
							<p style="font-family: opensans;font-size: 12px"><span style="font-weight: bold;">IFSC : </span><?php echo html_escape($activeBanking->ifsc); ?></p>  
					        <?php endif; ?>
						</td>
    					<td style="border-collapse: collapse; border-right: 0; " width="20%">
							<?php if (isset($page_title) && $page_title == 'Invoice Preview'): 
								$pay_qrcode = $this->session->userdata('qr_code');		
								?>
							 <?php if (!empty($pay_qrcode)): ?>
							<p style="font-family: opensans;font-weight: bold; font-size: 12px">Scan QR-code to Pay</p>			
							<p style="font-family: opensans;text-align: center"><img src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code" style="width: 75%; max-width: 150px"></p>
                            <?php endif; ?>
							<?php else: ?>
    					    <?php if (!empty($pay_qrcode)): ?>
							<p style="font-family: opensans;font-weight: bold; font-size: 12px">Scan QR-code to Pay</p>			
							<p style="font-family: opensans;text-align: center"><img src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code" style="width: 75%; max-width: 150px"></p>
                            <?php endif; ?>
							<?php endif ?>
						</td>
					</tr>
				</tbody>
			</table>
			<hr class="my-0" style="border-top-color: rgb(97 106 120 / 27%); margin: 0">
			<div class="table-responsive" style="margin: 0">
                <table class="table m-0 table-bordered">
                    <tr class="pre_head_tr inv">
                        <td width="50%" style='border-right: 1px solid #bdc1c7; vertical-align: top !important'>
                            <p style="font-family: opensans;font-weight: bold; text-decoration: underline; font-size: 12px">Notes / Terms : </p>
                            <?php if (!empty($footer_note)): ?>
							<p style="font-family: opensans;font-size: 12px"><?php echo $footer_note ?></p>
                            <?php endif; ?>
						</td>
                        <td width="50%" style="vertical-align: top !important">
							<p style="font-family: opensans;font-weight: bold; font-size: 12px">Receiver's Signature:  _________________________</p>
							<hr>
							<p style="font-family: opensans;text-align: right; font-weight: bold; font-size: 12px">For <?php echo $business_name ?></p>
							<br>
							<br>
							<p style="font-family: opensans;text-align: right; font-weight: bold; font-size: 12px">Authorised Signatory</p>
						</td>
					</tr>    
				</table>
			</div>
	
    <hr class="my-5" style="border-top-color: rgb(98 99 101 / 20%); margin: 0">
    
    <div class="flex-parent-between" style="width: 100%; justify-content: space-between; padding: 15px">
       
        <?php if (!empty($logo)): ?>
        <div class="col-2" style="width: 25%; float: left">
            <img src="<?php echo base_url($logo) ?>" alt="" style="max-width: 200px">
		</div>
        <?php endif ?>
        
        <div class="col-3" style="width: 40%; float: left">
            <p class="mb-0" style="font-weight: bold; font-size: 12px"><strong><?php echo html_escape($business_name) ?></strong></p>
           
			<?php if($this->business->gst_invoice == 1):?>
			<?php if (count($tax_format) > 0 && !empty($tax_format)):
				foreach($tax_format as $key=> $taxlable){
				?>
 				 <p class="mb-0" style="font-size: 12px"><?php echo $taxlable; ?>: <span style="text-transform: uppercase"><?php echo html_escape($biz_vat_code[$key]) ?></span></p>
			<?php 
				}
				endif ?>
            <?php endif ?>
            <span class="mt-0 mb-0 invbiz" style="font-size: 12px"><?php echo $business_address ?></span>
            <p class="" style="font-size: 12px"><?php echo html_escape($country) ?></p>
		</div>
        
        <div class="col-2" style="text-align: right; width: 20%; float: right">
            <p style="font-weight: bold; font-size:12px; margin: 0">Contact Information</p>
            <?php if (!empty($biz_number)): ?>
            <p class="mb-0" style="font-size: 12px"><?php echo trans('contact').' '.trans('no') ?>.: <?php echo html_escape($biz_number) ?></p>
            <?php endif ?>
            
            <?php if (!empty($website_url)): ?>
            <p class="mb-0" style="font-size: 12px"><?php echo html_escape($website_url) ?></p>
            <?php endif ?>
		</div>
	</div>
    
</div>