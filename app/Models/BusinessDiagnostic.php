<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessDiagnostic extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'city',
        'contact_person',
        'phone',
        'email',
        'problem_type',
        'description',
        'duration',
        'urgency',
        'customer_source',
        'budget',
        'active_ads',
        'website',
        'google_business',
        'social_profiles',
        'website_url',
        'instagram_url',
        'facebook_url',
        'google_business_url',
        'likely_reason',
        'recommended_specialists',
        'next_steps',
        'warnings',
    ];

    protected function casts(): array
    {
        return [
            'active_ads' => 'boolean',
            'website' => 'boolean',
            'google_business' => 'boolean',
            'social_profiles' => 'boolean',
            'recommended_specialists' => 'array',
            'next_steps' => 'array',
            'warnings' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
