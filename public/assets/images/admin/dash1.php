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
                    -ms-flex: 0 0 13% !important;
                    flex: 0 0 33% !important;
                    max-width: 33% !important;
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
                    flex: 0 0 50%;
                    max-width: 50%;
                }
            }

            @media only screen and (max-width: 600px) {
                .col-lg-2 {
                    -ms-flex: 0 0 16.666667% !important;
                    flex: 0 0 50% !important;
                    max-width: 50% !important;
                }

                .main-width {
                    -ms-flex: 0 0 100%;
                    flex: 0 0 100%;
                    max-width: 100%;
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
                                    <h3><?php echo $this->business->currency_symbol . ' ' . $cash_amount; ?></h3>
                                </div>
                            </div>
                            <div class="bank-box account-col">
                                <div class="bank-img">
                                    <img class="icon1" src="<?php echo base_url('assets/admin/ic-2.png') ?>">
                                </div>
                                <div class="text">
                                    <p>Bank Balance</p>
                                    <h3><?php echo $this->business->currency_symbol . ' ' . $bank_amount; ?></h3>
                                </div>
                            </div>
                            <div class="bank-box account-col">
                                <div class="bank-img">
                                    <img class="icon1" src="<?php echo base_url('assets/admin/ic-3.png') ?>">
                                </div>
                                <div class="text">
                                    <p>Card/Outstanding Balance</p>
                                    <h3><?php echo $this->business->currency_symbol . ' ' . $cards_limit; ?> / <?php echo $this->business->currency_symbol . ' ' . $outstand; ?></h3>
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
                                        <h3><?php echo $this->business->currency_symbol . ' ' . $total_overdues; ?></h3>
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
                                        <h3><?php echo $this->business->currency_symbol . ' ' . $total_pending; ?></h3>
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
                                        <h3><?php echo $this->business->currency_symbol . ' ' . $new_total_upcoming_payments; ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-2 box-icon-width">
                            <div class="card-box">
                                <div class="box-icon">
                                    <img src="<?php echo base_url('assets/admin/gif/deposit.gif') ?>">
                                    <h4>Deposit</h4>
                                    <p>Money</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 box-icon-width">
                            <div class="card-box">
                                <div class="box-icon">
                                    <img src="<?php echo base_url('assets/admin/gif/Withdrawal.gif') ?>">
                                    <h4>withdraw</h4>
                                    <p>Money</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 box-icon-width">
                            <div class="card-box">
                                <div class="box-icon">
                                    <img src="<?php echo base_url('assets/admin/gif/Recived.gif') ?>">
                                    <h4>Receive</h4>
                                    <p>Money</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 box-icon-width">
                            <div class="card-box">
                                <div class="box-icon">
                                    <img src="<?php echo base_url('assets/admin/gif/Sent.gif') ?>">
                                    <h4>Send</h4>
                                    <p>Money</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 box-icon-width">
                            <div class="card-box">
                                <div class="box-icon">
                                    <img src="<?php echo base_url('assets/admin/gif/Invooice.gif') ?>">
                                    <h4>invoice</h4>
                                    <p>Money</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 box-icon-width">
                            <div class="card-box">
                                <div class="box-icon">
                                    <img src="<?php echo base_url('assets/admin/gif/Payment.gif') ?>">
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
                </div>
        </section>
        <div class="row mt-1">
            <div class="col-lg-3 col-md-12">
                <div class="box ui-sortable-handle h100">
                    <div class="box-body">
                        <div class="heading mb-2">Income & Expense</div>
                        <div id="donut-chart">
                            <!-- <div class="chart-text d-flex justify-content-between align-items-center mb-5">
                                <div>
                                    <h4>Order Time</h4>
                                    <p>From 16 Dec, 2020</p>
                                </div>
                                <div>
                                    <button>View Report</button>
                                </div>
                            </div> -->
                            <div id="donut-chart-container" class="flot-charts box1_" style="height: 200px;margin: auto;"></div>
                            <div class="indicator mt-4">
                                <!-- <h4>indicator</h4> -->
                                <div class="row d-flex justify-content-center" style="align-items: center;">
                                    <div class="col-lg-6 col-md-12">
                                        <div class="text-right">
                                            Income <span class="font-weight-700 mr-2"><?php echo $total_incomes_per; ?> %</span>
                                            <span style="background-color: #9E6AF6; height: 5px; width: 5px;"> </span>
                                            <!-- <img src="<?php echo base_url('assets/admin/round-1.png') ?>">  -->
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12">
                                        <div class="text-left">
                                            <!-- <img src="<?php echo base_url('assets/admin/round-1.png') ?>">  -->
                                            <span style="background-color: #6448cb; height: 5px; width: 5px;"> </span>
                                            <span class="ml-1 font-weight-700"><?php echo $total_expense_per; ?> %</span> Expense
                                        </div>
                                    </div>
                                </div>
                                <!-- <button>Deposit</button> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card-box h100">
                    <div class="heading mb-2">Net Income</div>
                    <!-- <div class="card-box bg-color1">
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
                    </div> -->

                    <?php
                    if (empty($new_net_income)) { ?>
                        <p><?php echo trans('no-data-founds') ?></p>
                        <?php
                    } else {
                        foreach ($new_net_income as $year => $year_income) {
                            if ($year_income != null) {
                        ?>
                                <div class="card-box bg-color1">
                                    <div class="fine-col">
                                        <div class="d-flex justify-content-between f-no">
                                            <div class="f-left">
                                                <p>• <?php echo $year ?></p>
                                            </div>
                                            <div class="f-right">
                                                <p><?php echo $this->business->currency_symbol . ' ' . $year_income; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    <?php
                            }
                        }
                    } ?>
                </div>
            </div>
            <div class="col-lg-5 col-md-12">
                <div class="card-box h100">
                    <div class="heading mb-2">Upcomming Recurring Payments</div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered  dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>

                                    <th><?php echo trans('customer') ?></th>
                                    <th><?php echo trans('date') ?></th>
                                    <th><?php echo trans('total') ?></th>
                                    <th><?php echo trans('amount-due') ?></th>
                                    <th><?php echo trans('status') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($upcoming_payments)) { ?>
                                    <p><?php echo trans('no-data-founds') ?></p>
                                    <?php
                                } else {
                                    foreach ($upcoming_payments as $upc_pay) {
                                        $currency_symbol = $upc_pay->c_currency_symbol;
                                        $tag_html = $tag_text = $tag_class = $rc_tag_html = '';
                                        if ($upc_pay->status == 0) {
                                            $tag_text = trans('draft');
                                            $tag_class = 'default';
                                        } else if ($upc_pay->status == 2) {
                                            if ($upc_pay->type == 4) {
                                                $tag_text = trans('credit-note');
                                                $tag_class = 'warning';
                                            } else {
                                                $tag_text = trans('paid');
                                                $tag_class = 'success';
                                            }
                                        } else if ($upc_pay->status == 1) {
                                            if (check_paid_status($upc_pay->id) == 1) {
                                                $tag_text = trans('partial');
                                                $tag_class = 'success';
                                            } else {
                                                $tag_text = trans('unpaid');
                                                $tag_class = 'danger';
                                            }
                                        }
                                        if ($upc_pay->recurring == 1) {
                                            $rc_tag_text = $rc_tag_class = $rc_tooltip = '';
                                            if ($upc_pay->is_completed == 0) {
                                                $rc_tag_text = "R-U";
                                                $rc_tag_class = 'danger';
                                                $rc_tooltip = trans('complete-tooltip');
                                            } else {
                                                $rc_tag_text = "R-C";
                                                $rc_tag_class = 'success';
                                            }
                                            $rc_tag_html = "<span data-toggle='tooltip' title='$rc_tooltip' class='ml-1 custom-label-sm label-light-$rc_tag_class'>$rc_tag_text</span>";
                                        }
                                        $tag_html = "<span class='custom-label-sm label-light-$tag_class'>$tag_text</span>$rc_tag_html";
                                        $new_base_total_upcoming_payments = 0.00;
                                        if (isset($upc_pay->convert_total) && $upc_pay->convert_total != 0) {
                                            $new_base_total_upcoming_payments += $upc_pay->convert_total;
                                        } else {
                                            if ($upc_pay->c_currency_symbol == $this->business->currency_symbol) {
                                                $new_base_total_upcoming_payments += $upc_pay->grand_total;
                                            } else {
                                                $new_base_total_upcoming_payments += $upc_pay->grand_total * $upc_pay->c_rate;
                                            }
                                        }
                                        $new_base_total_upcoming_payments = 0.00;
                                        if (isset($upc_pay->convert_total) && $upc_pay->convert_total != 0) {
                                            $new_base_total_upcoming_payments += $upc_pay->convert_total;
                                        } else {
                                            if ($upc_pay->c_currency_symbol == $this->business->currency_symbol) {
                                                $new_base_total_upcoming_payments += $upc_pay->grand_total;
                                            } else {
                                                $new_base_total_upcoming_payments += $upc_pay->grand_total * $upc_pay->c_rate;
                                            }
                                        }
                                    ?>
                                        <tr>
                                            <td class="text-capitalize"><?php echo html_escape($upc_pay->customer_name) ?></td>
                                            <td><?php echo date("d M, Y", strtotime($upc_pay->date)); ?></td>
                                            <td>
                                                <span class="total-price">
                                                    <?php if (!empty($currency_symbol)) {
                                                        echo html_escape($currency_symbol);
                                                    }
                                                    echo decimal_format(html_escape($upc_pay->grand_total), 2) ?>
                                                </span>
                                                <br>
                                                <?php
                                                if ($this->business->currency_symbol != $currency_symbol) { ?>
                                                    <span class="conver-total">
                                                        <?php echo $this->business->currency_symbol . '' . $new_base_total_upcoming_payments . ' ' . $this->business->currency_code ?>
                                                    </span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span class="total-price">
                                                    <?php
                                                    if (!empty($currency_symbol)) {
                                                        echo html_escape($currency_symbol);
                                                    }
                                                    if ($upc_pay->c_rate != 0) {
                                                        echo decimal_format(html_escape($due_amt = $upc_pay->grand_total - get_total_invoice_payments($upc_pay->id, '') / $upc_pay->c_rate), 2);
                                                    } else {
                                                        echo decimal_format(html_escape($due_amt = $upc_pay->grand_total - get_total_invoice_payments($upc_pay->id, '')), 2);
                                                    } ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo $tag_html; ?>
                                            </td>
                                        </tr>
                                <?php
                                    };
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card-box">
                    <div class="heading mb-2">Profit and Loss</div>
                    <div id="line-adwords" class=""></div>
                    <div id="areachart" class=""></div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-6 col-md-12">
                <div class="card-box h100">
                    <div class="heading mb-2">Overdue Invoices</div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered  dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Due Payment</th>
                                    <th>Total Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($overdues)) { ?>
                                    <p><?php echo trans('no-data-founds') ?></p>
                                    <?php
                                } else {
                                    foreach ($overdues as $due) {
                                        $currency_symbol = $due->c_currency_symbol;
                                        $tag_html = $tag_text = $tag_class = $rc_tag_html = '';
                                        if ($due->status == 0) {
                                            $tag_text = trans('draft');
                                            $tag_class = 'default';
                                        } else if ($due->status == 2) {
                                            if ($due->type == 4) {
                                                $tag_text = trans('credit-note');
                                                $tag_class = 'warning';
                                            } else {
                                                $tag_text = trans('paid');
                                                $tag_class = 'success';
                                            }
                                        } else if ($due->status == 1) {
                                            if (check_paid_status($due->id) == 1) {
                                                $tag_text = trans('partial');
                                                $tag_class = 'success';
                                            } else {
                                                $tag_text = trans('unpaid');
                                                $tag_class = 'danger';
                                            }
                                        }
                                        if ($due->recurring == 1) {
                                            $rc_tag_text = $rc_tag_class = $rc_tooltip = '';
                                            if ($due->is_completed == 0) {
                                                $rc_tag_text = "R-U";
                                                $rc_tag_class = 'danger';
                                                $rc_tooltip = trans('complete-tooltip');
                                            } else {
                                                $rc_tag_text = "R-C";
                                                $rc_tag_class = 'success';
                                            }
                                            $rc_tag_html = "<span data-toggle='tooltip' title='$rc_tooltip' class='ml-1 custom-label-sm label-light-$rc_tag_class'>$rc_tag_text</span>";
                                        }
                                        $tag_html = "<span class='custom-label-sm label-light-$tag_class'>$tag_text</span>$rc_tag_html"; ?>
                                        <tr>
                                            <td class="text-capitalize"><?php echo html_escape($due->customer_name) ?></td>
                                            <td>
                                                <?php echo $tag_html; ?>
                                            </td>
                                            <td><?php echo date("d M, Y", strtotime($due->payment_due)); ?></td>
                                            <td>
                                                <span class="total-price">
                                                    <?php
                                                    if (!empty($currency_symbol)) {
                                                        echo html_escape($currency_symbol);
                                                    }
                                                    if ($due->c_rate != 0) {
                                                        echo decimal_format(html_escape($due_amt = $due->grand_total - get_total_invoice_payments($due->id, $due->parent_id) / $due->c_rate), 2);
                                                    } else {
                                                        echo decimal_format(html_escape($due_amt = $due->grand_total - get_total_invoice_payments($due->id, $due->parent_id)), 2);
                                                    } ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="total-price">
                                                    <?php if (!empty($currency_symbol)) {
                                                        echo html_escape($currency_symbol);
                                                    }
                                                    echo decimal_format(html_escape($due->grand_total), 2) ?>
                                                </span>
                                                <br>
                                                <?php
                                                if ($this->business->currency_symbol == $currency_symbol) {
                                                    $toatl = $due->grand_total;
                                                } else {
                                                    $toatl = $due->convert_total; ?>
                                                    <span class="conver-total"><?php echo $this->business->currency_symbol . '' . $toatl . ' ' . $this->business->currency_code ?> </span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card-box h100">
                    <div class="heading mb-2">Pending invoice</div>
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
                                <?php
                                if (empty($pending)) { ?>
                                    <p><?php echo trans('no-data-founds') ?></p>
                                    <?php
                                } else {
                                    foreach ($pending as $pen_inv) {
                                        $currency_symbol = $pen_inv->c_currency_symbol;
                                        $tag_html = $tag_text = $tag_class = $rc_tag_html = '';
                                        if ($pen_inv->status == 0) {
                                            $tag_text = trans('draft');
                                            $tag_class = 'default';
                                        } else if ($pen_inv->status == 2) {
                                            if ($pen_inv->type == 4) {
                                                $tag_text = trans('credit-note');
                                                $tag_class = 'warning';
                                            } else {
                                                $tag_text = trans('paid');
                                                $tag_class = 'success';
                                            }
                                        } else if ($pen_inv->status == 1) {
                                            if (check_paid_status($pen_inv->id) == 1) {
                                                $tag_text = trans('partial');
                                                $tag_class = 'success';
                                            } else {
                                                $tag_text = trans('unpaid');
                                                $tag_class = 'danger';
                                            }
                                        }
                                        if ($pen_inv->recurring == 1) {
                                            $rc_tag_text = $rc_tag_class = $rc_tooltip = '';
                                            if ($pen_inv->is_completed == 0) {
                                                $rc_tag_text = "R-U";
                                                $rc_tag_class = 'danger';
                                                $rc_tooltip = trans('complete-tooltip');
                                            } else {
                                                $rc_tag_text = "R-C";
                                                $rc_tag_class = 'success';
                                            }
                                            $rc_tag_html = "<span data-toggle='tooltip' title='$rc_tooltip' class='ml-1 custom-label-sm label-light-$rc_tag_class'>$rc_tag_text</span>";
                                        }
                                        $tag_html = "<span class='custom-label-sm label-light-$tag_class'>$tag_text</span>$rc_tag_html"; ?>
                                        <tr>
                                            <td class="text-capitalize"><?php echo html_escape($pen_inv->customer_name) ?></td>
                                            <td>
                                                <?php echo $tag_html; ?>
                                            </td>
                                            <td><?php echo date("d M, Y", strtotime($pen_inv->payment_due)); ?></td>
                                            <td>
                                                <span class="total-price">
                                                    <?php
                                                    if (!empty($currency_symbol)) {
                                                        echo html_escape($currency_symbol);
                                                    }
                                                    if ($pen_inv->c_rate != 0) {
                                                        echo decimal_format(html_escape($due_amt = $pen_inv->grand_total - get_total_invoice_payments($pen_inv->id, $pen_inv->parent_id) / $pen_inv->c_rate), 2);
                                                    } else {
                                                        echo decimal_format(html_escape($due_amt = $pen_inv->grand_total - get_total_invoice_payments($pen_inv->id, $pen_inv->parent_id)), 2);
                                                    } ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="total-price">
                                                    <?php if (!empty($currency_symbol)) {
                                                        echo html_escape($currency_symbol);
                                                    }
                                                    echo decimal_format(html_escape($pen_inv->grand_total), 2) ?>
                                                </span>
                                                <br>
                                                <?php
                                                if ($this->business->currency_symbol == $currency_symbol) {
                                                    $toatl = $pen_inv->grand_total;
                                                } else {
                                                    $toatl = $pen_inv->convert_total; ?>
                                                    <span class="conver-total"><?php echo $this->business->currency_symbol . '' . $toatl . ' ' . $this->business->currency_code ?> </span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } ?>
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
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <!-- Vendor js -->
    <script src="<?php echo base_url() ?>assets/admin/js1/vendor.min.js"></script>

    <!-- App js -->
    <script src="<?php echo base_url() ?>assets/admin/js1/app.min.js"></script>

    <script>
        let incomeDatas = <?php print_r($income_data) ?>;
        let expenseDatas = <?php print_r($expense_data) ?>;
        let incomeAxises = <?php print_r($income_axis) ?>;
        let total_incomes = <?php print_r($total_incomes_per) ?>;
        let total_expenses = <?php print_r($total_expense_per) ?>;

        incomeDatas.reverse();
        expenseDatas.reverse();
        incomeAxises.reverse();


        let optionsLine = {
            chart: {
                height: 400,
                type: 'line',
                zoom: {
                    enabled: true
                },
                dropShadow: {
                    enabled: true,
                    top: 3,
                    left: 2,
                    blur: 4,
                    opacity: 0.3,
                },
                toolbar: {
                    show: true,
                }
            },
            responsive: [{
                    breakpoint: 768, // Define breakpoint for screens with width <= 768px
                    options: {
                        chart: {
                            height: 400
                        },
                    }
                },
                // Add more responsive configurations for other breakpoints if needed
            ],
            stroke: {
                curve: 'smooth',
                width: 2
            },
            colors: ["#9E6AF6", '#6448cb'],
            series: [{
                    name: "Profit",
                    data: incomeDatas,
                },
                {
                    name: "Loss",
                    data: expenseDatas,
                },
            ],
            // title: {
            //     text: 'Media',
            //     align: 'left',
            //     offsetY: 25,
            //     offsetX: 20
            // },
            // subtitle: {
            //     display : false,
            //     text: 'Statistics',
            //     offsetY: 55,
            //     offsetX: 20
            // },
            markers: {
                size: 6,
                strokeWidth: 0,
                hover: {
                    size: 9
                }
            },
            grid: {
                show: true,
                padding: {
                    bottom: 0
                }
            },
            labels: incomeAxises,
            xaxis: {
                tooltip: {
                    enabled: true
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                offsetY: -20
            }
        }

        let chartLine = new ApexCharts(document.querySelector('#line-adwords'), optionsLine);
        chartLine.render();

        var optionsCircle4 = {
            chart: {
                type: 'radialBar',
                height: 320,
                // width: 380,
                animations: {
                    enabled: true, // Enable animation
                    easing: 'easein', // Optional easing function for animation
                    speed: 3000, // Duration of the animation in milliseconds
                    animateGradually: {
                        enabled: true,
                        delay: 150,
                    },
                    dynamicAnimation: {
                        enabled: true,
                        delay: 150,
                    },
                },
            },
            plotOptions: {
                radialBar: {
                    size: 100,
                    inverseOrder: true,
                    hollow: {
                        margin: 5,
                        size: '48%',
                        background: 'transparent',

                    },
                    track: {
                        show: true,
                    },
                    startAngle: -180,
                    endAngle: 180

                },
            },
            tooltip: {
                y: {
                    formatter: function(value, {
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        return w.globals.seriesNames[seriesIndex] + ': ' + value;
                    },
                },
            },
            stroke: {
                lineCap: 'round'
            },
            colors: ["#6448cb", '#9E6AF6'],
            labels: ['Expense', 'Income'],
            series: [total_expenses, total_incomes],
            legend: {
                show: false,
                floating: true,
                position: 'right',
                offsetX: 70,
                offsetY: 240
            },
        }

        var chartCircle4 = new ApexCharts(document.querySelector('#donut-chart-container'), optionsCircle4);
        chartCircle4.render();
    </script>