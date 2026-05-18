<?php

namespace App\Http\Controllers;

use App\Mail\NewServiceRequestOfferCustomerMail;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use App\Support\CategoryCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ServiceRequestOfferController extends Controller
{
    public function store(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $business = auth()->user();

        abort_unless($business && $business->role === 'business', 403);
        abort_unless($business->isPubliclyVisible(), 403);
        abort_unless($serviceRequest->isOpenForOffers(), 403);
        abort_unless(CategoryCatalog::businessMatchesRequest($business, $serviceRequest->category, $serviceRequest->city), 403);

        if (!Schema::hasTable('service_request_offers') || !Schema::hasColumn('users', 'offer_points_balance')) {
            return back()->withErrors([
                'offer' => 'Системата за оферти още не е активирана. Моля, пуснете migrations.',
            ]);
        }

        $business->ensureOfferPointsInitialized();

        if (!$business->canSpendOfferPoints(ServiceRequestOffer::POINTS_COST)) {
            return back()->withErrors([
                'offer' => 'Нямате достатъчно точки за изпращане на оферта.',
            ]);
        }

        $alreadySent = ServiceRequestOffer::query()
            ->where('service_request_id', $serviceRequest->id)
            ->where('business_id', $business->id)
            ->exists();

        if ($alreadySent) {
            return back()->withErrors([
                'offer' => 'Вече сте изпратили оферта към тази заявка.',
            ]);
        }

        $validated = $request->validate([
            'price_estimate' => 'required|string|max:120',
            'timeframe' => 'required|string|max:120',
            'message' => 'required|string|min:10|max:3000',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
        ], [
            'price_estimate.required' => 'Моля, въведете ориентировъчна цена.',
            'timeframe.required' => 'Моля, въведете срок за изпълнение.',
            'message.required' => 'Моля, напишете кратко съобщение към клиента.',
            'message.min' => 'Съобщението трябва да бъде поне 10 символа.',
            'phone.required' => 'Моля, въведете телефон за контакт.',
            'email.email' => 'Моля, въведете валиден имейл адрес.',
        ]);

        $offer = ServiceRequestOffer::create([
            'service_request_id' => $serviceRequest->id,
            'business_id' => $business->id,
            'price_estimate' => $validated['price_estimate'],
            'timeframe' => $validated['timeframe'],
            'message' => $validated['message'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'status' => ServiceRequestOffer::STATUS_SENT,
            'points_spent' => ServiceRequestOffer::POINTS_COST,
        ]);

        $business->spendOfferPoints(ServiceRequestOffer::POINTS_COST);
        $this->sendCustomerOfferEmail($serviceRequest, $offer);

        return redirect()
            ->route('business.service-requests.index')
            ->with('success', 'Офертата е изпратена успешно. От баланса ви са отнети 3 точки.');
    }

    private function sendCustomerOfferEmail(ServiceRequest $serviceRequest, ServiceRequestOffer $offer): void
    {
        if (!$serviceRequest->isOpenForOffers()) {
            return;
        }

        $serviceRequest->loadMissing('customer');
        $recipient = $serviceRequest->email ?: $serviceRequest->customer?->email;

        if (blank($recipient)) {
            return;
        }

        try {
            Mail::to($recipient)->send(new NewServiceRequestOfferCustomerMail($offer->fresh(['serviceRequest.customer', 'business'])));
        } catch (Throwable $exception) {
            Log::warning('FixNow new offer customer email failed.', [
                'offer_id' => $offer->id,
                'service_request_id' => $serviceRequest->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
