
<?php 
	
	$serial_no = explode(',',$product->serial_no); 
	$array = array_unique(array_merge($serial_no, $serial_nodd));
	if(count($array) > 0)
	{ 
		foreach($array as $key => $row)
		{
		?>
			<tr id="close_<?php echo $key+1;?>"> 
				<th width="100%" style="text-align:left"><input type="checkbox" class="serial_check" name="serial_check[]" value="<?php echo $row;?>" id="serial_<?php echo $key;?>" <?php echo (in_array($row,$serial_nodd))?'checked':'';?>/><label for="serial_<?php echo $key;?>"> <?php echo $row;?></label>
				</th>
			</tr>
		<?php 
		}
	}
	 
?>