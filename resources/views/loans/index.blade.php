@extends('layouts.admin')
@section('page-title')
{{ __('Loan View ') }}
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
        $('#invoice_frame').attr('src', '{{ url(' / invoices / preview ') }}/' + template + '/' + color);
    });

    $(document).on("change", "select[name='proposal_template'], input[name='proposal_color']", function () {
        var template = $("select[name='proposal_template']").val();
        var color = $("input[name='proposal_color']:checked").val();
        $('#proposal_frame').attr('src', '{{ url(' / proposal / preview ') }}/' + template + '/' + color);
    });

    $(document).on("change", "select[name='bill_template'], input[name='bill_color']", function () {
        var template = $("select[name='bill_template']").val();
        var color = $("input[name='bill_color']:checked").val();
        $('#bill_frame').attr('src', '{{ url(' / bill / preview ') }}/' + template + '/' + color);
    });

</script>
<script src="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Loan Details')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Loan Details')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Loan Details') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Loan Details') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{ __('Loan Details') }}</h4>
                            <div class="card-header-action">
                                @can('create bill')
                                <a href="#" data-url="{{ route('loanCreate') }}" data-ajax-popup="true"
                                    data-title="{{ __('Create New Sale') }}" class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{ __('Create') }}</span>
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="setting-tab">
                            <ul class="nav nav-pills nav-fill" id="myTab3" role="tablist">


                                <li class="nav-item">
                                    <a class="nav-link active" id="contact-tab4" data-toggle="tab" href="#item-details"
                                        role="tab" aria-controls="" aria-selected="false">{{ __('Pending Loans') }}</a>
                                </li>


                                <li class="nav-item">
                                    <a class="nav-link" id="contact-tab4" data-toggle="tab"
                                        href="#hire-purchase-details" role="tab" aria-controls=""
                                        aria-selected="false">{{ __('Granted Loans') }}</a>
                                </li>


                                <li class="nav-item">
                                    <a class="nav-link" id="profile-tab3" data-toggle="tab" href="#warrent-details"
                                        role="tab" aria-controls="" aria-selected="false">{{ __('Rejected Loans') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="profile-tab3" data-toggle="tab" href="#warrent-closed"
                                        role="tab" aria-controls="" aria-selected="false">{{ __('Closed Loans') }}</a>
                                </li>


                            </ul>

                            <div class="tab-content" id="myTabContent2">
                                <div class="tab-pane fade fade show active" id="item-details" role="tabpanel"
                                    aria-labelledby="profile-tab3">
                                    <div class="company-setting-wrap">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card">
                                                        {{-- <div class="card-header">
                                                            <div class="d-flex justify-content-between w-100">
                                                                <h4>{{__('Manage Loan')}}</h4>
                                                        <div class="card-header-action">
                                                            @can('create vender')
                                                            <a href="#">
                                                                <span class="btn-inner--icon"><i
                                                                        class="fas fa-plus"></i></span>
                                                                <span class="btn-inner--text"> {{__('Create')}}</span>
                                                            </a>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div id="table-1_wrapper"
                                                            class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                                            <div class="table-responsive">
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <table class="table table-flush" id="dataTable">
                                                                            <thead class="thead-light">
                                                                                <tr>
                                                                                    <th>{{ __('Loan Id') }}
                                                                                    </th>
                                                                                    <th> {{ __('Customer Name') }}
                                                                                    </th>
                                                                                    <th> {{ __('Loan Name') }}
                                                                                    </th>
                                                                                    <th> {{ __('Loan Ammount') }}
                                                                                    </th>
                                                                                    <th> {{ __('Time Period') }}
                                                                                    </th>
                                                                                    <th> {{ __('Repayment') }}
                                                                                    </th>
                                                                                    <th> {{ __('NIC') }}
                                                                                    </th>
                                                                                    <th class="text-right">
                                                                                        {{ __('Action') }}
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>

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

                        <div class="tab-pane fade fade" id="hire-purchase-details" role="tabpanel"
                            aria-labelledby="profile-tab3">
                            <div class="company-setting-wrap">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                {{-- <div class="card-header">
                                                            <div class="d-flex justify-content-between w-100">
                                                                <h4>{{__('Manage Loan')}}</h4>
                                                <div class="card-header-action">
                                                    @can('create vender')
                                                    <a href="#">
                                                        <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                                        <span class="btn-inner--text"> {{__('Create')}}</span>
                                                    </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="card-body">
                                            <div class="row">
                                                <div id="table-1_wrapper"
                                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                                    <div class="table-responsive">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <table class="table table-flush" id="dataTable2">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                            <th>{{ __('Loan Id') }}
                                                                            </th>
                                                                            <th> {{ __('Customer Name') }}
                                                                            </th>
                                                                            <th> {{ __('Loan Name') }}
                                                                            </th>
                                                                            <th> {{ __('Loan Ammount') }}
                                                                            </th>
                                                                            <th> {{ __('Time Period') }}
                                                                            </th>
                                                                            <th> {{ __('Repayment') }}
                                                                            </th>
                                                                            <th> {{ __('NIC') }}
                                                                            </th>
                                                                            <th class="text-right">
                                                                                {{ __('Action') }}
                                                                            </th>
                                                                        </tr>
                                                                    </thead>

                                                                    <tbody>

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
                <div class="tab-pane fade fade" id="warrent-details" role="tabpanel" aria-labelledby="profile-tab3">
                    <div class="company-setting-wrap">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        {{-- <div class="card-header">
                                                            <div class="d-flex justify-content-between w-100">
                                                                <h4>{{__('Manage Loan')}}</h4>
                                        <div class="card-header-action">
                                            @can('create vender')
                                            <a href="#">
                                                <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                                <span class="btn-inner--text"> {{__('Create')}}</span>
                                            </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="card-body">
                                    <div class="row">
                                        <div id="table-1_wrapper"
                                            class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                            <div class="table-responsive">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <table class="table table-flush" id="dataTable3">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>{{ __('Loan Id') }}
                                                                    </th>
                                                                    <th> {{ __('Customer Name') }}
                                                                    </th>
                                                                    <th> {{ __('Loan Name') }}
                                                                    </th>
                                                                    <th> {{ __('Loan Ammount') }}
                                                                    </th>
                                                                    <th> {{ __('Time Period') }}
                                                                    </th>
                                                                    <th> {{ __('Repayment') }}
                                                                    </th>
                                                                    <th> {{ __('NIC') }}
                                                                    </th>
                                                                    <th class="text-right">
                                                                        {{ __('Action') }}
                                                                    </th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>

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
        <div class="tab-pane fade" id="warrent-closed" role="tabpanel" aria-labelledby="contact-tab4">
            <div class="email-setting-wrap">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                {{-- <div class="card-header">
                                                            <div class="d-flex justify-content-between w-100">
                                                                <h4>{{__('Manage Loan')}}</h4>
                                <div class="card-header-action">
                                    @can('create vender')
                                    <a href="#">
                                        <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                        <span class="btn-inner--text"> {{__('Create')}}</span>
                                    </a>
                                    @endcan
                                </div>
                            </div>
                        </div> --}}
                        <div class="card-body">
                            <div class="row">
                                <div id="table-1_wrapper"
                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-flush" id="dataTable4">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>{{ __('Loan Id') }}
                                                            </th>
                                                            <th> {{ __('Customer Name') }}
                                                            </th>
                                                            <th> {{ __('Loan Name') }}
                                                            </th>
                                                            <th> {{ __('Loan Ammount') }}
                                                            </th>
                                                            <th> {{ __('Time Period') }}
                                                            </th>
                                                            <th> {{ __('Repayment') }}
                                                            </th>
                                                            <th> {{ __('NIC') }}
                                                            </th>
                                                            <th class="text-right">
                                                                {{ __('Action') }}
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>

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

    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
</section>
@endsection
