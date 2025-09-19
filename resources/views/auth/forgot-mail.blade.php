<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('setting.company_name') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Dear {{ $user['name'] }},</h2>

    <p>We received a request to reset your password. Below are your new login details:</p>

    <p><strong>Email:</strong> {{ $user['email'] }}</p>
    <p><strong>Temporary Password:</strong> {{ $user['password'] }}</p>

    <p>Please use this password to log in to your account. For security reasons, we strongly recommend changing your password immediately after logging in.</p>

    <p>Thank you for choosing our services. We appreciate your trust and look forward to assisting you with your future needs.</p>

    <p>If you have any questions or need further assistance, please feel free to contact our customer support team:</p>
    
    <p><strong>Email:</strong> <a href="mailto:{{ config('setting.company_email', '') }}">{{ config('setting.company_email') }}</a></p>
    <p><strong>Phone:</strong> {{ config('setting.company_contact', '') }}</p>

    <p>Best regards,</p>
    <p><strong>{{ config('setting.company_name') }} Team</strong></p>
</body>
</html>
