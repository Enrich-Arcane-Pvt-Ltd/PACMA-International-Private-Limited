@extends('layouts.admin')

@section('page-title')
    {{ __('Account Types') }}
@endsection

@section('content')
<style>
    table tfoot {
        display: table-row-group;
    }
</style>

<section class="section">
    {{-- <div class="section-header">
        <h1>{{ __('Account Types') }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
            <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
            <div class="breadcrumb-item">{{ __('Maintenance') }}</div>
            <div class="breadcrumb-item">{{ __('Account Types') }}</div>
        </div>
    </div> --}}

    <div class="row justify-content-center">

        <div class="col-12">
            <div class="bg-white p-2 br-4 mb-2">
                <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                    <h4 class="mt-2 ">{{ __('Account Types') }}</h1>
                    <div class="section-header-breadcrumb d-flex ">
                        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                        <div class="breadcrumb-item">{{ __('Maintenance') }}</div>
                        <div class="breadcrumb-item">{{ __('Account Types') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card br-4 cus_show">
                <div class="card-header">
                    <div class="d-flex justify-content-between w-100">
                        <h4>{{ __('Manage Account Types') }}</h4>
                        <div class="card-header-action"></div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="card-body p-0">
                        <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-flush" id="account_type_table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th> {{ __('Account Type') }}</th>
                                                    <th> {{ __('Detail Type') }}</th>
                                                    <th> {{ __('Description') }}</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($detailTypes as $detailTypes)
                                                    <tr>
                                                        <td class="chart_of_account_td">{{ $detailTypes->accountType->account_type_name }}</td>
                                                        <td class="chart_of_account_td">{{ $detailTypes->detail_type_name }}</td>
                                                        <td class="chart_of_account_td">{{ $detailTypes->description }}</td>
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
    $(document).ready(function(){
        $(".se-pre-con").fadeOut('slow');

        if(localStorage.getItem("account_type_no") == null){
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("account_type_no"));
        }

        var chart_of_accountTable = $('#account_type_table').DataTable({
            pageLength: 25,
            order: [[0,"asc"]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    title: 'Account Types of {{config('app.name')}}',
                    footer: true
                }, {
                    extend: 'print',
                    title: 'Account Types of {{config('app.name')}}',
                    footer: true
                }, {
                    extend: 'csv',
                    title: 'Account Types of {{config('app.name')}}',
                    footer: true
                }
            ]
        });
        chart_of_accountTable.page(pageNo).draw(false);

        $('#account_type_table').on( 'page.dt', function () {
            var pageInfo =  chart_of_accountTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("account_type_no", pageNo);
        });
    });
</script>

@endsection
