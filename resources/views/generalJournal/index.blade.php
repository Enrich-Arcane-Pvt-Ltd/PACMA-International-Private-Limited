@extends('layouts.admin')

@section('page-title')
    {{ __('General Journal') }}
@endsection
@section('content')
    <section class="section">
        {{-- <div class="section-header">
        <h1>{{ __('General Journal') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
        <div class="breadcrumb-item">{{ __('General Journal') }}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">


                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('General Journal') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                    <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
                                    <div class="breadcrumb-item">{{ __('General Journal') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Manage Journals') }}</h4>
                                <div class="card-header-action">
                                    @if (Gate::check('create general journal') || Gate::check('create stock adjustment'))
                                        <a href="#" data-size="2xl" data-url="{{ route('general_journal.create') }}"
                                            data-ajax-popup="true" data-title="{{ __('Create New Journal') }}"
                                            class="btn btn-icon icon-left btn-primary commonModal">
                                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                            <span class="btn-inner--text"> {{ __('Create') }}</span>
                                        </a>
                                    @endif
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
                                                <table class="table table-flush" id="general_journal_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th style="text-align: center"> {{ __('Journal #') }}</th>
                                                            <th style="text-align: center"> {{ __('Date') }}</th>
                                                            <th style="text-align: center"> {{ __('Account Name') }}</th>
                                                            <th style="text-align: center"> {{ __('Account Status') }}</th>
                                                            <th style="text-align: center">
                                                                {{ __('Related Account Name') }}
                                                            </th>
                                                            <th style="text-align: center">
                                                                {{ __('Realated Account Status') }}</th>
                                                            <th style="text-align: center"> {{ __('Narration') }}</th>
                                                            <th style="text-align: center"> {{ __('Amount') }}
                                                                ({{ $currencySymbol }})</th>
                                                            <th class="text-center" style="text-align: center">
                                                                {{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($generaljournals as $k => $generaljournal)
                                                            <tr>
                                                                <td style="text-align: center">
                                                                    {{ AUth::user()->journalNumberFormat($generaljournal['id']) }}
                                                                </td>
                                                                <td class="general_journal_td" style="text-align: center">
                                                                    {{ $generaljournal['date'] }}</td>
                                                                <td class="general_journal_td">
                                                                    {{ $generaljournal->chart_of_account->name }}</td>
                                                                <td class="general_journal_td" style="text-align: center">
                                                                    @if ($generaljournal->debit_credit == 0)
                                                                        Credit
                                                                    @elseif($generaljournal->debit_credit == 1)
                                                                        Debit
                                                                    @endif
                                                                </td>
                                                                <td class="general_journal_td">
                                                                    {{ $generaljournal->retlated_chart_of_account->name }}
                                                                </td>
                                                                <td class="general_journal_td" style="text-align: center">
                                                                    @if ($generaljournal->related_debit_credit == 0)
                                                                        Credit
                                                                    @elseif($generaljournal->related_debit_credit == 1)
                                                                        Debit
                                                                    @endif
                                                                </td>
                                                                <td class="general_journal_td">
                                                                    {{ $generaljournal['narration'] }}</td>
                                                                <td class="general_journal_td" style="text-align: right">
                                                                    {{ $generaljournal['amount'] }}</td>
                                                                <td class="text-center">
                                                                    @can('show general journal')
                                                                        <a href="{{ route('general_journal.show', $generaljournal['id']) }}"
                                                                            class="btn btn-info btn-action mr-1"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('View') }}">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                    @endcan

                                                                    @can('edit general journal')
                                                                        <a href="#"
                                                                            class="btn btn-primary btn-action mr-1"
                                                                            data-size="2xl"
                                                                            data-url="{{ route('general_journal.edit', $generaljournal['id']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Edit Journal') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Edit') }}">
                                                                            <i class="fas fa-pencil-alt"></i>
                                                                        </a>
                                                                    @endcan
                                                                </td>
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
                </div>
            </div>
        </div>
    </section>


    <script>
        $(document).ready(function() {
            $(".se-pre-con").fadeOut('slow');
            if (localStorage.getItem("general_journal_no") == null) {
                var pageNo = 0;
            } else {
                var pageNo = Number(localStorage.getItem("general_journal_no"));
            }
            var general_journalTable = $('#general_journal_table').DataTable({
                order: [
                    [0, "asc"]
                ]
            });
            general_journalTable.page(pageNo).draw(false);

            $('#general_journal_table').on('page.dt', function() {
                var pageInfo = general_journalTable.page.info();
                pageNo = pageInfo.page;
                localStorage.setItem("general_journal_no", pageNo);
            });
        });

        $('body').on('click', '.general_journal_td', function() {
            var generalJournalId = $(this).closest('tr').attr("data-id");
            window.location.href = "{{ url('general_journal') }}/" + generalJournalId;
        });
    </script>
@endsection
