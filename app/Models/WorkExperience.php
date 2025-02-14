<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkExperience extends Model
{
    protected $fillable = [
        'applicant_id',
        'employment_type',
        'designation',
        'dateFrom',
        'dateTo',
        'companyName',
        'companyAddress',
        'employmentStatus',
        'supervisorName',
        'reasonForLeaving',
        'responsibilities',
        'documents',
        'reference1_name',
        'reference1_contact',
        'reference2_name',
        'reference2_contact',
        'reference3_name',
        'reference3_contact',
    ];

    protected $casts = [
        'references' => 'array',
        'dateFrom' => 'integer',
        'dateTo' => 'integer'
    ];

    // Mutator for dateFrom
    public function setDateFromAttribute($value)
    {
        $this->attributes['dateFrom'] = is_numeric($value) ? (int)$value : null;
    }

    // Mutator for dateTo
    public function setDateToAttribute($value)
    {
        $this->attributes['dateTo'] = is_numeric($value) ? (int)$value : null;
    }

    public function personalInfo(): BelongsTo
    {
        return $this->belongsTo(PersonalInfo::class, 'applicant_id', 'applicant_id');
    }
} 