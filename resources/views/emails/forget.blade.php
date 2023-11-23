<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forget Password</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h3 {
            color: #007bff;
        }

        p {
            margin-bottom: 20px;
        }

        .otp-code {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            background-color: #e2f1e5;
            padding: 10px;
            border-radius: 5px;
        }

        .note {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Hello!</h3>
        <p>
            This is your one-time password (OTP) for resetting your password. Please use this code to proceed:
        </p>
        <div class="otp-code">{{ $OTP }}</div>
        <p class="note">
            Note: This OTP is valid for a limited time. Do not share it with anyone for security reasons.
        </p>
    </div>
</body>
</html>

