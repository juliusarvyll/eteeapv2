<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkAward extends Model
{
    protected $fillable = [
        'applicant_id',
        'title',
        'organization',
        'dateAwarded',
        'description',
        'document'
    ];

    protected $casts = [
        'dateAwarded' => 'date'
    ];

    public function personalInfo(): BelongsTo
    {
        return $this->belongsTo(PersonalInfo::class, 'applicant_id', 'applicant_id');
    }
} 