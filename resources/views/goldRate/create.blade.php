{{Form::open(array('url'=>'goldRate','method'=>'post'))}}

<div class="row">

    <div class="form-group col-md-4">
        {{ Form::label('gold_rate_type', __('Gold Rate Type')) }}
        {{ Form::text('gold_rate_type', '', array('class' => 'form-control','required'=>'required')) }}
    </div>

    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{ Form::label('gold_rate', __('Gold Rate')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{ Form::number('gold_rate', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{ Form::label('payment_rate', __('Payment Rate')) }}
            <div class="input-group">
                {{ Form::number('payment_rate', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
                <div class="input-group-prepend">
                    <div class="input-group-text">%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>

</div>
{{ Form::close() }}
