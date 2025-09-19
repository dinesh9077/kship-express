    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">

    <style type="text/css">
        .table>tbody>tr>td,
        .table>tbody>tr>th,
        .table>tfoot>tr>td,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>thead>tr>th {
            border-top: 1px solid #d4dde300;
            padding: 5px 0px;
            text-wrap: nowrap;
        }

        .table thead:first-child th {
            border-top: none;
            padding: 8px 0px;
        }

        .table thead:first-child th {
            border-bottom: solid 1px #000000;
        }

        @media (max-width: 1299px) {

            .main-text1 h2,
            .main-text h2 {
                font-size: 22px;
            }

            .main-text1 h3 {
                font-size: 16px;
            }
        }
    </style>


    <div class="content-wrapper">


        <section class="content">

            <div class="header-main mb-5">
                <h2>Dashboard</h2>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 box-width">
                    <div class="main-card chnge-bg">
                        <div class="d-flex justify-content-between">
                            <div class="main-card-img1">
                                <img src="<?php echo base_url('assets/admin/gif/income_01.gif') ?>">
                            </div>
                            <div class="main-text1">
                                <h3>Income</h3>
                                <h2>₹18,000</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 box-width">
                    <div class="main-card ">
                        <div class="d-flex justify-content-between">
                            <div class="main-card-img">
                                <img src="<?php echo base_url('assets/admin/gif/Coin.gif') ?>">
                            </div>
                            <div class="main-text">
                                <h3>Expense</h3>
                                <h2>₹20,000</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 box-width">
                    <div class="main-card">
                        <div class="d-flex justify-content-between">
                            <div class="main-card-img">
                                <img src="<?php echo base_url('assets/admin/gif/Invooice.gif') ?>">
                            </div>
                            <div class="main-text">
                                <h3>Invoice</h3>
                                <h2>18</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 box-width">
                    <div class="main-card">
                        <div class="d-flex justify-content-between">
                            <div class="main-card-img">
                                <img src="<?php echo base_url('assets/admin/gif/bill.gif') ?>">
                            </div>
                            <div class="main-text">
                                <h3>Invoice due </h3>
                                <h2>500</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-12 main-width1">
                    <div class="left-box">
                        <img src="<?php echo base_url('assets/admin/gif/Reciept_01.gif') ?>">
                        <h4>Scan receipts effortlessly,
                            anywhere</h4>
                        <p>Automatic expense tracking with Wave’s
                            mobile app.</p>
                        <a href="">Get started →</a>
                    </div>
                    <div class="left-box text-center">
                        <img src="<?php echo base_url('assets/admin/gif/Comp.gif') ?>">
                        <h4>Connect your bank account
                            or credit card</h4>
                        <p>Automate your bookkeeping by importing
                            transactions automatically.</p>
                        <button>Connect my account</button>
                    </div>
                    <div class="left-text">
                        <h3 class="box-title">Overdue invoices & bills</h3>
                        <div class="text-box">
                            <h5>Overdue Invoices</h5>
                            <ul>
                                <li>Softieons, ₹5,000.00</li>
                                <li>softieons, ₹5,000.00</li>
                            </ul>
                        </div>
                    </div>
                    <div class="left-text">
                        <h3 class="box-title">Things You Can Do</h3>
                        <p>Add a customer</p>
                        <p>Add a vendor</p>
                        <p>Customize your invoices</p>
                        <p>Invite a guest collaborator</p>
                        <p>Professional accounting help</p>
                        <p>Accept credit cards to get paid faster</p>
                    </div>
                </div>
                <div class="col-lg-9 col-md-12 main-width">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="right-chart">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3>Cash Flow</h3>
                                        <p>Cash coming in and going out of your business.</p>
                                    </div>
                                    <div>
                                        <a href="">View Report</a>
                                    </div>
                                </div>
                                <div id="chart"></div>
                            </div>
                        </div>

                    </div>
                    <h3 class="box-title">Payable & Owing</h3>

                    <div class="row">

                        <div class="col-lg-6">
                            <div class="table-responsive" style="width: 90%;">
                                <h3 class="box-title1">Invoices payable to you</h3>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <td class="color">Coming Due</td>
                                            <td class="text-right">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>1-30 days overdue</td>
                                            <td class="text-right">$3,844.84</td>
                                        </tr>
                                        <tr>
                                            <td>31-60 days overdue</td>
                                            <td class="text-right">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>61-90 days overdue</td>
                                            <td class="text-right">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>> 90 days overdue</td>
                                            <td class="text-right">$0.00</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                        <div class="col-lg-6">
                            <div class="table-responsive">
                                <h3 class="box-title1">Bills you owe</h3>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <td class="color">Coming Due</td>
                                            <td class="text-right">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>1-30 days overdue</td>
                                            <td class="text-right">$3,844.84</td>
                                        </tr>
                                        <tr>
                                            <td>31-60 days overdue</td>
                                            <td class="text-right">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>61-90 days overdue</td>
                                            <td class="text-right">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>> 90 days overdue</td>
                                            <td class="text-right">$0.00</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h3 class="box-title">Net Income</h3>

                    <div class="row">

                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fiscal Year</th>
                                            <th>Previous</th>
                                            <th>Previous</th>
                                        </tr>
                                        <tr>
                                            <td>Previous</td>
                                            <td>₹4,653,469.61</td>
                                            <td>₹4,653,469.61</td>
                                        </tr>
                                        <tr>
                                            <td>Previous</td>
                                            <td>₹4,653,469.61</td>
                                            <td>₹4,653,469.61</td>
                                        </tr>
                                        <tr>
                                            <td>Previous</td>
                                            <td>₹4,653,469.61</td>
                                            <td>₹4,653,469.61</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>



        </section>


        <!-- <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card-box">
                    <div class="heading mb-2">Profit and Loss</div>
                    <div id="line-adwords" class=""></div>
                    <div id="areachart" class=""></div>
                </div>
            </div>
        </div> -->

    </div>

    <!-- Resources -->
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <!-- Vendor js -->
    <script src="<?php echo base_url() ?>assets/admin/js1/vendor.min.js"></script>

    <!-- App js -->
    <script src="<?php echo base_url() ?>assets/admin/js1/app.min.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/js/irregular-data-series.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        var _seed = 42;
        Math.random = function() {
            _seed = _seed * 16807 % 2147483647;
            return (_seed - 1) / 2147483646;
        };
    </script>

    <script src="../../assets/irregular-data-series.js"></script>
    <script>
        var ts2 = 1484418600000;
        var dates = [];
        var spikes = [5, -5, 3, -3, 8, -8]
        for (var i = 0; i < 120; i++) {
            ts2 = ts2 + 86400000;
            var innerArr = [ts2, dataSeries[1][i].value];
            dates.push(innerArr)
        }
    </script>
    <script>
        var options = {
            series: [{
                name: 'XYZ MOTORS',
                data: dates
            }],
            colors: ['#7334D9'],

            chart: {
                type: 'area',
                stacked: false,
                height: 350,
                zoom: {
                    type: 'x',
                    enabled: true,
                    autoScaleYaxis: true
                },
                toolbar: {
                    autoSelected: 'zoom'
                }
            },
            dataLabels: {
                enabled: false
            },
            markers: {
                size: 0,
            },
            title: {
                text: 'Stock Price Movement',
                align: 'left'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.5,
                    opacityTo: 0,
                    stops: [0, 90, 100]
                },
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return (val / 1000000).toFixed(0);
                    },
                },
                title: {
                    text: 'Price'
                },
            },
            xaxis: {
                type: 'datetime',
            },
            tooltip: {
                shared: false,
                y: {
                    formatter: function(val) {
                        return (val / 1000000).toFixed(0)
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>

    <script>
        var options = {
            series: [{
                data: [{
                        x: '2008',
                        y: [2800, 4500]
                    },
                    {
                        x: '2009',
                        y: [3200, 4100]
                    },
                    {
                        x: '2010',
                        y: [2950, 7800]
                    },
                    {
                        x: '2011',
                        y: [3000, 4600]
                    },
                    {
                        x: '2012',
                        y: [3500, 4100]
                    },
                    {
                        x: '2013',
                        y: [4500, 6500]
                    },
                    {
                        x: '2014',
                        y: [4100, 5600]
                    }
                ]
            }],
            colors: ['#7334D9', '#7334D9'],
            chart: {
                height: 350,
                type: 'rangeBar',
                zoom: {
                    enabled: false
                }
            },
            plotOptions: {
                bar: {
                    isDumbbell: true,
                    columnWidth: 3,
                    dumbbellColors: [
                        ['#7334D9', '#7334D9']
                    ]
                }
            },
            legend: {
                show: true,
                showForSingleSeries: true,
                position: 'top',
                horizontalAlign: 'left',
                customLegendItems: ['Product A', 'Product B']
            },
            fill: {
                type: 'gradient',
                gradient: {
                    type: 'vertical',
                    gradientToColors: ['#f1ecfa'],
                    inverseColors: true,
                    stops: [0, 100]
                }
            },
            grid: {
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                }
            },
            xaxis: {
                tickPlacement: 'on'
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>