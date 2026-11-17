{{ Form::open(array('url' => 'bank-rec/entries_search', 'method'=>'post')) }}
<div class="row">
    <div class="form-group col-md-12">
        {{ Form::label('account_id', __('Bank Account')) }}
        {{Form::select('account_id',$accounts, null,array('class'=>'form-control customer-sel font-style','required'=>'required'))}}
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('start_date', __('Start Date')) }}
        {{ Form::date('start_date', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('end_date', __('End Date')) }}
        {{ Form::date('end_date', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('bank_statement_balance', __('Bank Statement Balance')) }}
        {{ Form::number('bank_statement_balance', '', array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
    </div>
</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
</div>

{{ Form::close() }}
