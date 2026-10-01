<?php

namespace App\Services\Privacy;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Data Subject Rights — Proclamation 1321/2024, Art. 34-39
 */
class DataRightsService
{
    // Art. 34 — Access
    public function show(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'username'   => $user->username,
            'email'      => $user->email,
            'created_at' => $user->created_at,
        ];
    }

    // Art. 35 — Rectification
    public function rectify(User $user, array $data): array
    {
        $user->fill($data);
        $user->save();
        return $this->show($user);
    }

    // Art. 36 — Erasure request (7-day grace)
    public function requestErasure(User $user, Request $request): array
    {
        $id = DB::table('data_requests')->insertGetId([
            'user_id'    => $user->id,
            'type'       => 'erasure',
            'status'     => 'pending',
            'payload'    => json_encode(['grace_days' => 7]),
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['request_id' => $id, 'status' => 'pending', 'grace_days' => 7];
    }

    // Art. 37 — Restriction
    public function restrict(User $user, array $data, Request $request): array
    {
        $id = DB::table('data_requests')->insertGetId([
            'user_id'    => $user->id,
            'type'       => 'restriction',
            'status'     => 'pending',
            'payload'    => json_encode($data),
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['request_id' => $id, 'status' => 'pending', 'scope' => $data['scope']];
    }

    // Art. 39 — Objection
    public function object(User $user, array $data, Request $request): array
    {
        $id = DB::table('data_requests')->insertGetId([
            'user_id'    => $user->id,
            'type'       => 'objection',
            'status'     => 'pending',
            'payload'    => json_encode($data),
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['request_id' => $id, 'status' => 'pending', 'purpose' => $data['purpose']];
    }
}
