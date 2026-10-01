<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $locale === 'am' ? 'የFelagi መግቢያ ኮድ' : 'Felagi Login Code' }}</title>
</head>
<body style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    @if($locale === 'am')
        <h1 style="color: #333;">ሰላም!</h1>
        <p>የFelagi መግቢያ ኮድዎ፦</p>
        <div style="font-size: 32px; font-weight: bold; letter-spacing: 4px; padding: 16px; background: #f5f5f5; text-align: center; border-radius: 8px; margin: 20px 0;">
            {{ $code }}
        </div>
        <p>ይህ ኮድ ለ10 ደቂቃ ብቻ ይሠራል።</p>
        <p>ኮዱን ለማንም አያጋሩ።</p>
        <p>አመሰግናለሁ፣<br>የFelagi ቡድን</p>
    @else
        <h1 style="color: #333;">Hello!</h1>
        <p>Your Felagi login code is:</p>
        <div style="font-size: 32px; font-weight: bold; letter-spacing: 4px; padding: 16px; background: #f5f5f5; text-align: center; border-radius: 8px; margin: 20px 0;">
            {{ $code }}
        </div>
        <p>This code is valid for 10 minutes.</p>
        <p>Never share this code with anyone.</p>
        <p>Thanks,<br>The Felagi Team</p>
    @endif
</body>
</html>
