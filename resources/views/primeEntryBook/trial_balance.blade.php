@extends('layouts.admin')

@section('page-title')
{{ __('Trial Balance') }}
@endsection
@section('content')
<section class="section">
    {{-- <div class="section-header">
        <h1>{{ __('Trial Balance') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
        <div class="breadcrumb-item">{{ __('Trial Balance') }}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Trial Balance') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
                                <div class="breadcrumb-item">{{ __('Trial Balance') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{ __('View Trial Balance') }}</h4>
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
                                            <table class="table table-flush" id="trial_balances_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th> {{ __('Trial Balance ID') }}</th>
                                                        <th> {{ __('Description/Chart of Account') }}</th>
                                                        <th> {{ __('Debit') }}</th>
                                                        <th> {{ __('Credit') }}</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($trialbalances as $k => $trialbalance)
                                                    <tr>
                                                        <td>
                                                            {{ AUth::user()->trialBalanceNumberFormat($trialbalance['id']) }}
                                                        </td>
                                                        <td class="trial_balance_td">
                                                            {{ $trialbalance->chart_of_account->name}}</td>
                                                        <td class="trial_balance_td">{{ $trialbalance['debit'] }}</td>
                                                        <td class="trial_balance_td">{{ $trialbalance['credit'] }}</td>
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
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        if (localStorage.getItem("trial_balance_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("trial_balance_no"));
        }
        var trial_balancesTable = $('#trial_balances_table').DataTable({
            order: [
                [0, "asc"]
            ]
        });
        trial_balancesTable.page(pageNo).draw(false);

        $('#trial_balances_table').on('page.dt', function () {
            var pageInfo = trial_balancesTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("trial_balance_no", pageNo);
        });
    });

    $('body').on('click', '.trial_balance_td', function () {
        var trialBalanceId = $(this).closest('tr').attr("data-id");
        window.location.href = "{{ url('trial_balances') }}/" + trialBalanceId;
    });

</script>

@endsection
