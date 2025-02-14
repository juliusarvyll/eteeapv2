<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityAward extends Model
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
        'dateAwarded' => 'integer'
    ];


    public function personalInfo(): BelongsTo
    {
        return $this->belongsTo(PersonalInfo::class, 'applicant_id', 'applicant_id');
    }
} 