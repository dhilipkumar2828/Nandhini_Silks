<!DOCTYPE html>
<html>
<head>
    <title>New Inquiry Received</title>
</head>
<body>
    <div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #f0f0f0; border-radius: 8px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="{{ isset($message) ? $message->embed(public_path('images/nandhini-logo.png')) : asset('images/nandhini-logo.png') }}" alt="Nandhini Silks" style="height: 60px; width: auto; background: white; padding: 5px; border-radius: 8px;">
        </div>
        
        <h1 style="color: #A91B43; font-size: 22px; margin-bottom: 20px;">New Website Inquiry</h1>
        
        <div style="background-color: #f9f9f9; padding: 20px; border-radius: 8px;">
            <p style="margin: 0 0 10px; color: #777; font-size: 12px; text-transform: uppercase; font-weight: bold;">Sender Details:</p>
            <p style="margin: 0; color: #333; font-size: 16px;"><strong>Name:</strong> {{ $inquiry->name }}</p>
            <p style="margin: 5px 0 0; color: #333; font-size: 16px;"><strong>Email:</strong> {{ $inquiry->email }}</p>
        </div>

        <div style="margin: 25px 0;">
            <p style="margin: 0 0 10px; color: #777; font-size: 12px; text-transform: uppercase; font-weight: bold;">Message Content:</p>
            <p style="margin: 0; color: #444; line-height: 1.6; background: #fff; border-left: 4px solid #A91B43; padding: 15px; font-style: italic;">
                "{{ $inquiry->message }}"
            </p>
        </div>
        
        <div style="text-align: center; margin-top: 30px; border-top: 1px dashed #eee; padding-top: 25px;">
            <a href="{{ route('admin.inquiries.show', $inquiry->id) }}" style="background-color: #A91B43; color: #ffffff; padding: 12px 25px; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 14px; display: inline-block;">
                View Details in Admin Panel
            </a>
        </div>
        <div style="padding-top: 20px; text-align: center; color: #bbb; font-size: 11px;">
            &copy; {{ date('Y') }} Nandhini Silks. Arani, Tamil Nadu.
            <span style="display:none !important; font-size:0; line-height:0;">{{ microtime() }}</span>
        </div>
    </div>
</body>
</html>
