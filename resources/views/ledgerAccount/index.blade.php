@extends('layouts.admin')

@section('page-title')
    {{ __('Ledger Accounts') }}
@endsection
@section('content')
    <style>
        table tfoot {
            display: table-row-group;
        }
    </style>
    <section class="section">
        {{-- <div class="section-header">
        <h1>{{ __('Ledger Accounts') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Account Summary') }}</div>
        <div class="breadcrumb-item">{{ __('Ledger Accounts') }}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">


                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Ledger Accounts') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                    <div class="breadcrumb-item">{{ __('Account Summary') }}</div>
                                    <div class="breadcrumb-item">{{ __('Ledger Accounts') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('ledger_account.search') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4 class="h4 mb-0">Filter</h4>
                                    </div>
                                    <div class="col-md-2" id="control_account">
                                        <label for="control_account">Ledger Account</label>
                                        {{ Form::select('account_id', $accounts, $chartOfAccount ?? '', ['class' => 'form-control', 'required' => 'required']) }}
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
                                </div>
                                <div class="row m-3" style="float: right;">
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
                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('View Ledger Accounts') }}</h4>
                                <div class="card-header-action">
                                    {{-- @can('create account transaction')
                                <a href="#" data-size="xl" data-url="{{ route('accountTransaction.create') }}"
                                data-ajax-popup="true" data-title="{{ __('Create New Account Transaction') }}"
                                class="btn btn-icon icon-left btn-primary commonModal">
                                <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                <span class="btn-inner--text"> {{ __('Create') }}</span>
                                </a>
                                @endcan --}}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-body p-0">
                                <div id="table-1_wrapper"
                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-flush" id="ledger_accounts_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th style="text-align: center"> {{ __('Date') }}</th>
                                                            <th style="text-align: center"> {{ __('#') }}</th>
                                                            <th style="text-align: center"> {{ __('Ledger Account') }}</th>
                                                            <th style="text-align: center"> {{ __('Cheque No.') }}</th>
                                                            <th style="text-align: center"> {{ __('Customer No') }}</th>
                                                            <th style="text-align: center"> {{ __('Customer') }}</th>
                                                            <th style="text-align: center"> {{ __('Narration') }}</th>
                                                            <th style="text-align: center"> {{ __('Debit') }}
                                                                ({{ $currencySymbol }})</th>
                                                            <th style="text-align: center"> {{ __('Credit') }}
                                                                ({{ $currencySymbol }})</th>
                                                        </tr>
                                                    </thead>

                                                    @if ($showTable == 'Yes')
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
                                                                    <td>{{ number_format($balance->credit_amount, 2) }}
                                                                    </td>
                                                                </tr>
                                                                @php
                                                                    $debitAmount += $balance->debit_amount;
                                                                    $creditAmount += $balance->credit_amount;
                                                                @endphp
                                                            @endforeach
                                                            @foreach ($ledgeraccounts as $transaction)
                                                                <tr>
                                                                    {{-- <td> {{ AUth::user()->cashBookNumberFormat($transaction->id) }}
                                                        </td> --}}
                                                                    <td class="ledger_account_td">{{ $transaction->date }}
                                                                    </td>
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
                                                                    <td class="ledger_account_td">
                                                                        {{ $transaction->related_chart_of_account->name ?? '' }}
                                                                    </td>
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
                                                                    <td> {{ Auth::user()->customerNumberFormat($transaction->customer_id) }}
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
                                                                    <td class="ledger_account_td" style="text-align: right">
                                                                        @if ($transaction->debit_credit == 1)
                                                                            {{ $transaction->amount }}
                                                                            @php
                                                                                $debitAmount += $transaction->amount;
                                                                            @endphp
                                                                        @endif
                                                                    </td>
                                                                    <td class="ledger_account_td" style="text-align: right">
                                                                        @if ($transaction->debit_credit == 0)
                                                                            {{ $transaction->amount }}
                                                                            @php
                                                                                $creditAmount += $transaction->amount;
                                                                            @endphp
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                {{-- @if ($transaction->debit_credit == 1)
                                                                    <tr>
                                                                        <td>{{ $transaction->date }}</td>
                                                    <td>{{ $transaction->transactionTypes->name ?? '' }}</td>
                                                    <td>{{ $transaction->amount }}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    </tr>
                                                    @php
                                                    $debitAmount += $transaction->amount;
                                                    @endphp
                                                    @elseif ($transaction->debit_credit == 0)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $transaction->date }}</td>
                                                        <td>{{ $transaction->transactionTypes->name ?? '' }}</td>
                                                        <td>{{ $transaction->amount }}</td>
                                                    </tr>
                                                    @php
                                                    $creditAmount += $transaction->amount;
                                                    @endphp
                                                    @endif --}}
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
                                                                <th style="text-align: center">Total</th>
                                                                <th style="text-align: right">
                                                                    {{ number_format($debitAmount, 2) }}</th>
                                                                <th style="text-align: right">
                                                                    {{ number_format($creditAmount, 2) }}</th>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                    {{-- </tbody> --}}
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script>
        $(document).ready(function() {
            $(".se-pre-con").fadeOut('slow');

            var filename = 'Ledger Account';
            if ($('#month').length > 0) {
                var duration = $('#month').val();
            } else {
                var duration = "";
            }

            if (localStorage.getItem("ledger_account_no") == null) {
                var pageNo = 0;
            } else {
                var pageNo = Number(localStorage.getItem("ledger_account_no"));
            }
            var ledger_accountsTable = $('#ledger_accounts_table').DataTable({
                order: [
                    [0, "asc"]
                ],
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excel',
                    title: filename + ' Of ' + duration,
                    footer: true
                }, {
                    extend: 'print',
                    title: function() {
                        return "<h3>" + filename + ' Of ' + duration + "</h3>";
                    },
                    footer: true
                }, {
                    extend: 'csv',
                    title: filename + ' Of ' + duration,
                    footer: true
                }]
            });
            ledger_accountsTable.page(pageNo).draw(false);

            $('#ledger_accounts_table').on('page.dt', function() {
                var pageInfo = ledger_accountsTable.page.info();
                pageNo = pageInfo.page;
                localStorage.setItem("ledger_account_no", pageNo);
            });

        });

        $('body').on('click', '.ledger_account_td', function() {
            var ledgerAccountId = $(this).closest('tr').attr("data-id");
            window.location.href = "{{ url('ledgerAccount.index') }}/" + ledgerAccountId;
        });
    </script>

@endsection
