@extends('layouts.admin')
@section('page-title')
{{__('Autherization Requests')}}
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
        <h1>{{__('Autherization Requests')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Autherization Requests')}}</div>
    </div>
    </div> --}}

    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2">{{__('Autherization Requests')}}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Autherization Requests')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Requests')}}</h4>
                            {{-- @can('create sale')
                            <div class="col-auto">
                                <a href="#" data-size="xl" data-url="{{ route('sale.create') }}" data-ajax-popup="true"
                            data-title="{{__('Create New Sale')}}" class="btn btn-icon icon-left btn-primary">
                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                            <span class="btn-inner--text"> {{__('Create')}}</span>
                            </a>
                            <a href="#" data-size="2xl" data-url="{{ route('direct_sale.create') }}"
                                data-ajax-popup="true" data-title="{{__('Create New Cash Sales')}}"
                                class="btn btn-icon icon-left btn-primary">
                                <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                <span class="btn-inner--text"> {{__('Cash Sales')}}</span>
                            </a>
                        </div>
                        @endcan --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-body p-0">
                        <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-flush" id="requests_table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th> {{__('#')}}</th>
                                                    <th> {{__('Category')}}</th>
                                                    <th> {{__('Date')}}</th>
                                                    <th> {{__('Customer')}}</th>
                                                    <th> {{__('Created By')}}</th>
                                                    {{-- <th> {{__('Status')}}</th> --}}
                                                    @if(Gate::check('approve authorization'))
                                                    <th class="text-right"> {{__('Action')}}</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sales as $sale)
                                                <tr>
                                                    {{-- <td class="font-style">{{ $sale->sale_id }}</td> --}}
                                                    <td class="font-style">
                                                        {{ Auth::user()->waittingSaleNumberFormat($sale->id) }}</td>
                                                    <td class="font-style">
                                                        @if ($sale->sale_type == 0)
                                                        Cash Sales
                                                        @else
                                                        Hire Sales
                                                        @endif
                                                    </td>
                                                    <td class="font-style">
                                                        {{ Auth::user()->dateFormat($sale->date_of_sale) }}</td>
                                                    <td class="font-style"> @if ($sale->customer_type == 1)
                                                        {{ $sale->customer->name ?? '' }}
                                                        @elseif ($sale->customer_type == 2)
                                                        {{ $sale->customer_name }}
                                                        @endif </td>

                                                    <td class="font-style text-center">
                                                        {{ Auth::user()->otherUserName($sale->created_by) }}</td>
                                                    @if(Gate::check('approve authorization'))
                                                    <td class="action text-right">
                                                        {{-- @can('view sale') --}}
                                                        {{-- <button type="button" class="btn btn-info btn-action mr-1 view" title="View" value="{{ $sale->id }}"><i
                                                            class="fas fa-eye"></i></button> --}}
                                                        {{-- <a href="#" class="btn btn-info btn-action mr-1" style="padding-top: 10px;" data-url="{{ route('sale.view',$sale->id) }}"
                                                        data-ajax-popup="true" data-title="{{__('View Sale')}}"
                                                        data-toggle="tooltip" data-original-title="{{__('View')}}">
                                                        <i class="fas fa-eye"></i>
                                                        </a> --}}
                                                        {{-- @endcan --}}

                                                        @can('approve authorization')
                                                        <a href="#" data-size="xl"
                                                            class="btn btn-primary btn-action mr-1"
                                                            style="padding-top: 10px;"
                                                            data-url="{{ route('waitting_sale.approve',$sale->id) }}"
                                                            data-ajax-popup="true" data-title="{{__('Approve Sale')}}"
                                                            data-toggle="tooltip"
                                                            data-original-title="{{__('Appove')}}">
                                                            <i class="fas fa-check" style="padding-top: 5px;"></i>
                                                        </a>
                                                        @endcan

                                                        {{-- @can('delete sale')
                                                                        <a href="#" class="btn btn-danger btn-action" style="padding-top: 10px;" data-toggle="tooltip" data-original-title="{{__('Delete')}}"
                                                        data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{$sale->id}}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                        </a>
                                                        {!! Form::open(['method' => 'DELETE', 'route' =>
                                                        ['sale.destroy', $sale->id],'id'=>'delete-form-'.$sale->id]) !!}
                                                        {!! Form::close() !!}
                                                        @endcan --}}
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                @foreach ($installments as $installment)
                                                <tr>
                                                    {{-- <td class="font-style">{{ $sale->sale_id }}</td> --}}
                                                    <td class="font-style">
                                                        {{ Auth::user()->waittingSaleInstallmentNumberFormat($installment->id) }}
                                                    </td>
                                                    <td class="font-style"> Hire Sales Installment </td>
                                                    <td class="font-style">
                                                        {{ Auth::user()->dateFormat($installment->date) }}</td>
                                                    <td class="font-style"> {{ $installment->customer->name ?? '' }}
                                                    </td>

                                                    <td class="font-style text-center">
                                                        {{ Auth::user()->otherUserName($installment->created_by) }}</td>
                                                    @if(Gate::check('approve authorization'))
                                                    <td class="action text-right">
                                                        @can('approve authorization')
                                                        <a href="#" data-size="xl"
                                                            class="btn btn-primary btn-action mr-1"
                                                            style="padding-top: 10px;"
                                                            data-url="{{ route('waitting_installment.approve',$installment->id) }}"
                                                            data-ajax-popup="true"
                                                            data-title="{{__('Approve Hire Sales Installment')}}"
                                                            data-toggle="tooltip"
                                                            data-original-title="{{__('Appove')}}">
                                                            <i class="fas fa-check" style="padding-top: 5px;"></i>
                                                        </a>
                                                        @endcan
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                @foreach ($loanInstallments as $loanInstallment)
                                                <tr>
                                                    {{-- <td class="font-style">{{ $sale->sale_id }}</td> --}}
                                                    <td class="font-style">
                                                        {{ Auth::user()->waittingLoanInstallmentNumberFormat($loanInstallment->id) }}
                                                    </td>
                                                    <td class="font-style"> Loan Installment </td>
                                                    <td class="font-style">
                                                        {{ Auth::user()->dateFormat($loanInstallment->date) }}</td>
                                                    <td class="font-style"> {{ $loanInstallment->customer->name ?? '' }}
                                                    </td>

                                                    <td class="font-style text-center">
                                                        {{ Auth::user()->otherUserName($loanInstallment->created_by) }}
                                                    </td>
                                                    @if(Gate::check('approve authorization'))
                                                    <td class="action text-right">
                                                        @can('approve authorization')
                                                        <a href="#" data-size="xl"
                                                            class="btn btn-primary btn-action mr-1"
                                                            style="padding-top: 10px;"
                                                            data-url="{{ route('waitting_loan_installment.approve',$loanInstallment->id) }}"
                                                            data-ajax-popup="true"
                                                            data-title="{{__('Approve Loan Installment')}}"
                                                            data-toggle="tooltip"
                                                            data-original-title="{{__('Appove')}}">
                                                            <i class="fas fa-check" style="padding-top: 5px;"></i>
                                                        </a>
                                                        @endcan
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                @foreach ($pawningInstallments as $pawningInstallment)
                                                <tr>
                                                    {{-- <td class="font-style">{{ $sale->sale_id }}</td> --}}
                                                    <td class="font-style">
                                                        {{ Auth::user()->waittingPawningInstallmentNumberFormat($pawningInstallment->id) }}
                                                    </td>
                                                    <td class="font-style"> Pawning Installment </td>
                                                    <td class="font-style">
                                                        {{ Auth::user()->dateFormat($pawningInstallment->paid_date) }}
                                                    </td>
                                                    <td class="font-style">
                                                        {{ $pawningInstallment->customer->name ?? '' }} </td>

                                                    <td class="font-style text-center">
                                                        {{ Auth::user()->otherUserName($pawningInstallment->created_by) }}
                                                    </td>
                                                    @if(Gate::check('approve authorization'))
                                                    <td class="action text-right">
                                                        @can('approve authorization')
                                                        <a href="#" data-size="xl"
                                                            class="btn btn-primary btn-action mr-1"
                                                            style="padding-top: 10px;"
                                                            data-url="{{ route('waitting_pawning_installment.approve',$pawningInstallment->id) }}"
                                                            data-ajax-popup="true"
                                                            data-title="{{__('Approve Pawning Installment')}}"
                                                            data-toggle="tooltip"
                                                            data-original-title="{{__('Appove')}}">
                                                            <i class="fas fa-check" style="padding-top: 5px;"></i>
                                                        </a>
                                                        @endcan
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                @foreach ($itemReturns ?? [] as $itemReturn)
                                                <tr>
                                                    <td class="font-style">
                                                        <a href="#" class="text-primary font-weight-bold" data-size="2xl" data-url="{{ route('item_return.edit', $itemReturn->id) }}" data-ajax-popup="true" data-title="{{ __('Item Return Details') }} — {{ Auth::user()->itemReturnNumberFormat($itemReturn->id) }}">
                                                            {{ Auth::user()->itemReturnNumberFormat($itemReturn->id) }}
                                                        </a>
                                                    </td>
                                                    <td class="font-style">
                                                        @if (($itemReturn->return_type ?? 'customer') === 'supplier')
                                                            <span class="badge badge-warning">{{ __('Supplier Return') }}</span>
                                                        @else
                                                            <span class="badge badge-info">{{ __('Customer Return') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="font-style">{{ $itemReturn->date }}</td>
                                                    <td class="font-style">
                                                        @if (($itemReturn->return_type ?? 'customer') === 'supplier')
                                                            {{ optional($itemReturn->vender)->name ?? '-' }}
                                                        @else
                                                            {{ optional($itemReturn->invoice)->invoice_code ?? '-' }}
                                                        @endif
                                                        — {{ optional($itemReturn->product)->name ?? '' }} ({{ number_format((float) $itemReturn->amount, 2) }})
                                                    </td>
                                                    <td class="font-style">{{ optional(\App\User::find($itemReturn->created_by))->name ?? '-' }}</td>
                                                    @if(Gate::check('approve authorization'))
                                                    <td class="Action text-right" style="white-space: nowrap;">
                                                        <form method="POST" action="{{ route('item_return.approve', $itemReturn->id) }}" class="d-inline">
                                                            @csrf
                                                            <button type="button" class="btn btn-success btn-action mr-1 btn-approve-confirm" data-title="{{ __('Approve Item Return?') }}" data-text="{{ __('Are you sure you want to approve this Item Return?') }}" data-toggle="tooltip" data-original-title="{{ __('Approve') }}">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('item_return.reject', $itemReturn->id) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="rejection_reason" value="">
                                                            <button type="button" class="btn btn-danger btn-action btn-reject-auth-return" data-toggle="tooltip" data-original-title="{{ __('Reject') }}">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                @foreach ($pendingGrns ?? [] as $grn)
                                                <tr>
                                                    <td class="font-style">
                                                        <a href="{{ route('grn.show', $grn->id) }}" class="text-primary font-weight-bold" target="_blank" rel="noopener">
                                                            {{ Auth::user()->grnNumberFormat($grn->id) }}
                                                        </a>
                                                    </td>
                                                    <td class="font-style">
                                                        <span class="badge badge-primary">{{ __('GRN') }}</span>
                                                    </td>
                                                    <td class="font-style">{{ $grn->order_date }}</td>
                                                    <td class="font-style">
                                                        {{ optional($grn->vender)->name ?? '-' }}
                                                        @if (!empty($grn->supplier_invoice_number))
                                                            — {{ $grn->supplier_invoice_number }}
                                                        @endif
                                                        ({{ number_format((float) $grn->total_amount, 2) }})
                                                    </td>
                                                    <td class="font-style">{{ optional(\App\User::find($grn->created_by))->name ?? '-' }}</td>
                                                    @if(Gate::check('approve authorization'))
                                                    <td class="Action text-right" style="white-space: nowrap;">
                                                        <form method="POST" action="{{ route('grn.approve_ho', $grn->id) }}" class="d-inline">
                                                            @csrf
                                                            <button type="button" class="btn btn-success btn-action mr-1 btn-approve-confirm" data-title="{{ __('Approve GRN?') }}" data-text="{{ __('Are you sure you want to approve this GRN?') }}" data-toggle="tooltip" data-original-title="{{ __('Approve') }}">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('grn.reject_ho', $grn->id) }}" class="d-inline">
                                                            @csrf
                                                            <button type="button" class="btn btn-danger btn-action btn-reject-confirm" data-title="{{ __('Reject GRN?') }}" data-text="{{ __('Are you sure you want to reject this GRN?') }}" data-toggle="tooltip" data-original-title="{{ __('Reject') }}">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                @foreach ($pendingStockAdjustments ?? [] as $adj)
                                                <tr>
                                                    <td class="font-style">
                                                        <a href="{{ route('stock.batch', $adj->inventory_id) }}" class="text-primary font-weight-bold" target="_blank" rel="noopener">
                                                            SA{{ str_pad($adj->id, 5, '0', STR_PAD_LEFT) }}
                                                        </a>
                                                    </td>
                                                    <td class="font-style">
                                                        <span class="badge {{ $adj->action === 'add' ? 'badge-success' : 'badge-warning' }}">
                                                            {{ $adj->action === 'add' ? __('Stock Add') : __('Stock Deduct') }}
                                                        </span>
                                                    </td>
                                                    <td class="font-style">{{ $adj->date }}</td>
                                                    <td class="font-style">
                                                        {{ optional(optional($adj->inventory)->products)->name ?? ('Inventory #' . $adj->inventory_id) }}
                                                        — Qty: <strong>{{ $adj->quantity }}</strong>
                                                        @if (!empty($adj->description))
                                                            ({{ $adj->description }})
                                                        @endif
                                                    </td>
                                                    <td class="font-style">{{ optional(\App\User::find($adj->created_by))->name ?? '-' }}</td>
                                                    @if(Gate::check('approve authorization'))
                                                    <td class="Action text-right" style="white-space: nowrap;">
                                                        <form method="POST" action="{{ route('stock_adjustment.approve', $adj->id) }}" class="d-inline">
                                                            @csrf
                                                            <button type="button" class="btn btn-success btn-action mr-1 btn-approve-confirm" data-title="{{ __('Approve Stock Adjustment?') }}" data-text="{{ __('Are you sure you want to approve this stock adjustment?') }}" data-toggle="tooltip" data-original-title="{{ __('Approve') }}">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('stock_adjustment.reject', $adj->id) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="rejection_reason" value="">
                                                            <button type="button" class="btn btn-danger btn-action btn-reject-auth-return" data-toggle="tooltip" data-original-title="{{ __('Reject') }}">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
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

            <div class="col-12 mt-3">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Processed Requests (Approved / Rejected)')}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-flush" id="processed_requests_table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{__('#')}}</th>
                                        <th>{{__('Category')}}</th>
                                        <th>{{__('Date')}}</th>
                                        <th>{{__('Details')}}</th>
                                        <th>{{__('Created By')}}</th>
                                        <th class="text-center">{{__('Status')}}</th>
                                        <th>{{__('Actioned By')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($processedItemReturns ?? [] as $itemReturn)
                                    <tr>
                                        <td class="font-style">
                                            <a href="#" class="text-primary font-weight-bold" data-size="2xl" data-url="{{ route('item_return.edit', $itemReturn->id) }}" data-ajax-popup="true" data-title="{{ __('Item Return Details') }} — {{ Auth::user()->itemReturnNumberFormat($itemReturn->id) }}">
                                                {{ Auth::user()->itemReturnNumberFormat($itemReturn->id) }}
                                            </a>
                                        </td>
                                        <td>
                                            @if (($itemReturn->return_type ?? 'customer') === 'supplier')
                                                <span class="badge badge-warning">{{ __('Supplier Return') }}</span>
                                            @else
                                                <span class="badge badge-info">{{ __('Customer Return') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $itemReturn->date }}</td>
                                        <td>
                                            @if (($itemReturn->return_type ?? 'customer') === 'supplier')
                                                {{ optional($itemReturn->vender)->name ?? '-' }}
                                            @else
                                                {{ optional($itemReturn->invoice)->invoice_code ?? '-' }}
                                            @endif
                                            — {{ optional($itemReturn->product)->name ?? '' }} ({{ number_format((float) $itemReturn->amount, 2) }})
                                        </td>
                                        <td>{{ optional(\App\User::find($itemReturn->created_by))->name ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ((int) $itemReturn->status === 1)
                                                <span class="badge badge-success">{{ __('Approved') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                                @if (!empty($itemReturn->rejection_reason))
                                                    <small class="text-danger d-block">{{ $itemReturn->rejection_reason }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ optional(\App\User::find($itemReturn->approved_by))->name ?? '-' }}
                                            @if ($itemReturn->approved_at)
                                                <small class="text-muted d-block">{{ $itemReturn->approved_at }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach

                                    @foreach ($processedGrns ?? [] as $grn)
                                    <tr>
                                        <td class="font-style">
                                            <a href="{{ route('grn.show', $grn->id) }}" class="text-primary font-weight-bold" target="_blank" rel="noopener">
                                                {{ Auth::user()->grnNumberFormat($grn->id) }}
                                            </a>
                                        </td>
                                        <td><span class="badge badge-primary">{{ __('GRN') }}</span></td>
                                        <td>{{ $grn->order_date }}</td>
                                        <td>
                                            {{ optional($grn->vender)->name ?? '-' }}
                                            @if (!empty($grn->supplier_invoice_number))
                                                — {{ $grn->supplier_invoice_number }}
                                            @endif
                                            ({{ number_format((float) $grn->total_amount, 2) }})
                                        </td>
                                        <td>{{ optional(\App\User::find($grn->created_by))->name ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ($grn->approval_status === 'approved')
                                                <span class="badge badge-success">{{ __('Approved') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ optional(\App\User::find($grn->approved_by))->name ?? '-' }}
                                            @if ($grn->approved_at)
                                                <small class="text-muted d-block">{{ $grn->approved_at }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach

                                    @foreach ($processedSales ?? [] as $sale)
                                    <tr>
                                        <td>{{ Auth::user()->waittingSaleNumberFormat($sale->id) }}</td>
                                        <td>{{ $sale->sale_type == 0 ? __('Cash Sales') : __('Hire Sales') }}</td>
                                        <td>{{ $sale->date_of_sale }}</td>
                                        <td>{{ optional($sale->customer)->name ?? '-' }}</td>
                                        <td>{{ optional($sale->createdUser)->name ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ((int) $sale->status === 3)
                                                <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                            @else
                                                <span class="badge badge-success">{{ __('Approved') }}</span>
                                            @endif
                                        </td>
                                        <td>-</td>
                                    </tr>
                                    @endforeach

                                    @foreach ($processedInstallments ?? [] as $installment)
                                    <tr>
                                        <td>{{ Auth::user()->waittingInstallmentNumberFormat($installment->id) }}</td>
                                        <td>{{ __('Sale Installment') }}</td>
                                        <td>{{ $installment->date }}</td>
                                        <td>{{ optional($installment->customer)->name ?? '-' }}</td>
                                        <td>{{ optional($installment->createdUser)->name ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ((int) $installment->status === 3)
                                                <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                            @else
                                                <span class="badge badge-success">{{ __('Approved') }}</span>
                                            @endif
                                        </td>
                                        <td>-</td>
                                    </tr>
                                    @endforeach

                                    @foreach ($processedLoanInstallments ?? [] as $loanInstallment)
                                    <tr>
                                        <td>{{ Auth::user()->waittingLoanInstallmentNumberFormat($loanInstallment->id) }}</td>
                                        <td>{{ __('Loan Installment') }}</td>
                                        <td>{{ $loanInstallment->date }}</td>
                                        <td>{{ optional($loanInstallment->customer)->name ?? '-' }}</td>
                                        <td>{{ optional($loanInstallment->createdUser)->name ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ((int) $loanInstallment->status === 3)
                                                <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                            @else
                                                <span class="badge badge-success">{{ __('Approved') }}</span>
                                            @endif
                                        </td>
                                        <td>-</td>
                                    </tr>
                                    @endforeach

                                    @foreach ($processedPawningInstallments ?? [] as $pawningInstallment)
                                    <tr>
                                        <td>{{ Auth::user()->waittingPawningInstallmentNumberFormat($pawningInstallment->id) }}</td>
                                        <td>{{ __('Pawning Installment') }}</td>
                                        <td>{{ $pawningInstallment->date }}</td>
                                        <td>{{ optional($pawningInstallment->customer)->name ?? '-' }}</td>
                                        <td>{{ optional($pawningInstallment->createdUser)->name ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ((int) $pawningInstallment->status === 3)
                                                <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                            @else
                                                <span class="badge badge-success">{{ __('Approved') }}</span>
                                            @endif
                                        </td>
                                        <td>-</td>
                                    </tr>
                                    @endforeach

                                    @foreach ($processedStockAdjustments ?? [] as $adj)
                                    <tr>
                                        <td class="font-style">
                                            <a href="{{ route('stock.batch', $adj->inventory_id) }}" class="text-primary font-weight-bold" target="_blank" rel="noopener">
                                                SA{{ str_pad($adj->id, 5, '0', STR_PAD_LEFT) }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge {{ $adj->action === 'add' ? 'badge-success' : 'badge-warning' }}">
                                                {{ $adj->action === 'add' ? __('Stock Add') : __('Stock Deduct') }}
                                            </span>
                                        </td>
                                        <td>{{ $adj->date }}</td>
                                        <td>
                                            {{ optional(optional($adj->inventory)->products)->name ?? ('Inventory #' . $adj->inventory_id) }}
                                            — Qty: <strong>{{ $adj->quantity }}</strong>
                                            @if (!empty($adj->description))
                                                ({{ $adj->description }})
                                            @endif
                                        </td>
                                        <td>{{ optional(\App\User::find($adj->created_by))->name ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ((int) $adj->status === 1)
                                                <span class="badge badge-success">{{ __('Approved') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                                @if (!empty($adj->rejection_reason))
                                                    <small class="text-danger d-block">{{ $adj->rejection_reason }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ optional(\App\User::find($adj->approved_by))->name ?? '-' }}
                                            @if ($adj->approved_at)
                                                <small class="text-muted d-block">{{ $adj->approved_at }}</small>
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

</section>
<script>
    $(document).on('click', '.btn-approve-confirm', function () {
        var btn = $(this);
        var form = btn.closest('form');
        Swal.fire({
            title: btn.data('title') || '{{ __("Are you sure?") }}',
            text: btn.data('text') || '',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: '{{ __("Yes, Approve") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then(function (result) {
            if (result.isConfirmed || result.value === true) {
                form.submit();
            }
        });
    });

    $(document).on('click', '.btn-reject-confirm', function () {
        var btn = $(this);
        var form = btn.closest('form');
        Swal.fire({
            title: btn.data('title') || '{{ __("Are you sure?") }}',
            text: btn.data('text') || '',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, Reject") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then(function (result) {
            if (result.isConfirmed || result.value === true) {
                form.submit();
            }
        });
    });

    $(document).on('click', '.btn-reject-auth-return', function () {
        var form = $(this).closest('form');
        Swal.fire({
            title: '{{ __("Reject Item Return") }}',
            text: '{{ __("Enter rejection reason (optional):") }}',
            input: 'text',
            inputPlaceholder: '{{ __("Reason...") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, Reject") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then(function (result) {
            if (result.isConfirmed || (typeof result.value === 'string' && !result.dismiss)) {
                form.find('input[name="rejection_reason"]').val(result.value || '');
                form.submit();
            }
        });
    });

    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        $('#requests_table').DataTable();
        $('#processed_requests_table').DataTable({
            order: []
        });
    });

</script>
@endsection
{{-- <script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
    // var sales = <php echo json_encode($sales) ?>;
    $(document).ready(function () {
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

        $('#sales_table').on('page.dt', function () {
            var pageInfo = salesTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("sale_page_no", pageNo);
        });

        $('body').on('click', '.edit', function () {
            var value = $(this).val();
            window.location.href = "{{ url('sale/view') }}/" + value;
        });

        $('body').on('click', '.view', function () {
            var value = $(this).val();
            window.location.href = "{{ url('sale/view') }}/" + value;
        });

        $('body').on('click', '.invoice-view', function () {
            var invoiceId = $(this).val();
            window.location.href = "{{ url('invoice') }}/" + invoiceId;
        });
    });

</script> --}}
