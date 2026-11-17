@extends('layouts.admin')
@section('page-title')
{{__('Pawning Ticket')}}
@endsection
@push('script-page')
<script>
    $(document).on('change', '.status_change', function() {
        var status = this.value;
        var url = $(this).data('url');
        $.ajax({
            url: url + '?status=' + status,
            type: 'GET',
            cache: false,
            success: function(data) {

            },
        });
    });
</script>
@endpush
@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{__('Pawning Ticket')}}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{route('customer.dashboard')}}">{{__('Dashboard')}}</a></div>
            <div class="breadcrumb-item"><a href="{{route('customer.proposal')}}">{{__('Pawning')}}</a></div>
            <div class="breadcrumb-item">{{__('Pawning Ticket')}}</div>
        </div>
    </div>

    <div class="row mb-10">
        <div class="col-lg-12">
            <div class="text-right">
                <a href="{{ route('pawning.pirint_ticket', $id) }}" target="_blank" class="btn btn-primary btn-action mr-1 float-right">
                    <i class="fas fa-print"></i> {{__('Print Ticket')}}
                </a>
            </div>
        </div>
    </div>

    <div class="section-body">
        <div class="invoice">
            <div class="invoice-print">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="row">
                            <div class="col-md-4">
                                <img src="{{$img}}" style="max-width: 250px" />
                            </div>
                            <div class="col-md-4">
                                Pawning Ticket
                            </div>

                            <div class="col-md-4">
                                <div data-v-f2a183a6="">
                                    <p data-v-f2a183a6="">@if($settings['company_name']){{$settings['company_name']}}@endif</p>
                                    <p data-v-f2a183a6="">
                                        @if($settings['company_address']){{$settings['company_address']}}@endif
                                        @if($settings['company_city']) <br> {{$settings['company_city']}}, @endif @if($settings['company_state']){{$settings['company_state']}}@endif @if($settings['company_zipcode']) - {{$settings['company_zipcode']}}@endif
                                        @if($settings['company_country']) <br>{{$settings['company_country']}}@endif <br>
                                    </p>
                                </div>
                            </div>
                        </div>


                        <hr>

                        <div class="row">
                            <div class="col-md-3">
                                <strong>{{__('Pawning ID')}} :</strong>
                                {{$pawningTicket->id}}
                            </div>
                            <div class="col-md-3">
                                <strong>{{__('Name')}} :</strong>
                                {{$pawningTicketDetails->customer->name}}
                            </div>

                            <div class="col-md-3">
                                <strong>{{__('Address')}}:</strong>
                                {{$pawningTicketDetails->customer->address}}
                            </div>

                            <div class="col-md-3">
                                <strong>{{__('Phone')}}:</strong>
                                {{$pawningTicketDetails->customer->phone}}
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-3">
                                <strong>{{__('Time Period')}}:</strong>
                                {{$pawningTicket->time_period_id .' Months'}}
                            </div>
                            <div class="col-md-3">
                                <strong>{{__('Installment Date')}}:</strong>
                                {{' Every Month '}}{{$pawningTicket->installment_date}}
                            </div>

                            <div class="col-md-3">
                                <strong>{{__('Issue Date')}}:</strong>
                                {{$pawningTicket->issue_date}}
                            </div>

                            <div class="col-md-3">
                                <strong>{{__('Due Date')}}:</strong>
                                {{$pawningTicket->due_date}}
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="section-title">{{__('Product Summary')}}</div>
                        <p class="section-lead">{{__('All items here cannot be deleted.')}}</p>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-md">
                                <tr>
                                    <th>{{__('Item')}}</th>
                                    <th>{{__('Gold Type')}}</th>
                                    <th>{{__('Weight')}}</th>
                                    <th>{{__('Amount Financed')}}</th>
                                    <th>{{__('Amount Of Payment')}}</th>
                                    <th>{{__('Interest Amount')}}</th>
                                    <th>{{__('Total of Payments')}}</th>
                                    <th>{{__('Prepayment')}}</th>
                                    {{-- <th class="text-center">{{__('Image')}}</th> --}}
                                </tr>

                                @foreach($pawningItemDetails as $pawningItemDetail)
                                <tr>
                                        <td>{{$pawningItemDetail->jewellery->name}}</td>
                                        <td>{{$pawningItemDetail->goldType->gold_rate_type}}</td>
                                        <td>{{$pawningItemDetail->weight .'g'}}</td>
                                        <td>{{'LKR '.number_format($pawningItemDetail->amount_financed, 2)}}</td>
                                        <td>{{'LKR '.number_format($pawningItemDetail->amount_payout, 2) }}</td>
                                        <td>{{'LKR '.number_format($pawningItemDetail->financed_charge, 2)}}</td>
                                        <td>{{'LKR '.number_format($pawningItemDetail->total_of_payment, 2)}}</td>
                                        <td>{{'LKR '.number_format($pawningItemDetail->prepayment, 2)}}</td>
                                        {{-- <td></td> --}}
                                </tr>
                                @endforeach

                                <tfoot>


                                    <tr>
                                        <td colspan="6"></td>
                                        <td class="text-right"><b>{{__('Full Amount Financed')}}</b></td>
                                        {{-- <td class="text-right">{{\Auth::user()->priceFormat($proposal->getSubTotal())}}</td> --}}
                                        <td class="text-right">{{'LKR '.number_format($pawningTicket->full_amount_financed, 2)}}</td>
                                    </tr>

                                    <tr>
                                        <td colspan="6"></td>
                                        <td class="text-right"><b>{{__('Full Amount of Payment')}}</b></td>
                                        {{-- <td class="text-right">{{\Auth::user()->priceFormat($proposal->getSubTotal())}}</td> --}}
                                        <td class="text-right">{{'LKR '.number_format($pawningTicket->full_amount_payout, 2)}}</td>
                                    </tr>

                                    <tr>
                                        <td colspan="6"></td>
                                        <td class="text-right"><b>{{__('Full Interest Amount')}}</b></td>
                                        {{-- <td class="text-right">{{\Auth::user()->priceFormat($proposal->getTotal())}}</td> --}}
                                        <td class="text-right">{{'LKR '.number_format($pawningTicket->full_financed_charge, 2)}}</td>
                                    </tr>

                                    <tr>
                                        <td colspan="6"></td>
                                        <td class="text-right"><b>{{__('Full Total of Payments')}}</b></td>
                                        {{-- <td class="text-right">{{\Auth::user()->priceFormat($proposal->getTotal())}}</td> --}}
                                        <td class="text-right">{{'LKR '.number_format($pawningTicket->full_total_of_payment, 2)}}</td>
                                    </tr>

                                    <tr>
                                        <td colspan="6"></td>
                                        <td class="text-right"><b>{{__('Full Prepayment')}}</b></td>
                                        {{-- <td class="text-right">{{\Auth::user()->priceFormat($proposal->getTotal())}}</td> --}}
                                        <td class="text-right">{{'LKR '.number_format($pawningTicket->full_prepayment, 2)}}</td>
                                    </tr>
                                </tfoot>
                            </table>
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
