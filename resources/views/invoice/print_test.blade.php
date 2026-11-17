<html>
    <body>
        <div id="invoice_preview" style="display: none;">
            <div id="app" style="width: 217mm;">
                <div class="editor">
                    <div style="width: 217mm;">
                        <div class="editor-content">
                            <div class="preview-main client-preview">
                                <div style="display: flex; flex-wrap: wrap; margin-top:43mm">
                                    <div style="height: 39mm; width: 135mm; float: left;">
                                        <div style="height: 6mm; font-size: 13px;">
                                            {{-- facebook.com/ Investment & Credit --}}
                                        </div>
                                        <table style="width: 111mm; height:33mm; margin-top: 4mm;">
                                            <tr>
                                                <td style="height: 7mm; width:34mm;"> {{-- CUSTOMER DETAILS --}} </td>
                                                <td style="height: 7mm; width:77mm;"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="height: 20mm; width:111mm; text-align: center; font-size:14px;">
                                                    {{ $invoice->customer->name ?? '' }} <br>
                                                    {{ $invoice->customer->address ?? '' }} <br>
                                                    {{ $invoice->customer->phone ?? '' }} <br>
                                                    {{ $invoice->customer->email ?? '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="height: 7mm; width:34mm;"> {{-- CUSTOMER CODE --}} </td>
                                                <td style="height: 7mm; width:77mm; text-align: center; font-size:14px;"> {{ \Auth::user()->customerNumberFormat($invoice->customer->id) ?? '--' }}</td>

                                            </tr>
                                        </table>
                                    </div>
                                    <div style="height: 39mm; width: 82mm; float: right;">
                                        <table style="width: 62.5mm; height: 22mm; float: right;">
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;"> {{-- INVOICE NO. --}} </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">{{ $invoice->invoice_code ?? '--' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;"> {{-- INVOICE DATE --}} </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">{{ $invoice->issue_date ?? '--' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;"> {{-- USER NAME --}} </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">{{ $user->name ?? '--' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;"> {{-- USER ID --}} </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;">{{ \Auth::user()->userNumberFormat($invoice->created_by) ?? '--' }}</td>
                                            </tr>
                                        </table>
                                        <table style="width: 62.5mm; margin-top:5.5mm; float: right;">
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;"> {{-- PO NUMBER --}} </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;"> {{  \Auth::user()->purchaseOrderNumberFormat($invoice->inventories->grns->order_id) ?? '--' }} </td>
                                            </tr>
                                            <tr>
                                                <td style="height: 5.5mm; width: 35mm;"> {{-- TERMS OF PAYMENT --}} </td>
                                                <td style="text-align: right; height: 5.5mm; font-size:14px;"> {{ $invoice->sales->installment ?? '--' }} </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div style="margin-top:11.5mm; height:98mm; display: flex; flex-wrap: wrap;">
                                    <div style="height: 9mm; width:100%; display: flex; flex-wrap: wrap;">
                                        <div style="width: 23mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- CODE --}}
                                            </span>
                                        </div>
                                        <div style="width: 82mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- ITEM DESCRIPTION class="th-div"--}}
                                            </span>
                                        </div>
                                        <div style="width: 24mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- PRODUCT TYPE class="th-div"--}}
                                            </span>
                                        </div>
                                        <div style="width: 13mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- QTY class="th-div"--}}
                                            </span>
                                        </div>
                                        <div style="width: 20mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- UNIT<br>PRICE class="th-div"--}}
                                            </span>
                                        </div>
                                        <div style="width: 20mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- DISCOUNT RATE class="th-div"--}}
                                            </span>
                                        </div>
                                        <div style="width: 28mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- AMOUNT class="th-div"--}}
                                            </span>
                                        </div>
                                    </div>
                                    <div style="height: 88mm; width:100%;">
                                        <div style="display: flex; flex-wrap: wrap;">
                                            <div class="td-div" style="width: 23mm; text-align:left; height: 8mm; display: table; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->products->barcode ?? '--' }}</span></div>
                                            <div class="td-div" style="width: 85mm; text-align:center; height: 8mm; display: table; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->products->name ?? '--' }}</span></div>
                                            <div class="td-div" style="width: 27mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->products->productCategory->name ?? '--' }}</span></div>
                                            <div class="td-div" style="width: 15mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->quantity ?? '--' }}</span></div>
                                            <div class="td-div" style="width: 23mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->unit_price ?? '--' }}</span></div>
                                            <div class="td-div" style="width: 22mm; height: 8mm; display: table; text-align: center; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->discount ?? '--' }}</span></div>
                                            <div class="td-div" style="width: 22mm; height: 8mm; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->net_amount ?? '--' }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-top:16mm; height:50mm; display: flex; flex-wrap: wrap;">
                                    <div style="height: 9mm; width:100%; display: flex; flex-wrap: wrap;">
                                        <div style="width: 105mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- Terms & Conditions --}}
                                            </span>
                                        </div>
                                        <div style="width: 62mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- Bank Details --}}
                                            </span>
                                        </div>
                                        <div style="width: 21.5mm; height: 9mm; line-height: 14px;">
                                            <span>
                                                {{-- Total Amount --}}
                                            </span>
                                        </div>
                                        <div style="width: 28mm; height:100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->net_amount ?? '0.00' }}</span></div>
                                    </div>
                                    <div style="height: 41mm; width:100%; display: flex; flex-wrap: wrap;">
                                        <div style="width: 105mm; height: 41mm; display: table; text-align: center;"><pre style="display: table-cell; vertical-align: middle; font-size:14px;">{{ $companyterms->description ?? '' }}</pre></div>
                                        <div style="width: 62mm; height: 41mm;  display: table; text-align: center;">
                                            <span style="display: table-cell; vertical-align: middle; font-size:14px;">{{ $invoice->bank->account_no ?? '' }} <br>
                                                {{ $invoice->bank->bank ?? '' }} <br>
                                                {{ $invoice->bank->address ?? '' }}
                                            </span>
                                        </div>
                                        <div style="width: 49.5mm; height: 41mm">
                                            <div style="width: 49.5mm; height: 10.25mm; display: flex; flex-wrap: wrap;">
                                                <div class="th-div-2-print" style="width: 21.5mm; height: 100%;">
                                                    <span>
                                                        {{-- TRANSPORT CHARGES --}}
                                                    </span>
                                                </div>
                                                <div style="width: 28mm; height: 100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->traveling_charges ?? '0.00' }}</span></div>
                                            </div>
                                            <div style="width: 49.5mm; height: 10.25mm; display: flex; flex-wrap: wrap;">
                                                <div class="th-div-2-print" style="width: 21.5mm; height: 100%;">
                                                    <span>
                                                        {{-- DOWN PAYEMENT --}}
                                                    </span>
                                                </div>
                                                <div style="width: 28mm; height: 100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->down_payment ?? '0.00' }}</span></div>
                                            </div>
                                            <div style="width: 49.5mm; height: 10.25mm;  display: flex; flex-wrap: wrap;">
                                                <div class="th-div-2-print" style="width: 21.5mm; height: 100%;">
                                                    <span>
                                                        {{-- ADVANCE AMOUNT --}}
                                                    </span>
                                                </div>
                                                <div style="width: 28mm; height: 100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->down_payment ?? '0.00' }}</span></div>
                                            </div>
                                            <div style="width: 49.5mm; height: 10.25mm; display: flex; flex-wrap: wrap;">
                                                <div class="th-div-2-print" style="width: 21.5mm; height: 100%;">
                                                    <span>
                                                        {{-- BALANCE AMOUNT --}}
                                                    </span>
                                                </div>
                                                <div style="width: 28mm; height: 100%; display: table; text-align: right; font-size:14px;"><span style="display: table-cell; vertical-align: middle;">{{ $invoice->sales->outstanding_amount ?? '0.00' }}</span></div>
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
    </body>
</html>
