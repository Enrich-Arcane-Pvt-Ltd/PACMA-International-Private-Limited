@extends('layouts.admin')
@section('page-title')
{{__('Debit Note')}}
@endsection
{{-- @push('script-page')
    <script>
        $(document).on('change', '#bill', function () {

            var id = $(this).val();
            var url = "{{route('bill.get')}}";

$.ajax({
url: url,
type: 'get',
cache: false,
data: {
'bill_id': id,

},
success: function (data) {
$('#amount').val(data)
},

});

})
</script>
@endpush --}}
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Debit Note')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Debit Note')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Debit Note') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Debit Note')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Debit Note')}}</h4>
                            @can('create debit note')
                            <a href="#" data-url="{{ route('bill.custom.debit.note') }}" data-ajax-popup="true"
                                data-title="{{__('Create New Debit Note')}}" class="btn btn-icon icon-left btn-primary">
                                <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                <span class="btn-inner--text"> {{__('Create')}}</span>
                            </a>
                            @endcan
                        </div>
                    </div>
                    <div>
                        {{-- <button type="button" id="print_debit_note" class="btn btn-primary btn-action mr-1 float-right">Print Debit Note</button> --}}
                        {{-- <button type="button" id="view_debit_note" class="btn btn-primary btn-action mr-1 float-right">View Debit Note</button> --}}
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
                                                        <th> {{__('Debit Note #')}}</th>
                                                        <th> {{__('Vendor / Customer')}}</th>
                                                        <th> {{__('Date')}}</th>
                                                        <th> {{__('Amount')}}</th>
                                                        <th> {{__('Quantity')}}</th>
                                                        <th> {{__('Description')}}</th>
                                                        <th class="text-right"> {{__('Action')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @foreach ($bills as $bill)
                                                    @if(!empty($bill->debitNotes))
                                                    @foreach ($bill->debitNotes as $debitNote)

                                                    <tr class="font-style">
                                                        <td>
                                                            <a class="btn btn-outline-primary"
                                                                href="{{ route('bill.show',$debitNote->bill) }}">{{ AUth::user()->debitNoteNumberFormat($debitNote->id) }}
                                                            </a>
                                                        </td>
                                                        {{-- <td>{{ (!empty($bill->vender)?$bill->vender->name:'-') }}
                                                        {{ (!empty($bill->customer)?$bill->customer->name:'-') }}</td>
                                                        --}}
                                                        <td>{{ $debitNote->debitVendor->name ?? ''}}
                                                            {{ $debitNote->debitCustomer->name ?? '' }}</td>
                                                        <td>{{ Auth::user()->dateFormat($debitNote->date) }}</td>
                                                        <td>{{ Auth::user()->priceFormat($debitNote->amount) }}</td>
                                                        <td>{{ ($debitNote->quantity) }}</td>
                                                        <td>{{!empty($debitNote->description)?$debitNote->description:'-'}}
                                                        </td>
                                                        <td class="text-right">
                                                            {{-- @can('edit debit note') --}}
                                                            <a href="{{ route('debitNote.print',['id'=> $debitNote->id, 'command'=>'print'] ) }}"
                                                                target="_blank" data-toggle="tooltip"
                                                                data-original-title="{{__('Print')}}"
                                                                class="btn btn-info btn-action mr-1">
                                                                <i class="fas fa-print" style="padding-top:5px;"></i>
                                                            </a>
                                                            {{-- @endcan --}}
                                                            @can('edit debit note')
                                                            <a data-url="{{ route('bill.edit.debit.note',[$debitNote->bill,$debitNote->id]) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{__('Edit Debit Note')}}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Edit')}}" href="#"
                                                                class="btn btn-primary btn-action mr-1"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Edit')}}">
                                                                <i class="fas fa-pencil-alt"
                                                                    style="padding-top:5px;"></i>
                                                            </a>
                                                            @endcan
                                                            @can('delete debit note')
                                                            <a href="#!" class="btn btn-danger btn-action "
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Delete')}}"
                                                                data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{$debitNote->id}}').submit();">
                                                                <i class="fas fa-trash" style="padding-top:5px;"></i>
                                                            </a>
                                                            {!! Form::open(['method' => 'DELETE', 'route' =>
                                                            array('bill.delete.debit.note',
                                                            $debitNote->bill,$debitNote->id),'id'=>'delete-form-'.$debitNote->id])
                                                            !!}
                                                            {!! Form::close() !!}
                                                            @endcan
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

        // $('#print_debit_note').click(function(){
        //     window.open("{{ route('debitNote.print',['id'=> $bill->id, 'command'=>'print'] ) }}");
        // });

        // $('#view_debit_note').click(function(){
        //     window.open("{{ route('debitNote.print',['id'=> $bill->id, 'command'=>'view'] ) }}");
        // });
    });

</script>
