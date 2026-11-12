<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Transfer Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6fa;
            font-family: Arial, sans-serif;
            color: #263248;
        }

        .payment-wrapper {
            max-width: 540px;
            margin: 20px auto;
            padding: 10px;
        }

        .payment-card {
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, .05);
        }

        .payment-header {
            text-align: center;
            margin-bottom: 16px;
        }

        .payment-icon {
            width: 45px;
            height: 45px;
            background: #fff1e7;
            color: #f58634;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 22px;
        }

        .payment-header h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .payment-header p {
            font-size: 12px;
            color: #7a8495;
            margin: 0;
        }

        .bank-details {
            background: #f8faff;
            border: 1px solid #e4e9f2;
            border-radius: 11px;
            padding: 14px;
        }

        .detail-item {
            margin-bottom: 10px;
        }

        .detail-label {
            font-size: 10px;
            font-weight: 700;
            color: #68758b;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .detail-value {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            background: #fff;
            border: 1px solid #dce3ef;
            border-radius: 7px;
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 600;
            min-height: 37px;
            word-break: break-all;
        }

        .copy-btn {
            border: none;
            background: #fff1e7;
            color: #ed7929;
            border-radius: 5px;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .copy-all-btn {
            background: #263b60;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 7px;
            font-weight: 600;
            font-size: 13px;
            width: 100%;
            margin-top: 5px;
        }

        .upload-section {
            margin-top: 18px;
        }

        .upload-section h5 {
            font-size: 16px;
        }

        .upload-section>p {
            font-size: 12px;
            margin-bottom: 10px;
        }

        .upload-box {
            border: 2px dashed #ccd5e4;
            background: #fafbfe;
            border-radius: 9px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
        }

        .upload-icon {
            font-size: 24px;
            color: #f58634;
        }

        .upload-box p {
            margin: 4px 0;
            font-size: 13px;
            font-weight: 600;
        }

        .upload-box small {
            font-size: 11px;
            color: #8993a3;
        }

        .form-label {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .form-control {
            font-size: 13px;
            padding: 9px 11px;
        }

        .submit-btn {
            width: 100%;
            background: #f58634;
            color: white;
            border: none;
            padding: 11px;
            border-radius: 7px;
            font-weight: 700;
            font-size: 14px;
            margin-top: 14px;
        }

        .submit-btn:hover {
            background: #df7023;
            color: white;
        }

        .security-note {
            font-size: 10px;
            color: #8791a1;
            text-align: center;
            margin-top: 10px;
        }

        @media(max-width:576px) {
            .payment-wrapper {
                margin: 8px auto;
                padding: 8px;
            }

            .payment-card {
                padding: 15px 12px;
                border-radius: 11px;
            }

            .payment-header h3 {
                font-size: 18px;
            }

            .bank-details {
                padding: 11px;
            }

            .detail-value {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="payment-wrapper">
        <div class="payment-card">
            <!-- Header -->
            <div class="payment-header">
                <div class="payment-icon">🏦</div>
                <h3>Bank Transfer Payment</h3>
                <p>Transfer your payment using the bank details below.</p>
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

            <!-- Bank Details -->
            <div class="bank-details">
                <div class="detail-item">
                    <div class="detail-label">Bank Name</div>
                    <div class="detail-value">
                        <span class="copy-value">{{ $setting['bank_name'] }}</span>
                        <button type="button" class="copy-btn" onclick="copyText(this)">Copy</button>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Account Holder's Name</div>
                    <div class="detail-value">
                        <span class="copy-value">{{ $setting['account_no'] }}</span>
                        <button type="button" class="copy-btn" onclick="copyText(this)">Copy</button>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Account Number</div>
                    <div class="detail-value">
                        <span class="copy-value">{{ $setting['iban'] }}</span>
                        <button type="button" class="copy-btn" onclick="copyText(this)">Copy</button>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">IBAN</div>
                    <div class="detail-value">
                        <span class="copy-value">{{ $setting['bank_holder_name'] }}</span>
                        <button type="button" class="copy-btn" onclick="copyText(this)">Copy</button>
                    </div>
                </div>
                <button type="button" class="copy-all-btn" onclick="copyAllDetails()"> 📋 Copy All Bank Details </button>
            </div>
            <!-- Payment Slip Form -->
            <form action="{{ url('/uae-payment/submit') }}" method="POST" enctype="multipart/form-data" id="paymentForm" autocomplete="off">
                @csrf
                {{-- <input type="hidden" name="ref_id" value="{{ $tnx->reference_id }}" />
                <input type="hidden" name="tnx_id" value="{{ $tnx->id }}" /> --}}
                <!-- Add CSRF token here if using Laravel -->
                <div class="upload-section">
                    <h5 class="fw-bold mb-2">Upload Payment Receipt</h5>
                    <p class="text-muted small"> After completing your payment, upload your transaction screenshot or payment receipt. </p>
                    <label for="payment_slip" class="upload-box w-100">
                        <div class="upload-icon">☁️</div>
                        <p>Click to upload payment slip</p>
                        <small>PNG, JPG, JPEG or PDF (Max 5 MB)</small>
                        <input type="file" name="payment_slip" id="payment_slip" class="d-none" accept=".png,.jpg,.jpeg,.pdf" required>
                    </label>
                    <div id="fileName" class="small text-success mt-2"></div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold"> Transaction ID (Optional) </label>
                        <input type="text" name="payment_id" class="form-control" placeholder="Enter transaction reference number">
                    </div>
                    <button type="submit" class="submit-btn"> Submit Payment Details → </button>
                    <div class="security-note"> 🔒 Your payment receipt will be submitted securely. </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Universal copy function with fallback
      function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
          return navigator.clipboard.writeText(text);
        }
        return new Promise((resolve, reject) => {
          const textarea = document.createElement('textarea');
          textarea.value = text;
          textarea.style.position = 'fixed';
          textarea.style.left = '-9999px';
          textarea.style.top = '0';
          document.body.appendChild(textarea);
          textarea.focus();
          textarea.select();
          try {
            const success = document.execCommand('copy');
            document.body.removeChild(textarea);
            success ? resolve() : reject();
          } catch (error) {
            document.body.removeChild(textarea);
            reject(error);
          }
        });
      }
      // Copy individual field
      function copyText(button) {
        const value = button.parentElement.querySelector('.copy-value').innerText;
        copyToClipboard(value).then(() => {
          button.innerText = 'Copied!';
          setTimeout(() => {
            button.innerText = 'Copy';
          }, 1500);
        }).catch(() => {
          alert('Unable to copy. Please copy manually.');
        });
      }
      // Copy all bank details
      function copyAllDetails() {
        const values = document.querySelectorAll('.copy-value');
        const labels = ['Bank Name', "Account Holder's Name", 'Account Number', 'IBAN'];
        let text = '';
        values.forEach((item, index) => {
          text += labels[index] + ': ' + item.innerText + '\n';
        });
        copyToClipboard(text).then(() => {
          const btn = document.querySelector('.copy-all-btn');
          btn.innerText = '✓ All Details Copied!';
          setTimeout(() => {
            btn.innerText = '📋 Copy All Bank Details';
          }, 2000);
        }).catch(() => {
          alert('Unable to copy. Please try again.');
        });
      }
      // File upload preview
      document.getElementById('payment_slip').addEventListener('change', function() {
        const file = this.files[0];
        const fileName = document.getElementById('fileName');
        if (!file) {
          fileName.innerText = '';
          return;
        }
        if (file.size > 5 * 1024 * 1024) {
          alert('Maximum file size is 5 MB.');
          this.value = '';
          fileName.innerText = '';
          return;
        }
        fileName.innerText = '✓ Selected: ' + file.name;
      });
    </script>
</body>

</html>