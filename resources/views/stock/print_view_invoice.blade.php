<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Lato&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

        <title>Invoice Print View</title>
        <style>
            @page {
                width: 217mm;
                /* height: 280mm; */
                margin: 0mm;
            }
            /* * {
                font-family: sans-serif;
            } */
            html, body {
                width: 217mm;
                height: 280mm;
                margin: 0;
                padding: 0;
                background-color: rgb(235, 235, 235);
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
                background-color: #c2cbd8;
                padding-left: 21px;
            }
            .td-printing-detail {
                text-align: center;
                font-size: 14px;
                background-color: #dae2ef;
            }
            .second-container td{
                height: 5.5mm;
            }
            .second-container .td-lable{
                width: 43mm;
                border-right: 1px solid #485c7a8a;
            }
            .second-container table{
                /* border: 1px solid; */
                float: right;
            }
            .second-container tr{
                border-bottom: 1px solid #485c7a8a;
            }
            .second-container-print{
                height: 39mm;
                width: 76.5mm;
            }
            .second-container-print td{
                height: 5.5mm;
            }
            .second-container-print .td-lable{
                width: 35mm;
            }
            .second-container-print table{
                float: right;
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
                font-size: 13px;
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
            .th-div-2-print {
                font-size: 12px;
                font-weight: bold;
                height: 9mm;
                line-height: 14px;
                display: table;
                text-align: center;
            }
            .th-div-2-print span {
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
                                            <p>Tel : 031-2230288 E-mail : info@com</p>
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
                                        <table style="width: 111mm; height:33mm;">
                                            <tr>
                                                <td class="td-lable" colspan="2" style="height: 7mm; width:100%; border-bottom:1px solid #485c7a8a; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                                    CUSTOMER DETAILS
                                                </td>
                                                {{-- <td style="height: 7mm; width:77mm; border-bottom:1px solid"></td> --}}
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="td-printing-detail" style="height: 20mm; width:111mm; border-bottom:1px solid #485c7a8a; background-color: rgb(218, 226, 239);">
                                                    {{ $transfers->branch->name ?? '' }} Branch
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="td-lable" style="height: 7mm; width:43mm; border-right:1px solid #485c7a8a; border-bottom-left-radius: 10px;">
                                                    CUSTOMER CODE
                                                </td>
                                                <td style="height: 7mm; width:77mm; border-bottom-right-radius: 10px;" class="td-printing-detail"> --  </td>

                                            </tr>
                                        </table>
                                    </div>
                                    <div class="second-container">
                                        <table style="width: 71.5mm; height: 22mm">
                                            <tr>
                                                <td class="td-lable" style="border-top-left-radius: 7px;">INVOICE NO.</td>
                                                <td class="td-printing-detail" style="border-top-right-radius: 7px;">{{ \Auth::user()->stockTransferNumberFormat($transfers->id) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="td-lable">INVOICE DATE</td>
                                                <td class="td-printing-detail">{{ date('d-m-Y', strtotime($transfers->date)) ?? '--' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="td-lable">USER NAME</td>
                                                <td class="td-printing-detail">{{ $user->name ?? '--' }}</td>
                                            </tr>
                                            <tr style="border-bottom: 0px;">
                                                <td class="td-lable" style="border-bottom-left-radius: 7px;">USER ID</td>
                                                <td class="td-printing-detail" style="border-bottom-right-radius: 7px;">{{ \Auth::user()->userNumberFormat($transfers->created_by) ?? '--' }}</td>
                                            </tr>
                                        </table>
                                        <table style="width: 71.5mm; margin-top:5.5mm">
                                            <tr>
                                                <td class="td-lable" style="border-top-left-radius: 7px;">PO NUMBER</td>
                                                <td class="td-printing-detail" style="border-top-right-radius: 7px;">--</td>
                                            </tr>
                                            <tr style="border-bottom: 0px;">
                                                <td class="td-lable" style="border-bottom-left-radius: 7px;">TERMS OF PAYMENT</td>
                                                <td class="td-printing-detail" style="border-bottom-right-radius: 7px;"> -- </td>
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
                                        @foreach ($transfers->stockTransItems as $items)
                                            <div class="row-div" style="padding-top: 7px">
                                                <div class="td-div" style="width: 23mm; font-size: 13px;"><span>{{ \Auth::user()->productNumberFormat($items->products->id) ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 82mm;"><span>{{ $items->products->name ?? '--' }} </span></div>
                                                <div class="td-div" style="width: 24mm"><span>{{ $items->products->productCategory->name ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 13mm"><span>{{ $items->quantity ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 20mm">
                                                    <span>{{ number_format($items->purchase_price, 2) ?? '--' }}</span>
                                                </div>
                                                <div class="td-div" style="width: 20mm"><span>--</span></div>
                                                <div class="td-div" style="width: 28mm; text-align:right; padding-right:5px">
                                                    <span>{{ number_format($items->amount, 2) ?? '--' }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="row" style="margin-top:2.5mm; height:50mm">
                                    <div class="row-div" style="height: 9mm; width:100%; background-color:rgb(218, 226, 239); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                                        <div class="th-div-2" style="width: 105mm; border-top-left-radius: 13px;"><span>Terms & Conditions</span></div>
                                        <div class="th-div-2" style="width: 57mm"><span>Bank Details</span></div>
                                        <div class="th-div-2" style="width: 21.5mm"><span>Total Amount</span></div>
                                        <div class="td-div-2" style="width: 28mm; height:100%; text-align:right; padding-right:10px; border-bottom: 1px solid #485c7a;">
                                            {{-- @if ($invoice->sales->sale_type == 1)
                                                <span>{{ number_format($invoice->sales->down_payment + $invoice->sales->total_due_amount, 2) ?? '0.00' }}</span>
                                            @else
                                                <span>{{ number_format($invoice->sales->net_amount, 2) ?? '0.00' }}</span>
                                            @endif --}}
                                            <span>{{ number_format($transfers->total_amount, 2) ?? '0.00' }}</span>
                                        </div>
                                    </div>
                                    <div class="row-div" style="height: 41mm; width:100%; background-color:rgb(218, 226, 239)">
                                        <div class="td-div-2" style="width: 105mm; height: 41mm; border-right: 1px solid #485c7a; display: table; text-align: center;">
                                            <div style="width: 100%; height: 50%; display: table; text-align: center;">
                                                <pre style="display: table-cell; vertical-align: middle; font-size:14px;">--</pre>
                                            </div>
                                            <div style="width: 100%; height: 50%; display: table; text-align: center;">
                                                {{-- @if (isset($invoice->sales->products))
                                                    <span style="display: table-cell; vertical-align: middle; font-size:14px;">Warrenty Period :<br>{{ $invoice->sales->products->warranty_period ?? '--' }} months</span>
                                                @endif --}}
                                            </div>
                                        </div>
                                        <div class="td-div-2" style="width: 57mm; height: 41mm">
                                            <span>
                                                {{-- {{ $invoice->bank->account_no ?? '' }} <br>
                                                {{ $invoice->bank->bank ?? '' }} <br>
                                                {{ $invoice->bank->address ?? '' }} --}}
                                            </span>
                                        </div>
                                        <div style="width: 49.5mm; height: 41mm">
                                            <div class="row-div" style="width: 49.5mm; height: 10.25mm">
                                                <div class="th-div-2" style="width: 21.5mm; height: 100%;"><span>TRANSPORT CHARGES</span></div>
                                                <div class="td-div-2" style="width: 28mm; text-align:right; padding-right:10px; height: 100%; border-bottom: 1px solid #485c7a;"><span>--</span></div>
                                            </div>
                                            <div class="row-div" style="width: 49.5mm; height: 10.25mm">
                                                <div class="th-div-2" style="width: 21.5mm; height: 100%;"><span>DOWN PAYMENT</span></div>
                                                <div class="td-div-2" style="width: 28mm; text-align:right; padding-right:10px; height: 100%; border-bottom: 1px solid #485c7a;"><span>--</span></div>
                                            </div>
                                            <div class="row-div" style="width: 49.5mm; height: 10.25mm">
                                                <div class="th-div-2" style="width: 21.5mm; height: 100%;"><span>ADVANCE AMOUNT</span></div>
                                                <div class="td-div-2" style="width: 28mm; text-align:right; padding-right:10px; height: 100%; border-bottom: 1px solid #485c7a;"><span>--</span></div>
                                            </div>
                                            <div class="row-div" style="width: 49.5mm; height: 10.25mm">
                                                <div class="th-div-2" style="width: 21.5mm; height: 100%;"><span>BALANCE AMOUNT</span></div>
                                                <div class="td-div-2" style="width: 28mm; text-align:right; padding-right:10px; height: 100%;"><span>--</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="width: 100%; margin-top: 3rem;">
                                    <div style="text-align: center; width: 33%;">
                                        <p>......................................................</p>
                                        <p>Customer Signature</p>
                                    </div>
                                    <div style="text-align: center; width: 33%;">
                                        <p>......................................................</p>
                                        <p>Prepared By</p>
                                    </div>
                                    <div style="text-align: center; width: 33%;">
                                        <p>......................................................</p>
                                        <p>Authorized By</p>
                                    </div>
                                </div>
                                <div class="row" style="text-align: center; width: 100%; margin-top: 1.5rem;">
                                    <p style="font-size: 11.5px; width: 100%;">All cheques to be drawn in favour of {{ config('app.name') }} and crossed ACCOUNT PAYEE. Please indicate invoice number with the payment.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="invoice_preview" style="display: none;">
            <div id="app" style="width: 217mm;">
                <div class="editor">
                    <div style="width: 217mm;">
                        <div class="editor-content">
                            <div class="preview-main client-preview">
                                <div style="display: flex; flex-wrap: wrap; margin-top:43mm">
                                    <div style="height: 39mm; width: 135mm; float: left;">
                                        <div style="height: 6mm; font-size: 13px;">

                                        </div>
                                        <table style="width: 111mm; height:33mm; margin-top: 4mm;">
                                            <tr>
                                                <td style="height: 7mm; width:34mm;"> {{-- CUSTOMER DETAILS --}} </td>
                                                <td style="height: 7mm; width:77mm;"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="height: 20mm; width:111mm; text-align: center; font-size:14px;">
                                                    {{ $transfers->branch->name ?? '' }} Branch
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="height: 7mm; width:34mm;"> {{-- CUSTOMER CODE --}} </td>
                                                <td style="height: 7mm; width:77mm; text-align: center; font-size:14px;"> -- </td>

                                            </tr>
                                        </table>
                                    </div>
                                    <div style="height: 39mm; width: 82mm; float: right;">
                                        <table style="width: 62.5mm; height: 22mm; float: right;">
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;"> {{-- INVOICE NO. --}} </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">{{ \Auth::user()->stockTransferNumberFormat($transfers->id) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;"> {{-- INVOICE DATE --}} </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">{{ date('d-m-Y', strtotime($transfers->date)) ?? '--' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;"> {{-- USER NAME --}} </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">{{ $user->name ?? '--' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;"> {{-- USER ID --}} </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">{{ \Auth::user()->userNumberFormat($transfers->created_by) ?? '--' }}</td>
                                            </tr>
                                        </table>
                                        <table style="width: 62.5mm; margin-top:5.5mm; float: right;">
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;"> {{-- PO NUMBER --}} </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">--</td>
                                            </tr>
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;"> {{-- TERMS OF PAYMENT --}} </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;"> -- </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div style="margin-top:11.5mm; height:98mm; display: flex; flex-wrap: wrap;">
                                    <div style="height: 9mm; width:100%; display: flex; flex-wrap: wrap;">
                                        <div style="width: 23mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- CODE --}}
                                            </span>
                                        </div>
                                        <div style="width: 82mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- ITEM DESCRIPTION class="th-div"--}}
                                            </span>
                                        </div>
                                        <div style="width: 24mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- PRODUCT TYPE class="th-div"--}}
                                            </span>
                                        </div>
                                        <div style="width: 13mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- QTY class="th-div"--}}
                                            </span>
                                        </div>
                                        <div style="width: 20mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- UNIT<br>PRICE class="th-div"--}}
                                            </span>
                                        </div>
                                        <div style="width: 20mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- DISCOUNT RATE class="th-div"--}}
                                            </span>
                                        </div>
                                        <div style="width: 28mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- AMOUNT class="th-div"--}}
                                            </span>
                                        </div>
                                    </div>
                                    <div style="height: 88mm; width:100%;">
                                        @foreach ($transfers->stockTransItems as $stockItems)
                                            <div style="display: flex; flex-wrap: wrap;">
                                                <div class="td-div" style="width: 23mm; text-align:left; height: 8mm; display: table; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ \Auth::user()->productNumberFormat($stockItems->products->id) ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 85mm; text-align:center; height: 8mm; display: table; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $stockItems->products->name ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 27mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $stockItems->products->productCategory->name ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 15mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $stockItems->quantity ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 23mm; height: 8mm; display: table; text-align: center; font-size:14px;">
                                                    <span style="display: table-cell; vertical-align: middle;">{{ number_format($stockItems->purchase_price, 2) ?? '--' }}</span>
                                                </div>
                                                <div class="td-div" style="width: 22mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">--</span></div>
                                                <div class="td-div" style="width: 22mm; height: 8mm; display: table; text-align: right; font-size:14px;">
                                                    <span style="display: table-cell; vertical-align: middle;">{{ number_format($stockItems->amount, 2) ?? '--' }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div style="margin-top:16mm; height:50mm; display: flex; flex-wrap: wrap;">
                                    <div style="height: 9mm; width:100%; display: flex; flex-wrap: wrap;">
                                        <div style="width: 105mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- Terms & Conditions --}}
                                            </span>
                                        </div>
                                        <div style="width: 62mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- Bank Details --}}
                                            </span>
                                        </div>
                                        <div style="width: 21.5mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- Total Amount --}}
                                            </span>
                                        </div>
                                        <div style="width: 28mm; height:100%; display: table; text-align: right; font-size:14px;">
                                            {{-- @if ($invoice->sales->sale_type == 1)
                                                <span style="display: table-cell; vertical-align: middle;">{{ number_format($invoice->sales->down_payment + $invoice->sales->total_due_amount, 2) ?? '--' }}</span>
                                            @else
                                                <span style="display: table-cell; vertical-align: middle;">{{ number_format($invoice->sales->net_amount, 2) ?? '--' }}</span>
                                            @endif --}}
                                            <span style="display: table-cell; vertical-align: middle;">{{ number_format($transfers->total_amount, 2) ?? '--' }}</span>
                                        </div>
                                    </div>
                                    <div style="height: 41mm; width:100%; display: flex; flex-wrap: wrap;">
                                        <div style="width: 105mm; height: 41mm; display: table; text-align: center;">
                                            <div style="width: 100%; height: 50%; display: table; text-align: center;">
                                                <pre style="display: table-cell; vertical-align: middle; font-size:14px;">--</pre>
                                            </div>
                                            <div style="width: 100%; height: 50%; display: table; text-align: center;">
                                                {{-- @if (isset($invoice->sales->products))
                                                    <span style="display: table-cell; vertical-align: middle;">Warrenty Period :<br>{{ $invoice->sales->products->warranty_period ?? '--' }} months</span>
                                                @endif --}}
                                            </div>
                                        </div>
                                        <div style="width: 62mm; height: 41mm;  display: table; text-align: center;">
                                            <span style="display: table-cell; vertical-align: middle; font-size:14px;">
                                                {{-- {{ $invoice->bank->account_no ?? '' }} <br>
                                                {{ $invoice->bank->bank ?? '' }} <br>
                                                {{ $invoice->bank->address ?? '' }} --}}
                                            </span>
                                        </div>
                                        <div style="width: 49.5mm; height: 41mm">
                                            <div style="width: 49.5mm; height: 10.25mm; display: flex; flex-wrap: wrap;">
                                                <div class="th-div-2-print" style="width: 21.5mm; height: 100%;">
                                                    <span>
                                                        {{-- TRANSPORT CHARGES --}}
                                                    </span>
                                                </div>
                                                <div style="width: 28mm; height: 100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">--</span></div>
                                            </div>
                                            <div style="width: 49.5mm; height: 10.25mm; display: flex; flex-wrap: wrap;">
                                                <div class="th-div-2-print" style="width: 21.5mm; height: 100%;">
                                                    <span>
                                                        {{-- DOWN PAYEMENT --}}
                                                    </span>
                                                </div>
                                                <div style="width: 28mm; height: 100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">--</span></div>
                                            </div>
                                            <div style="width: 49.5mm; height: 10.25mm;  display: flex; flex-wrap: wrap;">
                                                <div class="th-div-2-print" style="width: 21.5mm; height: 100%;">
                                                    <span>
                                                        {{-- ADVANCE AMOUNT --}}
                                                    </span>
                                                </div>
                                                <div style="width: 28mm; height: 100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">--</span></div>
                                            </div>
                                            <div style="width: 49.5mm; height: 10.25mm; display: flex; flex-wrap: wrap;">
                                                <div class="th-div-2-print" style="width: 21.5mm; height: 100%;">
                                                    <span>
                                                        {{-- BALANCE AMOUNT --}}
                                                    </span>
                                                </div>
                                                <div style="width: 28mm; height: 100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">--</span></div>
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

<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
    var command = <?php echo json_encode($command); ?>;
    var invoice = <?php echo json_encode($transfers); ?>;
        $(document).ready(function(){
            console.log(invoice);
            if(command == 'print'){
                var divContents = document.getElementById("invoice_preview").innerHTML;
                var a = window.open("", "", "width=1000mm, height=788.031pt");
                a.document.write("<html>");
                a.document.write("<body>");
                a.document.write(divContents);
                a.document.write("</body></html>");
                a.document.close();
                a.print();
                a.close();
            }
        });
</script>
