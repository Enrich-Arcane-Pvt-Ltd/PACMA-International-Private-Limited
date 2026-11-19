@include('sale.batch-controls', ['batchCash' => true])
<style>
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

    /* Card Item Form Groups - align labels at bottom so all inputs start on the same vertical line */
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

    /* Batch Dropdown inside Item card */
    .sale-batch-group {
        margin-top: 6px;
    }

    .sale-batch-group label {
        font-weight: 600;
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 4px;
        display: block;
    }

    .sale-batch-group .sale-batch-select {
        height: 36px !important;
        min-height: 36px !important;
        font-size: 12.5px !important;
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

    .cash-sale-product-menu {
        max-width: calc(100vw - 24px) !important;
    }

    .cash-sale-product-menu .dropdown-item,
    .cash-sale-product-menu .dropdown-item .text {
        white-space: normal !important;
        overflow-wrap: anywhere;
    }

    #barcode_input_container {
        border-radius: 4px;
        display: flex;
        background: #3abaf4;
        height: 38px;
    }

    #barcode_icon {
        display: flex;
        align-items: center;
        padding: 0 13px;
        color: #ffffff;
    }

    #barcode_input {
        overflow: hidden;
        border-radius: 0px 4px 4px 0px;
        border: 1px solid #3abaf4;
        height: 38px !important;
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

<form method="post" action="{{ route('sale.create') }}" onsubmit="return validateForm(this)" id="sale_form"
    enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="sale_type" id="sale_type" value="0">
    <input type="hidden" name="modal_type" id="modal_type" value="2">
    <div class="row">
        <div class="form-group col-md-3">
            <label for="date_of_sale">{{ __('Date of Sale') }} <span class="text-danger">*</span></label>
            <div class="input-group">
                {{ Form::text('date_of_sale', '', ['class' => 'form-control datepicker', 'required' => 'required', 'data-empty-message' => 'Please Select Date of Sale', 'data-focus' => 'date_of_sale', 'id' => 'date_of_sale']) }}
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
                <option value="1">Regular Customer</option>
                <option value="2">New Customers</option>
            </select>
        </div>

        <div class="form-group col-md-3" id="customer_div">
            <label for="customer">{{ __('Customer') }} <span class="text-danger">*</span></label>
            <select class="form-control customer-sel font-style selectpicker" data-live-search="true" name="customer"
                id="customer">
                <option value="">Select Customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->nic_no . ' - ' . $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-3" id="customer_title_div">
            <label for="customer_title">{{ __('Title') }}</label>
            <select class="form-control font-style" name="customer_title" id="customer_title"
                data-empty-message="Please Select Title" data-focus="customer_title">
                <option value="">Select title</option>
                <option value="1">Mr</option>
                <option value="2">Mrs</option>
                <option value="2">Miss</option>
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
                <input type="text" class="form-control font-style" name="customer_name" id="customer_name" pattern="(?!\b\w\b)([A-Za-z]+ ?)+">
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
                <input type="text" class="form-control font-style" name="customer_nic" id="customer_nic">
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
                <input type="text" class="form-control font-style" name="customer_phone_no" id="customer_phone_no">
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
                <input type="text" class="form-control font-style" name="customer_address" id="customer_address">
            </div>
        </div>

        <div class="form-group col-md-3">
            <label for="introducer_id">{{ __('Salesman') }} <span class="text-danger">*</span></label>
            <select class="form-control customer-sel font-style selectpicker" data-live-search="true" name="introducer_id" id="introducer_id"
                data-empty-message="Please Select Salesman" data-focus="introducer_id" required>
                <option value="">Select Salesman</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card repeater" id="cash_sale_repeater">
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
                                                <!-- Row 1: Product Code, Description, Available Qty, Quantity -->
                                                <div class="row">
                                                    <div class="col-md-5 form-group mb-2 cash-sale-product-cell">
                                                        <label class="font-weight-bold small mb-1">{{ __('Product Code') }} <span class="text-danger">*</span></label>
                                                        <input type="hidden" name="inventory_id" id="inventory_id" class="inventory_id">
                                                        <input type="hidden" name="batch_id" id="batch_id" class="batch_id">
                                                        <input type="hidden" name="purchase_price" id="purchase_price" class="purchase_price">
                                                        <select class="form-control customer-sel font-style product selectpicker"
                                                            name="product" id="product" required
                                                            data-empty-message="Please Select Product" data-focus="product"
                                                            data-live-search="true" data-container="body" data-width="100%"
                                                            data-size="auto" data-live-search-placeholder="Search by Category, Name or Code"
                                                            data-show-subtext="true">
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
                                                    <div class="col-md-3 form-group mb-2 cash-sale-description-cell">
                                                        <label class="font-weight-bold small mb-1">{{ __('Product Description') }} <span class="text-danger">*</span></label>
                                                        {{ Form::text('barcode_description', '', ['class' => 'form-control barcode_description', 'id' => 'barcode_description', 'readonly' => 'readonly', 'placeholder' => 'Description' ]) }}
                                                    </div>
                                                    <div class="col-md-2 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Available Quantity') }} <span class="text-danger">*</span></label>
                                                        {{ Form::number('available_quantity', null, ['class' => 'form-control available_quantity font-weight-bold bg-light text-center', 'readonly' => 'readonly', 'placeholder' => '0']) }}
                                                    </div>
                                                    <div class="col-md-2 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                                                        {{ Form::number('quantity', null, ['class' => 'form-control quantity font-weight-bold text-center', 'required' => 'required', 'data-empty-message' => 'Please Enter Quantity', 'data-focus' => 'quantity', 'min' => '1', 'placeholder' => '1']) }}
                                                    </div>
                                                </div>

                                                <!-- Row 2: Purchasing Price, Gross Amount, Discount, Net Amount -->
                                                <div class="row">
                                                    <div class="col-md-3 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Purchasing Price') }} <span class="text-danger">*</span></label>
                                                        <input type="hidden" name="original_unit_price" class="original_unit_price" />
                                                        <input type="hidden" name="unit_price" class="unit_price" />
                                                        {{ Form::number('hire_price', null, ['class' => 'form-control hire_price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Enter Purchasing Price', 'data-focus' => 'hire_price', 'placeholder' => '0.00']) }}
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
                                                        <select class="form-control serial_select" name="serial_card_no" id="serial_select" required
                                                                data-empty-message="Please Select Serial Card No">
                                                            <option value="">Select Serial Card No</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 form-group mb-2">
                                                        <label class="font-weight-bold small mb-1">{{ __('Warranty Card No') }}</label>
                                                        <select class="form-control warranty_select" name="warranty_card_no" id="warranty_select" required
                                                                data-empty-message="Please Select Warranty Card No">
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
                                    <span class="fullTotalOfPayments">0.00</span>
                                </h4>
                                <input type="hidden" name="fullTotalOfPayments" id="fullTotalOfPayments" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="advanced_div">
        <input type="hidden" name="advance_id" id="advance_id">
        <div class="form-group col-md-6">
            <label for="advance_amount">{{ __('Advance Amount') }} <span class="text-danger">*</span></label>
            <div class="input-group colorpickerinput">
                {{ Form::number('advance_amount', 0, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Insert Advance Amount', 'data-focus' => 'advance_amount', 'id' => 'advance_amount']) }}
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
                {{ Form::number('paid_amount', null, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01', 'data-empty-message' => 'Please Insert Paid Amount', 'data-focus' => 'paid_amount', 'id' => 'paid_amount']) }}
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
                data-empty-message="Please Insert Paid Amount In Words" data-focus="paid_amount_in_words"></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="payment_method">{{ __('Payment Method') }} <span class="text-danger">*</span></label>
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
            <label for="realised_date">{{ __('Realised Date') }} <span class="text-danger">*</span></label>
            <div class="input-group">
                {{ Form::text('realised_date', '', ['class' => 'form-control datepicker', 'required' => 'required', 'id' => 'realised_date']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-12 cheque_number_div">
            <label for="cheque_number">{{ __('Cheque Number') }}</label>
            {{ Form::text('cheque_number', null, ['class' => 'form-control cheque_number', 'id' => 'cheque_number']) }}
        </div>

        <div class="form-group col-md-6" id="card_type_div">
            <label for="card_type">{{ __('Card Type') }}</label>
            <select class="form-control card_type" name="card_type" id="card_type">
                <option value="">Select Card Type</option>
                <option value="1">Credit Card</option>
                <option value="2">Debit Card</option>
            </select>
        </div>

        <div class="form-group col-md-6" id="card_charge_amount_div">
            <label for="card_charge_amount">{{ __('Card Charges') }}</label>
            {{ Form::number('card_charge_amount', null, ['class' => 'form-control card_charge_amount', 'step' => '0.01', 'id' => 'card_charge_amount']) }}
        </div>

        <div class="form-group col-md-6" id="total_amount_div">
            <label for="total_amount">{{ __('Total Amount') }}</label>
            {{ Form::number('total_amount', null, ['class' => 'form-control total_amount', 'step' => '0.01', 'readonly' => 'readonly', 'id' => 'total_amount']) }}
        </div>

        <div class="form-group col-md-6">
            <label for="bank">{{ __('Company Bank') }} <span class="text-danger">*</span></label>
            {{ Form::select('bank', $banks, null, ['class' => 'form-control font-style', 'required' => 'required', 'data-empty-message' => 'Please Select a Company Bank', 'data-focus' => 'bank', 'id' => 'bank']) }}
        </div>

        <div class="form-group col-md-6">
            <label for="traveling_charges">{{ __('Transport Charges') }}</label>
            <div class="input-group colorpickerinput">
                {{ Form::number('traveling_charges', null, ['class' => 'form-control price', 'step' => '0.01', 'placeholder' => '0.00', 'id' => 'traveling_charges']) }}
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
        <button type="button" class="btn btn-primary" id="create_btn">{{ __('Create') }}</button>
    </div>
</form>

<script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>

<script>

    $('body').on('changed.bs.select', 'select.product', function () {
        $(this).closest('.bootstrap-select').find('> .dropdown-toggle')
            .attr('title', $(this).find('option:selected').text().trim());
    });
    $('body').on('shown.bs.select', 'select.product', function () {
        var picker = $(this).data('selectpicker');
        if (!picker) return;
        var width = Math.min(640, window.innerWidth - 24);
        picker.$menu.addClass('cash-sale-product-menu').css({width: width, minWidth: width});
        var container = picker.$menu.parent('.bs-container');
        if (container.length) {
            var left = Math.max(12, Math.min(container.offset().left, window.innerWidth - width - 12));
            container.css({width: width, left: left});
        }
    });

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
                // Reset inputs for newly added item
                newItem.find('.available_quantity').val('');
                newItem.find('.quantity').val(1).removeAttr('max');
                newItem.find('.gross_amount, .discount, .net_amount, .purchase_price, .hire_price').val('');
                newItem.find('.batch_id, .inventory_id').val('');
                newItem.removeData('raw_remain_qty');
                newItem.find('tr').removeData('raw_remain_qty');

                // Repeater clones Bootstrap Select markup; rebuild each new row's controls.
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

        var value = $repeaterContainer.attr('data-value');
        if (typeof value != 'undefined' && value.length != 0) {
            value = JSON.parse(value);
            $repeater.setList(value);
        }
    }

    $(document).ready(function() {
        $('.selectpicker').selectpicker();

        $('#advanced_div').hide();
        $('#customer_div').hide();
        $('#customer_name_div').hide();
        $('#customer_title_div').hide();
        $('#customer_nic_div').hide();
        $('#customer_phone_no_div').hide();
        $('#customer_address_div').hide();

        $('.cheque_number_div').hide();
        $('#card_type_div').hide();
        $('#card_charge_amount_div').hide();
        $('#total_amount_div').hide();
    });
    $('body').on('change', '#customer_type', function() {
        var customerType = $(this).val();
        if (customerType == 1) {
            $('#customer_name_div').hide();
            $('#customer_title_div').hide();
            $('#customer_nic_div').hide();
            $('#customer_phone_no_div').hide();
            $('#customer_address_div').hide();
            $('#customer_div').show(200);
        } else if (customerType == 2) {
            $('#customer_div').hide(200);
            $('#advanced_div').hide(200);
            $('#advance_amount').val(0);
            $('#customer_title_div').show(200);
            $('#customer_name_div').show(200);
            $('#customer_nic_div').show(200);
            $('#customer_phone_no_div').show(200);
            $('#customer_address_div').show(200);
        } else {
            $('#customer_div').hide(200);
            $('#customer_title_div').hide(200);
            $('#customer_name_div').hide(200);
            $('#customer_nic_div').hide(200);
            $('#customer_phone_no_div').hide(200);
            $('#customer_address_div').hide(200);
            $('#advanced_div').hide(200);
            $('#advance_amount').val(0);
        }
    });

    function getOtherRowsSelectedQty(productId, batchId, excludeEl) {
        var total = 0;
        var excludeItem = excludeEl ? $(excludeEl).closest('[data-repeater-item]')[0] : null;

        $('#cash_sale_items [data-repeater-item]').each(function() {
            if (excludeItem && this === excludeItem) {
                return; // Exclude the active row being calculated
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
        var currentTr = product.closest('tr');
        SaleWarranty.bind(currentTr, [], { preservePrices: true });
        var productId = product.val();
        if (!productId) {
            currentTr.data('raw_remain_qty', 0);
            currentTr.find(".barcode_description").val('');
            currentTr.find(".inventory_id").val('');
            currentTr.find(".batch_id").val('');
            currentTr.find(".purchase_price").val('');
            currentTr.find(".hire_price").val('');
            currentTr.find(".unit_price").val('');
            currentTr.find(".original_unit_price").val('');
            currentTr.find(".available_quantity").val('');
            currentTr.find(".quantity").val('');
            currentTr.find(".gross_amount").val('');
            currentTr.find(".discount").val('');
            currentTr.find(".net_amount").val('');
            currentTr.find(".warranty_period").val('');
            currentTr.find(".additional_warranty_description").val('');
            refreshAllAvailableStock();
            calculatePaidAmount();
            return;
        }

        var rowIndex = $(this).attr('id') ? $(this).attr('id').split('_')[1] : '';
        var serialSelect = '#serial_select_' + rowIndex;
        var warrantySelect = '#warranty_select_' + rowIndex;

        $.ajax({
            url: "{{ url('sale/get_details') }}/" + productId,
            method: 'GET',
            cache: false,
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                console.log("AJAX success. Data received:", data);
                if (data.length > 0) {
                    var rawStock = parseFloat(data[0].remain_qty) || 0;
                    currentTr.data('raw_remain_qty', rawStock);
                    var otherSelected = getOtherRowsSelectedQty(productId, null, currentTr);
                    var netAvailable = rawStock - otherSelected;

                    if (netAvailable <= 0) {
                        product.val('').selectpicker('refresh');
                        currentTr.data('raw_remain_qty', 0);
                        currentTr.find(".barcode_description").val('');
                        currentTr.find(".inventory_id").val('');
                        currentTr.find(".batch_id").val('');
                        currentTr.find(".purchase_price").val('');
                        currentTr.find(".hire_price").val('');
                        currentTr.find(".unit_price").val('');
                        currentTr.find(".original_unit_price").val('');
                        currentTr.find(".available_quantity").val(0);
                        currentTr.find(".quantity").val('');
                        currentTr.find(".gross_amount").val('');
                        currentTr.find(".discount").val('');
                        currentTr.find(".net_amount").val('');
                        currentTr.find(".warranty_period").val('');
                        currentTr.find(".additional_warranty_description").val('');
                        calculatePaidAmount();
                        refreshAllAvailableStock();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Out of Stock',
                            text: 'All available stock for this product has already been allocated in this invoice.'
                        });
                        return;
                    }

                    currentTr.find(".barcode_description").val(data[0].products.name);
                    currentTr.find(".inventory_id").val(data[0].inventory_id);
                    currentTr.find(".batch_id").val(data[0].inventory_id);
                    currentTr.find(".purchase_price").val(parseFloat(data[0].purchase_price).toFixed(2));
                    currentTr.find(".hire_price").val(parseFloat(data[0].hire_price).toFixed(2));
                    currentTr.find(".unit_price").val(parseFloat(data[0].sale_price).toFixed(2));
                    currentTr.find(".original_unit_price").val(parseFloat(data[0].sale_price).toFixed(2));
                    currentTr.find(".available_quantity").val(netAvailable);
                    currentTr.find(".quantity").val(1).attr('max', netAvailable);
                    currentTr.find(".gross_amount").val(parseFloat(data[0].hire_price).toFixed(2));
                    currentTr.find(".discount").val(data[0].hire_price - data[0].sale_price);
                    currentTr.find(".net_amount").val(parseFloat(data[0].sale_price).toFixed(2));
                    currentTr.find(".warranty_period").val(data[0].products.warranty_period);
                    currentTr.find(".additional_warranty_description").val(data[0].products.additional_warranty_description);

                    calculatePaidAmount();
                    refreshAllAvailableStock();
                } else {
                    product.val('').selectpicker('refresh');
                    currentTr.data('raw_remain_qty', 0);
                    currentTr.find(".barcode_description").val('');
                    currentTr.find(".inventory_id").val('');
                    currentTr.find(".batch_id").val('');
                    currentTr.find(".purchase_price").val('');
                    currentTr.find(".hire_price").val('');
                    currentTr.find(".unit_price").val('');
                    currentTr.find(".original_unit_price").val('');
                    currentTr.find(".available_quantity").val('');
                    currentTr.find(".quantity").val('');
                    currentTr.find(".gross_amount").val('');
                    currentTr.find(".discount").val('');
                    currentTr.find(".net_amount").val('');
                    currentTr.find(".warranty_period").val('');
                    currentTr.find(".additional_warranty_description").val('');
                    refreshAllAvailableStock();

                    Swal.fire({
                        icon: 'error',
                        title: 'Out of stock',
                        text: 'The selected product is out of stock',
                    });
                }

                // Warranty data
                $.ajax({
                    url: "{{ url('sale/get_warranty_serials') }}/" + productId,
                    method: 'GET',
                    cache: false,
                    dataType: 'json',
                    success: function(warrantyData) {
                        SaleWarranty.bind(currentTr, warrantyData || [], { preservePrices: true });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Error fetching warranty serials:", textStatus, errorThrown);
                    }
                });
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
                    barcode.closest('tr').find('select.product').val(data[0].id).trigger('change');
                }
            });
        }
    });

    $('body').on('click', '#add_row', function(e) {
        e.preventDefault();

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

    $('body').on('input', '.hire_price', function() {
        var unitPrice = $(this).closest("tr").find(".unit_price").val();
        var hirePrice = $(this).val();
        var quantVal = $(this).closest("tr").find(".quantity").val();
        var discountVal = hirePrice - unitPrice;
        var grossVal = parseFloat(hirePrice) * parseFloat(quantVal);
        $(this).closest("tr").find(".gross_amount").val(grossVal);
        discount($(this), discountVal);
    });

    $('body').on('input', '.discount', function() {
        var discountVal = $(this).val();
        discount($(this), discountVal);
    });

    $('body').on('input', '.quantity', function() {
        var quantityField = $(this);
        var tr = quantityField.closest("tr");
        var item = quantityField.closest("[data-repeater-item]");
        var productId = tr.find("select.product").val();
        var quantity = parseFloat(quantityField.val()) || 0;
        var rawStock = tr.data('raw_remain_qty');
        if (rawStock === undefined) {
            rawStock = item.data('raw_remain_qty');
        }
        if (rawStock === undefined) {
            rawStock = parseFloat(tr.find(".available_quantity").val()) || 0;
            tr.data('raw_remain_qty', rawStock);
            item.data('raw_remain_qty', rawStock);
        }
        var batchId = tr.find('.sale-batch-select').val() || tr.find('.inventory_id').val() || tr.find('.batch_id').val();
        var otherSelected = getOtherRowsSelectedQty(productId, batchId, tr);
        var netAvailable = Math.max(0, rawStock - otherSelected);

        if (quantity > netAvailable) {
            Swal.fire({
                icon: 'error',
                title: 'Quantity Exceeded',
                text: 'Available quantity exceeded! Only ' + netAvailable + ' unit(s) available for this item.'
            });
            quantity = netAvailable > 0 ? netAvailable : 1;
            quantityField.val(quantity);
        }

        var serialVal = tr.find('select.serial_select').val() || tr.find('.selected-warranty-unit-serial').val() || tr.find('#selected_serial_number').val();
        if (serialVal && quantity > 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Serialized Item',
                text: 'Items with a Serial Card No can only have a quantity of 1.'
            });
            quantity = 1;
            quantityField.val(1);
        }
        tr.find(".available_quantity").val(netAvailable);
        quantityField.attr('max', netAvailable);

        var hirePrice = tr.find(".hire_price").val();
        var unitPrice = rtnUnitPriceWithWarrntyInsu(quantityField);
        var gross = parseFloat(hirePrice) * parseFloat(quantity);
        tr.find(".gross_amount").val(gross);

        var originalUnitPrice = tr.find(".unit_price").val();
        var newDiscount = parseFloat(gross) - (parseFloat(originalUnitPrice) * parseFloat(quantity));

        discount(quantityField, newDiscount);
        refreshAllAvailableStock();
    });

    $('body').on('change', 'input[type="file"].warranty_document, input.warranty_document', function() {
        var tr = $(this).closest('tr');
        var prod = tr.find('select.product').val() || tr.find('.inventory_id').val();
        var qty = parseFloat(tr.find('.quantity').val()) || 0;
        if (!prod || qty <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Product Required',
                text: 'Please select a valid product with available stock before attaching a warranty document.'
            });
            $(this).val('');
            return false;
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
        var roundedTotal = Math.round(totalNetAmounts);
        $('.fullTotalOfPayments').html(roundedTotal.toFixed(2));
        $('#fullTotalOfPayments').val(roundedTotal.toFixed(2));

        const paidAmount = Math.round(totalNetAmounts - parseFloat($('#advance_amount').val() || 0));
        $('#paid_amount').val(paidAmount.toFixed(2));
        paidAmountInWords(paidAmount);
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
        }
        if (grossAmountVal != '') {
            grossAmount = grossAmountVal;
        }
        var netAmount = Math.round(parseFloat(grossAmount) - parseFloat(discount));
        row.closest("tr").find(".net_amount").val(netAmount.toFixed(2));
        row.closest("tr").find(".discount").val(Math.round(discount).toFixed(2));


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

    document.getElementById('customer_name').addEventListener('keyup', function() {
    // Define the pattern as a regular expression
    const pattern = /^(?!\b\w\b)([A-Za-z]+ ?)+$/;
    // const initialsPattern = /\b([A-Za-z]\s){2,}/;
    const customerNameDiv = document.getElementById('customer_name_div');

    // Check if the input value matches the pattern
    if (pattern.test(this.value)) {
        // If valid, add success style and remove any error message
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
        customerNameDiv.querySelector('.invalid-feedback')?.remove();
    } else {
        // If invalid, add error style and display an error message
        this.classList.remove('is-valid');
        this.classList.add('is-invalid');

        // Show an error message if it doesn't already exist
        if (!customerNameDiv.querySelector('.invalid-feedback')) {
            const errorFeedback = document.createElement('div');
            errorFeedback.className = 'invalid-feedback';
            errorFeedback.innerText = 'Please enter a valid customer name (letters and spaces only, no single letters).';
            customerNameDiv.appendChild(errorFeedback);
        }
    }
});


    $('body').on('input', '.warranty_charges', function() {
        $(this).closest("tr").find(".unit_price").trigger('input');
    });

    function rtnUnitPriceWithWarrntyInsu(ele) {
        var warranty_charges = parseFloat($(ele).closest("tr").find(".warranty_charges").val()) || 0;
        var unit_price = parseFloat($(ele).closest("tr").find(".original_unit_price").val());

        const net_amount = unit_price + warranty_charges;
        return net_amount;
    }
    var productData = [];

    document.getElementById('traveling_charges').addEventListener('change', function (e) {
        let value = parseFloat(e.target.value).toFixed(2);  // Convert the value to float and format to 2 decimal places
        e.target.value = value;  // Set the formatted value back to the input
    });

    $(document).on('focusout', '#customer_nic', function () {
        var currentValue = $(this).val();
        $(this).val(currentValue.toUpperCase());
    });
</script>
