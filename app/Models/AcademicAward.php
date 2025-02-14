<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicAward extends Model
{
    protected $fillable = [
        'applicant_id',
        'title',
        'institution',
        'dateReceived',
        'description',
        'document'
    ];

    protected $casts = [
        'dateReceived' => 'date'
    ];

    public function personalInfo(): BelongsTo
    {
        return $this->belongsTo(PersonalInfo::class, 'applicant_id', 'applicant_id');
    }
} 