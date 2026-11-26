@extends('layouts.admin')

@section('page-title')
    {{ __('Customer Detail') }}
@endsection

@section('content')
    <style>
        #customer_type {
            color: #fc544b;
            font-weight: bold;
        }
    </style>

    <section class="section">
        {{-- <div class="section-header">
            <h1>{{__('Customer Detail')}}</h1>

    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item active"><a href="{{ route('customer.index') }}">{{ __('Customer') }}</a></div>
        <div class="breadcrumb-item">{{$customer->name}}</div>
    </div>
    </div> --}}

        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Customer Detail') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('customer.index') }}">{{ __('Customer') }}</a></div>
                                    <div class="breadcrumb-item">{{ $customer->name }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                @php
                                    if ($customer['type'] == 0) {
                                        $customerType = 'Customer';
                                    } elseif ($customer['type'] == 1) {
                                        $customerType = 'Guarantor';
                                    } elseif ($customer['type'] == 2) {
                                        $customerType = 'Customer & Guarantor';
                                    }
                                @endphp

                                <h4>Manage Customer Detail <br><small id="customer_type">{{ $customerType }}</small></h4>

                                <div class="card-header-action">

                                    @can('edit customer')
                                        <a href="#" class="btn btn-primary mr-1" data-size="xl"
                                            data-url="{{ route('customer.edit', $customer['id']) }}" data-ajax-popup="true"
                                            data-title="{{ __('Edit Customer') }}" data-toggle="tooltip"
                                            data-original-title="{{ __('Edit') }}">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    @endcan

                                    @if ($customer->status == 1)
                                        @can('block customer')
                                            <button class="btn btn-danger mr-1 block" data-original-title="{{ __('Block') }}"
                                                data-toggle="tooltip" value="{{ $customer->id }}">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        @endcan
                                    @else
                                        @can('block customer')
                                            <button class="btn btn-success mr-1 unblock" data-toggle="tooltip"
                                                data-original-title="{{ __('Unblock') }}" value="{{ $customer->id }}">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        @endcan
                                    @endif

                                    @if (Gate::check('delete customer') || Auth::user()->type == 'company' || Auth::user()->type == 'super admin')
                                        <button class="btn btn-danger mr-1 delete-customer"
                                            data-id="{{ $customer->id }}"
                                            data-name="{{ $customer->name }}"
                                            data-toggle="tooltip"
                                            data-original-title="{{ __('Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $customer->id }}" action="{{ route('customer.destroy', $customer->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row custmer-detail-wrap">
                                <div class="col-md-4 mb-4">
                                    <h4 class="sub-title">{{ __('Personal Info') }}</h4>

                                    @php
                                        $custPhoto = !empty($customer->details->customer_photo) ? $customer->details->customer_photo : null;
                                        $custPhotoUrl = asset('assets/img/avatar/user.png');
                                        if (!empty($custPhoto)) {
                                            if (file_exists(public_path($custPhoto))) {
                                                $custPhotoUrl = asset($custPhoto);
                                            } elseif (file_exists(public_path('storage/' . $custPhoto))) {
                                                $custPhotoUrl = asset('storage/' . $custPhoto);
                                            } elseif (file_exists(storage_path('app/public/' . $custPhoto))) {
                                                $custPhotoUrl = asset('storage/' . $custPhoto);
                                            } elseif (strpos($custPhoto, 'uploads/') === 0) {
                                                $custPhotoUrl = asset('storage/' . $custPhoto);
                                            } else {
                                                $custPhotoUrl = asset(Storage::url($custPhoto));
                                            }
                                        }
                                    @endphp
                                    <div class="mb-3">
                                        <img src="{{ $custPhotoUrl }}" class="img-thumbnail rounded" width="90" height="90" style="object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('assets/img/avatar/user.png') }}'">
                                    </div>

                                    @php
                                        if ($customer['title'] == 0) {
                                            $customerTitle = 'Mr.';
                                        } elseif ($customer['title'] == 1) {
                                            $customerTitle = 'Mrs.';
                                        } elseif ($customer['title'] == 2) {
                                            $customerTitle = 'Miss.';
                                        }

                                        if ($customer['resident'] == 0) {
                                            $resident = 'Own';
                                        } elseif ($customer['resident'] == 1) {
                                            $resident = "Parent's";
                                        } elseif ($customer['resident'] == 2) {
                                            $resident = 'Rented';
                                        } elseif ($customer['resident'] == 3) {
                                            $resident = 'Others';
                                        }
                                    @endphp

                                    <span><b>Preferred Name:
                                        </b>{{ $customerTitle . ' ' . $customer['name'] ?? '' }}</span><br>
                                    <span><b>Full Name:
                                        </b>{{ $customerTitle . ' ' . $customer['full_name'] ?? '' }}</span><br>
                                    <span><b>NIC No: </b>{{ $customer['nic_no'] ?? '' }}</span><br>
                                    <span><b>Date of Birth: </b>{{ $customer['dob'] ?? '' }}</span><br>
                                    <span><b>Email: </b>{{ $customer['email'] ?? '' }}</span><br>
                                    <span><b>Phone (Home): </b>{{ $customer['phone'] ?? '' }}</span><br>
                                    <span><b>Phone (Mobile): </b>{{ $customer['phone_mobile'] ?? '' }}</span><br>
                                    <span><b>Address: </b>{{ $customer['house_no'] ?? '' }}
                                        {{ $customer['address'] ?? '' }}</span><br>
                                    <span><b>Address (as Per NIC): </b>{{ $customer['address_nic'] ?? '' }}</span><br>
                                    <span><b>Resident: </b>{{ $resident }}</span><br>
                                    <span><b>Names of children:
                                        </b>{{ $customer->details['children_names'] ?? 'N/A' }}</span><br>
                                    <span><b>Relationship to Hirer:
                                        </b>{{ $customer['relationship_to_hirer'] ?? 'N/A' }}</span><br>
                                    <span><b>Name of Business: </b>{{ $customer['business_name'] ?? 'N/A' }}</span><br>
                                    <span><b>Business Address:
                                        </b>{{ $customer->details['business_address'] ?? 'N/A' }}</span><br>
                                </div>

                                <div class="col-md-4 mb-4">
                                    <h4 class="sub-title">{{ __('Employment Info') }}</h4>


                                    <span><b>Name of Employment: </b>{{ $customer['employment_name'] ?? '' }}</span><br>
                                    <span><b>Number of Employment:
                                        </b>{{ $customer['employment_number'] ?? '' }}</span><br>
                                    {{-- <span><b>Place of Employment / Business:
                                    </b>{{ $customer['employment_place'] ?? '' }}</span><br> --}}
                                    <span><b>Job Title: </b>{{ $customer['job_title'] ?? '' }}</span><br><br>
                                    {{-- <span><b>Monthly Income / Salary:
                                    </b>{{ $customer['monthly_income'] ?? '' }}</span><br>
                                <span><b>Phone (Office): </b>{{ $customer['phone_office'] ?? '' }}</span><br>


                                <h4 class="sub-title">{{ __('Vehicle Details') }}</h4>
                                <span><b>Vehicle Model: </b>{{ $customer->details['vehicle_model'] ?? 'N/A' }}</span><br>
                                <span><b>Vehicle Number: </b>{{ $customer->details['vehicle_number'] ?? 'N/A' }}</span><br>
                                <span><b>Finance for vehicle: </b>
                                    @if ($customer->details['vehicle_finance'] == 1)
                                        Yes
                                    @elseif ($customer->details['vehicle_finance'] == 0)
                                        No
                                    @else
                                        N/A
                                    @endif
                                </span>
                                <br>
                                @if ($customer->details['vehicle_finance'] == 1)
                                    <span><b>Finance Amount: </b>{{ $customer->details['vehicle_finance_amount'] ?? 'N/A' }}</span><br>
                                @endif

                                {{-- <span><b>Finance Amount: </b>{{ $customer->details['vehicle_finance_amount'] ?? 'N/A' }}</span><br> --}}
                                </div>

                                <div class="col-md-4">
                                    <h4 class="sub-title">{{ __('Billing Info') }}</h4>

                                    @php
                                        if ($customer['billing_title'] == 0) {
                                            $billingTitle = 'Mr.';
                                        } elseif ($customer['billing_title'] == 1) {
                                            $billingTitle = 'Mrs.';
                                        } elseif ($customer['billing_title'] == 2) {
                                            $billingTitle = 'Miss.';
                                        }
                                    @endphp

                                    <span><b>Billing Name:
                                        </b>{{ $billingTitle . ' ' . $customer['billing_name'] ?? '' }}</span><br>
                                    <span><b>Billing Address: </b>{{ $customer['billing_house_no'] ?? '' }}
                                        {{ $customer['billing_address'] ?? '' }}</span><br>
                                    <span><b>Billing Phone: </b>{{ $customer['billing_phone'] ?? '' }}</span><br>
                                    <span><b>Billing Email: </b>{{ $customer['billing_email'] ?? 'N/A' }}</span><br>

                                </div>
                            </div>

                            @php
                                $cd = $customer->details;
                                $genderText = 'N/A';
                                if (isset($cd->gender)) {
                                    if ($cd->gender == 1) $genderText = __('Male');
                                    elseif ($cd->gender == 2) $genderText = __('Female');
                                }
                                $svcYears = (int) ($cd->service_duration_years ?? 0);
                                $svcMonths = (int) ($cd->service_duration_months ?? 0);
                                $svcText = ($svcYears > 0 || $svcMonths > 0)
                                    ? trim(($svcYears > 0 ? $svcYears . ' ' . ($svcYears === 1 ? 'Year' : 'Years') : '') . ' ' . ($svcMonths > 0 ? $svcMonths . ' ' . ($svcMonths === 1 ? 'Month' : 'Months') : ''))
                                    : 'N/A';
                            @endphp
                            <h4 class="sub-title mt-4">{{ __('Application Form Additional Details') }}</h4>
                            <div class="row mb-3">
                                <div class="col-md-4 mb-3">
                                    <span><b>{{ __('Gender') }}: </b>{{ $genderText }}</span><br>
                                    <span><b>{{ __('Group No.') }}: </b>{{ $cd->group_no ?? 'N/A' }}</span><br>
                                    <span><b>{{ __('Resident Period (Years)') }}: </b>{{ $cd->resident_period ?? 'N/A' }}</span><br>
                                    <span><b>{{ __('Preferred Payment Date') }}: </b>{{ $cd->preferred_payment_date ?? 'N/A' }}</span><br>
                                    <span><b>{{ __('Service Duration') }}: </b>{{ $svcText }}</span><br>
                                    <span><b>{{ __('Office Telephone No.') }}: </b>{{ $cd->phone_office ?? 'N/A' }}</span><br>
                                    <span><b>{{ __('Monthly Income') }}: </b>{{ !empty($cd->monthly_income) ? number_format((float)$cd->monthly_income, 2) : 'N/A' }}</span><br>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <span><b>{{ __('Other Income') }}: </b>{{ $cd->any_other_income_details ?? 'N/A' }}</span><br>
                                    <span><b>{{ __('Other Liabilities Company') }}: </b>{{ $cd->other_loan_company ?? 'N/A' }}</span><br>
                                    <span><b>{{ __('Other Liabilities Amount') }}: </b>{{ !empty($cd->other_loan_amount) ? number_format((float)$cd->other_loan_amount, 2) : 'N/A' }}</span><br>
                                    <span><b>{{ __("Spouse's Name & Address") }}: </b>{{ $cd->spouse_name_address ?? 'N/A' }}</span><br>
                                    <span><b>{{ __("Spouse's Telephone No.") }}: </b>{{ $cd->spouse_phone ?? 'N/A' }}</span><br>
                                    <span><b>{{ __("Spouse's Occupation") }}: </b>{{ $cd->spouse_occupation ?? 'N/A' }}</span><br>
                                    <span><b>{{ __("Spouse's Office Address") }}: </b>{{ $cd->spouse_office_address ?? 'N/A' }}</span><br>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <span><b>{{ __("Spouse's Income") }}: </b>{{ $cd->spouse_income ?? 'N/A' }}</span><br>
                                    <span><b>{{ __("Parents' Name") }}: </b>{{ $cd->parents_name ?? 'N/A' }}</span><br>
                                    <span><b>{{ __("Parents' Address") }}: </b>{{ $cd->parents_address ?? 'N/A' }}</span><br>
                                    <span><b>{{ __("Relation's Name, Address & Phone Number") }}: </b>{{ $cd->relation_details ?? 'N/A' }}</span><br>
                                    <span><b>{{ __('Relation') }}: </b>{{ $cd->relation_type ?? 'N/A' }}</span><br>
                                    <span><b>{{ __('Route to Residence') }}: </b>{{ $cd->route_to_residence ?? 'N/A' }}</span><br>
                                </div>
                            </div>

                            <h4 class="sub-title mt-4">{{ __('Customer Summary') }}</h4>

                            <div class="row custmer-detail-wrap">
                                <div class="col-md-3">
                                    <div class="info">
                                        <strong>{{ __('Customer ID') }}</strong>
                                        <span>{{ AUth::user()->customerNumberFormat($customer['customer_id']) }}</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="info">
                                        <strong>{{ __('Date of Registered') }}</strong>
                                        <span>{{ \Auth::user()->dateFormat($customer['created_at']) }}</span>
                                    </div>
                                </div>

                                {{-- @php
                                $totalInvoiceSum = 0;
                                $totalDueBalance = 0;
                                $totalOverDues = 0;
                                foreach ($sales as $sale) {
                                    $totalInvoiceSum += $sale->net_amount;
                                    $totalDueBalance += $sale->current_due_amount;
                                    foreach ($sale->installments as $installment) {
                                        if($installment->status == 3){
                                            $totalOverDues += $installment->total_amount_to_pay;
                                        }
                                    }
                                }
                                $averageSale=($totalInvoiceSum!=0)?$totalInvoiceSum/count($sales):0;
                            @endphp
                            <div class="col-md-3">
                                <div class="info">
                                    <strong>{{__('No of Invoices')}}</strong>
                            <span>{{ count($sales) }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info">
                            <strong>{{__('Total Sum of Invoices')}}</strong>
                            <span>{{\Auth::user()->priceFormat($totalInvoiceSum)}}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info">
                            <strong>{{__('Average Sales')}}</strong>
                            <span>{{\Auth::user()->priceFormat($averageSale)}}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info font-style">
                            <strong>{{__('Balance')}}</strong>
                            <span>{{\Auth::user()->priceFormat($totalDueBalance)}}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info">
                            <strong>{{__('Overdue')}}</strong>
                            <span class="text-danger">{{\Auth::user()->priceFormat($totalOverDues)}}</span>
                        </div>
                    </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Sales') }}</h4>
                    </div>

                    <div class="card-body">
                        <div id="table-1_wrapper2" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">

                                        <table class="table table-flush" id="dataTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th> {{ __('Sale #') }}</th>
                                                    <th> {{ __('Invoice #') }}</th>
                                                    <th> {{ __('Issue Date') }}</th>
                                                    <th> {{ __('Due Date') }}</th>
                                                    <th> {{ __('Outstanding Balance') }} ({{ $currencySymbol }})</th>
                                                    <th class="text-center"> {{ __('Status') }}</th>
                                                    @if (Gate::check('view invoice'))
                                                        <th class="text-center"> {{ __('Action') }}</th>
                                                    @endif
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($sales as $sale)
                                                    <tr>
                                                        <td>{{ $sale->sale_id }}</td>
                                                        <td>
                                                            <a href="{{ route('invoice.show', $sale->invoice_id) }}"
                                                                class="btn btn-outline-primary">
                                                                {{ $sale->invoice->invoice_code }}
                                                            </a>
                                                        </td>
                                                        <td class="invoice_td">{{ $sale->date_of_sale }}</td>
                                                        <td class="invoice_td">{{ $sale->due_date ?? '-' }}</td>
                                                        <td class="invoice_td">
                                                            @if (!empty($sale->current_due_amount))
                                                                {{ number_format($sale->current_due_amount, 2) }}
                                                                {{-- {{ \Auth::user()->priceFormat($sale->current_due_amount) }} --}}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center invoice_td">
                                                            @if ($sale->status == 1)
                                                                <span class="badge badge-pill badge-success">Paid</span>
                                                            @elseif($sale->status == 2)
                                                                <span class="badge badge-pill badge-info">Partially
                                                                    Paid</span>
                                                            @else
                                                                <span class="badge badge-pill badge-danger">Overdue</span>
                                                            @endif
                                                        </td>

                                                        @if (Gate::check('view invoice'))
                                                            <td class="text-center">
                                                                @can('view invoice')
                                                                    <a href="{{ route('sale.view', $sale->id) }}"
                                                                        class="btn btn-info btn-action mr-1"
                                                                        data-toggle="tooltip"
                                                                        data-original-title="{{ __('Detail') }}">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endcan
                                                            </td>
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

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Loans') }}</h4>
                    </div>

                    <div class="card-body">
                        <div id="table-1_wrapper2" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">

                                        <table class="table table-flush" id="dataTable2">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th> {{ __('Laon #') }}</th>
                                                    <th> {{ __('Issue Date') }}</th>
                                                    <th> {{ __('Due Date') }}</th>
                                                    <th> {{ __('Loan Amount') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Outstanding Balance') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Time Period') }}</th>
                                                    <th> {{ __('Collection Period') }}</th>
                                                    <th class="text-center"> {{ __('Status') }}</th>
                                                    @if (Gate::check('view loan'))
                                                        <th class="text-center"> {{ __('Action') }}</th>
                                                    @endif
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($loans as $loan)
                                                    <tr>
                                                        <td>{{ \Auth::user()->loanNumberFormat($loan->id) }}</td>
                                                        <td class="invoice_td">{{ $loan->date }}</td>
                                                        <td class="invoice_td">{{ $loan->due_date ?? '-' }}</td>
                                                        <td class="font-style">
                                                            {{ number_format($loan->loan_amount, 2) }}
                                                            {{-- {{ \Auth::user()->priceFormat($loan->loan_amount) }}  --}}
                                                        </td>
                                                        <td class="font-style">
                                                            {{ number_format($loan->current_due_amount, 2) }}
                                                            {{-- {{ \Auth::user()->priceFormat($loan->current_due_amount) }} --}}
                                                        </td>
                                                        <td class="font-style">
                                                            @if ($loan->time_range == 1)
                                                                {{ $loan->daily_time_period }} {{ __('Days') }}
                                                            @elseif ($loan->time_range == 2 || $loan->time_range == 3)
                                                                {{ $loan->time_period }} {{ __('Months') }}
                                                            @endif
                                                        </td>
                                                        <td class="font-style">
                                                            @if ($loan->time_range == 1)
                                                                Daily
                                                            @elseif ($loan->time_range == 2)
                                                                Weekly
                                                            @elseif ($loan->time_range == 3)
                                                                Monthly
                                                            @endif
                                                        </td>
                                                        <td class="text-center invoice_td">
                                                            @if ($loan->status == 1)
                                                                <span class="badge badge-pill badge-warning">Pending</span>
                                                            @elseif($loan->status == 2)
                                                                <span class="badge badge-pill badge-info">Approved</span>
                                                            @elseif($loan->status == 3)
                                                                <span class="badge badge-pill badge-success">Closed</span>
                                                            @elseif($loan->status == 4)
                                                                <span class="badge badge-pill badge-warning">Checked</span>
                                                            @elseif($loan->status == 0)
                                                                <span class="badge badge-pill badge-danger">Rejected</span>
                                                            @endif
                                                        </td>
                                                        @if (Gate::check('view loan'))
                                                            <td class="text-center">
                                                                @can('view loan')
                                                                    <a href="{{ route('loan.view', $loan->id) }}"
                                                                        class="btn btn-info btn-action mr-1"
                                                                        data-toggle="tooltip"
                                                                        data-original-title="{{ __('Detail') }}">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endcan
                                                            </td>
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

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Pawnings') }}</h4>
                    </div>

                    <div class="card-body">
                        <div id="table-1_wrapper2" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">

                                        <table class="table table-flush" id="dataTable3">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th> {{ __('Pawning #') }}</th>
                                                    <th> {{ __('Issue Date') }}</th>
                                                    <th> {{ __('Due Date') }}</th>
                                                    <th> {{ __('Total Payment') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Outstanding Balance') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Time Period') }}</th>
                                                    <th> {{ __('Collection Period') }}</th>
                                                    <th class="text-center"> {{ __('Status') }}</th>
                                                    @if (Gate::check('view pawning'))
                                                        <th class="text-center"> {{ __('Action') }}</th>
                                                    @endif
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($pawnings as $pawning)
                                                    <tr>
                                                        <td>{{ \Auth::user()->pawningNumberFormat($pawning->id) }}</td>
                                                        <td class="invoice_td">{{ $pawning->issue_date }}</td>
                                                        <td class="invoice_td">{{ $pawning->due_date ?? '-' }}</td>
                                                        <td class="font-style">
                                                            {{ number_format($pawning->full_total_of_payment, 2) }}
                                                            {{-- {{ \Auth::user()->priceFormat($pawning->full_total_of_payment) }} --}}
                                                        </td>
                                                        <td class="font-style">
                                                            {{ number_format($pawning->current_due_amount, 2) }}
                                                            {{-- {{ \Auth::user()->priceFormat($pawning->current_due_amount) }} --}}
                                                        </td>
                                                        <td class="font-style">
                                                            @if ($pawning->time_range == 1)
                                                                {{ $pawning->daily_time_period }} {{ __('Days') }}
                                                            @elseif ($pawning->time_range == 2 || $pawning->time_range == 3)
                                                                {{ $pawning->time_period }} {{ __('Months') }}
                                                            @endif
                                                        </td>
                                                        <td class="font-style">
                                                            @if ($pawning->time_range == 1)
                                                                Daily
                                                            @elseif ($pawning->time_range == 2)
                                                                Weekly
                                                            @elseif ($pawning->time_range == 3)
                                                                Monthly
                                                            @endif
                                                        </td>
                                                        <td class="text-center invoice_td">
                                                            @if ($pawning->status == 1)
                                                                <span class="badge badge-info">To Be Paid</span>
                                                            @elseif ($pawning->status == 2)
                                                                <span class="badge badge-warning">Partialy Paid</span>
                                                            @elseif ($pawning->status == 3)
                                                                <span class="badge badge-danger">Over Due</span>
                                                            @elseif ($pawning->status == 4)
                                                                <span class="badge badge-success">Paid</span>
                                                            @endif
                                                        </td>
                                                        @if (Gate::check('view pawning'))
                                                            <td class="text-center">
                                                                @can('view pawning')
                                                                    <a href="{{ route('pawning.view', $pawning->id) }}"
                                                                        class="btn btn-info btn-action mr-1"
                                                                        data-toggle="tooltip"
                                                                        data-original-title="{{ __('Detail') }}">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endcan
                                                            </td>
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

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Others') }}</h4>
                    </div>

                    <div class="card-body">
                        <div id="table-1_wrapper2" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">

                                        <table class="table table-flush" id="dataTable4">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th> {{ __('#') }}</th>
                                                    <th> {{ __('Issue Date') }}</th>
                                                    <th> {{ __('Amount') }} ({{ $currencySymbol }})</th>
                                                    <th class="text-center"> {{ __('Status') }}</th>
                                                    <th class="text-center"> {{ __('Action') }}</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($advances as $advance)
                                                    <tr>
                                                        <td>
                                                            @if ($advance->status == 0)
                                                                <a href="#" data-size="2xl"
                                                                    data-url="{{ route('advance_payement.purchase_details', $advance->id) }}"
                                                                    data-ajax-popup="true" data-toggle="tooltip"
                                                                    data-title="{{ __('Advance Details') }}"
                                                                    class="btn btn-outline-primary">
                                                                    {{ AUth::user()->advanceNumberFormat($advance->id) }}
                                                                </a>
                                                            @elseif ($advance->status == 1)
                                                                <a href="{{ url('advance_payement/receipt', [$advance->id, 'view']) }}"
                                                                    target="_blank" data-toggle="tooltip"
                                                                    data-title="{{ __('Advance Receipt') }}"
                                                                    class="btn btn-outline-primary">
                                                                    {{ AUth::user()->advanceNumberFormat($advance->id) }}
                                                                </a>
                                                            @elseif ($advance->status == 2)
                                                                <a href="#" data-size="2xl"
                                                                    data-url="{{ route('advance_payement.return_details', $advance->id) }}"
                                                                    data-ajax-popup="true" data-toggle="tooltip"
                                                                    data-title="{{ __('Advance Details') }}"
                                                                    class="btn btn-outline-primary">
                                                                    {{ AUth::user()->advanceNumberFormat($advance->id) }}
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td class="invoice_td">{{ $advance->advance_date }}</td>
                                                        <td class="font-style">
                                                            {{ number_format($advance->advance_amount, 2) }}
                                                            {{-- {{ \Auth::user()->priceFormat($advance->advance_amount) }} --}}
                                                        </td>
                                                        <td class="text-center invoice_td">
                                                            @if ($advance->status == 0)
                                                                <span class="badge badge-info">Purchased</span>
                                                            @elseif ($advance->status == 1)
                                                                <span class="badge badge-success">Advanced</span>
                                                            @elseif ($advance->status == 2)
                                                                <span class="badge badge-danger">Returned</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($advance->status == 0)
                                                                <a href="#" data-size="2xl"
                                                                    data-url="{{ route('advance_payement.purchase_details', $advance->id) }}"
                                                                    data-ajax-popup="true" data-toggle="tooltip"
                                                                    data-title="{{ __('Advance Details') }}"
                                                                    class="btn btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @elseif ($advance->status == 1)
                                                                <a href="{{ url('advance_payement/receipt', [$advance->id, 'view']) }}"
                                                                    target="_blank" data-toggle="tooltip"
                                                                    data-title="{{ __('Advance Receipt') }}"
                                                                    class="btn btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @elseif ($advance->status == 2)
                                                                <a href="#" data-size="2xl"
                                                                    data-url="{{ route('advance_payement.return_details', $advance->id) }}"
                                                                    data-ajax-popup="true" data-toggle="tooltip"
                                                                    data-title="{{ __('Advance Details') }}"
                                                                    class="btn btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @foreach ($customReceipts as $customReceipt)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ url('custom_receipt/receipt', [$customReceipt->id, 'view']) }}"
                                                                target="_blank" data-toggle="tooltip"
                                                                data-title="{{ __('Custom Receipt') }}"
                                                                class="btn btn-outline-primary">
                                                                {{ $customReceipt->receipt_code }}
                                                            </a>
                                                        </td>
                                                        <td class="invoice_td">{{ $customReceipt->issue_date }}</td>
                                                        <td class="font-style">
                                                            {{ number_format($customReceipt->paid_amount, 2) }}
                                                            {{-- {{ \Auth::user()->priceFormat($customReceipt->paid_amount) }} --}}
                                                        </td>
                                                        <td class="text-center invoice_td">
                                                            <span class="badge badge-success">Issued</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="{{ url('custom_receipt/receipt', [$customReceipt->id, 'view']) }}"
                                                                target="_blank" data-toggle="tooltip"
                                                                data-title="{{ __('Custom Receipt') }}"
                                                                class="btn btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
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

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Guarantor Contract') }}</h4>
                    </div>

                    <div class="card-body">
                        <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">

                                        <table class="table table-flush" id="dataTable5">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th> {{ __('#') }}</th>
                                                    <th> {{ __('Issue Date') }}</th>
                                                    <th> {{ __('Due Date') }}</th>
                                                    <th> {{ __('Amount') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Outstanding Balance') }} ({{ $currencySymbol }})</th>
                                                    <th class="text-center"> {{ __('Status') }}</th>
                                                    @if (Gate::check('view invoice') || Gate::check('view loan'))
                                                        <th class="text-center"> {{ __('Action') }}</th>
                                                    @endif
                                                </tr>
                                            </thead>

                                            <tbody>
                                                {{-- @foreach ($guarantorContracts as $guarantorContract)
                                                    <tr class="main_row" id="guarantor_contract_detail" data-url="{{ route('invoice.show', $guarantorContract['id']) }}"
                                            data-id="{{ $guarantorContract['id'] }}">
                                            <td>
                                                <a href="{{ route('invoice.show',$guarantorContract->id) }}"
                                                    class="btn btn-outline-primary">
                                                    {{ $guarantorContract->invoice->invoice_code }}
                                                </a>
                                            </td>
                                            <td class="guarantor_contract_td">{{ $guarantorContract->issue_date}}</td>
                                            <td class="guarantor_contract_td">{{ $guarantorContract->due_date ?? '-'}}
                                            </td>
                                            <td class="guarantor_contract_td">
                                                @if (!empty($guarantorContract->sales->current_due_amount))
                                                {{ \Auth::user()->priceFormat($guarantorContract->sales->current_due_amount) }}
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td class="text-center guarantor_contract_td">
                                                @if ($guarantorContract->status == 1)
                                                <span class="badge badge-pill badge-success">Paid</span>
                                                @elseif($guarantorContract->status == 2)
                                                <span class="badge badge-pill badge-info">Partially Paid</span>
                                                @else
                                                <span class="badge badge-pill badge-danger">Overdue</span>
                                                @endif
                                            </td>
                                            @if (Gate::check('view invoice'))
                                            <td class="text-center">
                                                @can('view invoice')
                                                <a href="{{ route('invoice.show',$guarantorContract->id) }}"
                                                    class="btn btn-info btn-action mr-1" data-toggle="tooltip"
                                                    data-original-title="{{__('Detail')}}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                            </td>
                                            @endif
                                            </tr>
                                            @endforeach --}}
                                                @foreach ($guarantorContracts as $guarantorContract)
                                                    <tr>
                                                        <td>{{ $guarantorContract->sale_id }}</td>
                                                        <td class="invoice_td">{{ $guarantorContract->date_of_sale }}
                                                        </td>
                                                        <td class="invoice_td">{{ $guarantorContract->due_date ?? '-' }}
                                                        </td>
                                                        <td class="font-style">
                                                            {{ number_format($guarantorContract->total_due_amount, 2) }}
                                                            {{-- {{ \Auth::user()->priceFormat($guarantorContract->total_due_amount) }} --}}
                                                        </td>
                                                        <td class="invoice_td">
                                                            @if (!empty($guarantorContract->current_due_amount))
                                                                {{ number_format($guarantorContract->current_due_amount, 2) }}
                                                                {{-- {{ \Auth::user()->priceFormat($guarantorContract->current_due_amount) }} --}}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center invoice_td">
                                                            @if ($guarantorContract->status == 1)
                                                                <span class="badge badge-pill badge-success">Paid</span>
                                                            @elseif($guarantorContract->status == 2)
                                                                <span class="badge badge-pill badge-info">Partially
                                                                    Paid</span>
                                                            @else
                                                                <span class="badge badge-pill badge-danger">Overdue</span>
                                                            @endif
                                                        </td>
                                                        @if (Gate::check('view invoice'))
                                                            <td class="text-center">
                                                                @can('view invoice')
                                                                    <a href="{{ route('sale.view', $guarantorContract->id) }}"
                                                                        class="btn btn-info btn-action mr-1"
                                                                        data-toggle="tooltip"
                                                                        data-original-title="{{ __('Detail') }}">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endcan
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach

                                                @foreach ($guarantorLoanContracts as $guarantorLoanContract)
                                                    <tr>
                                                        <td>{{ \Auth::user()->loanNumberFormat($guarantorLoanContract->id) }}
                                                        </td>
                                                        <td class="invoice_td">{{ $guarantorLoanContract->date }}</td>
                                                        <td class="invoice_td">
                                                            {{ $guarantorLoanContract->due_date ?? '-' }}</td>
                                                        <td class="font-style">
                                                            {{ number_format($guarantorLoanContract->loan_amount, 2) }}
                                                            {{-- {{ \Auth::user()->priceFormat($guarantorLoanContract->loan_amount) }} --}}
                                                        </td>
                                                        <td class="font-style">
                                                            @if (!empty($guarantorLoanContract->current_due_amount))
                                                                {{ number_format($guarantorLoanContract->current_due_amount, 2) }}
                                                                {{-- {{ \Auth::user()->priceFormat($guarantorLoanContract->current_due_amount) }} --}}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>

                                                        <td class="text-center invoice_td">
                                                            @if ($guarantorLoanContract->status == 1)
                                                                <span class="badge badge-pill badge-warning">Pending</span>
                                                            @elseif($guarantorLoanContract->status == 2)
                                                                <span class="badge badge-pill badge-info">Approved</span>
                                                            @elseif($guarantorLoanContract->status == 3)
                                                                <span class="badge badge-pill badge-success">Closed</span>
                                                            @elseif($guarantorLoanContract->status == 4)
                                                                <span class="badge badge-pill badge-warning">Checked</span>
                                                            @elseif($guarantorLoanContract->status == 0)
                                                                <span class="badge badge-pill badge-danger">Rejected</span>
                                                            @endif
                                                        </td>
                                                        @if (Gate::check('view loan'))
                                                            <td class="text-center">
                                                                @can('view loan')
                                                                    <a href="{{ route('loan.view', $guarantorLoanContract->id) }}"
                                                                        class="btn btn-info btn-action mr-1"
                                                                        data-toggle="tooltip"
                                                                        data-original-title="{{ __('Detail') }}">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endcan
                                                            </td>
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
    </section>

    <script>
        $("body").on("click", ".block", function() {
            var value = $(this).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure you want to block this user ?',
                text: "Blocking this user will not allow them to use services in this company.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6777ef',
                cancelButtonColor: '#fc544b',
                confirmButtonText: 'Block'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("block/" + value);
                }
            });
        });

        $("body").on("click", ".unblock", function() {
            var value = $(this).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure you want to unblock this user ?',
                text: "Unblocking this user will allow them to use services in this company.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6777ef',
                cancelButtonColor: '#fc544b',
                confirmButtonText: 'Unblock'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("unblock/" + value);
                }
            });
        });

        $("body").on("click", ".delete-customer", function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $(this).data('name') || 'this customer';
            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("Are you sure you want to delete") }} "' + name + '"? {{ __("This action cannot be undone.") }}',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#fc544b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __("Yes, delete it!") }}'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    </script>
@endsection
