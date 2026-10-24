<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClusterResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'clustering_run_id',
        'employee_id',
        'cluster_number',
        'cluster_label',
        'distance',
        'silhouette_score',
        'attribute_values',
        'normalized_values',
    ];

    protected $casts = [
        'attribute_values' => 'array',
        'normalized_values' => 'array',
        'distance' => 'decimal:6',
        'silhouette_score' => 'decimal:6',
    ];

    public function clusteringRun(): BelongsTo
    {
        return $this->belongsTo(ClusteringRun::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
