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
            margin-right: 11px;
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
            font-size: 10px;
        }
        .f5{
            font-size: 10px;
        }
        .text-block{
            font-size: 12px;
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
            height: 15px;
            width: 15px;
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

        .address-block{
            font-size: 11px;
            position: absolute;
            left: 700px;
            text-align: start;
            top: 30px;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="heading flex-table">
            <div class="valign">
                <h4 style="margin-block: 5px !important;font-size: 13px;">ප්‍රධාන අයදුම් පත්‍රය <br>1 වන කොටස
                    <br>
                    ප්‍රාග්ධන ආයෝජන අයදුම් පත්‍රය</h4>
            </div>
            <div class="address-block">
                <span>නේසන් ග්‍රෝ පුද්. සමාගම <br>නො. 360/02/03, <br>කටුවාන පාර,හෝමගම <br>දුර.අංකය :</span><br><br>
            </div>
        </div>

        <div class="text-block" style="margin-bottom: 0px !important;">
            <div class="">
                ප්‍රාග්ධන ආයෝජන අංකය :
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                    {{ \Auth::user()->loanNumberFormat($loan->id) }}
                    @endif
                </span>
            </div>

            {!! str_repeat('&nbsp;', 70) !!}<span class="f4">(සෑම කොටසක්ම සම්පූර්ණ කළ යුතුය )</span>

        </div>

        <div class="divider" style="margin-top: 5px !important;"></div>


        <div class="text-block text-block-single">
            1.  සම්පූර්ණ නම :
            <span class="u grow">
                @if($customer != null)&nbsp;&nbsp;
                    {!!
                        AUth::user()->customerNumberFormat( $customer['customer_id'] ?? '' ) .' - '.($customer->name ?? '')
                    !!}
                @endif
            </span>
        </div>
        <div class="text-block  text-block-single">
            2.  ජාතික හැ. අංකය :
            <span class="u mr-20 ">@if($customer != null)
                &nbsp;&nbsp;
                @endif
                {{ $customer->nic_no ?? '' }}
                {!! str_repeat('&nbsp;', 37) !!}
            </span>
            උපන්දිනය
            <span class="u grow">

                @if($customer != null)&nbsp;&nbsp;
                    {{ $customer->dob ?? '' }}
                @endif
            </span>
        </div>
        <div class="text-block" >
            <div style="display: flex; flex-direction: row; align-items: center; ">
                3.  විවාහක
                    &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->marital_status==0) X @endif</div>

                    <span class="mr-20"></span><span class="mr-20"></span>
                    අවිවාහක &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->marital_status==1) X @endif</div>

                    <span class="mr-20"></span><span class="mr-20"></span>
                    ස්ත්‍රි &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->gender==1) X @endif</div>

                    <span class="mr-20"></span><span class="mr-20"></span>
                    පුරුෂ &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->gender==0) X @endif</div>
            </div>

        </div>

        <div class="text-block">
            <div class="text-block-single">
                4.  පදිංචි ලිපිනය :
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{ $customer->address ?? '' }}
                    @endif
                </span>
            </div>

                {!! str_repeat('&nbsp;', 35) !!}<span class="f4">(කුලි පදනම / තමන් සතු )</span>

        </div>
        <div class="text-block">
            <div class="text-block-single">
                5.  ව්‍යාපාරික ලිපිනය :
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{ $customer->details->business_address }}
                    @endif
                </span>
            </div>

                {!! str_repeat('&nbsp;', 35) !!}<span class="f4">(කුලි පදනම / තමන් සතු )</span>

        </div>

        <div class="text-block text-block-single">
            6.  ස්ථිර ලිපිනය :
            <span class="u grow">
                @if($customer != null)&nbsp;&nbsp;
                    {{ $customer->address_nic ?? '' }}
                @endif
            </span>
        </div>

        <div class="text-block   text-block-single">
            7.  දුරකතන අංකය
                <span class="mr-20"></span>නිවස:
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                    {{ $customer->phone ?? '' }}

                    @endif
                </span>

                <span class="mr-20"></span>1. ජංගම:
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                    {{ $customer->phone_mobile ?? '' }}
                    @endif
                </span>

                <span class="mr-20"></span>2. ජංගම:
                <span class="u  grow">
                    @if($customer != null)&nbsp;&nbsp;
                    {{ $customer->details->phone_mobile2 ?? '' }}
                    @endif
                </span>
        </div>

        <div class="text-block text-block-single">
            8.  ප්‍රාග්ධන මුදල අයදුම් කරන්නේ කුමන කටයුත්තක් වෙනුවෙන්ද ? :
            <span class="u grow">
                @if($customer != null)&nbsp;&nbsp;
                    {{$loan->reason}}
                @endif
            </span>
        </div>
        <div class="text-block text-block-single">
            9.  ඔබ නිරතව සිටින ව්‍යාපාරය හෝ ස්වයං රැකියාව කුමක්ද ? :
            <span class="u grow">
                @if($customer != null)&nbsp;&nbsp;
                {{ $customer->business_name ?? '' }}
                @endif
            </span>
        </div>

        <div class="text-block" >
            <div style="display: flex; flex-direction: row; align-items: center; ">
                10.  ඔබට ආදායම ලැබෙන ආකාරය :
                    <span class="mr-20"></span>
                    දෛනිකව &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->income_period==0) X @endif</div>

                    <span class="mr-20"></span>
                    සතිපතා &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->income_period==1) X @endif</div>

                    <span class="mr-20"></span>
                    මාස්පතා &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->income_period==2) X @endif</div>


            </div>
            <div style="display: flex; flex-direction: row; align-items: center; ">
                &nbsp;&nbsp;&nbsp;&nbsp;
                (ආදායම දළ වශයෙන් <span class="u"> &nbsp;&nbsp; {{$customer->monthly_income ?? ''}} {!! str_repeat('&nbsp;', 40) !!} </span> / ලාබය දළ වශයෙන් <span class="u"> &nbsp;&nbsp; {{$customer->details->business_profit ?? ''}}  {!! str_repeat('&nbsp;', 40) !!} </span> )

            </div>
        </div>

        <div class="text-block ">
            <div class="text-block-single" style="display: flex; flex-direction: row; align-items: center; ">
                11.  ඔබගේ මෙම ව්‍යාපාරය / ස්වයං රැකියාවේ කොපමණ කලක සිට නිරත වන්නේද ? :
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{$customer->details->business_duration ?? ''}} years
                    @endif
                </span>
            </div>

            <div class="text-block-single" style="display: flex; flex-direction: row; align-items: center; ">
                &nbsp;&nbsp;&nbsp;&nbsp;
                1. ඉහත දක්වා ඇත්තේ ව්‍යාපාරික ලිපිනයේ ඔබ කොපමණ කලක පටන් සිටින්නේද ?
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{$customer->details->business_duration_on_address ?? ''}} years
                    @endif
                </span>
            </div>

        </div>

        <div class="text-block" >
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>12.  ඔබගේ ව්‍යාපාරය / ස්වයං රැකියාව ලියපදිංචි කර තිබේද ? :</span>

                <span style="display: flex;">
                    (&nbsp;
                    ඔවි &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->business_registration==1) X @endif </div>

                    <span class="mr-10"></span>
                    නැත &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->business_registration==0) X @endif</div>
                    &nbsp;)
                </span>
            </div>

        </div>

        <div class="text-block" >
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>13.  ඔබගේ ව්‍යාපාරය / ස්වයං රැකියාවට හවුල්කරුවන් සිටීද ? :</span>
                <span style="display: flex;">
                    (&nbsp;
                    ඔවි &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->business_partners==1) X @endif </div>

                    <span class="mr-10"></span>
                    නැත &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->business_partners==0) X @endif</div>
                    &nbsp;)
                </span>
            </div>
        </div>

        <div class="text-block" >
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>14.  ඔබ යටතේ සේවය කරනු ලබන සේවක / සේවිකාවන් සිටීද? :</span>
                <span style="display: flex;">
                    (&nbsp;
                    ඔවි &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->business_employees==1) X @endif </div>

                    <span class="mr-10"></span>
                    නැත &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->business_employees==0) X @endif</div>
                    &nbsp;)
                </span>
            </div>
        </div>

        <div class="text-block" >
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>15. ඔබගේ වර්තමාන ව්‍යාපාරය රක්ෂණය කර තිබේද ? :</span>
                <span style="display: flex;">
                    (&nbsp;
                    ඔවි &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->business_insurance==1) X @endif </div>

                    <span class="mr-10"></span>
                    නැත &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->business_insurance==0) X @endif</div>
                    &nbsp;)
                </span>
            </div>
        </div>

        <div class="text-block" >
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>16. ඔබ නමින් ජීවිත රක්ෂණයක් තිබේද ? :</span>
                <span style="display: flex;">
                    (&nbsp;
                    ඔවි &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->insurance==1) X @endif</div>

                    <span class="mr-10"></span>
                    නැත &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->insurance==0) X @endif</div>
                    &nbsp;)
                </span>
            </div>
        </div>

        {{-- <div class="text-block" >
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>16. ඔබ නමින් ජීවිත රක්ෂණයක් තිබේද ? :</span>
                <span style="display: flex;">
                    (&nbsp;
                    ඔවි &nbsp;&nbsp;
                    <div class="tick-box">@if ($loan_count>0) X @endif </div>

                    <span class="mr-10"></span>
                    නැත &nbsp;&nbsp;
                    <div class="tick-box">@if ($loan_count==0) X @endif</div>
                    &nbsp;)
                </span>
            </div>
        </div> --}}

        <div class="text-block" >
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>17. ඔබගේ ව්‍යාපාරය / ස්වයං රැකියාව වෙනුවෙන් පවත්වාගෙන යනු ලබන බැංකු ගිණුම් තිබේද ? :</span>
                <span style="display: flex;">
                    (&nbsp;
                    ඔවි &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->business_bank_accounts==1) X @endif </div>

                    <span class="mr-10"></span>
                    නැත &nbsp;&nbsp;
                    <div class="tick-box">@if($customer->details->business_bank_accounts==0) X @endif</div>
                    &nbsp;)
                </span>
            </div>


            <div class="text-block-single" style="display: flex; flex-direction: row; align-items: center; ">
                &nbsp;&nbsp;&nbsp;&nbsp;
                තිබේ නම් බැංකුව :
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{$customer->details->business_bank_name??''}}
                    @endif
                </span>
                &nbsp;&nbsp;
                ගිණුම් අංකය :
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                    {{$customer->details->business_bank_account_number	??''}}
                    @endif
                </span>
                (
                    @if($customer->details->business_bank_type == 1) <del>ඉතුරුම් ගිණුම්</del>
                    @else ඉතුරුම් ගිණුම්
                    @endif

                    /
                    @if($customer->details->business_bank_type == 0) <del>ජංගම ගිණුම්</del>
                    @else ජංගම ගිණුම්
                    @endif

                )
            </div>

        </div>

        <div class="text-block" >
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>18.  ඔබ සන්තකයේ වාහන / යතුරු පැදී තිබේද ? :</span>
                <span style="display: flex;">
                    (&nbsp;
                    ඔවි &nbsp;&nbsp;
                    <div class="tick-box">@if ($customer->details->vahecle_count==1) X @endif </div>

                    <span class="mr-10"></span>
                    නැත &nbsp;&nbsp;
                    <div class="tick-box">@if ($customer->details->vahecle_count==0) X @endif</div>
                    &nbsp;)
                </span>
            </div>

            <div class="text-block-single" style="display: flex; flex-direction: row; align-items: center; ">
                &nbsp;&nbsp;&nbsp;&nbsp;
                තිබේ නම් කුමන මාදිලියෙද යන වග :
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{$customer->details->vehicle_model ?? ''}}
                    @endif
                </span>
                අංකය :
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{$customer->details->vehicle_number ?? ''}}
                    @endif
                </span>
            </div>

            <div class="text-block-single" style="display: flex; flex-direction: row; align-items: center; ">
                &nbsp;&nbsp;&nbsp;&nbsp;
                ෆිනැන්ස් (කල්බදු) වාරික : {!! str_repeat('&nbsp;', 10) !!}
                (
                    @if($customer->details->business_bank_type == 1) <del>ඇත</del>
                    @else ඇත
                    @endif

                    /
                    @if($customer->details->business_bank_type == 0) <del>නැත</del>
                    @else නැත
                    @endif

                )
                {!! str_repeat('&nbsp;', 10) !!}

                තිබේ නම් වාරික මුදල :
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{$customer->details->vehicle_finance_amount ?? ''}}
                    @endif
                </span>
            </div>
        </div>


        <div class="text-block">
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>19.  මෙයට අමතරව ඔබ වෙත අදායම් ලැබෙන වෙනත් ක්‍රම තිබේද ? :</span>
                <span style="display: flex;">
                    (&nbsp;
                    ඔවි &nbsp;&nbsp;
                    <div class="tick-box">@if ($customer->details->any_other_income==1) X @endif</div>

                    <span class="mr-10"></span>
                    නැත &nbsp;&nbsp;
                    <div class="tick-box">@if ($customer->details->any_other_income==0) X @endif</div>
                    &nbsp;)
                </span>
            </div>
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>20.  තිබේ නම් කුමක්ද යන වග :</span>
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{$customer->details->any_other_income_details??''}}
                    @endif
                </span>
                {!! str_repeat('&nbsp;', 70) !!}
            </div>
        </div>

        <div class="text-block">
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>21.  වෙනත් ආයතන මගින් ඔබ ණය මුදල් ලබාගෙන තිබේ නම් ඒ පිළිබඳ තොරතුරු :</span>
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{$customer->details->other_loan}}
                    @endif
                </span>
            </div>
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                {!! str_repeat('&nbsp;', 11) !!}
                <span>i.  ලබාගත් ආයතනය {!! str_repeat('&nbsp;', 10) !!}:</span>
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{$customer->details->other_loan_company}}
                    @endif
                </span>
            </div>
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                {!! str_repeat('&nbsp;', 10) !!}
                <span>ii.  ලබාගත් මුදල {!! str_repeat('&nbsp;', 20) !!}:</span>
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{$customer->details->other_loan_amount}}
                    @endif
                </span>
            </div>
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                {!! str_repeat('&nbsp;', 9) !!}
                <span>iii.  ගෙවුම් වාරික මුදල {!! str_repeat('&nbsp;', 10) !!}:</span>
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{$customer->details->other_loan_installment}}
                    @endif
                </span>
            </div>
        </div>


        <div class="text-block">
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>22. ඉදිරියේදී ඔබ අමතර ව්‍යාපාරයක් /  ස්වයං රැකියාව සඳහා යොමුවීමට අපේක්ෂා කරන්නේද ? :</span>
                <span style="display: flex;">
                    (&nbsp;
                    ඔවි &nbsp;&nbsp;
                    <div class="tick-box">@if ($customer->details->expetation_for_starter_up==1) X @endif </div>

                    <span class="mr-10"></span>
                    නැත &nbsp;&nbsp;
                    <div class="tick-box">@if ($customer->details->expetation_for_starter_up==0) X @endif</div>
                    &nbsp;)
                </span>
            </div>

            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                {!! str_repeat('&nbsp;', 11) !!}
                <span>i.  ඔවි නම් කුමක්ද යන වග {!! str_repeat('&nbsp;', 10) !!}:</span>
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                    {{$customer->details->expetation_for_starter_up_details}}
                    @endif
                </span>
            </div>

        </div>

        <div class="text-block">
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                <span>23.  අධ්‍යාපනය ලබන වයසින් අඩුම දරුවාගේ නම හා පාසල:</span>
                <span class="u grow">
                    @if($customer != null)&nbsp;&nbsp;
                        {{$customer->details->children_names}}
                    @endif
                </span>
            </div>
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: start; ">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>කරුණු වසන් කිරීමකින් තොරව  නිවැරදිව ඉහත දත්තයන් මා විසින් සපයා ඇති බව ප්‍රකාශ කර පහත අත්සන තබා සිටිමි.</span>
            </div>

        </div>

        <div class="text-block" style="margin-top: 10px;">
            <b style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                &nbsp;&nbsp;
                <span>නම:</span>
                <span class="u grow" style="width:200px;">
                    @if($customer != null)&nbsp;&nbsp;
                        &nbsp;&nbsp;{{$loan->customer->name ?? ''}}&nbsp;&nbsp;
                    @endif
                </span>

                <span class="mr-20"></span>
                <span>අත්සන:</span>
                <span class="u grow" style="width:200px;">
                    @if($customer != null)&nbsp;&nbsp;
                        &nbsp;&nbsp; &nbsp;&nbsp;
                    @endif
                </span>

                <span class="mr-20"></span>
                <span>දිනය:</span>
                <span class="u grow" style="width:200px;">
                    @if($customer != null)&nbsp;&nbsp;
                        &nbsp;&nbsp;{{$loan->date ?? ''}}&nbsp;&nbsp;
                    @endif
                </span>
            </b>
        </div>



        <div class="divider"  style="margin-block: 3px !important;"></div>

        {{-- ==================================================================================== --}}


        <div class="text-block" >
            <b style=" display: flex;  flex-direction: row; align-items: center;justify-content: start; ">
                &nbsp;&nbsp;
                <span>කාර්යාලීය ප්‍රයෝජනය සඳහා පළමු අනුමැතිය</span>
            </b>
        </div>

        <div class="text-block" >
            <div style="">
                &nbsp;
                මෙහි සඳහන් තොරතුරු මා විසින් පරීක්ෂා කර බැලූ අතර එහි සත්‍යතාවය තහවුරු කරගත් බවත් ඔහුට / ඇයට ආයතනයේ
                &nbsp;&nbsp;නීතිරීති වලට අනුකූලව හා යටත්ව
                <span class="u grow" >
                    @if($customer != null)&nbsp;&nbsp;
                        &nbsp;&nbsp;{{$loan->total_amount}}&nbsp;&nbsp;
                    @endif
                </span>
                ක මුදලක් ලබා දීමට අනුමඅත කරමි.
                <span class="u"> (රු.
                    @if($loan->daily_installment != null)
                        {{$loan->daily_installment}}
                    @endif
                    @if($loan->monthly_installment != null)
                        {{$loan->monthly_installment}}
                    @endif
                    @if($loan->weekly_installment != null)
                        {{$loan->weekly_installment}}
                    @endif

                    {!! str_repeat('&nbsp;', 30) !!} )</span>
                <br>&nbsp;&nbsp;බැගින් දින (

                    <span class="u">
                        @if($loan->daily_time_period != null)
                        {{$loan->daily_time_period}}
                        @endif
                        @if($loan->time_period != null)
                            {{$loan->time_period}}
                        @endif
                        {!! str_repeat('&nbsp;', 30) !!}
                    </span> ) එම මුදල් ආපසු නිසිලෙස ආයතනය වෙත අයකර දීමේ වගකීම ද මා විසින් භාරගතමි.
            </div>
        </div>

        <div class="text-block" style="margin-top: 20px; ">
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                &nbsp;&nbsp;
                <span>නිලධාරි මහතාගේ නම :</span>
                <span class="u grow" style="width:200px;">
                    @if($customer != null)&nbsp;&nbsp;
                        &nbsp;&nbsp;{{$officer->name ?? ''}}&nbsp;&nbsp;
                    @endif
                </span>

                <span class="mr-20"></span>
                <span class="mr-20"></span>
                <span>නිලධාරි මහතාගේ :</span>
                <span class="u grow" style="width:200px;">
                    @if($customer != null)&nbsp;&nbsp;
                        &nbsp;&nbsp; &nbsp;&nbsp;
                    @endif
                </span>
            </div>
        </div>


        <div class="text-block" style="border: 1px solid black;padding: 10px;">
            කාර්යාලීය ප්‍රයෝජනය සඳහා පමණි.<br>
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                රු. <span class="u grow">&nbsp;&nbsp;{{$loan->total_amount}}  </span>
                ක මුදලක් <span class="u">  ({!! str_repeat('&nbsp;', 40) !!} </span> )
                ලෙස ගෙවීමට අනුමත කරමි.
            </div>
            <div style=" display: flex;  flex-direction: row; align-items: center;justify-content: space-between; ">
                නම
                <span class="u grow" >
                    @if($customer != null)&nbsp;&nbsp;
                    <span class="u">  {{$customer->name}} </span>
                    @endif
                </span>

                අත්සන <span class="u">   {!! str_repeat('&nbsp;', 60) !!} </span>{!! str_repeat('&nbsp;', 10) !!}
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
            // window.print();
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
