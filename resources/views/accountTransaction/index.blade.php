@extends('layouts.admin')

@section('page-title')
    {{ __('Account Transactions') }}
@endsection
@section('content')
    <style>
        nav {
            float: right;
        }
    </style>
    <section class="section">
        {{-- <div class="section-header">
        <h1>{{ __('Account Transactions') }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
            <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
            <div class="breadcrumb-item">{{ __('Maintenance') }}</div>
            <div class="breadcrumb-item">{{ __('Account Transactions') }}</div>
        </div>
    </div> --}}
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Account Transactions') }}</h1>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                <div class="breadcrumb-item">{{ __('Maintenance') }}</div>
                                <div class="breadcrumb-item">{{ __('Account Transactions') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    {{-- <form method="post" action="{{ route('account_transaction.search') }}">
                            @csrf --}}
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h4 class="h4 mb-0">Filter</h4>
                            </div>
                            {{-- <div class="col-md-2" id="control_account">
                                    <label for="control_account">Ledger Account</label>
                                    {{Form::select('account_id',$accounts, $chartOfAccount ?? '',array('class'=>'form-control'))}}
                                </div> --}}
                            <div class="col-md-2">
                                <label for="start_date">Start Date</label>
                                <input class="form-control font-style" name="start_date" type="date"
                                    value="{{ $start_date ?? '' }}" id="start_date">
                            </div>
                            <div class="col-md-2">
                                <label for="end_month">End Date</label>
                                <input class="form-control font-style" name="end_date" type="date"
                                    value="{{ $end_date ?? '' }}" id="end_date">
                            </div>
                        </div>
                        <div class="row mt-3" style="float: right;">
                            <div class="col-auto">
                                <input class="btn font-style btn-outline-primary" type="button" value="Search"
                                    id="search">
                            </div>
                        </div>
                    </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{ __('Manage Account Transactions') }}</h4>
                            <div class="card-header-action">
                                @can('create account transaction')
                                    <a href="#" data-size="xl" data-url="{{ route('account_transaction.create') }}"
                                        data-ajax-popup="true" data-title="{{ __('Create New Account Transaction') }}"
                                        class="btn btn-icon icon-left btn-primary commonModal">
                                        <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                        <span class="btn-inner--text"> {{ __('Create') }}</span>
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body p-0">
                            <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            {{ $accounttransactions->onEachSide(5)->links() }}
                                            <table class="table table-flush" id="account_transactions_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="text-align: center"> {{ __('#') }}</th>
                                                        <th style="text-align: center"> {{ __('Resource Id') }}</th>
                                                        <th style="text-align: center"> {{ __('Transaction Date') }}</th>
                                                        <th style="text-align: center"> {{ __('Transaction Type') }}</th>
                                                        <th style="text-align: center"> {{ __('Chart of Account') }}</th>
                                                        <th style="text-align: center">
                                                            {{ __('Transaction Amount') }}<br>({{ $currencySymbol }})</th>
                                                        <th style="text-align: center"> {{ __('Debit/Credit') }}</th>
                                                        <th style="text-align: center">
                                                            {{ __('Customer / Supplier / Employee') }}</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($accounttransactions as $k => $accounttransaction)
                                                        <tr>
                                                            <td>{{ $accounttransaction->id }}</td>
                                                            <td>
                                                                @if (isset($accounttransaction->advancePayments))
                                                                    {{ Auth::user()->advanceNumberFormat($accounttransaction->advancePayments->id) }}
                                                                @elseif (isset($accounttransaction->sales))
                                                                    {{ $accounttransaction->sales->sale_id }}
                                                                @elseif (isset($accounttransaction->loans))
                                                                    {{ Auth::user()->loanNumberFormat($accounttransaction->loan_id) }}
                                                                @elseif (isset($accounttransaction->pawnings))
                                                                    {{ Auth::user()->pawningNumberFormat($accounttransaction->pawning_id) }}
                                                                @elseif (isset($accounttransaction->customReceipts))
                                                                    {{ $accounttransaction->customReceipts->receipt_code }}
                                                                @elseif (isset($accounttransaction->grn_id))
                                                                    {{ Auth::user()->GrnNumberFormat($accounttransaction->grn_id) }}
                                                                @elseif (isset($accounttransaction->petty_cash_id))
                                                                    {{ Auth::user()->pettyCashNumberFormat($accounttransaction->petty_cash_id) }}
                                                                @elseif (isset($accounttransaction->item_return_id))
                                                                    {{ Auth::user()->itemReturnNumberFormat($accounttransaction->item_return_id) }}
                                                                @elseif (isset($accounttransaction->paymentVouchers))
                                                                    @if (isset($accounttransaction->paymentVouchers->loans))
                                                                        {{ Auth::user()->loanNumberFormat($accounttransaction->paymentVouchers->loan_id) }}
                                                                    @elseif (isset($accounttransaction->paymentVouchers->pawnings))
                                                                        {{ Auth::user()->pawningNumberFormat($accounttransaction->paymentVouchers->pawning_id) }}
                                                                    @elseif (isset($accounttransaction->paymentVouchers->advances))
                                                                        {{ Auth::user()->advanceNumberFormat($accounttransaction->paymentVouchers->advance_id) }}
                                                                    @else
                                                                        {{ Auth::user()->paymentVoucherNumberFormat($accounttransaction->payment_voucher_id) }}
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td class="account_transaction_td" style="text-align: center">
                                                                {{ $accounttransaction['date'] }}</td>
                                                            <td class="account_transaction_td">
                                                                {{ $accounttransaction->type->name }}</td>
                                                            <td class="account_transaction_td">
                                                                {{ $accounttransaction->chart_of_account->account_name ?? '' }}
                                                            </td>
                                                            <td class="account_transaction_td" style="text-align: center">
                                                                {{ number_format($accounttransaction['amount'], 2) }}</td>
                                                            <td class="account_transaction_td" style="text-align: center">
                                                                @if ($accounttransaction->debit_credit == 0)
                                                                    Credit
                                                                @elseif($accounttransaction->debit_credit == 1)
                                                                    Debit
                                                                @endif
                                                            </td>
                                                            {{-- <td class="font-style"><button type="button" class="btn btn-outline-primary view" value="{{ $sale->id }}"> {{ $sale->sale_id }} </button></td> --}}
                                                            {{-- <td class="account_transaction_td" style="text-align: center">{{ $accounttransaction->sales->sale_id ?? '' }}</td>
                                                    <td class="account_transaction_td" style="text-align: center">{{ $accounttransaction->invoice->invoice_code ?? '' }}</td> --}}
                                                            <td class="account_transaction_td" style="text-align: left">
                                                                {{ $accounttransaction->customer->name ?? '' }}
                                                                {{ $accounttransaction->vender->name ?? '' }}
                                                                {{ $accounttransaction->employee->name ?? '' }}</td>
                                                            {{-- <td class="account_transaction_td" style="text-align: center">{{ $accounttransaction['branch_id'] }}</td>                                                                                                   --}}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            {{ $accounttransactions->onEachSide(5)->links() }}
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
            // if(localStorage.getItem("account_transaction_no") == null){
            //     var pageNo = 0;
            // } else {
            //     var pageNo = Number(localStorage.getItem("account_transaction_no"));
            // }
            // var account_transactionsTable = $('#account_transactions_table').DataTable({order:[[0,"asc"]]});
            // account_transactionsTable.page(pageNo).draw(false);

            // $('#account_transactions_table').on( 'page.dt', function () {
            //     var pageInfo =  account_transactionsTable.page.info();
            //     pageNo = pageInfo.page;
            //     localStorage.setItem("account_transaction_no", pageNo);
            // });
        });

        $('body').on('click', '#search', function() {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var dates = [start_date, end_date];
            window.location.href = "{{ url('account-transaction/search') }}/" + dates;
        });
    </script>
@endsection
