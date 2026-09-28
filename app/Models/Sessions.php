<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    use HasFactory;

    public $table = 'sessions';
    // VERY IMPORTANT
    public $incrementing = false;
    protected $keyType = 'string';
    public $fillable = [
        'id','user_id','ip_address','user_agent','payload','last_activity'
    ];  

   public $timestamps = false;
}
