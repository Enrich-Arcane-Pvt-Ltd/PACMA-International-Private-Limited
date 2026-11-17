@extends('layouts.admin')
@section('page-title', __('Stock Batch'))
@section('content')
<section class="section"><div class="card"><div class="card-header"><h4>Batch B{{ str_pad($batch->id, 6, '0', STR_PAD_LEFT) }} — {{ optional($batch->products)->name }}</h4></div><div class="card-body">
    <p>Branch: {{ optional($batch->branch)->name }} · GRN: {{ $batch->grn_id }} · Supplier invoice: {{ $batch->supplier_invoice_number }} · Received: {{ $batch->date }}</p>
    <p>Received quantity: <strong>{{ $batch->grn_qty }}</strong> · Current remaining: <strong>{{ $batch->remain_qty }}</strong> · Cost: {{ number_format($batch->purchase_price, 2) }} · Cash price: {{ number_format($batch->sale_price, 2) }} · Retail price: {{ number_format($batch->hire_price, 2) }} · Current stock value: {{ number_format($batch->remain_qty * $batch->purchase_price, 2) }}</p>
    <form method="GET" class="form-inline mb-3"><label class="mr-2">From <input type="date" name="from" value="{{ request('from') }}" class="form-control ml-2"></label><label class="mr-2">To <input type="date" name="to" value="{{ request('to') }}" class="form-control ml-2"></label><button class="btn btn-primary">Filter</button><a class="btn btn-secondary ml-2" href="{{ route('stock.batch', $batch->id) }}">Reset</a><button type="button" class="btn btn-secondary ml-2" onclick="window.print()">Print</button></form>
    <p>Sales in selected period — Cash: <strong>{{ $cashRows->sum('quantity') + $singleRows->where('sale_type', 0)->sum('quantity') }}</strong> · Hire: <strong>{{ $singleRows->where('sale_type', 1)->sum('quantity') }}</strong> · Transfers out: <strong>{{ $movements->where('stock_master_id', 3)->sum('quantity') }}</strong> · Returns: <strong>{{ $movements->where('stock_master_id', 5)->sum('quantity') }}</strong></p>
    <small class="text-muted">Current remaining is today's balance. Date filters apply to sales and movement history below. Older sales spanning multiple inventory records require reconciliation before their allocation can be treated as confirmed.</small>
    <h5 class="mt-4">Sales / Invoices</h5><div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Sale</th><th>Invoice</th><th>Type</th><th>Quantity</th><th>Sale price</th></tr></thead><tbody>
    @foreach($cashRows as $row)<tr><td>{{ $row->date_of_sale }}</td><td><a href="{{ route('sale.view', $row->sale_id) }}">#{{ $row->sale_id }}</a></td><td>{{ optional($row->invoice)->invoice_code ?? $row->invoice_id }}</td><td>Cash</td><td>{{ $row->quantity }}</td><td>{{ number_format($row->unit_price, 2) }}</td></tr>@endforeach
    @foreach($singleRows as $row)<tr><td>{{ $row->date_of_sale }}</td><td><a href="{{ route('sale.view', $row->id) }}">#{{ $row->id }}</a></td><td>{{ optional($row->invoice)->invoice_code ?? $row->invoice_id }}</td><td>{{ $row->sale_type == 1 ? 'Hire' : 'Cash' }}</td><td>{{ $row->quantity }}</td><td>{{ number_format($row->unit_price, 2) }}</td></tr>@endforeach
    </tbody></table></div>
    <h5>Stock movements</h5><div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Movement</th><th>Quantity</th></tr></thead><tbody>
    @php $types = [1=>'GRN receipt', 2=>'Sale', 3=>'Transfer out', 4=>'Transfer in', 5=>'Return', 6=>'Adjustment in', 7=>'Adjustment out']; @endphp
    @foreach($movements as $movement)<tr><td>{{ $movement->date }}</td><td>{{ $types[$movement->stock_master_id] ?? 'Other movement' }}</td><td>{{ $movement->quantity }}</td></tr>@endforeach
    </tbody></table></div>
    <h5>Serial / Warranty details</h5>@if($warning)<p class="text-warning">{{ $warning }}</p>@endif
    <div class="table-responsive"><table class="table"><thead><tr><th>Serial / Color</th><th>Warranty card</th><th>Period</th><th>Terms</th><th>Document</th><th>Status</th></tr></thead><tbody>
    @foreach($units as $unit)<tr><td>{{ $unit->serial_number }}</td><td>{{ $unit->warranty_number }}</td><td>{{ $unit->warranty_period }} Months</td><td>{{ $unit->additional_warranty_description }}</td><td>@if($unit->warranty_document && $unit->warranty_document !== 'assets/img/products/default.png')<a href="{{ asset($unit->warranty_document) }}" target="_blank" rel="noopener">View Document</a>@endif</td><td>{{ $unit->status == 0 ? 'Available' : 'Used / archived' }}</td></tr>@endforeach
    </tbody></table></div>
</div></div></section>
@endsection
