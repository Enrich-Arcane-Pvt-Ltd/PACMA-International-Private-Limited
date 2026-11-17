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
        .md-5{
            width: 42%;
        }
        .md-4{
            width: 33%;
        }
        .md-3{
            width: 25%;
        }
        .md-2{
            width: 16%;
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
        <div class="date">
            <span>දිනය :&nbsp; <span class="u">{{ $loan->date }}</span>
        </div>
        <div class="heading flex-table">
            <div class="md-12 valign ">
                <center>
                    <h2 style="border: 2px solid; width: 40%;">
                        පොරොන්දු නෝට්ටුව<br>
                        රු <span class="u">{{ number_format($loan->loan_amount, 2) }}</span>
                    </h2>
                </center>
            </div>
        </div>

        <div class="detail">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            මෙහි පහත අත්සන් කරන <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->customer->address }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> හි පදිංචි
            <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->customer->name }}&nbsp;&nbsp;&nbsp;</span> (ජා. හැ. අංක <span class="u">&nbsp;&nbsp;&nbsp;{{ $loan->customer->nic_no }}&nbsp;&nbsp;&nbsp;</span>) වන මම,
            <span class="u">&nbsp;&nbsp;NO 61, K C DE SILVAPURAYA, THIMBIRIGASKATUWA&nbsp;&nbsp;</span> හි ප්‍රධාන ව්‍යාපාරික කාර්යාලය සහ / හෝ ව්‍යාපාරික ස්ථානය පවත්වාගෙන යන
            <span class="u">&nbsp;&nbsp;{{ config('app.name') }}&nbsp;&nbsp;</span> (ලි.ප.අංකය <span class="u">&nbsp;&nbsp;PV127252&nbsp;&nbsp;</span>) වෙතින් ශ්‍රී ලංකාවේ වලංගු මුදලින් රුපියල් &nbsp;<span class="loan_amount_in_words u" id="loan_amount_in_words" name="loan_amount_in_words"></span>&nbsp;
            (රු <span class="u">&nbsp;&nbsp;{{ number_format($loan->loan_amount, 2) }}&nbsp;&nbsp;</span>/=) ක මුදලක් හිග නැතිව ණයට ඉල්ලා භාර ගන්නට යෙදුනා ඇත.<br>
            <br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            එම මුදල එකී සමාගම හෝ එකී සමාගමේ ආඥව පිට එකී සමාගමේ යම් නියෝජිතයෙක් විසින් ඉල්ලා සිටි විට ගෙවන්නට ද සහ එසේ ගෙවා නිදහස් වන තුරු මුළු මුදල මත අර්ධ වාර්ෂිකව/ වාර්ෂිකව/ <span class="u2"> මාසිකව </span>/ දෛනිකව <span class="loan_interest_in_words u" id="loan_interest_in_words" name="loan_interest_in_words"></span> සියයට (<span class="u">{{ $loan->interest_rate }}%</span>) බැගින් පොළි ගෙවන හැටියට ද පොරොන්දු වී අත්සන් තැබුවෙමි.<br>
            <br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            තව ද ඉහත කී ණය මුදල වන රුපියල් &nbsp;<span class="loan_amount_in_words u" id="loan_amount_in_words2" name="loan_amount_in_words"></span>&nbsp; (රු <span class="u">&nbsp;&nbsp;{{ number_format($loan->loan_amount, 2) }}&nbsp;&nbsp;</span> /=) ක මුදල
            හිග නැතිව ණයට ඉල්ලා භාර ගනු ලැබූ අවස්ථාව වන විට එකී මුදලින් කිසිදු වාරිකයක්, පොළියක් හෝ ගාස්තුවක් අත්තිකාරම් මුදලක් ලෙස මා විසින් ඉහත කී <span class="u">&nbsp;&nbsp;{{ config('app.name') }}&nbsp;&nbsp;</span> වෙත ගෙවා නැති බව ද සහතික කරමි. පොරොන්දු වී අත්සන් තබා භාර දුනිමි.
            <br><br>
            සාක්ෂි : ඉහත කරුණු කියවා බලා තේරුම් ගෙන අත්සන් තැබුවෙමි.
        </div>
        <div class="md-12 mt-3">
            <div class="row">
                <div class="md-2">
                    {{-- <div class="valign"> --}}
                        <div class="flex-table"style="width: 90%; height: 6rem; border: 1.5px solid black; border-radius: 20px; text-align: center; margin-top: 8px;">
                            <div class="valign" >
                                මුද්දරය
                            </div>
                        </div>
                    {{-- </div> --}}
                </div>
                <div class="md-5" style="paddding: 1rem; line-height: 1.5;">
                    01......................................................................<br>
                    සම්පූර්ණ නම <span class="u">@if($loan->guarantor1 != null)&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor1['customer_id'] ?? '' ) }}-@endif{{ $loan->guarantor1->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
                    ජා.හැ.අ. <span class="u">&nbsp;&nbsp;&nbsp;&nbsp;{{ $loan->guarantor1->nic_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
                    ස්ථිර ලිපිනය <span class="u">&nbsp;&nbsp;&nbsp;&nbsp;{{ $loan->guarantor1->address ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
                    ..........................................................................
                </div>
                <div class="md-5" style="paddding: 1rem; line-height: 1.5;">
                    02......................................................................<br>
                    සම්පූර්ණ නම <span class="u">@if($loan->guarantor2 != null)&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor2['customer_id'] ?? '' ) }}-@endif{{ $loan->guarantor2->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
                    ජා.හැ.අ. <span class="u">&nbsp;&nbsp;&nbsp;&nbsp;{{ $loan->guarantor2->nic_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
                    ස්ථිර ලිපිනය <span class="u">&nbsp;&nbsp;&nbsp;&nbsp;{{ $loan->guarantor2->address ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
                    ..........................................................................
                </div>
            </div>
        </div>
        <div class="md-12 mt-2">
            <div class="row">
                <div class="md-2">
                    {{-- <div class="valign"> --}}
                        <div class="flex-table"style="width: 90%; height: 6rem; border: 1.5px solid black; border-radius: 20px; text-align: center; margin-top: 8px;">
                            <div class="valign" >
                                ඇගිලි සළකුණ
                            </div>
                        </div>
                    {{-- </div> --}}
                </div>
                <div class="md-5" style="paddding: 1rem; line-height: 1.5;">
                    03......................................................................<br>
                    සම්පූර්ණ නම <span class="u">@if($loan->guarantor3 != null)&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor3['customer_id'] ?? '' ) }}-@endif{{ $loan->guarantor3->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
                    ජා.හැ.අ. <span class="u">&nbsp;&nbsp;&nbsp;&nbsp;{{ $loan->guarantor3->nic_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
                    ස්ථිර ලිපිනය <span class="u">&nbsp;&nbsp;&nbsp;&nbsp;{{ $loan->guarantor3->address ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
                    ..........................................................................
                </div>
                <div class="md-5" style="paddding: 1rem; line-height: 1.5;">
                    04......................................................................<br>
                    සම්පූර්ණ නම <span class="u">@if($loan->guarantor4 != null)&nbsp;{{ AUth::user()->customerNumberFormat($loan->guarantor4['customer_id'] ?? '' ) }}-@endif{{ $loan->guarantor4->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
                    ජා.හැ.අ. <span class="u">&nbsp;&nbsp;&nbsp;&nbsp;{{ $loan->guarantor4->nic_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
                    ස්ථිර ලිපිනය <span class="u">&nbsp;&nbsp;&nbsp;&nbsp;{{ $loan->guarantor4->address ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
                    ..........................................................................
                </div>
            </div>
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
            $("#loan_amount_in_words,#loan_amount_in_words2").html('');
            var words = '';
            words = toWords(loan_amount);
            $("#loan_amount_in_words,#loan_amount_in_words2").html(words + "Only");
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
