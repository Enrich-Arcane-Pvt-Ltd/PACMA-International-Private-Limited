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

    .pay-detail-table {
        width: 100%;
        border: 2px solid;
    }

    .pay-detail-table th,
    td {
        padding: 10px;
    }
</style>

<div class="sale-detail-print" id="printableArea">
    <div class="row">
        <div class="col-lg-12">
            <div class="invoice-title">
                <h2>{{ __('Sale Details') }}</h2>
            </div>
            <hr>
            <div class="row">
                @if (!empty($sales->customer))
                    <div style="width: 25%;" class="col-md-4">
                        <address>
                            <strong>{{ __('Customer Address') }}:</strong><br>
                            @if ($sales->customer->title == 0)
                                Mr.
                            @elseif($sales->customer->title == 1)
                                Mrs.
                            @else
                                Miss.
                            @endif
                            {{ !empty($sales->customer->name) ? $sales->customer->name : '' }}<br>
                            {{ !empty($sales->customer->phone) ? $sales->customer->phone : '' }}<br>
                            {{ !empty($sales->customer->address) ? $sales->customer->address : '' }}<br>
                            {{ !empty($sales->customer->nic_no) ? $sales->customer->nic_no : '' }}<br>
                            {{ !empty($sales->customer->email) ? $sales->customer->email : '' }}
                        </address>
                    </div>
                    <div style="width: 50%;" class="col-md-4">
                        <address class="font-style text-md-center">
                            <strong>{{ __('Customer Number') }}:</strong><br>
                            {{ Auth::user()->customerNumberFormat($sales->customer->id) }}
                        </address>
                    </div>
                    {{-- <div class="col-md-4 text-md-right">
                            <address>
                                <strong>{{__('Customer Address')}}:</strong><br>
                                @if ($sales->customer->title == 0) Mr. @elseif($sales->customer->title == 1) Mrs. @else Miss. @endif {{!empty($sales->customer->name)?$sales->customer->name:''}}<br>
                                {{!empty($sales->customer->phone)?$sales->customer->phone:''}}<br>
                                {{!empty($sales->customer->address)?$sales->customer->address:''}}<br>
                                {{!empty($sales->customer->nic_no)?$sales->customer->nic_no:''}}<br>
                                {{!empty($sales->customer->email)?$sales->customer->email:''}}
                            </address>
                        </div> --}}
                @endif
                @if ($sales->sale_type == 1)
                    <div style="width: 25%;" class="col-md-4">
                        <table class="pay-detail-table">
                            <tr>
                                <th>Paid Amount</th>
                                <th class="text-center">:</th>
                                <td class="text-right"><span class="total_paid"></span></td>
                            </tr>
                            <tr>
                                <th>Outstanding Balance</th>
                                <th class="text-center">:</th>
                                <td class="text-right">{{ number_format($sales->current_due_amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                @endif
            </div>
            {{-- <div class="row mt-3">
                    <div class="col-md-4">
                        @if (!empty($sales->guarantor_id))
                            <address class="font-style">
                                <strong>{{__('Guarantor 1 ')}}:</strong><br>
                                @if ($sales->guarantor1->title == 0) Mr. @elseif($sales->guarantor1->title == 1) Mrs. @else Miss. @endif {{!empty($sales->guarantor1->name)?$sales->guarantor1->name:''}}<br>
                                {{!empty($sales->guarantor1->phone)?$sales->guarantor1->phone:''}}<br>
                                {{!empty($sales->guarantor1->address)?$sales->guarantor1->address:''}}<br>
                                {{!empty($sales->guarantor1->email)?$sales->guarantor1->email:''}}<br>
                                {{!empty($sales->guarantor1->nic_no)?$sales->guarantor1->nic_no:''}}
                            </address>
                        @endif
                    </div>
                    <div class="col-md-4">
                        @if (!empty($sales->guarantor_2_id))
                            <address>
                                <strong>{{__('Guarantor 2 ')}}:</strong><br>
                                @if ($sales->guarantor2->title == 0) Mr. @elseif($sales->guarantor2->title == 1) Mrs. @else Miss. @endif {{!empty($sales->guarantor2->name)?$sales->guarantor2->name:''}}<br>
                                {{!empty($sales->guarantor2->phone)?$sales->guarantor2->phone:''}}<br>
                                {{!empty($sales->guarantor2->address)?$sales->guarantor2->address:''}}<br>
                                {{!empty($sales->guarantor2->email)?$sales->guarantor2->email:''}}<br>
                                {{!empty($sales->guarantor2->nic_no)?$sales->guarantor2->nic_no:''}}
                            </address>
                        @endif
                    </div>
                    <div class="col-md-4 text-md-right">
                        @if (!empty($sales->introducer_id))
                            <address>
                                <strong>{{__('Salesman ')}}:</strong><br>
                                {{ $introducer->name ?? '' }}
                            </address>
                        @endif
                    </div>
                </div> --}}
            {{-- <div class="row">
                    <div class="col-md-4">
                        <address>
                            <strong>{{__('Status')}}:</strong><br>
                            @if ($sales->status == 1)
                                Paid
                            @elseif($sales->status == 2)
                                Partially Paid
                            @elseif($sales->status == 3)
                                Overdue
                            @endif
                        </address>
                    </div>
                    <div class="col-md-4 text-md-center">
                        <address>
                            <strong>{{__('Issue Date')}} :</strong><br>
                            {{ $sales->date_of_sale }}<br><br>
                        </address>
                    </div>
                    <div class="col-md-4 text-md-right">
                        @if (isset($sales->due_date))
                            <address>
                                <strong>{{__('Close Date')}} :</strong><br>
                                {{ $sales->due_date }}<br><br>
                            </address>
                        @endif
                    </div>
                </div> --}}
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="section-title"><b>{{ __('Purchase Summary') }}</b></div>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-md" style="width: 100%;">
                    <thead>
                        <tr>
                            <th data-width="40">#</th>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Purchasing Price') }} ({{ $currencySymbol }})</th>
                            <th>{{ __('Purchase Type') }}</th>
                            <th>{{ __('Discount') }} ({{ $currencySymbol }})</th>
                            @if ($sales->sale_type == 1)
                                <th>{{ __('Down Payment Rate') }}</th>
                                <th>{{ __('Installment Time Period') }}</th>
                                <th>{{ __('Interest Rate') }}</th>
                            @endif
                            <th class="text-right" width="12%">{{ __('Net Amount') }} ({{ $currencySymbol }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($sales->modal_type == 1)
                            <tr>
                                <td>{{ $sales->sale_id }}</td>
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
                                        {{ __('%') }}</td>
                                    <td>{{ $sales->installment }} {{ __('months') }}</td>
                                    <td>{{ $sales->interest_rate }} {{ __('%') }}</td>
                                @endif
                                <td class="text-right">{{ number_format($sales->net_amount, 2) }}
                                </td>
                            </tr>
                        @elseif ($sales->modal_type == 2)
                            @foreach ($sales->saleItems as $sale)
                                <tr>
                                    <td>{{ $sales->sale_id }}</td>
                                    <td>{{ $sale->products->name }}</td>
                                    <td>{{ $sale->quantity }}</td>
                                    <td>{{ number_format($sale->unit_price, 2) }}</td>
                                    <td>
                                        @if ($sales->sale_type == 0)
                                            {{ __('Cash Sales') }}
                                        @elseif ($sales->sale_type == 1)
                                            {{ __('Hire Sales') }}
                                        @endif
                                    </td>
                                    <td>{{ number_format($sale->discount) }}</td>
                                    <td class="text-right">
                                        {{ number_format($sale->net_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    @if ($sales->sale_type == 1)
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-right"><b>{{ __('Down Payment') }}</b></td>
                                <td class="text-center"><b>:</b></td>
                                <td colspan="2" class="text-right">
                                    {{ $currencySymbol }} {{ number_format($sales->down_payment, 2) }}</td>
                                <td colspan="3" class="text-right"><b>{{ __('Interest Amount') }}</b></td>
                                <td class="text-center"><b>:</b></td>
                                <td class="text-right">
                                    {{ number_format($sales->interest_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-right"><b>{{ __('Monthly Installment') }}</b></td>
                                <td class="text-center"><b>:</b></td>
                                <td colspan="2" class="text-right">
                                    {{ $currencySymbol }} {{ number_format($sales->monthly_installment, 2) }}</td>
                                <td colspan="3" class="text-right">
                                    <b>{{ __('Total of Investment Schedule for Payment') }}</b>
                                </td>
                                <td class="text-center"><b>:</b></td>
                                <td class="text-right">
                                    {{ number_format($sales->total_due_amount, 2) }}</td>
                            </tr>
                            {{-- <tr>
                                    <td colspan="7"></td>
                                    <td colspan="2" class="text-right"><b>{{__('Outstanding Balance')}}</b></td>
                                    <td class="text-right">{{__('Rs.')}} {{ number_format($sales->current_due_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="7"></td>
                                    <td colspan="2" class="text-right"><b>{{__('Document Charges')}}</b></td>
                                    <td class="text-right">{{__('Rs.')}} {{ number_format($sales->document_charges, 2) }}</td>
                                </tr> --}}
                        </tfoot>
                    @endif
                    @if ($sales->modal_type == 2)
                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="2" class="text-right"><b>{{ __('Total Amount') }}</b></td>
                                <td class="text-right">{{ number_format($sales->net_amount, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    @if ($sales->sale_type == 1)

        <div class="row">
            <div class="col-md-12">
                <div class="section-title"><b>{{ __('Receipt Summary') }}</b></div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-md">
                        <tr>
                            <th>{{ __('Receipt') }}</th>
                            <th>{{ __('Due Date') }}</th>
                            <th>{{ __('Paid Date') }}</th>
                            {{-- <th>{{__('Description')}}</th> --}}
                            <th>{{ __('Fine Amount') }}</th>
                            <th>{{ __('Excess Amount') }}</th>
                            <th>{{ __('Due Amount') }}</th>
                            <th>{{ __('Paid Amount') }}</th>
                            <th>{{ __('Total Paid') }}</th>
                            <th>{{ __('Balance') }}</th>
                            <th>{{ __('Collected By') }}</th>
                        </tr>
                        @php
                            $totalPaid = 0;
                        @endphp
                        @foreach ($sales->receipts as $receipt)
                            @php
                                $totalPaid += $receipt->paid_amount;
                            @endphp
                            <tr>
                                <td class="font-style">{{ $receipt->receipt_code }}</td>
                                <td>{{ $receipt->installment_due_date }}</td>
                                <td>{{ $receipt->issue_date }}</td>
                                {{-- <td>{{ $receipt->description }}</td> --}}
                                <td>{{ $receipt->installments->fine ?? '--' }}</td>
                                <td>
                                    @if (isset($receipt->installments) &&
                                            isset($receipt->installments->released_amount) &&
                                            $receipt->installments->released_amount > 0)
                                        {{ '-' . number_format($receipt->installments->released_amount, 2) }}
                                    @elseif(isset($receipt->installments) &&
                                            isset($receipt->installments->remainder_amount) &&
                                            $receipt->installments->remainder_amount > 0)
                                        {{ '+' . number_format($receipt->installments->remainder_amount, 2) }}
                                    @else
                                        --
                                    @endif
                                </td>
                                <td>{{ number_format($receipt->total_amount_to_pay, 2) }}</td>
                                <td>{{ number_format($receipt->paid_amount, 2) }}</td>
                                <td>{{ number_format($totalPaid, 2) }}</td>
                                <td>{{ number_format($receipt->balance_amount, 2) }}</td>
                                <td>{{ Auth::user()->otherUserName($receipt->created_by) }}</td>
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
                    <input type="hidden" name="total_paid" id="total_paid" value="{{ $totalPaid }}">
                </div>
            </div>
        </div>
        {{-- <div class="row">
                <div class="col-md-12">
                    <div class="section-title"><b>{{__('Installment Due Dates')}}</b></div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-md">
                            <tr>
                                <th>{{__('Installment Dates')}}</th>
                                <th class="text-center">{{__('Installment Amount')}}</th>
                                <th class="text-center">{{__('Excess Amount')}}</th>
                                <th class="text-center">{{__('Amount to be Paid')}}</th>
                                <th class="text-center">{{__('Paid Amount')}}</th>
                                <th class="text-center">{{__('Total Due Amount')}}</th>
                                <th class="text-center">{{__('Description')}}</th>
                            </tr>
                            @foreach ($sales->installments as $key => $installment)
                                <tr>
                                    <td>{{ $installment->installment_date }}</td>
                                    <td class="text-center">
                                        {{ number_format($installment->amount, 2) }} @if ($installment->fine != null) <span class="text-danger"> + {{ number_format($installment->fine, 2) }}</span> @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($installment->released_amount == null && $installment->remainder_amount == null) -- @else
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
                                    <td class="text-center">
                                        @if ($installment->status == 1)
                                            Paid
                                        @elseif ($installment->status == 2)
                                            Not Paid
                                        @elseif ($installment->status == 3)
                                            Over Due
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div> --}}
    @endif
</div>

<script>
    $(document).ready(function() {
        var totalPaid = parseFloat($('#total_paid').val()).toFixed(2);
        $('.total_paid').html(totalPaid.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
    });
</script>
@if ($command == 'print')
    <script>
        $(document).ready(function() {
            $('.sale-detail-print').printThis({
                importCSS: true,
                importStyle: true, //thrown in for extra measure
                // header:'<h3 class="mt-3 text-center" style="margin-bottom:-0.1px;">රිච්වින් ඉන්වෙස්ට්මන්ට් ඇන්ඩ් ක්‍රෙඩිට් ප්‍රයිවට් ලිමිටඩ්<br>'+
                //         '<span style="font-size: 22px;">ණය යෝජනා අනුමත අත්හිටුවීම් සහ නිකුත් කිරීමේ පත්‍රය</span></h3>'
            });
        });
    </script>
@endif
