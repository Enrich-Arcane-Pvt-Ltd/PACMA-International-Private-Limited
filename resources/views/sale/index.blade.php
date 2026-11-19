@extends('layouts.admin')
@section('page-title')
    {{ __('Cash Sales & Hire Sales') }}
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

    @if (
        \Auth::user()->type == 'Cash Collector' ||
            \Auth::user()->type == 'Branch Manager' ||
            \Auth::user()->type ==
                'Field
                    Manager' ||
            \Auth::user()->type == 'Center Manager')
        <style>
            .phone-col-div {
                padding-left: 0;
                padding-right: 0;
            }

            .phone-card-bd {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }
        </style>
    @endif

    <section class="section">
        <div class="se-pre-con"></div>

        {{-- <div class="section-header">
        <h1>{{__('Cash Sales & Hire Sales')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Cash Sales & Hire Sales')}}</div>
    </div>
    </div> --}}

        <div class="section-body">
            <div class="row justify-content-center">


                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2">{{ __('Cash Sales & Hire Sales') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Cash Sales & Hire Sales') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Manage Cash Sales & Hire Sales') }}</h4>
                                @can('create sale')
                                    <div class="col-auto">
                                        <a href="#" data-size="2xl" data-url="{{ route('sale.create') }}"
                                            data-ajax-popup="true" data-title="{{ __('Create New Hire Sales') }}"
                                            class="btn btn-icon icon-left btn-primary">
                                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                            <span class="btn-inner--text"> {{ __('Hire Sales') }}</span>
                                        </a>
                                        <a href="#" data-size="2xl" data-url="{{ route('direct_sale.create') }}"
                                            data-ajax-popup="true" data-title="{{ __('Create New Cash Sales') }}"
                                            class="btn btn-icon icon-left btn-primary">
                                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                            <span class="btn-inner--text"> {{ __('Cash Sales') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body phone-card-bd">
                            <div class="card-body p-0">
                                <div id="table-1_wrapper"
                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-flush" id="sales_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th> {{ __('Sale #') }}</th>
                                                            <th> {{ __('Invoice #') }}</th>
                                                            <th> {{ __('Date Of Sale') }}</th>
                                                            <th style="width: 10%"> {{ __('Customer') }}</th>
                                                            <th> {{ __('NIC') }}</th>
                                                            <th> {{ __('Sale Type') }}</th>
                                                            <th class="text-center"> {{ __('Status') }}</th>
                                                            @if (Gate::check('view sale') || Gate::check('edit sale') || Gate::check('delete sale'))
                                                                <th class="text-right"> {{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($sales as $sale)
                                                            <tr>
                                                                {{-- <td class="font-style">{{ $sale->sale_id }}</td> --}}
                                                                <td class="font-style"><button type="button"
                                                                        class="btn btn-outline-primary view"
                                                                        value="{{ $sale->id }}"> {{ $sale->sale_id }}
                                                                    </button>
                                                                </td>
                                                                <td class="font-style"><button type="button"
                                                                        class="btn btn-outline-primary invoice-view"
                                                                        value="{{ $sale->invoice_id }}">
                                                                        {{ $sale->invoice_code }} </button></td>
                                                                <td class="font-style">
                                                                    {{ Auth::user()->dateFormat($sale->date_of_sale) }}
                                                                </td>
                                                                <td class="font-style">
                                                                    @if ($sale->customer_type == 1)
                                                                        {{ $sale->customer->name ?? '' }}
                                                                    @elseif ($sale->customer_type == 2)
                                                                        {{ $sale->customer_name }}
                                                                    @endif
                                                                </td>
                                                                <td class="font-style">
                                                                    @if ($sale->customer_type == 1)
                                                                        {{ $sale->customer->nic_no ?? '' }}
                                                                    @elseif ($sale->customer_type == 2)
                                                                        {{ $sale->customer_nic ?? '' }}
                                                                    @endif
                                                                </td>
                                                                <td class="font-style text-left">
                                                                    @if ($sale->sale_type == 0)
                                                                        Cash Sales
                                                                    @else
                                                                        Hire Sales
                                                                    @endif
                                                                </td>
                                                                <td class="font-style text-center">
                                                                    @if ($sale->status == 1)
                                                                        <span class="badge badge-success">Paid</span>
                                                                        {{-- @elseif ($sale->status == 0)
                                                                <span class="badge badge-primary" >extra</span> --}}
                                                                    @elseif ($sale->status == 2)
                                                                        <span class="badge badge-warning">Partially
                                                                            Paid</span>
                                                                    @elseif ($sale->status == 3)
                                                                        <span class="badge badge-danger">Overdue</span>
                                                                    @elseif ($sale->status == 4)
                                                                        <span class="badge badge-dark">Returned</span>
                                                                    @endif
                                                                    @if ((int) $sale->status !== 4 && \App\ItemReturn::where('invoice_id', $sale->invoice_id)->where('status', 1)->exists())
                                                                        <small class="text-danger d-block">Partial Return</small>
                                                                    @endif
                                                                </td>
                                                                @if (Gate::check('view sale') || Gate::check('edit sale') || Gate::check('delete sale'))
                                                                    <td class="action text-right">
                                                                        @can('view sale')
                                                                            <button type="button"
                                                                                class="btn btn-info btn-action mr-1 view"
                                                                                title="View" value="{{ $sale->id }}"><i
                                                                                    class="fas fa-eye"></i></button>
                                                                        @endcan

                                                                        {{-- @can('edit sale')
                                                                            <a href="#" data-size="2xl"
                                                                                class="btn btn-primary btn-action mr-1"
                                                                                style="padding-top: 10px;"
                                                                                data-url="{{ route('sale.edit', $sale->id) }}"
                                                                                data-ajax-popup="true"
                                                                                @if ($sale->sale_type == 0) data-title="{{ __('Edit Cash Sales') }}"
                                                                @else
                                                                data-title="{{ __('Edit Hire Sales') }}" @endif
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Edit') }}">
                                                                                <i class="fas fa-pencil-alt"></i>
                                                                            </a>
                                                                        @endcan --}}

                                                                        @can('delete sale')
                                                                            <a href="#" class="btn btn-danger btn-action"
                                                                                style="padding-top: 10px;" data-toggle="tooltip"
                                                                                data-original-title="{{ __('Delete') }}"
                                                                                data-confirm="{{ __('Are You Sure?') . ' | ' . __('This action can not be undone. Do you want to continue?') }}"
                                                                                data-confirm-yes="document.getElementById('delete-form-{{ $sale->id }}').submit();">
                                                                                <i class="fas fa-trash"></i>
                                                                            </a>
                                                                            {!! Form::open([
                                                                                'method' => 'DELETE',
                                                                                'route' => ['sale.destroy', $sale->id],
                                                                                'id' => 'delete-form-' . $sale->id,
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
                                            <div class="col-md-12 mt-2">
                                                <div style="float: right">
                                                    {{ $sales->onEachSide(5)->links() }}
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

        </div>

    </section>
@endsection
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
    // var sales = <?php echo json_encode($sales); ?>;
    $(document).ready(function() {
        $(".se-pre-con").fadeOut('slow');
        // console.log(sales);
        if (localStorage.getItem("sale_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("sale_page_no"));
        }
        var salesTable = $('#sales_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        salesTable.page(pageNo).draw(false);

        $('#sales_table').on('page.dt', function() {
            var pageInfo = salesTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("sale_page_no", pageNo);
        });

        if (localStorage.getItem('newCustomerButtonClicked')) {
            setTimeout(function() {
                var $btn = $('a[data-url="{{ route('sale.create') }}"]').first();
                if ($btn.length) {
                    $btn.trigger('click');
                }
            }, 300);
        }

        $('body').on('click', '.edit', function() {
            var value = $(this).val();
            window.location.href = "{{ url('sale/view') }}/" + value;
        });

        $('body').on('click', '.view', function() {
            var value = $(this).val();
            window.location.href = "{{ url('sale/view') }}/" + value;
        });

        $('body').on('click', '.invoice-view', function() {
            var invoiceId = $(this).val();
            window.location.href = "{{ url('invoice') }}/" + invoiceId;
        });
    });
</script>
