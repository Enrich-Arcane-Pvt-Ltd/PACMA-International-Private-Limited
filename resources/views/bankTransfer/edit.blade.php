{{ Form::model($bankTransfer, array('route' => array('bank-transfer.update', $bankTransfer->id), 'method' => 'PUT')) }}

<div class="row">

    <div class="form-group  col-md-6">
        {{ Form::label('date', __('Date')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
            {{ Form::text('date', null, array('class' => 'form-control datepicker','required'=>'required')) }}
        </div>
    </div>


    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('from_account',__('From Account')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-landmark"></i>
                    </div>
                </div>
                {!! Form::select('from_account', $bankAccount, null, ['class' => 'form-control customer-sel font-style', 'required'=>'required', 'placeholder' => '- Select -'])!!}
            </div>
        </div>
    </div>


    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('to_account',__('To Account')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-landmark"></i>
                    </div>
                </div>
                {!! Form::select('to_account', $bankAccount, null, ['class' => 'form-control customer-sel font-style', 'required'=>'required', 'placeholder' => '- Select -'])!!}
            </div>
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
            {{ Form::number('amount', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
        </div>
    </div>


    <div class="form-group  col-md-6">
        {{ Form::label('reference', __('Reference')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            {{ Form::text('reference', null, array('class' => 'form-control')) }}
        </div>
    </div>


    <div class="form-group  col-md-6">
        {{ Form::label('description', __('Description')) }}
        {{ Form::textarea('description', null, array('class' => 'form-control')) }}
    </div>
   
</div>


    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
    </div>

{{ Form::close() }}




