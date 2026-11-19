@extends('layouts.admin')
@section('page-title')
    {{ __('Invoices') }}
@endsection
@section('content')
    <style>
        .se-pre-con {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url("{{ asset('assets') }}/loadings/Spinner-1s-200px_blue.gif") center no-repeat #ffffff99;
        }
    </style>
    <section class="section">
        <div class="se-pre-con"></div>
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Invoices') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    @if (\Auth::guard('customer')->check())
                                        <div class="breadcrumb-item active"><a
                                                href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    @else
                                        <div class="breadcrumb-item active"><a
                                                href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    @endif
                                    <div class="breadcrumb-item">{{ __('Invoices') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Manage Invoices') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-body p-0">
                                <div id="table-1_wrapper"
                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-flush" id="invoices_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th> {{ __('Invoice') }}</th>
                                                            <th> {{ __('Customer') }}</th>
                                                            <th style="display:none;">{{ 'Raw Issue Date' }}</th>
                                                            <!-- Hidden column for sorting -->
                                                            <th> {{ __('Issue Date') }}</th>
                                                            <th> {{ __('Type') }}</th>
                                                            <th class="text-right"> {{ __('Total Amount') }}
                                                                ({{ $currencySymbol }})</th>
                                                            @if (Gate::check('delete invoice') || Gate::check('view invoice'))
                                                                <th class="text-right"> {{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($invoices as $invoice)
                                                            <tr class="font-style">
                                                                <td>
                                                                    <a class="btn btn-outline-primary"
                                                                        href="{{ route('invoice.show', $invoice->id) }}">{{ $invoice->invoice_code }}
                                                                    </a>
                                                                </td>

                                                                <td>
                                                                    @if (!\Auth::guard('customer')->check())
                                                                        {{ !empty($invoice->customer) ? $invoice->customer->name : '' }}
                                                                    @else
                                                                        {{ '' }}
                                                                    @endif
                                                                </td>
                                                                <td style="display:none;">{{ $invoice->issue_date }}</td>
                                                                <!-- Hidden raw date column -->

                                                                <td>
                                                                    {{ Auth::user()->dateFormat($invoice->issue_date) }}
                                                                </td>

                                                                <td>
                                                                    @if (isset($invoice->sales))
                                                                        @if ((int) $invoice->sales->status === 4)
                                                                            <span class="badge badge-dark">{{ __('Returned') }}</span>
                                                                        @elseif ($invoice->sales->sale_type == 0)
                                                                            <span class="badge badge-success">{{ __('Cash Sales') }}</span>
                                                                        @elseif($invoice->sales->sale_type == 1)
                                                                            <span class="badge badge-info">{{ __('Hire Sales') }}</span>
                                                                        @endif
                                                                        @php
                                                                            $returnedAmt = \App\ItemReturn::where('invoice_id', $invoice->id)->where('status', 1)->sum('amount');
                                                                        @endphp
                                                                        @if ((int) $invoice->sales->status !== 4 && $returnedAmt > 0)
                                                                            <small class="text-danger d-block">{{ __('Returned') }}: {{ number_format($returnedAmt, 2) }}</small>
                                                                        @endif
                                                                    @endif
                                                                </td>

                                                                <td class="text-right">
                                                                    {{-- {{ \Auth::user()->priceFormat($invoice->sales->net_amount ?? '0') }} --}}
                                                                    {{ number_format($invoice->sales->net_amount ?? 0, 2) }}
                                                                </td>

                                                                @if (Gate::check('delete invoice') || Gate::check('view invoice'))
                                                                    <td class="action text-right">
                                                                        @can('view invoice')
                                                                            <a href="{{ route('invoice_print.print_preview', ['id' => $invoice->id, 'command' => 'print']) }}"
                                                                                target="_blank"
                                                                                class="btn btn-info btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                onclick="updatePrintStatus({{ $invoice->id }})"
                                                                                data-original-title="{{ __('Print') }}">
                                                                                <i class="fas fa-print"></i>
                                                                            </a>

                                                                            <a href="{{ route('invoice.show', $invoice->id) }}"
                                                                                class="btn btn-info btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Detail') }}">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>

                                                                            @if (optional($invoice->sales)->sale_type == 1)
                                                                                <a href="{{ route('pacma.forms.invoice_loan_requisition', $invoice->id) }}"
                                                                                    target="_blank"
                                                                                    class="btn btn-warning btn-action mr-1"
                                                                                    data-toggle="tooltip"
                                                                                    data-original-title="{{ __('Loan Requisition Form') }}">
                                                                                    <i class="fas fa-file-alt"></i>
                                                                                </a>
                                                                            @endif
                                                                        @endcan

                                                                        @can('delete invoice')
                                                                            <a href="#!" class="btn btn-danger btn-action "
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Delete') }}"
                                                                                data-confirm="{{ __('Are You Sure?') . ' | ' . __('This action can not be undone. Do you want to continue?') }}"
                                                                                data-confirm-yes="document.getElementById('delete-form-{{ $invoice->id }}').submit();">
                                                                                <i class="fas fa-trash"></i>
                                                                            </a>
                                                                            {!! Form::open([
                                                                                'method' => 'DELETE',
                                                                                'route' => ['invoice.destroy', $invoice->id],
                                                                                'id' => 'delete-form-' . $invoice->id,
                                                                            ]) !!}
                                                                            {!! Form::close() !!}
                                                                        @endcan
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                        @endforeach

                                                        @foreach ($receipts as $receipt)
                                                            <tr>
                                                                <td>
                                                                    <a class="btn btn-outline-primary"
                                                                        href="{{ route('advance_payment.view', $receipt->advance_payment_id) }}">
                                                                        {{ $receipt->receipt_code }}
                                                                    </a>
                                                                <td>
                                                                    {{ $receipt->customer->name ?? '' }}
                                                                </td>
                                                                <td style="display:none;">{{ $receipt->issue_date }}</td>
                                                                <!-- Hidden raw date column -->
                                                                <td>
                                                                    {{ Auth::user()->dateFormat($receipt->issue_date) }}
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="badge badge-warning">{{ __('Advance Payment') }}</span>
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ Auth::user()->priceFormat($receipt->paid_amount ?? 0) }}
                                                                </td>
                                                                @if (Gate::check('delete invoice') || Gate::check('view invoice'))
                                                                    @can('print receipt')
                                                                        <td class="action text-right">
                                                                            <a href="{{ route('advance_payement.receipt', ['id' => $receipt->id, 'command' => 'print']) }}"
                                                                                target="_blank"
                                                                                class="btn btn-info btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                onclick="advanceUpdatePrintStatus({{ $receipt->advance_payment_id }})"
                                                                                data-original-title="{{ __('Print') }}">
                                                                                <i class="fas fa-print"></i>
                                                                            </a>

                                                                            <a href="{{ route('advance_payment.view', $receipt->advance_payment_id) }}"
                                                                                class="btn btn-info btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Detail') }}">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                    @endcan
                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        function updatePrintStatus(invoiceId) {
            // Send AJAX request to update print status
            fetch(`/invoice-update-print-status/${invoiceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for Laravel
                },
                body: JSON.stringify({
                    print_status: 1
                })
            }).then(response => {
                if (response.ok) {
                    console.log('Print status updated to 1');
                } else {
                    console.error('Failed to update print status');
                }
            }).catch(error => console.error('Error:', error));
        }

        function advanceUpdatePrintStatus(advanceId) {
            // Send AJAX request to update print status
            fetch(`/advance-update-print-status/${advanceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for Laravel
                },
                body: JSON.stringify({
                    print_status: 1
                })
            }).then(response => {
                if (response.ok) {
                    console.log('Print status updated to 1');
                } else {
                    console.error('Failed to update print status');
                }
            }).catch(error => console.error('Error:', error));
        }

        $(document).ready(function() {
            $(".se-pre-con").fadeOut('slow');

            // Retrieve saved page number from local storage, if available
            let pageNo = localStorage.getItem("invoice_page_no") ? Number(localStorage.getItem("invoice_page_no")) :
                0;

            // Initialize DataTable with default order on the hidden "Raw Issue Date" column in descending order
            let invoicesTable = $('#invoices_table').DataTable({
                order: [
                    [2, "desc"] // Sort by the hidden raw date column (index 2) in descending order
                ],
                columnDefs: [{
                    targets: 2, // Target the hidden column
                    visible: false, // Hide this column in the display
                    searchable: false // Exclude from search
                }]
            });

            // Set the DataTable to the saved page number if it exists
            invoicesTable.page(pageNo).draw(false);

            // Update page number in local storage whenever the page changes
            $('#invoices_table').on('page.dt', function() {
                let pageInfo = invoicesTable.page.info();
                localStorage.setItem("invoice_page_no", pageInfo.page);
            });
        });




    </script>
@endsection

{{-- old code --}}
{{-- <div class="section-header">
            <h1>{{ __('Invoices') }}</h1>
    <div class="section-header-breadcrumb">
        @if (\Auth::guard('customer')->check())
        <div class="breadcrumb-item active"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a>
        </div>
        @else
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        @endif
        <div class="breadcrumb-item">{{ __('Invoices') }}</div>
    </div>
    </div> --}}

{{-- <div class="card-header-action">
        <div class="dropdown">
            <a href="#" data-toggle="dropdown" class="btn btn-icon icon-left btn-primary"><i class="fas fa-filter"></i>{{__('Filter')}}</a>
<div
    class="dropdown-menu dropdown-list dropdown-menu-right Filter-dropdown w-64 {{isset($_GET['customer'])?'show':''}}">
    @if (!\Auth::guard('customer')->check())
    {{ Form::open(array('route' => array('invoice.index'),'method' => 'GET')) }}
    @else
    {{ Form::open(array('route' => array('customer.invoice'),'method' => 'GET')) }}
    @endif
    @if (!\Auth::guard('customer')->check())
    <div class="form-group">
        {{ Form::label('customer', __('Customer')) }}
        {{ Form::select('customer',$customer,isset($_GET['customer'])?$_GET['customer']:'', array('class' => 'form-control font-style selectric')) }}
    </div>
    @endif
    <div class="form-group">
        {{ Form::label('issue_date', __('Date')) }}
        {{ Form::text('issue_date', isset($_GET['issue_date'])?$_GET['issue_date']:null, array('class' => 'form-control datepicker-range')) }}
    </div>
    <div class="form-group">
        {{ Form::label('status', __('Status')) }}
        {{ Form::select('status', [''=>'All']+$status,isset($_GET['status'])?$_GET['status']:'', array('class' => 'form-control font-style selectric')) }}
    </div>
    <div class="text-right">
        <button type="submit" class="btn btn-primary">{{__('Search')}}</button>
        @if (!\Auth::guard('customer')->check())
        <a href="{{route('invoice.index')}}" class="btn btn-danger">{{__('Reset')}}</a>
        @else
        <a href="{{route('customer.invoice')}}" class="btn btn-danger">{{__('Reset')}}</a>
        @endif
    </div>
    {{ Form::close() }}
</div>
</div>
@can('create invoice')
<a href="{{ route('invoice.create',0) }}" class="btn btn-icon icon-left btn-primary">
    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
    <span class="btn-inner--text"> {{__('Create')}}</span>
</a>
@endcan
</div> --}}
