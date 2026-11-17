@php
    $logo = asset(Storage::url('uploads/logo/'));
    $company_logo = Utility::getValByName('company_logo');
    $company_small_logo = Utility::getValByName('company_small_logo');
    $company_favicon = Utility::getValByName('company_favicon');
@endphp

@php
    $logo = asset(Storage::url('uploads/logo/'));
    $lang = \App\Utility::getValByName('default_language');
@endphp

@push('css-page')
    <link href="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.css">
@endpush

@push('script-page')
    <script src="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
    <script src="jquery/1.9.1/jquery.js"></script>
@endpush

<style>
    .image_container {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        width: 100%;
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        overflow: hidden;
    }

    .image_container img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
    }

    @media (min-width: 576px) {
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

{{ Form::open(['url' => 'product', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
<h4 class="sub-title">{{ __('Product Info') }}</h4>
<div class="row">

    <div class="col-lg-4 col-md-4 col-sm-4">
        <div class="form-group">
            {{ Form::label('product_category', __('Main Category'), ['class' => '']) }}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-sitemap"></i>
                    </div>
                </div>
                {!! Form::select('product_category', $productCategory, null, [
                    'class' => 'form-control customer-sel font-style',
                    'required' => 'required',
                    'placeholder' => '- Select -',
                    'id' => 'product_category',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4">
        <div class="form-group">
            {{ Form::label('product_sub_category', __('Sub Category'), ['class' => '']) }}<span
                class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-sitemap"></i>
                    </div>
                </div>
                {!! Form::select('product_sub_category', [], null, [
                    'class' => 'form-control customer-sel font-style',
                    'required' => 'required',
                    'placeholder' => __('Select a main category first'),
                    'id' => 'product_sub_category',
                    'disabled' => true,
                ]) !!}
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4">
        <div class="form-group">
            {{ Form::label('product_brand', __('Brand'), ['class' => '']) }}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-tag"></i>
                    </div>
                </div>
                {!! Form::select('product_brand', $productBrand, null, [
                    'class' => 'form-control customer-sel font-style',
                    'required' => 'required',
                    'placeholder' => '- Select -',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="col-lg-8 col-md-8 col-sm-8">
        <div class="form-group">
            {{ Form::label('name', __('Name'), ['class' => '']) }}<span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
                {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
    </div>

    {{-- <div class="col-lg-4 col-md-4 col-sm-4">
        <div class="form-group">
            {{ Form::label('serial_number', __('Serial Number'), ['class' => '']) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-memory"></i>
                    </div>
                </div>
                {{ Form::text('serial_number', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4">
        <div class="form-group">
            {{ Form::label('product_color', __('Color'), ['class' => '']) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                </div>
                {{ Form::text('product_color', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div> --}}

    <div class="col-lg-4 col-md-4 col-sm-4">
        <div class="form-group">
            {{ Form::label('barcode', __('Product Code (This should be unique)')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-barcode"></i>
                    </div>
                </div>
                {{ Form::text('barcode', null, ['class' => 'form-control', 'id' => 'barcode', 'placeholder' => __('Enter Product Code')]) }}
                {{ Form::hidden('product_id', $productCode, ['class' => 'form-control']) }}
                <button type="button" class="btn btn-info" onclick="generateBarcode()"><i class="fas fa-print"></i>
                    Print</button>
            </div>

            <div class="col-md-12 col-lg-12" style="text-align: center; padding: 10px;">
                <span id="barcode_preview_container" style="width: 49mm; height: 20mm;"></span>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div id="barcode_print_container" style="display: none;">
            <div id="barcode_container" style="width: 100%; height: 100%; text-align: center; margin-left: 0.5mm;">
            </div>
        </div>
    </div>

    {{-- <div class="col-lg-6 col-md-6 col-sm-6">
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
        </div> --}}

    {{-- <div class="col-lg-6 col-md-6 col-sm-6">
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
        </div> --}}

    <div class="form-group  col-md-12">
        {{ Form::label('description', __('Description')) }}<span class="text-danger">*</span>
        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '5', 'required' => 'required']) !!}
    </div>

    {{-- to change --}}
    <div class="col-lg-4">
        <label class="form-lable">Image 1</label>
        <input type="file" class="form-control form-input" id="product_image_1" name="product_image_1"
            onchange="uploadFile_1()" style="overflow: hidden;" accept="image/*" />

        <div class="image_container">
            <img src="{{ asset('assets') }}/img/products/default.png" id="image_preview_1" />
        </div>
    </div>

    <div class="col-lg-4">
        <label class="form-lable">Image 2</label>
        <input type="file" class="form-control form-input" id="product_image_2" name="product_image_2"
            onchange="uploadFile_2()" style="overflow: hidden;" accept="image/*" />

        <div class="image_container">
            <img src="{{ asset('assets') }}/img/products/default.png" id="image_preview_2" />
        </div>
    </div>

    <div class="col-lg-4">
        <label class="form-lable">Image 3</label>
        <input type="file" class="form-control form-input" id="product_image_3" name="product_image_3"
            onchange="uploadFile_3()" style="overflow: hidden;" accept="image/*" />

        <div class="image_container">
            <img src="{{ asset('assets') }}/img/products/default.png" id="image_preview_3" />
        </div>
    </div>

</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn btn-primary']) }}
</div>
{{ Form::close() }}

<script src="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    (function () {
        var subCategories = @json($productSubCategory);
        var category = document.getElementById('product_category') || document.querySelector('select[name="product_category"]');
        var subCategory = document.getElementById('product_sub_category') || document.querySelector('select[name="product_sub_category"]');
        if (!category || !subCategory) return;
        var form = category.form;

        function filterSubCategories() {
            var matches = subCategories.filter(function (item) {
                return String(item.product_category_id) === category.value;
            });
            var placeholder = !category.value
                ? @json(__('Select a main category first'))
                : (matches.length ? @json(__('- Select -')) : @json(__('No sub categories available')));

            subCategory.options.length = 0;
            subCategory.add(new Option(placeholder, ''));
            matches.forEach(function (item) {
                subCategory.add(new Option(item.name, item.id));
            });
            subCategory.disabled = !category.value || matches.length === 0;
            $(subCategory).trigger('change');
            // A disabled required select does not block submission on its own.
            $(form).find('[type="submit"]').prop('disabled', subCategory.disabled);
        }

        $(category).on('change', filterSubCategories);
        filterSubCategories();
    })();

    // Barcode Printing
    function generateBarcode() {
        $("#barcode_preview_container").html("");

        var barcodeDetails = ($('#barcode').val() || '').trim();
        if (!barcodeDetails) {
            AppMessages.warning('{{ __("Please enter a Product Code first.") }}');
            $('#barcode').focus();
            return;
        }
        $("#barcode_preview_container").append(
            "<svg class='barcode_preview'></svg><p style='display: block !important; page-break-after: always !important;'></p>"
        );

        JsBarcode(".barcode_preview", barcodeDetails, {
            displayValue: true,
            width: 4.5,
            height: 160,
            fontSize: 52
        });

        $("#barcode_container").html("");

        $("#barcode_container").append("<svg class='print_barcode_preview' style='margin-left: 0.5mm;'></svg><p></p>");

        JsBarcode(".print_barcode_preview", barcodeDetails, {
            displayValue: true,
            width: 1.37,
            height: 33,
            fontSize: 12
        });

        var divContents = document.getElementById("barcode_print_container").innerHTML;
        var a = window.open("", "");
        a.document.write("<html>");
        a.document.write("<body>");
        a.document.write(divContents);
        a.document.write("</body></html>");
        a.document.close();

        setTimeout(function() {
            a.print();
            a.close();
        }, 1000); // Delay printing by 1 second (adjust as needed)
    }

    function uploadFile_1() {
        var image_preview = document.getElementById("image_preview_1");
        var file = document.getElementById("product_image_1").files[0];

        var reader = new FileReader();
        reader.onloadend = function() {
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
        reader.onloadend = function() {
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
        reader.onloadend = function() {
            image_preview.src = reader.result;
            document.getElementById("base64").value = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);

        } else {
            image_preview.src = "";

        }
    }
</script>
