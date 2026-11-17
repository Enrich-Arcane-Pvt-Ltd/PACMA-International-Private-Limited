@extends('layouts.admin')
@section('page-title')
{{__('Collection Report')}}
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

    $(document).ready(function () {
        var show_table = $('#show_table').val();
        var filename = $('#filename').val();
        if ($('#duration').val() != undefined) {
            var duration = $('#duration').val();
        } else {
            var duration = "";
        }

        if ($('#collection_type_name').val() != undefined) {
            var collectionName = $('#collection_type_name').val();
        } else {
            var collectionName = "";
        }

        // if (show_table == 'No'){
        $('#report-dataTable').DataTable({
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excel',
                title: filename + collectionName + duration,
                footer: true

            }, {
                extend: 'print',
                title: function () {
                    return "<h4>" + filename + collectionName + duration + "</h4>";
                },
                footer: true
            }, {
                extend: 'csv',
                title: filename + collectionName + duration,
                footer: true
            }]
        });
        // } else if(show_table == 'Yes') {
        //     $('#report-dataTable').DataTable({
        //         dom: 'lBfrtip',
        //         buttons: [
        //             {
        //                 extend: 'excel',
        //                 title: filename + officerName + duration
        //             },  {
        //                 extend: 'print',
        //                 title: filename + officerName + duration
        //             }, {
        //                 extend: 'csv',
        //                 title: filename + officerName + duration
        //             }
        //         ]
        //     });
        // }

    });

</script>
@endpush
@section('content')
<section class="section">
    <input type="hidden" id="show_table" value="{{ $showTable }}">
    <input type="hidden" id="filename" value="{{ config('app.name') }} - Collection Report">
    {{-- <div class="section-header">
            <h1>{{__('Collection Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Report')}}</div>
        <div class="breadcrumb-item">{{__('Collection Report')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Collection Report') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Report')}}</div>
                                <div class="breadcrumb-item">{{__('Collection Report')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('collection_report.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
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
                                    <label for="loan_type">Collection Type</label>
                                    @if (isset($collectionType))
                                    @if($collectionType == 'loan')
                                    <input type="hidden" id="collection_type_name" value=" Of Loans">
                                    @elseif($collectionType == 'pawning')
                                    <input type="hidden" id="collection_type_name" value=" Of Pawnings">
                                    @elseif($collectionType == 'hire_purchase')
                                    <input type="hidden" id="collection_type_name" value=" Of Hire Sales">
                                    @endif
                                    @endif
                                    {{-- {{ Form::select('officer_id', $users, $officerId ?? '', array('class' => 'form-control font-style')) }}
                                    --}}
                                    <select name="collection_type" id="collection_type" class="form-control font-style">
                                        <option value="">Select Collection Type</option>
                                        {{-- <option {{ ($collectionType == 'loan') ? 'selected': '' }}
                                        value="loan">Loans</option>
                                        <option {{ ($collectionType == 'pawning') ? 'selected': '' }} value="pawning">
                                            Pawning</option>
                                        <option {{ ($collectionType == 'hire_purchase') ? 'selected': '' }}
                                            value="hire_purchase">Hire Sales</option> --}}
                                        <option @if(isset($collectionType) && $collectionType=='loan' ) selected @endif
                                            value="loan">Loans</option>
                                        <option @if(isset($collectionType) && $collectionType=='pawning' ) selected
                                            @endif value="pawning">Pawning</option>
                                        <option @if(isset($collectionType) && $collectionType=='hire_purchase' )
                                            selected @endif value="hire_purchase">Hire Sales</option>
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
                                    @if($showTable == 'Yes')
                                    <div class="col">
                                        {{-- <h6>{{__('Total due payment')}} : {{number_format($duetotal,2)}}</h6> --}}
                                    </div>

                                    <div class="col">

                                    </div>

                                    <div class="col">

                                    </div>

                                    <div class="col">
                                        <input type="hidden" id="duration"
                                            value="{{ __(' From '). $start_date . " To " .$end_date }}">
                                        <h6>{{__('Duration')}} : {{ $start_date . " TO " .$end_date }} </h6>
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
                            <div class="d-flex justify-content-between w-100" style="overflow-x: auto;">
                                <table class="table table-flush" id="report-dataTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th> {{__('SI No')}}</th>
                                            <th> {{__('Customer Name')}}</th>
                                            <th> {{__('Customer NIC')}}</th>
                                            <th> {{__('Collection Type')}}</th>
                                            <th> {{__('Date')}}</th>
                                            <th> {{__('Amount')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($showTable == 'Yes')
                                        @php
                                        $total = 0;
                                        @endphp
                                        @foreach($saleInfoLists as $saleInfoList)
                                        <tr>
                                            <td> {{ $saleInfoList->receipt_code }} </td>
                                            <td> {{ $saleInfoList->customer->name }} </td>
                                            <td> {{ $saleInfoList->customer->nic_no }} </td>
                                            <td>
                                                @php
                                                $receipCode = mb_substr($saleInfoList->receipt_code, 0, 2);
                                                @endphp
                                                @if ($receipCode == 'DO')
                                                <span>Document Charges</span>
                                                @elseif ($receipCode == 'DP')
                                                <span>Hire Sales</span>
                                                @elseif ($receipCode == 'IN')
                                                <span>Hire Sales</span>
                                                @elseif ($receipCode == 'LN')
                                                <span>Loan</span>
                                                @elseif ($receipCode == 'AD')
                                                <span>Advance Payment</span>
                                                @endif
                                            </td>
                                            <td class="issue_date">{{$saleInfoList->issue_date}}</td>
                                            <td style="text-align: right;">
                                                {{ number_format($saleInfoList->paid_amount, 2) }}</td>
                                            @php
                                            $total += $saleInfoList->paid_amount;
                                            @endphp
                                        </tr>
                                        @endforeach
                                        @foreach($pawningInfoLists as $pawningInfoList)
                                        <tr>
                                            <td>{{ $pawningInfoList->receipt_code }}</td>
                                            <td>{{ $pawningInfoList->customer->name }}</td>
                                            <td>{{ $pawningInfoList->customer->nic_no }}</td>
                                            <td>Pawning</td>
                                            <td class="issue_date">{{$pawningInfoList->issue_date}}</td>
                                            <td style="text-align: right;">
                                                {{ number_format($pawningInfoList->paid_amount, 2) }}</td>
                                            @php
                                            $total += $pawningInfoList->paid_amount;
                                            @endphp
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        @if($showTable == 'Yes')
                                        <tr>
                                            <td></td>
                                            <th>Total</th>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <th style="text-align: right;">{{ number_format($total, 2) }}</th>
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
{{-- <section>
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
                                    <th>Installment Amount</th>
                                    <th>Amount Tobe Paid</th>
                                    <th>Paid Amount</th>
                                    <th>Due Date</th>
                                    <th>Stauts</th>
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
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        //   console.log(JSON.parse($('#insList').val()));
        $('.orderModel').on('click', function () {
            const loanId = $(this).attr("data-id");
            let row = "";
            let status;
            $.ajax({
                type: 'GET',
                url: '{{("installment/getLoanInstallment/")}}' + loanId,
                beforeSend: function () {},
                success: function (data) {
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
                            "</td><td>" + data[i].total_amount_to_pay + "</td><td>" +
                            paidAmount + "</td><td>" + data[i].installment_date +
                            "</td><td>" + status + "</td></tr>";

                    }
                },
                complete: function () {
                    $('#installmetTableBody').append(row);
                    $('.modelView').modal();
                }
            });

        });
    });

</script>
