<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <style>
        @page {
            width: 240mm;
            height: 203mm;
            margin: 0mm;
        }
        html, body {
            width: 240mm;
            height: 203mm;
            margin: 0;
            padding: 0;
            background-color: rgb(235, 235, 235);
        }
        .container {
            width: inherit;
            height: inherit;
            margin-left: 25%;
            /* margin-top: 15%; */
            padding: 0;
            background: url('{{ asset("assets/pawning/pawning_ticket_model.jpg") }}');
            background-repeat: no-repeat;
            background-size: 240mm 203mm;
        }
        .content {
            width: 240mm;
            height: 203mm;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="width: 240mm; height: 203mm; display: flex; flex-wrap: wrap;">
            <div style="width: 100%; margin-top: 35.5mm;">
                <div style="width: 100%; height: 17mm; margin-left: 37mm; display: flex; flex-wrap: wrap;">
                    <div style="width: 44mm; height: 17mm;">
                        <div style="width: 100%; height: 8.5mm; text-align: center; display: table">
                            <span style="display: table-cell; vertical-align: middle;">{{ $pawningTicket->issue_date }}</span>
                        </div>
                        <div style="width: 100%; height: 8.5mm; text-align: center; display: table">
                            <span style="display: table-cell; vertical-align: middle;">{{ AUth::user()->pawningNumberFormat($pawningTicket->id) }}</span>
                        </div>
                    </div>
                    <div style="width: 116mm; height: 16mm; margin-left: 27.5mm; text-align: center; display: table;">
                        <span style="display: table-cell; vertical-align: middle;"> {{ $pawningTicket->customer->name ?? '--' }} <br>{{ $pawningTicket->customer->address ?? '--' }}</span>
                    </div>
                </div>
                <div style="width: 212mm; height: 37mm; margin-left: 15mm; margin-top: 19mm;">
                    @php
                        $totWeight = 0;
                    @endphp
                    @foreach ( $pawningTicket->pawnedItem as $pawnedItem)
                        <div style="width: 100%; margin-top: 1mm; display: flex; flex-wrap: wrap;">
                            <div style="width: 123mm; padding-left: 15px;">{{ $pawnedItem->jewellery->name ?? '--' }}</div>
                            <div style="width: 22mm; margin-left: 1.5mm; text-align: center;">{{ $pawnedItem->weight ?? '--' }}</div>
                            <div style="width: 58mm; text-align: center; margin-left: 2mm;">{{ $pawnedItem->goldType->gold_rate_type ?? '--' }}</div>
                        </div>
                        @php
                            $totWeight += $pawnedItem->weight;
                        @endphp
                    @endforeach
                </div>
                <div style="width: 212mm; height: 8mm; margin-left: 15mm; margin-top: 1mm; display: flex; flex-wrap: wrap;">
                    <div style="width: 22mm; height: 100%; text-align: center; margin-left: 128.5mm; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ $totWeight }}</span></div>
                    <div style="width: 58mm; height: 100%; text-align: center; margin-left: 2mm; display: table;"><span style="display: table-cell; vertical-align: middle;">*******</span></div>
                </div>
                @php
                    $timeRange = $pawningTicket->time_range;
                    $timePeriod = $pawningTicket->time_period_id;
                    if($timeRange == 3){
                        $installments = $timePeriod;
                    } elseif ($timeRange == 2) {
                        $installments = $timePeriod*4;
                    } elseif ($timeRange == 1) {
                        $installments = $timePeriod*30;
                    } else {
                        $installments = $timePeriod;
                    }
                @endphp
                <div style="width: 212mm; height: 8mm; margin-left: 15mm; margin-top: 4.2mm; display: flex; flex-wrap: wrap;">
                    <div style="width: 47mm; height: 100%; text-align: center; margin-left: 44mm; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ \Auth::user()->customerNumberFormat($pawningTicket->customer_id) }}</span></div>
                    <div style="width: 116mm; height: 100%; text-align: center; margin-left: 1mm; display: table;"><span style="display: table-cell; vertical-align: middle;">Total Payable Amount : {{ number_format($pawningTicket->full_prepayment, 2) }} x {{ $installments }} = {{ number_format($pawningTicket->full_total_of_payment, 2) }}</span></div>
                </div>
                <div style="width: 212mm; height: 8mm; margin-left: 15mm; margin-top: 0.5mm; display: flex; flex-wrap: wrap;">
                    <div style="width: 47mm; height: 100%; text-align: center; margin-left: 44mm; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ $pawningTicket->customer->nic_no ?? '--' }}</span></div>
                    <div style="width: 47mm; height: 100%; text-align: center; margin-left: 39mm; display: table; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ $pawningTicket->interest_rate }}</span></div>
                </div>
                <div style="width: 212mm; height: 8mm; margin-left: 15mm; margin-top: 0.5mm; display: flex; flex-wrap: wrap;">
                    <div style="width: 47mm; height: 100%; text-align: center; margin-left: 44mm; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ number_format($pawningTicket->full_amount_financed, 2) }}</span></div>
                    <div style="width: 47mm; height: 100%; text-align: center; margin-left: 39mm; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ $pawningTicket->due_date ?? '--' }}</span></div>
                </div>
                <div style="width: 212mm; height: 8mm; margin-left: 15mm; margin-top: 0.5mm; display: flex; flex-wrap: wrap;">
                    <div style="width: 85mm; height: 100%; text-align: center; margin-left: 44mm; display: table;"><span style="display: table-cell; vertical-align: middle; font-size: 14px;">{{ $pawningTicket->amount_payout_in_words }}</span></div>
                    <div style="width: 40mm; height: 100%; text-align: right; margin-left: 6mm; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ number_format($pawningTicket->full_amount_payout, 2) }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div id="ticket" style="display: none;">
        <div style="width: 216mm; height: 203mm; display: flex; flex-wrap: wrap;">
            <div style="width: 100%; margin-top: 30mm;">
                <div style="width: 100%; height: 17mm; margin-left: 24.5mm; display: flex; flex-wrap: wrap;">
                    <div style="width: 44mm; height: 17mm;">
                        <div style="width: 100%; height: 8.5mm; text-align: center; display: table">
                            <span style="display: table-cell; vertical-align: middle;">{{ $pawningTicket->issue_date }}</span>
                        </div>
                        <div style="width: 100%; height: 8.5mm; text-align: center; display: table">
                            <span style="display: table-cell; vertical-align: middle;">{{ AUth::user()->pawningNumberFormat($pawningTicket->id) }}</span>
                        </div>
                    </div>
                    <div style="width: 116mm; height: 16mm; margin-left: 49mm; text-align: center; display: table;">
                        <span style="display: table-cell; vertical-align: middle;"> {{ $pawningTicket->customer->name ?? '--' }} <br>{{ $pawningTicket->customer->address ?? '--' }}</span>
                    </div>
                </div>
                <div style="width: 212mm; height: 43mm; margin-left: 2.5mm; margin-top: 22mm;">
                    @php
                        $totWeight = 0;
                    @endphp
                    @foreach ( $pawningTicket->pawnedItem as $pawnedItem)
                        <div style="width: 100%; margin-top: 1mm; display: flex; flex-wrap: wrap;">
                            <div style="width: 123mm; padding-left: 15px;">{{ $pawnedItem->jewellery->name ?? '--' }}</div>
                            <div style="width: 22mm; margin-left: 2.5mm; text-align: right;">{{ $pawnedItem->weight ?? '--' }}</div>
                            <div style="width: 58mm; text-align: right; margin-left: 2mm;">{{ $pawnedItem->goldType->gold_rate_type ?? '--' }}</div>
                        </div>
                        @php
                            $totWeight += $pawnedItem->weight;
                        @endphp
                    @endforeach
                </div>
                <div style="width: 212mm; height: 8mm; margin-left: 2.5mm; margin-top: 1mm; display: flex; flex-wrap: wrap;">
                    <div style="width: 22mm; height: 100%; text-align: right; margin-left: 129.5mm; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ $totWeight }}</span></div>
                    <div style="width: 58mm; height: 100%; text-align: right; margin-left: 2mm; display: table;"><span style="display: table-cell; vertical-align: middle;">*******</span></div>
                </div>
                <div style="width: 212mm; height: 8mm; margin-left: 3.5mm; margin-top: 6mm; display: flex; flex-wrap: wrap;">
                    <div style="width: 47mm; height: 100%; text-align: center; margin-left: 44mm; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ \Auth::user()->customerNumberFormat($pawningTicket->customer_id) }}</span></div>
                    <div style="width: 116mm; height: 100%; text-align: center; margin-left: 1mm; display: table;"><span style="display: table-cell; vertical-align: middle;">Total Payable Amount : {{ number_format($pawningTicket->full_prepayment, 2) }} x {{ $installments }} = {{ number_format($pawningTicket->full_total_of_payment, 2) }}</span></div>
                </div>
                <div style="width: 212mm; height: 8mm; margin-left: 3.5mm; margin-top: 1mm; display: flex; flex-wrap: wrap;">
                    <div style="width: 47mm; height: 100%; text-align: center; margin-left: 44mm; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ $pawningTicket->customer->nic_no ?? '--' }}</span></div>
                    <div style="width: 47mm; height: 100%; text-align: center; margin-left: 50mm; display: table; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ $pawningTicket->interest_rate }}</span></div>
                </div>
                <div style="width: 212mm; height: 8mm; margin-left: 3.5mm; margin-top: 1mm; display: flex; flex-wrap: wrap;">
                    <div style="width: 47mm; height: 100%; text-align: center; margin-left: 44mm; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ number_format($pawningTicket->full_amount_financed, 2) }}</span></div>
                    <div style="width: 47mm; height: 100%; text-align: center; margin-left: 50mm; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ $pawningTicket->due_date ?? '--' }}</span></div>
                </div>
                <div style="width: 212mm; height: 8mm; margin-left: 3.5mm; margin-top: 1mm; display: flex; flex-wrap: wrap;">
                    <div style="width: 85mm; height: 100%; text-align: center; margin-left: 44mm; display: table;"><span style="display: table-cell; vertical-align: middle; font-size: 14px;">{{ $pawningTicket->amount_payout_in_words }}</span></div>
                    <div style="width: 40mm; height: 100%; text-align: right; margin-left: 18.5mm; display: table;"><span style="display: table-cell; vertical-align: middle;">{{ number_format($pawningTicket->full_amount_payout, 2) }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <script>
        // var pawnTicket = <?php echo json_encode($pawningTicket); ?>;
        $(document).ready(function(){
            // console.log(pawnTicket);
            var divContents = document.getElementById("ticket").innerHTML;
            var a = window.open("", "", "width=1000mm, height=600mm");
            a.document.write("<html>");
            a.document.write("<body>");
            a.document.write(divContents);
            a.document.write("</body></html>");
            a.document.close();
            a.print();
            a.close();
        });

    </script>
</body>
</html>
