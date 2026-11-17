<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <style>
        @page {
            width: 58mm;
            margin: 0mm;
        }
        html, body {
            width: 58mm;
            margin: 0;
            padding: 0;
            background-color: rgb(235, 235, 235);
        }
        .container {
            width: inherit;
            height: inherit;
            padding: 0;
            background-color: rgb(255, 255, 255);
        }
        .content {
            width: 58mm;
            /* padding-left: 2.5mm;
            padding-right: 2.5mm; */
            padding-bottom: 30px;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        td{
            font-size: 12px;
        }
        .user-details-table td {
            text-align: right;
        }
        .paid-details td{
            font-size: 15px;
        }
        .printed-details td{
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            {{-- logo & address --}}
            <div style="width: 100%; height: 16mm;">
                <center>
                    <div style="width: 20mm; height: 20mm; display: table;">
                        <div style="width: 10mm; height: 15mm; display: table-cell; vertical-align: middle;">
                            <img style="width: 100%; height: 100%;" src="{{ asset('storage/uploads/logo/small_logo.png') }}"/>
                        </div>
                    </div>
                </center>
            </div>
            <div style="width: 100%; height: 24mm; display: table;">
                <div style="display: table-cell; vertical-align: middle; text-align: center; width: 100%; height: 100%;">
                    <p style="font-size:11px;"><b>{{ config('app.name') }}</b><br>
                    {{-- Head Office : No.51, Thammita Road, Negombo. <br>
                    Tel : 031-2230288 E-mail : info@com <br>
                    <span>Banch : </span><span>{{ \App\Branch::branchName($user->branch_id)[0] }}</span></p> --}}
                    @include('components.headOfficeBlock')
                    </p>
                </div>
            </div>
            {{-- Title --}}
            <div style="width: 100%; height: 7mm; display: table;">
                <span style="display: table-cell; vertical-align: middle; text-align: center; width: 100%; height: 100%;" onclick="print();"><h4 style="margin-top: 7px; margin-bottom: 0px;">Payment Receipt</h4></span>
            </div>
            <div style="width: 100%; text-align: center;">**************************</div>

            {{-- Details --}}
            <div style="width: 100%; height: 15mm; text-align: right;">
                <table style="width: 70%; float: right;" class="user-details-table">
                    <tr><td>Issued Date</td><td>:</td><td>{{ $receipt->issue_date ?? '--' }}</td></tr>
                    <tr><td>Issued By</td><td>:</td><td>{{ $user->name ?? '--' }}</td></tr>
                    <tr><td>ID</td><td>:</td><td>{{ \Auth::user()->userNumberFormat($receipt->created_by) ?? '--' }}</td></tr>
                </table>
            </div>
            <div style="width: 100%; height: 38mm; margin-top:8px;">
                <table style="width: 100%;">
                    <tr>
                        <td colspan="3" style="width: 100%; text-align: center; padding: 5px; font-size: 13px;"><b>{{ $receipt->customer->name }}</b></td>
                    </tr>
                    <tr>
                        <td>Facility Number</td><td>:</td><td>{{ \Auth::user()->pawningNumberFormat($receipt->pawnings->id) ?? '--' }}</td>
                    </tr>
                    <tr>
                        <td>Customer Number</td><td>:</td><td>{{ \Auth::user()->customerNumberFormat($receipt->customer_id) ?? '--' }}</td>
                    </tr>
                    <tr>
                        <td>Receipt Number</td><td>:</td><td>{{ $receipt->receipt_code ?? '--' }}</td>
                    </tr>
                    <tr>
                        <td>Full Loan Amount</td><td>:</td><td>{{ number_format($receipt->pawnings->full_total_of_payment, 2) ?? '--' }}</td>
                    </tr>
                    <tr>
                        <td>Installment Amount</td><td>:</td><td>{{ number_format($receipt->installment_amount, 2) ?? '--' }}</td>
                    </tr>
                    {{-- <tr>
                        <td>Paid Amount</td><td>:</td><td>{{ number_format($receipt->paid_amount + $receipt->card_charge_amount, 2) ?? '--' }}</td>
                    </tr> --}}
                    <tr>
                        <td>Outstanding Balance</td><td>:</td><td>{{ number_format($receipt->balance_amount, 2) ?? '--' }}</td>
                    </tr>
                </table>
            </div>
            <div style="width: 100%; margin-top:50px;">
                <table style="width: 100%;" class="paid-details">
                    <tr>
                        <td style="text-align: center; border-bottom: solid 1px"><b>Description</b></td><td>&nbsp;</td><td style="text-align: center; border-bottom: solid 1px"><b>Amount</b></td>
                    </tr>
                    <tr>
                        <td style="height:30px;">Paid Amount</td><td>&nbsp;</td><td style="text-align: right;">{{ number_format($receipt->paid_amount + $receipt->card_charge_amount, 2) ?? '--' }}</td>
                    </tr>
                </table>
            </div>
            <div style="width: 100%;margin-top:8px;">
                <table style="width: 100%;">
                    <tr>
                        <td>Payment Method</td><td>:</td><td>{{ $receipt->payment_method ?? '--' }}</td>
                    </tr>
                </table>
            </div>
            <div style="width: 100%; height: 12mm; text-align: right; margin-top:8px;">
                <table style="width: 80%; float: right;" class="printed-details">
                    <tr><td>Printed By</td><td>:</td><td>{{ \Auth::user()->userName() }}</td></tr>
                    <tr><td>Printed Date</td><td>:</td><td>{{ \Carbon\Carbon::now()->format('Y-m-d') }}</td></tr>
                    <tr><td>Printed Time</td><td>:</td><td>{{ \Carbon\Carbon::now()->format('h:i:sa') }}</td></tr>
                    {{-- <tr><td>ID</td><td>:</td><td>{{ \Auth::user()->userNumberFormat($receipt->created_by) ?? '--' }}</td></tr> --}}
                </table>
            </div>
            {{-- Title --}}
            <div style="width: 100%; text-align: center; margin-top:8px;">**************************</div>
            <div style="width: 100%; height: 7mm; display: table; margin-top: -5px;">
                <span style="display: table-cell; vertical-align: middle; text-align: center; width: 100%; height: 100%;"><h4 style="margin-top: 0px; margin-bottom: 0px;">Thank You for the Business!</h4></span>
            </div>
            <div style="width: 100%; text-align: center;">**************************</div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <script>
        $(document).ready(function(){
            window.print();
            // window.close();
        });

    </script>
</body>
</html>
