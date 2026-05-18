<?php

namespace App\Support;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestAssignment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ServiceRequestMatcher
{
    public function assign(ServiceRequest $serviceRequest, int $limit = 3): Collection
    {
        if (!Schema::hasTable('service_request_assignments')) {
            return collect();
        }

        $businesses = $this->match($serviceRequest, $limit);

        $assignments = $businesses->map(function (User $business) use ($serviceRequest) {
            return ServiceRequestAssignment::firstOrCreate(
                [
                    'service_request_id' => $serviceRequest->id,
                    'business_id' => $business->id,
                ],
                [
                    'status' => ServiceRequestAssignment::STATUS_SENT,
                    'sent_at' => now(),
                ]
            );
        });

        if (
            $assignments->isNotEmpty()
            && Schema::hasColumn('service_requests', 'assigned_business_id')
            && !$serviceRequest->assigned_business_id
        ) {
            $serviceRequest->forceFill([
                'assigned_business_id' => $assignments->first()->business_id,
            ])->save();
        }

        return $assignments;
    }

    public function match(ServiceRequest $serviceRequest, int $limit = 3): Collection
    {
        $publicBusinesses = BusinessGrowthMetrics::publicBusinesses();

        if ($publicBusinesses->isEmpty()) {
            return collect();
        }

        $cityMatches = BusinessGrowthMetrics::filterByCity($publicBusinesses, $serviceRequest->city);

        if (!CategoryCatalog::acceptsRequests($serviceRequest->category)) {
            return collect();
        }

        $strongMatches = $cityMatches
            ->filter(fn (User $business) => CategoryCatalog::businessMatchesRequest($business, $serviceRequest->category, $serviceRequest->city))
            ->values();

        return $strongMatches
            ->unique('id')
            ->take($limit)
            ->values();
    }

}
