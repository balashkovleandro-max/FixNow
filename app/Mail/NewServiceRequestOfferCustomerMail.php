<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewServiceRequestOfferCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public ServiceRequestOffer $offer;
    public ServiceRequest $serviceRequest;
    public User $executor;
    public string $offersUrl;

    public function __construct(ServiceRequestOffer $offer)
    {
        $this->offer = $offer->loadMissing(['serviceRequest', 'business']);
        $this->serviceRequest = $this->offer->serviceRequest;
        $this->executor = $this->offer->business;
        $token = $this->serviceRequest->ensurePublicToken();
        $this->offersUrl = route('service-requests.offers.show', ['serviceRequest' => $token]);
    }

    public function build()
    {
        return $this
            ->subject('Получихте нова оферта във BON')
            ->view('emails.service-requests.new-offer-customer');
    }
}
