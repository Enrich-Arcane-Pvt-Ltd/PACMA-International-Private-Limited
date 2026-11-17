@extends('layouts.admin')
@section('page-title')
    {{ __('Hire Sales Repayment Sheet') }}
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

        /* @page { size: landscape; } */
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
            var filename = 'Hire Sales Repayment Sheet';
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
            <h1>{{__('Hire Sales Repayment Sheet')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                <div class="breadcrumb-item">{{__('Cash Sales & Hire Sales')}}</div>
                <div class="breadcrumb-item">{{__('Reports')}}</div>
                <div class="breadcrumb-item">{{__('Hire Sales Repayment Sheet')}}</div>
            </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Hire Sales Repayment Sheet') }}</h4>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Cash Sales & Hire Sales') }}</div>
                                <div class="breadcrumb-item">{{ __('Reports') }}</div>
                                <div class="breadcrumb-item">{{ __('Hire Sales Repayment Sheet') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('sales_repayment_sheet.search') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4 class="h4 mb-0">Filter</h4>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="start_month">Month</label>
                                        <input class="form-control font-style" name="month" type="month"
                                            value="{{ $month ?? '' }}" id="month" required>
                                    </div>
                                    {{-- <div class="col-md-2">
                                            <label for="end_month">End Date</label>
                                            <input class="form-control font-style" name="end_date" type="date" value="{{ $end_date ?? '' }}" id="end_date" required>
                                        </div> --}}
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
                                        <label for="oficcer_id">Officer</label>
                                        {{ Form::select('oficcer_id', $oficcers, $loanOficcer ?? '', ['id' => 'oficcer_id', 'class' => 'form-control font-style']) }}
                                    </div>
                                </div>
                                <div class="row mt-3 mb-3" style="float: right;">
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
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between w-100" style="overflow-x: auto;">
                                    <table class="table table-flush tbl-scrl" id="report-dataTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th> {{ __('Sale No') }}</th>
                                                <th> {{ __('Customer No') }}</th>
                                                <th> {{ __('Customer Name') }}</th>
                                                <th> {{ __('Customer Phone No') }}</th>
                                                <th> {{ __('Product Category') }}</th>
                                                <th> {{ __('Item') }}</th>
                                                <th> {{ __('Loan Date') }}</th>
                                                <th> {{ __('Loan Expiry Date') }}</th>
                                                <th> {{ __('Installment Date') }}</th>
                                                <th> {{ __('Due Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Salesman') }}</th>
                                                <th> {{ __('Officer') }}</th>
                                                <th> {{ __('Loan Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Loan With Interest') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Total Paid Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Balance') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Last Paid Date') }}</th>
                                                <th> {{ __('Last Paid Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Remarks') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($showTable == 'Yes')
                                                {{-- <input type="hidden" id="duration" value="{{ __(' - From ').Auth::user()->dateFormat($start_date).__(' To ').Auth::user()->dateFormat($end_date) }}"> --}}
                                                <input type="hidden" id="duration"
                                                    value="{{ __(' - From ') . Auth::user()->monthFormat($month) }}">
                                                @foreach ($saleInfoLists as $saleInfoList)
                                                    <tr>
                                                        {{-- <td><button class="btn font-style btn-outline-primary orderModel" type="button" data-id="{{$saleInfoList->id}}">{{ \Auth::user()->saleNumberFormat($saleInfoList->id) }}</button></td> --}}
                                                        <td>{{ $saleInfoList->sale_id }}</td>
                                                        <td>
                                                            @if (isset($saleInfoList->customer))
                                                                {{ Auth::user()->customerNumberFormat($saleInfoList->customer->id) }}
                                                            @else
                                                                --
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($saleInfoList->customer->title == 0)
                                                                Mr.
                                                            @elseif($saleInfoList->customer->title == 1)
                                                                Mrs.
                                                            @else
                                                                Miss.
                                                            @endif
                                                            {{ $saleInfoList->customer->name ?? '--' }}
                                                        </td>
                                                        <td>{{ $saleInfoList->customer->phone ?? '' }}</td>
                                                        <td>{{ $saleInfoList->products->productCategory->name ?? '' }}</td>
                                                        <td>{{ $saleInfoList->products->name ?? '' }}</td>
                                                        <td>{{ $saleInfoList->date_of_sale ?? '--' }}</td>
                                                        <td>{{ $saleInfoList->due_date ?? '--' }}</td>
                                                        <td>{{ $saleInfoList->installment_date ?? '--' }}</td>
                                                        {{-- <td></td> --}}
                                                        <td>
                                                            @if (isset($saleInfoList->installments[0]->total_amount_to_pay))
                                                                {{ number_format($saleInfoList->installments[0]->total_amount_to_pay, 2) }}
                                                            @else
                                                                {{ number_format($saleInfoList->monthly_installment, 2) }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $saleInfoList->introducer->name ?? '--' }}</td>
                                                        <td>{{ $saleInfoList->officer->name ?? '--' }}</td>
                                                        <td>{{ number_format($saleInfoList->outstanding_amount, 2) }}</td>
                                                        <td>{{ number_format($saleInfoList->total_due_amount, 2) }}</td>
                                                        <td>{{ number_format($saleInfoList->due_paid_amount, 2) }}</td>
                                                        <td>{{ number_format($saleInfoList->current_due_amount, 2) }}</td>
                                                        <td>{{ $saleInfoList->lastInstallment->paid_date ?? '' }}</td>
                                                        <td>
                                                            @if (isset($saleInfoList->lastInstallment->paid_amount))
                                                                {{ number_format($saleInfoList->lastInstallment->paid_amount, 2) }}
                                                            @endif
                                                        </td>
                                                        <td></td>
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
                        for (let i = 0; i < data.length; i++) {
                            let element = data[i].amount;
                            if(data[i].status == 1){
                                status = "Paid";
                            }else if(data[i].status == 2){
                                status = "Not Paid";
                            }else if(data[i].status == 3){
                                status = "Over Due";
                            }

                            var paidAmount = 0.00;
                            if(data[i].paid_amount != null){
                                paidAmount = data[i].paid_amount;
                            }

                            var no = i+1;
                            // row+="<tr><td>"+data[i].id+"</td><td>"+data[i].amount+"</td><td>"+data[i].branch_id+"</td><td>"+data[i].paid_amount+"</td><td>"+data[i].installment_date+"</td><td>"+status+"</td></tr>";
                            row+="<tr><td>"+ no +"</td><td>"+data[i].amount+"</td><td>"+data[i].total_amount_to_pay+"</td><td>"+paidAmount+"</td><td>"+data[i].installment_date+"</td><td>"+status+"</td></tr>";

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
                        for(i = 0; i < data.length; i++){
                            $('#group_id').append('<option value="'+data[i].id+'">'+data[i].name+'</option>');
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
