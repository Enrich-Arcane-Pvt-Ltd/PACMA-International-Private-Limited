@extends('layouts.admin')
@section('page-title')
    {{ __('Outstanding Hire Sales Report') }}
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


        $(document).ready(function() {
            var filename = 'Hire Sales Outstanding Report';
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
                    },
                    // {
                    //     extend: 'print',
                    //     title:  function() {
                    //             return "<h3>"+filename+category+duration+"</h3>";
                    //         } ,
                    //     footer: true
                    // },
                    {
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
            <h1>{{__('Outstanding Hire Sales Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Cash Sales & Hire Sales')}}</div>
        <div class="breadcrumb-item">{{__('Reports')}}</div>
        <div class="breadcrumb-item">{{__('Outstanding Hire Sales Report')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Outstanding Hire Sales Report') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Cash Sales & Hire Sales') }}</div>
                                    <div class="breadcrumb-item">{{ __('Reports') }}</div>
                                    <div class="breadcrumb-item">{{ __('Outstanding Hire Sales Report') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 ">
                        <form method="post" action="{{ route('hire_purchase_outstanding_report.search') }}">
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
                                        <label for="loan_type">Category</label>
                                        @if (isset($productCategory))
                                            @if ($productCategory != '')
                                                <input type="hidden" id="category"
                                                    value="{{ __(' Of ') . App\ProductCategory::categoryName($productCategory)[0] }}">
                                            @endif
                                        @endif
                                        {{ Form::select('category_id', $productCategories, $productCategory ?? '', ['class' => 'form-control font-style']) }}
                                    </div>
                                    <div class="col-md-2">
                                        <label for="oficcer_id">Oficcer</label>
                                        {{ Form::select('oficcer_id', $oficcers, $loanOficcer ?? '', ['id' => 'oficcer_id', 'class' => 'form-control font-style']) }}
                                    </div>
                                    <div class="col-md-2">
                                        <label for="introducer_id">Salesman</label>
                                        {{ Form::select('introducer_id', $introducers, $loanIntroducer ?? '', ['id' => 'introducer_id', 'class' => 'form-control font-style']) }}
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
                                                <th> {{ __('Sale No') }}</th>
                                                <th> {{ __('Customer No') }}</th>
                                                <th> {{ __('Customer Name') }}</th>
                                                <th> {{ __('Customer NIC') }}</th>
                                                <th> {{ __('Customer Phone') }}</th>
                                                <th> {{ __('Product Category') }}</th>
                                                <th> {{ __('Item') }}</th>
                                                <th> {{ __('QTY') }}</th>
                                                <th> {{ __('Index') }}</th>
                                                <th> {{ __('Date') }}</th>
                                                <th> {{ __('First Installment Date') }}</th>
                                                <th> {{ __('Installment Date') }}</th>
                                                <th> {{ __('Installment Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Loan Expiry Date') }}</th>
                                                <th> {{ __('Last Paid Date') }}</th>
                                                <th> {{ __('Last Paid Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Officer') }}</th>
                                                <th> {{ __('Salesman') }}</th>
                                                <th> {{ __('Loan Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Interest Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Interest Rate') }}</th>
                                                <th> {{ __('Period') }}</th>
                                                <th> {{ __('Loan With Interest') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Total Paid Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Outstanding Amount') }} ({{ $currencySymbol }})</th>
                                                {{-- <th> {{__('Salesman')}}</th> --}}
                                                {{-- <th> {{__('No of Installment Due')}}</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($showTable == 'Yes')
                                                {{-- <input type="hidden" id="duration" value="{{ __(' - From ').Auth::user()->dateFormat($start_date).__(' To ').Auth::user()->dateFormat($end_date) }}">
                                    --}}
                                                <input type="hidden" id="duration"
                                                    value="{{ __(' - For ') . Auth::user()->dateFormat($end_date) }}">
                                                @php
                                                    $totalOutstandAmount = 0;
                                                    $totalinterstAmount = 0;
                                                    $totalDueAmount = 0;
                                                    $totalDuePaid = 0;
                                                    $totalDue = 0;
                                                @endphp
                                                @foreach ($loanInfoLists as $loanInfoList)
                                                    <tr>
                                                        {{-- <td><button class="btn font-style btn-outline-primary orderModel" type="button" data-id="{{$loanInfoList->id}}">{{ \Auth::user()->loanNumberFormat($loanInfoList->id) }}</button>
                                        </td> --}}
                                                        <td>{{ $loanInfoList->sale_id }}</td>
                                                        <td>
                                                            @if (isset($loanInfoList->customer))
                                                                {{ Auth::user()->customerNumberFormat($loanInfoList->customer->id) }}
                                                            @else
                                                                --
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($loanInfoList->customer->title == 0)
                                                                Mr.
                                                            @elseif($loanInfoList->customer->title == 1)
                                                                Mrs.
                                                            @else
                                                                Miss.
                                                            @endif
                                                            {{ $loanInfoList->customer->name ?? '--' }}
                                                        </td>
                                                        <td>{{ $loanInfoList->customer->nic_no ?? '--' }}</td>
                                                        <td>{{ $loanInfoList->customer->phone ?? '--' }}</td>
                                                        <td>{{ $loanInfoList->products->productCategory->name ?? '' }}</td>
                                                        <td>{{ $loanInfoList->products->name ?? '' }}</td>
                                                        <td>{{ $loanInfoList->quantity ?? '' }}</td>
                                                        <td class="text-center">
                                                            @if ($loanInfoList->hrie_purchase_index == null)
                                                                {{ App\Sale::hirePurchaseIndex($loanInfoList->customer_id, $loanInfoList->date_of_sale) }}
                                                            @else
                                                                {{ $loanInfoList->hrie_purchase_index }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $loanInfoList->date_of_sale ?? '--' }}</td>
                                                        <td>{{ $loanInfoList->firstInstallment->installment_date ?? '--' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $loanInfoList->installment_date ?? '--' }}</td>
                                                        <td>{{ $loanInfoList->monthly_installment ?? '--' }}</td>
                                                        <td>{{ $loanInfoList->due_date ?? '--' }}</td>
                                                        {{-- @php
                                                            $instCount = count($loanInfoList->installments);
                                                            if($instCount > 0){
                                                                $lastInstNo = $instCount - 1;
                                                                $lastInst = $loanInfoList->installments[$lastInstNo]->paid_date;
                                                                $lastInstAmount = $loanInfoList->installments[$lastInstNo]->paid_amount;
                                                            } else {
                                                                $lastInst = '--';
                                                                $lastInstAmount = '--';
                                                            }
                                                        @endphp --}}
                                                        {{-- <td>{{$loanInfoList->loanInstallments[0]->installment_date ?? '--'}}</td>
                                        --}}
                                                        {{-- <td>{{$lastInst}}</td>
                                        <td>{{$lastInstAmount}}</td> --}}
                                                        <td>{{ $loanInfoList->lastInstallment->paid_date ?? '' }}</td>
                                                        <td>{{ $loanInfoList->lastInstallment->paid_amount ?? '' }}</td>
                                                        <td>{{ $loanInfoList->officer->name ?? '--' }}</td>
                                                        <td>{{ $loanInfoList->introducer->name ?? '--' }}</td>
                                                        <td>{{ number_format($loanInfoList->outstanding_amount, 2) }}</td>
                                                        <td>{{ number_format($loanInfoList->interest_amount, 2) }}</td>
                                                        <td>{{ $loanInfoList->interest_rate }}</td>
                                                        <td>{{ $loanInfoList->installment }}</td>
                                                        <td>{{ number_format($loanInfoList->total_due_amount, 2) }}</td>
                                                        @php
                                                            $paidAmount = 0;
                                                            $outstandingBalance = 0;
                                                            if (isset($loanInfoList->installments)) {
                                                                $paid = $loanInfoList->installments->sum('paid_amount');
                                                                $fine = $loanInfoList->installments->sum('fine');
                                                                $paidAmount = $paid - $fine;
                                                                $outstandingBalance =
                                                                    $loanInfoList->total_due_amount - $paidAmount;
                                                            }
                                                        @endphp
                                                        {{-- <td>{{ number_format($loanInfoList->due_paid_amount, 2) }}</td>
                                        <td>{{ number_format($loanInfoList->current_due_amount, 2) }}</td> --}}
                                                        <td>{{ number_format($paidAmount, 2) }}</td>
                                                        <td>{{ number_format($outstandingBalance, 2) }}</td>
                                                        {{-- <td>{{$loanInfoList->introducer->name ?? '--'}}</td> --}}
                                                        @php
                                                            $totalOutstandAmount += $loanInfoList->outstanding_amount;
                                                            $totalinterstAmount += $loanInfoList->interest_amount;
                                                            $totalDueAmount += $loanInfoList->total_due_amount;
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
                                            @if ($showTable == 'Yes')
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
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>{{ number_format($totalOutstandAmount, 2) }}</th>
                                                    <th>{{ number_format($totalinterstAmount, 2) }}</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>{{ number_format($totalDueAmount, 2) }}</th>
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
