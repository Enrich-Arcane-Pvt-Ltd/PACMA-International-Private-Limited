@extends('layouts.admin')
@section('page-title')
    {{ __('Cash Book') }}
@endsection
@push('script-page')
    <style>
        table tfoot {
            display: table-row-group;
        }

        .dt-buttons {
            position: relative !important;
            margin-bottom: 10px;
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

        $(document).ready(function() {
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
            $('#report-dataTable').DataTable({
                dom: 'lBfrtip',
                paging: false,
                searching: false,
                info: false,
                ordering: false,
                buttons: [{
                    extend: 'excel',
                    title: filename + bankName + duration,
                    footer: true
                }, {
                    extend: 'print',
                    title: function() {
                        return filename + bankName + duration;
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
    </style>

    <section class="section">
        <div class="se-pre-con"></div>

        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Cash Book') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                    <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
                                    <div class="breadcrumb-item">{{ __('Cash Book') }}</div>
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
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-flush" id="report-dataTable">
                                        <thead class="thead-light">
                                            <tr>

                                                <th style="text-align: center; white-space: nowrap;"> {{ __('Date') }}
                                                </th>
                                                <th style="text-align: center; white-space: nowrap;"> {{ __('Type') }}
                                                </th>
                                                <th style="text-align: center; white-space: nowrap;">
                                                    {{ __('Payment Method') }}</th>
                                                <th style="text-align: center; white-space: nowrap;">
                                                    {{ __('Receipt No.') }}</th>
                                                <th style="text-align: center; white-space: nowrap;">
                                                    {{ __('Account No') }}</th>
                                                <th style="text-align: center; white-space: nowrap;"> {{ __('Customer') }}
                                                </th>
                                                <th style="text-align: center; white-space: nowrap;">
                                                    {{ __('Product Code') }}</th>
                                                <th style="text-align: center; white-space: nowrap;"> {{ __('Debit') }}
                                                    ({{ $currencySymbol }})
                                                </th>
                                                <th style="text-align: center; white-space: nowrap;"> {{ __('Credit') }}
                                                    ({{ $currencySymbol }})
                                                </th>
                                                <th style="text-align: center; width: 15% !important; ">
                                                    {{ __('Remark') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        @if ($showTable == 'Yes')
                                            <tbody>
                                                @php
                                                    $debitAmount = 0;
                                                    $creditAmount = 0;
                                                    $chequeCreditAmount = 0;
                                                    $chequeDebitAmount = 0;
                                                    $cardCreditAmount = 0;
                                                    $cardDebitAmount = 0;
                                                    $onlineCreditAmount = 0;
                                                    $onlineDebitAmount = 0;
                                                    $cashCreditAmount = 0;
                                                    $cashDebitAmount = 0;
                                                    $cashHandAmount = 0;
                                                @endphp

                                                @php
                                                    $today = \Carbon\Carbon::today();
                                                @endphp

                                                @if ($openingBalance)
                                                    <tr>
                                                        <td style="white-space: nowrap;">
                                                            {{ Auth::user()->dateFormat($today) }}
                                                        </td>
                                                        <td style="white-space: nowrap;">{{ 'Opening Balance' }}</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="text-align: right; white-space: nowrap;">
                                                            {{ number_format($openingBalance->opening_balance, 2) }}
                                                            @php
                                                                $cashDebitAmount += $openingBalance->opening_balance;
                                                            @endphp
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td style="white-space: nowrap;">
                                                            {{ Auth::user()->dateFormat($today) }}
                                                        </td>
                                                        <td style="white-space: nowrap;">{{ 'Opening Balance' }}</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="text-align: right; white-space: nowrap;">
                                                            {{ number_format(0, 2) }}
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                @endif

                                                @foreach ($records as $record)
                                                    <tr>
                                                        {{-- @dd($transaction); --}}
                                                        <td style="white-space: nowrap;">
                                                            {{ Auth::user()->dateFormat($record->date) }}
                                                        </td>
                                                        <td style="white-space: nowrap;">{{ $record->type ?? '-' }}</td>
                                                        <td>{{ $record->payment_method ?? '-' }}</td>
                                                        @if ($record->entry_type == 'cash_sale')
                                                            <td>{{ \Auth::user()->invoiceNumberFormat($record->invoice_id) ?? '-' }}
                                                            </td>
                                                        @elseif($record->entry_type == 'document_charges')
                                                            <td>{{ '-' }}</td>
                                                        @else
                                                            <td>{{ $record->receipt_number ?? '-' }}</td>
                                                        @endif
                                                        <td>{{ $record->invoice_number ?? '-' }}</td>
                                                        <td>{{ $record->customer_name ?? '-' }}</td>
                                                        <td>{{ $record->product_name ?? '-' }}</td>
                                                        <td style="text-align: right; white-space: nowrap;">
                                                            @if ($record->debit_credit == 'debit')
                                                                {{ number_format($record->amount, 2) }}
                                                                @php
                                                                    $debitAmount += $record->amount;
                                                                    if ($record->payment_method == 'CHEQUE') {
                                                                        $chequeDebitAmount += $record->amount;
                                                                    } elseif ($record->payment_method == 'CARD') {
                                                                        $cardDebitAmount += $record->amount;
                                                                    } elseif (
                                                                        $record->payment_method == 'ONLINEPAYMENT'
                                                                    ) {
                                                                        $onlineDebitAmount += $record->amount;
                                                                    } elseif ($record->payment_method == 'CASH') {
                                                                        $cashDebitAmount += $record->amount;
                                                                    }
                                                                @endphp
                                                            @else
                                                                {{ '-' }}
                                                            @endif
                                                        </td>
                                                        <td style="text-align: right; white-space: nowrap;">
                                                            @if ($record->debit_credit == 'credit')
                                                                {{ number_format($record->amount, 2) }}
                                                                @php
                                                                    $creditAmount += $record->amount;
                                                                    if ($record->payment_method == 'CHEQUE') {
                                                                        $chequeCreditAmount += $record->amount;
                                                                    } elseif ($record->payment_method == 'CARD') {
                                                                        $cardCreditAmount += $record->amount;
                                                                    } elseif (
                                                                        $record->payment_method == 'ONLINEPAYMENT'
                                                                    ) {
                                                                        $onlineCreditAmount += $record->amount;
                                                                    } elseif ($record->payment_method == 'CASH') {
                                                                        $cashCreditAmount += $record->amount;
                                                                    }
                                                                @endphp
                                                            @else
                                                                {{ '-' }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $record->remarks ?? '-' }}</td>

                                                    </tr>
                                                @endforeach

                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th style="white-space: nowrap;">Total Debits/Credits</th>
                                                    <th style="white-space: nowrap; text-align: right;">
                                                        {{ number_format($debitAmount, 2) }} </th>
                                                    <th style="white-space: nowrap; text-align: right;">
                                                        {{ number_format($creditAmount, 2) }}
                                                    </th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th style="white-space: nowrap;">Total Cheque Payments</th>
                                                    <th style="white-space: nowrap; text-align: right;">
                                                        {{ number_format($chequeDebitAmount, 2) }} </th>
                                                    <th style="white-space: nowrap; text-align: right;">
                                                        {{ number_format($chequeCreditAmount, 2) }}
                                                    </th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th style="white-space: nowrap;">Total Credit Card Payments</th>
                                                    <th style="white-space: nowrap; text-align: right;">
                                                        {{ number_format($cardDebitAmount, 2) }} </th>
                                                    <th style="white-space: nowrap; text-align: right;">
                                                        {{ number_format($cardCreditAmount, 2) }}
                                                    </th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th style="white-space: nowrap;">Total Online Payments</th>
                                                    <th style="white-space: nowrap; text-align: right;">
                                                        {{ number_format($onlineDebitAmount, 2) }} </th>
                                                    <th style="white-space: nowrap; text-align: right;">
                                                        {{ number_format($onlineCreditAmount, 2) }}
                                                    </th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th style="white-space: nowrap;">Total Cash Payments</th>
                                                    <th style="white-space: nowrap; text-align: right;">
                                                        {{ number_format($cashDebitAmount, 2) }} </th>
                                                    <th style="white-space: nowrap; text-align: right;">
                                                        {{ number_format($cashCreditAmount, 2) }}
                                                    </th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th style=" font-size: 15px; font-weight: 900; white-space: nowrap;">
                                                        Total Cash in Hands
                                                    </th>
                                                    <th
                                                        style="white-space: nowrap; font-size: 15px; font-weight: 900; text-align: right;">
                                                        {{ number_format($cashDebitAmount - $cashCreditAmount, 2) }}
                                                    </th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </tbody>
                                        @endif
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <div class="card br-4 cus_show">

                            <div class="card-header">
                                <div class="d-flex justify-content-between w-100" style="align-items: center;">
                                    <h4>{{ __('Bank Deposit') }}</h4>
                                    <a href="{{ route('bank-deposits.index') }}" type="button" class="btn btn-primary"
                                        style="padding: .3rem .8rem; border-radius: 6px;"><i class="fa fa-search"></i> View
                                        Deposit History</a>
                                </div>
                            </div>

                            <form action="{{ route('bank-deposits.store') }}" method="POST" id="bank_deposit_form">
                                @csrf
                                <div class="card-body">
                                    <div class="col-md-12 mb-3">
                                        <label for="deposited_amount">{{ __('Amount:') }}</label>
                                        <input type="number" class="form-control text-right" name="deposited_amount"
                                            id="deposited_amount" step="0.01"
                                            value="{{ number_format($cashDebitAmount - $cashCreditAmount, 2, '.', '') }}"
                                            required min="0" />
                                        <input type="hidden" name="total_cash_in_hand" id="total_cash_in_hand"
                                            value="{{ $cashDebitAmount - $cashCreditAmount }}">
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="deposited_person_id">{{ __('Depositing Person:') }}</label>
                                        <select class="form-control" name="deposited_person_id" id="deposited_person_id"
                                            required>
                                            <option value="">{{ __('Select an user') }}</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="deposited_date">{{ __('Date:') }}</label>
                                        <input type="date" class="form-control" name="deposited_date"
                                            id="deposited_date" value="{{ \Carbon\Carbon::today()->toDateString() }}"
                                            required />
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="deposited_time">{{ __('Time:') }}</label>
                                        <input type="time" class="form-control" name="deposited_time"
                                            id="deposited_time" value="{{ \Carbon\Carbon::now()->format('H:i') }}"
                                            required />
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="reference_no">{{ __('Reference No:') }}</label>
                                        <input type="text" class="form-control" name="reference_no" id="reference_no"
                                            required />
                                    </div>

                                    <div class="col-md-12">
                                        <label for="deposited_remarks">{{ __('Remarks:') }}</label>
                                        <textarea type="text" class="form-control" name="deposited_remarks" id="deposited_remarks" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <div class="d-flex justify-content-end w-100">
                                        <button type="submit" class="btn btn-primary">{{ __('Deposit') }}</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            $('.se-pre-con').fadeOut('slow');
        });

        $('#bank_deposit_form').on('submit', function(event) {
            event.preventDefault();

            const depositedAmount = parseFloat($('#deposited_amount').val());
            console.log(depositedAmount);

            if (isNaN(depositedAmount) || depositedAmount <= 0) {
                Swal.fire({
                    title: "Error!",
                    text: "The amount must be greater than 0",
                    icon: "error",
                });

            } else {
                $(this).submit();
            }
        });

        $(document).on('focusout', '#deposited_amount', function() {
            let value = parseFloat($(this).val()).toFixed(2);
            $(this).val(value);
        });
    </script>
@endsection
