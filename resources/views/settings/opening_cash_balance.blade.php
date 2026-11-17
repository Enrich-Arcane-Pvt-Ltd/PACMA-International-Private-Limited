@extends('layouts.admin')
@section('page-title')
{{__('Opening Cash Balance')}}
@endsection
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Opening Cash Balance')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Opening Cash Balance')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Opening Cash Balance') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Opening Cash Balance')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Opening Cash Balance')}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body p-0">
                            {{ Form::open(array('url' => 'opening_cash/update', 'method'=>'POST')) }}
                            <div class="row">
                                <div class="form-group col-md-4">
                                    {{ Form::label('opening_cash', __('Manage Opening Cash Amount')) }}
                                    {{ Form::number('opening_cash', $openingBalance->amount ?? '', array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
                                </div>
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
