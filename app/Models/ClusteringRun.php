<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClusteringRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'period',
        'total_data',
        'total_clusters',
        'iterations',
        'silhouette_score',
        'attributes',
        'iteration_history',
    ];

    protected $casts = [
        'attributes' => 'array',
        'iteration_history' => 'array',
        'silhouette_score' => 'decimal:6',
    ];

    public function centroids(): HasMany
    {
        return $this->hasMany(ClusterCentroid::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(ClusterResult::class);
    }
}
