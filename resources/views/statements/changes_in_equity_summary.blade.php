@extends('layouts.admin')
@section('page-title')
{{__('Changes in Equity Statement')}}
@endsection
@push('script-page')
<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    var year = '{{$currentYear}}';

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

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Changes in Equity Statement')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Accounting System')}}</div>
        <div class="breadcrumb-item">{{__('Financial Statements')}}</div>
        <div class="breadcrumb-item">{{__('Changes in Equity Statement')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Changes in Equity Statement') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Accounting System')}}</div>
                                <div class="breadcrumb-item">{{__('Financial Statements')}}</div>
                                <div class="breadcrumb-item">{{__('Changes in Equity Statement')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <div class="card-body">
                        {{ Form::open(array('route' => array('changes_in_equity_summary.index'),'method' => 'GET')) }}
                        <div class="row">
                            <div class="col">
                                <h4 class="h4 mb-0">{{__('Filter')}}</h4>
                            </div>
                            <div class="col-md-2">
                                {{ Form::label('year', __('Financial Year')) }}
                                {{-- {{ Form::select('year',$yearList,isset($_GET['year'])?$_GET['year']:'', array('class' => 'form-control font-style selectric')) }}
                                --}}
                                <select name="year" id="year" class="form-control font-style selectric">
                                    @foreach ( $yearList as $year)
                                    <option @if(isset($_GET['year'])) @if ($_GET['year']==$year) selected @endif @endif
                                        value="{{ $year }}">{{ $year }} / {{ $year + 1 }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto apply-btn">
                                {{Form::submit(__('Apply'),array('class'=>'btn btn-outline-primary btn-sm'))}}
                                <a href="{{route('changes_in_equity_summary.index')}}"
                                    class="btn btn-outline-danger btn-sm">{{__('Reset')}}</a>
                                <a href="#" onclick="saveAsPDF();"
                                    class="btn btn-icon icon-left btn-outline-info pdf-btn btn-sm"
                                    id="download-buttons">
                                    {{__('Download')}}
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
        <div id="printableArea">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-wrap">
                            <div class="card-body">
                                <input type="hidden"
                                    value="{{__('Changes in Equity Statement').' '.'Report of'.' '.$filter['startDateRange'].' to '.$filter['endDateRange']}}"
                                    id="filename">
                                <div class="row">
                                    <div class="col">
                                        <h5>{{__('Changes in Equity Statement')}}</h5>
                                        <h6>{{__('of ')}} {{ config('app.name') }}</h6>
                                        <h6>{{__('for the period 01st')}}
                                            {{$filter['startDateRange'].' to 31st '.$filter['endDateRange']}}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12" id="chart-container">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-body p-0">
                                <div id="table-1_wrapper"
                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-flush border font-style"
                                                    id="dataTable-manual">
                                                    <thead>
                                                        <div
                                                            style="margin: 0 auto; text-align:center; font-size:12pt; font-weight:bold">
                                                            <span style="float:right;">LKR</span>
                                                        </div>
                                                        <tr style="background-color: coral">
                                                            <th width="10%"><b>{{__('PARTICULARS')}}</b></th>
                                                            <th width="5%" style="font-weight: bold; text-align:center">
                                                                {{__('Stated Capital')}}</th>
                                                            <th width="5%" style="font-weight: bold; text-align:center">
                                                                {{__('Retained Earnings')}}</th>
                                                            <th width="5%" style="font-weight: bold; text-align:center">
                                                                {{__('Revaluation')}}</th>
                                                            <th width="5%" style="font-weight: bold; text-align:center">
                                                                {{__('Total')}}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="10%" style="font-weight: bold">
                                                                <b>{{__('Balance as at')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="5%" style="text-align: center">
                                                                {{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="10%">{{__('Changes in Accounting Policy')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="5%" style="text-align: center">
                                                                {{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="10%">{{__('Prior Period Error Correction')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="5%" style="text-align: center">
                                                                {{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        <br><br>
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="10%">{{__('Add: New Paid in Capital')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="5%" style="text-align: center">
                                                                {{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="10%">{{__('Net Profit for the Year')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="5%" style="text-align: center">
                                                                {{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="10%">{{__('Revaluation Gain')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="5%" style="text-align: center">
                                                                {{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="10%">{{__('Deduct: Owner\'s Withdrawal')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="5%" style="text-align: center">
                                                                {{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="10%" style="font-weight: bold">
                                                                <b>{{__('Balance as at ')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="5%" style="text-align: center">
                                                                {{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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
    </div>
</section>

@endsection
