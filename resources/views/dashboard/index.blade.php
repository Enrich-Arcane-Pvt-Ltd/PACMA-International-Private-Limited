@extends('layouts.admin')

@section('page-title')
    {{ __('Dashboard') }}
@endsection

@section('content')
    @php
        $users = \Auth::user();
    @endphp
    <section class="section pacma-dashboard">
        <style>
            @media (min-width: 992px) {
                .pacma-dashboard {
                    margin-top: 6px;
                }
            }

            .pacma-hero {
                background: linear-gradient(135deg, #0F172A 0%, #1E293B 62%, #7C2D12 100%);
                border-radius: 18px;
                padding: 28px 32px;
                color: #FFFFFF;
                position: relative;
                overflow: hidden;
                box-shadow: 0 14px 34px rgba(15, 23, 42, 0.16);
                margin-bottom: 24px;
            }

            .pacma-hero::after {
                content: '';
                position: absolute;
                right: -60px;
                top: -60px;
                width: 240px;
                height: 240px;
                background: radial-gradient(circle, rgba(249, 115, 22, 0.28) 0%, rgba(249, 115, 22, 0) 70%);
                pointer-events: none;
            }

            .pacma-hero-badge {
                display: inline-block;
                background: transparent;
                border: none;
                color: #FFFFFF;
                font-size: 14.5px;
                font-weight: 700;
                letter-spacing: 1px;
                text-transform: uppercase;
                padding: 0;
                margin-bottom: 10px;
            }

            .pacma-hero h2 {
                font-size: 26px;
                font-weight: 800;
                color: #FFFFFF;
                margin-bottom: 6px;
                letter-spacing: -0.3px;
            }

            .pacma-hero p {
                color: #CBD5E1;
                font-size: 14px;
                margin-bottom: 0;
                max-width: 680px;
                line-height: 1.55;
            }

            .pacma-action-card {
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: #FFFFFF;
                border: 1px solid #E2E8F0;
                border-radius: 14px;
                padding: 18px 20px;
                text-decoration: none !important;
                box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
                transition: all 0.22s ease;
                margin-bottom: 18px;
                height: calc(100% - 18px);
            }

            .pacma-action-card:hover {
                transform: translateY(-4px);
                border-color: #F97316;
                box-shadow: 0 12px 26px rgba(249, 115, 22, 0.14);
            }

            .pacma-icon-box {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                flex-shrink: 0;
                margin-right: 14px;
            }

            .pacma-icon-orange { background: #FFF7ED; color: #EA580C; }
            .pacma-icon-blue   { background: #EFF6FF; color: #2563EB; }
            .pacma-icon-green  { background: #ECFDF5; color: #059669; }
            .pacma-icon-amber  { background: #FEF3C7; color: #D97706; }
            .pacma-icon-slate  { background: #F1F5F9; color: #334155; }
            .pacma-icon-purple { background: #F5F3FF; color: #7C3AED; }

            .pacma-card-title {
                font-size: 15px;
                font-weight: 700;
                color: #0F172A;
                margin-bottom: 2px;
            }

            .pacma-card-sub {
                font-size: 12px;
                color: #64748B;
                margin-bottom: 0;
            }

            .pacma-arrow {
                color: #94A3B8;
                font-size: 13px;
                transition: transform 0.2s ease, color 0.2s ease;
            }

            .pacma-action-card:hover .pacma-arrow {
                transform: translateX(3px);
                color: #F97316;
            }

            .pacma-report-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 11px 14px;
                border-radius: 11px;
                border: 1px solid #F1F5F9;
                background: #F8FAFC;
                text-decoration: none !important;
                margin-bottom: 10px;
                transition: all 0.2s ease;
            }

            .pacma-report-item:last-child {
                margin-bottom: 0;
            }

            .pacma-report-item:hover {
                background: #FFF7ED;
                border-color: #FED7AA;
                transform: translateX(3px);
            }

            .pacma-report-item span {
                font-size: 13.5px;
                font-weight: 600;
                color: #1E293B;
            }

            .pacma-report-icon {
                width: 34px;
                height: 34px;
                border-radius: 9px;
                background: #FFFFFF;
                color: #F97316;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                margin-right: 12px;
                box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
            }
        </style>

        @if (\Auth::user()->can('show dashboard'))
            {{-- Hero Welcome Banner --}}
            <div class="pacma-hero">
                <div class="pacma-hero-badge">
                    PACMA International Private Limited
                </div>
                <h2>{{ __('Welcome back') }}, {{ \Auth::user()->name }}!</h2>
                <p class="mb-3">
                    Manage your Furniture &amp; Electronic sales, hire purchase contracts, inventory, and financial reports from one streamlined workspace.
                </p>
                @if (\Auth::guard('customer')->check())
                    <a href="{{ route('customer.profile') }}" class="btn btn-primary btn-sm">
                        <i class="far fa-user mr-1"></i> {{ __('My profile') }}
                    </a>
                @elseif(\Auth::guard('vender')->check())
                    <a href="{{ route('vender.profile') }}" class="btn btn-primary btn-sm">
                        <i class="far fa-user mr-1"></i> {{ __('My profile') }}
                    </a>
                @else
                    <a href="{{ route('profile') }}" class="btn btn-primary btn-sm">
                        <i class="far fa-user mr-1"></i> {{ __('My profile') }}
                    </a>
                @endif
            </div>

            {{-- Primary Quick Actions --}}
            <div class="row">
                <div class="col-xl-3 col-md-6 col-12">
                    <a href="{{ route('sales_direct.index') }}" class="pacma-action-card">
                        <div class="d-flex align-items-center">
                            <div class="pacma-icon-box pacma-icon-orange">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <div class="pacma-card-title">{{ __('Cash Sales') }}</div>
                                <p class="pacma-card-sub">Direct &amp; cash invoices</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right pacma-arrow"></i>
                    </a>
                </div>

                <div class="col-xl-3 col-md-6 col-12">
                    <a href="{{ route('sales_hire.index') }}" class="pacma-action-card">
                        <div class="d-flex align-items-center">
                            <div class="pacma-icon-box pacma-icon-blue">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <div>
                                <div class="pacma-card-title">{{ __('Hire Sales') }}</div>
                                <p class="pacma-card-sub">Credit &amp; installments</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right pacma-arrow"></i>
                    </a>
                </div>

                <div class="col-xl-3 col-md-6 col-12">
                    <a href="{{ route('price-list.index') }}" class="pacma-action-card">
                        <div class="d-flex align-items-center">
                            <div class="pacma-icon-box pacma-icon-green">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div>
                                <div class="pacma-card-title">{{ __('Price List') }}</div>
                                <p class="pacma-card-sub">Product pricing &amp; rates</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right pacma-arrow"></i>
                    </a>
                </div>

                <div class="col-xl-3 col-md-6 col-12">
                    <a href="{{ route('advance_payement.index') }}" class="pacma-action-card">
                        <div class="d-flex align-items-center">
                            <div class="pacma-icon-box pacma-icon-amber">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div>
                                <div class="pacma-card-title">{{ __('Advance Payment') }}</div>
                                <p class="pacma-card-sub">Customer advance receipts</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right pacma-arrow"></i>
                    </a>
                </div>
            </div>

            {{-- Operations & Reports Split --}}
            <div class="row mt-2">
                <div class="{{ Gate::check('manage report') ? 'col-lg-7' : 'col-12' }}">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4><i class="fas fa-th-large text-warning mr-2"></i>{{ __('Quick Operations') }}</h4>
                            <span class="text-muted small">Core Modules</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <a href="{{ route('customer.index') }}" class="pacma-action-card">
                                        <div class="d-flex align-items-center">
                                            <div class="pacma-icon-box pacma-icon-blue">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div>
                                                <div class="pacma-card-title">{{ __('Customer') }}</div>
                                                <p class="pacma-card-sub">Manage customer profiles</p>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right pacma-arrow"></i>
                                    </a>
                                </div>

                                <div class="col-md-6 col-12">
                                    <a href="{{ route('vender.index') }}" class="pacma-action-card">
                                        <div class="d-flex align-items-center">
                                            <div class="pacma-icon-box pacma-icon-purple">
                                                <i class="fas fa-truck"></i>
                                            </div>
                                            <div>
                                                <div class="pacma-card-title">{{ __('Supplier') }}</div>
                                                <p class="pacma-card-sub">Vendors &amp; purchase partners</p>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right pacma-arrow"></i>
                                    </a>
                                </div>

                                <div class="col-md-6 col-12">
                                    <a href="{{ route('product.index') }}" class="pacma-action-card">
                                        <div class="d-flex align-items-center">
                                            <div class="pacma-icon-box pacma-icon-orange">
                                                <i class="fas fa-couch"></i>
                                            </div>
                                            <div>
                                                <div class="pacma-card-title">{{ __('Items') }}</div>
                                                <p class="pacma-card-sub">Furniture &amp; electronics catalog</p>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right pacma-arrow"></i>
                                    </a>
                                </div>

                                <div class="col-md-6 col-12">
                                    <a href="{{ route('stocks_report.index') }}" class="pacma-action-card">
                                        <div class="d-flex align-items-center">
                                            <div class="pacma-icon-box pacma-icon-green">
                                                <i class="fas fa-boxes"></i>
                                            </div>
                                            <div>
                                                <div class="pacma-card-title">{{ __('Inventory') }}</div>
                                                <p class="pacma-card-sub">Live warehouse stock</p>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right pacma-arrow"></i>
                                    </a>
                                </div>

                                <div class="col-md-6 col-12">
                                    <a href="{{ route('hire_purchase_contract.index') }}" class="pacma-action-card">
                                        <div class="d-flex align-items-center">
                                            <div class="pacma-icon-box pacma-icon-amber">
                                                <i class="fas fa-wallet"></i>
                                            </div>
                                            <div>
                                                <div class="pacma-card-title">{{ __('Collections') }}</div>
                                                <p class="pacma-card-sub">Monthly installment payments</p>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right pacma-arrow"></i>
                                    </a>
                                </div>

                                <div class="col-md-6 col-12">
                                    <a href="{{ route('purchase-orders.create-grn') }}" class="pacma-action-card">
                                        <div class="d-flex align-items-center">
                                            <div class="pacma-icon-box pacma-icon-slate">
                                                <i class="fas fa-clipboard-check"></i>
                                            </div>
                                            <div>
                                                <div class="pacma-card-title">{{ __('GRN') }}</div>
                                                <p class="pacma-card-sub">Goods received notes</p>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right pacma-arrow"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (Gate::check('manage report'))
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4><i class="fas fa-chart-line text-warning mr-2"></i>{{ __('Reports') }}</h4>
                                <span class="badge badge-primary">Analytics</span>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('cash_book.index') }}" class="pacma-report-item">
                                    <div class="d-flex align-items-center">
                                        <div class="pacma-report-icon"><i class="fas fa-book"></i></div>
                                        <span>Daily Cash Report</span>
                                    </div>
                                    <i class="fas fa-arrow-right pacma-arrow"></i>
                                </a>

                                <a href="{{ route('ele_profit.index') }}" class="pacma-report-item">
                                    <div class="d-flex align-items-center">
                                        <div class="pacma-report-icon"><i class="fas fa-chart-pie"></i></div>
                                        <span>Daily Sales &amp; Profit Report</span>
                                    </div>
                                    <i class="fas fa-arrow-right pacma-arrow"></i>
                                </a>

                                <a href="{{ route('collection_report.index') }}" class="pacma-report-item">
                                    <div class="d-flex align-items-center">
                                        <div class="pacma-report-icon"><i class="fas fa-coins"></i></div>
                                        <span>Collection Report</span>
                                    </div>
                                    <i class="fas fa-arrow-right pacma-arrow"></i>
                                </a>

                                <a href="{{ route('stocks_report.index') }}" class="pacma-report-item">
                                    <div class="d-flex align-items-center">
                                        <div class="pacma-report-icon"><i class="fas fa-cubes"></i></div>
                                        <span>Stock Report</span>
                                    </div>
                                    <i class="fas fa-arrow-right pacma-arrow"></i>
                                </a>

                                <a href="{{ route('stock_item_in_out_report.index') }}" class="pacma-report-item">
                                    <div class="d-flex align-items-center">
                                        <div class="pacma-report-icon"><i class="fas fa-exchange-alt"></i></div>
                                        <span>Item Wise In &amp; Out Stock Report</span>
                                    </div>
                                    <i class="fas fa-arrow-right pacma-arrow"></i>
                                </a>

                                <a href="{{ route('stock_in_out_report.index') }}" class="pacma-report-item">
                                    <div class="d-flex align-items-center">
                                        <div class="pacma-report-icon"><i class="fas fa-warehouse"></i></div>
                                        <span>All Products In &amp; Out Stock Report</span>
                                    </div>
                                    <i class="fas fa-arrow-right pacma-arrow"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-danger mb-0">{{ __('Permission Denied') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection
