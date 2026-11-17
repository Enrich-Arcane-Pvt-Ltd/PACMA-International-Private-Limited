@extends('layouts.admin')
@section('page-title')
    {{ __('Receipts') }}
@endsection
@section('content')
    <section class="section">
        {{-- <div class="section-header">
        <h1>{{__('Receipts')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Receipt')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">


                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Receipts') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Receipt') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Manage Receipts') }}</h4>
                                @can('create receipt')
                                    <div class="col-auto">
                                        <a href="#" data-size="xl" data-url="{{ route('custom_receipt.create') }}"
                                            data-ajax-popup="true" data-title="{{ __('New Receipts') }}"
                                            class="btn btn-icon icon-left btn-primary">
                                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                            <span class="btn-inner--text"> {{ __('New Receipt') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-body p-0">
                                <div id="table-1_wrapper"
                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-flush" id="custom_receipt_table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th> {{ __('Receipt #') }}</th>
                                                            <th> {{ __('Receipt From') }}</th>
                                                            <th> {{ __('Phone No') }}</th>
                                                            <th> {{ __('Date') }}</th>
                                                            <th> {{ __('Description') }}</th>
                                                            <th> {{ __('Amount') }} ({{ $currencySymbol }})</th>
                                                            <th class="text-right"> {{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($receipts as $receipt)
                                                            <tr>
                                                                <td class="font-style">
                                                                    {{ $receipt->receipt_code ?? '--' }}
                                                                </td>
                                                                <td class="font-style">
                                                                    @if ($receipt->receipt_type == 'supplier')
                                                                        {{ $receipt->vender->name ?? '' }}
                                                                    @elseif ($receipt->receipt_type == 'customer')
                                                                        {{ $receipt->customer->name ?? '' }}
                                                                    @elseif ($receipt->receipt_type == 'other')
                                                                        {{ $receipt->receipt_from ?? '' }}
                                                                    @elseif ($receipt->receipt_type == 'cash')
                                                                        Cash
                                                                    @endif
                                                                </td>
                                                                <td class="font-style">
                                                                    @if ($receipt->receipt_type == 'supplier')
                                                                        {{ $receipt->vender->contact_phone ?? '' }}
                                                                    @elseif ($receipt->receipt_type == 'customer')
                                                                        {{ $receipt->customer->phone ?? '' }}
                                                                    @elseif ($receipt->receipt_type == 'other')
                                                                        {{ $receipt->receipt_from_phone ?? '' }}
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </td>
                                                                <td class="font-style"> {{ $receipt->issue_date }} </td>
                                                                <td class="font-style"> {{ $receipt->description }} </td>
                                                                <td class="font-style">
                                                                    {{ number_format($receipt->paid_amount ?? 0, 2) }}
                                                                    {{-- {{ \Auth::user()->priceFormat($receipt->paid_amount) }} --}}
                                                                </td>
                                                                <td class="action text-right">
                                                                    @can('edit receipt')
                                                                        <a href="#" data-size="xl"
                                                                            data-url="{{ route('custom_receipt.edit', $receipt->id) }}"
                                                                            data-ajax-popup="true" data-toggle="tooltip"
                                                                            data-title="{{ __('Edit Receipts') }}"
                                                                            class="btn btn-primary btn-action mr-1">
                                                                            <i class="fas fa-pencil-alt mt-1"></i>
                                                                        </a>
                                                                    @endcan

                                                                    @can('print receipt')
                                                                        <button type="button"
                                                                            class="btn btn-info btn-action mr-1 view"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Receipt View') }}"
                                                                            value="{{ $receipt->id }}"><i
                                                                                class="fas fa-eye"></i></button>
                                                                        <button type="button"
                                                                            class="btn btn-info btn-action mr-1 print"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Receipt Print') }}"
                                                                            value="{{ $receipt->id }}"><i
                                                                                class="fas fa-print"></i></button>
                                                                    @endcan

                                                                    @can('delete receipt')
                                                                        <a href="#" class="btn btn-danger btn-action"
                                                                            style="padding-top: 10px;" data-toggle="tooltip"
                                                                            data-original-title="{{ __('Delete') }}"
                                                                            data-confirm="{{ __('Are You Sure?') . ' | ' . __('This action can not be undone. Do you want to continue?') }}"
                                                                            data-confirm-yes="document.getElementById('delete-form-{{ $receipt->id }}').submit();">
                                                                            <i class="fas fa-trash"></i>
                                                                        </a>
                                                                        {!! Form::open([
                                                                            'method' => 'DELETE',
                                                                            'route' => ['custom_receipt.destroy', $receipt->id],
                                                                            'id' => 'delete-form-' . $receipt->id,
                                                                        ]) !!}
                                                                        {!! Form::close() !!}
                                                                    @endcan
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
            </div>

        </div>

    </section>
@endsection
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
    $(document).ready(function() {
        if (localStorage.getItem("custom_receipt_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("custom_receipt_page_no"));
        }
        var customReceiptTable = $('#custom_receipt_table').DataTable({
            order: [
                [0, "decs"]
            ]
        });
        customReceiptTable.page(pageNo).draw(false);

        $('#custom_receipt_table').on('page.dt', function() {
            var pageInfo = customReceiptTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("custom_receipt_page_no", pageNo);
        });

        $('.view').click(function() {
            var receiptId = $(this).val();
            var url = "{{ url('custom_receipt/receipt/') }}/" + receiptId + "/view";
            window.open(url, '_blank');
        });

        $('.print').click(function() {
            var receiptId = $(this).val();
            var url = "{{ url('custom_receipt/receipt/') }}/" + receiptId + "/print";
            window.open(url, '_blank');
        });

        // $('.remove').click(function(){
        //     var advanceId = $(this).val();
        //     Swal.fire({
        //         icon: 'error',
        //         title: 'Oops...',
        //         text: 'The email address you entered is not correct. Please check your email address!'
        //     });
        //     $.ajax({
        //         url:"{{ url('custom_receipt/remove') }}/"+advanceId,
        //         method: 'GET',
        //         dataType: 'json',
        //         success: function(data, textStatus, jqXHR){
        //             console.log(data);
        //             if(data != null){
        //                 if(data.advance_amount != null){
        //                     $('#advance_amount').val(data.advance_amount);
        //                     $('#advance_id').val(data.id);
        //                     $('#advanced_div').show(200);
        //                 } else {
        //                     $('#advance_amount').val(0);
        //                     $('#advance_id').val('');
        //                     $('#advanced_div').hide(200);
        //                 }
        //             }else {
        //                 $('#advance_amount').val(0);
        //                 $('#advance_id').val('');
        //                 $('#advanced_div').hide(200);
        //             }
        //         }
        //     });
        // });
    });
</script>
