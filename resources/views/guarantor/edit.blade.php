{{Form::model($customer,array('route' => array('customer.update', $customer->id), 'method' => 'PUT')) }}
<h4 class="sub-title">{{__('Basic Info')}}</h4>
<div class="row">

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('title',__('Title'),array('class'=>'')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-address-card"></i>
                    </div>
                </div>
                {{-- {{Form::text('title',null,array('class'=>'form-control','required'=>'required'))}} --}}
                <select class="form-control customer-sel font-style" name="title">
                    <option selected>Choose...</option>
                    <option value="0" {{$customer->title == 0  ? 'selected' : ''}}>Ms.</option>
                    <option value="1" {{$customer->title == 1  ? 'selected' : ''}}>Mr.</option>
                    <option value="2" {{$customer->title == 2  ? 'selected' : ''}}>Mrs.</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'')) }}
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

    <div class="col-md-12">
        <div class="form-group">
            {{Form::label('address',__('Address'),array('class'=>'')) }}
            <label class="form-control-label" for="example2cols1Input"></label>
            <div class="input-group">
                {{Form::textarea('address',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('phone',__('Phone'))}}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                </div>
                {{Form::text('phone',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
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

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('nic_no',__('NIC No'))}}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-id-card"></i>
                    </div>
                </div>
                {{Form::text('nic_no',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>

</div>


<h4 class="sub-title">{{__('Billing Info')}}</h4>
<div class="row">

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('billing_title',__('Title'),array('class'=>'')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-address-card"></i>
                    </div>
                </div>
                {{-- {{ Form::select('billing_title',$titles,null, array('class' => 'form-control customer-sel font-style','required'=>'required')) }} --}}
                <select class="form-control customer-sel font-style" name="billing_title">
                    <option selected>Choose...</option>
                    <option value="0" {{$customer->billing_title == 0  ? 'selected' : ''}}>Ms.</option>
                    <option value="1" {{$customer->billing_title == 1  ? 'selected' : ''}}>Mr.</option>
                    <option value="2" {{$customer->billing_title == 2  ? 'selected' : ''}}>Mrs.</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('billing_name',__('Name'),array('class'=>'','required'=>'required')) }}
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

    <div class="col-md-12">
        <div class="form-group">
            {{Form::label('billing_address',__('Address'),array('class'=>'')) }}
            <label class="form-control-label" for="example2cols1Input"></label>
            <div class="input-group">
                {{Form::textarea('billing_address',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('billing_phone',__('Phone'))}}
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

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('billing_email',__('Email'))}}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-envelope"></i>
                    </div>
                </div>
                {{Form::text('billing_email',null,array('class'=>'form-control'))}}
            </div>
        </div>
    </div>

</div>


<div class="row">

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('type',__('Type'),array('class'=>'')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-address-card"></i>
                    </div>
                </div>
                {{-- {{ Form::select('type',$types,null, array('class' => 'form-control customer-sel font-style','required'=>'required')) }} --}}
                <select class="form-control customer-sel font-style" name="type">
                    <option selected>Choose...</option>
                    <option value="0" {{$customer->type == 0  ? 'selected' : ''}}>Customer</option>
                    <option value="1" {{$customer->type == 1  ? 'selected' : ''}}>Guarantor</option>
                    <option value="2" {{$customer->type == 2  ? 'selected' : ''}}>Both</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
</div>

{{Form::close()}}


