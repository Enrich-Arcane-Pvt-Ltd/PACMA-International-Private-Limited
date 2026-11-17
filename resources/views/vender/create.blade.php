{{Form::open(array('url'=>'vender','method'=>'post'))}}
<h4 class="sub-title">{{__('Basic Info')}}</h4>
<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('name',__('Company Name'),array('class'=>'')) }}<span class="text-danger">*</span>
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
            {{Form::label('contact',__('Contact'))}}<span class="text-danger">*</span>
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
            {{Form::label('email',__('Email'))}}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-envelope"></i>
                    </div>
                </div>
                {{Form::text('email',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {{Form::label('address',__('Address'),array('class'=>'')) }}<span class="text-danger">*</span>
            <label class="form-control-label" for="example2cols1Input"></label>
            <div class="input-group">
                {{Form::textarea('address',null,array('class'=>'form-control','required'=>'required'))}}
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

<h4 class="sub-title">{{__('Contact Person Info')}}</h4>
<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('contact_title',__('Title'),array('class'=>'')) }}

            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-id-badge"></i>
                    </div>
                </div>

                <select name="contact_title" class="form-control customer-sel font-style" style="border-color: #DCDCDC;">
                    <option value="3">Mr.</option>
                    <option value="1">Ms.</option>
                    <option value="2">Mrs.</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('contact_name',__('Name'),array('class'=>'')) }}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-address-card"></i>
                    </div>
                </div>
                {{Form::text('contact_name',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('contact_email',__('Email'))}}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-envelope"></i>
                    </div>
                </div>
                {{Form::text('contact_email',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('contact_phone',__('Phone'),array('class'=>'')) }}<span class="text-danger">*</span>
            <label class="form-control-label" for="example2cols1Input"></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                </div>
                {{Form::text('contact_phone',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
</div>

<h4 class="sub-title">{{__('BIlling Info')}}</h4>
<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('billing_name',__('Name'),array('class'=>'')) }}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-address-card"></i>
                    </div>
                </div>
                {{Form::text('billing_name',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="form-group">
            {{Form::label('billing_phone',__('Phone'),array('class'=>'')) }}<span class="text-danger">*</span>
            <label class="form-control-label" for="example2cols1Input"></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                </div>
                {{Form::text('billing_phone',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {{Form::label('billing_address',__('Address'),array('class'=>'')) }}<span class="text-danger">*</span>
            <label class="form-control-label" for="example2cols1Input"></label>
            <div class="input-group">
                {{Form::textarea('billing_address',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>

</div>


<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
</div>

{{Form::close()}}

