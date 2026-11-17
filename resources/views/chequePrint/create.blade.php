<style>
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
{{Form::open(array('url'=>'cheque-print','method'=>'post'))}}

<div class="row">
    <input type="hidden" name="loan_id" id="loan_id" value="">
    <input type="hidden" name="pawning_id" id="pawning_id" value="">
    <input type="hidden" name="advance_id" id="advance_id" value="">

    {{-- <div class="col-md-12">
        <div class="form-group">
            <label class="d-block form-control-label">Crossing Type</label>
            <div class="row">
                <div class="col-md-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="no_crossing" name="cross_type" value="no_crossing" required="required">
                        <label class="custom-control-label form-control-label" for="no_crossing">No Crossing</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="cross_only" name="cross_type" value="cross_only">
                        <label class="custom-control-label form-control-label" for="cross_only">Cross Only</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="ac_payee" name="cross_type" value="ac_payee">
                        <label class="custom-control-label form-control-label" for="ac_payee">A/C Payee</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="not_negotiable" name="cross_type" value="not_negotiable">
                        <label class="custom-control-label form-control-label" for="not_negotiable">Not Negotiable</label>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="form-group  col-md-6">
        {{ Form::label('date', __('Date')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
            {{ Form::text('date', '', array('class' => 'form-control datepicker','required'=>'required')) }}
        </div>
    </div>

    <div class="col-md-12">
        <input type="hidden" name="chart_of_account_id" id="chart_of_account_id" value="">
        <div class="form-group">
            {{ Form::label('voucher', __('Payment Voucher')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fab fa-flipboard"></i>
                    </div>
                </div>
                <select name="voucher" id="voucher" class="form-control customer-sel font-style">
                    <option value="">- Select -</option>
                    @foreach ($vouchers as $voucher)
                        <option value="{{ $voucher->id }}">{{ \Auth::user()->paymentVoucherNumberFormat($voucher->id) }} - {{ $voucher->description }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('cross_type', __('Crossing Type')) }}
            <select name="cross_type" id="cross_type" class="form-control customer-sel font-style">
                <option value="">- Crossing Type -</option>
                <option value="no_crossing">No Crossing</option>
                <option value="cross_only">Cross Only</option>
                <option value="ac_payee">A/C Payee</option>
                <option value="not_negotiable">Not Negotiable</option>
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('pay_type', __('Pay Type')) }}
            <select name="pay_type" id="pay_type" class="form-control customer-sel font-style">
                <option value="">- Pay Type -</option>
                <option value="cash">Cash</option>
                <option value="supplier">Pay for Supplier</option>
                <option value="customer">Customer</option>
                <option value="other">Other</option>
            </select>
        </div>
    </div>

    {{-- <div class="col-md-8">
        <div class="form-group">
            <label class="d-block form-control-label">Pay Type</label>
            <div class="row">
                <div class="col-md-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="cash" name="pay_type" value="cash">
                        <label class="custom-control-label form-control-label" for="cash">Cash</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="payment_voucher" name="pay_type" value="payment_voucher">
                        <label class="custom-control-label form-control-label" for="payment_voucher">Payment Voucher</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="supplier" name="pay_type" value="supplier" required="required">
                        <label class="custom-control-label form-control-label" for="supplier">Pay for Supplier</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="other" name="pay_type" value="other">
                        <label class="custom-control-label form-control-label" for="other">Other</label>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div id="list" class="col-lg-12 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('supplier', __('Supplier')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                {!! Form::select('supplier', $supplier, null, ['class' => 'form-control customer-sel font-style', 'placeholder' => '- Select -'])!!}
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

    <div id="name_div" class="col-lg-12 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('name', __('Name')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                {{Form::text('name',null,array('class'=>'form-control', 'readonly'=>'readonly'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('bank', __('Bank')) }}
            <select name="bank" id="bank" class="form-control" required>
                <option value="">Select Bank</option>
                @foreach ($chequeBanks as $chequeBank)
                    <option value="{{ $chequeBank->id }}">{{ $chequeBank->bank }}</option>
                @endforeach
            </select>
        </div>
    </div>
    {{-- <div class="form-group col-lg-6 col-md-6 col-sm-6">
        {{ Form::label('bank', __('Bank')) }}
        <div class="input-group">
            {{ Form::select('bank',$chequeBank,null, array('class' => 'form-control customer-sel font-style','required'=>'required', 'data-empty-message'=>"Please Select a Company Bank", 'data-focus'=>"bank")) }}
        </div>
    </div> --}}

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('cheque_number', __('Cheque Number')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                </div>
                <input type="hidden" name="cheque_book_cheque_id" id="cheque_book_cheque_id" value="">
                {{Form::text('cheque_number',null,array('class'=>'form-control', 'readonly'=>'readonly'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="form-group">
            {{ Form::label('description', __('Description')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-file"></i>
                    </div>
                </div>
                {{Form::text('description',null,array('class'=>'form-control','required'=>'required', 'readonly'=>'readonly'))}}
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('amount', __('Amount')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::number('amount', '', array('class' => 'form-control','required'=>'required','step'=>'0.01', 'readonly'=>'readonly')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('amount_in_word', __('Amount in Word')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-align-justify"></i>
                    </div>
                </div>
                {{Form::text('amount_in_word',null,array('class'=>'form-control', 'readonly'=>'readonly'))}}
            </div>
        </div>
    </div>

</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{-- {{Form::submit(__('Create & Print'),array('class'=>'btn btn-primary'))}} --}}
    {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
</div>

</form>


<script>
    $(document).ready(function () {
        $("#list").hide();
        $("#name_div").hide();
        $('#customer_div').hide();
        // $('#no_crossing').prop('checked', true);
        $('#cross_type').prop('disabled', true);
        $('#pay_type').prop('disabled', true);
        $('#bank').prop('disabled', true);
        $('#supplier').prop('disabled', true);
        $('#customer_id').prop('disabled', true);

    $('#voucher').on('change', function(){
        var voucherId = $(this).val();
        $.ajax({
            url:"{{ url('cheque-print_payment_voucher/get_voucher_details') }}/"+voucherId,
            method:'GET',
            dataType:'json',
            success: function (data, textStatus, jqXHR){
                console.log(data);
                if(data != null){
                    if(data.loan_id != null){
                        $("#loan_id").val(data.loan_id);
                    }
                    if(data.pawning_id != null){
                        $("#pawning_id").val(data.pawning_id);
                    }
                    if(data.advance_id != null){
                        $("#advance_id").val(data.advance_id);
                    }
                    $("#chart_of_account_id").val(data.chart_of_account_id);
                    $("#date").val(data.date);
                    $("#cross_type").val(data.cross_type);
                    $("#pay_type").val(data.pay_type);
                    if(data.pay_type == 'supplier'){
                        $("#name_div").hide(200);
                        $("#name").val('');
                        $('#customer_div').hide(200);
                        $('#customer_id').val('');
                        $("#list").show(200);
                        $("#supplier").val(data.vender_id);
                    }else if(data.pay_type == 'customer'){
                        $("#list").hide(200);
                        $("#name_div").hide(200);
                        $("#supplier").val('');
                        $("#name").val('');
                        $('#customer_div').show(200);
                        $('#customer_id').val(data.customer_id);
                    }else if(data.pay_type == 'other'){
                        $("#list").hide(200);
                        $("#supplier").val('');
                        $('#customer_div').hide(200);
                        $('#customer_id').val('');
                        $("#name_div").show(200);
                        $("#name").val(data.pay_to);
                    }else if(data.pay_type == 'cash'){
                        $("#list").hide(200);
                        $("#name_div").hide(200);
                        $('#customer_div').hide(200);
                    }
                    $("#bank").val(data.bank_id);
                    $("#cheque_book_cheque_id").val(data.cheque_book_cheque.id);
                    $("#cheque_number").val(data.cheque_book_cheque.cheque_number);
                    $("#description").val(data.description);
                    $("#amount").val(data.amount);
                    $("#amount_in_word").val(data.amount_in_word);
                }
                else {
                    $("#list").hide(200);
                    $("#name_div").hide(200);
                    $('#customer_div').hide(200);
                    $('#customer_id').val('');
                    $("#chart_of_account_id").val('');
                    $("#date").val('');
                    $("#cross_type").val('');
                    $("#pay_type").val('');
                    $("#supplier").val('');
                    $("#name").val('');
                    $("#bank").val('');
                    $("#cheque_book_cheque_id").val('');
                    $("#cheque_number").val('');
                    $("#description").val('');
                    $("#amount").val('');
                    $("#amount_in_word").val('');
                }
            }
        });
    });

    // $('#bank').on('change', function(){
    //     var bankId = $(this).val();
    //     $.ajax({
    //         url:"{{ url('cheque-print_no/get_cheque_number') }}/"+bankId,
    //         method:'GET',
    //         dataType:'json',
    //         success: function (data, textStatus, jqXHR){
    //             console.log(data);
    //             if(data != null){
    //                 $("#cheque_number").val(data.cheque_number);
    //             }
    //             else {
    //                 $("#cheque_number").val('');
    //             }
    //         }
    //     });
    // });
});

$('body').on('submit', function() {
    $('input, select').prop('disabled', false);
    $(".se-pre-con").css({"display": "block"});
});
</script>


<script>
    //Enter Only Numbers
    $("#amount").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            return false;
        }
    });

    var words="";
    $(function() {
        $("#amount").on("keydown keyup", convert);

        function convert() {
            var amount = Number($("#amount").val());
            words = toWords(amount);
            $("#amount_in_word").val(words + "Only");
        }
    });
</script>

