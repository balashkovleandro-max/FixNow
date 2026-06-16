<?php

namespace App\Http\Controllers;

use App\Mail\CustomerServiceRequestConfirmationMail;
use App\Mail\NewServiceRequestBusinessMail;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAssignment;
use App\Models\ServiceRequestOffer;
use App\Models\User;
use App\Support\CategoryCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

class BusinessServiceRequestController extends Controller
{
    public function store(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isBusiness() && $user->isPubliclyVisible(), 404);

        $validated = $request->validate([
            'customer_name' => 'required|string|max:120',
            'customer_phone' => 'required|string|max:40',
            'customer_email' => 'nullable|email|max:255',
            'city' => 'required|string|max:120',
            'message' => 'required|string|min:10|max:3000',
        ], [
            'customer_name.required' => 'Моля, въведете име.',
            'customer_name.max' => 'Името трябва да бъде до 120 символа.',
            'customer_phone.required' => 'Моля, въведете телефон.',
            'customer_phone.max' => 'Телефонът трябва да бъде до 40 символа.',
            'customer_email.email' => 'Моля, въведете валиден имейл адрес.',
            'city.required' => 'Моля, въведете град.',
            'city.max' => 'Градът трябва да бъде до 120 символа.',
            'message.required' => 'Моля, опишете от каква услуга имате нужда.',
            'message.min' => 'Описанието трябва да бъде поне 10 символа.',
            'message.max' => 'Описанието трябва да бъде до 3000 символа.',
        ]);

        $payload = [
            'customer_id' => $request->user()?->isCustomer() ? $request->user()->id : null,
            'name' => $validated['customer_name'],
            'phone' => $validated['customer_phone'],
            'email' => $validated['customer_email'] ?? null,
            'city' => $validated['city'],
            'category' => $user->business_category ?: collect($user->serviceCategories())->first(),
            'service' => 'Заявка от профил на бизнес',
            'description' => $validated['message'],
            'urgency' => ServiceRequest::URGENCY_NORMAL,
            'budget' => null,
            'assigned_business_id' => $user->id,
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_BUSINESS_PROFILE,
        ];

        $serviceRequest = ServiceRequest::create(
            collect($payload)
                ->filter(fn ($value, $column) => Schema::hasColumn('service_requests', $column))
                ->all()
        );

        $assignment = null;

        if (Schema::hasTable('service_request_assignments')) {
            $assignment = ServiceRequestAssignment::query()->create([
                'service_request_id' => $serviceRequest->id,
                'business_id' => $user->id,
                'status' => ServiceRequestAssignment::STATUS_SENT,
                'sent_at' => now(),
            ]);
        }

        $this->sendRequestEmails($serviceRequest, $assignment, $user);

        return redirect()
            ->route('businesses.show', $user)
            ->with('service_request_success', 'Заявката е изпратена успешно. Бизнесят ще се свърже с вас възможно най-скоро.');
    }

    public function index(): View
    {
        $business = $this->businessUser();
        $business->ensureOfferPointsInitialized();

        $requestRelations = ['assignedBusiness', 'assignments.business'];

        if (Schema::hasTable('service_request_photos')) {
            $requestRelations[] = 'photos';
        }

        if (Schema::hasTable('service_request_offers')) {
            $requestRelations[] = 'offers';
        }

        $serviceRequests = ServiceRequest::query()
            ->with($requestRelations)
            ->where(function ($query) use ($business) {
                $query
                    ->where('assigned_business_id', $business->id)
                    ->orWhereHas('assignments', fn ($assignmentQuery) => $assignmentQuery->where('business_id', $business->id));
            })
            ->latest()
            ->paginate(20);

        $hasRequestBasedCategories = $business->hasRequestBasedCategories();
        $availableServiceRequests = collect();
        $sentOffers = collect();
        $acceptedOffers = collect();

        if ($hasRequestBasedCategories && $business->isPubliclyVisible() && Schema::hasTable('service_requests')) {
            $availableServiceRequests = ServiceRequest::query()
                ->with($requestRelations)
                ->open()
                ->where('source', ServiceRequest::SOURCE_OFFER_FORM)
                ->latest()
                ->take(80)
                ->get()
                ->filter(fn (ServiceRequest $serviceRequest) => CategoryCatalog::businessMatchesRequest($business, $serviceRequest->category, $serviceRequest->city))
                ->filter(function (ServiceRequest $serviceRequest) use ($business) {
                    if (!Schema::hasTable('service_request_offers')) {
                        return true;
                    }

                    return !$serviceRequest->offers->contains('business_id', $business->id);
                })
                ->take(20)
                ->values();
        }

        if (Schema::hasTable('service_request_offers')) {
            $sentOffers = ServiceRequestOffer::query()
                ->with('serviceRequest')
                ->where('business_id', $business->id)
                ->latest()
                ->take(20)
                ->get();

            $acceptedOffers = ServiceRequestOffer::query()
                ->with('serviceRequest')
                ->where('business_id', $business->id)
                ->where('status', ServiceRequestOffer::STATUS_ACCEPTED)
                ->latest()
                ->take(20)
                ->get();
        }

        $offerPoints = [
            'balance' => $business->offerPointsBalance(),
            'remaining_offers' => $business->remainingOfferCount(),
            'cost' => ServiceRequestOffer::POINTS_COST,
            'included' => $business->includedMonthlyOfferPoints(),
        ];

        return view('business.service-requests', compact(
            'business',
            'serviceRequests',
            'hasRequestBasedCategories',
            'availableServiceRequests',
            'sentOffers',
            'acceptedOffers',
            'offerPoints'
        ));
    }

    public function contacted(ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->updateOwnedRequest($serviceRequest, 'contacted');

        return redirect()
            ->route('business.service-requests.index')
            ->with('success', 'Заявката е маркирана като свързана.');
    }

    public function completed(ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->updateOwnedRequest($serviceRequest, 'completed');

        return redirect()
            ->route('business.service-requests.index')
            ->with('success', 'Заявката е маркирана като завършена.');
    }

    public function cancelled(ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->updateOwnedRequest($serviceRequest, 'cancelled');

        return redirect()
            ->route('business.service-requests.index')
            ->with('success', 'Заявката е отказана.');
    }

    private function updateOwnedRequest(ServiceRequest $serviceRequest, string $status): void
    {
        $business = $this->businessUser();
        $assignment = $this->assignmentFor($serviceRequest, $business);

        abort_unless(
            (int) $serviceRequest->assigned_business_id === (int) $business->id || $assignment,
            403
        );

        match ($status) {
            'contacted' => $serviceRequest->markContacted(),
            'completed' => $serviceRequest->markCompleted(),
            'cancelled' => $serviceRequest->markCancelled(),
        };

        if ($assignment) {
            match ($status) {
                'contacted' => $assignment->markContacted(),
                'completed' => $assignment->markCompleted(),
                'cancelled' => $assignment->markCancelled(),
            };
        }
    }

    private function assignmentFor(ServiceRequest $serviceRequest, User $business): ?ServiceRequestAssignment
    {
        if (!Schema::hasTable('service_request_assignments')) {
            return null;
        }

        return ServiceRequestAssignment::query()
            ->where('service_request_id', $serviceRequest->id)
            ->where('business_id', $business->id)
            ->first();
    }

    private function businessUser(): User
    {
        abort_unless(auth()->check() && auth()->user()->role === 'business', 403);

        return auth()->user();
    }

    private function sendRequestEmails(ServiceRequest $serviceRequest, ?ServiceRequestAssignment $assignment, User $business): void
    {
        try {
            if ($assignment && filled($business->email)) {
                Mail::to($business->email)->send(new NewServiceRequestBusinessMail($assignment));
            }

            if (filled($serviceRequest->email)) {
                Mail::to($serviceRequest->email)->send(new CustomerServiceRequestConfirmationMail($serviceRequest));
            }
        } catch (Throwable $exception) {
            Log::warning('BON business profile service request email failed.', [
                'service_request_id' => $serviceRequest->id,
                'business_id' => $business->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
