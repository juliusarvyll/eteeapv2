<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $fillable = [
        'applicant_id',
        'type', // 'elementary', 'high_school', 'post_secondary', 'non_formal', 'certification'
        
        // Elementary & Common Fields
        'school_name',
        'address',
        'date_from',
        'date_to',
        'has_diploma',
        'diploma_file',
        
        // High School Specific
        'school_type', // For high school types
        'is_senior_high',
        'strand',
        
        // PEPT Specific
        'pept_year',
        'pept_grade',
        
        // Post Secondary Specific
        'program',
        'institution',
        'school_year',
        
        // Non-Formal Specific
        'title',
        'organization',
        'certificate',
        'participation',
        
        // Certification Specific
        'agency',
        'date_certified',
        'rating'
    ];

    // Update casts to handle year fields as integers
    protected $casts = [
        'has_diploma' => 'boolean',
        'is_senior_high' => 'boolean',
        'date_from' => 'integer',
        'date_to' => 'integer',
        'pept_year' => 'integer',
        'date_certified' => 'integer'
    ];

    // Define valid education types
    const TYPES = [
        'elementary',
        'high_school',
        'post_secondary',
        'non_formal',
        'certification'
    ];

    // Define valid strands for Senior High School
    const STRANDS = [
        'STEM' => 'Science, Technology, Engineering, and Mathematics',
        'HUMSS' => 'Humanities and Social Sciences',
        'ABM' => 'Accountancy, Business, and Management',
        'GAS' => 'General Academic Strand',
        'TVL' => 'Technical-Vocational-Livelihood',
        'Sports' => 'Sports Track',
        'Arts and Design' => 'Arts and Design Track'
    ];

    // Mutator for date_from
    public function setDateFromAttribute($value)
    {
        $this->attributes['date_from'] = is_numeric($value) ? (int)$value : null;
    }

    // Mutator for date_to
    public function setDateToAttribute($value)
    {
        $this->attributes['date_to'] = is_numeric($value) ? (int)$value : null;
    }

    // Mutator for pept_year
    public function setPeptYearAttribute($value)
    {
        $this->attributes['pept_year'] = is_numeric($value) ? (int)$value : null;
    }

    // Mutator for date_certified
    public function setDateCertifiedAttribute($value)
    {
        $this->attributes['date_certified'] = is_numeric($value) ? (int)$value : null;
    }

    public function applicant()
    {
        return $this->belongsTo(PersonalInfo::class, 'applicant_id', 'applicant_id');
    }

    // Helper method to check if education is high school
    public function isHighSchool()
    {
        return $this->type === 'high_school';
    }

    // Helper method to check if education is senior high
    public function isSeniorHigh()
    {
        return $this->isHighSchool() && $this->is_senior_high;
    }

    // Helper method to get the full strand name
    public function getFullStrandName()
    {
        return $this->strand ? self::STRANDS[$this->strand] ?? $this->strand : null;
    }

    // Scope to filter by education type
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope to get only senior high records
    public function scopeSeniorHigh($query)
    {
        return $query->where('type', 'high_school')->where('is_senior_high', true);
    }

    // Scope to get only junior high records
    public function scopeJuniorHigh($query)
    {
        return $query->where('type', 'high_school')->where('is_senior_high', false);
    }
} 