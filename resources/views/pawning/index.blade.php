@extends('layouts.admin')
@section('page-title')
{{__('Pawning')}}
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

@if(\Auth::user()->type == 'Cash Collector' || \Auth::user()->type == 'Branch Manager' || \Auth::user()->type == 'Field
Manager' || \Auth::user()->type == 'Center Manager')
<style>
    .phone-col-div {
        padding-left: 0;
        padding-right: 0;
    }

    .phone-card-bd {
        padding-left: 5px !important;
        padding-right: 5px !important;
    }

</style>
@endif

<section class="section">
    <div class="se-pre-con"></div>

    {{-- <div class="section-header">
        <h1>{{__('Pawning')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Pawning')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Pawning ') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Pawning')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 phone-col-div">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Pawning')}}</h4>
                            @can('create pawning')
                            <div class="col-auto">
                                <a href="#" data-size="2xl" data-url="{{ route('pawning.create') }}"
                                    data-ajax-popup="true" data-title="{{__('Create Pawning')}}"
                                    class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{__('Create')}}</span>
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body phone-card-bd">
                        <div class="card-body p-0">
                            <div id="table-1_wrapper"
                                class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-flush" id="pawning_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th> {{__('Pawning #')}}</th>
                                                        <th> {{__('Customer No')}}</th>
                                                        <th> {{__('Customer Name')}}</th>
                                                        <th> {{__('NIC')}}</th>
                                                        <th> {{__('Status')}}</th>
                                                        <th> {{__('Due Date')}}</th>
                                                        <th> {{__('Total Payment')}}</th>
                                                        <th> {{__('Outstanding Balance')}}</th>
                                                        <th> {{__('Prepayment')}}</th>
                                                        <th class="text-center"> {{__('Action')}}</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($pawnings as $pawning)
                                                    <tr class="font-style">
                                                        {{-- <td>
                                                            <a href="#" class="btn btn-outline-primary">
                                                                {{ AUth::user()->pawningNumberFormat($pawning['pawning_id']) }}
                                                        </a>
                                                        </td> --}}
                                                        <td class="font-style">
                                                            <a href="{{ url('pawning/view', $pawning->id) }}"
                                                                class="btn btn-outline-primary">{{ AUth::user()->pawningNumberFormat($pawning['id']) }}</a>
                                                            {{-- <button type="button" class="btn btn-outline-primary view" value="{{ $pawning->id }}">
                                                            {{ AUth::user()->pawningNumberFormat($pawning['pawning_id']) }}
                                                            </button> --}}
                                                        </td>
                                                        <td>{{ \Auth::user()->customerNumberFormat($pawning->customer->id) }}
                                                        </td>
                                                        <td>{{ $pawning->customer['name'] }}</td>
                                                        <td>{{ $pawning->customer['nic_no'] }}</td>
                                                        <td class="font-style text-center">
                                                            @if ($pawning->status == 1)
                                                            <span class="badge badge-info">To Be Paid</span>
                                                            @elseif ($pawning->status == 2)
                                                            <span class="badge badge-warning">Partialy Paid</span>
                                                            @elseif ($pawning->status == 3)
                                                            <span class="badge badge-danger">Over Due</span>
                                                            @elseif ($pawning->status == 4)
                                                            <span class="badge badge-success">Paid</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $pawning->due_date }}</td>
                                                        <td>{{ 'Rs. ' }}{{ number_format($pawning->full_total_of_payment, 2) }}
                                                        </td>
                                                        <td>{{ 'Rs. ' }}{{ number_format($pawning->current_due_amount, 2) }}
                                                        </td>
                                                        <td>{{ 'Rs. ' }}{{ number_format($pawning->full_prepayment, 2) }}
                                                        </td>
                                                        <td class="action text-center">
                                                            @can('view pawning')
                                                            {{-- <button type="button" class="btn btn-info btn-action mr-1 view" title="View" value="{{ $pawning->id }}"><i
                                                                class="fas fa-eye"></i></button> --}}
                                                            <a href="{{ url('pawning/view', $pawning->id) }}"
                                                                class="btn btn-info btn-action mr-1"
                                                                style="padding-top: 10px; padding-bottom: 10px;"><i
                                                                    class="fas fa-eye"></i></a>
                                                            @endcan
                                                            <a href="#" data-size="2xl"
                                                                data-url="{{ route('pawning.edit',$pawning->id) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{__('Edit Pawning')}}"
                                                                class="btn btn-info btn-primary"
                                                                style="padding-top: 10px; padding-bottom: 10px;">
                                                                <i class="fas fa-pen"></i>
                                                            </a>
                                                            @can('delete pawning')
                                                            <a href="#" class="btn btn-danger btn-action"
                                                                style="padding-top: 10px;" data-toggle="tooltip"
                                                                data-original-title="{{__('Delete')}}"
                                                                data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{$pawning->id}}').submit();">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            {!! Form::open(['method' => 'DELETE', 'route' =>
                                                            ['pawning.destroy',
                                                            $pawning->id],'id'=>'delete-form-'.$pawning->id]) !!}
                                                            {!! Form::close() !!}
                                                            @endcan
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
    </div>
</section>

@endsection
{{-- @include('partials.admin.footer') --}}
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
    // var pawnings = <php echo json_encode($pawnings) ?>;
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        // console.log(pawnings);

        if (localStorage.getItem("pawning_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("pawning_page_no"));
        }
        var pawningTable = $('#pawning_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        pawningTable.page(pageNo).draw(false);

        $('#pawning_table').on('page.dt', function () {
            var pageInfo = pawningTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("pawning_page_no", pageNo);
        });

        $('.view').click(function () {
            var value = $(this).val();
            window.location.href = "{{ url('pawning/view') }}/" + value;
        });
    });

</script>
