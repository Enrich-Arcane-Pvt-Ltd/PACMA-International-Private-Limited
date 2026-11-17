<div class="setting-tab">
    <ul class="nav nav-pills nav-fill" id="myTab3" role="tablist">
        @if(Gate::check('create general journal'))
            <li class="nav-item">
                <a class="nav-link @if(Gate::check('create general journal')) active @endif" id="contact-tab4" data-toggle="tab" href="#account-adjustment" role="tab" aria-controls="" aria-selected="false">{{__('Account Adjustment')}}</a>
            </li>
        @endif
        @if(Gate::check('create stock adjustment'))
            <li class="nav-item">
                <a class="nav-link @if(!Gate::check('create general journal') && Gate::check('create stock adjustment')) active @endif" id="contact-tab4" data-toggle="tab" href="#stock-adjustment" role="tab" aria-controls="" aria-selected="false">{{__('Stock Adjustment')}}</a>
            </li>
        @endif
    </ul>

    <div class="tab-content" id="myTabContent2">
        @if(Gate::check('create general journal'))
            <div class="tab-pane fade fade  @if(Gate::check('create general journal')) show active @endif" id="account-adjustment" role="tabpanel" aria-labelledby="profile-tab3">
                <div class="company-setting-wrap">
                    <div class="card-body" style="padding-left: 1px; padding-right: 1px;">
                        {{Form::open(array('url'=>'general-journal/store','method'=>'post','class'=>'repeater'))}}
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

                            <div class="col-md-12">
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
                            </div>
                            <div class="col-md-12 text-right">
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
            <div class="tab-pane fade fade @if(!Gate::check('create general journal') && Gate::check('create stock adjustment')) show active @endif" id="stock-adjustment" role="tabpanel" aria-labelledby="profile-tab3">
                <div class="company-setting-wrap">
                    <div class="card-body" style="padding-left: 1px; padding-right: 1px;">
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
                                            <option data-purchase="" data-sale="" value="">Select Product with price</option>
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
    </div>
</div>

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
                    // 'status': 1
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

                        // var inputs = $(".amount");
                        // var subTotal = 0;
                        // for (var i = 0; i < inputs.length; i++) {
                        //     subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                        // }
                        // $('.subTotal').html(subTotal.toFixed(2));
                        // $('.totalAmount').html(subTotal.toFixed(2));
                        // finalCalculation();

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
        $('#product_with_price').prop('disabled', true);
        $('#deductStock').prop('disabled', true);
        $('#addStock').prop('disabled', true);
    });

    $('body').on('change', '.debit_credit', function(){
        var statusVlue = $(this).val();
        var relatedStatus = $(this).closest('tr').find('.related_debit_credit');
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
        var statusVlue = $(this).closest('tr').find('.debit_credit');
        if(relatedStatus == 0){
            statusVlue.val(1);
        } else if(relatedStatus == 1){
            statusVlue.val(0);
        } else {
            statusVlue.val('');
        }
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
                    $('#product_with_price').append('<option value = "">Select Product with price</option>');
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
                            title: 'Success',
                            text: 'The Stock Adjusted Successfully!',
                            timer: 1300,
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

