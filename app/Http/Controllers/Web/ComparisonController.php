<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

/**
 * WP-32 — Comparison Controller
 *
 * Extracted from routes/web.php closure:
 *   - GET /comparisons/{id} → ComparisonController@show (S015)
 */
class ComparisonController extends Controller
{
    public function show(string $id)
    {
        return view('comparison-result-premium');
    }
}
