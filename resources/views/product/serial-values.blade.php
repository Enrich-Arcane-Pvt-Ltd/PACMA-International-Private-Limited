@php
    $serialValues = $warrantyUnits->pluck('serial_number')->prepend($product->serial_number)
        ->filter(function ($value) { return $value !== null && trim((string) $value) !== ''; })->unique();
@endphp
@forelse ($serialValues as $serial)
    <div>{{ $serial }}</div>
@empty
    {{ __('Not recorded') }}
@endforelse
