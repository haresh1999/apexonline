<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bill of Supply - Apex Online</title>
    <style>
        @page {
            margin: 25px 35px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #111;
            font-size: 12px;
            line-height: 1.35;
        }

        .invoice-wrapper {
            width: 100%;
        }

        /* ================= HEADER ================= */
        .header {
            width: 100%;
            padding-bottom: 8px;
            border-bottom: 2px solid #087ea8;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 68%;
            vertical-align: top;
        }

        .header-right {
            width: 32%;
            text-align: right;
            vertical-align: middle;
        }

        .company-name {
            font-size: 19px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company-info {
            font-size: 11px;
            line-height: 1.55;
        }

        /* Logo */
        .logo {
            font-family: Arial, sans-serif;
            font-size: 32px;
            font-weight: 300;
            letter-spacing: -2px;
            white-space: nowrap;
        }

        .logo .apex {
            color: #83c34e;
        }

        .logo .online {
            color: #762574;
            font-style: italic;
        }

        /* ================= TITLE ================= */
        .invoice-title {
            text-align: center;
            font-size: 23px;
            font-weight: bold;
            color: #087ea8;
            margin-top: 11px;
            margin-bottom: 14px;
        }

        /* ================= BILL / INVOICE INFO ================= */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .bill-to {
            width: 68%;
            vertical-align: top;
        }

        .invoice-details {
            width: 32%;
            vertical-align: top;
            text-align: right;
        }

        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .customer-name {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 7px;
        }

        .customer-info {
            font-size: 11px;
            line-height: 1.9;
        }

        .invoice-info {
            font-size: 11px;
            line-height: 2;
        }

        /* ================= ITEMS ================= */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        .items-table thead th {
            background: #087ea8;
            color: #fff;
            font-weight: bold;
            padding: 5px 6px;
            font-size: 12px;
        }

        .items-table th:first-child {
            text-align: left;
            width: 65%;
        }

        .items-table th:nth-child(2) {
            text-align: center;
            width: 15%;
        }

        .items-table th:nth-child(3) {
            text-align: right;
            width: 20%;
        }

        .items-table tbody td {
            padding: 7px 5px;
            vertical-align: top;
            font-size: 11px;
        }

        .item-name {
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.35;
        }

        .quantity {
            text-align: center;
        }

        .amount {
            text-align: right;
        }

        /* ================= LOWER CONTENT ================= */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 7px;
        }

        .left-bottom {
            width: 52%;
            vertical-align: top;
            padding-right: 15px;
        }

        .right-bottom {
            width: 48%;
            vertical-align: top;
        }

        .description-title {
            font-weight: bold;
            margin-top: 2px;
            margin-bottom: 7px;
            font-size: 12px;
        }

        .description {
            font-size: 11px;
            margin-bottom: 15px;
        }

        .words-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 7px;
        }

        .amount-words {
            font-size: 11px;
        }

        /* ================= TOTAL ================= */
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }

        .total-table td {
            padding: 5px 6px;
            font-size: 11px;
        }

        .subtotal-label {
            text-align: left;
        }

        .subtotal-value {
            text-align: right;
        }

        .grand-total td {
            background: #087ea8;
            color: white;
            font-weight: bold;
            font-size: 12px;
            padding: 5px 6px;
        }

        .grand-total .total-label {
            text-align: left;
        }

        .grand-total .total-value {
            text-align: right;
        }

        /* ================= SIGNATURE ================= */
        .signature-section {
            margin-top: 20px;
            text-align: center;
        }

        .for-company {
            font-size: 11px;
            margin-bottom: 7px;
        }

        .signature-box {
            width: 135px;
            height: 52px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 4px;
        }

        .signature-box img {
            max-width: 135px;
            max-height: 52px;
        }

        .signature-line {
            font-weight: bold;
            font-size: 12px;
            text-align: left;
            margin-left: 45px;
        }

        /* ================= TERMS ================= */
        .terms {
            margin-top: 12px;
        }

        .terms-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .terms-text {
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="invoice-wrapper">
        <!-- ================= HEADER ================= -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="header-left">
                        <div class="company-name"> APEX ONLINE </div>
                        <div class="company-info"> WARD NO 25 DOOR NO 704/1 SUFFIS ARCADE PATTOM <br> Thiruvananthapuram <br> Phone no. : 8075028573 <br> Email : apexonlinein@gmail.com <br> GSTIN : 32APEFH8226A1Z3 <br> State: 32-Kerala </div>
                    </td>
                    <td class="header-right">
                        <div class="logo">
                            <img width="100%" src="{{ public_path('admin/assets/img/logo.jpeg') }}" alt="">
                            {{-- <span class="apex">apex</span>
                            <span class="online">online</span> --}}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <!-- ================= TITLE ================= -->
        <div class="invoice-title"> Bill of Supply </div>
        <!-- ================= CUSTOMER + INVOICE DETAILS ================= -->
        <table class="details-table">
            <tr>
                <td class="bill-to">
                    <div class="section-title"> Bill To </div>
                    <div class="customer-name"> {{$data['customer_name']}} </div>
                    <div class="customer-info"> {{$data['email']}} <br> Contact No. : {{$data['mobile']}} </div>
                </td>
                <td class="invoice-details">
                    <div class="section-title"> Invoice Details </div>
                    <div class="invoice-info"> Invoice No. : {{$data['invoice_no']}} <br> Date : {{$data['date']}} </div>
                </td>
            </tr>
        </table>
        <!-- ================= ITEMS ================= -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item name</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-name"> {!! $data['item_name'] !!} </div>
                    </td>
                    <td class="quantity"> {{$data['quantity']}} </td>
                    <td class="amount"> ₹ {{$data['amount']}} </td>
                </tr>
            </tbody>
        </table>
        <!-- ================= DESCRIPTION + TOTAL ================= -->
        <table class="bottom-table">
            <tr>
                <!-- LEFT -->
                <td class="left-bottom">
                    <div class="description-title"> Description </div>
                    <div class="description"> UTR {{$data['utr']}} </div>
                    <div class="words-title"> Invoice Amount In Words </div>
                    <div class="amount-words"> {{amountInWords($data['amount'])}} </div>
                    <div class="terms">
                        <div class="terms-title"> Terms and Conditions </div>
                        <div class="terms-text"> Thanks for doing business with us! </div>
                    </div>
                </td>
                <!-- RIGHT -->
                <td class="right-bottom">
                    <table class="total-table">
                        <tr>
                            <td class="subtotal-label"> Sub Total </td>
                            <td class="subtotal-value"> ₹ {{$data['amount']}} </td>
                        </tr>
                        <tr class="grand-total">
                            <td class="total-label"> Total </td>
                            <td class="total-value"> ₹ {{$data['amount']}} </td>
                        </tr>
                    </table>
                    <!-- SIGNATURE -->
                    <div class="signature-section">
                        <div class="for-company"> For :APEX ONLINE </div>
                        <div class="signature-box">
                            <!-- Add signature image here -->
                            <!--
                        <img src="{{ public_path('images/signature.png') }}">
                        -->
                        </div>
                        <div class="signature-line"> Authorized Signatory </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>