<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apexonline - Payment Page</title>
</head>

<body>

    <p>Please wait while we are opening the payment screen...</p>

    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>

    <script>
        const cashfree = Cashfree({
            mode: "{{ $mode }}"
        });

        setTimeout(() => {
            openPayment();
        }, 1000);

        async function openPayment() {

            try {

                let response = await fetch("{{ $orderUrl }}?reference_id={{ $referenceId }}");
                let data = await response.json();
                let checkoutOptions = {
                    paymentSessionId: data.payment_session_id,
                    redirectTarget: "_modal",
                    platformName: "wc",
                };
                cashfree.checkout(checkoutOptions).then((result) => {
                    if (result.error) {
                        submitCallback(data.order_id,"failed");
                    }

                    if (result.paymentDetails) {
                        submitCallback(data.order_id,"success");
                    }
                });
            } catch (error) {
                console.log(error);
                alert("Unable to initiate payment.");
            }
        }

        function submitCallback(orderId, status) {
            
            let form = document.createElement("form");
            form.method = "POST";
            form.action = "{{ $callbackUrl }}";
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="reference_id" value="{{ $referenceId }}">
                <input type="hidden" name="order_id" value="${orderId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>

</html>