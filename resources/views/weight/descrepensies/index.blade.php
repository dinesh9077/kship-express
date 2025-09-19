@extends('layouts.backend.app')
@section('title',config('setting.company_name').' -  Weight Descrepencies')
@section('header_title','Weight Descrepencies')
@section('content') 
 <style>
	.modal-body h5{
	width:100%;
	}
	.model-width-1 {
    width: 50% !important;
    max-width: 100%;
	}
	.pagination {
		display: -webkit-box;
		display: -ms-flexbox; 
		padding-left: 0;
		list-style: none;
		border-radius: .25rem;
		margin-top: 20px;
		float: right;
	}
	
</style>
<div class="content-page">
    <div class="content"> 
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="main-find-weght"> 
				<div class="main-order-page-1">
					<div class="main-order-001">
						<div class="main-filter-weight">
							<form method="get" action="">
								<div class="row">
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<input type="text" class="form-control datepicker" name="date" <?php echo (isset($_GET['date'])) ? $_GET['date'] : ''; ?> id="date" placeholder="Date">
										</div>
									</div>
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<input type="text" class="form-control" value="<?php echo (isset($_GET['search'])) ? $_GET['search'] : ''; ?>" name="search" id="search" placeholder="Seacrh with AWB Number Or Order Id">
										</div>
									</div> 
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<button class="btn-main-1">Search</button>
										</div>
									</div>
								</div>
							</form>
						</div>

						<div class="amin-slip-1">
							<div class="main-tab-11">
								<div class="main-data-teble-1 table-responsive">
									<table id="example" class="dataTable no-footer" style="width:100%">
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
  
													<!-- Weight Status -->
													<td>
														<button class="main-gray-b">{{ $order->weight_status ?? 'New' }}</button> 
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

<div class="modal fade view_history" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg model-width-1">
		<div class="modal-content">
			<div class="modal-header head-00re pb-0" style="border: none;">
				<h5 class="modal-title" id="header_msg">  </h5>
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
				<h5 class="modal-title" id="header_msg_image">  </h5>
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
	
function viewHistory(obj,event)
{
	event.preventDefault();
	var order_id = $(obj).attr('data-order_id'); 
	$.get("{{url('weight/descripencies/history')}}/"+order_id, function(res)
	{
		$('#header_msg').html(res.header_msg);
		$('#display_view').html(res.view);
		$('.view_history').modal('show');
	},'Json');
}

function takeAction(obj,order_id)
{
	var acton = $(obj).val(); 
	if(acton == "Accepted")
	{
		Swal.fire({
			title:"Are you sure you want to "+acton+"?",
			text:"You won't be able to revert this!",
			type:"warning",
			showCancelButton:!0,
			confirmButtonColor:"#31ce77",
			cancelButtonColor:"#f34943",
			confirmButtonText:"Yes, "+acton+" it!"
			}).then(function (t) {
			if(t.value)
			{
				$.get("{{url('weight/descripencies/accepted')}}/"+order_id, function(res)
				{
					toastrMsg(res.status,res.msg);  
					location.reload();
				},'Json');
			}
			else
			{
				$(obj).val('');
			}
		})
	}
	else
	{	
		window.location.href = "{{url('weight/descripencies/reject')}}/"+order_id;
	}
}


	function viewImage(obj,event)
	{
		event.preventDefault();
		var id = $(obj).attr('data-id'); 
		$.get("{{url('weight/descripencies/view_image')}}/"+id, function(res)
		{
			$('#header_msg_image').html(res.header_msg);
			$('#display_view_image').html(res.view);
			$('.view_history').modal('hide');
			$('.view_image').modal('show');
		},'Json');
	}
</script>
@endpush