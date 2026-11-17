{{Form::open(array('url'=>'cheque-print','method'=>'post'))}}

<div class="row">
    <input type="hidden" name="loan_id" id="loan_id" value="{{ $loans->id }}">
    <div class="col-md-12">
        <div class="form-group">
            <label class="d-block form-control-label">Cross Type</label>
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


    <div class="col-md-6">
        <div class="form-group">
            <label class="d-block form-control-label">Pay Type</label>
            <div class="row">
                <div class="col-md-4">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="cash" name="pay_type" value="cash">
                        <label class="custom-control-label form-control-label" for="cash">Cash</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="customer" name="pay_type" value="customer" required="required">
                        <label class="custom-control-label form-control-label" for="customer">Pay for Customers</label>
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

    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('account', __('Chart of Account')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
                {!! Form::select('account', $accounts, null, ['class' => 'form-control customer-sel font-style', 'required'=>'required' , 'placeholder' => '- Select -'])!!}
            </div>
        </div>
    </div>

    <div id="list" class="col-lg-12 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('customer', __('Customer')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                {!! Form::select('customer', $customers, $loans->customer_id, ['class' => 'form-control customer-sel font-style'])!!}
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

    <div id="list" class="col-lg-6 col-md-6 col-sm-6">
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

    <div id="list" class="col-lg-6 col-md-6 col-sm-6">
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

    @php
        if($loans->loan_type == 1){
            $loanType = 'SME loan';
        } elseif($loans->loan_type == 2){
            $loanType = 'Group loan';
        } elseif($loans->loan_type == 3){
            $loanType = 'Vehicle loan';
        } elseif($loans->loan_type == 4){
            $loanType = 'Land loan';
        } elseif($loans->loan_type == 5){
            $loanType = 'Job loan';
        }
        $customer = \Auth::user()->customerNumberFormat($loans->customer_id);
        $description = 'Being payment for issuing '.$loanType.' - '.$customer;
    @endphp
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="form-group">
            {{ Form::label('description', __('Description')) }}
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

    <div class="form-group col-md-6">
        {{ Form::label('amount', __('Amount')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::number('amount', $loans->loan_amount, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
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
    var loans = <?php echo json_encode($loans); ?>;
    $(document).ready(function () {
        $("#list").show();
        $("#name").hide();
        $('#no_crossing').prop('checked', true);
        $('#customer').prop('checked', true);

        documentChargeInWords(loans.loan_amount);

    $("#customer").click(function () {
        $("#list").show();
        $("#name").hide();
    });

    $("#other").click(function () {
        $("#name").show();
        $("#list").hide();
    });

    $("#cash").click(function () {
        $("#list").hide();
        $("#name").hide();
    });

    $('#bank').on('change', function(){
        var bankId = $(this).val();
        console.log(bankId);
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

    $('#amount').on('input', function(){
        var amount = $(this).val();
        documentChargeInWords(amount);
    });
});

function documentChargeInWords(amount){
    var chargeWords = '';
    chargeWords = toWords(Number(amount));
    $("#amount_in_word").val(chargeWords + "Only");
}
</script>


