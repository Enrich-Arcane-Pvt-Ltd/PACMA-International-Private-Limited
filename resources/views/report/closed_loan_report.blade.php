@extends('layouts.admin')
@section('page-title')
{{__('Loan Settlement Report')}}
@endsection
@push('script-page')

<style>
    .tbl-scrl {
        display: block;
        max-height: 40rem;
        overflow-y: scroll;
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
        var filename = 'Loan Settlement Report';

        if ($('#loan_type').val() != '') {
            if ($('#loan_type').val() == 1) {
                var loanType = ' Of SME Loan';
            } else if ($('#loan_type').val() == 2) {
                var loanType = ' Of Group Loan';
            } else if ($('#loan_type').val() == 3) {
                var loanType = ' Of Vehicle Loan';
            } else if ($('#loan_type').val() == 4) {
                var loanType = ' Of Land Loan';
            } else if ($('#loan_type').val() == 5) {
                var loanType = ' Of Employment Loan';
            }
        } else {
            var loanType = "";
        }

        $('#report-dataTable').DataTable({
            order: [
                [0, "desc"]
            ],
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excel',
                title: filename + loanType
            }, {
                extend: 'print',
                title: filename + loanType
            }, {
                extend: 'csv',
                title: filename + loanType
            }]
        });
    });

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Loan Settlement Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Loan')}}</div>
        <div class="breadcrumb-item">{{__('Reports')}}</div>
        <div class="breadcrumb-item">{{__('Loan Settlement Report')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Loan Settlement Report') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Loan')}}</div>
                                <div class="breadcrumb-item">{{__('Reports')}}</div>
                                <div class="breadcrumb-item">{{__('Loan Settlement Report')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('closed_loan_report.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
                                </div>
                                <div class="col-md-4">
                                </div>
                                {{-- <div class="col-md-2">
                                            <label for="start_month">Start Date</label>
                                            <input class="form-control font-style" name="start_date" type="date" value="" id="start_date" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="end_month">End Date</label>
                                            <input class="form-control font-style" name="end_date" type="date" value="" id="end_date" required>
                                        </div> --}}
                                <div class="col-md-2">
                                    <label for="loan_type">Loan Type</label>
                                    <select class="form-control font-style" name="loan_type" id="loan_type">
                                        <option @if($loanType=='' ) selected @endif value="">Select Loan Type</option>
                                        <option @if($loanType==1) selected @endif value="1">SME Loan</option>
                                        <option @if($loanType==2) selected @endif value="2">Group Loan</option>
                                        <option @if($loanType==3) selected @endif value="3">Vehicle Loan</option>
                                        <option @if($loanType==4) selected @endif value="4">Land Loan</option>
                                        <option @if($loanType==5) selected @endif value="5">Employment Loan</option>
                                    </select>
                                </div>

                                <div class="col-auto apply-btn">
                                    <input class="btn font-style btn-outline-primary" type="submit" value="Search">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="printableArea">
            {{-- <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-wrap">
                                <div class="card-body">
                                    <div class="row">
                                            @if($showTable == 'Yes')
                                                <div class="col">
                                                    <h6>{{__('Total due payment')}} : {{number_format($duetotal,2)}}
            </h6>
        </div>

        <div class="col">

        </div>

        <div class="col">

        </div>

        <div class="col">
            <h6>{{__('Duration')}} : {{ $start_date . " TO " .$end_date }} </h6>
        </div>
        @endif

    </div>
    </div>
    </div>
    </div>
    </div>
    </div> --}}
    <div class="row">
        <div class="col-12" id="bill-container">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between w-100">
                        <table class="table table-flush tbl-scrl" id="report-dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{__('Loan Id')}}</th>
                                    <th> {{__('Customer No')}}</th>
                                    <th> {{__('Customer Name')}}</th>
                                    <th> {{__('NIC')}}</th>
                                    <th> {{__('Center')}}</th>
                                    <th> {{__('Group')}}</th>
                                    <th> {{__('Loan Type')}}</th>
                                    <th> {{__('Loan Amount')}}</th>
                                    <th> {{__('Time Period')}}</th>
                                    <th> {{__('Collection Period')}}</th>
                                    <th> {{__('Interest Rate')}}</th>
                                    <th> {{__('Interest Amount')}}</th>
                                    <th> {{__('Last Paid Date')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loans as $loan)
                                <tr>
                                    <td class="font-style"> {{ \Auth::user()->loanNumberFormat($loan->id) }} </td>
                                    <td class="font-style">
                                        {{ \Auth::user()->customerNumberFormat($loan->customer->id)}} </td>
                                    <td class="font-style"> {{ $loan->customer->name ?? '' }} </td>
                                    <td class="font-style"> {{ $loan->customer->nic_no ?? '' }} </td>
                                    <td class="font-style"> {{ $loan->center->name ?? '--' }}</td>
                                    <td class="font-style"> {{ $loan->group->name ?? '--' }}</td>
                                    <td class="font-style">
                                        @if ($loan->loan_type == 1)
                                        SME Loan
                                        @elseif ($loan->loan_type == 2)
                                        Group Loan
                                        @elseif ($loan->loan_type == 3)
                                        Vehicle Loan
                                        @elseif ($loan->loan_type == 4)
                                        Land Loan
                                        @elseif ($loan->loan_type == 5)
                                        Job Loan
                                        @endif
                                    </td>
                                    <td class="font-style"> {{ \Auth::user()->priceFormat($loan->loan_amount) }} </td>
                                    <td class="font-style"> {{ $loan->time_period }} {{ __('Months') }} </td>
                                    <td class="font-style">
                                        @if ($loan->time_range == 1)
                                        Daily
                                        @elseif ($loan->time_range == 2)
                                        Weekly
                                        @elseif ($loan->time_range == 3)
                                        Monthly
                                        @endif
                                    </td>
                                    <td class="font-style"> {{ $loan->interest_rate }} %</td>
                                    <td class="font-style"> {{ number_format($loan->interest_amount, 2) }}</td>
                                    @php
                                    $instCount = count($loan->loanInstallments);
                                    if($instCount > 0){
                                    $lastInstNo = $instCount - 1;
                                    $lastInst = $loan->loanInstallments[$lastInstNo]->paid_date;
                                    } else {
                                    $lastInst = '--';
                                    }
                                    @endphp
                                    <td class="font-style"> {{$lastInst}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
    </div>
</section>
@endsection
