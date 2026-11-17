@extends('layouts.admin')
@section('page-title')
{{__('Petty Cash Float')}}
@endsection
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Petty Cash Float')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Petty Cash Float')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Petty Cash Float') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Petty Cash Float')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Petty Cash Float')}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body p-0">
                            {{ Form::open(array('url' => 'petty_cash_fund/edit', 'method'=>'POST')) }}
                            <div class="row">
                                <div class="form-group col-md-4">
                                    {{ Form::label('petty_cash_fund', __('Manage Cash Fund Amount')) }}
                                    {{ Form::number('petty_cash_fund', $pettyCashFund->fund_amount ?? 0, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
                                </div>
                                <!--pettyCashFund-->
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
