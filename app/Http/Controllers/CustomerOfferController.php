<?php

namespace App\Http\Controllers;

use App\Mail\AcceptedOfferExecutorMail;
use App\Mail\ServiceRequestOfferNotSelectedBusinessMail;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class CustomerOfferController extends Controller
{
    public function accept(ServiceRequestOffer $offer): RedirectResponse
    {
        $customer = auth()->user();

        abort_unless($customer && $customer->isCustomer(), 403);

        $offer->loadMissing(['serviceRequest.offers', 'business']);
        $serviceRequest = $offer->serviceRequest;

        abort_unless($serviceRequest && $serviceRequest->belongsToCustomer($customer), 403);
        if ($serviceRequest->selected_offer_id || in_array($serviceRequest->status, [
            ServiceRequest::STATUS_IN_PROGRESS,
            ServiceRequest::STATUS_COMPLETED,
            ServiceRequest::STATUS_CLOSED,
            ServiceRequest::STATUS_CANCELLED,
        ], true)) {
            return redirect()
                ->route('dashboard')
                ->withErrors([
                    'offer' => 'Към тази заявка вече има избрана оферта или заявката е приключена.',
                ]);
        }

        abort_unless(in_array($offer->status, [
            ServiceRequestOffer::STATUS_SENT,
            ServiceRequestOffer::STATUS_VIEWED,
        ], true), 403);

        DB::transaction(function () use ($offer, $serviceRequest) {
            ServiceRequestOffer::query()
                ->where('service_request_id', $serviceRequest->id)
                ->whereKeyNot($offer->id)
                ->whereIn('status', [ServiceRequestOffer::STATUS_SENT, ServiceRequestOffer::STATUS_VIEWED])
                ->update(['status' => ServiceRequestOffer::STATUS_NOT_SELECTED]);

            $offer->forceFill([
                'status' => ServiceRequestOffer::STATUS_ACCEPTED,
            ])->save();

            $payload = [
                'assigned_business_id' => $offer->business_id,
                'status' => ServiceRequest::STATUS_IN_PROGRESS,
            ];

            if (Schema::hasColumn('service_requests', 'selected_offer_id')) {
                $payload['selected_offer_id'] = $offer->id;
            }

            if (Schema::hasColumn('service_requests', 'accepted_offer_at')) {
                $payload['accepted_offer_at'] = now();
            }

            $serviceRequest->forceFill($payload)->save();
        });

        try {
            if (filled($offer->business?->email)) {
                Mail::to($offer->business->email)->send(new AcceptedOfferExecutorMail($offer->fresh(['serviceRequest', 'business'])));
            }

            $this->sendNotSelectedOfferEmails($serviceRequest, $offer);
        } catch (Throwable $exception) {
            Log::warning('BON accepted offer email failed.', [
                'offer_id' => $offer->id,
                'service_request_id' => $serviceRequest->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Офертата е приета успешно. Избраният бизнес ще бъде уведомен.');
    }

    private function sendNotSelectedOfferEmails(ServiceRequest $serviceRequest, ServiceRequestOffer $acceptedOffer): void
    {
        ServiceRequestOffer::query()
            ->with(['serviceRequest', 'business'])
            ->where('service_request_id', $serviceRequest->id)
            ->whereKeyNot($acceptedOffer->id)
            ->where('status', ServiceRequestOffer::STATUS_NOT_SELECTED)
            ->get()
            ->each(function (ServiceRequestOffer $notSelectedOffer) {
                if (blank($notSelectedOffer->business?->email)) {
                    return;
                }

                Mail::to($notSelectedOffer->business->email)->send(new ServiceRequestOfferNotSelectedBusinessMail($notSelectedOffer));
            });
    }
}
