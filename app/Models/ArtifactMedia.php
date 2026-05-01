<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ArtifactMedia extends Model
{
    protected $table = 'artifact_media';

    protected $fillable = [
        'artifact_id',
        'type',
        'file_path',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }
}
