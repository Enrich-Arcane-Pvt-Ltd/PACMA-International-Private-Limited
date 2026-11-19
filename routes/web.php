<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();
Route::get('session/status', 'SessionActivityController@status')->middleware('auth')->name('session.status');
Route::post('session/activity', 'SessionActivityController@activity')->middleware('auth')->name('session.activity');

Route::get('/privacy', function () {
    return view('privacy');
});

Route::get('storage/uploads/{path}', function ($path) {
    $fullPath = storage_path('app/public/uploads/' . $path);
    if (!file_exists($fullPath) || !is_file($fullPath)) {
        $fullPath = public_path('uploads/' . $path);
    }
    if (!file_exists($fullPath) || !is_file($fullPath)) {
        $fullPath = storage_path('uploads/' . $path);
    }
    if (file_exists($fullPath) && is_file($fullPath)) {
        return response()->file($fullPath);
    }
    return response()->file(public_path('assets/img/avatar/user.png'));
})->where('path', '.*');

Route::get('/birth-day-wish/{id}', 'CustomerController@birthDayWish')->name('birthDayWish');

Route::get('/register/{lang?}', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register')->name('register');
Route::get('/login/{lang?}', 'Auth\LoginController@showLoginForm')->name('login');

Route::get('/password/resets/{lang?}', 'Auth\LoginController@showLinkRequestForm')->name('change.langPass');

Route::prefix('customer')->as('customer.')->group(
    function () {
        Route::get('login/{lang}', 'Auth\LoginController@showCustomerLoginLang')->name('login.lang')->middleware(['XSS']);
        Route::get('login', 'Auth\LoginController@showCustomerLoginForm')->name('login')->middleware(['XSS']);
        Route::post('login', 'Auth\LoginController@customerLogin')->name('login')->middleware(['XSS']);
        Route::get('dashboard', 'CustomerController@dashboard')->name('dashboard')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );

        Route::get('invoice', 'InvoiceController@customerInvoice')->name('invoice')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );
        Route::get('proposal', 'ProposalController@customerProposal')->name('proposal')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );

        Route::get('proposal/{id}/show', 'ProposalController@customerProposalShow')->name('proposal.show')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );

        Route::get('invoice/{id}/send', 'InvoiceController@customerInvoiceSend')->name('invoice.send')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );
        Route::post('invoice/{id}/send/mail', 'InvoiceController@customerInvoiceSendMail')->name('invoice.send.mail')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );

        Route::get('invoice/{id}/show', 'InvoiceController@customerInvoiceShow')->name('invoice.show')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );


        Route::post('invoice/{id}/payment', 'StripePaymentController@addpayment')->name('invoice.payment')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );
        Route::get('payment', 'CustomerController@payment')->name('payment')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );
        Route::get('transaction', 'CustomerController@transaction')->name('transaction')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );
        Route::post('logout', 'CustomerController@customerLogout')->name('logout')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );
        Route::get('profile', 'CustomerController@profile')->name('profile')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );

        Route::put('update-profile', 'CustomerController@editprofile')->name('update.profile')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );
        Route::put('billing-info', 'CustomerController@editBilling')->name('update.billing.info')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );
        Route::put('shipping-info', 'CustomerController@editShipping')->name('update.shipping.info')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );
        Route::put('change.password', 'CustomerController@updatePassword')->name('update.password')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );
        Route::get('change-language/{lang}', 'CustomerController@changeLanquage')->name('change.language')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );

        Route::post('{id}/pay-with-paypal', 'PaypalController@customerPayWithPaypal')->name('pay.with.paypal')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );
        Route::get('{id}/get-payment-status', 'PaypalController@customerGetPaymentStatus')->name('get.payment.status')->middleware(
            [
                'auth:customer',
                'XSS',
            ]
        );
    }
);


Route::prefix('vender')->as('vender.')->group(
    function () {
        Route::get('login/{lang}', 'Auth\LoginController@showVenderLoginLang')->name('login.lang')->middleware(['XSS']);
        Route::get('login', 'Auth\LoginController@showVenderLoginForm')->name('login')->middleware(['XSS']);
        Route::post('login', 'Auth\LoginController@VenderLogin')->name('login')->middleware(['XSS']);
        Route::get('dashboard', 'VenderController@dashboard')->name('dashboard')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );
        Route::get('bill', 'BillController@VenderBill')->name('bill')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );
        Route::get('bill/{id}/show', 'BillController@venderBillShow')->name('bill.show')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );


        Route::get('bill/{id}/send', 'BillController@venderBillSend')->name('bill.send')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );
        Route::post('bill/{id}/send/mail', 'BillController@venderBillSendMail')->name('bill.send.mail')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );


        Route::get('payment', 'VenderController@payment')->name('payment')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );
        Route::get('transaction', 'VenderController@transaction')->name('transaction')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );
        Route::post('logout', 'VenderController@venderLogout')->name('logout')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );

        Route::get('profile', 'VenderController@profile')->name('profile')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );

        Route::put('update-profile', 'VenderController@editprofile')->name('update.profile')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );
        Route::put('billing-info', 'VenderController@editBilling')->name('update.billing.info')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );
        Route::put('shipping-info', 'VenderController@editShipping')->name('update.shipping.info')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );
        Route::put('change.password', 'VenderController@updatePassword')->name('update.password')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );
        Route::get('change-language/{lang}', 'VenderController@changeLanquage')->name('change.language')->middleware(
            [
                'auth:vender',
                'XSS',
            ]
        );
    }
);


Route::get('/', 'DashboardController@index')->name('dashboard')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::get('/dashboard', 'DashboardController@index')->name('dashboard')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('user/{id}/plan', 'UserController@upgradePlan')->name('plan.upgrade')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('user/{id}/plan/{pid}', 'UserController@activePlan')->name('plan.active')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('profile', 'UserController@profile')->name('profile')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::put('edit-profile', 'UserController@editprofile')->name('update.account')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::resource('users', 'UserController')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('user/details/{id}', 'UserController@getUser')->name('user.get')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::get('user/switch_branch', 'UserController@switchBranchCreate')->name('user.switch_branch')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::post('user/switch_branch/change', 'UserController@switchBranchChange')->name('user.switch_branch.change')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::put('change-password', 'UserController@updatePassword')->name('update.password');


Route::resource('roles', 'RoleController')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::resource('permissions', 'PermissionController')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('change-language/{lang}', 'LanguageController@changeLanquage')->name('change.language');
        Route::get('manage-language/{lang}', 'LanguageController@manageLanguage')->name('manage.language');
        Route::post('store-language-data/{lang}', 'LanguageController@storeLanguageData')->name('store.language.data');
        Route::get('create-language', 'LanguageController@createLanguage')->name('create.language');
        Route::post('store-language', 'LanguageController@storeLanguage')->name('store.language');

        Route::delete('/lang/{lang}', 'LanguageController@destroyLang')->name('lang.destroy');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('systems', 'SystemController');
        Route::post('email-settings', 'SystemController@saveEmailSettings')->name('email.settings');
        Route::post('company-settings', 'SystemController@saveCompanySettings')->name('company.settings');
        Route::post('stripe-settings', 'SystemController@savePaymentSettings')->name('payment.settings');
        Route::post('system-settings', 'SystemController@saveSystemSettings')->name('system.settings');
        Route::get('company-setting', 'SystemController@companyIndex')->name('company.setting');
        Route::post('business-setting', 'SystemController@saveBusinessSettings')->name('business.setting');
        Route::post('company-payment-setting', 'SystemController@saveCompanyPaymentSettings')->name('company.payment.settings');
        Route::get('test-mail', 'SystemController@testMail')->name('test.mail');
        Route::post('test-mail', 'SystemController@testSendMail')->name('test.send.mail');
        Route::post('terms_conditions/settings', 'SystemController@saveTermsConditions')->name('terms_conditions.settings');
        Route::post('company-logo/settings', 'SystemController@saveLogoSettings')->name('company_logo.settings');
    }
);






Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('vender/{id}/show', 'VenderController@show')->name('vender.show');
        Route::resource('vender', 'VenderController');
        Route::get('vender/block/{id}', 'VenderController@block')->name('vender.block');
        Route::get('vender/unblock/{id}', 'VenderController@unblock')->name('vender.unblock');
    }
);



Route::resource('taxes', 'TaxController')->middleware(
    [
        'auth',
        'XSS',
    ]
);




// Route::get('taxes/edit/{id}', 'TaxController@edit');


Route::resource('product-unit', 'ProductServiceUnitController')->middleware(
    [
        'auth',
        'XSS',
    ]
);


Route::get('invoice/pdf/{id}', 'InvoiceController@invoice')->name('invoice.pdf')->middleware(
    [
        'XSS',
    ]
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {


        Route::get('invoice/{id}/duplicate', 'InvoiceController@duplicate')->name('invoice.duplicate');
        Route::get('invoice/{id}/shipping/print', 'InvoiceController@shippingDisplay')->name('invoice.shipping.print');
        Route::get('invoice/{id}/payment/reminder', 'InvoiceController@paymentReminder')->name('invoice.payment.reminder');
        Route::get('invoice/index', 'InvoiceController@index')->name('invoice.index');
        Route::post('invoice/product/destroy', 'InvoiceController@productDestroy')->name('invoice.product.destroy');
        Route::post('invoice/product', 'InvoiceController@product')->name('invoice.product');
        Route::post('invoice/customer', 'InvoiceController@customer')->name('invoice.customer');
        Route::get('invoice/{id}/sent', 'InvoiceController@sent')->name('invoice.sent');
        Route::get('invoice/{id}/resent', 'InvoiceController@resent')->name('invoice.resent');
        Route::get('invoice/{id}/payment', 'InvoiceController@payment')->name('invoice.payment');
        Route::post('invoice/{id}/payment', 'InvoiceController@createPayment')->name('invoice.payment');
        Route::post('invoice/{id}/payment/{pid}/destroy', 'InvoiceController@paymentDestroy')->name('invoice.payment.destroy');
        Route::get('invoice/items', 'InvoiceController@items')->name('invoice.items');

        Route::resource('invoice', 'InvoiceController');
        Route::get('invoice/create/{cid}', 'InvoiceController@create')->name('invoice.create');
        Route::get('hire_purchase_contract/{id}', 'InvoiceController@hirePurchaseContractPrint')->name('hire_purchase_contract.print');

        Route::get('invoice_print/print_preview/{id}/{command}', 'InvoiceController@printPreview')->name('invoice_print.print_preview');

        Route::post('/invoice-update-print-status/{id}', 'InvoiceController@updatePrintStatus')->name('invoice.update.print.status');
        Route::post('/invoice-admin-update-print-status/{id}', 'InvoiceController@adminUpdatePrintStatus')->name('invoice.admin.update.print.status');

    }
);

Route::get(
    '/invoices/preview/{template}/{color}',
    [
        'as' => 'invoice.preview',
        'uses' => 'InvoiceController@previewInvoice',
    ]
);
Route::post(
    '/invoices/template/setting',
    [
        'as' => 'template.setting',
        'uses' => 'InvoiceController@saveTemplateSettings',
    ]
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {


        Route::get('credit-note', 'CreditNoteController@index')->name('credit.note');
        Route::get('custom-credit-note', 'CreditNoteController@customCreate')->name('invoice.custom.credit.note');
        Route::post('custom-credit-note', 'CreditNoteController@customStore')->name('invoice.custom.credit.note');
        Route::get('credit-note/invoice', 'CreditNoteController@getinvoice')->name('invoice.get');
        Route::get('invoice/{id}/credit-note', 'CreditNoteController@create')->name('invoice.credit.note');
        Route::post('invoice/{id}/credit-note', 'CreditNoteController@store')->name('invoice.credit.note');
        Route::get('invoice/{id}/credit-note/edit/{cn_id}', 'CreditNoteController@edit')->name('invoice.edit.credit.note');
        Route::put('invoice/{id}/credit-note/edit/{cn_id}', 'CreditNoteController@update')->name('invoice.edit.credit.note');
        Route::delete('invoice/{id}/credit-note/delete/{cn_id}', 'CreditNoteController@destroy')->name('invoice.delete.credit.note');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {


        Route::get('debit-note', 'DebitNoteController@index')->name('debit.note');
        Route::get('custom-debit-note', 'DebitNoteController@customCreate')->name('bill.custom.debit.note');
        Route::post('custom-debit-note', 'DebitNoteController@customStore')->name('bill.custom.debit.note');
        Route::get('debit-note/bill', 'DebitNoteController@getbill')->name('bill.get');
        Route::get('bill/{id}/debit-note', 'DebitNoteController@create')->name('bill.debit.note');
        Route::post('bill/{id}/debit-note', 'DebitNoteController@store')->name('bill.debit.note');
        Route::get('bill/{id}/debit-note/edit/{cn_id}', 'DebitNoteController@edit')->name('bill.edit.debit.note');
        Route::put('bill/{id}/debit-note/edit/{cn_id}', 'DebitNoteController@update')->name('bill.edit.debit.note');
        Route::delete('bill/{id}/debit-note/delete/{cn_id}', 'DebitNoteController@destroy')->name('bill.delete.debit.note');
    }
);


Route::get(
    '/bill/preview/{template}/{color}',
    [
        'as' => 'bill.preview',
        'uses' => 'BillController@previewBill',
    ]
);
Route::post(
    '/bill/template/setting',
    [
        'as' => 'bill.template.setting',
        'uses' => 'BillController@saveBillTemplateSettings',
    ]
);

Route::resource('taxes', 'TaxController')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::get('revenue/index', 'RevenueController@index')->name('revenue.index')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('revenue/create', 'RevenueController@create')->name('revenue.create');

Route::resource('revenue', 'RevenueController')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::get('bill/pdf/{id}', 'BillController@bill')->name('bill.pdf')->middleware(
    [
        'XSS',
    ]
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('bill/{id}/duplicate', 'BillController@duplicate')->name('bill.duplicate');
        Route::get('bill/{id}/shipping/print', 'BillController@shippingDisplay')->name('bill.shipping.print');
        Route::get('bill/index', 'BillController@index')->name('bill.index');
        Route::post('bill/product/destroy', 'BillController@productDestroy')->name('bill.product.destroy');
        Route::post('bill/product', 'BillController@product')->name('bill.product');
        Route::post('bill/vender', 'BillController@vender')->name('bill.vender');
        Route::get('bill/{id}/sent', 'BillController@sent')->name('bill.sent');
        Route::get('bill/{id}/resent', 'BillController@resent')->name('bill.resent');
        Route::get('bill/{id}/payment', 'BillController@payment')->name('bill.payment');
        Route::post('bill/{id}/payment', 'BillController@createPayment')->name('bill.payment');
        Route::post('bill/{id}/payment/{pid}/destroy', 'BillController@paymentDestroy')->name('bill.payment.destroy');
        Route::get('bill/items', 'BillController@items')->name('bill.items');

        Route::resource('bill', 'BillController');
        Route::get('bill/create/{cid}', 'BillController@create')->name('bill.create');
    }
);


Route::get('payment/index', 'PaymentController@index')->name('payment.index')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::resource('payment', 'PaymentController')->middleware(
    [
        'auth',
        'XSS',
    ]
);


Route::resource('plans', 'PlanController')->middleware(
    [
        'auth',
        'XSS',
    ]
);


Route::resource('expenses', 'ExpenseController')->middleware(
    [
        'auth',
        'XSS',
    ]
);


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('/orders', 'StripePaymentController@index')->name('order.index');
        Route::get('/stripe/{code}', 'StripePaymentController@stripe')->name('stripe');
        Route::post('/stripe', 'StripePaymentController@stripePost')->name('stripe.post');
    }
);


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('report/transaction', 'TransactionController@index')->name('transaction.index');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('report/income-summary', 'ReportController@incomeSummary')->name('report.income.summary');
        Route::get('report/expense-summary', 'ReportController@expenseSummary')->name('report.expense.summary');
        Route::get('report/income-vs-expense-summary', 'ReportController@incomeVsExpenseSummary')->name('report.income.vs.expense.summary');
        Route::get('report/tax-summary', 'ReportController@taxSummary')->name('report.tax.summary');
        Route::get('report/profit-loss-summary', 'ReportController@profitLossSummary')->name('report.profit.loss.summary');

        Route::get('report/invoice-summary', 'ReportController@invoiceSummary')->name('report.invoice.summary');
        Route::get('report/bill-summary', 'ReportController@billSummary')->name('report.bill.summary');

        Route::get('report/invoice-report', 'ReportController@invoiceReport')->name('report.invoice');
        Route::get('report/account-statement-report', 'ReportController@accountStatement')->name('report.account.statement');
    }
);

Route::get(
    '/apply-coupon',
    [
        'as' => 'apply.coupon',
        'uses' => 'CouponController@applyCoupon',
    ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::resource('coupons', 'CouponController')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::get('proposal/pdf/{id}', 'ProposalController@proposal')->name('proposal.pdf')->middleware(
    [
        'XSS',
    ]
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('proposal/{id}/status/change', 'ProposalController@statusChange')->name('proposal.status.change');
        Route::get('proposal/{id}/convert', 'ProposalController@convert')->name('proposal.convert');
        Route::get('proposal/{id}/duplicate', 'ProposalController@duplicate')->name('proposal.duplicate');
        Route::post('proposal/product/destroy', 'ProposalController@productDestroy')->name('proposal.product.destroy');
        Route::post('proposal/customer', 'ProposalController@customer')->name('proposal.customer');
        Route::post('proposal/product2', 'ProposalController@product')->name('proposal.product2');
        Route::get('proposal/items', 'ProposalController@items')->name('proposal.items');
        Route::get('proposal/{id}/sent', 'ProposalController@sent')->name('proposal.sent');
        Route::get('proposal/{id}/resent', 'ProposalController@resent')->name('proposal.resent');

        Route::resource('proposal', 'ProposalController');
        Route::get('proposal/create/{cid}', 'ProposalController@create')->name('proposal.create');
    }
);

Route::get(
    '/proposal/preview/{template}/{color}',
    [
        'as' => 'proposal.preview',
        'uses' => 'ProposalController@previewProposal',
    ]
);
Route::post(
    '/proposal/template/setting',
    [
        'as' => 'proposal.template.setting',
        'uses' => 'ProposalController@saveProposalTemplateSettings',
    ]
);

Route::resource('goal', 'GoalController')->middleware(
    [
        'auth',
        'XSS',
    ]
);


Route::resource('custom-field', 'CustomFieldController')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::post('plan-pay-with-paypal', 'PaypalController@planPayWithPaypal')->name('plan.pay.with.paypal')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::get('{id}/plan-get-payment-status', 'PaypalController@planGetPaymentStatus')->name('plan.get.payment.status')->middleware(
    [
        'auth',
        'XSS',
    ]
);


//Iresha
Route::resource('sale', 'SaleController')->middleware(
    [
        'auth',
        'XSS',
    ]
);


//Dilan
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('sale/get_details/{id}', 'SaleController@getDetails');
        Route::get('sale/stock-batches/{product}', 'StockBatchController@options')->name('sale.stock-batches');
        Route::get('stock/batch/{id}', 'StockBatchController@show')->name('stock.batch');
        Route::get('sale/get_warranty_serials/{productId}', 'SaleController@getWarrantySerials');
        Route::get('sale/get_interest_rate/{id}', 'SaleController@getInterestRate');
        Route::get('sale/view/{id}', 'SaleController@view')->name('sale.view');

        // New routes for hire_sales and direct_sales views
        Route::get('sales_hire', 'SaleController@hireSales')->name('sales_hire.index'); // For hire sales
        Route::get('sales_direct', 'SaleController@directSales')->name('sales_direct.index'); // For direct sales

        // Route::get('sale/view/', 'SaleController@view')->name('sale.view');
        Route::post('sale/create', 'SaleController@store');
        Route::post('sale/authenticate', 'SaleController@authenticate')->name('sale.authenticate');
        Route::get('sale/product_id_by_barcode/{id}', 'SaleController@productIdByBarcode');

        Route::get('sale/get_cust_advance/{id}', 'SaleController@getCustAdvance')->name('sale.get_cust_advance');
        Route::get('direct_sale/create', 'SaleController@directSaleCreate')->name('direct_sale.create');
        Route::get('/get-product-details/{product_id}', 'SaleController@getProductDetails')->name('get.product.details');


        Route::get('sale/edit/{id}', 'SaleController@edit')->name('sale.edit');
        Route::post('sale/update', 'SaleController@update')->name('sale.update');

        Route::post('sale/item/delete', 'SaleController@itemDelete')->name('sale.item.delete');
        Route::get('sale/get_hire_purchase_index/{customer}/{date}', 'SaleController@hirePurchaseIndex')->name('sale.get_hire_purchase_index');

        Route::post('/sales-update-print-status/{id}', 'InvoiceController@updatePrintStatus')->name('sales.update.print.status');
        Route::post('/sales-admin-update-print-status/{id}', 'InvoiceController@adminUpdatePrintStatus')->name('sales.admin.update.print.status');
    }
);

Route::resource('hire_purchase_contract', 'HirePurchaseContractController')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('hire_purchase_contract/view/{id}', 'HirePurchaseContractController@view')->name('hire_purchase_contract.view');
        Route::get('hire_purchase_contract/pay_installment/{id}', 'HirePurchaseContractController@payInstallment')->name('hire_purchase_contract.pay_installment');
        Route::get('hire_purchase_contract/receipt/{id}', 'HirePurchaseContractController@receipt')->name('hire_purchase_contract.receipt');
        Route::get('hire_purchase_contract/receipt/print/{id}', 'HirePurchaseContractController@receiptPrint')->name('hire_purchase_contract.receipt.print');
        Route::get('hire_purchase_contract/cash_collector_receipt/{id}', 'HirePurchaseContractController@cashCollectorReceiptPrint')->name('hire_purchase_contract.cash_collector_receipt');
        Route::get('hire_purchase_contract/cancel_installment/{id}', 'HirePurchaseContractController@cancelInstallment')->name('hire_purchase_contract.cancel_installment');
        Route::post('hire_purchase_contract/get_advance_amount', 'HirePurchaseContractController@getAdvanceAmount')->name('hire_purchase_contract.get_advance_amount');
        Route::post('hire_purchase_contract/add_advance_amount', 'HirePurchaseContractController@addAdvanceAmount')->name('hire_purchase_contract.add_advance_amount');
        Route::post('hire_purchase_contract/edit_advance_amount', 'HirePurchaseContractController@editAdvanceAmount')->name('hire_purchase_contract.edit_advance_amount');
        Route::post('hire_purchase_contract/remove_advance_amount', 'HirePurchaseContractController@removeAdvanceAmount')->name('hire_purchase_contract.remove_advance_amount');
    }
);


// Route::get('hire_purchase_contract/receipt/print/{id}', 'HirePurchaseContractController@receiptPrint')->name('hire_purchase_contract.receipt.print');







//dilanja
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {


        // Route::get('due_payment/index', 'ReportController@duePaymentreport')->name('duepaymentReport');
        // Route::post('due_payment/search', 'ReportController@search')->name('duepaymentSearch');
        // Route::get('due_payment/index', 'ReportController@duePaymentreport')->name('duepaymentReport');
        // Route::get('due_payment/installment/getProductInstallment/{id}', 'ReportController@installmentBySale')->name('installmentBySale');

        Route::post('due_payment/search', 'ReportController@duePaymentSearch')->name('due_payment.search');
        Route::get('due_payment/index', 'ReportController@duePaymentIndex')->name('due_payment.index');
        Route::get('due_payment/installment/getProductInstallment/{id}', 'ReportController@installmentBySale')->name('installmentBySale');


        Route::get('ele_profit/index', 'ReportController@salesReportIndex')->name('ele_profit.index');
        Route::post('ele_profit/search', 'ReportController@salesReportSearch')->name('ele_profit.search');
        Route::get('ele_profit/installment/getProductInstallment/{id}', 'ReportController@installmentBySale')->name('installmentBySale');

        Route::get('accessories_profit/index', 'ReportController@accessoriesReportIndex')->name('accessories_profit.index');
        Route::post('accessories_profit/search', 'ReportController@accessoriesReportSearch')->name('accessories_profit.search');

        Route::get('customer_report/index', 'ReportController@customerReportIndex')->name('customer_report.index');

        Route::get('loan_report/index', 'ReportController@loanReportIndex')->name('loan_report.index');
        Route::post('loan_report/search', 'ReportController@loanReportSearch')->name('loan_report.search');
        Route::get('loan_report/installment/getLoanInstallment/{id}', 'ReportController@installmentByLoan')->name('installmentByLoan');

        Route::get('loan_outstanding_report/index', 'ReportController@loanOutstandingReportIndex')->name('loan_outstanding_report.index');
        Route::post('loan_outstanding_report/search', 'ReportController@loanOutstandingReportSearch')->name('loan_outstanding_report.search');
        Route::get('loan_outstanding_report/get_groups/{id}', 'ReportController@getGroups')->name('loan_outstanding_report.get_groups');

        Route::get('loan_installment_report/index', 'ReportController@loanInstallmentReportIndex')->name('loan_installment_report.index');
        Route::post('loan_installment_report/search', 'ReportController@loanInstallmentReportSearch')->name('loan_installment_report.search');

        Route::get('collection_report/index', 'ReportController@collectionReportIndex')->name('collection_report.index');
        Route::post('collection_report/search', 'ReportController@collectionReportSearch')->name('collection_report.search');

        Route::get('hire_purchase_outstanding_report/index', 'ReportController@hirePurchaseOutstandingReportIndex')->name('hire_purchase_outstanding_report.index');
        Route::post('hire_purchase_outstanding_report/search', 'ReportController@hirePurchaseOutstandingReportSearch')->name('hire_purchase_outstanding_report.search');

        Route::get('stocks_report/index', 'ReportController@stocksReportIndex')->name('stocks_report.index');
        Route::post('stocks_report/search', 'ReportController@stocksReportSearch')->name('stocks_report.search');

        Route::get('outstanding_loan_arrears_report/index', 'ReportController@outstandingLoanArrearsReportIndex')->name('outstanding_loan_arrears_report.index');
        Route::post('outstanding_loan_arrears_report/search', 'ReportController@outstandingLoanArrearsReportSearch')->name('outstanding_loan_arrears_report.search');

        Route::get('stock_transfers_report/index', 'ReportController@stockTransfersReportIndex')->name('stock_transfers_report.index');
        Route::post('stock_transfers_report/search', 'ReportController@stockTransfersReportSearch')->name('stock_transfers_report.search');

        Route::get('stock_item_in_out_report/index', 'ReportController@stockItemReportIndex')->name('stock_item_in_out_report.index');
        Route::post('stock_item_in_out_report/search', 'ReportController@stockItemReportSearch')->name('stock_item_in_out_report.search');

        Route::get('stock_in_out_report/index', 'ReportController@stockReportIndex')->name('stock_in_out_report.index');
        Route::post('stock_in_out_report/search', 'ReportController@stockReportSearch')->name('stock_in_out_report.search');

        Route::get('sales_repayment_sheet/index', 'ReportController@salesRepaymentSheetIndex')->name('sales_repayment_sheet.index');
        Route::post('sales_repayment_sheet/search', 'ReportController@salesRepaymentSheetSearch')->name('sales_repayment_sheet.search');

        Route::get('repayment_sheet/index', 'ReportController@repaymentSheetIndex')->name('repayment_sheet.index');
        Route::post('repayment_sheet/search', 'ReportController@repaymentSheetSearch')->name('repayment_sheet.search');

        Route::get('executive_repayment_sheet/index', 'ReportController@executiveRepaymentSheetIndex')->name('executive_repayment_sheet.index');
        Route::post('executive_repayment_sheet/search', 'ReportController@executiveRepaymentSheetSearch')->name('executive_repayment_sheet.search');

        Route::get('supplier_report/index', 'ReportController@supplierReportIndex')->name('supplier_report.index');
        Route::get('supplier_report/get_grns/{id}', 'ReportController@supplierReportgetGrns')->name('supplier_report.get_grns');

        Route::get('attendance_sheet/index', 'ReportController@attendanceSheetIndex')->name('attendance_sheet.index');
        Route::post('attendance_sheet/search', 'ReportController@attendanceSheetSearch')->name('attendance_sheet.search');

        Route::get('cash_collector_report/index', 'ReportController@cashCollectorReportIndex')->name('cash_collector_report.index');
        Route::post('cash_collector_report/search', 'ReportController@cashCollectorReportSearch')->name('cash_collector_report.search');

        Route::get('loan_disbursement_report/index', 'ReportController@loanDisbursementReportIndex')->name('loan_disbursement_report.index');
        Route::post('loan_disbursement_report/search', 'ReportController@loanDisbursementReportSearch')->name('loan_disbursement_report.search');

        Route::get('closed_loan_report/index', 'ReportController@closedLoanReportIndex')->name('closed_loan_report.index');
        Route::post('closed_loan_report/search', 'ReportController@closedLoanReportSearch')->name('closed_loan_report.search');

        Route::get('pawning_outstanding_report/index', 'ReportController@pawningOutstandingReportIndex')->name('pawning_outstanding_report.index');
        Route::post('pawning_outstanding_report/search', 'ReportController@pawningOutstandingReportSearch')->name('pawning_outstanding_report.search');

        Route::get('closed_hire_purchase_report/index', 'ReportController@closedHirePurchaseReportIndex')->name('closed_hire_purchase_report.index');

        Route::get('petty_cash_report/index', 'ReportController@pettyCashReportIndex')->name('petty_cash_report.index');
        Route::post('petty_cash_report/search', 'ReportController@pettyCashReportSearch')->name('petty_cash_report.search');

        Route::get('customer_ledger_report/index', 'ReportController@customerLedgerReportIndex')->name('customer_ledger_report.index');
        Route::post('customer_ledger_report/search', 'ReportController@customerLedgerReportSearch')->name('customer_ledger_report.search');
        Route::get('customer_ledger_report/sales_details/{id}/{command}', 'ReportController@customerLedgerReportSaleDetails')->name('customer_ledger_report.sales_details');
        Route::get('customer_ledger_report/loan_details/{id}/{command}', 'ReportController@customerLedgerReportLoanDetails')->name('customer_ledger_report.loan_details');
        Route::get('customer_ledger_report/pawning_details/{id}/{command}', 'ReportController@customerLedgerReportPawningDetails')->name('customer_ledger_report.pawning_details');

        // Item Return Report
        Route::get('item_return_report/index', 'ReportController@itemReturnReportIndex')->name('item_return_report.index');
        Route::post('item_return_report/search', 'ReportController@itemReturnReportSearch')->name('item_return_report.search');

        Route::get('loan_stock_report/index', 'ReportController@loanStockReportIndex')->name('loan_stock_report.index');
        Route::post('loan_stock_report/search', 'ReportController@loanStockReportSearch')->name('loan_stock_report.search');
    }
);



//Nipun
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('receiptReport/index', 'ReportController@receiptReport')->name('receiptReport');
        Route::post('receiptsearch/search', 'ReportController@receiptsearch')->name('receiptsearch');
        Route::get('receiptsearch/receipt/{id}', 'ReportController@receiptDetail')->name('receiptsearch.receipt');

        Route::get('holidays', 'HolidayController@index')->name('holidays');
        Route::get('holidays/create', 'HolidayController@create')->name('holidays.create');
        Route::post('holidays/create', 'HolidayController@store')->name('holidays.store');
        Route::post('holidays/edit/{id?}', 'HolidayController@edit')->name('holidays.edit');
        Route::get('holidays/delete/{id?}', 'HolidayController@destroy')->name('holidays.delete');
    }
);


// =========================================================================================================================================

// customer
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('customer/{id}/show/', 'CustomerController@show')->name('customer.show');
        Route::resource('customer', 'CustomerController');
        Route::get('customer/block/{id}', 'CustomerController@block')->name('customer.block');
        Route::get('customer/unblock/{id}', 'CustomerController@unblock')->name('customer.unblock');
        Route::get('customer/loaction/{id}', 'CustomerController@mapView')->name('customer.loaction');
    }
);


// banking
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('bank-account', 'BankAccountController');
        Route::get('bank-account/block/{id}', 'BankAccountController@block')->name('bank-account.block');
        Route::get('bank-account/unblock/{id}', 'BankAccountController@unblock')->name('bank-account.unblock');

        Route::get('bank-transfer/index', 'BankTransferController@index')->name('bank-transfer.index');
        Route::resource('bank-transfer', 'BankTransferController');
    }
);


// asset
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('asset', 'AssetController');
    }
);


// category & product
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('product-brand', 'ProductBrandController');
        Route::get('product-brand/block/{id}', 'ProductBrandController@block')->name('product-brand.block');
        Route::get('product-brand/unblock/{id}', 'ProductBrandController@unblock')->name('product-brand.unblock');

        Route::resource('product-category', 'ProductCategoryController');
        Route::get('product-category/block/{id}', 'ProductCategoryController@block')->name('product-category.block');
        Route::get('product-category/unblock/{id}', 'ProductCategoryController@unblock')->name('product-category.unblock');

        Route::resource('product-sub-category', 'ProductSubCategoryController');
        Route::get('product-sub-category/block/{id}', 'ProductSubCategoryController@block')->name('product-sub-category.block');
        Route::get('product-sub-category/unblock/{id}', 'ProductSubCategoryController@unblock')->name('product-sub-category.unblock');

        Route::get('product/{id}/show/', 'ProductController@show')->name('product.show');
        Route::resource('product', 'ProductController');
        Route::get('product/block/{id}', 'ProductController@block')->name('product.block');
        Route::get('product/unblock/{id}', 'ProductController@unblock')->name('product.unblock');

        Route::resource('product-warranty', 'ProductWarrantyController');
        Route::get('product-warranty/block/{id}', 'ProductWarrantyController@block')->name('product-warranty.block');
        Route::get('product-warranty/unblock/{id}', 'ProductWarrantyController@unblock')->name('product-warranty.unblock');
    }
);
















//loan
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('group', 'GroupController');
        Route::get('group/block/{id}', 'GroupController@block')->name('group.block');
        Route::get('group/unblock/{id}', 'GroupController@unblock')->name('group.unblock');
        Route::get('group/add_member/{id}', 'GroupController@addMember')->name('group.add_member');
        Route::post('group/store', 'GroupController@store')->name('group.store');
        Route::post('group/update', 'GroupController@update')->name('group.update');
        Route::get('group/view/{id}', 'GroupController@view')->name('group.view');
        Route::get('group/check_customer_loan/{id}', 'GroupController@checkLoan')->name('group.check_customer_loan');
    }
);


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        // Route::get('LoanView/index', 'LoanController@index')->name('loanView');
        // Route::get('LoanView/create', 'LoanController@create')->name('loanCreate');

        // Route::get('sme_loan', 'LoanController@smeLoan')->name('sme_loan');
        // Route::get('sme_loan/create', 'LoanController@smeLoanCreate')->name('sme_loan.create');
        // Route::get('sme_loan/doc_charge_rate', 'LoanController@docChargeRate')->name('sme_loan.doc_charge_rate');
        // Route::post('sme_loan/save', 'LoanController@smeLoanSave')->name('sme_loan.save');

        // Route::get('group_loan', 'LoanController@groupLoan')->name('group_loan');
        // Route::get('group_loan/create', 'LoanController@groupLoanCreate')->name('group_loan.create');
        // Route::post('group_loan/save', 'LoanController@groupLoanSave')->name('group_loan.save');

        // Route::get('vehicle_loan', 'LoanController@vehicleLoan')->name('vehicle_loan');
        // Route::get('vehicle_loan/create', 'LoanController@vehicleLoanCreate')->name('vehicle_loan.create');
        // Route::post('vehicle_loan/save', 'LoanController@vehicleLoanSave')->name('vehicle_loan.save');

        // Route::get('land_loan', 'LoanController@landLoan')->name('land_loan');
        // Route::get('land_loan/create', 'LoanController@landLoanCreate')->name('land_loan.create');
        // Route::post('land_loan/save', 'LoanController@landLoanSave')->name('land_loan.save');

        // Route::get('loan/approve/{id}', 'LoanController@loanApprove')->name('loan.approve');
        // Route::get('loan/reject/{id}', 'LoanController@loanReject')->name('loan.reject');
        // Route::get('loan/view/{id}', 'LoanController@show')->name('loan.view');
        // Route::get('loan/pay_installment/{id}', 'LoanController@payInstallment')->name('loan.pay_installment');
        // Route::post('loan_installment/pay', 'LoanController@installmentSave')->name('loan_installment.pay');
        // Route::get('loan/receipt/{id}', 'LoanController@receipt')->name('loan.receipt');
        // Route::get('loan/receipt/print/{id}', 'LoanController@receiptPrint')->name('loan.receipt.print');
        // Route::get('loan/group_search/{id}', 'LoanController@groupSearch')->name('loan.group_search');
        // Route::get('loan/customer_search/{id}', 'LoanController@customerSearch')->name('loan.customer_search');
        // Route::get('loan/guarantor_search/{cid}/{gid}', 'LoanController@guarantorSearch')->name('loan.guarantor_search');


        Route::get('LoanView/index', 'LoanController@index')->name('loanView');
        Route::get('LoanView/create', 'LoanController@create')->name('loanCreate');

        Route::get('sme_loan', 'LoanController@smeLoan')->name('sme_loan');
        Route::get('sme_loan/create', 'LoanController@smeLoanCreate')->name('sme_loan.create');
        Route::get('sme_loan/doc_charge_rate', 'LoanController@docChargeRate')->name('sme_loan.doc_charge_rate');
        Route::post('sme_loan/save', 'LoanController@smeLoanSave')->name('sme_loan.save');
        Route::get('sme_loan/cal-due-date', 'LoanController@calDueDate')->name('sme_loan.cal-due-date');

        Route::get('group_loan', 'LoanController@groupLoan')->name('group_loan');
        Route::get('group_loan/create', 'LoanController@groupLoanCreate')->name('group_loan.create');
        Route::post('group_loan/save', 'LoanController@groupLoanSave')->name('group_loan.save');
        Route::get('group_loan/edit/{id}', 'LoanController@groupLoanEdit')->name('group_loan.edit');
        Route::post('group_loan/update', 'LoanController@groupLoanUpdate')->name('group_loan.update');
        Route::get('group_loan/approve_check/{id}', 'LoanController@groupLoanApproveCheck')->name('group_loan.approve_check');

        Route::get('group_loan/group_loan_check/{id}', 'LoanController@groupLoanCheck')->name('group_loan.group_loan_check');

        Route::get('vehicle_loan', 'LoanController@vehicleLoan')->name('vehicle_loan');
        Route::get('vehicle_loan/create', 'LoanController@vehicleLoanCreate')->name('vehicle_loan.create');
        Route::post('vehicle_loan/save', 'LoanController@vehicleLoanSave')->name('vehicle_loan.save');

        Route::get('land_loan', 'LoanController@landLoan')->name('land_loan');
        Route::get('land_loan/create', 'LoanController@landLoanCreate')->name('land_loan.create');
        Route::post('land_loan/save', 'LoanController@landLoanSave')->name('land_loan.save');

        Route::get('job_loan', 'LoanController@jobLoan')->name('job_loan');
        Route::get('job_loan/create', 'LoanController@jobLoanCreate')->name('job_loan.create');
        Route::post('job_loan/save', 'LoanController@jobLoanSave')->name('job_loan.save');

        Route::get('loan/edit/{id}', 'LoanController@loanEdit')->name('loan.edit');
        Route::post('loan/update', 'LoanController@loanUpdate')->name('loan.update');

        Route::get('loan/loan_check/{id}', 'LoanController@loanCheck')->name('loan.loan_check');
        Route::post('loan/checked', 'LoanController@checked')->name('loan.checked');

        Route::get('loan/approve_check/{id}', 'LoanController@loanApproveCheck')->name('loan.approve_check');
        Route::post('loan/approve', 'LoanController@loanApprove')->name('loan.approve');
        Route::get('loan/destroy/{id}', 'LoanController@destroy')->name('loan.destroy');

        Route::get('loan/reject/{id}', 'LoanController@loanReject')->name('loan.reject');
        Route::get('loan/view/{id}', 'LoanController@show')->name('loan.view');
        Route::get('loan/pay_installment/{id}', 'LoanController@payInstallment')->name('loan.pay_installment');
        Route::post('loan_installment/pay', 'LoanController@installmentSave')->name('loan_installment.pay');
        Route::get('loan/receipt/{id}', 'LoanController@receipt')->name('loan.receipt');
        Route::get('loan/receipt/print/{id}', 'LoanController@receiptPrint')->name('loan.receipt.print');
        Route::get('loan/group_search/{id}', 'LoanController@groupSearch')->name('loan.group_search');
        Route::get('loan/customer_search/{id}', 'LoanController@customerSearch')->name('loan.customer_search');
        Route::get('loan/guarantor_search/{cid}/{gid}', 'LoanController@guarantorSearch')->name('loan.guarantor_search');
        Route::post('sme_loan/send_fine', 'LoanController@sendFine')->name('sme_loan.send_fine');

        Route::get('sms/test', 'LoanController@sendTest');

        Route::get('loan/guarantor_1_search/{id}', 'LoanController@guarantor1Search')->name('loan.guarantor_1_search');
        Route::get('loan/guarantor_2_search/{id1}/{id2}', 'LoanController@guarantor2Search')->name('loan.guarantor_2_search');
        Route::get('loan/guarantor_3_search/{id1}/{id2}/{id3}', 'LoanController@guarantor3Search')->name('loan.guarantor_3_search');

        Route::get('loan_cheque/create/{id}', 'LoanController@chequePrint')->name('loan_cheque.create');
        // Route::post('loan/authenticate', 'SaleController@authenticate')->name('sale.authenticate');
        Route::post('loan/authenticate', 'LoanController@authenticate')->name('loan.authenticate');

        Route::get('loan/loan_agreement/{id}', 'LoanController@loanAgreement')->name('loan.loan_agreement');
        Route::get('loan/loan_guaranter_bond/{id}', 'LoanController@loanGuaranterBond')->name('loan.loan_guaranter_bond');
        Route::get('loan/loan_promissary_note/{id}', 'LoanController@loanPromissaryNote')->name('loan.loan_promissary_note');

        Route::get('loan/cash_collector_receipt/{id}', 'LoanController@cashCollectorReceiptPrint')->name('loan.cash_collector_receipt');

        Route::get('loan_payment_voucher/create/{id}', 'LoanController@voucherCreate')->name('loan_payment_voucher.create');

        Route::get('loan_installment/cancel_installment/{id}', 'LoanController@cancelInstallment')->name('loan_installment.cancel_installment');

        //daily collection
        Route::get('daily_collection', 'LoanController@dailyCollection')->name('daily_collection');
        Route::post('daily_collection/search', 'LoanController@dailyCollectionSearch')->name('daily_collection.search');
        Route::get('loan/daily_collection_view/{id}/{array}', 'LoanController@dailyCollectionShow')->name('loan.daily_collection_view');


        Route::get('group_loan/disburse_check/{id}', 'LoanController@groupLoanDisburseCheck')->name('group_loan.disburse_check');
        Route::get('loan/disburse_check/{id}', 'LoanController@loanDisburseCheck')->name('loan.disburse_check');

        Route::post('loan/disburse', 'LoanController@loanDisburse')->name('loan.disburse');


        Route::post('loan/get_advance_amount', 'LoanController@getAdvanceAmount')->name('loan.get_advance_amount');
        Route::post('loan/add_advance_amount', 'LoanController@addAdvanceAmount')->name('loan.add_advance_amount');
        Route::post('loan/edit_advance_amount', 'LoanController@editAdvanceAmount')->name('loan.edit_advance_amount');
        Route::post('loan/remove_advance_amount', 'LoanController@removeAdvanceAmount')->name('loan.remove_advance_amount');
    }
);


// pawning
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        // Route::get('pawning/{id}/pawningTicket/', 'PawningController@pawningTicket')->name('pawning.pawningTicket');


        Route::resource('pawning', 'PawningController');
        Route::post('pawning/product', 'PawningController@product')->name('pawning.product');
        Route::get('pawning/view/{id}', 'PawningController@view')->name('pawning.view');
        Route::get('pawning/gold_rate_search/{id}', 'PawningController@goldRateSearch')->name('loan.gold_rate_search');
        Route::get('pawning/pay_installment/{id}', 'PawningController@payInstallment')->name('pawning.pay_installment');
        Route::post('pawning/pay', 'PawningController@installmentPayment')->name('pawning.pay');
        Route::get('pawning/receipt/{id}', 'PawningController@receipt')->name('pawning.receipt');
        Route::get('pawning/receipt/print/{id}', 'PawningController@receiptPrint')->name('pawning.receipt.print');
        Route::get('pawning/ticket/{id}', 'PawningController@showPawningTicket')->name('pawning.ticket');

        Route::get('pawning/cash_collector_receipt/{id}', 'PawningController@cashCollectorReceiptPrint')->name('pawning.cash_collector_receipt');

        Route::get('pawning/pirint_ticket/{id}', 'PawningController@pirintTicket')->name('pawning.pirint_ticket');

        Route::post('pawning/update', 'PawningController@update')->name('pawning.update');

        // Route::get('pawn-item/block/{id}', 'PawnItemController@block')->name('pawn-item.block');
        // Route::get('pawn-item/unblock/{id}', 'PawnItemController@unblock')->name('pawn-item.unblock');

        Route::get('pawning/reminder_one/{id}', 'PawningController@showReminderOne')->name('pawning.reminder_one');
        Route::get('pawning/reminder_two/{id}', 'PawningController@showReminderTwo')->name('pawning.reminder_two');
        Route::get('pawning/reminder_three/{id}', 'PawningController@showReminderThree')->name('pawning.reminder_three');

        Route::get('pawning_payment_voucher/create/{id}', 'PawningController@voucherCreate')->name('pawning_payment_voucher.create');

        // Route::get('invoice/{id}/payment/reminder', 'InvoiceController@paymentReminder')->name('invoice.payment.reminder');
        Route::post('pawning/authenticate', 'PawningController@authenticate')->name('pawning.authenticate');

        Route::get('pawning/cancel_installment/{id}', 'PawningController@cancelInstallment')->name('pawning.cancel_installment');

        Route::resource('overdue-pawning', 'OverduePawningController');
        Route::get('overdue-pawning/{id}/reminderSend/', 'OverduePawningController@sendReminder')->name('overdue-pawning.block');
    }
);


// company setting
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('branch', 'BranchController');
        Route::get('branch/block/{id}', 'BranchController@block')->name('branch.block');
        Route::get('branch/unblock/{id}', 'BranchController@unblock')->name('branch.unblock');

        Route::resource('center', 'CenterController');
        Route::get('center/block/{id}', 'CenterController@block')->name('center.block');
        Route::get('center/unblock/{id}', 'CenterController@unblock')->name('center.unblock');

        Route::resource('pawningJewellery', 'PawningJewelleryController');
        Route::get('pawningJewellery/block/{id}', 'PawningJewelleryController@block')->name('pawningJewellery.block');
        Route::get('pawningJewellery/unblock/{id}', 'PawningJewelleryController@unblock')->name('pawningJewellery.unblock');

        Route::resource('service-provider', 'ServiceController');
        Route::get('service-provider/block/{id}', 'ServiceController@block')->name('service_provider.block');
        Route::get('service-provider/unblock/{id}', 'ServiceController@unblock')->name('service_provider.unblock');

        Route::resource('time-period', 'TimePeriodController');
        Route::get('time-period/block/{id}', 'TimePeriodController@block')->name('time-period.block');
        Route::get('time-period/unblock/{id}', 'TimePeriodController@unblock')->name('time-period.unblock');

        Route::resource('business_category', 'BusinessCategoryController');
        Route::post('business_category/update', 'BusinessCategoryController@update')->name('business_category.update');
        Route::get('business_category/block/{id}', 'BusinessCategoryController@block')->name('business_category.block');
        Route::get('business_category/unblock/{id}', 'BusinessCategoryController@unblock')->name('business_category.unblock');

        Route::resource('loan_package', 'LoanPackageController');
        Route::get('loan_package/block/{id}', 'LoanPackageController@block')->name('loan_package.block');
        Route::get('loan_package/unblock/{id}', 'LoanPackageController@unblock')->name('loan_package.unblock');
        Route::get('loan_package/get_detail/{id}', 'LoanPackageController@getDetail')->name('loan_package.get_detail');
    }
);

// interest rate
Route::resource('interest-rate', 'InterestRateController')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::resource('goldRate', 'GoldRateController');
Route::get('goldRate/block/{id}', 'GoldRateController@block')->name('goldRate.block');
Route::get('goldRate/unblock/{id}', 'GoldRateController@unblock')->name('goldRate.unblock');

Route::get('productService/info/{id}', 'ProductServiceController@info')->name('productService.info');
Route::get('gridView/gridView', 'GridController@grid')->name('gridView.grid');
Route::get('productService/detail/{id}', 'GridController@detail')->name('productService.detail');

// purchase order
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('purchase-order/index', 'PurchaseOrderController@index')->name('purchaseOrder.index');
        Route::resource('purchase-order', 'PurchaseOrderController');
        Route::get('purchase-order/create/{cid}', 'PurchaseOrderController@create')->name('purchaseOrder.create');
        Route::post('purchase-order/get-last-purchase-price', 'PurchaseOrderController@lastPurchasePrice')->name('get_last_purchase_price');
        Route::post('proposal/product', 'PurchaseOrderController@productDetails')->name('proposal.product');
        Route::get('purchase-order/edit/{id}', 'PurchaseOrderController@edit')->name('purchaseOrder.edit');
        Route::post('purchase-order/items', 'PurchaseOrderController@items')->name('purchase-order.items');
        Route::post('purchase-order/product/destroy', 'PurchaseOrderController@productDestroy')->name('purchase-order.product.destroy');
        Route::get('purchase-order/pdf/{id}', 'PurchaseOrderController@pdf')->name('purchase-order.pdf');
        Route::get('purchase-order/sms/{id}', 'PurchaseOrderController@sms')->name('purchase-order.sms');
        Route::get('purchase-order/whatsappManual/{id}', 'PurchaseOrderController@whatsappManual')->name('purchase-order.whatsappManual');
        Route::get('purchase-order/print/{id}', 'PurchaseOrderController@print')->name('purchase-order.print');
        Route::post('purchase-order/vender', 'PurchaseOrderController@vender')->name('purchase-order.vender');


        Route::get('stock/{id}/quantity/', 'StockController@quantity')->name('stock.quantity');
        Route::resource('stock', 'StockController');

        Route::get('item/transfer', 'StockController@transfer')->name('item.transfer');
        Route::get('item/inventory_search/{id}', 'StockController@inventoryProducts')->name('item.inventory_search');
        // Route::get('item/get_inventory_detail/{id}', 'StockController@getInventoryDetail')->name('item.get_inventory_detail');
        Route::get('item/get_inventory_detail/{purchasePrice}/{salePrice}/{productId}', 'StockController@getInventoryDetail')->name('item.get_inventory_detail');

        Route::post('inventory/item_transfer', 'StockController@itemTransfer')->name('inventory.item_transfer');

        Route::get('stock_transfers/transfers', 'StockController@stockTransfers')->name('stock_transfers.transfers');
        // Route::get('stock_transfers/print_preview/{id}/{command}', 'StockController@stockTransferInvoice')->name('stock_transfers.print_preview');
        Route::get('stock_transfers/print_preview/{id}/{command}', 'StockController@stockTransferReceipt')->name('stock_transfers.print_preview');
    }
);

// price list
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('price-list/index', 'PriceListController@index')->name('price-list.index');
        Route::resource('price-list', 'PriceListController');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        // Route::resource('grn-order', 'GrnController');
        // Route::post('grn/store', 'GrnController@store')->name('grn.store');
        // Route::get('grn/create/{cid}', 'GrnController@create')->name('grn.create');
        // Route::get('grnOrderEdit/OrderEdit/{id}', 'GrnEditController@edit')->name('grnOrderEdit.edit');
        // Route::get('grn/show/{id}', 'GrnController@showGrn')->name('grn.show');
        // Route::get('purchase-order/grn/{id}', 'GrnController@genarateGrn')->name('purchase-order.grn');
        Route::get('grn/index', 'GrnController@index')->name('grn.index');
        Route::get('grn/manual-create', 'GrnController@createManual')->name('grn.manual.create');
        Route::get('grn/lookup-invoice', 'GrnController@lookupInvoice')->middleware('auth')->name('grn.invoice.lookup');
        Route::resource('grn-order', 'GrnController');
        Route::post('grn/store', 'GrnController@store')->name('grn.store');
        Route::get('grn/create/{cid}', 'GrnController@genarateGrn')->name('grn.create');
        Route::get('grnOrderEdit/OrderEdit/{id}', 'GrnController@edit')->name('grnOrderEdit.OrderEdit');
        Route::post('grn/{id}/invoice-number', 'GrnController@updateInvoiceNumber')->middleware('auth')->name('grn.invoice-number');
        Route::get('grn/show/{id}', 'GrnController@showGrn')->name('grn.show');
        Route::get('purchase-orders/create-grn', 'GrnController@viewPurchaseOrders')->name('purchase-orders.create-grn');
        Route::get('purchase-order/grn/{id}', 'GrnController@genarateGrn')->name('purchase-order.grn');
        Route::delete('grn/destroy/{id}', 'GrnController@destroy')->name('grn.destroy');
        Route::post('grn/items', 'GrnController@items')->name('grn.items');
        Route::post('grn/update/{id}', 'GrnController@update')->name('grn.update');
        Route::get('grn/pdf/{id}', 'GrnController@pdf')->name('grn.pdf');
        Route::get('grn/print/{id}', 'GrnController@print')->name('grn.print');
        Route::post('/update-serial-warranty', 'GrnController@updateSerialWarranty')->name('serial.warranty');
        Route::get('/get-serial-warranty', 'GrnController@getSerialWarranty')->name('serial.warranty');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        // Compatibility URLs for the older GRN links.
        Route::get('purchases/index', 'GrnController@index')->name('purchases.index');
        Route::get('purchases', 'GrnController@index');
        Route::get('purchases/create', 'GrnController@createManual');
        Route::post('purchases/store', 'GrnController@store')->name('purchases.store');
        Route::post('purchases', 'GrnController@store');
        Route::get('purchases/create/{cid}', 'GrnController@genarateGrn')->name('purchases.create');
        Route::get('purchasesOrderEdit/OrderEdit/{id}', 'GrnController@edit')->name('purchasesOrderEdit.edit');
        Route::get('purchases/show/{id}', 'GrnController@showGrn')->name('purchases.show');
        Route::get('purchases/{id}/edit', 'GrnController@edit')->name('purchases.edit');
        Route::match(['put', 'patch'], 'purchases/{id}', 'GrnController@update')->name('purchases.update');
        Route::delete('purchases/{id}', 'GrnController@destroy')->name('purchases.destroy');
        Route::get('purchases/{id}', 'GrnController@showGrn');
    }
);


Route::resource('serviceBill', 'ServiceBillController')->middleware(
    [
        'auth',
        'XSS',
    ]
);

// ('advance payements',
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('advance_payement', 'AdvancePaymentController');

        Route::get('advance_payment/view/{id}', 'AdvancePaymentController@show')->name('advance_payment.view');

        Route::get('advance_payement/edit/{id}', 'AdvancePaymentController@edit')->name('advance_payement.edit');
        Route::post('advance_payement/update', 'AdvancePaymentController@update')->name('advance_payement.update');
        Route::get('advance_payement/purchase_details/{id}', 'AdvancePaymentController@purchaseDetails')->name('advance_payement.purchase_details');
        Route::get('advance_payement/return_details/{id}', 'AdvancePaymentController@returnDetails')->name('advance_payement.return_details');
        Route::get('advance_payement/return/{id}', 'AdvancePaymentController@voucherCreate')->name('advance_payement.return');

        Route::get('advance_payement/receipt/{id}/{command}', 'AdvancePaymentController@receipt')->name('advance_payement.receipt');
        Route::post('/advance-update-print-status/{id}', 'AdvancePaymentController@updatePrintStatus')->name('advance.update.print.status');
        Route::post('/advance-admin-update-print-status/{id}', 'AdvancePaymentController@adminUpdatePrintStatus')->name('advance.admin.update.print.status');
    }
);


// cheque print
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('cheque-print', 'ChequePrintController');
        Route::get('cheque-print/{id}/{command}', 'ChequePrintController@show');
        Route::get('cheque-print_no/get_cheque_number/{id}', 'ChequePrintController@getChequeNumber');
        Route::get('cheque-print_cancel/cancel/{id}', 'ChequePrintController@cancel')->name('cheque-print_cancel.cancel');

        Route::get('cheque-print_voucher/payment_voucher/{id}', 'ChequePrintController@paymentVoucher');

        Route::get('cheque-print_payment_voucher/get_voucher_details/{id}', 'ChequePrintController@getVoucherDetails');
    }
);


// petty cash
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('petty-cash', 'PettyCashController');
        Route::get('petty_cash_fund/add_fund', 'PettyCashController@addFundCreate')->name('petty_cash_fund.add_fund');
        Route::post('petty_cash_fund/update', 'PettyCashController@addFundUpdate');
        Route::get('petty_cash_fund/create_fund', 'PettyCashController@pettyCashFundIndex')->name('petty_cash_fund.create_fund');
        Route::post('petty_cash_fund/edit', 'PettyCashController@pettyCashFundEdit');
        Route::get('petty-cash/destroy/{id}', 'PettyCashController@destroy')->name('petty-cash.destroy');
    }
);




Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('document_charge_rate/index', 'DocumentChargeRateController@index')->name('document_charge_rate.index');
        Route::post('document_charge_rate/edit', 'DocumentChargeRateController@edit')->name('document_charge_rate.edit');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('card_payment_rate/index', 'CardPaymentRateController@index')->name('card_payment_rate.index');
        Route::post('card_payment_rate/edit', 'CardPaymentRateController@edit')->name('card_payment_rate.edit');
        Route::get('card_payment_rate/get_detail/{id}', 'CardPaymentRateController@getDetail')->name('card_payment_rate.get_detail');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('cheque_book/index', 'ChequeBookController@index')->name('cheque_book.index');
        Route::get('cheque_book/create', 'ChequeBookController@create')->name('cheque_book.create');
        Route::post('cheque_book/store', 'ChequeBookController@store')->name('cheque_book.store');
        Route::get('cheque_book/view/{id}', 'ChequeBookController@view')->name('cheque_book.view');
        Route::post('cheque_book/update', 'ChequeBookController@update')->name('cheque_book.update');
        Route::get('cheque_book/disable', 'ChequeBookController@disable')->name('cheque_book.disable');
        Route::get('cheque_book/delete', 'ChequeBookController@delete')->name('cheque_book.delete');
    }
);

// Receipts
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('custom_receipt', 'CustomReceiptController');
        Route::get('custom_receipt/edit/{id}', 'CustomReceiptController@edit')->name('custom_receipt.edit');
        Route::post('custom_receipt/update', 'CustomReceiptController@update')->name('custom_receipt.update');
        Route::get('custom_receipt/receipt/{id}/{command?}', 'CustomReceiptController@receipt');
    }
);

// Opening Cash Balance
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('opening_cash/index', 'OpeningCashBalanceController@index')->name('opening_cash.index');
        Route::post('opening_cash/update', 'OpeningCashBalanceController@update');
    }
);

//ACCOUNTING SYSTEM
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        //maintenance
        Route::get('account-section/index', 'AccountSectionController@index')->name('account_section.index');
        Route::get('account-section/create', 'AccountSectionController@create')->name('account_section.create');
        Route::post('account-section/store', 'AccountSectionController@store')->name('account_section.store');
        Route::get('account-section/show/{id}', 'AccountSectionController@show')->name('account_section.show');
        Route::get('account-section/edit/{id}', 'AccountSectionController@edit')->name('account_section.edit');

        Route::get('account-group/index', 'AccountGroupController@index')->name('account_group.index');
        Route::get('account-group/create', 'AccountGroupController@create')->name('account_group.create');
        Route::post('account-group/store', 'AccountGroupController@store')->name('account_group.store');
        Route::get('account-group/show/{id}', 'AccountGroupController@show')->name('account_group.show');
        Route::get('account-group/edit/{id}', 'AccountGroupController@edit')->name('account_group.edit');
        Route::post('account-group/update/{id}', 'AccountGroupController@update')->name('account_group.update');

        Route::get('account_types/index', 'AccountTypeController@index')->name('account_types.index');
        Route::get('chartofaccount/index', 'ChartOfAccountsController@index')->name('chartofaccount.index');
        Route::get('chartofaccount/create', 'ChartOfAccountsController@create')->name('chartofaccount.create');
        Route::get('chartofaccount/get_detail_type', 'ChartOfAccountsController@getDetailType')->name('chartofaccount.get_detail_type');
        Route::get('chartofaccount/get_product_categories', 'ChartOfAccountsController@getProductCategories')->name('chartofaccount.get_product_categories');
        Route::post('chartofaccount/store', 'ChartOfAccountsController@store')->name('chartofaccount.store');
        Route::get('chartofaccount/edit/{id}', 'ChartOfAccountsController@edit')->name('chartofaccount.edit');
        Route::post('chartofaccount/update/{id}', 'ChartOfAccountsController@update')->name('chartofaccount.update');

        Route::get('chart-of-account/index', 'ChartOfAccountController@index')->name('chart_of_account.index');
        Route::get('chart-of-account/create', 'ChartOfAccountController@create')->name('chart_of_account.create');
        Route::post('chart-of-account/store', 'ChartOfAccountController@store')->name('chart_of_account.store');
        Route::get('chart-of-account/show/{id}', 'ChartOfAccountController@show')->name('chart_of_account.show');
        Route::get('chart-of-account/edit/{id}', 'ChartOfAccountController@edit')->name('chart_of_account.edit');
        Route::post('chart-of-account/update/{id}', 'ChartOfAccountController@update')->name('chart_of_account.update');

        Route::get('account-transaction/index', 'AccountTransactionController@index')->name('account_transaction.index');
        Route::get('account-transaction/search/{dates}', 'AccountTransactionController@search')->name('account_transaction.search');

        Route::get('account-transaction/create', 'AccountTransactionController@create')->name('account_transaction.create');
        Route::post('account-transaction/store', 'AccountTransactionController@store')->name('account_transaction.store');
        Route::get('account-transaction/show/{id}', 'AccountTransactionController@show')->name('account_transaction.show');
        Route::get('account-transaction/edit/{id}', 'AccountTransactionController@edit')->name('account_transaction.edit');
        Route::post('account-transaction/update/{id}', 'AccountTransactionController@update')->name('account_transaction.update');

        //prime entry books
        Route::get('sales-journal/index', 'PrimeEntryBookController@salesJournalIndex')->name('sales_journal.index');
        Route::post('sales-journal/search', 'PrimeEntryBookController@salesJournalSearch')->name('sales_journal.search');

        Route::get('sales-return-journal/index', 'PrimeEntryBookController@salesReturnJournalIndex')->name('sales_return_journal.index');
        Route::post('sales-return-journal/search', 'PrimeEntryBookController@salesReturnJournalSearch')->name('sales_return_journal.search');

        Route::get('purchase-journal/index', 'PrimeEntryBookController@purchaseJournalIndex')->name('purchase_journal.index');
        Route::post('purchase-journal/search', 'PrimeEntryBookController@purchaseJournalSearch')->name('purchase_journal.search');

        Route::get('purchase-return-journal/index', 'PrimeEntryBookController@purchaseReturnJournalIndex')->name('purchase_return_journal.index');
        Route::post('purchase-return-journal/search', 'PrimeEntryBookController@purchaseReturnJournalSearch')->name('purchase_return_journal.search');

        Route::get('cash-book/index', 'PrimeEntryBookController@cashBookIndex')->name('cash_book.index');
        Route::post('cash-book/search', 'PrimeEntryBookController@cashBookSearch')->name('cash_book.search');

        Route::get('daily-cash-book/index', 'PrimeEntryBookController@dailyCashBookIndex')->name('daily_cash_book.index');
        Route::post('daily-cash-book/search', 'PrimeEntryBookController@dailyCashBookSearch')->name('daily_cash_book.search');

        Route::get('bank-deposits/index', 'BankDepositsController@index')->name('bank-deposits.index');
        Route::post('bank-deposits/search', 'BankDepositsController@search')->name('bank-deposits.search');
        Route::post('bank-deposits/store', 'BankDepositsController@store')->name('bank-deposits.store');
        Route::get('bank-deposits/details', 'BankDepositsController@details')->name('bank-deposits.details');

        Route::get('bank-book/index', 'PrimeEntryBookController@bankBookIndex')->name('bank_book.index');
        Route::post('bank-book/search', 'PrimeEntryBookController@bankBookSearch')->name('bank_book.search');

        Route::get('petty-cash-book/index', 'PrimeEntryBookController@pettyCashBookIndex')->name('petty_cash_book.index');
        Route::post('petty-cash-book/search', 'PrimeEntryBookController@pettyCashBookSearch')->name('petty_cash_book.search');

        Route::get('general-journal/index', 'PrimeEntryBookController@generalJournalIndex')->name('general_journal.index');
        Route::get('general-journal/create', 'PrimeEntryBookController@generalJournalCreate')->name('general_journal.create');
        Route::post('general-journal/store', 'PrimeEntryBookController@generalJournalStore')->name('general_journal.store');
        Route::get('general-journal/edit/{id}', 'PrimeEntryBookController@generalJournalEdit')->name('general_journal.edit');
        Route::get('general-journal/show/{id}', 'PrimeEntryBookController@generalJournalShow')->name('general_journal.show');
        Route::post('general-journal/update', 'PrimeEntryBookController@generalJournalUpdate')->name('general_journal.update');

        Route::get('general_journal/inventory_search/{id}', 'PrimeEntryBookController@inventoryProducts');
        Route::get('general_journal_detail/get_inventory_details/{id}/{purchasePrice}/{salePrice}', 'PrimeEntryBookController@getInventoryDetail');
        Route::post('general_journal/adjust_stock', 'PrimeEntryBookController@adjustStock');
        Route::post('stock_adjustment/approve/{id}', 'PrimeEntryBookController@approveStockAdjustment')->name('stock_adjustment.approve');
        Route::post('stock_adjustment/reject/{id}', 'PrimeEntryBookController@rejectStockAdjustment')->name('stock_adjustment.reject');

        //ledger accounts
        Route::get('ledger-account/index', 'AccountSummaryController@ledgerAccountIndex')->name('ledger_account.index');
        Route::post('ledger-account/search', 'AccountSummaryController@ledgerAccountSearch')->name('ledger_account.search');

        //control accounts
        Route::get('control-account/index', 'AccountSummaryController@controlAccountIndex')->name('control_account.index');
        Route::post('control-account/search', 'AccountSummaryController@controlAccountSearch')->name('control_account.search');

        //bank rec statement
        Route::get('bank-rec/index', 'AccountSummaryController@bankRecIndex')->name('bank_rec.index');
        Route::get('bank-rec/create', 'AccountSummaryController@bankRecCreate')->name('bank_rec.create');
        Route::post('bank-rec/search', 'AccountSummaryController@bankRecSearch')->name('bank_rec.search');
        Route::post('bank-rec/store_entry', 'AccountSummaryController@storeEntry')->name('bank-rec.store_entry');
        Route::get('bank-rec/statement/{recId}', 'AccountSummaryController@bankRecStatement')->name('bank_rec.statement');
        Route::post('bank-rec/statement_update', 'AccountSummaryController@bankRecStatementUpdate')->name('bank_rec.statement_update');
        Route::get('bank-rec/destroy/{id}', 'AccountSummaryController@destroyBankRec')->name('bank_rec.destroy');

        // Route::get('bank-rec/entries', 'AccountSummaryController@bankRecEntriesIndex')->name('bank_rec.entries');
        Route::post('bank-rec/entries_search', 'AccountSummaryController@bankRecEntriesSearch')->name('bank_rec.entries_search');

        // Route::get('bank-rec/cleared_entries', 'AccountSummaryController@bankRecClearedEntriesIndex')->name('bank_rec.cleared_entries');
        // Route::post('bank-rec/cleared_entries_search', 'AccountSummaryController@bankRecClearedEntriesSearch')->name('bank_rec.cleared_entries_search');

        // Route::get('bank-rec/uncleared_entries', 'AccountSummaryController@bankRecUnclearedEntriesIndex')->name('bank_rec.uncleared_entries');
        // Route::post('bank-rec/uncleared_entries_search', 'AccountSummaryController@bankRecUnclearedEntriesSearch')->name('bank_rec.uncleared_entries_search');

        // Route::get('bank-rec/check_entry/{accId}', 'AccountSummaryController@checkEntry');
        // Route::get('bank-rec/uncheck_entry/{accId}', 'AccountSummaryController@uncheckEntry');


        //trial balance
        Route::get('trial-balance/index', 'AccountSummaryController@trialBalanceIndex')->name('trial_balance.index');
        Route::post('trial-balance/search', 'AccountSummaryController@trialBalanceSearch')->name('trial_balance.search');

        //financial statements
        Route::get('profit-loss/index', 'StatementController@profitLossIndex')->name('profit_loss_summary.index');
        Route::post('profit-loss/search', 'StatementController@profitLossSearch')->name('profit_loss_summary.search');

        Route::get('balance-sheet/index', 'StatementController@balanceSheetIndex')->name('balance_sheet_summary.index');
        Route::post('balance-sheet/search', 'StatementController@balanceSheetSearch')->name('balance_sheet_summary.search');

        Route::get('balance_sheet/show_detail/{accIds}/{date}/{balanceType}', 'StatementController@showDetails')->name('balance_sheet.show_detail');
        Route::get('profit_loss/show_detail/{accIds}/{startDate}/{endDate}/{balanceType}', 'StatementController@showPnLDetails')->name('profit_loss.show_detail');

        Route::get('cash-flow/index', 'StatementController@cashFlowIndex')->name('cash_flow_summary.index');
        Route::post('cash-flow/search', 'StatementController@cashFlowSearch')->name('cash_flow_summary.search');

        Route::get('changes-in-equity/index', 'StatementController@changesInEquityIndex')->name('changes_in_equity_summary.index');
        Route::post('changes-in-equity/search', 'StatementController@changesInEquitySearch')->name('changes_in_equity_summary.search');

        //source documents
        Route::get('credit-note', 'CreditNoteController@index')->name('credit.note');
        Route::get('custom-credit-note', 'CreditNoteController@customCreate')->name('invoice.custom.credit.note');
        Route::post('custom-credit-note', 'CreditNoteController@customStore')->name('invoice.custom.credit.note');
        Route::get('credit-note/invoice/{id}', 'CreditNoteController@getinvoice');
        Route::get('invoice/{id}/credit-note', 'CreditNoteController@create')->name('invoice.credit.note');
        Route::post('invoice/{id}/credit-note', 'CreditNoteController@store')->name('invoice.credit.note');
        Route::get('invoice/{id}/credit-note/edit/{cn_id}', 'CreditNoteController@edit')->name('invoice.edit.credit.note');
        Route::put('invoice/{id}/credit-note/edit/{cn_id}', 'CreditNoteController@update')->name('invoice.edit.credit.note');
        Route::delete('invoice/{id}/credit-note/delete/{cn_id}', 'CreditNoteController@destroy')->name('invoice.delete.credit.note');
        Route::get('creditNote/print/{id}/{command}', 'CreditNoteController@printCreditNote')->name('creditNote.print');

        Route::get('debit-note', 'DebitNoteController@index')->name('debit.note');
        Route::get('custom-debit-note', 'DebitNoteController@customCreate')->name('bill.custom.debit.note');
        Route::post('custom-debit-note', 'DebitNoteController@customStore')->name('bill.custom.debit.note');
        Route::get('debit-note/bill', 'DebitNoteController@getbill')->name('bill.get');
        Route::get('bill/{id}/debit-note', 'DebitNoteController@create')->name('bill.debit.note');
        Route::post('bill/{id}/debit-note', 'DebitNoteController@store')->name('bill.debit.note');
        Route::get('bill/{id}/debit-note/edit/{cn_id}', 'DebitNoteController@edit')->name('bill.edit.debit.note');
        Route::put('bill/{id}/debit-note/edit/{cn_id}', 'DebitNoteController@update')->name('bill.edit.debit.note');
        Route::delete('bill/{id}/debit-note/delete/{cn_id}', 'DebitNoteController@destroy')->name('bill.delete.debit.note');
        Route::get('debitNote/print/{id}/{command}', 'DebitNoteController@printDebitNote')->name('debitNote.print');
    }
);

Route::resource('payment_voucher', 'PaymentVoucheController')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('payment_voucher/print/{id}', 'PaymentVoucheController@print')->name('payment_voucher.print');
Route::get('payment_voucher_petty_cash/get_petty_cash_fund', 'PaymentVoucheController@getPettyCashFund');

// ('item returns',
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('item_return/supplier-filter-data', 'ItemReturnController@supplierFilterData')->name('item_return.supplier_filter_data');
        Route::resource('item_return', 'ItemReturnController');
        Route::get('item_return/create', 'ItemReturnController@create')->name('item_return.create');
        Route::get('item_return/invoice_search/{id}', 'ItemReturnController@invoiceSearch')->name('item_return.invoice_search');
        Route::post('item_return/store', 'ItemReturnController@store')->name('item_return.store');
        Route::get('item_return/edit/{id}', 'ItemReturnController@edit')->name('item_return.edit');
        Route::post('item_return/update', 'ItemReturnController@update')->name('item_return.update');
        Route::get('item_return/destroy/{id}', 'ItemReturnController@destroy')->name('item_return.destroy');

        Route::post('item_return/search', 'ItemReturnController@search')->name('item_return.search');
        Route::post('item_return/create_item_for_item', 'ItemReturnController@createItemForItem')->name('item_return.create_item_for_item');
        Route::post('item_return/store_item_for_item', 'ItemReturnController@storeItemForItem')->name('item_return.store_item_for_item');
        Route::post('item_return/approve/{id}', 'ItemReturnController@approve')->name('item_return.approve');
        Route::post('item_return/reject/{id}', 'ItemReturnController@reject')->name('item_return.reject');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('fine_rate/index', 'FineRateController@index')->name('fine_rate.index');
        Route::get('fine_rate/edit/{id}', 'FineRateController@edit')->name('fine_rate.edit');
        Route::resource('fine_rate', 'FineRateController');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('authorization_requests/index', 'AuthenticationConfirmationController@index')->name('authorization_requests.index');
        Route::get('authorization_requests/get_count', 'AuthenticationConfirmationController@getCount')->name('authorization_requests.get_count');
        Route::post('waitting_sale/create', 'AuthenticationConfirmationController@waittingSaleCreate')->name('waitting_sale.create');
        Route::get('waitting_sale/approve/{id}', 'AuthenticationConfirmationController@waittingSaleApprove')->name('waitting_sale.approve');
        Route::post('waitting_sale/reject', 'AuthenticationConfirmationController@waittingSaleReject')->name('waitting_sale.reject');

        Route::post('waitting_installment/create', 'AuthenticationConfirmationController@waittingInstallmentCreate')->name('waitting_installment.create');
        Route::get('waitting_installment/approve/{id}', 'AuthenticationConfirmationController@waittingInstallmentApprove')->name('waitting_installment.approve');
        Route::post('waitting_installment/reject', 'AuthenticationConfirmationController@waittingInstallmentReject')->name('waitting_installment.reject');

        Route::post('waitting_loan_installment/create', 'AuthenticationConfirmationController@waittingLoanInstallmentCreate')->name('waitting_loan_installment.create');
        Route::get('waitting_loan_installment/approve/{id}', 'AuthenticationConfirmationController@waittingLoanInstallmentApprove')->name('waitting_loan_installment.approve');
        Route::post('waitting_loan_installment/reject', 'AuthenticationConfirmationController@waittingLoanInstallmentReject')->name('waitting_loan_installment.reject');

        Route::post('waitting_pawning_installment/create', 'AuthenticationConfirmationController@waittingPawningInstallmentCreate')->name('waitting_pawning_installment.create');
        Route::get('waitting_pawning_installment/approve/{id}', 'AuthenticationConfirmationController@waittingPawningInstallmentApprove')->name('waitting_pawning_installment.approve');
        Route::post('waitting_pawning_installment/reject', 'AuthenticationConfirmationController@waittingPawningInstallmentReject')->name('waitting_pawning_installment.reject');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('fine_control/index', 'OverDueManageController@index')->name('fine_control.index');
        Route::post('fine_control/update', 'OverDueManageController@update')->name('fine_control.update');
        Route::post('fine_control/update_child', 'OverDueManageController@updateChild')->name('fine_control.update_child');
        Route::get('fine_control/add_center', 'OverDueManageController@addCenter')->name('fine_control.add_center');
        Route::post('fine_control/create_center', 'OverDueManageController@createCenter')->name('fine_control.create_center');
        Route::get('fine_control/edit_center/{id}', 'OverDueManageController@editCenter')->name('fine_control.edit_center');
        Route::post('fine_control/update_center', 'OverDueManageController@updateCenter')->name('fine_control.update_center');
    }
);

Route::get('apply_fine', 'OverDueManageController@applyFine');
Route::get('remove_fine', 'OverDueManageController@removeFine');

// PACMA International: Forms, Sales Summary & GRN Head Office Approval
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('pacma-forms/loan-requisition/{id?}', 'PacmaFormsController@loanRequisition')->name('pacma.forms.loan_requisition');
        Route::get('pacma-forms/invoice-loan-requisition/{invoiceId}', 'PacmaFormsController@invoiceLoanRequisition')->name('pacma.forms.invoice_loan_requisition');
        Route::get('pacma-forms/application-form/{id?}', 'PacmaFormsController@applicationForm')->name('pacma.forms.application_form');
        Route::get('pacma-forms/customer-application/{customerId}', 'PacmaFormsController@customerApplicationForm')->name('pacma.forms.customer_application');
        Route::get('pacma-forms/guarantor-declaration/{id?}', 'PacmaFormsController@guarantorDeclaration')->name('pacma.forms.guarantor_declaration');
        Route::get('pacma-forms/guarantee-indemnity/{id?}', 'PacmaFormsController@guaranteeIndemnity')->name('pacma.forms.guarantee_indemnity');

        Route::get('report/pacma-summary', 'PacmaSummaryController@index')->name('pacma.summary.index');
        Route::post('item_return/supplier-store', 'ItemReturnController@supplierReturnStore')->name('item_return.supplier_store');

        Route::post('grn/{id}/approve-ho', 'GrnController@approveHeadOffice')->name('grn.approve_ho');
        Route::post('grn/{id}/reject-ho', 'GrnController@rejectHeadOffice')->name('grn.reject_ho');
    }
);

Route::get('/storage/{path}', function ($path) {
    $basePath = realpath(storage_path('app/public'));
    $fullPath = realpath($basePath . DIRECTORY_SEPARATOR . $path);
    if (!$basePath || !$fullPath || strpos($fullPath, $basePath) !== 0 || !is_file($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*');
