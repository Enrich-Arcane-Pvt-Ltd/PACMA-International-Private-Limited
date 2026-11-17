@extends('layouts.admin')

@section('page-title')
    {{ __('GRN') }}
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

        .tab-view ul {
            list-style-type: none;
            display: flex;
            justify-content: space-between;
            padding-left: 0rem;
        }

        .tab-view ul li {
            display: flex;
            align-items: center;
            justify-content: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            border-bottom: 1px solid #F97316;
            padding: 0.5rem 1rem;
            width: 33.33%;
            text-align: center;
        }

        .tab-view ul li a {
            text-decoration: none;
            color: #EA580C;
            width: 100%;
        }

        .bg-primary {
            background-color: #F97316 !important;
        }

        .bg-primary a {
            color: #ffffff !important;
        }
    </style>

    <section class="section">
        <div class="se-pre-con"></div>

        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('GRN') }}</h4>

                            <div class="section-header-breadcrumb d-flex">
                                <div class="breadcrumb-item active">
                                    <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                                </div>

                                <div class="breadcrumb-item">
                                    {{ __('GRN') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="w-100 tab-view">
                                <ul>
                                    <li class="bg-primary">
                                        <a href="{{ route('purchase-orders.create-grn') }}">
                                            {{ __('Create GRN From Purchase Orders') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('grn.manual.create') }}">
                                            {{ __('Create Manual GRN') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('grn.index') }}">
                                            {{ __('Manage GRN') }}
                                        </a>
                                    </li>
                                </ul>
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
                                                            <th>{{ __('Order') }}</th>
                                                            @if (!\Auth::guard('vender')->check())
                                                                <th>{{ __('Supplier') }}</th>
                                                            @endif
                                                            <th>{{ __('Order Date') }}</th>
                                                            <th>{{ __('Total Price') }} ({{ $currencySymbol }})</th>
                                                            <th>{{ __('Status') }}</th>
                                                            @if (Gate::check('edit bill') || Gate::check('delete bill') || Gate::check('show bill'))
                                                                <th class="text-right"> {{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($purchOrders as $purchOrder)
                                                            <tr class="font-style">
                                                                <td>
                                                                    <a class="btn btn-outline-primary"
                                                                        href="{{ route('purchase-order.grn', $purchOrder->id) }}">
                                                                        {{ AUth::user()->purchaseOrderNumberFormat($purchOrder->id) }}
                                                                    </a>
                                                                </td>
                                                                @if (!\Auth::guard('vender')->check())
                                                                    <td>
                                                                        {{ !empty($purchOrder->vender) ? $purchOrder->vender->name : '' }}
                                                                    </td>
                                                                @endif
                                                                <td>
                                                                    {{ Auth::user()->dateFormat($purchOrder->order_date) }}
                                                                </td>
                                                                <td>
                                                                    {{ number_format($purchOrder->total_amount ?? 0, 2) }}
                                                                    {{-- {{ Auth::user()->priceFormat($purchOrder->total_amount) }} --}}
                                                                </td>
                                                                <td>
                                                                    @if ($purchOrder->status == 1)
                                                                        <span class="badge badge-warning">Cancelled</span>
                                                                    @elseif($purchOrder->status == 0)
                                                                        <span class="badge badge-danger">Rejected</span>
                                                                    @elseif($purchOrder->status == 2)
                                                                        <span class="badge badge-info">Not Yet
                                                                            Approved</span>
                                                                    @elseif($purchOrder->status == 3)
                                                                        <span class="badge badge-success">Approved</span>
                                                                    @endif
                                                                </td>
                                                                @if (Gate::check('edit bill') || Gate::check('delete bill') || Gate::check('show bill'))
                                                                    <td class="action text-right">
                                                                        @can('show bill')
                                                                            <a href="{{ route('purchase-order.show', $purchOrder->id) }}"
                                                                                class="btn btn-primary btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Detail') }}">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                        @endcan
                                                                        @can('edit bill')
                                                                            <a href="{{ route('purchase-order.edit', $purchOrder->id) }}"
                                                                                class="btn btn-primary btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Edit') }}">
                                                                                <i class="fas fa-pencil-alt"></i>
                                                                            </a>
                                                                        @endcan
                                                                        @can('delete bill')
                                                                            <a href="#!" class="btn btn-danger btn-action "
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Delete') }}"
                                                                                data-confirm="{{ __('Are You Sure?') . ' | ' . __('This action can not be undone. Do you want to continue?') }}"
                                                                                data-confirm-yes="document.getElementById('delete-form-{{ $purchOrder->id }}').submit();">
                                                                                <i class="fas fa-trash"></i>
                                                                            </a>
                                                                            {!! Form::open([
                                                                                'method' => 'DELETE',
                                                                                'route' => ['purchase-order.destroy', $purchOrder->id],
                                                                                'id' => 'delete-form-' . $purchOrder->id,
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
        });
    </script>
@endsection
