@extends('layouts.backend.app')
@section('title', config('setting.company_name') . ' - Shipping Charge Report')
@section('header_title', 'Shipping Charge Report')
@section('content')
    <style>
        .tooltip .tooltiptext {
            text-align: left !important;
            padding: 5px 0 5px 5px !important;
        }

        td p {
            margin-bottom: 0;
        }

        .dataTables_length {
            margin-top: 5px;
        }

        .dataTables_length {
            margin-top: 5px;
        }

        .totals-wrap .label {
            font-weight: 600;
        }

        .totals-wrap .value {
            font-size: 1.15rem;
        }
    </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="ordr-main-001">
                    <div class="main-filter-weight ">
                        <div class="row row-re paymentSearchForm">
                            @if (Auth::user()->role == 'admin')
                                <div class="col-lg-2 col-sm-6">
                                    <div class="main-selet-11">
                                        <select name="user" id="user_id">
                                            <option value=""> All Users </option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"> {{ $user->name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-lg-2 col-sm-6">
                                <div class="main-selet-11">
                                    <input type="text" class="form-control datepicker " name="fromdate"
                                        <?php echo isset($_GET['fromdate']) ? $_GET['fromdate'] : ''; ?> id="fromdate" placeholder="From Date">
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6">
                                <div class="main-selet-11">
                                    <input type="text" class="form-control datepicker" name="todate" <?php echo isset($_GET['todate']) ? $_GET['todate'] : ''; ?>
                                        id="todate" placeholder="To Date">
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6">
                                <div class="main-selet-11">
                                    <button class="btn-main-1 search_user search-btn-remi">Search</button>
                                </div>
                            </div>
                            <!-- Totals on the right -->
                            <div class="col-lg-4 ms-lg-auto">
                                <div class="d-flex justify-content-end gap-4 totals-wrap">
                                    <div class="text-end">
                                        <div class="label">Total Shipping Charge</div>
                                        <div id="total_shipping" class="value">₹0.00</div>
                                    </div>
                                    <div class="text-end ml-2">
                                        <div class="label">Total Profit</div>
                                        <div id="total_profit" class="value">₹0.00</div>
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
                                            <th> Date</th>
                                            <th> Seller Details </th>
                                            <th> Order details </th>
                                            <th> Shipping details</th>
                                            <th> Shipping Charge </th>
                                            {{-- <th> Comission Charge </th>  --}}
                                            <th> profit</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('js')
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script>
            var dataTable = $('#payment_datatable').DataTable({
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
                lengthMenu: [
                    [10, 25, 50, 100, 200, 500, 1000000],
                    [10, 25, 50, 100, 200, 500, 'All']
                ], // ðŸ”¥ options shown in dropdown
                order: [
                    [0, 'desc']
                ],
                bAutoWidth: false,
                "ajax": {
                    "url": "{{ route('report.shipping-charge.ajax') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.search = $('input[type="search"]').val();
                        d.user_id = $('.paymentSearchForm #user_id').val();
                        d.fromdate = $('.paymentSearchForm #fromdate').val();
                        d.todate = $('.paymentSearchForm #todate').val();
                    },
					// Update totals UI
					dataSrc: function(json) {
						const ship = json?.totals?.total_shipping ?? 0;
						const prof = json?.totals?.total_profit   ?? 0;
						$('#total_shipping').text(ship);
						$('#total_profit').text(prof);
						return json.data || [];
					}
                },
				
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "order_date"
                    },
                    {
                        "data": "seller_details"
                    },
                    {
                        "data": "order_details"
                    },
                    {
                        "data": "shippings"
                    },
                    {
                        "data": "shipping_charges"
                    },

                    {
                        "data": "profit"
                    }
                ],
                drawCallback: function() {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('.search_user').click(function() {
                dataTable.draw();
            });

            $(document).on('click', '.show-details-btn', function() {
                var items = $(this).data('order'); // JSON string automatically converted by jQuery
                if (typeof items === 'string') {
                    items = JSON.parse(items);
                }

                var totalAmount = 0;
                var html =
                    "<table class='table table-bordered table-sm'><thead><tr><th>Category</th><th>Name</th><th>SKU</th><th>HSN</th><th>Amount</th><th>Qty</th></tr></thead><tbody>";

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
                html += "<tfoot><tr><th colspan='4'>Total</th><th colspan='2'>" + totalAmount.toFixed(2) +
                    "</th></tr></tfoot>";
                html += "</table>";

                $('#infoModalLabel').html("Product Details");
                $('#infoModalBody').html(html);
                $('#infoModal').modal('show');
            });
        </script>
    @endpush
