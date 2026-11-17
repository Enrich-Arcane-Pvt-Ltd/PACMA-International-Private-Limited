{{ Form::model($productWarranty, array('route' => array('product-warranty.update', $productWarranty->id), 'method' => 'PUT')) }}

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="form-group">
            {{Form::label('warranty_card_no',__('Warranty Card No'),array('class'=>'','required'=>'required')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-tag"></i>
                    </div>
                </div>
                {{ Form::text('warranty_card_no',null, array('class' => 'form-control','required'=>'required')) }}
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
</div>

{{ Form::close() }}
