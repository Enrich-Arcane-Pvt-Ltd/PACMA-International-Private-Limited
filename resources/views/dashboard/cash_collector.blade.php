@extends('layouts.admin')
@section('page-title')
    {{__('Dashboard')}}
@endsection

@push('script-page')
    <script>
    @if(\Auth::user()->can('show dashboard'))

        var SalesChart = (function () {
                var $chart = $('#cash-flow');

                function init($this) {
                    var salesChart = new Chart($this, {
                        type: 'line',
                        options: {
                            scales: {
                                yAxes: [{
                                    gridLines: {
                                        color: Charts.colors.gray[200],
                                        zeroLineColor: Charts.colors.gray[200]
                                    },
                                    ticks: {}
                                }]
                            }
                        },
                        data: {
                            labels: {!! json_encode($incExpLineChartData['day']) !!},
                            datasets: {!! json_encode($incExpLineChartData['incExpArr']) !!}

                        },
                    });
                    $this.data('chart', salesChart);
                };
                if ($chart.length) {
                    init($chart);
                }
            })();
        var SalesChart = (function () {
            var $chart = $('#incExpBarChart');

            function init($this) {
                var salesChart = new Chart($this, {
                    type: 'bar',
                    options: {
                        scales: {
                            yAxes: [{
                                gridLines: {
                                    color: Charts.colors.gray[200],
                                    zeroLineColor: Charts.colors.gray[200]
                                },
                                ticks: {}
                            }]
                        }
                    },
                    data: {
                        labels: {!! json_encode($incExpBarChartData['month']) !!},
                        datasets: {!! json_encode($incExpBarChartData['incExpArr']) !!}
                    },
                });
                $this.data('chart', salesChart);
            };
            if ($chart.length) {
                init($chart);
            }
        })();

        var DoughnutChart = (function () {
            var $chart = $('#chart-doughnut-income');

            function init($this) {
                var randomScalingFactor = function () {
                    return Math.round(Math.random() * 100);
                };
                var doughnutChart = new Chart($this, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: {!! json_encode($incomeCatAmount) !!},
                            backgroundColor: {!! json_encode($incomeCategoryColor) !!},
                        }],
                        labels: {!! json_encode($incomeCategory) !!},
                    },
                    options: {
                        responsive: true,
                        legend: {
                            position: 'top',
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
                $this.data('chart', doughnutChart);
            };
            if ($chart.length) {
                init($chart);
            }
        })();
        var DoughnutChart = (function () {
            var $chart = $('#chart-doughnut-expense');

            function init($this) {
                var randomScalingFactor = function () {
                    return Math.round(Math.random() * 100);
                };
                var doughnutChart = new Chart($this, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: {!! json_encode($expenseCatAmount) !!},
                            backgroundColor: {!! json_encode($expenseCategoryColor) !!},
                        }],
                        labels: {!! json_encode($expenseCategory) !!},
                    },
                    options: {
                        responsive: true,
                        legend: {
                            position: 'top',
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
                $this.data('chart', doughnutChart);
            };
            if ($chart.length) {
                init($chart);
            }
        })();
    @endif
    </script>
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{__('Dashboard')}}</h1>
        </div>
        @if(\Auth::user()->can('show dashboard'))
            @if(\Auth::user()->type=='company')
                <div class="row">
                    @if($constant['taxes']<=0)
                        <div class="col-3">
                            <div class="alert alert-danger">
                                {{__('Please add constant taxes. ')}}<a href="{{route('taxes.index')}}"><b>{{__('click here')}}</b></a>
                            </div>
                        </div>
                    @endif
                    @if($constant['category']<=0)
                        <div class="col-3">
                            <div class="alert alert-danger">
                                {{__('Please add constant category. ')}}<a href="{{route('product-category.index')}}"><b>{{__('click here')}}</b></a>
                            </div>
                        </div>
                    @endif
                    @if($constant['units']<=0)
                        <div class="col-3">
                            <div class="alert alert-danger">
                                {{__('Please add constant unit. ')}}<a href="{{route('product-unit.index')}}"><b>{{__('click here')}}</b></a>
                            </div>
                        </div>
                    @endif

                    @if($constant['bankAccount']<=0)
                        <div class="col-3">
                            <div class="alert alert-danger">
                                {{__('Please create bank account. ')}}<a href="{{route('bank-account.index')}}"><b>{{__('click here')}}</b></a>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-sort-amount-down"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{__('DUE PAYMENTS')}}</h4>
                            </div>
                            <div class="card-body">
                                {{$totalDuePaymentCount}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="far fa-money-bill-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{__('DUE PAYMENTS AMOUNT')}}</h4>
                            </div>
                            <div class="card-body">
                                {{'LKR '.$totalDuePaymentAmount}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-sort-amount-down"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{__('OVER DUE')}}</h4>
                            </div>
                            <div class="card-body">
                                {{$totalOverDuePaymentCount}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="far fa-money-bill-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{__('OVER DUE AMOUNT')}}</h4>
                            </div>
                            <div class="card-body">
                                {{'LKR '.$totalOverDuePaymentAmount}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6><b>{{__('Due Today')}}</b></h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('Installment For')}}</th>
                                        <th>{{__('Customer No')}}</th>
                                        <th>{{__('Customer')}}</th>
                                        <th>{{__('Contact No.')}}</th>
                                        <th>{{__('Installment Date')}}</th>
                                        <th>{{__('Installment Amount')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($pawningTodayInstallments as $pawningTodayInstallment)
                                        <tr class="font-style">
                                            <td class="font-style">
                                                <a href="{{ route('pawning.view', $pawningTodayInstallment->pawning_id) }}" class="btn btn-outline-primary view">
                                                    {{ AUth::user()->pawningNumberFormat($pawningTodayInstallment->pawning_id) }}
                                                </a>
                                            </td>
                                            <td><span class="badge badge-warning">Pawning</span></td>
                                            <td>{{ AUth::user()->customerNumberFormat($pawningTodayInstallment->pawnings->customer_id ?? '') }} </td>
                                            <td>{{$pawningTodayInstallment->pawnings->customer->name ?? ''}} </td>
                                            <td>{{$pawningTodayInstallment->pawnings->customer->phone ?? ''}}</td>
                                            <td>{{$pawningTodayInstallment->installment_date}}</td>
                                            <td>{{'LKR '. $pawningTodayInstallment->installment_amount}}</td>
                                        </tr>
                                    @empty

                                    @endforelse
                                    @forelse($loanTodayInstallments as $loanTodayInstallment)
                                        <tr class="font-style">
                                            <td class="font-style">
                                                <a href="{{ route('loan.view', $loanTodayInstallment->loan_id) }}" class="btn btn-outline-primary view">
                                                    {{ Auth::user()->loanNumberFormat($loanTodayInstallment->loan_id)}}
                                                </a>
                                            </td>
                                            <td><span class="badge badge-success">Loan</span></td>
                                            <td>{{ AUth::user()->customerNumberFormat($loanTodayInstallment->loans->customer_id ?? '') }} </td>
                                            <td>{{$loanTodayInstallment->loans->customer->name ?? ''}} </td>
                                            <td>{{$loanTodayInstallment->loans->customer->phone ?? ''}}</td>
                                            <td>{{$loanTodayInstallment->installment_date}}</td>
                                            <td>{{'LKR '. $loanTodayInstallment->amount}}</td>
                                        </tr>
                                        @empty
                                @endforelse
                                @forelse($hirePurchaseTodayInstallments as $hirePurchaseTodayInstallment)
                                <tr class="font-style">
                                    <td class="font-style">
                                        <a href="{{ route('hire_purchase_contract.view', $hirePurchaseTodayInstallment->sale_id) }}" class="btn btn-outline-primary view">
                                            {{$hirePurchaseTodayInstallment->hirepurchaseDetails->sale_id ?? ''}}
                                        </a>
                                    </td>
                                    {{-- <td class="font-style"><button type="button" class="btn btn-outline-primary view" value=""> {{'#'.$hirePurchaseTodayInstallment->hirepurchaseDetails->sale_id}} </button></td> --}}
                                    <td><span class="badge badge-info">Hire Sales</span></td>
                                    <td>{{ AUth::user()->customerNumberFormat($hirePurchaseTodayInstallment->hirepurchaseDetails->customer_id ?? '') }} </td>
                                    <td>{{$hirePurchaseTodayInstallment->hirepurchaseDetails->customer->name ?? ''}} </td>
                                    <td>{{$hirePurchaseTodayInstallment->hirepurchaseDetails->customer->phone ?? ''}}</td>
                                    <td>{{$hirePurchaseTodayInstallment->installment_date}}</td>
                                    <td>{{'LKR '. $hirePurchaseTodayInstallment->amount}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="text-center">
                                            <h6>{{__('there is no recent invoice')}}</h6>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6><b>{{__('Over Dues')}}</b></h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('Installment For')}}</th>
                                        <th>{{__('Customer No')}}</th>
                                        <th>{{__('Customer')}}</th>
                                        <th>{{__('Contact No.')}}</th>
                                        <th>{{__('Installment Date')}}</th>
                                        <th> {{__('No of Overdue Installment')}}</th>
                                        <th>{{__('Over Due Installment Amount')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($pawningOverDueInstallments as $pawningOverDueInstallment)
                                        <tr class="font-style">
                                            <td class="font-style">
                                                <a href="{{ route('pawning.view', $pawningOverDueInstallment->pawning_id) }}" class="btn btn-outline-primary view">
                                                    {{ AUth::user()->pawningNumberFormat($pawningOverDueInstallment->pawning_id) }}
                                                </a>
                                            </td>
                                            <td><span class="badge badge-warning">Pawning</span></td>
                                            <td>{{ AUth::user()->customerNumberFormat($pawningOverDueInstallment->pawnings->customer_id ?? '') }} </td>
                                            <td>{{$pawningOverDueInstallment->pawning->customer->name ?? ''}} </td>
                                            <td>{{$pawningOverDueInstallment->pawning->customer->phone ?? ''}}</td>
                                            <td>{{$pawningOverDueInstallment->installment_date}}</td>
                                            <td>{{$pawningOverDueInstallment->overdue_installment_count }}</td>
                                            <td>{{'LKR '. $pawningOverDueInstallment->overdue_amount}}</td>
                                        </tr>
                                    @empty

                                    @endforelse
                                    @forelse($loanOverDueInstallments as $loanOverDueInstallment)
                                        <tr class="font-style">
                                            <td class="font-style">
                                                <a href="{{ route('loan.view', $loanOverDueInstallment->loan_id) }}" class="btn btn-outline-primary view">
                                                    {{ Auth::user()->loanNumberFormat($loanOverDueInstallment->loan_id)}}
                                                </a>
                                            </td>
                                            <td><span class="badge badge-success">Loan</span></td>
                                            <td>{{ AUth::user()->customerNumberFormat($loanOverDueInstallment->loans->customer_id ?? '') }} </td>
                                            <td>{{$loanOverDueInstallment->loans->customer->name ?? ''}} </td>
                                            <td>{{$loanOverDueInstallment->loans->customer->phone ?? ''}}</td>
                                            <td>{{$loanOverDueInstallment->installment_date}}</td>
                                            <td>{{$loanOverDueInstallment->overdue_installment_count }}</td>
                                            <td>{{'LKR '. $loanOverDueInstallment->overdue_amount}}</td>
                                        </tr>
                                        @empty
                                @endforelse
                                @forelse($hirePurchaseOverDueInstallments as $hirePurchaseOverDueInstallment)
                                <tr class="font-style">
                                    <td class="font-style">
                                        <a href="{{ route('hire_purchase_contract.view', $hirePurchaseOverDueInstallment->sale_id) }}" class="btn btn-outline-primary view">
                                            {{ $hirePurchaseOverDueInstallment->hirepurchaseDetails->sale_id??'' }}
                                        </a>
                                    </td>
                                    <td><span class="badge badge-info">Hire Sales</span></td>
                                    <td>{{ AUth::user()->customerNumberFormat($hirePurchaseOverDueInstallment->hirepurchaseDetails->customer_id ?? '') }} </td>
                                    <td>{{$hirePurchaseOverDueInstallment->hirepurchaseDetails->customer->name ?? ''}} </td>
                                    <td>{{$hirePurchaseOverDueInstallment->hirepurchaseDetails->customer->phone ?? ''}}</td>
                                    <td>{{$hirePurchaseOverDueInstallment->installment_date}}</td>
                                    <td>{{$hirePurchaseOverDueInstallment->overdue_installment_count }}</td>
                                    <td>{{'LKR '. $hirePurchaseOverDueInstallment->overdue_amount}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="text-center">
                                            <h6>{{__('there is no recent invoice')}}</h6>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <h4 class="text-danger">{{__('Permission Denied')}}</h4>
                </div>
            </div>
        @endif
    </section>
@endsection


