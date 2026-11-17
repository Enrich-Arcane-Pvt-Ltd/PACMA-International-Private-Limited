@extends('layouts.admin')
@section('page-title')
{{__('Profit & Loss Statement')}}
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

    // var data = <?php echo json_encode($revenueIncomeArray); ?>;
    // $(document).ready(function(){
    //     console.log(data);
    // });

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Profit & Loss Statement')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Accounting System')}}</div>
        <div class="breadcrumb-item">{{__('Financial Statements')}}</div>
        <div class="breadcrumb-item">{{__('Profit & Loss Statement')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Profit & Loss Statement') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Accounting System')}}</div>
                                <div class="breadcrumb-item">{{__('Financial Statements')}}</div>
                                <div class="breadcrumb-item">{{__('Profit & Loss Statement')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <div class="card-body">
                        {{ Form::open(array('route' => array('profit_loss_summary.index'),'method' => 'GET')) }}
                        <div class="row">
                            <div class="col">
                                <h4 class="h4 mb-0">{{__('Filter')}}</h4>
                            </div>
                            {{-- <div class="col">
                                    <h5>{{__('Profit & Loss Statement')}}</h5>
                            <h6>{{__('of ')}} {{ config('app.name') }}</h6>
                            <h6>{{__('for the period 01st')}}
                                {{$filter['startDateRange'].' to 31st '.$filter['endDateRange']}}</h6>
                        </div> --}}
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
                            <a href="{{route('profit_loss_summary.index')}}"
                                class="btn btn-outline-danger btn-sm">{{__('Reset')}}</a>
                            <a href="#" onclick="saveAsPDF();"
                                class="btn btn-icon icon-left btn-outline-info pdf-btn btn-sm" id="download-buttons">
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
                                value="{{__('Profit & Loss Statement').' '.'Report of'.' '.$filter['startDateRange'].' to '.$filter['endDateRange']}}"
                                id="filename">
                            <div class="row">
                                <div class="col">
                                    <h5>{{__('Profit & Loss Statement')}}</h5>
                                    <h6>{{__('of ')}} {{ config('app.name') }}</h6>
                                    <h6>{{__('for the period 01st')}}
                                        {{$filter['startDateRange'].' to 31st '.$filter['endDateRange']}}</h6>
                                </div>
                                {{-- <div class="col">
                                            {{__('Duration')}} : <h6>
                                    {{$filter['startDateRange'].' to '.$filter['endDateRange']}}</h6>
                            </div> --}}
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
                        <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-flush border font-style" id="dataTable-manual">
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
                                                @if(!empty($revenueIncomeArray))
                                                @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr>
                                                    <td width="30%">{{__('Revenue')}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                    <td width="15%">{{ number_format($amount, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif
                                                @if(!empty($revenueIncomeArray))
                                                @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr>
                                                    <td width="30%">{{__('Returns,Refunds & Discounts')}}</td>
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
                                                        {{__('Total Net Revenue')}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                    <td width="15%">{{ number_format($amount, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif
                                                @if(!empty($revenueIncomeArray))
                                                @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr>
                                                    <td width="30%">{{__('COGS')}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                    <td width="15%">{{ number_format($amount, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif
                                                @if(!empty($revenueIncomeArray))
                                                @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr style="background-color: rgb(223, 223, 223">
                                                    <td width="30%" style="font-weight: bold">{{__('Gross Profit')}}
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
                                                    <td width="30%">{{__('Interest Income')}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                    <td width="15%">{{ number_format($amount, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif
                                                @if(!empty($revenueIncomeArray))
                                                @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr>
                                                    <td width="30%">{{__('Other Income')}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                    <td width="15%">{{ number_format($amount, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif
                                                <tr style="background-color:rgb(248, 212, 167)">
                                                    <td colspan="20" style="font-weight: bold"><b>{{__('EXPENSES')}}</b>
                                                    </td>
                                                </tr>
                                                @if(!empty($revenueIncomeArray))
                                                @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr>
                                                    <td width="30%">{{__('Interest Expenses')}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                    <td width="15%">{{ number_format($amount, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif
                                                @if(!empty($revenueIncomeArray))
                                                @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr>
                                                    <td width="30%">{{__('Administration & Establishment Expenses')}}
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
                                                    <td width="30%">{{__('Marketing Expenses')}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                    <td width="15%">{{ number_format($amount, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif
                                                @if(!empty($revenueIncomeArray))
                                                @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr>
                                                    <td width="30%">{{__('Finance Expenses')}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                    <td width="15%">{{ number_format($amount, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif
                                                @if(!empty($revenueIncomeArray))
                                                @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr>
                                                    <td width="30%">{{__('Management Fees')}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                    <td width="15%">{{ number_format($amount, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif
                                                <div class="table-responsive-xl">
                                                    @if(!empty($revenueIncomeArray))
                                                    @foreach($revenueIncomeArray as $i=>$revenue)
                                                    <tr style="background-color: rgb(223, 223, 223">
                                                        <td width="30%"><b>{{__('Total Expenses')}}</b></td>
                                                        @foreach($revenue['amount'] as $j=>$amount)
                                                        <td width="15%">{{ number_format($amount, 2) }}</td>
                                                        @endforeach
                                                    </tr>
                                                    @endforeach
                                                    @endif
                                                </div>
                                                @if(!empty($revenueIncomeArray))
                                                @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr style="background-color: rgb(223, 223, 223">
                                                    <td width="30%" style="font-weight: bold">
                                                        {{__('Earnings before Tax')}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                    <td width="15%">{{ number_format($amount, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif
                                                @if(!empty($revenueIncomeArray))
                                                @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr>
                                                    <td width="30%">{{__('Taxation')}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                    <td width="15%">{{ number_format($amount, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif
                                                @if(!empty($revenueIncomeArray))
                                                @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr style="background-color: rgb(223, 223, 223">
                                                    <td width="30%" style="font-weight: bold">
                                                        {{__('Net Profit after Tax')}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                    <td width="15%">{{ number_format($amount, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif

                                                {{-- <tr>
                                                            <td width="50%"><h7>{{__('Returns, Refunds & Discounts')}}
                                                </h7>
                                                </td>
                                                </tr>
                                                @if(!empty($invoiceIncomeArray))
                                                @foreach($invoiceIncomeArray as $i=>$invoice)
                                                <tr>
                                                    {{-- <td>{{$invoice['category']}}</td> --}}
                                                    {{-- @foreach($invoice['amount'] as $j=>$amount)
                                                                        <td width="15%">{{\Auth::user()->priceFormat($amount)}}
                                                    </td>
                                                    @endforeach --}}
                                                    {{-- </tr>
                                                            @endforeach --}}
                                                    {{-- @endif --}}
                                                    {{-- <div class="row">
                                                            <div class="col-sm-12"> --}}
                                                    {{-- <table class="table table-flush border" id="dataTable-manual"> --}}
                                                    {{-- <tbody> --}}
                                                    {{-- <tr>
                                                                        <td colspan="13"><b><h6>{{__('Total Income =  Revenue + Invoice ')}}
                                                    </h6></b></td>
                                                </tr> --}}
                                                {{-- <tr style="font-weight: bold">
                                                                        <td width="30%">{{__('Total Net Revenue')}}
                                                </td>
                                                @foreach($totalIncome as $totalIncome)
                                                <td width="15%">{{\Auth::user()->priceFormat($income)}}</td>
                                                @endforeach --}}
                                                {{-- </tr> --}}
                                                {{-- </tbody> --}}
                                                {{-- </table> --}}
                                                {{-- </div>
                                                        </div> --}}
                                                {{-- </tbody> --}}
                                                {{-- </table> --}}
                                                {{-- </div>
                                            </div> --}}


                                                {{-- <div class="row">
                                                <div class="col-sm-12"> --}}

                                                {{-- <table class="table table-flush border font-style" id="dataTable-manual"> --}}
                                                {{-- <thead class="thead-light"> --}}
                                                {{-- <tr>
                                                            <th width="25%">{{__('Particulars')}}</th>
                                                @foreach($month as $m)
                                                <th width="15%">{{$m}}</th>
                                                @endforeach
                                                </tr> --}}
                                                {{-- </thead> --}}
                                                {{-- <tbody> --}}
                                                {{-- <tr>
                                                            <td colspan="13"><h7>{{__('COGS')}}</h7>
                                                </td>
                                                </tr>
                                                @if(!empty($expenseArray))
                                                @foreach($expenseArray as $i=>$expense)
                                                <tr>
                                                    <td>{{$expense['category']}}</td>
                                                    @foreach($expense['amount'] as $j=>$amount)
                                                    <td width="15%">{{\Auth::user()->priceFormat($amount)}}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                                @endif --}}
                                                {{-- <div class="row">
                                                            <div class="col-sm-12"> --}}
                                                {{-- <table class="table table-flush border" id="dataTable-manual"> --}}
                                                {{-- <tbody> --}}
                                                {{-- <tr>
                                                                        <td colspan="13"><b><h6>{{__('Total Expense =  Payment + Bill ')}}
                                                </h6></b></td>
                                                </tr> --}}
                                                {{-- <tr style="font-weight: bold">
                                                                        <td width="25%">{{__('Gross Profit')}}</td>
                                                @foreach($totalExpense as $expense)
                                                <td width="15%">{{\Auth::user()->priceFormat($expense)}}</td>
                                                @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <tbody>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <tr style="font-weight: bold">
                                                <td width="25%">{{__('Interest Income')}}</td>
                                                @foreach($totalExpense as $expense)
                                                <td width="15%">{{\Auth::user()->priceFormat($expense)}}</td>
                                                @endforeach
                                            </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                        <tbody>
                            <tr style="font-weight: bold">
                                <td width="25%">{{__('Other Income')}}</td>
                                @foreach($totalExpense as $expense)
                                <td width="15%">{{\Auth::user()->priceFormat($expense)}}</td>
                                @endforeach
                            </tr>
                        </tbody>
                        </table>
                    </div>
                </div> --}}

                {{-- <div class="row">
                                                <div class="col-sm-12">
                                                    {{-- <h4>{{__('Expense')}}</h4> --}}
                {{-- <table class="table table-flush border font-style" id="dataTable-manual">
                                                        <thead class="thead-light"> --}}
                {{-- <tr>
                                                            <th width="25%">{{__('Particulars')}}</th>
                @foreach($month as $m)
                <th width="15%">{{$m}}</th>
                @endforeach
                </tr> --}}
                {{-- </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td width="30%"><b><u><h7>{{__('Expenses')}}</h7></u></b>
                </td>
                </tr>
                @if(!empty($expenseArray))
                @foreach($expenseArray as $i=>$expense)
                <tr>
                    <td>{{$expense['category']}}</td>
                    @foreach($expense['amount'] as $j=>$amount)
                    <td width="15%">{{\Auth::user()->priceFormat($amount)}}</td>
                    @endforeach
                </tr>
                @endforeach
                @endif
                <tr>
                    <td width="30%">
                        <h7>{{__('Interest Expenses')}}</h7>
                    </td>
                </tr>
                @if(!empty($billExpenseArray))
                @foreach($billExpenseArray as $i=>$bill)
                <tr>
                    <td>{{$bill['category']}}</td>
                    @foreach($bill['amount'] as $j=>$amount)
                    <td width="15%">{{\Auth::user()->priceFormat($amount)}}</td>
                    @endforeach
                </tr>
                @endforeach
                @endif
                <tr>
                    <td width="30%">
                        <h7>{{__('Administration & Establishment Expenses')}}</h7>
                    </td>
                </tr>
                @if(!empty($billExpenseArray))
                @foreach($billExpenseArray as $i=>$bill)
                <tr>
                    <td>{{$bill['category']}}</td>
                    @foreach($bill['amount'] as $j=>$amount)
                    <td width="15%">{{\Auth::user()->priceFormat($amount)}}</td>
                    @endforeach
                </tr>
                @endforeach
                @endif
                <tr>
                    <td width="30%">
                        <h7>{{__('Marketing Expenses')}}</h7>
                    </td>
                </tr>
                @if(!empty($billExpenseArray))
                @foreach($billExpenseArray as $i=>$bill)
                <tr>
                    <td>{{$bill['category']}}</td>
                    @foreach($bill['amount'] as $j=>$amount)
                    <td width="15%">{{\Auth::user()->priceFormat($amount)}}</td>
                    @endforeach
                </tr>
                @endforeach
                @endif
                <tr>
                    <td width="30%">
                        <h7>{{__('Finance Expenses')}}</h7>
                    </td>
                </tr>
                @if(!empty($billExpenseArray))
                @foreach($billExpenseArray as $i=>$bill)
                <tr>
                    <td>{{$bill['category']}}</td>
                    @foreach($bill['amount'] as $j=>$amount)
                    <td width="15%">{{\Auth::user()->priceFormat($amount)}}</td>
                    @endforeach
                </tr>
                @endforeach
                @endif
                <tr>
                    <td width="30%">
                        <h7>{{__('Management Fees')}}</h7>
                    </td>
                </tr>
                @if(!empty($billExpenseArray))
                @foreach($billExpenseArray as $i=>$bill)
                <tr>
                    <td>{{$bill['category']}}</td>
                    @foreach($bill['amount'] as $j=>$amount)
                    <td width="15%">{{\Auth::user()->priceFormat($amount)}}</td>
                    @endforeach
                </tr>
                @endforeach
                @endif

                <div class="row">
                    {{-- <div class="col-sm-12">
                                                                <table class="table table-flush border" id="dataTable-manual">
                                                                    <tbody> --}}
                    {{-- <tr>
                                                                        <td colspan="13"><b><h6>{{__('Total Expense =  Payment + Bill ')}}
                    </h6></b></td>
                    </tr> --}}
                    {{-- <tr style="font-weight: bold">
                                                                        <td>{{__('Total Expenses')}}</td>
                    @foreach($totalExpense as $expense)
                    <td width="15%">{{\Auth::user()->priceFormat($expense)}}</td>
                    @endforeach
                    </tr>
                    </tbody> --}}
                    {{-- </table>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <table class="table table-flush border" id="dataTable-manual" style="font-weight: bold">
                                                                        <td>{{__('Earnings before Interest')}}</td>
                    @foreach($totalExpense as $expense)
                    <td width="15%">{{\Auth::user()->priceFormat($expense)}}</td>
                    @endforeach --}}
                    {{-- </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div> --}}
                    {{-- </div> --}}
                    {{-- <div class="row">
                                                            <div class="col-sm-12"> --}}
                    {{-- <table class="table table-flush border" id="dataTable-manual">
                                                                        <td>{{__('Interest Expenses')}}</td>
                    @foreach($totalExpense as $expense)
                    <td width="15%">{{\Auth::user()->priceFormat($expense)}}</td>
                    @endforeach
                    </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-flush border" id="dataTable-manual" style="font-weight: bold">
                        <tbody>
                            {{-- <tr>
                                                                        <td colspan="13"><b><h6>{{__('Net Profit = Total Income - Total Expense ')}}
                            </h6></b></td>
                            </tr> --}}
                            {{-- <tr>
                                                                        <td width="15%">{{__('Net Profit')}}</td>
                            @foreach($netProfitArray as $i=>$profit)
                            <td width="15%"> {{\Auth::user()->priceFormat($profit)}}</td>
                            @endforeach
                            </tr> --}}
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
