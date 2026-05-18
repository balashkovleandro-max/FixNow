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

    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest->loadMissing('assignedBusiness');
        $this->homeUrl = url('/');
    }

    public function build()
    {
        return $this
            ->subject('Получихме заявката ви във FixNow.bg')
            ->view('emails.service-requests.customer-confirmation');
    }
}
