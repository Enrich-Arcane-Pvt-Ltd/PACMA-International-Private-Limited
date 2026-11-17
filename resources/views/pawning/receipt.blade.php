<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <style>
        @page {
            width: 217mm;
            /* height: 280mm; */
            margin: 0mm;
        }
        html, body {
            width: 217mm;
            height: 139mm;
            margin: 0;
            padding: 0;
            background-color: rgb(235, 235, 235);
        }
        .container {
            width: inherit;
            height: inherit;
            margin-left: 50%;
            margin-top: 15%;
            padding: 0;
            background: url('{{ asset("assets/receipt/receipt_image.jpg") }}');
            background-repeat: no-repeat;
            background-size: 217mm 139mm;
        }
        .content {
            width: 217mm;
            height: 139mm;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="width: 217mm; height: 139mm; display: flex; flex-wrap: wrap;">
            {{-- <div style="margin-top: 16mm;"> --}}
            {{-- </div> --}}
            <div style="margin-top: 15mm;">
                <div style="width: 50mm; height:5mm; margin-left: 130mm; text-align: right; font-size: 14px;">
                    @php
                        $receipCode = mb_substr($receipt->receipt_code, 0, 3);
                    @endphp
                    @if ($receipCode == 'DPR')
                        <span>Document charges : Rs. {{ $receipt->extra_charges }}</span>
                    @endif
                </div>
                {{-- Top section --}}
                <div style="display: flex; flex-wrap: wrap; margin-top: 13.5mm;">
                    <div style="width: 37mm; height: 11mm; margin-left:32mm; margin-top: 7mm;">
                        <div style="width: 37mm; height:5mm; text-align:center;">{{ \Auth::user()->pawningNumberFormat($receipt->pawnings->id) ?? '--' }}</div>
                        <div style="width: 37mm; height:5mm; margin-top: 0.5mm; text-align:center;">{{ \Auth::user()->customerNumberFormat($receipt->customer_id) ?? '--' }}</div>
                    </div>
                    <div style="width: 45mm; margin-left:99mm; height:29mm;">
                        <div style="height:5mm; text-align:center;">{{ $receipt->receipt_code ?? '--' }}</div>
                        <div style="height:5mm; margin-top: 0.5mm; text-align:center;">{{ \Auth::user()->invoiceNumberFormat($receipt->invoice_id) ?? '--' }}</div>
                        <div style="height:5mm; margin-top: 0.5mm; text-align:center;">{{ $receipt->issue_date ?? '--' }}</div>
                        <div style="height:5mm; margin-top: 0.5mm; text-align:center;">{{ $user->name ?? '--' }}</div>
                        <div style="height:5mm; margin-top: 0.5mm; text-align:center;">{{ \Auth::user()->userNumberFormat($receipt->created_by) ?? '--' }}</div>
                    </div>
                </div>

                {{-- Detail Section --}}
                <div style="width: 170mm; height:5mm; margin-left:40mm; margin-top:4.5mm; text-align:center;">{{ $receipt->customer->name ?? '--' }}</div>
                <div style="width: 170mm; height:5mm; margin-left:40mm; margin-top:1mm; text-align:center;">{{ $receipt->paid_amount_in_words ?? '--' }}</div>
                <div style="width: 55mm; height:6mm; margin-left:15mm; margin-top:9.5mm; text-align:center; font-weight: bold;">{{ number_format($receipt->paid_amount + $receipt->card_charge_amount, 2) }}</div>

                {{-- payment method --}}
                <div style="display: flex; flex-wrap: wrap; margin-top: 9mm;">
                    <div style="width: 28mm; height: 4mm; text-align:center; font-size: 11px; margin-left:66mm">@if ($receipt->payment_method == 'CASH') CASH @endif </div>
                    <div style="width: 28mm; height: 4mm; text-align:center; font-size: 11px;">@if ($receipt->payment_method == 'CHEQUE') CHEQUE @endif </div>
                    <div style="width: 28mm; height: 4mm; text-align:center; font-size: 11px;">@if ($receipt->payment_method == 'CARD') CARD @endif </div>
                    <div style="width: 28mm; height: 4mm; text-align:center; font-size: 11px;">@if ($receipt->payment_method == 'ONLINEPAYMENT') ONLINEPAYMENT @endif </div>
                </div>
            </div>
        </div>
    </div>
    <div id="reciept" style="display: none;">
        <div style="width: 217mm; height: 139mm; display: flex; flex-wrap: wrap;">
            <div style="margin-top: 13mm;">
                <div style="width: 50mm; height:5mm; margin-left: 136mm; text-align: right; font-size: 14px;">
                    @if ($receipCode == 'DPR')
                        <span>Document charges : Rs. {{ $receipt->extra_charges }}</span>
                    @endif
                </div>
                {{-- Top section --}}
                <div style="display: flex; flex-wrap: wrap; margin-top: 15mm;">
                    <div style="width: 37mm; height: 11mm; margin-left:24mm; margin-top: 7mm;">
                        <div style="width: 37mm; height:5mm; text-align: center;">{{ \Auth::user()->pawningNumberFormat($receipt->pawnings->id) ?? '--' }}</div>
                        <div style="width: 37mm; height:5mm; margin-top: 1mm; text-align: center;">{{ \Auth::user()->customerNumberFormat($receipt->customer_id) ?? '--' }}</div>
                    </div>
                    <div style="width: 38.5mm; margin-left: 117mm; height:29mm;">
                        <div style="height:5mm; text-align: center;">{{ $receipt->receipt_code ?? '--' }}</div>
                        <div style="height:5mm; margin-top: 1mm; text-align: center;">{{ \Auth::user()->invoiceNumberFormat($receipt->invoice_id) ?? '--' }}</div>
                        <div style="height:5mm; margin-top: 1mm; text-align: center;">{{ $receipt->issue_date ?? '--' }}</div>
                        <div style="height:5mm; margin-top: 1mm; text-align: center;">{{ $user->name ?? '--' }}</div>
                        <div style="height:5mm; margin-top: 1mm; text-align: center;">{{ \Auth::user()->userNumberFormat($receipt->created_by) ?? '--' }}</div>
                    </div>
                </div>

                {{-- Detail Section --}}
                <div style="width: 170mm; height:5mm; margin-left:40mm; margin-top:8.5mm; text-align: center;">{{ $receipt->customer->name ?? '--' }}</div>
                <div style="width: 170mm; height:5mm; margin-left:40mm; margin-top:1mm; text-align: center;">{{ $receipt->paid_amount_in_words ?? '--' }}</div>
                <div style="width: 55mm; height:6mm; margin-left:15mm; margin-top:11mm; text-align: center; font-weight: bold;">{{ number_format($receipt->paid_amount + $receipt->card_charge_amount, 2) }}</div>

                {{-- payment method --}}
                <div style="display: flex; flex-wrap: wrap; margin-top: 10.5mm;">
                    <div style="width: 28mm; height: 4mm; text-align:center; font-size: 11px; margin-left:66mm">@if ($receipt->payment_method == 'CASH') CASH @endif </div>
                    <div style="width: 28mm; height: 4mm; text-align:center; font-size: 11px;">@if ($receipt->payment_method == 'CHEQUE') CHEQUE @endif </div>
                    <div style="width: 28mm; height: 4mm; text-align:center; font-size: 11px;">@if ($receipt->payment_method == 'CARD') CARD @endif </div>
                    <div style="width: 28mm; height: 4mm; text-align:center; font-size: 11px;">@if ($receipt->payment_method == 'ONLINEPAYMENT') ONLINEPAYMENT @endif </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
