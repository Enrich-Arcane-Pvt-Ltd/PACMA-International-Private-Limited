@extends('layouts.admin')
@section('page-title')
{{__('Daily Collections')}}
@endsection
@push('css-page')
<link href="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet"
    type="text/css" />
@endpush
@push('script-page')


<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    var filename = $('#filename').val();

    function saveAsPDF() {
        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.3,
            filename: filename,
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 4,
                dpi: 72,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'A2'
            }
        };
        html2pdf().set(opt).from(element).save();

    }


    $(document).ready(function () {

        $('#report-dataTable').DataTable();
    });

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Daily Collections')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Loans')}}</div>
        <div class="breadcrumb-item">{{__('Reports')}}</div>
        <div class="breadcrumb-item">{{__('Daily Collections')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Daily Collections') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Loans')}}</div>
                                {{-- <div class="breadcrumb-item">{{__('Reports')}}
                            </div> --}}
                            <div class="breadcrumb-item">{{__('Daily Collections')}}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card br-4 cus_show">
                <form method="post" action="{{ route('daily_collection.search') }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h4 class="h4 mb-0">Filter</h4>
                            </div>
                            <div class="col-md-4">
                            </div>
                            <div class="col-md-2">
                                <label for="end_month">Date</label>
                                <input class="form-control font-style" name="date" type="date" value="{{ $date ?? '' }}"
                                    id="date" required>
                            </div>
                            <div class="col-md-2">
                                <label for="officer_id">Officer</label>
                                {{ Form::select('officer_id', $users, $officerId ?? '', array('id' => 'officer_id','class' => 'form-control font-style', 'required'=>'required')) }}

                            </div>
                            <div class="col-md-2">
                                <label for="center_id">Center</label>
                                {{ Form::select('center_id', $centers, $centerId ?? '', array('id' => 'center_id','class' => 'form-control font-style', 'required'=>'required')) }}

                            </div>

                            <div class="col-auto apply-btn">
                                <input class="btn font-style btn-outline-primary" type="submit" value="Search">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="printableArea">
        {{-- <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-wrap">
                                <div class="card-body">
                                    <div class="row">
                                            @if($showTable == 'Yes')
                                                <div class="col">
                                                    <h6>{{__('Total due payment')}} : {{number_format($duetotal,2)}}
        </h6>
    </div>

    <div class="col">

    </div>

    <div class="col">

    </div>

    <div class="col">
        <h6>{{__('Duration')}} : {{ $start_date . " TO " .$end_date }} </h6>
    </div>
    @endif

    </div>
    </div>
    </div>
    </div>
    </div>
    </div> --}}
    <div class="row">
        <div class="col-12" id="bill-container">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between w-100">
                        <table class="table table-flush" id="report-dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{__('Loan Id')}}</th>
                                    <th> {{__('Customer No')}}</th>
                                    <th> {{__('Customer Name')}}</th>
                                    <th> {{__('Center')}}</th>
                                    <th> {{__('Group')}}</th>
                                    <th> {{__('Loan Amount')}}</th>
                                    <th> {{__('Outstanding Balance')}}</th>
                                    <th> {{__('Time Period')}}</th>
                                    <th> {{__('Collection Period')}}</th>
                                    <th> {{__('NIC')}}</th>
                                    @if(Gate::check('check loan') || Gate::check('edit loan') || Gate::check('delete
                                    loan') || Gate::check('reject loan'))
                                    <th class="text-right">{{__('Action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if($showTable == 'Yes')
                                @foreach($loans as $query)
                                <tr>
                                    <td class="font-style">
                                        {{-- <button type="button" class="btn btn-outline-primary view" value="" style="border-radius: 0.25rem;"></button> --}}
                                        <a class="btn btn-outline-primary view"
                                            href="{{route('loan.daily_collection_view', [$query->id, $loanArray])}}"
                                            style="text-decoration: none; border-radius: 0.25rem;">
                                            {{ \Auth::user()->loanNumberFormat($query->id) }} </a>
                                    </td>
                                    <td class="font-style">
                                        {{ \Auth::user()->customerNumberFormat($query->customer->id)}} </td>
                                    <td class="font-style"> {{ $query->customer->name ?? '' }} </td>
                                    <td class="font-style"> {{ $query->center->name ?? '' }} </td>
                                    <td class="font-style"> {{ $query->group->name ?? '' }} </td>
                                    <td class="font-style"> {{ \Auth::user()->priceFormat($query->loan_amount) }} </td>
                                    <td class="font-style"> {{ \Auth::user()->priceFormat($query->current_due_amount) }}
                                    </td>
                                    <td class="font-style"> {{ $query->time_period }} {{ __('Months') }} </td>
                                    <td class="font-style">
                                        @if ($query->time_range == 1)
                                        Daily
                                        @elseif ($query->time_range == 2)
                                        Weekly
                                        @elseif ($query->time_range == 3)
                                        Monthly
                                        @endif
                                    </td>
                                    <td class="font-style"> {{ $query->customer->nic_no ?? '' }} </td>
                                    @if(Gate::check('view loan') || Gate::check('delete loan'))
                                    <td class="action text-right">
                                        @can('view loan')
                                        {{-- <button type="button" class="btn btn-info btn-action mr-1 view" title="View" value="{{ $query->id }}"><i
                                            class="fas fa-eye"></i></button> --}}
                                        <a href="{{route('loan.daily_collection_view', [$query->id, $loanArray])}}"
                                            data-toggle="tooltip" data-original-title="{{__('View')}}"
                                            class="btn btn-info btn-action"
                                            style="padding-top: 5px; border-radius: 0.25rem;">
                                            <i class="fas fa-eye" style="padding-top: 5px;"></i>
                                        </a>
                                        @endcan

                                        {{-- @can('delete loan')
                                                                    <a href="#" class="btn btn-danger btn-action" style="padding-top: 10px; border-radius: 0.25rem;" data-toggle="tooltip" data-original-title="{{__('Delete')}}"
                                        data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                        data-confirm-yes="document.getElementById('delete-form-{{$granted->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                        </a>
                                        {!! Form::open(['method' => 'GET', 'route' => ['loan.destroy',
                                        $granted->id],'id'=>'delete-form-'.$granted->id]) !!}
                                        {!! Form::close() !!}
                                        @endcan --}}
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
    </div>
</section>
@endsection
