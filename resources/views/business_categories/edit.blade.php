{{-- {{Form::model($categories,array('route' => array('business_category.update', $categories->id), 'method' => 'PUT')) }} --}}
{{Form::open(array('url'=>'business_category/update','method'=>'post'))}}

<div class="row">
    <input type="hidden" name="id" value="{{ $categories->id }}">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('category_name',__('Category Name'),array('class'=>'')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-store-alt"></i>
                    </div>
                </div>
                {{Form::text('category_name',$categories->category_name,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>



</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
</div>

{{Form::close()}}


