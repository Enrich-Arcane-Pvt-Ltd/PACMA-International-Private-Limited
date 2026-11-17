@extends('layouts.admin')
@section('page-title')
{{__('Autherization Requests')}}
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
        <h1>{{__('Autherization Requests')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Autherization Requests')}}</div>
    </div>
    </div> --}}

    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2">{{__('Autherization Requests')}}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Autherization Requests')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Requests')}}</h4>
                            {{-- @can('create sale')
                            <div class="col-auto">
                                <a href="#" data-size="xl" data-url="{{ route('sale.create') }}" data-ajax-popup="true"
                            data-title="{{__('Create New Sale')}}" class="btn btn-icon icon-left btn-primary">
                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                            <span class="btn-inner--text"> {{__('Create')}}</span>
                            </a>
                            <a href="#" data-size="2xl" data-url="{{ route('direct_sale.create') }}"
                                data-ajax-popup="true" data-title="{{__('Create New Cash Sales')}}"
                                class="btn btn-icon icon-left btn-primary">
                                <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                <span class="btn-inner--text"> {{__('Cash Sales')}}</span>
                            </a>
                        </div>
                        @endcan --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-body p-0">
                        <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-flush" id="requests_table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th> {{__('#')}}</th>
                                                    <th> {{__('Category')}}</th>
                                                    <th> {{__('Date')}}</th>
                                                    <th> {{__('Customer')}}</th>
                                                    <th> {{__('Created By')}}</th>
                                                    {{-- <th> {{__('Status')}}</th> --}}
                                                    @if(Gate::check('approve authorization'))
                                                    <th class="text-right"> {{__('Action')}}</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sales as $sale)
                                                <tr>
                                                    {{-- <td class="font-style">{{ $sale->sale_id }}</td> --}}
                                                    <td class="font-style">
                                                        {{ Auth::user()->waittingSaleNumberFormat($sale->id) }}</td>
                                                    <td class="font-style">
                                                        @if ($sale->sale_type == 0)
                                                        Cash Sales
                                                        @else
                                                        Hire Sales
                                                        @endif
                                                    </td>
                                                    <td class="font-style">
                                                        {{ Auth::user()->dateFormat($sale->date_of_sale) }}</td>
                                                    <td class="font-style"> @if ($sale->customer_type == 1)
                                                        {{ $sale->customer->name ?? '' }}
                                                        @elseif ($sale->customer_type == 2)
                                                        {{ $sale->customer_name }}
                                                        @endif </td>

                                                    <td class="font-style text-center">
                                                        {{ Auth::user()->otherUserName($sale->created_by) }}</td>
                                                    @if(Gate::check('approve authorization'))
                                                    <td class="action text-right">
                                                        {{-- @can('view sale') --}}
                                                        {{-- <button type="button" class="btn btn-info btn-action mr-1 view" title="View" value="{{ $sale->id }}"><i
                                                            class="fas fa-eye"></i></button> --}}
                                                        {{-- <a href="#" class="btn btn-info btn-action mr-1" style="padding-top: 10px;" data-url="{{ route('sale.view',$sale->id) }}"
                                                        data-ajax-popup="true" data-title="{{__('View Sale')}}"
                                                        data-toggle="tooltip" data-original-title="{{__('View')}}">
                                                        <i class="fas fa-eye"></i>
                                                        </a> --}}
                                                        {{-- @endcan --}}

                                                        @can('approve authorization')
                                                        <a href="#" data-size="xl"
                                                            class="btn btn-primary btn-action mr-1"
                                                            style="padding-top: 10px;"
                                                            data-url="{{ route('waitting_sale.approve',$sale->id) }}"
                                                            data-ajax-popup="true" data-title="{{__('Approve Sale')}}"
                                                            data-toggle="tooltip"
                                                            data-original-title="{{__('Appove')}}">
                                                            <i class="fas fa-check" style="padding-top: 5px;"></i>
                                                        </a>
                                                        @endcan

                                                        {{-- @can('delete sale')
                                                                        <a href="#" class="btn btn-danger btn-action" style="padding-top: 10px;" data-toggle="tooltip" data-original-title="{{__('Delete')}}"
                                                        data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{$sale->id}}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                        </a>
                                                        {!! Form::open(['method' => 'DELETE', 'route' =>
                                                        ['sale.destroy', $sale->id],'id'=>'delete-form-'.$sale->id]) !!}
                                                        {!! Form::close() !!}
                                                        @endcan --}}
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                @foreach ($installments as $installment)
                                                <tr>
                                                    {{-- <td class="font-style">{{ $sale->sale_id }}</td> --}}
                                                    <td class="font-style">
                                                        {{ Auth::user()->waittingSaleInstallmentNumberFormat($installment->id) }}
                                                    </td>
                                                    <td class="font-style"> Hire Sales Installment </td>
                                                    <td class="font-style">
                                                        {{ Auth::user()->dateFormat($installment->date) }}</td>
                                                    <td class="font-style"> {{ $installment->customer->name ?? '' }}
                                                    </td>

                                                    <td class="font-style text-center">
                                                        {{ Auth::user()->otherUserName($installment->created_by) }}</td>
                                                    @if(Gate::check('approve authorization'))
                                                    <td class="action text-right">
                                                        @can('approve authorization')
                                                        <a href="#" data-size="xl"
                                                            class="btn btn-primary btn-action mr-1"
                                                            style="padding-top: 10px;"
                                                            data-url="{{ route('waitting_installment.approve',$installment->id) }}"
                                                            data-ajax-popup="true"
                                                            data-title="{{__('Approve Hire Sales Installment')}}"
                                                            data-toggle="tooltip"
                                                            data-original-title="{{__('Appove')}}">
                                                            <i class="fas fa-check" style="padding-top: 5px;"></i>
                                                        </a>
                                                        @endcan
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                @foreach ($loanInstallments as $loanInstallment)
                                                <tr>
                                                    {{-- <td class="font-style">{{ $sale->sale_id }}</td> --}}
                                                    <td class="font-style">
                                                        {{ Auth::user()->waittingLoanInstallmentNumberFormat($loanInstallment->id) }}
                                                    </td>
                                                    <td class="font-style"> Loan Installment </td>
                                                    <td class="font-style">
                                                        {{ Auth::user()->dateFormat($loanInstallment->date) }}</td>
                                                    <td class="font-style"> {{ $loanInstallment->customer->name ?? '' }}
                                                    </td>

                                                    <td class="font-style text-center">
                                                        {{ Auth::user()->otherUserName($loanInstallment->created_by) }}
                                                    </td>
                                                    @if(Gate::check('approve authorization'))
                                                    <td class="action text-right">
                                                        @can('approve authorization')
                                                        <a href="#" data-size="xl"
                                                            class="btn btn-primary btn-action mr-1"
                                                            style="padding-top: 10px;"
                                                            data-url="{{ route('waitting_loan_installment.approve',$loanInstallment->id) }}"
                                                            data-ajax-popup="true"
                                                            data-title="{{__('Approve Loan Installment')}}"
                                                            data-toggle="tooltip"
                                                            data-original-title="{{__('Appove')}}">
                                                            <i class="fas fa-check" style="padding-top: 5px;"></i>
                                                        </a>
                                                        @endcan
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                @foreach ($pawningInstallments as $pawningInstallment)
                                                <tr>
                                                    {{-- <td class="font-style">{{ $sale->sale_id }}</td> --}}
                                                    <td class="font-style">
                                                        {{ Auth::user()->waittingPawningInstallmentNumberFormat($pawningInstallment->id) }}
                                                    </td>
                                                    <td class="font-style"> Pawning Installment </td>
                                                    <td class="font-style">
                                                        {{ Auth::user()->dateFormat($pawningInstallment->paid_date) }}
                                                    </td>
                                                    <td class="font-style">
                                                        {{ $pawningInstallment->customer->name ?? '' }} </td>

                                                    <td class="font-style text-center">
                                                        {{ Auth::user()->otherUserName($pawningInstallment->created_by) }}
                                                    </td>
                                                    @if(Gate::check('approve authorization'))
                                                    <td class="action text-right">
                                                        @can('approve authorization')
                                                        <a href="#" data-size="xl"
                                                            class="btn btn-primary btn-action mr-1"
                                                            style="padding-top: 10px;"
                                                            data-url="{{ route('waitting_pawning_installment.approve',$pawningInstallment->id) }}"
                                                            data-ajax-popup="true"
                                                            data-title="{{__('Approve Pawning Installment')}}"
                                                            data-toggle="tooltip"
                                                            data-original-title="{{__('Appove')}}">
                                                            <i class="fas fa-check" style="padding-top: 5px;"></i>
                                                        </a>
                                                        @endcan
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
        $('#requests_table').DataTable();
    });

</script>
@endsection
{{-- <script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
    // var sales = <php echo json_encode($sales) ?>;
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        // console.log(sales);
        if (localStorage.getItem("sale_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("sale_page_no"));
        }
        var salesTable = $('#sales_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        salesTable.page(pageNo).draw(false);

        $('#sales_table').on('page.dt', function () {
            var pageInfo = salesTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("sale_page_no", pageNo);
        });

        $('body').on('click', '.edit', function () {
            var value = $(this).val();
            window.location.href = "{{ url('sale/view') }}/" + value;
        });

        $('body').on('click', '.view', function () {
            var value = $(this).val();
            window.location.href = "{{ url('sale/view') }}/" + value;
        });

        $('body').on('click', '.invoice-view', function () {
            var invoiceId = $(this).val();
            window.location.href = "{{ url('invoice') }}/" + invoiceId;
        });
    });

</script> --}}
