<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Payment Limit Reached</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at top left, #fff0e5, transparent 35%),
                #f4f7fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 35px 20px;
            color: #14253e;
        }

        .limit-wrapper {
            width: 100%;
            max-width: 980px;
            min-height: 620px;
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            background: #fff;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 25px 65px rgba(25, 40, 65, 0.12);
        }

        /* Left content */
        .limit-content {
            padding: 44px 55px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .limit-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 24px;
            border-radius: 50%;
            background: #fff3e9;
            color: #f58634;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 39px;
            position: relative;
        }

        .limit-icon::after {
            content: "";
            position: absolute;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: #f58634;
            border: 3px solid white;
            top: 5px;
            right: 5px;
        }

        .limit-content h1 {
            font-size: 29px;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 14px;
        }

        .limit-description {
            font-size: 14px;
            line-height: 1.8;
            color: #718096;
            margin-bottom: 27px;
        }

        /* Limit information */
        .limit-info {
            border: 1px solid #ffddc4;
            background: #fffdfb;
            border-radius: 15px;
            padding: 20px;
            text-align: left;
            margin-bottom: 24px;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 13px;
        }

        .info-row+.info-row {
            margin-top: 19px;
        }

        .info-icon {
            width: 37px;
            height: 37px;
            flex-shrink: 0;
            border-radius: 10px;
            background: #fff0e4;
            color: #f58634;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-text strong {
            display: block;
            font-size: 13px;
            margin: 2px 0 6px;
            color: #172b48;
        }

        .info-text p {
            color: #718096;
            font-size: 12px;
            line-height: 1.6;
        }

        /* Buttons */
        .btn {
            min-height: 48px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 12px 18px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(100deg, #fa842e, #ec6414);
            color: white;
            box-shadow: 0 9px 20px rgba(245, 134, 52, 0.22);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(245, 134, 52, 0.3);
        }

        .btn-secondary {
            margin-top: 11px;
            border: 1px solid #e0e5ec;
            color: #50647f;
            background: #fff;
        }

        .btn-secondary:hover {
            background: #f8fafc;
        }

        .secure-note {
            margin-top: 20px;
            font-size: 12px;
            color: #94a3b8;
        }

        .secure-note i {
            margin-right: 5px;
        }

        /* Right orange panel */
        .limit-sidebar {
            position: relative;
            overflow: hidden;
            padding: 46px 38px;
            background: linear-gradient(155deg, #fa8734 0%, #ed6d1a 55%, #cb4e0c 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }

        .circle-top {
            width: 300px;
            height: 300px;
            top: -120px;
            right: -105px;
        }

        .circle-bottom {
            width: 220px;
            height: 220px;
            bottom: -120px;
            left: -100px;
        }

        .sidebar-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 25px;
            position: relative;
        }

        .limit-sidebar h2 {
            font-size: 29px;
            line-height: 1.3;
            font-weight: 800;
            margin-bottom: 15px;
            position: relative;
        }

        .limit-sidebar>p {
            font-size: 14px;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.95);
            position: relative;
        }

        .sidebar-card {
            position: relative;
            margin-top: 35px;
            padding: 21px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .sidebar-card h3 {
            font-size: 15px;
            margin-bottom: 8px;
        }

        .sidebar-card p {
            font-size: 12px;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.95);
        }

        .support-text {
            position: relative;
            margin-top: auto;
            padding-top: 35px;
            font-size: 12px;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.9);
        }

        .support-text a {
            color: white;
            font-weight: 700;
        }

        /* Tablet and mobile */
        @media (max-width: 800px) {
            body {
                padding: 24px 15px;
            }

            .limit-wrapper {
                max-width: 520px;
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .limit-content {
                padding: 38px 28px;
            }

            .limit-sidebar {
                padding: 35px 28px;
                min-height: 370px;
            }

            .limit-content h1,
            .limit-sidebar h2 {
                font-size: 25px;
            }

            .support-text {
                margin-top: 20px;
            }
        }

        @media (max-width: 380px) {
            .limit-content {
                padding: 30px 18px;
            }

            .limit-sidebar {
                padding: 30px 22px;
            }

            .limit-content h1,
            .limit-sidebar h2 {
                font-size: 23px;
            }

            .limit-description {
                font-size: 13px;
            }

            .limit-info {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="limit-wrapper">
        <!-- Main message -->
        <main class="limit-content">
            <div class="limit-icon">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <h1>Daily Payment Limit Reached</h1>
            <p class="limit-description"> You have reached your allowed payment limit for today. Please try again tomorrow when your daily limit resets. </p>
            <div class="limit-info">
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="info-text">
                        <strong>Daily Limit Reached</strong>
                        <p>You have used all the payments allowed for today.</p>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                    <div class="info-text">
                        <strong>When Can I Pay Again?</strong>
                        <p>You can make another payment after your daily limit resets tomorrow.</p>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="info-text">
                        <strong>Your Information Is Safe</strong>
                        <p>This restriction helps maintain fair and secure payment access.</p>
                    </div>
                </div>
            </div>
            {{-- <a href="/" class="btn btn-primary">
                <i class="fa-solid fa-house"></i> Return to Home <i class="fa-solid fa-arrow-right"></i>
            </a> --}}
            {{-- <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Go Back </a> --}}
            <p class="secure-note">
                <i class="fa-solid fa-lock"></i> Your payment information remains secure
            </p>
        </main>
        <!-- Orange information panel -->
        <aside class="limit-sidebar">
            <div class="circle circle-top"></div>
            <div class="circle circle-bottom"></div>
            <div class="sidebar-icon">
                <i class="fa-solid fa-credit-card"></i>
            </div>
            <h2>Payment Limit <br>Reached </h2>
            <p> We noticed that you've reached your daily payment allowance. Don't worry! You can continue making payments once your daily limit resets. </p>
            <div class="sidebar-card">
                <div class="sidebar-card-icon">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h3>Come Back Tomorrow!</h3>
                <p> Your daily payment allowance will be available again after the daily reset. Thank you for your understanding. </p>
            </div>
            <div class="support-text"> Need help or have questions? <br> Please contact our support team for assistance. </div>
        </aside>
    </div>
</body>

</html>