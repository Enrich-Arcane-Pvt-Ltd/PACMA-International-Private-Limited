<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <style>
        @page {
            /* size: 21cm 14.8cm; */
            height: 210mm;
            width: 297mm;
            /* max-width: 21cm; */
            /* width: 217mm; */
            /* height: 280mm; */
            /* margin: 0mm; */
        }
        html, body {
            width: 297mm;
            height: 210mm;
            /* max-width: 30cm; */
            /* width: 217mm;
            height: 139mm;
            margin: 0;
            padding: 0; */
            background-color: rgb(235, 235, 235);
        }
        .container {
            width: 297mm;
            /* height: 210mm; */
            padding: 2rem;
            background-color: rgb(255, 255, 255);
            /* width: inherit;
            height: inherit;
            margin-left: 50%;
            margin-top: 15%;
            padding: 0;
            background: url('{{ asset("assets/receipt/receipt_image.jpg") }}');
            background-repeat: no-repeat;
            background-size: 217mm 139mm; */
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .flex-table {
            display: table;
        }
        .valign {
            display: table-cell;
            vertical-align: middle;
        }
        .heading {
            margin-left: 10em;
            text-align: center;
            font-weight: bolder;
            font-size: 15px;
            margin-top: 1em;
        }
        .date {
            width: 100%;
        }
        .detail {
            /* width: 100%; */
            margin-top: 2rem;
            margin-left: 2rem;
            margin-right: 2rem;
            line-height: 1.4;
            font-size: 83%;
            font-family: Arial;
            text-align: justify;
        }
        .text-center {
            text-align: center;
        }
        .u{
            border-bottom: 1px dotted;
            text-decoration: none;
        }
        .tamil{
            font-size: 89%;
        }
        .text{
            border-top: 0px;
            border-left: 0px;
            border-right: 0px;
            border-bottom: 1px dotted;
            width: 32%;
            text-align: center;
        }
        .button{
            background-color: #007bff;
            border: none;
            color: white;
            padding: 15px 32px;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            line-height: 12%;
            margin: 0;
            top: 715px;
            left: 550px;
            width:95px;
            height:35px;
            position: sticky;

}

        @media print{
            @page {size: landscape}

            .none-print{display: none}
            }

    </style>
</head>
<body>
    <div class="section-body">
        <div class="container">
            <div class="flex-table">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-6" style="margin-left: 2.1rem; margin-top: 0.5em;">
                                <img src="{{asset('storage/uploads/logo/large_logo.png')}}" style="max-width: 200px" />
                            </div>
                            <div class="col-md-6 heading"><u>
                                එන්තරවාසියයි / கோரிக்கை அறிவிப்பு / Notice of Demand - 02<br>
                                                දිනය / திகதி / Date</u> &nbsp;&nbsp;<input class="text" type="date" id="date" name="date" placeholder="mm/dd/yyyy"/>
                            </div>

                            <div class="detail">
                            මහත්මයානණනි / මහත්මියනි,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="u">&nbsp;&nbsp;{{$pawnings->customer->name}}&nbsp;&nbsp;</span><br>
                            <span class="tamil">அன்புடைய திரு / திருமதி / செல்வன் / செல்வி,</span><br>
                            Dear Mr. / Mrs,<br><br>

                            රන් ණය පහසූකම් අංකය. <span class="u">&nbsp;&nbsp;{{ Auth::user()->pawningNumberFormat($pawnings->pawning_id) }}&nbsp;&nbsp;</span><br>
                            <span class="tamil">தங்க கடன் வசதி இல. </span><span class="u">&nbsp;&nbsp;{{ Auth::user()->pawningNumberFormat($pawnings->pawning_id) }}&nbsp;&nbsp;</span><br>
                            Gold Loan Facility No. <span class="u">&nbsp;&nbsp;{{ Auth::user()->pawningNumberFormat($pawnings->pawning_id) }}&nbsp;&nbsp;</span><br><br>

                            රන් ණය අත්තිකාරම් මුදල රු. <span class="u">&nbsp;&nbsp;{{number_format($pawnings->full_amount_payout, 2)}}&nbsp;&nbsp;</span><br>
                            <span class="tamil">தங்க கடன் முற்பணம் ரூபா.</span><span class="u">&nbsp;&nbsp;{{number_format($pawnings->full_amount_payout, 2)}}&nbsp;&nbsp;</span><br>
                            Gold Loan Advance Rs. <span class="u">&nbsp;&nbsp;{{number_format($pawnings->full_amount_payout, 2)}}&nbsp;&nbsp;</span><br><br>

                            ඔබ විසින් අප ආයතනයේ ඇපයට තබන ලද රන් භාණ්ඩ සඳහා ගිවිසගත් කාලසීමාව ඉකුත්ව ගියද මේ දක්වා නිදහස් කරගැනිමට හෝ පොළිය/වාරික ගෙවා අලුත් කිරීමට ඔබ අපොහොසත්ව ඇත.
                            උකස් පනතේ 85 වන වගන්තියට ප්‍රකාරව එකී භාණ්ඩ මෙම ලිපියේ දින සිට මසක් ඇතුලත නිදහස් කරගැනිමට හෝ පොළිය/වාරික ගෙවා අලුත්කිරීමට කටයුතු කරන මෙන් ඉල්ලා සිටිමු.<br>
                            <span class="tamil">எமது கம்பனியில் அடகு வைக்கப்பட்ட நகைகளுக்கான ஒப்பந்த காலம் நிறைவடைந்துள்ளது எனினும் நீங்கள் நகைகளை மீட்பதற்கோ அல்லது
                            வட்டியை / தவணையை மீள செலுத்துவதன் மூலம் ஒப்பந்த விதிகளை புதுப்பிப்பதற்கோ தவறிவிட்டீர்கள். அடமான சட்டத்தின் 85ஆம் பிரிவுக்கு
                            அமைய இக் கடிதத்தின் திகதியிலிருந்து ஒரு மாதத்திற்குள் நகைகளை மீட்பதற்கு அல்லது வட்டியை / தவணையை மீள செலுத்துவதன் மூலம்
                            ஒப்பந்த விதிகளை புதுப்பிப்பதற்கு நடவடிக்கை எடுக்குமாறு வேண்டப்படுகின்றீர்கள்.</span><br><br>

                            The period of agreement has passed on the gold items you have mortgaged in our Company and however, you have failed so far to redeem them or to renew the term by paying
                            interest/rental. You are requested to take action to redeem those items or renew their term by paying the interest/rental within a month of this letter as per Section 85 of the Mortgage Act.<br><br>

                            <div style="text-align: left; margin-right:2em"> මෙයට විශ්වාසි, <span class="tamil">உங்கள் உண்டமயுள்ள,</span> Yours faithfully,<br><br><br>
                            .....................................................................................................................<br>
                            ශාඛා කළමණාකරු / <span class="tamil">கிளை முகாமையாளர்</span> / Branch Manager <br><br>

                            මෙම ලිපිය ලැබෙන විට ඔබ පොළිය ගෙවීමට හෝ නිදහස් කරගැනීමට කටයූතු කර ඇත්නම් මෙම ලිපිය නොසලකා හරින්න.<br>
                            <span class="tamil">வட்டியை மீளச்செலுத்த அல்லது நகைகளை மீட்பதற்கு இக் கடிதத்தை பெறுவதற்கு முன்னர் நடவடிக்கை எடுத்திருப்பின் தயவுசெய்து இக்
                            கடிதத்தை புறக்கணிக்கவும்.</span><br><br>

                            Please ignore this letter if you have taken action to pay the interest or redeem items by the time of its receipt.<br><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="row none-print">
             <button type="button" class="button" id="print">Print</button>
            </div>
        </div>

    {{-- <div id="reciept" style="display: none;">
        <div style="width: 217mm; height: 139mm; display: flex; flex-wrap: wrap;">
        </div>
    </div> --}}
    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <script>
        // var pawning = <php echo json_encode($pawningReminderTwo); ?>;
            $(document).ready(function(){
            // console.log(pawnings);
            // var divContents = document.getElementById("reciept").innerHTML;
            // var a = window.open("", "", "width=1000mm, height=600mm");
            // a.document.write("<html>");
            // a.document.write("<body>");
            // a.document.write(divContents);
            // a.document.write("</body></html>");
            // a.document.close();
            // a.print();
            // a.close();
            // window.print();
            $('#print').on('click', function(){
                window.print();
            });
        });
    </script>
</body>
</html>
