@extends('layouts.admin')
@section('page-title')
{{__('Loan Disbursement Report')}}
@endsection
@push('script-page')

<style>
    .detail-table tfoot {
        display: table-row-group;
    }

    .detail-table td,
    th {
        border: 1px solid;
        text-align: center;
    }

    .detail-table td.text-left,
    th.text-left {
        padding-left: 10px;
    }

    .text-left {
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .wi-4 {
        width: 4%;
    }

    .wi-5 {
        width: 5%;
    }

    .wi-6 {
        width: 6%;
    }

    .wi-10 {
        width: 10%;
    }

    .wi-15 {
        width: 15%;
    }

    .wi-20 {
        width: 20%;
    }

    .wi-25 {
        width: 25%;
    }

    .hi-25 {
        height: 25px;
    }

    .hi-35 {
        height: 35px;
    }

    .hi-45 {
        height: 45px;
    }

    .btn-ash {
        width: 6rem;
        border-radius: 12px;
        font-size: medium;
        color: #828282;
    }

    @page {
        size: landscape;
    }

    @media print {
        .header-print {
            display: table-header-group;
        }
    }

</style>

<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    var filename = $('#filename').val();

    function saveAsPDF() {
        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.3,
            filename: filename,
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 4,
                dpi: 72,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'A2'
            }
        };
        html2pdf().set(opt).from(element).save();

    }


    $(document).ready(function () {

        // var filename = 'Repayment Sheet';
        // if($('#duration').val() != undefined){
        //     var duration = $('#duration').val();
        // } else{
        //     var duration = "";
        // }

        // if($('#center').length > 0 ){
        //     var center = $('#center').val();
        // } else{
        //     var center = "";
        // }

        $('#print').click(function () {
            $('#printableArea').printThis({
                importCSS: true,
                importStyle: true, //thrown in for extra measure
                // header:'<h3 class="mt-3 text-center" style="margin-bottom:-0.1px;">රිච්වින් ඉන්වෙස්ට්මන්ට් ඇන්ඩ් ක්‍රෙඩිට් ප්‍රයිවට් ලිමිටඩ්<br>'+
                //         '<span style="font-size: 22px;">ණය යෝජනා අනුමත අත්හිටුවීම් සහ නිකුත් කිරීමේ පත්‍රය</span></h3>'
            });
        });
    });

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Loan Disbursement Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Loan')}}</div>
        <div class="breadcrumb-item">{{__('Reports')}}</div>
        <div class="breadcrumb-item">{{__('Loan Disbursement Report')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Loan Disbursement Report') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Loan')}}</div>
                                <div class="breadcrumb-item">{{__('Reports')}}</div>
                                <div class="breadcrumb-item">{{__('Loan Disbursement Report')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('loan_disbursement_report.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
                                </div>
                                <div class="col-md-3">
                                    <label for="start_date">Start Date</label>
                                    <input class="form-control font-style" name="start_date" type="date"
                                        value="{{ $startDate ?? '' }}" id="start_date" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date">End Date</label>
                                    <input class="form-control font-style" name="end_date" type="date"
                                        value="{{ $endDate ?? '' }}" id="end_date" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="center_id">Center</label>
                                    {{ Form::select('center_id', $allCenters, $centerId ?? '', array('id' => 'center_id','class' => 'form-control font-style')) }}
                                </div>
                            </div>
                            <div class="row mt-3 mb-3" style="float: right;">
                                <div class="col-auto">
                                    <input class="btn font-style btn-outline-primary" type="submit" value="Search">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if($showTable == 'Yes')
        @if(count($loans) > 0 || count($centerLoans) > 0)
        <button type="button" class="btn btn-ash mb-2" id="print">Print</button>
        <div id="printableArea">
            @foreach ( $centerLoans as $centerLoan )
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="mt-3 text-center" style="margin-bottom:-0.1px;">රිච්වින් ඉන්වෙස්ට්මන්ට්
                                        ඇන්ඩ් ක්‍රෙඩිට් ප්‍රයිවට් ලිමිටඩ්<br>
                                        <span style="font-size: 22px;">ණය යෝජනා අනුමත අත්හිටුවීම් සහ නිකුත් කිරීමේ
                                            පත්‍රය</span>
                                    </h3>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td class="wi-15">ශාඛාව</td>
                                            <td class="text-center">:</td>
                                            <td style="border-bottom: 1.3px dotted; width: 15rem; padding-left:10px;">
                                                {{ $centerLoan['center']->branch->name ?? '--' }} </td>
                                            <td class="wi-20"></td>
                                            <td class="text-right">ණය නිකුත් කළ දිනය</td>
                                            <td>:</td>
                                            <td style="border-bottom: 1.3px dotted; width: 15rem; padding-left:10px;">
                                                {{ $centerLoan['date'] ?? '' }} </td>
                                        </tr>
                                        <tr>
                                            <td class="wi-15">මධ්‍යස්ථාන අංක</td>
                                            <td class="text-center">:</td>
                                            <td style="border-bottom: 1.3px dotted; width: 15rem; padding-left:10px;">
                                                {{ Auth::user()->centerNumberFormat($centerLoan['center_id']) }} </td>
                                            <td class="wi-20"></td>
                                            <td class="text-right">දිනය</td>
                                            <td>:</td>
                                            <td style="border-bottom: 1.3px dotted; width: 15rem; padding-left:10px;">
                                                {{ \Carbon\Carbon::now()->format('Y-m-d') }} </td>
                                        </tr>
                                        <tr>
                                            <td class="wi-15">මධ්‍යස්ථානය</td>
                                            <td class="text-center">:</td>
                                            <td style="border-bottom: 1.3px dotted; width: 15rem; padding-left:10px;">
                                                {{ $centerLoan['center_name'] ?? '--' }} </td>
                                            <td class="wi-20"></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <table class="detail-table" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th class="hi-35 wi-6">ණය අංකය</th>
                                                <th class="hi-35 wi-6">සාමාජික අංකය</th>
                                                <th class="hi-35 wi-6">කණ්ඩායම් අංකය</th>
                                                <th class="hi-35 wi-20">සාමාජික / සාමාජිකාවගේ නම</th>
                                                <th class="hi-35 wi-6">ජාතික හැදුනුම්පත් අංකය</th>
                                                <th class="hi-35 wi-4">කීවෙනි ණයද</th>
                                                <th class="hi-35 wi-5">ණය පොළි අනුපාතය %</th>
                                                <th class="hi-35 wi-4">ණය කාල සීමාව (මාස)</th>
                                                <th class="hi-35 wi-10">ණය මුදල 100%</th>
                                                <th class="hi-35 wi-15">රිච්වින් ඉන්වෙස්ට්මන්ට් වෙතින් ලබා ගන්නාා ලද ණය
                                                    මුදල සහා එයට අදාල පොළියද සමග ගෙවා නිම කරන බවට පොරොන්දු වෙමින් අත්සන්
                                                    තබමි.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                            $totalLoanAmount = 0;
                                            $loanRaw = count($centerLoan['loans']);
                                            @endphp
                                            @foreach ($centerLoan['loans'] as $loan )
                                            <tr>
                                                <td class="hi-25"> {{ Auth::user()->loanNumberFormat($loan[0]->id) }}
                                                </td>
                                                <td class="hi-25">
                                                    {{ Auth::user()->customerNumberFormat($loan[0]->customer_id) }}
                                                </td>
                                                <td class="hi-25"> {{ $loan[0]->group->name ?? '' }} </td>
                                                <td class="hi-25"> @if($loan[0]->customer->title == 0)Mr.
                                                    @elseif($loan[0]->customer->title == 1)Mrs. @else Miss.
                                                    @endif{{$loan[0]->customer->name ?? '--'}}</td>
                                                <td class="hi-25"> {{$loan[0]->customer->nic_no ?? '--'}} </td>
                                                <td class="hi-25"> {{ $loan['count'] }} </td>
                                                <td class="hi-25"> {{ $loan[0]->interest_rate }} </td>
                                                <td class="hi-25"> {{ $loan[0]->time_period }} </td>
                                                <td class="hi-25"> {{ number_format($loan[0]->loan_amount, 2) }} </td>
                                                <td class="hi-25"> </td>
                                            </tr>

                                            @php
                                            $totalLoanAmount += $loan[0]->loan_amount;
                                            @endphp
                                            @endforeach

                                            @for ($i = 0; $i < 7 - $loanRaw; $i++) <tr>
                                                @for ($j = 0; $j < 10; $j++) <td class="hi-25">
                                                    </td>
                                                    @endfor
                                                    </tr>
                                                    @endfor
                                                    <tr>
                                                        <td colspan="8" class="text-right hi-25"
                                                            style="padding-right: 1rem; font-weight: bold;">මුළු එකතුව
                                                        </td>
                                                        <td class="hi-25" style="font-weight: bold;">
                                                            {{ number_format($totalLoanAmount, 2) }} </td>
                                                        <td class="hi-25"></td>
                                                    </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td class="wi-25 text-center">
                                                .....................................................................
                                            </td>
                                            <td class="wi-25 text-center">
                                                .....................................................................
                                            </td>
                                            <td class="wi-25 text-center">
                                                .....................................................................
                                            </td>
                                            <td class="wi-25 text-center">
                                                .....................................................................
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="wi-25 text-center">මධ්‍යස්ථාන කළමනාකරු</td>
                                            <td class="wi-25 text-center">ක්ෂේත්‍ර කළමනාකරු</td>
                                            <td class="wi-25 text-center">ශාඛා කළමනාකරු</td>
                                            <td class="wi-25 text-center">අධ්‍යක්ෂක / ජ්‍යේෂ්ඨ කළමනාකරු / ප්‍රා.කළමනාකරු
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="hi-35 wi-25" style="padding-left:10px; vertical-align: bottom;">
                                                නම : ...........................................................</td>
                                            <td class="hi-35 wi-25" style="padding-left:10px; vertical-align: bottom;">
                                                නම : ...........................................................</td>
                                            <td class="hi-35 wi-25" style="padding-left:10px; vertical-align: bottom;">
                                                නම : ...........................................................</td>
                                            <td class="hi-35 wi-25" style="padding-left:10px; vertical-align: bottom;">
                                                නම : ...........................................................</td>
                                        </tr>
                                        <tr>
                                            <td class="wi-25" style="padding-left:10px;">දිනය :
                                                ...........................................................</td>
                                            <td class="wi-25" style="padding-left:10px;">දිනය :
                                                ...........................................................</td>
                                            <td class="wi-25" style="padding-left:10px;">දිනය :
                                                ...........................................................</td>
                                            <td class="wi-25" style="padding-left:10px;">දිනය :
                                                ...........................................................</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="display" id="break_page" style='page-break-after:always'></div>
            @endforeach
            @foreach ( $loans as $loan )
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="mt-3 text-center" style="margin-bottom:-0.1px;">රිච්වින් ඉන්වෙස්ට්මන්ට්
                                        ඇන්ඩ් ක්‍රෙඩිට් ප්‍රයිවට් ලිමිටඩ්<br>
                                        <span style="font-size: 22px;">ණය යෝජනා අනුමත අත්හිටුවීම් සහ නිකුත් කිරීමේ
                                            පත්‍රය</span>
                                    </h3>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td class="wi-15">ශාඛාව</td>
                                            <td class="text-center">:</td>
                                            <td style="border-bottom: 1.3px dotted; width: 15rem; padding-left:10px;">
                                                {{ $loan->branch->name ?? '--' }} </td>
                                            <td class="wi-20"></td>
                                            <td class="text-right">ණය නිකුත් කළ දිනය</td>
                                            <td>:</td>
                                            <td style="border-bottom: 1.3px dotted; width: 15rem; padding-left:10px;">
                                                {{ $loan->date ?? '' }} </td>
                                        </tr>
                                        <tr>
                                            <td class="wi-15">මධ්‍යස්ථාන අංක</td>
                                            <td class="text-center">:</td>
                                            <td style="border-bottom: 1.3px dotted; width: 15rem; padding-left:10px;">
                                                @if(isset($loan->center))
                                                {{ Auth::user()->centerNumberFormat($loan->center_id) }} @else -- @endif
                                            </td>
                                            <td class="wi-20"></td>
                                            <td class="text-right">දිනය</td>
                                            <td>:</td>
                                            <td style="border-bottom: 1.3px dotted; width: 15rem; padding-left:10px;">
                                                {{ \Carbon\Carbon::now()->format('Y-m-d') }} </td>
                                        </tr>
                                        <tr>
                                            <td class="wi-15">මධ්‍යස්ථානය</td>
                                            <td class="text-center">:</td>
                                            <td style="border-bottom: 1.3px dotted; width: 15rem; padding-left:10px;">
                                                {{ $loan->center->name ?? '--' }} </td>
                                            <td class="wi-20"></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <table class="detail-table" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th class="hi-35 wi-6">ණය අංකය</th>
                                                <th class="hi-35 wi-6">සාමාජික අංකය</th>
                                                <th class="hi-35 wi-6">කණ්ඩායම් අංකය</th>
                                                <th class="hi-35 wi-20">සාමාජික / සාමාජිකාවගේ නම</th>
                                                <th class="hi-35 wi-6">ජාතික හැදුනුම්පත් අංකය</th>
                                                <th class="hi-35 wi-4">කීවෙනි ණයද</th>
                                                <th class="hi-35 wi-5">ණය පොළි අනුපාතය %</th>
                                                <th class="hi-35 wi-4">ණය කාල සීමාව (මාස)</th>
                                                <th class="hi-35 wi-10">ණය මුදල 100%</th>
                                                <th class="hi-35 wi-15">රිච්වින් ඉන්වෙස්ට්මන්ට් වෙතින් ලබා ගන්නාා ලද ණය
                                                    මුදල සහා එයට අදාල පොළියද සමග ගෙවා නිම කරන බවට පොරොන්දු වෙමින් අත්සන්
                                                    තබමි.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="hi-25"> {{ Auth::user()->loanNumberFormat($loan->id) }} </td>
                                                <td class="hi-25">
                                                    {{ Auth::user()->customerNumberFormat($loan->customer_id) }} </td>
                                                <td class="hi-25"> {{ $loan->group->name ?? '' }} </td>
                                                <td class="hi-25"> @if($loan->customer->title == 0)Mr.
                                                    @elseif($loan->customer->title == 1)Mrs. @else Miss.
                                                    @endif{{$loan->customer->name ?? '--'}}</td>
                                                <td class="hi-25"> {{$loan->customer->nic_no ?? '--'}} </td>
                                                <td class="hi-25">
                                                    @foreach ( $counts as $count )
                                                    @if($count['customer_id'] == $loan->customer_id)
                                                    {{ $count['count'] }}
                                                    @endif
                                                    @endforeach
                                                </td>
                                                <td class="hi-25"> {{ $loan->interest_rate }} </td>
                                                <td class="hi-25"> {{ $loan->time_period }} </td>
                                                <td class="hi-25"> {{ number_format($loan->loan_amount, 2) }} </td>
                                                <td class="hi-25"> </td>
                                            </tr>
                                            @for ($i = 0; $i < 8; $i++) <tr>
                                                @for ($j = 0; $j < 10; $j++) <td class="hi-25">
                                                    </td>
                                                    @endfor
                                                    </tr>
                                                    @endfor
                                                    <tr>
                                                        <td colspan="8" class="text-right hi-25"
                                                            style="padding-right: 1rem; font-weight: bold;">මුළු එකතුව
                                                        </td>
                                                        <td class="hi-25" style="font-weight: bold;">
                                                            {{ number_format($loan->loan_amount, 2) }} </td>
                                                        <td class="hi-25"></td>
                                                    </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td class="wi-25 text-center">
                                                .....................................................................
                                            </td>
                                            <td class="wi-25 text-center">
                                                .....................................................................
                                            </td>
                                            <td class="wi-25 text-center">
                                                .....................................................................
                                            </td>
                                            <td class="wi-25 text-center">
                                                .....................................................................
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="wi-25 text-center">මධ්‍යස්ථාන කළමනාකරු</td>
                                            <td class="wi-25 text-center">ක්ෂේත්‍ර කළමනාකරු</td>
                                            <td class="wi-25 text-center">ශාඛා කළමනාකරු</td>
                                            <td class="wi-25 text-center">අධ්‍යක්ෂක / ජ්‍යේෂ්ඨ කළමනාකරු / ප්‍රා.කළමනාකරු
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="hi-35 wi-25" style="padding-left:10px; vertical-align: bottom;">
                                                නම : ...........................................................</td>
                                            <td class="hi-35 wi-25" style="padding-left:10px; vertical-align: bottom;">
                                                නම : ...........................................................</td>
                                            <td class="hi-35 wi-25" style="padding-left:10px; vertical-align: bottom;">
                                                නම : ...........................................................</td>
                                            <td class="hi-35 wi-25" style="padding-left:10px; vertical-align: bottom;">
                                                නම : ...........................................................</td>
                                        </tr>
                                        <tr>
                                            <td class="wi-25" style="padding-left:10px;">දිනය :
                                                ...........................................................</td>
                                            <td class="wi-25" style="padding-left:10px;">දිනය :
                                                ...........................................................</td>
                                            <td class="wi-25" style="padding-left:10px;">දිනය :
                                                ...........................................................</td>
                                            <td class="wi-25" style="padding-left:10px;">දිනය :
                                                ...........................................................</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="display" id="break_page" style='page-break-after:always'></div>
            @endforeach
        </div>
        @else
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <p>No Data</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>
</section>
@endsection
