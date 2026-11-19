@extends('layouts.admin')

@section('page-title')
    {{ __('Sales & Collection Summary') }}
@endsection

@push('css-page')
<style>
    .pacma-print-header {
        display: none;
    }
    @media print {
        @page {
            size: A4 portrait;
            margin: 10mm 12mm;
        }
        body, #app, .main-wrapper, .main-content, .section {
            background: #fff !important;
            padding: 0 8px !important;
            margin: 0 !important;
            left: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow: visible !important;
            box-sizing: border-box !important;
        }
        .section .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        .main-sidebar,
        .main-navbar,
        .pacma-topbar-wrap,
        .bg-main_2,
        .section-header,
        .section-header-breadcrumb,
        .pacma-summary-filter,
        .main-footer,
        .no-print {
            display: none !important;
        }
        .pacma-print-header {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 2px solid #F97316;
            padding: 4px 0 10px 0;
            margin-bottom: 14px;
        }
        .pacma-print-header img {
            height: 42px;
            width: auto;
            flex-shrink: 0;
            object-fit: contain;
        }
        .pacma-print-header .ph-center {
            text-align: center;
            flex: 1;
        }
        .pacma-print-header h2 {
            margin: 0 0 2px;
            font-size: 18px;
            font-weight: 700;
            color: #0F172A;
        }
        .pacma-print-header p {
            margin: 0;
            font-size: 11px;
            color: #475569;
        }
        .pacma-kpi-row {
            display: flex !important;
            flex-wrap: nowrap !important;
            margin-left: -6px !important;
            margin-right: -6px !important;
            margin-bottom: 12px !important;
        }
        .pacma-kpi-row > div {
            flex: 0 0 25% !important;
            max-width: 25% !important;
            padding-left: 6px !important;
            padding-right: 6px !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #CBD5E1 !important;
            border-radius: 8px !important;
            break-inside: avoid;
            margin-bottom: 12px !important;
        }
        .pacma-kpi-row .card-body {
            padding: 10px 12px !important;
        }
        .pacma-kpi-row h4 {
            font-size: 14px !important;
        }
        .table-responsive {
            overflow: visible !important;
        }
        .table thead th,
        .table tbody td,
        .table tfoot td {
            padding: 6px 8px !important;
            font-size: 11px !important;
            white-space: normal !important;
            border: 1px solid #CBD5E1 !important;
        }
        .table thead th {
            background-color: #F1F5F9 !important;
            color: #0F172A !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endpush

@section('content')
    <section class="section">
        <div class="pacma-print-header">
            <img src="{{ asset('storage/uploads/logo/logo.png') }}" alt="PACMA">
            <div class="ph-center">
                <h2>PACMA International Private Limited</h2>
                <p>No: 61, K C De Silvapuraya, Thimbirigaskatuwa</p>
                <p style="font-weight: 700; color: #0F172A; margin-top: 3px;">{{ __('Furniture & Electronic System Summary') }}</p>
            </div>
            <div style="text-align: right; font-size: 10.5px; color: #475569;">
                <div><strong>Branch:</strong> {{ $branchId && isset($branches[$branchId]) ? $branches[$branchId] : __('All Branches') }}</div>
                <div><strong>Printed:</strong> {{ date('Y-m-d H:i') }}</div>
            </div>
        </div>

        <div class="section-header">
            <h1>{{ __('Furniture & Electronic System Summary') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ __('Sales & Collection Summary') }}</div>
            </div>
        </div>

        <div class="card mb-4 pacma-summary-filter">
            <div class="card-body">
                <form method="GET" action="{{ route('pacma.summary.index') }}" class="row align-items-end">
                    <div class="col-md-3 mb-2">
                        <label>{{ __('Summary Type') }}</label>
                        <select name="period" class="form-control">
                            <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>{{ __('Daily Summary') }}</option>
                            <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>{{ __('Weekly Summary') }}</option>
                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>{{ __('Monthly Summary') }}</option>
                            <option value="annual" {{ $period == 'annual' ? 'selected' : '' }}>{{ __('Annual Summary') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>{{ __('Reference Date') }}</label>
                        <input type="date" name="date" value="{{ $date }}" class="form-control">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>{{ __('Branch') }}</label>
                        <select name="branch_id" class="form-control">
                            <option value="">{{ __('All Branches') }}</option>
                            @foreach ($branches as $id => $name)
                                <option value="{{ $id }}" {{ $branchId == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 d-flex" style="gap: 8px;">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search mr-1"></i> {{ __('Filter') }}
                        </button>
                        <button type="button" onclick="window.print()" class="btn btn-outline-primary">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-3">
            <h5 class="font-weight-bold text-dark">{{ $periodLabel }}</h5>
        </div>

        <div class="row pacma-kpi-row">
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted small font-weight-bold">01. DOCUMENT FEE (CHARGE)</div>
                        <h4 class="mt-2 mb-0 font-weight-bold text-primary">Rs. {{ number_format($documentFee, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted small font-weight-bold">02. DOWN PAYMENT</div>
                        <h4 class="mt-2 mb-0 font-weight-bold text-success">Rs. {{ number_format($downPayment, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted small font-weight-bold">03. CASH SALES</div>
                        <h4 class="mt-2 mb-0 font-weight-bold text-info">Rs. {{ number_format($cashSales, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted small font-weight-bold">04. CREDIT SALES</div>
                        <h4 class="mt-2 mb-0 font-weight-bold text-warning">Rs. {{ number_format($creditSales, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>{{ __('Detailed Breakdown') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ $period == 'annual' ? __('Month') : __('Date') }}</th>
                                <th class="text-center">{{ __('Transactions') }}</th>
                                <th class="text-right">{{ __('01. Document Fee') }}</th>
                                <th class="text-right">{{ __('02. Down Payment') }}</th>
                                <th class="text-right">{{ __('03. Cash Sales') }}</th>
                                <th class="text-right">{{ __('04. Credit Sales') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($breakdown as $row)
                                <tr>
                                    <td>{{ $row['label'] }}</td>
                                    <td class="text-center">{{ $row['count'] }}</td>
                                    <td class="text-right">{{ number_format($row['document_fee'], 2) }}</td>
                                    <td class="text-right">{{ number_format($row['down_payment'], 2) }}</td>
                                    <td class="text-right">{{ number_format($row['cash_sales'], 2) }}</td>
                                    <td class="text-right">{{ number_format($row['credit_sales'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">{{ __('No transactions found for this period.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold bg-light">
                                <td colspan="2">{{ __('Total') }}</td>
                                <td class="text-right">{{ number_format($documentFee, 2) }}</td>
                                <td class="text-right">{{ number_format($downPayment, 2) }}</td>
                                <td class="text-right">{{ number_format($cashSales, 2) }}</td>
                                <td class="text-right">{{ number_format($creditSales, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
