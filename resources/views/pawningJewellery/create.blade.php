{{Form::open(array('url'=>'pawningJewellery','method'=>'post'))}}

<div class="row">

    <div class="form-group col-md-6">
        {{ Form::label('name', __('Pawning Jewellery Name')) }}
        {{ Form::text('name', '', array('class' => 'form-control','required'=>'required')) }}
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>

</div>
{{ Form::close() }}
