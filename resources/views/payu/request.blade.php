<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Apexonline - Payment Page</title>
</head>
<p>Please wait while we are opening the payment screen.</p>

<body>
    <form id="payuForm" method="post" action="{{ setting('payu','payu_url') }}">
        @foreach($data as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <input type="hidden" name="hash" value="{{ $hash }}">
    </form>

    <script>
        document.getElementById('payuForm').submit();
    </script>
</body>
</html>