<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CategoryCatalog;
use App\Support\ProfileTrust;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FreelancerDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $freelancers = collect();

        if (Schema::hasTable('users')) {
            $query = User::query()
                ->where(function ($query) {
                    $query->where('role', 'freelancer');

                    if (Schema::hasColumn('users', 'account_type')) {
                        $query->orWhere('account_type', 'freelancer');
                    }

                    if (Schema::hasColumn('users', 'profile_type')) {
                        $query->orWhere('profile_type', 'freelancer');
                    }
                })
                ->with(['services', 'freelancerPortfolioItems'])
                ->latest();

            if (Schema::hasColumn('users', 'is_suspended')) {
                $query->where(function ($query) {
                    $query->where('is_suspended', false)->orWhereNull('is_suspended');
                });
            }

            $query
                ->when($request->filled('q'), function ($query) use ($request) {
                    $term = '%' . trim((string) $request->q) . '%';

                    $query->where(function ($query) use ($term) {
                        $query
                            ->where('name', 'like', $term)
                            ->orWhere('business_name', 'like', $term)
                            ->orWhere('business_category', 'like', $term)
                            ->orWhere('short_description', 'like', $term)
                            ->orWhere('description', 'like', $term);
                    });
                })
                ->when($request->filled('city'), function ($query) use ($request) {
                    $city = trim((string) $request->city);

                    $query->where(function ($query) use ($city) {
                        $query->where('city', 'like', '%' . $city . '%');

                        if (Schema::hasColumn('users', 'service_cities')) {
                            $query->orWhereJsonContains('service_cities', $city);
                        }
                    });
                })
                ->when($request->filled('work_mode'), function ($query) use ($request) {
                    if ($request->work_mode === 'online') {
                        $query->where(function ($query) {
                            $query
                                ->whereNull('city')
                                ->orWhere('city', '')
                                ->orWhere('service_areas', 'like', '%онлайн%')
                                ->orWhere('service_areas', 'like', '%remote%')
                                ->orWhere('service_areas', 'like', '%дистанционно%');
                        });
                    }
                });

            $profiles = ProfileTrust::ranked(ProfileTrust::attach($query->get()))
                ->when($request->filled('category'), function ($profiles) use ($request) {
                    $category = trim((string) $request->category);

                    return $profiles
                        ->filter(fn (User $profile) => CategoryCatalog::matches((string) $profile->business_category, $category)
                            || collect($profile->serviceCategories())->contains(fn ($profileCategory) => CategoryCatalog::matches((string) $profileCategory, $category)))
                        ->values();
                })
                ->when($request->filled('rating'), function ($profiles) use ($request) {
                    $minimum = (float) $request->rating;

                    return $profiles->filter(fn (User $profile) => (float) data_get($profile, 'trust_summary.average_rating', 0) >= $minimum);
                })
                ->when($request->filled('availability'), function ($profiles) {
                    return $profiles->filter(fn (User $profile) => !$profile->last_active_at || $profile->last_active_at->greaterThanOrEqualTo(now()->subDays(30)));
                })
                ->values();

            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 12;
            $freelancers = new LengthAwarePaginator(
                $profiles->forPage($page, $perPage)->values(),
                $profiles->count(),
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        }

        $categories = CategoryCatalog::names()->all();

        return view('bon.freelancers', compact('freelancers', 'categories'));
    }
}
