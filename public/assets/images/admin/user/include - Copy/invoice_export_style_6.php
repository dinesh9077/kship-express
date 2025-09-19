<?php include'invoice_val.php'; ?>

<div class="card-body p-0"> 
    <div class="row p-35 align-items-start" style="justify-content: space-between">
        <div class="col-5" style="width: 40%; float: left; margin-top: -10px">
            <?php if (empty($logo)): ?>
                <span class="alterlogo"><?php echo $business_name ?></span>
            <?php else: ?>
                <img src="<?php echo base_url($logo) ?>" alt="Logo" style="max-width: 200px">
            <?php endif ?>
        </div> 
        <div class="col-3 text-right" style="width: 35%; float: right;">
            <h1 class="mb-1" style="text-transform: uppercase; font-weight: 500"><?php echo html_escape($title) ?></h1>
        </div> 
    </div>
    
    <hr style="border-top: 1px solid #f2f2f5; margin-top: 5px !important; margin-bottom: 5px !important">

    <div class="row p-35 align-items-start" style="justify-content: space-between">
        <div class="col-5 " style="width: 40%; float: left;">
            
            <?php if (isset($page) && $page == 'Bill'): ?>
			<h5 class="mb-0" style="font-weight: 400; text-transform: uppercase; color: #a7a7a7; font-size: 12px"><?php echo trans('purchase-from') ?></h5>
            <?php else: ?>
			<h5 class="mb-0" style="font-weight: 400; text-transform: uppercase; color: #a7a7a7; font-size: 12px"><?php echo trans('bill-to') ?></h5>
            <?php endif ?>
            
            <?php if (empty($customer_id)): ?>
                <p class="mb-1"><?php echo trans('empty-customer') ?></p>
            <?php else: ?>
                <p class="mb-1">
                    <?php if (!empty(helper_get_customer($customer_id))): ?>
                        <p class="mt-0 mb-0" style="font-weight: bold; font-size: 12px; text-transform: uppercase"><strong><?php echo helper_get_customer($customer_id)->name ?></strong></p>
                        <?php if (!empty($cus_vat_code)): ?>
        				<p class="mt-0 mb-0" style="font-size: 12px"><span><?php echo $cus_tax_format; ?>:</span> <span style="text-transform: uppercase"><?php echo html_escape($cus_vat_code) ?></span></p>
        				<?php endif ?>
                        <p class="mt-0 mb-0" style="font-size: 12px; text-transform: uppercase"><?php echo helper_get_customer($customer_id)->address ?></p>
        				<p class="mt-0 mb-0" style="font-size: 12px; text-transform: uppercase"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_customer($customer_id)->phone ?></p>
        				<p class="mt-0 mb-0" style="font-size: 12px;"><?php echo trans('email').' '.trans('id') ?>: <?php echo helper_get_customer($customer_id)->email ?></p>
                        
                        <?php if (!empty($cus_number)): ?>
                        <!--<p class="mt-0 mb-0"><?php echo trans('contact').' '.trans('no') ?>: <?php echo html_escape($cus_number) ?></p>-->
                        <?php endif ?>

                    <?php endif ?>
                </p>
            <?php endif ?>
        </div>
        
        <div class="col-3 text-right" style="width: 35%; float: right;">
            <p class="mb-0" style="font-weight: bold"><?php echo html_escape($business_name) ?></p>
             
			<?php if($this->business->gst_invoice == 1):?>
			<?php if (count($tax_format) > 0 && !empty($tax_format)):
				foreach($tax_format as $key=> $taxlable){
				?> 
				<p class="mb-0" style="color: #849194; font-size: 12px"><?php echo $taxlable; ?>: <span style="text-transform: uppercase"><?php echo html_escape($biz_vat_code[$key]) ?></span></p>
			<?php 
				}
				endif ?>
            <?php endif ?>
            
            <span class="mb-0 invbiz" style="font-size: 12px"><?php echo $business_address ?></span>
            <p style="font-size: 12px"><?php echo html_escape($country) ?></p>
            
            <?php if (!empty($biz_number)): ?>
            <p class="mb-0" style="font-size: 12px;"><?php echo trans('contact').' '.trans('no') ?>: <?php echo html_escape($biz_number) ?></p>
            <?php endif ?>

            
            
            <?php if (!empty($website_url)): ?>
            <p class="mb-0" style="font-size: 12px;"><?php echo html_escape($website_url) ?></p>
            <?php endif ?>

           
        </div>

        
    </div>

    <div class="row invinfo_area" style="background: <?php echo html_escape($color) ?>">
        <div class="col-12 table-responsive">
            <table class="table m-10">
                <thead class="pre_head2s">
                    <tr class="pre_head_trs" style="border: none">
                        
                        <th class="border-0" style="width: 25%; vertical-align: top">
                            <p class="text-white" style="font-size: 14px; font-weight: bold"><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-number');}else if($page == 'Estimate'){echo trans('estimate-number');}else if($page == 'Delivery'){echo 'Challan Number';}else{echo trans('bill-number');} ?></p>
                            <br>
                            <p class="lowp" style="border-top: none; font-size: 12px"><?php echo html_escape($number) ?></p>
                        </th>

                        <th class="border-0" style="width: 25%; vertical-align: top">
                            <p class="text-white" style="font-size: 14px; font-weight: bold"><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-date');}else if(isset($page) && $page == 'Delivery'){echo 'Delivery Date';}else{echo trans('date');} ?></p>
                            <br>
                            <p class="lowp" style="border-top: none; font-size: 12px"><?php echo my_date_show($date) ?></p>
                        </th>

                        <?php if (!empty($poso_number)): ?>
                        <th class="border-0" style="width: 25%; vertical-align: top">
                            <p class="text-white" style="font-size: 14px; font-weight: bold"><?php echo trans('p.o.s.o.-number') ?></p>
                            <br>
                            <p class="lowp" style="border-top: none; font-size: 12px"><?php echo html_escape($poso_number) ?></p>
                        </th>
                        <?php endif ?>

                        <?php  if(isset($page) && $page == 'Invoice'):?>
                            <th class="border-0" style="width: 25%; vertical-align: top">
                                <p class="text-white" style="font-size: 14px; font-weight: bold"><?php echo trans('due-date') ?></p>
                                <br>
                                <p class="lowp" style="border-top: none; font-size: 12px">
                                    <?php echo my_date_show($payment_due) ?>
                                
                                    <?php if ($due_limit == 1): ?>
                                        <small>(<?php echo trans('on-receipt') ?>)</small>
                                    <?php else: ?>
                                        <small>(<?php echo trans('within') ?> <?php echo html_escape($due_limit) ?> <?php echo trans('days') ?>)</small>
                                    <?php endif ?>
                                </p>
                            </th>
                        <?php else: ?>
                        <?php if ($invoice->expire_on != '0000-00-00'):
            			        if(isset($page) && $page != 'Delivery'):
            			?>
                            <th class="border-0" style="width: 25%; vertical-align: top">
                                <p class="text-white" style="font-size: 14px; font-weight: bold"><?php echo trans('expires-on') ?></p>
                                <br>
                                <p class="lowp" style="border: none; font-size: 12px">
                                    <?php echo my_date_show($invoice->expire_on) ?>
                                </p>
                            </th>
                        <?php endif; ?>
				        <?php endif; ?>
                        <?php endif; ?>

                        <th class="border-0" style="width: 25%; vertical-align: top">
                            <p class="text-white" style="font-size: 14px; font-weight: bold"><?php echo trans('amount-due') ?></p>
                            <br>
                            <p class="lowp" style="border: none; font-size: 12px">
                                <?php if ($status == 2): ?>
                                    <?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?>0.00 
							    <?php else: ?>
                                    <?php if (isset($page_title) && $page_title == 'Invoice Preview'): ?>
							            <?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo $grand_total; ?>
							        <?php else: ?>
							    <?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id), 2); ?>
							        <?php endif ?>
							    <?php endif ?>
                            </p>
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    
    <div class="row p-0 table_area">
        <div class="col-12 table-responsive">
            <table class="table m-10">
                <thead class="pre_head">
                     <tr class="pre_head_tr inv-pl30">
                        <th class="border-0" style="font-weight: bold; color: black; font-size: 12px"><?php echo trans('items') ?></th>
						<th class="border-0" style="font-weight: bold; color: black; font-size: 12px">HSN/SAC Code</th>
                        <th class="border-0" style="font-weight: bold; color: black; font-size: 12px; text-align: center;"><?php echo trans('rate') ?></th>
                        <th class="border-0" style="font-weight: bold; color: black; font-size: 12px; text-align: center;"><?php echo trans('quantity') ?></th>
						<th class="border-0" style="font-weight: bold; color: black; font-size: 12px; text-align: center;">Unit</th>
                        <th class="border-0" style="font-weight: bold; color: black; font-size: 12px; text-align: center;"><?php echo trans('amount') ?></th>
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
							<td width="25%" style="border-top: 0; font-size: 12px">
								<?php $product_id = $this->session->userdata('item')[$i] ?>
								
								<?php if (is_numeric($product_id)) {
									echo helper_get_product($product_id)->name.'<br> <small>'. nl2br(helper_get_product($product_id)->details).'</small>';
                                    } else {
									echo html_escape($product_id);
								} ?>
							</td>
							<td style="border-top: 0;">
							    <p style="font-size: 12px;"><?php echo $this->session->userdata('hsn_sac')[$i] ?></p>
							</td>
							<td style="border-top: 0; text-align: center; font-size: 12px"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo $this->session->userdata('price')[$i] ?></td>
							<td style="border-top: 0; text-align: center; font-size: 12px"><?php echo $this->session->userdata('quantity')[$i] ?></td>
							<td style="border-top: 0; text-align: center; font-size: 12px"><?php if(!empty($this->session->userdata('unit')[$i])){echo $this->session->userdata('unit')[$i];}else{echo "-";} ?></td>
							<td class="text-right" style="border-top: 0; text-align: right; font-size: 12px"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($this->session->userdata('total_price')[$i], 2) ?></td>
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
						<td width="25%" style="border-top: 0">
						    <p style="font-weight: bold; font-size: 12px;"><?php echo html_escape($item->item_name) ?></p>
						    <p style="font-size: 12px;"><?php echo html_escape($item->serial_no) ?></p>
						    <p style="font-size: 12px;"><?php echo nl2br($item->details) ?></p>
						</td>
						<td style="border-top: 0;"><p style="font-size: 12px"><?php echo $item->hsn_sac ?></p></td>
						<td style="border-top: 0; font-size: 12px; text-align: center"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->price, 2) ?></td>
						<td style="border-top: 0; font-size: 12px; text-align: center"><?php echo html_escape($item->qty) ?></td>
						<td style="border-top: 0; font-size: 12px; text-align: center"><?php if(!empty($item->unit)){echo html_escape($item->unit);}else{echo "-";} ?></td>
						<td class="text-right" style="border-top: 0; font-size: 12px; text-align: right; padding: 5px 20px"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->total, 2) ?></td>
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
                        <td class="text-right" style="font-size: 12px; padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total, 2) ?></span></td>
					</tr>
					<tr class="inv-pl30">
						<td style="border-top: 0; padding: 5px 10px"></td>
						<td style="border-top: 0; padding: 5px 10px"></td>
						<td style="border-top: 0; padding: 5px 10px"></td>
						<td style="border-top: 0; padding: 5px 10px"></td>
						<td class="text-right" style="border-top: 0; font-weight: bold; font-size: 12px; padding: 5px 10px"><strong><?php echo trans('discount') ?> <?php echo html_escape($discount) ?>% :</strong></td>
						<td class="text-right" style="border-top: 0; font-size: 12px; padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total * ($discount / 100), 2) ?></span></td>
					</tr>
					<tr class="inv-pl30" style="border-top: 3px solid #dfdede">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right" style="font-weight: bold; font-size: 12px; padding: 5px 10px"><strong><?php echo trans('sub-total') ?>:</strong></td>
                        <td style="font-size: 12px; padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total-$sub_total * ($discount / 100), 2) ?></span></td>
					</tr>
                    <?php else: ?>
                    <tr class="inv-pl30" style="border-top: 3px solid #dfdede">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right" style="font-weight: bold; font-size: 12px; padding: 5px 10px"><strong><?php echo trans('sub-total') ?>:</strong></td>
                        <td class="text-right" style="font-size: 12px; padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total, 2) ?></span></td>
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
							<td class="text-right" style="border-top: 0; font-size: 12px; padding: 5px 20px; text-align: right">
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
                        <td class="text-right" style="font-size: 12px; text-align: right; padding: 5px 20px"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($grand_total, 2) ?></span></td>
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
                        <td class="text-right" style="border-top: 3px solid #dfdede; font-size: 12px; padding: 5px 20px; text-align: right">
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
                <p style="font-size: 12px;"><strong>Notes / Terms</strong></p>
                <p><?php echo $footer_note ?></p>
                 
                 <p style="text-align: right; font-weight: bold; font-size: 14px">for <?php echo $business_name ?></p>
                 <br>
                 <br>
                 <p style="text-align: right; font-weight: bold; font-size: 14px">Authorised Signatory</p>
                <p class="">Receiver's Signature _________________________</p>
			</div>
			
            <?php if (!empty($pay_qrcode)): ?>
    			<h6 class="pl-10 ml-30" style="color: black; font-size: 12px">Scan-QR to Pay</h6>
    			<p class="p-10"><img class="qr_code_sm ml-30" style="background: transparent; font-size: 12px" src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code"></p>
            <?php endif; ?>
            
        </div>
    </div>
   
</div>