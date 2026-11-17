{{Form::open(array('url'=>'cheque_book/store','method'=>'post'))}}

<div class="row">

    <div class="form-group col-md-6">
        {{ Form::label('cheque_bank', __('Bank')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            {!! Form::select('cheque_bank', $chequeBanks, null, ['class' => 'form-control customer-sel font-style', 'required'=>'required', 'placeholder' => '- Select Bank -'])!!}
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('start_from', __('Start From')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-sort-numeric-down"></i>
                </div>
            </div>
            {{ Form::number('start_from', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('cheques_amount', __('Cheques Amount')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-list-ol"></i>
                </div>
            </div>
            {{ Form::number('cheques_amount', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('end_at', __('End At')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-sort-numeric-up"></i>
                </div>
            </div>
            {{ Form::number('end_at', '', array('class' => 'form-control','required'=>'required' , 'disabled'=>'disabled')) }}
        </div>
    </div>
</div>

<div class="col-md-12 text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
    {{-- {{Form::submit(__('Create & Print'),array('class'=>'btn btn-primary'))}} --}}
    {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
</div>

</form>


<script>
    $(document).ready(function () {
        $('#start_from').on('input', function(){
            var startVal = $(this).val();
            var count = $('#cheques_amount').val();
            var endVal = '';
            if(count != ''){
                endVal = parseFloat(startVal) + parseFloat(count) - 1;
            }
            $('#end_at').val(endVal);
        });

        $('#cheques_amount').on('input', function(){
            var count = $(this).val();
            var startVal = $('#start_from').val();
            var endVal = '';
            if(count != ''){
                endVal = parseFloat(startVal) + parseFloat(count) - 1;
            }
            $('#end_at').val(endVal);
        });
    });

    $('body').on('submit', function(){
        $('#end_at').prop('disabled', false);
    });

    //Enter Only Numbers
    // $("#amount").keypress(function (e) {
    //     //if the letter is not digit then display error and don't type anything
    //     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
    //         //display error message
    //         return false;
    //     }
    // });

    // var words="";
    // $(function() {
    //     $("#amount").on("keydown keyup", convert);

    //     function convert() {
    //         var amount = Number($("#amount").val());
    //         words = toWords(amount);
    //         $("#amount_in_word").val(words + "Only");
    //     }
    // });
</script>

