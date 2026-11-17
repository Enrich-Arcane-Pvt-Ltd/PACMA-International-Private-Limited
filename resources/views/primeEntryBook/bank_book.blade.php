@extends('layouts.admin')

@section('page-title')
{{ __('Bank Book') }}
@endsection
@section('content')
<style>
    table tfoot {
        display: table-row-group;
    }

    .left-border {
        border-left: 2px solid #dee2e6;
    }

    .right-border {
        border-right: 2px solid #dee2e6;
    }

</style>
<section class="section">
    {{-- <div class="section-header">
        <h1>{{ __('Bank Book') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
        <div class="breadcrumb-item">{{ __('Bank Book') }}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Bank Book') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
                                <div class="breadcrumb-item">{{ __('Bank Book') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('bank_book.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
                                </div>
                                <div class="col-md-4">
                                    <label for="bank_id">Bank</label>
                                    {{ Form::select('bank_id', $banks, $bank ?? '', array('id' => 'bank_id','class' => 'form-control font-style', 'data-attribute'=>'value', 'required'=>'required')) }}
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
                                        <label for="center_id">Center</label>
                                        @if (isset($repaymentCenters))
                                            @if ($repaymentCenters != '')
                                                <input type="hidden" id="center" value="{{ __(' Of ').App\Center::centerName($repaymentCenters)[0] }}">
                                @endif
                                @endif
                                {{ Form::select('center_id', $centers, $repaymentCenters ?? '', array('id' => 'center_id','class' => 'form-control font-style', 'required'=>'required')) }}
                            </div> --}}
                        </div>
                        <div class="row mt-3" style="float: right;">
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
                        <h4>{{ __('View Bank Book') }}</h4>
                        <div class="card-header-action">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-body p-0">
                        <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="button" class="btn btn-primary mb-2" id="print">Print</button>
                                        {{-- <button type="button" class="btn btn-info mb-2" id="export">Export</button> --}}
                                    </div>
                                    <div class="col-sm-12">
                                        <table class="table table-flush" id="bank_books_table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th colspan="3" style="border-bottom: 2px solid #dee2e6;">Debit</th>
                                                    <th colspan="3" class="text-right"
                                                        style="border-bottom: 2px solid #dee2e6;">Credit</th>
                                                </tr>
                                            </thead>
                                            @if($showTable == 'Yes')
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" class="right-border"
                                                        style="padding: 0; vertical-align: initial;">
                                                        <table class="table table-flush">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center"> {{ __('Date') }}</th>
                                                                    <th class="text-center"> {{ __('Description') }}
                                                                    </th>
                                                                    <th class="text-right"> {{ __('Amount') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>{{ $start_date }}</td>
                                                                    <td>Balance B/F</td>
                                                                    <td class="text-right right-border">
                                                                        {{ number_format($startDateBalance, 2) }}</td>
                                                                </tr>
                                                                @foreach ($debitRransactions as $debitRransaction)
                                                                <tr>
                                                                    <td>{{ $debitRransaction->date }}</td>
                                                                    <td>{{ $debitRransaction->related_chart_of_account->name ?? '' }}
                                                                    </td>
                                                                    <td class="text-right">
                                                                        {{ number_format($debitRransaction->amount, 2) }}
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td colspan="3" class="left-border"
                                                        style="padding: 0; vertical-align: initial;">
                                                        <table class="table table-flush">
                                                            <thead>
                                                                <tr>
                                                                    <th style="text-align: center"> {{ __('Date') }}
                                                                    </th>
                                                                    <th style="text-align: center">
                                                                        {{ __('Description') }}</th>
                                                                    <th style="text-align: right"> {{ __('Amount') }}
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($creditRransactions as $creditRransaction)
                                                                <tr>
                                                                    <td>{{ $creditRransaction->date }}</td>
                                                                    <td>{{ $creditRransaction->related_chart_of_account->name ?? '' }}
                                                                    </td>
                                                                    <td class="text-right">
                                                                        {{ number_format($creditRransaction->amount, 2) }}
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    @if ($balanceType = 'debit')
                                                    <td colspan="3" class="right-border"></td>
                                                    <td class="left-border">{{ $end_date }}</td>
                                                    <td>Balance C/D</td>
                                                    <td class="text-right">{{ number_format($balanceAmount, 2) }}</td>
                                                    @elseif ($balanceType = 'credit')
                                                    <td>{{ $end_date }}</td>
                                                    <td>Balance C/D</td>
                                                    <td class="text-right right-border">
                                                        {{ number_format($balanceAmount, 2) }}</td>
                                                    <td colspan="3" class="left-border"></td>
                                                    @endif
                                                </tr>
                                                <tr>
                                                    <th colspan="2"></th>
                                                    <th class="text-right right-border"
                                                        style="border-top: 2px solid #dee2e6; border-bottom: 2px solid #dee2e6;">
                                                        @if ($debitAmount >= $creditAmount)
                                                        {{ number_format($debitAmount, 2) }}
                                                        @elseif ($debitAmount < $creditAmount)
                                                            {{ number_format($creditAmount, 2) }} @endif </th> <th
                                                            colspan="2" class="left-border">
                                                    </th>
                                                    <th class="text-right"
                                                        style="border-top: 2px solid #dee2e6; border-bottom: 2px solid #dee2e6;">
                                                        @if ($debitAmount >= $creditAmount)
                                                        {{ number_format($debitAmount, 2) }}
                                                        @elseif ($debitAmount < $creditAmount)
                                                            {{ number_format($creditAmount, 2) }} @endif </th> </tr>
                                                            <tr>
                                                            @php
                                                            $nextFirstDate =
                                                            \Carbon\Carbon::parse($end_date)->addDay(1)->format('Y-m-d');
                                                            @endphp
                                                            @if ($balanceType = 'debit')
                                                    <td>{{ $nextFirstDate }}</td>
                                                    <td>Balance B/F</td>
                                                    <td class="text-right right-border">
                                                        {{ number_format($balanceAmount, 2) }}</td>
                                                    <td colspan="3" class="left-border"></td>
                                                    @elseif ($balanceType = 'credit')
                                                    <td colspan="3" class="right-border"></td>
                                                    <td class="left-border">{{ $nextFirstDate }}</td>
                                                    <td>Balance B/F</td>
                                                    <td class="text-right">{{ number_format($balanceAmount, 2) }}</td>
                                                    @endif
                                                </tr>
                                            </tfoot>
                                            @endif
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

        var filename = 'Bank Book';
        if ($('#month').length > 0) {
            var duration = $('#month').val();
        } else {
            var duration = "";
        }

        $('#print').click(function () {
            $('#bank_books_table').printThis({
                importCSS: true,
                importStyle: true, //thrown in for extra measure
                // header:'<h3 class="mt-3 text-center" style="margin-bottom:-0.1px;">රිච්වින් ඉන්වෙස්ට්මන්ට් ඇන්ඩ් ක්‍රෙඩිට් ප්‍රයිවට් ලිමිටඩ්<br>'+
                //         '<span style="font-size: 22px;">ණය යෝජනා අනුමත අත්හිටුවීම් සහ නිකුත් කිරීමේ පත්‍රය</span></h3>'
            });
        });

        $("#export").click(function () {
            // $("#bank_books_table").table2excel({
            //     // exclude CSS class
            //     // exclude:".noExl",
            //     name:"Cash Book",
            //     filename:"SomeFile",//do not include extension
            //     fileext:".xlsx" // file extension
            // });
        });
        // if(localStorage.getItem("bank_book_no") == null){
        //     var pageNo = 0;
        // } else {
        //     var pageNo = Number(localStorage.getItem("bank_book_no"));
        // }
        // var bank_booksTable = $('#bank_books_table').DataTable({
        //                                                         order:[[0,"asc"]],
        //                                                         dom: 'lBfrtip',
        //                                                         buttons: [
        //                                                             {
        //                                                                 extend: 'excel',
        //                                                                 title: filename+' Of '+duration,
        //                                                                 footer: true
        //                                                             },  {
        //                                                                 extend: 'print',
        //                                                                 title:  function() {
        //                                                                         return "<h3>"+filename+' Of '+duration+"</h3>";
        //                                                                     } ,
        //                                                                 footer: true
        //                                                             }, {
        //                                                                 extend: 'csv',
        //                                                                 title: filename+' Of '+duration,
        //                                                                 footer: true
        //                                                             }
        //                                                         ]
        //                                                     });
        // bank_booksTable.page(pageNo).draw(false);

        // $('#bank_books_table').on( 'page.dt', function () {
        //     var pageInfo =  bank_booksTable.page.info();
        //     pageNo = pageInfo.page;
        //     localStorage.setItem("bank_book_no", pageNo);
        // });

        // $('#report-dataTable').DataTable({
        //     dom: 'lBfrtip',
        //     buttons: [
        //         {
        //             extend: 'excel',
        //             title: filename+center+duration,
        //             footer: true
        //         },  {
        //             extend: 'print',
        //             title:  function() {
        //                     return "<h3>"+filename+center+duration+"</h3>";
        //                 } ,
        //             footer: true
        //         }, {
        //             extend: 'csv',
        //             title: filename+center+duration,
        //             footer: true
        //         }
        //     ]
        // });
    });

    $('body').on('click', '.bank_book_td', function () {
        var bankBookId = $(this).closest('tr').attr("data-id");
        window.location.href = "{{ url('bank_books') }}/" + bankBookId;
    });

</script>

@endsection
