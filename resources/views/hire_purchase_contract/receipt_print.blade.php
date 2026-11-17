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
            width: 216mm;
            height: 140mm;
            margin: 0mm;
        }

        html,
        body {
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
            background-color: transparent;
            line-height: 15px;
            /* background-color: #ffd0d0; */
        }

        .container {
            width: 216mm;
            height: 140mm;
            margin: 55px 0;
            /* margin: 20px 0; */
            /* background-color: #ffd0d0; */
        }

        .content {
            width: 216mm;
        }

        .logo-container {
            /* margin-top: 20px; */
            /* width: 100%; */
            height: 25mm;
            background-color: transparent;
        }

        .invoice-container {
            display: flex;
            width: 100%;
            /* height: 113mm; */
        }

        .left-margin {
            width: 15mm;
            height: 100%;
            background-color: transparent;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            width: 205mm;
            height: 22mm;
            padding: 0.5rem;
        }

        h3 {
            margin: 0.5rem 0;
            font-size: 16px;
        }

        .details-table th,
        .details-table td {
            font-size: 11px;
            text-align: left;
        }

        .details-table td {
            padding-left: 0.5rem;
        }

        .product-details {
            padding: 0.5rem;
        }

        .product-details table th,
        .product-details table td {
            font-size: 11px;
            padding: 0.2rem;
            line-height: normal;
        }

        .product-details table thead th {
            border-top: 1px ridge #000000;
            border-bottom: 1px ridge #000000;
        }

        .text-center {
            text-align: center;
        }

        p {
            font-size: 11px;
            margin: 1px 0;
        }

        .justify-content-space-between {
            justify-content: space-between;
        }

        .dotted-line {
            border: none;
            border-top: 1px dashed black;
            color: transparent;
            background-color: transparent;
            height: 1px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div id="app" class="content">
            <div class="logo-container" style="margin-left: 30px; margin-right: 30px">
                <table>
                    <tr>
                        <td style="width: 22%; text-left;text-align: left;display: contents;">
                            <table class="details-table">

                                <tr>
                                    <th>Run Date </th>
                                    <th>:</th>
                                    <td>{{ now()->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>Receipt Date </th>
                                    <th>:</th>
                                    <td>{{ $receipt->issue_date ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <th>:</th>
                                    <td>{{ \Auth::user()->userNumberFormat($receipt->created_by) ?? '--' }}</td>
                                </tr>
                            </table>
                        </td>
                        <td class=" text-center" style="width: 58%;">
                            <img src="{{ asset('storage\uploads\logo\logo.png') }}" alt="" width="30%"
                                style="">
                            <p>No: 61, K C De Silvapuraya, Thimbirigaskatuwa</p>
                            <p>031-2222206</p>
                        </td>
                        <td style="width: 20%;text-left;text-align: right;display: contents;">
                            <p style="font-size: 13px"><b>COLLECTION RECEIPT</b></p>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="invoice-container">
                <div class="left-margin"></div>

                <div class="invoice-body">
                    <div class="invoice-details">
                        <div>
                            {{-- <h3>Customer Details</h3> --}}

                            {{-- @if ($invoice->sales->customer_type == 2) --}}
                            <table class="details-table">
                                <tr>
                                    <th>Customer Name</th>
                                    <th>:</th>
                                    <td>
                                        {{ $receipt->customer->name ?? '--' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <th>:</th>
                                    <td>
                                        @php
                                            $rcptHouseNo = !empty($receipt->customer->billing_house_no)
                                                ? trim($receipt->customer->billing_house_no)
                                                : (!empty($receipt->customer->house_no) ? trim($receipt->customer->house_no) : '');
                                            $rcptAddress = !empty($receipt->customer->billing_address)
                                                ? trim($receipt->customer->billing_address)
                                                : (!empty($receipt->customer->address) ? trim($receipt->customer->address) : '');
                                            $rcptFullAddress = $rcptAddress;
                                            if (!empty($rcptHouseNo)) {
                                                $cleanRcptHouseNo = trim($rcptHouseNo, ", \t\n\r\0\x0B");
                                                if (empty($rcptAddress)) {
                                                    $rcptFullAddress = $rcptHouseNo;
                                                } elseif ($cleanRcptHouseNo !== '' && stripos($rcptAddress, $cleanRcptHouseNo) === false) {
                                                    $formattedRcptHouseNo = rtrim($cleanRcptHouseNo, ',') . ',';
                                                    $rcptFullAddress = $formattedRcptHouseNo . ' ' . $rcptAddress;
                                                }
                                            }
                                        @endphp
                                        {{ $rcptFullAddress ?: '--' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Contact No</th>
                                    <th>:</th>
                                    <td>{{ $receipt->customer->phone_mobile ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th>NIC No</th>
                                    <th>:</th>
                                    <td>{{ $receipt->customer->nic_no ?? '--' }}</td>
                                </tr>
                            </table>
                            {{-- @else --}}
                            @if (!empty($invoice->customer))
                                <table class="details-table">
                                    <tr>
                                        <th>Name</th>
                                        <th>:</th>
                                        <td>
                                            @if (isset($invoice->customer->billing_title) || isset($invoice->customer->title))
                                                @php
                                                    $title =
                                                        $invoice->customer->billing_title ?? $invoice->customer->title;
                                                    $titles = ['Mr.', 'Mrs.', 'Miss.'];
                                                @endphp

                                                @if (isset($titles[$title]))
                                                    {{ $titles[$title] }}
                                                @endif
                                            @endif
                                            {{ $invoice->customer->billing_name ?? ($invoice->customer->name ?? '') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <th>:</th>
                                        <td>
                                            @php
                                                $receiptHouseNo = !empty($invoice->customer->billing_house_no)
                                                    ? trim($invoice->customer->billing_house_no)
                                                    : (!empty($invoice->customer->house_no) ? trim($invoice->customer->house_no) : '');
                                                $receiptAddress = !empty($invoice->customer->billing_address)
                                                    ? trim($invoice->customer->billing_address)
                                                    : (!empty($invoice->customer->address) ? trim($invoice->customer->address) : '');
                                                $receiptFullAddress = $receiptAddress;
                                                if (!empty($receiptHouseNo)) {
                                                    $cleanReceiptHouseNo = trim($receiptHouseNo, ", \t\n\r\0\x0B");
                                                    if (empty($receiptAddress)) {
                                                        $receiptFullAddress = $receiptHouseNo;
                                                    } elseif ($cleanReceiptHouseNo !== '' && stripos($receiptAddress, $cleanReceiptHouseNo) === false) {
                                                        $formattedReceiptHouseNo = rtrim($cleanReceiptHouseNo, ',') . ',';
                                                        $receiptFullAddress = $formattedReceiptHouseNo . ' ' . $receiptAddress;
                                                    }
                                                }
                                            @endphp
                                            {{ $receiptFullAddress }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Contact No</th>
                                        <th>:</th>
                                        <td>{{ $invoice->customer->billing_phone ?? ($invoice->customer->phone_mobile ?? '') }}
                                        </td>
                                    </tr>
                                </table>
                            @endif
                            {{-- @endif --}}
                        </div>

                        <div>
                            <table class="details-table">
                                <tr>
                                    <th>Receipt No</th>
                                    <th>:</th>
                                    <td>{{ $receipt->receipt_code ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th>Orig Account No</th>
                                    <th>:</th>
                                    <td>{{ $receipt->invoice->invoice_code ?? '--' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="product-details">
                        <table>
                            <thead>
                                <tr>
                                    <th class="text-left">Product Code</th>
                                    <th class="text-left">Description</th>
                                    <th class="text-left">Serial Number</th>
                                    <th class="text-right">Amount ({{ $currencySymbol }})</th>
                                    <th class="text-right">Discount ({{ $currencySymbol }})</th>
                                    <th class="text-right">Amount Received ({{ $currencySymbol }})</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td class="text-center">{{ $receipt->sales->products->barcode }}</td>
                                    <td class="text-left">{{ $receipt->sales->products->name }}</td>
                                    <td class="text-center">
                                        {{ $receipt->sales->hirePurchaseDetail->warranty->serial_number ?? '-' }}</td>
                                    <td class="text-right">
                                        {{ number_format($receipt->paid_amount ?? 0, 2) }}
                                        {{-- {{ \Auth::user()->priceFormat($receipt->paid_amount) }} --}}
                                    </td>
                                    <td class="text-right"> {{ \Auth::user()->priceFormat(0) }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($receipt->paid_amount ?? 0, 2) }}
                                        {{-- {{ \Auth::user()->priceFormat($receipt->paid_amount) }} --}}
                                    </td>
                                </tr>

                                @if ($receipt->description == 'Down Payment')
                                    <!-- Loop through receipts to display Document Charges if they exist -->
                                    @foreach ($receipt->sales->receipts as $subReceipt)
                                        @if ($subReceipt->description == 'Document Charges')
                                            <tr>
                                                <td></td>
                                                <td class="text-left" colspan="2">Document Charges</td>
                                                <td class="text-right">
                                                    {{ number_format($subReceipt->paid_amount ?? 0, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($subReceipt->paid_amount) }} --}}
                                                </td>
                                                <td class="text-right">-</td>
                                                <td class="text-right">
                                                    {{ number_format($subReceipt->paid_amount ?? 0, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($subReceipt->paid_amount) }} --}}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                <tr>
                                    <td colspan="8" style="padding: 0.5rem;"></td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="4">
                                        <table>
                                            <tr style="text-align: left;">
                                                <th>Warranty Card No :
                                                    {{ $receipt->sales->hirePurchaseDetail->warranty->warranty_number ?? '-' }}
                                                </th>
                                            </tr>

                                            <tr style="text-align: left;">
                                                <th>
                                                    <p>
                                                        {{ $receipt->sales->hirePurchaseDetail->additional_warranty_description ?? '' }}
                                                    </p>
                                                </th>
                                            </tr>
                                        </table>
                                    </th>
                                    <th colspan="4">
                                        <table>
                                            {{-- <tr class="text-right">
                                                <th>Penalty</th>
                                                <th>:</th>
                                                <td>{{ __('Rs.') }}
                                            {{ number_format($receipt->sales->installments->last()->fine, 2) }}

                                            {{ number_format(0, 2) }}
                                            </td>
                                </tr> --}}

                                            @if ($receipt->penalty_charge)
                                                <tr class="text-right">
                                                    <th>Installment Amount</th>
                                                    <th>:</th>
                                                    <td>
                                                        {{ \Auth::user()->priceFormat($receipt->paid_amount - $receipt->penalty_charge) }}
                                                    </td>
                                                </tr>

                                                <tr class="text-right">
                                                    <th>Penalty Charge</th>
                                                    <th>:</th>
                                                    <td>
                                                        {{ \Auth::user()->priceFormat($receipt->penalty_charge) }}
                                                    </td>
                                                </tr>
                                            @endif

                                            <tr class="text-right">
                                                <th>Total Amount Received</th>
                                                <th>:</th>
                                                <td>
                                                    @if ($receipt->description == 'Down Payment')
                                                        {{ \Auth::user()->priceFormat($receipt->paid_amount + $receipt->sales->receipts->where('description', 'Document Charges')->sum('paid_amount')) }}
                                                    @else
                                                        {{ \Auth::user()->priceFormat($receipt->paid_amount) }}
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr class="text-right">
                                                <th>Payment Method</th>
                                                <th>:</th>
                                                <td>{{ $receipt->payment_method }}</td>
                                            </tr>

                                            @if (isset($receipt->sales) && isset($receipt->sales->outstanding_amount))
                                                <tr class="text-right">
                                                    <th>Total Outstanding</th>
                                                    <th>:</th>
                                                    <td><strong>{{ \Auth::user()->priceFormat($receipt->sales->outstanding_amount) }}</strong></td>
                                                </tr>
                                            @endif
                                        </table>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="border-top: 1px ridge #000000;" colspan="5">
                                        <table>
                                            <tbody>
                                                <th>
                                                    <table>
                                                        <tr style="text-align: left;">
                                                            <th class="text-left"
                                                                style="font-weight: 100; width: 100px;">
                                                                Agreed to Pay
                                                                Date</th>
                                                            <th style="width: 50px;">:</th>
                                                            <td class="text-left" style="font-weight: 100;">
                                                                {{ isset($receipt->sales->installment_date) ? \Auth::user()->ordinal($receipt->sales->installment_date) . ' Day' : '-- Day' }}
                                                            </td>
                                                        </tr>
                                                        {{-- <tr style="text-align: left;">
                                                            <th class="text-left"
                                                                style="font-weight: 100; width: 100px;">
                                                                CC/Cheque No
                                                            </th>
                                                            <th>:</th>
                                                            <td class="text-left" style="font-weight: 100;">
                                                                {{ $receipt->sales->remarks ?? '0' }}</td>
                        </tr> --}}
                                                    </table>
                                                </th>
                                </tr>
                                </tbody>
                        </table>
                        </th>
                        <th style="border-top: 1px ridge #000000;" colspan="2">
                            <table>
                                <tr style="text-align: left;">
                                    <td class="text-left" style="font-weight: 100;">Account No</td>
                                    <td>:</td>
                                    <td class="text-left" style="font-weight: 100;">
                                        {{ $receipt->invoice->invoice_code ?? '--' }}</td>
                                </tr>
                                {{-- <tr style="text-align: left;">
                                    <td class="text-left" style="font-weight: 100;">Account Closed :</td>
                                    <td>:</td>
                                    <td class="text-left" style="font-weight: 100;">
                                        {{  '' }}</td>
                                </tr> --}}
                            </table>
                        </th>
                        </tr>
                        </tfoot>
                        </table>
                        <div class="signature-box" style="margin-top: 50px;">
                            <div class="row d-flex justify-content-space-between">
                                <div class="col-6" style="width: 40%">
                                    <hr class="dotted-line">
                                    <p class="text-center">{{ __('Prepared By') }}</p>
                                </div>
                                <div class="col-6" style="width: 40%">
                                    <hr class="dotted-line">
                                    <p class="text-center">{{ __('Authorized By') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="left-margin"></div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <script>
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
