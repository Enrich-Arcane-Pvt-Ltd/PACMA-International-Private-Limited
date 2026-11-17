{{ Form::open(array('url' => 'vehicle_loan/save','enctype' => "multipart/form-data")) }}
<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('date', __('Date')) }}
        <div class="input-group">
            {{ Form::text('date', '', array('class' => 'form-control datepicker','required'=>'required', 'data-empty-message'=>"Please Select Date of Sale", 'data-focus'=>"date_of_sale")) }}
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('customer', __('Customer')) }}
        {{-- {{ Form::text('name', '', array('class' => 'form-control','required'=>'required')) }} --}}
        {{ Form::select('customer', $customers, null, array('class' => 'form-control selectpicker', 'data-live-search'=>'true','required'=>'required')) }}
    </div>
    {{-- <div class="form-group col-md-6">
        {{ Form::label('amount', __('Center')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div> --}}

    {{-- <div class="form-group col-md-6">
        {{ Form::label('amount', __('Group')) }}
        {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }}
    </div> --}}
    <div class="form-group col-md-6">
        {{ Form::label('business_category', __('Business Category')) }}
        {{ Form::select('business_category', $busiCategories, null, array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('reason', __('Reason')) }}
        {{ Form::text('reason', '', array('class' => 'form-control')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('guarantor_1', __('Guarantor 1')) }}
        {{ Form::select('guarantor_1', $guarantors, null, array('class' => 'form-control selectpicker', 'data-live-search'=>'true','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('guarantor_2', __('Guarantor 2')) }}
        {{ Form::select('guarantor_2', $guarantors, null, array('class' => 'form-control selectpicker', 'data-live-search'=>'true', 'data-none-selected-text'=>'-- Guarantor --')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('guarantor_3', __('Guarantor 3')) }}
        {{ Form::select('guarantor_3', $guarantors, null, array('class' => 'form-control selectpicker', 'data-live-search'=>'true', 'data-none-selected-text'=>'-- Guarantor --')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('guarantor_4', __('Guarantor 4')) }}
        {{ Form::select('guarantor_4', $guarantors, null, array('class' => 'form-control selectpicker', 'data-live-search'=>'true', 'data-none-selected-text'=>'-- Guarantor --')) }}
    </div>

    <div class="row  m-2 rounded">
        <p class=" col-md-12">Vehicle Details</p>

        <div class="form-group col-md-6">
            <label for="person_1_phone">Vehicle Category</label>
            <div class="input-group colorpickerinput">
                <select name="vehicle_category" id="vehicle_category" class="form-control" required>
                    <option value="">Select Type</option>
                    <option value="2">Cars</option>
                    <option value="3">Bus</option>
                    <option value="4">SUVs</option>
                    <option value="5">Trucks</option>
                    <option value="6">Vans and Minivans</option>
                    <option value="7">Motorcycles</option>
                    <option value="8">Electric Vehicles</option>
                    <option value="9">Hybrid Vehicles</option>
                </select>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_name">Vehicle Number</label>
            <input type="text" name="vehicle_number" class="form-control" >
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_name">Chassis Number</label>
            <input type="text" name="vehicle_number" class="form-control" >
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_name">Vehicle Registered Number</label>
            <input type="text" name="vehicle_number" class="form-control" >
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_name">Registration Status</label>
            <div class="input-group colorpickerinput">
                <select name="registration" id="registration" class="form-control" required>
                    <option value="">Select</option>
                    <option value="2">Registered</option>
                    <option value="3">Unregistered</option>
                    <option value="3">Recondition</option>
                </select>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_name">Engine Number</label>
            <input type="text" name="vehicle_number" class="form-control" >
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_phone">Manufacture year</label>
            <input type="text" name="manufacturer_year" class="form-control" >
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_phone">Manufacturer</label>
            <input type="text" name="manufacturer" class="form-control" >
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_phone">Vehicle Make</label>
            <input type="text" name="vehicle_make" class="form-control" >
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_phone"> Name of Supplier / Dealer </label>
            <input type="text" name="n_supplier" class="form-control" >
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_phone"> Address of Supplier / Dealer </label>
            <input type="text" name="a_supplier" class="form-control" >
        </div>

    </div>

    <div class="row  m-2 rounded">
        <p class=" col-md-12">Other Details</p>
        <div class="form-group col-md-6">
            <label for="person_1_name">Cost of Vehicle</label>
            <div class="input-group colorpickerinput">
            <input type="text" name="c_vehicle" class="form-control"  oninput="calculateTotalCost()" >
            <div class="input-group-append">
                    <div class="input-group-text">
                        {{\Auth::user()->currencySymbol()}}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_name">Copy of Invoice</label>
            <input type="file" class="form-control form-input" id="copy_of_invoice" name="copy_of_invoice" onchange="uploadFile_document()" style="overflow: hidden;" />
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_name">Cost of Insurance</label>
            <div class="input-group colorpickerinput">
            <input type="text" name="c_insurance" class="form-control"  oninput="calculateTotalCost()" >
            <div class="input-group-append">
                    <div class="input-group-text">
                        {{\Auth::user()->currencySymbol()}}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_phone">Cost of Accessories</label>
            <div class="input-group colorpickerinput">
            <input type="text" name="c_accessories" class="form-control"  oninput="calculateTotalCost()" >
            <div class="input-group-append">
                    <div class="input-group-text">
                        {{\Auth::user()->currencySymbol()}}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_phone">Margin/Applications Contribution</label>
            <div class="input-group colorpickerinput">
            <input type="text" name="contribution" class="form-control"  oninput="calculateTotalCost()" >
            <div class="input-group-append">
                    <div class="input-group-text">
                        {{\Auth::user()->currencySymbol()}}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_phone">Other</label>
            <div class="input-group colorpickerinput">
            <input type="text" name="c_other" class="form-control"  oninput="calculateTotalCost()" >
            <div class="input-group-append">
                    <div class="input-group-text">
                        {{\Auth::user()->currencySymbol()}}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="person_2_phone">Total Cost</label>
            <div class="input-group colorpickerinput">
                <input type="text" name="total_c" class="form-control" >
                <div class="input-group-append">
                    <div class="input-group-text">
                        {{\Auth::user()->currencySymbol()}}
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="row">
    <label for="non_relations" style="background-color: white; margin-bottom: -11px;  z-index: 1; font-size: 15px; padding-left: 6px; padding-right: 6px;">Non Relations</label>
    <fieldset id="non_relations" class="form-group col-md-12" style="border: 2px solid #d2d2d2c7; border-radius: 7px; padding: 14px;">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="person_1_name">1<sup>st</sup> Person Name</label>
                {{ Form::text('person_1_name', '', array('class' => 'form-control')) }}
            </div>
            <div class="form-group col-md-6">
                <label for="person_1_phone">1<sup>st</sup> Person Phone No</label>
                {{ Form::number('person_1_phone', '', array('class' => 'form-control')) }}
            </div>
            <div class="form-group col-md-6">
                <label for="person_2_name">2<sup>nd</sup> Person Name</label>
                {{ Form::text('person_2_name', '', array('class' => 'form-control')) }}
            </div>
            <div class="form-group col-md-6">
                <label for="person_2_phone">2<sup>nd</sup> Person Phone No</label>
                {{ Form::number('person_2_phone', '', array('class' => 'form-control')) }}
            </div>
        </div>
    </fieldset>
</div>
<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('officer_id', __('User')) }}
        {{ Form::select('officer_id', $users, null, array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('loan_amount', __('Loan Amount')) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('loan_amount', '', array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('interest_rate', __('Interest Rate')) }}
        <div class="input-group colorpickerinput">
            {{ Form::text('interest_rate', $interestRate->interest_rate, array('class' => 'form-control','required'=>'required')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{"%"}}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('time_range', __('Installment Collection Time Range')) }}
        <div class="input-group colorpickerinput">
            <select name="time_range" id="time_range" class="form-control" required>
                <option value="">Select Time Range</option>
                <option value="3">Monthly</option>
                <option value="2">Weekly</option>
                {{-- <option value="1">Daily</option> --}}
            </select>
        </div>
    </div>
    <div class="form-group col-md-6" id="installment_date_div">
        {{ Form::label('installment_date', __('First'), array('id'=>'first')) }} {{ Form::label('installment_date', __('Installment Date')) }}
        <div class="input-group colorpickerinput">
            <select class="form-control" name="installment_date" id="installment_date" required>
                <option value="">Select Installment Date</option>
                @for ($i = 1; $i <= 31; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('time_period', __('Time Period')) }}
        <div class="input-group colorpickerinput">
            {{ Form::select('time_period', $timeperiods, null, array('class' => 'form-control','required'=>'required')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{__('Month')}}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('interest_amount', __('Interest')) }}
        <div class="input-group colorpickerinput">
            {{ Form::text('interest_amount', '', array('class' => 'form-control','required'=>'required')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('total_amount', __('Total Amount')) }}
        <div class="input-group colorpickerinput">
            {{ Form::text('total_amount', '', array('class' => 'form-control','required'=>'required')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6" id="monthly_installment_div">
        {{ Form::label('monthly_installment', __('Monthly Installment')) }}
        <div class="input-group colorpickerinput">
            {{ Form::text('monthly_installment', '', array('class' => 'form-control','required'=>'required')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6" id="weekly_installment_div">
        {{ Form::label('weekly_installment', __('Weekly Installment')) }}
        <div class="input-group colorpickerinput">
            {{ Form::text('weekly_installment', '', array('class' => 'form-control','required'=>'required')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6" id="daily_installment_div">
        {{ Form::label('daily_installment', __('Daily Installment')) }}
        <div class="input-group colorpickerinput">
            {{ Form::text('daily_installment', '', array('class' => 'form-control','required'=>'required')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('due_date', __('Due Date')) }}
        <div class="input-group colorpickerinput">
            {{ Form::text('due_date','', array('class' => 'form-control','readonly'=>'readonly','required'=>'required')) }}
        </div>
    </div>
</div>
<div class="row">
    <input type="hidden" name="document_charges_in_words" id="document_charges_in_words">
    <div class="form-group col-md-12">
        {{ Form::label('document_charges', __('Document Charges')) }}
        <div class="row">
            <div class="form-group col-md-6">
                <div class="row">
                    <div class="form-group col-md-3" style="text-align: -webkit-right;">
                        <input type="radio" name="doc_type" class="form-control-radio" id="fixed" value="1">
                    </div>
                    <div class="form-group col-md-3">
                        {{ Form::label('fixed', __('Fixed'), array('class' => 'radio-lable')) }}
                    </div>
                    <div class="form-group col-md-3" style="text-align: -webkit-right;">
                        <input type="radio" name="doc_type" class="form-control-radio" id="flexible" value="2">
                    </div>
                    <div class="form-group col-md-3">
                        {{ Form::label('flexible', __('Flexible'), array('class' => 'radio-lable')) }}
                    </div>
                </div>
            </div>
            <div class="form-group col-md-6">
                <div class="input-group colorpickerinput">
                    {{ Form::text('document_charges', '', array('class' => 'form-control','required'=>'required')) }}
                    <div class="input-group-append">
                        <div class="input-group-text">
                            {{\Auth::user()->currencySymbol()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('application_document', __('Upload application')) }}
        {{-- {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }} --}}
        <input type="file" class="form-control form-input" id="application_document" name="application_document" onchange="uploadFile_document()" style="overflow: hidden;" />
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('other_document', __('Upload Document')) }}
        {{-- {{ Form::text('amount', '', array('class' => 'form-control','required'=>'required')) }} --}}
        <input type="file" class="form-control form-input" id="other_document" name="other_document" onchange="uploadFile_document()" style="overflow: hidden;" />
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();

        $('#interest_amount').prop('disabled', true);
        $('#total_amount').prop('disabled', true);
        $('#monthly_installment').prop('disabled', true);
        // $('#installment_date').prop('disabled', true);
        $('#document_charges').prop('disabled', true);
        $('#guarantor_2').prop('disabled', true);
        $('#guarantor_3').prop('disabled', true);
        $('#guarantor_4').prop('disabled', true);

        $('#monthly_installment_div').hide();
        $('#weekly_installment_div').hide();
        $('#installment_date_div').hide();
        $('#daily_installment_div').hide();
        $('#first').hide();

        $('.selectpicker').selectpicker('refresh');
    });

    $('body').on('change', '#guarantor_1', function(){
        var guarantor_1 = $(this).val();
        $.ajax({
            url:"{{ url('loan/guarantor_1_search') }}/"+guarantor_1,
            method:'GET',
            dataType:'json',
                success: function (data, textStatus, jqXHR){
                    // console.log(data);
                    if(data.length > 0){
                        $("#guarantor_2").html("");
                        $('#guarantor_2').append('<option value = "">-- Guarantor --</option>');
                            for (var i = 0; i < data.length; i++) {
                                $("#guarantor_2").append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
                            }
                        $('#guarantor_2').prop('disabled', false);
                        $('.selectpicker').selectpicker('refresh');
                    }
                    else{
                        $("#guarantor_2").html("");
                        $('#guarantor_2').append('<option value = "">Nothing Selected</option>');
                        $('.selectpicker').selectpicker('refresh');
                    }
                }
            });
    });

    $('body').on('change', '#guarantor_2', function(){
        var guarantor_1 = $("#guarantor_1").val();
        var guarantor_2 = $(this).val();
        $.ajax({
            url:"{{ url('loan/guarantor_2_search') }}/"+guarantor_1 + "/" + guarantor_2,
            method:'GET',
            dataType:'json',
                success: function (data, textStatus, jqXHR){
                    // console.log(data);
                    if(data.length > 0){
                        $("#guarantor_3").html("");
                        $('#guarantor_3').append('<option value = "">-- Guarantor --</option>');
                            for (var i = 0; i < data.length; i++) {
                                $("#guarantor_3").append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
                            }
                        $('#guarantor_3').prop('disabled', false);
                        $('.selectpicker').selectpicker('refresh');
                    }
                    else{
                        $("#guarantor_3").html("");
                        $('#guarantor_3').append('<option value = "">Nothing Selected</option>');
                        $('.selectpicker').selectpicker('refresh');
                    }
                }
            });
    });

    $('body').on('change', '#guarantor_3', function(){
        var guarantor_1 = $("#guarantor_1").val();
        var guarantor_2 = $("#guarantor_2").val();
        var guarantor_3 = $(this).val();
        $.ajax({
            url:"{{ url('loan/guarantor_3_search') }}/"+guarantor_1 + "/" + guarantor_2 + "/" + guarantor_3,
            method:'GET',
            dataType:'json',
                success: function (data, textStatus, jqXHR){
                    // console.log(data);
                    if(data.length > 0){
                        $("#guarantor_4").html("");
                        $('#guarantor_4').append('<option value = "">-- Guarantor --</option>');
                            for (var i = 0; i < data.length; i++) {
                                $("#guarantor_4").append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
                            }
                        $('#guarantor_4').prop('disabled', false);
                        $('.selectpicker').selectpicker('refresh');
                    }
                    else{
                        $("#guarantor_4").html("");
                        $('#guarantor_4').append('<option value = "">Nothing Selected</option>');
                        $('.selectpicker').selectpicker('refresh');
                    }
                }
            });
    });

    $('body').on('input', '#loan_amount', function(){
        if($(this).val() != '' && $('#interest_rate').val() != '' && $('#time_period').val() != ''){

            finalCalculation();

            if( $('#fixed').prop('checked') == true ){
                docCharge(1);
            }
            if( $('#flexible').prop('checked')== true ){
                docCharge(2);
            }
        }
    });

    $('body').on('change', '#time_range', function(){
        if($(this).val() == 1){
            $('#monthly_installment_div').hide(200);
            $('#weekly_installment_div').hide(200);
            $('#first').show(200);
            $('#installment_date_div').show(200);
            $('#daily_installment_div').show(200);
        } else if($(this).val() == 2){
            $('#daily_installment_div').hide(200);
            $('#monthly_installment_div').hide(200);
            $('#weekly_installment_div').show(200);
            $('#first').show(200);
            $('#installment_date_div').show(200);
        } else if($(this).val() == 3){
            $('#daily_installment_div').hide(200);
            $('#weekly_installment_div').hide(200);
            $('#first').hide(200);
            $('#monthly_installment_div').show(200);
            $('#installment_date_div').show(200);
        } else {
            $('#daily_installment_div').hide(200);
            $('#monthly_installment_div').hide(200);
            $('#weekly_installment_div').hide(200);
            $('#installment_date_div').hide(200);
            $('#first').hide(200);
        }

        if($('#loan_amount').val() != '' && $('#interest_rate').val() != '' && $('#time_range').val() != '' && $('#installment_date').val() != ''){
            finalCalculation();
        }
    });

    $('body').on('change', '#time_period', function(){
        if($('#loan_amount').val() == ''){
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Enter Loan Amount!'
                });
                $(this).val('');
            return this;
        }
        if($('#interest_rate').val() == ''){
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Insert Interest rate!'
                });
                $(this).val('');
            return this;
        }
        if($('#time_range').val() == ''){
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Select Time Range!'
                });
                $(this).val('');
            return this;
        }
        if($('#installment_date').val() == ''){
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Select Installment Date!'
                });
                $(this).val('');
            return this;
        }
        if($(this).val() != ''){
            // if($('#loan_amount').val() != '' && $('#interest_rate').val() != ''){
                $('#interest_amount').prop('disabled', false);
                $('#total_amount').prop('disabled', false);
                $('#monthly_installment').prop('disabled', false);
                // $('#installment_date').prop('disabled', false);

                finalCalculation();
            // } else {
            //     Swal.fire({
            //         icon: 'error',
            //         title: 'Oops...',
            //         text: 'Please Enter Loan Amount and Interest rate!'
            //     });
            //     $('#time_period').val('');

            // }
        } else {
            $('#interest_amount').prop('disabled', true);
            $('#total_amount').prop('disabled', true);
            $('#monthly_installment').prop('disabled', true);
            // $('#installment_date').prop('disabled', true);
        }
    });

    // $('body').on('change', '#installment_date', function(){
    //     var day = $(this).val();
    //     var date = $('#due_date').val();
    //     var seperateddate = date.split('-');
    //     var newDate = ''+seperateddate[0]+'-'+seperateddate[1]+'-'+day+'';

    //     function convert(str) {
    //         var date = new Date(str),
    //             mnth = ("0" + (date.getMonth() + 1)).slice(-2),
    //             day = ("0" + date.getDate()).slice(-2);
    //         return [date.getFullYear(), mnth, day].join("-");
    //     }

    //     var convertedNewDate = convert(newDate);

    //     $('#due_date').val(convertedNewDate);
    // });

    $('body').on('click', '#fixed', function(){
        var type = 1;
        docCharge(type);
    });
    $('body').on('click', '#flexible', function(){
        var type = 2;
        docCharge(type);
    });

    function docCharge(type){
        var time_period = 0;
        var loanAmount = 0;
        var docCharge = 0;

        if($('#time_period').val() != ''){
            time_period = $('#time_period').val();
        }
        if($('#loan_amount').val() != ''){
            loanAmount = $('#loan_amount').val();
        }
        $.ajax({
            url:"{{ url('sme_loan/doc_charge_rate') }}",
            method: 'GET',
            dataType: 'json',
            success: function(data, textStatus, jqXHR){
                if(data != null){
                    if(type == 1){
                        docCharge = parseFloat(loanAmount)  * parseFloat(data.rate)/100;
                    }else if(type == 2){
                        if(time_period < 5){
                            docCharge = parseFloat(loanAmount)  * parseFloat(time_period)/100;
                        } else {
                            docCharge = parseFloat(loanAmount)  * parseFloat(data.rate)/100;
                        }
                    }
                    $('#document_charges').val(docCharge.toFixed(2));
                    $('#document_charges').prop('disabled', false);
                    documentChargeInWords(docCharge);
                }
            }
        });
    }

    function documentChargeInWords(amount){
        var chargeWords = '';
        chargeWords = toWords(amount);
        $("#document_charges_in_words").val(chargeWords + "Only");
    }

    function finalCalculation(){
        var interestRate = parseFloat($('#interest_rate').val())/100;
        var loanAmout = $('#loan_amount').val();
        var timePeriod = $('#time_period').val();
        var timeRange = $('#time_range').val();
        var date = $('#date').val();

        var interestAmount = parseFloat(loanAmout) * parseFloat(timePeriod) * interestRate;
        $('#interest_amount').val(interestAmount.toFixed(2));

        var totDue = parseFloat(loanAmout) + interestAmount;
        $('#total_amount').val(totDue.toFixed(2));

        var monthlyInstallment = totDue/parseFloat(timePeriod);
        $('#monthly_installment').val(monthlyInstallment.toFixed(2));

        var weeklyInstallment = totDue/(parseFloat(timePeriod)*4);
        $('#weekly_installment').val(weeklyInstallment.toFixed(2));

        var dailyInstallment = totDue/(parseFloat(timePeriod)*30);
        $('#daily_installment').val(dailyInstallment.toFixed(2));

        var currentDate = $('#date').val();
        if(timeRange == 3){
            var installmentDate = $('#installment_date').val();
            if(installmentDate != ''){
                var seperatedDate = currentDate.split('-');
                var newDate = ''+seperatedDate[0]+'-'+seperatedDate[1]+'-'+installmentDate+'';
            } else {
                var newDate = currentDate;
            }

            var dateforFunction = new Date(newDate);

            Date.prototype.addMonths = function (months) {
                var dt = this.getDate();

                this.setMonth(this.getMonth() + months);
                var currDt = this.getDate();

                if (dt !== currDt) {
                    this.addDays(-currDt);
                }

                return this;
            };
            Date.prototype.addDays = function(days) {
                this.setDate( this.getDate()  + days);
                return this;
            };

            var duedate = dateforFunction.addMonths(parseFloat(timePeriod));

            function convert(str) {
                var date = new Date(str),
                    mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                    day = ("0" + date.getDate()).slice(-2);
                return [date.getFullYear(), mnth, day].join("-");
            }

            var convertedDueDate = convert(duedate);

            $('#due_date').val(convertedDueDate);
        } else if(timeRange == 2){
            var installmentDate = $('#installment_date').val();
            if(installmentDate != ''){
                var seperatedDate = currentDate.split('-');
                if( parseFloat(installmentDate) < parseFloat(seperatedDate[2]) ){
                    var nxtMonth = parseFloat(seperatedDate[1]) + 1;
                    var newDate = ''+seperatedDate[0]+'-'+ nxtMonth +'-'+installmentDate+'';
                } else {
                    var newDate = ''+seperatedDate[0]+'-'+seperatedDate[1]+'-'+installmentDate+'';
                }
            } else {
                var newDate = currentDate;
            }

            var dateforFunction = new Date(newDate);

            Date.prototype.addMonths = function (months) {
                var dt = this.getDate();

                this.setMonth(this.getMonth() + months);
                var currDt = this.getDate();

                if (dt !== currDt) {
                    this.addDays(-currDt);
                }

                return this;
            };
            Date.prototype.addDays = function(days) {
                this.setDate( this.getDate()  + days);
                return this;
            };

            var totDays = timePeriod*4*7 - 7;

            var duedate = dateforFunction.addDays(parseFloat(totDays));

            function convert(str) {
                var date = new Date(str),
                    mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                    day = ("0" + date.getDate()).slice(-2);
                return [date.getFullYear(), mnth, day].join("-");
            }

            var convertedDueDate = convert(duedate);

            $('#due_date').val(convertedDueDate);
        } else if(timeRange == 1){
            var installmentDate = $('#installment_date').val();
            if(installmentDate != ''){
                var seperatedDate = currentDate.split('-');
                if( parseFloat(installmentDate) < parseFloat(seperatedDate[2]) ){
                    var nxtMonth = parseFloat(seperatedDate[1]) + 1;
                    var newDate = ''+seperatedDate[0]+'-'+ nxtMonth +'-'+installmentDate+'';
                } else {
                    var newDate = ''+seperatedDate[0]+'-'+seperatedDate[1]+'-'+installmentDate+'';
                }
            } else {
                var newDate = currentDate;
            }

            var dateforFunction = new Date(newDate);

            Date.prototype.addMonths = function (months) {
                var dt = this.getDate();

                this.setMonth(this.getMonth() + months);
                var currDt = this.getDate();

                if (dt !== currDt) {
                    this.addDays(-currDt);
                }

                return this;
            };
            Date.prototype.addDays = function(days) {
                this.setDate( this.getDate()  + days);
                return this;
            };

            var totDays = timePeriod*30 - 1;

            var duedate = dateforFunction.addDays(parseFloat(totDays));

            function convert(str) {
                var date = new Date(str),
                    mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                    day = ("0" + date.getDate()).slice(-2);
                return [date.getFullYear(), mnth, day].join("-");
            }

            var convertedDueDate = convert(duedate);

            $('#due_date').val(convertedDueDate);
        }

    }

    function uploadFile_document() {
        var image_preview = document.getElementById("product_document");
        var file = document.querySelector("input[type=file]").files[0];

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
<script>
    function calculateTotalCost() {
        // Get the values entered into the input fields
        var costOfVehicle = parseFloat(document.getElementsByName('c_vehicle')[0].value) || 0;
        var costOfInsurance = parseFloat(document.getElementsByName('c_insurance')[0].value) || 0;
        var costOfAccessories = parseFloat(document.getElementsByName('c_accessories')[0].value) || 0;
        var contribution = parseFloat(document.getElementsByName('contribution')[0].value) || 0;
        var other = parseFloat(document.getElementsByName('c_other')[0].value) || 0;

        // Calculate the total cost
        var totalCost = costOfVehicle + costOfInsurance + costOfAccessories + contribution + other;

        // Update the total cost input field
        document.getElementsByName('total_c')[0].value = totalCost.toFixed(2);
    }
    </script>
