<style>
    hr {
        border-top-color: #dcdfe0;
    }
    .bg-light {
        background: #f8f9fa !important;
        border-radius: 10px !important;
    }
    .bg-light h1 {
        font-size: 22px;
        padding: 6px 20px;
    }
</style>
<div class="content-wrapper">
	
	<!-- Main content -->
	<section class="content">
      <div class="card-box">
        <div class="">
            <div class="bg-light">
                <h1 style="margin-bottom: 30px;">Reports</h1>  
            </div>
            <div class="card border-radius bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-12 ">
                            <div class="box-title">Financial statements</div>
                            <p class="m-0">Using these core statements, you can better understand your business' financial health.</p>    
                        </div>
                        <div class="col-md-6 col-sm-12 ">
                            <a href="<?php echo base_url('admin/reports/profit_loss?end='.date('Y-m-d').'&start='.date('Y').'-01-01&report_type=1') ?>" class="reports_links">
                                <p class="heading_subtitle m-0">
                                   Profit & Loss (Income Statement) 
                                   <span><i class="fa fa-angle-right pull-right"></i></span>
                               </p>
                               <p class="m-0">Provides a summary of revenues and expenses in a given period and shows you net profit.</p>
                           </a>
                       </div>
                   </div>
               </div>
           </div>

           <div class="card border-radius bg-light">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12 ">
                        <div class="box-title">Taxes</div>
                        <p class="m-0">Using your collection of and payment of sales taxes, we provide you with a detailed overview of the taxes you owe.</p>    
                    </div>
                    <div class="col-md-6 col-sm-12 ">
                        <a href="<?php echo base_url('admin/reports/sales_tax?end='.date('Y-m-d').'&start='.date('Y').'-01-01&report_type=1') ?>" class="reports_links">
                            <p class="heading_subtitle m-0">
                              Sales Tax Report
                              <span><i class="fa fa-angle-right pull-right"></i></span>
                          </p>
                          <p class="m-0">Here is a breakdown of how sales taxes were collected and how sales taxes were paid on purchases.</p>
                      </a>
                  </div>
              </div>
          </div>
      </div>

      <div class="card border-radius bg-light">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-sm-12 ">
                    <div class="box-title">Income and Purchases</div>
                    <p class="m-0">Using your collection of and payment of sales taxes, we provide you with a detailed overview of the taxes you owe.</p>    
                </div>
                <div class="col-md-6 col-sm-12 ">
                    <a href="<?php echo base_url('admin/reports/customers?end='.date('Y-m-d').'&start='.date('Y').'-01-01&report_type=1') ?>" class="reports_links">
                        <p class="heading_subtitle m-0">
                            Income by Customer
                            <span><i class="fa fa-angle-right pull-right"></i></span>
                        </p>
                        <p class="m-0">Breakdown of paid and unpaid income for every customer.</p>
                    </a>
                    <hr>
                    <a href="<?php echo base_url('admin/reports/vendors?end='.date('Y-m-d').'&start='.date('Y').'-01-01&report_type=1') ?>" class="reports_links">
                        <p class="heading_subtitle m-0">
                          Purchases by Vendor
                          <span><i class="fa fa-angle-right pull-right"></i></span>
                      </p>
                      <p class="m-0">Breakdown of purchases and bills from every vendor.</p>
                  </a>
              </div>
          </div>
      </div>
  </div>

  <div class="card border-radius bg-light">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-sm-12 ">
                <div class="box-title">Customer</div>
            </div>
            <div class="col-md-6 col-sm-12 ">
                <a href="<?php echo base_url('admin/reports/customer_statement') ?>" class="reports_links">
                    <p class="heading_subtitle m-0">
                        Customer Statement Report
                        <span><i class="fa fa-angle-right pull-right"></i></span>
                    </p>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card border-radius bg-light">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-sm-12 ">
                <div class="box-title">General Reports</div>
            </div>
            <div class="col-md-6 col-sm-12 ">
                <a href="<?php echo base_url('admin/reports') ?>" class="reports_links">
                    <p class="heading_subtitle m-0">
                        All Reports
                        <span><i class="fa fa-angle-right pull-right"></i></span>
                    </p>
                </a>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</section>
</div>