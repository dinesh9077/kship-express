<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">

        <div class="col-md-12 m-auto box add_area mt-50" style="display: <?php if ($page_title == "Edit") {
                                                                                echo "block";
                                                                            } else {
                                                                                echo "none";
                                                                            } ?>">
            <div class="box-header with-border">
                <?php if (isset($page_title) && $page_title == "Edit") : ?>
                    <h3 class="box-title"><i class="fa fa-exchange" aria-hidden="true"></i>&nbsp;Edit Transaction </h3>
                <?php else : ?>
                    <h3 class="box-title"><i class="fa fa-exchange" aria-hidden="true"></i>&nbsp;Add Transaction</h3>
                <?php endif; ?>
                <div class="box-tools pull-right">
                    <?php if (isset($page_title) && $page_title == "Edit") : ?>
                        <a href="<?php echo base_url('admin/banking') ?>" class="btn btn-default rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                    <?php else : ?>
                        <a href="#" class="btn btn-default btn-sm rounded cancel_btn"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <form method="post" enctype="multipart/form-data" class="row validate-form mt-20 p-30" action="<?php echo base_url('admin/banking/addtransaction') ?>" role="form" novalidate>
                <input type="hidden" name="bank_id" value="<?php if ($bank_id) {
                                                                echo $bank_id;
                                                            } else {
                                                                echo $expense[0]['bank_id'];
                                                            } ?>">
                <div class="col-lg-6 form-group">
                    <label class="col-sm-12 control-label p-0" for="example-input-normal">Select Type  <span class="text-danger">*</span></label>
                    <select class="form-control" id="type_transaction" name="type_transaction" onchange="changeFunction();" required>
                        <option value=""><?php echo trans('select') ?></option>
                        <option value="Expense" <?php echo ($expense[0]['type_transaction'] == "Expense") ? 'selected' : ''; ?>>Expense</option>
                        <option value="Income" <?php echo ($expense[0]['type_transaction'] == "Income") ? 'selected' : ''; ?>>Income</option>
                    </select>
                </div>


                <div class="col-lg-6 form-group expense_transaction" <?php if ($expense[0]['type_transaction'] == "Expense") {
                                                                            echo "style='display:block;'";
                                                                        } else {
                                                                            echo "style='display:none;'";
                                                                        } ?>>
                    <label><?php echo trans('expense-amount') ?>  <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="expense_amount" name="expense_amount" value="<?php echo html_escape($expense[0]['amount']); ?>">
                </div>

                <div class="col-lg-6 form-group income_transaction" <?php if ($expense[0]['type_transaction'] == "Income") {
                                                                        echo "style='display:block;'";
                                                                    } else {
                                                                        echo "style='display:none;'";
                                                                    } ?>>
                    <label>Income Amount  <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="income_amount" name="income_amount" value="<?php echo html_escape($expense[0]['amount']); ?>">
                </div>

                <!--<div class="col-lg-6 form-group">
                    <label><?php echo trans('tax') ?> %</label>
                    <input type="number" class="form-control" name="tax" value="<?php echo html_escape($expense[0]['tax']); ?>" >
					</div>
					
					<div class="col-lg-6 form-group">
                    <label class="col-sm-12 control-label p-0" for="example-input-normal"><?php echo trans('vendors') ?> </label>
                    <select class="form-control" name="vendor">
					<option value=""><?php echo trans('select') ?></option>
					<?php foreach ($vendors as $vendor) : ?>
					<option value="<?php echo html_escape($vendor->id); ?>" 
					<?php echo ($expense[0]['vendor'] == $vendor->id) ? 'selected' : ''; ?>>
					<?php echo html_escape($vendor->name); ?>
					</option>
					<?php endforeach ?>
                    </select>
				</div>-->

                <div class="col-lg-6 form-group expense_transaction" <?php if ($expense[0]['type_transaction'] == "Expense") {
                                                                            echo "style='display:block;'";
                                                                        } else {
                                                                            echo "style='display:none;'";
                                                                        } ?>>
                    <label class="col-sm-12 control-label p-0" for="example-input-normal"><?php echo trans('expense-category') ?> <span class="text-danger">*</span></label>
                    <select class="form-control" id="expense_category" name="expense_category">
                        <option value=""><?php echo trans('select') ?></option>
                        <?php foreach ($expense_category as $category) : ?>
                            <option value="<?php echo html_escape($category->id); ?>" <?php echo ($expense[0]['category'] == $category->id) ? 'selected' : ''; ?>>
                                <?php echo html_escape($category->name); ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="col-lg-6 form-group income_transaction" <?php if ($expense[0]['type_transaction'] == "Income") {
                                                                        echo "style='display:block;'";
                                                                    } else {
                                                                        echo "style='display:none;'";
                                                                    } ?>>
                    <label class="col-sm-12 control-label p-0" for="example-input-normal">Income Category <span class="text-danger">*</span></label>
                    <select class="form-control" id="income_category" name="income_category">
                        <option value=""><?php echo trans('select') ?></option>
                        <?php foreach ($income_category as $category) : ?>
                            <option value="<?php echo html_escape($category->id); ?>" <?php echo ($expense[0]['category'] == $category->id) ? 'selected' : ''; ?>>
                                <?php echo html_escape($category->name); ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="col-lg-6 form-group">
                    <label for="inputEmail3" class="col-sm-12 control-label p-0"><?php echo trans('date') ?>  <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control datepicker" placeholder="yyyy/mm/dd" name="date" value="<?php echo date('Y-m-d') ?>">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fa fa-calender"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 form-group">
                    <label><?php echo trans('notes') ?></label>
                    <textarea class="form-control" name="notes"><?php echo html_escape($expense[0]['notes']); ?></textarea>
                </div>

                <div class="col-lg-6 form-group">
                    <?php if (!empty($expense[0]['file'])) : ?>
                        <p><label class="label label-info"><?php echo $expense[0]['file'] ?></label></p>
                    <?php endif ?>
                    <label><?php echo trans('upload') ?></label>
                    <input class="form-control" type="file" name="file">
                </div>


                <input type="hidden" name="id" value="<?php echo html_escape($expense['0']['id']); ?>">
                <!-- csrf token -->
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                <div class="col-sm-12">
                    <?php if (isset($page_title) && $page_title == "Edit") : ?>
                        <button type="submit" class="btn btn-info btn-rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
                    <?php else : ?>
                        <button type="submit" class="btn btn-info btn-rounded pull-right"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
                    <?php endif; ?>
                </div>

            </form>
        </div>

        <?php if (isset($page_title) && $page_title != "Edit") : ?>
            <div class="list_area">

                <?php if (isset($page_title) && $page_title == "Edit") : ?>
                    <h3 class="box-title">Edit Transaction <a href="<?php echo base_url('admin/expense') ?>" class="pull-right btn btn-primary rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a></h3>
                <?php else : ?>
                    <h3 class="box-title">Transaction History
                        <a href="<?php echo base_url('admin/banking') ?>" class="pull-right btn btn-info btn-sm rounded"><i class="fa fa-reply"></i> Back</a>
                    </h3>
                <?php endif; ?>

                <div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0 over_scroll">
                    <div class="card-box">
                        <table class="table table-hover cushover  <?php if (count($expenses) > 10) {
                                                                        echo "datatable";
                                                                    } ?>" id="dg_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo trans('date') ?></th>
                                    <th>Transaction Type</th>
                                    <th><?php echo trans('client') ?></th>
                                    <th><?php echo trans('category') ?></th>
                                    <th><?php echo trans('notes') ?></th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                    <!--<th><?php echo trans('action') ?></th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $opening_bal = $this->db->where('bank_id', $bank_id)->where('type_transaction', 'Opening Balance')->get('expenses')->row()->net_amount;
                                $i = 1;
                                foreach ($expenses as $expense) :
                                ?>
                                    <tr id="row_<?php echo html_escape($expense->id); ?>">
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo my_date_show($expense->date); ?></td>

                                        <td><?php echo html_escape($expense->type_transaction); ?></td>
                                        <td style="text-transform: capitalize"><?php echo (!empty($expense->vendor_name)) ? $expense->vendor_name : $expense->customer_name; ?></td>
                                        <td><?php echo html_escape($expense->category_name); ?></td>
                                        <td><?php echo html_escape($expense->notes); ?></td>

                                        <?php if ($expense->type_transaction == "Purchase" || $expense->type_transaction == "Loan" || $expense->type_transaction == "Expense") { ?>
                                            <td><span class="badge badge-danger"><?php echo decimal_format($expense->net_amount); ?></span></td>
                                        <?php } else {  ?>
                                            <td> - </td>
                                        <?php } ?>

                                        <?php if ($expense->type_transaction == "Sale" || $expense->type_transaction == "Income" || $expense->type_transaction == "Opening Balance") { ?>
                                            <td><span class="badge badge-success"><?php echo decimal_format($expense->net_amount); ?></span></td>
                                        <?php } else {  ?>
                                            <td> - </td>
                                        <?php } ?>


                                        <?php
                                        if ($expense->type_transaction == "Sale" || $expense->type_transaction == "Income") {
                                            $balance = $opening_bal + $expense->net_amount;
                                            $opening_bal = $balance;
                                        ?>

                                            <td><?php echo $this->business->currency_symbol . '' . decimal_format($balance); ?></td>

                                        <?php
                                        } else if ($expense->type_transaction == "Purchase" || $expense->type_transaction == "Loan" || $expense->type_transaction == "Expense") {
                                            $balance = $opening_bal - $expense->net_amount;
                                            $opening_bal = $balance;
                                        ?>

                                            <td><?php echo $this->business->currency_symbol . '' . decimal_format($balance); ?></td>

                                        <?php } else if ($expense->type_transaction == "Opening Balance") {  ?>
                                            <td><?php echo $this->business->currency_symbol . '' . decimal_format($expense->net_amount); ?></td>
                                        <?php } ?>


                                        <!-- <td class="actions" width="15%">
								<?php if ($expense->type_transaction == "Sale" || $expense->type_transaction == "Purchase" || $expense->type_transaction == "Loan") {
                                ?>
									<a data-val="expense" data-id="<?php echo html_escape($expense->id); ?>" href="<?php echo base_url('admin/banking/deleteTransactionPayment/' . html_escape($expense->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a>  
									<?php } else { ?>
									
									<?php if ($expense->type_transaction != "Opening Balance") {
                                    ?>
										 <a href="<?php echo base_url('admin/banking/editTransaction/' . html_escape($expense->id)); ?>" class="on-default edit-row" data-placement="top" title="Edit"><i class="fa fa-pencil"></i></a>&nbsp; 
										<?php if ($expense->file != "") {
                                        ?>
											 <a href="<?php echo base_url('admin/expense/download/' . html_escape($expense->id)); ?>" class="on-default edit-row" data-placement="top" title="Download file"><i class="fa fa-download"></i></a>  
										<?php } ?>
									<?php } ?>
									
									 <a data-val="expense" data-id="<?php echo html_escape($expense->id); ?>" href="<?php echo base_url('admin/banking/deleteTransaction/' . html_escape($expense->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a> &nbsp;  
									
								<?php } ?>
							</td>-->
                                    </tr>

                                <?php $i++;
                                endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="9" style="text-align: right;"><span><strong>Total Income : <?php echo $this->business->currency_symbol . '' . decimal_format($income_total, 2); ?></span> </strong> <br> <span> <strong>Total Expense : <?php echo $this->business->currency_symbol . '' . decimal_format($expense_total, 2); ?></span></strong> <br> <span style="color:green;"> <strong>Avbl. Balance : <?php echo $this->business->currency_symbol . '' . decimal_format($income_total - $expense_total, 2); ?></span></strong> </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </section>
</div>
<script>
    changeFunction();

    function changeFunction() {
        var type_transaction = $("#type_transaction").val();

        if (type_transaction == 'Expense') {
            $(".expense_transaction").show();
            $(".income_transaction").hide();

            $("#expense_amount").attr('required', '');
            $("#expense_category").attr('required', '');
            $("#income_amount").removeAttr('required');
            $("#income_category").removeAttr('required');
        } else if (type_transaction == 'Income') {
            $(".income_transaction").show();
            $(".expense_transaction").hide();
            $("#income_amount").attr('required', '');
            $("#income_category").attr('required', '');
            $("#expense_amount").removeAttr('required');
            $("#expense_category").removeAttr('required');
        } else {
            $(".expense_transaction").hide();
            $(".income_transaction").hide();
            $("#expense_amount").removeAttr('required');
            $("#expense_category").removeAttr('required');
            $("#income_amount").removeAttr('required');
            $("#income_category").removeAttr('required');
        }
    }
</script>