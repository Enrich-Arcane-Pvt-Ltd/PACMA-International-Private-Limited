@extends('layouts.admin')
@section('page-title')
{{__('Cash Flow Statement')}}
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
            <h1>{{__('Cash Flow Statement')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Accounting System')}}</div>
        <div class="breadcrumb-item">{{__('Financial Statements')}}</div>
        <div class="breadcrumb-item">{{__('Cash Flow Statement')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Cash Flow Statement') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Accounting System')}}</div>
                                <div class="breadcrumb-item">{{__('Financial Statements')}}</div>
                                <div class="breadcrumb-item">{{__('Cash Flow Statement')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <div class="card-body">
                        {{ Form::open(array('route' => array('cash_flow_summary.index'),'method' => 'GET')) }}
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
                                <a href="{{route('cash_flow_summary.index')}}"
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
                                    value="{{__('Cash Flow Statement').' '.'Report of'.' '.$filter['startDateRange'].' to '.$filter['endDateRange']}}"
                                    id="filename">
                                <div class="row">
                                    <div class="col">
                                        <h5>{{__('Cash Flow Statement')}}</h5>
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
                                                            MONTH
                                                            <span style="float:right">LKR&nbsp;&nbsp;</span>
                                                        </div>
                                                        <tr style="background-color: coral">
                                                            <th width="25%"><b>{{__('PARTICULARS')}}</b></th>
                                                            @foreach($month as $m)
                                                            <th width="15%">{{$m}}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr style="background-color:rgb(248, 212, 167)">
                                                            <td colspan="20" style="font-weight: bold">
                                                                <b>{{__('OPERATING CASH FLOW')}}</b></td>
                                                        </tr>
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="30%">{{__('Net Earnings')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="15%">{{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="30%">{{__('(+) Depreciation & Amortization')}}
                                                            </td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="15%">{{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="30%">{{__('(-) Changes in Working Capital')}}
                                                            </td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="15%">{{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr style="background-color: rgb(223, 223, 223)">
                                                            <td width="30%" style="font-weight: bold">
                                                                {{__('Cash from Operations')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="15%">{{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        <tr style="background-color:rgb(248, 212, 167)">
                                                            <td colspan="20" style="font-weight: bold">
                                                                <b>{{__('INVESTING CASH FLOW')}}</b></td>
                                                        </tr>
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="30%">{{__('Investment in Property & Equipment')}}
                                                            </td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="15%">{{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr style="background-color: rgb(223, 223, 223)">
                                                            <td width="30%" style="font-weight: bold">
                                                                {{__('Cash from Investing')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="15%">{{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        <tr style="background-color:rgb(248, 212, 167)">
                                                            <td colspan="20" style="font-weight: bold">
                                                                <b>{{__('FINANCING CASH FLOW')}}</b></td>
                                                        </tr>
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="30%">{{__('Issuance(Repayment) of Debt')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="15%">{{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="30%">{{__('Issuance(Repayment) of Equity')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="15%">{{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr style="background-color: rgb(223, 223, 223)">
                                                            <td width="30%" style="font-weight: bold">
                                                                {{__('Cash from Financing')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="15%">{{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="30%">{{__('Net Increase/(Decrease) in Cash')}}
                                                            </td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="15%">{{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr>
                                                            <td width="30%">{{__('Opening Cash Balance')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="15%">{{ number_format($amount, 2) }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                        @if(!empty($revenueIncomeArray))
                                                        @foreach($revenueIncomeArray as $i=>$revenue)
                                                        <tr style="background-color: rgb(223, 223, 223)">
                                                            <td width="30%" style="font-weight: bold">
                                                                {{__('Closing Cash Balance')}}</td>
                                                            @foreach($revenue['amount'] as $j=>$amount)
                                                            <td width="15%">{{ number_format($amount, 2) }}</td>
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
