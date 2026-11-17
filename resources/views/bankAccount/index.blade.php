@extends('layouts.admin')
@section('page-title')
    {{ __('Bank Accounts') }}
@endsection
@section('content')
    <section class="section">
        {{-- <div class="section-header">
            <h1>{{__('Bank Accounts')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Bank Accounts')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Bank Accounts') }}</h1>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Bank Accounts') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Manage Bank Accounts') }}</h4>
                                <div class="card-header-action">
                                    @can('create bank account')
                                        <a href="#" data-size="xl" data-url="{{ route('bank-account.create') }}"
                                            data-ajax-popup="true" data-title="{{ __('Create New Bank Account') }}"
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
                                                <table class="table table-flush" id="dataTable">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th> {{ __('Bank Account #') }}</th>
                                                            <th> {{ __('Bank Holder') }}</th>
                                                            <th> {{ __('Bank') }}</th>
                                                            <th> {{ __('Account No') }}</th>
                                                            <th> {{ __('Current Balance') }} ({{ $currencySymbol }})</th>
                                                            <th> {{ __('Address') }}</th>
                                                            <th> {{ __('Phone') }}</th>
                                                            @if (Gate::check('edit bank account') ||
                                                                    Gate::check('delete bank
                                                                                                                                                                                                                                            account') ||
                                                                    Gate::check('block bank account'))
                                                                <th class="text-center"> {{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($bankAccounts as $bankAccount)
                                                            <tr>
                                                                <td>
                                                                    <a href="#" class="btn btn-outline-primary">
                                                                        {{ AUth::user()->bankAccountNumberFormat($bankAccount['bank_account_id']) }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ $bankAccount->bank_holder }}</td>
                                                                <td>{{ $bankAccount->bank }}</td>
                                                                <td>{{ $bankAccount->account_no }}</td>
                                                                <td>
                                                                    {{ number_format($bankAccount->opening_balance ?? 0, 2) }}
                                                                    {{-- {{ \Auth::user()->priceFormat($bankAccount->opening_balance) }} --}}
                                                                </td>
                                                                <td>{{ $bankAccount->address }}</td>
                                                                <td>{{ $bankAccount->phone }}</td>

                                                                @if (Gate::check('edit bank account') ||
                                                                        Gate::check('delete bank
                                                                                                                                                                                                                                                        account') ||
                                                                        Gate::check('block bank account'))
                                                                    <td class="text-center">
                                                                        @can('edit bank account')
                                                                            <a href="#!"
                                                                                class="btn btn-primary btn-action mr-1"
                                                                                data-size="xl"
                                                                                data-url="{{ route('bank-account.edit', $bankAccount->id) }}"
                                                                                data-ajax-popup="true"
                                                                                data-title="{{ __('Edit Bank Account') }}"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Edit') }}">
                                                                                <i class="fas fa-pencil-alt"></i>
                                                                            </a>
                                                                        @endcan

                                                                        @if ($bankAccount->status == 1)
                                                                            @can('block bank account')
                                                                                <button
                                                                                    class="btn btn-danger btn-action mr-1 block"
                                                                                    data-original-title="{{ __('Block') }}"
                                                                                    data-toggle="tooltip"
                                                                                    value="{{ $bankAccount->id }}">
                                                                                    <i class="fas fa-lock"></i>
                                                                                </button>
                                                                            @endcan
                                                                        @else
                                                                            @can('block bank account')
                                                                                <button
                                                                                    class="btn btn-success btn-action mr-1 unblock"
                                                                                    data-toggle="tooltip"
                                                                                    data-original-title="{{ __('Unblock') }}"
                                                                                    value="{{ $bankAccount->id }}">
                                                                                    <i class="fas fa-unlock"></i>
                                                                                </button>
                                                                            @endcan
                                                                        @endif

                                                                        @can('delete bank account')
                                                                            <a href="#!" class="btn btn-danger btn-action "
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Delete') }}"
                                                                                data-confirm="{{ __('Are you sure you want to delete this bank account ?') . ' | ' . __('Deleting this bank account will no longer available in this company. Do you want to continue ?') }}"
                                                                                data-confirm-yes="document.getElementById('delete-form-{{ $bankAccount->id }}').submit();">
                                                                                <i class="fas fa-trash"></i>
                                                                            </a>
                                                                            {!! Form::open([
                                                                                'method' => 'DELETE',
                                                                                'route' => ['bank-account.destroy', $bankAccount->id],
                                                                                'id' => 'delete-form-' . $bankAccount->id,
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
        $("body").on("click", ".block", function() {
            var value = $(this).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure you want to block this bank account ?',
                text: "Blocking this bank account will not allow it to use in this company.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6777ef',
                cancelButtonColor: '#fc544b',
                confirmButtonText: 'Block'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("bank-account/block/" + value);
                }
            });

        });


        $("body").on("click", ".unblock", function() {
            var value = $(this).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure you want to unblock this bank account ?',
                text: "Unblocking this bank account will allow it to use in this company.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6777ef',
                cancelButtonColor: '#fc544b',
                confirmButtonText: 'Unblock'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("bank-account/unblock/" + value);
                }
            });
        });
    </script>
@endsection
