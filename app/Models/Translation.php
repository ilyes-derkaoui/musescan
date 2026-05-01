<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = [
        'artifact_id',
        'locale',
        'name',
        'description',
    ];

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }
}
