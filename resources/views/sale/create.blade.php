@include('sale.batch-controls', ['batchCash' => false])
<style>
    #barcode_input_container label {
        margin-bottom: 0rem;
        line-height: 1.5rem;
    }

    #barcode_scan_input {
        border-radius: 3px;
        display: flex;
        border: 1px solid #3abaf4;
        background: #3abaf4;
    }

    #barcode_icon {
        padding: 11px 13px;
        color: #ffffff;
    }

    #barcode_input {
        /* border-radius: 0px; */
        overflow: hidden;
        border-radius: 0px 3px 3px 00px;
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

    .btn-light {
        box-shadow: none;
        background-color: #ffffff;
        border-color: #e4e6fc;
    }

    .customer-sel.form-control:not(.form-control-sm):not(.form-control-lg) {
        padding: 0px;
    }
</style>
<script>
        $(function () {
        $(document).on('click', '#billing_data', function () {
            $("[name='billing_title']").val($("[name='title']").val());
            $("[name='billing_name']").val($("[name='name']").val());
            $("[name='billing_house_no']").val($("[name='house_no']").val());
            $("[name='billing_address']").val($("[name='address']").val());
            $("[name='billing_phone']").val($("[name='phone_mobile']").val());
            $("[name='billing_email']").val($("[name='email']").val());
        })
    })
</script>
<div class="se-pre-con"></div>

<form method="post" action="{{ route('sale.create') }}" onsubmit="return validateForm(this)" id="sale_form"
    enctype="multipart/form-data">
    @csrf

    <input type="hidden" name="customer_type" value="1">
    <input type="hidden" name="modal_type" id="modal_type" value="1">

    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('date_of_sale', __('Date of Sale')) }} <span class="text-danger">*</span>
            <div class="input-group">
                {{ Form::text('date_of_sale', '', ['class' => 'form-control datepicker', 'required' => 'required', 'data-empty-message' => 'Please Select Date of Sale', 'data-focus' => 'date_of_sale']) }}
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('customer', __('New Customer')) }}
            <div>
                <a href="#" href="#" data-size="xl" data-url="{{ route('customer.create') }}"
                    data-ajax-popup="true" data-title="{{ __('Create New Customer') }}"
                    class="btn btn-primary btn-action mr-1 float-right" id="createCustomerBtn">
                    <span class="btn-inner--icon"><i class="fas fa-user-plus"></i></span>
                    <span class="btn-inner--text"> {{ __('Create New Customer') }}</span>
                </a>

            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('introducer_id', __('Salesman')) }}<span class="text-danger">*</span>
            <div class="input-group">
                <select class="form-control font-style" name="introducer_id" id="introducer_id"
                    data-empty-message="Please Select Salesman" data-focus="introducer_id" required>
                    <option value="">Select Salesman</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('customer', __('Regular Customer')) }} <span class="text-danger">*</span>
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" data-live-search="true"
                        name="customer" id="customer" required data-empty-message="Please Select Customer"
                        data-focus="customer">
                    <option value="">Select Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}"
                            data-recent="{{ isset($recentCustomer) && $recentCustomer->id == $customer->id ? 'true' : 'false' }}">
                            {{ $customer->nic_no . ' - ' . $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>



        <div class="form-group col-md-6" style="display: none;">
            {{ Form::label('sale_type', __('Sale Type')) }}<span class="text-danger">*</span>
            <div class="input-group">
                {{ Form::text('sale_type', 1, ['class' => 'form-control customer-sel font-style', 'required' => 'required', 'data-empty-message' => 'Please Select Sale Type', 'data-focus' => 'sale_type']) }}
            </div>
        </div>

        <div class="form-group col-md-6" id="hire_purchase_index_div">
            {{ Form::label('hire_purchase_index', __('Index')) }}
            <div class="input-group">
                {{ Form::text('hire_purchase_index', null, ['class' => 'form-control font-style', 'readonly' => 'readonly']) }}
            </div>
        </div>

        <div class="form-group col-md-4" id="guarantor_div">
            {{ Form::label('guarantor', __('1st Guarantor')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" data-live-search="true"
                    name="guarantor" id="guarantor" data-empty-message="Please Select Guarantor" data-focus="guarantor">
                    <option value="">Select 1st Guarantor</option>
                    @foreach ($guarantors as $guarantor)
                        <option value="{{ $guarantor->id }}">{{ $guarantor->nic_no . ' - ' . $guarantor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-4" id="guarantor_div_2">
            {{ Form::label('guarantor_2', __('2nd Guarantor')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" data-live-search="true"
                    name="guarantor_2" id="guarantor_2">
                    <option value="">Select 2nd Guarantor</option>
                    @foreach ($guarantors as $guarantor)
                        <option value="{{ $guarantor->id }}">{{ $guarantor->nic_no . ' - ' . $guarantor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-4" id="guarantor_div_3">
            {{ Form::label('guarantor_3', __('3rd Guarantor')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" data-live-search="true"
                    name="guarantor_3" id="guarantor_3">
                    <option value="">Select 3rd Guarantor</option>
                    @foreach ($guarantors as $guarantor)
                        <option value="{{ $guarantor->id }}">{{ $guarantor->nic_no . ' - ' . $guarantor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-6" id="guarantor_known_div_1">
            {{ Form::label('guarantor_1_known_duration', __('1st Guarantor Known Hirer Since (Years)')) }}
            {{ Form::text('guarantor_1_known_duration', null, ['class' => 'form-control', 'placeholder' => __('e.g. 5')]) }}
        </div>

        <div class="form-group col-md-6" id="guarantor_known_div_2">
            {{ Form::label('guarantor_2_known_duration', __('2nd Guarantor Known Hirer Since (Years)')) }}
            {{ Form::text('guarantor_2_known_duration', null, ['class' => 'form-control', 'placeholder' => __('e.g. 3')]) }}
        </div>

        <div class="col-12" id="witness_div">
            <div class="card border mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 font-weight-bold">{{ __('Witness Details') }}</h6>
                </div>
                <div class="card-body py-2">
                    <div class="row">
                        <div class="form-group col-md-4 mb-2">
                            {{ Form::label('witness_1_name', __('1st Witness Name')) }}
                            {{ Form::text('witness_1_name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) }}
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            {{ Form::label('witness_1_nic', __('1st Witness NIC')) }}
                            {{ Form::text('witness_1_nic', null, ['class' => 'form-control', 'placeholder' => __('NIC Number')]) }}
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            {{ Form::label('witness_1_address', __('1st Witness Address')) }}
                            {{ Form::text('witness_1_address', null, ['class' => 'form-control', 'placeholder' => __('Address')]) }}
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            {{ Form::label('witness_2_name', __('2nd Witness Name')) }}
                            {{ Form::text('witness_2_name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) }}
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            {{ Form::label('witness_2_nic', __('2nd Witness NIC')) }}
                            {{ Form::text('witness_2_nic', null, ['class' => 'form-control', 'placeholder' => __('NIC Number')]) }}
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            {{ Form::label('witness_2_address', __('2nd Witness Address')) }}
                            {{ Form::text('witness_2_address', null, ['class' => 'form-control', 'placeholder' => __('Address')]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-12">
            <div class="card repeater">
                <div class="card-header">
                    <h4 class="sub-title">{{ __('Products') }}</h4>

                    {{-- <div class="card-header-action">
                        <div id="barcode_input_container">
                            {{ Form::label('product', __('Enter Product Code')) }}
                            <div id="barcode_scan_input">
                                <i class="fas fa-barcode" id="barcode_icon"></i>
                                {{ Form::text('barcode', '', ['class' => 'form-control', 'id' => 'barcode_input', 'onkeypress' => 'scanEvent(event)']) }}
                            </div>
                        </div>
                    </div> --}}

                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <input type="hidden" name="inventory_id" id="inventory_id">
            <input type="hidden" name="purchase_price" id="purchase_price">
            {{ Form::label('product', __('Product')) }}<span class="text-danger">*</span>
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" name="product" id="product" required
                    data-empty-message="Please Select Product" data-focus="product" data-live-search="true">
                    <option value="">Select Product (Search by Category, Name or Code)</option>
                    @foreach ($products as $product)
                        @if(!empty($product->products))
                            @php
                                $catName = $product->products->productCategory->name ?? ($product->products->categoryDetails->name ?? '');
                                $subCatName = $product->products->productSubCategory->name ?? ($product->products->subCategoryDetails->name ?? '');
                                $prodName = $product->products->name ?? '';
                                $barcode = $product->products->barcode ?? '';
                                $catParts = array_filter([$catName, $subCatName]);
                                $catPrefix = !empty($catParts) ? implode(' / ', $catParts) . ' — ' : '';
                                $isOutOfStock = ($product->remain_qty ?? 0) <= 0;
                                $subtext = $barcode . ($isOutOfStock ? ($barcode ? ' — ' : '') . 'Out of Stock' : '');
                            @endphp
                            <option value="{{ $product->products->id }}"
                                data-tokens="{{ $catName }} {{ $subCatName }} {{ $prodName }} {{ $barcode }} {{ $isOutOfStock ? 'Out of Stock' : '' }}"
                                data-subtext="{{ $subtext }}">
                                {{ $catPrefix }}{{ $prodName }}{{ $isOutOfStock ? ' (Out of Stock)' : '' }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('product_description', __('Product Description')) }}
            <div class="input-group colorpickerinput">
                {{ Form::text('barcode_description', '', ['class' => 'form-control barcode_description', 'id' => 'barcode_description', 'readonly' => 'readonly' ]) }}
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('unit_price', __('Purchasing Price')) }} <span class="text-danger">*</span>
            <div class="input-group colorpickerinput">
                <input type="hidden" name="original_unit_price" id="original_unit_price" />
                {{ Form::number('unit_price', null, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Purchasing Price', 'data-focus' => 'unit_price']) }}
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
                {{ Form::number('available_quantity', null, ['class' => 'form-control available_quantity', 'id' => 'available_quantity', 'readonly' => 'readonly']) }}
            </div>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('quantity', __('Quantity')) }}<span class="text-danger">*</span>
            <div class="input-group colorpickerinput">
                {{ Form::number('quantity', null, ['class' => 'form-control quantity', 'required' => 'required', 'data-empty-message' => 'Please Enter Quantity', 'data-focus' => 'quantity']) }}
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('gross_hire_price', __('Gross Hire Price')) }} <span class="text-danger">*</span>
            <div class="input-group colorpickerinput">
                <input type="hidden" name="original_gross_hire_price" id="original_gross_hire_price" />
                {{ Form::number('gross_hire_price', null, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Gross Hire Price', 'data-focus' => 'gross_hire_price']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="form-group col-md-6">
            {{ Form::label('gross_amount', __('Gross Amount')) }}<span class="text-danger">*</span>
        <div class="input-group colorpickerinput">
            {{ Form::number('gross_amount',null, array('class' => 'form-control price','required'=>'required','step'=>'0.01', 'data-empty-message'=>"Please Enter Gross Amount", 'data-focus'=>"gross_amount", 'readonly'=>"readonly")) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div> --}}

        {{-- <div class="form-group col-md-6">
            {{ Form::label('discount', __('Discount')) }}<span class="text-danger">*</span>
    <div class="input-group colorpickerinput">
        {{ Form::number('discount',null, array('class' => 'form-control discount','required'=>'required','step'=>'0.01', 'data-empty-message'=>"Please Entert Discount", 'data-focus'=>"discount")) }}
        <div class="input-group-append">
            <div class="input-group-text">
                {{\Auth::user()->currencySymbol()}}
            </div>
        </div>
    </div>
    </div> --}}
        <input type="hidden" id="discount" name="discount">
        <input type="hidden" id="gross_amount" name="gross_amount">

        <div class="form-group col-md-6">
            {{ Form::label('net_amount', __('Net Amount')) }}<span class="text-danger">*</span>
            <div class="input-group colorpickerinput">
                {{ Form::number('net_amount', null, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Net Amount', 'data-focus' => 'net_amount', 'readonly' => 'readonly']) }}
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
            {{ Form::label('installment', __('Installment')) }} <span class="text-danger">*</span>
            <div class="input-group colorpickerinput">
                <select class="form-control installment" name="installment" id="installment">
                    <option value="">Select Time Period</option>
                    @foreach ($timePeriods as $timePeriod)
                        <option value="{{ $timePeriod->time_period }}">{{ $timePeriod->time_period }}</option>
                    @endforeach
                </select>
                <div class="input-group-text">
                    Months
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('outstanding_amount', __('Outstanding Amount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('outstanding_amount', null, ['class' => 'form-control price', 'step' => '0.01', 'readonly' => 'readonly']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('down_payment', __('Down Payment')) }} <span class="text-danger">Please select an installment period before entering the Down Payment.</span>
            <div class="input-group colorpickerinput">
                {{ Form::number('down_payment', null, ['class' => 'form-control price', 'step' => '0.01', 'min' => '0', 'id' => 'down_payment', 'disabled' => true]) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>



        <div class="form-group col-md-6">
            <input type="hidden" id="interest_rate_fixed" name="interest_rate_fixed">

            {{ Form::label('interest_rate', __('Interest Rate')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('interest_rate', null, ['class' => 'form-control interest_rate', 'step' => '0.01', 'id' => 'interest_rate', 'readonly' => 'readonly']) }}
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
                {{ Form::number('interest_amount', null, ['class' => 'form-control price', 'step' => '0.01', 'readonly' => 'readonly']) }}
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
                {{ Form::number('total_due_amount', null, ['class' => 'form-control price', 'step' => '0.01', 'readonly' => 'readonly']) }}
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
                {{ Form::number('monthly_installment', null, ['class' => 'form-control price', 'step' => '0.01', 'readonly' => 'readonly']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('installment_date', __('Installment Date')) }}<span class="text-danger">*</span>
            <div class="input-group colorpickerinput">
                <select class="form-control" name="installment_date" id="installment_date">
                    <option value="">Select Installment Date</option>
                    @for ($i = 1; $i < 31; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('due_date', __('Due Date')) }}
            <div class="input-group colorpickerinput">
                {{ Form::text('due_date', '', ['class' => 'form-control', 'readonly' => 'readonly']) }}
            </div>
        </div>

        <div class="form-group col-md-6">
            <input type="hidden" name="document_charges_in_words" id="document_charges_in_words">
            {{ Form::label('document_charges', __('Document Charges')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('document_charges', null, ['class' => 'form-control price', 'step' => '0.01']) }}
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
            {{ Form::label('advance_amount', __('Advance Amount')) }}<span class="text-danger">*</span>
            <div class="input-group colorpickerinput">
                {{ Form::number('advance_amount', 0, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Insert Advance Amount', 'data-focus' => 'advance_amount']) }}
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
            {{ Form::label('paid_amount', __('Paid Amount')) }}<span class="text-danger">*</span>
            <div class="input-group colorpickerinput">
                {{ Form::number('paid_amount', null, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Insert Paid Amount', 'data-focus' => 'paid_amount']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('paid_amount_in_words', __('Paid Amount In Words')) }}<span class="text-danger">*</span>
            <div class="input-group colorpickerinput">
                <textarea class="form-control paid_amount_in_words" id="paid_amount_in_words" name="paid_amount_in_words" required
                    data-empty-message="Please Insert Paid Amount In Words" data-focus="paid_amount_in_words"></textarea>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('payment_method', __('Payment Method')) }}<span class="text-danger">*</span>
            <select class="form-control payment_method" name="payment_method" id="payment_method" required
                data-empty-message="Please select Payment Method" data-focus="payment_method">
                <option value="">Select Payment Method</option>
                <option value="CASH">Cash</option>
                <option value="CHEQUE">Cheque</option>
                <option value="CARD">Card</option>
                <option value="ONLINEPAYMENT">Online Payment</option>
            </select>
        </div>

        <div class="form-group col-md-6 cheque_number_div">
            {{ Form::label('realised_date', __('Realised Date')) }}<span class="text-danger">*</span>
            <div class="input-group">
                {{ Form::text('realised_date', '', ['class' => 'form-control datepicker', 'required' => 'required']) }}
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-12 cheque_number_div">
            {{ Form::label('cheque_number', __('Cheque Number')) }}
            {{ Form::text('cheque_number', null, ['class' => 'form-control cheque_number']) }}
        </div>

        <div class="form-group col-md-6" id="card_type_div">
            {{ Form::label('card_type', __('Card Type')) }}
            <select class="form-control card_type" name="card_type" id="card_type">
                <option value="">Select Card Type</option>
                <option value="1">Credit Card</option>
                <option value="2">Debit Card</option>
            </select>
        </div>

        <div class="form-group col-md-6" id="card_charge_amount_div">
            {{ Form::label('card_charge_amount', __('Card Charges')) }}
            {{ Form::number('card_charge_amount', null, ['class' => 'form-control card_charge_amount', 'step' => '0.01']) }}
        </div>

        <div class="form-group col-md-6" id="total_amount_div">
            {{ Form::label('total_amount', __('Total Amount')) }}
            {{ Form::number('total_amount', null, ['class' => 'form-control total_amount', 'step' => '0.01', 'readonly' => 'readonly']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('bank', __('Company Bank')) }}<span class="text-danger">*</span>
            <div class="input-group">
                {{ Form::select('bank', $banks, null, ['class' => 'form-control font-style', 'required' => 'required', 'data-empty-message' => 'Please Select a Company Bank', 'data-focus' => 'bank']) }}
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('traveling_charges', __('Transport Charges')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('traveling_charges', null, ['class' => 'form-control price', 'step' => '0.01', 'placeholder' => '0.00']) }}
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
        <div class="form-group col-md-6">
            {{ Form::label('guarantor_2', __('Serial Card No')) }}
            <input type="hidden" name="selected_serial_number" id="selected_serial_number">

            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" data-live-search="true"
                    name="serial_card_no" id="serial_select">
                    <option value="">Select Serial Card No</option>
                    {{-- @foreach ($productWarranties as $productWarranty)
                        <option value="{{ $productWarranty->id }}">{{ $productWarranty->warranty_card_no }}</option>
                    @endforeach --}}
                </select>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('guarantor_2', __('Warranty Card No')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" data-live-search="true"
                    name="warranty_card_no" id="warranty_select">
                    <option value="">Select Warranty Card No</option>
                    {{-- @foreach ($productWarranties as $productWarranty)
                        <option value="{{ $productWarranty->id }}">{{ $productWarranty->warranty_card_no }}</option>
                    @endforeach --}}
                </select>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('warranty_period', __('Warranty Period')) }}
                <div class="input-group colorpickerinput">
                    {{ Form::number('warranty_period', null, ['class' => 'form-control price', 'step' => '1']) }}
                    <div class="input-group-append">
                        <div class="input-group-text">
                            {{ 'Months' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6 d-none">
            {{ Form::label('warranty_charges', __('Warranty Charges')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('warranty_charges', null, ['class' => 'form-control price', 'step' => '0.01', 'placeholder' => '0.00', 'min' => '0']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label class="form-lable">Warranty Document</label>
            {{-- <a href="#" class="text-info text-small ml-1" id="warranty_document_btn" download style="display: none;"><i class="fas fa-download"></i> Download</a> --}}
            <input type="file" class="form-control form-input" id="warranty_document" name="warranty_document"
                style="overflow: hidden;" />
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('additional_warranty_description', __('Additional Warranty Description')) }}
            {!! Form::textarea('additional_warranty_description', null, ['class' => 'form-control', 'rows' => '5']) !!}
        </div>
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="button" class="btn btn-primary" id="create_btn">{{ __('Create') }}</button>
    </div>
</form>

<script>

$(document).ready(function() {
    // Function to format the input to two decimal places
    function addCents(input) {
        let value = parseFloat(input.val()).toFixed(2); // Convert to two decimal places
        input.val(value); // Set formatted value
    }

    // Trigger the formatting when the user finishes entering the value
    $('#down_payment, #gross_hire_price').on('change', function() {
        addCents($(this));
    });
});

    $(document).ready(function () {
    // Monitor changes in the installment field
    $('#installment').on('change', function () {
        // Enable the down_payment field if installment is selected
        if ($(this).val()) {
            $('#down_payment').prop('disabled', false);
        } else {
            $('#down_payment').prop('disabled', true);
            $('#down_payment').val(''); // Clear down_payment if installment is deselected
        }
    });

    // Check if down_payment is editable when clicked
    $('#down_payment').on('focus', function () {
        if ($('#installment').val() === '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please Select Installment Time Period!'
            });
            $(this).blur(); // Remove focus from down_payment
        }
    });
});


document.getElementById('createCustomerBtn').addEventListener('click', function() {
    // Set a flag in localStorage when the "Create Customer" button is clicked
    localStorage.setItem("newCustomerButtonClicked", "true");
});

function applyRecentCustomer() {
    if (localStorage.getItem('newCustomerButtonClicked')) {
        let recentCustomerId = $('#customer option[data-recent="true"]').val();

        if (recentCustomerId) {
            $('#customer').val(recentCustomerId); // Select the recent customer
            if ($.fn.selectpicker) {
                $('.selectpicker').selectpicker('refresh'); // Refresh if using Bootstrap Select
            }
            $('#customer').trigger('change');
        }

        // Clear the flag to prevent re-selection on future loads
        localStorage.removeItem('newCustomerButtonClicked');
    }
}

$(document).ready(function() {
    applyRecentCustomer();
});






    $(document).ready(function() {
        $('.selectpicker').selectpicker();

        $('#hire_purchase_index_div').hide();

        $('#higher_purchase_div').hide();
        $('#advanced_div').hide();
        $('#guarantor_div').hide();
        $('#guarantor_div_2').hide();
        $('#guarantor_div_3').hide();
        $('#guarantor_known_div_1').hide();
        $('#guarantor_known_div_2').hide();
        $('#officer_div').hide();

        $('.cheque_number_div').hide();
        $('#card_type_div').hide();
        $('#card_charge_amount_div').hide();
        $('#total_amount_div').hide();

        $('#interest_rate').prop('disabled', true);
        $('#interest_amount').prop('disabled', true);
        $('#total_due_amount').prop('disabled', true);
        $('#monthly_installment').prop('disabled', true);
        $('#installment_date').prop('disabled', true);
        $('#due_date').prop('disabled', true);
        // $('#advance_amount').prop('disabled', true);
    });

    // Barcode Scan
    $('.modal').on('shown.bs.modal', function() {
        $("#barcode_input").focus();
    });

    function scanEvent(event) {
        if (event.keyCode == 13) {
            var barcode = $("#barcode_input").val();

            $.ajax({
                url: "{{ url('sale/product_id_by_barcode') }}/" + barcode,
                method: 'GET',
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    $('#product').val(data[0].id).trigger('change');
                }
            });
        }
    }

    $('body').on('change', '#customer', function() {
        var custId = $(this).val();
        if (custId != '') {
            $.ajax({
                url: "{{ url('sale/get_cust_advance') }}/" + custId,
                method: 'GET',
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    if (data != null) {
                        if (data.advance_amount != null) {
                            $('#advance_amount').val(data.advance_amount);
                            $('#advance_id').val(JSON.stringify(data.id));
                            $('#advanced_div').show(200);
                        } else {
                            $('#advance_amount').val(0);
                            $('#advance_id').val('');
                            $('#advanced_div').hide(200);
                        }
                    } else {
                        $('#advance_amount').val(0);
                        $('#advance_id').val('');
                        $('#advanced_div').hide(200);
                    }
                }
            });
        }

        if (custId != '') {
            hirePurchaseIndex(custId);
        } else {
            $('#hire_purchase_index_div').hide(200);
            $('#hire_purchase_index').val('');
        }
    });

    $('body').on('show.bs.modal', '#commonModal', function() {
        var customer = $('#customer').val();
        var interestId = 1;

        $.ajax({
            url: "{{ url('sale/get_interest_rate') }}/" + interestId,
            method: 'GET',
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                $('#interest_rate').val(data.interest_rate);
                $('#interest_rate_fixed').val(data.interest_rate);
                $('#higher_purchase_div').show(200);

                $('#paid_amount').val(parseFloat($('#down_payment').val()) - parseFloat($(
                    '#advance_amount').val()));
                paidAmountInWords(parseFloat($('#down_payment').val()) - parseFloat($(
                    '#advance_amount').val()));

                $('#guarantor_div').show(200);
                $('#guarantor_div_2').show(200);
                $('#guarantor_div_3').show(200);
                $('#guarantor_known_div_1').show(200);
                $('#guarantor_known_div_2').show(200);
            }
        });

        if (customer != '') {
            hirePurchaseIndex(customer);
        }
    });

    $('body').on('change', '#product', function() {
        SaleWarranty.bind($(this).closest('form'), [], { preservePrices: true });
        var productId = $(this).val();
        if (!productId) {
            $("#available_quantity, .available_quantity").val('');
            $("#barcode_description").val('');
            $("#inventory_id").val('');
            $("#purchase_price").val('');
            $("#unit_price").val('');
            $("#original_unit_price").val('');
            $("#gross_hire_price").val('');
            $("#original_gross_hire_price").val('');
            return;
        }
        $.ajax({
            url: "{{ url('sale/get_details') }}/" + productId,
            method: 'GET',
            cache: false,
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                // console.log(data);
                if (data.length > 0) {
                    $('#inventory_id').val(data[0].inventory_id);
                    $('#purchase_price').val(parseFloat(data[0].purchase_price).toFixed(2));
                    $("#unit_price").val(parseFloat(data[0].sale_price).toFixed(2));
                    $("#original_unit_price").val(parseFloat(data[0].sale_price).toFixed(2));
                    $("#gross_hire_price").val(parseFloat(data[0].hire_price).toFixed(2));
                    $("#original_gross_hire_price").val(parseFloat(data[0].hire_price).toFixed(2));
                    $("#barcode_description").val(data[0].products.name);
                    $("#available_quantity, .available_quantity").val(data[0].remain_qty);
                    $("#quantity").val(1).attr('max', data[0].remain_qty);
                    $("#gross_amount").val(parseFloat(data[0].sale_price).toFixed(2));
                    $('#discount').val(0);
                    $('#net_amount').val(parseFloat(data[0].sale_price).toFixed(2));

                    var documentcharges = 0;
                    if (parseFloat(data[0].sale_price) <= 20000) {
                        documentcharges = 200;
                    } else if (parseFloat(data[0].sale_price) > 20000) {
                        documentcharges = 300;
                    }

                    var downPaymentRate = parseFloat(data[0].products.category_details
                        .down_payment) / 100;
                    var downPayment = parseFloat(data[0].sale_price) * downPaymentRate;

                    $('#down_payment').val(parseFloat(downPayment).toFixed(2));
                    $('#down_payment_fixed').val(downPayment);
                    $('#down_payment_rate').val(downPaymentRate);
                    $('#document_charges').val(parseFloat(documentcharges).toFixed(2));
                    documentChargeInWords(documentcharges);

                    var adv = parseFloat($('#advance_amount').val() || 0);
                    var paidAmount = Math.max(0, downPayment - adv);
                    $('#paid_amount').val(parseFloat(paidAmount).toFixed(2));
                    paidAmountInWords(paidAmount);

                    outstanding();

                    $('#warranty_period').val(data[0].products.warranty_period);
                    $('#additional_warranty_description').html(data[0].products .additional_warranty_description);

                    if (!data[0].products.warranty_document || data[0].products.warranty_document == 'assets/img/products/default.png') {
                        $('#warranty_document_btn').hide();
                    } else {
                        $('#warranty_document_btn').attr('href', data[0].products
                            .warranty_document);
                        $('#warranty_document_btn').show();
                    }

                    $('#installment').prop('disabled', false);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Out of stock',
                        text: 'The selected product is out of stock',
                    });
                }

                $.ajax({
                    url: "{{ url('sale/get_warranty_serials') }}/" + productId,
                    method: 'GET',
                    dataType: 'json',
                    success: function(warrantyData) {
                        // Clear existing options in both selects
                        $('#warranty_select').empty().append('<option value="">Select Warranty Card No</option>');
                        $('#serial_select').empty().append('<option value="">Select Serial Card No</option>');

                        let serialToWarrantyMap = {};

                        if (warrantyData.length > 0) {
                            warrantyData.forEach(function(item) {
                                $('#serial_select').append(
                                    $('<option></option>').val(item.serial_number).text(item.serial_number)
                                );
                                $('#warranty_select').append(
                                    $('<option></option>').val(item.warranty_number).text(item.warranty_number)
                                );

                                // Map serial number to warranty number
                                serialToWarrantyMap[item.serial_number] = item.warranty_number;
                            });

                            $('.selectpicker').selectpicker('refresh');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'No Warranty Data',
                                text: 'No warranty data available for the selected product.',
                            });
                        }

                        // Listen for changes in the serial number select
                        $('#serial_select').off('change.saleSerial').on('change.saleSerial', function() {
                            const selectedSerial = $(this).val();
                            $('#selected_serial_number').val(selectedSerial);

                            if (selectedSerial) {
                                const correspondingWarranty = serialToWarrantyMap[selectedSerial];
                                $('#warranty_select').val(correspondingWarranty).trigger('change');
                                $('.selectpicker').selectpicker('refresh');
                            }
                        });
                        SaleWarranty.bind($('#serial_select').closest('form'), warrantyData, { preservePrices: true });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Error fetching warranty serials:", textStatus, errorThrown);
                    }
                });

            }
        });
    });

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

    $('body').on('input', '#card_charge_amount', function() {
        var cardCharges = $(this).val();
        var paidAmounts = $('#paid_amount').val();
        var totalAmounts = parseFloat(paidAmounts) + parseFloat(cardCharges);
        $('#total_amount').val(totalAmounts);
        paidAmountInWords(totalAmounts);
    });

    $('body').on('input', '#unit_price', function() {
        // var unitPricetVal = $(this).val();
        var quantVal = $('#quantity').val();
        var downRate = $('#down_payment_rate').val();
        var unitPricetVal = rtnUnitPriceWithWarrntyInsu();

        var grossVal = parseFloat(unitPricetVal) * parseFloat(quantVal);
        $('#gross_amount').val(parseFloat(grossVal).toFixed(2));
        $('#net_amount').val(parseFloat(grossVal).toFixed(2));

        if (downRate != '') {
            var newDown = parseFloat(unitPricetVal) * parseFloat(downRate);
            $('#down_payment').val(parseFloat(newDown).toFixed(2));
            var outStanding = parseFloat(grossVal) - parseFloat(newDown);
            $('#outstanding_amount').val(parseFloat(outStanding).toFixed(2));
        }


        finalCalculation();
        // var installment = $('#installment').val();
        // if (installment != '') {}
    });

    $('body').on('input', '#gross_hire_price', function() {
        finalCalculation();
    });

    $('body').on('input', '#discount', function() {
        var discountVal = $(this).val();
        discount(discountVal);
    });

    $('body').on('input', '#quantity', function() {
        var quantity = $(this).val();
        var serialVal = $('#serial_select').val() || $('.selected-warranty-unit-serial').val() || $('#selected_serial_number').val();
        if (serialVal && parseFloat(quantity) > 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Serialized Item',
                text: 'Items with a Serial Card No can only have a quantity of 1.'
            });
            quantity = 1;
            $(this).val(1);
        }
        var downRate = $('#down_payment_rate').val();
        var hirePrice = $('#original_gross_hire_price').val();
        var unitPrice = rtnUnitPriceWithWarrntyInsu();

        var newHirePrice = parseFloat(hirePrice) * parseFloat(quantity || 1);
        $('#gross_hire_price').val(parseFloat(newHirePrice).toFixed(2));
        // $('#original_gross_hire_price').val(parseFloat(newHirePrice).toFixed(2));

        var grossVal = parseFloat(unitPrice) * parseFloat(quantity);
        $('#gross_amount').val(parseFloat(grossVal).toFixed(2));
        $('#net_amount').val(parseFloat(grossVal).toFixed(2));


        if (downRate != '') {
            var newDown = parseFloat(unitPrice) * parseFloat(downRate) * parseFloat(quantity);
            $('#down_payment').val(parseFloat(newDown).toFixed(2));
            var outStanding = parseFloat(grossVal) - parseFloat(newDown);
            $('#outstanding_amount').val(parseFloat(outStanding).toFixed(2));
        }
        finalCalculation();
        // // var unitPrice = $('#unit_price').val();
        // var unitPrice = rtnUnitPriceWithWarrntyInsu();
        // var gross = parseFloat(unitPrice) * parseFloat(quantity);
        // $('#gross_amount').val(parseFloat(gross).toFixed(2));

        // var discountVal = $('#discount').val();
        // discount(discountVal);
    });

    $('body').on('change', '#installment', function() {
        if ($(this).val() != '') {
            $('#interest_rate').prop('disabled', false);
            $('#interest_amount').prop('disabled', false);
            $('#total_due_amount').prop('disabled', false);
            $('#monthly_installment').prop('disabled', false);
            $('#installment_date').prop('disabled', false);
            $('#due_date').prop('disabled', false);

            var installment = $(this).val();
            var quantity = $('#quantity').val();
            var downRate = $('#down_payment_rate').val();
            var unitPrice = rtnUnitPriceWithWarrntyInsu();

            var grossVal = parseFloat(unitPrice) * parseFloat(quantity);
            $('#gross_amount').val(parseFloat(grossVal).toFixed(2));
            $('#net_amount').val(parseFloat(grossVal).toFixed(2));

            if (downRate != '') {
                var newDown = parseFloat(unitPrice) * parseFloat(downRate);
                $('#down_payment').val(parseFloat(newDown).toFixed(2));
                var outStanding = parseFloat(grossVal) - parseFloat(newDown);
                $('#outstanding_amount').val(parseFloat(outStanding).toFixed(2));
            }
            finalCalculation();



        } else {
            $('#interest_rate').prop('disabled', true);
            $('#interest_amount').prop('disabled', true);
            $('#total_due_amount').prop('disabled', true);
            $('#monthly_installment').prop('disabled', true);
            $('#installment_date').prop('disabled', true);
            $('#due_date').prop('disabled', true);
            $('#interest_amount').val('');
            $('#total_due_amount').val('');
            $('#monthly_installment').val('');
            $('#due_date').val('');
        }
    });

    $('body').on('input', '#interest_rate', function() {
        finalCalculation();
    });

    $('body').on('change', '#installment_date', function() {
        var day = $(this).val();
        var date = $('#due_date').val();
        var seperateddate = date.split('-');
        var newDate = '' + seperateddate[0] + '-' + seperateddate[1] + '-' + day + '';

        function convert(str) {
            var date = new Date(str),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);
            return [date.getFullYear(), mnth, day].join("-");
        }

        var convertedNewDate = convert(newDate);

        $('#due_date').val(convertedNewDate);
    });

    $('body').on('input', '#down_payment', function() {
        var downPaymentFixed = $('#down_payment_fixed').val();
        var downPayment = $(this).val();
        if (parseFloat(downPayment) < parseFloat(downPaymentFixed)) {
            $('#down_payment').addClass("text-danger");
        } else {
            $('#down_payment').removeClass("text-danger");
        }

        var adv = parseFloat($('#advance_amount').val() || 0);
        var paidAmount = Math.max(0, parseFloat(downPayment || 0) - adv);
        $('#paid_amount').val(parseFloat(paidAmount).toFixed(2));
        paidAmountInWords(paidAmount);

        var quantity = $('#quantity').val();
        var unitPrice = rtnUnitPriceWithWarrntyInsu();

        var grossVal = parseFloat(unitPrice) * parseFloat(quantity);
        $('#gross_amount').val(parseFloat(grossVal).toFixed(2));
        $('#net_amount').val(parseFloat(grossVal).toFixed(2));

        var outStanding = parseFloat(grossVal) - parseFloat(downPayment);
        $('#outstanding_amount').val(parseFloat(outStanding).toFixed(2));

        finalCalculation();
        // outstanding();

        // var installment = $('#installment').val();
        // if (installment != '') {
        //     finalCalculation();
        // }
    });

    $('body').on('input', '#document_charges', function() {
        var documentcharges = $(this).val();
        documentChargeInWords(documentcharges);
    });

    $('body').on('input', '#paid_amount', function() {
        var adv = parseFloat($('#advance_amount').val() || 0);
        paidAmountInWords(Math.max(0, parseFloat($(this).val() || 0) - adv));
    });

    function hirePurchaseIndex(customer) {
        var date = $('#date_of_sale').val();
        $.ajax({
            url: "{{ url('sale/get_hire_purchase_index') }}/" + customer + "/" + date,
            method: 'GET',
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                // console.log(data);
                if (data != null) {
                    $('#hire_purchase_index').val(data + 1);
                    $('#hire_purchase_index_div').show(200);
                }
            }
        });
    }

    function paidAmountInWords(amount) {
        $("#paid_amount_in_words").html('');
        var roundedAmount = Math.round(amount * 100) / 100;
        var parts = roundedAmount.toString().split(".");
        var words = toWords(parts[0]);
        if (parts[1] && parseInt(parts[1]) > 0) {
            words += ' and ' + toWords(parts[1]) + ' cents';
        }
        $("#paid_amount_in_words").html(words + " Only");
    }

    function documentChargeInWords(amount) {
        // $("#document_charges_in_words").html('');
        var chargeWords = '';
        chargeWords = toWords(amount);
        $("#document_charges_in_words").val(chargeWords + "Only");
    }

    function discount(discountVal) {
        var grossAmountVal = $('#gross_amount').val();
        var discount = 0;
        var grossAmount = 0;
        if (discountVal != '') {
            discount = discountVal;
        } else {
            discount = 0;
        }
        if (grossAmountVal != '') {
            grossAmount = grossAmountVal;
        } else {
            grossAmount = 0;
        }

        var netAmount = Math.round(parseFloat(grossAmount) - parseFloat(discount));
        $('#net_amount').val(netAmount.toFixed(2));

        downPayment(netAmount);
        outstanding();
    }

    function downPayment(netAmount) {
        var downRate = $('#down_payment_rate').val();
        var downValu = Math.round(parseFloat(netAmount) * parseFloat(downRate));
        var adv = parseFloat($('#advance_amount').val() || 0);
        var paidAmount = Math.max(0, Math.round(downValu - adv));

        $('#down_payment_fixed').val(downValu.toFixed(2));
        $('#down_payment').val(downValu.toFixed(2));
        $('#paid_amount').val(paidAmount.toFixed(2));
        paidAmountInWords(paidAmount);
    }

    function outstanding() {
        var netAmountVal = $('#net_amount').val();
        var downPaymentVal = $('#down_payment').val();
        var netAmount = 0;
        var downPayment = 0;
        if (netAmountVal != '') {
            netAmount = netAmountVal;
        } else {
            netAmount = 0;
        }
        if (downPaymentVal != '') {
            downPayment = downPaymentVal;
        } else {
            downPayment = 0;
        }
        var outstanding = Math.max(0, Math.round(parseFloat(netAmount) - parseFloat(downPayment)));
        $('#outstanding_amount').val(outstanding.toFixed(2));
    }

    function finalCalculation() {

        // outstanding();
        var hirePrice = $('#gross_hire_price').val();
        var quantity = $('#quantity').val();
        var unitPrice = $('#unit_price').val();
        var downPayment = $('#down_payment').val();
        var outStanding = $('#outstanding_amount').val();
        var totDue = Math.max(0, Math.round(parseFloat(hirePrice) - parseFloat(downPayment)));
        $('#total_due_amount').val(totDue.toFixed(2));


        // var interestRate = parseFloat($('#interest_rate').val()) / 100;
        var timePeriod = $('#installment').val();
        var dateofSale = $('#date_of_sale').val();

        var interestAmount = Math.max(0, Math.round(parseFloat(totDue) - parseFloat(outStanding)));
        $('#interest_amount').val(interestAmount.toFixed(2));

        var interestRate = (parseFloat(unitPrice) > 0 && parseFloat(timePeriod) > 0)
            ? Math.max(0, (parseFloat(interestAmount) * 100) / (parseFloat(unitPrice) * parseFloat(timePeriod)))
            : 0;
        $('#interest_rate').val(parseFloat(interestRate));

        var monthlyInstallment = Math.round(totDue / parseFloat(timePeriod));
        $('#monthly_installment').val(monthlyInstallment.toFixed(2));

        var currentDate = $('#date_of_sale').val();
        var installmentDate = $('#installment_date').val();
        if (installmentDate != '') {
            var seperatedDate = currentDate.split('-');
            var newDate = '' + seperatedDate[0] + '-' + seperatedDate[1] + '-' + installmentDate + '';
        } else {
            var newDate = currentDate;
        }

        var dateforFunction = new Date(newDate);

        Date.prototype.addDays = function(days) {
            this.setDate(this.getDate() + days);
            return this;
        };

        Date.prototype.addMonths = function(months) {
            var dt = this.getDate();

            this.setMonth(this.getMonth() + months);
            var currDt = this.getDate();

            if (dt !== currDt) {
                this.addDays(-currDt);
            }

            return this;
        };

        var duedate = dateforFunction.addMonths(parseFloat(timePeriod));

        function convert(str) {
            var date = new Date(str),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);
            return [date.getFullYear(), mnth, day].join("-");
        }

        var convertedDueDate = convert(duedate);

        $('#due_date').val(convertedDueDate);
    }

    $('#commonModal').on('shown.bs.modal', function() {
        $(document).off('focusin.modal');
        applyRecentCustomer();
    });

    $('#create_btn').click(function() {
        var qtyRequired = $('#quantity').val();
        var qtyAvailable = $('#available_quantity').val();

        event.preventDefault(); // Only for debugging purposes
        const selectedSerial = $('#selected_serial_number').val();

        // console.log("Submitting form with Serial Number:", selectedSerial);

        // Log all form data to ensure it's included
        const formData = $('#sale_form').serializeArray();
        // console.log("Form data before submission:", formData);
        if (parseFloat(qtyRequired) > parseFloat(qtyAvailable)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'You Have No Available Quantity!'
            });
        } else {
            var downPaymentVal = parseFloat($('#down_payment').val()) || 0;
            var netAmountVal = parseFloat($('#net_amount').val()) || 0;
            var grossHirePriceVal = parseFloat($('#gross_hire_price').val()) || 0;

            if (downPaymentVal > netAmountVal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Down Payment',
                    text: 'Down Payment cannot exceed Cash Price / Net Amount!'
                });
                return false;
            }

            if (grossHirePriceVal < netAmountVal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Hire Price',
                    text: 'Gross Hire Price cannot be less than Cash Price / Net Amount!'
                });
                return false;
            }

            if ($('#guarantor').val() == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Select Guarantor!'
                });
                $(this).val('');
                return this;
            }
            if ($('#down_payment').val() == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Enter Down Payment!'
                });
                $(this).val('');
                return this;
            }
            if ($('#outstanding_amount').val() == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Enter Outstanding Amount!'
                });
                $(this).val('');
                return this;
            }
            if ($('#installment').val() == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Select Installment Time Period!'
                });
                $(this).val('');
                return this;
            }
            if ($('#interest_rate').val() == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Enter Interest Rate!'
                });
                $(this).val('');
                return this;
            }
            if ($('#interest_amount').val() == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Enter Interest Amount!'
                });
                $(this).val('');
                return this;
            }
            if ($('#total_due_amount').val() == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Enter Total Due Amount!'
                });
                $(this).val('');
                return this;
            }
            if ($('#monthly_installment').val() == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Enter Monthly Installment!'
                });
                $(this).val('');
                return this;
            }
            if ($('#installment_date').val() == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Select Installment Date!'
                });
                $(this).val('');
                return this;
            }

            var downPaymentFixed = $('#down_payment_fixed').val();
            var downPayment = $('#down_payment').val();
            var interestRate = $('#interest_rate').val();
            var interestRateFixed = $('#interest_rate_fixed').val();
            var discount = $('#discount').val();
            var unitPrice = $('#unit_price').val();
            var originalUnitPrice = $('#original_unit_price').val();

            var autherization = true;
            if (parseFloat(downPayment) < parseFloat(downPaymentFixed)) {
                autherization = false;
                // }
                // else if (interestRate != interestRateFixed) {
                //     autherization = false;
                // } else if (discount > 0) {
                //     autherization = false;
            } else if (unitPrice != originalUnitPrice) {
                autherization = false;
            }

            if (!autherization) {
                (async () => {
                    const {
                        value: formValues
                    } = await Swal.fire({
                        icon: 'warning',
                        title: 'Authenication Needed!',
                        html: '<input type="text" placeholder="E mail" id="swal-input1" class="swal2-input">' +
                            '<input type="password" placeholder="Password" id="swal-input2" class="swal2-input">' +
                            '<input type="button" id="send" value="Send For Autherization" class="btn btn-success">',
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Look up',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return [
                                document.getElementById('swal-input1').value,
                                document.getElementById('swal-input2').value
                            ]
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    })

                    if (formValues) {
                            const selectedSerial = $('#selected_serial_number').val();
                            // console.log("Selected Serial Number Before Submit:", selectedSerial);
                        $.ajax({
                            url: "{{ url('sale/authenticate') }}",
                            method: 'POST',
                            data: {
                                login_details: formValues,
                                "selected_serial_number": selectedSerial,
                                "_token": "{{ csrf_token() }}"
                            },
                            dataType: 'json',
                            success: function(data, textStatus, jqXHR) {
                                if (data === 'success') {
                                    // console.log("Authentication successful. Submitting form with serial:", selectedSerial);
                                        $('#sale_form').submit();
                                } else if (data == 'email_error') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'The email address you entered is not correct. Please check your email address!'
                                    });
                                } else if (data == 'user_error') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'You are not allowed to proceed this sale!'
                                    });
                                } else if (data == 'password_error') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'The password you entered is not correct!'
                                    });
                                }
                            }
                        });
                    }
                })()

            } else {
                $('#sale_form').submit();
            }
        }

    });

    $(document).on('click', '#send', function() {
        //Some code 1
        $('#sale_form').attr('action', '{{ route('waitting_sale.create') }}');
        $('#sale_form').submit();
        // swal.clickConfirm();
    });
    // $('body').submit(function() {
    //     $(".se-pre-con").css({"display": "block"});
    // });

    //form validation
    function validateForm(form) {
        var isValid = new Boolean();
        isValid = true;
        $(".se-pre-con").css({
            "display": "block"
        });

        var elems = $(form).find(':input');
        $.each(elems, function() {
            var elem = $(this);

            if (typeof $(elem).attr('required') != typeof undefined && $(elem).attr('required') != "false" && $(
                    elem).val().trim() == "") {
                Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: $(elem).attr('data-empty-message'),
                    })
                    .then((value) => {
                        $("#" + $(elem).attr('data-focus')).focus();
                    });
                isValid = false;

                $(".se-pre-con").css({
                    "display": "none"
                });
                return false;
            }
        });

        if (!isValid) return false;

        var hireBatch = $(form).find('.sale-batch-select');
        if (hireBatch.length && hireBatch.is(':visible') && !hireBatch.val()) {
            Swal.fire({
                icon: 'error',
                title: 'Batch Required',
                text: 'Please select a Stock Batch.'
            });
            isValid = false;
            $(".se-pre-con").css({"display": "none"});
            return false;
        }

        var serialVal = $(form).find('#serial_select').val() || $(form).find('.selected-warranty-unit-serial').val() || $(form).find('#selected_serial_number').val();
        var qtyVal = parseFloat($(form).find('#quantity').val()) || 0;
        var availVal = parseFloat($(form).find('#available_quantity').val()) || 0;
        if (availVal <= 0 || qtyVal > availVal) {
            Swal.fire({
                icon: 'error',
                title: 'Insufficient Stock',
                text: 'This item has insufficient available stock (Available: ' + availVal + ', Requested: ' + qtyVal + ').'
            });
            isValid = false;
        } else if (serialVal && qtyVal !== 1) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Serial Quantity',
                text: 'This item has a Serial Card No. Serialized items can only have a quantity of 1.'
            });
            isValid = false;
        }

        if (!isValid) {
            $(".se-pre-con").css({"display": "none"});
            return false;
        }

        return isValid;
    }

    $('body').on('input', '#warranty_charges', function() {
        $('#unit_price').trigger('input');
    });

    $('body').on('input', '#insurance_premium', function() {
        $('#unit_price').trigger('input');
    });

    function rtnUnitPriceWithWarrntyInsu() {
        const warranty_charges = parseFloat($('#warranty_charges').val()) || 0;
        const insurance_premium = parseFloat($('#insurance_premium').val()) || 0;
        const unit_price = parseFloat($('#unit_price').val());

        const net_amount = unit_price + warranty_charges + insurance_premium;
        return net_amount;
    }

    document.getElementById('traveling_charges').addEventListener('change', function (e) {
        let value = parseFloat(e.target.value).toFixed(2);  // Convert the value to float and format to 2 decimal places
        e.target.value = value;  // Set the formatted value back to the input
    });

    // Clear localStorage entry on form submission
    $('#sale_form').on('submit', function() {
        setTimeout(() => {
            localStorage.removeItem("newCustomerButtonClicked"); // Remove the flag from localStorage
        }, 100); // Optional delay to ensure the form submission completes
    });




// document.getElementById('sale_form').addEventListener('submit', function() {
//     // Clear the cookie by setting an expiration date in the past
//     document.cookie = "newCustomerButtonClicked=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
// });

</script>
