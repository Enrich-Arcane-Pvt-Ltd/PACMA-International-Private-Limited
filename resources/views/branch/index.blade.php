@extends('layouts.admin')
@section('page-title')
{{__('Branches')}}
@endsection

@section('content')
<section class="section">
    {{-- <div class="section-header">
        <h1>{{__('Branches')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Branches')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2">{{__('Branches')}}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Branches')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Branches')}}</h4>
                            <div class="card-header-action">
                                @if(\Auth::user()->type=='company')
                                <a href="#" data-size="xl" data-url="{{ route('branch.create') }}"
                                    data-ajax-popup="true" data-title="{{__('Create New Branch')}}"
                                    class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{__('Create')}}</span>
                                </a>
                                @else
                                <button type="button" class="btn btn-icon icon-left btn-primary" id="btn_create_branch">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{__('Create')}}</span>
                                </button>
                                @endif
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
                                                        <th> {{__('Branch #')}}</th>
                                                        <th> {{__('Name')}}</th>
                                                        <th> {{__('Address')}}</th>
                                                        <th> {{__('Phone')}}</th>
                                                        <th> {{__('Branch Manager')}}</th>
                                                        @if(Gate::check('edit branch') || Gate::check('block branch'))
                                                        <th class="text-center"> {{__('Action')}}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($branches as $branch)
                                                    <tr id="branch_detail">
                                                        <td>
                                                            <a href="#" class="btn btn-outline-primary">
                                                                {{ AUth::user()->branchNumberFormat($branch['branch_id']) }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $branch->name}}</td>
                                                        <td>{{ $branch->address}}</td>
                                                        <td>{{ $branch->phone}}</td>
                                                        <td>{{ $branch->branch_manager}}</td>
                                                        @if(Gate::check('edit branch') || Gate::check('block branch'))
                                                        <td class="action text-center">
                                                            @can('edit branch')
                                                            <a href="#" class="btn btn-primary btn-action mr-1"
                                                                data-size="xl"
                                                                data-url="{{ route('branch.edit', $branch['id']) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{ __('Edit Branch') }}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('Edit') }}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endcan

                                                            @if($branch->status==1)
                                                            @can('block branch')
                                                            <button class="btn btn-danger btn-action mr-1 block"
                                                                data-original-title="{{__('Block')}}"
                                                                data-toggle="tooltip" value="{{$branch->id}}">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                            @endcan
                                                            @else
                                                            @can('block branch')
                                                            <button class="btn btn-success btn-action mr-1 unblock"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Unblock')}}"
                                                                value="{{$branch->id}}">
                                                                <i class="fas fa-unlock"></i>
                                                            </button>
                                                            @endcan
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



<script>
    $("body").on("click", ".block", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to block this branch ?',
            text: "Blocking this branch will not allow it to use services in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Block'
        }).then((result) => {
            if (result.value) {
                window.location.replace("branch/block/" + value);
            }
        });

    });


    $("body").on("click", ".unblock", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to unblock this branch ?',
            text: "Unblocking this branch will allow it to use services in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Unblock'
        }).then((result) => {
            if (result.value) {
                window.location.replace("branch/unblock/" + value);
            }
        });
    });

    $('#btn_create_branch').on('click', function () {
        Swal.fire({
            title: 'Create Branch',
            text: "If you want to create a branch, please contact the software support team.",
            type: 'warning'
        });
    });

</script>

@endsection
