<?php

namespace App\Http\Controllers;

use App\Models\FreelancerPortfolioItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FreelancerPortfolioController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $freelancer = $request->user();

        abort_unless($freelancer && $freelancer->isFreelancer(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $imagePath = $request->file('image')?->store('freelancer-portfolio/images', 'public');
        $pdfPath = $request->file('pdf')?->store('freelancer-portfolio/files', 'public');

        $freelancer->freelancerPortfolioItems()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'project_url' => $validated['project_url'] ?? null,
            'image_path' => $imagePath,
            'pdf_path' => $pdfPath,
            'sort_order' => $freelancer->freelancerPortfolioItems()->count() + 1,
        ]);

        return back()->with('success', 'Проектът е добавен към портфолиото.');
    }

    public function destroy(Request $request, FreelancerPortfolioItem $portfolioItem): RedirectResponse
    {
        $freelancer = $request->user();

        abort_unless($freelancer && $freelancer->isFreelancer(), 403);
        abort_unless((int) $portfolioItem->freelancer_id === (int) $freelancer->id, 403);

        foreach ([$portfolioItem->image_path, $portfolioItem->pdf_path] as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }

        $portfolioItem->delete();

        return back()->with('success', 'Проектът е премахнат от портфолиото.');
    }
}
