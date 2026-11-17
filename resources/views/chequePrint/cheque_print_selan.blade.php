<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <style>
        @page {
            width: 241mm;
            /* height: 280mm; */
            margin: 0mm;
        }
        html, body {
            /* width: 241mm;
            height: 88mm;
            margin: 0;
            padding: 0; */
            background-color: rgb(235, 235, 235);
        }
        .container {
            width: inherit;
            height: inherit;
            margin-left: 25%;
            margin-top: 10%;
            padding: 0;
            /* background: url('{{ asset("assets/receipt/receipt_image.jpg") }}');
            background-repeat: no-repeat; */
            background-size: 241mm 88mm;
        }
        .content {
            width: 241mm;
            height: 88mm;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="container" style="background-image: url('{{ asset("assets/cheques/selan.jpg") }}'); background-repeat: no-repeat;">
        <div style="width: 241mm; height: 88mm; display: flex; flex-wrap: wrap;">
            <div style="width: 63mm;">
                <div style="margin-left: 24mm; margin-top: 6mm; width: 36mm; text-align: center; height: 4.8mm;">
                    {{ date('d-m-Y', strtotime($cheques->date)) }}
                </div>
                <div style="margin-left: 20mm; margin-top: 1mm; line-height: 6.5mm; width: 36mm; height: 24mm; text-align: center;">
                    {{ $cheques->description }}
                </div>
                <div style="margin-left: 28mm; margin-top: 21mm; width: 30mm; height: 4.8mm; text-align: center;">
                    {{  number_format($cheques->amount).__('/=') }}
                </div>
                <div style="margin-top: 16mm; margin-left: 16mm; width: 30mm; text-align: center;">
                    {{ $cheques->cheque_number}}
                </div>
            </div>
            <div style="width: 178mm;">
                <div style="width:178mm; height:21.5mm; display: flex; flex-wrap: wrap;">
                    <div style="margin-top: 4mm; height: 19mm; width: 34mm; overflow: hidden; margin-left: 5mm;">
                        @if ($cheques->cross_type == "cross_only")
                            <div style="height: 7mm; width: 30mm; border-top: 1px solid black; border-bottom: 1px solid black; -webkit-transform: rotate( 322deg ); text-align: center; font-weight: bold; font-size: 14px; margin-top: 4mm; margin-left: -5mm;">
                            </div>
                        @elseif ($cheques->cross_type == "ac_payee")
                            <div style="display: table; height: 7mm; width: 30mm; border-top: 1px solid black; border-bottom: 1px solid black; -webkit-transform: rotate( 322deg ); margin-top: 4mm; margin-left: -5mm;">
                                <span style="display: table-cell; vertical-align: middle; text-align: center; font-weight: bold; font-size: 10px;"> A/C PAYEE ONLY</span>
                             </div>
                        @elseif ($cheques->cross_type == "not_negotiable")
                            <div style="display: table; height: 7mm; width: 30mm; border-top: 1px solid black; border-bottom: 1px solid black; -webkit-transform: rotate( 322deg ); margin-top: 4mm; margin-left: -5mm;">
                               <span style="display: table-cell; vertical-align: middle; text-align: center; font-weight: bold; font-size: 10px;">NOT NEGOTIABLE</span>
                            </div>
                        @endif
                    </div>

                    <div style="display: flex; flex-wrap: wrap; margin-left: 81mm; margin-top: 8mm;">
                        @php
                            $chequeDate = $cheques->date;
                            $dateArray = explode('-',$chequeDate);
                            $year = $dateArray[0];
                            $month = $dateArray[1];
                            $day = $dateArray[2];

                            $yearArray = str_split($year);
                            $monthArray = str_split($month);
                            $dayArray = str_split($day);
                        @endphp
                        {{-- {{ print_r($dayArray) }} --}}
                        <div style="width: 6mm; height: 6mm; text-align: center; font-size: 17px;">{{ $dayArray[0] }}</div>
                        <div style="width: 6mm; height: 6mm; text-align: center; font-size: 17px; margin-left: 1mm;">{{ $dayArray[1] }}</div>
                        <div style="width: 6mm; height: 6mm; text-align: center; font-size: 17px;">{{ $monthArray[0] }}</div>
                        <div style="width: 6mm; height: 6mm; text-align: center; font-size: 17px;">{{ $monthArray[1] }}</div>
                        <div style="width: 6mm; height: 6mm; text-align: center; font-size: 17px; margin-left: 13mm;">{{ $yearArray[2] }}</div>
                        <div style="width: 6mm; height: 6mm; text-align: center; font-size: 17px; margin-left: 0.7mm;">{{ $yearArray[3] }}</div>
                    </div>
                </div>
                <div style="margin-left: 16mm; margin-top: 1.5mm; width: 140mm; height: 4.5mm; text-align: center;">
                    @if ($cheques->pay_type == "cash")
                        CASH
                    @elseif ($cheques->pay_type == "supplier")
                        {{ $cheques->vender->name ?? ''}}
                    @elseif ($cheques->pay_type == "customer")
                        {{ $cheques->customer->name ?? ''}}
                    @elseif ($cheques->pay_type == "other")
                        {{ $cheques->pay}}
                    @endif
                </div>
                <div style="display: flex; flex-wrap: wrap; margin-top: 4mm; margin-left: 17mm;">
                    <div style="height: 21mm; width: 91mm; line-height: 7.5mm; text-align: center;">
                        {{ $cheques->amount_in_word}}
                    </div>
                    <div style="display: table; margin-top: 3.5mm; margin-left: 11mm; width: 51mm; height: 12mm;">
                        <span style="display: table-cell; vertical-align: middle; width: 100%; height: 100%; text-align: center;">{{  number_format($cheques->amount).__('/=')}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="cheque" style="display: none;">
        <div style="width: 297mm; height: 210mm">
            <div style="width: 241mm; height: 88mm; display: flex; flex-wrap: wrap; float: right; margin-top:14.1rem; margin-right: 7mm;">
                <div style="width: 63mm;">
                    <div style="margin-left: 24mm; margin-top: 6mm; width: 36mm; text-align: center; height: 4.8mm;">
                        {{ date('d-m-Y', strtotime($cheques->date)) }}
                    </div>
                    <div style="margin-left: 20mm; margin-top: 1mm; line-height: 6.5mm; width: 36mm; height: 24mm; text-align: center;">
                        {{ $cheques->description }}
                    </div>
                    <div style="margin-left: 28mm; margin-top: 21mm; width: 30mm; height: 4.8mm; text-align: center;">
                        {{  number_format($cheques->amount).__('/=') }}
                    </div>
                </div>
                <div style="width: 178mm;">
                    <div style="width:178mm; height:20mm; display: flex; flex-wrap: wrap;">
                        <div style="margin-top: 4mm; height: 19mm; width: 34mm; overflow: hidden; margin-left: 5mm;">
                            @if ($cheques->cross_type == "cross_only")
                                <div style="height: 7mm; width: 30mm; border-top: 1px solid black; border-bottom: 1px solid black; -webkit-transform: rotate( 322deg ); text-align: center; font-weight: bold; font-size: 14px; margin-top: 4mm; margin-left: -5mm;">
                                </div>
                            @elseif ($cheques->cross_type == "ac_payee")
                                <div style="display: table; height: 7mm; width: 30mm; border-top: 1px solid black; border-bottom: 1px solid black; -webkit-transform: rotate( 322deg ); margin-top: 4mm; margin-left: -5mm;">
                                    <span style="display: table-cell; vertical-align: middle; text-align: center; font-weight: bold; font-size: 10px;"> A/C PAYEE ONLY</span>
                                </div>
                            @elseif ($cheques->cross_type == "not_negotiable")
                                <div style="display: table; height: 7mm; width: 30mm; border-top: 1px solid black; border-bottom: 1px solid black; -webkit-transform: rotate( 322deg ); margin-top: 4mm; margin-left: -5mm;">
                                <span style="display: table-cell; vertical-align: middle; text-align: center; font-weight: bold; font-size: 10px;">NOT NEGOTIABLE</span>
                                </div>
                            @endif
                        </div>

                        <div style="display: flex; flex-wrap: wrap; margin-left: 88mm; margin-top: 8mm;">
                            <div style="width: 6mm; height: 6mm; text-align: center; font-size: 17px;">{{ $dayArray[0] }}</div>
                            <div style="width: 6mm; height: 6mm; text-align: center; font-size: 17px; margin-left: 1mm;">{{ $dayArray[1] }}</div>
                            <div style="width: 6mm; height: 6mm; text-align: center; font-size: 17px;">{{ $monthArray[0] }}</div>
                            <div style="width: 6mm; height: 6mm; text-align: center; font-size: 17px;">{{ $monthArray[1] }}</div>
                            <div style="width: 6mm; height: 6mm; text-align: center; font-size: 17px; margin-left: 13mm;">{{ $yearArray[2] }}</div>
                            <div style="width: 6mm; height: 6mm; text-align: center; font-size: 17px; margin-left: 0.7mm;">{{ $yearArray[3] }}</div>
                        </div>
                    </div>
                    <div style="margin-left: 16mm; margin-top: 1.5mm; width: 140mm; height: 4.5mm; text-align: center;">
                        @if ($cheques->pay_type == "cash")
                            CASH
                        @elseif ($cheques->pay_type == "supplier")
                            {{ $cheques->vender->name ?? ''}}
                        @elseif ($cheques->pay_type == "customer")
                            {{ $cheques->customer->name ?? ''}}
                        @elseif ($cheques->pay_type == "other")
                            {{ $cheques->pay}}
                        @endif
                    </div>
                    <div style="display: flex; flex-wrap: wrap; margin-top: 4mm; margin-left: 17mm;">
                        <div style="height: 21mm; width: 91mm; line-height: 7.5mm; text-align: center;">
                            {{ $cheques->amount_in_word}}
                        </div>
                        <div style="display: table; margin-top: 3.5mm; margin-left: 11mm; width: 51mm; height: 12mm;">
                            <span style="display: table-cell; vertical-align: middle; width: 100%; height: 100%; text-align: center;">{{  number_format($cheques->amount).__('/=')}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
    var command = <?php echo json_encode($command) ?>;

    $(document).ready(function(){
        console.log(command);

        if(command == 'print'){
            var divContents = document.getElementById("cheque").innerHTML;
            var a = window.open("", "", "width=1000mm, height=700mm");
            a.document.write("<html>");
            a.document.write("<body>");
            a.document.write(divContents);
            a.document.write("</body></html>");
            a.document.close();
            a.print();
            a.close();
        }
    });
</script>
</body>
</html>
