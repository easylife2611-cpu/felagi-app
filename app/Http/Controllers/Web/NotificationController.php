<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

/**
 * WP-32 — Notification Controller
 *
 * Extracted from routes/web.php closure:
 *   - GET /notifications → NotificationController@index (S018)
 */
class NotificationController extends Controller
{
    public function index()
    {
        return view('notifications-premium');
    }
}
