<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KidsShow extends Model
{
    use HasFactory;
    protected $connection = 'second_db'; // Use the second database
    protected $table = 'kids_shows';
    
    protected $fillable = [
        'title', 'description', 'genre', 'duration', 'image_path', 'banner_image_path', 'language', 'rating', 'price'
    ];

    public function kidshowtime()
    {
        return $this->hasOne(KidsShowShowtime::class, 'show_id'); // Adjust this to match your database structure (e.g., hasMany if multiple showtimes exist)
    }
    public function stage()
    {
        return $this->belongsTo(KidsShowStage::class); // Assuming each ComedyShow belongs to one Stage
    }
}
