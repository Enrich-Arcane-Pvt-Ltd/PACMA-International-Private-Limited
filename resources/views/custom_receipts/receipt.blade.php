<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

    <title>Receipt - {{ $receipt->receipt_code ?? 'PACMA International' }}</title>

    <style>
        @page {
            width: 216mm;
            height: 140mm;
            margin: 0mm;
        }

        * {
            box-sizing: border-box;
            font-family: 'Lato', sans-serif;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            line-height: 15px;
            color: #212529;
            background-color: transparent;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            width: 216mm;
            height: 140mm;
            padding: 12px 25px;
            background-color: #ffffff;
            position: relative;
        }

        .content {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .logo-container {
            width: 100%;
            margin-bottom: 6px;
        }

        .logo-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-title {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0 0 2px 0;
        }

        .details-table {
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            font-size: 11.5px;
            text-align: left;
            padding: 1.5px 0;
            vertical-align: top;
        }

        .details-table th {
            font-weight: 700;
            white-space: nowrap;
        }

        .details-table td {
            padding-left: 6px;
        }

        .invoice-body {
            width: 100%;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            width: 100%;
            border-top: 1px dashed #b8b8b8;
            border-bottom: 1px dashed #b8b8b8;
            padding: 6px 0;
        }

        .party-col {
            width: 58%;
        }

        .meta-col {
            width: 40%;
        }

        .payment-section {
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 6px 0 4px 0;
        }

        .payment-description-col {
            width: 55%;
        }

        .payment-amount-col {
            width: 42%;
        }

        .payment-details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payment-details-table th {
            font-size: 11.5px;
            text-align: left;
            padding: 2px 0;
            white-space: nowrap;
        }

        .payment-details-table td {
            font-size: 11.5px;
            text-align: right;
            padding: 2px 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        p {
            font-size: 11.5px;
            margin: 1px 0;
        }

        .dotted-line {
            border: none;
            border-top: 1px dashed #333333;
            color: transparent;
            background-color: transparent;
            height: 1px;
            width: 100%;
            margin-bottom: 4px;
        }

        .signature-box {
            width: 100%;
            margin-top: 10px;
        }

        .signature-row {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .signature-col {
            width: 32%;
        }

        .signature-col p {
            font-size: 11px;
            font-weight: 600;
        }

        .btn-print {
            padding: 8px 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 4px;
            background-color: #5864ff;
            border: none;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-print:hover {
            background-color: #4351e8;
            color: #ffffff;
        }

        .btn-back {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 4px;
            background-color: #e2e8f0;
            border: none;
            color: #334155;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-back:hover {
            background-color: #cbd5e1;
            color: #1e293b;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background-color: transparent !important;
            }

            .container {
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
        }

        @media screen {
            body {
                background-color: #f1f5f9;
                padding: 24px 0 40px 0;
            }

            .no-print {
                margin-bottom: 16px;
                display: flex;
                gap: 12px;
                justify-content: center;
            }

            .container {
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
                border: 1px solid #e2e8f0;
                border-radius: 6px;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <a href="{{ url('custom_receipt') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Receipts
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Print Receipt
        </button>
    </div>

    <div class="container" id="printable-receipt">
        <div class="content">
            <!-- Header Section -->
            <div class="logo-container">
                <table>
                    <tr>
                        <td style="width: 28%; vertical-align: top;">
                            <table class="details-table">
                                <tr>
                                    <th>Run Date</th>
                                    <td>: {{ now()->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>Receipt Date</th>
                                    <td>: {{ $receipt->issue_date ?? now()->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>: {{ $user->name ?? (\Auth::user()->userNumberFormat($receipt->created_by) ?? '--') }}</td>
                                </tr>
                            </table>
                        </td>

                        <td class="text-center" style="width: 44%; vertical-align: top;">
                            <img src="{{ asset('storage/uploads/logo/logo.png') }}" alt="PACMA International" style="max-height: 48px; max-width: 180px; object-fit: contain;">
                            <p style="font-weight: 700; font-size: 12.5px; margin-top: 3px; letter-spacing: 0.3px;">PACMA INTERNATIONAL PRIVATE LIMITED</p>
                            <p>No: 61, K C De Silvapuraya, Thimbirigaskatuwa</p>
                            <p>031-2222206</p>
                        </td>

                        <td style="width: 28%; text-align: right; vertical-align: top;">
                            <div class="header-title">
                                @if (strtoupper($receipt->payment_method) == 'CASH')
                                    CASH RECEIPT
                                @else
                                    RECEIPT
                                @endif
                            </div>
                            <p style="font-size: 12px; font-weight: 700; color: #1e293b; margin-top: 2px;">
                                {{ $receipt->receipt_code ?? '--' }}
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Party & Metadata Details -->
            <div class="invoice-body">
                <div class="invoice-details">
                    <div class="party-col">
                        <table class="details-table">
                            <tr>
                                <th>Received From</th>
                                <td>:
                                    <strong>
                                        @if ($receipt->receipt_type == 'supplier')
                                            {{ $receipt->vender->name ?? '--' }}
                                        @elseif ($receipt->receipt_type == 'customer')
                                            @if (isset($receipt->customer->billing_title) || isset($receipt->customer->title))
                                                @php
                                                    $title = $receipt->customer->billing_title ?? $receipt->customer->title;
                                                    $titles = ['Mr.', 'Mrs.', 'Miss.'];
                                                @endphp
                                                @if (isset($titles[$title]))
                                                    {{ $titles[$title] }}
                                                @endif
                                            @endif
                                            {{ $receipt->customer->billing_name ?? ($receipt->customer->name ?? '--') }}
                                        @elseif ($receipt->receipt_type == 'other')
                                            {{ $receipt->receipt_from ?? '--' }}
                                        @elseif ($receipt->receipt_type == 'cash')
                                            Cash
                                        @else
                                            --
                                        @endif
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>:
                                    @if ($receipt->receipt_type == 'customer' && !empty($receipt->customer))
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
                                    @elseif ($receipt->receipt_type == 'supplier' && !empty($receipt->vender))
                                        {{ $receipt->vender->billing_address ?? ($receipt->vender->address ?? '--') }}
                                    @else
                                        --
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Contact No</th>
                                <td>:
                                    @if ($receipt->receipt_type == 'supplier')
                                        {{ $receipt->vender->contact_phone ?? '--' }}
                                    @elseif ($receipt->receipt_type == 'customer' && !empty($receipt->customer))
                                        {{ $receipt->customer->billing_phone ?? ($receipt->customer->phone_mobile ?? ($receipt->customer->phone ?? '--')) }}
                                    @elseif ($receipt->receipt_type == 'other')
                                        {{ $receipt->receipt_from_phone ?? '--' }}
                                    @else
                                        --
                                    @endif
                                </td>
                            </tr>
                            @if ($receipt->receipt_type == 'customer' && !empty($receipt->customer->nic_no))
                                <tr>
                                    <th>NIC No</th>
                                    <td>: {{ $receipt->customer->nic_no }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    <div class="meta-col">
                        <table class="details-table" style="width: 100%;">
                            <tr>
                                <th>Receipt No</th>
                                <td>: <strong>{{ $receipt->receipt_code ?? '--' }}</strong></td>
                            </tr>
                            <tr>
                                <th>Receipt Type</th>
                                <td style="text-transform: capitalize;">: {{ $receipt->receipt_type ?? '--' }}</td>
                            </tr>
                            @if ($receipt->receipt_type == 'customer' && !empty($receipt->customer_id))
                                <tr>
                                    <th>Customer ID</th>
                                    <td>: {{ \Auth::user()->customerNumberFormat($receipt->customer_id) }}</td>
                                </tr>
                            @elseif ($receipt->receipt_type == 'supplier' && !empty($receipt->vender_id))
                                <tr>
                                    <th>Supplier ID</th>
                                    <td>: {{ \Auth::user()->venderNumberFormat($receipt->vender_id) }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Payment & Description Details -->
                <div class="payment-section">
                    <div class="payment-description-col">
                        <table class="details-table" style="width: 100%;">
                            <tr>
                                <th style="width: 85px;">Description</th>
                                <td>: {{ $receipt->description ?: 'Custom Receipt' }}</td>
                            </tr>
                            @if (!empty($receipt->paid_amount_in_words))
                                <tr>
                                    <th>The Sum of</th>
                                    <td style="font-style: italic;">: {{ $receipt->paid_amount_in_words }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    <div class="payment-amount-col">
                        <table class="payment-details-table">
                            <tr>
                                <th>Amount Paid :</th>
                                <td><strong>Rs. {{ number_format($receipt->paid_amount ?? 0, 2) }}</strong></td>
                            </tr>
                            @if (!empty($receipt->card_charge_amount) && $receipt->card_charge_amount > 0)
                                <tr>
                                    <th>Card Charge :</th>
                                    <td>Rs. {{ number_format($receipt->card_charge_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Paid :</th>
                                    <td><strong>Rs. {{ number_format($receipt->paid_amount + $receipt->card_charge_amount, 2) }}</strong></td>
                                </tr>
                            @endif
                            <tr>
                                <th>Payment Method :</th>
                                <td>
                                    {{ $receipt->payment_method ?? 'CASH' }}
                                    @if ($receipt->payment_method == 'CHEQUE' && !empty($receipt->cheque_number))
                                        (#{{ $receipt->cheque_number }})
                                    @elseif ($receipt->payment_method == 'CARD' && !empty($receipt->card_type))
                                        ({{ $receipt->card_type }})
                                    @endif
                                </td>
                            </tr>
                            @if ($receipt->payment_method == 'CHEQUE' && !empty($receipt->cheque_realised_date))
                                <tr>
                                    <th>Realised Date :</th>
                                    <td>{{ $receipt->cheque_realised_date }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="signature-box">
                <div class="signature-row">
                    <div class="signature-col">
                        <hr class="dotted-line">
                        <p class="text-center">{{ __('Prepared By') }}</p>
                    </div>
                    <div class="signature-col">
                        <hr class="dotted-line">
                        <p class="text-center">{{ __('Customer / Payee') }}</p>
                    </div>
                    <div class="signature-col">
                        <hr class="dotted-line">
                        <p class="text-center">{{ __('Authorized By') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        var command = {!! json_encode($command ?? 'view') !!};
        $(document).ready(function() {
            if (command === 'print') {
                window.print();
            }
        });
    </script>
</body>

</html>
