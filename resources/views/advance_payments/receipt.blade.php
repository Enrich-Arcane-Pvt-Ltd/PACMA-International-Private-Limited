<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Lato&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

    <title>Receipt Print View</title>
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
            /* background-color: #cfcfcf; */
        }

        .container {
            width: 216mm;
            height: 140mm;
            margin: 55px 0;
            /* margin: 20px 0; */
            /* background-color: #ffffff; */
        }

        .content {
            width: 216mm;
        }

        .logo-container {
            /* margin-top: 20px; */
            /* width: 100%; */
            height: 20mm;
            background-color: transparent;
            margin-left: 30px;
            margin-right: 30px
        }

        .invoice-container {
            display: flex;
            /* height: 113mm; */
            margin-left: 30px;
            margin-right: 30px
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            width: 100%;
            height: 22mm;
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
        .advance-details {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
        }
        .payment-details-table th {
            font-size: 12px;
            text-align: left;
        }
        .payment-details-table td {
            font-size: 12px;
            text-align: right;
        }
        .product-details {
            margin-top: 3rem;
        }
    </style>
</head>

<body>
    <div class="container" id="app">
        <div class="logo-container">
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
                                <td>{{ $receipt->issue_date }}</td>
                            </tr>
                            <tr>
                                <th>User :</th>
                                <td>{{ \Auth::user()->userNumberFormat($receipt->created_by) ?? '--' }}</td>
                            </tr>
                        </table>
                    </td>

                    <td class="text-center" style="width: 50%; vertical-align: top;">
                        <img src="{{asset('storage\uploads\logo\logo.png')}}" alt="" width="30%" style="">
                        <p>No: 61, K C De Silvapuraya, Thimbirigaskatuwa</p>
                        <p>031-2222206</p>
                    </td>

                    <td style="width: 25%; text-align: right; vertical-align: top;">
                        <p style="font-size: 13px">
                            <b>
                                @if($advance->print_status == 1|| $advance->print_status == 3)
                                    CASH ADVANCES
                                @elseif($advance->print_status == 2 || $advance->print_status == 0)
                                    COPY OF CASH ADVANCES
                                @endif
                            </b>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-container">
            <div class="invoice-body" style="width: 100%;">
                <div class="invoice-details">
                    <div>
                        @if(!empty($receipt->customer))
                        <table class="details-table">
                            <tr>
                                <th>Name :</th>
                                <td>
                                    @if(isset($receipt->customer->billing_title) || isset($receipt->customer->title))
                                        @php
                                            $title = $receipt->customer->billing_title ?? $receipt->customer->title;
                                            $titles = ['Mr.', 'Mrs.', 'Miss.'];
                                        @endphp

                                        @if(isset($titles[$title]))
                                            {{ $titles[$title] }}
                                        @endif
                                    @endif

                                    {{ $receipt->customer->billing_name ?? $receipt->customer->name ?? "" }}
                                </td>
                            </tr>
                            <tr>
                                <th>Address :</th>
                                <td>
                                    @php
                                        $receiptCust = $receipt->customer ?? ($advance->customer ?? null);
                                        $advReceiptHouseNo = !empty($receiptCust->billing_house_no)
                                            ? trim($receiptCust->billing_house_no)
                                            : (!empty($receiptCust->house_no) ? trim($receiptCust->house_no) : '');
                                        $advReceiptAddress = !empty($receiptCust->billing_address)
                                            ? trim($receiptCust->billing_address)
                                            : (!empty($receiptCust->address) ? trim($receiptCust->address) : '');
                                        $advReceiptFullAddress = $advReceiptAddress;
                                        if (!empty($advReceiptHouseNo)) {
                                            $cleanAdvReceiptHouseNo = trim($advReceiptHouseNo, ", \t\n\r\0\x0B");
                                            if (empty($advReceiptAddress)) {
                                                $advReceiptFullAddress = $advReceiptHouseNo;
                                            } elseif ($cleanAdvReceiptHouseNo !== '' && stripos($advReceiptAddress, $cleanAdvReceiptHouseNo) === false) {
                                                $formattedAdvReceiptHouseNo = rtrim($cleanAdvReceiptHouseNo, ',') . ',';
                                                $advReceiptFullAddress = $formattedAdvReceiptHouseNo . ' ' . $advReceiptAddress;
                                            }
                                        }
                                    @endphp
                                    {{ $advReceiptFullAddress }}
                                </td>
                            </tr>
                            <tr>
                                <th>Contact No :</th>
                                <td>
                                    {{ $receipt->customer->billing_phone ?? $receipt->customer->phone_mobile ?? "" }}
                                </td>
                            </tr>
                            <tr>
                                <th>NIC :</th>
                                <td>{{ $receipt->customer->nic_no ?? "--" }}</td>
                            </tr>
                        </table>
                        @endif
                    </div>

                    <div>
                        <table class="details-table">
                            <tr>
                                <th>Account No :</th>
                                <td>{{ ($receipt->receipt_code) ?? '--' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="advance-details">
                    <div>
                        <table class="details-table">
                            <tr>
                                <th>Remarks :</th>
                                <td>{{ $advance->note }}</td>
                            </tr>
                        </table>
                    </div>

                    <div>
                        <table class="payment-details-table">
                            <tr>
                                <th>Amount Paid :</th>
                                <td>
                                    <strong>{{ $receipt->paid_amount ?? 0 }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th>Payment Method :</th>
                                <td>{{ $receipt->payment_method }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="product-details">
                    <div class="signature-box" style="margin-top: 30px;">
                        <div class="row d-flex justify-content-space-between">
                            <div class="col-6" style="width: 40%">
                                <hr class="dotted-line">
                                <p class="text-center">{{__('Prepared By')}}</p>
                            </div>
                            <div class="col-6" style="width: 40%">
                                <hr class="dotted-line">
                                <p class="text-center">{{__('Authorized By')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
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
