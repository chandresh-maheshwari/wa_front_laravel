<!DOCTYPE html>
<html>
<head>
    <title>OTP Code</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .header {
            background-color: #34495e;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .content {
            margin: 25px 0;
            text-align: center;
        }
        .otp {
            font-size: 30px;
            font-weight: bold;
            color: #3498db;
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            display: inline-block;
            margin: 20px 0;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
        }
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
            .header, .content, .footer {
                padding: 15px;
            }
            .otp {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your OTP Code</h1>
        </div>
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            <p>Your OTP code is:</p>
            <p class="otp">{{ $otp }}</p>
            <p>Please use this code to password reset process. The code is valid for 10 minutes.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} .</p>
        </div>
    </div>
</body>
</html>