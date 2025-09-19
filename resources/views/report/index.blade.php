@extends('layouts.backend.app')
@section('title', config('setting.company_name') . ' - Report')
@section('header_title', 'Report')
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
                <div class="ordr-main-001">
                    <ul id="tab">
                        <li class="active">
                            <div class="main-roow-1 main-calander-11">
                                <div class="table-responsive">
                                    <div class="col-12 d-flex">
                                        <div class="col-6">
                                            <h5 style="text-transform: uppercase;"> Daily Order </h5>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('report') }}"
                                                class="{{ request()->is('report*') ? 'active' : '' }}">

                                                <h1><i class="mdi mdi-package-variant-closed"></i> </h1>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="active">
                            <div class="main-roow-1 main-calander-11">
                                <div class="table-responsive">
                                    <div class="col-12 d-flex">
                                        <div class="col-6">
                                            <h5 style="text-transform: uppercase;"> Daily Wallet </h5>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('daily_wallet') }}"
                                                class="{{ request()->is('daily_wallet*') ? 'active' : '' }}">

                                                <h1> <img src="http://localhost/shipment/assets/images/dashbord/wallet-1.png" style="width: auto; height: 25px;"> </h1>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@push('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@endpush
