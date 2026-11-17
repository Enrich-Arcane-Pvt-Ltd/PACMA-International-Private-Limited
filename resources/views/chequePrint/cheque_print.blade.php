<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <style>
        @page {
            width: 176.5mm;
            /* height: 280mm; */
            margin: 0mm;
        }
        html, body {
            /* width: 176.5mm;
            height: 89mm;
            margin: 0;
            padding: 0; */
            background-color: rgb(235, 235, 235);
        }
        .container {
            width: inherit;
            height: inherit;
            margin-left: 30%;
            margin-top: 10%;
            padding: 0;
            /* background: url('{{ asset("assets/receipt/receipt_image.jpg") }}');
            background-repeat: no-repeat; */
            background-size: 176.5mm 89mm;
        }
        .content {
            width: 176.5mm;
            height: 89mm;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="container" style="background-image: url('{{ asset("assets/receipt/receipt_image.jpg") }}'); background-repeat: no-repeat;">
        <div style="width: 176.5mm; height: 89mm; display: flex; flex-wrap: wrap;">

        </div>
    </div>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
    var cheques = <?php echo json_encode($cheques) ?>;

    $(document).ready(function(){
        console.log(cheques);
    });
</script>
</body>
</html>
