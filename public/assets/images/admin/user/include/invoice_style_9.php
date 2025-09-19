<?php include  'invoice_val.php'; ?> 

<div class="card-body p-0">
    <div class="row p-35 align-items-start" style="justify-content: space-between">
        <div class="col-3">
            <?php if (empty($logo)): ?>
			<p><span class="alterlogo"><?php echo $business_name ?></span></p>
            <?php else: ?>
			<img width="100%" src="<?php echo base_url($logo) ?>" alt="Logo">
            <?php endif ?>
		</div>
        
        <div class="col-5 text-right">
            <h1 class="mb-0" style="text-transform: uppercase; font-weight: 400;"><?php echo html_escape($title) ?></h1>
             
			 <?php if($this->business->gst_invoice == 1):?>
			<?php if (count($tax_format) > 0 && !empty($tax_format)):
				foreach($tax_format as $key=> $taxlable){
				?>  
				 <p class="mb-0" style="color: #849194"><?php echo $taxlable; ?>: <?php echo html_escape($biz_vat_code[$key]) ?></p>
			<?php 
				}
				endif ?>
            <?php endif ?>
            <br>
            <p class="mb-0"><strong><?php echo $business_name ?></strong></p>
            
            <span class="mb-0 invbiz"><?php echo $business_address ?></span>
            <p class="mb-0"><?php echo html_escape($country) ?></p>
            <?php if (!empty($biz_number)): ?>
            <p class="mb-0"><?php echo trans('contact').' '.trans('no') ?>: <?php echo html_escape($biz_number) ?></p>   
            <?php endif ?>
            
            <?php if (!empty($website_url)): ?>
            <p class="mb-0"><?php echo html_escape($website_url) ?></p>
            <?php endif ?>
		</div>
	</div>
	
    <hr class="my-5" style="border-top-color: rgb(97 106 120 / 27%)">
	
    <div class="flex-parent-between bill_area">
        <div class="col-4 py-4 pl-30 pr-30">
			
            <?php if (isset($page) && $page == 'Bill'): ?>
			<p style="margin: 0; font-weight: 500; text-transform: uppercase; color: #a7a7a7"><?php echo trans('purchase-from') ?></p>
            <?php else: ?>
			<p style="margin: 0; font-weight: 500; text-transform: uppercase; color: #a7a7a7"><?php echo trans('bill-to') ?></p>
            <?php endif ?>
            
            <?php if (empty($customer_id)): ?>
			<p class="mb-1"><?php echo trans('empty-customer') ?></p>
            <?php else: ?>
            
			<?php if (isset($page) && $page == 'Bill'): ?>
			<?php if (!empty(helper_get_vendor($customer_id))): ?>
			<p class="mb-0" style="text-transform: uppercase;"><strong><?php echo helper_get_vendor($customer_id)->name ?></strong></p>
			<p class="mt-0 mb-0" style="text-transform: uppercase;"><?php echo helper_get_vendor($customer_id)->address ?></p>
			<p class="mt-0 mb-0"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_vendor($customer_id)->phone ?></p>
			<p class="mt-0 mb-0"><?php echo helper_get_vendor($customer_id)->email ?></p>
			<?php endif ?>
			<?php else: ?>
			
			<?php if (!empty(helper_get_customer($customer_id))): ?>
			<p class="mt-0 mb-0" style="text-transform: uppercase;"><strong><?php echo helper_get_customer($customer_id)->name ?></strong></p>
			<?php if (!empty($cus_vat_code)): ?>
			<p class="mt-0 mb-0"><?php echo $cus_tax_format; ?>: <span style="text-transform: uppercase"><?php echo html_escape($cus_vat_code) ?></span></p>
			<?php endif ?>
			<p class="mt-0 mb-0" style="text-transform: uppercase"><?php echo helper_get_customer($customer_id)->address ?>, <?php echo helper_get_customer($customer_id)->country ?></p>
			<p class="mt-0 mb-0"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_customer($customer_id)->phone ?></p>
			<p class="mt-0 mb-0"><?php echo trans('email').' '.trans('id') ?>: <?php echo helper_get_customer($customer_id)->email ?></p>
			
			<?php endif ?>
			<?php endif ?>
			
            <?php endif ?>
		</div>
        
        <div class="col-5 text-right py-4 pl-30 pr-30">
            <table class="tables" style="float: right; width: 100%">
                <tr>
                    <td style="text-align: right; font-size: 15px;"><b class="mr-10"><strong><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-number');}else if($page == 'Estimate'){echo trans('estimate-number');}else if($page == 'Delivery'){echo 'Challan Number';}else{echo trans('bill-number');} ?>:</b></strong></td>
                    <td class="text-left" colspan="1"><?php echo html_escape($number) ?></td>
				</tr>
                
                <?php if (!empty($poso_number)): ?>
                <tr>
                    <td style="text-align: right; font-size: 15px;"><b class="mr-10"><strong><?php echo trans('p.o.s.o.-number') ?>:</strong></b></td>
                    <td class="text-left" colspan="1"><?php echo html_escape($poso_number) ?></td>
				</tr>
                <?php endif ?>
                
                <tr>
                    <td style="text-align: right; font-size: 15px;"><b class="mr-10"><strong><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-date');}else if(isset($page) && $page == 'Delivery'){echo 'Delivery Date';}else{echo trans('date');} ?>:</b></strong></td>
                    <td class="text-left" colspan="1"><?php echo my_date_show($date) ?></td>
				</tr>
				
                
				
                <?php if(isset($page) && $page == 'Invoice'):?>
				<tr>
					<td style="text-align: right; font-size: 15px;"><b class="mr-10"><strong><?php echo trans('due-date') ?>:</strong></b></td>
					<td class="text-left">
						<?php echo my_date_show($payment_due) ?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td class="text-left">
						<?php if ($due_limit == 1): ?>
						<p><?php echo trans('on-receipt') ?></p>
						<?php else: ?>
						<p><?php echo trans('within') ?> <?php echo html_escape($due_limit) ?> <?php echo trans('days') ?></p>
						<?php endif ?>
					</td>
				</tr>
                <?php else: ?>
				<?php if ($invoice->expire_on != '0000-00-00'):
					if(isset($page) && $page != 'Delivery'):
				?>
				
				<tr>
					<td><b class="mr-10"><?php echo trans('expires-on') ?>:</b></td>
					<td class="text-left">
						<?php echo my_date_show($invoice->expire_on) ?>
					</td>
				</tr>
				<?php endif; ?>
				<?php endif; ?>
                <?php endif; ?>
                
                <tr>
					<td class="text-right bg-mlight" style="padding: 2%"><b class="mr-10"><strong><?php echo trans('amount-due') ?>:</strong></b></td>
					<td class="bg-mlight" style="padding: 2%; text-align: left">
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
            <table class="table m-0" style="border-bottom: 3px solid  #dfdede">
                <thead class="pre_head">
                    <tr class="pre_head_tr inv" style="background: <?php echo html_escape($color) ?>">
                        <th class="border-0" style="font-weight: bold; padding: 5px 10px"><?php echo trans('items') ?></th>
                        <th class="border-0" style="font-weight: bold; padding: 5px 10px">HSN/SAC Code</th>
                        <th class="border-0"  style="font-weight: bold; text-align: center; padding: 5px 10px"><?php echo trans('rate') ?></th>
                        <th class="border-0"  style="font-weight: bold; text-align: center; padding: 5px 10px"><?php echo trans('quantity') ?></th>
						<th class="border-0"  style="font-weight: bold; text-align: center; padding: 5px 10px">Unit</th>
                        <th class="border-0"  style="font-weight: bold; text-align: center; padding: 5px 10px"><?php echo trans('amount') ?></th>
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
						<td colspan="6" class="text-center"><?php echo trans('empty-items') ?></td>
					</tr>
					<?php else: ?>
					<?php for ($i=0; $i < $total_items; $i++) { ?>
						<tr class="inv">
							<td width="25%" style="border-top: 0">
								
								<p class="m-0" style="font-weight: bold"><?php $product_id = $this->session->userdata('item')[$i] ?></p>
								
								<?php if (is_numeric($product_id)) {
									echo '<p class="m-0" style="font-weight: bold">'.helper_get_product($product_id)->name.'</p> <p>'. nl2br(helper_get_product($product_id)->details).'</p>';
									} else {
									echo html_escape($product_id);
								} ?>
							</td>
							<td style="border-top: 0" ><?php echo $this->session->userdata('hsn_sac')[$i] ?></td>
							<td style="border-top: 0" style="text-align: right"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo $this->session->userdata('price')[$i] ?></td>
							<td style="border-top: 0" style="text-align: center"><?php echo $this->session->userdata('quantity')[$i] ?></td>
							<td style="border-top: 0" style="text-align: center"><?php if(!empty($this->session->userdata('unit')[$i])){ echo $this->session->userdata('unit')[$i]; }else {echo "";} ?></td>
							<td style="border-top: 0" style="text-align: right; padding-right:20px">
								<?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?> 
								<?php echo $this->session->userdata('total_price')[$i] ?>
							</td>
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
					<tr class="inv">
						<td width="25%" style="border-top: 0">
						    <p style="margin-bottom: 0px; font-weight: bold"><?php echo html_escape($item->item_name) ?></p>
						    <p class="m-0"><?php echo html_escape($item->serial_no) ?></p>
						    <p class="m-0"><?php echo nl2br($item->details) ?></p>
						</td>
						<td style="border-top: 0;"><p class="m-0"><?php echo $item->hsn_sac ?></p></td>
						<td style="border-top: 0; text-align: right"><p class="m-0"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->price,2) ?></p></td>
						<td style="border-top: 0; text-align: center"><p class="m-0"><?php echo html_escape($item->qty) ?></p></td>
						<td style="border-top: 0; text-align: center"><p class="m-0"><?php if(!empty($item->unit)){echo html_escape($item->unit);}else{echo "-";} ?></p></td>
						<td style="border-top: 0; text-align: right; padding-right:20px"><p class="m-0"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->total, 2) ?></p></td>
					</tr>
					
					<?php $discount_percent += $item->discount;
						endforeach  ?>
					<?php endif ?>
                    <?php endif ?>
					
				</tbody>
			</table>	
			<table class="table" style="width: 500px; margin: 0; margin-left: auto;">
                <thead>	
					
					<?php if (!empty($discount)): ?>
					<tr class="inv">
						<td class="text-right border-0" style="padding: 5px 12px"><strong>Total :</strong></td>
						<td class="border-0" style="padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total,2) ?></span></td>
					</tr>
					<tr class="inv">
						
						<td class="text-right border-0" style="border-top: 0; padding: 5px 12px"><strong><?php echo trans('discount') ?> ( <?php echo html_escape($discount_percent) ?>% ):</strong></td>
						<td class="border-0" style="border-top: 0; padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($discount, 2) ?></span></td>
					</tr>
					<tr class="inv">
                        
                        <td class="text-right border-0" style="padding: 5px 12px"><strong><?php echo trans('sub-total') ?>:</strong></td>
                        <td class="border-0" style="padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total - $discount,2) ?></span></td>
					</tr>
                    <?php else: ?>
					<tr class="inv">
                        <td class="text-right border-0" style="padding: 5px 12px"><strong><?php echo trans('sub-total') ?>:</strong></td>
                        <td class="border-0" style="padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total,2) ?></span></td>
					</tr>
                    <?php endif ?>
					
                     <?php if($this->business->invoice_tax == 1): ?>
					 <?php if (isset($page_title) && $page_title != 'Invoice Preview'): ?>

                        <?php if (!empty($taxes)): ?>
        					<?php foreach ($taxes as $tax): ?>
            					<?php if ($tax != 0): ?>
                					<tr class="inv">
                                        <td class="text-right border-0" style="padding: 5px 12px"><strong><?php echo $tax->tax_type; ?> :</strong></td>
                                        <td class="border-0" style="padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo decimal_format($tax->tax_value); ?></span></td>
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
        					        <tr class="inv">
                                        <td class="text-right border-0" style="padding: 5px 12px"><strong><?php echo $key; ?> :</strong></td>
                                        <td class="border-0" style="padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo decimal_format($tax); ?></span></td>
                					</tr>
            				<?php endif ?>
        					<?php endforeach ?>
                            <?php endif ?> 	
				
                        <?php endif ?>  
                    <?php endif ?>
                    
					
					
                    <tr class="inv">
                        
                        <td class="text-right" style="padding: 5px 12px"><strong><?php echo trans('grand-total') ?>:</strong></td>
                        <td style="padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($grand_total,2) ?></span></td>
					</tr>
					
					
                    <?php foreach (get_invoice_payments($invoice->id) as $payment): ?>
					<tr class="inv text-dark">
						
						<td class="text-right" width="60%" style="padding: 5px 12px">
							<span class="fs-13"><strong><?php echo trans('payment-on') ?> <?php echo my_date_show($payment->payment_date) ?> <?php echo trans('using') ?> <?php echo get_using_methods($payment->payment_method) ?>:</strong></span>
						</td>
						<td style="padding: 5px 20px; text-align: right">
							<span class="fs-13"><strong><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($payment->amount/$c_rate,2) ?></strong></span>
						</td>
					</tr>
                    <?php endforeach ?>
					
					
					<tr class="inv">
						
                        <td class="text-right" style="border-top: 3px solid #dfdede; padding: 5px 12px"><strong><?php echo trans('amount-due') ?>:</strong></td>
                        <td style="border-top: 3px solid #dfdede; padding: 5px 20px; text-align: right">
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
            
            <table class="p-30 table m-0">
			    <tbody>
			        <tr>
					    <td style="border-right: 1px solid rgb(97 106 120 / 27%); border-collapse: collapse; vertical-align: top !important; border-left: 0" width="80%">
							<?php 
								$activeBanking = $this->db->where('user_id',$this->session->userdata('id'))->where('bank_print_invoice',1)->get('banking')->row();
								if (!empty($activeBanking)): 
							?>
					        <p style="text-decoration: underline; font-weight: bold">Bank Details : </p>
							
							<p style="text-transform:capitalize"><span style="font-weight: bold;">Account Holder Name :</span> <?php echo html_escape($activeBanking->account_name); ?> </p>
							<p><span style="font-weight: bold">Bank name : </span><?php echo html_escape($activeBanking->bank_name); ?></p>
							<p><span style="font-weight: bold">Account Number : </span><?php echo html_escape($activeBanking->account_number); ?></p>
							<p><span style="font-weight: bold">IFSC : </span><?php echo html_escape($activeBanking->ifsc); ?></p>  
					        <?php endif; ?>
						</td>
    					<td style="border-collapse: collapse; border-right: 0; " width="20%">
							<?php if (isset($page_title) && $page_title == 'Invoice Preview'): 
								$pay_qrcode = $this->session->userdata('qr_code');		
								?>
							 <?php if (!empty($pay_qrcode)): ?>
							<p style="font-weight: bold">Scan QR-code to Pay</p>			
							<p style="text-align: center"><img src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code" style="width: 75%; max-width: 150px"></p>
                            <?php endif; ?>
							<?php else: ?>
    					    <?php if (!empty($pay_qrcode)): ?>
							<p style="font-weight: bold">Scan QR-code to Pay</p>			
							<p style="text-align: center"><img src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code" style="width: 75%; max-width: 150px"></p>
                            <?php endif; ?>
							<?php endif ?>
						</td>
					</tr>
				</tbody>
			</table>
			
            <hr class="my-0" style="border-top-color: rgb(97 106 120 / 27%);">
            
            <div class="table-responsive">
                <table class="table m-0 table-bordered">
                    <tr class="pre_head_tr inv">
                        <td width="50%" style='border-right: 1px solid rgb(97 106 120 / 27%);vertical-align: top !important'>
                            <p style="font-weight: bold; text-decoration: underline">Notes / Terms : </p>
                            <?php if (!empty($footer_note)): ?>
							<p><?php echo $footer_note ?></p>
                            <?php endif; ?>
						</td>
                        <td width="50%" style="vertical-align: top !important">
							<p style="font-weight: bold; font-size: 12px">Receiver's Signature:  _________________________</p>
							<hr>
							<p style="text-align: right; font-weight: bold; font-size: 14px">For <?php echo $business_name ?></p>
							<br>
							<br>
							<p style="text-align: right; font-weight: bold; font-size: 14px">Authorised Signatory</p>
						</td>
					</tr>    
				</table>
			</div>  
            
			<?php if (!empty($qr_code)): ?>
			    <p class="p-10"><img class="qr_code_sm ml-30" src="<?php echo base_url($qr_code) ?>" alt="QR Code"></p>
            <?php endif; ?>
			
		</div>
	</div>
	
</div>