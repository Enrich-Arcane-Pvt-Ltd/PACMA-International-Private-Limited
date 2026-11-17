@extends('layouts.admin')
@section('page-title')
{{__('Revenue')}}
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
            <h1>{{__('Revenue')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Revenue')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Revenue') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Revenue')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Revenue')}}</h4>
                            <div class="card-header-action">
                                <div class="dropdown">
                                    <a href="#" data-toggle="dropdown" class="btn btn-icon icon-left btn-primary"><i
                                            class="fas fa-filter"></i>{{__('Filter')}}</a>
                                    <div
                                        class="dropdown-menu dropdown-list dropdown-menu-right Filter-dropdown w-64 {{isset($_GET['date'])?'show':''}}">
                                        {{ Form::open(array('route' => array('revenue.index'),'method' => 'GET')) }}
                                        <div class="form-group">
                                            {{ Form::label('date', __('Date')) }}
                                            {{ Form::text('date', isset($_GET['date'])?$_GET['date']:null, array('class' => 'form-control datepicker-range')) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('account', __('Account')) }}
                                            {{ Form::select('account',$account,isset($_GET['account'])?$_GET['account']:'', array('class' => 'form-control font-style selectric')) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('customer', __('Customer')) }}
                                            {{ Form::select('customer',$customer,isset($_GET['customer'])?$_GET['customer']:'', array('class' => 'form-control font-style selectric')) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('category', __('Category')) }}
                                            {{ Form::select('category',$category,isset($_GET['category'])?$_GET['category']:'', array('class' => 'form-control font-style selectric')) }}
                                        </div>


                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary">{{__('Search')}}</button>
                                            <a href="{{route('revenue.index')}}"
                                                class="btn btn-danger">{{__('Reset')}}</a>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                                @can('create revenue')
                                <a href="#" data-url="{{ route('revenue.create') }}" data-ajax-popup="true"
                                    data-title="{{__('Create New Revenue')}}"
                                    class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{__('Create')}}</span>
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
                                            <table class="table table-flush" id="revenues_table">
                                                <thead class="thead-light">
                                                    <tr>

                                                        <th> {{__('Date')}}</th>
                                                        <th> {{__('Amount')}}</th>
                                                        <th> {{__('Account')}}</th>
                                                        <th> {{__('Customer')}}</th>
                                                        <th> {{__('Category')}}</th>
                                                        <th> {{__('Reference')}}</th>
                                                        <th> {{__('Description')}}</th>
                                                        @if(Gate::check('edit revenue') || Gate::check('delete
                                                        revenue'))
                                                        <th class="text-right"> {{__('Action')}}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($revenues as $revenue)

                                                    <tr class="font-style">
                                                        <td>{{  Auth::user()->dateFormat($revenue->date)}}</td>
                                                        <td>{{  Auth::user()->priceFormat($revenue->amount)}}</td>
                                                        <td>{{ !empty($revenue->bankAccount)?$revenue->bankAccount->bank_name.' '.$revenue->bankAccount->holder_name:''}}
                                                        </td>
                                                        <td>{{  (!empty($revenue->customer)?$revenue->customer->name:'-')}}
                                                        </td>
                                                        <td>{{  !empty($revenue->category)?$revenue->category->name:'-'}}
                                                        </td>
                                                        <td>{{  !empty($revenue->reference)?$revenue->reference:'-'}}
                                                        </td>
                                                        <td>{{  !empty($revenue->description)?$revenue->description:'-'}}
                                                        </td>

                                                        @if(Gate::check('edit revenue') || Gate::check('delete
                                                        revenue'))
                                                        <td class="action text-right">
                                                            @can('edit revenue')
                                                            <a href="#!" class="btn btn-primary btn-action mr-1"
                                                                data-url="{{ route('revenue.edit',$revenue->id) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{__('Edit Revenue')}}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Edit')}}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endcan
                                                            @can('delete revenue')
                                                            <a href="#!" class="btn btn-danger btn-action"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Delete')}}"
                                                                data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{$revenue->id}}').submit();">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            {!! Form::open(['method' => 'DELETE', 'route' =>
                                                            ['revenue.destroy',
                                                            $revenue->id],'id'=>'delete-form-'.$revenue->id]) !!}
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
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        if (localStorage.getItem("revenue_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("revenue_page_no"));
        }
        var revenuesTable = $('#revenues_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        revenuesTable.page(pageNo).draw(false);

        $('#revenues_table').on('page.dt', function () {
            var pageInfo = revenuesTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("revenue_page_no", pageNo);
        });
    });

</script>
@endsection
