@extends('layouts.admin')
@section('page-title')
    {{ __('Stock Transfer Report') }}
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
            var filename = 'Stock Transfer Report';
            if ($('#duration').val() != undefined) {
                var duration = $('#duration').val();
            } else {
                var duration = "";
            }

            if ($('#branch').length > 0) {
                var branch = $('#branch').val();
            } else {
                var branch = "";
            }

            $('#report-dataTable').DataTable({
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excel',
                    title: filename + branch + duration,
                    footer: true
                }, {
                    extend: 'print',
                    title: filename + branch + duration,
                    footer: true
                }, {
                    extend: 'csv',
                    title: filename + branch + duration,
                    footer: true
                }]
            });
        });
    </script>
@endpush
@section('content')
    <section class="section">
        {{-- <div class="section-header">
            <h1>{{__('Stock Transfer Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Cash Sales & Hire Sales')}}</div>
        <div class="breadcrumb-item">{{__('Reports')}}</div>
        <div class="breadcrumb-item">{{__('Stock Transfer Report')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Stock Transfer Report') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Cash Sales & Hire Sales') }}</div>
                                    <div class="breadcrumb-item">{{ __('Reports') }}</div>
                                    <div class="breadcrumb-item">{{ __('Stock Transfer Report') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('stock_transfers_report.search') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4 class="h4 mb-0">Filter</h4>
                                    </div>
                                    {{-- <div class="col-md-2">
                                        </div> --}}
                                    <div class="col-md-2">
                                        <label for="start_date">Start Date</label>
                                        <input class="form-control font-style" name="start_date" type="date"
                                            value="{{ $start_date ?? '' }}" id="start_date" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_date">End Date</label>
                                        <input class="form-control font-style" name="end_date" type="date"
                                            value="{{ $end_date ?? '' }}" id="end_date" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="branch">Branch</label>
                                        @if (isset($branch))
                                            @if ($branch != '')
                                                <input type="hidden" id="branch"
                                                    value="{{ __(' Of ') . App\Branch::branchName($branch)[0] }}">
                                            @endif
                                        @endif
                                        {{ Form::select('branch_id', $branches, $branch ?? '', ['class' => 'form-control font-style']) }}
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
                                    <table class="table table-flush tbl-scrl" id="report-dataTable" data-ordering="false">
                                        <thead class="thead-light">
                                            <tr>
                                                <th> {{ __('Transfer No') }}</th>
                                                <th> {{ __('Transfer Date') }}</th>
                                                <th> {{ __('Transfer Time') }}</th>
                                                <th> {{ __('Transfer From') }}</th>
                                                <th> {{ __('Transfer To') }}</th>
                                                <th> {{ __('Transfer By') }}</th>
                                                <th> {{ __('Approved By') }}</th>

                                                <th> {{ __('Product Category') }}</th>
                                                <th> {{ __('Item') }}</th>
                                                <th> {{ __('QTY') }}</th>
                                                <th> {{ __('Purchase Price') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Sales Price') }} ({{ $currencySymbol }})</th>

                                                <th> {{ __('Total Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Taken Over Name') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($showTable == 'Yes')
                                                <input type="hidden" id="duration"
                                                    value="{{ __(' - From ') . Auth::user()->dateFormat($start_date) . __(' To ') . Auth::user()->dateFormat($end_date) }}">
                                                @php
                                                    $totalAmount = 0;
                                                @endphp
                                                @foreach ($transfers as $transfer)
                                                    <tr>
                                                        {{-- <td><button class="btn font-style btn-outline-primary orderModel" type="button" data-id="{{$loanInfoList->id}}">{{ \Auth::user()->loanNumberFormat($loanInfoList->id) }}</button>
                                            </td> --}}
                                                        <td>{{ \Auth::user()->stockTransferNumberFormat($transfer->id) }}
                                                        </td>
                                                        <td>{{ Auth::user()->dateFormat($transfer->date) }}</td>
                                                        <td>{{ $transfer->created_at->format('h:ia') }}</td>
                                                        <td>{{ App\Branch::branchName($transfer->branch_id)[0] }}</td>
                                                        <td>{{ App\Branch::branchName($transfer->transfer_to)[0] }}</td>
                                                        <td>{{ $transfer->user->name ?? '--' }}</td>
                                                        <td></td>

                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>

                                                        <td>{{ number_format($transfer->total_amount, 2) }}</td>
                                                        <td></td>
                                                    </tr>
                                                    @foreach ($transfer->stockTransItems as $items)
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>

                                                            <td>{{ $items->products->productCategory->name ?? '' }}</td>
                                                            <td>{{ $items->products->productBrand->name ?? '' }} -
                                                                {{ $items->products->name ?? '' }}</td>
                                                            <td>{{ $items->quantity }}</td>
                                                            <td>{{ $items->purchase_price }}</td>
                                                            <td>{{ $items->sale_price }}</td>

                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                    @endforeach
                                                    @php
                                                        $totalAmount += $transfer->total_amount;
                                                    @endphp
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            @if ($showTable == 'Yes')
                                                <tr>
                                                    <th></th>
                                                    <th></th>
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
                                                    <th>{{ number_format($totalAmount, 2) }}</th>
                                                    <th></th>
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
