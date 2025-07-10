<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KidsShowBookingSeat extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id', 'seat_id'
    ];

    public function ksbooking()
    {
        return $this->belongsTo(KidsShowBooking::class, 'booking_id');
    }

    public function csseat()
    {
        return $this->belongsTo(KidsShowSeat::class, 'seat_id');
    }
}
