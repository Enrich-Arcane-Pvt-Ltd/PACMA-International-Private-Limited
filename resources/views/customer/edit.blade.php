<style>
    .modal-dialog.modal-xl {
        width: 800px !important;
        max-width: 800px !important;
    }
</style>

{{ Form::model($customer, array('route' => array('customer.update', $customer->id), 'method' => 'PUT', 'enctype' => 'multipart/form-data')) }}
    <h4 class="sub-title">{{__('Personal Info')}}</h4>



    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="d-block">{{__('Type')}}</label>
                <div class="row">
                    <div class="col-md-4">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="0" name="type" value="0" {{ ($customer->type == "0")? "checked" : "" }}>
                            <label class="custom-control-label" for="0">{{__('Customer')}}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="1" name="type" value="1" {{ ($customer->type == "1")? "checked" : "" }}>
                            <label class="custom-control-label" for="1">{{__('Guarantor')}}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="2" name="type" value="2" {{ ($customer->type == "2")? "checked" : "" }}>
                            <label class="custom-control-label" for="2">{{__('Both')}}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3">
            <div class="form-group">
                {{ Form::label('title', __('Title')) }}<span class="text-danger">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-user-tag"></i>
                        </div>
                    </div>
                    <select name="title" class="form-control customer-sel font-style" >
                        <option disabled>- Select -</option>
                        <option value="0" {{$customer->title == 0  ? 'selected' : ''}}>Mr.</option>
                        <option value="1" {{$customer->title == 1  ? 'selected' : ''}}>Mrs.</option>
                        <option value="2" {{$customer->title == 2  ? 'selected' : ''}}>Miss.</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-9 col-sm-9">
            <div class="form-group">
                {{ Form::label('name', __('Preferred Name')) }}<span class="text-danger">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    {{ Form::text('name', null, array('class' => 'form-control', 'required' => 'required')) }}
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{ Form::label('full_name', __('Full Name')) }} <span class="text-danger">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    {{ Form::text('full_name', null, array('class' => 'form-control', 'required' => 'required', 'id' => 'name', 'placeholder' => 'Please enter your full name without single initials', 'pattern' => '(?!\b\w\b)([A-Za-z]+ ?)+')) }}
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3">
            <div class="form-group">
                {{ Form::label('marital_status', __('Marital Status')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-user-tag"></i>
                        </div>
                    </div>
                    {!! Form::select('marital_status',$marrage,  $customer->details->marital_status ?? null, ['class' => 'form-control customer-sel font-style', 'placeholder' => '- Select -'])!!}
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-9 col-sm-9">
            <div class="form-group">
                {{ Form::label('nic_no', __('NIC No')) }}<span class="text-danger">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-id-card"></i>
                        </div>
                    </div>
                    {{ Form::text('nic_no', null, array('class' => 'form-control', 'required' => 'required')) }}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('dob', __('Date of Birth')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </div>
                    {{ Form::date('dob', null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('phone', __('Phone (Home)')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                    </div>
                    {{ Form::text('phone', null, array('class' => 'form-control', 'placeholder'=>'07xxxxxxxx', 'maxlength' => '11', 'pattern' => '^(0(?!94)\d{9})$')) }}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('phone_mobile', __('Phone (Mobile)')) }}<span class="text-danger">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                    </div>
                    {{ Form::text('phone_mobile', null, array('class' => 'form-control', 'placeholder'=>'07xxxxxxxx', 'required' => 'required', 'maxlength' => '11', 'pattern' => '^(0(?!94)\d{9})$')) }}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('phone_mobile2', __('Phone (Mobile 2)')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                    </div>
                    {{ Form::text('phone_mobile2',  $customer->details->phone_mobile2 ?? null, array('class' => 'form-control', 'placeholder'=>'07xxxxxxxx', 'maxlength' => '11', 'pattern' => '^(0(?!94)\d{9})$')) }}
                </div>
            </div>
        </div>


        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('email', __('Email')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="far fa-envelope"></i>
                        </div>
                    </div>
                    {{ Form::email('email', null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('house_no', __('House Number')) }}<span class="text-danger">*</span>
                        <label class="form-control-label" for="example2cols1Input"></label>
                        <div class="input-group">
                            {{ Form::text('house_no', null, array('class' => 'form-control', 'required' => 'required')) }}
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="form-group">
                        {{ Form::label('address', __('Address')) }}<span class="text-danger">*</span>
                        <label class="form-control-label" for="example2cols1Input"></label>
                        <div class="input-group">
                            {{ Form::textarea('address', null, array('class' => 'form-control')) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('address_nic', __('Address (as Per NIC)')) }}
                <label class="form-control-label" for="example2cols1Input"></label>
                <div class="input-group">
                    {{ Form::textarea('address_nic', null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>

        @php
            $currentPhoto = !empty($customer->details->customer_photo) ? $customer->details->customer_photo : null;
            $photoUrl = asset('assets/img/avatar/user.png');
            $hasExistingPhoto = false;
            if (!empty($currentPhoto)) {
                if (file_exists(public_path($currentPhoto))) {
                    $photoUrl = asset($currentPhoto);
                    if ($currentPhoto !== 'assets/img/avatar/user.png') {
                        $hasExistingPhoto = true;
                    }
                } elseif (file_exists(public_path('storage/' . $currentPhoto))) {
                    $photoUrl = asset('storage/' . $currentPhoto);
                    $hasExistingPhoto = true;
                } elseif (file_exists(storage_path('app/public/' . $currentPhoto))) {
                    $photoUrl = asset('storage/' . $currentPhoto);
                    $hasExistingPhoto = true;
                } elseif (strpos($currentPhoto, 'uploads/') === 0) {
                    $photoUrl = asset('storage/' . $currentPhoto);
                    $hasExistingPhoto = true;
                } else {
                    $photoUrl = asset(Storage::url($currentPhoto));
                    $hasExistingPhoto = true;
                }
            }
        @endphp

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('customer_photo', __('Photo')) }}
                <div class="input-group">
                    <input type="file" name="customer_photo" id="customer_photo" class="form-control" accept=".png,.jpg,.jpeg" onchange="previewCustomerPhoto(this)">
                </div>
                @if ($hasExistingPhoto)
                    <small class="text-success d-block mt-1">
                        <i class="fas fa-check-circle"></i> {{ __('Current photo saved') }}: <strong>{{ basename($currentPhoto) }}</strong>
                    </small>
                    <small class="text-muted d-block">
                        {{ __('(Leave file empty to keep current photo, or choose a new file to change)') }}
                    </small>
                @else
                    <small class="text-muted d-block mt-1">
                        {{ __('Choose a photo to upload (PNG, JPG).') }}
                    </small>
                @endif
                <small id="selected_photo_name" class="text-primary d-block mt-1" style="display: none;"></small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="form-control-label d-block">{{ __('Photo Preview') }}</label>
                <div class="d-flex align-items-center">
                    <img id="customer_photo_preview" 
                         src="{{ $photoUrl }}" 
                         class="img-thumbnail" 
                         width="100" 
                         height="100" 
                         style="object-fit: cover; border-radius: 6px;" 
                         onerror="this.onerror=null;this.src='{{ asset('assets/img/avatar/user.png') }}'">
                    <span id="preview_badge" class="badge badge-info ml-2" style="display: none;"></span>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="d-block">{{ __('Resident') }}</label>

                <div class="row">
                    <div class="col-md-3">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="own" name="resident" value="0" {{ ($customer->resident == "0")? "checked" : "" }}>
                            <label class="custom-control-label" for="own">{{__('Own')}}</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="parent" name="resident" value="1" {{ ($customer->resident == "1")? "checked" : "" }}>
                            <label class="custom-control-label" for="parent">{{__("Parent's")}}</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="rented" name="resident" value="2" {{ ($customer->resident == "2")? "checked" : "" }}>
                            <label class="custom-control-label" for="rented">{{__('Rented')}}</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="others" name="resident" value="3" {{ ($customer->resident == "3")? "checked" : "" }}>
                            <label class="custom-control-label" for="others">{{__('Others')}}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="lat_coordinats" class="lat_coordinats" name="lat_coordinates" value="{{ $customer->lat_coordinates }}">
        <input type="hidden" id="lng_coordinats" class="lng_coordinats" name="lng_coordinates" value="{{ $customer->lng_coordinates }}">



        <div class="d-none">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('business_duration_on_address', __('Business Duration Related to Address(Years)')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-business-time"></i>
                            </div>
                        </div>
                        {{ Form::number('business_duration_on_address', $customer->details->business_duration_on_address ?? null, array('class' => 'form-control')) }}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    {{ Form::label('business_registration', __('Business Registration')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-city"></i>
                            </div>
                        </div>
                        {!! Form::select('business_registration', $boolSelect, $customer->details->business_registration ?? null , ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    {{ Form::label('business_partners', __('Business Partners')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        {!! Form::select('business_partners', $boolSelect, $customer->details->business_partners ?? null , ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    {{ Form::label('business_employees', __('Any Employees')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        {!! Form::select('business_employees', $boolSelect, $customer->details->business_employees ?? null , ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    {{ Form::label('business_insurance', __('Any Insurance for business')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-address-card"></i>
                            </div>
                        </div>
                        {!! Form::select('business_insurance', $boolSelect, $customer->details->business_insurance ?? null , ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    {{ Form::label('business_bank_accounts', __('Bank Account for business')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-money-check-alt"></i>
                            </div>
                        </div>
                        {!! Form::select('business_bank_accounts', $boolSelect, $customer->details->business_bank_accounts ?? null , ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('business_bank_name', __('Bank Name')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-address-card"></i>
                            </div>
                        </div>
                        {!! Form::text('business_bank_name', $customer->details->business_bank_name ?? null , ['class' => 'form-control customer-sel font-style',  'placeholder' => ''])!!}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    {{ Form::label('business_bank_type', __('Bank Account Type')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-money-check-alt"></i>
                            </div>
                        </div>
                        {!! Form::select('business_bank_type', $bankAccountType, $customer->details->business_bank_type ?? null, ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="form-group">
                    {{ Form::label('business_bank_account_number', __('Bank A/N Of Bussiness')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-address-card"></i>
                            </div>
                        </div>
                        {{ Form::text('business_bank_account_number', $customer->details->business_bank_account_number ?? null, array('class' => 'form-control')) }}
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="form-group">
                    {{ Form::label('business_profit', __('Profit of Business')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        {{ Form::number('business_profit', $customer->details->business_profit ?? null, array('class' => 'form-control', )) }}
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('employment_place', __('Place of Employment / Business')) }}<span class="text-danger">*</span>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        {{ Form::text('employment_place', null, array('class' => 'form-control')) }}
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('income_period', __('Income Period')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-user-tag"></i>
                            </div>
                        </div>
                        {!! Form::select('income_period',$income_period, $customer->details->income_period ?? null, ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('expetation_for_starter_up', __('Expectation for New Business')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-business-time"></i>
                            </div>
                        </div>
                        {!! Form::select('expetation_for_starter_up', $boolSelect, $customer->details->expetation_for_starter_up ?? null, ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('expetation_for_starter_up_details', __('Details for related Business')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-business-time"></i>
                            </div>
                        </div>
                        {!! Form::text('expetation_for_starter_up_details',  $customer->details->expetation_for_starter_up_details ?? null, ['class' => 'form-control customer-sel font-style',  'placeholder' => ''])!!}
                    </div>
                </div>
            </div>


        </div>


        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('business_name', __('Name of Business')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    {{ Form::text('business_name', null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('business_address', __('Business Address')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    {{ Form::text('business_address', $customer->details->business_address ?? null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>


        <div class="col-lg-6 col-md-4 col-sm-4">
            <div class="form-group">
                {{ Form::label('employment_name', __('Name of Employment')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-address-card"></i>
                        </div>
                    </div>
                    {{ Form::text('employment_name', null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-4 col-sm-4">
            <div class="form-group">
                {{ Form::label('employment_number', __('Employment Number')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-address-card"></i>
                        </div>
                    </div>
                    {{ Form::text('employment_number', null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('employment_feild', __('Employment Type')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-business-time"></i>
                        </div>
                    </div>
                    {!! Form::select('employment_feild', $employment_feild, $customer->details->employment_feild ?? null, ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -', 'required' => 'required'])!!}
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('job_title', __('Job Title')) }}<span class="text-danger">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-id-card-alt"></i>
                        </div>
                    </div>
                    {{ Form::text('job_title', null, array('class' => 'form-control', 'required' => 'required')) }}
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{ Form::label('relationship_to_hirer', __('Relationship to Hirer')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-user-friends"></i>
                        </div>
                    </div>
                    {{ Form::text('relationship_to_hirer', null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>

    <div class="col-md-12 mt-5 text-right">
        <a href="#" class="btn btn-info" id="billing_data">{{ __('Billing Same As Personal') }}</a>
    </div>

    <h4 class="sub-title">{{ __('Billing Info') }}</h4>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_title', __('Title')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-user-tag"></i>
                        </div>
                    </div>
                    <select class="form-control customer-sel font-style" name="billing_title">
                        <option disabled>- Select -</option>
                        <option value="0" {{$customer->billing_title == 0  ? 'selected' : ''}}>Mr.</option>
                        <option value="1" {{$customer->billing_title == 1  ? 'selected' : ''}}>Mrs.</option>
                        <option value="2" {{$customer->billing_title == 2  ? 'selected' : ''}}>Miss.</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_name', __('Name')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    {{ Form::text('billing_name', null, array('class' => 'form-control')) }}
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('billing_house_no', __('House Number')) }}
                        <label class="form-control-label" for="example2cols1Input"></label>
                        <div class="input-group">
                            {{ Form::text('billing_house_no', null, array('class' => 'form-control')) }}
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="form-group">
                        {{ Form::label('billing_address', __('Address')) }}
                        <label class="form-control-label" for="example2cols1Input"></label>
                        <div class="input-group">
                            {{ Form::textarea('billing_address', null, array('class' => 'form-control')) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_phone', __('Phone')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                    </div>
                    {{ Form::text('billing_phone', null, array('class' => 'form-control', 'placeholder'=>'07xxxxxxxx', 'maxlength' => '11', 'pattern' => '^(0(?!94)\d{9})$')) }}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_email', __('Email')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="far fa-envelope"></i>
                        </div>
                    </div>
                    {{ Form::text('billing_email', null, array('class'=>'form-control')) }}
                </div>
            </div>
        </div>
    </div>

    <h4 class="sub-title">{{__('Vehicle Details')}}</h4>

    <div class="row">

        <div class="col-lg-2 col-md-2 col-sm-2">
            <div class="form-group">
                {{ Form::label('vahecle_count', __('Vehicle')) }}

                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-car"></i>
                        </div>
                    </div>
                    {!! Form::select('vahecle_count',$boolSelect, $customer->details->vahecle_count ?? null, ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-md-5 col-sm-5">
            <div class="form-group">
                {{ Form::label('vehicle_model', __('Vehicle Model')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-car"></i>
                        </div>
                    </div>
                    {{ Form::text('vehicle_model', $customer->details->vehicle_model ?? null, array('class' => 'form-control', )) }}
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-5">
            <div class="form-group">
                {{ Form::label('vehicle_number', __('Vehicle Number')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-car"></i>
                        </div>
                    </div>
                    {{ Form::text('vehicle_number', $customer->details->vehicle_number ?? null, array('class' => 'form-control', )) }}
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
            <div class="form-group">
                {{ Form::label('vehicle_finance', __('Finance for vehicle')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-car"></i>
                        </div>
                    </div>
                    {!! Form::select('vehicle_finance',$boolSelect, $customer->details->vehicle_finance ?? null, ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
            <div class="form-group">
                {{ Form::label('vehicle_finance_amount', __('Finance Amount')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                    </div>
                    {{ Form::number('vehicle_finance_amount', $customer->details->vehicle_finance_amount ?? null, array('class' => 'form-control', )) }}
                </div>
            </div>
        </div>

    </div>

    <h4 class="sub-title">{{__('Other Details')}}</h4>

    <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{ Form::label('children_names', __('Names of your children')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-child"></i>
                        </div>
                    </div>
                    {{ Form::text('children_names', $customer->details->children_names ?? null, array('class' => 'form-control', )) }}
                </div>
            </div>
        </div>

        <div class="d-none">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    {{ Form::label('insurance', __('Insurance')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-address-card"></i>
                            </div>
                        </div>
                        {!! Form::select('insurance',$boolSelect, $customer->details->insurance ?? null, ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    {{ Form::label('any_other_income', __('Any Other Income')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-money-bill"></i>
                            </div>
                        </div>
                        {!! Form::select('any_other_income',$boolSelect, $customer->details->any_other_income ?? null, ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                    </div>
                </div>
            </div>

            <h4 class="sub-title">{{__('Other Loan Details')}}</h4>

            <div class="row">

                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="form-group">
                        {{ Form::label('other_loan', __('Other loans')) }}

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-money-check"></i>
                                </div>
                            </div>
                            {!! Form::select('other_loan',$boolSelect, $customer->details->other_loan ?? null, ['class' => 'form-control customer-sel font-style',  'placeholder' => '- Select -'])!!}
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="form-group">
                        {{ Form::label('other_loan_installment', __('Loan Installment')) }}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-money-check-alt"></i>
                                </div>
                            </div>
                            {{ Form::number('other_loan_installment', $customer->details->other_loan_installment ?? null, array('class' => 'form-control', )) }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-12 mt-3">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">{{ __('Application Form Additional Details') }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('gender', __('Gender'), ['class' => 'form-label']) }}
                            {{ Form::select('gender', ['' => __('Select Gender'), 1 => __('Male'), 2 => __('Female')], $customer->details->gender ?? null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('group_no', __('Group No.'), ['class' => 'form-label']) }}
                            {{ Form::text('group_no', $customer->details->group_no ?? null, ['class' => 'form-control', 'placeholder' => __('Enter Group No.')]) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('resident_period', __('Resident Period (Years)'), ['class' => 'form-label']) }}
                            {{ Form::text('resident_period', $customer->details->resident_period ?? null, ['class' => 'form-control', 'placeholder' => __('e.g. 5')]) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('preferred_payment_date', __('Preferred Payment Date'), ['class' => 'form-label']) }}
                            {{ Form::text('preferred_payment_date', $customer->details->preferred_payment_date ?? null, ['class' => 'form-control', 'placeholder' => __('e.g. 10th of every month')]) }}
                        </div>
                    </div>

                    @php
                        $bdStr = (string) ($customer->details->business_duration ?? '');
                        $bdYears = 0;
                        $bdMonths = 0;
                        if (preg_match('/(\d+)\s*Year/i', $bdStr, $mY)) {
                            $bdYears = (int) $mY[1];
                        } elseif (is_numeric(trim($bdStr))) {
                            $bdYears = (int) trim($bdStr);
                        }
                        if (preg_match('/(\d+)\s*Month/i', $bdStr, $mM)) {
                            $bdMonths = (int) $mM[1];
                        }
                        $yearOpts = [];
                        for ($y = 0; $y <= 50; $y++) {
                            $yearOpts[$y] = $y . ' ' . ($y === 1 ? 'Year' : 'Years');
                        }
                        $monthOpts = [];
                        for ($m = 0; $m <= 11; $m++) {
                            $monthOpts[$m] = $m . ' ' . ($m === 1 ? 'Month' : 'Months');
                        }
                    @endphp
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('service_duration_years', __('Service Years'), ['class' => 'form-label']) }}
                            {{ Form::select('service_duration_years', $yearOpts, $bdYears, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('service_duration_months', __('Service Months'), ['class' => 'form-label']) }}
                            {{ Form::select('service_duration_months', $monthOpts, $bdMonths, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('phone_office', __('Office Telephone No.'), ['class' => 'form-label']) }}
                            {{ Form::text('phone_office', $customer->phone_office ?? null, ['class' => 'form-control', 'placeholder' => __('e.g. 0112345678')]) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('monthly_income', __('Monthly Income'), ['class' => 'form-label']) }}
                            {{ Form::number('monthly_income', $customer->monthly_income ?? null, ['class' => 'form-control', 'step' => '0.01', 'placeholder' => __('e.g. 75000')]) }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('any_other_income_details', __('Other Income'), ['class' => 'form-label']) }}
                            {{ Form::text('any_other_income_details', $customer->details->any_other_income_details ?? null, ['class' => 'form-control', 'placeholder' => __('Enter Other Income details')]) }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('other_loan_company', __('Other Liabilities Company'), ['class' => 'form-label']) }}
                            {{ Form::text('other_loan_company', $customer->details->other_loan_company ?? null, ['class' => 'form-control', 'placeholder' => __('Enter Bank / Finance Company')]) }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('other_loan_amount', __('Other Liabilities Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('other_loan_amount', $customer->details->other_loan_amount ?? null, ['class' => 'form-control', 'step' => '0.01', 'placeholder' => __('e.g. 25000')]) }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('spouse_name_address', __("Spouse's Name & Address"), ['class' => 'form-label']) }}
                            {{ Form::text('spouse_name_address', $customer->details->spouse_name_address ?? null, ['class' => 'form-control', 'placeholder' => __("Enter Spouse's Name & Address")]) }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('spouse_phone', __("Spouse's Telephone No."), ['class' => 'form-label']) }}
                            {{ Form::text('spouse_phone', $customer->details->spouse_phone ?? null, ['class' => 'form-control', 'placeholder' => __("e.g. 0771234567")]) }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('spouse_occupation', __("Spouse's Occupation"), ['class' => 'form-label']) }}
                            {{ Form::text('spouse_occupation', $customer->details->spouse_occupation ?? null, ['class' => 'form-control', 'placeholder' => __("Enter Spouse's Occupation")]) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('spouse_office_address', __("Spouse's Office Address"), ['class' => 'form-label']) }}
                            {{ Form::text('spouse_office_address', $customer->details->spouse_office_address ?? null, ['class' => 'form-control', 'placeholder' => __("Enter Spouse's Office Address")]) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('spouse_income', __("Spouse's Income"), ['class' => 'form-label']) }}
                            {{ Form::text('spouse_income', $customer->details->spouse_income ?? null, ['class' => 'form-control', 'placeholder' => __("Enter Spouse's Income")]) }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('parents_name', __("Parents' Name"), ['class' => 'form-label']) }}
                            {{ Form::text('parents_name', $customer->details->parents_name ?? null, ['class' => 'form-control', 'placeholder' => __("Enter Parents' Name")]) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('parents_address', __("Parents' Address"), ['class' => 'form-label']) }}
                            {{ Form::text('parents_address', $customer->details->parents_address ?? null, ['class' => 'form-control', 'placeholder' => __("Enter Parents' Address")]) }}
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="form-group">
                            {{ Form::label('relation_details', __("Relation's Name, Address & Phone Number"), ['class' => 'form-label']) }}
                            {{ Form::text('relation_details', $customer->details->relation_details ?? null, ['class' => 'form-control', 'placeholder' => __("Enter Relation's Name, Address & Phone Number")]) }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('relation_type', __('Relation'), ['class' => 'form-label']) }}
                            {{ Form::text('relation_type', $customer->details->relation_type ?? null, ['class' => 'form-control', 'placeholder' => __('e.g. Brother / Sister')]) }}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            {{ Form::label('route_to_residence', __('Route to Residence'), ['class' => 'form-label']) }}
                            {{ Form::textarea('route_to_residence', $customer->details->route_to_residence ?? null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Enter directions/route to residence')]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
        {{ Form::submit(__('Update'), array('class' => 'btn btn-primary'))}}
    </div>



{{ Form::close() }}

<script>
    document.addEventListener('click', function(event) {
        if (event.target.id === 'billing_data') {
            document.querySelector("[name='billing_title']").value = document.querySelector("[name='title']").value;
            document.querySelector("[name='billing_name']").value = document.querySelector("[name='name']").value;
            document.querySelector("[name='billing_house_no']").value = document.querySelector("[name='house_no']").value;
            document.querySelector("[name='billing_address']").value = document.querySelector("[name='address']").value;
            document.querySelector("[name='billing_phone']").value = document.querySelector("[name='phone_mobile']").value;
            document.querySelector("[name='billing_email']").value = document.querySelector("[name='email']").value;
        }
    });

    $(document).ready(function() {
        $('form').on('submit', function(e) {
            if ($('select[name="employment_feild"]').val() === "") {
                e.preventDefault(); // Prevent form submission
                AppMessages.warning('Please select a valid Employment Type.');
            }
        });
    });

    $(document).on('focusout', '#nic_no', function () {
        var currentValue = $(this).val();
        $(this).val(currentValue.toUpperCase());
    });

    function previewCustomerPhoto(input) {
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#customer_photo_preview').attr('src', e.target.result);
                $('#preview_badge').show().text('{{ __("New image preview") }}');
                $('#selected_photo_name').show().html('<i class="fas fa-file-image"></i> {{ __("Selected") }}: <strong>' + file.name + '</strong>');
            };
            reader.readAsDataURL(file);
        }
    }

    $(document).on('change', '#customer_photo', function () {
        previewCustomerPhoto(this);
    });
</script>

