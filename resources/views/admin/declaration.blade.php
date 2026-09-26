<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Service Completion &amp; Satisfaction Declaration</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            font-family: 'DejaVu Sans', sans-serif !important;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #000000;
        }

        .page {
            width: 100%;
            height: auto;
            margin: 0 auto;
        }

        /* =========================
           TITLE
        ========================== */
        .title {
            text-align: center;
            font-size: 11.5pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        /* =========================
           COMMON TEXT
        ========================== */
        p {
            margin: 0 0 7px 0;
            padding: 0;
        }

        .bold {
            font-weight: bold;
        }

        /* =========================
           UNDERLINE FIELDS
        ========================== */
        .field {
            display: inline-block;
            height: 14px;
            line-height: 13px;
            border-bottom: 1px solid #000;
            vertical-align: baseline;
            white-space: nowrap;
            color: #000;
            font-weight: normal;
        }

        .name-field {
            min-width: 150mm;
            font-size: 14px;
        }

        .aadhaar-field {
            min-width: 50mm;
        }

        .address-field {
            width: 100%;
            margin-top: 2px;
        }

        .amount-field {
            min-width: 40mm;
        }

        .payment-date-field {
            min-width: 28mm;
            text-align: center;
        }

        .payment-mode-field {
            min-width: 45mm;
        }

        .payment-reference-field {
            min-width: 55mm;
        }

        .date-field {
            min-width: 30mm;
            text-align: center;
        }

        /* =========================
           FIRST INFORMATION BLOCK
        ========================== */
        .intro {
            margin-bottom: 8px;
        }

        .address-label {
            display: block;
            margin-top: 4px;
        }

        .contact-row {
            margin-top: 6px;
            margin-bottom: 8px;
            white-space: nowrap;
        }

        .email-field {
            min-width: 60mm;
            margin-right: 15px;
        }

        .phone-field {
            min-width: 40mm;
        }

        /* =========================
           CONFIRMATION BLOCK
        ========================== */
        .confirmation {
            margin-bottom: 8px;
        }

        /* =========================
           BULLET LIST
        ========================== */
        .confirm-heading {
            margin-bottom: 5px;
        }

        .bullet-list {
            margin: 0 0 8px 0;
            padding-left: 20px;
        }

        .bullet-list li {
            margin: 0 0 5px 0;
            line-height: 1.3;
        }

        .bullet-list li:last-child {
            margin-bottom: 0;
        }

        /* =========================
           DECLARATION PARAGRAPHS
        ========================== */
        .declaration {
            margin-bottom: 8px;
        }

        /* =========================
           BOTTOM SECTION
        ========================== */
        .bottom-section {
            margin-top: 10px;
        }

        .bottom-row {
            margin-bottom: 12px;
        }

        .bottom-label {
            font-weight: bold;
        }

        .signature-title {
            font-weight: bold;
            margin-top: 15px;
        }

        @media print {
            .page {
                page-break-inside: avoid;
                page-break-after: avoid;
            }
        }
    </style>
</head>

<body style="padding: 45px">

    <div class="page">

        {{-- TITLE --}}
        <div class="title" style="margin-bottom: 40px">
            SERVICE COMPLETION &amp; SATISFACTION DECLARATION
        </div>

        {{-- PERSONAL DETAILS --}}
        <div class="intro">
            <p>
                I,
                <span class="field name-field">
                    {{ $data['declarant_name'] ?? '' }}
                </span>
                {{-- Aadhaar No.
                <span class="field aadhaar-field">
                    {{ $data['aadhaar_no'] ?? '' }}
                </span>, --}}
            </p>

            {{-- <div class="address-label">
                residing at (Address)
            </div>

            <span class="field address-field">
                {{ $data['address'] ?? '' }}
            </span> --}}

            <div class="contact-row">
                <span>Email ID:</span>
                <span class="field email-field">
                    {{ $data['email'] ?? '' }}
                </span>

                <span>Phone No.:</span>
                <span class="field phone-field">
                    {{ $data['phone'] ?? '' }}
                </span>
            </div>
        </div>

        {{-- PAYMENT DETAILS --}}
        <div class="confirmation">
            <p>
                hereby voluntarily confirm that I have made a payment of &#8377;
                <span class="field amount-field">
                    {{ $data['amount'] ?? '' }}
                </span>
                to <span class="bold">APEXONLINE</span>
            </p>

            <p>
                on (Date)
                <span class="field payment-date-field">
                    {{ $data['payment_date'] ?? '' }}
                </span>,
                through (UPI/NETBANKING)
                <span class="field payment-mode-field">
                    {{ $data['payment_mode'] ?? '' }}
                </span>
            </p>

            <p>
                (Payment Gateway / Mode of Payment), bearing Payment Reference No.
                <span class="field payment-reference-field">
                    {{ $data['payment_reference_no'] ?? '' }}
                </span>.
            </p>
        </div>

        {{-- SERVICE CONFIRMATION --}}
        <p class="confirmation">
            I hereby confirm that the
            <span class="bold">
                service offered by APEXONLINE in connection with the above-mentioned
                transaction has been completed and delivered to me to my satisfaction.
            </span>
        </p>

        {{-- CONFIRMATION POINTS --}}
        <p class="confirm-heading">
            I further confirm that:
        </p>

        <ul class="bullet-list">
            <li>
                I have received and availed the service as agreed.
            </li>
            <li>
                I am satisfied with the service provided by APEXONLINE.
            </li>
            <li>
                There are
                <span class="bold">no pending service obligations</span>
                from APEXONLINE in relation to the above-mentioned transaction.
            </li>
            <li>
                I have no complaint regarding the completion or delivery of the
                above-mentioned service as of the date of this Declaration.
            </li>
            <li>
                <span class="bold">
                    The above-mentioned payment was made by me voluntarily and of my
                    own free will and choice. No person has coerced, pressured,
                    threatened, or unduly influenced me to make the payment.
                </span>
            </li>
            <li>
                <span class="bold">
                    I confirm that I made the payment based on my own decision and
                    understanding, and that no person made any false, fraudulent,
                    or misleading promise or representation to induce me to make
                    the payment.
                </span>
            </li>
        </ul>

        {{-- FINAL DECLARATION --}}
        <p class="declaration">
            I am making this declaration voluntarily, with full understanding of
            its contents and without any coercion, pressure, threat, undue influence,
            false promise, or misrepresentation from APEXONLINE or any other person.
        </p>

        {{-- BOTTOM DETAILS --}}
        <div class="bottom-section">
            <div class="bottom-row">
                <span class="bottom-label">Date:</span>
                <span class="field date-field">
                    {{ $data['date'] ?? date('d / m / Y') }}
                </span>
            </div>

            <div class="signature-title">
                SIGNATURE OF DECLARANT
            </div>
        </div>

    </div>

</body>

</html>