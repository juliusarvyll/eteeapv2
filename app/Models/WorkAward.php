<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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

    protected $appends = ['document_url'];

    // Accessor for document URL
    public function getDocumentUrlAttribute()
    {
        if (!$this->document) {
            return null;
        }

        return Storage::disk('public')->url($this->document);
    }

    public function personalInfo(): BelongsTo
    {
        return $this->belongsTo(PersonalInfo::class, 'applicant_id', 'applicant_id');
    }
}
