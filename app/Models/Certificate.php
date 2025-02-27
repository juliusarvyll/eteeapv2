class Certificate extends Model
{
    protected $fillable = [
        'applicant_id', // Foreign key to Applicant
        'agency',
        'date_certified',
        'rating'
    ];

    // Define the inverse relationship with Education
    public function education()
    {
        return $this->belongsTo(Education::class, 'applicant_id', 'applicant_id');
    }
}
