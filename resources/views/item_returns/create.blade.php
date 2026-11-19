<style>
    #commonModal .modal-body {
        overflow: visible;
    }
</style>
<div class="setting-tab">
    <ul class="nav nav-pills nav-fill" id="myTab3" role="tablist">
        @if(Gate::check('create item return'))
            <li class="nav-item">
                <a class="nav-link @if(Gate::check('create item return')) active @endif" id="contact-tab4" data-toggle="tab" href="#account-adjustment" role="tab" aria-controls="" aria-selected="false">{{__('Account Adjustment')}}</a>
            </li>
        @endif
        @if(Gate::check('create stock adjustment'))
            <li class="nav-item">
                <a class="nav-link @if(!Gate::check('create item return') && Gate::check('create stock adjustment')) active @endif" id="contact-tab4" data-toggle="tab" href="#stock-adjustment" role="tab" aria-controls="" aria-selected="false">{{__('Stock Adjustment')}}</a>
            </li>
        @endif
        @if(Gate::check('create item return') || Gate::check('create stock adjustment'))
            <li class="nav-item">
                <a class="nav-link" id="supplier-return-tab" data-toggle="tab" href="#supplier-return" role="tab" aria-selected="false">{{__('Supplier Return')}}</a>
            </li>
        @endif
    </ul>

    <div class="tab-content" id="myTabContent2">
        @if(Gate::check('create item return'))
            <div class="tab-pane fade fade  @if(Gate::check('create item return')) show active @endif" id="account-adjustment" role="tabpanel" aria-labelledby="profile-tab3">
                <div class="company-setting-wrap">
                    <div class="card-body px-2 pt-2 pb-1">
                        {{Form::open(array('url'=>'item_return/store','method'=>'POST','class'=>'repeater'))}}
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    {{Form::label('date',__('Date')) }}
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                        </div>
                                        {{Form::text('date','',array('class'=>'form-control datepicker','required'=>'required'))}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('adjustment_preset', __('Adjustment Option (Preset)')) }}
                                    <select class="form-control customer-sel font-style" id="adjustment_preset">
                                        <option value="">-- Custom / Manual Selection --</option>
                                        <option value="cash_sale">Cash Sale</option>
                                        <option value="hire_sale">Hire Sale</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('invoice_id', __('Invoice')) }}
                                    <div class="input-group">
                                        <input type="hidden" name="customer_id" class="customer_id" id="customer_id">
                                        <select class="form-control customer-sel font-style invoice_id selectpicker" data-live-search="true" name="invoice_id" id="invoice_id" required>
                                            <option value="">-- Select Invoice --</option>
                                            @foreach ($invoices as $invoice)
                                                <option value="{{ $invoice->id }}">{{ $invoice->invoice_code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('product_id', __('Item')) }}
                                    <div class="input-group">
                                        <input type="hidden" name="inventory_id" class="inventory_id" id="inventory_id">
                                        <select class="form-control customer-sel font-style product_id" name="product_id" id="product_id" required>
                                            <option data-inventory="" value="">-- Select Item --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('serial_no', __('Serial No.')) }}
                                    <input type="text" name="serial_no" class="form-control customer-sel font-style serial_no" id="serial_no" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('account_id', __('Account Name')) }}
                                    <div class="input-group">
                                        <select class="form-control customer-sel font-style account_id selectpicker" data-live-search="true" title="-- Select Account Name --" name="account_id" id="account_id" required>
                                            <option value="">-- Select Account Name --</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {{ Form::label('debit_credit', __('Status')) }}
                                    <div class="input-group">
                                        <select class="form-control customer-sel font-style debit_credit" name="debit_credit" id="debit_credit" required>
                                            <option value="">--Select Status--</option>
                                            <option value="0">Credit</option>
                                            <option value="1">Debit</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('related_account_id', __('Related Account Name')) }}
                                    <div class="input-group">
                                        <select class="form-control customer-sel font-style related_account_id selectpicker" data-live-search="true" title="-- Select Account Name --" name="related_account_id" id="related_account_id" required>
                                            <option value="">-- Select Account Name --</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {{ Form::label('related_debit_credit', __('Status')) }}
                                    <div class="input-group">
                                        <select class="form-control customer-sel font-style related_debit_credit" name="related_debit_credit" id="related_debit_credit" required>
                                            <option value="">--Select Status--</option>
                                            <option value="0">Credit</option>
                                            <option value="1">Debit</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{ Form::label('amount', __('Amount')) }}
                                    {{ Form::number('amount',null, array('class' => 'form-control amount','required'=>'required','step'=>'0.01', 'data-empty-message'=>"Please Enter Amount", 'data-focus'=>"amount")) }}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {{ Form::label('return_qty', __('Return Qty')) }}
                                    <input type="number" name="return_qty" id="return_qty" class="form-control" value="1" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{ Form::label('item_condition', __('Item Condition')) }}
                                    <select name="item_condition" id="item_condition" class="form-control customer-sel font-style" required>
                                        <option value="good">Good (Restock)</option>
                                        <option value="damaged_resell">Damaged - Resell (Lower Price)</option>
                                        <option value="damaged_scrap">Damaged - Scrap (Remove)</option>
                                        <option value="account_only">Account Adjustment Only</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('narration', __('Narration')) }}
                                    {{Form::text('narration',null,array('class'=>'form-control narration','required'=>'required'))}}
                                </div>
                            </div>
                        </div>
                        <div class="row" id="resell_price_row" style="display: none;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="resell_sale_price">{{ __('New Sale Price (Damaged)') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="resell_sale_price" id="resell_sale_price" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="resell_hire_price">{{ __('New Hire Price (Optional)') }}</label>
                                    <input type="number" name="resell_hire_price" id="resell_hire_price" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            {{-- <div class="col-md-12">
                                <div class="card repeater">
                                    <div class="card-header">
                                        <div class="card-header-action">
                                            <a href="#" data-repeater-create="" class="btn btn-icon icon-left btn-primary" id="add_row" data-toggle="modal" data-target="#add-bank">
                                                <i class="fas fa-plus"></i>{{__('Add Entry')}}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <div class="col-sm-12">
                                            <table class="table table-striped" data-repeater-list="items" id="sortable-table">
                                                <thead>
                                                    <tr>
                                                        <th>{{__('Invoice')}}</th>
                                                        <th>{{__('Item')}}</th>
                                                        <th>{{__('Account Name')}}</th>
                                                        <th>{{__('Status')}} </th>
                                                        <th>{{__('Related Account Name')}}</th>
                                                        <th>{{__('Status')}} </th>
                                                        <th>{{__('Narration')}} </th>
                                                        <th>{{__('Amount')}}</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="ui-sortable" data-repeater-item>
                                                    <tr>
                                                        <td>
                                                            <select class="form-control customer-sel font-style invoice_id" name="invoice_id" required>
                                                                <option value="">-- Select Invoice --</option>
                                                                @foreach ($invoices as $invoice)
                                                                    <option value="{{ $invoice->id }}">{{ $invoice->invoice_code }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="inventory_id" class="inventory_id">
                                                            <select class="form-control customer-sel font-style product_id" name="product_id" required>
                                                                <option data-inventory="" value="">-- Select Item --</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control customer-sel font-style account_id" name="account_id" id="account_id" required>
                                                                <option value="">-- Select Account Name --</option>
                                                                @foreach ($accounts as $account)
                                                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control customer-sel font-style debit_credit" name="debit_credit" id="debit_credit" required>
                                                                <option value="">--Select Status--</option>
                                                                <option value="0">Credit</option>
                                                                <option value="1">Debit</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control customer-sel font-style related_account_id" name="related_account_id" id="related_account_id" required>
                                                                <option value="">-- Select Account Name --</option>
                                                                @foreach ($accounts as $account)
                                                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control customer-sel font-style related_debit_credit" name="related_debit_credit" id="related_debit_credit" required>
                                                                <option value="">--Select Status--</option>
                                                                <option value="0">Credit</option>
                                                                <option value="1">Debit</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            {{Form::text('narration',null,array('class'=>'form-control narration'))}}
                                                        </td>
                                                        <td>
                                                            {{ Form::number('amount',null, array('class' => 'form-control amount','required'=>'required','step'=>'0.01', 'data-empty-message'=>"Please Enter Amount", 'data-focus'=>"amount")) }}
                                                        </td>
                                                        <td>
                                                            <a href="#" class="fas fa-trash" data-repeater-delete></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-md-12 mt-2 d-flex justify-content-end" style="gap: 8px;">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
                                {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
        @endif
        @if(Gate::check('create stock adjustment'))
            <div class="tab-pane fade fade @if(!Gate::check('create item return') && Gate::check('create stock adjustment')) show active @endif" id="stock-adjustment" role="tabpanel" aria-labelledby="profile-tab3">
                <div class="company-setting-wrap">
                    <div class="card-body px-2 pt-2 pb-1">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    {{Form::label('current_date',__('Date')) }}
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                        </div>
                                        {{Form::text('current_date','',array('class'=>'form-control datepicker','required'=>'required'))}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('product', __('Product')) }}
                                    <div class="input-group">
                                        <select class="form-control customer-sel font-style" name="product" id="product">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->brands->name.' - '.$product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('product_with_price', __('Product with Prices')) }}
                                    <div class="input-group">
                                        <select class="form-control customer-sel font-style inventory_products" name="product_with_price" id="product_with_price">
                                            <option data-purchase="" data-sale="" value="">Select Product with purchase price</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{Form::label('purchase_price',__('Purchase Price')) }}
                                    {{Form::number('purchase_price','',array('class'=>'form-control', 'placeholder'=>'0.00','readonly'=>'readonly'))}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{Form::label('sale_price',__('Sale Price')) }}
                                    {{Form::number('sale_price','',array('class'=>'form-control', 'placeholder'=>'0.00','readonly'=>'readonly'))}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{Form::label('quantity',__('Remaining Quantity')) }}
                                    {{Form::number('quantity','',array('class'=>'form-control', 'placeholder'=>'0','readonly'=>'readonly'))}}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{Form::label('description',__('Description')) }}
                                    {{Form::textarea('description','',array('class'=>'form-control'))}}
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <input type="hidden" name="inventory_id" id="inventory_id">
                            <div class="col-md-5 text-right" style="margin-top: 32px;">
                                <button type="button" class="btn btn btn-danger" id="deductStock">{{__('Deduct From Stock')}}</button>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group text-center">
                                    <label for="changed_quantity"><b>Change In Quantity</b></label>
                                    <input type="number" class="form-control text-center" name="changed_quantity" id="changed_quantity" placeholder="0" style="font-weight:bold;">
                                    {{-- {{Form::number('changed_quantity','',array('class'=>'form-control text-center', 'placeholder'=>'0', 'style'=>'font-weight:bold;'))}} --}}
                                </div>
                            </div>
                            <div class="col-md-5" style="margin-top: 32px;">
                                <button type="button" class="btn btn btn-success" id="addStock">{{__('Add To Stock')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(Gate::check('create item return') || Gate::check('create stock adjustment'))
            <div class="tab-pane fade" id="supplier-return" role="tabpanel" aria-labelledby="supplier-return-tab">
                <div class="company-setting-wrap">
                    <div class="card-body px-2 pt-2 pb-1">
                        {{ Form::open(['route' => 'item_return.supplier_store', 'method' => 'POST']) }}
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="sr_date">{{ __('Date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="sr_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="sr_vender_id">{{ __('Supplier') }} <span class="text-danger">*</span></label>
                                <select name="vender_id" id="sr_vender_id" class="form-control customer-sel font-style selectpicker" data-live-search="true" title="-- {{ __('Select Supplier') }} --" required>
                                    <option value="">-- {{ __('Select Supplier') }} --</option>
                                    @foreach ($venders ?? [] as $vender)
                                        <option value="{{ $vender->id }}">{{ $vender->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="sr_grn_id">{{ __('GRN') }} <span class="text-danger">*</span></label>
                                <select name="grn_id" id="sr_grn_id" class="form-control customer-sel font-style selectpicker" data-live-search="true" title="-- {{ __('Select GRN') }} --" required disabled>
                                    <option value="">-- {{ __('Select GRN') }} --</option>
                                    @foreach ($grns ?? [] as $g)
                                        <option value="{{ $g->id }}" data-vender="{{ $g->vender_id }}">{{ $g->supplier_invoice_number ?? Auth::user()->grnNumberFormat($g->id) }} ({{ optional($g->vender)->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="sr_product_id">{{ __('Product') }} <span class="text-danger">*</span></label>
                                <select name="product_id" id="sr_product_id" class="form-control customer-sel font-style selectpicker" data-live-search="true" title="-- {{ __('Select Product') }} --" required disabled>
                                    <option value="">-- {{ __('Select Product') }} --</option>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="sr_quantity">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="sr_quantity" value="1" min="1" class="form-control" required disabled>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="sr_serial_no">{{ __('Serial No.') }}</label>
                                <input type="text" name="serial_no" id="sr_serial_no" list="sr_serial_suggestions" class="form-control" placeholder="{{ __('Type or select Serial No.') }}" autocomplete="off" disabled>
                                <datalist id="sr_serial_suggestions"></datalist>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="sr_amount">{{ __('Return Amount') }} <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="sr_amount" step="0.01" min="0" class="form-control" required placeholder="0.00" disabled>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="sr_narration">{{ __('Reason / Narration') }}</label>
                                <input type="text" name="narration" id="sr_narration" class="form-control" placeholder="{{ __('Reason for returning to supplier') }}">
                            </div>
                            <div class="col-md-12 mt-2 d-flex justify-content-end" style="gap: 8px;">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('Submit Supplier Return') }}</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script src="{{asset('assets/js/jquery.repeater.min.js')}}"></script>

<script>
    // var selector = "body";
    //     if ($(selector + " .repeater").length) {
    //         var $dragAndDrop = $("body .repeater tbody").sortable({
    //             handle: '.sort-handler'
    //         });
    //         var $repeater = $(selector + ' .repeater').repeater({
    //             initEmpty: false,
    //             defaultValues: {
    //                 // 'status': 1
    //             },
    //             show: function () {
    //                 $(this).slideDown();
    //                 var file_uploads = $(this).find('input.multi');
    //                 if (file_uploads.length) {
    //                     $(this).find('input.multi').MultiFile({
    //                         max: 3,
    //                         accept: 'png|jpg|jpeg',
    //                         max_size: 2048
    //                     });
    //                 }
    //             },
    //             hide: function (deleteElement) {
    //                 if (confirm('Are you sure you want to delete this element?')) {
    //                     $(this).slideUp(deleteElement);
    //                     $(this).remove();

    //                     // var inputs = $(".amount");
    //                     // var subTotal = 0;
    //                     // for (var i = 0; i < inputs.length; i++) {
    //                     //     subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
    //                     // }
    //                     // $('.subTotal').html(subTotal.toFixed(2));
    //                     // $('.totalAmount').html(subTotal.toFixed(2));
    //                     // finalCalculation();

    //                 }
    //             },
    //             ready: function (setIndexes) {
    //                 $dragAndDrop.on('drop', setIndexes);
    //             },
    //             isFirstItemUndeletable: true
    //         });
    //         var value = $(selector + " .repeater").attr('data-value');
    //         if (typeof value != 'undefined' && value.length != 0) {
    //             value = JSON.parse(value);
    //             $repeater.setList(value);
    //         }

    //     }

    $(document).ready(function(){
        $('.selectpicker').selectpicker();

        $('#product_id').prop('disabled', true);
        $('#product_with_price').prop('disabled', true);
        $('#deductStock').prop('disabled', true);
        $('#addStock').prop('disabled', true);
    });

    // Supplier Return: Sequential Selection, Cascading Filters & Auto-Fill
    var srAllGrnOptions = null;
    function getSrAllGrns() {
        if (!srAllGrnOptions) {
            srAllGrnOptions = $('#sr_grn_id option').clone();
        }
        return srAllGrnOptions;
    }

    function resetSrProductAndDetails() {
        $('#sr_product_id').empty().append('<option value="">-- {{ __("Select Product") }} --</option>').prop('disabled', true).selectpicker('refresh');
        resetSrDetailsOnly();
    }

    function resetSrDetailsOnly() {
        $('#sr_quantity').val(1).removeAttr('max').prop('disabled', true);
        $('#sr_serial_no').val('').prop('disabled', true);
        $('#sr_serial_suggestions').empty();
        $('#sr_amount').val('').prop('disabled', true);
    }

    function loadSupplierReturnData(fetchSerialsOnly) {
        var venderId = $('#sr_vender_id').val() || '';
        var grnId = $('#sr_grn_id').val() || '';
        var productId = $('#sr_product_id').val() || '';

        $.getJSON("{{ route('item_return.supplier_filter_data') }}", {
            vender_id: venderId,
            grn_id: grnId,
            product_id: productId
        }).done(function (res) {
            if (!fetchSerialsOnly) {
                var prodSelect = $('#sr_product_id');
                prodSelect.empty().append('<option value="">-- {{ __("Select Product") }} --</option>');
                (res.products || []).forEach(function (p) {
                    prodSelect.append(
                        $('<option>')
                            .val(p.id)
                            .text(p.label)
                            .attr('data-avail', p.avail)
                            .attr('data-cost', p.cost)
                    );
                });
                prodSelect.prop('disabled', !(res.products && res.products.length > 0)).selectpicker('refresh');
            }

            var datalist = $('#sr_serial_suggestions').empty();
            var serials = res.serials || [];
            serials.forEach(function (sn) {
                datalist.append($('<option>').attr('value', sn));
            });
            if (fetchSerialsOnly && serials.length > 0) {
                $('#sr_serial_no').val(serials[0]);
            }
        });
    }

    $('body').on('change', '#sr_vender_id', function () {
        var venderId = $(this).val();
        var allOpts = getSrAllGrns();
        var grnSelect = $('#sr_grn_id');
        grnSelect.empty().append('<option value="">-- {{ __("Select GRN") }} --</option>');
        if (venderId) {
            allOpts.each(function () {
                var opt = $(this);
                if (opt.val() && String(opt.attr('data-vender')) === String(venderId)) {
                    grnSelect.append(opt.clone());
                }
            });
            grnSelect.prop('disabled', false);
        } else {
            grnSelect.prop('disabled', true);
        }
        grnSelect.val('').selectpicker('refresh');
        resetSrProductAndDetails();
    });

    $('body').on('change', '#sr_grn_id', function () {
        var grnId = $(this).val();
        resetSrProductAndDetails();
        if (grnId) {
            loadSupplierReturnData(false);
        }
    });

    $('body').on('change', '#sr_product_id', function () {
        var prodId = $(this).val();
        if (!prodId) {
            resetSrDetailsOnly();
            return;
        }
        var selectedOpt = $('option:selected', this);
        var avail = parseInt(selectedOpt.attr('data-avail') || '0', 10);
        var cost = parseFloat(selectedOpt.attr('data-cost') || '0');
        var qtyInput = $('#sr_quantity');

        qtyInput.prop('disabled', false).val(1);
        $('#sr_serial_no').prop('disabled', false).val('');
        $('#sr_amount').prop('disabled', false);

        if (avail > 0) {
            qtyInput.attr('max', avail);
        } else {
            qtyInput.removeAttr('max');
        }
        $('#sr_amount').val((cost * 1).toFixed(2));
        loadSupplierReturnData(true);
    });

    $('body').on('input change', '#sr_quantity', function () {
        var selectedOpt = $('#sr_product_id option:selected');
        var cost = parseFloat(selectedOpt.attr('data-cost') || '0');
        var q = parseInt($(this).val() || '1', 10);
        if (q > 0) {
            $('#sr_amount').val((cost * q).toFixed(2));
        }
    });

    $('body').on('change', '#item_condition', function(){
        if ($(this).val() === 'damaged_resell') {
            $('#resell_price_row').slideDown(150);
            $('#resell_sale_price').prop('required', true);
        } else {
            $('#resell_price_row').slideUp(150);
            $('#resell_sale_price').prop('required', false).val('');
            $('#resell_hire_price').val('');
        }
    });

    $('body').on('change', '#adjustment_preset', function(){
        var preset = $(this).val();
        var presets = {
            cash_sale: { account: '4', dc: '1', related: '1',  rdc: '0' },
            hire_sale: { account: '4', dc: '1', related: '12', rdc: '0' }
        };

        if (presets[preset]) {
            var p = presets[preset];
            $('#account_id').selectpicker('val', p.account);
            $('#debit_credit').val(p.dc);
            $('#related_account_id').selectpicker('val', p.related);
            $('#related_debit_credit').val(p.rdc);
        }
    });

    $('body').on('change', '.debit_credit', function(){
        var statusVlue = $(this).val();
        var relatedStatus = $('.related_debit_credit');
        if(statusVlue == 0){
            relatedStatus.val(1);
        } else if(statusVlue == 1){
            relatedStatus.val(0);
        } else {
            relatedStatus.val('');
        }
    });

    $('body').on('change', '.related_debit_credit', function(){
        var relatedStatus = $(this).val();
        var statusVlue = $('.debit_credit');
        if(relatedStatus == 0){
            statusVlue.val(1);
        } else if(relatedStatus == 1){
            statusVlue.val(0);
        } else {
            statusVlue.val('');
        }
    });

    $('body').on('change', '#invoice_id', function(){
        var invoiceId = $(this).val();
        $('#inventory_id').val('');
        $('#serial_no').val('');
        $('#amount').val('');
        if(invoiceId != ''){
            $.ajax({
                url:"{{ url('item_return/invoice_search') }}/"+invoiceId,
                method:'GET',
                dataType:'json',
                success: function (data, textStatus, jqXHR){
                    if(data != null && data.sales){
                        $('#customer_id').val(data.customer_id);
                        if (Number(data.sales.sale_type) === 0) {
                            $('#adjustment_preset').val('cash_sale').trigger('change');
                        } else if (Number(data.sales.sale_type) === 1) {
                            $('#adjustment_preset').val('hire_sale').trigger('change');
                        }
                        $('#product_id').html("");
                        $('#product_id').append('<option data-inventory="" data-serial="" data-amount="" value="">-- Select Item --</option>');

                            if(data.sales.modal_type == 1){
                                var serialNo = (data.sales.hire_purchase_details && data.sales.hire_purchase_details.warranty && data.sales.hire_purchase_details.warranty.serial_number)
                                    ? data.sales.hire_purchase_details.warranty.serial_number
                                    : '';
                                var saleAmount = (data.sales.net_amount !== null && data.sales.net_amount !== undefined)
                                    ? data.sales.net_amount
                                    : (data.sales.unit_price || '');
                                $('#product_id').append('<option data-inventory="' + data.sales.inventory_id + '" data-serial="' + serialNo + '" data-amount="' + saleAmount + '" value="' + data.sales.product_id + '">' + data.sales.products.name + '</option>');
                            } else if(data.sales.modal_type == 2){
                                for (var i = 0; i < data.sales.sale_items.length; i++) {
                                    var itemSerial = (data.sales.sale_items[i].warranty && data.sales.sale_items[i].warranty.serial_number)
                                        ? data.sales.sale_items[i].warranty.serial_number
                                        : '';
                                    var itemAmount = (data.sales.sale_items[i].net_amount !== null && data.sales.sale_items[i].net_amount !== undefined)
                                        ? data.sales.sale_items[i].net_amount
                                        : (data.sales.sale_items[i].unit_price || '');
                                    $('#product_id').append('<option data-inventory="' + data.sales.sale_items[i].inventory_id + '" data-serial="' + itemSerial + '" data-amount="' + itemAmount + '" value="' + data.sales.sale_items[i].product_id + '">' + data.sales.sale_items[i].products.name + '</option>');
                                }
                            }
                        $('#product_id').prop('disabled', false);
                    }
                    else{
                        $('#customer_id').val('');
                        $('#product_id').html("");
                        $('#product_id').append('<option data-inventory="" data-serial="" data-amount="" value="">Nothing Selected</option>');
                        $('#product_id').prop('disabled', true);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                }
            });
        } else {
            $('#adjustment_preset').val('');
            $('#product_id').prop('disabled', true);
            $('#product_id').val("");
            $('#customer_id').val("");
        }

    });

    $('body').on('change', '#product_id', function(){
        var selectedOption = $('option:selected', this);
        var inventoryId = selectedOption.attr('data-inventory') || '';
        var serialNo = selectedOption.attr('data-serial') || '';
        var itemAmount = selectedOption.attr('data-amount') || '';
        $('#inventory_id').val(inventoryId);
        $('#serial_no').val(serialNo);
        $('#amount').val(itemAmount !== '' ? parseFloat(itemAmount).toFixed(2) : '');
    });


    $('body').on('change', '#product', function(){
        var productId = $(this).val();
        $.ajax({
            url:"{{ url('general_journal/inventory_search') }}/"+productId,
            method:'GET',
            dataType:'json',
            success: function (data, textStatus, jqXHR){
                if(data.length > 0){
                    $('#product_with_price').html("");
                    $('#product_with_price').append('<option value = "">Select Product with purchase price</option>');
                        for (var i = 0; i < data.length; i++) {
                            $('#product_with_price').append('<option data-purchase="' + data[i].purchase_price + '" data-sale="' + data[i].sale_price + '" value="' + data[i].product_id + '">' + data[i].products.name + ' - '+ data[i].purchase_price +'</option>');
                        }

                    $('#product_with_price').prop('disabled', false);
                }
                else{
                    $('#product_with_price').html("");
                    $('#product_with_price').append('<option value = "">Nothing Selected</option>');
                    $('#product_with_price').prop('disabled', true);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
            }
        });
    });

    $('body').on('change', '#product_with_price', function(){
        var productId = $(this).val();
        if(productId !=''){
            var purchasePrice = $('#product_with_price').find('option:selected').attr('data-purchase');
        var salePrice = $('#product_with_price').find('option:selected').attr('data-sale');
        $.ajax({
            url:"{{ url('general_journal_detail/get_inventory_details') }}/"+productId+"/"+purchasePrice+"/"+salePrice,
            method:'GET',
            dataType:'json',
            success: function (data, textStatus, jqXHR){
                if(data != null){
                    $('#inventory_id').val(data.id);
                    $('#purchase_price').val(data.purchase_price);
                    $('#sale_price').val(data.sale_price);
                    $('#quantity').val(data.remain_qty);
                    $('#deductStock').prop('disabled', false);
                    $('#addStock').prop('disabled', false);
                }
                else{
                    $('#inventory_id').val('');
                    $('#purchase_price').val('');
                    $('#sale_price').val('');
                    $('#quantity').val('');
                    $('#deductStock').prop('disabled', true);
                    $('#addStock').prop('disabled', true);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
            }
        });
        } else {
            $('#inventory_id').val('');
            $('#purchase_price').val('');
            $('#sale_price').val('');
            $('#quantity').val('');
            $('#deductStock').prop('disabled', true);
            $('#addStock').prop('disabled', true);
        }
    });

    $('body').on('click', '#deductStock', function(){
        var remainQty = $('#quantity').val();
        var newQty = $('#changed_quantity').val();
        var description = $('#description').val();
        var command = 'deduct';
        if(newQty == ''){
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Enter Quantity!'
                });
            return this;
        }
        if(parseFloat(newQty) > parseFloat(remainQty)){
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Enter Valid Quantity!'
                });
            return this;
        }
        if(description == ''){
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Enter Description!'
                });
            return this;
        }
        changeStock(newQty, description, command);
    });

    $('body').on('click', '#addStock', function(){
        var remainQty = $('#quantity').val();
        var newQty = $('#changed_quantity').val();
        var description = $('#description').val();
        var command = 'add';
        if(newQty == ''){
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Enter Quantity!'
                });
            return this;
        }
        if(description == ''){
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Enter Description!'
                });
            return this;
        }
        changeStock(newQty, description, command);
    });

    function changeStock(newQty, description, command){
        var date = $('#current_date').val();
        var inventryId = $('#inventory_id').val();

        $.ajax({
            url:"{{ url('general_journal/adjust_stock') }}",
            method:'POST',
            dataType:'json',
            data:{date:date, inventory_id:inventryId, new_qty:newQty, description:description, command:command, "_token": "{{ csrf_token() }}"},
            success: function (data, textStatus, jqXHR){
                if(data == 'success'){
                        Swal.fire({
                            icon: 'success',
                            title: 'Submitted',
                            text: 'Stock Adjustment submitted for Admin approval!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                    $('#inventory_id').val('');
                    $('#purchase_price').val('');
                    $('#sale_price').val('');
                    $('#quantity').val('');
                    $('#changed_quantity').val('');
                    $('#description').val('');
                    $('#product').val('');
                    $('#product_with_price').val('');
                    $('#deductStock').prop('disabled', true);
                    $('#addStock').prop('disabled', true);
                } else if(data == 'error'){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something Went Wrong...!'
                    });
                }
            }
        });
    }

//     $(document).ready(function(){
//       var i=1;
//      $(document).on("click", "#add_row", function(){
//         var datepic = "#date" + i;
//         $('#addr'+i).html("<td>"+ (i+1) +"</td><td><input id = 'date"+
//           i+"'  name='name" + i +
//           "' type='text' placeholder='Name' class='form-control input-md'/>"
//           + "</td><td><input  name='mail" +
//           i + "' type='text' placeholder='Mail'  class='form-control input-md'>"
//           +"</td><td><select class='input category'><option selected>d</option></select>"
//           +"</td>");

//         $(datepic).datepicker();

//          $('#tab_logic').append('<tr id="addr'+(i+1)+'"></tr>');
//             i++;
//      });
</script>





    {{-- <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('debit',__('Debit')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-money-bill-alt"></i>
                    </div>
                </div>
            {{Form::number('debit','',array('class'=>'form-control','step'=>'0.01'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('account_id',__('Account Name')) }}
            {{Form::select('account_id',$accounts, null,array('class'=>'form-control customer-sel font-style','required'=>'required'))}}
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('credit',__('Credit')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-money-bill-alt"></i>
                    </div>
                </div>
            {{Form::number('credit','',array('class'=>'form-control','step'=>'0.01'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('narration',__('Narration')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                {{Form::text('narration',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
</div> --}}

