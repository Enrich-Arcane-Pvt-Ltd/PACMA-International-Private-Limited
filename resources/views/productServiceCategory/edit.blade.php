{{ Form::model($category, array('route' => array('product-category.update', $category->id), 'method' => 'PUT')) }}
<div class="row">
    <div class="form-group col-md-12">
        {{ Form::label('name', __('Category Name')) }}<span class="text-danger">*</span>
        {{ Form::text('name', null, array('class' => 'form-control font-style','required'=>'required')) }}
    </div>
    <div class="form-group  col-md-12">
        <div class="input-group">
            {{ Form::label('type', __('Down Payment')) }}<span class="text-danger">*</span>
            {{ Form::select('type',$types,null, array('class' => 'form-control customer-sel font-style selectric ','required'=>'required')) }}
        </div>
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('color', __('Category Color')) }}<span class="text-danger">*</span>
        {{ Form::text('color', null, array('class' => 'form-control jscolor','required'=>'required')) }}
        <p class="small">{{__('For chart representation')}}</p>
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}
