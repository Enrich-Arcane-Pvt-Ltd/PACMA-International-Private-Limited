<style>
    #commonModal .modal-body {
        overflow: visible;
    }
</style>
{{Form::open(array('url'=>'item_return/update','method'=>'post'))}}

<div class="row">
    <input type="hidden" name="item_return_id" id="item_return_id" value="{{ $itemReturn->id }}">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('date',__('Date')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
                {{Form::text('date',$itemReturn->date,array('class'=>'form-control datepicker','required'=>'required'))}}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('invoice_id', __('Invoice')) }}
            <div class="input-group">
                <input type="hidden" name="customer_id" class="customer_id" id="customer_id" value="{{ $itemReturn->customer_id }}">
                <select class="form-control customer-sel font-style invoice_id selectpicker" data-live-search="true" name="invoice_id" id="invoice_id" required>
                    <option value="">-- Select Invoice --</option>
                    @foreach ($invoices as $invoice)
                        <option @if ($invoice->id == $itemReturn->invoice_id) selected @endif value="{{ $invoice->id }}">{{ $invoice->invoice_code }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('product_id', __('Item')) }}
            <div class="input-group">
                <input type="hidden" name="inventory_id" class="inventory_id" id="inventory_id" value="{{ $itemReturn->inventory_id }}">
                <select class="form-control customer-sel font-style product_id" name="product_id" id="product_id" required>
                    <option data-inventory="" value="">-- Select Item --</option>
                    <option data-inventory="{{ $itemReturn->inventory_id }}" value="{{ $itemReturn->product_id }}" selected>{{ $itemReturn->product->name }}</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('serial_no', __('Serial No.')) }}
            <input type="text" name="serial_no" class="form-control customer-sel font-style serial_no" id="serial_no" value="{{ $itemReturn->serial_no }}" required>
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
                        <option @if ($account->id == $itemReturn->account_id) selected @endif value="{{ $account->id }}">{{ $account->name }}</option>
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
                    <option @if($itemReturn->related_debit_credit == 0) selected @endif value="0">Credit</option>
                    <option @if($itemReturn->related_debit_credit == 1) selected @endif value="1">Debit</option>
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
                        <option @if ($account->id == $itemReturn->related_account_id) selected @endif value="{{ $account->id }}">{{ $account->name }}</option>
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
                    <option @if($itemReturn->related_debit_credit == 0) selected @endif value="0">Credit</option>
                    <option @if($itemReturn->related_debit_credit == 1) selected @endif value="1">Debit</option>
                </select>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('amount', __('Amount')) }}
            {{ Form::number('amount', $itemReturn->amount ?? '', array('class' => 'form-control amount','required'=>'required','step'=>'0.01', 'data-empty-message'=>"Please Enter Amount", 'data-focus'=>"amount")) }}
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            {{ Form::label('narration', __('Narration')) }}
            {{Form::text('narration',$itemReturn->narration ?? '',array('class'=>'form-control narration'))}}
        </div>
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Edit'),array('class'=>'btn btn-primary'))}}
    </div>
</div>

{{Form::close()}}

<script src="{{asset('assets/js/jquery.repeater.min.js')}}"></script>

<script>

    $(document).ready(function(){
        $('.selectpicker').selectpicker();
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
                    if(data != null){
                        $('#customer_id').val(data.customer_id);
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

</script>
