@extends('layouts.admin')
@section('page-title')
    {{ __('Invoice Detail') }}
@endsection
{{-- @push('script-page')
    <script src="https://js.stripe.com/v3/"></script>

    <script type="text/javascript">
            @if ($invoice->getDue() > 0 && Utility::getValByName('enable_stripe') == 'on' && !empty(Utility::getValByName('stripe_key')) && !empty(Utility::getValByName('stripe_secret')))
        var stripe = Stripe('{{ Utility::getValByName('stripe_key') }}');
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        var style = {
            base: {
                // Add your base input styles here. For example:
                fontSize: '14px',
                color: '#32325d',
            },
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Create a token or display an error when the form is submitted.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            stripe.createToken(card).then(function (result) {
                if (result.error) {
                    $("#card-errors").html(result.error.message);
                    toastrs('Error', result.error.message, 'error');
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }

        @endif


    </script>

    <script>
        $(document).on('click', '#shipping', function () {
            var url = $(this).data('url');
            var is_display = $("#shipping").is(":checked");
            $.ajax({
                url: url,
                type: 'get',
                data: {
                    'is_display': is_display,
                },
                success: function (data) {
                    // console.log(data);
                }
            });
        })
    </script>
@endpush --}}

@section('content')
    <style>
        .text-product {
            margin-bottom: 0rem;
            line-height: 1.5;
        }
    </style>

    <section class="section">
        <div class="section-header">
            <h1>{{ __('View Invoice') }}</h1>
            <div class="section-header-breadcrumb">
                @if (\Auth::guard('customer')->check())
                    <div class="breadcrumb-item active"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item"><a href="{{ route('customer.invoice') }}">{{ __('Invoice') }}</a></div>
                @else
                    <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('invoice.index') }}">{{ __('Invoice') }}</a></div>
                @endif
                <div class="breadcrumb-item">{{ $invoice->invoice_code }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="invoice">
                <div class="sale-detail-print">
                    <div class="row mb-10">
                        <div class="col-lg-12">
                            <div class="text-right">
                                <button type="button" onclick="updatePrintStatus({{ $invoice->id }})" id="print_invoice"
                                    class="btn btn-primary btn-action mr-1 float-right">Print Invoice</button>
                                @if (optional($invoice->sales)->sale_type == 1)
                                    <a href="{{ route('pacma.forms.invoice_loan_requisition', $invoice->id) }}"
                                        target="_blank" class="btn btn-warning btn-action mr-1 float-right">
                                        <i class="fas fa-file-alt mr-1"></i> {{ __('Loan Requisition Form') }}
                                    </a>
                                @endif
                                {{-- <button type="button" id="print_view_invoice" class="btn btn-primary btn-action mr-1 float-right">Print View Invoice</button> --}}

                                {{-- <a href="" class="btn btn-primary btn-action mr-1 float-right">
                                    {{__('Create an Invoice')}}
                                </a>
                                <a href="" target="_blank" class="btn btn-primary btn-action mr-1 float-right">
                                            {{__('Download')}}
                                </a>
                                <a href="" target="_blank" class="btn btn-primary btn-action mr-1 float-right">
                                    {{__('Download Hire Sales Agreement')}}
                                </a> --}}
                            </div>
                        </div>
                        @if ($invoice->sales->sale_type == 1)
                            <div class="col-lg-12 mt-3">
                                <div class="text-right">
                                    <a href="{{ route('hire_purchase_contract.print', $invoice->id) }}" target="_blank"
                                        class="btn btn-sm btn-outline-info mr-1">Hire Sales Contract</a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="invoice-title">
                                <h2>{{ __('Invoice Details') }}</h2>
                                <div class="invoice-number">{{ $invoice->invoice_code }}</div>
                            </div>
                            <hr>
                            <div class="row">
                                @if (!empty($invoice->customer))
                                    <div class="col-md-6">
                                        <address class="font-style">
                                            <strong>{{ __('Delivery Address') }}:</strong><br>
                                            @if ($invoice->customer->billing_title == 0)
                                                Mr.
                                            @elseif($invoice->customer->billing_title == 1)
                                                Mrs.
                                            @else
                                                Miss.
                                            @endif
                                            {{ !empty($invoice->customer->billing_name) ? $invoice->customer->billing_name : '' }}<br>
                                            {{ !empty($invoice->customer->billing_phone) ? $invoice->customer->billing_phone : '' }}<br>
                                            @php
                                                $deliveryHouseNo = !empty($invoice->customer->billing_house_no)
                                                    ? trim($invoice->customer->billing_house_no)
                                                    : (!empty($invoice->customer->house_no) ? trim($invoice->customer->house_no) : '');
                                                $deliveryAddress = !empty($invoice->customer->billing_address)
                                                    ? trim($invoice->customer->billing_address)
                                                    : (!empty($invoice->customer->address) ? trim($invoice->customer->address) : '');
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
                                            {{ !empty($invoice->customer->billing_email) ? $invoice->customer->billing_email : '' }}
                                        </address>
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        <address>
                                            <strong>{{ __('Current Address') }}:</strong><br>
                                            @if ($invoice->customer->title == 0)
                                                Mr.
                                            @elseif($invoice->customer->title == 1)
                                                Mrs.
                                            @else
                                                Miss.
                                            @endif
                                            {{ !empty($invoice->customer->name) ? $invoice->customer->name : '' }}<br>
                                            {{ !empty($invoice->customer->phone) ? $invoice->customer->phone : '' }}<br>
                                            @php
                                                $currentHouseNo = !empty($invoice->customer->house_no) ? trim($invoice->customer->house_no) : '';
                                                $currentAddress = !empty($invoice->customer->address) ? trim($invoice->customer->address) : '';
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
                                            {{ !empty($invoice->customer->email) ? $invoice->customer->email : '' }}
                                        </address>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <address>
                                        <strong>{{ __('Type') }}:</strong><br>
                                        @if ($invoice->sales->sale_type == 0)
                                            {{-- <span class="badge badge-primary"></span> --}}
                                            <span class="badge badge-success">{{ __('Cash Sales') }}</span>
                                        @elseif($invoice->sales->sale_type == 1)
                                            <span class="badge badge-info">{{ __('Hire Sales') }}</span>
                                            {{-- @elseif($invoice->sales->status == 3)
                                            <span class="badge badge-danger">Over Due</span> --}}
                                        @endif
                                    </address>
                                </div>
                                <div class="col-md-4 text-md-center">
                                    <address>
                                        <strong>{{ __('Issue Date') }} :</strong><br>
                                        {{ $invoice->sales->date_of_sale }}<br><br>
                                    </address>
                                </div>
                                <div class="col-md-4 text-md-right">
                                    @if (isset($invoice->sales->due_date))
                                        <address>
                                            <strong>{{ __('Due Date') }} :</strong><br>
                                            {{ $invoice->sales->due_date }}<br><br>
                                        </address>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{ __('Product Summary') }}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <thead>
                                        <tr>
                                            <th data-width="40">{{ __('Product Code') }}</th>
                                            <th>{{ __('Product Description') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Purchasing Price') }} ({{ $currencySymbol }})</th>
                                            <th>{{ __('Purchase Type') }}</th>
                                            <th>{{ __('Discount') }} ({{ $currencySymbol }})</th>
                                            @if ($invoice->sales->sale_type == 0)
                                                <th class="text-right">{{ __('Warranty Charges') }}
                                                    ({{ $currencySymbol }})</th>
                                                <th class="text-right">{{ __('Insurance Premium') }}
                                                    ({{ $currencySymbol }})</th>
                                            @endif
                                            @if ($invoice->sales->sale_type == 1)
                                                <th>{{ __('Down Payment Rate') }}</th>
                                                <th>{{ __('Installment Time Period') }}</th>
                                                <th>{{ __('Interest Rate') }}</th>
                                            @endif
                                            {{-- <th class="text-right" width="12%">{{__('Net Amount')}}<br><small class="text-danger">{{__('before tax & discount')}}</small></th> --}}
                                            <th class="text-right" width="12%">{{ __('Net Amount') }}
                                                ({{ $currencySymbol }})</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($invoice->sales->modal_type == 1)
                                            <tr>
                                                <td style="white-space: nowrap;">
                                                    <p class="text-product">{{ $invoice->sales->products->barcode }}</p>
                                                    <p class="text-product">{{ $invoice->sales->products->serial_number }}
                                                    </p>
                                                </td>
                                                <td>{{ $invoice->sales->products->name }}</td>
                                                <td>{{ $invoice->sales->quantity }}</td>
                                                <td>
                                                    {{ number_format($invoice->sales->unit_price ?? 0, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($invoice->sales->unit_price) }} --}}
                                                </td>
                                                <td>
                                                    @if ($invoice->sales->sale_type == 0)
                                                        {{ __('Cash Sales') }}
                                                    @elseif ($invoice->sales->sale_type == 1)
                                                        {{ __('Hire Sales') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ number_format($invoice->sales->discount ?? 0, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($invoice->sales->discount) }} --}}
                                                </td>
                                                @if ($invoice->sales->sale_type == 1)
                                                    <td>{{ $invoice->sales->products->product_category['down_payment'] }}
                                                        {{ __('%') }}</td>
                                                    <td>{{ $invoice->sales->installment }} {{ __('Months') }}</td>
                                                    <td>{{ $invoice->sales->interest_rate }} {{ __('%') }}</td>
                                                @endif
                                                <td class="text-right">
                                                    {{ number_format($invoice->sales->net_amount ?? 0, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($invoice->sales->net_amount) }} --}}
                                                </td>
                                            </tr>
                                        @elseif ($invoice->sales->modal_type == 2)
                                            @foreach ($invoice->sales->saleItems as $sale)
                                                <tr>
                                                    <td style="white-space: nowrap;">
                                                        <p class="text-product">{{ $sale->products->barcode }}</p>
                                                        <p class="text-product">{{ $sale->products->serial_number }}</p>
                                                    </td>
                                                    <td>{{ $sale->products->name }}</td>
                                                    <td>{{ $sale->quantity }}</td>
                                                    <td>{{ number_format($sale->unit_price, 2) }}</td>
                                                    <td>
                                                        @if ($invoice->sales->sale_type == 0)
                                                            {{ __('Cash Sales') }}
                                                        @elseif ($invoice->sales->sale_type == 1)
                                                            {{ __('Hire Sales') }}
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($sale->discount, 2) }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($sale->warranty_charges ?? 0, 2) }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($sale->insurance_premium ?? 0, 2) }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($sale->net_amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    @if ($invoice->sales->sale_type == 1)
                                        <tfoot>
                                            <tr>
                                                <td colspan="10"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Down Payment') }}</b></td>
                                                <td class="text-right">
                                                    {{ number_format($invoice->sales->down_payment, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($invoice->sales->down_payment) }} --}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Advance Payment') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($invoice->sales->advance_amount, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($invoice->sales->advance_amount) }} --}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Interest Amount') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($invoice->sales->interest_amount, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($invoice->sales->interest_amount) }} --}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right">
                                                    <b>{{ __('Monthly Installment') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($invoice->sales->monthly_installment, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($invoice->sales->monthly_installment) }} --}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right">
                                                    <b>{{ __('Total Investment Schedule for Payment') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($invoice->sales->total_due_amount, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($invoice->sales->total_due_amount) }} --}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Balance Due') }}</b></td>
                                                <td class="text-right">
                                                    {{ number_format($invoice->sales->current_due_amount, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($invoice->sales->current_due_amount) }} --}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Document Charges') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($invoice->sales->document_charges, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($invoice->sales->document_charges) }} --}}
                                                </td>
                                            </tr>
                                            @if ($hirePurchaseDetail)
                                                <tr>
                                                    <td colspan="7"></td>
                                                    <td colspan="2" class="text-right">
                                                        <b>{{ __('Warranty Charges') }}</b>
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($hirePurchaseDetail->warranty_charges, 2) }}
                                                        {{-- {{ \Auth::user()->priceFormat($hirePurchaseDetail->warranty_charges) }} --}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7"></td>
                                                    <td colspan="2" class="text-right">
                                                        <b>{{ __('Insurance Premium') }}</b>
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($hirePurchaseDetail->insurance_premium, 2) }}
                                                        {{-- {{ \Auth::user()->priceFormat($hirePurchaseDetail->insurance_premium) }} --}}
                                                    </td>
                                                </tr>
                                            @endif
                                        </tfoot>
                                    @endif
                                    @if ($invoice->sales->modal_type == 2)
                                        <tfoot>
                                            {{-- @php
                                                dump($invoice->sales);
                                            @endphp --}}
                                            <tr>
                                                <td colspan="6"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Total Amount') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($invoice->sales->net_amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="9"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td colspan="2" class="text-right"
                                                    style="font-weight: 300 !important;">
                                                    <b>{{ __('Advance Amount') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($invoice->sales->advance_amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td colspan="2" class="text-right"
                                                    style="font-weight: 300 !important;">
                                                    <b>{{ __('Paid Amount') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($invoice->sales->paid_amount, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{__('Receipt Summary')}}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <tr>
                                        <th>{{__('Date')}}</th>
                                        <th>{{__('Amount')}}</th>
                                        <th>{{__('Payment Type')}}</th>
                                        <th>{{__('Account')}}</th>
                                        <th>{{__('Reference')}}</th>
                                        <th>{{__('Description')}}</th>
                                        <th>{{__('Receipt')}}</th>
                                        <th>{{__('OrderId')}}</th>
                                        @can('delete payment invoice')
                                            <th class="text-right">{{__('Action')}}</th>
                                        @endcan
                                    </tr>
                                    @foreach ($invoice->payments as $key => $payment)

                                        <tr>
                                            <td>{{\Auth::user()->dateFormat($payment->date)}}</td>
                                            <td>{{\Auth::user()->priceFormat($payment->amount)}}</td>
                                            <td>{{$payment->payment_type}}</td>
                                            <td>{{!empty($payment->bankAccount)?$payment->bankAccount->bank_name.' '.$payment->bankAccount->holder_name:'--'}}</td>
                                            <td>{{!empty($payment->reference)?$payment->reference:'--'}}</td>
                                            <td>{{!empty($payment->description)?$payment->description:'--'}}</td>
                                            <td>@if (!empty($payment->receipt))<a href="{{$payment->receipt}}" target="_blank"> <i class="fas fa-file"></i></a>@else -- @endif</td>
                                            <td>{{!empty($payment->order_id)?$payment->order_id:'--'}}</td>
                                            @can('delete invoice product')
                                                <td class="text-right">

                                                    <a href="#!" class="btn btn-danger btn-action" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$payment->id}}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'post', 'route' => ['invoice.payment.destroy',$invoice->id,$payment->id],'id'=>'delete-form-'.$payment->id]) !!}
                                                    {!! Form::close() !!}

                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div> --}}
                    {{-- <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{__('Credit Note Summary')}}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <tr>
                                        <th>{{__('Date')}}</th>
                                        <th class="text-center">{{__('Amount')}}</th>
                                        <th class="text-center">{{__('Description')}}</th>
                                        @if (Gate::check('edit credit note') || Gate::check('delete credit note'))
                                            <th class="text-right">{{__('Action')}}</th>
                                        @endif
                                    </tr>
                                    @foreach ($invoice->creditNote as $key => $creditNote)
                                        <tr>
                                            <td>{{\Auth::user()->dateFormat($creditNote->date)}}</td>
                                            <td class="text-center">{{\Auth::user()->priceFormat($creditNote->amount)}}</td>
                                            <td class="text-center">{{$creditNote->description}}</td>
                                            <td class="text-right">
                                                @can('edit credit note')
                                                    <a data-url="{{ route('invoice.edit.credit.note',[$creditNote->invoice,$creditNote->id]) }}" data-ajax-popup="true" data-title="{{__('Add Credit Note')}}" data-toggle="tooltip" data-original-title="{{__('Credit Note')}}" href="#" class="btn btn-primary btn-action mr-1" data-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                @endcan
                                                @can('delete credit note')
                                                    <a href="#!" class="btn btn-danger btn-action " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$creditNote->id}}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => array('invoice.delete.credit.note', $creditNote->invoice,$creditNote->id),'id'=>'delete-form-'.$creditNote->id]) !!}
                                                    {!! Form::close() !!}
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </section>
    {{-- @auth('customer')
        @if ($invoice->getDue() > 0)
            <!-- Modal -->
            <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentModalLabel">{{ __('Add Payment') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            @if (Utility::getValByName('enable_stripe') == 'on' && Utility::getValByName('enable_paypal') == 'on')
                                <ul class="nav nav-pills" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#stripe-payment" role="tab" aria-controls="stripe" aria-selected="true">{{ __('Stripe') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#paypal-payment" role="tab" aria-controls="paypal" aria-selected="false">{{ __('Paypal') }}</a>
                                    </li>
                                </ul>
                            @endif
                            <div class="tab-content">
                                @if (Utility::getValByName('enable_stripe') == 'on')
                                    <div class="tab-pane fade {{ ((Utility::getValByName('enable_stripe') == 'on' && Utility::getValByName('enable_paypal') == 'on') || Utility::getValByName('enable_stripe') == 'on') ? "show active" : "" }}" id="stripe-payment" role="tabpanel" aria-labelledby="stripe-payment">
                                        <form method="post" action="{{ route('customer.invoice.payment',$invoice->id) }}" class="require-validation" id="payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <div class="custom-radio">
                                                        <label class="font-16 font-weight-bold">{{__('Credit / Debit Card')}}</label>
                                                    </div>
                                                    <p class="mb-0 pt-1">{{__('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.')}}</p>
                                                </div>
                                                <div class="col-sm-4 text-sm-right mt-3 mt-sm-0">
                                                    <img src="{{asset('assets/img/payments/master.png')}}" height="24" alt="master-card-img">
                                                    <img src="{{asset('assets/img/payments/discover.png')}}" height="24" alt="discover-card-img">
                                                    <img src="{{asset('assets/img/payments/visa.png')}}" height="24" alt="visa-card-img">
                                                    <img src="{{asset('assets/img/payments/american express.png')}}" height="24" alt="american-express-card-img">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="card-name-on">{{__('Name on card')}}</label>
                                                        <input type="text" name="name" id="card-name-on" class="form-control required" placeholder="{{\Auth::user()->name}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div id="card-element">

                                                    </div>
                                                    <div id="card-errors" role="alert"></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <br>
                                                    <label for="amount">{{ __('Amount') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-prepend"><span class="input-group-text">{{ Utility::getValByName('site_currency') }}</span></span>
                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="error" style="display: none;">
                                                        <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mt-3">
                                                <button class="btn btn-primary" type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                @if (Utility::getValByName('enable_paypal') == 'on')
                                    <div class="tab-pane fade {{ (Utility::getValByName('enable_stripe') != 'on' && Utility::getValByName('enable_paypal') == 'on') ? "show active" : "" }}" id="paypal-payment" role="tabpanel" aria-labelledby="paypal-payment">
                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="payment-form" action="{{ route('customer.pay.with.paypal',$invoice->id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount">{{ __('Amount') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-prepend"><span class="input-group-text">{{ Utility::getValByName('site_currency') }}</span></span>
                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                        @error('amount')
                                                        <span class="invalid-amount" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mt-3">
                                                <button class="btn btn-primary" name="submit" type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth --}}
@endsection
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
    function updatePrintStatus(invoiceId) {
        // Send AJAX request to update print status
        fetch(`/invoice-update-print-status/${invoiceId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for Laravel
            },
            body: JSON.stringify({
                print_status: 1
            })
        }).then(response => {
            if (response.ok) {
                console.log('Print status updated to 1');
            } else {
                console.error('Failed to update print status');
            }
        }).catch(error => console.error('Error:', error));
    }
    // var invoice = <?php echo json_encode($invoice); ?>;
    $(document).ready(function() {
        // console.log(invoice);
        $('#print_invoice').click(function() {
            window.open(
                "{{ route('invoice_print.print_preview', ['id' => $invoice->id, 'command' => 'print']) }}"
            );
        });
        $('#print_view_invoice').click(function() {
            window.open(
                "{{ route('invoice_print.print_preview', ['id' => $invoice->id, 'command' => 'view']) }}"
            );
        });
    });
</script>
