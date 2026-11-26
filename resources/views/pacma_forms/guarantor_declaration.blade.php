<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <title>PACMA International - ඇපකරුගේ ප්‍රකාශය</title>
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
            margin-bottom: 10px;
        }
        .header img { position: absolute; left: 0; height: 44px; object-fit: contain; }
        .header-text { text-align: center; }
        .header-text h1 { font-size: 17px; margin: 0 0 2px; font-family: Georgia, serif; }
        .header-text p { margin: 0; font-size: 11px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 700; margin: 10px 0 8px; }
        .intro { font-size: 11px; line-height: 1.7; margin-bottom: 10px; text-align: justify; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #222; padding: 4px 6px; font-size: 10px; font-weight: 400; vertical-align: middle; }
        th { background: #fafafa; text-align: center; font-weight: 700; }
        .col-label { width: 34%; }
        .col-g { width: 33%; }
        .box { display: inline-block; width: 10px; height: 10px; border: 1px solid #222; vertical-align: middle; margin: 0 4px 0 2px; text-align: center; line-height: 9px; font-size: 8.5px; }
        @media print {
            body { background: #fff; padding: 0; }
            .page { box-shadow: none; margin: 0; width: 100%; padding: 8mm 10mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    @php
        $g1 = $guarantors->values()->get(0);
        $g2 = $guarantors->values()->get(1);
        $g1d = optional($g1)->details;
        $g2d = optional($g2)->details;
        $loanAmt = $sale ? ($sale->outstanding_amount ?: ($sale->net_amount ?: ($sale->total_amount ?? null))) : null;
        $custName = optional($customer)->full_name ?: (optional($customer)->name ?? ($sale->customer_name ?? ''));
        $docDate = $sale->date_of_sale ?? ($invoice->issue_date ?? date('Y-m-d'));

        $fmtResPeriod = function ($gd) {
            $raw = trim((string) (optional($gd)->resident_period ?: (optional($gd)->business_duration_on_address ?? '')));
            if ($raw === '') return '';
            return is_numeric($raw) ? $raw . ' Years' : $raw;
        };
        $fmtMoney = function ($val) {
            if ($val === null || $val === '') return '';
            $clean = str_replace([',', 'Rs.', 'Rs', ' '], '', (string) $val);
            if (is_numeric($clean)) {
                return 'Rs. ' . number_format((float) $clean, 2);
            }
            return stripos((string) $val, 'rs') === 0 ? $val : 'Rs. ' . $val;
        };
        $fmtLiab = function ($gd) {
            return collect([
                optional($gd)->other_loan_company,
                !empty(optional($gd)->other_loan_amount) ? 'Rs. ' . number_format((float) $gd->other_loan_amount, 2) : null,
            ])->filter()->implode(' - ');
        };
        $custTitle = optional($customer)->title;
        $custHonorific = ($custTitle === 0 || $custTitle === '0') ? 'මයා'
            : (($custTitle === 1 || $custTitle === '1') ? 'මිය'
            : (($custTitle === 2 || $custTitle === '2') ? 'මෙනවිය' : 'මයා / මිය / මෙනවිය'));
        $fmtServiceDur = function ($gd) {
            $raw = trim((string) (optional($gd)->business_duration ?? ''));
            if ($raw === '') return '';
            if (is_numeric($raw)) {
                $n = (int) $raw;
                return $n . ' ' . ($n === 1 ? 'Year' : 'Years');
            }
            return $raw;
        };
        $fmtYears = function ($val) {
            $raw = trim((string) $val);
            if ($raw === '') return '';
            if (is_numeric($raw)) {
                $n = (int) $raw;
                return $n . ' ' . ($n === 1 ? 'Year' : 'Years');
            }
            return $raw;
        };
        $g1Known = $fmtYears(optional($sale)->guarantor_1_known_duration ?: (optional($g1d)->known_duration ?? ''));
        $g2Known = $fmtYears(optional($sale)->guarantor_2_known_duration ?: (optional($g2d)->known_duration ?? ''));
        $g1Title = optional($g1)->title;
        $g2Title = optional($g2)->title;
        $g1Res = optional($g1)->resident;
        $g2Res = optional($g2)->resident;
        $g1Mar = optional($g1d)->marital_status;
        $g2Mar = optional($g2d)->marital_status;
    @endphp
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Print Guarantor's Declaration</button>
    </div>

    <div class="page">
        <div class="header">
            <img src="{{ asset('storage/uploads/logo/logo.png') }}" alt="PACMA">
            <div class="header-text">
                <h1>PACMA International Private Limited</h1>
                <p>No:61, K C De Silvapuraya, Thimbirigaskatuwa</p>
            </div>
        </div>

        <div class="doc-title">ඇපකරුගේ ප්‍රකාශය</div>

        <div class="intro">
            පැක්මා ඉන්ටර්නැෂනල් ප්‍රයිවට් ලිමිටඩ් ආයතනයේ ණය ක්‍රමය යටතේ රුපියල්
            {{ $loanAmt !== null ? number_format((float)$loanAmt, 2) : '........................................................' }}
            ණය පහසුකම සඳහා අයදුම් කර ඇති
            {{ $custName !== '' ? $custName : '................................................................................' }}
            {{ $custHonorific }} හඳුනන අතර ආයතනය සමඟ ඇතුළත් වන්නා වූ ගිවිසුමේ අඩංගු කොන්දේසි සම්පූර්ණයෙන් පිළිපැදීමට එකඟ වන අතර ගැණුම් කරු විසින් ගෙවීම පැහැර හරිනු ලබන ණය මුදල සහ එම පොළී මුදල ගෙවීමේ වගකීම ඇපකරුවෙකු වශයෙන් මා භාරගන්නා බවට පොරොන්දු වෙමි.
        </div>

        <table>
            <tr>
                <th class="col-label"></th>
                <th class="col-g">පළමු ඇපකරු<br>
                    Mr. <span class="box">{{ $g1Title === 0 || $g1Title === '0' ? '✓' : '' }}</span>
                    Mrs. <span class="box">{{ $g1Title === 1 || $g1Title === '1' ? '✓' : '' }}</span>
                    Miss. <span class="box">{{ $g1Title === 2 || $g1Title === '2' ? '✓' : '' }}</span>
                </th>
                <th class="col-g">දෙවන ඇපකරු<br>
                    Mr. <span class="box">{{ $g2Title === 0 || $g2Title === '0' ? '✓' : '' }}</span>
                    Mrs. <span class="box">{{ $g2Title === 1 || $g2Title === '1' ? '✓' : '' }}</span>
                    Miss. <span class="box">{{ $g2Title === 2 || $g2Title === '2' ? '✓' : '' }}</span>
                </th>
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
                <td>01. සම්පූර්ණ නම (වාසගම සමඟ)<br>NAME IN FULL</td>
                <td>{{ $fmtGName($g1) }}</td>
                <td>{{ $fmtGName($g2) }}</td>
            </tr>
            <tr>
                <td>02. ඔබ ගැණුම්කරු හඳුනන්නේ කුමන කාලයක සිටද?</td>
                <td>{{ $g1Known }}</td>
                <td>{{ $g2Known }}</td>
            </tr>
            <tr>
                <td>03. ගැණුම්කරු සමඟ ඇති සම්බන්ධතාවය<br>RELATIONSHIP OF HIRE</td>
                <td>{{ optional($g1)->relationship_to_hirer ?? '' }}</td>
                <td>{{ optional($g2)->relationship_to_hirer ?? '' }}</td>
            </tr>
            <tr>
                <td>04. ජා.හැඳුනුම්පත් අංකය / N.I.C. NUMBER</td>
                <td>{{ optional($g1)->nic_no ?? '' }}</td>
                <td>{{ optional($g2)->nic_no ?? '' }}</td>
            </tr>
            <tr>
                <td>05. වයස / AGE</td>
                <td>{{ !empty(optional($g1)->dob) ? \Carbon\Carbon::parse($g1->dob)->age : '' }}</td>
                <td>{{ !empty(optional($g2)->dob) ? \Carbon\Carbon::parse($g2->dob)->age : '' }}</td>
            </tr>
            <tr>
                <td>06. ස්ථිර ලිපිනය / PERMANENT ADDRESS</td>
                <td>{{ optional($g1)->address_nic ?: (optional($g1)->address ?? '') }}</td>
                <td>{{ optional($g2)->address_nic ?: (optional($g2)->address ?? '') }}</td>
            </tr>
            <tr>
                <td>07. පදිංචි කාල සීමාව / RESIDENT PERIOD</td>
                <td>{{ $fmtResPeriod($g1d) }}</td>
                <td>{{ $fmtResPeriod($g2d) }}</td>
            </tr>
            <tr>
                <td>08. පණිවිඩයක් තැබිය හැකි දුරකථන අංකය<br>TELEPHONE NO</td>
                <td>නිවස: {{ optional($g1)->phone ?? '..................' }}<br>ජංගම: {{ optional($g1)->phone_mobile ?? '..................' }}</td>
                <td>නිවස: {{ optional($g2)->phone ?? '..................' }}<br>ජංගම: {{ optional($g2)->phone_mobile ?? '..................' }}</td>
            </tr>
            <tr>
                <td>09. දැනට පදිංචි ලිපිනය / ADDRESS</td>
                <td>{{ optional($g1)->address ?? '' }}</td>
                <td>{{ optional($g2)->address ?? '' }}</td>
            </tr>
            <tr>
                <td>10. පදිංචි කාල සීමාව / RESIDENT PERIOD</td>
                <td>{{ $fmtResPeriod($g1d) }}</td>
                <td>{{ $fmtResPeriod($g2d) }}</td>
            </tr>
            <tr>
                <td>11. පදිංචි ස්ථානය</td>
                <td>
                    අයිතිකරු <span class="box">{{ $g1Res === 0 || $g1Res === '0' ? '✓' : '' }}</span>
                    කුලිය <span class="box">{{ $g1Res === 2 || $g1Res === '2' ? '✓' : '' }}</span><br>
                    දෙමාපියන් සමඟ <span class="box">{{ $g1Res === 1 || $g1Res === '1' ? '✓' : '' }}</span>
                    නේවාසිකව <span class="box">{{ $g1Res === 3 || $g1Res === '3' ? '✓' : '' }}</span>
                </td>
                <td>
                    අයිතිකරු <span class="box">{{ $g2Res === 0 || $g2Res === '0' ? '✓' : '' }}</span>
                    කුලිය <span class="box">{{ $g2Res === 2 || $g2Res === '2' ? '✓' : '' }}</span><br>
                    දෙමාපියන් සමඟ <span class="box">{{ $g2Res === 1 || $g2Res === '1' ? '✓' : '' }}</span>
                    නේවාසිකව <span class="box">{{ $g2Res === 3 || $g2Res === '3' ? '✓' : '' }}</span>
                </td>
            </tr>
            <tr>
                <td>12. රැකියාව (තනතුර) / OCCUPATION</td>
                <td>{{ optional($g1)->job_title ?? '' }}</td>
                <td>{{ optional($g2)->job_title ?? '' }}</td>
            </tr>
            <tr>
                <td>13. සේවායෝජකයාගේ නම / EMPLOYER'S NAME</td>
                <td>{{ optional($g1)->employment_name ?: (optional($g1)->business_name ?? '') }}</td>
                <td>{{ optional($g2)->employment_name ?: (optional($g2)->business_name ?? '') }}</td>
            </tr>
            <tr>
                <td>14. සේවා ස්ථානයේ ලිපිනය / OFFICE ADDRESS</td>
                <td>{{ optional($g1)->employment_place ?: (optional($g1d)->business_address ?? '') }}</td>
                <td>{{ optional($g2)->employment_place ?: (optional($g2d)->business_address ?? '') }}</td>
            </tr>
            <tr>
                <td>15. සේවා කාලය / SERVICE DURATION</td>
                <td>{{ $fmtServiceDur($g1d) }}</td>
                <td>{{ $fmtServiceDur($g2d) }}</td>
            </tr>
            <tr>
                <td>16. සේවා ස්ථානයේ දුරකථන අංකය / TELEPHONE NO</td>
                <td>{{ optional($g1)->phone_office ?? '' }}</td>
                <td>{{ optional($g2)->phone_office ?? '' }}</td>
            </tr>
            <tr>
                <td>17. මාසික ආදායම / MONTHLY INCOME</td>
                <td>{{ $fmtMoney(optional($g1)->monthly_income) }}</td>
                <td>{{ $fmtMoney(optional($g2)->monthly_income) }}</td>
            </tr>
            <tr>
                <td>18. වෙනත් වගකීම් / OTHER LIABILITIES</td>
                <td>{{ $fmtLiab($g1d) }}</td>
                <td>{{ $fmtLiab($g2d) }}</td>
            </tr>
            <tr>
                <td>19. විවාහක ද අවිවාහක ද යන වග / MARRIED OR UNMARRIED</td>
                <td>{{ $g1Mar === 0 || $g1Mar === '0' ? 'Married' : ($g1Mar === 1 || $g1Mar === '1' ? 'Unmarried' : '') }}</td>
                <td>{{ $g2Mar === 0 || $g2Mar === '0' ? 'Married' : ($g2Mar === 1 || $g2Mar === '1' ? 'Unmarried' : '') }}</td>
            </tr>
            <tr>
                <td>20. යැපෙන්නන් සංඛ්‍යාව / DEPENDENTS</td>
                <td>{{ optional($g1d)->children_names ?? '' }}</td>
                <td>{{ optional($g2d)->children_names ?? '' }}</td>
            </tr>
            <tr>
                <td>21. බිරිඳ හෝ ස්වාමි පුරුෂයාගේ නම / SPOUSE'S NAME</td>
                <td>{{ optional($g1d)->spouse_name_address ?? '' }}</td>
                <td>{{ optional($g2d)->spouse_name_address ?? '' }}</td>
            </tr>
            <tr>
                <td>22. දුරකථන අංකය / TELEPHONE NO</td>
                <td>{{ optional($g1d)->spouse_phone ?? '' }}</td>
                <td>{{ optional($g2d)->spouse_phone ?? '' }}</td>
            </tr>
            <tr>
                <td>23. බිරිඳ හෝ ස්වාමි පුරුෂයාගේ රැකියාව / SPOUSE'S OCCUPATION</td>
                <td>{{ optional($g1d)->spouse_occupation ?? '' }}</td>
                <td>{{ optional($g2d)->spouse_occupation ?? '' }}</td>
            </tr>
            <tr>
                <td>24. මාසික ආදායම / MONTHLY INCOME</td>
                <td>{{ $fmtMoney(optional($g1d)->spouse_income) }}</td>
                <td>{{ $fmtMoney(optional($g2d)->spouse_income) }}</td>
            </tr>
            <tr>
                <td>25. සේවා ස්ථානයේ ලිපිනය / OFFICE ADDRESS</td>
                <td>{{ optional($g1d)->spouse_office_address ?? '' }}</td>
                <td>{{ optional($g2d)->spouse_office_address ?? '' }}</td>
            </tr>
            <tr>
                <td>26. සේවා ස්ථානයට යන මාර්ග සලකුණු</td>
                <td>{{ optional($g1d)->route_to_residence ?? '' }}</td>
                <td>{{ optional($g2d)->route_to_residence ?? '' }}</td>
            </tr>
            <tr>
                <td style="height: 36px;">අත්සන / SIGNATURE</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="height: 28px;">දිනය / DATE</td>
                <td>{{ $g1 ? $docDate : '' }}</td>
                <td>{{ $g2 ? $docDate : '' }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
