@extends('layouts.admin')
@push('script-page')
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
@endpush

@section('page-title')
    {{ __('Customers') }}
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
        <script>
            (function() {
                function hideLoader() {
                    var l = document.querySelector('.se-pre-con');
                    if (l) l.style.display = 'none';
                }
                if (document.readyState === 'complete' || document.readyState === 'interactive') {
                    setTimeout(hideLoader, 200);
                } else {
                    document.addEventListener('DOMContentLoaded', hideLoader);
                }
                window.addEventListener('load', hideLoader);
                setTimeout(hideLoader, 1000);
            })();
        </script>

        {{-- <div class="section-header">
            <h1>{{ __('Customers') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Customers') }}</div>
    </div>
    </div> --}}

        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Customers') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Customers') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">

                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Manage Customers') }}</h4>
                                <div class="card-header-action">
                                    @can('create customer')
                                        <a href="#" data-size="xl" data-url="{{ route('customer.create') }}"
                                            data-ajax-popup="true" data-title="{{ __('Create New Customer') }}"
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

                                                <table class="table table-flush" id="customers_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th> {{ __('Customer #') }}</th>
                                                            <th> {{ __('Name') }}</th>
                                                            <th> {{ __('Address') }}</th>
                                                            <th> {{ __('Phone') }}</th>
                                                            <th> {{ __('Email') }}</th>
                                                            <th> {{ __('NIC No') }}</th>
                                                            <th class="text-center"> {{ __('Type') }}</th>
                                                            @if (Gate::check('edit customer') ||
                                                                    Gate::check('delete customer') ||
                                                                    Gate::check('block customer') ||
                                                                    Gate::check('show customer'))
                                                                <th class="text-center">{{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($customers as $k => $customer)
                                                            <tr class="main_row" id="customer_detail"
                                                                data-url="{{ route('customer.show', $customer['id']) }}"
                                                                data-id="{{ $customer['id'] }}">
                                                                <td>
                                                                    <a href="{{ route('customer.show', $customer['id']) }}"
                                                                        class="btn btn-outline-primary">
                                                                        {{ AUth::user()->customerNumberFormat($customer['customer_id']) }}
                                                                    </a>
                                                                </td>
                                                                <td class="customer_td">
                                                                    @if ($customer['title'] == 0)
                                                                        Mr.
                                                                    @elseif($customer['title'] == 1)
                                                                        Mrs.
                                                                    @else
                                                                        Miss.
                                                                    @endif
                                                                    {{ $customer['name'] }}
                                                                </td>
                                                                <td class="customer_td">{{ $customer['house_no'] ?? '' }}
                                                                    {{ $customer['address'] ?? '' }}</td>
                                                                <td class="customer_td">{{ $customer['phone_mobile'] }}
                                                                </td>
                                                                <td class="customer_td">
                                                                    @if ($customer['email'] != '')
                                                                        {{ $customer['email'] }}
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </td>
                                                                <td class="customer_td">{{ $customer['nic_no'] }}</td>
                                                                <td class="text-center customer_td">
                                                                    @if ($customer['type'] == 0)
                                                                        <span
                                                                            class="badge badge-pill badge-success">Customer</span>
                                                                    @elseif($customer['type'] == 1)
                                                                        <span
                                                                            class="badge badge-pill badge-info">Guarantor</span>
                                                                    @else
                                                                        <span
                                                                            class="badge badge-pill badge-danger">Both</span>
                                                                    @endif
                                                                </td>
                                                                @if (Gate::check('edit customer') ||
                                                                        Gate::check('delete
                                                                                                                                                                                                                                                        customer') ||
                                                                        Gate::check('block customer') ||
                                                                        Gate::check('show
                                                                                                                                                                                                                                                        customer'))
                                                                    <td class="text-center" style="white-space: nowrap;">
                                                                        @can('show customer')
                                                                            <a href="{{ route('customer.show', $customer['id']) }}"
                                                                                class="btn btn-info btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('View') }}">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                            <a href="{{ route('pacma.forms.customer_application', $customer['id']) }}"
                                                                                target="_blank"
                                                                                class="btn btn-primary btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Application Form') }}">
                                                                                <i class="fas fa-file-alt"></i>
                                                                            </a>
                                                                        @endcan

                                                                        @can('edit customer')
                                                                            <a href="#"
                                                                                class="btn btn-primary btn-action mr-1"
                                                                                data-size="xl"
                                                                                data-url="{{ route('customer.edit', $customer['id']) }}"
                                                                                data-ajax-popup="true"
                                                                                data-title="{{ __('Edit Customer') }}"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Edit') }}">
                                                                                <i class="fas fa-pencil-alt"></i>
                                                                            </a>
                                                                        @endcan

                                                                        @if ($customer->status == 1)
                                                                            @can('block customer')
                                                                                <button
                                                                                    class="btn btn-danger btn-action mr-1 block"
                                                                                    data-original-title="{{ __('Block') }}"
                                                                                    data-toggle="tooltip"
                                                                                    value="{{ $customer->id }}">
                                                                                    <i class="fas fa-lock"></i>
                                                                                </button>
                                                                            @endcan
                                                                        @else
                                                                            @can('block customer')
                                                                                <button
                                                                                    class="btn btn-success btn-action mr-1 unblock"
                                                                                    data-toggle="tooltip"
                                                                                    data-original-title="{{ __('Unblock') }}"
                                                                                    value="{{ $customer->id }}">
                                                                                    <i class="fas fa-unlock"></i>
                                                                                </button>
                                                                            @endcan
                                                                        @endif

                                                                        @if (Gate::check('delete customer') || Auth::user()->type == 'company' || Auth::user()->type == 'super admin')
                                                                            <button class="btn btn-danger btn-action mr-1 delete-customer"
                                                                                data-id="{{ $customer['id'] }}"
                                                                                data-name="{{ $customer['name'] }}"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Delete') }}">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                            <form id="delete-form-{{ $customer['id'] }}" action="{{ route('customer.destroy', $customer['id']) }}" method="POST" style="display: none;">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                            </form>
                                                                        @endif
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
@endsection

@push('script-page')
    <script>
        function hideCustomerLoader() {
            $(".se-pre-con").fadeOut('slow');
        }
        $(document).ready(function() {
            hideCustomerLoader();
            if (localStorage.getItem("customer_page_no") == null) {
                var pageNo = 0;
            } else {
                var pageNo = Number(localStorage.getItem("customer_page_no"));
            }

            var customersTable = $('#customers_table').DataTable({
                order: [
                    [0, "desc"]
                ]
            });
            customersTable.page(pageNo).draw(false);

            $('#customers_table').on('page.dt', function() {
                var pageInfo = customersTable.page.info();
                pageNo = pageInfo.page;
                localStorage.setItem("customer_page_no", pageNo);
            });
        });

        $(window).on('load', function() {
            hideCustomerLoader();
        });
        setTimeout(hideCustomerLoader, 800);

        $('body').on('click', '.customer_td', function() {
            var customerId = $(this).closest('tr').attr("data-id");
            window.location.href = "{{ url('customer') }}/" + customerId;
        });

        $("body").on("click", ".delete-customer", function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $(this).data('name') || 'this customer';
            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("Are you sure you want to delete") }} "' + name + '"? {{ __("This action cannot be undone.") }}',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#fc544b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __("Yes, delete it!") }}'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });

        $("body").on("click", ".block", function() {
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
                    window.location.replace("customer/block/" + value);
                }
            });
        });

        $("body").on("click", ".unblock", function() {
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
                    window.location.replace("customer/unblock/" + value);
                }
            });
        });
    </script>
@endpush
