@extends('layouts.admin')

@section('page-title')
    {{__('Advance Payment Detail')}}
@endsection

@section('content')
    <style>
        .text-product {
            margin-bottom: 0rem;
            line-height: 1.5;
        }
    </style>

    <section class="section">
        <div class="section-header">
            <h1>{{__('View Advance Payment')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('advance_payement.index') }}">{{__('Advance Payments')}}</a></div>
                <div class="breadcrumb-item">{{ $advance->advance_code }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="invoice">
                <div class="sale-detail-print">
                    <div class="row mb-10">
                        <div class="col-lg-12">
                            <div class="text-right">
                                <button type="button"
                                        class="btn btn-info btn-action print"
                                        data-toggle="tooltip"
                                        data-original-title="{{ __('Advance Payment Receipt Print') }}"
                                        onclick="updatePrintStatus({{ $advance->id }})"
                                        value="{{ $advance->receipt_id }}">
                                    <i class="fas fa-print"></i> Print Receipt
                                </button>

                                @can('delete invoice product')
                                    <button type="button"
                                            class="btn btn-info btn-action print"
                                            data-toggle="tooltip"
                                            data-original-title="{{ __('Advance Payment Receipt Print') }}"
                                            onclick="adminUpdatePrintStatus({{ $advance->id }})"
                                            value="{{ $advance->receipt_id }}">
                                        <i class="fas fa-print"></i> Print Orginal Receipt
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="invoice-title">
                                <h2>{{__('Advance Payment Details')}}</h2>
                                <div class="invoice-number">{{ $advance->advance_code }}</div>
                            </div>
                            <hr>
                            <div class="row">
                                @if(!empty($advance->customer))
                                    <div class="col-md-6">
                                        <address class="font-style">
                                            <strong>{{__('Customer Information')}}:</strong><br>
                                            {{ $advance->customer->name }}<br>
                                            {{ $advance->customer->phone }}<br>
                                            @php
                                                $advHouseNo = !empty($advance->customer->billing_house_no)
                                                    ? trim($advance->customer->billing_house_no)
                                                    : (!empty($advance->customer->house_no) ? trim($advance->customer->house_no) : '');
                                                $advAddress = !empty($advance->customer->billing_address)
                                                    ? trim($advance->customer->billing_address)
                                                    : (!empty($advance->customer->address) ? trim($advance->customer->address) : '');
                                                $advFullAddress = $advAddress;
                                                if (!empty($advHouseNo)) {
                                                    $cleanAdvHouseNo = trim($advHouseNo, ", \t\n\r\0\x0B");
                                                    if (empty($advAddress)) {
                                                        $advFullAddress = $advHouseNo;
                                                    } elseif ($cleanAdvHouseNo !== '' && stripos($advAddress, $cleanAdvHouseNo) === false) {
                                                        $formattedAdvHouseNo = rtrim($cleanAdvHouseNo, ',') . ',';
                                                        $advFullAddress = $formattedAdvHouseNo . ' ' . $advAddress;
                                                    }
                                                }
                                            @endphp
                                            {{ $advFullAddress }}
                                        </address>
                                    </div>
                                @endif
                                <div class="col-md-6 text-md-right">
                                    <address>
                                        <strong>{{__('Advance Payment Date')}} :</strong><br>
                                        {{ $advance->advance_date }}<br><br>
                                    </address>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <address>
                                        <strong>{{__('Payment Method')}}:</strong><br>
                                        {{ ucfirst($advance->payment_method) }}
                                    </address>
                                </div>
                                <div class="col-md-6 text-md-right">
                                    <address>
                                        <strong>{{__('Advance Amount')}} :</strong><br>
                                        {{ \Auth::user()->priceFormat($advance->advance_amount) }}<br><br>
                                    </address>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <strong>{{__('Remarks')}}:</strong> {{($advance->note) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!empty($advance->product))
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="section-title">{{__('Product Information')}}</div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-md">
                                        <thead>
                                            <tr>
                                                <th>{{__('Product Code')}}</th>
                                                <th>{{__('Product Description')}}</th>
                                                <th>{{__('Quantity')}}</th>
                                                <th class="text-right">{{__('Unit Price')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $advance->product->barcode }}</td>
                                                <td>{{ $advance->product->name }}</td>
                                                <td>1</td>
                                                <td class="text-right">{{ \Auth::user()->priceFormat($advance->advance_amount) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Initialize tooltip if using Bootstrap tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Handle "print" button click
            $('.print').click(function () {
                var advanceId = $(this).val();  // Retrieve advance ID from button value
                var url = "{{ url('advance_payement/receipt') }}/" + advanceId + "/print"; // Corrected URL path
                window.open(url, '_blank');  // Open the print URL in a new tab
            });
        });

        function updatePrintStatus(advanceId) {
            // Send AJAX request to update print status
            fetch(`/advance-update-print-status/${advanceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'  // Include CSRF token for Laravel
                },
                body: JSON.stringify({ print_status: 1 })
            }).then(response => {
                if (response.ok) {
                    console.log('Print status updated to 1');
                } else {
                    console.error('Failed to update print status');
                }
            }).catch(error => console.error('Error:', error));
        }

        function adminUpdatePrintStatus(advanceId) {
            // Send AJAX request to update print status to 3
            fetch(`/advance-admin-update-print-status/${advanceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'  // Include CSRF token for Laravel
                },
                body: JSON.stringify({ print_status: 3 })  // Set print status to 3
            }).then(response => {
                if (response.ok) {
                    console.log('Print status updated to 3');
                } else {
                    console.error('Failed to update print status');
                }
            }).catch(error => console.error('Error:', error));
        }

    </script>


@endsection
