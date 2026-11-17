@extends('layouts.admin')
@section('page-title')
{{__('Pawning Outstanding Report')}}
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
        var filename = 'Pawning Outstanding Report';
        if ($('#duration').val() != undefined) {
            var duration = $('#duration').val();
        } else {
            var duration = "";
        }

        if ($('#category').length > 0) {
            var category = $('#category').val();
        } else {
            var category = "";
        }

        $('#report-dataTable').DataTable({
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excel',
                title: filename + category + duration,
                footer: true
            }, {
                extend: 'print',
                title: function () {
                    return "<h3>" + filename + category + duration + "</h3>";
                },
                footer: true
            }, {
                extend: 'csv',
                title: filename + category + duration,
                footer: true
            }]
        });
    });

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Pawning Outstanding Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Pawning')}}</div>
        <div class="breadcrumb-item">{{__('Reports')}}</div>
        <div class="breadcrumb-item">{{__('Pawning Outstanding Report')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Pawning Outstanding Report') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item">{{__('Pawning')}}</div>
                                <div class="breadcrumb-item">{{__('Reports')}}</div>
                                <div class="breadcrumb-item">{{__('Pawning Outstanding Report')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('pawning_outstanding_report.search') }}">
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
                                <label for="oficcer_id">Oficcer</label>
                                {{ Form::select('oficcer_id', $oficcers, $pawningOficcer ?? '', array('id' => 'oficcer_id','class' => 'form-control font-style')) }}
                            </div>
                            <div class="col-md-2">
                                <label for="introducer_id">Salesman</label>
                                {{ Form::select('introducer_id', $introducers, $pawningIntroducer ?? '', array('id' => 'introducer_id','class' => 'form-control font-style')) }}
                            </div>
                            <div class="col-md-2">
                                <label for="collection_type">Collection Type</label>
                                <select class="form-control font-style" name="collection_type" id="collection_type">
                                    <option @if($pawningCollectionType=='' ) selected @endif value="">Select Collection
                                        Type</option>
                                    <option @if($pawningCollectionType==3) selected @endif value="3">Monthly</option>
                                    <option @if($pawningCollectionType==2) selected @endif value="2">Weekly</option>
                                    <option @if($pawningCollectionType==1) selected @endif value="1">Daily</option>
                                </select>
                            </div>

                        </div>
                        <div class="row m-3" style="float: right;">
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
                                        <th> {{__('Pawning No')}}</th>
                                        <th> {{__('Customer No')}}</th>
                                        <th> {{__('Customer Name')}}</th>
                                        <th> {{__('Customer NIC')}}</th>
                                        <th> {{__('Customer Phone')}}</th>
                                        <th> {{__('Date')}}</th>
                                        <th> {{__('Installment Date')}}</th>
                                        <th> {{__('Last Paid Date')}}</th>
                                        <th> {{__('Last Paid Amount')}}</th>
                                        <th> {{__('Collection Period')}}</th>
                                        <th> {{__('Officer')}}</th>
                                        <th> {{__('Salesman')}}</th>
                                        <th> {{__('installment Amount')}}</th>
                                        {{-- <th> {{__('Interest Rate')}}</th> --}}
                                        <th> {{__('Loan Amount')}}</th>
                                        <th> {{__('Interest Amount')}}</th>
                                        <th> {{__('Loan with Interest')}}</th>
                                        {{-- <th> {{__('Paid Amount')}}</th> --}}
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
                                    $totalpawningAmount = 0;
                                    $totalinterstAmount = 0;
                                    $totalCapital = 0;
                                    $totalDue = 0;
                                    @endphp
                                    @foreach($pawningInfoLists as $pawningInfoList)
                                    <tr>
                                        {{-- <td><button class="btn font-style btn-outline-primary orderModel" type="button" data-id="{{$loanInfoList->id}}">{{ \Auth::user()->loanNumberFormat($loanInfoList->id) }}</button>
                                        </td> --}}
                                        <td>{{ Auth::user()->pawningNumberFormat($pawningInfoList->id) }}</td>
                                        <td>@if(isset($pawningInfoList->customer)){{ Auth::user()->customerNumberFormat($pawningInfoList->customer->id) }}
                                            @else -- @endif</td>
                                        <td>@if($pawningInfoList->customer->title == 0)Mr.
                                            @elseif($pawningInfoList->customer->title == 1)Mrs. @else Miss.
                                            @endif{{$pawningInfoList->customer->name ?? '--'}}</td>
                                        <td>{{$pawningInfoList->customer->nic_no ?? '--'}}</td>
                                        <td>{{$pawningInfoList->customer->phone ?? '--'}}</td>
                                        <td>{{$pawningInfoList->issue_date ?? '--'}}</td>
                                        <td class="text-center">@if($pawningInfoList->time_range == 3)
                                            {{$pawningInfoList->installment_date ?? '--'}} @endif</td>
                                        {{-- @php
                                                            $instCount = count($pawningInfoList->installments);
                                                            if($instCount > 0){
                                                                $lastInstNo = $instCount - 1;
                                                                $lastInst = $pawningInfoList->installments[$lastInstNo]->paid_date;
                                                                $lastInstAmount = $pawningInfoList->installments[$lastInstNo]->paid_amount;
                                                            } else {
                                                                $lastInst = '--';
                                                                $lastInstAmount = '--';
                                                            }
                                                        @endphp --}}
                                        {{-- <td>{{$pawningInfoList->loanInstallments[0]->installment_date ?? '--'}}
                                        </td> --}}
                                        {{-- <td>{{$lastInst}}</td>
                                        <td>{{$lastInstAmount}}</td> --}}
                                        <td>{{ $pawningInfoList->lastInstallment->paid_date ?? '' }}</td>
                                        <td>{{ $pawningInfoList->lastInstallment->paid_amount ?? '' }}</td>
                                        <td>@if ($pawningInfoList->time_range == 1) Daily
                                            @elseif ($pawningInfoList->time_range == 2) Weekly
                                            @elseif ($pawningInfoList->time_range == 3) Monthly @endif
                                        </td>
                                        <td>{{$pawningInfoList->officer->name ?? '--'}}</td>
                                        <td>{{$pawningInfoList->introducer->name ?? '--'}}</td>
                                        <td>{{ number_format($pawningInfoList->full_prepayment, 2) }} </td>
                                        {{-- <td>{{ $pawningInfoList->interest_rate }} %</td> --}}
                                        <td>{{ number_format($pawningInfoList->full_amount_payout, 2) }}</td>
                                        <td>{{ number_format($pawningInfoList->full_financed_charge, 2) }}</td>
                                        @php
                                        $paidAmount = 0;
                                        $outstandingBalance = 0;
                                        if(isset($pawningInfoList->installments)){
                                        $paidAmount = $pawningInfoList->installments->sum('paid_amount');
                                        $outstandingBalance = $pawningInfoList->full_amount_payout - $paidAmount;
                                        }
                                        @endphp
                                        {{-- <td>{{ number_format($pawningInfoList->full_total_of_payment, 2) }}</td>
                                        <td>{{ number_format($pawningInfoList->current_due_amount, 2) }}</td> --}}
                                        <td>{{ number_format($paidAmount, 2) }}</td>
                                        <td>{{ number_format($outstandingBalance, 2) }}</td>
                                        {{-- <td>{{$pawningInfoList->introducer->name ?? '--'}}</td> --}}
                                        @php
                                        $totalinterstAmount += $pawningInfoList->full_financed_charge;
                                        $totalCapital += $pawningInfoList->full_total_of_payment;
                                        $totalpawningAmount += $paidAmount;
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
                                        <th>{{ number_format($totalpawningAmount, 2) }}</th>
                                        <th>{{ number_format($totalinterstAmount, 2) }}</th>
                                        <th>{{ number_format($totalCapital, 2) }}</th>
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
</section>
@endsection
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    var loanType = <php echo json_encode($loanType); ?>;
    var loanCenter = <php echo json_encode($loanCenter); ?>;
    var loanGroup = <php echo json_encode($loanGroup); ?>;
  $(document).ready(function(){
      if(loanType == 2){
        $('#center_div').show();
        $('#center_id').val(loanCenter).trigger('change');
            if(loanCenter != ''){
                $('#group_div').show();
                $('#group_id').val(loanGroup);
            }
      } else {
        $('#center_div').hide();
        $('#group_div').hide();
      }

    $('.orderModel').on('click', function() {
            const loanId = $(this).attr("data-id");
            let row = "";
            let status;
                $.ajax({
                    type: 'GET',
                    url: '{{("installment/getLoanInstallment/")}}' + loanId,
beforeSend: function() {
},
success: function(data) {
result = data;
console.log(result);
$('#installmetTableBody').html("");
for (let i = 0; i < data.length; i++) { let element=data[i].amount; if(data[i].status==1){ status="Paid" ; }else
    if(data[i].status==2){ status="Not Paid" ; }else if(data[i].status==3){ status="Over Due" ; } var paidAmount=0.00;
    if(data[i].paid_amount !=null){ paidAmount=data[i].paid_amount; } var no=i+1; // row+="<tr><td>" +data[i].id+"</td>
    <td>"+data[i].amount+"</td>
    <td>"+data[i].branch_id+"</td>
    <td>"+data[i].paid_amount+"</td>
    <td>"+data[i].installment_date+"</td>
    <td>"+status+"</td>
    </tr>";
    row+="<tr>
        <td>"+ no +"</td>
        <td>"+data[i].amount+"</td>
        <td>"+data[i].total_amount_to_pay+"</td>
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

    $('#loan_type').change(function(){
    var loanType = $(this).val();
    if(loanType == 2){
    $('#center_div').show(200);
    } else {
    $('#center_div').hide(200);
    $('#center_id').val('').trigger('change');
    }
    });

    $('#center_id').change(function(){
    var centerId = $(this).val();
    if(centerId != ''){
    $.ajax({
    url: "{{ url('loan_outstanding_report/get_groups') }}/"+centerId,
    method: 'GET',
    dataType: 'json',
    success: function (data){
    if(data != null){
    $('#group_id').html('');
    $('#group_id').append('<option value="">Select Group</option>');
    for(i = 0; i < data.length; i++){ $('#group_id').append('<option value="'+data[i].id+'">'+data[i].name+'</option>');
        }
        } else {
        $('#group_id').html('');
        $('#group_id').append('<option value="">No Groups to Select</option>');
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

        </script> --}}
