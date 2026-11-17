@extends('layouts.admin')
@section('page-title')
{{__('Overdue Pawning')}}
@endsection
@section('content')
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
    {{-- <div class="section-header">
        <h1>{{__('Overdue Pawning')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Overdue Pawning')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Overdue Pawning') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Overdue Pawning')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Overdue Pawning')}}</h4>
                        </div>
                    </div>
                    <div class="card-body phone-card-bd">
                        <div class="card-body p-0">
                            <div id="table-1_wrapper"
                                class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-flush" id="dataTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th> {{__('Overdue Date')}}</th>
                                                        <th> {{__('Pawning #')}}</th>
                                                        <th> {{__('Customer')}}</th>
                                                        <th> {{__('Address')}}</th>
                                                        <th> {{__('Phone')}}</th>
                                                        <th> {{__('No of Overdue Installments')}}</th>
                                                        <th> {{__('Overdue Amount')}}</th>
                                                        <th class="text-center"> {{__('Action')}}</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($overduePawningInstallments as $overduePawningInstallment)

                                                    <tr class="font-style">
                                                        <td class="text-danger">
                                                            {{ $overduePawningInstallment->overdue_date }}</td>
                                                        <td>
                                                            <a href="#" class="btn btn-outline-primary">
                                                                {{ AUth::user()->pawningNumberFormat($overduePawningInstallment->pawning['id']) }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if($overduePawningInstallment->pawning->customer['title']
                                                            == 0)
                                                            Mr.
                                                            @elseif($overduePawningInstallment->pawning->customer['title']
                                                            == 1)
                                                            Mrs.
                                                            @else
                                                            Miss.
                                                            @endif
                                                            {{ $overduePawningInstallment->pawning->customer->name }}
                                                        </td>
                                                        <td>{{ $overduePawningInstallment->pawning->customer->address }}
                                                        </td>
                                                        <td>{{ $overduePawningInstallment->pawning->customer->phone }}
                                                        </td>
                                                        <td>{{ $overduePawningInstallment->overdue_installment_count }}
                                                            Installments</td>
                                                        <td>{{ \Auth::user()->priceFormat($overduePawningInstallment->overdue_amount) }}
                                                        </td>
                                                        <td class="action text-center">
                                                            @can('edit customer')
                                                            <button
                                                                class="btn btn-primary btn-action mr-1 send-reminder"
                                                                data-original-title="{{__('Block')}}"
                                                                data-toggle="tooltip"
                                                                value="{{$overduePawningInstallment->id}}">
                                                                <i class="fas fa-envelope"></i>
                                                            </button>
                                                            @endcan
                                                            @can('delete customer')
                                                            <a href="#" class="btn btn-danger btn-action"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Delete')}}"
                                                                data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{$overduePawningInstallment->id}}').submit();">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            {!! Form::open(['method' => 'DELETE', 'route' =>
                                                            ['overdue-pawning.destroy',
                                                            $overduePawningInstallment->id],'id'=>'delete-form-'.$overduePawningInstallment->id])
                                                            !!}
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


<script>
    $("body").on("click", ".send-reminder", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to send a reminder ?',
            text: "Blocking this brand will not allow it to use in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Block'
        }).then((result) => {
            if (result.value) {
                window.location.replace("overdue-pawning/reminderSend/" + value);
            }
        });

    });

</script>


@endsection
