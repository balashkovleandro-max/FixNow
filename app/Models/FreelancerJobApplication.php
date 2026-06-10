<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerJobApplication extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_NOT_SELECTED = 'not_selected';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_DONE = 'done';

    protected $fillable = [
        'freelancer_job_id',
        'freelancer_id',
        'cover_message',
        'proposed_price',
        'proposed_timeframe',
        'credits_spent',
        'status',
        'selected_at',
    ];

    protected function casts(): array
    {
        return [
            'credits_spent' => 'integer',
            'selected_at' => 'datetime',
        ];
    }

    public function job()
    {
        return $this->belongsTo(FreelancerJob::class, 'freelancer_job_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function creditTransactions()
    {
        return $this->hasMany(FreelancerCreditTransaction::class);
    }
}
