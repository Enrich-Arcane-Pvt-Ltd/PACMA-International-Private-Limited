{{ Form::open(array('url' => 'loan/update','enctype' => "multipart/form-data")) }}
<div class="row">
    <input type="hidden" id="loan_id" name="loan_id" value="{{ $loan->id }}">
    <div class="form-group col-md-6">
        {{ Form::label('date', __('Date')) }}
        <div class="input-group">
            {{ Form::text('date', $loan->date, array('class' => 'form-control datepicker','required'=>'required', 'data-empty-message'=>"Please Select Date of Sale", 'data-focus'=>"date_of_sale")) }}
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
        {{ Form::select('customer', $customers, $loan->customer_id, array('class' => 'form-control','required'=>'required')) }}
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
        {{ Form::select('business_category', $busiCategories, $loan->business_category_id, array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('reason', __('Reason')) }}
        {{ Form::text('reason', $loan->reason, array('class' => 'form-control')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('guarantor_1', __('Guarantor 1')) }}
        {{ Form::select('guarantor_1', $guarantors, $loan->guarantor_1, array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('guarantor_2', __('Guarantor 2')) }}
        {{ Form::select('guarantor_2', $guarantors, $loan->guarantor_2, array('class' => 'form-control')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('guarantor_3', __('Guarantor 3')) }}
        {{ Form::select('guarantor_3', $guarantors, $loan->guarantor_3, array('class' => 'form-control')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('guarantor_4', __('Guarantor 4')) }}
        {{ Form::select('guarantor_4', $guarantors, $loan->guarantor_4, array('class' => 'form-control')) }}
    </div>
</div>
<div class="row">
    <label for="non_relations" style="background-color: white; margin-bottom: -11px;  z-index: 1; font-size: 15px; padding-left: 6px; padding-right: 6px;">Non Relations</label>
    <fieldset id="non_relations" class="form-group col-md-12" style="border: 2px solid #d2d2d2c7; border-radius: 7px; padding: 14px;">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="person_1_name">1<sup>st</sup> Person Name</label>
                {{ Form::text('person_1_name', $loan->person_1_name, array('class' => 'form-control')) }}
            </div>
            <div class="form-group col-md-6">
                <label for="person_1_phone">1<sup>st</sup> Person Phone No</label>
                {{ Form::number('person_1_phone', $loan->person_1_phone, array('class' => 'form-control')) }}
            </div>
            <div class="form-group col-md-6">
                <label for="person_2_name">2<sup>nd</sup> Person Name</label>
                {{ Form::text('person_2_name', $loan->person_2_name, array('class' => 'form-control')) }}
            </div>
            <div class="form-group col-md-6">
                <label for="person_2_phone">2<sup>nd</sup> Person Phone No</label>
                {{ Form::number('person_2_phone', $loan->person_2_phone, array('class' => 'form-control')) }}
            </div>
        </div>
    </fieldset>
</div>
<div class="row">
    <div class="form-group col-md-5">
        {{ Form::label('officer_id', __('User')) }}
        {{ Form::select('officer_id', $users, $loan->officer_id, array('class' => 'form-control','required'=>'required')) }}
    </div>

    <div class="form-group col-md-7">
        {{ Form::label('time_range', __('Loan Referance Number')) }}
        <div class="input-group colorpickerinput">
            <div class="row">
                <div class="col-4">
                    {{ Form::text('reg_officer_code', $loan->reg_officer_code, array('class' => 'form-control')) }}
                </div>
                <div class="col-4">
                    <select name="reg_year_code" id="reg_year_code" class="form-control">
                        @foreach ($years as $year)
                            <option @if($year == $loan->reg_year_code) selected @endif value="{{$year}}">{{$year}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-4">
                    {{ Form::text('reg_loan_code', $loan->reg_loan_code , array('class' => 'form-control')) }}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('time_range', __('Installment Collection Time Range')) }}
        <div class="input-group colorpickerinput">
            <select name="time_range" id="time_range" class="form-control" required>
                <option value="">Select Time Range</option>
                <option @if ($loan->time_range == 3) selected @endif value="3">Monthly</option>
                <option @if ($loan->time_range == 2) selected @endif value="2">Weekly</option>
                <option @if ($loan->time_range == 1) selected @endif value="1">Daily</option>
            </select>
        </div>
    </div>

    <div class="form-group col-md-6" id="installment_date_div">
        {{ Form::label('installment_date', __('First'), array('id'=>'first')) }} {{ Form::label('installment_date', __('Installment Date')) }}
        <div class="input-group colorpickerinput">
            <select class="form-control" name="installment_date" id="installment_date" required onchange="calOnChange()">
                <option value="">Select Installment Date</option>
                @for ($i = 1; $i <= 31; $i++)
                    <option @if ($loan->installment_date == $i) selected @endif value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>

    {{-- <div class="form-group col-md-6" id="loan_package_div">
        {{ Form::label('loan_package', __('Loan Package')) }}
        {{ Form::select('loan_package', $packages, $loan->loan_package_id, array('class' => 'form-control')) }}
    </div> --}}

    <div class="form-group col-md-6">
        {{ Form::label('loan_amount', __('Loan amount')) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('loan_amount', $loan->loan_amount, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('interest_rate', __('Interest rate')) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('interest_rate', $loan->interest_rate, array('class' => 'form-control','required'=>'required','step'=>'0.01', 'onchange'=>'calOnChange()')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{"%"}}
                </div>
            </div>
        </div>
    </div>


    <div class="form-group col-md-6" id="time_period_div">
        {{ Form::label('time_period', __('Time Period')) }}
        <div class="input-group colorpickerinput">
            {{ Form::select('time_period', $timeperiods, $loan->time_period, array('class' => 'form-control')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{__('Month')}}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6" id="weekly_time_period_div">
        {{ Form::label('weekly_time_period', __('Time Period')) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('weekly_time_period',  $loan->time_period, array('class' => 'form-control', 'placeholder'=>'-- Weeks --')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{__('Weeks')}}
                </div>
            </div>
        </div>
    </div>


    <div class="form-group col-md-6" id="daily_time_period_div">
        {{ Form::label('daily_time_period', __('Time Period')) }}
        <div class="input-group colorpickerinput">
            {{ Form::number('daily_time_period', $loan->daily_time_period, array('class' => 'form-control', 'placeholder'=>'-- Days --')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{__('Days')}}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('interest_amount', __('Interest')) }}
        <div class="input-group colorpickerinput">
            {{ Form::text('interest_amount', $loan->interest_amount, array('class' => 'form-control','required'=>'required','readonly'=>'readonly')) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    {{\Auth::user()->currencySymbol()}}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('total_amount', __('Total amount')) }}
        <div class="input-group colorpickerinput">
            {{ Form::text('total_amount', $loan->total_amount, array('class' => 'form-control','required'=>'required','readonly'=>'readonly')) }}
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
            {{ Form::text('monthly_installment', $loan->monthly_installment, array('class' => 'form-control','readonly'=>'readonly')) }}
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
            {{ Form::text('weekly_installment', $loan->weekly_installment, array('class' => 'form-control','readonly'=>'readonly')) }}
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
            {{ Form::text('daily_installment', $loan->daily_installment, array('class' => 'form-control','readonly'=>'readonly')) }}
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
            {{ Form::text('due_date',$loan->due_date, array('class' => 'form-control','readonly'=>'readonly','required'=>'required')) }}
        </div>
    </div>
</div>
<div class="row">
    <input type="hidden" name="document_charges_in_words" id="document_charges_in_words" value="{{ $loan->document_charges_in_words }}">
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
                    {{ Form::text('document_charges', $loan->document_charges, array('class' => 'form-control','required'=>'required')) }}
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
    <div class="form-group col-md-3">
        {{-- <a href="{{ $loan->application_document }}" target="_blank" class="btn btn-primary"> View Application</a> --}}
        <button type="button" id="application_view" class="btn btn-primary" value="{{ $loan->application_document }}"> View Application</button>
    </div>
    <div class="form-group col-md-3">
        {{-- <a href="{{ $loan->other_document }}" target="_blank" class="btn btn-primary"> View Document</a> --}}
        <button type="button" id="document_view" class="btn btn-primary" value="{{ $loan->other_document }}"> View Document 1</button>
    </div>

    <div class="form-group col-md-3">
        {{-- <a href="{{ $loan->other_document }}" target="_blank" class="btn btn-primary"> View Document</a> --}}
        <button type="button" id="document_view2" class="btn btn-primary" value="{{ $loan->other_document2 }}"> View Document 2</button>
    </div>
    <div class="form-group col-md-3">
        {{-- <a href="{{ $loan->other_document }}" target="_blank" class="btn btn-primary"> View Document</a> --}}
        <button type="button" id="document_view3" class="btn btn-primary" value="{{ $loan->other_document3 }}"> View Document 3</button>
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}

<script>
    var loanData = <?php echo json_encode($loan); ?>;
    $(document).ready(function(){
        if(loanData.time_range == 1){
            $('#loan_amount').prop('disabled', false);

            $('#monthly_installment_div').hide(200);
            $('#weekly_installment_div').hide(200);

            $('#time_period_div').hide(200);
            $('#weekly_time_period_div').hide(200);
            $('#daily_time_period_div').show(200);

            $('#first').show(200);
            $('#installment_date_div').show(200);
            $('#daily_installment_div').show(200);
            // $('#monthly_installment_div').hide();
            // $('#weekly_installment_div').hide();
            // $('#daily_installment_div').show();
            // $('#first').show();

            // $('#time_period_div').hide();
            // $('#loan_package_div').show();
            // $('#daily_time_period_div').show();
        } else if(loanData.time_range == 2){
            $('#loan_amount').prop('disabled', false);

            $('#daily_installment_div').hide(200);
            $('#monthly_installment_div').hide(200);

            $('#time_period_div').hide(200);
            $('#daily_time_period_div').hide(200);
            $('#weekly_time_period_div').show(200);

            $('#weekly_installment_div').show(200);
            $('#first').show(200);
            $('#installment_date_div').show(200);
        } else if(loanData.time_range == 3){
            $('#loan_amount').prop('disabled', false);

            $('#daily_installment_div').hide(200);
            $('#weekly_installment_div').hide(200);
            $('#first').hide(200);

            $('#daily_time_period_div').hide(200);
            $('#time_period_div').show(200);
            $('#weekly_time_period_div').hide(200);

            $('#monthly_installment_div').show(200);
            $('#installment_date_div').show(200);
        }

        if(loanData.doc_type == 1){
            $('#fixed').prop('checked', true);
        } else if(loanData.doc_type == 2){
            $('#flexible').prop('checked', true);
        }
        // $('#interest_amount').prop('disabled', true);
        // $('#total_amount').prop('disabled', true);
        // $('#monthly_installment').prop('disabled', true);
        // $('#installment_date').prop('disabled', true);
        // $('#document_charges').prop('disabled', true);
        $('#application_view').on('click', function(){
            var url = $(this).val();
            if(url == ''){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Application Not Uploaded!'
                });
            } else if(url != ''){
                window.open('../../'+url);
            }
        });

        $('#document_view').on('click',function(){
            var url = $(this).val();
            if(url == ''){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Document Not Uploaded!'
                });
            } else if(url != ''){
                window.open('../../'+url);
            }
        });

        $('#document_view2').on('click',function(){
            var url = $(this).val();
            if(url == ''){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Document Not Uploaded!'
                });
            } else if(url != ''){
                window.open('../../'+url);
            }
        });
        $('#document_view3').on('click',function(){
            var url = $(this).val();
            if(url == ''){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Document Not Uploaded!'
                });
            } else if(url != ''){
                window.open('../../'+url);
            }
        });


        // -----------------------------------------------------
        $('#loan_amount').on('input', function () {
            if($(this).val() != '' && $('#interest_rate').val() != '' && ( $('#time_period').val() != '' || $('#daily_time_period').val() != '' || $('#weekly_time_period').val() != '')){
                finalCalculation();

                if( $('#fixed').prop('checked') == true ){
                    docCharge(1);
                }
                if( $('#flexible').prop('checked')== true ){
                    docCharge(2);
                }
            }

        });
    });

    // $('body').on('input', '#loan_amount', function(){

    // });

    $('body').on('change', '#officer_id', function(){
        var year = $(this).val();
        $.ajax({
            url:`/user/details/`+year,
            method:'GET',
            dataType:'json',
                success: function (data, textStatus, jqXHR){

                    $('input[name="reg_officer_code"]').val(data.reference_code ?? '');
                }
            });

    });

    $('body').on('change', '#time_range', function(){
        if($(this).val() == 1){
            $('#loan_amount').prop('disabled', false);

            $('#monthly_installment_div').hide(200);
            $('#weekly_installment_div').hide(200);

            $('#time_period_div').hide(200);
            $('#weekly_time_period_div').hide(200);
            $('#daily_time_period_div').show(200);

            $('#first').show(200);
            $('#installment_date_div').show(200);
            $('#daily_installment_div').show(200);
        } else if($(this).val() == 2){
            $('#loan_amount').prop('disabled', false);

            $('#daily_installment_div').hide(200);
            $('#monthly_installment_div').hide(200);

            $('#time_period_div').hide(200);
            $('#daily_time_period_div').hide(200);
            $('#weekly_time_period_div').show(200);

            $('#weekly_installment_div').show(200);
            $('#first').show(200);
            $('#installment_date_div').show(200);
        } else if($(this).val() == 3){
            $('#loan_amount').prop('disabled', false);

            $('#daily_installment_div').hide(200);
            $('#weekly_installment_div').hide(200);
            $('#first').hide(200);

            $('#daily_time_period_div').hide(200);
            $('#time_period_div').show(200);
            $('#weekly_time_period_div').hide(200);

            $('#monthly_installment_div').show(200);
            $('#installment_date_div').show(200);
        } else {
            $('#daily_time_period_div').hide(200);
            $('#time_period_div').hide(200);
            $('#loan_package_div').hide(200);
            $('#daily_installment_div').hide(200);
            $('#monthly_installment_div').hide(200);
            $('#weekly_installment_div').hide(200);
            $('#installment_date_div').hide(200);
            $('#first').hide(200);
        }

        if($('#loan_amount').val() != '' && $('#interest_rate').val() != '' && ( $('#time_range').val() != '' || $('#daily_time_period').val() != '' || $('#weekly_time_period').val() != ''  ) && $('#installment_date').val() != ''){
            finalCalculation();
        }
    });

    $('body').on('change', '#loan_package', function(){
        if($('#installment_date').val() == ''){
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Select Installment Date!'
                });
                $(this).val('');
            return this;
        }
        var packageId = $(this).val();
        if(packageId != ''){
            $.ajax({
                url:"{{ url('loan_package/get_detail') }}/"+packageId,
                method: 'GET',
                dataType: 'json',
                success: function(data, textStatus, jqXHR){
                    $('#loan_amount').prop('disabled', false);
                    $('#loan_amount').val(data.loan_amount);
                    $('#interest_rate').val(data.interest_rate);
                    $('#daily_time_period').val(data.time_period);

                    // $('#interest_amount').prop('disabled', false);
                    // $('#total_amount').prop('disabled', false);
                    // $('#daily_installment').prop('disabled', false);

                    finalCalculation();
                }
            });
        } else {
            // $('#interest_amount').prop('disabled', true);
            // $('#total_amount').prop('disabled', true);
            // $('#daily_installment').prop('disabled', true);
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

                finalCalculation();

                if( $('#fixed').prop('checked') == true ){
                    docCharge(1);
                }
                if( $('#flexible').prop('checked')== true ){
                    docCharge(2);
                }
            // } else {
            //     Swal.fire({
            //         icon: 'error',
            //         title: 'Oops...',
            //         text: 'Please Enter Loan Amount and Interest rate!'
            //     });
            //     $('#time_period').val('');

            // }
        // } else {
        //     // $('#interest_amount').prop('disabled', true);
        //     // $('#total_amount').prop('disabled', true);
        //     // $('#monthly_installment').prop('disabled', true);
        //     // $('#installment_date').prop('disabled', true);
        }
    });
    $('body').on('change', '#weekly_time_period', function(){
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

                finalCalculation();

                if( $('#fixed').prop('checked') == true ){
                    docCharge(1);
                }
                if( $('#flexible').prop('checked')== true ){
                    docCharge(2);
                }
            // } else {
            //     Swal.fire({
            //         icon: 'error',
            //         title: 'Oops...',
            //         text: 'Please Enter Loan Amount and Interest rate!'
            //     });
            //     $('#time_period').val('');

            // }
        // } else {
        //     // $('#interest_amount').prop('disabled', true);
        //     // $('#total_amount').prop('disabled', true);
        //     // $('#monthly_installment').prop('disabled', true);
        //     // $('#installment_date').prop('disabled', true);
        }
    });

    $('body').on('change', '#daily_time_period', function(){
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

                finalCalculation();

                if( $('#fixed').prop('checked') == true ){
                    docCharge(1);
                }
                if( $('#flexible').prop('checked')== true ){
                    docCharge(2);
                }
            // } else {
            //     Swal.fire({
            //         icon: 'error',
            //         title: 'Oops...',
            //         text: 'Please Enter Loan Amount and Interest rate!'
            //     });
            //     $('#time_period').val('');

            // }
        // } else {
        //     // $('#interest_amount').prop('disabled', true);
        //     // $('#total_amount').prop('disabled', true);
        //     // $('#monthly_installment').prop('disabled', true);
        //     // $('#installment_date').prop('disabled', true);
        }
    });

    function calOnChange(){
        console.log('cal');
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

        finalCalculation();
    }


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
        var timeRange = $('#time_range').val();

        var interestRate = parseFloat($('#interest_rate').val())/100;
        var loanAmout = $('#loan_amount').val();
        // var timePeriod = $('#time_period').val();
        var date = $('#date').val();
        if(timeRange == 1){
            var timePeriod = $('#daily_time_period').val();
            var interestAmount = parseFloat(loanAmout)  * ( interestRate / 30) * parseFloat(timePeriod);
        }
        else if(timeRange == 2){
            var timePeriod = $('#weekly_time_period').val();
            var interestAmount = parseFloat(loanAmout)  * ( interestRate / 4) * parseFloat(timePeriod);
        }
        else {
            var timePeriod = $('#time_period').val();
            var interestAmount = parseFloat(loanAmout)  * interestRate  * parseFloat(timePeriod);
        }

        $('#interest_amount').val(interestAmount.toFixed(2));

        var totDue = parseFloat(loanAmout) + interestAmount;
        $('#total_amount').val(totDue.toFixed(2));

        var monthlyInstallment = totDue/parseFloat(timePeriod);
        $('#monthly_installment').val(monthlyInstallment.toFixed(2));

        var weeklyInstallment = totDue/(parseFloat(timePeriod)*4);
        $('#weekly_installment').val(weeklyInstallment.toFixed(2));

        // var dailyInstallment = totDue/(parseFloat(timePeriod)*30);
        var dailyInstallment = totDue/(parseFloat(timePeriod));
        $('#daily_installment').val(dailyInstallment.toFixed(2));

        var currentDate = $('#date').val();
        if(timeRange == 3 || timeRange == 2|| timeRange == 1){
            $.ajax({
                type: "GET",
                url: "{{route('sme_loan.cal-due-date')}}",
                data: {
                    currentDate: currentDate,
                    timePeriod: timePeriod,
                    timeRange: timeRange,
                    installmentDate : $('#installment_date').val()
                },
                success: function (response) {
                    $('#due_date').val(response);
                }
            });
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
