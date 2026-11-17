<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Lato&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

    <style type="text/css">
        .resize-observer[data-v-b329ee4c] {
            position: absolute;
            top: 0;
            left: 0;
            z-index: -1;
            width: 100%;
            height: 100%;
            border: none;
            background-color: transparent;
            pointer-events: none;
            display: block;
            overflow: hidden;
            opacity: 0
        }

        .resize-observer[data-v-b329ee4c] object {
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: -1
        }
    </style>
    <style type="text/css">
        p[data-v-f2a183a6] {
            line-height: 1.2em;
            margin: 0 0 2px 0;
        }

        pre[data-v-f2a183a6] {
            margin: 0;
        }

        .d-table[data-v-f2a183a6] {
            margin-top: 20px;
        }

        .d-table-footer[data-v-f2a183a6] {
            display: -webkit-box;
            display: flex;
        }

        .d-table-controls[data-v-f2a183a6] {
            -webkit-box-flex: 2;
            flex: 2;
        }

        .d-table-summary[data-v-f2a183a6] {
            -webkit-box-flex: 1;
            flex: 1;
        }

        .d-table-summary-item[data-v-f2a183a6] {
            width: 100%;
            display: -webkit-box;
            display: flex;
        }

        .d-table-label[data-v-f2a183a6] {
            -webkit-box-flex: 1;
            flex: 1;
            display: -webkit-box;
            display: flex;
            -webkit-box-pack: end;
            justify-content: flex-end;
            padding-top: 9px;
            padding-bottom: 9px;
        }

        .d-table-label .form-input[data-v-f2a183a6] {
            margin-left: 10px;
            width: 80px;
            height: 24px;
        }

        .d-table-label .form-input-mask-text[data-v-f2a183a6] {
            top: 3px;
        }

        .d-table-value[data-v-f2a183a6] {
            -webkit-box-flex: 1;
            flex: 1;
            text-align: right;
            padding-top: 9px;
            padding-bottom: 9px;
            padding-right: 10px;
        }

        .d-table-spacer[data-v-f2a183a6] {
            margin-top: 5px;
        }

        .d-table-tr[data-v-f2a183a6] {
            display: -webkit-box;
            display: flex;
            flex-wrap: wrap;
        }

        .d-table-td[data-v-f2a183a6] {
            padding: 10px 10px 10px 10px;
        }

        .d-table-th[data-v-f2a183a6] {
            padding: 10px 10px 10px 10px;
            font-weight: bold;
            font-size: 14px;
        }

        .d-body[data-v-f2a183a6] {
            padding: 25px;
        }

        .d[data-v-f2a183a6] {
            font-size: 0.9em !important;
            color: black;
            background: white;
            min-height: 1000px;
        }

        .d-right[data-v-f2a183a6] {
            text-align: right;
        }

        .d-title[data-v-f2a183a6] {
            font-size: 50px;
            line-height: 50px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .d-header-50[data-v-f2a183a6] {
            -webkit-box-flex: 1;
            flex: 1;
        }

        .d-header-inner[data-v-f2a183a6] {
            display: -webkit-box;
            display: flex;
            padding: 25px;
        }

        .d-header-brand[data-v-f2a183a6] {
            width: 200px;
        }

        .d-logo[data-v-f2a183a6] {
            max-width: 100%;
        }
    </style>
    <style type="text/css">
        p[data-v-37eeda86] {
            line-height: 1.2em;
            margin: 0 0 2px 0;
        }

        pre[data-v-37eeda86] {
            margin: 0;
        }

        img[data-v-37eeda86] {
            max-width: 100%;
        }

        .d-table-value[data-v-37eeda86] {
            padding-right: 0;
        }

        .d-table-controls[data-v-37eeda86] {
            -webkit-box-flex: 5;
            flex: 5;
        }

        .d-table-summary[data-v-37eeda86] {
            -webkit-box-flex: 4;
            flex: 4;
        }
    </style>
    <style type="text/css">
        p[data-v-e95a8a8c] {
            line-height: 1.2em;
            margin: 0 0 2px 0;
        }

        pre[data-v-e95a8a8c] {
            margin: 0;
        }

        img[data-v-e95a8a8c] {
            max-width: 100%;
        }

        .d[data-v-e95a8a8c] {
            font-family: monospace;
        }

        .fancy-title[data-v-e95a8a8c] {
            margin-top: 0;
            padding-top: 0;
        }

        .d-table-value[data-v-e95a8a8c] {
            padding-right: 0;
        }

        .d-table-controls[data-v-e95a8a8c] {
            -webkit-box-flex: 5;
            flex: 5;
        }

        .d-table-summary[data-v-e95a8a8c] {
            -webkit-box-flex: 4;
            flex: 4;
        }
    </style>
    <style type="text/css">
        p[data-v-363339a0] {
            line-height: 1.2em;
            margin: 0 0 2px 0;
        }

        pre[data-v-363339a0] {
            margin: 0;
        }

        img[data-v-363339a0] {
            max-width: 100%;
        }

        .fancy-title[data-v-363339a0] {
            margin-top: 0;
            font-size: 30px;
            line-height: 1.2em;
            padding-top: 0;
        }

        .f-b[data-v-363339a0] {
            font-size: 17px;
            line-height: 1.2em;
        }

        .thank[data-v-363339a0] {
            font-size: 45px;
            line-height: 1.2em;
            text-align: right;
            font-style: italic;
            padding-right: 25px;
        }

        .f-remarks[data-v-363339a0] {
            padding-left: 25px;
        }

        .d-table-value[data-v-363339a0] {
            padding-right: 0;
        }

        .d-table-controls[data-v-363339a0] {
            -webkit-box-flex: 5;
            flex: 5;
        }

        .d-table-summary[data-v-363339a0] {
            -webkit-box-flex: 4;
            flex: 4;
        }
    </style>
    <style type="text/css">
        p[data-v-e23d9750] {
            line-height: 1.2em;
            margin: 0 0 2px 0;
        }

        pre[data-v-e23d9750] {
            margin: 0;
        }

        img[data-v-e23d9750] {
            max-width: 100%;
        }

        .fancy-title[data-v-e23d9750] {
            margin-top: 0;
            font-size: 40px;
            line-height: 1.2em;
            font-weight: bold;
            padding: 25px;
            margin-right: 25px;
        }

        .f-b[data-v-e23d9750] {
            font-size: 17px;
            line-height: 1.2em;
        }

        .thank[data-v-e23d9750] {
            font-size: 45px;
            line-height: 1.2em;
            text-align: right;
            font-style: italic;
            padding-right: 25px;
        }

        .f-remarks[data-v-e23d9750] {
            padding: 25px;
        }

        .d-table-value[data-v-e23d9750] {
            padding-right: 0;
        }

        .d-table-controls[data-v-e23d9750] {
            -webkit-box-flex: 5;
            flex: 5;
        }

        .d-table-summary[data-v-e23d9750] {
            -webkit-box-flex: 4;
            flex: 4;
        }
    </style>
    <style type="text/css">
        p[data-v-4b3dcb8a] {
            line-height: 1.2em;
            margin: 0 0 2px 0;
        }

        pre[data-v-4b3dcb8a] {
            margin: 0;
        }

        img[data-v-4b3dcb8a] {
            max-width: 100%;
        }

        .fancy-title[data-v-4b3dcb8a] {
            margin-top: 0;
            padding-top: 0;
        }

        .sub-title[data-v-4b3dcb8a] {
            margin: 5px 0 3px 0;
            display: block;
        }

        .d-table-value[data-v-4b3dcb8a] {
            padding-right: 0;
        }

        .d-table-controls[data-v-4b3dcb8a] {
            -webkit-box-flex: 5;
            flex: 5;
        }

        .d-table-summary[data-v-4b3dcb8a] {
            -webkit-box-flex: 4;
            flex: 4;
        }
    </style>
    <style type="text/css">
        p[data-v-1ad6e3b9] {
            line-height: 1.2em;
            margin: 0 0 2px 0;
        }

        pre[data-v-1ad6e3b9] {
            margin: 0;
        }

        img[data-v-1ad6e3b9] {
            max-width: 100%;
        }

        .fancy-title[data-v-1ad6e3b9] {
            margin-top: 0;
            padding-top: 0;
        }

        .sub-title[data-v-1ad6e3b9] {
            margin: 5px 0 3px 0;
            display: block;
        }

        .d-no-pad[data-v-1ad6e3b9] {
            padding: 0px;
        }

        .grey-box[data-v-1ad6e3b9] {
            padding: 50px;
            background: #f8f8f8;
        }

        .d-inner-2[data-v-1ad6e3b9] {
            padding: 50px;
        }
    </style>
    <style type="text/css">
        p[data-v-136bf9b5] {
            line-height: 1.2em;
            margin: 0 0 2px 0;
        }

        pre[data-v-136bf9b5] {
            margin: 0;
        }

        img[data-v-136bf9b5] {
            max-width: 100%;
        }

        .fancy-title[data-v-136bf9b5] {
            margin-top: 0;
            padding-top: 0;
        }

        .d-table-value[data-v-136bf9b5] {
            padding-right: 0px;
        }
    </style>
    <style type="text/css">
        p[data-v-7d9d14b5] {
            line-height: 1.2em;
            margin: 0 0 2px 0;
        }

        pre[data-v-7d9d14b5] {
            margin: 0;
        }

        img[data-v-7d9d14b5] {
            max-width: 100%;
        }

        .fancy-title[data-v-7d9d14b5] {
            margin-top: 0;
            padding-top: 0;
        }

        .sub-title[data-v-7d9d14b5] {
            margin: 0 0 5px 0;
        }

        .padd[data-v-7d9d14b5] {
            margin-left: 5px;
            padding-left: 5px;
            border-left: 1px solid #f8f8f8;
            margin-right: 5px;
            padding-right: 5px;
            border-right: 1px solid #f8f8f8;
        }

        .d-inner[data-v-7d9d14b5] {
            padding-right: 0px;
        }

        .d-table-value[data-v-7d9d14b5] {
            padding-right: 5px;
        }

        .d-table-controls[data-v-7d9d14b5] {
            -webkit-box-flex: 5;
            flex: 5;
        }

        .d-table-summary[data-v-7d9d14b5] {
            -webkit-box-flex: 4;
            flex: 4;
        }
    </style>
    <style type="text/css">
        p[data-v-b8f60a0c] {
            line-height: 1.2em;
            margin: 0 0 2px 0;
        }

        pre[data-v-b8f60a0c] {
            margin: 0;
        }

        img[data-v-b8f60a0c] {
            max-width: 100%;
        }

        .fancy-title[data-v-b8f60a0c] {
            margin-top: 0;
            padding-top: 10px;
        }

        .d-table-value[data-v-b8f60a0c] {
            padding-right: 0;
        }

        .d-table-controls[data-v-b8f60a0c] {
            -webkit-box-flex: 5;
            flex: 5;
        }

        .d-table-summary[data-v-b8f60a0c] {
            -webkit-box-flex: 4;
            flex: 4;
        }

        .overflow-x-hidden {
            overflow-x: hidden !important;
        }

        .centerText {
            text-align: center;
        }

        .textNoWrap {
            white-space: nowrap;
        }

        .content {
            width: 100%;
        }

        ..invoice-preview-inner {
            width: 100%;
        }

        .text-left {
        text-align: left !important;
        }

        .text-right {
        text-align: right !important;
        }

    </style>

    @if (isset($print))
        <style>
            @page {
                size: A4;
                margin: 0;
            }

            @media print {

                html,
                body {
                    width: 210mm;
                    height: 297mm;
                }
            }
        </style>
    @endif

</head>

<body class="">

    <div class="container">
        <div id="app" class="content">
            <div class="editor">
                <div class="invoice-preview-inner">
                    <div class="editor-content">
                        <div class="preview-main client-preview">
                            <div data-v-f2a183a6="" class="d" id="boxes"
                                style="width: auto; margin-left: 1cm; margin-right: 1cm;">
                                <div data-v-f2a183a6="" class="d-header"
                                    style="background: {{ $color }};color:{{ $font_color }}">
                                    <div data-v-f2a183a6="" class="d-header-inner">
                                        <div data-v-f2a183a6="" class="d-header-50">
                                            <div data-v-f2a183a6="" class="d-header-brand">
                                                <img src="{{ $img }}" style="max-width: 250px" />
                                            </div>
                                            <div data-v-f2a183a6="" class="break-25"></div>
                                            <p data-v-f2a183a6="">
                                            </p>
                                            {{ !empty($purchase_order->branch->branch_manager) ? $purchase_order->branch->branch_manager : '' }}<br>
                                            {{ !empty($purchase_order->branch->name) ? $purchase_order->branch->name : '' }}<br>
                                            {{ !empty($purchase_order->branch->address) ? $purchase_order->branch->address : '' }}<br>
                                        </div>

                                        <div data-v-f2a183a6="" class="d-header-50 d-right">
                                            <div data-v-f2a183a6="" class="d-title">{{ __('GRN') }}</div>
                                            <table data-v-f2a183a6="" class="summary-table">
                                                <tbody data-v-f2a183a6="">
                                                    <tr>
                                                        <td>{{ __('Supplier Invoice') }}:</td>
                                                        <td>{{ $purchase_order->supplier_invoice_number ?? '-N/A-' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('Number') }}:</td>
                                                        <td>{{ \Auth::user()->grnNumberFormat($purchase_order->id) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('Order Date') }}:</td>
                                                        <td>{{ \App\Utility::dateFormat($settings, $purchase_order->order_date) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('Required By') }}:</td>
                                                        <td>{{ \App\Utility::dateFormat($settings, $purchase_order->required_by) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('Received By') }}:</td>
                                                        <td>{{ $receivedByName . 'ho' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div data-v-f2a183a6="" class="d-body">
                                    <div data-v-f2a183a6="" class="d-bill-to">
                                        <div style="margin-top:-30px;" class="row">
                                            <table style="max-width:400px;">
                                                <tr>
                                                    <td>Checked By</td>
                                                    <td style="text-align:center;">
                                                        ......................................</td>
                                                    <td style="text-align:center;">......................</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td style="text-align:center;">(Name)</td>
                                                    <td style="text-align:center;">(Signature)</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div style="margin-top:25px;" class="row">
                                            <div class="bill_to">
                                                <strong data-v-f2a183a6="">{{ __('Bill To') }}:</strong>
                                                <p>
                                                    {{ !empty($vendor->billing_name) ? $vendor->billing_name : '' }}<br>
                                                    {{ !empty($vendor->billing_address) ? $vendor->billing_address : '' }}<br>
                                                    {{ !empty($vendor->billing_zip) ? $vendor->billing_zip : '' }}
                                                    {{ !empty($vendor->billing_city) ? $vendor->billing_city : '' }}
                                                    {{ !empty($vendor->billing_phone) ? $vendor->billing_phone : '' }}<br>
                                                    {{ !empty($vendor->email) ? $vendor->email : '' }}
                                                </p>
                                            </div>
                                        </div>
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <thead style="background: {{ $color }}; color: {{ $font_color }};">
                                                <tr>
                                                    <th class="w-auto">{{ __('Item') }}</th>
                                                    <th class="w-4 centerText">{{ __('Unit Cost') }}</th>
                                                    <th class="w-2 centerText">{{ __('Quantity') }}</th>
                                                    <th class="w-3 centerText">{{ __('GRN Cost') }}</th>
                                                    <th class="w-3 centerText">{{ __('Cash Price') }}</th>
                                                    <th class="w-3 centerText">{{ __('Retail Price') }}</th>
                                                    <th class="w-3 centerText">{{ __('Received Quantity') }}</th>
                                                    <th class="w-3 text-right">{{ __('Amount') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody >
                                                @if (isset($purchase_order->items) && count($purchase_order->items) > 0)
                                                    @foreach ($purchase_order->items as $key => $item)
                                                        <tr style="border-bottom: 1px solid black">
                                                            <td>{{ $item->name }}</td>
                                                            <td class="centerText textNoWrap">{{ __('Rs.')}} {{ number_format($item->unit_cost ?? 0, 2) }}</td>
                                                            <td class="centerText textNoWrap">{{ $item->quantity }}</td>
                                                            <td class="centerText textNoWrap">{{ __('Rs.')}} {{ number_format($item->grn_cost ?? 0, 2) }}</td>
                                                            <td class="centerText textNoWrap">{{ __('Rs.')}} {{ number_format($item->sale_price ?? 0, 2) }}</td>
                                                            <td class="centerText textNoWrap">{{ __('Rs.')}} {{ number_format($item->hire_price ?? 0, 2) }}</td>
                                                            <td class="centerText textNoWrap">{{ $item->received_qty }}</td>
                                                            <td class="text-right textNoWrap">{{ __('Rs.')}} {{ number_format($item->amount ?? 0, 2) }}</td>
                                                        </tr>

                                                    @endforeach
                                                @else
                                                    <tr style="border-bottom:1px solid {{ $color }};">
                                                        <td colspan="8" style="text-align:center;"><span>-</span></td>
                                                    </tr>

                                                @endif
                                            </tbody>
                                            <br>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="9" style="height: 5mm;">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" style="text-align:right;"><strong>{{ __('Subtotal') }}:</strong></td>
                                                    <td colspan="2" class="text-right">{{ __('Rs.')}} {{ number_format($purchase_order->total_amount ?? 0, 2) }}</td>
                                                </tr>
                                                <tr style="border-top: 1px solid {{ $color }}; border-bottom: 1px solid {{ $color }};">
                                                    <td colspan="7" style="text-align:right;"><strong>{{ __('Total') }}:</strong></td>
                                                    <td colspan="2" class="text-right">{{ __('Rs.')}} {{ number_format($purchase_order->total_amount ?? 0, 2) }}</td>
                                                </tr>

                                            </tfoot>
                                        </table>

                                        <div class="d-header-50">
                                            <p>
                                                {{ $settings['footer_title'] }} <br>
                                                {{ $settings['footer_notes'] }}
                                            </p>
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

    @if (!isset($preview))
        @if (isset($print))
            @include('purchaseOrder.print_script');
        @else
            @include('purchaseOrder.script');
        @endif
    @endif

</body>

</html>
<script src="{{ asset('js/jquery.min.js') }}"></script>
