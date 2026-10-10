<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

/**
 * WP-32 — Support Controller
 *
 * Extracted from routes/web.php closure:
 *   - GET /support/report → SupportController@report (S021)
 */
class SupportController extends Controller
{
    public function report()
    {
        return view('report-support-premium');
    }
}
