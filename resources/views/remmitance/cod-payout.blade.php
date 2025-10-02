@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - COD Payout')
@section('header_title','COD Payout')
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
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="main-find-weght">
                <div class="main-order-page-1">
                    <div class="main-order-001">
                        <div class="main-filter-weight">  
							<div class="inner-page-heading">
								<div class="row row-re w-100">
									{{-- From Date --}}
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<input type="text" 
												   class="form-control new-height-fcs-rmi datepicker" 
												   name="fromdate" 
												   id="fromdate" 
												   value="{{ request('fromdate') }}" 
												   placeholder="From Date">
										</div>
									</div>

									{{-- To Date --}}
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<input type="text" 
												   class="form-control new-height-fcs-rmi datepicker" 
												   name="todate" 
												   id="todate" 
												   value="{{ request('todate') }}" 
												   placeholder="To Date">
										</div>
									</div>

									{{-- Vendor (Admin only) --}}
									@if(Auth::user()->role === "admin")
										<div class="col-lg-2 col-sm-6">
											<div class="main-selet-11">
												<select name="vendor_id" id="vendor_id" class="new-height-fcs-rmi form-control " style="background-color: #F3F3F3 !important;">
													<option value="">Select User</option>
													@foreach($users as $user)
														<option value="{{ $user->user->id }}" 
															{{ request('vendor_id') == ($user->user->id ?? '') ? 'selected' : '' }}>
															{{ $user->user->name ?? '' }}
														</option>
													@endforeach
												</select>
											</div>
										</div>
									@endif

									{{-- Voucher Status --}}
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<select name="voucher_status" id="voucher_status" class="new-height-fcs-rmi  form-control" style="background-color: #F3F3F3 !important;">
												<option value="">Select Status</option>
												<option value="0" {{ request('voucher_status') === '0' ? 'selected' : '' }}>Unpaid</option>
												<option value="1" {{ request('voucher_status') === '1' ? 'selected' : '' }}>Paid</option>
											</select>
										</div>
									</div>

									{{-- Search Button --}}
									<div class="col-lg-2 col-sm-6">
										<div class="main-selet-11">
											<button type="button" class="btn-main-1 search_data search-btn-remi">Search</button>
										</div>
									</div>
								</div> 
							</div> 
                        </div>
                        <div class="ordr-main-001">
                            <ul id="tab">
                                <li class="active">
                                    <div class="main-calander-11">
                                        <div class="main-data-teble-1 table-responsive">
                                            <table id="remmitance_datatable" class="" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th> #</th>
                                                        <th> Seller Details </th>
                                                        <th> Generate On </th>
                                                        <th> Remittance Id </th>
                                                        <th> Cod Amount </th> 
                                                        <th> Status </th>
                                                        <th> Note </th>
                                                        <th> Reference No </th>
                                                        <th> Payment On</th> 
                                                        <th> Action</th>
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
<div class="modal fade bd-example-modal-lg" id="cod_payout_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> Cod Payout </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="payoutForm" action="{{ route('cod-payout-store')}}" method="post">
                @csrf
                <input type="hidden" name="id" id="id"> 
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reference No  <span class="text-danger">*</span></label>
                        <input type="text" name="reference_no" id="reference_no" class="form-control" required>
                    </div> 
                    <div class="form-group">
                        <label>Payout Date  <span class="text-danger">*</span></label>
                        <input type="date" name="payout_date" id="payout_date" class="form-control" required>
                    </div>
					<div class="form-group">
                        <label>Note  <span class="text-danger">*</span></label>
                        <input type="text" name="remarks" id="remarks" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" id="close_btn_" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 
<script>
    $(document).ready(function() { 
        $('ul.tabs-001 li').click(function() {
            var tab_id = $(this).attr('data-tab');
            $('ul.tabs-001 li').removeClass('current');
            $('.tab-content11').removeClass('current');
            $(this).addClass('current');
            $("#" + tab_id).addClass('current');
        })
    })


    var dataTable = $('#remmitance_datatable').DataTable({
        processing: true,
        "language": {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
        serverSide: true,
        bLengthChange: true,
        searching: true,
        bFilter: true,
        bInfo: true,
        iDisplayLength: 25,
        order: [
            [0, 'desc']
        ],
        bAutoWidth: false,
        "ajax": {
            "url": "{{ route('cod-payout-ajax') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) {
                d._token = "{{csrf_token()}}";
                d.search = $('input[type="search"]').val(); 
                d.fromdate = $('#fromdate').val();
                d.todate = $('#todate').val();
                d.voucher_status = $('#voucher_status').val();
                d.user_id = $('#vendor_id').val();
            }
        },
        "columns": [{
                "data": "id"
            },
            {
                "data": "seller_details"
            },
            {
                "data": "voucher_date"
            },
            {
                "data": "voucher_no"
            },
            {
                "data": "amount"
            },
            {
                "data": "status"
            },
            {
                "data": "remarks"
            },
            {
                "data": "reference_no"
            },
            {
                "data": "payout_date"
            },
            {
                "data": "action"
            },
        ]
    });

    $('.search_data').click(function() { 
        dataTable.draw();
    });

 
	function payNow(obj)
	{
		const $obj = $(obj);
		const payoutId = $obj.data('payout-id');
		$('#payoutForm').find('#id').val(payoutId);
		$('#cod_payout_modal').modal('show'); 
	}
 
    $('#remmitance_datatable').on('draw.dt', function() {
        $('[data-toggle="tooltip"]').tooltip({
            position: {
                my: "left bottom", // the "anchor point" in the tooltip element
                at: "left top", // the position of that anchor point relative to selected element
            }
        });
		
        @if(Auth::user()->role != "admin")
			dataTable.columns(1).visible(false);
        @endif
    });
</script>
@endpush