<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Purchase Successful</title>
</head>

<body style="margin:0; padding:0; background-color:#f5f7fb; font-family:Arial, Helvetica, sans-serif; color:#333333;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f5f7fb; padding:30px 15px;">
        <tr>
            <td align="center">

                <!-- Main Container -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:650px; background:#ffffff; border-radius:12px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#F58634; padding:25px 30px; text-align:center;">

                            <h1 style="margin:0; color:#ffffff; font-size:26px; line-height:34px;">
                                Congratulations! 🎉
                            </h1>

                            <p style="margin:8px 0 0; color:#ffffff; font-size:15px;">
                                Your course purchase was successful
                            </p>

                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:35px 35px 25px;">

                            <p style="margin:0 0 18px; font-size:16px; line-height:26px;">
                                Hello <strong>{{ $name }}</strong>,
                            </p>

                            <p style="margin:0 0 18px; font-size:15px; line-height:25px; color:#555555;">
                                Thank you for your purchase! We are happy to confirm that your
                                course enrollment has been completed successfully.
                            </p>

                            <p style="margin:0 0 25px; font-size:15px; line-height:25px; color:#555555;">
                                You can now access your purchased course and start learning.
                                We wish you a great learning journey!
                            </p>

                            <!-- Success Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f0faf4; border:1px solid #d7efdf; border-radius:8px; margin-bottom:25px;">
                                <tr>
                                    <td style="padding:18px 20px;">

                                        <p style="margin:0 0 8px; font-size:15px; color:#218838;">
                                            <strong>✓ Payment Successful</strong>
                                        </p>

                                        <p style="margin:0; font-size:13px; color:#555555;">
                                            Your payment has been received and your course has been successfully purchased.
                                        </p>

                                    </td>
                                </tr>
                            </table>

                            <!-- Course Details -->
                            <h2 style="margin:0 0 15px; font-size:18px; color:#222222;">
                                Course Details
                            </h2>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e5e7eb; border-radius:8px; overflow:hidden; margin-bottom:25px;">

                                <tr>
                                    <td style="padding:13px 15px; background:#f8f9fb; width:40%; font-size:14px; color:#666666;">
                                        Course Name
                                    </td>
                                    <td style="padding:13px 15px; font-size:14px; color:#222222;">
                                        <strong>{{ $courseName }}</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:13px 15px; background:#f8f9fb; font-size:14px; color:#666666;">
                                        Order ID
                                    </td>
                                    <td style="padding:13px 15px; font-size:14px; color:#222222;">
                                        <strong>{{ $orderId }}</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:13px 15px; background:#f8f9fb; font-size:14px; color:#666666;">
                                        Purchase Date
                                    </td>
                                    <td style="padding:13px 15px; font-size:14px; color:#222222;">
                                        {{ $purchaseDate }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:13px 15px; background:#f8f9fb; font-size:14px; color:#666666;">
                                        Amount Paid
                                    </td>
                                    <td style="padding:13px 15px; font-size:14px; color:#222222;">
                                        <strong>₹{{ number_format($amount, 2) }}</strong>
                                    </td>
                                </tr>

                            </table>

                            <!-- Invoice Notice -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fff8f2; border:1px solid #f8dfca; border-radius:8px; margin-bottom:25px;">
                                <tr>
                                    <td style="padding:18px 20px;">

                                        <p style="margin:0 0 7px; font-size:15px; color:#333333;">
                                            <strong>📄 Your Invoice is Attached</strong>
                                        </p>

                                        <p style="margin:0; font-size:13px; line-height:21px; color:#666666;">
                                            We have attached your purchase invoice to this email for your records.
                                            Please keep it safely for future reference.
                                        </p>

                                    </td>
                                </tr>
                            </table>

                            <!-- CTA -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:25px;">
                                <tr>
                                    <td align="center">

                                        <a href="{{ $courseUrl }}" style="display:inline-block;
                                                  background:#F58634;
                                                  color:#ffffff;
                                                  text-decoration:none;
                                                  font-size:15px;
                                                  font-weight:bold;
                                                  padding:13px 28px;
                                                  border-radius:6px;">
                                            Start Learning
                                        </a>

                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 10px; font-size:14px; line-height:23px; color:#555555;">
                                If you have any questions or need assistance with your course,
                                please feel free to contact our support team.
                            </p>

                            <p style="margin:0; font-size:14px; line-height:23px; color:#555555;">
                                Thank you for choosing us and happy learning! 🎓
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f8f9fb; padding:22px 30px; text-align:center; border-top:1px solid #eeeeee;">

                            <p style="margin:0 0 8px; font-size:13px; color:#555555;">
                                <strong>{{ config('app.name') }}</strong>
                            </p>

                            <p style="margin:0 0 8px; font-size:12px; color:#888888;">
                                This is an automated email. Please do not reply directly to this email.
                            </p>

                            <p style="margin:0; font-size:12px; color:#999999;">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>