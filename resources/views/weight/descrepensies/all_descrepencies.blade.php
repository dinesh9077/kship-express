@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Weight Descrepencies')
@section('header_title','Weight Descrepencies')
@section('content')

<style>
	.modal-body h5 {
	width: 100%;
	}
	
	.model-width-1 {
	width: 50% !important;
	max-width: 100%;
	}
	
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
						<div class="main-filter-weight">
							<form method="get" action="">
								<div class="row row-re">
									<!-- Date Filter -->
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<input type="text" class="form-control datepicker" name="date" id="date" 
												   value="{{ request('date') }}" placeholder="Date">
										</div>
									</div>

									<!-- Search Filter -->
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<input type="text" class="form-control" name="search" id="search"
												   value="{{ request('search') }}" 
												   placeholder="Search with AWB Number Or Order Id">
										</div>
									</div>
 
									<!-- User Filter -->
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<select name="user_id" class="form-control">
												<option value="">All Users</option>
												@foreach($users as $user)
													<option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
														{{ $user->name }}
													</option>
												@endforeach
											</select>
										</div>
									</div>

									<!-- Submit Button -->
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
												<th> Status Updated On </th>
												<th> Seller Details </th>
												<th> Product Details </th>
												<th> Shipping Details </th>
												<th> Applied Weight </th>
												<th> Charged Weight </th>
												<th> Excess Weight & Charges </th> 
												<th> Status </th>
												<th> Actions </th>
											</tr>
										</thead>
										<tbody>
											@foreach($orders as $order)
												@php
													$update_date = $order->weight_update_date ?? $order->created_at;
													
													$excess_weight = $order->excessWeight ?? null;
													 
													$weight_descrepencies = $order->weightDescrepency ?? null;

													$productDetails = $order->orderItems->map(fn($item) => 
														"<p>{$item->product_description} Amount: {$item->amount} No Of Box: {$item->quantity}</p>"
													)->implode(' | ');
												@endphp

												<tr>
													<!-- Date Column -->
													<td>{{ date('d M Y h:i:s', strtotime($update_date)) }}</td>

													<!-- User Column -->
													<td>
														<b>{{ optional($order->user)->name ?? 'N/A' }}</b><br>
														{{ optional($order->user)->mobile ?? 'N/A' }}
													</td>

													<!-- Product Details Tooltip -->
													<td>
														<a href="javascript:;">
															<div class="tooltip" data-toggle="tooltip" data-placement="top" title="{{ strip_tags($productDetails) }}">
																View Products
															</div>
														</a>
													</td>

													<!-- Courier & AWB Details -->
													<td>
														<b>AWB:</b> <a href="javascript:;">{{ $order->awb_number }}</a><br>
														@if($order->lr_no)
															<b>LR No.:</b> <a href="javascript:;">{{ $order->lr_no }}</a><br>
														@endif
														{{ $order->courier_name }}
													</td>

													<!-- Package Details -->
													<td>{!! \App\Http\Controllers\ReportController::orderPackageDetailHtml($order)!!}</td>

													<!-- Chargeable Weight -->
													<td>
														@if($excess_weight)
															<b>{{ $excess_weight->chargeable_weight }} Kg</b>
															<p>Chargeable Weight: {{ $excess_weight->chargeable_weight }} Kg</p>
														@endif
													</td>

													<!-- Excess Weight Details -->
													<td>
														@if($excess_weight)
															<b>Excess Weight: <span style="color: red;">{{ $excess_weight->excess_weight }} Kg</span></b>
															<br>
															<b>Excess Charges: <span style="color: red;">{{ $excess_weight->excess_charge }}</span></b> 
														@endif
													</td>

													<!-- Product Images (Weight Discrepancies) 
													<td>
														@if($weight_descrepencies && !empty($weight_descrepencies->product_image))
															<div class="row">
																@foreach(explode(',', $weight_descrepencies->product_image) as $image)
																	<div class="col-md-4">
																		<a href="{{ url('storage/weight_descrepency/' . $order->id . '/' . $image) }}" target="_blank">
																			<img src="{{ url('storage/weight_descrepency/' . $order->id . '/' . $image) }}" style="height:50px">
																		</a>
																	</div>
																@endforeach
															</div>
														@endif
													</td>-->

													<!-- Weight Status -->
													<td>
														<button class="main-gray-b">{{ $order->weight_status ?? 'New' }}</button> 
													</td>

													<!-- Actions -->
													<td>
														@if(!empty($order->weight_status))
															{{--@if($order->weight_status == "Rejected" && config('permission.weight_descripencies.add'))
																<select name="action" class="form-control" onchange="takeAction(this, {{ $order->id }})">
																	<option value="">Take Action</option>
																	<option value="Dispute Accepted by Courier">Dispute Accepted by Courier</option>
																	<option value="Dispute Rejected by Courier">Dispute Rejected by Courier</option>
																</select>
															@endif
															<button class="btn-main-1" data-order_id="{{ $order->id }}" onclick="viewHistory(this,event)"> View History </button>--}}
														@else
															@if(config('permission.weight_descripencies.add'))
																<button class="btn-main-1" data-id="{{ $order->id }}" data-user_id="{{ $order->user_id }}" onclick="raiseExcess(this,event)">Raise Weight</button>
															@endif
														@endif
													</td>
												</tr>
											@endforeach
										</tbody>
 
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				{!! $orders->links() !!}
			</div>
		</div>
	</div>
	
</div>

<div class="modal fade excees_weight" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header head-00re pb-0" style="border: none;">
				<h5 class="modal-title" id="exampleModalLabel"> Action Excess Weight </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" action="{{ route('weight.raise.excess-weight') }}" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="row">
						<br>
						<div class="form-group col-md-12">
							<label> Chargeable Weight </label>
							<input type="text" class="form-control" name="chargeable_weight" id="chargeable_weight" placeholder="Chargeable Weight" required>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-12">
							<label> Excess Weight </label>
							<input type="text" class="form-control" name="excess_weight" id="excess_weight" placeholder="Excess Weight" required>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-12">
							<label> Excess Charge </label>
							<input type="text" class="form-control" name="excess_charge" id="excess_charge" placeholder="Excess Charge" required>
						</div>
					</div>
					<input type="hidden" name="order_id" id="order_id">
					<input type="hidden" name="user_id" id="user_id">
				</div>
				<div class="modal-footer" style="justify-content: center;">
					<button type="submit" class="btn btn-primary btn-main-1"> Submit </button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade view_history" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg model-width-1">
		<div class="modal-content">
			<div class="modal-header head-00re pb-0" style="border: none;">
				<h5 class="modal-title" id="header_msg"> </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="display_view">
				
			</div>
		</div>
	</div>
</div>
<div class="modal fade view_image" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header head-00re pb-0" style="border: none;">
				<h5 class="modal-title" id="header_msg_image"> </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="display_view_image">
				
			</div>
		</div>
	</div>
</div>
@endsection
@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
	function viewHistory(obj, event) {
		event.preventDefault();
		var order_id = $(obj).attr('data-order_id');
		
		$.get("{{url('weight/descripencies/history')}}/" + order_id, function(res) {
			$('#header_msg').html(res.header_msg);
			$('#display_view').html(res.view);
			$('.view_history').modal('show');
		}, 'Json');
	}
	
	function raiseExcess(obj, event) {
		event.preventDefault();
		var order_id = $(obj).attr('data-id');
		var user_id = $(obj).attr('data-user_id');
		$('#order_id').val(order_id);
		$('#user_id').val(user_id);
		$('.excees_weight').modal('show');
	}
	
	function takeAction(obj, order_id) {
		var acton = $(obj).val();
		
		Swal.fire({
			title: "Are you sure you want to " + acton + "?",
			text: "You won't be able to revert this!",
			type: "warning",
			showCancelButton: !0,
			confirmButtonColor: "#31ce77",
			cancelButtonColor: "#f34943",
			confirmButtonText: "Yes, " + acton + " it!"
			}).then(function(t) {
			if (t.value) {
				$.post("{{url('weight/descripencies/bycourier')}}", {
					_token: "{{csrf_token()}}",
					order_id: order_id,
					acton: acton
				},
				function(res) {
					toastrMsg(res.status, res.msg);
					location.reload();
				}, 'Json');
				} else {
				$(obj).val('');
			}
		})
	}
	
	function viewImage(obj, event) {
		event.preventDefault();
		var id = $(obj).attr('data-id');
		$.get("{{url('weight/descripencies/view_image')}}/" + id, function(res) {
			$('#header_msg_image').html(res.header_msg);
			$('#display_view_image').html(res.view);
			$('.view_history').modal('hide');
			$('.view_image').modal('show');
		}, 'Json');
	}
</script>
@endpush