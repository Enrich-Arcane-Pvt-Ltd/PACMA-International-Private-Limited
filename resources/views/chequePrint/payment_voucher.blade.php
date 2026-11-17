<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <style>
        @page {
            /* size: auto;
            size: A4 portrait; */
        }
        html, body {
            /* width: 241mm;
            height: 88mm;
            margin: 0;
            padding: 0; */
            background-color: rgb(235, 235, 235);
        }
        .container {
            /* width: inherit;
            height: inherit; */
            /* margin-left: 10%; */
            /* margin-top: 10%; */
            /* padding: 0; */
            /* background: url('{{ asset("assets/receipt/receipt_image.jpg") }}');
            background-repeat: no-repeat; */
            /* background-size: 241mm 88mm; */
            padding-top: 5rem;
            padding-bottom: 10rem;
            background-color: rgb(255, 255, 255);

        }
        .content {
            /* width: inherit;
            height: inherit; */
            /* margin-left: 15%;
            padding-top: 5%; */
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .logo-container {
                height: 37mm;
                width: 37mm;
            }
            .logo-container img{
                height: 100%;
                width: 100%;
            }
            .heading-container {
                /*  height: 37mm; */
                width: 55%;
                padding-left: 3.5mm;
            }
            p {
                display: block;
                margin-block-start: 0;
                margin-block-end: 2px;
                margin-inline-start: 3px;
                margin-inline-end: 0px;
                font-size: 14px;
            }
            h1 {
                margin: 0;
                padding: 0;
            }
            .name-container {
                width: 100%;
                /* display: block;
                font-size: 1.3em;
                margin-block-start: 5px;
                margin-block-end: 15px;
                margin-inline-start: 0px;
                margin-inline-end: 0px;
                font-weight: bold; */
            }
            .invoice-lable {
                padding-top: 8%;
                width: 30%;
            }
            .invoice-lable h3{
                float: right;
            }
            .th-1 {
                width: 8rem;
                height: 1.5rem;
            }
            .td-1 {
                width: 1.5rem;
            }
    </style>
</head>
<body>
    <div class="container" id="print_div">
        <div class="content">

        <div class="row justify-content-between">

            <div class="col-md-4">
                <div class="name-container">
                    <h4>{{config('app.name')}}</h4>
                </div>

            </div>

            <div class="col-md-4">
                <div class="logo-container">
                    <img src="{{ asset('storage/uploads/logo/small_logo.png') }}"/>
                </div>
            </div>


            <div class="col-md-4">
                <h4>General Payment Voucher</h4><br>
                <p>Printed By : {{ \Auth::user()->userName() }}<br> {{ \Carbon\Carbon::now()->format('d-m-Y h:i:sa') }} </p>
            </div>

        </div><br>


        <div class="row">
            <div class="col-md-6">
                <div style="width: 70%; height: 40px; border: 2px solid black; text-align: center; display: table; margin-left: 6rem; margin-top: 3rem;">
                    <h5 style="display: table-cell; vertical-align: middle;">
                        @if ($cheques->cross_type == "no_crossing")
                        No Crossing
                        @elseif ($cheques->cross_type == "cross_only")
                        Cross Only
                        @elseif ($cheques->cross_type == "ac_payee")
                        A/C PAYEE ONLY
                        @elseif ($cheques->cross_type == "not_negotiable")
                        NOT NEGOTIABLE
                        @endif
                    </h5>
                </div>
            </div>
            <div class="col-md-6">
                <table>
                    <tr><th class="th-1">Date</th><td class="td-1">:</td><td>{{ date('d-m-Y', strtotime($cheques->date)) }}</td></tr>
                    <tr><th class="th-1">Voucher No.</th><td class="td-1">:</td><td>{{ Auth::user()->paymentVoucherNumberFormat($cheques->payment_voucher_id) }}</td></tr>
                    <tr><th class="th-1">Bank A/C</th><td class="td-1">:</td><td>{{ $cheques->bank->bank ?? '--'}}</td></tr>
                    <tr><th class="th-1">Cheque No.</th><td class="td-1">:</td><td>{{ $cheques->cheque_number ?? '--'}}</td></tr>
                    <tr><th class="th-1">Account No.</th><td class="td-1">:</td><td>{{ Auth::user()->chartOfAccountNumberFormat($cheques->chart_of_account_id ?? ($cheques->voucher->chart_of_account_id ?? ($cheques->chart_of_account->id ?? ''))) }}</td></tr>
                </table>
            </div>
        </div> <br>

        <div class="row ">
            <div class="col-md-12">
                <table>
                    <tr>
                        <th class="th-1">Payable To</th>
                        <td class="td-1">:</td>
                        <td>@if ($cheques->pay_type == "cash")
                                CASH
                            @elseif ($cheques->pay_type == "supplier")
                                {{ $cheques->vender->name ?? ''}}
                            @elseif ($cheques->pay_type == "customer")
                                {{ $cheques->customer->name ?? ''}}
                            @elseif ($cheques->pay_type == "other")
                                {{ $cheques->pay}}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <table style="width: 100%; border: 2px solid;">
                    <thead style="border: 2px solid;">
                        <tr>
                            <th style="text-align: center; border-right: 2px solid; height: 2rem;">Account Name / No.</th>
                            <th style="text-align: center; border-right: 2px solid;">Particulars</th>
                            <th style="text-align: center;">Amount (Rs.)</th>
                        </tr>
                    </thead>
                    <tbody style="border: 2px solid;">
                        <tr>
                            <td style="text-align: center; border-right: 2px solid; height: 7rem;">{{ $cheques->chart_of_account_name ?? ($cheques->account->account_name ?? ($cheques->account->name ?? '--')) }}</td>
                            <td style="text-align: center; border-right: 2px solid;">{{ $cheques->description ?? '--'}}</td>
                            <td style="text-align: right; padding-right: 10px">{{ number_format($cheques->amount, 2) }}</td>
                        </tr>
                    </tbody>
                    <tfoot style="border: 2px solid;">
                        <tr>
                            <th colspan="2" style="text-align: center; border-right: 2px solid; height: 2rem;">Total Amount</th>
                            <th style="text-align: right; padding-right: 10px ">{{ number_format($cheques->amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div><br>

        <div class="row">
            <div class="col-md-12">
                <table>
                    <tr><th class="th-1">Amount In Words</th><td class="td-1">:</td><td>{{ $cheques->amount_in_word}}</td></tr>
                </table>
            </div>
        </div><br>
        <div class="row">
            <div class="col-md-3" style="text-align: end;">
                Voucher Prepared By
            </div>
            <div class="col-md-1" style="text-align: center;">:</div>
            <div class="col-md-8">........................................................................................</div>
        </div><br>
        <div class="row">
            <div class="col-md-3" style="text-align: end;">
                Checked By
            </div>
            <div class="col-md-1" style="text-align: center;">:</div>
            <div class="col-md-8">........................................................................................</div>
        </div><br>


        <div class="row">
            <div class="col-md-3" style="text-align: end;">
                Approved By
            </div><br>


            <div class="col-md-9">
                <div class="row justify-content-between">
                    <div class="col-md-1" style="text-align: center;">:</div>
                    <div class="col-md-6"  style="text-align: center;">.....................................................</div>
                    <div class="col-md-5"  style="text-align: center;">..........................................................</div>
                </div>
                <div class="row  justify-content-between">
                    <div class="col-md-1" style="text-align: center;"></div>
                    <div class="col-md-6" style="text-align: center;">Being payment of above.</div>
                    <div class="col-md-5" style="text-align: center;">Accountant</div>
                </div>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-6">
                <div style="width: 75%; height: 7rem; padding: 10px; border: 2px solid; margin-left: 3rem;">
                    <p>Remarks</p>
                </div>
            </div>
        </div><br>
        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4" style="text-align: center;">............................................................ <br> Signature of Receipient</div>
        </div><br>
    </div>
    </div>
</div>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('assets') }}/js/printThis.js"></script>

<script>
    var command = <?php echo json_encode($cheques) ?>;

    $(document).ready(function(){
        console.log(command);
        window.print();
    });
</script>
</body>
</html>
