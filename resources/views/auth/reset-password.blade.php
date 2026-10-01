<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ app()->getLocale() === 'am' ? 'የይለፍ ቃል ቀይር' : 'Reset Password' }} — Felagi</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif; background: #F4F6F8; color: #192431; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 32px 20px; }
        .card { width: 100%; max-width: 420px; text-align: center; }
        h1 { font-size: 22px; margin-bottom: 8px; }
        p.sub { color: #586675; font-size: 14px; margin-bottom: 28px; }
        input { display: block; width: 100%; padding: 14px 16px; border: 1px solid #D1D5DB; border-radius: 10px; font-size: 15px; font-family: inherit; background: #fff; margin-bottom: 12px; }
        input:focus { outline: none; border-color: #003366; box-shadow: 0 0 0 3px rgba(0,51,102,0.1); }
        button { display: block; width: 100%; padding: 14px 16px; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; font-family: inherit; background: #003366; color: #fff; cursor: pointer; }
        button:hover { background: #002a52; }
        .status { margin-top: 16px; font-size: 14px; min-height: 20px; }
        .status.ok { color: #16A34A; }
        .status.err { color: #DC2626; }
        .back { display: block; margin-top: 24px; font-size: 13px; color: #6B7280; text-decoration: none; }
        .back:hover { color: #003366; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ app()->getLocale() === 'am' ? 'አዲስ የይለፍ ቃል' : 'New Password' }}</h1>
        <p class="sub">{{ app()->getLocale() === 'am' ? 'አዲስ የይለፍ ቃል ያስገቡ' : 'Enter your new password' }}</p>

        <form id="reset-form" method="POST" action="/reset-password">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}" />
            <input type="email" name="email" value="{{ $email }}" placeholder="your@email.com" required autocomplete="email" />
            <input type="password" name="password" placeholder="{{ app()->getLocale() === 'am' ? 'አዲስ የይለፍ ቃል' : 'New password' }}" required minlength="8" autocomplete="new-password" />
            <input type="password" name="password_confirmation" placeholder="{{ app()->getLocale() === 'am' ? 'የይለፍ ቃል ያረጋግጡ' : 'Confirm password' }}" required minlength="8" autocomplete="new-password" />
            <button type="submit">{{ app()->getLocale() === 'am' ? 'የይለፍ ቃል ቀይር' : 'Reset Password' }}</button>
        </form>

        <div class="status" id="status"></div>
        <a href="/" class="back">{{ app()->getLocale() === 'am' ? '← ወደ መግቢያ ተመለስ' : '← Back to sign in' }}</a>
    </div>

    <script>
        document.getElementById('reset-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const status = document.getElementById('status');
            status.textContent = '...';
            status.className = 'status';

            try {
                const res = await fetch('/reset-password', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': form._token.value },
                    body: new FormData(form)
                });
                const data = await res.json();
                if (!res.ok) {
                    status.textContent = data.message || 'Error';
                    status.className = 'status err';
                    return;
                }
                status.textContent = data.message || 'Password reset';
                status.className = 'status ok';
                setTimeout(() => window.location.href = '/', 1200);
            } catch (err) {
                status.textContent = 'Error. Please try again.';
                status.className = 'status err';
            }
        });
    </script>
</body>
</html>
