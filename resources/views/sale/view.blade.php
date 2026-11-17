@extends('layouts.admin')
@section('page-title')
    {{ __('Sale Detail') }}
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

        .hr-class {
            border: 1px solid #a3a3a3;
            margin-bottom: -50px;
            margin-top: -100px;
        }
    </style>

    <section class="section">
        <div class="section-header">
            <h1>{{ __('View Sale') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('sale.index') }}">{{ __('Sale') }}</a></div>
                <div class="breadcrumb-item">{{ __('Sale View') }}</div>
            </div>
        </div>
        <div class="section-body">
            <div class="invoice">
                <div class="sale-detail-print">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="invoice-title row align-items-start">
                                <div class="col-md-6">
                                    <h2>{{ __('Sale Details') }} - {{ optional($sales->invoice)->invoice_code ?? $sales->sale_id }}</h2>
                                </div>
                                <div class="col-md-6 d-flex justify-content-md-end align-items-center flex-wrap" style="gap: 8px;">
                                    @if ($sales->invoice)
                                        <a href="{{ route('invoice_print.print_preview', ['id' => $sales->invoice->id, 'command' => 'print']) }}"
                                            target="_blank" class="btn btn-primary"
                                            onclick="updatePrintStatus({{ $sales->invoice->id }})">
                                            <i class="fas fa-print mr-1"></i> Print Invoice
                                        </a>

                                        @can('delete invoice product')
                                            <a href="{{ route('invoice_print.print_preview', ['id' => $sales->invoice->id, 'command' => 'print']) }}"
                                                target="_blank" class="btn btn-primary"
                                                onclick="adminUpdatePrintStatus({{ $sales->invoice->id }})">
                                                <i class="fas fa-print mr-1"></i> Print Original Invoice
                                            </a>
                                        @endcan

                                        @if ($sales->sale_type == 1)
                                            <a href="{{ route('hire_purchase_contract.print', $sales->invoice->id) }}"
                                                target="_blank" class="btn btn-info">
                                                <i class="fas fa-file-contract mr-1"></i> {{ __('Hire Sales Contract') }}
                                            </a>
                                            <a href="{{ route('pacma.forms.loan_requisition', $sales->id) }}"
                                                target="_blank" class="btn btn-warning">
                                                <i class="fas fa-file-alt mr-1"></i> {{ __('Loan Requisition Form') }}
                                            </a>
                                        @endif
                                    @endif
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
                                            {{ !empty($sales->customer->billing_name) ? $sales->customer->billing_name : '' }}
                                            {{ !empty($sales->customer->last_name) ? $sales->customer->last_name : '' }}<br>
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
                                            {{ !empty($sales->customer->name) ? $sales->customer->name : '' }}
                                            {{ !empty($sales->customer->last_name) ? $sales->customer->last_name : '' }}<br>
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
                                            {{ !empty($sales->customer->email) ? $sales->customer->email : '' }}<br>
                                            {{ !empty($sales->customer->nic_no) ? $sales->customer->nic_no : '' }}
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
                                @else
                                    <div class="col-md-4">
                                        <address class="font-style">
                                            <strong>{{ __('Customer Details') }}:</strong><br>
                                            {{ $sales->customer_name ?? '' }}<br>
                                            {{ $sales->customer_phone_no ?? '' }}
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

                                    </div>
                                    <div class="col-md-4">
                                        @if (!empty($sales->introducer_id))
                                            <address>
                                                <strong>{{ __('Salesman') }}:</strong><br>
                                                {{ $introducer->name ?? '' }}
                                            </address>
                                        @endif
                                    </div>
                                @endif
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
                                            <th>{{ __('Product Code & Serial Number') }}</th>
                                            <th>{{ __('Product Discription') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th width="10%">{{ __('Purchasing Price') }} ({{ $currencySymbol }})</th>
                                            <th>{{ __('Purchase Type') }}</th>
                                            <th>{{ __('Discount') }} ({{ $currencySymbol }})</th>
                                            @if ($sales->sale_type == 0)
                                                <th class="text-right d-none">{{ __('Warranty Charges') }}</th>
                                            @endif
                                            @if ($sales->sale_type == 1)
                                                <th>{{ __('Down Payement rate') }}</th>
                                                <th>{{ __('Installment Time Period') }}</th>
                                                <th width="10%">{{ __('Interest rate') }}</th>
                                            @endif
                                            <th class="text-right" width="12%">{{ __('Net Amount') }}
                                                ({{ $currencySymbol }})</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($sales->modal_type == 1)
                                            {{-- @dd($hirePurchaseDetail->warranty->serial_number) --}}
                                            <tr>
                                                <td>
                                                    <p class="text-product">{{ $sales->products->barcode }}</p>
                                                    <p class="text-product">
                                                        {{ $hirePurchaseDetail->warranty->serial_number ?? '' }}</p>
                                                </td>

                                                <td>
                                                    <p class="text-product">{{ $sales->products->name }}</p>
                                                </td>

                                                <td style="text-align: center; ">{{ $sales->quantity }}</td>
                                                <td>{{ number_format($sales->unit_price, 2) }}</td>
                                                <td>
                                                    @if ($sales->sale_type == 0)
                                                        {{ __('Cash Sales') }}
                                                    @elseif ($sales->sale_type == 1)
                                                        {{ __('Hire Sales') }}
                                                    @endif
                                                </td>
                                                <td>{{ number_format($sales->discount, 2) }}</td>
                                                @if ($sales->sale_type == 0)
                                                    <td class="text-right d-none">
                                                        {{ number_format($hirePurchaseDetail->warranty_charges ?? 0, 2) }}
                                                    </td>
                                                @endif

                                                @if ($sales->sale_type == 1)
                                                    <td>{{ $sales->products->product_category['down_payment'] }}
                                                        {{ __('%') }}</td>
                                                    <td>{{ $sales->installment }} {{ __('months') }}</td>
                                                    <td>{{ $sales->interest_rate }} {{ __('%') }}</td>
                                                @endif

                                                <td class="text-right">
                                                    {{ number_format($sales->net_amount, 2) }}</td>
                                            </tr>
                                        @elseif ($sales->modal_type == 2)
                                            @foreach ($sales->saleItems as $sale)
                                                <tr>
                                                    <td>
                                                        <p class="text-product">{{ $sale->products->barcode }}</p>
                                                        <p class="text-product">{{ $sale->warranty->serial_number ?? '' }}
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p class="text-product">{{ $sale->products->name }}</p>
                                                    </td>

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
                                                    <td class="text-right d-none">
                                                        {{ number_format($sale->warranty_charges ?? 0, 2) }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($sale->net_amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    @if ($sales->sale_type == 1)
                                        <tfoot>
                                            <tr>
                                                <td colspan="9"></td>
                                            </tr>
                                            {{-- @if ($sales->advance_amount > 0)
                                                <tr>
                                                    <td colspan="7"></td>
                                                    <td colspan="2" class="text-right"><b>{{__('Advance Amount')}}</b>
                                                    </td>
                                                    <td class="text-right">{{__('Rs.')}} {{ number_format($sales->advance_amount, 2) }}</td>
                                                    </tr>
                                                    @endif --}}
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Down Payement') }}</b></td>
                                                <td class="text-right">
                                                    {{ number_format($sales->down_payment, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Interest Amount') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($sales->interest_amount, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right">
                                                    <b>{{ __('Monthly Installment') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($sales->monthly_installment, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right">
                                                    <b>{{ __('Total of Invesment shedule for payment') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($sales->total_due_amount, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right">
                                                    <b>{{ __('Outstanding Balance') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($sales->current_due_amount, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Document Charges') }}</b>
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($sales->document_charges, 2) }}
                                                </td>
                                            </tr>
                                            @if ($hirePurchaseDetail)
                                                <tr>
                                                    <td colspan="7"></td>
                                                    <td colspan="2" class="text-right d-none">
                                                        <b>{{ __('Warranty Charges') }}</b>
                                                    </td>
                                                    <td class="text-right d-none">
                                                        {{ number_format($hirePurchaseDetail->warranty_charges, 2) }}</td>
                                                </tr>
                                            @endif
                                        </tfoot>
                                    @endif
                                    @if ($sales->modal_type == 2)
                                        <tfoot>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2" class="text-right"><b>{{ __('Total Amount') }}</b></td>
                                                <td class="text-right">
                                                    {{ number_format($sales->net_amount, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{__('Receipt Summary')}}
                            </div>
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
                                        <td>{{!empty($payment->bankAccount)?$payment->bankAccount->bank_name.' '.$payment->bankAccount->holder_name:'--'}}
                                        </td>
                                        <td>{{!empty($payment->reference)?$payment->reference:'--'}}</td>
                                        <td>{{!empty($payment->description)?$payment->description:'--'}}</td>
                                        <td>@if (!empty($payment->receipt))<a href="{{$payment->receipt}}" target="_blank"> <i
                                                    class="fas fa-file"></i></a>@else -- @endif</td>
                                        <td>{{!empty($payment->order_id)?$payment->order_id:'--'}}</td>
                                        @can('delete invoice product')
                                        <td class="text-right">

                                            <a href="#!" class="btn btn-danger btn-action" data-toggle="tooltip"
                                                data-original-title="{{__('Delete')}}"
                                                data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                data-confirm-yes="document.getElementById('delete-form-{{$payment->id}}').submit();">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            {!! Form::open(['method' => 'post', 'route' =>
                                            ['invoice.payment.destroy',$invoice->id,$payment->id],'id'=>'delete-form-'.$payment->id]) !!}
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
                                            <a data-url="{{ route('invoice.edit.credit.note',[$creditNote->invoice,$creditNote->id]) }}"
                                                data-ajax-popup="true" data-title="{{__('Add Credit Note')}}" data-toggle="tooltip"
                                                data-original-title="{{__('Credit Note')}}" href="#" class="btn btn-primary btn-action mr-1"
                                                data-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            @endcan
                                            @can('delete credit note')
                                            <a href="#!" class="btn btn-danger btn-action " data-toggle="tooltip"
                                                data-original-title="{{__('Delete')}}"
                                                data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                data-confirm-yes="document.getElementById('delete-form-{{$creditNote->id}}').submit();">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => array('invoice.delete.credit.note',
                                            $creditNote->invoice,$creditNote->id),'id'=>'delete-form-'.$creditNote->id]) !!}
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
    <script>
        function updatePrintStatus(invoiceId) {
            // Send AJAX request to update print status
            fetch(`/sales-update-print-status/${invoiceId}`, {
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

        function adminUpdatePrintStatus(invoiceId) {
            // Send AJAX request to update print status to 3
            fetch(`/sales-admin-update-print-status/${invoiceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for Laravel
                },
                body: JSON.stringify({
                    print_status: 3
                }) // Set print status to 3
            }).then(response => {
                if (response.ok) {
                    console.log('Print status updated to 3');
                } else {
                    console.error('Failed to update print status');
                }
            }).catch(error => console.error('Error:', error));
        }
    </script>
@endsection
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
                    <a class="nav-link active" data-toggle="tab" href="#stripe-payment" role="tab" aria-controls="stripe"
                        aria-selected="true">{{ __('Stripe') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#paypal-payment" role="tab" aria-controls="paypal"
                        aria-selected="false">{{ __('Paypal') }}</a>
                </li>
            </ul>
            @endif
            <div class="tab-content">
                @if (Utility::getValByName('enable_stripe') == 'on')
                <div class="tab-pane fade {{ ((Utility::getValByName('enable_stripe') == 'on' && Utility::getValByName('enable_paypal') == 'on') || Utility::getValByName('enable_stripe') == 'on') ? "show active" : "" }}"
                    id="stripe-payment" role="tabpanel" aria-labelledby="stripe-payment">
                    <form method="post" action="{{ route('customer.invoice.payment',$invoice->id) }}" class="require-validation"
                        id="payment-form">
                        @csrf
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="custom-radio">
                                    <label class="font-16 font-weight-bold">{{__('Credit / Debit Card')}}</label>
                                </div>
                                <p class="mb-0 pt-1">
                                    {{__('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.')}}
                                </p>
                            </div>
                            <div class="col-sm-4 text-sm-right mt-3 mt-sm-0">
                                <img src="{{asset('assets/img/payments/master.png')}}" height="24" alt="master-card-img">
                                <img src="{{asset('assets/img/payments/discover.png')}}" height="24" alt="discover-card-img">
                                <img src="{{asset('assets/img/payments/visa.png')}}" height="24" alt="visa-card-img">
                                <img src="{{asset('assets/img/payments/american express.png')}}" height="24"
                                    alt="american-express-card-img">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="card-name-on">{{__('Name on card')}}</label>
                                    <input type="text" name="name" id="card-name-on" class="form-control required"
                                        placeholder="{{\Auth::user()->name}}">
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
                                    <span class="input-group-prepend"><span
                                            class="input-group-text">{{ Utility::getValByName('site_currency') }}</span></span>
                                    <input class="form-control" required="required" min="0" name="amount" type="number"
                                        value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}"
                                        id="amount">
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
                <div class="tab-pane fade {{ (Utility::getValByName('enable_stripe') != 'on' && Utility::getValByName('enable_paypal') == 'on') ? "show active" : "" }}"
                    id="paypal-payment" role="tabpanel" aria-labelledby="paypal-payment">
                    <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="payment-form"
                        action="{{ route('customer.pay.with.paypal',$invoice->id) }}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="amount">{{ __('Amount') }}</label>
                                <div class="input-group">
                                    <span class="input-group-prepend"><span
                                            class="input-group-text">{{ Utility::getValByName('site_currency') }}</span></span>
                                    <input class="form-control" required="required" min="0" name="amount" type="number"
                                        value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}"
                                        id="amount">
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
