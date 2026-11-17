@extends('layouts.admin')
@section('page-title')
{{__('Groups')}}
@endsection
@section('content')
<style>
    .se-pre-con {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url("{{ asset('assets') }}/loadings/Spinner-1s-200px_blue.gif") center no-repeat #ffffff99;
    }

</style>
<section class="section">
    <div class="se-pre-con"></div>
    {{-- <div class="section-header">
        <h1>{{__('Groups')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Group')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Groups') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Group')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Groups')}}</h4>
                            <div class="card-header-action">
                                @can('create group')
                                <a href="#" data-size="xl" data-url="{{ route('group.create') }}" data-ajax-popup="true"
                                    data-title="{{__('Create New Group')}}" class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{__('Create')}}</span>
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
                                            <table class="table table-flush" id="groups_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th> {{__('Group #')}}</th>
                                                        <th> {{__('Group Name')}}</th>
                                                        <th> {{__('No of Members')}}</th>
                                                        <th> {{__('Center')}}</th>
                                                        @if(Gate::check('edit group') || Gate::check('block group'))
                                                        <th class="text-center"> {{__('Action')}}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($groups as $group)
                                                    <tr id="group_detail">
                                                        <td>
                                                            <a href="#" class="btn btn-outline-primary" data-size="xl"
                                                                data-url="{{ route('group.view', $group->id) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{ __('View Group') }}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('View') }}">
                                                                {{ AUth::user()->groupNumberFormat($group->id) }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $group->name }}</td>
                                                        <td>{{ count($group->groupMembers) }}</td>
                                                        <td>{{ $group->center->name ?? '--'}}</td>
                                                        @if(Gate::check('edit group') || Gate::check('block group'))
                                                        <td class="action text-center">
                                                            @can('edit group')
                                                            <a href="#" class="btn btn-primary btn-action mr-1"
                                                                data-size="xl"
                                                                data-url="{{ route('group.edit', $group->id) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{ __('Edit Group') }}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('Edit') }}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endcan

                                                            @if($group->status==1)
                                                            @can('block group')
                                                            <button class="btn btn-danger btn-action mr-1 block"
                                                                data-original-title="{{__('Block')}}"
                                                                data-toggle="tooltip" value="{{$group->id}}">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                            @endcan
                                                            @else
                                                            @can('block group')
                                                            <button class="btn btn-success btn-action mr-1 unblock"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Unblock')}}"
                                                                value="{{$group->id}}">
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
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        if (localStorage.getItem("groups_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("groups_page_no"));
        }
        var groupsTable = $('#groups_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        groupsTable.page(pageNo).draw(false);

        $('#groups_table').on('page.dt', function () {
            var pageInfo = groupsTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("groups_page_no", pageNo);
        });
    });

    $("body").on("click", ".block", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to block this group ?',
            text: "Blocking this group will not allow it to use services in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Block'
        }).then((result) => {
            if (result.value) {
                window.location.replace("group/block/" + value);
            }
        });

    });


    $("body").on("click", ".unblock", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to unblock this group ?',
            text: "Unblocking this group will allow it to use services in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Unblock'
        }).then((result) => {
            if (result.value) {
                window.location.replace("group/unblock/" + value);
            }
        });
    });

</script>

@endsection
