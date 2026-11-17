{{Form::model($role,array('route' => array('roles.update', $role->id), 'method' => 'PUT')) }}
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{Form::label('name',__('Name'))}}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Role Name')))}}
            @error('name')
            <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            @if(!empty($permissions))
                <h6>{{__('Assign Permission to Roles')}} </h6>
                <table class="table table-striped mb-0" id="dataTable-1">
                    <thead>
                    <tr>
                        <th>{{__('Module')}} </th>
                        <th>{{__('Permissions')}} </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $modules=['dashboard','user','role', 'authorization','proposal','sale','advances','item return','invoice','loan', 'loan package','group','pawning','penalty','stock','stock transfer','purchase order',
                                    'grn','bill','revenue','petty cash','petty cash fund','payment','payment voucher','cheque','cheque book','account group','chart of account','account transaction','cash book',
                                    'bank book','petty cash book','general journal','stock adjustment','ledger account','control account','bank rec','trial balance','balance sheet','loss & profit',
                                    'proposal product','invoice product','bill product',
                                    'goal','credit note','debit note','bank account','transfer','transaction','product','product & service','customer','vender','plan',
                                    'constant tax','constant category','constant unit','constant payment method','constant custom field','company settings',
                                    'branch','center','pawning jewellery','business category','service','rate','assets','report','time period','receipt'];
                        if(\Auth::user()->type == 'super admin'){
                            $modules[] = 'language';
                            $modules[] = 'permission';
                        }
                    @endphp
                    @foreach($modules as $module)
                        <tr>
                            <td>{{ ucfirst($module) }}</td>
                            <td>
                                <div class="row">
                                    @if(in_array('manage '.$module,(array) $permissions))
                                        @if($key = array_search('manage '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Manage',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('create '.$module,(array) $permissions))
                                        @if($key = array_search('create '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Create',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('edit '.$module,(array) $permissions))
                                        @if($key = array_search('edit '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Edit',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('delete '.$module,(array) $permissions))
                                        @if($key = array_search('delete '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Delete',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('show '.$module,(array) $permissions))
                                        @if($key = array_search('show '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Show',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif

                                    @if(in_array('buy '.$module,(array) $permissions))
                                        @if($key = array_search('buy '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Buy',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('send '.$module,(array) $permissions))
                                        @if($key = array_search('send '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Send',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif

                                    @if(in_array('create payment '.$module,(array) $permissions))
                                        @if($key = array_search('create payment '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Create Payment',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('delete payment '.$module,(array) $permissions))
                                        @if($key = array_search('delete payment '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Delete Payment',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('income '.$module,(array) $permissions))
                                        @if($key = array_search('income '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Income',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('expense '.$module,(array) $permissions))
                                        @if($key = array_search('expense '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Expense',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('income vs expense '.$module,(array) $permissions))
                                        @if($key = array_search('income vs expense '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Income VS Expense',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif

                                    @if(in_array('tax '.$module,(array) $permissions))
                                        @if($key = array_search('tax '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Tax',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('loss & profit '.$module,(array) $permissions))
                                        @if($key = array_search('loss & profit '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Loss & Profit',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('invoice '.$module,(array) $permissions))
                                        @if($key = array_search('invoice '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Invoice',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('bill '.$module,(array) $permissions))
                                        @if($key = array_search('bill '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Bill',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('duplicate '.$module,(array) $permissions))
                                        @if($key = array_search('duplicate '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Duplicate',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('print '.$module,(array) $permissions))
                                        @if($key = array_search('print '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Print',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('collect '.$module,(array) $permissions))
                                        @if($key = array_search('collect '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Print collector',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('pay '.$module,(array) $permissions))
                                        @if($key = array_search('pay '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Pay',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('view '.$module,(array) $permissions))
                                        @if($key = array_search('view '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'View',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('return '.$module,(array) $permissions))
                                        @if($key = array_search('return '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Return',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('check '.$module,(array) $permissions))
                                        @if($key = array_search('check '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Check',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('approve '.$module,(array) $permissions))
                                        @if($key = array_search('approve '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Approve',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('disburse '.$module,(array) $permissions))
                                        @if($key = array_search('disburse '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Disburse',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('reject '.$module,(array) $permissions))
                                        @if($key = array_search('reject '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Reject',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('block '.$module,(array) $permissions))
                                        @if($key = array_search('block '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Block',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('collect '.$module,(array) $permissions))
                                        @if($key = array_search('collect '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Print Collector',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('pending '.$module,(array) $permissions))
                                        @if($key = array_search('pending '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Pending',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('checked '.$module,(array) $permissions))
                                        @if($key = array_search('checked '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Checked',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('granted '.$module,(array) $permissions))
                                        @if($key = array_search('granted '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Granted',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('disbursed '.$module,(array) $permissions))
                                        @if($key = array_search('disbursed '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Disbursed',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('rejected '.$module,(array) $permissions))
                                        @if($key = array_search('rejected '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Rejected',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('closed '.$module,(array) $permissions))
                                        @if($key = array_search('closed '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Closed',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('cancel '.$module,(array) $permissions))
                                        @if($key = array_search('cancel '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Cancel',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('cancel installment '.$module,(array) $permissions))
                                        @if($key = array_search('cancel installment '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Cancel Installment',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
    </div>
</div>

{{Form::close()}}

