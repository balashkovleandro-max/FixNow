<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'category',
        'city',
        'description',
        'price',
        'phone',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}