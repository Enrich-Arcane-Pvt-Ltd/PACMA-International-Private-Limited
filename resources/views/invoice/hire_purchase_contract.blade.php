<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Lato&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

    <title>hire purchase contract</title>
    <style>
        body {
            background: rgb(255, 255, 255);
            color: #000000;
            font-family: Lato, Helvetica, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.2em;
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
            align-content: center;
            align-items: center;

        }


        .p {
            padding-top: 1.5mm;
            padding-bottom: 1.5mm;
        }

        .p-full {
            padding: 0.5rem;
        }

        section {
            position: relative;
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;

        }

        .container {
            width: inherit;
            height: inherit;
            padding: 0;
            /* background-color: #a7a4a4c9; */
        }

        .content {
            width: inherit;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            padding-left: 2.5mm;
            padding-right: 3mm;
        }

        .row-div {
            display: flex;
            flex-wrap: wrap;
        }

        .d-flex {
            display: flex !important;
        }

        .heading-container {
            height: 42mm;
            padding: 0.7rem;
            width: inherit;
            /* background-color: #c0c0c0c9; */
            text-align: center;
        }

        .page_01_content {
            height: 255mm;
            width: 210mm;
            /* background-color: #5fca68a2 */
        }

        .page_02_content {
            height: 297mm;
            width: 210mm;
            /* background-color: #10a7bbc9 */
        }

        p {
            display: block;
            margin-block-start: 0;
            margin-block-end: 2px;
            margin-inline-start: 3px;
            margin-inline-end: 0px;
            font-size: 14px;
        }

        h1 {
            margin: 0;
            padding: 0;
        }

        .main-table {
            padding: 0mm 15mm 0mm 15mm;
        }

        .second-table {
            padding: 5mm 15mm 0mm 15mm;
        }

        .justify-content-start {
            justify-content: flex-start !important;
        }

        .justify-content-end {
            justify-content: flex-end !important;
        }

        .justify-content-center {
            justify-content: center !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .justify-content-around {
            justify-content: space-around !important;
        }

        .justify-content-evenly {
            justify-content: space-evenly !important;
        }

        .align-items-start {
            align-items: flex-start !important;
        }

        .align-items-end {
            align-items: flex-end !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .align-items-baseline {
            align-items: baseline !important;
        }

        .align-items-stretch {
            align-items: stretch !important;
        }

        .align-content-start {
            align-content: flex-start !important;
        }

        .align-content-end {
            align-content: flex-end !important;
        }

        .align-content-center {
            align-content: center !important;
        }

        .align-content-between {
            align-content: space-between !important;
        }

        .align-content-around {
            align-content: space-around !important;
        }

        .align-content-stretch {
            align-content: stretch !important;
        }

        .align-self-auto {
            align-self: auto !important;
        }

        .align-self-start {
            align-self: flex-start !important;
        }

        .align-self-end {
            align-self: flex-end !important;
        }

        .align-self-center {
            align-self: center !important;
        }

        .align-self-baseline {
            align-self: baseline !important;
        }

        .align-self-stretch {
            align-self: stretch !important;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

    </style>
</head>

<body id="print_container">

        <section class="page_01">
            <div class="container">
                <div class="content">

                    <div class="heading-container">
                        <table>
                            <tr>
                                <td>
                                    <h1>PACMA INTERNATIONAL PRIVATE LIMITED</h1>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="width: 100%; height: 15px"> </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    APPLICATION FOR A HIRE PURCHASE ACCOUNT
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="width: 100%; height: 6px"> </div>
                                </td>
                            </tr>
                            <tr>
                                <td> කුලී සින්නක්කර ගිණුමක් සඳහා ගිවිසුම් අයදුම් පත්‍රයයි</td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="width: 100%; height: 6px"> </div>
                                </td>
                            </tr>
                            <tr>
                                <td> கடன் கொள்வனவு கணக்கிற்கான உடன்படிக்கை விண்ணப்ப படிவம்</td>
                            </tr>
                        </table>
                    </div>
                    <div class="page_01_content">

                        <div class="main-table">
                            <table>

                                <tr>

                                    <td colspan="3" style="font-size: 8px;line-height: 12px">
                                        Full Name <br>
                                        සම්පූර්ණ නම <br>
                                        முழுப்‌ பெயர்‌
                                    </td>

                                    <td colspan="8">
                                        <div style="width: 100%;height: 9mm;border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                @if ($invoice->customer_id != 0)
                                                {{ $invoice->customer->full_name ?? '' }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 20px">

                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 8px;line-height: 12px">
                                        NIC / Passport No / D.L No <br>
                                        ජා:හැ:ප/වි:ග:බ/රි:බ අංකය <br>
                                        அடையாள அட்டை / கடவுச்சீட்டு / சாரதி அனுமதிப்பத்திர இல.
                                    </td>

                                    <td colspan="5">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                @if ($invoice->customer_id != 0)
                                                {{ $invoice->customer->nic_no ?? '' }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 20px">

                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-size: 8px;line-height: 12px">
                                        Residencial Address<br>
                                        පදිංචි ලිපිනය <br>
                                        மட்டு முகவரி
                                    </td>

                                    <td colspan="9">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->customer->house_no ?? '' }}
                                                {{ $invoice->sales->customer->address ?? ''  }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 20px">

                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="font-size: 8px;line-height: 12px">
                                        Product Requierd <br>
                                        මිලදී ගැනීමට අදහස් කරන භාණ්ඩය<br>
                                        தேவையான பொருள்‌
                                    </td>

                                    <td colspan="7">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">{{ $invoice->sales->products->name ?? '--' }}</p>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 20px">

                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="font-size: 8px;line-height: 12px">
                                        Taliphone No <br>
                                        දුරකථන අංකය<br>
                                        தொலைபேசி இலக்கம்‌
                                    </td>

                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Home <br>
                                        නිවස <br>
                                        வடு
                                    </td>

                                    <td colspan="1">
                                        <div style="width: 25mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                @if ($invoice->customer_id != 0)
                                                {{ $invoice->customer->phone ?? '' }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>

                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Office <br>
                                        කාර්යාලය <br>
                                        காரியாலயம்‌
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 25mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                @if ($invoice->customer_id != 0)
                                                {{ $invoice->customer->phone_office ?? '' }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Mobile <br>
                                        ජංගම <br>
                                        தொலைபேசி
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 25mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                @if ($invoice->customer_id != 0)
                                                {{ $invoice->customer->phone_mobile ?? '' }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 20px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-size: 8px;line-height: 12px">
                                        Residence <br>
                                        පදිංචිය <br>
                                        பதிவிடம்‌
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Own <br>
                                        තමාගේම <br>
                                        சொந்தமானது
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($invoice->sales->customer->resident) &&
                                                $invoice->sales->customer->resident == 0)
                                                X
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Parent's <br>
                                        දෙමාපියන්ගේ <br>
                                        பற்றோருடன்‌
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($invoice->sales->customer->resident) &&
                                                $invoice->sales->customer->resident == 1)
                                                X
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" class="text-left" style="font-size: 8px;line-height: 12px">
                                        Rented <br>
                                        කුලී <br>
                                        வடகை
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($invoice->sales->customer->resident) &&
                                                $invoice->sales->customer->resident == 2)
                                                X
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Other <br>
                                        වෙනත් <br>
                                        வறு
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($invoice->sales->customer->resident) &&
                                                $invoice->sales->customer->resident == 3)
                                                X
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 20px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="font-size: 8px;line-height: 12px">
                                        Name Of Employement/Business<br>
                                        රැකියා ස්ථානයේ / ව්‍යාපාර ස්ථානයේ නම<br>
                                        வலை தளத்தின்‌ பெயர்‌
                                    </td>
                                    <td colspan="7">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->customer->employment_name ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 20px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="font-size: 8px;line-height: 12px">
                                        Employement/Business Address<br>
                                        රැකියා ස්ථානයේ / ව්‍යාපාර ස්ථානයේ ලිපිනය <br>
                                        வலை தளத்தின்‌ பெயர்‌
                                    </td>
                                    <td colspan="7">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                @php
                                                    $contractCust = $invoice->sales->customer ?? ($invoice->customer ?? null);
                                                    $businessAddress = $contractCust->details->business_address ?? ($contractCust->employment_place ?? '');
                                                @endphp
                                                {{ !empty($businessAddress) && strtolower(trim($businessAddress)) !== 'n/a' ? $businessAddress : '' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 20px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="font-size: 8px;line-height: 12px">
                                        Job Title <br>
                                        රැකියා වේ/ව්‍යාපාරයේ තනතුර <br>
                                        வலை தலைப்பு
                                    </td>
                                    <td colspan="3">
                                        <div style="width: 90%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $contractCust->job_title ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Employee Number<br>
                                        සේවක අංකය <br>
                                        பணியாளர் எண்
                                    </td>
                                    <td colspan="3">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $contractCust->employment_number ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 20px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-size: 8px;line-height: 12px">
                                        Matrred Staus <br>
                                        ඔබ විවාහක?<br>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Married <br>
                                        විවාහක <br>
                                        திருமணமானவர்‌
                                    </td>
                                    <td colspan="2">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($contractCust->details->marital_status) && ($contractCust->details->marital_status === 0 || $contractCust->details->marital_status === '0' || strtolower($contractCust->details->marital_status) === 'married'))
                                                X
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Single <br>
                                        අවිවාහක <br>
                                        திருමணமாகாதவர்
                                    </td>
                                    <td colspan="2">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($contractCust->details->marital_status) && ($contractCust->details->marital_status === 1 || $contractCust->details->marital_status === '1' || strtolower($contractCust->details->marital_status) === 'single'))
                                                X
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 20px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="font-size: 8px;line-height: 12px">
                                        Do you have Chidren <br>
                                        ඔබට දරුවන් සිටීද? <br>
                                        குழந்தைகள்‌ உள்ளதா?
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        yes <br>
                                        ඔව් <br>
                                        ஆம்‌
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @php
                                                    $hasChildren = false;
                                                    if (isset($contractCust->details->children_names) && trim($contractCust->details->children_names) !== '') {
                                                        $cVal = strtolower(trim($contractCust->details->children_names));
                                                        if (!in_array($cVal, ['n/a', 'na', 'no', 'none', '-', '--', '0'])) {
                                                            $hasChildren = true;
                                                        }
                                                    }
                                                @endphp
                                                @if ($hasChildren)
                                                X
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        No <br>
                                        නැත <br>
                                        இல்லை
                                    </td>
                                    <td colspan="3">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($contractCust->details) && !$hasChildren)
                                                X
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 20px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="font-size: 8px;line-height: 12px">
                                        Do you have credit card <br>
                                        ඔබ සතුව ණය පතක් තිබේද ? <br>
                                        கடன்‌ அட்டை உள்ளதா?
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        yes <br>
                                        ඔව් <br>
                                        ஆம்‌
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        No <br>
                                        නැත <br>
                                        இல்லை
                                    </td>
                                    <td colspan="4">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 20px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" style="font-size: 8px;line-height: 12px">
                                        I here by certify above particulars are true and correct <br>
                                        ඉහත සඳහන් තොරතුරු සත්‍ය හා නිවැරදි බවට සහතික කරමි. <br>
                                        மேலுள்ள அனைத்துத்‌ தகவல்கள்‌ சரியானதும்‌ உண்மையானதும்‌ என உறுதி செய்கின்றேன்‌
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Signature <br>
                                        අත්සන<br>
                                        கையொப்பம்‌
                                    </td>
                                    <td colspan="3">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="page_02">
            <div class="container">
                <div class="content">
                    <div class="page_02_content">
                        <div class="second-table">
                            <table>
                                <tr>
                                    <td style="font-size: 15px;"> 1st GUARANTOR PERTICULARS / පළමු ඇපකරු පිළිබඳ විස්තරය /
                                        1ம்‌ பிணையாளியின்‌ விபரம்‌</td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Full Name <br>
                                        සම්පූර්ණ නම <br>
                                        முழுப்‌ பெயர்‌
                                    </td>
                                    <td colspan="14">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor1->name ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="font-size: 8px;line-height: 12px">
                                        NIC / Passport No / D.L No <br>
                                        ජා:හැ:ප/වි:ග:බ/රි:බ අංකය <br>
                                        அடையாள அட்டை / கடவுச்சீட்டு / சாரதி அனுமதிப்பத்திர இல.
                                    </td>
                                    <td colspan="8">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor1->nic_no ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-size: 8px;line-height: 12px">
                                        Residencial Address<br>
                                        පදිංචි ලිපිනය <br>
                                        மட்டு முகவரி
                                    </td>
                                    <td colspan="10">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">{{ $invoice->sales->guarantor1->house_no ?? '' }}
                                                {{ $invoice->sales->guarantor1->address ?? '' }}</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-size: 8px;line-height: 12px">
                                        Taliphone No <br>
                                        දුරකථන අංකය<br>
                                        தொலைபேசி இலக்கம்‌
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Home <br>
                                        නිවස <br>
                                        வடு
                                    </td>
                                    <td colspan="2">
                                        <div style="width: 25mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor1->phone ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Office <br>
                                        කාර්යාලය <br>
                                        காரியாலயம்‌
                                    </td>
                                    <td colspan="2">
                                        <div style="width: 25mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor1->phone_office ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Mobile <br>
                                        ජංගම <br>
                                        தொலைபேசி
                                    </td>
                                    <td colspan="2">
                                        <div style="width: 25mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor1->phone_mobile ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-size: 8px;line-height: 12px">
                                        Residence <br>
                                        පදිංචිය <br>
                                        பதிவிடம்‌
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Own <br>
                                        තමාගේම <br>
                                        சொந்தமானது
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($invoice->sales->guarantor1->resident) &&
                                                $invoice->sales->guarantor1->resident == 0)
                                                {{ $invoice->sales->guarantor1->resident == 0 ? 'X' : '' }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Parent's <br>
                                        දෙමාපියන්ගේ <br>
                                        பற்றோருடன்‌
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($invoice->sales->guarantor1->resident) &&
                                                $invoice->sales->guarantor1->resident == 1)
                                                {{ $invoice->sales->guarantor1->resident == 1 ? 'X' : '' }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Rented <br>
                                        කුලී <br>
                                        வடகை
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($invoice->sales->guarantor1->resident) &&
                                                $invoice->sales->guarantor1->resident == 2)
                                                {{ $invoice->sales->guarantor1->resident == 2 ? 'X' : '' }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Other <br>
                                        වෙනත් <br>
                                        வறு
                                    </td>
                                    <td colspan="2">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($invoice->sales->guarantor1->resident) &&
                                                $invoice->sales->guarantor1->resident == 3)
                                                {{ $invoice->sales->guarantor1->resident == 3 ? 'X' : '' }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="font-size: 8px;line-height: 12px">
                                        Name Of Employement/Business<br>
                                        රැකියා ස්ථානයේ / ව්‍යාපාර ස්ථානයේ නම<br>
                                        வலை தளத்தின்‌ பெயர்‌
                                    </td>
                                    <td colspan="7">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor1->business_name ?? ($invoice->sales->guarantor1->employment_name ?? '') }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-size: 8px;line-height: 12px">
                                        Job Title <br>
                                        රැකියා වේ/ව්‍යාපාරයේ තනතුර <br>
                                        வலை தலைப்பு
                                    </td>
                                    <td colspan="4">
                                        <div style="width: 90%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor1->job_title ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-size: 8px;line-height: 12px">
                                        Relationship to hirer <br>
                                        ගැනුම්කරුට ඇති සම්බන්දතාවය <br>
                                        வங்குபவருடனான உறவு
                                    </td>
                                    <td colspan="10">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor1->relationship_to_hirer ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8" style="font-size: 8px;line-height: 12px">
                                        I here by certify above particulars are true and correct <br>
                                        ඉහත සඳහන් තොරතුරු සත්‍ය හා නිවැරදි බවට සහතික කරමි. <br>
                                        மேலுள்ள அனைத்துத்‌ தகவல்கள்‌ சரியானதும்‌ உண்மையானதும்‌ என உறுதி செய்கின்றேன்‌
                                    </td>
                                    <td colspan="2" style="font-size: 8px;line-height: 12px">
                                        Signature <br>
                                        අත්සන<br>
                                        கையொப்பம்‌
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 50mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 4mm">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 15px;">2nd GUARANTOR PERTICULARS/ දෙවන ඇපකරු පිළිබඳ විස්තරය /2ம்‌
                                        பிணையாளியின்‌ விபரம்‌</td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Full Name <br>
                                        සම්පූර්ණ නම <br>
                                        முழுப்‌ பெயர்‌
                                    </td>
                                    <td colspan="14">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor2->name ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="font-size: 8px;line-height: 12px">
                                        NIC / Passport No / D.L No <br>
                                        ජා:හැ:ප/වි:ග:බ/රි:බ අංකය <br>
                                        அடையாள அட்டை / கடவுச்சீட்டு / சாரதி அனுமதிப்பத்திர இல.
                                    </td>
                                    <td colspan="10">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor2->nic_no ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-size: 8px;line-height: 12px">
                                        Residencial Address<br>
                                        පදිංචි ලිපිනය <br>
                                        மட்டு முகவரி
                                    </td>
                                    <td colspan="10">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p"> {{ $invoice->sales->guarantor2->house_no ?? '' }}
                                                {{ $invoice->sales->guarantor2->address ?? '' }}</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-size: 8px;line-height: 12px">
                                        Taliphone No <br>
                                        දුරකථන අංකය<br>
                                        தொலைபேசி இலக்கம்‌
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Home <br>
                                        නිවස <br>
                                        வடு
                                    </td>
                                    <td colspan="2">
                                        <div style="width: 25mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor2->phone ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Office <br>
                                        කාර්යාලය <br>
                                        காரியாலயம்‌
                                    </td>
                                    <td colspan="2">
                                        <div style="width: 25mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor2->phone_office ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Mobile <br>
                                        ජංගම <br>
                                        தொலைபேசி
                                    </td>
                                    <td colspan="2">
                                        <div style="width: 25mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor2->phone_mobile ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-size: 8px;line-height: 12px">
                                        Residence <br>
                                        පදිංචිය <br>
                                        பதிவிடம்‌
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Own <br>
                                        තමාගේම <br>
                                        சொந்தமானது
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($invoice->sales->guarantor2->resident) &&
                                                $invoice->sales->guarantor2->resident == 0)
                                                X
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Parent's <br>
                                        දෙමාපියන්ගේ <br>
                                        பற்றோருடன்‌
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($invoice->sales->guarantor2->resident) &&
                                                $invoice->sales->guarantor2->resident == 1)
                                                X
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Rented <br>
                                        කුලී <br>
                                        வடகை
                                    </td>
                                    <td colspan="1">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($invoice->sales->guarantor2->resident) &&
                                                $invoice->sales->guarantor2->resident == 2)
                                                X
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="1" style="font-size: 8px;line-height: 12px">
                                        Other <br>
                                        වෙනත් <br>
                                        வறு
                                    </td>
                                    <td colspan="2">
                                        <div style="width: 10mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p-full">
                                                @if (isset($invoice->sales->guarantor2->resident) &&
                                                $invoice->sales->guarantor2->resident == 3)
                                                X
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="font-size: 8px;line-height: 12px">
                                        Name Of Employement/Business<br>
                                        රැකියා ස්ථානයේ / ව්‍යාපාර ස්ථානයේ නම<br>
                                        வலை தளத்தின்‌ பெயர்‌
                                    </td>
                                    <td colspan="7">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor2->business_name ?? ($invoice->sales->guarantor2->employment_name ?? '') }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-size: 8px;line-height: 12px">
                                        Job Title <br>
                                        රැකියා වේ/ව්‍යාපාරයේ තනතුර <br>
                                        வலை தலைப்பு
                                    </td>
                                    <td colspan="4">
                                        <div style="width: 90%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor2->job_title ?? '' }}
                                            </p>
                                        </div>
                                    </td>


                                </tr>

                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">

                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-size: 8px;line-height: 12px">
                                        Relationship to hirer <br>
                                        ගැනුම්කරුට ඇති සම්බන්දතාවය <br>
                                        வங்குபவருடனான உறவு
                                    </td>

                                    <td colspan="10">
                                        <div style="width: 100%;height: 9mm; border:1px solid #000;margin: 2px;">
                                            <p class="p">
                                                {{ $invoice->sales->guarantor2->relationship_to_hirer ?? '' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div style="width: 100%; height: 10px">

                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8" style="font-size: 8px;line-height: 12px">
                                        I here by certify above particulars are true and correct <br>
                                        ඉහත සඳහන් තොරතුරු සත්‍ය හා නිවැරදි බවට සහතික කරමි. <br>
                                        மேலுள்ள அனைத்துத்‌ தகவல்கள்‌ சரியானதும்‌ உண்மையானதும்‌ என உறுதி செய்கின்றேன்‌
                                    </td>

                                    <td colspan="2" style="font-size: 8px;line-height: 12px">
                                        Signature <br>
                                        අත්සන<br>
                                        கையொப்பம்‌
                                    </td>

                                    <td colspan="1">
                                        <div style="width: 50mm;height: 9mm; border:1px solid #000;margin: 2px;">
                                        </div>
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <script>
        window.onload = function () {
            printContent('print_container');
        };

        function printContent(elementId) {
            var content = document.getElementById(elementId);
            var originalContents = document.body.innerHTML;
            var printContents = content.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }

    </script>

</body>

</html>
