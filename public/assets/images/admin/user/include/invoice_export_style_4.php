<?php include'invoice_val.php'; ?>
<style>
    @font-face {
    font-family: "georgia";
	src: url("assets/admin/fonts/georgia/Georgia.ttf") format("truetype");
	font-weight: normal;
	font-style: italic;
	font-display: swap
  }
  body{
      font-family: "georgia";
  }
</style>
<div class="card-body p-0">
    <div class="row p-35 align-items-start" style="justify-content: space-between;">
        <div class="col-3" style="width: 35%; float: left;">
            <?php if (empty($logo)): ?>
                <p><span class="alterlogo"><?php echo $business_name ?></span></p>
            <?php else: ?>
                <img width="100%" src="<?php echo base_url($logo) ?>" alt="Logo" style="max-width: 200px">
            <?php endif ?>
        </div>
        <div class="col-5 text-right" style="width: 40%; float: right; margin-top: -10px">
            <p style="font-weight: bold; font-size: 13px; font-family: georgia; color: black; margin: 0"><b><strong><?php echo $business_name ?></strong></b></p>
         
			<?php if($this->business->gst_invoice == 1):?>
			<?php if (count($tax_format) > 0 && !empty($tax_format)):
				foreach($tax_format as $key=> $taxlable){
				?>  
				  <p style="font-size: 13px; font-family: georgia; color: black; margin: 0"><?php echo $taxlable; ?>: <?php echo html_escape($biz_vat_code[$key]) ?></p>
			<?php 
				}
				endif ?>
            <?php endif ?>
			
            <span style="font-size: 13px; font-family: georgia; color: black; margin: 0"><?php echo $business_address ?></span>
            <p style="font-size: 12px; font-family: georgia; color: black; margin: 0"><?php echo html_escape($country) ?></p>
            <br>
            <?php if (!empty($biz_number)): ?>
            <p style="font-size: 12px; font-family: georgia; color: black; margin: 0"><?php echo trans('contact').' '.trans('no') ?>: <?php echo html_escape($biz_number) ?></p>
            <?php endif ?>
            
            <?php if (!empty($website_url)): ?>
            <p style="font-size: 12px; font-family: georgia; color: black; margin: 0"><?php echo html_escape($website_url) ?></p>
            <?php endif ?>

        </div>
    </div>
    
    <?php if (isset($page) && $page == 'Bill'): ?>
        <img width="100%" src="<?php echo base_url("assets/admin/Capture2.png") ?>" alt="Logo">
    <?php elseif(isset($page) && $page == 'Invoice'): ?>
        <img width="100%" src="<?php echo base_url("assets/admin/Capture.png") ?>" alt="Logo">
    <?php elseif(isset($page) && $page == 'Delivery'): ?>
        <img width="100%" src="<?php echo base_url("assets/admin/Capture1.png") ?>" alt="Logo">
    <?php elseif(isset($page) && $page == 'Estimate'): ?>
        <img width="100%" src="<?php echo base_url("assets/admin/Capture3.png") ?>" alt="Logo">
    <?php endif; ?>
    
        
        <!--<div style="float: left;">-->
        <!--    <hr style="width: 10%; border-color: rgb(68, 68, 68); border-width: 2px 0; border-style: solid; padding: 2px 0; flex-grow: 2; margin: 40px 0;">-->
        <!--</div>-->
        
            <!--<div class="flex-parent-center" style="width: 70%; margin: 0 20px;">-->
            <!--    <div style="width: 5%; float: left">-->
            <!--        <svg height="72" viewBox="0 0 28 72" width="28" >-->
            <!--            <g fill="none" stroke="#444444" stroke-width="2">-->
            <!--            <path d="M183 57.038v-42.076c-.33.025-.664.038-1 .038-7.18 0-13-5.82-13-13 0-.336.013-.67.038-1h-154.076c.025.33.038.664.038 1 0 7.18-5.82 13-13 13-.336 0-.67-.013-1-.038v42.076c.33-.025.664-.038 1-.038 7.18 0 13 5.82 13 13 0 .336-.013.67-.038 1h154.076c-.025-.33-.038-.664-.038-1 0-7.18 5.82-13 13-13 .336 0 .67.013 1 .038z"></path><path d="M177 51.503v-31.007c-.33.024-.664.037-1 .037-7.18 0-13-5.626-13-12.567 0-.325.013-.648.038-.967h-142.076c.025.319.038.641.038.967 0 6.94-5.82 12.567-13 12.567-.336 0-.67-.012-1-.037v31.007c.33-.024.664-.037 1-.037 7.18 0 13 5.626 13 12.567 0 .325-.013.648-.038.967h142.076c-.025-.319-.038-.641-.038-.967 0-6.94 5.82-12.567 13-12.567.336 0 .67.012 1 .037z"></path>-->
            <!--        </g></svg>-->
            <!--    </div>    -->
    
            <!--    <div style="width: 25%; float: left;">-->
            <!--        <div style="display: inline;  border-width: 2px 0; border-style: solid; height: 60px; line-height: 64px; position: relative; padding: 0 10px;">-->
            <!--            <h4 style="border-width: 10px; margin: 0;  font-size: 30px;  text-transform: uppercase;"><?php echo html_escape($title) ?></h4>-->
            <!--        </div>-->
            <!--    </div>-->
                
            <!--    <div style="width: 5%; float: left">-->
            <!--        <svg height="72" viewBox="0 0 28 72" width="28">-->
            <!--        <g fill="none" stroke="#444444" stroke-width="2"><path d="M27 57.038v-42.076c-.33.025-.664.038-1 .038-7.18 0-13-5.82-13-13 0-.336.013-.67.038-1h-154.076c.025.33.038.664.038 1 0 7.18-5.82 13-13 13-.336 0-.67-.013-1-.038v42.076c.33-.025.664-.038 1-.038 7.18 0 13 5.82 13 13 0 .336-.013.67-.038 1h154.076c-.025-.33-.038-.664-.038-1 0-7.18 5.82-13 13-13 .336 0 .67.013 1 .038z"></path><path d="M21 51.503v-31.007c-.33.024-.664.037-1 .037-7.18 0-13-5.626-13-12.567 0-.325.013-.648.038-.967h-142.076c.025.319.038.641.038.967 0 6.94-5.82 12.567-13 12.567-.336 0-.67-.012-1-.037v31.007c.33-.024.664-.037 1-.037 7.18 0 13 5.626 13 12.567 0 .325-.013.648-.038.967h142.076c-.025-.319-.038-.641-.038-.967 0-6.94 5.82-12.567 13-12.567.336 0 .67.012 1 .037z"></path></g></svg>-->
            <!--    </div>-->
            <!--</div>-->
            
        <!--<div style="float: right">-->
        <!--    <hr style="width: 10%; border-color: rgb(68, 68, 68); border-width: 2px 0; border-style: solid; padding: 2px 0; flex-grow: 2; margin: 40px 0;">-->
        <!--</div>-->
        <!--<hr class="inv_header_hr_right" style="width: 15%; float: right">-->
    
    
    <div class="flex-parent-between bill_area align-items-center" style="width: 100%">
        <div class="col-6 py-4 pl-30 pr-30" style="width: 25%; float: left;">
            
            <?php if (isset($page) && $page == 'Bill'): ?>
                <h5 style="font-weight: 400; text-transform: uppercase; color: #a7a7a7; font-size: 13px; font-family: georgia; margin: 0"><?php echo trans('purchase-from') ?></h5>
            <?php else: ?>
                <h5 style="font-weight: 400; text-transform: uppercase; color: #a7a7a7; font-size: 13px; font-family: georgia; margin: 0"><?php echo trans('bill-to') ?></h5>
            <?php endif ?>
            
       
            <?php if (empty($customer_id)): ?>
                <p style="margin: 0"><?php echo trans('empty-customer') ?></p>
            <?php else: ?>
            	<?php if (isset($page) && $page == 'Bill'): ?>
    				<?php if (!empty(helper_get_vendor($customer_id))): ?>
        				<p style="font-weight: bold; font-size: 13px; text-transform: uppercase;font-family: georgia; margin: 0"><strong><?php echo helper_get_vendor($customer_id)->name ?></strong></p>
        				<p style="font-size: 13px; text-transform: uppercase;font-family: georgia; margin: 0"><?php echo helper_get_vendor($customer_id)->address ?></p>
        				<p style="font-size: 13px;font-family: georgia; margin: 0"><?php echo html_escape($country) ?></p>
        				<p style="font-size: 13px;font-family: georgia; margin: 0"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_vendor($customer_id)->phone ?></p>
        				<p style="font-size: 13px;font-family: georgia; margin: 0"><?php echo helper_get_vendor($customer_id)->email ?></p>
    				<?php endif ?>
    				<?php else: ?>
    				
    				<?php if (!empty(helper_get_customer($customer_id))): ?>
    				    <p style="font-weight: bold; font-size: 13px; text-transform: uppercase; font-family: georgia; margin: 0"><strong><?php echo helper_get_customer($customer_id)->name ?></strong></p>
        				<?php if (!empty($cus_vat_code)): ?>
        				    <p style="font-size: 12px; font-family: georgia; margin: 0"><span><?php echo $cus_tax_format; ?>:</span> <span style="text-transform: uppercase"><?php echo html_escape($cus_vat_code) ?></span></p>
        				<?php endif ?>
        				<p style="font-size: 13px; text-transform: uppercasefont-family: georgia; margin: 0"><?php echo helper_get_customer($customer_id)->address ?></p>
        				<p style="font-size: 13px; font-family: georgia; margin: 0"><?php echo html_escape($country) ?></p>
        				<p style="font-size: 13px; text-transform: uppercasefont-family: georgia; margin: 0"><?php echo trans('contact').' '.trans('no') ?>: <?php echo helper_get_customer($customer_id)->phone ?></p>
        				<p style="font-size: 13px; font-family: georgia; margin: 0"><?php echo trans('email').' '.trans('id') ?>: <?php echo helper_get_customer($customer_id)->email ?></p>
				    <?php endif ?>
			    <?php endif ?>
            <?php endif ?>
        </div>

        <div class="col-5 py-4 text-right" style="width: 40%; float: right; text-align: right; margin-right: -105px;">
            <table class="tables" style="width: 100%">
                <tr>
                    <td style="text-align: right; font-size: 13px; font-weight: bold; padding-bottom: 5px; font-family: georgia">
                            <strong>
                                <?php 
                                if(isset($page) && $page == 'Invoice'){
                                    echo trans('invoice-number');
                                }
                                else if($page == 'Estimate'){
                                    echo trans('estimate-number');
                                }else if($page == 'Delivery'){
                                    echo 'Challan Number';
                                }else{
                                    echo trans('bill-number');
                                } ?>:
                            </strong>
                    </td>
                    <td colspan="1" style="padding-left: 12px; font-size: 13px; padding-bottom: 5px; font-family: georgia"><?php echo html_escape($number) ?></td>
                </tr>
                <tr>
                    <td style="text-align: right; font-size: 13px; font-weight: bold; padding-bottom: 5px; font-family: georgia"><b class="mr-10"><?php if(isset($page) && $page == 'Invoice'){echo trans('invoice-date');}else if(isset($page) && $page == 'Delivery'){echo 'Delivery Date';}else{echo trans('date');} ?>:</b></td>
                    <td class="text-left" colspan="1" style="padding-left: 12px; font-size: 13px; padding-bottom: 5px; font-family: georgia"><?php echo my_date_show($date) ?></td>
                </tr>
                <?php if (!empty($poso_number)): ?>
                    <tr>
                        <td style="text-align: right; font-size: 13px; font-weight: bold; padding-bottom: 5px; font-family: georgia"><b class="mr-10"><?php echo trans('p.o.s.o.-number') ?>:</b></td>
                        <td class="text-left" colspan="1" style="padding-left: 12px; font-size: 13px; padding-bottom: 5px; font-family: georgia"><?php echo html_escape($poso_number) ?></td>
                    </tr>
                <?php endif ?>
                <?php if(isset($page) && $page == 'Invoice'):?>
                    <tr>
                        <td style="text-align: right; font-size: 13px; font-weight: bold; padding-bottom: 5px; font-family: georgia"><b class="mr-10"><?php echo trans('due-date') ?>:</b></td>
                        <td class="text-left" style="padding-left: 12px; font-size: 13px; padding-bottom: 5px; font-family: georgia">
                            <?php echo my_date_show($payment_due) ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="text-left" style="padding-left: 12px; font-size: 13px; padding-bottom: 5px; font-family: georgia">
                            <?php if ($due_limit == 1): ?>
                                <p style="font-size: 13px;"><?php echo trans('on-receipt') ?></p>
                            <?php else: ?>
                                <p style="font-size: 13px;"><?php echo trans('within') ?> <?php echo html_escape($due_limit) ?> <?php echo trans('days') ?></p>
                            <?php endif ?>
                        </td>
                    </tr>
					<?php 
					else: 
					if(isset($page) && $page != 'Delivery'):
					?>
                    <tr>
                        <td style="text-align: right; font-size: 13px; font-weight: bold; padding-bottom: 5px; font-family: georgia"><b class="mr-10"><?php echo trans('expires-on') ?>:</b></td>
                        <td class="text-left" style="padding-left: 12px; font-size: 13px; padding-bottom: 5px; font-family: georgia">
                        <?php echo my_date_show($invoice->expire_on) ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endif; ?>
                
                <tr>
                    <td class="bg-mlight" style="padding: 2%; font-weight: bold; font-size: 13px; font-family: georgia; text-align: right">
                        <strong><?php echo trans('amount-due') ?>:</strong>
                    </td>
                    <td class="bg-mlight" style="padding: 2%; padding-left: 12px; text-align: left; font-weight: bold; font-size: 13px; font-family: georgia">
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
                        <th style="color: black; font-weight: bold; border-top: 0; font-family: georgia; font-size: 13px; border-bottom: 1px dotted black"><?php echo trans('items') ?></th>
                        <th style="color: black; font-weight: bold; border-top: 0; font-family: georgia; font-size: 13px; border-bottom: 1px dotted black">HSN/SAC Code</th>
                        <th style="color: black; font-weight: bold; text-align: right; border-top: 0; font-family: georgia; font-size: 13px; border-bottom: 1px dotted black"><?php echo trans('price') ?></th>
                        <th style="color: black; font-weight: bold; text-align: center; border-top: 0; font-family: georgia; font-size: 13px; border-bottom: 1px dotted black"><?php echo trans('quantity') ?></th> 
						<th style="color: black; font-weight: bold; text-align: center; border-top: 0; font-family: georgia; font-size: 13px; border-bottom: 1px dotted black">Unit</th>
                        <th style="color: black; font-weight: bold; text-align: center; border-top: 0; font-family: georgia; font-size: 13px; border-bottom: 1px dotted black"><?php echo trans('amount') ?></th>
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
                                <tr>
                                    <td width="25%" style="border-top: 0">
                                    <?php $product_id = $this->session->userdata('item')[$i] ?>
                                    
                                    <?php if (is_numeric($product_id)) {
                                         echo helper_get_product($product_id)->name.'<br> <small>'. nl2br(helper_get_product($product_id)->details).'</small>';
                                    } else {
                                        echo html_escape($product_id);
                                    } ?>
                                    </td>
									<td style="color: black; border-top: 0; font-size: 13px; text-align: center; font-family: georgia" ><?php echo $this->session->userdata('hsn_sac')[$i] ?></td>
                                    <td style="border-top: 0; text-align: center; font-size: 13px; font-family: georgia;"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo $this->session->userdata('price')[$i] ?></td>
                                    <td style="border-top: 0; text-align: center; font-size: 13px; font-family: georgia;"><?php echo $this->session->userdata('quantity')[$i] ?></td>
									<td style="border-top: 0; text-align: center; font-size: 13px; font-family: georgia;"><?php if(!empty($this->session->userdata('unit')[$i])){ echo $this->session->userdata('unit')[$i];}else{echo "-";} ?></td>
                                    <td class="text-right" style="border-top: 0; font-size: 13px; font-family: georgia; text-align: right"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($this->session->userdata('total_price')[$i], 2) ?></td>
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
                            <?php 
                            
                            $length = count($items)-1;
   
                            foreach ($items as $key => $item):
                                $brobot = "";
                                if($key == $length)
                                {
                                     $brobot="border-bottom: 1px dotted black";
                                }
                            ?>
                                <tr class="inv-pl30" style="">
                                    <td width="25%" style="border-top: 0; color: black; font-family: georgia; <?php echo $brobot;?>">
                                        <p style="color: black; font-weight: bold; font-size: 13px; margin: 0">
                                            <?php echo html_escape($item->item_name) ?>
                                        </p> 
                                        <p style="font-size: 13px; margin: 0; color: #868e96;"><?php echo html_escape($item->serial_no) ?></p>
                                        <p style="font-size: 13px; color: #868e96; margin: 0"><?php echo nl2br($item->details) ?></p>
                                    </td>
									<td style="color: black; border-top: 0; font-size: 13px; text-align: center; font-family: georgia; <?php echo $brobot;?>"><p><?php echo $item->hsn_sac ?></p></td>
                                    <td style="color: black; border-top: 0; font-size: 13px; text-align: center; font-family: georgia; <?php echo $brobot;?>"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->price, 2) ?></td>
                                    <td style="color: black; border-top: 0; font-size: 13px; text-align: center; font-family: georgia; <?php echo $brobot;?>"><?php echo html_escape($item->qty) ?></td>
									<td style="color: black; border-top: 0; font-size: 13px; text-align: center; font-family: georgia; <?php echo $brobot;?>"><?php if(!empty($item->unit)){echo html_escape($item->unit);}else{echo "-";} ?></td>
                                    <td class="text-right" style="color: black; border-top: 0; font-size: 13px; text-align: right; padding: 5px 20px; font-family: georgia; <?php echo $brobot;?>"><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($item->total, 2) ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    <?php endif ?>
                    
					 <?php if (!empty($discount)): ?>
					    <tr class="inv-pl30">
                            <td style="border-top: 0;"></td>
                            <td style="border-top: 0;"></td>
                            <td style="border-top: 0;"></td>
                            <td style="border-top: 0;"></td>
                            <td class="text-right" style="color: black; font-weight: bold; font-size: 12px; padding: 5px 10px; font-family: georgia; border-top: 0;"><strong>Total :</strong></td>
                            <td class="text-right" style="color: black; font-size: 12px; padding: 5px 20px; text-align: right; font-family: georgia; border-top: 0;"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total, 2) ?></span></td>
                        </tr>
                        <tr class="inv-pl30">
                            <td style="border-top: 0; padding: 5px 10px"></td>
                            <td style="border-top: 0; padding: 5px 10px"></td>
                            <td style="border-top: 0; padding: 5px 10px"></td>
                            <td style="border-top: 0; padding: 5px 10px"></td>
                            <td class="text-right" style="color: black; border-top: 0; font-weight: bold; font-size: 12px; padding: 5px 10px; font-family: georgia"><strong><?php echo trans('discount') ?> <?php echo html_escape($discount) ?>% :</strong></td>
                            <td class="text-right" style="color: black; border-top: 0; font-size: 12px; padding: 5px 20px; text-align: right; font-family: georgia"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total * ($discount / 100), 2) ?></span></td>
                        </tr>
						<tr class="inv-pl30" style="">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right" style="color: black; font-weight: bold; font-size: 12px; padding: 5px 10px; font-family: georgia"><strong><?php echo trans('sub-total') ?>:</strong></td>
                            <td class="text-right" style="color: black; font-size: 12px; padding: 5px 20px; text-align: right; font-family: georgia"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total-$sub_total * ($discount / 100), 2) ?></span></td>
                        </tr>
                    <?php else: ?>
                    <tr class="inv-pl30">
                        <td style="border-top: 0"></td>
                        <td style="border-top: 0"></td>
                        <td style="border-top: 0"></td>
                        <td style="border-top: 0"></td>
                        <td class="text-right" style="color: black; font-weight: bold; font-size: 12px; padding: 5px 10px; font-family: georgia; border-top: 0"><strong><?php echo trans('sub-total') ?>:</strong></td>
                        <td class="text-right" style="color: black; font-size: 12px; padding: 5px 20px; text-align: right; font-family: georgia; border-top: 0"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($sub_total, 2) ?></span></td>
                    </tr>
					<?php endif ?>
					
                    <?php if($this->business->invoice_tax == 1): ?>
					 <?php if (isset($page_title) && $page_title != 'Invoice Preview'): ?>

                        <?php if (!empty($taxes)): ?>
        					<?php foreach ($taxes as $tax): ?>
            					<?php if ($tax != 0): ?>
                               <tr class="inv-pl30">
                                    <td style="color: black; border-top: 0; font-size: 12px; padding: 5px 10px; font-family: georgia"></td>
                                    <td style="color: black; border-top: 0; font-size: 12px; padding: 5px 10px; font-family: georgia"></td>
                                    <td style="color: black; border-top: 0; font-size: 12px; padding: 5px 10px; font-family: georgia"></td>
                                    <td style="color: black; border-top: 0; font-size: 12px; padding: 5px 10px; font-family: georgia"></td>
                                    <td class="text-right" style="color: black; border-top: 0; font-weight: bold; font-size: 12px; padding: 5px 10px; font-family: georgia">
                                        <strong>
                                        <?php echo $tax->tax_type; ?> :
                                    </strong></td>
                                    
                                    <td class="text-right" style="color: black; border-top: 0; font-size: 12px; padding: 5px 20px; text-align: right; font-family: georgia">
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
                            <td style="color: black; border-top: 0; font-size: 12px; padding: 5px 10px; font-family: georgia"></td>
                            <td style="color: black; border-top: 0; font-size: 12px; padding: 5px 10px; font-family: georgia"></td>
                            <td style="color: black; border-top: 0; font-size: 12px; padding: 5px 10px; font-family: georgia"></td>
                            <td style="color: black; border-top: 0; font-size: 12px; padding: 5px 10px; font-family: georgia"></td>
                            <td class="text-right" style="color: black; border-top: 0; font-weight: bold; font-size: 12px; padding: 5px 10px; font-family: georgia">
                                <strong><?php echo $key; ?> :</strong>
                            </td>
                            <td class="text-right" style="color: black; border-top: 0; font-size: 12px; padding: 5px 20px; text-align: right; font-family: georgia">
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
                        <td class="text-right" style="color: black; font-weight: bold; font-size: 12px; padding: 5px 10px; font-family: georgia"><strong><?php echo trans('grand-total') ?>:</strong></td>
                        <td style="color: black; font-size: 12px; text-align: right; padding: 5px 20px; font-family: georgia"><span><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($grand_total, 2) ?></span></td>
                    </tr>


                    <?php foreach (get_invoice_payments($invoice->id) as $payment): ?>
                        <tr class="inv-pl30 text-dark">
                            <td style="border-top: 0; padding: 5px 10px"></td>
                            <td style="border-top: 0; padding: 5px 10px"></td>
                            <td style="border-top: 0; padding: 5px 10px"></td>
                            <td style="border-top: 0; padding: 5px 10px"></td>
                            <td class="text-right" style="color: black; font-size: 12px; padding: 5px 10px; font-family: georgia; text-align: right">
                                <span style=" text-align: right"><strong><?php echo trans('payment-on') ?> <?php echo my_date_show($payment->payment_date) ?> <?php echo trans('using') ?> <?php echo get_using_methods($payment->payment_method) ?>:</strong></span>
                            </td>
                            <td>
                                <span class="fs-13" style="color: black; font-size: 12px; padding: 5px 20px; text-align: right; font-family: georgia">
                                    <strong><?php if(!empty($currency_symbol)){echo html_escape($currency_symbol);} ?><?php echo number_format($payment->amount,2) ?></strong>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach ?>

                    <tr class="inv-pl30">
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td style="border-top: 0; padding: 5px 10px"></td>
                        <td class="text-right" style="color: black; border-top: 3px solid #dfdede; font-weight: bold; font-size: 12px; padding: 5px 10px; font-family: georgia"><strong><?php echo trans('amount-due') ?>:</strong></td>
                        <td style="color: black; border-top: 3px solid #dfdede; font-size: 12px; padding: 5px 20px; text-align: right; font-family: georgia">
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
            
            <table class="p-30 table m-0">
			    <tbody>
			        <tr>
					    <td style="border-right: 1px solid #bdc1c7; border-collapse: collapse; vertical-align: top !important; border-left: 0" width="80%">
							<?php 
								$activeBanking = $this->db->where('user_id',$this->session->userdata('id'))->where('bank_print_invoice',1)->get('banking')->row();
								if (!empty($activeBanking)): 
							?>
					        <p style="font-family: georgia; text-decoration: underline; font-weight: bold">Bank Details : </p>
							
							<p style="font-family: georgia; text-transform:capitalize"><span style="font-weight: bold;">Account Holder Name :</span> <?php echo html_escape($activeBanking->account_name); ?> </p>
							<p style="font-family: georgia"><span style="font-weight: bold">Bank name : </span><?php echo html_escape($activeBanking->bank_name); ?></p>
							<p style="font-family: georgia"><span style="font-weight: bold">Account Number : </span><?php echo html_escape($activeBanking->account_number); ?></p>
							<p style="font-family: georgia"><span style="font-weight: bold">IFSC : </span><?php echo html_escape($activeBanking->ifsc); ?></p>  
					        <?php endif; ?>
						</td>
    					<td style="border-collapse: collapse; border-right: 0; " width="20%">
							<?php if (isset($page_title) && $page_title == 'Invoice Preview'): 
								$pay_qrcode = $this->session->userdata('qr_code');		
								?>
							 <?php if (!empty($pay_qrcode)): ?>
							<p style="font-family: georgia; font-weight: bold">Scan QR-code to Pay</p>			
							<p style="font-family: georgia; text-align: center"><img src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code" style="width: 75%; max-width: 150px"></p>
                            <?php endif; ?>
							<?php else: ?>
    					    <?php if (!empty($pay_qrcode)): ?>
							<p style="font-family: georgia; font-weight: bold">Scan QR-code to Pay</p>			
							<p style="font-family: georgia; text-align: center"><img src="<?php echo base_url($pay_qrcode) ?>" alt="QR Code" style="width: 75%; max-width: 150px"></p>
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
                        <td width="50%" style='border-right: 1px solid #bdc1c7;vertical-align: top !important'>
                            <p style="font-family: georgia; font-weight: bold; text-decoration: underline">Notes / Terms : </p>
                            <?php if (!empty($footer_note)): ?>
							<p style="font-family: georgia"><?php echo $footer_note ?></p>
                            <?php endif; ?>
						</td>
                        <td width="50%" style="vertical-align: top !important">
							<p style="font-family: georgia; font-weight: bold; font-size: 12px">Receiver's Signature:  _________________________</p>
							<hr>
							<p style="font-family: georgia; text-align: right; font-weight: bold; font-size: 14px">For <?php echo $business_name ?></p>
							<br>
							<br>
							<p style="font-family: georgia; text-align: right; font-weight: bold; font-size: 14px">Authorised Signatory</p>
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