<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningObjective extends Model
{
    protected $fillable = [
        'applicant_id',
        'firstPriority',
        'secondPriority',
        'thirdPriority',
        'goalStatement',
        'timeCommitment',
        'overseasPlan',
        'costPayment',
        'otherCostPayment',
        'completionTimeline'
    ];

    public function personalInfo(): BelongsTo
    {
        return $this->belongsTo(PersonalInfo::class, 'applicant_id', 'applicant_id');
    }
} 