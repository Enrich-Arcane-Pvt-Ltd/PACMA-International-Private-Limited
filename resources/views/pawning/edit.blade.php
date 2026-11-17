
{{-- @push('css-page')
<link href="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.css">
@endpush
<style>
    .image_container {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        width: 100%;
    }

    .image_container img {
        width: 100%;
    }

    @media (min-width: 576px) {
        .modal-dialog {
            width: 768px;
            max-width: 768px;
        }
    }

    .barcode_preview {
        width: 50%;
        height: auto;
    }

    textarea.form-control {
        height: auto !important;
    }
</style> --}}
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
{{-- @include('partials.admin.footer') --}}
<script src="{{asset('assets/js/jquery.repeater.min.js')}}"></script>

{{-- @push('script-page') --}}

<script>
    $(document).ready(function(){
        $("#time_period").change(function(){

            var timePeriod = $(this).val();
            // var timePeriod = $(this).children("option:selected").val();
            // alert("You have selected the country - " + selectedCountry);
            // $("#group").html("");
            // $('#group').append('<input type="text" class="timeperiod" type="text" name="select_country" value=" '+timePeriod+' " />');
            $('.timeperiod').val(timePeriod);

        });
    });
    </script>

    <script>
        var value = {!! json_encode($pawning->pawnedItem) !!};
        var selector = "body";
        if ($(selector + " .repeater").length) {
            var $dragAndDrop = $("body .repeater tbody").sortable({
                handle: '.sort-handler'
            });
            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: false,
                defaultValues: {
                    'status': 1
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
                        finalCalculation();

                    });
                },
                ready: function (setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });
            // var value = $(selector + " .repeater").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                // value = JSON.parse(value);
                console.log(value);
                $repeater.setList(value);
                for (var i = 0; i < value.length; i++) {
                    if(i > 0){
                        $('#add_row').trigger('click');
                    }
                    var tr = $('#sortable-table [name="items['+ i +'][id]"]').parent();
                    tr.find('.jewellery_id').val(value[i].jewellery_id);
                    tr.find('.gold_rate_type').val(value[i].gold_rate_type);
                    tr.find('.gold_rate_value').val(value[i].gold_type.gold_rate);
                    tr.find('.payment_rate_value').val(value[i].payment_rate_value);
                    tr.find('.weight').val(value[i].weight);
                    tr.find('.amountFinanced-input').val(value[i].amount_financed);
                    tr.find('.amountFinanced').html(value[i].amount_financed);
                    tr.find('.amountPayout').val(value[i].amount_payout);
                    tr.find('.financeCharge-input').val(value[i].financed_charge);
                    tr.find('.financeCharge').html(value[i].financed_charge);
                    tr.find('.totalOfPayment-input').val(value[i].total_of_payment);
                    tr.find('.totalOfPayment').html(value[i].total_of_payment);
                    tr.find('.prepayment-input').val(value[i].prepayment);
                    tr.find('.prepayment').html(value[i].prepayment);
                        finalCalculation();
                    // changeItem(tr.find('.item'));
                }
            }

        }


        $('body').on('change', '.gold_rate_type', function(){
        var gold_rate_type = $(this).val();
        var goldRateValue = $(this).closest('tr').find('.gold_rate_value');
        var paymentRateValue = $(this).closest('tr').find('.payment_rate_value');
        $.ajax({
            url:"{{ url('pawning/gold_rate_search') }}/"+gold_rate_type,
            method:'GET',
            dataType:'json',
                success: function (data, textStatus, jqXHR){
                    if(data.length > 0){
                        goldRateValue.val(data[0].gold_rate);
                        paymentRateValue.val(data[0].payment_rate);
                        // $('#group').append('<input type="text" name="" value="' + data.gold_rate_type  +' ">');
                        goldRateValue.prop('disabled', false);
                    }
                    else{
                        goldRateValue.val("");
                        paymentRateValue.val("");
                    }
                }
            });
        });

        $(document).on('input', '.amountPayout', function(){
            var el = $(this).closest('tr');

            if($('#time_period').val() == ''){
                Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please Select Time Period!'
                    });
                    $(this).val('');
                return this;
            }
            if($(el.find('.weight')).val() == ''){
                Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please Enter Weight!'
                    });
                    $(this).val('');
                return this;
            }
            var payment_rate_value = $(el.find('.payment_rate_value')).val();
            var amount = $(this).val();

            var paymentAmount = parseFloat(amount);

            firstCalculation(el, paymentAmount);
        });

        $(document).on('keyup', '.weight', function () {
            var el = $(this).closest('tr');

            if($('#time_period').val() == ''){
                Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please Select Time Period!'
                    });
                    $(this).val('');
                return this;
            }
            if($(el.find('.gold_rate_value')).val() == ''){
                Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please Select Gold Type!'
                    });
                    $(this).val('');
                return this;
            }

            // var el = $(this).parent().parent().parent().parent();
            var weight = $(this).val();
            var gold_rate_value = $(el.find('.gold_rate_value')).val();
            var payment_rate_value = $(el.find('.payment_rate_value')).val();
            // var time_period = $(el.find('.timeperiod')).val();

            // var discount = $(el.find('.discount')).val();

            // console.log(payment_rate_value);

            var amountFinanced = ((gold_rate_value * weight) / 8); // to find th amount financed need to gld rate * weight / 8 (8 means constant-> 1pund = 8g)
            var amount = (amountFinanced);
            $(el.find('.amountFinanced')).html(amount.toFixed(2));
            $(el.find('.amountFinanced-input')).val(amount.toFixed(2));

            var paymentAmount = amount*( parseFloat(payment_rate_value)/100 );
            $(el.find('.amountPayout')).val(paymentAmount.toFixed(2));

            firstCalculation(el, paymentAmount);
        });

        function firstCalculation(el, paymentAmount){
            var gold_interest = $('.gold_interest').val();
            var time_period = $('#time_period').val();
            var time_range = $('#time_range').val();

            var financeCharge = (paymentAmount * (gold_interest / 100) * parseFloat(time_period)); // to find the finance charge need to (amount finance * gold interest rate / 100)
            var financeChargers = (financeCharge);
            $(el.find('.financeCharge')).html(financeChargers.toFixed(2));
            $(el.find('.financeCharge-input')).val(financeChargers.toFixed(2));

            var totalOfPayment = (paymentAmount + financeChargers); // to find the total of payments charge need to (aamount financed + finance charge)
            var totalOfPayments = (totalOfPayment);
            $(el.find('.totalOfPayment')).html(totalOfPayments.toFixed(2));
            $(el.find('.totalOfPayment-input')).val(totalOfPayments.toFixed(2));

            if(time_range == 3){
                var prepayment = (totalOfPayments / time_period); // to find time period total of payments / time period
                var prepayments = (prepayment);
                $(el.find('.prepayment')).html(prepayments.toFixed(2));
                $(el.find('.prepayment-input')).val(prepayments.toFixed(2));
            } else if(time_range == 2){
                var prepayment = (totalOfPayments / (time_period*4)); // to find time period total of payments / time period
                var prepayments = (prepayment);
                $(el.find('.prepayment')).html(prepayments.toFixed(2));
                $(el.find('.prepayment-input')).val(prepayments.toFixed(2));
            } else if(time_range == 1){
                var prepayment = (totalOfPayments / (time_period*30)); // to find time period total of payments / time period
                var prepayments = (prepayment);
                $(el.find('.prepayment')).html(prepayments.toFixed(2));
                $(el.find('.prepayment-input')).val(prepayments.toFixed(2));
            }

            finalCalculation();
        }

        function finalCalculation(){
            var inputs = $(".amountFinanced"); // full total of financed
            var fullTotalAmountFinanced = 0;
            for (var i = 0; i < inputs.length; i++) {
                fullTotalAmountFinanced = parseFloat(fullTotalAmountFinanced) + parseFloat($(inputs[i]).html());
            }
            $('.totalAmountFinanced').html(fullTotalAmountFinanced.toFixed(2));
            $('.totalAmountFinanced-input').val(fullTotalAmountFinanced.toFixed(2));

            var inputs = $(".amountPayout"); // full total of financed
            var fullTotalAmountPayOut = 0;
            for (var i = 0; i < inputs.length; i++) {
                fullTotalAmountPayOut = parseFloat(fullTotalAmountPayOut) + parseFloat($(inputs[i]).val());
            }
            $('.totalAmountPayOut').html(fullTotalAmountPayOut.toFixed(2));
            $('.totalAmountPayOut-input').val(fullTotalAmountPayOut.toFixed(2));

            amountPayoutInWords(fullTotalAmountPayOut);

            var inputs = $(".financeCharge"); // full total of financed
            var fullFinanceCharge = 0;
            for (var i = 0; i < inputs.length; i++) {
                fullFinanceCharge = parseFloat(fullFinanceCharge) + parseFloat($(inputs[i]).html());
            }
            $('.totalFinanceCharge').html(fullFinanceCharge.toFixed(2));
            $('.totalFinanceCharge-input').val(fullFinanceCharge.toFixed(2));

            var inputs = $(".totalOfPayment"); // full total of financed
            var fullTotalOfPayments = 0;
            for (var i = 0; i < inputs.length; i++) {
                fullTotalOfPayments = parseFloat(fullTotalOfPayments) + parseFloat($(inputs[i]).html());
            }
            $('.fullTotalOfPayments').html(fullTotalOfPayments.toFixed(2));
            $('.fullTotalOfPayments-input').val(fullTotalOfPayments.toFixed(2));

            var inputs = $(".prepayment"); // full total of prepayment
            var fullprepayments = 0;
            for (var i = 0; i < inputs.length; i++) {
                fullprepayments = parseFloat(fullprepayments) + parseFloat($(inputs[i]).html());
            }
            $('.fullPrepayment').html(fullprepayments.toFixed(2));
            $('.fullPrepayment-input').val(fullprepayments.toFixed(2));
        }

    function amountPayoutInWords(amount){
        $("#amount_payout_in_words").val('');
        var words = '';
        words = toWords(amount);
        $("#amount_payout_in_words").val(words + "Only");
    }
    </script>

{{-- @endpush --}}
<div class="se-pre-con"></div>

{{Form::open(array('url'=>'pawning/update', 'id'=>'form', 'method'=>'post','class'=>'repeater'))}}


    <div class="row">
        <input type="hidden" class="pawning_id" name="pawning_id" id="pawning_id" value="{{ $pawning->id }}">

        <div class="form-group col-md-3">
            {{ Form::label('issue_date', __('Date of Pawning')) }}
            <div class="input-group">
                {{ Form::text('issue_date', $pawning->issue_date, array('class' => 'form-control datepicker','required'=>'required', 'data-empty-message'=>"Please Select Date of Pawning", 'data-focus'=>"issue_date")) }}
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('customer', __('Customer')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" data-live-search="true" name="customer_id" id="customer" required
                data-empty-message="Please Select Customer" data-focus="customer">
                    <option value="">Select Customer</option>
                    @foreach ($customers as $customer)
                        <option @if($pawning->customer_id == $customer->id) selected @endif value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('officer_id', __('Officer')) }}
            {{ Form::select('officer_id', $users, $pawning->officer_id, array('class' => 'form-control', 'placeholder'=>'-- Officer --', 'required'=>'required')) }}
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('introducer_id', __('Salesman')) }}
            {{ Form::select('introducer_id', $users, $pawning->introducer_id, array('class' => 'form-control', 'placeholder'=>'-- Salesman --')) }}
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('time_range', __('Installment Collection Time Range')) }}
            <div class="input-group colorpickerinput">
                <select name="time_range" id="time_range" class="form-control" required>
                    <option value="">Select Time Range</option>
                    <option @if($pawning->time_range == 3) selected @endif value="3">Monthly</option>
                    <option @if($pawning->time_range == 2) selected @endif value="2">Weekly</option>
                    <option @if($pawning->time_range == 1) selected @endif value="1">Daily</option>
                </select>
            </div>
        </div>
        <div class="form-group col-md-3" id="installment_date_div">
            {{ Form::label('installment_date', __('First'), array('id'=>'first')) }} {{ Form::label('installment_date', __('Installment Date')) }}
            <div class="input-group colorpickerinput">
                <select class="form-control" name="installment_date" id="installment_date">
                    <option value="">Select Installment Date</option>
                    @for ($i = 1; $i < 31; $i++)
                        <option @if($pawning->installment_date == $i) selected @endif value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('time_period_id', __('Time Period')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style timePeriod" name="time_period_id" id="time_period" required
                data-empty-message="Please Select Time Period" data-focus="customer">
                    <option value="">Select Time Period</option>
                    @foreach ($timePeriods as $timeperiod)
                        <option @if($pawning->time_period_id == $timeperiod->time_period) selected @endif value="{{ $timeperiod->time_period }}">{{ $timeperiod->time_period . ' Months' }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('due_date', __('Due Date')) }}
            <div class="input-group">
                {{ Form::text('due_date', $pawning->due_date, array('class' => 'form-control datepicker','required'=>'required')) }}
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('gold_interest', __('Gold Interest')) }}
            <input type="number" class="form-control gold_interest" step="0.01" id="gold_interest" name="gold_interest" value="{{ $pawning->interest_rate }}" required>
        </div>
    </div>

    <div class="card repeater" data-value='{!! json_encode($pawning->pawnedItem) !!}'>
        <div class="card-header">
            <div class="card-header-action">
                <a href="#" data-repeater-create="" id="add_row" class="btn btn-icon icon-left btn-primary" data-toggle="modal" data-target="#add-bank">
                    <i class="fas fa-plus"></i>{{__('Add Item')}}
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
                    <th>{{__('Jewellery')}}</th>
                    <th>{{__('Gold Type')}}</th>
                    <th>{{__('Weight')}} </th>
                    <th>{{__('Amount Financed')}} </th>
                    <th>{{__('Amount of Payment')}} </th>
                    <th>{{__('Interest Amount')}}</th>
                    <th>{{__('Total of Payment')}}</th>
                    <th>{{__('Prepayment')}}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody class="ui-sortable"  data-repeater-item>
                <tr>
                    {{ Form::hidden('id',null, array('class' => 'form-control id')) }}
                    <input type="hidden" class="timeperiod">
                    <td>
                        <select class="form-control customer-sel font-style jewellery_id" name="jewellery_id" id="jewellery_id" required
                        data-empty-message="Please Select Jewellery" data-focus="customer" style="min-width: 8rem;">
                            <option value="">Select Jewellery</option>
                            @foreach ($pawningJewelleries as $pawnignJewellery)
                                <option value="{{ $pawnignJewellery->id }}">{{ $pawnignJewellery->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        {{ Form::select('gold_rate_type',$goldRates ,'', array('class' => 'form-control font-style gold_rate_type','data-url'=>route('pawning.product'),'required'=>'required', 'style'=>'min-width: 8rem;')) }}
                        {{ Form::hidden('gold_rate_value','', array('class' => 'form-control gold_rate_value','required'=>'required')) }}
                        {{ Form::hidden('payment_rate_value','', array('class' => 'form-control payment_rate_value','required'=>'required')) }}
                    </td>
                    <td>
                        {{ Form::text('weight','', array('class' => 'form-control weight','required'=>'required', 'style'=>'min-width: 5rem;')) }}
                    </td>
                    <td>
                        <input type="hidden" class="amountFinanced-input" name="amountFinanced">
                        <span  class="amountFinanced"> 0.00</span>
                    </td>
                    <td>
                        {{ Form::text('amountPayout','', array('class' => 'form-control amountPayout','required'=>'required', 'style'=>'min-width: 5rem;')) }}
                    </td>
                    <td >
                        <input type="hidden" class="financeCharge-input" name="financeCharge">
                       <span class="financeCharge">0.00</span>
                    </td>
                    <td>
                        <input type="hidden" class="totalOfPayment-input" name="totalOfPayment">
                        <span class="totalOfPayment">0.00</span>
                    </td>
                    <td>
                        <input type="hidden" name="prepayment" class="prepayment-input">
                       <span class="prepayment">0.00</span>
                    </td>
                    <td>
                        <a href="#" class="fas fa-trash" data-repeater-delete></a>
                    </td>
                </tr>
                <tr>
                    <td colspan="8">
                        {{-- {{ Form::textarea('description', null, ['class'=>'form-control','rows'=>'2','placeholder'=>__('Image')]) }} --}}
                        {{-- <input type="file" class="form-control form-input" id="item_image" name="item_image" onchange="uploadImg()" required style="overflow: hidden;" />

                        <div class="image_container">
                            <img src="{{ asset('assets') }}/img/pawn_items/default.png" id="image_preview_1" />
                        </div> --}}
                    </td>
                    {{-- <td colspan="6"></td> --}}
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2"><strong>{{__('Total Amount Financed')}} </strong></td>
                    <td class="text-right totalAmountFinanced">0.00</td>
                    <input type="hidden" class="totalAmountFinanced-input" name="totalAmountFinanced">
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2"><strong>{{__('Total Amount of Payment')}} </strong></td>
                    <td class="text-right totalAmountPayOut">0.00</td>
                    <input type="hidden" class="totalAmountPayOut-input" name="totalAmountPayOut">
                    <input type="hidden" id="amount_payout_in_words" name="amount_payout_in_words">
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2"><strong>{{__('Total Finance Charges')}} </strong></td>
                    <td class="text-right totalFinanceCharge"> 0.00</td>
                    <input type="hidden" class="totalFinanceCharge-input" name="totalFinanceCharge">
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2"><strong>{{__('Total of Payments')}} </strong></td>
                    <td class="text-right fullTotalOfPayments">0.00</td>
                    <input type="hidden" class="fullTotalOfPayments-input" name="fullTotalOfPayments">
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2"><strong>{{__('Total Prepayment')}} </strong></td>
                    <td class="text-right fullPrepayment">0.00</td>
                    <input type="hidden" class="fullPrepayment-input" name="fullPrepayment">
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
        </div>
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
    </div>
{{-- </form> --}}
{{ Form::close() }}

<script>
    $(document).ready(function(){
        $('#due_date').prop('disabled', true);
        $('#first').hide();

        // $('#customer').select2({
        //     dropdownParent: $("#commonModal")
        // });

        $('.selectpicker').selectpicker();
    });

    // $('body').on('shown.bs.modal', '.modal', function() {
    //     $(this).find('select').each(function() {
    //         var dropdownParent = $(document.body);
    //         if ($(this).parents('.modal.in:first').length !== 0)
    //         dropdownParent = $(this).parents('.modal.in:first');
    //         $(this).select2({
    //         dropdownParent: dropdownParent
    //         // ...
    //         });
    //     });
    // });

    $('form').submit(function(e) {
        $(':disabled').each(function(e) {
            $(this).removeAttr('disabled');
        })
    });

    $('body').on('change', '#time_range', function(){
        if($(this).val() == 1){
            // $('#monthly_installment_div').hide(200);
            // $('#weekly_installment_div').hide(200);
            $('#first').show(200);
            // $('#installment_date_div').show(200);
            // $('#daily_installment_div').show(200);
        } else if($(this).val() == 2){
            // $('#daily_installment_div').hide(200);
            // $('#monthly_installment_div').hide(200);
            // $('#weekly_installment_div').show(200);
            $('#first').show(200);
            // $('#installment_date_div').show(200);
        } else if($(this).val() == 3){
            // $('#daily_installment_div').hide(200);
            // $('#weekly_installment_div').hide(200);
            $('#first').hide(200);
            // $('#monthly_installment_div').show(200);
            // $('#installment_date_div').show(200);
        } else {
            // $('#daily_installment_div').hide(200);
            // $('#monthly_installment_div').hide(200);
            // $('#weekly_installment_div').hide(200);
            // $('#installment_date_div').hide(200);
            $('#first').hide(200);
        }

        if($('#loan_amount').val() != '' && $('#interest_rate').val() != '' && $('#time_range').val() != '' && $('#installment_date').val() != ''){
            // finalCalculation();
        }
    });

    $('body').on('change', '#time_period', function(){

        if($('#time_range').val() == ''){
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Select Time Range!'
                });
                $(this).val('');
            return this;
        }
        if($('#installment_date').val() == ''){
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Select Installment Date!'
                });
                $(this).val('');
            return this;
        }

        dueDateCal();
        // var timePeriod = $('#time_period').val();
        // var currentDate = $('#issue_date').val();
        // var installmentDate = $('#installment_date').val();
        // if(installmentDate != ''){
        //     var seperatedDate = currentDate.split('-');
        //     var newDate = ''+seperatedDate[0]+'-'+seperatedDate[1]+'-'+installmentDate+'';
        // } else {
        //     var newDate = currentDate;
        // }

        // var dateforFunction = new Date(newDate);

        // Date.prototype.addMonths = function (months) {
        //     var dt = this.getDate();

        //     this.setMonth(this.getMonth() + months);
        //     var currDt = this.getDate();

        //     if (dt !== currDt) {
        //         this.addDays(-currDt);
        //     }

        //     return this;
        // };

        // var duedate = dateforFunction.addMonths(parseFloat(timePeriod));

        // function convert(str) {
        //     var date = new Date(str),
        //         mnth = ("0" + (date.getMonth() + 1)).slice(-2),
        //         day = ("0" + date.getDate()).slice(-2);
        //     return [date.getFullYear(), mnth, day].join("-");
        // }

        // var convertedDueDate = convert(duedate);

        // $('#due_date').val(convertedDueDate);

        var allElement = $('body').find('.totalOfPayment-input');

        var totalPrerpaiment = 0;
        for (var i = 0; i < allElement.length; i++) {
            // console.log(allElement[i].value);
            if(allElement[i].value != ''){
                var element = allElement[i].closest('tr');
                var amoun = parseFloat(allElement[i].value) / parseFloat(timePeriod);
                totalPrerpaiment = totalPrerpaiment + amoun;
                $(element).find('.prepayment').html(amoun.toFixed(2));
                $(element).find('.prepayment-input').val(amoun.toFixed(2));

                $('.fullPrepayment').html(totalPrerpaiment.toFixed(2));
                $('.fullPrepayment-input').val(totalPrerpaiment.toFixed(2));
            }
        }
    });

    $('body').on('change', '#time_range', function(){
        if( $('#time_period').val() != '' && $('#installment_date').val() != ''){
            dueDateCal();
        }
    });

    $('body').on('change', '#installment_date', function(){
        if( $('#time_period').val() != '' && $('#time_range').val() != ''){
            dueDateCal();
        }
    });

function dueDateCal(){
    var timePeriod = $('#time_period').val();
    var timeRange = $('#time_range').val();
    var currentDate = $('#issue_date').val();
    if(timeRange == 3){
        var installmentDate = $('#installment_date').val();
        if(installmentDate != ''){
            var seperatedDate = currentDate.split('-');
            var newDate = ''+seperatedDate[0]+'-'+seperatedDate[1]+'-'+installmentDate+'';
        } else {
            var newDate = currentDate;
        }

        var dateforFunction = new Date(newDate);

        Date.prototype.addMonths = function (months) {
            var dt = this.getDate();

            this.setMonth(this.getMonth() + months);
            var currDt = this.getDate();

            if (dt !== currDt) {
                this.addDays(-currDt);
            }

            return this;
        };
        Date.prototype.addDays = function(days) {
            this.setDate( this.getDate()  + days);
            return this;
        };

        var duedate = dateforFunction.addMonths(parseFloat(timePeriod));

        function convert(str) {
            var date = new Date(str),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);
            return [date.getFullYear(), mnth, day].join("-");
        }

        var convertedDueDate = convert(duedate);

        $('#due_date').val(convertedDueDate);
    } else if(timeRange == 2){
        var installmentDate = $('#installment_date').val();
        if(installmentDate != ''){
            var seperatedDate = currentDate.split('-');
            if( parseFloat(installmentDate) < parseFloat(seperatedDate[2]) ){
                var nxtMonth = parseFloat(seperatedDate[1]) + 1;
                var newDate = ''+seperatedDate[0]+'-'+ nxtMonth +'-'+installmentDate+'';
            } else {
                var newDate = ''+seperatedDate[0]+'-'+seperatedDate[1]+'-'+installmentDate+'';
            }
        } else {
            var newDate = currentDate;
        }

        var dateforFunction = new Date(newDate);

        Date.prototype.addMonths = function (months) {
            var dt = this.getDate();

            this.setMonth(this.getMonth() + months);
            var currDt = this.getDate();

            if (dt !== currDt) {
                this.addDays(-currDt);
            }

            return this;
        };
        Date.prototype.addDays = function(days) {
            this.setDate( this.getDate()  + days);
            return this;
        };

        var totDays = timePeriod*4*7 - 7;

        var duedate = dateforFunction.addDays(parseFloat(totDays));

        function convert(str) {
            var date = new Date(str),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);
            return [date.getFullYear(), mnth, day].join("-");
        }

        var convertedDueDate = convert(duedate);

        $('#due_date').val(convertedDueDate);
    } else if(timeRange == 1){
        var installmentDate = $('#installment_date').val();
        if(installmentDate != ''){
            var seperatedDate = currentDate.split('-');
            if( parseFloat(installmentDate) < parseFloat(seperatedDate[2]) ){
                var nxtMonth = parseFloat(seperatedDate[1]) + 1;
                var newDate = ''+seperatedDate[0]+'-'+ nxtMonth +'-'+installmentDate+'';
            } else {
                var newDate = ''+seperatedDate[0]+'-'+seperatedDate[1]+'-'+installmentDate+'';
            }
        } else {
            var newDate = currentDate;
        }

        var dateforFunction = new Date(newDate);

        Date.prototype.addMonths = function (months) {
            var dt = this.getDate();

            this.setMonth(this.getMonth() + months);
            var currDt = this.getDate();

            if (dt !== currDt) {
                this.addDays(-currDt);
            }

            return this;
        };
        Date.prototype.addDays = function(days) {
            this.setDate( this.getDate()  + days);
            return this;
        };

        var totDays = timePeriod*30 - 1;

        var duedate = dateforFunction.addDays(parseFloat(totDays));

        function convert(str) {
            var date = new Date(str),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);
            return [date.getFullYear(), mnth, day].join("-");
        }

        var convertedDueDate = convert(duedate);

        $('#due_date').val(convertedDueDate);
    }
}

    $('body').submit(function() {
        $(".se-pre-con").css({"display": "block"});
    });
    // $('body').on('change', '#installment_date', function(){
    //     var day = $(this).val();
    //     var date = $('#due_date').val();
    //     var seperateddate = date.split('-');
    //     var newDate = ''+seperateddate[0]+'-'+seperateddate[1]+'-'+day+'';

    //     function convert(str) {
    //         var date = new Date(str),
    //             mnth = ("0" + (date.getMonth() + 1)).slice(-2),
    //             day = ("0" + date.getDate()).slice(-2);
    //         return [date.getFullYear(), mnth, day].join("-");
    //     }

    //     var convertedNewDate = convert(newDate);

    //     $('#due_date').val(convertedNewDate);
    // });

    // function uploadImg() {
    //     var image_preview = document.getElementById("image_preview_1");
    //     var file = document.getElementById("item_image").files[0];

    //     var reader = new FileReader();
    //     reader.onloadend = function() {
    //         image_preview.src = reader.result;
    //         document.getElementById("base64").value = reader.result;
    //     };

    //     if (file) {
    //         reader.readAsDataURL(file);

    //     } else {
    //         image_preview.src = "";

    //     }
    // }

</script>
