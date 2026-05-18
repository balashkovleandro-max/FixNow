<?php

namespace Database\Seeders;

use App\Models\BusinessAnalyticsEvent;
use App\Models\BusinessPhoto;
use App\Models\BusinessRecommendation;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed realistic local/demo content for development and launch previews.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $admin = $this->user('admin@example.com', [
            'name' => 'FixNow Admin',
            'role' => 'admin',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        $client = $this->user('client@example.com', [
            'name' => 'Demo Client',
            'role' => 'client',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        $businesses = collect([
            $this->business('premium@example.com', [
                'name' => 'Мария Иванова',
                'business_name' => 'Auto Premium Service',
                'business_category' => 'Автосервизи',
                'city' => 'София',
                'address' => 'бул. България 120, София',
                'phone' => '+359 88 800 1001',
                'website' => 'https://example.com/auto-premium',
                'facebook' => 'https://facebook.com/fixnow-demo-auto',
                'instagram' => 'https://instagram.com/fixnow_demo_auto',
                'whatsapp' => '+359888001001',
                'viber' => '+359888001001',
                'working_hours' => 'Понеделник - Събота, 08:30 - 19:00',
                'short_description' => 'Премиум автосервиз за диагностика, гуми, ходова част и обслужване.',
                'description' => 'Auto Premium Service е demo профил за професионален автосервиз с модерна диагностика, ясни услуги, бърз контакт и високо доверие в публичните резултати.',
                'payment_methods' => 'Карта, банков превод, в брой',
                'years_experience' => '12 години опит',
                'emergency_services' => true,
                'works_24_7' => false,
                'response_time_label' => 'Отговаря бързо',
                'service_areas' => 'София, Перник, Банкя',
                'service_cities' => ['София', 'Перник', 'Банкя'],
                'service_categories' => ['Автосервизи', 'Ремонти', 'Диагностика'],
                'subscription_plan' => 'premium',
                'subscription_status' => 'active',
                'subscription_started_at' => now()->subDays(12),
                'subscription_ends_at' => now()->addDays(30),
                'stripe_customer_id' => 'cus_demo_premium',
                'stripe_subscription_id' => 'sub_demo_premium',
                'is_verified' => true,
                'verified_at' => now()->subDays(8),
                'created_at' => now()->subDays(18),
            ], [
                ['title' => 'Компютърна диагностика', 'category' => 'Автосервизи', 'city' => 'София', 'price' => 25, 'image' => 'demo/auto-service.svg'],
                ['title' => 'Смяна на масло и филтри', 'category' => 'Автосервизи', 'city' => 'София', 'price' => 45, 'image' => 'demo/repair-tools.svg'],
                ['title' => 'Проверка на ходова част', 'category' => 'Ремонти', 'city' => 'Перник', 'price' => 35, 'image' => 'demo/electric-service.svg'],
            ]),

            $this->business('business@example.com', [
                'name' => 'Demo Business Owner',
                'business_name' => 'Нов бизнес профил',
                'business_category' => null,
                'city' => 'Плевен',
                'address' => null,
                'phone' => null,
                'website' => null,
                'working_hours' => null,
                'short_description' => null,
                'description' => null,
                'service_areas' => null,
                'service_cities' => ['Плевен'],
                'service_categories' => [],
                'subscription_plan' => 'standard',
                'subscription_status' => 'active',
                'subscription_started_at' => now()->subDays(2),
                'subscription_ends_at' => now()->addDays(28),
                'is_verified' => false,
                'verified_at' => null,
                'created_at' => now()->subDay(),
            ]),

            $this->business('demo.vik@fixnow.test', [
                'name' => 'Георги Петров',
                'business_name' => 'VIK Express Pleven',
                'business_category' => 'ВиК услуги',
                'city' => 'Плевен',
                'address' => 'ж.к. Сторгозия, Плевен',
                'phone' => '+359 88 800 1002',
                'website' => 'https://example.com/vik-express',
                'working_hours' => 'Всеки ден, 08:00 - 20:00',
                'short_description' => 'Бързи ВиК ремонти, течове, бойлери и аварийни посещения в Плевен.',
                'description' => 'VIK Express Pleven е demo стандартен профил с ясна услуга, град и телефон за директен контакт.',
                'payment_methods' => 'В брой, банков превод',
                'years_experience' => '8 години опит',
                'emergency_services' => true,
                'works_24_7' => true,
                'response_time_label' => 'Спешна реакция',
                'service_areas' => 'Плевен, Долна Митрополия',
                'service_cities' => ['Плевен', 'Долна Митрополия'],
                'service_categories' => ['ВиК услуги', 'Ремонти'],
                'subscription_plan' => 'standard',
                'subscription_status' => 'active',
                'subscription_started_at' => now()->subDays(20),
                'subscription_ends_at' => now()->addDays(10),
                'is_verified' => true,
                'verified_at' => now()->subDays(6),
                'created_at' => now()->subDays(22),
            ], [
                ['title' => 'Отстраняване на теч', 'category' => 'ВиК услуги', 'city' => 'Плевен', 'price' => 30, 'image' => 'demo/plumbing.svg'],
                ['title' => 'Монтаж на бойлер', 'category' => 'ВиК услуги', 'city' => 'Плевен', 'price' => 55, 'image' => null],
            ]),

            $this->business('demo.electric@fixnow.test', [
                'name' => 'Иван Димитров',
                'business_name' => 'Electric Pro Solutions',
                'business_category' => 'Електро услуги',
                'city' => 'Пловдив',
                'address' => 'кв. Кючук Париж, Пловдив',
                'phone' => '+359 88 800 1003',
                'website' => 'https://example.com/electric-pro',
                'working_hours' => 'Понеделник - Петък, 09:00 - 18:30',
                'short_description' => 'Електро услуги за домове, офиси, табла, контакти и осветление.',
                'description' => 'Electric Pro Solutions показва как premium бизнес може да има няколко града, повече услуги и доверителни badges.',
                'payment_methods' => 'Карта, фактура, банков превод',
                'years_experience' => '10 години опит',
                'emergency_services' => true,
                'works_24_7' => false,
                'response_time_label' => 'Отговаря до 1 час',
                'service_areas' => 'Пловдив, Асеновград, Пазарджик',
                'service_cities' => ['Пловдив', 'Асеновград', 'Пазарджик'],
                'service_categories' => ['Електро услуги', 'Ремонти', 'Спешни услуги'],
                'subscription_plan' => 'premium',
                'subscription_status' => 'active',
                'subscription_started_at' => now()->subDays(35),
                'subscription_ends_at' => now()->addDays(25),
                'stripe_customer_id' => 'cus_demo_electric',
                'stripe_subscription_id' => 'sub_demo_electric',
                'is_verified' => false,
                'verified_at' => null,
                'created_at' => now()->subDays(28),
            ], [
                ['title' => 'Смяна на ел. табло', 'category' => 'Електро услуги', 'city' => 'Пловдив', 'price' => 120, 'image' => 'demo/electric-service.svg'],
                ['title' => 'Монтаж на осветление', 'category' => 'Електро услуги', 'city' => 'Асеновград', 'price' => 40, 'image' => null],
                ['title' => 'Спешно отстраняване на късо', 'category' => 'Спешни услуги', 'city' => 'Пазарджик', 'price' => 60, 'image' => null],
            ]),

            $this->business('demo.catering@fixnow.test', [
                'name' => 'Николай Стоянов',
                'business_name' => 'EventPro Кетъринг',
                'business_category' => 'Събития и празници',
                'city' => 'Варна',
                'address' => 'Морска градина, Варна',
                'phone' => '+359 88 800 1004',
                'website' => 'https://example.com/eventpro',
                'instagram' => 'https://instagram.com/fixnow_demo_eventpro',
                'working_hours' => 'Понеделник - събота, 09:00 - 19:00',
                'short_description' => 'Кетъринг, организация и обслужване на събития във Варна и региона.',
                'description' => 'EventPro Кетъринг е demo профил за събитийна услуга с ясен пакет, снимки и директен контакт.',
                'payment_methods' => 'Карта, в брой',
                'years_experience' => '6 години',
                'emergency_services' => false,
                'works_24_7' => false,
                'response_time_label' => 'Отговаря в рамките на деня',
                'service_areas' => 'Варна',
                'service_cities' => ['Варна'],
                'service_categories' => ['Кетъринг', 'Събития и празници'],
                'subscription_plan' => 'standard',
                'subscription_status' => 'trial',
                'trial_started_at' => now()->subDays(3),
                'trial_ends_at' => now()->addDays(27),
                'is_verified' => false,
                'verified_at' => null,
                'created_at' => now()->subDays(3),
            ], [
                ['title' => 'Кетъринг за събитие', 'category' => 'Кетъринг', 'city' => 'Варна', 'price' => 45, 'image' => 'demo/catering.svg'],
            ]),

            $this->business('demo.photo@fixnow.test', [
                'name' => 'Елена Николова',
                'business_name' => 'Studio Focus Burgas',
                'business_category' => 'Събития и празници',
                'city' => 'Бургас',
                'address' => 'ул. Крайморска 8, Бургас',
                'phone' => '+359 88 800 1005',
                'website' => 'https://example.com/studio-focus',
                'facebook' => 'https://facebook.com/fixnow-demo-photo',
                'working_hours' => 'Сряда - неделя, 10:00 - 20:00',
                'short_description' => 'Фото и видео заснемане за семейни, бизнес и локални събития.',
                'description' => 'Studio Focus Burgas е premium demo профил с повече градове, снимки, услуги и препоръчано позициониране.',
                'payment_methods' => 'Карта, банков превод, фактура',
                'years_experience' => '15 години',
                'emergency_services' => false,
                'works_24_7' => true,
                'response_time_label' => 'Бърз отговор',
                'service_areas' => 'Бургас, Поморие, Несебър, Созопол',
                'service_cities' => ['Бургас', 'Поморие', 'Несебър', 'Созопол'],
                'service_categories' => ['Фотографи', 'Видеографи', 'Събития и празници'],
                'subscription_plan' => 'premium',
                'subscription_status' => 'active',
                'subscription_started_at' => now()->subDays(45),
                'subscription_ends_at' => now()->addDays(15),
                'stripe_customer_id' => 'cus_demo_photo',
                'stripe_subscription_id' => 'sub_demo_photo',
                'is_verified' => true,
                'verified_at' => now()->subDays(30),
                'created_at' => now()->subDays(45),
            ], [
                ['title' => 'Фото заснемане на събитие', 'category' => 'Фотографи', 'city' => 'Бургас', 'price' => 180, 'image' => 'demo/beauty.svg'],
                ['title' => 'Видео пакет за събитие', 'category' => 'Видеографи', 'city' => 'Поморие', 'price' => 260, 'image' => null],
            ]),

            $this->business('demo.cleaning@fixnow.test', [
                'name' => 'Силвия Маринова',
                'business_name' => 'Clean Home Pro',
                'business_category' => 'Почистване',
                'city' => 'София',
                'address' => 'Младост 4, София',
                'phone' => '+359 88 800 1006',
                'working_hours' => 'Понеделник - Събота, 08:00 - 18:00',
                'short_description' => 'Почистване на домове, офиси, входове и след ремонт.',
                'description' => 'Clean Home Pro е demo профил за локална услуга с ясна цена от и директен телефон.',
                'payment_methods' => 'В брой, банков превод',
                'years_experience' => '5 години',
                'emergency_services' => false,
                'works_24_7' => false,
                'service_areas' => 'София',
                'service_cities' => ['София'],
                'service_categories' => ['Почистване'],
                'subscription_plan' => 'standard',
                'subscription_status' => 'active',
                'subscription_started_at' => now()->subDays(10),
                'subscription_ends_at' => now()->addDays(20),
                'is_verified' => true,
                'verified_at' => now()->subDays(5),
                'created_at' => now()->subDays(12),
            ], [
                ['title' => 'Почистване на апартамент', 'category' => 'Почистване', 'city' => 'София', 'price' => 40, 'image' => 'demo/cleaning.svg'],
            ]),

            $this->business('demo.beauty@fixnow.test', [
                'name' => 'Анна Георгиева',
                'business_name' => 'Beauty Studio Elite',
                'business_category' => 'Красота и услуги',
                'city' => 'Плевен',
                'address' => 'ул. Дойран 12, Плевен',
                'phone' => '+359 88 800 1007',
                'instagram' => 'https://instagram.com/fixnow_demo_beauty',
                'working_hours' => 'Вторник - Събота, 10:00 - 19:00',
                'short_description' => 'Фризьор, маникюр и козметични услуги в центъра на Плевен.',
                'description' => 'Beauty Studio Elite показва как малък локален бизнес може да изглежда професионално във FixNow.',
                'payment_methods' => 'Карта, в брой',
                'years_experience' => '9 години',
                'emergency_services' => false,
                'works_24_7' => false,
                'response_time_label' => 'Отговаря бързо',
                'service_areas' => 'Плевен',
                'service_cities' => ['Плевен'],
                'service_categories' => ['Красота и услуги'],
                'subscription_plan' => 'standard',
                'subscription_status' => 'trial',
                'trial_started_at' => now()->subDays(1),
                'trial_ends_at' => now()->addDays(29),
                'is_verified' => false,
                'verified_at' => null,
                'created_at' => now()->subHours(18),
            ], [
                ['title' => 'Мъжко подстригване', 'category' => 'Красота и услуги', 'city' => 'Плевен', 'price' => 12, 'image' => 'demo/beauty.svg'],
                ['title' => 'Маникюр с гел лак', 'category' => 'Красота и услуги', 'city' => 'Плевен', 'price' => 25, 'image' => null],
            ]),

            $this->business('demo.repairs@fixnow.test', [
                'name' => 'Петър Христов',
                'business_name' => 'FixMaster Ремонти',
                'business_category' => 'Ремонти',
                'city' => 'Пловдив',
                'address' => 'Тракия, Пловдив',
                'phone' => '+359 88 800 1008',
                'website' => 'https://example.com/fixmaster',
                'working_hours' => 'Понеделник - Неделя, 08:00 - 20:00',
                'short_description' => 'Майстори за дребни ремонти, монтажи, боядисване и довършителни работи.',
                'description' => 'FixMaster Ремонти е стандартен demo профил за ремонтни услуги в Пловдив.',
                'payment_methods' => 'В брой, фактура',
                'years_experience' => '11 години',
                'emergency_services' => true,
                'works_24_7' => false,
                'service_areas' => 'Пловдив, Асеновград',
                'service_cities' => ['Пловдив', 'Асеновград'],
                'service_categories' => ['Ремонти', 'Почистване'],
                'subscription_plan' => 'standard',
                'subscription_status' => 'active',
                'subscription_started_at' => now()->subDays(6),
                'subscription_ends_at' => now()->addDays(24),
                'is_verified' => false,
                'verified_at' => null,
                'created_at' => now()->subDays(6),
            ], [
                ['title' => 'Дребни ремонти у дома', 'category' => 'Ремонти', 'city' => 'Пловдив', 'price' => 35, 'image' => 'demo/repair-tools.svg'],
            ]),

            $this->business('demo.expired@fixnow.test', [
                'name' => 'Expired Demo Owner',
                'business_name' => 'Скрит Expired Demo Бизнес',
                'business_category' => 'Ремонти',
                'city' => 'София',
                'phone' => '+359 88 800 1098',
                'short_description' => 'Demo профил със статус expired, който трябва да е скрит публично.',
                'description' => 'Този профил е само за QA на public visibility и admin status секциите.',
                'service_cities' => ['София'],
                'service_categories' => ['Ремонти'],
                'subscription_plan' => 'standard',
                'subscription_status' => 'expired',
                'subscription_started_at' => now()->subDays(60),
                'subscription_ends_at' => now()->subDay(),
                'is_verified' => false,
                'created_at' => now()->subDays(60),
            ]),

            $this->business('demo.cancelled@fixnow.test', [
                'name' => 'Cancelled Demo Owner',
                'business_name' => 'Скрит Cancelled Demo Бизнес',
                'business_category' => 'Почистване',
                'city' => 'Варна',
                'phone' => '+359 88 800 1099',
                'short_description' => 'Demo профил със статус cancelled, който не трябва да се вижда публично.',
                'description' => 'Този профил е само за QA на скриване от публични списъци.',
                'service_cities' => ['Варна'],
                'service_categories' => ['Почистване'],
                'subscription_plan' => 'premium',
                'subscription_status' => 'cancelled',
                'subscription_started_at' => now()->subDays(70),
                'subscription_ends_at' => now()->subDays(3),
                'cancelled_at' => now()->subDays(3),
                'is_verified' => true,
                'verified_at' => now()->subDays(50),
                'created_at' => now()->subDays(70),
            ]),
        ]);

        $this->seedTrustSignals($businesses, $client);
        $this->seedServiceRequests($businesses, $admin, $client);
    }

    private function user(string $email, array $attributes): User
    {
        $user = User::query()->firstOrNew(['email' => $email]);
        $user->forceFill(array_merge([
            'email' => $email,
            'password' => Hash::make('password'),
        ], $attributes))->save();

        return $user->fresh();
    }

    private function business(string $email, array $attributes, array $services = []): User
    {
        $business = $this->user($email, array_merge([
            'role' => 'business',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'subscription_plan' => 'standard',
            'subscription_status' => 'active',
            'trial_started_at' => null,
            'trial_ends_at' => null,
            'cancelled_at' => null,
            'extra_city_addon_count' => 0,
        ], $attributes));

        foreach ($services as $service) {
            Service::query()->updateOrCreate(
                [
                    'user_id' => $business->id,
                    'title' => $service['title'],
                ],
                [
                    'category' => $service['category'],
                    'city' => $service['city'],
                    'description' => $service['description'] ?? 'Demo услуга във FixNow.bg с примерна цена, град и директен контакт.',
                    'price' => $service['price'] ?? null,
                    'phone' => $business->phone ?: '+359 88 800 0000',
                    'image' => $service['image'] ?? null,
                ]
            );

            if (Schema::hasTable('business_photos') && filled($service['image'] ?? null)) {
                BusinessPhoto::query()->updateOrCreate(
                    [
                        'business_id' => $business->id,
                        'path' => $service['image'],
                    ],
                    [
                        'original_name' => basename($service['image']),
                        'alt_text' => $service['title'],
                        'sort_order' => 0,
                    ]
                );
            }
        }

        return $business->fresh(['services']);
    }

    private function seedTrustSignals($businesses, User $client): void
    {
        if (Schema::hasTable('reviews')) {
            $reviews = [
                ['email' => 'premium@example.com', 'name' => 'Даниела П.', 'rating' => 5, 'comment' => 'Много бърза диагностика и ясна комуникация. Профилът във FixNow беше полезен за сравнение.'],
                ['email' => 'demo.vik@fixnow.test', 'name' => 'Ивайло М.', 'rating' => 5, 'comment' => 'Реагираха в същия ден при теч. Хареса ми, че телефонът и услугите са видими веднага.'],
                ['email' => 'demo.electric@fixnow.test', 'name' => 'Станислава Р.', 'rating' => 4, 'comment' => 'Добра комуникация и професионално отношение при смяна на осветление.'],
                ['email' => 'demo.catering@fixnow.test', 'name' => 'Мартин К.', 'rating' => 5, 'comment' => 'Добра организация за събитие и лесен контакт през профила.'],
                ['email' => 'demo.photo@fixnow.test', 'name' => 'Елица Н.', 'rating' => 5, 'comment' => 'Чист профил, ясни снимки и удобна информация за услугите.'],
                ['email' => 'demo.cleaning@fixnow.test', 'name' => 'Габриела В.', 'rating' => 4, 'comment' => 'Поръчахме почистване след ремонт. Всичко беше описано ясно.'],
            ];

            foreach ($reviews as $review) {
                $business = $businesses->firstWhere('email', $review['email']);

                if (!$business) {
                    continue;
                }

                Review::query()->updateOrCreate(
                    [
                        'business_id' => $business->id,
                        'reviewer_email' => str($review['name'])->slug('.') . '@example.test',
                    ],
                    [
                        'reviewer_name' => $review['name'],
                        'rating' => $review['rating'],
                        'comment' => $review['comment'],
                        'status' => Review::STATUS_APPROVED,
                        'approved_at' => now()->subDays(rand(1, 12)),
                    ]
                );
            }
        }

        if (Schema::hasTable('business_recommendations')) {
            foreach ($businesses->where('business_name', '!=', 'Нов бизнес профил')->values() as $index => $business) {
                BusinessRecommendation::query()->updateOrCreate(
                    ['business_id' => $business->id, 'user_id' => $client->id],
                    ['ip_hash' => null]
                );

                BusinessRecommendation::query()->updateOrCreate(
                    ['business_id' => $business->id, 'ip_hash' => hash('sha256', 'demo-' . $business->id . '-' . $index)],
                    ['user_id' => null]
                );
            }
        }

        if (Schema::hasTable('business_analytics_events')) {
            foreach ($businesses->where('business_name', '!=', 'Нов бизнес профил') as $business) {
                foreach ([BusinessAnalyticsEvent::PROFILE_VIEW, BusinessAnalyticsEvent::PHONE_CLICK, BusinessAnalyticsEvent::WEBSITE_CLICK, BusinessAnalyticsEvent::INQUIRY_CLICK] as $eventType) {
                    $times = $eventType === BusinessAnalyticsEvent::PROFILE_VIEW ? 4 : 2;

                    for ($i = 0; $i < $times; $i++) {
                        $analyticsPayload = [
                            'business_id' => $business->id,
                            'actor_id' => null,
                            'event_type' => $eventType,
                            'ip_address' => null,
                            'user_agent' => 'FixNow demo seeder',
                            'metadata' => ['source' => 'demo-seeder'],
                        ];

                        if (Schema::hasColumn('business_analytics_events', 'ip_hash')) {
                            $analyticsPayload['ip_hash'] = hash('sha256', 'demo-analytics-' . $business->id . '-' . $eventType . '-' . $i);
                        }

                        BusinessAnalyticsEvent::query()->create($analyticsPayload);
                    }
                }
            }
        }
    }

    private function seedServiceRequests($businesses, User $admin, User $client): void
    {
        if (!Schema::hasTable('service_requests')) {
            return;
        }

        $requests = [
            [
                'name' => 'Demo Client',
                'phone' => '+359 88 700 2001',
                'email' => 'client@example.com',
                'city' => 'Плевен',
                'category' => 'ВиК услуги',
                'service' => 'Спешен теч',
                'description' => 'Имаме теч под мивката и търсим бизнес, който може да реагира бързо.',
                'urgency' => ServiceRequest::URGENCY_URGENT,
                'budget' => 'до 120 лв.',
                'status' => ServiceRequest::STATUS_NEW,
                'business_email' => 'demo.vik@fixnow.test',
            ],
            [
                'name' => 'Demo Event Client',
                'phone' => '+359 88 700 2002',
                'email' => 'event.client@example.test',
                'city' => 'Варна',
                'category' => 'Кетъринг',
                'service' => 'Кетъринг за събитие',
                'description' => 'Търся кетъринг за малко фирмено събитие с около 25 гости.',
                'urgency' => ServiceRequest::URGENCY_NORMAL,
                'budget' => 'до 500 лв.',
                'status' => ServiceRequest::STATUS_CONTACTED,
                'business_email' => 'demo.catering@fixnow.test',
            ],
            [
                'name' => 'Demo Home Owner',
                'phone' => '+359 88 700 2003',
                'email' => 'home.owner@example.test',
                'city' => 'Пловдив',
                'category' => 'Електро услуги',
                'service' => 'Смяна на контакти',
                'description' => 'Трябва да се сменят няколко контакта и да се провери осветлението.',
                'urgency' => ServiceRequest::URGENCY_NORMAL,
                'budget' => 'по оферта',
                'status' => ServiceRequest::STATUS_NEW,
                'business_email' => 'demo.electric@fixnow.test',
            ],
        ];

        foreach ($requests as $payload) {
            $business = $businesses->firstWhere('email', $payload['business_email']);
            $serviceRequest = ServiceRequest::query()->updateOrCreate(
                [
                    'phone' => $payload['phone'],
                    'description' => $payload['description'],
                ],
                collect($payload)
                    ->except('business_email')
                    ->merge(['assigned_business_id' => $business?->id])
                    ->all()
            );

            if ($business && Schema::hasTable('service_request_assignments')) {
                ServiceRequestAssignment::query()->updateOrCreate(
                    [
                        'service_request_id' => $serviceRequest->id,
                        'business_id' => $business->id,
                    ],
                    [
                        'status' => $payload['status'] === ServiceRequest::STATUS_CONTACTED
                            ? ServiceRequestAssignment::STATUS_CONTACTED
                            : ServiceRequestAssignment::STATUS_SENT,
                        'sent_at' => now()->subDays(1),
                        'contacted_at' => $payload['status'] === ServiceRequest::STATUS_CONTACTED ? now()->subHours(6) : null,
                    ]
                );
            }
        }
    }
}
