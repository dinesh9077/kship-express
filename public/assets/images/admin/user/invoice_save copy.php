<?php //include  'invoice_val.php'; 
$settings = get_settings();
// $currency_symbol = helper_get_customer($invoice->customer)->currency_symbol;
if (isset($invoice->c_currency_symbol) && !empty($invoice->c_currency_symbol)) {
    $currency_symbol = $invoice->c_currency_symbol;
} else {
    $currency_symbol = $this->business->currency_symbol;
}
?>

<style>
    /* .menu__toggler span,
    .menu__toggler span::before,
    .menu__toggler span::after {
        background: #000;
    } */
</style>

<div class="menu__toggler"><span></span></div>
<div class="content-wrapper">
    <section class="content mt-20">
        <div class="container">
            <div class="col-md-10 col-xs-12 m-auto in_remind">
                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        <h2 class="mt-0"><?php echo trans('invoice') ?> #<?php echo html_escape($invoice->number) ?></h2>
                    </div>
                    <div class="col-md-6 col-xs-12 text-right">
                        <div class="top_button">
                            <a href="<?php echo base_url('admin/invoice/create') ?>" class="btn btn-default btn-rounded">Create another invoice</a>
                            <div class="btn-group mr-5">
                                <button type="button" class="btn btn-default btn-rounded dropdown-toggle btn_trigger" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="icon-settings"></i> <?php echo trans('more-actions') ?>
                                    <div class="circle circle-blue"></div>
                                </button>
                                <div class="dropdown-menu st" x-placement="bottom-start">
                                    <a class="dropdown-item print_invoice" href="#"><?php echo trans('print') ?></a>

                                    <?php if (settings()->pdf_type == 1) : ?>
                                        <a href="#" data-id="invoice_<?php echo rand() ?>" class="dropdown-item btnExport"><?php echo trans('download-pdf') ?> </a>
                                    <?php else : ?>
                                        <a target="_blank" class="dropdown-item" href="<?php echo base_url('readonly/export_pdf/' . md5($invoice->id)) ?>"><?php echo trans('export-as-pdf') ?></a>
                                    <?php endif ?>


                                    <?php if ($invoice->recurring == 1 && $invoice->is_completed == 0) : ?>
                                        <a class="dropdown-item stop_recurring" href="<?php echo base_url('admin/invoice/stop_recurring/' . $invoice->id) ?>"><?php echo trans('stop-recurring') ?></a>
                                    <?php endif ?>

                                    <?php if ($invoice->recurring == 0) : ?>
                                        <a class="dropdown-item convert_to_recurring" href="<?php echo base_url('admin/invoice/convert_recurring/' . md5($invoice->id)) ?>"><?php echo trans('convert-recurring') ?></a>
                                    <?php endif ?>

                                    <a class="dropdown-item" data-toggle="modal" href="#sendInvoiceModal_<?php echo html_escape($invoice->id) ?>"><?php echo trans('send') ?></a>
                                    <a target="_blank" class="dropdown-item" href="<?php echo base_url('readonly/invoice/preview/' . md5($invoice->id)) ?>"><?php echo trans('preview-as-a-customer') ?></a>
                                    <?php if ($settings->enable_delete_invoice == 1) : ?>
                                        <a class="dropdown-item delete_item" href="<?php echo base_url('admin/invoice/delete/' . $invoice->id) ?>"><?php echo trans('delete') ?></a>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="set_value" name="check_value">
                </div>
                <hr style="border-top: 1px solid #c6cccd;">
                <div class="row m-0 p-0">
                    <div class="col-md-12 p-15 table-responsive border-none">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th class="border-0"><?php echo trans('status') ?></th>
                                    <th class="border-0"><?php echo trans('customer') ?></th>
                                    <th class="border-0"><?php echo trans('amount-due') ?> </th>
                                    <th class="border-0"><?php echo trans('total') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="border-top: none">
                                        <?php if ($invoice->status == 0) : ?>
                                            <span class="custom-label-sm label-light-default"><?php echo trans('draft') ?></span>
                                        <?php elseif ($invoice->status == 1) : ?>
                                            <span class="custom-label-sm label-light-info"><?php echo trans('approved') ?></span>
                                        <?php elseif ($invoice->status == 2) : ?>
                                            <span class="custom-label-sm label-light-success"><?php echo trans('paid') ?></span>
                                        <?php endif ?>
                                    </td>
                                    <td style="text-transform: capitalize; border-top: none">
                                        <p class="view_link m-0">
                                            <?php if (!empty(helper_get_customer($invoice->customer))) : ?>
                                                <?php echo helper_get_customer($invoice->customer)->name ?>
                                            <?php endif ?>
                                        </p>
                                    </td>
                                    <td style=" border-top: none">
                                        <?php
                                        if (!empty($currency_symbol)) {
                                            echo html_escape($currency_symbol);
                                        }
                                        if ($invoice->c_rate != 0) {
                                            echo decimal_format(html_escape($due_amt = $invoice->grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id) / $invoice->c_rate), 2);
                                        } else {

                                            echo decimal_format(html_escape($due_amt = $invoice->grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id)), 2);
                                        }
                                        // if (!empty($currency_symbol)) {
                                        //     echo html_escape($currency_symbol);
                                        // } 

                                        // if($convert_total != 0){
                                        //     $amotdue = $convert_total - get_total_invoice_payments($invoice->id, $invoice->parent_id);
                                        //     echo decimal_format($amotdue / $c_rate, 2);
                                        // } else {
                                        //     echo decimal_format($invoice->grand_total, 2);
                                        // }
                                        ?>
                                    </td>
                                    <td style=" border-top: none">
                                        <?php
                                        if (!empty($currency_symbol)) {
                                            echo html_escape($currency_symbol);
                                        }
                                        echo decimal_format($invoice->grand_total, 2);
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-12 p-0">
                        <!-- for regular invoices -->
                        <?php
                        if ($invoice->recurring == 0) :
                            if ($invoice->status == 0) : ?>
                                <div class="row mt-20">
                                    <div class="col-md-12">
                                        <div class="card br-10 b-warning bg-pending <?php if ($invoice->status == 0) {
                                                                                        echo "dshadow";
                                                                                    } ?>">
                                            <div class="card-body bg-muted br-10">
                                                <div class="m-l-10 align-self-center">
                                                    <h3 class="box-title mt-10 m-b-0"><?php echo trans('draft-invoice') ?>
                                                        <button data-id="<?php echo html_escape(md5($invoice->id)) ?>" type="button" class="pull-right btn btn-info btn-sm rounded approve_invoice"><i class="ti-check"></i> <?php echo trans('approve') ?> </button>
                                                    </h3>
                                                    <h5 class="text-muted m-b-0"><i class="icon-info"></i> <?php echo trans('draft-inv-info') ?>
                                                    </h5>
                                                    <p><strong>Created:</strong> On <?php echo my_date_show($invoice->created_at) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="invoice-vertical-line"></div>
                                </div>
                            <?php
                            endif ?>
                            <div class="row mt-10">
                                <div class="col-md-12">
                                    <div class="card br-10 border-radius">
                                        <div class="card-body br-10 d-flex">
                                            <div class="inv_icon">
                                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                            </div>
                                            <div class="m-l-10 align-self-center" style="width: 100%">
                                                <p class="box-title text-muted m-b-0">
                                                    Create invoice
                                                    <?php if ($invoice->edit_show != 1) : ?>
                                                        <a href="<?php echo base_url('admin/invoice/edit/' . md5($invoice->id)) ?>" class="btn btn-default btn-rounded mr-5 pull-right"> <?php echo trans('edit') ?> invoice</a>
                                                    <?php endif; ?>
                                                </p>

                                                <p><strong>Created:</strong> On <?php echo my_date_show($invoice->created_at) ?></p>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="invoice-vertical-line"></div>
                            </div>
                            <div class="row mt-10">
                                <div class="col-md-12">
                                    <div class="card br-10 border-radius">
                                        <div class="card-body br-10 d-flex">
                                            <div class="inv_icon">
                                                <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                            </div>
                                            <div class="m-l-10 align-self-center" style="width: 100%">
                                                <p class="box-title text-muted m-b-0"><?php echo trans('send-invoice') ?>
                                                    <?php if ($invoice->is_sent == 1) : ?>
                                                        <button data-toggle="modal" data-target="#sendInvoiceModal_<?php echo html_escape($invoice->id) ?>" type="button" class="pull-right btn btn-default btn-sm rounded mr-5" <?php if ($invoice->status == 0) {
                                                                                                                                                                                                                                        echo "disabled";
                                                                                                                                                                                                                                    } ?>> Resend invoice... </button>
                                                    <?php else : ?>
                                                        <button data-toggle="modal" data-target="#sendInvoiceModal_<?php echo html_escape($invoice->id) ?>" type="button" class="pull-right btn btn-default btn-sm rounded mr-5" <?php if ($invoice->status == 0) {
                                                                                                                                                                                                                                        echo "disabled";
                                                                                                                                                                                                                                    } ?>> <?php echo trans('send-invoice') ?> </button>
                                                    <?php endif ?>
                                                </p>

                                                <p><strong><?php echo trans('last-sent') ?>:</strong>
                                                    <?php
                                                    if ($invoice->is_sent == 1) {
                                                        echo my_date_show_time($invoice->sent_date);
                                                    } else {
                                                        echo "None";
                                                    } ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="invoice-vertical-line"></div>
                            </div>

                            <div class="row mt-10">
                                <div class="col-md-12">
                                    <div class="card br-10 <?php
                                                            if ($invoice->status == 0) {
                                                                echo "is_disable";
                                                            } elseif ($invoice->status == 1) {
                                                                echo "dshadow";
                                                            } else {
                                                                echo "nshadow";
                                                            } ?>">
                                        <div class="card-body br-10 d-flex">
                                            <div class="inv_icon">
                                                <i class="fa fa-credit-card" aria-hidden="true"></i>
                                            </div>
                                            <div class="m-l-10 align-self-center" style="width: 100%">
                                                <p class="box-title text-muted m-b-0">Get Paid
                                                    <?php if ($invoice->status != 2) : ?>
                                                        <button data-toggle="modal" data-target="#recordPayment" type="button" class="pull-right btn btn-info btn-sm rounded" <?php if ($invoice->status == 0) {
                                                                                                                                                                                    echo "disabled";
                                                                                                                                                                                } ?>> <?php echo trans('record-a-payment') ?> </button>
                                                    <?php endif ?>
                                                </p>
                                                <p class="mt-10 m-b-0 text-info">
                                                    <?php if ($invoice->status == 1) : ?>
                                                        <?php if (check_paid_status($invoice->id) == 1) : ?>
                                                            <?php echo trans('paid-partial') ?>
                                                        <?php endif ?>
                                                    <?php elseif ($invoice->status == 2) : ?>
                                                        <?php echo trans('paid-info') ?>
                                                    <?php endif ?>
                                                </p>

                                                <p class="text-muteds m-b-0">
                                                    <strong>
                                                        <?php echo trans('amount-due') ?>: <?php if (!empty($currency_symbol)) {
                                                                                                echo html_escape($currency_symbol);
                                                                                            } ?></strong>
                                                    <?php
                                                    if ($invoice->status == 2) {
                                                        echo "0.00";
                                                    } else {
                                                        // if (!empty($currency_symbol)) {
                                                        //     echo html_escape($currency_symbol);
                                                        // }
                                                        if ($invoice->c_rate != 0) {
                                                            echo decimal_format(html_escape($due_amt = $invoice->grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id) / $invoice->c_rate), 2);
                                                        } else {
                                                            echo decimal_format(html_escape($due_amt = $invoice->grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id)), 2);
                                                        }
                                                        // if($convert_total != 0){
                                                        //     $amotdue = $convert_total - get_total_invoice_payments($invoice->id, $invoice->parent_id);
                                                        //     echo decimal_format($amotdue / $c_rate, 2);
                                                        // } else {
                                                        //     echo decimal_format($invoice->grand_total, 2);
                                                        // }
                                                        // $amotdue = $invoice->convert_total - get_total_invoice_payments($invoice->id, $invoice->parent_id);
                                                        // echo decimal_format($amotdue / $invoice->c_rate, 2);
                                                    } ?>
                                                </p>
                                                <?php if ($invoice->status == 1) : ?>
                                                    <p class="fs-13 text-danger"><strong>Status:</strong> <?php echo trans('awaiting-payment') ?></p>
                                                <?php endif; ?>

                                                <?php if ($invoice->status == 2) : ?>
                                                    <p class="fs-13 text-success"><strong>Status:</strong> <?php echo trans('paid-in-full') ?></p>
                                                    <hr>
                                                    <h5><strong><?php echo trans('payments-received') ?>:</strong></h5>
                                                    <p><?php echo my_date_show($payment->payment_date) ?> - <?php echo trans('a-payment-for') ?> <?php if (!empty($currency_symbol)) {
                                                                                                                                                        echo $currency_symbol;
                                                                                                                                                    } ?><?php echo html_escape($invoice->grand_total) ?> </p>
                                                <?php endif ?>


                                                <?php if (!empty(get_invoice_payments($invoice->id))) : ?>
                                                    <hr>
                                                    <h5 class="mb-20"><?php echo trans('payment-records') ?> </h5>
                                                <?php endif ?>

                                                <?php foreach (get_invoice_payments($invoice->id) as $payment) : ?>
                                                    <div class="payment_record flex-parent-between" id="row_<?php echo html_escape($payment->id) ?>">
                                                        <p>
                                                            <?php echo trans('payments-received') ?>
                                                            <strong class="text_mark"><?php if (!empty($currency_symbol)) {
                                                                                            echo html_escape($currency_symbol);
                                                                                        } ?><?php echo decimal_format($payment->amount / $invoice->c_rate, 2) ?></strong> at

                                                            <strong class="text_mark"><?php echo my_date_show($payment->payment_date) ?></strong> <?php echo trans('using') ?> <strong class="text_mark"><?php echo get_using_methods($payment->payment_method) ?></strong></span>
                                                        </p>

                                                        <p>
                                                            <span class="flex-parent-between">
                                                                <?php if ($invoice->edit_show != 1) : ?>
                                                                    <a href="#editRecordPayment_<?php echo html_escape($payment->id) ?>" data-toggle="modal"><span class="custom-label-sm label-light-info"><i class="ficon flaticon-pencil"></i> <?php echo trans('edit-payment') ?></span></a>
                                                                <?php endif; ?>
                                                                <!-- <a data-toggle="tooltip" data-placement="top" data-title="<?php echo trans('delete') ?>" href="<?php echo base_url('admin/invoice/delete_payment_record/' . $payment->id) ?>" data-id="<?php echo html_escape($payment->id) ?>" class="delete_item"><span class="custom-label-sm label-light-danger"><i class="icon-trash"></i></span></a> -->
                                                            </span>
                                                        </p>
                                                    </div>
                                                <?php endforeach ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <!-- for recurring invoices -->
                        <?php else : ?>
                            <div class="row mt-20">
                                <div class="col-md-12">
                                    <div class="card br-10 nshadow">
                                        <div class="card-body bg-muted br-10 d-flex">
                                            <div class="inv_icon">
                                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                            </div>
                                            <div class="m-l-10 align-self-center" style="width: 100%">
                                                <p class="box-title text-muted m-b-0">
                                                    Create invoice
                                                    <a href="<?php echo base_url('admin/invoice/edit/' . md5($invoice->id) . '/1') ?>" class="btn btn-default btn-rounded mr-5 pull-right"><i class="icon-pencil"></i> <?php echo trans('edit') ?> </a>
                                                </p>
                                                <p class="pt-10"><strong><?php echo trans('created-on') ?>:</strong> <?php echo my_date_show($invoice->created_at) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="invoice-vertical-line"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card br-10 <?php if ($invoice->status == 0) {
                                                                echo "dshadow";
                                                            } else {
                                                                echo "nshadow";
                                                            } ?>">
                                        <div class="card-body bg-muted br-10 d-flex">
                                            <div class="inv_icon">
                                                <i class="fa fa-credit-card" aria-hidden="true"></i>
                                            </div>
                                            <div class="m-l-10" style="width: 100%">

                                                <?php if (!empty($invoice->frequency)) : ?>
                                                    <p class="box-title text-muted mb-10">
                                                        <?php echo trans('invoice-schedule') ?>
                                                    </p>
                                                    <p class="mb-0"><strong><?php echo trans('recurring-start') ?>:</strong> <?php echo my_date_show($invoice->recurring_start) ?></p>
                                                    <p class="mb-0"><strong><?php echo trans('recurring-end') ?>:</strong>
                                                        <?php if ($invoice->recurring_end == '0000-00-00') : ?>
                                                            <?php echo trans('unlimited') ?>
                                                        <?php else : ?>
                                                            <?php echo my_date_show($invoice->recurring_end) ?>
                                                        <?php endif ?>
                                                    </p>
                                                    <p class="mb-0"><strong><?php echo trans('repeat-invoice') ?>:</strong> <?php echo html_escape($invoice->frequency) ?> days</p>
                                                <?php else : ?>

                                                    <h3 class="mb-10"><?php echo trans('set-schedule') ?></h3>
                                                    <form id="recurring_form" method="post" class="validate-form" action="<?php echo base_url('admin/invoice/set_recurring/' . $invoice->id) ?>" role="form" novalidate>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label><?php echo trans('start-date') ?> <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control datepicker" required name="recurring_start" autocomplete="off" value="<?php echo date('Y-m-d') ?>">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label><?php echo trans('repeat-this-invoice') ?> <span class="text-danger">*</span></label>
                                                                    <select class="form-control" name="frequency">
                                                                        <option value=""><?php echo trans('select') ?></option>
                                                                        <option value="7"><?php echo trans('weekly') ?></option>
                                                                        <option value="14">2 <?php echo trans('weeks') ?></option>
                                                                        <option value="21">3 <?php echo trans('weeks') ?></option>
                                                                        <option value="30"><?php echo trans('monthly') ?></option>
                                                                        <option value="60">2 <?php echo trans('months') ?></option>
                                                                        <option value="90">3 <?php echo trans('months') ?></option>
                                                                        <option value="120">4 <?php echo trans('months') ?></option>
                                                                        <option value="180">6 <?php echo trans('months') ?></option>
                                                                        <option value="365"><?php echo trans('yearly') ?></option>
                                                                        <option value="730">2 <?php echo trans('years') ?></option>
                                                                        <option value="1095">3 <?php echo trans('years') ?></option>
                                                                        <option value="1825">5 <?php echo trans('years') ?></option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label><?php echo trans('end-date') ?> </label>
                                                                    <input type="text" class="form-control datepicker" name="recurring_end" autocomplete="off">
                                                                    <p class="fs-12"><i class="fa fa-info-circle text-info"></i> <?php echo trans('recurring-end-info') ?></p>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <!-- <div class="form-group mb-0">
                                                                    <input type="checkbox" id="md_checkbox_1" class="filled-in chk-col-blue" value="1" name="auto_send">
                                                                    <label for="md_checkbox_1"><?php //echo trans('auto-send-invoice-by-e-mail') 
                                                                                                ?></label>
                                                                </div> -->

                                                                <!-- <div class="form-group mt-0">
                                                                    <input type="checkbox" id="md_checkbox_2" class="filled-in chk-col-blue" value="1" name="send_myself">
                                                                    <label for="md_checkbox_2"> <?php //echo trans('email-a-copy') 
                                                                                                ?></label>
                                                                </div> -->
                                                            </div>

                                                            <button type="submit" class=" btn btn-info btn-sm rounded ml-15"><i class="ti-check"></i> <?php echo trans('finish-setup') ?></button>

                                                        </div>

                                                        <!-- csrf token -->
                                                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                                    </form>

                                                <?php endif ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>

                    </div>
                </div>

                <div id="invoice_save_area" class="row card save_area print_area m-0 p-0" style="overflow: auto;">
                    <?php include "include/invoice_style_" . $this->business->template_style . ".php"; ?>
                </div>

            </div>
        </div>
    </section>
</div>


<?php include "include/send_invoice_modal.php"; ?>


<div id="recordPayment" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="vcenter" aria-hidden="true">
    <div class="modal-dialog modal-dialog-zoom modal-md">
        <form id="record_payment_form" method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/invoice/record_payment') ?>" role="form" novalidate>
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="vcenter"><?php echo trans('record-a-payment-for-this-invoice') ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <?php $records = get_customer_advanced_record($invoice->customer) ?>
                    <?php if (!empty($records) && $records->customer_id == $invoice->customer) : ?>
                        <?php if ($records->amount != 0) : ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> <strong>You have reserve amount for this customer: <?php echo $records->amount . ' ' . $records->currency; ?></strong>
                            </div>
                        <?php endif ?>
                    <?php endif ?>

                    <div class="form-group m-t-30" style="display: none;">
                        <input type="checkbox" name="is_autoload_amount" value="1" <?php if ($this->business->is_autoload_amount == 1) {
                                                                                        echo 'checked';
                                                                                    } ?> data-toggle="toggle" data-onstyle="info" data-width="100"></span>

                        <label> is autoload advance amount in your next invoice?</label>
                    </div>

                    <p class="text-muted"><?php echo trans('record-payment-info') ?></p><br>

                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label"><?php echo trans('payment-date') ?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="date" class="form-control datepicker" placeholder="yyyy/mm/dd" name="payment_date" value="<?php echo date('Y-m-d') ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fa fa-calender"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label"><?php echo trans('payment-date') ?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="date" class="form-control datepicker" placeholder="Enter the due date for partial payment" name="due_date" value="">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fa fa-calender"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($convert_total != 0) {
                        $amotdue = $invoice->convert_total - get_total_invoice_payments($invoice->id, $invoice->parent_id);
                    } else {
                        $amotdue = $invoice->grand_total - get_total_invoice_payments($invoice->id, $invoice->parent_id);
                    }
                    $amotdue = $amotdue / $invoice->c_rate
                    ?>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label"><?php echo trans('amount') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="amount" value="<?php echo $amotdue; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label">Select Bank</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="bank_id" required>
                                <option value=""><?php echo trans('select') ?></option>
                                <?php foreach ($bankdetails as $banking) :
                                    if ($banking->account_type != "Credit Card") :
                                ?>
                                        <option value="<?php echo html_escape($banking->id); ?>">

                                            <?php echo html_escape($banking->account_type) . '-' . html_escape($banking->bank_name); ?>
                                        </option>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label"><?php echo trans('payment-method') ?></label>
                        <div class="col-sm-8">
                            <select class="form-control" id="tax" name="payment_method">
                                <option value="0"><?php echo trans('select-payment-method') ?></option>
                                <?php $i = 1;
                                foreach (get_payment_methods() as $payment) : ?>
                                    <option value="<?php echo $i; ?>"><?php echo html_escape($payment); ?></option>
                                <?php $i++;
                                endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label"><?php echo trans('memo-notes') ?></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" name="note"> </textarea>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <input type="hidden" name="invoice_id" value="<?php echo html_escape(md5($invoice->id)) ?>">
                    <!-- csrf token -->
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <button type="submit" class="btn btn-info waves-effect pull-right"><?php echo trans('add-payment') ?></button>
                </div>
            </div>
        </form>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



<?php foreach (get_invoice_payments($invoice->id) as $payment) : ?>
    <div id="editRecordPayment_<?php echo html_escape($payment->id) ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="vcenter" aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom modal-md">
            <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/invoice/update_record_payment/' . $payment->id) ?>" role="form" novalidate>
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="vcenter"><?php echo trans('edit-payment') ?> </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">

                        <?php $records = get_customer_advanced_record($invoice->customer) ?>
                        <?php if (!empty($records) && $records->customer_id == $invoice->customer) : ?>
                            <?php if ($records->amount != 0) : ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> <strong>You have reserve amount for this customer: <?php echo $records->amount . ' ' . $records->currency; ?></strong>
                                </div>
                            <?php endif ?>
                        <?php endif ?>

                        <div class="form-group m-t-30" style="display: none;">
                            <input type="checkbox" name="is_autoload_amount" value="1" <?php if ($this->business->is_autoload_amount == 1) {
                                                                                            echo 'checked';
                                                                                        } ?> data-toggle="toggle" data-onstyle="info" data-width="100"></span>

                            <label> is autoload advance amount in your next invoice?</label>
                        </div>

                        <p class="text-muted"><?php echo trans('record-payment-info') ?></p><br>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label"><?php echo trans('payment-date') ?></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" class="form-control datepicker" placeholder="yyyy/mm/dd" name="payment_date" value="<?php echo html_escape($payment->payment_date) ?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fa fa-calender"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label"><?php echo trans('amount') ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="amount" value="<?php echo html_escape($payment->amount) ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label">Select Bank</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="bank_id" required>
                                    <option value=""><?php echo trans('select') ?></option>
                                    <?php foreach ($bankdetails as $banking) : ?>
                                        <option value="<?php echo html_escape($banking->id); ?>" <?php if ($banking->id == $payment->bank_id) {
                                                                                                        echo "selected";
                                                                                                    } ?>>
                                            <?php echo html_escape($banking->account_type) . '-' . html_escape($banking->bank_name); ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label"><?php echo trans('payment-method') ?></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="tax" name="payment_method">
                                    <option value="0"><?php echo trans('select-payment-method') ?></option>
                                    <?php $i = 1;
                                    foreach (get_payment_methods() as $payments) : ?>
                                        <option value="<?php echo $i; ?>" <?php if ($i == $payment->payment_method) {
                                                                                echo "selected";
                                                                            } ?>><?php echo html_escape($payments); ?></option>
                                    <?php $i++;
                                    endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 text-right control-label col-form-label"><?php echo trans('memo-notes') ?></label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="note"><?php echo $payment->note ?></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <input type="hidden" name="invoice_id" value="<?php echo html_escape(md5($payment->invoice_id)) ?>">
                        <!-- csrf token -->
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <button type="submit" class="btn btn-info waves-effect pull-right"><?php echo trans('update') ?></button>
                    </div>
                </div>
            </form>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
<?php endforeach; ?>


<script type="text/javascript">
    const toggler = document.querySelector('.menu__toggler');
    const menu = document.querySelector('.menu');

    /*
     * Toggles on and off the 'active' class on the menu
     * and the toggler button.
     */
    toggler.addEventListener('click', () => {
        toggler.classList.toggle('active');
        menu.classList.toggle('active');
    })
</script>