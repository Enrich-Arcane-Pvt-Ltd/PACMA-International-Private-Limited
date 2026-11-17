@extends('layouts.admin')
@section('page-title')
    {{ __('GRN Detail') }}
@endsection
@push('script-page')
    <script>
        $(document).on('click', '#shipping', function() {
            var url = $(this).data('url');
            var is_display = $("#shipping").is(":checked");
            $.ajax({
                url: url,
                type: 'get',
                data: {
                    'is_display': is_display,
                },
                success: function(data) {
                    // console.log(data);
                }
            });
        })
    </script>
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('GRN') }}</h1>
            <div class="section-header-breadcrumb">

                <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('grn.index') }}">{{ __('GRN') }}</a></div>


                <div class="breadcrumb-item">{{ AUth::user()->grnNumberFormat($grn->grn_id) }}</div>
            </div>
        </div>
        @can('send bill')
            @if ($grn->status != 4)
                <div class="row">
                    <div class="col-12">
                        <div class="activities">
                            <div class="activity">
                                <div class="activity-icon bg-primary text-white shadow-primary">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="activity-detail">
                                    <div class="mb-2">
                                        <span class="text-job text-primary">
                                            <h6>{{ __('Create Bill') }}</h6>
                                        </span>
                                        @can('edit bill')
                                            <a href="{{ route('bill.edit', $grn->id) }}"
                                                class="btn btn-primary btn-action mr-1 float-right">
                                                {{ __('Edit') }}
                                            </a>
                                        @endcan
                                    </div>
                                    <p>{{ __('Status') }} : <a href="#">{{ __('Created on ') }}
                                            {{ \Auth::user()->dateFormat($grn->grn_date) }} </a></p>
                                </div>
                            </div>
                            <div class="activity">
                                <div class="activity-icon bg-primary text-white shadow-primary">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="activity-detail">
                                    <div class="mb-2">
                                        <span class="text-job text-primary">
                                            <h6>{{ __('Send Bill') }}</h6>
                                        </span>

                                        @if ($grn->status != 0)
                                            <p>{{ __('Status') }} : <a href="#">{{ __('Sent on') }}
                                                    {{ \Auth::user()->dateFormat($grn->send_date) }} </a></p>
                                        @else
                                            @can('send bill')
                                                <a href="{{ route('bill.sent', $bgrn->id) }}"
                                                    class="btn btn-primary btn-action mr-1 float-right">
                                                    {{ __('Mark Sent') }}
                                                </a>
                                            @endcan
                                            <p>{{ __('Status') }} : <a href="#">{{ __('Not Sent') }} </a></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="activity">
                                <div class="activity-icon bg-primary text-white shadow-primary">
                                    <i class="far fa-money-bill-alt"></i>
                                </div>
                                <div class="activity-detail">
                                    <div class="mb-2">
                                        <span class="text-job text-primary">
                                            <h6>{{ __('Get Paid') }}</h6>
                                        </span>
                                    </div>
                                    @if ($grn->status != 0)
                                        @can('create payment bill')
                                            <a href="#!" data-url="{{ route('bill.payment', $grn->id) }}"
                                                data-ajax-popup="true" data-title="{{ __('Add Payment') }}"
                                                class="btn btn-primary btn-action mr-1 float-right">
                                                {{ __('Add Payment') }}
                                            </a>
                                        @endcan
                                    @endif
                                    <p>{{ __('Status') }} : <a href="#">{{ __('Awaiting payment') }} </a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endcan
        <div class="section-body">
            <div class="invoice">
                <div class="invoice-print">
                    @if (\Auth::user()->type == 'company')
                        @if ($grn->status != 0)
                            <div class="row mb-10">
                                <div class="col-lg-12">
                                    <div class="text-right">
                                        @if (!empty($billPayment))
                                            <a href="#" data-url="{{ route('bill.debit.note', $bgrn->id) }}"
                                                data-ajax-popup="true" data-title="{{ __('Add Debit Note') }}"
                                                data-toggle="tooltip" data-original-title="{{ __('Debit Note') }}"
                                                class="btn btn-primary btn-action mr-1 float-right">
                                                {{ __('Add Debit Note') }}
                                            </a>
                                        @endif

                                        <a href="{{ route('bill.pdf', Crypt::encrypt($grn->id)) }}" target="_blank"
                                            class="btn btn-primary btn-action mr-1 float-right">
                                            {{ __('Create Bill') }}
                                        </a>
                                        <a href="{{ route('bill.pdf', Crypt::encrypt($grn->id)) }}" target="_blank"
                                            class="btn btn-primary btn-action mr-1 float-right">
                                            {{ __('Print') }}
                                        </a>
                                        <a href="{{ route('bill.pdf', Crypt::encrypt($grn->id)) }}" target="_blank"
                                            class="btn btn-primary btn-action mr-1 float-right">
                                            {{ __('Download') }}
                                        </a>

                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="row mb-10">
                            <div class="col-lg-12">
                                <div class="text-right">
                                    <a href="#" data-url="{{ route('vender.bill.send', $grn->id) }}"
                                        data-ajax-popup="true" data-title="{{ __('Send Bill') }}"
                                        class="btn btn-primary btn-action mr-1 float-right">
                                        {{ __('Send Mail') }}
                                    </a>
                                    <a href="{{ route('bill.pdf', Crypt::encrypt($grn->id)) }}" target="_blank"
                                        class="btn btn-primary btn-action mr-1 float-right">
                                        {{ __('Download') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="invoice-title">
                                <h2>{{ __('GRN') }}</h2>
                                <div class="invoice-number">{{ AUth::user()->grnNumberFormat($grn->grn_id) }}</div>
                                <div class="invoice-number">
                                    {{ AUth::user()->grnNumberFormat($grn->supplier_invoice_number) }}</div>
                            </div>
                            <hr>
                            <div class="row">
                                @if (!empty($vendor->billing_name))
                                    <div class="col-md-6">
                                        <address class="font-style">
                                            <strong>{{ __('Delivery Address') }}:</strong><br>
                                            {{ !empty($vendor->billing_name) ? $vendor->billing_name : '' }}<br>
                                            {{ !empty($vendor->billing_phone) ? $vendor->billing_phone : '' }}<br>
                                            {{ !empty($vendor->billing_address) ? $vendor->billing_address : '' }}<br>
                                            {{ !empty($vendor->billing_zip) ? $vendor->billing_zip : '' }}<br>
                                            {{ !empty($vendor->billing_city) ? $vendor->billing_city : '' . ', ' }}
                                            {{ !empty($vendor->billing_state) ? $vendor->billing_state : '', ', ' }}
                                            {{ !empty($vendor->billing_country) ? $vendor->billing_country : '' }}
                                        </address>
                                    </div>
                                @endif
                                @if (\Utility::getValByName('shipping_display') == 'on')
                                    <div class="col-md-6 text-md-right">
                                        <address>
                                            <strong>{{ __('Shipped To') }}:</strong><br>
                                            {{ !empty($vendor->shipping_name) ? $vendor->shipping_name : '' }}<br>
                                            {{ !empty($vendor->shipping_phone) ? $vendor->shipping_phone : '' }}<br>
                                            {{ !empty($vendor->shipping_address) ? $vendor->shipping_address : '' }}<br>
                                            {{ !empty($vendor->shipping_zip) ? $vendor->shipping_zip : '' }}<br>
                                            {{ !empty($vendor->shipping_city) ? $vendor->shipping_city : '' . ', ' }}
                                            {{ !empty($vendor->shipping_state) ? $vendor->shipping_state : '', ', ' }}
                                            {{ !empty($vendor->shipping_country) ? $vendor->shipping_country : '' }}
                                        </address>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <!-- <div class="col-md-4">
                                            <address>
                                                <strong>{{ __('Status') }}:</strong><br>
                                                @if ($grn->status == 0)
    <span class="badge badge-primary">{{ __(\App\Grn::$statues[$grn->status]) }}</span>
@elseif($grn->status == 1)
    <span class="badge badge-warning">{{ __(\App\Grn::$statues[$grn->status]) }}</span>
@elseif($grn->status == 2)
    <span class="badge badge-danger">{{ __(\App\Grn::$statues[$grn->status]) }}</span>
@elseif($grn->status == 3)
    <span class="badge badge-info">{{ __(\App\Grn::$statues[$grn->status]) }}</span>
@elseif($grn->status == 4)
    <span class="badge badge-success">{{ __(\App\Grn::$statues[$grn->status]) }}</span>
    @endif
                                            </address>
                                        </div> -->
                                <div class="col-md-4 text-md-center">
                                    <address>
                                        <strong>{{ __('Received Date') }} :</strong><br>
                                        {{ \Auth::user()->dateFormat($grn->received_date) }}<br><br>
                                    </address>
                                </div>
                                <!-- <div class="col-md-4 text-md-right">
                                            <address>
                                                <strong>{{ __('Due Date') }} :</strong><br>
                                                {{ \Auth::user()->dateFormat($grn->due_date) }}<br><br>
                                            </address>
                                        </div> -->
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="section-title">{{ __('Product Summary') }}</div>
                            <p class="section-lead">{{ __('All items here cannot be deleted.') }}</p>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <tr>
                                        <th data-width="40">#</th>
                                        <th>{{ __('Product Code') }}</th>
                                        <th>{{ __('Ordered Quantity') }}</th>
                                        <th>{{ __('Received Quantity') }}</th>
                                        <th>{{ __('Unit Cost') }}</th>
                                        <th>{{ __('Tax') }}</th>
                                        <th>{{ __('Discount') }}</th>
                                        <th class="text-right" width="12%">{{ __('Total Price') }}<br><small
                                                class="text-danger">{{ __('(without discount)') }}</small></th>
                                    </tr>
                                    @php
                                        $totalQuantity = 0;
                                        $totalRate = 0;
                                        // $totalTaxPrice=0;
                                        $totalDiscount = 0;
                                        $taxesData = [];
                                    @endphp
                                    @if (count($iteams) > 0)
                                        @foreach ($iteams as $key => $iteam)
                                            @php
                                                $taxes = \Utility::tax($iteam->tax);
                                                $totalQuantity += $iteam->quantity;
                                                $totalRate += $iteam->price;
                                                $totalDiscount += $iteam->discount;
                                                foreach ($taxes as $taxe) {
                                                    $taxDataPrice = \Utility::taxRate(
                                                        $taxe->rate,
                                                        $iteam->price,
                                                        $iteam->quantity,
                                                    );
                                                    if (array_key_exists($taxe->name, $taxesData)) {
                                                        $taxesData[$taxe->name] =
                                                            $taxesData[$taxe->name] + $taxDataPrice;
                                                    } else {
                                                        $taxesData[$taxe->name] = $taxDataPrice;
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ !empty($iteam->product()) ? $iteam->product()->barcode : '' }}</td>
                                                <td>{{ $iteam->quantity }}</td>
                                                <td>{{ \Auth::user()->priceFormat($iteam->price) }}</td>
                                                {{-- <td>
                                                 <table>
                                                     @php $totalTaxRate = 0;
                                                     @endphp
                                                     @foreach ($taxes as $tax)
                                                         @php
                                                             $taxPrice=\Utility::taxRate($tax->rate,$iteam->price,$iteam->quantity);
                                                             $totalTaxPrice+=$taxPrice;
                                                         @endphp
                                                      <tr>
                                                             <td>{{$tax->name .' ('.$tax->rate .'%)'}}</td>
                                                             <td>{{\Auth::user()->priceFormat($taxPrice)}}</td>
                                                             </tr>
                                                       @endforeach
                                                </table>
                                             </td> --}}
                                                <td> {{ \Auth::user()->priceFormat($iteam->discount) }}</td>
                                                <td class="text-right">
                                                    {{ \Auth::user()->priceFormat($iteam->price * $iteam->quantity) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td><b>{{ __('Total') }}</b></td>
                                            <td><b>{{ $totalQuantity }}</b></td>
                                            <td><b>{{ \Auth::user()->priceFormat($totalRate) }}</b></td>
                                            {{-- <td><b>{{\Auth::user()->priceFormat($totalTaxPrice)}}</b></td> --}}
                                            <td><b>{{ \Auth::user()->priceFormat($totalDiscount) }}</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td class="text-right"><b>{{ __('Sub Total') }}</b></td>
                                            <td class="text-right">{{ \Auth::user()->priceFormat($grn->getSubTotal()) }}
                                            </td>
                                        </tr>
                                        {{-- @if ($bill->discount_apply == 1) --}}
                                        <tr>
                                            <td colspan="5"></td>
                                            <td class="text-right"><b>{{ __('Discount') }}</b></td>
                                            <td class="text-right">
                                                {{ \Auth::user()->priceFormat($grn->getTotalDiscount()) }}</td>
                                        </tr>
                                        {{-- @endif --}}
                                        {{-- // @if (!empty($taxesData))
                                    //     @foreach ($taxesData as $taxName => $taxPrice)
                                    //         <tr>
                                    //             <td colspan="5"></td>
                                    //             <td class="text-right"><b>{{$taxName}}</b></td>
                                    //             <td class="text-right">{{ \Auth::user()->priceFormat($taxPrice) }}</td>
                                    //         </tr>
                                    //     @endforeach
                                    // @endif --}}
                                        <tr>
                                            <td colspan="5"></td>
                                            <td class="text-right"><b>{{ __('Total') }}</b></td>
                                            <td class="text-right">{{ \Auth::user()->priceFormat($grn->getTotal()) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td class="text-right"><b>{{ __('Paid') }}</b></td>
                                            <td class="text-right">
                                                {{ \Auth::user()->priceFormat($grn->getTotal() - $grn->getDue() - $grn->grnTotalDebitNote()) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td class="text-right"><b>{{ __('Debit Note') }}</b></td>
                                            <td class="text-right">
                                                {{ \Auth::user()->priceFormat($grn->grnTotalDebitNote()) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td class="text-right"><b>{{ __('Due') }}</b></td>
                                            <td class="text-right">{{ \Auth::user()->priceFormat($grn->getDue()) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
