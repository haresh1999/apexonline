<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6fa;
            color: #263445;
            padding: 30px 15px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        body::-webkit-scrollbar {
            display: none;
        }

        /* Main Wrapper */
        .payment-wrapper {
            max-width: 950px;
            width: 100%;
            margin: 0 auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 35px rgba(0, 0, 0, 0.07);
        }

        /* Header */
        .payment-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px;
            border: 1px solid #e5eaf1;
            border-radius: 10px;
            margin-bottom: 22px;
            gap: 10px;
        }

        .logo img {
            max-width: 120px;
            max-height: 45px;
            object-fit: contain;
        }

        .logo {
            font-size: 21px;
            font-weight: 700;
            color: #172c48;
        }

        .amount-box {
            text-align: right;
        }

        .amount-box small {
            display: block;
            color: #718096;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .amount-box strong {
            font-size: 21px;
            color: #172c48;
        }

        /* Alert */
        .alert {
            padding: 14px 16px;
            margin-bottom: 20px;
            border-radius: 9px;
            font-size: 14px;
            line-height: 1.5;
            border: 1px solid transparent;
        }

        .alert strong {
            font-weight: 700;
        }

        .alert-danger {
            background: #fff0f0;
            color: #c62828;
            border-color: #ffcaca;
        }

        .alert-success {
            background: #eaf8ef;
            color: #217644;
            border-color: #bce8ca;
        }

        .alert-warning {
            background: #fff8e7;
            color: #946200;
            border-color: #f5dfa1;
        }

        /* Two Column Layout */
        .payment-layout {
            display: grid;
            grid-template-columns: 0.85fr 1.15fr;
            gap: 22px;
            align-items: stretch;
        }

        /* Instructions */
        .instruction-card {
            border: 1px solid #e5eaf1;
            border-radius: 12px;
            padding: 24px 20px;
            background: #f9fbfe;
            height: 100%;
        }

        .instruction-card h3 {
            font-size: 18px;
            margin-bottom: 22px;
            color: #172c48;
        }

        .instruction-list {
            list-style: none;
        }

        .instruction-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.6;
            color: #68778a;
        }

        .step-number {
            min-width: 27px;
            height: 27px;
            border-radius: 50%;
            background: #edf3ff;
            color: #2463eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        /* Payment Main */
        .payment-main {
            min-width: 0;
        }

        /* QR Card */
        .qr-card {
            border: 1px solid #e5eaf1;
            border-radius: 12px;
            padding: 25px 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        .qr-card h2 {
            font-size: 19px;
            margin-bottom: 22px;
            color: #172c48;
        }

        .qr-image {
            width: 210px;
            height: 210px;
            max-width: 100%;
            object-fit: contain;
            padding: 8px;
            border: 1px solid #e5eaf1;
            border-radius: 10px;
        }

        .qr-card p {
            color: #718096;
            font-size: 13px;
            margin-top: 15px;
            line-height: 1.5;
        }

        /* UTR Section */
        .utr-section {
            margin-bottom: 15px;
        }

        .utr-section label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 9px;
            color: #263445;
        }

        .utr-section input {
            width: 100%;
            height: 58px;
            border: 1px solid #dce3ed;
            border-radius: 9px;
            padding: 0 16px;
            font-size: 16px;
            outline: none;
            transition: .2s;
        }

        .utr-section input:focus {
            border-color: #2463eb;
            box-shadow: 0 0 0 3px rgba(36, 99, 235, .08);
        }

        .utr-section input::placeholder {
            color: #a0aaba;
        }

        .validation-error {
            display: block;
            color: #c62828;
            font-size: 12px;
            margin-top: 7px;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            height: 52px;
            border: none;
            border-radius: 9px;
            background: #172c48;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }

        .submit-btn:hover {
            background: #254b75;
        }

        .submit-btn:active {
            transform: scale(.99);
        }

        /* Footer */
        .secure-footer {
            text-align: center;
            margin-top: 22px;
            font-size: 12px;
            color: #8793a3;
            line-height: 1.6;
        }

        /* Tablet */
        @media(max-width: 768px) {

            .payment-wrapper {
                max-width: 600px;
            }

            .payment-layout {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .payment-main {
                order: 1;
            }

            .instruction-card {
                order: 2;
            }

            .instruction-card {
                padding: 22px 18px;
            }
        }

        /* Mobile */
        @media(max-width: 480px) {

            body {
                padding: 12px 10px;
            }

            .payment-wrapper {
                padding: 14px;
                border-radius: 12px;
            }

            .payment-header {
                padding: 13px;
                margin-bottom: 16px;
            }

            .logo img {
                max-width: 105px;
                max-height: 40px;
            }

            .logo {
                font-size: 18px;
            }

            .amount-box small {
                font-size: 11px;
            }

            .amount-box strong {
                font-size: 18px;
            }

            .qr-card {
                padding: 22px 12px;
            }

            .qr-card h2 {
                font-size: 17px;
            }

            .qr-image {
                width: 190px;
                height: 190px;
            }

            .instruction-card {
                padding: 20px 15px;
            }

            .instruction-card h3 {
                font-size: 17px;
            }

            .instruction-list li {
                margin-bottom: 16px;
                font-size: 13px;
            }

            .submit-btn {
                height: 50px;
                font-size: 14px;
            }
        }

        @media(max-width: 350px) {

            .payment-wrapper {
                padding: 10px;
            }

            .payment-header {
                padding: 10px;
            }

            .amount-box strong {
                font-size: 16px;
            }

            .qr-image {
                width: 170px;
                height: 170px;
            }
        }
    </style>
</head>

<body>

    <div class="payment-wrapper">

        <!-- Header -->
        <div class="payment-header">

            <div class="logo">
                <img src="{{ asset('admin/assets/img/logo.jpeg') }}" alt="Store Logo">
            </div>

            <div class="amount-box">
                <small>Total Amount</small>
                <strong>₹{{ number_format($tnx->amount, 2) }}</strong>
            </div>

        </div>

        <!-- Error Alert -->
        @if (session('error'))
        <div class="alert alert-danger">
            <strong>Error!</strong> {{ session('error') }}
        </div>
        @endif

        <!-- Success Alert -->
        @if (session('success'))
        <div class="alert alert-success">
            <strong>Success!</strong> {{ session('success') }}
        </div>
        @endif

        <!-- Payment Layout -->
        <div class="payment-layout">

            <!-- LEFT SIDE: Payment Instructions -->
            <div class="instruction-card">

                <h3>Payment Instructions</h3>

                <ul class="instruction-list">

                    <li>
                        <span class="step-number">1</span>
                        <span>
                            Open Google Pay, PhonePe, Paytm or any UPI application.
                        </span>
                    </li>

                    <li>
                        <span class="step-number">2</span>
                        <span>
                            Scan the QR code displayed on the right side.
                        </span>
                    </li>

                    <li>
                        <span class="step-number">3</span>
                        <span>
                            Pay the exact amount shown on this page.
                        </span>
                    </li>

                    <li>
                        <span class="step-number">4</span>
                        <span>
                            After successful payment, copy your UTR / Transaction ID.
                        </span>
                    </li>

                    <li>
                        <span class="step-number">5</span>
                        <span>
                            Enter your UTR number and click Submit Payment Details.
                        </span>
                    </li>

                    <li>
                        <span class="step-number">6</span>
                        <span>
                            Your payment will be verified by our admin team.
                        </span>
                    </li>

                </ul>

            </div>


            <!-- RIGHT SIDE: QR Code and UTR -->
            <div class="payment-main">

                <!-- QR Code -->
                <div class="qr-card">

                    <h2>Scan & Pay Using UPI</h2>

                    <img src="{{ asset('assets/img/qr.jpeg') }}" alt="Scan to Pay QR Code" class="qr-image">

                    <p>
                        Scan this QR code using your preferred UPI app.
                    </p>

                </div>


                <!-- UTR Form -->
                <form action="{{ url('vyapar/utr/submit') }}" method="POST" autocomplete="off">

                    @csrf
                    @method('patch')

                    <input type="hidden" name="reference_id" value="{{ $tnx->reference_id }}">

                    <input type="hidden" name="tnx_id" value="{{ $tnx->id }}">

                    <div class="utr-section">

                        <label for="utr">
                            UTR / Transaction Reference Number
                        </label>

                        <input type="text" id="utr" name="utr" placeholder="Enter your UTR number" maxlength="30" value="{{ old('utr') }}" required>

                        @error('utr')
                        <span class="validation-error">
                            {{ $message }}
                        </span>
                        @enderror

                    </div>

                    <button type="submit" class="submit-btn">
                        Submit Payment Details →
                    </button>

                </form>

            </div>

        </div>


        <!-- Footer -->
        <div class="secure-footer">
            🔒 Secure Payment Submission
            <br>
            Thank you for shopping with us!
        </div>

    </div>

</body>

</html>