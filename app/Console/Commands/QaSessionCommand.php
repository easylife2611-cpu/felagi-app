<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QaSessionCommand extends Command
{
    protected $signature = 'qa:session {user=So}';
    protected $description = 'Create QA test session (Laravel EncryptCookies parity)';

    public function handle(): int
    {
        $userName = $this->argument('user');
        $user = User::where('full_name', $userName)->first();

        if (!$user) {
            $this->error("User not found: {$userName}");
            return 1;
        }

        $sessionId = Str::random(40);
        $guardKey = 'login_web_' . sha1('Illuminate\Auth\SessionGuard');

        // 5-key payload
        $data = [
            '_token'            => Str::random(40),
            '_previous'         => ['url' => 'https://zagcreativity.com/'],
            '_flash'            => ['old' => [], 'new' => []],
            $guardKey           => $user->id,
            'password_hash_web' => sha1((string) $user->getAuthPassword()),
        ];

        $payload = base64_encode(serialize($data));

        DB::table('sessions')->insert([
            'id'            => $sessionId,
            'user_id'       => $user->id,
            'ip_address'    => '127.0.0.1',
            'user_agent'    => 'QA-Deep-Test',
            'payload'       => $payload,
            'last_activity' => time(),
        ]);

        // EncryptCookies parity: prefix + value, then encrypt(serialize=true)
        $cookieName = config('session.cookie');   // 'felagi_session'
        $key = app('encrypter')->getKey();  // decoded 32-byte key (NOT raw config)
        $prefix = CookieValuePrefix::create($cookieName, $key);
        $cookieValue = Crypt::encryptString($prefix . $sessionId);

        $this->info('SESSION_ID=' . $sessionId);
        $this->info('COOKIE_VALUE=' . $cookieValue);
        $this->info('USER=' . $user->full_name);
        return 0;
    }
}
