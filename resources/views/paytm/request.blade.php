<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Apexonline - Payment Page</title>
</head>

<body>
    <p>Please wait while we are opening the payment screen...</p>
    <script>
    function onScriptLoad() {

        var config = {
            "root": "",
            "flow": "DEFAULT",
            "data": {
                "orderId": "{{ $token['orderId'] }}",
                "token": "{{ $token['txnToken'] }}",
                "tokenType": "TXN_TOKEN",
                "amount": "{{ $token['amount'] }}"
            },
            "handler": {
                "notifyMerchant": function(eventName, data) {
                    console.log("notifyMerchant handler function called");
                    console.log("eventName => ", eventName);
                    console.log("data => ", data);
                }
            }
        };

        if (window.Paytm && window.Paytm.CheckoutJS) {
            window.Paytm.CheckoutJS.onLoad(function excecuteAfterCompleteLoad() {
                // initialze configuration using init method
                window.Paytm.CheckoutJS.init(config).then(function onSuccess() {
                    // after successfully updating configuration, invoke JS Checkout
                    window.Paytm.CheckoutJS.invoke();
                }).catch(function onError(error) {
                    console.log("error => ", error);
                });
            });
        }
    } 
    </script>

    @if ($transaction->env == 'sandbox')
    <script type="application/javascript" src="https://securegw-stage.paytm.in/merchantpgpui/checkoutjs/merchants/{{ setting('paytm','mid') }}.js" onload="payNow();"></script>
    @else
    <script type="application/javascript" src="https://secure.paytmpayments.com/merchantpgpui/checkoutjs/merchants/{{setting('paytm','mid')}}.js" onload="onScriptLoad();" crossorigin="anonymous"></script>
    @endif
</body>
</html>