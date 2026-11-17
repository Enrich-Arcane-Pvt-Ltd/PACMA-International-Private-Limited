{{ Form::open(array('url' => 'interest-rate')) }}
<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('service_type', __('Service Type')) }}
        {{ Form::text('service_type', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('interest_rate', __('Rate %')) }}
        {{ Form::number('interest_rate', '', array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}
