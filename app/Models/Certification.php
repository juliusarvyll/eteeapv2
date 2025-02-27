<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $table = 'education_certification';

    protected $casts = [
        'certificate_files' => 'array'
    ];

    protected $fillable = [
        'education_id',
        'agency',
        'date_certified',
        'rating',
        'certificate_files'
    ];

    /**
     * Define the relationship with the Education model.
     */
    public function education()
    {
        return $this->belongsTo(Education::class, 'education_id');
    }
}
