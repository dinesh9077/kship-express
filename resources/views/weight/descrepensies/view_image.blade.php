@if(!empty($weight_descrepencies))
<div class="row">
	<div class="col-md-12">
		<a href="{{url('storage/weight_descrepency/'.$weight_descrepencies->order_id,$weight_descrepencies->length_image)}}" target="_blank"><img src="{{url('storage/weight_descrepency/'.$weight_descrepencies->order_id,$weight_descrepencies->length_image)}}" style="height:80px"></a>
	</div>
	
	<div class="col-md-12">
		<a href="{{url('storage/weight_descrepency/'.$weight_descrepencies->order_id,$weight_descrepencies->width_image)}}" target="_blank"><img src="{{url('storage/weight_descrepency/'.$weight_descrepencies->order_id,$weight_descrepencies->width_image)}}" style="height:80px"></a>
	</div>
	
	<div class="col-md-12">
		<a href="{{url('storage/weight_descrepency/'.$weight_descrepencies->order_id,$weight_descrepencies->height_image)}}" target="_blank"><img src="{{url('storage/weight_descrepency/'.$weight_descrepencies->order_id,$weight_descrepencies->height_image)}}" style="height:80px"></a>
	</div>
	<div class="col-md-12">
		<a href="{{url('storage/weight_descrepency/'.$weight_descrepencies->order_id,$weight_descrepencies->weight_image)}}" target="_blank"><img src="{{url('storage/weight_descrepency/'.$weight_descrepencies->order_id,$weight_descrepencies->weight_image)}}" style="height:80px"></a>
	</div>
	
	<div class="col-md-12">
		<a href="{{url('storage/weight_descrepency/'.$weight_descrepencies->order_id,$weight_descrepencies->label_image)}}" target="_blank"><img src="{{url('storage/weight_descrepency/'.$weight_descrepencies->order_id,$weight_descrepencies->label_image)}}" style="height:80px"></a>
	</div> 
</div>

@endif