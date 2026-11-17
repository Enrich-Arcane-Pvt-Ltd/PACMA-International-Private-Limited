@extends('layouts.admin')
@section('page-title')
{{__('Card Payment Rate')}}
@endsection
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Card Payment Rate')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Card Payment Rate')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{__('Card Payment Rate')}}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Card Payment Rate')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Card Payment Rate')}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body p-0">
                            {{ Form::open(array('url' => 'card_payment_rate/edit')) }}
                            <div class="row">
                                <div class="form-group col-md-4">
                                    {{ Form::label('credit_card_rate', __('Credit Card Rate')) }}
                                    <div class="input-group colorpickerinput">
                                        {{ Form::number('credit_card_rate', $cardRates[0]->rate, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                {{"%"}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    {{ Form::label('debit_card_rate', __('Debit Card Rate')) }}
                                    <div class="input-group colorpickerinput">
                                        {{ Form::number('debit_card_rate', $cardRates[1]->rate, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                {{"%"}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 text-right">
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
