<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestOffer extends Model
{
    public const STATUS_SENT = 'sent';
    public const STATUS_VIEWED = 'viewed';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_NOT_SELECTED = 'not_selected';

    public const POINTS_COST = 3;

    protected $fillable = [
        'service_request_id',
        'business_id',
        'price_estimate',
        'timeframe',
        'message',
        'phone',
        'email',
        'status',
        'points_spent',
    ];

    protected function casts(): array
    {
        return [
            'points_spent' => 'integer',
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
}
