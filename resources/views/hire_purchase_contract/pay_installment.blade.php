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

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 25px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 17px;
        width: 17px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
<div class="se-pre-con"></div>
{{ Form::open(['url' => 'hire_purchase_contract', 'id' => 'hire_purchase_contract_form']) }}
<div class="row">
    <input type="hidden" id="sale_id" name="sale_id" value="{{ $installments->sale_id }}">
    <input type="hidden" id="installment_id" name="installment_id" value="{{ $installments->id }}">
    <input type="hidden" id="invoice_id" name="invoice_id" value="{{ $installments->invoice_id }}">
    <input type="hidden" id="customer_id" name="customer_id" value="{{ $sales->customer_id }}">
    <input type="hidden" id="next_installment_date" name="next_installment_date"
        value="{{ $nextInstallment->installment_date ?? '' }}">
    <input type="hidden" id="status" name="status" value="{{ $installments->status }}">
    <div class="form-group col-md-6">
        {{ Form::label('current_date', __('Current Date')) }}
        <div class="input-group">
            {{ Form::text('current_date', '', ['class' => 'form-control datepicker', 'required' => 'required']) }}
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('installment_due_date', __('Installment Due Date')) }}
        <div class="input-group">
            <input type="text" class="form-control" id="installment_due_date" name="installment_due_date"
                value="{{ $installments->installment_date }}" readonly>
        </div>
    </div>

    <div class="form-group col-md-4">
        {{ Form::label('this_sale_value', __('Item Value for Installment')) }}
        <div class="input-group colorpickerinput">
            <input type="number" class="form-control" step="0.01" id="this_sale_value" name="this_sale_value"
                value="{{ $data['amountForInst'] ?? '0.00' }}" readonly>
            <div class="input-group-append">
                <div class="input-group-text">
                    {{ \Auth::user()->currencySymbol() }}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-4">
        {{ Form::label('this_interest_value', __('Interest Value for Installment')) }}
        <div class="input-group colorpickerinput">
            <input type="number" class="form-control" step="0.01" id="this_interest_value"
                name="this_interest_value" value="{{ $data['interestForInst'] ?? '0.00' }}" readonly>
            <div class="input-group-append">
                <div class="input-group-text">
                    {{ \Auth::user()->currencySymbol() }}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-4">
        {{ Form::label('installment_amount', __('Installment Amount')) }}
        <div class="input-group colorpickerinput">
            <input type="number" class="form-control" step="0.01" id="installment_amount" name="installment_amount"
                value="{{ $installments->amount ?? '0.00' }}" readonly>
            <div class="input-group-append">
                <div class="input-group-text">
                    {{ \Auth::user()->currencySymbol() }}
                </div>
            </div>
        </div>
    </div>

    @if ($nextInstallment != null)
        @php
            if ($installmentAdvances) {
                $totalAdvance = $installmentAdvances->sum('advance_amount');
            } else {
                $totalAdvance = 0;
            }
        @endphp

        <div class="form-group col-md-4">
            {{ Form::label('due_sale_value', __('Due Item Value')) }}
            <div class="input-group colorpickerinput">
                <input type="number" class="form-control" step="0.01" id="due_sale_value" name="due_sale_value"
                    value="{{ $data['amountForInst'] * $data['dueInsts'] ?? '0.00' }}" readonly>
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-4">
            {{ Form::label('due_interest_value', __('Due Interest Value')) }}
            <div class="input-group colorpickerinput">
                <input type="number" class="form-control" step="0.01" id="due_interest_value"
                    name="due_interest_value" value="{{ $data['interestForInst'] * $data['dueInsts'] ?? '0.00' }}"
                    readonly>
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-4">
            {{ Form::label('due_installment_amount', __('Due Installment Amounts')) }}
            <div class="input-group colorpickerinput">
                <input type="number" class="form-control" step="0.01" id="due_installment_amount"
                    name="due_installment_amount"
                    value="{{ ($data['amountForInst'] + $data['interestForInst']) * $data['dueInsts'] ?? '0.00' }}"
                    readonly>
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

</div>
<div class="row">

    <div class="form-group col-md-6">
        <div class="input-group colorpickerinput" style="padding-top: 1.5rem; padding-left: 2rem;">
            <label class="switch">
                <input type="checkbox" id="pay_total" name="pay_total">
                <span class="slider round"></span>
            </label>
            <label style="padding-left: 1.5rem; padding-top: 3px; font-weight: bold;"
                for="pay_total">{{ __('Pay Total') }}</label>
        </div>
    </div>

    <div class="form-group col-md-2" id="release_interest_div">
        <div class="input-group colorpickerinput" style="padding-top: 1.5rem;">
            <label class="switch">
                <input type="checkbox" id="release_interest" name="release_interest">
                <span class="slider round"></span>
            </label>
            <label style="padding-left: 10px; padding-top: 3px;"
                for="release_interest">{{ __('Release Interest') }}</label>
        </div>
    </div>

    <div class="form-group col-md-6" id="pay_total_amount_div">
        {{ Form::label('pay_total_amount', __('Payment Amount')) }}
        <div class="input-group colorpickerinput">
            <input type="number" class="form-control" step="0.01" id="pay_total_amount" name="pay_total_amount"
                value="" readonly>
            <div class="input-group-append">
                <div class="input-group-text">
                    {{ \Auth::user()->currencySymbol() }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
<div class="row">

    <div class="form-group col-md-6">
        <div class="input-group colorpickerinput" style="padding-bottom: 1.1rem; padding-left: 2rem;">
            <label class="switch">
                <input type="checkbox" id="add_penalty" name="add_penalty">
                <span class="slider round"></span>
            </label>
            <label style="padding-left: 1.5rem; padding-top: 3px; font-weight: bold;"
                for="add_penalty">{{ __('Add Penalty') }}</label>
        </div>
    </div>


    <div class="form-group col-md-6" id="add_penalty_amount_div">
        {{ Form::label('add_penalty_amount', __('Penalty Amount')) }}
        <div class="input-group colorpickerinput">
            <input type="number" class="form-control" step="0.01" id="base_penalty_amount"
                name="base_penalty_amount" value="" hidden>
            <input type="number" class="form-control" step="0.01" id="add_penalty_amount"
                name="add_penalty_amount" value="">
            <div class="input-group-append">
                <div class="input-group-text">
                    {{ \Auth::user()->currencySymbol() }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    @if (isset($installments->fine))
        <div class="form-group col-md-6">
            {{ Form::label('fine_amount', __('Fine Amount')) }}
            <div class="input-group colorpickerinput">
                <input type="hidden" id="pre_fine_amount" value="{{ $installments->fine }}">
                {{ Form::number('fine_amount', $installments->fine, ['class' => 'form-control fine', 'required' => 'required', 'readonly' => 'readonly', 'step' => '0.01']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <div class="input-group colorpickerinput" style="padding-top: 1.5rem;">
                <label class="switch">
                    <input type="checkbox" id="release_fine" name="release_fine">
                    <span class="slider round"></span>
                </label>
                <label style="padding-left: 1.5rem; padding-top: 3px;"
                    for="release_fine">{{ __('Release Fine Amount') }}</label>
            </div>
        </div>

        @php
            $totAmount = $installments->amount + $installments->fine - $installments->released_amount;
        @endphp

</div>
<div class="row">
@else
    @php
        $totAmount = $installments->amount - $installments->released_amount;
    @endphp
    @endif

    @if ($installments->released_amount != null)
        <div class="form-group col-md-6">
            {{ Form::label('excess_amount', __('Excess Amount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('excess_amount', $installments->released_amount ?? '0.00', ['class' => 'form-control released_amount', 'required' => 'required', 'readonly' => 'readonly', 'step' => '0.01']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($installments->remainder_amount != null)
        <div class="form-group col-md-6">
            {{ Form::label('remainder_amount', __('Remainder Amount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('remainder_amount', $installments->remainder_amount, ['class' => 'form-control remainder_amount', 'required' => 'required', 'readonly' => 'readonly', 'step' => '0.01']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
        @php
            $totAmount += $installments->remainder_amount;
        @endphp
    @endif

</div>

<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('total_amount_tobe_paid', __('Total Amount To Be Paid'), ['style' => 'font-weight: bold;']) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('total_amount_tobe_paid', $totAmount, [
                'class' => 'form-control tot_amount',
                'required' => 'required',
                'step' => '0.01',
                'readonly' => 'readonly',
                'style' => 'font-weight: bold;',
                'id' => 'total_amount_tobe_paid',
                'data-base-total' => $totAmount,
            ]) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{ \Auth::user()->currencySymbol() }}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('released_amount', __('Released Amount'), ['style' => 'color: red;']) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('released_amount', '0.00', ['class' => 'form-control released_amount', 'required' => 'required', 'step' => '0.01', 'style' => 'background-color: #ffe4e4;']) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{ \Auth::user()->currencySymbol() }}
                </div>
            </div>
        </div>
    </div>

    @php
        if ($installmentAdvances) {
            $payAmount = $installmentAdvances->sum('advance_amount');
        } else {
            $payAmount = 0;
        }
    @endphp

    <div class="form-group col-md-6">
        {{ Form::label('paid_amount', __('Paid Amount')) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('paid_amount', $payAmount, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01']) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{ \Auth::user()->currencySymbol() }}
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
            {{ Form::text('realised_date', '', ['class' => 'form-control datepicker', 'required' => 'required']) }}
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-12 cheque_number_div">
        {{ Form::label('cheque_number', __('Cheque Number')) }}
        {{ Form::text('cheque_number', null, ['class' => 'form-control cheque_number']) }}
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
        {{ Form::number('card_charge_amount', null, ['class' => 'form-control card_charge_amount', 'step' => '0.01']) }}
    </div>

    <div class="form-group col-md-6" id="total_amount_div">
        {{ Form::label('total_amount', __('Total Amount')) }}
        {{ Form::number('total_amount', null, ['class' => 'form-control total_amount', 'step' => '0.01', 'readonly' => 'readonly']) }}
    </div>

    <div class="form-group col-md-12">
        {{ Form::label('description', __('Description')) }}
        <div class="input-group">
            <textarea class="form-control" id="description" name="description" required>Installment</textarea>
        </div>
    </div>
</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="button" class="btn btn-primary" id="create_btn">{{ __('Pay') }}</button>
    {{-- {{Form::submit(__('Pay'),array('class'=>'btn btn-primary'))}} --}}
</div>
</div>

<script>
    $('#create_btn').on('click', function() {
        $(this).prop('disabled', true);
    });

    $(document).ready(function() {
        $('.cheque_number_div').hide();
        $('#card_type_div').hide();
        $('#card_charge_amount_div').hide();
        $('#total_amount_div').hide();
        $('#pay_total_amount_div').hide();
        $('#add_penalty_amount_div').hide();
        $('#release_interest_div').hide();


        if ($('#paid_amount').val() != '' || $('#paid_amount').val() != 0) {
            paidAmountInWords($('#paid_amount').val());
        }

        $('#paid_amount').on('input', function() {
            var amount = Number($(this).val());
            paidAmountInWords(amount);
        });
        $('#add_penalty_amount').on('input', function() {
            var basePenaltyAmount = parseFloat($('#base_penalty_amount').val()) || 0;
            var totalAmount = parseFloat($('#total_amount_tobe_paid').val()) || 0;
            var penaltyAmount = parseFloat($(this).val()) || 0;
            var payTotal = parseFloat($('#pay_total').val()) || 0;
            var newTotalAmount = (totalAmount - basePenaltyAmount) + penaltyAmount + payTotal;
            $('#total_amount_tobe_paid')
                .val(newTotalAmount.toFixed(2));
            $('#base_penalty_amount').val(penaltyAmount.toFixed(2));
            // if ($('#add_penalty_amount').val() == null || $('#add_penalty_amount').val().trim() ==
            //     '') {
            //     $('#add_penalty_amount').val(parseFloat(0));
            // }

        });

        $('#commonModal').on('shown.bs.modal', function() {
            $(document).off('focusin.modal');
        });

        $('#create_btn').on('click', function() {
            var payForm = $('#hire_purchase_contract_form');
            console.log('works');
            var amountToPay = $('#total_amount_tobe_paid').val();
            var paidAmount = $('#paid_amount').val();
            if ($('#next_installment_date').val() == '') {
                payForm.submit();
                $(".se-pre-con").css({
                    "display": "block"
                });
            } else if ($('#next_installment_date').val() != '') {
                if ($('#pay_total').prop("checked") == true || $('#release_fine').prop("checked") ==
                    true) {
                    (async () => {
                        const {
                            value: formValues
                        } = await Swal.fire({
                            icon: 'warning',
                            title: 'Authenication Needed!',
                            html: '<input type="text" placeholder="E mail" id="swal-input1" class="swal2-input">' +
                                '<input type="password" placeholder="Password" id="swal-input2" class="swal2-input">' +
                                '<input type="button" id="send" value="Send For Autherization" class="btn btn-success">',
                            focusConfirm: false,
                            showCancelButton: true,
                            confirmButtonText: 'Look up',
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                                return [
                                    document.getElementById('swal-input1')
                                    .value,
                                    document.getElementById('swal-input2').value
                                ]
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        })

                        if (formValues) {
                            $.ajax({
                                url: "{{ url('sale/authenticate') }}",
                                method: 'POST',
                                data: {
                                    login_details: formValues,
                                    "_token": "{{ csrf_token() }}"
                                },
                                dataType: 'json',
                                success: function(data, textStatus, jqXHR) {
                                    if (data == 'success') {
                                        payForm.submit();
                                        $(".se-pre-con").css({
                                            "display": "block"
                                        });
                                    } else if (data == 'email_error') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'The email address you entered is not correct. Please check your email address!'
                                        });
                                    } else if (data == 'user_error') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'You are not allowed to proceed this sale!'
                                        });
                                    } else if (data == 'password_error') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'The password you entered is not correct!'
                                        });
                                    }
                                }
                            });
                        }
                    })()
                } else {
                    payForm.submit();
                    $(".se-pre-con").css({
                        "display": "block"
                    });
                }
            }
        });
    });

    $('body').on('change', '#payment_method', function() {
        var method = $(this).val();
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
    });

    $('body').on('change', '#card_type', function() {
        var cardType = $(this).val();
        var paidAmount = $('#paid_amount').val();
        $.ajax({
            url: "{{ url('card_payment_rate/get_detail') }}/" + cardType,
            method: 'GET',
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                if (data != null) {
                    var cardCharge = parseFloat(paidAmount) * (parseFloat(data.rate) / 100);
                    var totalAmount = parseFloat(paidAmount) + cardCharge;
                    $('#card_charge_amount').val(cardCharge);
                    $('#total_amount').val(totalAmount);
                    paidAmountInWords(totalAmount);
                }
            }
        });
    });

    $('body').on('input', '#card_charge_amount', function() {
        var cardCharges = $(this).val();
        var paidAmounts = $('#paid_amount').val();
        var totalAmounts = parseFloat(paidAmounts) + parseFloat(cardCharges);
        $('#total_amount').val(totalAmounts);
        paidAmountInWords(totalAmounts);
    });

    $('body').on('change', '#release_fine', function() {
        var status = $(this);
        var fineAmount = $('#fine_amount').val();
        var preFineAmount = parseFloat($('#pre_fine_amount').val());
        var totalAmount = $('#total_amount_tobe_paid').val();
        var newTotalAmount = 0;
        var newReleasedAmount = 0;
        if (status.prop("checked") == true) {
            newTotalAmount = parseFloat(totalAmount) - parseFloat(fineAmount);
            $('#fine_amount').val(0);
            $('#total_amount_tobe_paid').val(newTotalAmount.toFixed(2));
        } else if (status.prop("checked") == false) {
            newTotalAmount = parseFloat(totalAmount) + preFineAmount;
            $('#fine_amount').val(preFineAmount.toFixed(2));
            $('#total_amount_tobe_paid').val(newTotalAmount.toFixed(2));
        }
    });

    $('body').off('change', '#pay_total').on('change', '#pay_total', function() {
        var status = $(this);
        var fineAmount = 0;
        var remainderAmount = 0;
        if ($('#fine_amount').length > 0 && $('#fine_amount').val() != '') {
            var fineAmount = $('#fine_amount').val();
        }
        if ($('#remainder_amount').length > 0 && $('#remainder_amount').val() != '') {
            var remainderAmount = $('#remainder_amount').val();
        }
        if ($('#excess_amount').length > 0) {
            var excessAmount = $('#excess_amount').val();
        } else {
            var excessAmount = 0;
        }
        var penaltyValue = 0;
        if ($('#add_penalty').prop("checked") == true) {
            penaltyValue = $('#add_penalty_amount').val() || 0;
        }
        var installmentAmount = $('#installment_amount').val();
        var dueSaleValue = $('#due_sale_value').val();
        var dueInterestValue = $('#due_interest_value').val();
        var paymentValue = 0;
        var newTotalValue = 0;
        if (status.prop("checked") == true) {
            paymentValue = parseFloat(installmentAmount) + parseFloat(dueSaleValue) + parseFloat(
                dueInterestValue);
            newTotalValue = paymentValue + parseFloat(penaltyValue) + parseFloat(remainderAmount) + parseFloat(
                fineAmount) - parseFloat(
                excessAmount);
            $('#pay_total_amount').val(paymentValue.toFixed(2));
            $('#pay_total_amount_div').show(200);
            $('#release_interest_div').hide(200);
            $('#release_interest').prop('checked', true);
            $('#total_amount_tobe_paid').val(newTotalValue.toFixed(2));
        } else if (status.prop("checked") == false) {
            newTotalValue = parseFloat(installmentAmount) + parseFloat(penaltyValue) + parseFloat(
                remainderAmount) + parseFloat(
                fineAmount) - parseFloat(excessAmount);
            $('#pay_total_amount').val('');
            $('#pay_total_amount_div').hide(200);
            $('#release_interest').prop('checked', false);
            $('#release_interest_div').hide(200);
            $('#total_amount_tobe_paid').val(newTotalValue.toFixed(2));
        }
    });

    $('body').off('change', '#add_penalty').on('change', '#add_penalty', function() {
        var status = $(this);
        var penaltyValue = 1500;
        var totalAmount = 0;
        totalAmount = parseFloat($('#total_amount_tobe_paid').val()) || 0;

        if (status.prop("checked") == true) {
            var newTotalAmount = 0;
            newTotalAmount = totalAmount + penaltyValue;
            $('#add_penalty_amount').val(penaltyValue.toFixed(2));
            $('#add_penalty_amount_div').show(200);
            $('#total_amount_tobe_paid').val(newTotalAmount.toFixed(2));
            $('#base_penalty_amount').val(penaltyValue.toFixed(2));
        } else if (status.prop("checked") == false) {
            var newTotalAmount = 0;
            newTotalAmount = totalAmount - parseFloat($('#add_penalty_amount').val() || 0);
            $('#add_penalty_amount').val(0);
            $('#add_penalty_amount_div').hide(200);
            $('#total_amount_tobe_paid').val(newTotalAmount.toFixed(2));
            $('#base_penalty_amount').val(0);
        }
    });

    // $('body').off('change', '#add_penalty_amount').on('input', '#add_penalty_amount', function() {
    //     var basePenaltyAmount = parseFloat($('#base_penalty_amount').val()) || 0;
    //     var totalAmount = parseFloat($('#total_amount_tobe_paid').val()) || 0;
    //     var penaltyAmount = parseFloat($('#add_penalty_amount').val()) || 0;
    //     var payTotal = parseFloat($('#pay_total').val()) || 0;
    //     console.log(totalAmount, basePenaltyAmount);
    //     var newTotalAmount = totalAmount + penaltyAmount + payTotal;
    //     $('#total_amount_tobe_paid').val(newTotalAmount.toFixed(2));
    //     $('#base_penalty_amount').val(penaltyAmount.toFixed(2));
    // });

    $('body').on('change', '#release_interest', function() {
        var status = $(this);
        var fineAmount = 0;
        var remainderAmount = 0;
        if ($('#fine_amount').length > 0 && $('#fine_amount').val() != '') {
            var fineAmount = $('#fine_amount').val();
        }
        if ($('#remainder_amount').length > 0 && $('#remainder_amount').val() != '') {
            var remainderAmount = $('#remainder_amount').val();
        }

        if ($('#excess_amount').length > 0) {
            var excessAmount = $('#excess_amount').val();
        } else {
            var excessAmount = 0;
        }
        var installmentAmount = $('#installment_amount').val();
        var dueSaleValue = $('#due_sale_value').val();
        var dueInterestValue = $('#due_interest_value').val();
        var paymentValue = 0;
        var newTotalValue = 0;
        if (status.prop("checked") == true) {
            paymentValue = parseFloat(installmentAmount) + parseFloat(dueSaleValue) + parseFloat(
                dueInterestValue);
            newTotalValue = paymentValue + parseFloat(remainderAmount) + parseFloat(fineAmount) - parseFloat(
                excessAmount);
            $('#pay_total_amount').val(paymentValue.toFixed(2));
            $('#total_amount_tobe_paid').val(newTotalValue.toFixed(2));
        } else if (status.prop("checked") == false) {
            paymentValue = parseFloat(installmentAmount) + parseFloat(dueSaleValue) + parseFloat(
                dueInterestValue);
            newTotalValue = paymentValue + parseFloat(remainderAmount) + parseFloat(fineAmount) - parseFloat(
                excessAmount);
            $('#pay_total_amount').val(paymentValue.toFixed(2));
            $('#total_amount_tobe_paid').val(newTotalValue.toFixed(2));
        }
    });

    $(document).on('click', '#send', function() {
        $('#hire_purchase_contract_form').attr('action', '{{ route('waitting_installment.create') }}');
        $('#hire_purchase_contract_form').submit();
    });


    function paidAmountInWords(amount) {
        $("#paid_amount_in_words").html('');
        var words = '';
        words = toWords(amount);
        $("#paid_amount_in_words").html(words + "Only");
    }

    $('body').off('click', '#add_advance_btn').on('click', '#add_advance_btn', function() {
        const installment_id = $(this).val();
        const advance_amount = $('#advance_amount').val();
        const advance_payment_method = $('#advance_payment_method').val();

        if (advance_amount == '') {
            toastrs('Error', 'Please Advance Amount', 'error');
        } else if (advance_payment_method == '') {
            toastrs('Error', 'Please Select Payment Method', 'error');
        } else {
            $.ajax({
                url: '{{ route('hire_purchase_contract.add_advance_amount') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    installment_id: installment_id,
                    advance_amount: advance_amount,
                    advance_payment_method: advance_payment_method
                },
                success: function(response) {
                    console.log(response);
                    toastrs('Done!', 'Advance payment added successfully!', 'success');
                },
                complete: function() {
                    getAdvanceAmount(installment_id);
                    $('#advance_amount').val('');
                    $('#advance_payment_method').val('');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    });

    function getAdvanceAmount(installment_id) {
        $.ajax({
            url: '{{ route('hire_purchase_contract.get_advance_amount') }}',
            type: 'POST',
            dataType: 'JSON',
            data: {
                _token: '{{ csrf_token() }}',
                installment_id: installment_id
            },
            success: function(response) {
                console.log(response);
                if (response.length > 0) {

                    let row = '';
                    let totalAdvance = 0;
                    for (let i = 0; i < response.length; i++) {
                        totalAdvance += parseFloat(response[i].advance_amount);

                        row += `<tr>
                                    <td>
                                        <div class="input-group colorpickerinput">
                                            <input type="number" class="form-control edit_advance_amount" name="edit_advance_amount" value="${response[i].advance_amount}">
                                            <div class="input-group-append">
                                                <div class="input-group-text">LKR</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control edit_advance_payment_method" name="edit_advance_payment_method">
                                            <option value="">Select Payment Method</option>
                                            <option value="CASH" ${response[i].advance_payment_method == "CASH" ? 'selected' : ''}>Cash</option>
                                            <option value="ONLINEPAYMENT" ${response[i].advance_payment_method == "ONLINEPAYMENT" ? 'selected' : ''}>Online Payment</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-action add_advance_print" title="Print" value="${response[i].id}">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-action remove_advance_btn" title="Remove" value="${response[i].id}" data-installment_id="${installment_id}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>`;
                    }

                    console.log(totalAdvance);

                    $('#paid_amount').val(totalAdvance);
                    $('#existing_advance_table_body').html(row);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }

    $('body').on('click', '.edit_advance_btn', function() {
        const installment_advance_id = $(this).val();
        const advance_amount = $(this).closest('tr').find('.edit_advance_amount').val();
        const advance_payment_method = $(this).closest('tr').find('.edit_advance_payment_method').val();

        console.log(advance_amount);
        console.log(advance_payment_method);

        if (advance_amount == '') {
            toastrs('Error', 'Please Advance Amount', 'error');
        } else if (advance_payment_method == '') {
            toastrs('Error', 'Please Select Payment Method', 'error');
        } else {
            $.ajax({
                url: '{{ route('hire_purchase_contract.edit_advance_amount') }}',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    _token: '{{ csrf_token() }}',
                    installment_advance_id: installment_advance_id,
                    advance_amount: advance_amount,
                    advance_payment_method: advance_payment_method
                },
                success: function(response) {
                    console.log(response);
                    toastrs('Done!', 'Advance payment updated successfully!', 'success');

                    console.log(response);
                    if (response.length > 0) {

                        let totalAdvance = 0;
                        for (let i = 0; i < response.length; i++) {
                            totalAdvance += parseFloat(response[i].advance_amount);
                        }

                        console.log(totalAdvance);

                        $('#paid_amount').val(totalAdvance);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    });

    $('body').on('click', '.remove_advance_btn', function() {
        const installment_advance_id = $(this).val();
        const installment_id = $(this).attr('data-installment_id');

        $.ajax({
            url: '{{ route('hire_purchase_contract.remove_advance_amount') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                installment_advance_id: installment_advance_id
            },
            success: function(response) {
                console.log(response);
                getAdvanceAmount(installment_id);
                toastrs('Done!', 'Advance payment removed successfully!', 'success');
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    });
</script>
