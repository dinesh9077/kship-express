<div class="content-wrapper">
    <section class="content"> 
        <div class="row cus"> 
            <div class="col-lg-3 col-md-6 col-xs-12">
                <div class="card counts">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round align-self-center bg-warning"><i class="flaticon-document"></i></div>
                            <div class="ml-20 align-self-center">
                                <h2 class="m-b-0"><?php echo number_format($bills, 0) ?></h2>
                                <h4 class="text-muteds m-b-0">Bills</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 col-xs-12">
                <div class="card counts">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round align-self-center round-blue"><i class="flaticon-accept"></i></div>
                            <div class="ml-20 align-self-center">
                                <h2 class="m-b-0"><?php echo number_format($invoices, 0) ?></h2>
                                <h4 class="text-muteds m-b-0"><?php echo trans('invoices') ?></h4>
							</div>
						</div>
					</div>
				</div>
			</div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6 col-xs-12">
                <div class="card counts">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round align-self-center round-success"><i class="flaticon-legal-paper"></i></div>
                            <div class="ml-20 align-self-center">
                                <h2 class="m-b-0"><?php echo number_format($estimates, 0) ?></h2>
							<h4 class="text-muteds m-b-0"><?php echo trans('estimates') ?></h4></div>
						</div>
					</div>
				</div>
			</div> 
            <div class="col-lg-3 col-md-6 col-xs-12">
                <div class="card counts">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round align-self-center bg-danger"><i class="flaticon-clock"></i></div>
                            <div class="ml-20 align-self-center">
                                <h2 class="m-b-0"><?php echo number_format($deliveryChallan, 0) ?></h2>
							<h4 class="text-muteds m-b-0">Delivery Challan</h4></div>
						</div>
					</div>
				</div>
			</div>
            <!-- Column -->
		</div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Last Login</h3>
					</div>
                    <div class="box-body">
                        <div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive mt-20 p-0">
							<table class="table table-hover <?php if(count($last_logins) > 0){ echo 'datatable'; } ?> cushover">
								<thead>
									<tr>
										<th>#</th> 
										<th><?php echo trans('name') ?></th> 
										<th>Ip Address</th>
										<th><?php echo trans('status') ?></th>
										<th>Login At</th>
										<th>Logout At</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$i= 1; 
										foreach($last_logins as $last_login):
									?>
									<tr>
										<td><?php echo $i; ?></td>
										<td><?php echo $last_login->name; ?></td>
										<td><?php echo $last_login->ip_address; ?></td>
										<?php if($last_login->status == 1): ?>
										<td>Active</td>
										<?php else: ?>
										<td>In-Active</td>
										<?php endif ?>
										<td><?php echo $last_login->created_at; ?></td>
										<td><?php echo $last_login->updated_at; ?></td>
									</tr>
									<?php endforeach ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
			</div>
			
		</div>
	</section>
</div>