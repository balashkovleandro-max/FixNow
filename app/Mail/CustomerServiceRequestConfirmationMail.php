<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerServiceRequestConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public ServiceRequest $serviceRequest;
    public string $homeUrl;
    public string $offersUrl;

    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest->loadMissing('assignedBusiness');
        $this->homeUrl = url('/');
        $token = $this->serviceRequest->ensurePublicToken();
        $this->offersUrl = $token
            ? route('service-requests.offers.show', ['serviceRequest' => $token])
            : $this->homeUrl;
    }

    public function build()
    {
        return $this
            ->subject('Получихме заявката ви във BON')
            ->view('emails.service-requests.customer-confirmation');
    }
}
