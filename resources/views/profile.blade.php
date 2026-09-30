<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('profile') }} — {{ __('brand') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
            background: #F4F6F8; color: #192431; line-height: 1.6; min-height: 100vh;
        }
        header {
            background: #003366; color: white; padding: 16px 24px;
            display: flex; justify-content: space-between; align-items: center;
        }
        header h1 { font-size: 18px; font-weight: 600; }
        .logout-btn {
            background: transparent; color: white; border: 1px solid rgba(255,255,255,0.4);
            padding: 8px 16px; border-radius: 6px; cursor: pointer;
            font-family: inherit; font-size: 14px;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.1); }
        main { max-width: 720px; margin: 32px auto; padding: 0 20px; }
        .card {
            background: white; border-radius: 12px; padding: 32px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .card h2 { font-size: 22px; margin-bottom: 8px; color: #003366; }
        .card .subtitle { color: #586675; font-size: 14px; margin-bottom: 24px; }
        .form-group { margin-bottom: 20px; }
        label {
            display: block; font-size: 14px; font-weight: 600;
            margin-bottom: 6px; color: #192431;
        }
        label .required { color: #c62828; }
        input[type="text"], input[type="tel"] {
            width: 100%; padding: 12px 14px; border: 1px solid #d0d7de;
            border-radius: 6px; font-family: inherit; font-size: 15px;
            transition: border 0.2s;
        }
        input:focus { outline: none; border-color: #003366; box-shadow: 0 0 0 3px rgba(0,51,102,0.1); }
        .field-error { color: #c62828; font-size: 13px; margin-top: 4px; display: none; }
        .field-error.visible { display: block; }
        .photo-preview { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 12px; }
        .actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; border: none; font-family: inherit; transition: background 0.2s; text-decoration: none; display: inline-block; }
        .btn-primary { background: #003366; color: white; }
        .btn-primary:hover:not(:disabled) { background: #002a52; }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-secondary { background: #f0f0f0; color: #192431; }
        .btn-secondary:hover { background: #e0e0e0; }
        .status { margin-top: 16px; padding: 12px 16px; border-radius: 6px; font-size: 14px; display: none; }
        .status.visible { display: block; }
        .status.success { background: #e8f5e9; color: #1b5e20; }
        .status.error { background: #ffebee; color: #b71c1c; }
        .status.info { background: #e3f2fd; color: #0d47a1; }
        .loading { text-align: center; padding: 40px; color: #586675; }
        .spinner {
            display: inline-block; width: 24px; height: 24px;
            border: 3px solid #e0e0e0; border-top-color: #003366;
            border-radius: 50%; animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        #profile-form { display: none; }
        #profile-form.visible { display: block; }
    </style>
</head>
<body>
    <header>
        <h1>{{ __('brand') }}</h1>
        <button class="logout-btn" onclick="felagiLogout()">{{ __('logout') }}</button>
    </header>

    <main>
        <div class="card">
            <h2>{{ __('profile') }}</h2>
            <p class="subtitle">{{ __('profileSubtitle') }}</p>

            <div id="loading" class="loading">
                <div class="spinner"></div>
                <p style="margin-top: 12px;">{{ __('loading') }}...</p>
            </div>

            <form id="profile-form">
                <div class="form-group" style="text-align: center;">
                    <img id="photo-preview" class="photo-preview" src="" alt="" style="display: none;">
                </div>

                <div class="form-group">
                    <label for="full_name">{{ __('fullName') }} <span class="required">*</span></label>
                    <input type="text" id="full_name" name="full_name" maxlength="150" required>
                    <div class="field-error" id="error-full_name"></div>
                </div>

                <div class="form-group">
                    <label for="phone_number">{{ __('phoneNumber') }}</label>
                    <input type="tel" id="phone_number" name="phone_number" maxlength="30">
                    <div class="field-error" id="error-phone_number"></div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary" id="save-btn">{{ __('save') }}</button>
                    <a href="/browse" class="btn btn-secondary">{{ __('browseNeeds') }} →</a>
                </div>

                <div id="status" class="status"></div>
            </form>
        </div>
    </main>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        function getToken() { return localStorage.getItem('felagi_token'); }
        function showStatus(type, msg) {
            const el = document.getElementById('status');
            el.className = 'status visible ' + type;
            el.textContent = msg;
        }
        function hideStatus() {
            const el = document.getElementById('status');
            el.className = 'status';
            el.textContent = '';
        }

        async function loadProfile() {
            const token = getToken();
            if (!token) { window.location.href = '/'; return; }
            try {
                const res = await fetch('/api/v1/auth/me', {
                    method: 'GET', credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
                });
                if (res.status === 401) {
                    localStorage.removeItem('felagi_token');
                    localStorage.removeItem('felagi_user');
                    window.location.href = '/';
                    return;
                }
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                const user = data.data || data;
                localStorage.setItem('felagi_user', JSON.stringify(user));
                document.getElementById('full_name').value = user.full_name || '';
                document.getElementById('phone_number').value = user.phone_number || '';
                if (user.profile_photo_url) {
                    const img = document.getElementById('photo-preview');
                    img.src = user.profile_photo_url;
                    img.style.display = 'block';
                }
                document.getElementById('loading').style.display = 'none';
                document.getElementById('profile-form').classList.add('visible');
            } catch (e) {
                console.error('[FELAGI] Load profile failed:', e);
                showStatus('error', 'Failed to load profile');
                document.getElementById('loading').innerHTML = '<p style="color: #c62828;">Failed to load</p>';
            }
        }

        document.getElementById('profile-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            hideStatus();
            const saveBtn = document.getElementById('save-btn');
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';
            document.querySelectorAll('.field-error').forEach(el => {
                el.classList.remove('visible'); el.textContent = '';
            });
            const payload = { full_name: document.getElementById('full_name').value.trim() };
            const phone = document.getElementById('phone_number').value.trim();
            if (phone) payload.phone_number = phone;
            try {
                const res = await fetch('/api/v1/profile', {
                    method: 'PATCH', credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json', 'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + getToken(),
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (res.status === 422 && data.errors) {
                    Object.entries(data.errors).forEach(([field, msgs]) => {
                        const errEl = document.getElementById('error-' + field);
                        if (errEl) {
                            errEl.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                            errEl.classList.add('visible');
                        }
                    });
                    showStatus('error', 'Validation failed');
                    return;
                }
                if (!res.ok) throw new Error(data.message || 'HTTP ' + res.status);
                const user = data.data || data;
                localStorage.setItem('felagi_user', JSON.stringify(user));
                showStatus('success', 'Profile saved');
            } catch (e) {
                console.error('[FELAGI] Save failed:', e);
                showStatus('error', 'Failed to save');
            } finally {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save';
            }
        });

        window.felagiLogout = async function() {
            const token = getToken();
            if (token) {
                try {
                    await fetch('/api/v1/auth/logout', {
                        method: 'POST', credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json', 'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + token, 'X-CSRF-TOKEN': csrfToken
                        }
                    });
                } catch (e) { console.warn('Logout API failed', e); }
            }
            localStorage.removeItem('felagi_token');
            localStorage.removeItem('felagi_user');
            window.location.href = '/';
        };

        loadProfile();
    </script>
</body>
</html>
