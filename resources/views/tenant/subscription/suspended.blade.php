<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Suspended — MediBook</title>
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
            background: #FCEBEB;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #A32D2D;
            margin: 0 auto 1.25rem;
        }
        h1 { font-size: 20px; font-weight: 600; margin-bottom: 8px; }
        p  { font-size: 14px; color: #6b7280; line-height: 1.6; margin-bottom: 1.5rem; }
        .meta { margin-top: 1.25rem; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon"><i class="ti ti-ban"></i></div>
        <h1>Account Suspended</h1>
        <p>
            Your clinic account <strong>{{ tenant('clinic_name') }}</strong>
            has been suspended by the administrator.
            Please contact support to resolve this issue.
        </p>
        <p class="meta">
            Contact support at
            <a href="mailto:support@medibook.com.bd" style="color:#185FA5;">
                support@medibook.com.bd
            </a>
        </p>
    </div>
</body>
</html>