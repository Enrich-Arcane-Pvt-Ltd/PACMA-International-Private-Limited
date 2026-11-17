@extends('layouts.admin')
@section('page-title')
    {{ __('Payment Voucher') }}
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
            <h1>{{__('Payment Voucher')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Payment Voucher')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Payment Voucher') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Payment Voucher') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Payment Voucher') }}</h4>
                                @can('create payment voucher')
                                    <div class="col-auto">
                                        <a href="#" data-size="xl" data-url="{{ route('payment_voucher.create') }}"
                                            data-ajax-popup="true" data-title="{{ __('Create Payment Voucher') }}"
                                            class="btn btn-icon icon-left btn-primary">
                                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                            <span class="btn-inner--text"> {{ __('Create') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-body p-0">
                                <div id="table-1_wrapper"
                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-flush" id="payment_voucher_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th> {{ __('Voucher #') }}</th>
                                                            <th> {{ __('Voucher Date') }}</th>
                                                            <th> {{ __('#') }}</th>
                                                            <th> {{ __('Chart Of Account') }}</th>
                                                            <th> {{ __('Pay Type') }}</th>
                                                            <th> {{ __('Pay To') }}</th>
                                                            <th> {{ __('Voucher Description') }}</th>
                                                            <th> {{ __('Payment Method') }}</th>
                                                            <th> {{ __('Cheque No.') }}</th>
                                                            <th> {{ __('Amount') }} ({{ $currencySymbol }})</th>
                                                            @if (Gate::check('edit payment voucher') ||
                                                                    Gate::check('delete
                                                                                                                                                                                payment voucher'))
                                                                <th class="text-center"> {{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($vouchers as $voucher)
                                                            <tr class="font-style">
                                                                <td>
                                                                    <p class="btn btn-outline-primary">
                                                                        {{ AUth::user()->paymentVoucherNumberFormat($voucher->id) }}
                                                                    </p>
                                                                </td>
                                                                <td>{{ $voucher->date }}</td>
                                                                <td>
                                                                    @if (isset($voucher->loans))
                                                                        {{ Auth::user()->loanNumberFormat($voucher->loan_id) }}
                                                                    @elseif (isset($voucher->pawnings))
                                                                        {{ Auth::user()->pawningNumberFormat($voucher->pawning_id) }}
                                                                    @elseif (isset($voucher->advances))
                                                                        {{ Auth::user()->advanceNumberFormat($voucher->advance_id) }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $voucher->account->account_name ?? '' }}</td>
                                                                <td>
                                                                    @if ($voucher->pay_type == 'supplier')
                                                                        Supplier
                                                                    @elseif($voucher->pay_type == 'other')
                                                                        Other
                                                                    @elseif($voucher->pay_type == 'customer')
                                                                        Customer
                                                                    @else
                                                                        Cash
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($voucher->pay_type == 'supplier')
                                                                        {{ $voucher->supplier->name ?? '' }}
                                                                    @elseif($voucher->pay_type == 'other')
                                                                        {{ $voucher->pay_to }}
                                                                    @elseif($voucher->pay_type == 'customer')
                                                                        {{ $voucher->customer->name ?? '' }}
                                                                    @else
                                                                        Cash
                                                                    @endif
                                                                </td>
                                                                <td>{{ $voucher->description }}</td>
                                                                <td>
                                                                    @if ($voucher->payment_method == 'CASH')
                                                                        ONLINE
                                                                    @elseif ($voucher->payment_method == 'CHEQ')
                                                                        CHEQUE
                                                                    @elseif ($voucher->payment_method == 'CARD')
                                                                        CARD
                                                                    @endif
                                                                </td>
                                                                <td>{{ $voucher->cheque_number }}</td>
                                                                <td>{{ number_format($voucher->amount, 2) }}</td>
                                                                @if (Gate::check('edit payment voucher') ||
                                                                        Gate::check('delete
                                                                                                                                                                                        payment voucher'))
                                                                    <td class="action text-right">
                                                                        @can('edit payment voucher')
                                                                            @if ($voucher->status == 1)
                                                                                <a href="#"
                                                                                    class="btn btn-primary btn-action mr-1"
                                                                                    data-size="xl"
                                                                                    data-url="{{ route('payment_voucher.edit', $voucher->id) }}"
                                                                                    data-ajax-popup="true"
                                                                                    data-title="{{ __('Edit Payment Voucher') }}"
                                                                                    data-toggle="tooltip"
                                                                                    data-original-title="{{ __('Edit') }}">
                                                                                    <i class="fas fa-pencil-alt"></i>
                                                                                </a>
                                                                            @endif
                                                                        @endcan
                                                                        @can('print payment voucher')
                                                                            <a href="{{ route('payment_voucher.print', $voucher->id) }}"
                                                                                target="_blank"
                                                                                class="btn btn-info btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Print') }}">
                                                                                <i class="fas fa-print"></i>
                                                                            </a>
                                                                        @endcan
                                                                        @can('delete payment voucher')
                                                                            @if ($voucher->status == 1)
                                                                                <a href="#"
                                                                                    class="btn btn-danger btn-action"
                                                                                    data-toggle="tooltip"
                                                                                    data-original-title="{{ __('Delete') }}"
                                                                                    data-confirm="{{ __('Are You Sure?') . ' | ' . __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="document.getElementById('delete-form-{{ $voucher->id }}').submit();"><i
                                                                                        class="fas fa-trash"></i></a>
                                                                            @elseif($voucher->status == 2 && $voucher->payment_method == 'CASH')
                                                                                <a href="#"
                                                                                    class="btn btn-danger btn-action"
                                                                                    data-toggle="tooltip"
                                                                                    data-original-title="{{ __('Delete') }}"
                                                                                    data-confirm="{{ __('Are You Sure?') . ' | ' . __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="document.getElementById('delete-form-{{ $voucher->id }}').submit();"><i
                                                                                        class="fas fa-trash"></i></a>
                                                                            @endif
                                                                            {!! Form::open([
                                                                                'method' => 'DELETE',
                                                                                'route' => ['payment_voucher.destroy', $voucher->id],
                                                                                'id' => 'delete-form-' . $voucher->id,
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
            // console.log(sales);
            if (localStorage.getItem("payment_voucher_page_no") == null) {
                var pageNo = 0;
            } else {
                var pageNo = Number(localStorage.getItem("payment_voucher_page_no"));
            }
            var paymentVoucherTable = $('#payment_voucher_table').DataTable({
                order: [
                    [0, "desc"]
                ]
            });
            paymentVoucherTable.page(pageNo).draw(false);

            $('#payment_voucher_table').on('page.dt', function() {
                var pageInfo = paymentVoucherTable.page.info();
                pageNo = pageInfo.page;
                localStorage.setItem("payment_voucher_page_no", pageNo);
            });
        });
    </script>
    {{-- <script>


        $("body").on("click", ".delete", function(){
            var value = $( this ).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Continue!'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("timeperiods/unblock/"+value);
                }
            });

            });

            $("body").on("click", ".unblock", function(){
            var value = $( this ).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Continue!'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("timeperiods/block/"+value);
                }
            });

            });


        </script> --}}
@endsection
