@extends('layouts.admin')
@section('page-title')
{{__('Loan Installment Report')}}
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

    $(document).ready(function () {
        var show_table = $('#show_table').val();
        var filename = $('#filename').val();
        var officerName = $('#officer_name').val();
        var duration = $('#duration').val();

        // if (show_table == 'No'){
        $('#report-dataTable').DataTable({
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
    <input type="hidden" id="filename" value="{{ config('app.name') }} - Loan Installments Report">
    {{-- <div class="section-header">
            <h1>{{__('Loan Installment Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Report')}}</div>
        <div class="breadcrumb-item">{{__('Loan Installment Report')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Loan Installment Report') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Report')}}</div>
                                <div class="breadcrumb-item">{{__('Loan Installment Report')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('loan_installment_report.search') }}">
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
                                    <label for="loan_type">Officer</label>
                                    @if (isset($officerId))
                                    <input type="hidden" id="officer_name"
                                        value="{{ $officerId ? __(' Of ').Auth::user()->otherUserName($officerId) : '' }}">
                                    @endif
                                    {{ Form::select('officer_id', $users, $officerId ?? '', array('class' => 'form-control font-style')) }}
                                </div>
                                <div class="col-md-2">
                                    <label for="loan_type">Loan Type</label>
                                    <select class="form-control font-style" name="loan_type" id="loan_type">
                                        <option value="">Select Loan Type</option>
                                        <option @if( isset($loanType)) {{ ($loanType == 1) ? 'selected' : ''}} @endif
                                            value="1">SME Loan</option>
                                        <option @if( isset($loanType)) {{ ($loanType == 2) ? 'selected' : ''}} @endif
                                            value="2">Group Loan</option>
                                        <option @if( isset($loanType)) {{ ($loanType == 3) ? 'selected' : ''}} @endif
                                            value="3">Vehicle Loan</option>
                                        <option @if( isset($loanType)) {{ ($loanType == 4) ? 'selected' : ''}} @endif
                                            value="4">Land Loan</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="end_month">Status</label>
                                    <select class="form-control font-style" name="status" id="status">
                                        <option value="">Select Status</option>
                                        <option @if( isset($status)) {{ ($status == 4) ? 'selected' : ''}} @endif
                                            value="4">Not Paid & Over Due</option>
                                        <option @if( isset($status)) {{ ($status == 2) ? 'selected' : ''}} @endif
                                            value="2">Not Paid</option>
                                        <option @if( isset($status)) {{ ($status == 3) ? 'selected' : ''}} @endif
                                            value="3">Over Due</option>
                                        <option @if( isset($status)) {{ ($status == 1) ? 'selected' : ''}} @endif
                                            value="1">Paid</option>
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
                                        <h6>{{__('Total due payment')}} : {{number_format($duetotal,2)}}</h6>
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
                                <table class="table table-flush tbl-scrl" id="report-dataTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th> {{__('Loan No')}}</th>
                                            <th> {{__('Officer Name')}}</th>
                                            <th> {{__('Customer Name')}}</th>
                                            <th> {{__('Customer NIC')}}</th>
                                            {{-- <th> {{__('Customer Tel')}}</th> --}}
                                            <th> {{__('Customer Address')}}</th>
                                            <th> {{__('Loan Type')}}</th>
                                            <th> {{__('Status')}}</th>
                                            <th> {{__('Loan Amount')}}</th>
                                            <th> {{__('Due Amount')}}</th>
                                            <th> {{__('Time Period')}}</th>
                                            <th> {{__('Collection Period')}}</th>
                                            <th> {{__('Installment Date')}}</th>
                                            <th> {{__('Installment Amount')}}</th>
                                            <th> {{__('Amount To Pay')}}</th>
                                            <th> {{__('Paid Amount')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($showTable == 'Yes')
                                        <input type="hidden" id="insList" value="{{ $loanInfoLists }}">
                                        @foreach($loanInfoLists as $loanInfoList)
                                        <tr>
                                            <td>{{ \Auth::user()->loanNumberFormat($loanInfoList->loan_id) }}</td>
                                            <td>@if(isset($loanInfoList->loan) && $loanInfoList->loan->officer_id !=
                                                null){{ \Auth::user()->otherUserName($loanInfoList->loan->officer_id) }}
                                                @else -- @endif</td>
                                            <td>@if(isset($loanInfoList->loan))
                                                @if(isset($loanInfoList->loan->customer))
                                                @if($loanInfoList->loan->customer->title == 0) Mr.
                                                @elseif($loanInfoList->loan->customer->title == 1) Mrs. @else Miss.
                                                @endif {{$loanInfoList->loan->customer->name ?? ''}}
                                                @endif
                                                @endif
                                            </td>
                                            <td>{{$loanInfoList->loan->customer->nic_no ?? ''}}</td>
                                            <td>{{$loanInfoList->loan->customer->address ?? ''}}</td>
                                            <td>
                                                @if (isset($loanInfoList->loan))
                                                @if ($loanInfoList->loan->loan_type == 1) SME Loan
                                                @elseif ($loanInfoList->loan->loan_type == 2) Group Loan
                                                @elseif ($loanInfoList->loan->loan_type == 3) Vehicle Loan
                                                @elseif ($loanInfoList->loan->loan_type == 4) Land Loan @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($loanInfoList->loan))
                                                @if ($loanInfoList->status == 1) Paid
                                                @elseif ($loanInfoList->status == 2) Not Paid
                                                @elseif ($loanInfoList->status == 3) Overdue @endif
                                                @endif
                                            </td>
                                            <td>@if (isset($loanInfoList->loan))
                                                {{ number_format($loanInfoList->loan->loan_amount, 2) }} @endif</td>
                                            <td>@if (isset($loanInfoList->loan))
                                                {{ number_format($loanInfoList->loan->current_due_amount, 2) }} @endif
                                            </td>
                                            <td>{{ $loanInfoList->loan->time_period ?? '' }}</td>
                                            <td>@if ($loanInfoList->time_range == 1) Daily
                                                @elseif ($loanInfoList->time_range == 2) Weekly
                                                @elseif ($loanInfoList->time_range == 3) Monthly @endif
                                            </td>
                                            <td>{{ $loanInfoList->installment_date }}</td>
                                            <td> {{ number_format($loanInfoList->amount, 2) }} </td>
                                            <td>{{ number_format($loanInfoList->total_amount_to_pay, 2) }}</td>
                                            <td>@if ($loanInfoList->paid_amount != null)
                                                {{ number_format($loanInfoList->paid_amount, 2) }} @endif</td>
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
                                    <th>Installment Amount</th>
                                    <th>Amount to be Paid</th>
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
