{{ Form::open(array('url' => 'petty-cash', 'id' => 'petty_cash_create_form')) }}
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('voucher_no',__('Voucher No'),array('class'=>'')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
                {{Form::text('voucher_no', $pettyCashId, array('class'=>'form-control','required'=>'required','readonly'=>'readonly'))}}
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

    <div class="form-group col-md-12 col-sm-12">
        {{ Form::label('chart_of_accounts', __('Chart of Account')) }}
        {{ Form::select('chart_of_accounts', $chartofAccounts, null, array('class' => 'form-control','required'=>'required')) }}
    </div>

    <div class="form-group  col-md-6">
        {{ Form::label('balance_amount', __('Balance_Amount')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::number('balance_amount', $pettyCashFund->balance_amount ?? 0, array('class' => 'form-control','required'=>'required','step'=>'0.01', 'readonly'=>'readonly')) }}
        </div>
    </div>

    <div class="form-group  col-md-6">
        {{ Form::label('amount', __('Amount')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::number('amount', null, ['class' => 'form-control', 'required' => 'required', 'step' => '0.01', 'id' => 'amount']) }}
        </div>
        <small id="petty_cash_amount_error" class="text-danger font-weight-bold" style="display: none;"></small>
    </div>

    <div class="form-group col-md-12 col-sm-12">
        {{ Form::label('description', __('Description')) }}
        {{ Form::textarea('description', null, array('class'=>'form-control','required'=>'required')) }}
    </div>

    <div class="form-group  col-md-6">
        {{ Form::label('approved_by',__('Approved By')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            {!! Form::select('approved_by', $user, null, ['class' => 'form-control customer-sel font-style', 'required'=>'required', 'placeholder' => '- Select -'])!!}
        </div>
    </div>

    <div class="form-group  col-md-6">
        {{ Form::label('received_by',__('Received By')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            {!! Form::select('received_by', $user, null, ['class' => 'form-control customer-sel font-style', 'required'=>'required', 'placeholder' => '- Select -'])!!}
        </div>
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}

<script>
    $(document).ready(function () {
        var floatAmount = {{ isset($pettyCashFund) ? (float)$pettyCashFund->fund_amount : 0 }};
        var balanceAmount = {{ isset($pettyCashFund) ? (float)$pettyCashFund->balance_amount : 0 }};

        function validateAmount() {
            var val = parseFloat($('#amount').val());
            if (isNaN(val) || val <= 0) {
                $('#petty_cash_amount_error').hide().text('');
                $('#amount').removeClass('is-invalid');
                return true;
            }
            if (floatAmount <= 0) {
                $('#petty_cash_amount_error').text('{{ __("Petty Cash Float is not configured. Please set the Petty Cash Float in Company Settings first.") }}').show();
                $('#amount').addClass('is-invalid');
                return false;
            }
            if (val > floatAmount) {
                $('#petty_cash_amount_error').text('{{ __("Amount cannot exceed Petty Cash Float") }} (' + floatAmount.toFixed(2) + ')').show();
                $('#amount').addClass('is-invalid');
                return false;
            }
            if (val > balanceAmount) {
                $('#petty_cash_amount_error').text('{{ __("Amount exceeds available Balance Amount") }} (' + balanceAmount.toFixed(2) + ')').show();
                $('#amount').addClass('is-invalid');
                return false;
            }
            $('#petty_cash_amount_error').hide().text('');
            $('#amount').removeClass('is-invalid');
            return true;
        }

        $('#amount').on('input change', validateAmount);

        $(document).off('submit', '#petty_cash_create_form').on('submit', '#petty_cash_create_form', function (e) {
            if (!validateAmount()) {
                e.preventDefault();
                var errMsg = $('#petty_cash_amount_error').text();
                if (typeof toastrs === 'function') {
                    toastrs('Error', errMsg, 'error');
                } else if (typeof toastr !== 'undefined' && typeof toastr.error === 'function') {
                    toastr.error(errMsg);
                } else {
                    AppMessages.error(errMsg);
                }
                return false;
            }
        });
    });
</script>
