<style>
    li.col.nav-item {
        padding: 0;
    }

    ul.nav.nav-pills.text-center.m-auto li.nav-item img {
        width: 10%;
    }

    ul.nav.nav-pills.text-center.m-auto li a {
        height: 100%;
    }

    .content-wrapper {
        margin: 0px;
    }
</style>
<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">


        <?php if ($type == 1) : ?>
            <?php
            $currency_symbol = helper_get_customer($invoice->customer)->currency_symbol;
            if (isset($currency_symbol)) {
                $currency_symbol = $currency_symbol;
            } else {
                $currency_symbol = $invoice->currency_symbol;
            }

            $currency_code = helper_get_customer($invoice->customer)->currency_code;
            if (isset($currency_code)) {
                $currency_code = $currency_code;
            } else {
                $currency_code = $this->business->currency_symbol;
            }

            ?>

            <div class="container mt-50">
                <div class="col-md-10 mt-50 m-auto">
                    <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/payment/online/2/' . md5($invoice->id)) ?>" role="form">
                        <div class="box">
                            <div class="box-header">
                                <h4 class="box-title" id="vcenter"><?php echo trans('pay-online') ?> - <?php if (!empty($currency_symbol)) {
                                                                                                            echo html_escape($currency_symbol);
                                                                                                        } ?><?php echo number_format($invoice->grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id), 2); ?></h4>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label><?php echo trans('amount') ?></label>
                                    <input type="text" class="form-control" name="amount" value="<?php echo number_format($invoice->grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id), 2); ?>" readonly>
                                    <input type="hidden" name="currency_code" value="<?php echo $currency_code; ?>">
                                </div>
                            </div>

                            <div class="box-footer">
                                <!-- csrf token -->
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <button type="submit" class="btn btn-info waves-effect pull-left"><?php echo trans('continue') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php else : ?>

            <?php
            $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
            $paypal_id = html_escape($user->paypal_email);
            $price = $final_amount;
            $amount = $amount;
            ?>


            <?php if ($invoice->status != 2) : ?>
                <div class="container mt-50 mb-20">
                    <div class="col-md-10 m-auto">
                        <div class="box text-center">
                            <h3 class="pt-15 text-center">Please select a payment method</h3>
                            <div class="box-body">
                                <ul class="nav nav-pills nav-justified text-center m-auto">
                                    <?php if ($user->paypal_payment == 1) : ?>
                                        <li class="col nav-item">
                                            <a class="nav-link active" data-toggle="pill" href="#paypal"><img src="<?php echo base_url("assets/admin/paypal.png") ?>" width="20%">&nbsp;Paypal</a>
                                        </li>
                                    <?php endif ?>
                                    <?php if ($user->stripe_payment == 1) : ?>
                                        <li class="col nav-item">
                                            <a class="nav-link" data-toggle="pill" href="#stripe"><img src="<?php echo base_url("assets/admin/stripe.png") ?>" width="20%">&nbsp;Stripe</a>
                                        </li>
                                    <?php endif ?>
                                    <?php if ($user->razorpay_payment == 1) : ?>
                                        <li class="col nav-item">
                                            <a class="nav-link" data-toggle="pill" href="#razorpay"><img src="<?php echo base_url("assets/admin/rzp-glyph-positive.png") ?>" width="20%">&nbsp;Razorpay</a>
                                        </li>
                                    <?php endif ?>
                                    <?php if ($user->paystack_payment == 1) : ?>
                                        <li class="col nav-item">
                                            <a class="nav-link" data-toggle="pill" href="#paystack"><img src="<?php echo base_url("assets/admin/paystack.png") ?>" width="20%">&nbsp;Paystack</a>
                                        </li>
                                    <?php endif ?>
                                    <?php if ($user->cashfree_payment == 1) : ?>
                                        <li class="col nav-item">
                                            <a class="nav-link" data-toggle="pill" href="#cashfree"><img src="<?php echo base_url("assets/admin/a146b015e8eb3e923b4d285d5c1dc972f7f513de.png") ?>" width="20%">&nbsp;Cashfree</a>
                                        </li>
                                    <?php endif ?>
                                </ul>


                                <!-- Tab panes -->
                                <div class="tab-content">

                                    <div class="tab-pane container">
                                        <div class="row">
                                            <div class="col-md-6 m-auto">
                                                <div class="box-body text-center">
                                                    <h5 class="pt-15"><?php echo trans('please-select-a-payment-method') ?></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- paypal payment -->
                                    <?php if ($user->paypal_payment == 1) : ?>
                                        <div class="tab-pane container active" id="paypal">
                                            <div class="row">
                                                <div class="col-md-6 m-auto">

                                                    <div class="box-body text-center">

                                                        <!-- PRICE ITEM -->
                                                        <form action="<?php echo html_escape($paypal_url); ?>" method="post" name="frmPayPal1">
                                                            <div class="pipanel price panel-red">
                                                                <input type="hidden" name="business" value="<?php echo html_escape($paypal_id); ?>" readonly>
                                                                <input type="hidden" name="cmd" value="_xclick">
                                                                <input type="hidden" name="item_name" value="Online Payment">
                                                                <input type="hidden" name="item_number" value="1">
                                                                <input type="hidden" name="amount" value="<?php echo $price ?>" readonly>
                                                                <input type="hidden" name="no_shipping" value="1">
                                                                <input type="hidden" name="currency_code" value="<?php echo html_escape($invoice->currency_code); ?>">
                                                                <input type="hidden" name="cancel_return" value="<?php echo base_url('admin/payment/payment_cancel/' . $invoice->id) ?>">
                                                                <input type="hidden" name="return" value="<?php echo base_url('admin/payment/payment_success/' . $invoice->id . '/' . $amount) ?>">

                                                                <div class="panel-footer">
                                                                    <button class="btn btn-info btn-lg payment_btn" href="#"><?php echo trans('pay-now') ?> <?php echo currency_to_symbol($invoice->currency_code); ?><?php echo html_escape($price) ?></button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <!-- /PRICE ITEM -->

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    <?php endif ?>

                                    <!-- stripe payment -->
                                    <?php if ($user->stripe_payment == 1) : ?>
                                        <div class="tab-pane container" id="stripe">
                                            <div class="row">
                                                <div class="col-md-6 m-auto">

                                                    <div class="credit-card-box" style="text-align: left;">
                                                        <div class="box-header">
                                                            <h3 class="box-title flex-parent-between">
                                                                <?php echo trans('payment') . ' ' . trans('details') ?>
                                                                <span><img class="img-responsive pull-right" width="40%" src="<?php echo base_url('assets/images/accept-cards.jpg') ?>"></span>
                                                            </h3>
                                                        </div>
                                                        <div class="box-body">

                                                            <form role="form" action="<?php echo base_url('admin/payment/stripe_payment/' . $invoice->id . '/' . $price . '/' . $amount) ?>" method="post" class="require-validation" data-cc-on-file="false" data-stripe-publishable-key="<?php echo $user->publish_key; ?>" id="payment-form">

                                                                <div class='form-row row'>
                                                                    <div class='col-xs-12 col-md-12 form-group required'>
                                                                        <label class='control-label'><?php echo trans('name-on-card') ?></label>
                                                                        <input class='form-control' type='text' value="">
                                                                    </div>
                                                                </div>

                                                                <div class='form-row row'>
                                                                    <div class='col-xs-12 col-md-12 form-group card required'>
                                                                        <label class='control-label'><?php echo trans('card-number') ?></label> <input autocomplete='off' class='form-control card-number' type='text' value="">
                                                                    </div>
                                                                </div>

                                                                <div class='form-row row'>
                                                                    <div class='col-xs-12 col-md-4 form-group cvc required'>
                                                                        <label class='control-label'>CVC</label> <input autocomplete='off' class='form-control card-cvc' placeholder='ex. 311' size='4' type='text' value="">
                                                                    </div>
                                                                    <div class='col-xs-12 col-md-4 form-group expiration required'>
                                                                        <label class='control-label'><?php echo trans('expiration') . ' ' . trans('month') ?></label> <input class='form-control card-expiry-month' placeholder='MM' size='2' type='text' value="">
                                                                    </div>
                                                                    <div class='col-xs-12 col-md-4 form-group expiration required'>
                                                                        <label class='control-label'><?php echo trans('expiration') . ' ' . trans('year') ?></label> <input class='form-control card-expiry-year' placeholder='YYYY' size='4' type='text' value="">
                                                                    </div>
                                                                </div>

                                                                <div class="text-center text-success">
                                                                    <div class="payment_loader" style="display: none;"><i class="fa fa-spinner fa-spin"></i> Loading....</div><br>
                                                                </div>

                                                                <!-- csrf token -->
                                                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">


                                                                <div class="row">
                                                                    <div class="col-md-12 text-center">
                                                                        <button class="btn btn-info btn-lg payment_btn" type="submit">Pay Now <?php echo currency_to_symbol($invoice->currency_code); ?><?php echo html_escape($price) ?></button>
                                                                    </div>
                                                                </div>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif ?>

                                    <!-- paypal payment -->
                                    <?php if ($user->razorpay_payment == 1) : ?>

                                        <?php
                                        $productinfo = $invoice->title;
                                        $txnid = time();
                                        $price = $price;
                                        $surl = $surl;
                                        $furl = $furl;
                                        $key_id = $user->razorpay_key_id;
                                        $currency_code = $invoice->currency_code;
                                        $total = ($price * 100);
                                        $amount = $amount;
                                        $merchant_order_id = $invoice->id;
                                        $card_holder_name = helper_get_customer($invoice->customer)->name;
                                        $email = helper_get_customer($invoice->customer)->email;
                                        $phone = helper_get_customer($invoice->customer)->phone;
                                        $name = settings()->site_name;
                                        $return_url = base_url() . 'addons/razorpay/user_payment/' . $invoice->id . '/' . $price . '/' . $amount;
                                        ?>

                                        <div class="tab-pane container" id="razorpay">
                                            <div class="row">
                                                <div class="col-md-6 m-auto">

                                                    <div class="box-body text-center">

                                                        <form name="razorpay-form" id="razorpay-form" action="<?php echo $return_url; ?>" method="POST">
                                                            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" />
                                                            <input type="hidden" name="merchant_order_id" id="merchant_order_id" value="<?php echo $merchant_order_id; ?>" />
                                                            <input type="hidden" name="merchant_trans_id" id="merchant_trans_id" value="<?php echo $txnid; ?>" />
                                                            <input type="hidden" name="merchant_product_info_id" id="merchant_product_info_id" value="<?php echo $productinfo; ?>" />
                                                            <input type="hidden" name="merchant_surl_id" id="merchant_surl_id" value="<?php echo $surl; ?>" />
                                                            <input type="hidden" name="merchant_furl_id" id="merchant_furl_id" value="<?php echo $furl; ?>" />
                                                            <input type="hidden" name="card_holder_name_id" id="card_holder_name_id" value="<?php echo $card_holder_name; ?>" />
                                                            <input type="hidden" name="merchant_total" id="merchant_total" value="<?php echo $total; ?>" />
                                                            <input type="hidden" name="merchant_amount" id="merchant_amount" value="<?php echo $amount; ?>" />

                                                            <input type="hidden" name="currency_code" value="<?php echo html_escape($invoice->currency_code); ?>">
                                                            <!-- csrf token -->
                                                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                                        </form><br>

                                                        <input class="btn btn-info btn-lg payment_btn" id="submit-pay" type="submit" onclick="razorpaySubmit(this);" value="<?php echo trans('pay-now') ?> <?php echo currency_to_symbol($invoice->currency_code); ?><?php echo html_escape($price) ?>" class="btn btn-lg btn-infocs " />

                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <?php include APPPATH . 'views/admin/include/razorpay-user-js.php'; ?>

                                    <?php endif ?>

                                    <?php if ($user->paystack_payment == 1) : ?>
                                        <div class="tab-pane container" id="paystack">
                                            <div class="row">
                                                <div class="col-md-6 m-auto">
                                                    <?php
                                                    $paystack_type = 'user';
                                                    $productinfo = $invoice->title;
                                                    $price = $price;
                                                    $currency_code = $invoice->currency_code;
                                                    $order_id = $invoice->id;
                                                    $customer_name = helper_get_customer($invoice->customer)->name;
                                                    $email = helper_get_customer($invoice->customer)->email;
                                                    $phone = helper_get_customer($invoice->customer)->phone;
                                                    $name = settings()->site_name;
                                                    ?>
                                                    <div class="box-body text-center">
                                                        <form method="post">
                                                            <script src="https://js.paystack.co/v1/inline.js"></script><br>
                                                            <button type="button" onclick="payWithPaystack()" class="btn btn-info btn-lg payment_btn"> <?php echo trans('pay-now') ?> <?php echo currency_to_symbol($invoice->currency_code); ?> <?php echo html_escape($price) ?> </button>
                                                        </form>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <?php include APPPATH . 'views/admin/include/paystack-js.php'; ?>
                                    <?php endif ?>


                                    <?php if ($user->cashfree_payment == 1) : ?>

                                        <?php
                                        $mode = "PROD";

                                        $randum = rand(10, 100);
                                        $productinfo = $package->name;
                                        $txnid = time();
                                        $price = $price;
                                        $currency_code = $user->currency;
                                        $total = ($price * 100);
                                        $amount = $price;
                                        //echo $orderId = $billing_type.'-'."16";
                                        $orderId = $billing_type . '-' . $package->id . '-' . $randum;
                                        $card_holder_name = user()->name;
                                        $email = user()->email;
                                        $phone = user()->phone;
                                        $name = $user->site_name;
                                        $return_url = base_url() . 'home/testfunction';
                                        $notifyUrl = base_url() . 'home/testfunction';
                                        //$notifyUrl = base_url().'addons/cashfree/redirect_cancel_payment';

                                        $secretKey = $user->cashfree_secret_key;
                                        $appId = $user->cashfree_app_id;
                                        $orderNote = "Subscription Account";
                                        $csrf_test_name = $this->security->get_csrf_hash();

                                        $postData = array(
                                            "appId" => $appId,
                                            "user_id" => user()->id,
                                            "orderId" => $orderId,
                                            "orderAmount" => $amount,
                                            "orderCurrency" => $currency_code,
                                            "orderNote" => $orderNote,
                                            "customerName" => $card_holder_name,
                                            "customerPhone" => $phone,
                                            "customerEmail" => $email,
                                            "returnUrl" => $return_url,
                                            "notifyUrl" => $notifyUrl,
                                        );

                                        ksort($postData);
                                        $signatureData = "";
                                        foreach ($postData as $key => $value) {
                                            $signatureData .= $key . $value;
                                        }
                                        $signature = hash_hmac('sha256', $signatureData, $secretKey, true);
                                        $signature = base64_encode($signature);

                                        if ($mode == "TEST") {
                                            $url = "https://www.cashfree.com/checkout/post/submit";
                                        } else {
                                            $url = "https://test.cashfree.com/billpay/checkout/post/submit";
                                        }

                                        ?>
                                        <div class="tab-pane container" id="cashfree">
                                            <div class="row">
                                                <div class="col-md-12 m-auto">
                                                    <div class="box-body text-center">
                                                        <?php if ($user->enable_discount == 1) : ?>
                                                            <?php if ($billing_type == 'monthly') : ?>
                                                                <span class="soft-success dp"><?php echo $package->dis_month ?>% <?php echo trans('off') ?></span>
                                                            <?php else : ?>
                                                                <span class="soft-success dp"><?php echo $package->dis_year ?>% <?php echo trans('off') ?></span>
                                                            <?php endif ?>
                                                        <?php endif ?>
                                                        <form action="<?php echo $url; ?>" name="frm_cashfree" id="frm_cashfree" method="post">
                                                            <input type="hidden" name="signature" value='<?php echo $signature; ?>' />
                                                            <input type="hidden" name="user_id" value='<?php echo user()->id; ?>' />
                                                            <input type="hidden" name="orderNote" value='<?php echo $orderNote; ?>' />
                                                            <input type="hidden" name="orderCurrency" value='<?php echo $currency_code; ?>' />
                                                            <input type="hidden" name="customerName" value='<?php echo $card_holder_name; ?>' />
                                                            <input type="hidden" name="customerEmail" value='<?php echo $email; ?>' />
                                                            <input type="hidden" name="customerPhone" value='<?php echo $phone; ?>' />
                                                            <input type="hidden" name="orderAmount" value='<?php echo $amount; ?>' />
                                                            <input type="hidden" name="notifyUrl" value='<?php echo $notifyUrl; ?>' />
                                                            <input type="hidden" name="returnUrl" value='<?php echo $return_url; ?>' />
                                                            <input type="hidden" name="appId" value='<?php echo $appId; ?>' />
                                                            <input type="hidden" name="orderId" value='<?php echo $orderId; ?>' />
                                                        </form>

                                                        <input class="btn btn-info btn-lg payment_btn" id="submit-pay" type="submit" onclick="cashfreeSubmit();" value="<?php echo trans('pay-now') ?> <?php echo currency_to_symbol($invoice->currency_code); ?> <?php echo html_escape($price) ?>" class="btn btn-lg btn-infocs" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>

        <?php endif ?>


        <div class="container mt-50">
            <div class="col-md-10 m-auto">

                <div class="row mb-10">
                    <div class="col-md-6">
                        <a href="<?php echo $_SERVER['HTTP_REFERER'] ?>" class="btn btn-default btn-rounded mr-5"><i class="fa fa-long-arrow-left"></i> <?php echo trans('back') ?> </a>
                    </div>
                    <div class="col-md-6 text-right mt-15">
                        <?php if ($invoice->status == 0) : ?>
                            <span data-toggle="tooltip" data-placement="right" title="<?php echo trans('approve-info') ?>" class="custom-label-sm label-light-default pull-right"><?php echo trans('draft') ?></span>
                        <?php elseif ($invoice->status == 2) : ?>
                            <span data-toggle="tooltip" data-placement="right" title="<?php echo trans('paid-info') ?>" class="custom-label-sm label-light-success pull-right"><?php echo trans('paid') ?></span>
                        <?php elseif ($invoice->status == 1) : ?>
                            <span data-toggle="tooltip" data-placement="right" title="<?php echo trans('unpaid-info') ?>" class="custom-label-sm label-light-danger pull-right"><?php echo trans('unpaid') ?></span>
                        <?php endif ?>
                    </div>
                </div>

                <div id="invoice_save_area mt-0" class="card inv save_area print_area">

                    <?php include "include/invoice_style_1.php"; ?>

                </div>
            </div>
        </div>


    </section>

</div>

<script>
    function cashfreeSubmit() {
        var form = document.getElementById("frm_cashfree");

        form.submit();
    }
</script>