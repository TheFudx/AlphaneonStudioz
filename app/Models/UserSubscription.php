<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'payment_id',
        'razorpay_subscription_id',
        'package_id',
        'status',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Optionally, if you have a Package model:
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
