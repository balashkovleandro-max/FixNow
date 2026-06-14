<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestPhoto;
use App\Support\CategoryCatalog;
use App\Support\ServiceRequestMatcher;
use App\Support\ServiceRequestNotificationSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ServiceRequestController extends Controller
{
    public function create()
    {
        $requestCategories = CategoryCatalog::requestBased();

        return view('request-service', compact('requestCategories'));
    }

    public function store(Request $request, ServiceRequestMatcher $matcher, ServiceRequestNotificationSender $notifications)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'city' => 'required|string|max:255',
            'category' => [
                'required',
                'string',
                'max:255',
                fn ($attribute, $value, $fail) => CategoryCatalog::acceptsRequests($value)
                    ? null
                    : $fail('Моля, изберете категория, която приема заявки за оферти.'),
            ],
            'service' => 'nullable|string|max:255',
            'description' => 'required|string|max:5000',
            'urgency' => 'nullable|in:normal,urgent,this_week,this_month,no_deadline',
            'budget' => 'nullable|string|max:255',
            'image' => 'nullable|file|image|mimetypes:image/jpeg,image/png,image/webp|mimes:jpg,jpeg,png,webp|max:2048',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'nullable|file|image|mimetypes:image/jpeg,image/png,image/webp|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'name.required' => 'Моля, въведете име.',
            'phone.required' => 'Моля, въведете телефон.',
            'email.email' => 'Моля, въведете валиден имейл адрес.',
            'city.required' => 'Моля, въведете град.',
            'category.required' => 'Моля, изберете категория/услуга.',
            'description.required' => 'Моля, опишете от каква услуга имате нужда.',
            'urgency.in' => 'Моля, изберете валиден срок/спешност.',
            'image.image' => 'Снимката трябва да бъде JPG, PNG или WEBP и да е до позволения размер.',
            'image.mimes' => 'Снимката трябва да бъде JPG, PNG или WEBP и да е до позволения размер.',
            'image.max' => 'Снимката трябва да бъде JPG, PNG или WEBP и да е до позволения размер.',
            'photos.max' => 'Можете да добавите до 5 снимки към заявката.',
            'photos.*.image' => 'Снимките трябва да бъдат JPG, PNG или WEBP.',
            'photos.*.mimes' => 'Снимките трябва да бъдат JPG, PNG или WEBP.',
            'photos.*.max' => 'Всяка снимка трябва да бъде до 4MB.',
        ]);

        $photoFiles = collect($request->file('photos', []))->filter();
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('service-requests', 'public');
        }

        $payload = [
            'customer_id' => $request->user()?->isCustomer() ? $request->user()->id : null,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'city' => $validated['city'],
            'category' => $validated['category'],
            'service' => $validated['service'] ?? null,
            'description' => $validated['description'],
            'urgency' => $validated['urgency'] ?? ServiceRequest::URGENCY_NO_DEADLINE,
            'budget' => $validated['budget'] ?? null,
            'assigned_business_id' => null,
            'image' => $imagePath,
            'status' => ServiceRequest::STATUS_NEW,
            'source' => ServiceRequest::SOURCE_OFFER_FORM,
        ];

        $serviceRequest = ServiceRequest::create(
            collect($payload)
                ->filter(fn ($value, $column) => Schema::hasColumn('service_requests', $column))
                ->all()
        );

        if (Schema::hasTable('service_request_photos') && $photoFiles->isNotEmpty()) {
            $sortOrder = 0;

            foreach ($photoFiles as $photo) {
                $path = $photo->store('service-requests', 'public');

                if (!$imagePath) {
                    $imagePath = $path;
                    $serviceRequest->forceFill(['image' => $imagePath])->save();
                }

                ServiceRequestPhoto::create([
                    'service_request_id' => $serviceRequest->id,
                    'path' => $path,
                    'original_name' => $photo->getClientOriginalName(),
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        $matcher->assign($serviceRequest);

        try {
            $notifications->newServiceRequest($serviceRequest);
        } catch (Throwable $exception) {
            Log::warning('BON service request notification sender failed.', [
                'service_request_id' => $serviceRequest->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('request.service')
            ->with('success', 'Заявката ви е изпратена успешно. Подходящи бизнеси ще могат да ви изпратят оферта.')
            ->with('offers_url', $serviceRequest->public_token ? route('service-requests.offers.show', ['serviceRequest' => $serviceRequest->public_token]) : null)
            ->with('bon_event', [
                'name' => 'service_request_submit',
                'params' => [
                    'source' => 'request_form',
                    'request_id' => $serviceRequest->id,
                    'category' => $serviceRequest->category,
                ],
            ]);
    }
}
