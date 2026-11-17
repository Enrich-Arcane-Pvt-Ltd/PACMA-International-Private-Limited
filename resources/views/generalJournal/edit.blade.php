{{Form::open(array('url'=>'general-journal/update','method'=>'post'))}}

<div class="row">
    <input type="hidden" name="journal_id" id="journal_id" value="{{ $journal->id }}">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('date',__('Date')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
                {{Form::text('date',$journal->date,array('class'=>'form-control datepicker','required'=>'required'))}}
            </div>
        </div>
    </div>

    <div class="col-md-12">
        {{-- <div class="card repeater">
            <div class="card-header">
                <div class="card-header-action">
                    <a href="#" data-repeater-create="" class="btn btn-icon icon-left btn-primary" id="add_row" data-toggle="modal" data-target="#add-bank">
                        <i class="fas fa-plus"></i>{{__('Add Entry')}}
                    </a>
                </div>
            </div>
        </div> --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="col-sm-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{__('Account Name')}}</th>
                                <th>{{__('Status')}} </th>
                                <th>{{__('Related Account Name')}}</th>
                                <th>{{__('Status')}} </th>
                                <th>{{__('Narration')}} </th>
                                <th>{{__('Amount')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select class="form-control customer-sel font-style account_id" name="account_id" id="account_id" required>
                                        <option value="">-- Select Account Name --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" @if ($journal->account_id == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control customer-sel font-style debit_credit" name="debit_credit" id="debit_credit" required>
                                        <option value="">--Select Status--</option>
                                        <option @if ($journal->debit_credit == 0) selected @endif value="0">Credit</option>
                                        <option @if ($journal->debit_credit == 1) selected @endif value="1">Debit</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control customer-sel font-style related_account_id" name="related_account_id" id="related_account_id" required>
                                        <option value="">-- Select Account Name --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" @if ($journal->related_account_id == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control customer-sel font-style related_debit_credit" name="related_debit_credit" id="related_debit_credit" required>
                                        <option value="">--Select Status--</option>
                                        <option @if ($journal->related_debit_credit == 0) selected @endif value="0">Credit</option>
                                        <option @if ($journal->related_debit_credit == 1) selected @endif value="1">Debit</option>
                                    </select>
                                </td>
                                <td>
                                    {{Form::text('narration', $journal->narration ?? '',array('class'=>'form-control narration'))}}
                                </td>
                                <td>
                                    {{ Form::number('amount', $journal->amount ?? '', array('class' => 'form-control amount','required'=>'required','step'=>'0.01', 'data-empty-message'=>"Please Enter Amount", 'data-focus'=>"amount")) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>
</div>

{{Form::close()}}

<script src="{{asset('assets/js/jquery.repeater.min.js')}}"></script>

<script>
    // var selector = "body";
    //     if ($(selector + " .repeater").length) {
    //         var $dragAndDrop = $("body .repeater tbody").sortable({
    //             handle: '.sort-handler'
    //         });
    //         var $repeater = $(selector + ' .repeater').repeater({
    //             initEmpty: false,
    //             defaultValues: {
    //                 // 'status': 1
    //             },
    //             show: function () {
    //                 $(this).slideDown();
    //                 var file_uploads = $(this).find('input.multi');
    //                 if (file_uploads.length) {
    //                     $(this).find('input.multi').MultiFile({
    //                         max: 3,
    //                         accept: 'png|jpg|jpeg',
    //                         max_size: 2048
    //                     });
    //                 }
    //             },
    //             hide: function (deleteElement) {
    //                 if (confirm('Are you sure you want to delete this element?')) {
    //                     $(this).slideUp(deleteElement);
    //                     $(this).remove();

    //                     // var inputs = $(".amount");
    //                     // var subTotal = 0;
    //                     // for (var i = 0; i < inputs.length; i++) {
    //                     //     subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
    //                     // }
    //                     // $('.subTotal').html(subTotal.toFixed(2));
    //                     // $('.totalAmount').html(subTotal.toFixed(2));
    //                     // finalCalculation();

    //                 }
    //             },
    //             ready: function (setIndexes) {
    //                 $dragAndDrop.on('drop', setIndexes);
    //             },
    //             isFirstItemUndeletable: true
    //         });
    //         var value = $(selector + " .repeater").attr('data-value');
    //         if (typeof value != 'undefined' && value.length != 0) {
    //             value = JSON.parse(value);
    //             $repeater.setList(value);
    //         }

    //     }

    $('body').on('change', '.debit_credit', function(){
        var statusVlue = $(this).val();
        var relatedStatus = $(this).closest('tr').find('.related_debit_credit');
        if(statusVlue == 0){
            relatedStatus.val(1);
        } else if(statusVlue == 1){
            relatedStatus.val(0);
        } else {
            relatedStatus.val('');
        }
    });

    $('body').on('change', '.related_debit_credit', function(){
        var relatedStatus = $(this).val();
        var statusVlue = $(this).closest('tr').find('.debit_credit');
        if(relatedStatus == 0){
            statusVlue.val(1);
        } else if(relatedStatus == 1){
            statusVlue.val(0);
        } else {
            statusVlue.val('');
        }
    });

//     $(document).ready(function(){
//       var i=1;
//      $(document).on("click", "#add_row", function(){
//         var datepic = "#date" + i;
//         $('#addr'+i).html("<td>"+ (i+1) +"</td><td><input id = 'date"+
//           i+"'  name='name" + i +
//           "' type='text' placeholder='Name' class='form-control input-md'/>"
//           + "</td><td><input  name='mail" +
//           i + "' type='text' placeholder='Mail'  class='form-control input-md'>"
//           +"</td><td><select class='input category'><option selected>d</option></select>"
//           +"</td>");

//         $(datepic).datepicker();

//          $('#tab_logic').append('<tr id="addr'+(i+1)+'"></tr>');
//             i++;
//      });
</script>





    {{-- <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('debit',__('Debit')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-money-bill-alt"></i>
                    </div>
                </div>
            {{Form::number('debit','',array('class'=>'form-control','step'=>'0.01'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('account_id',__('Account Name')) }}
            {{Form::select('account_id',$accounts, null,array('class'=>'form-control customer-sel font-style','required'=>'required'))}}
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('credit',__('Credit')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-money-bill-alt"></i>
                    </div>
                </div>
            {{Form::number('credit','',array('class'=>'form-control','step'=>'0.01'))}}
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('narration',__('Narration')) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                {{Form::text('narration',null,array('class'=>'form-control','required'=>'required'))}}
            </div>
        </div>
    </div>
</div> --}}

