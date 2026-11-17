<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <title>PACMA International - ඇපකරය හා වන්දි පූරණය</title>
    <style>
        @page {
            size: A4;
            margin: 14mm 16mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Noto Sans Sinhala', Arial, Helvetica, sans-serif;
            font-size: 13.5px;
            color: #111;
            margin: 0;
            padding: 18px;
            background: #f3f4f6;
            line-height: 1.75;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 16px;
            background: #fff;
            padding: 14mm 16mm;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            page-break-after: always;
        }
        .page:last-child {
            page-break-after: auto;
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
        h1 { text-align: center; font-size: 20px; margin: 0 0 10px; }
        .agreement-no { text-align: right; margin-bottom: 14px; font-weight: normal; font-size: 13.5px; }
        p { text-align: justify; margin: 0 0 12px; }
        .sig-block { margin-top: 26px; }
        .sig-line { margin-bottom: 24px; page-break-inside: avoid; }
        .witness-row { margin-left: 28px; line-height: 2.05; }
        .fill {
            display: inline-block;
            font-family: inherit;
            font-size: inherit;
            font-weight: normal;
            border-bottom: 1px dotted #111;
            padding: 0 4px;
            line-height: 1.3;
            min-width: 140px;
            text-decoration: none;
        }
        .fill-xs { min-width: 55px; text-align: center; }
        .fill-sm { min-width: 95px; text-align: center; }
        .fill-md { min-width: 210px; }
        .fill-lg { min-width: 340px; }
        @media print {
            body { background: #fff; padding: 0; }
            .page { box-shadow: none; margin: 0; width: 100%; min-height: auto; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    @php
        $buyerName = optional($customer)->name ?? ($sale->customer_name ?? null);
        $buyerAddress = optional($customer)->address ?? ($sale->customer_address ?? null);

        $g1 = $guarantors->values()->get(0);
        $g2 = $guarantors->values()->get(1);

        $guarantorAddresses = collect([optional($g1)->address, optional($g2)->address])->filter()->unique()->implode(' / ');
        $guarantorNics = collect([optional($g1)->nic_no, optional($g2)->nic_no])->filter()->unique()->implode(' / ');

        $saleDate = !empty($sale->date_of_sale) ? \Carbon\Carbon::parse($sale->date_of_sale) : null;
        $sinhalaMonths = [
            1 => 'ජනවාරි', 2 => 'පෙබරවාරි', 3 => 'මාර්තු', 4 => 'අප්‍රේල්',
            5 => 'මැයි', 6 => 'ජූනි', 7 => 'ජූලි', 8 => 'අගෝස්තු',
            9 => 'සැප්තැම්බර්', 10 => 'ඔක්තෝබර්', 11 => 'නොවැම්බර්', 12 => 'දෙසැම්බර්',
        ];
    @endphp

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Print Guarantee &amp; Indemnity</button>
    </div>

    <div class="page">
        <h1>ඇපකරය හා වන්දි පූරණය</h1>
        <div class="agreement-no">
            ගිවිසුම් අංකය :- <span class="fill fill-md">{{ $sale->sale_id ?? '' }}</span>
        </div>

        <p>
            කේ සී ද සිල්වාපුරය අංක 61 දරන ලියාපදිංචි කාර්යාලය පවත්වාගෙන යනු ලබන <strong>පැක්මා ඉන්ටර්නැෂනල් ප්‍රයිවට් ලිමිටඩ්</strong> ආයතනය සහ එකී සමාගමේ අනුප්‍රාප්තිකයන් සහ ලැබුම්කරුවන් වෙත.
        </p>

        <p>
            1. මාගේ ඉල්ලීම පරිදි සමාගම
            <span class="fill fill-lg">{{ $buyerName ?? '' }}</span>
            පදිංචි
            <span class="fill fill-lg">{{ $buyerAddress ?? '' }}</span>
            (මෙහි මින් ඉදිරියට ගැණුම්කරු යනුවෙන් හඳුන්වනු ලබන අතර එකී පද සම්බන්ධයෙන් ඔහුගේ / ඇයගේ අනුප්‍රාප්තිකයින්, උරුමකරුවන්, පොල්මාකරුවන්, අද්මිනිස්ත්‍රාසි කරුවන්, ලැබුම්කරුවන් අදහස්වේ) සමඟ ඇති කරගන්නා ලද ඉහත අංක දරණ "ණය ගිවිසුම" (මෙහි මින් ඉදිරියට ණය ගිවිසුම යනුවෙන් සඳහන් වන) ණයගිවිසුමේ 2 වන උපලේඛණයෙහි විස්තර කර ඇති නිසි දිනයට ණය වාරිකය ද, පොලිය ද පැහැර හැරීමේ ගාස්තු ඇතුළුව මෙම ණය ගිවිසුම යටතේ ගෙවිය යුතු කවර හෝ ගාස්තුවක් ණය ගැණුම්කරු විසින් ගෙවන බවට ද, එකී ගිවිසුම යටතේ ණය ගැණුම්කරු විසින් ඉටු කළ යුතු බන්ධනයන් එකී ගැණුම්කරු විසින් ඉටු කරන බවට ද, මෙහි පහත අත්සන් තබන මම එක්ව හා වෙන් වෙන්ව සහතික වී ඇපවන අතර, තවද මෙම ණය ගිවිසුම යටතේ ණය ගැණුම්කරු විසින් සිදු කරනු ලබන කවර හෝ පැහැර හැරීමක් හේතුවෙන් ඔබ සමාගමට වියදමක්, පාඩුවක්, අලාභයක් හෝ සිදුවුනි නම් (නඩු ගාස්තු ආදිය ද, සම්පූර්ණ හානි පූරණයට යටත්ව) ඒ සියල්ලන් ඔබ සමාගම හානි පූර්ණය කිරීමට මෙහි පහත අත්සන් තබන මම එක්ව හා වෙන් වෙන්ව බැඳී පොරොන්දුවෙමි.
        </p>

        <p>
            2. මෙම ඇපකරය හා හානි පූරණය යටතේ මාගේ වගකීම ඇපකරුවෙකු වශයෙන් නොව ප්‍රධාන ණයකරුවෙකු වශයෙන් බවට මෙයින් විශේෂයෙන් එකඟවන බවට ප්‍රකාශ කරන අතර මෙම ඇපකරය අඛණ්ඩ හා අවලංගු කළ නොහැකි වන අතර ගැණුම්කරු ඇතිකර ගන්නා ලද සමටියක් ඔහුට විරුද්ධව නඩු පැවරීමක් නොකිරීම, ණය ගිවිසුමේ වෙනස් කිරීමත් සිදුකිරීම ආදියෙන් ණය ගැණුම්කරුට කල් දීමක් හෝ සහන කාල සීමාව හේතුවෙන් මා ඇපකරයෙන් හෝ හානි පූරණයෙන් නිදහස් නොවන අතර ණය ගැණුම්කරුට විරුද්ධව නීතිමය පියවරක් ඔබ විසින් ගනු ලැබුවත්, නැතත් නීතිමය අයිතිවාසිකමක් ඔබට තිබුණාත් නැතත් හෝ ණය ගිවිසුම අළුත් කිරීමක් හෝ වෙනත් ආකාරයේ ඇපකරයකට මින් මතු ඉදිරියට ඇතුළුවීමෙන් මාගේ ඇපකරනයට හෝ හානි පූරණයට බලපෑමක් නොසිදුවන අතර ඉදිරියටද බලාත්මක වන බවට මෙයින් එකඟ වූ බැව් තව දුරටත් ප්‍රකාශ කරමි.
        </p>

        <p>
            3. ණය ගැණුම්කරුට හා මා හට විරුද්ධව එනම් නඩුවක් පැවරීම සඳහා හෝ එක්ව හා වෙන් වෙන්ව නඩු පැවරීම සඳහා හෝ මුල් වතාවට මා හට විරුද්ධව නඩු පැවරීමට ඔබට නිදහස තිබෙන බවට විශේෂයෙන් එකඟ වන බැව් මෙයින් ප්‍රකාශ කරන අතර තවද, මුල් වතාවට ණය ගැණුම්කරුට විරුද්ධව නඩු පැවරීමෙන් ණය ගැණුම්කරු නිදහස් වන ලෙසට හෝ ඔබගේ ඉල්ලීම බෙදා වෙන් කර මා හට හෝ ණය ගැණුම්කරුට විරුද්ධව ප්‍රමාණුකූලව ගෙවන ලෙසට ඉල්ලා නඩු පවරන ලෙසට හෝ වෙනත් අයෙකු ඇතුළුව හෝ නොමැතිව මා හට විරුද්ධව නඩු නොපවරන ලෙසට හෝ ප්‍රමාණුකූලව කොටස පමණක් මාගෙන් අයකර ගන්නා ලෙසට හෝ ඇපකරුවෙකු වශයෙන් නීතියෙන් මා හට අයිති වෙනත් අයිතිවාසිකම් හා වරප්‍රසාද ඉල්ලීම මම මෙයින් අත් හරිමි. ණය ගැණුම්කරුට විරුද්ධව
        </p>
    </div>

    <div class="page">
        <p>
            ඉල්ලා ඇති සහනය ලැබීමට පෙර මා හට විරුද්ධව නඩු පැවරීමේ වගකීමද ඇතුළුව සෑම අවස්ථාවකදීම මා මෙම කාරණා සඳහා ප්‍රධාන ණය කරුවෙකු වශයෙන් මෙයින් බැඳී සිටින බවට එකඟ වී ප්‍රකාශ කර සිටිමි.
        </p>

        <p>
            <span class="fill fill-lg">{{ $guarantorAddresses ?: '' }}</span>
            පදිංචි මම ඇපකරු වශයෙන් මෙම ගිවිසුම අත්සන් තැබීමට පෙර එහි අඩංගු කාරණා මා හට තේරුම් කරදුන් බවත් ඒ සියල්ල මැනවින් අවබෝධ කරගත් බව පිළිගන්නා බවත් මෙයින් ප්‍රකාශ කර සිටිමි. මෙම ගිවිසුම මත ණය ගැණුම්කරුට විරුද්ධව නඩු පැවරීමේ පූර්ණ අභිමතය පැක්මා ඉන්ටර්නැෂනල් ප්‍රයිවට් ලිමිටඩ් හා කැමැත්ත වන්නේ නම් එය ක්‍රියාත්මක කර හැකි බවටද මෙම ගිවිසුම මත පැන නැගුණ යම් නඩු තත්වයකද මා හට විරුද්ධව ලබාගන්නා ලද නඩු තීන්දුවක් ක්‍රියාත්මක කිරීමේදී ණය ගැණුම්කරුගේ දේපල තහනමට හෝ විකිණීමට හැකි බවට ද මම මෙයින් පිළිගෙන ප්‍රකාශ කර සිටිමි.
        </p>

        <p>
            ණය ගිවිසුමේ සහ මෙම ප්‍රකාශයෙහි අඩංගු දෑ හොඳාකාරව තේරුම් ගෙන මා විසින් ඊට අත්සන් තබන ලද්දේ වර්ෂ
            <span class="fill fill-xs">{{ $saleDate ? $saleDate->format('Y') : '20' }}</span>
            වූ මස
            <span class="fill fill-sm">{{ $saleDate ? ($sinhalaMonths[(int)$saleDate->format('n')] ?? $saleDate->format('F')) : '' }}</span>
            <span class="fill fill-xs">{{ $saleDate ? $saleDate->format('d') : '' }}</span>
            දින තිඹිරිගස්කටුවේදීය.
        </p>

        <div class="sig-block">
            <div class="sig-line">
                ..............................................<br>
                ඇපකරුගේ අත්සන<br>
                ජාතික හැඳුනුම්පත් අංකය :- <span class="fill fill-md">{{ $guarantorNics ?: '' }}</span>
            </div>

            <div class="sig-line">
                ..............................................<br>
                සාක්ෂිකරුගේ අත්සන
                <div class="witness-row">
                    නම &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <span class="fill fill-lg">{{ $sale->witness_1_name ?? '' }}</span><br>
                    ලිපිනය &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <span class="fill fill-lg">{{ $sale->witness_1_address ?? '' }}</span><br>
                    ජාතික හැඳුනුම්පත් අංකය : <span class="fill fill-md">{{ $sale->witness_1_nic ?? '' }}</span>
                </div>
            </div>

            <div class="sig-line">
                ..............................................<br>
                සාක්ෂිකරුගේ අත්සන
                <div class="witness-row">
                    නම &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <span class="fill fill-lg">{{ $sale->witness_2_name ?? '' }}</span><br>
                    ලිපිනය &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <span class="fill fill-lg">{{ $sale->witness_2_address ?? '' }}</span><br>
                    ජාතික හැඳුනුම්පත් අංකය : <span class="fill fill-md">{{ $sale->witness_2_nic ?? '' }}</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
