<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFavorite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FavoriteController extends Controller
{
    public function store(Request $request, User $profile): RedirectResponse
    {
        abort_unless($this->canFavorite($request->user(), $profile), 403);
        abort_unless(Schema::hasTable('user_favorites'), 500, 'Favorites are not migrated yet.');

        UserFavorite::query()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'favorite_user_id' => $profile->id,
            ],
            [
                'favorite_type' => $profile->role,
            ]
        );

        return back()->with('success', 'Профилът е добавен в Любими.');
    }

    public function destroy(Request $request, User $profile): RedirectResponse
    {
        abort_unless($request->user(), 403);

        if (Schema::hasTable('user_favorites')) {
            UserFavorite::query()
                ->where('user_id', $request->user()->id)
                ->where('favorite_user_id', $profile->id)
                ->delete();
        }

        return back()->with('success', 'Профилът е премахнат от Любими.');
    }

    private function canFavorite(?User $user, User $profile): bool
    {
        return $user
            && (int) $user->id !== (int) $profile->id
            && in_array($profile->role, ['business', 'freelancer'], true);
    }
}
