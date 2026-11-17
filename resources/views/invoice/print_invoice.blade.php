<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Lato&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

        <title>Hello World</title>
        <style>
            html, body {
                width: 217mm;
                height: 280mm;
                margin: 0;
                padding: 0;
                background-color: rgb(255, 255, 255);
            }
            .container {
                width: inherit;
                height: inherit;
                margin-left: 50%;
                padding: 0;
                background-color: white;
            }
            .content {
                width: 217mm;
            }
            .invoice-preview-inner {
                width: 217mm;
            }
            .row {
                display: flex;
                flex-wrap: wrap;
                padding-left: 2.5mm;
                padding-right: 3mm;
            }
            .row-div {
                display: flex;
                flex-wrap: wrap;
            }
            .logo-container {
                height: 37mm;
                width: 37mm;
            }
            .logo-container img{
                height: 100%;
                width: 100%;
            }
            .heading-container {
                height: 37mm;
                width: 143.5mm;
                padding-left: 3.5mm;
            }
            p {
                display: block;
                margin-block-start: 0;
                margin-block-end: 2px;
                margin-inline-start: 3px;
                margin-inline-end: 0px;
                font-size: 14px;
            }
            h1 {
                margin: 0;
                padding: 0;
            }
            .name-container h2 {
                display: block;
                font-size: 1.3em;
                margin-block-start: 5px;
                margin-block-end: 15px;
                margin-inline-start: 0px;
                margin-inline-end: 0px;
                font-weight: bold;
            }
            .invoice-lable {
                width: 31mm;
                height: 37mm;
            }
            .first-container{
                height: 39mm;
                width: 135mm;
                float: left;
            }
            .second-container{
                height: 39mm;
                width: 76.5mm;
            }
            .td-lable {
                font-size: 13px;
                font-weight: bold;
            }
            .second-container td{
                height: 5.5mm;
            }
            .second-container .td-lable{
                width: 35mm;
                border-right: 1px solid;
            }
            .second-container table{
                border: 1px solid;
                float: right;
            }
            .second-container tr{
                border-bottom: 1px solid;
            }
            .th-lable {
                font-size: 13px;
                color: white;
                font-weight: bold;
                text-align: center;
            }
            .item-table td {
                height: 8mm;
            }
            .th-div {
                font-size: 13px;
                color: white;
                font-weight: bold;
                height: 9mm;
                line-height: 14px;
                display: table;
                text-align: center;
            }
            .th-div span {
                display: table-cell;
                vertical-align: middle;
            }
            .td-div {
                height: 8mm;
                display: table;
                text-align: center;
            }
            .td-div span {
                display: table-cell;
                vertical-align: middle;
            }
            .th-div-2 {
                font-size: 12px;
                color: white;
                font-weight: bold;
                height: 9mm;
                line-height: 14px;
                display: table;
                text-align: center;
                background-color:rgb(72, 92, 122);
            }
            .th-div-2 span {
                display: table-cell;
                vertical-align: middle;
            }
            .td-div-2 {
                display: table;
                text-align: center;
            }
            .td-div-2 span {
                display: table-cell;
                vertical-align: middle;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div id="app" class="content">
                <div class="editor">
                    <div class="invoice-preview-inner">
                        <div class="editor-content">
                            <div class="preview-main client-preview">
                                <div class="row">
                                    <div class="logo-container">
                                        <img src="{{ asset('storage/uploads/logo/small_logo.png') }}"/>
                                    </div>
                                    <div class="heading-container">
                                        <div class="name-container">
                                            <h2>{{ config('app.name') }}</h2>
                                        </div>
                                        <div class="detail-container">
                                            {{-- <p>Head Office : No.51, Thammita Road, Negombo.</p>
                                            <p>Tel : 031-2230288 E-mail : info@rcom</p>
                                            <p><span>Banch : </span><span>Negombo Branch</span></p> --}}
                                            @include('components.headOfficeBlock')
                                        </div>
                                    </div>
                                    <div class="invoice-lable">
                                        <h3>INVOICE</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="first-container">
                                        <div style="height: 6mm; font-size: 13px;">
                                            facebook.com/{{ config('app.name') }}
                                        </div>
                                        <table style="width: 111mm; height:33mm; background-color:rgb(194, 203, 216); border:1px solid">
                                            <tr>
                                                <td class="td-lable" style="height: 7mm; width:34mm; border-bottom:1px solid">
                                                    CUSTOMER DETAILS
                                                </td>
                                                <td style="height: 7mm; width:77mm; border-bottom:1px solid"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="height: 20mm; width:111mm; border-bottom:1px solid; background-color: rgb(218, 226, 239);">
                                                    custumer details here
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="td-lable" style="height: 7mm; width:34mm; border-right:1px solid">
                                                    CUSTOMER CODE
                                                </td>
                                                <td style="height: 7mm; width:77mm"></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="second-container">
                                        <table style="width: 62.5mm; height: 22mm">
                                            <tr>
                                                <td class="td-lable">INVOICE NO.</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td class="td-lable">INVOICE DATE</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td class="td-lable">USERNAME</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td class="td-lable">USER ID</td>
                                                <td></td>
                                            </tr>
                                        </table>
                                        <table style="width: 62.5mm; margin-top:5.5mm">
                                            <tr>
                                                <td class="td-lable">PO NUMBER</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td class="td-lable">TERMS OF PAYMENT</td>
                                                <td></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="row" style="margin-top:3.5mm; height:98mm">
                                    <div class="row-div" style="height: 9mm; width:100%; background-color:rgb(72, 92, 122); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                                        <div class="th-div" style="width: 23mm"><span>CODE</span></div>
                                        <div class="th-div" style="width: 82mm"><span>ITEM DESCRIPTION</span></div>
                                        <div class="th-div" style="width: 24mm"><span>PRODUCT TYPE</span></div>
                                        <div class="th-div" style="width: 13mm"><span>QTY</span></div>
                                        <div class="th-div" style="width: 20mm"><span>UNIT<br>PRICE</span></div>
                                        <div class="th-div" style="width: 20mm"><span>DISCOUNT RATE</span></div>
                                        <div class="th-div" style="width: 28mm"><span>AMOUNT</span></div>
                                    </div>
                                    <div style="height: 88mm; width:100%; background-color:rgb(218, 226, 239)">
                                        <div class="row-div">
                                            <div class="td-div" style="width: 23mm"><span>1</span></div>
                                            <div class="td-div" style="width: 82mm; text-align:left; padding-left:10px"><span>1</span></div>
                                            <div class="td-div" style="width: 24mm"><span>1</span></div>
                                            <div class="td-div" style="width: 13mm"><span>1</span></div>
                                            <div class="td-div" style="width: 20mm"><span>1</span></div>
                                            <div class="td-div" style="width: 20mm"><span>1</span></div>
                                            <div class="td-div" style="width: 28mm; text-align:right; padding-right:5px"><span>1</span></div>
                                        </div>
                                        <div class="row-div">
                                            <div class="td-div" style="width: 23mm"><span>2</span></div>
                                            <div class="td-div" style="width: 82mm; text-align:left; padding-left:10px"><span>2</span></div>
                                            <div class="td-div" style="width: 24mm"><span>2</span></div>
                                            <div class="td-div" style="width: 13mm"><span>2</span></div>
                                            <div class="td-div" style="width: 20mm"><span>2</span></div>
                                            <div class="td-div" style="width: 20mm"><span>2</span></div>
                                            <div class="td-div" style="width: 28mm; text-align:right; padding-right:5px"><span>2</span></div>
                                        </div>
                                        <div class="row-div">
                                            <div class="td-div" style="width: 23mm"><span>3</span></div>
                                            <div class="td-div" style="width: 82mm; text-align:left; padding-left:10px"><span>3</span></div>
                                            <div class="td-div" style="width: 24mm"><span>3</span></div>
                                            <div class="td-div" style="width: 13mm"><span>3</span></div>
                                            <div class="td-div" style="width: 20mm"><span>3</span></div>
                                            <div class="td-div" style="width: 20mm"><span>3</span></div>
                                            <div class="td-div" style="width: 28mm; text-align:right; padding-right:8px"><span>3</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top:2.5mm; height:50mm">
                                    <div class="row-div" style="height: 9mm; width:100%; background-color:rgb(218, 226, 239); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                                        <div class="th-div-2" style="width: 105mm; border-top-left-radius: 13px;"><span>Terms & Conditions</span></div>
                                        <div class="th-div-2" style="width: 57mm"><span>Bank Details</span></div>
                                        <div class="th-div-2" style="width: 21.5mm"><span>Total Amount</span></div>
                                        <div class="td-div-2" style="width: 28mm; height:100%; text-align:right; padding-right:8px; border-bottom: 1px solid #485c7a;"><span>0.00</span></div>
                                    </div>
                                    <div class="row-div" style="height: 41mm; width:100%; background-color:rgb(218, 226, 239)">
                                        <div class="td-div-2" style="width: 105mm; height: 41mm; border-right: 1px solid #485c7a;"><span>conditions</span></div>
                                        <div class="td-div-2" style="width: 57mm; height: 41mm"><span>1</span></div>
                                        <div style="width: 49.5mm; height: 41mm">
                                            <div class="row-div" style="width: 49.5mm; height: 10.25mm">
                                                <div class="th-div-2" style="width: 21.5mm; height: 100%;"><span>TRANSPORT CHARGES</span></div>
                                                <div class="td-div-2" style="width: 28mm; text-align:right; padding-right:8px; height: 100%; border-bottom: 1px solid #485c7a;"><span>1</span></div>
                                            </div>
                                            <div class="row-div" style="width: 49.5mm; height: 10.25mm">
                                                <div class="th-div-2" style="width: 21.5mm; height: 100%;"><span>DOWN PAYMENT</span></div>
                                                <div class="td-div-2" style="width: 28mm; text-align:right; padding-right:8px; height: 100%; border-bottom: 1px solid #485c7a;"><span>1</span></div>
                                            </div>
                                            <div class="row-div" style="width: 49.5mm; height: 10.25mm">
                                                <div class="th-div-2" style="width: 21.5mm; height: 100%;"><span>ADVANCE AMOUNT</span></div>
                                                <div class="td-div-2" style="width: 28mm; text-align:right; padding-right:8px; height: 100%; border-bottom: 1px solid #485c7a;"><span>1</span></div>
                                            </div>
                                            <div class="row-div" style="width: 49.5mm; height: 10.25mm">
                                                <div class="th-div-2" style="width: 21.5mm; height: 100%;"><span>BALANCE AMOUNT</span></div>
                                                <div class="td-div-2" style="width: 28mm; text-align:right; padding-right:8px; height: 100%;"><span>1</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<script>
</script>
