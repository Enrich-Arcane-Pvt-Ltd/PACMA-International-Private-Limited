@extends('layouts.admin')

@section('page-title')
{{ __('Account Sections') }}
@endsection
@section('content')
<section class="section">
    {{-- <div class="section-header">
        <h1>{{ __('Account Sections') }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
            <div class="breadcrumb-item">{{ __('Account Sections') }}</div>
        </div>
    </div> --}}
    <div class="row justify-content-center">

        <div class="col-12">
            <div class="bg-white p-2 br-4 mb-2">
                <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                    <h4 class="mt-2 ">{{ __('Account Sections') }}</h1>
                    <div class="section-header-breadcrumb d-flex ">
                        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
            <div class="breadcrumb-item">{{ __('Account Sections') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card br-4 cus_show">
                <div class="card-header">
                    <div class="d-flex justify-content-between w-100">
                        <h4>{{ __('Manage Account Sections') }}</h4>
                        <div class="card-header-action">
                            @can('create account section')
                            <a href="#" data-size="xl" data-url="{{ route('account_section.create') }}" data-ajax-popup="true" data-title="{{ __('Create New Account Section') }}" class="btn btn-icon icon-left btn-primary commonModal">
                                <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                <span class="btn-inner--text"> {{ __('Create') }}</span>
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-body p-0">
                        <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-flush" id="account_sections_table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th> {{ __('Account Section #') }}</th>
                                                    <th> {{ __('Section Name') }}</th>
                                                    <th class="text-center">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($accountsections as $k => $accountsection)
                                                {{-- <tr class="main_row" id="account_group_detail" data-url="{{ route('accountGroup.show', $accountgroup['id']) }}" data-id="{{ $accountgroup['id'] }}"> --}}
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('account_section.show', $accountsection['id']) }}" class="btn btn-outline-primary">
                                                            {{ AUth::user()->accountGroupNumberFormat($accountsection['id']) }}
                                                        </a>
                                                    </td>
                                                    {{-- <td class="account_group_td">
                                                    {{ $accountgroup['Group Name'] }}</td>
                                                    <td class="account_group_td">{{ $accountgroup['Section Name'] }}</td>  --}}
                                                    {{-- <td class="account_group_td">{{ $accountgroup['id'] }}</td> --}}
                                                    <td class="account_section_td">{{ $accountsection['name'] }}</td>

                                                    <td class="text-center">
                                                        @can('show account section')
                                                        <a href="{{ route('account_section.show', $accountsection['id']) }}" class="btn btn-info btn-action mr-1" data-toggle="tooltip" data-original-title="{{ __('View') }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @endcan

                                                        @can('edit account section')
                                                        <a href="#" class="btn btn-primary btn-action mr-1" data-size="xl" data-url="{{ route('account_section.edit', $accountsection['id']) }}" data-ajax-popup="true" data-title="{{ __('Edit Account Group') }}" data-toggle="tooltip" data-original-title="{{ __('Edit') }}">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        @endcan

                                                        {{-- @if($accountgroup->status==1)
                                                        @can('change group status')
                                                        <button class="btn btn-danger btn-action mr-1 block" data-original-title="{{__('Block')}}" data-toggle="tooltip" value="{{$accountgroup->id}}">
                                                            <i class="fas fa-lock"></i>
                                                        </button>
                                                        @endcan
                                                        @else
                                                        @can('change group status')
                                                        <button class="btn btn-success btn-action mr-1 unblock" data-toggle="tooltip" data-original-title="{{__('Unblock')}}" value="{{$accountgroup->id}}">
                                                            <i class="fas fa-unlock"></i>
                                                        </button>
                                                        @endcan --}}
                                                        {{-- @endif --}}
                                                    </td>
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
</section>


<script>

        $(document).ready(function(){
        $(".se-pre-con").fadeOut('slow');
        if(localStorage.getItem("account_section_no") == null){
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("account_section_no"));
        }
        var account_groupsTable = $('#account_section_table').DataTable({order:[[0,"desc"]]});
        account_groupsTable.page(pageNo).draw(false);

        $('#account_section_table').on( 'page.dt', function () {
            var pageInfo =  account_groupsTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("account_section_no", pageNo);
        });
    });

        $('body').on('click', '.account_section_td', function(){
        var accountGroupId = $(this).closest('tr').attr("data-id");
        window.location.href = "{{ url('account_sections') }}/"+accountGroupId;
        });


        $("body").on("click", ".block", function() {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to block this user ?',
            text: "Blocking this user will not allow them to use services in this company.",
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


    $("body").on("click", ".unblock", function() {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to unblock this user ?',
            text: "Unblocking this user will allow them to use services in this company.",
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
