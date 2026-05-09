<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Apexonline - Payment Request</title>
</head>

<body>
    <form id="payment-form" action="{{ $actionUrl }}" method="post">
        @csrf
        <input type="hidden" name="reference_id" value="{{ $transaction->reference_id }}">
        <p>Please wait while we redirecting you...</p>
    </form>
    <script>
        setTimeout(() => {
        document.getElementById('payment-form').submit();
    }, 1000);
    </script>
</body>

</html>