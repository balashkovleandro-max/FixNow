<?php

namespace App\Http\Controllers;

use App\Models\FreelancerJob;
use App\Models\FreelancerJobApplication;
use App\Support\CategoryCatalog;
use App\Support\FreelancerCredits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class FreelancerJobController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless($user && $user->isFreelancer(), 403);

        FreelancerCredits::ensureMonthlyCredits($user);

        $jobs = FreelancerJob::query()
            ->open()
            ->with('business')
            ->withCount('applications')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%' . trim((string) $request->q) . '%';

                $query->where(function ($query) use ($term) {
                    $query
                        ->where('title', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('category', 'like', $term)
                        ->orWhere('location', 'like', $term);
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->category))
            ->when($request->filled('location'), fn ($query) => $query->where('location', 'like', '%' . trim((string) $request->location) . '%'))
            ->when($request->filled('work_mode') && Schema::hasColumn('freelancer_jobs', 'work_mode'), fn ($query) => $query->where('work_mode', $request->work_mode))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $creditStats = FreelancerCredits::stats($user);
        $categories = $this->categories();

        return view('freelancer.jobs.index', compact('jobs', 'creditStats', 'categories'));
    }

    public function publicIndex(Request $request)
    {
        $jobs = FreelancerJob::query()
            ->open()
            ->with('business')
            ->withCount('applications')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%' . trim((string) $request->q) . '%';

                $query->where(function ($query) use ($term) {
                    $query
                        ->where('title', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('category', 'like', $term)
                        ->orWhere('location', 'like', $term);
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->category))
            ->when($request->filled('location'), fn ($query) => $query->where('location', 'like', '%' . trim((string) $request->location) . '%'))
            ->when($request->filled('work_mode') && Schema::hasColumn('freelancer_jobs', 'work_mode'), fn ($query) => $query->where('work_mode', $request->work_mode))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = $this->categories();

        return view('freelancer.jobs.public-index', compact('jobs', 'categories'));
    }

    public function publicShow(FreelancerJob $freelancerJob)
    {
        abort_unless($freelancerJob->isOpen(), 404);

        $freelancerJob->load('business')->loadCount('applications');

        return view('freelancer.jobs.public-show', compact('freelancerJob'));
    }

    public function show(Request $request, FreelancerJob $freelancerJob)
    {
        $user = $request->user();

        abort_unless($user && $user->isFreelancer(), 403);

        FreelancerCredits::ensureMonthlyCredits($user);

        $freelancerJob->load('business');

        $hasApplied = $freelancerJob->applications()
            ->where('freelancer_id', $user->id)
            ->exists();

        $creditStats = FreelancerCredits::stats($user);
        $packages = FreelancerCredits::PACKAGES;

        return view('freelancer.jobs.show', compact('freelancerJob', 'creditStats', 'packages', 'hasApplied'));
    }

    public function apply(Request $request, FreelancerJob $freelancerJob)
    {
        $user = $request->user();

        abort_unless($user && $user->isFreelancer(), 403);

        $validated = $request->validate([
            'cover_message' => ['nullable', 'string', 'max:3000'],
            'proposed_price' => ['nullable', 'string', 'max:120'],
            'proposed_timeframe' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:80'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
        ]);

        try {
            FreelancerCredits::applyToJob(
                $user,
                $freelancerJob,
                $validated['cover_message'] ?? null,
                $validated['proposed_price'] ?? null,
                $validated['proposed_timeframe'] ?? null,
                $validated['contact_phone'] ?? null,
                $validated['contact_email'] ?? null,
                $validated['portfolio_url'] ?? null
            );
        } catch (ValidationException $exception) {
            $errors = $exception->errors();

            return back()
                ->withErrors($errors)
                ->withInput()
                ->with('show_credit_modal', array_key_exists('credits', $errors));
        }

        return redirect()
            ->route('freelancer.jobs.show', $freelancerJob)
            ->with('success', 'Кандидатурата е изпратена. Оставащ баланс: ' . $user->freelancerCreditsBalance() . ' кредита.');
    }

    public function selectApplication(Request $request, FreelancerJobApplication $application)
    {
        $business = $request->user();

        abort_unless($business && ($business->isBusiness() || $business->isCustomer()), 403);

        $application->loadMissing('job');
        $job = $application->job;

        abort_unless($job && (int) $job->business_id === (int) $business->id, 403);

        if ($application->status === FreelancerJobApplication::STATUS_ACCEPTED) {
            return redirect()
                ->route('business.jobs.index')
                ->with('success', 'Този кандидат вече е избран.');
        }

        DB::transaction(function () use ($application, $job) {
            FreelancerJobApplication::query()
                ->where('freelancer_job_id', $job->id)
                ->whereKeyNot($application->id)
                ->where('status', FreelancerJobApplication::STATUS_SUBMITTED)
                ->update(['status' => FreelancerJobApplication::STATUS_NOT_SELECTED]);

            $payload = [
                'status' => FreelancerJobApplication::STATUS_ACCEPTED,
            ];

            if (Schema::hasColumn('freelancer_job_applications', 'selected_at')) {
                $payload['selected_at'] = now();
            }

            $application->forceFill($payload)->save();

            $job->forceFill([
                'status' => FreelancerJob::STATUS_CLOSED,
            ])->save();
        });

        return redirect()
            ->route('business.jobs.index')
            ->with('success', 'Кандидатът е избран успешно. Обявата е затворена за нови кандидатури.');
    }

    public function businessIndex(Request $request)
    {
        $business = $request->user();

        abort_unless($business && ($business->isBusiness() || $business->isCustomer()), 403);

        $jobs = $business->freelancerJobs()
            ->withCount('applications')
            ->latest()
            ->paginate(12);

        return view('business.freelancer-jobs.index', compact('business', 'jobs'));
    }

    public function create(Request $request)
    {
        $business = $request->user();

        abort_unless($business && ($business->isBusiness() || $business->isCustomer()), 403);

        return view('business.freelancer-jobs.create', [
            'business' => $business,
            'categories' => $this->categories(),
        ]);
    }

    public function store(Request $request)
    {
        $business = $request->user();

        abort_unless($business && ($business->isBusiness() || $business->isCustomer()), 403);

        if ($business->isBusiness() && !$business->isTrialActive() && !$business->hasActiveSubscription()) {
            return redirect()
                ->route('business.billing')
                ->withErrors(['plan' => 'Публикуването на обяви е достъпно за активни бизнес профили.']);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:5000'],
            'budget' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'category' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:160'],
            'work_mode' => ['nullable', 'in:online,onsite,hybrid'],
            'client_name' => ['nullable', 'string', 'max:160'],
            'client_phone' => ['nullable', 'string', 'max:80'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx'],
        ]);

        $payload = collect($validated)
            ->except('attachment')
            ->filter(fn ($value, $column) => Schema::hasColumn('freelancer_jobs', $column))
            ->all();

        if ($request->hasFile('attachment') && Schema::hasColumn('freelancer_jobs', 'attachment_path')) {
            $payload['attachment_path'] = $request->file('attachment')->store('freelancer-projects', 'public');
        }

        $job = $business->freelancerJobs()->create(array_merge($payload, [
            'status' => FreelancerJob::STATUS_OPEN,
        ]));

        return redirect()
            ->route('business.jobs.index')
            ->with('success', 'Обявата е публикувана и вече е видима за фрийлансъри.');
    }

    private function categories(): array
    {
        return CategoryCatalog::names()->all();
    }
}
