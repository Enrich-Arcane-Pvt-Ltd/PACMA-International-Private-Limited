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
</style>

<form method="post" class="repeater" action="{{ route('inventory.item_transfer') }}" onsubmit="return validateForm(this)" id="transfer_form">
    @csrf

    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('date_of_transfer', __('Date of Transfer')) }}
            <div class="input-group">
                {{ Form::text('date_of_transfer', '', array('class' => 'form-control datepicker','required'=>'required', 'data-empty-message'=>"Please Select Date of Sale", 'data-focus'=>"date_of_transfer")) }}
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('branch', __('Transfer To')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style" name="branch" id="branch" required
                data-empty-message="Please Select Branch" data-focus="branch">
                    <option value="">Select Branch</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card repeater">
                <div class="card-header">
                    <div class="card-header-action">
                        <a href="#" data-repeater-create="" class="btn btn-icon icon-left btn-primary" id="add_row" data-toggle="modal" data-target="#add-bank">
                            <i class="fas fa-plus"></i>{{__('Add Item')}}
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
                                    <th>{{__('Product Code')}}</th>
                                    <th>{{__('Product')}}</th>
                                    <th>{{__('Available Prices')}} </th>
                                    <th>{{__('Purchase Price')}} </th>
                                    <th>{{__('Sale Price')}} </th>
                                    <th>{{__('Available Quantity')}} </th>
                                    <th>{{__('Quantity')}}</th>
                                    <th>{{__('Amount')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="ui-sortable" data-repeater-item>
                                <tr>
                                    <td>
                                        <div id="barcode_input_container">
                                            <i class="fas fa-barcode" id="barcode_icon"></i>
                                            {{ Form::text('barcode', '', array('class'=>'form-control barcode_input', 'id'=>'barcode_input')) }}
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control customer-sel font-style product selectpicker" name="product" id="product" required
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
                                                    @endphp
                                                    <option data-sale="" data-id="" value="{{ $product->products->id }}"
                                                        data-tokens="{{ $catName }} {{ $subCatName }} {{ $prodName }} {{ $barcode }}"
                                                        data-subtext="{{ $barcode }}">
                                                        {{ $catPrefix }}{{ $prodName }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control customer-sel font-style inventory_products" name="inventory_products" id="inventory_products" required
                                            data-empty-message="Please Select Product With Available Prices" data-focus="product">
                                            <option data-id="" value="">Select Product with price</option>
                                        </select>
                                    </td>
                                    <td>
                                        {{ Form::number('purchase_price',null, array('class' => 'form-control purchase_price', 'readonly' => 'readonly')) }}
                                    </td>
                                    <td>
                                        {{ Form::number('sale_price',null, array('class' => 'form-control sale_price', 'readonly' => 'readonly')) }}
                                    </td>
                                    <td >
                                        {{ Form::number('available_quantity',null, array('class' => 'form-control available_quantity', 'readonly' => 'readonly')) }}
                                    </td>
                                    <td>
                                        {{ Form::number('quantity',null, array('class' => 'form-control quantity','required'=>'required', 'data-empty-message'=>"Please Enter Quantity", 'data-focus'=>"quantity")) }}
                                    </td>
                                    <td>
                                        {{ Form::number('net_amount',null, array('class' => 'form-control net_amount', 'readonly' => 'readonly')) }}
                                    </td>
                                    <td>
                                        <a href="#" class="fas fa-trash" data-repeater-delete></a>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5"></td>
                                <td colspan="2" class="text-right">
                                    <input type="hidden" name="total_amount" id="total_amount">
                                    <strong>{{__('Total Amount')}} </strong>
                                </td>
                                <td class="text-right total_amount">0.00</td>
                                <td></td>
                            </tr>

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        <button type="button" class="btn btn-primary" id="create_btn">{{__('Transfer')}}</button>
    </div>
</form>

<script src="{{asset('assets/js/jquery.repeater.min.js')}}"></script>
<script>
    var selector = "body";
        if ($(selector + " .repeater").length) {
            var $dragAndDrop = $("body .repeater tbody").sortable({
                handle: '.sort-handler'
            });
            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: false,
                defaultValues: {
                    'status': 1
                },
                show: function () {
                    $(this).slideDown();
                    var file_uploads = $(this).find('input.multi');
                    if (file_uploads.length) {
                        $(this).find('input.multi').MultiFile({
                            max: 3,
                            accept: 'png|jpg|jpeg',
                            max_size: 2048
                        });
                    }
                },
                hide: function (deleteElement) {
                    AppMessages.confirm('Are you sure you want to delete this element?', () => {
                        $(this).slideUp(deleteElement);
                        $(this).remove();

                        calculatePaidAmount();
                    });
                },
                ready: function (setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });
            var value = $(selector + " .repeater").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
            }

        }

    $(document).ready(function(){
    });

    // Barcode Scan
    $('.modal').on('shown.bs.modal', function() {
        $("#barcode_input").focus();
    });

    $('body').on('keyup','.barcode_input', function(event){
        if (event.keyCode == 13) {
            var barcode = $(this);
            var barcodeId = barcode.val();
            $.ajax({
                url:"{{ url('sale/product_id_by_barcode') }}/"+barcodeId,
                method: 'GET',
                dataType: 'json',
                success: function(data, textStatus, jqXHR){
                    barcode.closest('tr').find('.product').val(data[0].id).trigger('change');
                }
            });
        }
    });

    $('body').on('change', '.product', function(){
        var product = $(this);
        var productId = product.val();
        $.ajax({
            url:"{{ url('item/inventory_search') }}/"+productId,
            method:'GET',
            dataType:'json',
            success: function (data, textStatus, jqXHR){
                if(data.length > 0){
                    product.closest('tr').find('.inventory_products').html("");
                    product.closest('tr').find('.inventory_products').append('<option value = "">Select Product with price</option>');
                        for (var i = 0; i < data.length; i++) {
                            product.closest('tr').find('.inventory_products').append('<option data-id="' + data[i].product_id + '" data-sale="' + data[i].sale_price + '" value="' + data[i].purchase_price + '">' + data[i].products.name + ' - '+ data[i].purchase_price +'</option>');
                        }
                }
                else{
                    product.closest('tr').find('.inventory_products').html("");
                    product.closest('tr').find('.inventory_products').append('<option value = "">Nothing Selected</option>');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
            }
        });
    });

    $('body').on('change', '.inventory_products', function(){
        var inventoryProduct = $(this);
        // var inventoryId = inventoryProduct.val();
        var purchasePrice = inventoryProduct.val();
        var productId = inventoryProduct.find('option:selected').attr('data-id');
        var salePrice = inventoryProduct.find('option:selected').attr('data-sale');

            $.ajax({
                url:"{{ url('item/get_inventory_detail') }}/"+purchasePrice+"/"+salePrice+"/"+productId,
                method:'GET',
                dataType:'json',
                success: function (data, textStatus, jqXHR){
                    if(data != null){
                        inventoryProduct.closest('tr').find('.available_quantity').val(data.remain_qty);
                        inventoryProduct.closest('tr').find('.purchase_price').val(data.purchase_price);
                        inventoryProduct.closest('tr').find('.sale_price').val(data.sale_price);
                    }
                    else{
                        inventoryProduct.closest('tr').find('.available_quantity').val('');
                        inventoryProduct.closest('tr').find('.purchase_price').val('');
                        inventoryProduct.closest('tr').find('.sale_price').val('');
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                }
            });
    });

    $('body').on('input', '.quantity', function(){
        var quantityField = $(this);
        var quantity = quantityField.val();
        var availableQuantity = quantityField.closest("tr").find(".available_quantity").val();
        if(parseFloat(quantity) > parseFloat(availableQuantity) ){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'You Have No Available Quantity!'
            });
            quantityField.val('');
        } else {
            var purchasePrice = quantityField.closest("tr").find(".purchase_price").val();
            var netAmount = parseFloat(purchasePrice) * parseFloat(quantity);
            quantityField.closest("tr").find(".net_amount").val(netAmount);
            calculatePaidAmount();
        }
    });

    $('#commonModal').on('shown.bs.modal', function() {
        $(document).off('focusin.modal');
    });

    $('#create_btn').click(function(){
        $('#transfer_form').submit();
    });

    function calculatePaidAmount(){
        var netAmounts = $('body').find('.net_amount');
        var totalNetAmounts = 0;
        for(var i = 0; i < netAmounts.length; i++){
            if(netAmounts[i].value != ''){
                totalNetAmounts += parseFloat(netAmounts[i].value);
            }
        }
            $('.total_amount').html(totalNetAmounts.toFixed(2));
            $('#total_amount').val(totalNetAmounts);
    }

    //form validation
    function validateForm(form){
        var isValid = new Boolean();
        isValid = true;

        var elems = $(form).find(':input');
        $.each(elems, function(){
            var elem = $(this);

            if(typeof $(elem).attr('required') != typeof undefined && $(elem).attr('required') != "false" && $(elem).val().trim() == ""){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: $(elem).attr('data-empty-message'),
                })
                .then((value) => {
                    $("#"+$(elem).attr('data-focus')).focus();
                });
                isValid =  false;

                return false;
            }
        });

        return isValid;
    }
</script>

