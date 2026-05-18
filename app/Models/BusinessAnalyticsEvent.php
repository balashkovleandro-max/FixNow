<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

class BusinessAnalyticsEvent extends Model
{
    public const PROFILE_VIEW = 'profile_view';
    public const PHONE_CLICK = 'phone_click';
    public const WEBSITE_CLICK = 'website_click';
    public const INQUIRY_CLICK = 'inquiry_click';
    public const CHAT_CLICK = 'chat_click';
    public const SOCIAL_CLICK = 'social_click';

    protected $fillable = [
        'business_id',
        'actor_id',
        'event_type',
        'ip_hash',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public static function record(User $business, string $eventType, ?User $actor = null, array $metadata = []): void
    {
        if (!$business->isBusiness() || !Schema::hasTable('business_analytics_events')) {
            return;
        }

        try {
            $payload = [
                'business_id' => $business->id,
                'actor_id' => $actor?->id,
                'event_type' => $eventType,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => $metadata ?: null,
            ];

            if (Schema::hasColumn('business_analytics_events', 'ip_hash')) {
                $ip = (string) request()->ip();
                $payload['ip_hash'] = $ip !== ''
                    ? hash('sha256', $ip . '|' . (string) config('app.key'))
                    : null;
            }

            self::create($payload);
        } catch (Throwable) {
            // Analytics must never block the public profile or click redirects.
        }
    }

    public static function eventTypes(): array
    {
        return [
            self::PROFILE_VIEW,
            self::PHONE_CLICK,
            self::INQUIRY_CLICK,
            self::CHAT_CLICK,
            self::WEBSITE_CLICK,
            self::SOCIAL_CLICK,
        ];
    }

    public static function emptyCounts(): array
    {
        return array_fill_keys(self::eventTypes(), 0);
    }

    public static function clickEventTypes(): array
    {
        return [
            self::PHONE_CLICK,
            self::INQUIRY_CLICK,
            self::CHAT_CLICK,
            self::WEBSITE_CLICK,
            self::SOCIAL_CLICK,
        ];
    }

    public static function countsForBusiness(User $business, ?\DateTimeInterface $from = null): array
    {
        if (!Schema::hasTable('business_analytics_events')) {
            return self::emptyCounts();
        }

        $query = self::query()
            ->where('business_id', $business->id);

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        $counts = $query
            ->selectRaw('event_type, COUNT(*) as aggregate')
            ->groupBy('event_type')
            ->pluck('aggregate', 'event_type');

        return collect(self::emptyCounts())
            ->map(fn ($value, $eventType) => (int) ($counts[$eventType] ?? 0))
            ->all();
    }

    public static function platformCounts(?\DateTimeInterface $from = null): array
    {
        if (!Schema::hasTable('business_analytics_events')) {
            return self::emptyCounts();
        }

        $query = self::query();

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        $counts = $query
            ->selectRaw('event_type, COUNT(*) as aggregate')
            ->groupBy('event_type')
            ->pluck('aggregate', 'event_type');

        return collect(self::emptyCounts())
            ->map(fn ($value, $eventType) => (int) ($counts[$eventType] ?? 0))
            ->all();
    }

    public static function clickTotal(array $counts): int
    {
        return collect(self::clickEventTypes())
            ->sum(fn ($eventType) => (int) ($counts[$eventType] ?? 0));
    }

    public static function websiteAndSocialTotal(array $counts): int
    {
        return (int) ($counts[self::WEBSITE_CLICK] ?? 0)
            + (int) ($counts[self::SOCIAL_CLICK] ?? 0);
    }

    public static function topBusinessesBy(string $eventType, int $limit = 5)
    {
        if (!Schema::hasTable('business_analytics_events')) {
            return collect();
        }

        return self::query()
            ->selectRaw('business_id, COUNT(*) as aggregate')
            ->where('event_type', $eventType)
            ->whereNotNull('business_id')
            ->groupBy('business_id')
            ->orderByDesc('aggregate')
            ->with('business')
            ->take($limit)
            ->get();
    }

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
