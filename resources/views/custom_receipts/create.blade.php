{{ Form::open(array('url' => 'custom_receipt')) }}
<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('current_date', __('Current Date')) }}
        <div class="input-group">
            {{ Form::text('current_date', '', array('class' => 'form-control datepicker','required'=>'required')) }}
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('account', __('Chart of Account')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
                {!! Form::select('account', $accounts, null, ['class' => 'form-control customer-sel font-style', 'required'=>'required'])!!}
            </div>
        </div>
    </div>

    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="form-group">
            <label class="d-block form-control-label">Receipt Type</label>
            <div class="row">
                <div class="col-md-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="cash" name="receipt_type" value="cash">
                        <label class="custom-control-label form-control-label" for="cash">Cash</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="supplier" name="receipt_type" value="supplier">
                        <label class="custom-control-label form-control-label" for="supplier">Receipt from Supplier</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="customer" name="receipt_type" value="customer">
                        <label class="custom-control-label form-control-label" for="customer">Receipt from Customer</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="other" name="receipt_type" value="other">
                        <label class="custom-control-label form-control-label" for="other">Other</label>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="list" class="col-lg-12 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('supplier_id', __('Supplier')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                {!! Form::select('supplier_id', $supplier, null, ['class' => 'form-control customer-sel font-style', 'placeholder' => '- Select -'])!!}
            </div>
        </div>
    </div>

    <div id="customer_div" class="col-lg-12 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('customer_id', __('Customer')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                {!! Form::select('customer_id', $customers, null, ['class' => 'form-control customer-sel font-style', 'placeholder' => '- Select -'])!!}
            </div>
        </div>
    </div>

    <div id="name_div" class="col-lg-12 col-md-12">
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    {{ Form::label('name', __('Name')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        {{Form::text('name',null,array('class'=>'form-control'))}}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('phone', __('Phone No.')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-phone"></i>
                            </div>
                        </div>
                        {{Form::number('phone',null,array('class'=>'form-control'))}}
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- <div class="form-group col-md-6">
        {{ Form::label('customer', __('Customer')) }}
        <div class="input-group">
            <select class="form-control customer-sel font-style selectpicker" data-live-search="true" name="customer" id="customer" required
            data-empty-message="Please Select Customer" data-focus="customer">
                <option value="">Select Customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
    </div> --}}

    <div class="form-group col-md-6">
        {{ Form::label('description', __('Description')) }}
        <div class="input-group colorpickerinput">
            <textarea class="form-control" id="description" name="description" required>Custom Receipt</textarea>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('paid_amount', __('Paid Amount')) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('paid_amount',null, array('class' => 'form-control price','required'=>'required','step'=>'0.01')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('paid_amount_in_words', __('Paid Amount In Words')) }}
        <div class="input-group colorpickerinput">
            <textarea class="form-control paid_amount_in_words" id="paid_amount_in_words" name="paid_amount_in_words" required></textarea>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('payment_method', __('Payment Method')) }}
        <select class="form-control payment_method" name="payment_method" id="payment_method" required>
            <option value="">Select Payment Method</option>
            <option value="CASH">Cash</option>
            <option value="CHEQUE">Cheque</option>
            <option value="CARD">Card</option>
            <option value="ONLINEPAYMENT">Online Payment</option>
        </select>
    </div>

    <div class="form-group col-md-6 cheque_number_div">
        {{ Form::label('realised_date', __('Realised Date')) }}
        <div class="input-group">
            {{ Form::text('realised_date', '', array('class' => 'form-control datepicker','required'=>'required')) }}
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-12 cheque_number_div">
        {{ Form::label('cheque_number', __('Cheque Number')) }}
        {{ Form::text('cheque_number',null, array('class' => 'form-control cheque_number')) }}
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
        {{ Form::number('card_charge_amount',null, array('class' => 'form-control card_charge_amount', 'step'=>'0.01')) }}
    </div>

    <div class="form-group col-md-6" id="total_amount_div">
        {{ Form::label('total_amount', __('Total Amount')) }}
        {{ Form::number('total_amount',null, array('class' => 'form-control total_amount', 'step'=>'0.01', 'readonly'=>'readonly')) }}
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('bank', __('Company Bank')) }}
        <div class="input-group">
            {{ Form::select('bank',$banks, null, array('class' => 'form-control customer-sel font-style','required'=>'required', 'data-empty-message'=>"Please Select a Company Bank", 'data-focus'=>"bank")) }}
        </div>
    </div>
</div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Pay'),array('class'=>'btn btn-primary'))}}
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();

        $('.cheque_number_div').hide();
        $('#card_type_div').hide();
        $('#card_charge_amount_div').hide();
        $('#total_amount_div').hide();
        $('#pay_total_amount_div').hide();

        $("#list").hide();
        $("#name_div").hide();
        $('#customer_div').hide();
        $('#cash').prop('checked', true);

        $("#supplier").click(function () {
            $("#name_div").hide(200);
            $('#customer_div').hide(200);
            $("#customer_id").val('');
            $("#name").val('');
            $("#phone").val('');
            $("#list").show(200);
        });

        $("#other").click(function () {
            $("#list").hide(200);
            $('#customer_div').hide(200);
            $("#supplier_id").val('');
            $("#customer_id").val('');
            $("#name_div").show(200);
        });

        $("#customer").click(function () {
            $("#list").hide(200);
            $("#name_div").hide(200);
            $("#supplier_id").val('');
            $("#name").val('');
            $("#phone").val('');
            $('#customer_div').show(200);
        });

        $("#cash").click(function () {
            $("#supplier_id").val('');
            $("#name").val('');
            $("#phone").val('');
            $("#customer_id").val('');
            $("#list").hide(200);
            $("#name_div").hide(200);
            $('#customer_div').hide(200);
        });

        $('#paid_amount').on('input', function(){
            $("#paid_amount_in_words").html('');
            var amount = $(this).val();
            var words = '';
            words = toWords(amount);
            $("#paid_amount_in_words").html(words + "Only");
        });

        $('body').on('change', '#payment_method', function(){
        var method = $(this).val();
        if(method == 'CHEQUE'){
            $('#card_type').val('');
            $('#card_charge_amount').val('');
            $('#total_amount').val('');
            $('#card_type_div').hide(200);
            $('#card_charge_amount_div').hide(200);
            $('#total_amount_div').hide(200);
            $('.cheque_number_div').show(200);
        }else if(method == 'CARD'){
            $('#cheque_number').val('');
            $('.cheque_number_div').hide(200);
            $('#card_type_div').show(200);
            $('#card_charge_amount_div').show(200);
            $('#total_amount_div').show(200);
        }else {
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

    $('body').on('change', '#card_type', function(){
        var cardType = $(this).val();
        var paidAmount = $('#paid_amount').val();
        $.ajax({
            url:"{{ url('card_payment_rate/get_detail') }}/"+cardType,
            method:'GET',
            dataType:'json',
            success: function (data, textStatus, jqXHR){
                if(data != null){
                    var cardCharge = parseFloat(paidAmount)*( parseFloat(data.rate)/100 );
                    var totalAmount = parseFloat(paidAmount)+cardCharge;
                    $('#card_charge_amount').val(cardCharge);
                    $('#total_amount').val(totalAmount);
                }
            }
        });
    });

    $('body').on('input', '#card_charge_amount', function(){
        var cardCharges = $(this).val();
        var paidAmounts = $('#paid_amount').val();
        var totalAmounts = parseFloat(paidAmounts)+parseFloat(cardCharges);
        $('#total_amount').val(totalAmounts);
    });
    });
</script>
