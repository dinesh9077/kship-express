<style>
	.content-wrapper,
	.main-footer {
		margin: 0px !important;
	}

	.box-body {
		padding: 25px 25px 25px;
		-ms-flex: 1 1 auto;
		flex: 1 1 auto;
	}

	@media only screen and (max-width: 767px) {
		.content-wrapper {
			width: 100% !important;
		}
	}
</style>
<?php if ($billing_type == 'monthly') : ?>
	<?php
	if (settings()->enable_discount == 1) {
		$price = get_discount($package->monthly_price, $package->dis_month);
	} else {
		$price = round($package->monthly_price);
	}
	$frequency = trans('per-month');
	$billing_type = 'monthly';
	?>
<?php else : ?>
	<?php
	if (settings()->enable_discount == 1) {
		$price = get_discount($package->price, $package->dis_year);
	} else {
		$price = round($package->price);
	}
	$frequency = trans('per-year');
	$billing_type = 'yearly';
	?>
<?php endif ?>
<div class="content-wrapper">
	<!-- Main content -->
	<section class="content">
		<div class="container">
			<div class="text-center">
				<h1 class="box-title">Upgrade your package</h1>
			</div>
			<div class="row justify-content-center" style="margin-top: 2%">
				<div class="col-lg-4">
					<div class="box">
						<!--<div class="box-header with-border">-->

						<!--</div>-->
						<div class="box-body">
							<h3>Package Details</h3>
							<!--<h5>Package Details</h5>-->
							<h5 style="text-transform: capitalize;"><?php echo $slug; ?> - <?php echo $billing_type; ?></h5>
							<hr>
							<br>
							<h5>Total Due Today: <span style="float: right; text-align: right"><?php echo currency_to_symbol(settings()->currency); ?><?php echo round($price); ?> <br>
									<p class="text-muted" style="font-weight: 400">(Inc. all Taxes)</p>
								</span></h5>
							<br>
							<h6 class="text-center text-muted">This is the prorated amount due today for your plan change.</h6>
							<br>
							<br>
							<div class="text-center">
								<a class="btn btn-info rounded btn-sm" href="<?php echo base_url('admin/subscription/upgrade/' . $slug . '/' . $billing_type . '/1') ?>">Confirm upgrade package</a>
								<br>
								<a class="btn btn-default mt-10" href="<?php echo base_url('admin/subscription/') ?>">Cancel</i></a>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</section>
</div>
<!-- Facebook Pixel Code -->
<script>
	! function(f, b, e, v, n, t, s) {
		if (f.fbq) return;
		n = f.fbq = function() {
			n.callMethod ?
				n.callMethod.apply(n, arguments) : n.queue.push(arguments)
		};
		if (!f._fbq) f._fbq = n;
		n.push = n;
		n.loaded = !0;
		n.version = '2.0';
		n.queue = [];
		t = b.createElement(e);
		t.async = !0;
		t.src = v;
		s = b.getElementsByTagName(e)[0];
		s.parentNode.insertBefore(t, s)
	}(window, document, 'script',
		'https://connect.facebook.net/en_US/fbevents.js');
	fbq('init', '631435904807252');
	fbq('track', 'PageView');
</script>
<noscript>
	<img height="1" width="1" src="https://www.facebook.com/tr?id=631435904807252&ev=PageView
	&noscript=1" />
</noscript>
<!-- End Facebook Pixel Code -->
<script>
	var price = '<?php echo round($price); ?>';
	var currency = '<?php echo settings()->currency; ?>';
	fbq('track', 'InitiateCheckout', {
		value: price,
		currency: currency,
	});
</script>