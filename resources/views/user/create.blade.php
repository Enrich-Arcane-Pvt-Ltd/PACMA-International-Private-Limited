{{Form::open(array('url'=>'users','method'=>'post'))}}
<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter User Name'),'required'=>'required'))}}
            @error('name')
            <span class="invalid-name" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{Form::label('email',__('Email'))}}
            {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter User Email'),'required'=>'required'))}}
            @error('email')
            <span class="invalid-email" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{Form::label('reference_code',__('Reference Code'))}}
            {{Form::text('reference_code',null,array('class'=>'form-control','placeholder'=>__('Enter User Reference Code'), 'maxlength'=>"6"))}}
            @error('reference_code')
                <span class="invalid-reference_code" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{Form::label('password',__('Password'))}}
            {{Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter User Password'),'required'=>'required','minlength'=>"6"))}}
            @error('password')
            <span class="invalid-password" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    @if(\Auth::user()->type != 'super admin')
        <div class="form-group col-md-6">
            {{ Form::label('role', __('User Role')) }}
            {!! Form::select('role', $roles, null,array('class' => 'form-control font-style selectric','required'=>'required')) !!}
            @error('role')
            <span class="invalid-role" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
            @enderror
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('branch_id', __('User Branch')) }}
            {{-- {!! Form::select('branch', $branches, null,array('class' => 'form-control font-style selectric','required'=>'required')) !!} --}}
            <select name="branch_id" id="branch_id" class="form-control font-style selectric">
                <option value="">-- --</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            @error('branch')
            <span class="invalid-branch" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
            @enderror
        </div>
    @endif
    @if(!$customFields->isEmpty())
        <div class="col-md-6">
            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                @include('customFields.formBuilder')
            </div>
        </div>
    @endif
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>
</div>

{{Form::close()}}

{{-- <script>
        $('#branch').hide();
    $(document).ready(function(){
        $('body').on('change', '#role', function(){
            var roleId = $(this).val()
            console.log(roleId);
                if(roleId == 1 || roleId == 2 || roleId == 3 || roleId == 4 || roleId == 6){
                    $('#branch').val('');
                    // $('#branch').prop('disabled', true);
        $('#branch').hide();
                } else {
                    // $('#branch').prop('disabled', false);
        $('#branch').shwo();
                }
        });
    });
</script> --}}

