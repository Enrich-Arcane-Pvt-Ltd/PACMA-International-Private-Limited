@extends('layouts.admin')

@section('page-title')
{{ __('Bank Reconciliation Statements') }}
@endsection
@section('content')
<style>
    /* .print-only {
        display: none;
    }
    @media print{
        .print-only {
            display: flex;
        }
        .headding {
            text-align: center;
        }
    } */
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

    {{-- <div class="section-header">
        <h1>{{ __('Bank Reconciliation Statements') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Bank Reconciliation') }}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Bank Reconciliation Statements') }}</h1>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                <div class="breadcrumb-item">{{ __('Bank Reconciliation') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Cash Sales & Hire Sales')}}</h4>
                            @can('create bank rec')
                            <div class="col-auto">
                                <a href="#" data-url="{{ route('bank_rec.create') }}" data-ajax-popup="true"
                                    data-title="{{__('Create New Bank Reconciliation')}}"
                                    class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{__('Create New Bank Reconciliation')}}</span>
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body phone-card-bd">
                        <div class="setting-tab">
                            <ul class="nav nav-pills nav-fill" id="myTab3" role="tablist">
                                @foreach ($banks as $bank)
                                <li class="nav-item">
                                    <a class="nav-link" id="cheques_{{ $bank->id }}_tab" data-toggle="tab"
                                        href="#cheques_{{ $bank->id }}" role="tab" aria-controls=""
                                        aria-selected="false">{{ $bank->name }}</a>
                                </li>
                                @endforeach
                            </ul>
                            <div class="tab-content" id="myTabContent2">
                                @foreach ($banks as $bank)
                                <div class="tab-pane fade fade" id="cheques_{{ $bank->id }}" role="tabpanel"
                                    aria-labelledby="profile-tab3">
                                    <div class="company-setting-wrap">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <table class="table table-flush dataTable">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th> {{__('#')}}</th>
                                                                    <th> {{__('Reconciliation Date Range')}}</th>
                                                                    <th> {{__('Cash Book Balance in Start')}}</th>
                                                                    <th> {{__('Cash Book Balance in End')}}</th>
                                                                    @if(Gate::check('view bank rec') ||
                                                                    Gate::check('delete bank rec'))
                                                                    <th class="text-center"> {{__('Action')}}</th>
                                                                    @endif
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($bank->bankRecs as $rec)
                                                                <tr id="center_detail">
                                                                    <td>
                                                                        @if(Gate::check('view bank rec'))
                                                                        <a href="{{ route('bank_rec.statement', $rec->id) }}"
                                                                            class="btn btn-outline-primary">
                                                                            {{ AUth::user()->bankRecNumberFormat($rec->id) }}
                                                                        </a>
                                                                        @else
                                                                        {{ AUth::user()->bankRecNumberFormat($rec->id) }}
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $rec->start_date.' To '. $rec->end_date}}
                                                                    </td>
                                                                    <td class="text-right">
                                                                        {{ number_format($rec->start_balance, 2) }}</td>
                                                                    <td class="text-right">
                                                                        {{ number_format($rec->end_balance, 2) }}</td>
                                                                    @if(Gate::check('view bank rec') ||
                                                                    Gate::check('delete bank rec'))
                                                                    <td class="action text-center">
                                                                        @can('view bank rec')
                                                                        <a href="{{ route('bank_rec.statement', $rec->id) }}"
                                                                            class="btn btn-info btn-action mr-1"
                                                                            data-title="{{ __('View Rec Statement') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show') }}">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        @endcan

                                                                        @can('delete bank rec')
                                                                        <a href="#!" class="btn btn-danger btn-action "
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{__('Delete')}}"
                                                                            data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                            data-confirm-yes="document.getElementById('delete-form-{{$rec->id}}').submit();">
                                                                            <i class="fas fa-trash"></i>
                                                                        </a>
                                                                        {!! Form::open(['method' => 'GET', 'route' =>
                                                                        ['bank_rec.destroy',
                                                                        $rec->id],'id'=>'delete-form-'.$rec->id]) !!}
                                                                        {!! Form::close() !!}
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
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    var banks = {!! json_encode($banks) !!};

    $(document).ready(function () {
        try {
            $('.dataTable').dataTable({
                order: [
                    [0, "desc"]
                ]
            });
            if (banks && banks.length > 0) {
                $('#cheques_' + banks[0].id + '_tab').addClass('active show');
                $('#cheques_' + banks[0].id).addClass('active show');
            }
        } catch (e) {
            console.error('Error initializing bank reconciliation data:', e);
        } finally {
            $(".se-pre-con").fadeOut('slow');
        }
    });

    // Fallback safeguard in case anything delays or blocks ready handler
    setTimeout(function () {
        $(".se-pre-con").fadeOut('slow');
    }, 1500);

    $('body').on('input', '#bank_statement_balance', function () {
        var bankBalance = 0;
        if ($(this).val() != '') {
            bankBalance = parseFloat($(this).val());
        }
        var cashBalance = parseFloat($('#bank_book_balance').val());
        var difference = 0;
        if (bankBalance >= cashBalance) {
            difference = bankBalance - cashBalance;
        } else {
            difference = cashBalance - bankBalance;
        }
        $('#difference').html(difference.toFixed(2))
    });

    function print() {
        $('#printableArea').printThis({
            importCSS: true,
            importStyle: true,
        });
    }

</script>

@endsection
