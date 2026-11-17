@extends('layouts.admin')
@section('page-title')
{{__('Supplier Report')}}
@endsection
@push('script-page')

<style>
    table tfoot {
        display: table-row-group;
    }

</style>

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
        var filename = 'Supplier Report';
        if ($('#duration').val() != undefined) {
            var duration = $('#duration').val();
        } else {
            var duration = "";
        }

        if ($('#category').length > 0) {
            var category = $('#category').val();
        } else {
            var category = "";
        }

        $('#report-dataTable').DataTable({
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excel',
                title: filename + category + duration,
                footer: true
            }, {
                extend: 'print',
                title: function () {
                    return "<h3>" + filename + category + duration + "</h3>";
                },
                footer: true
            }, {
                extend: 'csv',
                title: filename + category + duration,
                footer: true
            }]
        });
    });

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Supplier Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Report')}}</div>
        <div class="breadcrumb-item">{{__('Supplier Report')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        {{-- <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                            <form method="post" action="{{ route('supplier_report.search') }}">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h4 class="h4 mb-0">Filter</h4>
                </div>
                <div class="col-md-2">
                    <label for="start_month">Month</label>
                    <input class="form-control font-style" name="month" type="month" value="{{ $month ?? '' }}"
                        id="month" required>
                </div>
                <div class="col-md-2">
                    <label for="end_month">End Date</label>
                    <input class="form-control font-style" name="end_date" type="date" value="{{ $end_date ?? '' }}"
                        id="end_date" required>
                </div>
                <div class="col-md-2">
                    <label for="loan_type">Category</label>
                    @if (isset($productCategory))
                    @if ($productCategory != '')
                    <input type="hidden" id="category"
                        value="{{ __(' Of ').App\ProductCategory::categoryName($productCategory)[0] }}">
                    @endif
                    @endif
                    {{ Form::select('category_id', $productCategories, $productCategory ?? '', array('class' => 'form-control font-style')) }}
                </div>
                <div class="col-md-2">
                    <label for="oficcer_id">Oficcer</label>
                    {{ Form::select('oficcer_id', $oficcers, $loanOficcer ?? '', array('id' => 'oficcer_id','class' => 'form-control font-style')) }}
                </div>
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
    </div> --}}
    <div id="printableArea">

        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Supplier Report') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Report')}}</div>
                                <div class="breadcrumb-item">{{__('Supplier Report')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 " id="bill-container">
                <div class="card br-4 cus_show">

                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100" style="overflow-x: auto;">
                            <table class="table table-flush" id="report-dataTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th> {{__('Supplier No')}}</th>
                                        <th> {{__('Supplier Name')}}</th>
                                        <th> {{__('Company Name')}}</th>
                                        <th> {{__('Contact')}}</th>
                                        <th> {{__('Email')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @if($showTable == 'Yes') --}}
                                    {{-- <input type="hidden" id="duration" value="{{ __(' - From ').Auth::user()->dateFormat($start_date).__(' To ').Auth::user()->dateFormat($end_date) }}">
                                    --}}
                                    {{-- <input type="hidden" id="duration" value="{{ __(' - From ').Auth::user()->monthFormat($month) }}">
                                    --}}
                                    @foreach($venders as $vender)
                                    <tr>
                                        <td>
                                            <a href="#" data-size="xl"
                                                data-url="{{ route('supplier_report.get_grns', $vender->id) }}"
                                                data-ajax-popup="true" data-title="{{__('Supplier Transactions')}}"
                                                class="btn font-style btn-outline-primary">
                                                {{ AUth::user()->venderNumberFormat($vender->id) }}
                                            </a>
                                            {{-- <button class="btn font-style btn-outline-primary orderModel" type="button" data-id="{{$vender->id}}">
                                            {{ AUth::user()->venderNumberFormat($vender->id) }} </button> --}}
                                        </td>
                                        <td>{{ $vender->contact_name }}</td>
                                        <td>{{ $vender->name }}</td>
                                        <td>{{ $vender->contact }}</td>
                                        <td>{{ $vender->email }}</td>
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
            <div class="modal-dialog" role="document" style="width: auto !important; max-width: 1000px !important;">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Instalment Summery</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class=" col-md-12 table-responsive">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>Installment Amount</th>
                                    <th>Amount Tobe Paid</th>
                                    <th>Paid Amount</th>
                                    <th>Due Date</th>
                                    <th>Stauts</th>
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
{{-- <script>

  $(document).ready(function(){

    $('.orderModel').on('click', function() {
            const supplierId = $(this).attr("data-id");
            let row = "";
            let status;
                $.ajax({
                    type: 'GET',
                    url: '{{("installment/getLoanInstallment/")}}' + supplierId,
beforeSend: function() {
},
success: function(data) {
result = data;
console.log(result);
$('#installmetTableBody').html("");
for (let i = 0; i < data.length; i++) { let element=data[i].amount; if(data[i].status==1){ status="Paid" ; }else
    if(data[i].status==2){ status="Not Paid" ; }else if(data[i].status==3){ status="Over Due" ; } var paidAmount=0.00;
    if(data[i].paid_amount !=null){ paidAmount=data[i].paid_amount; } var no=i+1; // row+="<tr><td>" +data[i].id+"</td>
    <td>"+data[i].amount+"</td>
    <td>"+data[i].branch_id+"</td>
    <td>"+data[i].paid_amount+"</td>
    <td>"+data[i].installment_date+"</td>
    <td>"+status+"</td>
    </tr>";
    row+="<tr>
        <td>"+ no +"</td>
        <td>"+data[i].amount+"</td>
        <td>"+data[i].total_amount_to_pay+"</td>
        <td>"+paidAmount+"</td>
        <td>"+data[i].installment_date+"</td>
        <td>"+status+"</td>
    </tr>";

    }
    },
    complete: function() {
    $('#installmetTableBody').append(row);
    $('.modelView').modal();
    }
    });

    });

    });

    </script> --}}

    @endsection
