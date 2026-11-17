{{-- <form method="post" action="{{ route('warehousesUpdate', $warehouse->id ) }}">
<div class="row">
    <div class="form-group  col-md-6">
        
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-address-card"></i>
                </div>
            </div>
            <label for="name">Warehouse Name:</label>
            <input type="text" class="form-control" name="name"  value="{{$warehouse->name}}" value="{{ $warehouse->name }}"  required>
        </div>
    </div>
    
    <div class="form-group  col-md-12">
        {{ Form::label('contact', __('Contact Number')) }}
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-mobile-alt"></i>
                </div>
            </div>
            <input type="text" class="form-control" name="contact" required>
        </div>
    </div>
    <div class="form-group  col-md-12">
        {{ Form::label('address', __('Address')) }}
        <input type="text" class="form-control" name="address" required>
    </div>
    @if(!$customFields->isEmpty())
        <div class="col-md-12">
            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                @include('customFields.formBuilder')
            </div>
        </div>
    @endif
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        
    </div>
</div>
</form> --}}



