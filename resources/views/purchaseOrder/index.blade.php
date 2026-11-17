@extends('layouts.admin')
@section('page-title')
    {{ __('Purchase Orders') }}
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
            <h1>{{__('Purchase Orders')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Purchase Orders')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Purchase Orders') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Purchase Orders') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Manage Purchase Orders') }}</h4>
                                <div class="card-header-action">
                                    <div class="dropdown">
                                        {{-- <a href="#" data-toggle="dropdown" class="btn btn-icon icon-left btn-primary"><i class="fas fa-filter"></i>{{__('Filter')}}</a>
                                    --}}
                                        {{-- <div class="dropdown-menu dropdown-list dropdown-menu-right Filter-dropdown w-64  {{isset($_GET['vender'])?'show':''}}">
                                    @if (!\Auth::guard('vender')->check())
                                    {{ Form::open(array('route' => array('bill.index'),'method' => 'GET')) }}
                                    @else
                                    {{ Form::open(array('route' => array('vender.bill'),'method' => 'GET')) }}
                                    @endif
                                    @if (!\Auth::guard('vender')->check())
                                    <div class="form-group">
                                        {{ Form::label('vender', __('Vender')) }}
                                        {{ Form::select('vender',$vender,isset($_GET['vender'])?$_GET['vender']:'', array('class' => 'form-control font-style selectric')) }}
                                    </div>
                                    @endif
                                    <div class="form-group">
                                        {{ Form::label('bill_date', __('Date')) }}
                                        {{ Form::text('bill_date', isset($_GET['bill_date'])?$_GET['bill_date']:null, array('class' => 'form-control datepicker-range')) }}
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('status', __('Status')) }}
                                        {{ Form::select('status', [''=>'All'] + $status,isset($_GET['status'])?$_GET['status']:'', array('class' => 'form-control font-style selectric')) }}
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary">{{__('Search')}}</button>
                                        @if (!\Auth::guard('vender')->check())
                                        <a href="{{route('bill.index')}}" class="btn btn-danger">{{__('Reset')}}</a>
                                        @else
                                        <a href="{{route('vender.bill')}}" class="btn btn-danger">{{__('Reset')}}</a>
                                        @endif

                                    </div>
                                    {{ Form::close() }}
                                </div> --}}
                                    </div>
                                    @can('create purchase order')
                                        <a href="{{ route('purchaseOrder.create', 0) }}"
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
                                                <table class="table table-flush" id="purchase_order_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th> {{ __('Order') }}</th>
                                                            @if (!\Auth::guard('vender')->check())
                                                                <th> {{ __('Supplier') }}</th>
                                                            @endif
                                                            <th> {{ __('Order Date') }}</th>
                                                            <th> {{ __('Total Price') }} ({{ $currencySymbol }})</th>
                                                            <th>{{ __('Status') }}</th>
                                                            @if (Gate::check('edit purchase order') || Gate::check('delete purchase order') || Gate::check('view purchase order'))
                                                                <th class="text-right"> {{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($purchOrders as $purchOrder)
                                                            <tr class="font-style">
                                                                <td>
                                                                    {{-- @if (\Auth::guard('vender')->check())
                                                                    <a class="btn btn-outline-primary" href="{{ route('vender.bill.show',$purchOrder->id) }}">{{ AUth::user()->purchaseOrderNumberFormat($purchOrder->id) }}
                                                        </a>
                                                        @else --}}
                                                                    <a class="btn btn-outline-primary"
                                                                        href="{{ route('purchase-order.show', $purchOrder->id) }}">{{ AUth::user()->purchaseOrderNumberFormat($purchOrder->id) }}
                                                                    </a>
                                                                    {{-- @endif --}}
                                                                </td>
                                                                @if (!\Auth::guard('vender')->check())
                                                                    <td> {{ !empty($purchOrder->vender) ? $purchOrder->vender->name : '' }}
                                                                    </td>
                                                                @endif
                                                                <td>{{ Auth::user()->dateFormat($purchOrder->order_date) }}
                                                                </td>
                                                                <td>
                                                                    {{ number_format($purchOrder->total_amount ?? 0, 2) }}
                                                                    {{-- {{ Auth::user()->priceFormat($purchOrder->total_amount) }} --}}
                                                                </td>
                                                                {{-- <td>{{ Auth::user()->dateFormat($purchOrder->due_date) }}</td>
                                                    --}}
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
                                                                    @elseif($purchOrder->status == 4)
                                                                        <span class="badge badge-primary">Issued</span>
                                                                    @endif
                                                                </td>
                                                                @if (Gate::check('edit purchase order') ||
                                                                        Gate::check('delete
                                                                                                                                                                                                                                                                                                                                                                                    purchase order') ||
                                                                        Gate::check('view purchase order'))
                                                                    <td class="action text-right">
                                                                        {{-- @can('duplicate bill')
                                                                        <a href="#" class="btn btn-success btn-action mr-1" data-toggle="tooltip" data-original-title="{{__('Duplicate')}}"
                                                        data-toggle="tooltip" data-original-title="{{__('Delete')}}"
                                                        data-confirm="You want to confirm this action. Press Yes to
                                                        continue or Cancel to go back"
                                                        data-confirm-yes="document.getElementById('duplicate-form-{{$purchOrder->id}}').submit();">
                                                        <i class="fas fa-copy"></i>
                                                        {!! Form::open(['method' => 'get', 'route' => ['bill.duplicate',
                                                        $purchOrder->id],'id'=>'duplicate-form-'.$purchOrder->id]) !!}
                                                        {!! Form::close() !!}
                                                        </a>
                                                        @endcan --}}
                                                                        @can('view purchase order')
                                                                            {{-- @if (\Auth::guard('vender')->check())
                                                                            <a href="{{ route('vender.bill.show',$purchOrder->id) }}"
                                                        class="btn btn-primary btn-action mr-1" data-toggle="tooltip"
                                                        data-original-title="{{__('Detail')}}">
                                                        <i class="fas fa-eye"></i>
                                                        </a>
                                                        @else
                                                        <a href="{{ route('purchase-order.show',$purchOrder->id) }}"
                                                            class="btn btn-primary btn-action mr-1"
                                                            data-toggle="tooltip"
                                                            data-original-title="{{__('Detail')}}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @endif --}}
                                                                            <a href="{{ route('purchase-order.show', $purchOrder->id) }}"
                                                                                class="btn btn-primary btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Detail') }}">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                        @endcan
                                                                        @can('edit purchase order')
                                                                            @if ($purchOrder->status != 4)
                                                                                <a href="{{ route('purchase-order.edit', $purchOrder->id) }}"
                                                                                    class="btn btn-primary btn-action mr-1"
                                                                                    data-toggle="tooltip"
                                                                                    data-original-title="{{ __('Edit') }}">
                                                                                    <i class="fas fa-pencil-alt"></i>
                                                                                </a>
                                                                            @else
                                                                                <button type="button"
                                                                                    class="btn btn-primary btn-action mr-1"
                                                                                    disabled><i
                                                                                        class="fas fa-pencil-alt"></i></button>
                                                                            @endif
                                                                        @endcan
                                                                        @can('delete purchase order')
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
            if (localStorage.getItem("purchase_order_page_no") == null) {
                var pageNo = 0;
            } else {
                var pageNo = Number(localStorage.getItem("purchase_order_page_no"));
            }
            var purchaseOrderTable = $('#purchase_order_table').DataTable({
                order: [
                    [0, "desc"]
                ]
            });
            purchaseOrderTable.page(pageNo).draw(false);

            $('#purchase_order_table').on('page.dt', function() {
                var pageInfo = purchaseOrderTable.page.info();
                pageNo = pageInfo.page;
                localStorage.setItem("purchase_order_page_no", pageNo);
            });
        });
    </script>
@endsection
