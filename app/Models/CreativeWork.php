<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreativeWork extends Model
{
    protected $fillable = [
        'applicant_id',
        'title',
        'description',
        'significance',
        'date_completed',
        'corroborating_body'
    ];

    protected $casts = [
        'date_completed' => 'date',
    ];

    public function personalInfo(): BelongsTo
    {
        return $this->belongsTo(PersonalInfo::class, 'applicant_id', 'applicant_id');
    }
} 