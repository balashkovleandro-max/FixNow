<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isBusiness(), 404);
        abort_unless($user->isPubliclyVisible(), 404);

        $validated = $request->validate([
            'reviewer_name' => ['required', 'string', 'max:120'],
            'reviewer_email' => ['nullable', 'email', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1500'],
        ], [
            'reviewer_name.required' => 'Моля, въведете име.',
            'reviewer_name.max' => 'Името трябва да бъде до 120 символа.',
            'reviewer_email.email' => 'Моля, въведете валиден имейл адрес.',
            'rating.required' => 'Моля, изберете оценка.',
            'rating.integer' => 'Оценката трябва да бъде между 1 и 5.',
            'rating.min' => 'Оценката трябва да бъде между 1 и 5.',
            'rating.max' => 'Оценката трябва да бъде между 1 и 5.',
            'comment.max' => 'Отзивът трябва да бъде до 1500 символа.',
        ]);

        Review::create([
            'business_id' => $user->id,
            'reviewer_name' => $validated['reviewer_name'],
            'reviewer_email' => $validated['reviewer_email'] ?? null,
            'rating' => (int) $validated['rating'],
            'comment' => trim((string) ($validated['comment'] ?? '')),
            'status' => Review::STATUS_PENDING,
        ]);

        return redirect()
            ->route('businesses.show', $user)
            ->with('review_success', 'Благодарим ви. Отзивът чака одобрение от администратор.');
    }
}
