<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestPhoto extends Model
{
    protected $fillable = [
        'service_request_id',
        'path',
        'original_name',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
