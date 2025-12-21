<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magic Login</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .welcome-text {
            font-size: 18px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            margin: 20px 0;
        }
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.6);
        }
        .security-info {
            background: #f8f9ff;
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
            border-left: 4px solid #667eea;
        }
        .security-info h3 {
            color: #667eea;
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: 600;
        }
        .security-info p {
            color: #666;
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }
        .footer {
            background: #f8f9ff;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer .company-name {
            color: #667eea;
            font-weight: 600;
        }
        .timer {
            display: inline-block;
            background: #ff6b6b;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin: 10px 0;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }
            .header, .content, .footer {
                padding: 25px 20px;
            }
            .header h1 {
                font-size: 24px;
            }
            .login-button {
                padding: 12px 30px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="icon">🔐</span>
            <h1>Secure Login</h1>
        </div>

        <div class="content">
            <div class="welcome-text">
                Welcome! You've requested a secure login link for your account.
            </div>

            <a href="{{ $url }}" class="login-button">
                🚀 Login Now
            </a>

            <div class="timer">
                ⏰ Expires in 15 minutes
            </div>

            <div class="security-info">
                <h3>🛡️ Security Information</h3>
                <p>
                    • This link is valid for one-time use only<br>
                    • Expires automatically after 15 minutes<br>
                    • Do not share this link with anyone else<br>
                    • If you didn't request this link, please ignore this message
                </p>
            </div>
        </div>

        <div class="footer">
            <p>
                Sent by <span class="company-name">{{ config('app.name') }}</span><br>
                This is an automated message, please do not reply.
            </p>
        </div>
    </div>
</body>
</html>
