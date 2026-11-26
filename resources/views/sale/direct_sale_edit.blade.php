@include('sale.batch-controls', ['batchCash' => true])
<style>
    #barcode_input_container {
        border-radius: 3px;
        display: flex;
        background: #3abaf4;
    }

    #barcode_icon {
        padding: 11px 13px;
        color: #ffffff;
    }

    #barcode_input {
        overflow: hidden;
        border-radius: 0px 3px 3px 0px;
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

    /* =========================================================
       Form Layout & Vertical Alignment Standards
       ========================================================= */
    #sale_form .form-group {
        margin-bottom: 1rem;
    }

    #sale_form .form-group > label {
        font-size: 13px;
        font-weight: 600;
        color: #34395e;
        margin-bottom: 6px;
        height: 24px;
        min-height: 24px;
        display: flex;
        align-items: center;
        line-height: 1.3;
    }

    #sale_form .form-group > label .text-danger {
        margin-left: 3px;
        display: inline;
    }

    /* All form controls unified vertical height & alignment */
    #sale_form input.form-control,
    #sale_form select.form-control,
    #sale_form .input-group-text,
    #sale_form .bootstrap-select > .dropdown-toggle {
        height: 38px !important;
        min-height: 38px !important;
        font-size: 13px !important;
        line-height: 22px !important;
        padding: 7px 12px !important;
        border-radius: 4px;
        box-sizing: border-box;
    }

    /* Input group alignment & border fix */
    #sale_form .input-group {
        display: flex;
        align-items: stretch;
        width: 100%;
    }

    #sale_form .input-group .input-group-text {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 12px !important;
    }

    #sale_form .input-group > .form-control {
        flex: 1 1 auto;
        width: 1%;
    }

    #sale_form .input-group > .form-control:not(:last-child) {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }

    #sale_form .input-group > .input-group-append > .input-group-text {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }

    #sale_form .input-group > .input-group-prepend > .input-group-text {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }

    #sale_form .input-group > .form-control:not(:first-child) {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }

    /* Bootstrap selectpicker vertical alignment */
    .cash-sale-product-cell .bootstrap-select {
        width: 100% !important;
    }

    .cash-sale-product-cell .bootstrap-select > .dropdown-toggle {
        height: 38px !important;
        min-height: 38px !important;
        display: flex !important;
        align-items: center !important;
        padding: 7px 28px 7px 12px !important;
    }

    .cash-sale-product-cell .bootstrap-select .filter-option {
        display: flex !important;
        align-items: center !important;
        height: 100% !important;
    }

    .cash-sale-product-cell .filter-option-inner-inner {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 22px;
    }

    .cash-sale-item-card {
        border-radius: 8px;
        border: 1px solid #e3e8ee;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        background: #fff;
        margin-bottom: 1.25rem;
    }

    .cash-sale-item-card .card-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #eef2f7;
    }

    .cash-sale-item-card .form-group {
        margin-bottom: 0.85rem;
    }

    .cash-sale-item-card .form-group > label {
        min-height: 28px;
        display: flex;
        align-items: flex-end;
        margin-bottom: 6px;
        font-size: 12.5px;
        font-weight: 600;
        color: #495057;
        line-height: 1.25;
    }

    .cash-sale-item-card input[type="file"].form-control {
        height: 38px !important;
        min-height: 38px !important;
        padding: 5px 10px !important;
        line-height: 24px !important;
    }

    /* Text alignment in inputs */
    .cash-sale-item-card .available_quantity,
    .cash-sale-item-card .quantity {
        text-align: center;
    }

    .cash-sale-item-card .hire_price,
    .cash-sale-item-card .gross_amount,
    .cash-sale-item-card .discount,
    .cash-sale-item-card .net_amount {
        text-align: right;
    }

    #cash_sale_items {
        overflow-x: visible !important;
        min-height: 200px;
    }

    #sortable-table {
        width: 100% !important;
        max-width: 100% !important;
    }

    .cash-sale-item-row > td {
        padding: 0 !important;
        border: none !important;
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
<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

<form method="post" action="{{ route('sale.update') }}" onsubmit="return validateForm(this)" id="sale_form"
    enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="sale_type" id="sale_type" value="0">
    <input type="hidden" name="modal_type" id="modal_type" value="2">
    <input type="hidden" name="invoice_id" value="{{ $sales->invoice_id }}">
    <input type="hidden" name="sale_id" value="{{ $sales->id }}">
    <div class="row">
        <div class="form-group col-md-3">
            <label for="date_of_sale">{{ __('Date of Sale') }} <span class="text-danger">*</span></label>
            <div class="input-group">
                {{ Form::text('date_of_sale', $sales->date_of_sale, ['class' => 'form-control datepicker', 'required' => 'required', 'data-empty-message' => 'Please Select Date of Sale', 'data-focus' => 'date_of_sale', 'id' => 'date_of_sale']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-3">
            <label for="customer_type">{{ __('Customer Type') }} <span class="text-danger">*</span></label>
            <select class="form-control font-style" name="customer_type" id="customer_type" required
                data-empty-message="Please Select Customer Type" data-focus="customer_type">
                <option value="">Select Customer</option>
                <option @if ($sales->customer_type == 1) selected @endif value="1">Regular Customer</option>
                <option @if ($sales->customer_type == 2) selected @endif value="2">New Customer</option>
            </select>
        </div>

        <div class="form-group col-md-3" id="customer_div">
            <label for="customer">{{ __('Customer') }} <span class="text-danger">*</span></label>
            <select class="form-control customer-sel font-style selectpicker" data-live-search="true" name="customer"
                id="customer">
                <option value="">Select Customer</option>
                @foreach ($customers as $customer)
                    <option @if ($sales->customer_id == $customer->id) selected @endif value="{{ $customer->id }}">
                        {{ $customer->nic_no . ' - ' . $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-3" id="customer_name_div">
            <label for="customer_name">{{ __('Customer Name') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <input type="text" class="form-control font-style" name="customer_name" id="customer_name"
                    value="{{ $sales->customer_name }}">
            </div>
        </div>

        <div class="form-group col-md-3" id="customer_nic_div">
            <label for="customer_nic">{{ __('Customer NIC') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-id-card"></i>
                    </div>
                </div>
                <input type="text" class="form-control font-style" name="customer_nic" id="customer_nic"
                    value="{{ $sales->customer_nic }}">
            </div>
        </div>

        <div class="form-group col-md-3" id="customer_phone_no_div">
            <label for="customer_phone_no">{{ __('Customer Phone No.') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-phone"></i>
                    </div>
                </div>
                <input type="text" class="form-control font-style" name="customer_phone_no" id="customer_phone_no"
                    value="{{ $sales->customer_phone_no }}">
            </div>
        </div>

        <div class="form-group col-md-3" id="customer_address_div">
            <label for="customer_address">{{ __('Customer Address') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-map"></i>
                    </div>
                </div>
                <input type="text" class="form-control font-style" name="customer_address" id="customer_address"
                    value="{{ $sales->customer_address }}">
            </div>
        </div>

        <div class="form-group col-md-3">
            <label for="introducer_id">{{ __('Salesman') }} <span class="text-danger">*</span></label>
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
    <div class="row">
        <div class="col-md-12">
            <div class="card repeater" id="cash_sale_repeater" data-value='{!! json_encode($sales->saleItems) !!}'>
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark font-weight-bold">
                        <i class="fas fa-shopping-cart text-primary mr-2"></i>{{ __('Sale Items') }}
                    </h5>
                    <div class="card-header-action">
                        <a href="javascript:void(0)" class="btn btn-icon icon-left btn-primary"
                            id="add_row">
                            <i class="fas fa-plus mr-1"></i>{{ __('Add Item') }}
                        </a>
                        <button type="button" data-repeater-create class="d-none" id="hidden_repeater_create"></button>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="col-sm-12 px-0" id="cash_sale_items">
                        <table class="table table-borderless p-0 w-100 mb-0" data-repeater-list="items" id="sortable-table" style="width: 100%;">
                            <tbody class="ui-sortable" data-repeater-item>
                                <tr class="cash-sale-item-row">
                                    <td class="p-0 border-0 pb-3">
                                        <div class="card border shadow-sm mb-0 cash-sale-item-card">
                                            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                                <span class="font-weight-bold text-dark">
                                                    <i class="fas fa-box text-primary mr-2"></i>{{ __('Item Details') }}
                                                </span>
                                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger" data-repeater-delete title="{{ __('Remove Item') }}">
                                                    <i class="fas fa-trash mr-1"></i> {{ __('Remove Item') }}
                                                </a>
                                            </div>
                                            <div class="card-body p-3">
                                                {{ Form::hidden('id', null, ['class' => 'form-control id']) }}
                                                <input type="hidden" name="pre_inventory_id" id="pre_inventory_id" class="pre_inventory_id" value="">
                                                <input type="hidden" name="inventory_id" id="inventory_id" class="inventory_id" value="">
                                                <input type="hidden" name="batch_id" id="batch_id" class="batch_id" value="">
                                                <input type="hidden" name="sale_item_id" id="sale_item_id" class="sale_item_id" value="">
                                                <input type="hidden" name="purchase_price" id="purchase_price" class="purchase_price" value="">
                                                <input type="hidden" name="pre_qty" id="pre_qty" class="pre_qty" value="">
                                                <input type="hidden" name="original_unit_price" class="original_unit_price" />

                                                <!-- Row 1: Barcode & Product Code, Available Qty, Quantity -->
                                                <div class="row">
                                                    <div class="col-md-3 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Barcode') }}</label>
                                                        <div id="barcode_input_container">
                                                            <i class="fas fa-barcode" id="barcode_icon"></i>
                                                            {{ Form::text('barcode', '', ['class' => 'form-control barcode_input', 'id' => 'barcode_input', 'placeholder' => 'Scan Barcode']) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5 form-group mb-2 cash-sale-product-cell">
                                                        <label class="font-weight-bold small mb-1">{{ __('Product') }} <span class="text-danger">*</span></label>
                                                        <select class="form-control customer-sel font-style product selectpicker"
                                                            name="product" id="product" required
                                                            data-empty-message="Please Select Product" data-focus="product"
                                                            data-live-search="true" data-container="body" data-width="100%">
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
                                                    <div class="col-md-2 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Available Quantity') }} <span class="text-danger">*</span></label>
                                                        {{ Form::number('available_quantity', null, ['class' => 'form-control available_quantity font-weight-bold bg-light text-center', 'readonly' => 'readonly', 'placeholder' => '0']) }}
                                                    </div>
                                                    <div class="col-md-2 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                                                        {{ Form::number('quantity', null, ['class' => 'form-control quantity font-weight-bold text-center', 'required' => 'required', 'data-empty-message' => 'Please Enter Quantity', 'data-focus' => 'quantity', 'placeholder' => '1']) }}
                                                    </div>
                                                </div>

                                                <!-- Row 2: Purchasing Price, Gross Amount, Discount, Net Amount -->
                                                <div class="row">
                                                    <div class="col-md-3 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Purchasing Price') }} <span class="text-danger">*</span></label>
                                                        {{ Form::number('unit_price', null, ['class' => 'form-control unit_price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Purchasing Price', 'data-focus' => 'unit_price', 'placeholder' => '0.00']) }}
                                                    </div>
                                                    <div class="col-md-3 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Gross Amount') }} <span class="text-danger">*</span></label>
                                                        {{ Form::number('gross_amount', null, ['class' => 'form-control gross_amount bg-light', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Gross Amount', 'data-focus' => 'gross_amount', 'readonly' => 'readonly', 'placeholder' => '0.00']) }}
                                                    </div>
                                                    <div class="col-md-3 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Discount') }} <span class="text-danger">*</span></label>
                                                        {{ Form::number('discount', null, ['class' => 'form-control discount', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Discount', 'data-focus' => 'discount', 'placeholder' => '0.00']) }}
                                                    </div>
                                                    <div class="col-md-3 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Net Amount') }} <span class="text-danger">*</span></label>
                                                        {{ Form::number('net_amount', null, ['class' => 'form-control net_amount font-weight-bold bg-light text-primary', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Net Amount', 'data-focus' => 'net_amount', 'readonly' => 'readonly', 'placeholder' => '0.00']) }}
                                                    </div>
                                                </div>

                                                <!-- Row 3: Serial Card No, Warranty Card No, Warranty Period, Warranty Document -->
                                                <div class="row">
                                                    <div class="col-md-3 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Serial Card No') }}</label>
                                                        <select class="form-control serial_select" name="serial_card_no" id="serial_select" required>
                                                            <option value="">Select Serial Card No</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Warranty Card No') }}</label>
                                                        <select class="form-control warranty_select" name="warranty_card_no" id="warranty_select" required>
                                                            <option value="">Select Warranty Card No</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Warranty Period') }}</label>
                                                        {{ Form::number('warranty_period', null, ['class' => 'form-control warranty_period', 'step' => '1', 'placeholder' => 'Months']) }}
                                                        <input type="hidden" name="warranty_charges" class="warranty_charges" value="0">
                                                    </div>
                                                    <div class="col-md-4 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Warranty Document') }}</label>
                                                        <input type="file" class="form-control form-input warranty_document" name="warranty_document" style="overflow: hidden;" />
                                                    </div>
                                                </div>

                                                <!-- Row 4: Additional Warranty Description -->
                                                <div class="row">
                                                    <div class="col-md-12 form-group mb-0">
                                                        <label class="font-weight-bold small mb-1">{{ __('Additional Warranty Description') }}</label>
                                                        {{ Form::textarea('additional_warranty_description', null, ['class' => 'form-control additional_warranty_description', 'rows' => '2', 'style' => 'resize: vertical; min-height: 50px;', 'placeholder' => 'Enter additional warranty description if any...']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Full Total of Payments block -->
                        <div class="card bg-white border shadow-sm p-3 mt-2 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-dark font-weight-bold">
                                    <i class="fas fa-calculator mr-2 text-primary"></i>{{ __('Total of Payments') }}:
                                </h5>
                                <h4 class="mb-0 text-primary font-weight-bold">
                                    <span>{{ \Auth::user()->currencySymbol() }}</span>
                                    <span class="fullTotalOfPayments"></span>
                                </h4>
                                <input type="hidden" name="fullTotalOfPayments" id="fullTotalOfPayments" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="advanced_div">
        {{-- <input type="hidden" name="advance_id" id="advance_id"> --}}
        <div class="form-group col-md-6">
            <label for="advance_amount">{{ __('Advance Amount') }} <span class="text-danger">*</span></label>
            <div class="input-group colorpickerinput">
                {{ Form::number('advance_amount', $sales->advance_amount ?? 0, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Insert Advance Amount', 'data-focus' => 'advance_amount', 'id' => 'advance_amount']) }}
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
            <label for="paid_amount">{{ __('Paid Amount') }} <span class="text-danger">*</span></label>
            <div class="input-group colorpickerinput">
                {{ Form::number('paid_amount', $sales->paid_amount, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Insert Paid Amount', 'data-focus' => 'paid_amount', 'id' => 'paid_amount']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="paid_amount_in_words">{{ __('Paid Amount In Words') }} <span class="text-danger">*</span></label>
            <textarea class="form-control paid_amount_in_words" id="paid_amount_in_words" name="paid_amount_in_words" rows="1" required
                style="height: 38px; min-height: 38px; resize: vertical;"
                data-empty-message="Please Insert Paid Amount In Words" data-focus="paid_amount_in_words">{{ $sales->paid_amount_in_words }}</textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="payment_method">{{ __('Payment Method') }} <span class="text-danger">*</span></label>
            <select class="form-control payment_method" name="payment_method" id="payment_method" required
                data-empty-message="Please select Payment Method" data-focus="payment_method">
                <option value="">Select Payment Method</option>
                <option @if ($sales->payment_method == 'CASH') selected @endif value="CASH">Cash</option>
                <option @if ($sales->payment_method == 'CHEQUE') selected @endif value="CHEQUE">Cheque</option>
                <option @if ($sales->payment_method == 'CARD') selected @endif value="CARD">Card</option>
                <option @if ($sales->payment_method == 'ONLINEPAYMENT') selected @endif value="ONLINEPAYMENT">Online Payment</option>
            </select>
        </div>

        <div class="form-group col-md-6 cheque_number_div">
            <label for="realised_date">{{ __('Realised Date') }} <span class="text-danger">*</span></label>
            <div class="input-group">
                {{ Form::text('realised_date', $sales->cheque_realised_date, ['class' => 'form-control datepicker', 'required' => 'required', 'id' => 'realised_date']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-12 cheque_number_div">
            <label for="cheque_number">{{ __('Cheque Number') }}</label>
            {{ Form::text('cheque_number', $sales->cheque_number, ['class' => 'form-control cheque_number', 'id' => 'cheque_number']) }}
        </div>

        <div class="form-group col-md-6" id="card_type_div">
            <input type="hidden" name="card_expence_id" id="card_expence_id"
                value="{{ $sales->card_expence_id }}">
            <label for="card_type">{{ __('Card Type') }}</label>
            <select class="form-control card_type" name="card_type" id="card_type">
                <option value="">Select Card Type</option>
                <option @if ($sales->card_type == 1) selected @endif value="1">Credit Card</option>
                <option @if ($sales->card_type == 2) selected @endif value="2">Debit Card</option>
            </select>
        </div>

        <div class="form-group col-md-6" id="card_charge_amount_div">
            <label for="card_charge_amount">{{ __('Card Charges') }}</label>
            {{ Form::number('card_charge_amount', $sales->card_charge_amount, ['class' => 'form-control card_charge_amount', 'step' => '0.01', 'id' => 'card_charge_amount']) }}
        </div>

        <div class="form-group col-md-6" id="total_amount_div">
            <label for="total_amount">{{ __('Total Amount') }}</label>
            {{ Form::number('total_amount', $sales->total_amount, ['class' => 'form-control total_amount', 'step' => '0.01', 'readonly' => 'readonly', 'id' => 'total_amount']) }}
        </div>

        <div class="form-group col-md-6">
            <label for="bank">{{ __('Company Bank') }} <span class="text-danger">*</span></label>
            {{ Form::select('bank', $banks, $sales->bank_id, ['class' => 'form-control font-style', 'required' => 'required', 'data-empty-message' => 'Please Select a Company Bank', 'data-focus' => 'bank', 'id' => 'bank']) }}
        </div>

        <div class="form-group col-md-6">
            <label for="traveling_charges">{{ __('Transport Charges') }}</label>
            <div class="input-group colorpickerinput">
                {{ Form::number('traveling_charges', sprintf('%.2f', $sales->traveling_charges), ['class' => 'form-control price', 'step' => '0.01', 'placeholder' => '0.00', 'id' => 'traveling_charges']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="button" class="btn btn-primary" id="create_btn">{{ __('Edit') }}</button>
    </div>
</form>

<script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>

<script>
    var value = {!! json_encode($sales->saleItems) !!};
    var $repeaterContainer = $("#cash_sale_repeater");
    if ($repeaterContainer.length) {
        var $dragAndDrop = $repeaterContainer.find("tbody").sortable({
            handle: '.sort-handler'
        });
        var $repeater = $repeaterContainer.repeater({
            initEmpty: false,
            defaultValues: {
                'status': 1
            },
            show: function() {
                var newItem = $(this);
                // Clean up any cloned stock batch dropdown
                newItem.find('.sale-batch-group').remove();
                // Reset inputs for newly added items
                newItem.find('.available_quantity').val('');
                newItem.find('.quantity').val(1).removeAttr('max');
                newItem.find('.gross_amount, .discount, .net_amount, .purchase_price, .hire_price').val('');
                newItem.find('.batch_id, .inventory_id, .sale_item_id').val('');
                newItem.removeData('raw_remain_qty');

                newItem.find('.bootstrap-select').each(function () {
                    var select = $(this).children('select.selectpicker');
                    select.removeData('selectpicker').removeClass('bs-select-hidden');
                    $(this).replaceWith(select);
                });
                newItem.find('select.selectpicker').val('').selectpicker();
                newItem.show();

                var file_uploads = newItem.find('input.multi');
                if (file_uploads.length) {
                    newItem.find('input.multi').MultiFile({
                        max: 3,
                        accept: 'png|jpg|jpeg',
                        max_size: 2048
                    });
                }
            },
            hide: function(deleteElement) {
                var totalItems = $('#cash_sale_items [data-repeater-item]').length;
                if (totalItems <= 1) {
                    if (typeof AppMessages !== 'undefined' && AppMessages.warning) {
                        AppMessages.warning('At least one item is required in the sale.');
                    } else {
                        alert('At least one item is required in the sale.');
                    }
                    return;
                }

                AppMessages.confirm('Are you sure you want to delete this element?', function() {
                    deleteElement();
                    calculatePaidAmount();
                    if (typeof refreshAllAvailableStock === 'function') {
                        refreshAllAvailableStock();
                    }
                    if (window.SaleBatches && typeof SaleBatches.syncAllRows === 'function') {
                        SaleBatches.syncAllRows();
                    }
                    if (window.SaleWarranty && typeof SaleWarranty.refreshAll === 'function') {
                        SaleWarranty.refreshAll();
                    }
                });
            },
            ready: function(setIndexes) {
                $dragAndDrop.on('drop', setIndexes);
            },
            isFirstItemUndeletable: false
        });
        if (typeof value != 'undefined' && value.length != 0) {
            $repeater.setList(value);
            console.log(value);
            var fulltotal = 0;
            for (var i = 0; i < value.length; i++) {
                var remainQty = 0;
                if (value[i].inventory != null) {
                    remainQty = value[i].inventory.remain_qty;
                }
                var availableQty = parseFloat(remainQty) + parseFloat(value[i].quantity);
                var tr = $('#sortable-table [name="items[' + i + '][id]"]').closest('tr');



                tr.find('.pre_inventory_id').val(value[i].inventory_id);
                tr.find('.inventory_id').val(value[i].inventory_id);
                tr.find('.purchase_price').val(parseFloat(value[i].inventory.purchase_price).toFixed(2));
                tr.find('.sale_item_id').val(value[i].id);
                tr.find('.barcode_input').val(value[i].products.barcode);
                // tr.find('.product').append('<option value="'+value[i].product_id+'">'+value[i].products.barcode+' - '+value[i].products.name+'</option>');
                tr.find('.product').val(value[i].product_id);
                tr.find('.unit_price').val(parseFloat(value[i].unit_price).toFixed(2));
                tr.find('.original_unit_price').val(value[i].unit_price);
                tr.find('.available_quantity').val(availableQty);
                tr.find('.pre_qty').val(value[i].quantity);
                tr.find('.quantity').val(value[i].quantity);
                tr.find('.gross_amount').val(parseFloat(value[i].gross_amount).toFixed(2));
                tr.find('.discount').val(parseFloat(value[i].discount).toFixed(2));
                tr.find('.net_amount').val(parseFloat(value[i].net_amount).toFixed(2));

                if (value[i].warranty) {
                    tr.find('.product_warranty_id').val(value[i].product_warranty_id);
                }

                tr.find('.warranty_period').val(value[i].warranty_period);
                tr.find('.warranty_charges').val(value[i].warranty_charges);

                fulltotal += parseFloat(value[i].net_amount);
            }
            $('#fullTotalOfPayments').val(fulltotal);
            $('.fullTotalOfPayments').html(parseFloat(fulltotal).toFixed(2));
        }

    }

    var sales = <?php echo json_encode($sales); ?>;

    $(document).ready(function() {
        $('.selectpicker').selectpicker();
        console.log(sales, "sales");

        if (sales.customer_type == 1) {
            $('#customer_name_div').hide();
            $('#customer_phone_no_div').hide();
            $('#customer_div').show();
        } else if (sales.customer_type == 2) {
            $('#customer_div').hide();
            $('#customer_name_div').show();
            $('#customer_phone_no_div').show();
        }

        if (sales.advance_amount != 0) {
            $('#advanced_div').show();
        } else {
            $('#advanced_div').hide();
        }

        var paymentMethod = sales.payment_method;
        changeMethod(paymentMethod);

        $('#card_type').val(sales.card_type).trigger('change');

        $('#paid_amount').val(parseFloat(sales.paid_amount).toFixed(2));
        paidAmountInWords(sales.paid_amount);

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

    $('body').on('change', '#customer_type', function() {
        var customerType = $(this).val();
        if (customerType == 1) {
            $('#customer_name_div').hide();
            $('#customer_phone_no_div').hide();
            $('#customer_div').show(200);
        } else if (customerType == 2) {
            $('#customer_div').hide(200);
            $('#advanced_div').hide(200);
            $('#advance_amount').val(0);
            $('#customer_name_div').show(200);
            $('#customer_phone_no_div').show(200);
        } else {
            $('#customer_div').hide(200);
            $('#customer_name_div').hide(200);
            $('#customer_phone_no_div').hide(200);
            $('#advanced_div').hide(200);
            $('#advance_amount').val(0);
        }
    });

    function getOtherRowsSelectedQty(productId, batchId, excludeEl) {
        var total = 0;
        var excludeItem = excludeEl ? $(excludeEl).closest('[data-repeater-item]')[0] : null;

        $('#cash_sale_items [data-repeater-item]').each(function() {
            if (excludeItem && this === excludeItem) {
                return;
            }
            var row = $(this);
            var p = row.find('select.product').val() || row.find('.inventory_id').val();
            if (p && String(p) === String(productId)) {
                var rowBatch = row.find('.sale-batch-select').val() || row.find('.inventory_id').val() || row.find('.batch_id').val();
                if (batchId && rowBatch) {
                    if (String(batchId) === String(rowBatch)) {
                        total += parseFloat(row.find('.quantity').val()) || 0;
                    }
                } else {
                    total += parseFloat(row.find('.quantity').val()) || 0;
                }
            }
        });
        return total;
    }

    function refreshAllAvailableStock() {
        $('#cash_sale_items [data-repeater-item]').each(function() {
            var item = $(this);
            var prodId = item.find('select.product').val();
            if (prodId) {
                var rawStock = item.data('raw_remain_qty');
                if (rawStock === undefined) {
                    rawStock = item.find('tr').data('raw_remain_qty');
                }
                if (rawStock !== undefined && rawStock !== null && rawStock !== '') {
                    rawStock = parseFloat(rawStock) || 0;
                    var batchId = item.find('.sale-batch-select').val() || item.find('.inventory_id').val() || item.find('.batch_id').val();
                    var otherSelected = getOtherRowsSelectedQty(prodId, batchId, item);
                    var netAvail = Math.max(0, rawStock - otherSelected);
                    item.find('.available_quantity').val(netAvail);
                    item.find('.quantity').attr('max', netAvail);
                }
            }
        });
        if (window.SaleBatches && typeof SaleBatches.syncAllRows === 'function') {
            SaleBatches.syncAllRows();
        }
    }

    $('body').on('change', '.product', function(event) {
        var product = $(this);
        var productId = product.val();
        $.ajax({
            url: "{{ url('sale/get_details') }}/" + productId,
            method: 'GET',
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                if (data.length > 0) {
                    product.closest("tr").find(".inventory_id").val(data[0].inventory_id);
                    product.closest("tr").find(".purchase_price").val(parseFloat(data[0]
                        .purchase_price).toFixed(2));
                    product.closest("tr").find(".unit_price").val(parseFloat(data[0].sale_price)
                        .toFixed(2));
                    product.closest("tr").find(".original_unit_price").val(parseFloat(data[0]
                        .sale_price).toFixed(2));
                    product.closest("tr").find(".available_quantity").val(data[0].remain_qty);
                    product.closest("tr").find(".quantity").val(1);
                    product.closest("tr").find(".gross_amount").val(parseFloat(data[0].sale_price)
                        .toFixed(2));
                    product.closest("tr").find(".discount").val(0);
                    product.closest("tr").find(".net_amount").val(parseFloat(data[0].sale_price)
                        .toFixed(2));
                    product.closest("tr").find(".warranty_period").val(data[0].warranty_period);
                    product.closest("tr").find(".additional_warranty_description").val(data[0]
                        .additional_warranty_description);

                    calculatePaidAmount();

                } else {
                    product.val('');
                    product.closest("tr").find(".inventory_id").val('');
                    product.closest("tr").find(".purchase_price").val('');
                    product.closest("tr").find(".unit_price").val('');
                    product.closest("tr").find(".original_unit_price").val('');
                    product.closest("tr").find(".available_quantity").val('');
                    product.closest("tr").find(".quantity").val('');
                    product.closest("tr").find(".gross_amount").val('');
                    product.closest("tr").find(".discount").val('');
                    product.closest("tr").find(".net_amount").val('');
                    product.closest("tr").find(".warranty_period").val('');
                    product.closest("tr").find(".additional_warranty_description").html('');

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

    $('form').submit(function(e) {
        $(':disabled').each(function(e) {
            $(this).removeAttr('disabled');
        })
    });

    // Barcode Scan
    $('.modal').on('shown.bs.modal', function() {
        $(".barcode_input").focus();
    });

    $('body').on('keyup', '.barcode_input', function(event) {
        if (event.keyCode == 13) {
            var barcode = $(this);
            var barcodeId = barcode.val();
            $.ajax({
                url: "{{ url('sale/product_id_by_barcode') }}/" + barcodeId,
                method: 'GET',
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    barcode.closest('tr').find('.product').val(data[0].id).trigger('change');
                }
            });
        }
    });
    $('body').off('click.cashSaleAddRowEdit', '#add_row').on('click.cashSaleAddRowEdit', '#add_row', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var canAdd = true;
        var errorMsg = '';
        var items = $('#cash_sale_items [data-repeater-item]');
        var seenSerials = {};

        items.each(function(idx) {
            var item = $(this);
            var prod = item.find('select.product, select[name*="product"]').val() || item.find('.inventory_id, input[name*="inventory_id"]').val();
            var hasDesc = item.find('.barcode_description').val();
            var batchSelect = item.find('.sale-batch-select');
            var qty = parseFloat(item.find('.quantity').val()) || 0;
            var avail = parseFloat(item.find('.available_quantity').val()) || 0;
            var serial = item.find('select.serial_select').val() || item.find('.selected-warranty-unit-serial').val() || item.find('#selected_serial_number').val();

            if (!prod && !hasDesc) {
                canAdd = false;
                errorMsg = 'Please select a product for Item #' + (idx + 1) + ' before adding a new item.';
                return false;
            }
            if (batchSelect.length && batchSelect.is(':visible') && !batchSelect.val()) {
                canAdd = false;
                errorMsg = 'Please select a Stock Batch for Item #' + (idx + 1) + ' before adding a new item.';
                return false;
            }
            if (avail <= 0) {
                canAdd = false;
                errorMsg = 'Item #' + (idx + 1) + ' has no available stock (Available Quantity: 0). You cannot add more items.';
                return false;
            }
            if (qty <= 0) {
                canAdd = false;
                errorMsg = 'Please enter a valid quantity for Item #' + (idx + 1) + '.';
                return false;
            }
            if (qty > avail) {
                canAdd = false;
                errorMsg = 'Item #' + (idx + 1) + ' quantity exceeds available stock (' + avail + ').';
                return false;
            }
            if (serial) {
                if (seenSerials[serial]) {
                    canAdd = false;
                    errorMsg = 'Duplicate Serial Card No (' + serial + ') found in Item #' + (idx + 1) + '. Each item must have a unique serial number.';
                    return false;
                }
                seenSerials[serial] = true;
                if (qty !== 1) {
                    canAdd = false;
                    errorMsg = 'Item #' + (idx + 1) + ' has a Serial Card No. Serialized items can only have a quantity of 1.';
                    return false;
                }
            }
        });

        if (!canAdd) {
            Swal.fire({
                icon: 'warning',
                title: 'Cannot Add Item',
                text: errorMsg
            });
            return false;
        }

        var hiddenBtn = document.getElementById('hidden_repeater_create');
        if (hiddenBtn) {
            hiddenBtn.click();
        } else {
            $('#hidden_repeater_create').trigger('click');
        }

        var barInputs = $('body').find('.barcode_input');
        if (barInputs.length > 0) {
            var lastBarInput = barInputs.last();
            lastBarInput.focus();
        }

        $('.selectpicker').selectpicker();

        if (typeof refreshAllAvailableStock === 'function') {
            refreshAllAvailableStock();
        }
        if (window.SaleBatches && typeof SaleBatches.syncAllRows === 'function') {
            SaleBatches.syncAllRows();
        }
        if (window.SaleWarranty && typeof SaleWarranty.refreshAll === 'function') {
            SaleWarranty.refreshAll();
        }
    });

    $('body').on('change', '#customer', function() {
        var custId = $(this).val();
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
        $('#total_amount').val(parseFloat(totalAmounts).toFixed(2));
        paidAmountInWords(totalAmounts);
    });

    $('body').on('input', '.unit_price', function() {
        var unitPricetVal = rtnUnitPriceWithWarrntyInsu(this);
        var quantVal = $(this).closest("tr").find(".quantity").val();
        var discountVal = $(this).closest("tr").find(".discount").val();
        var grossVal = parseFloat(unitPricetVal) * parseFloat(quantVal);
        $(this).closest("tr").find(".gross_amount").val(parseFloat(grossVal).toFixed(2));
        discount($(this), discountVal);
    });

    $('body').on('input', '.discount', function() {
        var discountVal = $(this).val();
        discount($(this), parseFloat(discountVal).toFixed(2));
    });

    $('body').on('input', '.quantity', function() {
        var quantityField = $(this);
        var quantity = quantityField.val();
        var availableQuantity = quantityField.closest("tr").find(".available_quantity").val();
        if (parseFloat(quantity) > parseFloat(availableQuantity)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'You Have No Available Quantity!'
            });
            quantityField.val(availableQuantity);
            quantity = availableQuantity;
        }

        var serialVal = quantityField.closest("tr").find('select.serial_select').val() || quantityField.closest("tr").find('.selected-warranty-unit-serial').val() || quantityField.closest("tr").find('#selected_serial_number').val();
        if (serialVal && parseFloat(quantity) > 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Serialized Item',
                text: 'Items with a Serial Card No can only have a quantity of 1.'
            });
            quantity = 1;
            quantityField.val(1);
        }

        var unitPrice = rtnUnitPriceWithWarrntyInsu(quantityField);
        var gross = parseFloat(unitPrice) * parseFloat(quantity);
        quantityField.closest("tr").find(".gross_amount").val(gross);

            var discountVal = quantityField.closest("tr").find(".discount").val();
            discount(quantityField, discountVal);
        }
    });

    $('body').on('input', '#paid_amount', function() {
        paidAmountInWords($(this).val());
    });

    function calculatePaidAmount() {
        var netAmounts = $('body').find('.net_amount');
        var totalNetAmounts = 0;
        for (var i = 0; i < netAmounts.length; i++) {
            if (netAmounts[i].value != '') {
                totalNetAmounts += parseFloat(netAmounts[i].value);
            }
        }
        $('.fullTotalOfPayments').html(parseFloat(totalNetAmounts).toFixed(2));
        $('#fullTotalOfPayments').val(totalNetAmounts);

        const paidAmount = totalNetAmounts - parseFloat($('#advance_amount').val());
        $('#paid_amount').val(parseFloat(paidAmount).toFixed(2));
        paidAmountInWords(totalNetAmounts - parseFloat($('#advance_amount').val()));
    }

    function paidAmountInWords(amount) {
        $("#paid_amount_in_words").html('');
        var words = '';
        words = toWords(amount);
        $("#paid_amount_in_words").html(words + "Only");
    }

    function discount(row, discountVal) {
        var grossAmountVal = row.closest("tr").find(".gross_amount").val();
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
        row.closest("tr").find(".net_amount").val(parseFloat(netAmount).toFixed(2));

        calculatePaidAmount();
    }

    $('#create_btn').click(function() {
        $('#sale_form').submit();
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

        var items = $(form).find('#cash_sale_items [data-repeater-item]');
        var seenSerials = {};

        items.each(function(idx) {
            var row = $(this);
            var prod = row.find('select.product').val() || row.find('.inventory_id').val();
            if (!prod) return;
            var batchSelect = row.find('.sale-batch-select');
            if (batchSelect.length && batchSelect.is(':visible') && !batchSelect.val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Batch Required',
                    text: 'Please select a Stock Batch for Item #' + (idx + 1) + '.'
                });
                isValid = false;
                return false;
            }
            var avail = parseFloat(row.find('.available_quantity').val()) || 0;
            var qty = parseFloat(row.find('.quantity').val()) || 0;
            if (avail <= 0 || qty > avail) {
                Swal.fire({
                    icon: 'error',
                    title: 'Insufficient Stock',
                    text: 'Item #' + (idx + 1) + ' has insufficient available stock (Available: ' + avail + ', Requested: ' + qty + ').'
                });
                isValid = false;
                return false;
            }
            var serial = row.find('select.serial_select').val() || row.find('.selected-warranty-unit-serial').val() || row.find('#selected_serial_number').val();
            if (serial) {
                if (seenSerials[serial]) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Serial Number',
                        text: 'Duplicate Serial Card No (' + serial + ') found in Item #' + (idx + 1) + '. Each item must have a unique serial number.'
                    });
                    isValid = false;
                    return false;
                }
                seenSerials[serial] = true;
                if (qty !== 1) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Serial Quantity',
                        text: 'Item #' + (idx + 1) + ' has a Serial Card No. Serialized items can only have a quantity of 1.'
                    });
                    isValid = false;
                    return false;
                }
            }
        });

        if (!isValid) {
            $(".se-pre-con").css({"display": "none"});
            return false;
        }

        return isValid;
    }

    $('body').on('input', '.warranty_charges', function() {
        $(this).closest("tr").find(".unit_price").trigger('input');
    });

    function rtnUnitPriceWithWarrntyInsu(ele) {
        var warranty_charges = parseFloat($(ele).closest("tr").find(".warranty_charges").val()) || 0;
        var unit_price = parseFloat($(ele).closest("tr").find(".original_unit_price").val());

        const net_amount = unit_price + warranty_charges;
        return net_amount;
    }

    $(document).on('focusout', '#customer_nic', function () {
        var currentValue = $(this).val();
        $(this).val(currentValue.toUpperCase());
    });
</script>
