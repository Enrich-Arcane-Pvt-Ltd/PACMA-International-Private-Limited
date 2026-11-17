@extends('layouts.admin')
@section('page-title')
{{__('Attendance Sheet')}}
@endsection
@push('script-page')

<style>
    table tfoot {
        display: table-row-group;
    }

    table td,
    th {
        border: 1px solid;
        text-align: center;
    }

    table td.text-left,
    th.text-left {
        padding-left: 10px;
    }

    .wi-2 {
        width: 2.3%;
    }

    .wi-8 {
        width: 8%;
    }

    .wi-22 {
        width: 22%;
    }

    .hi-25 {
        height: 25px;
    }

    .hi-35 {
        height: 45px;
    }

    .hi-45 {
        height: 45px;
    }

    .btn-ash {
        width: 6rem;
        border-radius: 12px;
        font-size: medium;
        color: #828282;
    }

    @page {
        size: landscape;
    }

    @media print {
        .header-print {
            display: table-header-group;
        }
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

        var filename = 'Attendance Sheet';
        if ($('#duration').val() != undefined) {
            var duration = $('#duration').val();
        } else {
            var duration = "";
        }

        if ($('#center').length > 0) {
            var center = $('#center').val();
        } else {
            var center = "";
        }

        $('#print').click(function () {
            $('#printableArea').printThis({
                importCSS: true,
                importStyle: true, //thrown in for extra measure
                // loadCSS: "/app/css/printListTable.css",
                header: '<h3>' + filename + center + duration + '</h3>'
            });
        });

        // $('#export').click(function(){
        //     $("#printableArea").table2excel({
        //         exclude:".noExl",
        //         name:"Worksheet Name",
        //         filename:filename,
        //         fileext:".xlsx",
        //         preserveColors:true
        //     });

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

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Attendance Sheet')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Loan')}}</div>
        <div class="breadcrumb-item">{{__('Reports')}}</div>
        <div class="breadcrumb-item">{{__('Attendance Sheet')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Attendance Sheet') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Loan')}}</div>
                                <div class="breadcrumb-item">{{__('Reports')}}</div>
                                <div class="breadcrumb-item">{{__('Attendance Sheet')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('attendance_sheet.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
                                </div>
                                <div class="col-md-3">
                                    <label for="start_month">Start Month</label>
                                    <input class="form-control font-style" name="start_month" type="month"
                                        value="{{ $start_month ?? '' }}" id="start_month" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="end_month">End Month</label>
                                    <input class="form-control font-style" name="end_month" type="month"
                                        value="{{ $end_month ?? '' }}" id="end_month" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="center_id">Center</label>
                                    @if (isset($attendanceCenters))
                                    @if ($attendanceCenters != '')
                                    <input type="hidden" id="center"
                                        value="{{ __(' Of ').App\Center::centerName($attendanceCenters)[0] }}">
                                    @endif
                                    @endif
                                    {{ Form::select('center_id', $centers, $attendanceCenters ?? '', array('id' => 'center_id','class' => 'form-control font-style', 'required'=>'required')) }}
                                </div>
                            </div>
                            <div class="row m-3" style="float: right;">
                                <div class="col-auto">
                                    <input class="btn font-style btn-outline-primary" type="submit" value="Search">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card br-4 cus_show">
                        <div class="card-body">
                            <button type="button" class="btn btn-ash mb-2" id="print">Print</button>
                            <table style="width: 100%" id="printableArea">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="wi-8"> {{__('Customer No')}}</th>
                                        <th rowspan="2" class="wi-22"> {{__('Customer Name')}}</th>
                                        <th colspan="5" class="hi-25"> {{ $attendMonths[0] ?? ''}}</th>
                                        <th colspan="5" class="hi-25"> {{ $attendMonths[1] ?? '' }}</th>
                                        <th colspan="5" class="hi-25"> {{ $attendMonths[2] ?? '' }}</th>
                                        <th colspan="5" class="hi-25"> {{ $attendMonths[3] ?? '' }}</th>
                                        <th colspan="5" class="hi-25"> {{ $attendMonths[4] ?? '' }}</th>
                                        <th colspan="5" class="hi-25"> {{ $attendMonths[5] ?? '' }}</th>
                                    </tr>
                                    <tr>
                                        @for( $i = 0; $i < 30; $i++ ) <th class="wi-2 hi-45">
                                            </th>
                                            @endfor
                                            {{-- <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>

                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>

                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>

                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>

                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>

                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th>
                                            <th class="wi-2 hi-25"></th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($showTable == 'Yes')
                                    <input type="hidden" id="duration"
                                        value="{{ __(' - From ').Auth::user()->monthFormat($start_month).__(' - To ').Auth::user()->monthFormat($end_month) }}">
                                    @foreach ($centerInfoLists as $centerInfoList)
                                    @foreach ($centerInfoList->groups as $groups)
                                    <tr>
                                        <th colspan="32" class="hi-35" style="padding-left: 25px; text-align: left;">
                                            Group No = {{ $groups->name }}</th>
                                    </tr>
                                    @foreach ($groups->groupMembers as $groupMember)
                                    <tr>
                                        <td>@if(isset($groupMember->customers)){{ Auth::user()->customerNumberFormat($groupMember->customers->id) }}
                                            @endif</td>
                                        <td class="text-left">
                                            @if(isset($groupMember->customers)){{ $groupMember->customers->name }}@endif
                                        </td>
                                        @for( $i = 0; $i < 30; $i++ ) <td class="hi-45">
                                            </td>
                                            @endfor
                                            {{-- <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>

                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>

                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>

                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>

                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>

                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td> --}}
                                    </tr>
                                    @endforeach
                                    @endforeach
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="32">No Data</td>
                                    </tr>
                                    @endif
                                </tbody>
                                @if($showTable == 'Yes')
                                <tfoot>
                                    <tr>
                                        <td colspan="32 hi-35"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-left">Present Member</td>
                                        @for( $i = 0; $i < 30; $i++ ) <td class="hi-45">
                                            </td>
                                            @endfor
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-left">Inform Member</td>
                                        @for( $i = 0; $i < 30; $i++ ) <td class="hi-45">
                                            </td>
                                            @endfor
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-left">Late Member</td>
                                        @for( $i = 0; $i < 30; $i++ ) <td class="hi-45">
                                            </td>
                                            @endfor
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-left">Absent Member</td>
                                        @for( $i = 0; $i < 30; $i++ ) <td class="hi-45">
                                            </td>
                                            @endfor
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-left">Total Member</td>
                                        @for( $i = 0; $i < 30; $i++ ) <td class="hi-45">
                                            </td>
                                            @endfor
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-left">Member Persentage</td>
                                        @for( $i = 0; $i < 30; $i++ ) <td class="hi-45">
                                            </td>
                                            @endfor
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-left">New Member</td>
                                        @for( $i = 0; $i < 30; $i++ ) <td class="hi-45">
                                            </td>
                                            @endfor
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-left">Left Member</td>
                                        @for( $i = 0; $i < 30; $i++ ) <td class="hi-45">
                                            </td>
                                            @endfor
                                    </tr>
                                    <tr>
                                        <td colspan="32 hi-35"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="32 hi-35"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-left">Signature of Center Manager</td>
                                        @for( $i = 0; $i < 30; $i++ ) <td class="hi-45">
                                            </td>
                                            @endfor
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-left">Signature of Field Manager</td>
                                        @for( $i = 0; $i < 30; $i++ ) <td class="hi-45">
                                            </td>
                                            @endfor
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-left">Signature of Branch Manager</td>
                                        @for( $i = 0; $i < 30; $i++ ) <td class="hi-45">
                                            </td>
                                            @endfor
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
</section>

<script>
    $('body').on('change', '#start_month', function () {
        $('#end_month').val('');
    });

</script>
@endsection
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    var loanType = <php echo json_encode($loanType); ?>;
    var loanCenter = <php echo json_encode($loanCenter); ?>;
    var loanGroup = <php echo json_encode($loanGroup); ?>;
  $(document).ready(function(){
      if(loanType == 2){
        $('#center_div').show();
        $('#center_id').val(loanCenter).trigger('change');
            if(loanCenter != ''){
                $('#group_div').show();
                $('#group_id').val(loanGroup);
            }
      } else {
        $('#center_div').hide();
        $('#group_div').hide();
      }

    $('.orderModel').on('click', function() {
            const loanId = $(this).attr("data-id");
            let row = "";
            let status;
                $.ajax({
                    type: 'GET',
                    url: '{{("installment/getLoanInstallment/")}}' + loanId,
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

    $('#loan_type').change(function(){
    var loanType = $(this).val();
    if(loanType == 2){
    $('#center_div').show(200);
    } else {
    $('#center_div').hide(200);
    $('#center_id').val('').trigger('change');
    }
    });

    $('#center_id').change(function(){
    var centerId = $(this).val();
    if(centerId != ''){
    $.ajax({
    url: "{{ url('loan_outstanding_report/get_groups') }}/"+centerId,
    method: 'GET',
    dataType: 'json',
    success: function (data){
    if(data != null){
    $('#group_id').html('');
    $('#group_id').append('<option value="">Select Group</option>');
    for(i = 0; i < data.length; i++){ $('#group_id').append('<option value="'+data[i].id+'">'+data[i].name+'</option>');
        }
        } else {
        $('#group_id').html('');
        $('#group_id').append('<option value="">No Groups to Select</option>');
        }

        }
        });
        $('#group_div').show(200);
        } else {
        $('#group_div').hide(200);
        $('#group_id').html('');
        $('#group_id').append('<option value="">No Groups to Select</option>');
        }
        });
        });

        </script> --}}
