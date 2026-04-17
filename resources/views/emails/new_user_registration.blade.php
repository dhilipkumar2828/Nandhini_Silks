<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #334155; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: 800; color: #a91b43; }
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; }
        .btn { display: inline-block; background: #a91b43; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ isset($message) ? $message->embed(public_path('images/nandhini-logo.png')) : asset('images/nandhini-logo.png') }}" alt="Nandhini Silks Logo" style="height: 60px; width: auto;">
    </div>
    
    <div class="card">
        @if($isAdmin)
            <h2>New Registration Alert</h2>
            <p>Admin, a new customer has joined Nandhini Silks.</p>
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone:</strong> {{ $user->phone }}</p>
            <p><strong>Date:</strong> {{ now()->format('d M Y, h:i A') }}</p>
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn">View Customer Profile</a>
        @else
            <h2>Welcome to the Family!</h2>
            <p>Hi {{ $user->name }},</p>
            <p>Thank you for registering with <strong>Nandhini Silks</strong>. We're excited to have you with us!</p>
            <p>You can now explore our premium collections, track your orders, and save your favorites to your wishlist.</p>
            <p>Join the pure silk experience today!</p>
            <a href="{{ route('home') }}" class="btn">Start Shopping</a>
        @endif
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Nandhini Silks. All rights reserved.<br>
        Traditional Elegance, Modern Style.
    </div>
</body>
</html>
