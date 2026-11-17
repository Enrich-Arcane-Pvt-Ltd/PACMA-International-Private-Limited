@extends('layouts.admin')

@section('page-title')
    {{ __('Bank Deposits History') }}
@endsection

@section('content')
    <style>
        .se-pre-con {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url("{{ asset('assets') }}/loadings/Spinner-1s-200px_blue.gif") center no-repeat #ffffff99;
        }

        .white-space-nowrap {
            white-space: nowrap;
        }

        .overflow-x-scroll {
            overflow-x: scroll;
        }

        #financial_entries_table td {
            vertical-align: middle;
        }

        .cash-in-hand-txt {
            color: #444444;
            font-size: 15px;
        }

        #financial_entries_table_wrapper .dt-buttons {
            position: relative !important;
            margin-bottom: 5px;
        }
    </style>

    <section class="section">
        <div class="se-pre-con"></div>

        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2">{{ __('Bank Deposits History') }}</h4>

                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active">
                                    <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                                </div>
                                <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
                                <div class="breadcrumb-item">{{ __('Bank Deposits History') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('bank-deposits.search') }}">
                            @csrf

                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4 class="h4 mb-0">Filter</h4>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="start_month">Start Date</label>
                                        <input type="date" class="form-control font-style" name="start_date"
                                            id="start_date" value="{{ $startDate ?? '' }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_month">End Date</label>
                                        <input type="date" class="form-control font-style" name="end_date" id="end_date"
                                            value="{{ $endDate ?? '' }}" required>
                                    </div>
                                </div>

                                <div class="row mt-3 mb-3" style="float: right;">
                                    <div class="col-auto">
                                        <a href="{{ route('bank-deposits.index') }}"
                                            class="btn font-style btn-secondary me-1">Cancel</a>
                                        <input class="btn font-style btn-outline-primary" type="submit" value="Search">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{ __('Bank Deposits') }}</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="card-body p-0">
                            <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">

                                @if ($showTable == 'Yes')
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-flush" id="bank_deposits_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th style="text-align: center"> {{ __('Reference No') }}</th>
                                                            <th style="text-align: center"> {{ __('Date') }}</th>
                                                            <th style="text-align: right"> {{ __('Amount') }}
                                                                ({{ $currencySymbol }})</th>
                                                            <th style="text-align: center"> {{ __('Remarks') }}</th>
                                                            <th style="text-align: center"> {{ __('Person') }}</th>
                                                            <th style="text-align: center"> {{ __('Time') }}</th>
                                                            <th style="text-align: center"> {{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($bankDeposits as $bankDeposit)
                                                            <tr>
                                                                <td style="text-align: center">
                                                                    {{ $bankDeposit->reference_no }}
                                                                </td>
                                                                <td style="text-align: center">
                                                                    {{ Auth::user()->dateFormat($bankDeposit->deposited_date) }}
                                                                </td>
                                                                <td style="text-align: right">
                                                                    <strong>
                                                                        {{ number_format($bankDeposit->deposited_amount, 2) }}
                                                                    </strong>
                                                                </td>
                                                                <td style="text-align: center">
                                                                    {{ $bankDeposit->deposited_remarks }}
                                                                </td>
                                                                <td style="text-align: center">
                                                                    {{ $bankDeposit->user->name }}
                                                                </td>
                                                                <td style="text-align: center">
                                                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $bankDeposit->deposited_time)->format('h:i A') }}
                                                                </td>
                                                                <td style="text-align: center">
                                                                    <button type="button"
                                                                        class="btn btn-info view-bank-deposit"
                                                                        value="{{ $bankDeposit->id }}">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div id="empty-message" style="text-align: center; padding: 20px; color: #d2d2d2;">
                                        <img src="{{ asset('assets') }}/img/icons/search_icon.webp" class="mb-3"
                                            style="width: 125px; opacity: .5;" alt="No Data">
                                        <p>No data available in this table</p>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    @include('bank_deposits.bank_deposit_model')

    <script>
        $(document).ready(function() {
            $('.se-pre-con').fadeOut('slow');

            $('#bank_deposits_table').DataTable({
                pageLength: 50,
                buttons: [{
                    extend: 'excel',
                    title: 'Cash Report',
                    footer: true
                }, {
                    extend: 'print',
                    title: 'Cash Report',
                    footer: true
                }, {
                    extend: 'csv',
                    title: 'Cash Report',
                    footer: true
                }]
            });
        });

        $('.view-bank-deposit').on('click', function() {
            var bank_deposit_id = $(this).val();
            console.log(bank_deposit_id);


            $.ajax({
                url: "{{ route('bank-deposits.details') }}",
                type: "GET",
                data: {
                    bank_deposit_id: bank_deposit_id
                },
                beforeSend: function() {
                    $('.se-pre-con').fadeIn('slow');
                },
                success: function(response) {
                    console.log(response);

                    $('#financial_entries_table_tbody').html('');
                    if ($.fn.DataTable.isDataTable('#financial_entries_table')) {
                        $('#financial_entries_table').DataTable().destroy();
                    }

                    var financial_entries = response.records;
                    var opening_balance = response.openingBalance;

                    var debitAmount = 0;
                    var creditAmount = 0;
                    var chequeCreditAmount = 0;
                    var chequeDebitAmount = 0;
                    var cardCreditAmount = 0;
                    var cardDebitAmount = 0;
                    var onlineCreditAmount = 0;
                    var onlineDebitAmount = 0;
                    var cashCreditAmount = 0;
                    var cashDebitAmount = 0;
                    var cashHandAmount = 0;

                    let rows = [];

                    if (opening_balance) {
                        cashDebitAmount = parseFloat(opening_balance.opening_balance);

                        rows += `<tr>
                                <td class="text-center white-space-nowrap">${ moment(opening_balance.date).format('DD/MM/YYYY') }</td>
                                <td class="text-center white-space-nowrap">Opening Balance</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right white-space-nowrap">${ parseFloat(opening_balance.opening_balance).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }</td>
                                <td></td>
                                <td></td>
                            </tr>`;
                    }

                    if (financial_entries.length > 0) {
                        for (let i = 0; i < financial_entries.length; i++) {
                            var debit_amount = 0;
                            if (financial_entries[i].debit_credit == 'debit') {
                                debit_amount = financial_entries[i].amount;
                                debitAmount = parseFloat(debitAmount) + parseFloat(financial_entries[i]
                                    .amount);

                                if (financial_entries[i].payment_method == 'CHEQUE') {
                                    chequeDebitAmount = parseFloat(chequeDebitAmount) + parseFloat(
                                        financial_entries[i].amount);
                                } else if (financial_entries[i].payment_method == 'CARD') {
                                    cardDebitAmount = parseFloat(cardDebitAmount) + parseFloat(
                                        financial_entries[i].amount);
                                } else if (financial_entries[i].payment_method == 'ONLINEPAYMENT') {
                                    onlineDebitAmount = parseFloat(onlineDebitAmount) + parseFloat(
                                        financial_entries[i].amount);
                                } else if (financial_entries[i].payment_method == 'CASH') {
                                    cashDebitAmount = parseFloat(cashDebitAmount) + parseFloat(
                                        financial_entries[i].amount);
                                }
                            }

                            var credit_amount = 0;
                            if (financial_entries[i].debit_credit == 'credit') {
                                credit_amount = financial_entries[i].amount;
                                creditAmount = parseFloat(creditAmount) + parseFloat(financial_entries[
                                    i].amount);

                                if (financial_entries[i].payment_method == 'CHEQUE') {
                                    chequeCreditAmount = parseFloat(chequeCreditAmount) + parseFloat(
                                        financial_entries[i].amount);
                                } else if (financial_entries[i].payment_method == 'CARD') {
                                    cardCreditAmount = parseFloat(cardCreditAmount) + parseFloat(
                                        financial_entries[i].amount);
                                } else if (financial_entries[i].payment_method == 'ONLINEPAYMENT') {
                                    onlineCreditAmount = parseFloat(onlineCreditAmount) + parseFloat(
                                        financial_entries[i].amount);
                                } else if (financial_entries[i].payment_method == 'CASH') {
                                    cashCreditAmount = parseFloat(cashCreditAmount) + parseFloat(
                                        financial_entries[i].amount);
                                }
                            }

                            rows += `<tr>
                                <td class="text-center white-space-nowrap">${ moment(financial_entries[i].date).format('DD/MM/YYYY') }</td>
                                <td class="text-center white-space-nowrap">${ financial_entries[i].type ?? '-' }</td>
                                <td class="text-center white-space-nowrap">${ financial_entries[i].payment_method ?? '-' }</td>
                                <td class="text-center white-space-nowrap">${ financial_entries[i].receipt_number ?? '-' }</td>
                                <td class="text-center white-space-nowrap">${ financial_entries[i].invoice_number ?? '-' }</td>
                                <td>${ financial_entries[i].customer_name ?? '-' }</td>
                                <td>${ financial_entries[i].product_name ?? '-' }</td>
                                <td class="text-right white-space-nowrap">${ debit_amount != 0 ? parseFloat(debit_amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '-' }</td>
                                <td class="text-right white-space-nowrap">${ credit_amount != 0 ?  parseFloat(credit_amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '-' }</td>
                                <td>${ financial_entries[i].remarks ?? '-' }</td>
                            </tr>`;
                        }

                        rows += `<tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="white-space-nowrap">Total Debits/Credits</th>
                                <th class="text-right white-space-nowrap">${ parseFloat(debitAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }</th>
                                <th class="text-right white-space-nowrap">${ parseFloat(creditAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }</th>
                                <th></th>
                            </tr>`;

                        rows += `<tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="white-space-nowrap">Total Cheque Payments</th>
                                <th class="text-right white-space-nowrap">${ parseFloat(chequeDebitAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }</th>
                                <th class="text-right white-space-nowrap">${ parseFloat(chequeCreditAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }</th>
                                <th></th>
                            </tr>`;

                        rows += `<tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="white-space-nowrap">Total Credit Card Payments</th>
                                <th class="text-right white-space-nowrap">${ parseFloat(cardDebitAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }</th>
                                <th class="text-right white-space-nowrap">${ parseFloat(cardCreditAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }</th>
                                <th></th>
                            </tr>`;

                        rows += `<tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="white-space-nowrap">Total Online Payments</th>
                                <th class="text-right white-space-nowrap">${ parseFloat(onlineDebitAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }</th>
                                <th class="text-right white-space-nowrap">${ parseFloat(onlineCreditAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }</th>
                                <th></th>
                            </tr>`;

                        rows += `<tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="white-space-nowrap">Total Cash Payments</th>
                                <th class="text-right white-space-nowrap">${ parseFloat(cashDebitAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }</th>
                                <th class="text-right white-space-nowrap">${ parseFloat(cashCreditAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }</th>
                                <th></th>
                            </tr>`;

                        rows += `<tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="white-space-nowrap cash-in-hand-txt">Total Cash in Hands</th>
                                <th class="text-right white-space-nowrap cash-in-hand-txt">${ parseFloat(cashDebitAmount - cashCreditAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }</th>
                                <th></th>
                                <th></th>
                            </tr>`;

                        $('#financial_entries_table_tbody').html(rows);
                        $('#view_bank_deposit_modal').modal('show');

                        $('#financial_entries_table').DataTable({
                            dom: 'lBfrtip',
                            paging: false,
                            searching: false,
                            info: false,
                            ordering: false,
                            buttons: [{
                                extend: 'excel',
                                title: 'Cash Report',
                                footer: true
                            }, {
                                extend: 'print',
                                title: function() {
                                    return 'Cash Report';
                                },
                                footer: true
                            }, {
                                extend: 'csv',
                                title: 'Cash Report',
                                footer: true
                            }]
                        });

                    } else {
                        Swal.fire({
                            title: "Sorry!",
                            text: "No data found for this bank deposit.",
                            icon: "error",
                        });
                    }
                },
                complete: function() {
                    $('.se-pre-con').fadeOut('slow');
                }
            });
        });
    </script>
@endsection
