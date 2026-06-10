<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestAssignment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceRequestAssignmentContactedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public ServiceRequestAssignment $assignment;
    public ServiceRequest $serviceRequest;
    public User $business;
    public string $dashboardUrl;

    public function __construct(ServiceRequestAssignment $assignment)
    {
        $this->assignment = $assignment->loadMissing(['serviceRequest', 'business']);
        $this->serviceRequest = $this->assignment->serviceRequest;
        $this->business = $this->assignment->business;
        $this->dashboardUrl = route('dashboard');
    }

    public function build()
    {
        return $this
            ->subject('Бизнес се свърза с клиент във BON')
            ->view('emails.service-requests.assignment-contacted-admin');
    }
}
