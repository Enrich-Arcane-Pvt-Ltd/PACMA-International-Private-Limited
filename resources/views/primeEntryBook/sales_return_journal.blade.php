@extends('layouts.admin')

@section('page-title')
{{ __('Sales Return Journal') }}
@endsection
@section('content')
<style>
    table tfoot {
        display: table-row-group;
    }

</style>
<section class="section">
    {{-- <div class="section-header">
        <h1>{{ __('Sales Return Journal') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
        <div class="breadcrumb-item">{{ __('Sales Return Journal') }}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Sales Return Journal') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
                                <div class="breadcrumb-item">{{ __('Sales Return Journal') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('sales_return_journal.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
                                </div>
                                <div class="col-md-2">
                                    <label for="start_month">Start Date</label>
                                    <input class="form-control font-style" name="start_date" type="date"
                                        value="{{ $start_date ?? '' }}" id="start_date" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="end_month">End Date</label>
                                    <input class="form-control font-style" name="end_date" type="date"
                                        value="{{ $end_date ?? '' }}" id="end_date" required>
                                </div>
                                {{-- <div class="col-md-3">
                                        <label for="end_month">End Month</label>
                                        <input class="form-control font-style" name="end_month" type="month" value="{{ $end_month ?? '' }}"
                                id="end_month" readonly>
                            </div> --}}
                            {{-- <div class="col-md-3">
                                        <label for="center_id">Center</label>
                                        @if (isset($repaymentCenters))
                                            @if ($repaymentCenters != '')
                                                <input type="hidden" id="center" value="{{ __(' Of ').App\Center::centerName($repaymentCenters)[0] }}">
                            @endif
                            @endif
                            {{ Form::select('center_id', $centers, $repaymentCenters ?? '', array('id' => 'center_id','class' => 'form-control font-style', 'required'=>'required')) }}
                        </div> --}}
                </div>
                <div class="row mt-3 mb-3" style="float: right;">
                    <div class="col-auto">
                        <input class="btn font-style btn-outline-primary" type="submit" value="Search">
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between w-100">
                        <h4>{{ __('View Sales Return Journal') }}</h4>
                        <div class="card-header-action">
                            {{-- @can('create account transaction')
                                <a href="#" data-size="xl" data-url="{{ route('accountTransaction.create') }}"
                            data-ajax-popup="true" data-title="{{ __('Create New Account Transaction') }}" class="btn
                            btn-icon icon-left btn-primary commonModal">
                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                            <span class="btn-inner--text"> {{ __('Create') }}</span>
                            </a>
                            @endcan --}}
                            @if($showTable == 'Yes')
                            <input type="hidden" id="duration"
                                value="{{ __(' - From ').Auth::user()->dateFormat($start_date).__(' To ').Auth::user()->dateFormat($end_date) }}">
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-body p-0">
                        <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-flush" id="sales_return_journals_table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="text-align: center"> {{ __('Date') }}</th>
                                                    <th style="text-align: center"> {{ __('Customer') }}</th>
                                                    <th style="text-align: center"> {{ __('Invoice No.') }}</th>
                                                    <th style="text-align: center"> {{ __('Amount') }}</th>
                                                </tr>
                                            </thead>

                                            @if($showTable == 'Yes')
                                            <tbody>
                                                @php
                                                $paidAmount = 0;
                                                @endphp
                                                @foreach ($salesreturns as $salesreturn)
                                                <tr>
                                                    <td class="font-style">{{ $salesreturn->date }}</td>
                                                    <td class="font-style">
                                                        @if ($salesreturn->sale->customer_type == 1)
                                                        {{ $salesreturn->sale->customer->name ?? '' }}
                                                        @elseif ($salesreturn->sale->customer_type == 2)
                                                        {{ $salesreturn->sale->customer_name }}
                                                        @endif
                                                    </td>
                                                    <td class="font-style">
                                                        {{-- <a href="{{ url('invoice', $salesreturn->sale->invoice_id) }}"
                                                        class="btn font-style btn-outline-primary"> --}}
                                                        {{ $salesreturn->sale->invoice->invoice_code ?? '' }}
                                                        {{-- </a> --}}
                                                    </td>
                                                    <td class="font-style text-right">
                                                        {{ number_format($salesreturn->amount, 2) }}
                                                    </td>
                                                </tr>
                                                @php
                                                $paidAmount += $salesreturn->amount;
                                                @endphp
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th class="text-center">Total</th>
                                                    <th></th>
                                                    <th style="text-align: right">{{ number_format($paidAmount, 2) }}
                                                    </th>
                                                </tr>
                                            </tfoot>
                                            @endif
                                            {{-- </tbody> --}}
                                        </table>
                                    </div>
                                </div>
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
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');

        var filename = 'Sales Return Journal';
        if ($('#duration').length > 0) {
            var duration = $('#duration').val();
        } else {
            var duration = "";
        }

        if (localStorage.getItem("sales_return_journal_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("sales_return_journal_no"));
        }
        var sales_return_journalsTable = $('#sales_return_journals_table').DataTable({
            order: [
                [0, "asc"]
            ],
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excel',
                title: filename + duration,
                footer: true
            }, {
                extend: 'print',
                title: function () {
                    return "<h3>" + filename + duration + "</h3>";
                },
                footer: true
            }, {
                extend: 'csv',
                title: filename + duration,
                footer: true
            }]
        });
        sales_return_journalsTable.page(pageNo).draw(false);

        $('#sales_return_journals_table').on('page.dt', function () {
            var pageInfo = sales_return_journalsTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("sales_return_journal_no", pageNo);
        });

    });

    $('body').on('click', '.sales_return_journal_td', function () {
        var salesReturnJournalId = $(this).closest('tr').attr("data-id");
        window.location.href = "{{ url('sales_return_journals') }}/" + salesReturnJournalId;
    });

</script>

@endsection
