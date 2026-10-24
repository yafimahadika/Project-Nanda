<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClusterCentroid extends Model
{
    use HasFactory;

    protected $fillable = [
        'clustering_run_id',
        'cluster_number',
        'cluster_label',
        'values',
    ];

    protected $casts = [
        'values' => 'array',
    ];

    public function clusteringRun(): BelongsTo
    {
        return $this->belongsTo(ClusteringRun::class);
    }
}
