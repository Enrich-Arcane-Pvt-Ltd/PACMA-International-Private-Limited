@extends('layouts.admin')
@section('page-title')
{{__('Bill')}}
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
            <h1>{{__('Bill')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Bill')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Bill') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Bill')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Bill')}}</h4>
                            @can('create bill')
                            <div class="col-auto">
                                <a href="#" data-url="{{ route('serviceBill.create') }}" data-ajax-popup="true"
                                    data-title="{{__('Create Bill')}}" class="btn btn-icon icon-left btn-primary">
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
                                            <table class="table table-flush" id="service_bills_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th> {{__('Bill #')}}</th>
                                                        <th> {{__('Bill Date')}}</th>
                                                        <th> {{__('Service Name')}}</th>
                                                        <th> {{__('Account Number')}}</th>
                                                        <th> {{__('Bill Amount')}}</th>
                                                        <th> {{__('Paid Amount')}}</th>
                                                        @if(Gate::check('edit bill') || Gate::check('delete bill'))
                                                        <th class="text-center"> {{__('Action')}}</th>
                                                        @endif
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($serviceBills as $serviceBill)
                                                    <tr class="font-style">
                                                        <td>
                                                            <p class="btn btn-outline-primary">
                                                                {{ AUth::user()->serviceBillNumberFormat($serviceBill['bill_id']) }}
                                                            </p>
                                                        </td>
                                                        <td>{{ $serviceBill->bill_date }}</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $serviceBill->bill_amount }}</td>
                                                        <td>{{ $serviceBill->paid_amount }}</td>
                                                        @if(Gate::check('edit bill') || Gate::check('delete bill'))
                                                        <td class="action text-right">
                                                            @can('edit bill')
                                                            <a href="#" class="btn btn-primary btn-action mr-1"
                                                                data-url="{{ route('serviceBill.edit',$serviceBill->id) }}"
                                                                data-ajax-popup="true" data-title="{{__('Edit Bill')}}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Edit')}}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endcan
                                                            @can('delete bill')
                                                            <a href="#" class="btn btn-danger btn-action"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Delete')}}"
                                                                data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{$serviceBill->id}}').submit();"><i
                                                                    class="fas fa-trash"></i></a>

                                                            {!! Form::open(['method' => 'DELETE', 'route' =>
                                                            ['serviceBill.destroy',
                                                            $serviceBill->id],'id'=>'delete-form-'.$serviceBill->id])
                                                            !!}
                                                            {!! Form::close() !!}
                                                            @endcan

                                                            {{-- @if($timeperiod->status==1)
                                                                            @can('delete customer')
                                                                                <button  class="btn btn-danger btn-action unblock" data-original-title="{{__('Block')}}"
                                                            data-toggle="tooltip" value="{{$timeperiod->id}}">
                                                            <i class="fas fa-lock"></i>
                                                            </button>
                                                            @endcan
                                                            @else($timeperiod->status=!1)
                                                            @can('delete customer')
                                                            <button class="btn btn-success btn-action delete"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Unblock')}}"
                                                                value="{{$timeperiod->id}}">
                                                                <i class="fas fa-unlock"></i>
                                                            </button>
                                                            @endcan
                                                            @endif --}}
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
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        // console.log(sales);
        if (localStorage.getItem("service_bill_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("service_bill_page_no"));
        }
        var serviceBillsTable = $('#service_bills_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        serviceBillsTable.page(pageNo).draw(false);

        $('#service_bills_table').on('page.dt', function () {
            var pageInfo = serviceBillsTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("service_bill_page_no", pageNo);
        });
    });

</script>
{{-- <script>


        $("body").on("click", ".delete", function(){
            var value = $( this ).val();
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
                    window.location.replace("timeperiods/unblock/"+value);
                }
            });

            });

            $("body").on("click", ".unblock", function(){
            var value = $( this ).val();
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
                    window.location.replace("timeperiods/block/"+value);
                }
            });

            });


        </script> --}}


@endsection
