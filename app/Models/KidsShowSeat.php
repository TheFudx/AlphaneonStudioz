<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KidsShowSeat extends Model
{
    use HasFactory;
    protected $connection = 'second_db'; // Use the second database
    protected $table = 'kids_show_seats';

    protected $fillable = [
        'stage_id', 'seat_number', 'seat_type','section', 'price', 'commission_price'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($seat) {
            $seat->commission_price = $seat->price * 0.02;
        });

        static::updating(function ($seat) {
            $seat->commission_price = $seat->price * 0.02;
        });
    }

    public function stage()
    {
        return $this->belongsTo(KidsShowStage::class, 'stage_id'); // Specify the foreign key explicitly
    }
    
}
