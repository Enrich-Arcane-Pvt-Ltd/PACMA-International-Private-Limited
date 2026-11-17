<script src="{{ asset('assets/js/sale-warranty.js') }}?v={{ filemtime(public_path('assets/js/sale-warranty.js')) }}"></script>
<script src="{{ asset('assets/js/sale-batches.js') }}?v={{ filemtime(public_path('assets/js/sale-batches.js')) }}"></script>
@php
    $batchSavedRows = isset($sales) ? ($batchCash ? $sales->saleItems->where('status', 1)->values() : [$sales]) : [];
@endphp
<script>
setTimeout(function () {
    SaleBatches.install({
        cash: @json($batchCash), saleId: @json(isset($sales) ? $sales->id : null),
        url: @json(url('sale/stock-batches')),
        rows: @json($batchSavedRows),
        warrantyId: @json(isset($hirePurchaseDetail) ? $hirePurchaseDetail->product_warranty_id : null),
        snapshot: @json(isset($hirePurchaseDetail) ? $hirePurchaseDetail : null),
        canViewBatch: @json(auth()->user()->can('manage stock'))
    });
}, 0);
</script>
