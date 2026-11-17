@extends('layouts.admin')
@php
$profile = asset(Storage::url('uploads/avatar/'));
@endphp
@push('script-page')
<script>
    // $(document).on('click', '#billing_data', function () {
    //     $("[name='shipping_name']").val($("[name='billing_name']").val());
    //     $("[name='shipping_country']").val($("[name='billing_country']").val());
    //     $("[name='shipping_state']").val($("[name='billing_state']").val());
    //     $("[name='shipping_city']").val($("[name='billing_city']").val());
    //     $("[name='shipping_phone']").val($("[name='billing_phone']").val());
    //     $("[name='shipping_zip']").val($("[name='billing_zip']").val());
    //     $("[name='shipping_address']").val($("[name='billing_address']").val());
    // })

</script>
@endpush
@section('page-title')
{{ __('Suppliers') }}
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
    <script>
        (function() {
            function hideLoader() {
                var l = document.querySelector('.se-pre-con');
                if (l) l.style.display = 'none';
            }
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(hideLoader, 200);
            } else {
                document.addEventListener('DOMContentLoaded', hideLoader);
            }
            window.addEventListener('load', hideLoader);
            setTimeout(hideLoader, 1000);
        })();
    </script>
    {{-- <div class="section-header">
            <h1>{{__('Suppliers')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Suppliers')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Suppliers') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Suppliers') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{ __('Manage Suppliers') }}</h4>
                            <div class="card-header-action">
                                @can('create vender')
                                <a href="#" data-size="2xl" data-url="{{ route('vender.create') }}"
                                    data-ajax-popup="true" data-title="{{ __('Create New Supplier') }}"
                                    class="btn btn-icon icon-left btn-primary commonModal">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{ __('Create') }}</span>
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div id="table-1_wrapper"
                                class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-flush" id="venders_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Supplier #</th>
                                                        <th> {{ __('Supplier Name') }}</th>
                                                        <th> {{ __('Company Name') }}</th>
                                                        <th> {{ __('Contact') }}</th>
                                                        <th> {{ __('Email') }}</th>
                                                        <th class="text-right">{{ __('Action') }}</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($venders as $k => $Vender)
                                                    <tr class="cust_tr" id="vend_detail">
                                                        <td>
                                                            @can('show vender')
                                                            <a href="{{ route('vender.show', $Vender['id']) }}"
                                                                class="btn btn-outline-primary">
                                                                {{ AUth::user()->venderNumberFormat($Vender['vender_id']) }}
                                                            </a>
                                                            @else
                                                            <a href="#" class="btn btn-outline-primary">
                                                                {{ AUth::user()->venderNumberFormat($Vender['vender_id']) }}
                                                            </a>
                                                            @endcan

                                                        </td>
                                                        <td>{{ $Vender['contact_name'] }}</td>
                                                        <td>{{ $Vender['name'] }}</td>
                                                        <td>{{ $Vender['contact'] }}</td>
                                                        <td>{{ $Vender['email'] }}</td>
                                                        <td class="text-right">
                                                            @if ($Vender['is_active'] == 0)
                                                            <i class="fa fa-lock" title="Inactive"></i>
                                                            @else
                                                            @can('show vender')
                                                            <a href="{{ route('vender.show', $Vender['id']) }}"
                                                                class="btn btn-info btn-action mr-1"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('View') }}">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @endcan
                                                            @can('edit vender')
                                                            <a href="#!" class="btn btn-primary btn-action mr-1"
                                                                data-size="2xl"
                                                                data-url="{{ route('vender.edit', $Vender['id']) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{ __('Edit Supplier') }}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('Edit') }}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endcan
                                                            @if ($Vender->is_active == 1)
                                                            @can('block vender')
                                                            <button class="btn btn-danger btn-action block mr-1"
                                                                data-original-title="{{ __('Block') }}"
                                                                data-toggle="tooltip" value="{{ $Vender->id }}">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                            @endcan
                                                            @else
                                                            @can('block vender')
                                                            <button class="btn btn-success btn-action unblock mr-1"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('Unblock') }}"
                                                                value="{{ $Vender->id }}">
                                                                <i class="fas fa-unlock"></i>
                                                            </button>
                                                            @endcan
                                                            @endif

                                                            @if (Gate::check('delete vender') || Auth::user()->type == 'company' || Auth::user()->type == 'super admin')
                                                            <button class="btn btn-danger btn-action delete-vender mr-1"
                                                                data-id="{{ $Vender['id'] }}"
                                                                data-name="{{ $Vender['name'] }}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('Delete') }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                            <form id="delete-form-{{ $Vender['id'] }}" action="{{ route('vender.destroy', $Vender['id']) }}" method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                            @endif
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-none" id="vender_details">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script-page')
<script>
    function hideVenderLoader() {
        $(".se-pre-con").fadeOut('slow');
    }
    $(document).ready(function () {
        hideVenderLoader();
        if (localStorage.getItem("vender_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("vender_page_no"));
        }
        var vendersTable = $('#venders_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        vendersTable.page(pageNo).draw(false);

        $('#venders_table').on('page.dt', function () {
            var pageInfo = vendersTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("vender_page_no", pageNo);
        });
    });

    $(window).on('load', function () {
        hideVenderLoader();
    });
    setTimeout(hideVenderLoader, 800);

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
                window.location.replace("vender/unblocktwo/" + value);
            }
        });

    });

    $("body").on("click", ".unblock", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to unblock this Supplier?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Continue!'
        }).then((result) => {
            if (result.value) {
                window.location.replace("vender/unblock/" + value);
            }
        });

    });

    $("body").on("click", ".block", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to block this Supplier?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Continue!'
        }).then((result) => {
            if (result.value) {
                window.location.replace("vender/block/" + value);
            }
        });
    });

    $("body").on("click", ".delete-vender", function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name') || 'this supplier';
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Are you sure you want to delete") }} "' + name + '"? {{ __("This action cannot be undone.") }}',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#fc544b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.value || result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    });

</script>
@endpush
