<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    public const TYPE_REQUEST_BASED = 'request_based';
    public const TYPE_DIRECTORY_BASED = 'directory_based';

    protected $fillable = [
        'name',
        'slug',
        'group',
        'type',
        'accepts_requests',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'accepts_requests' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function isRequestBased(): bool
    {
        return $this->type === self::TYPE_REQUEST_BASED || $this->accepts_requests;
    }
}
