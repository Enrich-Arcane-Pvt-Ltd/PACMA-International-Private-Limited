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
            height: 20mm;
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
            height: 26mm;
            padding: 0.5rem;
        }

        h3 {
            margin: 0.5rem 0;
            font-size: 16px;
        }

        .details-table th,
        .details-table td {
            font-size: 12px;
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
            font-size: 12px;
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
            font-size: 12px;
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
    <div class="container" id="app">
        <div class="logo-container" style="margin-left: 30px; margin-right: 30px;">
            <table>
                <tr>
                    <td style="width: 25%; text-align: left; vertical-align: top;">
                        <table class="details-table">
                            <tr>
                                <th>Run Date :</th>
                                <td>{{ now()->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Receipt Date :</th>
                                <td>{{ $invoice->issue_date }}</td>
                            </tr>
                            <tr>
                                <th>User :</th>
                                <td>{{ \Auth::user()->userNumberFormat($invoice->created_by) ?? '' }}</td>
                            </tr>
                        </table>
                    </td>

                    <td class="text-center" style="width: 50%; vertical-align: top;">
                        <img src="{{ asset('storage\uploads\logo\logo.png') }}" alt="" width="30%"
                            style="">
                        <p>No: 61, K C De Silvapuraya, Thimbirigaskatuwa</p>
                        <p>031-2222206</p>
                    </td>

                    <td style="width: 25%; text-align: right; vertical-align: top;">

                        <p style="font-size: 13px">
                            <b>
                                @if ($invoice->print_status == 1 || $invoice->print_status == 3)
                                    CASH RECEIPT
                                @elseif($invoice->print_status == 2 || $invoice->print_status == 0)
                                    COPY OF CASH RECEIPT
                                @endif
                            </b>
                        </p>

                    </td>
                </tr>
            </table>
        </div>
        <div class="invoice-container">
            <div class="left-margin"></div>

            <div class="invoice-body">
                <div class="invoice-details">
                    <div>
                        @if ($invoice->sales->customer_type == 2)
                            <table class="details-table">
                                <tr>
                                    <th>Customer Name :</th>
                                    <td>
                                        {{ $invoice->sales->customer_name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Address :</th>
                                    <td>{{ $invoice->sales->customer_address ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Contact No :</th>
                                    <td>{{ $invoice->sales->customer_phone_no ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>NIC :</th>
                                    <td>{{ $invoice->sales->customer_nic ?? '' }}</td>
                                </tr>
                            </table>
                        @else
                            @if (!empty($invoice->customer))
                                <table class="details-table">
                                    <tr>
                                        <th>Name :</th>
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
                                        <th>Address :</th>
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
                                        <th>Contact No :</th>
                                        <td>
                                            {{ $invoice->customer->billing_phone ?? ($invoice->customer->phone_mobile ?? '') }}
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        @endif
                    </div>

                    <div>
                        <table class="details-table">
                            <tr>
                                <th>Account No :</th>
                                <td>{{ $invoice->invoice_code ?? '' }}</td>
                            </tr>
                            <tr>
                                <th>Receipt No :</th>
                                <td>{{ \Auth::user()->invoiceNumberFormat($invoice->id) ?? '' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                @php
                    $total = $invoice->sales->net_amount + $invoice->sales->traveling_charges;
                @endphp

                <div class="product-details">
                    <table>
                        <thead>
                            <tr>
                                <th style="text-align: left;">Product Code</th>
                                <th style="text-align: left;">Description</th>
                                <th style="text-align: left;">Warranty</th>
                                <th style="text-align: left;">Serial Number</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right" style="white-space: nowrap; padding-right: 5px">Purchasing<br />
                                    Price
                                    ({{ $currencySymbol }})</th>
                                <th class="text-right" style="white-space: nowrap">Discount ({{ $currencySymbol }})
                                </th>
                                <th width="12%" class="text-right" style="white-space: nowrap">Amount
                                    ({{ $currencySymbol }})</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($invoice->sales->saleItems as $saleItem)
                                <tr>
                                    <td style="text-align: left;">{{ $saleItem->products->barcode }}</td>
                                    <td style="text-align: left;">{{ $saleItem->products->name }}</td>
                                    <td style="text-align: left;">
                                        {{ $saleItem->warranty->warranty_number ?? '-' }}<br>{{ $saleItem->additional_warranty_description ?? '' }}
                                    </td>
                                    <td style="text-align: left;">{{ $saleItem->warranty->serial_number ?? '-' }}</td>
                                    <td class="text-center">{{ $saleItem->quantity }}</td>
                                    <td class="text-right">
                                        {{ number_format($saleItem->unit_price ?? 0, 2) }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($saleItem->discount ?? 0, 2) }}</td>
                                    <td class="text-right">
                                        {{ number_format($saleItem->net_amount ?? 0, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>


                        <tfoot>
                            <tr>
                                <th style="border-top: 1px ridge #000000;" colspan="8"></th>

                            </tr>
                        </tfoot>
                        <table>
                            <tr>
                                <th style="width: 40%;">
                                    <table style="text-align: left;">
                                        <tr>
                                            <th class="text-left">Payment Method : </th>
                                            <th class="text-right">{{ $invoice->sales->payment_method ?? '' }}</th>
                                        </tr>
                                        @if ($advancePaymentDetail)
                                            <tr>
                                                <th class="text-left">Advance Payment Date : </th>
                                                <th class="text-right">
                                                    {{ $advancePaymentDetail->advance_date ?? '-n/a-' }}</th>
                                            </tr>
                                        @else
                                            <tr>
                                                <th colspan="2" style="height: 20px;"></th>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th colspan="2" style="height: 20px;"></th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" style="height: 20px;"></th>
                                        </tr>



                                    </table>
                                </th>
                                <th style="width: 20%;"></th>
                                <th style="width: 40%;">
                                    <table style="text-align: left;">
                                        <tr>
                                            <th class="text-right">Total
                                                Amount : </th>
                                            <th class="text-right">{{ number_format($total ?? 0, 2) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Advance Amount : </th>
                                            <th class="text-right">
                                                {{ number_format($invoice->sales->advance_amount ?? 0, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Transport charge : </th>
                                            <th class="text-right">
                                                {{ number_format($invoice->sales->traveling_charges ?? 0, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Paid Amount : </th>
                                            <th class="text-right">
                                                {{ number_format($invoice->sales->paid_amount ?? 0, 2) }}</th>
                                        </tr>
                                    </table>
                                </th>
                            </tr>
                        </table>
                    </table>
                    {{-- <div style="margin-bottom: 30px">
                            <table>
                               <tbody style="
                               display: flex;
                               justify-content: space-between;
                               width: 50%;">
                                <tr style="text-align: left;">
                                    <th class="text-left">Cheque No:
                                        <p>Points Earned: </p>
                                    </th>
                                    <td class="">{{ $invoice->sales->remarks ?? '0' }}</td>
                    </tr>
                    <tr style="text-align: left;">
                        <th class="text-left">CC No:
                            <p>Points Balance: </p>
                        </th>
                        <td class="">{{ $invoice->sales->remarks ?? '0' }}</td>
                    </tr>
                    </tbody>
                    </table>
                </div> --}}
                    <div class="signature-box" style="margin-top: 30px;">
                        <div class="row d-flex justify-content-space-between">
                            <div class="col-6" style="width: 40%">
                                <hr class="dotted-line">
                                <p class="text-center">{{ __('Prepared By') }}</p>
                            </div>
                            <div class="col-6" style="width: 40%">
                                <hr class="dotted-line">
                                <p class="text-center">{{ __('Signature') }}</p>
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
