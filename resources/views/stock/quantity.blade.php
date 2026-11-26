<style>
    .table-responsive {
        overflow-x: auto;
    }
</style>
<div>
    <div class="row">
        <div class="col-sm-12 table-responsive">
            <table class="table table-flush border font-style " id="dataTable-manual">
                <thead class="thead-light">
                    <tr>
                        <th>{{ __('Branch') }}</th>
                        <th>{{ __('Batch / GRN') }}</th>
                        <th>{{ __('Stock Qty.') }}</th>
                        <th>{{ __('Stock Value') }} ({{ $currencySymbol }})</th>
                        <th>{{ __('Purchase Price') }} ({{ $currencySymbol }})</th>
                        <th>{{ __('Cash Price') }} ({{ $currencySymbol }})</th>
                        <th>{{ __('Selling Price') }} ({{ $currencySymbol }})</th>
                        <th>{{ __('Last Purchase Price') }} ({{ $currencySymbol }})</th>
                        <th>{{ __('Last Cash Price') }} ({{ $currencySymbol }})</th>
                        <th>{{ __('Last Selling Price') }} ({{ $currencySymbol }})</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($current_inventory ?? [] as $batch)
                        <tr>
                            <td>
                                {{ $branch->name }}
                            </td>
                            <td><a href="{{ route('stock.batch', $batch->id) }}" target="_blank" rel="noopener">B{{ str_pad($batch->id, 6, '0', STR_PAD_LEFT) }}</a><br>{{ $batch->supplier_invoice_number ?: $batch->grn_id }}<br><small>{{ optional($batch->grns)->approval_status === 'rejected' ? 'Rejected' : ($batch->status == 1 && $batch->remain_qty > 0 ? 'Available' : 'Closed / archived') }}</small></td>
                            <td>
                                {{ $batch->remain_qty ?? '0' }}
                            </td>
                            <td>
                                {{ number_format($batch->purchase_price * ($batch->remain_qty ?? 0), 2) }}
                                {{-- {{ \Auth::user()->priceFormat($current_inventory->purchase_price * ($current_inventory->remain_qty ?? 0)) }} --}}
                            </td>
                            <td>
                                {{ number_format($batch->purchase_price ?? 0, 2) }}
                                {{-- {{ \Auth::user()->priceFormat($current_inventory->purchase_price ?? '0') }} --}}
                            </td>
                            <td>
                                {{ number_format($batch->sale_price ?? 0, 2) }}
                                {{-- {{ \Auth::user()->priceFormat($current_inventory->sale_price ?? '0') }} --}}
                            </td>
                            <td>
                                {{ number_format($batch->hire_price ?? 0, 2) }}
                                {{-- {{ \Auth::user()->priceFormat($current_inventory->hire_price ?? '0') }} --}}
                            </td>
                            <td>
                                {{ number_format($last_purchase->last_purchase_price ?? 0, 2) }}
                                {{-- {{ \Auth::user()->priceFormat($last_purchase->last_purchase_price ?? '0') }} --}}
                            </td>
                            <td>
                                {{ number_format($last_purchase->last_sale_price ?? 0, 2) }}
                                {{-- {{ \Auth::user()->priceFormat($last_purchase->last_sale_price ?? '0') }} --}}
                            </td>
                            <td>
                                {{ number_format($last_purchase->last_hire_price ?? 0, 2) }}
                                {{-- {{ \Auth::user()->priceFormat($last_purchase->last_hire_price ?? '0') }} --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">{{ __('No Inventory for This Product') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
