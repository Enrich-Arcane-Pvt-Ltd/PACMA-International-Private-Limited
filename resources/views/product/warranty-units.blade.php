<h6 class="mt-4">{{ __('GRN serial and warranty details') }}</h6>
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>{{ __('GRN') }}</th>
                <th>{{ __('Serial Number / Color') }}</th>
                <th>{{ __('Warranty Number') }}</th>
                <th>{{ __('Warranty Period') }}</th>
                <th>{{ __('Warranty Document') }}</th>
                <th>{{ __('Additional Warranty Description') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($warrantyUnits as $unit)
                @php
                    $period = $unit->warranty_period ?? $product->warranty_period;
                    $document = $unit->warranty_document ?: $product->warranty_document;
                    $description = $unit->additional_warranty_description ?: ($product->additional_warranty_description ?: $product->additional_warranty_details);
                    $hasDocument = $document && $document !== 'assets/img/products/default.png';
                @endphp
                <tr>
                    <td>{{ \Auth::user()->grnNumberFormat($unit->product->grn_id) }}</td>
                    <td>{{ $unit->serial_number ?: __('Not recorded') }}</td>
                    <td>{{ $unit->warranty_number ?: __('Not recorded') }}</td>
                    <td>{{ $period !== null && $period !== '' ? $period . ' ' . __('Months') : __('Not recorded') }}</td>
                    <td>
                        @if ($hasDocument)
                            <a href="{{ asset($document) }}" target="_blank" rel="noopener noreferrer">{{ __('View document') }}</a>
                        @else
                            {{ __('No document uploaded') }}
                        @endif
                    </td>
                    <td>{{ $description ?: __('Not recorded') }}</td>
                    <td>{{ [0 => __('Available'), 1 => __('Used'), 2 => __('Not Available')][$unit->status] ?? __('Unknown') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">{{ __('No serial or warranty details recorded for received units in this branch.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
