{{Form::model($branch,array('route' => array('branch.update', $branch->id), 'method' => 'PUT')) }}

<div class="row">
    
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-warehouse"></i>
                    </div>
                </div>
                {{Form::text('name',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>


    <div class="col-md-6">
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
        {{ Form::label('branch_manager', __('Branch Manager')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
            {{Form::text('branch_manager',null,array('class'=>'form-control','required'=>'required'))}}
        </div>
    </div>
</div>


<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
</div>

{{Form::close()}}


