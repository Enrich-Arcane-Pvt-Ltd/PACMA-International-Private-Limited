@extends('layouts.admin')
@section('page-title')
{{ __('Warehouse') }}
@endsection

@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{ __('Warehouse') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Warehouse Detail') }}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Warehouse') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Warehouse Detail') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{ __('Manage Warehouse') }}</h4>
                            <div class="card-header-action">
                                @can('create bank account')
                                <a href="#" data-url="{{ route('warehousesCreate') }}" data-ajax-popup="true"
                                    data-title="{{ __('Create New Warehouse') }}"
                                    class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{ __('Create') }}</span>
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body p-0">
                            <div id="table-1_wrapper"
                                class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-flush" id="dataTable">
                                                <thead class="thead-light">
                                                    <tr>

                                                        <th> {{ __('Warehouse Name') }}</th>
                                                        <th> {{ __('Contact Number') }}</th>
                                                        <th> {{ __('Address') }}</th>
                                                        <th class="text-right"> {{ __('Action') }}</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($warehouses as $warehouse)
                                                    <tr class="font-style">
                                                        <td class="name">{{ $warehouse->name }}</td>
                                                        <td class="contact">{{ $warehouse->contact }}</td>
                                                        <td class="address">{{ $warehouse->address }}</td>


                                                        @if (Gate::check('edit bank account') || Gate::check('delete
                                                        bank account'))
                                                        <td class="action text-right">
                                                            @if ($warehouse->name != 'Cash')
                                                            @can('edit bank account')
                                                            <button type="button"
                                                                class="btn btn-primary btn-action btnClosePopup mr-1"
                                                                value="{{ $warehouse->id }}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </button>
                                                            @endcan
                                                            @if ($warehouse->status == 1)
                                                            {{-- @can('delete bank account') --}}
                                                            <button class="btn btn-danger btn-action unblock"
                                                                data-original-title="{{ __('Block') }}"
                                                                data-toggle="tooltip" value="{{ $warehouse->id }}">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                            {{-- @endcan --}}
                                                            @else
                                                            {{-- @can('delete bank account') --}}
                                                            <button class="btn btn-success btn-action delete"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('Unblock') }}"
                                                                value="{{ $warehouse->id }}">
                                                                <i class="fas fa-unlock"></i>
                                                            </button>
                                                            {{-- @endcan --}}
                                                            @endif
                                                            @else
                                                            -
                                                            @endif
                                                        </td>
                                                        @endif
                                                    </tr>
                                                    @endforeach

                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>




</section>
<section>

    <!-- Modal -->
    <form action="{{ route('warehousesUpdate') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="myModal">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Warehouse</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" class="modelId" name="warehouse_id" value="" />

                            <div class="form-group  col-md-6">
                                {{ Form::label('name', __('Warehouse Name')) }}
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-warehouse"></i>
                                        </div>
                                    </div>

                                    <input type="text" class="form-control modelName" name="name" value="" required>
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
                                    <input type="text" class="form-control modelContact" name="contact" value=""
                                        required>
                                </div>
                            </div>
                            <div class="form-group  col-md-12">
                                {{ Form::label('address', __('Address')) }}
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-address-card"></i>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control modelAddress" name="address" value=""
                                        required>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Edit</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </form>
</section>


<script type="text/javascript">
    $(".btnClosePopup").click(function () {
        var id = $(this).val();
        var name = $(this).closest("tr").find(".name").html();
        var contact = $(this).closest("tr").find(".contact").html();
        var address = $(this).closest("tr").find(".address").html();
        $('.modelId').val(id);
        $('.modelName').val(name);
        $('.modelContact').val(contact);
        $('.modelAddress').val(address);
        $("#myModal").modal();
    });

</script>


<script>
    $("body").on("click", ".delete", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Continue!'
        }).then((result) => {
            if (result.value) {
                window.location.replace("../account/unblock1/" + value);
            }
        });

    });

    $("body").on("click", ".unblock", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Continue!'
        }).then((result) => {
            if (result.value) {
                window.location.replace("../account/unblock2/" + value);
            }
        });

    });

</script>
@endsection
