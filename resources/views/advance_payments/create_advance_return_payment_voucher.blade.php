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
{{ Form::open(array('url' => 'payment_voucher','method'=>'post')) }}
<div class="row">
    <div class="col-md-12 text-center"><p style="color: red">*This action can not be undone.</p></div>
</div>

<div class="row">
    <input type="hidden" name="advance_id" id="advance_id" value="{{ $advance->id }}">
    <input type="hidden" name="payment_method" id="payment_method" value="CHEQ">

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

    <div class="form-group  col-md-6">
        {{ Form::label('voucher_date', __('Voucher Date')) }}<span class="text-danger">*</span>
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
            {{ Form::text('voucher_date', '', array('class' => 'form-control datepicker','required'=>'required')) }}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('account', __('Chart of Account')) }}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
                {!! Form::select('account', $accounts, $chartAccount ?? '', ['class' => 'form-control customer-sel font-style', 'required'=>'required' , 'placeholder' => '- Select -'])!!}
            </div>
        </div>
    </div>
    {{-- <div class="form-group  col-md-6">
        {{ Form::label('service_id', __('Service')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::select('service_id',$services, $voucher->service_id, array('class' => 'form-control customer-sel font-style ','required'=>'required')) }}
        </div>
    </div> --}}

    {{-- <div class="form-group col-md-6">
        {{ Form::label('payment_method', __('Payment Method')) }}
        <select class="form-control payment_method" name="payment_method" id="payment_method" required
            data-empty-message="Please select Payment Method" data-focus="payment_method">
            <option value="">Select Payment Method</option>
            <option selected value="CHEQ">Cheque</option>
        </select>
    </div> --}}

    <div class="form-group col-md-1"></div>

    <div class="col-md-10">
        <div class="form-group">
            <label class="d-block form-control-label">Pay Type</label>
            <div class="row">
                <div class="col-md-4">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="cash" name="pay_type" value="cash">
                        <label class="custom-control-label form-control-label" for="cash">Cash</label>
                    </div>
                </div>
                {{-- <div class="col-md-4">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="supplier" name="pay_type" value="supplier">
                        <label class="custom-control-label form-control-label" for="supplier">Pay for Supplier</label>
                    </div>
                </div> --}}
                <div class="col-md-4">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="customer" name="pay_type" value="customer">
                        <label class="custom-control-label form-control-label" for="customer">Pay for Customer</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="other" name="pay_type" value="other">
                        <label class="custom-control-label form-control-label" for="other">Other</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-1"></div>

    {{-- <div id="list" class="col-lg-12 col-md-6 col-sm-6">
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
    </div> --}}

    <div id="customer_div" class="col-lg-12 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('customer_id', __('Customer')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                {!! Form::select('customer_id', $customers, $advance->customer_id, ['class' => 'form-control customer-sel font-style', 'placeholder' => '- Select -'])!!}
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
                {{Form::text('name', null,array('class'=>'form-control'))}}
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('bank', __('Bank')) }}<span class="text-danger">*</span>
        <div class="input-group">
            {{ Form::select('bank',$banks, null, array('class' => 'form-control customer-sel font-style','required'=>'required', 'data-empty-message'=>"Please Select a Company Bank", 'data-focus'=>"bank")) }}
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
                <input type="hidden" name="cheque_book_cheque_id" id="cheque_book_cheque_id">
                {{Form::text('cheque_number',null,array('class'=>'form-control', 'readonly'=>'readonly'))}}
            </div>
        </div>
    </div>

    @php
        $customer = \Auth::user()->customerNumberFormat($advance->customer_id);
        $description = 'Being payment for Advance Return - '.$customer;
    @endphp
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="form-group">
            {{ Form::label('description', __('Description')) }}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-file"></i>
                    </div>
                </div>
                {{Form::text('description',$description,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>

    <div class="form-group  col-md-6">
        {{ Form::label('amount', __('Amount')) }}<span class="text-danger">*</span>
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::number('amount', $advance->advance_amount, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('amount_in_word', __('Amount in Word')) }}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-align-justify"></i>
                    </div>
                </div>
                {{Form::text('amount_in_word', null,array('class'=>'form-control', 'required'=>'required'))}}
            </div>
        </div>
    </div>

    {{-- <div class="form-group col-md-12">
        {{ Form::label('description', __('Description')) }}
        <div class="input-group">
            <textarea class="form-control" id="description" name="description" required>{{ $voucher->description }}</textarea>
        </div>
    </div> --}}

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}

<script>
    var advance = <?php echo json_encode($advance); ?>;
    $(document).ready(function () {
        $("#name_div").hide();
        $('#no_crossing').prop('checked', true);
        $('#customer').prop('checked', true);

        amountInWord(advance.advance_amount);

        // if(voucher.pay_type == 'supplier'){
        //     $("#name_div").hide();
        //     $('#customer_div').hide();
        //     $("#customer_id").val('');
        //     $("#name").val('');
        //     $("#list").show();
        //     $('#supplier').prop('checked', true);


        // }else if(voucher.pay_type == 'other'){
        //     $("#list").hide();
        //     $('#customer_div').hide();
        //     $("#supplier_id").val('');
        //     $("#customer_id").val('');
        //     $("#name_div").show();
        //     $('#other').prop('checked', true);
        // }else if(voucher.pay_type == 'customer'){
        //     $("#list").hide();
        //     $("#name_div").hide();
        //     $("#supplier_id").val('');
        //     $("#name").val('');
        //     $('#customer_div').show();
        //     $('#customer').prop('checked', true);
        // }else{
        //     $("#list").hide();
        //     $("#name_div").hide();
        //     $('#customer_div').hide();
        //     $("#supplier_id").val('');
        //     $("#name").val('');
        //     $("#customer_id").val('');
        //     $('#cash').prop('checked', true);
        // }
        // $("#list").hide();
        // $("#name_div").hide();
        // $('#cash').prop('checked', true);

    // $("#supplier").click(function () {
    //     $("#name_div").hide(200);
    //     $('#customer_div').hide(200);
    //     $("#customer_id").val('');
    //     $("#name").val('');
    //     $("#list").show(200);
    // });

    $("#other").click(function () {
        // $("#list").hide(200);
        $('#customer_div').hide(200);
        // $("#supplier_id").val('');
        $("#customer_id").val('');
        $("#name_div").show(200);
    });

    $("#customer").click(function () {
        // $("#list").hide(200);
        $("#name_div").hide(200);
        // $("#supplier_id").val('');
        $("#name").val('');
        $('#customer_div').show(200);
    });

    $("#cash").click(function () {
        // $("#supplier_id").val('');
        $("#name").val('');
        $("#customer_id").val('');
        // $("#list").hide(200);
        $("#name_div").hide(200);
        $('#customer_div').hide(200);
    });

    $('#amount').on('input',function(){
        var voucherAmount = $(this).val();
        amountInWord(voucherAmount);
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
                    $("#cheque_book_cheque_id").val(data.id);
                }
                else {
                    $("#cheque_number").val('');
                    $("#cheque_book_cheque_id").val('');
                }
            }
        });
    });

    $('body').submit(function() {
        $(".se-pre-con").css({"display": "block"});
    });
});

function amountInWord(voucherAmount) {
    var amount = Number(voucherAmount);
    words = toWords(amount);
    $("#amount_in_word").val(words + "Only");
}
</script>
