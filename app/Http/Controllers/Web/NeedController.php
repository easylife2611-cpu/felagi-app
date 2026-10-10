<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

/**
 * WP-32 — Need Controller
 *
 * Extracted from routes/web.php closures (12 routes):
 *   - GET /needs/new                       → create
 *   - GET /needs/new/public-preview        → preview
 *   - GET /needs/{id}                      → show
 *   - GET /needs/{id}/created              → created
 *   - GET /needs/{id}/offers/new           → submitOffer
 *   - GET /needs/{id}/offers               → receivedOffers
 *   - GET /needs/{id}/compare              → compare
 *   - GET /needs/{id}/boost                → boost
 *   - GET /needs/{id}/rating               → rating
 *   - GET /needs/{id}/comparisons          → comparisons
 *   - GET /needs/{id}/publications         → publications
 *   - GET /needs/{id}/offers/unlock        → unlock
 *
 * Behavior preserved — view names unchanged.
 */
class NeedController extends Controller
{
    public function create()
    {
        return view('create-need-premium');
    }

    public function preview()
    {
        return view('need-preview-premium');
    }

    public function show(string $id)
    {
        return view('show-need-premium');
    }

    public function created(string $id)
    {
        return view('need-created-premium');
    }

    public function submitOffer(string $id)
    {
        return view('submit-offer-premium');
    }

    public function receivedOffers(string $id)
    {
        return view('received-offers-premium');
    }

    public function compare(string $id)
    {
        return view('compare-offers-premium');
    }

    public function boost(string $id)
    {
        return view('boost-need-premium');
    }

    public function rating(string $id)
    {
        return view('rate-participant-premium');
    }

    public function comparisons(string $id)
    {
        return view('comparison-history-premium');
    }

    public function publications(string $id)
    {
        return view('telegram-publications-premium');
    }

    public function unlock(string $id)
    {
        return view('offer-unlock-premium');
    }
}
