@extends('layouts.admin')
@section('page-title')
{{__('Petty Cash Fund')}}
@endsection
@push('script-page')

<style>
    table tfoot {
        display: table-row-group;
    }

    table td,
    th {
        border: 1px solid;
        text-align: center;
    }

    table td.text-left,
    th.text-left {
        padding-left: 10px;
    }

    .wi-4 {
        width: 4%;
    }

    .wi-5 {
        width: 5%;
    }

    .wi-8 {
        width: 8%;
    }

    .wi-10 {
        width: 10%;
    }

    .wi-20 {
        width: 20%;
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

        var filename = 'Petty Cash Fund';
        if ($('#duration').val() != undefined) {
            var duration = $('#duration').val();
        } else {
            var duration = "";
        }

        if ($('#center').length > 0) {
            var center = $('#center').val();
        } else {
            var center = "";
        }

        $('#print').click(function () {
            $('#printableArea').printThis({
                importCSS: true,
                importStyle: true, //thrown in for extra measure
                // loadCSS: "/app/css/printListTable.css",
                header: '<h3>' + filename + center + duration + '</h3>'
            });
        });

        // $('#export').click(function(){
        //     $("#printableArea").table2excel({
        //         exclude:".noExl",
        //         name:"Worksheet Name",
        //         filename:filename,
        //         fileext:".xlsx",
        //         preserveColors:true
        //     });

        // });
        // $('#report-dataTable').DataTable({
        //     dom: 'lBfrtip',
        //     buttons: [
        //         {
        //             extend: 'excel',
        //             title: filename+center+duration,
        //             footer: true
        //         },  {
        //             extend: 'print',
        //             title:  function() {
        //                     return "<h3>"+filename+center+duration+"</h3>";
        //                 } ,
        //             footer: true
        //         }, {
        //             extend: 'csv',
        //             title: filename+center+duration,
        //             footer: true
        //         }
        //     ]
        // });
    });

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Petty Cash Fund')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Accounting System')}}</div>
        <div class="breadcrumb-item">{{__('Prime Entry Books')}}</div>
        <div class="breadcrumb-item">{{__('Petty Cash Fund')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Petty Cash Fund') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Accounting System')}}</div>
                                <div class="breadcrumb-item">{{__('Prime Entry Books')}}</div>
                                <div class="breadcrumb-item">{{__('Petty Cash Fund')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('petty_cash_fund.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
                                </div>
                                <div class="col-md-2">
                                    <label for="start_month">Start Date</label>
                                    <input class="form-control font-style" name="start_date" type="date"
                                        value="{{ $start_date ?? '' }}" id="start_date" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="end_month">End Date</label>
                                    <input class="form-control font-style" name="end_date" type="date"
                                        value="{{ $end_date ?? '' }}" id="end_date" required>
                                </div>
                                {{-- <div class="col-md-3">
                                            <label for="end_month">End Month</label>
                                            <input class="form-control font-style" name="end_month" type="month" value="{{ $end_month ?? '' }}"
                                id="end_month" readonly>
                            </div> --}}
                            {{-- <div class="col-md-3">
                                            <label for="center_id">Center</label>
                                            @if (isset($repaymentCenters))
                                                @if ($repaymentCenters != '')
                                                    <input type="hidden" id="center" value="{{ __(' Of ').App\Center::centerName($repaymentCenters)[0] }}">
                            @endif
                            @endif
                            {{ Form::select('center_id', $centers, $repaymentCenters ?? '', array('id' => 'center_id','class' => 'form-control font-style', 'required'=>'required')) }}
                        </div> --}}
                </div>
                <div class="row mt-3" style="float: right;">
                    <div class="col-auto">
                        <input class="btn font-style btn-outline-primary" type="submit" value="Search">
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
    </div>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <button type="button" class="btn btn-ash mb-2" id="print">Print</button>
                        <table style="width: 100%" id="printableArea">
                            <thead>
                                <tr>
                                    <th class="wi-8 hi-35"> {{__('Date')}}</th>
                                    <th class="wi-20 hi-35" style="background-color:darksalmon">
                                        {{__('Amount Received')}}</th>
                                    <th class="wi-8 hi-35"> {{__('Date')}}</th>
                                    <th class="wi-8 hi-35"> {{__('Particulars')}}</th>
                                    <th class="wi-8 hi-35" style="background-color: darksalmon"> {{__('Amount Paid')}}
                                    </th>
                                    <th colspan="2" class=" hi-35 wi-10"> {{__('Stationery & Printing')}}</th>
                                    <th colspan="2" class=" hi-35 wi-10"> {{__('Photocopy & Laminating')}}</th>
                                    <th colspan="2" class=" hi-35"> {{__('Printing')}}</th>
                                    <th colspan="2" class=" hi-35"> {{__('Postage')}}</th>
                                    <th colspan="2" class=" hi-35"> {{__('Travelling')}}</th>
                                    <th colspan="2" class=" hi-35"> {{__('Sundry')}}</th>
                                </tr>
                            </thead>
                            {{-- <tbody> --}}
                            {{-- <tr>
                                            <th colspan="14" class="text-left hi-35">Group No</th>
                                        </tr>
                                        <tr>
                                            <td class="hi-45">no</td>
                                            <td class="hi-45 text-left">name</td>
                                            <td class="hi-45">1234567890</td>
                                            <td class="hi-45">0.00</td>
                                            <td class="hi-45">0.00</td>
                                            <td class="hi-45">0.00</td>
                                            <td class="wi-5 hi-45">date</td>
                                            <td class="wi-5 hi-45"></td>
                                            <td class="wi-5 hi-45">date</td>
                                            <td class="wi-5 hi-45"></td>
                                            <td class="wi-5 hi-45">date</td>
                                            <td class="wi-5 hi-45"></td>
                                            <td class="wi-5 hi-45">date</td>
                                            <td class="wi-5 hi-45"></td>
                                        </tr>
                                        <tr>
                                            <th colspan="6" class="text-left hi-35">Group Total</th>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr> --}}
                            {{-- @if($showTable == 'Yes')
                                            <input type="hidden" id="duration" value="{{ __(' - From ').Auth::user()->monthFormat($month) }}">
                            @foreach ($centerInfoLists as $centerInfoList)
                            @foreach ($centerInfoList->groups as $groups)
                            <tr>
                                <th colspan="16" class="hi-35" style="padding-left: 25px; text-align: left;">Group No =
                                    {{ $groups->name }}</th>
                            </tr>
                            @foreach ($groups->groupMembers as $groupMember)
                            <tr>
                                <td>@if(isset($groupMember->customers)){{ Auth::user()->customerNumberFormat($groupMember->customers->id) }}
                                    @endif</td>
                                <td class="text-left">
                                    @if(isset($groupMember->customers)){{ $groupMember->customers->name }}@endif</td>
                                <td class="hi-45">
                                    @if(isset($groupMember->customers)){{ $groupMember->customers->phone }}@endif</td>
                                <td class="hi-45">
                                    @if(isset($groupMember->customers))@if(isset($groupMember->customers->loans))
                                    {{ number_format($groupMember->customers->loans->loan_amount) }} @endif @endif</td>
                                <td class="hi-45">
                                    @if(isset($groupMember->customers))@if(isset($groupMember->customers->loans))
                                    @if($groupMember->customers->loans->time_range == 1)
                                    {{ number_format($groupMember->customers->loans->daily_installment) }}
                                    @elseif($groupMember->customers->loans->time_range == 2)
                                    {{ number_format($groupMember->customers->loans->weekly_installment) }} @else
                                    {{ number_format($groupMember->customers->loans->monthly_installment) }} @endif
                                    @endif @endif</td>
                                <td class="hi-45">
                                    @if(isset($groupMember->customers))@if(isset($groupMember->customers->loans))
                                    {{ number_format($groupMember->customers->loans->current_due_amount) }} @endif
                                    @endif</td>

                                <td class="wi-4 hi-45">@if(isset($groupMember->customers->loans->loanInstallments))
                                    {{ $groupMember->customers->loans->loanInstallments[0]->installment_date ?? '' }}
                                    @endif</td>
                                <td class="wi-4 hi-45">@if(isset($groupMember->customers->loans->loanInstallments))
                                    {{ $groupMember->customers->loans->loanInstallments[0]->paid_amount ?? '' }} @endif
                                </td>

                                <td class="wi-4 hi-45">@if(isset($groupMember->customers->loans->loanInstallments))
                                    {{ $groupMember->customers->loans->loanInstallments[1]->installment_date ?? '' }}
                                    @endif</td>
                                <td class="wi-4 hi-45">@if(isset($groupMember->customers->loans->loanInstallments))
                                    {{ $groupMember->customers->loans->loanInstallments[1]->paid_amount ?? '' }} @endif
                                </td>

                                <td class="wi-4 hi-45">@if(isset($groupMember->customers->loans->loanInstallments))
                                    {{ $groupMember->customers->loans->loanInstallments[2]->installment_date ?? '' }}
                                    @endif</td>
                                <td class="wi-4 hi-45">@if(isset($groupMember->customers->loans->loanInstallments))
                                    {{ $groupMember->customers->loans->loanInstallments[2]->paid_amount ?? '' }} @endif
                                </td>

                                <td class="wi-4 hi-45">@if(isset($groupMember->customers->loans->loanInstallments))
                                    {{ $groupMember->customers->loans->loanInstallments[3]->installment_date ?? '' }}
                                    @endif</td>
                                <td class="wi-4 hi-45">@if(isset($groupMember->customers->loans->loanInstallments))
                                    {{ $groupMember->customers->loans->loanInstallments[3]->paid_amount ?? '' }} @endif
                                </td>

                                <td class="wi-4 hi-45">@if(isset($groupMember->customers->loans->loanInstallments))
                                    {{ $groupMember->customers->loans->loanInstallments[4]->installment_date ?? '' }}
                                    @endif</td>
                                <td class="wi-4 hi-45">@if(isset($groupMember->customers->loans->loanInstallments))
                                    {{ $groupMember->customers->loans->loanInstallments[4]->paid_amount ?? '' }} @endif
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <th colspan="6" class="hi-35" style="padding-left: 25px; text-align: left;">Group Total
                                </th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endforeach
                            @endforeach
                            @else
                            <tr>
                                <td colspan="16">No Data</td>
                            </tr>
                            @endif
                            </tbody> --}}
                            @if($showTable == 'Yes')
                            <tfoot>
                                <tr>
                                    <td colspan="16 hi-35"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-left">Total</td>
                                    @for( $i = 0; $i < 14; $i++ ) <td class="hi-25">
                                        </td>
                                        @endfor
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<script>
    $('body').on('change', '#start_month', function () {
        $('#end_month').val('');
    });

</script>
@endsection
