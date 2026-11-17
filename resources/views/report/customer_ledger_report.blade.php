@extends('layouts.admin')
@section('page-title')
    {{ __('Customer Ledger Report') }}
@endsection
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


        $(document).ready(function() {
            var filename = $('#filename').val();
            $('#report-dataTable').DataTable({
                order: [
                    [2, "asc"]
                ],
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excel',
                    title: filename
                }, {
                    extend: 'print',
                    title: filename
                }, {
                    extend: 'csv',
                    title: filename
                }]
            });
        });
    </script>
@endpush
@section('content')
    <section class="section">
        {{-- <div class="section-header">
            <h1>{{__('Customer Ledger Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Report')}}</div>
        <div class="breadcrumb-item">{{__('Customer Ledger Report')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Customer Ledger Report') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Report') }}</div>
                                    <div class="breadcrumb-item">{{ __('Customer Ledger Report') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('customer_ledger_report.search') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4 class="h4 mb-0">Filter</h4>
                                    </div>
                                    {{-- <div class="col-md-4">
                                        </div> --}}
                                    {{-- <div class="col-md-2">
                                            <label for="start_month">Start Date</label>
                                            <input class="form-control font-style" name="start_date" type="date" value="" id="start_date" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="end_month">End Date</label>
                                            <input class="form-control font-style" name="end_date" type="date" value="" id="end_date" required>
                                        </div> --}}
                                    <div class="col-md-4">
                                        <label for="customer_id">Customer Number</label>
                                        <select name="customer_id" id="customer_id"
                                            class="form-control font-style selectpicker" data-live-search="true">
                                            <option value="">Select Customer Number</option>
                                            @foreach ($customers as $customer)
                                                <option @if ($customerId == $customer->id) selected @endif
                                                    value="{{ $customer->id }}">
                                                    {{ Auth::user()->customerNumberFormat($customer->id) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="nic_no">NIC Number</label>
                                        <input type="text" name="nic_no" id="nic_no" class="form-control font-style"
                                            value="{{ $customerNic ?? '' }}">
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
                                            <div class="col">
                                                <address>
                                                    <strong>{{ __('Customer Name') }} :</strong><br>
                                                    {{ $customerInfo->name ?? '' }}
                                                </address>
                                            </div>

                                            <div class="col">
                                                <address>
                                                    <strong>{{ __('Customer NIC') }} :</strong><br>
                                                    {{ $customerInfo->nic_no ?? '' }}
                                                </address>
                                            </div>

                                            <div class="col">
                                                <address>
                                                    <strong>{{ __('Customer Address') }} :</strong><br>
                                                    {{ $customerInfo->address ?? '' }}
                                                </address>
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
                            <div class="card-body">
                                <div class="d-flex justify-content-between w-100">
                                    <table class="table table-flush" id="report-dataTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th> {{ __('#') }}</th>
                                                <th> {{ __('Category') }}</th>
                                                <th> {{ __('Date') }}</th>
                                                <th> {{ __('Status') }}</th>
                                                <th> {{ __('Capital Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Paid Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Outstanding Balance') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($showTable == 'Yes')
                                                @foreach ($saleInfoLists as $saleInfoList)
                                                    <tr>
                                                        <td>{{ $saleInfoList->sale_id }}</td>
                                                        <td>
                                                            @if ($saleInfoList->sale_type == 0)
                                                                Cash Sales
                                                            @elseif($saleInfoList->sale_type == 1)
                                                                Hire Sales
                                                            @endif
                                                        </td>
                                                        <td>{{ $saleInfoList->date_of_sale }}</td>
                                                        <td>
                                                            @if ($saleInfoList->status == 1)
                                                                Paid
                                                            @elseif ($saleInfoList->status == 2)
                                                                Partialy Paid
                                                            @elseif ($saleInfoList->status == 3)
                                                                Over Due
                                                            @elseif ($saleInfoList->status == 4)
                                                                Returned
                                                            @endif
                                                        </td>
                                                        <td>{{ number_format($saleInfoList->net_amount, 2) }}</td>
                                                        <td>
                                                            @if ($saleInfoList->sale_type == 0)
                                                                @if ($saleInfoList->paid_amount != null)
                                                                    {{ number_format($saleInfoList->paid_amount, 2) }}
                                                                @else
                                                                    {{ number_format($saleInfoList->net_amount, 2) }}
                                                                @endif
                                                            @elseif($saleInfoList->sale_type == 1)
                                                                {{ number_format($saleInfoList->due_paid_amount, 2) }}
                                                            @endif
                                                        </td>
                                                        <td>{{ number_format($saleInfoList->current_due_amount, 2) }}</td>
                                                        <td>
                                                            <a href="#" data-size="2xl"
                                                                class="btn btn-primary btn-action mr-1"
                                                                style="padding-top: 10px;"
                                                                data-url="{{ route('customer_ledger_report.sales_details', [$saleInfoList->id, 'view']) }}"
                                                                data-ajax-popup="true" data-toggle="tooltip"
                                                                data-original-title="{{ __('View') }}">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="#" data-size="2xl"
                                                                class="btn btn-primary btn-action mr-1"
                                                                style="padding-top: 10px;"
                                                                data-url="{{ route('customer_ledger_report.sales_details', [$saleInfoList->id, 'print']) }}"
                                                                data-ajax-popup="true" data-toggle="tooltip"
                                                                data-original-title="{{ __('Print') }}">
                                                                <i class="fas fa-print"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @foreach ($loanInfoLists as $loanInfoList)
                                                    <tr>
                                                        <td>{{ Auth::user()->loanNumberFormat($loanInfoList->id) }}</td>
                                                        <td>
                                                            @if ($loanInfoList->loan_type == 1)
                                                                SME Loan
                                                            @elseif ($loanInfoList->loan_type == 2)
                                                                Group Loan
                                                            @elseif ($loanInfoList->loan_type == 3)
                                                                Vehicle Loan
                                                            @elseif ($loanInfoList->loan_type == 4)
                                                                Land Loan
                                                            @elseif ($loanInfoList->loan_type == 5)
                                                                Employment Loan
                                                            @endif
                                                        </td>
                                                        <td>{{ $loanInfoList->date }}</td>
                                                        <td>
                                                            @if ($loanInfoList->status == 0)
                                                                Rejected
                                                            @elseif ($loanInfoList->status == 1)
                                                                Pending
                                                            @elseif ($loanInfoList->status == 2)
                                                                Partialy Paid
                                                            @elseif ($loanInfoList->status == 3)
                                                                Paid
                                                            @elseif ($loanInfoList->status == 4)
                                                                Checked
                                                            @endif
                                                        </td>
                                                        <td>{{ number_format($loanInfoList->loan_amount, 2) }}</td>
                                                        <td>
                                                            @if (isset($loanInfoList->loanInstallments))
                                                                {{ number_format($loanInfoList->loanInstallments->sum('paid_amount'), 2) }}
                                                            @endif
                                                        </td>
                                                        <td>{{ number_format($loanInfoList->current_due_amount, 2) }}</td>
                                                        <td>
                                                            <a href="#" data-size="2xl"
                                                                class="btn btn-primary btn-action mr-1"
                                                                style="padding-top: 10px;"
                                                                data-url="{{ route('customer_ledger_report.loan_details', [$loanInfoList->id, 'view']) }}"
                                                                data-ajax-popup="true" data-toggle="tooltip"
                                                                data-original-title="{{ __('View') }}">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="#" data-size="2xl"
                                                                class="btn btn-primary btn-action mr-1"
                                                                style="padding-top: 10px;"
                                                                data-url="{{ route('customer_ledger_report.loan_details', [$loanInfoList->id, 'print']) }}"
                                                                data-ajax-popup="true" data-toggle="tooltip"
                                                                data-original-title="{{ __('Print') }}">
                                                                <i class="fas fa-print"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @foreach ($pawningInfoLists as $pawningInfoList)
                                                    <tr>
                                                        <td>{{ Auth::user()->pawningNumberFormat($pawningInfoList->id) }}
                                                        </td>
                                                        <td>Pawning</td>
                                                        <td>{{ $pawningInfoList->issue_date }}</td>
                                                        <td>
                                                            @if ($pawningInfoList->status == 1)
                                                                To Be Paid
                                                            @elseif ($pawningInfoList->status == 2)
                                                                Partialy Paid
                                                            @elseif ($pawningInfoList->status == 3)
                                                                Over Due
                                                            @elseif ($pawningInfoList->status == 4)
                                                                Paid
                                                            @endif
                                                        </td>
                                                        <td>{{ number_format($pawningInfoList->full_amount_payout, 2) }}
                                                        </td>
                                                        <td>{{ number_format($pawningInfoList->full_total_of_payment - $pawningInfoList->current_due_amount, 2) }}
                                                        </td>
                                                        <td>{{ number_format($pawningInfoList->current_due_amount, 2) }}
                                                        </td>
                                                        <td>
                                                            <a href="#" data-size="2xl"
                                                                class="btn btn-primary btn-action mr-1"
                                                                style="padding-top: 10px;"
                                                                data-url="{{ route('customer_ledger_report.pawning_details', [$pawningInfoList->id, 'view']) }}"
                                                                data-ajax-popup="true" data-toggle="tooltip"
                                                                data-original-title="{{ __('View') }}">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="#" data-size="2xl"
                                                                class="btn btn-primary btn-action mr-1"
                                                                style="padding-top: 10px;"
                                                                data-url="{{ route('customer_ledger_report.pawning_details', [$pawningInfoList->id, 'print']) }}"
                                                                data-ajax-popup="true" data-toggle="tooltip"
                                                                data-original-title="{{ __('Print') }}">
                                                                <i class="fas fa-print"></i>
                                                            </a>
                                                        </td>
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
    {{-- <section>
        <div class="modal modelView" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Instalment Summery</h5>
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
                            <tbody id="installmetTableBody">

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
    </section> --}}
    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();

        });
    </script>
@endsection
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function(){


    $('.orderModel').on('click', function() {
            const saleId = $(this).attr("data-id");
            let row = "";
            let status;
                $.ajax({
                    type: 'GET',
                    url: '{{("installment/getProductInstallment/")}}' + saleId,
beforeSend: function() {
},
success: function(data) {
result = data;
console.log(result);
$('#installmetTableBody').html("");
for (let i = 0; i < data.length; i++) { let element=data[i].amount; if(data[i].status==1){ status="Paid" ; }else
    if(data[i].status==2){ status="Not Paid" ; }else if(data[i].status==3){ status="Over Due" ; } var paidAmount=0.00;
    if(data[i].paid_amount !=null){ paidAmount=data[i].paid_amount; } // row+="<tr><td>" +data[i].id+"</td> <td>
    "+data[i].amount+"</td>
    <td>"+data[i].branch_id+"</td>
    <td>"+data[i].paid_amount+"</td>
    <td>"+data[i].installment_date+"</td>
    <td>"+status+"</td>
    </tr>";
    row+="<tr>
        <td>"+data[i].id+"</td>
        <td>"+data[i].amount+"</td>
        <td>"+paidAmount+"</td>
        <td>"+data[i].installment_date+"</td>
        <td>"+status+"</td>
    </tr>";

    }
    },
    complete: function() {
    $('#installmetTableBody').append(row);
    $('.modelView').modal();
    }
    });

    });
    });

    </script> --}}
