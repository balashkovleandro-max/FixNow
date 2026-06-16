<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceRequestOfferNotSelectedBusinessMail extends Mailable
{
    use Queueable, SerializesModels;

    public ServiceRequestOffer $offer;
    public ServiceRequest $serviceRequest;
    public User $executor;
    public string $dashboardUrl;

    public function __construct(ServiceRequestOffer $offer)
    {
        $this->offer = $offer->loadMissing(['serviceRequest', 'business']);
        $this->serviceRequest = $this->offer->serviceRequest;
        $this->executor = $this->offer->business;
        $this->dashboardUrl = route('business.service-requests.index');
    }

    public function build()
    {
        return $this
            ->subject('Заявката беше затворена във BON')
            ->view('emails.service-requests.offer-not-selected-business');
    }
}
