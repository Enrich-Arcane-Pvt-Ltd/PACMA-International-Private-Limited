

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
                width: 148mm;
                height: 210mm;
                margin: 0mm;
            }

            html, body {
                display: flex;
                justify-content: center;
                margin: 0;
                padding: 0;
                background-color: #f5f5f5;
            }
            .container {
                width: 148mm;
                height: 210mm;
                padding: 0;
                background-color: white;
            }
            .content {
                width: 148mm;
            }
            .invoice-preview-inner {
                width: 148mm;
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
                height: 25mm;
                width: 25mm;
            }
            .logo-container img{
                height: 100%;
                width: 100%;
            }
            .heading-container {
                height: 30mm;
                width: 87mm;
                padding-left: 3mm;
            }
            p {
                display: block;
                margin-block-start: 0;
                margin-block-end: 2px;
                margin-inline-start: 3px;
                margin-inline-end: 0px;
                font-size: 12px;
            }
            h1 {
                margin: 0;
                padding: 0;
            }
            h3 {
                font-size: 0.9rem;
            }
            .name-container h2 {
                display: block;
                font-size: 1.1em;
                margin-block-start: 5px;
                margin-block-end: 15px;
                margin-inline-start: 0px;
                margin-inline-end: 0px;
                font-weight: bold;
            }
            .invoice-lable {
                width: 30mm;
                height: 30mm;
            }
            .first-container{
                height: 39mm;
                width: 66mm;
                float: left;
            }
            .second-container{
                height: 39mm;
                width: 76.5mm;
            }
            .td-lable {
                font-size: 12px;
                font-weight: bold;
                background-color: #c2cbd8;
                padding-left: 10px;
            }
            .td-printing-detail {
                text-align: center;
                font-size: 12px;
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
                font-size: 12px;
                color: white;
                font-weight: bold;
                text-align: center;
            }
            .item-table td {
                height: 8mm;
            }
            .th-div {
                font-size: 12px;
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
                font-size: 12px;
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
                                            @include('components.headOfficeBlock')
                                        </div>
                                    </div>
                                    <div class="invoice-lable">
                                        <h3>CASH RECEIPT</h3>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="first-container">
                                        <div style="height: 6mm; font-size: 12px;">
                                            facebook.com/{{ config('app.name') }}
                                        </div>
                                        <table style="width: 66mm; margin-top:5.5mm">
                                            @if ($receipt->loans)
                                                <tr>
                                                    <td class="td-lable" style="border-top-left-radius: 7px; border-bottom:1px solid #485c7a8a;">FACULTY NUMBER</td>
                                                    <td class="td-printing-detail" style="border-top-right-radius: 7px;border-bottom:1px solid #485c7a8a;border-left:1px solid #485c7a8a;"> {{ \Auth::user()->loanNumberFormat($receipt->loans->id) ?? '--' }}</td>
                                                </tr>
                                            @endif
                                            <tr style="border-bottom: 0px;">
                                                <td
                                                    class="td-lable"
                                                    @if ($receipt->loans)
                                                        style="border-bottom-left-radius: 7px;"
                                                    @else
                                                        style="border-top-left-radius: 7px; border-bottom-left-radius: 7px;"
                                                    @endif
                                                >
                                                    CUSTOMER ID
                                                </td>
                                                <td
                                                    class="td-printing-detail"
                                                    @if ($receipt->loans)
                                                        style="border-bottom-right-radius: 7px; border-left:1px solid #485c7a8a;"
                                                    @else
                                                        style="border-bottom-right-radius: 7px; border-top-right-radius: 7px; border-left:1px solid #485c7a8a;"
                                                    @endif
                                                >
                                                    {{ \Auth::user()->customerNumberFormat($receipt->customer_id) ?? '--' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div class="second-container">
                                        <table style="width: 71.5mm; height: 22mm">
                                            <tr>
                                                <td class="td-lable" style="border-top-left-radius: 7px;">Receipt No.</td>
                                                <td class="td-printing-detail" style="border-top-right-radius: 7px;">{{ $receipt->receipt_code ?? '--' }}</td>
                                            </tr>
                                            @if ($receipt->invoice_id)
                                                <tr>
                                                    <td class="td-lable">Quote/Invoice No.</td>
                                                    <td class="td-printing-detail">{{ \Auth::user()->invoiceNumberFormat($receipt->invoice_id) ?? '--' }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td class="td-lable">Payment Method</td>
                                                <td class="td-printing-detail">{{ $receipt->payment_method ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="td-lable">Date</td>
                                                <td class="td-printing-detail">{{ date('d-m-Y', strtotime($receipt->issue_date)) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="td-lable">User Name</td>
                                                <td class="td-printing-detail">{{ $user->name ?? '--' }}</td>
                                            </tr>
                                            <tr style="border-bottom: 0px;">
                                                <td class="td-lable" style="border-bottom-left-radius: 7px;">User ID</td>
                                                <td class="td-printing-detail" style="border-bottom-right-radius: 7px;">{{ \Auth::user()->userNumberFormat($receipt->created_by) ?? '--' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="row" style="width: 100%; margin-top: 1rem;line-height:1.8rem;position: relative;">
                                    <div style="text-align: left; width: 100%; padding-left:1rem;">
                                        <p><b>CUSTOMER NAME</b> .............................................................................................................................................................</p>
                                        <p><b>THE SUM OF LKR</b> ................................................................................................................................................................</p>
                                        <p>......................................................................................................................................................................................................</p>

                                        <p><b>Rs</b> ................................................................................</p>
                                        <p style="font-size: 12px;">PAYMENT METHOD  NON REFUNDABLE</p>
                                    </div>

                                    <div style="text-align: left; width: 100%; padding-left:1rem;position: absolute; top:-4px;">
                                        <p style="padding-left: 35mm;">{{ $receipt->customer->name ?? '--' }}</p>
                                        <p style="padding-left: 30mm;" id="paid_amount_in_words"> {{ $receipt->paid_amount_in_words ?? '--' }}</p>
                                        <br>

                                        <input type="hidden" id="paid_amount" value="{{ number_format($receipt->paid_amount + $receipt->card_charge_amount, 2) }}" />
                                        <p style="padding-left: 10mm;">{{ number_format($receipt->paid_amount + $receipt->card_charge_amount, 2) }}</p>
                                    </div>
                                </div>


                                <div class="row" style="width: 100%; margin-top: 3rem;">
                                    <div style="text-align: center; width: 50%;">

                                    </div>
                                    <div style="text-align: center; width: 50%;">
                                        <p>...................................................................................................</p>
                                        <p>FOR {{ config('app.name') }} AUTHORIZED SIGNATORY</p>
                                    </div>
                                </div>

                                <div class="row" style="text-align: center; width: 100%; margin-top: 1.5rem;">
                                    <p style="font-size: 10px; width: 100%;">NOTE: CHEQUES SUBJECT TO REALIZATION
                                        "OTHER TERMS & CONDITIONS AS PER THE INVOICE.</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/modules/num-to-words.js') }} "></script>

        <script>
            $(document).ready(function() {
                if ($('#paid_amount').val() != '' || $('#paid_amount').val() != 0) {
                    paidAmountInWords($('#paid_amount').val());
                }
            });

            function paidAmountInWords(amount){
                $("#paid_amount_in_words").html('');
                var words = '';
                words = toWords(amount);
                $("#paid_amount_in_words").html(words + "Only");
            }

            window.onload = function() {
                printContent('app');
            };

            function printContent(elementId) {
                var content = document.getElementById(elementId);
                var originalContents = document.body.innerHTML;
                var printContents = content.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            }
        </script>

    </body>
</html>

