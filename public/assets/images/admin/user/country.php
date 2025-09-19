<div class="content-wrapper">
  <style>
    ::-webkit-input-placeholder {
      /* Edge */
      color: #c9c9c9 !important;
    }

    :-ms-input-placeholder {
      /* Internet Explorer 10-11 */
      color: #c9c9c9 !important;
    }

    ::placeholder {
      color: #c9c9c9 !important;
    }
  </style>
  <!-- Main content -->
  <section class="content">


    <div class="card-box">
      <div class="row">
        <div class="col-xl-3 col-lg-12">
          <div class="nav-tabs-custom profile_menu_web">
            <?php include "include/profile_menu.php"; ?>
          </div>
          <div class="nav-tabs-custom profile_menu_mobile">
            <?php include "include/profile_menu_1.php"; ?>
          </div>
        </div>

        <div class="col-xl-9">
          <div class="m-auto box add_area mt-50" style="display: <?php if ($page_title == "Edit") {
                                                                    echo "block";
                                                                  } else {
                                                                    echo "none";
                                                                  } ?>">
            <div class="box-header d-flex align-items-center justify-content-between f-no">
              <?php if (isset($page_title) && $page_title == "Edit") : ?>
                <h3><?php echo trans('edit') ?></h3>
              <?php else : ?>
                <h3>Add New </h3>
              <?php endif; ?>

              <div class="box-tools pull-right">
                <?php if (isset($page_title) && $page_title == "Edit") : ?>
                  <a href="<?php echo base_url('admin/country') ?>" class="pull-right btn btn-info rounded btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                <?php else : ?>
                  <a href="#" class="pull-right btn btn-info rounded btn-sm cancel_btn"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                <?php endif; ?>
              </div>
            </div>

            <div class="box-body">
              <form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/country/add') ?>" role="form" novalidate>

                <div class="form-group">
                  <label><?php echo trans('name') ?> <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required name="name" placeholder="Country <?php echo trans('name') ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('country-code') ?> <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required name="code" placeholder="<?php echo trans('country-code') ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('currency-name') ?> <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required name="currency_name" placeholder="<?php echo trans('currency-name') ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('currency-symbol') ?> <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required name="currency_symbol" placeholder="<?php echo trans('currency-symbol') ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('currency-code') ?> <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required name="currency_code" placeholder="<?php echo trans('currency-code') ?>">
                </div>



                <input type="hidden" name="id" value="<?php echo html_escape($country['0']['id']); ?>">
                <!-- csrf token -->
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">


                <div class="row mt-20">
                  <div class="col-sm-12">
                    <?php if (isset($page_title) && $page_title == "Edit") : ?>
                      <button type="submit" class="btn btn-info rounded pull-left"><i class="fa fa-check"></i> <?php echo trans('save-changes') ?></button>
                    <?php else : ?>
                      <button type="submit" class="btn btn-info rounded pull-left"><i class="fa fa-check"></i> <?php echo trans('save') ?></button>
                    <?php endif; ?>
                  </div>
                </div>

              </form>

            </div>
          </div>

          <?php if (isset($page_title) && $page_title != "Edit") : ?>

            <div class="list_area mt-30 container">

              <div class="box-header d-flex align-items-center justify-content-between f-no">
                <?php if (isset($page_title) && $page_title == "Edit") : ?>
                  <h3><?php echo trans('edit') ?> </h3>
                  <a href="<?php echo base_url('admin/country') ?>" class="pull-right btn btn-primary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                <?php else : ?>
                  <h3><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;<?php echo trans('countries') ?> </h3>
                  <a href="#" class="pull-right btn btn-info rounded btn-sm add_btn"><i class="fa fa-plus"></i> <?php echo trans('create-new') ?></a>
                <?php endif; ?>
              </div>

              <div class="box-body">
                <div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0">
                  <table class="table table-hover datatable" id="dg_table">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th><?php echo trans('name') ?></th>
                        <th><?php echo trans('currency') ?></th>
                        <th><?php echo trans('action') ?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $i = 1;
                      foreach ($countries as $row) : ?>
                        <tr id="row_<?php echo html_escape($row->id); ?>">

                          <td><?php echo $i; ?></td>
                          <td><?php echo html_escape($row->name); ?> - <?php echo html_escape($row->code); ?></td>
                          <td><?php echo html_escape($row->currency_symbol); ?> - <?php echo html_escape($row->currency_code); ?> - <?php echo html_escape($row->currency_name); ?></td>

                          <td class="actions" width="15%">
                            <?php if (is_admin() || user()->id == $row->user_id) : ?>
                              <a href="<?php echo base_url('admin/country/edit/' . html_escape($row->id)); ?>" class="on-default edit-row" data-placement="top" title="Edit"><i class="fa fa-pencil"></i></a> &nbsp;

                              <a data-val="country" data-id="<?php echo html_escape($row->id); ?>" href="<?php echo base_url('admin/country/delete/' . html_escape($row->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a>
                            <?php endif ?>
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
        </div>
      </div>

    </div>
  </section>
</div>