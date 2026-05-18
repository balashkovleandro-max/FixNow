<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class AdminReviewController extends Controller
{
    public function approve(Review $review): RedirectResponse
    {
        $this->authorizeAdmin();

        $review->approve();

        return back()->with('success', 'Отзивът беше одобрен.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $this->authorizeAdmin();

        $review->reject();

        return back()->with('success', 'Отзивът беше отхвърлен.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
    }
}
