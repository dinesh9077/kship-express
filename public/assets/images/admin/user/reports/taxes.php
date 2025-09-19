

<style type="text/css">
/*	.card-box{
		padding: 50px;
	}*/
</style>
<div class="content-wrapper">
	<section class="content">
		<div class="card-box">
			<div class="">
				<div class="card-box">
					<div class="row">

						<div class="col-md-12">
							<div class="d-flex justify-content-between align-items-center">
								<h2 style="font-size: 26px"><i class="fa fa-bar-chart" aria-hidden="true"></i> <?php echo trans('sales-tax-report') ?></h2>
								<div class="add-btn">
									<a href="#" class="btn btn-default btn-rounded print"><i class="fa fa-print"></i> <?php echo trans('print') ?> </a>
								</div>
							</div>

							<form method="GET" class="sort_report_form validate-form" action="<?php echo base_url('admin/reports/sales_tax') ?>">
								<div class="reprt-box">
									<div class="row">
										<div class="col-xl-12 text-right">
											<button type="submit" class="btn btn-info btn-report"><i class="fa fa-search"></i> <?php echo trans('show-report') ?></button>
										</div>
									</div>
									<div class="row">
										<div class="col-xl-12">
											<div class="row align-items-end">
										<!-- <div class="col-xl-4 mt-10">
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
											<select class="form-control single_select report_type" required name="report_type" style="width: 100%">
												<option value=""><?php echo trans('report-types') ?></option>
												<option <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 1) ? 'selected' : ''; ?> value="1"><?php echo trans('paid-unpaid') ?></option>
												<option <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 2) ? 'selected' : ''; ?> value="2"><?php echo trans('paid') ?></option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>


		<div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-50 p-0 print_area">
			<div class="card-box">
				<table class="table cushover table-hover">
					<thead>
						<tr>
							<th><?php echo trans('tax') ?></th>
							<th><?php echo trans('sales-product-tax') ?></th>
							<th><?php echo trans('tax-amount-sale') ?></th>
							<th><?php echo trans('purchase-subject') ?></th>
							<th><?php echo trans('tax-amount-purchase') ?></th>
							<th><?php echo trans('tax-owing') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sale_tax = $purchase_tax = $total_tax = 0;
						foreach ($taxes as $tax => $tax_details) {
							$total_sale_amount = isset($tax_details['total_sale_amount']) ? $tax_details['total_sale_amount'] : 0.00;
							$tax_sale_amount = isset($tax_details['tax_sale_amount']) ? $tax_details['tax_sale_amount'] : 0.00;
							$total_purchase_amount = isset($tax_details['total_purchase_amount']) ? $tax_details['total_purchase_amount'] : 0.00;
							$tax_purchase_amount = isset($tax_details['tax_purchase_amount']) ? $tax_details['tax_purchase_amount'] : 0.00;
							$tota_tax_amount = $tax_sale_amount - $tax_purchase_amount;
							?>
							<tr>
								<td><?php echo $tax; ?></td>
								<td><?php echo $this->business->currency_symbol . ' ' . number_format($total_sale_amount,2); ?></td>
								<td><?php echo $this->business->currency_symbol . ' ' . number_format($tax_sale_amount,2); ?></td>
								<td><?php echo $this->business->currency_symbol . ' ' . number_format($total_purchase_amount,2); ?></td>
								<td><?php echo $this->business->currency_symbol . ' ' . number_format($tax_purchase_amount,2); ?></td>
								<td><?php echo $this->business->currency_symbol; ?> <?php echo number_format($tota_tax_amount,2); ?></td>
							</tr>
							<?php
							$sale_tax += $tax_sale_amount;
							$purchase_tax += $tax_purchase_amount;
							$total_tax += $tota_tax_amount;
						}
						?>
						<tr>
							<td><strong>Total </strong></td>
							<td></td>
							<td><strong><?php echo $this->business->currency_symbol . ' ' . number_format($sale_tax, 2); ?> </strong></td>
							<td></td>
							<td><strong><?php echo $this->business->currency_symbol . ' ' . number_format($purchase_tax, 2); ?></strong></td>
							<td><strong><?php echo $this->business->currency_symbol . ' ' . number_format($total_tax, 2); ?></strong></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
</section>
</div>