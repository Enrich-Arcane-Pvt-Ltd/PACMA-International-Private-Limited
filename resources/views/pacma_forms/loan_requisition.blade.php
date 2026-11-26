<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PACMA International - Loan Requisition Form</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111;
            margin: 0;
            padding: 20px;
            background: #f3f4f6;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 14mm 16mm;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        .no-print {
            width: 210mm;
            margin: 0 auto 12px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .btn-print {
            background: #F97316;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-bottom: 16px;
        }
        .header img {
            position: absolute;
            left: 0;
            height: 46px;
            object-fit: contain;
        }
        .header-text {
            text-align: center;
        }
        .header-text h1 {
            font-size: 18px;
            margin: 0 0 3px;
            font-family: Georgia, 'Times New Roman', serif;
        }
        .header-text h2 {
            font-size: 13px;
            margin: 0;
            letter-spacing: 0.4px;
            font-family: Georgia, 'Times New Roman', serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #222;
            padding: 5px 8px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11.5px;
            line-height: 1.35;
            font-weight: 400;
            vertical-align: middle;
        }
        th {
            font-weight: 700;
            text-align: center;
            background: #fafafa;
        }
        .label-cell {
            font-weight: 700;
        }
        .num-cell {
            text-align: right;
            white-space: nowrap;
        }
        .remarks-box {
            border: 1px solid #222;
            height: 62px;
            padding: 6px;
            margin-top: 4px;
            margin-bottom: 34px;
        }
        .footer-sigs {
            display: flex;
            justify-content: space-between;
            text-align: center;
            margin-top: 30px;
            font-weight: 700;
            font-size: 11px;
        }
        .footer-sigs div {
            width: 30%;
            border-top: 1px dotted #333;
            padding-top: 6px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .page { box-shadow: none; margin: 0; width: 100%; min-height: auto; padding: 10mm 12mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Print Requisition Form</button>
    </div>

    <div class="page">
        <div class="header">
            <img src="{{ asset('storage/uploads/logo/logo.png') }}" alt="PACMA">
            <div class="header-text">
                <h1>PACMA International Private Limited</h1>
                <h2>LOAN REQUISITION FORM</h2>
            </div>
        </div>

        <table>
            <tr>
                <td class="label-cell" colspan="2">Date</td>
                <td colspan="4">{{ $sale->date_of_sale ?? ($invoice->issue_date ?? '') }}</td>
            </tr>
            <tr>
                <td class="label-cell" colspan="2">Name Of the customer</td>
                <td colspan="4">{{ optional($customer)->full_name ?: (optional($customer)->name ?? ($sale->customer_name ?? '')) }}</td>
            </tr>
            <tr>
                <th style="width: 7%;">Article</th>
                <th style="width: 30%;">Item</th>
                <th style="width: 14%;">Qty</th>
                <th style="width: 17%;">Model No</th>
                <th style="width: 16%;">Serial No</th>
                <th style="width: 16%;">Value</th>
            </tr>
            @for ($i = 0; $i < 10; $i++)
                @php
                    $item = $items->values()->get($i);
                    $itemPrice = $item ? ($item->net_amount ?? ($item->price ?? null)) : null;
                    $itemModel = $item ? (optional($item->products)->model_no ?? (optional($item->products)->sku ?? (optional($item->products)->barcode ?? ''))) : '';
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: 700;">{{ $i + 1 }}</td>
                    <td>{{ $item ? optional($item->products)->name : '' }}</td>
                    <td style="text-align: center;">{{ $item->quantity ?? '' }}</td>
                    <td>{{ $itemModel }}</td>
                    <td>{{ $item->serial_no ?? '' }}</td>
                    <td class="num-cell">{{ $itemPrice !== null && $itemPrice !== '' ? 'Rs. ' . number_format((float)$itemPrice, 2) : '' }}</td>
                </tr>
            @endfor
            @php
                $totalAmt = $sale ? ($sale->total_amount ?: ($sale->net_amount ?: ($sale->gross_amount ?? null))) : null;
            @endphp
            <tr>
                <td colspan="2" class="label-cell">Document Charge: Rs</td>
                <td class="num-cell">{{ $sale && isset($sale->document_charges) ? number_format((float)$sale->document_charges, 2) : '' }}</td>
                <td class="label-cell">Total Amount</td>
                <td colspan="2" class="num-cell">{{ $totalAmt !== null ? 'Rs. ' . number_format((float)$totalAmt, 2) : '' }}</td>
            </tr>
            <tr>
                <td colspan="2" class="label-cell">Monthly Rental</td>
                <td class="num-cell">{{ $sale && isset($sale->monthly_installment) ? 'Rs. ' . number_format((float)$sale->monthly_installment, 2) : '' }}</td>
                <td class="label-cell">Down Payment</td>
                <td colspan="2" class="num-cell">{{ $sale && isset($sale->down_payment) ? 'Rs. ' . number_format((float)$sale->down_payment, 2) : '' }}</td>
            </tr>
            <tr>
                <td colspan="2" class="label-cell">Promise Date</td>
                <td class="num-cell">{{ $sale->due_date ?? ($sale && $sale->installment_date ? $sale->installment_date . 'th of every month' : '') }}</td>
                <td class="label-cell">Promise</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2" class="label-cell">Rental Period</td>
                <td class="num-cell">{{ $sale && $sale->installment ? $sale->installment . ' Months' : '' }}</td>
                <td class="label-cell">Amount Finance</td>
                <td colspan="2" class="num-cell">{{ $sale && isset($sale->outstanding_amount) ? 'Rs. ' . number_format((float)$sale->outstanding_amount, 2) : '' }}</td>
            </tr>
            <tr>
                <td colspan="6" style="height: 18px;"></td>
            </tr>
            @php
                $fmtGName = function ($g) {
                    if (!$g) return '';
                    $name = trim((string)($g->name ?? ''));
                    $full = trim((string)($g->full_name ?? ''));
                    $biz = trim((string)($g->business_name ?? ''));
                    $emp = trim((string)($g->employment_name ?? ''));
                    if ($full !== '' && strcasecmp($full, $biz) !== 0 && strcasecmp($full, $emp) !== 0) {
                        return $full;
                    }
                    return $name !== '' ? $name : $full;
                };
            @endphp
            <tr>
                <td colspan="2" class="label-cell">1st Guarantor's Name</td>
                <td colspan="4">{{ $fmtGName($guarantors->values()->get(0)) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="label-cell">2nd Guarantor's Name</td>
                <td colspan="4">{{ $fmtGName($guarantors->values()->get(1)) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="label-cell">3rd Guarantor's Name</td>
                <td colspan="4">{{ $fmtGName($guarantors->values()->get(2)) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="label-cell">Sales Promoter Name &amp; Signature</td>
                <td colspan="4">{{ optional(optional($sale)->introducer)->name ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="2" class="label-cell">Branch Manager / Regional Manager Approved</td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="2" class="label-cell">Settle Loans</td>
                <td colspan="4"></td>
            </tr>
        </table>

        <div><strong>Remarks</strong></div>
        <div class="remarks-box"></div>

        <div class="footer-sigs">
            <div>Application<br>Filed by re O/A</div>
            <div>Entered To the System<br>Re/OA</div>
            <div>Investigating<br>Officer RE/OA</div>
        </div>
    </div>
</body>
</html>
