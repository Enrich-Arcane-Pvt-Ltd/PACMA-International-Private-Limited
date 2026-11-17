{{Form::open(array('class'=>'repeater'))}}

        <h4 class="sub-title">{{__('Group Info')}}</h4>
        <div class="row">

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('name', __('Group Name')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        {{Form::text('name',$groups->name,array('class'=>'form-control', 'readonly'=>'readonly'))}}
                    </div>
                </div>
            </div>



            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('center_id',__('Center')) }}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-store-alt"></i>
                            </div>
                        </div>
                        {!! Form::text('center_id', $groups->center->name ?? '', ['class' => 'form-control', 'readonly'=>'readonly'])!!}
                    </div>
                </div>
            </div>

        </div>

        <div class="card repeater">
            <div class="card-header">
                <h4>{{__('Member Info')}}</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped" id="members_tbl">
                        <thead>
                        <tr>
                            <th>{{__('#')}}</th>
                            <th>{{__('Name')}}</th>
                            <th>{{__('Address')}} </th>
                        </tr>
                        </thead>
                        <tbody id="members_tbl_body">
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($groups->groupMembers as $members)
                                <tr>
                                    <td> {{ $no }} </td>
                                    <td> {{ $members->customers->name ?? '--' }} </td>
                                    <td> {{ $members->customers->address ?? '--' }} </td>
                                </tr>
                                @php $no++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12 text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
        </div>
{{ Form::close() }}
