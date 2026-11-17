@extends('layouts.admin')
@section('page-title')
{{__('Cash Book')}}
@endsection
@push('script-page')

<style>
    table tfoot {
        display: table-row-group;
    }

    /* @page { size: landscape; }

@media print {
  .header-print {
    display: table-header-group;
  }
} */

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

        var filename = 'Cash Book';
        if ($('#duration').length > 0) {
            var duration = $('#duration').val();
        } else {
            var duration = "";
        }

        if ($('#bank_name').length > 0) {
            var bankName = $('#bank_name').val();
        } else {
            var bankName = '';
        }

        // $('#print').click(function(){
        //     $('#printableArea').printThis({
        //         importCSS: true,
        //         importStyle: true,//thrown in for extra measure
        //         // loadCSS: "/app/css/printListTable.css",
        //         header:'<h3>'+filename+' Of '+duration+'</h3>'
        //     });
        // });

        // $('#export').click(function(){
        //     $("#printableArea").table2excel({
        //         exclude:".noExl",
        //         name:"Worksheet Name",
        //         filename:filename,
        //         fileext:".xlsx",
        //         preserveColors:true
        //     });

        // });
        $('#report-dataTable').DataTable({
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excel',
                title: filename + bankName + duration,
                footer: true
            }, {
                extend: 'print',
                title: function () {
                    return "<h4>" + filename + bankName + duration + "</h4>";
                },
                footer: true
            }, {
                extend: 'csv',
                title: filename + bankName + duration,
                footer: true
            }]
        });
    });

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Cash Book')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Accounting System')}}</div>
        <div class="breadcrumb-item">{{__('Prime Entry Books')}}</div>
        <div class="breadcrumb-item">{{__('Cash Book')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Daily Cash Report') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Accounting System')}}</div>
                                <div class="breadcrumb-item">{{__('Prime Entry Books')}}</div>
                                <div class="breadcrumb-item">{{__('Cash Book')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('cash_book.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
                                </div>
                                <div class="col-md-4">
                                    <label for="bank_id">Bank</label>
                                    @if (isset($bank))
                                    @if ($bank != '')
                                    <input type="hidden" id="bank_name"
                                        value="{{ __(' Of ').App\ChartOfAccount::accountName($bank)[0] }}">
                                    @endif
                                    @endif
                                    {{ Form::select('bank_id', $banks, $bank ?? '', array('id' => 'bank_id','class' => 'form-control font-style', 'data-attribute'=>'value', 'required'=>'required')) }}
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
    <div class="row">
        <div class="col-lg-12">
            <div class="card br-4 cus_show">
                <div class="card-wrap">
                    <div class="card-body">
                        <div class="row">
                            @if($showTable == 'Yes')
                            <div class="col">
                                {{-- <h6>{{__('Total due payment')}} : {{number_format($duetotal,2)}}</h6> --}}
                            </div>
                            <div class="col">
                            </div>
                            <div class="col">
                                <input type="hidden" id="duration"
                                    value="{{ __(' - From ').Auth::user()->dateFormat($start_date).__(' To ').Auth::user()->dateFormat($end_date) }}">
                                <h6>{{__('Duration')}} : {{ $start_date . " TO " .$end_date }} </h6>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="card br-4 cus_show">
                    <div class="card-body">
                        {{-- <button type="button" class="btn btn-ash mb-2" id="print">Print</button> --}}
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="table table-flush" id="report-dataTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="text-align: center"> {{ __('Date') }}</th>
                                        <th style="text-align: center"> {{ __('#') }}</th>
                                        <th style="text-align: center"> {{ __('Ledger Account') }}</th>
                                        <th style="text-align: center"> {{ __('Cheque No.') }}</th>
                                        <th style="text-align: center"> {{ __('Customer No') }}</th>
                                        <th style="text-align: center"> {{ __('Customer') }}</th>
                                        <th style="text-align: center"> {{ __('Narration') }}</th>
                                        <th style="text-align: center"> {{ __('Debit') }}</th>
                                        <th style="text-align: center"> {{ __('Credit') }}</th>
                                    </tr>
                                </thead>
                                @if($showTable == 'Yes')
                                <tbody>
                                    @php
                                    $debitAmount = 0;
                                    $creditAmount = 0;
                                    @endphp
                                    @foreach ($openingBalance as $balance)
                                    <tr>
                                        <td>{{ $start_date }}</td>
                                        <td></td>
                                        <td>Opening Balance</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ number_format($balance->debit_amount, 2) }}</td>
                                        <td>{{ number_format($balance->credit_amount, 2) }}</td>
                                    </tr>
                                    @php
                                    $debitAmount += $balance->debit_amount;
                                    $creditAmount += $balance->credit_amount;
                                    @endphp
                                    @endforeach
                                    @foreach ($transactions as $transaction)
                                    <tr>
                                        {{-- @dd($transaction); --}}
                                        <td>{{ $transaction->date }}</td>
                                        <td>
                                            @if (isset($transaction->advancePayments))
                                            {{ Auth::user()->advanceNumberFormat($transaction->advancePayments->id) }}
                                            @elseif (isset($transaction->sales))
                                            {{ $transaction->sales->sale_id }}
                                            @elseif (isset($transaction->loans))
                                            {{ Auth::user()->loanNumberFormat($transaction->loan_id) }}
                                            @elseif (isset($transaction->pawnings))
                                            {{ Auth::user()->pawningNumberFormat($transaction->pawning_id) }}
                                            @elseif (isset($transaction->customReceipts))
                                            {{ $transaction->customReceipts->receipt_code }}
                                            @elseif (isset($transaction->grn_id))
                                            {{ Auth::user()->GrnNumberFormat($transaction->grn_id) }}
                                            @elseif (isset($transaction->petty_cash_id))
                                            {{ Auth::user()->pettyCashNumberFormat($transaction->petty_cash_id) }}
                                            @elseif (isset($transaction->item_return_id))
                                            {{ Auth::user()->itemReturnNumberFormat($transaction->item_return_id) }}
                                            @elseif (isset($transaction->paymentVouchers))
                                            @if (isset($transaction->paymentVouchers->loans))
                                            {{ Auth::user()->loanNumberFormat($transaction->paymentVouchers->loan_id) }}
                                            @elseif (isset($transaction->paymentVouchers->pawnings))
                                            {{ Auth::user()->pawningNumberFormat($transaction->paymentVouchers->pawning_id) }}
                                            @elseif (isset($transaction->paymentVouchers->advances))
                                            {{ Auth::user()->advanceNumberFormat($transaction->paymentVouchers->advance_id) }}
                                            @else
                                            {{ Auth::user()->paymentVoucherNumberFormat($transaction->payment_voucher_id) }}
                                            @endif
                                            @endif
                                        </td>
                                        <td>{{ $transaction->related_chart_of_account->name ?? '' }}</td>
                                        <td>
                                            @if (isset($transaction->advancePayments))
                                            {{ $transaction->advancePayments->cheque_number ?? 'N/A' }}
                                            @elseif (isset($transaction->sales))
                                            {{ $transaction->sales->cheque_number ?? 'N/A' }}
                                            @elseif (isset($transaction->installments))
                                            {{ $transaction->installments->cheque_number ?? 'N/A' }}
                                            @elseif (isset($transaction->loanInstallments))
                                            {{ $transaction->loanInstallments->cheque_number ?? 'N/A' }}
                                            @elseif (isset($transaction->pawningInstallments))
                                            {{ $transaction->pawningInstallments->cheque_number ?? 'N/A' }}
                                            @elseif (isset($transaction->customReceipts))
                                            {{ $transaction->customReceipts->cheque_number ?? 'N/A' }}
                                            @elseif (isset($transaction->paymentVouchers))
                                            {{ $transaction->paymentVouchers->cheque->cheque_number ?? 'N/A' }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td> @if(isset($transaction->customer_id))
                                            {{ Auth::user()->customerNumberFormat($transaction->customer_id) }} @endif
                                        </td>
                                        <td> {{ $transaction->customer->name ?? '' }} </td>
                                        <td>
                                            @if (isset($transaction->pettyCashes))
                                            {{ $transaction->pettyCashes->description ?? '' }}
                                            @elseif (isset($transaction->generalJournals))
                                            {{ $transaction->generalJournals->narration ?? '' }}
                                            @elseif (isset($transaction->itemReturns))
                                            {{ $transaction->itemReturns->narration ?? '' }}
                                            @elseif (isset($transaction->paymentVouchers))
                                            {{ $transaction->paymentVouchers->description ?? '' }}
                                            @elseif (isset($transaction->customReceipts))
                                            {{ $transaction->customReceipts->description ?? '' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($transaction->debit_credit == 1)
                                            {{-- @dd ($transaction->amount) --}}
                                            {{ number_format($transaction->amount, 2) }}
                                            @php
                                            $debitAmount += $transaction->amount;
                                            @endphp
                                            @endif
                                        </td>
                                        <td>
                                            @if ($transaction->debit_credit == 0)
                                            {{ number_format($transaction->amount, 2) }}
                                            @php
                                            $creditAmount += $transaction->amount;
                                            @endphp
                                            @endif
                                        </td>
                                    </tr>

                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Total</th>
                                        <th>{{ number_format($debitAmount, 2) }}</th>
                                        <th>{{ number_format($creditAmount, 2) }}</th>
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
    </div>
</section>
@endsection
