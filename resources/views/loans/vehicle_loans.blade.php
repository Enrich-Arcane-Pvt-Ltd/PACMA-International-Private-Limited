@extends('layouts.admin')
@section('page-title')
{{__('Vehicle Loan')}}
@endsection
@section('content')
@push('css-page')
<link href="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet"
    type="text/css" />
@endpush

@push('script-page')
<script>
    // $(document).ready(function(){
    //     $('#customer').select2();
    // });

    $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function () {
        var template = $("select[name='invoice_template']").val();
        var color = $("input[name='invoice_color']:checked").val();
        $('#invoice_frame').attr('src', '{{url(' / invoices / preview ')}}/' + template + '/' + color);
    });

    $(document).on("change", "select[name='proposal_template'], input[name='proposal_color']", function () {
        var template = $("select[name='proposal_template']").val();
        var color = $("input[name='proposal_color']:checked").val();
        $('#proposal_frame').attr('src', '{{url(' / proposal / preview ')}}/' + template + '/' + color);
    });

    $(document).on("change", "select[name='bill_template'], input[name='bill_color']", function () {
        var template = $("select[name='bill_template']").val();
        var color = $("input[name='bill_color']:checked").val();
        $('#bill_frame').attr('src', '{{url(' / bill / preview ')}}/' + template + '/' + color);
    });

</script>
<script src="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
@endpush
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

    /* .card-body {
    padding-left: 1px;
    padding-right: 1px;
} */

</style>

@if(\Auth::user()->type == 'Cash Collector' || \Auth::user()->type == 'Branch Manager' || \Auth::user()->type == 'Field
Manager' || \Auth::user()->type == 'Center Manager')
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
            <h1>{{__('Vehicle Loan')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Loans')}}</div>
        <div class="breadcrumb-item">{{__('Vehicle Loan')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Vehicle Loan') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Loans')}}</div>
                                <div class="breadcrumb-item">{{__('Vehicle Loan')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Loan Details')}}</h4>
                            <div class="card-header-action">
                                @can('create loan')
                                <a href="#" data-size="xl" data-url="{{ route('vehicle_loan.create') }}"
                                    data-ajax-popup="true" data-title="{{__('Create New Vehicle Loan')}}"
                                    class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{__('Create')}}</span>
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="card-body phone-card-bd">
                        <div class="setting-tab">
                            <ul class="nav nav-pills nav-fill" id="myTab3" role="tablist">

                                @if(Gate::check('pending loan'))
                                <li class="nav-item">
                                    <a class="nav-link @if(Gate::check('pending loan')) active @endif" id="contact-tab4"
                                        data-toggle="tab" href="#pending-loans" role="tab" aria-controls=""
                                        aria-selected="false">{{__('Pending Loans')}}</a>
                                </li>
                                @endif
                                @if(Gate::check('checked loan'))
                                <li class="nav-item">
                                    <a class="nav-link @if(!Gate::check('pending loan') && Gate::check('checked loan')) active @endif"
                                        id="contact-tab4" data-toggle="tab" href="#checked-loans" role="tab"
                                        aria-controls="" aria-selected="false">{{__('Checked Loans')}}</a>
                                </li>
                                @endif
                                @if(Gate::check('granted loan'))
                                <li class="nav-item">
                                    <a class="nav-link @if(!Gate::check('pending loan') && !Gate::check('checked loan') && Gate::check('granted loan')) active @endif"
                                        id="profile-tab3" data-toggle="tab" href="#granted-loans" role="tab"
                                        aria-controls="" aria-selected="false">{{__('Approved Loans')}}</a>
                                </li>
                                @endif
                                @if(Gate::check('disbursed loan'))
                                <li class="nav-item">
                                    <a class="nav-link @if(!Gate::check('pending loan') && !Gate::check('checked loan') && !Gate::check('granted loan') && Gate::check('disbursed loan')) active @endif"
                                        id="profile-tab3" data-toggle="tab" href="#disbursed-loans" role="tab"
                                        aria-controls="" aria-selected="false">{{__('Disbursed Loans')}}</a>
                                </li>
                                @endif
                                @if(Gate::check('rejected loan'))
                                <li class="nav-item">
                                    <a class="nav-link @if(!Gate::check('pending loan') && !Gate::check('checked loan') && !Gate::check('granted loan') && !Gate::check('disbursed loan') && Gate::check('rejected loan')) active @endif"
                                        id="profile-tab3" data-toggle="tab" href="#rejected-loans" role="tab"
                                        aria-controls="" aria-selected="false">{{__('Rejected Loans')}}</a>
                                </li>
                                @endif
                                @if(Gate::check('closed loan'))
                                <li class="nav-item">
                                    <a class="nav-link @if(!Gate::check('pending loan') && !Gate::check('checked loan') && !Gate::check('granted loan') && !Gate::check('disbursed loan') && !Gate::check('rejected loan') && Gate::check('closed loan')) active @endif"
                                        id="profile-tab3" data-toggle="tab" href="#closed-loans" role="tab"
                                        aria-controls="" aria-selected="false">{{__('Closed Loans')}}</a>
                                </li>
                                @endif
                            </ul>

                            <div class="tab-content" id="myTabContent2">
                                @if(Gate::check('pending loan'))
                                <div class="tab-pane fade fade @if(Gate::check('pending loan')) show active @endif"
                                    id="pending-loans" role="tabpanel" aria-labelledby="profile-tab3">
                                    <div class="company-setting-wrap">
                                        <div class="card-body" style="padding-left: 1px; padding-right: 1px;">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-body"
                                                            style="padding-left: 1px; padding-right: 1px;">
                                                            <div class="row">
                                                                <div id="table-1_wrapper"
                                                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                                                    <div class="table-responsive"
                                                                        style="overflow-x: auto;">
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <table class="table table-flush"
                                                                                    id="pending_vehicle_loan_table">
                                                                                    <thead class="thead-light">
                                                                                        <tr>
                                                                                            <th>{{__('Loan Id')}}</th>
                                                                                            <th> {{__('Customer No')}}
                                                                                            </th>
                                                                                            <th> {{__('Customer Name')}}
                                                                                            </th>
                                                                                            <th> {{__('Loan Amount')}}
                                                                                            </th>
                                                                                            <th> {{__('Outstanding Balance')}}
                                                                                            </th>
                                                                                            <th> {{__('Time Period')}}
                                                                                            </th>
                                                                                            <th> {{__('Collection Period')}}
                                                                                            </th>
                                                                                            <th> {{__('NIC')}}</th>
                                                                                            @if(Gate::check('check
                                                                                            loan') || Gate::check('edit
                                                                                            loan') ||
                                                                                            Gate::check('delete loan')
                                                                                            || Gate::check('reject
                                                                                            loan'))
                                                                                            <th class="text-right">
                                                                                                {{__('Action')}}</th>
                                                                                            @endif
                                                                                        </tr>
                                                                                    </thead>

                                                                                    <tbody>
                                                                                        @foreach ($pendingLoan as
                                                                                        $pending)
                                                                                        <tr>
                                                                                            {{-- <td class="font-style">{{ $sale->sale_id }}
                                                                                            </td> --}}
                                                                                            <td class="font-style">
                                                                                                <button type="button"
                                                                                                    class="btn btn-outline-primary view"
                                                                                                    value="{{ $pending->id }}">
                                                                                                    {{ \Auth::user()->loanNumberFormat($pending->id) }}
                                                                                                </button></td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->customerNumberFormat($pending->customer->id)}}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $pending->customer->name ?? '' }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->priceFormat($pending->loan_amount) }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->priceFormat($pending->current_due_amount) }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $pending->time_period }}
                                                                                                {{ __('Months') }} </td>
                                                                                            <td class="font-style">
                                                                                                @if
                                                                                                ($pending->time_range ==
                                                                                                1)
                                                                                                Daily
                                                                                                @elseif
                                                                                                ($pending->time_range ==
                                                                                                2)
                                                                                                Weekly
                                                                                                @elseif
                                                                                                ($pending->time_range ==
                                                                                                3)
                                                                                                Monthly
                                                                                                @endif
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $pending->customer->nic_no ?? '' }}
                                                                                            </td>
                                                                                            @if(Gate::check('check
                                                                                            loan') || Gate::check('edit
                                                                                            loan') ||
                                                                                            Gate::check('delete loan')
                                                                                            || Gate::check('reject
                                                                                            loan'))
                                                                                            <td
                                                                                                class="action text-right">
                                                                                                @can('edit loan')
                                                                                                <a href="#"
                                                                                                    data-size="xl"
                                                                                                    data-url="{{ route('loan.edit',$pending->id) }}"
                                                                                                    data-ajax-popup="true"
                                                                                                    data-toggle="tooltip"
                                                                                                    data-title="{{__('Edit Vehicle Loan')}}"
                                                                                                    class="btn btn-action btn-primary">
                                                                                                    <i class="fas fa-pencil-alt"
                                                                                                        style="padding-top: 5px;"></i>
                                                                                                </a>
                                                                                                @endcan
                                                                                                @can('check loan')
                                                                                                <a href="#"
                                                                                                    data-size="xl"
                                                                                                    data-url="{{ route('loan.loan_check',$pending->id) }}"
                                                                                                    data-ajax-popup="true"
                                                                                                    data-toggle="tooltip"
                                                                                                    data-title="{{__('Check Vehicle Loan')}}"
                                                                                                    class="btn btn-action btn-info">
                                                                                                    <i class="fas fa-check"
                                                                                                        style="padding-top: 5px;"></i>
                                                                                                </a>
                                                                                                @endcan
                                                                                                @can('reject loan')
                                                                                                <a href="#"
                                                                                                    class="btn btn-warning btn-action"
                                                                                                    style="padding-top: 10px;"
                                                                                                    data-toggle="tooltip"
                                                                                                    data-original-title="{{__('Reject')}}"
                                                                                                    data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                                                    data-confirm-yes="document.getElementById('reject-form-{{$pending->id}}').submit();">
                                                                                                    <i
                                                                                                        class="fas fa-times"></i>
                                                                                                </a>
                                                                                                @endcan
                                                                                                @can('delete loan')
                                                                                                <a href="#"
                                                                                                    class="btn btn-danger btn-action"
                                                                                                    style="padding-top: 10px;"
                                                                                                    data-toggle="tooltip"
                                                                                                    data-original-title="{{__('Delete')}}"
                                                                                                    data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                                                    data-confirm-yes="document.getElementById('delete-form-{{$pending->id}}').submit();">
                                                                                                    <i
                                                                                                        class="fas fa-trash"></i>
                                                                                                </a>
                                                                                                @endcan

                                                                                                {!! Form::open(['method'
                                                                                                => 'GET', 'route' =>
                                                                                                ['loan.reject',
                                                                                                $pending->id],'id'=>'reject-form-'.$pending->id])
                                                                                                !!}
                                                                                                {!! Form::close() !!}

                                                                                                {!! Form::open(['method'
                                                                                                => 'GET', 'route' =>
                                                                                                ['loan.destroy',
                                                                                                $pending->id],'id'=>'delete-form-'.$pending->id])
                                                                                                !!}
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
                                                                <div class="col-md-6 d-none" id="customer_details">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(Gate::check('checked loan'))
                                <div class="tab-pane fade fade  @if(!Gate::check('pending loan') && Gate::check('checked loan')) show active @endif"
                                    id="checked-loans" role="tabpanel" aria-labelledby="profile-tab3">
                                    <div class="company-setting-wrap">
                                        <div class="card-body" style="padding-left: 1px; padding-right: 1px;">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-body"
                                                            style="padding-left: 1px; padding-right: 1px;">
                                                            <div class="row">
                                                                <div id="table-1_wrapper"
                                                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                                                    <div class="table-responsive"
                                                                        style="overflow-x: auto;">
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <table class="table table-flush"
                                                                                    id="checked_vehicle_loan_table">
                                                                                    <thead class="thead-light">
                                                                                        <tr>
                                                                                            <th>{{__('Loan Id')}}</th>
                                                                                            <th> {{__('Customer No')}}
                                                                                            </th>
                                                                                            <th> {{__('Customer Name')}}
                                                                                            </th>
                                                                                            <th> {{__('Loan Amount')}}
                                                                                            </th>
                                                                                            <th> {{__('Outstanding Balance')}}
                                                                                            </th>
                                                                                            <th> {{__('Time Period')}}
                                                                                            </th>
                                                                                            <th> {{__('Collection Period')}}
                                                                                            </th>
                                                                                            <th> {{__('NIC')}}</th>
                                                                                            @if(Gate::check('approve
                                                                                            loan') ||
                                                                                            Gate::check('delete loan')
                                                                                            || Gate::check('reject
                                                                                            loan'))
                                                                                            <th class="text-right">
                                                                                                {{__('Action')}}</th>
                                                                                            @endif
                                                                                        </tr>
                                                                                    </thead>

                                                                                    <tbody>
                                                                                        @foreach ($checkedLoan as
                                                                                        $checked)
                                                                                        <tr>
                                                                                            <td class="font-style">
                                                                                                <button type="button"
                                                                                                    class="btn btn-outline-primary view"
                                                                                                    value="{{ $checked->id }}">
                                                                                                    {{ \Auth::user()->loanNumberFormat($checked->id) }}
                                                                                                </button></td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->customerNumberFormat($checked->customer->id)}}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $checked->customer->name ?? '' }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->priceFormat($checked->loan_amount) }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->priceFormat($checked->current_due_amount) }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $checked->time_period }}
                                                                                                {{ __('Months') }} </td>
                                                                                            <td class="font-style">
                                                                                                @if
                                                                                                ($checked->time_range ==
                                                                                                1)
                                                                                                Daily
                                                                                                @elseif
                                                                                                ($checked->time_range ==
                                                                                                2)
                                                                                                Weekly
                                                                                                @elseif
                                                                                                ($checked->time_range ==
                                                                                                3)
                                                                                                Monthly
                                                                                                @endif
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $checked->customer->nic_no ?? '' }}
                                                                                            </td>
                                                                                            @if(Gate::check('approve
                                                                                            loan') ||
                                                                                            Gate::check('delete loan')
                                                                                            || Gate::check('reject
                                                                                            loan'))
                                                                                            <td
                                                                                                class="action text-right">
                                                                                                @can('approve loan')
                                                                                                <a href="#"
                                                                                                    data-size="xl"
                                                                                                    data-url="{{ route('loan.approve_check',$checked->id) }}"
                                                                                                    data-ajax-popup="true"
                                                                                                    data-toggle="tooltip"
                                                                                                    data-title="{{__('Approve Vehicle Loan')}}"
                                                                                                    class="btn btn-action btn-info">
                                                                                                    <i class="fas fa-check"
                                                                                                        style="padding-top: 5px;"></i>
                                                                                                </a>
                                                                                                @endcan
                                                                                                @can('reject loan')
                                                                                                <a href="#"
                                                                                                    class="btn btn-warning btn-action"
                                                                                                    style="padding-top: 10px;"
                                                                                                    data-toggle="tooltip"
                                                                                                    data-original-title="{{__('Reject')}}"
                                                                                                    data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                                                    data-confirm-yes="document.getElementById('reject-form-{{$checked->id}}').submit();">
                                                                                                    <i
                                                                                                        class="fas fa-times"></i>
                                                                                                </a>
                                                                                                @endcan
                                                                                                @can('delete loan')
                                                                                                <a href="#"
                                                                                                    class="btn btn-danger btn-action"
                                                                                                    style="padding-top: 10px;"
                                                                                                    data-toggle="tooltip"
                                                                                                    data-original-title="{{__('Delete')}}"
                                                                                                    data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                                                    data-confirm-yes="document.getElementById('delete-form-{{$checked->id}}').submit();">
                                                                                                    <i
                                                                                                        class="fas fa-trash"></i>
                                                                                                </a>
                                                                                                @endcan
                                                                                                {!! Form::open(['method'
                                                                                                => 'GET', 'route' =>
                                                                                                ['loan.reject',
                                                                                                $checked->id],'id'=>'reject-form-'.$checked->id])
                                                                                                !!}
                                                                                                {!! Form::close() !!}

                                                                                                {!! Form::open(['method'
                                                                                                => 'GET', 'route' =>
                                                                                                ['loan.destroy',
                                                                                                $checked->id],'id'=>'delete-form-'.$checked->id])
                                                                                                !!}
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
                                                                <div class="col-md-6 d-none" id="customer_details">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(Gate::check('granted loan'))
                                <div class="tab-pane fade fade @if(!Gate::check('pending loan') && !Gate::check('checked loan') && Gate::check('granted loan')) show active @endif"
                                    id="granted-loans" role="tabpanel" aria-labelledby="profile-tab3">
                                    <div class="company-setting-wrap">
                                        <div class="card-body" style="padding-left: 1px; padding-right: 1px;">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-body"
                                                            style="padding-left: 1px; padding-right: 1px;">
                                                            <div class="row">
                                                                <div id="table-1_wrapper"
                                                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                                                    <div class="table-responsive"
                                                                        style="overflow-x: auto;">
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <table class="table table-flush"
                                                                                    id="granted_vehicle_loan_table">
                                                                                    <thead class="thead-light">
                                                                                        <tr>
                                                                                            <th>{{__('Loan Id')}}</th>
                                                                                            <th> {{__('Customer No')}}
                                                                                            </th>
                                                                                            <th> {{__('Customer Name')}}
                                                                                            </th>
                                                                                            <th> {{__('Loan Amount')}}
                                                                                            </th>
                                                                                            <th> {{__('Outstanding Balance')}}
                                                                                            </th>
                                                                                            <th> {{__('Time Period')}}
                                                                                            </th>
                                                                                            <th> {{__('Collection Period')}}
                                                                                            </th>
                                                                                            <th> {{__('NIC')}}</th>
                                                                                            @if(Gate::check('disburse
                                                                                            loan') ||
                                                                                            Gate::check('reject loan'))
                                                                                            <th class="text-right">
                                                                                                {{__('Action')}}</th>
                                                                                            @endif
                                                                                        </tr>
                                                                                    </thead>

                                                                                    <tbody>
                                                                                        @foreach ($grantedLoan as
                                                                                        $granted)
                                                                                        <tr>
                                                                                            <td class="font-style">
                                                                                                <button type="button"
                                                                                                    class="btn btn-outline-primary view"
                                                                                                    value="{{ $granted->id }}">
                                                                                                    {{ \Auth::user()->loanNumberFormat($granted->id) }}
                                                                                                </button></td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->customerNumberFormat($granted->customer->id)}}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $granted->customer->name ?? '' }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->priceFormat($granted->loan_amount) }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->priceFormat($granted->current_due_amount) }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $granted->time_period }}
                                                                                                {{ __('Months') }} </td>
                                                                                            <td class="font-style">
                                                                                                @if
                                                                                                ($granted->time_range ==
                                                                                                1)
                                                                                                Daily
                                                                                                @elseif
                                                                                                ($granted->time_range ==
                                                                                                2)
                                                                                                Weekly
                                                                                                @elseif
                                                                                                ($granted->time_range ==
                                                                                                3)
                                                                                                Monthly
                                                                                                @endif
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $granted->customer->nic_no ?? '' }}
                                                                                            </td>
                                                                                            @if(Gate::check('disburse
                                                                                            loan') ||
                                                                                            Gate::check('reject loan'))
                                                                                            <td
                                                                                                class="action text-right">
                                                                                                @can('disburse loan')
                                                                                                <a href="#"
                                                                                                    data-size="xl"
                                                                                                    data-url="{{ route('loan.disburse_check',$granted->id) }}"
                                                                                                    data-ajax-popup="true"
                                                                                                    data-toggle="tooltip"
                                                                                                    data-title="{{__('Disburse Vehicle Loan')}}"
                                                                                                    class="btn btn-action btn-info">
                                                                                                    <i class="fas fa-check"
                                                                                                        style="padding-top: 5px;"></i>
                                                                                                </a>
                                                                                                @endcan
                                                                                                @can('reject loan')
                                                                                                <a href="#"
                                                                                                    class="btn btn-warning btn-action"
                                                                                                    style="padding-top: 10px;"
                                                                                                    data-toggle="tooltip"
                                                                                                    data-original-title="{{__('Reject')}}"
                                                                                                    data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                                                    data-confirm-yes="document.getElementById('reject-form-{{$granted->id}}').submit();">
                                                                                                    <i
                                                                                                        class="fas fa-times"></i>
                                                                                                </a>
                                                                                                {!! Form::open(['method'
                                                                                                => 'GET', 'route' =>
                                                                                                ['loan.reject',
                                                                                                $granted->id],'id'=>'reject-form-'.$granted->id])
                                                                                                !!}
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
                                                                <div class="col-md-6 d-none" id="customer_details">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(Gate::check('disbursed loan'))
                                <div class="tab-pane fade fade @if(!Gate::check('pending loan') && !Gate::check('checked loan') && !Gate::check('granted loan') && Gate::check('disbursed loan')) show active @endif"
                                    id="disbursed-loans" role="tabpanel" aria-labelledby="profile-tab3">
                                    <div class="company-setting-wrap">
                                        <div class="card-body" style="padding-left: 1px; padding-right: 1px;">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-body"
                                                            style="padding-left: 1px; padding-right: 1px;">
                                                            <div class="row">
                                                                <div id="table-1_wrapper"
                                                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                                                    <div class="table-responsive"
                                                                        style="overflow-x: auto;">
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <table class="table table-flush"
                                                                                    id="disbursed_vehicle_loan_table">
                                                                                    <thead class="thead-light">
                                                                                        <tr>
                                                                                            <th>{{__('Loan Id')}}</th>
                                                                                            <th> {{__('Customer No')}}
                                                                                            </th>
                                                                                            <th> {{__('Customer Name')}}
                                                                                            </th>
                                                                                            <th> {{__('Loan Amount')}}
                                                                                            </th>
                                                                                            <th> {{__('Outstanding Balance')}}
                                                                                            </th>
                                                                                            <th> {{__('Time Period')}}
                                                                                            </th>
                                                                                            <th> {{__('Collection Period')}}
                                                                                            </th>
                                                                                            <th> {{__('NIC')}}</th>
                                                                                            @if(Gate::check('view
                                                                                            loan'))
                                                                                            <th class="text-right">
                                                                                                {{__('Action')}}</th>
                                                                                            @endif
                                                                                        </tr>
                                                                                    </thead>

                                                                                    <tbody>
                                                                                        @foreach ($disbursedLoan as
                                                                                        $disbursed)
                                                                                        <tr>
                                                                                            <td class="font-style">
                                                                                                <button type="button"
                                                                                                    class="btn btn-outline-primary view"
                                                                                                    value="{{ $disbursed->id }}">
                                                                                                    {{ \Auth::user()->loanNumberFormat($disbursed->id) }}
                                                                                                </button></td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->customerNumberFormat($disbursed->customer->id)}}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $disbursed->customer->name ?? '' }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->priceFormat($disbursed->loan_amount) }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->priceFormat($disbursed->current_due_amount) }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $disbursed->time_period }}
                                                                                                {{ __('Months') }} </td>
                                                                                            <td class="font-style">
                                                                                                @if
                                                                                                ($disbursed->time_range
                                                                                                == 1)
                                                                                                Daily
                                                                                                @elseif
                                                                                                ($disbursed->time_range
                                                                                                == 2)
                                                                                                Weekly
                                                                                                @elseif
                                                                                                ($disbursed->time_range
                                                                                                == 3)
                                                                                                Monthly
                                                                                                @endif
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $disbursed->customer->nic_no ?? '' }}
                                                                                            </td>
                                                                                            @if(Gate::check('view
                                                                                            loan'))
                                                                                            <td
                                                                                                class="action text-right">
                                                                                                @can('view loan')
                                                                                                <a href="{{ url('loan/view',$disbursed->id) }}"
                                                                                                    data-toggle="tooltip"
                                                                                                    data-original-title="{{__('View')}}"
                                                                                                    class="btn btn-info btn-action">
                                                                                                    <i class="fas fa-eye"
                                                                                                        style="padding-top: 5px;"></i>
                                                                                                </a>
                                                                                                @endcan

                                                                                                {{-- @can('delete loan')
                                                                                                            <a href="#" class="btn btn-danger btn-action" style="padding-top: 10px;" data-toggle="tooltip" data-original-title="{{__('Delete')}}"
                                                                                                data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                                                data-confirm-yes="document.getElementById('delete-form-{{$disbursed->id}}').submit();">
                                                                                                <i
                                                                                                    class="fas fa-trash"></i>
                                                                                                </a>
                                                                                                {!! Form::open(['method'
                                                                                                => 'GET', 'route' =>
                                                                                                ['loan.destroy',
                                                                                                $disbursed->id],'id'=>'delete-form-'.$disbursed->id])
                                                                                                !!}
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
                                                                <div class="col-md-6 d-none" id="customer_details">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(Gate::check('rejected loan'))
                                <div class="tab-pane fade fade @if(!Gate::check('pending loan') && !Gate::check('checked loan') && !Gate::check('granted loan') && Gate::check('rejected loan')) active @endif"
                                    id="rejected-loans" role="tabpanel" aria-labelledby="profile-tab3">
                                    <div class="company-setting-wrap">
                                        <div class="card-body" style="padding-left: 1px; padding-right: 1px;">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-body"
                                                            style="padding-left: 1px; padding-right: 1px;">
                                                            <div class="row">
                                                                <div id="table-1_wrapper"
                                                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                                                    <div class="table-responsive"
                                                                        style="overflow-x: auto;">
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <table class="table table-flush"
                                                                                    id="rejected_vehicle_loan_table">
                                                                                    <thead class="thead-light">
                                                                                        <tr>
                                                                                            <th>{{__('Loan Id')}}</th>
                                                                                            <th> {{__('Customer No')}}
                                                                                            </th>
                                                                                            <th> {{__('Customer Name')}}
                                                                                            </th>
                                                                                            <th> {{__('Loan Amount')}}
                                                                                            </th>
                                                                                            <th> {{__('Outstanding Balance')}}
                                                                                            </th>
                                                                                            <th> {{__('Time Period')}}
                                                                                            </th>
                                                                                            <th> {{__('Collection Period')}}
                                                                                            </th>
                                                                                            <th> {{__('NIC')}}</th>
                                                                                            @if(Gate::check('view loan')
                                                                                            || Gate::check('delete
                                                                                            loan'))
                                                                                            <th class="text-right">
                                                                                                {{__('Action')}}</th>
                                                                                            @endif
                                                                                        </tr>
                                                                                    </thead>

                                                                                    <tbody>
                                                                                        @foreach ($rejectedLoan as
                                                                                        $rejected)
                                                                                        <tr>
                                                                                            <td class="font-style">
                                                                                                <button type="button"
                                                                                                    class="btn btn-outline-primary view"
                                                                                                    value="{{ $rejected->id }}">
                                                                                                    {{ \Auth::user()->loanNumberFormat($rejected->id) }}
                                                                                                </button></td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->customerNumberFormat($rejected->customer->id)}}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $rejected->customer->name ?? '' }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->priceFormat($rejected->loan_amount) }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->priceFormat($rejected->current_due_amount) }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $rejected->time_period }}
                                                                                                {{ __('Months') }} </td>
                                                                                            <td class="font-style">
                                                                                                @if
                                                                                                ($rejected->time_range
                                                                                                == 1)
                                                                                                Daily
                                                                                                @elseif
                                                                                                ($rejected->time_range
                                                                                                == 2)
                                                                                                Weekly
                                                                                                @elseif
                                                                                                ($rejected->time_range
                                                                                                == 3)
                                                                                                Monthly
                                                                                                @endif
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $rejected->customer->nic_no ?? '' }}
                                                                                            </td>
                                                                                            @if(Gate::check('view loan')
                                                                                            || Gate::check('delete
                                                                                            loan'))
                                                                                            <td
                                                                                                class="action text-right">
                                                                                                @can('view loan')
                                                                                                <button type="button"
                                                                                                    class="btn btn-info btn-action mr-1 view"
                                                                                                    title="View"
                                                                                                    value="{{ $rejected->id }}"><i
                                                                                                        class="fas fa-eye"></i></button>
                                                                                                @endcan
                                                                                                @can('delete loan')
                                                                                                <a href="#"
                                                                                                    class="btn btn-danger btn-action"
                                                                                                    style="padding-top: 10px;"
                                                                                                    data-toggle="tooltip"
                                                                                                    data-original-title="{{__('Delete')}}"
                                                                                                    data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                                                    data-confirm-yes="document.getElementById('delete-form-{{$rejected->id}}').submit();">
                                                                                                    <i
                                                                                                        class="fas fa-trash"></i>
                                                                                                </a>
                                                                                                {!! Form::open(['method'
                                                                                                => 'GET', 'route' =>
                                                                                                ['loan.destroy',
                                                                                                $rejected->id],'id'=>'delete-form-'.$rejected->id])
                                                                                                !!}
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
                                                                <div class="col-md-6 d-none" id="customer_details">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(Gate::check('closed loan'))
                                <div class="tab-pane fade @if(!Gate::check('pending loan') && !Gate::check('checked loan') && !Gate::check('granted loan') && !Gate::check('rejected loan') && Gate::check('closed loan')) show active @endif"
                                    id="closed-loans" role="tabpanel" aria-labelledby="contact-tab4">
                                    <div class="email-setting-wrap">
                                        <div class="card-body">
                                            @can('show report')
                                            <div class="row">
                                                <div class="col-sm-12 text-right">
                                                    {!! Form::open(['method' => 'POST', 'route' =>
                                                    ['closed_loan_report.search']]) !!}
                                                    <input type="hidden" name="loan_type" value="3">
                                                    <button type="submit" class="btn btn-outline-primary"
                                                        title="View"><i class="fas fa-angle-double-left"></i> View
                                                        Report</button>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                            @endcan
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div id="table-1_wrapper"
                                                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                                                    <div class="table-responsive">
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <table class="table table-flush"
                                                                                    id="closed_vehicle_loan_table">
                                                                                    <thead class="thead-light">
                                                                                        <tr>
                                                                                            <th>{{__('Loan Id')}}</th>
                                                                                            <th> {{__('Customer No')}}
                                                                                            </th>
                                                                                            <th> {{__('Customer Name')}}
                                                                                            </th>
                                                                                            <th> {{__('Loan Ammount')}}
                                                                                            </th>
                                                                                            <th> {{__('Time Period')}}
                                                                                            </th>
                                                                                            <th> {{__('Collection Period')}}
                                                                                            </th>
                                                                                            <th> {{__('NIC')}}</th>
                                                                                            @if(Gate::check('view
                                                                                            loan'))
                                                                                            <th class="text-right">
                                                                                                {{__('Action')}}</th>
                                                                                            @endif
                                                                                        </tr>
                                                                                    </thead>

                                                                                    <tbody>
                                                                                        @foreach ($closedLoan as
                                                                                        $closed)
                                                                                        <tr>
                                                                                            <td class="font-style">
                                                                                                <button type="button"
                                                                                                    class="btn btn-outline-primary view"
                                                                                                    value="{{ $closed->id }}">
                                                                                                    {{ \Auth::user()->loanNumberFormat($closed->id) }}
                                                                                                </button></td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->customerNumberFormat($closed->customer->id)}}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $closed->customer->name ?? '' }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ \Auth::user()->priceFormat($closed->loan_amount) }}
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $closed->time_period }}
                                                                                                {{ __('Months') }} </td>
                                                                                            <td class="font-style">
                                                                                                @if ($closed->time_range
                                                                                                == 1)
                                                                                                Daily
                                                                                                @elseif
                                                                                                ($closed->time_range ==
                                                                                                2)
                                                                                                Weekly
                                                                                                @elseif
                                                                                                ($closed->time_range ==
                                                                                                3)
                                                                                                Monthly
                                                                                                @endif
                                                                                            </td>
                                                                                            <td class="font-style">
                                                                                                {{ $closed->customer->nic_no ?? '' }}
                                                                                            </td>
                                                                                            @if(Gate::check('view
                                                                                            loan'))
                                                                                            <td
                                                                                                class="action text-right">
                                                                                                @can('view loan')
                                                                                                <button type="button"
                                                                                                    class="btn btn-info btn-action mr-1 view"
                                                                                                    title="View"
                                                                                                    value="{{ $closed->id }}"><i
                                                                                                        class="fas fa-eye"></i></button>
                                                                                                @endcan

                                                                                                {{-- @can('delete loan')
                                                                                                            <a href="#" class="btn btn-danger btn-action" style="padding-top: 10px;" data-toggle="tooltip" data-original-title="{{__('Delete')}}"
                                                                                                data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                                                data-confirm-yes="document.getElementById('delete-form-{{$closed->id}}').submit();">
                                                                                                <i
                                                                                                    class="fas fa-trash"></i>
                                                                                                </a>
                                                                                                {!! Form::open(['method'
                                                                                                => 'GET', 'route' =>
                                                                                                ['loan.destroy',
                                                                                                $closed->id],'id'=>'delete-form-'.$closed->id])
                                                                                                !!}
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
                                                                <div class="col-md-6 d-none" id="customer_details">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
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
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');

        // Pending
        if (localStorage.getItem("pending_vehicle_loan_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("pending_vehicle_loan_page_no"));
        }
        var pendingVehicleLoanTable = $('#pending_vehicle_loan_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        pendingVehicleLoanTable.page(pageNo).draw(false);

        $('#pending_vehicle_loan_table').on('page.dt', function () {
            var pageInfo = pendingVehicleLoanTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("pending_vehicle_loan_page_no", pageNo);
        });

        // Checked
        if (localStorage.getItem("checked_vehicle_loan_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("checked_vehicle_loan_page_no"));
        }
        var checkedVehicleLoanTable = $('#checked_vehicle_loan_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        checkedVehicleLoanTable.page(pageNo).draw(false);

        $('#checked_vehicle_loan_table').on('page.dt', function () {
            var pageInfo = checkedVehicleLoanTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("checked_vehicle_loan_page_no", pageNo);
        });

        // Granted
        if (localStorage.getItem("granted_vehicle_loan_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("granted_vehicle_loan_page_no"));
        }
        var grantedVehicleLoanTable = $('#granted_vehicle_loan_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        grantedVehicleLoanTable.page(pageNo).draw(false);

        $('#granted_vehicle_loan_table').on('page.dt', function () {
            var pageInfo = grantedVehicleLoanTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("granted_vehicle_loan_page_no", pageNo);
        });

        // Disbursed
        if (localStorage.getItem("disbursed_vehicle_loan_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("disbursed_vehicle_loan_page_no"));
        }
        var disbursedVehicleLoanTable = $('#disbursed_vehicle_loan_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        disbursedVehicleLoanTable.page(pageNo).draw(false);

        $('#disbursed_vehicle_loan_table').on('page.dt', function () {
            var pageInfo = disbursedVehicleLoanTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("disbursed_vehicle_loan_page_no", pageNo);
        });

        // rejected
        if (localStorage.getItem("rejected_vehicle_loan_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("rejected_vehicle_loan_page_no"));
        }
        var rejectedVehicleLoanTable = $('#rejected_vehicle_loan_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        rejectedVehicleLoanTable.page(pageNo).draw(false);

        $('#rejected_vehicle_loan_table').on('page.dt', function () {
            var pageInfo = rejectedVehicleLoanTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("rejected_vehicle_loan_page_no", pageNo);
        });

        // closed
        if (localStorage.getItem("closed_vehicle_loan_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("closed_vehicle_loan_page_no"));
        }
        var closedVehicleLoanTable = $('#closed_vehicle_loan_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        closedVehicleLoanTable.page(pageNo).draw(false);

        $('#closed_vehicle_loan_table').on('page.dt', function () {
            var pageInfo = closedVehicleLoanTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("closed_vehicle_loan_page_no", pageNo);
        });

        // console.log(sales);
        $('body').on('click', '.view', function () {
            var value = $(this).val();
            window.location.href = "{{ url('loan/view') }}/" + value;
        });

        // $('.approve').click(function(){
        //     var value = $(this).val();
        //     window.location.href = "{{ url('loan/approve') }}/"+value;
        // });

        // $('.reject').click(function(){
        //     var value = $(this).val();
        //     window.location.href = "{{ url('loan/reject') }}/"+value;
        // });
    });

</script>
