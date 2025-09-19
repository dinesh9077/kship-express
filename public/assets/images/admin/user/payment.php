<style>
    li.col.nav-item {
        padding: 0;
    }

    ul.nav.nav-pills.text-center.m-auto {
        width: 95%;
    }

    ul.nav.nav-pills.text-center.m-auto li.nav-item {
        margin-bottom: 5px;
    }

    ul.nav.nav-pills.text-center.m-auto li.nav-item img {
        width: 20%;
    }
</style>
<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
        <?php $settings = get_settings(); ?>
        <?php
        $paypal_url = ($settings->paypal_mode == 'sandbox') ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
        $paypal_id = html_escape($settings->paypal_email);

        ?>

        <?php if ($billing_type == 'monthly') : ?>
            <?php
            if (settings()->enable_discount == 1) {
                $price = get_discount($package->monthly_price, $package->dis_month);
            } else {
                $price = round($package->monthly_price);
            }
            $frequency = trans('per-month');
            $billing_type = 'monthly';
            ?>
        <?php else : ?>
            <?php
            if (settings()->enable_discount == 1) {
                $price = get_discount($package->price, $package->dis_year);
            } else {
                $price = round($package->price);
            }
            $frequency = trans('per-year');
            $billing_type = 'yearly';
            ?>
        <?php endif ?>

        <?php
        if (empty($this->session->userdata('landing_plan'))) :
            $active = "";
        else :
            $active = "active";
        endif
        ?>
        <div class="container mt-50 mb-20">
            <div class="row justify-content-center">
                <div class="box col-lg-8">
                    <h3 class="pt-15 text-center"><?php echo trans('please-select-a-payment-method') ?></h3>
                    <div class="text-center m-auto">
                        <ul class="nav nav-pills text-center m-auto">
                            <?php if (settings()->paypal_payment == 1) : ?>
                                <li class="col nav-item">
                                    <a class="nav-link active" data-toggle="pill" href="#paypal"><img src="<?php echo base_url("assets/admin/paypal.png") ?>" width="25%">&nbsp; Paypal</a>
                                </li>
                            <?php endif ?>
                            <?php if (settings()->stripe_payment == 1) : ?>
                                <li class="col nav-item">
                                    <a class="nav-link" data-toggle="pill" href="#stripe"><img src="<?php echo base_url("assets/admin/stripe.png") ?>" width="25%">&nbsp;Stripe</a>
                                </li>
                            <?php endif ?>
                            <?php if (settings()->razorpay_payment == 1) : ?>
                                <li class="col nav-item">
                                    <a class="nav-link" data-toggle="pill" href="#razorpay"><img src="<?php echo base_url("assets/admin/rzp-glyph-positive.png") ?>" width="25%">&nbsp;Razorpay</a>
                                </li>
                            <?php endif ?>
                            <?php if (settings()->paystack_payment == 1) : ?>
                                <li class="col nav-item">
                                    <a class="nav-link" data-toggle="pill" href="#paystack"><img src="<?php echo base_url("assets/admin/paystack.png") ?>" width="25%">&nbsp;Paystack</a>
                                </li>
                            <?php endif ?>
                            <?php if (settings()->cashfree_payment == 1) : ?>
                                <li class="col nav-item">
                                    <a class="nav-link <?php echo $active; ?>" data-toggle="pill" href="#cashfree"><img src="<?php echo base_url("assets/admin/a146b015e8eb3e923b4d285d5c1dc972f7f513de.png") ?>" width="25%">&nbsp;Cashfree</a>
                                </li>
                            <?php endif ?>
                        </ul>
                    </div>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <!-- paypal payment -->
                        <?php if (settings()->paypal_payment == 1) : ?>
                            <div class="tab-pane container active" id="paypal">

                                <div class="row">
                                    <div class="col-md-12 m-auto">

                                        <div class="box-body text-center">

                                            <?php if (settings()->enable_discount == 1) : ?>
                                                <?php if ($billing_type == 'monthly') : ?>
                                                    <span class="custom-label-sm label-light-success"><?php echo $package->dis_month ?>% <?php echo trans('off') ?></span>
                                                <?php else : ?>
                                                    <span class="custom-label-sm label-light-success"><?php echo $package->dis_year ?>% <?php echo trans('off') ?></span>
                                                <?php endif ?>
                                            <?php endif ?>

                                            <!-- PRICE ITEM -->
                                            <form action="<?php echo html_escape($paypal_url); ?>" method="post" name="frmPayPal1">
                                                <div class="pipanel price panel-red">
                                                    <input type="hidden" name="business" value="<?php echo html_escape($paypal_id); ?>" readonly>
                                                    <input type="hidden" name="cmd" value="_xclick">
                                                    <input type="hidden" name="item_name" value="<?php echo html_escape($package->name); ?>">
                                                    <input type="hidden" name="item_number" value="1">
                                                    <input type="hidden" name="amount" value="<?php echo html_escape($price) ?>" readonly>
                                                    <input type="hidden" name="no_shipping" value="1">
                                                    <input type="hidden" name="currency_code" value="<?php echo html_escape($settings->currency); ?>">
                                                    <input type="hidden" name="cancel_return" value="<?php echo base_url('admin/subscription/payment_cancel/' . $billing_type . '/' . html_escape($package->id) . '/' . html_escape($payment_id)) ?>">
                                                    <input type="hidden" name="return" value="<?php echo base_url('admin/subscription/payment_success/' . $billing_type . '/' . html_escape($package->id) . '/' . html_escape($payment_id)) ?>">


                                                    <div class="panel-body text-center p-0">
                                                        <h3 class="mb-0"><?php echo trans('package-plan') ?>: <?php echo html_escape($package->name); ?></h3>
                                                        <p class="lead"><strong><?php echo currency_to_symbol(settings()->currency); ?><?php echo html_escape($price) ?> <?php echo html_escape($frequency) ?></strong></p>
                                                    </div>
                                                    <div class="panel-footer">
                                                        <button class="btn btn-info btn-lg payment_btn" href="#"><?php echo trans('pay-now') ?> <?php echo currency_to_symbol(settings()->currency); ?><?php echo html_escape($price) ?></button>
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
                        <?php if (settings()->stripe_payment == 1) : ?>
                            <div class="tab-pane container" id="stripe">
                                <div class="row">
                                    <div class="col-md-12 m-auto">
                                        <h3 class="mb-0 text-center"><?php echo trans('package-plan') ?>: <?php echo html_escape($package->name); ?></h3><br>
                                        <div class="text-center mb-20">
                                            <?php if (settings()->enable_discount == 1) : ?>
                                                <?php if ($billing_type == 'monthly') : ?>
                                                    <span class="custom-label-sm label-light-success"><?php echo $package->dis_month ?>% <?php echo trans('off') ?></span>
                                                <?php else : ?>
                                                    <span class="custom-label-sm label-light-success"><?php echo $package->dis_year ?>% <?php echo trans('off') ?></span>
                                                <?php endif ?>
                                            <?php endif ?><br>
                                        </div>

                                        <div class="credit-card-box">
                                            <div class="box-header">
                                                <h3 class="box-title flex-parent-between">
                                                    <?php echo trans('payment') . ' ' . trans('details') ?>
                                                    <span><img class="img-responsive pull-right" width="40%" src="<?php echo base_url('assets/images/accept-cards.jpg') ?>"></span>
                                                </h3>
                                            </div>
                                            <div class="box-body">

                                                <form role="form" action="<?php echo base_url('auth/stripe_payment') ?>" method="post" class="require-validation" data-cc-on-file="false" data-stripe-publishable-key="<?php echo settings()->publish_key; ?>" id="payment-form">

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

                                                    <input type="hidden" name="billing_type" value="<?php echo $billing_type; ?>" readonly>
                                                    <input type="hidden" name="package_id" value="<?php echo $package->id; ?>" readonly>
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button class="btn btn-info btn-lg payment_btn" type="submit">Pay Now <?php echo currency_to_symbol(settings()->currency); ?><?php echo html_escape($price) ?></button>
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
                        <?php if (settings()->razorpay_payment == 1) : ?>

                            <?php
                            $productinfo = $package->name;
                            $txnid = time();
                            $price = $price;
                            $surl = $surl;
                            $furl = $furl;
                            $key_id = settings()->razorpay_key_id;
                            $currency_code = settings()->currency;
                            $total = ($price * 100);
                            $amount = $price;
                            $merchant_order_id = $package->id;
                            $card_holder_name = user()->name;
                            $email = user()->email;
                            $phone = user()->phone;
                            $name = settings()->site_name;
                            $return_url = base_url() . 'addons/razorpay/payment';
                            ?>

                            <div class="tab-pane container" id="razorpay">
                                <div class="row">
                                    <div class="col-md-12 m-auto">

                                        <div class="box-body text-center">

                                            <?php if (settings()->enable_discount == 1) : ?>
                                                <?php if ($billing_type == 'monthly') : ?>
                                                    <span class="custom-label-sm label-light-success"><?php echo $package->dis_month ?>% <?php echo trans('off') ?></span>
                                                <?php else : ?>
                                                    <span class="custom-label-sm label-light-success"><?php echo $package->dis_year ?>% <?php echo trans('off') ?></span>
                                                <?php endif ?>
                                            <?php endif ?>

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

                                                <input type="hidden" name="billing_type" value="<?php echo html_escape($billing_type); ?>" readonly>
                                                <!-- csrf token -->
                                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                            </form>


                                            <h3 class="mb-0"><?php echo trans('package-plan') ?>: <?php echo html_escape($package->name); ?></h3>
                                            <br>
                                            <p class="lead"><strong><?php echo currency_to_symbol(settings()->currency); ?><?php echo html_escape($price) ?> <?php echo html_escape($frequency) ?></strong></p>
                                            <br>
                                            <input class="btn btn-info btn-lg payment_btn" id="submit-pay" type="submit" onclick="razorpaySubmit(this);" value="<?php echo trans('pay-now') ?> <?php echo currency_to_symbol(settings()->currency); ?><?php echo html_escape($price) ?>" class="btn btn-lg btn-infocs" />

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <?php include APPPATH . 'views/admin/include/razorpay-js.php'; ?>


                        <?php endif ?>


                        <?php if (settings()->paystack_payment == 1) : ?>
                            <div class="tab-pane container" id="paystack">
                                <div class="row">
                                    <div class="col-md-12 m-auto">

                                        <div class="box-body text-center">
                                            <?php if (settings()->enable_discount == 1) : ?>
                                                <?php if ($billing_type == 'monthly') : ?>
                                                    <span class="custom-label-sm label-light-success"><?php echo $package->dis_month ?>% <?php echo trans('off') ?></span>
                                                <?php else : ?>
                                                    <span class="custom-label-sm label-light-success"><?php echo $package->dis_year ?>% <?php echo trans('off') ?></span>
                                                <?php endif ?>
                                            <?php endif ?>
                                            <form method="post">
                                                <div class="panel-body text-center">
                                                    <h3 class="mb-0"><?php echo trans('package-plan') ?>: <?php echo html_escape($package->name); ?></h3>
                                                    <br>
                                                    <p class="lead"><strong><?php echo currency_to_symbol(settings()->currency); ?><?php echo html_escape($price) ?> <?php echo html_escape($frequency) ?></strong></p>
                                                </div>
                                                <br>
                                                <script src="https://js.paystack.co/v1/inline.js"></script>
                                                <button type="button" onclick="payWithPaystack()" class="btn btn-info btn-lg payment_btn"> <?php echo trans('pay-now') ?> <?php echo currency_to_symbol(settings()->currency); ?><?php echo html_escape($price) ?> </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php include APPPATH . 'views/admin/include/paystack-js.php'; ?>
                        <?php endif ?>

                        <?php if (settings()->cashfree_payment == 1) : ?>

                            <?php
                            $mode = "PROD";

                            $randum = rand(10, 100);
                            $productinfo = $package->name;
                            $txnid = time();
                            $price = $price;
                            $currency_code = settings()->currency;
                            $total = ($price * 100);
                            $amount = $price;
                            //echo $orderId = $billing_type.'-'."16";
                            $orderId = $billing_type . '-' . $package->id . '-' . user()->id . '-' . $randum;
                            $card_holder_name = user()->name;
                            $email = user()->email;
                            $phone = user()->phone;
                            $name = settings()->site_name;
                            $return_url = base_url() . 'home/paymnet_success';
                            $notifyUrl = base_url() . 'home/paymnet_success';
                            //$notifyUrl = base_url().'addons/cashfree/redirect_cancel_payment';

                            $secretKey = settings()->cashfree_secret_key;
                            $appId = settings()->cashfree_app_id;
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

                            if (settings()->cashfree_mode == "sandbox") {
                                $url = "https://test.cashfree.com/billpay/checkout/post/submit";
                            } else {
                                $url = "https://www.cashfree.com/checkout/post/submit";
                            }

                            ?>
                            <div class="tab-pane container <?php echo $active; ?>" id="cashfree">
                                <div class="row">
                                    <div class="col-md-12 m-auto">
                                        <div class="box-body text-center">
                                            <?php if (settings()->enable_discount == 1) : ?>
                                                <?php if ($billing_type == 'monthly') : ?>
                                                    <span class="custom-label-sm label-light-success"><?php echo $package->dis_month ?>% <?php echo trans('off') ?></span>
                                                <?php else : ?>
                                                    <span class="custom-label-sm label-light-success"><?php echo $package->dis_year ?>% <?php echo trans('off') ?></span>
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

                                            <h3 class="mb-0"><?php echo trans('package-plan') ?>: <?php echo html_escape($package->name); ?></h3>
                                            <br>
                                            <p class="lead"><strong><?php echo currency_to_symbol(settings()->currency); ?><?php echo html_escape($price) ?> <?php echo html_escape($frequency) ?></strong></p>
                                            <br>
                                            <input class="btn btn-info btn-lg payment_btn" id="submit-pay" type="submit" onclick="cashfreeSubmit();" value="<?php echo trans('pay-now') ?> <?php echo currency_to_symbol(settings()->currency); ?><?php echo html_escape($price) ?>" class="btn btn-lg btn-infocs" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>



        <div class="container text-center">

        </div>


    </section>

</div>
<script>
    function cashfreeSubmit() {
        var form = document.getElementById("frm_cashfree");

        form.submit();
    }
</script>