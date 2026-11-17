{{Form::open(array('url'=>'business_category','method'=>'post'))}}

<div class="row">

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('category_name', __('Category Name')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-store-alt"></i>
                    </div>
                </div>
                {{Form::text('category_name',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>


</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
</div>

</form>
