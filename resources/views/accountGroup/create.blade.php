{{Form::open(array('url'=>'account-group/store','method'=>'post'))}}

{{-- <h4 class="sub-title">{{__('Create Account Group')}}</h4> --}}
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('group_name',__('Group Name')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user-tag"></i>
                    </div>
                </div>
                {{Form::text('group_name',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('group_type',__('Type')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                {{Form::text('group_type',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('section',__('Section')) }}
            {{Form::select('section',$sections, null,array('class'=>'form-control','required'=>'required'))}}
        </div>
    </div>
</div>
{{-- <div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="d-block">{{__('Financial Statement')}}</label>
            <div class="row">
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="0" name="type" value="0" onclick="hide_show(this)">
                        <label class="custom-control-label" for="0">{{__('Profit & Loss')}}</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="1" name="type" value="1" onclick="hide_show(this)">
                        <label class="custom-control-label" for="1">{{__('Balance Sheet')}}</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="2" name="type" value="2" onclick="hide_show(this)">
                        <label class="custom-control-label" for="2">{{__('Cash Flow')}}</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="2" name="type" value="3" onclick="hide_show(this)">
                        <label class="custom-control-label" for="2">{{__('Bank Rec')}}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}


<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
</div>

{{Form::close()}}


