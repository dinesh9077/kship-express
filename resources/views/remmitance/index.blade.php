@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Remmitance')
@section('header_title','Remmitance')
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
</style>
<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="main-find-weght">
                <div class="main-order-page-1">
                    <div class="main-order-001">  
                        <div class="main-filter-weight">
							<form method="get" id="remmitanceSearchForm">
								<div class="inner-page-heading">
									<div class="row row-re w-100">
										<div class="col-lg-2 col-sm-6">
											<div class="main-selet-11">
												<input type="text" class="form-control datepicker" name="from_date" 
													   value="{{ request()->get('from_date', '') }}" 
													   id="from_date" placeholder="From Date">
											</div>
										</div>
										<div class="col-lg-2 col-sm-6">
											<div class="main-selet-11">
												<input type="text" class="form-control datepicker" name="to_date" 
													   value="{{ request()->get('to_date', '') }}" 
													   id="to_date" placeholder="To Date">
											</div>
										</div>
										
										@if(session('success'))
											<div class="alert alert-success">
												{{ session('message') }}
											</div>
										@endif
										
										<div class="col-lg-2 col-sm-6">
											<div class="main-selet-11">
												<select name="shipping_company_id" id="shipping_company_id" class="form-control select2">
													<option value="">All Shipping Company</option> 
													@foreach($shippingCompany as $shipping)
														<option value="{{ $shipping->id }}" 
															{{ (request()->get('shipping_company_id') == $shipping->id) ? 'selected' : '' }}>
															{{ $shipping->name }}
														</option>
													@endforeach
												</select>
											</div>
										</div>
										
										<div class="col-lg-2 col-sm-6">
											<div class="main-selet-11">
												<button type="button" class="btn-main-1 serachFilterData">Search</button>
											</div>
										</div>
									</div>
									@if(config('permission.remittance.add'))	
										<div class="order-111-add">
											<div class="main-selet-11 text-end" style="white-space: nowrap;">
												<button type="button" id="remittanceBtn" class="btn-main-1 remittance_data" style="display:none;">Remittance Create</button>
												<!--<b>Total Amount : </b> ₹<span id="totAmt"></span>-->
											</div> 
										</div>
									@endif
								</div>
							</form> 
						</div>

                        
                        <div class="ordr-main-001">
                            <ul id="tab">                             
                                <li class="active">
                                    <div class="main-calander-11"> 
                                        <div class="main-data-teble-1 table-responsive">
                                            <table id="remmitance_datatable" class="" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th> <input type="checkbox" id="checkboxesMain"></th>
                                                        <th> Order Id</th>
                                                        <th> Seller Details </th>
                                                        <th> Delivery Date </th>
                                                        <th> Amount </th>
                                                        <th> Shipping Details </th>
                                                        <th> Status </th> 
                                                    </tr>
                                                </thead> 
                                            </table>
                                        </div>
                                    </div>
                                </li> 
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-lg" id="remmitance_form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> 
     <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remmitace Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('order.remmitance.store') }}" method="post">
                @csrf
                <input type="hidden" name="is_remmitance" value="1">
                <input type="hidden" name="orders_id" id="remittance_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reference Id</label>
                        <input type="text" name="remittance_reference_id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Remittance Amount</label>
                        <input type="text" name="remittance_amount" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" id="amount" class="form-control"  required>
                    </div>
                    <div class="form-group">
                        <label>Remittance Date</label>
                        <input type="text" name="remittance_date" class="form-control datepicker"  required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('js')  
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 
<script>
$(document).ready(function () {
    $('.remittance_data').hide();  

    $.fn.dataTable.ext.errMode = 'none'; // Suppress DataTable errors in console

    var dataTable = $('#remmitance_datatable').DataTable({
        processing: true,
        language: {
            loadingRecords: '&nbsp;',
            processing: 'Loading...'
        },
        serverSide: true,
        lengthChange: true,
        searching: true,
        filter: true,
        info: true,
        pageLength: 25,
        order: false,  
		columnDefs: [
			{ orderable: false, targets: '_all' }
		], 
        autoWidth: false, 
        ajax: {
            url: "{{ route('order.remmitance.ajax') }}",
            type: "POST",
            data: function (d) {
                d._token = "{{ csrf_token() }}";
                d.search = $('#remmitance_datatable_filter input').val(); // More efficient search value
                d.shipping_company_id = $('#remmitanceSearchForm #shipping_company_id').val();
                d.from_date = $('#remmitanceSearchForm #from_date').val();
                d.to_date = $('#remmitanceSearchForm #to_date').val();
            }
        },
        columns: [
            { data: "id" },
            { data: "order_id" },
            { data: "seller_details" },
            { data: "delivery_date" },
            { data: "amount" },
            { data: "shipment_details" },
            { data: "status_courier" }
        ],
        drawCallback: function (settings) {
            $('#totAmt').text(settings.json.totAmt);
            updateRemittanceButtonVisibility();
        }
    });

    // Search Button Click
    $('.serachFilterData').click(function () {
        dataTable.draw();
    });

    // Select/Deselect All Checkboxes
    $('#checkboxesMain').on('click', function () {
        var isChecked = $(this).prop('checked');
        $(".order-checkbox").prop('checked', isChecked);
        updateRemittanceButtonVisibility();
    });

    // Individual Checkbox Click
    $(document).on('click', '.order-checkbox', function () {
        var allChecked = $('.order-checkbox:checked').length === $('.order-checkbox').length;
        $('#checkboxesMain').prop('checked', allChecked);
        updateRemittanceButtonVisibility();
    });

    // Show Modal with Selected Order IDs
    $('.remittance_data').click(function () {
        var orderIds = $(".order-checkbox:checked").map(function () {
            return $(this).attr('data-order_id');
        }).get().join(',');

        $("#remittance_id").val(orderIds);
        $('#remmitance_form').modal('show');
    });

    // Tooltip Activation
    $('#remmitance_datatable').on('draw.dt', function () {
        $('[data-toggle="tooltip"]').tooltip();

        @if(Auth::user()->role != "admin") 
            dataTable.columns(1).visible(false);
        @endif 
    });

    // Function to Show/Hide Remittance Button
    function updateRemittanceButtonVisibility() {
        $('.remittance_data').toggle($('.order-checkbox:checked').length > 0);
    }
});
</script>

@endpush