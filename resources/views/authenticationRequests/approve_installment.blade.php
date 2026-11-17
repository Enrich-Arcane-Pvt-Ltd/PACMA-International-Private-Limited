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

    <input type="hidden" name="waitting_installment_id" value="{{ $waittingInstallment->id }}">
    <input type="hidden" name="created_by" value="{{ $waittingInstallment->created_by }}">
    <input type="hidden" id="sale_id" name="sale_id" value="{{ $waittingInstallment->sale_id }}">
    <input type="hidden" id="installment_id" name="installment_id" value="{{ $waittingInstallment->installment_id }}">
    <input type="hidden" id="invoice_id" name="invoice_id" value="{{ $waittingInstallment->invoice_id }}">
    <input type="hidden" id="customer_id" name="customer_id" value="{{ $waittingInstallment->customer_id }}">
    <input type="hidden" id="next_installment_date" name="next_installment_date"
        value="{{ $waittingInstallment->next_installment_date ?? '' }}">
    <div class="form-group col-md-6">
        {{ Form::label('current_date', __('Current Date')) }}
        <div class="input-group">
            {{ Form::text('current_date', $waittingInstallment->date, ['class' => 'form-control datepicker', 'required' => 'required']) }}
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
                value="{{ $waittingInstallment->installment_due_date }}" readonly>
        </div>
    </div>

    {{-- <div class="form-group col-md-6">
        {{ Form::label('customer', __('Customer')) }}
        <div class="input-group">
            <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ $sales->customer->name }}" readonly>
        </div>
    </div> --}}

    <div class="form-group col-md-4">
        {{ Form::label('this_sale_value', __('Item Value for Installment')) }}
        <div class="input-group colorpickerinput">
            <input type="number" class="form-control" step="0.01" id="this_sale_value" name="this_sale_value"
                value="{{ $waittingInstallment->this_sale_value }}" readonly>
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
                name="this_interest_value" value="{{ $waittingInstallment->this_interest_value }}" readonly>
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
                value="{{ $waittingInstallment->installment_amount }}" readonly>
            <div class="input-group-append">
                <div class="input-group-text">
                    {{ \Auth::user()->currencySymbol() }}
                </div>
            </div>
        </div>
    </div>

    @if ($waittingInstallment->next_installment_date != null)
        <div class="form-group col-md-4">
            {{ Form::label('due_sale_value', __('Due Item Value')) }}
            <div class="input-group colorpickerinput">
                <input type="number" class="form-control" step="0.01" id="due_sale_value" name="due_sale_value"
                    value="{{ $waittingInstallment->due_sale_value }}" readonly>
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
                    name="due_interest_value" value="{{ $waittingInstallment->due_interest_value }}" readonly>
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
                    name="due_installment_amount" value="{{ $waittingInstallment->due_installment_amount }}" readonly>
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
                <input type="checkbox" id="pay_total" name="pay_total"
                    @if ($waittingInstallment->pay_total == 1) checked @endif>
                <span class="slider round"></span>
            </label>
            <label style="padding-left: 1.5rem; padding-top: 3px; font-weight: bold;"
                for="pay_total">{{ __('Pay Total') }}</label>
        </div>
    </div>

    <div class="form-group col-md-3" id="release_interest_div">
        <div class="input-group colorpickerinput" style="padding-top: 1.5rem;">
            <label class="switch">
                <input type="checkbox" id="release_interest" name="release_interest"
                    @if ($waittingInstallment->release_interest == 1) checked @endif>
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
                value="{{ $waittingInstallment->pay_total_amount }}" readonly>
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
                <input type="checkbox" id="add_penalty" name="add_penalty"
                    @if (floatval($waittingInstallment->penalty_charge) > 0) checked @endif>
                <span class="slider round"></span>
            </label>
            <label style="padding-left: 1.5rem; padding-top: 3px; font-weight: bold;"
                for="add_penalty">{{ __('Add Penalty') }}</label>
        </div>
    </div>


    <div class="form-group col-md-6" id="add_penalty_amount_div">
        {{ Form::label('add_penalty_amount', __('Penalty Amount')) }}
        <div class="input-group colorpickerinput">
            <input type="number" class="form-control" step="0.01" id="add_penalty_amount"
                name="add_penalty_amount" value="{{ $waittingInstallment->penalty_charge }}" readonly>
            <div class="input-group-append">
                <div class="input-group-text">
                    {{ \Auth::user()->currencySymbol() }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">

    @if ($waittingInstallment->installment_status == 3)
        <div class="form-group col-md-6">
            {{ Form::label('fine_amount', __('Fine Amount')) }}
            <div class="input-group colorpickerinput">
                <input type="hidden" id="pre_fine_amount" value="{{ $waittingInstallment->pre_fine_amount }}">
                {{ Form::number('fine_amount', $waittingInstallment->fine_amount, ['class' => 'form-control fine', 'required' => 'required', 'readonly' => 'readonly', 'step' => '0.01']) }}
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
                    <input type="checkbox" id="release_fine" name="release_fine"
                        @if ($waittingInstallment->release_fine == 1) checked @endif>
                    <span class="slider round"></span>
                </label>
                <label style="padding-left: 1.5rem; padding-top: 3px;"
                    for="release_fine">{{ __('Release Fine Amount') }}</label>
            </div>
        </div>

        {{-- <div class="form-group col-md-6">
            {{ Form::label('total_amount_tobe_paid', __('Total Amount To Be Paid')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('total_amount_tobe_paid',$totAmount, array('class' => 'form-control tot_amount','required'=>'required','step'=>'0.01')) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{\Auth::user()->currencySymbol()}}
                    </div>
                </div>
            </div>
        </div> --}}
</div>
<div class="row">
    @endif

    @if ($waittingInstallment->excess_amount != null)
        <div class="form-group col-md-6">
            {{ Form::label('excess_amount', __('Excess Amount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('excess_amount', $waittingInstallment->excess_amount ?? '0.00', ['class' => 'form-control released_amount', 'required' => 'required', 'readonly' => 'readonly', 'step' => '0.01']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($waittingInstallment->remainder_amount != null)
        <div class="form-group col-md-6">
            {{ Form::label('remainder_amount', __('Remainder Amount')) }}
            <div class="input-group colorpickerinput">
                {{ Form::number('remainder_amount', $waittingInstallment->remainder_amount, ['class' => 'form-control remainder_amount', 'required' => 'required', 'readonly' => 'readonly', 'step' => '0.01']) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ \Auth::user()->currencySymbol() }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="form-group col-md-6">
        {{ Form::label('total_amount_tobe_paid', __('Total Amount To Be Paid'), ['style' => 'font-weight: bold;']) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('total_amount_tobe_paid', $waittingInstallment->total_amount_tobe_paid, ['class' => 'form-control tot_amount', 'required' => 'required', 'step' => '0.01', 'readonly' => 'readonly', 'style' => 'font-weight: bold;']) }}
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
            {{ Form::number('released_amount', $waittingInstallment->released_amount ?? '0.00', ['class' => 'form-control released_amount', 'required' => 'required', 'step' => '0.01', 'style' => 'background-color: #ffe4e4;']) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{ \Auth::user()->currencySymbol() }}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('paid_amount', __('Paid Amount')) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('paid_amount', $waittingInstallment->paid_amount, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01']) }}
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
            <textarea class="form-control paid_amount_in_words" id="paid_amount_in_words" name="paid_amount_in_words" required>{{ $waittingInstallment->paid_amount_in_words }}</textarea>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('payment_method', __('Payment Method')) }}
        <select class="form-control payment_method" name="payment_method" id="payment_method" required>
            <option value="">Select Payment Method</option>
            <option @if ($waittingInstallment->payment_method == 'CASH') selected @endif value="CASH">Cash</option>
            <option @if ($waittingInstallment->payment_method == 'CHEQUE') selected @endif value="CHEQUE">Cheque</option>
            <option @if ($waittingInstallment->payment_method == 'CARD') selected @endif value="CARD">Card</option>
            <option @if ($waittingInstallment->payment_method == 'ONLINEPAYMENT') selected @endif value="ONLINEPAYMENT">Online Payment</option>
        </select>
    </div>

    <div class="form-group col-md-6 cheque_number_div">
        {{ Form::label('realised_date', __('Realised Date')) }}
        <div class="input-group">
            {{ Form::text('realised_date', $waittingInstallment->realised_date, ['class' => 'form-control datepicker', 'required' => 'required']) }}
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-12 cheque_number_div">
        {{ Form::label('cheque_number', __('Cheque Number')) }}
        {{ Form::text('cheque_number', $waittingInstallment->cheque_number, ['class' => 'form-control cheque_number']) }}
    </div>

    <div class="form-group col-md-6" id="card_type_div">
        {{ Form::label('card_type', __('Card Type')) }}
        <select class="form-control card_type" name="card_type" id="card_type">
            <option value="">Select Card Type</option>
            <option @if ($waittingInstallment->card_type == 1) selected @endif value="1">Credit Card</option>
            <option @if ($waittingInstallment->card_type == 2) selected @endif value="2">Debit Card</option>
        </select>
    </div>

    <div class="form-group col-md-6" id="card_charge_amount_div">
        {{ Form::label('card_charge_amount', __('Card Charges')) }}
        {{ Form::number('card_charge_amount', $waittingInstallment->card_charge_amount, ['class' => 'form-control card_charge_amount', 'step' => '0.01']) }}
    </div>

    <div class="form-group col-md-6" id="total_amount_div">
        {{ Form::label('total_amount', __('Total Amount')) }}
        {{ Form::number('total_amount', $waittingInstallment->total_amount, ['class' => 'form-control total_amount', 'step' => '0.01', 'readonly' => 'readonly']) }}
    </div>

    <div class="form-group col-md-12">
        {{ Form::label('description', __('Description')) }}
        <div class="input-group">
            <textarea class="form-control" id="description" name="description" required>{{ $waittingInstallment->description }}</textarea>
        </div>
    </div>
</div>

<div class="col-md-12 text-center">
    <button type="submit" class="btn btn-primary" id="approve_btn">{{ __('Approve') }}</button>
    <button type="button" class="btn btn-danger" id="reject_btn">{{ __('Reject') }}</button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal"
        id="cancel_btn">{{ __('Cancel') }}</button>
</div>
</div>

<script>
    var waittingInstallment = <?php echo json_encode($waittingInstallment); ?>;
    $(document).ready(function() {
        $('.cheque_number_div').hide();
        $('#card_type_div').hide();
        $('#card_charge_amount_div').hide();
        $('#total_amount_div').hide();


        $('#payment_method').val(waittingInstallment.payment_method).trigger('change');
        $('#paid_amount').val(waittingInstallment.paid_amount);
        $('#paid_amount_in_words').html(waittingInstallment.paid_amount_in_words);

        var paymentMethod = waittingInstallment.payment_method;
        changeMethod(paymentMethod);

        if (waittingInstallment.pay_total == 1) {
            $('#pay_total_amount_div').show();
            $('#release_interest_div').hide();
        } else {
            $('#pay_total_amount_div').hide();
            $('#release_interest_div').hide();
        }

        $('#card_type').val(waittingInstallment.card_type).trigger('change');
        // $('#paid_amount').on('input', function(){
        //     // $("#paid_amount_in_words").html('');
        //     var amount = Number($(this).val());
        //     // var words = '';
        //     // words = toWords(amount);
        //     // $("#paid_amount_in_words").html(words + "Only");
        //     paidAmountInWords(amount);
        // });

        $('#commonModal').on('shown.bs.modal', function() {
            $(document).off('focusin.modal');
        });

        $("#hire_purchase_contract_form :input").prop("disabled", true);
        $('#approve_btn').prop('disabled', false);
        $('#reject_btn').prop('disabled', false);
        $('#cancel_btn').prop('disabled', false);
        // $('#hire_purchase_contract_form').on('submit', function(e){
        //     e.preventDefault();
        //     var payForm = this;
        //     console.log('works');
        //     var amountToPay = $('#total_amount_tobe_paid').val();
        //     var paidAmount = $('#paid_amount').val();
        //     if( $('#next_installment_date').val() == '' ){
        //         // if( parseFloat(paidAmount) < parseFloat(amountToPay) ){
        //         //     (async () => {
        //         //         const { value: formValues } = await Swal.fire({
        //         //             icon: 'warning',
        //         //             title: 'Authenication Needed!',
        //         //             html:
        //         //                 '<input type="text" placeholder="E mail" id="swal-input1" class="swal2-input">' +
        //         //                 '<input type="password" placeholder="Password" id="swal-input2" class="swal2-input">',
        //         //             focusConfirm: false,
        //         //             showCancelButton: true,
        //         //             confirmButtonText: 'Look up',
        //         //             showLoaderOnConfirm: true,
        //         //             preConfirm: () => {
        //         //                 return [
        //         //                 document.getElementById('swal-input1').value,
        //         //                 document.getElementById('swal-input2').value
        //         //                 ]
        //         //             },
        //         //             allowOutsideClick: () => !Swal.isLoading()
        //         //         })

        //         //         if (formValues) {
        //         //             $.ajax({
        //         //                 url:"{{ url('sale/authenticate') }}",
        //         //                 method: 'POST',
        //         //                 data: { login_details: formValues, "_token": "{{ csrf_token() }}"},
        //         //                 dataType: 'json',
        //         //                 success: function(data, textStatus, jqXHR){
        //         //                     if(data == 'success'){
        //         //                         payForm.submit();
        //         //                         $(".se-pre-con").css({"display": "block"});
        //         //                     } else if(data == 'email_error'){
        //         //                         Swal.fire({
        //         //                             icon: 'error',
        //         //                             title: 'Oops...',
        //         //                             text: 'The email address you entered is not correct. Please check your email address!'
        //         //                         });
        //         //                     } else if(data == 'user_error'){
        //         //                         Swal.fire({
        //         //                             icon: 'error',
        //         //                             title: 'Oops...',
        //         //                             text: 'You are not allowed to proceed this sale!'
        //         //                         });
        //         //                     } else if(data == 'password_error'){
        //         //                         Swal.fire({
        //         //                             icon: 'error',
        //         //                             title: 'Oops...',
        //         //                             text: 'The password you entered is not correct!'
        //         //                         });
        //         //                     }
        //         //                 }
        //         //             });
        //         //         }
        //         //     })()

        //         // } else {
        //             payForm.submit();
        //             $(".se-pre-con").css({"display": "block"});
        //         // }
        //     } else if( $('#next_installment_date').val() != '' ){
        //         if( $('#pay_total').prop("checked") == true || $('#release_fine').prop("checked") == true ){
        //             (async () => {
        //                 const { value: formValues } = await Swal.fire({
        //                     icon: 'warning',
        //                     title: 'Authenication Needed!',
        //                     html:
        //                         '<input type="text" placeholder="E mail" id="swal-input1" class="swal2-input">' +
        //                         '<input type="password" placeholder="Password" id="swal-input2" class="swal2-input">',
        //                         '<input type="button" id="send" value="Send For Autherization" class="btn btn-success">',
        //                     focusConfirm: false,
        //                     showCancelButton: true,
        //                     confirmButtonText: 'Look up',
        //                     showLoaderOnConfirm: true,
        //                     preConfirm: () => {
        //                         return [
        //                         document.getElementById('swal-input1').value,
        //                         document.getElementById('swal-input2').value
        //                         ]
        //                     },
        //                     allowOutsideClick: () => !Swal.isLoading()
        //                 })

        //                 if (formValues) {
        //                     $.ajax({
        //                         url:"{{ url('sale/authenticate') }}",
        //                         method: 'POST',
        //                         data: { login_details: formValues, "_token": "{{ csrf_token() }}"},
        //                         dataType: 'json',
        //                         success: function(data, textStatus, jqXHR){
        //                             if(data == 'success'){
        //                                 payForm.submit();
        //                                 $(".se-pre-con").css({"display": "block"});
        //                             } else if(data == 'email_error'){
        //                                 Swal.fire({
        //                                     icon: 'error',
        //                                     title: 'Oops...',
        //                                     text: 'The email address you entered is not correct. Please check your email address!'
        //                                 });
        //                             } else if(data == 'user_error'){
        //                                 Swal.fire({
        //                                     icon: 'error',
        //                                     title: 'Oops...',
        //                                     text: 'You are not allowed to proceed this sale!'
        //                                 });
        //                             } else if(data == 'password_error'){
        //                                 Swal.fire({
        //                                     icon: 'error',
        //                                     title: 'Oops...',
        //                                     text: 'The password you entered is not correct!'
        //                                 });
        //                             }
        //                         }
        //                     });
        //                 }
        //             })()
        //         } else {
        //             payForm.submit();
        //             $(".se-pre-con").css({"display": "block"});
        //         }
        //     }
        // });
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

    // $('body').on('change', '#release_fine', function(){
    //     var status = $(this);
    //     var fineAmount = $('#fine_amount').val();
    //     var preFineAmount = parseFloat($('#pre_fine_amount').val());
    //     var totalAmount = $('#total_amount_tobe_paid').val();
    //     var newTotalAmount = 0;
    //     var newReleasedAmount = 0;
    //     if(status.prop("checked") == true){
    //         newTotalAmount = parseFloat(totalAmount) - parseFloat(fineAmount);
    //         // newReleasedAmount = parseFloat(releasedAmount) + parseFloat(fineAmount);
    //         // $('#released_amount').val(newReleasedAmount.toFixed(2));
    //         $('#fine_amount').val(0);
    //         $('#total_amount_tobe_paid').val(newTotalAmount.toFixed(2));

    //             // (async () => {
    //             //         const { value: formValues } = await Swal.fire({
    //             //             icon: 'warning',
    //             //             title: 'Authenication Needed!',
    //             //             html:
    //             //                 '<input type="text" placeholder="E mail" id="swal-input1" class="swal2-input">' +
    //             //                 '<input type="password" placeholder="Password" id="swal-input2" class="swal2-input">',
    //             //             focusConfirm: false,
    //             //             showCancelButton: true,
    //             //             confirmButtonText: 'Look up',
    //             //             showLoaderOnConfirm: true,
    //             //             preConfirm: () => {
    //             //                 return [
    //             //                 document.getElementById('swal-input1').value,
    //             //                 document.getElementById('swal-input2').value
    //             //                 ]
    //             //             },
    //             //             allowOutsideClick: () => !Swal.isLoading()
    //             //         })

    //             //         if (formValues) {
    //             //             $.ajax({
    //             //                 url:"{{ url('sale/authenticate') }}",
    //             //                 method: 'POST',
    //             //                 data: { login_details: formValues, "_token": "{{ csrf_token() }}"},
    //             //                 dataType: 'json',
    //             //                 success: function(data, textStatus, jqXHR){
    //             //                     if(data == 'success'){
    //             //                         newTotalAmount = parseFloat(totalAmount) - parseFloat(fineAmount);
    //             //                         // newReleasedAmount = parseFloat(releasedAmount) + parseFloat(fineAmount);
    //             //                         // $('#released_amount').val(newReleasedAmount.toFixed(2));
    //             //                         $('#fine_amount').val(0);
    //             //                         $('#total_amount_tobe_paid').val(newTotalAmount.toFixed(2));

    //             //                         Swal.fire({
    //             //                             icon: 'success',
    //             //                             title: 'Success',
    //             //                             text: 'The fine removed successfully!'
    //             //                         });
    //             //                     } else if(data == 'email_error'){
    //             //                         status.prop("checked", false);
    //             //                         Swal.fire({
    //             //                             icon: 'error',
    //             //                             title: 'Oops...',
    //             //                             text: 'The email address you entered is not correct. Please check your email address!'
    //             //                         });
    //             //                     } else if(data == 'user_error'){
    //             //                         status.prop("checked", false);
    //             //                         Swal.fire({
    //             //                             icon: 'error',
    //             //                             title: 'Oops...',
    //             //                             text: 'You are not allowed to proceed this sale!'
    //             //                         });
    //             //                     } else if(data == 'password_error'){
    //             //                         status.prop("checked", false);
    //             //                         Swal.fire({
    //             //                             icon: 'error',
    //             //                             title: 'Oops...',
    //             //                             text: 'The password you entered is not correct!'
    //             //                         });
    //             //                     }
    //             //                 }
    //             //             });
    //             //         } else {
    //             //             status.prop("checked", false);
    //             //         }
    //             //     })()
    //         }
    //         else if(status.prop("checked") == false){
    //             newTotalAmount = parseFloat(totalAmount) + preFineAmount;
    //             // newReleasedAmount = parseFloat(releasedAmount) - parseFloat(fineAmount);
    //             // $('#released_amount').val(newReleasedAmount.toFixed(2));
    //             $('#fine_amount').val(preFineAmount.toFixed(2));
    //             $('#total_amount_tobe_paid').val(newTotalAmount.toFixed(2));
    //         }
    // });

    // $('body').on('change', '#pay_total', function(){
    //     var status = $(this);
    //     var fineAmount = 0;
    //     var remainderAmount = 0;
    //     if($('#fine_amount').length > 0 && $('#fine_amount').val() != ''){
    //         var fineAmount = $('#fine_amount').val();
    //     }
    //     if($('#remainder_amount').length > 0 && $('#remainder_amount').val() != ''){
    //         var remainderAmount = $('#remainder_amount').val();
    //     }

    //     var releasedAmount = $('#released_amount').val();
    //     var installmentAmount = $('#installment_amount').val();
    //     var dueSaleValue = $('#due_sale_value').val();
    //     var dueInterestValue = $('#due_interest_value').val();
    //     var paymentValue = 0;
    //     var newTotalValue = 0;
    //     if(status.prop("checked") == true){
    //         paymentValue = parseFloat(installmentAmount) + parseFloat(dueSaleValue);
    //         newTotalValue = paymentValue + parseFloat(remainderAmount) + parseFloat(fineAmount) - parseFloat(releasedAmount);
    //         $('#pay_total_amount').val(paymentValue.toFixed(2));
    //         $('#pay_total_amount_div').show(200);
    //         $('#release_interest_div').show(200);
    //         $('#release_interest').prop('checked', true);
    //         $('#total_amount_tobe_paid').val(newTotalValue.toFixed(2));
    //     }
    //     else if(status.prop("checked") == false){
    //         newTotalValue = parseFloat(installmentAmount) + parseFloat(remainderAmount) + parseFloat(fineAmount) - parseFloat(releasedAmount);
    //         $('#pay_total_amount').val('');
    //         $('#pay_total_amount_div').hide(200);
    //         $('#release_interest').prop('checked', false);
    //         $('#release_interest_div').hide(200);
    //         $('#total_amount_tobe_paid').val(newTotalValue.toFixed(2));
    //     }
    // });

    // $('body').on('change', '#release_interest', function(){
    //     var status = $(this);
    //     var fineAmount = 0;
    //     var remainderAmount = 0;
    //     if($('#fine_amount').length > 0 && $('#fine_amount').val() != ''){
    //         var fineAmount = $('#fine_amount').val();
    //     }
    //     if($('#remainder_amount').length > 0 && $('#remainder_amount').val() != ''){
    //         var remainderAmount = $('#remainder_amount').val();
    //     }

    //     var releasedAmount = $('#released_amount').val();
    //     var installmentAmount = $('#installment_amount').val();
    //     var dueSaleValue = $('#due_sale_value').val();
    //     var dueInterestValue = $('#due_interest_value').val();
    //     var paymentValue = 0;
    //     var newTotalValue = 0;
    //     if(status.prop("checked") == true){
    //         paymentValue = parseFloat(installmentAmount) + parseFloat(dueSaleValue);
    //         newTotalValue = paymentValue + parseFloat(remainderAmount) + parseFloat(fineAmount) - parseFloat(releasedAmount);
    //         $('#pay_total_amount').val(paymentValue.toFixed(2));
    //         $('#total_amount_tobe_paid').val(newTotalValue.toFixed(2));
    //     }
    //     else if(status.prop("checked") == false){
    //         paymentValue = parseFloat(installmentAmount) + parseFloat(dueSaleValue) + parseFloat(dueInterestValue);
    //         newTotalValue = paymentValue + parseFloat(remainderAmount) + parseFloat(fineAmount) - parseFloat(releasedAmount);
    //         $('#pay_total_amount').val(paymentValue.toFixed(2));
    //         $('#total_amount_tobe_paid').val(newTotalValue.toFixed(2));
    //     }
    // });

    $(document).on('click', '#reject_btn', function() {
        //Some code 1
        // console.log('Button 1');
        $(".se-pre-con").css({
            "display": "block"
        });
        $('#hire_purchase_contract_form').attr('action', '{{ route('waitting_installment.reject') }}');
        $('#hire_purchase_contract_form').submit();
        // swal.clickConfirm();
    });

    $('body').submit(function() {
        $("#hire_purchase_contract_form :input").prop("disabled", false);
        $(".se-pre-con").css({
            "display": "block"
        });
    });

    // function paidAmountInWords(amount){
    //     $("#paid_amount_in_words").html('');
    //     var words = '';
    //     words = toWords(amount);
    //     $("#paid_amount_in_words").html(words + "Only");
    // }
</script>
