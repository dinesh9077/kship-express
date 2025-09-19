<!DOCTYPE html>
<html lang="en">
	
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- Favicon icon -->
		<title>Tracking - {{$order->awb_number}}</title>
		<!-- Import Google Fonts -->
		<style>
        @page {
            margin: 0;
            padding: 0;
        }
        
        @font-face {
            font-family: 'Roboto';
            font-stretch: normal;
        }
        body {
            margin: 0;
            padding: 0;
        }
        </style>
	</head>
	
	<body style="background-color: #fff;">
	    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin: 0 auto; padding: 0 0;">
            <tr>
                <td>
        	    <table style="border: 2px solid; border-bottom: 2px solid" width="100%">
                	<tr>
                		<td style="border: 2px;text-align:center; margin-top:10px" width="25%">
                			<img src="{{asset('storage/shipping-logo/'.$shipping->logo)}}"
                				style="width:300px;height: 77px;">
                		</td>
                		<td style="border: 1px; border-left: 2px solid; text-align: center" width="30%; ">
                
                			<h2 style="font-family:roboto; font-weight: bold !important;">ORDER NO </h2>
                			<h2 style="">{{$order->order_prefix}}</h2>
                		</td>
                	</tr>
                </table>
                <table style="border-bottom: 2px solid;" width="100%">
                	<tr>
                		<td style="text-align: center" width="25%">
                			<h2 style="text-transform: uppercase; margin-block-end: 0">{{$order->shipping_mode}}</h2>
                			<h2 style="margin-block-start: 0">Rs. {{$order->total_amount}}</h2>
                		</td>
                
                		<td style="border: 1px; border-left: 2px solid;  text-align: center; width: 100%;font-size: 20px;margin-top: 4px;font-weight: bold"
                			width="75%">
                			{{$shipping->courier_name}}
                			<img style="min-height: 75px; max-height: 81px; width: 51%; margin-top: 6px;"
                				src="https://barcode.tec-it.com/barcode.ashx?data={{$order->awb_number}}&multiplebarcodes=false&translate-esc=true&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&qunit=Mm&quiet=0"
                				alt='' />
                		</td>
                	</tr>
                </table>
                
                <table style="border-bottom: 2px solid;" width="100%">
                	<tr>
                
                		<td style="text-align: start;max-width: 100%;font-weight: 900;line-height: 24px;padding: 30px" width="25%">
                			<p style="margin-block-start: 0; margin-block-end: 0;font-size: 26px">Date: &nbsp;<span
                					style="font-weight: 700">{{$order->order_date}}</span></p>
                			<p style="margin-block-start: 0; margin-block-end: 0;font-size: 30px">
                				Weight: &nbsp;
                				<span style="font-weight: 700">{{$order->applicable_weight}}</span>
                			</p>
                
                    
                		</td>
                
                		<td style="border: 1px; border-left: 0px solid;  text-align: center" width="70%">
                
                			<p
                				style="text-align: center;font-size: 30px;width: 100%;float: left;margin-block-start: 0;font-weight: bold;">
                				Dimension</p>
                			<p style="width: 100%; text-align: center; font-size: 20px; font-weight: 700; margin-top: 1px;">
                				Length: &nbsp; {{$order->length}} &nbsp; &nbsp; Width: &nbsp;{{$order->width}} &nbsp; &nbsp;Height:
                				&nbsp;{{$order->height}} </p>
                
                		</td>
                	</tr>
                </table>
                
                <?php
                $pickup_address = $vendorAddress->address . ' ' . $vendorAddress->city . ' ' . $vendorAddress->state . ' ' . $vendorAddress->zip_code . ',' . $vendorAddress->country;
                $customer_address = $customerAddr->address . ' ' . $customerAddr->city . ' ' . $customerAddr->state . ' ' . $customerAddr->zip_code . ',' . $customerAddr->country;
                ?>
                
                <table style="border-bottom: 2px solid;max-height: 30%;min-height: 1%" width="100%">
                	<tr>
                		<td>
                			<p style="font-size: 24px;font-weight: 700;text-align:center">Note - No Open Delivery Allowed</p>
                		</td>
                
                	</tr>
                </table>
                <table style="border-bottom: 2px solid;max-height: 30%" width="100%">
                	<tr>
                		<td style="padding: 30px">
                			<b style="font-size: 22px;"> Shipping Address :</b><br>
                			<h2 style=" margin-block-start: 5px; margin-block-end: 0; text-transform: capitalize; font-weight: 700">
                				{{$customer->first_name.' '.$customer->last_name}} </h2>
                			<p style="font-size: 20px; margin-block-start: 0; margin-block-end: 0; font-weight: 700">
                				{{$customer_address}} </p>
                			<p style="font-size: 24px; margin-block-start: 0; margin-block-end: 0; font-weight: 500">Mobile :
                				{{$customer->mobile}}</p>
                		</td>
                
                	</tr>
                </table>
                
                <table style="border-bottom: 2px solid;max-height: 30%" width="100%">
                	<tr>
                		<td style="padding: 30px">
                			<b style="font-size: 22px;"> Pickup and Return Address: </b><br>
                			<h2 style=" margin-block-start: 5px; margin-block-end: 0; text-transform: capitalize; font-weight: 500 ">
                				{{$vendor->first_name.' '.$vendor->last_name}}</h2>
                			<p style="font-size: 20px; margin-block-start: 0; margin-block-end: 0; font-weight: 700">
                				{{$pickup_address}} </p>
                			<p style="font-size: 24px; margin-block-start: 0; margin-block-end: 0; font-weight: 500"><b>Mobile:</b>
                				{{$vendor->mobile}}</p>
                		</td>
                	</tr>
                </table>
        		</td>
        	</tr>
        </table>
				
	</body>
		
</html>
<!--<button class='button -dark center no-print' onClick="window.print();" style="font-size:16px;">Print label &nbsp;&nbsp; <li class="fa fa-print"></i></button>-->