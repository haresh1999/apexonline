<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Payment Gateway | PayFlow</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #080e1c;
            --card: #111b2e;
            --input: #0b1425;
            --border: #293750;
            --text: #f1f5ff;
            --muted: #92a1ba;
            --blue: #6487ff;
            --green: #39d9a5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(ellipse at 15% 0%, #19284a 0, transparent 40%),
                radial-gradient(ellipse at 100% 50%, #102b32 0, transparent 32%),
                var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 36px 20px;
        }

        .container {
            max-width: 1080px;
            margin: 0 auto;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 55px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #6487ff, #8c6cff);
            font-size: 23px;
            font-weight: 800;
            box-shadow: 0 8px 28px #6487ff35;
        }

        .brand h2 {
            font-size: 20px;
            letter-spacing: -0.7px;
        }

        .brand p {
            color: var(--muted);
            font-size: 12px;
            margin-top: 4px;
        }

        .secure-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #a6f3d8;
            background: #12352f;
            border: 1px solid #205746;
            border-radius: 30px;
            padding: 10px 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .hero {
            text-align: center;
            margin-bottom: 38px;
        }

        .eyebrow {
            display: inline-block;
            color: #a8b9ff;
            background: #19264a;
            border: 1px solid #303f70;
            padding: 8px 14px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: clamp(30px, 5vw, 48px);
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1.15;
        }

        .hero h1 span {
            background: linear-gradient(90deg, #88a4ff, #54e7b8);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }

        .hero p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.8;
            max-width: 520px;
            margin: 16px auto 0;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }

        .payment-card {
            background: linear-gradient(145deg, #152139, #10192b);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 30px;
            position: relative;
            overflow: hidden;
            transition: transform .25s, border-color .25s,
                box-shadow .25s;
        }

        .payment-card:hover {
            transform: translateY(-5px);
            border-color: #52668c;
            box-shadow: 0 20px 55px #00000030;
        }

        .payment-card.live {
            border-color: #245b4e;
            background: linear-gradient(145deg, #142b2c, #101b2a);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
        }

        .mode-icon {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            border-radius: 15px;
            font-size: 23px;
            background: #202e50;
        }

        .live .mode-icon {
            background: #174338;
        }

        .mode-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
            padding: 8px 10px;
            border-radius: 7px;
            color: #b4c5ff;
            background: #222f50;
        }

        .live .mode-label {
            color: #83edc6;
            background: #174338;
        }

        .card-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .card-description {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 24px;
            line-height: 1.7;
        }

        .amount-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #0b1424;
            border: 1px solid #29364d;
            border-radius: 14px;
            padding: 16px 18px;
            margin-bottom: 27px;
        }

        .amount-label {
            font-size: 12px;
            color: var(--muted);
        }

        .amount {
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .live .amount {
            color: var(--green);
        }

        .section-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.2px;
            color: #aebbd0;
            margin-bottom: 18px;
        }

        .form-group {
            margin-bottom: 17px;
        }

        .form-group label {
            display: block;
            color: #cbd5e7;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 9px;
        }

        .form-group input {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            background: var(--input);
            border: 1px solid #2a3850;
            border-radius: 10px;
            outline: none;
            color: var(--text);
            font-family: inherit;
            font-size: 13px;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-group input::placeholder {
            color: #66758d;
        }

        .form-group input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px #6487ff20;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 13px;
        }

        .pay-button {
            width: 100%;
            min-height: 51px;
            border: 0;
            border-radius: 12px;
            margin-top: 8px;
            cursor: pointer;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(100deg, #597eff, #8269ff);
            box-shadow: 0 8px 24px #6487ff28;
            transition: transform .2s, filter .2s;
        }

        .pay-button:hover {
            filter: brightness(1.12);
            transform: translateY(-2px);
        }

        .live .pay-button {
            color: #06241b;
            background: linear-gradient(100deg, #54e3b3, #8bf0c8);
            box-shadow: 0 8px 24px #39d9a520;
        }

        .button-icon {
            margin-right: 8px;
        }

        .card-footer {
            text-align: center;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.8;
            margin-top: 18px;
        }

        .footer {
            text-align: center;
            color: #6f7d94;
            font-size: 12px;
            margin-top: 40px;
            line-height: 1.9;
        }

        .footer strong {
            color: #a6b6cf;
        }

        @media (max-width: 760px) {
            body {
                padding: 24px 15px;
            }

            .navbar {
                margin-bottom: 40px;
            }

            .payment-grid {
                grid-template-columns: 1fr;
                max-width: 520px;
                margin: 0 auto;
            }

            .payment-card {
                padding: 24px 20px;
            }

            .hero h1 {
                letter-spacing: -1.3px;
            }

            .secure-badge {
                font-size: 0;
                padding: 11px;
            }

            .secure-badge span {
                font-size: 17px;
            }
        }

        @media (max-width: 380px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .brand h2 {
                font-size: 17px;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <main class="payment-grid">

            <!-- Sandbox Payment -->
            <section class="payment-card">
                <div class="card-top">
                    <div class="mode-icon">🧪</div>
                    <span class="mode-label">TEST MODE</span>
                </div>
                <form action="{{ url('/sandbox/request') }}" method="post">
                    @csrf

                    <div class="form-group">
                        <label>Refresh Token</label>
                        <input type="text" name="refresh_token" placeholder="Enter refresh token">
                    </div>

                    <div class="form-group">
                        <label>Signature</label>
                        <input type="text" name="signature" placeholder="Enter signature">
                    </div>

                    <div class="form-group">
                        <label>Payer Name</label>
                        <input type="text" name="payer_name" value="Haresh" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="payer_email" value="haresh@swapinfoway.com" required>
                        </div>

                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="tel" name="payer_mobile" value="9106029220" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Amount (INR)</label>
                            <input type="number" name="amount" value="1" min="1" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label>Order ID</label>
                            <input type="text" name="order_id" value="ORD_{{ time() }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Callback URL</label>
                        <input type="url" name="callback_url" value="{{ env('APP_URL') }}/callback" required>
                    </div>

                    <div class="form-group">
                        <label>Redirect URL</label>
                        <input type="url" name="redirect_url" value="{{ env('APP_URL') }}/redirect" required>
                    </div>

                    <button type="submit" class="pay-button">
                        <span class="button-icon">↗</span>
                        Initiate Test Payment
                    </button>
                </form>
            </section>

            <!-- Live Payment -->
            <section class="payment-card live">
                <div class="card-top">
                    <div class="mode-icon">💳</div>
                    <span class="mode-label">LIVE MODE</span>
                </div>
                <form action="{{ url('/request') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label>Refresh Token</label>
                        <input type="text" name="refresh_token" placeholder="Enter refresh token">
                    </div>

                    <div class="form-group">
                        <label>Signature</label>
                        <input type="text" name="signature" placeholder="Enter signature">
                    </div>

                    <div class="form-group">
                        <label>Payer Name</label>
                        <input type="text" name="payer_name" value="Haresh" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="payer_email" value="haresh@swapinfoway.com" required>
                        </div>

                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="tel" name="payer_mobile" value="9106029220" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Amount (INR)</label>
                            <input type="number" name="amount" value="1" min="1" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label>Order ID</label>
                            <input type="text" name="order_id" value="ORD_{{ time() }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Callback URL</label>
                        <input type="url" name="callback_url" value="{{ env('APP_URL') }}/callback" required>
                    </div>

                    <div class="form-group">
                        <label>Redirect URL</label>
                        <input type="url" name="redirect_url" value="{{ env('APP_URL') }}/redirect" required>
                    </div>

                    <button type="submit" class="pay-button">
                        <span class="button-icon">↗</span>
                        Initiate Live Payment
                    </button>
                </form>
            </section>
        </main>
    </div>
</body>

</html>