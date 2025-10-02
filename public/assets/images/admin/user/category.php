<div class="content-wrapper">

  <!-- Main content -->
  <section class="content">

    <div class="col-lg-12 m-auto box add_area" style="display: <?php if ($page_title == "Edit") {
                                                                  echo "block";
                                                                } else {
                                                                  echo "none";
                                                                } ?>">
      <div class="box-header d-flex f-no with-border justify-content-between bg-light1 align-items-center">
        <?php if (isset($page_title) && $page_title == "Edit") : ?>
          <h3 class="box-title"><i class="flaticon-folder-1"></i>&nbsp;<?php echo trans('edit-category') ?></h3>
        <?php else : ?>
          <h3 class="box-title"><i class="flaticon-folder-1"></i>&nbsp;<?php echo trans('add-new-category') ?> </h3>
        <?php endif; ?>

        <div class="box-tools pull-right">
          <?php if (isset($page_title) && $page_title == "Edit") : ?>
            <a href="<?php echo base_url('admin/category') ?>" class="btn btn-info btn-rounded pull-right"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
          <?php else : ?>
            <a href="#" class="btn btn-info btn-rounded pull-right cancel_btn"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
          <?php endif; ?>
        </div>
      </div>

      <div class="box-body">
        <form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/category/add') ?>" role="form" novalidate>

          <div class="form-group">
            <label><?php echo trans('category-name') ?>  <span class="text-danger">*</span></label>
            <input type="text" class="form-control" required name="name" value="<?php echo html_escape($category[0]['name']); ?>">
          </div>

          <div class="row cata_re m-t-30">
            <div class="col-sm-2 text-left">
              <div class="radio radio-info radio-inline">
                <input type="radio" id="inlineRadio1" required value="1" name="type" <?php if ($category[0]['type'] == 1) {
                                                                                        echo "checked";
                                                                                      } ?>>
                <label for="inlineRadio1"> <?php echo trans('income') ?> </label>
              </div>
            </div>
            <div class="col-sm-1 text-left">
              <div class="radio radio-info radio-inline">
                <input type="radio" id="inlineRadio2" required value="2" name="type" <?php if ($category[0]['type'] == 2) {
                                                                                        echo "checked";
                                                                                      } ?>>
                <label for="inlineRadio2"> <?php echo trans('expense') ?> </label>
              </div>
            </div>
          </div>

          <input type="hidden" name="id" value="<?php echo html_escape($category['0']['id']); ?>">
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
      <div class="list_area">


        <div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-10 p-0">
          <div class="card-box">
            <div class="bg-light1">
              <?php if (isset($page_title) && $page_title == "Edit") : ?>
                <h3 class="box-title"><?php echo trans('edit-category') ?></h3>
                <div class="add-btn">
                  <a href="<?php echo base_url('admin/portfolio_category') ?>" class="btn btn-primary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                </div>
              <?php else : ?>
                <div class="d-flex justify-content-between align-items-center loan_re">
                  <h3 class="box-title"><i class="flaticon-folder-1"></i> <?php echo trans('categories') ?></h3>
                  <div class="add-btn add_new">
                    <a href="#" class="btn btn-info rounded btn-sm add_btn"><i class="fa fa-plus"></i> <?php echo trans('add-new-category') ?></a>
                  </div>
                </div>
              <?php endif; ?>
            </div>

            <table class="table table-hover cushover <?php if (count($categories) > 10) {
                                                        echo "datatable";
                                                      } ?>" id="dg_table">
              <thead>
                <tr>
                  <th>#</th>
                  <th><?php echo trans('name') ?></th>
                  <th><?php echo trans('type') ?></th>
                  <th><?php echo trans('action') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1;
                foreach ($categories as $cat) : ?>
                  <tr id="row_<?php echo html_escape($cat->id); ?>">

                    <td><?php echo $i; ?></td>
                    <td style="text-transform: capitalize"><?php echo html_escape($cat->name); ?></td>
                    <td>
                      <?php if ($cat->type == 1) : ?>
                        <span class="label label-success"><?php echo trans('income') ?></span>
                      <?php else : ?>
                        <span class="label label-primary"><?php echo trans('expense') ?></span>
                      <?php endif ?>
                    </td>

                    <td class="actions" width="15%">
                      <a href="<?php echo base_url('admin/category/edit/' . html_escape($cat->id)); ?>" class="on-default edit-row" data-placement="top" title="Edit"><i class="fa fa-pencil"></i></a> &nbsp;

                      <a data-val="Category" data-id="<?php echo html_escape($cat->id); ?>" href="<?php echo base_url('admin/category/delete/' . html_escape($cat->id)); ?>" class="on-default remove-row delete_item" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a>
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