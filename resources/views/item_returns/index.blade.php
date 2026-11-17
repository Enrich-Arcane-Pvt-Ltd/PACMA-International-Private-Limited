@extends('layouts.admin')

@section('page-title')
{{ __('Item Return') }}
@endsection
@section('content')
<section class="section">
    {{-- <div class="section-header">
        <h1>{{ __('Item Return') }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
            <div class="breadcrumb-item">{{ __('Item Return') }}</div>
        </div>
    </div> --}}
    <div class="row justify-content-center">

        <div class="col-12">
            <div class="bg-white p-2 br-4 mb-2">
                <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                    <h4 class="mt-2 ">{{ __('Item Return') }}</h3>
                    <div class="section-header-breadcrumb d-flex ">
                        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                        <div class="breadcrumb-item">{{ __('Item Return') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card br-4 cus_show">
                <div class="card-header">
                    <div class="d-flex justify-content-between w-100">
                        <h4>{{ __('Manage Item Return') }}</h4>
                        <div class="card-header-action">
                            @if( Gate::check('create item return') || Gate::check('create stock adjustment'))
                                <a href="#" data-size="2xl" data-url="{{ route('item_return.create') }}" data-ajax-popup="true" data-title="{{ __('Create New Item Return') }}" class="btn btn-icon icon-left btn-primary commonModal">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{ __('Create') }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-body p-0">
                        <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-flush" id="item_return_table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="text-align: center"> {{ __('Return #') }}</th>
                                                    <th style="text-align: center"> {{ __('Date') }}</th>
                                                    <th style="text-align: center"> {{ __('Return Type') }}</th>
                                                    <th style="text-align: center"> {{ __('Invoice / Supplier') }}</th>
                                                    <th style="text-align: center"> {{ __('Product') }}</th>
                                                    <th style="text-align: center"> {{ __('Serial No.') }}</th>
                                                    <th style="text-align: center"> {{ __('Account Name') }}</th>
                                                    <th style="text-align: center"> {{ __('Account Status') }}</th>
                                                    <th style="text-align: center"> {{ __('Related Account Name') }}</th>
                                                    <th style="text-align: center"> {{ __('Realated Account Status') }}</th>
                                                    <th style="text-align: center"> {{ __('Narration') }}</th>
                                                    <th style="text-align: center"> {{ __('Amount') }}</th>
                                                    <th class="text-center" style="text-align: center">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($itemReturns as $itemReturn)
                                                <tr>
                                                    <td style="text-align: center">
                                                        {{ AUth::user()->itemReturnNumberFormat($itemReturn->id) }}
                                                    </td>
                                                    <td style="text-align: center">{{ $itemReturn->date }}</td>
                                                    <td style="text-align: center">
                                                        @if (($itemReturn->return_type ?? 'customer') === 'supplier')
                                                            <span class="badge badge-warning">{{ __('Supplier Return') }}</span>
                                                        @else
                                                            <span class="badge badge-info">{{ __('Customer Return') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (($itemReturn->return_type ?? 'customer') === 'supplier')
                                                            {{ optional($itemReturn->vender)->name ?? '-' }}
                                                            @if ($itemReturn->grn)
                                                                <small class="text-muted d-block">{{ $itemReturn->grn->supplier_invoice_number ?? '' }}</small>
                                                            @endif
                                                        @else
                                                            {{ $itemReturn->invoice->invoice_code ?? '' }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $itemReturn->product->name ?? '' }}</td>
                                                    <td>{{ $itemReturn->serial_no ?? '' }}</td>
                                                    <td>{{ $itemReturn->chart_of_account->name ?? '' }}</td>
                                                    <td style="text-align: center">
                                                        @if($itemReturn->debit_credit == 0)
                                                            Credit
                                                        @elseif($itemReturn->debit_credit == 1)
                                                            Debit
                                                        @endif
                                                    </td>
                                                    <td>{{ $itemReturn->retlated_chart_of_account->name ?? '' }}</td>
                                                    <td style="text-align: center">
                                                        @if($itemReturn->related_debit_credit == 0)
                                                            Credit
                                                        @elseif($itemReturn->related_debit_credit == 1)
                                                            Debit
                                                        @endif
                                                    </td>
                                                    <td>{{ $itemReturn->narration }}</td>
                                                    <td style="text-align: right">{{ $itemReturn->amount }}</td>
                                                    <td class="text-center" style="white-space: nowrap;">
                                                        @can('edit item return')
                                                            <a href="#" class="btn btn-primary btn-action mr-1" data-size="2xl" data-url="{{ route('item_return.edit', $itemReturn->id) }}" data-ajax-popup="true" data-title="{{ __('Edit Item Return') }}" data-toggle="tooltip" data-original-title="{{ __('Edit') }}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                        @endcan

                                                        @can('delete item return')
                                                            <a href="#" class="btn btn-danger btn-action" style="padding-top: 10px;" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$itemReturn->id}}').submit();">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            {!! Form::open(['method' => 'GET', 'route' => ['item_return.destroy', $itemReturn->id],'id'=>'delete-form-'.$itemReturn->id]) !!}
                                                            {!! Form::close() !!}
                                                        @endcan
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
</section>


<script>

        $(document).ready(function(){
        $(".se-pre-con").fadeOut('slow');
        if(localStorage.getItem("item_return_no") == null){
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("item_return_no"));
        }
        var item_returnTable = $('#item_return_table').DataTable({order:[[0,"asc"]]});
        item_returnTable.page(pageNo).draw(false);

        $('#item_return_table').on( 'page.dt', function () {
            var pageInfo =  item_returnTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("item_return_no", pageNo);
        });
    });

        // $('body').on('click', '.item_return_td', function(){
        // var itemReturnId = $(this).closest('tr').attr("data-id");
        // window.location.href = "{{ url('item_return') }}/"+itemReturnId;
        // });

</script>

@endsection
