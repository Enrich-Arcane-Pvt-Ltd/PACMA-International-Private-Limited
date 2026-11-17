@extends('layouts.admin')
@section('page-title')
    {{ __('Receipt Summary Report') }}
@endsection
@push('script-page')
    <style>
        table tfoot {
            display: table-row-group;
        }
    </style>

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


        $(document).ready(function() {
            var filename = 'Receipt Summary Report';

            if ($('#duration').val() != undefined) {
                var duration = $('#duration').val();
            } else {
                var duration = "";
            }

            if ($('#category').val() == 'advance') {
                var category = ' Of Advance Payments';
            } else if ($('#category').val() == 'direct') {
                var category = ' Of Cash Sales';
            } else if ($('#category').val() == 'hire') {
                var category = ' Of Hire Sales';
            } else if ($('#category').val() == 'loan') {
                var category = ' Of Loans';
            } else if ($('#category').val() == 'pawning') {
                var category = ' Of Pawning';
            } else {
                var category = "";
            }

            $('#report-dataTable').DataTable({
                dom: 'lBfrtip',
                order: [
                    [4, "asc"]
                ],
                // "bSort" : false,
                buttons: [{
                        extend: 'excel',
                        title: filename + category + duration,
                        footer: true
                    },
                    {
                        extend: 'pdf',
                        title: filename + category + duration,
                        footer: true
                    }, {
                        extend: 'print',
                        title: function() {
                            return "<h4>" + filename + category + duration + "</h4>";
                        },
                        footer: true
                    }, {
                        extend: 'csv',
                        title: filename + category + duration,
                        footer: true
                    }
                ]
            });
        });
    </script>
@endpush
@section('content')
    <section class="section">
        {{-- <div class="section-header">
            <h1>{{__('Receipt Summary Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Report')}}</div>
        <div class="breadcrumb-item">{{__('Receipt Summary Report')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Receipt Summary Report') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Report') }}</div>
                                    <div class="breadcrumb-item">{{ __('Receipt Summary Report') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('receiptsearch') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4 class="h4 mb-0">Filter</h4>
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="start_month">Start Date</label>
                                        <input class="form-control font-style" name="start_date" type="date"
                                            value="{{ $start_date ?? '' }}" id="start_date" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_month">End Date</label>
                                        <input class="form-control font-style" name="end_date" type="date"
                                            value="{{ $end_date ?? '' }}" id="end_date" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="category">Category</label>
                                        <select name="category" id="category" class="form-control font-style">
                                            <option value="">-- All --</option>
                                            <option @if ($category == 'advance') selected @endif value="advance">
                                                Advance
                                                Payments</option>
                                            <option @if ($category == 'direct') selected @endif value="direct">Cash
                                                Sales
                                            </option>
                                            <option @if ($category == 'hire') selected @endif value="hire">Hire
                                                Sales</option>
                                            <option @if ($category == 'loan') selected @endif value="loan">Loans
                                            </option>
                                            <option @if ($category == 'pawning') selected @endif value="pawning">
                                                Pawning
                                            </option>
                                            <option @if ($category == 'custom') selected @endif value="custom">Custom
                                                Receipts
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" id="hire_sub_category_div">
                                        <label for="hire_sub_category">Sub Category</label>
                                        <select name="hire_sub_category" id="hire_sub_category"
                                            class="form-control font-style">
                                            <option value="">-- All --</option>
                                            <option @if (isset($hireSubCategory) && $hireSubCategory == 'INR') selected @endif value="INR">
                                                Instrallments</option>
                                            <option @if (isset($hireSubCategory) && $hireSubCategory == 'DOC') selected @endif value="DOC">DOC
                                                Charges</option>
                                            <option @if (isset($hireSubCategory) && $hireSubCategory == 'DPR') selected @endif value="DPR">Down
                                                Payments</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" id="loan_sub_category_div">
                                        <label for="loan_sub_category">Sub Category</label>
                                        <select name="loan_sub_category" id="loan_sub_category"
                                            class="form-control font-style">
                                            <option value="">-- All --</option>
                                            <option @if (isset($loanSubCategory) && $loanSubCategory == 'LN') selected @endif value="LN">
                                                Instrallments</option>
                                            <option @if (isset($loanSubCategory) && $loanSubCategory == 'DOC') selected @endif value="DOC">DOC
                                                Charges</option>
                                        </select>
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
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-wrap">
                                <div class="card-body">
                                    <div class="row">
                                        @if ($showTable == 'Yes')
                                            {{-- <div class="col">
                                                    <h6>{{__('Total due payment')}} : {{number_format($duetotal,2)}}
                                    </h6>
                                </div> --}}

                                            <div class="col">

                                            </div>

                                            <div class="col">

                                            </div>

                                            <div class="col">
                                                <h6>{{ __('Duration') }} : {{ $start_date . ' TO ' . $end_date }} </h6>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="bill-container">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between w-100">
                                    <table class="table table-flush" id="report-dataTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th> {{ __('Receipt No') }}</th>
                                                <th> {{ __('Customer No') }}</th>
                                                <th> {{ __('Customer Name') }}</th>
                                                <th> {{ __('Category') }}</th>
                                                <th> {{ __('Payment Date') }}</th>
                                                <th> {{ __('Amount') }} ({{ $currencySymbol }})</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($showTable == 'Yes')
                                                <input type="hidden" id="duration"
                                                    value="{{ __(' - From ') . Auth::user()->dateFormat($start_date) . __(' To ') . Auth::user()->dateFormat($end_date) }}">
                                                @php
                                                    $totalAmount = 0;
                                                @endphp
                                                @foreach ($invoiceLists as $invoiceList)
                                                    <tr>
                                                        <td>
                                                            <button type="button"
                                                                class="btn font-style btn-outline-primary view"
                                                                value="{{ $invoiceList->invoice_id }}">
                                                                {{ $invoiceList->invoice->invoice_code ?? '' }} </button>
                                                        </td>
                                                        <td>
                                                            @if ($invoiceList->customer_type == 1)
                                                                {{ Auth::user()->customerNumberFormat($invoiceList->customer_id) }}
                                                            @elseif($invoiceList->customer_type == 2)
                                                                {{ Auth::user()->customerNumberFormat(1) }}
                                                            @endif

                                                        </td>
                                                        <td>
                                                            @if ($invoiceList->customer_type == 1)
                                                                {{ $invoiceList->customer->name ?? '' }}
                                                            @elseif($invoiceList->customer_type == 2)
                                                                {{ $invoiceList->customer_name ?? '' }}
                                                            @endif
                                                        </td>
                                                        <td>Cash Sales</td>
                                                        <td>{{ $invoiceList->date_of_sale }}</td>
                                                        <td class="text-right">
                                                            {{-- @if ($invoiceList->paid_amount != null)
                                                                {{ $invoiceList->paid_amount }}
                                            @else --}}
                                                            {{ number_format($invoiceList->net_amount - $invoiceList->advance_amount, 2) }}
                                                            {{-- @endif --}}
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $totalAmount +=
                                                            $invoiceList->net_amount - $invoiceList->advance_amount;
                                                    @endphp
                                                @endforeach
                                                @foreach ($saleInfoLists as $saleInfoList)
                                                    <tr>
                                                        {{-- <td>{{$saleInfoList->receipt_code}}</td> --}}
                                                        {{-- <td><input class="btn font-style btn-outline-primary orderModel" type="button" data-id="{{$saleInfoList->id}}"
                                        value="{{$saleInfoList->receipt_code}}"></td> --}}
                                                        <td>
                                                            {{-- <a href="#" data-url="{{ route('receiptsearch.receipt',$saleInfoList->id) }}"
                                            data-id="{{$saleInfoList->id}}" data-ajax-popup="true"
                                            data-title="{{__('Reciept')}}" class="btn font-style btn-outline-primary">
                                            {{ $saleInfoList->receipt_code }}
                                            </a> --}}
                                                            <button type="button"
                                                                class="btn font-style btn-outline-primary view"
                                                                value="{{ $saleInfoList->id }}">
                                                                {{ $saleInfoList->receipt_code }}
                                                            </button>
                                                        </td>
                                                        <td>{{ Auth::user()->customerNumberFormat($saleInfoList->customer_id) }}
                                                        </td>
                                                        <td>{{ $saleInfoList->customer->name ?? '' }}</td>
                                                        <td>
                                                            @if ($saleInfoList->description == null)
                                                                @php
                                                                    $receipCode = mb_substr(
                                                                        $saleInfoList->receipt_code,
                                                                        0,
                                                                        3,
                                                                    );
                                                                @endphp
                                                                @if ($receipCode == 'ADR')
                                                                    Advance Payment
                                                                @endif
                                                            @else
                                                                {{ $saleInfoList->description }}
                                                            @endif
                                                        </td>
                                                        {{-- <td>{{$saleInfoList->status}}</td> --}}
                                                        <td>{{ $saleInfoList->issue_date }}</td>
                                                        <td class="text-right">
                                                            {{ number_format($saleInfoList->paid_amount, 2) }}</td>
                                                        {{-- <td>{{$saleInfoList->customerInfo->name}}</td> --}}
                                                        {{-- <td>{{$saleInfoList->customerInfo->phone}}</td>
                                        <td>{{$saleInfoList->paid_amount}}</td>
                                        <td>On going</td>
                                        <td>{{$saleInfoList->productInfo->name}}</td>
                                        <td>{{number_format($saleInfoList->gross_amount, 2) }}</td>
                                        <td>{{number_format($saleInfoList->total_due_amount,2)}}</td>
                                        <td>{{number_format($saleInfoList->monthly_installment,2)}}</td>
                                        <td>{{$saleInfoList->installment}}</td> --}}
                                                    </tr>
                                                    @php
                                                        $totalAmount += $saleInfoList->paid_amount;
                                                    @endphp
                                                @endforeach
                                                @foreach ($pawningInfoLists as $pawningInfoList)
                                                    <tr>
                                                        {{-- <td>{{$saleInfoList->receipt_code}}</td> --}}
                                                        {{-- <td><input class="btn font-style btn-outline-primary orderModel" type="button" data-id="{{$saleInfoList->id}}"
                                        value="{{$saleInfoList->receipt_code}}"></td> --}}
                                                        <td>
                                                            {{-- <a href="#" data-url="{{ route('receiptsearch.receipt',$saleInfoList->id) }}"
                                            data-id="{{$saleInfoList->id}}" data-ajax-popup="true"
                                            data-title="{{__('Reciept')}}" class="btn font-style btn-outline-primary">
                                            {{ $saleInfoList->receipt_code }}
                                            </a> --}}
                                                            <button type="button"
                                                                class="btn font-style btn-outline-primary view"
                                                                value="{{ $pawningInfoList->id }}">
                                                                {{ $pawningInfoList->receipt_code }}
                                                            </button>
                                                        </td>
                                                        <td>{{ Auth::user()->customerNumberFormat($pawningInfoList->customer_id) }}
                                                        </td>
                                                        <td>{{ $pawningInfoList->customer->name ?? '' }}</td>
                                                        <td>{{ $pawningInfoList->description }}</td>
                                                        {{-- <td>{{$pawningInfoList->status}}</td> --}}
                                                        <td>{{ $pawningInfoList->issue_date }}</td>
                                                        <td class="text-right">
                                                            {{ number_format($pawningInfoList->paid_amount, 2) }}
                                                        </td>
                                                        {{-- <td>{{$saleInfoList->customerInfo->name}}</td> --}}
                                                        {{-- <td>{{$saleInfoList->customerInfo->phone}}</td>
                                        <td>{{$saleInfoList->paid_amount}}</td>
                                        <td>On going</td>
                                        <td>{{$saleInfoList->productInfo->name}}</td>
                                        <td>{{number_format($saleInfoList->gross_amount, 2) }}</td>
                                        <td>{{number_format($saleInfoList->total_due_amount,2)}}</td>
                                        <td>{{number_format($saleInfoList->monthly_installment,2)}}</td>
                                        <td>{{$saleInfoList->installment}}</td> --}}
                                                    </tr>
                                                    @php
                                                        $totalAmount += $pawningInfoList->paid_amount;
                                                    @endphp
                                                @endforeach
                                                @foreach ($customInfoLists as $customInfoList)
                                                    <tr>
                                                        <td>
                                                            <button type="button"
                                                                class="btn font-style btn-outline-primary view"
                                                                value="{{ $customInfoList->id }}">
                                                                {{ $customInfoList->receipt_code }}
                                                            </button>
                                                        </td>
                                                        <td>{{ Auth::user()->customerNumberFormat($customInfoList->customer_id) }}
                                                        </td>
                                                        <td>{{ $customInfoList->customer->name ?? '' }}</td>
                                                        <td>{{ $customInfoList->description }}</td>
                                                        <td>{{ $customInfoList->issue_date }}</td>
                                                        <td class="text-right">
                                                            {{ number_format($customInfoList->paid_amount, 2) }}</td>
                                                    </tr>
                                                    @php
                                                        $totalAmount += $customInfoList->paid_amount;
                                                    @endphp
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            @if ($showTable == 'Yes')
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td><b>Total</b></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                                                </tr>
                                            @endif
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="modal modelView" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Receipt Summery</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class=" col-md-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Due Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody id="installmetTableBody"></tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        var category = < ? php echo json_encode($category); ? > ;
        $(document).ready(function() {
            if (category == 'hire') {
                $('#loan_sub_category_div').hide();
                $('#hire_sub_category_div').show();
            } else if (category == 'loan') {
                $('#hire_sub_category_div').hide();
                $('#loan_sub_category_div').show();
            } else {
                $('#loan_sub_category_div').hide();
                $('#hire_sub_category_div').hide();
            }

            $('#category').change(function() {
                if ($(this).val() == 'hire') {
                    $('#loan_sub_category').val('');
                    $('#loan_sub_category_div').hide(200);
                    $('#hire_sub_category_div').show(200);
                } else if ($(this).val() == 'loan') {
                    $('#hire_sub_category').val('');
                    $('#hire_sub_category_div').hide(200);
                    $('#loan_sub_category_div').show(200);
                } else {
                    $('#loan_sub_category').val('');
                    $('#hire_sub_category').val('');
                    $('#loan_sub_category_div').hide(200);
                    $('#hire_sub_category_div').hide(200);
                }
            });
        });
    </script>
@endsection
