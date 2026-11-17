@extends('layouts.admin')
@section('page-title')
    {{__('Loan Details')}}
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

    .for-print {display: none;}
    @media print {
        .no-print {display: none;}
        .for-print {display: block;}
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
    <section class="section phone-section">
    <div class="se-pre-con"></div>

        <div class="section-header">
            <h1>{{__('View Loan')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                <div class="breadcrumb-item">{{__('Loan')}}</div>
                <div class="breadcrumb-item">{{__('Loan View')}}</div>
            </div>
        </div>
        <div class="section-body">
            <div class="invoice phone-card-bd">
                <div class="loan-detail-print">
                    <div class="row mb-10">
                        <div class="col-lg-12">
                            <div class="text-right">
                                {{-- <a href="../../{{ $loans->application_document }}" target="_blank" class="btn btn-primary"> View Application</a>
                                <a href="../../{{ $loans->other_document }}" target="_blank" class="btn btn-primary"> View Document</a> --}}
                                <button type="button" id="application_view" class="btn btn-primary" value="{{ $loans->application_document ?? '' }}"> View Application</button>
                                <button type="button" id="document_view" class="btn btn-primary" value="{{ $loans->other_document ?? ''}}"> View Document 1</button>
                                <button type="button" id="document_view2" class="btn btn-primary" value="{{ $loans->other_document2 ?? ''}}"> View Document 2</button>
                                <button type="button" id="document_view3" class="btn btn-primary" value="{{ $loans->other_document3 ?? ''}}"> View Document 3</button>
                                @if($loans->status ?? 0 == 3) <button type="button" id="document_print" class="btn btn-primary"> Print Loan Detail</button> @endif
                            </div>
                        </div>
                        <div class="col-lg-12 mt-3">
                            <div class="text-right">
                                {{-- <a href="../../{{ $loans->application_document }}" target="_blank" class="btn btn-primary"> View Application</a>
                                <a href="../../{{ $loans->other_document }}" target="_blank" class="btn btn-primary"> View Document</a> --}}
                                @if (isset($loans->cheque_id))
                                    @can('view cheque')
                                        <button type="button" class="btn btn-outline-primary view_cheque" value="{{ $loans->cheque_id }}">
                                            View Cheque - {{ AUth::user()->chequeNumberFormat($loans->cheque_id) }}
                                        </button>
                                    @endcan
                                    @can('print cheque')
                                        <button type="button" class="btn btn-primary print_cheque" value="{{ $loans->cheque_id }}"> Print Cheque </button>
                                    @endcan
                                    @can('print payment voucher')
                                        <button type="button" class="btn btn-primary payment_voucher" value="{{ $loans->cheque_id }}"> Payment Voucher </button>
                                    @endcan
                                    {{-- <button type="button" class="btn btn-primary print_cheque" value="{{ $loans->cheque_id }}">Print Cheque</button> --}}
                                    {{-- <button type="button" id="document_view" class="btn btn-primary" value="{{ $loans->other_document }}"> View Voucher</button> --}}
                                @endif
                                @if ($loans->status == 2 || $loans->status == 5)
                                    {{-- @can('create bank account')
                                        <a href="#" data-size="xl" data-url="{{ route('loan_cheque.create',$loans->id) }}" data-ajax-popup="true" data-title="{{__('Create New Cheque')}}" class="btn btn-icon icon-left btn-primary">
                                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                            <span class="btn-inner--text"> {{__('Create Cheque')}}</span>
                                        </a>
                                    @endcan --}}
                                    @can('create payment voucher')
                                        <a href="#" data-size="xl" data-url="{{ route('loan_payment_voucher.create',$loans->id) }}" data-ajax-popup="true" data-title="{{__('Create Payment Voucher')}}" class="btn btn-icon icon-left btn-primary">
                                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                            <span class="btn-inner--text"> {{__('Create Payment Voucher')}}</span>
                                        </a>
                                    @endcan
                                @endif
                            </div>
                        </div>
                        @if ($loans->status == 2 || $loans->status == 5)
                            <div class="col-lg-12 mt-3">
                                <div class="text-right">
                                    <a href="{{ route('loan.loan_agreement',$loans->id) }}" target="_blank" class="btn btn-sm btn-outline-info">Loan Agreement</a>
                                    @if($loans->guarantor_1!=null)<a href="{{ route('loan.loan_guaranter_bond',$loans->id) }}" target="_blank" class="btn btn-sm btn-outline-info">Guarantor 1 Bond</a>@endif
                                    @if($loans->guarantor_2!=null)<a href="{{ route('loan.loan_guaranter_bond',$loans->id) }}?gid=2" target="_blank" class="btn btn-sm btn-outline-info">Guarantor 2 Bond</a>@endif
                                    @if($loans->guarantor_3!=null)<a href="{{ route('loan.loan_guaranter_bond',$loans->id) }}?gid=3" target="_blank" class="btn btn-sm btn-outline-info">Guarantor 3 Bond</a>@endif
                                    @if($loans->guarantor_4!=null)<a href="{{ route('loan.loan_guaranter_bond',$loans->id) }}?gid=4" target="_blank" class="btn btn-sm btn-outline-info">Guarantor 4 Bond</a>@endif
                                    {{-- <a href="{{ route('loan.loan_promissary_note',$loans->id) }}" target="_blank" class="btn btn-sm btn-outline-info">Promissary Note</a> --}}
                                </div>
                            </div>
                        @endif
                    </div>
                <div class="loan-detail-print2">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="invoice-title">
                                <h2>{{__('Loan Details')}}</h2>
                            </div>
                            <hr>
                            <div class="row">
                                @if(!empty($loans->center))
                                    <div class="col-md-6">
                                        <address class="font-style">
                                            <strong>{{__('Center ')}}:</strong><br>
                                            {{!empty($loans->center->name)?$loans->center->name:''}}<br>
                                            {{!empty($loans->center->branches->name)?$loans->center->branches->name:''}}<br>
                                            {{!empty($loans->center->branches->address)?$loans->center->branches->address:''}}<br>
                                            {{-- {{!empty($loans->center->branches->email)?$loans->center->branches->email:''}} --}}
                                        </address>
                                    </div>
                                @endif
                                @if(!empty($loans->customer))
                                    <div class="col-md-3">
                                        <address>
                                            <strong>{{__('Customer ')}}:</strong><br>
                                            {{!empty($loans->customer->name)?$loans->customer->name:''}}<br>
                                            {{!empty($loans->customer->phone)?$loans->customer->phone:''}}<br>
                                            {{!empty($loans->customer->address)?$loans->customer->address:''}}<br>
                                            {{!empty($loans->customer->email)?$loans->customer->email:''}}<br>
                                            {{!empty($loans->customer->nic_no)?$loans->customer->nic_no:''}}
                                        </address>

                                    </div>
                                    <div class="col-md-3">
                                        <address class="font-style text-md-center">
                                            <strong>{{__('Customer Number')}}:</strong><br>
                                            {{ Auth::user()->customerNumberFormat($loans->customer->id) }}
                                        </address>
                                    </div>
                                @endif
                            </div>
                            <div class="row mt-3">
                                @if(!empty($loans->guarantor1))
                                    <div class="col-md-3">
                                        <address class="font-style">
                                            <strong>{{__('Guarantor 1 ')}}:</strong><br>
                                            {{!empty($loans->guarantor1->name)?$loans->guarantor1->name:''}}<br>
                                            {{!empty($loans->guarantor1->phone)?$loans->guarantor1->phone:''}}<br>
                                            {{!empty($loans->guarantor1->address)?$loans->guarantor1->address:''}}<br>
                                            {{!empty($loans->guarantor1->email)?$loans->guarantor1->email:''}}<br>
                                            {{!empty($loans->guarantor1->nic_no)?$loans->guarantor1->nic_no:''}}
                                        </address>
                                    </div>
                                @endif
                                @if(!empty($loans->guarantor2))
                                <div class="col-md-3">
                                        <address>
                                            <strong>{{__('Guarantor 2 ')}}:</strong><br>
                                            {{!empty($loans->guarantor2->name)?$loans->guarantor2->name:''}}<br>
                                            {{!empty($loans->guarantor2->phone)?$loans->guarantor2->phone:''}}<br>
                                            {{!empty($loans->guarantor2->address)?$loans->guarantor2->address:''}}<br>
                                            {{!empty($loans->guarantor2->email)?$loans->guarantor2->email:''}}<br>
                                            {{!empty($loans->guarantor2->nic_no)?$loans->guarantor2->nic_no:''}}
                                        </address>
                                    </div>
                                @endif
                                @if(!empty($loans->guarantor3))
                                <div class="col-md-3">
                                        <address>
                                            <strong>{{__('Guarantor 3 ')}}:</strong><br>
                                            {{!empty($loans->guarantor3->name)?$loans->guarantor3->name:''}}<br>
                                            {{!empty($loans->guarantor3->phone)?$loans->guarantor3->phone:''}}<br>
                                            {{!empty($loans->guarantor3->address)?$loans->guarantor3->address:''}}<br>
                                            {{!empty($loans->guarantor3->email)?$loans->guarantor3->email:''}}<br>
                                            {{!empty($loans->guarantor3->nic_no)?$loans->guarantor3->nic_no:''}}
                                        </address>
                                    </div>
                                @endif
                                @if(!empty($loans->guarantor4))
                                <div class="col-md-3">
                                        <address>
                                            <strong>{{__('Guarantor 4 ')}}:</strong><br>
                                            {{!empty($loans->guarantor4->name)?$loans->guarantor4->name:''}}<br>
                                            {{!empty($loans->guarantor4->phone)?$loans->guarantor4->phone:''}}<br>
                                            {{!empty($loans->guarantor4->address)?$loans->guarantor4->address:''}}<br>
                                            {{!empty($loans->guarantor4->email)?$loans->guarantor4->email:''}}<br>
                                            {{!empty($loans->guarantor4->nic_no)?$loans->guarantor4->nic_no:''}}
                                        </address>
                                    </div>
                                @endif
                            </div>
                            <div class="row mt-3">
                                @if ($loans->loan_type == 2)
                                    <div class="col-md-3">
                                        <address>
                                            <strong>{{__('Guardian Name ')}}:</strong><br>
                                            {{ $loans->guardian_name ?? '--' }}<br>
                                        </address>
                                    </div>
                                    <div class="col-md-3">
                                        <address>
                                            <strong>{{__('Guardian NIC ')}}:</strong><br>
                                            {{ $loans->guardian_nic_no ?? '--' }}<br>
                                        </address>
                                    </div>
                                    <div class="col-md-3">
                                        <address>
                                            <strong>{{__('Guardian Address ')}}:</strong><br>
                                            {{ $loans->guardian_address ?? '--' }}<br>
                                        </address>
                                    </div>
                                    <div class="col-md-3">
                                        <address>
                                            <strong>{{__('Guardian Phone No ')}}:</strong><br>
                                            {{ $loans->guardian_phone_no ?? '--' }}<br>
                                        </address>
                                    </div>
                                @endif
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <address>
                                        <strong>Non Related 1<sup>st</sup> Person Name :</strong><br>
                                        {{ $loans->person_1_name ?? '--' }}<br>
                                    </address>
                                </div>
                                <div class="col-md-3">
                                    <address>
                                        <strong>Non Related 1<sup>st</sup> Person Phone No :</strong><br>
                                        {{ $loans->person_1_phone ?? '--' }}<br>
                                    </address>
                                </div>
                                <div class="col-md-3">
                                    <address>
                                        <strong>Non Related 2<sup>nd</sup> Person Name :</strong><br>
                                        {{ $loans->person_2_name ?? '--' }}<br>
                                    </address>
                                </div>
                                <div class="col-md-3">
                                    <address>
                                        <strong>Non Related 2<sup>nd</sup> Person Phone No :</strong><br>
                                        {{ $loans->person_2_phone ?? '--' }}<br>
                                    </address>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <address>
                                        <strong>{{__('Status')}}:</strong><br>
                                        @if($loans->status == 0)
                                        <span class="badge badge-danger">Rejected</span>
                                        @elseif($loans->status == 1)
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($loans->status == 2)
                                            <span class="badge badge-success">Disbursed</span>
                                        @elseif($loans->status == 3)
                                            <span class="badge badge-primary">Closed</span>
                                        @elseif($loans->status == 4)
                                            <span class="badge badge-info">Checked</span>
                                        @elseif($loans->status == 5)
                                            <span class="badge badge-info">Approved</span>
                                        @endif
                                    </address>
                                </div>
                                <div class="col-md-4 text-md-center">
                                    <address>
                                        <strong>{{__('Issue Date')}} :</strong><br>
                                        {{ \Auth::user()->dateFormat($loans->date) }}<br><br>
                                    </address>
                                </div>
                                <div class="col-md-4 text-md-right">
                                    @if (isset($loans->due_date))
                                        <address>
                                            <strong>{{__('Close Date')}} :</strong><br>
                                            {{ \Auth::user()->dateFormat($loans->due_date) }}<br><br>
                                        </address>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{__('Loan Summary')}}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <thead>
                                        <tr>
                                            <th data-width="40">#</th>
                                            <th>{{__('Loan Type')}}</th>
                                            <th>{{__('Business Category')}}</th>
                                            <th>{{__('Loan Reason')}}</th>
                                            <th>{{__('Interest Rate')}}</th>
                                            <th>{{__('Loan Amount')}}</th>
                                            <th>{{__('Installment Time Period')}}</th>
                                            <th>{{__('Collection Period')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ \Auth::user()->loanNumberFormat($loans->id) }}</td>

                                            @if ($loans->loan_type == 1)
                                                <td>{{ __('SME Loan') }}</td>
                                            @elseif ($loans->loan_type == 2)
                                                <td>{{ __('Group Loan') }}</td>
                                            @elseif ($loans->loan_type == 3)
                                                <td>{{ __('Vehicle Loan') }}</td>
                                            @elseif ($loans->loan_type == 4)
                                                <td>{{ __('Land Loan') }}</td>
                                            @elseif ($loans->loan_type == 5)
                                                <td>{{ __('Job Loan') }}</td>
                                            @endif

                                            <td>{{ $loans->businessCategory->category_name ?? '--' }}</td>
                                            <td>{{ $loans->reason ?? '--' }}</td>
                                            <td>{{ $loans->interest_rate }} {{ __('%') }}</td>
                                            <td>{{  \Auth::user()->priceFormat($loans->loan_amount) }}</td>
                                            <td>
                                                @if ($loans->time_range == 1)
                                                    {{ $loans->daily_time_period }} {{ __('Days') }}
                                                @elseif ($loans->time_range == 2 || $loans->time_range == 3)
                                                    {{  $loans->time_period }} {{ __('Months') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($loans->time_range == 1)
                                                {{ __('Daily') }}
                                                @elseif ($loans->time_range == 2)
                                                {{ __('Weekly') }}
                                                @elseif ($loans->time_range == 3)
                                                {{ __('Monthly') }}
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="8"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"></td>
                                            <td colspan="2" class="text-right"><b>{{__('Total Interest')}}</b></td>
                                            <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($loans->interest_amount, 2) }}</td>
                                        </tr>
                                        @if ($loans->time_range == 3)
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2" class="text-right"><b>{{__('Monthly Installment')}}</b></td>
                                                <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($loans->monthly_installment, 2) }}</td>
                                            </tr>
                                        @elseif ($loans->time_range == 2)
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2" class="text-right"><b>{{__('Weekly Installment')}}</b></td>
                                                <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($loans->weekly_installment, 2) }}</td>
                                            </tr>
                                        @elseif ($loans->time_range == 1)
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2" class="text-right"><b>{{__('Daily Installment')}}</b></td>
                                                <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($loans->daily_installment, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="4"></td>
                                            <td colspan="2" class="text-right"><b>{{__('Total Installment')}}</b></td>
                                            <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($loans->total_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"></td>
                                            <td colspan="2" class="text-right"><b>{{__('Document Charges')}}</b></td>
                                            <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($loans->document_charges, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"></td>
                                            <td colspan="2" class="text-right"><b>{{__('Outstanding Balance')}}</b></td>
                                            <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($loans->current_due_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"></td>
                                            <td colspan="2" class="text-right"><b>{{__('Officer')}}</b></td>
                                            <td colspan="2" class="text-right">{{ $loanOfficer->name ?? '--' }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                @if ($loans->loan_type != 2)
                    <div class="display" id="break_page" style='page-break-after:always'></div>
                @endif

                @if (count($loans->receipts) > 0)
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
                                        <th class="text-right no-print">{{__('Action')}}</th>
                                    </tr>
                                    @foreach($loans->receipts as $receipt)

                                        <tr>
                                            <td class="font-style"><button type="button" class="btn btn-outline-primary reciept" value="{{ $receipt->id }}"> {{ $receipt->receipt_code }} </button></td>
                                            <td>{{ $receipt->description }}</td>
                                            {{-- <td>{{ $receipt->installment_amount }}</td>
                                            <td>{{ $receipt->extra_charges ?? '--' }}</td> --}}
                                            <td>{{ number_format($receipt->total_amount_to_pay, 2) }}</td>
                                            <td>{{ number_format($receipt->paid_amount, 2) }}</td>
                                            <td>{{ Auth::user()->otherUserName($receipt->created_by) }}</td>
                                            <td>{{ $receipt->issue_date }}</td>
                                            <td>{{ $receipt->installment_due_date }}</td>
                                            <td class="text-right no-print">
                                                @can('print receipt')
                                                    <button type="button" class="btn btn-info btn-action mr-1 reciept" title="View" value="{{ $receipt->id }}"><i class="fas fa-eye"></i></button>
                                                    <button type="button" class="btn btn-info btn-action mr-1 print" title="Print" value="{{ $receipt->id }}"><i class="fas fa-print"></i></button>
                                                @endcan
                                                @can('collect receipt')
                                                    <a href="{{ route('loan.cash_collector_receipt', $receipt->id) }}" target="_blank" class="btn btn-info btn-action mr-1" title="Cash Collector Receipt Print"><i class="fas fa-mobile mt-1"></i></a>
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


                                    @foreach($loans->loanInstallments as $key =>$installment)
                                        @foreach($installment->loanInstallmentAdvances as $installmentAdvance)
                                            <tr>
                                                <td class="font-style"><button type="button" class="btn btn-outline-primary view" value="{{ $installmentAdvance->receipts->id }}"> {{ $installmentAdvance->receipts->receipt_code }} </button></td>
                                                <td>{{ $installmentAdvance->receipts->description ?? "" }}</td>
                                                <td></td>
                                                <td>{{ number_format($installmentAdvance->receipts->paid_amount, 2) }}</td>
                                                <td>{{ Auth::user()->otherUserName($installmentAdvance->receipts->created_by) }}</td>
                                                <td>{{ $installmentAdvance->receipts->issue_date }}</td>
                                                <td></td>
                                                @if(Gate::check('print receipt') || Gate::check('collect receipt'))
                                                    <td class="text-right">
                                                        {{-- @can('print receipt')
                                                            <button type="button" class="btn btn-info btn-action mr-1 view" title="View" value="{{ $installmentAdvance->receipts->id }}"><i class="fas fa-eye"></i></button>
                                                            <button type="button" class="btn btn-info btn-action mr-1 print" title="Print" value="{{ $installmentAdvance->receipts->id }}"><i class="fas fa-print"></i></button>
                                                        @endcan
                                                        @can('collect receipt')
                                                            <a href="{{ route('hire_purchase_contract.cash_collector_receipt', $installmentAdvance->receipts->id) }}" target="_blank" class="btn btn-info btn-action mr-1" title="Cash Collector Receipt Print"><i class="fas fa-mobile mt-1"></i></a>
                                                        @endcan --}}

                                                        @can('print receipt')
                                                        <button type="button" class="btn btn-info btn-action mr-1 reciept" title="View" value="{{ $installmentAdvance->receipts->id }}"><i class="fas fa-eye"></i></button>
                                                        <button type="button" class="btn btn-info btn-action mr-1 print" title="Print" value="{{ $installmentAdvance->receipts->id }}"><i class="fas fa-print"></i></button>
                                                    @endcan
                                                    @can('collect receipt')
                                                        <a href="{{ route('loan.cash_collector_receipt', $installmentAdvance->receipts->id) }}" target="_blank" class="btn btn-info btn-action mr-1" title="Cash Collector Receipt Print"><i class="fas fa-mobile mt-1"></i></a>
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
                @endif


                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="section-title">{{__('Send a fine for the delay to the customer')}}</div>
                        {{ Form::open(array('url' => 'sme_loan/send_fine', 'method' => 'post')) }}
                            <div class="form-group col-md-12">
                                <input type="hidden" value="{{$loans->customer->phone_mobile ?? ''}}" name="phone">
                                <div class="row">
                                    <div class="form-group col-md-6" style="text-align: -webkit-right;">

                                        {{ Form::text('fine_message', '', array('class' => 'form-control','required'=>'required', 'placeholder'=>'Type your message about the fine...', 'requried'=>'requried', 'maxlength'=>100)) }}

                                    </div>
                                    <div class="form-group">
                                            {{Form::submit(__('Send'),array('class'=>'btn btn-primary'))}}
                                    </div>

                                </div>
                            </div>
                        {{ Form::close() }}

                    </div>
                </div>
                @if (count($loans->loanInstallments) > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{__('Installment Due Dates')}}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <tr>
                                        <th class="text-center">{{__('Installment Due Dates')}}</th>
                                        <th class="text-center">{{__('Installment Dates')}}</th>
                                        <th class="text-center">{{__('Installment Amount')}}</th>
                                        <th class="text-center">{{__('Excess Amount')}}</th>
                                        <th class="text-center">{{__('Amount to be Paid')}}</th>
                                        <th class="text-center">{{__('Paid Amount')}}</th>
                                        <th class="text-center">{{__('Total Due Amount')}}</th>
                                        <th class="text-center">{{__('Description')}}</th>
                                        @if(Gate::check('pay loan') || Gate::check('cancel installment loan'))
                                            <th class="text-right no-print">{{__('Action')}}</th>
                                        @endif
                                    </tr>
                                    @php
                                        $lastInst = $loans->lastInstallment->id ?? '';
                                    @endphp
                                    @foreach($loans->loanInstallments as $key =>$installment)
                                        <tr>
                                            <td class="text-center">{{ $installment->installment_date }}</td>
                                            <td class="text-center">{{ $installment->paid_date }}</td>
                                            <td class="text-center">
                                                {{-- @if ($installment->released_amount != null)
                                                    @if ($installment->fine != null)
                                                        {{ $installment->amount }}<span class="text-primary"> - {{ $installment->released_amount }}</span><span class="text-danger"> + {{ $installment->fine }}</span>
                                                    @else
                                                        {{ $installment->amount }}<span class="text-primary"> - {{ $installment->released_amount }}</span>
                                                    @endif
                                                @else
                                                    @if ($installment->fine != null)
                                                        {{ $installment->amount }}<span class="text-danger"> + {{ $installment->fine }}</span>
                                                    @else
                                                        {{ $installment->amount }}
                                                    @endif
                                                @endif --}}
                                                {{-- {{ number_format($installment->amount, 2) }} @if ($installment->released_amount != null) <span class="text-primary"> - {{ number_format($installment->released_amount, 2) }}</span> @endif @if ($installment->fine != null) <span class="text-danger"> + {{ number_format($installment->fine, 2) }}</span> @endif @if ($installment->remainder_amount != null) <span class="text-danger"> + {{ number_format($installment->remainder_amount, 2) }}</span> @endif --}}
                                                {{ number_format($installment->amount, 2) }} @if ($installment->fine != null) <span class="text-danger"> + {{ number_format($installment->fine, 2) }}</span> @endif
                                            </td>
                                            <td class="text-center">
                                                @if($installment->released_amount == null && $installment->remainder_amount == null) -- @else
                                                    @if ($installment->released_amount != null) <span class="text-primary"> - {{ number_format($installment->released_amount, 2) }}</span> <br> @endif
                                                    @if ($installment->remainder_amount != null) <span class="text-danger"> + {{ number_format($installment->remainder_amount, 2) }}</span> @endif
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($installment->total_amount_to_pay, 2) }}
                                            </td>
                                            <td class="text-center">
                                                @if ($installment->paid_amount != null) {{ number_format($installment->paid_amount, 2) }} @else -- @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($installment->total_due_amount != null) {{ number_format($installment->total_due_amount, 2) }} @else -- @endif
                                            </td>
                                            <td class="text-center no-print">
                                                @if ($installment->status == 1)
                                                    <span class="badge badge-success">Paid</span>
                                                @elseif ($installment->status == 2)
                                                    <span class="badge badge-primary">Not Paid</span>
                                                @elseif ($installment->status == 3)
                                                    <span class="badge badge-danger">Overdue</span>
                                                @endif
                                            </td>
                                            <td class="text-center for-print">
                                                @if ($installment->status == 1)
                                                    Paid
                                                @elseif ($installment->status == 2)
                                                    Not Paid
                                                @elseif ($installment->status == 3)
                                                    Overdue
                                                @endif
                                            </td>
                                            @if(Gate::check('pay loan') || Gate::check('cancel installment loan'))
                                                <td class="text-right no-print">
                                                    @can('pay loan')
                                                        @if ($installment->status != 1)
                                                            <a href="#" class="btn btn-danger btn-action mr-1" style="padding-top: 10px;" data-url="{{ route('loan.pay_installment',$installment->id) }}" data-ajax-popup="true" data-title="{{__('Pay Installment')}}" data-toggle="tooltip" data-original-title="{{__('Pay')}}">
                                                                {{__('Pay')}}
                                                            </a>
                                                        @endif
                                                    @endcan
                                                    @can('cancel installment loan')
                                                        @if ($installment->status == 1 && $installment->id == $lastInst)
                                                            <a href="#" class="btn btn-danger btn-action" style="padding-top: 10px;" data-toggle="tooltip" data-original-title="{{__('Cancel Payment')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('cancel-form-{{$installment->id}}').submit();">
                                                                <i class="fas fa-undo-alt"></i>
                                                            </a>
                                                            {!! Form::open(['method' => 'GET', 'route' => ['loan_installment.cancel_installment', $installment->id],'id'=>'cancel-form-'.$installment->id]) !!}
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
                @endif

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
    // var sale = <?php echo json_encode($loans) ?>;

    $(document).ready(function(){
        $(".se-pre-con").fadeOut('slow');
        // console.log(sale);
        $('.view').click(function(){
            var value = $(this).val();
            url = "{{ url('loan/view') }}/"+value;
            window.open(url, '_blank');
        });

        $('.reciept').click(function(){
            var value = $(this).val();
            url = "{{ url('loan/receipt') }}/"+value;
            window.open(url, '_blank');
        });

        $('.print').click(function(){
            var value = $(this).val();
            url = "{{ url('loan/receipt/print') }}/"+value;
            window.open(url, '_blank');
        });

        $('#document_print').click(function(){
                $('.loan-detail-print2').printThis({
                    importCSS: true,
                    importStyle: true,//thrown in for extra measure
                    // header:'<h3 class="mt-3 text-center" style="margin-bottom:-0.1px;">රිච්වින් ඉන්වෙස්ට්මන්ට් ඇන්ඩ් ක්‍රෙඩිට් ප්‍රයිවට් ලිමිටඩ්<br>'+
                    //         '<span style="font-size: 22px;">ණය යෝජනා අනුමත අත්හිටුවීම් සහ නිකුත් කිරීමේ පත්‍රය</span></h3>'
                });
            });

        $('#application_view').click(function(){
            var url = $(this).val();
            if(url == ''){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Application Not Uploaded!'
                });
            } else if(url != ''){
                window.open('../../'+url);
            }
        });

        $('#document_view').click(function(){
            var url = $(this).val();
            if(url == ''){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Document Not Uploaded!'
                });
            } else if(url != ''){
                window.open('../../'+url);
            }
        });
        $('#document_view2').click(function(){
            var url = $(this).val();
            if(url == ''){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Document Not Uploaded!'
                });
            } else if(url != ''){
                window.open('../../'+url);
            }
        });
        $('#document_view3').click(function(){
            var url = $(this).val();
            if(url == ''){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Document Not Uploaded!'
                });
            } else if(url != ''){
                window.open('../../'+url);
            }
        });

        $('body').on('click', '.view_cheque', function(){
            var chequeId = $(this).val();
            window.open("{{ url('cheque-print') }}/"+chequeId+"/view");
        });

        $('body').on('click', '.print_cheque', function(){
            var chequeId = $(this).val();
            window.open("{{ url('cheque-print') }}/"+chequeId+"/print");
        });

        $('body').on('click', '.payment_voucher', function(){
            var chequeId = $(this).val();
            window.open("{{ url('cheque-print_voucher/payment_voucher') }}/"+chequeId);
        });
    });

</script>
