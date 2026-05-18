<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessRecommendation extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
        'ip_hash',
    ];

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function ipHash(?string $ip = null): ?string
    {
        $ip = $ip ?: request()->ip();

        return $ip ? hash('sha256', $ip . '|' . (string) config('app.key')) : null;
    }
}
