@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Shipping Charge Report')
@section('header_title','Shipping Charge Report')
@section('content') 
<style>
	.tooltip .tooltiptext 
	{ 
	text-align: left !important; 
	padding: 5px 0 5px 5px !important;
	}	
	td p {
	margin-bottom: 0;
    }
    .dataTables_length{
	margin-top:5px;
    }
</style>
<div class="content-page">
	<div class="content">
		<!-- Start Content-->
		<div class="container-fluid">
			<div class="ordr-main-001">
				<div class="main-filter-weight ">
                    <div class="row row-re paymentSearchForm">  
						@if(Auth::user()->role == "admin")                  
						<div class="col-lg-2 col-sm-6">
							<div class="main-selet-11">
								<select name="user" id="user_id">
									<option value=""> All Users </option> 
									@foreach ($users as $user)
									<option value="{{$user->id}}"> {{$user->name}}  </option>
									@endforeach
								</select>
							</div>
						</div>
						@endif 
						<div class="col-lg-2 col-sm-6">
							<div class="main-selet-11">
								<input type="text" class="form-control datepicker " name="fromdate" <?php echo (isset($_GET['fromdate']))?$_GET['fromdate']:''; ?> id="fromdate" placeholder="From Date">
							</div>
						</div>
						<div class="col-lg-2 col-sm-6">
							<div class="main-selet-11">
								<input type="text" class="form-control datepicker" name="todate" <?php echo (isset($_GET['todate']))?$_GET['todate']:''; ?> id="todate" placeholder="To Date">
							</div>
						</div>
						<div class="col-lg-2 col-sm-6">
							<div class="main-selet-11">
								<button class="btn-main-1 search_user">Search</button>
							</div>
						</div>
					</div> 
				</div>      
				<div class="main-calander-11 mt-2"> 
					<div class="main-data-teble-1 table-responsive">
						<table id="payment_datatable" class="" style="width:100%">
							<thead>
								<tr>
									<th> Sr.No</th>
									<th> Seller Details </th>
									<th> Order details </th>
									<th> Shipping details</th>
									<th> Charge </th>
									<th> Shipping charges</th> 
								</tr>
							</thead> 
						</table>
					</div>
				</div> 
			</div>
		</div>
	</div>
</div> 
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="hotelInfoModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="infoModalLabel">Activity Description</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div> 
			<div class="modal-body" id="infoModalBody">
				<!-- HTML hotel description appears here -->
			</div>
		</div>
	</div>
</div>
@endsection
@push('js')  
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>  
<script> 
	var dataTable = $('#payment_datatable').DataTable({
		processing:true,
		"language": {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
		}, 
		serverSide:true,
		bLengthChange: true,
		searching: true,
		bFilter: true,
		bInfo: true,
		iDisplayLength: 25, 
    	lengthMenu: [ [10, 25, 50, 100, 200, 500, 1000000], [10, 25, 50, 100, 200, 500, 'All'] ], // ðŸ”¥ options shown in dropdown
		order: [[0, 'desc'] ],
		bAutoWidth: false,			 
		"ajax":{
            "url": "{{ route('report.shipping-charge.ajax') }}",
            "dataType": "json",
            "type": "POST",
            "data": function (d) {
				d._token   = "{{csrf_token()}}";
				d.search   = $('input[type="search"]').val(); 
				d.user_id   = $('.paymentSearchForm #user_id').val(); 
				d.fromdate   = $('.paymentSearchForm #fromdate').val(); 
				d.todate   = $('.paymentSearchForm #todate').val();  
			}
		},
		"columns": [
		{ "data": "id" }, 
		{ "data": "seller_details" }, 
		{ "data": "order_details" }, 
		{ "data": "shippings" }, 
		{ "data": "charge" }, 
		{ "data": "shipping_charges"}
		],
		drawCallback: function () {
			$('[data-toggle="tooltip"]').tooltip();
			//$('[data-fancybox="images"]').fancybox({ loop: true });
		}
	}); 
	
	$('.search_user').click(function (){
		dataTable.draw();	
	}); 
	
	$(document).on('click', '.show-details-btn', function () {
		var items = $(this).data('order'); // JSON string automatically converted by jQuery
		if (typeof items === 'string') {
			items = JSON.parse(items);
		}

		var totalAmount = 0;
		var html = "<table class='table table-bordered table-sm'><thead><tr><th>Category</th><th>Name</th><th>SKU</th><th>HSN</th><th>Amount</th><th>Qty</th></tr></thead><tbody>";

		items.forEach(function(item) {
			html += "<tr>" +
						"<td>" + item.product_category + "</td>" +
						"<td>" + item.product_name + "</td>" +
						"<td>" + item.sku_number + "</td>" +
						"<td>" + item.hsn_number + "</td>" +
						"<td>" + item.amount + "</td>" +
						"<td>" + item.quantity + "</td>" +
					"</tr>";
			totalAmount += parseFloat(item.amount * item.quantity) || 0;
		});

		html += "</tbody>";
		html += "<tfoot><tr><th colspan='4'>Total</th><th colspan='2'>" + totalAmount.toFixed(2) + "</th></tr></tfoot>";
		html += "</table>";

		$('#infoModalLabel').html("Product Details");
		$('#infoModalBody').html(html);
		$('#infoModal').modal('show');
	});
</script>
@endpush