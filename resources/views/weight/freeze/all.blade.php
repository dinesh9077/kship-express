@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Weight Freeze')
@section('header_title','Weight Freeze')
@section('content')
<style>
	.pagination {
		display: -webkit-box;
		display: -ms-flexbox;
		/* display: flex; */
		padding-left: 0;
		list-style: none;
		border-radius: .25rem;
		margin-top: 20px;
		float: right;
	}

	th,
	td {
		padding: 10px 15px;
		text-align: inherit;
	}
</style>
<div class="content-page">
	<div class="content">
		<!-- Start Content-->
		<div class="container-fluid">
			<div class="main-find-weght mt-3">
				<div class="main-order-page-1">
				<div class="main-order-001">
				<div class="main-filter-weight ">
					<form method="get" action="">
						<div class="row row-re">
							<div class="col-lg-2 col-sm-6">
								<div class="main-selet-11">
									<input type="text" class="form-control" value="<?php echo (isset($_GET['search'])) ? $_GET['search'] : ''; ?>" name="search" id="search" placeholder="Search with Order Id">
								</div>
							</div>
							<div class="col-lg-2 col-sm-6">
								<div class="main-selet-11">
									<select name="user_id">
										<option value=""> All Users </option>
										@foreach($users as $user)
										<option value="{{$user->id}}"> {{$user->name}} </option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-lg-2 col-sm-6">
								<div class="main-selet-11">
									<button type="submit" class="btn-main-1">Search</button>
								</div>
							</div>
						</div>
					</form>

				</div>

				<div class="amin-slip-1">
					<div class="main-tab-11">
						<div class="main-data-teble-1 table-responsive">
							<table class="dataTable no-footer" style="width:100%">
								<thead>
									<tr>
										<th> ID </th>
										<th> Order ID</th>
										<th> User Name</th>
										<th> Product Name</th>
										<th> Package Details </th>
										<th> Images </th>
										<th> Weight Freeze Status </th>
										<th> Date </th>
									</tr>
								</thead>
								<tbody>
									@foreach($weightfreezes as $keys => $weightfreeze)
									<?php
									$order = App\Models\Order::whereId($weightfreeze->order_id)->first();
									$orderItems = App\Models\OrderItem::whereOrder_id($weightfreeze->order_id)->get();
									$product_details = "";
									foreach ($orderItems as $key => $orderitem) {
										$product_details .= '<p>' . $orderitem->product_name . '<br>  Amount :' . $orderitem->amount . '  QTY : ' . $orderitem->quantity . '</p>';
										if (count($orderItems) - 1 != $key) {
											$product_details .= '<hr>';
										}
									}

									?>
									<tr>
										<td> {{( $keys + 1 )}} </td>
										<td> #{{$order->id}} </td>
										<td> {{$weightfreeze->name}} </td>
										<td>
											<div class="tooltip">View Product<span class="tooltiptext">{!!$product_details!!}</span></div>
											</th>
										<td>
											<b> Dimension </b><br>
											{{$order->length}} x {{$order->width}} x {{$order->height}} CM <br>
											<b> Dead Weight </b> <br>
											{{$order->weight}} Kg
										</td>
										<td>
											@if(!empty($weightfreeze->packaging_id))
											<?php $package = App\Models\Packaging::whereId($weightfreeze->packaging_id)->first(); ?>
											@php($images = explode(',',$package->images))
											@if(count($images) > 0)
											@foreach($images as $image)
											<a target="_blank" href="{{url('storage/packaging',$image)}}"><img src="{{url('storage/packaging',$image)}}" style="height:50px;width: 50px;object-fit: cover;"></a>
											@endforeach
											@endif

											@else
											@if($weightfreeze->freeze_status == "Freezed")
											<!--<div class="row">-->
											<!--<div class="col-md-4">-->
											<a href="{{url('storage/weight_freeze/'.$weightfreeze->order_id,$weightfreeze->length_image)}}" target="_blank"><img src="{{url('storage/weight_freeze/'.$weightfreeze->order_id,$weightfreeze->length_image)}}" style="height:50px;width:50px;object-fit: cover;"></a>
											<!--</div>-->

											<!--<div class="col-md-4">-->
											<a href="{{url('storage/weight_freeze/'.$weightfreeze->order_id,$weightfreeze->width_image)}}" target="_blank"><img src="{{url('storage/weight_freeze/'.$weightfreeze->order_id,$weightfreeze->width_image)}}" style="height:50px;width:50px;;width:50px;object-fit: cover;"></a>
											<!--</div>-->

											<!--<div class="col-md-4">-->
											<a href="{{url('storage/weight_freeze/'.$weightfreeze->order_id,$weightfreeze->height_image)}}" target="_blank"><img src="{{url('storage/weight_freeze/'.$weightfreeze->order_id,$weightfreeze->height_image)}}" style="height:50px;width:50px;object-fit: cover;"></a>
											<!--</div>-->
											<!--<div class="col-md-4">-->
											<a href="{{url('storage/weight_freeze/'.$weightfreeze->order_id,$weightfreeze->weight_image)}}" target="_blank"><img src="{{url('storage/weight_freeze/'.$weightfreeze->order_id,$weightfreeze->weight_image)}}" style="height:50px;width:50px;object-fit: cover;"></a>
											<!--</div>-->

											<!--<div class="col-md-4">-->
											<a href="{{url('storage/weight_freeze/'.$weightfreeze->order_id,$weightfreeze->label_image)}}" target="_blank"><img src="{{url('storage/weight_freeze/'.$weightfreeze->order_id,$weightfreeze->label_image)}}" style="height:50px;width:50px;object-fit: cover;"></a>
											<!--</div> -->
											<!--</div> -->
											@endif
											@endif
										</td>
										<td>
											<p class="{{($weightfreeze->freeze_status == 'Freezed')?'prepaid':'cod'}}"> {{$weightfreeze->freeze_status}} </p>
										</td>
										<td> {{date('Y-m-d h:i a',strtotime($weightfreeze->created_at))}} </td>


									</tr>
									@endforeach
								</tbody>
							</table>

						</div>
					</div>
				</div>
				</div>
				</div>
				{!! $weightfreezes->links() !!}
			</div>
		</div>
	</div>

</div>

@endsection
@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
	let tabSwitchers = document.querySelectorAll("[target-wrapper]");
	tabSwitchers.forEach((item) => {
		item.addEventListener("click", (e) => {
			let currentWrapperId = item.getAttribute("target-wrapper");
			let currentWrapperTargetId = item.getAttribute("target-tab");

			let currentWrapper = document.querySelector(`#${currentWrapperId}`);
			let currentWrappersTarget = document.querySelector(
				`#${currentWrapperTargetId}`
			);

			let allCurrentTabItem = document.querySelectorAll(
				`[target-wrapper='${currentWrapperId}']`
			);
			let allCurrentWrappersTarget = document.querySelectorAll(
				`#${currentWrapperId} .tab-content`
			);

			if (currentWrappersTarget) {
				if (!currentWrappersTarget.classList.contains("active")) {
					allCurrentWrappersTarget.forEach((tabItem) => {
						tabItem.classList.remove("active");
					});
					allCurrentTabItem.forEach((item) => {
						item.classList.remove("active");
					});
					item.classList.add("active");
					currentWrappersTarget.classList.add("active");
				}
			}
		});
	});
</script>
@endpush