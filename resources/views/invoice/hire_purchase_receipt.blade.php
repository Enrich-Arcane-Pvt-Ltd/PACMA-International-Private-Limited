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
            height: 22mm;
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
        <div class="logo-container" style="margin-left: 30px; margin-right: 30px">
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
                                <td>{{ \Auth::user()->userNumberFormat($invoice->created_by) ?? '--' }}</td>

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
                                    FRIST PAYMENT RECEIPT
                                @elseif($invoice->print_status == 2 || $invoice->print_status == 0)
                                    COPY OF FRIST PAYMENT RECEIPT
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
                        @if (!empty($invoice->customer))
                            <table class="details-table">
                                <tr>
                                    <th>Name :</th>
                                    <td>
                                        @if (isset($invoice->customer->billing_title) || isset($invoice->customer->title))
                                            @php
                                                $title = $invoice->customer->billing_title ?? $invoice->customer->title;
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
                                    <td>{{ $invoice->customer->billing_phone ?? ($invoice->customer->phone_mobile ?? '') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>NIC :</th>
                                    <td>{{ $invoice->customer->nic_no ?? '--' }}</td>
                                </tr>
                            </table>
                        @endif
                    </div>

                    <div>
                        <table class="details-table">
                            <tr>
                                <th>Account No :</th>
                                <td>{{ $invoice->invoice_code ?? '--' }}</td>
                            </tr>
                            <tr>
                                <th>Receipt No :</th>
                                <td>{{ \Auth::user()->invoiceNumberFormat($invoice->id) ?? '--' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="product-details">
                    <table>
                        <thead>
                            <tr>
                                <th style="text-align: left;">Product Code</th>
                                <th style="text-align: left;">Description</th>
                                <th class="text-center">Qty</th>
                                <th style="text-align: left;">Serial Number</th>
                                <th class="text-right">Discount ({{ $currencySymbol }})</th>
                                <th class="text-right">Price ({{ $currencySymbol }})</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td style="text-align: left;">{{ $invoice->sales->products->barcode }}</td>
                                <td style="text-align: left;">{{ $invoice->sales->products->name }} </td>
                                <td class="text-center">{{ $invoice->sales->quantity }}</td>
                                <td style="text-align: left;">{{ $hirePurchaseDetail->warranty->serial_number ?? '' }}
                                </td>
                                <td class="text-right">{{ number_format($invoice->sales->discount ?? 0, 2) }}</td>
                                <td class="text-right">{{ number_format($invoice->sales->gross_hire_price ?? 0, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="8" style="padding: 5px;"></td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="4">
                                    <table style="width: 100%;">
                                        <tr>
                                            <th style="text-align: left;">Warranty Card No :
                                                {{ $hirePurchaseDetail->warranty->warranty_number ?? '' }}</th>
                                            <th style="text-align: right;">
                                                @if ($advancePaymentDetail)
                                                    Advance Payment Date : {{ $advancePaymentDetail->advance_date ?? '' }}
                                                @endif
                                            </th>
                                        </tr>

                                        <tr style="text-align: left;">
                                            <th colspan="2">
                                                <p>{{ $hirePurchaseDetail->additional_warranty_description ?? '' }}</p>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                            </tr>
                        </tfoot>

                        <table>
                            <tr>
                                <th style="width: 40%;">
                                    <table style="text-align: left;">
                                        <tr>
                                            <td class="text-left">Cash Price :</td>
                                            <td class="text-right">{{ $currencySymbol }}
                                                {{ number_format($invoice->sales->net_amount ?? 0, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Gross Hire Price :</td>
                                            <td class="text-right">{{ $currencySymbol }}
                                                {{ number_format($invoice->sales->gross_hire_price ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Advance Payment :</td>
                                            <td class="text-right">{{ $currencySymbol }}
                                                {{ number_format($invoice->sales->advance_amount ?? 0, 2) }}</td>
                                        </tr>

                                        @php
                                            $total =
                                                $invoice->sales->down_payment +
                                                $invoice->sales->traveling_charges +
                                                $invoice->sales->document_charges;
                                        @endphp
                                        <tr>
                                            <td class="text-left">Amount Paid :</td>
                                            <td class="text-right">{{ $currencySymbol }}
                                                {{ number_format($total, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Payment Method :</td>
                                            <td class="text-right">{{ $invoice->sales->payment_method ?? '' }}
                                            </td>
                                        </tr>
                                    </table>
                                </th>
                                <th style="width: 20%;"></th>
                                <th style="width: 40%;">
                                    <table style="text-align: left;">
                                        <tr>
                                            <td class="text-left">Term :</td>
                                            <td class="text-right">
                                                {{ number_format($invoice->sales->installment ?? 0) }} Months</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Monthly Payment :</td>
                                            <td class="text-right">{{ $currencySymbol }}
                                                {{ number_format($invoice->sales->monthly_installment ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Agreed to Pay Date :</td>
                                            <td class="text-right">
                                                {{ isset($invoice->sales->installment_date) ? \Auth::user()->ordinal($invoice->sales->installment_date) . ' Day' : '-- Day' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Transport charges :</td>
                                            <td class="text-right">{{ $currencySymbol }}
                                                {{ number_format($invoice->sales->traveling_charges ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Document charges :</td>
                                            <td class="text-right">{{ $currencySymbol }}
                                                {{ number_format($invoice->sales->document_charges ?? 0, 2) }}</td>
                                        </tr>
                                    </table>
                                </th>
                            </tr>
                        </table>
                    </table>
                    <div class="signature-box" style="margin-top: 30px;">
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
