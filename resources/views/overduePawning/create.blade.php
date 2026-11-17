{{Form::open(array('url'=>'pawning','method'=>'post'))}}

    <div class="row">
        <div class="form-group col-md-4">
            {{ Form::label('issue_date', __('Date of Pawning')) }}
            <div class="input-group">
                {{ Form::text('issue_date', '', array('class' => 'form-control datepicker','required'=>'required', 'data-empty-message'=>"Please Select Date of Pawning", 'data-focus'=>"issue_date")) }}
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('customer', __('Customer')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style" name="customer" id="customer" required
                data-empty-message="Please Select Customer" data-focus="customer">
                    <option value="">Select Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('time_period_id', __('Time Period')) }}
            <div class="input-group">
                <select class="form-control customer-sel font-style" name="time_period" id="time_period" required
                data-empty-message="Please Select Time Period" data-focus="customer">
                    <option value="">Select Time Period</option>
                    @foreach ($timePeriods as $timeperiod)
                        <option value="{{ $timeperiod->id }}">{{ $timeperiod->time_period . ' Months' }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('due_date', __('Due Date')) }}
            <div class="input-group">
                {{ Form::text('due_date', '', array('class' => 'form-control datepicker','required'=>'required', 'readonly')) }}
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="form-group col-md-6">
            {{ Form::label('due_date', __('Due Date')) }}
            <div class="input-group colorpickerinput">
                {{ Form::text('due_date','', array('class' => 'form-control','readonly'=>'readonly')) }}
            </div>
        </div> --}}
        <div class="form-group col-md-4">
            {{ Form::label('installment_date', __('Installment Date')) }}
            <div class="input-group colorpickerinput">
                <select class="form-control" name="installment_date" id="installment_date">
                    <option value="">Select Installment Date</option>
                    @for ($i = 1; $i < 31; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>


    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        <button type="button" class="btn btn-primary" id="create_btn">{{__('Create')}}</button>
    </div>
{{-- </form> --}}

<script>
    $(document).ready(function(){
        $('#due_date').prop('disabled', true);
    });

    $('body').on('change', '#time_period', function(){

        var duedate = $(this).val();
        var date = $('#due_date').val();
        var seperateddate = date.split('-');
        var newDate = ''+seperateddate[0]+'-'+duedate+'-'+duedate+'';

        function convert(str) {
            var date = new Date(str),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);
            return [date.getFullYear(), mnth, day].join("-");
        }

        var convertedDueDate = convert(duedate);

        $('#due_date').val(convertedDueDate);

    });

    $('body').on('change', '#installment_date', function(){
        var day = $(this).val();
        var date = $('#due_date').val();
        var seperateddate = date.split('-');
        var newDate = ''+seperateddate[0]+'-'+seperateddate[1]+'-'+day+'';

        function convert(str) {
            var date = new Date(str),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);
            return [date.getFullYear(), mnth, day].join("-");
        }

        var convertedNewDate = convert(newDate);

        $('#due_date').val(convertedNewDate);
    });
</script>

{{ Form::close() }}
