@extends('layouts.admin')

@section('page-title')
    {{ __('Petty Cash Book') }}
@endsection
@section('content')
    <section class="section">
        {{-- <div class="section-header">
        <h1>{{ __('Petty Cash Book') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
        <div class="breadcrumb-item">{{ __('Petty Cash Book') }}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">


                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Petty Cash Book') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                    <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
                                    <div class="breadcrumb-item">{{ __('Petty Cash Book') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('petty_cash_book.search') }}">
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
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{ __('View Petty Cash Book') }}</h4>
                            <div class="card-header-action">
                                {{-- @can('create account transaction')
                            <a href="#" data-size="xl" data-url="{{ route('accountTransaction.create') }}"
                            data-ajax-popup="true" data-title="{{ __('Create New Account Transaction') }}" class="btn
                            btn-icon icon-left btn-primary commonModal">
                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                            <span class="btn-inner--text"> {{ __('Create') }}</span>
                            </a>
                            @endcan --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body p-0">
                            <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-flush" id="petty_cash_books_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        {{-- <th> {{ __('Petty Cash ID') }}</th> --}}
                                                        <th style="text-align: center"> {{ __('Date') }}</th>
                                                        <th style="text-align: center"> {{ __('Description') }}</th>
                                                        <th style="text-align: center"> {{ __('Narration') }}</th>
                                                        <th style="text-align: center"> {{ __('Amount Received') }}
                                                            ({{ $currencySymbol }})</th>
                                                        <th style="text-align: center"> {{ __('Amount Paid') }}
                                                            ({{ $currencySymbol }})</th>
                                                    </tr>
                                                </thead>
                                                @if ($showTable == 'Yes')
                                                    <tbody>
                                                        @foreach ($pettycashbooks as $pettycashbook)
                                                            <tr>
                                                                <td class="petty_cash_book_td" style="text-align: center">
                                                                    {{ $pettycashbook['date'] }}</td>
                                                                <td class="petty_cash_book_td">
                                                                    {{ $pettycashbook->related_chart_of_account->name ?? '' }}
                                                                </td>
                                                                <td class="petty_cash_book_td"></td>
                                                                <td class="petty_cash_book_td" style="text-align: right">
                                                                    @if ($pettycashbook->debit_credit == 1)
                                                                        {{ $pettycashbook->amount }}
                                                                    @endif
                                                                </td>
                                                                <td class="petty_cash_book_td" style="text-align: right">
                                                                    @if ($pettycashbook->debit_credit == 0)
                                                                        {{ $pettycashbook->amount }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        {{-- @foreach ($pettycashbooks as $k => $pettycashbook)
                                                        <tr>
                                                            <td class="petty_cash_book_td" style="text-align: center">{{ $pettycashbook['date']}}
                                                </td>
                                                <td class="petty_cash_book_td">
                                                    {{ $pettycashbook->chart_of_account->name ?? ''}}</td>
                                                <td class="petty_cash_book_td">{{ $pettycashbook['narration'] }}</td>
                                                <td class="petty_cash_book_td" style="text-align: right">
                                                    {{ $pettycashbook['amount_received'] }}</td>
                                                <td class="petty_cash_book_td" style="text-align: right">
                                                    {{ $pettycashbook['amount_paid'] }}</td>
                                                </tr>
                                                @endforeach --}}
                                                    </tbody>
                                                @endif
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
    </section>


    <script>
        $(document).ready(function() {
            $(".se-pre-con").fadeOut('slow');
            if (localStorage.getItem("petty_cash_book_no") == null) {
                var pageNo = 0;
            } else {
                var pageNo = Number(localStorage.getItem("petty_cash_book_no"));
            }
            var petty_cash_booksTable = $('#petty_cash_books_table').DataTable({
                order: [
                    [0, "asc"]
                ]
            });
            petty_cash_booksTable.page(pageNo).draw(false);

            $('#petty_cash_books_table').on('page.dt', function() {
                var pageInfo = petty_cash_booksTable.page.info();
                pageNo = pageInfo.page;
                localStorage.setItem("petty_cash_book_no", pageNo);
            });
        });

        $('body').on('click', '.petty_cash_book_td', function() {
            var pettyCashBookId = $(this).closest('tr').attr("data-id");
            window.location.href = "{{ url('cash_books') }}/" + pettyCashBookId;
        });
    </script>

@endsection
