<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminServiceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin();

        $filters = [
            'status' => $request->query('status', 'all'),
            'city' => trim((string) $request->query('city', '')),
            'category' => trim((string) $request->query('category', '')),
            'has_offers' => $request->query('has_offers', 'all'),
            'selected' => $request->query('selected', 'all'),
        ];

        $query = ServiceRequest::query()
            ->with(['assignedBusiness', 'assignments.business', 'offers.business', 'selectedOffer.business', 'customer'])
            ->withCount('offers');

        $this->applyFilters($query, $filters);

        $serviceRequests = $query
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $cities = ServiceRequest::query()
            ->whereNotNull('city')
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->filter()
            ->values();

        $categories = ServiceRequest::query()
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->filter()
            ->values();

        return view('admin.service-requests', compact('serviceRequests', 'filters', 'cities', 'categories'));
    }

    public function show(ServiceRequest $serviceRequest): View
    {
        $this->authorizeAdmin();

        $serviceRequest->load([
            'assignedBusiness',
            'assignments.business',
            'customer',
            'offers.business',
            'photos',
            'selectedOffer.business',
        ]);

        $publicOffersUrl = $serviceRequest->public_token
            ? route('service-requests.offers.show', ['serviceRequest' => $serviceRequest->public_token])
            : null;

        $timeline = $this->timelineFor($serviceRequest);

        return view('admin.service-request-show', compact('serviceRequest', 'publicOffersUrl', 'timeline'));
    }

    public function markContacted(ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorizeAdmin();

        $serviceRequest->markContacted();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Заявката беше маркирана като contacted.');
    }

    public function markClosed(ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorizeAdmin();

        $serviceRequest->markClosed();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Заявката беше маркирана като closed.');
    }

    public function markCompleted(ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorizeAdmin();

        $serviceRequest->markCompleted();

        return back()->with('success', 'Заявката беше маркирана като завършена.');
    }

    public function markCancelled(ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorizeAdmin();

        $serviceRequest->markCancelled();

        return back()->with('success', 'Заявката беше маркирана като отказана.');
    }

    private function applyFilters($query, array $filters): void
    {
        match ($filters['status']) {
            'open' => $query->whereIn('status', [
                ServiceRequest::STATUS_NEW,
                ServiceRequest::STATUS_OPEN,
                ServiceRequest::STATUS_CONTACTED,
            ]),
            'in_progress', 'accepted' => $query->where('status', ServiceRequest::STATUS_IN_PROGRESS),
            'completed' => $query->where('status', ServiceRequest::STATUS_COMPLETED),
            'cancelled_closed' => $query->whereIn('status', [
                ServiceRequest::STATUS_CANCELLED,
                ServiceRequest::STATUS_CLOSED,
            ]),
            'new' => $query->where('status', ServiceRequest::STATUS_NEW),
            'contacted' => $query->where('status', ServiceRequest::STATUS_CONTACTED),
            'cancelled' => $query->where('status', ServiceRequest::STATUS_CANCELLED),
            'closed' => $query->where('status', ServiceRequest::STATUS_CLOSED),
            default => null,
        };

        if ($filters['city'] !== '') {
            $query->where('city', 'like', '%'.$filters['city'].'%');
        }

        if ($filters['category'] !== '') {
            $query->where('category', 'like', '%'.$filters['category'].'%');
        }

        if ($filters['has_offers'] === 'yes') {
            $query->has('offers');
        } elseif ($filters['has_offers'] === 'no') {
            $query->doesntHave('offers');
        }

        if ($filters['selected'] === 'yes') {
            $query->whereNotNull('selected_offer_id');
        } elseif ($filters['selected'] === 'no') {
            $query->whereNull('selected_offer_id');
        }
    }

    private function timelineFor(ServiceRequest $serviceRequest): Collection
    {
        $events = collect([
            [
                'label' => 'Заявката е създадена',
                'date' => $serviceRequest->created_at,
                'note' => $serviceRequest->source === ServiceRequest::SOURCE_OFFER_FORM
                    ? 'Пусната през формата за оферта.'
                    : 'Изпратена директно към профил на бизнес.',
            ],
        ]);

        $serviceRequest->offers->each(function ($offer) use ($events) {
            $events->push([
                'label' => 'Оферта е изпратена',
                'date' => $offer->created_at,
                'note' => ($offer->business?->business_name ?: $offer->business?->name ?: 'Бизнес').' · '.$offer->price_estimate,
            ]);
        });

        if ($serviceRequest->selectedOffer) {
            $events->push([
                'label' => 'Оферта е избрана',
                'date' => $serviceRequest->accepted_offer_at ?: $serviceRequest->selectedOffer->updated_at,
                'note' => $serviceRequest->selectedOffer->business?->business_name ?: $serviceRequest->selectedOffer->business?->name ?: 'Избран бизнес',
            ]);
        }

        if (in_array($serviceRequest->status, [
            ServiceRequest::STATUS_CLOSED,
            ServiceRequest::STATUS_COMPLETED,
            ServiceRequest::STATUS_CANCELLED,
        ], true)) {
            $events->push([
                'label' => 'Заявката е приключена',
                'date' => $serviceRequest->closed_at ?: $serviceRequest->updated_at,
                'note' => 'Краен статус: '.$serviceRequest->status,
            ]);
        }

        return $events
            ->filter(fn ($event) => $event['date'])
            ->sortBy(fn ($event) => $event['date']->timestamp)
            ->values();
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
    }
}
