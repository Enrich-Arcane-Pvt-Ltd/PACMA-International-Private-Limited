# PACMA International Private Limited — Hire Purchase, Sales & Accounting ERP

A full-featured web-based Enterprise Resource Planning (ERP) and Hire Purchase Management System built for **PACMA International Private Limited** (*No: 61, K C De Silvapuraya, Thimbirigaskatuwa*).

The platform streamlines **Cash & Hire Sales**, **Customer & Guarantor Management**, **Purchase Orders & Inventory (GRN / SRN)**, **Banking & Double-Entry Accounting**, **Branch Operations**, **SMS Alerts**, and **Automated Printable Legal/Application Forms**.

---

## Key Features

- **Cash & Hire Sales Management**
  - Direct Cash Sales and Hire Purchase Sales with configurable interest rates, down payments, document charges, monthly installments, and due dates.
  - Support for up to **3 Guarantors** and **2 Witnesses** per Hire Sale, plus per-sale guarantor relationship duration tracking.
  - Sales Return Notes (SRN), Advance Payments, and Receipt / Invoice printing.
- **Automated PACMA Printable Forms**
  - **Customer Application Form** (`pacma_forms/application_form`) — auto-filled from Customer & Customer Details records (Masters → Customers).
  - **Loan Requisition Form** (`pacma_forms/loan_requisition`) — auto-filled for Hire Sales & Hire Sale Invoices with itemized products, model/serial numbers, Rs. amounts, guarantors, and sales promoter.
  - **Guarantor's Declaration Form** (`pacma_forms/guarantor_declaration`) — bilingual Sinhala/English declaration auto-filled for 1st & 2nd Guarantors, selectable from Hire Sales Contracts.
  - **Guarantee & Indemnity Form** (`pacma_forms/guarantee_indemnity`) — contract-linked legal indemnity form.
- **Inventory & Procurement**
  - Product catalog with Categories, Sub-categories, Brands, Price Lists, Serial/Warranty tracking.
  - Purchase Orders (PO), Goods Received Notes (GRN) with Head Office approval workflow, Stock Transfers, and Warehouses.
- **Banking & Accounting System**
  - Chart of Accounts, Account Sections/Groups, Control Accounts, Journal Entries, General Ledger, Trial Balance, and Bank Reconciliation.
  - Bank Accounts, Bank Deposits, Bank Transfers, Cheque Books & Cheque Printing, Petty Cash, and Vouchers.
- **Role-Based Access Control & Notifications**
  - Granular permissions powered by `spatie/laravel-permission`.
  - Automated SMS gateway integration (`app/SMS.php`) for customer/payment notifications.

---

## Languages & Technologies

### Backend
- **Language:** PHP (`^7.2.5`, tested on **PHP 7.4**)
- **Framework:** Laravel Framework (`^7.0`)
- **Database:** MySQL / MariaDB
- **Key Composer Packages:**
  - `spatie/laravel-permission` (`^3.13`) — Roles & Permissions
  - `laravelcollective/html` (`^6.1`) — Blade Form & HTML helpers
  - `laravel/ui` (`^2.0`) — Authentication scaffolding
  - `doctrine/dbal` (`^2.10`) — Database schema & migration tooling
  - `intervention/image` (`^2.7`) — Image processing
  - `guzzlehttp/guzzle` (`^6.3`) — HTTP client for SMS & external APIs
  - `paypal/rest-api-sdk-php` (`^1.14`) & `stripe/stripe-php` (`^7.36`) — Payment gateways

### Frontend
- **Templating & Markup:** Laravel Blade, HTML5, CSS3 (`public/css/pacma-modern.css`)
- **CSS Framework:** Bootstrap (`^4.0.0`), Sass (`^1.15.2`)
- **JavaScript & Libraries:** JavaScript (ES6+), jQuery (`^3.2`), Popper.js (`^1.12`), Axios (`^0.19`), Lodash (`^4.17.13`)
- **Asset Bundler:** Laravel Mix (`^5.0.1`) / Webpack



## Project File Structure

```text
PACMA-International-Private-Limited/
├── app/
│   ├── Http/
│   │   ├── Controllers/           # Application controllers
│   │   │   ├── CustomerController.php
│   │   │   ├── SaleController.php
│   │   │   ├── InvoiceController.php
│   │   │   ├── PacmaFormsController.php
│   │   │   ├── PacmaSummaryController.php
│   │   │   ├── GrnController.php / PurchaseOrderController.php
│   │   │   └── ...                # Accounting, Banking, Masters, Reports
│   │   └── Middleware/            # Auth, XSS, IdleTimeout, etc.
│   ├── Customer.php               # Customer & Guarantor Eloquent model
│   ├── CustomerDetail.php         # Extended applicant/guarantor details
│   ├── Sale.php / SaleItem.php    # Cash & Hire Sales models
│   ├── Invoice.php                # Invoice model
│   ├── Product.php / Grn.php      # Inventory & Procurement models
│   ├── SMS.php                    # SMS gateway helper
│   └── Utility.php                # Shared accounting/app utilities
├── bootstrap/                     # Framework bootstrap & cache
├── config/                        # App, auth, database, mail, permissions configs
├── database/
│   ├── factories/                 # Model factories
│   ├── migrations/                # Database schema migrations
│   └── seeds/                     # Database seeders (Users, Plans)
├── public/
│   ├── assets/                    # Static assets & reference documents
│   ├── css/                       # Compiled CSS & pacma-modern.css
│   ├── js/                        # Compiled JS & vendor scripts
│   └── storage -> ../storage/app/public
├── resources/
│   ├── js/ & sass/                # Frontend source assets
│   └── views/
│       ├── customer/              # Customer create, edit, index, view
│       ├── sale/                  # Cash & Hire Sales create, edit, view
│       ├── invoice/               # Invoices & receipt print views
│       ├── hire_purchase_contract/# Hire Sales Contracts & form dropdowns
│       ├── pacma_forms/           # Application, Loan Requisition, Guarantor & Indemnity forms
│       ├── grn/ & purchaseOrder/  # Inventory & PO views
│       ├── layouts/               # Master layout, sidebar, header
│       └── ...                    # Accounting, Banking, Masters, Reports
├── routes/
│   └── web.php                    # Web & resource route definitions
├── storage/                       # Logs, framework cache, uploaded files
├── tests/                         # PHPUnit feature & unit tests
├── .env.example                   # Environment variable template
├── artisan                        # Laravel CLI entry point
├── composer.json                  # PHP dependencies
├── package.json                   # Node/Frontend dependencies
└── webpack.mix.js                 # Laravel Mix build configuration
```

---

## Prerequisites

- **PHP** `7.2.5` – `7.4.x` (with `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `gd` / `fileinfo` extensions)
- **Composer** `2.x`
- **MySQL** `5.7+` or **MariaDB** `10.3+` (e.g., via XAMPP)
- **Node.js & npm** (optional, only needed if recompiling frontend assets)

---

## Installation & Setup Guide

### 1. Clone the Repository
```bash
git clone <your-repository-url>
cd PACMA-International-Private-Limited
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Configure Environment Variables
Copy `.env.example` to `.env` and update your database and application settings:
```bash
cp .env.example .env
```
Edit `.env`:
```env
APP_NAME="PACMA International Private Limited"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8001

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pacma_international
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Set Up the Database
1. Create an empty MySQL database named `pacma_international` (via phpMyAdmin or MySQL CLI):
   ```sql
   CREATE DATABASE pacma_international CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Run migrations (or import your existing SQL dump if migrating production data):
   ```bash
   php artisan migrate
   ```
3. *(Optional, for fresh installs)* Seed initial users and plans:
   ```bash
   php artisan db:seed
   ```

### 6. Link Public Storage
Create the symbolic link from `public/storage` to `storage/app/public` so logos, customer photos, and product images load properly:
```bash
php artisan storage:link
```

### 7. (Optional) Compile Frontend Assets
If you modify files in `resources/sass` or `resources/js`:
```bash
npm install
npm run dev
```

### 8. Clear Caches & Run the Server
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan serve --port=8001
```
Open **`http://127.0.0.1:8001`** in your browser (or configure an Apache VirtualHost in XAMPP pointing to the `public/` directory).

---

## Useful Artisan Commands

```bash
# Clear all application caches
php artisan optimize:clear

# Clear compiled Blade views after template edits
php artisan view:clear

# Run PHPUnit tests
./vendor/bin/phpunit
```
