{{ Form::model($fineRate, array('route' => array('fine_rate.update', $fineRate->id), 'method' => 'PUT')) }}
<div class="row">
    <input type="hidden" id="fine_rate_id" name="fine_rate_id" value="{{ $fineRate->id }}">
    <div class="form-group col-md-6">
        {{ Form::label('service_type', __('Service Type')) }}
        {{ Form::text('service_type', $fineRate->service_type, array('class' => 'form-control','required'=>'required','readonly'=>'readonly')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('fine_rate', __('Rate %')) }}
        {{ Form::number('fine_rate', $fineRate->fine_rate, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}
