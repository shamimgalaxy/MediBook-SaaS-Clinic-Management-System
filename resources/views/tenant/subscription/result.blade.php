<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscription {{ $status === 'success' ? 'Successful' : 'Failed' }}</title>
    <style>
        body { font-family: sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; border-radius: 12px; padding: 40px; max-width: 420px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        h1 { font-size: 20px; margin-bottom: 12px; color: {{ $status === 'success' ? '#16a34a' : '#dc2626' }}; }
        p { color: #475569; margin-bottom: 24px; }
        a { display: inline-block; background: #2563eb; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $status === 'success' ? '✓ Payment Successful' : '✕ Payment Issue' }}</h1>
        <p>{{ $message }}</p>
        <a href="{{ route('subscription.index') }}">Go to Subscription Page</a>
    </div>
</body>
</html>