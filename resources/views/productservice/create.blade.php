@php
    $logo=asset(Storage::url('uploads/logo/'));
    $company_logo=Utility::getValByName('company_logo');
    $company_small_logo=Utility::getValByName('company_small_logo');
    $company_favicon=Utility::getValByName('company_favicon');
@endphp
@php
    $logo=asset(Storage::url('uploads/logo/'));
    $lang=\App\Utility::getValByName('default_language');
@endphp
@push('css-page')
    <link href="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.css">
@endpush
@push('script-page')
    <script src="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
    <script src="jquery/1.9.1/jquery.js"></script>
@endpush
{{ Form::open(array('url' => 'productservice','method'=>'POST','enctype' => "multipart/form-data")) }}

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
    <div class="form-group col-md-6">
        {{ Form::label('category', __('Category')) }}<span class="text-muted">*</span>
    <div class="input-group">
        {{ Form::select('category',$category,null, array('class'=>'form-control customer-sel font-style', 'required'=>'required')) }}
    </div>
</div>

<div class="form-group col-md-6">
    {{ Form::label('sub_category', __('Sub Category')) }}<span class="text-muted">*</span>
    <div class="input-group">
        {{ Form::select('sub_category', $productSubCategories, null, array('class'=>'form-control customer-sel font-style','required'=>'required')) }}
    </div>

    </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('name', __('Name')) }}<span class="text-muted">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-address-card"></i>
                        </div>
                    </div>
                    {{ Form::text('name', '', array('class'=>'form-control', 'required'=>'required')) }}
                </div>
            </div>
        </div>
    
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('barcode', __('Barcode')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-barcode"></i>
                        </div>
                    </div>
                    {{ Form::text('barcode', $productCode, array('class'=>'form-control', 'id'=>'barcode', 'readonly'=>'true')) }}
                    {{ Form::hidden('product_id', $productCode, array('class'=>'form-control',)) }}

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
            {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'5']) !!}
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sale_price', __('Sale Price')) }}<span class="text-muted">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="far fa-money-bill-alt"></i>
                        </div>
                    </div>
                    {{ Form::number('sale_price', '', array('class'=>'form-control','required'=>'required','step'=>'0.01')) }}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('purchase_price', __('Purchase Price')) }}<span class="text-muted">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="far fa-money-bill-alt"></i>
                        </div>
                    </div>
                    {{ Form::number('purchase_price', '', array('class'=>'form-control','required'=>'required','step'=>'0.01')) }}
                </div>
            </div>
        </div>
    </div>

    <div class="row"> 
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('quantity', __('Quantity')) }}<span class="text-muted">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                    
                    </div>
                    {{ Form::text('quantity', '', array('class' => 'form-control','required'=>'required')) }}
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('warehouse', __('Warehouse')) }}<span class="text-muted">*</span>
            <div class="input-group">
                {{ Form::select('warehouse',$warehouse,null, array('class' => 'form-control customer-sel font-style','required'=>'required')) }}
            </div>
        </div>
    
    </div> 

    <div class="row">
        <div class="col-lg-4">
            <label class="form-lable">Image 1<span class="text-muted">*</span></label>
            <input type="file" class="form-control form-input" id="product_image_1" name="product_image_1" onchange="uploadFile_1()" required style="overflow: hidden;" />

            <div class="image_container">
                <img src="{{ asset('assets') }}/img/products/default.png" id="image_preview_1" />
            </div>
        </div>

        <div class="col-lg-4">
            <label class="form-lable">Image 2<span class="text-muted">*</span></label>
            <input type="file" class="form-control form-input" id="product_image_2" name="product_image_2" onchange="uploadFile_2()" required style="overflow: hidden;" />

            <div class="image_container">
                <img src="{{ asset('assets') }}/img/products/default.png" id="image_preview_2" />
            </div>
        </div>
        
        <div class="col-lg-4">
            <label class="form-lable">Image 3<span class="text-muted">*</span></label>
            <input type="file" class="form-control form-input" id="product_image_3" name="product_image_3" onchange="uploadFile_3()" required style="overflow: hidden;" />

            <div class="image_container">
                <img src="{{ asset('assets') }}/img/products/default.png" id="image_preview_3" />
            </div>
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
            {{ Form::label('warranty_period', __('Warranty Period')) }}<span class="text-muted">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    
                </div>
                {{ Form::text('warranty_period', '', array('class' => 'form-control','required'=>'required')) }}
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-lable">Warranty Document</label>
            <input type="file" class="form-control form-input" id="product_document" name="product_document" onchange="uploadFile_document()" required style="overflow: hidden;" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
            {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
        </div>
    </div>
{{ Form::close() }}

<script src="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
   
<script type="text/javascript">
    $(document).ready(function() {
        $(".btn-success").click(function(){ 
            var lsthmtl = $(".clone").html();
            $(".increment").after(lsthmtl);
        });

        $("body").on("click",".btn-danger",function(){ 
            $(this).parents(".hdtuto control-group lst").remove();
        });

        // Clear Barcode Container
        $("#barcode_preview_container").html("");
    });

    function uploadFile_1() {
        var image_preview = document.getElementById("image_preview_1");
        var file = document.getElementById("product_image_1").files[0];

        var reader = new FileReader();
        reader.onloadend = function () {
            image_preview.src = reader.result;
            document.getElementById("base64").value = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);

        } else {
            image_preview.src = "";

        }
    }

    function uploadFile_2() {
        var image_preview = document.getElementById("image_preview_2");
        var file = document.getElementById("product_image_2").files[0];

        var reader = new FileReader();
        reader.onloadend = function () {
            image_preview.src = reader.result;
            document.getElementById("base64").value = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);
        } else {
            image_preview.src = "";

        }
    }

    function uploadFile_3() {
        var image_preview = document.getElementById("image_preview_3");
        var file = document.getElementById("product_image_3").files[0];

        var reader = new FileReader();
        reader.onloadend = function () {
            image_preview.src = reader.result;
            document.getElementById("base64").value = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);

        } else {
            image_preview.src = "";

        }
    }
    
    function uploadFile_document() {
        var image_preview = document.getElementById("product_document");
        var file = document.querySelector("input[type=file]").files[0];

        var reader = new FileReader();
        reader.onloadend = function () {
            image_preview.src = reader.result;
            document.getElementById("base64").value = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);

        } else {
            image_preview.src = "";

        }
    }

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
