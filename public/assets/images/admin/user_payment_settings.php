<style>
    .btn-info {
        border-radius: 8px !important;
    }

    @media only screen and (max-width: 320px) {

        .box-title {
            font-size: 18px;
        }

        .toggle.btn.btn-info {
            width: 60px !important;
            height: 15px !important;
        }

        .toggle.btn.btn-light.off {
            width: 60px !important;
            height: 15px !important;
        }

    }
</style>
<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">

        <div class="card-box">
            <!--<div class="row">-->
            <!--    <h2 style="font-size: 28px">Payments Settings</h2>-->
            <!--</div>-->

            <div class="row mt-20">
                <div class="col-xl-3 col-lg-12">
                    <div class="nav-tabs-custom profile_menu_web">
                        <?php include "user/include/profile_menu.php"; ?>
                    </div>
                    <div class="nav-tabs-custom profile_menu_mobile">
                        <?php include "user/include/profile_menu_1.php"; ?>
                    </div>
                </div>

                <div class="col-xl-9">
                    <form method="post" action="<?php echo base_url('admin/payment/user_update') ?>" role="form" class="form-horizontal">
                        <div class="row">

                            <div class="col-lg-4 col-md-6 col-sm-6 pay-width">
                                <div class="box bg-light">
                                    <div class="box-header with-border">
                                        <h3 class="box-title d-block"><img src="<?php echo base_url('assets\front-purple\image\paystack-2.png') ?>" style="width: auto; height: 20px;">&nbsp;<?php //echo trans('paystack') ?> <span class="pull-right">
                                                <input type="checkbox" name="paystack_payment" value="1" <?php if (user()->paystack_payment == 1) {
                                                                                                                echo 'checked';
                                                                                                            } ?> data-toggle="toggle" data-onstyle="info" data-width="100"></span></h3>
                                    </div>
                                </div>

                                <div class="box-body pl-0 pr-0">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('publish-key') ?> </label>
                                        <div class="col-sm-12">
                                            <input type="text" name="paystack_public_key" value="<?php echo html_escape(user()->paystack_public_key); ?>" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('secret-key') ?> </label>
                                        <div class="col-sm-12">
                                            <input type="text" name="paystack_secret_key" value="<?php echo html_escape(user()->paystack_secret_key); ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-6 pay-width">
                                <div class="box bg-light">
                                    <div class="box-header with-border">
                                        <h3 class="box-title d-block"><img src="<?php echo base_url('assets\front-purple\image\rozapay.png') ?>" style="width: auto; height: 30px;">&nbsp;<?php //echo trans('razorpay') ?> <span class="pull-right"><input type="checkbox" name="razorpay_payment" value="1" <?php if (user()->razorpay_payment == 1) {
                                                                                                                                                                                                                                                                                        echo 'checked';
                                                                                                                                                                                                                                                                                    } ?> data-toggle="toggle" data-onstyle="info" data-width="100"></span></h3>
                                    </div>
                                </div>

                                <div class="box-body pl-0 pr-0  ">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('key-id') ?> </label>
                                        <div class="col-sm-12">
                                            <input type="text" name="razorpay_key_id" value="<?php echo html_escape(user()->razorpay_key_id); ?>" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('key-secret') ?> </label>
                                        <div class="col-sm-12">
                                            <input type="text" name="razorpay_key_secret" value="<?php echo html_escape(user()->razorpay_key_secret); ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <?php // echo trans('stripe-payment') ?> 
                            
                            <div class="col-lg-4 col-md-6 col-sm-6 pay-width">
                                <div class="box bg-light">
                                    <div class="box-header with-border">
                                        <h3 class="box-title d-block"><img src="<?php echo base_url('assets\front-purple\image\stripe.png') ?>" style="width: auto; height: 30px;" >&nbsp;<span class="pull-right"><input type="checkbox" name="stripe_payment" value="1" <?php if (user()->stripe_payment == 1) {
                                                                                                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                                                                                                            } ?> data-toggle="toggle" data-onstyle="info" data-width="100"></span></h3>
                                    </div>
                                </div>

                                <div class="box-body pl-0 pr-0  ">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('publish-key') ?> </label>
                                        <div class="col-sm-12">
                                            <input type="text" name="publish_key" value="<?php echo html_escape(user()->publish_key); ?>" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('secret-key') ?> </label>
                                        <div class="col-sm-12">
                                            <input type="text" name="secret_key" value="<?php echo html_escape(user()->secret_key); ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 pay-width">
                                <div class="box bg-light">
                                    <div class="box-header with-border">
                                        <h3 class="box-title d-block"><img src="<?php echo base_url('assets\front-purple\image\cashfree.png') ?>" style="width: auto; height: 30px;">&nbsp;  <span class="pull-right"><input type="checkbox" name="cashfree_payment" value="1" <?php if (user()->cashfree_payment == 1) {
                                                                                                                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                                                                                                                            } ?> data-toggle="toggle" data-onstyle="info" data-width="100"></span></h3>
                                    </div>
                                </div>
                                <div class="box-body pl-0 pr-0  ">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="example-input-normal">Cashfree APP ID </label>
                                        <div class="col-sm-12">
                                            <input type="text" name="cashfree_app_id" value="<?php echo html_escape(user()->cashfree_app_id); ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="example-input-normal">Cashfree Secret Key </label>
                                        <div class="col-sm-12">
                                            <input type="text" name="cashfree_secret_key" value="<?php echo html_escape(user()->cashfree_secret_key); ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 pay-width">
                                <div class="box bg-light">

                                    <div class="box-header with-border">
                                        <h3 class="box-title d-block"><img src="<?php echo base_url('assets\front-purple\image\paypal.png') ?>" style="width: auto; height: 30px;">&nbsp;<?php //echo trans('paypal-payment') ?> <span class="pull-right"><input type="checkbox" name="paypal_payment" value="1" <?php if (user()->paypal_payment == 1) {
                                                                                                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                                                                                                            } ?> data-toggle="toggle" data-onstyle="info" data-width="100"></span></h3>
                                    </div>
                                </div>

                                <div class="box-body pl-0 pr-0   mb-10">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('paypal-merchant-account') ?></label>
                                        <div class="col-sm-12">
                                            <input type="text" name="paypal_email" value="<?php echo html_escape(user()->paypal_email); ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- csrf token -->
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                        <div class="div">
                            <button type="submit" class="btn btn-info btn-lgs waves-effect w-md waves-light m-b-5"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
                        </div>

                    </form>
                </div>

            </div>

        </div>
    </section>
</div>