@extends('layouts.admin')
@section('page-title')
    {{ __('Purchase Order Detail') }}
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
                    // console.log(data);
                }
            });
        })
    </script>
@endpush

<style>
    .btn-custom-green {
        background-color: #009b00;
        border-color: #009b00;
        transition: background-color 0.3s ease;
    }

    .btn-custom-green:hover {
        background-color: #006400;
        border-color: #006400;
    }

    .activity-icon {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Purchase Order') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">
                    <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                </div>
                <div class="breadcrumb-item">
                    <a href="{{ route('purchase-order.index') }}">{{ __('Purchase Order') }}</a>
                </div>
                <div class="breadcrumb-item">{{ AUth::user()->purchaseOrderNumberFormat($purchorders->id) }}</div>
            </div>
        </div>

        @can('send bill')
            @if ($purchorders->status != 4)
                <div class="row">
                    <div class="col-12">
                        <div class="activities">
                            <div class="activity">
                                <div class="activity-icon bg-primary text-white shadow-primary">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="activity-detail">
                                    <div class="mb-2">
                                        <span class="text-job text-primary">
                                            <h6>{{ __('Create Purchase Order') }}</h6>
                                        </span>
                                        @can('edit purchase order')
                                            <a href="{{ route('purchase-order.edit', $purchorders->id) }}"
                                                class="btn btn-primary btn-action mr-1 float-right">
                                                {{ __('Edit') }}
                                            </a>
                                        @endcan
                                    </div>
                                    <p>
                                        {{ __('Status') }} :
                                        <a href="#">
                                            {{ __('Created on ') }} {{ \Auth::user()->dateFormat($purchorders->order_date) }}
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="activity">
                                <div class="activity-icon bg-primary text-white shadow-primary">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="activity-detail">
                                    <div class="mb-2">
                                        <span class="text-job text-primary">
                                            <h6>{{ __('Send Purchase Order') }}</h6>
                                        </span>

                                        @if ($purchorders->status != 0)
                                            <p>
                                                {{ __('Status') }} :
                                                <a href="#">
                                                    {{ __('Sent on') }}
                                                    {{ \Auth::user()->dateFormat($purchorders->order_date) }}
                                                </a>
                                            </p>
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
                                    <a href="{{ route('purchase-order.print', Crypt::encrypt($purchorders->id)) }}"
                                        target="_blank" class="btn btn-info btn-action mr-1 float-right">
                                        {{ __('Print') }}
                                    </a>
                                    @if ($purchorders->status != 4)
                                        @can('create grn')
                                            <a href="{{ route('grn.create', $purchorders->id) }}"
                                                class="btn btn-info btn-action mr-1 float-right">
                                                {{ __('GRN') }}
                                            </a>
                                        @endcan
                                    @endif
                                    <a href="{{ route('purchase-order.pdf', Crypt::encrypt($purchorders->id)) }}"
                                        target="_blank" class="btn btn-primary btn-action mr-1 float-right">
                                        {{ __('Download') }}
                                    </a>
                                    <a href="{{ route('purchase-order.sms', Crypt::encrypt($purchorders->id)) }}"
                                        class="btn btn-primary btn-action mr-1 float-right" data-toggle="tooltip"
                                        data-original-title="{{ __('Send SMS') }}">
                                        <i class="fas fa-paper-plane"></i>
                                        {{ __('Send SMS') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row mb-10">
                            <div class="col-lg-12">
                                <div class="text-right">
                                    <a href="#" data-url="{{ route('vender.bill.send', $purchorders->id) }}"
                                        data-ajax-popup="true" data-title="{{ __('Send Bill') }}"
                                        class="btn btn-primary btn-action mr-1 float-right">
                                        {{ __('Send Mail') }}
                                    </a>
                                    <a href="{{ route('bill.pdf', Crypt::encrypt($purchorders->id)) }}" target="_blank"
                                        class="btn btn-primary btn-action mr-1 float-right">
                                        {{ __('Download') }}
                                    </a>
                                    <a href="{{ route('purchase-order.sms', Crypt::encrypt($purchorders->id)) }}"
                                        class="btn btn-primary btn-action mr-1 float-right" data-toggle="tooltip"
                                        data-original-title="{{ __('Send SMS') }}">
                                        <i class="fas fa-paper-plane"></i>
                                        {{ __('Send SMS') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="invoice-title">
                                <h2>{{ __('Purchase Order') }}</h2>
                                <div class="invoice-number">
                                    {{ AUth::user()->purchaseOrderNumberFormat($purchorders->id) }}
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <address class="font-style">
                                        <strong>{{ __('Purchase From') }}:</strong><br>
                                        {{ !empty($purchorders->vender->name) ? $purchorders->vender->name : '' }}<br>
                                        {{ !empty($purchorders->vender->address) ? $purchorders->vender->address : '' }}<br>
                                        {{ !empty($purchorders->vender->contact) ? $purchorders->vender->contact : '' }}<br>
                                        {{ !empty($purchorders->vender->email) ? $purchorders->vender->email : '' }}
                                    </address>
                                </div>
                                <div class="col-md-6 text-md-right">
                                    <address class="font-style">
                                        <strong>{{ __('Delivery Address') }}:</strong><br>
                                        {{ !empty($purchorders->branch->branch_manager) ? $purchorders->branch->branch_manager : '' }}<br>
                                        {{ !empty($purchorders->branch->address) ? $purchorders->branch->address : '' }}<br>
                                        {{ !empty($purchorders->branch->phone) ? $purchorders->branch->phone : '' }}

                                    </address>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <address>
                                        <strong>{{ __('Status') }}:</strong>
                                        <br>
                                        @if ($purchorders->status == 1)
                                            <span class="badge badge-warning">Cancelled</span>
                                        @elseif($purchorders->status == 0)
                                            <span class="badge badge-danger">Rejected</span>
                                        @elseif($purchorders->status == 2)
                                            <span class="badge badge-info">Not Yet Approved</span>
                                        @elseif($purchorders->status == 3)
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($purchorders->status == 4)
                                            <span class="badge badge-primary">Issued</span>
                                        @endif
                                    </address>
                                </div>
                                <div class="col-md-4 text-md-center">
                                    <address>
                                        <strong>{{ __('Issue Date') }} :</strong><br>
                                        {{ \Auth::user()->dateFormat($purchorders->order_date) }}<br><br>
                                    </address>
                                </div>
                                <div class="col-md-4 text-md-right">
                                    <address>
                                        <strong>{{ __('Debit Date') }} :</strong><br>
                                        {{ !empty($purchorders->required_by) ? \Auth::user()->dateFormat($purchorders->required_by) : '-' }}<br><br>
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{ __('Product Summary') }}</div>
                            <p class="section-lead">
                                {{ __('All items here cannot be deleted.') }}</p>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <tr>
                                        <th data-width="40">#</th>
                                        <th>{{ __('Product Code') }}</th>
                                        <th>{{ __('Product Description') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Unit Cost') }} ({{ $currencySymbol }})</th>
                                        <th class="text-right" width="12%">{{ __('Amount') }} ({{ $currencySymbol }})
                                        </th>
                                    </tr>
                                    @php
                                        $totalAmount = 0;
                                    @endphp

                                    @foreach ($purchorders->purchaseProducts as $key => $purchaseProduct)
                                        @php
                                            $totalAmount += $purchaseProduct->amount;
                                        @endphp
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $purchaseProduct->products['barcode'] }}</td>
                                            <td>{{ $purchaseProduct->products['name'] }}</td>
                                            <td>{{ $purchaseProduct->quantity }}</td>
                                            <td>
                                                {{ number_format($purchaseProduct->unit_cost ?? 0, 2) }}
                                                {{-- {{ \Auth::user()->priceFormat($purchaseProduct->unit_cost) }} --}}
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($purchaseProduct->amount ?? 0, 2) }}
                                                {{-- {{ \Auth::user()->priceFormat($purchaseProduct->amount) }} --}}
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tfoot>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td colspan="2" class="text-right"><b>{{ __('Sub Total') }}</b></td>
                                            <td class="text-right">
                                                {{ number_format($totalAmount ?? 0, 2) }}
                                                {{-- {{ \Auth::user()->priceFormat($totalAmount) }} --}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td colspan="2" class="text-right"><b>{{ __('Total') }}</b></td>
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
@endsection
@push('script-page')
@endpush
