<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KidsShowStage extends Model
{
    use HasFactory;
    protected $connection = 'second_db'; // Use the second database
    protected $table = 'kids_show_stages';
    protected $fillable = [
        'location_id', 'stage_number', 'total_seats'
    ];

    public function slocation()
    {
        return $this->belongsTo(KidsShowLocation::class, 'location_id');
    }

    public function cseats()
    {
        return $this->hasMany(KidsShowSeat::class, 'stage_id'); // Specify the foreign key explicitly
    }

    public function kshowtimes()
    {
        return $this->hasMany(KidsShowShowtime::class);
    }
}
