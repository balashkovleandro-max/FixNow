<?php

namespace App\Support;

use App\Models\FreelancerJobApplication;
use App\Models\Review;
use App\Models\ServiceRequestAssignment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ProfileTrust
{
    public static function summary(User $profile): array
    {
        $rating = self::rating($profile);
        $projects = self::projectStats($profile);
        $response = self::responseStats($profile);
        $profileCompleteness = (int) (ProfileCompletion::summary($profile)['percent'] ?? 0);
        $lastActivityAt = self::lastActivityAt($profile);
        $activityScore = self::activityScore($lastActivityAt);
        $emailVerified = (bool) $profile->email_verified_at;
        $phoneVerified = Schema::hasColumn('users', 'phone_verified_at') && (bool) $profile->phone_verified_at;

        $score = min(100, (int) round(
            min(25, $profileCompleteness * 0.25)
            + ($emailVerified ? 10 : 0)
            + ($phoneVerified ? 10 : 0)
            + min(20, $projects['completed'] * 4)
            + min(20, ((float) ($rating['average'] ?? 0)) * 4)
            + $activityScore
        ));

        $summary = [
            'average_rating' => $rating['average'],
            'reviews_count' => $rating['count'],
            'completed_projects_count' => $projects['completed'],
            'total_projects_count' => $projects['total'],
            'success_rate' => $projects['success_rate'],
            'registered_year' => $profile->created_at?->format('Y'),
            'is_verified' => (bool) $profile->is_verified,
            'email_verified' => $emailVerified,
            'phone_verified' => $phoneVerified,
            'trust_score' => $score,
            'response_average_minutes' => $response['average_minutes'],
            'response_rate' => $response['rate'],
            'response_label' => self::responseLabel($response['average_minutes']),
            'last_active_at' => $lastActivityAt,
            'profile_completeness' => $profileCompleteness,
        ];

        $summary['badges'] = self::badges($profile, $summary);
        $summary['reasons'] = self::reasons($summary);
        $summary['sort_tuple'] = [
            $profile->isPremium() ? 1 : 0,
            $summary['trust_score'],
            (float) ($summary['average_rating'] ?? 0),
            $summary['completed_projects_count'],
            $lastActivityAt?->timestamp ?? 0,
        ];

        return $summary;
    }

    public static function attach(Collection $profiles): Collection
    {
        return $profiles->map(function (User $profile) {
            $summary = self::summary($profile);

            $profile->setAttribute('trust_summary', $summary);
            $profile->setAttribute('trust_score', $summary['trust_score']);
            $profile->setAttribute('trust_badges', $summary['badges']);
            $profile->setAttribute('trust_completed_projects_count', $summary['completed_projects_count']);
            $profile->setAttribute('trust_success_rate', $summary['success_rate']);
            $profile->setAttribute('trust_response_label', $summary['response_label']);
            $profile->setAttribute('trust_response_rate', $summary['response_rate']);

            return $profile;
        });
    }

    public static function ranked(Collection $profiles): Collection
    {
        return $profiles
            ->sort(function (User $first, User $second) {
                $firstSummary = data_get($first, 'trust_summary') ?: self::summary($first);
                $secondSummary = data_get($second, 'trust_summary') ?: self::summary($second);

                return self::compareTuples($secondSummary['sort_tuple'], $firstSummary['sort_tuple']);
            })
            ->values();
    }

    public static function score(User $profile): int
    {
        return self::summary($profile)['trust_score'];
    }

    public static function badges(User $profile, array $summary): array
    {
        $badges = [];

        if ($profile->isPremium()) {
            $badges[] = 'Premium';
        }

        if ($summary['is_verified']) {
            $badges[] = 'Верифициран';
        }

        if ($summary['completed_projects_count'] < 3 && $profile->created_at?->greaterThanOrEqualTo(now()->subDays(90))) {
            $badges[] = 'Нов талант';
        }

        if ($summary['trust_score'] >= 70 && $summary['completed_projects_count'] >= 3) {
            $badges[] = 'Надежден изпълнител';
        }

        if (($summary['average_rating'] ?? 0) >= 4.8 && $summary['reviews_count'] >= 3) {
            $badges[] = 'Топ оценен';
        }

        if (($summary['response_average_minutes'] ?? null) !== null && $summary['response_average_minutes'] <= 180 && $summary['response_rate'] >= 70) {
            $badges[] = 'Бърз отговор';
        }

        return array_values(array_unique($badges));
    }

    public static function reasons(array $summary): array
    {
        $reasons = [];

        if ($summary['trust_score'] >= 70) {
            $reasons[] = 'Има висок Trust Score, изграден от активност, профилна завършеност и репутация.';
        }

        if (($summary['average_rating'] ?? 0) >= 4.5 && $summary['reviews_count'] > 0) {
            $reasons[] = 'Получава положителни оценки от клиенти.';
        }

        if ($summary['completed_projects_count'] > 0) {
            $reasons[] = 'Има завършени проекти и реална история в BON.';
        }

        if ($summary['response_label']) {
            $reasons[] = 'Отговаря средно за ' . $summary['response_label'] . '.';
        }

        if ($summary['profile_completeness'] >= 80) {
            $reasons[] = 'Профилът е добре попълнен с ясна информация за услуги, контакт и присъствие.';
        }

        if ($summary['is_verified']) {
            $reasons[] = 'Профилът е верифициран от BON.';
        }

        if (empty($reasons)) {
            $reasons[] = 'Профилът е нов и започва да изгражда своята репутация в BON.';
        }

        return $reasons;
    }

    private static function rating(User $profile): array
    {
        if (!Schema::hasTable('reviews')) {
            return ['average' => null, 'count' => 0];
        }

        $average = Review::query()
            ->where('business_id', $profile->id)
            ->where('status', Review::STATUS_APPROVED)
            ->avg('rating');

        $count = Review::query()
            ->where('business_id', $profile->id)
            ->where('status', Review::STATUS_APPROVED)
            ->count();

        return [
            'average' => $average !== null ? round((float) $average, 1) : null,
            'count' => (int) $count,
        ];
    }

    private static function projectStats(User $profile): array
    {
        if ($profile->isFreelancer()) {
            return self::freelancerProjectStats($profile);
        }

        return self::businessProjectStats($profile);
    }

    private static function freelancerProjectStats(User $profile): array
    {
        if (!Schema::hasTable('freelancer_job_applications')) {
            return ['total' => 0, 'completed' => 0, 'success_rate' => 0];
        }

        $query = FreelancerJobApplication::query()->where('freelancer_id', $profile->id);
        $total = (clone $query)->count();
        $completed = (clone $query)->whereIn('status', [
            FreelancerJobApplication::STATUS_COMPLETED,
            FreelancerJobApplication::STATUS_ACCEPTED,
            FreelancerJobApplication::STATUS_DONE,
        ])->count();

        return [
            'total' => (int) $total,
            'completed' => (int) $completed,
            'success_rate' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }

    private static function businessProjectStats(User $profile): array
    {
        if (!Schema::hasTable('service_request_assignments')) {
            return ['total' => 0, 'completed' => 0, 'success_rate' => 0];
        }

        $query = ServiceRequestAssignment::query()->where('business_id', $profile->id);
        $total = (clone $query)->count();
        $completed = (clone $query)->whereIn('status', ['completed', 'closed'])->count();

        return [
            'total' => (int) $total,
            'completed' => (int) $completed,
            'success_rate' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }

    private static function responseStats(User $profile): array
    {
        if (!$profile->isBusiness() || !Schema::hasTable('service_request_assignments')) {
            return ['average_minutes' => null, 'rate' => 0];
        }

        $assignments = ServiceRequestAssignment::query()
            ->where('business_id', $profile->id)
            ->whereNotNull('sent_at')
            ->get(['sent_at', 'contacted_at', 'status']);

        $total = $assignments->count();

        if ($total === 0) {
            return ['average_minutes' => null, 'rate' => 0];
        }

        $responded = $assignments->filter(fn ($assignment) => $assignment->contacted_at !== null || in_array($assignment->status, ['contacted', 'completed', 'closed'], true));
        $minutes = $responded
            ->filter(fn ($assignment) => $assignment->sent_at && $assignment->contacted_at)
            ->map(fn ($assignment) => max(0, $assignment->sent_at->diffInMinutes($assignment->contacted_at)));

        return [
            'average_minutes' => $minutes->isNotEmpty() ? (int) round($minutes->avg()) : null,
            'rate' => (int) round(($responded->count() / $total) * 100),
        ];
    }

    private static function responseLabel(?int $minutes): ?string
    {
        if ($minutes === null) {
            return null;
        }

        if ($minutes < 60) {
            return $minutes . ' мин';
        }

        $hours = max(1, (int) round($minutes / 60));

        return $hours . ' ' . ($hours === 1 ? 'час' : 'часа');
    }

    private static function lastActivityAt(User $profile)
    {
        $dates = collect([
            $profile->last_active_at,
            $profile->updated_at,
            $profile->created_at,
        ])->filter();

        if ($profile->isFreelancer() && Schema::hasTable('freelancer_job_applications')) {
            $application = FreelancerJobApplication::query()
                ->where('freelancer_id', $profile->id)
                ->latest()
                ->first(['created_at']);

            if ($application?->created_at) {
                $dates->push($application->created_at);
            }
        }

        return $dates->sortDesc()->first();
    }

    private static function activityScore($lastActivityAt): int
    {
        if (!$lastActivityAt) {
            return 0;
        }

        if ($lastActivityAt->greaterThanOrEqualTo(now()->subDays(7))) {
            return 15;
        }

        if ($lastActivityAt->greaterThanOrEqualTo(now()->subDays(30))) {
            return 10;
        }

        if ($lastActivityAt->greaterThanOrEqualTo(now()->subDays(90))) {
            return 5;
        }

        return 0;
    }

    private static function compareTuples(array $first, array $second): int
    {
        foreach ($first as $index => $value) {
            $comparison = $value <=> ($second[$index] ?? 0);

            if ($comparison !== 0) {
                return $comparison;
            }
        }

        return 0;
    }
}
