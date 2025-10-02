<style>
    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }
</style>


<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="col-md-12 m-auto box add_area pd" style="display: <?php if (isset($_SESSION['error']) || $page_title == "Edit") {
                                                                            echo "block";
                                                                        } else {
                                                                            echo "none";
                                                                        } ?>">
            <div class="box-header with-border bg-light1 justify-content-center f-no align-items-center">
                <?php if (isset($page_title) && $page_title == "Edit") : ?>
                    <h3 class="box-title"><i class="flaticon-network"></i>&nbsp;<?php echo trans('edit-customer') ?></h3>
                <?php else : ?>
                    <h3 class="box-title"><i class="flaticon-network"></i>&nbsp;<?php echo trans('add-new-customer') ?> </h3>
                <?php endif; ?>
                <div class="box-tools pull-right">
                    <?php if (isset($page_title) && $page_title == "Edit") : ?>
                        <a href="<?php echo base_url('admin/customer') ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                    <?php else : ?>
                        <a href="#" class="pull-right btn btn-info rounded btn-sm cancel_btn"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="box-body">
                <form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form row" action="<?php echo base_url('admin/customer/add') ?>" role="form" novalidate>
                    <div class="col-lg-6 form-group">
                        <label><?php echo trans('customer-name') ?>  <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" required name="name" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['name'] : html_escape($customer[0]['name']); ?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <label><?php echo trans('email') ?></label>
                        <input type="email" class="form-control" name="email" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['email'] : html_escape($customer[0]['email']); ?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <label><?php echo trans('phone') ?></label>
                        <input type="text" class="form-control" name="phone" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['phone'] : html_escape($customer[0]['phone']); ?>">
                    </div>
                    <div class="col-lg-6 form-group">
                        <label><?php echo trans('address') ?> </label>
                        <textarea class="form-control" name="address"><?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['address'] : html_escape($customer[0]['address']); ?></textarea>
                    </div>
                    <div class="col-lg-4 form-group">
                        <label>Shipping contact person name : </label>
                        <input type="text" class="form-control" required name="s_name" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['s_name'] : html_escape($customer[0]['s_name']); ?>">
                    </div>
                    <div class="col-lg-3 form-group">
                        <label>Shipping <?php echo trans('phone') ?> No.</label>
                        <input type="text" class="form-control" name="s_phone" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['s_phone'] : html_escape($customer[0]['s_phone']); ?>">
                    </div>
                    <div class="col-lg-5 form-group">
                        <label>Shipping Address</label>
                        <textarea class="form-control" name="address1"><?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['address1'] : html_escape($customer[0]['address1']); ?></textarea>
                    </div>
                    <div class="col-lg-12">
                        <h4><?php echo trans('billing-information') ?></h4>
                        <br>
                    </div>
                    <!-- <div class="col-lg-6 form-group" >
                        <label><?php echo trans('business') . ' ' . trans('number') ?></label>
                        <input type="text" class="form-control" name="cus_number" value="<?php echo html_escape($customer[0]['cus_number']); ?>" >
					</div> -->
                    <div class="col-lg-6 form-group">
                        <label>Select Tax Type</label>
                        <select class="form-control single_select" style="width:100%" name="tax_format" id="tax_format" onchange="changeFunction();">
                            <option value="0"><?php echo trans('select') ?></option>
                            <option value="GST Number" <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['tax_format'] == 'GST Number') || $customer[0]['tax_format'] == 'GST Number') ? 'selected' : ''; ?>>GST Number</option>
                            <option value="Tax Number" <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['tax_format'] == 'Tax Number') || $customer[0]['tax_format'] == 'Tax Number') ? 'selected' : ''; ?>>Tax Number</option>
                            <option value="Vat Number" <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['tax_format'] == 'Vat Number') || $customer[0]['tax_format'] == 'Vat Number') ? 'selected' : ''; ?>>Vat Number</option>
                            <option value="Tax/Vat Number" <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['tax_format'] == 'Tax/Vat Number') || $customer[0]['tax_format'] == 'Tax/Vat Number') ? 'selected' : ''; ?>>Tax/Vat Number</option>
                        </select>
                    </div>
                    <div class="col-lg-6 form-group" id="vat_code_show" <?php if ($customer[0]['tax_format'] != "" || isset($_SESSION['error'])) {
                                                                            echo "style='display:block;'";
                                                                        } else {
                                                                            echo "style='display:none;'";
                                                                        } ?>>
                        <label id="text_name_change"><?php echo $customer[0]['tax_format']; ?></label>
                        <input type="text" class="form-control" name="vat_code" id="edit_vat_code" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['vat_code'] : html_escape($customer[0]['vat_code']); ?>">
                        <span id="lblError" class="error"><?php echo (isset($_SESSION['error']) && isset($_SESSION['gst_error'])) ? $_SESSION['gst_error'] : ''; ?></span>
                        <span id="lblSError" class="suerror"></span>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label class="col-sm-2 control-label p-0" for="example-input-normal"><?php echo trans('country') ?> </label>
                        <select class="form-control single_select col-sm-12 country" id="countrys" name="country" style="width: 100%" <?php echo ((isset($page_title) && $page_title == "Edits") && $customer[0]['country'] != "0") ? "disabled" : '' ?>>
                            <option value=""><?php echo trans('select') ?></option>
                            <?php foreach ($countries as $country) : ?>
                                <?php if (!empty($country->currency_name)) : ?>
                                    <option value="<?php echo html_escape($country->id); ?>" data-currencyname="<?php echo html_escape($country->name); ?>" data-currency_code="<?php echo html_escape($country->currency_code); ?>" data-currency_symbol="<?php echo html_escape($country->currency_symbol); ?>"  <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['country'] == $country->id) || $customer[0]['country'] == $country->id) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($country->name); ?>
                                    </option>
                                <?php endif ?>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label class="col-sm-2 control-label p-0" for="example-input-normal"><?php echo trans('currency') ?> </label>
                        <select class="form-control col-sm-12 wd-100 single_select" style="width:100%" id="currency" name="currency" <?php echo (isset($page_title) && $page_title == "Edit") ? "disabled" : '' ?>>
                            <option value=""><?php echo trans('select') ?></option>
                            <?php foreach ($countries as $currency) : ?>
                                <option value="<?php echo $currency->currency_code; ?>" <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['country'] == $currency->id) || $customer[0]['country'] == $currency->id) ? 'selected' : ''; ?>><?php echo $currency->currency_code . ' - ' . $currency->currency_name; ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-lg-6 form-group hide">
                        <label><?php echo trans('city') ?></label>
                        <input type="text" class="form-control" name="city" value="<?php echo html_escape($customer[0]['city']); ?>">
                    </div>
                    <div class="col-lg-6 form-group hide">
                        <label><?php echo trans('postal-zip-code') ?></label>
                        <input type="text" class="form-control" name="postal_code" value="<?php echo html_escape($customer[0]['postal_code']); ?>">
                    </div>
                    <div class="col-lg-6 form-group hide">
                        <label><?php echo trans('address') ?> 1</label>
                        <!-- <textarea class="form-control" name="address1"><?php echo html_escape($customer[0]['address1']); ?></textarea> -->
                    </div>
                    <div class="col-lg-6 form-group hide">
                        <label><?php echo trans('address') ?> 2</label>
                        <textarea class="form-control" name="address2"><?php echo html_escape($customer[0]['address2']); ?></textarea>
                    </div>
                    <input type="hidden" name="id" value="<?php echo html_escape($customer['0']['id']); ?>">
                    <!-- csrf token -->
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <hr>
                    <!--<div class="row m-t-30">-->
                    <div class="col-sm-12">
                        <?php if (isset($page_title) && $page_title == "Edit") : ?>
                            <input type="hidden" id="hid_country" name="country" value="<?php echo html_escape($customer[0]['country']); ?>">
                            <input type="hidden" id="hid_currency" name="currency" value="<?php echo html_escape($customer[0]['currency']); ?>">
                            <button type="submit" class="btn btn-info rounded pull-left"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
                        <?php else : ?>
                            <button type="submit" class="btn btn-info rounded pull-left"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
                        <?php endif; ?>
                    </div>
                    <!--</div>-->
                    <input type="hidden" name="gst_validation" id="gst_validation" value="0">
                </form>
            </div>
            <div class="box-footer">
            </div>
        </div>

        <?php if (isset($page_title) && $page_title != "Edit") : ?>

            <div class="list_area" style="display: <?php if (isset($_SESSION['error']) || $page_title == "Edit") {
                                                        echo "none";
                                                    } else {
                                                        echo "block";
                                                    } ?>">
                <div class="card-box">
                    <?php if (isset($page_title) && $page_title == "Edit") : ?>
                        <h3 class="box-title"><?php echo trans('edit-customer') ?> <a href="<?php echo base_url('admin/customer') ?>" class="pull-right btn btn-primary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a></h3>
                    <?php else : ?>
                        <div class="d-flex justify-content-between bg-light1 align-items-center loan_re">
                            <h3 class="box-title"><i class="flaticon-network"></i>&nbsp;<?php echo trans('all-customers') ?></h3>
                            <div class="add-btn add_new">
                                <a href="#" class="btn btn-info rounded btn-sm add_btn"><i class="fa fa-plus"></i> <?php echo trans('add-new-customer') ?></a>
                            </div>
                        <?php endif; ?>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0 over_scroll">

                            <table class="table table-hover cushover <?php if (count($customers) > 10) {
                                                                            echo "datatable";
                                                                        } ?>">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo trans('name') ?></th>
                                        <th><?php echo trans('info') ?></th>
                                        <th><?php echo trans('email') ?></th>
                                        <th><?php echo trans('phone') ?></th>
                                        <th>Created At</th>
                                        <th><?php echo trans('action') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($customers as $customer) : ?>
                                        <tr id="row_<?php echo html_escape($customer->id); ?>">

                                            <td><?php echo $i; ?></td>
                                            <td style="text-transform: capitalize"><strong><?php echo html_escape($customer->name); ?></strong></td>
                                            <td>
                                                <p class="mb-0"><?php echo html_escape($customer->country_name); ?></p>
                                                <?php if (!empty($customer->country_name)) : ?>
                                                    <p class="mb-0 fs-12"><?php echo html_escape($customer->currency_code . ' - ' . $customer->currency_name . ' (' . $customer->currency_symbol . ')'); ?></p>
                                                <?php endif ?>
                                            </td>
                                            <td><?php echo html_escape($customer->email); ?></td>
                                            <td><?php echo html_escape($customer->phone); ?></td>
                                            <td><?php echo my_date_show($customer->created_at); ?></td>

                                            <td class="actions" width="12%">
                                                <a href="<?php echo base_url('admin/customer/edit/' . html_escape($customer->id)); ?>" class="on-default edit-row" data-placement="top" title="<?php echo trans('edit') ?>"><i class="fa fa-pencil"></i></a> &nbsp;

                                                <a data-val="<?php echo trans('customer') ?>" data-id="<?php echo html_escape($customer->id); ?>" href="<?php echo base_url('admin/customer/delete/' . html_escape($customer->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a> &nbsp;
                                            </td>
                                        </tr>

                                    <?php $i++;
                                    endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                </div>

            </div>
        <?php endif; ?>

    </section>
</div>
<style type="text/css">
    body {
        font-family: Arial;
        font-size: 10pt;
    }

    .error {
        color: Red;
    }

    .suerror {
        color: green;
    }

    .gst {
        text-transform: uppercase;
    }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script>
    changeFunction();

    function changeFunction() {
        var tax_format = $("#tax_format").val();
        if (tax_format == 'GST Number') {
            $("#vat_code_show").show();
            $("#text_name_change").text("GST Number");

            $("#edit_vat_code").attr("onkeyup", "ValidateGSTNumber()");
        } else if (tax_format == 'Tax Number') {
            $("#vat_code_show").show();
            $("#text_name_change").text("Tax Number");

            $("#edit_vat_code").attr("onkeyup", "");
            $("#gst_validation").val(0);
        } else if (tax_format == 'Vat Number') {
            $("#vat_code_show").show();
            $("#text_name_change").text("Vat Number");

            $("#edit_vat_code").attr("onkeyup", "");
            $("#gst_validation").val(0);
        } else if (tax_format == 'Tax/Vat Number') {
            $("#vat_code_show").show();
            $("#text_name_change").text("Tax/Vat Number");

            $("#edit_vat_code").attr("onkeyup", "");
            $("#gst_validation").val(0);
        } else {
            $("#vat_code_show").hide();
            $("#edit_vat_code").attr("onkeyup", "");
            $("#gst_validation").val(0);
        }
    }
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js"></script>
<script type="text/javascript">
    function ValidateGSTNumber() {
        var gstNumber = document.getElementById("edit_vat_code").value;
        var lblError = document.getElementById("lblError");
        lblError.innerHTML = "";
        lblSError.innerHTML = "";
        var expr = /^([0-9]{2}[a-zA-Z]{4}([a-zA-Z]{1}|[0-9]{1})[0-9]{4}[a-zA-Z]{1}([a-zA-Z]|[0-9]){3}){0,15}$/;

        if (gstNumber) {
            if (!expr.test(gstNumber)) {
                lblError.innerHTML = "Invalid GST Number.";
                $("#gst_validation").val(1);
            } else {
                lblSError.innerHTML = "Valid GST Number.";
                $("#gst_validation").val(2);
            }
        } else {
            lblError.innerHTML = "Please Enter GST Number.";
            $("#gst_validation").val(0);
        }
    }
    // $(document).ready(function() {
    //     $('.country').change(function(e) {
    //         e.preventDefault();
    //         let hid_country = $('#country').find(":selected").val();
    //         let hid_currency = $('#currency').find(":selected").val();
    //         console.log('hid_country', hid_country);
    //         console.log('hid_currency', hid_currency);
    //         $('#hid_country').val(hid_country);
    //         $('#hid_currency').val(hid_currency);
    //     });
    // });
</script>