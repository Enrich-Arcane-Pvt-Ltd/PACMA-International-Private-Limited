{{ Form::open(array('url' => 'serviceBill','method'=>'post')) }}
<div class="row">
    <div class="form-group  col-md-6">
        {{ Form::label('bill_date', __('Bill Date')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
            {{ Form::text('bill_date', '', array('class' => 'form-control datepicker','required'=>'required')) }}
        </div>
    </div>
    <div class="form-group  col-md-6">
        {{ Form::label('service_id', __('Service')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::select('service_id',$services,null, array('class' => 'form-control customer-sel font-style ','required'=>'required')) }}
        </div>
    </div>
    <div class="form-group  col-md-6">
        {{ Form::label('bill_amount', __('Bill Amount')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::number('bill_amount', '', array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
        </div>
    </div>
    <div class="form-group  col-md-6">
        {{ Form::label('paid_amount', __('Paid Amount')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::number('paid_amount', '', array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
        </div>
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}
