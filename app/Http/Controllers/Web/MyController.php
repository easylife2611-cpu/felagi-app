<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

/**
 * WP-32 — My (User's own) Controller
 *
 * Extracted from routes/web.php closures:
 *   - GET /my/needs  → MyController@needs  (S009)
 *   - GET /my/offers → MyController@offers (S013)
 */
class MyController extends Controller
{
    public function needs()
    {
        return view('my-needs-premium');
    }

    public function offers()
    {
        return view('my-offers-premium');
    }
}
