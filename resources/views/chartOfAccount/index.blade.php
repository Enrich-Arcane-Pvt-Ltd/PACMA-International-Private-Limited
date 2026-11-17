@extends('layouts.admin')

@section('page-title')
{{ __('Chart of Accounts') }}
@endsection

@section('content')
<style>
    table tfoot {
        display: table-row-group;
    }

</style>

<section class="section">
    {{-- <div class="section-header">
        <h1>{{ __('Chart of Accounts') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Maintenance') }}</div>
        <div class="breadcrumb-item">{{ __('Chart of Accounts') }}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Chart of Accounts') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                <div class="breadcrumb-item">{{ __('Maintenance') }}</div>
                                <div class="breadcrumb-item">{{ __('Chart of Accounts') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{ __('Manage Chart of Accounts') }}</h4>
                            <div class="card-header-action">
                                @can('create chart of account')
                                <a href="#" data-size="xl" data-url="{{ route('chart_of_account.create') }}"
                                    data-ajax-popup="true" data-title="{{ __('Create New Chart of Account') }}"
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
                            <div id="table-1_wrapper"
                                class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-flush" id="chart_of_account_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th> {{ __('Chart of Account #') }}</th>
                                                        <th> {{ __('Account Type') }}</th>
                                                        <th> {{ __('Account Name') }}</th>
                                                        <th> {{ __('Group Type') }}</th>
                                                        <th> {{ __('Group Name') }}</th>
                                                        <th> {{ __('Section Name') }}</th>
                                                        <th class="text-center">{{ __('Action') }}</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($chartofaccounts as $k => $chartofaccount)
                                                    <tr>
                                                        <td>{{ AUth::user()->chartOfAccountNumberFormat($chartofaccount['id']) }}
                                                        </td>
                                                        <td class="chart_of_account_td">{{ $chartofaccount['type'] }}
                                                        </td>
                                                        <td class="chart_of_account_td">{{ $chartofaccount['name'] }}
                                                        </td>
                                                        <td class="chart_of_account_td">
                                                            {{ $chartofaccount['group_type'] }}</td>
                                                        <td class="chart_of_account_td">
                                                            {{ $chartofaccount['group_name'] }}</td>
                                                        <td class="chart_of_account_td">
                                                            {{ $chartofaccount['section_name'] }}</td>

                                                        <td class="text-center">
                                                            @can('edit chart of account')
                                                            <a href="#" class="btn btn-primary btn-action mr-1"
                                                                data-size="xl"
                                                                data-url="{{ route('chart_of_account.edit', $chartofaccount['id']) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{ __('Edit Chart of Account') }}"
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
</section>

<script>
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        if (localStorage.getItem("chart_of_account_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("chart_of_account_no"));
        }
        var chart_of_accountTable = $('#chart_of_account_table').DataTable({
            order: [
                [0, "asc"]
            ],
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excel',
                title: 'Chart of Accounts of {{config('
                app.name ')}}',
                footer: true
            }, {
                extend: 'print',
                title: function () {
                    return "<h3>Chart of Accounts of {{config('app.name')}}</h3>";
                },
                footer: true
            }, {
                extend: 'csv',
                title: 'Chart of Accounts of {{config('
                app.name ')}}',
                footer: true
            }]
        });
        chart_of_accountTable.page(pageNo).draw(false);

        $('#chart_of_account_table').on('page.dt', function () {
            var pageInfo = chart_of_accountTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("chart_of_account_no", pageNo);
        });
    });

    $("body").on("click", ".block", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to block this user ?',
            text: "Blocking this user will not allow them to use services in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Block'
        }).then((result) => {
            if (result.value) {
                window.location.replace("chartofaccount/block/" + value);
            }
        });
    });

    $("body").on("click", ".unblock", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to unblock this user ?',
            text: "Unblocking this user will allow them to use services in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Unblock'
        }).then((result) => {
            if (result.value) {
                window.location.replace("chartofaccount/unblock/" + value);
            }
        });
    });

</script>

@endsection
