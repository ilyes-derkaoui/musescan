<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrScan extends Model
{
    protected $fillable = [
        'artifact_id',
        'device_type',
        'language_used',
    ];

    public function artifact()
    {
        return $this->belongsTo(Artifact::class);
    }
}

