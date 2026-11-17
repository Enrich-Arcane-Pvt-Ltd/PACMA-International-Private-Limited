@extends('layouts.admin')
@section('page-title')
    {{ __('Fixed Assets') }}
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
            <h1>{{__('Fixed Assets')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Fixed Assets')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Fixed Assets') }}</h1>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Fixed Assets') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('View Fixed Assets') }}</h4>
                                {{-- @can('create assets')
                                    <div class="col-auto">
                                        <a href="#" data-size="xl" data-url="{{ route('asset.create') }}"
                            data-ajax-popup="true" data-title="{{__('Create New Fixed Asset')}}" class="btn btn-icon
                            icon-left btn-primary">
                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                            <span class="btn-inner--text"> {{__('Create')}}</span>
                            </a>
                        </div>
                        @endcan --}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-body p-0">
                                <div id="table-1_wrapper"
                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-flush" id="assets_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th> {{ __('Asset #') }}</th>
                                                            <th> {{ __('Name') }}</th>
                                                            <th> {{ __('Amount') }} ({{ $currencySymbol }})</th>
                                                            {{-- @if (Gate::check('edit assets') || Gate::check('delete assets'))
                                                            <th class="text-center"> {{__('Action')}}</th>
                                                    @endif --}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($assets as $asset)
                                                            <tr>
                                                                <td>
                                                                    {{ AUth::user()->chartOfAccountNumberFormat($asset['id']) }}
                                                                </td>
                                                                <td class="font-style">{{ $asset['name'] }}</td>
                                                                <td class="font-style">
                                                                    {{ number_format($asset['amount']) }}
                                                                    {{-- {{ \Auth::user()->priceFormat($asset['amount']) }} --}}
                                                                </td>
                                                                {{-- <td class="font-style">{{ \Auth::user()->dateFormat($asset->purchase_date) }}
                                                    </td>
                                                    <td class="font-style">
                                                        {{ \Auth::user()->dateFormat($asset->supported_date) }}</td>
                                                    <td class="font-style">
                                                        {{ !empty($asset->description)?$asset->description:'-' }}</td>
                                                    --}}
                                                                {{-- @if (Gate::check('edit assets') || Gate::check('delete assets'))
                                                                <td class="action text-center">
                                                                    @can('edit assets')
                                                                        <a href="#" class="btn btn-primary btn-action mr-1" data-size="xl" data-url="{{ route('asset.edit',$asset->id) }}"
                                                    data-ajax-popup="true" data-title="{{__('Edit Asset')}}"
                                                    data-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                    @endcan
                                                    @can('delete assets')
                                                    <a href="#" class="btn btn-danger btn-action" data-toggle="tooltip"
                                                        data-original-title="{{__('Delete')}}"
                                                        data-confirm="{{__('Are you sure you want to delete this asset ?').' | '.__('Deleting this asset will no longer available in this company. Do you want to continue ?')}}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{$asset->id}}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['asset.destroy',
                                                    $asset->id],'id'=>'delete-form-'.$asset->id]) !!}
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    </td>
                                                    @endif --}}
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
            if (localStorage.getItem("asset_page_no") == null) {
                var pageNo = 0;
            } else {
                var pageNo = Number(localStorage.getItem("asset_page_no"));
            }
            var assetsTable = $('#assets_table').DataTable({
                order: [
                    [0, "asc"]
                ]
            });
            assetsTable.page(pageNo).draw(false);

            $('#assets_table').on('page.dt', function() {
                var pageInfo = assetsTable.page.info();
                pageNo = pageInfo.page;
                localStorage.setItem("asset_page_no", pageNo);
            });
        });
    </script>
@endsection
