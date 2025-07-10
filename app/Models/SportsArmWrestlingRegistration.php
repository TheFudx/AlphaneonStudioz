<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportsArmWrestlingRegistration extends Model
{
    use HasFactory;
    protected $table = 'sports_arm_wrestling_registration';

    protected $fillable = [
        'fullname', 'email', 'age', 'weight', 'dhand', 'experience', 'injury', 'mobile', 'payment_id', 'amount'
    ];
}
