<style>
    #detail_type_description {
        font-size: 12px;
    }
</style>

{{Form::model($chartOfAccount, array('route' => array('chartofaccount.update', $chartOfAccount->id), 'method' => 'POST', 'onsubmit'=>'return validateForm()')) }}

<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('account_type_id', __('Account Type')) }}
            {{Form::select('account_type_id', $accountTypes, $chartOfAccount->account_type_id, array('class'=>'form-control customer-sel font-style'))}}
        </div>
    </div>

    <input type="hidden" value="{{$chartOfAccount->id}}" id="chartOfAccountId">

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('detail_type_id', __('Detail Type')) }}
            <select class="form-control customer-sel font-style" id="detail_type_id" name="detail_type_id">
                <option value="">-- Select Detail Type --</option>
                @foreach ($detailTypes as $detailType)
                    <option value="{{ $detailType->id }}" data-description="{{ $detailType->description }}" @if ($detailType->id == $chartOfAccount->detail_type_id) selected @endif>{{ $detailType->detail_type_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('account_name', __('Account Name')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-file-signature"></i>
                    </div>
                </div>
                {{Form::text('account_name', $chartOfAccount->account_name, array('class'=>'form-control'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('description', __('Description')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-file-signature"></i>
                    </div>
                </div>
                {{Form::text('description', $chartOfAccount->description, array('class'=>'form-control'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12" id="detail_type_description">{{ $chartOfAccount->detailType->description }}</div>
</div>

<hr></hr>

<div class="row" id="bank_account_section" @if (!$chartOfAccount->bank_account_id) style="display: none;" @endif>
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('bank_account_id', __('Bank Account')) }}
            {{Form::select('bank_account_id', $bankAccounts, $chartOfAccount->bank_account_id, array('class'=>'form-control customer-sel font-style'))}}
        </div>
    </div>
</div>

<div class="row" id="product_category_section" @if ($chartOfAccount->detail_type_id != 6 && $chartOfAccount->detail_type_id != 88) style="display: none;" @endif>
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('product_category_id', __('Product Category')) }}
            <br>

            <div id="product_category_checkboxes"></div>
         </div>
    </div>
</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
</div>

{{Form::close()}}

<script>
    $(document).ready(function () {
        var detail_type_id = $('#detail_type_id').val();
        if(detail_type_id){
            getProductCategories(detail_type_id);
        }

    })

    $('#account_type_id').on('click', function(){
        const account_type_id = $(this).val();

        $.ajax({
            url: "{{ route('chartofaccount.get_detail_type') }}",
            type: 'GET',
            data: {
                account_type_id: account_type_id
            },
            success: function(data) {
                let detail_type_options = `<option value="" data-description="">-- Select Detail Type --</option>`;
                for (let i = 0; i < data.length; i++) {
                    let description = data[i].description.replace(/'/g, '&#39;').replace(/"/g, '&quot;');
                    detail_type_options += `<option value="${data[i].id}" data-description="${description}">${data[i].detail_type_name}</option>`;
                }

                $('#detail_type_id').html(detail_type_options);
                $('#detail_type_description').html('');
            }
        });
    });

    $('#detail_type_id').on('click', function(){
        const detail_type_id = $(this).val();

        if (detail_type_id == 15) { // Bank
            getProductCategories(detail_type_id);
            $('#bank_account_section').fadeIn(500);
        } else {
            $('#bank_account_section').fadeOut(500);
        }

        const product_category_ids = [6, 88, 140, 96, 141, 142, 143];
        if (product_category_ids.includes(detail_type_id)) { // Inventory || Sales of Product Income
            getProductCategories(detail_type_id);
            $('#product_category_section').fadeIn(500);
        } else {
            $('#product_category_section').fadeOut(500);
        }

        const detail_type = $(this).find(':selected').text();
        $('#account_name').val(detail_type);

        const description = $(this).find(':selected').data('description');
        $('#detail_type_description').html(description);
    });

    function getProductCategories(detail_type_id) {
        $.ajax({
            url: "{{ route('chartofaccount.get_product_categories') }}",
            type: 'GET',
            data: {
                detail_type_id: detail_type_id,
                chart_of_account_id: $('#chartOfAccountId').val()
            },
            success: function(data) {
                console.log(data);
                let product_categories = ``;
                for (let i = 0; i < data.length; i++) {
                    product_categories += `<div class="product-category-checkboxes">
                                                <input type="checkbox" ${data[i].checked?'checked':''}  id="${data[i].name}" name="product_category[]" value="${data[i].id}" />
                                                <label for="${data[i].name}">${data[i].name}</label>
                                            </div>`;
                }

                $('#product_category_checkboxes').html(product_categories);
                $('#product_category_section').show();
            }
        });
    }

    function validateForm() {
        const account_type_id = $('#account_type_id').val();
        const detail_type_id = $('#detail_type_id').val();
        const account_name = $('#account_name').val();
        const bank_account_id = $('#bank_account_id').val();

        if (account_type_id == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select an Account Type',
            })
            .then((value) => {
                $('#account_type_id').focus();
            });
            return false;

        } else if (detail_type_id == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select a Detail Type',
            })
            .then((value) => {
                $('#detail_type_id').focus();
            });
            return false;

        } else if (account_name == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select a Account Name',
            })
            .then((value) => {
                $('#account_name').focus();
            });
            return false;

        }
        // else if (detail_type_id == 15 && bank_account_id == '') {
        //     Swal.fire({
        //         icon: 'error',
        //         title: 'Oops...',
        //         text: 'Please select a Bank Account',
        //     })
        //     .then((value) => {
        //         $('#bank_account_id').focus();
        //     });
        //     return false;
        // }
    }
</script>
