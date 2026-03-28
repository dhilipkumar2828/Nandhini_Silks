<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Nandhini Silks</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); overflow: hidden; }
        .header { background: linear-gradient(90deg, #a91b43 0%, #fbb624 100%); padding: 40px; text-align: center; }
        .logo { max-height: 80px; width: auto; background: #fff; padding: 12px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .content { padding: 45px 35px; text-align: center; }
        .otp-box { background-color: #fdf2f8; border: 2px dashed #a91b43; padding: 25px; border-radius: 15px; margin: 30px auto; width: fit-content; }
        .otp-code { font-size: 38px; font-weight: 900; color: #a91b43; letter-spacing: 12px; margin: 0; }
        .footer { padding: 30px; text-align: center; color: #888; font-size: 13px; border-top: 1px solid #eee; background-color: #fafafa; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ asset('images/nandhini-logo.png') }}" class="logo" alt="Nandhini Silks">
            <h1 style="color: #fff; margin: 0; font-size: 24px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px;text-align: center !important;">Verification Code</h1>
        </div>
        <div class="content" style="padding:30px;">
            <h2 style="color: #111; font-weight: 800; margin-top: 0;">Confirm Your Identity</h2>
            <p style="color: #666; font-size: 16px; line-height: 1.6;">Welcome to Nandhini Silks! To complete your registration and secure your account, please use the following one-time password (OTP):</p>
            
            <div class="otp-box">
                <p class="otp-code">{{ $otp }}</p>
            </div>

            <p style="color: #999; font-size: 14px;">This code will expire in 10 minutes. <br>Please do not share this code with anyone.</p>
            
            <p style="margin-top: 40px; font-weight: 600; color: #444;">Happy Shopping!</p>
        </div>
        <div class="footer text-center">
            &copy; {{ date('Y') }} Nandhini Silks. Arani, Tamil Nadu. <br>
        </div>
    </div>
</body>
</html>
