{{Form::open(array('url'=>'cheque-print','method'=>'post'))}}

<div class="row">

    <div class="col-md-12">
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
    </div>


    <div class="form-group  col-md-4">
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


    <div class="col-md-8">
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
    </div>

    <div id="voucher_div" class="col-lg-12 col-md-6 col-sm-6">
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


    <div id="name" class="col-lg-12 col-md-6 col-sm-6">
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

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('bank', __('Bank')) }}
            <select name="bank" id="bank" class="form-control" required>
                <option value="">Select Bank</option>
                @foreach ($chequeBanks as $chequeBank)
                    <option value="{{ $chequeBank->id }}">{{ $chequeBank->bank }}</option>
                @endforeach
                {{-- <option value="commertial">Commertial Bank Cheque</option>
                <option value="selan">Selan Bank Cheque</option> --}}
            </select>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('cheque_number', __('Cheque Number')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
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
                {{Form::text('description',null,array('class'=>'form-control','required'=>'required'))}}
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
            {{ Form::number('amount', '', array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
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
                {{Form::text('amount_in_word',null,array('class'=>'form-control'))}}
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
        $('#voucher_div').hide();
        $("#list").hide();
        $("#name").hide();
        $('#no_crossing').prop('checked', true);
        $('#cash').prop('checked', true);

    $("#supplier").click(function () {
        $("#list").show();
        $("#name").hide();
        $('#voucher_div').hide();
    });

    $("#other").click(function () {
        $("#name").show();
        $("#list").hide();
        $('#voucher_div').hide();
    });

    $("#cash").click(function () {
        $("#list").hide();
        $("#name").hide();
        $('#voucher_div').hide();
    });

    $("#payment_voucher").click(function () {
        $('#voucher_div').show();
        $("#list").hide();
        $("#name").hide();
    });

    $('#voucher').on('change', function(){
        var voucherId = $(this).val();
        $.ajax({
            url:"{{ url('cheque-print_payment_voucher/get_voucher_details') }}/"+voucherId,
            method:'GET',
            dataType:'json',
            success: function (data, textStatus, jqXHR){
                console.log(data);
                if(data != null){
                    $("#description").val(data.description);
                    $("#amount").val(data.amount);

                    var amount = Number(data.amount);
                    words = toWords(amount);
                    $("#amount_in_word").val(words + "Only");
                }
                else {
                    $("#description").val('');
                    $("#amount").val('');
                    $("#amount_in_word").val('');
                }
            }
        });
    });

    $('#bank').on('change', function(){
        var bankId = $(this).val();
        $.ajax({
            url:"{{ url('cheque-print_no/get_cheque_number') }}/"+bankId,
            method:'GET',
            dataType:'json',
            success: function (data, textStatus, jqXHR){
                console.log(data);
                if(data != null){
                    $("#cheque_number").val(data.cheque_number);
                }
                else {
                    $("#cheque_number").val('');
                }
            }
        });
    });
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

