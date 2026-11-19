@extends('layouts.admin')
@section('page-title')
{{__('Petty Cash')}}
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
        <h1>{{__('Petty Cash')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Petty Cash')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Petty Cash ') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Petty Cash')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-center align-items-center w-100 py-1">
                            <div class="px-4 text-right" style="border-right: 1px solid #CBD5E1;">
                                <h6 class="text-muted text-uppercase small font-weight-bold mb-1">{{__('Petty Cash Float')}}</h6>
                                <h5 class="mb-0 font-weight-bold">{{ number_format($pettyCashFund->fund_amount ?? 0, 2) }}</h5>
                            </div>
                            <div class="px-4 text-left">
                                <h6 class="text-muted text-uppercase small font-weight-bold mb-1">{{__('Balance Amount')}}</h6>
                                <h5 class="mb-0 font-weight-bold text-primary">{{ number_format($pettyCashFund->balance_amount ?? 0, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Petty Cash')}}</h4>
                            <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            {{-- {{Form::label('petty_cash_fund','Petty Cash Fund',array('id'=>'pettycashfund'))}} --}}
                            {{-- <td class="account_transaction_td" style="text-align: center">{{$pettyCashFund->payment->petty_cash_id}}
                            </td> --}}
                            @can('create petty cash')
                            <div class="col-auto">
                                <a href="#" data-size="xl" data-url="{{ route('petty-cash.create') }}"
                                    data-ajax-popup="true" data-title="{{__('Create New Petty Cash')}}"
                                    class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{__('Create')}}</span>
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
                                            <table class="table table-flush" id="petty_cash_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th> {{__('Petty Cash #')}}</th>
                                                        <th> {{__('Date')}}</th>
                                                        <th> {{__('Chart Of Account')}}</th>
                                                        <th> {{__('Description')}}</th>
                                                        <th> {{__('Amount')}}</th>
                                                        <th> {{__('Approved By')}}</th>
                                                        <th> {{__('Received By')}}</th>
                                                        @if(Gate::check('edit petty cash') || Gate::check('delete petty
                                                        cash'))
                                                        <th class="text-center"> {{__('Action')}}</th>
                                                        @endif
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($pettyCashes as $pettyCash)
                                                    <tr>
                                                        <td>
                                                            <a href="#" class="btn btn-outline-primary">
                                                                {{ AUth::user()->pettyCashNumberFormat($pettyCash['petty_cash_id']) }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $pettyCash->date }}</td>
                                                        <td>{{ $pettyCash->chartOfAccount->name ?? '' }}</td>
                                                        <td>{{ $pettyCash->description }}</td>
                                                        <td>{{ \Auth::user()->priceFormat($pettyCash->amount)}}</td>
                                                        <td>{{ $pettyCash->approvedUser->name ?? '' }}</td>
                                                        <td>{{ $pettyCash->receivedUser->name ?? '' }}</td>
                                                        @if(Gate::check('edit petty cash') || Gate::check('delete petty
                                                        cash'))
                                                        <td class="text-center">
                                                            @can('edit petty cash')
                                                            <a href="#!" class="btn btn-primary btn-action mr-1"
                                                                data-size="xl"
                                                                data-url="{{ route('petty-cash.edit',$pettyCash->id) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{__('Edit Petty Cash')}}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Edit')}}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endcan
                                                            @can('delete petty cash')
                                                            <a href="#" class="btn btn-danger btn-action"
                                                                style="padding-top: 10px;" data-toggle="tooltip"
                                                                data-original-title="{{__('Delete')}}"
                                                                data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{$pettyCash->id}}').submit();">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            @endcan
                                                            {!! Form::open(['method' => 'GET', 'route' =>
                                                            ['petty-cash.destroy',
                                                            $pettyCash->id],'id'=>'delete-form-'.$pettyCash->id]) !!}
                                                            {!! Form::close() !!}
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
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        // console.log(sales);
        if (localStorage.getItem("petty_cash_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("petty_cash_page_no"));
        }
        var pettyCashTable = $('#petty_cash_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        pettyCashTable.page(pageNo).draw(false);

        $('#petty_cash_table').on('page.dt', function () {
            var pageInfo = pettyCashTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("petty_cash_page_no", pageNo);
        });
    });

</script>

@endsection
