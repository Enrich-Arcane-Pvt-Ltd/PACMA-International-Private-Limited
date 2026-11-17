{{Form::model($chartofaccount,array('route' => array('chart_of_account.update', $chartofaccount->id), 'method' => 'POST')) }}

{{-- <h4 class="sub-title">{{__('Edit Chart of Account')}}</h4> --}}
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('account_name',__('Account Name')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-file-signature"></i>
                    </div>
                </div>
                {{Form::text('account_name',$chartofaccount->name,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('account_type',__('Account Type')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-file-signature"></i>
                    </div>
                </div>
                {{Form::text('account_type',$chartofaccount->type,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>

    {{-- <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('group_type',__('Group Type')) }}
            {{Form::select('section',$grouptypes, $chartofaccount->section_id,array('class'=>'form-control customer-sel font-style','required'=>'required'))}}
        </div>
    </div> --}}
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('group',__('Group Name')) }}
            {{Form::select('group',$groups, $chartofaccount->group_id,array('class'=>'form-control customer-sel font-style','required'=>'required'))}}
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('section',__('Section Name')) }}
            {{Form::select('section',$sections, $chartofaccount->section_id,array('class'=>'form-control customer-sel font-style','required'=>'required'))}}
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
                        <input type="checkbox" class="custom-control-input" id="0" name="type" value="0" {{ ($accountgroup->type=="0")? "checked" : "" }} onclick="hide_show(this)">
                        <label class="custom-control-label" for="0">{{__('Profit & Loss')}}</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="1" name="type" value="1" {{ ($accountgroup->type=="1")? "checked" : "" }} onclick="hide_show(this)">
                        <label class="custom-control-label" for="1">{{__('Balance Sheet')}}</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="2" name="type" value="2" {{ ($accountgroup->type=="2")? "checked" : "" }} onclick="hide_show(this)">
                        <label class="custom-control-label" for="2">{{__('Cash Flow')}}</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="2" name="type" value="2" {{ ($accountgroup->type=="3")? "checked" : "" }} onclick="hide_show(this)">
                        <label class="custom-control-label" for="2">{{__('Bank Rec')}}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
</div>

{{Form::close()}}


