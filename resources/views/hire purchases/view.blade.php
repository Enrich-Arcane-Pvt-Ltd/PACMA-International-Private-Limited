@extends('layouts.admin')
@section('page-title')
    {{ __('Hire Sales Contract Details') }}
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('View Hire Sales Contracts') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                <div class="breadcrumb-item"><a
                        href="{{ route('hire_purchase_contract.index') }}">{{ __('Hire Sales Contracts') }}</a></div>
                <div class="breadcrumb-item">{{ __('Hire Sales Contract View') }}</div>
            </div>
        </div>
        <div class="section-body">
            <div class="invoice">
                <div class="sale-detail-print">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="invoice-title">
                                <h2>{{ __('Hire Sales Contract Details') }}</h2>
                            </div>
                            <hr>
                            <div class="row">
                                @if (!empty($sales->customer))
                                    <div class="col-md-6">
                                        <address class="font-style">
                                            <strong>{{ __('Delivery Address') }}:</strong><br>
                                            {{ !empty($sales->customer->name) ? $sales->customer->name : '' }}
                                            {{ !empty($sales->customer->last_name) ? $sales->customer->last_name : '' }}<br>
                                            {{ !empty($sales->customer->contact) ? $sales->customer->contact : '' }}<br>
                                            {{ !empty($sales->customer->billing_address) ? $sales->customer->billing_address : '' }}<br>
                                            {{ !empty($sales->customer->email) ? $sales->customer->email : '' }}
                                        </address>
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        <address>
                                            <strong>{{ __('Current Address') }}:</strong><br>
                                            {{ !empty($sales->customer->name) ? $sales->customer->name : '' }}
                                            {{ !empty($sales->customer->last_name) ? $sales->customer->last_name : '' }}<br>
                                            {{ !empty($sales->customer->contact) ? $sales->customer->contact : '' }}<br>
                                            {{ !empty($sales->customer->billing_address) ? $sales->customer->billing_address : '' }}<br>
                                            {{ !empty($sales->customer->email) ? $sales->customer->email : '' }}
                                        </address>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <address>
                                        <strong>{{ __('Status') }}:</strong><br>
                                        @if ($sales->status == 1)
                                            <span class="badge badge-success">Paid</span>
                                        @elseif($sales->status == 2)
                                            <span class="badge badge-warning">Partialy Paid</span>
                                        @elseif($sales->status == 3)
                                            <span class="badge badge-danger">Over Due</span>
                                        @endif
                                    </address>
                                </div>
                                <div class="col-md-4 text-md-center">
                                    <address>
                                        <strong>{{ __('Issue Date') }} :</strong><br>
                                        {{ $sales->date_of_sale }}<br><br>
                                    </address>
                                </div>
                                <div class="col-md-4 text-md-right">
                                    @if (isset($sales->due_date))
                                        <address>
                                            <strong>{{ __('Due Date') }} :</strong><br>
                                            {{ $sales->due_date }}<br><br>
                                        </address>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{ __('Purchase Summary') }}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <thead>
                                        <tr>
                                            <th data-width="40">#</th>
                                            <th>{{ __('Product') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Purchasing Price') }}</th>
                                            <th>{{ __('Purchase Type') }}</th>
                                            <th>{{ __('Discount') }}</th>
                                            <th>{{ __('Down Payment Rate') }}</th>
                                            <th>{{ __('Installment Time Period') }}</th>
                                            <th>{{ __('Interest Rate') }}</th>
                                            <th class="text-right" width="12%">{{ __('Net Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $sales->id }}</td>
                                            <td>{{ $sales->products->name }}</td>
                                            <td>{{ $sales->quantity }}</td>
                                            <td>{{ __('Rs.') }} {{ $sales->unit_price }}</td>
                                            <td>
                                                @if ($sales->sale_type == 0)
                                                    {{ __('Cash Sales') }}
                                                @elseif ($sales->sale_type == 1)
                                                    {{ __('Hire Sales') }}
                                                @endif
                                            </td>
                                            <td>{{ $sales->discount }}</td>
                                            @if ($sales->sale_type == 1)
                                                <td>{{ $sales->products->product_category->down_payment }}
                                                    {{ __('%') }}</td>
                                                <td>{{ $sales->installment }} {{ __('months') }}</td>
                                                <td>{{ $sales->interest_rate }} {{ __('%') }}</td>
                                            @endif
                                            <td class="text-right">{{ __('Rs.') }} {{ $sales->net_amount }}</td>
                                        </tr>
                                    </tbody>
                                    @if ($sales->sale_type == 1)
                                        <tfoot>
                                            <tr>
                                                <td colspan="10"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Down Payment') }}</b></td>
                                                <td class="text-right">{{ __('Rs.') }} {{ $sales->down_payment }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Interest Amount') }}</b>
                                                </td>
                                                <td class="text-right">{{ __('Rs.') }} {{ $sales->interest_amount }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right">
                                                    <b>{{ __('Monthly Installment') }}</b>
                                                </td>
                                                <td class="text-right">{{ __('Rs.') }}
                                                    {{ $sales->monthly_installment }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right">
                                                    <b>{{ __('Total Of Investment Schedule For Payment') }}</b>
                                                </td>
                                                <td class="text-right">{{ __('Rs.') }} {{ $sales->total_due_amount }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Balance Due') }}</b></td>
                                                <td class="text-right">{{ __('Rs.') }}
                                                    {{ $sales->current_due_amount }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Document Charges') }}</b>
                                                </td>
                                                <td class="text-right">{{ __('Rs.') }} {{ $sales->document_charges }}
                                                </td>
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
                                        <th>{{ __('Total Amount To Be Paid') }}</th>
                                        <th>{{ __('Paid Amount') }}</th>
                                        <th>{{ __('Collected By') }}</th>
                                        <th>{{ __('Paid Date') }}</th>
                                        <th>{{ __('Due Date') }}</th>
                                        <th class="text-right">{{ __('Action') }}</th>
                                    </tr>
                                    @foreach ($sales->receipts as $receipt)
                                        <tr>
                                            <td class="font-style"><button type="button"
                                                    class="btn btn-outline-primary view" value="{{ $receipt->id }}">
                                                    {{ $receipt->receipt_code }} </button></td>
                                            <td>{{ $receipt->description }}</td>
                                            {{-- <td>{{ $receipt->installment_amount }}</td>
                                            <td>{{ $receipt->extra_charges ?? '--' }}</td> --}}
                                            <td>{{ $receipt->total_amount_to_pay }}</td>
                                            <td>{{ $receipt->paid_amount }}</td>
                                            <td>{{ $receipt->created_by }}</td>
                                            <td>{{ $receipt->issue_date }}</td>
                                            <td>{{ $receipt->installment_due_date }}</td>
                                            <td class="text-right">
                                                <button type="button" class="btn btn-info btn-action mr-1 view"
                                                    title="View" value="{{ $receipt->id }}"><i
                                                        class="fas fa-eye"></i></button>
                                                <button type="button" class="btn btn-info btn-action mr-1 print"
                                                    title="Print" value="{{ $receipt->id }}"><i
                                                        class="fas fa-print"></i></button>
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
                            <div class="section-title">{{ __('Installment Due Dates') }}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <tr>
                                        <th>{{ __('Installment Dates') }}</th>
                                        <th class="text-center">{{ __('Installment Amount') }}</th>
                                        <th class="text-center">{{ __('Total Amount Tobe Paid') }}</th>
                                        <th class="text-center">{{ __('Description') }}</th>
                                        <th class="text-right">{{ __('Action') }}</th>
                                    </tr>
                                    @foreach ($sales->installments as $key => $installment)
                                        <tr>
                                            <td>{{ $installment->installment_date }}</td>
                                            <td class="text-center">
                                                @if ($installment->fine != null)
                                                    {{ $installment->amount }}<span class="text-danger"> +
                                                        {{ $installment->fine }}</span>
                                                @else
                                                    {{ $installment->amount }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $fine = 0;
                                                    if ($installment->fine != null) {
                                                        $fine = $installment->fine;
                                                    }
                                                    $totAmount = $installment->amount + $fine;

                                                    echo $totAmount;
                                                @endphp
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
                                            <td class="text-right">
                                                @if ($installment->status == 1)
                                                    <button type="button" class="btn btn-danger pay"
                                                        value="{{ $installment->id }}" disabled>Pay</button>
                                                @else
                                                    <a href="#" class="btn btn-danger btn-action mr-1"
                                                        style="padding-top: 10px;"
                                                        data-url="{{ route('hire_purchase_contract.pay_installment', $installment->id) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Pay Installment') }}"
                                                        data-toggle="tooltip" data-original-title="{{ __('Pay') }}">
                                                        {{ __('Pay') }}
                                                    </a>
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
    var sale = <?php echo json_encode($sales); ?>;

    $(document).ready(function() {
        console.log(sale);
        $('.view').click(function() {
            var value = $(this).val();
            // window.location.href = "{{ url('hire_purchase_contract/receipt') }}/"+value;
            url = "{{ url('hire_purchase_contract/receipt') }}/" + value;
            window.open(url, '_blank');
        });

        $('.print').click(function() {
            var value = $(this).val();
            // window.location.href = "{{ url('hire_purchase_contract/receipt') }}/"+value;
            url = "{{ url('hire_purchase_contract/receipt/print') }}/" + value;
            window.open(url, '_blank');
        });
    });
</script>
