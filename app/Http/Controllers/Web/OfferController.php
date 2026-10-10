<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

/**
 * WP-32 — Offer Controller
 *
 * Extracted from routes/web.php closures:
 *   - GET /offers/{id}          → OfferController@show     (S012)
 *   - GET /offers/{id}/messages → OfferController@messages (S017)
 */
class OfferController extends Controller
{
    public function show(string $id)
    {
        return view('offer-detail-premium');
    }

    public function messages(string $id)
    {
        return view('offer-messages-premium');
    }
}
