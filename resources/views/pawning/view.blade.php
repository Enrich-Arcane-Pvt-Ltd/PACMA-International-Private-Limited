@extends('layouts.admin')
@section('page-title')
    {{__('Pawning Detail')}}
@endsection

<style>
    .col-auto {
        margin-left: 75%;
    }

    .card-header {
        padding: 15px 0px !important;
    }
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

@if(\Auth::user()->type == 'Cash Collector' || \Auth::user()->type == 'Branch Manager' || \Auth::user()->type == 'Field Manager' || \Auth::user()->type == 'Center Manager')
    <style>
        .phone-card-bd{
            padding-left: 5px !important;
            padding-right: 5px !important;
        }
        .phone-section {
            margin-left: -25px;
            margin-right: -25px;
        }
    </style>
@endif

@section('content')
    <section class="section phone-section">
        <div class="se-pre-con"></div>

        <div class="section-header">
            <h1>{{__('View Pawnings')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('pawning.index') }}">{{__('Pawning')}}</a></div>
                <div class="breadcrumb-item">{{__('Pawning View')}}</div>
            </div>
        </div>
        <div class="section-body">
            <div class="invoice phone-card-bd">
                <div class="sale-detail-print">
                    <div class="row">
                        <div class="col-lg-12">
                            {{-- <div class="invoice-title"> --}}
                            <div class="card">
                                <div class="card-header">
                                    <h2>{{__('Pawning Details')}}</h2>
                                    <div class="col-auto">
                                        <a href="{{ route('pawning.ticket',$pawningDetails->id) }}" class="btn btn-icon icon-left btn-primary">
                                            <span class="btn-inner--text"> {{__('Pawning Ticket')}}</span>
                                        </a>
                                        {{-- @foreach($pawnings as $pawning) --}}
                                        {{-- <button type="button" class="btn btn-icon icon-left btn-primary" title="View" value="{{ $pawning->id }}"><span class="btn-inner--text"> {{__('View Pawning Ticket')}}</span></button> --}}
                                        {{-- @endforeach --}}
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            @if (isset($pawningDetails->cheque_id))
                                                @can('view cheque')
                                                    <button type="button" class="btn btn-outline-primary view_cheque" value="{{ $pawningDetails->cheque_id }}">
                                                        View Cheque - {{ AUth::user()->chequeNumberFormat($pawningDetails->cheque_id) }}
                                                    </button>
                                                @endcan
                                                @can('print cheque')
                                                    <button type="button" class="btn btn-primary print_cheque" value="{{ $pawningDetails->cheque_id }}"> Print Cheque </button>
                                                @endcan
                                            @endif
                                            @can('create payment voucher')
                                                <a href="#" data-size="xl" data-url="{{ route('pawning_payment_voucher.create',$pawningDetails->id) }}" data-ajax-popup="true" data-title="{{__('Create Payment Voucher')}}" class="btn btn-icon icon-left btn-primary"  style="float: right;">
                                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                                    <span class="btn-inner--text"> {{__('Create Payment Voucher')}}</span>
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mt-3 text-right">
                                            <a href="{{ route('pawning.reminder_one',$pawningDetails->id) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                <span class="btn-inner--text"> {{__('Pawning Reminder 1')}}</span>
                                            </a>
                                            <a href="{{ route('pawning.reminder_two',$pawningDetails->id) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                <span class="btn-inner--text"> {{__('Pawning Reminder 2')}}</span>
                                            </a>
                                            <a href="{{ route('pawning.reminder_three',$pawningDetails->id) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                <span class="btn-inner--text"> {{__('Pawning Reminder 3')}}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {{-- </div> --}}
                            <div class="row">
                                @if(!empty($pawningDetails->customer))
                                    <div class="col-md-6">
                                        <address class="font-style">
                                            <strong>{{__('Customer')}}:</strong><br>
                                            {{!empty($pawningDetails->customer->name)?$pawningDetails->customer->name:''}}<br>
                                            {{!empty($pawningDetails->customer->nic_no)?$pawningDetails->customer->nic_no:''}}<br>
                                            {{-- {{!empty($pawningDetails->customer->phone)?$pawningDetails->customer->phone:''}}<br>
                                            {{!empty($pawningDetails->customer->address)?$pawningDetails->customer->address:''}}<br> --}}
                                            {{!empty($pawningDetails->customer->email)?$pawningDetails->customer->email:''}}
                                            {{-- @foreach ($pawningDetails as $pawningDetail)
                                                {{ $pawningDetail->customer['name'] }}
                                            @endforeach --}}
                                        </address>
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        <address>
                                            <strong>{{__('Current Address')}}:</strong><br>
                                            {{-- {{!empty($pawningDetails->customer->name)?$pawningDetails->customer->name:''}}<br> --}}
                                            {{!empty($pawningDetails->customer->address)?$pawningDetails->customer->address:''}}<br>
                                            {{!empty($pawningDetails->customer->phone)?$pawningDetails->customer->phone:''}}<br>
                                            {{!empty($pawningDetails->customer->email)?$pawningDetails->customer->email:''}}
                                        </address>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <address>
                                        <strong>{{__('Status')}}:</strong><br>
                                        @if($pawningDetails->status == 1)
                                            <span class="badge badge-info">To be Paid</span>
                                        @elseif($pawningDetails->status == 2)
                                            <span class="badge badge-warning">Partialy Paid</span>
                                        @elseif($pawningDetails->status == 3)
                                            <span class="badge badge-danger">Over Due</span>
                                        @elseif($pawningDetails->status == 4)
                                            <span class="badge badge-success">Paid</span>
                                        @endif
                                    </address>
                                </div>
                                <div class="col-md-4 text-md-center">
                                    <address>
                                        <strong>{{__('Issue Date')}} :</strong><br>
                                        {{ $pawningDetails->issue_date }}<br><br>
                                    </address>
                                </div>
                                <div class="col-md-4 text-md-right">
                                    @if (isset($pawningDetails->due_date))
                                        <address>
                                            <strong>{{__('Close Date')}} :</strong><br>
                                            {{ $pawningDetails->due_date }}<br><br>
                                        </address>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{__('Pawning Summary')}}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <thead>
                                        <tr>
                                            {{-- <th data-width="40">#</th> --}}
                                            <th>{{__('Jewellery')}}</th>
                                            <th>{{__('Gold Type')}}</th>
                                            <th>{{__('Weight')}}</th>
                                            <th class="text-right">{{__('Amount Finance')}}</th>
                                            <th class="text-right">{{__('Amount of Payment')}}</th>
                                            <th class="text-right">{{__('Interest Amount')}}</th>
                                            <th class="text-right">{{__('Total of Payment')}}</th>
                                            <th class="text-right">{{__('Prepayment')}}</th>
                                            {{-- <th class="text-right" width="12%">{{__('Net Amount')}}</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pawnedItemDetails as $pawnedItemDetail)
                                        <tr>
                                            {{-- <td>{{ $pawnedItemDetail->pawning_id }}</td> --}}
                                            <td>{{ $pawnedItemDetail->jewellery->name ?? '' }}</td>
                                            <td>{{ $pawnedItemDetail->goldType->gold_rate_type ?? '' }}</td>
                                            <td>{{ $pawnedItemDetail->weight }}{{ 'g' }}</td>
                                            <td class="text-right">{{ 'Rs. ' }}{{ number_format($pawnedItemDetail->amount_financed, 2) }}</td>
                                            <td class="text-right">{{ 'Rs. ' }}{{ number_format($pawnedItemDetail->amount_payout, 2) }}</td>
                                            <td class="text-right">{{ 'Rs. ' }}{{ number_format($pawnedItemDetail->financed_charge, 2) }}</td>
                                            <td class="text-right">{{ 'Rs. ' }}{{ number_format($pawnedItemDetail->total_of_payment, 2) }}</td>
                                            <td class="text-right">{{ 'Rs. ' }}{{ number_format($pawnedItemDetail->prepayment, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    {{-- @if ($sales->sale_type == 1) --}}
                                        <tfoot>
                                            <tr>
                                                <td colspan="8"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2" class="text-right"><b>{{__('Total Amount Financed')}}</b></td>
                                                <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($pawningDetails->full_amount_financed, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2" class="text-right"><b>{{__('Total Amount of Payment')}}</b></td>
                                                <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($pawningDetails->full_amount_payout, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2" class="text-right"><b>{{__('Total Interest Amount')}}</b></td>
                                                <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($pawningDetails->full_financed_charge, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2" class="text-right"><b>{{__('Total of Payments')}}</b></td>
                                                <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($pawningDetails->full_total_of_payment, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2" class="text-right"><b>{{__('Total Prepayments')}}</b></td>
                                                <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($pawningDetails->full_prepayment, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2" class="text-right"><b>{{__('Officer')}}</b></td>
                                                <td colspan="2" class="text-right">{{ $pawningOfficer->name ?? '--' }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2" class="text-right"><b>{{__('Salesman')}}</b></td>
                                                <td colspan="2" class="text-right">{{ $introducer->name ?? '--' }}</td>
                                            </tr>
                                        </tfoot>
                                    {{-- @endif --}}
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{__('Receipt Summary')}}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <tr>
                                        <th>{{__('Receipt')}}</th>
                                        <th>{{__('Description')}}</th>
                                        {{-- <th>{{__('Amount')}}</th>
                                        <th>{{__('Extra Charges')}}</th> --}}
                                        <th>{{__('Total Amount to be Paid')}}</th>
                                        <th>{{__('Paid Amount')}}</th>
                                        <th>{{__('Collected By')}}</th>
                                        <th>{{__('Paid Date')}}</th>
                                        <th>{{__('Due Date')}}</th>
                                        <th class="text-right">{{__('Action')}}</th>
                                    </tr>
                                    @foreach($pawnings->receipts as $receipt)

                                        <tr>
                                            <td class="font-style"><button type="button" class="btn btn-outline-primary view" value="{{ $receipt->id }}"> {{ $receipt->receipt_code }} </button></td>
                                            <td>{{ $receipt->description }}</td>
                                            {{-- <td>{{ $receipt->installment_amount }}</td> --}}
                                            {{-- <td>{{ $receipt->extra_charges ?? '--' }}</td> --}}
                                            <td>{{ number_format($receipt->total_amount_to_pay, 2) }}</td>
                                            <td>{{ number_format($receipt->paid_amount, 2) }}</td>
                                            <td>{{ Auth::user()->otherUserName($receipt->created_by) }}</td>
                                            <td>{{ $receipt->issue_date }}</td>
                                            <td>{{ $receipt->installment_due_date }}</td>
                                            <td class="text-right">
                                                @can('print receipt')
                                                    <button type="button" class="btn btn-info btn-action mr-1 view" title="View" value="{{ $receipt->id }}"><i class="fas fa-eye"></i></button>
                                                    <button type="button" class="btn btn-info btn-action mr-1 print" title="Print" value="{{ $receipt->id }}"><i class="fas fa-print"></i></button>
                                                @endcan
                                                @can('collect receipt')
                                                    <a href="{{ route('pawning.cash_collector_receipt', $receipt->id) }}" target="_blank" class="btn btn-info btn-action mr-1" title="Cash Collector Receipt Print"><i class="fas fa-mobile mt-1"></i></a>
                                                @endcan
                                            </td>
                                            {{-- <td class="text-right">

                                                <a href="#!" class="btn btn-danger btn-action" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$payment->id}}').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                {!! Form::open(['method' => 'post', 'route' => ['invoice.payment.destroy',$invoice->id,$payment->id],'id'=>'delete-form-'.$payment->id]) !!}
                                                {!! Form::close() !!}

                                            </td> --}}
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{__('Installment Due Dates')}}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <tr>
                                        <th>{{__('Installment Dates')}}</th>
                                        <th class="text-right">{{__('Installment Amount')}}</th>
                                        <th class="text-center">{{__('Excess Amount')}}</th>
                                        <th class="text-center">{{__('Amount To be Paid')}}</th>
                                        <th class="text-center">{{__('Paid Amount')}}</th>
                                        <th class="text-center">{{__('Total Due Amount')}}</th>
                                        <th class="text-center">{{__('Description')}}</th>
                                        @if(Gate::check('pay pawning') || Gate::check('cancel installment pawning'))
                                            <th class="text-right">{{__('Action')}}</th>
                                        @endif
                                    </tr>
                                    @php
                                        $lastInst = $pawnings->lastInstallment->id ?? '';
                                    @endphp
                                     @foreach($pawnings->installments as $key =>$installmentDetail)
                                        <tr>
                                            <td>{{ $installmentDetail->installment_date }}</td>
                                            <td class="text-center">
                                                {{-- @if ($installmentDetail->released_amount != null)
                                                    {{ $installmentDetail->installment_amount }}<span class="text-primary"> - {{ $installmentDetail->released_amount }}</span>
                                                @else
                                                    {{ $installmentDetail->installment_amount }}
                                                @endif --}}
                                                {{ number_format($installmentDetail->installment_amount, 2) }}
                                                {{-- {{ 'Rs.' }}{{ $installmentDetail->installment_amount  }}</td> --}}
                                            <td class="text-center">
                                                @if($installmentDetail->released_amount == null && $installmentDetail->remainder_amount == null) -- @else
                                                    @if ($installmentDetail->released_amount != null) <span class="text-primary"> - {{ number_format($installmentDetail->released_amount, 2) }}</span> <br> @endif
                                                    @if ($installmentDetail->remainder_amount != null) <span class="text-danger"> + {{ number_format($installmentDetail->remainder_amount, 2) }}</span> @endif
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($installmentDetail->total_amount_to_pay, 2) }}
                                            </td>
                                            <td class="text-center">
                                                @if ($installmentDetail->paid_amount != null) {{ number_format($installmentDetail->paid_amount, 2) }} @else -- @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($installmentDetail->total_due_amount != null) {{ number_format($installmentDetail->total_due_amount, 2) }} @else -- @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($installmentDetail->status == 1)
                                                    <span class="badge badge-success">Paid</span>
                                                @elseif ($installmentDetail->status == 2)
                                                    <span class="badge badge-danger">Over Due</span>
                                                @elseif ($installmentDetail->status == 0)
                                                    <span class="badge badge-primary">Not Paid</span>
                                                @endif
                                            </td>
                                            @if(Gate::check('pay pawning') || Gate::check('cancel installment pawning'))
                                                <td class="text-right">
                                                    @can('pay pawning')
                                                        @if ($installmentDetail->status != 1)
                                                            <a href="#" class="btn btn-danger btn-action mr-1" style="padding-top: 10px;" data-url="{{ route('pawning.pay_installment',$installmentDetail->id) }}" data-ajax-popup="true" data-title="{{__('Pay Installment')}}" data-toggle="tooltip" data-original-title="{{__('Pay')}}">
                                                                {{__('Pay')}}
                                                            </a>
                                                        @endif
                                                    @endcan
                                                    @can('cancel installment pawning')
                                                        @if ($installmentDetail->status == 1 && $installmentDetail->id == $lastInst)
                                                            <a href="#" class="btn btn-danger btn-action" style="padding-top: 10px;" data-toggle="tooltip" data-original-title="{{__('Cancel Payment')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('cancel-form-{{$installmentDetail->id}}').submit();">
                                                                <i class="fas fa-undo-alt"></i>
                                                            </a>
                                                            {!! Form::open(['method' => 'GET', 'route' => ['pawning.cancel_installment', $installmentDetail->id],'id'=>'cancel-form-'.$installmentDetail->id]) !!}
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
    // var pawning = <php echo json_encode($pItms) ?>;

    $(document).ready(function(){
        $(".se-pre-con").fadeOut('slow');
        // console.log(pawning);
        $('.view').click(function(){
            var value = $(this).val();
            // window.location.href = "{{ url('hire_purchase_contract/receipt') }}/"+value;
            url = "{{ url('pawning/receipt') }}/"+value;
            window.open(url, '_blank');
        });

        $('.print').click(function(){
            var value = $(this).val();
            // window.location.href = "{{ url('hire_purchase_contract/receipt') }}/"+value;
            url = "{{ url('pawning/receipt/print') }}/"+value;
            window.open(url, '_blank');
        });

        $('body').on('click', '.view_cheque', function(){
            var chequeId = $(this).val();
            window.open("{{ url('cheque-print') }}/"+chequeId+"/view");
        });

        $('body').on('click', '.print_cheque', function(){
            var chequeId = $(this).val();
            window.open("{{ url('cheque-print') }}/"+chequeId+"/print");
        });
    });
</script>
