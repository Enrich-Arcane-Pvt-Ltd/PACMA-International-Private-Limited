{{ Form::model($bankAccount, array('route' => array('bank-account.update', $bankAccount->id), 'method' => 'PUT')) }}
<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('bank_holder', __('Bank Holder')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
            {{ Form::text('bank_holder', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('bank', __('Bank')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-landmark"></i>
                </div>
            </div>
            {{ Form::text('bank', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('account_no', __('Account No')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-sort-numeric-down"></i>
                </div>
            </div>
            {{ Form::text('account_no', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('opening_balance', __('Opening Balance')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::number('opening_balance', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('address', __('Address')) }}
        {{ Form::textarea('address', null, array('class' => 'form-control','required'=>'required')) }}
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('phone', __('Phone')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-mobile-alt"></i>
                </div>
            </div>
            {{ Form::text('phone', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
    </div>
</div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
    </div>

{{ Form::close() }}



