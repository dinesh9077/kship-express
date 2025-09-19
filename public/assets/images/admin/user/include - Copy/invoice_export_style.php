<?php include'invoice_val.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Mukta&display=swap" rel="stylesheet">

<div class="card-body p-0">
    <div class="row p-35 align-items-start" style="justify-content: space-between">
        <div class="col-3" style="width: 35%; float: left;">
            <?php if (!empty($qr_code)): ?>
                <p><img class="qr_code_sm" src="<?php echo base_url($qr_code) ?>" alt="QR Code"></p>
            <?php endif; ?>
            <?php if (empty($logo)): ?>
                <p><span class="alterlogo"><?php echo $business_name ?></span></p>
            <?php else: ?>
                <img width="100%" src="<?php echo base_url($logo) ?>" alt="Logo">
            <?php endif ?>
        </div>
        <div class="col-5 text-right" style="width: 40%; float: right; margin-top: -10px">
            <h1 class="mb-0" style="text-transform: uppercase; font-family: montserrat; font-weight: 400"><?php echo html_escape($title) ?></h1>
            
            <?php if (!empty($biz_vat_code)): ?>
            <p class="mb-0" style="color: #849194; font-family: montserrat;"><?php echo $tax_format; ?>: <span style="text-transform: uppercase"><?php echo html_escape($biz_vat_code) ?></span></p>
            <?php endif ?>
            
            <br>
            <p class="mb-0" style="font-weight: bold; font-size: 12px"><b><strong><?php echo $business_name ?></strong></b></p>
            
            <span class="mb-0 invbiz" style="font-size: 14px"><?php echo $business_address ?></span>
            <p class="mb-0" style="font-size: 14px"><?php echo html_escape($country) ?></p>
            
            <?php if (!empty($biz_number)): ?>
            <p class="mb-0" style="font-size: 14px; font-family: montserrat;"><?php echo trans('contact').' '.trans('no') ?>: <?php echo html_escape($biz_number) ?></p>
            <?php endif ?>
            
            <?php if (!empty($website_url)): ?>
            <p class="mb-0" style="font-size: 14px; font-family: montserrat;"><?php echo html_escape($website_url) ?></p>
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
                    
                    <?php if (!empty(helper_get_customer($customer_id))): ?>
                        <p class="mt-0 mb-0" style="font-weight: bold; font-size: 12px; text-transform: uppercase"><strong><?php echo helper_get_customer($customer_id)->name ?></strong></p>
                        <?php if (!empty($cus_vat_code)): ?>
                        <p class="mt-0 mb-0" style="font-size: 12px"><span><?php echo $tax_format; ?>:</span> <span style="text-transform: uppercase"><?php echo html_escape($cus_vat_code) ?></span></p>
                        <?php endif ?>
                        <p class="mt-0 mb-0" style="font-size: 12px; text-transform: uppercase"><?php echo helper_get_customer($customer_id)->address ?></p>
                        <p class="mt-0 mb-0" style="font-size: 12px; text-transform: uppercase"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_customer($customer_id)->phone ?></p>
                        <p class="mt-0 mb-0" style="font-size: 12px;"><?php echo trans('email').' '.trans('id') ?>: <?php echo helper_get_customer($customer_id)->email ?></p>
                    <?php endif ?>
                </p>
            <?php endif ?>
        </div>

        <div class="col-5 py-4 text-right" style="width: 40%; float: right; text-align: right; margin-right: -105px;">
            <table class="tables" style="width: 100%">
                <tr>
                    <td style="text-align: right; font-size: 12px; font-weight: bold; padding-bottom: 5px"><b class="mr-10"><strong><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-number');}else{echo trans('estimate-number');} ?>:</strong></b></td>
                    <td class="text-left" colspan="1" style="padding-left: 12px; font-size: 12px; padding-bottom: 5px"><?php echo html_escape($number) ?></td>
                </tr>
                <tr>
                    <td style="text-align: right; font-size: 12px; font-weight: bold; padding-bottom: 5px"><b class="mr-10"><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-date');}else{echo trans('estimate-date');} ?>:</b></td>
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
                <?php else: ?>
                    <tr>
                        <td style="text-align: right; font-size: 12px; font-weight: bold; padding-bottom: 5px"><b class="mr-10"><?php echo trans('expires-on') ?>:</b></td>
                        <td class="text-left" style="padding-left: 12px; font-size: 12px; padding-bottom: 5px">
                        <?php echo my_date_show($invoice->expire_on) ?>
                    </td>
                </tr>
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
                        <th class="border-0" style="font-weight: bold; text-align: center; padding: 5px 10px"><?php echo trans('rate') ?></th>
                        <th class="border-0" style="font-weight: bold; text-align: center; padding: 5px 10px"><?php echo trans('quantity') ?></th>
                        <th class="border-0" style="font-weight: bold; text-align: center; padding: 5px 10px"><?php echo trans('amount') ?></th>
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
                                    <td width="50%" style="border-top: 0">
                                    <?php $product_id = $this->session->userdata('item')[$i] ?>
                                    
                                    <?php if (is_numeric($product_id)) {
                                         echo helper_get_product($product_id)->name.'<br> <small>'. nl2br(helper_get_product($product_id)->details).'</small>';
                                    } else {
                                        echo html_escape($product_id);
                                    } ?>
                                    </td>
                                    <td style="border-top: 0; text-align: center"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo $this->session->userdata('price')[$i] ?></td>
                                    <td style="border-top: 0; text-align: center"><?php echo $this->session->userdata('quantity')[$i] ?></td>
                                    <td style="border-top: 0; text-align: right"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($this->session->userdata('total_price')[$i], 2) ?></td>
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
                                    <td width="50%" style="border-top: 0"><h5 style="font-weight: bold; font-size: 12px;"><?php echo html_escape($item->item_name) ?></h5> <p style="font-size: 12px"><?php echo nl2br($item->details) ?></p></td>
                                    <td style="border-top: 0; font-size: 12px; text-align: center"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->price, 2) ?></td>
                                    <td style="border-top: 0; font-size: 12px; text-align: center"><?php echo html_escape($item->qty) ?></td>
                                    <td style="border-top: 0; font-size: 12px; text-align: right; padding: 5px 20px"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->total, 2) ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    <?php endif ?>

                    <tr class="inv-pl30" style="border-top: 3px solid  #dfdede">
                        <td></td>
                        <td></td>
                        <td class="text-right" style="font-weight: bold; font-size: 12px; padding: 5px 10px"><strong><?php echo trans('sub-total') ?>:</strong></td>
                        <td style="font-size: 12px; padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total, 2) ?></span></td>
                    </tr>

                    <?php if (!empty($taxes)): ?>
                        <?php foreach ($taxes as $tax): ?>
                            <?php if ($tax != 0): ?>
                               <tr class="inv-pl30">
                                    <td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
                                    <td style="border-top: 0; font-size: 12px; padding: 5px 10px"></td>
                                    <!--<td class="text-right"><strong><?php echo get_tax_id($tax)->type_name.' ('.get_tax_id($tax)->name.'-'.get_tax_id($tax)->number.') '.get_tax_id($tax)->rate.'%' ?></strong></td>-->
                                    
                                    <td class="text-right" style="border-top: 0; font-weight: bold; font-size: 12px; padding: 5px 10px">
                                        <strong>
                                        <?php echo get_tax_id($tax)->type_name.'  ('.get_tax_id($tax)->rate.'%)' ?>:
                                    </strong></td>
                                    <td style="border-top: 0; font-size: 12px; padding: 5px 20px; text-align: right">
                                        <span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total * (get_tax_id($tax)->rate / 100), 2) ?></span>
                                    </td>
                                </tr>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>

                    <?php if (!empty($discount)): ?>
                        <tr class="inv-pl30">
                            <td style="border-top: 0; padding: 5px 10px"></td>
                            <td style="border-top: 0; padding: 5px 10px"></td>
                            <td class="text-right" style="border-top: 0; font-weight: bold; font-size: 12px; padding: 5px 10px"><strong><?php echo trans('discount') ?> <?php echo html_escape($discount) ?>% :</strong></td>
                            <td style="border-top: 0; font-size: 12px; padding: 5px 20px; text-align: right"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total * ($discount / 100), 2) ?></span></td>
                        </tr>
                    <?php endif ?>
                    <tr class="inv-pl30">
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td class="text-right" style="font-weight: bold; font-size: 12px; padding: 5px 10px"><strong><?php echo trans('grand-total') ?>:</strong></td>
                        <td style="font-size: 12px; text-align: right; padding: 5px 20px"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($grand_total, 2) ?></span></td>
                    </tr>


                    <?php foreach (get_invoice_payments($invoice->id) as $payment): ?>
                        <tr class="inv-pl30 text-dark">
                            <td style="border-top: 0; padding: 5px 10px"></td>
                            <td style="border-top: 0; padding: 5px 10px"></td>
                            <td class="text-right" width="60%" style="font-size: 12px; padding: 5px 10px">
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
    
    <div class="p-30">
        <p style="font-weight: bold; font-size: 12px"><strong>Notes / Terms</strong></p>
        <p class="" style="font-size: 12px">Authorized Signature _________________________</p>
    </div>

    <div class="text-center">
        <p class="text-centers" style="font-size: 12px"><?php echo $footer_note ?></p>
    </div>
</div>