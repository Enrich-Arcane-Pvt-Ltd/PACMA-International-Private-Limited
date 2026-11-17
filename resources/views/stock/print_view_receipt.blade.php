<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Lato&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

    <style type="text/css">.resize-observer[data-v-b329ee4c] {
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
        }</style>
    <style type="text/css">p[data-v-f2a183a6] {
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
        }

        .d-body[data-v-f2a183a6] {
            padding-top: 0px;
            padding-right: 50px;
            padding-bottom: 50px;
            padding-left: 50px;
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
            padding: 35px;
        }

        .d-header-brand[data-v-f2a183a6] {
            width: 200px;
        }

        .d-logo[data-v-f2a183a6] {
            max-width: 100%;
        }</style>
    <style type="text/css">p[data-v-37eeda86] {
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
        }</style>
    <style type="text/css">p[data-v-e95a8a8c] {
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
        }</style>
    <style type="text/css">p[data-v-363339a0] {
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
        }</style>
    <style type="text/css">p[data-v-e23d9750] {
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
        }</style>
    <style type="text/css">p[data-v-4b3dcb8a] {
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
        }</style>
    <style type="text/css">p[data-v-1ad6e3b9] {
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
        }</style>
    <style type="text/css">p[data-v-136bf9b5] {
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
        }</style>
    <style type="text/css">p[data-v-7d9d14b5] {
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
        }</style>
    <style type="text/css">p[data-v-b8f60a0c] {
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
        .centerText{
            text-align:center;
        }

        .textNoWrap{
            white-space: nowrap;
        }

    </style>

    @if(isset($print))
        <style>
            @page {
            size: A4;
            margin: 0;
            }
            @media print {
                html, body {
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
                        <div data-v-f2a183a6="" class="d" id="boxes" style="width:950px;margin-left: auto;margin-right: auto;">
                            <div data-v-f2a183a6="" class="d-header" style="background: {{$color}};color:{{$font_color}}; margin-bottom: -2.5rem;">
                                <div data-v-f2a183a6="" class="d-header-inner">
                                    <div data-v-f2a183a6="" class="d-header-75 row">
                                        <div data-v-f2a183a6="" class="d-header-brand">
                                            <img src="{{$img}}" style="max-width: 150px"/>
                                        </div>
                                        <div data-v-f2a183a6="" style="padding-top: 1rem;">
                                            <p data-v-f2a183a6="" style="font-weight:bold; font-size:18px;">
                                                {{ config('app.name') }}
                                            </p>
                                            @include('components.headOfficeBlock')
                                            {{-- <p>Head Office : No.51, Thammita Road, Negombo.</p>
                                            <p>Tel : 031-2230288 E-mail : info@com</p>
                                            <p><span>Banch : </span><span>{{ $transfers->branch->name ?? ''}}</span></p> --}}
                                        </div>
                                            {{-- {{!empty($purchase_order->branch->branch_manager)?$purchase_order->branch->branch_manager:''}}<br>
                                            {{!empty($purchase_order->branch->name)?$purchase_order->branch->name:''}}<br>
                                            {{!empty($purchase_order->branch->address)?$purchase_order->branch->address:''}}<br> --}}
                                    </div>

                                    <div data-v-f2a183a6="" class="d-header-50 d-right">
                                        <div data-v-f2a183a6="" style="font-size:30px;" class="d-title">{{__('Stock Transfer')}}</div>
                                    </div>
                                </div>
                            </div>

                            <div data-v-f2a183a6="" class="d-body">
                                <div data-v-f2a183a6="" class="d-bill-to">

                                    <div class="row">
                                        <div data-v-f2a183a6="" class="d-table" style="width: 50%;">
                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}; height: 30px;; height: 30px;">
                                                <div class="d-table-th w-11">{{__('Transfer From')}}</div>
                                                <div class="d-table-td w-2">:</div>
                                                <div class="d-table-td w-11">{{ $transfers->branch->name ?? ''}}</div>
                                            </div>
                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}; height: 30px;">
                                                <div class="d-table-th w-11">{{__('Transfer To')}}</div>
                                                <div class="d-table-td w-2">:</div>
                                                <div class="d-table-td w-11">{{ $transfers->branchto->name ?? ''}}</div>
                                            </div>
                                        </div>
                                        <div data-v-f2a183a6="" class="d-table" style="width: 50%;">
                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}; height: 30px;">
                                                <div class="d-table-th w-11 text-right">{{__('Transfer No')}}</div>
                                                <div class="d-table-td w-2 centerText">:</div>
                                                <div class="d-table-td w-11">{{ \Auth::user()->stockTransferNumberFormat($transfers->id) }}</div>
                                            </div>
                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}; height: 30px;">
                                                <div class="d-table-th w-11 text-right">{{__('Transfer Date')}}</div>
                                                <div class="d-table-td w-2 centerText">:</div>
                                                <div class="d-table-td w-11">{{ $transfers->date }}</div>
                                            </div>
                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}; height: 30px;">
                                                <div class="d-table-th w-11 text-right">{{__('Time')}}</div>
                                                <div class="d-table-td w-2 centerText">:</div>
                                                <div class="d-table-td w-11">{{ $transfers->created_at->format('h:i a') }}</div>
                                            </div>
                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}; height: 30px;">
                                                <div class="d-table-th w-11 text-right">{{__('Transfer Name')}}</div>
                                                <div class="d-table-td w-2 centerText">:</div>
                                                <div class="d-table-td w-11">{{ $user->name }}</div>
                                            </div>
                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}; height: 30px;">
                                                <div class="d-table-th w-11 text-right">{{__('User ID')}}</div>
                                                <div class="d-table-td w-2 centerText">:</div>
                                                <div class="d-table-td w-11">{{ \Auth::user()->userNumberFormat($user->id) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                        <div data-v-f2a183a6="" class="d-table" style="margin-top: 1.5rem;">

                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}">
                                                <div class="d-table-th w-3">{{__('Code')}}</div>
                                                <div class="d-table-th w-6">{{__('Product Type')}}</div>
                                                <div class="d-table-th w-6">{{__('Item Description')}}</div>
                                                <div class="d-table-th w-3 centerText">{{__('Quantity')}}</div>
                                                <div class="d-table-th w-3 centerText">{{__('Purchasing Price')}}</div>
                                                <div class="d-table-th w-3 text-right">{{__('Total')}}</div>
                                            </div>
                                            <div class="d-table-body">
                                                {{-- @if(isset($transfers->stockTransItems) && count($transfers->stockTransItems) > 0) --}}
                                                    @foreach($transfers->stockTransItems as $key => $item)
                                                        <div class="d-table-tr" style="border-bottom: 1px solid black;">
                                                            <div class="d-table-td w-3">
                                                                <pre data-v-f2a183a6="">{{ \Auth::user()->productNumberFormat($item->product_id) ?? '--' }}</pre>
                                                            </div>
                                                            <div class="d-table-td w-6">
                                                                <pre data-v-f2a183a6="">{{ $item->products->productCategory->name ?? '--' }}</pre>
                                                            </div>
                                                            <div class="d-table-td w-6">
                                                                <pre data-v-f2a183a6="">{{ $item->products->name ?? '' }}</pre>
                                                            </div>

                                                            <div class="d-table-td w-3 centerText textNoWrap">
                                                                <pre data-v-f2a183a6="">{{ $item->quantity ?? '' }}</pre>
                                                            </div>
                                                            <div class="d-table-td w-3 centerText textNoWrap">
                                                                <pre data-v-f2a183a6="">{{ number_format($item->purchase_price, 2) }}</pre>
                                                            </div>
                                                            <div class="d-table-td w-3 text-right textNoWrap"><span>{{ number_format($item->amount, 2) }}</span></div>
                                                        </div>
                                                    @endforeach
                                                {{-- @else
                                                    <div class="d-table-tr" style="border-bottom:1px solid {{$color}};">
                                                        <div class="d-table-td w-6"><span>-</span></div>
                                                        <div class="d-table-td w-6">
                                                            <pre data-v-f2a183a6="">-</pre>
                                                        </div>
                                                        <div class="d-table-td w-6">
                                                            <pre data-v-f2a183a6="">-</pre>
                                                        </div>
                                                        <div class="d-table-td w-6 text-right"><span>-</span></div>
                                                    </div>
                                                @endif --}}
                                            </div>
                                            <div data-v-f2a183a6="" class="d-table-footer">
                                                <div data-v-f2a183a6="" class="d-table-controls"></div>
                                                <div data-v-f2a183a6="" class="d-table-summary">
                                                    {{-- <div data-v-f2a183a6="" class="d-table-summary-item">
                                                        <div data-v-f2a183a6="" class="d-table-label">{{__('Ta')}}:</div>
                                                        <div data-v-f2a183a6="" class="d-table-value">{{\App\Utility::priceFormat($settings,$purchase_order->total_amount)}}</div>
                                                    </div> --}}
                                                    <div data-v-f2a183a6="" class="d-table-summary-item" style="border-top: 1px solid {{$color}}; border-bottom: 1px solid {{$color}};">
                                                        <div data-v-f2a183a6="" class="d-table-label">{{__('Total')}}:</div>
                                                        <div data-v-f2a183a6="" class="d-table-value">{{ number_format($transfers->total_amount, 2)}}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div data-v-f2a183a6="" class="d-header-50">
                                                <p data-v-f2a183a6="">
                                                    {{$settings['footer_title']}} <br>
                                                    {{$settings['footer_notes']}}
                                                </p>
                                            </div>
                                        </div>
                                    <div class="row">
                                        <div data-v-f2a183a6="" class="d-table" style="width: 65%;">
                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}; height: 30px;; height: 30px;">
                                                <div class="d-table-td w-8 centerText">{{__('......................................')}}</div>
                                                <div class="d-table-td w-8 centerText">{{__('......................................')}}</div>
                                                <div class="d-table-td w-8 centerText">{{__('......................................')}}</div>
                                            </div>
                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}; height: 30px;">
                                                <div class="d-table-td w-8 centerText">{{__('Transfer By')}}</div>
                                                <div class="d-table-td w-8 centerText">{{__('Checked By')}}</div>
                                                <div class="d-table-td w-8 centerText">{{__('Approved By')}}</div>
                                            </div>
                                        </div>
                                        <div data-v-f2a183a6="" class="d-table" style="width: 35%;">
                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}; height: 40px; border-bottom:3px solid;">
                                                <div class="d-table-th w-24  centerText">{{__('Accepted By')}}</div>
                                            </div>
                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}; height: 40px;">
                                                <div class="d-table-th w-8">{{__('Name')}}</div>
                                                <div class="d-table-td w-2">:</div>
                                                <div class="d-table-td w-14" style="border-bottom:2px dotted;"></div>
                                            </div>
                                            <div data-v-f2a183a6="" class="d-table-tr" style="background: {{$color}};color:{{$font_color}}; height: 30px;">
                                                <div class="d-table-th w-8">{{__('Signature')}}</div>
                                                <div class="d-table-td w-2">:</div>
                                                <div class="d-table-td w-14" style="border-bottom:2px dotted;"></div>
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
    </div>
</div>
@if($command == 'print')
    @include('stock.print_script');
@endif

</body>
</html>



