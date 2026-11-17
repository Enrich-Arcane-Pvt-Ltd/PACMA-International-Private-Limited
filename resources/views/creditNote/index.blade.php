@extends('layouts.admin')
@section('page-title')
{{__('Credit Note')}}
@endsection
@push('script-page')
<script>
    $(document).on('change', '#invoice', function () {

        var id = $(this).val();
        var url = "{{route('invoice.get')}}";

        $.ajax({
            url: url,
            type: 'get',
            cache: false,
            data: {
                'id': id,

            },
            success: function (data) {
                $('#amount').val(data)
            },

        });

    })

</script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Credit Note')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Credit Note')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Credit Note') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Credit Note')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Credit Note')}}</h4>
                            @can('create credit note')
                            <a href="#" data-url="{{ route('invoice.custom.credit.note') }}" data-ajax-popup="true"
                                data-title="{{__('Create New Credit Note')}}"
                                class="btn btn-icon icon-left btn-primary">
                                <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                <span class="btn-inner--text"> {{__('Create')}}</span>
                            </a>
                            @endcan
                        </div>
                    </div>
                    <div>
                        {{-- <button type="button" id="print_credit_note" class="btn btn-primary btn-action mr-1 float-right">Print Credit Note</button> --}}
                        {{-- <button type="button" id="view_credit_note" class="btn btn-primary btn-action mr-1 float-right">View Credit Note</button> --}}
                    </div>
                    <div class="card-body">
                        <div class="card-body p-0">
                            <div id="table-1_wrapper"
                                class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-flush" id="dataTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="text-align: center"> {{__('Credit Note #')}}</th>
                                                        <th style="text-align: center"> {{__('Customer')}}</th>
                                                        <th style="text-align: center"> {{__('Date')}}</th>
                                                        <th style="text-align: center"> {{__('Amount')}}</th>
                                                        <th style="text-align: center"> {{__('Quantity')}}</th>
                                                        <th style="text-align: center"> {{__('Description')}}</th>
                                                        <th class="text-right" style="text-align: center">
                                                            {{__('Action')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($invoices as $invoice)

                                                    @if(!empty($invoice->creditNote))
                                                    @foreach ($invoice->creditNote as $creditNote)
                                                    <tr class="font-style">
                                                        <td style="text-align: center">
                                                            <a class="btn btn-outline-primary"
                                                                href="{{ route('invoice.show',$creditNote->invoice) }}">{{ AUth::user()->creditNoteNumberFormat($creditNote->id) }}
                                                            </a>
                                                        </td>
                                                        <td style="text-align: center">
                                                            {{ (!empty($invoice->customer)?$invoice->customer->name:'-') }}
                                                        </td>
                                                        <td style="text-align: center">
                                                            {{ Auth::user()->dateFormat($creditNote->date) }}</td>
                                                        <td style="text-align: center">
                                                            {{ Auth::user()->priceFormat($creditNote->amount) }}</td>
                                                        <td style="text-align: center">{{($creditNote->quantity) }}</td>
                                                        <td style="text-align: center">
                                                            {{!empty($creditNote->description)?$creditNote->description:'-'}}
                                                        </td>
                                                        <td class="text-right" style="text-align: center">
                                                            {{-- @can('edit debit note') --}}
                                                            <a href="{{ route('creditNote.print',['id'=> $creditNote->id, 'command'=>'print'] ) }}"
                                                                target="_blank" data-toggle="tooltip"
                                                                data-original-title="{{__('Print')}}"
                                                                class="btn btn-info btn-action mr-1">
                                                                <i class="fas fa-print" style="padding-top:5px;"></i>
                                                            </a>
                                                            {{-- @endcan --}}
                                                            @can('edit credit note')
                                                            <a data-url="{{ route('invoice.edit.credit.note',[$creditNote->invoice,$creditNote->id]) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{__('Edit Credit Note')}}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Edit')}}" href="#"
                                                                class="btn btn-primary btn-action mr-1"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Edit')}}">
                                                                <i class="fas fa-pencil-alt"
                                                                    style="padding-top:5px;"></i>
                                                            </a>
                                                            @endcan
                                                            @can('delete credit note')
                                                            <a href="#!" class="btn btn-danger btn-action "
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Delete')}}"
                                                                data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{$creditNote->id}}').submit();">
                                                                <i class="fas fa-trash" style="padding-top:5px;"></i>
                                                            </a>
                                                            {!! Form::open(['method' => 'DELETE', 'route' =>
                                                            array('invoice.delete.credit.note',
                                                            $creditNote->invoice,$creditNote->id),'id'=>'delete-form-'.$creditNote->id])
                                                            !!}
                                                            {!! Form::close() !!}
                                                            @endcan
                                                            {{-- @can('print credit note')
                                                                            <a href="#!" class="btn btn-danger btn-action " data-toggle="tooltip" data-original-title="{{__('Delete')}}"
                                                            data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                            data-confirm-yes="document.getElementById('delete-form-{{$creditNote->id}}').submit();">
                                                            <i class="fas fa-trash" style="padding-top:5px;"></i>
                                                            </a>
                                                            {!! Form::open(['method' => 'DELETE', 'route' =>
                                                            array('invoice.delete.credit.note',
                                                            $creditNote->invoice,$creditNote->id),'id'=>'delete-form-'.$creditNote->id])
                                                            !!}
                                                            {!! Form::close() !!}
                                                            @endcan --}}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @endif
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
    // var invoice = <php echo json_encode($invoice) ?>;
    $(document).ready(function () {
        // console.log(invoice);

        // $('#print_credit_note').click(function(){
        //     window.open("{{ route('creditNote.print',['id'=> $invoice->id, 'command'=>'print'] ) }}");
        // });

        // $('#view_credit_note').click(function(){
        //     window.open("{{ route('creditNote.print',['id'=> $invoice->id, 'command'=>'view'] ) }}");
        // });
    });

</script>
