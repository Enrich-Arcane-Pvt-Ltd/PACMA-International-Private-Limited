@extends('layouts.admin')
@section('page-title')
    {{ __('Cash Collector Report') }}
@endsection
@push('script-page')
    <style>
        table tfoot {
            display: table-row-group;
        }

        .card .card-header .btn:not(.note-btn) {
            border-radius: 30px;
            background-color: #00AF6B !important;
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
            var filename = 'Cash Collector Report';
            if ($('#duration').val() != undefined) {
                var duration = $('#duration').val();
            } else {
                var duration = "";
            }

            if ($('#oficcer').length > 0) {
                var oficcer = $('#oficcer').val();
            } else {
                var oficcer = "";
            }

            $('#report-dataTable').DataTable({
                order: [
                    [3, "asc"]
                ],
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excel',
                    title: filename + oficcer + duration,
                    footer: true
                }, {
                    extend: 'print',
                    title: function() {
                        return "<h3>" + filename + oficcer + duration + "</h3>";
                    },
                    footer: true
                }, {
                    extend: 'csv',
                    title: filename + oficcer + duration,
                    footer: true
                }]
            });
        });
    </script>
@endpush
@section('content')
    <section class="section">
        {{-- <div class="section-header">
            <h1>{{__('Cash Collector Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Report')}}</div>
        <div class="breadcrumb-item">{{__('Cash Collector Report')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Collector Report') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Report') }}</div>
                                    <div class="breadcrumb-item">{{ __('Cash Collector Report') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('cash_collector_report.search') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <h4 class="h4 mb-0">Filter</h4>
                                    </div>
                                    {{-- <div class="col-md-2">
                                            <label for="start_month">Month</label>
                                            <input class="form-control font-style" name="month" type="month" value="{{ $month ?? '' }}"
                                id="month" required>
                            </div> --}}
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
                                            <option @if ($category == 1) selected @endif value="1">Hire
                                                Sales</option>
                                            <option @if ($category == 2) selected @endif value="2">Loans
                                            </option>
                                            <option @if ($category == 3) selected @endif value="3">Pawning
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" id="product_category_div">
                                        <label for="loan_type">Sub Category</label>
                                        @if (isset($product_category))
                                            @if ($product_category != '')
                                                <input type="hidden" id="product_category"
                                                    value="{{ __(' Of ') . App\ProductCategory::categoryName($product_category)[0] }}">
                                            @endif
                                        @endif
                                        {{ Form::select('product_category_id', $productCategories, $product_category ?? '', ['class' => 'form-control font-style']) }}
                                    </div>
                                    <div class="col-md-2 d-none" id="loan_type_div">
                                        <label for="loan_type">Loan Type</label>
                                        <select class="form-control font-style" name="loan_type" id="loan_type">
                                            <option @if ($loanType == '') selected @endif value="">-- All
                                                --</option>
                                            <option @if ($loanType == 1) selected @endif value="1">SME
                                                Loan</option>
                                            <option @if ($loanType == 2) selected @endif value="2">Group
                                                Loan</option>
                                            <option @if ($loanType == 3) selected @endif value="3">Vehicle
                                                Loan</option>
                                            <option @if ($loanType == 4) selected @endif value="4">Land
                                                Loan</option>
                                            <option @if ($loanType == 5) selected @endif value="4">
                                                Employment Loan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 " id="center_div">
                                        <label for="center_id">Branch</label>
                                        {{ Form::select('center_id', $centers, null, ['id' => 'center_id', 'class' => 'form-control font-style']) }}
                                    </div>
                                    <div class="col-md-2 d-none" id="group_div">
                                        <label for="group_id">Group</label>
                                        <select name="group_id" id="group_id" class="form-control font-style">
                                            <option value="">Select Group</option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-none" id="collection_type_div">
                                        <label for="collection_type">Collection Type</label>
                                        <select class="form-control font-style" name="collection_type" id="collection_type">
                                            <option @if ($loanCollectionType == '') selected @endif value="">-- All
                                                --</option>
                                            <option @if ($loanCollectionType == 3) selected @endif value="3">
                                                Monthly</option>
                                            <option @if ($loanCollectionType == 2) selected @endif value="2">
                                                Weekly</option>
                                            <option @if ($loanCollectionType == 1) selected @endif value="1">Daily
                                            </option>
                                        </select>
                                    </div>
                                    {{-- <div class="col-md-2">
                                            <label for="end_month">End Date</label>
                                            <input class="form-control font-style" name="end_date" type="date" value="{{ $end_date ?? '' }}"
                            id="end_date" required>
                        </div> --}}
                                    {{-- <div class="col-md-2">
                                            <label for="loan_type">Category</label>
                                            @if (isset($productCategory))
                                                @if ($productCategory != '')
                                                    <input type="hidden" id="category" value="{{ __(' Of ').App\ProductCategory::categoryName($productCategory)[0] }}">
                        @endif
                        @endif
                        {{ Form::select('category_id', $productCategories, $productCategory ?? '', array('class' => 'form-control font-style')) }}
                </div> --}}
                                    <div class="col-md-2">
                                        <label for="oficcer_id">Officer</label>
                                        @if (isset($loanOficcer))
                                            @if ($loanOficcer != '')
                                                <input type="hidden" id="oficcer"
                                                    value="{{ __(' Of ') . Auth::user()->otherUserName($loanOficcer) }}">
                                            @endif
                                        @endif
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
                                    <table class="table table-flush" id="report-dataTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th> {{ __('#') }}</th>
                                                <th> {{ __('Customer No') }}</th>
                                                <th> {{ __('Customer Name') }}</th>
                                                <th> {{ __('Phone No') }}</th>
                                                <th> {{ __('Installment Date') }}</th>
                                                <th> {{ __('Due Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Balance') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Paid Date') }}</th>
                                                <th> {{ __('Paid Amount') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Remarks') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($showTable == 'Yes')
                                                <input type="hidden" id="duration"
                                                    value="{{ __(' - From ') . Auth::user()->dateFormat($start_date) . __(' To ') . Auth::user()->dateFormat($end_date) }}">
                                                {{-- <input type="hidden" id="duration" value="{{ __(' - From ').Auth::user()->monthFormat($month) }}">
                                    --}}
                                                @foreach ($saleInfoLists as $saleInfoList)
                                                    @foreach ($saleInfoList->installments as $saleInst)
                                                        <tr>
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
                                                            <td>{{ $saleInst->installment_date ?? '--' }}</td>
                                                            <td>
                                                                @if (isset($saleInst->total_amount_to_pay))
                                                                    {{ number_format($saleInst->total_amount_to_pay, 2) }}
                                                                @endif
                                                            </td>
                                                            <td>{{ number_format($saleInfoList->current_due_amount, 2) }}
                                                            </td>
                                                            <td>{{ $saleInst->paid_date ?? '' }}</td>
                                                            <td>
                                                                @if (isset($saleInst->paid_amount))
                                                                    {{ number_format($saleInst->paid_amount, 2) }}
                                                                @endif
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                                @foreach ($loanInfoLists as $loanInfoList)
                                                    @foreach ($loanInfoList->loanInstallments as $loanInst)
                                                        <tr>
                                                            <td>{{ Auth::user()->loanNumberFormat($loanInfoList->id) }}
                                                            </td>
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
                                                            <td>{{ $loanInst->customer->phone ?? '' }}</td>
                                                            <td>{{ $loanInst->installment_date ?? '--' }}</td>
                                                            <td>
                                                                @if (isset($loanInst->total_amount_to_pay))
                                                                    {{ number_format($loanInst->total_amount_to_pay, 2) }}
                                                                @endif
                                                            </td>
                                                            <td>{{ number_format($loanInfoList->current_due_amount, 2) }}
                                                            </td>
                                                            <td>{{ $loanInst->paid_date ?? '' }}</td>
                                                            <td>
                                                                @if (isset($loanInst->paid_amount))
                                                                    {{ number_format($loanInst->paid_amount, 2) }}
                                                                @endif
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                                @foreach ($pawningInfoLists as $pawningInfoList)
                                                    @foreach ($pawningInfoList->installments as $pawnInst)
                                                        <tr>
                                                            <td>{{ Auth::user()->pawningNumberFormat($pawningInfoList->id) }}
                                                            </td>
                                                            <td>
                                                                @if (isset($pawningInfoList->customer))
                                                                    {{ Auth::user()->customerNumberFormat($pawningInfoList->customer->id) }}
                                                                @else
                                                                    --
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($pawningInfoList->customer->title == 0)
                                                                    Mr.
                                                                @elseif($pawningInfoList->customer->title == 1)
                                                                    Mrs.
                                                                @else
                                                                    Miss.
                                                                @endif
                                                                {{ $pawningInfoList->customer->name ?? '--' }}
                                                            </td>
                                                            <td>{{ $pawnInst->customer->phone ?? '' }}</td>
                                                            <td>{{ $pawnInst->installment_date ?? '--' }}</td>
                                                            <td>
                                                                @if (isset($pawnInst->total_amount_to_pay))
                                                                    {{ number_format($pawnInst->total_amount_to_pay, 2) }}
                                                                @endif
                                                            </td>
                                                            <td>{{ number_format($pawningInfoList->current_due_amount, 2) }}
                                                            </td>
                                                            <td>{{ $pawnInst->paid_date ?? '' }}</td>
                                                            <td>
                                                                @if (isset($pawnInst->paid_amount))
                                                                    {{ number_format($pawnInst->paid_amount, 2) }}
                                                                @endif
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    @endforeach
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    var loanType = < ? php echo json_encode($loanType); ? > ;
    var loanCenter = < ? php echo json_encode($loanCenter); ? > ;
    var loanGroup = < ? php echo json_encode($loanGroup); ? > ;
    var category = < ? php echo json_encode($category); ? > ;
    var loanCollectionType = < ? php echo json_encode($loanCollectionType); ? > ;
    var product_category = < ? php echo json_encode($product_category); ? > ;

    $(document).ready(function() {

        if (category == 1) {
            $('#product_category_div').show();
            $('#loan_type_div').hide();
            $('#center_div').hide();
            $('#group_div').hide();
            $('#collection_type_div').hide();
        } else if (category == 2) {
            $('#product_category_div').hide();
            $('#loan_type_div').show();
            $('#collection_type_div').show();
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
        } else if (category == 3) {
            $('#product_category_div').hide();
            $('#loan_type_div').hide();
            $('#center_div').hide();
            $('#group_div').hide();
            $('#collection_type_div').show();
        } else {
            $('#product_category_div').hide();
            $('#loan_type_div').hide();
            $('#center_div').hide();
            $('#group_div').hide();
            $('#collection_type_div').hide();
        }

        //   if(loanType == 2){
        //     $('#center_div').show();
        //     $('#center_id').val(loanCenter).trigger('change');
        //         if(loanCenter != ''){
        //             $('#group_div').show();
        //             $('#group_id').val(loanGroup);
        //         }
        //   } else {
        //     $('#center_div').hide();
        //     $('#group_div').hide();
        //   }

        $('#category').change(function() {
            var categoryType = $(this).val();
            if (categoryType == 1) {
                $('#product_category_div').show(200);
                $('#loan_type_div').hide(200);
                $('#center_div').hide(200);
                $('#group_div').hide(200);
                $('#collection_type_div').hide(200);

                // $('#product_category_id').val('');
                $('#loan_type').val('');
                $('#center_id').val('');
                $('#group_id').val('');
                $('#collection_type').val('');

            } else if (categoryType == 2) {
                $('#product_category_div').hide(200);
                $('#loan_type_div').show(200);
                $('#center_div').hide(200);
                $('#group_div').hide(200);
                $('#collection_type_div').show(200);

                $('#product_category_id').val('');
                // $('#loan_type').val('');
                $('#center_id').val('');
                $('#group_id').val('');
                // $('#collection_type').val('');

            } else if (categoryType == 3) {
                $('#product_category_div').hide(200);
                $('#loan_type_div').hide(200);
                $('#center_div').hide(200);
                $('#group_div').hide(200);
                $('#collection_type_div').show(200);

                $('#product_category_id').val('');
                $('#loan_type').val('');
                $('#center_id').val('');
                $('#group_id').val('');
                // $('#collection_type').val('');
            } else {
                $('#product_category_div').hide(200);
                $('#loan_type_div').hide(200);
                $('#center_div').hide(200);
                $('#group_div').hide(200);
                $('#collection_type_div').hide(200);

                $('#product_category_id').val('');
                $('#loan_type').val('');
                $('#center_id').val('');
                $('#group_id').val('');
                $('#collection_type').val('');
            }
        });

        $('#loan_type').change(function() {
            var type = $(this).val();
            if (type == 2) {
                $('#center_div').show(200);
            } else {
                $('#center_div').hide(200);
                $('#center_id').val('').trigger('change');
            }
        });

        $('#center_id').change(function() {
            var centerId = $(this).val();
            if (centerId != '') {
                $.ajax({
                    url: "{{ url('loan_outstanding_report/get_groups') }}/" + centerId,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
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
