<?php

namespace App\Http\Controllers;

use App\Support\CategoryCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        abort_unless($user && $user->isCustomer(), 403);

        return view('dashboards.client-profile-edit', [
            'user' => $user,
            'categories' => CategoryCatalog::all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user && $user->isCustomer(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:80'],
            'city' => ['nullable', 'string', 'max:120'],
            'preferred_categories' => ['nullable', 'array'],
            'preferred_categories.*' => ['nullable', 'string', 'max:120'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'preferred_categories' => collect($validated['preferred_categories'] ?? [])->filter()->values()->all(),
        ];

        $payload = collect($payload)
            ->filter(fn ($value, $column) => Schema::hasColumn('users', $column))
            ->all();

        $user->forceFill($payload)->save();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Клиентският профил е обновен.');
    }
}
