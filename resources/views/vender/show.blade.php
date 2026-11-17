@extends('layouts.admin')
@push('script-page')
@endpush
@section('page-title')
    {{ __('Supplier-Detail') }}
@endsection
@section('content')
    <section class="section">
        {{-- <div class="section-header">
            <h1>{{__('Supplier')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item active"><a href="{{route('customer.index')}}">{{__('Supplier Info')}}</a></div>
        <div class="breadcrumb-item">{{$vendor->name}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Supplier') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('customer.index') }}">{{ __('Supplier Info') }}</a></div>
                                    <div class="breadcrumb-item">{{ $vendor->name }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4></h4>
                                <div class="card-header-action">
                                    {{-- @can('create bill')
                                    <a href="{{ route('bill.create',$vendor->id) }}" class="btn btn-sm btn-info lift">
                                <i class="fa fa-file"></i> {{__('Create Bill')}}
                                </a>
                                @endcan --}}
                                    @can('edit vender')
                                        <a href="#!" class="btn btn-primary mr-1" data-size="2xl"
                                            data-url="{{ route('vender.edit', $vendor['id']) }}" data-ajax-popup="true"
                                            data-title="{{ __('Edit Vendor') }}" data-toggle="tooltip"
                                            data-original-title="{{ __('Edit') }}">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    @endcan
                                    @if (Gate::check('delete vender') || Auth::user()->type == 'company' || Auth::user()->type == 'super admin')
                                        <button class="btn btn-danger mr-1 delete-vender"
                                            data-id="{{ $vendor['id'] }}"
                                            data-name="{{ $vendor['name'] }}"
                                            data-toggle="tooltip"
                                            data-original-title="{{ __('Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $vendor['id'] }}" action="{{ route('vender.destroy', $vendor['id']) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                    @if ($vendor->is_active == 1)
                                        @can('block vender')
                                            <button class="btn btn-danger btn-action block"
                                                data-original-title="{{ __('Block') }}" data-toggle="tooltip"
                                                value="{{ $vendor->id }}">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        @endcan
                                    @else($vendor->is_active=!1)
                                        @can('block vender')
                                            <button class="btn btn-success btn-action unblock" data-toggle="tooltip"
                                                data-original-title="{{ __('Unblock') }}" value="{{ $vendor->id }}">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row custmer-detail-wrap">
                                <div class="col-md-4">
                                    <h4 class="sub-title">{{ __('Supplier Info') }}</h4>
                                    <address>
                                        {{ $vendor->name }} <br>
                                        {{ $vendor->email }} <br>
                                        {{ $vendor->contact }} <br>
                                    </address>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="sub-title">{{ __('Billing Info') }}</h4>
                                            <address>
                                                {{ $vendor->billing_name }} <br>
                                                {{ $vendor->billing_phone }} <br>
                                                {{ $vendor->billing_address }} <br>
                                            </address>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="sub-title">{{ __('Contact Person Info') }}</h4>
                                            <address>

                                                @if ($vendor->contact_title == 1)
                                                    Ms.
                                                @elseif($vendor->contact_title == 2)
                                                    Mrs.
                                                @elseif($vendor->contact_title == 3)
                                                    Mr.
                                                @endif
                                                {{ $vendor->contact_name }} <br>
                                                {{ $vendor->contact_email }} <br>
                                                {{ $vendor->contact_phone }} <br>

                                            </address>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h4 class="sub-title">{{ __('Company Info') }}</h4>
                            <div class="row custmer-detail-wrap">
                                <div class="col-md-3">
                                    <div class="info">
                                        <strong>{{ __('Supplier Id') }}</strong>
                                        <span>{{ \Auth::user()->venderNumberFormat($vendor->vender_id) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info">
                                        <strong>{{ __('Date of Creation') }}</strong>
                                        <span>{{ \Auth::user()->dateFormat($vendor->created_at) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info font-style">
                                        <strong>{{ __('Balance') }}</strong>
                                        <span>{{ \Auth::user()->priceFormat($vendor->balance) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info">
                                        <strong>{{ __('Overdue') }}</strong>
                                        <span>{{ \Auth::user()->priceFormat($vendor->vendorOverdue($vendor->id)) }}</span>
                                    </div>
                                </div>
                                @php
                                    $totalBillSum = $vendor->vendorTotalBillSum($vendor['id']);
                                    $totalBill = $vendor->vendorTotalBill($vendor['id']);
                                    $averageSale = $totalBillSum != 0 ? $totalBillSum / $totalBill : 0;
                                @endphp
                                <div class="col-md-3">
                                    <div class="info">
                                        <strong>{{ __('Total Sum of Bills') }}</strong>
                                        <span>{{ \Auth::user()->priceFormat($totalBillSum) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info">
                                        <strong>{{ __('Quantity of Bills') }}</strong>
                                        <span>{{ $totalBill }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info">
                                        <strong>{{ __('Average Sales') }}</strong>
                                        <span>{{ \Auth::user()->priceFormat($averageSale) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <h4>{{ __('Bills') }}</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-flush" id="dataTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th> {{ __('Bill') }}</th>
                                        <th> {{ __('Bill Date') }}</th>
                                        <th> {{ __('Due Date') }}</th>
                                        <th> {{ __('Due Amount') }} ({{ $currencySymbol }})</th>
                                        <th>{{ __('Status') }}</th>
                                        @if (Gate::check('edit bill') ||
                                                Gate::check('delete bill') ||
                                                Gate::check('show
                                                                                                                    bill'))
                                            <th class="text-right"> {{ __('Action') }}</th>
                                        @endif
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($vendor->vendorBill($vendor->id) as $bill)
                                        <tr class="font-style">
                                            <td>
                                                @if (\Auth::guard('vender')->check())
                                                    <a class="btn btn-outline-primary"
                                                        href="{{ route('vender.bill.show', $bill->id) }}">{{ AUth::user()->billNumberFormat($bill->bill_id) }}
                                                    </a>
                                                @else
                                                    <a class="btn btn-outline-primary"
                                                        href="{{ route('bill.show', $bill->id) }}">{{ AUth::user()->billNumberFormat($bill->bill_id) }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ Auth::user()->dateFormat($bill->bill_date) }}</td>
                                            <td>
                                                @if ($bill->due_date < date('Y-m-d'))
                                                    <p class="text-danger">
                                                        {{ \Auth::user()->dateFormat($bill->due_date) }}</p>
                                                @else
                                                    {{ \Auth::user()->dateFormat($bill->due_date) }}
                                                @endif
                                            </td>
                                            <td>
                                                {{ number_format($bill->getDue()) }}
                                                {{-- {{ \Auth::user()->priceFormat($bill->getDue()) }} --}}
                                            </td>
                                            <td>
                                                @if ($bill->status == 0)
                                                    <span
                                                        class="badge badge-primary">{{ __(\App\Invoice::$statues[$bill->status]) }}</span>
                                                @elseif($bill->status == 1)
                                                    <span
                                                        class="badge badge-warning">{{ __(\App\Invoice::$statues[$bill->status]) }}</span>
                                                @elseif($bill->status == 2)
                                                    <span
                                                        class="badge badge-danger">{{ __(\App\Invoice::$statues[$bill->status]) }}</span>
                                                @elseif($bill->status == 3)
                                                    <span
                                                        class="badge badge-info">{{ __(\App\Invoice::$statues[$bill->status]) }}</span>
                                                @elseif($bill->status == 4)
                                                    <span
                                                        class="badge badge-success">{{ __(\App\Invoice::$statues[$bill->status]) }}</span>
                                                @endif
                                            </td>
                                            @if (Gate::check('edit bill') ||
                                                    Gate::check('delete bill') ||
                                                    Gate::check('show
                                                                                                                            bill'))
                                                <td class="action text-right">
                                                    @can('duplicate bill')
                                                        <a href="#" class="btn btn-success btn-action mr-1"
                                                            data-toggle="tooltip" data-original-title="{{ __('Duplicate') }}"
                                                            data-toggle="tooltip" data-original-title="{{ __('Delete') }}"
                                                            data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back"
                                                            data-confirm-yes="document.getElementById('duplicate-form-{{ $bill->id }}').submit();">
                                                            <i class="fas fa-copy"></i>
                                                            {!! Form::open([
                                                                'method' => 'get',
                                                                'route' => ['bill.duplicate', $bill->id],
                                                                'id' => 'duplicate-form-' . $bill->id,
                                                            ]) !!}
                                                            {!! Form::close() !!}
                                                        </a>
                                                    @endcan
                                                    @can('show bill')
                                                        @if (\Auth::guard('vender')->check())
                                                            <a href="{{ route('vender.bill.show', $bill->id) }}"
                                                                class="btn btn-primary btn-action mr-1" data-toggle="tooltip"
                                                                data-original-title="{{ __('Detail') }}">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('bill.show', $bill->id) }}"
                                                                class="btn btn-primary btn-action mr-1" data-toggle="tooltip"
                                                                data-original-title="{{ __('Detail') }}">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif
                                                    @endcan
                                                    @can('edit bill')
                                                        <a href="{{ route('bill.edit', $bill->id) }}"
                                                            class="btn btn-primary btn-action mr-1" data-toggle="tooltip"
                                                            data-original-title="{{ __('Edit') }}">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                    @endcan
                                                    @can('delete bill')
                                                        <a href="#!" class="btn btn-danger btn-action "
                                                            data-toggle="tooltip" data-original-title="{{ __('Delete') }}"
                                                            data-confirm="{{ __('Are You Sure?') . ' | ' . __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="document.getElementById('delete-form-{{ $bill->id }}').submit();">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['bill.destroy', $bill->id],
                                                            'id' => 'delete-form-' . $bill->id,
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


    </section>

    <script>
        $("body").on("click", ".delete", function() {
            var value = $(this).val();
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
                    window.location.replace("vender/unbloocktwo/" + value);
                }
            });

        });

        $("body").on("click", ".unblock", function() {
            var value = $(this).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to unblock this Supplier?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Continue!'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("unblock/" + value);
                }
            });

        });

        $("body").on("click", ".block", function() {
            var value = $(this).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to block this Supplier?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Continue!'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("block/" + value);
                }
            });

        });

        $("body").on("click", ".delete-vender", function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $(this).data('name') || 'this supplier';
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
    </script>
@endsection
