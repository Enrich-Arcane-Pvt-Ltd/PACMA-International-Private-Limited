@extends('layouts.admin')
@section('page-title')
    {{ __('Hire Sales Contracts') }}
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

        .table-responsive {
            max-width: 100%;
            /* Ensures the table doesn't exceed the width of its container */
            overflow-x: auto;
            /* overflow-y: auto; Adds vertical scroll */
            max-height: 100%;
            /* Adjust the height as needed */
        }

        table {
            width: 100%;
            /* Ensures the table occupies the full width of the container */
            border-collapse: collapse;
            /* Optional, keeps borders clean */
        }
    </style>

    @if (
        \Auth::user()->type == 'Cash Collector' ||
            \Auth::user()->type == 'Branch Manager' ||
            \Auth::user()->type ==
                'Field
                                                Manager' ||
            \Auth::user()->type == 'Center Manager')
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
        <h1>{{__('Hire Sales Contracts')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Hire Sales Contracts')}}</div>
    </div>
    </div> --}}

        <div class="section-body">
            <div class="row justify-content-center">


                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2">{{ __('Hire Sales Contracts') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Hire Sales Contracts') }}</div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 phone-col-div">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Manage Hire Sales Contracts') }}</h4>
                                {{-- @can('create sale')
                            <div class="col-auto">
                                <a href="#" data-url="{{ route('sale.create') }}" data-ajax-popup="true"
                            data-title="{{__('Create New Sale')}}" class="btn btn-icon icon-left btn-primary">
                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                            <span class="btn-inner--text"> {{__('Create')}}</span>
                            </a>
                        </div>
                        @endcan --}}
                            </div>
                        </div>
                        <div class="card-body phone-card-bd">
                            {{-- <div class="card-body p-0"> --}}
                            <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-flush" id="hire_purchase_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            {{-- <th> {{__('Contract #')}}</th> --}}
                                                            <th> {{ __('Sale #') }}</th>
                                                            <th> {{ __('Invoice #') }}</th>
                                                            <th style="width: 12%"> {{ __('Date Of Sale') }}</th>
                                                            {{-- <th> {{__('Customer #')}}</th> --}}
                                                            <th style="width: 10%"> {{ __('Customer') }}</th>
                                                            <th> {{ __('NIC') }}</th>
                                                            <th class="text-right">
                                                                {{ __('Outstanding Amount') }} ({{ $currencySymbol }})
                                                            </th>
                                                            <th class="text-center"> {{ __('Status') }}</th>
                                                            <th class="text-center"> {{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($contracts as $contract)
                                                            <tr>
                                                                <td class="font-style">
                                                                    <a href="{{ url('hire_purchase_contract/view', $contract->id) }}"
                                                                        class="btn btn-outline-primary">{{ $contract->sale_id }}</a>
                                                                </td>
                                                                <td class="font-style"><button type="button"
                                                                        class="btn btn-outline-primary invoice-view"
                                                                        value="{{ $contract->invoice_id }}">
                                                                        {{ $contract->invoice['invoice_code'] ?? '' }}
                                                                    </button></td>
                                                                <td class="font-style">
                                                                    {{ Auth::user()->dateFormat($contract->date_of_sale) }}
                                                                </td>
                                                                <td class="font-style">
                                                                    {{ $contract->customer->name ?? '--' }} </td>
                                                                <td class="font-style text-left">
                                                                    {{ $contract->customer->nic_no ?? '' }} </td>
                                                                <td class="font-style text-right">
                                                                    {{-- {{ __('Rs.') }} --}}
                                                                    {{ number_format($contract->current_due_amount ?? 0, 2) }}
                                                                </td>
                                                                <td class="font-style text-center">
                                                                    @if ($contract->status == 1)
                                                                        <span class="badge badge-success">Paid</span>
                                                                    @elseif ($contract->status == 2)
                                                                        <span class="badge badge-warning">Partialy
                                                                            Paid</span>
                                                                    @elseif ($contract->status == 3)
                                                                        <span class="badge badge-danger">Over Due</span>
                                                                    @endif
                                                                </td>
                                                                <td class="action text-center">
                                                                    @can('view sale')
                                                                        <a href="{{ url('hire_purchase_contract/view', $contract->id) }}"
                                                                            class="btn btn-info btn-action mr-1" title="{{ __('View') }}"><i
                                                                                class="fas fa-eye"></i></a>
                                                                        <a href="{{ route('hire_purchase_contract.print', $contract->invoice_id) }}"
                                                                            target="_blank" class="btn btn-primary btn-action mr-1" title="{{ __('Print Contract') }}"><i
                                                                                class="fas fa-file-contract"></i></a>
                                                                        <div class="btn-group">
                                                                            <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                <i class="fas fa-print mr-1"></i> {{ __('Forms') }}
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('pacma.forms.guarantee_indemnity', $contract->id) }}" target="_blank">
                                                                                    {{ __('Guarantee & Indemnity') }}
                                                                                </a>
                                                                                <a class="dropdown-item" href="{{ route('pacma.forms.guarantor_declaration', $contract->id) }}" target="_blank">
                                                                                    {{ __("Guarantor's Declaration") }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
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
                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
    // var contracts = <php echo json_encode($contracts) ?>;
    $(document).ready(function() {
        $(".se-pre-con").fadeOut('slow');

        // console.log(contracts);
        if (localStorage.getItem("hire_purchase_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("hire_purchase_page_no"));
        }
        var salesTable = $('#hire_purchase_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        salesTable.page(pageNo).draw(false);

        $('#hire_purchase_table').on('page.dt', function() {
            var pageInfo = salesTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("hire_purchase_page_no", pageNo);
        });

        $('body').on('click', '.view', function() {
            var value = $(this).val();
            window.location.href = "{{ url('hire_purchase_contract/view') }}/" + value;
        });
        $('body').on('click', '.invoice-view', function() {
            var invoiceId = $(this).val();
            window.location.href = "{{ url('invoice') }}/" + invoiceId;
        });
    });
</script>
