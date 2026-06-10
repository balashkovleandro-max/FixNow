<?php

namespace App\Http\Controllers;

use App\Models\BusinessDiagnostic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BusinessDiagnosticController extends Controller
{
    public function create(): View
    {
        return view('bon.business-problem');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['nullable', 'string', 'max:160'],
            'business_type' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:60'],
            'email' => ['nullable', 'email', 'max:255'],
            'problem_type' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:5000'],
            'duration' => ['nullable', 'string', 'max:120'],
            'urgency' => ['nullable', 'string', 'max:120'],
            'customer_source' => ['nullable', 'string', 'max:180'],
            'budget' => ['nullable', 'string', 'max:120'],
            'active_ads' => ['nullable', 'boolean'],
            'website' => ['nullable', 'boolean'],
            'google_business' => ['nullable', 'boolean'],
            'social_profiles' => ['nullable', 'boolean'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'google_business_url' => ['nullable', 'url', 'max:255'],
        ]);

        foreach (['active_ads', 'website', 'google_business', 'social_profiles'] as $field) {
            $validated[$field] = $request->boolean($field);
        }

        if (!Schema::hasTable('business_diagnostics')) {
            return back()
                ->withErrors(['diagnostic' => 'BON диагностиката е готова, но таблицата business_diagnostics още не е мигрирана. Пусни php artisan migrate и опитай отново.'])
                ->withInput();
        }

        $analysis = $this->analyze($validated);

        $diagnostic = BusinessDiagnostic::create(array_merge($validated, [
            'user_id' => $request->user()?->id,
            'likely_reason' => $analysis['likely_reason'],
            'recommended_specialists' => $analysis['recommended_specialists'],
            'next_steps' => $analysis['next_steps'],
            'warnings' => $analysis['warnings'],
        ]));

        return redirect()->route('bon.business-problem.result', $diagnostic);
    }

    public function result(BusinessDiagnostic $diagnostic): View
    {
        $viewer = auth()->user();

        abort_if($diagnostic->user_id && (!$viewer || (int) $viewer->id !== (int) $diagnostic->user_id), 403);

        return view('bon.business-problem-result', compact('diagnostic'));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{likely_reason: string, recommended_specialists: array<int, string>, next_steps: array<int, string>, warnings: array<int, string>}
     */
    private function analyze(array $data): array
    {
        $problem = Str::lower((string) ($data['problem_type'] ?? ''));
        $context = Str::lower((string) (($data['description'] ?? '') . ' ' . ($data['business_type'] ?? '')));
        $combined = $problem . ' ' . $context;

        if (Str::contains($combined, ['клиент', 'заявк', 'видимост'])) {
            return [
                'likely_reason' => 'Вероятната причина е комбинация от слаба видимост, неясно представена оферта и недостатъчно доверие в публичния профил.',
                'recommended_specialists' => ['маркетолог', 'copywriter', 'фотограф', 'консултант по продажби'],
                'next_steps' => [
                    'Опиши една ясна оферта за първи контакт или първа покупка.',
                    'Подобри профила със снимки, конкретни услуги и доказателства за доверие.',
                    'Провери дали клиентът има лесен начин да се свърже с теб.',
                    'Пусни локална кампания едва след като профилът и офертата са подредени.',
                ],
                'warnings' => [
                    'Не увеличавай рекламния бюджет, преди офертата, профилът и контактният път да са ясни.',
                ],
            ];
        }

        if (Str::contains($combined, ['реклам', 'кампания', 'клик'])) {
            return [
                'likely_reason' => 'Проблемът може да не е само в рекламата, а в офертата, страницата, доверието или аудиторията, към която водиш трафик.',
                'recommended_specialists' => ['performance маркетолог', 'дизайнер', 'copywriter', 'landing page специалист'],
                'next_steps' => [
                    'Провери дали рекламата води към ясна оферта с конкретно действие.',
                    'Подобри текста, визуала и призива за действие.',
                    'Измервай заявки и продажби, не само кликове.',
                    'Тествай малък бюджет с различни послания, преди да мащабираш.',
                ],
                'warnings' => [
                    'Не сменяй само рекламната платформа, ако страницата или профилът не убеждават.',
                ],
            ];
        }

        if (Str::contains($combined, ['празни', 'час', 'резервац'])) {
            return [
                'likely_reason' => 'Свободните часове вероятно не са достатъчно видими или клиентът няма причина да резервира точно сега.',
                'recommended_specialists' => ['маркетолог', 'CRM/автоматизации специалист', 'copywriter'],
                'next_steps' => [
                    'Покажи свободните часове ясно в профила и основните си канали.',
                    'Създай last-minute оферта за слабите часови слотове.',
                    'Изпрати кратко съобщение към стари клиенти.',
                    'Пусни локална кампания само за свободните часове.',
                ],
                'warnings' => [
                    'Не намалявай цените хаотично без ясна оферта, срок и измерване на резултата.',
                ],
            ];
        }

        if (Str::contains($combined, ['персонал', 'служител', 'екип'])) {
            return [
                'likely_reason' => 'Предложението към кандидатите може да не е достатъчно ясно, видимо или конкурентно спрямо очакванията на пазара.',
                'recommended_specialists' => ['HR консултант', 'copywriter', 'оперативен консултант'],
                'next_steps' => [
                    'Опиши ролята, смените и очакванията много конкретно.',
                    'Добави диапазон на заплащане или ясни условия.',
                    'Покажи защо работата е добра възможност.',
                    'Публикувай обявата в правилните канали и следи откъде идват кандидатите.',
                ],
                'warnings' => [
                    'Не публикувай обща обява без ясни условия и кратък процес за кандидатстване.',
                ],
            ];
        }

        if (Str::contains($combined, ['сайт', 'страница', 'онлайн'])) {
            return [
                'likely_reason' => 'Вероятно сайтът не превръща интереса в действие: липсва ясна оферта, доверие, бърз контакт или измерване.',
                'recommended_specialists' => ['web designer', 'developer', 'copywriter', 'маркетолог'],
                'next_steps' => [
                    'Постави основната оферта и контактното действие над първия екран.',
                    'Добави доказателства: снимки, отзиви, случаи, гаранции или процес.',
                    'Провери скоростта и mobile версията.',
                    'Измервай изпратени форми, обаждания и кликове към контакт.',
                ],
                'warnings' => [
                    'Не прави пълен redesign, преди да знаеш къде точно потребителите отпадат.',
                ],
            ];
        }

        if (Str::contains($combined, ['хаос', 'организация', 'процес'])) {
            return [
                'likely_reason' => 'Проблемът изглежда оперативен: заявките, задачите и отговорностите не са подредени в ясен процес.',
                'recommended_specialists' => ['оперативен консултант', 'CRM специалист', 'automation специалист'],
                'next_steps' => [
                    'Опиши пътя на една заявка от първи контакт до приключване.',
                    'Определи кой отговаря за всяка стъпка.',
                    'Въведи прост статус за всяка заявка: нова, в работа, чака отговор, приключена.',
                    'Автоматизирай само повтарящите се стъпки, след като процесът е ясен.',
                ],
                'warnings' => [
                    'Не добавяй още инструменти, преди да подредиш базовия процес.',
                ],
            ];
        }

        return [
            'likely_reason' => 'Вероятно има няколко свързани причини. BON препоръчва първо да се подреди проблемът, после каналите, офертата и изпълнението.',
            'recommended_specialists' => ['бизнес консултант', 'маркетолог', 'дизайнер', 'оперативен специалист'],
            'next_steps' => [
                'Опиши проблема като измерим резултат: заявки, продажби, часове, разход или доверие.',
                'Провери профила, офертата и начина за контакт.',
                'Избери едно действие за следващите 7 дни и го измери.',
                'Потърси специалист само за конкретната част, която блокира резултата.',
            ],
            'warnings' => [
                'Не започвай с много действия едновременно. Избери най-вероятния блокер и го тествай.',
            ],
        ];
    }
}
