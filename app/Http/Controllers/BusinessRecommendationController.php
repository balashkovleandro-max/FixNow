<?php

namespace App\Http\Controllers;

use App\Models\BusinessRecommendation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BusinessRecommendationController extends Controller
{
    public function store(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isBusiness(), 404);
        abort_unless($user->isPubliclyVisible(), 404);

        $viewer = $request->user();
        $ipHash = $viewer ? null : BusinessRecommendation::ipHash($request->ip());

        $query = BusinessRecommendation::query()
            ->where('business_id', $user->id);

        if ($viewer) {
            $query->where('user_id', $viewer->id);
        } else {
            $query->where('ip_hash', $ipHash);
        }

        if (!$query->exists()) {
            BusinessRecommendation::create([
                'business_id' => $user->id,
                'user_id' => $viewer?->id,
                'ip_hash' => $ipHash,
            ]);
        }

        return redirect()
            ->route('businesses.show', $user)
            ->with('recommendation_success', 'Благодарим ви. Препоръката беше отчетена.');
    }
}
