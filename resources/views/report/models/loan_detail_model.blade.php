<style>
    .for-print {display: none;}
    @media print {
        .no-print {display: none;}
        .for-print {display: block;}
    }
    .pay-detail-table{
        width: 100%;
        border: 2px solid;
    }
    .pay-detail-table th, td{
        padding: 10px;
    }
</style>
        <div class="loan-detail-print">

            <div class="row">
                <div class="col-lg-12">
                    <div class="invoice-title">
                        <h2>{{__('Loan Details')}}</h2>
                    </div>
                    <hr>
                    <div class="row">
                        @if(!empty($loans->center))
                            <div style="width: 25%;" class="col-md-3">
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
                            <div style="width: 25%;" class="col-md-3">
                                <address>
                                    <strong>{{__('Customer ')}}:</strong><br>
                                    {{!empty($loans->customer->name)?$loans->customer->name:''}}<br>
                                    {{!empty($loans->customer->phone)?$loans->customer->phone:''}}<br>
                                    {{!empty($loans->customer->address)?$loans->customer->address:''}}<br>
                                    {{!empty($loans->customer->nic_no)?$loans->customer->nic_no:''}}<br>
                                    {{!empty($loans->customer->email)?$loans->customer->email:''}}
                                </address>
                            </div>
                            <div style="width: 25%;" class="col-md-3">
                                <address class="font-style text-md-center">
                                    <strong>{{__('Customer Number')}}:</strong><br>
                                    {{ Auth::user()->customerNumberFormat($loans->customer->id) }}
                                </address>
                            </div>
                        @endif
                        @if(empty($loans->center))
                            <div style="width: 25%;" class="col-md-3">
                            </div>
                        @endif
                        <div style="width: 25%;" class="col-md-3">
                            <table class="pay-detail-table">
                                <tr>
                                    <th>Paid Amount</th><th class="text-center">:</th><td class="text-right"><span class="total_paid"></span></td>
                                </tr>
                                <tr>
                                    <th>Outstanding Balance</th><th class="text-center">:</th><td class="text-right">{{ number_format($loans->current_due_amount, 2)}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    {{-- <div class="row mt-3">
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
                    </div> --}}
                    {{-- <div class="row mt-3">
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
                    </div> --}}
                    {{-- <div class="row mt-3">
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
                    </div> --}}
                    {{-- <div class="row mt-3">
                        <div class="col-md-4">
                            <address>
                                <strong>{{__('Status')}}:</strong><br>
                                @if($loans->status == 0)
                                    Rejected
                                @elseif($loans->status == 1)
                                    Pending
                                @elseif($loans->status == 2)
                                    Approved
                                @elseif($loans->status == 3)
                                    Closed
                                @endif
                            </address>
                        </div>
                        <div style="width: 25%;" class="col-md-4 text-md-center">
                            <address>
                                <strong>{{__('Issue Date')}} :</strong><br>
                                {{ \Auth::user()->dateFormat($loans->date) }}<br><br>
                            </address>
                        </div>
                        <div style="width: 25%;" class="col-md-4 text-md-right">
                            @if (isset($loans->due_date))
                                <address>
                                    <strong>{{__('Close Date')}} :</strong><br>
                                    {{ \Auth::user()->dateFormat($loans->due_date) }}<br><br>
                                </address>
                            @endif
                        </div>
                    </div> --}}
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="section-title"><b>{{__('Loan Summary')}}</b></div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-md">
                            <thead>
                                <tr>
                                    <th data-width="40">#</th>
                                    <th>{{__('Loan Date')}}</th>
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
                                    <td>{{ $loans->date }}</td>
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
                                    <td>{{  $loans->time_period }} {{ __('Months') }}</td>
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
                                {{-- <tr>
                                    <td colspan="9"></td>
                                </tr> --}}
                                <tr>
                                    <td></td>
                                    <td colspan="2" class="text-right"><b>{{__('Total Interest')}}</b></td>
                                    <td class="text-center"><b>:</b></td>
                                    <td class="text-right">{{__('Rs.')}} {{ number_format($loans->interest_amount, 2) }}</td>
                                    @if ($loans->time_range == 3)
                                        <td colspan="2" class="text-right"><b>{{__('Monthly Installment')}}</b></td>
                                        <td class="text-center"><b>:</b></td>
                                        <td class="text-right">{{__('Rs.')}} {{ number_format($loans->monthly_installment, 2) }}</td>
                                    @elseif ($loans->time_range == 2)
                                        <td colspan="2" class="text-right"><b>{{__('Weekly Installment')}}</b></td>
                                        <td class="text-center"><b>:</b></td>
                                        <td class="text-right">{{__('Rs.')}} {{ number_format($loans->weekly_installment, 2) }}</td>
                                    @elseif ($loans->time_range == 1)
                                        <td colspan="2" class="text-right"><b>{{__('Daily Installment')}}</b></td>
                                        <td class="text-center"><b>:</b></td>
                                        <td class="text-right">{{__('Rs.')}} {{ number_format($loans->daily_installment, 2) }}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td></td>
                                    <td colspan="2" class="text-right"><b>{{__('Total Installment')}}</b></td>
                                    <td class="text-center"><b>:</b></td>
                                    <td class="text-right">{{__('Rs.')}} {{ number_format($loans->total_amount, 2) }}</td>
                                {{-- <tr>
                                    <td colspan="5"></td>
                                    <td colspan="2" class="text-right"><b>{{__('Document Charges')}}</b></td>
                                    <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($loans->document_charges, 2) }}</td>
                                </tr> --}}
                                {{-- <tr>
                                    <td colspan="5"></td>
                                    <td colspan="2" class="text-right"><b>{{__('Outstanding Balance')}}</b></td>
                                    <td colspan="2" class="text-right">{{__('Rs.')}} {{ number_format($loans->current_due_amount, 2) }}</td>
                                </tr> --}}
                                    <td colspan="2" class="text-right"><b>{{__('Officer')}}</b></td>
                                    <td class="text-center"><b>:</b></td>
                                    <td class="text-right">{{ $loanOfficer->name ?? '--' }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        @if (count($loans->receipts) > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title"><b>{{__('Receipt Summary')}}</b></div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-md">
                            <tr>
                                <th>{{__('Receipt')}}</th>
                                {{-- <th>{{__('Description')}}</th> --}}
                                <th>{{__('Due Date')}}</th>
                                <th>{{__('Paid Date')}}</th>
                                <th>{{__('Fine Amount')}}</th>
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
                            @foreach($loans->receipts as $receipt)
                                @php
                                    $totalPaid += $receipt->paid_amount;
                                @endphp
                                <tr>
                                    <td class="font-style"> {{ $receipt->receipt_code }}</td>
                                    {{-- <td>{{ $receipt->description }}</td> --}}
                                    <td>{{ $receipt->installment_due_date }}</td>
                                    <td>{{ $receipt->issue_date }}</td>
                                    <td>{{ $receipt->loanInstallments->fine ?? '--' }}</td>
                                    <td>
                                        @if (isset($receipt->loanInstallments) && isset($receipt->loanInstallments->released_amount) && $receipt->loanInstallments->released_amount > 0)
                                            {{ '-'.number_format($receipt->loanInstallments->released_amount, 2) }}
                                        @elseif(isset($receipt->loanInstallments) && isset($receipt->loanInstallments->remainder_amount) && $receipt->loanInstallments->remainder_amount > 0)
                                            {{ '+'.number_format($receipt->loanInstallments->remainder_amount, 2) }}
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
        @endif
        {{-- @if (count($loans->loanInstallments) > 0)
            <div class="row mt-4">
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
                            @foreach($loans->loanInstallments as $key =>$installment)
                                <tr>
                                    <td>{{ $installment->installment_date }}</td>
                                    <td class="text-center">
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
                                    <td class="text-center">
                                        @if ($installment->status == 1)
                                            Paid
                                        @elseif ($installment->status == 2)
                                            Not Paid
                                        @elseif ($installment->status == 3)
                                            Overdue
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endif --}}

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
                $('.loan-detail-print').printThis({
                    importCSS: true,
                    importStyle: true,//thrown in for extra measure
                    // header:'<h3 class="mt-3 text-center" style="margin-bottom:-0.1px;">රිච්වින් ඉන්වෙස්ට්මන්ට් ඇන්ඩ් ක්‍රෙඩිට් ප්‍රයිවට් ලිමිටඩ්<br>'+
                    //         '<span style="font-size: 22px;">ණය යෝජනා අනුමත අත්හිටුවීම් සහ නිකුත් කිරීමේ පත්‍රය</span></h3>'
                });
            });
        </script>
    @endif

