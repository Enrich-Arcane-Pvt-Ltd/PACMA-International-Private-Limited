{{ Form::open(array('url' => 'finerateView/store', 'method'=>'POST')) }}
<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('service_type', __('Servise Type')) }}
        {{ Form::text('service_type', '', array('class' => 'form-control','required'=>'required')) }}
        @error('service_type')
        <span class="invalid-name" role="alert">
        <strong class="text-danger">{{ $message }}</strong>
    </span>
        @enderror
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('fine_rate', __('Fine Rate %')) }}
        {{ Form::number('fine_rate', '', array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
        @error('fine_rate')
        <span class="invalid-rate" role="alert">
        <strong class="text-danger">{{ $message }}</strong>
    </span>
        @enderror
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}
