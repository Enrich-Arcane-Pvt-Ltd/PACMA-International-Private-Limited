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

            html, body {
                display: flex;
                justify-content: center;
                margin: 0;
                padding: 0;
                background-color: #f5f5f5;
            }
            .container {
                width: 217mm;
                height: 280mm;
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
                width: 133.5mm;
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
                width: 41mm;
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
                                            @include('components.headOfficeBlock')
                                        </div>
                                    </div>
                                    <div class="invoice-lable">
                                        <h3>CASH RECEIPT</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="first-container">
                                        <div style="height: 6mm; font-size: 13px;">
                                            facebook.com/{{ config('app.name') }}
                                        </div>
                                        <table style="width: 80mm; margin-top:5.5mm">
                                            @if ($receipt->sales)
                                                <tr>
                                                    <td class="td-lable" style="border-top-left-radius: 7px; border-bottom:1px solid #485c7a8a;">FACULTY NUMBER</td>
                                                    <td class="td-printing-detail" style="border-top-right-radius: 7px;border-bottom:1px solid #485c7a8a;border-left:1px solid #485c7a8a;"> {{ $receipt->sales->sale_id ?? '--' }}</td>
                                                </tr>
                                            @endif
                                            <tr style="border-bottom: 0px;">
                                                <td
                                                    class="td-lable"
                                                    @if ($receipt->sales)
                                                        style="border-bottom-left-radius: 7px;"
                                                    @else
                                                        style="border-top-left-radius: 7px; border-bottom-left-radius: 7px;"
                                                    @endif
                                                >
                                                    CUSTOMER ID
                                                </td>
                                                <td
                                                    class="td-printing-detail"
                                                    @if ($receipt->sales)
                                                        style="border-bottom-right-radius: 7px; border-left:1px solid #485c7a8a;"
                                                    @else
                                                        style="border-bottom-right-radius: 7px; border-top-right-radius: 7px; border-left:1px solid #485c7a8a;"
                                                    @endif
                                                >
                                                    {{ \Auth::user()->customerNumberFormat($receipt->customer_id) ?? '--' }}
                                                </td>
                                            </tr>
                                        </table>
                                        {{-- <table style="width: 111mm; height:33mm;"> --}}
                                            {{-- <tr>
                                                <td class="td-lable" colspan="2" style="height: 7mm; width:100%; border-bottom:1px solid #485c7a8a; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                                    CUSTOMER DETAILS
                                                </td>
                                            </tr> --}}
                                            {{-- <tr>
                                                <td colspan="2" class="td-printing-detail" style="height: 20mm; width:111mm; border-bottom:1px solid #485c7a8a; background-color: rgb(218, 226, 239);">
                                                    @if ($invoice->customer_id != 0)
                                                        {{ $invoice->customer->name ?? '' }} <br>
                                                        {{ $invoice->customer->address ?? '' }} <br>
                                                        {{ $invoice->customer->phone ?? '' }} <br>
                                                        {{ $invoice->customer->email ?? '' }}
                                                    @elseif ($invoice->customer_id == 0)
                                                        {{ $invoice->sales->customer_name ?? '' }} <br>
                                                        {{ $invoice->sales->customer_phone_no ?? '' }} <br>
                                                    @endif
                                                </td>
                                            </tr> --}}
                                            {{-- <tr>
                                                <td class="td-lable" style="height: 7mm; width:43mm; border-right:1px solid #485c7a8a; border-bottom-left-radius: 10px;">
                                                    CUSTOMER CODE
                                                </td>
                                                <td style="height: 7mm; width:77mm; border-bottom-right-radius: 10px;" class="td-printing-detail">  @if ($invoice->customer_id != 0) {{ \Auth::user()->customerNumberFormat($invoice->customer->id) }} @else -- @endif </td>

                                            </tr> --}}
                                        {{-- </table> --}}
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
                                        {{-- <table style="width: 71.5mm; margin-top:5.5mm">
                                            <tr>
                                                <td class="td-lable" style="border-top-left-radius: 7px;">PO NUMBER</td>
                                                <td class="td-printing-detail" style="border-top-right-radius: 7px;"> @if (isset($invoice->inventories)) @if ( $invoice->inventories->grn_id != 0) @if (isset($invoice->inventories->grns)) {{  \Auth::user()->purchaseOrderNumberFormat($invoice->inventories->grns->order_id) ?? '--' }} @else -- @endif @else -- @endif @else -- @endif</td>
                                            </tr>
                                            <tr style="border-bottom: 0px;">
                                                <td class="td-lable" style="border-bottom-left-radius: 7px;">TERMS OF PAYMENT</td>
                                                <td class="td-printing-detail" style="border-bottom-right-radius: 7px;"> {{ $invoice->sales->installment ?? '--' }} </td>
                                            </tr>
                                        </table> --}}
                                    </div>
                                </div>

                                {{-- <div class="row" style="margin-top:3.5mm; height:98mm">
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
                                        @if ($invoice->sales->modal_type == 1)
                                            <div class="row-div" style="padding-top: 7px">
                                                <div class="td-div" style="width: 23mm; font-size: 13px;"><span>{{ \Auth::user()->productNumberFormat($invoice->sales->products->id) ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 82mm;"><span>{{ $invoice->sales->products->name ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 24mm"><span>{{ $invoice->sales->products->productCategory->name ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 13mm"><span>{{ $invoice->sales->quantity ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 20mm">
                                                    <span>{{ number_format($invoice->sales->unit_price, 2) ?? '--' }}</span>
                                                </div>
                                                <div class="td-div" style="width: 20mm"><span>{{ number_format($invoice->sales->discount, 2) ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 28mm; text-align:right; padding-right:5px">
                                                    <span>{{ number_format($invoice->sales->net_amount, 2) ?? '--' }}</span>
                                                </div>
                                            </div>
                                        @elseif ($invoice->sales->modal_type == 2)
                                            @foreach ($invoice->sales->saleItems as $items)
                                                <div class="row-div" style="padding-top: 7px">
                                                    <div class="td-div" style="width: 23mm; font-size: 13px;"><span>{{ \Auth::user()->productNumberFormat($items->products->id) ?? '--' }}</span></div>
                                                    <div class="td-div" style="width: 82mm;"><span>{{ $items->products->name ?? '--' }} @if ($items->products->warranty_period != 0) &nbsp;&nbsp;/ &nbsp;&nbsp; Wr - {{ $items->products->warranty_period ?? '--' }} mth @endif</span></div>
                                                    <div class="td-div" style="width: 24mm"><span>{{ $items->products->productCategory->name ?? '--' }}</span></div>
                                                    <div class="td-div" style="width: 13mm"><span>{{ $items->quantity ?? '--' }}</span></div>
                                                    <div class="td-div" style="width: 20mm">
                                                        <span>{{ number_format($items->unit_price, 2) ?? '--' }}</span>
                                                    </div>
                                                    <div class="td-div" style="width: 20mm"><span>{{ number_format($items->discount, 2) ?? '--' }}</span></div>
                                                    <div class="td-div" style="width: 28mm; text-align:right; padding-right:5px">
                                                        <span>{{ number_format($items->net_amount, 2) ?? '--' }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="row" style="margin-top:2.5mm; height:50mm">
                                    <div class="row-div" style="height: 9mm; width:100%; background-color:rgb(218, 226, 239); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                                        <div class="th-div-2" style="width: 105mm; border-top-left-radius: 13px;"><span>Terms & Conditions</span></div>
                                        <div class="th-div-2" style="width: 57mm"><span>Bank Details</span></div>
                                        <div class="th-div-2" style="width: 21.5mm"><span>Total Amount</span></div>
                                        <div class="td-div-2" style="width: 28mm; height:100%; text-align:right; padding-right:10px; border-bottom: 1px solid #485c7a;">

                                            <span>{{ number_format($invoice->sales->net_amount, 2) ?? '0.00' }}</span>
                                        </div>
                                    </div>
                                    <div class="row-div" style="height: 41mm; width:100%; background-color:rgb(218, 226, 239)">
                                        @if ($invoice->sales->sale_type == 1)
                                            <div class="td-div-2" style="width: 105mm; height: 41mm; border-right: 1px solid #485c7a;">
                                                <div style="width: 100%; height: 11mm">
                                                    <pre style="font-size: 14px; height: 100%; margin: 0; padding-top: 4px;">{{ $companyterms->description ?? '' }}</pre>
                                                </div>
                                                <div class="row-div">
                                                    <div style="width: 42%; height: 30mm; font-size: 14px; display: table;">
                                                        <span style="display: table-cell; vertical-align: middle;">Warrenty Period :<br>{{ $invoice->sales->products->warranty_period ?? '--' }} months</span>
                                                    </div>
                                                    <div style="width: 58%; height: 30mm; font-size: 14px; display: table;">
                                                        <span style="display: table-cell; vertical-align: middle;">
                                                            <p style="height: 0;"><span>Monthly Installment</span><span style="padding-left: 5px; padding-right: 5px;">:</span><span>{{ number_format($invoice->sales->monthly_installment, 2) ?? '0.00' }}</span></p><br>
                                                            <p style="height: 0;"><span>No. of Installments</span><span style="padding-left: 15px; padding-right: 5px;">:</span><span>{{ $invoice->sales->installment ?? '0.00' }}</span></p><br>
                                                            <p style="height: 0;"><span>Total Due</span><span style="padding-left: 71.5px; padding-right: 5px;">:</span style="padding-left: 5px; padding-right: 5px;"><span>{{ number_format($invoice->sales->monthly_installment, 2) }} x {{ $invoice->sales->installment }}</span></p><br>
                                                            <p style="height: 0;"><span style="padding-left: 131px; padding-right: 5px;">:</span><span>{{ number_format($invoice->sales->monthly_installment*$invoice->sales->installment, 2) }}</span></p><br>
                                                            <p style="height: 0;"><span>Installment Date</span><span style="padding-left: 29px; padding-right: 5px;">:</span><span>{{ $invoice->sales->installment_date }}</span></p><br>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="td-div-2" style="width: 105mm; height: 41mm; border-right: 1px solid #485c7a; display: table; text-align: center;">
                                                <div style="width: 100%; height: 50%; display: table; text-align: center;">
                                                    <pre style="display: table-cell; vertical-align: middle; font-size:14px;">{{ $companyterms->description ?? '' }}</pre>
                                                </div>
                                                <div style="width: 100%; height: 50%; display: table; text-align: center;">
                                                    @if (isset($invoice->sales->products))
                                                        <span style="display: table-cell; vertical-align: middle; font-size:14px;">Warrenty Period :<br>{{ $invoice->sales->products->warranty_period ?? '--' }} months</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        <div class="td-div-2" style="width: 57mm; height: 41mm">
                                            <span>
                                                {{ $invoice->bank->account_no ?? '' }} <br>
                                                {{ $invoice->bank->bank ?? '' }} <br>
                                                {{ $invoice->bank->address ?? '' }}
                                            </span>
                                        </div>
                                        <div style="width: 49.5mm; height: 41mm">
                                            <div class="row-div" style="width: 49.5mm; height: 10.25mm">
                                                <div class="th-div-2" style="width: 21.5mm; height: 100%;"><span>TRANSPORT CHARGES</span></div>
                                                <div class="td-div-2" style="width: 28mm; text-align:right; padding-right:10px; height: 100%; border-bottom: 1px solid #485c7a;"><span>{{ number_format($invoice->sales->traveling_charges, 2) ?? '0.00' }}</span></div>
                                            </div>
                                            <div class="row-div" style="width: 49.5mm; height: 10.25mm">
                                                <div class="th-div-2" style="width: 21.5mm; height: 100%;"><span>DOWN PAYMENT</span></div>
                                                <div class="td-div-2" style="width: 28mm; text-align:right; padding-right:10px; height: 100%; border-bottom: 1px solid #485c7a;"><span>{{ number_format($invoice->sales->down_payment, 2) ?? '0.00' }}</span></div>
                                            </div>
                                            <div class="row-div" style="width: 49.5mm; height: 10.25mm">
                                                <div class="th-div-2" style="width: 21.5mm; height: 100%;"><span>ADVANCE AMOUNT</span></div>
                                                <div class="td-div-2" style="width: 28mm; text-align:right; padding-right:10px; height: 100%; border-bottom: 1px solid #485c7a;"><span>{{ number_format($invoice->sales->advance_amount, 2) ?? '0.00' }}</span></div>
                                            </div>
                                            <div class="row-div" style="width: 49.5mm; height: 10.25mm">
                                                <div class="th-div-2" style="width: 21.5mm; height: 100%;"><span>BALANCE AMOUNT</span></div>
                                                <div class="td-div-2" style="width: 28mm; text-align:right; padding-right:10px; height: 100%;"><span>{{ number_format($invoice->sales->total_due_amount, 2) ?? '0.00' }}</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>--}}

                                <div class="row" style="width: 100%; margin-top: 1rem;line-height:1.8rem;position: relative;">
                                    <div style="text-align: left; width: 100%; padding-left:1rem;">
                                        <p><b>CUSTOMER NAME</b> .......................................................................................................................................................................................................</p>
                                        <p><b>THE SUM OF LKR</b> ..........................................................................................................................................................................................................</p>
                                        <p>................................................................................................................................................................................................................................................</p>

                                        <p><b>Rs</b> .........................................................................</p>
                                        <p style="font-size: 12px;">PAYMENT METHOD  NON REFUNDABLE</p>
                                    </div>

                                    <div style="text-align: left; width: 100%; padding-left:1rem;position: absolute; top:-4px;">
                                        <p style="padding-left: 60mm;">{{ $receipt->customer->name ?? '--' }}</p>
                                        <p style="padding-left: 60mm;" id="paid_amount_in_words"> {{ $receipt->paid_amount_in_words ?? '--' }}</p>
                                        <br>

                                        <input type="hidden" id="paid_amount" value="{{ number_format($receipt->paid_amount + $receipt->card_charge_amount, 2) }}" />
                                        <p style="padding-left: 10mm;">{{ number_format($receipt->paid_amount + $receipt->card_charge_amount, 2) }}</p>
                                    </div>


                                </div>


                                <div class="row" style="width: 100%; margin-top: 3rem;">
                                    <div style="text-align: center; width: 33%;">

                                    </div>
                                    <div style="text-align: center; width: 33%;">

                                    </div>
                                    <div style="text-align: center; width: 33%;">
                                        <p>..................................................................................</p>
                                        <p>FOR {{ config('app.name') }} AUTHORIZED SIGNATORY</p>
                                    </div>
                                </div>
                                <div class="row" style="text-align: center; width: 100%; margin-top: 1.5rem;">
                                    <p style="font-size: 11.5px; width: 100%;">NOTE: CHEQUES SUBJECT TO REALIZATION
                                        "OTHER TERMS & CONDITIONS AS PER THE INVOICE.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div id="invoice_preview" style="display: none;">
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
                                                <td style="height: 7mm; width:34mm;">  </td>
                                                <td style="height: 7mm; width:77mm;"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="height: 20mm; width:111mm; text-align: center; font-size:14px;">
                                                    @if ($invoice->customer_id != 0)
                                                        {{ $invoice->customer->name ?? '' }} <br>
                                                        {{ $invoice->customer->address ?? '' }} <br>
                                                        {{ $invoice->customer->phone ?? '' }} <br>
                                                        {{ $invoice->customer->email ?? '' }}
                                                    @elseif ($invoice->customer_id == 0)
                                                        {{ $invoice->sales->customer_name ?? '' }} <br>
                                                        {{ $invoice->sales->customer_phone_no ?? '' }} <br>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="height: 7mm; width:34mm;">  </td>
                                                <td style="height: 7mm; width:77mm; text-align: center; font-size:14px;"> @if ($invoice->customer_id != 0) {{ \Auth::user()->customerNumberFormat($invoice->customer->id) }} @else -- @endif </td>

                                            </tr>
                                        </table>
                                    </div>
                                    <div style="height: 39mm; width: 82mm; float: right;">
                                        <table style="width: 62.5mm; height: 22mm; float: right;">
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;">  </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">{{ $invoice->invoice_code ?? '--' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;">  </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">{{ date('d-m-Y', strtotime($invoice->issue_date)) ?? '--' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;">  </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">{{ $user->name ?? '--' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;">  </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">{{ \Auth::user()->userNumberFormat($invoice->created_by) ?? '--' }}</td>
                                            </tr>
                                        </table>
                                        <table style="width: 62.5mm; margin-top:5.5mm; float: right;">
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;">  </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">@if (isset($invoice->inventories)) @if ( $invoice->inventories->grn_id != 0) @if (isset($invoice->inventories->grns)) {{  \Auth::user()->purchaseOrderNumberFormat($invoice->inventories->grns->order_id) ?? '--' }} @else -- @endif @else -- @endif @else -- @endif</td>
                                            </tr>
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;">  </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;"> {{ $invoice->sales->installment ?? '--' }} </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div style="margin-top:11.5mm; height:98mm; display: flex; flex-wrap: wrap;">
                                    <div style="height: 9mm; width:100%; display: flex; flex-wrap: wrap;">
                                        <div style="width: 23mm; height: 9mm; line-height: 14px;">
                                            <span>
                                            </span>
                                        </div>
                                        <div style="width: 82mm; height: 9mm; line-height: 14px;">
                                            <span>
                                            </span>
                                        </div>
                                        <div style="width: 24mm; height: 9mm; line-height: 14px;">
                                            <span>
                                            </span>
                                        </div>
                                        <div style="width: 13mm; height: 9mm; line-height: 14px;">
                                            <span>
                                            </span>
                                        </div>
                                        <div style="width: 20mm; height: 9mm; line-height: 14px;">
                                            <span>
                                            </span>
                                        </div>
                                        <div style="width: 20mm; height: 9mm; line-height: 14px;">
                                            <span>
                                            </span>
                                        </div>
                                        <div style="width: 28mm; height: 9mm; line-height: 14px;">
                                            <span>
                                            </span>
                                        </div>
                                    </div>
                                    <div style="height: 88mm; width:100%;">
                                        @if ($invoice->sales->modal_type == 1)
                                            <div style="display: flex; flex-wrap: wrap;">
                                                <div class="td-div" style="width: 23mm; text-align:left; height: 8mm; display: table; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ \Auth::user()->productNumberFormat($invoice->sales->products->id) ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 85mm; text-align:center; height: 8mm; display: table; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->products->name ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 27mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->products->productCategory->name ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 15mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->quantity ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 23mm; height: 8mm; display: table; text-align: center; font-size:14px;">
                                                    <span style="display: table-cell; vertical-align: middle;">{{ number_format($invoice->sales->unit_price, 2) ?? '--' }}</span>
                                                </div>
                                                <div class="td-div" style="width: 22mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ number_format($invoice->sales->discount, 2) ?? '--' }}</span></div>
                                                <div class="td-div" style="width: 22mm; height: 8mm; display: table; text-align: right; font-size:14px;">
                                                    <span style="display: table-cell; vertical-align: middle;">{{ number_format($invoice->sales->net_amount, 2) ?? '--' }}</span>
                                                </div>
                                            </div>
                                        @elseif ($invoice->sales->modal_type == 2)
                                            @foreach ($invoice->sales->saleItems as $saleItems)
                                                <div style="display: flex; flex-wrap: wrap;">
                                                    <div class="td-div" style="width: 23mm; text-align:left; height: 8mm; display: table; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ \Auth::user()->productNumberFormat($saleItems->products->id) ?? '--' }}</span></div>
                                                    <div class="td-div" style="width: 85mm; text-align:center; height: 8mm; display: table; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $saleItems->products->name ?? '--' }} @if ($saleItems->products->warranty_period != 0) &nbsp;&nbsp;/ &nbsp;&nbsp; Wr - {{ $saleItems->products->warranty_period ?? '--' }} mth @endif</span></div>
                                                    <div class="td-div" style="width: 27mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $saleItems->products->productCategory->name ?? '--' }}</span></div>
                                                    <div class="td-div" style="width: 15mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $saleItems->quantity ?? '--' }}</span></div>
                                                    <div class="td-div" style="width: 23mm; height: 8mm; display: table; text-align: center; font-size:14px;">
                                                        <span style="display: table-cell; vertical-align: middle;">{{ number_format($saleItems->unit_price, 2) ?? '--' }}</span>
                                                    </div>
                                                    <div class="td-div" style="width: 22mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ number_format($saleItems->discount, 2) ?? '--' }}</span></div>
                                                    <div class="td-div" style="width: 22mm; height: 8mm; display: table; text-align: right; font-size:14px;">
                                                        <span style="display: table-cell; vertical-align: middle;">{{ number_format($saleItems->net_amount, 2) ?? '--' }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div style="margin-top:17mm; height:50mm; display: flex; flex-wrap: wrap;">
                                    <div style="height: 9mm; width:100%; display: flex; flex-wrap: wrap;">
                                        <div style="width: 105mm; height: 9mm; line-height: 14px;">
                                            <span>
                                            </span>
                                        </div>
                                        <div style="width: 62mm; height: 9mm; line-height: 14px;">
                                            <span>
                                            </span>
                                        </div>
                                        <div style="width: 21.5mm; height: 9mm; line-height: 14px;">
                                            <span>
                                            </span>
                                        </div>
                                        <div style="width: 28mm; height:100%; display: table; text-align: right; font-size:14px;">
                                            <span style="display: table-cell; vertical-align: middle;">{{ number_format($invoice->sales->net_amount, 2) ?? '--' }}</span>
                                        </div>
                                    </div>
                                    <div style="height: 41mm; width:100%; display: flex; flex-wrap: wrap;">
                                        @if ($invoice->sales->sale_type == 1)
                                            <div style="width: 105mm; height: 41mm;">
                                                <div style="width: 100%; height: 11mm; text-align: center;">
                                                    <pre style="font-size: 14px; height: 100%; margin: 0; padding-top: 4px;">{{ $companyterms->description ?? '' }}</pre>
                                                </div>
                                                <div style="display: flex; flex-wrap: wrap;">
                                                    <div style="width: 42%; height: 30mm; font-size: 14px; display: table; text-align: center;">
                                                        <span style="display: table-cell; vertical-align: middle;">Warrenty Period :<br>{{ $invoice->sales->products->warranty_period ?? '--' }} months</span>
                                                    </div>
                                                    <div style="width: 58%; height: 30mm; font-size: 14px; display: table;">
                                                        <span style="display: table-cell; vertical-align: middle;">
                                                            <p style="height: 5px;"><span>Monthly Installment</span><span style="padding-left: 8px; padding-right: 5px;">:</span><span>{{ number_format($invoice->sales->monthly_installment, 2) ?? '0.00' }}</span></p>
                                                            <p style="height: 5px;"><span>No. of Installments</span><span style="padding-left: 15px; padding-right: 5px;">:</span><span>{{ $invoice->sales->installment ?? '0.00' }}</span></p>
                                                            <p style="height: 5px;"><span>Total Due</span><span style="padding-left: 67.5px; padding-right: 5px;">:</span style="padding-left: 5px; padding-right: 5px;"><span>{{ number_format($invoice->sales->monthly_installment, 2) }} x {{ $invoice->sales->installment }}</span></p>
                                                            <p style="height: 5px;"><span style="padding-left: 123px; padding-right: 5px;">:</span><span>{{ number_format($invoice->sales->monthly_installment*$invoice->sales->installment, 2) }}</span></p>
                                                            <p style="height: 5px;"><span>Installment Date</span><span style="padding-left: 29px; padding-right: 5px;">:</span><span>{{ $invoice->sales->installment_date }}</span></p>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div style="width: 105mm; height: 41mm; display: table; text-align: center;">
                                                <div style="width: 100%; height: 50%; display: table; text-align: center;">
                                                    <pre style="display: table-cell; vertical-align: middle; font-size:14px;">{{ $companyterms->description ?? '' }}</pre>
                                                </div>
                                                <div style="width: 100%; height: 50%; display: table; text-align: center;">
                                                    @if (isset($invoice->sales->products))
                                                        <span style="display: table-cell; vertical-align: middle;">Warrenty Period :<br>{{ $invoice->sales->products->warranty_period ?? '--' }} months</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        <div style="width: 62mm; height: 41mm;  display: table; text-align: center;">
                                            <span style="display: table-cell; vertical-align: middle; font-size:14px;">{{ $invoice->bank->account_no ?? '' }} <br>
                                                {{ $invoice->bank->bank ?? '' }} <br>
                                                {{ $invoice->bank->address ?? '' }}
                                            </span>
                                        </div>
                                        <div style="width: 49.5mm; height: 41mm">
                                            <div style="width: 49.5mm; height: 11mm; display: flex; flex-wrap: wrap;">
                                                <div class="th-div-2-print" style="width: 21.5mm; height: 100%;">
                                                    <span>

                                                    </span>
                                                </div>
                                                <div style="width: 28mm; height: 100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ number_format($invoice->sales->traveling_charges, 2) ?? '0.00' }}</span></div>
                                            </div>
                                            <div style="width: 49.5mm; height: 11mm; margin-top:1mm; display: flex; flex-wrap: wrap;">
                                                <div class="th-div-2-print" style="width: 21.5mm; height: 100%;">
                                                    <span>
                                                    </span>
                                                </div>
                                                <div style="width: 28mm; height: 100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ number_format($invoice->sales->down_payment, 2) ?? '0.00' }}</span></div>
                                            </div>
                                            <div style="width: 49.5mm; height: 11mm; margin-top:1mm; display: flex; flex-wrap: wrap;">
                                                <div class="th-div-2-print" style="width: 21.5mm; height: 100%;">
                                                    <span>
                                                    </span>
                                                </div>
                                                <div style="width: 28mm; height: 100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ number_format($invoice->sales->advance_amount, 2) ?? '0.00' }}</span></div>
                                            </div>
                                            <div style="width: 49.5mm; height: 11mm; margin-top:1mm; display: flex; flex-wrap: wrap;">
                                                <div class="th-div-2-print" style="width: 21.5mm; height: 100%;">
                                                    <span>
                                                    </span>
                                                </div>
                                                <div style="width: 28mm; height: 100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ number_format($invoice->sales->total_due_amount, 2) ?? '0.00' }}</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </body>
</html>

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
