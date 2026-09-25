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
    <form id="paymentForm" method="POST" enctype="application/x-www-form-urlencoded" action="<?= htmlspecialchars($paymentUrl) ?>">
        <!-- Merchant ID -->
        <input type="hidden" name="me_id" value="<?= htmlspecialchars($merchantId) ?>">
        <!-- Encrypted Merchant Request -->
        <input type="hidden" name="merchant_request" value="<?= htmlspecialchars($merchant_request) ?>">
        <!-- Encrypted Hash -->
        <input type="hidden" name="hash" value="<?= htmlspecialchars($hash) ?>">
        {{-- <button type="submit">Pay Now</button> --}}
    </form>
</body>
<script>
    setTimeout(() => {
        document.getElementById('paymentForm').submit();
    }, 1000);
</script>

</html>