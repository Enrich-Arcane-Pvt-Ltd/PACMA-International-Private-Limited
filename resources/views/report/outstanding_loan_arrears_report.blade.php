@extends('layouts.admin')
@section('page-title')
{{__('Outstanding Loan Arrears Report')}}
@endsection
@push('script-page')
@php
use App\Http\Controllers\ReportController;
$reportController = new ReportController();
@endphp

<style>
    table tfoot {
        display: table-row-group;
    }

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
        var filename = 'Outstanding Loan Arrears Report';
        if ($('#duration').val() != undefined) {
            var duration = $('#duration').val();
        } else {
            var duration = "";
        }

        if ($('#category').length > 0) {
            var category = $('#category').val();
        } else {
            var category = "";
        }

        $('#report-dataTable').DataTable({
            dom: 'lBfrtip',
            buttons: [{
                    extend: 'excel',
                    title: filename + category + duration,
                    footer: true
                },

                {
                    extend: 'csv',
                    title: filename + category + duration,
                    footer: true
                }
            ]
        });
    });

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Outstanding Hire Sales Arrears Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Cash Sales & Hire Sales')}}</div>
        <div class="breadcrumb-item">{{__('Reports')}}</div>
        <div class="breadcrumb-item">{{__('Outstanding Hire Sales Report')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Outstanding Hire Sales Arrears Report') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Cash Sales & Hire Sales')}}</div>
                                <div class="breadcrumb-item">{{__('Reports')}}</div>
                                <div class="breadcrumb-item">{{__('Outstanding Hire Sales Report')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('outstanding_loan_arrears_report.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
                                </div>

                                <div class="col-md-2">
                                    <label for="end_date">Date</label>
                                    <input class="form-control font-style" name="end_date" type="date"
                                        value="{{ $end_date ?? '' }}" id="end_date" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="loan_type">Category</label>
                                    @if (isset($productCategory))
                                    @if ($productCategory != '')
                                    <input type="hidden" id="category"
                                        value="{{ __(' Of ').App\ProductCategory::categoryName($productCategory)[0] }}">
                                    @endif
                                    @endif
                                    {{ Form::select('category_id', $productCategories, $productCategory ?? '', array('class' => 'form-control font-style')) }}
                                </div>
                                <div class="col-md-2">
                                    <label for="oficcer_id">Oficcer</label>
                                    {{ Form::select('oficcer_id', $oficcers, $loanOficcer ?? '', array('id' => 'oficcer_id','class' => 'form-control font-style')) }}
                                </div>
                                <div class="col-md-2">
                                    <label for="introducer_id">Salesman</label>
                                    {{ Form::select('introducer_id', $introducers, $loanIntroducer ?? '', array('id' => 'introducer_id','class' => 'form-control font-style')) }}
                                </div>

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

        <div id="printableArea">
            <div class="row">
                <div class="col-12" id="bill-container">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100" style="overflow-x: auto;">

                                <table class="table table-flush tbl-scrl" id="report-dataTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th> {{__('Sale No')}}</th>
                                            <th> {{__('Customer No')}}</th>
                                            <th> {{__('Customer Name')}}</th>
                                            <th> {{__('Customer Phone')}}</th>
                                            <th> {{__('NIC')}}</th>
                                            <th class="text-right"> {{__('Loan Amount')}}</th>
                                            <th class="text-right"> {{__('Rental')}}</th>
                                            <th class="text-center"> {{__('Term')}}</th>
                                            <th class="text-right"> {{__('Arrears Amount')}}</th>
                                            <th class="text-center"> {{__('Arrears Months')}}</th>
                                            <th class="text-right"> {{__('Last Payment')}}</th>
                                            <th> {{__('Date')}}</th>
                                            <th> {{__('Total Receivable')}}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @if($showTable == 'Yes')
                                        <input type="hidden" id="duration"
                                            value="{{ __(' - For ').Auth::user()->dateFormat($end_date) }}">
                                        @php
                                        $totalOutstandAmount = 0;
                                        $totalinterstAmount = 0;
                                        $totalDueAmount = 0;
                                        $totalDuePaid = 0;
                                        $totalDue = 0;
                                        @endphp

                                        @foreach($loanInfoLists as $loanInfoList)
                                        @php
                                        $paidAmount = 0;
                                        $outstandingBalance = 0;
                                        if(isset($loanInfoList->installments)){
                                        $paid = $loanInfoList->installments->sum('paid_amount');
                                        $fine = $loanInfoList->installments->sum('fine');
                                        $paidAmount = $paid - $fine;
                                        $outstandingBalance = $loanInfoList->total_due_amount - $paidAmount;
                                        }

                                        $arrearsMonths =
                                        $reportController->calculateArrearsMonths($loanInfoList->lastInstallment->paid_date
                                        ?? null);
                                        $arrearsAmount =
                                        $reportController->calculateArrearsAmount($loanInfoList->lastInstallment->paid_date
                                        ?? null, $loanInfoList->monthly_installment ?? 0);

                                        $arrearsMonthsDisplay = is_numeric($arrearsMonths) ? $arrearsMonths : '--';
                                        $arrearsAmountDisplay = is_numeric($arrearsAmount) ?
                                        number_format($arrearsAmount, 2) : '--';
                                        $outstandingBalanceDisplay = is_numeric($outstandingBalance) ?
                                        number_format($outstandingBalance, 2) : '--';
                                        @endphp

                                        <tr>
                                            <td>{{ $loanInfoList->sale_id }}</td>
                                            <td>
                                                @if(isset($loanInfoList->customer))
                                                {{ Auth::user()->customerNumberFormat($loanInfoList->customer->id) }}
                                                @else
                                                {{ "--" }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($loanInfoList->customer->title == 0)
                                                {{ "Mr." }}
                                                @elseif($loanInfoList->customer->title == 1)
                                                {{ "Mrs." }}
                                                @else
                                                {{ "Miss." }}
                                                @endif
                                                {{ $loanInfoList->customer->name ?? '--' }}
                                            </td>
                                            <td>{{ $loanInfoList->customer->phone ?? '--' }}</td>
                                            <td>{{ $loanInfoList->customer->nic_no ?? '--' }}</td>
                                            <td class="text-right">{{ number_format($loanInfoList->net_amount, 2) }}
                                            </td> {{-- Loan Amount --}}
                                            <td class="text-right">
                                                {{ number_format($loanInfoList->monthly_installment, 2) ?? '--' }}</td>
                                            {{-- Rental --}}
                                            <td class="text-center">{{ $loanInfoList->installment }}</td> {{-- Term --}}
                                            <td class="text-right">
                                                {{ number_format($loanInfoList->arrears_amount, 2) ?? '--' }}</td>
                                            {{-- Arrears Amount --}}
                                            <td class="text-center">{{ $loanInfoList->arrears_months ?? '--' }}</td>
                                            {{-- Arrears Months --}}
                                            <td class="text-right">
                                                @if(isset($loanInfoList->lastInstallment)) {{-- Last Payment --}}
                                                {{ number_format($loanInfoList->lastInstallment->paid_amount, 2) }}
                                                @else
                                                --
                                                @endif
                                            </td>
                                            <td style="white-space: nowrap;">
                                                {{ $loanInfoList->lastInstallment->paid_date ?? '--' }}</td>
                                            <td>{{ $outstandingBalanceDisplay }}</td>

                                            @php
                                            $totalOutstandAmount += $loanInfoList->outstanding_amount;
                                            $totalinterstAmount += $loanInfoList->interest_amount;
                                            $totalDueAmount += $loanInfoList->total_due_amount;
                                            $totalDuePaid += $paidAmount;
                                            $totalDue += $outstandingBalance;
                                            @endphp
                                        </tr>
                                        @endforeach
                                        @endif
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
<section>
    <div class="modal modelView" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="width: auto !important; max-width: 1000px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Instalment Summery</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class=" col-md-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>Installment Amount</th>
                                    <th>Amount Tobe Paid</th>
                                    <th>Paid Amount</th>
                                    <th>Due Date</th>
                                    <th>Stauts</th>
                                </tr>
                            </thead>
                            <tbody id="installmetTableBody">

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
