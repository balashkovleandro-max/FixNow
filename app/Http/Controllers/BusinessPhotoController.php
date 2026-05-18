<?php

namespace App\Http\Controllers;

use App\Models\BusinessPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessPhotoController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $business = $request->user();

        abort_unless($business && $business->isBusiness(), 403);

        $validated = $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'photos.required' => 'Моля, изберете поне една снимка.',
            'photos.array' => 'Моля, изберете валидни снимки.',
            'photos.*.image' => 'Всеки файл трябва да бъде изображение.',
            'photos.*.mimes' => 'Снимките трябва да бъдат JPG, PNG или WEBP.',
            'photos.*.max' => 'Всяка снимка трябва да бъде до 4MB.',
        ]);

        $incomingCount = count($validated['photos']);
        $currentCount = $business->businessPhotoCount();

        if ($currentCount + $incomingCount > $business->photoLimit()) {
            return back()
                ->withErrors([
                    'photos' => $business->effectivePlanLabel() . ' позволява до ' . $business->photoLimit() . ' снимки в бизнес галерията.',
                ]);
        }

        $nextOrder = $business->businessPhotos()->max('sort_order') ?? 0;

        foreach ($request->file('photos', []) as $photo) {
            $path = $photo->store('business-photos', 'public');

            $business->businessPhotos()->create([
                'path' => $path,
                'original_name' => $photo->getClientOriginalName(),
                'alt_text' => $business->business_name ?: $business->name,
                'sort_order' => ++$nextOrder,
            ]);
        }

        return back()->with('success', 'Снимките бяха качени успешно.');
    }

    public function destroy(Request $request, BusinessPhoto $businessPhoto): RedirectResponse
    {
        $business = $request->user();

        abort_unless($business && $business->isBusiness(), 403);
        abort_unless((int) $businessPhoto->business_id === (int) $business->id, 403);

        Storage::disk('public')->delete($businessPhoto->path);
        $businessPhoto->delete();

        return back()->with('success', 'Снимката беше изтрита.');
    }
}
