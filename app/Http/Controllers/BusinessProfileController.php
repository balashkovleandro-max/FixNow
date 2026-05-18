<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BusinessProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        if (!$user || !$user->isBusiness()) {
            abort(403);
        }

        $relations = ['services'];

        if (Schema::hasTable('business_photos')) {
            $relations[] = 'businessPhotos';
        }

        $user->loadMissing($relations);

        return view('dashboards.edit-business-profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->isBusiness()) {
            abort(403);
        }

        $validated = $request->validate([
            'business_name' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:320'],
            'description' => ['nullable', 'string', 'max:3000'],
            'business_category' => ['nullable', 'string', 'max:255'],
            'service_categories' => ['nullable', 'array'],
            'service_categories.*' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'viber' => ['nullable', 'string', 'max:50'],
            'payment_methods' => ['nullable', 'string', 'max:255'],
            'years_experience' => ['nullable', 'string', 'max:255'],
            'service_areas' => ['nullable', 'string', 'max:1000'],
            'emergency_services' => ['nullable', 'boolean'],
            'works_24_7' => ['nullable', 'boolean'],
            'response_time_label' => ['nullable', 'string', 'max:80'],
            'service_cities' => ['nullable', 'array'],
            'service_cities.*' => ['nullable', 'string', 'max:255'],
            'service_cities_custom' => ['nullable', 'string', 'max:1000'],
            'working_hours_text' => ['nullable', 'string', 'max:500'],
            'start_hour' => ['nullable', 'string'],
            'start_minute' => ['nullable', 'string'],
            'end_hour' => ['nullable', 'string'],
            'end_minute' => ['nullable', 'string'],
        ], [
            'service_cities.array' => 'Моля, изберете валидни градове.',
            'service_cities.*.string' => 'Моля, изберете валидни градове.',
            'service_categories.array' => 'Моля, изберете валидни категории.',
            'service_categories.*.string' => 'Моля, изберете валидни категории.',
            'short_description.max' => 'Краткото описание трябва да бъде до 320 символа.',
            'description.max' => 'Подробното описание трябва да бъде до 3000 символа.',
        ]);

        $customCities = collect(preg_split('/[,;\r\n]+/', $validated['service_cities_custom'] ?? ''))
            ->map(fn ($city) => trim((string) $city))
            ->filter();

        $serviceCities = collect($validated['service_cities'] ?? [])
            ->merge($customCities)
            ->map(fn ($city) => trim((string) $city))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (count($serviceCities) > $user->cityLimit()) {
            $limitMessage = $user->isPremium()
                ? 'Premium включва до ' . $user->cityLimit() . ' града с активния ви абонамент. Намалете обслужваните градове, за да останете в лимита на плана.'
                : 'Standard включва до 2 града с активния ви абонамент. За повече градове преминете към Premium план.';

            return back()
                ->withErrors(['service_cities' => $limitMessage])
                ->withInput();
        }

        $serviceCategories = collect($validated['service_categories'] ?? [])
            ->map(fn ($category) => trim((string) $category))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (count($serviceCategories) > $user->categoryLimit()) {
            return back()
                ->withErrors(['service_categories' => $user->effectivePlanLabel() . ' позволява до ' . $user->categoryLimit() . ' категории/услуги с активния ви абонамент.'])
                ->withInput();
        }

        $workingHours = trim((string) ($validated['working_hours_text'] ?? ''));

        if ($workingHours === '' && $this->hasCompleteWorkingHours($request)) {
            $workingHours =
                $request->start_hour . ':' . $request->start_minute .
                ' - ' .
                $request->end_hour . ':' . $request->end_minute;
        }

        $profileData = [
            'business_name' => $validated['business_name'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'business_category' => $validated['business_category'] ?? null,
            'service_categories' => $serviceCategories ?: null,
            'city' => $validated['city'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'website' => $validated['website'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
            'viber' => $validated['viber'] ?? null,
            'payment_methods' => $validated['payment_methods'] ?? null,
            'years_experience' => $validated['years_experience'] ?? null,
            'service_areas' => $validated['service_areas'] ?? null,
            'emergency_services' => $request->boolean('emergency_services'),
            'works_24_7' => $request->boolean('works_24_7'),
            'response_time_label' => $validated['response_time_label'] ?? null,
            'service_cities' => $serviceCities ?: null,
            'working_hours' => $workingHours !== '' ? $workingHours : null,
        ];

        $legacyMirrors = [
            'адрес' => $profileData['address'],
            'уебсайт' => $profileData['website'],
            'фейсбук' => $profileData['facebook'],
            'инстаграм' => $profileData['instagram'],
            'обслужвани_райони' => $profileData['service_areas'],
            'години_опит' => $profileData['years_experience'],
            'работно_време' => $profileData['working_hours'],
            'спешни_услуги' => $profileData['emergency_services'],
            'методи_на_плащане' => $profileData['payment_methods'],
        ];

        $profileData = array_merge($profileData, $legacyMirrors);

        $profileData = collect($profileData)
            ->filter(fn ($value, $column) => Schema::hasColumn('users', $column))
            ->all();

        $user->forceFill($profileData)->save();

        return redirect()
            ->route('business.profile.edit')
            ->with('success', 'Профилът беше обновен успешно.');
    }

    private function hasCompleteWorkingHours(Request $request): bool
    {
        return $request->filled('start_hour')
            && $request->filled('start_minute')
            && $request->filled('end_hour')
            && $request->filled('end_minute');
    }
}
