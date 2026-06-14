<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLog;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;

class AdminReviewController extends Controller
{
    public function approve(Review $review): RedirectResponse
    {
        $this->authorizeAdmin();

        $old = $review->only(['status', 'approved_at']);
        $review->approve();
        $this->log('review.approved', $review, $old, $review->only(['status', 'approved_at']));

        return back()->with('success', 'Отзивът беше одобрен.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $this->authorizeAdmin();

        $old = $review->only(['status', 'approved_at']);
        $review->reject();
        $this->log('review.rejected', $review, $old, $review->only(['status', 'approved_at']));

        return back()->with('success', 'Отзивът беше отхвърлен.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
    }

    private function log(string $action, Review $review, array $oldValues, array $newValues): void
    {
        if (!Schema::hasTable('admin_activity_logs')) {
            return;
        }

        AdminActivityLog::query()->create([
            'admin_user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $review::class,
            'subject_id' => $review->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
