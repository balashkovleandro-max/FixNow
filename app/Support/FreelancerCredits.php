<?php

namespace App\Support;

use App\Models\FreelancerCreditTransaction;
use App\Models\FreelancerJob;
use App\Models\FreelancerJobApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class FreelancerCredits
{
    public const MONTHLY_GRANT = 30;
    public const APPLICATION_COST = 3;

    public const PACKAGES = [
        'credits_30' => ['credits' => 30, 'price' => 4.99, 'label' => '30 кредита'],
        'credits_75' => ['credits' => 75, 'price' => 9.99, 'label' => '75 кредита'],
        'credits_150' => ['credits' => 150, 'price' => 17.99, 'label' => '150 кредита'],
    ];

    public static function ensureMonthlyCredits(User $user): void
    {
        if (!self::isReady() || !$user->isFreelancer()) {
            return;
        }

        $monthStart = now()->startOfMonth();

        DB::transaction(function () use ($user, $monthStart) {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->first();

            if (!$lockedUser || !$lockedUser->isFreelancer()) {
                return;
            }

            if (
                $lockedUser->freelancer_monthly_credits_granted_at
                && $lockedUser->freelancer_monthly_credits_granted_at->greaterThanOrEqualTo($monthStart)
            ) {
                return;
            }

            $balance = (int) ($lockedUser->freelancer_credits_balance ?? 0);
            $newBalance = $balance + self::MONTHLY_GRANT;

            $lockedUser->forceFill([
                'freelancer_credits_balance' => $newBalance,
                'freelancer_monthly_credits_granted_at' => now(),
            ])->save();

            FreelancerCreditTransaction::create([
                'user_id' => $lockedUser->id,
                'type' => FreelancerCreditTransaction::TYPE_MONTHLY_GRANT,
                'amount' => self::MONTHLY_GRANT,
                'balance_after' => $newBalance,
                'description' => 'Месечен BON баланс за кандидатстване',
            ]);
        });

        $user->refresh();
    }

    public static function addCredits(
        User $user,
        int $amount,
        string $type,
        ?string $description = null,
        ?User $admin = null,
        ?array $meta = []
    ): FreelancerCreditTransaction {
        if (!self::isReady() || !$user->isFreelancer()) {
            throw ValidationException::withMessages([
                'credits' => 'Кредитите са достъпни само за фрийлансър профили.',
            ]);
        }

        if ($amount === 0) {
            throw ValidationException::withMessages([
                'amount' => 'Сумата кредити не може да бъде 0.',
            ]);
        }

        return DB::transaction(function () use ($user, $amount, $type, $description, $admin, $meta) {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $currentBalance = (int) ($lockedUser->freelancer_credits_balance ?? 0);
            $newBalance = $currentBalance + $amount;

            if ($newBalance < 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Балансът не може да стане отрицателен.',
                ]);
            }

            $lockedUser->forceFill([
                'freelancer_credits_balance' => $newBalance,
            ])->save();

            $transaction = FreelancerCreditTransaction::create([
                'user_id' => $lockedUser->id,
                'admin_id' => $admin?->id,
                'freelancer_job_id' => $meta['freelancer_job_id'] ?? null,
                'freelancer_job_application_id' => $meta['freelancer_job_application_id'] ?? null,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'credit_package' => $meta['credit_package'] ?? null,
                'price_amount' => $meta['price_amount'] ?? null,
                'currency' => $meta['currency'] ?? 'EUR',
                'description' => $description,
            ]);

            $user->refresh();

            return $transaction;
        });
    }

    public static function applyToJob(
        User $freelancer,
        FreelancerJob $job,
        ?string $coverMessage = null,
        ?string $proposedPrice = null,
        ?string $proposedTimeframe = null
    ): FreelancerJobApplication
    {
        self::ensureMonthlyCredits($freelancer);

        if (!$job->isOpen()) {
            throw ValidationException::withMessages([
                'job' => 'Тази обява вече не приема кандидатури.',
            ]);
        }

        return DB::transaction(function () use ($freelancer, $job, $coverMessage, $proposedPrice, $proposedTimeframe) {
            $lockedUser = User::query()->whereKey($freelancer->id)->lockForUpdate()->firstOrFail();

            if (!$lockedUser->isFreelancer()) {
                throw ValidationException::withMessages([
                    'job' => 'Само фрийлансър профили могат да кандидатстват по обяви.',
                ]);
            }

            $alreadyApplied = FreelancerJobApplication::query()
                ->where('freelancer_job_id', $job->id)
                ->where('freelancer_id', $lockedUser->id)
                ->exists();

            if ($alreadyApplied) {
                throw ValidationException::withMessages([
                    'job' => 'Вече си кандидатствал по тази обява.',
                ]);
            }

            $balance = (int) ($lockedUser->freelancer_credits_balance ?? 0);

            if ($balance < self::APPLICATION_COST) {
                throw ValidationException::withMessages([
                    'credits' => 'Нямаш достатъчно кредити за кандидатстване.',
                ]);
            }

            $applicationPayload = [
                'freelancer_job_id' => $job->id,
                'freelancer_id' => $lockedUser->id,
                'cover_message' => $coverMessage,
                'credits_spent' => self::APPLICATION_COST,
                'status' => FreelancerJobApplication::STATUS_SUBMITTED,
            ];

            if (Schema::hasColumn('freelancer_job_applications', 'proposed_price')) {
                $applicationPayload['proposed_price'] = $proposedPrice;
            }

            if (Schema::hasColumn('freelancer_job_applications', 'proposed_timeframe')) {
                $applicationPayload['proposed_timeframe'] = $proposedTimeframe;
            }

            $application = FreelancerJobApplication::create($applicationPayload);

            $newBalance = $balance - self::APPLICATION_COST;

            $lockedUser->forceFill([
                'freelancer_credits_balance' => $newBalance,
            ])->save();

            FreelancerCreditTransaction::create([
                'user_id' => $lockedUser->id,
                'freelancer_job_id' => $job->id,
                'freelancer_job_application_id' => $application->id,
                'type' => FreelancerCreditTransaction::TYPE_APPLICATION_SPEND,
                'amount' => -self::APPLICATION_COST,
                'balance_after' => $newBalance,
                'description' => 'Кандидатстване по обява: ' . $job->title,
            ]);

            $freelancer->refresh();

            return $application;
        });
    }

    public static function stats(User $user): array
    {
        if (!self::isReady() || !$user->isFreelancer()) {
            return [
                'available' => 0,
                'used' => 0,
                'purchased' => 0,
                'monthly_granted_at' => null,
            ];
        }

        return [
            'available' => (int) ($user->freelancer_credits_balance ?? 0),
            'used' => abs((int) $user->freelancerCreditTransactions()
                ->where('type', FreelancerCreditTransaction::TYPE_APPLICATION_SPEND)
                ->sum('amount')),
            'purchased' => (int) $user->freelancerCreditTransactions()
                ->where('type', FreelancerCreditTransaction::TYPE_PURCHASE)
                ->sum('amount'),
            'monthly_granted_at' => $user->freelancer_monthly_credits_granted_at,
        ];
    }

    public static function isReady(): bool
    {
        return Schema::hasTable('freelancer_credit_transactions')
            && Schema::hasColumn('users', 'freelancer_credits_balance')
            && Schema::hasColumn('users', 'freelancer_monthly_credits_granted_at');
    }
}
