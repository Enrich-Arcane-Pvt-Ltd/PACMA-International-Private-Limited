@extends('layouts.admin')
@section('page-title')
    {{ __('Bank Transfers') }}
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
    </style>
    <section class="section">
        <div class="se-pre-con"></div>
        {{-- <div class="section-header">
            <h1>{{__('Bank Transfers')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Bank Transfers')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Bank Transfers') }}</h1>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Bank Transfers') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Manage Bank Transfers') }}</h4>
                                <div class="card-header-action">
                                    <div class="dropdown">
                                        <a href="#" data-toggle="dropdown"
                                            class="btn btn-icon icon-left btn-primary"><i
                                                class="fas fa-filter"></i>{{ __('Filter') }}</a>
                                        <div
                                            class="dropdown-menu dropdown-list dropdown-menu-right Filter-dropdown w-64 {{ isset($_GET['date']) ? 'show' : '' }}">
                                            {{ Form::open(['route' => ['bank-transfer.index'], 'method' => 'GET']) }}
                                            <div class="form-group">
                                                {{ Form::label('date', __('Date')) }}
                                                {{ Form::text('date', isset($_GET['date']) ? $_GET['date'] : '', ['class' => 'form-control datepicker-range']) }}
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('from_account', __('From Account')) }}
                                                {{ Form::select('from_account', $bankAccount, isset($_GET['from_account']) ? $_GET['from_account'] : '', ['class' => 'form-control font-style selectric']) }}
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('to_account', __('To Account')) }}
                                                {{ Form::select('to_account', $bankAccount, isset($_GET['to_account']) ? $_GET['to_account'] : '', ['class' => 'form-control font-style selectric']) }}
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                                                <a href="{{ route('bank-transfer.index') }}"
                                                    class="btn btn-danger">{{ __('Reset') }}</a>
                                            </div>
                                            {{ Form::close() }}
                                        </div>
                                    </div>

                                    @can('create transfer')
                                        <a href="#" data-size="xl" data-url="{{ route('bank-transfer.create') }}"
                                            data-ajax-popup="true" data-title="{{ __('Create New Bank Transfer ') }}"
                                            class="btn btn-icon icon-left btn-primary">
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
                                                <table class="table table-flush" id="bank_transfers_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th> {{ __('Bank Transfer #') }}</th>
                                                            <th> {{ __('Date') }}</th>
                                                            <th> {{ __('From Account') }}</th>
                                                            <th> {{ __('To Account') }}</th>
                                                            <th> {{ __('Amount') }} ({{ $currencySymbol }})</th>
                                                            <th> {{ __('Reference') }}</th>
                                                            <th> {{ __('Description') }}</th>
                                                            @if (Gate::check('edit transfer') ||
                                                                    Gate::check('delete
                                                                                                                                                                                                                                            transfer'))
                                                                <th class="text-center"> {{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($bankTransfers as $bankTransfer)
                                                            <tr class="font-style">
                                                                <td>
                                                                    <a href="#" class="btn btn-outline-primary">
                                                                        {{ AUth::user()->bankTransferNumberFormat($bankTransfer['bank_transfer_id']) }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ \Auth::user()->dateFormat($bankTransfer->date) }}
                                                                </td>
                                                                <td>{{ !empty($bankTransfer->fromBankAccount()) ? $bankTransfer->fromBankAccount()->bank . ' - ' . $bankTransfer->fromBankAccount()->bank_holder : '' }}
                                                                </td>
                                                                <td>{{ !empty($bankTransfer->toBankAccount()) ? $bankTransfer->toBankAccount()->bank . ' - ' . $bankTransfer->toBankAccount()->bank_holder : '' }}
                                                                </td>
                                                                <td>
                                                                    {{-- {{ \Auth::user()->priceFormat($bankTransfer->amount) }} --}}
                                                                    {{ number_format($bankTransfer->amount ?? 0, 2) }}
                                                                </td>
                                                                <td>{{ $bankTransfer->reference }}</td>
                                                                <td>{{ $bankTransfer->description }}</td>

                                                                @if (Gate::check('edit transfer') ||
                                                                        Gate::check('delete
                                                                                                                                                                                                                                                        transfer'))
                                                                    <td class="action text-center">
                                                                        @can('edit transfer')
                                                                            <a href="#!"
                                                                                class="btn btn-primary btn-action mr-1"
                                                                                data-size="xl"
                                                                                data-url="{{ route('bank-transfer.edit', $bankTransfer->id) }}"
                                                                                data-ajax-popup="true"
                                                                                data-title="{{ __('Edit Bank Transfer') }}"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Edit') }}">
                                                                                <i class="fas fa-pencil-alt"></i>
                                                                            </a>
                                                                        @endcan
                                                                        @can('delete transfer')
                                                                            <a href="#!" class="btn btn-danger btn-action "
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Delete') }}"
                                                                                data-confirm="{{ __('Are you sure you want to delete this bank transfer ?') . ' | ' . __('Deleting this bank transfer will no longer available in this company. Do you want to continue ?') }}"
                                                                                data-confirm-yes="document.getElementById('delete-form-{{ $bankTransfer->id }}').submit();">
                                                                                <i class="fas fa-trash"></i>
                                                                            </a>
                                                                            {!! Form::open([
                                                                                'method' => 'DELETE',
                                                                                'route' => ['bank-transfer.destroy', $bankTransfer->id],
                                                                                'id' => 'delete-form-' . $bankTransfer->id,
                                                                            ]) !!}
                                                                            {!! Form::close() !!}
                                                                        @endcan
                                                                    </td>
                                                                @endif
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
            if (localStorage.getItem("bank_transfer_page_no") == null) {
                var pageNo = 0;
            } else {
                var pageNo = Number(localStorage.getItem("bank_transfer_page_no"));
            }
            var bankTransfersTable = $('#bank_transfers_table').DataTable({
                order: [
                    [0, "desc"]
                ]
            });
            bankTransfersTable.page(pageNo).draw(false);

            $('#bank_transfers_table').on('page.dt', function() {
                var pageInfo = bankTransfersTable.page.info();
                pageNo = pageInfo.page;
                localStorage.setItem("bank_transfer_page_no", pageNo);
            });
        });
    </script>
@endsection
