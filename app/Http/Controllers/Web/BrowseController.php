<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

/**
 * WP-32 — Browse Controller
 *
 * Extracted from routes/web.php closure:
 *   - GET /browse → BrowseController@index (S004)
 */
class BrowseController extends Controller
{
    public function index()
    {
        return view('browse-premium');
    }
}
