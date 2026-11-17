@extends('layouts.admin')
@section('page-title')
    {{ __('Cheque Issue') }}
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
        <h1>{{__('Cheque Issue')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Cheque Issue')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">


                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2">{{ __('Cheque Issue') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Cheque Issue') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Manage Cheques') }}</h4>
                                <div class="card-header-action">
                                    @can('create cheque')
                                        <a href="#" data-size="xl" data-url="{{ route('cheque-print.create') }}"
                                            data-ajax-popup="true" data-title="{{ __('Create New Cheque') }}"
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
                                                <table class="table table-flush" id="cheque_issue_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th> {{ __('Cheque #') }}</th>
                                                            <th> {{ __('Cheque Number') }}</th>
                                                            <th> {{ __('Cross Type') }}</th>
                                                            <th> {{ __('Date') }}</th>
                                                            <th> {{ __('#') }}</th>
                                                            <th> {{ __('Chart Of Account') }}</th>
                                                            <th> {{ __('Pay') }}</th>
                                                            <th> {{ __('Amount') }} ({{ $currencySymbol }})</th>
                                                            <th> {{ __('Amount in Words') }}</th>
                                                            <th> {{ __('Status') }}</th>
                                                            @if (Gate::check('print payment voucher') ||
                                                                    Gate::check('print
                                                                                                                                                                                                                                                                                                        cheque') ||
                                                                    Gate::check('cancel cheque'))
                                                                <th class="text-center"> {{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($cheques as $cheque)
                                                            <tr id="center_detail">
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary view"
                                                                        value="{{ $cheque->id }}">
                                                                        {{ AUth::user()->chequeNumberFormat($cheque['cheque_id']) }}
                                                                    </button>
                                                                </td>
                                                                <td>{{ $cheque->cheque_number }}</td>
                                                                <td>{{ $cheque->cross_type }}</td>
                                                                <td>{{ $cheque->date }}</td>
                                                                <td>
                                                                    @if (isset($cheque->voucher->loans))
                                                                        {{ Auth::user()->loanNumberFormat($cheque->voucher->loan_id) }}
                                                                    @elseif (isset($cheque->voucher->pawnings))
                                                                        {{ Auth::user()->pawningNumberFormat($cheque->voucher->pawning_id) }}
                                                                    @elseif (isset($cheque->voucher->advances))
                                                                        {{ Auth::user()->advanceNumberFormat($cheque->voucher->advance_id) }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $cheque->chart_of_account_name ?? ($cheque->account->account_name ?? ($cheque->account->name ?? ($cheque->voucher->account->account_name ?? ''))) }}</td>
                                                                <td>
                                                                    @if ($cheque['pay_type'] == 'supplier')
                                                                        {{ $cheque->vender->name ?? '' }}
                                                                    @elseif ($cheque['pay_type'] == 'customer')
                                                                        {{ $cheque->customer->name ?? '' }}
                                                                    @elseif ($cheque['pay_type'] == 'other')
                                                                        {{ $cheque->pay }}
                                                                    @elseif ($cheque['pay_type'] == 'cash')
                                                                        CASH
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{ number_format($cheque->amount ?? 0, 2) }}
                                                                    {{-- {{ \Auth::user()->priceFormat($cheque->amount) }} --}}
                                                                </td>
                                                                <td>{{ $cheque->amount_in_word }}</td>
                                                                <td>
                                                                    @if ($cheque->status == 1)
                                                                        <span class="badge badge-success">Issued</span>
                                                                    @elseif ($cheque->status == 0)
                                                                        <span class="badge badge-warning">Canceled</span>
                                                                    @endif
                                                                </td>
                                                                @if (Gate::check('print payment voucher') ||
                                                                        Gate::check('print
                                                                                                                                                                                                                                                                                                                        cheque') ||
                                                                        Gate::check('cancel cheque'))
                                                                    <td class="action text-center">
                                                                        {{-- @if ($cheque->status == 1)
                                                                            @can('print cheque')
                                                                                <button type="button"
                                                                                    class="btn btn-info btn-action mr-1 print"
                                                                                    title="Print"
                                                                                    value="{{ $cheque->id }}"><i
                                                                                        class="fas fa-print"></i></button>
                                                                            @endcan
                                                                        @endif --}}
                                                                        @can('print payment voucher')
                                                                            <button type="button"
                                                                                class="btn btn-info btn-action mr-1 print-voucher"
                                                                                title="Print Voucher"
                                                                                value="{{ $cheque->id }}"><i
                                                                                    class="fas fa-paste"></i></button>
                                                                        @endcan
                                                                        @if ($cheque->status == 1)
                                                                            @can('cancel cheque')
                                                                                <a href="#"
                                                                                    class="btn btn-danger btn-action"
                                                                                    style="padding-top: 10px;"
                                                                                    data-toggle="tooltip"
                                                                                    data-original-title="{{ __('Cancel') }}"
                                                                                    data-confirm="{{ __('Are You Sure?') . ' | ' . __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="document.getElementById('cancel-form-{{ $cheque->id }}').submit();">
                                                                                    <i class="fas fa-times"></i>
                                                                                </a>
                                                                                {!! Form::open([
                                                                                    'method' => 'GET',
                                                                                    'route' => ['cheque-print_cancel.cancel', $cheque->id],
                                                                                    'id' => 'cancel-form-' . $cheque->id,
                                                                                ]) !!}
                                                                                {!! Form::close() !!}
                                                                            @endcan
                                                                        @endif
                                                                        {{-- @can('edit customer')
                                                            <a href="#" class="btn btn-primary btn-action mr-1" data-size="xl" data-url="{{ route('center.edit', $center['id']) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Edit Center') }}"
                                                            data-toggle="tooltip"
                                                            data-original-title="{{ __('Edit') }}">
                                                            <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endcan

                                                            @if ($center->status == 1)
                                                            @can('delete bank account')
                                                            <button class="btn btn-danger btn-action mr-1 block"
                                                                data-original-title="{{__('Block')}}"
                                                                data-toggle="tooltip" value="{{$center->id}}">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                            @endcan
                                                            @else
                                                            @can('delete bank account')
                                                            <button class="btn btn-success btn-action mr-1 unblock"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Unblock')}}"
                                                                value="{{$center->id}}">
                                                                <i class="fas fa-unlock"></i>
                                                            </button>
                                                            @endcan
                                                            @endif --}}
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
        var cheques = <?php echo json_encode($cheques); ?>;

        $(document).ready(function() {
            $(".se-pre-con").fadeOut('slow');
            // console.log(sales);
            if (localStorage.getItem("cheque_issue_page_no") == null) {
                var pageNo = 0;
            } else {
                var pageNo = Number(localStorage.getItem("cheque_issue_page_no"));
            }
            var chequeIssueTable = $('#cheque_issue_table').DataTable({
                order: [
                    [0, "desc"]
                ]
            });
            chequeIssueTable.page(pageNo).draw(false);

            $('#cheque_issue_table').on('page.dt', function() {
                var pageInfo = chequeIssueTable.page.info();
                pageNo = pageInfo.page;
                localStorage.setItem("cheque_issue_page_no", pageNo);
            });
        });

        $("body").on("click", ".block", function() {
            var value = $(this).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure you want to block this center ?',
                text: "Blocking this center will not allow it to use in this company.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6777ef',
                cancelButtonColor: '#fc544b',
                confirmButtonText: 'Block'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("center/block/" + value);
                }
            });

        });


        $("body").on("click", ".unblock", function() {
            var value = $(this).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure you want to unblock this center ?',
                text: "Unblocking this center will allow it to use in this company.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6777ef',
                cancelButtonColor: '#fc544b',
                confirmButtonText: 'Unblock'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("center/unblock/" + value);
                }
            });
        });

        $('body').on('click', '.view', function() {
            var chequeId = $(this).val();
            window.open("{{ url('cheque-print') }}/" + chequeId + "/view");
        });

        $('body').on('click', '.print', function() {
            var chequeId = $(this).val();
            window.open("{{ url('cheque-print') }}/" + chequeId + "/print");
        });

        $('body').on('click', '.print-voucher', function() {
            var chequeId = $(this).val();
            window.open("{{ url('cheque-print_voucher/payment_voucher') }}/" + chequeId);
        });
    </script>
@endsection
