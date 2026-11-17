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
            margin-top: 2rem;
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
        .f1{
            font-size: 22px;
            font-weight: bold;
        }
        .f2{
            font-size: 20px;
            font-weight: bolder;
        }
        .f3{
            font-size: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="heading flex-table">
            <div class="valign">
                <span class="f1"> {{ config('app.name') }}</span><br>
                <span class="f2">ඇප බැදුම්කරය </span><br>
                <span class="f3">සියලු දෙනාම දැනගත යුතුයි. <br>
                මෙහි ඇතැම් තැනක ඇපකරුවන් යැයි කියනු ලබන</span>
            </div>
        </div>
        <div class="detail">
            {{-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; --}}
            <span class="u">@if($loan->guarantor1 != null)&nbsp;&nbsp;{{ $loan->guarantor1->address ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>හි පදිංචි<br> <span class="u">&nbsp;&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor1['customer_id'] ?? '' ) }}-{{ $loan->guarantor1->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> ( ජා.හැ.අ. <span class="u">&nbsp;&nbsp;{{ $loan->guarantor1->nic_no ?? '' }}</span> )<br>@endif
            @if($loan->guarantor2 != null) <span class="u">&nbsp;&nbsp;{{ $loan->guarantor2->address ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> හි පදිංචි<br> <span class="u">&nbsp;&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor2['customer_id'] ?? '' ) }}-{{ $loan->guarantor2->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> ( ජා.හැ.අ. <span class="u">&nbsp;&nbsp;{{ $loan->guarantor2->nic_no ?? '' }}&nbsp;&nbsp;</span> )<br>@endif
            @if($loan->guarantor3 != null) <span class="u">&nbsp;&nbsp;{{ $loan->guarantor3->address ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> හි පදිංචි<br> <span class="u">&nbsp;&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor3['customer_id'] ?? '' ) }}-{{ $loan->guarantor3->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> ( ජා.හැ.අ. <span class="u">&nbsp;&nbsp;{{ $loan->guarantor3->nic_no ?? '' }}&nbsp;&nbsp;</span> )<br>@endif
            @if($loan->guarantor4 != null) <span class="u">&nbsp;&nbsp;{{ $loan->guarantor4->address ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> හි පදිංචි<br> <span class="u">&nbsp;&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor4['customer_id'] ?? '' ) }}-{{ $loan->guarantor4->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> ( ජා.හැ.අ. <span class="u">&nbsp;&nbsp;{{ $loan->guarantor4->nic_no ?? '' }}&nbsp;&nbsp;</span> )<br>@endif
            යන අය, මෙහි පහත ඇතැම් තැනක ඇප හිමි පාර්ශවය යැයි කියනු ලබන <span class="u">&nbsp;&nbsp;NO 51, THAMMITA ROAD, NEGOMBO&nbsp;&nbsp;</span> හි තම ප්‍රදාන ව්‍යාපාරික කාර්යාලය සහ හෝ ව්‍යාපාරික ස්ථානය පවත්වාගෙන යනු ලබන
            @php
            $loan_date = $loan->date;
            $date = explode('-',$loan_date);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
            @endphp
            <span class="u">&nbsp;&nbsp;{{ config('app.name') }}&nbsp;&nbsp;&nbsp;</span> (ලි.ප.අංකය: <span class="u">&nbsp;&nbsp;PV127252&nbsp;&nbsp;</span>) වෙත වර්ෂ <span class="u">&nbsp;&nbsp;&nbsp;{{$year}}&nbsp;&nbsp;&nbsp;</span> ක් වූ <span class="u">&nbsp;&nbsp;&nbsp;{{$month}}&nbsp;&nbsp;&nbsp;</span> මස <span class="u">&nbsp;&nbsp;&nbsp;{{$day}}&nbsp;&nbsp;&nbsp;</span> දින ලබාදෙන කියවා තේරුම්කරදීමෙන් වටහාගත් මෙම ඇප බැදුම්කරයේ වග මෙසේ ය.
        </div>
        <div class="detail">
            <ol>
                <li>
                    ඉහත කී ඇප හිමි පාර්ශවය වන <span class="u">&nbsp;&nbsp;{{ config('app.name') }}&nbsp;&nbsp;&nbsp;</span> සමග මෙකී ඇතැම් තැනක යැයි කියනු ලබන <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->customer->address }}&nbsp;&nbsp;&nbsp;</span> හි පදිංචි
                    <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->customer->name }}&nbsp;&nbsp;&nbsp;</span> (ජා.හැ.අ. <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->customer->nic_no }}&nbsp;&nbsp;&nbsp;</span>) යන අය <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->date }}&nbsp;&nbsp;&nbsp;</span> දින ඇතිකර ගනු ලැබූ මුදල් ණයට දීමේ ගිවිසුම මත සහ ඒ අනුව ශ්‍රී ලංකාවේ වලංගු මුදලින් රුපියල්
                    &nbsp;<span class="loan_amount_in_words u" id="loan_amount_in_words" name="loan_amount_in_words"></span>&nbsp; (රු. <span class="u">&nbsp;&nbsp;&nbsp;{{ number_format($loan->loan_amount, 2) }}&nbsp;&nbsp;&nbsp;</span>) ක ණය මුදලක් ඉහත කී මුදල් ණයට දීමේ ගිවිසුමේ සදහන් කොන්දේසි සහ පොරොන්දු ගිවිසිවලට යටත්ව ඉහත කී ණයකරු විසින් <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->date }}&nbsp;&nbsp;&nbsp;</span> දින ලබාගත් බව ඇපකාර පාර්ශවය පිළිගනී.
                </li>
                <li class="mt-1">
                    ඒ අනුව ඉහත කී රු.<span class="u">&nbsp;&nbsp;&nbsp;{{ number_format($loan->loan_amount, 2) }}&nbsp;&nbsp;&nbsp;</span> ක ණය මුදල, ඉහත කී ණයට දීමේ ගිවිසුමේ සදහන් කොන්දේසි සහ පොරොන්දු ගිවිසිලි වලට යටත් ව ඉහත කී ණයකරු වන <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->customer->name }}&nbsp;&nbsp;&nbsp;</span> යන අය විසින් ඉහත කී <span class="u">&nbsp;&nbsp;&nbsp;{{ config('app.name') }}&nbsp;&nbsp;&nbsp;</span>
                    වෙත එකී සම්පූර්ණ ණය මුදල සහ ඊට අදාළ පොලිය ද සමග සහ / හෝ හිග ණය මුදල (දඩ පොලිය ද ඇතුළත්ව) ගෙවීමට අපොහොසත් වුව හොත් සහ / හෝ නොසලකා හැරිය හොත් / හෝ පැහැර හරිනු ලැබුව හොත් එකී සම්පූර්ණ ණය මුදල සහ ඊට අදාළ පොලිය සහ /හෝ හිග ණය මුදල (දඩ පොළිය ද ඇතුළත්ව) ඉහත කී ණයට දීමේ ගිවිසුමට ප්‍රකාරව ගෙවීම සදහා එකග වී බැදී මෙම බැදුම්කරය ඇපකාර පාර්ශවය වන අප විසින් ඇප හිමිකාර පාර්ශවය වෙත එක්ව හා වෙන්ව ලබාදීමට එකග වෙමි.
                </li>
                <li class="mt-1">
                    ඇප හිමි පාර්ශවය විසින් ඉහත කී ණය මුදල සහ ඊට අදාළ පොලිය ඇතුලු අනෙකුත් ගාස්තු ණයකරුගෙන් සහ ඇපකාර පාර්ශවයන්ගෙන් එක්ව හා වෙන් වෙන්ව ඉල්ලා සිටීමට සහ අයකර ගැනීමේ දී ගනු ලබන නීතිමය ක්‍රියාමාර්ගවලදී ඉහත කී ණයකරු සහ ඇපකාර පාර්ශවයට එරෙහිව එක්ව හා වෙන් වෙන්ව නඩු පැවරීමට ඇපහිමිකාර පාර්ශවයට අයිතිය ඇති බවට ද ඇපකාර පාර්ශවයන් පිළිගනී.
                </li>
                <li class="mt-1">
                    තව ද ඉහත කී මුදල් ණයට දීමේ ගිවිසුම අර්ථකථනය කිරීමේ දී මෙම බැදුම්කරය එකී මුදල් ණයට දීමේ ගිවිසුමේම කොටසක් වශයෙන් පිළිගැනීමට ඇපකාර පාර්ශවයන් මෙහි ඇප බැදුම්කරය ලියා අත්සන් කිරීම මගින් වැඩිදුරටත් එකග වී පිළිගනු ලබයි.
                </li>
                <li class="mt-1">
                    තව ද ණයකරු විසින් ඉහත කී මුදල් ණයට  දීමේ ගිවිසුම ප්‍රකාරව ගෙවිය යුතු මුදල් සහ අදාළ පොලිය ගෙවීම් පැහැර හරින්නේ නම්, එම මුදල ණයකරුගෙන් මුලින් ඉල්ලා සිටින්නේ නැතිව ඉහත කී ඇපකාර පාර්ශවයෙන් මුලින් ඉල්ලා සිටීමට අයිතියක් ඇති බවටත්, ඉහත කී ණයකරු සමග එක්ව හෝ වෙන් වෙන්ව හෝ ඇපකාර පාර්ශවයට එරෙහිව පමණක් එකී මුදල ඉල්ලා නඩු පැවරීමේ අයිතිය ඇපහිමි පාර්ශවයට ඇති බවට ඇපකාර පාර්ශවය පිළිගනී.
                </li>
                <li class="mt-1">
                    තව ද ඇපකාර පාර්ශවය ඇපකරුවන්ට හිමි වරප්‍රසාද වන අයවිය යුතු මුදල් ණයකරුගෙන් මුලින් ඉල්ලා අයකරගත යුතු යැයි ඉල්ලා සිටීමේ අයිතිය සහ ණයකර‌ැට විරැද්ධව මුලින් නඩු පවරා අයකරගත යුතු යැය‌ි ඉල්ලා සිටීමේ අයිතිය සහ අයවිය යුතු මුදල් ඇපකාර පාර්ශවයන් අතර ප්‍රමාණුකූලව බෙදා අයකර යුතු යැයි ඉල්ලා සිටීමේ අයිතිය සහ ඇපකරුවන්ට එරෙහි ව පවරන ලද නඩුවක දී ණයහිමි වෙත අයවිය යුතු මුදල ඇපකරුවන් අතර ප්‍රමාණුකූලව බෙදා අයකර ගත යුතු යැයි ඉල්ලා සිටීමේ අයිතිය ඇපකරුවන් හට නීතිය අනුව හෝ සාධාරණත්වය මත හිමි අයිතිවාසිකම් සහ වරප්‍රසාද මෙයින් ප්‍රකාශිතවම අතහැර දමනු ලබයි.
                </li>
            </ol>
        </div>
        <div class="detail">
            1 වන ඇපකරුගේ නම: <span class="u">@if($loan->guarantor1 != null)&nbsp;&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor1['customer_id'] ?? '' ) }}-@endif{{ $loan->guarantor1->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br><br>
            අත්සන .................................................... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ජා.හැ.අ. <span class="u">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $loan->guarantor1->nic_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
        <div class="detail">
            2 වන ඇපකරුගේ නම: <span class="u">@if($loan->guarantor2 != null)&nbsp;&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor2['customer_id'] ?? '' ) }}-@endif{{ $loan->guarantor2->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br><br>
            අත්සන .................................................... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ජා.හැ.අ. <span class="u">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $loan->guarantor2->nic_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
        <div class="detail">
            3 වන ඇපකරුගේ නම: <span class="u">@if($loan->guarantor3 != null)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor3['customer_id'] ?? '' ) }}-@endif{{ $loan->guarantor3->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br><br>
            අත්සන .................................................... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ජා.හැ.අ. <span class="u">&nbsp;&nbsp;{{ $loan->guarantor3->nic_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
        <div class="detail">
            4 වන ඇපකරුගේ නම: <span class="u">@if($loan->guarantor4 != null)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor4['customer_id'] ?? '' ) }}-@endif{{ $loan->guarantor4->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br><br>
            අත්සන .................................................... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ජා.හැ.අ. <span class="u">&nbsp;&nbsp;{{ $loan->guarantor4->nic_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
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
            $("#loan_amount_in_words").html('');
            var words = '';
            words = toWords(loan_amount);
            $("#loan_amount_in_words").html(words + "Only");
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
