<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $locale === 'am' ? 'የግላዊነት ማሳወቂያ' : 'Privacy Notice' }}</title>
</head>
<body style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; color: #333;">
    @if($locale === 'am')
        <h1 style="color: #c0392b;">⚠️ አስፈላጊ የግላዊነት ማሳወቂያ</h1>
        <p>ውድ {{ $recipientName }}፣</p>
        <p>በFelagi ላይ የውሂብ ጥሰት ስለተከሰተ እናሳውቅዎታለን።</p>
        <h2 style="color: #555;">የጥሰቱ ዝርዝር</h2>
        <ul style="line-height: 1.8;">
            <li><strong>ርዕስ:</strong> {{ $incident->title }}</li>
            <li><strong>የተከሰተበት ጊዜ:</strong> {{ $incident->detected_at->format('Y-m-d H:i') }}</li>
            <li><strong>የተነኩ የውሂብ ዓይነቶች:</strong>
                @foreach($incident->data_categories ?? [] as $cat)
                    {{ $cat }}@if(!$loop->last), @endif
                @endforeach
            </li>
        </ul>
        <h2 style="color: #555;">ምን እያደረግን ነው</h2>
        <p>የጥሰቱን ምንጭ ለይተን አግደናል። ተጨማሪ ጥናት በመካሄድ ላይ ነው።</p>
        <h2 style="color: #555;">ምን ማድረግ ይችላሉ</h2>
        <ul style="line-height: 1.8;">
            <li>የመግቢያ የይለፍ ቃልዎን ይቀይሩ (ካለ)</li>
            <li>የሚጠረጥሩ ኢሜሎችን ይጠንቀቁ</li>
            <li>ተጨማሪ መረጃ ለማግኘት dpo@felagi.et ያግኙ</li>
        </ul>
        <p>ስለ ትዕግስትዎ እናመሰግናለን።</p>
        <p>አመሰግናለሁ፣<br>የFelagi የግላዊነት ቡድን</p>
    @else
        <h1 style="color: #c0392b;">⚠️ Important Privacy Notice</h1>
        <p>Dear {{ $recipientName }},</p>
        <p>We are writing to inform you of a data breach at Felagi.</p>
        <h2 style="color: #555;">Incident Details</h2>
        <ul style="line-height: 1.8;">
            <li><strong>Title:</strong> {{ $incident->title }}</li>
            <li><strong>Detected:</strong> {{ $incident->detected_at->format('Y-m-d H:i') }}</li>
            <li><strong>Data categories affected:</strong>
                @foreach($incident->data_categories ?? [] as $cat)
                    {{ $cat }}@if(!$loop->last), @endif
                @endforeach
            </li>
        </ul>
        <h2 style="color: #555;">What we are doing</h2>
        <p>We have identified and contained the source. Further investigation is ongoing.</p>
        <h2 style="color: #555;">What you can do</h2>
        <ul style="line-height: 1.8;">
            <li>Change your login credentials (if applicable)</li>
            <li>Be cautious of suspicious emails</li>
            <li>Contact dpo@felagi.et for more information</li>
        </ul>
        <p>Thank you for your patience.</p>
        <p>Regards,<br>The Felagi Privacy Team</p>
    @endif
</body>
</html>
