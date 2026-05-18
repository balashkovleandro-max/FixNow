<?php

namespace App\Http\Controllers;

use App\Mail\AcceptedOfferExecutorMail;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

class ServiceRequestPublicOfferController extends Controller
{
    public function show(ServiceRequest $serviceRequest): View
    {
        abort_if(blank($serviceRequest->public_token), 404);

        $serviceRequest->load([
            'assignedBusiness',
            'offers.business',
            'photos',
            'selectedOffer.business',
        ]);

        return view('service-requests.offers-show', compact('serviceRequest'));
    }

    public function accept(ServiceRequest $serviceRequest, ServiceRequestOffer $offer): RedirectResponse
    {
        abort_if(blank($serviceRequest->public_token), 404);
        abort_unless((int) $offer->service_request_id === (int) $serviceRequest->id, 404);

        $serviceRequest->loadMissing(['offers', 'selectedOffer.business']);
        $offer->loadMissing(['business', 'serviceRequest']);

        $routeParameters = ['serviceRequest' => $serviceRequest->public_token];

        if ($serviceRequest->selected_offer_id || in_array($serviceRequest->status, [
            ServiceRequest::STATUS_IN_PROGRESS,
            ServiceRequest::STATUS_COMPLETED,
            ServiceRequest::STATUS_CLOSED,
            ServiceRequest::STATUS_CANCELLED,
        ], true)) {
            return redirect()
                ->route('service-requests.offers.show', $routeParameters)
                ->withErrors([
                    'offer' => 'Към тази заявка вече има избран изпълнител или заявката е приключена.',
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
        } catch (Throwable $exception) {
            Log::warning('FixNow public accepted offer email failed.', [
                'offer_id' => $offer->id,
                'service_request_id' => $serviceRequest->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('service-requests.offers.show', $routeParameters)
            ->with('success', 'Изпълнителят е избран успешно. Можете да се свържете директно с него.');
    }
}
