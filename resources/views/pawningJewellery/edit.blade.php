{{ Form::model($pawningJewellery, array('route' => array('pawningJewellery.update', $pawningJewellery->id), 'method' => 'PUT')) }}

<div class="row">

    <div class="form-group col-md-6">
        {{ Form::label('name', __('Jewellery Name')) }}
        {{ Form::text('name', $pawningJewellery->name, array('class' => 'form-control','required'=>'required')) }}
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
    </div>

</div>

{{ Form::close() }}
