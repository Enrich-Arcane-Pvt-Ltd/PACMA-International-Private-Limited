{{ Form::model($productCategory, array('route' => array('product-category.update', $productCategory->id), 'method' => 'PUT')) }}

<div class="row">

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'','required'=>'required')) }}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-sitemap"></i>
                    </div>
                </div>
                {{Form::text('name',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>


    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{ Form::label('down_payment', __('Down Payment')) }}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-money-bill-alt"></i>
                    </div>
                </div>
                {{ Form::number('down_payment',null, array('class' => 'form-control discount','required'=>'required','step'=>'0.01')) }}
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{"%"}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
</div>

{{ Form::close() }}
