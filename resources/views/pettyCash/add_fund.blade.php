{{ Form::open(array('url' => 'petty_cash_fund/update', 'method'=>'POST')) }}
<div class="row">

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

    <div class="form-group  col-md-6">
        {{ Form::label('full_amount', __('Full Amount')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::number('full_amount', $pettyCashFund->fund_amount, array('class' => 'form-control','required'=>'required','step'=>'0.01', 'readonly'=>'readonly')) }}
        </div>
    </div>

    <div class="form-group  col-md-6">
        {{ Form::label('balance_amount', __('Balance Amount')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::number('balance_amount', $pettyCashFund->balance_amount, array('class' => 'form-control','required'=>'required','step'=>'0.01', 'readonly'=>'readonly')) }}
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
            {{ Form::number('amount', $pettyCashFund->fund_amount - $pettyCashFund->balance_amount, array('class' => 'form-control','required'=>'required','step'=>'0.01', 'max'=>$pettyCashFund->fund_amount - $pettyCashFund->balance_amount)) }}
        </div>
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}
