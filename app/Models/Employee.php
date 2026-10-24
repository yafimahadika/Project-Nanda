<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'full_name',
        'branch',
        'job_position',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function customerSatisfactions(): HasMany
    {
        return $this->hasMany(CustomerSatisfaction::class);
    }

    public function clusterResults(): HasMany
    {
        return $this->hasMany(ClusterResult::class);
    }
}
