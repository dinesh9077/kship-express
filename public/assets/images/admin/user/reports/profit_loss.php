<style type="text/css">
  .card-box {
    padding: 3rem;
  }
</style>

<div class="content-wrapper">
  <section class="content">
    <div class="card-box">
      <div class="">
      <div class="row">
        <div class="col-md-12">
          <div class="d-flex justify-content-between align-items-center">
            <h2 style="font-size: 26px"><i class="fa fa-balance-scale"></i> <?php echo trans('profit-loss') ?> </h2>
            <div class="add-btn mb-25">
              <a href="#" class="btn btn-default btn-rounded print"><i class="fa fa-print"></i> <?php echo trans('print') ?> </a>
            </div>
          </div>

          <form method="GET" class="sort_report_form validate-form" action="<?php echo base_url('admin/reports/profit_loss') ?>">
            <div class="reprt-box mb-10 pl-15">
              <div class="row">
                <div class="col-lg-12 text-right">
                  <button type="submit" class="btn btn-info btn-report"><i class="fa fa-search"></i> <?php echo trans('show-report') ?></button>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                  <div class="row align-items-end">
                    <!-- <div class="col-lg-4 mt-10">
                      <p class="m-0">Date Range</p>
                    </div> -->
                    <div class="col-lg-4 mt-10 pro_date">
                      <p class="m-0">Date Range</p>
                      <div class="input-group">
                        <input type="text" class="inv-dpick form-control datepicker" placeholder="From" name="start" value="<?php if (isset($_GET['start'])) {
                          echo $_GET['start'];
                        } ?>" autocomplete="off">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      </div>
                    </div>
                    <div class="col-lg-4 mt-10 pro_date">
                      <div class="input-group">
                        <input type="text" class="inv-dpick form-control datepicker" placeholder="To" name="end" value="<?php if (isset($_GET['end'])) {
                          echo $_GET['end'];
                        } ?>" autocomplete="off">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      </div>
                    </div>
                    <div class="col-lg-4 mt-10 pro_date">
                      <p class="m-0">Report Type</p>
                      <select class="form-control single_select report_type" required name="report_type" style="width: 100%;">
                        <option value=""><?php echo trans('report-types') ?></option>
                        <option <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 1) ? 'selected' : ''; ?> value="1"><?php echo trans('paid-unpaid') ?> (<?php echo trans('paid-unpaid-inv-bill') ?>)</option>
                        <option <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 2) ? 'selected' : ''; ?> value="2"><?php echo trans('paid') ?> (<?php echo trans('paid-inv-bill') ?>)</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="print_area">

        <div class="profit-and-loss-report mt-50">
          <div class="profit-and-loss-single">
            <p class="mb-0"><?php echo trans('income') ?></p>
            <h1 class="fs-40 text-dark"><?php echo $this->business->currency_symbol . ' ' . decimal_format($total_incomes, 2); ?></h1>
          </div>
          <div class="seperater-minus profit-and-loss-seperater">-</div>

          <div class="profit-and-loss-single">
            <p class="mb-0"><?php echo trans('expenses') ?></p>
            <h1 class="fs-40 text-dark"><?php echo $this->business->currency_symbol . ' ' . decimal_format($total_expenses, 2); ?></h1>
          </div>
          <div class="seperater-minus profit-and-loss-seperater">=</div>

          <div class="profit-and-loss-single">
            <p class="mb-0"><?php echo trans('net-profit') ?></p>
            <?php if ($profitloss < 0) : ?>
              <h1 class="fs-40 text-danger"><?php echo $this->business->currency_symbol . ' ' . decimal_format($profitloss, 2); ?></h1>
            <?php else : ?>
              <h1 class="fs-40 text-success"><?php echo $this->business->currency_symbol . ' ' . decimal_format($profitloss, 2); ?></h1>
            <?php endif; ?>
          </div>
        </div>

        <div class="profit-and-loss-report mt-30 bb-2">
          <div>
            <h4 class="mb-0"><?php echo trans('profit-loss') ?> <?php echo trans('reports') ?></h4>
          </div>

          <div class="mb-10">
            <strong>
              <p class="p-0 m-0">&emsp;<?php if (isset($_GET['start'])) {
                echo $_GET['start'];
              } ?></p>
              <p class="p-0 m-0">to <?php if (isset($_GET['end'])) {
                echo $_GET['end'];
              } ?></p>
            </strong>
          </div>
        </div>

        <div class="profit-and-loss-report mt-20">
          <div>
            <h4 class="m-0 fwn"><?php echo trans('income') ?></h4>
          </div>

          <div class="mb-10">
            <p class="p-0 m-0 fs-20"><?php echo $this->business->currency_symbol . ' ' . decimal_format($total_incomes, 2); ?></p>
          </div>
        </div>

        <div class="profit-and-loss-report pt-10 pb-10 bb-2">
          <div>
            <h4 class="m-0 fwn"><?php echo trans('expense') ?></h4>
          </div>

          <div class="mb-10">
            <p class="p-0 m-0 fs-20"><?php echo $this->business->currency_symbol . ' ' . decimal_format($total_expenses, 2); ?></p>
          </div>
        </div>

        <div class="profit-and-loss-report pt-10">
          <div>
            <h4 class="m-0 fwn"><?php echo trans('net-profit') ?></h4>
          </div>

          <div class="mb-10">
            <?php if ($profitloss < 0) : ?>
              <p class="fs-20 p-0 m-0 text-danger"><?php echo $this->business->currency_symbol . ' ' . decimal_format($profitloss, 2); ?></p>
            <?php else : ?>
              <p class="fs-20 p-0 m-0 text-success"><?php echo $this->business->currency_symbol . ' ' . decimal_format($profitloss, 2); ?></p>
            <?php endif; ?>
          </div>
        </div>

      </div>

    </div>
    </div>
  </section>
</div>