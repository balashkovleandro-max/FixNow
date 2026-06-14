<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLog;
use App\Models\FreelancerCreditTransaction;
use App\Models\FreelancerJobApplication;
use App\Models\Review;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use App\Models\User;
use App\Support\CategoryCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $stats = $this->platformStats();

        $recentBusinesses = User::query()
            ->where('role', 'business')
            ->latest()
            ->take(8)
            ->get();

        $recentUsers = User::query()
            ->latest()
            ->take(8)
            ->get();

        $recentRequests = Schema::hasTable('service_requests')
            ? ServiceRequest::query()->with(['customer', 'assignedBusiness', 'offers'])->latest()->take(8)->get()
            : collect();

        $activityLogs = Schema::hasTable('admin_activity_logs')
            ? AdminActivityLog::query()->with('admin')->latest()->take(12)->get()
            : collect();

        return view('admin.dashboard', compact('stats', 'recentBusinesses', 'recentUsers', 'recentRequests', 'activityLogs'));
    }

    public function businesses(Request $request): View
    {
        $filters = [
            'status' => $request->query('status', 'all'),
            'search' => trim((string) $request->query('search', '')),
        ];

        $query = User::query()
            ->where('role', 'business')
            ->withCount([
                'services',
                'businessPhotos',
                'serviceRequestOffers',
                'reviews',
            ]);

        $this->applyBusinessFilters($query, $filters);

        $businesses = $query
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.businesses', compact('businesses', 'filters'));
    }

    public function updateBusiness(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isBusiness(), 404);

        $validated = $request->validate([
            'action' => ['required', Rule::in([
                'activate',
                'suspend',
                'restore',
                'premium',
                'standard',
                'remove_plan',
                'mark_paid',
                'mark_unpaid',
                'trial',
                'expired',
                'cancelled',
                'verify',
                'unverify',
            ])],
        ]);

        $old = $this->businessSnapshot($user);
        $updates = [];

        match ($validated['action']) {
            'activate', 'restore' => $updates = [
                'is_suspended' => false,
                'subscription_plan' => $user->subscription_plan ?: 'standard',
                'subscription_status' => 'active',
                'subscription_started_at' => $user->subscription_started_at ?: now(),
                'subscription_ends_at' => $user->subscription_ends_at && $user->subscription_ends_at->greaterThan(now())
                    ? $user->subscription_ends_at
                    : now()->addDays(30),
                'cancelled_at' => null,
            ],
            'suspend' => $updates = [
                'is_suspended' => true,
                'subscription_status' => 'suspended',
                'cancelled_at' => now(),
            ],
            'premium' => $updates = ['subscription_plan' => 'premium'],
            'standard' => $updates = ['subscription_plan' => 'standard'],
            'remove_plan' => $updates = [
                'subscription_plan' => null,
                'subscription_status' => 'expired',
                'subscription_ends_at' => now(),
            ],
            'mark_paid' => $updates = [
                'subscription_plan' => $user->subscription_plan ?: 'standard',
                'subscription_status' => 'active',
                'subscription_started_at' => $user->subscription_started_at ?: now(),
                'subscription_ends_at' => $user->subscription_ends_at && $user->subscription_ends_at->greaterThan(now())
                    ? $user->subscription_ends_at
                    : now()->addDays(30),
                'cancelled_at' => null,
            ],
            'mark_unpaid', 'expired' => $updates = [
                'subscription_status' => 'expired',
                'subscription_ends_at' => now(),
                'cancelled_at' => null,
            ],
            'trial' => $updates = [
                'is_suspended' => false,
                'subscription_status' => 'trial',
                'trial_started_at' => $user->trial_started_at ?: now(),
                'trial_ends_at' => now()->addDays(14),
                'cancelled_at' => null,
            ],
            'cancelled' => $updates = [
                'subscription_status' => 'cancelled',
                'subscription_ends_at' => now(),
                'cancelled_at' => now(),
            ],
            'verify' => $updates = [
                'is_verified' => true,
                'verified_at' => now(),
            ],
            'unverify' => $updates = [
                'is_verified' => false,
                'verified_at' => null,
            ],
        };

        if (!Schema::hasColumn('users', 'is_suspended')) {
            unset($updates['is_suspended']);
        }

        $user->forceFill($updates)->save();
        $user->refresh();

        $this->logAdminAction('business.'.$validated['action'], $user, $old, $this->businessSnapshot($user));

        return back()->with('success', 'Промяната по бизнес профила е записана.');
    }

    public function editBusiness(User $user): View
    {
        abort_unless($user->isBusiness(), 404);

        return view('admin.business-edit', ['business' => $user]);
    }

    public function saveBusinessProfile(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isBusiness(), 404);

        $validated = $request->validate([
            'business_name' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:120'],
            'business_category' => ['nullable', 'string', 'max:160'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'website' => ['nullable', 'string', 'max:255'],
            'working_hours' => ['nullable', 'string', 'max:255'],
        ]);

        $old = $this->businessSnapshot($user);
        $user->forceFill($validated)->save();
        $user->refresh();

        $this->logAdminAction('business.profile.updated', $user, $old, $this->businessSnapshot($user));

        return redirect()->route('admin.businesses.index', ['search' => $user->email])->with('success', 'Бизнес профилът е редактиран.');
    }

    public function destroyBusiness(User $user): RedirectResponse
    {
        abort_unless($user->isBusiness(), 404);

        $old = $this->businessSnapshot($user);
        $name = $user->business_name ?: $user->name;

        $user->delete();

        $this->logAdminAction('business.deleted', $user, $old, ['deleted' => true, 'name' => $name]);

        return redirect()->route('admin.businesses.index')->with('success', 'Бизнес профилът е изтрит.');
    }

    public function users(Request $request): View
    {
        $filters = [
            'role' => $request->query('role', 'all'),
            'type' => $request->query('type', 'all'),
            'search' => trim((string) $request->query('search', '')),
        ];

        $query = User::query()
            ->withCount([
                'customerServiceRequests',
                'serviceRequestOffers',
                'freelancerJobApplications',
            ]);

        if (in_array($filters['role'], ['admin', 'business', 'customer', 'client', 'freelancer'], true)) {
            $query->where('role', $filters['role']);
        }

        if (in_array($filters['type'], ['admin', 'business', 'client', 'freelancer'], true)) {
            $type = $filters['type'];

            $query->where(function ($query) use ($type) {
                $query->where('role', $type);

                if ($type === 'client') {
                    $query->orWhere('role', 'customer');
                }

                if (Schema::hasColumn('users', 'account_type')) {
                    $query->orWhere('account_type', $type);
                }

                if (Schema::hasColumn('users', 'profile_type')) {
                    $query->orWhere('profile_type', $type);
                }
            });
        }

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('business_name', 'like', '%'.$search.'%');
            });
        }

        $users = $query->latest()->paginate(30)->withQueryString();

        return view('admin.users', compact('users', 'filters'));
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['role', 'suspend', 'activate'])],
            'role' => ['nullable', Rule::in(['admin', 'business', 'customer', 'client', 'freelancer'])],
        ]);

        $old = $this->userSnapshot($user);

        if ($validated['action'] === 'role' && filled($validated['role'] ?? null)) {
            $updates = ['role' => $validated['role']];

            if (Schema::hasColumn('users', 'account_type')) {
                $updates['account_type'] = $validated['role'] === 'customer' ? 'client' : $validated['role'];
            }

            if (Schema::hasColumn('users', 'profile_type')) {
                $updates['profile_type'] = in_array($validated['role'], ['business', 'freelancer'], true) ? $validated['role'] : null;
            }

            $user->forceFill($updates)->save();
        }

        if ($validated['action'] === 'suspend' && Schema::hasColumn('users', 'is_suspended')) {
            abort_if($user->is(auth()->user()), 422, 'Не можеш да спреш собствения си admin профил.');
            $user->forceFill(['is_suspended' => true])->save();
        }

        if ($validated['action'] === 'activate' && Schema::hasColumn('users', 'is_suspended')) {
            $user->forceFill(['is_suspended' => false])->save();
        }

        $user->refresh();
        $this->logAdminAction('user.'.$validated['action'], $user, $old, $this->userSnapshot($user));

        return back()->with('success', 'Потребителят е обновен.');
    }

    public function destroyUser(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 422, 'Не можеш да изтриеш собствения си admin профил.');

        $old = $this->userSnapshot($user);
        $user->delete();

        $this->logAdminAction('user.deleted', $user, $old, ['deleted' => true]);

        return redirect()->route('admin.users.index')->with('success', 'Потребителят е изтрит.');
    }

    public function requests(Request $request): View
    {
        $filters = [
            'status' => $request->query('status', 'all'),
            'search' => trim((string) $request->query('search', '')),
        ];

        $query = ServiceRequest::query()
            ->with(['customer', 'assignedBusiness', 'offers.business', 'selectedOffer.business', 'photos'])
            ->withCount('offers');

        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%')
                    ->orWhere('category', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $requests = $query->latest()->paginate(30)->withQueryString();
        $businesses = User::query()->where('role', 'business')->orderBy('business_name')->get(['id', 'name', 'business_name', 'email']);

        return view('admin.requests', compact('requests', 'filters', 'businesses'));
    }

    public function updateRequest(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in([
                ServiceRequest::STATUS_NEW,
                ServiceRequest::STATUS_OPEN,
                ServiceRequest::STATUS_CONTACTED,
                ServiceRequest::STATUS_IN_PROGRESS,
                ServiceRequest::STATUS_COMPLETED,
                ServiceRequest::STATUS_CANCELLED,
                ServiceRequest::STATUS_CLOSED,
            ])],
            'assigned_business_id' => ['nullable', 'exists:users,id'],
        ]);

        $old = $serviceRequest->only(['status', 'assigned_business_id', 'closed_at']);

        $updates = [];

        if (array_key_exists('status', $validated) && filled($validated['status'])) {
            $updates['status'] = $validated['status'];

            if (in_array($validated['status'], [ServiceRequest::STATUS_COMPLETED, ServiceRequest::STATUS_CANCELLED, ServiceRequest::STATUS_CLOSED], true)) {
                $updates['closed_at'] = now();
            }
        }

        if (array_key_exists('assigned_business_id', $validated)) {
            $updates['assigned_business_id'] = $validated['assigned_business_id'] ?: null;
        }

        $serviceRequest->forceFill($updates)->save();
        $serviceRequest->refresh();

        $this->logAdminAction('request.updated', $serviceRequest, $old, $serviceRequest->only(['status', 'assigned_business_id', 'closed_at']));

        return back()->with('success', 'Заявката е обновена.');
    }

    public function destroyRequest(ServiceRequest $serviceRequest): RedirectResponse
    {
        $old = $serviceRequest->toArray();

        if (Schema::hasTable('service_request_offers')) {
            $serviceRequest->offers()->delete();
        }

        if (Schema::hasTable('service_request_assignments')) {
            $serviceRequest->assignments()->delete();
        }

        if (Schema::hasTable('service_request_photos')) {
            $serviceRequest->photos()->delete();
        }

        $serviceRequest->delete();

        $this->logAdminAction('request.deleted', $serviceRequest, $old, ['deleted' => true]);

        return redirect()->route('admin.requests.index')->with('success', 'Заявката е изтрита.');
    }

    public function offers(): View
    {
        $serviceOffers = Schema::hasTable('service_request_offers')
            ? ServiceRequestOffer::query()->with(['business', 'serviceRequest'])->latest()->paginate(30)
            : collect();

        $freelancerApplications = Schema::hasTable('freelancer_job_applications')
            ? FreelancerJobApplication::query()->with(['freelancer', 'job.business'])->latest()->take(40)->get()
            : collect();

        return view('admin.offers', compact('serviceOffers', 'freelancerApplications'));
    }

    public function subscriptions(Request $request): View
    {
        $businesses = User::query()
            ->where('role', 'business')
            ->latest()
            ->paginate(35)
            ->withQueryString();

        $mode = $request->routeIs('admin.payments') ? 'payments' : 'subscriptions';

        return view('admin.subscriptions', compact('businesses', 'mode'));
    }

    public function reviews(): View
    {
        $reviews = Schema::hasTable('reviews')
            ? Review::query()->with('business')->latest()->paginate(40)
            : collect();

        return view('admin.reviews', compact('reviews'));
    }

    public function categories(): View
    {
        $categories = CategoryCatalog::all();
        $databaseCategories = Schema::hasTable('service_categories')
            ? ServiceCategory::query()->orderBy('sort_order')->orderBy('name')->get()
            : collect();

        return view('admin.categories', compact('categories', 'databaseCategories'));
    }

    public function cities(): View
    {
        $businessCities = User::query()
            ->where('role', 'business')
            ->whereNotNull('city')
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->filter()
            ->values();

        $requestCities = Schema::hasTable('service_requests')
            ? ServiceRequest::query()->whereNotNull('city')->select('city')->distinct()->orderBy('city')->pluck('city')->filter()->values()
            : collect();

        return view('admin.cities', compact('businessCities', 'requestCities'));
    }

    public function settings(): View
    {
        $stats = $this->platformStats();

        return view('admin.settings', compact('stats'));
    }

    public function activity(): View
    {
        $activityLogs = Schema::hasTable('admin_activity_logs')
            ? AdminActivityLog::query()->with('admin')->latest()->paginate(50)
            : collect();

        return view('admin.activity', compact('activityLogs'));
    }

    private function platformStats(): array
    {
        $businessBase = User::query()->where('role', 'business');
        $activeStatuses = ['active', 'trialing', 'trial'];

        return [
            'total_users' => User::query()->count(),
            'clients' => User::query()->whereIn('role', ['customer', 'client'])->count(),
            'freelancers' => User::query()->where('role', 'freelancer')->count(),
            'total_businesses' => (clone $businessBase)->count(),
            'active_businesses' => (clone $businessBase)->whereIn('subscription_status', $activeStatuses)->count(),
            'suspended_businesses' => (clone $businessBase)
                ->where(function ($query) {
                    if (Schema::hasColumn('users', 'is_suspended')) {
                        $query->where('is_suspended', true);
                    }

                    $query->orWhereIn('subscription_status', ['suspended', 'cancelled', 'canceled']);
                })
                ->count(),
            'paid_businesses' => (clone $businessBase)
                ->whereIn('subscription_status', ['active', 'trialing'])
                ->whereIn('subscription_plan', ['standard', 'premium'])
                ->count(),
            'unpaid_businesses' => (clone $businessBase)
                ->where(function ($query) {
                    $query
                        ->whereNull('subscription_status')
                        ->orWhereNotIn('subscription_status', ['active', 'trialing', 'trial']);
                })
                ->count(),
            'standard_subscriptions' => (clone $businessBase)->where('subscription_plan', 'standard')->count(),
            'premium_subscriptions' => (clone $businessBase)->where('subscription_plan', 'premium')->count(),
            'trial_businesses' => (clone $businessBase)->whereIn('subscription_status', ['trial', 'trialing'])->count(),
            'expired_profiles' => (clone $businessBase)->where('subscription_status', 'expired')->count(),
            'new_registrations' => User::query()->where('created_at', '>=', now()->subDays(7))->count(),
            'new_requests' => Schema::hasTable('service_requests') ? ServiceRequest::query()->where('created_at', '>=', now()->subDays(7))->count() : 0,
            'total_requests' => Schema::hasTable('service_requests') ? ServiceRequest::query()->count() : 0,
            'sent_offers' => Schema::hasTable('service_request_offers') ? ServiceRequestOffer::query()->count() : 0,
            'accepted_offers' => Schema::hasTable('service_request_offers') ? ServiceRequestOffer::query()->where('status', ServiceRequestOffer::STATUS_ACCEPTED)->count() : 0,
            'credit_transactions' => Schema::hasTable('freelancer_credit_transactions') ? FreelancerCreditTransaction::query()->count() : 0,
        ];
    }

    private function applyBusinessFilters($query, array $filters): void
    {
        match ($filters['status']) {
            'active' => $query->whereIn('subscription_status', ['active', 'trialing', 'trial']),
            'stopped' => $query->where(function ($query) {
                if (Schema::hasColumn('users', 'is_suspended')) {
                    $query->where('is_suspended', true);
                }

                $query->orWhereIn('subscription_status', ['suspended', 'cancelled', 'canceled']);
            }),
            'paid' => $query->whereIn('subscription_status', ['active', 'trialing'])->whereIn('subscription_plan', ['standard', 'premium']),
            'unpaid' => $query->where(function ($query) {
                $query->whereNull('subscription_status')->orWhereNotIn('subscription_status', ['active', 'trialing', 'trial']);
            }),
            'standard' => $query->where('subscription_plan', 'standard'),
            'premium' => $query->where('subscription_plan', 'premium'),
            'trial' => $query->whereIn('subscription_status', ['trial', 'trialing']),
            'expired' => $query->where('subscription_status', 'expired'),
            'verified' => $query->where('is_verified', true),
            'unverified' => $query->where(function ($query) {
                $query->where('is_verified', false)->orWhereNull('is_verified');
            }),
            default => null,
        };

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($query) use ($search) {
                $query
                    ->where('business_name', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%')
                    ->orWhere('business_category', 'like', '%'.$search.'%')
                    ->orWhere('service_cities', 'like', '%'.$search.'%')
                    ->orWhere('service_categories', 'like', '%'.$search.'%');
            });
        }
    }

    private function businessSnapshot(User $user): array
    {
        return $user->only([
            'id',
            'business_name',
            'name',
            'email',
            'role',
            'subscription_plan',
            'subscription_status',
            'subscription_started_at',
            'subscription_ends_at',
            'trial_started_at',
            'trial_ends_at',
            'cancelled_at',
            'is_verified',
            'verified_at',
            'is_suspended',
        ]);
    }

    private function userSnapshot(User $user): array
    {
        return $user->only(['id', 'name', 'email', 'role', 'account_type', 'profile_type', 'phone', 'is_suspended', 'created_at']);
    }

    private function logAdminAction(string $action, object $subject, ?array $oldValues = null, ?array $newValues = null, array $metadata = []): void
    {
        if (!Schema::hasTable('admin_activity_logs')) {
            return;
        }

        AdminActivityLog::query()->create([
            'admin_user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject::class,
            'subject_id' => $subject->id ?? null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
        ]);
    }
}
