<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notification' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-height: 60px;
            margin-bottom: 10px;
            max-width: 100%;
            height: auto;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .content p {
            margin: 0 0 15px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .btn:hover {
            background-color: #218838;
        }
        .ticket-icon {
            font-size: 48px;
            color: #007bff;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://whencefinancesystem.com/images/w/logo.jpg" alt="Whence Finance System Logo" width="200" height="60" style="max-width: 100%; height: auto; display: block; margin: 0 auto;" onerror="this.style.display='none';">
        <div class="header">
            <h1>Ticket System Notification</h1>
        </div>
        <div class="content">
            {!! $body !!}
        </div>
        <div class="footer">
            <p>This is an automated message from the Ticket System. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>