<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редакция на профил на бизнес | BON</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#020812] pb-24 text-white md:pb-0">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(249,115,22,0.20),transparent_30%),radial-gradient(circle_at_82%_16%,rgba(245,158,11,0.18),transparent_30%),linear-gradient(180deg,#030712,#061426,#020812)]"></div>

    @php
        $baseCities = ['Плевен','София','Пловдив','Варна','Бургас','Русе','Стара Загора','Велико Търново','Благоевград','Добрич','Шумен','Сливен','Хасково','Пазарджик'];
        $baseCategories = ['Ресторанти и кафенета','Хотели','Ремонти и строителство','ВиК','Електро услуги','Автосервизи','Почистване','Красота и грижа','Здраве и уелнес','Спорт и активности'];
        $selectedServiceCities = old('service_cities', $user->serviceCities());
        $selectedServiceCities = is_array($selectedServiceCities) ? $selectedServiceCities : [];
        $availableServiceCities = array_values(array_unique(array_merge($baseCities, $selectedServiceCities)));
        $selectedCategories = old('service_categories', $user->serviceCategories());
        $selectedCategories = is_array($selectedCategories) ? $selectedCategories : [];
        $availableCategories = array_values(array_unique(array_merge($baseCategories, $selectedCategories)));
        $cityLimit = $user->cityLimit();
        $serviceLimit = $user->categoryLimit();
        $photoLimit = $user->photoLimit();
        $serviceCount = $user->services->count();
        $photoCount = $user->photoCount();
        $businessPhotos = $user->relationLoaded('businessPhotos') ? $user->businessPhotos : collect();
        $profile = $user->profileCompleteness();
        $shortDescription = old('short_description', data_get($user, 'short_description'));
        $description = old('description', data_get($user, 'description'));
        $facebook = old('facebook', data_get($user, 'facebook') ?: data_get($user, 'фейсбук'));
        $instagram = old('instagram', data_get($user, 'instagram') ?: data_get($user, 'инстаграм'));
        $whatsapp = old('whatsapp', data_get($user, 'whatsapp'));
        $viber = old('viber', data_get($user, 'viber'));
        $paymentMethods = old('payment_methods', data_get($user, 'payment_methods') ?: data_get($user, 'методи_на_плащане'));
        $yearsExperience = old('years_experience', data_get($user, 'years_experience') ?: data_get($user, 'години_опит'));
        $serviceAreas = old('service_areas', data_get($user, 'service_areas') ?: data_get($user, 'обслужвани_райони'));
        $emergencyServices = (bool) old('emergency_services', data_get($user, 'emergency_services', data_get($user, 'спешни_услуги', false)));
        $works247 = (bool) old('works_24_7', data_get($user, 'works_24_7', false));
        $responseTimeLabel = old('response_time_label', data_get($user, 'response_time_label'));
        $workingHoursText = old('working_hours_text', $user->working_hours ?: data_get($user, 'работно_време'));
        $endDate = $user->effectiveSubscriptionStatus() === 'trial' ? $user->trial_ends_at : $user->subscription_ends_at;
    @endphp

    <main class="mx-auto max-w-[1500px] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('dashboard') }}" class="inline-flex w-fit rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:bg-white/10">Назад към таблото</a>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('businesses.show', $user) }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white/75 hover:bg-white/10">Виж публичния профил</a>
                <a href="{{ route('services.create') }}" class="rounded-2xl bg-orange-300/10 px-4 py-2 text-sm font-bold text-orange-100 hover:bg-orange-300/15">Добави услуга</a>
            </div>
        </div>

        <section class="grid gap-6 lg:grid-cols-[1fr_390px]">
            <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.25em] text-orange-200/80">Business profile editor</p>
                <h1 class="mt-3 text-3xl font-black sm:text-5xl">Редакция на профил на бизнес</h1>
                <p class="mt-3 max-w-3xl text-white/60">Попълнете информацията, която клиентите виждат в публичния профил. Лимитите се прилагат според текущия план.</p>

                @if(session('success'))
                    <div class="mt-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-emerald-200">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="mt-6 rounded-2xl border border-rose-400/20 bg-rose-400/10 p-4 text-rose-200">
                        <p class="font-black">Моля, проверете полетата:</p>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('business.profile.update') }}" method="POST" class="mt-8 grid gap-6">
                    @csrf
                    @method('PUT')

                    <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-xl font-black">Основна информация</h2>
                                <p class="mt-1 text-sm text-white/50">Име, основна категория и описания.</p>
                            </div>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white/60">{{ $user->planLabel() }}</span>
                        </div>

                        <div class="mt-5 grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">Име на профила</label>
                                <input type="text" name="business_name" value="{{ old('business_name', $user->business_name) }}" placeholder="Например: Auto Premium Service" class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                                @error('business_name')<p class="mt-2 text-sm text-rose-200">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">Основна категория</label>
                                <select name="business_category" class="w-full rounded-2xl border border-white/10 bg-slate-950 px-4 py-4 text-white outline-none focus:border-orange-300/50">
                                    <option value="">Избери категория</option>
                                    @foreach($baseCategories as $category)
                                        <option value="{{ $category }}" {{ old('business_category', $user->business_category) == $category ? 'selected' : '' }}>{{ $category }}</option>
                                    @endforeach
                                </select>
                                @error('business_category')<p class="mt-2 text-sm text-rose-200">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="mb-2 block text-sm font-semibold text-white/75">Кратко описание</label>
                            <input type="text" name="short_description" value="{{ $shortDescription }}" maxlength="320" placeholder="Едно ясно изречение, което представя услугите ви." class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            <p class="mt-2 text-xs text-white/45">Показва се в hero секцията на публичния профил.</p>
                            @error('short_description')<p class="mt-2 text-sm text-rose-200">{{ $message }}</p>@enderror
                        </div>

                        <div class="mt-5">
                            <label class="mb-2 block text-sm font-semibold text-white/75">Подробно описание</label>
                            <textarea name="description" rows="6" placeholder="Опишете опита, подхода, услугите и защо клиентите могат да ви се доверят." class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">{{ $description }}</textarea>
                            @error('description')<p class="mt-2 text-sm text-rose-200">{{ $message }}</p>@enderror
                        </div>
                    </section>

                    <section id="cities" class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-xl font-black">Градове и обслужвани райони</h2>
                                <p class="mt-1 text-sm text-white/50">{{ $user->planLabel() }} лимит: {{ $cityLimit }} града.</p>
                            </div>
                            <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">{{ count($selectedServiceCities) }} / {{ $cityLimit }}</span>
                        </div>

                        @error('service_cities')
                            <p class="mt-4 rounded-2xl border border-rose-400/20 bg-rose-400/10 p-3 text-sm text-rose-200">{{ $message }}</p>
                        @enderror

                        <div class="mt-5 grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">Основен град</label>
                                <input list="city-options" name="city" value="{{ old('city', $user->city) }}" placeholder="Например: Плевен" class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                                <datalist id="city-options">
                                    @foreach($baseCities as $city)
                                        <option value="{{ $city }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">Адрес / район</label>
                                <input type="text" name="address" value="{{ old('address', $user->address) }}" placeholder="Например: ул. Иван Вазов 12 или район Център" class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            </div>
                        </div>

                        <div class="mt-5 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($availableServiceCities as $serviceCity)
                                <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-3 py-3 text-sm font-bold text-white/75">
                                    <input type="checkbox" name="service_cities[]" value="{{ $serviceCity }}" {{ in_array($serviceCity, $selectedServiceCities, true) ? 'checked' : '' }} class="rounded border-white/20 bg-white/10 text-orange-400">
                                    {{ $serviceCity }}
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-5">
                            <label class="mb-2 block text-sm font-semibold text-white/75">Други градове</label>
                            <input type="text" name="service_cities_custom" value="{{ old('service_cities_custom') }}" placeholder="Добавете градове, разделени със запетая" class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            <p class="mt-2 text-xs text-white/45">Standard: до 2 града. Premium: до 5 града. Ако ви трябват повече градове, свържете се с администратор преди промяна на плана.</p>
                        </div>

                        <div class="mt-5">
                            <label class="mb-2 block text-sm font-semibold text-white/75">Обслужвани райони</label>
                            <textarea name="service_areas" rows="3" placeholder="Например: Плевен и област, център, квартали, до 25 км..." class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">{{ $serviceAreas }}</textarea>
                        </div>
                    </section>

                    <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-xl font-black">Категории и услуги</h2>
                                <p class="mt-1 text-sm text-white/50">Изберете до {{ $serviceLimit }} категории/услуги според плана.</p>
                            </div>
                            <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-black text-orange-100">{{ count($selectedCategories) }} / {{ $serviceLimit }}</span>
                        </div>

                        @error('service_categories')
                            <p class="mt-4 rounded-2xl border border-rose-400/20 bg-rose-400/10 p-3 text-sm text-rose-200">{{ $message }}</p>
                        @enderror

                        <div class="mt-5 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($availableCategories as $category)
                                <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-3 py-3 text-sm font-bold text-white/75">
                                    <input type="checkbox" name="service_categories[]" value="{{ $category }}" {{ in_array($category, $selectedCategories, true) ? 'checked' : '' }} class="rounded border-white/20 bg-white/10 text-orange-400">
                                    {{ $category }}
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-5 rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="font-black">Публикувани услуги: {{ $serviceCount }} / {{ $serviceLimit }}</p>
                            <p class="mt-2 text-sm leading-6 text-white/60">Услугите с цена “от” се добавят от отделната форма. Лимитът се прилага при публикуване.</p>
                            <a href="{{ route('services.create') }}" class="mt-4 inline-flex rounded-2xl bg-orange-300/10 px-4 py-3 text-sm font-black text-orange-100 hover:bg-orange-300/15">Добави услуга</a>
                        </div>
                    </section>

                    <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                        <h2 class="text-xl font-black">Контакти</h2>
                        <div class="mt-5 grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">Телефон</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Например: 0899123456" class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">Уебсайт</label>
                                <input type="text" name="website" value="{{ old('website', $user->website) }}" placeholder="https://example.com" class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">Facebook</label>
                                <input type="text" name="facebook" value="{{ $facebook }}" placeholder="https://facebook.com/..." class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">Instagram</label>
                                <input type="text" name="instagram" value="{{ $instagram }}" placeholder="https://instagram.com/..." class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">WhatsApp</label>
                                <input type="text" name="whatsapp" value="{{ $whatsapp }}" placeholder="+359..." class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">Viber</label>
                                <input type="text" name="viber" value="{{ $viber }}" placeholder="+359..." class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            </div>
                        </div>
                    </section>

                    <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                        <h2 class="text-xl font-black">Работно време и доверие</h2>
                        <div class="mt-5">
                            <label class="mb-2 block text-sm font-semibold text-white/75">Работно време</label>
                            <input type="text" name="working_hours_text" value="{{ $workingHoursText }}" placeholder="Например: Пон-Пет 09:00 - 18:00, Съб 10:00 - 14:00" class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            <p class="mt-2 text-xs text-white/45">Може да въведете свободен текст или да използвате часовете по-долу.</p>
                        </div>

                        <div class="mt-5 grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">От</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <select name="start_hour" class="rounded-2xl border border-white/10 bg-slate-950 px-4 py-4 text-white outline-none focus:border-orange-300/50">
                                        <option value="">Час</option>
                                        @for($h = 0; $h <= 23; $h++)
                                            @php $hh = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                                            <option value="{{ $hh }}" {{ old('start_hour') == $hh ? 'selected' : '' }}>{{ $hh }}</option>
                                        @endfor
                                    </select>
                                    <select name="start_minute" class="rounded-2xl border border-white/10 bg-slate-950 px-4 py-4 text-white outline-none focus:border-orange-300/50">
                                        <option value="">Мин</option>
                                        @foreach(['00', '15', '30', '45'] as $m)
                                            <option value="{{ $m }}" {{ old('start_minute') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">До</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <select name="end_hour" class="rounded-2xl border border-white/10 bg-slate-950 px-4 py-4 text-white outline-none focus:border-orange-300/50">
                                        <option value="">Час</option>
                                        @for($h = 0; $h <= 23; $h++)
                                            @php $hh = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                                            <option value="{{ $hh }}" {{ old('end_hour') == $hh ? 'selected' : '' }}>{{ $hh }}</option>
                                        @endfor
                                    </select>
                                    <select name="end_minute" class="rounded-2xl border border-white/10 bg-slate-950 px-4 py-4 text-white outline-none focus:border-orange-300/50">
                                        <option value="">Мин</option>
                                        @foreach(['00', '15', '30', '45'] as $m)
                                            <option value="{{ $m }}" {{ old('end_minute') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">Методи на плащане</label>
                                <input type="text" name="payment_methods" value="{{ $paymentMethods }}" placeholder="В брой, карта, банков превод..." class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-white/75">Години опит</label>
                                <input type="text" name="years_experience" value="{{ $yearsExperience }}" placeholder="Например: 8 години" class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white outline-none placeholder:text-white/40 focus:border-orange-300/50">
                            </div>
                        </div>

                        <label class="mt-5 flex items-start gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 text-sm font-bold text-white/75">
                            <input type="checkbox" name="emergency_services" value="1" {{ $emergencyServices ? 'checked' : '' }} class="mt-1 rounded border-white/20 bg-white/10 text-orange-400">
                            <span>
                                Приемам спешни заявки
                                <span class="mt-1 block text-xs font-normal leading-5 text-white/45">Това е бизнес-контролирана настройка и може да се използва като trust/filter signal по-късно.</span>
                            </span>
                        </label>

                        <label class="mt-3 flex items-start gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 text-sm font-bold text-white/75">
                            <input type="checkbox" name="works_24_7" value="1" {{ $works247 ? 'checked' : '' }} class="mt-1 rounded border-white/20 bg-white/10 text-orange-400">
                            <span>
                                Работим 24/7
                                <span class="mt-1 block text-xs font-normal leading-5 text-white/45">Показва badge “24/7” в публичните резултати.</span>
                            </span>
                        </label>

                        <div class="mt-5">
                            <label class="mb-2 block text-sm font-semibold text-white/75">Време за отговор</label>
                            <select name="response_time_label" class="w-full rounded-2xl border border-white/10 bg-slate-950 px-4 py-4 text-white outline-none focus:border-orange-300/50">
                                <option value="">Без badge</option>
                                @foreach(['Отговаря бързо', 'Отговаря до 1 час', 'Отговаря в рамките на деня'] as $label)
                                    <option value="{{ $label }}" {{ $responseTimeLabel === $label ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </section>

                    <section id="gallery" class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5 md:col-span-2">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <h2 class="text-xl font-black">Галерия / Снимки</h2>
                                    <p class="mt-3 max-w-2xl text-sm leading-6 text-white/60">Качвайте снимки директно към профила на бизнес. Това е отделна галерия и не е свързана с “Предлагай услуга”.</p>
                                    <p class="mt-2 text-sm font-bold text-orange-200">Използвани: {{ $photoCount }} / {{ $photoLimit }} снимки</p>
                                </div>

                                <div class="grid gap-3 rounded-3xl border border-white/10 bg-white/5 p-4 lg:min-w-[360px]">
                                    <label class="block">
                                        <span class="mb-2 block text-sm font-semibold text-white/75">Добави снимки</span>
                                        <input form="business-photo-upload-form" type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" class="block w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white file:mr-3 file:rounded-xl file:border-0 file:bg-orange-400 file:px-4 file:py-2 file:font-black file:text-slate-950">
                                    </label>
                                    @error('photos')<p class="text-sm text-rose-200">{{ $message }}</p>@enderror
                                    @error('photos.*')<p class="text-sm text-rose-200">{{ $message }}</p>@enderror
                                    <button form="business-photo-upload-form" type="submit" class="min-h-11 rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-5 py-3 text-sm font-black text-white">Качи в галерията</button>
                                </div>
                            </div>

                            @if($businessPhotos->isNotEmpty())
                                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                    @foreach($businessPhotos as $photo)
                                        <article class="overflow-hidden rounded-3xl border border-white/10 bg-slate-950/50">
                                            <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->alt_text ?: $user->business_name }}" loading="lazy" class="h-36 w-full object-cover">
                                            <div class="p-3">
                                                <button form="delete-business-photo-{{ $photo->id }}" type="submit" class="w-full rounded-2xl border border-rose-300/20 bg-rose-400/10 px-4 py-2 text-sm font-black text-rose-100 hover:bg-rose-400/15">Изтрий</button>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div class="mt-5 rounded-3xl border border-dashed border-white/15 bg-white/5 p-6 text-center">
                                    <p class="font-black">Все още няма снимки в галерията</p>
                                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-white/55">Качете снимки от обекта, екипа, работата преди/след или портфолио кадри според типа услуга.</p>
                                </div>
                            @endif
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                            <h2 class="text-xl font-black">Verified статус</h2>
                            @if($user->is_verified)
                                <p class="mt-3 text-sm leading-6 text-emerald-100">Бизнесят е потвърден и има отделен Verified badge.</p>
                            @else
                                <p class="mt-3 text-sm leading-6 text-white/60">Потвърждаването се управлява от админ, за да остане badge-ът реален trust signal.</p>
                            @endif
                        </div>
                    </section>

                    <div class="sticky bottom-4 z-20 flex flex-col gap-3 rounded-3xl border border-white/10 bg-slate-950/90 p-4 shadow-2xl shadow-black/30 backdrop-blur-xl sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-white/60">Промените се записват в текущия профил на бизнес.</p>
                        <button type="submit" class="rounded-2xl bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 px-6 py-4 font-black text-white shadow-lg shadow-orange-600/25">Запази промените</button>
                    </div>
                </form>

                <form id="business-photo-upload-form" action="{{ route('business.profile.photos.store') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                </form>

                @foreach($businessPhotos as $photo)
                    <form id="delete-business-photo-{{ $photo->id }}" action="{{ route('business.profile.photos.destroy', $photo) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            </div>

            <aside class="lg:sticky lg:top-8 lg:self-start">
                <div class="rounded-[32px] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/25 backdrop-blur-xl">
                    <p class="text-sm font-black uppercase text-orange-200/80">Live preview</p>
                    <div class="mt-5 h-36 rounded-3xl bg-gradient-to-br from-orange-400/20 via-orange-500/10 to-orange-600/20"></div>
                    <div class="-mt-10 flex h-20 w-20 items-center justify-center rounded-2xl border-4 border-slate-950 bg-gradient-to-br from-orange-400 to-orange-600 text-2xl font-black">
                        {{ strtoupper(mb_substr($user->business_name ?: $user->name, 0, 1)) }}
                    </div>
                    <h2 class="mt-4 text-2xl font-black">{{ $user->business_name ?: $user->name }}</h2>
                    <p class="mt-1 text-orange-200">{{ $user->business_category ?: 'Категория не е избрана' }}</p>
                    <p class="mt-3 text-sm leading-6 text-white/60">{{ $shortDescription ?: 'Краткото описание ще се покаже тук.' }}</p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="rounded-full bg-orange-400/10 px-3 py-1 text-xs font-bold text-orange-200">{{ $user->planLabel() }}</span>
                        @if($user->is_verified)
                            <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-bold text-emerald-200">Потвърден</span>
                        @endif
                        @if($emergencyServices)
                            <span class="rounded-full bg-rose-400/10 px-3 py-1 text-xs font-bold text-rose-200">Спешни заявки</span>
                        @endif
                    </div>

                    <div class="mt-5 grid gap-3 text-sm text-white/70">
                        <p class="rounded-2xl bg-slate-950/50 p-3">Статус: {{ ucfirst($user->effectiveSubscriptionStatus()) }}</p>
                        <p class="rounded-2xl bg-slate-950/50 p-3">Крайна дата: {{ $endDate ? $endDate->format('d.m.Y') : 'без крайна дата' }}</p>
                        <p class="rounded-2xl bg-slate-950/50 p-3">Градове: {{ count($selectedServiceCities) }} / {{ $cityLimit }}</p>
                        <p class="rounded-2xl bg-slate-950/50 p-3">Категории: {{ count($selectedCategories) }} / {{ $serviceLimit }}</p>
                        <p class="rounded-2xl bg-slate-950/50 p-3">Снимки: {{ $photoCount }} / {{ $photoLimit }}</p>
                        <p class="rounded-2xl bg-slate-950/50 p-3">Завършеност: {{ $profile['percent'] }}%</p>
                    </div>

                    @if(!$user->isPubliclyVisible())
                        <div class="mt-5 rounded-2xl border border-rose-300/20 bg-rose-400/10 p-4 text-sm leading-6 text-rose-100">
                            Профилът е скрит за публичните потребители, но owner preview работи през бутона “Виж публичния профил”.
                        </div>
                    @endif
                </div>
            </aside>
        </section>
    </main>
    @include('partials.mobile-bottom-nav')
</body>
</html>
