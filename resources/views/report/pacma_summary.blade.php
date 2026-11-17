@extends('layouts.admin')

@section('page-title')
    {{ __('Sales & Collection Summary') }}
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Furniture & Electronic System Summary') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ __('Sales & Collection Summary') }}</div>
            </div>
        </div>

        <div class="card mb-4">
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

        <div class="row">
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
