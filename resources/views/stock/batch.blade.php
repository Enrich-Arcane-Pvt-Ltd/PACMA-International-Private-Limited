@extends('layouts.admin')
@section('page-title', __('Stock Batch'))
@section('content')
<section class="section">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Batch B{{ str_pad($batch->id, 6, '0', STR_PAD_LEFT) }} — {{ optional($batch->products)->name }}</h4>
            <button type="button" class="btn btn-secondary btn-sm no-print" onclick="window.print()">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="p-2 border rounded bg-light h-100">
                        <small class="text-muted d-block">Branch</small>
                        <strong>{{ optional($batch->branch)->name ?? '-' }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="p-2 border rounded bg-light h-100">
                        <small class="text-muted d-block">GRN</small>
                        <strong>{{ $batch->grn_id }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="p-2 border rounded bg-light h-100">
                        <small class="text-muted d-block">Supplier Invoice</small>
                        <strong>{{ $batch->supplier_invoice_number ?? '-' }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="p-2 border rounded bg-light h-100">
                        <small class="text-muted d-block">Received Date</small>
                        <strong>{{ $batch->date }}</strong>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-2 col-sm-4 mb-2">
                    <div class="p-2 border rounded h-100">
                        <small class="text-muted d-block">Received Qty</small>
                        <strong>{{ $batch->grn_qty }}</strong>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-2">
                    <div class="p-2 border rounded h-100">
                        <small class="text-muted d-block">Remaining Qty</small>
                        <strong>{{ $batch->remain_qty }}</strong>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-2">
                    <div class="p-2 border rounded h-100">
                        <small class="text-muted d-block">Cost</small>
                        <strong>{{ number_format($batch->purchase_price, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-2">
                    <div class="p-2 border rounded h-100">
                        <small class="text-muted d-block">Cash Price</small>
                        <strong>{{ number_format($batch->sale_price, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-2">
                    <div class="p-2 border rounded h-100">
                        <small class="text-muted d-block">Retail Price</small>
                        <strong>{{ number_format($batch->hire_price, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-2">
                    <div class="p-2 border rounded h-100">
                        <small class="text-muted d-block">Stock Value</small>
                        <strong>{{ number_format($batch->remain_qty * $batch->purchase_price, 2) }}</strong>
                    </div>
                </div>
            </div>

            <form method="GET" class="d-flex flex-wrap align-items-end mb-3 no-print" style="gap: 10px;">
                <div>
                    <label class="small font-weight-bold mb-1 d-block">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div>
                    <label class="small font-weight-bold mb-1 d-block">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>
                <div class="d-flex" style="gap: 8px;">
                    <button class="btn btn-primary">Filter</button>
                    <a class="btn btn-secondary" href="{{ route('stock.batch', $batch->id) }}">Reset</a>
                </div>
            </form>

            <div class="p-3 mb-2 border rounded bg-light d-flex flex-wrap align-items-center" style="gap: 18px;">
                <span class="font-weight-bold">Sales in selected period:</span>
                <span>Cash: <strong>{{ $cashRows->sum('quantity') + $singleRows->where('sale_type', 0)->sum('quantity') }}</strong></span>
                <span>Hire: <strong>{{ $singleRows->where('sale_type', 1)->sum('quantity') }}</strong></span>
                <span>Transfers out: <strong>{{ $movements->where('stock_master_id', 3)->sum('quantity') }}</strong></span>
                <span>Returns: <strong>{{ $movements->where('stock_master_id', 5)->sum('quantity') }}</strong></span>
            </div>
            <small class="text-muted d-block mb-3">Current remaining is today's balance. Date filters apply to sales and movement history below. Older sales spanning multiple inventory records require reconciliation before their allocation can be treated as confirmed.</small>

            <h5 class="mt-4 mb-2 font-weight-bold">Sales / Invoices</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Sale</th>
                            <th>Invoice</th>
                            <th>Type</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Sale price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cashRows as $row)
                            <tr>
                                <td>{{ $row->date_of_sale }}</td>
                                <td><a href="{{ route('sale.view', $row->sale_id) }}">#{{ $row->sale_id }}</a></td>
                                <td>{{ optional($row->invoice)->invoice_code ?? $row->invoice_id }}</td>
                                <td>Cash</td>
                                <td class="text-right">{{ $row->quantity }}</td>
                                <td class="text-right">{{ number_format($row->unit_price, 2) }}</td>
                            </tr>
                        @endforeach
                        @foreach($singleRows as $row)
                            <tr>
                                <td>{{ $row->date_of_sale }}</td>
                                <td><a href="{{ route('sale.view', $row->id) }}">#{{ $row->id }}</a></td>
                                <td>{{ optional($row->invoice)->invoice_code ?? $row->invoice_id }}</td>
                                <td>{{ $row->sale_type == 1 ? 'Hire' : 'Cash' }}</td>
                                <td class="text-right">{{ $row->quantity }}</td>
                                <td class="text-right">{{ number_format($row->unit_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h5 class="mt-4 mb-2 font-weight-bold">Stock movements</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Movement</th>
                            <th class="text-right">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $types = [1=>'GRN receipt', 2=>'Sale', 3=>'Transfer out', 4=>'Transfer in', 5=>'Return', 6=>'Adjustment in', 7=>'Adjustment out']; @endphp
                        @foreach($movements as $movement)
                            <tr>
                                <td>{{ $movement->date }}</td>
                                <td>{{ $types[$movement->stock_master_id] ?? 'Other movement' }}</td>
                                <td class="text-right">{{ $movement->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h5 class="mt-4 mb-2 font-weight-bold">Serial / Warranty details</h5>
            @if($warning)<p class="text-warning">{{ $warning }}</p>@endif
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Serial / Color</th>
                            <th>Warranty card</th>
                            <th>Period</th>
                            <th>Terms</th>
                            <th>Document</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $unit)
                            <tr>
                                <td>{{ $unit->serial_number }}</td>
                                <td>{{ $unit->warranty_number }}</td>
                                <td>{{ $unit->warranty_period }} Months</td>
                                <td>{{ $unit->additional_warranty_description }}</td>
                                <td>@if($unit->warranty_document && $unit->warranty_document !== 'assets/img/products/default.png')<a href="{{ asset($unit->warranty_document) }}" target="_blank" rel="noopener">View Document</a>@endif</td>
                                <td class="text-center">{{ $unit->status == 0 ? 'Available' : 'Used / archived' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
