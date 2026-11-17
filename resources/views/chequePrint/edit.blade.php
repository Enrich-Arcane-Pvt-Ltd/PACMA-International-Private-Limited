{{Form::model($center,array('route' => array('center.update', $center->id), 'method' => 'PUT')) }}

<div class="row">
    

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('branch_id',__('Branch')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
                {!! Form::select('branch_id', $branch, null, ['class' => 'form-control customer-sel font-style', 'required'=>'required', 'placeholder' => '- Select -'])!!}
            </div>
        </div>
    </div>

    
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'')) }}
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



</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
</div>

{{Form::close()}}


