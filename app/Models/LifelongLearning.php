<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LifelongLearning extends Model
{
    protected $fillable = [
        'applicant_id',
        'type',          // 'hobby', 'skill', 'work', 'volunteer', 'travel'
        'description'
    ];

    public function personalInfo(): BelongsTo
    {
        return $this->belongsTo(PersonalInfo::class, 'applicant_id', 'applicant_id');
    }
} 