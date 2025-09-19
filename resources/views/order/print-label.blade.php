<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Tracking - {{$order->awb_number}}</title>
    <link type='text/css' href="{{asset('assets/css/label_custom.css')}}" rel='stylesheet' />

    <style>
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 400;
            src: url('data:font/ttf;base64,[Your Base64 Encoded Regular Font Here]') format('truetype');
        }

        @font-face {
            font-family: 'Poppins';
            font-style: bold;
            font-weight: 700;
            src: url('data:font/ttf;base64,[Your Base64 Encoded Bold Font Here]') format('truetype');
        }

        @page {
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            color: #333;
        }
        .order-label {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 15px;
             margin-top: 20px;
        }
        .order-label table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .order-label table, .order-label th, .order-label td {
            border: 2px solid #000;
        }
        .order-label th, .order-label td {
            padding: 10px;
            text-align: left;
        }
        .order-label th {
            background-color: #f4f4f4;
        }
        .order-label h2 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            text-align: center;
        }
        .order-label .barcode img {
            display: block;
            margin: 0 auto;
        }
        .order-label .note {
            text-align: center;
            font-weight: bold;
        }
        .order-label .address {
            font-weight: 700;
        }
    </style>
    </style>
</head>
<body>
    <div id="page-wrap">
        <div class="order-label">
        <!-- Order Number and Shipping Mode -->
        <table>
            <tr>
                <td style="width: 30%;">
                    <img src="{{asset('storage/shipping-logo/'.$shipping->logo)}}" alt="Shipping Logo" style="width: 70px; height: 70px;">
                </td>
                <td style="text-align: center; width: 40%;font-weight: 600; font-size: 12px; color: #000; text-align: start; padding: 10px;">
                    ORDER NO
                    <p style="font-size: 16px; margin: 0; font-weight: 500;">{{$order->order_prefix}}</p>
                </td>
                <td style="text-align: center; width: 30%;">
                    <p  style="font-size: 16px; margin: 0; font-weight: 500;">Total: <br> Rs. {{$order->total_amount}}</p>
                </td>
            </tr>
        </table>

        <!-- Courier and Barcode -->
        <table>
            <tr>
                <td style="text-align: center;">
                    <div style="font-size: 20px; font-weight: bold;">
                        {{$shipping->courier_name}}
                    </div>
                    <div class="barcode">
                        <img src="https://barcode.tec-it.com/barcode.ashx?data={{$order->awb_number}}&multiplebarcodes=false&translate-esc=true&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&qunit=Mm&quiet=0" alt="Barcode">
                    </div>
                </td>
            </tr>
        </table>

        <!-- Date, Weight, and Dimensions -->
        <table>
            <tr>
                <th style="color: #000;font-size: 16px;font-weight: 600;">Date</th>
                <th style="color: #000;font-size: 16px;font-weight: 600;">Weight</th>
                <th style="color: #000;font-size: 16px;font-weight: 600;">Dimensions</th>
            </tr>
            <tr>
                <td style="color: #000;font-size: 14px;font-weight: 500;">{{$order->order_date}}</td>
                <td style="color: #000;font-size: 14px;font-weight: 500;">{{$order->applicable_weight}}</td>
                <td style="color: #000;font-size: 14px;font-weight: 500;">Length: {{$order->length}} cm, Width: {{$order->width}} cm, Height: {{$order->height}} cm</td>
            </tr>
        </table>
                <?php
					$pickup_address = $vendorAddress->address.' '.$vendorAddress->city.' '.$vendorAddress->state.' '.$vendorAddress->zip_code.','.$vendorAddress->country;
					$customer_address = $customerAddr->address.' '.$customerAddr->city.' '.$customerAddr->state.' '.$customerAddr->zip_code.','.$customerAddr->country;
				?>
        <!-- Shipping Address -->
        <table>
            <tr>
                <th style="color: #000;font-size: 16px;font-weight: 600;">Shipping Address</th>
            </tr>
            <tr>
                <td class="address">
                    <p style="color: #000;font-size: 14px;font-weight: 500;margin: 0;">{{$customer->first_name.' '.$customer->last_name}}</p>
                    <p style="color: #000;font-size: 14px;font-weight: 500;margin: 0;">{{$customer_address}}</p>
                    <p style="color: #000;font-size: 14px;font-weight: 500;margin: 0;">Mobile: {{$customer->mobile}}</p>
                </td>
            </tr>
        </table>

        <!-- Pickup and Return Address -->
        <table>
            <tr>
                <th style="color: #000;font-size: 16px;font-weight: 600;">Pickup and Return Address</th>
            </tr>
            <tr>
                <td class="address">
                    <p style="color: #000;font-size: 14px;font-weight: 500;margin: 0;">{{$vendor->first_name.' '.$vendor->last_name}}</p>
                    <p style="color: #000;font-size: 14px;font-weight: 500;margin: 0;">{{$pickup_address}}</p>
                    <p style="color: #000;font-size: 14px;font-weight: 500;margin: 0;">Mobile: {{$vendor->mobile}}</p>
                </td>
            </tr>
        </table>

        <!-- Note -->
        <div class="note">
            <p style="color: #000;font-size: 14px;font-weight: 500;margin: 0;color:red;">Note: No Open Delivery Allowed</p>
        </div>
    </div>
    </div>
</body>
</html>
