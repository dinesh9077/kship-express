    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="css/custom.css">  


    <div class="content-wrapper">

        <style>
            #chartdiv {
                width: 100%;
                height: 500px;
            }

            .switch {
                /*position: absolute;*/
                /*right: 0%;*/
                /*top: 5%;*/
                transform: translate(-70%, 25%);
                width: auto;
                height: 34px;
            }

            .dayNight input {
                display: none;
            }

            .dayNight input+div {
                border-radius: 50%;
                width: 25px;
                height: 25px;
                position: relative;
                box-shadow: inset 32px -32px 0 0 #000;
                -webkit-transform: scale(.5) rotate(0deg);
                transform: scale(.5) rotate(0deg);
                transition: transform .3s ease .1s, box-shadow .2s ease 0s;
            }

            .dayNight input:checked+div {
                box-shadow: inset 5px -6px 0 0 #000;
                -webkit-transform: scale(1) rotate(-2deg);
                transform: scale(1) rotate(-2deg);
                transition: box-shadow .5s ease 0s, transform .4s ease .1s;
            }

            .dayNight input+div::before {
                content: '';
                width: inherit;
                height: inherit;
                border-radius: inherit;
                position: absolute;
                left: 0;
                top: 0;
                border: 6px solid #000;
                transition: background .3s ease .1s;
            }

            .dayNight input:checked+div::before {
                border: 3px solid transparent;
                transition: background .3s ease;
            }

            .dayNight input+div::after {
                content: '';
                width: 4px;
                height: 4px;
                border-radius: 50%;
                margin: -1px 0 0 -3px;
                position: absolute;
                top: 50%;
                left: 50%;
                box-shadow: 0 -23px 0 #000, 0 23px 0 #000, 23px 0 0 #000, -23px 0 0 #000, 15px 15px 0 #000, -15px 15px 0 #000, 15px -15px 0 #000, -15px -15px 0 #000;
                -webkit-transform: scale(1.5);
                transform: scale(1.5);
                transition: transform .5s ease .15s;
            }

            .dayNight input:checked+div::after {
                -webkit-transform: scale(0);
                transform: scale(0);
                transition: all .3s ease;
            }

            .box {
                cursor: all-scroll;
            }

            .indicator img {
                height: 12px;
                margin-right: 4px;
            }

            h2.data-text {
                position: relative;
                font-size: 40px;
                color: #f1f4f8 !important;
                -webkit-text-stroke: 0.5px #383d52;
                text-transform: capitalize;
                display: inline-block;
                padding: 0px 10px;
                margin-bottom: 35px;
            }

            h2.data-text::before {
                content: attr(data-text);
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                color: #824cda;
                -webkit-text-stroke: 0vw #824cda;
                border-right: 2px solid #824cda;
                overflow: hidden;
                padding: 0px 10px;
                animation: animate 6s linear infinite;

            }

            @keyframes animate {

                0%,
                10%,
                100% {
                    width: 0;
                }

                70%,
                90% {
                    width: 100%;
                }
            }

            @media only screen and (max-width: 1199px) {
                .col-lg-2 {
                    -ms-flex: 0 0 16.666667% !important;
                    flex: 0 0 24.666667% !important;
                    max-width: 24.666667% !important;
                }

                .icon1 {
                    height: 35px;
                }

                .heading {
                    font-size: 18px;
                }

                .box-icon h4 {
                    font-size: 16px;
                    margin-bottom: 0px;
                }

                .box1 {
                    width: 80%;
                }

                .income-width {
                    flex: 0 0 40.333333% !important;
                    max-width: 40.333333% !important;
                }
            }

            @media only screen and (max-width: 1024px) {
                .box-icon-width {
                    -ms-flex: 0 0 16.666667% !important;
                    flex: 0 0 30.666667% !important;
                    max-width: 30.666667% !important;
                }

                .chart-text {
                    display: block !important;
                }

                .chart-text button {
                    margin-top: 10px;
                }

                .f-no {
                    display: block !important;
                }

                .dash-width1 {
                    -ms-flex: 0 0 25%;
                    flex: 0 0 35%;
                    max-width: 35%;
                }

                .dash-width {
                    -ms-flex: 0 0 25%;
                    flex: 0 0 65%;
                    max-width: 65%;
                }

                .main-width {
                    -ms-flex: 0 0 50%;
                    flex: 0 0 50%;
                    max-width: 50%;
                }

            }

            @media only screen and (max-width: 991px) {
                .icon1 {
                    height: 50px;
                }

                .f-no {
                    display: flex !important;
                }

                .dash-width1 {
                    -ms-flex: 0 0 25%;
                    flex: 0 0 100%;
                    max-width: 100%;
                }

                .dash-width {
                    -ms-flex: 0 0 25%;
                    flex: 0 0 100%;
                    max-width: 100%;
                }

                .main-width {
                    -ms-flex: 0 0 100%;
                    flex: 0 0 100%;
                    max-width: 100%;
                }
            }

            @media only screen and (max-width: 600px) {
                .col-lg-2 {
                    -ms-flex: 0 0 16.666667% !important;
                    flex: 0 0 50% !important;
                    max-width: 50% !important;
                }
            }
        </style>


        <section class="content">
            <?php if (user()->email_verified == 0 && empty(user()->email_verified)) { ?>
                <div class="verify_mail">
                    <div class="box-body">
                        <h3>
                            Verify Email to your account
                        </h3>
                        <p>
                            An email with a verification code has been sent to your <strong><?php echo user()->email; ?></strong>. If you would like a new code, or you haven't received the email, <a href="<?php echo base_url('auth/dashresend_mail') ?>">click here</a> to send a new one.
                        </p>
                    </div>
                </div>
            <?php } ?>

            <h2 class="data-text" data-text="Dashboard">Dashboard</h2>

            <h1 style="margin-bottom: 30px; display: none;">Dashboard
                <label class="switch dayNight pull-right" style="opacity: 1;">
                    <input type="checkbox" id="dark_mode" <?php echo ($this->business->mode == 1) ? "checked" : ""; ?>>
                    <div></div>
                </label>
            </h1>




            <div class="row">
                <div class="col-lg-3 col-md-12 sortables dash-width1">
                    <div class="box ui-sortable-handle">
                        <div class="box-body">
                            <div class="heading mb-5">Banking</div>
                            <div class="bank-box account-col">
                                <div class="bank-img">
                                    <img class="icon1" src="<?php echo base_url('assets/admin/ic-1.png') ?>">
                                </div>
                                <div class="text">
                                    <p>Cash In Hand</p>
                                    <h3>₹986,665.20</h3>
                                </div>
                            </div>
                            <div class="bank-box account-col">
                                <div class="bank-img">
                                    <img class="icon1" src="<?php echo base_url('assets/admin/ic-2.png') ?>">
                                </div>
                                <div class="text">
                                    <p>Bank Balance</p>
                                    <h3>₹986,665.20</h3>
                                </div>
                            </div>
                            <div class="bank-box account-col">
                                <div class="bank-img">
                                    <img class="icon1" src="<?php echo base_url('assets/admin/ic-3.png') ?>">
                                </div>
                                <div class="text">
                                    <p>Outstanding Balance</p>
                                    <h3>₹986,665.20</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>




                <div class="col-lg-9 col-md-12 sortables dash-width">
                    <div class="bts">
                        <div class="row">
                            <div class="col-md-4 main-width">
                                <div class="box ui-sortable-handle grdiant bg-img">
                                    <div class="balance-box">
                                        <div class="bg-white-box">
                                            <img src="<?php echo base_url('assets/admin/gif/Dollar.gif') ?>">
                                        </div>
                                        <p>Overdue Invoice</p>
                                        <h3>$1,390.00</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 main-width">
                                <div class="box ui-sortable-handlegrdiant">
                                    <div class="balance-box">
                                        <div class="img-grey">
                                            <img src="<?php echo base_url('assets/admin/gif/Invocie-main.gif') ?>">
                                        </div>
                                        <p>Pending Invoice</p>
                                        <h3>$1,390.00</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 main-width">
                                <div class="box ui-sortable-handlegrdiant">
                                    <div class="balance-box">
                                        <div class="img-grey">
                                            <img src="<?php echo base_url('assets/admin/gif/credit-card.gif') ?>">
                                        </div>
                                        <p>Upcoming Recurring Payment</p>
                                        <h3>$1,390.00</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-2 box-icon-width">
                            <div class="card-box">
                                <div class="box-icon">
                                    <img src="<?php echo base_url('assets/admin/Gif/deposit.gif') ?>">
                                    <h4>Deposit</h4>
                                    <p>Money</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 box-icon-width">
                            <div class="card-box">
                                <div class="box-icon">
                                    <img src="<?php echo base_url('assets/admin/Gif/Withdrawal.gif') ?>">
                                    <h4>withdraw</h4>
                                    <p>Money</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 box-icon-width">
                            <div class="card-box">
                                <div class="box-icon">
                                    <img src="<?php echo base_url('assets/admin/Gif/Recived.gif') ?>">
                                    <h4>Receive</h4>
                                    <p>Money</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 box-icon-width">
                            <div class="card-box">
                                <div class="box-icon">
                                    <img src="<?php echo base_url('assets/admin/Gif/Sent.gif') ?>">
                                    <h4>Send</h4>
                                    <p>Money</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 box-icon-width">
                            <div class="card-box">
                                <div class="box-icon">
                                    <img src="<?php echo base_url('assets/admin/Gif/Invooice.gif') ?>">
                                    <h4>invoice</h4>
                                    <p>Money</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 box-icon-width">
                            <div class="card-box">
                                <div class="box-icon">
                                    <img src="<?php echo base_url('assets/admin/Gif/Payment.gif') ?>">
                                    <h4>Payment</h4>
                                    <p>Money</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- end row -->
                    <div class="col-lg-6" style="visibility: hidden;">
                        <div class="card-box1 scope">

                            <div id="website-stats" style="height: 1px;" class="flot-chart mt-5"></div>
                        </div>
                    </div>


                    <div class="row d-none">
                        <div class="col-lg-6 col-md-12">
                            <div class="heading mb-2">Upcomming Recurring Payments</div>
                            <div class="box">
                                <div class="box-body">
                                    <?php if (empty($upcoming_payments)) : ?>
                                        <p><?php echo trans('no-data-founds') ?></p>
                                    <?php else : ?>
                                        <div class="table-responsive">
                                            <table id="datatable" class="table table-bordered  dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo trans('customer') ?></th>
                                                        <th><?php echo trans('total') ?></th>
                                                        <th><?php echo trans('amount-due') ?></th>
                                                        <th><?php echo trans('next-payment') ?></th>
                                                        <th><?php echo trans('status') ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($upcoming_payments as $payment) : ?>
                                                        <tr>
                                                            <td><?php echo html_escape($payment->customer_name) ?></td>
                                                            <td><?php echo decimal_format(html_escape($this->business->currency_symbol . '' . $payment->grand_total), 2) ?></td>
                                                            <td><?php echo decimal_format(html_escape($this->business->currency_symbol . '' . ($payment->grand_total - get_total_invoice_payments($payment->id, $payment->parent_id))), 2) ?></td>
                                                            <td><?php echo my_date_show($payment->next_payment) ?></td>
                                                            <td>
                                                                <span class="custom-label-lg label-light-info"><?php echo trans('upcomming') ?></span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif ?>
                                </div>
                                <?php if (!empty($upcoming_payments) && check_permissions(auth('role'), 'invoices') == TRUE) : ?>
                                    <div class="text-center bt-1 border-light p-10">
                                        <a class="d-block font-size-14" href="<?php echo base_url('admin/invoice/type/3?recurring=1') ?>"><?php echo trans('all-invoices') ?> <i class="fa fa-long-arrow-right"></i></a>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="heading mb-2">net-income</div>
                            <div class="box">
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-bordered  dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th><?php echo trans('fiscal-year') ?> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="Fiscal year start is January 01"></i></th>
                                                    <?php foreach ($net_income as $netincome) : ?>
                                                        <th class="text-right"><?php echo show_year($netincome->payment_date) ?></th>
                                                    <?php endforeach ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><?php echo trans('income') ?></td>
                                                    <?php foreach ($net_income as $netincome) : ?>
                                                        <td class="view_link text-right"><?php echo html_escape($this->business->currency_symbol) ?><?php echo decimal_format($netincome->total + $total_incomes, 2) ?></td>
                                                    <?php endforeach ?>
                                                </tr>
                                                <tr>
                                                    <td><?php echo trans('expense') ?></td>
                                                    <?php foreach ($net_income as $netincome) : ?>
                                                        <td class="view_link text-right"><?php echo html_escape($this->business->currency_symbol) ?><?php echo decimal_format(get_expense_by_year(show_year($netincome->payment_date)), 2) ?>
                                                        </td>
                                                    <?php endforeach ?>
                                                </tr>
                                                <tr>
                                                    <td><?php echo trans('net-income') ?> </td>
                                                    <?php foreach ($net_income as $netincome) : ?>
                                                        <td class="view_link text-right"><strong><?php echo html_escape($this->business->currency_symbol) ?><?php echo decimal_format($netincome->total + $total_incomes - get_expense_by_year(show_year($netincome->payment_date)), 2) ?></strong>
                                                        </td>
                                                    <?php endforeach ?>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>




        </section>



        <div class="row">
            <div class="col-lg-4 col-md-12">


                <div class="box ui-sortable-handle">

                    <div class="box-body">
                        <div class="heading mb-2">Income & Expense</div>
                        <div id="donut-chart">
                            <div class="chart-text d-flex justify-content-between align-items-center mb-5">
                                <div>
                                    <h4>Order Time</h4>
                                    <p>From 1-6 Dec, 2020</p>
                                </div>
                                <div>
                                    <button>View Report</button>
                                </div>
                            </div>
                            <div id="donut-chart-container" class="flot-charts box1" style="height: 200px;margin: auto;"></div>
                            <div class="indicator mt-4">
                                <!-- <h4>indicator</h4> -->
                                <div class="row d-flex justify-content-center">
                                    <div class="col-lg-4 col-md-12 income-width">
                                        <p><img src="<?php echo base_url('assets/admin/round-1.png') ?>"> Income <br> <span>40%</span></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 income-width">
                                        <p><img src="<?php echo base_url('assets/admin/round-1.png') ?>"> Expense <br> <span>60%</span></p>
                                    </div>
                                </div>
                                <!-- <button>Deposit</button> -->
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card-box">
                    <div class="heading mb-2">Upcomming Recurring Payments</div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered  dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Amount Due</th>
                                    <th>Next Payment</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card-box">
                    <div class="heading mb-2">Net Income</div>
                    <div class="card-box bg-color1">
                        <div class="fine-col">
                            <div class="d-flex justify-content-between f-no">
                                <div class="f-left">
                                    <p>• 2023 Apr</p>
                                </div>
                                <div class="f-right">
                                    <p>₹173,440.56</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-box bg-color1">
                        <div class="fine-col">
                            <div class="d-flex justify-content-between f-no">
                                <div class="f-left">
                                    <p>• 2023 Apr</p>
                                </div>
                                <div class="f-right">
                                    <p>₹173,440.56</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-box bg-color1">
                        <div class="fine-col">
                            <div class="d-flex justify-content-between f-no">
                                <div class="f-left">
                                    <p>• 2023 Apr</p>
                                </div>
                                <div class="f-right">
                                    <p>₹173,440.56</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-box bg-color1">
                        <div class="fine-col">
                            <div class="d-flex justify-content-between f-no">
                                <div class="f-left">
                                    <p>• 2023 Apr</p>
                                </div>
                                <div class="f-right">
                                    <p>₹173,440.56</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-box bg-color1">
                        <div class="fine-col">
                            <div class="d-flex justify-content-between f-no">
                                <div class="f-left">
                                    <p>• 2023 Apr</p>
                                </div>
                                <div class="f-right">
                                    <p>₹173,440.56</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="card-box bg-color1">
    <div class="fine-col">
        <div class="d-flex justify-content-between">
          <div class="f-left"><p>• 2023 Apr</p></div>
          <div class="f-right"><p>₹173,440.56</p></div>
      </div>
  </div>
</div> -->
                </div>
            </div>
        </div>


        <div class="row">


            <div class="col-lg-12">
                <div class="card-box">
                    <div class="heading mb-2">Profit and Loss</div>
                    <div id="flotRealTime" style="height: 350px;" class="flot-chart mt-5"></div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="card-box">
                    <div class="heading mb-2">Overdue Interview</div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered  dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th> Customer</th>
                                    <th>Status</th>
                                    <th>Amount Due</th>
                                    <th>Next Payment</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card-box">
                    <div class="heading mb-2">Pending invoice</div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered  dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th> Customer</th>
                                    <th>Status</th>
                                    <th>Amount Due</th>
                                    <th>Next Payment</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                                <tr>
                                    <td>Suhash Patel</td>
                                    <td><button class="sm-btn">Upcomming</button></td>
                                    <td>15 Jun 2023</td>
                                    <td>20 Jun 2023</td>
                                    <td>$2934.5</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- Resources -->
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script type="text/javascript">
        var options = {
            series: [44, 55, 13, 33],
            chart: {
                width: 380,
                type: 'donut',
            },
            dataLabels: {
                enabled: false
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        show: false
                    }
                }
            }],
            legend: {
                position: 'right',
                offsetY: 0,
                height: 230,
            }
        };

        // Check if the element with ID "chart" exists on the page
        if (document.querySelector("#chart")) {
            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        }


        function appendData() {
            var arr = chart.w.globals.series.slice()
            arr.push(Math.floor(Math.random() * (100 - 1 + 1)) + 1)
            return arr;
        }

        function removeData() {
            var arr = chart.w.globals.series.slice()
            arr.pop()
            return arr;
        }

        function randomize() {
            return chart.w.globals.series.map(function() {
                return Math.floor(Math.random() * (100 - 1 + 1)) + 1
            })
        }

        function reset() {
            return options.series
        }

        const randomizeButton = document.querySelector("#randomize");
        if (randomizeButton) {
            randomizeButton.addEventListener("click", function() {
                chart.updateSeries(randomize());
            });
        }

        const addButton = document.querySelector("#add");
        if (addButton) {
            addButton.addEventListener("click", function() {
                chart.updateSeries(appendData())
            });
        }

        const removeButton = document.querySelector("#remove");
        if (removeButton) {
            removeButton.addEventListener("click", function() {
                chart.updateSeries(removeData())
            });
        }

        const resetButton = document.querySelector("#reset");
        if (resetButton) {
            resetButton.addEventListener("click", function() {
                chart.updateSeries(reset())
            });
        }
    </script>
    <script type="text/javascript">
        var options = {
            chart: {
                type: "area",
                height: 370,
                foreColor: "#999",
                stacked: true,
                dropShadow: {
                    enabled: true,
                    enabledSeries: [0],
                    top: -2,
                    left: 2,
                    blur: 5,
                    opacity: 0.06
                }
            },
            colors: ['#6933C2', '#A773FF'],
            stroke: {
                curve: "smooth",
                width: 3
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: 'Total Views',
                data: generateDayWiseTimeSeries(0, 18)
            }, {
                name: 'Unique Views',
                data: generateDayWiseTimeSeries(1, 18)
            }],
            markers: {
                size: 0,
                strokeColor: "#fff",
                strokeWidth: 3,
                strokeOpacity: 1,
                fillOpacity: 1,
                hover: {
                    size: 6
                }
            },
            xaxis: {
                type: "datetime",
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    offsetX: 14,
                    offsetY: -5
                },
                tooltip: {
                    enabled: true
                }
            },
            grid: {
                padding: {
                    left: -5,
                    right: 5
                }
            },
            tooltip: {
                x: {
                    format: "dd MMM yyyy"
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left'
            },
            fill: {
                type: "solid",
                fillOpacity: 0.7
            }
        };

        // Check if the element with ID "chart" exists on the page
        if (document.querySelector("#timeline-chart")) {
            var chart = new ApexCharts(document.querySelector("#timeline-chart"), options);
            chart.render();
        }

        function generateDayWiseTimeSeries(s, count) {
            var values = [
                [
                    4, 3, 10, 9, 29, 19, 25, 9, 12, 7, 19, 5, 13, 9, 17, 2, 7, 5
                ],
                [
                    2, 3, 8, 7, 22, 16, 23, 7, 11, 5, 12, 5, 10, 4, 15, 2, 6, 2
                ]
            ];
            var i = 0;
            var series = [];
            var x = new Date("11 Nov 2012").getTime();
            while (i < count) {
                series.push([x, values[s][i]]);
                x += 86400000;
                i++;
            }
            return series;
        }
    </script>

    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <!-- Vendor js -->
    <script src="<?php echo base_url() ?>assets/admin/js1/vendor.min.js"></script>

    <script src="<?php echo base_url() ?>assets/admin/libs1/flot-charts/jquery.flot.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/libs1/flot-charts/jquery.flot.time.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/libs1/flot-charts/jquery.flot.tooltip.min.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/libs1/flot-charts/jquery.flot.resize.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/libs1/flot-charts/jquery.flot.pie.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/libs1/flot-charts/jquery.flot.selection.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/libs1/flot-charts/jquery.flot.stack.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/libs1/flot-charts/jquery.flot.orderBars.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/libs1/flot-charts/jquery.flot.crosshair.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/libs1/flot-charts/curvedLines.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/libs1/flot-charts/jquery.flot.axislabels.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/js1/pages/flot.init.js"></script>
    <!-- App js -->
    <script src="<?php echo base_url() ?>assets/admin/js1/app.min.js"></script>

    <!-- Chart code -->
    <script>
        am5.ready(function() {

            // Create root element
            // https://www.amcharts.com/docs/v5/getting-started/#Root_element
            // Check if the element with id "chartdiv" exists
            if (document.getElementById("chartdiv")) {
                // If the element is found, execute the following code
                var root = am5.Root.new("chartdiv");
                // (You can add more code here if needed)
                // Set themes
                // https://www.amcharts.com/docs/v5/concepts/themes/
                root.setThemes([
                    am5themes_Animated.new(root)
                ]);

                // Create chart
                // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/
                var chart = root.container.children.push(
                    am5percent.PieChart.new(root, {
                        endAngle: 270
                    })
                );

                // Create series
                // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Series
                var series = chart.series.push(
                    am5percent.PieSeries.new(root, {
                        valueField: "value",
                        categoryField: "category",
                        endAngle: 270
                    })
                );

                series.states.create("hidden", {
                    endAngle: -90
                });

                // Set data
                // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Setting_data
                series.data.setAll([{
                    category: "Lithuania",
                    value: 501.9
                }, {
                    category: "Czechia",
                    value: 301.9
                }, {
                    category: "Ireland",
                    value: 201.1
                }, {
                    category: "Germany",
                    value: 165.8
                }, {
                    category: "Australia",
                    value: 139.9
                }, {
                    category: "Austria",
                    value: 128.3
                }, {
                    category: "UK",
                    value: 99
                }]);

                series.appear(1000, 100);

            }
        }); // end am5.ready()
    </script>