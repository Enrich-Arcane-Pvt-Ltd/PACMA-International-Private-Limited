<table class="table table-striped table-bordered mt-3">
    <tbody>
        @foreach (['warranty_period' => 'Warranty Period', 'warranty_document' => 'Warranty Document', 'additional_warranty_description' => 'Additional Warranty Description'] as $field => $label)
            @php
                $default = $product->$field;
                if ($field === 'additional_warranty_description') {
                    $default = $default ?: $product->additional_warranty_details;
                }
                if ($field === 'warranty_document' && $default === 'assets/img/products/default.png') {
                    $default = null;
                }
                $values = $warrantyUnits->map(function ($unit) use ($field, $default) {
                    $value = $unit->$field;
                    if ($field === 'warranty_document' && $value === 'assets/img/products/default.png') {
                        $value = null;
                    }
                    return [
                        'value' => $value !== null && $value !== '' ? $value : $default,
                        'serial' => $unit->serial_number ?: $unit->warranty_number,
                    ];
                })->filter(function ($entry) {
                    return $entry['value'] !== null && trim((string) $entry['value']) !== '';
                });
                if ($values->isEmpty() && $default !== null && $default !== '') {
                    $values = collect([['value' => $default, 'serial' => null]]);
                }
                $differentValues = $values->pluck('value')->unique()->count() > 1;
                if (!$differentValues) $values = $values->unique('value');
            @endphp
            <tr>
                <th scope="row" class="w-150 dark-grey-text h6">{{ __($label) }}</th>
                <td>
                    @forelse ($values as $entry)
                        <div class="mb-2">
                            @if ($differentValues && $entry['serial'])
                                <span class="text-muted">{{ $entry['serial'] }}:</span>
                            @endif
                            @if ($field === 'warranty_document')
                                <a href="{{ asset($entry['value']) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary">{{ __('View Document') }}</a>
                                <a href="{{ asset($entry['value']) }}" download class="btn btn-sm btn-info">{{ __('Download') }}</a>
                            @elseif ($field === 'warranty_period')
                                {{ $entry['value'] }} {{ __('Months') }}
                            @else
                                <span style="white-space: pre-wrap;">{{ $entry['value'] }}</span>
                            @endif
                        </div>
                    @empty
                        {{ $field === 'warranty_document' ? __('No document uploaded') : __('Not recorded') }}
                    @endforelse
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
