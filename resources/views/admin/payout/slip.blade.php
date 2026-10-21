<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>


    <style>
        * {
            box-sizing: border-box;
            margin: 0px;
            padding: 0px;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #19334f;
            background: #e8f2fc;
        }

        .receipt {
            width: 93.80%;
            background: #ffffff;
            /* border: 1px solid #dce5ee; */
            border-radius: 14px;
            overflow: hidden;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 18px 16px 16px;
            background: #e8f2fc;
            border-bottom: 1px solid #d8e6f5;
        }

        .brand {
            display: inline-block;
            font-size: 21px;
            font-weight: bold;
            font-style: italic;
            color: #0753a1;
            border-bottom: 3px solid #f3aa2d;
            padding-bottom: 4px;
        }

        .success-icon {
            width: 58px;
            height: 58px;
            margin: 14px auto 12px;
            border-radius: 50%;
            position: relative;
            border: 4px solid;
            text-align: center;
        }

        .success-icon.success {
            background: #36b957;
            border-color: #c9f0d1;
        }

        .success-icon span {
            position: absolute;
            left: 0;
            right: 0;
            top: 40%;
            margin-top: -18px;
            font-family: DejaVu Sans, sans-serif;
            font-size: 32px;
            font-weight: bold;
            line-height: 36px;
            text-align: center;
            color: #ffffff;
        }

        /* Amount */
        .amount {
            font-size: 28px;
            font-weight: bold;
            color: #102b49;
            margin-bottom: 10px;
            overflow-wrap: anywhere;
        }

        /* Status */
        .status {
            display: inline-block;
            padding: 6px 13px;
            border-radius: 20px;
            background: #e1f6e7;
            color: #218343;
            border: 1px solid #bde8c7;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Account holder */
        .holder-label {
            margin-top: 13px;
            margin-bottom: 5px;
            font-size: 11px;
            color: #657c94;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .holder-name {
            font-size: 16px;
            line-height: 1.4;
            font-weight: bold;
            overflow-wrap: anywhere;
            word-wrap: break-word;
        }

        /* Transaction details */
        .details {
            padding: 8px 18px 12px;
            background: #ffffff;
            border-radius: 14px;
        }

        .details-title {
            padding: 10px 0;
            font-size: 14px;
            font-weight: bold;
            color: #19446c;
            border-bottom: 1px solid #e4eaf0;
        }

        .detail-row {
            display: table;
            width: 100%;
            table-layout: fixed;
            padding: 10px 0;
            border-bottom: 1px solid #e7ecf1;
            page-break-inside: avoid;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label,
        .detail-value {
            display: table-cell;
            vertical-align: top;
            font-size: 11px;
            line-height: 1.5;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .detail-label {
            width: 42%;
            padding-right: 8px;
            color: #718196;
        }

        .detail-value {
            width: 58%;
            text-align: right;
            font-weight: bold;
            color: #192b40;
        }

        .transaction-id {
            font-size: 10px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 10px 14px;
            background: #f5f8fc;
            border-top: 1px solid #e2eaf2;
            color: #718196;
            font-size: 10px;
            line-height: 1.6;
        }

        .footer strong {
            color: #28547d;
            font-size: 11px;
        }

        /* Mobile responsive design */
        @media screen and (max-width: 480px) {
            body {
                padding: 10px;
            }

            .receipt {
                max-width: 100%;
                border-radius: 14px;
            }

            .header {
                padding: 22px 14px 18px;
            }

            .brand {
                font-size: 20px;
            }

            .amount {
                font-size: 27px;
            }

            .holder-name {
                font-size: 15px;
            }

            .details {
                padding: 8px 14px 12px;
            }

            .detail-label,
            .detail-value {
                font-size: 10px;
            }

            .transaction-id {
                font-size: 9px;
            }
        }

        /* PDF / Print optimization */
        @media print {

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100%;
                background: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .receipt {
                width: 100%;
                max-width: none;
                margin: 0;
                border-radius: 8px;
                box-shadow: none;
                overflow: hidden;
                page-break-inside: avoid;
            }

            .header {
                padding: 12px 14px !important;
            }

            .brand {
                font-size: 19px;
            }

            .success-icon {
                width: 48px;
                height: 48px;
                margin: 8px auto;
                font-size: 28px;
                line-height: 40px;
            }

            .amount {
                font-size: 24px;
                margin-bottom: 7px;
            }

            .holder-label {
                margin-top: 9px;
                margin-bottom: 4px;
            }

            .holder-name {
                font-size: 13px;
            }

            .details {
                padding: 5px 12px 7px !important;
            }

            .details-title {
                padding: 7px 0 !important;
                font-size: 12px;
            }

            .detail-row {
                padding: 7px 0 !important;
                page-break-inside: avoid;
            }

            .detail-label,
            .detail-value {
                font-size: 9px;
                line-height: 1.4;
            }

            .transaction-id {
                font-size: 8px;
            }

            .footer {
                padding: 7px 10px !important;
                font-size: 9px;
            }

            .footer strong {
                font-size: 10px;
            }
        }
    </style>

</head>


<body>
    <div class="pdf-page">
        <div class="receipt">

            <div class="header">

                <div class="success-icon {{ strtolower($payout->status) === 'success' ? 'success' : 'failed' }}">
                    <span>
                        {{ strtolower($payout->status) === 'success' ? '✓' : '✕' }}
                    </span>
                </div>

                <!-- Amount -->
                <div class="amount">
                    &#8377;{{ $payout->amount }}
                </div>

                <!-- Status -->
                <div class="status">{{strtoupper($payout->status)}}</div>

                <!-- Account Holder -->
                <div class="holder-label">Account Holder</div>

                <div class="holder-name">
                    {{$payout->holder_name}}
                </div>

            </div>

            <div class="details">

                <div class="details-title">
                    Transaction Details
                </div>

                <!-- Transaction ID -->
                <div class="detail-row">
                    <div class="detail-label">Transaction ID</div>
                    <div class="detail-value transaction-id">
                        {{strtoupper($payout->order_id)}}
                    </div>
                </div>

                <!-- Status -->
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-value" style="color:#218343;">
                        {{strtoupper($payout->status)}}
                    </div>
                </div>

                <!-- Date and Time -->
                <div class="detail-row">
                    <div class="detail-label">Date &amp; Time</div>
                    <div class="detail-value">
                        {{ Carbon\Carbon::parse($payout->created_at)->format('d m Y') }},<br>
                        {{ Carbon\Carbon::parse($payout->created_at)->format('h:i A') }}
                    </div>
                </div>

                <!-- Account Number -->
                <div class="detail-row">
                    <div class="detail-label">Account Number</div>
                    <div class="detail-value">
                        {{$payout->account_number}}
                    </div>
                </div>

                <!-- IFSC -->
                <div class="detail-row">
                    <div class="detail-label">IFSC Code</div>
                    <div class="detail-value">
                        {{$payout->ifsc_code}}
                    </div>
                </div>

            </div>

            <div class="footer">
                <strong>Transaction Receipt</strong><br>
                Please keep this receipt for your records.
            </div>
        </div>
    </div>
</body>

</html>