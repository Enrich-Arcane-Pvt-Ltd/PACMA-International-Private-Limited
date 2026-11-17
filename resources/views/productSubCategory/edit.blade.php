{{ Form::model($productSubCategory, array('route' => array('product-sub-category.update', $productSubCategory->id), 'method' => 'PUT')) }}

<div class="row">

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('product_category_id',__('Product Category'),array('class'=>'')) }}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-sitemap"></i>
                    </div>
                </div>
                {!! Form::select('product_category_id', $productCategory, null, ['class' => 'form-control customer-sel font-style', 'required'=>'required', 'placeholder' => '- Select -'])!!}
            </div>
        </div>
    </div>


    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'','required'=>'required')) }}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-sitemap"></i>
                    </div>
                </div>
                {{ Form::text('name',null, array('class' => 'form-control','required'=>'required')) }}
            </div>
        </div>
    </div>

</div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
    </div>

{{ Form::close() }}
