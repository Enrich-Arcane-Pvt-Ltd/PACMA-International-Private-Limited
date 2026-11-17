@extends('layouts.admin')
@section('page-title')
{{__('Customer Report')}}
@endsection
@push('script-page')


<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    var filename = $('#filename').val();

    function saveAsPDF() {
        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.3,
            filename: filename,
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 4,
                dpi: 72,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'A2'
            }
        };
        html2pdf().set(opt).from(element).save();

    }


    $(document).ready(function () {
        var filename = $('#filename').val();
        $('#report-dataTable').DataTable({
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excel',
                title: filename
            }, {
                extend: 'print',
                title: filename
            }, {
                extend: 'csv',
                title: filename
            }]
        });
    });

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Customer Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Report')}}</div>
        <div class="breadcrumb-item">{{__('Customer Report')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        {{-- <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                            <form method="post" action="{{ route('sales_report.search') }}">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h4 class="h4 mb-0">Filter</h4>
                </div>
                <div class="col-md-4">
                </div>
                <div class="col-md-2">
                    <label for="start_month">Start Date</label>
                    <input class="form-control font-style" name="start_date" type="date" value="" id="start_date"
                        required>
                </div>
                <div class="col-md-2">
                    <label for="end_month">End Date</label>
                    <input class="form-control font-style" name="end_date" type="date" value="" id="end_date" required>
                </div>

                <div class="col-auto apply-btn">
                    <input class="btn font-style btn-outline-primary" type="submit" value="Search">
                </div>
            </div>
        </div>
        </form>
    </div>
    </div>
    </div> --}}
    <div id="printableArea">
        {{-- <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-wrap">
                                <div class="card-body">
                                    <div class="row">
                                            @if($showTable == 'Yes')
                                                <div class="col">
                                                    <h6>{{__('Total due payment')}} : {{number_format($duetotal,2)}}
        </h6>
    </div>

    <div class="col">

    </div>

    <div class="col">

    </div>

    <div class="col">
        <h6>{{__('Duration')}} : {{ $start_date . " TO " .$end_date }} </h6>
    </div>
    @endif

    </div>
    </div>
    </div>
    </div>
    </div>
    </div> --}}

    <div class="row justify-content-center">

        <div class="col-12">
            <div class="bg-white cus_show p-2 br-4 mb-2">
                <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                    <h4 class="mt-2 ">{{ __('Customer Report') }}</h3>
                        <div class="section-header-breadcrumb d-flex ">
                            <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a>
                            </div>
                            <div class="breadcrumb-item">{{__('Report')}}</div>
                            <div class="breadcrumb-item">{{__('Customer Report')}}</div>
                        </div>
                </div>
            </div>
        </div>

        <div class="col-12 ">
            <div class="card br-4 cus_show">
                <div class="card-header">
                    <div class="d-flex justify-content-between w-100">
                        <table class="table table-flush" id="report-dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th> {{__('Customer No')}}</th>
                                    <th> {{__('Name')}}</th>
                                    <th> {{__('Address')}}</th>
                                    <th> {{__('Phone')}}</th>
                                    <th> {{__('Email')}}</th>
                                    <th> {{__('NIC No')}}</th>
                                    <th> {{__('Type')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @if($showTable == 'Yes') --}}
                                @foreach($customers as $customer)
                                <tr>
                                    <td>{{ AUth::user()->customerNumberFormat($customer->id) }}</td>
                                    <td>@if($customer['title'] == 0)
                                        Mr.
                                        @elseif($customer['title'] == 1)
                                        Mrs.
                                        @else
                                        Miss.
                                        @endif
                                        {{$customer->name ?? ''}}</td>
                                    <td>{{$customer->address ?? ''}}</td>
                                    <td>{{$customer->phone ?? ''}}</td>
                                    <td>{{$customer->email ?? ''}}</td>
                                    <td>{{$customer->nic_no ?? ''}}</td>
                                    <td>
                                        @if($customer['type'] == 0) Customer Only</span>
                                        @elseif($customer['type'] == 1) Guarantor Only</span>
                                        @else Customer & Guarantor</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                {{-- @endif --}}
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
    </div>
</section>
{{-- <section>
        <div class="modal modelView" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Instalment Summery</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class=" col-md-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>Due Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="installmetTableBody">

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
    </section> --}}
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {

        $('.orderModel').on('click', function () {
            const saleId = $(this).attr("data-id");
            let row = "";
            let status;
            $.ajax({
                type: 'GET',
                url: '{{("installment/getProductInstallment/")}}' + saleId,
                beforeSend: function () {},
                success: function (data) {
                    result = data;
                    console.log(result);
                    $('#installmetTableBody').html("");
                    for (let i = 0; i < data.length; i++) {
                        let element = data[i].amount;
                        if (data[i].status == 1) {
                            status = "Paid";
                        } else if (data[i].status == 2) {
                            status = "Not Paid";
                        } else if (data[i].status == 3) {
                            status = "Over Due";
                        }

                        var paidAmount = 0.00;
                        if (data[i].paid_amount != null) {
                            paidAmount = data[i].paid_amount;
                        }
                        // row+="<tr><td>"+data[i].id+"</td><td>"+data[i].amount+"</td><td>"+data[i].branch_id+"</td><td>"+data[i].paid_amount+"</td><td>"+data[i].installment_date+"</td><td>"+status+"</td></tr>";
                        row += "<tr><td>" + data[i].id + "</td><td>" + data[i].amount +
                            "</td><td>" + paidAmount + "</td><td>" + data[i]
                            .installment_date + "</td><td>" + status + "</td></tr>";

                    }
                },
                complete: function () {
                    $('#installmetTableBody').append(row);
                    $('.modelView').modal();
                }
            });

        });
    });

</script>
