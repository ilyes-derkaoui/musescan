<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class HistoricalFigure extends Model
{
    protected $fillable = [
        'name',
        'birth_year',
        'death_year',
        'artifact_id',
    ];

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    public function artifacts(): BelongsToMany
    {
        return $this->belongsToMany(Artifact::class, 'figure_artifacts', 'figure_id', 'artifact_id');
    }
}
