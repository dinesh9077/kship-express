<?php
$account_types = ['Bank', 'Current account', 'Savings account', 'Salary account', 'Fixed deposit account', 'Recurring deposit account', 'NRI account'];
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content" style="padding-top: 2%;">
        <div class="col-md-12 m-auto box add_area add-banking" style="display: <?php if (isset($_SESSION['error']) || $page_title == "Edit") {
                                                                                    echo "block";
                                                                                } else {
                                                                                    echo "none";
                                                                                } ?>">
            <div class="add-banking-bg">
                <div class="d-flex align-items-center justify-content-between bg-light1 f-no">
                    <?php if (isset($page_title) && $page_title == "Edit") : ?>
                        <h3 class="box-title"><i class="fa fa-bank" aria-hidden="true"></i>&nbsp;Edit Bank Detail</h3>
                    <?php else : ?>
                        <h3 class="box-title"><i class="fa fa-bank" aria-hidden="true"></i>&nbsp;Add New Bank</h3>
                    <?php endif; ?>
                    <div class="box-tools pull-right">
                        <?php if (isset($page_title) && $page_title == "Edit") : ?>
                            <a href="<?php echo base_url('admin/banking') ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                        <?php else : ?>
                            <a href="#" class="text-right pull-right btn btn-info rounded btn-sm add_btn cancel_btn"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="box-body">
                    <form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form row" action="<?php echo base_url('admin/banking/add') ?>" role="form" novalidate>
                        <div class="col-lg-6 form-group ">
                            <label for="example-input-normal">Account Type  <span class="text-danger">*</span></label>
                            <select class="form-control col-sm-12 wd-100 single_select" style="width:100%" id="account_type" name="account_type" required>
                                <option value="">Select Account Type</option>
                                <option <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['account_type'] == 'Current account"') || $customer[0]['account_type'] == "Current account") ? 'selected' : ''; ?>>Current account</option>
                                <option <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['account_type'] == 'Savings account') || $customer[0]['account_type'] == "Savings account") ? 'selected' : ''; ?>>Savings account</option>
                                <option <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['account_type'] == 'Salary account') || $customer[0]['account_type'] == "Salary account") ? 'selected' : ''; ?>>Salary account</option>
                                <option <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['account_type'] == 'Fixed deposit account') || $customer[0]['account_type'] == "Fixed deposit account") ? 'selected' : ''; ?>>Fixed deposit account</option>
                                <option <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['account_type'] == 'Recurring deposit account') || $customer[0]['account_type'] == "Recurring deposit account") ? 'selected' : ''; ?>>Recurring deposit account</option>
                                <option <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['account_type'] == 'NRI account') || $customer[0]['account_type'] == "NRI account") ? 'selected' : ''; ?>>NRI account</option>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group">
                            <label>Account Holder Name  <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="account_name" name="account_name" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['account_name'] : html_escape($customer[0]['account_name']); ?>" required>
                        </div>

                        <div class="col-lg-6 form-group">
                            <label id="acc_numb">Account Number</label>
                            <input type="text" class="form-control" name="account_number" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['account_number'] : html_escape($customer[0]['account_number']); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                            <?php if (isset($_SESSION['error']) && isset($_SESSION['account_number_error'])) { ?>
                                <span id="lblError" class="text-danger"><?= $_SESSION['account_number_error'] ?></span>
                            <?php } ?>
                        </div>

                        <div class="col-lg-6 form-group">
                            <label> Bank Name  <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['bank_name'] : html_escape($customer[0]['bank_name']); ?>" required>
                        </div>

                        <div class="col-lg-6 form-group">
                            <label>IFSC</label>
                            <input type="text" class="form-control" name="ifsc" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['ifsc'] : html_escape($customer[0]['ifsc']); ?>" required>
                        </div>

                        <div class="col-lg-6 form-group">
                            <label id="card_lim">Opening Balance  <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="opening_balance" value="<?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['opening_balance'] : html_escape($customer[0]['opening_balance']); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                        </div>

                        <div class="col-lg-6 form-group">
                            <label>Description </label>
                            <textarea class="form-control" name="description"><?php echo (isset($_SESSION['error'])) ? $_SESSION['post_data']['description'] : html_escape($customer[0]['description']); ?></textarea>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label class="control-label p-0" for="example-input-normal">Status  <span class="text-danger">*</span></label>
                            <select class="form-control col-sm-12 wd-100 single_select" style="width:100%" id="status" name="status" required>
                                <option value="1" <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['status'] == '1') || $customer[0]['status'] == "1") ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo ((isset($_SESSION['error']) && $_SESSION['post_data']['status'] == '0') || $customer[0]['status'] == "0") ? 'selected' : ''; ?>>In-Active</option>
                            </select>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label class="control-label p-0" for="example-input-normal">Opening Date</label>
                            <input type="text" class="form-control datepicker" name="created_at" value="<?php echo (!empty($customer[0]['created_at'])) ? date('Y-m-d', strtotime($customer[0]['created_at'])) : date('Y-m-d'); ?>" required>
                        </div>

                        <input type="hidden" name="id" value="<?php echo html_escape($customer['0']['id']); ?>">
                        <!-- csrf token -->
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                        <hr>

                        <!--<div class="row m-t-30">-->
                        <div class="col-sm-12">
                            <?php if (isset($page_title) && $page_title == "Edit") : ?>
                                <button type="submit" class="btn btn-info rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
                            <?php else : ?>
                                <button type="submit" class="btn btn-info rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
                            <?php endif; ?>
                        </div>
                        <!--</div>-->
                    </form>
                </div>
            </div>
        </div>

        <?php if (isset($page_title) && $page_title != "Edit") : ?>
            <div class="list_area" style="display: <?php if (isset($_SESSION['error']) || $page_title == "Edit") {
                                                        echo "none";
                                                    } else {
                                                        echo "block";
                                                    } ?>">
                <?php if (isset($page_title) && $page_title == "Edit") : ?>
                    <h2 class="box-title">Edit Bank Detail <a href="<?php echo base_url('admin/banking') ?>" class="pull-right btn btn-primary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a></h2>
                <?php else : ?>

                    <div class="row">
                        <div class="col-xl-2 col-lg-12 col-md-12 width-bank">
                            <div class="bank-left">
                                <div class="card-box1">
                                    <h3 class="box-title2">Banking</h3>
                                    <div class="purple-box">
                                        <img src="<?php echo base_url('assets/admin/Money.png') ?>">
                                        <p>Cash in Hand </p>
                                        <h4><?php echo $this->business->currency_symbol . $cash_amount; ?></h4>
                                    </div>
                                    <div class="purple-box">
                                        <img src="<?php echo base_url('assets/admin/bank1.png') ?>">
                                        <p>Bank Balance </p>
                                        <h4><?php echo $this->business->currency_symbol . $bank_amount; ?></h4>
                                    </div>
                                    <div class="purple-box">
                                        <img src="<?php echo base_url('assets/admin/outstnt.png') ?>">
                                        <p>Card / Outstanding Balance </p>
                                        <h4><?php echo $this->business->currency_symbol . $card_amount; ?> / <?php echo $this->business->currency_symbol . $outstand; ?></h4>
                                    </div>
                                    <div class="purple-box">
                                        <img src="<?php echo base_url('assets/admin/card.png') ?>">
                                        <p>Total Card Limit</p>
                                        <h4><?php echo $this->business->currency_symbol . $card_limit_amount; ?> </h4>
                                    </div>

                                </div>
                            <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-xl-10 col-lg-12 col-md-12 width-bank">
                            <div class="bank-right">
                                <div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive p-0">
                                    <div class="card-box">
                                        <div class="d-flex justify-content-between bg-light1 align-items-center f-no">
                                            <h2><img class="ic-icon" src="<?php echo base_url('assets/admin/bank1.png') ?>">&nbsp;Bank </h2>
                                            <a href="#" class="pull-right btn btn-info rounded btn-sm add_btn"><i class="fa fa-plus"></i> Add New Bank </a>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table nowrape table-hover cushover <?php if (count($customers) > 10) {
                                                                                                    echo "datatable";
                                                                                                } ?>" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Account Type / No.</th>
                                                        <th>Account Holder Name</th>
                                                        <th>Bank Name / Ifsc</th>
                                                        <!-- <th>Currency</th> -->
                                                        <th class="text-right">Opening Balance</th>
                                                        <th>Status</th>
                                                        <th></th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $i = 1;
                                                    foreach ($customers as $customer) :
                                                        if (in_array($customer->account_type, $account_types, TRUE)) { ?>
                                                            <tr id="row_<?php echo html_escape($customer->id); ?>">
                                                                <td><?php echo $i; ?></td>
                                                                <td>
                                                                    <div><strong><?php echo html_escape($customer->account_type); ?></strong></div>
                                                                    <div>#<?php echo html_escape($customer->account_number); ?></div>
                                                                </td>
                                                                <td style="text-transform: capitalize"><?php echo html_escape($customer->account_name); ?></td>
                                                                <td style="text-transform: capitalize">
                                                                    <div><strong><?php echo html_escape($customer->bank_name); ?></strong></div>
                                                                    <div><?php echo html_escape($customer->ifsc); ?></div>
                                                                </td>
                                                                <!-- <td><?php echo html_escape($customer->currency); ?></td> -->
                                                                <td class="text-right">
                                                                    <span class="total-price">
                                                                        <?php echo $this->business->currency_symbol . ' ' . html_escape(decimal_format($customer->opening_balance, 2)); ?>
                                                                    </span>
                                                                    <!-- <br>
                                                    <span class="conver-total"><?php echo $customer->currency . html_escape(decimal_format($customer->converted_to_bussiness_currency, 2)); ?></span> -->
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    if ($customer->status == 1) {
                                                                        echo "<span class='badge badge-success'>Active</span>";
                                                                    } else {
                                                                        echo "<span class='badge badge-danger'>In-Active</span>";
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?php if ($customer->bank_print_invoice == 1) : ?>
                                                                        <a href="#" class="btn btn-default" disabled data-toggle="tooltip" data-placement="top" title="Print bank details on invoices"><i class="fa fa-check"></i> <?php echo trans('active') ?></a>
                                                                    <?php else : ?>
                                                                        <a data-id="<?php echo html_escape($customer->id); ?>" href="<?php echo base_url('admin/banking/set_primary/' . md5($customer->id)); ?>" class="btn btn-default primary_banking" data-toggle="tooltip" data-placement="top" title="Print bank details on invoices"> <?php echo trans('set-default') ?></a>
                                                                    <?php endif ?>
                                                                </td>
                                                                <td class="actions" width="12%">
                                                                    <?php if ($customer->edit == "no") { ?>
                                                                        <a href="<?php echo base_url('admin/banking/edit/' . html_escape($customer->id)); ?>" class="on-default edit-row" data-toggle="tooltip" data-placement="top" title="<?php echo trans('edit') ?>"><i class="fa fa-pencil"></i></a>
                                                                    <?php } ?>
                                                                    <a data-val="<?php echo trans('customer') ?>" data-id="<?php echo html_escape($customer->id); ?>" href="<?php echo base_url('admin/banking/delete/' . html_escape($customer->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a>

                                                                    <a href="<?php echo base_url('admin/banking/view/' . html_escape($customer->id)); ?>" class="on-default view-row" data-toggle="tooltip" data-placement="top" title="View Transaction"><i class="fa fa-eye"></i></a>
                                                                </td>
                                                            </tr>
                                                    <?php $i++;
                                                        }
                                                    endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive p-0">
                                    <div class="card-box">
                                        <div class="d-flex justify-content-between bg-light1 align-items-center f-no">
                                            <h2><img class="ic-icon" src="<?php echo base_url('assets/admin/card.png') ?>" height="40">&nbsp;Credit Card </h2>
                                            <a href="<?php echo base_url('admin/banking/add_credit_card'); ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-plus"></i> Add New Card</a>
                                        </div>
                                        <table class="table nowrape table-hover cushover <?php if (count($cards) > 10) {
                                                                                                echo "datatable";
                                                                                            } ?>" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Account Type</th>
                                                    <th>Account Holder Name</th>
                                                    <th>Bank Name</th>
                                                    <!--<th>Currency</th>-->
                                                    <th class="text-right">Card Limit</th>
                                                    <th class="text-right">Current Limit</th>
                                                    <th class="text-right">Outstanding</th>
                                                    <th>Card Due Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1;
                                                foreach ($cards as $customer) : ?>
                                                    <?php $old_lim = $customer->card_limit - $customer->current_limit; ?>
                                                    <?php $oots = $old_lim + get_outstanding_value($customer->id); ?>
                                                    <tr id="row_<?php echo html_escape($customer->id); ?>" style="background-color:<?php // echo ($oots > 0) ? '#6347cb17' : '#1ce74d29'; 
                                                                                                                                    ?>">
                                                        <td><?php echo $i; ?></td>
                                                        <td><strong><?php echo html_escape($customer->account_type); ?></strong></td>
                                                        <td style="text-transform: capitalize"><?php echo html_escape($customer->account_name); ?></td>
                                                        <td style="text-transform: capitalize"><?php echo html_escape($customer->bank_name); ?></td>
                                                        <!-- <td><?php echo html_escape($customer->currency); ?></td>-->

                                                        <td class="text-right"><?php echo $this->business->currency_symbol . decimal_format($customer->main_balance, 2); ?></td>

                                                        <td class="text-right"><?php echo $this->business->currency_symbol . decimal_format($customer->opening_balance, 2); ?></td>

                                                        <td class="text-right"><?php
                                                                                echo $this->business->currency_symbol . decimal_format($oots, 2); ?></td>

                                                        <td><?php echo html_escape(date('d', strtotime($customer->card_duedate))); ?> / every month</td>
                                                        <td>
                                                            <?php
                                                            if ($customer->status == 1) {
                                                                echo "<span class='badge badge-success'>Active</span>";
                                                            } else {
                                                                echo "<span class='badge badge-danger'>In-Active</span>";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="actions" width="12%">
                                                            <?php if ($customer->edit == "no") { ?>

                                                            <?php } ?>
                                                            <a href="<?php echo base_url('admin/banking/edit_card/' . html_escape($customer->id)); ?>" class="on-default edit-row" data-toggle="tooltip" data-placement="top" title="<?php echo trans('edit') ?>"><i class="fa fa-pencil"></i></a>
                                                            <a data-val="<?php echo trans('customer') ?>" data-id="<?php echo html_escape($customer->id); ?>" href="<?php echo base_url('admin/banking/delete/' . html_escape($customer->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a>

                                                            <a href="<?php echo base_url('admin/banking/view/' . html_escape($customer->id)); ?>" class="on-default view-row" data-toggle="tooltip" data-placement="top" title="View Transaction"><i class="fa fa-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php $i++;
                                                endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive p-0">
                                    <div class="card-box">
                                        <div class="d-flex justify-content-between bg-light1 align-items-center f-no">
                                            <h2><img src="<?php echo base_url('assets/admin/Money.png') ?>" height="40">&nbsp;Cash </h2>
                                            <a href="<?php echo base_url('admin/banking/add_cash'); ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-plus"></i> Add New Cash</a>
                                        </div>
                                        <table class="table nowrape table-hover cushover <?php if (count($cashes) > 10) {
                                                                                                echo "datatable";
                                                                                            } ?>" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Account Type</th>
                                                    <th>Cash Holder Name</th>
                                                    <!--<th>Bank Name</th>-->
                                                    <th>Currency</th>
                                                    <th class="text-center">Cash Balance</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1;
                                                foreach ($cashes as $customer) : ?>
                                                    <tr id="row_<?php echo html_escape($customer->id); ?>">
                                                        <td><?php echo $i; ?></td>
                                                        <td><strong><?php echo html_escape($customer->account_type); ?></strong></td>
                                                        <td><?php echo html_escape($customer->account_name); ?></td>
                                                        <!--<td><?php //echo html_escape($customer->bank_name); 
                                                                ?></td>-->
                                                        <td><?php echo html_escape($customer->currency); ?></td>
                                                        <td class="text-center"><?php echo $this->business->currency_symbol . decimal_format($customer->opening_balance, 2); ?></td>
                                                        <td>
                                                            <?php
                                                            if ($customer->status == 1) {
                                                                echo "<span class='badge badge-success'>Active</span>";
                                                            } else {
                                                                echo "<span class='badge badge-danger'>In-Active</span>";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="actions" width="12%">
                                                            <?php if ($customer->edit == "no") { ?>
                                                                <a href="<?php echo base_url('admin/banking/edit_cash/' . html_escape($customer->id)); ?>" class="on-default edit-row" data-toggle="tooltip" data-placement="top" title="<?php echo trans('edit') ?>"><i class="fa fa-pencil"></i></a>
                                                            <?php } ?>
                                                            <a data-val="<?php echo trans('customer') ?>" data-id="<?php echo html_escape($customer->id); ?>" href="<?php echo base_url('admin/banking/delete/' . html_escape($customer->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a>

                                                            <a href="<?php echo base_url('admin/banking/view/' . html_escape($customer->id)); ?>" class="on-default view-row" data-toggle="tooltip" data-placement="top" title="View Transaction"><i class="fa fa-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php $i++;
                                                endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        <?php endif; ?>

    </section>
</div>