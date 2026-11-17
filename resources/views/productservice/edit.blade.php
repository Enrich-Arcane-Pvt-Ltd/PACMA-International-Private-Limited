
{{ Form::model($productService, array('route' => array('productservice.update', $productService->id), 'method' => 'PUT')) }}
<style>
    .image_container {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        width: 100%;
    }
    .image_container img {
        width: 100%;
    }
    @media (min-width: 576px){
        .modal-dialog {
            width: 768px;
            max-width: 768px;
        }
    }
    .barcode_preview {
        width: 50%;
        height: auto;
    }
    textarea.form-control {
        height: auto !important;
    }
</style>

<div class="row">
    <div class="form-group  col-md-6">
        {{ Form::label('product_category_id', __('Category')) }}
        {{ Form::select('product_category_id', $category, null, array('class'=>'form-control font-style selectric', 'required'=>'required')) }}
    </div>
      
    <div class="form-group col-md-6">
        {{ Form::label('product_sub_category_id', __('Sub Category')) }}
        <div class="input-group">
            {{ Form::select('product_sub_category_id', $subcategory, null, array('class'=>'form-control customer-sel font-style', 'required'=>'required')) }}
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('name', __('Name')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-address-card"></i>
                    </div>
                </div>
                {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('barcode', __('Barcode')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-key"></i>
                    </div>
                </div>
                {{ Form::text('barcode', null, array('class' => 'form-control', 'readonly'=>'true')) }}
                {{ Form::hidden('product_id', null, array('class' => 'form-control')) }}

                <button type="button" class="btn btn-info" onclick="generateBarcode()"><i class="fas fa-print"></i> Print</button>
            </div>

            <div class="col-md-12 col-lg-12" style="text-align: center; padding: 10px;">
                <span id="barcode_preview_container" style="width: 49mm; height: 20mm;"></span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group  col-md-12">
        {{ Form::label('description', __('Description')) }}
        {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'10']) !!}
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('sale_price', __('Sale Price')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-money-bill-alt"></i>
                    </div>
                </div>
                {{ Form::number('sale_price', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('purchase_price', __('Purchase Price')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-money-bill-alt"></i>
                    </div>
                </div>
                {{ Form::number('purchase_price', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
            </div>
        </div>
    </div>
</div>

<div class="row"> 
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('quantity', __('Quantity')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                
                </div>
                {{ Form::text('quantity',null, array('class' => 'form-control','required'=>'required')) }}
            </div>
        </div>
    </div>
        
    <div class="form-group col-md-6">
        {{ Form::label('ware_house_id', __('Warehouse')) }}
        <div class="input-group">
            {{ Form::select('ware_house_id',$warehouse,null, array('class' => 'form-control customer-sel font-style','required'=>'required')) }}
        </div>
    </div>
</div> 

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <h4>Warranty Details</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('warranty_period', __('Warranty Period')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    
                </div>
                {{ Form::text('warranty_period',null, array('class' => 'form-control','required'=>'required')) }}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}

<script>
    // Barcode Printing
    function generateBarcode() {
        $("#barcode_preview_container").html("");

        var barcodeDetails = $('#barcode').val();
		$("#barcode_preview_container").append("<svg class='barcode_preview'></svg><p style='display: block !important; page-break-after: always !important;'></p>");

        JsBarcode(".barcode_preview", barcodeDetails, {
            displayValue: true,
            width: 4.5,
            height: 160,
            fontSize: 52
        });

        $("#barcode_preview_container").printThis({
            canvas: true,
            importCSS: true,
            importStyle: true,
        });
    }
</script>

