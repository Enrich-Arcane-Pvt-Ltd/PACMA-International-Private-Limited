<style>
    #barcode_input_container {
        margin-right: -25px;
        border-radius: 50px;
        display: flex;
        background: #3abaf4;
    }

    #barcode_icon {
        padding: 11px 13px;
        color: #ffffff;
    }

    #barcode_input {
        border-radius: 0px;
        overflow: hidden;
        border-radius: 0px 50px 50px 00px;
        border: 1px solid #3abaf4;
    }

    .se-pre-con {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url("{{ asset('argon') }}/img/loader/Spinner-1s-200px.gif") center no-repeat #ffffff99;
        display: none;
    }
</style>
<div class="se-pre-con"></div>

<form method="post" action="{{ route('sale.create') }}" id="sale_form">
    @csrf
    <input type="hidden" name="waitting_sale_id" value="{{ $waittingSales->id }}">
    <input type="hidden" name="created_by" value="{{ $waittingSales->created_by }}">
    <input type="hidden" name="customer_type" value="1">
    <input type="hidden" name="modal_type" id="modal_type" value="1">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('date_of_sale', __('Date of Sale')) }}
            <div class="input-group">
                {{ Form::text('date_of_sale', $waittingSales->date_of_sale, ['class' => 'form-control datepicker', 'required' => 'required', 'data-empty-message' => 'Please Select Date of Sale', 'data-focus' => 'date_of_sale']) }}
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('customer', __('Customer')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" data-live-search="true"
                    name="customer" id="customer" required data-empty-message="Please Select Customer"
                    data-focus="customer">
                    <option value="">Select Customer</option>
                    @foreach ($customers as $customer)
                        <option @if ($waittingSales->customer_id == $customer->id) selected @endif value="{{ $customer->id }}">
                            {{ $customer->nic_no . ' - ' . $customer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- <div class="form-group col-md-6">
            {{ Form::label('sale_type', __('Sale Type')) }}
            <div class="input-group">
                {{ Form::select('sale_type', $saleTypes, $waittingSales->sale_type, ['class' => 'form-control customer-sel font-style', 'required' => 'required', 'data-empty-message' => 'Please Select Sale Type', 'data-focus' => 'sale_type']) }}
            </div>
        </div> --}}
        <div class="form-group col-md-6" style="display: none;">
            {{ Form::label('sale_type', __('Sale Type')) }}<span class="text-danger">*</span>
            <div class="input-group">
                {{ Form::text('sale_type', 1, ['class' => 'form-control customer-sel font-style', 'required' => 'required', 'data-empty-message' => 'Please Select Sale Type', 'data-focus' => 'sale_type']) }}
            </div>
        </div>

        <div class="form-group col-md-6" id="guarantor_div">
            {{ Form::label('guarantor', __('Guarantor')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" data-live-search="true"
                    name="guarantor" id="guarantor" data-empty-message="Please Select Guarantor" data-focus="guarantor">
                    <option value="">Select Guarantor</option>
                    @foreach ($guarantors as $guarantor)
                        <option @if ($waittingSales->guarantor_id == $guarantor->id) selected @endif value="{{ $guarantor->id }}">
                            {{ $guarantor->nic_no . ' - ' . $guarantor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-6" id="guarantor_div_2">
            {{ Form::label('guarantor_2', __('Guarantor 2')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" data-live-search="true"
                    name="guarantor_2" id="guarantor_2">
                    <option value="">Select 2nd Guarantor</option>
                    @foreach ($guarantors as $guarantor)
                        <option @if ($waittingSales->guarantor_2_id == $guarantor->id) selected @endif value="{{ $guarantor->id }}">
                            {{ $guarantor->nic_no . ' - ' . $guarantor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- <div class="form-group col-md-6" id="officer_div">
            {{ Form::label('officer_id', __('Officer')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style" name="officer_id" id="officer_id">
                    <option value="">Select Officer</option>
                    @foreach ($users as $user)
                        <option @if ($waittingSales->officer_id == $user->id) selected @endif value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div> --}}

        <div class="form-group col-md-6">
            {{ Form::label('introducer_id', __('Salesman')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style" name="introducer_id" id="introducer_id"
                    data-empty-message="Please Select Salesman" data-focus="introducer_id" required>
                    <option value="">Select Salesman</option>
                    @foreach ($users as $user)
                        <option @if ($waittingSales->introducer_id == $user->id) selected @endif value="{{ $user->id }}">
                            {{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- <div class="col-12">
            <div class="card repeater">
                <div class="card-header">
                    <h4 class="sub-title">{{__('Products')}}</h4>
                    <div class="card-header-action">

                        <div id="barcode_input_container">
                            <i class="fas fa-barcode" id="barcode_icon"></i>
                            {{ Form::text('barcode', '', array('class'=>'form-control', 'id'=>'barcode_input', 'onkeypress'=>'scanEvent(event)')) }}
                        </div>

                    </div>
                </div>
            </div>
        </div> --}}

        <div class="form-group col-md-6">
            {{-- <input type="hidden" name="inventory_id" id="inventory_id" value="{{ $waittingSales->inventory_id }}"> --}}
            {{ Form::label('product', __('Product Code')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style" name="product" id="product" required
                    data-empty-message="Please Select Product Code" data-focus="product">
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option @if ($waittingSales->product_id == $product->products->id) selected @endif value="{{ $product->products->id }}">
                            {{ $product->products->barcode }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-6">
            <input type="hidden" name="inventory_id" id="inventory_id" value="{{ $waittingSales->inventory_id }}">
            {{ Form::label('product', __('Product Description')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style" name="product" id="product" required
                    data-empty-message="Please Select Product" data-focus="product">
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option @if ($waittingSales->product_id == $product->products->id) selected @endif value="{{ $product->products->id }}">
                            {{ $product->products->brands->name . ' - ' . $product->products->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('unit_price', __('Purchasing Price')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('unit_price', $waittingSales->unit_price, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Purchasing Price', 'data-focus' => 'unit_price']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-3">
            {{ Form::label('available_quantity', __('Available Quantity')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('available_quantity', $waittingSales->available_quantity, ['class' => 'form-control available_quantity', 'readonly' => 'readonly']) }}
            </div>
        </div>

        <div class="form-group col-md-3">
            {{ Form::label('quantity', __('Quantity')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('quantity', $waittingSales->quantity, ['class' => 'form-control quantity', 'required' => 'required', 'data-empty-message' => 'Please Enter Quantity', 'data-focus' => 'quantity']) }}
            </div>
        </div>
        <input type="hidden" name="gross_amount" id="gross_amount" value="{{ $waittingSales->gross_amount }}">
        {{-- <div class="form-group col-md-6">
            {{ Form::label('gross_amount', __('Gross Amount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('gross_amount', $waittingSales->gross_amount, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Gross Amount', 'data-focus' => 'gross_amount', 'readonly' => 'readonly']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div> --}}
        {{--
        <div class="form-group col-md-6">
            {{ Form::label('discount', __('Discount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('discount', $waittingSales->discount, ['class' => 'form-control discount', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Entert Discount', 'data-focus' => 'discount']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="form-group col-md-6">
            {{ Form::label('gross_hire_price', __('Gross Hire Price')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('gross_hire_price', $waittingSales->gross_hire_price, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Gross Hire Price', 'data-focus' => 'gross_hire_price', 'readonly' => 'readonly']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('net_amount', __('Net Amount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('net_amount', $waittingSales->net_amount, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Net Amount', 'data-focus' => 'net_amount', 'readonly' => 'readonly']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="higher_purchase_div">
        <input type="hidden" id="down_payment_fixed" name="down_payment_fixed">
        <input type="hidden" id="down_payment_rate" name="down_payment_rate">
        <div class="form-group col-md-6">
            {{ Form::label('down_payment', __('Down Payment')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('down_payment', $waittingSales->down_payment, ['class' => 'form-control price', 'step' => '0.01']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('outstanding_amount', __('Outstanding Amount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('outstanding_amount', $waittingSales->outstanding_amount, ['class' => 'form-control price', 'step' => '0.01', 'readonly' => 'readonly']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('installment', __('Installment')) }}
            <div class="input-group colorpickerinput">
                <select class="form-control installment" name="installment" id="installment">
                    <option value="">Select Time Period</option>
                    @foreach ($timePeriods as $timePeriod)
                        <option @if ($waittingSales->installment == $timePeriod->time_period) selected @endif
                            value="{{ $timePeriod->time_period }}">{{ $timePeriod->time_period }}</option>
                    @endforeach
                </select>
                <div class="input-group-text">
                    Months
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('interest_rate', __('Interest Rate')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('interest_rate', $waittingSales->interest_rate, ['class' => 'form-control interest_rate', 'step' => '0.01', 'id' => 'interest_rate']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ '%' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('interest_amount', __('Interest Amount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('interest_amount', $waittingSales->interest_amount, ['class' => 'form-control price', 'step' => '0.01']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('total_due_amount', __('Total Due Amount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('total_due_amount', $waittingSales->total_due_amount, ['class' => 'form-control price', 'step' => '0.01']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('monthly_installment', __('Monthly Installment')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('monthly_installment', $waittingSales->monthly_installment, ['class' => 'form-control price', 'step' => '0.01']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('installment_date', __('Installment Date')) }}
            <div class="input-group colorpickerinput">
                <select class="form-control" name="installment_date" id="installment_date">
                    <option value="">Select Installment Date</option>
                    @for ($i = 1; $i < 31; $i++)
                        <option @if ($waittingSales->installment_date == $i) selected @endif value="{{ $i }}">
                            {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('due_date', __('Due Date')) }}
            <div class="input-group colorpickerinput">
                {{ Form::text('due_date', $waittingSales->due_date, ['class' => 'form-control', 'readonly' => 'readonly']) }}
            </div>
        </div>

        <div class="form-group col-md-6">
            <input type="hidden" name="document_charges_in_words" id="document_charges_in_words">
            {{ Form::label('document_charges', __('Document Charges')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('document_charges', $waittingSales->document_charges, ['class' => 'form-control price', 'step' => '0.01']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="advanced_div">
        <input type="hidden" name="advance_id" id="advance_id">
        <div class="form-group col-md-6">
            {{ Form::label('advance_amount', __('Advance Amount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('advance_amount', $waittingSales->advance_amount, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Insert Advance Amount', 'data-focus' => 'advance_amount']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('paid_amount', __('Paid Amount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('paid_amount', $waittingSales->paid_amount, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Insert Paid Amount', 'data-focus' => 'paid_amount']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('paid_amount_in_words', __('Paid Amount In Words')) }}
            <div class="input-group colorpickerinput">
                <textarea class="form-control paid_amount_in_words" id="paid_amount_in_words" name="paid_amount_in_words" required
                    data-empty-message="Please Insert Paid Amount In Words" data-focus="paid_amount_in_words">{{ $waittingSales->paid_amount_in_words }}</textarea>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('payment_method', __('Payment Method')) }}
            <select class="form-control payment_method" name="payment_method" id="payment_method" required
                data-empty-message="Please select Payment Method" data-focus="payment_method">
                <option value="">Select Payment Method</option>
                <option @if ($waittingSales->payment_method == 'CASH') selected @endif value="CASH">Cash</option>
                <option @if ($waittingSales->payment_method == 'CHEQUE') selected @endif value="CHEQUE">Cheque</option>
                <option @if ($waittingSales->payment_method == 'CARD') selected @endif value="CARD">Card</option>
                <option @if ($waittingSales->payment_method == 'ONLINEPAYMENT') selected @endif value="ONLINEPAYMENT">Online Payment
                </option>
            </select>
        </div>

        <div class="form-group col-md-6 cheque_number_div">
            {{ Form::label('realised_date', __('Realised Date')) }}
            <div class="input-group">
                {{ Form::text('realised_date', $waittingSales->cheque_realised_date, ['class' => 'form-control datepicker', 'required' => 'required']) }}
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-12 cheque_number_div">
            {{ Form::label('cheque_number', __('Cheque Number')) }}
            {{ Form::text('cheque_number', $waittingSales->cheque_number, ['class' => 'form-control cheque_number']) }}
        </div>

        <div class="form-group col-md-6" id="card_type_div">
            {{ Form::label('card_type', __('Card Type')) }}
            <select class="form-control card_type" name="card_type" id="card_type">
                <option value="">Select Card Type</option>
                <option @if ($waittingSales->card_type == 1) selected @endif value="1">Credit Card</option>
                <option @if ($waittingSales->card_type == 2) selected @endif value="2">Debit Card</option>
            </select>
        </div>

        <div class="form-group col-md-6" id="card_charge_amount_div">
            {{ Form::label('card_charge_amount', __('Card Charges')) }}
            {{ Form::number('card_charge_amount', $waittingSales->card_charge_amount, ['class' => 'form-control card_charge_amount', 'step' => '0.01']) }}
        </div>

        <div class="form-group col-md-6" id="total_amount_div">
            {{ Form::label('total_amount', __('Total Amount')) }}
            {{ Form::number('total_amount', $waittingSales->total_amount, ['class' => 'form-control total_amount', 'step' => '0.01', 'readonly' => 'readonly']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('bank', __('Company Bank')) }}
            <div class="input-group">
                {{ Form::select('bank', $banks, $waittingSales->bank_id, ['class' => 'form-control customer-sel font-style', 'required' => 'required', 'data-empty-message' => 'Please Select a Company Bank', 'data-focus' => 'bank']) }}
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('traveling_charges', __('Transport Charges')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('traveling_charges', $waittingSales->traveling_charges, ['class' => 'form-control price', 'step' => '0.01', 'placeholder' => '0.00']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h4 class="sub-title mt-3">{{ __('Warranty') }}</h4>
    <div class="row">
        <input type="hidden" name="product_warranty_serial_id" id="product_warranty_serial_id"
            value="{{ $waittingSales->product_warranty_serial_id }}">
        <div class="form-group col-md-6">
            {{ Form::label('serial_number', __('Serial Card No')) }}
            <div class="input-group colorpickerinput">
                {{ Form::text('serial_number', $waittingSales->warranty->serial_number ?? null, ['class' => 'form-control', 'readonly' => 'readonly']) }}
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('warranty_number', __('Warranty Card No')) }}
            <div class="input-group colorpickerinput">
                {{ Form::text('warranty_number', $waittingSales->warranty->warranty_number ?? null, ['class' => 'form-control', 'readonly' => 'readonly']) }}
            </div>
        </div>
    </div>


    <div class="col-md-12 text-center" style="margin-top: 20px">
        <button type="submit" class="btn btn-primary" id="approve_btn">{{ __('Approve') }}</button>
        <button type="button" class="btn btn-danger" id="reject_btn">{{ __('Reject') }}</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal"
            id="cancel_btn">{{ __('Cancel') }}</button>
    </div>
</form>

<script>
    var sales = <?php echo json_encode($waittingSales); ?>;
    $(document).ready(function() {
        $('.selectpicker').selectpicker();

        $('#higher_purchase_div').show();
        $('#guarantor_div').show();
        $('#guarantor_div_2').show();
        $('#officer_div').show();
        $('#interest_rate').prop('disabled', false);
        $('#interest_amount').prop('disabled', false);
        $('#total_due_amount').prop('disabled', false);
        $('#monthly_installment').prop('disabled', false);
        $('#installment_date').prop('disabled', false);
        $('#due_date').prop('disabled', false);

        $('#payment_method').val(sales.payment_method).trigger('change');
        $('#paid_amount').val(sales.paid_amount);
        $('#paid_amount_in_words').html(sales.paid_amount_in_words);
        $('#document_charges_in_words').val(sales.paid_amount_in_words);

        var paymentMethod = sales.payment_method;
        changeMethod(paymentMethod);

        $('#card_type').val(sales.card_type).trigger('change');

        if (sales.advance_amount > 0) {
            $('#advanced_div').show();
        } else {
            $('#advanced_div').hide();
        }

        $("#sale_form :input").prop("disabled", true);
        $('#approve_btn').prop('disabled', false);
        $('#reject_btn').prop('disabled', false);
        $('#cancel_btn').prop('disabled', false);
        // $('#higher_purchase_div').hide();
        // $('#advanced_div').hide();
        // $('#guarantor_div').hide();
        // $('#guarantor_div_2').hide();
        // $('#officer_div').hide();

        // $('.cheque_number_div').hide();
        // $('#card_type_div').hide();
        // $('#card_charge_amount_div').hide();
        // $('#total_amount_div').hide();

        // $('#interest_rate').prop('disabled', true);
        // $('#interest_amount').prop('disabled', true);
        // $('#total_due_amount').prop('disabled', true);
        // $('#monthly_installment').prop('disabled', true);
        // $('#installment_date').prop('disabled', true);
        // $('#due_date').prop('disabled', true);
        // $('#advance_amount').prop('disabled', true);
    });

    // Barcode Scan
    $('.modal').on('shown.bs.modal', function() {
        $("#barcode_input").focus();
    });

    // function scanEvent(event){
    // 	if (event.keyCode == 13) {
    //         var barcode = $("#barcode_input").val();

    //         $.ajax({
    //             url:"{{ url('sale/product_id_by_barcode') }}/"+barcode,
    //             method: 'GET',
    //             dataType: 'json',
    //             success: function(data, textStatus, jqXHR){
    //                 $('#product').val(data[0].id).trigger('change');
    //             }
    //         });
    //     }
    // }

    // $('body').on('change', '#customer', function(){
    //     var custId = $(this).val();
    //     $.ajax({
    //             url:"{{ url('sale/get_cust_advance') }}/"+custId,
    //             method: 'GET',
    //             dataType: 'json',
    //             success: function(data, textStatus, jqXHR){
    //                 console.log(data);
    //                 if(data != null){
    //                     if(data.advance_amount != null){
    //                         $('#advance_amount').val(data.advance_amount);
    //                         $('#advance_id').val(data.id);
    //                         $('#advanced_div').show(200);
    //                     } else {
    //                         $('#advance_amount').val(0);
    //                         $('#advance_id').val('');
    //                         $('#advanced_div').hide(200);
    //                     }
    //                 }else {
    //                     $('#advance_amount').val(0);
    //                     $('#advance_id').val('');
    //                     $('#advanced_div').hide(200);
    //                 }
    //             }
    //         });
    // });

    // $('body').on('change', '#sale_type', function(){
    //     var saleType = $(this).val();
    //     if(saleType == 1){
    //         var interestId = 1;
    //         $.ajax({
    //             url:"{{ url('sale/get_interest_rate') }}/"+interestId,
    //             method: 'GET',
    //             dataType: 'json',
    //             success: function(data, textStatus, jqXHR){
    //                 $('#interest_rate').val(data.interest_rate);
    //                 $('#higher_purchase_div').show(200);

    //                 $('#paid_amount').val( parseFloat($('#down_payment').val())  - parseFloat($('#advance_amount').val()) );
    //                 paidAmountInWords( parseFloat($('#down_payment').val())  - parseFloat($('#advance_amount').val()) );

    //                 $('#guarantor_div').show(200);
    //                 $('#guarantor_div_2').show(200);
    //                 $('#officer_div').show(200);
    //             }
    //         });
    //     } else {
    //         $('#paid_amount').val( parseFloat($('#net_amount').val())  - parseFloat($('#advance_amount').val()));
    //         paidAmountInWords( parseFloat($('#net_amount').val())  - parseFloat($('#advance_amount').val()));

    //         $('#higher_purchase_div').hide(200);
    //         $('#guarantor_div').hide(200);
    //         $('#guarantor_div_2').hide(200);
    //         $('#officer_div').hide(200);

    //         $('#interest_rate').prop('disabled', true);
    //         $('#interest_amount').prop('disabled', true);
    //         $('#total_due_amount').prop('disabled', true);
    //         $('#monthly_installment').prop('disabled', true);
    //         $('#installment_date').prop('disabled', true);
    //         $('#due_date').prop('disabled', true);
    //         $('#installment').val('');
    //         $('#interest_rate').val('');
    //         $('#interest_amount').val('');
    //         $('#total_due_amount').val('');
    //         $('#monthly_installment').val('');
    //         $('#installment_date').val('');
    //         $('#due_date').val('');
    //     }
    // });

    // $('body').on('change', '#product', function(){
    //     var productId = $(this).val();
    //     $.ajax({
    //         url:"{{ url('sale/get_details') }}/"+productId,
    //         method:'GET',
    //         dataType:'json',
    //         success: function (data, textStatus, jqXHR){
    //             if(data != null){
    //                 $('#inventory_id').val(data[0].inventory_id);
    //                 $("#unit_price").val(data[0].sale_price);
    //                 $("#available_quantity").val(data[0].remain_qty);
    //                 $("#quantity").val(1);
    //                 $("#gross_amount").val(data[0].sale_price);
    //                 $('#discount').val(0);
    //                 $('#net_amount').val(data[0].sale_price);

    //                 var documentcharges = 0;
    //                 if(parseFloat(data[0].sale_price) <= 20000){
    //                     documentcharges = 200;
    //                 }else if(parseFloat(data[0].sale_price) > 20000){
    //                     documentcharges = 300;
    //                 }

    //                 var downPaymentRate = parseFloat(data[0].products.category_details.down_payment)/100;
    //                 var downPayment = parseFloat(data[0].sale_price) * downPaymentRate;

    //                 $('#down_payment').val(downPayment);
    //                 $('#down_payment_fixed').val(downPayment);
    //                 $('#down_payment_rate').val(downPaymentRate);
    //                 $('#document_charges').val(documentcharges);
    //                 documentChargeInWords(documentcharges);

    //                 if( $('#sale_type').val() == 0 ){
    //                     $('#paid_amount').val(data[0].sale_price  - parseFloat($('#advance_amount').val()) );
    //                     paidAmountInWords( data[0].sale_price  - parseFloat($('#advance_amount').val()) );
    //                 } else if ( $('#sale_type').val() == 1 ){
    //                     $('#paid_amount').val(downPayment - parseFloat($('#advance_amount').val()));
    //                     paidAmountInWords( downPayment - parseFloat($('#advance_amount').val()));
    //                 }
    //                 outstanding();

    //                 $('#installment').prop('disabled', false);
    //             }
    //         }
    //     });
    // });

    $('body').on('change', '#payment_method', function() {
        var method = $(this).val();
        if (method == 'CHEQUE') {
            $('#card_type').val('');
            $('#card_charge_amount').val('');
            $('#total_amount').val('');
            $('#card_type_div').hide(200);
            $('#card_charge_amount_div').hide(200);
            $('#total_amount_div').hide(200);
            $('.cheque_number_div').show(200);
        } else if (method == 'CARD') {
            $('#cheque_number').val('');
            $('.cheque_number_div').hide(200);
            $('#card_type_div').show(200);
            $('#card_charge_amount_div').show(200);
            $('#total_amount_div').show(200);
        } else {
            $('.cheque_number_div').hide(200);
            $('#card_type_div').hide(200);
            $('#card_charge_amount_div').hide(200);
            $('#total_amount_div').hide(200);
            $('#cheque_number').val('');
            $('#card_type').val('');
            $('#card_charge_amount').val('');
            $('#total_amount').val('');
        }
    });

    $('body').on('change', '#card_type', function() {
        var cardType = $(this).val();
        var paidAmount = $('#paid_amount').val();
        $.ajax({
            url: "{{ url('card_payment_rate/get_detail') }}/" + cardType,
            method: 'GET',
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                if (data != null) {
                    var cardCharge = parseFloat(paidAmount) * (parseFloat(data.rate) / 100);
                    var totalAmount = parseFloat(paidAmount) + cardCharge;
                    $('#card_charge_amount').val(cardCharge);
                    $('#total_amount').val(totalAmount);
                    paidAmountInWords(totalAmount);
                }
            }
        });
    });

    // $('body').on('input', '#card_charge_amount', function(){
    //     var cardCharges = $(this).val();
    //     var paidAmounts = $('#paid_amount').val();
    //     var totalAmounts = parseFloat(paidAmounts)+parseFloat(cardCharges);
    //     $('#total_amount').val(totalAmounts);
    //     paidAmountInWords(totalAmounts);
    // });

    // $('body').on('input', '#unit_price', function(){
    //     var unitPricetVal = $(this).val();
    //     var quantVal = $('#quantity').val();
    //     var discountVal = $('#discount').val();
    //     var downRate = $('#down_payment_rate').val();
    //     var grossVal = parseFloat(unitPricetVal)*parseFloat(quantVal);
    //     $('#gross_amount').val(grossVal);
    //     console.log(downRate);
    //     if(downRate != ''){
    //         var newDown = parseFloat(unitPricetVal)*parseFloat(downRate);
    //         $('#down_payment').val(newDown);
    //     }

    //     discount(discountVal);

    //     var installment = $('#installment').val();
    //     if(installment != ''){
    //         finalCalculation();
    //     }
    // });

    // $('body').on('input', '#discount', function(){
    //     var discountVal = $(this).val();
    //     discount(discountVal);
    // });

    // $('body').on('input', '#quantity', function(){
    //     var quantity = $(this).val();
    //     var unitPrice = $('#unit_price').val();
    //     var gross = parseFloat(unitPrice) * parseFloat(quantity);
    //     $('#gross_amount').val(gross);

    //     var discountVal = $('#discount').val();
    //     discount(discountVal);
    // });

    // $('body').on('change', '#installment', function(){
    //     if($(this).val() != ''){
    //         $('#interest_rate').prop('disabled', false);
    //         $('#interest_amount').prop('disabled', false);
    //         $('#total_due_amount').prop('disabled', false);
    //         $('#monthly_installment').prop('disabled', false);
    //         $('#installment_date').prop('disabled', false);
    //         $('#due_date').prop('disabled', false);

    //         if($('#interest_rate').val() != ''){
    //             finalCalculation();
    //         };

    //     } else {
    //         $('#interest_rate').prop('disabled', true);
    //         $('#interest_amount').prop('disabled', true);
    //         $('#total_due_amount').prop('disabled', true);
    //         $('#monthly_installment').prop('disabled', true);
    //         $('#installment_date').prop('disabled', true);
    //         $('#due_date').prop('disabled', true);
    //         $('#interest_amount').val('');
    //         $('#total_due_amount').val('');
    //         $('#monthly_installment').val('');
    //         $('#due_date').val('');
    //     }
    // });

    // $('body').on('input', '#interest_rate', function(){
    //     finalCalculation();
    // });

    // $('body').on('change', '#installment_date', function(){
    //     var day = $(this).val();
    //     var date = $('#due_date').val();
    //     var seperateddate = date.split('-');
    //     var newDate = ''+seperateddate[0]+'-'+seperateddate[1]+'-'+day+'';

    //     function convert(str) {
    //         var date = new Date(str),
    //             mnth = ("0" + (date.getMonth() + 1)).slice(-2),
    //             day = ("0" + date.getDate()).slice(-2);
    //         return [date.getFullYear(), mnth, day].join("-");
    //     }

    //     var convertedNewDate = convert(newDate);

    //     $('#due_date').val(convertedNewDate);
    // });

    // $('body').on('input', '#down_payment', function(){
    //     var downPaymentFixed = $('#down_payment_fixed').val();
    //     var downPayment = $(this).val();
    //     if( parseFloat(downPayment) < parseFloat(downPaymentFixed) ){
    //         $('#down_payment').addClass("text-danger");
    //     } else {
    //         $('#down_payment').removeClass("text-danger");
    //     }

    //     $('#paid_amount').val( parseFloat(downPayment) - parseFloat($('#advance_amount').val())  );
    //     paidAmountInWords( parseFloat(downPayment) - parseFloat($('#advance_amount').val())  );

    //     outstanding();

    //     var installment = $('#installment').val();
    //     if(installment != ''){
    //         finalCalculation();
    //     }
    // });

    // $('body').on('input', '#document_charges', function(){
    //     var documentcharges = $(this).val();
    //     documentChargeInWords(documentcharges);
    // });

    // $('body').on('input', '#paid_amount', function(){
    //     paidAmountInWords( $(this).val()  - parseFloat($('#advance_amount').val()) );
    // });

    function paidAmountInWords(amount) {
        $("#paid_amount_in_words").html('');
        var words = '';
        words = toWords(amount);
        $("#paid_amount_in_words").html(words + "Only");
    }

    function documentChargeInWords(amount) {
        // $("#document_charges_in_words").html('');
        var chargeWords = '';
        chargeWords = toWords(amount);
        $("#document_charges_in_words").val(chargeWords + "Only");
    }

    function changeMethod(method) {
        if (method == 'CHEQUE') {
            $('#card_type').val('');
            $('#card_charge_amount').val('');
            $('#total_amount').val('');
            $('#card_type_div').hide(200);
            $('#card_charge_amount_div').hide(200);
            $('#total_amount_div').hide(200);
            $('.cheque_number_div').show(200);
        } else if (method == 'CARD') {
            $('#cheque_number').val('');
            $('.cheque_number_div').hide(200);
            $('#card_type_div').show(200);
            $('#card_charge_amount_div').show(200);
            $('#total_amount_div').show(200);
        } else {
            $('.cheque_number_div').hide(200);
            $('#card_type_div').hide(200);
            $('#card_charge_amount_div').hide(200);
            $('#total_amount_div').hide(200);
            $('#cheque_number').val('');
            $('#card_type').val('');
            $('#card_charge_amount').val('');
            $('#total_amount').val('');
        }
    }
    // function discount(discountVal){
    //     var grossAmountVal = $('#gross_amount').val();
    //     var discount = 0;
    //     var grossAmount = 0;
    //     if(discountVal != ''){
    //         discount = discountVal;
    //     } else {
    //         discount = 0;
    //     }
    //     if(grossAmountVal != ''){
    //         grossAmount = grossAmountVal;
    //     } else {
    //         grossAmount = 0;
    //     }

    //     var netAmount = parseFloat(grossAmount) - parseFloat(discount);
    //     $('#net_amount').val(netAmount);


    //     if( $('#sale_type').val() == 0 ){
    //         $('#paid_amount').val(netAmount - parseFloat($('#advance_amount').val()) );
    //         paidAmountInWords(netAmount - parseFloat($('#advance_amount').val()) );
    //     }
    //     if( $('#sale_type').val() == 1 ){
    //         downPayment(netAmount);
    //         outstanding();
    //     }
    // }

    // function downPayment(netAmount){
    //     var downRate = $('#down_payment_rate').val();
    //     var downValu = parseFloat(netAmount)*parseFloat(downRate);
    //     $('#down_payment_fixed').val(downValu);
    //     $('#down_payment').val(downValu);
    //     $('#paid_amount').val(downValu - parseFloat($('#advance_amount').val()) );
    //     paidAmountInWords(downValu - parseFloat($('#advance_amount').val()) );
    // }

    // function outstanding(){
    //     var netAmountVal = $('#net_amount').val();
    //     var downPaymentVal = $('#down_payment').val();
    //     var netAmount = 0;
    //     var downPayment = 0;
    //     if(netAmountVal != ''){
    //         netAmount = netAmountVal;
    //     } else {
    //         netAmount = 0;
    //     }
    //     if(downPaymentVal != ''){
    //         downPayment = downPaymentVal;
    //     } else {
    //         downPayment = 0;
    //     }
    //     var outstanding =  parseFloat(netAmount) - parseFloat(downPayment);
    //     $('#outstanding_amount').val(outstanding);
    // }

    // function finalCalculation(){
    //     var interestRate = parseFloat($('#interest_rate').val())/100;
    //     var outStanding = $('#outstanding_amount').val();
    //     var timePeriod = $('#installment').val();
    //     var dateofSale = $('#date_of_sale').val();

    //     var interestAmount = parseFloat(outStanding) * parseFloat(timePeriod) * interestRate;
    //     $('#interest_amount').val(interestAmount.toFixed(2));

    //     var totDue = parseFloat(outStanding) + interestAmount;
    //     $('#total_due_amount').val(totDue.toFixed(2));

    //     var monthlyInstallment = totDue/parseFloat(timePeriod);
    //     $('#monthly_installment').val(monthlyInstallment.toFixed(2));

    //     var currentDate = $('#date_of_sale').val();
    //     var installmentDate = $('#installment_date').val();
    //     if(installmentDate != ''){
    //         var seperatedDate = currentDate.split('-');
    //         var newDate = ''+seperatedDate[0]+'-'+seperatedDate[1]+'-'+installmentDate+'';
    //     } else {
    //         var newDate = currentDate;
    //     }

    //     var dateforFunction = new Date(newDate);

    //     Date.prototype.addMonths = function (months) {
    //         var dt = this.getDate();

    //         this.setMonth(this.getMonth() + months);
    //         var currDt = this.getDate();

    //         if (dt !== currDt) {
    //             this.addDays(-currDt);
    //         }

    //         return this;
    //     };
    //     var duedate = dateforFunction.addMonths(parseFloat(timePeriod));

    //     function convert(str) {
    //         var date = new Date(str),
    //             mnth = ("0" + (date.getMonth() + 1)).slice(-2),
    //             day = ("0" + date.getDate()).slice(-2);
    //         return [date.getFullYear(), mnth, day].join("-");
    //     }

    //     var convertedDueDate = convert(duedate);

    //     $('#due_date').val(convertedDueDate);
    // }

    // $('#commonModal').on('shown.bs.modal', function() {
    //     $(document).off('focusin.modal');
    // });

    // $('#create_btn').click(function(){
    //     var saleType = $('#sale_type').val();
    //     var qtyRequired = $('#quantity').val();
    //     var qtyAvailable = $('#available_quantity').val();

    //     if(parseFloat(qtyRequired) > parseFloat(qtyAvailable) ){
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Oops...',
    //             text: 'You Have No Available Quantity!'
    //         });
    //     } else {
    //         if (saleType == 1){

    //             if($('#guarantor').val() == ''){
    //                 Swal.fire({
    //                         icon: 'error',
    //                         title: 'Oops...',
    //                         text: 'Please Select Guarantor!'
    //                     });
    //                     $(this).val('');
    //                 return this;
    //             }
    //             if($('#officer_id').val() == ''){
    //                 Swal.fire({
    //                         icon: 'error',
    //                         title: 'Oops...',
    //                         text: 'Please Select Officer!'
    //                     });
    //                     $(this).val('');
    //                 return this;
    //             }
    //             if($('#down_payment').val() == ''){
    //                 Swal.fire({
    //                         icon: 'error',
    //                         title: 'Oops...',
    //                         text: 'Please Enter Down Payment!'
    //                     });
    //                     $(this).val('');
    //                 return this;
    //             }
    //             if($('#outstanding_amount').val() == ''){
    //                 Swal.fire({
    //                         icon: 'error',
    //                         title: 'Oops...',
    //                         text: 'Please Enter Outstanding Amount!'
    //                     });
    //                     $(this).val('');
    //                 return this;
    //             }
    //             if($('#installment').val() == ''){
    //                 Swal.fire({
    //                         icon: 'error',
    //                         title: 'Oops...',
    //                         text: 'Please Select Installment Time Period!'
    //                     });
    //                     $(this).val('');
    //                 return this;
    //             }
    //             if($('#interest_rate').val() == ''){
    //                 Swal.fire({
    //                         icon: 'error',
    //                         title: 'Oops...',
    //                         text: 'Please Enter Interest Rate!'
    //                     });
    //                     $(this).val('');
    //                 return this;
    //             }
    //             if($('#interest_amount').val() == ''){
    //                 Swal.fire({
    //                         icon: 'error',
    //                         title: 'Oops...',
    //                         text: 'Please Enter Interest Amount!'
    //                     });
    //                     $(this).val('');
    //                 return this;
    //             }
    //             if($('#total_due_amount').val() == ''){
    //                 Swal.fire({
    //                         icon: 'error',
    //                         title: 'Oops...',
    //                         text: 'Please Enter Total Due Amount!'
    //                     });
    //                     $(this).val('');
    //                 return this;
    //             }
    //             if($('#monthly_installment').val() == ''){
    //                 Swal.fire({
    //                         icon: 'error',
    //                         title: 'Oops...',
    //                         text: 'Please Enter Monthly Installment!'
    //                     });
    //                     $(this).val('');
    //                 return this;
    //             }
    //             if($('#installment_date').val() == ''){
    //                 Swal.fire({
    //                         icon: 'error',
    //                         title: 'Oops...',
    //                         text: 'Please Select Installment Date!'
    //                     });
    //                     $(this).val('');
    //                 return this;
    //             }

    //             var downPaymentFixed = $('#down_payment_fixed').val();
    //             var downPayment = $('#down_payment').val();
    //             if( parseFloat(downPayment) < parseFloat(downPaymentFixed) ){

    //                 (async () => {
    //                     const { value: formValues } = await Swal.fire({
    //                         icon: 'warning',
    //                         title: 'Authenication Needed!',
    //                         html:
    //                             '<input type="text" placeholder="E mail" id="swal-input1" class="swal2-input">' +
    //                             '<input type="password" placeholder="Password" id="swal-input2" class="swal2-input">' +
    //                             '<input type="button" id="send" value="Send For Autherization" class="btn btn-success">',
    //                         focusConfirm: false,
    //                         showCancelButton: true,
    //                         confirmButtonText: 'Look up',
    //                         showLoaderOnConfirm: true,
    //                         preConfirm: () => {
    //                             return [
    //                             document.getElementById('swal-input1').value,
    //                             document.getElementById('swal-input2').value
    //                             ]
    //                         },
    //                         allowOutsideClick: () => !Swal.isLoading()
    //                     })

    //                     if (formValues) {
    //                         $.ajax({
    //                             url:"{{ url('sale/authenticate') }}",
    //                             method: 'POST',
    //                             data: { login_details: formValues, "_token": "{{ csrf_token() }}"},
    //                             dataType: 'json',
    //                             success: function(data, textStatus, jqXHR){
    //                                 if(data == 'success'){
    //                                     $('#sale_form').submit();
    //                                 } else if(data == 'email_error'){
    //                                     Swal.fire({
    //                                         icon: 'error',
    //                                         title: 'Oops...',
    //                                         text: 'The email address you entered is not correct. Please check your email address!'
    //                                     });
    //                                 } else if(data == 'user_error'){
    //                                     Swal.fire({
    //                                         icon: 'error',
    //                                         title: 'Oops...',
    //                                         text: 'You are not allowed to proceed this sale!'
    //                                     });
    //                                 } else if(data == 'password_error'){
    //                                     Swal.fire({
    //                                         icon: 'error',
    //                                         title: 'Oops...',
    //                                         text: 'The password you entered is not correct!'
    //                                     });
    //                                 }
    //                             }
    //                         });
    //                     }
    //                 })()

    //             } else {
    //                 $('#sale_form').submit();
    //             }
    //         }
    //         else if (saleType == 0){
    //             $('#sale_form').submit();
    //         }
    //     }

    // });

    $(document).on('click', '#reject_btn', function() {
        //Some code 1
        // console.log('rejected');
        $('#sale_form').attr('action', '{{ route('waitting_sale.reject') }}');
        $("#sale_form :input").prop("disabled", false);
        $('#sale_form').submit();
        // swal.clickConfirm();
    });
    $('body').submit(function() {
        $("#sale_form :input").prop("disabled", false);
        $(".se-pre-con").css({
            "display": "block"
        });
    });

    //form validation
    // function validateForm(form){
    //     var isValid = new Boolean();
    //     isValid = true;
    //     $(".se-pre-con").css({"display": "block"});

    //     var elems = $(form).find(':input');
    //     $.each(elems, function(){
    //         var elem = $(this);

    //         if(typeof $(elem).attr('required') != typeof undefined && $(elem).attr('required') != "false" && $(elem).val().trim() == ""){
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: 'Oops...',
    //                 text: $(elem).attr('data-empty-message'),
    //             })
    //             .then((value) => {
    //                 $("#"+$(elem).attr('data-focus')).focus();
    //             });
    //             isValid =  false;

    //             $(".se-pre-con").css({"display": "none"});
    //             return false;
    //         }
    //     });

    //     return isValid;
    // }
</script>
