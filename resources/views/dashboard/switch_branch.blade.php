{{ Form::open(array('route' => array('user.switch_branch.change'),'mothod'=>'post')) }}
<style>
    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 25px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      -webkit-transition: .4s;
      transition: .4s;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 17px;
      width: 17px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
    }

    input:checked + .slider {
      background-color: #2196F3;
    }

    input:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
      border-radius: 34px;
    }

    .slider.round:before {
      border-radius: 50%;
    }
</style>
<div class="row">
    {{-- <div class="form-group  col-md-6">
        {{ Form::label('date', __('Date')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::text('date','', array('class' => 'form-control datepicker')) }}
        </div>
    </div>
    <div class="form-group  col-md-6">
        {{ Form::label('amount', __('Amount')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            {{ Form::number('amount', !empty($billDue)?$billDue->getDue():0, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
        </div>
    </div> --}}
    <div class="form-group col-md-12">
        {{-- {{ Form::label('description', __('Description')) }}
        {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2']) !!} --}}
        {{-- <table>
            @foreach ($branches as $branch)
                <tr>
                    <td>
                        <input type="radio" class="form-control" name="branch_id" id="{{ $branch->id }}" value="{{ $branch->id }}">
                    </td>
                    <td>
                        <label for="{{ $branch->id }}">{{ $branch->name }}</label>
                    </td>
                </tr>
            @endforeach
        </table> --}}
        <ul>
            @foreach ($branches as $branch)
                <li class="input-group colorpickerinput">
                    <label class="switch">
                        <input type="radio" name="branch_id" class="branch_id" id="branch_{{ $branch->id }}" value="{{ $branch->id }}" @if(Auth::user()->branch_id == $branch->id) checked @endif>
                        <span class="slider round"></span>
                    </label>
                    <label style="padding-left: 1.5rem; padding-top: 3px;" for="branch_{{ $branch->id }}"><h6>{{ $branch->name }}</h6></label>

                    {{-- <input type="radio" name="branch_id" id="{{ $branch->id }}" value="{{ $branch->id }}">
                    <label for="{{ $branch->id }}">{{ $branch->name }}</label> --}}
                </li>
            @endforeach
        </ul>
    </div>
    <div class="col-md-12 text-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Switch'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}

{{-- <script>
    $('body').on('click', 'branch_id', function(){
        console.log($(this).val());
    });
</script> --}}
