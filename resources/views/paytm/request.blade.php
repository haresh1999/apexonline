{{--
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

    <button onclick="payNow()">Pay</button>

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

        // setTimeout(() => {
        //     payNow();
        // }, 2000);
    </script>
</body>

</html> --}}

<div class="pg-paytm-checkout">

    <div id="paytm-loader">
        Loading Paytm...
    </div>
    <script>
        function invokeBlinkCheckoutPopup() {
            fetch("{{ $createOrderUrl }}").then(response => response.json()).then(data => {
                console.log('Create Order Response', data);
                if (!data.status) {
                    alert(data.message || 'Unable to create payment');
                    return;
                }

                var config = {
                    root: "",
                    flow: "DEFAULT",
                    data: {
                        orderId: data.orderId,
                        token: data.txnToken,
                        tokenType: "TXN_TOKEN",
                        amount: data.amount
                    },
                    integration: {
                        platform: "Woocommerce",
                        version: "10.5.3"
                    },
                    handler: {
                        notifyMerchant: function(eventName, data) {
                            console.log("notifyMerchant",eventName,data);
                            if (eventName === "APP_CLOSED") {
                                document.getElementById('paytm-loader').style.display = 'none';
                                console.log("Payment popup closed");
                            }
                        }
                    }
                };

                console.log(config);

                if (window.Paytm && window.Paytm.CheckoutJS) {

                    window.Paytm.CheckoutJS.onLoad(function () {
                        console.log("Paytm Loaded");
                        window.Paytm.CheckoutJS.init(config).then(function () {
                                console.log("Init Success");
                                window.Paytm.CheckoutJS.invoke();
                            })
                            .catch(function(error) {
                                console.log("Paytm Error",error);
                            });
                        });
                } else {
                    console.log("Paytm CheckoutJS not found");
                }

            })
            .catch(error => {
                console.log(
                    'Fetch Error',
                    error
                );
            });
        }
    </script>
    <script type="application/javascript" crossorigin="anonymous" src="https://securegw.paytm.in/merchantpgpui/checkoutjs/merchants/{{ setting('paytm','mid') }}.js" onload="invokeBlinkCheckoutPopup();"></script>
</div>