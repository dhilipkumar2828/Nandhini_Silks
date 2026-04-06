<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Nandhini Silks</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #ffffff; color: #333333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; border: 1px solid #eeeeee; padding: 30px; border-radius: 8px; }
        .header { text-align: center; border-bottom: 2px solid #a91b43; padding-bottom: 20px; margin-bottom: 20px; }
        .content { font-size: 16px; line-height: 1.5; }
        .otp-box { background-color: #f7f7f7; border: 1px solid #dddddd; padding: 20px; text-align: center; margin: 20px 0; border-radius: 4px; }
        .otp-code { font-size: 32px; font-weight: bold; color: #a91b43; letter-spacing: 5px; margin: 0; }
        .footer { font-size: 12px; color: #777777; text-align: center; margin-top: 30px; border-top: 1px solid #eeeeee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ isset($message) ? $message->embed(public_path('images/nandhini-logo.png')) : asset('images/nandhini-logo.png') }}" alt="Nandhini Silks" style="max-height: 60px; width: auto;">
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>Welcome to Nandhini Silks center! Please use the following one-time password (OTP) to verify your email address:</p>
            
            <div class="otp-box">
                <p class="otp-code">{{ $otp }}</p>
            </div>

            <p>This code will expire in 10 minutes. If you did not request this, please ignore this email.</p>
            
            <p>Regards,<br>Team Nandhini Silks</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Nandhini Silks. Arani, Tamil Nadu.
        </div>
    </div>
</body>
</html>
