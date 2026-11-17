{{ Form::open(array('url' => 'fine_control/create_center', 'method'=>'POST')) }}
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('center',__('Center')) }}
            {!! Form::select('center', $centers, null, ['class' => 'form-control customer-sel font-style', 'required'=>'required', 'placeholder' => '- Select Center-'])!!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('start_date_center',__('Start Date')) }}
            {!! Form::text('start_date_center', null, ['class' => 'form-control datepicker', 'required'=>'required'])!!}
        </div>
    </div>
    <div class="form-group col-md-6">
        <div class="input-group colorpickerinput" style="padding-top: 1.5rem; padding-left: 2rem;">
            <label class="switch">
                <input type="checkbox" id="fine_status_center" name="fine_status_center" checked>
                <span class="slider round"></span>
            </label>
            <label style="padding-left: 1.5rem; padding-top: 3px; font-weight: bold;" for="fine_status_center">{{__('Applying Penalty')}}</label>
        </div>
    </div>
</div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Add'),array('class'=>'btn btn-primary'))}}
    </div>

{{ Form::close() }}
