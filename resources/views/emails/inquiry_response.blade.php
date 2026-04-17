<!DOCTYPE html>
<html>
<head>
    <title>Response to Inquiry</title>
</head>
<body>
    <div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #f0f0f0; border-radius: 8px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="{{ isset($message) ? $message->embed(public_path('images/nandhini-logo.png')) : asset('images/nandhini-logo.png') }}" alt="Nandhini Silks" style="height: 60px; width: auto; background: white; padding: 5px; border-radius: 8px;">
        </div>
        
        <h1 style="color: #333; font-size: 22px; margin-bottom: 20px;">Hello {{ $inquiry->name }},</h1>
        <p style="color: #555; line-height: 1.6;">Thank you for reaching out to us. We have reviewed your inquiry and here is our response:</p>
        
        @if($inquiry->admin_note)
        <div style="background-color: #f0f7ff; padding: 15px; border-left: 4px solid #007bff; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0; font-weight: bold; color: #007bff; font-size: 12px; text-transform: uppercase;">Our Response:</p>
            <p style="margin: 10px 0 0; color: #333; line-height: 1.5;">{!! nl2br(e($inquiry->admin_note)) !!}</p>
        </div>
        @endif


        
        <p style="color: #777; font-size: 14px; margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
            Best Regards,<br>
            <strong>Nandhini Silks Team</strong>
        </p>
        <div style="padding-top: 20px; text-align: center; color: #bbb; font-size: 11px;">
            &copy; {{ date('Y') }} Nandhini Silks. Arani, Tamil Nadu.
            <span style="display:none !important; font-size:0; line-height:0;">{{ microtime() }}</span>
        </div>
    </div>
</body>
</html>
