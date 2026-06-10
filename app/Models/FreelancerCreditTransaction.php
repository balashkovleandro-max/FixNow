<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerCreditTransaction extends Model
{
    use HasFactory;

    public const TYPE_MONTHLY_GRANT = 'monthly_grant';
    public const TYPE_APPLICATION_SPEND = 'application_spend';
    public const TYPE_PURCHASE = 'purchase';
    public const TYPE_ADMIN_ADJUSTMENT = 'admin_adjustment';

    protected $fillable = [
        'user_id',
        'admin_id',
        'freelancer_job_id',
        'freelancer_job_application_id',
        'type',
        'amount',
        'balance_after',
        'credit_package',
        'price_amount',
        'currency',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'balance_after' => 'integer',
            'price_amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function job()
    {
        return $this->belongsTo(FreelancerJob::class, 'freelancer_job_id');
    }

    public function application()
    {
        return $this->belongsTo(FreelancerJobApplication::class, 'freelancer_job_application_id');
    }
}
