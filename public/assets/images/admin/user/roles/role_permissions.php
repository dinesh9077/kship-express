<style>
  /* ghanshyam Css 31-08-2023	 */
  [type="checkbox"].filled-in:not(:checked)+label::after {
    height: 25px;
    width: 25px;
    background-color: transparent;
    border: 2px solid #844fdd;
    top: 0;
    z-index: 0;
    border-radius: 100%;
  }

  [type="checkbox"].filled-in:checked.chk-col-blue+label::after {
    border: 4px solid #d2b9ff;
    background: linear-gradient(90.29deg, #9E6AF6 0.25%, #6A34C3 100.78%);
    border-radius: 100%;
  }

  [type="checkbox"].filled-in:checked+label::after {
    top: 0;
    width: 25px;
    height: 25px;
    border: 2px solid #398bf7;
    background-color: #398bf7;
    z-index: 0;
  }

  [type="checkbox"].filled-in:checked+label::before {
    top: 6px;
    left: 6px;
    width: 5px;
    height: 9px;
    border-top: 1px solid transparent;
    border-left: 1px solid transparent;
  }

  .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #723ccb;
    border: none;
    color: #fff;
    font-weight: 400;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered,
  input#amount_decimal {
    color: #7843d1;
  }
</style>

<div class="content-wrapper">

  <!-- Main content -->
  <section class="content">

    <div class="card-box">
      <div class=" bus_area row">
        <div class="col-xl-3 col-lg-12">
          <div class="nav-tabs-custom profile_menu_web">
            <?php $this->load->view("admin/user/include/profile_menu"); ?>
          </div>
          <div class="nav-tabs-custom profile_menu_mobile">
            <?php $this->load->view("admin/user/include/profile_menu_1"); ?>
          </div>
        </div>
        <div class="col-xl-8 add_area" style="display: <?php if ($page_title == "Edit") {
                                                          echo "block";
                                                        } else {
                                                          echo "block";
                                                        } ?> ">

          <div class="box-header with-border">
            <h3 class="box-title"><?php echo trans('role-permissions') ?> </h3>
          </div>

          <div class="box-body">
            <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/role_management/update_permissions') ?>" role="form" novalidate>

              <div class="row">
                <?php for ($i = 1; $i <= 3; $i++) { ?>
                  <div class="col-md-4 pay-width">
                    <p class="mb-20 mt-10 fs-20">
                      <?php if ($i == 1) {
                        echo trans('admin');
                        $j = 10;
                        $value = 'subadmin';
                      } else if ($i == 2) {
                        echo trans('editor');
                        $j = 32;
                        $value = 'editor';
                      } else {
                        echo trans('viewer');
                        $j = 50;
                        $value = 'viewer';
                      }
                      ?>
                    </p>

                    <div class="permission_list">
                      <?php $f = 4;
                      foreach ($features as $feature) : ?>
                        <div class="form-group">
                          <input type="checkbox" id="md_checkbox_<?php echo $f * $j; ?>" class="filled-in chk-col-blue view_only_<?php echo $i; ?>" value="<?php echo $feature->id; ?>" name="features_<?php echo $i; ?>[]" <?php if (check_role_assign_features($value, $feature->id) == 1) {
                                                                                                                                                                                                                              echo "checked";
                                                                                                                                                                                                                            } ?> <?php if ($i == 3) {
                                echo "disabled";
                              } ?>>
                          <label for="md_checkbox_<?php echo $f * $j; ?>"> <?php echo html_escape($feature->name); ?></label>
                        </div>
                      <?php $f++;
                      endforeach ?>
                    </div>
                  </div>
                <?php } ?>
              </div>


              <input type="hidden" name="id" value="<?php echo html_escape($user['0']['id']); ?>">
              <!-- csrf token -->
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

              <div class="row mt-10">
                <div class="col-sm-12">
                  <button type="submit" class="btn btn-info rounded pull-left"><i class="fa fa-check"></i> <?php echo trans('update') ?></button>
                </div>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>

  </section>
</div>