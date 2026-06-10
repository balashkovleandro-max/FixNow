<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'favorite_user_id',
        'favorite_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoriteUser()
    {
        return $this->belongsTo(User::class, 'favorite_user_id');
    }
}
