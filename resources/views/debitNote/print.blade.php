<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <style>
            @page {
                width: 217mm;
                height:280mm;
                margin:0mm;
            }
            html, body {
                width: 217mm;
                height: 280mm;
                margin: 0;
                padding: 0;
                background-color: rgb(235, 235, 235);
            }
            .container {
                width: inherit;
                height: inherit;
                margin-left: 0%;
                margin-right:70%;
                margin-top:up;
                padding: 0;
                background-color: white;
            }
            .content {
                width: 217mm;
            }
            .note-preview-inner {
                width: 217mm;
            }
            .detail{
                margin-left:30px;
                margin-right:30px;
            }
            .row {
                display: flex;
                flex-wrap: wrap;
            }
            .row-div {
                display: flex;
                flex-wrap: wrap;
            }
            .logo {
                position: relative;
                width: 100%;
                margin-left:30px;
                margin-top:5px;
            }
            .note-label {
                position: sticky;
                float:right;
                padding-right:50px;
                font-size:42px;
                height: 9mm;
                line-height: 80px;
                font-family:sans-serif;
            }
            .number{
                padding-right:180px;
                font-size:18px;
                line-height:-50px;
                float:right;
                font-weight:bold;
            }
            .th-div span {
                display: table-cell;
                vertical-align: middle;
            }
            .td-div {
                height: 8mm;
                display: table;
                text-align: center;
                font-size: 13px;
            }
            .space{
                padding-left:6px;
            }
            .line1{
                border-bottom: 2px solid;
                width:100%;
            }
            .line2{
                border-bottom: 2px solid;
                width:100%;
            }
            .detail2{
                float:right;
                padding-left:420px;
                padding-right:130px;
            }
            .detail3{
                /* float:right; */
                /* padding-left:420px; */
                padding-left:180px;
                /* font-size:15px; */
                /* padding-right:165px; */
                line-height:20px;
            }
            .line-space{
                margin-top:10px;
                margin-bottom:50px;
            }
            .table {
                display: table;
            }
            .table-row {
               display: table-row;
            }
            .table-cell {
               display: table-cell;
               padding: 3px 10px;
               border: 1px solid;
               height:250px;
               text-align: center;
            }
            .table-header{
               display: table-cell;
               padding: 1px;
               border: 1px solid;
               padding-left:55px;
               padding-right:55px;
               text-align:center;
            }
            .signature{
               text-align:left;
               width:50%;
               font-size:15px;
               font-weight:bold;
            }
            .footer{
                text-align:left;
                line-height:20px;
            }
            .footer-details{
                font-weight:bold;
                padding-left:6px;
            }
            .end-footer{
                text-align:center;
                font-weight:bold;
                background-color:lightslategrey;
            }
            .button{
            background-color: #007bff;
            border: none;
            color: white;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            line-height: 10%;
            width:95px;
            height:30px;
            margin-left:330px;
            /* margin-right:50%; */
            position: sticky;
        }
        </style>
        <style type="text/css" media="print">
            @page{
            size:portrait;
            }
            .none-print{
            display:none;
            }
        </style>
</head>
<body>
    <div class="container">
        <div id="app" class="content">
            <div class="editor">
            <div class="note-preview-inner">
              <div class="editor-content">
               <div class="preview-main client-preview">
                <div class="row">
                <div class="logo">
                    <div><img src="{{ asset('storage/uploads/logo/large_logo.png') }}"/>
                    <span class="note-label">DEBIT NOTE</span><br>
                    <span class="number">Debit Note No: {{ AUth::user()->debitNoteNumberFormat($debitNote->id) }}</span> </div>
                </div>
                <div class="detail">
                <div class="line1"></div>
                <div style="margin-top:10px">
                    @include('components.headOfficeBlockBolt')
                <div style="margin-bottom:10px"></div>
                <div class="line2"></div><br>
                <div class="row" style="margin-top:0.2px; width:100%">
                    <div style="width: 70%">
                        <span style="font-weight:bold">Bill To</span><br>
                        <table style="width: 100%">
                            <tr>
                                <td>Name</td><td style="text-align: center;">:</td><td>{{ $debitNote->debitVendor->name ?? ''}} {{ $debitNote->debitCustomer->name ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Address</td><td style="text-align: center;">:</td><td>{{ $debitNote->debitVendor->address ?? ''}} {{ $debitNote->debitCustomer->address ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Phone</td><td style="text-align: center;">:</td><td>{{ $debitNote->debitVendor->contact_phone ?? ''}} {{ $debitNote->debitCustomer->phone ?? '' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div style="width: 30%">
                        <table style="width: 100%">
                            <tr>
                                <td>Debit Note Date</td><td style="text-align: center;">:</td><td>{{ $debitNote->date ?? ''}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                {{-- <div>
                    <span style="font-weight:bold">Bill To</span><span class="detail2">Debit Note Date:</span><br>
                    Name<span style="padding-left:75px">:</span><span class="detail2">Terms :</span><br>
                    Company Name<span style="padding-left:10px">:<span class="detail2">Due Date:</span><br>
                    Address<span style="padding-left:62px">:</span><br>
                    Phone<span style="padding-left:75px">:</span><br>
                <div style="margin-bottom:0.2px"></div>
                </div> --}}
                <br>
                <div class="line1"></div>
                <div class="line-space"><b>
                    COMMENTS/SPECIAL INSTRUCTIONS:</b><br><br>
                </div>
                <div class="line1"></div><br>
                <div class="table">
                    <div class="table-row">
                        <div class="table-header"><span style="font-weight: bold;">QUANTITY</span></div>
                        <div class="table-header"><span style="font-weight: bold;">DESCRIPTION</span></div>
                        <div class="table-header"><span style="font-weight: bold;">UNIT PRICE</span></div>
                        <div class="table-header"><span style="font-weight: bold;">TOTAL</span></div>
                    </div>
                    <div class="table-row">
                        <div class="table-cell">{{ $debitNote->quantity ?? ''}}</div>
                        <div class="table-cell">{{ $debitNote->description ?? ''}}</div>
                        <div class="table-cell">{{ number_format($debitNote->amount, 2) }}</div>
                        <div class="table-cell">{{ number_format($debitNote->amount, 2) }}</div>
                    </div>
                </div><br>
                {{-- <div class="detail3">
                    <span style="padding-left:72px"><b>SUB TOTAL<span class="space">:</span></span></b><br>
                    <span style="padding-left:74px"><b>SALES TAX<span class="space">:</span></span></b><br>
                    <b>DELIVERY CHARGES<span class="space">:</span></b><br>
                    <span style="padding-left:70px"><b>TOTAL DUE<span class="space">:</span></span></b><br>
                </div> --}}
                <div class="detail3">
                    <table style="width: 100%;">
                        <tr>
                            <td style="text-align: right;"><b>SUB TOTAL</b></td><td style="text-align: center;"><b>:</b><td style="text-align: right;"><b>{{ number_format($debitNote->amount, 2) }}</b></td></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><b>SALES TAX</b></td><td style="text-align: center;"><b>:</b><td style="text-align: right;"><b>0.00</b></td></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><b>DELIVERY CHARGES</b></td><td style="text-align: center;"><b>:</b><td style="text-align: right;"><b>0.00</b></td></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><b>TOTAL D</b></td><td style="text-align: center;"><b>:</b><td style="text-align: right;"><b>{{ number_format($debitNote->amount, 2) }}</b></td></td>
                        </tr>
                    </table>
                    {{-- <span><b>SUB TOTAL<span class="space">:</span><span class="space">{{ number_format($debitNote->amount, 2) }}</span></span></b><br>
                    <span><b>SALES TAX<span class="space">:</span><span class="space">0.00</span></span></b><br>
                    <span><b>DELIVERY CHARGES<span class="space">:</span><span class="space">0.00</span></span></b><br>
                    <span><b>TOTAL DUE<span class="space">:</span><span class="space">{{ number_format($debitNote->amount, 2) }}</span></span></b><br> --}}
                </div>
                <div>
                <div class="signature">
                    <p>..................................................</p>
                    <p>AUTHORIZED SIGNATURE</p>
                </div>
                <div class="line1"></div>
                <div style="margin-top:2px"></div>
                <div class="footer">
                    Make all cheques payable to<span class="space">:</span><span class="footer-details">{{ config('app.name') }}</span><br>
                    Payment is due within 30 days.<br>
                    If you have any questions concerning this Debit Note, contact<span class="space">:</span><span class="footer-details">031-223 0288</span><br>
                </div>
                <div style="margin-bottom:2px"></div>
                <div class="line1"></div><br>
                <div class="end-footer">THANK YOU FOR YOUR BUSINESS!</div>
            </div><br>

            <div class="col-lg-12 none-print">
                <div class="row none-print">
                <button type="button" class="button" id="print">Print</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>

       <script>
       // var pawnings = <php echo json_encode($pawnings); ?>;
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
