@extends('layouts.admin')
@section('page-title')
    {{ __('Hire Sales Contract Details') }}
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

        .text-product {
            margin-bottom: 0rem;
            line-height: 1.5;
        }

        .hr-class {
            border: 1px solid #a3a3a3;
            margin-bottom: -50px;
            margin-top: -100px;
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
            .phone-card-bd {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }

            .phone-section {
                margin-left: -25px;
                margin-right: -25px;
            }

            .table.table-md th,
            .table.table-md td {
                padding: 4px 7px;
            }
        </style>
    @endif

    <section class="section phone-section">
        <div class="se-pre-con"></div>

        {{-- <div class="section-header">
            <h1>{{__('View Hire Sales Contracts ')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item"><a
                href="{{ route('hire_purchase_contract.index') }}">{{__('Hire Sales Contracts')}}</a></div>
        <div class="breadcrumb-item">{{__('Hire Sales Contracts View')}}</div>
    </div>
    </div> --}}
        <style>

        </style>
        <div class="section-body">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">

                        <h4 class="mt-2 ">{{ __('View Hire Sales Contracts') }}</h4>


                        <div class="section-header-breadcrumb d-flex ">
                            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                            </div>
                            <div class="breadcrumb-item"><a
                                    href="{{ route('hire_purchase_contract.index') }}">{{ __('Hire Sales Contracts') }}</a>
                            </div>
                            <div class="breadcrumb-item">{{ __('Hire Sales Contracts View') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="invoice phone-card-bd br-4">
                <div class="sale-detail-print">
                    <div class="row">
                        <div class="col-12">
                            <div class="invoice-title row">
                                <div class="col-10">
                                    <h2>{{ __('Hire Sales Contract Details - ') }} {{ $sales->invoice['invoice_code'] }}</h2>
                                </div>
                                <div class="col-2 text-right">
                                    <a href="{{ route('hire_purchase_contract.print', $sales->invoice_id) }}" target="_blank"
                                        class="btn btn-primary btn-action float-right">
                                        <i class="fas fa-file-contract"></i> {{ __('Print Contract') }}
                                    </a>
                                </div>
                            </div>
                            <address>
                                {{-- <strong>{{__('Status')}}:</strong><br> --}}
                                @if ($sales->status == 1)
                                    <span class="badge badge-success">Paid</span>
                                @elseif($sales->status == 2)
                                    <span class="badge badge-warning">Partially Paid</span>
                                @elseif($sales->status == 3)
                                    <span class="badge badge-danger">Overdue</span>
                                @endif
                            </address>
                            <hr class="hr-class">
                            <div class="row">
                                @if (!empty($sales->customer))
                                    <div class="col-md-4">
                                        <address>
                                            <strong>{{ __('Delivery Address') }}:</strong><br>
                                            @if ($sales->customer->billing_title == 0)
                                                Mr.
                                            @elseif($sales->customer->billing_title == 1)
                                                Mrs.
                                            @else
                                                Miss.
                                            @endif
                                            {{ !empty($sales->customer->billing_name) ? $sales->customer->billing_name : '' }}<br>
                                            {{ !empty($sales->customer->billing_phone) ? $sales->customer->billing_phone : '' }}<br>
                                            @php
                                                $deliveryHouseNo = !empty($sales->customer->billing_house_no) ? trim($sales->customer->billing_house_no) : (!empty($sales->customer->house_no) ? trim($sales->customer->house_no) : '');
                                                $deliveryAddress = !empty($sales->customer->billing_address) ? trim($sales->customer->billing_address) : (!empty($sales->customer->address) ? trim($sales->customer->address) : '');
                                                $deliveryFullAddress = $deliveryAddress;
                                                if (!empty($deliveryHouseNo)) {
                                                    $cleanDeliveryHouseNo = trim($deliveryHouseNo, ", \t\n\r\0\x0B");
                                                    if (empty($deliveryAddress)) {
                                                        $deliveryFullAddress = $deliveryHouseNo;
                                                    } elseif ($cleanDeliveryHouseNo !== '' && stripos($deliveryAddress, $cleanDeliveryHouseNo) === false) {
                                                        $formattedDeliveryHouseNo = rtrim($cleanDeliveryHouseNo, ',') . ',';
                                                        $deliveryFullAddress = $formattedDeliveryHouseNo . ' ' . $deliveryAddress;
                                                    }
                                                }
                                            @endphp
                                            {{ !empty($deliveryFullAddress) ? $deliveryFullAddress : '' }}<br>
                                            {{ !empty($sales->customer->billing_email) ? $sales->customer->billing_email : '' }}
                                        </address>
                                    </div>
                                    <div class="col-md-4 ">
                                        <address>
                                            <strong>{{ __('Current Address') }}:</strong><br>
                                            @if ($sales->customer->title == 0)
                                                Mr.
                                            @elseif($sales->customer->title == 1)
                                                Mrs.
                                            @else
                                                Miss.
                                            @endif
                                            {{ !empty($sales->customer->name) ? $sales->customer->name : '' }}<br>
                                            {{ !empty($sales->customer->phone) ? $sales->customer->phone : '' }}<br>
                                            @php
                                                $currentHouseNo = !empty($sales->customer->house_no) ? trim($sales->customer->house_no) : '';
                                                $currentAddress = !empty($sales->customer->address) ? trim($sales->customer->address) : '';
                                                $currentFullAddress = $currentAddress;
                                                if (!empty($currentHouseNo)) {
                                                    $cleanCurrentHouseNo = trim($currentHouseNo, ", \t\n\r\0\x0B");
                                                    if (empty($currentAddress)) {
                                                        $currentFullAddress = $currentHouseNo;
                                                    } elseif ($cleanCurrentHouseNo !== '' && stripos($currentAddress, $cleanCurrentHouseNo) === false) {
                                                        $formattedCurrentHouseNo = rtrim($cleanCurrentHouseNo, ',') . ',';
                                                        $currentFullAddress = $formattedCurrentHouseNo . ' ' . $currentAddress;
                                                    }
                                                }
                                            @endphp
                                            {{ !empty($currentFullAddress) ? $currentFullAddress : '' }}<br>
                                            {{ !empty($sales->customer->nic_no) ? $sales->customer->nic_no : '' }}<br>
                                            {{ !empty($sales->customer->email) ? $sales->customer->email : '' }}
                                        </address>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-6 ">
                                                <address>
                                                    <strong>{{ __('Issue Date') }} :</strong><br>
                                                    {{ $sales->date_of_sale }}
                                                </address>
                                            </div>
                                            <div class="col-md-6 ">
                                                @if (isset($sales->due_date))
                                                    <address>
                                                        <strong>{{ __('Close Date') }} :</strong><br>
                                                        {{ $sales->due_date }}
                                                    </address>
                                                @endif
                                            </div>
                                        </div>
                                        @if (!empty($sales->introducer_id))
                                            <address>
                                                <strong>{{ __('Salesman') }}:</strong><br>
                                                {{ $introducer->name ?? '' }}
                                            </address>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    @if (!empty($sales->guarantor_id))
                                        <address>
                                            <strong>{{ __('Guarantor 1 ') }}:</strong><br>
                                            @if ($sales->guarantor1->title == 0)
                                                Mr.
                                            @elseif($sales->guarantor1->title == 1)
                                                Mrs.
                                            @else
                                                Miss.
                                            @endif
                                            {{ !empty($sales->guarantor1->name) ? $sales->guarantor1->name : '' }}<br>
                                            {{ !empty($sales->guarantor1->phone) ? $sales->guarantor1->phone : '' }}<br>
                                            @php
                                                $g1HouseNo = !empty($sales->guarantor1->house_no) ? trim($sales->guarantor1->house_no) : '';
                                                $g1Address = !empty($sales->guarantor1->address) ? trim($sales->guarantor1->address) : '';
                                                $g1FullAddress = $g1Address;
                                                if (!empty($g1HouseNo)) {
                                                    $cleanG1HouseNo = trim($g1HouseNo, ", \t\n\r\0\x0B");
                                                    if (empty($g1Address)) {
                                                        $g1FullAddress = $g1HouseNo;
                                                    } elseif ($cleanG1HouseNo !== '' && stripos($g1Address, $cleanG1HouseNo) === false) {
                                                        $formattedG1HouseNo = rtrim($cleanG1HouseNo, ',') . ',';
                                                        $g1FullAddress = $formattedG1HouseNo . ' ' . $g1Address;
                                                    }
                                                }
                                            @endphp
                                            {{ !empty($g1FullAddress) ? $g1FullAddress : '' }}<br>
                                            {{ !empty($sales->guarantor1->nic_no) ? $sales->guarantor1->nic_no : '' }}<br>
                                            {{ !empty($sales->guarantor1->email) ? $sales->guarantor1->email : '' }}
                                        </address>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    @if (!empty($sales->guarantor_2_id))
                                        <address>
                                            <strong>{{ __('Guarantor 2 ') }}:</strong><br>
                                            @if ($sales->guarantor2->title == 0)
                                                Mr.
                                            @elseif($sales->guarantor2->title == 1)
                                                Mrs.
                                            @else
                                                Miss.
                                            @endif
                                            {{ !empty($sales->guarantor2->name) ? $sales->guarantor2->name : '' }}<br>
                                            {{ !empty($sales->guarantor2->phone) ? $sales->guarantor2->phone : '' }}<br>
                                            @php
                                                $g2HouseNo = !empty($sales->guarantor2->house_no) ? trim($sales->guarantor2->house_no) : '';
                                                $g2Address = !empty($sales->guarantor2->address) ? trim($sales->guarantor2->address) : '';
                                                $g2FullAddress = $g2Address;
                                                if (!empty($g2HouseNo)) {
                                                    $cleanG2HouseNo = trim($g2HouseNo, ", \t\n\r\0\x0B");
                                                    if (empty($g2Address)) {
                                                        $g2FullAddress = $g2HouseNo;
                                                    } elseif ($cleanG2HouseNo !== '' && stripos($g2Address, $cleanG2HouseNo) === false) {
                                                        $formattedG2HouseNo = rtrim($cleanG2HouseNo, ',') . ',';
                                                        $g2FullAddress = $formattedG2HouseNo . ' ' . $g2Address;
                                                    }
                                                }
                                            @endphp
                                            {{ !empty($g2FullAddress) ? $g2FullAddress : '' }}<br>
                                            {{ !empty($sales->guarantor2->nic_no) ? $sales->guarantor2->nic_no : '' }}<br>
                                            {{ !empty($sales->guarantor2->email) ? $sales->guarantor2->email : '' }}
                                        </address>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="section-title">{{ __('Purchase Summary') }}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-responsive table-md">
                                    <thead>
                                        <tr>
                                            <th data-width="40">{{ __('Product code') }}</th>
                                            <th>{{ __('Product Discription') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Purchasing Price') }} ({{ $currencySymbol }})</th>
                                            <th>{{ __('Purchase Type') }}</th>
                                            <th>{{ __('Discount') }}</th>
                                            <th>{{ __('Down Payment Rate') }}</th>
                                            <th>{{ __('Installment Time Period') }}</th>
                                            <th>{{ __('Interest Rate') }}</th>
                                            <th class="text-right" width="12%">{{ __('Net Amount') }}
                                                ({{ $currencySymbol }})</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="white-space: nowrap;">
                                                <p class="text-product">{{ $sales->products->barcode }}</p>
                                                <p class="text-product">{{ $sales->products->serial_number }}</p>
                                            </td>
                                            <td>{{ $sales->products->name ?? '' }}</td>
                                            <td>{{ $sales->quantity }}</td>
                                            <td>{{ number_format($sales->unit_price, 2) }}</td>
                                            <td>
                                                @if ($sales->sale_type == 0)
                                                    {{ __('Cash Sales') }}
                                                @elseif ($sales->sale_type == 1)
                                                    {{ __('Hire Sales') }}
                                                @endif
                                            </td>
                                            <td>{{ $sales->discount }}</td>
                                            @if ($sales->sale_type == 1)
                                                <td>{{ $sales->products->product_category->down_payment ?? '' }}
                                                    {{ __('%') }}
                                                </td>
                                                <td>{{ $sales->installment }} {{ __('months') }}</td>
                                                <td>{{ $sales->interest_rate }} {{ __('%') }}</td>
                                            @endif
                                            <td class="text-right">
                                                {{ number_format($sales->net_amount, 2) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                    @if ($sales->sale_type == 1)
                                        <tfoot>
                                            <tr>
                                                <td colspan="10"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Down Payment') }}</b></td>
                                                <td colspan="2" class="text-right">
                                                    {{ number_format($sales->down_payment, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Interest Amount') }}</b>
                                                </td>
                                                <td colspan="2" class="text-right">
                                                    {{ number_format($sales->interest_amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td colspan="2" class="text-right">
                                                    <b>{{ __('Monthly Installment') }}</b>
                                                </td>
                                                <td colspan="2" class="text-right">
                                                    {{ number_format($sales->monthly_installment, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td colspan="2" class="text-right">
                                                    <b>{{ __('Gross Hire Price') }}</b>
                                                    {{-- <b>{{__('Total of Investment Schedule for Payment')}}</b> --}}
                                                </td>
                                                <td colspan="2" class="text-right">
                                                    {{ number_format($sales->gross_hire_price, 2) }}
                                                    {{-- {{ number_format($sales->total_due_amount, 2) }} --}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td colspan="2" class="text-right">
                                                    <b>{{ __('Outstanding Balance') }}</b>
                                                </td>
                                                <td colspan="2" class="text-right">
                                                    {{ number_format($sales->current_due_amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Document Charges') }}</b>
                                                </td>
                                                <td colspan="2" class="text-right">
                                                    {{ number_format($sales->document_charges, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{ __('Receipt Summary') }}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <tr>
                                        <th>{{ __('Receipt') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        {{-- <th>{{__('Amount')}}</th>
                                    <th>{{__('Extra Charges')}}</th> --}}
                                        <th class="text-right">{{ __('Total Amount to be Paid') }}</th>
                                        <th class="text-right">{{ __('Paid Amount') }}</th>
                                        <th>{{ __('Collected By') }}</th>
                                        <th>{{ __('Paid Date') }}</th>
                                        <th>{{ __('Due Date') }}</th>
                                        <th class="text-right">{{ __('Action') }}</th>
                                    </tr>
                                    @foreach ($sales->receipts as $receipt)
                                        @if ($receipt->description !== 'Document Charges')
                                            @if ($receipt->description == 'Down Payment')
                                                <tr>
                                                    <td class="font-style"><button type="button"
                                                            class="btn btn-outline-primary view"
                                                            value="{{ $receipt->id }}">
                                                            {{ $receipt->receipt_code }} </button></td>
                                                    <td>{{ $receipt->description }}</td>
                                                    {{-- <td>{{ $receipt->installment_amount }}</td>
                                                <td>{{ $receipt->extra_charges ?? '--' }}</td> --}}
                                                    <td class="text-right">
                                                        {{ number_format($receipt->total_amount_to_pay + $receipt->sales->receipts->where('description', 'Document Charges')->sum('total_amount_to_pay'), 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($receipt->paid_amount + $receipt->sales->receipts->where('description', 'Document Charges')->sum('paid_amount'), 2) }}
                                                    </td>
                                                    <td>{{ Auth::user()->otherUserName($receipt->created_by) }}</td>
                                                    <td>{{ $receipt->issue_date }}</td>
                                                    <td>{{ $receipt->installment_due_date }}</td>
                                                    @if (Gate::check('print receipt') || Gate::check('collect receipt'))
                                                        <td class="text-right">
                                                            @can('print receipt')
                                                                <button type="button" class="btn btn-info btn-action mr-1 view"
                                                                    title="View" value="{{ $receipt->id }}"><i
                                                                        class="fas fa-eye"></i></button>
                                                                <button type="button"
                                                                    class="btn btn-info btn-action mr-1 print" title="Print"
                                                                    value="{{ $receipt->id }}"><i
                                                                        class="fas fa-print"></i></button>
                                                            @endcan
                                                            @can('collect receipt')
                                                                <a href="{{ route('hire_purchase_contract.cash_collector_receipt', $receipt->id) }}"
                                                                    target="_blank" class="btn btn-info btn-action mr-1"
                                                                    title="Cash Collector Receipt Print"><i
                                                                        class="fas fa-mobile mt-1"></i></a>
                                                            @endcan
                                                        </td>
                                                    @endif
                                                </tr>
                                            @else
                                                <tr>
                                                    <td class="font-style"><button type="button"
                                                            class="btn btn-outline-primary view"
                                                            value="{{ $receipt->id }}">
                                                            {{ $receipt->receipt_code }} </button></td>
                                                    <td>{{ $receipt->description }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($receipt->total_amount_to_pay, 2) }}
                                                    </td>
                                                    <td class="text-right">{{ number_format($receipt->paid_amount, 2) }}
                                                    </td>
                                                    <td>{{ Auth::user()->otherUserName($receipt->created_by) }}</td>
                                                    <td>{{ $receipt->issue_date }}</td>
                                                    <td>{{ $receipt->installment_due_date }}</td>
                                                    @if (Gate::check('print receipt') || Gate::check('collect receipt'))
                                                        <td class="text-right">
                                                            @can('print receipt')
                                                                <button type="button"
                                                                    class="btn btn-info btn-action mr-1 view" title="View"
                                                                    value="{{ $receipt->id }}"><i
                                                                        class="fas fa-eye"></i></button>
                                                                <button type="button"
                                                                    class="btn btn-info btn-action mr-1 print" title="Print"
                                                                    value="{{ $receipt->id }}"><i
                                                                        class="fas fa-print"></i></button>
                                                            @endcan
                                                            @can('collect receipt')
                                                                <a href="{{ route('hire_purchase_contract.cash_collector_receipt', $receipt->id) }}"
                                                                    target="_blank" class="btn btn-info btn-action mr-1"
                                                                    title="Cash Collector Receipt Print"><i
                                                                        class="fas fa-mobile mt-1"></i></a>
                                                            @endcan
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endif
                                        @endif
                                    @endforeach
                                    {{-- <td class="text-right">

                                                <a href="#!" class="btn btn-danger btn-action" data-toggle="tooltip" data-original-title="{{__('Delete')}}"
                                    data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                    data-confirm-yes="document.getElementById('delete-form-{{$payment->id}}').submit();">
                                    <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'post', 'route' =>
                                    ['invoice.payment.destroy',$invoice->id,$payment->id],'id'=>'delete-form-'.$payment->id])
                                    !!}
                                    {!! Form::close() !!}

                                    </td> --}}

                                    @foreach ($sales->installments as $key => $installment)
                                        @foreach ($installment->installmentAdvances as $installmentAdvance)
                                            <tr>
                                                <td class="font-style"><button type="button"
                                                        class="btn btn-outline-primary view"
                                                        value="{{ $installmentAdvance->receipts->id }}">
                                                        {{ $installmentAdvance->receipts->receipt_code }} </button>
                                                </td>
                                                <td>{{ $installmentAdvance->receipts->description ?? '' }}</td>
                                                <td></td>
                                                <td>{{ number_format($installmentAdvance->receipts->paid_amount, 2) }}
                                                </td>
                                                <td>{{ Auth::user()->otherUserName($installmentAdvance->receipts->created_by) }}
                                                </td>
                                                <td>{{ $installmentAdvance->receipts->issue_date }}</td>
                                                <td></td>
                                                @if (Gate::check('print receipt') || Gate::check('collect receipt'))
                                                    <td class="text-right">
                                                        @can('print receipt')
                                                            <button type="button" class="btn btn-info btn-action mr-1 view"
                                                                title="View"
                                                                value="{{ $installmentAdvance->receipts->id }}"><i
                                                                    class="fas fa-eye"></i></button>
                                                            <button type="button" class="btn btn-info btn-action mr-1 print"
                                                                title="Print"
                                                                value="{{ $installmentAdvance->receipts->id }}"><i
                                                                    class="fas fa-print"></i></button>
                                                        @endcan
                                                        @can('collect receipt')
                                                            <a href="{{ route('hire_purchase_contract.cash_collector_receipt', $installmentAdvance->receipts->id) }}"
                                                                target="_blank" class="btn btn-info btn-action mr-1"
                                                                title="Cash Collector Receipt Print"><i
                                                                    class="fas fa-mobile mt-1"></i></a>
                                                        @endcan
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{ __('Installment Due Dates') }}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <tr>
                                        <th class="text-center">{{ __('Installment Due Dates') }}</th>
                                        <th class="text-center">{{ __('Installment Dates') }}</th>
                                        <th class="text-center">{{ __('Installment Amount') }}</th>
                                        <th class="text-center">{{ __('Excess Amount') }}</th>
                                        <th class="text-center">{{ __('Amount to be Paid') }}</th>
                                        <th class="text-center">{{ __('Paid Amount') }}</th>
                                        <th class="text-center">{{ __('Total Due Amount') }}</th>
                                        <th class="text-center">{{ __('Description') }}</th>
                                        @if (Gate::check('pay sale') || Gate::check('cancel installment sale'))
                                            <th class="text-right">{{ __('Action') }}</th>
                                        @endif
                                    </tr>
                                    @php
                                        $lastInst = $sales->lastInstallment->id ?? '';
                                    @endphp
                                    @foreach ($sales->installments as $key => $installment)
                                        <tr>
                                            <td class="text-center" style="white-space: nowrap;">
                                                {{ $installment->installment_date }}</td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                {{ $installment->paid_date }}
                                            </td>
                                            <td class="text-center">
                                                {{-- @if ($installment->released_amount != null)
                                                    @if ($installment->fine != null)
                                                        {{ $installment->amount }}<span class="text-primary"> -
                                            {{ $installment->released_amount }}</span><span class="text-danger"> +
                                            {{ $installment->fine }}</span>
                                        @else
                                        {{ $installment->amount }}<span class="text-primary"> -
                                            {{ $installment->released_amount }}</span>
                                        @endif
                                        @else
                                        @if ($installment->fine != null)
                                        {{ $installment->amount }}<span class="text-danger"> +
                                            {{ $installment->fine }}</span>
                                        @else
                                        {{ $installment->amount }}
                                        @endif
                                        @endif --}}

                                                {{-- {{ number_format($installment->amount, 2) }} @if ($installment->released_amount != null) <span class="text-primary"> -
                                            {{ number_format($installment->released_amount, 2) }}</span> @endif @if ($installment->fine != null)<span class="text-danger"> +
                                            {{ number_format($installment->fine, 2) }}</span> @endif @if ($installment->remainder_amount != null)<span class="text-danger"> +
                                            {{ number_format($installment->remainder_amount, 2) }}</span> @endif --}}
                                                {{ number_format($installment->amount, 2) }} @if ($installment->fine != null)
                                                    <span class="text-danger"> +
                                                        {{ number_format($installment->fine, 2) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($installment->released_amount == null && $installment->remainder_amount == null)
                                                    --
                                                @else
                                                    @if ($installment->released_amount != null)
                                                        <span class="text-primary"> -
                                                            {{ number_format($installment->released_amount, 2) }}</span>
                                                        <br>
                                                    @endif
                                                    @if ($installment->remainder_amount != null)
                                                        <span class="text-danger"> +
                                                            {{ number_format($installment->remainder_amount, 2) }}</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($installment->total_amount_to_pay, 2) }}

                                                {{-- @endif --}}
                                                {{-- @php
                                                    $fine = 0;
                                                    $released = 0;
                                                    if($installment->released_amount != null){
                                                        $released = $installment->released_amount;
                                                    }
                                                    if($installment->fine != null){
                                                        $fine = $installment->fine;
                                                    }
                                                    $totAmount = $installment->amount + $fine - $released;
                                                    if ($totAmount < 0) {
                                                        echo '0';
                                                    } else {
                                                        echo $totAmount;
                                                    }

                                                @endphp --}}
                                            </td>
                                            <td class="text-center">
                                                @if ($installment->paid_amount != null)
                                                    {{ number_format($installment->paid_amount, 2) }}
                                                @elseif ($installment->installmentAdvances->sum('advance_amount'))
                                                    {{ number_format($installment->installmentAdvances->sum('advance_amount'), 2) }}
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($installment->paid_amount != null)
                                                    @if ($installment->total_due_amount != null)
                                                        {{ number_format($installment->total_due_amount, 2) }}
                                                    @else
                                                        --
                                                    @endif
                                                @else
                                                    @if ($installment->total_due_amount != null)
                                                        {{ number_format($installment->total_due_amount - $installment->installmentAdvances->sum('advance_amount'), 2) }}
                                                    @else
                                                        --
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($installment->status == 1)
                                                    <span class="badge badge-success">Paid</span>
                                                @elseif ($installment->status == 2)
                                                    <span class="badge badge-primary">Not Paid</span>
                                                @elseif ($installment->status == 3)
                                                    <span class="badge badge-danger">Over Due</span>
                                                @endif
                                            </td>
                                            @if (Gate::check('pay sale') || Gate::check('cancel installment sale'))
                                                <td class="text-right">
                                                    @can('pay sale')
                                                        @if ($installment->status != 1)
                                                            <a href="#" class="btn btn-danger btn-action mr-1"
                                                                style="padding-top: 10px;"
                                                                data-url="{{ route('hire_purchase_contract.pay_installment', $installment->id) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{ __('Pay Installment') }}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('Pay') }}">
                                                                {{ __('Pay') }}
                                                            </a>
                                                        @endif
                                                    @endcan
                                                    @can('cancel installment sale')
                                                        @if ($installment->status == 1 && $installment->id == $lastInst)
                                                            <a href="#" class="btn btn-danger btn-action"
                                                                style="padding-top: 10px;" data-toggle="tooltip"
                                                                data-original-title="{{ __('Cancel Payment') }}"
                                                                data-confirm="{{ __('Are You Sure?') . ' | ' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('cancel-form-{{ $installment->id }}').submit();">
                                                                <i class="fas fa-undo-alt"></i>
                                                            </a>
                                                            {!! Form::open([
                                                                'method' => 'GET',
                                                                'route' => ['hire_purchase_contract.cancel_installment', $installment->id],
                                                                'id' => 'cancel-form-' . $installment->id,
                                                            ]) !!}
                                                            {!! Form::close() !!}
                                                        @endif
                                                    @endcan
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </table>
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
    $(document).ready(function() {
        $(".se-pre-con").fadeOut('slow');

        $('.view').click(function() {
            var value = $(this).val();
            url = "{{ url('hire_purchase_contract/receipt/print') }}/" + value;
            window.open(url, '_blank');
        });

        $('.print').click(function() {
            var value = $(this).val();
            url = "{{ url('hire_purchase_contract/receipt/print') }}/" + value;
            window.open(url, '_blank');
        });
    });
</script>
