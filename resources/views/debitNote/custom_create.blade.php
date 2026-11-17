{{ Form::open(array('route' => array('bill.custom.debit.note'),'mothod'=>'post')) }}
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{Form::label('bill',__('Bill')) }}
            {{-- {{Form::select('bill',$bills, null, array('class'=>'form-control','required'=>'required'))}} --}}
            <select class="form-control" name="bill" id="bill" required>
                <option value="">-- Select Bill --</option>
                @foreach ($bills as $bill)
                    <option value="{{ $bill->id }}">{{ Auth::user()->paymentVoucherNumberFormat($bill->id).' - '. $bill->description}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group  col-md-6">
        {{ Form::label('date', __('Date')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-calendar"></i>
                </div>
            </div>
            {{ Form::text('date','', array('class' => 'form-control datepicker')) }}
        </div>
    </div>
    <div class="form-group col-md-6"></div>
    <div class="form-group  col-md-6">
        {{ Form::label('amount', __('Amount')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::number('amount', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
        </div>
    </div>
    <div class="form-group  col-md-6">
        {{ Form::label('quantity', __('Quantity')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-dolly-flatbed"></i>
                </div>
            </div>
            {{ Form::number('quantity', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('description', __('Description')) }}
        {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2']) !!}
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}
