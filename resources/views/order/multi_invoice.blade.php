<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    @page {
        size: A4;
        margin: 0;
    }

    body {
        font-family: sans-serif !important;
        margin: 0;
        padding: 0;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin: 0 auto;
    }

    td,
    th {
        padding: 10px;
        text-align: start;
    }

    th {
        font-size: 12px;
    }

    td {
        font-size: 14px;
        font-weight: 600;
    }
    
 
</style>


<body>
    <div class="table-responsive" id="pdfContent">
        <table border="0" cellpadding="0" cellspacing="0"
            style="font-family: sans-serif !important; border: 1px solid #000;width: 100%;margin: 0 auto;text-wrap: wrap !important; padding-bottom: 0;font-size: 11px;">
            <tbody>
                <tr>
                    <td style="padding: 0px 0 !important;">
                        <table border="0" cellpadding="0" cellspacing="0"
                            style="width: 100%; margin: 0px auto;margin-bottom: 0;">
                            <tbody>
                                <tr>
                                    <td style="text-align: right;background: rgb(245, 247, 251);padding: 18px;">
                                        <a href="javascript:void(0)" id="generatePdfButton" style="text-decoration: none;">
                                            <b
                                                style="text-align: right; background: #ff6801;padding: 9px 13px;color: #fff;font-size: 14px;border-radius: 5px;">Print</b>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table border="0" cellpadding="0" cellspacing="0"
                            style="width: 100%; margin: 0px auto;margin-bottom: 0;  padding-top: 15px;">
                            <tbody>
                                <tr>
                                    <td>
                                        <img width="100" src="https://starexpressin.com/assets/img/foot-logo.png"
                                            alt="">
                                    </td>
                                    <td style="width: 50%;text-align: end;">
                                        <h6 style="font-size: 18px;margin: 0;">Star Express</h6>
                                        <!--<p style="font-size: 14px;color: #7c7c7c;margin: 8px 0;">Exyte Solutions Pvt.-->
                                        <!--    Ltd.-->
                                        <!--</p>-->
                                        <p style="font-size: 14px;color: #7c7c7c;margin: 8px 0;">H NO.2348 SHOP 4 LOWER GROUND FLOOR, RAJ MARKET,</p>
                                        <p style="font-size: 14px;color: #7c7c7c;margin: 8px 0;">DORIYAWAD SALABATPURA SURAT-395002 GUJARAT[INDIA]</p>
                                        <p style="font-size: 14px;color: #7c7c7c;margin: 8px 0;">info@starexpressin.in</p>
                                        <p style="font-size: 14px;color: #7c7c7c;margin: 8px 0;">starexpressin.com</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table border="0" cellpadding="0" cellspacing="0"
                            style="width: 100%; margin: 0px auto;padding-top: 15px;border-bottom: 1px solid #bbb;padding-bottom: 0;margin-bottom: 0;padding-top: 15px;">
                            <tbody>
                                <tr>
                                    <td>
                                        <h6 style="font-size: 16px;line-height: 1.5;color: #262626;margin: 0;">Date: <br>
                                        {{ date("d-M-Y", strtotime($invoice_data->date)) }}
                                            <!--29/Feb/2024-->
                                        </h6>
                                    </td>
                                    <td style="width: 50%;text-align: end;">
                                        <!--<h5 style="font-size: 14px;margin: 0;margin-bottom: 5px;">PAN: CWLPB2098G</h5>-->
                                        <h5 style="font-size: 14px;margin: 0;">GSTIN: 24CWLPB2098G1ZK</h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table border="0" cellpadding="0" cellspacing="0"
                            style="width: 100%; margin: 0px auto;margin-bottom: 0;  padding-top: 15px;">
                            <tbody>
                                <tr>
                                    <td>
                                        <h4 style="margin: 8px 0;font-size: 16px;">Invoice Number : </h4>
                                        <h4 style="margin: 8px 0;font-size: 16px;">Bill To:</h4>
                                        <h4 style="margin: 8px 0;font-size: 16px;">{{ $invoice_data->user_company_name }}</h4>
                                        <p style="font-size: 14px;color: #7c7c7c;margin: 8px 0;">{{ $invoice_data->user_name }}</p>
                                        <p style="font-size: 14px;color: #7c7c7c;margin: 8px 0;">{{ $invoice_data->company_address }}</p>
                                        <p style="font-size: 14px;color: #7c7c7c;margin: 8px 0;">Number : {{ $invoice_data->user_mobile }}</p>
                                        <p style="font-size: 14px;color: #7c7c7c;margin: 8px 0;">Email : {{ $invoice_data->user_email }}</p>

                                        <h5 style="font-size: 14px;margin: 5px 0; margin-top: 20px;">PAN: {{ $invoice_data->user_pancard }} </h5>
                                        <h5 style="font-size: 14px;margin: 5px 0;">GSTIN: {{ $invoice_data->user_gst }}</h5>
                                        <h5 style="font-size: 14px;margin: 5px 0;">Billing State: {{ $invoice_data->billing_state }}</h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table border="1" cellpadding="0" cellspacing="0"
                            style="width: 100%; margin: 0px auto;margin-bottom: 0;  margin-top: 15px; border-color: #ebedf0;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Particulars</th>
                                    <th>HSN/SAC</th>
                                    <th>Shipments Count</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{ $i = 1 }}
                                @foreach($invoice_datas as $invoice_data)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>Logistics Services <br/>{{ $invoice_data->order_data }}</td>
                                    <td>996812</td>
                                    <td>1</td>
                                    <td>{{ $invoice_data->order_total_amount_without_tax }}</td>
                                </tr>
                                {{$i++}}
                                @endforeach
                               
                                <tr>
                                    <td colspan="3" rowspan="5">
                                        <h6 style="font-size: 14px;margin: 0;margin-top: 10px;">Terms & Conditions:</h6>
                                        <ul>
                                            <li style="font-size: 14px;color: #7c7c7c;margin: 5px 0;">All Cheques/DD in
                                                favor of Star Express</li>
                                            <li style="font-size: 14px;color: #7c7c7c;margin: 5px 0;">If you have any kind of discrepancy on this bill, please contact key manager or write to us on info@starexpressin.com within 15 calander days of invocie date.</li>
                                             <li style="font-size: 14px;color: #7c7c7c;margin: 5px 0;">Post 15 calander days Star Express will not be able to settle any kind of dispute on this bill.</li>
                                            <li style="font-size: 14px;color: #7c7c7c;margin: 5px 0;">For any queries
                                                feel free to contact your account manager.</li>
                                            <li style="font-size: 14px;color: #7c7c7c;margin: 5px 0;">Any dispute
                                                subject to Surat jurisdition</li>
                                            
                                        </ul>
                                        <p style="font-size: 14px;color: #7c7c7c;margin: 5px 0;">This is computer
                                            generated receipt and does not require physical signature.
                                        </p>
                                    </td>
                                    <td>SUBTOTAL</td>
                                    <td>{{ $invoice_data->order_total_amount_without_tax }}</td>
                                </tr>
                                @if($invoice_data->billing_state == 'Gujarat')
                                <tr>
                                    <td>SGST @ 9%</td>
                                    <td>{{ $invoice_data->order_total_tax / 2 }}</td>
                                </tr>
                                <tr>
                                    <td>CGST @ 9%</td>
                                    <td>{{ $invoice_data->order_total_tax / 2 }}</td>
                                </tr>
                                @else
                                <tr>
                                    <td>IGST @ 18%</td>
                                    <td>{{ $invoice_data->order_total_tax }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>GRAND TOTAL</td>
                                    <td>{{ $invoice_data->order_total_amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <table border="0" cellpadding="0" cellspacing="0"
                            style="width: 100%; margin: 0px auto;margin-bottom: 0;  padding-top: 15px;">
                            <tbody>
                                <tr>
                                    <td>
                                        <!--<h6 style="font-size: 14px;margin: 5px 0;">Description</h6>-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table border="0" cellpadding="0" cellspacing="0"
                            style="width: 100%; margin: 0px auto;margin-bottom: 0;">
                            <tbody>
                                <tr>
                                    <td style="vertical-align: bottom;">
                                        <h5 style="font-size: 20px;color: #7c7c7c;margin: 5px 0;">
                                            Thank you for trusting and doing business with Star Express.</h5>
                                    </td>
                                    <td style="text-align: end;vertical-align: bottom;">
                                        <img width="100"
                                            src="https://cdn.pixabay.com/photo/2020/04/10/13/23/paid-5025785_960_720.png"
                                            alt="">
                                        <h5 style="font-size: 20px;color: #000;margin: 5px 0;">
                                            Authorised Signatory</h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        $('#generatePdfButton').click(function () {
            var element = document.getElementById('pdfContent');
            html2pdf()
                .from(element)
                .save('document.pdf');
        });
    });
</script>

</html>