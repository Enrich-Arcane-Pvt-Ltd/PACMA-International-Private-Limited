{{ Form::open(array('url' => 'LoanView/create')) }}
<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('name', __('Customer')) }}
        {{-- {{ Form::text('name', '', array('class' => 'form-control','required'=>'required')) }} --}}
        {{ Form::select('customer', $customers, null, array('class' => 'form-control','required'=>'required')) }}
    </div>
    {{-- <div class="form-group col-md-6">
        {{ Form::label('amount', __('Center')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div> --}}

    <div class="form-group col-md-6">
        {{ Form::label('amount', __('Group')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('amount', __('Reason')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('guarantor_1', __('Guarantor 1')) }}
        {{ Form::select('guarantor_1', $guarantors, null, array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('guarantor_2', __('Guarantor 2')) }}
        {{ Form::select('guarantor_2', $guarantors, null, array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('guarantor_3', __('Guarantor 3')) }}
        {{ Form::select('guarantor_3', $guarantors, null, array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('guarantor_4', __('Guarantor 4')) }}
        {{ Form::select('guarantor_4', $guarantors, null, array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('amount', __('Loan amount')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('amount', __('Interest rate')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('time_period', __('Time Period')) }}
        <div class="input-group colorpickerinput">
            {{ Form::select('time_period', $timeperiods, null, array('class' => 'form-control','required'=>'required')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{__('Month')}}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('amount', __('Interest')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('amount', __('Document Charges')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('amount', __('Total amount')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('amount', __('Monthly Installment')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('amount', __('Upload application')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('amount', __('Upload Document')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}
