<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewServiceRequestAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public ServiceRequest $serviceRequest;
    public $assignments;
    public string $dashboardUrl;

    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest->loadMissing(['assignments.business']);
        $this->assignments = $this->serviceRequest->assignments;
        $this->dashboardUrl = route('admin.service-requests.index');
    }

    public function build()
    {
        return $this
            ->subject('Нова заявка за оферта във FixNow.bg')
            ->view('emails.service-requests.admin-new');
    }
}
