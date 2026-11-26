@php
$logo=asset(Storage::url('uploads/logo/'));
$company_logo=Utility::getValByName('company_logo');
$company_small_logo=Utility::getValByName('company_small_logo');
@endphp
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper" class=" bg-dark">
        <div class="sidebar-brand">
            <a href="{{route('dashboard')}}">
                <img class="img-fluid" src="{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo.png')}}" alt="">
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{route('dashboard')}}">
                <img class="img-fluid" src="{{$logo.'/'.(isset($company_small_logo) && !empty($company_small_logo)?$company_small_logo:'small_logo.png')}}" alt="">
            </a>
        </div>
        <ul class="sidebar-menu">


            {{-- dashboard --}}
            {{-- @if(\Auth::guard('customer')->check()) --}}
            {{-- <li class="dropdown {{ (Request::route()->getName() == 'customer.dashboard') ? ' active' : '' }} ">
            <a class="nav-link" href="{{route('customer.dashboard')}}"> <i class="fas fa-tachometer-alt"></i> <span>{{__('Dashboard')}}</span></a>
            </li> --}}
            {{-- @elseif(\Auth::guard('vender')->check()) --}}
            {{-- <li class="dropdown {{ (Request::route()->getName() == 'vender.dashboard') ? ' active' : '' }} ">
            <a class="nav-link" href="{{route('vender.dashboard')}}"> <i class="fas fa-tachometer-alt"></i> <span>{{__('Dashboard')}}</span></a>
            </li> --}}
            {{-- @else --}}
            <li class="dropdown {{ (Request::route()->getName() == 'dashboard') ? ' active' : '' }} ">
                <a class="nav-link" href="{{route('dashboard')}}"> <i class="fas fa-tachometer-alt"></i> <span>{{__('Dashboard')}}</span></a>
            </li>
            {{-- @endif --}}


            {{-- sale --}}
            @if( Gate::check('manage sale') )
                <li class="dropdown {{ (Request::segment(1) == 'sale' || Request::segment(1) == 'hire_purchase_contract' || Request::segment(1) == 'advance_payement')?' active':''}}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="far fa-credit-card"></i><span>{{__('Cash & Hire Sales')}}</span></a>
                    <ul class="dropdown-menu {{ (Request::segment(1) == 'sale' || Request::segment(1) == 'hire_purchase_contract'|| Request::segment(1) == 'advance_payement')?'display:block':''}}">
                        @can('manage sale')
                            <li class="{{ (Request::segment(1) == 'sale')?' active':''}}">
                                <a class="nav-link" href="{{ route('sale.index') }}">{{ __('Cash & Hire Sales') }}</a>
                            </li>
                        @endcan
                        @can('manage sale')
                            <li class="{{ (Request::segment(1) == 'hire_purchase_contract')?' active':''}}">
                                <a class="nav-link" href="{{ route('hire_purchase_contract.index') }}">{{ __('Hire Sales Contracts') }}</a>
                            </li>
                        @endcan
                        @can('manage advances')
                            <li class="{{ (Request::segment(1) == 'advance_payement')?' active':''}}">
                                <a class="nav-link" href="{{ route('advance_payement.index') }}">{{ __('Advance Payements') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif



            {{-- loans --}}
            @if( Gate::check('manage loan')  ||  Gate::check('manage group') )
                <li class="dropdown {{ (Request::segment(1) == 'sme_loan' || Request::segment(1) == 'group' || Request::segment(1) == 'group_loan' || Request::segment(1) == 'vehicle_loan' || Request::segment(1) == 'land_loan' || Request::segment(1) == 'job_loan' || Request::route()->getName() == 'loan.view')?' active':''}}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-hand-holding-usd"></i> <span>{{__('Loan')}}</span></a>
                    <ul class="dropdown-menu {{ (Request::segment(1) == 'sme_loan' || Request::segment(1) == 'group' || Request::segment(1) == 'group_loan' || Request::segment(1) == 'vehicle_loan' || Request::segment(1) == 'land_loan' || Request::segment(1) == 'job_loan' || Request::route()->getName() == 'loan.view')?'active':''}}">

                        @can('manage loan')
                            <li class="{{ (Request::segment(1) == 'sme_loan') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('sme_loan') }}">
                                    <span>{{__('SME Loan')}}</span></a>
                            </li>
                        @endcan

                        <li class="{{ (Request::segment(1) == 'group' || Request::segment(1) == 'group_loan') ? ' active' : '' }}">
                            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><span>{{__('Group Loan')}}</span></a>
                            <ul class="dropdown-menu {{ (Request::segment(1) == 'group' || Request::segment(1) == 'group_loan')?'display:block':''}}">
                                @can('manage group')
                                    <li class="{{ (Request::segment(1) == 'group') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{route('group.index')}}"> {{__('Group')}}</a>
                                    </li>
                                @endcan

                                @can('manage loan')
                                    <li class="{{ (Request::segment(1) == 'group_loan') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{route('group_loan')}}"> {{__('Group Loan')}}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>

                        @can('manage loan')
                            <li class="{{ (Request::segment(1) == 'vehicle_loan') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('vehicle_loan') }}">
                                    <span>{{__('Vehicle Loan')}}</span></a>
                            </li>
                        @endcan

                        @can('manage loan')
                            <li class="{{ (Request::segment(1) == 'land_loan') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('land_loan') }}">
                                    <span>{{__('Land Loan')}}</span></a>
                            </li>
                        @endcan

                        @can('manage loan')
                            <li class="{{ (Request::segment(1) == 'job_loan') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('job_loan') }}">
                                    <span>{{__('Job Loan')}}</span></a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif



            {{-- pawning --}}
            @if(Gate::check('manage pawning'))
                <li class="dropdown {{ (Request::segment(1) == 'pawning' || Request::segment(1) == 'overdue-pawning')?' active':''}}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-coins"></i> <span>{{__('Pawning')}}</span></a>
                    <ul class="dropdown-menu {{ (Request::segment(1) == 'pawning' || Request::segment(1) == 'overdue-pawning')?'display:block':''}}">
                        @can('manage pawning')
                            <li class="{{ (Request::route()->getName() == 'pawning.index' || Request::route()->getName() == 'bank-account.create' || Request::route()->getName() == 'bank-account.edit') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('pawning.index') }}">{{ __('Pawning') }}</a>
                            </li>
                        @endcan
                        @can('manage pawning')
                            <li class="{{ (Request::route()->getName() == 'overdue-pawning.index' || Request::route()->getName() == 'bank-transfer.create' || Request::route()->getName() == 'bank-transfer.edit') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('overdue-pawning.index') }}">{{ __('Overdue Pawning') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endif

            {{-- inventory --}}
            @if( Gate::check('manage stock')  ||  Gate::check('manage purchase order') ||  Gate::check('manage grn'))
                <li class="dropdown {{ (Request::segment(1) == 'stock' || Request::segment(1) == 'purchase-order' || Request::segment(1) == 'grn' || Request::route()->getName() == 'stock_transfers.transfers')?' active':''}}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-dolly-flatbed"></i> <span>{{__('Inventory')}}</span></a>
                    <ul class="dropdown-menu {{ (Request::segment(1) == 'stock' || Request::segment(1) == 'purchase-order' || Request::segment(1) == 'grn' || Request::route()->getName() == 'stock_transfers.transfers')?'display:block':''}}">
                        @can('manage stock')
                            <li class="{{ (Request::route()->getName() == 'stock.index' || Request::route()->getName() == 'stock_transfers.transfers') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('stock.index') }}">{{ __('Stock') }}</a>
                            </li>
                        @endcan

                        @can('manage purchase order')
                            <li class="{{ (Request::route()->getName() == 'purchase-order.index') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('purchase-order.index') }}">{{ __('Purchase Order') }}</a>
                            </li>
                        @endcan

                        @can('manage grn')
                            <li class="{{ (Request::route()->getName() == 'grn.index') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('grn.index') }}">{{  __('GRN') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif


            {{-- banking --}}
            @if( Gate::check('manage bank account') ||  Gate::check('manage transfer'))
                <li class="dropdown {{ (Request::segment(1) == 'bank-account' || Request::segment(1) == 'bank-transfer')?' active':''}}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-landmark"></i> <span>{{__('Banking')}}</span></a>
                    <ul class="dropdown-menu {{ (Request::segment(1) == 'bank-account' || Request::segment(1) == 'bank-transfer')?'display:block':''}}">
                        @can('manage bank account')
                            <li class="{{ (Request::route()->getName() == 'bank-account.index' || Request::route()->getName() == 'bank-account.create' || Request::route()->getName() == 'bank-account.edit') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('bank-account.index') }}">{{ __('Account') }}</a>
                            </li>
                        @endcan
                        @can('manage transfer')
                            <li class="{{ (Request::route()->getName() == 'bank-transfer.index' || Request::route()->getName() == 'bank-transfer.create' || Request::route()->getName() == 'bank-transfer.edit') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('bank-transfer.index') }}">{{ __('Transfer') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endif


            {{-- asset --}}
            @if(Gate::check('manage assets'))
                <li class="{{ (Request::segment(1) == 'asset') ?' active':''}}">
                    <a class="nav-link" href="{{ route('asset.index') }}">
                        <i class="fas fa-calculator"></i> <span>{{__('Asset Manager')}}</span></a>
                </li>
            @endif


            {{-- income --}}
            @if( Gate::check('manage invoice') ||  Gate::check('manage revenue'))
                <li class="dropdown {{ (Request::segment(1) == 'invoice' || Request::segment(1) == 'revenue')?' active':''}}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-money-bill"></i> <span>{{__('Income')}}</span></a>
                    <ul class="dropdown-menu {{ (Request::segment(1) == 'invoice' || Request::segment(1) == 'revenue')?'display:block':''}}">
                        @can('manage invoice')
                            <li class="{{ (Request::route()->getName() == 'invoice.index' || Request::route()->getName() == 'invoice.create' || Request::route()->getName() == 'invoice.edit' || Request::route()->getName() == 'invoice.show') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('invoice.index') }}">{{ __('Invoice') }}</a>
                            </li>
                        @endcan
                        @can('manage revenue')
                            <li class="{{ (Request::route()->getName() == 'revenue.index' || Request::route()->getName() == 'revenue.create' || Request::route()->getName() == 'revenue.edit') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('revenue.index') }}">{{ __('Revenue') }}</a>
                            </li>
                        @endcan
                        {{-- @can('manage credit note')
                                <li class="{{ (Request::route()->getName() == 'credit.note' ) ? ' active' : '' }}">
                                    <a class="nav-link" href="{{ route('credit.note') }}">{{ __('Credit Note') }}</a>
                            </li>
                            @endcan --}}
                    </ul>
                </li>
            @endif


            {{-- expense --}}
            @if( Gate::check('manage bill'))
                <li class="dropdown {{ (Request::segment(1) == 'serviceBill' || Request::segment(1) == 'petty-cash' || Request::segment(1) == 'cheque-print' || Request::segment(1) == 'cheque_book') ?' active':''}}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-money-bill-wave"></i> <span>{{__('Payables')}}</span></a>
                    <ul class="dropdown-menu {{ (Request::segment(1) == 'serviceBill' || Request::segment(1) == 'petty-cash' || Request::segment(1) == 'cheque-print' || Request::segment(1) == 'cheque_book')?'display:block':''}}">
                        @can('manage bill')
                            <li class="{{ (Request::route()->getName() == 'serviceBill.index' || Request::route()->getName() == 'serviceBill.create' || Request::route()->getName() == 'serviceBill.edit') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('serviceBill.index') }}">{{  __('Bill') }}</a>
                            </li>
                        @endcan
                        @can('manage payment')
                            <li class="{{ (Request::route()->getName() == 'petty-cash.index' || Request::route()->getName() == 'petty-cash.create' || Request::route()->getName() == 'petty-cash.edit') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('petty-cash.index') }}">{{  __('Petty Cash') }}</a>
                            </li>
                        @endcan
                        @can('manage cheque')
                            <li class="{{ (Request::route()->getName() == 'cheque-print.index') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('cheque-print.index') }}">{{  __('Cheque Print') }}</a>
                            </li>
                        @endcan
                        @can('manage cheque book')
                            <li class="{{ (Request::route()->getName() == 'cheque_book.index' || Request::route()->getName() == 'cheque_book.view') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('cheque_book.index') }}">{{  __('Cheque Books') }}</a>
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

            {{-- general ledger --}}
            {{-- @if( Gate::check('manage payment'))  --}}
            <li class="dropdown {{ (Request::segment(1) == 'statements.profit.loss.summary' || Request::segment(1) == 'statements.balance.sheet.summary' || Request::segment(1) == 'statements.cash.flow.summary' || Request::segment(1) == 'statements.bank.rec.summary' || Request::segment(1) == 'accountSection.index' || Request::segment(1) == 'accountGroup.index' || Request::segment(1) == 'chartOfAccount.index' || Request::segment(1) == 'accountTransaction.index') ?' active':''}}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-file-invoice"></i> <span>{{__('General Ledger')}}</span></a>
                <ul class="dropdown-menu {{ (Request::segment(1) == 'statements.profit.loss.summary' || Request::segment(1) == 'statements.balance.sheet.summary' || Request::segment(1) == 'statements.cash.flow.summary' || Request::segment(1) == 'statements.bank.rec.summary' || Request::segment(1) == 'accountSection.index' || Request::segment(1) == 'accountGroup.index' || Request::segment(1) == 'chartOfAccount.index' || Request::segment(1) == 'accountTransaction.index')?'display:block':''}}">

                    <li class="{{ (Request::segment(1) == 'statements.profit.loss.summary' || Request::segment(1) == 'statements.balance.sheet.summary' || Request::segment(1) == 'statements.cash.flow.summary' || Request::segment(1) == 'statements.bank.rec.summary') ? ' active' : '' }}">
                        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><span>{{__('Financial Statements')}}</span></a>
                        <ul class="dropdown-menu {{ (Request::segment(1) == 'statements.profit.loss.summary' || Request::segment(1) == 'statements.balance.sheet.summary' || Request::segment(1) == 'statements.cash-flow' || Request::segment(1) == 'statements.bank-rec')?'display:block':''}}">
                            {{-- @can('loss & profit report') --}}
                                <li class="{{ (Request::segment(1) == 'statements.profit.loss.summary') ? ' active' : '' }}">
                                    <a class="nav-link" href="{{ route('statements.profit.loss.summary') }}">{{  __('Profit & Loss Statement') }}</a>
                                </li>
                            {{-- @endcan --}}
                            {{-- @can('loss & profit report') --}}
                                <li class="{{ (Request::segment(1) == 'statements.balance.sheet.summary') ? ' active' : '' }}">
                                    <a class="nav-link" href="{{ route('statements.balance.sheet.summary') }}">{{  __('Balance Sheet') }}</a>
                                </li>
                            {{-- @endcan --}}
                            {{-- @can('loss & profit report') --}}
                                <li class="{{ (Request::segment(1) == 'statements.cash.flow.summary') ? ' active' : '' }}">
                                    <a class="nav-link" href="{{ route('statements.cash.flow.summary') }}">{{  __('Cash Flow Statement') }}</a>
                                </li>
                            {{-- @endcan
                            @can('loss & profit report') --}}
                                <li class="{{ (Request::segment(1) == 'statements.bank.rec.summary') ? ' active' : '' }}">
                                    <a class="nav-link" href="{{ route('statements.bank.rec.summary') }}">{{  __('Bank Reconciliation') }}</a>
                                </li>
                            {{-- @endcan --}}
                        </ul>
                    <li class="{{ (Request::segment(1) == 'accountSection.index' || Request::segment(1) == 'accountGroup.index' || Request::segment(1) == 'chartOfAccount.index' || Request::segment(1) == 'accountTransaction.index') ? ' active' : '' }}">
                        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><span>{{__('Maintenance')}}</span></a>
                        <ul class="dropdown-menu {{ (Request::segment(1) == 'accountSection.index' || Request::segment(1) == 'accountGroup.index' || Request::segment(1) == 'chartOfAccount.index' || Request::segment(1) == 'accountTransaction.index')?'display:block':''}}">
                            {{-- @can('manage payment') --}}
                                <li class="{{ (Request::segment(1) == 'accountSection.index') ? ' active' : '' }}">
                                    <a class="nav-link" href="{{ route('accountSection.index') }}">{{  __('Account Sections') }}</a>
                                </li>
                            {{-- @endcan
                            @can('manage payment') --}}
                                <li class="{{ (Request::segment(1) == 'accountGroup.index') ? ' active' : '' }}">
                                    <a class="nav-link" href="{{ route('accountGroup.index') }}">{{  __('Account Groups') }}</a>
                                </li>
                            {{-- @endcan
                            {{-- @can('manage payment') --}}
                                <li class="{{ (Request::segment(1) == 'chartOfAccount.index') ? ' active' : '' }}">
                                    <a class="nav-link" href="{{ route('chartOfAccount.index') }}">{{  __('Chart of Accounts') }}</a>
                                </li>
                            {{-- @endcan --}}
                            {{-- @can('manage payment') --}}
                                <li class="{{ (Request::segment(1) == 'accountTransaction.index') ? ' active' : '' }}">
                                    <a class="nav-link" href="{{ route('accountTransaction.index') }}">{{  __('Account Transactions') }}</a>
                                </li>
                            {{-- @endcan --}}
                        </ul>
                    </li>
                </ul>
            {{-- @endif --}}

            {{-- masters --}}
            @if( Gate::check('manage customer') || Gate::check('manage vender') || Gate::check('manage product'))
                <li class="dropdown {{ (Request::segment(1) == 'customer' || Request::segment(1) == 'vender' || Request::segment(1) == 'product-brand' || Request::segment(1) == 'product-category' || Request::segment(1) == 'product-sub-category' || Request::segment(1) == 'product') ?' active':''}}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-th"></i> <span>{{__('Masters')}}</span></a>
                    <ul class="dropdown-menu {{ (Request::segment(1) == 'customer' || Request::segment(1) == 'vender' ||Request::segment(1) == 'product-brand' || Request::segment(1) == 'product-category' || Request::segment(1) == 'product-sub-category' || Request::segment(1) == 'product')?'display:block':''}}">
                        @can('manage customer')
                            <li class="{{ (Request::route()->getName() == 'customer.index' || Request::route()->getName() == 'customer.show') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('customer.index') }}">{{ __('Customer') }}</a>
                            </li>
                        @endcan
                        @can('manage vender')
                            <li class="{{ (Request::route()->getName() == 'vender.index' || Request::route()->getName() == 'vender.show') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('vender.index') }}">{{ __('Supplier') }}</a>
                            </li>
                        @endcan
                        @can('manage product')
                            <li class="{{ (Request::route()->getName() == 'product-brand.index' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('product-brand.index') }}">{{ __('Model') }}</a>
                            </li>
                        @endcan
                        @can('manage product')
                            <li class="{{ (Request::route()->getName() == 'product-category.index' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('product-category.index') }}">{{ __('Category') }}</a>
                            </li>
                        @endcan
                        @can('manage product')
                            <li class="{{ (Request::route()->getName() == 'product-sub-category.index' ) ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('product-sub-category.index') }}">{{ __('Sub Category') }}</a>
                            </li>
                        @endcan
                        @can('manage product')
                            <li class="{{ (Request::route()->getName() == 'product.index' || Request::route()->getName() == 'product.show') ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('product.index') }}">{{ __('Item') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif




            {{-- accounting --}}
            {{-- @if( Gate::check('manage bill')  ||  Gate::check('manage payment') ||  Gate::check('manage debit note'))
            <li class="dropdown {{ (Request::segment(1) == 'bill' || Request::segment(1) == 'payment' || Request::segment(1) == 'debit-note'  )?' active':''}}">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-file-invoice-dollar"></i> <span>{{__('Accounting')}}</span></a>
            <ul class="dropdown-menu {{ (Request::segment(1) == 'invoice')?'display:block':''}}">
                @can('manage bill')
                <li class="{{ (Request::route()->getName() == 'bill.index' || Request::route()->getName() == 'bill.create' || Request::route()->getName() == 'bill.edit' || Request::route()->getName() == 'bill.show') ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('bill.index') }}">{{ __('Ledger') }}</a>
                </li>
                @endcan
                @can('manage bill')
                <li class="{{ (Request::route()->getName() == 'bill.index' || Request::route()->getName() == 'bill.create' || Request::route()->getName() == 'bill.edit' || Request::route()->getName() == 'bill.show') ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('bill.index') }}">{{ __('General Ledger') }}</a>
                </li>
                @endcan
                @can('manage payment')
                <li class="{{ (Request::route()->getName() == 'payment.index' || Request::route()->getName() == 'payment.create' || Request::route()->getName() == 'payment.edit') ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('payment.index') }}">{{ __('Trial Balance') }}</a>
                </li>
                @endcan
            </ul>
            </li>
            @endif --}}



        {{-- reports --}}
        {{-- @if( Gate::check('income report') || Gate::check('expense report') || Gate::check('income vs expense report') || Gate::check('tax report')  || Gate::check('loss & profit report') || Gate::check('invoice report') || Gate::check('bill report') || Gate::check('invoice report') ||  Gate::check('manage transaction')||  Gate::check('statement report')) --}}
        @if( Gate::check('manage report'))
        <li class="dropdown {{ (Request::segment(1) == 'duepaymentReport' || Request::segment(1) == 'receiptReport' || Request::segment(1) == 'ele_profit'
                                || Request::segment(1) == 'accessories_profit' || Request::segment(1) == 'collection_report' || Request::segment(1) == 'loan_report'
                                || Request::segment(1) == 'loan_installment_report' || Request::segment(1) == 'customer_report' || Request::segment(1) == 'loan_outstanding_report'
                                || Request::segment(1) == 'hire_purchase_outstanding_report' || Request::segment(1) == 'stock_transfers_report' || Request::segment(1) == 'repayment_sheet'
                                || Request::segment(1) == 'supplier_report' || Request::segment(1) == 'attendance_sheet' || Request::segment(1) == 'cash_collector_report')?' active':''}}">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-chart-area"></i> <span>{{__('Report')}}</span></a>
            <ul class="dropdown-menu {{ (Request::segment(1) == 'duepaymentReport' || Request::segment(1) == 'receiptReport')?'display:block':''}}">
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'ele_profit' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('ele_profit.index')}}">{{ __('ELE Profit') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'accessories_profit' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('accessories_profit.index')}}">{{ __('Accessories Profit') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'collection_report' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('collection_report.index')}}">{{ __('Collection Report') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'duepaymentReport' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('duepaymentReport')}}">{{ __('Due Payment') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'repayment_sheet' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('repayment_sheet.index')}}">{{ __('Repayment Sheet') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'cash_collector_report' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('cash_collector_report.index')}}">{{ __('Cash Collector Report') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'attendance_sheet' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('attendance_sheet.index')}}">{{ __('Attendance Sheet') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'hire_purchase_outstanding_report' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('hire_purchase_outstanding_report.index')}}" style="line-height: 17px">{{ __('Hire Sales Outstanding Report') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'loan_report' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('loan_report.index')}}">{{ __('Loan Report') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'loan_outstanding_report' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('loan_outstanding_report.index')}}">{{ __('Loan Outstanding Report') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'loan_installment_report' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('loan_installment_report.index')}}">{{ __('Loan Installments Report') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'stocks_report' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('stocks_report.index')}}">{{ __('Stock Report') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'stock_transfers_report' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('stock_transfers_report.index')}}">{{ __('Stock Transfers Report') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'receiptReport' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('receiptReport')}}">{{ __('Receipt Summary') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'customer_report' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('customer_report.index')}}">{{ __('Customer Report') }}</a>
                    </li>
                @endcan
                @can('show report')
                    <li class="{{ (Request::segment(1) == 'supplier_report' ) ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('supplier_report.index')}}">{{ __('Supplier Report') }}</a>
                    </li>
                @endcan

                {{--@can('manage transaction')
                             <li class="{{ (Request::route()->getName() == 'transaction.index' || Request::route()->getName() == 'transfer.create' || Request::route()->getName() == 'transaction.edit') ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('transaction.index') }}">{{ __('Transaction') }}</a>
        </li>
        @endcan
        @can('statement report')
        <li class="{{ (Request::route()->getName() == 'report.account.statement') ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('report.account.statement') }}">{{ __('Account Statement') }}</a>
        </li>
        @endcan
        @can('income report')
        <li class="{{ (Request::route()->getName() == 'report.income.summary' ) ? ' active' : '' }}">
            <a class="nav-link" href="{{route('report.income.summary')}}">{{ __('Income Summary') }}</a>
        </li>
        @endcan
        @can('expense report')
        <li class="{{ (Request::route()->getName() == 'report.expense.summary' ) ? ' active' : '' }}">
            <a class="nav-link" href="{{route('report.expense.summary')}}">{{ __('Expense Summary') }}</a>
        </li>
        @endcan
        @can('income vs expense report')
        <li class="{{ (Request::route()->getName() == 'report.income.vs.expense.summary' ) ? ' active' : '' }}">
            <a class="nav-link" href="{{route('report.income.vs.expense.summary')}}">{{ __('Income VS Expense') }}</a>
        </li>
        @endcan
        @can('tax report')
        <li class="{{ (Request::route()->getName() == 'report.tax.summary' ) ? ' active' : '' }}">
            <a class="nav-link" href="{{route('report.tax.summary')}}">{{ __('Tax Summary') }}</a>
        </li>
        @endcan
        @can('loss & profit report')
        <li class="{{ (Request::route()->getName() == 'report.profit.loss.summary' ) ? ' active' : '' }}">
            <a class="nav-link" href="{{route('report.profit.loss.summary')}}">{{ __('Profit & Loss') }}</a>
        </li>
        @endcan
        @can('invoice report')
        <li class="{{ (Request::route()->getName() == 'report.invoice.summary' ) ? ' active' : '' }}">
            <a class="nav-link" href="{{route('report.invoice.summary')}}">{{ __('Invoice Summary') }}</a>
        </li>
        @endcan
        @can('bill report')
        <li class="{{ (Request::route()->getName() == 'report.bill.summary' ) ? ' active' : '' }}">
            <a class="nav-link" href="{{route('report.bill.summary')}}">{{ __('Bill Summary') }}</a>
        </li>
        @endcan--}}

        </ul>
        </li>
        @endif



        {{-- company setting --}}

        {{-- @if( Gate::check('manage bill')  ||  Gate::check('manage payment') ||  Gate::check('manage debit note'))
            <li class="dropdown {{ (Request::segment(1) == 'bill' || Request::segment(1) == 'payment' || Request::segment(1) == 'debit-note'  )?' active':''}}">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-sliders-h"></i> <span>{{__('Company Settings')}}</span></a>
        <ul class="dropdown-menu {{ (Request::segment(1) == 'invoice')?'display:block':''}}">
            @can('manage bill')
            <li class="{{ (Request::route()->getName() == 'bill.index' || Request::route()->getName() == 'bill.create' || Request::route()->getName() == 'bill.edit' || Request::route()->getName() == 'bill.show') ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('bill.index') }}">{{ __('Ledger') }}</a>
            </li>
            @endcan
            @can('manage bill')
            <li class="{{ (Request::route()->getName() == 'bill.index' || Request::route()->getName() == 'bill.create' || Request::route()->getName() == 'bill.edit' || Request::route()->getName() == 'bill.show') ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('bill.index') }}">{{ __('General Ledger') }}</a>
            </li>
            @endcan
            @can('manage payment')
            <li class="{{ (Request::route()->getName() == 'payment.index' || Request::route()->getName() == 'payment.create' || Request::route()->getName() == 'payment.edit') ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('payment.index') }}">{{ __('Trial Balance') }}</a>
            </li>
            @endcan
        </ul>
        </li>
        @endif --}}


        {{-- @if(Gate::check('manage company settings'))
                <li class="{{ (Request::route()->getName() == 'systems.index') ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('company.setting') }}"><i class="fas fa-sliders-h"></i> <span>{{ __('Company Setting') }} </span></a>
        </li>
        @endif --}}



        {{-- @if(\Auth::user()->type=='super admin')
            @can('manage user')
                <li class="dropdown {{ (Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users.create' || Request::route()->getName() == 'users.edit') ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('users.index') }}"> <i class="fas fa-columns"></i> <span>{{__('User') }}</span></a>
        </li>
        @endcan
        @else --}}


        @if( Gate::check('manage branch') || Gate::check('manage center') || Gate::check('manage pawning jewellery') || Gate::check('manage business category') || Gate::check('manage service') ||
            Gate::check('manage user') || Gate::check('manage role') || Gate::check('manage time period') || Gate::check('manage rate') || Gate::check('manage company setting') )
        <li class="dropdown {{ (Request::segment(1) == 'branch' || Request::segment(1) == 'center' || Request::segment(1) == 'pawningJewellery' || Request::segment(1) == 'business_category' || Request::segment(1) == 'service' || Request::segment(1) == 'users' || Request::segment(1) == 'time-period' || Request::segment(1) == 'roles' || Request::segment(1) == 'permissions' || Request::segment(1) == 'systems' || Request::segment(1) == 'interest-rate' || Request::segment(1) == 'goldRate' || Request::segment(1) == 'document_charge_rate'|| Request::segment(1) == 'card_payment_rate' || Request::segment(1) == 'company-setting')?' active':''}}">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-sliders-h"></i> <span>{{__('Company Setting')}}</span></a>
            <ul class="dropdown-menu {{ (Request::segment(1) == 'branch' || Request::segment(1) == 'center' || Request::segment(1) == 'pawningJewellery' || Request::segment(1) == 'service' || Request::segment(1) == 'users' || Request::segment(1) == 'time-period' || Request::segment(1) == 'roles' || Request::segment(1) == 'permissions' || Request::segment(1) == 'systems' || Request::segment(1) == 'interest-rate' || Request::segment(1) == 'goldRate' || Request::segment(1) == 'document_charge_rate'|| Request::segment(1) == 'card_payment_rate' || Request::segment(1) == 'company-setting')?'display:block':''}}">
                @can('manage branch')
                    <li class="{{ (Request::segment(1) == 'branch') ?' active':''}}">
                        <a class="nav-link" href="{{ route('branch.index') }}">{{ __('Branch') }}</a>
                    </li>
                @endcan

                @can('manage center')
                    <li class="{{ (Request::route()->getName() == 'center.index') ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('center.index')}}">{{__('Center')}}</a>
                    </li>
                @endcan

                @can('manage pawning jewellery')
                    <li class="{{ (Request::route()->getName() == 'pawningJewellery.index') ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('pawningJewellery.index')}}">{{__('Jewellery')}}</a>
                    </li>
                @endcan

                @can('manage business category')
                    <li class="{{ (Request::route()->getName() == 'business_category.index') ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('business_category.index')}}">{{__('Business Category')}}</a>
                    </li>
                @endcan

                @can('manage service')
                    <li class="{{ (Request::route()->getName() == 'service.index') ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('service.index')}}">{{__('Service')}}</a>
                    </li>
                @endcan

                @can('manage user')
                    <li class="{{ (Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users.create' || Request::route()->getName() == 'users.edit') ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('users.index') }}">{{ __('Users') }}</a>
                    </li>
                @endcan

                @can('manage role')
                    <li class="{{ (Request::route()->getName() == 'roles.index' || Request::route()->getName() == 'roles.create' || Request::route()->getName() == 'roles.edit') ? ' active' : '' }}">
                        <a class="nav-link" href="{{route('roles.index')}}">{{__('Role')}}</a>
                    </li>
                @endcan

                @if( Gate::check('manage time period') || Gate::check('manage rate'))
                    <li class="{{ (Request::segment(1) == 'time-period' || Request::segment(1) == 'interest-rate' || Request::segment(1) == 'goldRate' || Request::segment(1) == 'document_charge_rate'|| Request::segment(1) == 'card_payment_rate') ? ' active' : '' }}">
                        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><span>{{__('Constant')}}</span></a>
                        <ul class="dropdown-menu {{ (Request::segment(1) == 'time-period' || Request::segment(1) == 'interest-rate' || Request::segment(1) == 'goldRate' || Request::segment(1) == 'document_charge_rate'|| Request::segment(1) == 'card_payment_rate' || Request::segment(1) == 'systems')?'display:block':''}}">
                            @can('manage time period')
                                <li class="{{ (Request::route()->getName() == 'time-period.index' ) ? 'active' : '' }}">
                                    <a class="nav-link" href="{{route('time-period.index')}}"> {{__('Time Period')}}</a>
                                </li>
                            @endcan

                            @can('manage rate')
                                <li class="{{ (Request::route()->getName() == 'interest-rate.index' ) ? 'active' : '' }}">
                                    <a class="nav-link" href="{{route('interest-rate.index')}}"> {{__('Interest Rate')}}</a>
                                </li>
                            @endcan

                            @can('manage rate')
                                <li class="{{ (Request::route()->getName() == 'goldRate.index' ) ? 'active' : '' }}">
                                    <a class="nav-link" href="{{route('goldRate.index')}}"> {{__('Gold Rate')}}</a>
                                </li>
                            @endcan

                            @can('manage rate')
                                <li class="{{ (Request::route()->getName() == 'document_charge_rate.index' ) ? 'active' : '' }}">
                                    <a class="nav-link" href="{{route('document_charge_rate.index')}}"> {{__('Document Charge Rate')}}</a>
                                </li>
                            @endcan

                            @can('manage rate')
                                <li class="{{ (Request::route()->getName() == 'card_payment_rate.index' ) ? 'active' : '' }}">
                                    <a class="nav-link" href="{{route('card_payment_rate.index')}}"> {{__('Card Payment Rate')}}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                @can('manage company settings')
                    <li class="{{ (Request::segment(1) == 'company-setting') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('company.setting') }}"> {{ __('Company Info') }}</a>
                    </li>
                @endcan

            </ul>
        </li>
        @endif

        </ul>
    </aside>
</div>
