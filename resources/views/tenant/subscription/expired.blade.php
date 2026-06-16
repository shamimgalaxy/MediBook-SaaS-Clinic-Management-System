<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Subscription Expired — MediBook</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, sans-serif;
            background: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }
        .card {
            background: #fff;
            border: 0.5px solid #e5e7eb;
            border-radius: 16px;
            padding: 2.5rem;
            max-width: 460px;
            width: 100%;
            text-align: center;
        }
        .icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #FEF3C7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #D97706;
            margin: 0 auto 1.25rem;
        }
        h1 { font-size: 20px; font-weight: 600; margin-bottom: 8px; }
        p  { font-size: 14px; color: #6b7280; line-height: 1.6; margin-bottom: 1.5rem; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #185FA5;
            color: #fff;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
        }
        .btn:hover { background: #0C447C; }
        .meta {
            margin-top: 1.25rem;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon"><i class="ti ti-clock-exclamation"></i></div>
        <h1>Subscription Expired</h1>
        <p>
            Your subscription for <strong>{{ tenant('clinic_name') }}</strong>
            has expired. Renew your plan to continue using MediBook.
        </p>
        <a href="{{ route('subscription.index') }}" class="btn">
            <i class="ti ti-credit-card"></i> Renew Subscription
        </a>
        <p class="meta">
            Need help? Contact support at
            <a href="mailto:support@medibook.com.bd" style="color:#185FA5;">
                support@medibook.com.bd
            </a>
        </p>
    </div>
</body>
</html>