@extends('layouts.admin')
@section('page-title')
    {{ __('Item Wise In & Out Stock Report') }}
@endsection
@push('script-page')
    <style>
        table tfoot {
            display: table-row-group;
        }

        .customer-sel.form-control:not(.form-control-sm):not(.form-control-lg) {
            padding: 0px;
        }

        .btn-light {
            background-color: #fdfdff !important;
            font-size: 13px;
            font-weight: 400;
            color: var(--bs-body-color);
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
            // var filename = 'Item Wise In & Out Stock Report';
            if ($('#file_name').length > 0) {
                var filename = $('#file_name').html();
                var ecxelFilename = $('#heading1').val() + ' - ' + $('#heading2').val() + ' - ' + $('#heading3')
                    .val();
            } else {
                var filename = "";
                var ecxelFilename = "";
            }

            if ($('#duration').val() != undefined) {
                var duration = $('#duration').val();
            } else {
                var duration = "";
            }

            // if($('#category').length > 0 ){
            //     var category = $('#category').val();
            // } else{
            //     var category = "";
            // }

            // if($('#branch').length > 0 ){
            //     var branch = $('#branch').val();
            // } else{
            //     var branch = "";
            // }

            $('#report-dataTable').DataTable({
                dom: 'lBfrtip',
                "ordering": false,
                buttons: [{
                    extend: 'excel',
                    title: ecxelFilename + duration,
                    footer: true
                }, {
                    extend: 'print',
                    title: function() {
                        return "<h4>" + filename + duration + "</h4>";
                    },
                    footer: true
                }, {
                    extend: 'csv',
                    title: ecxelFilename + duration,
                    footer: true
                }]
            });
        });
    </script>
@endpush
@section('content')
    <section class="section">
        {{-- <div class="section-header">
            <h1>{{__('Item Wise In & Out Stock Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Cash Sales & Hire Sales')}}</div>
        <div class="breadcrumb-item">{{__('Reports')}}</div>
        <div class="breadcrumb-item">{{__('Item Wise In & Out Stock Report')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Item Wise In & Out Stock Report') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Cash Sales & Hire Sales') }}</div>
                                    <div class="breadcrumb-item">{{ __('Reports') }}</div>
                                    <div class="breadcrumb-item">{{ __('Item Wise In & Out Stock Report') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('stock_item_in_out_report.search') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4 class="h4 mb-0">Filter</h4>
                                    </div>
                                    {{-- <div class="col-md-2">
                                        </div> --}}

                                    <div class="col-md-3">
                                        <label for="product">Product</label>
                                        <select class="form-control customer-sel font-style selectpicker" name="product"
                                            id="product" data-live-search="true" required>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option @if (isset($productId) && $productId == $product->id) selected @endif
                                                    value="{{ $product->id }}">
                                                    {{ $product->barcode . ' - ' . $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- <div class="col-md-2">
                                            <label for="category">Category</label>
                                            @if (isset($category))
                                                @if ($category != '')
                                                    <input type="hidden" id="category" value="{{ __(' Of ').App\ProductCategory::categoryName($category)[0] }}">
                                @endif
                                @endif
                                {{ Form::select('category_id', $productCategories, $category ?? '', array('class' => 'form-control font-style')) }}
                            </div> --}}

                                    <div class="col-md-2">
                                        <label for="start_date">Start Purchase Date</label>
                                        <input class="form-control font-style" name="start_date" type="date"
                                            value="{{ $start_date ?? '' }}" id="start_date">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_month">End Purchase Date</label>
                                        <input class="form-control font-style" name="end_date" type="date"
                                            value="{{ $end_date ?? '' }}" id="end_date">
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
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-wrap">
                                <div class="card-body">
                                    <div class="row">
                                        @if ($showTable == 'Yes')
                                            <input type="hidden" id="heading1" value="Item Wise In & Out Stock Report">
                                            <input type="hidden" id="heading2"
                                                value="Product Category : {{ $productDetail->productCategory->name ?? '' }}">
                                            <input type="hidden" id="heading3" value="Item : {{ $productDetail->name }}">

                                            <div class="col" id="file_name">
                                                <h4>{{ __('Item Wise In & Out Stock Report') }}</h4>
                                                <h6>{{ __('Product Category') }} :
                                                    {{ $productDetail->productCategory->name ?? '' }}
                                                </h6>
                                                <h6>{{ __('Item') }} : {{ $productDetail->name }}</h6>
                                            </div>

                                            <input type="hidden" id="duration"
                                                value="@if (isset($start_date) && $start_date != '') {{ __(' From ') . Auth::user()->dateFormat($start_date) }} @endif @if (isset($end_date) && $end_date != '') {{ __(' To ') . Auth::user()->dateFormat($end_date) }} @endif ">

                                            {{-- <div class="col">

                                                </div>

                                                <div class="col">

                                                </div>

                                                <div class="col">
                                                    <input type="hidden" id="duration" value="{{ __(' - From ').Auth::user()->dateFormat($start_date).__(' To ').Auth::user()->dateFormat($end_date) }}">
                                <h6>{{__('Duration')}} : {{ $start_date . " TO " .$end_date }} </h6>
                            </div> --}}
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

                                    <style>
                                        .table .thead-light th {
                                            border-right: 2px solid #ccc;
                                        }

                                        .table .thead-light th:last-child {
                                            border-right: none;
                                        }

                                        .table .thead-light tr:first-child th {
                                            border-right: 2px solid #7e7e7e;
                                        }

                                        .table .thead-light tr:first-child th:last-child {
                                            border-right: none;
                                        }

                                        .table .thead-light th:nth-child(5),
                                        .table .thead-light th:nth-child(9),
                                        .table .thead-light th:nth-child(11) {
                                            border-right: 2px solid #7e7e7e;
                                        }

                                        .table tbody td {
                                            border-right: 2px solid #ccc;
                                        }

                                        .table tbody td:last-child {
                                            border-right: none;
                                        }

                                        .table tbody td:nth-child(5),
                                        .table tbody td:nth-child(9),
                                        .table tbody td:nth-child(11) {
                                            border-right: 2px solid #7e7e7e;
                                        }
                                    </style>

                                    <table class="table table-flush" id="report-dataTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th colspan="5" class="text-center ">In</th>
                                                <th colspan="4" class="text-center  ">Out</th>
                                                <th colspan="2" class="text-center  ">Balance</th>
                                                <th class="text-center  ">Remarks</th>
                                            </tr>
                                            <tr>
                                                <th> {{ __('Purchasing Date') }}</th>
                                                <th> {{ __('Purchasing Number') }}</th>
                                                <th> {{ __('Purchasing Price Buying') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Total Qty') }}</th>
                                                <th> {{ __('Total Purchase Price') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Selling Date') }}</th>
                                                <th> {{ __('Invoice Number') }}</th>
                                                <th> {{ __('QTY') }}</th>
                                                <th> {{ __('Total Purchase Price') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Purchase Price') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Remaining Qty') }}</th>
                                                <th> {{ __('Remarks') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($showTable == 'Yes')
                                                @foreach ($invenvtories as $inventory)
                                                    <tr>
                                                        <td>{{ $inventory->date }}</td>
                                                        <td>{{ Auth::user()->purchaseOrderNumberFormat($inventory->grns->order_id) }}
                                                        </td>
                                                        <td>{{ number_format($inventory->purchase_price, 2) }}</td>
                                                        <td>{{ $inventory->grn_qty }}</td>
                                                        <td>
                                                            {{ number_format($inventory->purchase_price * $inventory->grn_qty, 2) }}
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>
                                                            {{ number_format($inventory->purchase_price * $inventory->grn_qty, 2) }}
                                                        </td>
                                                        <td>{{ $inventory->grn_qty }}</td>
                                                        <td "></td>
                                                                                                                                                                    </tr>
                                                                                                                                                                    @php
                                                                                                                                                                        $balancePurchasePrice =
                                                                                                                                                                            $inventory->purchase_price *
                                                                                                                                                                            $inventory->grn_qty;
                                                                                                                                                                        $balanceQty =
                                                                                                                                                                            $inventory->grn_qty;
                                                                                                                                                                    @endphp





                                                                      @foreach ($inventory->sales as $sale)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $sale->date_of_sale }}</td>
                                                        <td>{{ Auth::user()->invoiceNumberFormat($sale->invoice_id) }}
                                                        </td>
                                                        <td>{{ $sale->quantity }}</td>
                                                        <td>
                                                            @if ($sale->purchase_price != null)
                                                                {{ number_format($sale->purchase_price * $sale->quantity, 2) }}
                                                            @else
                                                                {{ number_format($inventory->purchase_price * $sale->quantity, 2) }}
                                                            @endif
                                                        </td>
                                                        @php
                                                            if ($sale->purchase_price != null) {
                                                                $balancePurchasePrice -=
                                                                    $sale->purchase_price * $sale->quantity;
                                                            } else {
                                                                $balancePurchasePrice -=
                                                                    $inventory->purchase_price * $sale->quantity;
                                                            }
                                                            $balanceQty -= $sale->quantity;
                                                        @endphp
                                                        <td>{{ number_format($balancePurchasePrice, 2) }}</td>
                                                        <td>{{ $balanceQty }}</td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                                @foreach ($inventory->saleItems as $saleItem)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $saleItem->date_of_sale }}</td>
                                                        <td>{{ Auth::user()->invoiceNumberFormat($saleItem->invoice_id) }}
                                                        </td>
                                                        <td>{{ $saleItem->quantity }}</td>
                                                        <td>
                                                            @if ($saleItem->purchase_price != null)
                                                                {{ number_format($saleItem->purchase_price * $saleItem->quantity, 2) }}
                                                            @else
                                                                {{ number_format($inventory->purchase_price * $saleItem->quantity, 2) }}
                                                            @endif
                                                        </td>
                                                        @php
                                                            if ($saleItem->purchase_price != null) {
                                                                $balancePurchasePrice -=
                                                                    $saleItem->purchase_price * $saleItem->quantity;
                                                            } else {
                                                                $balancePurchasePrice -=
                                                                    $inventory->purchase_price * $saleItem->quantity;
                                                            }
                                                            $balanceQty -= $saleItem->quantity;
                                                        @endphp
                                                        <td>{{ number_format($balancePurchasePrice, 2) }}</td>
                                                        <td>{{ $balanceQty }}</td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach

                                                @foreach ($inventory->stockTransferItems as $transferItem)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $transferItem->stock_transfer_parent->date ?? '' }}</td>
                                                        <td>{{ Auth::user()->stockTransferNumberFormat($transferItem->stock_transfer_id) }}
                                                        </td>
                                                        <td>{{ $transferItem->quantity }}</td>
                                                        <td>
                                                            {{ number_format($transferItem->purchase_price * $transferItem->quantity, 2) }}
                                                        </td>
                                                        @php
                                                            $balancePurchasePrice -=
                                                                $transferItem->purchase_price * $transferItem->quantity;
                                                            $balanceQty -= $transferItem->quantity;
                                                        @endphp
                                                        <td>{{ number_format($balancePurchasePrice, 2) }}</td>
                                                        <td>{{ $balanceQty }}</td>
                                                        <td>{{ _('Transfers ' . $transferItem->stock_transfer_parent->branchto->name ?? '') }}
                                                            Branch</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach

                                            {{-- Inventories from Transfers --}}
                                            @foreach ($transfersInvenvtories as $inventory)
                                                <tr>
                                                    <td>{{ $inventory->date }}</td>
                                                    <td></td>
                                                    <td>{{ number_format($inventory->purchase_price, 2) }}</td>
                                                    <td>{{ number_format($inventory->purchase_price * $inventory->grn_qty, 2) }}
                                                    </td>
                                                    <td>{{ $inventory->grn_qty }}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>{{ number_format($inventory->purchase_price * $inventory->grn_qty, 2) }}
                                                    </td>
                                                    <td>{{ $inventory->grn_qty }}</td>
                                                    <td>{{ _('Purchased From ' . $inventory->transferFrom->name ?? '') }}
                                                        Branch</td>
                                                </tr>
                                                @php
                                                    $balancePurchasePrice =
                                                        $inventory->purchase_price * $inventory->grn_qty;
                                                    $balanceQty = $inventory->grn_qty;
                                                @endphp

                                                @foreach ($inventory->sales as $sale)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $sale->date_of_sale }}</td>
                                                        <td>{{ Auth::user()->invoiceNumberFormat($sale->invoice_id) }}
                                                        </td>
                                                        <td>{{ $sale->quantity }}</td>
                                                        <td>
                                                            @if ($sale->purchase_price != null)
                                                                {{ number_format($sale->purchase_price * $sale->quantity, 2) }}
                                                            @else
                                                                {{ number_format($inventory->purchase_price * $sale->quantity, 2) }}
                                                            @endif
                                                        </td>
                                                        @php
                                                            if ($sale->purchase_price != null) {
                                                                $balancePurchasePrice -=
                                                                    $sale->purchase_price * $sale->quantity;
                                                            } else {
                                                                $balancePurchasePrice -=
                                                                    $inventory->purchase_price * $sale->quantity;
                                                            }
                                                            $balanceQty -= $sale->quantity;
                                                        @endphp
                                                        <td>{{ number_format($balancePurchasePrice, 2) }}</td>
                                                        <td>{{ $balanceQty }}</td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                                @foreach ($inventory->saleItems as $saleItem)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $saleItem->date_of_sale }}</td>
                                                        <td>{{ Auth::user()->invoiceNumberFormat($saleItem->invoice_id) }}
                                                        </td>
                                                        <td>{{ $saleItem->quantity }}</td>
                                                        <td>
                                                            @if ($saleItem->purchase_price != null)
                                                                {{ number_format($saleItem->purchase_price * $saleItem->quantity, 2) }}
                                                            @else
                                                                {{ number_format($inventory->purchase_price * $saleItem->quantity, 2) }}
                                                            @endif
                                                        </td>
                                                        @php
                                                            if ($saleItem->purchase_price != null) {
                                                                $balancePurchasePrice -=
                                                                    $saleItem->purchase_price * $saleItem->quantity;
                                                            } else {
                                                                $balancePurchasePrice -=
                                                                    $inventory->purchase_price * $saleItem->quantity;
                                                            }
                                                            $balanceQty -= $saleItem->quantity;
                                                        @endphp
                                                        <td>{{ number_format($balancePurchasePrice, 2) }}</td>
                                                        <td>{{ $balanceQty }}</td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach

                                                @foreach ($inventory->stockTransferItems as $transferItem)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $transferItem->stock_transfer_parent->date ?? '' }}</td>
                                                        <td>{{ Auth::user()->stockTransferNumberFormat($transferItem->stock_transfer_id) }}
                                                        </td>
                                                        <td>{{ $transferItem->quantity }}</td>
                                                        <td>
                                                            {{ number_format($transferItem->purchase_price * $transferItem->quantity, 2) }}
                                                        </td>
                                                        @php
                                                            $balancePurchasePrice -=
                                                                $transferItem->purchase_price * $transferItem->quantity;
                                                            $balanceQty -= $transferItem->quantity;
                                                        @endphp
                                                        <td>{{ number_format($balancePurchasePrice, 2) }}</td>
                                                        <td>{{ $balanceQty }}</td>
                                                        <td>{{ _('Transfers ' . $transferItem->stock_transfer_parent->branchto->name ?? '') }}
                                                            Branch</td>
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
