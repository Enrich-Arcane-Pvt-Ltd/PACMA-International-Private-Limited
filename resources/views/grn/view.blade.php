@extends('layouts.admin')
@section('page-title')
    {{ __('GRN Detail') }}
@endsection
@push('script-page')
    <script>
        $(document).on('click', '#shipping', function() {
            var url = $(this).data('url');
            var is_display = $("#shipping").is(":checked");
            $.ajax({
                url: url,
                type: 'get',
                data: {
                    'is_display': is_display,
                },
                success: function(data) {
                    // Handle success
                }
            });
        })
    </script>
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('GRN') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('grn.index') }}">{{ __('GRN') }}</a></div>
                <div class="breadcrumb-item">{{ \AUth::user()->grnNumberFormat($purchorders->id) }}</div>
            </div>
        </div>

        @can('manage grn')
            @if ($purchorders->status != 4)
                <div class="row">
                    <div class="col-12">
                        <div class="activities">
                            <!-- GRN Created Section -->
                            <div class="activity">
                                <div class="activity-icon bg-primary text-white shadow-primary">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="activity-detail">
                                    <div class="mb-2">
                                        <span class="text-job text-primary">
                                            <h6>{{ __('Create GRN') }}</h6>
                                        </span>
                                        @can('edit grn')
                                            <a href="{{ url('grnOrderEdit/OrderEdit', $purchorders->id) }}"
                                                class="btn btn-primary btn-action mr-1 float-right">
                                                {{ __('Edit') }}
                                            </a>
                                        @endcan
                                    </div>
                                    <p>{{ __('Status') }} : <a href="#">{{ __('Created on ') }}
                                            {{ \Auth::user()->dateFormat($purchorders->order_date) }} </a></p>
                                </div>
                            </div>
                            <div class="activity">
                                <div class="activity-icon bg-primary text-white shadow-primary">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="activity-detail">
                                    <div class="mb-2">
                                        <span class="text-job text-primary">
                                            <h6>{{ __('Send GRN') }}</h6>
                                        </span>

                                        @if ($purchorders->status != 0)
                                            <p>{{ __('Status') }} : <a href="#">{{ __('Sent on') }}
                                                    {{ \Auth::user()->dateFormat($purchorders->order_date) }} </a></p>
                                        @else
                                            @can('send bill')
                                                <a href="{{ route('bill.sent', $purchorders->id) }}"
                                                    class="btn btn-primary btn-action mr-1 float-right">
                                                    {{ __('Mark Sent') }}
                                                </a>
                                            @endcan
                                            <p>{{ __('Status') }} : <a href="#">{{ __('Not Sent') }} </a></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="activity">
                                <div class="activity-icon bg-primary text-white shadow-primary">
                                    <i class="far fa-money-bill-alt"></i>
                                </div>
                                <div class="activity-detail">
                                    <div class="mb-2">
                                        <span class="text-job text-primary">
                                            <h6>{{ __('Debit date') }}</h6>
                                        </span>
                                    </div>

                                    <p>{{ __('Status') }} : <a
                                            href="#">{{ \Auth::user()->dateFormat($purchorders->required_by) }} </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endcan

        <div class="section-body">
            <div class="invoice">
                <div class="invoice-print">
                    @if ($purchorders->status != 0)
                        <div class="row mb-10">
                            <div class="col-lg-12">
                                <div class="text-right">
                                    <a href="{{ route('grn.print', Crypt::encrypt($purchorders->id)) }}" target="_blank"
                                        class="btn btn-info btn-action mr-1 float-right">
                                        {{ __('Print') }}
                                    </a>
                                    <a href="{{ route('grn.pdf', Crypt::encrypt($purchorders->id)) }}" target="_blank"
                                        class="btn btn-primary btn-action mr-1 float-right">
                                        {{ __('Download') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Product Summary Table -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{ __('Product Summary') }}</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <thead>
                                        <tr>
                                            <th width="9%">{{ __('Product Code') }}</th>
                                            <th width="16%">{{ __('Item Name') }}</th>
                                            <th width="10%">{{ __('Model') }}</th>
                                            <th width="6%">{{ __('Quantity') }}</th>
                                            <th width="6%">{{ __('Received Quantity') }}</th>
                                            <th>{{ __('Serial & Warranty / Color') }}</th>
                                            <th width="12%" class="text-right">{{ __('Unit Cost') }}
                                                ({{ $currencySymbol }})</th>
                                            <th width="12%" class="text-right">{{ __('GRN Cost') }}
                                                ({{ $currencySymbol }})</th>
                                            <th width="12%" class="text-right">{{ __('Cash Price') }}
                                                ({{ $currencySymbol }})</th>
                                            <th width="12%" class="text-right">{{ __('Selling Price') }}
                                                ({{ $currencySymbol }})</th>
                                            <th width="12%" class="text-right">{{ __('Amount') }}
                                                ({{ $currencySymbol }})</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalAmount = 0;
                                        @endphp

                                        @foreach ($purchorders->GrnProducts as $key => $product)
                                            @php
                                                $totalAmount += $product->amount;
                                            @endphp
                                            <tr>
                                                {{-- <td>{{ $key + 1 }}</td> --}}
                                                <td>{{ $product->products['barcode'] }}</td>
                                                <td>{{ $product->products['name'] }}</td>
                                                <td>{{ optional(optional($product->products)->brands)->name ?? 'N/A' }}</td>
                                                <td class="text-center">{{ $product->quantity }}</td>
                                                <td class="text-center">{{ $product->received_qty }}</td>
                                                <td>
                                                    @if ($product->productWarrantySerial->isEmpty())
                                                        <span>{{ __('N/A') }}</span>
                                                    @else
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#serialWarrantyModal_{{ $product->id }}"
                                                            class="btn btn-primary btn-action d-flex align-items-center justify-content-center view-serial-warranty-btn me-1"
                                                            data-toggle="tooltip" title="View Serial & Warranty / Color">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($product->unit_cost ?? 0, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($product->unit_cost) }}</td> --}}
                                                <td class="text-right">
                                                    {{ number_format($product->grn_cost ?? 0, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($product->grn_cost) }} --}}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($product->sale_price ?? 0, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($product->sale_price) }}</td> --}}
                                                <td class="text-right">
                                                    {{ number_format($product->hire_price ?? 0, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($product->hire_price) }}</td> --}}
                                                <td class="text-right">
                                                    {{ number_format($product->amount ?? 0, 2) }}
                                                    {{-- {{ \Auth::user()->priceFormat($product->amount) }} --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="6"></td>
                                            <td colspan="4" class="text-right"><b>{{ __('Sub Total') }}</b></td>
                                            <td class="text-right">
                                                {{ number_format($totalAmount ?? 0, 2) }}
                                                {{-- {{ \Auth::user()->priceFormat($totalAmount) }} --}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"></td>
                                            <td colspan="4" class="text-right"><b>{{ __('Total') }}</b></td>
                                            <td class="text-right">
                                                {{ number_format($totalAmount ?? 0, 2) }}
                                                {{-- {{ \Auth::user()->priceFormat($totalAmount) }} --}}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- All Modals for Serial & Warranty -->
    @foreach ($purchorders->GrnProducts as $product)
        <!-- Modal for Serial & Warranty -->
        <div class="modal fade" id="serialWarrantyModal_{{ $product->id }}" tabindex="-1" role="dialog"
            aria-labelledby="serialWarrantyModalLabel_{{ $product->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="serialWarrantyModalLabel_{{ $product->id }}">
                            {{ __('Serial & Warranty Details') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @include('grn.warranty-summary')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" data-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
