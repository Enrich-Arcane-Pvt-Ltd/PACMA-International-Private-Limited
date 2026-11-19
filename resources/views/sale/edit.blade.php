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
        border-radius: 0px;
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
<div class="se-pre-con"></div>

<form method="post" action="{{ route('sale.update') }}" onsubmit="return validateForm(this)" id="sale_form"
    enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="customer_type" value="1">
    <input type="hidden" name="modal_type" id="modal_type" value="1">
    <input type="hidden" name="invoice_id" value="{{ $sales->invoice_id }}">
    <input type="hidden" name="sale_id" value="{{ $sales->id }}">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('date_of_sale', __('Date of Sale')) }}<span class="text-danger"> *</span>
            <div class="input-group">
                {{ Form::text('date_of_sale', $sales->date_of_sale, ['class' => 'form-control datepicker', 'required' => 'required', 'data-empty-message' => 'Please Select Date of Sale', 'data-focus' => 'date_of_sale']) }}
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('customer', __('Customer')) }}<span class="text-danger"> *</span>
            <div class="input-group">
                <select class="form-control customer-sel font-style " data-live-search="true" name="customer"
                    id="customer" required data-empty-message="Please Select Customer" data-focus="customer">
                    <option value="">Select Customer</option>
                    @foreach ($customers as $customer)
                        <option @if ($sales->customer_id == $customer->id) selected @endif value="{{ $customer->id }}">
                            {{ $customer->nic_no . ' - ' . $customer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-6" style="display: none;">
            {{ Form::label('sale_type', __('Sale Type')) }}<span class="text-danger"> *</span>
            <div class="input-group">
                {{ Form::text('sale_type', 1, ['class' => 'form-control customer-sel font-style', 'required' => 'required', 'data-empty-message' => 'Please Select Sale Type', 'data-focus' => 'sale_type']) }}
            </div>
        </div>

        <div class="form-group col-md-6" id="hire_purchase_index_div">
            {{ Form::label('hire_purchase_index', __('Index')) }}
            <div class="input-group">
                {{ Form::text('hire_purchase_index', $sales->hire_purchase_index, ['class' => 'form-control font-style', 'readonly' => 'readonly']) }}
            </div>
        </div>

        <div class="form-group col-md-4" id="guarantor_div">
            {{ Form::label('guarantor', __('1st Guarantor')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style " data-live-search="true" name="guarantor"
                    id="guarantor" data-empty-message="Please Select Guarantor" data-focus="guarantor">
                    <option value="">Select 1st Guarantor</option>
                    @foreach ($guarantors as $guarantor)
                        <option @if ($sales->guarantor_id == $guarantor->id) selected @endif value="{{ $guarantor->id }}">
                            {{ $guarantor->nic_no . ' - ' . $guarantor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-4" id="guarantor_div_2">
            {{ Form::label('guarantor_2', __('2nd Guarantor')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style " data-live-search="true" name="guarantor_2"
                    id="guarantor_2">
                    <option value="">Select 2nd Guarantor</option>
                    @foreach ($guarantors as $guarantor)
                        <option @if ($sales->guarantor_2_id == $guarantor->id) selected @endif value="{{ $guarantor->id }}">
                            {{ $guarantor->nic_no . ' - ' . $guarantor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-4" id="guarantor_div_3">
            {{ Form::label('guarantor_3', __('3rd Guarantor')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style " data-live-search="true" name="guarantor_3"
                    id="guarantor_3">
                    <option value="">Select 3rd Guarantor</option>
                    @foreach ($guarantors as $guarantor)
                        <option @if ($sales->guarantor_3_id == $guarantor->id) selected @endif value="{{ $guarantor->id }}">
                            {{ $guarantor->nic_no . ' - ' . $guarantor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-6" id="guarantor_known_div_1">
            {{ Form::label('guarantor_1_known_duration', __('1st Guarantor Known Hirer Since (Years)')) }}
            {{ Form::text('guarantor_1_known_duration', $sales->guarantor_1_known_duration ?? null, ['class' => 'form-control', 'placeholder' => __('e.g. 5')]) }}
        </div>

        <div class="form-group col-md-6" id="guarantor_known_div_2">
            {{ Form::label('guarantor_2_known_duration', __('2nd Guarantor Known Hirer Since (Years)')) }}
            {{ Form::text('guarantor_2_known_duration', $sales->guarantor_2_known_duration ?? null, ['class' => 'form-control', 'placeholder' => __('e.g. 3')]) }}
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
                            {{ Form::text('witness_1_name', $sales->witness_1_name ?? null, ['class' => 'form-control', 'placeholder' => __('Name')]) }}
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            {{ Form::label('witness_1_nic', __('1st Witness NIC')) }}
                            {{ Form::text('witness_1_nic', $sales->witness_1_nic ?? null, ['class' => 'form-control', 'placeholder' => __('NIC Number')]) }}
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            {{ Form::label('witness_1_address', __('1st Witness Address')) }}
                            {{ Form::text('witness_1_address', $sales->witness_1_address ?? null, ['class' => 'form-control', 'placeholder' => __('Address')]) }}
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            {{ Form::label('witness_2_name', __('2nd Witness Name')) }}
                            {{ Form::text('witness_2_name', $sales->witness_2_name ?? null, ['class' => 'form-control', 'placeholder' => __('Name')]) }}
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            {{ Form::label('witness_2_nic', __('2nd Witness NIC')) }}
                            {{ Form::text('witness_2_nic', $sales->witness_2_nic ?? null, ['class' => 'form-control', 'placeholder' => __('NIC Number')]) }}
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            {{ Form::label('witness_2_address', __('2nd Witness Address')) }}
                            {{ Form::text('witness_2_address', $sales->witness_2_address ?? null, ['class' => 'form-control', 'placeholder' => __('Address')]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('introducer_id', __('Salesman')) }}<span class="text-danger"> *</span>
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" data-live-search="true" name="introducer_id" id="introducer_id"
                    data-empty-message="Please Select Salesman" data-focus="introducer_id" required>
                    <option value="">Select Salesman</option>
                    @foreach ($users as $user)
                        <option @if ($sales->introducer_id == $user->id) selected @endif value="{{ $user->id }}">
                            {{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-12">
            <div class="card repeater">
                <div class="card-header">
                    <h4 class="sub-title">{{ __('Products') }}</h4>

                    <div class="card-header-action">
                        <div id="barcode_input_container">
                            {{ Form::label('product', __('Enter Product Code')) }}
                            <div id="barcode_scan_input">
                                <i class="fas fa-barcode" id="barcode_icon"></i>
                                {{ Form::text('barcode', $sales->products->barcode, ['class' => 'form-control', 'id' => 'barcode_input', 'onkeypress' => 'scanEvent(event)']) }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <input type="hidden" name="pre_inventory_id" id="pre_inventory_id" value="{{ $sales->inventory_id }}">
            <input type="hidden" name="inventory_id" id="inventory_id" value="{{ $sales->inventory_id }}">
            <input type="hidden" name="purchase_price" id="purchase_price"
                value="{{ $sales->inventory->purchase_price }}">
            {{ Form::label('product', __('Product')) }}<span class="text-danger"> *</span>
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" name="product" id="product" required
                    data-empty-message="Please Select Product" data-focus="product" data-live-search="true">
                    <option value="">Select Product (Search by Category, Name or Code)</option>
                    @if(!empty($sales->products))
                        @php
                            $selectedCatName = $sales->products->productCategory->name ?? ($sales->products->categoryDetails->name ?? '');
                            $selectedSubCatName = $sales->products->productSubCategory->name ?? ($sales->products->subCategoryDetails->name ?? '');
                            $selectedProdName = $sales->products->name ?? '';
                            $selectedBarcode = $sales->products->barcode ?? '';
                            $selectedCatParts = array_filter([$selectedCatName, $selectedSubCatName]);
                            $selectedCatPrefix = !empty($selectedCatParts) ? implode(' / ', $selectedCatParts) . ' — ' : '';
                        @endphp
                        <option selected value="{{ $sales->products->id }}"
                            data-tokens="{{ $selectedCatName }} {{ $selectedSubCatName }} {{ $selectedProdName }} {{ $selectedBarcode }}"
                            data-subtext="{{ $selectedBarcode }}">
                            {{ $selectedCatPrefix }}{{ $selectedProdName }}
                        </option>
                    @endif
                    @foreach ($products as $product)
                        @if(!empty($product->products) && (empty($sales->products) || $product->products->id != $sales->products->id))
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
            {{ Form::label('unit_price', __('Purchasing Price')) }}<span class="text-danger"> *</span>
            <div class="input-group colorpickerinput">
                <input type="hidden" name="original_unit_price" id="original_unit_price"
                    value="{{ $sales->unit_price }}" />
                {{ Form::number('unit_price', sprintf('%.2f', $sales->unit_price), ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Purchasing Price', 'data-focus' => 'unit_price']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        @php
            $remainQty = $sales->inventory->remain_qty ?? 0;
            $saleQty = $sales->quantity;
            $availableQty = $remainQty + $saleQty;

        @endphp

        <div class="form-group col-md-3">
            {{ Form::label('available_quantity', __('Available Quantity')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('available_quantity', $availableQty, ['class' => 'form-control available_quantity', 'readonly' => 'readonly']) }}
            </div>
        </div>
        <div class="form-group col-md-3">
            <input type="hidden" name="pre_qty" value="{{ $sales->quantity }}">
            {{ Form::label('quantity', __('Quantity')) }}<span class="text-danger"> *</span>
            <div class="input-group colorpickerinput">
                {{ Form::number('quantity', $sales->quantity, ['class' => 'form-control quantity', 'required' => 'required', 'data-empty-message' => 'Please Enter Quantity', 'data-focus' => 'quantity']) }}
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('gross_hire_price', __('Gross Hire Price')) }} <span class="text-danger">*</span>
            <div class="input-group colorpickerinput">
                <input type="hidden" name="original_gross_hire_price" id="original_gross_hire_price"
                    value="{{ $sales->gross_hire_price }}" />
                {{ Form::number('gross_hire_price', $sales->gross_hire_price, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Gross Hire Price', 'data-focus' => 'gross_hire_price']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="form-group col-md-6">
            {{ Form::label('gross_amount', __('Gross Amount')) }}<span class="text-danger"> *</span>
        <div class="input-group colorpickerinput">
            {{ Form::number('gross_amount', sprintf('%.2f', $sales->gross_amount), array('class' => 'form-control price','required'=>'required','step'=>'0.01', 'data-empty-message'=>"Please Enter Gross Amount", 'data-focus'=>"gross_amount", 'readonly'=>"readonly")) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('discount', __('Discount')) }}<span class="text-danger"> *</span>
        <div class="input-group colorpickerinput">
            {{ Form::number('discount', sprintf('%.2f', $sales->discount), array('class' => 'form-control discount','required'=>'required','step'=>'0.01', 'data-empty-message'=>"Please Entert Discount", 'data-focus'=>"discount")) }}
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
            {{ Form::label('net_amount', __('Net Amount')) }}<span class="text-danger"> *</span>
            <div class="input-group colorpickerinput">
                {{ Form::number('net_amount', sprintf('%.2f', $sales->net_amount), ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Net Amount', 'data-focus' => 'net_amount', 'readonly' => 'readonly']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $downPayementRate = $sales->products->product_category->down_payment / 100;
        $downPaymentFixed = $sales->net_amount * $downPayementRate;
    @endphp
    <div class="row" id="higher_purchase_div">
        <input type="hidden" id="down_payment_fixed" name="down_payment_fixed" value="{{ $downPaymentFixed }}">
        <input type="hidden" id="down_payment_rate" name="down_payment_rate" value="{{ $downPayementRate }}">
        <div class="form-group col-md-6">
            {{ Form::label('down_payment', __('Down Payment')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('down_payment', sprintf('%.2f', $sales->down_payment), ['class' => 'form-control price', 'step' => '0.01']) }}
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
                {{ Form::number('outstanding_amount', sprintf('%.2f', $sales->outstanding_amount), ['class' => 'form-control price', 'step' => '0.01', 'readonly' => 'readonly']) }}
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
                        <option @if ($sales->installment == $timePeriod->time_period) selected @endif
                            value="{{ $timePeriod->time_period }}">{{ $timePeriod->time_period }}</option>
                    @endforeach
                </select>
                <div class="input-group-text">
                    Months
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <input type="hidden" id="interest_rate_fixed" name="interest_rate_fixed"
                value="{{ $sales->interest_rate }}">

            {{ Form::label('interest_rate', __('Interest Rate')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('interest_rate', $sales->interest_rate, ['class' => 'form-control interest_rate', 'step' => '0.01', 'id' => 'interest_rate']) }}
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
                {{ Form::number('interest_amount', sprintf('%.2f', $sales->interest_amount), ['class' => 'form-control price', 'step' => '0.01']) }}
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
                {{ Form::number('total_due_amount', sprintf('%.2f', $sales->total_due_amount), ['class' => 'form-control price', 'step' => '0.01']) }}
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
                {{ Form::number('monthly_installment', sprintf('%.2f', $sales->monthly_installment), ['class' => 'form-control price', 'step' => '0.01']) }}
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
                        <option @if ($sales->installment_date == $i) selected @endif value="{{ $i }}">
                            {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('due_date', __('Due Date')) }}
            <div class="input-group colorpickerinput">
                {{ Form::text('due_date', $sales->due_date, ['class' => 'form-control', 'readonly' => 'readonly']) }}
            </div>
        </div>

        <div class="form-group col-md-6">
            <input type="hidden" name="document_charges_in_words" id="document_charges_in_words">
            {{ Form::label('document_charges', __('Document Charges')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('document_charges', sprintf('%.2f', $sales->document_charges), ['class' => 'form-control price', 'step' => '0.01']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="advanced_div">
        <div class="form-group col-md-6">
            {{ Form::label('advance_amount', __('Advance Amount')) }}<span class="text-danger"> *</span>
            <div class="input-group colorpickerinput">
                {{ Form::number('advance_amount', $sales->advance_amount ? sprintf('%.2f', $sales->advance_amount) : 0, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Insert Advance Amount', 'data-focus' => 'advance_amount']) }}
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
            {{ Form::label('paid_amount', __('Paid Amount')) }}<span class="text-danger"> *</span>
            <div class="input-group colorpickerinput">
                {{ Form::number('paid_amount', '', ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Insert Paid Amount', 'data-focus' => 'paid_amount']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('paid_amount_in_words', __('Paid Amount In Words')) }}<span class="text-danger"> *</span>
            <div class="input-group colorpickerinput">
                <textarea class="form-control paid_amount_in_words" id="paid_amount_in_words" name="paid_amount_in_words" required
                    data-empty-message="Please Insert Paid Amount In Words" data-focus="paid_amount_in_words"></textarea>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('payment_method', __('Payment Method')) }}<span class="text-danger"> *</span>
            <select class="form-control payment_method" name="payment_method" id="payment_method" required
                data-empty-message="Please select Payment Method" data-focus="payment_method">
                <option value="">Select Payment Method</option>
                <option @if ($sales->payment_method == 'CASH') selected @endif value="CASH">Cash</option>
                <option @if ($sales->payment_method == 'CHEQUE') selected @endif value="CHEQUE">Cheque</option>
                <option @if ($sales->payment_method == 'CARD') selected @endif value="CARD">Card</option>
                <option @if ($sales->payment_method == 'ONLINEPAYMENT') selected @endif value="ONLINEPAYMENT">Online
                    Payment</option>
            </select>
        </div>

        <div class="form-group col-md-6 cheque_number_div">
            {{ Form::label('realised_date', __('Realised Date')) }}<span class="text-danger"> *</span>
            <div class="input-group">
                {{ Form::text('realised_date', $sales->cheque_realised_date, ['class' => 'form-control datepicker', 'required' => 'required']) }}
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6 cheque_number_div">
            {{ Form::label('cheque_number', __('Cheque Number')) }}
            {{ Form::text('cheque_number', $sales->cheque_number, ['class' => 'form-control cheque_number']) }}
        </div>

        <div class="form-group col-md-6" id="card_type_div">
            {{ Form::label('card_type', __('Card Type')) }}
            <select class="form-control card_type" name="card_type" id="card_type">
                <option value="">Select Card Type</option>
                <option @if ($sales->card_type == 1) selected @endif value="1">Credit Card</option>
                <option @if ($sales->card_type == 2) selected @endif value="2">Debit Card</option>
            </select>
        </div>

        <div class="form-group col-md-6" id="card_charge_amount_div">
            {{ Form::label('card_charge_amount', __('Card Charges')) }}
            {{ Form::number('card_charge_amount', $sales->card_charge_amount, ['class' => 'form-control card_charge_amount', 'step' => '0.01']) }}
        </div>

        <div class="form-group col-md-6" id="total_amount_div">
            {{ Form::label('total_amount', __('Total Amount')) }}
            {{ Form::number('total_amount', $sales->total_amount, ['class' => 'form-control total_amount', 'step' => '0.01', 'readonly' => 'readonly']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('bank', __('Company Bank')) }}<span class="text-danger"> *</span>
            <div class="input-group">
                {{ Form::select('bank', $banks, $sales->bank_id, ['class' => 'form-control font-style', 'required' => 'required', 'data-empty-message' => 'Please Select a Company Bank', 'data-focus' => 'bank']) }}
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('traveling_charges', __('Transport Charges')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('traveling_charges', $sales->traveling_charges, ['class' => 'form-control price', 'step' => '0.01', 'placeholder' => '0.00']) }}
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
        {{-- <div class="form-group col-md-6">
            {{ Form::label('guarantor_2', __('Warranty Card No')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style " data-live-search="true" name="product_warranty_id"
                    id="product_warranty_id">
                    <option value="">Select Warranty Card No</option>
                    @foreach ($productWarranties as $productWarranty)
                    <option value="{{ $productWarranty->id }}" @if ($hirePurchaseDetail->product_warranty_id == $productWarranty->id) selected @endif>
                        {{ $productWarranty->warranty_card_no }}</option>
                    @endforeach
                </select>
            </div>
        </div> --}}
        <div class="form-group col-md-6">
            {{ Form::label('guarantor_2', __('Serial Card No')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style" data-live-search="true" name="serial_card_no"
                    id="serial_select">
                    <option value="">Select Serial Card No</option>
                    <option selected value="{{ $sale->hirePurchaseDetails->warranty->product_warranty_id }}">
                        {{ $sale->hirePurchaseDetails->warranty->serial_number }}
                    </option>
                    @foreach ($serialNumbers as $serial)
                        <option value="{{ $serial->id }}" data-warranty="{{ $serial->warranty_number }}">
                            {{ $serial->serial_number }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('guarantor_2', __('Warranty Card No')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style" data-live-search="true" name="warranty_card_no"
                    id="warranty_select">
                    <option value="">Select Warranty Card No</option>
                    <option selected value="{{ $sale->hirePurchaseDetails->warranty->product_warranty_id }}">
                        {{ $sale->hirePurchaseDetails->warranty->warranty_number }}
                    </option>
                    @foreach ($serialNumbers as $serial)
                        <option value="{{ $serial->id }}">
                            {{ $serial->warranty_number }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('warranty_period', __('Warranty Period')) }}
                <div class="input-group colorpickerinput">
                    {{ Form::number('warranty_period', $hirePurchaseDetail->warranty_period ?? '', ['class' => 'form-control price', 'step' => '1']) }}
                    <div class="input-group-append">
                        <div class="input-group-text">
                            {{ 'Months' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('warranty_charges', __('Warranty Charges')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('warranty_charges', $hirePurchaseDetail->warranty_charges ?? 0, ['class' => 'form-control price', 'step' => '0.01', 'placeholder' => '0.00', 'min' => '0']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label class="form-lable">Warranty Document</label>
            @if ($hirePurchaseDetail && $hirePurchaseDetail->warranty_document != 'assets/img/products/default.png')
                <a href="{{ asset($hirePurchaseDetail->warranty_document) }}" class="text-info text-small ml-1"
                    id="warranty_document_btn" download><i class="fas fa-download"></i> Download</a>
            @endif
            <input type="file" class="form-control form-input" id="warranty_document" name="warranty_document"
                style="overflow: hidden;" />
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('additional_warranty_description', __('Additional Warranty Description')) }}
            {!! Form::textarea(
                'additional_warranty_description',
                $hirePurchaseDetail->additional_warranty_description ?? '',
                ['class' => 'form-control', 'rows' => '5'],
            ) !!}
        </div>
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="button" class="btn btn-primary" id="create_btn">{{ __('Edit') }}</button>
    </div>
    <div id="sales-data" data-sales='<?php echo json_encode($sales); ?>'></div>
</form>

<script>
    var salesDataElement = document.getElementById('sales-data');
    var sales = JSON.parse(salesDataElement.getAttribute('data-sales'));

    $(document).ready(function() {
        // $('.selectpicker').selectpicker();

        console.log(sales);

        $('#hire_purchase_index_div').show();

        $('#higher_purchase_div').show();
        $('#guarantor_div').show();
        $('#guarantor_div_2').show();
        $('#guarantor_div_3').show();
        $('#guarantor_known_div_1').show();
        $('#guarantor_known_div_2').show();
        $('#interest_rate').prop('disabled', false);
        $('#interest_amount').prop('disabled', false);
        $('#total_due_amount').prop('disabled', false);
        $('#monthly_installment').prop('disabled', false);
        $('#installment_date').prop('disabled', false);
        $('#due_date').prop('disabled', false);

        $('#payment_method').val(sales.payment_method).trigger('change');
        $('#paid_amount').val(sales.paid_amount);
        $('#paid_amount_in_words').html(sales.paid_amount_in_words);
        // $('#document_charges_in_words').val(sales.receipts[1].paid_amount_in_words);

        if ($('#hire_purchase_index').val() == '') {
            hirePurchaseIndex(sales.customer_id, sales.date_of_sale);
        }

        var paymentMethod = sales.payment_method;
        changeMethod(paymentMethod);

        if (paymentMethod == 'CARD') {
            $('#card_type').val(sales.card_type).trigger('change');
        }

        if (sales.advance_amount != 0) {
            $('#advanced_div').show();
        } else {
            $('#advanced_div').hide();
        }

        // Pre-select warranty and serials for the edited sale
        @foreach ($sales->saleItems as $item)
            var productId = {{ $item->product_id }};
            var serials = @json($warrantyData[$item->product_id] ?? []);

            // If there are serials for this product, populate the select
            if (serials.length > 0) {
                var serialSelect = $('#serial_select'); // Adjust this selector based on your context
                var warrantySelect = $('#warranty_select'); // Adjust this selector based on your context

                serialSelect.empty().append('<option value="">Select Serial Card No</option>');
                warrantySelect.empty().append('<option value="">Select Warranty Card No</option>');

                serials.forEach(function(item) {
                    $('#serial_select').append(
                        $('<option></option>').val(item
                            .serial_number).text(item
                            .serial_number)
                    );
                    $('#warranty_select').append(
                        $('<option></option>').val(item
                            .warranty_number).text(item
                            .warranty_number)
                    );
                    // serialSelect.append($('<option></option>').val(item.serial_number).text(item
                    //     .serial_number));
                    // warrantySelect.append($('<option></option>').val(item.warranty_number).text(item
                    //     .warranty_number));
                });

                // Set the selected values
                serialSelect.val('{{ $item->warranty->serial_number }}').trigger('change');
                warrantySelect.val('{{ $item->warranty->warranty_number }}').trigger('change');

                // Refresh select picker
                serialSelect.selectpicker('refresh');
                warrantySelect.selectpicker('refresh');
                $('.selectpicker').selectpicker('refresh');


            }
        @endforeach
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
        var date = $('#date_of_sale').val();
        var custId = $(this).val();
        if (custId != '') {
            $.ajax({
                url: "{{ url('sale/get_cust_advance') }}/" + custId,
                method: 'GET',
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    console.log(data);
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

        if (custId != '' && $('#sale_type').val() == 1) {
            hirePurchaseIndex(custId, date);
        } else {
            $('#hire_purchase_index_div').hide(200);
            $('#hire_purchase_index').val('');
        }
    });

    $('body').on('change', '#product', function() {
        var productId = $(this).val();
        $.ajax({
            url: "{{ url('sale/get_details') }}/" + productId,
            method: 'GET',
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                if (data.length > 0) {
                    console.log(data, "hire data");

                    $('#inventory_id').val(data[0].inventory_id);
                    $('#purchase_price').val(parseFloat(data[0].purchase_price).toFixed(2));
                    $("#unit_price").val(parseFloat(data[0].sale_price).toFixed(2));
                    $("#original_unit_price").val(parseFloat(data[0].sale_price).toFixed(2));
                    $("#gross_hire_price").val(parseFloat(data[0].hire_price).toFixed(2));
                    $("#original_gross_hire_price").val(parseFloat(data[0].hire_price)
                        .toFixed(2));
                    $("#available_quantity").val(data[0].remain_qty);
                    $("#quantity").val(1);
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
                    $('#additional_warranty_description').html(data[0].products
                        .additional_warranty_description);

                    if (!data[0].products.warranty_document || data[0].products.warranty_document ==
                        'assets/img/products/default.png') {
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

                // use for warranty data
                $.ajax({
                    url: "{{ url('sale/get_warranty_serials') }}/" + productId,
                    method: 'GET',
                    dataType: 'json',
                    success: function(warrantyData) {

                        // Clear existing options in both selects
                        $('#warranty_select').empty().append(
                            '<option value="">Select Warranty Card No</option>');
                        $('#serial_select').empty().append(
                            '<option value="">Select Serial Card No</option>');

                        let serialToWarrantyMap = {};

                        if (warrantyData.length > 0) {
                            warrantyData.forEach(function(item) {
                                // Populate the serial number select
                                $('#serial_select').append(
                                    $('<option></option>').val(item
                                        .serial_number).text(item
                                        .serial_number)
                                );

                                // Populate the warranty number select (for manual selection)
                                $('#warranty_select').append(
                                    $('<option></option>').val(item
                                        .warranty_number).text(item
                                        .warranty_number)
                                );

                                // Map serial number to warranty number
                                serialToWarrantyMap[item.serial_number] = item
                                    .warranty_number;
                            });

                            // Refresh select picker
                            $('.selectpicker').selectpicker('refresh');
                        } else {
                            $('#warranty_select').empty().append(
                                '<option value="">Select Warranty Card No</option>');
                            $('#serial_select').empty().append(
                                '<option value="">Select Serial Card No</option>');
                            $('.selectpicker').selectpicker('refresh');
                            Swal.fire({
                                icon: 'error',
                                title: 'No Warranty Data',
                                text: 'No warranty data available for the selected product.',
                            });
                        }
                        // Listen for changes in the serial number select
                        $('#serial_select').on('change', function() {
                            const selectedSerial = $(this).val();

                            // If a serial number is selected, auto-fill the warranty number
                            if (selectedSerial) {
                                const correspondingWarranty =
                                    serialToWarrantyMap[selectedSerial];
                                $('#warranty_select').val(correspondingWarranty)
                                    .trigger('change');
                                $('.selectpicker').selectpicker('refresh');
                            }
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Error fetching warranty serials:", textStatus,
                            errorThrown);
                    }
                });
                $('#warranty_select').empty().append(
                    '<option value="">Select Warranty Card No</option>');
                $('#serial_select').empty().append(
                    '<option value="">Select Serial Card No</option>');
                $('.selectpicker').selectpicker('refresh');
            }
        });
    });

    $('body').on('change', '#payment_method', function() {
        var method = $(this).val();
        changeMethod(method);
    });

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

    $('body').on('change', '#card_type', function() {
        var cardType = $(this).val();
        var paidAmount = $('#paid_amount').val();
        var paymentMethod = $('#payment_method').val();

        if (paymentMethod == 'CARD') {
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
        }
    });

    $('body').on('input', '#card_charge_amount', function() {
        var cardCharges = $(this).val();
        var paidAmounts = $('#paid_amount').val();
        var totalAmounts = parseFloat(paidAmounts) + parseFloat(cardCharges);
        $('#total_amount').val(totalAmounts);
        paidAmountInWords(totalAmounts);
    });


    $('body').on('input', '#unit_price', function() {

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
        var
            unitPrice = rtnUnitPriceWithWarrntyInsu();
        var grossVal = parseFloat(unitPrice) * parseFloat(quantity);
        $('#gross_amount').val(parseFloat(grossVal).toFixed(2));
        $('#net_amount').val(parseFloat(grossVal).toFixed(2));
        var outStanding = parseFloat(grossVal) -
            parseFloat(downPayment);
        $('#outstanding_amount').val(parseFloat(outStanding).toFixed(2));
        finalCalculation();
    });

    $('body').on('input', '#document_charges', function() {
        var documentcharges = $(this).val();
        documentChargeInWords(documentcharges);
    });

    $('body').on('input', '#paid_amount', function() {
        var adv = parseFloat($('#advance_amount').val() || 0);
        paidAmountInWords(Math.max(0, parseFloat($(this).val() || 0) - adv));
    });

    function hirePurchaseIndex(customer, date) {
        $.ajax({
            url: "{{ url('sale/get_hire_purchase_index') }}/" + customer + "/" + date,
            method: 'GET',
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                if (data != null) {
                    $('#hire_purchase_index').val(data);
                    $('#hire_purchase_index_div').show(200);
                }
            }
        });
    }

    // function paidAmountInWords(amount) {
    //     $("#paid_amount_in_words").html('');
    //     var words = '';
    //     words = toWords(Number(amount));
    //     $("#paid_amount_in_words").html(words + "Only");
    // }

    function paidAmountInWords(amount) {
        $("#paid_amount_in_words").html('');
        // Round the amount to 2 decimal places
        var roundedAmount = Math.round(amount * 100) / 100;
        var words = toWords(roundedAmount);
        $("#paid_amount_in_words").html(words + " Only");
    }

    // function documentChargeInWords(amount) {
    //     // $("#document_charges_in_words").html('');
    //     var chargeWords = '';
    //     chargeWords = toWords(Number(amount));
    //     $("#document_charges_in_words").val(chargeWords + "Only");
    // }

    function documentChargeInWords(amount) {
        // $("#document_charges_in_words").html('');
        // Round the amount to 2 decimal places
        var roundedAmount = Math.round(amount * 100) / 100;
        var chargeWords = toWords(roundedAmount);
        $("#document_charges_in_words").val(chargeWords + " Only");
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

        var netAmount = parseFloat(grossAmount) - parseFloat(discount);
        $('#net_amount').val(parseFloat(netAmount).toFixed(2));

        downPayment(parseFloat(netAmount).toFixed(2));
        outstanding();
    }

    function downPayment(netAmount) {
        var downRate = $('#down_payment_rate').val();
        var downValu = parseFloat(netAmount) * parseFloat(downRate);
        var adv = parseFloat($('#advance_amount').val() || 0);
        var paidAmount = Math.max(0, downValu - adv);

        $('#down_payment_fixed').val(parseFloat(downValu).toFixed(2));
        $('#down_payment').val(parseFloat(downValu).toFixed(2));
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
        var outstanding = Math.max(0, parseFloat(netAmount) - parseFloat(downPayment));
        $('#outstanding_amount').val(parseFloat(outstanding).toFixed(2));
    }

    function finalCalculation() {
        // outstanding();
        var hirePrice = $('#gross_hire_price').val();
        var quantity = $('#quantity').val();
        var unitPrice = $('#unit_price').val();
        var downPayment = $('#down_payment').val();
        var outStanding = $('#outstanding_amount').val();
        var totDue = Math.max(0, parseFloat(hirePrice) - parseFloat(downPayment));
        $('#total_due_amount').val(parseFloat(totDue).toFixed(2));


        // var interestRate = parseFloat($('#interest_rate').val()) / 100;
        var timePeriod = $('#installment').val();
        var dateofSale = $('#date_of_sale').val();

        var interestAmount = Math.max(0, parseFloat(totDue) - parseFloat(outStanding));
        $('#interest_amount').val(parseFloat(interestAmount).toFixed(2));

        var interestRate = (parseFloat(unitPrice) > 0 && parseFloat(timePeriod) > 0)
            ? Math.max(0, (parseFloat(interestAmount) * 100) / (parseFloat(unitPrice) * parseFloat(timePeriod)))
            : 0;
        $('#interest_rate').val(parseFloat(interestRate));

        var monthlyInstallment = totDue / parseFloat(timePeriod);
        $('#monthly_installment').val(parseFloat(monthlyInstallment).toFixed(2));

        var currentDate = $('#date_of_sale').val();
        var installmentDate = $('#installment_date').val();
        if (installmentDate != '') {
            var seperatedDate = currentDate.split('-');
            var newDate = '' + seperatedDate[0] + '-' + seperatedDate[1] + '-' + installmentDate + '';
        } else {
            var newDate = currentDate;
        }

        var dateforFunction = new Date(newDate);

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
    });

    $('#create_btn').click(function() {
        var saleType = $('#sale_type').val();
        var qtyRequired = $('#quantity').val();
        var qtyAvailable = $('#available_quantity').val();

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
                // } else if (interestRate != interestRateFixed) {
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
                            '<input type="password" placeholder="Password" id="swal-input2" class="swal2-input">',
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
                        $.ajax({
                            url: "{{ url('sale/authenticate') }}",
                            method: 'POST',
                            data: {
                                login_details: formValues,
                                "_token": "{{ csrf_token() }}"
                            },
                            dataType: 'json',
                            success: function(data, textStatus, jqXHR) {
                                if (data == 'success') {
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
</script>
