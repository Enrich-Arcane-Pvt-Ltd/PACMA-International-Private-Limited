@extends('layouts.admin')
@section('page-title')
{{__('Penalty Control')}}
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
        background: url("{{ asset('argon') }}/img/loader/Spinner-1s-200px.gif") center no-repeat #ffffff99;
        display: none;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 25px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 17px;
        width: 17px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

</style>
<div class="se-pre-con"></div>
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Penalty Control')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Penalty Control')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Penalty Control') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Penalty Control')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Main Penalty Control')}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body p-0">
                            {{ Form::open(array('url' => 'fine_control/update', 'method'=>'POST')) }}
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <div class="input-group colorpickerinput"
                                        style="padding-top: 1.5rem; padding-left: 2rem;">
                                        <label class="switch">
                                            <input type="checkbox" id="fine_status" name="fine_status"
                                                @if(optional($fineCommand)->fine_status == 1) checked @endif>
                                            <span class="slider round"></span>
                                        </label>
                                        <label style="padding-left: 1.5rem; padding-top: 3px; font-weight: bold;"
                                            for="fine_status">{{__('Applying Penalty')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <center>
                                        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
                                    </center>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-5">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Sub Penalty Control')}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Penelty For</h5>
                            </div>
                            <div class="col-md-4">
                                <h5>Start Date</h5>
                            </div>
                            <div class="col-md-4">
                                <h5>Status</h5>
                            </div>
                        </div>
                        <hr>
                        {{ Form::open(array('url' => 'fine_control/update_child', 'method'=>'POST')) }}
                        @foreach ($fineChildCommands as $key=>$fineChildCommand)
                        <div class="row">
                            <input type="hidden" name="fchild[{{ $key }}][id]" value="{{ $fineChildCommand->id }}">
                            <div class="col-md-4">
                                <h6>{{ $fineChildCommand->name }}</h6>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="fchild[{{ $key }}][start_date]"
                                    class="start_date form-control datepicker"
                                    value="{{ $fineChildCommand->start_date }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="input-group colorpickerinput"
                                    style="padding-top: 0.25rem; padding-left: 1rem;">
                                    <label class="switch">
                                        <input type="checkbox" id="fine_status" name="fchild[{{ $key }}][fine_status]"
                                            @if($fineChildCommand->fine_status == 1) checked @endif>
                                        <span class="slider round"></span>
                                    </label>
                                    {{-- <label style="padding-left: 1.5rem; padding-top: 3px; font-weight: bold;" for="fine_status">{{__('Applying Penalty')}}</label>
                                    --}}
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="row">
                            <div class="col-md-12">
                                <center>
                                    {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
                                </center>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <div class="col-7">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Penalty For Centers')}}</h4>
                            @can('edit penalty')
                            <a href="#" data-size="xl" data-url="{{ route('fine_control.add_center') }}"
                                data-ajax-popup="true" data-title="{{__('Add New Center')}}"
                                class="btn btn-icon icon-left btn-primary">
                                <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                <span class="btn-inner--text"> {{__('Add New Center')}}</span>
                            </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table" id="dataTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Center</th>
                                            <th>Start Date</th>
                                            <th>Fine Status</th>
                                            @if(Gate::check('edit penalty'))
                                            <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fineCenterCommands as $fineCenterCommand)
                                        <tr>
                                            <td>{{ optional($fineCenterCommand->center)->name ?? '' }}</td>
                                            <td>{{ $fineCenterCommand->start_date ?? '' }}</td>
                                            <td>
                                                @if ($fineCenterCommand->fine_status == 1)
                                                <span class="badge badge-success">Applying</span>
                                                @elseif ($fineCenterCommand->fine_status == 0)
                                                <span class="badge badge-danger">Stopped</span>
                                                @endif
                                            </td>
                                            @if(Gate::check('edit penalty'))
                                            <td>
                                                @can('edit penalty')
                                                <a href="#" data-size="xl"
                                                    data-url="{{ route('fine_control.edit_center',$fineCenterCommand->id) }}"
                                                    data-ajax-popup="true" data-title="{{__('Change Center Penalty')}}"
                                                    class="btn btn-icon icon-left btn-primary">
                                                    <span class="btn-inner--icon"><i class="fas fa-undo-alt"></i></span>
                                                    <span class="btn-inner--text"> {{__('Change')}}</span>
                                                </a>
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
</section>
@endsection
