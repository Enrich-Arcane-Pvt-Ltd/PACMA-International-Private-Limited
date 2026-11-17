{{ Form::open(array('url' => 'advance_payement', 'id' => 'advance_payment_form')) }}
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

    {{-- <div class="form-group col-md-6">
        {{ Form::label('current_date', __('Sales Type')) }}
        <div class="input-group">
            <select  class="form-control customer-sel font-style selectpicker" data-empty-message="Please select Sales Type" required>
                <option value=" ">Select Sales Type</option>
                <option value="1">Cash Sales</option>
                <option value="2">Hire Sales</option>
            </select>
        </div>
    </div> --}}

        <div class="form-group col-md-6">
            {{ Form::label('customer', __('New Customer')) }}
            <div>
                <a href="#" href="#" data-size="xl" data-url="{{ route('customer.create') }}"
                    data-ajax-popup="true" data-title="{{ __('Create New Customer') }}"
                    class="btn btn-primary btn-action mt-1 float-right w-100" id="createCustomerBtn">
                    <span class="btn-inner--icon"><i class="fas fa-user-plus"></i></span>
                    <span class="btn-inner--text"> {{ __('Create New Customer') }}</span>
                </a>
            </div>
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('customer', __('Regular Customer')) }} <span class="text-danger">*</span>
            <div class="input-group">
                <select class="form-control customer-sel font-style selectpicker" data-live-search="true"
                        name="customer" id="customer" required data-empty-message="Please Select Customer"
                        data-focus="customer">
                    <option value="">Select Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}"
                            data-recent="{{ isset($recentCustomer) && $recentCustomer->id == $customer->id ? 'true' : 'false' }}">
                            {{ $customer->nic_no . ' - ' . $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>


    <div class="form-group col-md-6">
        {{ Form::label('advance_amount', __('Advance Amount')) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('advance_amount',null, array('class' => 'form-control price','required'=>'required','step'=>'0.01')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('advance_amount_in_words', __('Advance Amount In Words')) }}
        <div class="input-group colorpickerinput">
            <textarea class="form-control advance_amount_in_words" id="advance_amount_in_words" name="advance_amount_in_words" required></textarea>
        </div>
    </div>

    {{-- <div class="form-group col-md-6">
        {{ Form::label('product', __('Product Code')) }}<span class="text-danger">*</span>
        <div class="input-group">
            <select class="form-control customer-sel font-style selectpicker" name="product" id="product" required
                data-empty-message="Please Select Product" data-focus="product" data-live-search="true">
                <option value="">Select Product code</option>
                @if(!empty($products))
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->barcode }}</option>
                @endforeach
            @else
                <option value="">No products available</option>
            @endif
            </select>
        </div>
    </div> --}}


    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('Note', __('Note')) }}
            <label class="form-control-label" ></label>
            <div class="input-group mt-1">
                <textarea class="form-control note text-start" id="note" name="note" ></textarea>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('bank', __('Company Bank')) }}
        <div class="input-group mt-2">
            {{ Form::select('bank',$banks, null, array('class' => 'form-control customer-sel font-style','required'=>'required', 'data-empty-message'=>"Please Select a Company Bank", 'data-focus'=>"bank")) }}
        </div>
    </div>


    <div class="form-group col-md-6">
        {{ Form::label('payment_method', __('Payment Method')) }}
        <select class="form-control payment_method  mt-2 " name="payment_method" id="payment_method" required>
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


</div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Pay'),array('class'=>'btn btn-primary'))}}
    </div>
</div>

<script>

    $(document).ready(function() {
        document.getElementById('createCustomerBtn').addEventListener('click', function() {
            localStorage.setItem("newCustomerButtonClickedAdvance", "true");
        });

        if (localStorage.getItem('newCustomerButtonClickedAdvance')) {
            let recentCustomerId = $('#customer option[data-recent="true"]').val();
            if (recentCustomerId) {
                $('#customer').val(recentCustomerId);
                $('.selectpicker').selectpicker('refresh');
            }
            localStorage.removeItem('newCustomerButtonClickedAdvance');
        }

        // Clear localStorage entry on form submission
        $('#advance_payment_form').on('submit', function() {
            setTimeout(() => {
                localStorage.removeItem("newCustomerButtonClickedAdvance");
            }, 100);
        });
    });

    $(document).ready(function(){
        $('.selectpicker').selectpicker();

        $('.cheque_number_div').hide();
        $('#card_type_div').hide();
        $('#card_charge_amount_div').hide();
        $('#total_amount_div').hide();
        $('#pay_total_amount_div').hide();

        $('#advance_amount').on('input', function(){
            $("#advance_amount_in_words").html('');
            var amount = $(this).val();
            amount = amount.replace(/\.00$/, '');
            var words = toWords(amount);

            $("#advance_amount_in_words").html(words + "Only");
        });

        $('#advance_amount').on('focusout', function () {
            let value = parseFloat($(this).val()).toFixed(2);
            $(this).val(value);
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
            var advanceAmount = $('#advance_amount').val();
            $.ajax({
                url:"{{ url('card_payment_rate/get_detail') }}/"+cardType,
                method:'GET',
                dataType:'json',
                success: function (data, textStatus, jqXHR){
                    if(data != null){
                        var cardCharge = parseFloat(advanceAmount)*( parseFloat(data.rate)/100 );
                        var totalAmount = parseFloat(advanceAmount)+cardCharge;
                        $('#card_charge_amount').val(cardCharge);
                        $('#total_amount').val(totalAmount);
                    }
                }
            });
        });

        $('body').on('input', '#card_charge_amount', function(){
            var cardCharges = $(this).val();
            var advanceAmounts = $('#advance_amount').val();
            var totalAmounts = parseFloat(advanceAmounts)+parseFloat(cardCharges);
            $('#total_amount').val(totalAmounts);
        });
    });
</script>
