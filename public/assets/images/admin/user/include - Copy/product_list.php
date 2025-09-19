<?php $amount_decimal = $this->business->amount_decimal; ?>
<tr class="row_id_<?php echo $i; ?>" >
	<td colspan="3" style="width: 20%;">  
		<input type="text" class="form-control item" placeholder="Item" name="items[]" value="<?php echo html_escape($product->name) ?>" id="item_<?php echo $i; ?>">
	</td> 
	<input type="hidden" class="form-control ws-180" name="serial_no[]" value="" id="serial_no_<?php echo $i; ?>" >
	<td style="width: 30%;vertical-align: top !important;" rowspan="3">
		<?php if($this->business->enable_serial_no == 1):?>
			<i class="fa fa-list form-control item ws-180" style="cursor:pointer;" aria-hidden="true" onclick="openSerialModal(<?php echo ($product->id)?$product->id:0 ?>,<?php echo $i; ?>)"></i> 
		<?php endif;?>
		<?php if($this->business->hsn_sac == 1):?>
			<input class="form-control" placeholder="HSN / SAC Code" type="text" name="hsn_sac[]" value="<?php echo html_escape($product->hsn_code) ?>" id="hsn_sac_<?php echo $i; ?>"> 
		<?php endif;?>
		<textarea name="details[]" class="form-control ac-textarea" rows="1" placeholder="Enter item description"><?php echo html_escape($product->details) ?></textarea> 
	</td>
	<td style="width: 20%;">
		<input class="form-control invo_price text-right" placeholder="Price" type="text" name="price[]" value="<?php echo (isset($product->price))?sprintf("%01.".$amount_decimal."f",$product->price):0; ?>" id="price_<?php echo $i; ?>"> 
	</td>
	<td>
		<input class="form-control invo_qty" placeholder="Qnty." type="text" name="quantity[]" value="1" id="qty_<?php echo $i; ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" max='<?php echo $this->business->quantity_limit; ?>'>
	</td>
	<td width="10%"> 
		<select class="form-control single_select" name="unit[]" id="unit_<?php echo $i; ?>">
			<?php foreach($units as $unit) { ?>
				<option value="<?php echo $unit->unit; ?>"
				<?php echo (!empty($product->unit) && $unit->unit == $product->unit)?'selected':""; ?>
				><?php echo $unit->unit; ?></option>
			<?php } ?>
		</select>
	</td>
	<td class="text-right" width="15%">
		<span class="currency_wrapper"></span>
		<span class="total" id="price_text_<?php echo $i; ?>"><?php echo html_escape(sprintf("%01.".$amount_decimal."f",$product->price)) ?></span>
	</td>
	<td class="text-right">
		<div class="delete-btn">
			<button type="button" class="delete remove_row" href="javascript:;"  id="<?php echo $i;?>" style="cursor:pointer" title="Remove row"><i class="fa fa-trash" aria-hidden="true" ></i></button>  
			<input class="total" type="hidden" name="total_price[]" value="<?php echo html_escape(sprintf("%01.".$amount_decimal."f",$product->price)) ?>" id="total_price_<?php echo $i; ?>" >
			<input type="hidden" name="product_ids[]" value="<?php echo ($product->id)?$product->id:0 ?>">
			<input type="hidden" name="tax_key[]" value="<?php echo html_escape($i) ?>">
		</div>
	</td>
</tr>

<tr class="row_id_<?php echo $i; ?>" >
	<td colspan="2"></td>
	<td></td>
	<td class="text-right" ><strong style="display:<?php echo ($this->business->invoice_discount == 1)?'block':'none';?>">Discount</strong></td>
	<td colspan="2">
		<input type="text" id="discount_<?php echo $i; ?>" name="discount[]" value="0" class="form-control invo_discount" aria-describedby="basic-addon2" style="display:<?php echo ($this->business->invoice_discount == 1)?'block':'none';?>">
	</td>
	<td class="text-right" ><span class="currency_wrapper"></span><span id="discount_text_<?php echo $i; ?>" style="display:<?php echo ($this->business->invoice_discount == 1)?'block':'none';?>">000.00</span></td>
	<td class="text-right"> 
	</td>
</tr>  
<tr class="row_id_<?php echo $i; ?>" id="gst_row_1" style="border-bottom: 1px solid #b2c2cd8c;"> 
	<td ></td>
	<td></td>
	<td class="text-right" colspan="3" ><strong style="display:<?php echo ($this->business->invoice_tax == 1)?'block':'none';?>">Tax</strong></td>
	<td colspan="2">
		<?php if($this->business->invoice_tax == 1): ?>
		<select class="form-control single_select selectgst"  id="tax_<?php echo $i; ?>_1" data-id="<?php echo $i; ?>" style="text-transform: capitalize">
			<option value="" selected>Select Tax</option> 
			<?php foreach($gsts as $key => $gst){ ?>
				<option value="<?php echo $gst->name; ?> <?php echo $gst->rate;?>%" data-id="<?php echo $gst->name; ?> <?php echo $gst->rate;?>%" data-rate="<?php echo $gst->rate;?>" data-name="<?php echo $gst->name; ?>">
					<?php echo $gst->name; ?> <?php echo $gst->rate;?>%
				</option> 
			<?php } ?>
		</select> 
		<?php endif; ?>
	</td>
	<td class="text-right" style="display:<?php echo ($this->business->invoice_tax == 1)?'block':'none';?>">
		—
	</td> 
</tr> 
 
<script>
	$('.single_select').select2(); 
</script>
<?php if($i == 2){ ?>
	<input type="hidden" value="1" id="total_gst_row"> 
	<script>
		$(document).ready(function() 
		{ 
			var j = 1;   
			$(document).on('change', ".selectgst", function(event) 
			{   
				var id = $(this).attr('data-id');
				j++; 
				$('#total_gst_row').val(j); 
				var value = $(this).val();
				var name = $(this).find('option:selected').attr('data-id');    
				$(this).val('');
				var html = '';
				html +='<tr class="row_id_'+id+'" id="gst_row_'+j+'"><td ></td><td></td><td class="text-right" colspan="3"><strong>Tax</strong></td><td colspan="2"><select class="form-control single_select changesel"  name="tax['+id+'][]" id="tax_'+id+'_'+j+'" style="text-transform: capitalize"><option value="" selected>Select Tax</option><?php foreach($gsts as $key => $gst){ ?><option value="<?php echo $gst->name; ?> <?php echo $gst->rate;?>%" data-rate="<?php echo $gst->rate;?>"  data-name="<?php echo $gst->name; ?>"><?php echo $gst->name; ?> <?php echo $gst->rate;?>%</option><?php } ?></select></td><td class="text-right" id="tax_text_'+id+'_'+j+'">0.00</td><td class="text-right"><div class="delete-btn"><input type="hidden"  name="taxval['+id+'][]" id="tax_val_'+id+'_'+j+'" value="0.00"><a class="delete remove_gst_row" id="'+j+'" href="javascript:;" title="Remove row"><i class="fa fa-trash" aria-hidden="true"></i></a></div></td></tr>'; 
				$(this).closest("tr").before(html); 
				$('#tax_'+id+'_'+j+'').val(value);
				$('#select2-tax_'+id+'_'+j+'-container').text(name);
				$('.single_select').select2();
				inv_cal_final_total();
				currency_fromat()
			});
			
			$(document).on('click', '.remove_gst_row', function () { 
				var row_id = $(this).attr("id"); 
				$('#gst_row_' + row_id).remove();  
				inv_cal_final_total();
				currency_fromat()
			}); 
			
			$(document).on('change', ".changesel", function(event) 
			{  
				inv_cal_final_total();
				currency_fromat()
			}); 
		}); 
	</script>
<?php } ?>