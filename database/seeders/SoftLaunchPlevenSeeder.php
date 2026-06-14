<?php

namespace Database\Seeders;

use App\Models\BusinessPhoto;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SoftLaunchPlevenSeeder extends Seeder
{
    public function run(): void
    {
        $executors = collect([
            [
                'email' => 'pleven.remonti@bon.test',
                'name' => 'Иван Петров',
                'business_name' => 'Плевен Ремонт Про',
                'business_category' => 'Ремонти и строителство',
                'phone' => '+359 88 000 2101',
                'address' => 'Плевен, район Център',
                'working_hours' => 'Понеделник - събота, 08:30 - 18:30',
                'short_description' => 'Ремонти, довършителни работи, бани, шпакловка и боядисване в Плевен.',
                'description' => 'Профил за soft launch тест на изпълнител в Плевен. Предлага оглед, ясна оферта и изпълнение на ремонтни дейности за домове и малки обекти.',
                'service_areas' => 'Плевен, Сторгозия, Дружба, Кайлъка',
                'service_cities' => ['Плевен'],
                'service_categories' => ['Ремонти и строителство'],
                'payment_methods' => 'В брой, банков превод, фактура',
                'years_experience' => '12 години опит',
                'emergency_services' => false,
                'works_24_7' => false,
                'response_time_label' => 'Отговаря в рамките на деня',
                'subscription_plan' => 'premium',
                'subscription_status' => 'active',
                'is_verified' => true,
                'photo' => 'demo/repair-tools.svg',
                'offer_points_balance' => 90,
                'services' => [
                    ['title' => 'Ремонт на баня', 'category' => 'Ремонти и строителство', 'price' => 1200],
                    ['title' => 'Шпакловка и боядисване', 'category' => 'Ремонти и строителство', 'price' => 280],
                ],
            ],
            [
                'email' => 'pleven.vik@bon.test',
                'name' => 'Георги Николов',
                'business_name' => 'Домашни услуги Плевен',
                'business_category' => 'Домашни услуги',
                'phone' => '+359 88 000 2102',
                'address' => 'Плевен, ж.к. Сторгозия',
                'working_hours' => 'Всеки ден, 08:00 - 20:00',
                'short_description' => 'Домашни ремонти, поддръжка и аварийни посещения.',
                'description' => 'Контролиран soft-launch профил за домашни услуги в Плевен. Подходящ за проверка на заявки, matching по град и категория и публични профили.',
                'service_areas' => 'Плевен, Долна Митрополия, Буковлък',
                'service_cities' => ['Плевен', 'Долна Митрополия'],
                'service_categories' => ['Домашни услуги'],
                'payment_methods' => 'В брой, банков превод',
                'years_experience' => '9 години опит',
                'emergency_services' => true,
                'works_24_7' => true,
                'response_time_label' => 'Спешна реакция',
                'subscription_plan' => 'standard',
                'subscription_status' => 'active',
                'is_verified' => true,
                'photo' => 'demo/plumbing.svg',
                'offer_points_balance' => 30,
                'services' => [
                    ['title' => 'Отстраняване на теч', 'category' => 'Домашни услуги', 'price' => 45],
                    ['title' => 'Монтаж на бойлер', 'category' => 'Домашни услуги', 'price' => 85],
                ],
            ],
            [
                'email' => 'pleven.elektro@bon.test',
                'name' => 'Димитър Илиев',
                'business_name' => 'Електро Профил Плевен',
                'business_category' => 'Домашни услуги',
                'phone' => '+359 88 000 2103',
                'address' => 'Плевен, ж.к. Дружба',
                'working_hours' => 'Понеделник - петък, 09:00 - 18:00',
                'short_description' => 'Електро табла, контакти, осветление, диагностика и аварийни ремонти.',
                'description' => 'Soft-launch изпълнител за електроуслуги. Профилът е попълнен с категории, град, телефон, описание и снимка за реалистичен публичен изглед.',
                'service_areas' => 'Плевен, Левски, Пордим',
                'service_cities' => ['Плевен', 'Левски'],
                'service_categories' => ['Домашни услуги'],
                'payment_methods' => 'В брой, фактура',
                'years_experience' => '15 години опит',
                'emergency_services' => true,
                'works_24_7' => false,
                'response_time_label' => 'Отговаря бързо',
                'subscription_plan' => 'premium',
                'subscription_status' => 'active',
                'is_verified' => true,
                'photo' => 'demo/electric-service.svg',
                'offer_points_balance' => 90,
                'services' => [
                    ['title' => 'Смяна на електро табло', 'category' => 'Домашни услуги', 'price' => 180],
                    ['title' => 'Монтаж на осветление', 'category' => 'Домашни услуги', 'price' => 60],
                ],
            ],
            [
                'email' => 'pleven.clean@bon.test',
                'name' => 'Мария Стоянова',
                'business_name' => 'Чист Дом Плевен',
                'business_category' => 'Почистване',
                'phone' => '+359 88 000 2104',
                'address' => 'Плевен, район Дружба',
                'working_hours' => 'Понеделник - събота, 08:00 - 17:00',
                'short_description' => 'Почистване на домове, офиси, входове и след ремонт.',
                'description' => 'Soft-launch профил за почистване в Плевен с ясно описание, телефон и снимка. Използва се за реалистичен публичен профил.',
                'service_areas' => 'Плевен, Кайлъка, Сторгозия',
                'service_cities' => ['Плевен'],
                'service_categories' => ['Почистване'],
                'payment_methods' => 'В брой, банков превод',
                'years_experience' => '6 години опит',
                'emergency_services' => false,
                'works_24_7' => false,
                'response_time_label' => 'Отговаря в рамките на деня',
                'subscription_plan' => 'standard',
                'subscription_status' => 'trialing',
                'is_verified' => false,
                'photo' => 'demo/cleaning.svg',
                'offer_points_balance' => 45,
                'services' => [
                    ['title' => 'Почистване след ремонт', 'category' => 'Почистване', 'price' => 120],
                    ['title' => 'Абонаментно почистване', 'category' => 'Почистване', 'price' => 80],
                ],
            ],
            [
                'email' => 'pleven.auto@bon.test',
                'name' => 'Николай Георгиев',
                'business_name' => 'Авто Сервиз Север',
                'business_category' => 'Автосервизи',
                'phone' => '+359 88 000 2105',
                'address' => 'Плевен, Северна индустриална зона',
                'working_hours' => 'Понеделник - събота, 08:30 - 19:00',
                'short_description' => 'Диагностика, масла, ходова част, гуми и поддръжка на автомобили.',
                'description' => 'Контролиран профил за автоуслуги в Плевен. Използва се за проверка на профили с директен контакт, публични карти и Premium/Verified badges.',
                'service_areas' => 'Плевен, Долни Дъбник, Гривица',
                'service_cities' => ['Плевен', 'Долни Дъбник'],
                'service_categories' => ['Автосервизи'],
                'payment_methods' => 'В брой, карта, фактура',
                'years_experience' => '10 години опит',
                'emergency_services' => false,
                'works_24_7' => false,
                'response_time_label' => 'Отговаря бързо',
                'subscription_plan' => 'premium',
                'subscription_status' => 'active',
                'is_verified' => false,
                'photo' => 'demo/auto-service.svg',
                'offer_points_balance' => 90,
                'services' => [
                    ['title' => 'Компютърна диагностика', 'category' => 'Автосервизи', 'price' => 35],
                    ['title' => 'Смяна на масло и филтри', 'category' => 'Автосервизи', 'price' => 65],
                ],
            ],
            [
                'email' => 'pleven.clima@bon.test',
                'name' => 'Петър Димов',
                'business_name' => 'Клима Сервиз Плевен',
                'business_category' => 'Домашни услуги',
                'phone' => '+359 88 000 2106',
                'address' => 'Плевен, район Кайлъка',
                'working_hours' => 'Понеделник - петък, 09:00 - 18:00',
                'short_description' => 'Монтаж, профилактика и ремонт на климатици и отоплителни системи.',
                'description' => 'Допълнителен soft-launch профил за поддръжка на домове и имоти. Попълнен е достатъчно за висок profile completeness.',
                'service_areas' => 'Плевен, Белене, Червен бряг',
                'service_cities' => ['Плевен', 'Червен бряг'],
                'service_categories' => ['Домашни услуги'],
                'payment_methods' => 'В брой, банков превод',
                'years_experience' => '8 години опит',
                'emergency_services' => true,
                'works_24_7' => false,
                'response_time_label' => 'Отговаря до 1 час',
                'subscription_plan' => 'standard',
                'subscription_status' => 'active',
                'is_verified' => false,
                'photo' => 'demo/electric-service.svg',
                'offer_points_balance' => 30,
                'services' => [
                    ['title' => 'Профилактика на климатик', 'category' => 'Домашни услуги', 'price' => 55],
                    ['title' => 'Монтаж на климатик', 'category' => 'Домашни услуги', 'price' => 180],
                ],
            ],
        ]);

        $executors->each(fn (array $payload) => $this->seedExecutor($payload));
    }

    private function seedExecutor(array $payload): User
    {
        $services = $payload['services'] ?? [];
        $photo = $payload['photo'] ?? null;

        unset($payload['services'], $payload['photo']);

        $subscriptionStatus = $payload['subscription_status'] ?? 'active';
        $isVerified = (bool) ($payload['is_verified'] ?? false);

        $business = User::query()->firstOrNew(['email' => $payload['email']]);
        $business->forceFill(array_merge([
            'role' => 'business',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'city' => 'Плевен',
            'subscription_started_at' => in_array($subscriptionStatus, ['active', 'trialing'], true) ? now()->subDays(7) : null,
            'subscription_ends_at' => in_array($subscriptionStatus, ['active', 'trialing'], true) ? now()->addDays(30) : null,
            'trial_started_at' => null,
            'trial_ends_at' => null,
            'verified_at' => $isVerified ? now()->subDays(2) : null,
            'cancelled_at' => null,
            'extra_city_addon_count' => 0,
            'offer_points_initialized_at' => now(),
        ], $payload))->save();

        foreach ($services as $service) {
            Service::query()->updateOrCreate(
                [
                    'user_id' => $business->id,
                    'title' => $service['title'],
                ],
                [
                    'category' => $service['category'],
                    'city' => 'Плевен',
                    'description' => $service['description'] ?? 'Soft-launch услуга за Плевен с реалистично описание и ориентировъчна цена.',
                    'price' => $service['price'] ?? null,
                    'phone' => $business->phone,
                    'image' => $photo,
                ]
            );
        }

        if ($photo && Schema::hasTable('business_photos')) {
            BusinessPhoto::query()->updateOrCreate(
                [
                    'business_id' => $business->id,
                    'path' => $photo,
                ],
                [
                    'original_name' => basename($photo),
                    'alt_text' => $business->business_name,
                    'sort_order' => 0,
                ]
            );
        }

        return $business->fresh(['services', 'businessPhotos']);
    }
}
