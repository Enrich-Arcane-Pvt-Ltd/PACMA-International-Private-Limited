<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <style>
        @page {
            size: 21cm 29.7cm;
            /* width: 217mm; */
            /* height: 280mm; */
            /* margin: 0mm; */
        }
        html, body {
            width: 21cm;
            /* width: 217mm;
            height: 139mm;
            margin: 0;
            padding: 0; */
            background-color: rgb(235, 235, 235);
        }
        .container {
            width: 21cm;
            padding: 2rem;
            background-color: rgb(255, 255, 255);
            /* width: inherit;
            height: inherit;
            margin-left: 50%;
            margin-top: 15%;
            padding: 0;
            background: url('{{ asset("assets/receipt/receipt_image.jpg") }}');
            background-repeat: no-repeat;
            background-size: 217mm 139mm; */
        }
        .content {
            /* width: 217mm;
            height: 139mm; */
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .flex-table {
            display: table;
        }
        .valign {
            display: table-cell;
            vertical-align: middle;
        }
        .heading {
            width: 100%;
            text-align: center;
            line-height: 1.8;
        }
        .date {
            width: 100%;
        }
        .date span {
            float: right;
            margin-right: 2.5rem;
        }
        .detail {
            width: 100%;
            margin-top: 3rem;
            line-height: 1.5;
            text-align: justify;
        }
        .md-12{
            width: 100%;
        }
        .md-6{
            width: 50%;
        }
        .md-4{
            width: 33%;
        }
        .md-3{
            width: 25%;
        }
        .text-center {
            text-align: center;
        }
        .mt-1 {
            margin-top: 1rem;
        }
        .mt-2 {
            margin-top: 2rem;
        }
        .mt-3 {
            margin-top: 3rem;
        }
        .mt-4 {
            margin-top: 4rem;
        }
        .mt-5 {
            margin-top: 5rem;
        }
        .u{
            border-bottom: 1px dotted;
            text-decoration: none;
        }
        .u2{
            border-bottom: 1px solid;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="heading flex-table">
            <h2 class="valign">
                {{ config('app.name') }} <br>
                මුදල් ණයට දීමේ ගිවිසුම <br>
                රු. <span class="u">&nbsp;&nbsp;&nbsp;{{ number_format($loan->loan_amount, 2) }}&nbsp;&nbsp;&nbsp;</span>
            </h2>
        </div>
        <div class="date">
            <span>දිනය :&nbsp; <span class="u">{{ $loan->date }}</span>
        </div>
        <div class="detail">
            මෙහි පහත ඇතැම් තැනක ණයකරු යැයි කියනු ලබන <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->customer->address }}&nbsp;&nbsp;&nbsp;</span> ලිපිනයෙහි පදිංචි <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->customer->name }}&nbsp;&nbsp;&nbsp;</span>
            ( ජා. හැ. අ: <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->customer->nic_no }}&nbsp;&nbsp;&nbsp;</span> ) යන අය මෙහි පහත ඇතැම් තැනක ණයහිමි යැයි කියනු ලබන
            <span class="u">&nbsp;&nbsp;&nbsp;NO 51, THAMMITA ROAD, NEGOMBO&nbsp;&nbsp;&nbsp;</span> නම ලියාපදිංචි කාර්ය්‍යාලය සම / හෝ ප්‍රධාන ව්‍යාපාරික ස්ථානය පවත්වාගෙන යනු ලබන
            <span class="u">&nbsp;&nbsp;&nbsp;{{ config('app.name') }} (PV127252)&nbsp;&nbsp;&nbsp;</span> යන සමාගම ඔවුනොවුන්ගේ
            උරුමක්කාර, බලකාර, ලැබුම්කාරාදීන්, සමගින් ද බැදී ඇතිකර ගනු ලබන මුදල් ණයට දීමේ ගිවිසුමේ කොන්දේසි පහත පරිදි වේ.

        </div>
        <div class="detail">
            <ol>
                <li>
                    ඉහත කී ණයකරු ණය හිමි පක්ෂය වෙතින් තම ස්වයං රැකියාව වන <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->businessCategory->category_name }}&nbsp;&nbsp;&nbsp;</span> දියුණු කර ගැනීමේ කාරණාව උදෙසා රුපියල් &nbsp;<span class="loan_amount_in_words u" id="loan_amount_in_words" name="loan_amount_in_words"></span>&nbsp; (රු. <span class="u">&nbsp;&nbsp;{{ number_format($loan->loan_amount, 2) }}&nbsp;&nbsp;</span>)
                    ණයක් අයදුම් කර ඇති අතර, ණය හිමි පක්ෂය එකී රු. &nbsp;<span class="loan_amount_in_words u" id="loan_amount_in_words2" name="loan_amount_in_words"></span> ක ණය මුදල ණයකරු වෙත ලබාදීමට එකග වේ. ඉහත කී ණය මුදල් <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->businessCategory->category_name }}&nbsp;&nbsp;&nbsp;</span>
                    කාරණාව උදෙසා පමණක් යෙදවීමට ණයකරු එකග වේ.<br>
                    ඉහතින් සදහන් කළ ආකාරයට ණය හිමි පක්ෂය වෙතින් ශ්‍රී ලංකාවේ වලංගු මුදලින් &nbsp;<span class="loan_amount_in_words u" id="loan_amount_in_words3" name="loan_amount_in_words"></span>&nbsp;
                    (රු. <span class="u">&nbsp;&nbsp;{{ number_format($loan->loan_amount, 2) }}&nbsp;&nbsp;</span>) ක් ණයකරු විසින් ණයට ඉල්ලා භාරගැනීම සිදු වූ බැවින් සහ එසේ හෙයින් මුදල් භාරගත්තේ නැත යයි කීමේ ප්‍රයෝජනය ණයකරු විසින් අත්හැර දමනු ලබයි.
                </li>
                <li class="mt-1">
                    ණයකරු එකී ණය මුදල සියයට &nbsp;<span class="loan_interest_in_words u" id="loan_interest_in_words" name="loan_interest_in_words"></span>&nbsp;&nbsp;(<span class="u">&nbsp;&nbsp;{{ $loan->interest_rate }}%&nbsp;&nbsp;</span>) ක අර්ධ වාර්ෂිකව / වාර්ෂිකව / වර්ෂ එකයි මාස හයක / දෛනික / <span class="u2"> මාසික පොලී අනුපාතයක් </span> මත ණය හිමි පක්ෂය වෙත ගෙවීමට එකග වේ.
                </li>
                <li class="mt-1">
                    ඉහත කී ණය මුදල <span class="u2"> සති </span>/ දෙසති / දෛනික / මාසික වාරිකයක් වන රු <span class="u">&nbsp;&nbsp;{{ number_format($loan->weekly_installment, 2) }}&nbsp;&nbsp;</span class="u"> බැගින් වන වාරික <span class="u">&nbsp;&nbsp;{{ $loan->time_period * 4 }}&nbsp;&nbsp;</span> කින් ණයකරු විසින් ගෙවා නිම කල යුතුය. කෙසේ වෙතත් එය ණයහිමි පක්ෂයේ අයිතියට යටත් වනු ඇත.
                </li>
                <li class="mt-1">
                    නියමිත ණය මුදල නිසි කාලයට ගෙවීමට ණයකරු අපොහොසත් වුවහොත් ප්‍රමාද වන සෑම වාරීකයක් මතම <span class="u">&nbsp;&nbsp;5%&nbsp;&nbsp;</span> ක දඩ පොලී අනුපාතිකයක් ණය හිමි පක්ෂය වෙත ගෙවීමට ණයකරු එකග වේ.
                </li>
                <li class="mt-1">
                    මෙම ණය ගිවිසුම අනුව සහ හෝ ඊට අදාළව ඉහත කී ලෙස ගෙවිය යුතු ණය මුදල් සහ ඊට අදාල පොලිය ණය හිමි පක්ෂය වෙත ගෙවීමෙි පොරොන්දුව ඇතිව ණය මුදලට සුරක්ෂිතයක් වශයෙන් ඉහත කී ණය හිමි පක්ෂය වෙත මෙම ගිවිසුමෙහිම දිනැතිව අත්සන් තබන ලද පොරොන්දු නෝට්ටුවක් ලබාදීමට එකග වේ.
                </li>
                <li class="mt-1">
                    එසේ හෙයින් මෙම මුදල් ණයට දීමේ ගිවිසුමෙහි සහ හෝ ඉහත කී ලෙස පොරොන්දු නෝට්ටුවේ සදහන් නියමයන් සහ කොන්දේසිවලට අනුකූලව අදාළ ණය මුදල ඊට අදාළ පොලිය සමගින් නැවත අයකර ගැනීම සදහා ණය හිමි පක්ෂය වෙත අයිතිය ඇති බව ණයකරැ පිළිගනී.
                    <br>
                    එමෙන්ම රු. <span class="u">&nbsp;&nbsp;{{ number_format($loan->loan_amount, 2) }}&nbsp;&nbsp;</span> ක සම්පූර්ණ ණය මුදල සහ ඊට අදාළ පොලිය ණය හිමි පක්ෂය විසින් හෝ එකී සමාගමේ අනුප්‍රාප්තික බලකාර ලැබුම්කාරාදීන් විසින් එකවර ගෙවන ලෙස ඉල්ලා සිටින විටකදී එකී මුදල සදහා වෙනත් වවුචරයක් හෝ ලේඛණයක් ඉදිරිපත් නොකල ද එකී මුදල එසේ එකවර ගෙවා නිදහස් වීමට ද ණයකරු එකග වේ.
                </li>
                <li class="mt-1">
                    තවද, මෙම මුදල් ණය දීමේ ගිවිසුම අර්ථකථනය කිරීමෙී දී මෙම ණය ගිවිසුම සදහා සුරක්ෂිතයක් වශයෙන් ණයකරු විසින් ලබාදී ඇති පොරොන්දු නෝට්ටුව මෙම මුදල් ණයටදීමේ ගිවිසුමෙිම කොටසක් වශයෙන් පිළිගැනීමට දෙපක්ෂයම එකග වේ. ඒ අනුව ගෙවීම් පැහැර හැරියහොත් ණය හිමිට ලඝුකාර්යය පරීපාටිය අනුව මෙම පොදු පොරොන්දු නෝට්ටුව මත නඩු පවරා මෙම මුදල අයකර ගැනීමට හැකි බව පිළිගනී.
                </li>
            </ol>
        </div>

        @php
        $loan_date = $loan->date;
        $date = explode('-',$loan_date);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        @endphp

        <div class="detail">
            මෙම ලියවිල්ලේ සදහන් කොන්දේසි සහ පොරොන්දු කියවා තේරුම් ගෙන / කියවා තේරුම් කර දීමෙන් පසු නිසි ලෙස අවබෝධ කරගෙන ගිවිසිලි හරි ආකාරව ඉෂ්ඨ කිරීම පිණිස ඉහත කී දෙපක්ෂය ඔවුනොවුන්ගේ උරුමක්කාර, පොල්මක්කාර, අද්මිනිස්ත්‍රාසිකාර, බලාකාර ලැබුම්කාර, අනුප්‍රාප්තිකාදීන් සමගින් මෙයින් බැදී මෙම මුදල් ණයට දීමේ ගිවිසුමට වර්ෂ <span class="u">&nbsp;&nbsp;{{$year}}&nbsp;&nbsp;</span> ක් වූ <span class="u">&nbsp;&nbsp;{{$month}}&nbsp;&nbsp;</span> මස <span class="u">&nbsp;&nbsp;{{$day}}&nbsp;&nbsp;</span> දින <span class="u">&nbsp;&nbsp;{{$loan->branch->name}}&nbsp;&nbsp;</span>දී අත්සන් තබන ලදී.
        </div>
        <div class="md-12">
            <div class="row">
                <div class="md-4">
                    <div class="detail text-center">
                        ........................................................... <br>
                        ණයකරු<br>
                        (අත්සන)
                    </div>
                </div>
                <div class="md-4"></div>
                <div class="md-4">
                    <div class="detail text-center">
                        ........................................................... <br>
                        ණය හිමි පක්ෂය <br>
                        ශාඛා කළමණාකරු / ක්ෂේත්‍ර කළමණාකරු / ගණකාධිකාරී<br>
                        නිල මුද්‍රාව

                    </div>
                </div>
            </div>
        </div>
        <div class="detail">
            1 වන ඇපකරුගේ නම: <span class="u">@if($loan->guarantor1 != null)&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor1['customer_id'] ?? '' ) }}-@endif{{ $loan->guarantor1->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br><br>
            අත්සන .................................................... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ජා.හැ.අ. <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->guarantor1->nic_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
        <div class="detail">
            2 වන ඇපකරුගේ නම: <span class="u">@if($loan->guarantor2 != null)&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor2['customer_id'] ?? '' ) }}-@endif{{ $loan->guarantor2->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br><br>
            අත්සන .................................................... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ජා.හැ.අ. <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->guarantor2->nic_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
        <div class="detail">
            3 වන ඇපකරුගේ නම: <span class="u">@if($loan->guarantor3 != null)&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor3['customer_id'] ?? '' ) }}-@endif{{ $loan->guarantor3->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br><br>
            අත්සන .................................................... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ජා.හැ.අ. <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->guarantor3->nic_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
        <div class="detail">
            4 වන ඇපකරුගේ නම: <span class="u">@if($loan->guarantor4 != null)&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor4['customer_id'] ?? '' ) }}-@endif{{ $loan->guarantor4->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br><br>
            අත්සන .................................................... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ජා.හැ.අ. <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->guarantor4->nic_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/num-to-words.js') }} "></script>

    <script>
        var loan = <?php echo json_encode($loan); ?>;
        $(document).ready(function (){
            console.log(loan.loan_amount,loan.interest_rate);
            loanAmountInWords(Number(loan.loan_amount));
            loanInterestInWords(Number(loan.interest_rate));
            window.print();
        });

        function loanAmountInWords(loan_amount){
            $("#loan_amount_in_words,#loan_amount_in_words2,#loan_amount_in_words3").html('');
            var words = '';
            words = toWords(loan_amount);
            $("#loan_amount_in_words,#loan_amount_in_words2,#loan_amount_in_words3").html(words + "Only");
        }

        function loanInterestInWords(interest_rate){
            $("#loan_interest_in_words").html('');
            var words = '';
            words = toWords(interest_rate);
            $("#loan_interest_in_words").html(words + "Percent");
        }

    </script>
</body>
</html>
