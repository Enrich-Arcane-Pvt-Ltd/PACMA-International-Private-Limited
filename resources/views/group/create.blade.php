{{Form::open(array('url'=>'group/store','method'=>'post','class'=>'repeater'))}}

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
                        {{Form::text('name',null,array('class'=>'form-control','required'=>'required'))}}
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
                        {!! Form::select('center_id', $center, null, ['class' => 'form-control customer-sel font-style', 'required'=>'required', 'placeholder' => '- Select -'])!!}
                    </div>
                </div>
            </div>

        </div>

        <div class="card repeater">
            <div class="card-header">
                <h4>{{__('Member Info')}}</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="overflow: auto;">
                    <table class="table table-striped" id="members_tbl">
                        <thead>
                        <tr>
                            <th>{{__('#')}}</th>
                            <th>{{__('Name')}}</th>
                            <th>{{__('Address')}} </th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="members_tbl_body">
                            {{-- <tr>
                                <td><input type="hidden" name="member[0][id]" value=""> 1 </td>
                                <td>Name</td>
                                <td>Address</td>
                                <td><a href="#" class="fas fa-trash remove"></a></td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>{{__('All Members')}}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12" style="overflow: auto;">
                        <table class="table table-flush" id="customers_tbl">
                            <thead class="thead-light">
                                <tr>
                                    <th> {{__('#')}}</th>
                                    <th> {{__('Name')}}</th>
                                    <th> {{__('Address')}}</th>
                                    <th class="text-center"> {{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                <tr id="group_detail">
                                    <td>
                                        {{ AUth::user()->customerNumberFormat($customer->id) }}
                                    </td>
                                    <td>{{ $customer->name}}</td>
                                    <td>{{ $customer->address}}</td>
                                    <td class="action text-center">
                                        @can('edit customer')
                                        <button type="button" class="btn btn-info btn-action btn-sm mr-1 add" data-original-title="{{__('Block')}}" data-toggle="tooltip" value="{{$customer->id}}">
                                            <i class="fa fa-plus"></i> {{ __('Add') }}
                                        </button>
                                        @endcan

                                    </td>
                                </tr>
                                @endforeach
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
{{ Form::close() }}

{{-- @push('script-page') --}}
    <script src="{{asset('assets/js/jquery.repeater.min.js')}}"></script>
    <script>

        $(document).ready(function(){
            $('#customers_tbl').DataTable();

            //remove rows from table
            $(document).delegate('a.remove', 'click', function(e) {
                e.preventDefault();


                AppMessages.confirm("Are you sure You want to remove this member?", () => {

                    var classId = $(this).closest("tr").find('input').val();
                    var id = $(this).attr('data-id');
                    var targetDiv = $(this).attr('targetDiv');
                    $('#rec-' + id).remove();

                    //regnerate index number on table
                    $('#members_tbl_body tr').each(function(index) {
                        //alert(index);
                        $(this).find('span.slno').html(index+1);
                    });

                    var size = $('#members_tbl >tbody >tr').length;
                    if(size >= 5){
                            $('.add').prop('disabled', true);
                        } else {
                            $('.add').prop('disabled', false);
                        }

                    $('#customers_tbl .add').each(function() {
                        var get_class_value = $(this).val();
                        if(get_class_value == classId){
                            $(this).removeClass('added');
                        }
                    });
                    $('.added').prop('disabled', true);
                return true;

                });
            });
        });

        $('body').unbind('click').on('click', '.add', function () {
            // $('.add').click(function () {
                var el = $(this);
                var member_id = el.val();
                $.ajax({
                    url: "{{ url('group/add_member') }}/"+member_id,
                    method: 'GET',
                    success: function (data) {
                        var member = JSON.parse(data);
                        var size = $('#members_tbl >tbody >tr').length + 1;
                        var newRow = $('<tr>');
                        var cols = '';
                            cols += '<td><input type="hidden" name="member[' + size + '][id]" value="' + member.id + '"> <span class="slno"></span></td>';
                            cols += '<td>' + member.name + '</td>';
                            cols += '<td>' + member.address + '</td>';
                            cols += '<td><a href="#" class="fas fa-trash remove" title="Remove" data-id="0"></a></td></tr>';
                            newRow.append(cols);
                        var content = newRow;
                        var element = null;
                            element = content.clone();
                            element.attr('id', 'rec-'+size);
                            element.find('.remove').attr('data-id', size);
                            element.appendTo('#members_tbl_body');
                            element.find('.slno').html(size);

                        if(size >= 5){
                            $('.add').prop('disabled', true);
                        } else {
                            $('.add').prop('disabled', false);
                        }

                        el.addClass('added');
                        $('.added').prop('disabled', true);
                    }
                });
            });

    </script>
{{-- @endpush --}}
