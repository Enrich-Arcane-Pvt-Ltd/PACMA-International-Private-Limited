@extends('layouts.admin')
@section('page-title')
    {{ __('Due Payment Report') }}
@endsection
@push('script-page')
    <style>
        .tbl-scrl {
            display: block;
            max-height: 40rem;
            overflow-y: scroll;
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
            var filename = $('#filename').val() || "{{ config('app.name') }} - Due Payment Report";
            $('#report-dataTable').DataTable({
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'excel',
                        title: filename,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        title: filename,
                        orientation: 'landscape',
                        pageSize: 'A4',
                        @if ($showTable == 'Yes' && !empty($start_date) && !empty($end_date))
                        messageTop: "Duration : {{ $start_date . ' TO ' . $end_date }}   |   Total Due Payment : {{ number_format($duetotal ?? 0, 2) }}",
                        @endif
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function(doc) {
                            doc.pageOrientation = 'landscape';
                            doc.pageSize = 'A4';
                            doc.pageMargins = [10, 15, 10, 15];
                            doc.defaultStyle.fontSize = 6.5;
                            if (doc.styles.tableHeader) {
                                doc.styles.tableHeader.fontSize = 7;
                                doc.styles.tableHeader.alignment = 'center';
                            }
                            if (doc.styles.title) {
                                doc.styles.title.fontSize = 11;
                                doc.styles.title.alignment = 'center';
                            }
                            if (doc.styles.message) {
                                doc.styles.message.fontSize = 8;
                                doc.styles.message.alignment = 'center';
                            }

                            // Find table in doc.content
                            for (var i = 0; i < doc.content.length; i++) {
                                if (doc.content[i].table) {
                                    var tableNode = doc.content[i];
                                    if (tableNode.table.body && tableNode.table.body[0] && tableNode.table.body[0].length === 13) {
                                        tableNode.table.widths = [
                                            36,  // 0: Sale No
                                            32,  // 1: Installment Date
                                            48,  // 2: Officer Name
                                            75,  // 3: Customer Name
                                            50,  // 4: Customer Tel
                                            125, // 5: Customer Address
                                            42,  // 6: Category
                                            35,  // 7: Status
                                            48,  // 8: Product Code
                                            55,  // 9: Gross Amount
                                            55,  // 10: Due Amount
                                            55,  // 11: Monthly Installment
                                            48   // 12: No of Installments Due
                                        ];
                                    } else if (tableNode.table.body && tableNode.table.body[0]) {
                                        tableNode.table.widths = Array(tableNode.table.body[0].length).fill('*');
                                    }

                                    tableNode.layout = {
                                        hLineWidth: function(i, node) { return 0.5; },
                                        vLineWidth: function(i, node) { return 0.5; },
                                        hLineColor: function(i, node) { return '#cccccc'; },
                                        vLineColor: function(i, node) { return '#cccccc'; },
                                        paddingLeft: function(i, node) { return 3; },
                                        paddingRight: function(i, node) { return 3; },
                                        paddingTop: function(i, node) { return 2; },
                                        paddingBottom: function(i, node) { return 2; }
                                    };

                                    if (tableNode.table.body) {
                                        for (var r = 1; r < tableNode.table.body.length; r++) {
                                            var row = tableNode.table.body[r];
                                            if (row[1]) row[1].alignment = 'center';
                                            if (row[7]) row[7].alignment = 'center';
                                            if (row[9]) row[9].alignment = 'right';
                                            if (row[10]) row[10].alignment = 'right';
                                            if (row[11]) row[11].alignment = 'right';
                                            if (row[12]) row[12].alignment = 'center';
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                    }, {
                        extend: 'print',
                        title: filename,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }, {
                        extend: 'csv',
                        title: filename,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });
        });
    </script>
@endpush
@section('content')
    <section class="section">
        {{-- <div class="section-header">
            <h1>{{__('Due Payment Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Report')}}</div>
        <div class="breadcrumb-item">{{__('Due Payment Report')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Due Payment Report') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Report') }}</div>
                                    <div class="breadcrumb-item">{{ __('Due Payment Report') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('due_payment.search') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4 class="h4 mb-0">Filter</h4>
                                    </div>
                                    <div class="col-md-4">
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
                                                <h6>{{ __('Total due payment') }} : {{ number_format($duetotal, 2) }}</h6>
                                            </div>

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
                                    <table class="table table-flush tbl-scrl" id="report-dataTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th> {{ __('Sale No') }}</th>
                                                <th> {{ __('Installment Date') }}</th>
                                                <th> {{ __('Officer Name') }}</th>
                                                <th> {{ __('Customer Name') }}</th>
                                                <th> {{ __('Customer Tel') }}</th>
                                                <th> {{ __('Customer Address') }}</th>
                                                <th> {{ __('Category') }}</th>
                                                <th> {{ __('Status') }}</th>
                                                <th> {{ __('Product Code') }}</th>
                                                <th> {{ __('Gross Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Due Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Monthly Installment') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('No of Installments Due') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($showTable == 'Yes')
                                                @foreach ($saleInfoLists as $saleInfoList)
                                                    <tr>
                                                        <td><button class="btn font-style btn-outline-primary orderModel"
                                                                type="button"
                                                                data-id="{{ $saleInfoList->id }}">{{ $saleInfoList->sale_id }}</button>
                                                        </td>
                                                        <td>{{ $saleInfoList->installment_date }}</td>
                                                        <td>{{ AUth::user()->userName($saleInfoList->officer_id) }}</td>
                                                        <td>{{ $saleInfoList->customer->name }}</td>
                                                        <td>{{ $saleInfoList->customer->phone }}</td>
                                                        <td>
                                                            @php
                                                                $cHouseNo = !empty($saleInfoList->customer->billing_house_no)
                                                                    ? trim($saleInfoList->customer->billing_house_no)
                                                                    : (!empty($saleInfoList->customer->house_no) ? trim($saleInfoList->customer->house_no) : '');
                                                                $cAddress = !empty($saleInfoList->customer->address)
                                                                    ? trim($saleInfoList->customer->address)
                                                                    : (!empty($saleInfoList->customer->billing_address) ? trim($saleInfoList->customer->billing_address) : '');
                                                                $cFullAddress = $cAddress;
                                                                if (!empty($cHouseNo)) {
                                                                    $cleanCHouseNo = trim($cHouseNo, ", \t\n\r\0\x0B");
                                                                    if (empty($cAddress)) {
                                                                        $cFullAddress = $cHouseNo;
                                                                    } elseif ($cleanCHouseNo !== '' && stripos($cAddress, $cleanCHouseNo) === false) {
                                                                        $formattedCHouseNo = rtrim($cleanCHouseNo, ',') . ',';
                                                                        $cFullAddress = $formattedCHouseNo . ' ' . $cAddress;
                                                                    }
                                                                }
                                                            @endphp
                                                            {{ $cFullAddress }}
                                                        </td>
                                                        <td>
                                                            @if ($saleInfoList->sale_type == 0)
                                                                Direct
                                                            @elseif ($saleInfoList->sale_type == 1)
                                                                Hire Sales
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($saleInfoList->status == 1)
                                                                Paid
                                                            @elseif ($saleInfoList->status == 2)
                                                                On going
                                                            @endif
                                                        </td>
                                                        <td>{{ $saleInfoList->products->barcode ?? '' }}</td>
                                                        <td>{{ number_format($saleInfoList->gross_amount, 2) }}</td>
                                                        <td>{{ number_format($saleInfoList->total_due_amount, 2) }}</td>
                                                        <td>{{ number_format($saleInfoList->monthly_installment, 2) }}</td>
                                                        <td>{{ $saleInfoList->installment }}</td>
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
    <section>
        <div class="modal modelView" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document" style="width: auto !important; max-width: 1000px !important;">
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
                                        {{-- <th>Branch</th> --}}
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
    </section>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {

        $('.orderModel').on('click', function() {
            const saleId = $(this).attr("data-id");
            let row = "";
            let status;
            $.ajax({
                type: 'GET',
                url: '{{ 'installment/getProductInstallment/' }}' + saleId,
                beforeSend: function() {},
                success: function(data) {
                    result = data;
                    console.log(result);
                    $('#installmetTableBody').html("");
                    for (let i = 0; i < data.length; i++) {
                        let element = data[i].amount;
                        if (data[i].status == 1) {
                            status = "Paid";
                        } else if (data[i].status == 2) {
                            status = "Not Paid";
                        } else if (data[i].status == 3) {
                            status = "Over Due";
                        }

                        var paidAmount = 0.00;
                        if (data[i].paid_amount != null) {
                            paidAmount = data[i].paid_amount;
                        }

                        var no = i + 1;
                        // row+="<tr><td>"+data[i].id+"</td><td>"+data[i].amount+"</td><td>"+data[i].branch_id+"</td><td>"+data[i].paid_amount+"</td><td>"+data[i].installment_date+"</td><td>"+status+"</td></tr>";
                        row += "<tr><td>" + no + "</td><td>" + data[i].amount +
                            "</td><td>" + paidAmount + "</td><td>" + data[i]
                            .installment_date + "</td><td>" + status + "</td></tr>";

                    }
                },
                complete: function() {
                    $('#installmetTableBody').append(row);
                    $('.modelView').modal();
                }
            });

        });
    });
</script>
