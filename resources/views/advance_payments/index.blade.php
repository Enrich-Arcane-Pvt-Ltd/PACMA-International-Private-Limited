@extends('layouts.admin')
@section('page-title')
    {{ __('Advance Payments') }}
@endsection
@section('content')
    <script>
        $(function() {
            $(document).on('click', '#billing_data', function() {
                $("[name='billing_title']").val($("[name='title']").val());
                $("[name='billing_name']").val($("[name='name']").val());
                $("[name='billing_house_no']").val($("[name='house_no']").val());
                $("[name='billing_address']").val($("[name='address']").val());
                $("[name='billing_phone']").val($("[name='phone_mobile']").val());
                $("[name='billing_email']").val($("[name='email']").val());
            })
        })
    </script>
    <section class="section">


        {{-- <div class="section-header">
        <h1>{{__('Advance Payments')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Advance Payment')}}</div>
    </div>
    </div> --}}

        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2">{{ __('Advance Payment') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Advance Payment') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Manage Advance Payments') }}</h4>
                                @can('create advances')
                                    <div class="col-auto">
                                        <a href="#" data-size="xl" data-url="{{ route('advance_payement.create') }}"
                                            data-ajax-popup="true" data-title="{{ __('Pay New Advance') }}"
                                            class="btn btn-icon icon-left btn-primary">
                                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                            <span class="btn-inner--text"> {{ __('New Advance') }}</span>
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
                                                <table class="table table-flush" id="advance_payement_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th> {{ __('Advance #') }}</th>
                                                            <th> {{ __('Receipt') }}</th>
                                                            <th> {{ __('Customer') }}</th>
                                                            <th> {{ __('Phone No') }}</th>
                                                            <th> {{ __('Date') }}</th>
                                                            <th class="text-right"> {{ __('Advance Amount') }}
                                                                ({{ $currencySymbol }})</th>
                                                            <th> {{ __('Status') }}</th>
                                                            <th class="text-right"> {{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($advances as $advance)
                                                            <tr>
                                                                <td class="font-style"><a
                                                                        class="btn btn-outline-primary">{{ AUth::user()->advanceNumberFormat($advance->id) }}</a>
                                                                </td>
                                                                <td class="font-style">
                                                                    {{ $advance->receipts->receipt_code ?? '--' }} </td>
                                                                <td class="font-style">
                                                                    {{ $advance->customer->name ?? '' }} </td>
                                                                <td class="font-style">
                                                                    {{ $advance->customer->phone ?? '' }} </td>
                                                                <td class="font-style">
                                                                    {{ \Auth::user()->dateFormat($advance->advance_date) }}
                                                                </td>
                                                                <td class="font-style text-right">
                                                                    {{ number_format($advance->advance_amount ?? 0, 2) }}
                                                                    {{-- {{ \Auth::user()->priceFormat($advance->advance_amount) }} --}}
                                                                </td>
                                                                <td class="font-style">
                                                                    @if ($advance->status == 0)
                                                                        <span class="badge badge-info">Purchased</span>
                                                                    @elseif ($advance->status == 1)
                                                                        <span class="badge badge-success">Advanced</span>
                                                                    @elseif ($advance->status == 2)
                                                                        <span class="badge badge-danger">Returned</span>
                                                                    @endif
                                                                </td>
                                                                <td class="action text-right">
                                                                    @if ($advance->status == 0)
                                                                        @can('print receipt')
                                                                            <button type="button"
                                                                                class="btn btn-info btn-action print "
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Receipt Print') }}"
                                                                                onclick="updatePrintStatus({{ $advance->id }})"
                                                                                value="{{ $advance->receipt_id }}"><i
                                                                                    class="fas fa-print"></i>
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-info btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Receipt View') }}"
                                                                                onclick="window.location.href='{{ route('advance_payment.view', $advance->id) }}'">
                                                                                <i class="fas fa-eye"></i>
                                                                            </button>
                                                                        @endcan
                                                                    @elseif ($advance->status == 1)
                                                                        @can('edit advances')
                                                                            <a href="#"
                                                                                data-url="{{ route('advance_payement.edit', $advance->id) }}"
                                                                                data-ajax-popup="true" data-toggle="tooltip"
                                                                                data-title="{{ __('Edit Advance') }}"
                                                                                class="btn btn-primary btn-action mr-1">
                                                                                <i class="fas fa-pencil-alt mt-1"></i>
                                                                            </a>
                                                                        @endcan

                                                                        @if (Gate::check('create payment voucher') || Gate::check('return advances'))
                                                                            <a href="#" data-size="xl"
                                                                                data-url="{{ route('advance_payement.return', $advance->id) }}"
                                                                                data-ajax-popup="true" data-toggle="tooltip"
                                                                                data-title="{{ __('Return') }}"
                                                                                class="btn btn-danger btn-action"
                                                                                style="padding-top: 10px;">
                                                                                <i class="fas fa-undo-alt"></i>
                                                                            </a>
                                                                        @endif

                                                                        @can('delete advances')
                                                                            <a href="#" class="btn btn-danger btn-action"
                                                                                style="padding-top: 10px;" data-toggle="tooltip"
                                                                                data-original-title="{{ __('Delete') }}"
                                                                                data-confirm="{{ __('Are You Sure?') . ' | ' . __('This action can not be undone. Do you want to continue?') }}"
                                                                                data-confirm-yes="document.getElementById('delete-form-{{ $advance->id }}').submit();">
                                                                                <i class="fas fa-trash"></i>
                                                                            </a>
                                                                        @endcan

                                                                        @can('print receipt')
                                                                            <button type="button"
                                                                                class="btn btn-info btn-action print "
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Receipt Print') }}"
                                                                                onclick="updatePrintStatus({{ $advance->id }})"
                                                                                value="{{ $advance->receipt_id }}"><i
                                                                                    class="fas fa-print"></i>
                                                                            </button>

                                                                            <button type="button"
                                                                                class="btn btn-info btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Receipt View') }}"
                                                                                onclick="window.location.href='{{ route('advance_payment.view', $advance->id) }}'">
                                                                                <i class="fas fa-eye"></i>
                                                                            </button>
                                                                        @endcan
                                                                        {!! Form::open([
                                                                            'method' => 'DELETE',
                                                                            'route' => ['advance_payement.destroy', $advance->id],
                                                                            'id' => 'delete-form-' . $advance->id,
                                                                        ]) !!}
                                                                        {!! Form::close() !!}
                                                                    @elseif ($advance->status == 2)
                                                                        @can('print receipt')
                                                                            <button type="button"
                                                                                class="btn btn-info btn-action print "
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Receipt Print') }}"
                                                                                onclick="updatePrintStatus({{ $advance->id }})"
                                                                                value="{{ $advance->receipt_id }}"><i
                                                                                    class="fas fa-print"></i>
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-info btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Receipt View') }}"
                                                                                onclick="window.location.href='{{ route('advance_payment.view', $advance->id) }}'">
                                                                                <i class="fas fa-eye"></i>
                                                                            </button>
                                                                        @endcan
                                                                    @endif
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
@endsection
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
    // var advances = <?php echo json_encode($advances); ?>;
    $(document).ready(function() {
        // console.log(advances);
        if (localStorage.getItem("advance_payement_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("advance_payement_page_no"));
        }
        var advancePayementTable = $('#advance_payement_table').DataTable({
            order: [
                [6, "asc"]
            ]
        });
        advancePayementTable.page(pageNo).draw(false);

        $('#sales_table').on('page.dt', function() {
            var pageInfo = advancePayementTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("advance_payement_page_no", pageNo);
        });

        if (localStorage.getItem('newCustomerButtonClickedAdvance')) {
            setTimeout(function() {
                var $btn = $('a[data-url="{{ route('advance_payement.create') }}"]').first();
                if ($btn.length) {
                    $btn.trigger('click');
                }
            }, 300);
        }

        $('.view').click(function() {
            var advanceId = $(this).val();
            var url = "{{ url('advance_payement/receipt') }}/" + advanceId + "/view";
            window.open(url, '_blank');
        });

        $('.print').click(function() {
            var advanceId = $(this).val();
            var url = "{{ url('advance_payement/receipt') }}/" + advanceId + "/print";
            window.open(url, '_blank');
        });
    });

    function updatePrintStatus(advanceId) {
        // Send AJAX request to update print status
        fetch(`/advance-update-print-status/${advanceId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for Laravel
            },
            body: JSON.stringify({
                print_status: 1
            })
        }).then(response => {
            if (response.ok) {
                console.log('Print status updated to 1');
            } else {
                console.error('Failed to update print status');
            }
        }).catch(error => console.error('Error:', error));
    }
</script>
