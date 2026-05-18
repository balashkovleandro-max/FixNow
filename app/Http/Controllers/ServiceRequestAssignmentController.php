<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequestAssignment;
use App\Support\ServiceRequestNotificationSender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ServiceRequestAssignmentController extends Controller
{
    public function contacted(ServiceRequestAssignment $assignment, ServiceRequestNotificationSender $notifications): RedirectResponse
    {
        $this->authorizeBusinessAssignment($assignment);

        $assignment->loadMissing('serviceRequest');
        $assignment->markContacted();

        try {
            $notifications->assignmentContacted($assignment);
        } catch (Throwable $exception) {
            Log::warning('FixNow assignment contacted notification sender failed.', [
                'assignment_id' => $assignment->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Маркирахте, че сте се свързали с клиента.');
    }

    public function declined(ServiceRequestAssignment $assignment): RedirectResponse
    {
        $this->authorizeBusinessAssignment($assignment);

        $assignment->markDeclined();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Заявката беше маркирана като отказана.');
    }

    public function closed(ServiceRequestAssignment $assignment): RedirectResponse
    {
        $this->authorizeBusinessAssignment($assignment);

        $assignment->markClosed();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Заявката беше затворена за вашия бизнес.');
    }

    private function authorizeBusinessAssignment(ServiceRequestAssignment $assignment): void
    {
        abort_unless(
            auth()->check()
            && auth()->user()->role === 'business'
            && (int) $assignment->business_id === (int) auth()->id(),
            403
        );
    }
}
