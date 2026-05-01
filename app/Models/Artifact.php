<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Artifact extends Model
{
    protected $fillable = [
        'name',
        'floor',
        'category_id',
        'qr_code',
        'qr_image_path',
        'has_3d_model',
    ];

    protected $casts = [
        'has_3d_model' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ArtifactMedia::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    public function historicalFigures(): HasMany
    {
        return $this->hasMany(HistoricalFigure::class);
    }

    public function figures(): BelongsToMany
    {
        return $this->belongsToMany(HistoricalFigure::class, 'figure_artifacts', 'artifact_id', 'figure_id');
    }
}
