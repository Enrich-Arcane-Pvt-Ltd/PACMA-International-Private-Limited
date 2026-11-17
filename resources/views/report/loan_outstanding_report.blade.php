@extends('layouts.admin')
@section('page-title')
{{__('Outstanding Loan Report')}}
@endsection
@push('script-page')

<style>
    table tfoot {
        display: table-row-group;
    }

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
        var filename = 'Loan Outstanding Report';
        if ($('#duration').val() != undefined) {
            var duration = $('#duration').val();
        } else {
            var duration = "";
        }
        if ($('#loan_type').val() != '') {
            if ($('#loan_type').val() == 1) {
                var loanType = ' Of SME Loan';
            } else if ($('#loan_type').val() == 2) {
                var loanType = ' Of Group Loan';
            } else if ($('#loan_type').val() == 3) {
                var loanType = ' Of Vehicle Loan';
            } else if ($('#loan_type').val() == 4) {
                var loanType = ' Of Land Loan';
            }
        } else {
            var loanType = "";
        }
        $('#report-dataTable').DataTable({
            dom: 'lBfrtip',
            buttons: [{
                    extend: 'excel',
                    title: filename + loanType + duration,
                    footer: true
                },
                // {
                //     extend: 'print',
                //     title:  function() {
                //             return "<h4>"+filename+loanType+duration+"</h4>";
                //         } ,
                //     footer: true
                // },
                {
                    extend: 'csv',
                    title: filename + loanType + duration,
                    footer: true
                }
            ],
            "initComplete": function (settings, json) {
                $('.buttons-print').css('display', 'none');
            }
        });
    });

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Outstanding Loan Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Loan')}}</div>
        <div class="breadcrumb-item">{{__('Reports')}}</div>
        <div class="breadcrumb-item">{{__('Outstanding Loan Report')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Outstanding Loan Report') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Loan')}}</div>
                                <div class="breadcrumb-item">{{__('Reports')}}</div>
                                <div class="breadcrumb-item">{{__('Outstanding Loan Report')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('loan_outstanding_report.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
                                </div>
                                {{-- <div class="col-md-2">
                                            <label for="start_month">Start Date</label>
                                            <input class="form-control font-style" name="start_date" type="date" value="{{ $start_date ?? '' }}"
                                id="start_date" required>
                            </div> --}}
                            <div class="col-md-2">
                                <label for="end_date">Date</label>
                                <input class="form-control font-style" name="end_date" type="date"
                                    value="{{ $end_date ?? '' }}" id="end_date" required>
                            </div>
                            <div class="col-md-2">
                                <label for="loan_type">Loan Type</label>
                                <select class="form-control font-style" name="loan_type" id="loan_type">
                                    <option @if($loanType=='' ) selected @endif value="">Select Loan Type</option>
                                    <option @if($loanType==1) selected @endif value="1">SME Loan</option>
                                    <option @if($loanType==2) selected @endif value="2">Group Loan</option>
                                    <option @if($loanType==3) selected @endif value="3">Vehicle Loan</option>
                                    <option @if($loanType==4) selected @endif value="4">Land Loan</option>
                                </select>
                            </div>
                            <div class="col-md-2" id="center_div">
                                <label for="center_id">Center</label>
                                {{ Form::select('center_id', $centers, null, array('id' => 'center_id','class' => 'form-control font-style')) }}
                            </div>
                            <div class="col-md-2" id="group_div">
                                <label for="group_id">Group</label>
                                <select name="group_id" id="group_id" class="form-control font-style">
                                    <option value="">Select Group</option>
                                    @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="oficcer_id">Officer</label>
                                {{ Form::select('oficcer_id', $oficcers, $loanOficcer ?? '', array('id' => 'oficcer_id','class' => 'form-control font-style')) }}
                            </div>
                            <div class="col-md-2">
                                <label for="collection_type">Collection Type</label>
                                <select class="form-control font-style" name="collection_type" id="collection_type">
                                    <option @if($loanCollectionType=='' ) selected @endif value="">Select Collection
                                        Type</option>
                                    <option @if($loanCollectionType==3) selected @endif value="3">Monthly</option>
                                    <option @if($loanCollectionType==2) selected @endif value="2">Weekly</option>
                                    <option @if($loanCollectionType==1) selected @endif value="1">Daily</option>
                                </select>
                            </div>
                            {{-- <div class="col-md-2">
                                            <label for="end_month">Loan Status</label>
                                            <select class="form-control font-style" name="loan_status" id="loan_status">
                                                <option value="">Select Loan Status</option>
                                                <option value="0">Rejected Loans</option>
                                                <option value="1">Pending Loans</option>
                                                <option value="2">Granted Loans</option>
                                                <option value="3">Clossed Loans</option>
                                            </select>
                                        </div> --}}
                        </div>
                        <div class="row mt-3" style="float: right;">
                            <div class="col-auto">
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
            <div class="col-12" id="bill-container">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100" style="overflow-x: auto;">
                            <table class="table table-flush tbl-scrl" id="report-dataTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th> {{__('Loan No')}}</th>
                                        <th> {{__('Ref Code')}}</th>
                                        <th> {{__('Customer No')}}</th>
                                        <th> {{__('Customer Name')}}</th>
                                        <th> {{__('Customer NIC')}}</th>
                                        <th> {{__('Customer Phone')}}</th>
                                        <th> {{__('Center')}}</th>
                                        <th> {{__('Group')}}</th>
                                        <th> {{__('Date')}}</th>
                                        <th> {{__('Monthly Installment Date')}}</th>
                                        <th> {{__('Last Paid Date')}}</th>
                                        <th> {{__('Last Paid Amount')}}</th>
                                        <th> {{__('Collection Period')}}</th>
                                        <th> {{__('Loan Type')}}</th>
                                        <th> {{__('Officer')}}</th>
                                        <th> {{__('Installment Amount')}}</th>
                                        <th> {{__('Interest Rate')}}</th>
                                        <th> {{__('Loan Amount')}}</th>
                                        <th> {{__('Interest Amount')}}</th>
                                        <th> {{__('Loan with Interest')}}</th>
                                        <th> {{__('Paid Amount')}}</th>
                                        <th> {{__('Outstanding Amount')}}</th>
                                        {{-- <th> {{__('Salesman')}}</th> --}}
                                        {{-- <th> {{__('No of Installment Due')}}</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($showTable == 'Yes')
                                    {{-- <input type="hidden" id="duration" value="{{ __(' - From ').Auth::user()->dateFormat($start_date).__(' To ').Auth::user()->dateFormat($end_date) }}">
                                    --}}
                                    <input type="hidden" id="duration"
                                        value="{{ __(' - For ').Auth::user()->dateFormat($end_date) }}">
                                    @php
                                    $totalDuePaid = 0;
                                    $totalDue = 0;
                                    $totalinterstAmount = 0;
                                    $totalLoanAmount = 0;
                                    $loanCapitalAmount = 0;
                                    @endphp
                                    @foreach($loanInfoLists as $loanInfoList)
                                    <tr>
                                        {{-- <td><button class="btn font-style btn-outline-primary orderModel" type="button" data-id="{{$loanInfoList->id}}">{{ \Auth::user()->loanNumberFormat($loanInfoList->id) }}</button>
                                        </td> --}}
                                        <td>{{ \Auth::user()->loanNumberFormat($loanInfoList->id) }}</td>
                                        <td>{{$loanInfoList->reg_officer_code }}/{{$loanInfoList->reg_year_code }}/{{$loanInfoList->reg_loan_code}}
                                        </td>
                                        <td>@if(isset($loanInfoList->customer)){{ Auth::user()->customerNumberFormat($loanInfoList->customer->id) }}
                                            @else -- @endif</td>
                                        <td>@if(isset($loanInfoList->customer)) @if($loanInfoList->customer->title ==
                                            0)Mr. @elseif($loanInfoList->customer->title == 1)Mrs. @else Miss.
                                            @endif{{$loanInfoList->customer->name ?? '--'}} @else -- @endif</td>
                                        <td>{{$loanInfoList->customer->nic_no ?? '--'}}</td>
                                        <td>{{$loanInfoList->customer->phone ?? '--'}}</td>
                                        <td>{{$loanInfoList->center->name ?? '--'}}</td>
                                        <td>{{$loanInfoList->group->name ?? '--'}}</td>
                                        <td>{{$loanInfoList->date ?? '--'}}</td>
                                        <td class="text-center">
                                            @if($loanInfoList->time_range == 3)
                                            {{$loanInfoList->installment_date ?? '--'}} @endif
                                            @if($loanInfoList->time_range == 2) -- @endif
                                            @if($loanInfoList->time_range == 1) -- @endif
                                        </td>
                                        {{-- @php
                                                            $instCount = count($loanInfoList->loanInstallments);
                                                            if($instCount > 0){
                                                                $lastInstNo = $instCount - 1;
                                                                $lastInst = $loanInfoList->loanInstallments[$lastInstNo]->paid_date;
                                                                $lastInstAmount = $loanInfoList->loanInstallments[$lastInstNo]->paid_amount;
                                                            } else {
                                                                $lastInst = '--';
                                                                $lastInstAmount = '--';
                                                            }
                                                        @endphp --}}
                                        {{-- <td>{{$loanInfoList->loanInstallments[0]->installment_date ?? '--'}}</td>
                                        --}}
                                        {{-- <td>{{$lastInst}}</td>
                                        <td>{{$lastInstAmount}}</td> --}}
                                        <td>{{ $loanInfoList->lastInstallment->paid_date ?? '--' }}</td>
                                        <td>{{ $loanInfoList->lastInstallment->paid_amount ?? '--' }}</td>
                                        <td>@if ($loanInfoList->time_range == 1) Daily
                                            @elseif ($loanInfoList->time_range == 2) Weekly
                                            @elseif ($loanInfoList->time_range == 3) Monthly @endif
                                        </td>
                                        <td>
                                            @if ($loanInfoList->loan_type == 1) SME Loan
                                            @elseif ($loanInfoList->loan_type == 2) Group Loan
                                            @elseif ($loanInfoList->loan_type == 3) Vehicle Loan
                                            @elseif ($loanInfoList->loan_type == 4) Land Loan
                                            @elseif ($loanInfoList->loan_type == 5) Land Loan @endif
                                        </td>
                                        <td>{{$loanInfoList->officer->name ?? '--'}}</td>
                                        <td>
                                            @if($loanInfoList->time_range == 3)
                                            {{ number_format($loanInfoList->monthly_installment, 2) }}
                                            @elseif($loanInfoList->time_range == 2)
                                            {{ number_format($loanInfoList->weekly_installment, 2) }}
                                            @elseif($loanInfoList->time_range == 1)
                                            {{ number_format($loanInfoList->daily_installment, 2) }}
                                            @endif
                                        </td>
                                        <td>{{ $loanInfoList->interest_rate }} %</td>
                                        <td>{{ number_format($loanInfoList->loan_amount, 2) }}</td>
                                        <td>{{ number_format($loanInfoList->interest_amount, 2) }}</td>
                                        <td>{{ number_format($loanInfoList->total_amount, 2) }}</td>
                                        @php
                                        $paidAmount = 0;
                                        $outstandingBalance = 0;
                                        if(isset($loanInfoList->loanInstallments)){
                                        $paid = $loanInfoList->loanInstallments->sum('paid_amount');
                                        $fine = $loanInfoList->loanInstallments->sum('fine');
                                        $paidAmount = $paid;
                                        $outstandingBalance = $loanInfoList->total_amount - $paidAmount + $fine;
                                        }
                                        @endphp
                                        {{-- <td>{{ number_format($loanInfoList->due_paid_amount, 2) }}</td>
                                        <td>{{ number_format($loanInfoList->current_due_amount, 2) }}</td> --}}
                                        <td>{{ number_format($paidAmount, 2) }}</td>
                                        <td>{{ number_format($outstandingBalance, 2) }}</td>
                                        {{-- <td>{{$loanInfoList->introducer->name ?? '--'}}</td> --}}
                                        @php
                                        $totalinterstAmount += $loanInfoList->interest_amount;
                                        $totalLoanAmount += $loanInfoList->loan_amount;
                                        $loanCapitalAmount += $loanInfoList->total_amount;
                                        // $totalDuePaid += $loanInfoList->due_paid_amount;
                                        // $totalDue += $loanInfoList->current_due_amount;
                                        $totalDuePaid += $paidAmount;
                                        $totalDue += $outstandingBalance;
                                        // $count = 0;
                                        // foreach ($loanInfoList->loanInstallments as $loanInstallment) {
                                        // if($loanInstallment->status != 1){
                                        // $count++;
                                        // }
                                        // }
                                        @endphp
                                        {{-- <td>{{ $count }}</td> --}}
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    @if($showTable == 'Yes')
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Total</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>{{ number_format($totalLoanAmount, 2) }}</th>
                                        <th>{{ number_format($totalinterstAmount, 2) }}</th>
                                        <th>{{ number_format($loanCapitalAmount, 2) }}</th>
                                        <th>{{ number_format($totalDuePaid, 2) }}</th>
                                        <th>{{ number_format($totalDue, 2) }}</th>
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
    var loanType = < ? php echo json_encode($loanType); ? > ;
    var loanCenter = < ? php echo json_encode($loanCenter); ? > ;
    var loanGroup = < ? php echo json_encode($loanGroup); ? > ;
    $(document).ready(function () {
        if (loanType == 2) {
            $('#center_div').show();
            $('#center_id').val(loanCenter).trigger('change');
            if (loanCenter != '') {
                $('#group_div').show();
                $('#group_id').val(loanGroup);
            }
        } else {
            $('#center_div').hide();
            $('#group_div').hide();
        }

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

        $('#loan_type').change(function () {
            var loanType = $(this).val();
            if (loanType == 2) {
                $('#center_div').show(200);
            } else {
                $('#center_div').hide(200);
                $('#center_id').val('').trigger('change');
            }
        });

        $('#center_id').change(function () {
            var centerId = $(this).val();
            if (centerId != '') {
                $.ajax({
                    url: "{{ url('loan_outstanding_report/get_groups') }}/" + centerId,
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        if (data != null) {
                            $('#group_id').html('');
                            $('#group_id').append('<option value="">Select Group</option>');
                            for (i = 0; i < data.length; i++) {
                                $('#group_id').append('<option value="' + data[i].id +
                                    '">' + data[i].name + '</option>');
                            }
                        } else {
                            $('#group_id').html('');
                            $('#group_id').append(
                                '<option value="">No Groups to Select</option>');
                        }

                    }
                });
                $('#group_div').show(200);
            } else {
                $('#group_div').hide(200);
                $('#group_id').html('');
                $('#group_id').append('<option value="">No Groups to Select</option>');
            }
        });
    });

</script>
