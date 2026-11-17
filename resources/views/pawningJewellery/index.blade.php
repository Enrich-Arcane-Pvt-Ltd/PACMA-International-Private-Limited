@extends('layouts.admin')
@section('page-title')
{{__('Pawning Jewellery')}}
@endsection
@section('content')
<section class="section">
    {{-- <div class="section-header">
        <h1>{{__('Pawning Jewellery')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Pawning Jewellery')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Pawning Jewellery ') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Pawning Jewellery')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Pawning Jewellery')}}</h4>
                            @can('create pawning jewellery')
                            <div class="col-auto">
                                <a href="#" data-size="xl" data-url="{{ route('pawningJewellery.create') }}"
                                    data-ajax-popup="true" data-title="{{__('Create Jewellery')}}"
                                    class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{__('Create')}}</span>
                                </a>
                            </div>
                            @endcan
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
                                                        <th> {{__('Jewellery #')}}</th>
                                                        <th> {{__('Jewellery Name')}}</th>
                                                        @if(Gate::check('edit pawning jewellery') || Gate::check('block
                                                        pawning jewellery'))
                                                        <th class="text-center"> {{__('Action')}}</th>
                                                        @endif
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($pawningJewelleries as $pawningJewellery)
                                                    <tr class="font-style">
                                                        <td>
                                                            <a href="#" class="btn btn-outline-primary">
                                                                {{ AUth::user()->jewelleryNumberFormat($pawningJewellery['jewellery_id']) }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $pawningJewellery->name }}</td>
                                                        @if(Gate::check('edit pawning jewellery') || Gate::check('block
                                                        pawning jewellery'))
                                                        <td class="action text-center">
                                                            @can('edit pawning jewellery')
                                                            <a href="#" class="btn btn-primary btn-action mr-1"
                                                                data-size="xl"
                                                                data-url="{{ route('pawningJewellery.edit', $pawningJewellery['id']) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{ __('Edit Jewellery') }}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('Edit') }}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endcan

                                                            @if($pawningJewellery->status==1)
                                                            @can('block pawning jewellery')
                                                            <button class="btn btn-danger btn-action block"
                                                                data-original-title="{{__('Block')}}"
                                                                data-toggle="tooltip" value="{{$pawningJewellery->id}}">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                            @endcan
                                                            @else
                                                            @can('block pawning jewellery')
                                                            <button class="btn btn-success btn-action unblock"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Unblock')}}"
                                                                value="{{$pawningJewellery->id}}">
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
            title: 'Are you sure you want to block this jewellery ?',
            text: "Blocking this jewellery will not allow it to use in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Block'
        }).then((result) => {
            if (result.value) {
                window.location.replace("pawningJewellery/block/" + value);
            }
        });

    });


    $("body").on("click", ".unblock", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to unblock this jewellery ?',
            text: "Unblocking this jewellery will allow it to use in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Unblock'
        }).then((result) => {
            if (result.value) {
                window.location.replace("pawningJewellery/unblock/" + value);
            }
        });
    });

</script>


@endsection
