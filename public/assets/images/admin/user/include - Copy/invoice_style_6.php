<?php include'invoice_val.php'; ?>
<style>
.inv6 img{
    width: 100%;
}
</style>
<div class="card-body p-0">

    <div class="flex-parent-between inv6-header">
        <div class="inv6">
            <?php if (empty($logo)): ?>
                <span class="alterlogo"><?php echo $business_name ?></span>
            <?php else: ?>
                <img src="<?php echo base_url($logo) ?>" alt="Logo" style="max-width: 200px">
            <?php endif ?>
        </div>
   
        <div class="inv6">
            <h1 class="mb-1 text-uppercase" style="font-weight: 500"><?php echo html_escape($title) ?></h1>
            <p><?php echo html_escape($summary) ?></p>
        </div>
    </div>

    <hr class="my-5">

    <div class="flex-parent-between invtem_top_2">

        <div class="col-5 text-left">
           
            <p class="m-0" style="text-transform: uppercase"><?php echo trans('bill-to') ?></p>
            
            <?php if (empty($customer_id)): ?>
                <p class="mb-1"><?php echo trans('empty-customer') ?></p>
            <?php else: ?>
                <?php if (!empty(helper_get_customer($customer_id))): ?>
                    <p class="mb-0"><strong><?php echo helper_get_customer($customer_id)->name ?></strong></p>
                    <?php if (!empty($cus_vat_code)): ?>
                    <p class="mt-0"><?php echo $cus_tax_format; ?>: <?php echo html_escape($cus_vat_code) ?></p>
                    <?php endif ?>
                    <p class="mt-0 mb-0"><?php echo helper_get_customer($customer_id)->address ?> <?php echo helper_get_customer($customer_id)->country ?></p>
                    <p class="mt-0 mb-0"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_customer($customer_id)->phone ?></p>
                <?php endif ?>
            <?php endif ?>
        </div>

        <div class="col-6 text-right">
            <p class="mb-0"><strong><?php echo html_escape($business_name) ?></strong></p>
         
			<?php if($this->business->gst_invoice == 1):?>
			<?php if (count($tax_format) > 0 && !empty($tax_format)):
				foreach($tax_format as $key=> $taxlable){
				?>  
				<p class="mb-0"><?php echo $taxlable; ?>: <?php echo html_escape($biz_vat_code[$key]) ?></p>
			<?php 
				}
				endif ?>
            <?php endif ?>
			
            <span class="mt-0 mb-0 invbiz"><?php echo $business_address ?></span>
            <p class=""><?php echo html_escape($country) ?></p>
            
            <?php if (!empty($biz_number)): ?>
            <p class="mb-0"><?php echo trans('contact').' '.trans('no') ?>: <?php echo html_escape($biz_number) ?></p>
            <?php endif ?>

            <?php if (!empty($website_url)): ?>
            <p class="mb-0"><?php echo html_escape($website_url) ?></p>
            <?php endif ?>

            
            
        </div>

    </div>

   
    <div class="row invinfo6_area" style="background: <?php echo html_escape($color) ?>">
        <div class="col-12 table-responsive">
            <table class="table">
                <thead class="pre_head2s">
                    <tr class="pre_head_trs">
                        
                        <th class="border-0 p-0">
                          <p class="text-white"><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-number');}else if($page == 'Estimate'){echo trans('estimate-number');}else if($page == 'Delivery'){echo 'Challan Number';}else{echo trans('bill-number');} ?></p>
                         <p class="text-white m-0" style="font-weight: normal"><?php echo html_escape($number) ?></p>
                        </th>

                        <th class="border-0">
                            <p class="text-white"><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-date');}else if(isset($page) && $page == 'Delivery'){echo 'Delivery Date';}else{echo trans('date');} ?></p>
                            <p class="text-white m-0" style="font-weight: normal"><?php echo my_date_show($date) ?></p>
                        </th>

                        <?php if (!empty($poso_number)): ?>
                        <th class="border-0 p-0">
                            <p class="mb-5"><?php echo trans('p.o.s.o.-number') ?></p>
                            <p class="text-white" style="font-weight: normal"><?php echo html_escape($poso_number) ?></p>
                        </th>
                        <?php endif ?>

                        <?php if(isset($page) && $page == 'Invoice'):?>
                            <th class="border-0">
                                <p class="text-white"><?php echo trans('due-date') ?></p>
                                <p class="text-white m-0"  style="font-weight: normal">
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
                            <th class="border-0">
                                <p class="text-white"><?php echo trans('expires-on') ?></p>
                                <p class="text-white m-0" style="font-weight: normal">
                                    <?php echo my_date_show($invoice->expire_on) ?>
                                </p>
                            </th>
                        <?php endif; ?>
				        <?php endif; ?>
                        <?php endif; ?>

                        <th class="border-0">
                            <p class="text-white"><?php echo trans('amount-due') ?></p>
                            <p class="text-white m-0" style="font-weight: normal">
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



    <div class="row p-20 table_area m-0" style="width: 100%">
        <div class="col-12 table-responsive">
             <table class="table m-0">
                <thead class="pre_head5">
                    <tr class="pre_head_tr5">
                        <th class="border-0" style="color: black"><?php echo trans('items') ?></th>
                        <th class="border-0" style="color: black">HSN/SAC Code</th>
                        <th class="border-0 text-right" style="color: black"><?php echo trans('rate') ?></th>
                        <th class="border-0 text-center" style="color: black"><?php echo trans('quantity') ?></th>
                        <th class="border-0 text-center" style="color: black">Unit</th>
                        <th class="border-0 text-right" style="color: black"><?php echo trans('amount') ?></th>
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
                                <td colspan="4" class="text-center"><?php echo trans('empty-items') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php for ($i=0; $i < $total_items; $i++) { ?>
                                <tr>
                                    <td width="40%">
                                    <?php $product_id = $this->session->userdata('item')[$i] ?>
                                    
                                    <?php if (is_numeric($product_id)) {
                                        echo '<p style="font-weight: bold">'.helper_get_product($product_id)->name.'</p> <p>'. nl2br(helper_get_product($product_id)->details).'</p>';
                                    } else {
                                        echo html_escape($product_id);
                                    } ?>
                                    </td>
                                    <td><?php echo $this->session->userdata('hsn_sac')[$i] ?></td>
                                    <td class="text-right"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo $this->session->userdata('price')[$i] ?></td>
                                    <td class="text-center"><?php echo $this->session->userdata('quantity')[$i] ?></td>
                                    <td class="text-center"><?php if(!empty($this->session->userdata('unit')[$i])){echo $this->session->userdata('unit')[$i];}else{echo "-";} ?></td>
                                    <td class="text-right"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($this->session->userdata('total_price')[$i], 2) ?></td>
                                </tr>
                            <?php } ?>
                        <?php endif ?>

                    <?php else: ?>

                        <?php $items = helper_get_invoice_items($invoice->id) ?>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="4" class="text-center"><?php echo trans('empty-items') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td width="40%">
                                        <p class="m-0" style="font-weight: bold"><?php echo html_escape($item->item_name) ?></p>
                                        <p class="m-0"><?php echo html_escape($item->serial_no) ?></p> 
                                        <p class="m-0"><?php echo nl2br($item->details) ?></p>
                                    </td>
                                    <td><?php echo $item->hsn_sac ?></td>
                                    <td class="text-right"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->price,2) ?></td>
                                    <td class="text-center"><?php echo html_escape($item->qty) ?></td>
                                    <td class="text-center"><?php if(!empty($item->unit)){echo html_escape($item->unit);}else{echo "-";} ?></td>
                                    <td class="text-right"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->total, 2) ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    <?php endif ?>
                </tbody>
            </table>
            <table class="table m-0">
                <tbody>
                    <tr>
                        <td width="40%"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><strong><?php echo trans('sub-total') ?></strong></td>
                        <td class="text-right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total,2) ?></span></td>
                    </tr>
                    
                    <?php if (!empty($discount)): ?>
                        <tr>
                            <td width="40%"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right"><strong><?php echo trans('discount') ?> <?php echo html_escape($discount) ?>% :</strong></td>
                            <td class="text-right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total * ($discount / 100), 2) ?></span></td>
                        </tr>
                    <?php endif ?>

                    <?php if (!empty($taxes)): ?>
                        <?php foreach ($taxes as $tax): ?>
                            <?php if ($tax != 0): ?>
                                <tr>
                                    <td width="40%"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right"><strong><?php echo get_tax_id($tax)->type_name.'  ('.get_tax_id($tax)->rate.'%)' ?>:</strong></td>
                                    <?php 
            						    if (!empty($discount)){
            						        $gstamount = $sub_total-$sub_total * ($discount / 100);
            						    }else{
            						        $gstamount = $sub_total;
            						    } 
                    				?>
                                    <td class="text-right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($gstamount * (get_tax_id($tax)->rate / 100), 2) ?></span></td>
                                </tr>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>

                    
                    <tr>
                        <td width="40%" style="border: 0"></td>
                        <td style="border: 0"></td>
                        <td style="border: 0"></td>
                        <td style="border: 0"></td>
                        <td class="text-right"><strong><?php echo trans('grand-total') ?></strong></td>
                        <td class="text-right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($grand_total,2) ?></span></td>
                    </tr>

                    <?php foreach (get_invoice_payments($invoice->id) as $payment): ?>
                        <tr class="inv-pl30 text-dark">
                            <td width="40%" style="border: 0"></td>
                            <td style="border: 0"></td>
                            <td style="border: 0"></td>
                            <td style="border: 0"></td>
                            <td class="text-right" width="60%">
                                <span class="fs-13"><strong><?php echo trans('payment-on') ?> <?php echo my_date_show($payment->payment_date) ?> <?php echo trans('using') ?> <?php echo get_using_methods($payment->payment_method) ?></strong></span>
                            </td>
                            <td class="text-right">
                                <span class="fs-13"><strong><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($payment->amount,2) ?></strong></span>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    
                    <tr>
                        <td width="40%" style="border: 0"></td>
                        <td style="border: 0"></td>
                        <td style="border: 0"></td>
                        <td style="border: 0"></td>
                        <td class="text-right"><strong><?php echo trans('amount-due') ?>:</strong></td>
                        <td class="text-right"><span>
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
            
            <div class="p-30">
                <p><strong>Notes / Terms</strong></p>
                <p><?php echo $footer_note ?></p>
                 
                 <p style="text-align: right; font-weight: bold; font-size: 14px">for <?php echo $business_name ?></p>
                 <br>
                 <br>
                 <p style="text-align: right; font-weight: bold; font-size: 14px">Authorised Signatory</p>
                <p class="">Receiver's Signature _________________________</p>
			</div>
			
            <?php if (!empty($qr_code)): ?>
			    <p class="p-10"><img class="qr_code_sm ml-30" src="<?php echo base_url($qr_code) ?>" alt="QR Code"></p>
            <?php endif; ?> 
            
			<?php if (!empty($pay_qrcode)): ?>
    			<h6 class="ml-40">Scan QR-code to Pay</h6>			
    			<p class="p-10"><img class="qr_code_sm ml-30" src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code"></p>
            <?php endif; ?> 
            
        </div>
    </div>

</div>