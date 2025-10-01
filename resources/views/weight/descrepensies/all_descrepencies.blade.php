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
							<form method="GET" action="">
								<div class="row row-re">
									<!-- Date Input -->
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<input type="text" class="form-control new-height-fcs-rmi datepicker" name="date" id="date" 
											value="{{ old('date', request('date')) }}" placeholder="Date">
										</div>
									</div>
									
									<!-- Search Input -->
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<input type="text" class="form-control new-height-fcs-rmi" name="search" id="search" 
											value="{{ old('search', request('search')) }}" 
											placeholder="Search with AWB Number or Order ID">
										</div>
									</div>
									
									<!-- Weight Status Dropdown -->
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<select name="weight_status" class="form-control new-height-fcs-rmi" style="background-color: #F3F3F3 !important;">
												<option value="">All Statuses</option>
												@foreach(['New Descrepency', 'Accepted', 'Auto Accepted', 'Dispute Accepted by Courier', 'Dispute Rejected by Courier'] as $status)
												<option value="{{ $status }}" {{ request('weight_status') == $status ? 'selected' : '' }}>
													{{ $status }}
												</option>
												@endforeach
											</select>
										</div>
									</div>
									
									<!-- User Dropdown -->
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<select name="user_id" class="form-control new-height-fcs-rmi" style="background-color: #F3F3F3 !important;">
												<option value="">All Users</option>
												@foreach($users as $user)
												<option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
													{{ $user->name }}
												</option>
												@endforeach
											</select>
										</div>
									</div>
									
									<!-- Search Button -->
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<button type="submit" class="btn-main-1 search_data search-btn-remi">Search</button>
										</div>
									</div>
								</div>
							</form>
						</div>
						<hr> 
						<div class="amin-slip-1">
							<div class="main-tab-11">
								<div class="main-data-teble-1 table-responsive">
									<table class="dataTable no-footer" style="width:100%">
										<thead>
											<tr>
												<th>Status Updated On</th>
												<th>User Name</th> 
												<th>Shipping Details</th>
												<th>Applied Weight</th>
												<th>Charged Weight</th>
												<th>Excess Weight & Charges</th>
												<th>Courier Images</th>
												<th>Status</th>
												<th>Actions</th>
											</tr>
										</thead>
										<tbody>
											@forelse($orders as $order)
												<tr>
													<td>{{ date('d M Y h:i:s', strtotime($order->weight_update_date ?? $order->created_at)) }}</td>
													<td>{{ $order->user->name ?? 'N/A' }}</td> 
													<td>
														{{ $order->courier_name }}<br>
														<b>AWB:</b> <a href="{{ url('order/details', $order->id) }}">{{ $order->awb_number }}</a> <br>
														<b>Status:</b> {{ $order->status_courier }} <br>
													</td>
													<td>
														<b>{{ $order->applicable_weight }} kg</b><br>
														Dead Weight: {{ $order->weight }} Kg <br>
														Volumetric Weight: {{ round(($order->length * $order->width * $order->height) / 5000, 2) }} Kg <br>
														({{ $order->length }}x{{ $order->width }}x{{ $order->height }} cm)
													</td>
													<td>
														@if($order->excessWeight)
															<b>{{ $order->excessWeight->chargeable_weight }} kg</b> 
														@endif
													</td>
													<td>
														@if($order->excessWeight)
															<span>Excess Weight: <p style="color: red;margin: 0;">{{ $order->excessWeight->excess_weight }} Kg</p></span>  
															<span>Excess Charges: <p style="color: red;margin: 0;">{{ $order->excessWeight->excess_charge }}</p></span>
														@endif
													</td>
													<td>
														@if($order->excessWeight && !empty($order->excessWeight->product_images))
															<div class="row">
																@foreach($order->excessWeight->product_images as $image)
																	<div class="col-md-4">
																		<a href="{{ url('storage/'.$image) }}" target="_blank">
																			<img src="{{ url('storage/'.$image) }}" style="height:50px">
																		</a>
																	</div>
																@endforeach
															</div>
														@endif
													</td>
													<td>
														@if(!empty($order->weight_status))
															<button class="main-gray-b">{{ $order->weight_status }}</button>
														@endif
													</td>
								
													 @if(config('permission:weight_descripencies.add'))
														<td>
															@if(!empty($order->weight_status))
																@if($order->weight_status == "remark")
																	<select name="action" id="take_action" class="form-control" onchange="takeAction(this,{{$order->id}})">
																		<option value="">Take Action</option> 
																		<option value="Accepted">Accepted</option>
																		<option value="Remark">Remark</option>
																	</select>
																@endif

																@if(in_array($order->weight_status, ["Accepted", "Auto Accepted"]))
																<a class="btn-main-1 ml-2" style="color:#fff !important" href="{{ url('weight/descripencies/remark', $order->id) }}"> View Remark </a>
																	<a class="btn-main-1" style="color:#fff !important" href="javascript:;" data-order_id="{{ $order->id }}" onclick="viewHistory(this, event)"> View Details </a>
																@endif
															@else
																<button class="btn-main-1" data-id="{{ $order->id }}" data-user_id="{{ $order->user_id }}" onclick="raiseExcess(this,event)">Raise Weight</button>
															@endif
														</td>
													@endif 
												</tr>
											@empty
												<tr>
													<td colspan="9" class="text-center">No orders found</td>
												</tr>
											@endforelse

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
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header head-00re pb-0" style="border: none;">
				<h5 class="modal-title" id="exampleModalLabel"> Action Excess Weight </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="raiseWeightForm" method="post" action="{{ route('weight.raise.excess-weight') }}" enctype="multipart/form-data" style="margin-top: 20px;">
				@csrf
				<div class="modal-body" style="border-top: 1px solid #dcdcdc;">
					<div class="row">
						<br>
						<div class="form-group col-md-12">
							<label> Chargeable Weight{{--  <span class="text-danger">*</span>--}}  </label>
							<input type="text" class="form-control new-border-popups" name="chargeable_weight" id="chargeable_weight" placeholder="Chargeable Weight" required>
						</div>
					 
						<div class="form-group col-md-12">
							<label> Excess Weight {{-- <span class="text-danger">*</span>--}}  </label>
							<input type="text" class="form-control new-border-popups" name="excess_weight" id="excess_weight" placeholder="Excess Weight" required>
						</div>
					 
						<div class="form-group col-md-12">
							<label> Excess Charge {{-- <span class="text-danger">*</span>--}}  </label>
							<input type="text" class="form-control new-border-popups" name="excess_charge" id="excess_charge" placeholder="Excess Charge" required>
						</div>
						<div class="form-group col-md-12">
							<label> Product Images </label>
							<input type="file" class="form-control new-border-popups" name="product_images[]" id="product_images" multiple>
						</div>
					</div>
					<input type="hidden" name="order_id" id="order_id">
					<input type="hidden" name="user_id" id="user_id"> 
				</div> 
				<div class="px-2 pb-2" style="justify-content: center;">
					<button type="submit" class="btn new-submit-popup-btn"> Submit </button>
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
			<div class="modal-body" id="display_view_image"></div>
		</div>
	</div>
</div>
@endsection
@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script> 
	var dataTable = $('#ratecard_datatable').DataTable({
		paging: false,
		searching: true,
		info: false,
		lengthChange: false,
		ordering: true
	});
	
	$('#ratecard_datatable_filter').hide();
	
	setTimeout(function(){
		$('#weight').trigger('change');
	}, 1000);
	  
	$('#weight').on('change', function () {
		var weightValue = $(this).val().trim(); 
		dataTable
		.column(2) 
		.search('^' + weightValue + '$', true, false) // exact match using regex
		.draw();
	});
 
	function viewHistory(obj, event) {
		event.preventDefault();
		var order_id = $(obj).attr('data-order_id');
		
		$.get("{{url('weight/descripencies/history')}}/" + order_id, function(res) {
			$('#header_msg').html(res.header_msg);
			$('#display_view').html(res.view);
			$('.view_history').modal('show');
		}, 'Json');
	}
	
	function takeAction(obj, order_id) {
		var action = $(obj).val();
		if(!action)
		{
			return false;
		} 
		 
		if(action === "Accepted")
		{
			Swal.fire({
				title: "Are you sure you want to " + action + "?",
				text: "You won't be able to revert this!",
				type: "warning",
				showCancelButton: !0,
				confirmButtonColor: "#31ce77",
				cancelButtonColor: "#f34943",
				confirmButtonText: "Yes, " + action + " it!"
			}).then(function(t) {
				if (t.value) {
					$.get("{{url('weight/descripencies/accepted')}}/" + order_id+"?by=admin", function(res) {
						toastrMsg(res.status, res.msg);
						location.reload();
					}, 'Json');
				} else {
					$(obj).val('');
				}
			})
		}
		else
		{
			window.location.href = "{{url('weight/descripencies/remark')}}/" + order_id; 
		}
	}
	
	function raiseExcess(obj, event) {
		event.preventDefault();
		
		const $obj = $(obj);  // Cache the jQuery object
		const order_id = $obj.data('id'); 
		const user_id = $obj.data('user_id');

		$('#raiseWeightForm #order_id').val(order_id);
		$('#raiseWeightForm #user_id').val(user_id);
		
		$('.excees_weight').modal('show');
	}

	
	/* function takeAction(obj, order_id) {
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
	} */
	
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