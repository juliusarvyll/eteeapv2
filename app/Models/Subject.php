<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'course_name',
        'subject_name',
        'grade'
    ];

    public function personalInfo()
    {
        return $this->belongsTo(PersonalInfo::class);
    }
} 