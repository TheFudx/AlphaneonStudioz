<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KidsShowTransaction extends Model
{
    use HasFactory;
    protected $connection = 'second_db'; // Use the second database
    protected $table = 'kids_show_transactions';

    protected $fillable = ['payment_id','booking_id', 'user_id', 'amount', 'status', 'convenience_fee', 'margin_profit'];

    public function booking()
    {
        return $this->belongsTo(KidsShowBooking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    

}
