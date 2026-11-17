{{ Form::open(array('url' => 'fine_control/update_center', 'method'=>'POST')) }}
<div class="row">
    <input type="hidden" name="fine_center_id" value="{{ $fineCenterCommand->id }}">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('center',__('Center')) }}
            {!! Form::select('center', $centers, $fineCenterCommand->center_id, ['class' => 'form-control customer-sel font-style', 'disabled'=>'disabled','required'=>'required', 'placeholder' => '- Select Center-'])!!}
            {{-- <select name="center" id="center" class="form-control" disabled>
                <option value="">- Select Center-</option>
                @foreach ($centers as $center)
                    <option value="{{ $center->id }}" @if($fineCenterCommand->center_id == $center->id) selected @endif>{{ $center->name }}</option>
                @endforeach
            </select> --}}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
            {{Form::label('start_date_center',__('Start Date')) }}
            {!! Form::text('start_date_center', $fineCenterCommand->start_date, ['class' => 'form-control datepicker', 'required'=>'required'])!!}
        </div>
    </div>
    <div class="form-group col-md-6">
        <div class="input-group colorpickerinput" style="padding-top: 1.5rem; padding-left: 2rem;">
            <label class="switch">
                <input type="checkbox" id="fine_status_center" name="fine_status_center" @if($fineCenterCommand->fine_status == 1) checked @endif>
                <span class="slider round"></span>
            </label>
            <label style="padding-left: 1.5rem; padding-top: 3px; font-weight: bold;" for="fine_status_center">{{__('Applying Penalty')}}</label>
        </div>
    </div>
</div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Change'),array('class'=>'btn btn-primary'))}}
    </div>

{{ Form::close() }}
