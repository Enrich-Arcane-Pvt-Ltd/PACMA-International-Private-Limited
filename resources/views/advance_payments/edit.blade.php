{{ Form::open(['url' => 'advance_payement/update']) }}
<div class="row">
    <input type="hidden" name="advance_id" id="advance_id" value="{{ $advance->id }}">
    <div class="form-group col-md-6">
        {{ Form::label('current_date', __('Current Date')) }}
        <div class="input-group">
            {{ Form::text('current_date', $advance->advance_date, ['class' => 'form-control datepicker', 'required' => 'required']) }}
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('customer', __('Customer')) }}
        <div class="input-group">
            <select class="form-control customer-sel font-style" name="customer" id="customer" required
                data-empty-message="Please Select Customer" data-focus="customer">
                <option value="">Select Customer</option>
                @foreach ($customers as $customer)
                    <option @if ($advance->customer_id == $customer->id) selected @endif value="{{ $customer->id }}">
                        {{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('advance_amount', __('Advance Amount')) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('advance_amount', number_format($advance->advance_amount, 2, '.', ''), ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01']) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{ \Auth::user()->currencySymbol() }}
                </div>
            </div>
        </div>
    </div>



    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('Note', __('Note')) }}
            <label class="form-control-label"></label>
            <div class="input-group mt-1">
                <textarea class="form-control note text-start" id="note" name="note">{{ $advance->note }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('advance_amount_in_words', __('Advance Amount In Words')) }}
        <div class="input-group colorpickerinput">
            <textarea class="form-control advance_amount_in_words" id="advance_amount_in_words" name="advance_amount_in_words"
                required>{{ $advance->advance_amount_in_words }}</textarea>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('payment_method', __('Payment Method')) }}
        <select class="form-control payment_method" name="payment_method" id="payment_method" required>
            <option value="">Select Payment Method</option>
            <option @if ($advance->payment_method == 'CASH') selected @endif value="CASH">Cash</option>
            <option @if ($advance->payment_method == 'CHEQUE') selected @endif value="CHEQUE">Cheque</option>
            <option @if ($advance->payment_method == 'CARD') selected @endif value="CARD">Card</option>
            <option @if ($advance->payment_method == 'ONLINEPAYMENT') selected @endif value="ONLINEPAYMENT">Online Payment</option>
        </select>
    </div>
    <div class="form-group col-md-6 cheque_number_div">
        {{ Form::label('realised_date', __('Realised Date')) }}
        <div class="input-group">
            {{ Form::text('realised_date', $advance->cheque_realised_date, ['class' => 'form-control datepicker', 'required' => 'required']) }}
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6 cheque_number_div">
        {{ Form::label('cheque_number', __('Cheque Number')) }}
        {{ Form::text('cheque_number', $advance->cheque_number, ['class' => 'form-control cheque_number']) }}
    </div>

    <div class="form-group col-md-6" id="card_type_div">
        {{ Form::label('card_type', __('Card Type')) }}
        <select class="form-control card_type" name="card_type" id="card_type">
            <option value="">Select Card Type</option>
            <option @if ($advance->card_type == 1) selected @endif value="1">Credit Card</option>
            <option @if ($advance->card_type == 2) selected @endif value="2">Debit Card</option>
        </select>
    </div>

    <div class="form-group col-md-6" id="card_charge_amount_div">
        {{ Form::label('card_charge_amount', __('Card Charges')) }}
        {{ Form::number('card_charge_amount', $advance->card_charge_amount, ['class' => 'form-control card_charge_amount', 'step' => '0.01']) }}
    </div>

    <div class="form-group col-md-6" id="total_amount_div">
        {{ Form::label('total_amount', __('Total Amount')) }}
        {{ Form::number('total_amount', $advance->total_amount, ['class' => 'form-control total_amount', 'step' => '0.01', 'readonly' => 'readonly']) }}
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('bank', __('Company Bank')) }}
        <div class="input-group">
            {{ Form::select('bank', $banks, $advance->bank_id, ['class' => 'form-control customer-sel font-style', 'required' => 'required', 'data-empty-message' => 'Please Select a Company Bank', 'data-focus' => 'bank']) }}
        </div>
    </div>
</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn btn-primary']) }}
</div>
</div>

<script>
    var advance = <?php echo json_encode($advance); ?>;
    $(document).ready(function() {
        $('#advance_amount').on('input', function() {
            $("#advance_amount_in_words").html('');
            var amount = $(this).val();
            amount = amount.replace(/\.00$/, '');
            var words = toWords(amount);

            $("#advance_amount_in_words").html(words + "Only");
        });

        $('#advance_amount').on('focusout', function() {
            let value = parseFloat($(this).val()).toFixed(2);
            $(this).val(value);
        });

        var paymentMethod = advance.payment_method;
        changeMethod(paymentMethod);
    });

    $('body').on('change', '#payment_method', function() {
        var method = $(this).val();
        changeMethod(method);
    });

    function changeMethod(method) {
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
    }

    $('body').on('change', '#card_type', function() {
        var cardType = $(this).val();
        var advanceAmount = $('#advance_amount').val();
        $.ajax({
            url: "{{ url('card_payment_rate/get_detail') }}/" + cardType,
            method: 'GET',
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                if (data != null) {
                    var cardCharge = parseFloat(advanceAmount) * (parseFloat(data.rate) / 100);
                    var totalAmount = parseFloat(advanceAmount) + cardCharge;
                    $('#card_charge_amount').val(cardCharge);
                    $('#total_amount').val(totalAmount);
                }
            }
        });
    });

    $('body').on('input', '#card_charge_amount', function() {
        var cardCharges = $(this).val();
        var advanceAmounts = $('#advance_amount').val();
        var totalAmounts = parseFloat(advanceAmounts) + parseFloat(cardCharges);
        $('#total_amount').val(totalAmounts);
    });
</script>
