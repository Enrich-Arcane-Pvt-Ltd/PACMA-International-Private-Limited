{{Form::open(array('url'=>'loan_package','method'=>'post'))}}

<div class="row">

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('name', __('Package Name')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-store-alt"></i>
                    </div>
                </div>
                {{Form::text('name',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('loan_amount', __('Loan Amount')) }}
            <div class="input-group">
                {{Form::number('loan_amount',null,array('class'=>'form-control','required'=>'required', 'step'=>'0.00'))}}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{\Auth::user()->currencySymbol()}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('interest_rate', __('Interest Rate')) }}
            <div class="input-group">
                {{Form::number('interest_rate',null,array('class'=>'form-control','required'=>'required', 'step'=>'0.00'))}}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{"%"}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('time_period', __('Time Period')) }}
            <div class="input-group">
                {{Form::number('time_period',null,array('class'=>'form-control','required'=>'required'))}}
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{__('Days')}}
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
</div>

</form>
