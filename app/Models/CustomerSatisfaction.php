<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerSatisfaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'assessment_date',
        'satisfaction_score',
        'customers_handled',
        'complaints',
        'complaints_resolved',
        'notes',
    ];

    protected $casts = [
        'assessment_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
