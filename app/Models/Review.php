<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'business_id',
        'reviewer_name',
        'reviewer_email',
        'rating',
        'comment',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function approve(): void
    {
        $this->forceFill([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
        ])->save();
    }

    public function reject(): void
    {
        $this->forceFill([
            'status' => self::STATUS_REJECTED,
            'approved_at' => null,
        ])->save();
    }
}
