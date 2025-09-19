<?php include'invoice_val.php'; ?>

<style>
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th
    .table>thead>tr>th{
	border: 0;
    }
    .table>thead>tr>th{
	border-bottom: 2px solid #949494;
    }
    
    tbody tr.inv-pl30:nth-child(odd) {
        background: #F2F2F2;
    }
    
    .inv-pl40 td, .inv-pl40 th {
	padding-left: 30px !important;
    }
</style>

<div class="card-body p-0 overhidden">
    
	
    <div class="row invtem_top_3 m-0" style="background: <?php echo html_escape($color) ?>; align-items: inherit">
        <div class="col-9 text-left" style="padding: 25px">
            <h1 class="mb-1 text-uppercase text-white" style="font-weight: 400"><?php echo html_escape($title) ?></h1>
            <p class="mb-0"><?php echo html_escape($business_name) ?></p>
            <?php if($this->business->gst_invoice == 1):?>
        					<?php if (!empty($cus_vat_code)): ?>
            <p class="text-white"><?php echo $cus_tax_format; ?>: <?php echo html_escape($cus_vat_code) ?></p>
            <?php endif ?>
            <?php endif ?>
		</div>
		
		
        <div class="col-3 text-center text-white" style="background: rgb(0 0 0 / 22%); padding: 25px">
            <p class="mt-25 text-white mb-0"><?php echo trans('amount-due') ?></p>
            <h1 class="text-white mt-0" style="font-weight: 400">
                <?php if (isset($view_type) && $view_type == 'live'): ?>
				<?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php if ($status == 2){echo "0.00";}else{echo number_format($amount_due,2);} ?>
                <?php else: ?>
				<?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?> 0.00
                <?php endif; ?>
			</h1>
		</div>
	</div>
	
    <div class="rows bill_area flex-parent-between">
        <div class="col-4">
            <?php if (isset($page) && $page == 'Bill'): ?>
			<h5 class="font-weight-bold" style="text-transform: uppercase"><?php echo trans('purchase-from') ?></h5>
            <?php else: ?>
			<h5 class="font-weight-bold" style="text-transform: uppercase"><?php echo trans('bill-to') ?></h5>
            <?php endif ?>
            
            <?php if (empty($customer_id)): ?>
			<p class="mb-1"><?php echo trans('empty-customer') ?></p>
            <?php else: ?>
			<p class="mb-1">
				
				<?php if (isset($page) && $page == 'Bill'): ?>
				<?php if (!empty(helper_get_vendor($customer_id))): ?>
				<p class="mb-0" style="text-transform: uppercase;"><strong><?php echo helper_get_vendor($customer_id)->name ?></strong></p>
				<p class="mt-0 mb-0" style="text-transform: uppercase;"><?php echo helper_get_vendor($customer_id)->address ?></p>
				<p class="mt-0 mb-0" style="text-transform: uppercase;"><?php echo html_escape($country) ?></p>
				<p class="mt-0 mb-0"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_vendor($customer_id)->phone ?></p>
				<p class="mt-0 mb-0"><?php echo helper_get_vendor($customer_id)->email ?></p>
				<?php endif ?>
				<?php else: ?>
				
				<?php if (!empty(helper_get_customer($customer_id))): ?>
				<p class="mb-0" style="text-transform: uppercase"><strong><?php echo helper_get_customer($customer_id)->name ?></strong></p>
				<?php if (!empty($cus_vat_code)): ?>
				<p class="mt-0 mb-0"><?php echo $cus_tax_format; ?>: <span style="text-transform: uppercase"><?php echo html_escape($cus_vat_code) ?></span></p>
				<?php endif ?>
				<p class="mt-0 mb-0"><?php echo helper_get_customer($customer_id)->address ?></p> 
				<p class="mt-0 mb-0"><?php echo helper_get_customer($customer_id)->country ?></p>
				<p class="mt-0 mb-0"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_customer($customer_id)->phone ?></p>
				<p class="mt-0 mb-0"><?php echo trans('email').' '.trans('id') ?>: <?php echo helper_get_customer($customer_id)->email ?></p>
				<?php if (!empty($cus_number)): ?>
				<p class="mt-0 mb-0"><?php echo trans('business').' '.trans('number') ?>: <?php echo html_escape($cus_number) ?></p>
				<?php endif ?>
				
				<?php endif ?>
				<?php endif ?>
			</p>
            <?php endif ?>
		</div>
		
        <div class="col-5 text-right">
            <table class="tables" style="float: right">
                <tr>
                    <td><strong class="mr-10"><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-number');}else if($page == 'Estimate'){echo trans('estimate-number');}else if($page == 'Delivery'){echo 'Challan Number';}else{echo trans('bill-number');} ?>:</strong></td>
                    <td class="text-left" colspan="1"><?php echo html_escape($number) ?></td>
				</tr>
				 
                <tr>
                    <td><strong class="mr-10"><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-date');}else if(isset($page) && $page == 'Delivery'){echo 'Delivery Date';}else{echo trans('date');} ?>:</strong></td>
                    <td class="text-left" colspan="1"><?php echo my_date_show($date) ?></td>
				</tr>
				
                <?php if (!empty($poso_number)): ?>
                <tr>
                    <td><strong class="mr-10"><?php echo trans('p.o.s.o.-number') ?>:</strong></td>
                    <td class="text-left" colspan="1"><?php echo html_escape($poso_number) ?></td>
				</tr>
                <?php endif ?>
				
                <?php if(isset($page) && $page == 'Invoice'):?>
				<tr>
					<td><strong class="mr-10"><?php echo trans('due-date') ?>:</strong></td>
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
                <?php else:
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
			</table>
		</div>
	</div>
	
    <div class="row p-0 table_area">
        <div class="col-12 table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr class="pre_head_tr2 inv-pl30">
                        <th class="text-muted" style="font-weight: 400; text-transform: uppercase"><?php echo trans('items') ?></th>
                        <th class="text-muted" style="font-weight: 400; text-transform: uppercase">HSN/SAC Code</th>
                        <th class="text-muted text-right" style="font-weight: 400; text-transform: uppercase"><?php echo trans('price') ?></th>
                        <th class="text-muted text-center" style="font-weight: 400; text-transform: uppercase"><?php echo trans('quantity') ?></th>
                        <th class="text-muted" style="font-weight: 400; text-transform: uppercase; text-align: center">Unit</th>
                        <th class="text-muted text-right" style="font-weight: 400; text-transform: uppercase"><?php echo trans('amount') ?></th>
					</tr>
				</thead>
                <tbody style="border-top: 2px solid #949494; border-bottom: 2px solid #949494">
					
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
							<td width="25%">
								<?php $product_id = $this->session->userdata('item')[$i] ?>
								
								<?php if (is_numeric($product_id)) {
									echo helper_get_product($product_id)->name.'<br> <small>'. nl2br(helper_get_product($product_id)->details).'</small>';
                                    } else {
									echo html_escape($product_id);
								} ?>
							</td>
							<td style="border-top: 0" ><?php echo $this->session->userdata('hsn_sac')[$i] ?></td>
							<td><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo $this->session->userdata('price')[$i] ?></td>
							<td><?php echo $this->session->userdata('quantity')[$i] ?></td>
							<td><?php if(!empty($this->session->userdata('unit')[$i])){echo $this->session->userdata('unit')[$i];}else {echo "-";} ?></td>
							<td><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($this->session->userdata('total_price')[$i], 2) ?></td>
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
    					<?php foreach ($items as $item): ?>
        					<tr class="inv-pl30">
        						<td width="25%">
        							<p style="margin-bottom: 0px; font-weight: bold"><?php echo html_escape($item->item_name) ?></p>
        							<p class="m-0"><?php echo html_escape($item->serial_no) ?></p>
        							<p class="m-0"><?php echo nl2br($item->details) ?></p>
        						</td>
        						<td style="border-top: 0;"><p class="m-0"><?php echo $item->hsn_sac ?></p></td>
        						<td class="text-right"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->price,2) ?></td>
        						<td class="text-center"><?php echo html_escape($item->qty) ?></td>
        						<td style="text-align: center"><?php if(!empty($item->unit)){echo html_escape($item->unit);}else{echo "-";} ?></td>
        						<td style="text-align: right"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->total, 2) ?></td>
        					</tr>
    					<?php endforeach ?>
					<?php endif ?>
                    <?php endif ?>
					
				</tbody>
			</table>
            
            <table class="table">
                <tbody>
					<?php if (!empty($discount)): ?>
					<tr class="inv-pl40">
                        <td style="width: 25%"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><strong>Total :</strong></td>
                        <td class="text-right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total,2) ?></span></td>
					</tr>
					<tr class="inv-pl40">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-right" ><strong><?php echo trans('discount') ?> (<?php echo html_escape($discount) ?>%):</strong></td>
						<td class="text-right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total * ($discount / 100), 2) ?></span></td>
					</tr>
					<tr class="inv-pl40">
                        <td style="width: 25%"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><strong><?php echo trans('sub-total') ?>:</strong></td>
                        <td class="text-right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total-$sub_total * ($discount / 100),2) ?></span></td>
					</tr>
                    <?php else: ?>
					<tr class="inv-pl40">
						<td style="width: 25%"></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-right"><strong><?php echo trans('sub-total') ?>:</strong></td>
						<td class="text-right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total,2) ?></span></td>
					</tr>
					<?php endif ?>
					
					
                    <?php if($this->business->invoice_tax == 1): ?>
					 <?php if (isset($page_title) && $page_title != 'Invoice Preview'): ?>

                        <?php if (!empty($taxes)): ?>
        					<?php foreach ($taxes as $tax): ?>
            					<?php if ($tax != 0): ?>
            					<tr class="inv-pl40">
            						<td></td>
            						<td></td>
            						<td></td>
            						<td></td>
            						<td></td>
            						<td></td>
            						<td class="text-right"><strong><?php echo $tax->tax_type; ?> :</strong>
            						</td>
            						<td class="text-right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo decimal_format($tax->tax_value); ?></span></td>
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
					    <tr class="inv-pl40">
    						<td></td>
    						<td></td>
    						<td></td>
    						<td></td>
    						<td></td>
    						<td></td>
    						<td class="text-right"><strong><?php echo $key; ?> :</strong>
    						</td>
    						<td class="text-right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo decimal_format($tax); ?></span></td>
    					</tr>
            		<?php endif ?>
					<?php endforeach ?>
                    <?php endif ?> 
					
					
                    <?php endif ?>
                    <?php endif ?>
					
                    <tr class="inv-pl40">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right" style="border-top: 1px solid #949494"><strong><?php echo trans('grand-total') ?>:</strong></td>
                        <td style="border-top: 1px solid #949494" class="text-right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($grand_total, 2) ?></span></td>
					</tr>
					
                    <?php foreach (get_invoice_payments($invoice->id) as $payment): ?>
					<tr class="inv-pl40 text-dark">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-right" width="60%">
							<span class="fs-13"><strong><?php echo trans('payment-on') ?> <?php echo my_date_show($payment->payment_date) ?> <?php echo trans('using') ?> <?php echo get_using_methods($payment->payment_method) ?>:</strong></span>
						</td>
						<td class="text-right">
							<span class="fs-13"><strong><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($payment->amount,2) ?></strong></span>
						</td>
					</tr>
                    <?php endforeach ?>
                    
                    <tr class="inv-pl40">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right" style="border-top: 2px solid #b2b2b2"><b><strong><?php echo trans('amount-due')?>:</strong></b></td>
                        <td style="border-top: 2px solid #b2b2b2" class="text-right">
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
		</div>
	</div>
    
    <hr class="my-0" style="border-top-color: rgb(97 106 120 / 27%);">
    
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
	
	
    <hr class="my-5" style="border-top-color: rgb(98 99 101 / 20%)">
	
    <div class="row p-15" style="justify-content: space-between; margin: 0; width: 100%">
        <?php if (!empty($logo)): ?>
        <div class="col-3">
            <img src="<?php echo base_url($logo) ?>" alt="" style="max-height: 200px">
		</div>
        <?php endif ?>
		
        <div class="col-4">
            <p class="mb-0"><strong><?php echo html_escape($business_name) ?></strong></p>
           
			<?php if($this->business->gst_invoice == 1):?>
			<?php if (count($tax_format) > 0 && !empty($tax_format)):
				foreach($tax_format as $key=> $taxlable){
				?>
				<p  class="mb-0"><strong><?php echo $taxlable; ?>: </strong> <?php echo html_escape($biz_vat_code[$key]) ?></p>
			<?php 
				}
				endif ?>
            <?php endif ?>
            <span class="mt-0 mb-0 invbiz"><?php echo $business_address ?></span>
            <p class=""><?php echo html_escape($country) ?></p>
		</div>
        
        <div class="col-3" style="text-align: right">
            <p class="m-0"><strong>Contact Information</strong></p>
            <?php if (!empty($biz_number)): ?>
            <p class="mb-0"><?php echo trans('contact').' '.trans('no') ?>.: <?php echo html_escape($biz_number) ?></p>
            <?php endif ?>
            
            <?php if (!empty($website_url)): ?>
            <p class="mb-0"><?php echo html_escape($website_url) ?></p>
            <?php endif ?>
		</div>
        
        
	</div>
</div>