<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Apexonline - Payment Page</title>
</head>

<body>
    <p>Please wait while we are opening the payment screen.</p>
    <form id="hdfcForm" method="post" action="{{ $url }}">
        @csrf
        <input type="hidden" name="reference_id" value="{{ $reference_id }}">
    </form>
    <script>
        document.getElementById('hdfcForm').submit();
    </script>
</body>

</html>