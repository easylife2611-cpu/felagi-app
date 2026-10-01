<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $locale === 'am' ? 'የይለፍ ቃል መቀየሪያ' : 'Password Reset' }}</title>
</head>
<body style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; color: #333;">
    @if($locale === 'am')
        <h1 style="color: #003366;">ሰላም {{ $user->name ?? $user->email }}!</h1>
        <p>የFelagi የይለፍ ቃልዎን ለመቀየር ከታች ያለውን ቁልፍ ይጫኑ፦</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" style="background: #003366; color: #fff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: 600;">የይለፍ ቃል ቀይር</a>
        </div>
        <p style="font-size: 13px; color: #666;">ይህ ማስፈንጠሪያ ለ60 ደቂቃ ብቻ ይሠራል።</p>
        <p style="font-size: 13px; color: #666;">ይህን ጥያቄ ካልላኩ ይህን ኢሜል ችላ ይበሉት።</p>
        <p style="margin-top: 30px;">አመሰግናለሁ፣<br>የFelagi ቡድን</p>
    @else
        <h1 style="color: #003366;">Hello {{ $user->name ?? $user->email }}!</h1>
        <p>To reset your Felagi password, click the button below:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" style="background: #003366; color: #fff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: 600;">Reset Password</a>
        </div>
        <p style="font-size: 13px; color: #666;">This reset link is valid for 60 minutes.</p>
        <p style="font-size: 13px; color: #666;">If you did not request this, you can safely ignore this email.</p>
        <p style="margin-top: 30px;">Thanks,<br>The Felagi Team</p>
    @endif
</body>
</html>
