<style>
    .col-auto {
        margin-left: 75%;
    }

    .card-header {
        padding: 15px 0px !important;
    }

    .pay-detail-table{
        width: 100%;
        border: 2px solid;
    }
    .pay-detail-table th, td{
        padding: 10px;
    }
</style>

    <div class="pawning-detail-print">
        <div class="row">
            <div class="col-lg-12">
                {{-- <div class="invoice-title"> --}}
                <div class="card">
                    <div class="card-header">
                        <h2>{{__('Pawning Details')}}</h2>
                    </div>
                </div>
            {{-- </div> --}}
                <div class="row">
                    @if(!empty($pawningDetails->customer))
                        <div style="width: 25%;" class="col-md-3">
                            <address class="font-style">
                                <strong>{{__('Customer')}}:</strong><br>
                                {{!empty($pawningDetails->customer->name)?$pawningDetails->customer->name:''}}<br>
                                {{!empty($pawningDetails->customer->nic_no)?$pawningDetails->customer->nic_no:''}}<br>
                                {{-- {{!empty($pawningDetails->customer->phone)?$pawningDetails->customer->phone:''}}<br>
                                {{!empty($pawningDetails->customer->address)?$pawningDetails->customer->address:''}}<br> --}}
                                {{-- {{!empty($pawningDetails->customer->email)?$pawningDetails->customer->email:''}} --}}
                                {{-- @foreach ($pawningDetails as $pawningDetail)
                                    {{ $pawningDetail->customer['name'] }}
                                @endforeach --}}
                            </address>
                        </div>
                        <div style="width: 25%;" class="col-md-3">
                            <address>
                                <strong>{{__('Current Address')}}:</strong><br>
                                {{-- {{!empty($pawningDetails->customer->name)?$pawningDetails->customer->name:''}}<br> --}}
                                {{!empty($pawningDetails->customer->address)?$pawningDetails->customer->address:''}}<br>
                                {{!empty($pawningDetails->customer->phone)?$pawningDetails->customer->phone:''}}<br>
                                {{!empty($pawningDetails->customer->email)?$pawningDetails->customer->email:''}}
                            </address>
                        </div>
                        <div style="width: 25%;" class="col-md-3 text-md-center">
                            <address>
                                <strong>{{__('Customer Number')}}:</strong><br>
                                {{ Auth::user()->customerNumberFormat($pawningDetails->customer->id) }}<br>
                            </address>
                        </div>
                    @endif
                    <div style="width: 25%;" class="col-md-3">
                        <table class="pay-detail-table">
                            <tr>
                                <th>Paid Amount</th><th class="text-center">:</th><td class="text-right"><span class="total_paid"></span></td>
                            </tr>
                            <tr>
                                <th>Outstanding Balance</th><th class="text-center">:</th><td class="text-right">{{ number_format($pawningDetails->current_due_amount, 2)}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-4">
                        <address>
                            <strong>{{__('Status')}}:</strong><br>
                            @if($pawningDetails->status == 1)
                                To be Paid
                            @elseif($pawningDetails->status == 2)
                                Partialy Paid
                            @elseif($pawningDetails->status == 3)
                                Over Due
                            @elseif($pawningDetails->status == 4)
                                Paid
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
                </div> --}}
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="section-title"><b>{{__('Pawning Summary')}}</b></div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-md">
                        <thead>
                            <tr>
                                <th data-width="40">#</th>
                                <th>{{__('Pawning Date')}}</th>
                                <th>{{__('Officer')}}</th>
                                <th>{{__('Salesman')}}</th>
                                <th class="text-right">{{__('Total Amount Financed')}}</th>
                                <th class="text-right">{{__('Total Amount of Payment')}}</th>
                                <th class="text-right">{{__('Interest Rate')}}</th>
                                <th class="text-right">{{__('Total Interest Amount')}}</th>
                                <th class="text-right">{{__('Total of Payment')}}</th>
                                <th class="text-right">{{__('Installment Amount')}}</th>
                                {{-- <th class="text-right" width="12%">{{__('Net Amount')}}</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ \Auth::user()->pawningNumberFormat($pawningDetails->id) }}</td>
                                <td>{{ $pawningDetails->issue_date ?? '' }}</td>
                                <td>{{ $pawningOfficer->name ?? '--' }}</td>
                                <td>{{ $introducer->name ?? '--' }}</td>
                                <td class="text-right">{{ 'Rs. ' }}{{ number_format($pawningDetails->full_amount_financed, 2) }}</td>
                                <td class="text-right">{{ 'Rs. ' }}{{ number_format($pawningDetails->full_amount_payout, 2) }}</td>
                                <td class="text-right">{{ $pawningDetails->interest_rate }} {{ __('%') }}</td>
                                <td class="text-right">{{ 'Rs. ' }}{{ number_format($pawningDetails->full_financed_charge, 2) }}</td>
                                <td class="text-right">{{ 'Rs. ' }}{{ number_format($pawningDetails->full_total_of_payment, 2) }}</td>
                                <td class="text-right">{{ 'Rs. ' }}{{ number_format($pawningDetails->full_prepayment, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="section-title"><b>{{__('Pawned Item Details')}}</b></div>
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
                        {{-- <tfoot>
                            <tr>
                                <td colspan="2" class="text-right"><b>{{__('Total Amount Financed')}}</b></td>
                                <td class="text-center"><b>:</b></td>
                                <td class="text-right">{{__('Rs.')}} {{ number_format($pawningDetails->full_amount_financed, 2) }}</td>
                                <td colspan="2" class="text-right"><b>{{__('Total Amount of Payment')}}</b></td>
                                <td class="text-center"><b>:</b></td>
                                <td class="text-right">{{__('Rs.')}} {{ number_format($pawningDetails->full_amount_payout, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-right"><b>{{__('Total Interest Amount')}}</b></td>
                                <td class="text-center"><b>:</b></td>
                                <td class="text-right">{{__('Rs.')}} {{ number_format($pawningDetails->full_financed_charge, 2) }}</td>
                                <td colspan="2" class="text-right"><b>{{__('Total of Payments')}}</b></td>
                                <td class="text-center"><b>:</b></td>
                                <td class="text-right">{{__('Rs.')}} {{ number_format($pawningDetails->full_total_of_payment, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-right"><b>{{__('Total Prepayments')}}</b></td>
                                <td class="text-center"><b>:</b></td>
                                <td class="text-right">{{__('Rs.')}} {{ number_format($pawningDetails->full_prepayment, 2) }}</td>
                                <td colspan="2" class="text-right"><b>{{__('Officer')}}</b></td>
                                <td class="text-center"><b>:</b></td>
                                <td class="text-right">{{ $pawningOfficer->name ?? '--' }}</td>
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="2" class="text-right"><b>{{__('Salesman')}}</b></td>
                                <td class="text-center"><b>:</b></td>
                                <td class="text-right">{{ $introducer->name ?? '--' }}</td>
                            </tr>
                        </tfoot> --}}
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="section-title"><b>{{__('Receipt Summary')}}</b></div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-md">
                        <tr>
                            <th>{{__('Receipt')}}</th>
                            <th>{{__('Due Date')}}</th>
                            <th>{{__('Paid Date')}}</th>
                            {{-- <th>{{__('Description')}}</th> --}}
                            {{-- <th>{{__('Amount')}}</th>
                            <th>{{__('Extra Charges')}}</th> --}}
                            <th>{{__('Excess Amount')}}</th>
                            <th>{{__('Due Amount')}}</th>
                            <th>{{__('Paid Amount')}}</th>
                            <th>{{__('Total Paid')}}</th>
                            <th>{{__('Balance')}}</th>
                            <th>{{__('Collected By')}}</th>
                        </tr>
                        @php
                            $totalPaid = 0;
                        @endphp
                        @foreach($pawnings->receipts as $receipt)
                            @php
                                $totalPaid += $receipt->paid_amount;
                            @endphp
                            <tr>
                                <td class="font-style">{{ $receipt->receipt_code }}</td>
                                <td>{{ $receipt->installment_due_date }}</td>
                                <td>{{ $receipt->issue_date }}</td>
                                {{-- <td>{{ $receipt->description }}</td> --}}
                                {{-- <td>{{ $receipt->installment_amount }}</td> --}}
                                {{-- <td>{{ $receipt->extra_charges ?? '--' }}</td> --}}
                                <td>
                                    @if (isset($receipt->installments) && isset($receipt->installments->released_amount) && $receipt->installments->released_amount > 0)
                                        {{ '-'.number_format($receipt->installments->released_amount, 2) }}
                                    @elseif(isset($receipt->installments) && isset($receipt->installments->remainder_amount) && $receipt->installments->remainder_amount > 0)
                                        {{ '+'.number_format($receipt->installments->remainder_amount, 2) }}
                                    @else
                                        --
                                    @endif
                                </td>
                                <td>{{ number_format($receipt->total_amount_to_pay, 2) }}</td>
                                <td>{{ number_format($receipt->paid_amount, 2) }}</td>
                                <td>{{ number_format($totalPaid, 2) }}</td>
                                <td>{{ number_format($receipt->balance_amount, 2) }}</td>
                                <td>{{ Auth::user()->otherUserName($receipt->created_by) }}</td>
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
                            <th class="text-right">{{__('Installment Amount')}}</th>
                            <th class="text-center">{{__('Excess Amount')}}</th>
                            <th class="text-center">{{__('Amount To be Paid')}}</th>
                            <th class="text-center">{{__('Paid Amount')}}</th>
                            <th class="text-center">{{__('Total Due Amount')}}</th>
                            <th class="text-center">{{__('Description')}}</th>
                        </tr>
                            @foreach($pawnings->installments as $key =>$installmentDetail)
                            <tr>
                                <td>{{ $installmentDetail->installment_date }}</td>
                                <td class="text-center">
                                    {{ number_format($installmentDetail->installment_amount, 2) }}
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
                                        Paid
                                    @elseif ($installmentDetail->status == 2)
                                        Over Due
                                    @elseif ($installmentDetail->status == 0)
                                        Not Paid
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div> --}}
    </div>

    <script>
        $(document).ready(function () {
            var totalPaid = parseFloat($('#total_paid').val()).toFixed(2);
            $('.total_paid').html(totalPaid.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
        });
    </script>
    @if ($command == 'print')
        <script>
            $(document).ready(function(){
                $('.pawning-detail-print').printThis({
                    importCSS: true,
                    importStyle: true,//thrown in for extra measure
                    // header:'<h3 class="mt-3 text-center" style="margin-bottom:-0.1px;">රිච්වින් ඉන්වෙස්ට්මන්ට් ඇන්ඩ් ක්‍රෙඩිට් ප්‍රයිවට් ලිමිටඩ්<br>'+
                    //         '<span style="font-size: 22px;">ණය යෝජනා අනුමත අත්හිටුවීම් සහ නිකුත් කිරීමේ පත්‍රය</span></h3>'
                });
            });
        </script>
    @endif
