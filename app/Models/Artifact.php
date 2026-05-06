<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\QrScan;
use App\Models\ArtifactMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }
    public function qrScans()
    {
    return $this->hasMany(QrScan::class);
    }
    public function images()
    {
        return $this->hasMany(ArtifactMedia::class);
    }
}


