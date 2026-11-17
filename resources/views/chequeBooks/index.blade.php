@extends('layouts.admin')
@section('page-title')
{{__('Cheque Books')}}
@endsection

@section('content')
<section class="section">
    {{-- <div class="section-header">
        <h1>{{__('Cheque Books')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Cheque Books')}}</div>
    </div>
    </div> --}}

    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2">{{__('Cheque Books')}}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Cheque Books')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Cheque Books')}}</h4>
                            <div class="card-header-action">
                                @can('create cheque book')
                                <a href="#" data-size="xl" data-url="{{ route('cheque_book.create') }}"
                                    data-ajax-popup="true" data-title="{{__('Open New Cheque Book')}}"
                                    class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{__('Create')}}</span>
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="setting-tab">
                            <ul class="nav nav-pills nav-fill" id="myTab3" role="tablist">
                                @foreach ($chequeBanks as $chequeBank)
                                <li class="nav-item">
                                    <a class="nav-link" id="cheques_{{ $chequeBank->id }}_tab" data-toggle="tab"
                                        href="#cheques_{{ $chequeBank->id }}" role="tab" aria-controls=""
                                        aria-selected="false">{{ $chequeBank->bank }}</a>
                                </li>
                                @endforeach
                                {{-- <li class="nav-item">
                                    <a class="nav-link active" id="commercial_cheques_tab" data-toggle="tab" href="#commercial_cheques" role="tab" aria-controls="" aria-selected="false">{{__('Commercial Bank')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="selan_cheques_tab" data-toggle="tab" href="#selan_cheques"
                                        role="tab" aria-controls="" aria-selected="false">{{__('Selan Bank')}}</a>
                                </li> --}}
                            </ul>
                            <div class="tab-content" id="myTabContent2">
                                @foreach ($chequeBanks as $cheque_bank)
                                <div class="tab-pane fade fade" id="cheques_{{ $cheque_bank->id }}" role="tabpanel"
                                    aria-labelledby="profile-tab3">
                                    <div class="company-setting-wrap">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <table class="table table-flush dataTable">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th> {{__('Cheque Book #')}}</th>
                                                                    <th> {{__('Cheques Amount')}}</th>
                                                                    <th> {{__('Start From')}}</th>
                                                                    <th> {{__('End At')}}</th>
                                                                    <th> {{__('Status')}}</th>
                                                                    {{-- <th class="text-center"> {{__('Action')}}</th>
                                                                    --}}
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($cheque_bank->chequeBooks as $chequeBook)
                                                                <tr id="center_detail">
                                                                    <td>
                                                                        <a href="{{ route('cheque_book.view', $chequeBook->id) }}"
                                                                            class="btn btn-outline-primary">
                                                                            {{ AUth::user()->chequeBookNumberFormat($chequeBook->id) }}
                                                                        </a>
                                                                        {{-- <button type="button" class="btn btn-outline-primary view" value="{{ $chequeBook->id }}">
                                                                        {{ AUth::user()->chequeBookNumberFormat($chequeBook->id) }}
                                                                        </button> --}}
                                                                    </td>
                                                                    <td>{{ $chequeBook->cheques_amount }}</td>
                                                                    <td>{{ $chequeBook->start_from }}</td>
                                                                    <td>{{ $chequeBook->end_at }}</td>
                                                                    <td>
                                                                        @if ($chequeBook->status == 1)
                                                                        <span class="badge badge-primary">Ongoing</span>
                                                                        @elseif ($chequeBook->status == 0)
                                                                        <span
                                                                            class="badge badge-success">Finished</span>
                                                                        @elseif ($chequeBook->status == 3)
                                                                        <span
                                                                            class="badge badge-danger">Cancelled</span>
                                                                        @endif
                                                                    </td>
                                                                    {{-- <td class="action text-center">
                                                                            <button type="button" class="btn btn-info btn-action mr-1 print" title="Print" value="{{ $chequeBook->id }}"><i
                                                                        class="fas fa-print"></i></button> --}}
                                                                    {{-- @can('edit customer')
                                                                            <a href="#" class="btn btn-primary btn-action mr-1" data-size="xl" data-url="{{ route('center.edit', $center['id']) }}"
                                                                    data-ajax-popup="true"
                                                                    data-title="{{ __('Edit Center') }}"
                                                                    data-toggle="tooltip"
                                                                    data-original-title="{{ __('Edit') }}">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                    </a>
                                                                    @endcan

                                                                    @if($center->status==1)
                                                                    @can('delete bank account')
                                                                    <button class="btn btn-danger btn-action mr-1 block"
                                                                        data-original-title="{{__('Block')}}"
                                                                        data-toggle="tooltip" value="{{$center->id}}">
                                                                        <i class="fas fa-lock"></i>
                                                                    </button>
                                                                    @endcan
                                                                    @else
                                                                    @can('delete bank account')
                                                                    <button
                                                                        class="btn btn-success btn-action mr-1 unblock"
                                                                        data-toggle="tooltip"
                                                                        data-original-title="{{__('Unblock')}}"
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
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>



<script>
    var chequeBanks = < ? php echo json_encode($chequeBanks); ? > ;

    $(document).ready(function () {
        console.log(chequeBanks);
        $('.dataTable').dataTable();
        if (chequeBanks.length > 0) {
            $('#cheques_' + chequeBanks[0].id + '_tab').addClass('active show');
            $('#cheques_' + chequeBanks[0].id).addClass('active show');
        }
    });

    $('body').on('click', '.view', function () {
        var chequeBookId = $(this).val();
        location.href("{{ url('cheque_book/view') }}/" + chequeBookId);
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

    $('body').on('click', '.print', function () {
        var chequeId = $(this).val();
        window.open("{{ url('cheque-print') }}/" + chequeId + "/print");
    });

</script>

@endsection
