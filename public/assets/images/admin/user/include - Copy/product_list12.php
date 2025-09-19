
<tr class="item-row">
	<td width="10%">
		<input type="text" class="form-control item ws-180" placeholder="Item" type="text" name="items[]" value="<?php echo html_escape($product->name) ?>">
	</td>
	<td width="10%">
		<i class="fa fa-list form-control item ws-180" style="cursor:pointer;" aria-hidden="true" onclick="openSerialModal(<?php echo $product->id ?>,0)"></i> 
	</td>
	<input type="hidden" class="form-control ws-180" name="serial_no[]" value="" id="serial_no_<?php echo html_escape($product->id) ?>" >
	<td width="10%">
		<input type="text" class="form-control ws-180" placeholder="HSN / SAC Code" type="text" name="hsn_sac[]" value="<?php echo html_escape($product->hsn_code); ?>" onkeyup="return this.value = this.value.replace(/[^0-9\.]/g,'');">
	</td>
	<td width="15%">
		<textarea name="details[]" class="form-control ac-textarea ws-180" rows="1" placeholder="<?php echo trans('item-description') ?>"><?php echo html_escape($product->details) ?></textarea>
	</td>
	<td width="10%">
		<input class="form-control price invo ws-100" placeholder="Price" type="text" name="price[]" value="<?php echo html_escape($product->price) ?>" onkeyup="return this.value = this.value.replace(/[^0-9\.]/g,'');"> 
	</td>
	<td width="5%">
		<input class="form-control qty ws-100" placeholder="Qty" type="text" name="quantity[]" value="1" onkeyup="return this.value = this.value.replace(/[^0-9\.]/g,'');" id="qty_<?php echo html_escape($product->id) ?>" >
	</td>
	<td width="5%">
		<input class="form-control ws-100" placeholder="Unit" type="text" name="unit[]" value="<?php echo (!empty($product->unit))?html_escape($product->unit):""; ?>">
	</td> 
	<td >
		<span class="currency_wrapper"></span>
		<span class="total"><?php echo html_escape($product->price) ?></span>
		<div class="delete-btn">
			
			<a class="delete" href="javascript:;" title="Remove row"><i class="fa fa-trash-o"></i></a>
			<input type="hidden" class="total" name="total_price[]" value="<?php echo html_escape($product->price) ?>">
			<input type="hidden" name="product_ids[]" value="<?php echo html_escape($product->id) ?>">
		</div>
	</td>
</tr>
