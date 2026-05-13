<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Apexonline - Payment Page</title>
</head>

<body>

    <p>Please wait while we are opening the payment screen...</p>

    @if ($transaction->env == 'sandbox')
    <script type="application/javascript" src="https://securegw-stage.paytm.in/merchantpgpui/checkoutjs/merchants/{{ setting('paytm','mid') }}.js"></script>
    @else
    <script type="application/javascript" src="https://securegw.paytm.in/merchantpgpui/checkoutjs/merchants/{{ setting('paytm','mid') }}.js"></script>
    @endif
    <script>
        function payNow() {

            fetch("{{ $createOrderUrl }}").then(res => res.json()).then(data => {

                if (! data.status) {
                    if (confirm(data.message)) {
                        window.location.href = "{{ $transaction->redirect_url }}"
                    }
                }

                var config = {
                    flow: "DEFAULT",
                    data: {
                        orderId: data.orderId,
                        token: data.txnToken,
                        tokenType: "TXN_TOKEN",
                        amount: data.amount
                    },
                    handler: {
                        notifyMerchant: function(eventName, data){
                            console.log(eventName, data);
                        }
                    }
                };

                if (window.Paytm?.CheckoutJS) {

                    window.Paytm.CheckoutJS.init(config).then(function () {
                        console.log("Init Success");
                        window.Paytm.CheckoutJS.invoke();
                    })
                    .catch(function(error) {
                        console.log("Paytm Error", error);
                    });

                } else {
                    console.log("Paytm JS Not Loaded");
                }
            });
        }

        setTimeout(() => {
            payNow();
        }, 2000);
    </script>
</body>

</html>