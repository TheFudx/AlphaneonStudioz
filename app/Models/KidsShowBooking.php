<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class KidsShowBooking extends Model
{
    use HasFactory;
    protected $connection = 'second_db'; // Use the second database
    protected $table = 'kids_show_bookings';
    
    protected $fillable = [
        'user_id', 'showtime_id', 'total_seats', 'total_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ksshowtime()
    {
        return $this->belongsTo(KidsShowShowtime::class, 'showtime_id');
    }

    public function ksbookingSeats()
    {
        return $this->hasMany(KidsShowBookingSeat::class, 'booking_id');
    }

    public function transaction()
    {
        // Specify the foreign key explicitly
        return $this->hasOne(KidsShowTransaction::class, 'booking_id');
    }

    public function kstransaction()
    {
        return $this->hasOne(KidsShowTransaction::class);
    }

    

}
