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
            width: 220mm;
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
        }

        .container {
            width: 220mm;
            height: 140mm;
            padding: 0;
            background-color: transparent;
        }

        .content {
            width: 220mm;
        }

        .logo-container {
            width: 100%;
            height: 37mm;
            background-color: transparent;
        }

        .invoice-container {
            display: flex;
            width: 100%;
            height: 113mm;
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
            height: 40mm;
            padding-top: 0.5rem;
        }

        .invoice-condition {
            display: flex;
            justify-content: space-between;
            width: 205mm;
            height: 40mm;
            padding-top: 0.5rem;
        }

        .sign-details {
            display: flex;
            justify-content: space-between;
            width: 205mm;
            height: 15mm;
            padding-top: 0.5rem;
        }

        .footer-details {
            display: flex;
            justify-content: center;
            width: 205mm;
            height: 10mm;
            padding-top: 0.5rem;
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
            padding-top: 0.5rem;
        }

        .product-details table th,
        .product-details table td {
            font-size: 12px;
            padding: 0.2rem;
            line-height: normal;
        }

        .product-details table thead th {
            border-top: 1px dashed #000000;
            border-bottom: 1px dashed #000000;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .terms-table td {
            font-size: 12px !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div id="app" class="content">
            <div class="logo-container"></div>

            <div class="invoice-container">
                <div class="left-margin"></div>

                <div class="invoice-body">
                    <div class="invoice-details">
                        <div>
                            <h3>Customer Details</h3>
                            <table class="details-table">
                                @if ($invoice->customer_id != 0)
                                    <tr>
                                        <th>Name</th>
                                        <th>:</th>
                                        <td>{{ $invoice->customer->name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Contact No</th>
                                        <th>:</th>
                                        <td>{{ $invoice->customer->phone ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <th>:</th>
                                        <td>{{ $invoice->customer->address ?? '' }}</td>
                                    </tr>
                                    {{-- @php
                                        dd($invoice->customer->email);
                                    @endphp
                                    @if (isset($invoice->customer->email))
                                        <tr>
                                            <th>Email</th>
                                            <th>:</th>
                                            <td>{{ $invoice->customer->email }}</td>
                                        </tr>
                                    @endif --}}

                                    <tr>
                                            <th>Email</th>
                                            <th>:</th>
                                            <td>{{ !empty($invoice->customer->email) ? $invoice->customer->email : '-n/a-' }}</td>
                                        </tr>
                                    <tr>
                                        <th>Customer Code</th>
                                        <th>:</th>
                                        <td>
                                            @if ($invoice->customer_id != 0)
                                                {{ \Auth::user()->customerNumberFormat($invoice->customer->id) }}
                                            @else
                                                --
                                            @endif
                                        </td>
                                    </tr>
                                @elseif ($invoice->customer_id == 0)
                                    <tr>
                                        <th>Name</th>
                                        <th>:</th>
                                        <td>{{ $invoice->customer->name ?? '' }} </td>
                                    </tr>
                                    <tr>
                                        <th>Contact No</th>
                                        <th>:</th>
                                        <td>{{ $invoice->customer->phone ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Customer Code</th>
                                        <th>:</th>
                                        <td>
                                            @if ($invoice->customer_id != 0)
                                                {{ \Auth::user()->customerNumberFormat($invoice->customer->id) }}
                                            @else
                                                --
                                            @endif
                                        </td>
                                    </tr>
                                @endif

                            </table>
                        </div>

                        <div>
                            <table class="details-table">
                                <tr>
                                    <th>Invoice No</th>
                                    <th>:</th>
                                    <td>{{ $invoice->invoice_code ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th>Invoice Date</th>
                                    <th>:</th>
                                    <td>{{ date('d-m-Y', strtotime($invoice->issue_date)) ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th>User Name</th>
                                    <th>:</th>
                                    <td>{{ $user->name ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th>User ID</th>
                                    <th>:</th>
                                    <td>{{ \Auth::user()->userNumberFormat($invoice->created_by) ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th>PO Number</th>
                                    <th>:</th>
                                    <td>
                                        @if (isset($invoice->inventories))
                                            @if ($invoice->inventories->grn_id != 0)
                                                @if (isset($invoice->inventories->grns))
                                                    {{ \Auth::user()->purchaseOrderNumberFormat($invoice->inventories->grns->order_id) ?? '--' }}
                                                @else
                                                    --
                                                @endif
                                            @else
                                                --
                                            @endif
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Terms Of Payment</th>
                                    <th>:</th>
                                    <td>{{ $invoice->sales->installment ?? '--' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="product-details">
                        <table>
                            <thead>
                                <tr>
                                    <th class="text-left">Code</th>
                                    <th class="text-center">Item Description</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Purchasing Price</th>
                                    <th class="text-center">Discount Rate</th>
                                    @if ($invoice->sales->modal_type == 2)
                                        <th class="text-center">Warranty Charges</th>
                                        <th class="text-center">Insurance Premium</th>
                                    @endif
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if ($invoice->sales->modal_type == 1)
                                    <tr>
                                        <td class="text-left">{{ $invoice->sales->products->barcode ?? '--' }}</td>
                                        <td class="text-center">{{ $invoice->sales->products->name ?? '--' }}</td>
                                        <td class="text-center">{{ $invoice->sales->quantity ?? '--' }}</td>
                                        <td class="text-center">
                                            {{ number_format($invoice->sales->unit_price, 2) ?? '--' }}</td>
                                        <td class="text-center">
                                            {{ number_format($invoice->sales->discount, 2) ?? '--' }}</td>
                                        <td class="text-right">
                                            {{ number_format($invoice->sales->net_amount, 2) ?? '--' }}</td>
                                    </tr>
                                @elseif ($invoice->sales->modal_type == 2)
                                    @foreach ($invoice->sales->saleItems as $items)
                                        <tr>
                                            <td class="text-left">{{ $invoice->sales->products->barcode ?? '--' }}</td>
                                            <td class="text-center">{{ $items->products->name ?? '--' }} @if ($items->products->warranty_period != 0)
                                                    &nbsp;&nbsp;/ &nbsp;&nbsp; Wr -
                                                    {{ $items->products->warranty_period ?? '--' }} mth
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $invoice->sales->quantity ?? '--' }}</td>
                                            <td class="text-center">
                                                {{ number_format($invoice->sales->unit_price, 2) ?? '--' }}</td>
                                            <td class="text-center">
                                                {{ number_format($invoice->sales->discount, 2) ?? '--' }}</td>
                                            <td class="text-center">
                                                {{ number_format($items->warranty_charges, 2) ?? '--' }}</td>
                                            <td class="text-center">
                                                {{ number_format($items->insurance_premium, 2) ?? '--' }}</td>
                                            <td class="text-right">
                                                {{ number_format($invoice->sales->net_amount, 2) ?? '--' }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <th style="border-top: 1px dashed #000000; " colspan="6"></th>
                                </tr>
                            </tbody>

                        </table>
                    </div>

                    <div class="invoice-condition ">
                        <div>
                            <h3 class="text-center">Installment Details</h3>

                            <table class="terms-table">
                                @if ($invoice->sales->sale_type == 1)
                                    <tr>
                                        <td>Warranty Period</td>
                                        <td>:</td>
                                        <td>{{ $invoice->sales->products->warranty_period ?? '--' }} months</td>
                                    </tr>
                                    <tr>
                                        <td>Monthly Installment</td>
                                        <td>:</td>
                                        <td>{{ number_format($invoice->sales->monthly_installment, 2) ?? '0.00' }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. of Installments</td>
                                        <td>:</td>
                                        <td>{{ $invoice->sales->installment ?? '0.00' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Due</td>
                                        <td>:</td>
                                        <td>{{ number_format($invoice->sales->monthly_installment * $invoice->sales->installment, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Installment Date</td>
                                        <td>:</td>
                                        <td>{{ $invoice->sales->installment_date }}</td>
                                    </tr>
                                @else
                                    @if (isset($invoice->sales->products))
                                        <tr>
                                            <td>Warranty Period</td>
                                            <td>:</td>
                                            <td>{{ $invoice->sales->products->warranty_period ?? '--' }} months</td>
                                        </tr>
                                    @endif
                                @endif

                            </table>
                        </div>
                        <div>
                            <h3 class="text-center">Bank Details</h3>
                            <table class="terms-table">
                                <tr class="text-center">
                                    <td>{{ $invoice->bank->account_no ?? '' }}</td>
                                </tr>
                                <tr class="text-center">
                                    <td>{{ $invoice->bank->bank ?? '' }} </td>
                                </tr>
                                <tr class="text-center">
                                    <td>{{ $invoice->bank->address ?? '' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="amount">
                            <table class="details-table">

                                <tr>
                                    <th>Total Amount</th>
                                    <th>:</th>
                                    <td>{{ number_format($invoice->sales->net_amount, 2) ?? '0.00' }}</td>
                                </tr>
                                <tr>
                                    <th>Transport Charges</th>
                                    <th>:</th>
                                    <td>{{ number_format($invoice->sales->traveling_charges, 2) ?? '0.00' }}</td>
                                </tr>
                                <tr>
                                    <th>Down Payment</th>
                                    <th>:</th>
                                    <td>{{ number_format($invoice->sales->down_payment, 2) ?? '0.00' }}</td>
                                </tr>
                                <tr>
                                    <th>Advance Amount</th>
                                    <th>:</th>
                                    <td>{{ number_format($invoice->sales->advance_amount, 2) ?? '0.00' }}</td>
                                </tr>
                                <tr>
                                    <th>Warranty Charges</th>
                                    <th>:</th>
                                    <td>{{ number_format($hirePurchaseDetail->warranty_charges, 2) ?? '0.00' }}</td>
                                </tr>
                                <tr>
                                    <th>Insurance Premium</th>
                                    <th>:</th>
                                    <td>{{ number_format($hirePurchaseDetail->insurance_premium, 2) ?? '0.00' }}</td>
                                </tr>
                                <tr>
                                    <th>Balance Amount</th>
                                    <th>:</th>
                                    <td>{{ number_format($invoice->sales->total_due_amount, 2) ?? '0.00' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="sign-details">
                        <div>
                            <table class="details-table">

                                <tr>
                                    <td>............................</td>
                                </tr>
                                <tr>
                                    <td>Customer Signature</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table class="details-table">
                                <tr>
                                    <td>............................</td>
                                </tr>
                                <tr>
                                    <td>Prepared By</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table class="details-table">
                                <tr>
                                    <td>............................</td>
                                </tr>
                                <tr>
                                    <td>Authorized By</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="footer-details text-center">
                        <div>
                            <table class="details-table">
                                <tr>
                                    <td>All cheques to be drawn in favour of {{ config('app.name') }} and crossed
                                        ACCOUNT PAYEE. Please indicate invoice number with the payment.</td>
                                </tr>
                            </table>
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
