<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminServiceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin();

        $status = $request->query('status', 'all');
        $allowedStatuses = [
            'new' => ServiceRequest::STATUS_NEW,
            'contacted' => ServiceRequest::STATUS_CONTACTED,
            'completed' => ServiceRequest::STATUS_COMPLETED,
            'cancelled' => ServiceRequest::STATUS_CANCELLED,
        ];

        $serviceRequests = ServiceRequest::query()
            ->with(['assignedBusiness', 'assignments.business', 'offers.business', 'selectedOffer.business', 'customer'])
            ->when(
                isset($allowedStatuses[$status]),
                fn ($query) => $query->where('status', $allowedStatuses[$status])
            )
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.service-requests', compact('serviceRequests', 'status'));
    }

    public function markContacted(ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorizeAdmin();

        $serviceRequest->markContacted();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Заявката беше маркирана като contacted.');
    }

    public function markClosed(ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorizeAdmin();

        $serviceRequest->markClosed();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Заявката беше маркирана като closed.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
    }
}
