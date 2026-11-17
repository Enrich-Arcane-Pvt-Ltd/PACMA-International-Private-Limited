@extends('layouts.admin')
@section('page-title')
{{__('Cheques In Book')}}
@endsection

@section('content')
<section class="section">
    {{-- <div class="section-header">
        <h1>{{__('Cheques In Book')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Cheques In Book')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2">{{__('Cheques In Book')}}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Cheques In Book')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Cheques In Book')}}</h4>
                            {{-- <div class="card-header-action">
                                @can('create bank account')
                                <a href="#" data-size="xl" data-url="" data-ajax-popup="true" data-title="{{__('Open New Cheque Book')}}"
                            class="btn btn-icon icon-left btn-primary">
                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                            <span class="btn-inner--text"> {{__('Create')}}</span>
                            </a>
                            @endcan
                        </div> --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-flush" id="dataTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th> {{__('Cheque #')}}</th>
                                            <th> {{__('Status')}}</th>
                                            {{-- <th class="text-center"> {{__('Action')}}</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cheques as $cheque)
                                        <tr id="center_detail">
                                            <td>
                                                <button type="button" class="btn btn-outline-primary view"
                                                    value="{{ $cheque->cheque_id }}">
                                                    {{ $cheque->cheque_number }}
                                                </button>
                                            </td>
                                            <td>
                                                @if ($cheque->status == 1)
                                                <span class="badge badge-primary">Pending</span>
                                                @elseif ($cheque->status == 0)
                                                <span class="badge badge-success">Issued</span>
                                                @elseif ($cheque->status == 2)
                                                <span class="badge badge-danger">Cancelled</span>
                                                @elseif ($cheque->status == 3)
                                                <span class="badge badge-warning">Reserved</span>
                                                @endif
                                            </td>
                                            {{-- <td class="action text-center"> --}}
                                            {{-- @can('edit customer')
                                                    <a href="#" class="btn btn-primary btn-action mr-1" data-size="xl" data-url="{{ route('center.edit', $center['id']) }}"
                                            data-ajax-popup="true" data-title="{{ __('Edit Center') }}"
                                            data-toggle="tooltip" data-original-title="{{ __('Edit') }}">
                                            <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            @endcan

                                            @if($center->status==1)
                                            @can('delete bank account')
                                            <button class="btn btn-danger btn-action mr-1 block"
                                                data-original-title="{{__('Block')}}" data-toggle="tooltip"
                                                value="{{$center->id}}">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                            @endcan
                                            @else
                                            @can('delete bank account')
                                            <button class="btn btn-success btn-action mr-1 unblock"
                                                data-toggle="tooltip" data-original-title="{{__('Unblock')}}"
                                                value="{{$center->id}}">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                            @endcan
                                            @endif --}}
                                            {{-- </td> --}}
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

</section>



<script>
    var cheques = < ? php echo json_encode($cheques); ? > ;

    $(document).ready(function () {
        console.log(cheques);
    });

    $("body").on("click", ".block", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to block this center ?',
            text: "Blocking this center will not allow it to use in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Block'
        }).then((result) => {
            if (result.value) {
                window.location.replace("center/block/" + value);
            }
        });

    });


    $("body").on("click", ".unblock", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to unblock this center ?',
            text: "Unblocking this center will allow it to use in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Unblock'
        }).then((result) => {
            if (result.value) {
                window.location.replace("center/unblock/" + value);
            }
        });
    });

    $('body').on('click', '.view', function () {
        var chequeId = $(this).val();
        window.open("{{ url('cheque-print') }}/" + chequeId + "/view");
    });

    $('body').on('click', '.print', function () {
        var chequeId = $(this).val();
        window.open("{{ url('cheque-print') }}/" + chequeId + "/print");
    });

</script>

@endsection
