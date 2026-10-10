<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

/**
 * WP-32 — Profile Controller
 *
 * Extracted from routes/web.php closure:
 *   - GET /profile → ProfileController@index (S003)
 */
class ProfileController extends Controller
{
    public function index()
    {
        return view('profile-premium');
    }
}
