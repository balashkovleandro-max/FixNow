<?php

namespace App\Support;

use App\Mail\NewServiceRequestAdminMail;
use App\Mail\NewServiceRequestBusinessMail;
use App\Mail\ServiceRequestAssignmentContactedAdminMail;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAssignment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ServiceRequestNotificationSender
{
    public function newServiceRequest(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->loadMissing(['assignments.business']);

        $this->adminRecipients()->each(function (string $email) use ($serviceRequest) {
            $this->sendSafely(
                fn () => Mail::to($email)->send(new NewServiceRequestAdminMail($serviceRequest)),
                'admin new service request email',
                ['service_request_id' => $serviceRequest->id, 'email' => $email]
            );
        });

        $serviceRequest->assignments
            ->filter(fn (ServiceRequestAssignment $assignment) => filled($assignment->business?->email))
            ->each(function (ServiceRequestAssignment $assignment) use ($serviceRequest) {
                $this->sendSafely(
                    fn () => Mail::to($assignment->business->email)->send(new NewServiceRequestBusinessMail($assignment)),
                    'business new service request email',
                    [
                        'service_request_id' => $serviceRequest->id,
                        'assignment_id' => $assignment->id,
                        'business_id' => $assignment->business_id,
                    ]
                );
            });
    }

    public function assignmentContacted(ServiceRequestAssignment $assignment): void
    {
        $assignment->loadMissing(['serviceRequest', 'business']);

        $this->adminRecipients()->each(function (string $email) use ($assignment) {
            $this->sendSafely(
                fn () => Mail::to($email)->send(new ServiceRequestAssignmentContactedAdminMail($assignment)),
                'admin assignment contacted email',
                ['assignment_id' => $assignment->id, 'email' => $email]
            );
        });
    }

    protected function adminRecipients(): Collection
    {
        if (
            !Schema::hasTable('users')
            || !Schema::hasColumn('users', 'role')
            || !Schema::hasColumn('users', 'email')
        ) {
            return collect();
        }

        return User::query()
            ->where('role', 'admin')
            ->whereNotNull('email')
            ->pluck('email')
            ->filter()
            ->unique()
            ->values();
    }

    private function sendSafely(callable $send, string $context, array $meta = []): void
    {
        try {
            $send();
        } catch (Throwable $exception) {
            Log::warning("BON mail failed: {$context}", array_merge($meta, [
                'exception' => $exception->getMessage(),
            ]));
        }
    }
}
