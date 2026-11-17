@foreach ($product->productWarrantySerial as $index => $serialWarranty)
    @php
        $master = $product->products;
        $period = $serialWarranty->warranty_period ?? optional($master)->warranty_period;
        $terms = $serialWarranty->additional_warranty_description ?: (optional($master)->additional_warranty_description ?: optional($master)->additional_warranty_details);
        $document = $serialWarranty->warranty_document ?: optional($master)->warranty_document;
    @endphp
    <div class="border rounded p-3 mb-3 bg-white shadow-sm">
        <span class="badge badge-primary mb-3">{{ __('Unit') }} #{{ $index + 1 }}</span>
        <div class="row">
            <div class="col-md-6">
                <label class="font-weight-bold">{{ __('Serial Number / Color') }}</label>
                <p>{{ $serialWarranty->serial_number ?: __('Not recorded') }}</p>
            </div>
            <div class="col-md-6">
                <label class="font-weight-bold">{{ __('Warranty Card Number') }}</label>
                <p>{{ $serialWarranty->warranty_number ?: __('Not recorded') }}</p>
            </div>
            <div class="col-md-6">
                <label class="font-weight-bold">{{ __('Warranty Period') }}</label>
                <p>{{ $period !== null && $period !== '' ? $period . ' ' . __('Months') : __('Not recorded') }}</p>
            </div>
            <div class="col-md-6">
                <label class="font-weight-bold d-block">{{ __('Warranty Document') }}</label>
                @if ($document && $document !== 'assets/img/products/default.png')
                    <a href="{{ asset($document) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary mb-3">{{ __('View Document') }}</a>
                    <a href="{{ asset($document) }}" download class="btn btn-sm btn-info mb-3">{{ __('Download') }}</a>
                @else
                    <p>{{ __('No document uploaded') }}</p>
                @endif
            </div>
            <div class="col-12">
                <label class="font-weight-bold">{{ __('Additional Warranty Description / Terms') }}</label>
                <p style="white-space: pre-wrap;">{{ $terms ?: __('Not recorded') }}</p>
            </div>
        </div>
    </div>
@endforeach
