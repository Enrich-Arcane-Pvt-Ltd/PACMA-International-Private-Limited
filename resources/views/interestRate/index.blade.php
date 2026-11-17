@extends('layouts.admin')
@section('page-title')
{{__('Interest Rates')}}
@endsection
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Interest Rates')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Interest Rates')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Interest Rates') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Interest Rates')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Interest Rates')}}</h4>
                            {{-- @can('create rate')
                                    <div class="col-auto">
                                        <a href="#" data-url="{{ route('interest-rate.create') }}"
                            data-ajax-popup="true" data-title="{{__('Create Interest Rate')}}" class="btn btn-icon
                            icon-left btn-primary">
                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                            <span class="btn-inner--text"> {{__('Create')}}</span>
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
                                        <table class="table table-flush" id="dataTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th> {{__('#')}}</th>
                                                    <th> {{__('Service Type')}}</th>
                                                    <th> {{__('Rate %')}}</th>
                                                    @if(Gate::check('edit rate'))
                                                    <th class="text-right"> {{__('Action')}}</th>
                                                    @endif
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($interestRates as $interestRate)
                                                <tr class="font-style">
                                                    <td>{{ AUth::user()->interestRateNumberFormat($interestRate['id']) }}
                                                    </td>
                                                    <td>{{ $interestRate->service_type }}</td>
                                                    <td>{{ $interestRate->interest_rate."%"}}</td>
                                                    @if(Gate::check('edit rate'))
                                                    <td class="action text-right">
                                                        @can('edit rate')
                                                        <a href="#" class="btn btn-primary btn-action mr-1"
                                                            data-url="{{ route('interest-rate.edit',$interestRate->id) }}"
                                                            data-ajax-popup="true" data-title="{{__('Edit Tax Rate')}}"
                                                            data-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        @endcan
                                                        {{-- @can('delete constant tax')
                                                                        <a href="#" class="btn btn-danger btn-action " data-toggle="tooltip" data-original-title="{{__('Delete')}}"
                                                        data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{$interestRate->id}}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                        </a>
                                                        {!! Form::open(['method' => 'DELETE', 'route' =>
                                                        ['taxes.destroy',
                                                        $interestRate->id],'id'=>'delete-form-'.$interestRate->id]) !!}
                                                        {!! Form::close() !!}
                                                        @endcan --}}
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
