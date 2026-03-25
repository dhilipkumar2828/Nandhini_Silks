<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New User Registration - Nandhini Silks</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px; }
        .card { max-width: 500px; margin: 30px auto; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.05); }
        .head { padding: 40px 30px; text-align: center; border-bottom: 2px solid #f9fafb; background: linear-gradient(135deg, #a91b43 0%, #940437 100%); color: #fff; }
        .head h1 { margin: 0; font-size: 26px; font-weight: 800; letter-spacing: -1px; }
        .body { padding: 40px 35px; text-align: center; }
        .user-info { margin: 25px 0; padding: 20px; background: #fdf2f8; border-radius: 16px; border: 1px solid #fce7f3; }
        .user-info h3 { margin: 0; color: #a91b43; font-size: 20px; font-weight: 800; }
        .user-meta { margin-top: 10px; color: #666; font-size: 14px; }
        .button { display: inline-block; padding: 16px 45px; background-color: #a91b43; color: #ffffff; text-decoration: none; border-radius: 14px; font-weight: 800; font-size: 15px; margin-top: 25px; }
        .foot { padding: 25px; font-size: 12px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <div class="head">
            <img src="{{ url('images/nandhini-logo.png') }}" alt="Nandhini Silks" style="max-height: 50px; width: auto; background: white; padding: 10px; border-radius: 12px; margin-bottom: 20px;">
            <h1 style="display: block;">New Customer!</h1>
            <p style="margin: 8px 0 0; opacity: 0.8; font-weight: 600;">Someone just joined our family</p>
        </div>
        <div class="body">
            <div style="background: #fdf2f8; width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="" style="width: 40px; filter: grayscale(1) brightness(0.9);">
            </div>
            
            <div class="user-info">
                <h3>{{ $user->name }}</h3>
                <div class="user-meta">
                    <p style="margin: 5px 0;"><strong>Email:</strong> {{ $user->email }}</p>
                    <p style="margin: 0;"><strong>Customer ID:</strong> #{{ $user->id }}</p>
                </div>
            </div>

            <p style="color: #6b7280; font-size: 14px; line-height: 1.6;">A new account has been created successfully. Welcome them to the Nandhini Silks experience!</p>
            
            <a href="{{ url('/admin/users/'.$user->id) }}" class="button">View Customer Profile</a>
        </div>
        <div class="foot">
            Automated Alert System &bull; {{ date('H:i, d M Y') }}
        </div>
    </div>
</body>
</html>
