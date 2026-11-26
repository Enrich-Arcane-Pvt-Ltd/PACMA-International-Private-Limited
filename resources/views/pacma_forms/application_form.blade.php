<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PACMA International - Application Form</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Noto Sans Sinhala', Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #111;
            margin: 0;
            padding: 18px;
            background: #f3f4f6;
        }
        .page {
            width: 210mm;
            margin: 0 auto 16px;
            background: #fff;
            padding: 12mm 14mm;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        .no-print {
            width: 210mm;
            margin: 0 auto 12px;
            display: flex;
            justify-content: flex-end;
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
            margin-bottom: 12px;
        }
        .header img {
            position: absolute;
            left: 0;
            height: 44px;
            object-fit: contain;
        }
        .header-text { text-align: center; }
        .header-text h1 { font-size: 17px; margin: 0 0 2px; font-family: Georgia, serif; }
        .header-text p { margin: 1px 0; font-size: 11.5px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #222; padding: 4px 6px; font-size: 10.5px; vertical-align: middle; }
        th { background: #fafafa; font-weight: 700; text-align: center; }
        .meta-table td { width: 50%; height: 24px; }
        .box { display: inline-block; width: 11px; height: 11px; border: 1px solid #222; vertical-align: middle; margin: 0 4px 0 2px; text-align: center; line-height: 10px; font-size: 9px; }
        .declaration { margin-top: 14px; font-size: 11px; line-height: 1.5; }
        .sig-table { margin-top: 12px; }
        .sig-table td { height: 42px; width: 50%; }
        @media print {
            body { background: #fff; padding: 0; }
            .page { box-shadow: none; margin: 0; width: 100%; padding: 8mm 10mm; page-break-after: always; }
            .page:last-child { page-break-after: auto; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Print Application Form</button>
    </div>

    <div class="page">
        <div class="header">
            <img src="{{ asset('storage/uploads/logo/logo.png') }}" alt="PACMA">
            <div class="header-text">
                <h1>PACMA International Private Limited</h1>
                <p>අයදුම්පත්‍රය / APPLICATION FORM</p>
                <p>අයදුම්කරුගේ තොරතුරු / APPLICANT DETAILS</p>
            </div>
        </div>

        @php
            $custName = optional($customer)->full_name ?: (optional($customer)->name ?? '');
            $custAge = !empty(optional($customer)->dob) ? \Carbon\Carbon::parse($customer->dob)->age : '';
            $houseNo = trim((string)(optional($customer)->house_no ?? ''));
            $streetAddr = trim((string)(optional($customer)->address ?? ''));
            $currAddr = trim(($houseNo !== '' ? $houseNo . ' ' : '') . $streetAddr);
            $permAddr = trim((string)(optional($customer)->address_nic ?? '')) ?: $currAddr;
            $appNo = optional($invoice)->invoice_code
                ?? ($sale->sale_id ?? ($customer ? 'CUST-' . str_pad($customer->customer_id, 5, '0', STR_PAD_LEFT) : '........................................'));
            $appDate = $sale->date_of_sale ?? ($invoice->issue_date ?? ($customer && $customer->created_at ? $customer->created_at->format('Y-m-d') : '........................................'));
            $titleVal = optional($customer)->title;
            $genderVal = optional($customerDetail)->gender;
            $resVal = optional($customer)->resident;
            $marVal = optional($customerDetail)->marital_status;
            $otherLiab = collect([
                optional($customerDetail)->other_loan_company,
                !empty(optional($customerDetail)->other_loan_amount) ? 'Rs. ' . number_format((float)$customerDetail->other_loan_amount, 2) : null,
            ])->filter()->implode(' - ');
            $resPeriodRaw = trim((string)(optional($customerDetail)->resident_period ?: (optional($customerDetail)->business_duration_on_address ?? '')));
            $resPeriodDisplay = $resPeriodRaw !== '' ? (is_numeric($resPeriodRaw) ? $resPeriodRaw . ' Years' : $resPeriodRaw) : '';
            $spouseIncRaw = trim((string)(optional($customerDetail)->spouse_income ?? ''));
            $spouseIncClean = str_replace([',', 'Rs.', 'Rs', ' '], '', $spouseIncRaw);
            $spouseIncDisplay = $spouseIncRaw !== '' ? (is_numeric($spouseIncClean) ? 'Rs. ' . number_format((float)$spouseIncClean, 2) : (stripos($spouseIncRaw, 'rs') === 0 ? $spouseIncRaw : 'Rs. ' . $spouseIncRaw)) : '';
        @endphp

        <table class="meta-table">
            <tr>
                <td>කණ්ඩායම් අංකය / Group No.: {{ optional($customerDetail)->group_no ?: '........................................' }}</td>
                <td>අයදුම්පත් අංකය / Application No.: {{ $appNo }}</td>
            </tr>
            <tr>
                <td>දිනය / Date: {{ $appDate }}</td>
                <td>ශාඛාව / Branch: {{ optional(Auth::user())->branchName() ?? '........................................' }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <th style="width: 7%;">අංකය</th>
                <th style="width: 38%;">විස්තරය / DESCRIPTION</th>
                <th style="width: 55%;">පිළිතුර / DETAILS</th>
            </tr>
            <tr>
                <td style="text-align:center;">01</td>
                <td>අයදුම්කරුගේ සම්පූර්ණ නම / NAME IN FULL<br>
                    Mr. <span class="box">{{ $titleVal === 0 || $titleVal === '0' ? '✓' : '' }}</span>
                    Mrs. <span class="box">{{ $titleVal === 1 || $titleVal === '1' ? '✓' : '' }}</span>
                    Miss <span class="box">{{ $titleVal === 2 || $titleVal === '2' ? '✓' : '' }}</span>
                    Male <span class="box">{{ $genderVal === 1 || $genderVal === '1' ? '✓' : '' }}</span>
                    Female <span class="box">{{ $genderVal === 2 || $genderVal === '2' ? '✓' : '' }}</span>
                </td>
                <td>{{ $custName }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">02</td>
                <td>ජාතික හැඳුනුම්පත් අංකය / N.I.C. NUMBER</td>
                <td>{{ optional($customer)->nic_no ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">03</td>
                <td>වයස / AGE</td>
                <td>{{ $custAge }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">04</td>
                <td>ස්ථිර ලිපිනය / PERMANENT ADDRESS</td>
                <td>{{ $permAddr }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">05</td>
                <td>දැනට පදිංචි ලිපිනය / ADDRESS</td>
                <td>{{ $currAddr }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">06</td>
                <td>පදිංචි තත්ත්වය / RESIDENT</td>
                <td>
                    අයිතිකරු <span class="box">{{ $resVal === 0 || $resVal === '0' ? '✓' : '' }}</span>
                    කුලී <span class="box">{{ $resVal === 2 || $resVal === '2' ? '✓' : '' }}</span>
                    වෙනත් <span class="box">{{ $resVal === 1 || $resVal === '1' || $resVal === 3 || $resVal === '3' ? '✓' : '' }}</span>
                </td>
            </tr>
            <tr>
                <td style="text-align:center;">07</td>
                <td>පදිංචි කාලය / RESIDENT PERIOD</td>
                <td>{{ $resPeriodDisplay }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">08</td>
                <td>දුරකථන අංක / TELEPHONE</td>
                <td>නිවස / HOME: {{ optional($customer)->phone ?? '............................' }} &nbsp;&nbsp; ජංගම / MOBILE: {{ optional($customer)->phone_mobile ?? '............................' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">09</td>
                <td>රැකියාව / OCCUPATION</td>
                <td>{{ optional($customer)->job_title ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">10</td>
                <td>සේවා කාලය / SERVICE DURATION</td>
                <td>{{ optional($customerDetail)->business_duration ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">11</td>
                <td>සේවායෝජකයාගේ නම / EMPLOYER'S NAME</td>
                <td>{{ optional($customer)->employment_name ?: (optional($customer)->business_name ?? '') }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">12</td>
                <td>සේවා ස්ථානයේ දුරකථන අංකය / TELEPHONE NO.</td>
                <td>{{ optional($customer)->phone_office ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">13</td>
                <td>සේවා ස්ථානයේ ලිපිනය / OFFICE ADDRESS</td>
                <td>{{ optional($customer)->employment_place ?: (optional($customerDetail)->business_address ?? '') }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">14</td>
                <td>මාසික ආදායම / MONTHLY INCOME</td>
                <td>{{ optional($customer)->monthly_income !== null && optional($customer)->monthly_income !== '' ? 'Rs. ' . number_format((float)$customer->monthly_income, 2) : '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">15</td>
                <td>වෙනත් ආදායම් / OTHER INCOME</td>
                <td>{{ optional($customerDetail)->any_other_income_details ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">16</td>
                <td>වෙනත් වගකීම් / OTHER LIABILITIES</td>
                <td>{{ $otherLiab }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">17</td>
                <td>විවාහක / අවිවාහක බව / MARRIED OR UNMARRIED</td>
                <td>
                    විවාහක / Married <span class="box">{{ $marVal === 0 || $marVal === '0' ? '✓' : '' }}</span> &nbsp;&nbsp;
                    අවිවාහක / Unmarried <span class="box">{{ $marVal === 1 || $marVal === '1' ? '✓' : '' }}</span>
                </td>
            </tr>
            <tr>
                <td style="text-align:center;">18</td>
                <td>යැපෙන්නන් / DEPENDENTS</td>
                <td>{{ optional($customerDetail)->children_names ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">19</td>
                <td>කලත්‍රයාගේ නම සහ ලිපිනය / SPOUSE'S NAME AND ADDRESS</td>
                <td>{{ optional($customerDetail)->spouse_name_address ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">20</td>
                <td>කලත්‍රයාගේ රැකියාව / SPOUSE'S OCCUPATION</td>
                <td>{{ optional($customerDetail)->spouse_occupation ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">21</td>
                <td>කලත්‍රයාගේ සේවා ස්ථානයේ ලිපිනය / SPOUSE'S OFFICE ADDRESS</td>
                <td>{{ optional($customerDetail)->spouse_office_address ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">22</td>
                <td>කලත්‍රයාගේ ආදායම / SPOUSE'S INCOME</td>
                <td>{{ $spouseIncDisplay }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">23</td>
                <td>දෙමාපියන්ගේ නම් / PARENTS' NAME</td>
                <td>{{ optional($customerDetail)->parents_name ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">24</td>
                <td>ලිපිනය / ADDRESS</td>
                <td>{{ optional($customerDetail)->parents_address ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">25</td>
                <td>ඥාතියෙකුගේ නම, ලිපිනය සහ දුරකථන අංකය / RELATION'S NAME, ADDRESS AND PHONE NUMBER</td>
                <td>{{ optional($customerDetail)->relation_details ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">26</td>
                <td>ඥාතීත්වය / RELATION</td>
                <td>{{ optional($customerDetail)->relation_type ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">27</td>
                <td>පදිංචි ස්ථානයට යන මාර්ගය / ROUTE</td>
                <td>{{ optional($customerDetail)->route_to_residence ?? '' }}</td>
            </tr>
            <tr>
                <td style="text-align:center;">28</td>
                <td>වාරික ගෙවීමේ දිනය / PAYMENT DATE</td>
                <td>{{ optional($customerDetail)->preferred_payment_date ?? '' }}</td>
            </tr>
        </table>

        <div class="declaration">
            <strong>ප්‍රකාශය / DECLARATION</strong><br>
            ඉහත සඳහන් කර ඇති සියලුම තොරතුරු සත්‍ය හා නිවැරදි බවත්, ලබා දී ඇති තොරතුරු සම්බන්ධයෙන් අවශ්‍ය විමසීම් සිදු කිරීමට අදාළ ආයතනයට අවසර ලබා දෙන බවත් ප්‍රකාශ කරමි.
        </div>

        <table class="sig-table">
            <tr>
                <td>අයදුම්කරුගේ අත්සන / SIGNATURE: ........................................</td>
                <td>දිනය / DATE: <span style="display:inline-block; min-width:160px; border-bottom:1px dotted #111; text-align:center; padding:0 6px;">{{ $appDate }}</span></td>
            </tr>
        </table>
    </div>
</body>
</html>
