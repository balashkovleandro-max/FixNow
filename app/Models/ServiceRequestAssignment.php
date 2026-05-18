<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestAssignment extends Model
{
    public const STATUS_SENT = 'sent';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_RESPONSE = 'no_response';

    protected $fillable = [
        'service_request_id',
        'business_id',
        'status',
        'sent_at',
        'contacted_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'contacted_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function markContacted(): void
    {
        $this->forceFill([
            'status' => self::STATUS_CONTACTED,
            'contacted_at' => now(),
        ])->save();

        if ($this->serviceRequest && $this->serviceRequest->status === ServiceRequest::STATUS_NEW) {
            $this->serviceRequest->markContacted();
        }
    }

    public function markDeclined(): void
    {
        $this->forceFill([
            'status' => self::STATUS_DECLINED,
        ])->save();
    }

    public function markClosed(): void
    {
        $this->forceFill([
            'status' => self::STATUS_CLOSED,
            'closed_at' => now(),
        ])->save();
    }

    public function markCompleted(): void
    {
        $this->forceFill([
            'status' => self::STATUS_COMPLETED,
            'closed_at' => now(),
        ])->save();

        if ($this->serviceRequest) {
            $this->serviceRequest->markCompleted();
        }
    }

    public function markCancelled(): void
    {
        $this->forceFill([
            'status' => self::STATUS_CANCELLED,
            'closed_at' => now(),
        ])->save();

        if ($this->serviceRequest) {
            $this->serviceRequest->markCancelled();
        }
    }
}
