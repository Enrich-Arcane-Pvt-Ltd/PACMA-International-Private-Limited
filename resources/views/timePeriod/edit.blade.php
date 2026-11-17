{{ Form::model($timePeriod, array('route' => array('time-period.update', $timePeriod->id), 'method' => 'PUT')) }}

<div class="row">

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('time_period', __('Time Period')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::number('time_period', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{ "Months" }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
    </div>

</div>

{{ Form::close() }}