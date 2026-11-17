@extends('layouts.admin')
@section('page-title')
{{__('Loan Packages')}}
@endsection

@section('content')
<section class="section">
    {{-- <div class="section-header">
        <h1>{{__('Loan Packages')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Loan Packages')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Loan Packages') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Loan Packages')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Loan Packages')}}</h4>
                            <div class="card-header-action">
                                @can('create loan package')
                                <a href="#" data-size="xl" data-url="{{ route('loan_package.create') }}"
                                    data-ajax-popup="true" data-title="{{__('Create New Center')}}"
                                    class="btn btn-icon icon-left btn-primary">
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
                                            <table class="table table-flush" id="dataTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th> {{__('#')}}</th>
                                                        <th> {{__('Name')}}</th>
                                                        <th> {{__('Loan Amount')}}</th>
                                                        <th> {{__('Interest Rate (%)')}}</th>
                                                        <th> {{__('Time Period (Days)')}}</th>
                                                        @if(Gate::check('edit loan package') || Gate::check('block loan
                                                        package'))
                                                        <th class="text-center"> {{__('Action')}}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($packages as $package)
                                                    <tr>
                                                        <td>{{ $package->id }}</td>
                                                        <td>{{ $package->name}}</td>
                                                        <td>{{ number_format($package->loan_amount, 2) }}</td>
                                                        <td>{{ $package->interest_rate}}</td>
                                                        <td>{{ $package->time_period}}</td>
                                                        @if(Gate::check('edit loan package') || Gate::check('block loan
                                                        package'))
                                                        <td class="action text-center">
                                                            @can('edit loan package')
                                                            <a href="#" class="btn btn-primary btn-action mr-1"
                                                                data-size="xl"
                                                                data-url="{{ route('loan_package.edit',$package->id) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{ __('Edit Loan Packages') }}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('Edit') }}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endcan

                                                            @if($package->status==1)
                                                            @can('block loan package')
                                                            <button class="btn btn-danger btn-action mr-1 block"
                                                                data-original-title="{{__('Block')}}"
                                                                data-toggle="tooltip" value="{{$package->id}}">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                            @endcan
                                                            @elseif($package->status==0)
                                                            @can('block loan package')
                                                            <button class="btn btn-success btn-action mr-1 unblock"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Unblock')}}"
                                                                value="{{$package->id}}">
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
            title: 'Are you sure you want to block this Loan Package ?',
            text: "Blocking this Loan Package will not allow it to use in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Block'
        }).then((result) => {
            if (result.value) {
                window.location.replace("loan_package/block/" + value);
            }
        });

    });


    $("body").on("click", ".unblock", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to unblock this Loan Package ?',
            text: "Unblocking this Loan Package will allow it to use in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Unblock'
        }).then((result) => {
            if (result.value) {
                window.location.replace("loan_package/unblock/" + value);
            }
        });
    });

</script>

@endsection
