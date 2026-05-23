<!DOCTYPE html>
<html>

<head>
    <title>Apexonline - Payment Page</title>
    <script src="https://static.zohocdn.com/zpay/zpay-js/v1/zpayments.js"></script>
</head>

<body>
    <p>Please wait while we are opening the payment screen...</p>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            let config = {
                account_id: "{{ $accountId }}",
                domain: "IN",
                otherOptions: {
                    api_key: "{{ env('ZOHO_API_KEY') }}",
                    __environment: "",
                    request_origin: "woocommerce-plugin"
                }
            };
            let instance = new window.ZPayments(config);
            let options = {
                amount: "{{ $amount }}",
                currency_code: "{{ $currency }}",
                payments_session_id: "{{ $paymentSessionId }}",
                currency_symbol: "₹",
                business: "{{ $business }}",
                description: "{{ $description }}",
                address: {
                    phone: "{{ $phone }}"
                }
            };
            instance.requestPaymentMethod(options).then(function (data) {
                console.log('Payment Success', data);
                if (data.payment_id && data.signature) {
                    window.location.href = "{{ url('/zoho/callback?ref_id='.$reference_id) }}" +"&payment_id=" +data.payment_id;
                }
            })
            .catch(error => {
                console.error(error);
                instance.close();
            });
        });
    </script>
</body>

</html>