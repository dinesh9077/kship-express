@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - NDR Orders')
@section('header_title','NDR Orders')
@section('content') 
<style>
	.tooltip .tooltiptext {
		text-align: left !important;
		padding: 5px 0 5px 5px !important;
	}

	td p {
		margin-bottom: 0;
	}
</style>
<div class="content-page">
	<div class="content"> 
		<div class="container-fluid">
			<div class="main-order-page-1">
				<div class="main-order-001">
					<div class="main-calander-11">
						<div class="main-data-teble-1 table-responsive">
							<table id="ndrDataTable" style="width:100%">
								<thead>
									<tr> 
										<th> # </th>
										<th> Order Date</th> 
										<th> NDR Date</th> 
										<th> Seller Details </th> 
										<th> Customer details </th> 
										<th> Payment </th> 
										<th> Status </th>
										<th> Exception Info </th> 
										<th> Action </th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>  

<div class="modal fade bd-example-modal-lg main-bg0-021 takeActionNdrForm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header pb-0" style="border: none;">
                <h5 class="modal-title pick-up0" id="exampleModalLabel"> NDR Submit Form </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="NDRForm" action="{{ route('ndr.order.raise') }}" method="post" enctype="multipart/form-data"> 
				@csrf
				<div class="modal-body"> 
					<input type="hidden" name="order_id" id="order_id">
					<input type="hidden" name="shipping_company_id" id="shipping_company_id">
					<input type="hidden" name="awb_number" id="awb_number">
					
					<div class="form-group">
						<label>Choose Action</label>
						<select name="action" class="form-control" id="action" required>
							<option value="">Choose Action</option>
							<option value="re-attempt">Re-Attempt</option>
							<option value="change_phone">Update Phone Number</option>
							<option value="change_address">Update Address</option>
						</select>
					</div>
 
					<!-- Phone Number Input (Hidden Initially) -->
					<div class="form-group" id="phone-input" style="display: none;">
						<label>New Phone Number</label>
						<input type="text" name="phone" id="phone" class="form-control" placeholder="Enter New Phone Number">
					</div>

					<!-- Address Input (Hidden Initially) -->
					<div class="form-group" id="address-input" style="display: none;">
						<label>New Address</label>
						<textarea name="address_1" id="address_1" class="form-control" placeholder="Enter New Address"></textarea>
					</div>
					
					<!-- Re-Attempt Date -->
					<div class="form-group" id="date-picker">
						<label>Re-Attempt Date</label>
						<input type="date" name="reattemptdate" class="form-control" value="{{ date('Y-m-d') }}" required>
					</div>
					
					<div class="form-group">
						<label>Remark</label>
						<textarea class="form-control" name="remarks" placeholder="Enter Remark" required></textarea>
					</div>
				</div>

				<div class="modal-footer" style="margin: auto; border: none;"> 
					<button type="submit" class="btn btn-primary btn-main-1">Submit</button>
				</div>
			</form> 

		</div>
	</div>
</div>
@endsection

@push('js')
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>  
	<script> 
		document.getElementById('action').addEventListener('change', function () {
			let action = this.value;

			// Get input elements
			let phoneInputDiv = document.getElementById('phone-input');
			let addressInputDiv = document.getElementById('address-input');
			let phoneField = document.getElementById('phone');
			let addressField = document.getElementById('address_1');

			// Hide all optional fields initially
			phoneInputDiv.style.display = 'none';
			addressInputDiv.style.display = 'none';
			phoneField.removeAttribute("required");
			addressField.removeAttribute("required");

			// Show and set required based on selected action
			if (action === 'change_phone') {
				phoneInputDiv.style.display = 'block';
				phoneField.setAttribute("required", "required");
			} else if (action === 'change_address') {
				addressInputDiv.style.display = 'block';
				addressField.setAttribute("required", "required");
			}
		});
 
		$(document).ready(function() {
			$('ul.tabs-001 li').click(function() {
				var tab_id = $(this).attr('data-tab');
				$('ul.tabs-001 li').removeClass('current');
				$('.tab-content11').removeClass('current');
				$(this).addClass('current');
				$("#" + tab_id).addClass('current');
			})
		})
 
		function redirectUrl(status) {
			let url = new URL(window.location.href);
			url.searchParams.set("status", status);
			window.location.href = url.toString();
		}
 
		var dataTable = $('#ndrDataTable').DataTable({
			processing: true,
			serverSide: true,
			lengthChange: true,
			searching: true,
			autoWidth: false,
			pageLength: 25,
			orderable: true,
			language: { processing: 'Loading...' },
			ajax: {
				url: "{{ route('ndr.order.ajax') }}",
				type: "POST",
				data: function(d) {
					d._token = "{{ csrf_token() }}";
					d.search = $('input[type="search"]').val(); 
					d.status = @json($status);
				}
			},
			columns: [
				{ data: "id", orderable: false },
				{ data: "order_date" },
				{ data: "ndr_date" },
				{ data: "seller" }, 
				{ data: "customer" },
				{ data: "payment" }, 
				{ data: "status_courier" }, 
				{ data: "exception_info" },
				{ data: "action" }
			],
			drawCallback: function() { 
				$('[data-toggle="tooltip"]').tooltip();
			},
			columnDefs: [{ orderable: false, targets: 0 }]
		});
  
		$('.search_data').click(function() { 
			dataTable.draw();
		}); 
		  
		function openNdrUpdateModal(obj, event)
		{
			var orderId = $(obj).data('id');
			var shippingId = $(obj).data('shipping_id');
			var awbNumber = $(obj).data('awb_number');
		  
			$('#NDRForm #shipping_company_id').val(shippingId);
			$('#NDRForm #order_id').val(orderId);
			$('#NDRForm #awb_number').val(awbNumber);
			$('.takeActionNdrForm').modal('show');
		}	
		
		$('#NDRForm').submit(function(event) {
			event.preventDefault();  
			$(this).find('button').prop('disabled',true); 
			var formData = new FormData(this); 
			formData.append('_token',"{{csrf_token()}}"); 
			$.ajax({
				async: true,
				type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: formData,
				cache: false,
				processData: false,
				contentType: false, 
				dataType: 'Json', 
				success: function (res) 
				{  
					if(res.status == "success")
					{
						toastrMsg('success', res.msg);
						setTimeout(function() {
							location.reload();
						}, 3000);
					}
					else
					{
						toastrMsg('error',res.msg);
					}  
				},
				error: function(xhr, status, error) {
					console.error(xhr.responseText);
				}
			});
		});
	</script> 
@endpush