<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessPhoto extends Model
{
    protected $fillable = [
        'business_id',
        'path',
        'original_name',
        'alt_text',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }
}
