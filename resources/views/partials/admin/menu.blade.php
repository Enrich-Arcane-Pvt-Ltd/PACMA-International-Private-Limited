@php
    $logo = asset(Storage::url('uploads/logo/'));
    $company_logo = Utility::getValByName('company_logo');
    $company_small_logo = Utility::getValByName('company_small_logo');
@endphp
<style>
    .alert {
        background-color: #ff050578 !important;
        color: white !important;
    }

    /* .longmenu-item {
        padding: 0.3rem 0;
        background-color: #ffffff;
    } */
</style>
<div class="main-sidebar sidebar-style-2 ">
    <aside id="sidebar-wrapper" class=" bg-main_1 m-3 p-1 br-4 shadow  bg-body-tertiary" style="min-height: 98vh">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">
                <img class="img-fluid"
                    src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png') }}"
                    alt="PACMA International">
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('dashboard') }}">
                <img class="img-fluid"
                    src="{{ $logo . '/' . (isset($company_small_logo) && !empty($company_small_logo) ? $company_small_logo : 'small_logo.png') }}"
                    alt="PACMA">
            </a>
        </div>
        <ul class="sidebar-menu mb-4">

            <li class="dropdown {{ Request::route()->getName() == 'dashboard' ? ' active' : '' }} ">
                <a class="nav-link" href="{{ route('dashboard') }}"> <i class="fas fa-tachometer-alt"></i>
                    <span>{{ __('Dashboard') }}</span></a>
            </li>
            {{-- @endif --}}

            @if (Gate::check('manage authorization'))
                <li
                    class="dropdown {{ Request::route()->getName() == 'authorization_requests.index' ? ' active' : '' }} ">
                    <a class="nav-link" href="{{ route('authorization_requests.index') }}" id="authorization_requests">
                        <i class="fab fa-autoprefixer"></i>
                        <span style="width: 85%;">{{ __('Authorization Requests') }}</span>
                        <span style="width: 15%; float: right;" id="authorization_request_count">00</span>
                    </a>
                </li>
            @endif

            {{-- sale --}}
            @if (Gate::check('manage sale') ||
                    Gate::check('manage advances') ||
                    Gate::check('manage invoice') ||
                    Gate::check('show report'))
                <li
                    class=" dropdown {{
                    Request::segment(1) == 'sales_direct' ||
                    Request::segment(1) == 'sales_hire' ||
                    Request::segment(1) == 'hire_purchase_contract' ||
                    Request::segment(1) == 'advance_payement' ||
                    Request::segment(1) == 'item_return' ||
                    Request::segment(1) == 'ele_profit' ||
                    Request::segment(1) == 'hire_purchase_outstanding_report' ||
                    Request::segment(1) == 'closed_hire_purchase_report' ||
                    Request::segment(1) == 'sales_repayment_sheet' ||
                    Request::segment(1) == 'stocks_report' ||
                    Request::segment(1) == 'stock_transfers_report' ||
                    Request::route()->getName() == 'invoice.index' ||
                    Request::route()->getName() == 'invoice.show' ||
                    Request::segment(1) == 'stock_item_in_out_report' ||
                    Request::segment(1) == 'stock_in_out_report'
                        ? ' active'
                        : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="far fa-credit-card"></i><span>{{ __('Cash & Hire Sales') }}</span></a>
                    <ul
                        class="dropdown-menu {{
                        Request::segment(1) == 'sales_direct' ||
                        Request::segment(1) == 'sales_hire' ||
                        Request::segment(1) == 'hire_purchase_contract' ||
                        Request::segment(1) == 'advance_payement' ||
                        Request::segment(1) == 'item_return' ||
                        Request::segment(1) == 'ele_profit' ||
                        Request::segment(1) == 'hire_purchase_outstanding_report' ||
                        Request::segment(1) == 'closed_hire_purchase_report' ||
                        Request::segment(1) == 'sales_repayment_sheet' ||
                        Request::segment(1) == 'stocks_report' ||
                        Request::route()->getName() == 'invoice.index' ||
                        Request::route()->getName() == 'invoice.show' ||
                        Request::segment(1) == 'stock_transfers_report' ||
                        Request::segment(1) == 'stock_item_in_out_report' ||
                        Request::segment(1) == 'stock_in_out_report'
                            ? 'display:block'
                            : '' }}">

                        @can('manage sale')
                            <li class="{{ Request::segment(1) == 'sales_direct' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('sales_direct.index') }}">{{ __('Cash Sales') }}</a>
                            </li>
                        @endcan
                        @can('manage sale')
                            <li class="{{ Request::segment(1) == 'sales_hire' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('sales_hire.index') }}">{{ __('Hire Sales') }}</a>
                            </li>
                        @endcan
                        {{-- @can('manage sale')
                        <li class="{{ (Request::segment(1) == 'sale')?' active':''}}">
                            <a class="nav-link" href="{{ route('sale.index') }}">{{ __('Cash & Hire Sales') }}</a>
                        </li>
                    @endcan --}}
                        @can('manage sale')
                            <li class="{{ Request::segment(1) == 'hire_purchase_contract' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('hire_purchase_contract.index') }}">{{ __('Hire Sales Contracts') }}</a>
                            </li>
                        @endcan
                        @can('manage advances')
                            <li class="{{ Request::segment(1) == 'advance_payement' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('advance_payement.index') }}">{{ __('Advance Payments') }}</a>
                            </li>
                        @endcan
                        @can('manage invoice')
                            <li
                                class="{{ Request::route()->getName() == 'invoice.index' || Request::route()->getName() == 'invoice.create' || Request::route()->getName() == 'invoice.edit' || Request::route()->getName() == 'invoice.show' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('invoice.index') }}">{{ __('Invoices') }}</a>
                            </li>
                        @endcan
                        @can('manage item return')
                            <li class="{{ (Request::segment(1) == 'item_return') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('item_return.index') }}">{{ __('Sales Return Note (SRN)') }}</a>
                            </li>
                        @endcan
                        @can('manage sale')
                            <li class="dropdown {{ Request::segment(1) == 'pacma-forms' ? ' active' : '' }}">
                                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><span>{{ __('PACMA Forms') }}</span></a>
                                <ul class="dropdown-menu {{ Request::segment(1) == 'pacma-forms' ? 'display:block' : '' }}">
                                    <li><a class="nav-link" target="_blank" href="{{ route('pacma.forms.loan_requisition') }}">{{ __('Loan Requisition Form') }}</a></li>
                                    <li><a class="nav-link" target="_blank" href="{{ route('pacma.forms.application_form') }}">{{ __('Application Form') }}</a></li>
                                    <li><a class="nav-link" target="_blank" href="{{ route('pacma.forms.guarantor_declaration') }}">{{ __('Guarantor Declaration') }}</a></li>
                                    <li><a class="nav-link" target="_blank" href="{{ route('pacma.forms.guarantee_indemnity') }}">{{ __('Guarantee & Indemnity') }}</a></li>
                                </ul>
                            </li>
                        @endcan
                        @if (Gate::check('manage report'))
                            <li
                                class="dropdown {{ Request::segment(1) == 'ele_profit' ||
                                Request::segment(1) == 'hire_purchase_outstanding_report' ||
                                Request::segment(1) == 'closed_hire_purchase_report' ||
                                Request::segment(1) == 'sales_repayment_sheet' ||
                                Request::segment(1) == 'stocks_report' ||
                                Request::segment(1) == 'stock_transfers_report' ||
                                Request::segment(1) == 'stock_item_in_out_report' ||
                                Request::segment(1) == 'stock_in_out_report'
                                    ? ' active'
                                    : '' }}">
                                <a href="#" class="nav-link has-dropdown"
                                    data-toggle="dropdown"><span>{{ __('Reports') }}</span></a>
                                <ul
                                    class="dropdown-menu {{
                                    Request::segment(1) == 'sales_direct' ||
                                    Request::segment(1) == 'sales_hire' ||
                                    Request::segment(1) == 'hire_purchase_contract' ||
                                    Request::segment(1) == 'advance_payement' ||
                                    Request::segment(1) == 'item_return' ? 'display:block' : '' }}">
                                    @can('show report')
                                        <li class="{{ Request::route()->getName() == 'pacma.summary.index' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('pacma.summary.index') }}">{{ __('Sales & Collection Summary') }}</a>
                                        </li>
                                    @endcan
                                    @can('show report')
                                        <li class="{{ Request::segment(1) == 'ele_profit' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('ele_profit.index') }}">{{ __('Daily Sales Profit Report') }}</a>
                                        </li>
                                    @endcan
                                    @can('show report')
                                        <li
                                            class="{{ Request::segment(1) == 'hire_purchase_outstanding_report' ? ' active' : '' }} longmenu-item">
                                            <a class="nav-link"
                                                href="{{ route('hire_purchase_outstanding_report.index') }}"
                                                style="line-height: 17px">{{ __('Outstanding Hire Sales Report') }}</a>
                                        </li>
                                    @endcan
                                    {{-- @can('show report')
                                    <li class="{{ Request::segment(1) == 'outstanding_loan_arrears_report' ? ' active' : '' }} longmenu-item">
                                        <a class="nav-link" href="{{ route('outstanding_loan_arrears_report.index') }}" style="line-height: 17px">{{ __('Outstanding Loan Arrears Report') }}</a>
                                    </li>
                                @endcan --}}
                                    @can('show report')
                                        <li
                                            class="{{ Request::segment(1) == 'closed_hire_purchase_report' ? ' active' : '' }} longmenu-item">
                                            <a class="nav-link" href="{{ route('closed_hire_purchase_report.index') }}"
                                                style="line-height: 17px">{{ __('Hire Sales Settlement Report') }}</a>
                                        </li>
                                    @endcan
                                    @can('show report')
                                        <li
                                            class="{{ Request::segment(1) == 'sales_repayment_sheet' ? ' active' : '' }} longmenu-item">
                                            <a class="nav-link" href="{{ route('sales_repayment_sheet.index') }}"
                                                style="line-height: 17px">{{ __('Hire Sales Repayment Sheet') }}</a>
                                        </li>
                                    @endcan
                                    @can('show report')
                                        <li class="{{ Request::segment(1) == 'stocks_report' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('stocks_report.index') }}">{{ __('Stock Report') }}</a>
                                        </li>
                                    @endcan
                                    @can('show report')
                                        <li
                                            class="{{ Request::segment(1) == 'stock_transfers_report' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('stock_transfers_report.index') }}">{{ __('Stock Transfer Report') }}</a>
                                        </li>
                                    @endcan
                                    @can('show report')
                                        <li
                                            class="{{ Request::segment(1) == 'stock_item_in_out_report' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('stock_item_in_out_report.index') }}">{{ __('Item Wise In & Out Stock Report') }}</a>
                                        </li>
                                    @endcan
                                    @can('show report')
                                        <li
                                            class="{{ Request::segment(1) == 'stock_in_out_report' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('stock_in_out_report.index') }}">{{ __('All Products In & Out Stock Report') }}</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- inventory --}}
            @if (Gate::check('manage stock') ||
                    Gate::check('manage purchase order') ||
                    Gate::check('manage grn') ||
                    Gate::check('create sale'))
                <li
                    class="dropdown {{ Request::segment(1) == 'stock' || Request::segment(1) == 'price-list' || Request::segment(1) == 'purchase-order' || Request::segment(1) == 'purchase-orders' || Request::segment(1) == 'grn' || Request::segment(1) == 'grn-order' || Request::segment(1) == 'grnOrderEdit' || Request::route()->getName() == 'purchase-orders.create-grn' || Request::route()->getName() == 'stock_transfers.transfers' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-dolly-flatbed"></i> <span>{{ __('Inventory') }}</span></a>
                    <ul class="dropdown-menu"
                        style="{{ Request::segment(1) == 'stock' || Request::segment(1) == 'price-list' || Request::segment(1) == 'purchase-order' || Request::segment(1) == 'purchase-orders' || Request::segment(1) == 'grn' || Request::segment(1) == 'grn-order' || Request::segment(1) == 'grnOrderEdit' || Request::route()->getName() == 'purchase-orders.create-grn' || Request::route()->getName() == 'stock_transfers.transfers' ? 'display:block;' : '' }}">

                        @can('create sale')
                            <li class="{{ Request::route()->getName() == 'price-list.index' ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('price-list.index') }}">{{ __('Price List') }}</a>
                            </li>
                        @endcan

                        @can('manage stock')
                            <li
                                class="{{ Request::route()->getName() == 'stock.index' || Request::route()->getName() == 'stock_transfers.transfers' ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('stock.index') }}">{{ __('Stock') }}</a>
                            </li>
                        @endcan

                        @can('manage purchase order')
                            <li class="{{ Request::route()->getName() == 'purchase-order.index' ? 'active' : '' }}">
                                <a class="nav-link"
                                     href="{{ route('purchase-order.index') }}">{{ __('Purchase Orders') }}</a>
                            </li>
                        @endcan

                        @can('manage grn')
                            <li class="{{ Request::route()->getName() == 'purchase-orders.create-grn' || Request::segment(1) == 'grn' || Request::segment(1) == 'grn-order' || Request::segment(1) == 'grnOrderEdit' || Request::segment(1) == 'purchase-orders' ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('purchase-orders.create-grn') }}">{{ __('GRN') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            {{-- banking --}}
            @if (Gate::check('manage bank account') || Gate::check('manage transfer'))
                <li
                    class="dropdown {{ Request::segment(1) == 'bank-account' || Request::segment(1) == 'bank-transfer' ? ' active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-landmark"></i> <span>{{ __('Banking') }}</span></a>
                    <ul
                        class="dropdown-menu {{ Request::segment(1) == 'bank-account' || Request::segment(1) == 'bank-transfer' ? 'display:block' : '' }}">
                        @can('manage bank account')
                            <li
                                class="{{ Request::route()->getName() == 'bank-account.index' || Request::route()->getName() == 'bank-account.create' || Request::route()->getName() == 'bank-account.edit' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('bank-account.index') }}">{{ __('Accounts') }}</a>
                            </li>
                        @endcan
                        @can('manage transfer')
                            <li
                                class="{{ Request::route()->getName() == 'bank-transfer.index' || Request::route()->getName() == 'bank-transfer.create' || Request::route()->getName() == 'bank-transfer.edit' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('bank-transfer.index') }}">{{ __('Transfers') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endif

            {{-- reports --}}
            @if (Gate::check('manage report'))
                <li
                    class="dropdown {{ Request::segment(1) == 'due_payment' ||
                    Request::segment(1) == 'receiptReport' ||
                    Request::route()->getName() == 'receiptsearch' ||
                    Request::segment(1) == 'accessories_profit' ||
                    Request::segment(1) == 'collection_report' ||
                    Request::segment(1) == 'loan_report' ||
                    Request::segment(1) == 'loan_stock_report' ||
                    Request::segment(1) == 'loan_installment_report' ||
                    Request::segment(1) == 'customer_report' ||
                    Request::segment(1) == 'petty_cash_report' ||
                    Request::segment(1) == 'supplier_report' ||
                    Request::segment(1) == 'cash_collector_report' ||
                    Request::segment(1) == 'customer_ledger_report' ||
                    Request::segment(1) == 'item_return_report'
                        ? ' active'
                        : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-chart-area"></i> <span>{{ __('Other Reports') }}</span></a>
                    <ul
                        class="dropdown-menu {{ Request::segment(1) == 'due_payment' ||
                        Request::segment(1) == 'receiptReport' ||
                        Request::route()->getName() == 'receiptsearch' ||
                        Request::segment(1) == 'accessories_profit' ||
                        Request::segment(1) == 'collection_report' ||
                        Request::segment(1) == 'loan_report' ||
                        Request::segment(1) == 'loan_stock_report' ||
                        Request::segment(1) == 'loan_installment_report' ||
                        Request::segment(1) == 'customer_report' ||
                        Request::segment(1) == 'petty_cash_report' ||
                        Request::segment(1) == 'supplier_report' ||
                        Request::segment(1) == 'cash_collector_report' ||
                        Request::segment(1) == 'customer_ledger_report' ||
                        Request::segment(1) == 'item_return_report'
                            ? 'display:block'
                            : '' }}">
                        {{-- @can('show report')
                            <li class="{{ (Request::segment(1) == 'ele_profit' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('ele_profit.index')}}">{{ __('Daily Sales Profit Report') }}</a>
                            </li>
                        @endcan --}}
                        {{-- @can('show report')
                            <li class="{{ (Request::segment(1) == 'accessories_profit' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('accessories_profit.index')}}">{{ __('Accessory Profit Report') }}</a>
                            </li>
                        @endcan --}}
                        {{-- @can('show report')
                            <li class="{{ (Request::segment(1) == 'hire_purchase_outstanding_report' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('hire_purchase_outstanding_report.index')}}" style="line-height: 17px">{{ __('Outstanding Hire Sales Report') }}</a>
                            </li>
                        @endcan --}}
                        {{-- @can('show report')
                            <li class="{{ (Request::segment(1) == 'sales_repayment_sheet' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('sales_repayment_sheet.index')}}">{{ __('Sales Repayment Sheet') }}</a>
                            </li>
                        @endcan --}}
                        {{-- @can('show report')
                            <li class="{{ (Request::segment(1) == 'loan_report') ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('loan_report.index')}}">{{ __('Loan Report') }}</a>
                            </li>
                        @endcan
                        @can('show report')
                            <li class="{{ (Request::segment(1) == 'loan_installment_report') ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('loan_installment_report.index')}}">{{ __('Loan Installment Report') }}</a>
                            </li>
                        @endcan
                        @can('show report')
                            <li class="{{ (Request::segment(1) == 'loan_stock_report') ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('loan_stock_report.index')}}">{{ __('Loan Stock Report') }}</a>
                            </li>
                        @endcan --}}
                        {{-- @can('show report')
                            <li class="{{ (Request::segment(1) == 'loan_outstanding_report' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('loan_outstanding_report.index')}}">{{ __('Outstanding Loan Report') }}</a>
                            </li>
                        @endcan --}}
                        @can('show report')
                            <li class="{{ Request::segment(1) == 'due_payment' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('due_payment.index') }}">{{ __('Due Payment Report') }}</a>
                            </li>
                        @endcan
                        {{-- @can('show report')
                            <li class="{{ (Request::segment(1) == 'repayment_sheet' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('repayment_sheet.index')}}">{{ __('Loan Repayment Sheet') }}</a>
                            </li>
                        @endcan --}}
                        {{-- @can('show report')
                            <li class="{{ (Request::segment(1) == 'loan_disbursement_report' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('loan_disbursement_report.index')}}">{{ __('Loan Disbursement Report') }}</a>
                            </li>
                        @endcan --}}
                        {{-- @can('show report')
                            <li class="{{ (Request::segment(1) == 'closed_loan_report' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('closed_loan_report.index')}}">{{ __('Closed Loan Report') }}</a>
                            </li>
                        @endcan --}}
                        {{-- @can('bill report')
                            <li class="{{ (Request::segment(1) == 'stocks_report' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('stocks_report.index')}}">{{ __('Stock Report') }}</a>
                            </li>
                        @endcan --}}
                        {{-- @can('show report')
                            <li class="{{ (Request::segment(1) == 'stock_transfers_report' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('stock_transfers_report.index')}}">{{ __('Stock Transfer Report') }}</a>
                            </li>
                        @endcan --}}
                        @can('show report')
                            <li class="{{ Request::segment(1) == 'cash_collector_report' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('cash_collector_report.index') }}">{{ __('Cash Collector Report') }}</a>
                            </li>
                        @endcan
                        @can('show report')
                            <li class="{{ Request::segment(1) == 'collection_report' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('collection_report.index') }}">{{ __('Collection Report') }}</a>
                            </li>
                        @endcan
                        @can('show report')
                            <li
                                class="{{ Request::segment(1) == 'receiptReport' || Request::route()->getName() == 'receiptsearch' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('receiptReport') }}">{{ __('Receipt Summary Report') }}</a>
                            </li>
                        @endcan
                        @can('show report')
                            <li class="{{ Request::segment(1) == 'petty_cash_report' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('petty_cash_report.index') }}">{{ __('Petty Cash Report') }}</a>
                            </li>
                        @endcan
                        {{-- @can('show report')
                            <li class="{{ (Request::segment(1) == 'item_return_report') ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('item_return_report.index')}}">{{ __('Item Return Report') }}</a>
                            </li>
                        @endcan --}}
                        @can('show report')
                            <li class="{{ Request::segment(1) == 'customer_report' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('customer_report.index') }}">{{ __('Customer Report') }}</a>
                            </li>
                        @endcan
                        @can('show report')
                            <li class="{{ Request::segment(1) == 'customer_ledger_report' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('customer_ledger_report.index') }}">{{ __('Customer Ledger Report') }}</a>
                            </li>
                        @endcan
                        @can('show report')
                            <li class="{{ Request::segment(1) == 'supplier_report' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('supplier_report.index') }}">{{ __('Supplier Report') }}</a>
                            </li>
                        @endcan
                        {{-- @can('show report')
                            <li class="{{ (Request::segment(1) == 'attendance_sheet' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('attendance_sheet.index')}}">{{ __('Attendance Sheet') }}</a>
                            </li>
                        @endcan --}}
                    </ul>
                </li>
            @endif


            {{-- expense --}}
            @if (Gate::check('manage payment voucher') || Gate::check('manage payment') || Gate::check('manage cheque'))
                <li
                    class="dropdown {{ Request::segment(1) == 'payment_voucher' || Request::segment(1) == 'serviceBill' || Request::segment(1) == 'petty-cash' || Request::segment(1) == 'cheque-print' ? ' active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-money-bill-wave"></i> <span>{{ __('Payments') }}</span></a>
                    <ul
                        class="dropdown-menu {{ Request::segment(1) == 'payment_voucher' || Request::segment(1) == 'serviceBill' || Request::segment(1) == 'petty-cash' || Request::segment(1) == 'cheque-print' ? 'display:block' : '' }}">
                        {{-- @can('manage bill')
                            <li class="{{ (Request::route()->getName() == 'serviceBill.index' || Request::route()->getName() == 'serviceBill.create' || Request::route()->getName() == 'serviceBill.edit') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('serviceBill.index') }}">{{  __('Bills') }}</a>
                            </li>
                        @endcan --}}
                        @if (Gate::check('manage payment voucher'))
                            <li
                                class="{{ Request::route()->getName() == 'payment_voucher.index' ? ' active' : '' }}">
                                <a href="#" class="nav-link has-dropdown"
                                    data-toggle="dropdown"><span>{{ __('Expenses') }}</span></a>
                                <ul
                                    class="dropdown-menu {{ Request::route()->getName() == 'payment_voucher.index' ? 'display:block' : '' }}">
                                    @can('manage payment voucher')
                                        <li
                                            class="{{ Request::route()->getName() == 'payment_voucher.index' || Request::route()->getName() == 'payment_voucher.create' || Request::route()->getName() == 'payment_voucher.edit' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('payment_voucher.index') }}">{{ __('Payment Voucher') }}</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endif
                        @can('manage petty cash')
                            <li
                                class="{{ Request::route()->getName() == 'petty-cash.index' || Request::route()->getName() == 'petty-cash.create' || Request::route()->getName() == 'petty-cash.edit' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('petty-cash.index') }}">{{ __('Petty Cash') }}</a>
                            </li>
                        @endcan
                        @can('manage cheque')
                            <li class="{{ Request::route()->getName() == 'cheque-print.index' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('cheque-print.index') }}">{{ __('Cheque Issue') }}</a>
                            </li>
                        @endcan
                        {{-- @can('manage debit note')
                            <li class="{{ (Request::route()->getName() == 'debit.note' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('debit.note') }}">{{  __('Debit Note') }}</a>
                            </li>
                        @endcan --}}
                    </ul>
                </li>
            @endif

            {{-- ACCOUNTING SYSTEM --}}
            @if (Gate::check('manage account group') ||
                    Gate::check('manage chart of account') ||
                    Gate::check('manage account transaction') ||
                    Gate::check('view sales journal') ||
                    Gate::check('view sales return journal') ||
                    Gate::check('view purchase journal') ||
                    Gate::check('view cash book') ||
                    Gate::check('view bank book') ||
                    Gate::check('view petty cash book') ||
                    Gate::check('view general journal') ||
                    Gate::check('view ledger account') ||
                    Gate::check('view control account') ||
                    Gate::check('view trial balance') ||
                    Gate::check('manage bank rec') ||
                    Gate::check('view bank rec') ||
                    Gate::check('manage debit note') ||
                    Gate::check('manage credit note') ||
                    Gate::check('view loss & profit') ||
                    Gate::check('view balance sheet'))
                <li
                    class="dropdown {{ Request::route()->getName() == 'account_types.index' ||
                    Request::route()->getName() == 'account_group.index' ||
                    Request::route()->getName() == 'chartofaccount.index' ||
                    Request::route()->getName() == 'account_transaction.index' ||
                    Request::route()->getName() == 'account_transaction.search' ||
                    Request::route()->getName() == 'sales_journal.index' ||
                    Request::route()->getName() == 'sales_journal.search' ||
                    Request::route()->getName() == 'sales_return_journal.index' ||
                    Request::route()->getName() == 'sales_return_journal.search' ||
                    Request::route()->getName() == 'purchase_journal.index' ||
                    Request::route()->getName() == 'purchase_journal.search' ||
                    Request::route()->getName() == 'purchase_return_journal.index' ||
                    Request::route()->getName() == 'cash_book.index' ||
                    Request::route()->getName() == 'cash_book.search' ||
                    Request::route()->getName() == 'daily_cash_book.index' ||
                    Request::route()->getName() == 'daily_cash_book.search' ||
                    Request::route()->getName() == 'bank_book.index' ||
                    Request::route()->getName() == 'bank_book.search' ||
                    Request::route()->getName() == 'petty_cash_book.index' ||
                    Request::route()->getName() == 'petty_cash_book.search' ||
                    Request::route()->getName() == 'general_journal.index' ||
                    Request::route()->getName() == 'ledger_account.index' ||
                    Request::route()->getName() == 'ledger_account.search' ||
                    Request::route()->getName() == 'control_account.index' ||
                    Request::route()->getName() == 'control_account.search' ||
                    Request::route()->getName() == 'bank_rec.index' ||
                    Request::route()->getName() == 'bank_rec.entries_search' ||
                    Request::route()->getName() == 'bank_rec.statement' ||
                    Request::route()->getName() == 'trial_balance.index' ||
                    Request::route()->getName() == 'trial_balance.search' ||
                    Request::route()->getName() == 'debit.note' ||
                    Request::route()->getName() == 'credit.note' ||
                    Request::route()->getName() == 'profit_loss_summary.index' ||
                    Request::route()->getName() == 'profit_loss_summary.search' ||
                    Request::route()->getName() == 'balance_sheet_summary.index' ||
                    Request::route()->getName() == 'balance_sheet_summary.search'
                        ? ' active'
                        : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-calculator"></i><span>{{ __('Accounting System') }}</span></a>
                    <ul
                        class="dropdown-menu {{ Request::route()->getName() == 'account_types.index' ||
                        Request::route()->getName() == 'account_group.index' ||
                        Request::route()->getName() == 'chartofaccount.index' ||
                        Request::route()->getName() == 'account_transaction.index' ||
                        Request::route()->getName() == 'account_transaction.search' ||
                        Request::route()->getName() == 'sales_journal.index' ||
                        Request::route()->getName() == 'sales_journal.search' ||
                        Request::route()->getName() == 'sales_return_journal.index' ||
                        Request::route()->getName() == 'sales_return_journal.search' ||
                        Request::route()->getName() == 'purchase_journal.index' ||
                        Request::route()->getName() == 'purchase_journal.search' ||
                        Request::route()->getName() == 'purchase_return_journal.index' ||
                        Request::route()->getName() == 'cash_book.index' ||
                        Request::route()->getName() == 'cash_book.search' ||
                        Request::route()->getName() == 'daily_cash_book.index' ||
                        Request::route()->getName() == 'daily_cash_book.search' ||
                        Request::route()->getName() == 'bank_book.index' ||
                        Request::route()->getName() == 'bank_book.search' ||
                        Request::route()->getName() == 'petty_cash_book.index' ||
                        Request::route()->getName() == 'petty_cash_book.search' ||
                        Request::route()->getName() == 'general_journal.index' ||
                        Request::route()->getName() == 'ledger_account.index' ||
                        Request::route()->getName() == 'ledger_account.search' ||
                        Request::route()->getName() == 'control_account.index' ||
                        Request::route()->getName() == 'control_account.search' ||
                        Request::route()->getName() == 'bank_rec.index' ||
                        Request::route()->getName() == 'bank_rec.entries_search' ||
                        Request::route()->getName() == 'bank_rec.statement' ||
                        Request::route()->getName() == 'trial_balance.index' ||
                        Request::route()->getName() == 'trial_balance.search' ||
                        Request::route()->getName() == 'debit.note' ||
                        Request::route()->getName() == 'credit.note' ||
                        Request::route()->getName() == 'profit_loss_summary.index' ||
                        Request::route()->getName() == 'profit_loss_summary.search' ||
                        Request::route()->getName() == 'balance_sheet_summary.index' ||
                        Request::route()->getName() == 'balance_sheet_summary.search'
                            ? 'display:block'
                            : '' }}">

                        @if (Gate::check('manage account group') ||
                                Gate::check('manage chart of account') ||
                                Gate::check('manage account transaction'))
                            <li
                                class="{{ Request::route()->getName() == 'account_types.index' || Request::route()->getName() == 'account_group.index' || Request::route()->getName() == 'chartofaccount.index' || Request::route()->getName() == 'account_transaction.index' || Request::route()->getName() == 'account_transaction.search' ? ' active' : '' }}">
                                <a href="#" class="nav-link has-dropdown"
                                    data-toggle="dropdown"><span>{{ __('Maintenance') }}</span></a>
                                <ul
                                    class="dropdown-menu {{ Request::route()->getName() == 'account_types.index' || Request::route()->getName() == 'account_group.index' || Request::route()->getName() == 'chartofaccount.index' || Request::route()->getName() == 'account_transaction.index' || Request::route()->getName() == 'account_transaction.search' ? 'display:block' : '' }}">
                                    {{-- @can('manage account group')
                                        <li class="{{ (Request::route()->getName() == 'account_group.index') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{ route('account_group.index') }}">{{  __('Account Groups') }}</a>
                                        </li>
                                    @endcan --}}
                                    @can('manage chart of account')
                                        <li
                                            class="{{ Request::route()->getName() == 'account_types.index' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('account_types.index') }}">{{ __('Account Types') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage chart of account')
                                        <li
                                            class="{{ Request::route()->getName() == 'chartofaccount.index' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('chartofaccount.index') }}">{{ __('Chart of Accounts') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage account transaction')
                                        <li
                                            class="{{ Request::route()->getName() == 'account_transaction.index' || Request::route()->getName() == 'account_transaction.search' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('account_transaction.index') }}">{{ __('Account Transactions') }}</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endif

                        @if (Gate::check('view sales journal') ||
                                Gate::check('view sales return journal') ||
                                Gate::check('view purchase journal') ||
                                Gate::check('view cash book') ||
                                Gate::check('view bank book') ||
                                Gate::check('view petty cash book') ||
                                Gate::check('view general journal'))
                            <li
                                class="{{ Request::route()->getName() == 'sales_journal.index' ||
                                Request::route()->getName() == 'sales_journal.search' ||
                                Request::route()->getName() == 'sales_return_journal.index' ||
                                Request::route()->getName() == 'sales_return_journal.search' ||
                                Request::route()->getName() == 'purchase_journal.index' ||
                                Request::route()->getName() == 'purchase_journal.search' ||
                                Request::route()->getName() == 'purchase_return_journal.index' ||
                                Request::route()->getName() == 'cash_book.index' ||
                                Request::route()->getName() == 'cash_book.search' ||
                                Request::route()->getName() == 'daily_cash_book.index' ||
                                Request::route()->getName() == 'daily_cash_book.search' ||
                                Request::route()->getName() == 'bank_book.index' ||
                                Request::route()->getName() == 'bank_book.search' ||
                                Request::route()->getName() == 'petty_cash_book.index' ||
                                Request::route()->getName() == 'general_journal.index' ||
                                Request::route()->getName() == 'petty_cash_book.search'
                                    ? ' active'
                                    : '' }}">
                                <a href="#" class="nav-link has-dropdown"
                                    data-toggle="dropdown"><span>{{ __('Prime Entry Books') }}</span></a>
                                <ul
                                    class="dropdown-menu {{ Request::route()->getName() == 'sales_journal.index' ||
                                    Request::route()->getName() == 'sales_journal.search' ||
                                    Request::route()->getName() == 'sales_return_journal.index' ||
                                    Request::route()->getName() == 'sales_return_journal.search' ||
                                    Request::route()->getName() == 'purchase_journal.index' ||
                                    Request::route()->getName() == 'purchase_journal.search' ||
                                    Request::route()->getName() == 'purchase_return_journal.index' ||
                                    Request::route()->getName() == 'cash_book.index' ||
                                    Request::route()->getName() == 'cash_book.search' ||
                                    Request::route()->getName() == 'daily_cash_book.index' ||
                                    Request::route()->getName() == 'daily_cash_book.search' ||
                                    Request::route()->getName() == 'bank_book.index' ||
                                    Request::route()->getName() == 'bank_book.search' ||
                                    Request::route()->getName() == 'petty_cash_book.index' ||
                                    Request::route()->getName() == 'general_journal.index' ||
                                    Request::route()->getName() == 'petty_cash_book.search'
                                        ? 'display:block'
                                        : '' }}">
                                    {{-- @can('view sales journal')
                                        <li class="{{ (Request::route()->getName() == 'sales_journal.index' || Request::route()->getName() == 'sales_journal.search') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{ route('sales_journal.index') }}">{{  __('Sales Journal') }}</a>
                                        </li>
                                    @endcan
                                    @can('view sales return journal')
                                        <li class="{{ (Request::route()->getName() == 'sales_return_journal.index' || Request::route()->getName() == 'sales_return_journal.search') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{ route('sales_return_journal.index') }}">{{  __('Sales Return Journal') }}</a>
                                        </li>
                                    @endcan
                                    @can('view purchase journal')
                                        <li class="{{ (Request::route()->getName() == 'purchase_journal.index' || Request::route()->getName() == 'purchase_journal.search') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{ route('purchase_journal.index') }}">{{  __('Purchase Journal') }}</a>
                                        </li>
                                    @endcan --}}
                                    @can('view cash book')
                                        <li
                                            class="{{ Request::route()->getName() == 'daily_cash_book.index' || Request::route()->getName() == 'daily_cash_book.search' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('daily_cash_book.index') }}">{{ __('Daily Cash Book') }}</a>
                                        </li>
                                    @endcan
                                    @can('view cash book')
                                        <li
                                            class="{{ Request::route()->getName() == 'cash_book.index' || Request::route()->getName() == 'cash_book.search' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('cash_book.index') }}">{{ __('Cash Book') }}</a>
                                        </li>
                                    @endcan
                                    {{-- @can('view bank book')
                                        <li class="{{ (Request::route()->getName() == 'bank_book.index' || Request::route()->getName() == 'bank_book.search') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{ route('bank_book.index') }}">{{  __('Bank Book') }}</a>
                                        </li>
                                    @endcan --}}
                                    @can('view petty cash book')
                                        <li
                                            class="{{ Request::route()->getName() == 'petty_cash_book.index' || Request::route()->getName() == 'petty_cash_book.search' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('petty_cash_book.index') }}">{{ __('Petty Cash Book') }}</a>
                                        </li>
                                    @endcan
                                    @if (Gate::check('view general journal') || Gate::check('create stock adjustment'))
                                        <li
                                            class="{{ Request::route()->getName() == 'general_journal.index' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('general_journal.index') }}">{{ __('General Journal') }}</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (Gate::check('view ledger account') || Gate::check('view control account'))
                            <li
                                class="{{ Request::route()->getName() == 'ledger_account.index' ||
                                Request::route()->getName() == 'ledger_account.search' ||
                                Request::route()->getName() == 'control_account.index' ||
                                Request::route()->getName() == 'control_account.search'
                                    ? ' active'
                                    : '' }}">
                                <a href="#" class="nav-link has-dropdown"
                                    data-toggle="dropdown"><span>{{ __('Account Summary') }}</span></a>
                                <ul
                                    class="dropdown-menu {{ Request::route()->getName() == 'ledger_account.index' ||
                                    Request::route()->getName() == 'ledger_account.search' ||
                                    Request::route()->getName() == 'control_account.index' ||
                                    Request::route()->getName() == 'control_account.search'
                                        ? 'display:block'
                                        : '' }}">
                                    @can('view ledger account')
                                        <li
                                            class="{{ Request::route()->getName() == 'ledger_account.index' || Request::route()->getName() == 'ledger_account.search' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('ledger_account.index') }}">{{ __('Ledger Accounts') }}</a>
                                        </li>
                                    @endcan

                                    @can('view control account')
                                        <li
                                            class="{{ Request::route()->getName() == 'control_account.index' || Request::route()->getName() == 'control_account.search' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('control_account.index') }}">{{ __('Control Accounts') }}</a>
                                        </li>
                                    @endcan


                                </ul>
                            </li>
                        @endif

                        @if (Gate::check('manage bank rec') || Gate::check('view bank rec'))
                            <li
                                class="{{ Request::route()->getName() == 'bank_rec.index' || Request::route()->getName() == 'bank_rec.entries_search' || Request::route()->getName() == 'bank_rec.statement' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('bank_rec.index') }}">
                                    <span>{{ __('Bank Reconciliation') }}</span></a>
                            </li>
                        @endif

                        {{-- @if (Gate::check('manage bank rec') || Gate::check('view bank rec'))
                            <li class="{{ (Request::route()->getName() == 'bank_rec.index' || Request::route()->getName() == 'bank_rec.search' || Request::route()->getName() == 'bank_rec.entries' || Request::route()->getName() == 'bank_rec.entries_search' || Request::route()->getName() == 'bank_rec.cleared_entries' || Request::route()->getName() == 'bank_rec.cleared_entries_search'
                                            || Request::route()->getName() == 'bank_rec.uncleared_entries' || Request::route()->getName() == 'bank_rec.uncleared_entries_search') ? ' active' : '' }}">
                                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><span>{{__('Bank Reconciliation')}}</span></a>
                                <ul class="dropdown-menu {{ (Request::route()->getName() == 'bank_rec.index' || Request::route()->getName() == 'bank_rec.search' || Request::route()->getName() == 'bank_rec.entries' || Request::route()->getName() == 'bank_rec.entries_search' || Request::route()->getName() == 'bank_rec.cleared_entries' || Request::route()->getName() == 'bank_rec.cleared_entries_search'
                                                                || Request::route()->getName() == 'bank_rec.uncleared_entries' || Request::route()->getName() == 'bank_rec.uncleared_entries_search')?'display:block':''}}">
                                    @can('manage bank rec')
                                        <li class="{{ (Request::route()->getName() == 'bank_rec.entries' || Request::route()->getName() == 'bank_rec.entries_search') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{ route('bank_rec.entries') }}" style="line-height: 17px">{{  __('All Entries') }}</a>
                                        </li>
                                    @endcan

                                    @can('view bank rec')
                                        <li class="{{ (Request::route()->getName() == 'bank_rec.cleared_entries' || Request::route()->getName() == 'bank_rec.cleared_entries_search') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{ route('bank_rec.cleared_entries') }}" style="line-height: 17px">{{  __('Cleared Entries') }}</a>
                                        </li>
                                    @endcan

                                    @can('view bank rec')
                                        <li class="{{ (Request::route()->getName() == 'bank_rec.uncleared_entries' || Request::route()->getName() == 'bank_rec.uncleared_entries_search') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{ route('bank_rec.uncleared_entries') }}" style="line-height: 17px">{{  __('Uncleared Entries') }}</a>
                                        </li>
                                    @endcan

                                    @can('view bank rec')
                                        <li class="{{ (Request::route()->getName() == 'bank_rec.index' || Request::route()->getName() == 'bank_rec.search') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{ route('bank_rec.index') }}" style="line-height: 17px">{{  __('Reconciliation Statement') }}</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endif --}}

                        {{-- @if (Gate::check('manage debit note') || Gate::check('manage credit note'))
                            <li class="{{ (Request::route()->getName() == 'debit.note' || Request::route()->getName() == 'credit.note') ? ' active' : '' }}">
                                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><span>{{__('Source Documents')}}</span></a>
                                <ul class="dropdown-menu {{ (Request::route()->getName() == 'debit.note' || Request::route()->getName() == 'credit.note')?'display:block':''}}">
                                    @can('manage debit note')
                                        <li class="{{ (Request::route()->getName() == 'debit.note') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{ route('debit.note') }}">{{  __('Debit Note') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage credit note')
                                        <li class="{{ (Request::route()->getName() == 'credit.note') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{ route('credit.note') }}">{{  __('Credit Note') }}</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endif --}}

                        @if (Gate::check('view loss & profit') || Gate::check('view balance sheet') || Gate::check('view trial balance'))
                            <li
                                class="{{ Request::route()->getName() == 'profit_loss_summary.index' ||
                                Request::route()->getName() == 'profit_loss_summary.search' ||
                                Request::route()->getName() == 'balance_sheet_summary.index' ||
                                Request::route()->getName() == 'balance_sheet_summary.search' ||
                                Request::route()->getName() == 'trial_balance.index' ||
                                Request::route()->getName() == 'trial_balance.search'
                                    ? ' active'
                                    : '' }}">
                                <a href="#" class="nav-link has-dropdown"
                                    data-toggle="dropdown"><span>{{ __('Financial Statements') }}</span></a>
                                <ul
                                    class="dropdown-menu {{ Request::route()->getName() == 'profit_loss_summary.index' ||
                                    Request::route()->getName() == 'profit_loss_summary.search' ||
                                    Request::route()->getName() == 'balance_sheet_summary.index' ||
                                    Request::route()->getName() == 'balance_sheet_summary.search' ||
                                    Request::route()->getName() == 'trial_balance.index' ||
                                    Request::route()->getName() == 'trial_balance.search'
                                        ? 'display:block'
                                        : '' }}">
                                    @can('view loss & profit')
                                        <li
                                            class="{{ Request::route()->getName() == 'profit_loss_summary.index' || Request::route()->getName() == 'profit_loss_summary.search' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('profit_loss_summary.index') }}">{{ __('Profit & Loss Statement') }}</a>
                                        </li>
                                    @endcan
                                    @can('view balance sheet')
                                        <li
                                            class="{{ Request::route()->getName() == 'balance_sheet_summary.index' || Request::route()->getName() == 'balance_sheet_summary.search' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('balance_sheet_summary.index') }}">{{ __('Balance Sheet') }}</a>
                                        </li>
                                    @endcan
                                    @can('view trial balance')
                                        <li
                                            class="{{ Request::route()->getName() == 'trial_balance.index' || Request::route()->getName() == 'trial_balance.search' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('trial_balance.index') }}">{{ __('Trial Balance') }}</a>
                                        </li>
                                    @endcan
                                    {{-- @can('loss & profit report') --}}
                                    {{-- <li class="{{ (Request::route()->getName() == 'changes_in_equity_summary.index') ? ' active' : '' }}">
                                        <a class="nav-link" href="{{ route('changes_in_equity_summary.index') }}" style="line-height: 17px">{{  __('Equity Statement') }}</a>
                                    </li> --}}
                                    {{-- @endcan --}}
                                    {{-- @can('loss & profit report') --}}
                                    {{-- <li class="{{ (Request::route()->getName() == 'cash_flow_summary.index') ? ' active' : '' }}">
                                        <a class="nav-link" href="{{ route('cash_flow_summary.index') }}">{{  __('Cash Flow Statement') }}</a>
                                    </li> --}}
                                    {{-- @endcan --}}
                                </ul>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif

            {{-- masters --}}
            @if (Gate::check('manage customer') ||
                    Gate::check('manage vender') ||
                    Gate::check('manage product') ||
                    Gate::check('manage pawning jewellery') ||
                    Gate::check('manage cheque book'))
                <li
                    class="dropdown {{ Request::segment(1) == 'customer' || Request::segment(1) == 'vender' || Request::segment(1) == 'product-brand' || Request::segment(1) == 'product-category' || Request::segment(1) == 'product-sub-category' || Request::segment(1) == 'product-warranty' || Request::segment(1) == 'product' || Request::segment(1) == 'pawningJewellery' || Request::segment(1) == 'cheque_book' || Request::segment(1) == 'holidays' ? ' active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-th"></i>
                        <span>{{ __('Masters') }}</span></a>
                    <ul
                        class="dropdown-menu {{ Request::segment(1) == 'customer' || Request::segment(1) == 'vender' || Request::segment(1) == 'product-brand' || Request::segment(1) == 'product-category' || Request::segment(1) == 'product-sub-category' || Request::segment(1) == 'product-warranty' || Request::segment(1) == 'product' || Request::segment(1) == 'pawningJewellery' || Request::segment(1) == 'cheque_book' || Request::segment(1) == 'holidays' ? 'display:block' : '' }}">
                        @can('manage customer')
                            <li class="{{ Request::route()->getName() == 'customer.index' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('customer.index') }}">{{ __('Customers') }}</a>
                            </li>
                        @endcan
                        @can('manage vender')
                            <li class="{{ Request::route()->getName() == 'vender.index' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('vender.index') }}">{{ __('Suppliers') }}</a>
                            </li>
                        @endcan
                        @if (Gate::check('manage product'))
                            <li
                                class="{{ Request::segment(1) == 'product-brand' || Request::segment(1) == 'product-category' || Request::segment(1) == 'product-sub-category' || Request::segment(1) == 'product-warranty' || Request::segment(1) == 'product' ? ' active' : '' }}">
                                <a href="#" class="nav-link has-dropdown"
                                    data-toggle="dropdown"><span>{{ __('Products') }}</span></a>
                                <ul
                                    class="dropdown-menu {{ Request::segment(1) == 'product-brand' || Request::segment(1) == 'product-category' || Request::segment(1) == 'product-sub-category' || Request::segment(1) == 'product-warranty' || Request::segment(1) == 'product' ? 'display:block' : '' }}">
                                    @can('manage product')
                                        <li class="{{ Request::segment(1) == 'product-brand' ? 'active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('product-brand.index') }}">{{ __('Models') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage product')
                                        <li class="{{ Request::segment(1) == 'product-category' ? 'active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('product-category.index') }}">{{ __('Categories') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage product')
                                        <li
                                            class="{{ Request::segment(1) == 'product-sub-category' ? 'active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('product-sub-category.index') }}">{{ __('Sub Categories') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage product')
                                        <li class="{{ Request::segment(1) == 'product-warranty' ? 'active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('product-warranty.index') }}">{{ __('Warranty Cards') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage product')
                                        <li class="{{ Request::segment(1) == 'product' ? ' active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('product.index') }}">{{ __('Items') }}</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endif
                        {{-- @can('manage pawning jewellery')
                            <li class="{{ (Request::route()->getName() == 'pawningJewellery.index') ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('pawningJewellery.index')}}">{{__('Pawning Jewellery')}}</a>
                            </li>
                        @endcan --}}
                        @can('manage cheque book')
                            <li class="{{ Request::route()->getName() == 'cheque_book.index' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('cheque_book.index') }}">{{ __('Cheque Books') }}</a>
                            </li>
                        @endcan
                        {{-- @can('manage customer')
                            <li class="{{ (Request::route()->getName() == 'holidays') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('holidays') }}">{{  __('Holidays') }}</a>
                            </li>
                        @endcan --}}
                    </ul>
                </li>
            @endif

            @if (Gate::check('manage branch') ||
                    Gate::check('manage center') ||
                    Gate::check('manage business category') ||
                    Gate::check('manage service') ||
                    Gate::check('manage user') ||
                    Gate::check('manage role') ||
                    Gate::check('manage time period') ||
                    Gate::check('manage rate') ||
                    Gate::check('manage company setting') ||
                    Gate::check('manage opening cash'))
                <li
                    class="dropdown {{ Request::segment(1) == 'branch' ||
                    Request::segment(1) == 'center' ||
                    Request::segment(1) == 'business_category' ||
                    Request::segment(1) == 'service' ||
                    Request::segment(1) == 'service-provider' ||
                    Request::route()->getName() == 'service-provider.index' ||
                    Request::segment(1) == 'fine_control' ||
                    Request::route()->getName() == 'fine_control.index' ||
                    Request::segment(1) == 'card_payment_rate' ||
                    Request::route()->getName() == 'card_payment_rate.index' ||
                    Request::segment(1) == 'fine_rate' ||
                    Request::segment(1) == 'users' ||
                    Request::segment(1) == 'roles' ||
                    Request::segment(1) == 'interest-rate' ||
                    Request::segment(1) == 'goldRate' ||
                    Request::segment(1) == 'document_charge_rate' ||
                    Request::route()->getName() == 'fine_rate.index' ||
                    Request::segment(1) == 'time-period' ||
                    Request::segment(1) == 'company-setting' ||
                    Request::segment(1) == 'petty_cash_fund' ||
                    Request::route()->getName() == 'petty_cash_fund.create_fund' ||
                    Request::route()->getName() == 'loan_package.index' ||
                    Request::route()->getName() == 'opening_cash.index'
                        ? ' active'
                        : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-sliders-h"></i> <span>{{ __('Company Settings') }}</span></a>
                    <ul
                        class="dropdown-menu {{ Request::segment(1) == 'branch' ||
                        Request::segment(1) == 'center' ||
                        Request::segment(1) == 'business_category' ||
                        Request::segment(1) == 'service' ||
                        Request::segment(1) == 'service-provider' ||
                        Request::route()->getName() == 'service-provider.index' ||
                        Request::segment(1) == 'fine_control' ||
                        Request::route()->getName() == 'fine_control.index' ||
                        Request::segment(1) == 'card_payment_rate' ||
                        Request::route()->getName() == 'card_payment_rate.index' ||
                        Request::segment(1) == 'fine_rate' ||
                        Request::segment(1) == 'users' ||
                        Request::segment(1) == 'roles' ||
                        Request::segment(1) == 'interest-rate' ||
                        Request::segment(1) == 'goldRate' ||
                        Request::segment(1) == 'document_charge_rate' ||
                        Request::route()->getName() == 'fine_rate.index' ||
                        Request::segment(1) == 'time-period' ||
                        Request::segment(1) == 'company-setting' ||
                        Request::segment(1) == 'petty_cash_fund' ||
                        Request::route()->getName() == 'petty_cash_fund.create_fund' ||
                        Request::route()->getName() == 'loan_package.index' ||
                        Request::route()->getName() == 'opening_cash.index'
                            ? 'display:block'
                            : '' }}">
                        @can('manage branch')
                            <li class="{{ Request::segment(1) == 'branch' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('branch.index') }}">{{ __('Branches') }}</a>
                            </li>
                        @endcan

                        {{-- @can('manage center')
                            <li class="{{ Request::route()->getName() == 'center.index' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('center.index') }}">{{ __('Centers') }}</a>
                            </li>
                        @endcan --}}

                        {{-- @can('manage loan package')
                            <li class="{{ (Request::route()->getName() == 'loan_package.index') ? ' active' : '' }}">
                                <a class="nav-link" href="{{route('loan_package.index')}}">{{__('Loan Packages')}}</a>
                            </li>
                        @endcan --}}

                        @can('manage business category')
                            <li
                                class="{{ Request::route()->getName() == 'business_category.index' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('business_category.index') }}">{{ __('Business Categories') }}</a>
                            </li>
                        @endcan

                        @can('manage petty cash fund')
                            <li
                                class="{{ Request::route()->getName() == 'petty_cash_fund.create_fund' || Request::segment(1) == 'petty_cash_fund' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('petty_cash_fund.create_fund') }}">{{ __('Petty Cash Float') }}</a>
                            </li>
                        @endcan

                        @can('manage penalty')
                            <li class="{{ Request::route()->getName() == 'fine_control.index' || Request::segment(1) == 'fine_control' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('fine_control.index') }}">{{ __('Penalty Control') }}</a>
                            </li>
                        @endcan

                        {{-- @can('manage service')
                            <li class="{{ Request::route()->getName() == 'service-provider.index' || Request::segment(1) == 'service-provider' ? ' active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('service-provider.index') }}">{{ __('Services') }}</a>
                            </li>
                        @endcan --}}


                        @can('manage user')
                            <li
                                class="{{ Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users.create' || Request::route()->getName() == 'users.edit' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('users.index') }}">{{ __('Users') }}</a>
                            </li>
                        @endcan

                        @can('manage role')
                            <li
                                class="{{ Request::route()->getName() == 'roles.index' || Request::route()->getName() == 'roles.create' || Request::route()->getName() == 'roles.edit' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('roles.index') }}">{{ __('Roles') }}</a>
                            </li>
                        @endcan

                        @if (Gate::check('manage time period') || Gate::check('manage rate'))
                            <li
                                class="{{ Request::segment(1) == 'time-period' || Request::segment(1) == 'interest-rate' || Request::segment(1) == 'goldRate' || Request::segment(1) == 'document_charge_rate' || Request::segment(1) == 'fine_rate' || Request::segment(1) == 'card_payment_rate' || Request::route()->getName() == 'fine_rate.index' || Request::route()->getName() == 'card_payment_rate.index' ? ' active' : '' }}">
                                <a href="#" class="nav-link has-dropdown"
                                    data-toggle="dropdown"><span>{{ __('Rates & Tariffs') }}</span></a>
                                <ul
                                    class="dropdown-menu {{ Request::segment(1) == 'time-period' || Request::segment(1) == 'interest-rate' || Request::segment(1) == 'goldRate' || Request::segment(1) == 'document_charge_rate' || Request::segment(1) == 'fine_rate' || Request::segment(1) == 'card_payment_rate' || Request::segment(1) == 'systems' || Request::route()->getName() == 'fine_rate.index' || Request::route()->getName() == 'card_payment_rate.index' ? 'display:block' : '' }}">

                                    @can('manage rate')
                                        <li
                                            class="{{ Request::route()->getName() == 'interest-rate.index' ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ route('interest-rate.index') }}">
                                                {{ __('Interest Rates') }}</a>
                                        </li>
                                    @endcan

                                    {{-- @can('manage rate')
                                        <li class="{{ (Request::route()->getName() == 'goldRate.index' ) ? 'active' : '' }}">
                                            <a class="nav-link" href="{{route('goldRate.index')}}"> {{__('Gold Rates')}}</a>
                                        </li>
                                    @endcan --}}

                                    @can('manage rate')
                                        <li
                                            class="{{ Request::route()->getName() == 'document_charge_rate.index' ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ route('document_charge_rate.index') }}">
                                                {{ __('Document Charge Rates') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage rate')
                                        <li
                                            class="{{ Request::route()->getName() == 'fine_rate.index' ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ route('fine_rate.index') }}">
                                                {{ __('Fine Rates') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage rate')
                                        <li
                                            class="{{ Request::route()->getName() == 'card_payment_rate.index' || Request::segment(1) == 'card_payment_rate' ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ route('card_payment_rate.index') }}">
                                                {{ __('Card Payment Rate') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage time period')
                                        <li
                                            class="{{ Request::route()->getName() == 'time-period.index' ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ route('time-period.index') }}">
                                                {{ __('Time Periods') }}</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endif

                        @can('manage company settings')
                            <li class="{{ Request::segment(1) == 'company-setting' ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('company.setting') }}">
                                    {{ __('Company Info') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endif

            {{-- asset --}}
            @if (Gate::check('manage assets'))
                <li class="{{ Request::segment(1) == 'asset' ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('asset.index') }}">
                        <i class="fas fa-building"></i> <span>{{ __('Fixed Assets') }}</span></a>
                </li>
            @endif

            {{-- receipt --}}
            @if (Gate::check('manage receipt'))
                <li class="{{ Request::segment(1) == 'custom_receipt' ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('custom_receipt.index') }}">
                        <i class="fas fa-receipt"></i> <span>{{ __('Receipts') }}</span></a>
                </li>
            @endif

        </ul>
    </aside>
</div>

@if (Gate::check('manage authorization'))
    <script>
        $(document).ready(function() {
            countCal();
            setInterval(function() {
                countCal();
            }, 5000)
        });

        function countCal() {
            $.ajax({
                url: "{{ url('authorization_requests/get_count') }}",
                method: 'GET',
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    if (data > 0) {
                        if (data < 10) {
                            $('#authorization_request_count').html('0' + data);
                        } else if (data >= 10) {
                            $('#authorization_request_count').html(data);
                        }
                        $('#authorization_requests').addClass('alert');
                    } else {
                        $('#authorization_requests').removeClass('alert');
                        $('#authorization_request_count').html('00');
                    }

                }
            });
        }
    </script>
@endif
