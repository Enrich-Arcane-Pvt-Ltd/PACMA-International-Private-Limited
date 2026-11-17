{{Form::open(array('url'=>'customer','method'=>'post'))}}
<h4 class="sub-title">{{__('Basic Info')}}</h4>
<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('name',__('First Name'),array('class'=>'')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-address-card"></i>
                    </div>
                </div>
                {{Form::text('name',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('last_name',__('Last Name'),array('class'=>'')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-address-card"></i>
                    </div>
                </div>
                {{Form::text('last_name',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('email',__('Email'))}}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-envelope"></i>
                    </div>
                </div>
                {{Form::text('email',null,array('class'=>'form-control'))}}
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('contact',__('Contact'))}}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                </div>
                {{Form::text('contact',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('nic',__('NIC'),array('class'=>'')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-id-card"></i>
                    </div>
                </div>
                {{Form::text('nic',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('gender',__('Gender'),array('class'=>'')) }}
            
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                       <i class="fas fa-venus-mars"></i>
                    </div>
                </div>
                <select class="input-group-select">
                    <option selected>Choose...</option>
                    <option value="1">Male</option>
                    <option value="2">Female</option>
                </select>
            </div>
        </div>
    </div>


    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('acc_number',__('Account Number'),array('class'=>'')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                    <i class="fas fa-money-check"></i>
                    </div>
                </div>
                {{Form::text('acc_number',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('occupation',__('Occupation'),array('class'=>'')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
                {{Form::text('occupation',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('marital_status',__('Marital Status'),array('class'=>'')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-ring"></i>
                    </div>
                </div>
                <select class="input-group-select">
                    <option selected>Choose...</option>
                    <option value="1">Married</option>
                    <option value="2">Unmarried</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-12">
            <div class="form-group">
                {{Form::label('billing_address',__('Address'),array('class'=>'')) }}
                <label class="form-control-label" for="example2cols1Input"></label>
                <div class="input-group">
                    {{Form::textarea('billing_address',null,array('class'=>'form-control','required'=>'required'))}}
                </div>
            </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="d-block">{{__('Type')}}</label>
            <div class="row">
                <div class="col-md-6">
                    <div class="custom-control custom-checkbox mt-4">
                        <input class="custom-control-input" type="checkbox" name="customer_status" id="customer_status">
                        <label class="custom-control-label" for="customer_status">{{__('Customer')}}</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="custom-control custom-checkbox mt-4">
                        <input class="custom-control-input" type="checkbox" name="guarantor_status" id="guarantor_status">
                        <label class="custom-control-label" for="guarantor_status">{{__('Guarantor')}}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(!$customFields->isEmpty())
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                @include('customFields.formBuilder')
            </div>
        </div>
    @endif
</div>

<!-- <h4 class="sub-title">{{__('BIlling Address')}}</h4>
<div class="row">
</div> -->

<!-- @if(Utility::getValByName('shipping_display')=='on') -->
    

    
<!-- @endif -->
<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
</div>

{{Form::close()}}


