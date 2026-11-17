{{ Form::model($interestRates, array('route' => array('interest-rate.update', $interestRates->id), 'method' => 'PUT')) }}
<div class="row">
    <input type="hidden" id="interest_rate_id" name="interest_rate_id" value="{{ $interestRates->id }}">
    <div class="form-group col-md-6">
        {{ Form::label('service_type', __('Service Type')) }}
        {{ Form::text('service_type', $interestRates->service_type, array('class' => 'form-control','required'=>'required','readonly'=>'readonly')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('interest_rate', __('Rate %')) }}
        {{ Form::number('interest_rate', $interestRates->interest_rate, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}
