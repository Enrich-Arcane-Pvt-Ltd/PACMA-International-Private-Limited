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
        .f4{
            font-size: 12px;
        }
        .text-block{
            font-size: 14px;
            margin-bottom: 8px;

        }

        .text-block-single{
            display: flex;
            align-items: center;
        }
        .mr-10{
            margin-right: 10px;
        }
        .mr-20{
            margin-right: 30px;
        }
        .tick-box{
            height: 20px;
            width: 20px;
            border: 1px solid black;
            display: inline-block;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;

        }
        .divider {
            border-bottom: 1px solid rgb(147, 146, 146);
            margin-block: 20px;
        }
        .grow{
            flex-grow: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="heading flex-table">
            <div class="valign">
                <h4 style="margin-block: 5px !important;font-size: 15px;">ඇපකරු ප්‍රකාශය</h4>
                <h4 style="margin-block: 5px !important;font-size: 15px;">(සෑම කොටසක්ම අනිවාර්යයෙන්ම සම්පූර්ණ කළ යුතුය)</h4>

            </div>
        </div>
        <ul>
            <li><h5  style="margin-block: 5px !important;font-size: 13px;">ප්‍රාග්ධන මුදල් ඉල්ලුම් කරනු ලබන අයගේ සැමියා / බිරිද / දරුවන් හෝ ප්‍රාග්ධන මුදල් ඉල්ලුම්කරුගේ ලිපිනයෙහිම පදිංචි වෙනත් බාහිර පාර්ශවයන් හෝ මේ සඳහා වලංගු නොවේ.</h5></li>
        </ul>

        <div class="text-block text-block-single">
            1.  සම්පූර්ණ නම :
                <span class="u grow">
                    @if($guarantor != null)&nbsp;&nbsp;
                        {!!
                            AUth::user()->customerNumberFormat( $guarantor['customer_id'] ?? '' ) .' - '.($guarantor->name ?? '')
                        !!}
                    @endif
                </span>
        </div>
        <div class="text-block  text-block-single">
            2.  ජාතික හැ. අංකය :
            <span class="u mr-20 ">@if($guarantor != null)
                &nbsp;&nbsp;
                @endif
                {{ $guarantor->nic_no ?? '' }}
                {!! str_repeat('&nbsp;', 37) !!}
            </span>
            උපන්දිනය
            <span class="u grow">

                @if($guarantor != null)&nbsp;&nbsp;
                    {{ $guarantor->dob ?? '' }}
                @endif
            </span>
        </div>
        <div class="text-block" >
            <div style="display: flex; flex-direction: row; align-items: center; ">
                3. විවාහක
                     &nbsp;&nbsp;
                    <div class="tick-box">@if($guarantor->details->marital_status==0) X @endif</div>

                    <span class="mr-20"></span><span class="mr-20"></span>
                    අවිවාහක &nbsp;&nbsp;
                    <div class="tick-box">@if($guarantor->details->marital_status==1)X @endif</div>

                    <span class="mr-20"></span><span class="mr-20"></span>
                    ස්ත්‍රි &nbsp;&nbsp;
                    <div class="tick-box">@if($guarantor->details->gender==1)X @endif</div>

                    <span class="mr-20"></span><span class="mr-20"></span>
                    පුරුෂ &nbsp;&nbsp;
                    <div class="tick-box">@if($guarantor->details->gender==0)X @endif</div>
            </div>

        </div>

        <div class="text-block">
            <div class="text-block-single">
                4.  පදිංචි ලිපිනය :
                <span class="u grow">
                    @if($guarantor != null)&nbsp;&nbsp;
                        {{ $guarantor->address ?? '' }}
                    @endif
                </span>
            </div>

                {!! str_repeat('&nbsp;', 35) !!}<span class="f4">(කුලි පදනම / තමන් සතු )</span>

        </div>


        <div class="text-block   text-block-single">
            5.  පෞද්ගලික දුරකතන අංකය <span class="mr-20"></span>(1):
                <span class="u grow">
                    @if($guarantor != null)&nbsp;&nbsp;
                    {{ $guarantor->phone_mobile ?? '' }}
                    @endif
                </span>

                <span class="mr-20"></span>(2):
                <span class="u  grow">
                    @if($guarantor != null)&nbsp;&nbsp;
                    {{ $guarantor->phone ?? '' }}
                    @endif
                </span>
        </div>

        <div class="text-block">
            <div class="text-block-single">
                6.  ස්ථිර ලිපිනය :
                <span class="u grow">
                    @if($guarantor != null)&nbsp;&nbsp;
                        {{ $guarantor->address_nic ?? '' }}
                    @endif
                    {!! str_repeat('&nbsp;', 140) !!}
                </span>
            </div>

            <span class="u">
                {!! str_repeat('&nbsp;', 225) !!}
            </span>

        </div>

        <div class="text-block" >
            <div style="display: flex; flex-direction: row; align-items: center; ">
                7.  ඔබ නිරතව සිටින රැකියා ක්ෂේත්‍රය :
                    <span class="mr-10"></span>
                    රජයේ රැකියා &nbsp;&nbsp;
                    <div class="tick-box">@if($guarantor->details->marital_status==0) X @endif</div>

                    <span class="mr-10"></span>
                    පෞද්ගලික රැකියා &nbsp;&nbsp;
                    <div class="tick-box">@if($guarantor->details->marital_status==1) X @endif</div>

                    <span class="mr-10"></span>
                    ව්‍යාපාර &nbsp;&nbsp;
                    <div class="tick-box">@if($guarantor->details->marital_status==2) X @endif</div>

                    <span class="mr-10"></span>
                    ස්වයං රැකියා &nbsp;&nbsp;
                    <div class="tick-box">@if($guarantor->details->marital_status==3) X @endif</div>
            </div>
            <div style="display: flex; flex-direction: row; align-items: center; ">
                {!! str_repeat('&nbsp;', 30) !!}
                ( ඉදිරියේ නිරත වීමට බලාපොරොත්තුවෙත් සිටි &nbsp;&nbsp;
                <div class="tick-box">@if($guarantor->details->marital_status==4) X @endif</div> &nbsp;)
            </div>
        </div>

        <div class="text-block">
            <div class="text-block-single">
                8.  රැකියාව ස්වභාවය කුමක්ද යන වග :
                <span class="u grow">
                    @if($guarantor != null)&nbsp;&nbsp;
                    {{ $guarantor->job_title ?? '' }}
                    @endif
                    {!! str_repeat('&nbsp;', 98) !!}
                </span>
            </div>
            <div class="text-block-single">
                &nbsp;&nbsp;&nbsp;&nbsp;රැකියා හෝ ව්‍යාපාරික ස්ථානයේ නම ලිපිනය දු.ක. අං :
                <span class="u grow">
                    @if($guarantor != null)&nbsp;&nbsp;
                        {{ $guarantor->employment_name ?? '' }}
                    @endif
                </span>
            </div>
            <div class="text-block-single">
                <span class="u grow">
                    @if($guarantor != null)&nbsp;&nbsp;&nbsp;&nbsp;
                        {{ $guarantor->employment_place ?? '' }}
                        &nbsp;-&nbsp;
                        {{ $guarantor->phone_office ?? '' }}
                    @endif
                </span>
            </div>
        </div>


        <div class="text-block text-block-single">
            9.  ප්‍රාග්ධන මුදල් ඉල්ලුම්කරු හා ඔබ අතර ඇති සබඳකම කුමක්ද?
            <span class="u grow">
                @if($guarantor != null)&nbsp;&nbsp;
                    {{ $guarantor->relationship_to_hirer ?? '' }}
                @endif
            </span>
        </div>

        <div class="text-block" >
            <div style="display: flex; flex-direction: row; align-items: center; ">
                10.  ඔබ විසින් අප ආයතනයේ ප්‍රාග්ධන මුදල් පහසුකම් ලබාගෙන තිබේද? :
                    <span class="mr-20"></span>
                    <span class="mr-20"></span>
                    <span class="mr-20"></span>
                    <span class="mr-20"></span>
                    (&nbsp;
                    ඔවි &nbsp;&nbsp;
                    <div class="tick-box">@if ($loan_count>0) X @endif </div>

                    <span class="mr-10"></span>
                    නැත &nbsp;&nbsp;
                    <div class="tick-box">@if ($loan_count==0) X @endif</div>
                    &nbsp;)
            </div>

        </div>

        <div class="text-block" >
            <div style="display: flex; flex-direction: row; align-items: center; ">
                <span>11.  අප ආයතනයේ වෙතත් ප්‍රාග්ධන ගිණුම් වෙනුවෙන් ඔබ ඇප අත්සන් තබා තිබේද? :</span>
                <span class="mr-20"></span><span class="mr-10"></span>
                <span style="display: flex; flex-direction: row; align-items: center; ">
                    (&nbsp;
                    ඔවි &nbsp;&nbsp;
                    <div class="tick-box"> @if ($guarant_count>0) X @endif </div>

                    <span class="mr-10"></span>
                    නැත &nbsp;&nbsp;
                    <div class="tick-box">@if ($guarant_count==0) X @endif</div>
                    &nbsp;)
                </span>
            </div>

        </div>

        <div class="text-block text-block-single">
            12.  තිබේ නම් ප්‍රාග්ධන ගිණුම් කියක් වෙනුවෙන් ද යන වග :
                <span class="u grow">
                    @if($guarantor != null)&nbsp;&nbsp;
                        {{$guarant_count}}
                    @endif
                </span>
        </div>

        <div class="text-block" style="margin-top: 20px;">

            <div style="display: flex; flex-direction: row; justify-content: space-between; ">
                <b class="text-block-single">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;අත්සන : <span class="u grow" style="width:200px;">&nbsp;&nbsp; &nbsp;&nbsp;</span></b>
                <b class="text-block-single">දිනය :<span class="u grow" style="width:200px;">&nbsp;&nbsp;{{$loan->date ?? ''}}&nbsp;&nbsp;</span></b>
            </div>
        </div>


        <div class="divider"></div>

        <div class="text-block" style="margin-top: 20px;padding-left: 20px;">
            මෙහි ප්‍රාග්ධන ඉල්ලුම්කාර පාර්ශවය වන <span class="u">&nbsp;&nbsp;{{$loan->customer->name ?? ''}}&nbsp;&nbsp;</span> යන අය දෛනිකව
            රුපියල්<span class="u">&nbsp;&nbsp;{{$loan->daily_installment ?? ''}}&nbsp;&nbsp;</span> බැගින් වාරික
            <span class="u">&nbsp;&nbsp;{{$loan->daily_time_period ?? ''}}&nbsp;&nbsp;</span> කින් ආපසු නේසන් ග්‍රෝ ආයතනයට ගෙවා නිම කිරිමට
            ලබාගත් රු<span class="u">&nbsp;&nbsp;{{$loan->total_amount ?? ''}}&nbsp;&nbsp;</span> ක මුදල යම් හෙයකින්<span class="u">&nbsp;&nbsp;{{$loan->due_date ?? ''}}&nbsp;&nbsp;</span> දින වන විටදී ගෙවීමට අපොහොසත් වන්නේ
            නම් හෝ පැහැර හරින්නේ නම්, පෙර කී ආයතනය <span class="u">&nbsp;&nbsp;{{$guarantor->name ?? ''}}&nbsp;&nbsp;</span> වන මා වෙත ඒ බව දන්වන
            දිනක එහි සම්පූර්ණ හිහ මුදල ගෙවීමට හැකි බව මෙමගින් ප්‍රකාශ කර සිටිමි. මෙම ප්‍රාග්ධන මුදල ලබාගත් දින සිටම ප්‍රාග්ධන
            ඉල්ලුම්කාර පාර්ශවය හා සමානවම සම්පූර්ණ මුදල ගෙවා අවසන් වනතුරු මැද මෙකී වගකීමට බැඳී සිටින බව කොන්දේසි විරහිතව
            පිදිගනිමි. යම් හෙයකින් සමාගම තීරණය කරන දිගත ඉහත ය මුදල ප්‍රතිව්‍යුහගත කිරීමකට සිදුවුවහොත් ඒ සදහා ඇපකරු ලෙස වගකිමට බැදෙන බව පිළිගනිමි. එමෙන්ම මෙම ප්‍රකාශය මා විසින් නිසිලෙස කියවා බලා, තේරුම්ගෙන, එකඟ වී අත්සන තබ
            සිදුකරන බව වැඩිදුරටත් ප්‍රකාශ කරමි.
        </div>

        <div class="text-block" style="margin-top: 20px;">

            <div style="display: flex; flex-direction: row; justify-content: space-between; ">
                <b class="text-block-single">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;නම : <span class="u grow" style="width:200px;">&nbsp;&nbsp;{{$loan->customer->name ?? ''}}&nbsp;&nbsp;</span></b>
                <b class="text-block-single">ඇපකරු අත්සන :<span class="u grow" style="width:200px;">&nbsp;&nbsp; &nbsp;&nbsp;</span></b>
            </div>
        </div>


        <div class="divider"></div>

        <div class="text-block" style="margin-top: 20px;padding-left: 20px;">
            ප්‍රාග්ධන ඉල්ලුම්කරු වන <span class="u">&nbsp;&nbsp;{{$loan->customer->name ?? ''}}&nbsp;&nbsp;</span>මාගේ විවෘත ප්‍රකාශයයි.
            ජා. හැ. අංක<span class="u">&nbsp;&nbsp;{{$loan->customer->nic_no ?? ''}}&nbsp;&nbsp;</span>
            දරන<span class="u">&nbsp;&nbsp;{{$loan->customer->name ?? ''}}&nbsp;&nbsp;</span>වන
            මා විසින් <span class="u">&nbsp;&nbsp;7B 109/ Kottawa Road, Rukmalgama&nbsp;&nbsp;</span> යන ලිපිනය වෙත
            පැමිණ<span class="u">&nbsp;&nbsp;{{$loan->date ?? ''}}&nbsp;&nbsp;</span>දින ලබාගත්
            රු<span class="u">&nbsp;&nbsp;{{$loan->total_amount ?? ''}}&nbsp;&nbsp;</span>ක ප්‍රාග්ධන මුදල දින වන විට හෝ ආයතන එකගතාව මත ලබා දෙන සහන කාල සීමාව ඇතුළත හෝ ගෙවා නිම කිරීමට අපොහොසත් වුවහොත් සමාගයම් කැමැත්ත පරිදි මා සතු නිශ්චල හෝ චංචල වස්තුවක්/
            දේපලක් මුදල් කර හෝ ආයතනයට පවරා දීම මගින් හෝ එදිනට පවතින ගිග
            මුදල නේෂන් ග්‍රෝ ආයතනයට ගෙවා පියවීමට කොන්දෙසි
            විරහිතව එකඟ වන අතර, මෙම එකඟතාවය මගේ කැමැත්ත පරිදි ස්වාධීනව කියවා බලා තේරුම් ගෙන ලෙස
            අවබෝධ කරගත් පසුව සිදුකරන බව වැඩිදුරටත් ප්‍රකාශ කර සිටීමි.
            <br>
            එසේම යම් හෙයකින් නියමිත දින 60 හා අනුග්‍රාහක කාලය වන දින 65 ක කාලය තුල ණය මුදල සම්පූර්ණයෙන්ම ගෙවීමට
            අපොහොසත් උවහොත් එදිනට පවතිත ශේෂය මත අදාළ ණය මුදල ප්‍රතිව්‍යුහගත කිරීමට නේෂන් ග්‍රෝ පුද්ගලික සමාගම තීරණය
            කළහොත් ඒ සඳහා කොන්දේසි විරහිතව එකඟ වන අතර ණය මුදල ලබා ගන්නා දිනට අදාළ අයදුම් පත්‍රයම ඒ සදහා නීත්‍යානුකූලව
            වලංගු බවත්, ණය මුදල ලබා ගන්නා දින වන විට ඇතුලත් කර තිබු ඇපකරු <span class="u">&nbsp;&nbsp;{{$guarantor->name ?? ''}}&nbsp;&nbsp;</span>(නම) අයව ඒ සඳහා නීත්‍යානුකූලව වලංගු බවත් කොන්දේසි විරහිතව පිළිගත්තා බව තවදුරටත් ප්‍රකාශ කර සිටිමි.

        </div>


        <div class="text-block" style="margin-top: 20px;">

            <div style="display: flex; flex-direction: row; justify-content: space-between; ">
                <b class="text-block-single">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;සම්පුර්ණ නම : <span class="u grow" style="width:200px;">&nbsp;&nbsp;{{$guarantor->name ?? ''}}&nbsp;&nbsp;</span></b>
                <b class="text-block-single">අත්සන :<span class="u grow" style="width:200px;">&nbsp;&nbsp; &nbsp;&nbsp;</span></b>
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
