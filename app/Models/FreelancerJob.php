<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerJob extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'business_id',
        'title',
        'description',
        'budget',
        'deadline',
        'category',
        'location',
        'work_mode',
        'client_name',
        'client_phone',
        'client_email',
        'attachment_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'deadline' => 'date',
        ];
    }

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function applications()
    {
        return $this->hasMany(FreelancerJobApplication::class);
    }

    public function creditTransactions()
    {
        return $this->hasMany(FreelancerCreditTransaction::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }
}
