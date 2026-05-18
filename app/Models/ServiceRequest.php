<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_OPEN = 'open';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_IN_PROGRESS = 'in_progress';

    public const SOURCE_OFFER_FORM = 'offer_form';
    public const SOURCE_BUSINESS_PROFILE = 'business_profile';

    public const URGENCY_NORMAL = 'normal';
    public const URGENCY_URGENT = 'urgent';
    public const URGENCY_THIS_WEEK = 'this_week';
    public const URGENCY_THIS_MONTH = 'this_month';
    public const URGENCY_NO_DEADLINE = 'no_deadline';

    protected $fillable = [
        'customer_id',
        'name',
        'phone',
        'email',
        'city',
        'category',
        'service',
        'description',
        'urgency',
        'budget',
        'assigned_business_id',
        'selected_offer_id',
        'accepted_offer_at',
        'closed_at',
        'image',
        'status',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'accepted_offer_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function assignedBusiness()
    {
        return $this->belongsTo(User::class, 'assigned_business_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function business()
    {
        return $this->assignedBusiness();
    }

    public function selectedOffer()
    {
        return $this->belongsTo(ServiceRequestOffer::class, 'selected_offer_id');
    }

    public function assignments()
    {
        return $this->hasMany(ServiceRequestAssignment::class);
    }

    public function offers()
    {
        return $this->hasMany(ServiceRequestOffer::class);
    }

    public function photos()
    {
        return $this->hasMany(ServiceRequestPhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeForCustomer($query, User $customer)
    {
        return $query->where(function ($query) use ($customer) {
            $query->where('customer_id', $customer->id);

            if (filled($customer->email)) {
                $query->orWhere(function ($query) use ($customer) {
                    $query
                        ->whereNull('customer_id')
                        ->where('email', $customer->email);
                });
            }
        });
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_NEW, self::STATUS_OPEN, self::STATUS_CONTACTED]);
    }

    public function isOpenForOffers(): bool
    {
        return in_array($this->status, [self::STATUS_NEW, self::STATUS_OPEN], true)
            && $this->source === self::SOURCE_OFFER_FORM;
    }

    public function belongsToCustomer(User $customer): bool
    {
        if ((int) $this->customer_id === (int) $customer->id) {
            return true;
        }

        return !$this->customer_id
            && filled($customer->email)
            && $this->email === $customer->email;
    }

    public function markContacted(): void
    {
        $this->forceFill(['status' => self::STATUS_CONTACTED])->save();
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
    }

    public function markCancelled(): void
    {
        $this->forceFill([
            'status' => self::STATUS_CANCELLED,
            'closed_at' => now(),
        ])->save();
    }
}
