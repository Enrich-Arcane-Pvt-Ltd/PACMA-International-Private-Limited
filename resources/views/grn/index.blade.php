@extends('layouts.admin')

@section('page-title')
    {{ __('GRN') }}
@endsection
@push('script-page')
    <script src="{{ asset('assets/js/grn-search-history.js') }}"></script>
@endpush

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

        .tab-view ul {
            list-style-type: none;
            display: flex;
            justify-content: space-between;
            padding-left: 0rem;
        }

        .tab-view ul li {
            display: flex;
            align-items: center;
            justify-content: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            border-bottom: 1px solid #F97316;
            padding: 0.5rem 1rem;
            width: 33.33%;
            text-align: center;
        }

        .tab-view ul li a {
            text-decoration: none;
            color: #EA580C;
            width: 100%;
        }

        .bg-primary {
            background-color: #F97316 !important;
        }

        .bg-primary a {
            color: #ffffff !important;
        }
    </style>

    <section class="section">
        <div class="se-pre-con"></div>

        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2">{{ __('GRN') }}</h4>

                            <div class="section-header-breadcrumb d-flex">
                                <div class="breadcrumb-item active">
                                    <a href="{{ route('dashboard') }}">
                                        {{ __('Dashboard') }}
                                    </a>
                                </div>

                                <div class="breadcrumb-item">
                                    {{ __('GRN') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="card-header">
                                <div class="w-100 tab-view">
                                    <ul>
                                        <li>
                                            <a href="{{ route('purchase-orders.create-grn') }}">
                                                {{__('Create GRN From Purchase Orders')}}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('grn.manual.create') }}">
                                                {{__('Create Manual GRN')}}
                                            </a>
                                        </li>
                                        <li class="bg-primary">
                                            <a href="{{ route('grn.index') }}">
                                                {{__('Manage GRN')}}
                                            </a>
                                        </li>
                                    </ul>
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

                                                <table class="table table-flush" id="grn_table" data-search-user="{{ Auth::id() }}">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th class="d-none"> {{ __('GRN') }}</th>
                                                            <th> {{ __('Supplier Invoice Number') }}</th>
                                                            <th> {{ __('Received Date') }}</th>
                                                            <th> {{ __('Supplier Name') }}</th>
                                                            <th> {{ __('Status') }}</th>
                                                            <th> {{ __('Head Office Approval') }}</th>
                                                            @if (Gate::check('manage grn') || Gate::check('edit grn') || Gate::check('delete grn') || Gate::check('view grn'))
                                                                <th class="text-right"> {{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($grn as $grn)
                                                            @php
                                                                $productCodes = $grn->grnProducts->map(function ($line) {
                                                                    return optional($line->products)->barcode;
                                                                })->filter()->unique()->implode(' ');
                                                            @endphp
                                                            <tr class="font-style">
                                                                <td class="d-none" data-order="{{ $grn->id }}">
                                                                    @if (\Auth::guard('vender')->check())
                                                                        <a class="btn btn-outline-primary"
                                                                            href="{{ route('purchases.show', $grn->id) }}">{{ AUth::user()->GrnNumberFormat($grn->id) }}
                                                                        </a>
                                                                    @else
                                                                        <a class="btn btn-outline-primary"
                                                                            href="{{ route('grn.show', $grn->id) }}">{{ AUth::user()->GrnNumberFormat($grn->id) }}
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                                <td data-search="{{ $grn->supplier_invoice_number }} {{ $productCodes }}" data-order="{{ $grn->supplier_invoice_number }}">
                                                                    @if (\Auth::guard('vender')->check())
                                                                        <a class="btn btn-outline-primary"
                                                                            href="{{ route('purchases.show', $grn->id) }}">{{ $grn->supplier_invoice_number ?? '-N/A-' }}
                                                                        </a>
                                                                    @else
                                                                        <a class="btn btn-outline-primary"
                                                                            href="{{ route('grn.show', $grn->id) }}">{{ $grn->supplier_invoice_number ?? '-N/A-' }}
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{ Auth::user()->dateFormat($grn->required_by) }}
                                                                </td>
                                                                <td>
                                                                    {{ !empty($grn->vender) ? $grn->vender->name : '-' }}
                                                                </td>
                                                                <td>
                                                                    @include('grn.status-badge', ['status' => $grn->status])
                                                                </td>
                                                                <td>
                                                                    @php $hoStatus = $grn->approval_status ?? 'pending'; @endphp
                                                                    @if ($hoStatus === 'approved')
                                                                        <span class="badge badge-success">{{ __('Approved by HO') }}</span>
                                                                    @elseif ($hoStatus === 'rejected')
                                                                        <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                                                    @else
                                                                        <span class="badge badge-warning">{{ __('Pending HO Approval') }}</span>
                                                                    @endif
                                                                </td>

                                                                @if (Gate::check('manage grn') || Gate::check('edit grn') || Gate::check('delete grn') || Gate::check('view grn'))
                                                                    <td class="action text-right">
                                                                        @if ($hoStatus === 'pending' && Auth::user()->hasRole(['super admin', 'company', 'Admin']))
                                                                            <form method="POST" action="{{ route('grn.approve_ho', $grn->id) }}" class="d-inline">
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-success btn-action mr-1" data-toggle="tooltip" data-original-title="{{ __('Approve (Head Office)') }}">
                                                                                    <i class="fas fa-check"></i>
                                                                                </button>
                                                                            </form>
                                                                            <form method="POST" action="{{ route('grn.reject_ho', $grn->id) }}" class="d-inline">
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-warning btn-action mr-1" data-toggle="tooltip" data-original-title="{{ __('Reject (Head Office)') }}">
                                                                                    <i class="fas fa-times"></i>
                                                                                </button>
                                                                            </form>
                                                                        @endif
                                                                        @can('view grn')
                                                                            <a href="{{ route('grn.show', $grn->id) }}"
                                                                                class="btn btn-primary btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Detail') }}">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                        @endcan
                                                                        @can('edit grn')
                                                                            <a href="{{ url('grnOrderEdit/OrderEdit/' . $grn->id . '') }}"
                                                                                class="btn btn-primary btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Edit') }}">
                                                                                <i class="fas fa-pencil-alt"></i>
                                                                            </a>
                                                                        @endcan
                                                                        @if (Auth::user()->canDeleteGrn())
                                                                            <a href="#" data-id="{{ $grn->id }}"
                                                                                class="btn btn-danger btn-action delete"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Delete') }}">
                                                                                <i class="fas fa-trash"></i>
                                                                            </a>
                                                                            <form id="grn-delete-form-{{ $grn->id }}" method="POST" action="{{ route('grn.destroy', $grn->id) }}" class="d-none">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                            </form>
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
        $(document).ready(function() {
            $(".se-pre-con").fadeOut('slow');
            var grnTable = $('#grn_table').DataTable({
                order: [
                    [0, "desc"]
                ]
            });
        });

        $("body").on("click", ".delete", function(event) {
            var value = $(this).attr('data-id');
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
                    document.getElementById('grn-delete-form-' + value).submit();
                }
            });

        });

        $("body").on("click", ".unblock", function() {
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
                    window.location.replace("purchase/unblock/" + value);
                }
            });
        });
    </script>
@endsection
